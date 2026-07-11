# W1 1.11/1.12 — Novi proizvodi (7 dobavljača) + Court builder 2D + oprema

> Plan upisan 2026-07-11 `[claude-code]`. Izvor istine za ovaj paket.
> Status: ⏳ U TOKU — **S1 ✅ + S2 ✅ + S3 ✅ + S4 ✅ + S5 ✅ + R-Tile/traka/stair nosing ✅, sve 2026-07-11**. S1: 2 atributa id 20/21, nosivost termin, roze, 4 kategorije term_id 369–372. S2 (Condor Grass, 3): condorgrass-sport.com nedostupan → opisi generički. S3 (Radici Sport, 7): 🟢 ULTRAMIX EVO N.I./Tournament 20 potvrđeni specovi; ostalih 5 generički. S4 (Geoplast, 7 — 5 planiranih + Geogravel/Geoflor van plana): 🟢 svih 5 planiranih ima potvrđene specifikacije (geoplastglobal.com); Geogravel/Geoflor iskorišćen već postojeći, odobreni sadržaj sa hub-a 16589 sa PRAVIM CENAMA. 🔴🔴 **Sistemski bug otkriven u S4**: postavljanje realne `_price` duplira Product JSON-LD (Yoast + globalni W2 2.7 hook) — fixirano u `functions.php` (proverava cenu pre fallback-a), ali svaki budući price-upis na postojeće proizvode treba proveriti. S5 (Objectflor Expona LVT, 6): 🟢 4/6 (Commercial/Flow/Simplay/Clic 19dB) sa pravim proizvođačkim PDF tehničkim listovima pročitanim direktno iz uploads-a (uklj. bitnu razliku Bfl-s1 vs Cfl-s1); Design/Living Clic samo WebSearch-potvrđeno, kraći spec. Backlink dodat na LVT hub 16667. **R-Tile/traka/SureGrip (4)**: 🔴 **plan-odstupanje nađeno i ispravljeno** — originalni plan je pretpostavio da R-Tile Design/Urban imaju "10 boja/dovetail/10g garanciju" (to su specifikacije GARAŽNE R-Tile linije sa r-tekmanufacturingltd.com); stvarna Design/Urban retail kolekcija (r-tileretailflooring.com) ima DEKOR uzorke (beton/teraco/kamen/drvo, 7+12), ne prave boje → oba urađena kao **simple** (isti obrazac kao Expona), dekori nabrojani u opisu, debljina/garancija koje proizvođač ne navodi javno NISU izmišljene. PermaStripe traka (ecotileflooring.com) variable 6 boja (crvena/žuta/bela/zelena/plava/crno-žuta hazard, postojeći pa_boja slugovi). SureGrip stepenišni profil (ecotileflooring.com) simple, GRP, potvrđene dimenzije/DDA — prvi proizvod u kategoriji 372 (do sada prazna). Svi bez fotografija — #ceka-M. **S6 (Ecotile rampe, 4)**: 🟢 sve specifikacije (dimenzije/debljina/tip spoja) potvrđene direktno sa ecotileflooring.com/product/floor-tile-accessories/; 🔴 broj boja za OBA ugaona profila manji nego što je plan pretpostavio (E500 ugaona: 3 ne 8; X500 ugaona: 2 ne 5) — zvanično potvrđeni brojevi upisani, ne plan-pretpostavka; ukupno 18 varijacija (8+3+5+2), ne 26 kako je plan procenio. Cross-link dodat: E500/7 (16538)→T-Joint rampe, ESD X-Joint (16542)→X-Joint rampe. Nema fotografija ni za jedan. **S7 (Hoop n Court, 8)**: 🟢🟢 PRVA sesija sa PRAVIM slikama I PRAVIM cenama — M dao dozvolu "slobodno skini sa sajta" pa su 34 slike preuzete direktno sa hoopncourt.com CDN-a (6 po Hoopair modelu, 4-5 po Goalrilla modelu, 1 po GB60/Gotek54/LED — manje slika dostupno kod ova 3, ne izmišljeno) i ubačene u media biblioteku (wp_insert_attachment) + M je dao punu cenovnu tabelu (ulazna+troškovi+zarada u EUR, "Cena sa PDV" u RSD, implicitni kurs ~117,5 potvrđen unakrsnom proverom svih 8 redova) → upisano kao stvarna `_price`/`_regular_price`. Verifikovano da S4 bug (dupliranje Product schema kod postavljanja cene) NIJE nastupio — Yoast @graph sadrži tačno JEDAN Product entry. M svesno suzio listu sa originalnih 9 na 8 (izbacio Gotek 54 Wallmount i Thunder-500 FIBA 3x3, dodao Goalrilla LED rasvetu). 🔴 Nalaz: hub 16657 (923 GSC klika) filtrira grid preko `namena-sport-dvorana` TAGA (term 266), ne preko kategorije 251 — svih 8 novih proizvoda dobilo taj tag da bi se pojavili tamo; grid je `items_per_page="6"` pa se ne vide svi odjednom na prvoj strani (uređivačka odluka, ne greška). Svih 27+8=35 novih proizvoda iz S1-S7+R-Tile niza — 27 i dalje bez fotografije/cene (#ceka-M), 8 iz S7 ima oboje.
> Redosled: ~~S1~~ ✅ · ~~S2 Condor~~ ✅ · ~~S3 Radici~~ ✅ · ~~S4 Geoplast~~ ✅ · ~~S5 Expona~~ ✅ · ~~R-Tile+traka+stair nosing~~ ✅ · ~~S6 Ecotile rampe~~ ✅ · ~~S7 Hoop n Court~~ ✅ · ~~S8 generička oprema~~ ✅ · **RP1 U POTPUNOSTI ZATVOREN** · ~~CB1~~ ✅ · ~~CB2~~ ✅ 2026-07-11 · **sledeći: CB3** (⚠️ GATE — mora biti gotov ≥2 nedelje pre go-live 2026-08-31: dompdf + mejlovi + admin metabox + verzije/token + cene + GA4 + bezbednost + puna regresija). ⚠️ Napomena: sesijski brojevi S1–S8 u tabeli ispod su iz originalnog plana (pre podele Radici/Geoplast/Expona u zasebne sesije) — od S4 nadalje ne poklapaju se 1:1 sa dnevnik oznakama, prati "Status" liniju gore za tačan redosled.
> **CB1 status (2026-07-11)**: CPT `al_court_design` registrovan (public=false, van sitemap-a po prirodi) · REST `POST /wp-json/al/v1/design` radi (validacija proizvod/dimenzije/veličina grid stringa, BEZ nonce/rate-limit/honeypot — CB3 obim) · `/planer-terena/` (ID 17004, noindex dok CB3 ne zatvori) sa `[al_court_builder]` shortcode-om · SVG grid + klik/drag farbanje (miš i touch) + live obračun ploča po boji (m² preko `plate_mm`, ne `module_mm`) + RLE enkodovanje pre slanja — sve testirano end-to-end kroz pravi browser (Chrome), ne samo curl. Moduli/boje potvrđeni iz baze (Ultimate 375,3/376,7mm 15 boja, FLOW 374/376mm 13 boja) — pa_boja hex mapa je builder-ova sopstvena (approx UI boje, ne zvanični RAL kodovi, pošto termini nemaju hex termmeta). 🔴 Nađen i ispravljen vizuelni bug (F7.14 u woodmart-sabloni): nova stranica bez `_woodmart_main_layout=full-width` pada na sidebar layout → globalna "Brzi upit" forma se stisla u sidebar preko hero-a; HTTP/H1 provera to ne hvata, samo browser screenshot. Max 3000 ćelija u CB1 (performansa), veći limit/sport šabloni dolaze u CB2.
> **CB2 status (2026-07-11)**: 7 sportskih šablona (Košarka FIBA, Basket 3x3 polukort, Tenis, Padel, Odbojka, Futsal, Pickleball) sa vektorskim linijama — SVE dimenzije verifikovane WebSearch-om (ITF/FIBA/FIP zvanični izvori) pre pisanja koda, ne pretpostavljene. Preset dugme postavlja širinu/dužinu na STVARNU dimenziju terena (ne "preporučeni ukupno" broj iz plana — taj se prikazuje kao informativna napomena ispod dugmadi, korisnik ga ručno unosi ako želi celu površinu), čime se izbegao performansni problem (futsal "ukupno" 44×24=7488 ćelija > limit; sam teren 40×20=5778 stane). Pickleball automatski prebacuje proizvod na FLOW (16801) po planu. 🔴 **Svesno izostavljena futsal D-oblik kaznenog prostora** (radijus 6m od svake stative + spojna linija) — geometrija zahteva dodatnu verifikaciju, umesto pogrešnog crteža prikazana samo centralna linija + krug (r=3m, FIFA futsal). Rub/rampa obračun (perimetar×1000/modul + 4 ugaone) implementiran kao INFORMATIVNA stavka bez vezanog WC proizvoda — Bergo nema poseban rub-proizvod u katalogu, cena dolazi iz `al_cb_prices` opcije tek u CB3. Oprema selektor čita 16 proizvoda sa `_al_cb_equipment=1` (8 S8 generičkih + **8 S7 Hoop n Court, retroaktivno flagovano ovom sesijom** — plan je tražio "koševi 251 + oprema 252" ali S7 flag nije postavljen u svojoj sesiji, ispravljeno sada). PNG export klijentski (XMLSerializer→canvas→toDataURL, max 1600px) radi bez grešaka. Testirano end-to-end u Chrome-u: Basket 3×3 šablon (ključ+3pt luk textbook-tačan vizuelno), farbanje preko linija, PNG export, čuvanje sa sport/rub/oprema podacima (svi tačno upisani, ramp_qty matematika potvrđena). 🔴🔴 **Ozbiljan bug nađen i ispravljen**: FAQ JSON-LD na `/planer-terena/` se renderovao kao GOLI VIDLJIV TEKST — uzrok NIJE bio uobičajeni wpautop mangling nego `wp_insert_post()` pozvan bez ulogovanog korisnika (CLI kontekst) koji je pustio ceo sadržaj kroz kses i OBRISAO `<script>` tagove (ostavio JSON tekst). Nova gotcha F7.15 u [[migracija/woodmart-sabloni]] — pravilo: `<script>` u post_content ide ISKLJUČIVO kroz `$wpdb->update()`, nikad kroz `wp_insert_post()` content parametar u CLI kontekstu.

