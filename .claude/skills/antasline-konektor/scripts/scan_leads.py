#!/usr/bin/env python
"""Skenira CF7 lead-mejlove (office@antasline.com) i puni leads.csv za Customer Match.

Namenjeno da se pokrece NA cPANEL SERVERU (u [cpanel-live] sesiji), ne lokalno
sa Windows masine — vidi CLAUDE-CODE-instrukcija-CPANEL.md. Podrazumevano cita
Maildir direktno sa diska (isti Linux korisnik kao sajt/mail nalog, bez mrezne
konekcije). --imap prebacuje na imaplib ka localhost ako Maildir putanja ili
permisije ne rade.

CF7 forme (ID 16593 "Kontakt", ID 16737 "Brzi upit") salju obavestenje sa
wordpress@antasline.com, telo sadrzi "Email: klijent@primer.com" polje —
odatle se izvlaci kontakt.

Inkrementalno: state.json (van git-a, u connector-home/customer-match/) cuva
setove vec obradjenih poruka (Maildir key ili Message-ID) — svaki sledeci run
gleda samo nove.

PRIVATNOST: stdout ispisuje SAMO brojeve, nikad email adrese ili sadrzaj
poruka. leads.csv i state.json zive iskljucivo van git stabla
(ANTASLINE_CONNECTOR_HOME, isti princip kao credentials/ folder).

Pokretanje:
    python scan_leads.py --dry-run                 # samo ispise brojeve, ne upisuje
    python scan_leads.py                            # upisuje nove leadove u leads.csv
    python scan_leads.py --maildir /path/to/Maildir
    python scan_leads.py --imap                     # fallback: imaplib ka localhost
"""

import argparse
import csv
import json
import mailbox
import re
import sys
from datetime import datetime, timezone
from email.header import decode_header
from email.message import Message
from email.utils import parsedate_to_datetime
from pathlib import Path
from typing import Optional

sys.path.insert(0, str(Path(__file__).parent))
from auth import connector_home  # noqa: E402

LEAD_SENDER = "no-reply@antasline.com"
INTERNAL_ADDRESSES = {"office@antasline.com", "no-reply@antasline.com", "wordpress@antasline.com", "admin@antasline.com"}
EMAIL_RE = re.compile(r"[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}")
DEFAULT_MAILDIR_GUESSES = [
    Path.home() / "mail" / "antasline.com" / "office",
    Path.home() / "mail" / "office",
]


def customer_match_dir() -> Path:
    d = connector_home() / "customer-match"
    d.mkdir(parents=True, exist_ok=True)
    return d


def leads_csv_path() -> Path:
    return customer_match_dir() / "leads.csv"


def state_path() -> Path:
    return customer_match_dir() / "scan-state.json"


def load_state() -> dict:
    p = state_path()
    if not p.exists():
        return {"processed_keys": []}
    return json.loads(p.read_text(encoding="utf-8"))


def save_state(state: dict) -> None:
    state_path().write_text(json.dumps(state, indent=2), encoding="utf-8")


def load_leads() -> dict:
    """email -> row dict"""
    p = leads_csv_path()
    if not p.exists():
        return {}
    with p.open(encoding="utf-8", newline="") as f:
        return {row["email"]: row for row in csv.DictReader(f)}


def save_leads(leads: dict) -> None:
    fieldnames = ["email", "first_seen_date", "source_form", "uploaded", "upload_date", "interest_category"]
    with leads_csv_path().open("w", encoding="utf-8", newline="") as f:
        writer = csv.DictWriter(f, fieldnames=fieldnames)
        writer.writeheader()
        for row in sorted(leads.values(), key=lambda r: r["first_seen_date"]):
            writer.writerow(row)


def decode_str(value) -> str:
    if value is None:
        return ""
    parts = decode_header(str(value))
    out = []
    for text, enc in parts:
        if isinstance(text, bytes):
            out.append(text.decode(enc or "utf-8", errors="replace"))
        else:
            out.append(text)
    return "".join(out)


def message_body_text(msg: Message) -> str:
    if msg.is_multipart():
        for part in msg.walk():
            if part.get_content_type() == "text/plain" and not part.get_filename():
                charset = part.get_content_charset() or "utf-8"
                payload = part.get_payload(decode=True) or b""
                return payload.decode(charset, errors="replace")
        return ""
    charset = msg.get_content_charset() or "utf-8"
    payload = msg.get_payload(decode=True) or b""
    return payload.decode(charset, errors="replace")


def source_form_from_subject(subject: str) -> str:
    if "brzi upit" in subject.lower():
        return "brzi-upit"
    if "kontakt" in subject.lower():
        return "kontakt"
    return "nepoznato"


def extract_lead_email(body: str) -> Optional[str]:
    m = re.search(r"^\s*E-?mail\s*:\s*(.+)$", body, re.IGNORECASE | re.MULTILINE)
    candidate = None
    if m:
        found = EMAIL_RE.search(m.group(1))
        if found:
            candidate = found.group(0).strip().lower()
    if not candidate:
        for found in EMAIL_RE.findall(body):
            found = found.strip().lower()
            if found not in INTERNAL_ADDRESSES:
                candidate = found
                break
    if candidate and candidate not in INTERNAL_ADDRESSES:
        return candidate
    return None


