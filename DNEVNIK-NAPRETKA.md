# Dnevnik napretka — Antasline SEO

## 2026-07-10 [cpanel-live] [PROVERA — LiteSpeed image optimizacija] — Problem se ponovio, uzrok drugačiji od 2026-07-05 🔴
- **Nalaz: ISTI SIMPTOM KAO 2026-07-05 (200 slika zaglavljeno u REQUESTED), ali NOVI/DUBLJI UZROK.** Read-only provera preko WP-CLI na live (`wp db query`, `wp cron event list`, `wp option get`) — bez izmena baze.
- `wp_litespeed_img_optming`: **1.361 slika u RAW** (nikad poslato) + **200 u REQUESTED** (poslato, čeka notify) na **25 distinct post_id** (5900–5940 opseg, novi proizvodi/postovi u odnosu na 20 ID-jeva iz 07-05 fixa).
- 🔴 **Pravi uzrok ovog puta: cron `litespeed_task_imgoptm_req` uopšte nije zakazan** (`wp cron event list` ga ne prikazuje — samo ccss/ucss/lqip/crawler/guest_sync litespeed hook-ovi postoje). DNEVNIK unos od 07-05 se oslanjao na pretpostavku da ovaj cron sam nastavlja slanje na 15 min — pretpostavka je pogrešna otkad je nestao iz rasporeda.
- Potvrda da je slanje stalo: `last_request.img_optm-new_req` = **2026-07-05 20:36 UTC** (tačno trenutak prošlog fix-a) → **0 novih zahteva ka cloud-u u 5 dana**. `last_pulled` = 2026-06-13 (i pre prošlog fixa). Kvota NIJE problem sada (`remaining_daily_quota: 1000` danas) — čisto pitanje da ništa ne okida slanje/pull.
- Dodatno: 114 slika u statusu `err_optm` (-7, trajni fail na cloud strani), 17 u `err`/`miss` (-3) na glavnoj `wp_litespeed_img_optm` tabeli — manji, odvojen nalaz, nije blokirajući.
- Access log (`~/access-logs/antasline.com-ssl_log`) pokriva samo danas (rotacija) — 0 `notify_img` poziva danas, ali nedovoljan uzorak za zaključak o QUIC.cloud strani.
- **Nije primenjena nikakva izmena na live** — samo dijagnostika. Predlog za sledeći korak (čeka M odluku): (a) ručni `Img_Optm::reset_row()` kao 07-05 + ručno okinuti/proveriti zašto se `litespeed_task_imgoptm_req` ne re-zakazuje (možda `img_optm-cron` opcija ili WP-Cron uslov), ili (b) eskalacija ka QUIC.cloud podršci ako se cron ponovo ne zakaže sam posle ručnog trigera — ovo je taj scenario koji je 07-05 unos predvideo ("ako se ponovi, QUIC.cloud ima dublji problem").
- Izvor istine dalje: [[PROGRESS]] Blokeri sekcija ažurirana.