---

## Odluke Miroslava (2026-07-11)

1. **Dodati SVE iz ponude** 7 dobavljača — višak se kasnije prebacuje u draft kad M sazna šta se ne prodaje.
2. **Court builder 2D sada, 3D posle migracije.** Samo za Bergo Ultimate (16770) i Bergo Flow / Ultimate FLOW (16801).
3. Dizajn se **zaključava i čuva u WP adminu** — klijent NE može sam da menja. Klijent dobija **mejl sa PNG slikom + PDF specifikacijom** (bez linka za izmenu). Antasline iz admina šalje token-link koji otvara **kopiju kao novu verziju** (original netaknut) — svi uvek gledaju istu važeću verziju.
4. Specifikacija/predračun uključuje i **opremu**: koševi, mreže za tenis/padel, tribine, mrežice za koš; **lopte kasnije** (lako proširenje).
5. **Generički proizvodi bez brenda** ("na upit") za tribine/stolice/golove/mreže — dobavljači u pregovorima, dopune se brendom/cenom kasnije. Court builder submiti će vremenom pokazati koja oprema se najviše traži (odgovor na "šta je najisplativije uz teren").
6. LVT: obavezno istaći **podno grejanje** (Expona zvanično do max 27 °C, važi za sve linije).

## Standardi izvršavanja (svaka sesija)

