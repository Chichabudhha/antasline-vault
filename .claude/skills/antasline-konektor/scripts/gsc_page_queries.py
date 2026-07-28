#!/usr/bin/env python
"""Google Search Console — upiti po KONKRETNOJ stranici (page + query).

Odgovara na pitanje "koja stranica drži koji upit", koje `gsc_report.py` ne moze
(on agregira po upitu preko celog sajta). Koristi se pri odluci rebuild vs 301,
pri dijagnozi kanibalizacije i pri proveri da li stranica uopste zasluzuje da
prezivi migraciju.

GSC podaci kasne 2-3 dana - pomeri --to prozor za toliko.

Primer:
    python gsc_page_queries.py --from 2026-06-28 --to 2026-07-25 \
        --page https://www.antasline.com/gumeni-podovi-javne-objekte-i-teretane/ \
        --page https://www.antasline.com/sportske-podloge/
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
    parser.add_argument(
        "--page",
        action="append",
        required=True,
        help="Pun live URL (moze se ponoviti vise puta)",
    )
    parser.add_argument("--limit", type=int, default=25, help="Max upita po stranici")
    args = parser.parse_args()

    service = get_gsc_service()
    pages = {}

    for page in args.page:
        request = {
            "startDate": args.date_from,
            "endDate": args.date_to,
            "dimensions": ["query"],
            "dimensionFilterGroups": [
                {"filters": [{"dimension": "page", "operator": "equals", "expression": page}]}
            ],
            "rowLimit": 500,
        }
        response = service.searchanalytics().query(siteUrl=SITE_URL, body=request).execute()

        rows = [
            {
                "query": row["keys"][0],
                "impressions": row.get("impressions", 0),
                "clicks": row.get("clicks", 0),
                "ctr": round(row.get("ctr", 0) * 100, 2),
                "position": round(row.get("position", 0), 1),
            }
            for row in response.get("rows", [])
        ]
        rows.sort(key=lambda r: r["impressions"], reverse=True)

        pages[page] = {
            "total_impressions": sum(r["impressions"] for r in rows),
            "total_clicks": sum(r["clicks"] for r in rows),
            "queries": rows[: args.limit],
        }

    print(
        json.dumps(
            {"period": {"from": args.date_from, "to": args.date_to}, "pages": pages},
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
