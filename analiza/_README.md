---
tip: reference
azurirano: 2026-07-04
---

# analiza/ — Dnevnik stanja (Ads · GA4 · GSC · GMB)

Periodični puni snapshot svih marketing podataka preko Windsor.ai. Svaki snapshot = jedan fajl `YYYY-MM-DD-snapshot-full.md`, istorija se gradi poređenjem snapshot-ova.

## Sistem

- **Kadenca:** mesečno (početkom meseca za prethodni), ili vanredno pre velikih odluka
- **Kako se pravi novi:** kopiraj [[analiza/_TEMPLATE-snapshot]] → Claude Code povuče podatke kroz Windsor.ai (konektori: `google_ads` 156-886-0314, `googleanalytics4` 292720335, `searchconsole` sc-domain:antasline.com, `google_my_business`)
- **Prozori:** 16mo trend (GSC tvrdi limit) · 90d breakdowns · 28d vs prethodnih 28d
- **Posle pisanja:** red u [[DNEVNIK-NAPRETKA]] + link u [[00-INDEX]]

## Pravila podataka (iz [[CLAUDE]] §10)

- Prava konverzija = **hvala-proxy** (`page_path contains "hvala"`) — serija postoji tek od juna 2026
- GA4 `conversions` metrika je slomljena od juna 2026 (key event bug) — NE koristiti dok se ne popravi
- generate_lead pre 27.06.2026 = stara definicija (pregled /kontakt) — neuporedivo
- Ads uvoz konverzija radi tek od juna 2026 — istorijske nule ≠ nula lidova
- GA4 `in` filter nepouzdan → evente vući nefiltrirano pa agregirati
- Prazan rezultat = "Nema podataka za [izvor]" — nikad izmišljati brojeve
- Sve Ads vrednosti u RSD

## Snapshot-ovi

- [[analiza/2026-07-04-snapshot-full]] — **BASELINE** (prvi puni; jun 2026 = mesec-nula za konverzije)

## Dubinske analize

- [[analiza/2026-07-04-gsc-kw-analiza-16m]] — svih 2.893 GSC upita klasterizovano (tema × intent) + akcioni plan 10 stavki
- [[analiza/2026-07-04-ads-st-analiza-16m]] — svih 1.899 Ads search termina (isti klasteri) + poređenje GSC↔Ads; curenje 16.607 RSD (15,4%)
