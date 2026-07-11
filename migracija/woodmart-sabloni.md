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

## F7.2 — Icon set: namena + USP (2026-07-07)

Dodato 12 novih SVG ikonica u `woodmart-child/images/icons/`, isti stil kao postojećih 6
(viewBox `0 0 24 24`, `stroke="#F04D22"` width `1.7`, `stroke-linecap/linejoin="round"`, `fill="none"`):

- **Namena** (prati F6 `namena-*` product_tag termine): `namena-magacin-hala`, `namena-radionica`,
  `namena-sport-dvorana`, `namena-sportski-teren-otvoreni`, `namena-esd`, `namena-garaza`,
  `namena-terasa`, `namena-stala`. Kad F6 dobije novi termin (novi proizvod pokriva namenu koje
  nema) — nova ikonica se crta u istom stilu i dodaje ovde.
- **USP dopuna**: `garancija`, `sertifikat`, `dostava`, `telefon-podrska` (brza montaža i nosivost
  već pokriveni postojećim `montaza.svg` / `izdrzljivost.svg`).

Upotreba — ista kao postojeće ikonice, `<img class="al-icon" src=".../images/icons/NAZIV.svg" alt="" />`
unutar `.al-card__body`.

## F7.3 — Video lite-embed fasada (2026-07-07)

**Problem:** direktan YouTube iframe embed učitava YouTube JS/iframe odmah (loše za LCP/CWV).
**Rešenje:** thumbnail + play dugme (fasada), iframe se kreira TEK na klik/Enter/Space.

- CSS: `.al-video-facade` / `.al-video-facade__play` u `antas-design.css`
- JS: **globalni fajl** `woodmart-child/js/al-video-facade.js`, enqueue-ovan u `functions.php`
  (`in_footer=true`, `filemtime` verzionisanje) — **ne** vc_raw_html po stranici. Ovo zaobilazi
  gotcha #9 (CLI skida vc_raw_html) u potpunosti jer nema inline `<script>` u post_content-u;
  markup u sadržaju je čist HTML (div/img/button), bezbedan za `wp_insert_post` sa CLI-ja.
- Event delegation na `document` (klik + tastatura) — radi za bilo koji broj fasada na stranici
  bez dodatne inicijalizacije po elementu.
- `youtube-nocookie.com` domen (privacy-enhanced embed).

Markup obrazac (unutar `vc_column_text`, jedna linija):
```html
<div class="al-video-facade" data-yt-id="YOUTUBE_ID" data-title="Naslov videa" role="button" tabindex="0" aria-label="Pokreni video: ...">
  <img src="https://i.ytimg.com/vi/YOUTUBE_ID/hqdefault.jpg" alt="..." loading="lazy" width="480" height="270" />
  <button class="al-video-facade__play" aria-label="Pokreni video" type="button">&#9658;</button>
</div>
```

