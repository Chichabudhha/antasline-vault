"""Zajednički helper za autentifikaciju — AntasLine sopstveni Google API konektor.

Kredencijali NIKAD ne žive u vault-u (git repo). Čitaju se iz
ANTASLINE_CONNECTOR_HOME (default C:\\Users\\Miroslav\\antasline-connector),
folder koji je potpuno van git stabla.

Dva puta autentifikacije:
- Service account (GA4 + GSC) — bez OAuth-a, jednom se doda pristup u
  GA4/GSC admin panelu pa radi zauvek.
- OAuth Desktop klijent (Ads + GMB) — traži user-consent jednom
  (authorize_oauth.py), posle toga koristi keširan refresh token.
"""

import json
import os
import sys
from pathlib import Path


def connector_home() -> Path:
    return Path(os.environ.get("ANTASLINE_CONNECTOR_HOME", str(Path.home() / "antasline-connector")))


def credentials_dir() -> Path:
    return connector_home() / "credentials"


def _fail(msg: str) -> None:
    print(f"GRESKA: {msg}", file=sys.stderr)
    sys.exit(1)


def service_account_path(name: str) -> Path:
    """name npr. 'ga4', 'gsc', 'gmb' -> credentials/{name}-service-account.json"""
    path = credentials_dir() / f"{name}-service-account.json"
    if not path.exists():
        _fail(
            f"Nedostaje service account fajl na {path}. "
            "Videti reference/api-konektor-setup.md — kreiraj/kopiraj service account "
            "JSON ključ za ovaj servis na tačno ovu putanju."
        )
    return path


def get_ga4_credentials():
    from google.oauth2 import service_account

    scopes = ["https://www.googleapis.com/auth/analytics.readonly"]
    return service_account.Credentials.from_service_account_file(str(service_account_path("ga4")), scopes=scopes)


def get_gsc_service():
    from google.oauth2 import service_account
    from googleapiclient.discovery import build

    scopes = ["https://www.googleapis.com/auth/webmasters.readonly"]
    creds = service_account.Credentials.from_service_account_file(str(service_account_path("gsc")), scopes=scopes)
    return build("searchconsole", "v1", credentials=creds)


def get_gmb_service_account_credentials(scopes: list[str]):
    """GMB — pokusaj prvo preko service account-a (ako je dodat kao manager na Business Profile nalogu)."""
    from google.oauth2 import service_account

    path = credentials_dir() / "gmb-service-account.json"
    if not path.exists():
        return None
    return service_account.Credentials.from_service_account_file(str(path), scopes=scopes)


def oauth_client_path() -> Path:
    path = credentials_dir() / "oauth-client.json"
    if not path.exists():
        _fail(
            f"Nedostaje OAuth client fajl na {path}. "
            "Videti reference/api-konektor-setup.md korak 3 — kreiraj OAuth 2.0 Client ID "
            "(Desktop app) u istom Google Cloud projektu, preuzmi JSON, sačuvaj na ovu putanju."
        )
    return path


def token_path() -> Path:
    return credentials_dir() / "token.json"


def get_oauth_credentials(scopes: list[str]):
    """Vraća OAuth Credentials za Ads/GMB. Traži da je authorize_oauth.py već pokrenut."""
    from google.auth.transport.requests import Request
    from google.oauth2.credentials import Credentials

    tpath = token_path()
    if not tpath.exists():
        _fail(
            f"Nedostaje token.json na {tpath}. "
            "Pokreni prvo: python authorize_oauth.py — jednokratna autorizacija u browseru "
            "(videti reference/api-konektor-setup.md korak 3)."
        )

    creds = Credentials.from_authorized_user_file(str(tpath), scopes)
    if creds.expired and creds.refresh_token:
        creds.refresh(Request())
        tpath.write_text(creds.to_json(), encoding="utf-8")
    return creds


def friendly_api_error(exc: Exception) -> None:
    """Pretvara sirov Google API HttpError u citljivu GRESKA: poruku (npr. 'API nije ukljucen',
    sa direktnim linkom za aktivaciju ako Google ga vrati) umesto sirovog traceback-a."""
    try:
        from googleapiclient.errors import HttpError

        if isinstance(exc, HttpError):
            try:
                detail = json.loads(exc.content.decode("utf-8"))
                message = detail.get("error", {}).get("message", str(exc))
            except Exception:
                message = str(exc)
            _fail(f"Google API greska ({exc.resp.status}): {message}")
    except ImportError:
        pass
    _fail(f"{type(exc).__name__}: {exc}")


def read_json(path: Path) -> dict:
    with open(path, encoding="utf-8") as f:
        return json.load(f)


def ads_config_path() -> Path:
    path = credentials_dir() / "ads-config.json"
    if not path.exists():
        _fail(
            f"Nedostaje ads-config.json na {path}. "
            'Sadrzaj: {"developer_token": "...", "login_customer_id": "..." (opciono, samo ako '
            "koristis MCC)}. Videti reference/api-konektor-setup.md korak 4."
        )
    return path


def get_ads_client(customer_id: str = "1568860314"):
    """Vraca (GoogleAdsClient, customer_id) - koristi OAuth token + developer token iz ads-config.json."""
    from google.ads.googleads.client import GoogleAdsClient

    oauth_client = read_json(oauth_client_path())
    installed = oauth_client.get("installed") or oauth_client.get("web") or {}
    ads_config = read_json(ads_config_path())
    creds = get_oauth_credentials(["https://www.googleapis.com/auth/adwords"])

    config = {
        "developer_token": ads_config["developer_token"],
        "client_id": installed["client_id"],
        "client_secret": installed["client_secret"],
        "refresh_token": creds.refresh_token,
        "use_proto_plus": True,
    }
    if ads_config.get("login_customer_id"):
        config["login_customer_id"] = str(ads_config["login_customer_id"]).replace("-", "")

    client = GoogleAdsClient.load_from_dict(config)
    return client, customer_id
