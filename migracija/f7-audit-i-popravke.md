---
tip: plan
naziv: F7 audit postojećih W1 stranica + popravka izgubljene FAQ/Product schema
datum: 2026-07-08
izvor: audit sesija 2026-07-08 (25 stranica/layout-a provereno)
---

# 📋 F7 audit + popravke — red čekanja

Audit urađen 2026-07-08 nad svih 25 W1 rebuild stranica/Layout Builder kategorija
(sve što sadrži `al-section` marker ili je `woodmart_layout` post_type). Cilj: uskladiti
već napravljene stranice sa F7 content standardom ([[migracija/woodmart-sabloni]] F7.1–F7.5)
i otkriti regresije. **Ništa nije menjano na stranicama tokom audita** — samo čitanje
(`post_content` + rendered HTML) i poređenje. Ovaj fajl je red čekanja za sledeće sesije.

---

## 🔴 Prioritet 1 — Izgubljena FAQPage/Product schema (regresija, ne F7 stvar)

Sve niže: FAQ **tekst** je vidljiv na stranici, ali `<script type="application/ld+json">`
FAQPage/Product blok nedostaje u renderovanom HTML-u. Obrazac ukazuje na gotcha #9
(CLI `wp_update_post`/`wp_insert_post` briše `vc_raw_html` bez ulogovanog korisnika) —
sve 4 kategorije su tačno one koje je dnevnik 2026-07-06 pomenuo kao naknadno
"diferencirane" (term_id parovi 245↔246 i 251↔252), verovatno kroz `wp_update_post`
umesto direktnog `$wpdb->update`. `/industrijski-podovi/` je verovatno stradala kroz
F2 Yoast indexable cache sesiju (2026-07-07) na isti način.