- `mysqldump` backup pre; string upisi **samo kroz PHP skripte** (mysql CLI kvari ć/č i sa utf8mb4); komande >965B → .php fajl.
- Proizvod po skill `/obogati-proizvod`: pa_ atributi + `_product_attributes` meta, galerija (zvanične slike dobavljača, prva glavna), Yoast, opis = GEO intro / spec tabela / standardi-sa-linkovima / FAQ + FAQPage JSON-LD u script tagu / CTA 072, cross-linkovi, excerpt, `_woodmart_title_off=on`.
- Posle sesije: **LookupDataStore regen** (WC attribute lookup laže posle direktnih upisa!), Yoast indexable purge, verifikacija 200 / 1×H1 / JSON-LD validan / linkovi 200 / regresija.

---

## RP1 — Taksonomija + proizvodi (~46 novih, ~78 varijacija)

### Nove product_cat kategorije (top-level, kao postojećih 12)

| Slug | Naziv | Sadržaj | Cross-link |
|---|---|---|---|
| `vestacka-trava` | Veštačka trava | Condor + Radici (10) | postojeće trava stranice |
| `parking-i-travne-resetke` | Parking i travne rešetke | Geoplast (5) | 16589 hub, 16876 cena |
| `lvt-podovi` | LVT vinil podovi | Expona + R-Tile (8) | 16667/16668/16684/16685/16686, 16142 |
| `rampe-i-zavrsni-profili` | Rampe i završni profili | Ecotile rampe/nosing (5) | 16660/16682/16683, Ecotile 16538+ |

