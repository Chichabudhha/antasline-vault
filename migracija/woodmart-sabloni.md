---
tip: migracija
datum: 2026-07-05
namena: WPBakery šabloni + design system klase za WoodMart rebuild stranica
---

# WoodMart rebuild — šabloni i pravila

> Design system: `woodmart-child/css/antas-design.css` (tokeni + utility klase) ·
> Fontovi self-hosted u `woodmart-child/fonts/` · Header definisan u `woodmart-child/functions.php`
> (filter `woodmart_default_header_structure`) · Boje: [[reference/brend-knjiga]]

## ⚠️ Pravila koja su već napravila bug (ne ponavljati)

1. **wpautop**: HTML unutar `[vc_column_text]` mora biti **u jednoj liniji** unutar grid/hero blokova — svaki prelom reda postaje `<br>`/`<p>` i razbija CSS grid (viđeno na home kartcama; ista lekcija kao odbojka refresh na live)
2. **Slug kolizija**: nove stranice se prave SAMO ako ne postoje; ako slug postoji → sufiks **5** (odluka Miroslava 2026-07-05)
3. **Copy izvor**: sadržaj stranica iz `migracija/live-export-2026-07-05/live-pages-2026-07-05.xml` + Yoast meta iz `live-inventar-2026-07-05.csv` (parity!)
4. **WPBakery clearfix**: `.vc_row:before` nameće `display:table` — custom `::before` overlay na vc_row MORA imati eksplicitno `display:block; width:100%; height:100%`
5. **CSS keš**: enqueue koristi `filemtime` verzionisanje — ne vraćati fiksnu verziju
6. **Figma odluke (2026-07-05)**: naslovi Bebas uppercase (ne Inter Bold iz Figme) · header CTA telefon 072 (ne "Zatražite ponudu") · 5 kategorija · 6 USP kartica
7. **Grid kartice sa h3/p unutra = samo `div` omotači**: `<a class="al-card">`/`<span class="al-card__body">` oko blok elemenata wpautop uvija u `<p>` i lomi markup — link ide u `<h3><a>`, kartica ostaje `div` (viđeno na industrijski grid--4)
8. **`vc_raw_html` enkoding**: `base64_encode(rawurlencode($html))` — obrnut redosled (`rawurlencode(base64_encode())`) daje prazan output
9. **`wp_insert_post`/`wp_update_post` iz CLI skida `[vc_raw_html]`** (kses/save filteri bez ulogovanog korisnika) — JSON-LD blokove ubacivati direktnim `$wpdb->update` na `post_content` + `clean_post_cache($id)`
10. **Porto backtick parametri = segfault rizik**: shortcode-ovi sa `{``kljuc``:``vrednost``}` parametrima ruše PHP (PCRE backtracking u parse_atts, ne pomaže no-op registracija). Rušio /o-nama/ i staru home. ✅ **Sanirano 2026-07-05 na svih 7 preostalih stranica** (porto_* tagovi skinuti, backtick atributi uklonjeni, sadržaj/layout očuvani; originali u scratchpad `backtick-pages-original.json`). Pri rebuildu i dalje NE kopirati stare porto_* shortcode-ove iz live exporta
12. **PHP single-quoted stringovi ne interpretiraju `\xNN` hex escape (2026-07-08)**: pisanje UTF-8 karaktera (npr. em-dash u Yoast title-u) preko `'\xe2\x80\x94'` u single-quoted stringu ostavlja literalni tekst `xe2x80x94` u bazi/meti — `\x` escape radi samo u double-quoted stringovima. Fix: ili kucati stvarni UTF-8 karakter direktno u fajlu (kao svuda u post_content-u, bez problema), ili koristiti double-quoted string. Posle ispravke title/meta polja, obavezno i obrisati keširani red iz `wpgs_yoast_indexable` (`DELETE ... WHERE object_id=X`) da se prisili regeneracija — inače stari (pogrešan) naslov ostaje keširan u `<title>` tag-u.
11. **`margin-top` na `.vc_row` se ne primenjuje (2026-07-07)**: `full_width="stretch_row"` ubacuje prazan `<div class="vc_row-full-width vc_clearfix">` (float:left, height:0) između svaka dva reda — to poništava negativni `margin-top` na sledećem redu (computed stil ispravan, render pozicija nula efekta, potvrđeno testom). Zato `al-diag-top`/`al-diag-top--rev` preklop (rez koji treba da otkrije boju prethodne sekcije) nikad nije radio kako treba — ostajao beli trougao na CTA prelazima, najvidljivije na mobile. **Fix**: obe klase sada koriste `position: relative; top: calc(-1 * var(--al-cut))` (+ kompenzujući `margin-bottom`) umesto `margin-top`. Ne vraćati na `margin-top` pristup za ove klase.