| # | Stranica | ID | Dnevnik tvrdi | Napomena |
|---|---|---|---|---|
| [ ] | `/industrijski-podovi/` | 16567 | FAQ+FAQPage/**Product** JSON-LD, 2026-07-05 | Najveći gubitak — 7 standarda + Product schema, money stranica |
| [ ] | `/spoljnje-podne-obloge/` | 16590 | "FAQ schema", 2026-07-07 (N1 silo) | 9× h3 FAQ tekst prisutan |
| [ ] | `/dimenzije-kosarkaske-table/` | 16585 | — | FAQ tekst (5× h3) postoji, schema nikad dodata (sestrinska `dimenzije-kosarkaskog-terena` je ima) |
| [ ] | Kategorija Zaštita i Bumperi | 16572 (term 245) | deo "6 punih" (hero+USP+FAQ+CTA) | Duplikat par sa 246, diferenciran 2026-07-06 |
| [ ] | Kategorija Industrijska zaštita | 16573 (term 246) | isto | Duplikat par sa 245 |
| [ ] | Kategorija Košarkaške konstrukcije | 16578 (term 251) | isto | Duplikat par sa 252 |
| [ ] | Kategorija Oprema za sportske terene | 16579 (term 252) | isto | Duplikat par sa 251 |

**Postupak popravke** (dokazan, koristi se od F6 pilota): pročitaj `post_content`, izvuci
postojeći FAQ tekst (h3/p parovi) za svaku stranicu, sastavi FAQPage (+ Product za
industrijski-podovi) JSON-LD, `base64(rawurlencode())` → `[vc_raw_html]` shortcode →
**isključivo** `$wpdb->update` + `clean_post_cache()` (NIKAD `wp_update_post` iz CLI-ja
na ovim stranicama ubuduće). Verifikacija: rendered HTML grep `"@type":"FAQPage"` bez
dupliranja + regression na paru (245/246, 251/252 se ne smeju međusobno pomešati).

Procena: ~45–60 min (mehanički, nizak rizik, čist SEO dobitak — trenutno bez rich snippets).

---

## 🟡 Prioritet 2 — F7.1 standardi bez linka

Standard pomenut kao goli tekst, bez `<a href>` ka izvoru. Pravilo: link SAMO ako se
standard potvrdi (WebSearch/proizvođački sajt/već postojeći potvrđen izvor iz antistatik
pilota) — ništa se ne izmišlja.

| # | Stranica | ID | Standardi bez linka |
|---|---|---|---|
| [ ] | `/industrijski-podovi/` | 16567 | EN 660-2, ISO 6721, DIN 51130, EN 14041, ISO 10140, ISO 9001, ISO 14001 (7!) |
| [ ] | `/sportske-podloge/` | 5438 | FIBA, ITF, EN 14877 |
| [ ] | `/sportske-podloge/kosarkaske-konstrukcije/` | 16657 | FIBA, EN1270 (moj F6 pilot — građen PRE F7, ironično) |
| [ ] | `/podloge-za-futsal-terene/` | 16581 | FIBA, ITF |
| [ ] | `/kosarka-3x3-tereni/` | 16584 | FIBA, ITF |
| [ ] | `/dimenzije-kosarkaskog-terena/` | 16586 | FIBA |
| [ ] | `/dimenzije-kosarkaske-table/` | 16585 | FIBA |
| [ ] | `/spoljnje-podne-obloge/` | 16590 | ISO 9001 |
| [ ] | `/pocetna/` (home) | 16550 | FIBA (1 pomen, niska prioritetnost) |

Već potvrđeni izvori sa antistatik pilota (mogu se ponovo iskoristiti gde relevantno):
IEC 61340-5-1 → `webstore.iec.ch/en/publication/74748`, BS EN IEC 61340-5-1 →
`knowledge.bsigroup.com/...general-requirements-7`. Za FIBA/ITF/EN1270/EN14877/DIN 51130
itd. treba nova WebSearch potvrda po standardu (fiba.basketball, itftennis.com, ili
zvanični standard-body stranice).

Procena: ~1,5–2h (istraživanje + ubacivanje linkova na 9 stranica).

---

## 🟢 Prioritet 3 — antas-skica prilike

Najprirodniji fit u celom sajtu, a nula skica postoji van antistatik pilota:

| # | Stranica | ID | Predlog skice |
|---|---|---|---|
| [ ] | `/dimenzije-kosarkaskog-terena/` | 16586 | Dimenzije terena (FIBA/NBA/školski) — top-down dijagram sa kotama |
| [ ] | `/dimenzije-kosarkaske-table/` | 16585 | Dimenzije table (180×105) — dijagram sa kotama |
| [ ] | `/industrijski-podovi/` | 16567 | Presek slojeva (podloga → Ecotile ploča → završna obrada), po uzoru na antistatik pilot |
| [ ] | `/sportske-podloge/` | 5438 | Bergo klik-sistem — presek mehanizma uklapanja ploča |

Procena: ~45 min–1h (4 skice, stil već definisan u F7.4).

---

## 🔵 Prioritet 4 — video lite-embed prilike (niži prioritet)

| # | Stranica | ID | Predlog |
|---|---|---|---|
| [ ] | `/sportske-podloge/` | 5438 | Bergo ugradnja — traži zvaničan video (WebSearch + oEmbed potvrda, isti postupak kao antistatik) |
| [ ] | `/industrijski-podovi/` | 16567 | Ecotile generalna ugradnja (ne ESD-specifično — različit video od antistatik stranice) |

Sport pod-stranice (futsal/hokej/stoni-tenis/3x3) NE trebaju svaka svoj video — dovoljan
je jedan na `/sportske-podloge/` hub-u, cross-link odande.

**Namena ikonice**: nisu primenjive na sport pod-stranice (nisu namena-grid sekcije) —
bez akcije.

---

## Redosled preporučen

1. P1 (schema regresija) — najveći SEO rizik, nula rizika izvršenja, uraditi prvo
2. P2 (standardi-linkovi) — najveći F7-compliance gap po broju stranica
3. P3 (skice) — najveći vizuelni/GEO dobitak za dimenzije stranice
4. P4 (video) — opciono, niži prioritet

Svaka sledeća sesija bira JEDAN prioritet (po pravilu "jedan glavni zadatak po sesiji"),
štiklira redove ovde, i upisuje unos u [[DNEVNIK-NAPRETKA]].

## Veze
[[migracija/woodmart-sabloni]] (F7.1–F7.5 standard) · [[migracija/w1-red-cekanja]] (novi W1 rebuild red) · [[PROGRESS]]
