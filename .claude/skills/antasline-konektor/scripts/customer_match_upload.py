#!/usr/bin/env python
"""Hesuje email-ove iz leads.csv (v. scan_leads.py) i upload-uje ih u Google Ads
Customer Match listu preko OfflineUserDataJobService.

Namenjeno da se pokrece NA cPANEL SERVERU (u [cpanel-live] sesiji) posle
scan_leads.py — vidi CLAUDE-CODE-instrukcija-CPANEL.md.

DRY-RUN JE PODRAZUMEVAN: skripta samo ispise koliko NOVIH (uploaded=False)
kontakata bi bilo poslato, bez ijednog Google Ads write poziva. Potreban je
eksplicitan --confirm da bi se job stvarno kreirao/pokrenuo.

Google Ads Customer Match zahteva: email normalizovan (lowercase, trim) pa
SHA-256 hesovan pre slanja — nikad plaintext.

Kredencijali: reuse-uje postojeci antasline-konektor OAuth token (scope
'adwords' vec pokriva write operacije, nije potreban nov re-authorize).

--split-by-category deli pending kontakte u odvojene Customer Match liste po
'interest_category' kolomi (v. categorize_leads.py) — svaka poznata kategorija
dobija svoju listu ("<list-name> - <kategorija>"), a 'nepoznato'/nekategorisano
ostaje u glavnoj listi (--list-name). Bez ove zastavice, ponasanje je isto kao
pre: svi pending kontakti idu u jednu listu.

Pokretanje:
    python customer_match_upload.py                                  # dry-run, samo brojevi
    python customer_match_upload.py --confirm                        # stvarni upload, jedna lista
    python customer_match_upload.py --confirm --split-by-category    # stvarni upload, po kategoriji
    python customer_match_upload.py --confirm --list-name "AntasLine - Website Leads"
"""

import argparse
import hashlib
import sys
from collections import defaultdict
from datetime import date
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
from auth import friendly_api_error, get_ads_client  # noqa: E402
from scan_leads import leads_csv_path, load_leads, save_leads  # noqa: E402

DEFAULT_LIST_NAME = "AntasLine - Website Leads"
# Google Ads od 2025-04-07 nameće tvrd max od 540 dana za CRM-based (Customer Match)
# liste — stariji "10000 = bez isteka" sentinel je ukinut i API ga odbija (RangeError.TOO_HIGH).
MAX_MEMBERSHIP_LIFE_SPAN_DAYS = 540

# Isti taksonomija kao categorize_leads.py / GA4 publike (CLAUDE.md sek. 5).
# "nepoznato" nema sufiks — ti kontakti ostaju u glavnoj listi (--list-name).
CATEGORY_LIST_SUFFIX = {
    "industrijski-esd-ecotile": "Industrijski, ESD, Ecotile",
    "sportski-tereni": "Sportski tereni",
    "terase-spoljne-podloge": "Terase i spoljne podloge",
    "lvt-expona": "LVT, Expona",
    "vestacka-trava": "Vestacka trava",
    "epoksid-conquest": "Epoksid conquest",
}


def normalize_and_hash_email(email: str) -> str:
    normalized = email.strip().lower()
    return hashlib.sha256(normalized.encode("utf-8")).hexdigest()


def get_or_create_user_list(client, customer_id: str, list_name: str) -> str:
    ga_service = client.get_service("GoogleAdsService")
    query = f"SELECT user_list.resource_name FROM user_list WHERE user_list.name = '{list_name}'"
    response = ga_service.search(customer_id=customer_id, query=query)
    for row in response:
        return row.user_list.resource_name

    user_list_service = client.get_service("UserListService")
    operation = client.get_type("UserListOperation")
    user_list = operation.create
    user_list.name = list_name
    user_list.description = "AntasLine kontakt forma (CF7) — leadovi iz office@antasline.com"
    user_list.membership_life_span = MAX_MEMBERSHIP_LIFE_SPAN_DAYS
    user_list.crm_based_user_list.upload_key_type = (
        client.enums.CustomerMatchUploadKeyTypeEnum.CONTACT_INFO
    )
    response = user_list_service.mutate_user_lists(customer_id=customer_id, operations=[operation])
    return response.results[0].resource_name