## Utility klase (antas-design.css)

| Klasa | Efekat |
|---|---|
| `al-section` + `--navy/--blue/--mist/--paper` | sekcija sa vertikalnim ritmom i bojom |
| `al-diag-top` / `al-diag-top--rev` | kosi rez na vrhu (uvlači se u prethodnu sekciju preko `position:relative;top:` — ne `margin-top`, vidi gotcha #11) |
| `al-diag-bottom` / `--rev` | kosi rez na dnu |
| `al-section--compact` | kratak uvodni red (kicker) ispred CTA — samo padding-top, bez punog `--al-gap` ritma |
| `al-plates` | potpis: nagnute "listajuće ploče" iz logoa (desno, skew -7°) — samo na navy sekcijama |
| `al-label` | narandžasti/crveni micro-label sa kosim crtama `/ TEKST /` |
| `al-display--xl` / `--lg` | Bebas displej naslovi (h1/h2 su ionako Bebas) |
| `al-btn` / `al-btn--ghost` | crveni CTA paralelogram / okvirna varijanta |
| `al-grid al-grid--4/--3` | responsive grid (4→2→1) |
| `al-card` + `al-card__media/__title/__body` | foto kartica sa overlay naslovom |
| `al-stat` + `al-stat__num/__cap` | USP brojka (Bebas crvena) + caption |
| `al-ref-row` | red logotipa/imena referenci |
| `al-hero` + `al-hero__sub/__cta` | hero blok |
| `al-hero-photo` (+ modifikator sa slikom) | foto hero sa navy gradijent overlay-em |
| `al-icon` | SVG ikonica 46px — fajlovi u `woodmart-child/images/icons/` (6 kom: montaza, izdrzljivost, protivklizna, fleksibilna, odrzavanje, izgled). **Nove ikonice crtati u istom stilu**: viewBox 24, stroke #F04D22, width 1.7, round caps |
| `al-logo-row` | traka logotipa (grayscale, hover color) |

## Šablon: SILO LANDING stranica (industrijski, sportski, terase, parking)

```
[vc_row full_width="stretch_row" el_class="al-section al-section--navy al-plates al-diag-bottom"][vc_column][vc_column_text]
<div class="al-hero"><span class="al-label">KATEGORIJA</span><h1 class="al-display--xl">NASLOV IZ LIVE YOAST TITLE</h1><p class="al-hero__sub">Intro iz live sadržaja, 2 rečenice.</p><div class="al-hero__cta"><a class="al-btn" href="[kontakt]">Zatražite ponudu</a><a class="al-btn al-btn--ghost" href="tel:+381692340072">069 234 00 72</a></div></div>
[/vc_column_text][/vc_column][/vc_row]
[vc_row full_width="stretch_row" el_class="al-section al-section--paper"][vc_column][vc_column_text]
... live body sadržaj (H2 sekcije, tabele, cena) — višelinijski OK van grid-ova ...
[/vc_column_text][/vc_column][/vc_row]
[vc_row full_width="stretch_row" el_class="al-section al-section--mist al-diag-top"][vc_column][vc_column_text]
... FAQ blok (iz C3 draftova gde postoje) + FAQPage JSON-LD u vc_raw_html ...
[/vc_column_text][/vc_column][/vc_row]
[vc_row full_width="stretch_row" el_class="al-section al-section--navy al-plates al-diag-top--rev"][vc_column][vc_column_text]
<div class="al-hero"><span class="al-label">Kontakt</span><h2 class="al-display--lg">Spremni za novi pod?</h2>...CTA isto kao home...</div>
[/vc_column_text][/vc_column][/vc_row]
```

Obavezno po stranici: `_woodmart_main_layout` = `full-width` · `_woodmart_title_off` = `on` ·
Yoast title/metadesc iz inventara (ili C3 draft ako je nova stranica) · interni linkovi po C3 pravilima.

## Home referenca (implementirano 2026-07-05)

Stranica ID **16550** — hero (navy+plates) → 4 segment kartice → USP 25/0/1 → reference row →
blog `[woodmart_blog items_per_page="3" blog_design="masonry" blog_columns="3" img_size="medium_large"]` → CTA (navy+plates, rev dijagonala).
Slike segmenata: ecotile-floor-1.jpg · Spanoulis-Belgrade-7-1.jpg · podovi-za-terase-i-balkone-1.jpg · parking-sa-runfloor-podlogom.webp

## Redosled rebuilda (prati [[seo/plan-novih-stranica]] + redirect mapu)

1. ✅ Home
2. ✅ /industrijski-podovi/ (2026-07-05, ID 16567) — stara 4937 → draft + slug `industrijski-podovi-stara`, nova preuzela čist slug (home kartica već linkuje tamo). FAQ + FAQPage/Product JSON-LD u vc_raw_html. Cena-FAQ izostavljen (čeka `/industrijski-podovi-cena/`)
3. Nove C3 stranice (ne postoje → čist slug): /industrijski-podovi-cena/, /podovi-za-garaze/, /gumeni-podovi-za-terase-cena/, /dimenzije-kosarkaskog-terena/, /dimenzije-kosarkaske-table/, /podloge-za-parkiraliste-cena/
4. Silo (u toku): ✅ /sportske-podloge/ (2026-07-06, ID 5438) → spoljne-podne-obloge, podloge-za-parking, kontakt, o-nama
5. Ostale iz live inventara (50 pages)

### /sportske-podloge/ (2026-07-06, ID 5438)
- Content parity izvučen iz live SiteOrigin `panels_data` (post_id 1849, serialized PHP — ne WPBakery na live!) + postojeći lokalni WPBakery sadržaj (12 sport kartica, USP boxovi, specifikacija, 4 FAQ)
- Sekcije: hero (navy) → intro+6 USP kartica (paper) → grid 11 sport disciplina (mist, linkovi ka postojećim lokalnim stranicama) → specifikacija Bergo Ultimate (paper) → FAQ + FAQPage JSON-LD (mist) → CTA (navy)
- ⚠️ Nova lekcija: `/bergo-ultimate/` (ID 15480) je **child stranica** `/sportske-podloge/` (post_parent), pravi URL je `/sportske-podloge/bergo-ultimate/` — direktan `/bergo-ultimate/` daje 301. Proveriti post_parent pre linkovanja na child stranice iz hub-a.
- Yoast title/metadesc preneti sa live (nisu postojali lokalno)
- 200 verifikovano, JSON-LD validiran (4 pitanja), sve slike (11 kartica) i svi link target-i (200/200) provereni

## Šablon: NAMENSKA LANDING (rešenje hub) — F6 troslojni model

**Kad koristiti:** namenske/rešenje-hub stranice (npr. `/podovi-za-garaze/`, `/sportske-podloge/kosarkaske-konstrukcije/`)
koje pokrivaju VIŠE proizvoda za istu namenu — ne opis jednog proizvoda. Razlikuje se od Woo kategorije
istog naziva: stranica je edukativna/informativna (nosi istoriju rangiranja, GEO prvi pasus = direktan odgovor),
kategorija je transakciona/browse. Obavezan cross-link u OBA smera (stranica → kategorija u intro pasusu,
kategorija → stranica u svom intro pasusu).

### Taksonomija — `namena-*` product_tag termini

Termini se kreiraju u `product_tag` taxonomy (odvojeno od `product_cat`), prefiks `namena-` da se razlikuje
od brend/tip-proizvoda tagova (F6 F folder — `bergo`, `ergomat` itd., van ovog obima). Kreiranje:

```php
wp_insert_term('Sportska dvorana', 'product_tag', ['slug' => 'namena-sport-dvorana']);
```

Dodela proizvodu (append, ne replace — proizvod može imati i druge product_tag vrednosti):

```php
wp_set_object_terms($product_id, ['namena-sport-dvorana', 'namena-sportski-teren-otvoreni'], 'product_tag', true);
```

Lista termina se **prilagođava stvarnoj ponudi** — pre kreiranja izvuci sve proizvode sa kategorijama/opisima
i predloži tabelu proizvod→namena Miroslavu na potvrdu PRE upisa (on zna ponudu bolje od kategorija/opisa).

### Grid mehanika — `woodmart_products` shortcode

WPBakery/WoodMart shortcode filtriran po `product_tag` (ili bilo kojoj product taksonomiji) preko atributa
`taxonomies` — **očekuje term ID (broj), ne slug**:

```
[woodmart_products taxonomies="266" columns="3" items_per_page="6" post_type="product" layout="grid" lazy_loading="yes"]
```

Term ID se dobija iz `wp_insert_term()` povratne vrednosti (`$result['term_id']`) ili `get_term_by('slug', ..., 'product_tag')->term_id`.
Auto-mehanika je potvrđena radna (2026-07-07, F6 pilot): dodavanje/uklanjanje taga na proizvodu menja grid
na stranici BEZ ikakve izmene `post_content` — dovoljno je da editor doda `namena-*` tag pri unosu novog proizvoda.

Izvor u temi: `inc/shortcodes/products.php` — `taxonomies` atribut ide kroz `get_terms(['include' => $taxonomies])`,
zatim `tax_query` po `slug` sa `include_children => true`, relacija `OR` (menjivo preko `query_type` atributa).

### Struktura stranice (redosled sekcija)

```
1. Hero (navy+plates+diag-bottom) — h1 + intro + CTA
2. Intro + uporedna tabela (paper) — GEO prvi pasus (direktan odgovor) + <table class="al-table">
   (model/tip/specifikacija/namena/cena) + cross-link ka Woo kategoriji istog naziva
3. Auto grid (mist) — [woodmart_products taxonomies="ID" ...] filtriran po namena tagu
4. FAQ (paper) + FAQPage JSON-LD (vc_raw_html, isti postupak kao ostale stranice — vidi gotcha #8/#9)
5. CTA (navy+plates+diag-top--rev)
```

Nova CSS klasa `.al-table` dodata u `antas-design.css` (navy header red, zebra redovi, `overflow-x:auto` wrapper
za mobile — tabela ima `min-width:640px` pa je omotač obavezan).

### Pilot (2026-07-07): `/sportske-podloge/kosarkaske-konstrukcije/` (ID 16657)

Prvi troslojni model primenjen u praksi — `namena-sport-dvorana` (5 proizvoda: Street Sport, Lite Shot 325,
Mini Shot 225, MicroShot 125, Zglobni obruč) se poklopio tačno sa W1 SEO prioritetom #1 (923 GSC klika,
čekala ga redirect mapa) — F6 pilot i W1 rebuild zadatak spojeni u jednu sesiju. Sadržaj izveden iz live
WebFetch-a (ZionBuilder serialized data na live-u, nije lako parsirati direktno — WebFetch rendered tekst
je brži put za content parity kad je live gradio drugim builderom).


