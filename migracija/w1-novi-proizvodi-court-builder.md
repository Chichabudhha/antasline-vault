# W1 1.11/1.12 — Novi proizvodi (7 dobavljača) + Court builder 2D + oprema

> Plan upisan 2026-07-11 `[claude-code]`. Izvor istine za ovaj paket.
> Status: ⏳ U TOKU — **S1 ✅ + S2 ✅, oba 2026-07-11**. S1: 2 atributa id 20/21, nosivost termin, roze, 4 kategorije term_id 369–372 (`pa_nosivost` je već postojao iz batch #1, trebala su samo 2 nova atributa). S2 (Condor Grass, 3 proizvoda 16877/16885/16893): 🔴 condorgrass-sport.com nedostupan, condor-group.eu ne potvrđuje tačne nazive modela/spec brojeve → opisi samo sa potvrđenim opštim činjenicama; 🔴 nema fotografija nijednog proizvoda. Oba #ceka-M — detalji DNEVNIK.
> Redosled: ~~S1 preduslov~~ ✅ · S2 ✅ · sledeći S3 (Radici Sport trava) · S4–S8 se ubacuju naizmenično sa Faza 2 batchevima · CB1–CB3 posle S8 · **CB3 gotov ≥2 nedelje pre go-live 2026-08-31**.

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
- [ ] ULTRAMIX EVO N.I. (mali fudbal, FIFA/FIGC) — simple
- [ ] Tournament 20 (tenis/padel, ITF) — **variable** (zelena, tennisred, plava = 3)
- [ ] Rugby linija — simple · [ ] Golf — simple · [ ] Hockey — simple
- [ ] Multisport — **variable** (zelena, crvena, plava = 3) · [ ] Landscape — simple

**Geoplast** (geoplastglobal.com) → `parking-i-travne-resetke` (sve simple, HDPE reciklirani):
- [ ] Salvaverde Type A 50×50×4 cm (350 t/m², permeabilnost 95%) · [ ] Salvaverde Type B 58×58×4 cm
- [ ] Runfloor · [ ] Geograss · [ ] Geocross

**Objectflor Expona** (objectflor.de) → `lvt-podovi` (svi simple — 50+ dekora NIJE variable, dekori u galeriji/opisu; **svi `pa_podno-grejanje`="Da (do 27 °C)"** u spec + FAQ):
- [ ] Expona Design · [ ] Flow (→16668) · [ ] Simplay · [ ] Simplay Acoustic Clic · [ ] Living Clic · [ ] Commercial (→16685/16686)

**R-Tile** (R-Tek Manufacturing) → `lvt-podovi` + 254:
- [ ] R-Tile Design — **variable** 10 boja (mapirati na postojeće slugove), debljina 4/5 mm spec, dovetail, 10 g garancije
- [ ] R-Tile Urban — **variable** 10 boja

**Ecotile oprema** (ecotileflooring.com) → `rampe-i-zavrsni-profili` (traka → 248):
- [ ] E500 T-Joint rampa 500×90 mm — **variable** 8 boja (žuta, svetlo-siva, tamno-siva, grafit, crna, zelena, crvena, tamno-plava)
- [ ] E500 T-Joint ugaona — **variable** (8) · [ ] X500 X-Joint rampa 497×90 mm — **variable** (grafit, crna, žuta, crvena, plava = 5) · [ ] X500 X-Joint ugaona — **variable** (5)
- [ ] Line-marking traka — **variable** ~6 boja (kat. 248) · [ ] SureGrip stair nosing — simple

**Hoop n Court** (hoopncourt.com) → 251 (svi simple):
- [ ] Hoopair D60 · [ ] Hoopair D54-F · [ ] Hoopair D72 · [ ] Goalrilla DC72E1 · [ ] Goalrilla CV72 · [ ] Goaliath GB60 · [ ] Goaliath Gotek 54 · [ ] Gotek 54 Wallmount · [ ] Thunder-500 (FIBA 3x3)

**Generički** (bez brenda, "na upit") → 252:
- [ ] Tribina montažno-demontažna — simple · [ ] Stolica za tribine — **variable** boja (~6) · [ ] Go za mali fudbal — simple · [ ] Golovi rukomet/futsal — simple · [ ] Zaštitna mreža za terene (po m²) — simple · [ ] Mreža za tenis — simple · [ ] Mreža za padel — simple · [ ] Mrežica za koš — simple

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
- [ ] Tribine i stolice → generički proizvodi
- [ ] Golovi i zaštitne mreže → generički
- [ ] Oprema za tenis i padel → mreže tenis/padel
- [ ] Mrežice i dodaci za koš → generički + Hoop n Court accessories
- [ ] (posle CB3) CTA baner "Isprojektujte svoj teren — Planer terena" → `/planer-terena/`

Posle izmene: Yoast purge 16676, 1×H1, linkovi, regresija 16677 (reflektori).

---

## Sesije

| # | Sadržaj | Obim | Status |
|---|---|---|---|
| **S1** ⚠️ preduslov | 4 kategorije + `pa_podno-grejanje` + `pa_visina-vlakna` + nosivost/roze termini + verifikacija filtera | mala | [ ] |
| S2 | Condor (3) + Radici deo (4) | 7 proizvoda, 14 var | [ ] |
| S3 | Radici ostatak (3) + Geoplast (5) | 8 | [ ] |
| S4 | Expona (6) — podno grejanje 27 °C spec+FAQ | 6 | [ ] |
| S5 | R-Tile (2×10 var) + traka + stair nosing | 4, ~26 var | [ ] |
| S6 | Ecotile rampe (4, 26 var) + cross-linkovi | 4 | [ ] |
| S7 | Hoop n Court (9) | 9 | [ ] |
| **S8** ⚠️ pre CB2 | Generička oprema (8) + kartice 16676 + `_al_cb_equipment` flagovi | 8 | [ ] |
| CB1 | CPT + REST skeleton + stranica + SVG grid + farbanje + obračun ploča | builder A | [ ] |
| CB2 | Sport šabloni + linije + rampe + oprema selektor + PNG export | builder B | [ ] |
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

- [ ] Cene za predračun → [[reference/cenovnik]] (fallback: specifikacija bez cena — NE blokira)
- [ ] Brendovi/dobavljači za tribine/stolice/golove/mreže posle pregovora (fallback: ostaju generički — NE blokira)
- [ ] Trim asortimana: šta od "sve iz ponude" se ne prodaje → draft
- [ ] 3D varijanta buildera — odluka POSLE migracije
- [ ] (kasnije) lopte po sportu u opremu specifikacije
