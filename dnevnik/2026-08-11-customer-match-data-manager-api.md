---
tip: dnevnik-sesija
alat: claude-code
datum: 2026-08-11
blok: B
status: ceka
---

# Sesija — Customer Match upload uživo (blokiran, Data Manager API)

## Šta je urađeno
- Potvrđen i push-ovan commit prethodne sesije (`4067cd2`, `categorize_leads.py` + `customer_match_upload.py --split-by-category`) — nije bio pushovan zbog prekida veze.
- Pokrenuta prava `[cpanel-live]` sesija na `wp1.oblak.host`. `scan_leads.py --dry-run`: 0 novih kontakata, `leads.csv` na serveru već ima 9 (5 `nepoznato` · 3 `sportski-tereni` · 1 `terase-spoljne-podloge`), svi `uploaded=False`.
- `customer_match_upload.py --split-by-category` dry-run: potvrdio podelu (5/1/3 po listi), 0 API poziva.
- Prvi `--confirm` pokušaj: `invalid_grant` (token mrtav, osvežen 07.08 → pao 11.08). M re-autorizovao lokalno preko `authorize_oauth.py`, naišao na ekran „App not verified" (OAuth consent screen u statusu *Testing*, sensitive `adwords` scope) — rešeno preko „Advanced → Go to mcp-za-claude (unsafe)". Novi `token.json` prekopiran na server.
- Drugi `--confirm` pokušaj: auth prošao, ali `CUSTOMER_NOT_ALLOWLISTED_FOR_THIS_FEATURE` — Google eksplicitno traži Data Manager API za ovaj developer token.
- Nusefekat na živom Ads nalogu: prazna user lista `AntasLine - Website Leads` (`userLists/9444454571`) kreirana pre pada poziva za dodavanje članova. M ručno obrisao u Ads UI-ju (Audience manager → Segments) — potvrđeno.
- Istraženo (WebSearch + WebFetch nad developers.google.com i migration guide-om): Data Manager API je od dec. 2025 GA, i od 01.04.2026 developer tokeni koji nikad ranije nisu slali Customer Match zahtev preko starog `OfflineUserDataJobService`-a su blokirani bez obzira na tier (Basic/Standard) — moraju na Data Manager API. Ovo koriguje raniju pretpostavku iz plana (4.9, 2026-08-07: „treba Standard access").
- `reference/api-konektor-setup.md` dobio Korak F (app verification radni obilazak) i Korak G (Data Manager API setup plan — API enable, mogući nov OAuth scope, `pip install google-ads-datamanager`, prepis upload dela koda).
- Kod nije menjan (`customer_match_upload.py` i dalje koristi stari `OfflineUserDataJobService`) — čeka odluku da li se ide na migraciju.

## Otvorene akcije
- [ ] Uključiti `datamanager.googleapis.com` API u GCP projektu `mcp-za-claude` #ceka-miroslav
- [ ] Proveriti da li je potreban nov OAuth scope za Data Manager API i ponoviti `authorize_oauth.py` ako da #ceka-miroslav
- [ ] `pip install google-ads-datamanager` lokalno i na serveru (`~/antasline-connector`) #claude-code (posle M odluke da se ide u tom pravcu)
- [ ] Prepisati upload deo `customer_match_upload.py` na `IngestionServiceClient.ingest_audience_members()` #claude-code
- [ ] Odluka: da li i dalje ima smisla tražiti Standard developer token access u Ads API Center (možda nepotrebno za ovaj konkretan blok) #ceka-miroslav

## Beleške / odluke
- `leads.csv` na serveru netaknut celu sesiju — nijedan od 9 kontakata nije uspešno upload-ovan, svi `uploaded=False`.
- Nema izmena na WP buildu ni u bazi ove sesije — samo API pokušaji i istraživanje.
- Formalni error kod (`CUSTOMER_NOT_ALLOWLISTED_FOR_THIS_FEATURE`) je identičan onom iz 2026-08-07, ali je uzrok sad bolje razumljen — nije (nužno) developer token tier, nego obavezna migracija na Data Manager API za integracije koje ga nikad ranije nisu koristile.

## Veze
- [[reference/api-konektor-setup.md]] — Korak F (app verification) + Korak G (Data Manager API setup)
- [[reference/naucene-lekcije]] — nova lekcija o Data Manager API
- [[2026-07-06-MASTER-PLAN-V2]] 4.9 — ažurirano
- [[PROGRESS]] — Blokeri
- [[DNEVNIK-NAPRETKA]] — glavni unos
