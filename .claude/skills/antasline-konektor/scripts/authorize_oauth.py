#!/usr/bin/env python
"""Jednokratna interaktivna OAuth autorizacija za Google Ads + Business Profile.

Otvara browser, Miroslav se loguje Google nalogom koji upravlja Ads nalogom
156-886-0314 i GMB stranicom "Industrijski podovi AntasLine", odobrava pristup.
Refresh token se cuva u credentials/token.json - posle ovoga se ne ponavlja
(auth.get_oauth_credentials automatski osvezava kad istekne).

Pokrenuti SAMO JEDNOM (ili ponovo ako se token.json obrise/opozove pristup):
    python authorize_oauth.py
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
from auth import oauth_client_path, token_path  # noqa: E402

SCOPES = [
    "https://www.googleapis.com/auth/adwords",
    "https://www.googleapis.com/auth/business.manage",
    # dodato 2026-07-27 — GTM API (gtm_mailto_tag.py); trazi ukljucen "Tag Manager API"
    # u istom Google Cloud projektu. Posle dodavanja scope-a ovaj skript se pokrece PONOVO
    # (postojeci token.json nema tagmanager scope pa bi GTM poziv pukao na 403/insufficient).
    "https://www.googleapis.com/auth/tagmanager.edit.containers",
]


def main() -> None:
    from google_auth_oauthlib.flow import InstalledAppFlow

    client_path = oauth_client_path()  # fail-fast ako oauth-client.json ne postoji
    flow = InstalledAppFlow.from_client_secrets_file(str(client_path), SCOPES)
    creds = flow.run_local_server(port=0)

    out_path = token_path()
    out_path.write_text(creds.to_json(), encoding="utf-8")
    print(f"Gotovo. Token sacuvan: {out_path}")


if __name__ == "__main__":
    main()
