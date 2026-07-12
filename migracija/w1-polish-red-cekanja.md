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
| 1 | **PREDUSLOV: pomirenje atribut seta** — konačan set: 18 taksonomija (8 filter + 10 spec-only, 2 nove: dimenzije-ploce, nosivost), `color` obrisan, 251 import-smeće dodela očišćeno, odluka upisana u skill | — | ✅ 2026-07-09 |
| 2 | Ecotile ploče | 16538, 16542, 16540 | ✅ 2026-07-09 (svih 8 tačaka + povratni linkovi 16660/16658; #ceka-M: PDF-ovi, debljina 7 vs 7,6 mm) |
| 3 | Koševi / košarkaške konstrukcije | 16544, 16546, 16548, 16532, 16536 | ✅ 2026-07-10 (pun standard; MicroShot bez svojih slika → #ceka-M AI slika) |
| 4 | Mosolut + Bergo | 16530, 16534 | ✅ 2026-07-10 — Bergo Unique (pun rebuild + variable 4 boje) i Mosolut Heavy (zvanični TDS 123: 23 mm, ne 32 — #ceka-M potvrda modela). Bonus: +10 novih Bergo proizvoda (M zahtev, bergoflooring spec, 2 nove kategorije) |
| 5 | DuraStripe trake | 16518–16524 | ✅ 2026-07-10 (4 rolne, pun standard; debljine Xtreme/Supreme V izostavljene — konflikt izvora, #ceka-M datasheet; PDF-ovi ne postoje u uploads) |
| 6 | Ergomat odbojnici (21 kom, ne 19!) | 16476–16516 | ✅ 2026-07-10 — svih 21 u jednoj sesiji; AI-smeće u 3 opisa očišćeno. **Follow-up sesija (M zahtevi) ✅**: 21 zvanična slika sa ergomat.com u galerijama, 4 PDF-a ("Tehnička dokumentacija"), zvanični profili u cm, EP preimenovani u 25×122 / 10×122 cm. Ostaje #ceka-M: 16476≈16484 mogući duplikat, ECB120 verovatno diskontinuiran |
| 7 | Senzori / signalni sistemi | 16526, 16528 | ✅ 2026-07-10 — pun standard; ergomat.com GetDetails #449 (AwareSigns dvostrani combo) i #1659 (AwarePass IQ PIR) + 2 spec PDF-a ("Tehnička dokumentacija"); nepotvrđene tvrdnje iz starih opisa izbačene (300 m RF za IQ, IQMat/Lidar uparivanje); potvrđeno: direkcioni PIR, 90 dB alarm, baterija ~2 g, magnet montaža. **FAZA 1 KOMPLETNA 47/47** |
| 8 | **FILTERI na shopu** — layered-nav widgeti u `filters-area`/`sidebar-shop` (registrovani, sidebari prazni). TEK kad većina proizvoda ima atribute | — | ✅ 2026-07-10 — 8 WoodMart layered-nav widgeta (filter-set) u `filters-area`, off-canvas "Filters" na `/katalog/`; auto Sort by/Price widgeti ugašeni (`hide_sort_by`/`hide_price_filter`); WC attribute lookup regenerisan (113→413). ⚠️ Kategorije (Layout Builder) nemaju Filters dugme — layouti bez toolbar elementa, po potrebi dodati kasnije |
| 9 | Related products (WoodMart linked/auto po kategoriji) — drži B2B posetioce u katalogu | — | ✅ 2026-07-11 — provera pokazala da je već aktivno (WoodMart `related_products=1`, slider, 4 kolone, 8 kom) i radi ispravno; testirano na Ecotile E500/7 i Bergo Unique, relevantne preporuke (Bergo→Bergo preko `product_tag`), svi linkovi 200. Nikakva izmena nije bila potrebna. |

## 🅱️ FAZA 2 — postovi (30 reimportovanih, restyle)

Page title šlajfna je rešena globalno (Faza 0) — ostaje per-post markup:
stari Porto/plain stil, poznati slučajevi višestrukih `<h1>`, slike,
tipografija, interni linkovi. Redosled po GSC saobraćaju:

| # | Post | Napomena | Status |
|---|---|---|---|
| 1 | Conquest 2542 (epoksidni-podovi-ili-ecotile) | poklapa se sa W2 2.9 refresh (poz. 26 "epoksi podovi") — spojiti u jednu sesiju | ✅ 2026-07-10 — GEO intro, goli FAQ JSON→script tag, al-table, brend CTA box, live-domen linkovi→lokal, link na /industrijski-podovi-cena/, 4× _thumbnail_id dedupe, sidebar-opener sakriven na postovima. ⚠️ obrazac za ostale: goli JSON-LD + dupli postmeta + kategorija |
| 2 | Basket 2298 (kako-napraviti-teren) | najveći organski post | ✅ 2026-07-11 — 8×H1→1, lažna Review schema UKLONJENA (fabricated, ne script-tag), 19 meta dedupe, GEO+CTA+FAQ/JSON-LD. Bonus: post_author=0 fix na svih 28 postova (byline 404) |
| 3 | `/politika-kolacica/` (16656) | poznat 7×`<h1>` slučaj | ✅ 2026-07-11 — 7×H1→1; 🔴 nalaz gori: ceo AI chat odgovor bio javan (uvod "primer politike", eu.anta.com citati sa utm_source=chatgpt, "Preporuka — pošto si ranije pomenuo..." sekcija) → uklonjen; Yoast title/desc dodati. **Nov pod-obrazac za batch: proveravati AI-chat ostatke** |
| 4 | Batch top-5: podloga-za-teniske-terene (2699, 299kl), teren-za-basket-3x3 (5170, 169kl), podovi-za-radionice (5637, 154kl), podne-ploce-za-kontejnere (5181, 128kl), sta-postaviti...-2 (6588, 202kl) | GSC lista izvučena (Windsor.ai searchconsole last_6m) | ✅ 2026-07-11 — 🔴🔴 **6588 imao prazan post_content** (ZionBuilder zn_page_builder_els, ne post_content — sadržaj doslovno obnovljen iz XML-a); 2699 3 slomljene slike (-1 sufiks gotcha); 5170 &nbsp; otpad + GEO intro; 5637 bare JSON-LD + **izmišljena aggregateRating uklonjena**; 5181 goli URL→anchor. Svi dedupe-ovani. **Nov pod-obrazac: proveriti CHAR_LENGTH(post_content)=0 na svakom postu pre bilo čega** |
| 5 | Batch: koji-pod-postaviti-u-garazu (16609, 94kl), podovi-za-detailing-radionice-i-servise (16615, 80kl), sta-postaviti...-plocica (16613, 76kl), pop-tenis (16611, 75kl), podloge-za-krovove-i-terase (5276, 68kl) | nastavak GSC liste | ✅ 2026-07-11 — 🟢 prvi batch bez praznog/AI-chat/bare-JSON nalaza; 14 live-domen linkova, 6 slomljenih slika (-1 sufiks), dupli 074, &nbsp; otpad — sve fiksirano |
| 6 | Batch: pvc-podne-ploce-ili-gumeni-podovi (2641, 126kl), podloga-za-odbojkaske-terene (4318, 62kl), ftalati-stetnost-i-uticaj-na-ljudsko-zdravlje (16612, 41kl), montazni-podovi...privremeni-podovi (3398, 39kl), modularni-industrijski-podovi (5411, 27kl) | nastavak GSC liste (Windsor.ai searchconsole last_6m, trenutni klik-brojevi) | ✅ 2026-07-12 — dedupe postmeta na svih 5 (8–15 duplih ključeva po postu); live-domen linkovi→lokal (10+ href-ova); 3 slomljena slike u 16612 (poznat "-1 sufiks" gotcha: `lab-g543ad3bb2_1280-1024x678`→`-1-1024x678`, isto za music/reciklaza); 2641 imao 2×`<h1>` u sadržaju (uz temu title = 3×H1 ukupno)→oba na H2; 5411 (Modularni podovi) prvi nalaz "tihog" AI-artefakta bez vidljivog teksta — ChatGPT/markdown-export `data-start`/`data-end`/`data-section-id` atributi na desetinama tagova (ne renderuju se ali su smeće u markup-u) → stripovano regex-om; isti post imao dupli broj telefona (072 **i** stari 074 u istom `<strong>`) → 074 uklonjen; 16612 imao 2× malformed `https:/onlinelibrary...` (fali slovo) → ispravljeno na `https://`. Backup: `antasline_local_2026-07-12_0844_pre-w1-polish-faza2-batch6.sql`. Svih 5 verifikovano 200/vizuelno kroz Chrome/0 JS grešaka/regresija čista (home, industrijski-podovi, planer-terena i dalje 200). Detalji: [[DNEVNIK-NAPRETKA]] |
| 7 | Batch: zasto-vam-je-potreban-esd-pod (3318, 20kl), sportska-igralista (16614, 18kl), bergo-ultimate-i-ultimate-plus (4813, 17kl), zamena-parketa-u-sportskim-salama (16610, 17kl), izbor-industrijskog-poda-tri-najcesca-pitanja (2622, 16kl) | klik-brojevi sveže povučeni preko Windsor.ai (nije postojala sačuvana lista za preostalih 12) | ✅ 2026-07-12 — dedupe postmeta (10–16 duplih ključeva/post); 4 Yoast title dodata + 1 ispravljen placeholder bug (4813); 2×H1 na 3318 (skriven non-breaking space) i 2622 → oba na H2; 3 slomljene slike ("-1 sufiks" gotcha); 6 live-domen linkova→lokal; 1 mrtav link ispravljen (4813). Prvi čist batch — bez ijednog AI-otpad/JSON-LD/074 nalaza. Backup: `antasline_local_2026-07-12_0904_pre-w1-polish-faza2-batch7.sql`. Detalji: [[DNEVNIK-NAPRETKA]] |
| 8 | Batch: ugradnje-industrijskog-poda (3257, 5kl), izbor-industrijskog-poda-tri-najcesca-pitanja-2 (3274, 1kl), podovi-za-stamparije (3388, 0kl), instalacija-i-ugradnja-esd-poda (5163, 8kl), prednosti-r-tile-design (6824, 1kl), esd-podovi-prica-kupca (6874, 0kl), osteceni-industrijski-pod (16608, 7kl) | poslednji batch — zatvara ceo red čekanja | ✅ 2026-07-12 — dedupe postmeta (12–17 ključeva/post na 6/7); 6 Yoast title dodata; 4×H1→H2 (6874 imao dva); nov AI-artefakt (`data-start`/`data-end` na `<br>`, 6824); 2 gola URL linka→anchor; 1 pokidana reč ispravljena; prva trajno nedostajuća slika u nizu uklonjena (ne "-1 sufiks" slučaj); 7 live-domen linkova→lokal. **FAZA 2 U POTPUNOSTI ZATVORENA** (29/30, `teren-za-pickleball` namerno preskočen). Detalji: [[DNEVNIK-NAPRETKA]] |

**Faza 2 (postovi) — ZATVORENA 2026-07-12.** Svih 8 batch-eva (#1–#8) urađeno, 29/30 reimportovanih
postova restilizovano. Jedini preostali: `teren-za-pickleball` (16616) — namerno preskočen zbog
fake-review Product schema blokatora, čeka Miroslavljevu odluku (videti Blokeri sekciju u [[PROGRESS]]).

## Pravila (ista kao w1-red-cekanja)

- Pre sesije: [[migracija/woodmart-sabloni]] (gotcha-i, uklj. novi F7.9 CF7 odeljak)
- Backup baze pre svake sesije · upis kroz `$wpdb->update` (gotcha #9)
- Ništa se ne izmišlja: spec samo iz postojećeg teksta/datasheet-a, cena "na upit"
- Verifikacija: 200 · 1×H1 · JSON-LD validan · slike/linkovi 200 · Yoast u `<head>` · regresija

## Veze
[[2026-07-06-MASTER-PLAN-V2]] W1 · [[migracija/w1-red-cekanja]] (prethodni, zatvoren) · skill `/obogati-proizvod` · [[reference/cenovnik]] (M10, i dalje prazan)
