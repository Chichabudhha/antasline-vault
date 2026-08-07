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

Pokretanje:
    python customer_match_upload.py                 # dry-run, samo brojevi
    python customer_match_upload.py --confirm        # stvarni upload
    python customer_match_upload.py --confirm --list-name "AntasLine - Website Leads"
"""

import argparse
import hashlib
import sys
from datetime import date
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
from auth import friendly_api_error, get_ads_client  # noqa: E402
from scan_leads import leads_csv_path, load_leads, save_leads  # noqa: E402

DEFAULT_LIST_NAME = "AntasLine - Website Leads"
NO_EXPIRY_MEMBERSHIP_LIFE_SPAN = 10000  # Google Ads konvencija: 10000 = bez isteka


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
    user_list.membership_life_span = NO_EXPIRY_MEMBERSHIP_LIFE_SPAN
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


def main():
    p = argparse.ArgumentParser()
    p.add_argument("--confirm", action="store_true", help="stvarno kreira/pokrece upload job (bez ovoga: dry-run)")
    p.add_argument("--list-name", default=DEFAULT_LIST_NAME)
    p.add_argument("--customer-id", default="1568860314")
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

    if not args.confirm:
        print("[dry-run] Google Ads API NIJE pozvan. Pokreni sa --confirm za stvarni upload.")
        return

    client, customer_id = get_ads_client(args.customer_id)

    user_list_resource_name = get_or_create_user_list(client, customer_id, args.list_name)
    print(f"User lista: {args.list_name} ({user_list_resource_name})")

    hashed_emails = [normalize_and_hash_email(row["email"]) for row in pending]

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
    for row in pending:
        row["uploaded"] = "True"
        row["upload_date"] = today
    save_leads(leads)

    print(f"Upload pokrenut: +{len(pending)} kontakata, kumulativno u listi: {len(leads)}")
    print("Google Ads asinhrono obradjuje job — clanstvo u listi se popunjava sa zakasnjenjem.")


if __name__ == "__main__":
    try:
        main()
    except SystemExit:
        raise
    except Exception as exc:  # noqa: BLE001
        friendly_api_error(exc)
