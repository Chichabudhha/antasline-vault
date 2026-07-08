# SEO Progress — Antasline lokalni build

> **Plan projekta:** [[2026-07-06-MASTER-PLAN-V2]] (5 workstream-ova, 8 nedelja do migracije 2026-08-31, gate kriterijumi) — stari [[2026-07-02-MASTER-PLAN-DO-LIVE]] je superseded.

## Urađeno

| Datum | Stranica | Šta |
|---|---|---|
| 2026-07-08 | W1 F7 P4 — video lite-embed (2 stranice) — **F7 AUDIT POTPUNO ZATVOREN** | ✅ Ecotile ugradnja (`/industrijski-podovi/`) + Bergo sportski teren (`/sportske-podloge/`), oba oEmbed-potvrđena zvanična kanala. F7.3 lite-embed fasada + VideoObject schema. Debug nalaz: `loading="lazy"` se ne okida u automatizovanom Chrome tabu (potvrđeno preexisting i na antistatik pilotu, nije bug) — funkcionalnost potvrđena direktnim klikom (iframe kreiran ispravno na obe). Backup pre izmena, regresija čista. **P1–P4 svi zatvoreni** — sledeći W1 fokus: `migracija/w1-red-cekanja.md`. Detalji: [[DNEVNIK-NAPRETKA]] |
| 2026-07-08 | W1 F7 P3 — 4 nove antas-skice (SVG tehničke ilustracije) | ✅ Dimenzije terena (top-down FIBA 28×15m), dimenzije table (front-view 1,80×1,05m), industrijski pod (presek slojeva), Bergo (klik-sistem presek) — sve po F7.4 stilu, inline SVG preko `$wpdb->update`. 2 sitna vizuelna bug-fixa nakon Chrome provere (viewBox preuzak na tabla dijagramu, nagurane labele na Bergo dijagramu). Verifikovano HTTP+vizuelno (Chrome screenshot) na sve 4. Backup pre izmena. **F7 P3 zatvoren**, ostaje samo P4 (video, niži prioritet). Detalji: [[DNEVNIK-NAPRETKA]] |
| 2026-07-08 | W1 F7 P2 — standardi sa linkovima (9 stranica) + P1+P2 kombinovani test | ✅ 11 standarda linkovano (FIBA/ITF/EN1270/EN14877/DIN51130/EN14041/ISO10140/ISO9001/ISO14001/EN660-2/ISO6721), svi izvori potvrđeni WebSearch-om pre upisa, `str_replace` na unique anchor + `$wpdb->update`. Kombinovani test P1+P2 na svih 13 stranica: 200/1×H1/schema bez dupliranja/linkovi validni/regresija čista. Backup pre izmena. **F7 P2 zatvoren**, sledeći P3 (skice). Detalji: [[DNEVNIK-NAPRETKA]] |
| 2026-07-08 | W1 F7 P1 — popravka izgubljene FAQPage/Product schema (7 stranica) | ✅ `/industrijski-podovi/` (7 Q&A+Product AggregateOffer), `/spoljnje-podne-obloge/` (5 Q&A + čišćenje 1.321 znaka gole JSON teksta van shortcode-a), `/dimenzije-kosarkaske-table/` (5 Q&A, prvi put), 4 Woo kategorije 245/246/251/252 (po 3 Q&A). Upis isključivo `$wpdb->update`+`clean_post_cache()`. Verifikovano: 200/1×H1/JSON-LD bez dupliranja/regresija 245↔246,251↔252 nisu pomešani. Backup pre izmena. **F7 P1 zatvoren**, sledeći P2 (standardi-linkovi). Detalji: [[DNEVNIK-NAPRETKA]] |
| 2026-07-08 | W1 AUDIT — F7 compliance + regresija provera (25 stranica) | ✅ Bez izmena baze. 🔴 7 stranica izgubilo FAQPage/Product JSON-LD (gotcha #9 obrazac — tačno 245/246/251/252 kategorije + industrijski-podovi + spoljnje-podne-obloge + dimenzije-table). 🟡 9 stranica ima standarde bez linka (industrijski-podovi 7×!). 🟢 2 prirodna antas-skica kandidata (dimenzije stranice) bez ijedne skice. Plan sa checkbox listom: `migracija/f7-audit-i-popravke.md`. Detalji: [[DNEVNIK-NAPRETKA]] |
| 2026-07-07 | W1 + F7 — antistatik stranica + content standard (paralelno) | ✅ `/antistatik-i-elektroprovodljivi-podovi/` (ID 16658, W1 top prioritet 1131 kl.) — namena-esd grid, standardi sa linkovima (IEC 61340-5-1 i dr., potvrđeni WebSearch-om), FAQ+VideoObject JSON-LD, 2 stara pokvarena cross-linka usput ispravljena. **F7 standard zatvoren**: skill `obogati-proizvod` dopunjen, 12 novih SVG ikonica, video lite-embed (globalni JS, ne po-stranici vc_raw_html — čist rešava CLI/kses gotcha), antas-skica stil + pilot skica preseka ESD poda. Lighthouse pre/posle odloženo na W3 3.5 (CLI nedostupan lokalno). Sve verifikovano (200/1×H1/JSON-LD/regression 7 stranica). Detalji: [[DNEVNIK-NAPRETKA]] |
| 2026-07-07 | W1 PARITY F6 — namena arhitektura + pilot kosarkaske-konstrukcije | ✅ 8 `namena-*` product_tag termina na svih 37 proizvoda (tabela proizvod→namena potvrđena sa Miroslavom pre upisa) — realan katalog dominiraju Ergomat industrijska zaštita (28) i košarkaške konstrukcije (5), pa je lista tagova prilagođena stvarnoj ponudi umesto originalnih F6 pretpostavki. Grid mehanika potvrđena: `woodmart_products taxonomies="<term_id>"`. Pilot = `/sportske-podloge/kosarkaske-konstrukcije/` (ID 16657) — spojen sa W1 SEO prioritetom #1 (923 GSC klika), troslojni model (uporedna tabela + auto grid 5 proizvoda + FAQ/JSON-LD), redirect mapa red rešen (identičan URL, redirect nepotreban). 200/1×H1/JSON-LD/auto-mehanika/regression svi verifikovani. Novi odeljak u [[migracija/woodmart-sabloni]]. Detalji: [[DNEVNIK-NAPRETKA]] |
| 2026-07-07 | W3 PARITY F5 — trijaža nedostajućih stranica | ✅ CSV resync (F2/F3 promene nisu bile upisane, 52→46 NEDOSTAJE-LOKAL). `politika-kolacica` kreirana odmah (ID 16656). 33 stranice kategorisane i sortirane po GSC klikovima u novom `migracija/w1-red-cekanja.md` — top 5: antistatik (1131), bergo-xl (978), kosarkaske-konstrukcije (923 🔴), Ecotile 500/7 info (625), sportski-podovi-za-sale-i-balone (378, PAGE tip). Nađen Ecotile info klaster (3 str.), LVT/Expona silo (6 str.), epoksid-conquest srodna stranica, 2 duplikat para za proveru. Detalji: [[DNEVNIK-NAPRETKA]] |
| 2026-07-07 | W3 PARITY F4 — minimalna redirect mapa (7 redova) | ✅ Sa Miroslavom: parity za spoljnje-podne-obloge (1304 kl.) i podovi-za-stale (402 kl.) izvršen; **ispravljena pogrešna pretpostavka** — Bergo easy/elite/unique/xl NISU duplikat bergo-ultimate, i dalje su u ponudi → F5 W1 rebuild kao zasebne stranice, ne redirect. 3 draft posta (pogrešan post_type) obrisana → F5 rebuild kao page. 2 izbor-poda članka odloženo na W2. Usput nađena i rešena stara (2022) duplirana stranica koja je blokirala čist spoljnje-podne-obloge slug. `redirect-mapa-FINAL.csv` (7 redova, 3 verifikovana 200, 3 čekaju F5) + `htaccess-301-DRAFT.txt` (NE aktiviran). Detalji: [[DNEVNIK-NAPRETKA]] |
| 2026-07-07 | W3 PARITY F3 — pun reimport 30 postova sa live | ✅ Cleanup 7 LOKAL-NOVO postova (1 zadržan, 4 draft, 2 obrisana) → WXR import (4 pokušaja, redom otkriveni missing-function gotcha-i: WP_LOAD_IMPORTERS, dupli require, post_exists/comment_exists includes) → 30 publish postova (2 od 30 live namerno preskočena kao pravi duplikati postojećeg sadržaja, matematika se poklapa). ID-evi 2542/2298 sačuvani (bez izmena u CLAUDE.md). Slike: 26 postova popravljeno na lokalni domen, 20 referenci na stvarno nedostajuće fajlove zabeleženo za restyle. Anti-kanibalizacija basket članka ponovo primenjena. Detalji: [[DNEVNIK-NAPRETKA]] |
| 2026-07-07 | W3 PARITY F2 — permalink fix (Woo /proizvod/ + /kategorija-proizvoda/ + aktuelnosti) | ✅ Woo `product_base`→`proizvod` flat, `category_base`→`kategorija-proizvoda`, blog slug→`aktuelnosti`, 2 proizvod sluga vraćena na live, 8 pokvarenih internih linkova ispravljeno. lite-shot 325/795 razrešeno (isti proizvod, ne preimenovan, ide u F4 301 mapu). 🔴 2 nova gotcha-a: hard flush (`flush_rewrite_rules(true)`) obavezan posle permastruct izmena; Yoast indexable keš mora ručno da se obriše (`wpGs_yoast_indexable`) posle SVAKE product/product_cat/page permalink izmene. Backup 47MB. Verifikacija 200 svuda. Detalji: [[DNEVNIK-NAPRETKA]] |
| 2026-07-07 | W3 PARITY F1 — master inventar (175 redova) | ✅ Svih 7 live sub-sitemapa vs lokalna baza + GSC 12mes klikovi (Windsor.ai) → `migracija/parity-inventar.csv`. PARITY 84 · NEDOSTAJE-LOKAL 57 · LOKAL-NOVO 32. 🔴 Nov nalaz: `kosarkaske-konstrukcije` = 923 klika (>478 ranije dokumentovanih) — prioritet #1 za F5. Spot-check 5/5 → 200. Detalji: [[DNEVNIK-NAPRETKA]] |
| 2026-07-07 | STRATEGIJA — PARITY-PLAN (zamena redirect pristupa) | ✅ Odluka: build 1:1 prema live sajtu (URL+content parity). Stara redirect mapa (118 redova, `/shop/` targeti) arhivirana → `migracija/arhiva/`. Novi izvor istine: [[migracija/PARITY-PLAN]] + 7 samostalnih promptova [[migracija/promptovi/_README]] (F1 inventar → F2 permalinci → F3 reimport postova → F4 minimalna mapa → F5 trijaža → F6 namena arhitektura → F7 content standard). Odluke: slug hibrid po težini, pun reimport 30 postova (M8 ✅), troslojni model namena→proizvod, standardi-sa-linkovima za proizvode. Detalji: [[DNEVNIK-NAPRETKA]] |
| 2026-07-07 | W1 — Dizajn fix: desktop spacing/font + CTA dijagonala | ✅ `--al-gap` desktop max 140→72px, hero naslov desktop max 104→88px (mobile nepromenjen); `/o-nama/` kicker red dobio `al-section--compact`. **Sistemski bug**: dijagonalni CTA prelaz (`al-diag-top`/`--rev`) ostavljao beli trougao na svakoj stranici (WPBakery stretch-row marker div poništava `margin-top`) → fix na `position:relative+top`, jedna CSS izmena, sitewide. 13 stranica verifikovano 200. Detalji: [[DNEVNIK-NAPRETKA]] |
| 2026-07-07 | W3 — 3.13 backup automatizovan | ✅ `nocni-backup.ps1` (mysqldump + zip wp-content, rotacija 14) registrovan u Task Scheduler-u (Daily 03:00). Test: 90MB DB + 3,6GB wp-content → 3GB arhiv, 27 min. Piše lokalno dok M ne prijavi OneDrive (auto-prebacivanje spremno). ⏳ 3.14 u toku: popis cPanel (PHP 8.3 ⚠️ vs lokal 8.2.12, disk 42% zauzeto/6,9GB slobodno, subdomeni dostupni) — nastavlja se sutra. Detalji: [[DNEVNIK-NAPRETKA]] |
| 2026-07-07 | W1 N1 1.1 zatvoren + W1 1.9 bonus + C1 spot | ✅ **N1 silo sekvenca gotova**: `/spoljne-podne-obloge/` (ID 16590, bez j), `/podloge-za-parkiraliste-i-staze/` (ID 16589), `/kontakt/` (ID 61, forma ispravljena), `/o-nama/` (ID 571). Sve: HTTP 200, 1×H1 al-display--xl, Yoast mete, FAQ schema. **Bonus W1 1.9**: `/podloge-za-parking/` (ID 15580, lokal draft) — Yoast title/metadesc ažurirani. **C1 spot**: 5 ključnih stranica verifikovane (spoljne-podne, parking-staze, sportska-igralista, zamena-parketa, podloge-za-krovove) — sve publish, 200. C1 CSV redirect ostatak trebao dalje (20+ redova). Backup-ovi: 5 × 90MB. Detalji: [[DNEVNIK-NAPRETKA]] |
| 2026-07-06 | W5 TRACKING — GA4 publike + GMB | ✅ GA4: "Parking & spoljne podloge" + "Košarkaški tereni" kreirane (Miroslav u GA4 UI); sinhronizovanje sa Ads aktivno (Too small to serve status; čeka rast saobraćaja). GMB: UTM fix (GA4 sada meri GMB kanal umesto Unassigned) + kategorije proširene (sportski + industrijalni) + prvi post 6 godina + review link spreman (čeka poslove). Efekat: GMB saobraćaj merljiv, CTR na Google Post da se prati sledećih nedelja |
| 2026-07-06 | ADS — negativne KW + čišćenje (M2 / plan 4.1) | ✅ Miroslav primenio u Ads UI po pripremljenom spisku: 13 novih negativnih (epoksi + padeži epoksidni/-ih/-og, betonski, "industrijski beton", [podne obloge], teraco, letvice, pevex, "uradi sam", "keramičke/podne pločice" kao phrase), pauzirani KW "bastenski namestaj" + "oprema za bazene" (Terase), lista potvrđena na obe kampanje → zatvara ~16% curenja budžeta. `laminat` svesno izostavljen (watch). Detalji: [[dnevnik/ADS-DNEVNIK]] |
| 2026-07-06 | PLAN — MASTER PLAN V2 | ✅ Novi jedini izvor istine za plan: [[2026-07-06-MASTER-PLAN-V2]] — 5 workstream-ova (W1 rebuild, W2 SEO content, W3 tehnička+migracija, W4 Ads, W5 tracking), nedeljni raspored N1–N8 unazad od go-live 2026-08-31, gate kriterijumi za migraciju, 8 zavisnosti od Miroslava sa fallback-ovima, KPI tabla (jun = mesec-nula), registar rizika. Stari plan od 2026-07-02 označen superseded. |
| 2026-07-06 | C3 TIER1 #4/#5 — dimenzije terena + table za košarku | ✅ `/dimenzije-kosarkaskog-terena/` (FIBA/NBA tabela + 3x3) i `/dimenzije-kosarkaske-table/` (staklo/akril, uradi sam vs konstrukcija) napravljene. Slug konflikt sa starim image attachment-om rešen (preimenovan attachment slug). Anti-kanibalizacija: skraćena dimenzije-sekcija u postojećem basket članku (2298) + linkovi ka novim stranicama. Verifikovano 200/1×H1/JSON-LD → [[DNEVNIK-NAPRETKA]] |
| 2026-07-06 | DIZAJN — 4 nove sport stranice (futsal/hokej/stoni tenis/3x3) | ✅ `/sportske-podloge/` grid je imao 4 kartice sa pogrešnim/placeholder linkovima (Futsal → `/industrijski-podovi/`, 3x3+stoni tenis+hokej → isti bergo-ultimate fallback) jer te 4 dedicated stranice nikad nisu postojale. Napravljene sve 4 po al- WoodMart šablonu (hero+USP+specifikacija+foto-reference+FAQ/schema+CTA), sadržaj oslonjen na stvarne Naxos Evolution/Bergo Ultimate proizvod-činjenice. Grid linkovi ispravljeni. Nova gotcha: `_woodmart_title_off=on` postmeta mora ručno posle `wp_insert_post()` da se izbegne 2×H1. Verifikovano 4/4: 200, 1×H1, FAQPage JSON-LD, slike/linkovi rade → [[DNEVNIK-NAPRETKA]] |
| 2026-07-06 | DIZAJN — 10 WooCommerce kategorija (Layout Builder) | ✅ Svih 10 kategorija (Ergomat/DuraStripe/Bergo/Ecotile katalog, term_id 245–254) dobilo hero+USP+grid+FAQ+CTA (6 punih) ili skraćenu (4 za 1-2 SKU) landing sekciju preko WoodMart "Shop Archive" Layout Builder-a (`woodmart_layout` CPT + `wd_layout_conditions`) — prvi put isprobano u projektu. Diferenciran ugao za duplikat parove 245↔246 i 251↔252 + cross-linkovi; 254 razdvojen od postojeće `/industrijski-podovi/` (edukativna vs. transakciona). Yoast title/metadesc setovan za svih 10. 3 nova gotcha-e dokumentovana (condition_comparison obavezan, WPSEO set_values ne set_value, Yoast indexable keš treba ručno invalidirati). Verifikovano 10/10: 200, 1×H1, FAQPage JSON-LD bez dupliranja, grid sa pravim proizvodima → [[DNEVNIK-NAPRETKA]] |
| 2026-07-06 | DIZAJN — /sportske-podloge/ WoodMart rebuild | ✅ Silo hub (ID 5438): hero, 6 USP kartica, grid 11 sport disciplina (linkovi ka postojećim stranicama), Bergo Ultimate specifikacija, FAQ + FAQPage JSON-LD, CTA. Content parity iz live SiteOrigin panels_data + lokalnog sadržaja. Nova lekcija: bergo-ultimate je child stranica → `/sportske-podloge/bergo-ultimate/`, ne `/bergo-ultimate/`. 200 + JSON-LD + svi linkovi/slike verifikovani → [[migracija/woodmart-sabloni]] |
| 2026-07-05 | DIZAJN — /industrijski-podovi/ rebuild | ✅ Nova silo landing (ID 16567) po WoodMart šablonu: hero, 6 USP, tabela debljina, 4 pod-kartice, reference, FAQ + FAQPage/Product JSON-LD; stara 4937 → draft (`-stara`), nova preuzela čist slug; Yoast meta prenet. Vizuelno + 200 verifikovano |
| 2026-07-05 | ALATI — UI/UX skill + Magic MCP | ✅ ui-ux-pro-max v2.6.2 (7 skill-ova) globalno u `~/.claude/skills/` + security audit čist; Magic MCP (21st.dev) u user scope ✔ Connected — za dizajn rad od sledeće sesije |
| 2026-07-05 | TEHNIČKA [cpanel-live] — LiteSpeed WebP fix | ✅ Uzrok nađen: 200 slika trajno zaglavljeno u "REQUESTED" (cela dnevna kvota), 1.561 čekalo u redu; resetovano preko `Img_Optm::reset_row()`, pipeline ponovo šalje slike. ⏳ Proveriti za par dana da li se ponavlja (moguć dublji QUIC.cloud problem) |
| 2026-07-05 | DIZAJN — Mondo look na WoodMart | ✅ Design system (antas-design.css), self-hosted Inter+Bebas, header sa crvenim CTA, home 6 sekcija izgrađen i verifikovan → [[migracija/woodmart-sabloni]] |
| 2026-07-05 | BREND — logo SVG/PNG izvoz | ✅ Vektorski SVG (vertikalni + horizontalni) iz PDF-a, boje = zvanična paleta, transparent PNG fallback; u `Logo/` + `woodmart-child/images/` |
| 2026-07-05 | WOODMART — instalacija na lokal | ✅ Tema 8.5.4 + child + core aktivirani, WPBakery → 8.7.3, home crash rešen (nova Početna 16550), smoke test 6/6 · 200 |
| 2026-07-05 | C3 #9 — odbojka refresh paket | ✅ Title/meta/FAQ/cena/pesak spakovano → [[dnevnik/2026-07-05-refresh-odbojka]]; ⚠️ stranica samo na live → `[cpanel-live]` primena čeka Miroslava + C2 parity gap |
| 2026-07-05 | C3 TIER 1 — 5 draftova | ✅ Draftovani #1 terase-cena, #2 industrijski-cena, #3 garaže, #4 dimenzije-terena, #6 parking-cena (svi: title/meta, H2, FAQ+schema, cena placeholderi) → [[seo/plan-novih-stranica]] checkbox lista |
| 2026-07-05 | BREND — logo + brand book | ✅ PDF-ovi u `Logo/` pregledani, specifikacije u [[reference/brend-knjiga]] (paleta, Inter font, web look&feel); 4 greške u PDF-u za dizajnera |
| 2026-07-04 | ANALIZA — puni snapshot | ✅ Baseline dnevnik stanja Ads+GA4+GSC+GMB → [[analiza/2026-07-04-snapshot-full]] (16mo istorija, strategija §6) |
| 2026-07-04 | Redirect mapa C1 verifikacija | ✅ Proverio 18 stranica, 10 kategorija, 37 proizvoda + ažurirao CSV (106/118 redova) |
| 2026-07-04 | WooCommerce — Svi proizvodi | ✅ 37 proizvoda + 10 kategorija + 115 slika importovano na localhost |
| 2026-06-27 | 301 redirect mapa | Završen auto-fill + verifikacija: 30 × 404 ispravljeni, 119 redova popunjeno (88% kompletno) |
| 2026-06-25 | /industrijski-podovi/ | On-page: Yoast title (69 znakova), meta opis — optimizovano za search |
| 2026-06-23 | /pop-tenis/ | On-page: Yoast title, H2 entry-title, dodat "piklbol" u uvod |

## Sledeće

1. ✅ **F7 audit popravke — U POTPUNOSTI ZATVOREN 2026-07-08** (P1 schema, P2 standardi-linkovi, P3 skice, P4 video) → [[migracija/f7-audit-i-popravke]]. Sledeći W1 fokus: **`migracija/w1-red-cekanja.md`** (bergo-xl 978 kl., Ecotile 500/7 info 625 kl., sportski-podovi-za-sale-i-balone 378 kl.).

2. **W1 rebuild po w1-red-cekanja.md** — ⭐⭐⭐ PRIORITET
   - F1–F7 ✅ SVI ZATVORENI 2026-07-07 → [[migracija/promptovi/_README]] (parity faze gotove, content standard definisan)
   - **`migracija/w1-red-cekanja.md`** je izvor istine za preostali W1 1.2 (31 stranica) — top: bergo-xl (978), Ecotile 500/7 info (625), sportski-podovi-za-sale-i-balone (378, PAGE tip!). (antistatik i kosarkaske-konstrukcije ✅ gotovo 2026-07-07.)
   - Svaka nova namenska landing stranica prati F6 troslojni obrazac + F7 content standard (ikonice/skica/video/standardi-sa-linkovima) → [[migracija/woodmart-sabloni]]
   - Blokirač za go-live

2. **C3 — Content plan: 20 stranica u 4 tijera** — ⭐⭐ PRIORITET → [[seo/plan-novih-stranica]]
   - Obuhvata ranije 4 GSC stranice + 16 novih iz pune 16m keyword analize
   - Prvi talas: odbojka refresh (30 min!) → /gumeni-podovi-za-terase-cena/ (4.221 impr) → industrijski cena → garaže landing
   - Nove namenske stranice grade se po F6 troslojnom modelu (rešenje hub + auto grid)
   - Vreme: ~40 min–1h po stranici

3. **WooCommerce testiranje — Checkout**
   - Testirati da li je checkout funkcionalan (posle F2 permalink izmene!)
   - Testirati slike na product page

4. **SEO quick-win: title/meta prepis 4 stranice** (iz [[analiza/2026-07-04-snapshot-full]] §6.1) — ⭐
   - /pop-tenis/ (5.503 impr, CTR 0,6%), /podloga-za-odbojkaske-terene/ (2.668, 1,3%), /spoljnje-podne-obloge/ (11.019, 3,5%), conquest članak (4.401, 0,6%)
   - ⚠️ pop-tenis i odbojka su POSTOVI — raditi POSLE F3 reimporta (inače se izmena briše)
   - Očekivano: +500–700 klikova/90d bez nove stranice · Vreme: 1-2h (na lokalnom buildu)

## Blokeri

- 🟡 **LiteSpeed image optimization — proveriti ponavlja li se** — ako se za par dana "Too many requested images" ponovo pojavi, QUIC.cloud notify webhook verovatno ima trajniji problem → potrebna njihova podrška #claude-code
- ✅ ~~Kontakt forma~~ — Gotovo 2026-07-07: CF7 forma sa GA4, placeholderis, autofill, email notifikacije
- ✅ **GA4 key events audit** — lažni key eventi skinuti krajem juna 2026; `conversions` metrika treba da se vrati na normalu od jula. Proveriti jula podatke kad budu dostupni da se potvrdi popravka.
- ✅ ~~Negativne KW liste~~ — rešeno 2026-07-06
- ✅ ~~GA4 publike~~ — rešeno 2026-07-06 (Parking & Košarkaški)
- ✅ ~~GMB ažuriranje~~ — rešeno 2026-07-06 (UTM + kategorije + post; review link čeka na poslove)
- ✅ **C1 verifikacija spot** — 2026-07-07: Live 80 stranica vs lokal 98; pronađeno 38 nedostajućih (PROVERI redovi), 25 verifikovanih; CSV `antasline-redirect-mapa-2026-07-07.csv` kreirama. 🔴 Kritična stranica `/hvala-za-poruku/` dodana (ID 16600)

## Napomene

**Redirect/parity (od 2026-07-07):** stara mapa i statistike su arhivirane
(`migracija/arhiva/` + [[migracija/arhiva/_SUPERSEDED]]). Aktuelno stanje parity-ja
živi u `migracija/parity-inventar.csv` (generiše F1). Izmereno 2026-07-07:
postovi 25/30 match · pages 8/50 · proizvodi 34/37 · Woo permalinci lokalno pogrešni
(`/shop/` umesto `/proizvod/` — F2 fix).

---

## ADS — Trenutno stanje

| Kampanja | Stanje | Napomena |
|---|---|---|
| Podloge za terase i bazene | ✅ radi | CTR 19%, slaba konverzija → kreativa čeka |
| ECOTILE INDUSTRIJSKI PODOVI | ✅ odblokirana | Nalog odblokiran 2026-07-04 → proveriti da su prikazi/CPC vraćeni na normalu |

**Aktivna faza:** 1 (RSA Terase — može odmah) + 2 (struktura ad grupa); Faza 0 (odblokiranje) ✅ zatvorena 2026-07-04  
**Pravih konverzija (jun):** 55 (hvala-proxy) / Ads uvezeno 6 / prag Smart Bidding: 20–30 plaćenih  
**Snapshot podataka (16mo, sva 4 izvora):** [[analiza/2026-07-04-snapshot-full]] — jun = najveći Ads mesec (30,7k RSD); ECOTILE phrase "industrijski podovi" = 1.073 RSD/konv. ⭐; Terase imp. share 24% (QS problem); ~16% budžeta curi kroz neaktivne negativne

## ADS — Sledeće

1. **Faza 0 — preduslov** ✅ ZAVRŠENO 2026-07-04
   - Dopuna balansa + verifikacija oglašivača gotovi → nalog odblokiran
   - Preostaje: potvrditi da su ECOTILE prikazi/CPC vraćeni na normalu
2. **Faza 1 — RSA Terase** (ne čeka odblokiranje)
   - 15 headlines + 4 descriptions → Ad Strength ≥ Good
3. **Faza 2 — Struktura ad grupe** (Terase → 3 grupe: terase | bazeni | bergo/modularne)

## ADS — Blokeri

- Nema — nalog odblokiran 2026-07-04 (balans + verifikacija). Preostaje samo potvrda da su ECOTILE prikazi/CPC vraćeni na normalu.

**Detalji, plan i RSA banka:** [[dnevnik/ADS-DNEVNIK]]