## F7.x gotcha hronologija — izmešteno u dubinsku referencu

Detaljni dnevnik feature-po-feature gotcha-a (ikonice F7.2, video fasada F7.3,
"antas-skica" F7.4, footer/meni F7.7–F7.8, CF7/dinamička forma F7.9–F7.10,
layered nav F7.11, mobile QA F7.12, Product JSON-LD dupliranje F7.13,
`_woodmart_main_layout` F7.14, `wp_insert_post` kses F7.15, testimonials F7.16,
foto baner F7.17, `_woodmart_title_off` samo za stranice F7.18, custom
property pravilo F7.19, dijagonalni rezovi F7.20, slike+lightbox F7.21,
GEO-intro/CTA-box F7.24, "Reference" sekcija provera F7.25 — 2026-07-07 do
2026-08-07): [[migracija/woodmart-gotchas-detalji]]

## Otvoreno
- [ ] ⏳ Kuriranje fotografija — pun prolaz kroz sve stranice (v. F7.21 u [[migracija/woodmart-gotchas-detalji]]). Urađeno: 16657. Ostaje ~28 stranica bez slika + dopuna postojećih.
- [x] ✅ 2026-07-10 — Mobilni viewport vizuelna provera (W1 1.6): 15 stranica smoke čist, toolbar/filteri/spec-tabele/futer OK; metod u F7.12 ([[migracija/woodmart-gotchas-detalji]])
- [x] ✅ 2026-07-22 — Testimonials sekcija (F7.16 u [[migracija/woodmart-gotchas-detalji]])
- [x] ✅ 2026-07-22 — "Najprodavanije podloge u 2025." foto baner (F7.17 u [[migracija/woodmart-gotchas-detalji]]) — **W1 1.7 u potpunosti zatvoren**
