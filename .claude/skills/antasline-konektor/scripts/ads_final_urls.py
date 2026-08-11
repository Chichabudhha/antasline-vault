#!/usr/bin/env python
"""Google Ads API — popis SVIH odredisnih (final) URL-ova u nalogu.

Namena: W4 4.10 "final URL audit oglasa" pre migracije 2026-08-24. Posle
migracije slugovi mogu da se promene, pa svaki oglas/sitelink koji pokazuje
na stari URL gubi Quality Score (301 pomaze, ali landing page experience se
meri na finalnom URL-u).

READ-ONLY — ne menja nista u nalogu.

Pokriva:
  - ad_group_ad          (final_urls + final_mobile_urls svakog oglasa)
  - ad_group_criterion   (keyword-level final_urls, ako postoje)
  - campaign_asset       (sitelink/callout/promotion asset-i na nivou kampanje)
  - ad_group_asset       (isto na nivou ad grupe)
  - customer_asset       (nalog-nivo asset-i)
  - tracking_url_template na nivou kampanje i naloga

Primer:
    python ads_final_urls.py
    python ads_final_urls.py --include-removed
"""

import argparse
import json
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
from auth import friendly_api_error, get_ads_client  # noqa: E402

CUSTOMER_ID = "1568860314"


def _rows(ga_service, customer_id, query):
    out = []
    for batch in ga_service.search_stream(customer_id=customer_id, query=query):
        out.extend(batch.results)
    return out