Line-marking traka → postojeća **248 Podno obeležavanje**; Hoop n Court → **251**; generička oprema → **252**. R-Tile dual-cat (lvt-podovi + 254; primary term `_yoast_wpseo_primary_product_cat`).

### Novi atributi (spec-only, is_variation=0) — proširuje set od 18, upisati odluku u skill

| Atribut | Termini |
|---|---|
| `pa_podno-grejanje` | "Da (do 27 °C)", "Ne" |
| `pa_visina-vlakna` | 20/24/40/50/60 mm (po trava linijama) |
| `pa_nosivost` | registrovan bez termina → prvi: "350 t/m²" (Geoplast) |

`pa_boja`: nov termin `roze` (Condor); ostale boje mapirati na postojeće srpske slugove; dark/light nijanse NE kao termini (spec u opisu). Ne dodavati više atributa (dtex/gustina → spec tabela).

Izvršenje: PHP skripta (`wp_insert_term` + INSERT u `wpGs_woocommerce_attribute_taxonomies` + `delete_transient('wc_attribute_taxonomies')`).

### Proizvodi po dobavljaču

**Condor Grass** (condor-group.eu / condorgrass-sport.com) → `vestacka-trava`:
- [x] Condor Schools trava u boji — **variable** pa_boja (crvena, žuta, plava, bela, roze, zelena, braon = 7) — ✅ 16877, 2026-07-11
- [x] Condor Playgrass — **variable** (istih 7) — ✅ 16885, 2026-07-11
- [x] Condor shock-pad podloga — simple — ✅ 16893, 2026-07-11

**Radici Sport** (radicisport.it) → `vestacka-trava`:
- [x] ULTRAMIX EVO N.I. (mali fudbal, FIFA/FIGC) — simple — ✅ 16894, 2026-07-11 (38mm/no-infill/6000+8000 dtex potvrđeno)
- [x] Tournament 20 (tenis/padel, ITF) — **variable** (zelena, crvena, plava = 3) — ✅ 16895, 2026-07-11 (20-25mm/5500dtex/ITF potvrđeno, 7 zvaničnih nijansi grupisano u 3)
- [x] Rugby linija — simple — ✅ 16899 (samo World Rugby sertifikacija potvrđena) · [x] Golf — simple — ✅ 16900 (generički) · [x] Hockey — simple — ✅ 16901 (generički)
- [x] Multisport MX — **variable** (zelena, crvena, plava = 3) — ✅ 16902 (naziv potvrđen, spec ne) · [x] Landscape — simple — ✅ 16906 (generički)

