#!/usr/bin/env python
"""Google Ads API — zamena za Windsor.ai `google_ads` konektor.

Vraca potrosnju/klikove/CTR/CPC/konverzije po kampanji za nalog 156-886-0314
("Gogin Nalog"). Zahteva odobren developer token (Basic access) - dok se ne
odobri, radi samo protiv test naloga. Videti reference/api-konektor-setup.md
korak 4.

Primer:
    python ads_report.py --from 2026-07-20 --to 2026-07-26
"""

import argparse
import json
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
from auth import friendly_api_error, get_ads_client  # noqa: E402

CUSTOMER_ID = "1568860314"


def main() -> None:
    parser = argparse.ArgumentParser()
    parser.add_argument("--from", dest="date_from", required=True, help="YYYY-MM-DD")
    parser.add_argument("--to", dest="date_to", required=True, help="YYYY-MM-DD")
    args = parser.parse_args()

    client, customer_id = get_ads_client(CUSTOMER_ID)
    ga_service = client.get_service("GoogleAdsService")

    query = f"""
        SELECT
            campaign.name,
            metrics.cost_micros,
            metrics.clicks,
            metrics.impressions,
            metrics.ctr,
            metrics.average_cpc,
            metrics.conversions
        FROM campaign
        WHERE segments.date BETWEEN '{args.date_from}' AND '{args.date_to}'
        ORDER BY campaign.name
    """

    campaigns = []
    total_spend = total_clicks = total_impressions = total_conversions = 0.0

    stream = ga_service.search_stream(customer_id=customer_id, query=query)
    for batch in stream:
        for row in batch.results:
            spend = row.metrics.cost_micros / 1_000_000
            clicks = row.metrics.clicks
            impressions = row.metrics.impressions
            conversions = row.metrics.conversions
            campaigns.append(
                {
                    "campaign": row.campaign.name,
                    "spend_rsd": round(spend, 2),
                    "clicks": clicks,
                    "impressions": impressions,
                    "ctr_pct": round(row.metrics.ctr * 100, 2),
                    "avg_cpc_rsd": round(row.metrics.average_cpc / 1_000_000, 2),
                    "conversions": round(conversions, 2),
                }
            )
            total_spend += spend
            total_clicks += clicks
            total_impressions += impressions
            total_conversions += conversions

    print(
        json.dumps(
            {
                "period": {"from": args.date_from, "to": args.date_to},
                "campaigns": campaigns,
                "totals": {
                    "spend_rsd": round(total_spend, 2),
                    "clicks": total_clicks,
                    "impressions": total_impressions,
                    "conversions": round(total_conversions, 2),
                },
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