def main() -> None:
    parser = argparse.ArgumentParser()
    parser.add_argument(
        "--include-removed",
        action="store_true",
        help="ukljuci i REMOVED oglase/asset-e (podrazumevano se preskacu)",
    )
    args = parser.parse_args()

    # Windows konzola je cp1250 — bez ovoga cirilicni URL-ovi obore print sa
    # UnicodeEncodeError cim se izlaz preusmeri u fajl.
    try:
        sys.stdout.reconfigure(encoding="utf-8")
    except Exception:  # noqa: BLE001
        pass

    client, customer_id = get_ads_client(CUSTOMER_ID)
    ga = client.get_service("GoogleAdsService")

    status_filter = "" if args.include_removed else "AND ad_group_ad.status != 'REMOVED'"

    # --- 1. Oglasi -------------------------------------------------------
    ads = []
    q_ads = f"""
        SELECT
            campaign.name, campaign.status,
            ad_group.name, ad_group.status,
            ad_group_ad.status,
            ad_group_ad.ad.id,
            ad_group_ad.ad.type,
            ad_group_ad.ad.final_urls,
            ad_group_ad.ad.final_mobile_urls,
            ad_group_ad.ad.tracking_url_template,
            ad_group_ad.ad.responsive_search_ad.path1,
            ad_group_ad.ad.responsive_search_ad.path2
        FROM ad_group_ad
        WHERE campaign.status != 'REMOVED' {status_filter}
        ORDER BY campaign.name, ad_group.name
    """
    for r in _rows(ga, customer_id, q_ads):
        ads.append(
            {
                "campaign": r.campaign.name,
                "campaign_status": r.campaign.status.name,
                "ad_group": r.ad_group.name,
                "ad_group_status": r.ad_group.status.name,
                "ad_id": r.ad_group_ad.ad.id,
                "ad_type": r.ad_group_ad.ad.type_.name,
                "ad_status": r.ad_group_ad.status.name,
                "final_urls": list(r.ad_group_ad.ad.final_urls),
                "final_mobile_urls": list(r.ad_group_ad.ad.final_mobile_urls),
                "tracking_url_template": r.ad_group_ad.ad.tracking_url_template or None,
                "path1": r.ad_group_ad.ad.responsive_search_ad.path1 or None,
                "path2": r.ad_group_ad.ad.responsive_search_ad.path2 or None,
            }
        )

    # --- 2. Keyword-level final URL-ovi ----------------------------------
    keywords = []
    q_kw = """
        SELECT
            campaign.name, campaign.status, ad_group.name,
            ad_group_criterion.criterion_id,
            ad_group_criterion.keyword.text,
            ad_group_criterion.keyword.match_type,
            ad_group_criterion.status,
            ad_group_criterion.final_urls,
            ad_group_criterion.tracking_url_template
        FROM ad_group_criterion
        WHERE ad_group_criterion.type = 'KEYWORD'
          AND ad_group_criterion.negative = FALSE
          AND ad_group_criterion.status != 'REMOVED'
          AND campaign.status != 'REMOVED'
        ORDER BY campaign.name, ad_group.name
    """
    for r in _rows(ga, customer_id, q_kw):
        urls = list(r.ad_group_criterion.final_urls)
        tmpl = r.ad_group_criterion.tracking_url_template or None
        if not urls and not tmpl:
            continue  # keyword bez sopstvenog URL-a nasledjuje oglas — nije predmet audita
        keywords.append(
            {
                "campaign": r.campaign.name,
                "ad_group": r.ad_group.name,
                "keyword": r.ad_group_criterion.keyword.text,
                "match_type": r.ad_group_criterion.keyword.match_type.name,
                "status": r.ad_group_criterion.status.name,
                "final_urls": urls,
                "tracking_url_template": tmpl,
            }
        )

    # --- 3. Asset-i (sitelink, promotion, call, ...) ---------------------
    assets = []
    asset_queries = {
        "campaign": """
            SELECT campaign.name, campaign.status, campaign_asset.field_type, campaign_asset.status,
                   asset.id, asset.type, asset.name, asset.final_urls,
                   asset.sitelink_asset.link_text
            FROM campaign_asset
            WHERE campaign_asset.status != 'REMOVED' AND campaign.status != 'REMOVED'
        """,
        "ad_group": """
            SELECT campaign.name, campaign.status, ad_group.name, ad_group_asset.field_type,
                   ad_group_asset.status, asset.id, asset.type, asset.name,
                   asset.final_urls, asset.sitelink_asset.link_text
            FROM ad_group_asset
            WHERE ad_group_asset.status != 'REMOVED' AND campaign.status != 'REMOVED'
        """,
        "customer": """
            SELECT customer_asset.field_type, customer_asset.status,
                   asset.id, asset.type, asset.name, asset.final_urls,
                   asset.sitelink_asset.link_text
            FROM customer_asset
            WHERE customer_asset.status != 'REMOVED'
        """,
    }
    for level, q in asset_queries.items():
        try:
            for r in _rows(ga, customer_id, q):
                urls = list(r.asset.final_urls)
                if not urls:
                    continue
                entry = {
                    "level": level,
                    "asset_id": r.asset.id,
                    "asset_type": r.asset.type_.name,
                    "asset_name": r.asset.name or None,
                    "link_text": r.asset.sitelink_asset.link_text or None,
                    "final_urls": urls,
                }
                if level in ("campaign", "ad_group"):
                    entry["campaign"] = r.campaign.name
                if level == "ad_group":
                    entry["ad_group"] = r.ad_group.name
                assets.append(entry)
        except Exception as exc:  # noqa: BLE001
            assets.append({"level": level, "greska": str(exc)[:300]})

    # --- 4. Tracking template na nivou kampanje/naloga -------------------
    campaigns = []
    q_camp = """
        SELECT campaign.name, campaign.status, campaign.tracking_url_template,
               campaign.final_url_suffix, campaign.advertising_channel_type
        FROM campaign
        WHERE campaign.status != 'REMOVED'
        ORDER BY campaign.name
    """
    for r in _rows(ga, customer_id, q_camp):
        campaigns.append(
            {
                "campaign": r.campaign.name,
                "status": r.campaign.status.name,
                "channel": r.campaign.advertising_channel_type.name,
                "tracking_url_template": r.campaign.tracking_url_template or None,
                "final_url_suffix": r.campaign.final_url_suffix or None,
            }
        )

    # --- 5. Jedinstveni URL-ovi (ono sto se stvarno auditira) ------------
    unique = {}
    for a in ads:
        for u in a["final_urls"] + a["final_mobile_urls"]:
            unique.setdefault(u, []).append(f"oglas {a['ad_id']} ({a['campaign']} / {a['ad_group']})")
    for k in keywords:
        for u in k["final_urls"]:
            unique.setdefault(u, []).append(f"keyword \"{k['keyword']}\" ({k['campaign']})")
    for s in assets:
        for u in s.get("final_urls", []):
            label = s.get("link_text") or s.get("asset_name") or s.get("asset_type")
            unique.setdefault(u, []).append(f"asset {s['asset_id']} [{label}] ({s['level']})")

    print(
        json.dumps(
            {
                "customer_id": CUSTOMER_ID,
                "campaigns": campaigns,
                "ads": ads,
                "keywords_with_own_url": keywords,
                "assets": assets,
                "unique_final_urls": [{"url": u, "koriste": v} for u, v in sorted(unique.items())],
                "brojevi": {
                    "ads": len(ads),
                    "keywords_with_own_url": len(keywords),
                    "assets_with_url": len([a for a in assets if a.get("final_urls")]),
                    "unique_urls": len(unique),
                },
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
