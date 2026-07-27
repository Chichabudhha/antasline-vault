#!/usr/bin/env python
"""GTM API — kreira `mailto` trigger + GA4 Event tag u WORKSPACE-u (ne objavljuje).

Zasto postoji: `mailto` je do 2026-06-26 punio MonsterInsights; gasenjem MI-ja u
BLOK A `generate_lead` je prevezan i `tel` je dobio svoj GTM tag, ali mailto nije —
u objavljenom kontejneru GTM-TRDT8K9 ne postoji ni tag ni trigger za mailto
(potvrdjeno direktnim fetch-om gtm.js 2026-07-27). Rezultat: 30 dana tacno nula.

Pravi TACNO isti obrazac kao postojeci `tel` tag (tag_id 32):
  - Trigger: Just Links (LINK_CLICK), Click URL contains "mailto:", waitForTags=false
  - Tag: GA4 Event (gaawe), eventName="mailto", parametar mailto_address={{Click URL}}
  - measurementIdOverride: G-H8BRCZN8W4

⚠️ NE objavljuje (nema publish poziva) — ostaje kao izmena u workspace-u dok
Miroslav ne uradi Submit u GTM UI. Isti tretman kao pdf_download/gallery_view draftovi.

Preduslovi (jednokratno, oba trazi Miroslav):
  1. Ukljuciti Tag Manager API u istom Google Cloud projektu:
     console.cloud.google.com -> APIs & Services -> Enable -> "Tag Manager API"
  2. Dodati tagmanager scope u token:
     python authorize_oauth.py        (SCOPES lista vec sadrzi tagmanager.edit.containers)

Pokretanje:
    python gtm_mailto_tag.py            # kreira (idempotentno — preskace ako vec postoji)
    python gtm_mailto_tag.py --dry-run  # samo ispise sta bi uradio
"""

import argparse
import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
from auth import friendly_api_error, get_oauth_credentials  # noqa: E402

CONTAINER_PUBLIC_ID = "GTM-TRDT8K9"
MEASUREMENT_ID = "G-H8BRCZN8W4"
SCOPES = ["https://www.googleapis.com/auth/tagmanager.edit.containers"]

TRIGGER_NAME = "Klik na mailto"
TAG_NAME = "GA4 - mailto"


def find_container(service):
    accounts = service.accounts().list().execute().get("account", [])
    for acc in accounts:
        containers = (
            service.accounts()
            .containers()
            .list(parent=acc["path"])
            .execute()
            .get("container", [])
        )
        for c in containers:
            if c.get("publicId") == CONTAINER_PUBLIC_ID:
                return c
    raise SystemExit(f"GRESKA: kontejner {CONTAINER_PUBLIC_ID} nije nadjen na ovom nalogu.")


def default_workspace(service, container):
    ws = (
        service.accounts()
        .containers()
        .workspaces()
        .list(parent=container["path"])
        .execute()
        .get("workspace", [])
    )
    if not ws:
        raise SystemExit("GRESKA: kontejner nema nijedan workspace.")
    for w in ws:
        if w.get("name") == "Default Workspace":
            return w
    return ws[0]


def main():
    p = argparse.ArgumentParser()
    p.add_argument("--dry-run", action="store_true")
    args = p.parse_args()

    from googleapiclient.discovery import build

    creds = get_oauth_credentials(SCOPES)
    service = build("tagmanager", "v2", credentials=creds)

    container = find_container(service)
    ws = default_workspace(service, container)
    print(f"Kontejner: {container['name']} ({CONTAINER_PUBLIC_ID})")
    print(f"Workspace: {ws['name']}\n")

    ws_api = service.accounts().containers().workspaces()

    existing_triggers = ws_api.triggers().list(parent=ws["path"]).execute().get("trigger", [])
    trigger = next((t for t in existing_triggers if t["name"] == TRIGGER_NAME), None)

    trigger_body = {
        "name": TRIGGER_NAME,
        "type": "linkClick",
        "waitForTags": {"type": "boolean", "value": "false"},
        "checkValidation": {"type": "boolean", "value": "false"},
        "filter": [
            {
                "type": "contains",
                "parameter": [
                    {"type": "template", "key": "arg0", "value": "{{Click URL}}"},
                    {"type": "template", "key": "arg1", "value": "mailto:"},
                ],
            }
        ],
    }

    if trigger:
        print(f"= trigger '{TRIGGER_NAME}' vec postoji (id {trigger['triggerId']}) — preskacem")
    elif args.dry_run:
        print(f"[dry-run] kreirao bih trigger '{TRIGGER_NAME}' (Just Links, Click URL contains 'mailto:')")
        trigger = {"triggerId": "<novi>"}
    else:
        trigger = ws_api.triggers().create(parent=ws["path"], body=trigger_body).execute()
        print(f"+ trigger kreiran: {TRIGGER_NAME} (id {trigger['triggerId']})")

    existing_tags = ws_api.tags().list(parent=ws["path"]).execute().get("tag", [])
    if any(t["name"] == TAG_NAME for t in existing_tags):
        print(f"= tag '{TAG_NAME}' vec postoji — preskacem")
        return

    tag_body = {
        "name": TAG_NAME,
        "type": "gaawe",
        "parameter": [
            {"type": "template", "key": "eventName", "value": "mailto"},
            {"type": "boolean", "key": "sendEcommerceData", "value": "false"},
            {"type": "template", "key": "measurementIdOverride", "value": MEASUREMENT_ID},
            {"type": "boolean", "key": "enableUserProperties", "value": "true"},
            {
                "type": "list",
                "key": "eventSettingsTable",
                "list": [
                    {
                        "type": "map",
                        "map": [
                            {"type": "template", "key": "parameter", "value": "mailto_address"},
                            {"type": "template", "key": "parameterValue", "value": "{{Click URL}}"},
                        ],
                    }
                ],
            },
        ],
        "firingTriggerId": [str(trigger["triggerId"])],
    }

    if args.dry_run:
        print(f"[dry-run] kreirao bih tag '{TAG_NAME}' (GA4 Event, eventName=mailto)")
        return

    tag = ws_api.tags().create(parent=ws["path"], body=tag_body).execute()
    print(f"+ tag kreiran: {TAG_NAME} (id {tag['tagId']})")
    print("\nGOTOVO — izmene su u WORKSPACE-u, NISU objavljene.")
    print("Sledeci korak (Miroslav): GTM UI -> Submit -> Publish.")
    print("Preporuka: spojiti sa pdf_download/gallery_view draftovima u isti Submit.")


if __name__ == "__main__":
    try:
        main()
    except SystemExit:
        raise
    except Exception as exc:  # noqa: BLE001
        friendly_api_error(exc)
