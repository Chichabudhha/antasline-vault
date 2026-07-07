---
tip: indeks
naziv: Promptovi za parity faze F1–F7
datum: 2026-07-07
---

# 📋 Promptovi F1–F7 — kako se koriste

Svaki fajl u ovom folderu je **samostalan prompt za jednu radnu sesiju**.
Strategija i odluke: [[migracija/PARITY-PLAN]].

## Kako pokrenuti fazu

U novoj Claude Code sesiji (u vault-u) reci:
```
Izvrši migracija/promptovi/F1-parity-inventar.md
```
ili samo `nastavi parity F1`. Model čita prompt fajl i izvršava ga od početka
do kraja, uključujući protokol zatvaranja. **Jedna faza = jedna sesija.**

## Redosled i status

- [x] **F1** — [[F1-parity-inventar]] — master inventar live vs lokal (bez izmena baze) ✅ 2026-07-07 — `migracija/parity-inventar.csv` (175 redova)
- [x] **F2** — [[F2-permalink-fix]] — Woo permalinci + blog slug + proizvod slugovi ⚠️ menja bazu ✅ 2026-07-07 — backup 47MB, hard flush + Yoast indexable keš gotcha nađen
- [x] **F3** — [[F3-posts-reimport]] — pun reimport 30 postova ⚠️ briše + menja bazu ✅ 2026-07-07 — 30 publish (2/30 namerno preskočena kao pravi duplikati), ID-evi 2542/2298 sačuvani
- [x] **F4** — [[F4-redirect-mapa]] — minimalna redirect mapa 🔴 čeka Miroslavljeve odluke o graničnim slugovima ✅ 2026-07-07 — 7 redova, bergo-konsolidacija pretpostavka ispravljena (M: i dalje u ponudi, F5 rebuild)
- [x] **F5** — [[F5-trijaza-stranica]] — trijaža nedostajućih stranica → W1 red čekanja ✅ 2026-07-07 — `migracija/w1-red-cekanja.md` (33 stranice A, politika-kolacica gotova)
- [x] **F6** — [[F6-namena-arhitektura]] — `namena` tag + auto grid arhitektura ⚠️ menja bazu ✅ 2026-07-07 — 8 `namena-*` termina na svih 37 proizvoda, `woodmart_products taxonomies="ID"` grid mehanika potvrđena, pilot `/sportske-podloge/kosarkaske-konstrukcije/` (ID 16657) = spojen sa W1 SEO prioritetom #1
- [x] **F7** — [[F7-content-standard]] — content standard: definicije (ikonice, skica stil, skill update); obogaćivanje proizvoda ide iterativno posle ✅ 2026-07-07 — skill `obogati-proizvod` dopunjen (standardi-sa-linkovima + namena), 12 novih SVG ikonica, video lite-embed obrazac (global JS, ne vc_raw_html), antas-skica stil definisan + pilot skica; sve testirano na `/antistatik-i-elektroprovodljivi-podovi/`. Lighthouse pre/posle odloženo na W3 3.5 (CLI nije dostupan lokalno)

Zavisnosti: F3 čeka F1 (LOKAL-NOVO statusi) · F4 čeka F1–F3 · F6 čeka F2 · F7 čeka F6.
F5 može odmah posle F1, paralelno sa F2/F3.

## Pravila za model koji izvršava (ne preskakati)

1. Pročitaj CEO prompt fajl pre prvog koraka.
2. Faze označene ⚠️ počinju backup-om baze — bez izuzetka.
3. Live sajt (antasline.com) se NE dira ni u jednoj fazi.
4. Na kraju sesije: štikliraj fazu OVDE, upiši unos u [[DNEVNIK-NAPRETKA]] (na vrh),
   ažuriraj [[PROGRESS]], i ako prompt kaže — ažuriraj [[migracija/PARITY-PLAN]].
5. Ako nešto ne može da se završi — dokumentuj tačno gde je stalo u dnevniku
   i označi fazu sa ⏳ umesto štikliranja.