def message_date(msg: Message) -> str:
    try:
        dt = parsedate_to_datetime(msg.get("Date"))
        if dt.tzinfo is None:
            dt = dt.replace(tzinfo=timezone.utc)
        return dt.date().isoformat()
    except Exception:
        return datetime.now(timezone.utc).date().isoformat()


def find_maildir(explicit: Optional[str]) -> Path:
    if explicit:
        p = Path(explicit)
        if not p.exists():
            raise SystemExit(f"GRESKA: Maildir putanja ne postoji: {p}")
        return p
    for guess in DEFAULT_MAILDIR_GUESSES:
        if guess.exists():
            return guess
    raise SystemExit(
        "GRESKA: nijedna podrazumevana Maildir putanja ne postoji "
        f"({', '.join(str(g) for g in DEFAULT_MAILDIR_GUESSES)}). "
        "Proveri tacnu putanju (cPanel Email Accounts) i prosledi --maildir, "
        "ili koristi --imap fallback."
    )


def iter_maildir_messages(maildir_path: Path):
    md = mailbox.Maildir(str(maildir_path), factory=None)
    for key in md.keys():
        yield key, md.get_message(key)


def iter_imap_messages(imap_user: str, imap_password: str):
    import imaplib
    import email as email_mod

    conn = imaplib.IMAP4_SSL("localhost")
    try:
        conn.login(imap_user, imap_password)
        conn.select("INBOX", readonly=True)
        typ, data = conn.search(None, "FROM", f'"{LEAD_SENDER}"')
        if typ != "OK":
            raise SystemExit(f"GRESKA: IMAP SEARCH neuspesan: {data}")
        for num in data[0].split():
            typ, msg_data = conn.fetch(num, "(RFC822)")
            if typ != "OK":
                continue
            raw = msg_data[0][1]
            yield num.decode(), email_mod.message_from_bytes(raw)
    finally:
        conn.logout()


def main():
    p = argparse.ArgumentParser()
    p.add_argument("--dry-run", action="store_true", help="samo ispise brojeve, ne upisuje leads.csv/state.json")
    p.add_argument("--maildir", help="eksplicitna Maildir putanja (podrazumevano: auto-detekcija)")
    p.add_argument("--imap", action="store_true", help="fallback: imaplib ka localhost umesto Maildir-a")
    p.add_argument("--imap-user", default="office@antasline.com")
    args = p.parse_args()

    state = load_state()
    processed = set(state.get("processed_keys", []))
    leads = load_leads()

    if args.imap:
        from auth import credentials_dir, read_json

        cred_path = credentials_dir() / "mail-imap.json"
        if not cred_path.exists():
            raise SystemExit(
                f"GRESKA: nedostaje {cred_path}. Sadrzaj: "
                '{"password": "..."} (user je --imap-user, default office@antasline.com).'
            )
        password = read_json(cred_path)["password"]
        messages = iter_imap_messages(args.imap_user, password)
    else:
        maildir_path = find_maildir(args.maildir)
        messages = iter_maildir_messages(maildir_path)

    new_count = 0
    skipped_no_email = 0
    already_known = 0

    for key, msg in messages:
        if key in processed:
            continue
        processed.add(key)

        from_header = decode_str(msg.get("From", ""))
        if LEAD_SENDER not in from_header.lower():
            continue

        body = message_body_text(msg)
        email = extract_lead_email(body)
        if not email:
            skipped_no_email += 1
            continue

        if email in leads:
            already_known += 1
            continue

        subject = decode_str(msg.get("Subject", ""))
        leads[email] = {
            "email": email,
            "first_seen_date": message_date(msg),
            "source_form": source_form_from_subject(subject),
            "uploaded": "False",
            "upload_date": "",
            "interest_category": "",
        }
        new_count += 1

    print(f"Novih kontakata nadjeno: {new_count}")
    print(f"Vec poznatih (preskoceno): {already_known}")
    if skipped_no_email:
        print(f"Lead-mejlova bez prepoznate email adrese: {skipped_no_email}")
    print(f"Ukupno u leads.csv posle ovog run-a: {len(leads)}")

    if args.dry_run:
        print("[dry-run] leads.csv i state.json NISU izmenjeni.")
        return

    state["processed_keys"] = sorted(processed)
    state["last_run"] = datetime.now(timezone.utc).isoformat()
    save_state(state)
    save_leads(leads)
    print(f"Sacuvano: {leads_csv_path()}")


if __name__ == "__main__":
    try:
        main()
    except SystemExit:
        raise
    except Exception as exc:  # noqa: BLE001
        print(f"GRESKA: {type(exc).__name__}: {exc}", file=sys.stderr)
        sys.exit(1)
