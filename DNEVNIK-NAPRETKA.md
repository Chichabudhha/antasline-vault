# Dnevnik napretka — Antasline SEO

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
