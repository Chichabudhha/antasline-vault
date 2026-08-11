#!/usr/bin/env python
"""Google Search Console — stanje submit-ovanih sitemap-a (READ-ONLY).

Nema Windsor pandana. Napisano za W3 „GSC priprema" stavku pre migracije
2026-08-24: treba znati TACNO koji su sitemap-ovi submit-ovani na live-u,
da li Google ima greske na njima, i koji ce od njih posle migracije postati
404 (Yoast/legacy ostaci) pa ih treba ukloniti.

Servisni nalog ima scope `webmasters.readonly` — skripta NE MOZE nista
submit-ovati ni obrisati, samo cita. Resubmit se radi rucno u GSC UI
posle migracije (v. migracija/2026-08-10-pre-migration-checklist B7).

Primer:
    python gsc_sitemaps.py
    python gsc_sitemaps.py --json
"""

import argparse
import json
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
from auth import friendly_api_error, get_gsc_service  # noqa: E402

SITE_URL = "sc-domain:antasline.com"


def collect() -> list[dict]:
    service = get_gsc_service()
    response = service.sitemaps().list(siteUrl=SITE_URL).execute()

    rows = []
    for sm in response.get("sitemap", []):
        contents = {c.get("type", "?"): int(c.get("submitted", 0)) for c in sm.get("contents", [])}
        rows.append(
            {
                "path": sm.get("path", ""),
                "type": sm.get("type", ""),
                "is_index": bool(sm.get("isSitemapsIndex", False)),
                "is_pending": bool(sm.get("isPending", False)),
                "last_submitted": sm.get("lastSubmitted", ""),
                "last_downloaded": sm.get("lastDownloaded", ""),
                "errors": int(sm.get("errors", 0)),
                "warnings": int(sm.get("warnings", 0)),
                "contents": contents,
            }
        )
    rows.sort(key=lambda r: r["path"])
    return rows


def main() -> None:
    parser = argparse.ArgumentParser()
    parser.add_argument("--json", action="store_true", help="Sirov JSON umesto tabele")
    args = parser.parse_args()

    rows = collect()

    if args.json:
        print(json.dumps({"site": SITE_URL, "sitemaps": rows}, ensure_ascii=False, indent=2))
        return

    if not rows:
        print("Nema submit-ovanih sitemap-a za", SITE_URL)
        return

    print(f"{SITE_URL} — {len(rows)} submit-ovanih sitemap-a\n")
    for r in rows:
        flags = []
        if r["is_index"]:
            flags.append("INDEX")
        if r["is_pending"]:
            flags.append("PENDING")
        if r["errors"]:
            flags.append(f"ERR:{r['errors']}")
        if r["warnings"]:
            flags.append(f"WARN:{r['warnings']}")
        flag_s = (" [" + " ".join(flags) + "]") if flags else ""
        print(f"- {r['path']}{flag_s}")
        print(f"    submitted: {r['last_submitted'] or '-'}   downloaded: {r['last_downloaded'] or '-'}")
        if r["contents"]:
            print("    contents: " + ", ".join(f"{k}={v}" for k, v in sorted(r["contents"].items())))


if __name__ == "__main__":
    try:
        sys.stdout.reconfigure(encoding="utf-8")
        main()
    except SystemExit:
        raise
    except Exception as exc:  # noqa: BLE001
        friendly_api_error(exc)
