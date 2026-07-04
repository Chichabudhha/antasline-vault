# Dnevnik napretka — Antasline SEO

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
