#!/usr/bin/env python
"""GA4 Data API — zamena za Windsor.ai `googleanalytics4` konektor.

Vraća kompaktan JSON: korisnici, sesije, brojevi za generate_lead/tel/mailto,
i hvala-proxy (screenPageViews na /hvala-za-poruku/) za jedan period.

Pravilo (nasledjeno od Windsor rada): UVEK eksplicitni --from/--to
(YYYY-MM-DD), nikad relativni presets — poziva se posebno za tekući i
prethodni period kad treba poređenje.

--live-only iskljucuje ne-produkcijske hostname-ove (localhost/staging/
127.0.0.1). BEZ tog flag-a ponasanje je nepromenjeno (svi hostovi) — trajni
filter u svim izvestajima ceka M odluku, v. PROGRESS Blokeri 2026-08-11.
🔴 Za svaki izvestaj koji ide Miroslavu: pozivati SA --live-only.
Izlaz uvek nosi i `hosts` raspodelu, da kontaminacija bude vidljiva.

Primer:
    python ga4_report.py --from 2026-07-20 --to 2026-07-26 --live-only
"""

import argparse
import json
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
from auth import friendly_api_error, get_ga4_credentials  # noqa: E402

GA4_PROPERTY = "292720335"
KEY_EVENTS = ["generate_lead", "tel", "mailto"]
# Ne-produkcijski hostname-ovi (izmereno 2026-08-11: jul je imao 1.075 pregleda
# sa localhost-a = 15% svih, a nedelja 28.07–03.08 cak 42%).
NONLIVE_EXACT = ["localhost", "127.0.0.1"]
NONLIVE_PREFIX = ["staging.", "test.", "dev."]


def main() -> None:
    parser = argparse.ArgumentParser()
    parser.add_argument("--from", dest="date_from", required=True, help="YYYY-MM-DD")
    parser.add_argument("--to", dest="date_to", required=True, help="YYYY-MM-DD")
    parser.add_argument(
        "--live-only",
        action="store_true",
        help="iskljuci localhost/staging/test/dev hostname-ove (preporuceno za svaki izvestaj)",
    )
    args = parser.parse_args()

    from google.analytics.data_v1beta import BetaAnalyticsDataClient
    from google.analytics.data_v1beta.types import (
        DateRange,
        Dimension,
        Filter,
        FilterExpression,
        FilterExpressionList,
        Metric,
        RunReportRequest,
    )

    client = BetaAnalyticsDataClient(credentials=get_ga4_credentials())
    date_range = DateRange(start_date=args.date_from, end_date=args.date_to)
    property_path = f"properties/{GA4_PROPERTY}"

    def host_expr(match_type, value):
        return FilterExpression(
            filter=Filter(
                field_name="hostName",
                string_filter=Filter.StringFilter(match_type=match_type, value=value),
            )
        )

    live_filter = None
    if args.live_only:
        exact = Filter.StringFilter.MatchType.EXACT
        prefix = Filter.StringFilter.MatchType.BEGINS_WITH
        live_filter = FilterExpression(
            not_expression=FilterExpression(
                or_group=FilterExpressionList(
                    expressions=[host_expr(exact, h) for h in NONLIVE_EXACT]
                    + [host_expr(prefix, p) for p in NONLIVE_PREFIX]
                )
            )
        )

    # 0) Raspodela po hostname-u — uvek, da kontaminacija bude vidljiva u izlazu
    hosts_resp = client.run_report(
        RunReportRequest(
            property=property_path,
            date_ranges=[date_range],
            dimensions=[Dimension(name="hostName")],
            metrics=[Metric(name="activeUsers"), Metric(name="screenPageViews")],
        )
    )
    hosts = {
        r.dimension_values[0].value: {
            "users": int(r.metric_values[0].value),
            "pageviews": int(r.metric_values[1].value),
        }
        for r in hosts_resp.rows
    }

    # 1) Totali: korisnici + sesije
    totals_resp = client.run_report(
        RunReportRequest(
            property=property_path,
            date_ranges=[date_range],
            metrics=[Metric(name="activeUsers"), Metric(name="sessions")],
            dimension_filter=live_filter,
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
            dimension_filter=live_filter,
        )
    )
    all_events = {r.dimension_values[0].value: int(r.metric_values[0].value) for r in events_resp.rows}
    key_events = {name: all_events.get(name, 0) for name in KEY_EVENTS}

    # 3) Hvala-proxy (prava konverzija): screenPageViews gde pagePath sadrzi "hvala"
    hvala_path_filter = FilterExpression(
        filter=Filter(
            field_name="pagePath",
            string_filter=Filter.StringFilter(match_type=Filter.StringFilter.MatchType.CONTAINS, value="hvala"),
        )
    )
    hvala_resp = client.run_report(
        RunReportRequest(
            property=property_path,
            date_ranges=[date_range],
            dimensions=[Dimension(name="pagePath")],
            metrics=[Metric(name="screenPageViews"), Metric(name="sessions")],
            dimension_filter=(
                FilterExpression(and_group=FilterExpressionList(expressions=[hvala_path_filter, live_filter]))
                if live_filter
                else hvala_path_filter
            ),
        )
    )
    hvala_proxy = sum(int(r.metric_values[0].value) for r in hvala_resp.rows)
    hvala_sessions = sum(int(r.metric_values[1].value) for r in hvala_resp.rows)

    # 4) Korekcija merenja (dijagnoza 2026-08-11) — vazi SAMO za live saobracaj
    #    do migracije 2026-08-24; posle nje faktori padaju na 1 (v. napomenu).
    korekcija = {
        "vazi_do": "2026-08-24",
        "faktor_hvala_proxy": 2,
        "faktor_generate_lead": 3,
        "napomena": (
            "Suvisan GA4 page_view tag id 18 duplira hvala-proxy (2x, postoji i na "
            "buildu -> prezivljava migraciju, dok se tag ne obrise). Live Kallyas ima "
            "dva GTM embeda -> generate_lead 3x (nestaje migracijom). Deljenje ima "
            "smisla samo za live saobracaj."
        ),
        "stvarni_dolasci_est": round(hvala_proxy / 2),
        "hvala_proxy_sessions": hvala_sessions,
    }

    print(
        json.dumps(
            {
                "period": {"from": args.date_from, "to": args.date_to},
                "live_only": bool(args.live_only),
                "hosts": hosts,
                "users": users,
                "sessions": sessions,
                "events": key_events,
                "hvala_proxy_pageviews": hvala_proxy,
                "korekcija_merenja": korekcija,
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