`VideoObject` JSON-LD ide kroz isti `vc_raw_html` base64 postupak kao FAQPage (gotcha #8/#9) —
`name`/`thumbnailUrl`/`embedUrl` iz potvrđenih izvora (YouTube oEmbed API,
`https://www.youtube.com/oembed?url=...&format=json`, bez auth). **`uploadDate` se izostavlja
ako nije potvrđen** (ne izmišljati) — smanjuje rich-result eligibility ali ne krši tvrdo pravilo.

**Video izvor**: PRVO proveri postoji li već pravi Ecotile/Bergo/Ergomat zvanični video
(WebSearch, `allowed_domains: youtube.com,<brend>.com`) — potvrdi da radi preko oEmbed pre
upotrebe (stari linkovi lako postanu privatni/obrisani, video 4-dNngajiCY iz starog lokalnog
posta 3318 je npr. sad "Forbidden" na oEmbed-u — ne koristiti ga ponovo dok se ne proveri).

Pilot: `/antistatik-i-elektroprovodljivi-podovi/` (ID 16658) — zvanično Ecotile "ESD Flooring -
How to install" (kanal ecotile-Germany, potvrđeno oEmbed-om).

## F7.4 — "antas-skica" stil (2026-07-07)

Standard za tehničke ilustracije (presek slojeva, dimenzije, koraci montaže):
- Linije: navy `#0E2950` za strukturu/dimenzije, crvena `#F04D22`/`#D43C14` samo za akcenat
  (npr. statički elektricitet, upozorenje) — nikad kao glavna linija
- Debljina: 2px za glavne konture, 1px za pomoćne/dimenzione linije, dashed za skrivene/unutrašnje
  detalje (npr. čelična vlakna u ploči)
- Font: Inter (`var(--al-text)`) za sve labele, 11–13px
- Pozadina: transparent/bela — nema fill osim svetlih tokena (`--al-mist`, `--al-paper`) za slojeve
- Dimenzione linije: tanka linija + kratke poprečne "tick" linije na krajevima + tekst pored
- CSS klasa `.al-skica` (font-family + `max-width:100%; height:auto`) u `antas-design.css`
- Fajlovi u `woodmart-child/images/skice/` (odvojeno od `icons/`)
- Ubacivanje: inline SVG direktno u `post_content` (ne `<img>` fajl) da ostane skalabilno i da
  labele nasleđuju font stranice — čitaj fajl i `str_replace(["\r","\n","\t"], '', $svg)` pre
  ubacivanja u shortcode string (spreči wpautop da razbije markup, ista logika kao gotcha #1)

Pilot primer: `esd-pod-presek-slojeva.svg` — presek ESD poda (betonska podloga → 7mm ESD ploča
sa čeličnim vlaknima → uzemljenje), korišćen na `/antistatik-i-elektroprovodljivi-podovi/`.

## F7.5 — Performanse-ograda (2026-07-07)

Svi F7 dodaci na pilot stranici (antistatik): ikonice ~250–400B/kom, JS fasada 972B (footer,
deferred), inline skica ~2,4KB (vektor, bez dodatnog HTTP zahteva), video iframe se NE učitava
dok se ne klikne (potvrđeno — HTML odgovor ne sadrži `<iframe>`, samo `<img loading="lazy">` +
dugme). ⚠️ **Lighthouse CLI nije dostupan u ovom okruženju** (`npx lighthouse` traži instalaciju) —
pun pre/posle LCP test ostaje za W3 3.5 (Lighthouse baseline sesija na celom buildu), gde će
imati pravi alat i baseline za poređenje. Proxy provera (veličina fajlova + deferred loading)
ne pokazuje regresiju.

## F7.6 — Reusable PHP helper pattern za nove stranice (2026-07-08)

Kad se gradi više stranica u istoj sesiji (npr. #13-#18), isplati se jedan `al-helpers.php` u scratchpad-u
(ne perzistira između sesija — samo napraviti ponovo po ovom obrascu) sa:
- `al_faq_jsonld($pairs)` — prima asocijativni niz pitanje→odgovor, vraća gotov `[vc_raw_html]...[/vc_raw_html]`
  blok (base64(rawurlencode(JSON-LD script))) — zamenjuje ručno pisanje/enkodovanje po stranici
- `al_update_content($id, $content)` — `$wpdb->update` + `clean_post_cache()` + brisanje
  `wpgs_yoast_indexable` reda (obavezno posle svake programske izmene `post_content`/meta, gotcha #12)
- `al_set_page($id, $title, $metadesc)` — postavlja `_woodmart_main_layout=full-width`,
  `_woodmart_title_off=on`, Yoast title/metadesc u jednom pozivu

Svaka nova stranica: `wp_insert_post()` sa praznim `post_content` (dobija ID), zatim `al_update_content()`
sa punim sadržajem (izbegava gotcha #9 — CLI insert/update briše `[vc_raw_html]`).

## F6 troslojni model — potvrđen van pilota (2026-07-08)

Drugi primer posle kosarkaske-konstrukcije pilota: `/industrijski-podovi/bumperi-zastita-za-police-regale-i-zidove/`
— 19 postojećih Ergomat bumper proizvoda već direktno u Woo kategoriji `Zaštita i Bumperi` (term_id 245, ne
poseban `namena-*` tag), pa je `[woodmart_products taxonomies="245" ...]` radio bez ikakve pripreme taksonomije.
Kad namenska landing tačno odgovara postojećoj Woo kategoriji (ne poduzorku preko namena-taga), koristiti
`taxonomies="<product_cat term_id>"` direktno — ne izmišljati novi `namena-*` tag ako nije potreban.
Cross-link u oba smera i dalje obavezan (kategorija je Layout Builder `woodmart_layout` CPT — v. dnevnik
"10 WooCommerce kategorija", term_id 245-254, str_replace na postojeći pasus, ne novi red).

## F7.7 — Footer + glavni meni (W1 1.4/1.5, 2026-07-08)

**Meni**: default lokalni `main-menu` (term_id 67) je imao samo 4 flat stavke (Početna/O nama/Aktuelnosti/Kontakt) —
Figma odluka "5 kategorija" (gotcha #6) nikad nije izvedena. Live WebFetch je izvor istine za punu strukturu
(Sport/Terase i dom/Industrija/Poslovni prostori/Specijalni podovi, ~34 podstavke, 1 pod-podstavka pod
"Oprema za sportske terene" → "Košarkaške konstrukcije"). Rebuild preko `wp_update_nav_menu_item()`
(NE direktan SQL insert — hendluje `_menu_item_*` meta i `menu-item-parent-id` hijerarhiju ispravno):

```php
wp_update_nav_menu_item( $menu_id, 0, array(
    'menu-item-title'     => 'Naslov',
    'menu-item-object-id' => $post_id,       // ili term_id za taxonomy tip
    'menu-item-object'    => 'page',         // 'post' / 'product' / 'product_cat' itd.
    'menu-item-type'      => 'post_type',    // 'taxonomy' za kategorije
    'menu-item-parent-id' => $parent_item_id, // 0 = top level, inače povratna vrednost roditeljskog poziva
    'menu-item-position'  => $order,
    'menu-item-status'    => 'publish',
) );
```

Pre upisa: potvrditi da SVI target slug-ovi postoje lokalno (`SELECT ID, post_name, post_parent FROM
wp_posts WHERE post_name IN (...)`) — u ovoj sesiji svih ~34 URL-a je već postojalo (W1 1.2 red čekanja
zatvoren ranije), nula 404.

**Footer** — WoodMart footer je "kolona = zasebna sidebar" model, ne jedna sidebar sa auto-raspoređivanjem:

- `sidebar-footer.php` zove `dynamic_sidebar('footer-' . $index)` PO KOLONI. Broj kolona dolazi iz
  `woodmart_get_opt('footer-layout')` (default `13` = pet kolona) → `footer-1` ... `footer-5` sidebar-ovi
  se registruju dinamički u `theme-setup.php`. Widgeti se raspoređuju preko
  `sidebars_widgets['footer-N'] = array('widget-id')` — SVE u `footer-1` = sve u prvu kolonu, ostale prazne
  (prva pogrešna pretpostavka ove sesije).
- Postojeći NEAKTIVNI widgeti (`wp_inactive_widgets`) često nose prave stare podatke iz teme/migracije
  pre WoodMart-a (`follow-us-widget-2` je već imao tačne social linkove, `custom_html-3` "Kontaktirajte nas"
  tačan telefon) — proveriti `get_option('widget_<type>')` PRE pisanja novog sadržaja, reaktivacija je brža
  i manje rizična od pisanja ispočetka.
- Bela varijanta logoa: swap svih obojenih/teget `fill="#..."` na `#FFFFFF`, I OBRNUTO originalni
  `fill="#ffffff"` (negative-space cutout unutar mark-a) na navy — replicira identičan optički efekat
  (pozadina "proviruje" kroz cutout) na tamnoj pozadini kao original na svetloj.

### 🔴 KRITIČAN gotcha — `xts-woodmart-options` je SVE-ILI-NIŠTA, ne merge

WoodMart-ov `XTS\Admin\Modules\Options::load_options()` radi `self::$_options = get_option('xts-woodmart-options')`
— **REPLACE, ne merge** — svaki put kad je ta DB opcija truthy (neprazna). `load_defaults()` (koji puni SVIH
~883 registrovanih polja default vrednostima) se izvršava PRE toga (`init` prioritet 100 vs 110) i ostaje
netaknut SAMO ako je opcija prazna/falsy. Direktan `update_option('xts-woodmart-options', ['moj_kljuc' => 'x'])`
sa par ključeva BRIŠE sve ostale default-e (npr. `disable_footer`, `disable_copyrights`, `footer-layout`) —
`footer.php` čita te ključeve BEZ default argumenta u `woodmart_get_opt()` pozivu, pa missing = `false` =
**ceo `<footer>` element se tiho renderuje prazan** (ni copyrights bar). Nema PHP greške, nema warning-a —
samo nestane sadržaj.

**Pravilan postupak za izmenu bilo kog WoodMart theme option-a preko koda (ne UI):**
1. Napraviti privremeni `wp-content/mu-plugins/zz-fix-TEMP.php` (MORA biti mu-plugin — `init` hook mora da se
   zakači PRE `wp-load.php` završi bootstrap, obično CLI skripta sa `require wp-load.php` na vrhu je već
   prekasno jer se `init` odradi TOKOM tog require-a)
2. U mu-pluginu: `add_action('init', function(){ $defaults = \XTS\Admin\Modules\Options::get_options();
   $full = array_merge($defaults, $moji_override_kljucevi); update_option('xts-woodmart-options', $full); }, 105);`
   (prioritet 105 = između `load_defaults`@100 i `load_options`@110, hvata pun default niz pre nego što se
   prepiše). **⚠️ Dopuna 2026-07-10: koristiti TROSMERNU merge** — `array_merge($defaults,
   get_option('xts-woodmart-options') ?: [], $overrides)` — jer na 105 `get_options()` vraća SAMO
   default-e (DB kastomizacije se učitavaju tek na 110), pa dvosmerna merge gubi sve ranije izmene
   (footer, boje...). Trosmerna verzija verifikovana (futer preživeo 2 uzastopne izmene opcija).
3. Pokrenuti JEDAN normalan front-end request (curl na bilo koju stranicu) da se mu-plugin izvrši
4. Obrisati mu-plugin fajl odmah

**Drugi keš sloj — CSS se NE regeneriše automatski posle `update_option`:** WoodMart generisani CSS iz
theme opcija (uključujući `.wd-footer{background-color:...}` iz `footer-bar-bg` polja) je keširan preko
`XTS\Modules\Styles_Storage` (data_name `theme_settings_default`), status `xts-theme_settings_default-status`
mora biti `'valid'` DA BI SE KORISTIO keš — invalidacija normalno ide preko `xts_after_theme_settings` action-a
koji ima guard (`$_GET['settings-updated']`/`$_GET['page']`) i ne okida se van pravog admin-save HTTP zahteva.
Fix: `(new \XTS\Modules\Styles_Storage('theme_settings_default'))->reset_data(); ->delete_css();` — sledeći
front-end request automatski regeneriše CSS iz trenutnih opcija (`print_styles()` na `wp` hook proverava
`is_css_exists()` i piše svež CSS ako je false).

## F7.8 — Footer/meni polish krug (2026-07-08, nastavak F7.7)

Pet vizuelnih ispravki posle prve footer/meni verzije — sve rešeno u istoj sesiji:

- **Bela linija ispod poslednje sekcije**: `main.wd-content-layout` nosi sitewide `padding-bottom:40px`
  (WoodMart default) — nevidljivo na belim/mist završecima, otkriveno kad stranica završava
  `al-section--navy` CTA-om. Fix: `main.wd-content-layout:has(.al-section) { padding-bottom: 0; }`
  (scoped preko `:has()` na stranice koje koriste naš sistem, ne dira default Woo/blog stranice).
- **Ikonice u sadržaju treba da prate `al-icon` stil** (isti stroke/viewBox kao USP kartice), ne generičke
  Porto/tuđe SVG-ove zaostale iz starih widget-a. Za inline icon+tekst (ne card layout) koristiti
  `.al-icon--sm` (20px, `display:inline-block`) — bazni `.al-icon` je `display:block` 46px sa
  `margin-bottom`, lomi inline layout ako se ne override-uje.
- **Social ikonice — koristiti WoodMart-ov native `[social_buttons]` shortcode**
  (`woodmart_shortcode_social()`, `inc/shortcodes/social.php`), NE custom pill-dugmad/SVG. Primer:
  ```php
  do_shortcode('[social_buttons type="follow" social_links_source="custom" style="default" form="circle" color="light" fb_link="..." isntagram_link="..." linkedin_link="..." pinterest_link="..."]');
  ```
  (pažnja: parametar je `isntagram_link`, tipfeler ugrađen u temu, ne popravljati). Renderuje prave
  icon-font glyph-ove iz `woodmart-font` seta. **Mora se pre-renderovati preko `do_shortcode()` i snimiti
  kao statičan HTML** ako ide u `custom_html` widget — taj widget tip namerno NE prolazi kroz
  `do_shortcode()` (WP core sigurnosna odluka). Brend boje se override-uju preko istih CSS custom
  properties koje shortcode koristi (`--wd-social-color/-bg/-brd-color[-hover]`), scope-ovano na
  wrapper klasu (npr. `.wd-footer .wd-social-icons`).
- **Sticky header cramping = simptom, ne uzrok** — ako se sticky header čini "preuzak", prvo proveriti da
  li se glavni meni prelama u 2 reda (čak i u ne-sticky stanju) pre nego što se diže `sticky_height`.
  Uzrok je često prezasićen `mainmenu` (previše top-level stavki/dugih reči na 1222px kontejneru) — rešiti
  raspodelu stavki (v. sledeća stavka) PRE podešavanja visine. `--nav-gap` na `.wd-nav` (default 20px)
  je prvi lako podesiv parametar da se dobije još prostora bez sečenja stavki.
- **Sekundarni/utility meni (odvojen od glavnog kategorija-menija)**: header builder `Menu` element
  (razlikuje se od `Mainmenu`) prima `menu_id` DIREKTNO (ne zavisi od theme `nav_menu_locations`), pa
  se može ubaciti bilo koji WP meni (`wp_create_nav_menu()`) u bilo koju header-builder kolonu
  (`'type' => 'menu', 'params' => ['menu_id' => ['value' => $term_id, 'type' => 'select'], ...]`).
  Koristiti za "gornji red" utility linkove (Početna/O nama/Kontakt/Aktuelnosti) odvojeno od glavnog
  kategorija-menija u `general-header` redu. 🔴 **Mobile parity**: ako je top-bar/kolona gde utility meni
  živi markirana `hide_mobile: true`, taj meni je NEVIDLJIV na mobilnom — dodati isti sadržaj (linkovi) i u
  `mobile-menu-widgets` sidebar (postojeća WoodMart oblast "Area after the mobile menu", obično prazna).
- 🔴 **Header builder CSS keš je ODVOJEN od theme options keša** (v. F7.7 `xts-woodmart-options` gotcha) —
  isti `XTS\Modules\Styles_Storage` mehanizam, ali drugi `data_name`: `default_header` (ne
  `theme_settings_default`). Izmena `sticky_height`/bilo čega u `woodmart_default_header_structure`
  filteru se ne pojavljuje dok se ne resetuje:
  ```php
  (new \XTS\Modules\Styles_Storage('default_header'))->reset_data();
  (new \XTS\Modules\Styles_Storage('default_header'))->delete_css();
  ```

## F7.9 — CF7 + katalog režim gotcha-i (2026-07-08, polish Faza 0)

- 🔴 **CF7 čita formu iz `_form` POSTMETA, ne iz `post_content`** (isto: mail iz `_mail`,
  poruke iz `_messages`). Programski kreirana forma upisom samo u `post_content` se renderuje
  PRAZNA (samo hidden polja) i mail nikad ne odlazi — bez greške, bez warning-a. Pravi način:
  `WPCF7_ContactForm::get_instance($id)` → `get_properties()` → izmena → `set_properties()` →
  `save()` (save sinhronizuje i post_content).
- 🔴 **CF7 tag gramatika: `[tip ime OPCIJE... "VREDNOSTI"...]`** — sve opcije MORAJU pre
  quoted vrednosti; opcija posle quoted vrednosti (`placeholder "X" autocomplete:tel`) obara
  ceo tag (renderuje se kao goli tekst). Opcije koriste dvotačku (`autocomplete:tel`,
  `default:get`), NIKAD HTML-atribut sintaksu (`autocomplete="tel"` — znak `="` takođe obara
  tag). Ispravno: `[text* ime autocomplete:tel default:get placeholder "Tekst"]`.
- **CF7 prefill iz URL-a**: opcija `default:get` na polju → vrednost iz GET parametra sa
  ISTIM imenom kao polje (`?form-naslov=...`). Koristi se za "Zatražite ponudu" tok sa
  proizvoda (`add_query_arg('form-naslov', rawurlencode('Ponuda: '.$product->get_name()), ...)`).
- **`wpcf7_mail_sent` PHP hook NE može da echo-uje `<script>` ka posetiocu** — izvršava se u
  AJAX/REST kontekstu, output se odbacuje (ili kvari JSON odgovor). Za post-submit ponašanje
  (redirect, tracking) koristiti front-end `document.addEventListener('wpcf7mailsent', ...)`
  kroz `wp_footer`.
- **WoodMart `catalog_mode`** (theme option): skida add-to-cart sa loop-a i single-a +
  redirektuje cart/checkout na home. NE skida compare/wishlist/reviews — to su odvojene
  opcije (`compare`, `compare_on_grid`, `wishlist`, `product_loop_wishlist`,
  `enable_reviews_tab` + WC `woocommerce_enable_reviews`). Prazan "Shipping & Delivery" tab
  dolazi iz `additional_tab_title` default vrednosti — isprazniti title da nestane. Sve kroz
  mu-plugin merge postupak (F7.7).
- **"Zatražite ponudu" CTA na proizvodu**: `woocommerce_single_product_summary` prioritet 30
  (= tačna pozicija uklonjenog add-to-cart dugmeta). `.al-btn--ghost` je bele boje — na beloj
  proizvod stranici nevidljiv, treba navy override (`.al-product-quote .al-btn--ghost`).
- 🔴 **Shop stranica ne nastaje sama**: `woocommerce_shop_page_id` može da pokazuje na
  nepostojeći post (ovde 1614 iz starog importa) → shop URL 404 bez ikakve greške u adminu.
  Fix: kreirati stranicu, `update_option('woocommerce_shop_page_id', $id)`,
  `flush_rewrite_rules(true)`. Lokalno: `/katalog/` = ID 16736.
- **Sticky toolbar custom linkovi**: `sticky_toolbar_fields` prima `link_1`..`link_5`
  (svaki ima `link_N_url`/`link_N_text`/`link_N_icon` opcije) — pun srpski tekst i `tel:` URL
  rade. Bez attachment ikonice span `.wd-tools-icon` ostaje prazan → ikonica preko CSS
  `background-image` na `.wd-toolbar-link-N .wd-tools-icon` (child `images/icons/` SVG-ovi).

## F7.10 — Brzi upit / dinamička forma gotcha-i (2026-07-09)

Puna strategija i uputstvo: [[migracija/brzi-upit-forma]]. Ovde samo gotcha-i:

- 🔴 **CF7 `[_post_title]`/`[_post_url]` mail tagovi rade SAMO kad je forma renderovana
  `in_the_loop()`** — CF7 tada upisuje hidden `_wpcf7_container_post` = get_the_ID()
  (`contact-form.php:719`); van loop-a (npr. `wp_footer`) container = 0 i tagovi se
  resolve-uju u PRAZAN string (bez greške). Za sitewide ubacivanje forme: `the_content`
  filter **prio 12** (posle wpautop@10/do_shortcode@11; sopstveni markup + ručni
  `do_shortcode()` na CF7 shortcode — ne prolazi wpautop).
- 🔴 **`wpcf7mailsent` se NE okida ako `wp_mail` ne uspe** (→ `wpcf7mailfailed`) — na XAMPP-u
  bez SMTP-a redirect na hvala stranicu deluje "pokvareno" a problem je mail transport.
  Lokalni fix: mu-plugin `pre_wp_mail` logger (`al-local-mail-log.php`) koji vraća `true` i
  loguje u `wp-content/mail-log.txt` — bolji od `wpcf7_skip_mail` jer se mail template
  KOMPAJLIRA pa se vidi resolve special tagova. ⚠️ OBRISATI pre produkcije.
- **`form-row`/`form-col-6` klase u CF7 markupu do 2026-07-09 NISU imale CSS nigde** —
  stilizovano u `antas-design.css` (`.wpcf7 .form-row` flex + `flex: 1 1 240px` kolone =
  auto-stack na mobilnom bez media query-ja).
- **`scrollIntoView({behavior:'smooth'})` se ne animira u automatizovanom Chrome tabu**
  (rAF ne radi u nefokusiranom tabu) — ista familija kao `loading="lazy"` nalaz (F7 P4).
  Instant varijanta dokazuje tačan target; smooth radi za pravog korisnika.
- 🔴 **Woo permalink opcija + `flush_rewrite_rules(true)` u ISTOM PHP procesu ne radi** —
  taksonomija se registruje na `init` sa STAROM vrednošću opcije, pa flush upiše stara
  pravila (bez greške; simptom: novi URL-ovi 404, stari i dalje 200). Fix: flush u SVEŽEM
  procesu/requestu posle izmene opcije. (Nađeno pri `tag_base` → `oznaka-proizvoda` fixu.)
- 🔴 **CF7 mail_2 (auto-reply) čini email polje faktički OBAVEZNIM** — mail_2 recipient
  `[form-email]` sa praznim poljem obara CEO submit u `mail_failed` (nema redirecta na
  hvala stranicu, nema konverzije). CF7 nema uslovno slanje mail_2 → `[email* ...]`.
- 🔴 **`woodmart_products` shortcode upisuje kolone kao INLINE stil** (`--wd-col-lg:3` na
  grid elementu) — CSS override kolona mora `!important`:
  `.wd-products.grid-columns-3 { --wd-col-lg: 4 !important; }`.
- **`wd-more-desc` hover blok se izliva van kartice** — WoodMart base hover na hover
  prikazuje sirovi `post_content` excerpt u absolute fade bloku koji se širi ISPOD kartice
  preko sledećeg reda (naročito ružno dok su opisi blok teksta, pre polish Faze 1).
  Ugašeno globalno: `.wd-product .wd-more-desc { display: none; }`.
- **Full-bleed sekcija iz `the_content` konteksta**: viewport breakout
  `width:100vw; margin-left:calc(50% - 50vw)` radi SAMO kad je content kolona centrirana
  u viewportu — na layoutu sa sidebar-om (blog postovi) breakout je iskošen (levo iseče,
  desno rupa). Fix: `body:has(.sidebar-container)` override vraća blok u kolonu.
  Stranice (page) nemaju `.sidebar-container` u DOM-u uopšte, postovi imaju — pouzdan uslov.
- 🔴 **base.css `:is(.entry-content, ...) > :where(:last-child) { margin-bottom: 0 }` gazi
  al-diag negativni margin-bottom** kad je diag sekcija POSLEDNJE dete entry-content-a —
  `:is()` nosi specifičnost najspecifičnijeg argumenta (ovde `.is-layout-constrained >
  .wp-block-group__inner-container` = 0,2,0), pa dvoklas selektor gubi na kasnijem redu
  učitavanja. Simptom: bela traka visine --al-cut između sekcije i futera. Fix: selektor
  sa TRI klase (npr. `.al-section.al-diag-top.al-quick-quote`). Diag u shorthand sudaru:
  `padding` shorthand kasnijeg pravila gazi `padding-top` iz `.al-diag-top` — cut se mora
  uračunati u sopstveni padding-top.

## F7.11 — Shop filteri (layered nav) gotcha-i (2026-07-10, polish Faza 1 #8)

- **Mehanika**: opcija `shop_filters='1'` (mu-plugin merge, F7.7) prikazuje "Filters" dugme na shop
  toolbaru; klik otvara `filters-area` sidebar (`dynamic_sidebar('filters-area')`) iznad grida.
  Widget: `WOODMART WooCommerce Layered Nav` (id_base `woodmart-woocommerce-layered-nav`), instanca:
  `title`, `attribute` (slug BEZ `pa_`), `category:['all']`, `query_type` (and/or), `display:'list'`,
  `labels/tooltips/checkboxes`, `search_by_filters`. Widget se renderuje samo na `is_shop()`/product
  taksonomijama i sakriva termine bez proizvoda u tekućem prikazu.
- 🔴 **WoodMart AUTO ubacuje "Sort by" i Price filter widgete** u filters-area čim je `shop_filters`
  uključen (`woodmart_before_filters_widgets` hook) — sa Price/Rating opcijama koje u katalog režimu
  nemaju smisla. Gase se theme opcijama `hide_sort_by='1'` + `hide_price_filter='1'`. Posledica
  gašenja: default WC sort dropdown se vraća u toolbar (lokalizovan, prihvatljivo).
- 🔴 **WC `wc_product_attributes_lookup` tabela je STALE posle direktnih `wp_set_object_terms` upisa**
  (`woocommerce_attribute_lookup_enabled=yes` — Filterer iz nje računa brojeve uz filtere; sinhronizuje
  se samo na pravi product save). Fix posle svakog programskog dodeljivanja pa_ termina:
  `wc_get_container()->get(LookupDataStore::class)->create_data_for_product($product)` po proizvodu
  + obrisati `_transient_wc_layered_nav_counts_*`. (Ovde: 113→413 redova.)
- ✅ **REŠENO 2026-07-10 — Filteri na kategorijama**: WoodMart ima gotove layout shortcode-ove
  `[woodmart_shop_archive_filters_area_btn]` (dugme, poziva `woodmart_filter_buttons`) i
  `[woodmart_shop_archive_filters_area]` (oblast, poziva `woodmart_shop_filters_area`) — definisani u
  `inc/modules/layouts/wpb/shortcodes/shop-archive/`. Ubačeni bez atributa neposredno PRE
  `[woodmart_shop_archive_products]` u svih 10 layouta (16571–16580) — rade jer je `shop_filters=1`
  već upaljen, widgeti u `filters-area` imaju `category:['all']`, a layered-nav sam krije termine
  bez proizvoda u tekućoj kategoriji.

## F7.12 — Faza 2 (postovi) + mobile QA lekcije (2026-07-10)

- 🔴 **F3 reimport ostavlja DUPLE postmeta redove šire nego što se znalo** — na 2542 nađen 4×
  `_thumbnail_id` (ranije već dedupe-ovan Yoast na 2542/4318). Pri svakom Faza 2 restyle-u:
  `SELECT meta_key, COUNT(*) ... GROUP BY meta_key HAVING COUNT(*)>1` pa dedupe.
- 🔴 **Goli FAQPage JSON-LD (bez `<script>` taga) je OBRAZAC, ne izolovan bug** — potvrđen na
  odbojci (W2 #9) i na conquest 2542: JSON stoji kao vidljiv tekst na dnu posta, wpautop ga
  mangle-uje, schema ne radi. Proveriti na svakom reimportovanom postu: `strpos(post_content,
  '"@context"')` bez okolnog `<script`.
- **Linkovi na `https://www.antasline.com/...` u reimportovanim postovima** → zameniti lokalnim
  (parity; na migraciji URL replace vraća produkcioni domen).
- **`.single-post .wd-sidebar-opener` sakriven** (antas-design.css) — WoodMart fixed burger za blog
  sidebar lebdeo preko teksta na mobilnom; sidebar drži samo default widgete.
- **Neprevedeni WoodMart stringovi** ("Continue reading", "Categories") → `gettext_woodmart` filter
  u child functions.php (mapa string→prevod), ne .po fajl.
- **Mobile QA metod**: Chrome `resize_window` NE menja viewport (window manager ignoriše) →
  same-origin **iframe širine 390px** u praznom tabu; media queries u iframe-u reaguju na širinu
  iframe-a. Automatski smoke po stranicama: `scrollWidth>390` (h-overflow), broj `<h1>`, slomljene
  slike (`img.complete && naturalWidth===0`); `lazy.svg` unosi su WoodMart lazy placeholder, ne greška.
- 2 posta su u kategoriji НЕКАТЕГОРИЗОВАНО (term 64) — vidljivo kao ćirilični bedž na home blog
  karticama; dodeliti prave kategorije tokom Faza 2 batch-a.
- 🔴 **Goli JSON-LD može biti i LAŽNA Review schema** (2298: izmišljena recenzija "Sava Marković"
  5/5 kao vidljiv tekst na vrhu posta) — takve se UKLANJAJU u potpunosti, ne pakuju u script tag
  (fabricated review = Google spam policy + "ne izmišljati" pravilo). FAQPage/legit scheme → script tag.
- 🔴 **AI-chat ostaci javno objavljeni u sadržaju** (treći slučaj 2026-07-11, politika-kolacica 16656:
  uvod "U nastavku je primer...", citat linkovi sa `utm_source=chatgpt.com`, završna sekcija koja se
  obraća Miroslavu i preporučuje tuđe alate) — na svakom F2 postu grep-ovati: `chatgpt`, "primer",
  "preporuč", "pomenuo", citat linkove ka nepovezanim domenima. Ranije viđeno i u ergomat opisima
  (`avantorsciences.com+6` otpad).
- 🔴 **`post_author=0` na 28/30 F3 reimportovanih postova** (rešeno globalno 2026-07-11) — simptom:
  prazan byline sa 404 linkom na `/author/`. Fix: `post_author=1` + user 1 nicename `savamar` /
  display "Miroslav Marković" (live parity) + yoast_indexable regen. Novi reimporti: proveriti odmah.
- 🔴 **mysql CLI kvari dijakritike I SA `--default-character-set=utf8mb4`** kad su u inline `-e`
  stringu iz Git Bash-a (ć upisan kao literalno `?`). String upisi sa ne-ASCII karakterima →
  ISKLJUČIVO PHP/wp-load (`$wpdb->update`). Provera ispravnosti: `SELECT HEX(kolona)` (ć = `C487`).
  Napomena: python print takvih vrednosti u Windows konzoli prikazuje `�` i kad su bajtovi ispravni —
  proveravati bajtove, ne konzolni prikaz.

## Otvoreno
- [x] ✅ 2026-07-10 — Mobilni viewport vizuelna provera (W1 1.6): 15 stranica smoke čist, toolbar/filteri/spec-tabele/futer OK; metod gore (F7.12)
- [ ] Figma sync — čeka link od Miroslava #ceka-miroslav
