#!/usr/bin/env python
"""Kategorizuje leads.csv po interesovanju (proizvod/usluga) na osnovu sadrzaja
CF7 lead-mejlova, dodajuci kolonu 'interest_category'.

Koristi istu Maildir logiku kao scan_leads.py, ali ignorise scan-state.json
processed_keys filter (mora ponovo da procita sve poruke, uklj. stare, da bi
kategorisao postojece redove u leads.csv).

Kategorije (isti taksonomija kao GA4 publike, CLAUDE.md sek. 5):
    industrijski-esd-ecotile, sportski-tereni, terase-spoljne-podloge,
    lvt-expona, vestacka-trava, epoksid-conquest, nepoznato

PRIVATNOST: stdout ispisuje SAMO agregirane brojeve po kategoriji, nikad
email adrese ili sadrzaj poruka (isti princip kao scan_leads.py).

Pokretanje:
    python categorize_leads.py --dry-run
    python categorize_leads.py
    python categorize_leads.py --maildir /path/to/Maildir
"""

import argparse
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
from scan_leads import (  # noqa: E402
    LEAD_SENDER,
    decode_str,
    extract_lead_email,
    find_maildir,
    iter_maildir_messages,
    leads_csv_path,
    load_leads,
    message_body_text,
    save_leads,
)

CATEGORY_KEYWORDS = [
    ("epoksid-conquest", ["epoksid", "epoxy"]),
    ("industrijski-esd-ecotile", ["ecotile", "esd", "antistatik", "industrijsk", "ergonomsk"]),
    ("sportski-tereni", [
        "sportsk", "kosark", "košark", "basket", "odbojk", "tenis", "padel",
        "pickleball", "šljak", "sljak",
    ]),
    ("terase-spoljne-podloge", ["teras", "spoljn", "dvorišt", "dvorist", "bergo", "parkiral"]),
    ("lvt-expona", ["lvt", "expona", "vinil"]),
    ("vestacka-trava", ["veštačk", "vestack"]),
]


def categorize(body: str) -> str:
    lowered = body.lower()
    for category, keywords in CATEGORY_KEYWORDS:
        for kw in keywords:
            if kw in lowered:
                return category
    return "nepoznato"


def main():
    p = argparse.ArgumentParser()
    p.add_argument("--dry-run", action="store_true", help="samo ispise agregate, ne upisuje leads.csv")
    p.add_argument("--maildir", help="eksplicitna Maildir putanja (podrazumevano: auto-detekcija)")
    args = p.parse_args()

    leads = load_leads()
    if not leads:
        print("leads.csv je prazan ili ne postoji — nema sta da se kategorise.")
        return

    maildir_path = find_maildir(args.maildir)
    email_to_category = {}

    for _key, msg in iter_maildir_messages(maildir_path):
        from_header = decode_str(msg.get("From", ""))
        if LEAD_SENDER not in from_header.lower():
            continue
        body = message_body_text(msg)
        email = extract_lead_email(body)
        if not email or email not in leads:
            continue
        # ako ima vise mejlova od istog kontakta, poslednja kategorija koja se nadje pobedjuje
        email_to_category[email] = categorize(body)

    counts = {}
    uncategorized = 0
    for email, row in leads.items():
        category = email_to_category.get(email, "nepoznato")
        row["interest_category"] = category
        counts[category] = counts.get(category, 0) + 1
        if category == "nepoznato":
            uncategorized += 1

    print("Raspodela po interesovanju:")
    for category, count in sorted(counts.items(), key=lambda kv: -kv[1]):
        print(f"  {category}: {count}")
    print(f"Ukupno kontakata: {len(leads)}")

    if args.dry_run:
        print("[dry-run] leads.csv NIJE izmenjen.")
        return

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
