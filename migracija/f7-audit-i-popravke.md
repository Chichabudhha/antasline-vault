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
| [x] | `/industrijski-podovi/` | 16567 | FAQ+FAQPage/**Product** JSON-LD, 2026-07-05 | ✅ 2026-07-08 — 7 Q&A FAQPage + Product (AggregateOffer 2.000–5.500 RSD) vraćeno |
| [x] | `/spoljnje-podne-obloge/` | 16590 | "FAQ schema", 2026-07-07 (N1 silo) | ✅ 2026-07-08 — stvarni slug `spoljnje-podne-obloge` (ne "spoljne"); imala golu JSON tekst (1321 znakova) van shortcode-a (verovatno nezavršen prethodni pokušaj) → obrisano, 5 Q&A FAQPage ispravno upakovano u `vc_raw_html` |
| [x] | `/dimenzije-kosarkaske-table/` | 16585 | — | ✅ 2026-07-08 — 5 Q&A FAQPage dodato (prvi put) |
| [x] | Kategorija Zaštita i Bumperi | 16572 (term 245) | deo "6 punih" (hero+USP+FAQ+CTA) | ✅ 2026-07-08 — 3 Q&A FAQPage, regresija na 246 potvrđena (nisu pomešani) |
| [x] | Kategorija Industrijska zaštita | 16573 (term 246) | isto | ✅ 2026-07-08 — 3 Q&A FAQPage |
| [x] | Kategorija Košarkaške konstrukcije | 16578 (term 251) | isto | ✅ 2026-07-08 — 3 Q&A FAQPage, regresija na 252 potvrđena |
| [x] | Kategorija Oprema za sportske terene | 16579 (term 252) | isto | ✅ 2026-07-08 — 3 Q&A FAQPage |

**P1 ZATVOREN 2026-07-08.** Postupak: `$wpdb->update` + `clean_post_cache()` (bez `wp_update_post`), FAQ tekst izvučen iz postojećeg `post_content` (h3/p ili `al-faq` div parovi), JSON-LD kroz `[vc_raw_html]base64(rawurlencode())[/vc_raw_html]` ubačen odmah posle FAQ sekcije. Verifikovano: 200/1×H1/schema bez dupliranja (Yoast graph i naš FAQ/Product su odvojeni `<script>` blokovi, ne sukobljavaju se) na svih 7 + regresija 2 nedirane stranice (`/kontakt/`, `/sportske-podloge/`).

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
| [x] | `/industrijski-podovi/` | 16567 | ✅ 2026-07-08 — EN 660-2, ISO 6721, DIN 51130, EN 14041, ISO 10140, ISO 9001, ISO 14001 (7!) svi linkovani |
| [x] | `/sportske-podloge/` | 5438 | ✅ 2026-07-08 — FIBA, ITF, EN 14877 linkovani (jedna "Sertifikovano" kartica) |
| [x] | `/sportske-podloge/kosarkaske-konstrukcije/` | 16657 | ✅ 2026-07-08 — FIBA, EN1270 linkovani (u uporednoj tabeli) |
| [x] | `/podloge-za-futsal-terene/` | 16581 | ✅ 2026-07-08 — FIBA/ITF linkovani |
| [x] | `/kosarka-3x3-tereni/` | 16584 | ✅ 2026-07-08 — FIBA, ITF linkovani |
| [x] | `/dimenzije-kosarkaskog-terena/` | 16586 | ✅ 2026-07-08 — FIBA linkovan (postojeći "Izvor:" citat) |
| [x] | `/dimenzije-kosarkaske-table/` | 16585 | ✅ 2026-07-08 — FIBA linkovan |
| [x] | `/spoljnje-podne-obloge/` | 16590 | ✅ 2026-07-08 — ISO 9001 linkovan |
| [x] | `/pocetna/` (home) | 16550 | ✅ 2026-07-08 — FIBA linkovan |

**P2 ZATVOREN 2026-07-08.** Svi linkovi potvrđeni WebSearch-om (FIBA→about.fiba.basketball, ITF→itftennis.com, EN1270/EN14041→knowledge.bsigroup.com, EN14877→standards.globalspec.com, DIN51130→dinmedia.de, ISO9001/14001→iso.org explainer, ISO10140-3/ISO6721-1→iso.org standard page, EN660-2→landingpage.bsigroup.com), format `target="_blank" rel="noopener"` (isti kao antistatik pilot). Postupak: `str_replace` na unique anchor tekstu (proveren `substr_count()===1` pre upisa) preko `$wpdb->update`+`clean_post_cache()`. Jedan link po standardu po stranici (ne svako pominjanje) — izabran najčistiji/najprirodniji kontekst (tabela ili prva suštinska rečenica, ne hero/FAQ ponavljanja). Verifikovano: 200/1×H1/linkovi validni na svih 9 + P1 i P2 zajedno testirani na svih 13 stranica + regresija 2 nedirane stranice.

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
| [x] | `/dimenzije-kosarkaskog-terena/` | 16586 | ✅ 2026-07-08 — top-down dijagram (28×15m, centralni krug, reket, troseks linije, dashed slobodno bacanje) sa kotama |
| [x] | `/dimenzije-kosarkaske-table/` | 16585 | ✅ 2026-07-08 — front-view dijagram table+koša (1,80×1,05m, obruč na 3,05m) sa kotama |
| [x] | `/industrijski-podovi/` | 16567 | ✅ 2026-07-08 — presek slojeva (podloga → Ecotile 5–10mm → klik spoj), crveni akcent za viljuškarski saobraćaj |
| [x] | `/sportske-podloge/` | 5438 | ✅ 2026-07-08 — Bergo klik-sistem, presek dve ploče na nožicama sa klik-prstenovima i provetravanjem |

**P3 ZATVOREN 2026-07-08.** 4 nova SVG fajla u `woodmart-child/images/skice/` (dimenzije-terena-fiba.svg, dimenzije-table-kosarka.svg, industrijski-pod-presek-slojeva.svg, bergo-klik-sistem-presek.svg), stil po F7.4 (navy `#0E2950` struktura, crvena akcent samo, dimenzione linije sa tick oznakama, dashed za skrivene detalje). Ubačeno inline (minified, `str_replace(["\r","\n","\t"],'')`) preko `$wpdb->update`+`clean_post_cache()`. Dva sitna vizuelna bag-fixa nakon prve provere u Chrome-u: (1) tabla dijagram — "3,05 m" label sečen na ivici viewBox-a (380→410 širina), (2) Bergo dijagram — "Klik-prstenovi" labela prenatrpana uz "Bergo ploča" naslov (razdvojeno + dodata leader linija). Verifikovano vizuelno (Chrome screenshot) na sve 4 + 200/1×H1/bez neizrendovanih shortcode-ova.

Procena: ~45 min–1h (4 skice, stil već definisan u F7.4).

---

## 🔵 Prioritet 4 — video lite-embed prilike (niži prioritet)

| # | Stranica | ID | Predlog |
|---|---|---|---|
| [x] | `/sportske-podloge/` | 5438 | ✅ 2026-07-08 — "Bergo Multisport court installation" (zvanični kanal Bergo Flooring AB, oEmbed potvrđen) |
| [x] | `/industrijski-podovi/` | 16567 | ✅ 2026-07-08 — "How to Install Ecotile Garage Flooring" (zvanični kanal Ecotile Flooring Ltd, oEmbed potvrđen) |

**P4 ZATVOREN 2026-07-08 — F7 AUDIT U POTPUNOSTI ZATVOREN.** Video izvori potvrđeni WebSearch+oEmbed (`author_name`/`author_url` provereni pre upotrebe): Ecotile `fncQrsTvHoE` (@EcotileflooringSolutions), Bergo `VdZWT2O5_-M` (@BergoflooringSweden, "Multisport court" — tematski najbolji fit za sportsku stranicu). Markup = F7.3 lite-embed fasada (thumbnail+play dugme, globalni `al-video-facade.js`, iframe se kreira tek na klik) + `VideoObject` JSON-LD (bez `uploadDate`, nije potvrđen) preko `vc_raw_html`, ubačeno odmah posle antas-skice na obe stranice preko `$wpdb->update`+`clean_post_cache()`. Verifikovano: 200/1×H1/`<iframe>` odsutan iz initial HTML-a (potvrđuje lazy fasadu)/VideoObject schema prisutna/klik na dugme kreira iframe sa tačnim video ID-jem (testirano na obe stranice) — isto ponašanje kao verifikovani antistatik pilot. Regresija na 2 nedirane stranice čista.

Sport pod-stranice (futsal/hokej/stoni-tenis/3x3) NE trebaju svaka svoj video — dovoljan
je jedan na `/sportske-podloge/` hub-u, cross-link odande.

**Namena ikonice**: nisu primenjive na sport pod-stranice (nisu namena-grid sekcije) —
bez akcije.

---

## Redosled preporučen

1. ✅ ZATVORENO 2026-07-08 — P1 (schema regresija) — najveći SEO rizik, nula rizika izvršenja, uraditi prvo
2. ✅ ZATVORENO 2026-07-08 — P2 (standardi-linkovi) — najveći F7-compliance gap po broju stranica
3. ✅ ZATVORENO 2026-07-08 — P3 (skice) — najveći vizuelni/GEO dobitak za dimenzije stranice
4. ✅ ZATVORENO 2026-07-08 — P4 (video) — opciono, niži prioritet

Svaka sledeća sesija bira JEDAN prioritet (po pravilu "jedan glavni zadatak po sesiji"),
štiklira redove ovde, i upisuje unos u [[DNEVNIK-NAPRETKA]].

## Veze
[[migracija/woodmart-sabloni]] (F7.1–F7.5 standard) · [[migracija/w1-red-cekanja]] (novi W1 rebuild red) · [[PROGRESS]]
