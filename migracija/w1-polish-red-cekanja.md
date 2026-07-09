---
tip: red-cekanja
naziv: W1 POLISH — red čekanja za Faze 1 (proizvodi) i 2 (postovi)
datum: 2026-07-08
izvor: Miroslavljev polish paket zamerki + Explore audit builda (plan graceful-humming-shannon)
---

# 📋 W1 POLISH — red čekanja (Faza 1 proizvodi · Faza 2 postovi)

> **Faza 0 (globalni fixevi) ✅ zatvorena 2026-07-08**: page title traka, futer
> simetrija + gradient prelaz, B2B mobilni toolbar, katalog režim + "Zatražite
> ponudu" (W1 1.8), CF7 forma popravljena, shop stranica kreirana.
> Detalji: [[DNEVNIK-NAPRETKA]] unos 2026-07-08.

## 🅰️ FAZA 1 — proizvodi (37 kom, kroz `/obogati-proizvod` skill)

**Izmereno stanje (2026-07-08):** atributi dodeljeni **0/37** (17 `pa_*`
taksonomija postoji, sve prazne, dupli `color`/`boja`) · galerija **0/37** ·
Yoast title/metadesc **0/37** · post_content **0/37** strukturiran (plain
text bez ijednog HTML taga — ali spec podaci već u tekstu za ~30/37) ·
excerpt 13/37 prekratak · cene 0/37 (namerno — katalog režim).

Po proizvodu (skill šablon, 8 tačaka): strukturiran opis (intro → spec
tabela/buleti IZ POSTOJEĆEG teksta → Primena → FAQ → CTA 072) · excerpt ·
atributi (termini + dodela) · galerija 3–6 slika · Yoast · standardi sa
linkovima · namena tagovi. Batch 3–5 iste linije po sesiji.

| # | Batch (money-first redosled iz skilla) | ID-jevi | Status |
|---|---|---|---|
| 1 | **PREDUSLOV: pomirenje atribut seta** — 17 postojećih `pa_*` vs skill set; konačan set ~8 filter-korisnih (predlog: debljina, materijal, boja, montaza, sertifikacija, protivklizna-svojstva, vatrootpornost, antistatican), dupli `color` obrisati, upisati odluku u skill | — | ⬜ prva sesija |
| 2 | Ecotile ploče | 16538, 16542, 16540 | ⬜ |
| 3 | Koševi / košarkaške konstrukcije | 16544, 16546, 16548, 16532, 16536 | ⬜ |
| 4 | Mosolut + Bergo | 16530, 16534 | ⬜ (Bergo Unique: nema spec u tekstu → datasheet bergoflooring.com) |
| 5 | DuraStripe trake | 16518–16524 | ⬜ (najlakši — već ima "Karakteristike:/Primena:" labele) |
| 6 | Ergomat odbojnici (19 kom, 2-3 sesije) | 16476–16516 | ⬜ (13 prekratkih excerpt-a uglavnom ovde; deo bez spec → ergomat datasheet) |
| 7 | Senzori / signalni sistemi | 16526, 16528 | ⬜ |
| 8 | **FILTERI na shopu** — layered-nav widgeti u `filters-area`/`sidebar-shop` (registrovani, sidebari prazni). TEK kad većina proizvoda ima atribute | — | ⬜ poslednja sesija Faze 1 |
| 9 | Related products (WoodMart linked/auto po kategoriji) — drži B2B posetioce u katalogu | — | ⬜ opciono, uz #8 |

## 🅱️ FAZA 2 — postovi (30 reimportovanih, restyle)

Page title šlajfna je rešena globalno (Faza 0) — ostaje per-post markup:
stari Porto/plain stil, poznati slučajevi višestrukih `<h1>`, slike,
tipografija, interni linkovi. Redosled po GSC saobraćaju:

| # | Post | Napomena | Status |
|---|---|---|---|
| 1 | Conquest 2542 (epoksidni-podovi-ili-ecotile) | poklapa se sa W2 2.9 refresh (poz. 26 "epoksi podovi") — spojiti u jednu sesiju | ⬜ |
| 2 | Basket 2298 (kako-napraviti-teren) | najveći organski post | ⬜ |
| 3 | `/politika-kolacica/` (16656) | poznat 7×`<h1>` slučaj | ⬜ |
| 4+ | Ostalih ~27 postova batch po GSC klikovima | izvući listu iz GSC pre prve sesije | ⬜ |

## Pravila (ista kao w1-red-cekanja)

- Pre sesije: [[migracija/woodmart-sabloni]] (gotcha-i, uklj. novi F7.9 CF7 odeljak)
- Backup baze pre svake sesije · upis kroz `$wpdb->update` (gotcha #9)
- Ništa se ne izmišlja: spec samo iz postojećeg teksta/datasheet-a, cena "na upit"
- Verifikacija: 200 · 1×H1 · JSON-LD validan · slike/linkovi 200 · Yoast u `<head>` · regresija

## Veze
[[2026-07-06-MASTER-PLAN-V2]] W1 · [[migracija/w1-red-cekanja]] (prethodni, zatvoren) · skill `/obogati-proizvod` · [[reference/cenovnik]] (M10, i dalje prazan)
