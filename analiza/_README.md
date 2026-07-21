---
tip: reference
azurirano: 2026-07-04
---

# analiza/ — Dnevnik stanja (Ads · GA4 · GSC · GMB)

Periodični puni snapshot svih marketing podataka preko Windsor.ai. Svaki snapshot = jedan fajl `YYYY-MM-DD-snapshot-full.md`, istorija se gradi poređenjem snapshot-ova.

## Sistem

- ⚠️ **Windsor.ai pretplata otkazana 2026-07-21** — [[analiza/2026-07-21-windsor-final-export]] je poslednji automatski pull. Ubuduće nema direktnog Claude Code pristupa GA4/Ads/GSC/GMB podacima — snapshot-ovi zahtevaju ručni izvoz/copy-paste iz native UI (Google Ads UI, GA4 UI, Search Console UI, GMB dashboard) pre nego što Claude Code može da ih analizira.
- **Kadenca:** mesečno (početkom meseca za prethodni), ili vanredno pre velikih odluka
- **Kako se pravi novi:** kopiraj [[analiza/_TEMPLATE-snapshot]] → ~~Claude Code povuče podatke kroz Windsor.ai~~ Miroslav izvozi podatke ručno iz native UI-a (konektori koji su ranije korišćeni: `google_ads` 156-886-0314, `googleanalytics4` 292720335, `searchconsole` sc-domain:antasline.com, `google_my_business`) → Claude Code analizira dostavljene podatke
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
- [[analiza/2026-07-21-windsor-final-export]] — **POSLEDNJI Windsor.ai izvoz** (pretplata otkazana) — pun 16mo/90d izvoz sva 4 konektora, čuvati kao arhivu jer se pull više ne može ponoviti

## Dubinske analize

- [[analiza/2026-07-04-gsc-kw-analiza-16m]] — svih 2.893 GSC upita klasterizovano (tema × intent) + akcioni plan 10 stavki
- [[analiza/2026-07-04-ads-st-analiza-16m]] — svih 1.899 Ads search termina (isti klasteri) + poređenje GSC↔Ads; curenje 16.607 RSD (15,4%)
- [[analiza/2026-07-21-serp-snapshot-pre-migracija]] — W3 3.15 baseline: top 20 GSC upita + rizik-grupa pre migracije (2026-08-31), za post-live poređenje (3.12)
