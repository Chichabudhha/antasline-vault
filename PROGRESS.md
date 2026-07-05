# SEO Progress — Antasline lokalni build

## Urađeno

| Datum | Stranica | Šta |
|---|---|---|
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

1. **C1 — 301 Redirect mapa — Ručna verifikacija (20 redova PROVERI)** — ⭐⭐⭐ PRIORITET
   - Potvrdi da sve PROVERI stranice stvarno postoje na buildu
   - Posebno: /sportska-igralista/, /zamena-parketa-u-sportskim-salama/, /podloge-za-krovove-i-terase/, itd.
   - Vreme: 2-3h
   - Blokirač za go-live

2. **C3 — Content plan: 20 stranica u 4 tijera** — ⭐⭐ PRIORITET → [[seo/plan-novih-stranica]]
   - Obuhvata ranije 4 GSC stranice + 16 novih iz pune 16m keyword analize
   - Prvi talas: odbojka refresh (30 min!) → /gumeni-podovi-za-terase-cena/ (4.221 impr) → industrijski cena → garaže landing
   - Vreme: ~40 min–1h po stranici

3. **WooCommerce testiranje — Checkout**
   - Testirati da li je checkout funkcionalan
   - Testirati slike na product page

4. **SEO quick-win: title/meta prepis 4 stranice** (iz [[analiza/2026-07-04-snapshot-full]] §6.1) — ⭐ NOVO
   - /pop-tenis/ (5.503 impr, CTR 0,6%), /podloga-za-odbojkaske-terene/ (2.668, 1,3%), /spoljnje-podne-obloge/ (11.019, 3,5%), conquest članak (4.401, 0,6%)
   - Očekivano: +500–700 klikova/90d bez nove stranice · Vreme: 1-2h (na lokalnom buildu)

## Blokeri

- 🔴 **GA4 key events audit** — `conversions` metrika slomljena od juna (5.859/mes umesto ~100); naći i skinuti pogrešan key event u GA4 Admin #ceka-miroslav
- 🔴 **Negativne KW liste** — proveriti da li je "AntasLine — univerzalne negativne" zakačena na obe aktivne kampanje (epoksid/sika/rinol prolaze!) #ceka-miroslav
- Redirect mapa gotova — ručna verifikacija ide paralelno.

## Napomene

**301 Redirect mapa — STATISTIKA:**
- ✅ Inventar builda: 133 putanje (posts, pages, products, categories)
- ✅ Auto-popunjeno 1:1: 2 redda
- ✅ Auto-predlog po slug-u: 41 reda
- ✅ 404 upozorenja ISPRAVLJENA: 30 redova
- ✅ Verifikovano da postoji: 44 redda
- 📋 PROVERI (ručna verifikacija): 20 redova
- 📋 NEMA NA BUILDU (strateška odluka): 5 redova
- 📋 AUTO-PREDLOG WooCommerce: 49 redova (proizvodi + kategorije)

**Fajlovi generirani:**
- `antasline-redirect-mapa-POPUNJENA.csv` — finalna mapa sa popunjenim i ispravljenim redovima
- `redirect-fill.php` — skriptu za auto-fill
- `fix-404-redirect.php` — skriptu za ispravku 404 ciljeva

**Ključni problemi koji su ispravljeni:**
- `/industrija-podovi/` → `/industrijski-podovi/` (8 redova)
- `/lvt-podovi/*` → `/podovi-za-poslovni-prostor/*` (5 redova)
- Sve `/spoljne-podne-obloge/*` varijacije maprirane na `/bergo-ultimate/` i `/vestacka-trava/`
- Sve `/industrijski-podovi/*` sub-stranice sada kažu `/industrijski-podovi/` (ne `/industrija-podovi/`)

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