def build_operations(client, hashed_emails):
    operations = []
    for hashed_email in hashed_emails:
        op = client.get_type("OfflineUserDataJobOperation")
        identifier = client.get_type("UserIdentifier")
        identifier.hashed_email = hashed_email
        op.create.user_identifiers.append(identifier)
        operations.append(op)
    return operations


def group_pending_by_list(pending, base_list_name, split_by_category):
    """Vraca {list_name: [rows]}. Bez split_by_category: sve u jednu listu."""
    if not split_by_category:
        return {base_list_name: pending}

    groups = defaultdict(list)
    for row in pending:
        category = (row.get("interest_category") or "").strip()
        suffix = CATEGORY_LIST_SUFFIX.get(category)
        list_name = f"{base_list_name} - {suffix}" if suffix else base_list_name
        groups[list_name].append(row)
    return dict(groups)


def upload_group(client, customer_id, list_name, rows):
    user_list_resource_name = get_or_create_user_list(client, customer_id, list_name)
    print(f"User lista: {list_name} ({user_list_resource_name})")

    hashed_emails = [normalize_and_hash_email(row["email"]) for row in rows]

    offline_user_data_job_service = client.get_service("OfflineUserDataJobService")
    job = client.get_type("OfflineUserDataJob")
    job.type_ = client.enums.OfflineUserDataJobTypeEnum.CUSTOMER_MATCH_USER_LIST
    job.customer_match_user_list_metadata.user_list = user_list_resource_name

    create_response = offline_user_data_job_service.create_offline_user_data_job(
        customer_id=customer_id, job=job
    )
    job_resource_name = create_response.resource_name

    request = client.get_type("AddOfflineUserDataJobOperationsRequest")
    request.resource_name = job_resource_name
    request.operations = build_operations(client, hashed_emails)
    request.enable_partial_failure = True

    offline_user_data_job_service.add_offline_user_data_job_operations(request=request)
    offline_user_data_job_service.run_offline_user_data_job(resource_name=job_resource_name)

    today = date.today().isoformat()
    for row in rows:
        row["uploaded"] = "True"
        row["upload_date"] = today

    print(f"  +{len(rows)} kontakata poslato u '{list_name}'")


def main():
    p = argparse.ArgumentParser()
    p.add_argument("--confirm", action="store_true", help="stvarno kreira/pokrece upload job (bez ovoga: dry-run)")
    p.add_argument("--list-name", default=DEFAULT_LIST_NAME)
    p.add_argument("--customer-id", default="1568860314")
    p.add_argument(
        "--split-by-category",
        action="store_true",
        help="deli pending kontakte u odvojene liste po interest_category (v. categorize_leads.py)",
    )
    args = p.parse_args()

    if not leads_csv_path().exists():
        raise SystemExit(f"GRESKA: {leads_csv_path()} ne postoji — pokreni prvo scan_leads.py")

    leads = load_leads()
    pending = [row for row in leads.values() if row.get("uploaded") != "True"]

    print(f"Ukupno kontakata u leads.csv: {len(leads)}")
    print(f"Kandidata za upload (uploaded=False): {len(pending)}")

    if not pending:
        print("Nema novih kontakata za upload.")
        return

    groups = group_pending_by_list(pending, args.list_name, args.split_by_category)
    if args.split_by_category:
        print("Podela po kategoriji:")
        for list_name, rows in groups.items():
            print(f"  {list_name}: {len(rows)}")

    if not args.confirm:
        print("[dry-run] Google Ads API NIJE pozvan. Pokreni sa --confirm za stvarni upload.")
        return

    client, customer_id = get_ads_client(args.customer_id)

    for list_name, rows in groups.items():
        upload_group(client, customer_id, list_name, rows)

    save_leads(leads)

    print(f"Upload pokrenut: +{len(pending)} kontakata, kumulativno u leads.csv: {len(leads)}")
    print("Google Ads asinhrono obradjuje job(ove) — clanstvo u listi(ama) se popunjava sa zakasnjenjem.")


if __name__ == "__main__":
    try:
        main()
    except SystemExit:
        raise
    except Exception as exc:  # noqa: BLE001
        friendly_api_error(exc)
