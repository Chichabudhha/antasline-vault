---
name: antasline-konektor
description: Sopstveni Google API konektor za GA4/GSC/Ads/GMB — zamena za Windsor.ai (istekao 2026-07-27). Koristi kad treba povuci žive podatke za izveštaj (W5 5.4, mesečni snapshot) ili kad Miroslav kaže "konektor", "Windsor je mrtav", "poveži API", "izveštaj". Podaci se čitaju direktno iz Google API-ja preko lokalnih Python skripti, bez trećih učesnika.
---

# AntasLine konektor — direktan pristup Google API-jima

Zamenjuje Windsor.ai (istekao 2026-07-27, otkazan još 2026-07-21 —
[[DNEVNIK-NAPRETKA]]). Arhitektura: skripta na zahtev, ja je pokrećem u
sesiji kad treba podatak — bez servera, bez stalnog posla.

**Kredencijali NIKAD nisu u vault-u.** Žive u
`C:\Users\Miroslav\antasline-connector\credentials\` — potpuno van git
stabla. Ako taj folder/kredencijali ne postoje, videti
`reference/api-konektor-setup.md` (Miroslavljev checklist, bez tajni).

## Kako se pokreće

```
cd C:\Users\Miroslav\antasline-connector
venv\Scripts\python.exe "C:\Projekti\antasline-vault\.claude\skills\antasline-konektor\scripts\ga4_report.py" --from 2026-07-20 --to 2026-07-26
```

Prvi put (posle `pip install -r requirements.txt` u venv-u): pokrenuti
`authorize_oauth.py` jednom pre `ads_report.py`/`gmb_report.py` (GA4/GSC
ne trebaju ovaj korak, rade preko service account-a odmah).

## Skripte i šta vraćaju

| Skripta | Zamenjuje Windsor konektor | Izlaz (kompaktan JSON) |
|---|---|---|
| `ga4_report.py --from --to` | `googleanalytics4` | `users`, `sessions`, `events.{generate_lead,tel,mailto}`, `hvala_proxy_pageviews` |
| `gsc_report.py --from --to [--limit]` | `searchconsole` | lista `opportunities` (upit/prikazi/klikovi/CTR/pozicija) za poziciju 5–15 |
| `gsc_page_queries.py --from --to --page URL [--page URL …]` | (nema Windsor pandana) | upiti po KONKRETNOJ stranici — `total_impressions`, `total_clicks`, `queries[]`. Za odluku rebuild vs 301, dijagnozu kanibalizacije, „koja stranica drži koji upit" |
| `ads_report.py --from --to` | `google_ads` | `campaigns[]` (spend_rsd/clicks/impressions/ctr_pct/avg_cpc_rsd/conversions) + `totals` |
| `gmb_report.py --from --to [--location]` | GMB (Windsor pokrivenost je i onako bila ograničena) | `metrics` (impresije desktop/mobile maps/search, pozivi, klikovi na sajt, direkcije) |
| `ai_report.py --from --to` | (nema Windsor pandana) | AI-asistent saobraćaj: `ai_sessions_total`, `ga4_channel_ai_assistant`, `podbacaj_kanala`, `po_izvoru`, `top_landing`, `eventi` |
| `gtm_mailto_tag.py [--dry-run]` | (nema Windsor pandana — **write**, ne read) | kreira `mailto` trigger + GA4 Event tag u GTM **workspace-u**; ne objavljuje |
| `scan_leads.py [--dry-run] [--maildir PATH] [--imap]` | (nema Windsor pandana — **write** na leads.csv, ne Google API) | skenira CF7 lead-mejlove sa `office@antasline.com` (Maildir na disku, ili `--imap` fallback), puni `leads.csv` (van git-a) za Customer Match. **Pokreće se SAMO na cPanel serveru** (`[cpanel-live]` sesija) — mailbox je tamo, ne lokalno. |
| `customer_match_upload.py [--confirm]` | (nema Windsor pandana — **write**, ne read) | hešuje (SHA-256) i upload-uje nove kontakte iz `leads.csv` u Google Ads Customer Match user listu preko `OfflineUserDataJobService`. **Pokreće se SAMO na cPanel serveru**, posle `scan_leads.py`. Dry-run podrazumevano. |

⚠️ **`ai_report.py` postoji zato što GA4-ov ugrađeni kanal „AI Assistant" potcenjuje
stvarni AI saobraćaj ~3×** — `medium=ai-assistant` klasifikacija je proradila tek u
junu 2026, sve pre toga je razbacano po referral/organic/(not set)/gmb. Skripta
agregira po hostname-u izvora, pa hvata sve varijante. (Mereno 2026-07-27: 98 stvarnih
sesija vs 33 u GA4 kanalu.)

⚠️ **`gtm_mailto_tag.py`, `scan_leads.py` i `customer_match_upload.py` PIŠU.**
`gtm_mailto_tag.py` traži dva jednokratna koraka koja radi Miroslav: (1)
uključiti „Tag Manager API" u istom Cloud projektu, (2) pokrenuti
`authorize_oauth.py` ponovo (scope `tagmanager.edit.containers` je dodat
2026-07-27, postojeći `token.json` ga nema). Nikad ne objavljuje sam —
Submit/Publish u GTM UI ostaje Miroslavljeva odluka.

`scan_leads.py`/`customer_match_upload.py` su drugačiji obrazac: **ne
pokreću se lokalno** (kredencijali i mailbox žive na cPanel serveru, ne na
Windows mašini) — pokreću se u `[cpanel-live]` sesiji preko SSH-a, po
`CLAUDE-CODE-instrukcija-CPANEL.md`. Zahtevaju kopiju `oauth-client.json`/
`token.json`/`ads-config.json` u `~/antasline-connector/credentials/` na
samom serveru (v. `reference/api-konektor-setup.md`). `customer_match_
upload.py` je dry-run podrazumevano, `--confirm` neophodan za stvaran
upload u Google Ads. **Poznat rizik**: kontakt forma trenutno nema consent
checkbox za marketing korišćenje email-a (samo cookie policy) — svesna
Miroslavljeva odluka od 2026-08-07 da se ipak nastavi, v.
`.claude/skills/w6-social/SKILL.md` Faza 0.

Svaka skripta traži **eksplicitne** `--from`/`--to` (YYYY-MM-DD) — nikad
presets, ista disciplina kao kod Windsor rada (izbegava dvosmislenost oko
"poslednjih 7 dana" preko vremenskih zona/dana u toku).

## Mapiranje na stare Windsor navike (šta se ne menja)

- Poređenje perioda (7d vs 7d) i dalje: pozvati skriptu DVA puta (tekući +
  prethodni period), ne postoji ugrađen "compare" mod — namerno, radi
  jednostavnosti i da ostane eksplicitno koji je period koji.
- GSC podaci kasne 2–3 dana — pomeri `--to` unazad za toliko.
- `nedeljni-izvestaj` skill sad poziva ove skripte umesto Windsor MCP
  poziva — format izveštaja (CLAUDE §10) je NEPROMENJEN.
- Pravilo "Nema podataka za [izvor]" umesto izmišljanja i dalje važi ako
  skripta vrati grešku (npr. Ads developer token još nije odobren).

## Greške koje ćeš verovatno videti dok Miroslav ne završi setup

Svaka skripta fail-fast-uje sa jasnom porukom (ne generic traceback) ako
nedostaje kredencijal — poruka kaže tačno koji fajl nedostaje i na koji
korak `reference/api-konektor-setup.md` da se vratiš. Najčešće:
- `Nedostaje service account fajl` → GA4/GSC, korak 2
- `Nedostaje OAuth client fajl` / `Nedostaje token.json` → Ads/GMB, korak 3
- `Nedostaje ads-config.json` → Ads developer token, korak 4 (čeka Google
  odobrenje, obično 1–3 radna dana — ne pokušavati ponovo instant)

## Veze
- `reference/api-konektor-setup.md` — Miroslavljev jednokratni setup checklist
- `reference/identifikatori.md` — javni ID-evi (property/account brojevi)
- `.claude/skills/nedeljni-izvestaj/SKILL.md` — koristi ove skripte za izveštaj
