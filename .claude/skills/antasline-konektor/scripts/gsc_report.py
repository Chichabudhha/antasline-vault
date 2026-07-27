#!/usr/bin/env python
"""Google Search Console API — zamena za Windsor.ai `searchconsole` konektor.

Vraća top upite na poziciji 5-15 sa niskim CTR-om (GEO/SEO prilike), sortirano
po prikazima opadajuce. GSC podaci kasne 2-3 dana - pomeri --to prozor za toliko
kad birate datume.

Primer:
    python gsc_report.py --from 2026-06-29 --to 2026-07-26 --limit 15
"""

import argparse
import json
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
from auth import friendly_api_error, get_gsc_service  # noqa: E402

SITE_URL = "sc-domain:antasline.com"


def main() -> None:
    parser = argparse.ArgumentParser()
    parser.add_argument("--from", dest="date_from", required=True, help="YYYY-MM-DD")
    parser.add_argument("--to", dest="date_to", required=True, help="YYYY-MM-DD")
    parser.add_argument("--limit", type=int, default=15, help="Max redova u izlazu (podrazumevano 15)")
    parser.add_argument("--min-position", type=float, default=5.0)
    parser.add_argument("--max-position", type=float, default=15.0)
    args = parser.parse_args()

    service = get_gsc_service()
    request = {
        "startDate": args.date_from,
        "endDate": args.date_to,
        "dimensions": ["query"],
        "rowLimit": 1000,
    }
    response = service.searchanalytics().query(siteUrl=SITE_URL, body=request).execute()

    rows = []
    for row in response.get("rows", []):
        position = row.get("position", 0)
        if args.min_position <= position <= args.max_position:
            rows.append(
                {
                    "query": row["keys"][0],
                    "impressions": row.get("impressions", 0),
                    "clicks": row.get("clicks", 0),
                    "ctr": round(row.get("ctr", 0) * 100, 2),
                    "position": round(position, 1),
                }
            )

    rows.sort(key=lambda r: r["impressions"], reverse=True)

    print(
        json.dumps(
            {
                "period": {"from": args.date_from, "to": args.date_to},
                "opportunities": rows[: args.limit],
            },
            ensure_ascii=False,
        )
    )


if __name__ == "__main__":
    try:
        main()
    except SystemExit:
        raise
    except Exception as exc:  # noqa: BLE001
        friendly_api_error(exc)
