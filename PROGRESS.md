# SEO Progress — Antasline lokalni build

## Urađeno

| Datum | Stranica | Šta |
|---|---|---|
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

2. **C3 — 4 nove GSC landing stranice + FAQ**  — ⭐⭐ PRIORITET
   - /dimenzije-basketskog-terena/ (240 impr)
   - /terasa-cena/ (236 impr)
   - /basketball-tabla-dimenzije/ (150 impr)
   - /podloge-za-parkiraliste-cena/ (~100 impr)
   - Vreme: 2-3h

3. **WooCommerce testiranje — Checkout**
   - Testirati da li je checkout funkcionalan
   - Testirati slike na product page

## Blokeri

- Nema — redirect mapa je gotova. Ručna verifikacija je normalna, može se raditi parallelno sa ostalim zadacima.

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
| ECOTILE INDUSTRIJSKI PODOVI | ⛔ zagušena | Faza 0 blokira — dopuni balans + verifikacija |

**Aktivna faza:** 0 (odblokiranje) → 1 (RSA Terase — može odmah)  
**Pravih konverzija (jun):** 53 ukupno, 2–3 plaćene / prag Smart Bidding: 20–30

## ADS — Sledeće

1. **Faza 0 — preduslov** `#ceka-miroslav`
   - Dopuna balansa i završetak verifikacije oglašivača
   - ECOTILE nema smisla dirati dok se ovo ne reši
2. **Faza 1 — RSA Terase** (ne čeka odblokiranje)
   - 15 headlines + 4 descriptions → Ad Strength ≥ Good
3. **Faza 2 — Struktura ad grupe** (Terase → 3 grupe: terase | bazeni | bergo/modularne)

## ADS — Blokeri

- Verifikacija oglašivača + nizak balans → ECOTILE je beznačajan dok se ovo ne reši

**Detalji, plan i RSA banka:** [[dnevnik/ADS-DNEVNIK]]
