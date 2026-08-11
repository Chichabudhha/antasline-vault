#!/usr/bin/env python
"""GA4 Data API — landing stranice PLACENOG saobracaja (google / cpc).

Namena: interim izvor za W4 4.10 "final URL audit oglasa" kad Ads API nije
dostupan (OAuth token istekao) — pokazuje na koje URL-ove korisnici STVARNO
sleću iz oglasa. Nije zamena za Ads export (ne vidi oglase bez klikova, ne
vidi sitelink-ove bez klikova, i vidi URL POSLE redirekta), ali daje
proverljiv presek najprometnijih odredista.

READ-ONLY. Service account (isti kao ga4_report.py) — ne trazi OAuth.

Primer:
    python ga4_paid_landing.py --from 2026-05-11 --to 2026-08-10
"""

import argparse
import json
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
from auth import friendly_api_error, get_ga4_credentials  # noqa: E402

GA4_PROPERTY = "292720335"


def main() -> None:
    parser = argparse.ArgumentParser()
    parser.add_argument("--from", dest="date_from", required=True, help="YYYY-MM-DD")
    parser.add_argument("--to", dest="date_to", required=True, help="YYYY-MM-DD")
    parser.add_argument("--limit", type=int, default=200)
    args = parser.parse_args()

    from google.analytics.data_v1beta import BetaAnalyticsDataClient
    from google.analytics.data_v1beta.types import (
        DateRange,
        Dimension,
        Filter,
        FilterExpression,
        Metric,
        OrderBy,
        RunReportRequest,
    )

    client = BetaAnalyticsDataClient(credentials=get_ga4_credentials())

    req = RunReportRequest(
        property=f"properties/{GA4_PROPERTY}",
        date_ranges=[DateRange(start_date=args.date_from, end_date=args.date_to)],
        dimensions=[Dimension(name="landingPagePlusQueryString"), Dimension(name="sessionCampaignName")],
        metrics=[Metric(name="sessions")],
        dimension_filter=FilterExpression(
            filter=Filter(
                field_name="sessionMedium",
                string_filter=Filter.StringFilter(value="cpc", match_type=Filter.StringFilter.MatchType.EXACT),
            )
        ),
        order_bys=[OrderBy(metric=OrderBy.MetricOrderBy(metric_name="sessions"), desc=True)],
        limit=args.limit,
    )
    resp = client.run_report(req)

    rows = []
    for r in resp.rows:
        rows.append(
            {
                "landing_page": r.dimension_values[0].value,
                "campaign": r.dimension_values[1].value,
                "sessions": int(r.metric_values[0].value),
            }
        )

    # agregat po cistoj putanji (bez query stringa)
    by_path: dict[str, int] = {}
    for row in rows:
        path = row["landing_page"].split("?")[0]
        by_path[path] = by_path.get(path, 0) + row["sessions"]

    print(
        json.dumps(
            {
                "period": {"from": args.date_from, "to": args.date_to},
                "napomena": "sessionMedium=cpc; landing page je URL POSLE eventualnog redirekta",
                "rows": rows,
                "unique_paths": [
                    {"path": p, "sessions": s} for p, s in sorted(by_path.items(), key=lambda x: -x[1])
                ],
                "brojevi": {"rows": len(rows), "unique_paths": len(by_path)},
            },
            ensure_ascii=False,
            indent=2,
        )
    )


if __name__ == "__main__":
    try:
        main()
    except SystemExit:
        raise
    except Exception as exc:  # noqa: BLE001
        friendly_api_error(exc)
