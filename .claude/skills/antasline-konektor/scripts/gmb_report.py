#!/usr/bin/env python
"""Google Business Profile Performance API - zamena za Windsor.ai GMB pokrivenost
(koja je i preko Windsora bila ogranicena - recenzije/objave ostaju rucno kroz
GMB dashboard, ovo pokriva samo merljive metrike: pregledi/pozivi/klikovi).

Prvi put: auto-discover-uje location preko Account Management + Business
Information API-ja i ispise resource name da se moze keširati sa --location
u buducim pozivima (brze, bez 2 dodatna poziva).

Primer:
    python gmb_report.py --from 2026-07-20 --to 2026-07-26
    python gmb_report.py --from 2026-07-20 --to 2026-07-26 --location locations/1234567890
"""

import argparse
import json
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
from auth import friendly_api_error, get_gmb_service_account_credentials, get_oauth_credentials  # noqa: E402

SCOPES = ["https://www.googleapis.com/auth/business.manage"]


def get_gmb_credentials():
    """Prvo pokusaj service account (ako je dodat kao manager na Business Profile nalogu),
    fallback na OAuth (authorize_oauth.py) ako service account ne postoji/ne uspe."""
    creds = get_gmb_service_account_credentials(SCOPES)
    if creds is not None:
        return creds
    return get_oauth_credentials(SCOPES)

METRICS = [
    "BUSINESS_IMPRESSIONS_DESKTOP_MAPS",
    "BUSINESS_IMPRESSIONS_DESKTOP_SEARCH",
    "BUSINESS_IMPRESSIONS_MOBILE_MAPS",
    "BUSINESS_IMPRESSIONS_MOBILE_SEARCH",
    "CALL_CLICKS",
    "WEBSITE_CLICKS",
    "BUSINESS_DIRECTION_REQUESTS",
]


def discover_location(creds) -> str:
    from googleapiclient.discovery import build

    account_service = build("mybusinessaccountmanagement", "v1", credentials=creds)
    accounts = account_service.accounts().list().execute().get("accounts", [])
    if not accounts:
        print("GRESKA: nijedan GMB nalog nije dostupan ovom OAuth korisniku.", file=sys.stderr)
        sys.exit(1)
    account_name = accounts[0]["name"]

    info_service = build("mybusinessbusinessinformation", "v1", credentials=creds)
    locations = (
        info_service.accounts()
        .locations()
        .list(parent=account_name, readMask="name,title")
        .execute()
        .get("locations", [])
    )
    if not locations:
        print(f"GRESKA: nijedna lokacija nije nadjena za nalog {account_name}.", file=sys.stderr)
        sys.exit(1)
    return locations[0]["name"]


def main() -> None:
    parser = argparse.ArgumentParser()
    parser.add_argument("--from", dest="date_from", required=True, help="YYYY-MM-DD")
    parser.add_argument("--to", dest="date_to", required=True, help="YYYY-MM-DD")
    parser.add_argument("--location", default=None, help="npr. locations/1234567890 (preskace auto-discovery)")
    args = parser.parse_args()

    creds = get_gmb_credentials()

    location = args.location or discover_location(creds)

    from googleapiclient.discovery import build

    perf_service = build("businessprofileperformance", "v1", credentials=creds)

    y1, m1, d1 = (int(x) for x in args.date_from.split("-"))
    y2, m2, d2 = (int(x) for x in args.date_to.split("-"))

    totals = {}
    for metric in METRICS:
        resp = (
            perf_service.locations()
            .fetchMultiDailyMetricsTimeSeries(
                location=location,
                dailyMetrics=[metric],
                **{
                    "dailyRange.start_date.year": y1,
                    "dailyRange.start_date.month": m1,
                    "dailyRange.start_date.day": d1,
                    "dailyRange.end_date.year": y2,
                    "dailyRange.end_date.month": m2,
                    "dailyRange.end_date.day": d2,
                },
            )
            .execute()
        )
        series = resp.get("multiDailyMetricTimeSeries", [])
        total = 0
        for entry in series:
            for tsdata in entry.get("dailyMetricTimeSeries", []):
                for point in tsdata.get("timeSeries", {}).get("datedValues", []):
                    total += int(point.get("value", 0))
        totals[metric] = total

    print(
        json.dumps(
            {
                "period": {"from": args.date_from, "to": args.date_to},
                "location": location,
                "metrics": totals,
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
