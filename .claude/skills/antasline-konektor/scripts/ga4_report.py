#!/usr/bin/env python
"""GA4 Data API — zamena za Windsor.ai `googleanalytics4` konektor.

Vraća kompaktan JSON: korisnici, sesije, brojevi za generate_lead/tel/mailto,
i hvala-proxy (screenPageViews na /hvala-za-poruku/) za jedan period.

Pravilo (nasledjeno od Windsor rada): UVEK eksplicitni --from/--to
(YYYY-MM-DD), nikad relativni presets — poziva se posebno za tekući i
prethodni period kad treba poređenje.

Primer:
    python ga4_report.py --from 2026-07-20 --to 2026-07-26
"""

import argparse
import json
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
from auth import friendly_api_error, get_ga4_credentials  # noqa: E402

GA4_PROPERTY = "292720335"
KEY_EVENTS = ["generate_lead", "tel", "mailto"]


def main() -> None:
    parser = argparse.ArgumentParser()
    parser.add_argument("--from", dest="date_from", required=True, help="YYYY-MM-DD")
    parser.add_argument("--to", dest="date_to", required=True, help="YYYY-MM-DD")
    args = parser.parse_args()

    from google.analytics.data_v1beta import BetaAnalyticsDataClient
    from google.analytics.data_v1beta.types import (
        DateRange,
        Dimension,
        Filter,
        FilterExpression,
        Metric,
        RunReportRequest,
    )

    client = BetaAnalyticsDataClient(credentials=get_ga4_credentials())
    date_range = DateRange(start_date=args.date_from, end_date=args.date_to)
    property_path = f"properties/{GA4_PROPERTY}"

    # 1) Totali: korisnici + sesije
    totals_resp = client.run_report(
        RunReportRequest(
            property=property_path,
            date_ranges=[date_range],
            metrics=[Metric(name="activeUsers"), Metric(name="sessions")],
        )
    )
    if totals_resp.rows:
        row = totals_resp.rows[0]
        users = int(row.metric_values[0].value)
        sessions = int(row.metric_values[1].value)
    else:
        users = sessions = 0

    # 2) Svi eventi nefiltrirano, agregacija u Python-u (pouzdanije od API-side filtera)
    events_resp = client.run_report(
        RunReportRequest(
            property=property_path,
            date_ranges=[date_range],
            dimensions=[Dimension(name="eventName")],
            metrics=[Metric(name="eventCount")],
        )
    )
    all_events = {r.dimension_values[0].value: int(r.metric_values[0].value) for r in events_resp.rows}
    key_events = {name: all_events.get(name, 0) for name in KEY_EVENTS}

    # 3) Hvala-proxy (prava konverzija): screenPageViews gde pagePath sadrzi "hvala"
    hvala_resp = client.run_report(
        RunReportRequest(
            property=property_path,
            date_ranges=[date_range],
            dimensions=[Dimension(name="pagePath")],
            metrics=[Metric(name="screenPageViews")],
            dimension_filter=FilterExpression(
                filter=Filter(
                    field_name="pagePath",
                    string_filter=Filter.StringFilter(match_type=Filter.StringFilter.MatchType.CONTAINS, value="hvala"),
                )
            ),
        )
    )
    hvala_proxy = sum(int(r.metric_values[0].value) for r in hvala_resp.rows)

    print(
        json.dumps(
            {
                "period": {"from": args.date_from, "to": args.date_to},
                "users": users,
                "sessions": sessions,
                "events": key_events,
                "hvala_proxy_pageviews": hvala_proxy,
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
