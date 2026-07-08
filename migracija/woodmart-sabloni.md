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

## Otvoreno
- [ ] Mobilni viewport vizuelna provera (media queries napisani, nije snimljeno) #claude-code
- [ ] Bela varijanta logoa za navy footer #claude-code
- [ ] Footer builder (WoodMart HTML block) — još default #claude-code
- [ ] Figma sync — čeka link od Miroslava #ceka-miroslav
- [ ] Meni: proširiti sa novim silo stranicama kad se izgrade