**Geoplast** (geoplastglobal.com) → `parking-i-travne-resetke` (sve simple, HDPE reciklirani):
- [x] Salvaverde Type A 50×50×4 cm (350 t/m², permeabilnost 95%) — ✅ 16907, 2026-07-11 · [x] Salvaverde Type B 58×58×4 cm — ✅ 16908
- [x] Runfloor — ✅ 16909 (600 t/m²/90% propusnost, cena 2.800–3.400 din/m² — hub 16589 autoritativniji od inicijalnog WebFetch-a) · [x] Geograss — ✅ 16910 (280 t/m², na upit) · [x] Geocross — ✅ 16911 (100 t/m², cena 4.200 din/m²)
- 🆕 **Van originalnog plana, dodato jer je hub 16589 već imao gotov sadržaj+cene**: [x] Geogravel — ✅ 16912 (400 t/m²/61% propusnost, cena 4.000 din/m²) · [x] Geoflor — ✅ 16913 (cena 3.400 din/m²)

**Objectflor Expona** (objectflor.de) → `lvt-podovi` (svi simple — 50+ dekora NIJE variable, dekori u galeriji/opisu; **svi `pa_podno-grejanje`="Da (do 27 °C)"** u spec + FAQ gde potvrđeno):
- [x] Expona Design — ✅ 16918, 2026-07-11 (samo WebSearch potvrda, bez lokalnog PDF-a/fotki — #ceka-M) · [x] Flow (→16668) — ✅ 16915 (pun PDF spec) · [x] Simplay 19dB — ✅ 16916 (pun PDF spec, loose-lay, Cfl-s1) · [x] Clic 19dB Wood — ✅ 16917 (pun PDF spec, 5G-i Välinge, umesto plana pisanog "Simplay Acoustic Clic" — realno ime kod objectflor-a je "Clic 19dB", odvojen proizvod od Simplay-a) · [x] Living Clic — ✅ 16919 (samo WebSearch potvrda, bez lokalnog PDF-a/fotki — #ceka-M) · [x] Commercial (→16685/16686) — ✅ 16914 (pun PDF spec)

**R-Tile** (R-Tek Manufacturing, r-tileretailflooring.com) → `lvt-podovi` + 254:
- [x] R-Tile Urban — **simple** (6,5mm, 100% reciklirani PVC, interlocking, 7 dekora beton/teraco potvrđeno) — ✅ 16920, 2026-07-11
- [x] R-Tile Design — **simple** (mat PU + hidden interlock, 12 dekora kamen/drvo potvrđeno) — ✅ 16921, 2026-07-11 — 🔴 plan pogrešno pretpostavio "variable 10 boja/dovetail/10g garancije" (to su specifikacije garažne R-Tile linije, ne retail Design/Urban) — vidi napomenu u statusu gore

**Ecotile oprema** (ecotileflooring.com) → `rampe-i-zavrsni-profili` (traka → 248):
- [x] E500 T-Joint rampa 500×90 mm — **variable** 8 boja (žuta, svetlo-siva, tamno-siva, grafit, crna, zelena, crvena, tamno-plava) — ✅ 16930, 2026-07-11 (potvrđeno ecotileflooring.com, tačno se poklopilo sa planom)
- [x] E500 T-Joint ugaona — **variable 3 boje** (svetlo-siva, tamno-siva, grafit) — ✅ 16939, 2026-07-11 — 🔴 plan pretpostavio isti broj boja (8) kao prava rampa, zvanična stranica potvrđuje samo 3 za ugaonu · [x] X500 X-Joint rampa 497×90 mm — **variable** (grafit, crna, žuta, crvena, plava = 5) — ✅ 16943 (poklopilo se sa planom) · [x] X500 X-Joint ugaona — **variable 2 boje** (plava, crna) — ✅ 16949 — 🔴 isti obrazac, plan pretpostavio 5, zvanično potvrđeno samo 2
- [x] Line-marking traka (PermaStripe) — **variable** 6 boja (kat. 248) — ✅ 16922, 2026-07-11 · [x] SureGrip stair nosing — **simple** — ✅ 16929, 2026-07-11 (prvi proizvod u 372)

**Hoop n Court** (hoopncourt.com) → 251 (svi simple) — ✅ 2026-07-11, samo M-lista (8, ne originalnih 9 — Gotek 54 Wallmount i Thunder-500 FIBA 3x3 SVESNO preskočeni po M instrukciji "dodaj samo navedene modele"):
- [x] Hoopair D72 — ✅ 16952, 349.680 RSD · [x] Hoopair D60 — ✅ 16959, 320.070 RSD · [x] Hoopair D54-F — ✅ 16966, 313.020 RSD
- [x] Goalrilla DC72E1 — ✅ 16973, 549.900 RSD · [x] Goalrilla CV72 — ✅ 16978, 458.250 RSD
- [x] Goaliath GB60 — ✅ 16984, 246.750 RSD · [x] Goaliath Gotek 54 — ✅ 16986, 167.790 RSD
- [x] Goalrilla LED (rasvetna oprema, van originalne liste 9 — M dodao u tabeli) — ✅ 16988, 116.325 RSD

**Generički** (bez brenda, "na upit") → 252 — ✅ 2026-07-11, svi sa `_al_cb_equipment=1` flagom (CB2 zavisnost):
- [x] Tribina montažno-demontažna — simple — ✅ 16990 · [x] Stolica za tribine — **variable 6 boja** — ✅ 16991 · [x] Go za mali fudbal — simple — ✅ 16998 · [x] Golovi rukomet/futsal — simple — ✅ 16999 · [x] Zaštitna mreža za terene (po m²) — simple — ✅ 17000 · [x] Mreža za tenis — simple — ✅ 17001 · [x] Mreža za padel — simple — ✅ 17002 · [x] Mrežica za koš — simple — ✅ 17003

---

## RP2 — Court builder 2D (`/planer-terena/`)

**Tehnologija:** child tema modul `woodmart-child/inc/court-builder/` (require iz functions.php), `js/al-court-builder.js` + `css/al-court-builder.css`, vanilla JS bez build stepa. **SVG grid** (`<rect>` + event delegacija za klik/drag farbanje; linije = vektorski overlay). **PNG export klijentski** (`XMLSerializer` → Image → canvas → `toDataURL`, max 1600 px) — server NEMA imagick (potvrđeno), GD ne rasterizuje SVG. Shortcode `[al_court_builder]`, enqueue samo na toj stranici.

**Moduli ploča (⚠️ verifikovano iz baze, NE 376,5):** Ultimate 16770 = ploča 375,3 / **modul 376,7 mm** (15 boja) · FLOW 16801 = ploča 374 / **modul 376 mm** (13 boja). `pa_boja` termini nemaju hex termmeta → builder nosi **svoju slug→hex mapu** u configu.

**Sport šabloni** (linije 50 mm, overlay — ne računaju se kao ploče; boja linija iz palete):

| Sport | Teren (m) | Preporuka ukupno | Linije |
|---|---|---|---|
| Košarka FIBA | 28×15 | 32×19 | FIBA komplet (reket, 3pt 6,75, centar) |
| Basket 3x3 | 15×11 | 17×13 | FIBA 3x3 polukort |
| Tenis | 23,77×10,97 | 36,57×18,29 (ITF) | singl+dubl |
| Padel | 20×10 | 20×10 | servis linije |
| Odbojka | 18×9 | 24×15 | napadačka 3 m |
| Futsal | 40×20 (i 25×15, 20×10) | +2 m obod | kazneni 6 m, centar |
| Pickleball (FLOW!) | 13,41×6,10 | 18,29×9,14 | kuhinja 2,13 m |
| Multisport/custom | slobodan unos | — | kombinacija |

**Model podataka:** CPT `al_court_design` (public=false, show_ui=true, **van WPML prevoda**, van sitemap-a). Meta:
- `_al_design_data` — JSON: product_id, module_mm, sport, dims, cols/rows, palette, **grid kao RLE string** (futsal 107×54 = 5.778 ćelija mora <10 KB), lines, equipment[{id,qty}], ramps
- `_al_design_locked`=1 · `_al_version` + `_al_parent_design` · `_al_contact` · `_al_totals` · `_al_png_id`/`_al_pdf_id` · `_al_revision_token` + expiry 30 d, jednokratan

**Obračun:** ploče po boji = ćelije; m² realno + pokrivna površina; rampe = ceil(obim×1000/modul) + 4 ugaone, boja izbor; oprema server-side iz WC proizvoda sa postmeta **`_al_cb_equipment=1`** (M kontroliše listu iz admina — koševi 251 + oprema 252 + generički).

**Submit flow:** REST `POST /wp-json/al/v1/design` (nonce `X-WP-Nonce`, honeypot, rate limit transient 3/h po IP; validacija: dims max 100 m, whitelist boja/sportova/proizvoda, qty 0–100; PNG magic bytes + **re-encode kroz GD**, max 2 MB) → zaključan CPT + PNG/PDF attachmenti → mejl klijentu (PNG+PDF prilog, ID dizajna, CTA 072, **bez edit linka**) + mejl Antasline (`[_site_admin_email]`, wp-admin link) → `dataLayer.push({event:'court_design_submit', sport, product})` → redirect `/hvala-za-poruku/?src=planer` (BLOK A generate_lead radi i dalje).

**PDF:** **dompdf 2.x drop-in ZIP** (bez composera; GD/DOM/mbstring potvrđeni) u `inc/court-builder/lib/dompdf/`. DejaVu Sans = latinica+ćirilica. `chroot` na uploads. PNG resize ~1200 px pre embed. Šablon: logo, slika dizajna, tabela ploča po boji, rampe, oprema, kontakt, "Dizajn #ID v{n}".

**Cene → predračun:** option `al_cb_prices` (JSON mapa: product_id / `tile:{id}` / `ramp` / `ramp_corner` → {cena_rsd, pdv}); admin podstranica "Cene planera" pod CPT-om. Stavka bez cene → "na upit"; **sve popunjeno → "Predračun br. AL-{ID}"** sa PDV totalom — nula koda se menja kad M popuni [[reference/cenovnik]]. **NE dirati WC `_price`** (katalog režim ostaje).

**Admin + verzije:** CPT list table (datum, klijent, telefon, sport, proizvod, ploče, verzija) + metabox: PNG pregled, obračun, "Preuzmi PDF", **"Generiši link za novu verziju"** (admin-post + nonce + capability; token `random_bytes`) → URL `/planer-terena/?rev={token}` → builder učita dizajn kao polaznu kopiju → submit = **nov post** (verzija+1, `_al_parent_design`), token se invalidira.

---

## RP3 — Stranica 16676 (oprema za sportske terene)

Live parity gap (live ima, lokal nema): tribine, stolice za tribine, teniska oprema; golovi/mreže bez linka. Dopuna `al-card` kartica u postojeći grid (PHP string-safe upis u WPBakery markup):
- [x] Tribine i stolice → generički proizvodi — ✅ 2026-07-11 (nova kartica → 16990)
- [x] Golovi i zaštitne mreže → generički — ✅ 2026-07-11 (2 postojeće kartice, ranije bez linka, sada linkovane → 17000, 16999)
- [x] Oprema za tenis i padel → mreže tenis/padel — ✅ 2026-07-11 (nova kartica → 17001)
- [x] Mrežice i dodaci za koš → generički — ✅ 2026-07-11 (nova kartica → 17003); Hoop n Court accessories (S7) nema posebnih dodataka van LED-a, koji je već na svojoj proizvod stranici
- [ ] (posle CB3) CTA baner "Isprojektujte svoj teren — Planer terena" → `/planer-terena/`

Posle izmene: Yoast purge 16676, 1×H1, linkovi, regresija 16677 (reflektori).

---

## Sesije

| # | Sadržaj | Obim | Status |
|---|---|---|---|
| **S1** ⚠️ preduslov | 4 kategorije + `pa_podno-grejanje` + `pa_visina-vlakna` + nosivost/roze termini + verifikacija filtera | mala | [ ] |
| S2 | Condor (3) + Radici deo (4) | 7 proizvoda, 14 var | [ ] |
| S3 | Radici ostatak (3) + Geoplast (5) | 8 | [ ] |
| S4 | Expona (6) — podno grejanje 27 °C spec+FAQ | 6 | [x] ✅ 2026-07-11 (izvršeno pod dnevnik-oznakom "S5" jer je Geoplast odradjen kao S4 — vidi napomenu iznad) |
| S5 | R-Tile (2× simple, ne variable — vidi napomenu) + traka + stair nosing | 4, 6 var (traka) | [x] ✅ 2026-07-11 |
| S6 | Ecotile rampe (4, 18 var stvarno — ne 26, vidi napomenu) + cross-linkovi | 4 | [x] ✅ 2026-07-11 |
| S7 | Hoop n Court (9) | 9 | [ ] |
| S7 | Hoop n Court (8, po M-listi) | 8 | [x] ✅ 2026-07-11 |
| **S8** ⚠️ pre CB2 | Generička oprema (8) + kartice 16676 + `_al_cb_equipment` flagovi | 8 | [x] ✅ 2026-07-11 |
| CB1 | CPT + REST skeleton + stranica + SVG grid + farbanje + obračun ploča | builder A | [x] ✅ 2026-07-11 |
| CB2 | Sport šabloni + linije + rampe + oprema selektor + PNG export | builder B | [x] ✅ 2026-07-11 |
| **CB3** ⚠️ gate | dompdf + mejlovi + admin metabox + verzije/token + cene + GA4 + bezbednost + puna regresija | builder C | [ ] |

**Verifikacija buildera end-to-end (CB3, Chrome):** dizajn → farbanje → oprema → submit → CPT zaključan → mejl u mail logu sa PNG+PDF → PDF ispravan (ć/č) → `/hvala-za-poruku/` → admin token link → nova verzija → original netaknut.

## Rizici

1. WoodMart swatches: pa_boja bez hex termmeta → varijacije kao label dugmad; hex upis = opcioni mini-zadatak (builder ne zavisi).
2. WC attribute lookup laže bez regen-a (poznat gotcha, ~78 novih varijacija).
3. XAMPP `post_max_size` vs PNG dataURL ~2 MB — proveriti u CB1.
4. `al-local-mail-log.php` verovatno ne loguje priloge → dopuniti u CB3.
5. WPML: CPT i `/planer-terena/` izvan prevoda (duple rute + nonce problemi inače).
6. **SMTP na live mora biti konfigurisan pre javnog puštanja buildera** (posle migracije).
7. Yoast: CPT ne sme u sitemap.
8. 965 B limit + ć/č → svi upisi kroz .php fajlove (posebno 16676 VC markup).

## #ceka-M

- [ ] 🆕 2026-07-11 — **Batch obrada slika na standard 1:1, max 1000×1000, WebP** (M pravilo, važi za sve buduće i postojeće slike) — S7 Hoop n Court (34 slike, proizvodi 16952/16959/16966/16973/16978/16984/16986/16988) trenutno NISU u ovom formatu (sirov hoopncourt.com CDN format/dimenzije) i moraju se reprocesirati; verovatno i starije 2022-uvoz slike treba proveriti. Zaseban budući zadatak, ne blokira S8.
- [ ] Cene za predračun → [[reference/cenovnik]] (fallback: specifikacija bez cena — NE blokira)
- [ ] Brendovi/dobavljači za tribine/stolice/golove/mreže posle pregovora (fallback: ostaju generički — NE blokira)
- [ ] Trim asortimana: šta od "sve iz ponude" se ne prodaje → draft
- [ ] 3D varijanta buildera — odluka POSLE migracije
- [ ] (kasnije) lopte po sportu u opremu specifikacije
