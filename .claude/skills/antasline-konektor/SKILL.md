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
| `ads_report.py --from --to` | `google_ads` | `campaigns[]` (spend_rsd/clicks/impressions/ctr_pct/avg_cpc_rsd/conversions) + `totals` |
| `gmb_report.py --from --to [--location]` | GMB (Windsor pokrivenost je i onako bila ograničena) | `metrics` (impresije desktop/mobile maps/search, pozivi, klikovi na sajt, direkcije) |

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