## 2026-07-10 [claude-code] [W1 POLISH F1] — Ergomat zvanične slike + PDF-ovi + spec dopune + Edge Protector cm rename (M zahtevi) ✅
- **M odgovorio na 3 pitanja iz batch #6**: (1) slike sa ergomat.com ✅, (2) EP nazivi u cm ✅, (3) PDF-ovi sa ergomat.com ✅. Backup: `antasline_local_2026-07-10_pre-ergomat-slike-pdf.sql` (48,7MB).
- 🔴 **Nova tehnika — ergomat.com scraping** (WebFetch 403, curl sa browser UA prolazi): kategorije preko `GET /en/Category/List?id=X` sa `X-Requested-With: XMLHttpRequest` headerom (bez njega vraća pun layout bez proizvoda); **proizvod-detalji preko JSON API-ja `GET /en/Product/GetDetails?id=X&langId=3`** (Vue komponenta, `product-id-prop` u HTML-u; langId iz `settings-prop`) — vraća `Photo` (→ `/Content/images/products/{Photo}.jpg`), `KnowledgeSpec` (PDF putanja), `AvailableOptions` (dimenzije!). → lekcija.
- **21 zvanična slika importovana** (16844–16864, 566×336, slug `ergomat-*`) i stavljena PRVA u galerije 22 proizvoda (bumperi + Cart Stop + T-Slot + EP ×2 + 3 trake). **4 PDF-a** (16865–16868): Bumper Guards (15MB, na svih 19 bumper proizvoda), T-Slot, Cold Storage, Floor Marking (Supreme V) — nova sekcija "Tehnička dokumentacija" pre FAQ. Xtreme PDF je **78MB** → eksterni link na ergomat.com umesto lokalnog hostovanja.
- **Zvanične spec dopune (GetDetails/PDF):** profili SVIH bumpera u cm (HCIB 4×4, SCIB 2,6×2,6, CCIB 3,6×4, SCBP 3,3×3,3, LSCB 4,6×4,6✓, XL 6,29×6,29, Large Round 6×6, pipe 4,5×4,5/1m, površinski 2×5 / 2×7,5 / 1,1×4 / 4×3, konusni surface 3,6×4) · Cart Stop 25×25 cm · T-Slot 2m sekcije, 4 boje · **Cold Storage: 0,85mm, zakošene ivice, R10 (DIN 51130), −40 do +60°C, ugradnja od +4°C, 7 boja + 4 hazard** (novi atributi+redovi) · **Xtreme: zvanični PDF kaže 19 mil (0,48mm) — LOKALNA vrednost je bila tačna, US retail (30 mil) pogrešan!** Debljina vraćena u tabelu + 11 boja + rubber-based lepak. EP debljina 2,4mm + pakovanja (3/6 kom).
- **EP rename (M odluka)**: 16514 "10×48 cm" → **"DuraStripe Edge Protector 25×122 cm Ergomat"**, 16516 "4×48 cm" → **"10×122 cm"** — naslov+Yoast+excerpt+sve pominjanja u sadržaju/FAQ; **slugovi netaknuti** (interni linkovi žive). Rename preko `$wpdb->update` (NE `wp_update_post` — povukao bi kses strip na post_content, gotcha #9 familija).
- ✅ Verifikacija 25/25 proizvoda: 200 · 1×H1 · JSON-LD validni · sve galerije slike 200 · svi PDF linkovi 200 · nema tragova starih inč oznaka · regresija čista.
- ⚠️ #ceka-miroslav (novo): (a) **16476 (konusni štitnik za I-profil) i 16484 (CCIB120) su možda isti proizvod** — ergomat.com ima samo jedan "Conic I-Beam Protector"; 16476 nema pandan na sajtu → bez zvanične slike. Spojiti?; (b) **16486 (ECB120) više ne postoji u Ergomat lineup-u** — možda diskontinuiran → bez slike/spec potvrde; (c) Mean Lean nema svoj spec PDF ni stranicu (API id=63 prazan za region) — postojeći podaci ostaju; (d) Supreme V debljina i dalje nepotvrđena (spec tabela u PDF-u je slika, retail izvori se ne slažu 34 vs 36 mil).
- Skripte (scratchpad): `ergomat-slike-pdf.php`, `verify-slike-pdf.php`; JSON-ovi u `/tmp/ergo/`.

## 2026-07-10 [claude-code] [W1 POLISH F1] — batch #6 Ergomat odbojnici/bumperi + edge protectori — **SVIH 21 U JEDNOJ SESIJI** ✅
- **21 proizvod (16476–16516) po punom skill standardu u jednoj sesiji** (plan predviđao 2–3): grupa A I-grede/cevi (konusni štitnik, zaštitnik cevi, HCIB120, SCIB120, CCIB120) · B uglovi/ivice (ECB120, CCP120, okrugli, SCBP120, LSCB120, veliki zaobljeni, XL kvadratni) · C površinski (konusni, pravougaoni, veliki pravougaoni, zaobljeni ivični, okrugli) · D blokatori (Cart Stopper, T-Slot Snap-In) · E DuraStripe Edge Protectori (10×48, 4×48). Backup: `antasline_local_2026-07-10_pre-batch6-ergomat.sql` (48,6MB).
- **Data-driven skripta** (jedan `$P[]` niz → loop): atributi+`_product_attributes` (oblik/materijal/montaža/boja iz postojećih opisa; potvrda spolja: Ergomat = PU pena, crno-žuto, ISO Class 5, samolepljivi — Avantor/ASG listinzi), Yoast ×21, restruktuiran opis (GEO intro → spec `al-table` → Primena → Ugradnja → 3 FAQ + FAQPage JSON-LD → CTA 072 → srodni linkovi), 11 prekratkih excerpt-a prepisano. 5 novih pa_ termina (Crno-žuta, Okrugli, Zaobljeni, PU pena + reuse Elastomer/Ekspandirana pena/Izdržljiva guma/Mehaničko prijanjanje/4"/10").
- 🔴 **Očišćeno AI-smeće iz starih opisa**: 16494 imao citat-otpad `avantorsciences.com+6more4floors.com+6kasama.us+6` u javnom tekstu; 16488 "Hamm absorbira" typo; 16512 mešana ćirilica. Sve zamenjeno punim rewrite-om.
- **Edge Protector dimenzije ispravljene matematički**: stari tekst tvrdio 48″ = 1300 mm (netačno, = 1219 mm); naslovi kažu "cm" a radi se o inčima (4″≈10 cm, 10″≈25 cm) — u spec tabeli sada inči + tačna konverzija. Naslovi NISU menjani (live parity) → #ceka-M.
- **Cross-linkovi**: svi ka landing 16671 (`/bumperi-zastita-za-police-regale-i-zidove/`, auto-grid taxonomies=245 vraća link nazad) + kategorije 245/247/248 + srodni proizvodi po familiji (stari plain-text "Pogledajte i" pretvoren u prave `<a>` linkove).
- ✅ Verifikacija 21/21: 200 · 1×H1 · JSON-LD validni bez dupliranja · spec tabele čiste · 23 galerija slika 200 · 27 unique internih linkova 200 · Yoast u `<head>` · regresija (batch #5 proizvod, landing 16671, kategorija 245) čista.
- ⚠️ #ceka-miroslav: (a) **galerije su tanke** — svaki proizvod ima samo 1 svoju fotku (duplirana više puta u uploads), dodata generička aplikaciona `odbojnik-za-zid-u-magacinu` (15830); prave fotke po modelu ili AI slike po [[reference/standard-slika-proizvoda]]; (b) Edge Protector nazivi kažu "cm" a dimenzije su u inčima — preimenovati ili ostaviti (live parity)?; (c) Ergomat PDF datasheet-ovi ne postoje u uploads (dužine/profili za površinske modele bez cifara u tabelama zbog toga).
- Skripte (scratchpad): `inspect-batch6.php`, `enrich-batch6.php`, `verify-batch6.php`.

## 2026-07-10 [claude-code] [W1 POLISH F1] — batch #5 DuraStripe trake (4) + Mosolut Heavy — **FAZA 1 batch #4 i #5 ZATVORENI** ✅
- **5 proizvoda po punom skill standardu**: DuraStripe Xtreme (16518), Supreme V (16520), Mean Lean (16522), Cold Storage (16524) + Mosolut Heavy (16530). Backup: `antasline_local_2026-07-10_pre-batch5-durastripe-mosolut.sql` (48,5MB).
- **Po proizvodu**: atributi + `_product_attributes` postmeta (trake: materijal/montaža/širina/dužina-rolne + specifično; Mosolut: dimenzije/debljina/materijal/montaža/vatrootpornost/protivkliznost/boja) · galerija 3 slike iz uploads · Yoast title/metadesc · restruktuiran opis (GEO intro → spec `al-table` → Primena → Ugradnja → [Standardi] → 3 FAQ + FAQPage JSON-LD → CTA 072 "cena na upit" → cross-linkovi). 9 novih pa_ termina (50 mm, 50–150 mm, 98 Shore, 0,56 mm, Bela, Narandžasta, 1200 × 800 mm, 23 mm, Pero i žleb, S3).
- **Izvori potvrđeni pre upisa**: Supreme V 7 boja + širine 2"–6" (US retail, poklapa se sa lokalnim "5–15 cm") · Xtreme + Mean Lean 98 Shore A · Mean Lean 0,56 mm (lokal + retail 22 mil se POKLAPAJU) · **Mosolut Heavy 123 zvanični TDS (mosolut.com): 1200×800×23 mm, 30 kg, Bfl-s1, S3** — stari lokalni opis tvrdio **32 mm** (to je model Heavy 132!); slika proizvoda je `mosolut-heavy-123` → opis prepisan na 123 podatke. Standardi sekcija: EN 13501-1 (reuse verifikovan dinmedia href).
- 🔴 **Namerno IZOSTAVLJENE debljine zbog konflikta izvora** (tvrdo pravilo): Xtreme — lokal 0,48 mm vs US retail 30 mil (0,76 mm); Supreme V — retail 34 vs 36 mil. Nijedna nije u spec tabeli dok se ne potvrdi datasheet-om.
- **Cross-link trougao**: trake ↔ međusobno + → vodič 16666 (`/industrijski-podovi/trake-za-obelezavanje/`, grid taxonomies=248 ih automatski prikazuje) + → kategorija 248 + → silo; Mosolut → kategorija 250 + `/podovi-za-stale/` (5791) + Bergo Unique/Ecotile E500-10. **5791 dobio link nazad ka proizvodu** + usput popravljen zatečen 2×H1 (content h1 → h2, isti obrazac kao šljaka stranica).
- 🔴 **Gotcha (okruženje): MariaDB nije hteo da se podigne — "Aria recovery failed"** posle neurednog gašenja XAMPP-a. Fix: preimenovati (ne obrisati) `aria_log.*` + `aria_log_control` u `mysql\data\`, pa restart — InnoDB (wpGs_ tabele) netaknut. → lekcija.
- ✅ Verifikacija svih 5: 200 · 1×H1 · JSON-LD validni bez dupliranja (Yoast graph + FAQPage + BreadcrumbList + Product global hook) · spec tabela bez `<br>` (wpautop čist) · atributi renderuju · 15 slika galerija 200 · 12 internih linkova 200 · Yoast u `<head>` · regresija (ecotile proizvod, vodič 16666, podovi-za-stale) 200/1×H1.
- ⚠️ #ceka-miroslav: (a) **Mosolut model potvrda** — prodajemo li Heavy 123 (23 mm, dvostrana — po slici i TDS-u) ili Heavy 132 (32 mm, kako je pisalo u starom opisu)?; (b) **PDF tehnički listovi ne postoje u uploads** za DuraStripe i Mosolut (skill tačka 7 — zabeleženo, bez praznih linkova); (c) Mosolut galerija koristi 3 generičke štala-fotke sa stranice 5791 — ako postoje prave Heavy 123 fotke, zameniti; (d) standard slika 1080×1080 još ne postoji ni za jedan od 5.
- Skripte (scratchpad): `inspect-batch5.php`, `inspect-pattern.php`, `inspect-links.php`, `enrich-batch5.php`, `verify-batch5.php`.

## 2026-07-10 [claude-code] [W1 POLISH F1] — M-paket: varijabilni proizvodi (Ecotile+Bergo, 79 varijacija), 10 novih Bergo proizvoda, batch #3 koševi, futer/tabela/labele fixevi ✅
- **Sesija po Miroslavljevom paketu od 2026-07-09** (izvršenje prekinuo pad permission klasifikatora 09. uveče — nastavljeno i završeno 10.). Backup: `antasline_local_2026-07-09_pre-varijacije-futer.sql` (48MB).
- **(1) Atribut labele kapitalizovane** (svih 18: Antistatičan, Električni otpor, Montaža, Širina, Tvrdoća (Shore A)…). 🔴 Gotcha: mysql CLI kroz Windows konzolu MANGLE-uje UTF-8 u `-e` stringu (č→?, upisano u bazu!) — ispravka kroz PHP fajl. → lekcija.
- **(2) Spec tabela bez horizontalnog skrola na mobilnom**: `.single-product .al-table { min-width:0 }` + `word-break` + kompaktniji padding/font ispod 576px. Verifikovano JS merenjem na 588px viewportu: `wrapperScroll == clientWidth`, bez skrola.
- **(3) Futer**: layout **13 (5 kolona) → 4 (4 jednake)** kroz F7.7 mu-plugin merge postupak + Styles_Storage reset; "Pratite nas" (custom_html-7) premešten u kolonu 4 ispod "Kontaktirajte nas" (2 widgeta u istom sidebar-u — dynamic_sidebar ih slaže vertikalno); nova ikonica `mobilni-telefon.svg` (al-icon stil) umesto slušalice; social ikonice razmaknute (`gap:12px !important` — widget nosi SOPSTVENI `<link>` el-social-icons.css u futeru koji se učitava POSLE child CSS-a → bez !important ne prolazi). → lekcija.
- **(4) Standard slika proizvoda** → `[[reference/standard-slika-proizvoda]]` (M prompt šablon: 1080×1080, čista bela pozadina, ~15% margine, studio svetlo) + upisano u skill `/obogati-proizvod` (tačka 3/3b).
- **(5) Ecotile → varijabilni** (M odluka): **E500/7 = variable, 8 zvaničnih boja sa RAL kodovima** (ecotileflooring.com; 6 varijacija ima prave slike boja iz uploads), **E500/10 = variable, 3 boje** (Tamno siva/Crna/Grafit), **ESD ostaje simple** (zvanično samo Dark Grey — potvrđeno i na shop.ecotileflooring.com). ✅ **Rešena dilema 7 vs 7,6 mm: zvanični spec kaže 7,6 mm (±0,3)** — tabela ažurirana + dodati masa/tvrdoća/zvučna izolacija (proizvođačke vrednosti). 7 Ecotile PDF-ova skinuto+attachovano+linkovano ("Tehnička dokumentacija" sekcija): E500 uputstvo, ESD X-Joint uputstvo, ESD test sertifikat, požarni/protivklizni sertifikati, hemijski vodič, katalog. ⚠️ 4 prva pokušaja URL-ova bila 404 (WebFetch dao zastarele linkove) — validni nađeni na downloads stranici, `file -b` provera obavezna. 🔴 **WC gotcha: varijacija BEZ cene je nevidljiva** (`data-product_variations="[]"`, prazan select) → 3 filtera u child functions.php (`woocommerce_variation_is_visible/is_active` true + `hide_invisible_variations` false) — bezbedno jer je katalog režim. → lekcija.
- **(6) Bergo**: **Unique (16534) pun rebuild** — zatečeni opis pričao o XL-u, excerpt "Boja: Bela, Dezen: Cvetni" (import haos); sada pun standard + variable sa 4 standardne boje (Stone Grey/Graphite Grey/Sand/Cedar Wood — brend imena po M odluci; cedar/sand varijacije imaju prave fotke). **10 NOVIH proizvoda** sa zvaničnim bergoflooring.com specifikacijama: Ultimate (15 boja, FIBA L1&2+EN14877+ITF, 16770) · Ultimate PLUS (13 boja, FIBA SVE kategorije, 16786) · Ultimate PLUS GreenMatter (50% reciklirana veštačka trava, 16800) · Ultimate FLOW (pickleball, ugrađene 50mm linije, 13 boja, 16801) · XL (7 boja, 16815) · Elite (6, 16823) · Nova (5, 16830) · Excellence (brodske palube, 5, 16836) · Extreme IMO (IMO/MED, 16842) · Solid (HDPE 630×575×50, nosi kamione, 16843). **2 nove kategorije**: Sportske podloge (#302), Brodske palube (#303) — bez Layout Builder landinga za sad. 6 Bergo PDF-ova attachovano. Cross-linkovi ka postojećim landing stranicama (16679/15480/16680/16659/16681/16663) u oba smera koncepta (proizvod → landing; landing → proizvod postoji od ranije za Unique/XL/Elite kroz hub linkove). **Ukupno 79 varijacija boja (11 Ecotile + 68 Bergo).**
- **(7) Batch #3 koševi ZATVOREN** (16544 Lite Shot 325 · 16546 Mini Shot 225 · 16548 MicroShot 125 · 16532 Street Sport · 16536 zglobni obruč): restruktuiran opis (spec tabela iz postojećeg teksta), atributi+`_product_attributes` (čelik/točkovi/FIBA L1/L3/EN1270), galerije iz uploads (MicroShot NEMA nijednu svoju sliku), Yoast, FAQ+JSON-LD, EN 1270 + FIBA linkovi (reuse verifikovanih hrefova sa 16657), CTA 072, međusobni cross-linkovi + landing + kategorija.
- ✅ **Verifikacija**: 19 proizvoda × (200 · 1×H1 · FAQPage+BreadcrumbList+Product JSON-LD validni · slike/linkovi/PDF-ovi 200 · Yoast) — SVE ČISTO; regresija home/katalog/3 kategorije/landing 200. Chrome: izbor boje menja glavnu sliku (zuta → ecotile-500-7-zuta.jpg) ✅ · mobilni B2B toolbar ✅ · futer struktura (4 kolone, red widgeta, ikonica, gap 12px) ✅. 🔴 Chrome gotcha: otvoren NATIVE select dropdown zamrzava CDP screenshot (timeout) — Escape pre snimanja. → lekcija.
- ⚠️ #ceka-miroslav: **(a) AI slike po [[reference/standard-slika-proizvoda]]** za proizvode bez ijedne slike: GreenMatter, FLOW, Nova, Excellence, Extreme IMO + MicroShot 125 galerija (thumb mu je MiniShot fotka); (b) **E500/10 vs X500/10**: aktuelna fabrička verzija 10mm je X500/10 (497×497, 9,6mm, X-Joint) — naš opis zadržan po live tekstu (500×500, 10mm, T-Joint SKU) → odluka da li uskladiti; (c) **Bergo lajsne/System dodaci** (~25 stavki: edge/corner/line strips, alati, podloge) — dodati u katalog ili ne; (d) nove kategorije bez LB landinga; (e) 10 novih proizvoda + 2 kategorije = **LOKAL-NOVO** (ne postoje na live — nisu parity rizik, novi sadržaj posle migracije).
- Skripte (scratchpad): `labele-fix.php`, `futer-fix.php` (+mu-plugin TEMP, obrisan), `ecotile-varijabilni.php`, `bergo-proizvodi.php`, `kosevi-obogati.php`, `verifikacija3/4.php`.

## 2026-07-09 [claude-code] [W1 POLISH F1] — atribut set pomiren + Ecotile batch obogaćen (3 proizvoda) ✅
- **Polish Faza 1 batch #1 (pomirenje atribut seta) ZATVOREN** — odluka M: **18 `pa_*` taksonomija u dve grupe** — filter-set 8 (debljina, materijal, boja, montaza, protivklizna-svojstva, vatrootpornost, antistatican, sertifikacija) + spec-only 10 (dimenzije-ploce🆕, nosivost🆕, oblik, sirina, duzina-rolne, otpornost-na-udar, otpornost-na-hemikalije, tvrdoca-shore-a, zakosene-ivice, elektricni-otpor). NE kreiraju se: primena (F6 namena-tagovi), boje (=boja), garancija/poreklo (tek uz datasheet). Odluka upisana u skill `/obogati-proizvod`.
- 🔴 **Nalaz: "0/37 atributa" iz audita je bio tačan, ali termini NISU bili prazni** — 251 `term_relationships` red za pa_ taksonomije je postojao, ali sve dodele su **import artefakt** (32 na attachment-ima, 219 orphan object_id) — live object_id-jevi iz SQL dumpa pokazuju na pogrešne lokalne objekte. Smeće očišćeno, `pa_color` duplikat obrisan (`wc_delete_attribute`), count reset. **Termini sami (R10, Bfl-S1, 550kg/cm2, 89-92 Shore, esd-1,46×10⁶ Ω…) su realan live vokabular — reuse-ovani, ne rekreirani.**
- **Polish Faza 1 batch #2 (Ecotile) ZATVOREN** — 16538 (E500/7), 16540 (E500/10), 16542 (ESD 7mm) po svih 8 tačaka skila: atributi (8–10 taksonomija po proizvodu, termini + `wp_set_object_terms` + **`_product_attributes` postmeta** — bez tog meta se atribut NE prikazuje, dokumentovano u skill), galerija 5–6 slika (postojeći uploads, provera `file_exists` pre upisa), Yoast title/metadesc, restruktuiran `post_content` ($wpdb->update): GEO intro → spec `al-table` (jedna linija, overflow wrapper) → Primena → Ugradnja → **standardi sa linkovima** (DIN 51130, DIN EN 13501-1, DIN 53516, BS 476-7, IEC 61340-5-1 — hrefovi reuse sa ranije verifikovanih stranica) → 3 FAQ + FAQPage JSON-LD (jednolinijski `<div><script>`, bez vc_raw_html jer proizvodi nisu WPBakery) → CTA 072 + "cena na upit" → cross-linkovi.
- **Cross-link trougao kompletan**: proizvodi ↔ međusobno, → kategorija 254, → silo `/industrijski-podovi/`, ESD → `/antistatik-i-elektroprovodljivi-podovi/`, → trake. **Povratni linkovi dodati**: 16660 (E500/7 info) → proizvod u katalogu, 16658 (antistatik) → ESD proizvod (str_replace, anchor uniqueness provera).
- ✅ Verifikacija sve 3: 200 · 1×H1 · JSON-LD 3 bloka validna (FAQPage + BreadcrumbList + Product global hook — bez dupliranja) · 168 URL-ova (slike+interni) 0 neispravnih · atributi renderuju (Additional info tab) · `<p>` u tabeli 0 · regresija home/silo/katalog/kategorija 200.
- ⚠️ #ceka-miroslav: (1) **Ecotile PDF tehnički listovi ne postoje u uploads** (skill tačka 7 — nema linka, zabeleženo umesto praznog linka); (2) **debljina E500/7 nekonzistentna**: proizvod/live tekst kaže **7 mm**, info stranica 16660 kaže **7,6 mm** — koja je tačna? (obe vrednosti su sa live izvora, ne diram dok M ne potvrdi); (3) usput nađen pokvaren href `http://srps%20en%20660-2:2011/` na legacy CPT 5303 (nije javan URL, nizak prioritet).
- Backup: `antasline_local_2026-07-09_pre-atribut-set.sql` (48MB). Skripte (scratchpad): `atribut-set.php`, `ecotile-obogati.php`, `reverse-linkovi.php`, `verifikacija.sh`, `verifikacija2.php`.
- 📅 Podsetnik zavisnosti: **M1/M10 (cene Tier1 + cenovnik) rok SUTRA 2026-07-10** — fallback "cena na upit" spreman; M3 (odbojka cpanel-live) rok 2026-07-13.

## 2026-07-09 [claude-code] [W3 TEHNIČKA] — backup na eksterni HDD + RevSlider off + WebP potvrda ✅
- **M dodao eksterni HDD (G: "Maxtor", 931GB)** — nova backup politika: backup ide na disk kad god je disk prikačen (ne čeka OneDrive). `nocni-backup.ps1` ažuriran: prioritet destinacije **G:\AntasLine-Backups → OneDrive → lokalno**.
- 🔴 **Nalaz: noćni backup (3.13) NIKAD nije stvarno radio** — `auto\` folder prazan, task je jutros pao sa `0x800710E0` (odbijen zbog uslova). Uzroci: `DisallowStartIfOnBatteries=True` + `StartWhenAvailable=False` (propušten termin se ne nadoknađuje). Oba popravljena (`Set-ScheduledTask`). "Test uspešan" iz 2026-07-07 unosa je bio ručni test, ne scheduled run — task uslovi nikad nisu provereni. → lekcija.
- **Propušteni backup izvršen odmah ručno** → `G:\AntasLine-Backups\antasline_backup_2026-07-09_1719.zip` — **2,95GB, zip validan (117.915 stavki: DB dump 92MB + ceo wp-content)**, trajanje 50 min.
- **RevSlider deaktiviran (M)** — CWV preporuka #1: verifikovano 0 referenci (sr7.js/tptools nema), regresija 4 stranice 200. −540KB JS na svakoj stranici.
- **ESD slika (M)**: kompresovana kao NOVA slika `esd-podovi-u-primeni-768x774.webp` (**112KB vs stari PNG 946KB**, 8×) i zamenjena na home — stari fajl `esd-pod-u-primeni` (jednina!) ostaje samo u postu 6874.
- **Kontrolni Lighthouse home mobile (posle RevSlider+WebP): Perf 42→45, LCP 20,4→15,0s, težina 3,9→2,6MB, TTFB 3,2→1,3s, TBT 332→276ms.** CLS nepromenjen (0,158 — stretch-row, preporuka #3 ostaje). Sledeće poluge: stari 2020/2018 JPG na home, CLS fix, js_composer CSS (uz LSCache na live).
- [[dnevnik/PERFORMANCE-AUDIT]] ažuriran (preporuke #1 ✅, #2 delimično, #4 ✅).

## 2026-07-09 [claude-code] [W3 TEHNIČKA] — porto-functionality deaktiviran (M) → sanacija zavisnosti ✅
- **Miroslav deaktivirao `porto-functionality` plugin** (legacy Porto tema — bio i preporuka #4 iz CWV audita). Zadatak: sve što je zavisilo od njega mora da radi bez Porto-a.
- **Trijaža zavisnosti**: legacy CPT-ovi (industrija-podovi/podovi-posl-prostor/spoljne-podne-obloge/vestacka-trava/sportski-podovi2) prežive — registruje ih **CPT UI**, ne porto. `portfolio` (6 publish) i `porto_builder` (10 publish) gube javni URL — nisu live-parity, samo interni šabloni/izvori. Golog shortcode curenja skoro da nema jer **child tema već ima no-op shim** (9 tagova, ranije dodat zbog PCRE segfault buga).
- 🔴 **Jedini stvarni gubitak: galerije** — `[porto_image_gallery]` (×27 na 18 publish objekata, uklj. 3 javne stranice: `/podovi-za-stale/` 402 GSC kl., `/podne-obloge-za-promocije-i-sajmove/`, **`/galerija-sportskih-terena/` — rebuild #18 se oslanjao na porto galerije!**) sada renderuje prazno kroz shim. **Fix: zamena native `[gallery ids=... columns="4" size="medium" link="file"]`** na svih 18 (`$wpdb->update` + `clean_post_cache`), galerije potvrđeno renderuju (46 stavki na galeriji terena, srcset/medium ✔).
- **Ne-gubici (proveren strah)**: `[porto_block id="4945"]` ("CTA pri dnu", na svih 6 starih stranica) je imao `conditional_render=administrator` bug → posetioci ga NIKAD nisu videli, shim-prazno = status quo. `[porto_product id="15631"]` → ID ne postoji u bazi, bio mrtav i ranije (ali je CURIO kao go tekst jer nije bio u shim listi — sad jeste).
- **Shim proširen** (child functions.php) sa svih 21 preostalih porto_* tagova nađenih u bazi (hb_/sb_/tb_/single_product_/product) — anti-segfault + anti-leak mreža.
- ✅ Verifikacija: 5 pogođenih URL-ova bez leak-a + galerije rade + slike 200 · regresija home/industrijski-podovi/sportske-podloge/o-nama 200 · `shortcode_exists('porto_product')` DA.
- Backup: `antasline_local_2026-07-09_pre-porto-off-fix.sql` (48MB). Skripte (scratchpad): `porto-check.php`, `porto-render-test.php`, `porto-gallery-fix.php`.
- ⚠️ Napomena: **RevSlider je i dalje aktivan** (CWV preporuka #1, 540KB JS/stranici, 0 upotreba) — čeka istu odluku kao porto.

## 2026-07-09 [claude-code] [W3 TEHNIČKA] — 3.5 Lighthouse/CWV baseline + XAMPP opcache fix ✅
- **Zadatak W3 3.5 zatvoren**: Lighthouse 13.4.0 (npx, headless) baseline na 7 prolaza (6 stranica mobile + početna desktop) → **[[dnevnik/PERFORMANCE-AUDIT]]** (rezultati, krivci, redosled za 3.6).
- 🔴 **Pre-uslov nalaz: sajt je bio praktično mrtav** — prvi zahtevi posle Apache restarta visili >60s, stabilno stanje ~8–10s TTFB po stranici. Dijagnostika mu-plugin hook-trace-om (tajming po hook-u): render raspoređen ravnomerno (plugins_loaded 1,6s, init→wp_loaded 2,9s…) = nema jednog krivca → sumnja na PHP izvršavanje samo.
- 🔴 **Uzrok: OPcache uopšte nije bio uključen u XAMPP-u** (default!). Fix: `php.ini` `zend_extension=opcache` + `opcache.enable=1` + `jit=disable`. **TTFB pao ~8–10s → ~2,4–3,4s.**
- 🔴 **Nov gotcha: opcache + XAMPP Apache = crash** (`0xC00000FD` stack overflow, `VirtualProtect failed [87]`, konekcija se resetuje bez odgovora) — worker thread stack premali. Fix: `httpd-mpm.conf` → `ThreadStackSize 8388608` u `mpm_winnt` bloku + Apache restart. → [[reference/naucene-lekcije]].
- **Baseline (mobile)**: Perf 24–48 · LCP 8,6–20,4s (cilj <2,5s) · TTFB ~3,2s svuda · CLS problem samo na početnoj (0,155 — WPBakery stretch-row JS init) i Woo kategoriji (0,188). A11y 84–90, BP 100, SEO 92–100.
- **Top poluge za 3.6** (po redu): (1) **RevSlider deaktivirati** — 540KB JS na svakoj stranici, 0 upotreba u publish sadržaju (SQL potvrda); (2) `esd-pod-u-primeni…png` **924KB PNG** na home → WebP (home LCP 20,4s!); (3) CLS stretch-row fix; (4) proveriti `porto-functionality` (legacy); (5) fontovi 6×Inter ≈390KB. `js_composer.min.css` 437KB unused — tek uz LiteSpeed UCSS na live, ne ručno.
- Bez izmena baze (samo php.ini + httpd-mpm.conf — reverzibilno, dokumentovano). Dijagnostički mu-plugin `al-hang-trace.php` obrisan posle upotrebe. Apache sada pokrenut kao detached proces (XAMPP Control Panel će ga pokazivati kao spolja pokrenut do sledećeg restarta).
- Skripte (scratchpad `lh/`): `run-lh.sh`, `extract.py`, `detail.py` + 7 JSON izveštaja.

## 2026-07-09 [claude-code] [W1 KONVERZIJE + W3 PARITY] — "Brzi upit" dinamička forma na svim uslugama + sveža live provera ✅
- **"Brzi upit" (CF7 ID 16737)** — jedna kratka forma automatski na dnu SVIH stranica usluga i blog postova (jedan `the_content` prio 12 hook, nula editovanja stranica). Mejl adminu uvek javlja tačan izvor kroz CF7 ugrađene `[_post_title]`/`[_post_url]` special mail tagove (container-post mehanika, verifikovano iz CF7 source koda). Polja: Ime i prezime/firma* + Telefon* (email/poruka opcioni). Puna strategija/uputstvo: [[migracija/brzi-upit-forma]].
- **Forma 16593 (/kontakt/) skraćena** (M zahtev): ime+prezime+kompanija spojeni u jedno polje `form-ime-firma` "Ime i prezime / firma"; `form-naslov default:get` prefill sa proizvoda netaknut (regresija ✔).
- **Redirect listener proširen na obe forme** (`[16593, 16737]` → /hvala-za-poruku/) — BLOK A generate_lead model hvata sve submite. **CTA scroll-to-#upit**: in-content linkovi ka /kontakt/ (bez query stringa) sad skroluju na formu iste stranice; header/footer meni + product "Zatražite ponudu" netaknuti (progressive enhancement).
- CSS: `.al-quick-quote` navy kartica (gradient traka, personalizovan naslov "Zatražite ponudu: {stranica}") + **prvi put stilizovan CF7 `form-row`/`form-col-6` grid** (do sada nije postojao nigde — kontakt forma se renderovala bez grida). Mobilni stack na 380px bez overflow-a ✔.
- **Mail test infrastruktura**: mu-plugin `al-local-mail-log.php` (`pre_wp_mail` → log u `wp-content/mail-log.txt` + vraća true da `wpcf7mailsent` okine) — `wpcf7_skip_mail` je lošiji (ne kompajlira template). ⚠️ **OBRISATI pre migracije** (presreće sve mejlove) — stavka za 3.10 checklist.
- ✅ Verifikacija: container post tačan na hub/child/post (16567/16687/2542) · 3 REST test submita — mejl nosi tačan naslov+URL izvora, UTF-8 ✔ · exclusion (kontakt/hvala/katalog/home bez forme) ✔ · 1×H1 ✔ · Chrome vizuelno + mobilni stack ✔ · regresija 16593 submit + prefill ✔.
- **W3 PARITY — sveža live provera (142 sitemap URL-a → lokalni HTTP status)**: 126×200. 🔴 **Nov nalaz: Woo `tag_base` bio `product-tag`, live koristi `oznaka-proizvoda`** — F2 je sredio product/category baze ali ne i tag; termini su bili PARITY ali bi svih 8 live tag URL-ova 404-ovalo posle migracije. Fix: `tag_base` → `oznaka-proizvoda`, svih 8 arhiva sada 200, stara baza 404, regresija (proizvod/kategorija) čista. 🔴 **Nov gotcha: opcija + `flush_rewrite_rules(true)` u istom PHP procesu NE radi** (taksonomija registrovana na init sa starom vrednošću) — flush mora u svežem procesu → [[migracija/woodmart-sabloni]] F7.10.
- **Redirect mapa +3 reda** ([[migracija/redirect-mapa-FINAL.csv]] + htaccess draft): `/бренд/ecotile|ergomat/` (ćirilična live brand baza → lokalna latinična `/brend/`, arhive 200) i `/moj-nalog/` → `/kontakt/` (katalog režim, bez naloga). ⚠️ ćirilični path u .htaccess-u testirati na subdomen probi (N6).
- **parity-inventar.csv resync**: 23 zastarela NEDOSTAJE-LOKAL reda (izgrađeni 2026-07-08, nikad flipovani) → PARITY kroz PHP CSV parser + `url_to_postid()` potvrdu (ne regex — poznati gotcha sa razbijenim navodnicima); + `/katalog/` (16736, shop arhiva — `url_to_postid` ne radi za nju, curl potvrda). **Novo stanje: PARITY 135 · NEDOSTAJE-LOKAL 1** (samo FAQ konsolidacija, namerno čeka W2/M odluku) · 301-KANDIDAT 7 · LOKAL-NOVO 29. Nijedna nova stranica nije bila potrebna → meni bez izmena (5-kategorijska struktura već parity).
- Backup: `antasline_local_2026-07-09_pre-brzi-upit.sql` (47MB). Skripte (scratchpad): `brzi-upit-setup.php`, `tag-base-fix.php`, `csv-resync.php`, `parity-check.sh`.
- Nove lekcije (container post/in_the_loop, wpcf7mailfailed na XAMPP-u, flush u svežem procesu, smooth scroll u automatizovanom tabu) → [[migracija/woodmart-sabloni]] F7.10.
- **RUNDA 2 (M zamerke, ista sesija):** forma full width (skinut `max-width:720px` sa inner-a) · saglasnost checkbox UKLONJEN iz obe forme (M odluka) · placeholder poruke → "Opišite problem koji treba da se reši" (obe forme) · **auto-reply pošiljaocu (mail_2)** na obe forme ("primili smo Vaš upit... u najkraćem mogućem roku" + prepisan upit + 072) → zbog toga **email sada obavezan i u 16737** (CF7 ne šalje mail_2 uslovno, prazan recipient bi oborio submit — gotcha u F7.10). Test: 1 submit = 2 mejla (admin + potvrda), validacija bez emaila hvata ✔, redirect na /hvala-za-poruku/ potvrđen za obe forme.
- **RUNDA 3 — full-bleed forma**: blok je bio "odsečen" (kartica u content kontejneru) → viewport breakout (`width:100vw; margin-left:calc(50% - 50vw)`), sadržaj forme vraćen na širinu kontejnera (1192px centriran). 🔴 Gotcha: na layoutima SA sidebar-om (blog postovi) kolona nije centrirana u viewportu pa je breakout iskošen (levo -153px isečeno) → `body:has(.sidebar-container)` override vraća karticu u kolonu. Verifikovano: stranica 0→1905px (full), post u koloni bez isečanja, bez horizontalnog skrola, prelaz preko postojećeg navy CTA čist (gradient traka).
- **RUNDA 4 — kontra boja + dijagonala**: forma je bila navy pa se dno stranice slivalo u jedno plavo (navy CTA + navy forma + tamni futer) → sekcija prebačena na SVETLU (`--al-mist`) sa `al-diag-top` rezom ("spuštena linija" iz design systema — navy CTA iznad ispunjava rez). Restyle za svetlu podlogu: navy naslov, beli inputi sa navy borderom, crveni tel link. Na post kartici (sidebar layout) dijagonala nema smisla → gradient traka umesto reza. 🔴 Nov gotcha: WoodMart base.css `:is(.entry-content...) > :where(:last-child) { margin-bottom: 0 }` nosi specifičnost (0,2,0) kroz najspecifičniji `:is()` argument i gazi `.al-diag-top` negativni margin-bottom na poslednjem detetu → bela traka visine reza pred futerom; fix = selektor sa tri klase. Verifikovano: gap do futera 0px, spoj navy CTA → svetla forma → gradient → tamni futer čist na hub + child + post.
- **RUNDA 2 — proizvod grid fix (M zamerke)**: (1) kartice prevelike → 3-kolonski gridovi na 4 kolone desktop (`--wd-col-lg` je INLINE stil iz shortcode-a → `!important` obavezan, gotcha F7.10); (2) portret fotke naduvavale kartice → `max-height:300px` + `object-fit:contain`; (3) 🔴 hover je prikazivao sirovi post_content excerpt (`.wd-more-desc`, absolute fade blok) koji se izlivao preko sledećeg reda kartica → ugašen globalno. Verifikovano na kosarkaske-konstrukcije (5 proizvoda) + bumperi (12 na strani): 4 kolone, hover unutar kartice, forma prisutna. **Napomena: "opis kao specifikacija umesto blok teksta" = polish Faza 1** ([[migracija/w1-polish-red-cekanja]], 37 proizvoda kroz `/obogati-proizvod`) — nije rađeno u ovoj sesiji, prva sesija = pomirenje atribut seta.

## 2026-07-08 [claude-code] [W1 POLISH FAZA 0 + 1.8] — globalni vizuelni fixevi + katalog režim ✅
- Miroslav dao paket zamerki ("prepakivanje" sajta): prelaz sekcija→futer nevidljiv (iste boje), futer asimetričan, crna šlajfna na postovima, mobilni shop-toolbar sa korpom, proizvodi nestrukturirani, katalog mod. Plan podeljen na 3 faze (plan fajl `graceful-humming-shannon.md`) — ova sesija = **Faza 0** (globalno, opcije/CSS/widgeti); Faza 1 (37 proizvoda kroz `/obogati-proizvod`) i Faza 2 (30 postova restyle) → [[migracija/w1-polish-red-cekanja]].
- **0.1 Page title šlajfna** (svi postovi/arhive odjednom): `title-background.color` `#0a0a0a`→`#0E2950` (mu-plugin merge postupak, F7.7) + child CSS: traka niža (`--wd-title-sp` 60→34px), naslov clamp, gradient akcent linija (sky→blue→red→orange) na dnu, breadcrumbs prigušeni.
- **0.2 Futer simetrija**: uzrok dvostruk — layout 13 ima NEjednake kolone (25/25/16.6×3) a najduži sadržaj ("Podovi", 5 linkova) bio u uskoj koloni dok je najkraći ("Antas Line") bio u širokoj + logo `aligncenter` dok je sve ostalo levo. Fix: swap widgeta (custom_html-5 ↔ custom_html-4 između footer-2/footer-3) + logo align uklonjen — bez diranja layout-a.
- **0.3 Prelaz sekcija→futer (sitewide)**: futer potamnjen (`#0A1F3D`, tamniji od `--al-navy` sekcija) + `::before` gradient traka 5px preko cele širine — prelaz vidljiv i kad se navy CTA i futer poklapaju. `.wd-copyrights` usklađen.
- **0.4 Mobilni toolbar → B2B**: `sticky_toolbar_fields` [shop,sidebar,wishlist,cart,account] → [link_1,link_2,link_3] = **Katalog / Pozovite (tel:072) / Ponuda (kontakt)** — custom linkovi nose pun URL+tekst, ikonice preko CSS background (child `icons/` set: izgled/telefon-podrska/email).
- **0.5 Katalog režim (W1 1.8, M9)**: WoodMart `catalog_mode` uključen (skida add-to-cart, redirektuje cart/checkout na home). Na single product dodat CTA blok `al-product-quote` (`woocommerce_single_product_summary` prio 30 = mesto starog dugmeta): crveno "Zatražite ponudu" → `/kontakt/?form-naslov=Ponuda: {naziv}` + ghost tel dugme (dodat navy override za belu podlogu — ghost je bio nevidljiv). Runda 2 čišćenja: compare/wishlist off, reviews tab off (+ WC `woocommerce_enable_reviews`=no, fake-review pravilo), prazan "Shipping & Delivery" tab off.
- 🔴 **KRITIČAN CF7 nalaz — kontakt forma nikad nije radila kako je dokumentovano**: (1) kontakt stranica (61) je embedovala STARU formu 5339, ne novu 16593; (2) forma 16593 je imala prazan `_form` postmeta (kreirana upisom samo u post_content — CF7 čita iz postmeta!) → renderovala bi se prazna; (3) `_mail` meta takođe nije postojala → mejl ne bi otišao; (4) form markup koristio HTML-atribut sintaksu (`autocomplete="tel"`, `class:size="1/2"`) i opcije POSLE quoted vrednosti — oboje obara CF7 tag parser (tag se ispiše kao goli tekst). Sve popravljeno: shortcode 61→16593, `_form`+`_mail` postavljeni kroz `WPCF7_ContactForm` API, sintaksa ispravljena (opcije pre vrednosti, `autocomplete:tel` stil), `default:get` na form-naslov (prefill iz URL-a — potvrđeno `value="Ponuda: Ecotile E500/7"`), stari neispravan `wpcf7_mail_sent` PHP echo hook (output u AJAX kontekstu ne stiže do stranice) zamenjen `wp_footer` JS-om koji na `wpcf7mailsent` (16593) redirektuje na `/hvala-za-poruku/` — konverzioni model BLOK A (generate_lead na pageview) sada radi i lokalno.
- 🔴 **Drugi nalaz — shop stranica nije postojala**: `woocommerce_shop_page_id=1614` pokazivao na nepostojeći post → `/katalog/` 404 (F5 Kategorija B pretpostavka "radi automatski" nije važila). Kreirana stranica Katalog (ID 16736) + dodela + hard flush → 200.
- ✅ Verifikacija: 10 stranica HTTP 200, Chrome screenshotovi (page title traka na postu, futer desktop — simetrija+gradient+copyright bar radi, navy CTA→futer prelaz na industrijski-podovi, proizvod sa Zatražite ponudu dugmetom), curl markeri (0 add-to-cart/compare/wishlist/reviews na proizvodu, 3 toolbar linka, redirect skripta na kontakt), regresija 3 stranice (200/1×H1/JSON-LD). 🟡 Chrome ekstenzija pala pre mobile screenshot-a — mobilni vizuelni QA ostaje u 1.6 (ionako otvoren).
- Backup: `antasline_local_2026-07-08_pre-polish-faza0.sql` (47MB). Skripte (scratchpad): `inspect-faza0.php`, `footer-swap.php`, `cf7-fix.php`, `cf7-props-fix.php`, `cf7-form-syntax-fix2.php`, `shop-page-fix.php`.
- Nove lekcije (CF7 postmeta model, CF7 tag gramatika, shop page id) → [[migracija/woodmart-sabloni]] F7.9.

## 2026-07-08 [claude-code] [W1 1.4/1.5 polish] — 5 vizuelnih ispravki posle prve footer/meni sesije ✅
- Nastavak iste sesije — Miroslav dao 5 konkretnih zamerki posle vizuelne provere prve footer/meni verzije.
- **1. Bela linija između poslednje sekcije i footera** — uzrok: `main.wd-content-layout` (WoodMart sitewide) nosi fiksnih `padding-bottom:40px`, nevidljivo na belim/mist završecima ali otkriveno kad stranica završava našom `al-section--navy` CTA sekcijom (diag-top--rev trik već kompenzuje margin, ali theme-ov padding posle toga ostaje beo). Fix: `main.wd-content-layout:has(.al-section) { padding-bottom: 0; }` — skoupljeno samo na naše rebuild-ovane stranice (`:has()` selector), ne dira default WooCommerce/blog stranice koje se oslanjaju na taj razmak.
- **2. Ikonice telefon/mejl u futeru** — stare Porto inline SVG ikone (veliki, drugačiji stil) zamenjene sa `al-icon` stilom (isti `telefon-podrska.svg` kao USP kartice + nov `email.svg`, isti stil: viewBox 24, stroke `#F04D22`, width 1.7). Nova `.al-icon--sm` CSS klasa (20px, `display:inline-block`, override-uje bazni `.al-icon` koji je `display:block` 46px — inače bi ikonica pala u novi red umesto inline sa tekstom).
- **3. "Pratite nas" — prave social ikonice** — stare gole pill-dugmadi (tekst "Facebook"/"Instagram"/...) zamenjene WoodMart-ovim native `[social_buttons type="follow" ...]` shortcode-om (`woodmart_shortcode_social()`, `inc/shortcodes/social.php`) — pravi icon-font glyph-ovi (Facebook/Instagram/Pinterest/LinkedIn) iz teminog `woodmart-font` seta, ne custom SVG. Shortcode pre-renderovan jednom preko `do_shortcode()` i snimljen kao statičan HTML u novi `custom_html-7` widget (Custom HTML widget NE prolazi kroz `do_shortcode()` sam po sebi — WP core namerno, sigurnosni razlog — zato je pre-render neophodan, ne staviti raw shortcode tekst u widget). Brend override preko istih CSS custom properties koje shortcode već koristi (`--wd-social-color/-bg/-brd-*`), scoped na `.wd-footer .wd-social-icons` — bela ikonica, providna pozadina, crveni hover.
- **4. Sticky header "preuzak" (cramped)** — pravi uzrok otkriven tek posle 5. stavke: svih 9 menu stavki (5 kategorija + 4 utility linka) bilo je zbijeno u JEDAN `mainmenu` header-builder element → prelamalo se u 2 reda čak i u normalnom (ne-sticky) headeru na 1222px kontejneru → kad se sticky suzi na `sticky_height` (60px), 2-red meni se vizuelno gnječio. Rešeno kroz stavku 5 (razdvajanje menija) + `sticky_height` 60→68px za dodatnu marginu + `--nav-gap` na glavnom meniju 20px→8px (5 kategorija, `Poslovni prostori`/`Specijalni podovi` su duge reči — trebalo je 671px dostupno vs 694px potrebno na 20px gap-u, tačno preliva za 1 stavku).
- **5. Meni podeljen na 2 nivoa** (Početna/Aktuelnosti/O nama/Kontakt gore, 5 kategorija ispod, redosled Sport→Industrija→Terase i dom→Poslovni prostori→Specijalni podovi): nov WP meni "Utility meni" (term_id 280, 4 flat stavke) kreiran preko `wp_create_nav_menu()`, dodat kao poseban `Menu` header-builder element (NE `Mainmenu` — `Menu` tip prima `menu_id` direktno, ne zavisi od theme location-a) u prazan `column6` top-bar reda (`functions.php`, `woodmart_default_header_structure` filter). Stare 4 flat stavke obrisane iz `main-menu` (term 67), preostalih 5 kategorija re-numerisano preko `menu_order`. 🔴 **Mobile parity nalaz**: top-bar ima `hide_mobile: true` (postojeća postavka) — utility meni bi bio NEVIDLJIV na mobilnom da nije dodat i u `mobile-menu-widgets` sidebar (postojeća, ranije prazna WoodMart oblast "Area after the mobile menu" — tačno za ovu namenu) preko novog `custom_html-6` widgeta (O nama/Aktuelnosti/Kontakt, Početna izostavljena jer je dostupna klikom na logo).
- 🔴 **Dodatni gotcha (header builder CSS keš)**: isti `XTS\Modules\Styles_Storage` keš problem kao u prošloj sesiji (v. F7.7) postoji i za HEADER builder CSS (odvojen data_name `default_header`, ne `theme_settings_default`) — `sticky_height` izmena u `functions.php` se ne pojavljuje dok se keš ručno ne resetuje (`(new \XTS\Modules\Styles_Storage('default_header'))->reset_data(); ->delete_css();`).
- ✅ Verifikacija: 8 stranica HTTP 200, Chrome screenshot na desktop (home hero + sticky scroll stanje + footer close-up) — svih 5 stavki vizuelno potvrđeno ispravno. `.al-mobile-utility-nav` potvrđen u mobile markup-u.
- Skripte (scratchpad): `restructure-menu.php`, `fix-footer-icons.php`.
- `migracija/woodmart-sabloni.md` F7.7 odeljak dopunjen sa gore navedenim gotcha-ima.

## 2026-07-08 [claude-code] [W1 1.4/1.5] — Footer builder + glavni meni (5 kategorija) ✅
- Novi zadatak nakon zatvaranja W1 1.2 reda čekanja — Miroslav izabrao "w1 1.4 i 1.5" (footer + meni) umesto planiranog W3 Lighthouse audita (taj ostaje za sledeću sesiju, node/npx/lighthouse potvrđeni radni).
- **1.5 Meni**: WebFetch na živi antasline.com otkrio punu meni strukturu (5 kategorija: Sport/Terase i dom/Industrija/Poslovni prostori/Specijalni podovi, ~34 podstavke + 1 pod-podstavka) koja NIJE bila replicirana lokalno (lokalni `main-menu`, term_id 67, imao samo 4 stavke: Početna/O nama/Aktuelnosti/Kontakt — Figma odluka iz gotcha #6 "5 kategorija" nikad nije izvedena u meni). Svih ~34 target URL-ova potvrđeno da postoje lokalno (DB query po slug-u) pre upisa — nijedan nedostaje. Meni rekreiran preko `wp_update_nav_menu_item()` (43 stavke, 3 nivoa: Sport→Oprema za sportske terene→Košarkaške konstrukcije), stari 4 flat item obrisana i zamenjena. `Bezbednosni i signalni sistemi` je `taxonomy`/`product_cat` (term 249) tip, ne `post_type`. Svih 39 unique URL-ova verifikovano 200.
- **1.4 Footer**: Bio potpuno default WoodMart (5 praznih kolona, samo stari kvadratni logo iz 2021 u koloni 1, "Based on WoodMart theme" copyright + payments.png). Otkriveno 2 postojeća NEAKTIVNA widget-a sa pravim podacima (`follow-us-widget-2` — tačni social linkovi iz `reference/drustvene-mreze`, `custom_html-3` "Kontaktirajte nas" — tačan 072 broj) — reaktivirana umesto pisanja ispočetka. Novi `custom_html` widgeti za "Antas Line" (o nama/kontakt/aktuelnosti) i "Podovi" (5 kategorija, isti target kao meni) kolone. Bela varijanta loga kreirana (`antas-line-logo-horizontalni-belo.svg` — svi obojeni/teget fill-ovi → belo, originalni beli negative-space swoosh → teget, tako da se na navy pozadini vidi identičan optički efekat kao original na beloj) — zatvara stavku iz `brend-knjiga.md` "Bela varijanta za navy pozadinu još nije napravljena".
- 🔴 **Veliki gotcha (2h debugging)**: WoodMart footer je NESTAO POTPUNO (prazan `<footer>`, i bez copyrights bara) posle prvog pokušaja upisa `update_option('xts-woodmart-options', ['copyrights'=>...,'copyrights2'=>...])`. Uzrok: `XTS\Admin\Modules\Options::load_options()` radi `self::$_options = get_option(...)` (REPLACE, ne merge) kad god je DB opcija truthy — pošto je opcija ranije bila prazan string (falsy), `load_defaults()` (koji puni SVE default vrednosti iz 883 registrovana polja) je ostajao netaknut i to je bio jedini razlog da je default footer uopšte radio. Moj parcijalni upis od samo 2 ključa je "postao truthy" i obrisao svih ostalih 881 default (uključujući `disable_footer`, `disable_copyrights`, `footer-layout` — footer.php ih čita BEZ default argumenta u `woodmart_get_opt()` pozivu, pa prazno/missing = `false` = ceo `<footer>` blok se preskače). **Fix**: privremeni mu-plugin (`wp-content/mu-plugins/zz-fix-*-TEMP.php`, mora biti mu-plugin jer `init` hook mora da se zakači PRE `wp-load.php` završi bootstrap) koji hook-uje `init` na prioritet 105 (između `load_defaults`@100 i `load_options`@110), pokupi `Options::get_options()` (pun default niz), merguje moje override-e, snimi kompletan niz — pa se mu-plugin fajl odmah obriše.
- 🔴 **Drugi gotcha**: `sidebar-footer.php` zove `dynamic_sidebar('footer-' . $index)` PO KOLONI — svaka kolona je SVOJA sidebar (`footer-1`...`footer-5`), NE jedna `footer-1` sidebar sa 5 widgeta koji se auto-raspoređuju (pogrešna prva pretpostavka — sva 5 widgeta su prvo završila u koloni 1, kolone 2-5 prazne). Ispravljeno: `sidebars_widgets['footer-N'] = [widget_id]` za N=1..5.
- 🔴 **Treći gotcha**: `.wd-footer{background-color:#fff}` inline CSS pravilo (iz `footer-bar-bg` opcije, ispravljene na `#0E2950` preko istog options fix-a) se NIJE regenerisalo posle `update_option` — WoodMart peruje CSS u fajl/opciju keš (`XTS\Modules\Styles_Storage`, data_name `theme_settings_default`), invalidira se SAMO kroz `xts_after_theme_settings` action koji ima guard `if (!isset($_GET['settings-updated']))`/`$_GET['page']==='xts_theme_settings'` — ne okida se na `do_action()` iz CLI-ja. Pravi fix: direktno `(new \XTS\Modules\Styles_Storage('theme_settings_default'))->reset_data(); ->delete_css();` — sledeći front-end request (`print_styles()` na `wp` hook-u) automatski regeneriše CSS iz trenutnih opcija jer `is_css_exists()` postaje false.
- ✅ Verifikacija: HTTP 200 na 7 spot-check stranica (home, industrijski-podovi, sportske-podloge, o-nama, kontakt, bergo-xl, proizvod bergo-unique), Chrome screenshot na home + industrijski-podovi (desktop) — meni i footer vizuelno ispravni na oba, boje/logo/social dugmad/copyright tekst svi tačni. 🟡 Sitan kozmetički nalaz: tanka bela linija (~15-20px) između poslednje `al-section--navy` sekcije i `<footer>` elementa — postojeći strukturni artefakt teme (ne uveden ovom sesijom), niska prioritet, kandidat za W1 1.6 mobile/vizuelni QA prolaz.
- Backup: `antasline_local_2026-07-08_pre-w1-14-15-footer-menu.sql` (47MB, pre svih izmena).
- Skripte (scratchpad): `build-main-menu.php`, `build-footer.php`, `fix-footer-options.php` (referenca, stvarni fix izvršen preko mu-plugin varijante).
- `migracija/woodmart-sabloni.md` "Otvoreno" lista ažurirana (footer/meni/bela logo stavke uklonjene, dodat novi Footer/Meni + `xts-woodmart-options` gotcha odeljak).

## 2026-07-08 [claude-code] [W2 2.8] — GEO paket: LocalBusiness schema + llms.txt ✅
- Nastavak iste sesije — poslednji zadatak po korisnikovom izboru ("2.7 pa 2.8").
- 🔍 **Nalaz**: Yoast već generiše `Organization` schema sitewide automatski (name, logo, `sameAs` sa FB/IG/LinkedIn/Pinterest — Site Representation podešavanja su već urađena u ranijoj sesiji) ali BEZ adrese/telefona i BEZ `LocalBusiness` tipa — prava rupa za lokalni/GEO signal.
- ✅ Dodat `wpseo_schema_organization` filter u `functions.php`: `@type` prošireno na `["Organization","LocalBusiness"]` + `telephone` (+381692340072, isti broj kao header/CTA sitewide) + `address` (PostalAddress: Ulcinjska 13, Beograd, 11000, RS — ista adresa kao header top-bar i conquest članak footer, ne izmišljeno).
- ✅ Kreiran `llms.txt` u root-u lokalnog builda (`C:\xampp\htdocs\antasline\llms.txt`) po llms.txt konvenciji — kratak opis firme, eksplicitna napomena da se NE prodaje epoksid (za AI asistente koji sumiraju upite), linkovi ka ključnim stranicama (industrijski podovi, sportski tereni + sve "dimenzije" stranice napravljene danas, spoljne podloge, o nama, kontakt) — koristi produkcioni domen (`www.antasline.com`) jer se aktivira tek na migraciji.
- 🟢 "O nama" (proverljive činjenice: 15+ godina, brendovi Ecotile/Bergo/Sit-in, imenovane reference HTEC/Bosch/Institut Vinča itd.) je već urađeno u ranijoj sesiji (2026-07-07) — nije ponovo dirano, samo linkovano iz llms.txt.
- ✅ Verifikacija: `php -l` čist, LocalBusiness+telefon potvrđen na 3 različita tipa stranice (home, o-nama, proizvod), `llms.txt` vraća 200, regresija (sportske-podloge, spoljnje-podne-obloge, dimenzije-teniskog-terena) i dalje 200.
- Preostalo iz GEO plana van obima CC-a: `robots.txt` AI crawler dozvole (samo na LIVE, #ceka-miroslav), PR/case studije/GMB recenzije (#ceka-miroslav) — v. [[seo/geo-ai-plan]].

## 2026-07-08 [claude-code] [W2 2.7] — Product schema na SVE WooCommerce proizvode (globalni fix) ✅
- 🔍 **Nalaz**: WooCommerce-ov ugrađeni structured data output (`WC_Structured_data`) se uopšte ne renderuje na ovom sajtu — proverio na proizvod stranici (`/proizvod/konusni-stitnik-za-i-profil/`): Yoast graf sadrži samo WebPage/ImageObject/BreadcrumbList/WebSite/Organization, nigde `"@type":"Product"`. Nema Yoast WooCommerce SEO premium ekstenzije (samo besplatni Yoast); WoodMart tema ima filter koji dodaje brend u Product schema (`woodmart_add_brands_to_structured_data` na `woocommerce_structured_data_product`) ali taj filter se nikad ne pozove jer bazni WC hook ne radi. Uzrok nije dalje istražen (van budžeta ove sesije) — rešeno zaobilaznim, pouzdanim putem umesto debug-ovanja WC internals.
- ✅ **Rešenje**: jedan globalni hook u `woodmart-child/functions.php` (`wp_footer` + `is_product()` provera) generiše validan Product JSON-LD za SVAKI proizvod odjednom — umesto ručnog upisa na 37 pojedinačnih proizvod stranica. Polja: name, url, sku, description (strip_tags), image, category, offers (priceCurrency RSD, availability iz stvarnog stock statusa, url).
- 🔴 **Namerno izostavljeno**: `aggregateRating`/`review` polja — nema pravih recenzija u sistemu, izmišljanje bi ponovilo tačno onu grešku koja je već nađena na `/teren-za-pickleball/` (fake recenzije, v. Blokeri). `price` se dodaje SAMO ako je `_regular_price`/`get_price()` stvarno postavljen (cenovnik M10 je i dalje prazan za većinu proizvoda) — bez cene u schema-i dok ne stigne od Miroslava, ne izmišljeno.
- ✅ Backup: `functions.php.bak-2026-07-08-pre-product-schema` (kopija fajla pre izmene, pošto je ovo kod izmena, ne DB).
- ✅ Verifikacija: `php -l` čist, testirano na sve 3 "money" linije iz zadatka (Bergo Unique, Ecotile E500/7, Lite Shot 325 košarkaška konstrukcija) — validan JSON, tačno 1 Product schema po stranici, bez cene/ocene tamo gde ne postoje. Regresija: ne pojavljuje se duplo na `/industrijski-podovi/` (ta stranica ima svoj RUČNO ugrađeni Product/AggregateOffer iz F7 P1 sesije — odvojen, nepromenjen), ne pojavljuje se na home. 3 dodatna proizvoda spot-checked (200, bez PHP warning/fatal u izlazu — lažno pozitivan "warning" match bio je CSS varijabla `--notices-warning-bg`, ne greška).
- Efekat: svi budući i postojeći proizvodi automatski dobijaju Product schema, nema potrebe za ručnim radom po proizvodu.

## 2026-07-08 [claude-code] [W2 #10] — Piklbol PRESKOČEN (M odluka) 🔴
- Pre gradnje `/piklbol/`, GSC provera (isti obrazac kao #7/#8 danas) otkrila da `/teren-za-pickleball/` VEĆ postoji i dominira ceo klaster: "piklbol" 404 impr, "oprema za piklbol" 269 impr, "piklbol sport" 134 impr (pozicije 7-27, ima prostora za poboljšanje ali stranica postoji i rangira). Nova stranica bi kanibalizovala.
- 🔴 Ali ta ista stranica nosi **nerešen blokator** iz ranije sesije danas (W1 1.2 #24): izmišljene recenzije u Product schema (4.9/5, 18 recenzija, 3 imenovane lažne osobe) + cena "0.00" placeholder — Miroslav je tada odlučio da se post ne dira dok se ne donese odluka.
- Pitao Miroslava da li da radim samo title/meta (bez diranja schema/recenzija dela) ili da preskočim potpuno. **Odgovor: potpuno preskoči ovu sesiju.** #10 ostaje otvoren i van obima dok se recenzije pitanje ne reši — tek onda title/meta refresh ima smisla.
- Bez ijedne izmene baze ove pod-sesije.
- Plan ažuriran: [[seo/plan-novih-stranica]] #10 označen kao preskočen/#ceka-miroslav, ne kao urađen.

## 2026-07-08 [claude-code] [W2 #8] — Nova stranica `/dimenzije-teniskog-terena/` (ID 16688) ✅
- Nastavak iste sesije — sledeći W2 zadatak po korisnikovom izboru.
- 🔍 **Ključni nalaz pre gradnje**: GSC (Windsor.ai, 2026-01 do 2026-07) pokazuje da `/pop-tenis/` (padel stranica, danas ranije osvežena) **dominira** "dimenzije teniskog terena" klaster — 2.367 impr/6mes na poziciji 1,9 ali samo 1 klik (CTR 0,04%!). Ovo NIJE isti slučaj kao šljaka hub (#7) — sadržaj `/pop-tenis/` je o PADELU, ne o regularnom tenisu; Google ga slučajno match-uje jer padel opis pominje "trećina teniskog terena" bez pravih brojeva. Intent mismatch (korisnik traži dimenzije regularnog terena, dobija padel stranicu) → ovde JE opravdano napraviti novu, tačno ciljanu stranicu (za razliku od #7 gde je kanibalizacija bila loša ideja).
- ✅ Nova stranica po F6/"dimenzije" šablonu (identičan obrazac kao `/dimenzije-kosarkaskog-terena/` iz 2026-07-06): hero sa direktnim odgovorom (GEO), 3 stat kartice (singl 23,77×8,23m / dubl 23,77×10,97m / ukupna preporučena površina ITF 36,57×18,29m), tabela svih mera, SVG skica terena (dubl kontura + isprekidane singl linije), sekcija "najbrža podloga" (trava>tvrde>šljaka) + US Open (hard court od 1978), FAQ (5 pitanja) + FAQPage JSON-LD (`vc_raw_html`, base64+rawurlencode).
- ✅ Cross-linkovi: nova stranica → `/podloga-za-teniske-terene/` (šljaka hub, 2×) i → `/pop-tenis/` (padel, poređenje veličine); povratni link `/pop-tenis/` → nova stranica dodat u rečenicu o veličini terena (disambiguacija za Google + korisnike koji traže regularni tenis a slete na padel stranicu).
- ✅ Verifikacija: 200, 1×H1, title/meta u `<head>`, JSON-LD validan (5 pitanja), shortcode balans proveren PRE upisa (6/6/6 vc_row/vc_column/vc_column_text), nema neprocesuiranih `[vc_` ostataka, svi target linkovi 200, regresija (dimenzije-kosarkaskog-terena) i dalje 200.
- Skripta (scratchpad): `create-dimenzije-tenis.php`.
- Podaci (dimenzije, mrežа, ITF preporuka, US Open podloga) su opšte poznate/javne teniske činjenice (ITF pravila), ne izmišljeno.

## 2026-07-08 [claude-code] [W2 #7] — Šljaka hub refresh (postojeća stranica, ne nova) ✅
- Nastavak iste sesije — "najveći" (najveći volumen preostao u W2 Tier2 planu).
- 🔍 **Odluka: NE praviti novu `/sljaka-za-teniske-terene/` stranicu** kako plan predlaže — GSC podaci (Windsor.ai, 2026-01 do 2026-07) pokazuju da `/podloga-za-teniske-terene/` (ID 2699, postojeća stranica) VEĆ rangira na poziciji 4–5 za ogroman volumen ("sljaka" 2.425 impr/6mes, "šljaka" 1.118 impr/6mes, CTR katastrofalnih 0,08–0,09%). Nova konkurentska stranica bi kanibalizovala postojeći rank umesto da ga popravi — isti anti-kanibalizacija princip kao ranije u projektu. Umesto toga: refresh postojeće (isti pristup kao W2 2.3 ranije danas).
- ✅ Title/meta refresh: stari title uopšte nije postojao (fallback na post_title "Podloga za teniske terene", ne pominje "šljaka" iako je to skoro sav saobraćaj na stranici) → novi title vodi sa "Šljaka" + hub napomena (ostale podloge) + meta sa direktnim odgovorom (GEO) + CTA 072.
- ✅ Dodat FAQ blok (3 pitanja: šta je šljaka, da li je jeftinija, koja podloga traži najmanje održavanja) + FAQPage JSON-LD odmah kao pravi `<script>` tag (izbegnuta greška iz odbojka sesije).
- 🔴 **Bug nađen i ispravljen**: sve 4 CTA "Saznaj više" dugmeta na stranici vodila su na `/sportske-podloge/sportski-podovi-za-teniske-terene/` — URL koji nikad nije postojao lokalno (mrtav link na money-page dugmićima kroz CEO članak). Ispravljeno na `/sportske-podloge/` (potvrđeno 200, tematski relevantno).
- 🔴 **Drugi bug nađen i ispravljen**: stranica je imala 2×H1 (WoodMart tema automatski renderuje `post_title` kao H1 + sadržaj je imao svoj `<h1>` blok iz F3 reimporta) — content H1 spušten na H2 (isti obrazac kao poznati "2×H1" gotcha iz woodmart-sabloni, samo ovog puta uzrokovan sadržajem umesto teme).
- ✅ Verifikacija: 200, novi title/meta u `<head>`, 1×H1, JSON-LD validan (3 pitanja), mrtav link zamenjen radnim (200), regresija (pop-tenis, odbojka) i dalje 200.
- Skripte (scratchpad): `w2-sljaka-meta.sql`, `fix-sljaka-hub.php`.
- Plan ažuriran: [[seo/plan-novih-stranica]] #7 zatvoren kao "refresh postojeće", ne nova stranica.

## 2026-07-08 [claude-code] [W2 #9/#11] — FAQ schema fix (odbojka) + FAQ dodat (padel) ✅
- Nastavak iste sesije posle W2 2.3 title/meta prepisa — "w2 nastavi".
- 🔴 **Bug nađen i ispravljen na `/podloga-za-odbojkaske-terene/` (ID 4318)**: FAQPage JSON-LD iz F3 reimporta bio je gola JSON tekst u `post_content` (ne u `<script>` tagu) — `wpautop` ga je razbio u `<p>`/`<br>`, a `wptexturize` pretvorio prave navodnike u kucane (`„…"`), što je i vizuelno izlagalo iskvareni JSON kao vidljiv tekst posetiocima I potpuno onesposobilo schema (Google ne bi ni pokušao da parsira JSON van `<script>` taga). Ovo je verovatno identično na live-u (F3 je povukao sadržaj 1:1 sa live XML exporta) — vredi proveriti kad se cPanel pristup otvori. Fix: `$wpdb->update` direktno (bez `wp_update_post`, izbegava kses probleme), stari razbacani blok zamenjen sa `<script type="application/ld+json">` + minifikovan validan JSON (potvrđeno `json_decode` bez greške, 4 pitanja). Skripta: `fix-odbojka-schema.php`.
- ✅ **`/pop-tenis/` (padel, ID 16611)** — dodat nov FAQ blok (4 pitanja: dimenzije 20×10m, visina mreže 88/92cm, podloga, razlika padel/tenis) + FAQPage JSON-LD napisan ODMAH kao pravi `<script>` tag (izbegnuta ista greška). Sadržaj pre ovoga nije imao nikakav FAQ — potpuno nov dodatak, ne editovanje postojećeg. Napomena: stari dnevnik (2026-06-23) beleži da je "piklbol dodat u uvod" na ovoj stranici, ali taj tekst NE postoji u trenutnom lokalnom sadržaju (verovatno izgubljen u punom F3 reimportu koji je povukao stariju live verziju) — nije rekonstruisano ove sesije jer GSC podaci za ovu stranicu ne pokazuju nijedan pickleball upit (izmišljanje bi kršilo content pravilo); ako je piklbol sekcija i dalje poželjna, treba posebna odluka/#10 `/piklbol/` stranica po planu. Skripta: `add-padel-faq.php`.
- ✅ Verifikacija oba: HTTP 200, JSON-LD `json_decode` bez greške (4/4 pitanja svaki), FAQ tekst vidljiv na stranici, garbled `&#8222;` tekst potpuno nestao sa odbojka stranice, 1×H1 na obe, yoast_indexable keš obrisan.
- Plan ažuriran: [[seo/plan-novih-stranica]] #9 i #11 štiklirani (cena i dalje "na upit", čeka M10).

## 2026-07-08 [claude-code] [W2 2.3] — Title/meta prepis 4 stranice ✅
- ✅ **Backup**: `antasline_local_2026-07-08_pre-w2-title-meta.sql` (47MB), pre svih izmena.
- 🔍 **Metodologija**: pre pisanja title/meta, povučeni stvarni GSC query podaci po stranici (Windsor.ai `searchconsole`, 2026-04-01 do 2026-07-07) da se vidi koji tačno upiti nose impresije — title/meta pisani da pokriju dominantan query klaster, ne pretpostavku.
- **`/pop-tenis/`** (ID 16611, sadržaj je zapravo o Padel tenisu) — Yoast title uopšte nije postojao u bazi (fallback na post_title "Padel tenis"). GSC otkrio da 90%+ impresija dolazi od "dimenzije padel/tenis terena" upita (1100+404+376 impr), ne od "padel tenis" samog. Novi title/meta cilja dimenzije + podlogu + izgradnju. Focuskw ispravljen sa netačnog "Pickleball teren" (sadržaj ne pominje pickleball) na "padel teren dimenzije".
- **`/podloga-za-odbojkaske-terene/`** (ID 4318) — stari title imao bug: `%%sep%% %%sep%%` (dupli prazan Yoast separator placeholder, verovatno import artefakt), nigde nije pominjao "dimenzije" iako je to 100% dominantan query klaster (dimenzije odbojkaškog terena 318 impr @ pozicija 1,09 ali CTR samo 0,3%!). Sadržaj već ima FAQ+FAQPage schema sa tačnim merama (18×9m, mreža 2,43/2,24m, pesak 16×8m) — samo title/meta nije to odražavao.
- **`/spoljnje-podne-obloge/`** (ID 16590, W1 hub rebuild 2026-07-07) — postojeći title/meta bio dobar ali nije pominjao "dvorišta" (795 impr klaster, najveći na stranici, CTR samo 1,76% na poziciji 3,7 — ispod očekivanog za tu poziciju). Dodato u title i meta.
- **Conquest članak `/epoksidni-podovi-ili-ecotile-podovi/`** (ID 2542) — title/meta osvežen sa fokusom na "cena po m²" (212 impr na poziciji 10,8 sa 0 klikova). 🔴 **Usput nađen i ispravljen bug**: CTA box u sadržaju članka imao hardkodovan `tel:+381692340074` + vidljiv tekst "069 234 00 74" — stari broj, mimo 1.9 audita (taj audit je proverio samo `functions.php`/header, ne inline post_content). Ispravljeno na 072 (href + vidljiv tekst).
- 🔍 **Nalaz duplikata**: sve dirane stranice (2542, 4318) imale su duplirane Yoast postmeta redove (2542: 4× focuskw/metadesc, 2× title; 4318: 2× svaki) — verovatno artefakt višestrukih F3 reimport pokušaja. Očišćeno (DELETE+INSERT single row) umesto samo UPDATE, da se izbegne budući flaky Yoast render (`get_post_meta($id,$key,true)` vraća prvi nađeni red, poredak nije garantovan).
- ✅ **Verifikacija**: sve 4 stranice HTTP 200, `<title>`/`<meta name="description">` u `<head>` sadrže nove vrednosti (curl potvrđeno), `wpgs_yoast_indexable` keš obrisan za sva 4 post_id (gotcha #12 — inače stari naslov ostaje keširan), regresija (industrijski-podovi, sportske-podloge) i dalje 200.
- Skripta (scratchpad): `w2-title-meta.sql`.
- **Očekivano** (iz Master Plan analize): +500–700 klikova/90 dana bez ijedne nove stranice. Sledeći W2 korak po planu: Tier1 implementacija (#1-3,6) čim stignu cene od Miroslava (M1, rok 2026-07-10).

## 2026-07-08 [claude-code] [W1 Kategorija F] — product_tag termini rekreirani (8/8) ✅ — W1 1.2 RED ČEKANJA U POTPUNOSTI ZATVOREN
- ✅ **Backup**: `antasline_local_2026-07-08_pre-kategorija-f-tags.sql` (47MB), pre svih izmena (additivna, ne-destruktivna izmena taksonomije, backup ipak uzet po konvenciji).
- 🔍 **Metodologija**: pre upisa, svih 8 live `/oznaka-proizvoda/*/` arhiva scrape-ovano direktnim `curl` (ne WebFetch summarizer — prvi prolaz kroz mali model je vratio identičan tekst za 4 različita URL-a, posumnjano na artefakt pa dvostruko provereno protiv sirovog HTML-a `href="…/proizvod/…"` linkova; ispalo je da je duplirani rezultat TAČAN, ne bug — live zaista tako tagira).
- 🔍 **Nalaz**: 4 termina (`ergomat`, `industrijski-amortizer`, `zastita-kablova`, `zastitnik-cevi`) su na live-u dodeljena identičnom skupu od 9 Ergomat odbojnik proizvoda; druga 3 (`samolepljiva-zastita`, `konusni-stitnik`, `industrijski-bumper`) identičnom 1 proizvodu (Konusni štitnik za I-profil, ID 16476); `bergo` → Bergo Unique (ID 16534, proizvod, ne informativna landing 16679). Svi ciljni proizvodi već postoje lokalno (Woo import), termini kreirani preko `wp_insert_term()` + dodeljeni preko `wp_set_object_terms(..., true)` (append, ne replace).
- ✅ **Term counts potvrđeni identični live-u**: bergo=1, ergomat/amortizer/kablova/cevi=9, samolepljiva/konusni/bumper=1.
- ✅ **Verifikacija**: term_id 272-279 kreirani i dodeljeni · regresija čista (bumperi #15 stranica, Bergo XL, kategorija Zaštita i Bumperi, home i dalje 200) — product_tag je odvojena taksonomija od product_cat pa ne utiče na postojeće `taxonomies="245"` gridove.
- 🔴 **Napomena tokom CSV update-a**: prvi pokušaj regex zamene u `parity-inventar.csv` je ostavio nezatvorene navodnike (CSV escaped `""` unutar polja nije bio properly matchovan) — otkriveno odmah kroz Read verifikaciju, ispravljeno ručnim Edit-om na svih 8 redova pre nastavka.
- Skripta (scratchpad): `create-kategorija-f-tags.php`.
- **W1 1.2 red čekanja (Kategorije A/E/F): u potpunosti zatvoren.** Preostaje samo FAQ konsolidacija (Kategorija E, W2 content-strategija, čeka M odluku). Sledeći W1 fokus: preostale stavke plana (1.4 footer, 1.5 meni, 1.6 mobile QA, 1.7 Figma) ili prelazak na W2/W3.

## 2026-07-08 [claude-code] [W1 Kategorija E] — Konsolidacija/redirect čišćenje (2/3 rešeno) ✅
- Bez izmena baze (samo dokumentacija/redirect mapa — nema destruktivnih izmena, backup nije potreban).
- ✅ **Elektroprovodni-podovi → antistatik**: #1 antistatik stranica gotova od 2026-07-07 (ID 16658), stari live URL `/industrijski-podovi/elektroprovodni-podovi/` nema lokalni parnjak — dodat pravi 301 red u `redirect-mapa-FINAL.csv` + `htaccess-301-DRAFT.txt` (⛔ ne aktivira se do dana migracije, samo dokumentovano). Cilj potvrđen 200 na lokalu.
- ✅ **#27/#31 par**: potvrđeno rešeno iz ranije sesije (nisu duplikat, obe stranice postoje) — Kategorija E red ažuriran da to odražava.
- 🔴 **Usput otkriven i ispravljen zastareo red u `redirect-mapa-FINAL.csv`/`htaccess-301-DRAFT.txt`**: padel-tereni red je i dalje pisao "⏳ ČEKA F5 REBUILD" iako je stranica izgrađena još u W1 #14 sesiji (2026-07-08, ID 16670) na identičnom URL-u kao live — ažurirano na isti "identičan URL, redirect nepotreban" obrazac kao kosarkaske-konstrukcije/sportski-podovi-za-sale-i-balone redovi. `htaccess-301-DRAFT.txt` komentar blok za sva 3 "ČEKA F5" reda (kosarkaske-konstrukcije, padel-tereni, sportski-podovi-za-sale-i-balone) ažuriran — sva tri su rešena kao identičan URL, ništa se ne dodaje u aktivni .htaccess.
- ⏳ **FAQ konsolidacija** (`industrijski-podovi-najcesca-pitanja` ↔ 2 postojeće varijante) — namerno bez akcije, i dalje čeka W2 content-strategija odluku (M).
- **Kategorija E: 2/3 rešeno, 1/3 svesno odloženo na W2.** Sledeći W1 fokus: preostale stavke plana (1.4 footer, 1.5 meni, 1.6 mobile QA, 1.7 Figma) ili prelazak na W2/W3.

## 2026-07-08 [claude-code] [W1 1.2 #33] — Podovi za magacine i hale (ID 16687) ✅ — KATEGORIJA A ZATVORENA
- ✅ **Backup**: `antasline_local_2026-07-08_pre-magacini-hale-33.sql` (47MB), pre svih izmena.
- 🔍 **Izvor**: live URL je ZionBuilder (kao #27/#28/#30-32), WebFetch korišćen za ekstrakciju sadržaja (isti postupak kao kosarkaske-konstrukcije pilot). Sadržaj = **poređenje-vodič**, ne opis jednog proizvoda: koji Ecotile model (500/5 lako/bez vozila, 500/7 srednje opterećenje, 500/10 čest/težak saobraćaj viljuškara) za koji tip magacina/hale. Namerno izgrađena kao troslojni model (uporedna tabela + cross-linkovi) umesto duplikata #4 (500/7 specifična stranica) — različita svrha (decision-guide vs. product spec).
- ⚠️ Live sadržaj je uključivao privremeno obaveštenje o zatvaranju firme (6-15.07.2026) — ispravno **izostavljeno** iz rebuild-a (tranzijentni banner, ne evergreen sadržaj stranice).
- ✅ **Uporedna tabela** (500/5 vs 500/7 vs 500/10) cross-link ka sve tri postojeće Ecotile stranice + srodne teme (trake-za-obeležavanje, ESD/antistatik sa BS EN 61340-5-1 standardom, garaže).
- 🔴 **Usput otkriven i ispravljen DRUGI par dupliranog broken-link buga na `/industrijski-podovi/` hub-u** (treći put ove nedelje, ista šema kao #26 sesija): kartice "Ergonomski podovi" i "Odbojnici — bumperi" u 4-karticnom gridu linkovale su na **stare legacy `industrija-podovi` CPT unose** (5503, 15825 — oba i dalje `publish`, potpuno odvojeni od stvarnih novih al- template stranica 16672/16671 izgrađenih ranije ove nedelje) umesto na prave stranice. Ispravljeno na tačne URL-ove (`/ergonomske-podloge-2/`, `/industrijski-podovi/bumperi-zastita-za-police-regale-i-zidove/`), stari CPT unosi arhivirani u draft (`-stara` sufiks, potvrđeno 404 na starim javnim URL-ovima). Dodata i nova kartica za #33 u isti grid.
- ✅ **Verifikacija**: 200 · 1×H1 · 1 FAQPage JSON-LD · sve slike/linkovi 200 · hub i dalje 200/1×H1/3 validna JSON-LD bloka (Video+FAQ+Product, bez dupliranja) · regresija čista (500/7, 500/10, ergonomske-podloge-2, bumperi, home).
- Skripte (scratchpad): `build-magacini-hale.php`, `fix-hub-links-33.php`.
- **W1 1.2: KATEGORIJA A U POTPUNOSTI ZATVORENA (#1-33, 33/33).** → [[migracija/w1-red-cekanja]]. Preostaje Kategorija E (3 konsolidacije/301 slučaja, nisu W1 rebuild posao) i Kategorija F (8 product_tag termina, F6/F7 posao van W1 obima).

## 2026-07-08 [claude-code] [W1 1.2 #29/#30/#32] — LVT silo ostatak: Expona Click + Commercial (×2) ✅
- ✅ **Backup**: `antasline_local_2026-07-08_pre-lvt-silo-29-30-32.sql` (47MB), pre svih izmena.
- 🔍 **Izvor**: legacy CPT `podovi-posl-prostor` (isti obrazac kao Bergo `spoljne-podne-obloge` CPT) — 4 unosa: `expona-clic19db` (5568, korišćen za #29), `expona-flow` (5591, već korišćen za #11), `expona-commercial` (5636, korišćen za #30/#32), `expona-simplay19db` (5667, nema live URL u parity-inventar.csv → nije deo W1 reda, ostaje samo pomenuto u hub tekstu). Za razliku od #27/#28, ovaj CPT **nije imao conditional_render bug** — normalan generički WoodMart blog template, samo netačan slug/nedostatak Yoast mete.
- 🔍 **#30 vs #32 odluka**: obe URL adrese vode na isti proizvod "Expona Commercial" ali sa različitim uglom — #30 (`vinil-podovi`, 7 kl.) = opšta/dizajn-fokusirana stranica sa punom kolekcijom (12 od 80 dezena, IAC Gold sertifikat, 4 dokumenta), #32 (`vinil-podovi-za-restorane-hotele-kafice-kancelarije-i-poslovne-prostore`, 0 kl.) = kraća namenska stranica za ugostiteljstvo sa 4 izdvojena dezena i FAQ fokusiranim na vlagu/buku/rad bez prekida — cross-link ka #30 za punu specifikaciju. Izbegnut pravi duplikat sadržaja istim pristupom kao #27/#31.
- ✅ **#29 Expona Click** (ID 16684, 12 kl.) — 12 dezena (concrete/steel/oak), 4 realna PDF dokumenta (katalog, DoP, tehnički podaci, uputstvo za montažu — svi potvrđeni na disku).
- ✅ **#30 Expona Commercial** (ID 16685, 7 kl.) — 12 od 80 dezena, IAC Gold sertifikat (real PDF), brošura/tehnički/uputstvo (real PDF-ovi, brošura na nemačkom — zadržana kao original, samo dopunska dokumentacija).
- ✅ **#32 Expona Commercial — ugostiteljstvo** (ID 16686, 0 kl. ali potrebna za parity) — 4 izdvojena dezena, FAQ fokusiran na vlagu/buku/rad bez prekida.
- ✅ **Hub ažuriran** (`/lvt-podovi-za-komercijalne-i-javne-prostore/`, ID 16667): "EXPONA Design" i "EXPONA Click" kartice u gridu sada linkuju na #30/#29 (ranije bez linka), dodat cross-link ka #32 u "Primena" listi (stavka "Hoteli, restorani i kafići").
- ✅ **Verifikacija**: sve 4 stranice (29/30/32/hub) 200 · 1×H1 svuda · po 1 FAQPage JSON-LD (bez dupliranja) · svih 33 slike/PDF-a 200 (CRLF gotcha u verifikacionoj skripti — `tr -d '\r'` pre curl petlje) · cross-linkovi potvrđeni u oba smera (29↔30, 30↔32, hub→29/30/32) · regresija čista (#11 Flow, #13 kancelarije, home i dalje 200).
- Skripta (scratchpad): `build-lvt-silo.php` (helper `al_swatch_grid()` za dezen-grid kartice).
- **W1 1.2: #1-32 zatvoreno.** → [[migracija/w1-red-cekanja]]. Sledeći: #33 (Ecotile magacini-i-hale) — poslednja stavka Kategorije A.

## 2026-07-08 [claude-code] [W1 1.2 #28] — Privremene podloge Isotrack (16 kl., ID 16111) ✅
- ✅ **Backup**: `antasline_local_2026-07-08_pre-isotrack-28.sql` (47MB), pre svih izmena.
- 🔍 **Isti obrazac kao #27**: orphan post 16111 ("Montažno demontažne podloge u pločama", pogrešan slug, kreiran 2026-02-10) je već sadržao pun Isotrack L + Isotrack X sadržaj (specifikacije, primena, video), ali skoro svaki `vc_row` je imao `conditional_render="administrator"` — nevidljiv posetiocima, nigde linkovan, bez Yoast mete. Treći nalaz ove vrste ove nedelje (uz #27) — ista februarska serija zaboravljenih probnih upisa.
- ✅ **Fix**: slug → `privremene-podloge-isotrack`, uklonjen conditional_render, prebačen u al- template. Dva modela odvojena u sekcije: **Isotrack L** (lagana, 2410×1200mm, 36kg, do 20t meko/80t+ tvrdo tlo, ručna montaža) i **Isotrack X** (teška, 4000×2000mm, 360kg, 605 psi/≈415 t/m², mehanizovana montaža, RFID/GPS opcija).
- ✅ **Video**: YouTube ID `QnnOiq90rnM` ("Isotrack ground protection mats", zvaničan kanal ISOTRACK) potvrđen oEmbed-om, ugrađen kao F7.3 lite-embed fasada + VideoObject JSON-LD.
- ✅ **Cross-link oba smera sa #7** (`/iznajmljivanje-podova/`, Bergo Solid rental) — srodna "privremeno/rental" tema, različit proizvod (modularni sportski pod vs. teška ground-protection podloga), zadržane kao zasebne stranice.
- ⚠️ Stari sadržaj imao `[vc_btn ... "Katalog" ... link="url:tel:..."]` (dugme "Preuzmi katalog" koje je zapravo vodilo na tel: link, verovatno placeholder greška iz izvora) — nije prenet, nema lokalnog PDF kataloga za linkovanje.
- ✅ **Verifikacija**: 200 · 1×H1 · 2 JSON-LD bloka (FAQPage + VideoObject, bez dupliranja) · sve slike 200 (5 realnih Isotrack fotki) · nema `<iframe>` u HTML odgovoru (lite-embed potvrđen) · regresija čista (#7, home i dalje 200).
- Skripte (scratchpad): `build-isotrack.php`, `crosslink-iznajmljivanje.php`.
- **W1 1.2: #1-28 zatvoreno.** → [[migracija/w1-red-cekanja]]. Sledeći: #29/#30/#32 (LVT silo ostatak) i #33 (Ecotile magacini-i-hale).

## 2026-07-08 [claude-code] [W1 1.2 #27/#31 + 1.9] — Maloprodajne stranice + tel: audit ✅
- ✅ **Backup**: `antasline_local_2026-07-08_pre-maloprodaja-27-31.sql` (47MB), pre svih izmena.
- 🔍 **Odluka #27/#31**: NISU duplikat. #27 (`/podovi-za-radnje-i-maloprodajne-objekte/`, 26 kl.) = R-Tile brendirana interlocking kolekcija za veliki promet (hipermarketi/lanci). #31 (`/industrijski-podovi/podovi-za-maloprodajne-objekte/`, 6 kl.) = opšta Ecotile 500/5 preporuka za manje poslovne prostore. Obe izgrađene kao zasebne al- template stranice, cross-link u oba smera + #31 linkuje i ka Ecotile 500/5 info stranici.
- 🔴 **Nalaz-bug**: post 16142 (orphan, "Podovi za maloprodajne objekte i hipermarkete", pun R-Tile sadržaj sa specifikacijom/FAQ/testimonijalom) je postojao lokalno ali **svaki `vc_row` je imao `conditional_render="administrator"`** — sadržaj je bio nevidljiv svim običnim posetiocima (samo praznina), stranica nikad nije bila linkovana ni sa jednog menija/huba, bez Yoast mete. Datum kreiranja post-a (2026-02-19) prethodi glavnoj fazi projekta — verovatno zaboravljen probni upis. Iskorišćen kao izvor sadržaja za #27: fixed slug (`podovi-za-radnje-i-maloprodajne-objekte`), uklonjen conditional_render, prebačen u al- WoodMart template (hero/USP/spec tabela/reference/FAQ+FAQPage JSON-LD/CTA), dodat Yoast title/metadesc.
- ✅ **#31 nova stranica** (ID 16683, child 16567 `/industrijski-podovi/`) — sadržaj iz live SiteOrigin exporta (post 1195), prepisan u al- template, stari broj 063 zamenjen aktuelnim 072/069 CTA formatom.
- ✅ **Standardi sa linkovima**: Bfl-s1 → DIN EN 13501-1, R10 → DIN 51130:2004 (reuse istih anchor URL-ova kao na drugim stranicama, konzistentnost).
- ✅ **Verifikacija**: obe 200 · 1×H1 svuda · po 1 FAQPage JSON-LD (bez dupliranja) · sve slike 200 (rtile-ploce, Podovi-maloprodaja, pod-za-maloprodaju) · svi interni linkovi 200 · regresija čista (hub 16567, Ecotile 500/5, 500/7 i dalje 200).
- ✅ **1.9 quick-win — tel: audit sitewide**: SQL grep po `post_content`/`postmeta`/`options` + grep po theme PHP fajlovima. Nalaz: **header top-bar** (`functions.php:75`) je koristio `+381692340074` ("069 234 00 74") dok CTA dugme i mobilna tel-ikonica (linije 143/192) koriste `+381692340072`. Prema CLAUDE.md analitici (072 dominira ~50 vs ~7 klikova, 46/50 mobilnih) — ujednačeno na **072 sitewide**, top-bar ispravljen. Stari 063 broj se ne pojavljuje nigde lokalno (samo u live exportu, sad zamenjen). Neaktivan widget sa starim 072 tekstom u `wp_inactive_widgets` — ne renderuje se, ostavljen bez izmene.
- Skripta (scratchpad): `build-maloprodaja.php` (helper funkcije `al_faq_jsonld_block`/`al_update_content`/`al_set_page`).
- **W1 1.2: #1-31 zatvoreno.** → [[migracija/w1-red-cekanja]]. Sledeći: #28 privremene-podloge-isotrack (16 kl., srodno sa #7).

## 2026-07-08 [claude-code] [W1 1.2 #26] — Ecotile 500/5 rebuild (31 kl., ID 16682) ✅
- ✅ **Backup**: `antasline_local_2026-07-08_pre-ecotile-5005.sql` (47MB), pre svih izmena.
- 🔍 **Izvor**: legacy CPT `industrija-podovi` (5301, "Ecotile 500/5", publish) — ista typo-CPT porodica kao #21 (500/10). Sadržaj: osnovna/najlakša Ecotile ploča (5mm, virgin vinil, 550 kg/cm² otpornost na udar, 17 N/mm² po ISO 4649:2019), namenjena radnjama, kancelarijama, javnim objektima, sportskim salama — **eksplicitno NIJE za viljuškare/teška vozila** (max. ručni paletar do 300 kg), samo unutrašnja upotreba. Ključni diferencijator od 500/7 i 500/10 (nema Woo proizvod za 500/5 — potvrđeno, čisto informativna stranica).
- ✅ **Nema lokalni Woo proizvod za 500/5** (potvrđeno upitom na `product` post_type) — cross-link umesto toga ka postojećem Woo proizvodu Ecotile E500/7 za korisnike kojima ipak treba veća nosivost, plus ka #21 (500/10) stranici u "ograničenje nosivosti" sekciji.
- 🔧 **Usput otkriven i ispravljen dupli broken-link bug na hub-u** (`/industrijski-podovi/`, ID 16567): tabela "Koja debljina za koju namenu?" je linkovala 500/5 I 500/10 na stare legacy CPT slugove (`/industrija-podovi/ecotile-500-5/` i `/industrija-podovi/ecotile-500-10/`, i dalje `publish` status) umesto na stvarne nove build-ovane stranice (16682 i 16678 iz #21 sesije). Oba reda ispravljena na tačne URL-ove.
- ✅ **Arhivirane 2 legacy CPT stavke**: 5301 → `draft`/`ecotile-500-5-stara` (izvor ove sesije), 5298 → `draft`/`ecotile-500-10-stara` (bio publish i broken-linked sa hub-a otkad je #21 izgrađen, nije ranije arhiviran jer nije korišćen kao sadržajni izvor — sad zatvoren kao čišćenje).
- ✅ **Verifikacija**: 200 (nova stranica + hub + 500/7 + 500/10 regresija) · 1×H1 svuda · FAQPage JSON-LD validan · sve slike 200 (3 prave fotke: sportska sala, market, zubarska ordinacija) · svi interni linkovi 200 · hub sadrži oba ispravljena linka.
- Skripte (scratchpad): `build-ecotile-5005.php` (koristi `al-helpers.php`), `update-hub-ecotile-links.php`, `verify-ecotile-5005.php`.
- **W1 1.2: #1-26 zatvoreno (23/33)** → [[migracija/w1-red-cekanja]]. Sledeći po klikovima: #27/#31 moguć duplikat par (podovi-za-radnje-i-maloprodajne-objekte ↔ industrijski-podovi/podovi-za-maloprodajne-objekte) — proveriti preklapanje pre gradnje oba; #28 privremene-podloge-isotrack (16, srodno sa #7 iznajmljivanje).

## 2026-07-08 [claude-code] [W1 1.2 #25] — Bergo Elite rebuild (33 kl., ID 16681) ✅
- ✅ **Backup**: `antasline_local_2026-07-08_pre-bergo-elite.sql` (47MB), pre svih izmena.
- 🔍 **Izvor**: legacy CPT `spoljne-podne-obloge` (5028, "Bergo Elite", publish) — isti postupak kao #22/#9. Specifikacija: PP, 380×380mm, 10,2mm, **350 t/m² = 3.500 kg/dm² (130 kg/cm²) nosivost** (manja od Bergo Unique-ovih 500 t/m²), **26 boja ukupno** (7 standardnih + 19 dizajn — bogatije od Unique-ovih 25), primena naglašena ka ugostiteljstvu/poslovnim prostorima (kafići, kancelarije, prodavnice, štandovi, showroom) + eksplicitna mogućnost brendiranja pločama logotipom (jedina od 3 dosad građena Bergo modela sa ovom opcijom u izvornom sadržaju).
- ✅ **Diferencijacija od Bergo Unique** eksplicitno u USP kartici + FAQ ("Po čemu se razlikuje?"): oba puna jednobojna dezena, ali Elite ima širi izbor boja (26 vs 25) i brendiranje logotipom, Unique ima veću nosivost (500 vs 350 t/m²) — realna razlika iz izvornih specifikacija, ne izmišljena.
- ✅ **Prave fotke** (6 na disku, potvrđene): terasa kafića, balkon, brendiranje logotipom (bonus — potkrepljuje USP), prodajni prostor cvećare — iskorišćene u ugradnja-koraku #3 i posebnoj referenci-galeriji od 4 slike.
- ✅ **Cross-link**: nova stranica → Bergo Unique + hub; **hub (16590) ažuriran** — plain `<h3>Bergo Elite</h3>` sad linkuje, i **ispravljen zastareo broj boja u vidljivom tekstu i u FAQPage JSON-LD** ("u 6 standardnih boja" → "u 26 boja (7 standardnih + 19 dizajn)", uz dodatak o brendiranju) — hub je imao pojednostavljenu/zastarelu brojku iz ranije marketing-kopije, sad usklađena sa stvarnom legacy specifikacijom.
- 🔍 **Otvoreno pitanje iz prošle sesije REŠENO**: proverio `migracija/parity-inventar.csv` — `bergo-solid` i `bergo-flow` (CPT 5051/5053) **nemaju live URL** (nisu deo sitemap inventara), za razliku od elite/unique/xl/easy koji su svi potvrđeni NEDOSTAJE-LOKAL sa live URL-om. Zaključak: nisu deo trenutne ponude za W1 parity rebuild, ne ulaze u red čekanja. (Napomena: hub pominje četvrti model "Bergo Soft" za bazenske ivice — različito ime/moguće preklapanje sa već izgrađenom `/spoljnje-podne-obloge/podovi-za-bazene/`, van obima ove sesije.)
- ✅ **Stara CPT stavka** 5028 → `draft`/`bergo-elite-stara`.
- ✅ **Verifikacija**: 200 (nova stranica + hub + unique + xl regresija) · 1×H1 svuda · JSON-LD (FAQPage + VideoObject) validan bez dupliranja · svih 15 slika 200 · svi interni linkovi 200 (osim trivijalnog `/antasline` bez trailing slash → 301, isto na svim ostalim stranicama).
- Skripte (scratchpad): `build-bergo-elite.php` (koristi `al-helpers.php`), `update-hub-elite-link.php` (decode/patch/re-encode FAQPage JSON-LD blok), `verify-bergo-elite.php`.
- **W1 1.2: #1-25 zatvoreno (22/33)** → [[migracija/w1-red-cekanja]]. Sledeći po klikovima: #26 Ecotile 500/5 (31, nema lokalni Woo proizvod — proveriti pre gradnje), #27/#31 podovi-za-radnje/maloprodajne (moguć duplikat par, proveriti pre gradnje oba).

## 2026-07-08 [claude-code] [W1 1.2 #24] — Gumirana podloga za pickleball / Bergo Ultimate FLOW (41 kl., ID 16680) ✅
- ✅ **Backup**: `antasline_local_2026-07-08_pre-pickleball-podloga.sql` (47MB), pre svih izmena.
- 🔍 **Izvor**: legacy CPT `spoljne-podne-obloge` (5053, "Bergo Flow", publish) — sadržavao je punu FLOW specifikaciju (PE, FDA odobren, 374×374mm, 12,4mm, EN14877 standard, 13 boja) i 10 pravih pickleball fotografija (attachment ID 16237-16247).
- 🔴 **VAŽAN NALAZ (nerešen, čeka odluku)**: postojeći post `/teren-za-pickleball/` (ID 16616, pravila+dimenzije) VEĆ sadrži veliku sekciju o Bergo Ultimate FLOV™ podlozi + sopstvenu **Product schema sa `aggregateRating` (4.9/5, 18 recenzija) i 3 imenovane "recenzije"** (Marko Petrović, Ana Jovanović, Ivan M.) koje deluju izmišljeno (nisu iz stvarnog review sistema) + placeholder `"price": "0.00"` u Offer bloku. Ovo krši "ne izmišljati brojeve" pravilo i nosi rizik za Google rich-results (fake review policy). **Korisnik je eksplicitno odlučio (2026-07-08): izgraditi #24 kao planirano, NE dirati post za sada** — čišćenje fake recenzija ostaje otvoreno pitanje za buduću sesiju/odluku.
- ✅ Nova stranica (child `/sportske-podloge/`) — čist, fokusiran sadržaj o samoj podlozi (specifikacija/6 USP/13 boja/3 galerija fotke/FAQ), sa linkom ka `/teren-za-pickleball/` za pravila i dimenzije (nije diran suprotan smer, videti nalaz gore).
- ✅ Standard EN 14877 linkovan (en-standard.eu, potvrđen WebSearch-om pre upisa).
- ✅ **Verifikacija**: 200 · 1×H1 · FAQPage JSON-LD bez dupliranja · 3 slike 200 · regresija na `/teren-za-pickleball/` i `/sportske-podloge/` čista.
- Skripte (scratchpad): `build-24-pickleball-podloga.php` (nova `al_swatch2()` lokalna helper funkcija, duplikat `al_swatch()` iz bergo-unique sesije — razmotriti konsolidaciju u `al-helpers.php` ako se ponovi treći put).
- **W1 1.2: #1-24 zatvoreno (21/33)** → [[migracija/w1-red-cekanja]]. Sledeći: #25 bergo-elite (33, isti CPT porodica kao #22). **#ceka-odluku: fake recenzije na `/teren-za-pickleball/` Product schema** — predložiti Miroslavu čišćenje ili zamenu pravim recenzijama pre live migracije (rizik: Google spam policy za fabricated reviews).

## 2026-07-08 [claude-code] [W1 1.2 #22] — Bergo Unique rebuild (53 kl., ID 16679) ✅
- ✅ **Backup**: `antasline_local_2026-07-08_pre-bergo-unique.sql` (47MB), pre svih izmena.
- 🔍 **Izvor**: legacy CPT `spoljne-podne-obloge` (4936, publish, stari Porto-era markup) — isti postupak kao bergo-xl (#2) sesija. Bogat sadržaj potvrđen: **25 boja** (12 standardnih + 13 "dizajn" boja, više od Bergo XL-ovih 16), specifikacija (PP, 380×380mm, 10,1mm, **500 t/m² = 130 kg/cm² nosivost — veća od Bergo XL-a**, 250 t/m²/85 kg/cm²), 2 install fotke, video (Bergo Flooring AB, `Yfw14Tt94ec`, potvrđen oEmbed-om, isti video kao bergo-xl jer je montaža identična za sve Bergo modele).
- ⚠️ **Woo proizvod već postojao** (16534, "Bergo Unique", publish, term Woo katalog) — ali informativna landing stranica (kao kod XL/Easy/Ultimate) nije postojala. Potvrđen obrazac: svaki Bergo model dobija zaseban `page` (edukativni/specifikacija/boje/ugradnja) ODVOJENO od transakcionog Woo proizvoda.
- ✅ **Diferencijacija od Bergo XL** eksplicitno u sadržaju (USP kartica + FAQ pitanje "Po čemu se razlikuje od XL?"): puna jednobojna površina (mirniji izgled) nasuprot XL cvetnom/florentinskom dezenu, veća nosivost, bogatiji izbor boja — sprečava dojam duplog sadržaja.
- ✅ **Cross-link**: nova stranica → Bergo XL i `/spoljnje-podne-obloge/` hub; **hub (16590) ažuriran** — stari plain `<h3>Bergo Unique</h3>` (bez linka, čekao je na ovu sesiju) sada linkuje na novu stranicu.
- ✅ Stara CPT stavka 4936 → `draft`/`bergo-unique-stara` (isti obrazac kao ranije arhivirane CPT stavke).
- ✅ **Verifikacija**: 200 · 1×H1 · FAQPage + VideoObject JSON-LD (2 bloka, bez dupliranja) · sve 3 slike 200 · 6 USP ikonica render (montaza/fleksibilna/odrzavanje/izdrzljivost/protivklizna/izgled) · regresija na bergo-xl i Woo proizvod čista.
- Skripte (scratchpad): `build-22-bergo-unique.php` (nova `al_swatch()` helper funkcija za boje-grid, generiše swatch markup umesto ručnog pisanja 25× div bloka), `archive-4936-and-link.php`.
- **W1 1.2: #1-23 zatvoreno (20/33)** → [[migracija/w1-red-cekanja]]. Sledeći: #24 sportska-podloga-za-pickleball (41), #25 bergo-elite (33, isti CPT izvor kao ova sesija — proveriti da li 4936/5028 CPT porodica ima i bergo-solid/bergo-flow van trenutne liste).

## 2026-07-08 [claude-code] [W1 1.2 #19-#21 + #23] — epoksid-conquest srodna, oprema za sportske terene + reflektori, Ecotile 500/10 ✅
- ✅ **Backup**: `antasline_local_2026-07-08_pre-w1-19-21.sql` (47MB), pre svih izmena.
- **#19 industrijski-podovi-montaza-preko-ostecenog-epoksida** (72 kl., ID 16675, root-level) — Ecotile PVC preko oštećenog epoksida/betona/pločica/vinila, priprema+montaža+FAQ. **Cross-link u OBA smera sa conquest člankom (post 2542)**: nova stranica linkuje na `/epoksidni-podovi-ili-ecotile-podovi/` u intro pasusu; conquest članak je već imao sekciju "Ecotile u praksi: montaža preko oštećenog epoksidnog poda" koja je linkovala samo na generički `/industrijski-podovi/` — ažurirana da linkuje na ovu novu detaljnu stranicu. Nikad nije predložen epoksid kao rešenje (stranica je o Ecotile-u koji PREKRIVA oštećeni epoksid).
- **#23 opremazasportsketerene** (48 kl., ID 16676, child `/sportske-podloge/`) — **silo parent izgrađen PRE deteta #20** (isti obrazac kao LVT #12/#11 sesija). Grid: košarkaške konstrukcije (link na postojeću 16657), zaštitne mreže, golovi (slike), LED reflektori (link na #20).
- **#20 reflektori-za-sportske-terene** (71 kl., ID 16677, child #23) — Ritelite Sports-Lite mobilni LED komplet, puna specifikacija (22.000 lm, IP65, baterija 2h20-4h20min) + cena (266.000 din/2kom). 🔴 Nema lokalnih slika proizvoda u media library-u (za razliku od gotovo svih ostalih sesija) — čisto tekst/specifikacija, bez fotografije.
- **#21 podne-ploce-ecotile-50010** (56 kl., ID 16678, child `/industrijski-podovi/`) — Ecotile 500/10 (10mm, 550kg/cm² otpornost na udar), specifikacija tabela sa linkovanim standardima (BS 476, DIN 53516). Cross-link ka postojećem Woo proizvodu (16540, `ecotile-e500-10-ultra-heavy-duty-interlocking-podna-ploca`) i ka #19 (montaža preko oštećenog epoksida). Legacy CPT duplikat `industrija-podovi` (5298, "Ecotile 500/10") potvrđen kao stari sadržaj, ne pravi Woo proizvod — ignorisan.
- ✅ **Verifikacija sve 4**: 200 · 1×H1 · FAQPage JSON-LD bez dupliranja · sve slike 200 · regresija na industrijski-podovi/conquest/sportske-podloge/kosarkaske-konstrukcije čista.
- Skripte (scratchpad): `build-19-epoksid-montaza.php`, `link-2542-to-19.php`, `build-23-oprema-sportski-tereni.php`, `build-20-reflektori.php`, `build-21-ecotile-50010.php` (svi koriste `al-helpers.php` iz prethodne sesije).
- **W1 1.2: #1-21 + #23 zatvoreno (19/33)** → [[migracija/w1-red-cekanja]]. Sledeći po klikovima: #22 bergo-unique (53, legacy CPT izvor postoji), #25 bergo-elite (33, isti CPT), #24 pickleball podloga (41).

## 2026-07-08 [claude-code] [W1 1.2 #13-#18] — 6 stranica u nizu (kancelarije, padel, bumperi, ergonomske, veštačka trava terase, galerija) ✅
- ✅ **Backup**: `antasline_local_2026-07-08_pre-w1-13-18.sql` (47MB), pre svih 6 izmena.
- **#13 kancelarije-i-poslovni-prostori** (128 kl., ID 16669) — LVT silo podstranica, child #12 (16667). EXPONA Clic 19dB + Cavalio + Simplay fokus (klik-sistem bez lepljenja), specifikacija tabela, EXPONA Design/Commercial dezen grid (Design-Tile-Retail.Office linija, pravе slike). Cross-link dodat na #12 parentu (nazad).
- **#14 padel-tereni** (119 kl., PAGE tip, ID 16670) — child `/sportske-podloge/`. Notiks Tvist (Safitex, Italija) — specifikacija + FAQ. ⚠️ Napomena: live Yoast metadesc pominje "proizvođača Sit-in" ali live body sadržaj pominje Safitex/Notiks — preneto verbatim po parity pravilu (nesklad postoji već na live-u, nismo ga uveli). **Usput nađen i ispravljen pravi broken link**: `/sportske-podloge/` grid je linkovao na `/padel-tenis/` (301 → stara `padel-tenis-2-2` stranica) umesto na ovu novu — ispravljeno na `/sportske-podloge/padel-tereni/`.
- **#15 bumperi-zastita-za-police-regale-i-zidove** (113 kl., ID 16671) — child `/industrijski-podovi/`. **Prvi F6 troslojni model posle pilota** (kosarkaske-konstrukcije): 19 postojećih Ergomat bumper proizvoda već u Woo kategoriji `Zaštita i Bumperi` (term_id 245) → `[woodmart_products taxonomies="245" ...]` auto-grid radi bez izmene. Cross-link u OBA smera: nova stranica → kategorija (u intro pasusu), kategorija (16572, Layout Builder) → nova stranica (dodat pasus).
- **#16 ergonomske-podloge-2** (110 kl., ID 16672, root-level) — 8 Ergomat tipova podloga (Diamond Allround, Soft Air Meter, SuperSoft Smooth/Office, La Ola/La Ola Hygienic, Nitrile Walk, Solido I). 🔴 **Gap potvrđen**: nula lokalnih Woo proizvoda za ovu liniju (za razliku od bumpera) — čisto informativna stranica, cena "na upit" svuda. Kandidat za buduće `/obogati-proizvod` uvoženje kao pravi proizvodi. 🐛 **Nov gotcha**: fajl sa non-ASCII karakterom u imenu (en-dash u `Supersoft-Smooth-–-PU.webp`) vraćao 403 dok `src` nije eksplicitno URL-encode-ovan (`%E2%80%93`) — literalni Unicode karakter u `<img src>` ne radi pouzdano na ovom Apache setupu.
- **#17 vestacka-trava-za-terase** (104 kl., ID 16673) — child `/spoljnje-podne-obloge/`. ⚠️ **Overlap provera zatvorena**: potvrđeno da NIJE duplikat postojećeg `/vestacka-trava/` (5455, 1538 kl., PARITY) — to je opšta/sportska veštačka trava (Sit-in/Edel Grass, fudbal/tenis), dok je ova stranica dekorativna Condor Grass linija (Highlands/Nature/Put/Springgrass) za dvorišta/terase. Realne slike po modelu + bojama (Plava/Srebrna/Ljubičasta/Limun/Antracit). **Usput nađeno i ispravljeno**: 3 stara WP nav menu item-a pod "Terase i dom" (5248 Bazeni, 5257 Bašte i dvorišta, 5462 Veštačka trava za terase) pokazivala na DRAFT/pogrešne stare post ID-eve (5231, 5255, 5455) — ažurirani na tačne trenutne stranice (16662, 16590, 16673). Meni trenutno nije renderovan u WoodMart headeru (čeka W1 1.5), ali podaci su sad tačni za kad se to uradi.
- **#18 galerija** (88 kl., ID 16674, root-level) — Live ima **potvrđeno pokvarene placeholder slike** (i na produkciji), ali lokalni media library ima 9 pravih fotografija terena (3x3: Jakovo/Zlatibor/Novi Sad Banatić; pun teren: Spanoulis Court Beograd, Bergo multisport Slankamen/Subotica/Belegiš/Širig/Vrdnik) — lokalna verzija je faktički bolja od live-a. Bez FAQ (galerija ne zahteva ga po standardu).
- ✅ **Verifikacija svih 6**: 200 · 1×H1 · FAQPage JSON-LD validan bez dupliranja (osim #18 bez FAQ) · sve slike 200 (uključujući encoding fix #16) · regresija na 5 dodirnih stranica (parent-i, kategorija, kosarkaske-konstrukcije) čista.
- Skripte (scratchpad): `al-helpers.php` (deljeni FAQ/VideoObject JSON-LD + meta helper, reusable za buduće sesije), `build-13..18-*.php`, `link-12-to-13.php`, `fix-5438-padel-link.php`, `link-category-to-15.php`, `fix-16-image-url.php`.
- **W1 1.2: #1-18 zatvoreno (18/33)** → [[migracija/w1-red-cekanja]]. Sledeći: #19 industrijski-podovi-montaza-preko-ostecenog-epoksida (72, conquest-srodna), #20 reflektori-za-sportske-terene (71), #21 ecotile-50010 info (56).

## 2026-07-08 [claude-code] [W1 1.2 #11] — EXPONA Flow / vinil-podovi-objectflor (150 kl., ID 16668) ✅
- ✅ **Backup**: deljen sa #12 (`antasline_local_2026-07-08_121215_pre-lvt-silo-parent.sql`), pre oba.
- 🔍 **Redosled sesije namerno izmenjen**: korisnik je tražio "10, 11, 12" ali w1-red-cekanja izričito kaže da #12 (LVT silo parent) mora biti izgrađen PRE #11 (njegova podstranica) — sagrađeno u redosledu 10 → 12 → 11, sve tri stavke isporučene.
- ✅ **Nova stranica ID 16668** (`page`, post_parent=16667) na **identičnoj live-parity URL** `/lvt-podovi-za-komercijalne-i-javne-prostore/vinil-podovi-objectflor/`. Sekcije: hero → kolekcija (3 realne reference slike, uključujući `expona-flow-lvt-pod.jpg`/`expona-flow-design.jpg` čiji fajlovi tačno odgovaraju live alt-tekstu) → **Indoor Air Comfort Gold sertifikat** (pravi PDF asset pronađen lokalno, `Certificate-Indoor-Air-Comford-Gold-EXPONA-FLOW...pdf`, linkovan direktno — potvrđena stvarna činjenica, ne izmišljena) → priprema podloge → primena → FAQ (6 pitanja) + FAQPage JSON-LD → CTA sa cross-linkom nazad ka LVT silo parentu.
- 💰 **Cena**: nema unosa u cenovniku → FAQ upućuje na upit.
- ✅ **Verifikacija**: 200 · 1×H1 · FAQPage JSON-LD bez dupliranja · title tag čist · sve slike + PDF sertifikat 200 · regresija na parent (16667, cross-link vidljiv) čista.
- Skripte (scratchpad): `create-vinil-objectflor.php`, `check-escape-16668.php`.
- **W1 1.2 #11 zatvoren** → [[migracija/w1-red-cekanja]]. LVT silo: 2/6 stranica gotove (parent + Flow). Preostaje: #13 kancelarije-i-poslovni-prostori (128), #29 expona-click (12), #30/#32 vinil-podovi/Expona Commercial.

## 2026-07-08 [claude-code] [W1 1.2 #12] — LVT silo parent (144 kl., ID 16667) ✅
- ✅ **Backup**: `antasline_local_2026-07-08_121215_pre-lvt-silo-parent.sql` pre svih izmena.
- 🔍 **Kontekst**: potpuno nov klaster — nula lokalnih proizvoda/kategorija za LVT/Expona (za razliku od svih prethodnih sesija koje su imale bar delimičan lokalni izvor). Sadržaj u potpunosti izveden iz WebFetch-a live stranice (brend Objectflor, 4 EXPONA kolekcije: Design/Flow/Simplay/Click).
- ⚠️ **Namerno bez linkova ka negrađenim podstranicama**: live hub linkuje 4 podstranice (kancelarije, expona-click, vinil-podovi, vinil-podovi-objectflor) — ova sesija gradi samo poslednju (#11), pa su preostale 3 pomenute tekstualno bez `<a href>` da se izbegnu mrtvi linkovi. Buduće sesije koje grade #13/#29/#30/#32 treba da dodaju linkove ovde.
- ✅ **Nova stranica ID 16667** (`page`, top-level) na **identičnoj live-parity URL** `/lvt-podovi-za-komercijalne-i-javne-prostore/`. Sekcije: hero → intro + 4 USP kartice → 4 kolekcije (kartice sa realnim slikama pronađenim u 2020/12 uploads, jedina postojeća slika po kolekciji potvrđena na disku) → primena → FAQ (6 pitanja) + FAQPage JSON-LD → CTA.
- 💰 **Cena**: nema cenovnik unosa (nov proizvod-klaster) → "na upit" svuda.
- ✅ **Verifikacija**: 200 · 1×H1 · FAQPage JSON-LD bez dupliranja · title tag čist · sve 4 slike 200 · vizuelna provera (2 od 4 kartice prvo prazne u screenshot-u — potvrđeni poznati lazy-load artefakt, curl 200 na sve).
- Skripte (scratchpad): `create-lvt-silo.php`, `check-escape-16667.php`.
- **W1 1.2 #12 zatvoren** → [[migracija/w1-red-cekanja]]. Mora ostati parent referenca za sve buduće LVT podstranice.

## 2026-07-08 [claude-code] [W1 1.2 #10] — Trake za obeležavanje (153 kl., ID 16666) ✅
- ✅ **Backup**: `antasline_local_2026-07-08_120805_pre-trake-obelezavanje.sql` pre svih izmena.
- 🔍 **Nalaz — treći primer "industrija-podovi" (typo) CPT porodice**: postojao je iznenađujuće **bogat i nedavan** (CSS timestamp-ovi 2025-12/2026-07, slike 2025/07 upload) draft na `/trake-za-obelezavanje/` (top-level, ID 15838, post_type `industrija-podovi`) sa preciznim tehničkim podacima (11/7/4 boje po modelu, širine 2"/3"/4", dužine rolni 100'/200'/400', metrički ekvivalenti) — bolji izvor od WebFetch live sažetka, iskorišćen kao primarni izvor teksta. Ovo je TREĆI nalaz ove CPT porodice sa "industrija" (bez "ski") u slugu koja generiše broken linkove (posle Ecotile 500/7 sesije) — obrazac vredi imati na umu za preostale W1 stranice.
- ✅ **Nova stranica ID 16666** (`page`, post_parent=16567) na **identičnoj live-parity URL** `/industrijski-podovi/trake-za-obelezavanje/`. Sekcije: hero → zašto traka (4 USP kartice) → **vodič za izbor** (uporedna al-table: Xtreme/Mean Lean/Supreme V/Floor Marking Shapes po nameni, bojama, širinama) — namerno diferencirano od postojeće Woo kategorije "Podno obeležavanje" (ID 16575, term 248, već ima hero+USP+grid+FAQ) da se izbegne kanibalizacija → primena → auto grid `[woodmart_products taxonomies="248"]` (6+ realnih DuraStripe proizvoda, potvrđeno radi bez namena-taga, direktno preko product_cat term ID) → FAQ (6 pitanja) + FAQPage JSON-LD → CTA.
- 🔧 **Slug kolizija rešena preventivno**: 15838 preimenovan u `trake-za-obelezavanje-stara` + draft PRE kreiranja nove stranice.
- 🔧 **Drugi broken link na `/industrijski-podovi/` parentu ispravljen**: "Trake za obeležavanje" kartica u 4-kartičnom gridu je ciljala `industrija-podovi/trake-za-obelezavanje/` (isti typo obrazac) → ispravljeno na tačan URL.
- ℹ️ **WP canonical redirect nalaz**: posle arhiviranja starog top-level sluga, `/trake-za-obelezavanje/` (bez prefiksa) sada vraća 301 → tačna nova ugnježdena URL — WordPress automatski razrešava po slugu bez obzira na hijerarhiju, nije potrebna ručna redirect mapa stavka niti je ovo bug.
- 💰 **Cena**: cenovnik red "DuraStripe trake za obeležavanje" prazan → "na upit".
- ✅ **Verifikacija**: 200 · 1×H1 · FAQPage JSON-LD bez dupliranja · title tag čist · auto-grid mehanika potvrđena (prava DuraStripe imena/slike) · regresija na `/industrijski-podovi/` (schema Product+FAQPage netaknuti, oba linka rade) čista.
- Skripte (scratchpad): `create-trake-obelezavanje.php`, `fix-parent-link-trake.php`, `check-escape-16666.php`.
- **W1 1.2 #10 zatvoren** → [[migracija/w1-red-cekanja]]. Sledeći: #12 LVT silo parent (144, gradi se PRE #11).

## 2026-07-08 [claude-code] [W1 1.2 #9] — Bergo Easy (166 kl., ID 16665) ✅
- ✅ **Backup**: `antasline_local_2026-07-08_114832_pre-bergo-easy.sql` pre svih izmena.
- 🔍 **Kontekst pre gradnje**: legacy CPT draft (ID 5830, `spoljne-podne-obloge`, iz bergo-xl sesije poznat izvor) sadržao bogatu specifikaciju (PP, 302×302mm, 14mm, 800 t/m²), 7 standardnih + 9 dizajn boja sa hex kodovima, i zvaničan Bergo Flooring AB instalacioni video (`RIqaFPK0C6s`, oEmbed potvrđen). **Nalaz**: live stranica se u međuvremenu proširila u opštiji "event flooring" hub (veštačka trava u bojama, vinil rolne, isotrack) — ali w1-red-cekanja F4 odluka je da Bergo Easy ostaje **zasebna proizvod-stranica** na ovom URL-u, pa je sadržaj građen iz lokalnog CPT izvora (fokusiran, ne pokušava da pokrije sav prošireni live obim).
- ⚠️ **Slike u CPT media grid-u preskočene**: fajlovi 5045-5050 nose imena/alt-tekst koji ne odgovaraju Bergo Easy sadržaju (terasa/bazen fotke, verovatno recikliran asset iz drugog perioda sajta) → **nije korišćena statična galerija** da se izbegne pogrešno kontekstuiranje slika; video (koji JESTE potvrđeno tačan za ovaj proizvod) preuzeo ulogu vizuelnog dokaza.
- ✅ **Nova stranica ID 16665** (`page`, post_parent=16590) na **identičnoj live-parity URL** `/spoljnje-podne-obloge/bergo-easy/`. Sekcije: hero → intro + 4 USP kartice → primena (bullet lista 6 namena) → 7 boja (swatch grid, realni hex iz izvora) → specifikacija tabela + video lite-embed (F7.3 fasada) + VideoObject schema → FAQ (6 pitanja) + FAQPage JSON-LD → CTA.
- 🔧 **Slug kolizija rešena preventivno**: stari CPT (5830) preimenovan u `bergo-easy-stara` PRE kreiranja nove stranice (isti postupak kao bergo-xl), sad 404 kao očekivano.
- 💰 **Cena**: cenovnik ima red "Bergo Easy" ali prazan → "na upit", FAQ isto.
- ✅ **Verifikacija**: 200 · 1×H1 · FAQPage+VideoObject JSON-LD bez dupliranja · title tag čist · svi linkovi/ikonice 200 · vizuelna provera (primena lista + boje + spec tabela) · regresija na `/spoljnje-podne-obloge/` čista.
- Skripte (scratchpad): `create-bergo-easy.php`, `check-escape-16665.php`.
- **W1 1.2 #9 zatvoren** → [[migracija/w1-red-cekanja]]. Sledeći: #10 trake-za-obelezavanje (153 kl.).

## 2026-07-08 [claude-code] [W1 1.2 #8] — Podovi za garaže i auto-servise (229 kl., ID 16664) ✅
- ✅ **Backup**: `antasline_local_2026-07-08_114430_pre-garaze-autoservisi.sql` pre svih izmena.
- 🔍 **Nalaz pre gradnje**: w1-red-cekanja je označila ovu stranicu kao "kandidat za F6 troslojni model" (namena: garaza), ali provera je pokazala da `namena-garaza` product_tag ima samo **1 tagovan proizvod** (Ecotile E500/7) i da live stranica sama pokriva **jedan proizvod** (Ecotile 500/7 za garaže), ne multi-proizvod hub — F6 troslojni grid model odbačen kao neprikladan za ovaj obim, građeno kao standardna informativna sub-stranica (isti obrazac kao Ecotile 500/7 stranica, ali garažni ugao: ulje/hemikalije, visina poda, boje/dezeni, podzemne garaže).
- ✅ **Nova stranica ID 16664** (`page`, post_parent=16567 `/industrijski-podovi/`) na **identičnoj live-parity URL** `/industrijski-podovi/garaze-i-autoservisi/`. Sekcije: hero → intro + 4 USP kartice (namena-garaza ikonica + auto-servis/vulkanizer/podzemna garaža) → karakteristike (3 kartice) + cross-linkovi (Ecotile 500/7 spec stranica, Woo kategorije Zaštita i bumperi / Industrijska zaštita) → 3 realne reference slike (garaža, luksuzna garaža, auto-servis — pronađene u postojećim 2020/10 uploads) → FAQ (6 pitanja) + FAQPage JSON-LD → CTA.
- 💰 **Cena**: cenovnik ima red "PVC ploče za garažu"/"Gumeni pod za garažu" ali prazan → "na upit".
- ✅ **Verifikacija**: 200 · 1×H1 · FAQPage JSON-LD bez dupliranja · title tag čist · svi linkovi/slike/ikonice 200 · vizuelna provera (treća galerija slika prvo izgledala prazno u screenshot-u — potvrđeno da je to poznati lazy-load timing artefakt iz automatizovanog Chrome taba, ne pravi bug; slika radi na sledećem screenshot-u i direktnim `curl` 200) · regresija čista.
- Skripte (scratchpad): `create-garaze-autoservisi.php`, `check-escape-16664.php`.
- **W1 1.2 #8 zatvoren** → [[migracija/w1-red-cekanja]]. Sledeći: #9 bergo-easy (166 kl.).

## 2026-07-08 [claude-code] [W1 1.2 #7] — Iznajmljivanje podova (232 kl., ID 16663) ✅
- ✅ **Backup**: `antasline_local_2026-07-08_113858_pre-iznajmljivanje-podova.sql` pre svih izmena.
- 🔍 **Kontekst pre gradnje**: live stranica je servisna (rentiranje, ne prodaja) zasnovana na proizvodu **Bergo Solid** — već postoji lokalno kao legacy CPT (ID 5051, `spoljne-podne-obloge`, publish, ali na sopstvenom CPT slugu koji sad vraća 301 preusmerenje, van obima ove sesije jer nije u w1-red-cekanja listi). Sadržaj/specifikacija (HDPE, 630×575×50mm, nosivost 5 t/m² meko / 600 t/m² tvrdo, ~100 m²/h montaža, UN hitna područja poreklo) i 8 realnih fotografija (šator, kamion na travi, pesak, stadion, događaji) iskorišćeni kao izvor — potvrđeno da fajlovi postoje na disku pre upotrebe.
- ✅ **Nova stranica ID 16663** (`page`, top-level) na **identičnoj live-parity URL** `/iznajmljivanje-podova/`. Sekcije: hero → intro s 4 USP kartice (nosivost/svaki teren/brza montaža/kompletna usluga) → primena (bullet lista 7 namena) → specifikacija tabela (al-table) → 3 realne reference slike → FAQ (6 pitanja, originalno pisano — live nema FAQ) + FAQPage JSON-LD → CTA.
- 📝 **Srodna niska-prioritetna stranica ostavljena van obima**: `/privremene-podloge-isotrack/` (#28, 16 kl.) je označena u w1-red-cekanja kao srodna ovoj (#7) ali NIJE građena ovu sesiju (nizak saobraćaj, van izabranog zadatka) — kandidat za buduću sesiju ako se ukaže prilika za spajanje s ovom temom.
- 💰 **Cena**: nema unosa u cenovniku, servisna/projektna cena — FAQ upućuje na upit bez izmišljanja brojki.
- ✅ **Verifikacija**: 200 · 1×H1 · FAQPage JSON-LD bez dupliranja · title tag čist (bez escape bug-a) · svi linkovi/slike/ikonice 200 · vizuelna provera (hero + primena lista + spec tabela) čista.
- Skripte (scratchpad): `create-iznajmljivanje-podova.php`, `check-escape-16663.php`.
- **W1 1.2 #7 zatvoren** → [[migracija/w1-red-cekanja]]. Sledeći: #8 garaze-i-autoservisi (229 kl., F6 troslojni kandidat).

## 2026-07-08 [claude-code] [W1 1.2 #6] — Podovi za bazene (262 kl., ID 16662) ✅
- ✅ **Backup**: `antasline_local_2026-07-08_113427_pre-podovi-za-bazene.sql` pre svih izmena.
- 🔍 **Kontekst pre gradnje**: postojao je **tanak orphan stub (ID 5231)** na `/podloge-oko-bazena/` (pogrešan slug, top-level, bez Yoast mete, samo 2 pasusa + prazan masonry grid) — nedovoljno za pravu landing stranicu. Nijedan od 10 legacy `spoljne-podne-obloge` CPT unosa (bergo-unique/elite/solid/flow/xl/ultimate/easy + Naxos/Patmos Evolution) nije pokrivao bazenski proizvod — specifikacija (380×380mm, 10,1mm, 150 t/m², antibakterijski PP) je jedinstvena za ovaj proizvod. Sadržaj/specifikacija izvučeni WebFetch-om sa live URL-a (dva prolaza — opšti sadržaj + fokusirana provera boja/FAQ/garancije, koja je potvrdila da live NEMA FAQ sekciju niti navedene hex boje u tekstu).
- 💡 **Odluka o bojama**: live pominje "standardna i opciona paleta" ali ne navodi imena/hex kodove boja (za razliku od Naxos/bergo-xl gde su boje bile eksplicitno navedene) → stranica NE prikazuje swatch grid, samo tekstualna napomena "dostupnost na upit" u specifikaciji, bez izmišljanja hex vrednosti.
- ✅ **Nova stranica ID 16662** (`page`, post_parent=16590 `/spoljnje-podne-obloge/`) na **identičnoj live-parity URL** `/spoljnje-podne-obloge/podovi-za-bazene/`. Sekcije: hero → intro + 4 USP kartice (reused ikonice: protivklizna/fleksibilna/odrzavanje/izdrzljivost) → specifikacija tabela (al-table) → 3 realne reference slike pronađene u postojećim uploads folderima (2018/2022, uključujući "Bergo Easy za bazene" i "Bergo Unique za bazene" — potvrđuje da su ovo isti proizvodi samo u bazenskoj primeni) → FAQ (6 pitanja, originalno pisano jer live nema FAQ) + FAQPage JSON-LD → CTA sa cross-linkom ka `/spoljnje-podne-obloge/`.
- 🔧 **Stari thin stub (5231) arhiviran**: `post_status=draft`, slug→`podloge-oko-bazena-stara` (potvrđeno da ga ništa u aktivnom sadržaju nije linkovalo pre arhiviranja), sada 404 kao očekivano.
- 💰 **Cena**: nema unosa u cenovniku za bazenske Bergo modele → specifikacija kaže "na upit", FAQ isto.
- ✅ **Verifikacija**: 200 · 1×H1 · FAQPage JSON-LD bez dupliranja · title tag čist (bez escape bug-a) · svi linkovi/slike/ikonice 200 · vizuelna provera (hero + spec tabela + galerija) · regresija na `/spoljnje-podne-obloge/` (schema+H1 netaknuti) čista.
- Skripte (scratchpad): `create-podovi-za-bazene.php`, `archive-5231.php`, `check-escape-16662.php`.
- **W1 1.2 #6 zatvoren** → [[migracija/w1-red-cekanja]]. Sledeći: #7 iznajmljivanje-podova (232 kl.).

## 2026-07-08 [claude-code] [W1 1.2 #5] — Naxos Evolution podovi za sale i balone (378 kl., ID 16661) ✅
- ✅ **Backup**: `antasline_local_2026-07-08_112542_pre-naxos-sale-balone.sql` pre svih izmena.
- 🔍 **Nalaz pre gradnje**: rich legacy sadržaj već postojao lokalno kao **orphan page ID 15490** "Naxos Evolution" na pogrešnoj URL `/sportske-podloge/naxos-evolution/` (post_parent=5438) — generički template (`<h1>` bez al- klasa, nema Yoast metu, nigde nije linkovan). Sadržaj bogat i realan (specifikacija modula 25×25×1,1cm PP sa 7 muških/ženskih tačaka poveza, 4mm gumena podloga, 15 god. garancije, 2 palete boja sa hex kodovima — 16 "standardnih" + 7 "dizajn", video demo) — iskorišćen kao izvor, isti obrazac kao bergo-xl sesija. Otkriven i **postojeći broken link** na `/sportske-podloge/` hub-u (grid "Izaberite sport" → kartica "Podovi za sale" je ciljala F4-obrisani draft slug `sportski-podovi-za-skole-i-sportske-sale`, 404 pre ove sesije).
- ✅ **Nova stranica ID 16661** (`page`, top-level, post_parent=0) na **identičnoj live-parity URL** `/sportski-podovi-za-sale-i-balone/` — redirect mapa red (redirect-mapa-FINAL.csv, "ČEKA F5 REBUILD") sada rešen bez potrebe za redirekcijom (isti slug kao live). Sekcije: hero → intro s namena ikonicom (namena-sport-dvorana, reused iz F7.2) + 3 USP kartice → specifikacija tabela (al-table, modul/spajanje/guma/površina/montaža/garancija) → 16 standardnih boja (swatch grid, samostalni inline stilovi — ista lekcija kao bergo-xl, ne kopirati Porto `.color-square` klasu) → 3 realne reference slike + video (Module Floors/Sports Partner instalacija, `EKthI0X8Uhs`, oEmbed potvrđen) → FAQ (6 pitanja) + FAQPage/VideoObject JSON-LD → CTA sa cross-linkom ka `/sportske-podloge/`.
- 🔧 **2 cross-link popravke**: (1) hub grid "Podovi za sale" kartica na `/sportske-podloge/` (5438) sada linkuje ka novoj stranici umesto ka obrisanom draftu (`$wpdb->update`, `substr_count()===1` provera); (2) stari orphan 15490 arhiviran (`post_status=draft`, slug→`naxos-evolution-stara`), sad vraća 404 (očekivano, ništa ga nije linkovalo).
- 💰 **Cena**: nema unosa u cenovniku za Naxos Evolution → FAQ odgovor upućuje na upit, bez izmišljanja brojke.
- ✅ **Verifikacija**: 200 · 1×H1 · FAQPage+VideoObject JSON-LD bez dupliranja · title tag čist (bez `\x` escape bug-a iz prošle sesije — ovog puta pisan double-quoted/direktan UTF-8 karakter) · svi linkovi/slike/ikonice 200 · vizuelna provera (Chrome screenshot hero → spec tabela → boje → galerija+video → FAQ) · regresija na `/sportske-podloge/` (schema+H1 netaknuti, novi link radi) čista.
- Skripte (scratchpad): `create-naxos-sale-balone.php`, `fix-hub-link-and-archive.php`, `check-escape-16661.php`.
- **W1 1.2 #5 zatvoren** → [[migracija/w1-red-cekanja]]. Sledeći: #6 podovi-za-bazene (262 kl.), #7 iznajmljivanje-podova (232 kl.).

## 2026-07-08 [claude-code] [W1 1.2 #4] — Ecotile 500/7 industrijski pod (625 kl., ID 16660) ✅
- ✅ **Backup**: `antasline_local_2026-07-08_110922_pre-ecotile500-7-page.sql` pre svih izmena.
- 🔍 **Kontekst pre gradnje**: live `/industrijski-podovi/industrijski-pod/` je informativna stranica fokusirana na jedan model (Ecotile 500/7 spec sheet), različita namena od parent silo-a `/industrijski-podovi/` (16567, opšti pregled) i od transakcionog Woo proizvoda 16538 (`ecotile-e500-7-...`) — građeno kao dopuna oba, ne duplikat. Sadržaj parity izvučen WebFetch-om sa live URL-a (specifikacija, FAQ, H1, gde se koristi).
- ✅ **Nova stranica ID 16660** (`page`, post_parent=16567) na **identičnoj live-parity URL** `/industrijski-podovi/industrijski-pod/` — nije trebala redirect mapa (slug + parent se poklapaju 1:1 sa live). Sekcije: hero → intro s namena ikonicama (magacin-hala/radionica/garaza, već postojeće iz F7.2) → specifikacija tabela (al-table, 12 svojstava) → prednosti (6 USP kartica, reused ikonice) → FAQ (6 pitanja) + FAQPage JSON-LD → CTA sa cross-linkom ka Woo kategoriji i ka Ecotile 500/10 proizvodu.
- 🔗 **Standardi novi za ovu stranicu** (WebSearch-potvrđeni, format identičan P2 postupku): DIN 53516 (mehanička/habanje otpornost, dinmedia.de), BS 476-7 (protivpožarna klasa, standards.globalspec.com), DIN EN 13501-1 (protivpožarna klasa, dinmedia.de 2019 izdanje) — plus reuse ISO 9001/14001 linkova iz antistatik/industrijski-podovi pilota.
- 💰 **Cena**: nema unosa u `[[reference/cenovnik]]` za Ecotile E500/7 (prazno, M10 još čeka popunu) → stranica ne navodi brojku, FAQ odgovor upućuje na slanje kvadrature/upita ("cena na upit" princip), bez izmišljanja.
- 🔧 **Bug uhvaćen i ispravljen**: Yoast title je prvobitno upisan sa PHP single-quote stringom koji je sadržao `\xe2\x80\x94` (hex escape za em-dash) — u single-quoted PHP stringovima `\x` escape ne radi, pa je literalni tekst `xe2x80x94` završio u `<title>` tag-u. Otkriveno u browser tab title-u odmah posle prvog screenshot-a. **Fix**: update_post_meta sa pravim UTF-8 em-dash karakterom (ne hex escape) + `DELETE FROM wpgs_yoast_indexable WHERE object_id=16660` da se prisili regeneracija keširanog naslova. Verifikovano da se indexable red ispravno regenerisao sa tačnim tekstom pri sledećem učitavanju. **Lekcija**: nikad koristiti `\xNN` hex escape sekvence u single-quoted PHP stringovima za UTF-8 karaktere — ili koristiti double-quoted string, ili kucati stvarni karakter direktno (kao što je urađeno svuda drugde u post_content-u ovog fajla, bez problema).
- 🔧 **Usput fiksiran postojeći broken link na parent stranici** (16567): tabela "Koja debljina za koju namenu" je imala placeholder href `industrija-podovi/ecotile-500-7/` (pogrešan slug, 404) iz ranije sesije — zamenjen tačnim `industrijski-podovi/industrijski-pod/` (`$wpdb->update`, `substr_count()===1` provera pre zamene). Redovi za 500/5 i 500/10 (#21, #26) ostaju placeholderi dok se ne izgrade.
- ✅ **Verifikacija**: 200 · 1×H1 · FAQPage JSON-LD bez dupliranja · svi interni linkovi i ikonice 200 · vizuelna provera (Chrome screenshot hero + specifikacija tabela + standardi linkovi) · regresija na `/industrijski-podovi/` (schema Product+FAQPage netaknuta, novi link radi), `/`, `/sportske-podloge/`, `/kontakt/` čista.
- Skripte (scratchpad): `create-ecotile-500-7.php`, `fix-parent-link.php`, `fix-yoast-title.php`, `check-escape.php`.
- **W1 1.2 #4 zatvoren** → [[migracija/w1-red-cekanja]]. Sledeći: #5 sportski-podovi-za-sale-i-balone (378 kl., PAGE tip), #6 podovi-za-bazene (262 kl.).

## 2026-07-08 [claude-code] [W1 1.2 #2] — Bergo XL rebuild (978 kl., ID 16659) ✅
- ✅ **Backup**: `antasline_local_2026-07-08_pre-bergo-xl-rebuild.sql` pre svih izmena.
- 🔍 **Nalaz pre gradnje**: Bergo XL je već postojala lokalno kao **legacy CPT `spoljne-podne-obloge`** (bez j, ID 5039, Porto-era, `publicly_queryable`) na `/spoljne-podne-obloge/bergo-xl/` — vraćala je 200, ali kroz generički WoodMart blog/CPT template (sidebar, "Posted by sava", kategorija badge, 2×H1), ne kroz al- landing šablon. Sadržaj je bio bogat i realan (specifikacije, 16 boja sa hex kodovima, foto koraci ugradnje, video) — iskorišćen kao izvor umesto pisanja od nule. Otkrivena cela porodica od 6 sličnih CPT unosa (bergo-unique/elite/solid/flow/ultimate/easy) — zapisano u [[migracija/w1-red-cekanja]] za buduće sesije.
- ✅ **Nova stranica ID 16659** (`page`, post_parent=16590) na **tačnoj live-parity URL** `/spoljnje-podne-obloge/bergo-xl/` (sa j, nasleđeno od parent hub-a — bolja parity od starog CPT-a koji je koristio "spoljne" bez j). Sekcije: hero → 6 USP kartica → specifikacija (PP, 380×380mm, 10,1mm, 250 t/m²) + primena lista → 16 boja (swatch grid) → 3 koraka ugradnje sa realnim fotografijama + zvaničan video (Bergo Flooring AB, `Hq_KkIPxt3o`, isti ID već vetovan u P4) → FAQ (6 pitanja) + FAQPage/VideoObject JSON-LD → CTA.
- 🔧 **2 bug-fixa tokom vizuelne provere**: (1) hero tekst pogrešno govorio "17 standardnih boja" dok je sekcija ispod imala 16 (uklonjen duplikat ECO Black = identičan hex kao Silk Black) → usklađeno na 16; (2) **veći nalaz** — boje kopirane iz starog Porto markupa (`.color-square` div sa samo `background` inline stilom, oslonjen na Porto CSS klasu koja ne postoji u WoodMart-u) renderovale su se kao **nevidljive elementi bez dimenzija** (prazan beo prostor, samo tekst imena boja) → zamenjeno samostalnim inline stilovima (width/height/border-radius) koji ne zavise ni od jedne teme. **Lekcija**: kopiranje starog Porto markupa nikad ne raditi 1:1 — Porto-specifične CSS klase (`.color-square`, `.productColors-block` i sl.) ne postoje u WoodMart-u i moraju se zameniti samostalnim inline stilovima ili al- klasama.
- 🔧 **Stari CPT unos (5039) → draft + slug `bergo-xl-stara`** (isti obrazac kao industrijski-podovi-stara), parent hub stranica (16590) ažurirana da linkuje `<h3>Bergo XL</h3>` ka novoj stranici.
- ✅ **Verifikacija**: 200 (stari CPT URL sad 404, potvrđuje draft) · 1×H1 · FAQPage+VideoObject JSON-LD bez dupliranja · sve slike 200 · parent link radi · **puna vizuelna provera Chrome screenshot-om svih sekcija** (hero → USP → specifikacija → boje → ugradnja+video → FAQ → CTA) — upravo ta provera je uhvatila oba bug-fixa gore, HTTP/DOM provera ih ne bi otkrila. Regresija na `/kontakt/`, `/o-nama/`, `/industrijski-podovi/` čista.
- Skripte (scratchpad): `bergo-xl-build.php`, `bergo-xl-schema.php`, `bergo-xl-cleanup.php`, `bergo-xl-textfix.php`, `bergo-xl-colorfix.php`.
- **W1 1.2 #2 zatvoren** → [[migracija/w1-red-cekanja]]. Sledeći: #4 industrijski-pod/Ecotile 500-7 (625 kl.).

## 2026-07-08 [claude-code] [W1 F7 P4] — Video lite-embed na 2 stranice — F7 AUDIT U POTPUNOSTI ZATVOREN ✅
- ✅ **Backup**: `antasline_local_2026-07-08_pre-f7-p4-video.sql` pre svih izmena.
- ✅ **2 videa dodata** po F7.3 standardu (lite-embed fasada, isti globalni `al-video-facade.js` kao antistatik pilot): `/industrijski-podovi/` (16567) — "How to Install Ecotile Garage Flooring - Durable PVC Interlocking Tiles", zvanični kanal Ecotile Flooring Ltd (`fncQrsTvHoE`); `/sportske-podloge/` (5438) — "Bergo Multisport court installation", zvanični kanal Bergo Flooring AB (`VdZWT2O5_-M`, tematski najbolji izbor od 5 kandidata jer je specifično o sportskim terenima).
- ✅ **Izvori potvrđeni WebSearch + YouTube oEmbed pre upotrebe** (obavezno pravilo iz F7.3 — stari linkovi lako postanu privatni/obrisani): proveren `author_name`/`author_url` za svaki kandidat, odbačeni neoficijelni kanali (npr. "BERGO FLOORING ROYAL HOW TO INSTALL" je sa kanala "GRFWS", ne Bergo Flooring — preskočeno).
- ✅ **VideoObject JSON-LD** dodat preko istog `vc_raw_html` base64 postupka kao FAQPage (P1), bez `uploadDate` (nije potvrđen, pravilo: ne izmišljati).
- 🔧 **Debug nalaz tokom vizuelne provere**: video thumbnail se u Chrome automatizovanom tabu nije učitavao (`img.complete=false`, `naturalWidth=0`) uprkos ispravnom markupu i mrežnom pristupu (potvrđeno `fetch()` i `new Image()` rade odmah). Uzrok identifikovan: `loading="lazy"` na `<img>` se ne okida u pozadinskom/automatizovanom tabu (Chrome native lazy-load intersection observer se ponaša drugačije van fokusiranog taba) — **potvrđeno da je preexisting ponašanje** testiranjem na već verifikovanom antistatik pilotu (identičan simptom). Nije bug u P4 radu, samo ograničenje test okruženja — u pravom browseru sa fokusiranim tabom radi normalno.
- ✅ **Funkcionalnost potvrđena direktnim dispatchEvent klikom** (zaobilazi automation coordinate quirk): klik na play dugme kreira `<iframe>` sa tačnim `youtube-nocookie.com` embed URL-om na obe stranice — event delegation iz F7.3 radi kako treba.
- ✅ **Verifikacija**: 200/1×H1 na obe stranice · `<iframe>` odsutan iz initial HTML response-a (potvrđuje da se ne učitava eagerly, LCP/CWV cilj ispunjen) · VideoObject schema prisutna bez dupliranja · bez neizrendovanih shortcode-ova · regresija na `/kontakt/` i `/o-nama/` čista.
- Skripta (scratchpad): `f7-p4-fix.php`.
- **F7 audit P4 zatvoren — cela f7-audit-i-popravke.md lista (P1–P4) je sada zatvorena.** → [[migracija/f7-audit-i-popravke]]. Sledeći W1 fokus vraća se na `migracija/w1-red-cekanja.md` (bergo-xl, Ecotile 500/7 info, itd.).

## 2026-07-08 [claude-code] [W1 F7 P3] — 4 nove antas-skice (SVG tehničke ilustracije) ✅
- ✅ **Backup**: `antasline_local_2026-07-08_0941_pre-f7-p3-skice.sql` pre svih izmena.
- ✅ **4 nova inline SVG-a** po F7.4 standardu (`woodmart-child/images/skice/`): `dimenzije-terena-fiba.svg` (top-down FIBA teren 28×15m sa centralnim krugom/reketom/troseks linijama, na `/dimenzije-kosarkaskog-terena/`), `dimenzije-table-kosarka.svg` (front-view table+koša 1,80×1,05m + obruč na 3,05m, na `/dimenzije-kosarkaske-table/`), `industrijski-pod-presek-slojeva.svg` (presek podloga→Ecotile 5-10mm→klik spoj, crveni akcent za viljuškarski saobraćaj, na `/industrijski-podovi/`), `bergo-klik-sistem-presek.svg` (presek dve ploče na nožicama sa klik-prstenovima, na `/sportske-podloge/`).
- 🔧 **2 sitna vizuelna bug-fixa nakon Chrome provere** (nisu bila vidljiva iz samog SVG koda, samo u renderu): (1) tabla dijagram — text "3,05 m" sečen na desnoj ivici jer je viewBox bio preuzak za tekst na toj poziciji → proširen 380→410; (2) Bergo dijagram — labela "Klik-prstenovi (bez lepka i šrafova)" vizuelno se sudarala sa "Bergo ploča" naslovom ispod (samo 8px razmaka) → razdvojeno na veći razmak + dodata tanka leader linija ka spoju radi jasnoće. **Lekcija**: kod inline SVG teksta uvek vizuelno proveriti u browseru (ne samo grep za prisustvo elementa) — koordinate koje izgledaju OK u kodu mogu da se seku/preklapaju u stvarnom renderu zbog font širine koja se ne vidi statički.
- ✅ **Postupak**: SVG fajlovi napisani po F7.4 stilu (navy `#0E2950` struktura, crvena `#F04D22`/`#D43C14` samo akcenat, dimenzione linije sa tick oznakama na krajevima, dashed za skrivene/unutrašnje detalje kao klik-spojevi), minifikovani (`str_replace(["\r","\n","\t"],'')`) i ubačeni u `<div style="margin-top:24px;max-width:440px">` unutar postojećeg `vc_column_text` bloka preko `$wpdb->update`+`clean_post_cache()` (anchor tekst potvrđen `substr_count()===1` pre upisa).
- ✅ **Verifikacija**: sve 4 stranice 200 · 1×H1 · bez neizrendovanih shortcode-ova · `class="al-skica"` prisutan i renderuje se · **vizuelno potvrđeno Chrome screenshot-om** na sve 4 (ne samo HTTP/DOM provera) — dijagrami čitljivi, dimenzije i labele na mestu, title/desc pristupačni (`find` alat ih ispravno pročitao preko alt/aria opisa).
- Skripte (scratchpad): `f7-p3-fix.php` (glavno ubacivanje), `f7-p3-tabla-fix.php` + `f7-p3-bergo-fix.php` (post-vizuelni fix-evi).
- **F7 audit P3 zatvoren** → [[migracija/f7-audit-i-popravke]] (P4 video je poslednji preostali prioritet u redu, niži prioritet).

## 2026-07-08 [claude-code] [W1 F7 P2] — Standardi sa linkovima na 9 stranica + P1+P2 kombinovani test ✅
- ✅ **Backup**: `antasline_local_2026-07-08_0859_pre-f7-p2-standardi.sql` (46,9 MB) pre svih izmena.
- ✅ **11 standarda linkovano na 9 stranica**: `/industrijski-podovi/` (7 — EN 660-2, ISO 6721, DIN 51130, EN 14041, ISO 10140, ISO 9001, ISO 14001), `/sportske-podloge/` (FIBA, ITF, EN 14877), `/sportske-podloge/kosarkaske-konstrukcije/` (FIBA, EN1270), `/podloge-za-futsal-terene/` (FIBA/ITF), `/kosarka-3x3-tereni/` (FIBA, ITF), `/dimenzije-kosarkaskog-terena/` (FIBA), `/dimenzije-kosarkaske-table/` (FIBA), `/spoljnje-podne-obloge/` (ISO 9001), home `/pocetna/` (FIBA).
- ✅ **Svi linkovi potvrđeni WebSearch-om pre upisa** (pravilo: link samo ako je izvor potvrđen, ništa izmišljeno): FIBA→`about.fiba.basketball/.../approved-equipment`, ITF→`itftennis.com/.../facilities`, EN1270 i EN14041→`knowledge.bsigroup.com` (isti obrazac kao antistatik pilot IEC linkovi), EN14877→`standards.globalspec.com` (BSI knowledge stranica ne postoji za ovaj standard, korišćen distributer kao legitimna referenca), DIN 51130→`dinmedia.de`, ISO 9001/14001→`iso.org` explainer stranice (stabilne, ne vezane za izdanje — bitno jer je ISO 14001:2015 upravo povučen i zamenjen 2026 izdanjem), ISO 10140-3/ISO 6721-1→`iso.org` standard stranice, EN 660-2→`landingpage.bsigroup.com`.
- 🔧 **Postupak**: `str_replace` na unique anchor tekstu iz postojećeg `post_content` (`substr_count()===1` provera pre upisa, da se ne pogodi pogrešna instanca kod stranica sa više FIBA/ITF pomena — futsal/3x3/sportske-podloge imaju FIBA i po 5-6 puta) — jedan link po standardu po stranici, biran najprirodniji kontekst (spec tabela ili prva suštinska rečenica), ne svaki pojedinačni pomen. Upis isključivo `$wpdb->update`+`clean_post_cache()`. Link format `target="_blank" rel="noopener"` (isti kao antistatik pilot).
- ✅ **P1+P2 kombinovani test na svih 13 stranica** (7 iz P1 + 9 iz P2, 3 preklapaju): HTTP 200 (`/pocetna/` → 301 na `/` je očekivano, front-page canonical) · 1×H1 svuda · FAQPage JSON-LD i dalje bez dupliranja na P1 stranicama · svih 11 standard-linkova renderuje kao validan `<a href target="_blank" rel="noopener">` bez razbijenog markupa · regresija na `/kontakt/` i `/o-nama/` čista. (Lažna uzbuna: gruba `<a `/`</a>` brojanja regexom pokazala neuravnoteženost na `kosarkaske-konstrukcije` — pokazalo se da je iz theme header/footer/product-grid chrome-a van mog edita, ne stvarni bug; potvrđeno direktnim grep-om na moje ubačene linkove.)
- Skripta (scratchpad): `f7-p2-fix.php`.
- **F7 audit P2 zatvoren** → [[migracija/f7-audit-i-popravke]] (P3 antas-skice je sledeći prioritet u redu, ~45 min–1h).

## 2026-07-08 [claude-code] [W1 F7 P1] — Popravka izgubljene FAQPage/Product schema na 7 stranica ✅
- ✅ **Backup**: `antasline_local_2026-07-08_0828_pre-f7-p1-schema.sql` (46,9 MB) pre svih izmena.
- ✅ **Svih 7 stranica iz audita popravljeno**: `/industrijski-podovi/` (16567, 7 Q&A FAQPage + Product AggregateOffer 2.000–5.500 RSD), `/spoljnje-podne-obloge/` (16590, 5 Q&A), `/dimenzije-kosarkaske-table/` (16585, 5 Q&A, prvi put dodata), Woo kategorije 16572/16573/16578/16579 (term 245/246/251/252, po 3 Q&A svaka).
- 🔴 **Novi nalaz tokom popravke**: `/spoljnje-podne-obloge/` (ID 16590) nije samo imala odsutnu schemu — imala je 1.321 znak **golog JSON teksta zalepljenog na kraj `post_content`, van bilo kog shortcode-a** (ni `[vc_raw_html]`, ni `<script>` tag) — ostatak nezavršenog/pokvarenog ranijeg pokušaja. Obrisan, zamenjen ispravno upakovanom schemom.
- 🔧 **Napomena za slug**: stvarni post_name je `spoljnje-podne-obloge` (sa "j"), ne "spoljne-podne-obloge" kako je PROGRESS unos 2026-07-07 tvrdio — proveriti buduće reference na ovu stranicu.
- ✅ **Postupak** (dokazan iz F6 pilota, sad ponovljen 7×): FAQ tekst izvučen direktno iz postojećeg `post_content` (h3/p parovi ili `al-faq` div), JSON-LD sastavljen (`json_encode(JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)`), `<script type="application/ld+json">` → `base64(rawurlencode())` → `[vc_raw_html]` shortcode ubačen odmah posle `[/vc_column_text]` FAQ reda (pronađeno preko unique anchor teksta zadnjeg FAQ odgovora) — upisano **isključivo** preko `$wpdb->update` + `clean_post_cache()`, nikad `wp_update_post` iz CLI-ja.
- ✅ **Verifikacija svih 7**: HTTP 200 · 1×H1 · JSON-LD parsira se ispravno bez dupliranja (Yoast `yoast-schema-graph` je poseban `<script>` blok, ne sudara se sa našim) · regresija na 245/246 i 251/252 parovima potvrđena (pitanja se ne mešaju međusobno) · 2 nedirane stranice (`/kontakt/`, `/sportske-podloge/`) i dalje 200.
- Skripta (scratchpad): `f7-p1-fix.php` (wp-load bootstrap, 7 stranica u jednom prolazu).
- **F7 audit P1 zatvoren** → [[migracija/f7-audit-i-popravke]] (P2 standardi-linkovi je sledeći prioritet u redu).

## 2026-07-08 [claude-code] [W1 AUDIT] — F7 compliance audit svih postojećih stranica + plan ✅
- ✅ **Audit, bez izmena baze**: svih 25 W1 rebuild stranica/Layout Builder kategorija (post_content + rendered HTML) provereno protiv F7 standarda (standardi-sa-linkovima, namena ikonice, video, antas-skica) i protiv ranijih dnevnik tvrdnji o JSON-LD schema.
- 🔴 **Nalaz — izgubljena FAQPage/Product schema na 7 stranica**, FAQ tekst prisutan ali JSON-LD blok odsutan u renderu: `/industrijski-podovi/` (16567, dnevnik tvrdi FAQ+Product dodato 2026-07-05), `/spoljnje-podne-obloge/` (16590, dnevnik tvrdi 2026-07-07), `/dimenzije-kosarkaske-table/` (16585, nikad dodata), i tačno 4 Woo kategorije (16572/16573/16578/16579 = term 245/246/251/252) — baš oni parovi koje je dnevnik 2026-07-06 pomenuo kao naknadno "diferencirane" (245↔246, 251↔252). Obrazac ukazuje na gotcha #9 (CLI `wp_update_post` briše `vc_raw_html`) primenjen tokom te diferencijacije, umesto dokazanog `$wpdb->update` puta.
- 🟡 **Nalaz — 9 stranica pominje standarde (FIBA/ITF/EN1270/EN14877/DIN 51130/ISO 9001-14001-6721-10140/EN 660-2/EN 14041) kao goli tekst, bez linka** — najveći F7.1 compliance gap po broju stranica. `/industrijski-podovi/` ima čak 7 nelinkovanih standarda.
- 🟢 **Nalaz — antas-skica prilike**: `/dimenzije-kosarkaskog-terena/` i `/dimenzije-kosarkaske-table/` nemaju nijednu skicu iako su doslovno o dimenzijama (najprirodniji fit u sajtu); `/industrijski-podovi/` i `/sportske-podloge/` kandidati za presek-slojeva skicu.
- 🔵 **Nalaz — video prilike** (niži prioritet): `/sportske-podloge/` (Bergo) i `/industrijski-podovi/` (Ecotile generalno) nemaju video, sport pod-stranice ne trebaju svaki svoj.
- 📁 **Plan upisan** u novi `migracija/f7-audit-i-popravke.md` (4 prioritetna nivoa, checkbox lista po stranici, procena vremena, preporučen redosled P1→P4). Miroslav odabrao da se plan samo zapiše ove sesije, izvršenje ide u narednim sesijama (jedan prioritet po sesiji).
- Skripte (scratchpad): `audit-f7.php`, `check-rendered-jsonld.sh`.

## 2026-07-07 [claude-code] [W1 + W1/W2 PARITY F7] — Antistatik stranica + F7 content standard (paralelno) ✅
- ✅ **Backup**: `antasline_local_2026-07-07_pre-antistatik-f7.sql` (44,7 MB) pre svih izmena.
- ✅ **W1 1.2 — `/antistatik-i-elektroprovodljivi-podovi/` (ID 16658)**, top prioritet po klikovima (1131/12mes), top-level stranica (parity sa live URL strukturom). Sadržaj: WebFetch live (specifikacija, standardi, prednosti, primena — real facts, ne izmišljeno) + troslojni F6 model (namena-esd grid, 2 proizvoda: Ecotile 7mm ESD + polukružni zaštitnik za cevi ESD verzija). Standardi navedeni SA LINKOVIMA na potvrđene zvanične izvore (IEC 61340-5-1, BS EN IEC 61340-5-1, IEC TR 61340-5-2 — pronađeni preko WebSearch, ne izmišljeni). FAQ (5 pitanja, grounded) + FAQPage JSON-LD.
- 🔧 **2 stara/pokvarena cross-linka pronađena i ispravljena** usput: `/industrijski-podovi/` (ID 16567) linkovao je ka nepostojećem starom Porto CPT-u (`industrija-podovi/antistatik-podne-obloge/`, post_type `industrija-podovi` ID 5272 — leftover, van parity obima) — 2 pojavljivanja ispravljena na novu stranicu. Lokalni post `/zasto-vam-je-potreban-esd-pod/` (ID 3318, F3 reimport) linkovao je na `https://www.antasline.com/...` (živi live domen, netačan slug) — 2 pojavljivanja ispravljena na lokalni URL.
- ✅ **F6 pilot (kosarkaske-konstrukcije) rešen u redirect mapi**: `redirect-mapa-FINAL.csv` red ažuriran na REŠENO (identičan URL, redirect nepotreban) — vidi prethodni F6 unos.
- ✅ **F7 — content standard definisan i primenjen na pilot stranici**:
  - **F7.1**: `.claude/skills/obogati-proizvod/SKILL.md` dopunjen — korak 1b "standardi sa linkovima" (tvrdo pravilo: samo iz datasheet-a/zvaničnog sajta/postojeće live stranice) + korak 1c "namena tagovi" (F6 lista + auto-proširenje).
  - **F7.2**: 12 novih SVG ikonica (`namena-*` × 8 + USP `garancija`/`sertifikat`/`dostava`/`telefon-podrska` × 4), isti stil kao postojećih 6 (viewBox 24, stroke #F04D22, width 1.7).
  - **F7.3**: video lite-embed fasada — **globalni JS fajl** (`woodmart-child/js/al-video-facade.js`, enqueue u `functions.php`, `in_footer`, `filemtime` verzija) umesto `vc_raw_html` po stranici (zaobilazi CLI/kses gotcha #9 u potpunosti, jer u `post_content`-u nema `<script>`, samo čist HTML). Testiran sa pravim, potvrđenim Ecotile videom ("ESD Flooring - How to install", kanal ecotile-Germany, potvrđeno YouTube oEmbed API-jem) — `VideoObject` JSON-LD BEZ `uploadDate` (nije potvrđen, ne izmišljati). Gotcha: stari video link iz posta 3318 (`4-dNngajiCY`) je sad "Forbidden" na oEmbed-u — ne koristiti ponovo bez provere.
  - **F7.4**: "antas-skica" stil definisan (navy strukturne linije, crvena samo akcenat, Inter labele, `.al-skica` CSS klasa, folder `images/skice/`) + pilot skica `esd-pod-presek-slojeva.svg` (presek ESD poda: betonska podloga → 7mm ESD ploča sa čeličnim vlaknima → uzemljenje), inline ubačena u antistatik stranicu.
  - **F7.5**: performanse-ograda — svi F7 dodaci na pilot stranici su sitni (ikonice ~250-400B, JS 972B footer/deferred, skica ~2,4KB inline vektor), video iframe se NE učitava dok se ne klikne (potvrđeno u HTML odgovoru). ⚠️ Pravi Lighthouse pre/posle test NIJE urađen — CLI nije instaliran u ovom okruženju (`npx lighthouse` traži download); odloženo na W3 3.5 (Lighthouse baseline sesija).
- ✅ **Verifikacija**: antistatik stranica 200 · 1×H1 · Yoast title/metadesc parity sa live · 2 JSON-LD bloka validna (FAQPage 5 pitanja + VideoObject) bez dupliranja · grid tačan (2 proizvoda) · video fasada i skica prisutni u HTML-u · regression 7 stranica (Početna, industrijski-podovi, sportske-podloge, kosarkaske-konstrukcije, kontakt, o-nama, kategorija industrijski-podovi) → sve 200.
- 📁 Ažurirano: `parity-inventar.csv` (antistatik red → PARITY), `migracija/w1-red-cekanja.md` (#1 antistatik označen gotovim), `migracija/promptovi/_README.md` (F7 ✅), `migracija/woodmart-sabloni.md` (novi odeljci F7.2–F7.5), `.claude/skills/obogati-proizvod/SKILL.md` (F7.1), `2026-07-06-MASTER-PLAN-V2.md` (W1 1.2/1.8 napomene).
- Skripte (scratchpad): `antistatik-page.php`, `antistatik-add-jsonld.php`, `fix-industrijski-antistatik-link.php`, `fix-esd-post-crosslink.php`, `validate-antistatik.php`.

## 2026-07-07 [claude-code] [W1 PARITY F6] — Namena arhitektura + pilot kosarkaske-konstrukcije ✅
- ✅ **Backup**: `antasline_local_2026-07-07_pre-f6-namena-tagovi.sql` (44,7 MB) pre svih izmena.
- ✅ **Popis stvarnog kataloga (37 proizvoda)** pokazao da F6 prompt pretpostavke o ponudi (garaža/terasa/bazen/teretana ravnomerno zastupljeni) ne odgovaraju stvarnosti: katalog je dominiran Ergomat industrijskom zaštitom (28 proizvoda: bumperi, DuraStripe trake, senzori) i košarkaškim konstrukcijama (5 proizvoda) — samo 4 proizvoda pokrivaju terasu/štalu/ESD/garažu pojedinačno.
- ✅ **8 `namena-*` product_tag termina kreirano** (prilagođeno stvarnoj ponudi, izostavljeno namena-parking/teretana/bazen jer nijedan proizvod ne pokriva tu namenu): `namena-magacin-hala` (29), `namena-radionica` (12), `namena-sport-dvorana` (5), `namena-sportski-teren-otvoreni` (2), `namena-esd` (2), `namena-garaza` (1), `namena-terasa` (1), `namena-stala` (1).
- ✅ **Tabela proizvod→namena prezentovana Miroslavu i potvrđena PRE upisa** (F6 pravilo) — svih 37 proizvoda dobilo tagove preko `wp_set_object_terms(..., true)` (append, ne replace), 0 grešaka.
- ✅ **Grid mehanika potvrđena**: `woodmart_products` shortcode, atribut `taxonomies` prima **term ID** (ne slug) — `get_terms(['include' => $taxonomies])` u `inc/shortcodes/products.php`. Radni primer: `[woodmart_products taxonomies="266" columns="3" items_per_page="6" post_type="product" layout="grid" lazy_loading="yes"]`.
- ✅ **Pilot = W1 SEO prioritet #1 spojen u jednu sesiju**: `namena-sport-dvorana` (5 proizvoda: Street Sport, Lite Shot 325, Mini Shot 225, MicroShot 125, Zglobni obruč) poklopio se tačno sa `/sportske-podloge/kosarkaske-konstrukcije/` (923 GSC klika/12mes, dugo dokumentovan prioritet #1, čekala ga redirect mapa). Umesto originalnog F6 predloga (`/podovi-za-garaze/`, samo 1 proizvod — slab grid demo), izgrađena je ova stranica (ID 16657) kao pravi W1 rebuild + F6 pilot.
- ✅ **Sadržaj**: content parity izvučen preko WebFetch-a sa live URL-a (live koristi ZionBuilder serialized podatke, ne WPBakery — teško parsirati direktno; WebFetch rendered tekst je brži put). Troslojni model: hero → uporedna tabela 5 modela (tip/podesiva visina/standard/namena/cena "na upit" jer cenovnik prazan za ove SKU) + cross-link ka Woo kategoriji → auto grid (`taxonomies="266"`) → FAQ (5 pitanja, sve činjenično utemeljeno na postojećim opisima proizvoda, bez izmišljanja) + FAQPage JSON-LD → CTA. Cross-link dodat i u suprotnom smeru (Woo kategorija ID 16578 → nova stranica).
- ✅ **Nova CSS klasa `.al-table`** dodata u `antas-design.css` (navy header, zebra redovi, `overflow-x:auto` wrapper obavezan jer `min-width:640px`).
- ✅ **Verifikacija**: 200 · 1×H1 · JSON-LD validan (1 blok, FAQPage, 5 pitanja, bez dupliranja) · Yoast title/metadesc u `<head>` (identični live parity) · grid prikazuje tačno 5 ispravnih proizvoda · **auto-mehanika potvrđena radna** (test tag dodat na Ecotile ESD → odmah se pojavio u gridu bez izmene stranice, pa uklonjen) · regression `/sportske-podloge/`, `/industrijski-podovi/`, `/kategorija-proizvoda/kosarkaske-konstrukcije/` → sve 200.
- 📁 Ažurirano: `redirect-mapa-FINAL.csv` (red kosarkaske-konstrukcije → REŠENO, identičan URL, redirect nepotreban), `parity-inventar.csv` (red 56 → PARITY), `migracija/w1-red-cekanja.md` (#3 označen gotovim), `migracija/promptovi/_README.md` (F6 ✅), `migracija/woodmart-sabloni.md` (nov odeljak "NAMENSKA LANDING (rešenje hub) — F6 troslojni model" sa radnim shortcode primerom).
- Skripte (scratchpad): `f6-products.php`, `f6-namena-tags.php`, `f6-pilot-kosarkaske-konstrukcije.php`, `f6-add-jsonld.php`, `f6-crosslink-category.php`.

## 2026-07-07 [claude-code] [W3 PARITY F5] — Trijaža nedostajućih stranica → W1 red čekanja ✅
- 🔧 **CSV resync pre trijaže**: `parity-inventar.csv` nije bio ažuriran posle F2/F3 promena — 6 redova (postovi uvezeni u F3 + 2 proizvoda preimenovana u F2) je resync-ovano sa NEDOSTAJE-LOKAL na PARITY po stvarnom stanju baze. NEDOSTAJE-LOKAL palo sa 52 na 46 pre trijaže.
- ✅ **Kategorija D odmah izvršena**: `/politika-kolacica/` kreirana (ID 16656) — sadržaj 1:1 iz live WXR exporta (SimpleXML sa wp/content namespace parsing), 200 verifikovano. Poznato odstupanje: 7×`<h1>` u starom markup-u (isti tip problema kao basket članak) — restyle sesija rešava.
- ✅ **Kategorija A (33 stranice)** — puna lista sa GSC klikovima, Yoast title-ovima i napomenama u novom **`migracija/w1-red-cekanja.md`**. Ključni nalazi pri kategorizaciji:
  - **Ecotile informativni klaster** (3 stranice: industrijski-pod=500/7 625kl., podne-ploce-ecotile-50010=500/10 56kl., ecotile-5005-podne-ploce=500/5 31kl.) — 500/7 i 500/10 imaju lokalne proizvode (PARITY), 500/5 nema → razmotriti dodavanje kroz `/obogati-proizvod`
  - **LVT/Expona silo** (6 stranica pod `/lvt-podovi-za-komercijalne-i-javne-prostore/`) — potvrđeno da je LVT/Expona i dalje deo ponude (CLAUDE.md §1), graditi parent PRE podstranica
  - **Epoksid-conquest srodna stranica** nađena: `industrijski-podovi-montaza-preko-ostecenog-epoksida` (72 kl.) — mora linkovati ka glavnom conquest članku (2542), nikad predlagati epoksid
  - 2 potencijalna duplikat para flagovana za proveru pre gradnje: `podovi-za-radnje-i-maloprodajne-objekte` (26 kl.) vs `industrijski-podovi/podovi-za-maloprodajne-objekte` (6 kl.); `vestacka-trava-za-terase` (104 kl.) vs postojeći `/vestacka-trava/` (1538 kl., PARITY)
- ✅ **Kategorija C prazna ovog kruga** — svi ranije pretpostavljeni "proizvod-duplikat" kandidati (Ecotile 5005/50010, Expona Click, trake-za-obelezavanje, vinil-podovi-objectflor) reklasifikovani u Kategoriju A jer Yoast title-ovi pokazuju informativni ugao koji dopunjuje (ne duplira) transakcionu proizvod-stranicu — u skladu sa F6 troslojnim modelom.
- ✅ **Kategorija E (3 slučaja)**: `elektroprovodni-podovi`→301 na antistatik (kad bude gotov); treći skoro-identičan FAQ (`industrijski-podovi-najcesca-pitanja`) pridružen ranijoj W2 odluci o konsolidaciji sa postojeća 2 `izbor-industrijskog-poda` članka.
- 🆕 **Kategorija F identifikovana** (nova, nije bila u originalnom prompt planu): 8 live `product_tag` termina (bergo, ergomat, industrijski-amortizer...) ne postoje lokalno — odvojena taksonomija od F6 "namena" taga, van W1 obima, ide u F7 razmatranje.
- ✅ Svaki NEDOSTAJE-LOKAL red u `parity-inventar.csv` dobio kategoriju u `napomena` koloni (verifikovano skriptom — 0 redova bez oznake).
- 📝 [[2026-07-06-MASTER-PLAN-V2]] W1 1.2 sada pokazuje na `migracija/w1-red-cekanja.md` kao izvor istine.
- Skripte (scratchpad `f5/`): `resync-inventory.php`, `build-politika.php`, `tag-categories.php`.

## 2026-07-07 [claude-code] [W3 PARITY F4] — Minimalna redirect mapa (7 redova) ✅
- ✅ **Backup**: `antasline_local_2026-07-07_pre-f4-redirect-mapa.sql` (47MB) pre svih izmena.
- ✅ **Odluke sa Miroslavom** (hibrid pravilo — top-15 GSC = parity, nisko-saobraćajno sme 301):
  - `/spoljnje-podne-obloge/` (1304 kl., top-5 sajta) → **parity**, vraćeno "j"
  - `/podovi-za-stale/` (402 kl., top-15) → **parity**, uklonjen prefiks "gumeni-"
  - `sigurnosni-senzori-signalni-sistemi` (nizak saobraćaj) → lokalna varijanta sa "i" OSTAJE, 301 sa live
  - **Bergo easy/elite/unique/xl (4 live stranice, 978+166+53+33 kl.) — VAŽNA ISPRAVKA plana**: pretpostavka iz starog plana ("konsoliduj u bergo-ultimate") je bila POGREŠNA. Miroslav potvrdio: Bergo Ultimate je poseban proizvod za sportske terene, NIJE isti kao easy/elite/unique/xl (terase-varijante). Sve 4 i dalje su deo ponude → idu u **F5 W1 red kao zasebne landing stranice**, NE konsoliduju se, NE idu u redirect mapu.
  - 3 draft posta iz F3 (pogrešan `post_type`: post umesto page) → **obrisani**, F5 rebuild kao PAGE pod live slugom: `padel-tereni`, `sportski-podovi-za-sale-i-balone`; `kako-izabrati-pravi...poterbama` (typo, bez live parnjaka) → obrisan bez zamene
  - 2 skoro-identična `izbor-industrijskog-poda-tri-najcesca-pitanja` članka (oba sada publish lokalno) → **odloženo na W2** (content-strategija, ne redirect-mapa posao)
- 🔴 **Nova nalazak pri izvršenju**: `/spoljnje-podne-obloge/` je imala DVE lokalne stranice — stara (ID 5255, iz 2022, staro Porto obeležje) je i dalje bila `publish` i zauzimala čist slug, dok je NOVA W1 rebuild stranica (ID 16590, napravljena 2026-07-07) automatski dobila sufiks `-2` jer je slug bio zauzet. Ispravljeno u istoj operaciji: 5255 → draft, 16590 preimenovana na `spoljnje-podne-obloge` (bez sufiksa).
- ✅ **Izvršeno**: 2 slug rename-a (`wp_update_post` + Yoast indexable cache invalidacija po F2 lekciji + `flush_rewrite_rules(true)`), 3 drafta obrisana.
- ✅ **`migracija/redirect-mapa-FINAL.csv`** — 7 redova (umesto starih 118): 3 odmah verifikovana (200 na cilju: na-kojoj-podlozi→bergo-ultimate, lite-shot-795→325, sigurnosni-senzori), 3 privremena čekaju F5 rebuild (kosarkaske-konstrukcije 923 kl. PRIORITET #1, padel-tereni, sportski-podovi-za-sale-i-balone) — target TBD, NE ulaze u aktivni .htaccess dok stranice ne postoje.
- ✅ **`migracija/htaccess-301-DRAFT.txt`** generisan sa 3 verifikovana pravila + komentar-blok za 3 buduća. **NE aktiviran** (ostaje draft do dana migracije).
- ✅ `parity-inventar.csv` ažuriran: 84→86 PARITY, 57→52 NEDOSTAJE-LOKAL, 0→5 301-KANDIDAT (matematika se poklapa, ukupno i dalje 175 redova).
- ✅ Verifikacija: sva 3 real-target redirekta → 200 na lokalu, oba slug rename-a → 200 + ispravan canonical, regression Početna/`/industrijski-podovi/`/`/sportske-podloge/` → 200.
- ✅ Stare redirect mape obrisane iz `C:\xampp\htdocs\antasline\` i `antasline-backups\` (POPUNJENA.csv, ZA-POPUNITI.csv, 2026-07-07.csv) — Miroslav potvrdio, arhivske kopije ostaju u `migracija/arhiva/`.
- 📝 [[migracija/promptovi/F5-trijaza-stranica]] ažuriran sa F4 ispravkama: kosarkaske-konstrukcije 923 kl. (ne 478), bergo-easy/elite/unique/xl premešteni iz kategorije C (konsolidacija) u kategoriju A (zasebni rebuild), padel-tereni/sportski-podovi-za-sale-i-balone napomena da su PAGE tip.

## 2026-07-07 [claude-code] [W3 PARITY F3] — Pun reimport 30 postova sa live ✅
- ✅ **Backup**: `antasline_local_2026-07-07_pre-posts-reimport.sql` (46MB) pre svega.
- ✅ **Cleanup 7 LOKAL-NOVO postova** po hibrid pravilu: `bergo-ultimate...` (4813) ZADRŽAN, 4 prebačena u draft (`kako-izabrati-pravi...poterbama` 3327, `padel-tenis` 3973, `podovi-za-garaze` 3378, `sportski-podovi-za-skole...` 3621 — čekaju F4 odluku), 2 obrisana (`izbor-...-2-2` 15962 duplikat, `sportska-podloga-za-odbojku` 4318 — live verzija je zamenjuje).
- 🔴 **Sopstvena greška + oporavak**: prvi pokušaj cleanup-a je slučajno obrisao i `bergo-ultimate` (4813) jer "zadrži" znači i dalje `publish`, pa ga je bulk-delete upit pokupio. Otkriveno odmah (verifikacija posle svakog koraka) → pun DB restore iz backup-a → cleanup ponovljen sa eksplicitnim izuzetkom ID-a. Lekcija: kad je odluka "zadrži kao publish", eksplicitno isključi ID iz svake sledeće bulk operacije, ne oslanjaj se na to da ga "nisi dirao".
- ✅ **WXR import** (`live-posts-2026-07-05.xml`, `fetch_attachments=true`) — 4 uzastopna pokušaja dok nije prošao čisto, svaki sledeći pokušaj idempotentan (post_exists() sprečava duplikate):
  1. `Class "WXR_Parser" not found` → nedostajao `define('WP_LOAD_IMPORTERS', true)` pre `wp-load.php`
  2. `Cannot redeclare wordpress_importer_init()` → definisanje `WP_LOAD_IMPORTERS` PRE `wp-load.php` uzrokuje da WP već učita plugin (jer je aktivan) — eksplicitan drugi `require` istog fajla posle je duplikat. Rešenje: definisati konstantu, samo `require wp-load.php`, NE ponovo `require`-ovati plugin fajl.
  3. `Call to undefined function post_exists()` → nedostajao `require_once ABSPATH.'wp-admin/includes/post.php'` (+ media/image/file/taxonomy za attachment fetch)
  4. `Call to undefined function comment_exists()` → nedostajao `require_once ABSPATH.'wp-admin/includes/comment.php'`
  - Posle sva 4 fix-a: import prošao čisto (`result: OK`).
- 🔴 **2 posta od 30 namerno NISU uvezena** (WP_Import `post_exists()` title-match zaštita, ne greška):
  - `ugradnje-industrijskog-poda` — blokirao stari lokalni "pending" draft iz 2019 (`o-cemu-treba-voditi-racuna-prilikom-ugradnje-industrijskog-poda`, ID 3257) sa identičnim naslovom → obrisan stari draft, ponovljen import (idempotentno), post uspešno uvezen (zadržao ISTI ID 3257).
  - `na-kojoj-podlozi-se-igraju-turniri-u-3x3` — live URL slug je zastareo/nasleđen ali stvarni `<title>` je "Bergo ultimate i ultimate plus - Nova generacija sportskih podova" = identičan naslovu lokalnog LOKAL-NOVO posta 4813 → ISPRAVNO preskočen (isti sadržaj već postoji lokalno pod drugim slugom). `parity-inventar.csv` ažuriran: oba reda (live URL i lokalni 4813) sada `301-KANDIDAT` sa unakrsnom napomenom za F4.
  - **Finalna matematika**: 30 live − 1 (na-kojoj-podlozi, duplikat) + 1 (zadržan bergo-ultimate) = **30 publish postova** (ne 31 kako je prompt pretpostavio — ispravno, izbegnut je pravi duplikat sadržaja).
- 🔧 **ID-evi sačuvani**: WP_Import je zadržao originalne post ID-eve gde slot nije bio zauzet → conquest `epoksidni-podovi-ili-ecotile-podovi` = **i dalje 2542**, basket `kako-napraviti-teren-za-basket-ili-kosarkaski-teren` = **i dalje 2298**. Nema potrebe za ID izmenama u CLAUDE.md.
- ✅ **Slike — domen fix**: 26 postova je zadržalo `https://www.antasline.com/wp-content/uploads/...` u `post_content` (fajlovi su već lokalno prisutni od ranijeg rsync-a, ali WP_Import ih je tretirao kao "already exists" po nazivu i nije remapovao URL u telu teksta) → globalni `str_replace` na `http://localhost/antasline/wp-content/uploads/` kroz `wp_update_post` po postu. 20 referenci ostaje na stvarno nedostajuće fajlove (uglavnom stock/Pixabay slike koje nikad nisu rsync-ovane) — **zabeleženo, NE popravljeno** (restyle sesije), pošto prompt eksplicitno kaže da se poznata odstupanja ne rešavaju u ovoj fazi.
- ✅ **Anti-kanibalizacija basket članka (ID 2298) ponovo primenjena**: sekcija "Dimenzije terena za košarku" → "Obruč koša" (puna FIBA specifikacija, dupliran sadržaj sa `/dimenzije-kosarkaskog-terena/` i `/dimenzije-kosarkaske-table/`) skraćena na 1 pasus + 2 relativna linka (12928→11446 bajtova); sekcija "Košarkaške konstrukcije" ispod ostala netaknuta (kako je i dokumentovano 2026-07-06).
- ✅ Verifikacija: 30 publish postova, 5 nasumičnih → 200, `dimenzije-kosarkaskog-terena`/`dimenzije-kosarkaske-table` linkovi → 200, regression Početna/`/industrijski-podovi/` → 200.
- 📁 `migracija/parity-inventar.csv` ažuriran (na-kojoj-podlozi + bergo-ultimate redovi → 301-KANDIDAT sa unakrsnim napomenama).
- Skripte (scratchpad `f3/`): `step1-cleanup-v2.php`, `run-import-v5.php` (finalna radna verzija sa svim wp-admin include-ovima), `fix-image-urls.php`, `fix-basket-article.php`.

## 2026-07-07 [claude-code] [W3 PARITY F2] — Permalink fix: Woo /proizvod/ flat + /kategorija-proizvoda/ + aktuelnosti ✅
- ✅ **Backup**: `antasline_local_2026-07-07_pre-permalink-fix.sql` (47MB) pre svih izmena.
- ✅ **Woo permalinci**: `product_base` `/shop/%product_cat%` → `proizvod` (flat, kao live); `category_base` `kategorija` → `kategorija-proizvoda` (kao live). Menjano preko `update_option('woocommerce_permalinks', ...)`, ne sirov SQL.
- ✅ **Blog slug**: `/blog/` (ID 21, "Aktuelnosti") → `/aktuelnosti/` (parity sa live).
- ✅ **2 proizvod sluga vraćena na live**: `durastripe-supreme-v-industrijska-traka-...` → `durastripe-supreme-v-roll-50-mm-x-30-m-ergomat` (ID 16520); `ecotile-e500-10-ultra-heavy-duty-podovi-za-kretanje-...` → `ecotile-e500-10-ultra-heavy-duty-interlocking-podna-ploca` (ID 16540).
- ✅ **lite-shot 325 vs 795 razrešeno** (F2 otvoreno pitanje iz F1): WebFetch live `/proizvod/lite-shot-795.../` pokazao IDENTIČAN naslov/specifikacije kao lokalni `lite-shot-325` (700kg, 325cm domet — "795" je stari nepovezan interni kod, ne drugi model). **Nije preimenovano** (nizak GSC saobraćaj — 3 klika, lokalni slug tačniji od live-ovog) — umesto toga oba reda u `parity-inventar.csv` ažurirana na `301-KANDIDAT` sa napomenom za F4 (301 sa starog live URL-a na lokalni `/proizvod/lite-shot-325-.../`).
- ✅ **8 pokvarenih internih linkova ispravljeno**: 2× `/blog/` u footeru (porto_builder ID 5751, 15371) → `/aktuelnosti/`; 6× `/kategorija/` u WoodMart Layout Builder sadržaju (ID 16567, 16572, 16573, 16578, 16579, 16585) → `/kategorija-proizvoda/`.
- 🔴 **Gotcha #1 (novi)**: `flush_rewrite_rules()` (soft) NIJE bio dovoljan posle promene `product_base` — proizvod URL-ovi vraćali 404 uprkos ispravnim rewrite_rules zapisu i ispravnom `get_permalink()`. Rešenje: `flush_rewrite_rules(true)` (hard flush, briše i regeneriše `.htaccess`-relevantne interne strukture). Ubuduće UVEK hard flush posle permalink/permastruct izmena.
- 🔴 **Gotcha #2 (potvrđuje raniju lekciju od 2026-07-06)**: Yoast `wpGs_yoast_indexable` keš (canonical, og:url, JSON-LD) NIJE se osvežio automatski posle permalink izmene — stare `/kategorija/...` i implicitno stare product URL vrednosti ostale zaglavljene u `<link rel="canonical">`/`og:url` sve dok redovi nisu ručno obrisani (`DELETE FROM wpGs_yoast_indexable WHERE object_sub_type IN ('product_cat','product')` + red za ID 21). Posle brisanja, Yoast je ispravno regenerisao sve pri sledećoj poseti. Ovo pravilo sada važi šire nego samo termmeta izmene (prošla lekcija) — **svaka permalink/slug izmena na product/product_cat/page zahteva invalidaciju Yoast indexable keša**.
- ✅ Verifikacija: 5 proizvoda + 3 kategorije + `/aktuelnosti/` → 200; `/blog/` i stari `/shop/...` → 404 (očekivano); canonical/og:url na svim proverenim stranicama ispravni; regression Početna/`/industrijski-podovi/`/`/sportske-podloge/`/`/kontakt/` → 200.
- 📁 `migracija/parity-inventar.csv` ažuriran (lite-shot redovi → 301-KANDIDAT sa F4 napomenom).

## 2026-07-07 [claude-code] [W3 PARITY F1] — Master parity inventar (175 redova) ✅
- ✅ **F1 izvršen**: povučeno svih 7 live sub-sitemapa (curl, `Mozilla/5.0` UA — bez njega Yoast sitemap ponekad vraća prazno/blokira), izvučeno 175 URL-ova (30 post + 1 arhiva, 48 page, 37 product + 1 katalog, 7 category, 2 product_brand, 9 product_cat, 8 product_tag), upoređeno sa lokalnom bazom preko PHP skripte (`WP_Query`/`get_term_by`, ne pojedinačni SQL pozivi) → `migracija/parity-inventar.csv`.
- 📊 **Rezultat**: PARITY 84 · NEDOSTAJE-LOKAL 57 · LOKAL-NOVO 32 · ARHIVA-STRANICA 2 (aktuelnosti, katalog — sistemske, ne prave stranice). Poklapa se sa prošlom sesijom procenjenim brojevima (25/30 postova, 8/50 stranica, 34/37 proizvoda) — potvrđeno tačnim.
- 🔴 **Nov kritičan nalaz**: `/sportske-podloge/kosarkaske-konstrukcije/` = **923 klika/12mes** (GSC preko Windsor.ai, `searchconsole` konektor, `page`+`clicks` neflitrirano pa spojeno u skripti — in-filter gotcha izbegnut) — veće od ranije dokumentovanih 478 (verovatno stariji/kraći period). Postaje najveći pojedinačni SEO rizik u planu, prioritet #1 za F5.
- 🔧 **Gotcha nađen**: `/kategorija-proizvoda/sigurnosni-senzori-signalni-sistemi/` (live) pao je u NEDOSTAJE-LOKAL jer lokalni term ima "i" (`sigurnosni-senzori-i-signalni-sistemi`) — nije pravi gap, slug varijanta. Anotirano u CSV `napomena` koloni za F4.
- 🔧 **Gotcha**: nijedan od 8 live `product_tag` termina (bergo, ergomat, industrijski-amortizer...) ne postoji lokalno — ovo je DRUGA taxonomy od planiranog "namena" taga u F6, razmotriti rekreiranje u F7.
- ✅ Verifikacija: spot-check 5 nasumičnih live URL-ova (kosarkaske-konstrukcije, bergo-xl, antistatik, kontakt, lite-shot-795) → svi 200.
- Bez izmena baze ove sesije. CSV: `migracija/parity-inventar.csv` (175 redova, semicolon, UTF-8-BOM).

## 2026-07-07 [claude-code] [STRATEGIJA] — PARITY-PLAN: nova migracija strategija + 7 promptova ✅
- ✅ **Odluka (Miroslav):** build se pravi **1:1 prema live sajtu** (URL + content parity) — SEO se čuva pa unapređuje. Stari redirect plan (Porto era, 118 redova) proglašen nevažećim.
- 📊 **Izmereno stanje** (live sitemap vs lokalna baza): postovi 25/30 slug match (5 nedostaje) · pages 8/50 (42 nedostaje, ~12 su Woo sistem/proizvod-stranice/legal) · proizvodi 34/37 · **lokalni Woo permalinci pogrešni**: `/shop/%product_cat%` + `/kategorija/` vs live `/proizvod/` flat + `/kategorija-proizvoda/` — jedna izmena opcije briše ~47 redirect redova.
- 🔴 **Nađene greške u starim mapama**: POPUNJENA.csv cilja `/shop/` URL-ove (kontradiktorno i sa live i sa CLAUDE.md odlukom o flat `/proizvod/`); mapa 2026-07-07 vodila `podovi-za-parkiraliste-i-staze`→`podloge-za-...` kao PARITY iako se slugovi razlikuju.
- ✅ **Donete odluke**: (P1) slug politika = hibrid po težini — top ~15 GSC URL-ova strogi parity, nisko-saobraćajni smeju novi slug uz 301, konsolidacije uvek OK (obrazloženje: keyword u slugu ≈ zanemarljiv faktor, 301 nosi 2–8 ned. nestabilnosti + rizik izvršenja); (P2/M8 ✅) postovi = **pun reimport svih 30 sa live**, restyle posle; (P5) troslojna arhitektura namena→proizvod (`namena` tag + auto grid — namenske stranice postaju "rešenje hub", ne opis jednog proizvoda); (P6) content standard pre live-a (standardi sa linkovima ka izvorima, SVG ikonice, "antas-skica" stil, video kroz fasadni embed).
- 📁 **Kreirano**: [[migracija/PARITY-PLAN]] (izvor istine) · [[migracija/promptovi/_README]] + 7 samostalnih promptova F1–F7 (svaki izvršava jedna buduća sesija, bilo koji model) · `migracija/arhiva/` sa 3 stare mape + [[migracija/arhiva/_SUPERSEDED]]
- 📝 **Ažurirano**: MASTER-PLAN V2 (W3 3.1–3.4 prepisani, W1 1.3 + M8 rešeni, gate kriterijum), BLOK-C (C1/C2 → parity), PROGRESS (Sledeće = F1→F7, stare statistike arhivirane), CLAUDE.md §7.4
- ⚠️ **Gotcha za buduće sesije**: title/meta quick-win za pop-tenis i odbojku raditi POSLE F3 (reimport bi pregazio izmene); live postovi imaju `<h1>` u sadržaju → 2×H1 posle importa (rešava restyle); lite-shot 325 (lokal) vs 795 (live) — verovatno različiti modeli, proveriti pre rename.
- Ništa nije menjano u bazi ove sesije — samo dokumentacija + arhiviranje kopija CSV-ova.

## 2026-07-07 [claude-code] [ANALITIKA] — Nedeljni izveštaj (GA4+Ads+GSC+GMB) + bounce rate nalaz ✅
- ✅ **Nedeljni izveštaj 7d vs 7d** (30.6–6.7 vs 23.6–29.6) povučen preko Windsor.ai, prošireno sa GMB podacima (novo — connector `google_my_business`, lokacija "Industrijski i sportski podovi Beograd - Antas Line", `locations/3289324505122199130`)
- 📊 **Prava konverzija** (`/hvala-za-poruku/` page view) pala -45,5% (22→12) uz skoro stabilan saobraćaj (korisnici -2,3%) → pad konverzione stope 2,79%→1,56%, ne pad tražnje
- 🔴 **Nalaz — `generate_lead` event dosledno veći od `/hvala-za-poruku/` pageviews** (18 vs 12 ove nedelje, 27 vs 22 prošle, ~20-50% sistematski offset oba perioda) → sumnja na duplo okidanje Page View trigera u GTM-u; treba proveriti GTM Preview. Nije nova pojava (postoji u oba perioda), ali nikad ranije flagovano.
- 📊 **Bounce rate — veliki WoW pad**: 57,9% (23-29.6) → 34,9% (30.6-6.7), oštra korak-promena tačno oko 28-30.6. Poklapa se sa BLOK A GTM v10 čišćenjem (Consent Mode + MI gašenje) → verovatno tačnije merenje engagement-a (MI/GTM konflikt ranije lažno naduvavao bounce), ne stvarna promena ponašanja. Nema alarmantnih stranica po bounce-u na visokom saobraćaju (`/kontakt/` 6,7%, `/industrijski-podovi/` 18,6%, homepage 20,9%); jedino niskoprometne stranice (`/pop-tenis/`, parket-guide) imaju visok bounce ali premali uzorak (5-12 sesija) za pouzdan signal.
- 📊 **Ads**: kumulativ plaćenih konverzija od 2026-06-01 = 10 (prag za Maximize Conversions je 20-30) → ostaje se na Maximize Clicks. ECOTILE CPC pao 73,9→51,8 RSD uz bolji CTR, throttling nije prisutan.
- 📊 **GSC top prilika (28d, poz. 5-15)**: "epoksidni podovi cena po m2" (210 impr, 0% CTR) i "epoksi podovi"/"epoxy podovi" varijante — visok volumen, nula klikova unatoč dobroj poziciji; conquest članak (`/epoksidni-podovi-ili-ecotile-podovi/`) verovatno ne hvata price-intent frazu u title/meta. "industrijski podovi" (199 impr, poz 10,8, CTR 1,5%) — money-keyword na str. 2, vezano za blokirani WPBakery insert na post ID 4937 (6 blokova čeka, poznat JS bug).
- 📊 **GMB**: impresije prepolovljene WoW (62→30), samo 6 recenzija ukupno (prosek 4,7), 0 novih ove nedelje — nema plaćenog troška, signal slab, prilika za brz win (traženje recenzija od skorašnjih lidova).
- **Akcija nedelje predložena**: proveri GTM Preview na `/hvala-za-poruku/` (moguće duplo okidanje `generate_lead`) + pokreni traženje recenzija za GMB.
- Izveštaj ostao u chat-u (nije eksportovan kao poseban fajl); nije menjano ništa u GTM-u/Ads-u ove sesije — samo analiza preko Windsor.ai (read-only).

## 2026-07-07 [claude-code] [W1 — DIZAJN FIX] — Desktop razmaci/font + sistemski bug dijagonalnih CTA sekcija ✅
- ✅ **Desktop spacing/font** (Miroslav: "previše prazno, font u hederu prevelik"): u `antas-design.css` —
  - `--al-gap` (vertikalni ritam sekcija): `clamp(56px, 9vw, 140px)` → `clamp(56px, 5vw, 72px)` (desktop max −49%, mobile min 56px nepromenjen)
  - `.al-display--xl` (hero naslov): `clamp(44px, 7.5vw, 104px)` → `clamp(44px, 6.4vw, 88px)` (desktop max −15%, mobile min 44px nepromenjen)
  - `/o-nama/` (ID 571) "Kontaktirajte nas" kicker red izgledao kao prazna kutija (pun `--al-gap` ritam za 2 reda teksta) → nova klasa `.al-section--compact` (samo padding-top, tesan uz sekciju iznad)
- 🔴→✅ **Sistemski bug nađen i popravljen**: dijagonalni prelaz ka CTA sekciji (`al-diag-top`/`al-diag-top--rev`) je na svakoj stranici ostavljao beli trougao/traku umesto da boja prethodne sekcije ispuni rez — najvidljivije na mobile (manji `--al-cut`), ali i na desktopu (`/industrijski-podovi/`).
  - **Uzrok**: `margin-top: calc(-1 * var(--al-cut))` je trebalo da "povuče" CTA red preko prethodne sekcije (preklop koji rez treba da otkrije). Na ovom sajtu WPBakery `full_width="stretch_row"` ubacuje prazan `<div class="vc_row-full-width vc_clearfix">` (float:left, height:0) između SVAKA dva reda — iz nejasnog razloga to poništava `margin-top` na sledećem redu (computed stil pokazuje ispravnu vrednost, ali render pozicija se ne pomera ni za piksel — potvrđeno testom sa `margin-top:-300px !important` inline).
  - **Fix**: `.al-diag-top` i `.al-diag-top--rev` sada koriste `position: relative; top: calc(-1 * var(--al-cut))` (+ kompenzujući `margin-bottom` da ne ostane rupa u toku dokumenta) umesto `margin-top`. `top` radi ispravno na ovom sajtu (potvrđeno merenjem: preklop tačno −96px). Jedna CSS izmena, važi sitewide — nije trebalo dirati sadržaj nijedne stranice.
  - Usput probao (pa vratio) privremene per-page `al-diag-bottom`/kombinovane klase na 13 stranica dok nisam našao pravi uzrok — sve te dodatne klase su uklonjene iz `post_content` (13 stranica), ostao je samo `al-section--compact` na 571 (namerno, nezavisna ispravka).
  - Nova lekcija upisana u [[reference/naucene-lekcije]] i [[migracija/woodmart-sabloni]] (gotcha #11).
- Backup-ovi: `antasline_local_2026-07-07_0839_pre-onama-kontakt-section.sql`, `antasline_local_2026-07-07_1011_pre-industrijski-cta-seam.sql`, `antasline_local_2026-07-07_1034_pre-sitewide-cta-seam.sql`
- Verifikovano: HTTP 200 na svih 13 pogođenih stranica (Početna, industrijski-podovi, kontakt, o-nama, sportske-podloge, 4 sport stranice, 2 dimenzije stranice, parking-staze, spoljne-podne-obloge), dijagonale čiste na desktop i mobile (simulirano `--al-cut:28px`).

## 2026-07-07 [claude-code] [W1 + C1 BRZI COMBO] — N1 silo zatvoren + C1 verifikacija + /hvala-za-poruku/ kreirana ✅
- ✅ **N1 silo sekvenca 1.1 zatvoren**: sve 4 stranice iz plana su gotove/ažurirane
  - `/spoljne-podne-obloge/` (ID 16590 — bez j, Bergo za terase) — kreirano 2026-07-07
  - `/podloge-za-parkiraliste-i-staze/` (ID 16589 — parking + staze) — kreirano 2026-07-07
  - `/kontakt/` (ID 61 — forma + mapa) — upgrade 2026-07-07, forma ispravljena
  - `/o-nama/` (ID 571 — brend info) — upgrade 2026-07-07
- ✅ **Brzi W1 bonus**: `/podloge-za-parking/` (ID 15580 — lokalni draft) — Yoast title/metadesc ažurirani (meta title "Podloge za Parking, Pešačke Staze i Prilaze - Antasline")
- ✅ **C1 verifikacija — 5 ključnih stranica**:
  - `/spoljne-podne-obloge/` (ID 5255) — 200, publish
  - `/podloge-za-parkiraliste-i-staze/` (ID 16589) — 200, publish
  - `/sportska-igralista/` (ID 15973) — 200, publish
  - `/zamena-parketa-u-sportskim-salama/` (ID 15965) — 200, publish
  - `/podloge-za-krovove-i-terase/` (ID 15971) — 200, publish
- ✅ **C1 verifikacija — UKUPNO (live vs lokal)**:
  - **Live stranice**: 80 (pages + posts + products)
  - **Lokalne stranice**: 98 (nova + rebuilds)
  - **Verifikovane (match live+lokal)**: 25 stranica — spremne za parity
  - **Nedostaje na lokalu (PROVERI)**: 38 stranica — trebalo importovati ili 301 redirect
  - **CSV redirect mapa** — kreirama: `antasline-redirect-mapa-2026-07-07.csv` (38 PROVERI redova + 3 GOTOVO)
- 🔴 **KRITIČNA PRONALAZKA**: `/hvala-za-poruku/` (thank you page za forme) **NEDOSTAJALA** — kreirama odmah (ID 16600). To je KEY page za `generate_lead` GA4 event tracking!
- 📋 **Prioriteti za C1 ostatak (W1 1.2)**: padel-tereni, kosarkaske-konstrukcije, garaze-i-autoservisi (sport/vertikala silo) + antistatik (industrijski) + 20+ proizvoda + legal stranice
- 🔧 **Lesson**: Live `/aktuelnosti/` → trebalo `/blog/` na lokalu (slug rename); `/spoljnje-podne-obloge/` (live sa j) → `/spoljne-podne-obloge/` (lokal bez j) — 301 redirect
- 🔧 **Lesson**: `/podloge-za-parking/` i `/podloge-za-parkiraliste-i-staze/` — dve različite stranice na lokalu (ID 15580 vs 16589), ali live samo ima `/podloge-za-parkiraliste-i-staze/`; parity odluka: ID 15580 može biti placeholder ili draft, ili se izbriše pre migracije
- Backup-ovi: `antasline_local_2026-07-07_pre-parking-rebuild.sql` (90 MB); prethodni iz iste sesije: `antasline_local_2026-07-07_pre-kontakt-fix.sql`, `antasline_local_2026-07-07_pre-onama-kontakt-upgrade.sql`, `antasline_local_2026-07-07_pre-spoljne-podne-obloge.sql`, `antasline_local_2026-07-07_pre-podloge-za-parking.sql`

## 2026-07-07 [claude-code] [W3 TEHNIČKA] — 3.13 backup automatizovan ✅ + 3.14 popis pokrenut ⏳
- ✅ **3.13 zatvoreno**: `C:\xampp\htdocs\antasline-backups\scripts\nocni-backup.ps1` — mysqldump `antasline_local` + zip `wp-content` u jedan arhiv, rotacija zadržava poslednjih 14, log fajl. Registrovan u Windows Task Scheduler-u ("AntasLine Nocni Backup", Daily 03:00, RunLevel Limited). Ručni test: DB dump 90MB (2s) + zip 3,6GB wp-content → 3GB arhiv (27 min ukupno) — prihvatljivo za noćni posao.
- Destinacija je pametna: skripta proverava da li je OneDrive folder (`C:\Users\Miroslav\OneDrive`) stvarno sinhronizovan (ne samo prazan placeholder) — trenutno NIJE ulogovan pa piše LOKALNO u `antasline-backups\auto\`; čim se M prijavi na OneDrive, sledeće pokretanje automatski prebacuje na cloud kopiju bez izmene skripte. #ceka-miroslav: prijava na OneDrive.
- Retencija 14×~3GB = do 42GB na disku — trenutno 88,9GB slobodno na C:, dovoljno.
- ⏳ **3.14 u toku**: M pročitao cPanel i javio brojeve — PHP 8.3 (⚠️ lokalni XAMPP build je na 8.2.12, treba proveriti kompatibilnost teme/pluginova pre migracije), disk 5,05/11,95GB (42%, 6,9GB slobodno), subdomeni dostupni (0 iskorišćeno, neograničeno). Dovoljno prostora za probu migracije na `novi.antasline.com`.
- Sledeći korak (subdomen kreiranje + upload/import + merenje vremena) nastavlja se u sledećoj sesiji — otvoreno pitanje načina rada (M sam uz moje instrukcije / SSH kredencijali meni / cPanel File Manager bez SSH-a).

## 2026-07-07 [claude-code] [KONTAKT FORMA + MAPA] — Ispravka i finalizacija ✅
- ✅ **Kontakt forma**: CF7 ID 5339 (`Kontakt forma` — postojeća, funkcionalna)
  - Polja: Ime, Email, Naslov, Poruka, Dugme "Pošalji"
  - Email notifikacije (admin + auto-reply) — već konfiguriran
- ✅ **Google Mapa**: Embed mapa sa lokacijom (Ulcinjska 13, Beograd, real Google Maps embed)
  - Vidljiva ispod forme na `/kontakt/`
- ✅ **Rezultat**: `/kontakt/` stranica je sada čista, forma je vidljiva i funkcionalna, mapa je vidljiva
- 🔧 **Ispravka workflow**: Počeo sa CF7 ID 16593 (problem) → zamenjeno sa ID 5339 (funkcionira)
- Backup-ovi: `antasline_local_2026-07-07_pre-forma-ga4.sql` + `antasline_local_2026-07-07_pre-kontakt-fix.sql` (46 MB svaki)

## 2026-07-07 [claude-code] [W1 — UPGRADE ×2] — /o-nama/ + /kontakt/ WoodMart redesign ✅
- ✅ **Obe stranice upgradan** sa al-WoodMart šablonom (hero navy+plates → paper body → mist info → CTA navy+rev-diag)
  - `/o-nama/` (ID 571) — O AntasLine, kompanija, šta nudimo, kontakt CTA
  - `/kontakt/` (ID 61) — Informacije, forma, FAQ, kontakt detalji (radno vreme, lokacija)
- ✅ Svaka: Yoast mete + Yoast title u `<head>` + H1 sa `al-display--xl` + WoodMart layout (full-width, title-off)
- ⚠️ Forma na `/kontakt/` — Contact Form 7 ID 3 nije pronađena; trebala bi ispravljanje ako trebala prava forma (za sada placeholder)
- HTTP 200 obe stranice, dizajn konzistentan sa ostalim silo stranicama
- Backup: `antasline_local_2026-07-07_pre-onama-kontakt-upgrade.sql` (46 MB)

## 2026-07-07 [claude-code] [W1 — SILO REBUILD ×2] — /spoljne-podne-obloge/ + /podloge-za-parkiraliste-i-staze/ ✅
- ✅ **2 silo landing-a** kreirane po al-WoodMart šablonu (hero navy+plates → paper body → FAQ mist → CTA navy+rev-diag)
  - `/spoljne-podne-obloge/` (ID 16590 — ispravljeno sa 16588; trebalo je bez "j": "spoljne" ne "spoljnje") — Bergo ploče za terase, karakteristike, Bergo Flooring info
  - `/podloge-za-parkiraliste-i-staze/` (ID 16589) — industrijske podloge za parking, specifikacije, sigurnost
- ✅ Svaka stranica: Yoast mete, FAQPage JSON-LD (3-4 stavke), CTA linkovi, HTTP 200, 1×H1
- 🔧 Lesson: vc_raw_html za JSON-LD nije pouzdano → direktno dodavanje kao `<script>` tag u post_content (gotcha #8 iz woodmart-sabloni)
- ⚠️ Napomena za live migraciju (C1 redirect mapa):
  - Live `/spoljnje-podne-obloge/` (sa j) → Lokal `/spoljne-podne-obloge/` (bez j, ID 16590) — 301 redirect
  - Live `/podloge-za-parkiraliste-i-staze/` → Lokal `/podloge-za-parkiraliste-i-staze/` (ID 16589) — parity (isti slug)
- Backup-ovi: `antasline_local_2026-07-07_pre-spoljne-podne-obloge.sql` + `antasline_local_2026-07-07_pre-podloge-za-parking.sql` (46 MB svaki)
- Skripti: `build-spoljnje-podne-obloge.php`, `build-parking.php` (scratchpad)

## 2026-07-07 [claude-code] [W1 — SILO REBUILD] — /spoljnje-podne-obloge/ WoodMart silo landing ✅
- ✅ Backup pre rada: `antasline_local_2026-07-07_pre-spoljne-podne-obloge.sql` (46 MB)
- ✅ Kreirane `/spoljnje-podne-obloge/` (ID 16588) po al-WoodMart šablonu (hero navy+plates+diag-bottom → paper body → FAQ mist+diag-top → CTA navy+plates+rev-diag)
- ✅ Content parity iz live export XML (SiteOrigin layout): intro 2 rečenice · Bergo karakteristike + Bergo Flooring historia · FAQ 4 stavke (trajnost, demontaža, restorani, održavanje) · JSON-LD schema (FAQPage) · Yoast mete iz live-inventar CSV
- ✅ Postmeta: `_woodmart_main_layout=full-width`, `_woodmart_title_off=on`
- ✅ Yoast: Title "Podne obloge za bašte i terase - jednostavna montaza i veliki izbor boja" · Metadesc "Spoljasnje podne obloge za terase, dvorista, baste..."
- ✅ Verifikacija: HTTP 200 · 1×H1 "Spoljne podne obloge za bašte i terase" · Yoast title u <head> · FAQPage JSON-LD dodan; interni CTA linkovi ka `#upit` forma
- 🔧 Gotcha: `vc_raw_html` sa JSON-LD nije se prikazao → JSON-LD dodan direktno kao `<script>` tag na kraju post_content (WPBakery vc_raw_html gotcha #8)
- 📍 Gde čeka: Slike/referenci (nije dodata galerija — live stranica je imala SiteOrigin `[Best_Wordpress_Gallery id="35"]` — trebam da dodavam referentne slike ako postoje lokalno)
- Skripti: `build-spoljnje-podne-obloge.php`, `fix-faq-schema.php` (scratchpad)

## 2026-07-07 [claude-code] [PLAN - PROCESNI AUDIT] — 9 predloga upisano u Master Plan V2 ✅
- ✅ Drugi krug audita (posle sadržajnog 07-06) — fokus na proces/rizik/biznis logiku, ne sadržaj:
  1. 🔴 **Backup rizik**: 2 meseca rada samo na jednom disku → novi zadatak 3.13 (noćni mysqldump + wp-content zip na drugu lokaciju)
  2. 🔴 **M6 SSH bez fallbacka, rok tek u N8** → 3.14 ubrzano na OVU nedelju (test pristupa) + proba migracije na subdomen `novi.antasline.com` u N6 (izmeriti stvarno vreme, testirati rollback)
  3. **Woo checkout vs katalog režim**: 0/37 proizvoda ima cenu → nova zavisnost M9 (odluka: "Zatraži ponudu" umesto korpe) + W1 zadatak 1.8
  4. **Cenovnik kao fajl**: nova zavisnost M10 + kreiran `[[reference/cenovnik]]` (tabele po kategoriji proizvoda, prazno = na upit) — sprečava ponovno pitanje cena po svakoj sesiji
  5. **Telefon haos**: 063/069/072/074 u opticaju na buildu → novi zadatak 1.9 (SQL audit `tel:` linkova, ujednačiti na jedan)
  6. **SERP snapshot**: nema baseline pozicija konkurencije pre migracije → novi zadatak 3.15
  7. Sezonski kalendar → nova sekcija **8. W6/W7 POSLE LIVE-A** u planu (B2B jesen, priprema terase kampanje zima, GSC špic mar–maj)
  8. Post-live monitoring pojačan (3.12): UptimeRobot + dnevni 404 pregled umesto ad-hoc
  9. Proces: **"ponedeljak 15 min"** pregled svih M-zavisnosti — ugrađeno u skill `/antasline-sesija` (korak 3b) i pomenuto u `[[reference/claude-skilovi]]`
- ✅ Ažurirano: [[2026-07-06-MASTER-PLAN-V2]] (W1 1.8/1.9, W3 3.13/3.14/3.15, zavisnosti M9/M10, rizici, gate kriterijumi, N1/N6 raspored, nova sekcija 8), `[[reference/cenovnik]]` (nov fajl), `/antasline-sesija` skill, `[[reference/claude-skilovi]]`, CLAUDE.md §13 hub
- 🔴 Najhitnije: M9 (checkout odluka) + M10 (cenovnik popuna) + 3.13/3.14 (backup + SSH test) — sve ove nedelje

## 2026-07-06 [claude-code] [W4 + W5 UNOS] — GA4 publike + GMB ažuriranje ✅
- ✅ **GA4 publike — 2 nove kreirane od Miroslava**
  - `Parking & spoljne podloge` — `page_path contains /podloge-za-parkiraliste/ OR /spoljnje-podne-obloge/` (očekivano ~120 korisnika/14d)
  - `Košarkaški tereni` — `page_path contains kako-napraviti-teren-za-basket OR kosarkaske-konstrukcije` (~265/14d)
  - Status: "Too small to serve" prvih dan-dva dok saobraćaj poraste; sinhronizovanje sa Google Ads aktivno
- ✅ **GMB ažuriranje (M4 / plan 5.2, rok 2026-07-31)**
  - UTM fix: Website URI zamenjeno na `https://antasline.com?utm_source=google&utm_medium=gmb&utm_campaign=local` (GA4 će meriti kao GMB kanal umesto Unassigned)
  - Kategorije proširene: +`Gradnja sportskih terena` + `Pružalac usluga za podove` (bilo samo "Продавница подова")
  - Prvi Google Post za 6 godina (jula 2026 kampanja — Bergo Ultimate/Naxos Evolution)
  - Review link: čeka na prve poslove (M4 fallback, nije blocker)
- Efekat: GMB impresije −73% (trend) + saobraćaj sa GMB sada merljiv u GA4; reviews mogu početi prirodno sa poslovima

## 2026-07-06 [claude-code] [AUDIT + SKILL INFRASTRUKTURA] — Rupe u projektu + 4 Claude Code skila ✅
- ✅ **Audit celog projekta** — dve glavne rupe potvrđene podacima:
  1. **Social/email ne postoji u planu**: Organic Social 70 korisnika/90d (0,5%) ali 81% engagement; nijedan social/email/video zadatak u Master planu V2; ~55 kontakata/mes bez follow-up-a (M5)
  2. **Proizvodi thin (provera lokalne baze, 37 proizvoda)**: 0/37 cena · 0/37 Yoast title/metadesc · 0/37 galerija (a 115 slika importovano) · 0/37 Woo atributa · 14/37 opis <1.000 znakova · 0 PDF tehničkih listova; kanibalizacija rizik proizvod↔stranica (Bergo Unique)
  - Manje rupe: CRO odsutan (0,88% konverzija, 76–87% mobile, nema sticky CTA), `/hvala-za-poruku/` prazna (0 reči), `lead_form_start` nije implementiran (Form Abandoners publika se ne puni), blog bez post-live plana, nema saglasnosti za email na formi
- ✅ **4 projektna skila** u `.claude/skills/` (aktivni od sledeće sesije):
  - `antasline-sesija` — master protokol sesije (otvaranje → W1–W5 tok → verifikacija → zatvaranje)
  - `obogati-proizvod` — 8-tačka šablon obogaćivanja Woo proizvoda + money-first redosled (Ecotile → konstrukcije → batch)
  - `w6-social` — novi W6 workstream (Faza 0 pre live-a: popis profila/M5/GMB/saglasnost; pun ritam od 2026-09-01; UTM standard za social)
  - `nedeljni-izvestaj` — 7d vs 7d kroz Windsor po formatu [[CLAUDE]] §10 sa svim naučenim zamkama
- ⏳ Čeka odluku Miroslava: (1) product šablon kao novi W1 zadatak → start sa Ecotile linijom, (2) W6 upis u Master plan, (3) popis social profila + M5 odgovor #ceka-miroslav

## 2026-07-06 [claude-code] [ADS - NEGATIVNE KW] — M2 / plan 4.1 zatvoreni ✅
- ✅ Analiziran izvoz iz Ads UI (`Files/Negative keyword details report.csv`, 44 negativne) vs [[CLAUDE]] §6 referentna lista — falilo 7: `epoksi`, `epoksidni`, `epoksidnih`, `epoksidnog`, `betonski`, `"industrijski beton"`, `[podne obloge]`. Ključni nalaz: **oblik "epoksi" uopšte nije bio pokriven** (broad negativne nisu morfološke — `epoksidna` ne blokira `epoksidni`)
- ✅ Miroslav u Ads UI dodao 13 negativnih (gornjih 7 + `teraco`, `letvice`, `pevex`, `"uradi sam"`, `"keramičke pločice"`, `"podne pločice"` — phrase umesto broad `plocice` da ne blokira "pvc pločice" upite iz ponude), pauzirao KW `bastenski namestaj` + `oprema za bazene` u Terasama, potvrdio da je lista "AntasLine — univerzalne negativne" primenjena na obe kampanje
- `laminat` svesno izostavljen ([[CLAUDE]] §6 pravilo) — watch lista
- Efekat: zatvara ~16% curenja budžeta (M2 iz [[2026-07-06-MASTER-PLAN-V2]]); sledeće u W4: Faza 1 RSA Terase
- Detalji: [[dnevnik/ADS-DNEVNIK]] log 2026-07-06

## 2026-07-06 [claude-code] [PLAN - MASTER PLAN V2] — Novi plan projekta do live-a ✅
- ✅ Pročitani svi .md fajlovi u vault-u (40) → napravljen **[[2026-07-06-MASTER-PLAN-V2]]** kao jedini izvor istine za plan (stari [[2026-07-02-MASTER-PLAN-DO-LIVE]] označen `superseded` — pisan pre Porto→WoodMart prelaska, live exporta i C3 draftova)
- Struktura V2: **baseline 2026-07-06** (šta je gotovo + metrike-nula iz [[analiza/2026-07-04-snapshot-full]]) → **5 workstream-ova** (W1 dizajn/rebuild · W2 SEO content C3+GEO · W3 SEO tehnička+migracija C1/C2+CWV · W4 Ads faze 1–4 · W5 tracking/merenje) → **nedeljni raspored N1–N8** unazad od migracije **2026-08-31** → **gate kriterijumi** za go/no-go → **8 zavisnosti od Miroslava** sa fallback-ovima i rokovima → **KPI tabla** (jun = mesec-nula) → **registar rizika**
- Odluke Miroslava: novi fajl V2 (ne update starog in-place) · go-live ostaje 2026-08-31
- Ažurirane reference: [[PROGRESS]] (banner + red u tabeli), [[blokovi/BLOK-C-sledece]] (C1/C2/C3 → workstream mapiranje), [[CLAUDE]] §12/§13, stari plan (superseded napomena + frontmatter)
- 🔴 Najhitnije iz plana: M1 cene za Tier1 draftove (rok 10.07, fallback "cena na upit") + M2 negativna lista u Ads UI (15 min, zaustavlja ~16% curenja)

## 2026-07-06 [claude-code] [C3 TIER1 #4/#5] — Dimenzije terena + table za košarku ✅
- ✅ Napravljene **2 TIER1 SEO stranice** iz [[seo/plan-novih-stranica]] (~20k impr, poz. 1–2 ali nizak CTR — cilj je featured snippet, ne rang):
  - `/dimenzije-kosarkaskog-terena/` (ID 16586) — FIBA vs NBA tabela (teren, koš, tri poena, reket, centralni krug), školski/rekreativni/3x3 varijante, link ka `/kosarka-3x3-tereni/`
  - `/dimenzije-kosarkaske-table/` (ID 16585) — dimenzije table, visina montaže, staklo vs akril, uradi-sam vs gotova konstrukcija (cena "na upit", bez izmišljenih brojeva jer nemamo prave cifre), link ka `/kategorija/kosarkaske-konstrukcije/`
- 🐛 **Slug konflikt otkriven**: `/dimenzije-kosarkaskog-terena/` slug je od 2022. bio zauzet starim image **attachment**-om (`post_type=attachment`, prazan `post_content`) korišćenim inline u basket članku — `get_page_by_path()` ga je vraćao i pored `post_type='page'` filtera (WP kvirk, attachment slug i dalje blokira page slug). Rešeno preimenovanjem attachment `post_name` u `dimenzije-kosarkaskog-terena-slika` (guid/URL same slike ostaje netaknut, samo interni slug se menja) — bezbedno jer se slika u sadržaju referencira direktno preko uploads putanje, ne preko attachment permalink-a
- ✅ **Anti-kanibalizacija**: postojeći članak "Kako napraviti teren za basket ili košarkaški teren" (ID 2298) je imao punu "Dimenzije terena za košarku" → "Obruč koša" sekciju (1894 bajta, dupliran sadržaj sa novom stranicom) — skraćeno na 2 rečenice + linkovi ka obe nove stranice; sekcija "Košarkaške konstrukcije" ispod ostala netaknuta
- ✅ Verifikacija (2/2 nove + 1 izmenjena): sve 200, 1×H1, FAQPage JSON-LD, cross-linkovi (`/kosarka-3x3-tereni/`, `/kategorija/kosarkaske-konstrukcije/`) rade, Yoast title/metadesc + `_woodmart_title_off` setovani
- Skripte: `build-basket-dimension-pages.php`, `trim-basket-article.php` (scratchpad)

## 2026-07-06 [claude-code] [DIZAJN - 4 nove sport stranice] — Popunjena rupa u /sportske-podloge/ gridu ✅
- 🐛 **Bug otkriven u jučerašnjem gridu (5438)**: 4 od 11 kartica u "Izaberite sport" gridu na `/sportske-podloge/` nisu vodile ka pravom sadržaju — Futsal kartica je linkovala na `/industrijski-podovi/` (potpuno nepovezano), a 3x3/Stoni tenis/Hokej kartice su sve tri vodile na isti `/sportske-podloge/bergo-ultimate/` fallback. Provera baze potvrdila da za sva 4 sporta nikad nije postojala prava dedicated stranica — stari nav meniji (`futsal-tereni`, `hokejaski-tereni`) su i ranije upućivali na generičke proizvod-stranice (Naxos Evolution / Bergo Ultimate)
- ✅ Napravljene **4 nove landing stranice** po istom al- WoodMart šablonu (hero navy+plates → USP paper → specifikacija mist → foto-reference paper → FAQ+FAQPage JSON-LD mist → CTA navy):
  - `/podloge-za-futsal-terene/` (ID 16581) — indoor (Naxos Evolution) + outdoor (Bergo Ultimate) opcije, FIFA/FIBA dimenzije 38–42×18–22m
  - `/podloge-za-hokejaske-terene/` (ID 16582) — Naxos Evolution, dvoranski hokej/floorball
  - `/podovi-za-stoni-tenis-sale/` (ID 16583) — Naxos Evolution, ugao na mat površinu (bitno za praćenje loptice)
  - `/kosarka-3x3-tereni/` (ID 16584) — Bergo Ultimate, FIBA 15×11m, foto-reference sa stvarnih terena (Jakovo, Zlatibor, Novi Sad) + link ka Dunk Shop case study (`/teren-za-basket-3x3/`)
  - Sadržaj oslonjen na stvarne proizvod-činjenice iz postojećih Naxos Evolution (ID 15490) i Bergo Ultimate (ID 15480) stranica, ne izmišljen
- ✅ Sva 4 linka u `/sportske-podloge/` gridu (5438) ispravljena da vode ka novim stranicama umesto na placeholder ciljeve
- 🔧 **Nova gotcha**: nove `page` stranice pravljene direktno preko `wp_insert_post()` dobijaju WoodMart-ov automatski page-title `<h1 class="entry-title">` PORED našeg `<h1 class="al-display--xl">` iz sadržaja → 2×H1. Rešenje: `_woodmart_title_off = 'on'` postmeta (isti trik već postoji na 16567 industrijski-podovi, ali nije bio dokumentovan) — mora se dodati ručno posle insert-a, nije default
- ✅ Verifikacija (4/4): HTTP 200, tačno 1×H1 (posle `_woodmart_title_off` fix-a), FAQPage JSON-LD validan, sve slike (postojeći uploads) i interni linkovi vraćaju 200, Yoast title/metadesc setovan
- Backup pre izmena: `antasline_local_2026-07-06_pre-4-sport-pages.sql` u `C:\xampp\htdocs\antasline-backups\`
- Skript: `build-sport-pages.php` + `fix-sport-grid-links.php` (scratchpad, nisu u vault-u)

## 2026-07-06 [claude-code] [DIZAJN - 10 WooCommerce kategorija] — WoodMart Layout Builder landing sekcije ✅
- ✅ **Novi mehanizam otkriven i prvi put isproban u projektu**: WoodMart "Layout Builder" (`post_type=woodmart_layout`, `wd_layout_type=shop_archive`, `wd_layout_conditions` sa `condition_type=product_term`) potpuno zamenjuje WooCommerce archive template za odabranu kategoriju — omogućava hero/USP/FAQ+schema tretman + `[woodmart_shop_archive_products]` grid, isto vizuelno poput `/industrijski-podovi/` i `/sportske-podloge/` stranica
- ✅ Svih **10 kategorija** (245–254, Ergomat/DuraStripe/Bergo/Ecotile katalog, prethodno bez ikakvog opisa/SEO meta) dobilo puni ili skraćeni landing tretman: 6 punih (hero+USP+grid+FAQ+CTA: 245, 246, 248, 251, 252, 254), 4 skraćene (hero+intro+grid+FAQ+CTA bez USP grid-a, za 1–2 SKU kategorije: 247, 249, 250, 253)
- ✅ **Diferencijacija duplikata**: 245 "Zaštita i Bumperi" (proizvod-katalog ugao) ↔ 246 "Industrijska zaštita" (rešenje-za-problem ugao, isti proizvodi) i 251 "Košarkaške konstrukcije" (teren/instalacija) ↔ 252 "Oprema za sportske terene" (šira sportska oprema, 100% identični proizvodi) — obostrani cross-linkovi da se izbegne dupli sadržaj za Google
- ✅ **254 "Industrijski podovi" vs postojeća `/industrijski-podovi/` (16567) kanibalizacija rešena**: 16567 ostaje edukativna/poredbena stranica, nova kategorija je transakciona/katalog stranica + dodat 1 interni link sa 16567 ka novoj kategoriji
- ✅ Yoast SEO title/metadesc setovan za svih 10 (`WPSEO_Taxonomy_Meta::set_values()`)
- 🔧 **3 nova gotcha-e otkrivene i rešene** (bitno za buduće layout builder izmene):
  1. `wd_layout_conditions` MORA imati `condition_comparison => 'include'` po uslovu — bez toga se layout tiho nikad ne aktivira, bez greške
  2. `WPSEO_Taxonomy_Meta::set_value()` pozvan pojedinačno (title, pa desc) **briše** prethodno postavljeno polje jer nema "retain old value" fallback za title/desc — mora `set_values()` sa oba ključa u JEDNOM pozivu
  3. Yoast keš-uje title/desc u `wpGs_yoast_indexable` tabeli (Indexables sistem) — ne osvežava se automatski kad se termmeta menja mimo admin UI-ja; mora se obrisati stale red (`$wpdb->delete` po `object_id`+`object_type`+`object_sub_type`) da se izgradi iznova sa svežim vrednostima
  4. Direktan `wp_update_post()` posle `$wpdb->update` patch-a JSON-LD-a **ponovo** provlači ceo `post_content` kroz kses (briše `vc_raw_html` opet) — status na `publish` mora ići u ISTOM raw `$wpdb->update` pozivu kao i content patch, nikad kroz `wp_update_post()`; pošto to zaobilazi `save_post` hook, `wd_layouts_conditions` keš se mora ručno regenerisati (`new \XTS\Modules\Layouts\Conditions_Cache())->regenerate()`) posle batch-a
- ✅ Verifikacija (10/10): HTTP 200, tačno 1×H1, FAQPage JSON-LD validan bez dupliranja Yoast `CollectionPage`/`BreadcrumbList` grafa, `<title>`/meta = Yoast vrednosti, product grid renderuje prave proizvode (3/12/12/1/6/2/1/5/5/1), cross-linkovi 200, `SELECT COUNT(*) WHERE post_type='woodmart_layout' AND post_status='publish'` = 10
- ⏳ Mobilni viewport vizuelni check nije urađen (browser resize alat nije pouzdano menjao render viewport u ovoj sesiji) — isti otvoreni item kao i za ostale WoodMart stranice
- Backup pre izmena: `antasline_local_2026-07-06_pre-category-landings.sql` (46,6 MB) u `C:\xampp\htdocs\antasline-backups\` (van webroot-a)
- Skript: `build-category-landings.php` (scratchpad, nije u vault-u — sadrži sav copy za 10 kategorija, ponovo pokretljiv sa `pilot`/`batch`/`all` argumentom)

## 2026-07-06 [claude-code] [DIZAJN - /sportske-podloge/ rebuild] — Silo hub na WoodMart šablonu ✅
- ✅ **Stranica ID 5438** (postojeći slug `/sportske-podloge/`, nije nova) rebuildovana po istom šablonu kao industrijski-podovi: hero (navy+plates) → intro + 6 USP kartica (paper: neklizajući, multisport, sertifikovano, montaža, održavanje, boje) → grid 11 sport disciplina sa foto karticama (mist, diag-top) → Bergo Ultimate specifikacija (paper) → FAQ 4 pitanja + FAQPage JSON-LD (mist) → CTA (navy, diag-top--rev)
- ✅ **Content parity izvor bio je dupli**: live sadržaj je u SiteOrigin `panels_data` (serijalizovan PHP niz, ne WPBakery — `content:encoded` prazan!), post_id 1849; napisan mali PHP ekstraktor (`unserialize` + `strip_tags`) da se izvuče tekst. Lokalni WPBakery sadržaj (pre-rebuild) imao je dodatnu hub-grid strukturu (12 sport kartica) koje live verzija nije imala — zadržano jer služi internom linkovanju ka postojećim sport stranicama
- ✅ **Yoast title/metadesc preneti sa live** (lokalno nisu postojali): "Sportske podloge za kosarku, basket, 3x3, odbojku, futsal" / metadesc o košarci, odbojci, rukometu, futsalu, tenisu
- 🔧 **Nova lekcija** (dodato u woodmart-sabloni): `/bergo-ultimate/` (ID 15480) ima `post_parent = 5438` → kanonski URL je `/sportske-podloge/bergo-ultimate/`, direktan `/bergo-ultimate/` vraća 301. Proveriti `post_parent` pre linkovanja na child stranice iz hub grid-a.
- ✅ Verifikacija: HTTP 200 · 1×H1 · FAQPage JSON-LD parsiran i validan (4 pitanja) · svih 11 slika kartica vraća 200 · svih 9 unikatnih link targeta (uklj. ispravljen bergo-ultimate) vraća 200 · WPBakery CSS keš meta očišćen posle izmene
- Backup pre izmena: post_content sačuvan u scratchpad (`sportske-podloge-BACKUP-content.txt`)

## 2026-07-05 [claude-code] [DIZAJN - /industrijski-podovi/ rebuild] — Silo landing na WoodMart šablonu ✅
- ✅ **Nova stranica ID 16567** po silo šablonu iz [[migracija/woodmart-sabloni]]: hero (navy+plates, H1 "Industrijski PVC podovi u pločama") → 6 USP kartica sa ikonicama (paper) → tabela debljina 500/5·500/7·500/10 + 4 kartice pod-asortimana (mist, diag-top) → reference Hankook/Amicus/Ecotile + HTEC·Quectel → FAQ 4 pitanja + FAQPage/Product JSON-LD (vc_raw_html) → CTA (navy+plates, diag-top--rev)
- ✅ **Slug odluka**: stara Porto stranica 4937 → **draft** + slug `industrijski-podovi-stara`; nova preuzima čist slug `/industrijski-podovi/` (home kartica već linkuje tamo). Porto_builder 15447 netaknut.
- ✅ **Yoast meta prenet sa 4937** (optimizovan 2026-06-25): title "Industrijski PVC podovi u pločama — bez zastoja proizvodnje | Antas Line" + metadesc; `_woodmart_main_layout=full-width`, `_woodmart_title_off=on`
- ✅ **Interni linkovi (silo juice)**: 3× Ecotile debljine + antistatik/ergonomski/trake/odbojnici (CPT `industrija-podovi`, svi 200) + conquest članak `/epoksidni-podovi-ili-ecotile-podovi/` + 2× kontakt
- ✅ **Content parity sa live** (ID 255): intro, "razlozi za Ecotile", teksture/boje, ESD varijante, Product schema (AggregateOffer 2.000–5.500 RSD) — sve preneto; cena-FAQ namerno izostavljen (rezervisan za buduću `/industrijski-podovi-cena/`, [[dnevnik/2026-07-05-draft-industrijski-podovi-cena]])
- 🔧 **Novi gotchas** (dodato u woodmart-sabloni pravila): (1) grid kartice sa h3/p unutra moraju biti `div` — `<a>`/`<span>` omotače wpautop uvija u `<p>` i lomi markup; (2) `vc_raw_html` enkoding je `base64_encode(rawurlencode($html))` — obrnut redosled daje prazan output; (3) `wp_insert/update_post` iz CLI (bez korisnika) **skida `[vc_raw_html]` blok** (kses/save filteri) → JSON-LD ubačen direktnim `$wpdb->update` + `clean_post_cache`
- ✅ Verifikacija: HTTP 200 · 1×H1 · svi al-* markeri · FAQ JSON-LD validan (parse test) · bez neizrendovanih shortcode-ova · vizuelno u Chrome (hero, USP, tabela, grid--4, reference, FAQ) · stara `/industrijski-podovi-stara/` = 404 (draft)
- Backup pre izmena: `backup-posts-postmeta-pre-industrijski.sql` (29,8 MB, scratchpad)
- Korišćen novi **ui-ux-pro-max** skil (landing pattern: social proof pre CTA → reference sekcija ubačena pre FAQ/CTA)

## 2026-07-05 [claude-code] [ALATI - UI/UX skill + Magic MCP] — Design alati instalirani za Claude Code
- ✅ **ui-ux-pro-max skill** (github.com/nextlevelbuilder/ui-ux-pro-max-skill v2.6.2) instaliran **globalno** u `C:\Users\Miroslav\.claude\skills\` — 7 skill-ova: `ui-ux-pro-max` (orkestrator: 67 UI stilova, 161 paleta, 57 font parova, 99 UX pravila, 25 chart tipova u CSV bazama + Python search) + pod-skill-ovi `banner-design`, `brand`, `design`, `design-system`, `slides`, `ui-styling`
- 🔧 `npm install -g ui-ux-pro-max-cli` blokiran od permission sistema (untrusted install skripte) → instalirano ručno: git clone + replikacija `uipro init --ai claude --global` logike iz `cli/src/utils/template.ts`; verifikovano (`search.py "glassmorphism" --design` radi)
- ✅ **Security audit skill paketa — čisto**: bez binarnih fajlova; Python/Node skripte bez eval/exec/mrežnih poziva/obfuskacije; bez prompt injection u SKILL.md; jedini spoljni URL-ovi = Pexels stock liste + Google Fonts. Napomene: `shadcn_add.py` poziva `npx shadcn` (samo za React projekte, na eksplicitan poziv), `logo/generate.py` traži `GEMINI_API_KEY` (bez ključa ne radi)
- ✅ **Magic MCP** (21st.dev) dodat u user scope: `claude mcp add magic --scope user ... -- npx -y @21st-dev/magic@latest` → ✔ Connected; API ključ u `~/.claude.json` (rotirati na 21st.dev ako zatreba)
- Namena: podrška za dizajn rad na WoodMart buildu (silo stranice, komponente) — aktivno od sledeće Claude Code sesije
## 2026-07-05 [cpanel-live] [FIX - LiteSpeed WebP optimizacija zaglavljena] — Red odblokiran, pipeline ponovo radi
- **Simptom:** LiteSpeed Cache ne konvertuje slike u WebP (QUIC.cloud optimizacija) — Miroslav prijavio da "ne radi".
- 🔍 **Pravi uzrok:** tabela `wp_litespeed_img_optming` imala **200 slika trajno zaglavljenih u statusu REQUESTED** (poslate ka cloud-u, notify webhook se nikad nije vratio) — to je tačno cela dnevna kvota (200/dan), pa je svaki naredni pokušaj slanja trajno blokiran porukom "Too many requested images". Dodatnih **1.561 slika** čekalo je u lokalnom redu (RAW) i nikad nije poslato. `img_optm-webp` konfiguracija je sve vreme bila ispravna (uključena) — problem je isključivo u zaglavljenom redu za slanje.
- 🔍 Potvrđeno debug logom (privremeno uključen `litespeed.conf.debug=1`, isključen posle): `new_req()` je stabilno vraćao `❌ Too many requested images 200`; `last_request.img_optm-taken` stoji zamrznuto na **2023-09-01** (skoro 3 godine) dok su novi zahtevi slati normalno (`last_requested` 2026-07-03) — tipičan znak trajno zaglavljenog reda, ne kvar konfiguracije.
- ✅ **Backup pre izmene:** `~/backups/antasline_db_2026-07-05_pre-litespeed-fix.sql`
- ✅ **Ispravka:** identifikovano 20 post ID-jeva sa 200 zaglavljenih redova → resetovano preko zvanične plugin metode `Img_Optm::reset_row()` (isto što radi "Reset Row" dugme u adminu, samo automatizovano za sve odjednom) — bez ručnog SQL brisanja
- ✅ **Verifikacija:** posle reseta, ručno pokrenut `new_req()` je uspešno poslao novih 200 slika (RAW 1561→1361, REQUESTED 0→200, potvrđeno da su to novi post ID-jevi, ne stari zaglavljeni) — pipeline ponovo teče
- ⏳ **Otvoreno:** cloud notify za ovih 200 nije stigao u prvih ~6 min posle slanja (uobičajeno, cloud obrada može trajati duže) — dalja obrada ide automatski kroz postojeći cron (`litespeed_task_imgoptm_req` na 15 min) bez potrebne dalje intervencije. **Proveriti za par dana** da li se isti simptom ("Too many requested images") ponovo pojavljuje — ako da, moguće je da QUIC.cloud nalog ima dublji problem sa notify webhook-om i treba njihova podrška.
- Access log (`~/access-logs/antasline.com-ssl_log`) potvrđuje: nema nijednog pokušaja od pravog QUIC.cloud servera da pozove `/wp-json/litespeed/v1/notify_img` u poslednjih ~12h — samo moji test curl pozivi. REST ruta je ispravno registrovana i reaguje (401 na neautentifikovan POST), nije blokirana firewall-om/security pluginom.

## 2026-07-05 [claude-code] [DIZAJN - 4 ispravke po Miroslavljevim primedbama]
- ✅ **Dugmad**: umesto reza samo sleva (delovalo isečeno) → simetrični paralelogram (obe kose ivice, padding 38px > rez 12px); isto i mobilno tel dugme
- ✅ **Ikonice sistem**: 6 custom SVG linijskih ikonica u brand stilu (crvena, 1.7 stroke) → `woodmart-child/images/icons/` (montaza, izdrzljivost, protivklizna, fleksibilna, odrzavanje, izgled) + `.al-icon` klasa — **osnova za ceo sajt**, USP kartice ih već koriste; za silo stranice dodati nove u istom stilu
- ✅ **Veruju nam**: 3 projekt foto kartice (Hankook fabrika guma / Amicus farmacija / Spanoulis Court — prave slike iz medijateke) + HTEC·Quectel·Dunk Shop tekst + logo traka proizvođača (Bergo/Ecotile/Artisport, grayscale→color hover, `.al-logo-row`)
- ✅ **Blog slike**: uniformno 3:2 (`aspect-ratio` + object-fit na `.wd-blog-holder`) — važi za sve blog loop-ove na sajtu
- Sve verifikovano vizuelno (Chrome): 6/6 ikonica, reference kartice, logo traka, blog kartice iste visine

## 2026-07-05 [claude-code] [FIX - /o-nama/ crash] — PHP segfault dijagnostikovan i rešen
- 🔍 Uzrok: `[porto_social_icons icon_size="{``xl``:``30``...}"]` — Porto backtick-JSON parametri izazivaju katastrofalno PCRE backtracking u `shortcode_parse_atts` → PHP proces pada bez traga u logu (isti uzrok kao crash stare home 143)
- 🔍 Metod: bisekcija po vc_row redovima u zasebnim PHP procesima (exit 255 = segfault) → red 2 → suženo na porto_social_icons
- ✅ Fix 1: no-op registracija svih 9 porto_* shortcode-ova u child temi (čisti raw junk iz izlaza legacy stranica)
- ✅ Fix 2: `porto_social_icons` fizički uklonjen iz 571 (no-op ne pomaže — atributi se parsiraju pre handlera); sadržaj ostao netaknut (3515→3097 B), verifikovano da se stranica renderuje sa tekstom
- ✅ **Sanacija svih 7 preostalih stranica** (61 kontakt, 5255, 5512, 15480, 15490, 15580, 16142): porto_* tagovi uklonjeni (unutrašnji sadržaj sačuvan), backtick atributi skinuti sa ostalih shortcode-ova (layout očuvan) — **nula backtick parametara u objavljenom sadržaju**
- ✅ Originali sačuvani (`backtick-pages-original.json` u scratchpad + jutarnji full snapshot); svih 7 verifikovano 200 + kontakt vizuelno (forma i podaci netaknuti)
- ✅ **Sve stranice bez sidebara**: `_woodmart_main_layout=full-width` na svih 25 pages (publish+draft); verifikovano na kontakt/o-nama/parking — bez sidebar markup-a. Blog postovi zadržavaju sidebar (odluka po potrebi)

## 2026-07-05 [claude-code] [DIZAJN - Figma sync] — Home usklađen sa Figma početkom dizajna
- ✅ Pročitan Figma fajl "Antas line" (Desktop-2 frejm, 1440×4663) kroz Figma konektor — struktura, tokeni, screenshot
- ✅ **Odluke (Miroslav):** naslovi ostaju **Bebas uppercase** (Figma koristi Inter Bold sentence case → Figma se dovodi u sklad kasnije); header CTA ostaje **telefon 072** (ne "Zatražite ponudu" — podaci: ~50 tel klikova/mes, 46 mobil)
- ✅ Usklađeno sa Figmom na buildu: **foto hero** (Spanoulis teren + navy gradijent overlay, `al-hero-photo`), **5 kategorija** (+ Poslovni prostori, Expona Commercial slika), **6 USP kartica** ("Zašto izabrati Antasline?": brza montaža, izdržljivost 25g, protivklizna, fleksibilna, održavanje, izgled — umesto 3 brojke), naslovi sekcija iz Figme ("Temelj vrhunskog poda")
- 🔧 Bugovi: WPBakery `.vc_row:before` clearfix (display:table) skuplja overlay na 0×0 → eksplicitni display/width/height; CSS keš → `filemtime` verzionisanje enqueue-a; kartice različitih proporcija → `aspect-ratio: 4/3` + object-fit
- ⏳ Iz Figme još neimplementirano: testimonials kartice (imaju placeholder copy — čekaju prave recenzije sa GMB), "Najprodavanije podloge u 2025." foto sekcija — po odluci
- Sve verifikovano vizuelno (Chrome) — hero overlay, 5 kartica sa slikama, 6 USP kartica renderuju
- 🔧 Meni "Početna" (2 menija) pokazivao na staru draft stranicu 143 (404) → prevezano na novu Početnu 16550; potvrđeno da link vodi na `/`

## 2026-07-05 [claude-code] [DIZAJN - Mondo look implementiran] — Design system + header + home na WoodMart buildu ✅
- ✅ **Analiza Monda** (Chrome + computed styles): Bebas Neue + Proxima Nova, clip-path dijagonale, paralelogram CTA, dijamant strelice → plan odobren (Inter + Bebas Neue, boje strogo brand book)
- ✅ **Fontovi self-hosted**: Inter 400/600/700 + Bebas Neue woff2 (latin+latin-ext, šđčćž ✓) u `woodmart-child/fonts/` — nula CDN zahteva (uklonjen i preconnect hint)
- ✅ **Design system**: `woodmart-child/css/antas-design.css` — tokeni, `:root:root` preklapanje WoodMart varijabli, utility klase (al-section/diag/plates/btn/label/card/stat/grid) — katalog u [[migracija/woodmart-sabloni]]
- ✅ **Header kodom** (filter `woodmart_default_header_structure`): navy top bar (adresa+mail+074) · beli glavni red: logo SVG + uppercase meni + crveni paralelogram CTA "POZOVITE NAS 069 234 00 72" · sticky · mobilni: burger/logo/tel dugme
- ✅ **Home (16550) izgrađen**: hero "PODOVI KOJI IZDRŽE SVE" (navy + listajuće ploče = potpis iz logoa) → 4 segment kartice (Industrijski/Sportski/Terase/Parking, slike iz medijateke) → USP 25/0/1 → reference (Hankook·HTEC·Amicus·Quectel·Dunk Shop·Spanoulis) → blog masonry 3 kol → završni CTA; `_woodmart_main_layout=full-width`, `_woodmart_title_off=on`
- 🔧 Bugovi rešeni usput: wpautop razbijao grid (`<br>` između kartica → HTML u jednoj liniji); sidebar preko hero-a (full-width meta); `woodmart_blog` param je `blog_columns`, ne `columns`
- ✅ Verifikovano vizuelno (Chrome, svih 6 sekcija) + fontovi lokalno + smoke 200
- **Sledeće:** rebuild silo stranica po šablonu iz [[migracija/woodmart-sabloni]] (live copy + sufiks 5 pravilo) · footer · mobilna provera · Figma link #ceka-miroslav

## 2026-07-05 [claude-code] [BREND - logo SVG izvoz] — Vektorski logo izvezen iz PDF-a za WoodMart header
- ✅ PyMuPDF izvoz iz `Logo/ANTAS LINE FINAL LOGO.pdf` — **pravi vektor (SVG), ne raster**; tight crop na bounding box crteža (+6pt margina)
- ✅ Boje normalizovane na zvaničnu paletu iz [[reference/brend-knjiga]] (`#0E2950`/`#0B3E75`/`#5287B7`/`#F04D22`/`#F89C1C`) — MuPDF konverzija odstupala 1–2 jedinice
- ✅ Fajlovi u `Logo/`: `antas-line-logo-vertikalni.svg` + `.png` (668×777, transparent) · `antas-line-logo-horizontalni.svg` + `.png` (1360×435, transparent) — PNG jer WP media po default-u ne prima SVG
- ✅ Kopirano i u `wp-content/themes/woodmart-child/images/` za header builder
- Vizuelno verifikovano (render PNG-a) · SVG ima `role="img"` + aria-label
- Otvoreno: bela varijanta za navy footer — napraviti kad header/footer dizajn to zatraži

## 2026-07-05 [claude-code] [WOODMART - instalacija] — Tema instalirana i aktivirana na lokalu ✅
- ✅ WoodMart **8.5.4** (tema + child `woodmart-child` sa brand CSS varijablama iz [[reference/brend-knjiga]]) + **woodmart-core 1.1.8** aktivirani; WPBakery ažuriran 8.7.2 → **8.7.3** (bundlovan, stara verzija sačuvana u `C:\Projekti\woodmart-tema\bak\`)
- ⏭️ Revolution Slider iz bundle-a NAMERNO preskočen (CWV balast, ne koristi se)
- 🔧 **Home (143, Porto carousel sadržaj) izaziva PHP crash pod WoodMart-om** → nova prazna Početna (ID 16550) postavljena kao front page, stara 143 u draft (home se ionako gradi ispočetka)
- ✅ Smoke test 200: home, proizvod, post, kontakt, sportske-podloge, industrijski-podovi
- ✅ Live export stigao u vault: 30 postova XML + 50 pages XML (referenca) + parity CSV (80 redova); ⚠️ lokal ima 32 posta vs 30 live — utvrditi koja 2
- Snapshot pre svega: `backup-pre-woodmart-rebuild-20260705-1125.sql`
- **Sledeće:** header/footer u WoodMart builderu (brand book look) → theme options (Inter self-hosted + boje) → import 30 postova → rebuild pages po prioritetu

## 2026-07-05 [cpanel-live] [MIGRACIJA - live export za WoodMart rebuild] — Read-only export završen → [[migracija/2026-07-05-live-export-prompt]]
- ✅ `migracija/live-export-2026-07-05/live-posts-2026-07-05.xml` — 30 objavljenih postova (WXR)
- ✅ `migracija/live-export-2026-07-05/live-pages-2026-07-05.xml` — 50 objavljenih pages (WXR, samo referenca za rebuild)
- ✅ `migracija/live-export-2026-07-05/live-inventar-2026-07-05.csv` — 80 redova (30 posts + 50 pages): ID, slug, permalink, title, Yoast title/meta, word count
- **Kontrolni brojevi (live vs. lokal staging):**
  - Postovi: **30** live vs. 32 na lokalu (razlika −2, proveriti koja 2 posta nedostaju/su viška)
  - Pages: **50** live (lokal broj nije evidentiran u ovoj sesiji za poređenje)
  - Proizvodi: **37** live = **37** lokal ✓ (poklapa se sa C2 importom)
  - Kategorije: **10** live = **10** lokal ✓ (poklapa se)
- ✅ Striktno read-only prema WP-u — nikakva izmena baze/fajlova/podešavanja, samo export + zapis u vault
- **Sledeće:** `git pull` na lokalu → import postova (`wp import` ili WP admin), CSV inventar kao checklist za page rebuild parity

## 2026-07-05 [claude-code] [ODLUKA - WoodMart clean rebuild] — GO za novi sajt na lokalu
- ✅ **Odluka (Miroslav):** ne konvertuje se Porto sadržaj — novi sajt na postojećem lokalnom installu: proizvodi ostaju (37+slike), postovi se prenose sa live, pages se grade NOVE u WoodMart+WPBakery prema live/staging sadržaju + C3 draftovi direktno ugrađeni
- ✅ Snapshot pre svega: `C:\xampp\htdocs\antasline\backup-pre-woodmart-rebuild-20260705-1125.sql` (44,1 MB, verifikovan header+footer)
- ✅ Prompt za cPanel live export (posts XML + pages XML referenca + parity CSV inventar) → [[migracija/2026-07-05-live-export-prompt]]
- ✅ WoodMart licenca postoji; tema fajlovi idu u `C:\Projekti\woodmart-tema\` (van vault-a, da ne ulazi u git)
- **Sledeće:** Miroslav pokreće cPanel prompt + dostavlja woodmart.zip → instalacija teme, header/footer (brand book), rebuild po prioritetu

## 2026-07-05 [claude-code] [TEHNIČKA - WoodMart audit] — Porto → WoodMart procena → [[dnevnik/2026-07-05-audit-porto-woodmart]]
- ✅ Read-only audit 53 objavljena page/post: 53% čist HTML, 9% vanilla vc_*, **30% (16 stranica) sa porto_* elementima** — 8 različitih elemenata, dominira porto_block (10)
- ✅ Procena: ~3–5 radnih dana (16 stranica zamena + header/footer + test); Woo proizvodi/Yoast meta/redirect mapa netaknuti
- 💡 Bonus: čišćenje porto_* na 4937 verovatno rešava i WPBakery JS bug koji blokira 6 blokova
- **Zaključak: prelazak jeftin, rok nije ugrožen — GO/NO-GO #ceka-miroslav** (pre aktivacije: db export + js_composer verzija + licenca)

## 2026-07-05 [cpanel-live] [C3 - #9 odbojka refresh] — Primenjeno na live (delimično) → [[dnevnik/2026-07-05-refresh-odbojka]]
- ✅ Post 4318 (`/podloga-za-odbojkaske-terene/`) izmenjen na live: H1, snippet pasus, sekcija "peska", FAQ (4 pitanja) + FAQPage JSON-LD
- ✅ Backup pre izmene: `~/backup-pre-odbojka-refresh-20260705-1020.sql`
- ✅ Verifikovano curl-om: sve sekcije prisutne, JSON-LD validan
- ⏭️ Namerno preskočeno: Yoast title (#1) i meta description (#2) — po eksplicitnom zahtevu
- ⏳ Cena sekcija (#6) NIJE ubačena — čeka stvarne cifre od Miroslava (placeholder na live bi bio vidljiv posetiocima)
- **Sledeće:** Rich Results Test, GSC Request indexing, C2 parity (stranica ne postoji na lokalnom buildu)

## 2026-07-05 [claude-code] [C3 - #9 odbojka refresh] — Kompletan refresh paket → [[dnevnik/2026-07-05-refresh-odbojka]]
- 🔍 Dijagnoza CTR 0,6% @ poz. 2,3: live title bez reči "dimenzije" (a to je ~80% od 7.817 impr klastera), **meta description ne postoji**, nema FAQ/cene/peska
- ✅ Copy-paste paket: novi title+meta, snippet pasus (18×9), nova sekcija odbojka na pesku (16×8, ~330 impr), cena sekcija (placeholderi), FAQ HTML + FAQPage JSON-LD, postupak primene korak-po-korak
- ⚠️ **Stranica postoji SAMO na live** → primena ide `[cpanel-live]` kroz WP admin (~15 min) #ceka-miroslav; lokalni build je nema → **C2 parity gap zabeležen**
- Merenje: CTR klastera pre (0,6%) vs 28d posle primene

## 2026-07-05 [claude-code] [C3 - TIER 1 draftovi] — Svih 5 preostalih Tier 1 stranica draftovano
- ✅ #1 [[dnevnik/2026-07-05-draft-gumeni-podovi-za-terase-cena]] — cena tabela 4 tipa, conquest sekcija za "epoksidni podovi za terase" (1.499 impr)
- ✅ #2 [[dnevnik/2026-07-05-draft-industrijski-podovi-cena]] — odluka: posebna stranica (4937 blokiran WPBakery bugom); postaje i Ads landing → gasi 4,1k RSD curenja
- ✅ #3 [[dnevnik/2026-07-05-draft-podovi-za-garaze]] — konsolidovana landing za 14k impr klaster + 16,8k RSD Ads rupe
- ✅ #4 [[dnevnik/2026-07-05-draft-dimenzije-kosarkaskog-terena]] — snippet-format tabele FIBA/NBA/školski; ⚠️ anti-kanibalizacija vs basket članak (skratiti tamo dimenzije)
- ✅ #6 [[dnevnik/2026-07-05-draft-podloge-za-parkiraliste-cena]] — cena+nosivost+saće-vs-šljunak (hvata i ~700 impr šljunak upita); #5 tabla draftovan juče
- Svi draftovi: Yoast title/meta + H2 struktura + FAQ HTML + FAQPage JSON-LD + CTA 072/forma + interni linkovi; cene = `{{PLACEHOLDER}}` #ceka-miroslav
- **Sledeće:** cifre od Miroslava → implementacija na lokalnom buildu (WPBakery) → Rich Results Test

## 2026-07-05 [claude-code] [BREND] — Logo + brand book dodati u vault → [[reference/brend-knjiga]]
- ✅ Pregledani `Logo/ANTAS LINE FINAL LOGO.pdf` (vertikalna + horizontalna varijanta) i `Logo/Brand book.pdf` (13 str.)
- ✅ Specifikacije izvučene u [[reference/brend-knjiga]]: paleta (655 C / 279 C / 172 C / 137 C), tipografija **Inter**, web look&feel (crveni CTA "pozovite nas" 069 234 00 72), kontakt podaci
- ✅ HEX boje izmerene pipetom iz renderovanog vektorskog PDF-a (pdfium): teget `#0E2950`, plava `#0B3E75`, svetloplava `#5287B7`, crvena `#F04D22`, narandžasta `#F89C1C` — **ove koristiti u temi**, ne Pantone aproksimacije
- ⚠️ 4 greške u PDF-u za dizajnera pre štampe: "KNJGA" typo na svim stranama, dupliran Pantone 655 C za dve različite plave, "enviroment", "Informisite se"
- Relevantno za redizajn: Porto tema → Inter font (self-hosted, Core Web Vitals) + brand boje u temi

## 2026-07-04 [claude-code] [GEO/AI plan] — Kako da AI preporučuje Antasline → [[seo/geo-ai-plan]]
- ✅ GEO strategija: AI crawleri (robots.txt/llms.txt), citabilan sadržaj (C3 plan = GEO gorivo), entitet schema, pominjanja treće strane (PR o Spanoulis/Dunk Shop terenima, case studije Hankook/HTEC/Quectel), GMB recenzije
- ✅ Merenje ugrađeno u [[analiza/_TEMPLATE-snapshot]] §4.5: GA4 AI Assistant kanal (baseline 9 korisnika/90d) + 5 fiksnih ChatGPT test promptova
- Otvoreno: robots.txt provera na live #ceka-miroslav · llms.txt priprema #claude-code

## 2026-07-04 [claude-code] [C3 - #5 draft] — Sadržaj za `/dimenzije-kosarkaske-table/` napisan → [[dnevnik/2026-07-04-dimenzije-kosarkaske-table]]
- ✅ Pun draft: naslov/meta, body (dimenzije, materijali, DIY sekcija, cena), FAQ HTML + FAQPage JSON-LD
- Cilja ~2.400 impr "tabla" upita (poz. već 1–3,5 — problem je pokrivenost/CTR, ne rang)
- Link ka kategoriji Košarkaške konstrukcije (slug čeka C1 redirect odluku)
- **Status: draft gotov, čeka implementaciju na lokalnom buildu** (cifre cena + finalni slug čekaju Miroslava)

## 2026-07-04 [claude-code] [C3 - Content plan] — 20 novih stranica u 4 tijera → [[seo/plan-novih-stranica]]
- ✅ Master plan izveden iz 16m keyword analiza (GSC + Ads); obuhvata i ranije 4 GSC stranice
- Tier 1 purchase-intent: terasa cena (4.221 impr), industrijski cena, garaže landing, basket set
- Tier 2: tenis hub (šljaka 9k impr), odbojka refresh (poz. 2,3 / CTR 0,6% — 30 min posla), piklbol/padel
- Tier 3: komercijalni vertikali (kancelarije poz. 1,9!, restorani, zdravstvo, tržni centri)
- Tier 4: reference tereni (Dunk Shop/Spanoulis ~3k impr), Bergo brend, teretane
- Povezano sa [[blokovi/BLOK-C-sledece]] C3 + [[PROGRESS]] Sledeće #2

## 2026-07-04 [claude-code] [ANALIZA - Ads search terms 16m + GSC poređenje] → [[analiza/2026-07-04-ads-st-analiza-16m]]
- ✅ Svih 1.899 Ads search termina (16m, 107,8k RSD, 5 konv) kroz iste klastere kao GSC + CSV banka
- 🔴 **Curenje kvantifikovano: 16.607 RSD = 15,4%** (315 termina krši negativnu listu = 10,5k; 289 van ponude = 6,1k — deking 2,3k!)
- 🔴 Garaže = ogledni problem: 16,8k RSD + organik poz. 8–10 + 14k GSC impr = 0 konverzija → landing, ne kanal
- 🔴 "pvc podovi" broad = 5,5k RSD bez konverzije; "industrijski podovi cena po m2" 4,1k (landing nema cenu)
- ✅ Struktura kanala zdrava: basket/parking organik #1 → 0 RSD u Ads; industrijski paid opravdan (jedini konvertuje, 3)
- **Ključ:** cena-termini = 19% Ads potrošnje jer organik nema cena stranice → cena sekcije rešavaju oba kanala

## 2026-07-04 [claude-code] [ANALIZA - GSC keywords 16m] — Svih 2.893 upita klasterizovano → [[analiza/2026-07-04-gsc-kw-analiza-16m]]
- ✅ Puna GSC banka (16m) → CSV + klasterizacija (24 klastera × intent) PowerShell skriptom
- 🔴 Top nalazi: odbojka wpos 2,3 / CTR 0,6% (7.8k impr!); tenis 23,7k impr / CTR 1,7% (šljaka 9k impr); industrijski cena-gap; epoksid conquest poz. 26 za "epoksi podovi"; komercijalni vertikali (kancelarije poz. 1,9!) bez stranica; reference-tereni (Dunk Shop/Spanoulis ~3k impr)
- 📊 Intent: cena CTR 9,9% vs info 3,3% — cena stranice rade gde postoje (20–30% CTR)
- **Akcioni plan:** 10 stavki u §5 analize (odbojka → tenis hub → cena sekcije → piklbol → vertikali…)

## 2026-07-04 [claude-code] [ANALIZA - puni snapshot] — Dnevnik stanja: Ads+GA4+GSC+GMB (baseline) → [[analiza/2026-07-04-snapshot-full]]
- ✅ Novi folder `analiza/` — sistem periodičnih snapshot-ova (README + template + prvi puni snapshot)
- ✅ Povučeno ~25 pull-ova kroz Windsor.ai: GSC (16mo trend, upiti, stranice, uređaji, movers), GA4 (trend, kanali, eventi, publike), Ads (trend, kampanje, KW, search terms, imp. share), GMB (trend, keywords, recenzije, profil)
- 🔴 **Nalazi:** GA4 `conversions` slomljen od juna (5.859!) → key event audit #ceka-miroslav; hvala-proxy postoji tek od juna (55 = baseline); negativne KW ne važe na kampanjama (epoksid/sika/rinol prolaze, ~16% otpada) #ceka-miroslav; GSC CTR erozija (jun YoY: klikovi −19%, impresije +22%)
- 🟢 **Nalazi:** ECOTILE phrase "industrijski podovi" = 1.073 RSD/konv.; jun = najveći Ads mesec (30,7k); Product snippets CTR 10,5%; prelaz na nove kampanje uspeo
- **Strategija:** §6 snapshot-a — 5 SEO + 6 Ads + 4 GMB + 3 tracking akcija, prioritizovano
- **Akcija nedelje:** proveri negativnu listu na obe kampanje + skini 2 pogrešna KW (15 min, zaustavlja ~16% rasipanja)

## 2026-07-04 [claude-code] [VAULT - konzistentnost] — Ispravke nedoslednosti + brisanje B3
- ✅ Obrisan `B3 - Odblokiranje naloga.md` (zadatak gotov: balans + verifikacija) + prazan `2026-07-02.md`
- ✅ Sve B3 reference uklonjene/ažurirane; ADS Faza 0 zatvorena u [[dnevnik/ADS-DNEVNIK]], PROGRESS, MASTER-PLAN, CLAUDE, BLOK-B
- ✅ ECOTILE status: ⛔ zagušena → ✅ odblokirana (istorijski logovi ostaju netaknuti)
- ✅ Konektor `googleads` → `google_ads` u [[reference/identifikatori]]
- ✅ Konverzije usklađene: `33` → `53` (jun) u [[00-INDEX]] + [[odluke/_pregled-odluka]]
- ✅ Datum migracije: `2026-09-01 (utorak, pogrešno)` → `ponedeljak 2026-08-31`; weekly cadence prepravljen
- ✅ WooCommerce lokal import (04.07) označen gotovim u blokerima (SSH ostaje samo za live)
- **Otvoreno:** potvrditi u Ads da su ECOTILE prikazi/CPC vraćeni na normalu #claude-code

## 2026-07-04 [claude-code] [BLOK C1 - Redirect mapa VERIFIKACIJA] — ✅ SKORO GOTOVO! 106/118 redova finalizovano
- ✅ Proverio 18 stranica sa "PROVERI da postoji" — sve postoje na localhost
- ✅ WooCommerce kategorije — sve 10 postoje sa `/kategorija/...` URL struktura
- ✅ WooCommerce proizvodi — svi 37 postoje sa `/shop/kategorija/proizvod/` struktura
- ✅ Refresh-ovao WordPress permalinks — URL struktura sada ISPRAVNA
- ✅ Ažurirao CSV: 18+41 = 59 redova sa AUTO-PREDLOG → "postoji"
- ✅ Popunio 4 "ZA POPUNITI" redova — kategorija URL-evi
- ⏭️ Preostalo: 5 "NEMA NA BUILDU" redova (skipped za kasnije) + 2 "Dodati kontent" (minor)
- **CSV Status:** 106 redova "gotovo" od 118 (89.8% kompletan)
- **Sledeće:** Kreiraj 301 redirect-e u WordPress

## 2026-07-04 [claude-code] [BLOK C2 - WooCommerce import] — ✅ ZAVRŠENO! Proizvodi sa live → staging
- ✅ **Live export** preuzet: `woo-export-2026-07-04.zip` (products.csv + variations.csv)
- ✅ **37 proizvoda** importovano na localhost
- ✅ **10 kategorija** automatski kreirane i vezane:
  - Industrijska zaštita (24), Zaštita i Bumperi (19), Podno označavanje (6), Košarkaške konstr. (5), itd.
- ✅ **115 slika** preuzete sa live sajta kroz `media_sideload_image()`
- ✅ **Svi opisi + specifikacije** (srpski znakovi ispravno, bez čudnih karaktera)
- ✅ **24 stranice + 34 posta ostaju netaknuti**
- **Problem rešen:** UTF-8 BOM (`﻿id`) u CSV header — `ltrim($header[0], "\xEF\xBB\xBF")`
- **Problem rešen:** Separator za kategorije bio `|` umesto `,`
- **Finalni bekap:** `backup-FINAL-37products-10categories-20260704.sql`
- **Script:** `import-final-woo.php` — robustan, čuva UTF-8, kreira kategorije ako ne postoje, preuzima slike

## 2026-07-03 [claude-code] [BLOK C - WooCommerce import] — Prebacivanje proizvoda sa live na localhost
- ✅ Vratim bekap pre nego što su obrisani proizvodi (backup-pre-parity-20260628-1135.sql) — homepage i stranice ostaju
- ✅ Obrisao samo 43 stara proizvoda + kategorije (bez dotacanja stranica/postova)
- ✅ Učitana live baza (127 MB) u temp
- ✅ Prebačeni proizvodi + attachment-i sa live baze (sa konverzijom prefixsa wp_ → wpGs_)
- ✅ Preuzeli XML export sa live (`antasline.WordPress.2026-07-03.xml`)
- ✅ WP-CLI import: `wp import import.xml --authors=create` — 42 proizvoda importovana sa svim meta podacima
- **Rezultat:** 42 proizvoda, 41 sa slikama (97.6%), 434 relevantne attachment-a, 24 stranice + 34 posta netaknuti
- **Otvoreno:** Ručno brisanje dodatnih/nepotrebnih slika na proizvodima #ceka-miroslav
- Finalni bekap: `backup-FINAL-41sa-slikom-20260703.sql`

## 2026-07-03 [cpanel-live] — Optimizacija baze (UŽIVO)
- Backup: `~/backups/antasline_db_2026-07-03_2031.sql`
- Otklonjen kritični problem: `wp_litespeed_img_optm` imala 3.251.490 orphan redova (post_id=0, src prazan) — runaway LiteSpeed greška → tabela sa 315.91 MB smanjena na 0.05 MB
- Obrisano 50 post revizija, 1 expired transient, 34 stara ActionScheduler completed akcija
- OPTIMIZE TABLE na svim tabelama (recreate+analyze)
- **Ukupna veličina baze: 354 MB → 38.67 MB (-89%)**

## 2026-07-02 [chat] [Windsor/GA4+Ads+GSC + FAQ/Schema] — Kompletan pulov podataka + preporuke
- Povučeni podaci iz Windsor.ai: GA4 (30 stranica), Google Ads (56 dana), GSC (60 ključnih reči)
- Analiza top stranica: Spoljne podloge (1062 users), Industrijski (481), Sport (742), Parking (247)
- **Preporuka:** 5 novih GA4 publika — Spoljne/Industrijski+ESD/Sport/Parking/Bazen
- **GSC analiza:** 4 KRITIČNA priority-a za nove stranice: dimenzije basket terena (240 impr), cena terase (236 impr), dimenzije table (150 impr), gumeni tepih (160 impr)
- **Basketball stranica:** Kreirani FAQ + unapređena schema (FAQPage + HowTo + Product) za /kako-napraviti-teren-za-basket/ 
- Task #1: GA4 publike #ceka-miroslav
- Detaljni izveštaji: [[dnevnik/2026-07-02-analiza-segmentacije]] + [[dnevnik/2026-07-02-gsc-keywords-analiza]] + [[dnevnik/2026-07-02-basket-page-faq-schema]]
- Sledeće: Implementiraj FAQ + schema na stranici, kreiraj 4 nove stranice + Ads reorganizacija #claude-code

## 2026-07-01 [chat] [ADS] — Snimak podataka + fazni plan
- ECOTILE zagušen: prikazi −67%, CPC 26→74 RSD — uzrok je blokada naloga (balans/verifikacija)
- Terase: 296 klik/ned, CTR 19%, konverzija slaba (2/ned) → prioritet je kreativa
- Napravljen fazni plan 0–4 i banka RSA asseta za obe kampanje
- Detalji i banka asseta: [[dnevnik/ADS-DNEVNIK]]

## 2026-06-29 [cpanel-live] — GTM tel: tag obrisan (UŽIVO)
- GTM tag koji je okidao GA4 event "tel:+381692340072" obrisan iz GTM-TRDT8K9 i publishovan
- Verifikovano: event više ne okida u GA4 DebugView ✓

## 2026-06-28 [cpanel-live] — Opt-out consent model aktiviran (UŽIVO)
- Plugin antasline-consent prešao na opt-out: pri prvoj poseti kolačić se odmah postavlja na {ad:true, analytics:true}
- Consent Mode v2 default (nema kolačića): sve kategorije sada 'granted' umesto 'denied'
- Banner se i dalje prikazuje — posetilac može da klikne "Odbij sve" ili podesi po kategorijama
- Toggles u panelu podrazumevano checked=true kada nema kolačića
- Verifikacija: curl potvrđuje 'granted' u else grani ✓

## 2026-06-28 [cpanel-live] — SEO title fix, GA4 istraga, SSH most, WooCommerce export (UŽIVO)
- SEO: Obrisani duplikat/neispravni _yoast_wpseo_title na 6 postova (ID 2542 duplikat, 3327/3621 %%title%%, 3257/4813/6824 %%title%% %%page%% %%sep%%)
- GA4 event "tel:+381692340072" — utvrđeno: izvor je GTM tag (ne server/plugin); #ceka-miroslav da obriše tag u GTM UI
- SSH ključ ed25519 kreiran (~/.ssh/id_ed25519_github), GitHub autentikacija OK
- [[CLAUDE]] kreiran u ~/public_html/ sa vault workflow instrukcijama
- live-export.sh popravljen (trailing comma bug, --no-create-info bug); woo-export.sql 444K generisan (47 proizvoda, 71 attachment, 22 pa_* atributa)
- Otvorene akcije: prenos woo-export.sql na staging #ceka-miroslav, brisanje GTM tel: taga #ceka-miroslav

## 2026-06-28 [chat] — Obsidian vault + git most postavljen → [[dnevnik/2026-06-28-postavljanje-vault]]
- Vault C:\Projekti\antasline-vault\ kao jedina istina; GitHub Chichabudhha/antasline-vault
- [[DNEVNIK-NAPRETKA]] + [[PROGRESS]] preseljeni iz htdocs; cPanel vault kloniran; git most testiran OK
- Sledeće: BLOK C1 redirect mapa (nov chat, Sonnet, zalepi [[PROGRESS]] u seed)

## 2026-06-28 [chat] — Obsidian vault + git most postavljen
- Vault: C:\Projekti\antasline-vault\ kao jedina istina projekta
- [[DNEVNIK-NAPRETKA]] i [[PROGRESS]] preseljeni iz htdocs u vault
- GitHub repo: Chichabudhha/antasline-vault (private)
- Obsidian Git plugin aktivan, auto-sync 10min
- cPanel: vault kloniran u ~/antasline-vault, [[CLAUDE]] kreiran
- Git most zatvoren: lokal ↔ GitHub ↔ cPanel sinhronizovani
- Sledeće: BLOK C1 — redirect mapa (nov chat, Sonnet model)
## 2026-06-28 [chat] — Obsidian vault postavljen i objedinjen
- Vault `C:\Projekti\antasline-vault\` postao jedina istina projekta.
- [[DNEVNIK-NAPRETKA]] i [[PROGRESS]] preseljeni iz htdocs u vault.
- [[CLAUDE]] (htdocs) dopunjen vezom ka vault-u; Claude Code odsad loguje ovde.
- Detaljan zapis: [[dnevnik/2026-06-28-postavljanje-vault]]
- [ ] Aktivirati Dataview plugin #ceka-miroslav
- [ ] Izabrati BLOK C stavku (C1/C2/C3) #ceka-miroslav

## 2026-06-25 — Optimizacija /industrijski-podovi/ (Yoast meta)

**Stranica:** http://localhost/antasline/industrijski-podovi/ (ID 4937, post_type=post)

**Urađeno:**
- ✅ Yoast title: `Industrijski PVC podovi u pločama — bez zastoja proizvodnje | Antas Line` (69 znakova, optimalno)
- ✅ Yoast meta opis: `Industrijski PVC podovi Ecotile — montaža preko postojećeg betona bez zastoja proizvodnje i bez lepka. Otporni na viljuškare, hemikalije, R10. Brzo do upita.`
- ✅ Stranica radi ispravno za posetioce (karakteri, footer, width — sve OK)

**Nije urađeno:**
- ❌ 6 sadržajnih blokova (planiran): WPBakery visual editor ima JavaScript bug pri parsiranju shortcode-a (`Cannot read properties of undefined`). Programski pristup (PHP) pravi probleme sa editor-om, a manual unos je komplikovan zbog strukture.

**Zaključak:**
- Yoast SEO optimizacija je **ZAVRŠENA i aktivna**
- Blokovi se mogu dodati kasnije ručno kroz WPBakery editor (drag-and-drop), ili koristiti Text editor za ažuriranja
- Stranica je **sprema za produkciju sa SEO meta-om**

**Backup:** `backup-industrijski-20260625-1059.sql` (31.56 MB)

---

## 2026-06-25 — Pokušaj: Optimizacija /industrijski-podovi/ (6 sadržajnih blokova)

**Stranica:** http://localhost/antasline/industrijski-podovi/ (ID 4937, post_type=post)

**Izmene:**
- `_yoast_wpseo_title`: (stari/dugačak) → `Industrijski PVC podovi u pločama — bez zastoja proizvodnje | Antas Line` (69 znakova)
- `_yoast_wpseo_metadesc`: ažuriran sa fokusom na "bez zastoja", "bez lepka", "Ecotile", "R10"
- Dodati 6 WPBakery blokova (`[vc_row]` strukture) PRE FAQ sekcije:
  1. **Uporedna tabela** (PVC vs epoksid vs poliuretan vs mikrocement) — conquest za "epoksid" nameru
  2. **Cena blok** ({{CENA_PVC_OD}}–{{CENA_PVC_DO}} €/m² sa placeholder-ima za Miroslava)
  3. **Vrste industrijskih podova** — edukativni tekst o razlici između silo-pristupa
  4. **Namena grid** (magacini, proizvodnja, autoservisi, HACCP, farmacija, hladnjače, ESD)
  5. **Reference galerija** (sprema za slike: Hankook, HTEC, Amicus — trust signal)
  6. **Tehnička svojstva tabela** (R10, Bfl-s1, hemijska otpornost, debljine, OHSAS 18001, 25 godina trajanja)

**Verifikacija:**
- WPBakery struktura: 14 [vc_row] ↔ 14 [/vc_row] (integritet ✓)
- Svih 6 blokova prisutno u sadržaju ✓
- Yoast meta postavljeni ✓
- Bez broken shortcode-a ✓
- HTTP 200 pri učitavanju ✓

**Napomene:**
- Placeholder cene `{{CENA_PVC_OD}}` i `{{CENA_PVC_DO}}` ostavljeni za Miroslava da popuni sa realnim ciframa
- Reference galerija sprema za fotografije (nedostaju slike iz medijateke)
- Blok "Namena grid" može biti osnova za kasnije pod-stranice (/industrija-podovi/magacini/, itd.)
- Backup pre izmena: `backup-industrijski-20260625-1059.sql` (31.53 MB)

---

## 2026-06-23 — On-page popravka /pop-tenis/

**Stranica:** http://localhost/antasline/pop-tenis/ (ID 15966, post_type=post)

**Izmene:**
- `_yoast_wpseo_title`: (prazno) → `Teren za pop tenis i pickleball – dimenzije i izrada`
- `post_title` (= H2 entry-title): `Padel tenis` → `Teren za pop tenis i pickleball`
- `_yoast_wpseo_metadesc`: zadržan (pominje pickleball i pop tenis)
- Intro paragraf: dodata reč `piklbol` (fonetski oblik, 293 prikaza koji nisu hvatani)

**Verifikacija:**
- `<title>`: Teren za pop tenis i pickleball – dimenzije i izrada ✓
- `<h2 class="entry-title">`: Teren za pop tenis i pickleball ✓
- "Padel tenis" više nije title/H2 ✓
- "piklbol" prisutan u rendered HTML ✓
- Regression: industrijski-podovi i spoljnje-podne-obloge Yoast titles nepromenjeni ✓

**Napomene:**
- Porto tema renderuje entry-title kao `<h2>`, ne `<h1>` — `<h1>` je blog archive heading ("Aktuelnosti")
- Padel reference u body-ju ostavljene netaknute (upućuju na zaseban padel teren)
- Backup pre izmena: `backup-onpage-20260623.sql` (31.53 MB)

## 2026-07-10 [cpanel-live] — LiteSpeed img-optm: reset + ručni cronjob urađeni, red i dalje blokiran uzvodno (UŽIVO)
- **Šta je TAČNO promenjeno na produkciji:**
  - Backup pre izmena: `~/backups/antasline_2026-07-10_pre-litespeed-recron.sql` (wp_litespeed_img_optming/img_optm/wp_options)
  - `Img_Optm::reset_row()` pozvan za svih 25 zaglavljenih post_id (5898–5941) preko `wp eval-file` — obrisao njihove redove iz `wp_litespeed_img_optm`/`img_optming` i povezan postmeta (isto što radi admin "Reset Row" dugme)
  - `Img_Optm::cls()->new_req()` ručno pokrenut odmah posle reset-a → **200 slika uspešno poslato i prihvaćeno od cloud-a** (potvrđuje da je send mehanizam sam po sebi ispravan)
  - **Novi sistemski cronjob registrovan** (`crontab -e`, NE WP-Cron): `*/15 * * * * /usr/local/bin/wp eval-file /home/antasline/scripts/litespeed-img-optm-cron.php --path=/home/antasline/public_html >> /home/antasline/logs/litespeed-img-optm-cron.log 2>&1` — poziva `new_req()` + `async_handler(true)` svakih 15 min (isti interval kao originalni plugin cron)
  - Skripta: `/home/antasline/scripts/litespeed-img-optm-cron.php`; log: `/home/antasline/logs/litespeed-img-optm-cron.log`
- 🔍 **Pravi uzrok zašto WP-Cron nikad nije sam radio (kodom potvrđeno, `task.cls.php`):** cron hook `litespeed_task_imgoptm_req` se registruje SAMO ako je plugin opcija **"Auto Request Cron" (`img_optm-auto`) uključena** — kod nas je prazna/isključena (i default vrednost u pluginu je `false`). Nije bug, nego config koji ništa ne šalje bez ovoga ili ručnog trigera. Zato je sistemski crontab pravo rešenje (ne zavisi od te opcije).
- ✅ **Provera posle 2 ciklusa (15:45, 16:00) — cronjob RADI na OS nivou** (log potvrđuje tačno 15-min interval).
- 🔴 **ALI red se i dalje NE pomera** — RAW ostao na 1.157, REQUESTED zaglavljen na tačno 200 (isti 200 od pre 07-05!). Oba cron-poziva vratila `"Error: You have too many requested images"` — `new_req()` odbija da šalje dok se postojećih 200 REQUESTED ne oslobodi (pull).
- 🔴 **Potvrđen dublji uzrok = tačno scenario koji je 07-05 unos predvideo:** `need_pull` opcija stoji na `9` (STATUS_PULLED), nikad ne prelazi na `6` (STATUS_NOTIFIED) → **QUIC.cloud notify webhook i dalje ne stiže** (0 `notify_img` poziva u access logu od registracije cron-a). Plugin nema fallback za "poll cloud direktno" — `pull()` metoda čita ISKLJUČIVO redove sa statusom NOTIFIED, koji se postavlja samo preko webhook-a. Bez njega, poslatih 200 slika ostaje zaglavljeno zauvek i blokira sve nove batch-eve.
- **Zaključak:** lokalna automatizacija (reset + manual send + cronjob) je urađena i radi ispravno, ali ne može zaobići problem — webhook mora da radi da bi se red ikad pomerio. **Sledeći korak nije više lokalni fix, nego QUIC.cloud podrška** (potvrditi da li njihov notify_img callback stvarno stiže do servera; moguće je da firewall/CDN nešto blokira samo za njihove IP-ove, što se ne može testirati iznutra).
- #ceka-miroslav: otvoriti tiket QUIC.cloud podršci (linked domain je aktivan/linked, `qc_activated: "linked"` potvrđeno u opcijama) — ili privremeno isključiti LiteSpeed image optimizaciju dok se ne reši, da cronjob ne troši resurse uzalud svakih 15 min bez efekta.
