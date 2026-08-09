---
datum: 2026-08-09
tip: sesija
tag: "[claude-code]"
oblast: W4 (Ads) — 4.7 Enhanced Conversions
status: lokalni deo zatvoren · GTM+Ads deo zakazan
---

# 2026-08-09 — Enhanced Conversions, lokalni deo

## Šta je urađeno

1. **GMB API kvota — retest (quick-win).** I dalje `429 Quota exceeded`
   (`mybusinessaccountmanagement.googleapis.com`, „Requests per minute").
   Četvrti put bez promene od 07-30. Google-ova ručna revizija Basic API
   Access zahteva još traje — nema akcije na našoj strani.

2. **W4 4.7 — lokalna implementacija Enhanced Conversions-a.**
   `woodmart-child/functions.php`: `wpcf7mailsent` handler sad, pre redirecta,
   upisuje email/telefon u `sessionStorage` (`al_lead_em`, `al_lead_ph` u
   E.164, `al_lead_ts`, plus `al_am_*` za Metu). Obe CF7 forme (16593, 16737)
   pokrivene jednom implementacijom — imaju identična imena polja.
   Backup: `functions.php.bak-2026-08-09-pre-enhanced-conversions`.

3. **Spec za dan migracije** — [[migracija/2026-08-09-enhanced-conversions-4.7]]:
   2 Custom JS promenljive (TTL 10 min), User-Provided Data promenljiva,
   izmena isključivo lead konverzionog taga, brisanje mrtvih Meta Zion objekata.

## Otvorene akcije

- **#ceka-miroslav** — Google Ads UI: konverzija „Lead - forma (GTM)" →
  Settings → Enhanced conversions → uključiti, metod **Google Tag Manager**,
  prihvatiti „customer data terms". Bez toga GTM šalje a Ads ignoriše.
  Bezopasno, može bilo kad pre 31.08.
- **Dan migracije (CC)** — izvršiti GTM deo po spec-u.
- **Otvoreno pitanje (nije blokator)** — forma nema checkbox za saglasnost za
  marketinško korišćenje podataka; isti nalaz već evidentiran za Customer
  Match. EC je merenje konverzija (lakši slučaj), ali jedan checkbox bi
  pokrio oba.

## Beleške i odluke

- 🔴 **Meta ključevi se ne mogu deliti.** Prva ideja je bila da EC iskoristi
  postojeće `al_am_em`/`al_am_ph`. Izmereno uživo da ne radi: GTM tag
  `Meta Pixel - Base Code` okida na All Pages i posle čitanja ih **briše**
  (`removeItem`) — posle test-submita na `/hvala-za-poruku/` ostali su samo
  `al_lead_*`. Deljenje bi značilo tiho slanje konverzija bez EC podataka.
  Odvojen prostor imena je neophodan, ne stvar ukusa.
- **Ne heširati unapred.** Ads tag sam radi SHA-256; unapred heširana
  vrednost bi bila dvostruko heširana i ne bi se poklopila ni sa čim.
- **GTM se namerno ne dira sada.** Live koristi Zion formu koja ne piše ove
  ključeve — tagovi bi bili prazan hod, neproveren pokretni deo u živom
  kontejneru bez ijedne koristi do 31.08.
- **EC samo na lead tagu.** Tag za klik na telefon (`QQCBCNDQ_sUcEKCi_cwD`)
  nema podatke forme — tamo EC nema izvor.
- 🟢 **Nusefekat: manje posla na dan migracije.** Pošto sajt sad sam piše
  `al_am_*`, gate stavka o Meta Pixel prepravci svodi se sa „prepiši sve
  selektore za CF7" na „obriši dva objekta".
- 🟢 **Nusefekat: bolji Meta match.** Stari tag je slao telefon bez pozivnog
  broja (`0692340072`) — Meta ga traži, pa je taj deo match-a verovatno
  oduvek propadao. Sad `381692340072`.

## Verifikacija

- Pravi submit-ovi kroz Chrome na obe forme → svih 5 ključeva tačno →
  redirect → `al_lead_*` prisutni na `/hvala-za-poruku/`
- Normalizacija telefona: 12 graničnih slučajeva, svi tačni (uklj. odbacivanje
  smeća — granica 9–15 cifara po E.164)
- Bez regresije: GA4 `generate_lead`, Ads `pagead/conversion/966742304/` +
  `ccm/conversion/`, Meta `facebook.com/tr/` — svi okinuli
- PHP lint čist · 4 ključne stranice HTTP 200 · GTM-TRDT8K9 učitan na localhost
- Dva test-lead-a ostala u lokalnom `mail-log.txt` (mu-plugin presreće slanje,
  ništa nije stvarno poslato; fajl se briše u 3.10)

## Veze
[[migracija/2026-08-09-enhanced-conversions-4.7]] · [[2026-07-06-MASTER-PLAN-V2]] W4 4.7 ·
[[PROGRESS]] · [[DNEVNIK-NAPRETKA]] · [[dnevnik/ADS-DNEVNIK]]
