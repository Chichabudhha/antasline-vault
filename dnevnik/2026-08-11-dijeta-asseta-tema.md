---
datum: 2026-08-11
tag: claude-code
oblast: W3 / CWV / performanse
status: zatvoreno
---

# 2026-08-11 — Pregled teme: šta se učitava a ne koristi (dijeta asseta)

## Zadatak (M)

„Pregledaj temu i da li pokreće neke stvari koje se ne koriste, da i to isključimo
da se ne učitava bez potrebe."

## Rezultat u brojkama

Merено sumiranjem stvarnih veličina svih `<script src>` i `<link rel=stylesheet>`
fajlova po tipu stranice, pre i posle (obe izmene privremeno gašene pa ponovo
paljene — merenje reproducibilno u dva prolaza):

| Tip stranice | Pre | Posle | Ušteda |
|---|---:|---:|---:|
| **proizvod** (94 kom) | 1.117 KB | 606 KB | **−511 KB (−46%)** |
| **post** (31 kom) | 924 KB | 456 KB | **−468 KB (−51%)** |
| **blog arhiva** | 772 KB | 269 KB | **−503 KB (−65%)** |
| **/katalog/** | 1.061 KB | 552 KB | **−509 KB (−48%)** |
| kategorija proizvoda | 1.051 KB | 997 KB | −54 KB |
| početna | 772 KB | 722 KB | −50 KB |
| silo stranica | 771 KB | 757 KB | −14 KB |
| /kontakt/ | 771 KB | 757 KB | −14 KB |

Najveći efekat je baš na **money stranicama** (proizvodi) i na svim postovima.

## Šta je isključeno i zašto

Sve u novom fajlu `woodmart-child/inc/al-asset-diet.php` (uključen iz `functions.php`).

### 1. WPBakery CSS 437 KB + JS 17 KB — samo tamo gde nema nijednog `vc_` elementa

Najveća pojedinačna stavka na sajtu. WPBakery **sam ima** ispravnu proveru
(`Vc_Base::enqueueStyle()` traži `[vc_row` u sadržaju), ali je **WoodMart pregazi**:
`inc/enqueue.php:616` enqueue-uje `js_composer_front` bezuslovno čim je
`WPB_VC_VERSION` definisan.

Izmereno u `<body>` (ne u celom dokumentu — u `<head>` su `vc_` selektori iz inline
CSS-a, pa naivno brojanje daje lažni pozitiv):

- 94 proizvoda → **0** pojava `class="vc_`
- `/katalog/`, `/aktuelnosti/` → **0**
- 30 od 31 posta → **0** (jedini izuzetak 17027)
- 67 od 71 stranice → **ima** → ostaju na punom CSS-u

Konzervativno: **arhive kategorija se ne diraju** iako je deo njih čist — neke vuku
WPBakery iz WoodMart `cms_block` sloja koji se ne vidi iz `post_content`, a cena
greške (razbijen raspored kategorije) je veća od uštede.

Sigurnosna mreža: ako neki shortcode ipak krene da se renderuje posle hook-a,
`WPBakeryShortCode::enqueueDefaultScripts()` vraća i CSS i JS — stranica tada dobije
stil iz footera (kasnije, ali ne izgubljeno).

### 2. WooCommerce Blocks CSS 13,7 KB — sa SVAKE stranice

Nijedna objavljena stranica ne koristi `wp:woocommerce/*` blok (0/196 u `post_content`);
sadržaj je WPBakery, ne Gutenberg. Fajl stilizuje block-based WooCommerce obaveštenja
(korpa/naplata), kojih u katalog režimu nema.

### 3. Contact Form 7 — 25,3 KB JS tamo gde forme nema

CF7 je učitavao svoj JS na svih 8 tipova stranice, a forma se stvarno renderuje samo
na `/kontakt/` (shortcode, forma 16593) i na singular page/post kroz „Brzi upit"
(16737). Nema je na proizvodima, kategorijama, `/katalog/` i `/aktuelnosti/`.
Detekcija čita **isti** `al_quick_form_excluded_slugs` filter kao i sama injekcija,
da se dve liste ne raziđu.

### 4. Četiri mrtve WoodMart skripte — preko native opcije

`scripts_not_use` (Theme Settings → Performance → „Scripts never load"):
`add-to-cart-all-types`, `action-after-add-to-cart`, `quick-shop`, `woocommerce-quantity`.
Sve mrtvo jer je `catalog_mode` = true (nema nijedne `<form class="cart">` na sajtu)
i `product_quantity` = false.

Korišćen je theme-ov sopstveni mehanizam umesto dequeue hakova.
`advanced_js` **nije** diran — provereno da je samo UI gate za prikaz polja u
podešavanjima, enqueue kod (`woodmart_enqueue_js_script()`) ga ne čita.

## 🔴 Šta je namerno NIJE isključeno

**`wc-add-to-cart-variation` + `wc-jquery-blockui` + `underscore` + `wp-util`
(~43,7 KB).** Izgledalo je mrtvo — katalog režim, nula `<form class="cart">` na celom
sajtu — i dequeue je bio izveden i radio. Provera pre zatvaranja ga je oborila:

> 20 varijabilnih proizvoda i dalje renderuje `variations_form`, a WoodMart-ov
> `swatchesVariations.min.js` se oslanja na `wc_add_to_cart_variation_params` iz baš
> te skripte. **Katalog režim skida DUGME, ne i varijacijsku formu.**

Potvrđeno uživo u Chrome-u na Ecotile E500/7: izbor boje u „Boja" padajućem meniju
menja sliku (`ecotile-e500-7-crna-ai-standard-600x600.webp` → `ecotile-500-7-crna.jpg`)
i prikazuje varijacijski blok. Da je dequeue ostao, ovo bi tiho prestalo da radi —
funkcija napravljena 2026-08-08 (Condor Schools/Playgrass, Tournament 20, Multisport MX).

Ako se tih 43,7 KB ikad bude tražilo, put je zameniti WooCommerce inicijalizaciju
sopstvenim mini-handlerom za swatch→slika, ne prosto ugasiti skriptu.

## 🔵 Otvorena odluka za M — najveći preostali dobitak

**WoodMart opcija `light_wpb_css` („Use simplified WPBakery styles")** zamenjuje pun
`js_composer.min.css` (437 KB) theme-ovim `light-wpbakery.css` (**33 KB**) —
ušteda **404 KB na svakoj stranici koja WPBakery i dalje koristi** (67 stranica +
kategorije + početna). To je jedini preostali potez tog reda veličine.

**Nije uključena** jer provera pokrivenosti pokazuje da lagana verzija ne nosi 24 klase
koje stvarno koristimo, među njima:

| Klasa | Pojava | Šta bi se videlo |
|---|---:|---|
| `wpb_row`, `wpb_text_column` | 447 / 442 | donje margine svakog tekstualnog bloka → poremećen vertikalni ritam **na svim stranicama** |
| `vc_btn3*` (8 klasa) | 5 | nestilizovana dugmad |
| `vc_single_image-wrapper`, `vc_figure`, `vc_box_border_grey` | 5 | okviri/figure oko slika |
| `vc_masonry_grid`, `vc_media_grid` | 3 | galerijske mreže |

(`wpb_wrapper`, `wpb_column`, `wpb_content_element` su pokriveni — njih stilizuje sama
WoodMart tema, 71/192/26 pravila.)

**Predlog:** izvodljivo je, ali traži da se tih ~10 pravila prepiše u `antas-design.css`
plus vizuelni prolaz kroz glavne šablone. To je 1–2 sata sa realnim rizikom po izgled,
5 dana pred content freeze. **Preporuka: posle live-a**, osim ako M ne odluči da je
404 KB/stranici vredno rizika sada.

## Usput popravljeno

🟢 **2 slike 404** (`sta-staviti-preko-postojeceg-poda-1024x768.jpg`,
`podna-obloga-preko-ostecene-podloge-u-stampariji-1024x576.jpg`) na postu 16613.
Pre-postojeće (post izmenjen 07.08), nije od ovog rada. Promakle su 10.08 jer je 16613
`noindex`, a tadašnji sweep ide kroz sitemap — **tačno ona slepa tačka opisana istog
dana**. Fajlovi postoje lokalno sa `-1` sufiksom, ali je primenjen isti postupak kao
10.08: povučeni originali sa live-a na tačne putanje, **bez prepisivanja `src`**
(prepis bi razišao lokal od live-a).

## Beleške / gotchas

🔴 **Dequeue stilova na `wp_enqueue_scripts` nema efekta na ovoj temi.** WoodMart svoje
stilove kači vrlo kasno: `woodmart_enqueue_base_styles` na **prioritetu 10000**,
`woodmart_force_enqueue_styles` na **10001**. Naš prolaz mora na **10002**. Prvi
pokušaj (prioritet 100) je tiho prošao bez ijedne promene.

🔴 **`wc-blocks-style` se ne može dequeue-ovati na `wp_enqueue_scripts` ni na jednom
prioritetu** — WooCommerce ga stavlja u red iz `Blocks/Domain/Services/Notices.php`
na **`wp_head` prioritet 10**, dakle posle celog `wp_enqueue_scripts` ciklusa.
Dijagnostika: na `wp_head` 999 stoji „u queue: DA, done: ne", i nije ničija zavisnost.
Hook mora biti `wp_head` na 11.

⚠️ **`curl -o fajl` u ovom git bash okruženju upisuje 0 bajtova** (i u `/tmp` i u job
folder), dok `curl -o /dev/null -w '%{http_code}'` radi normalno. Za analizu HTML-a
koristiti PHP (`file_get_contents`/`curl_multi`), ne bash curl.

⚠️ **Brojanje `vc_` po celom HTML dokumentu daje lažni pozitiv** — inline CSS u
`<head>` sadrži `vc_row` selektore. Meriti isključivo u `<body>`.

## Verifikacija

- `al_verify.php` + provera slika: **212 URL-ova · HTTP≠200: 0 · ≠1×H1: 0 ·
  PHP greške: 0 · slike ≠200: 0 · naslovna slika bez fajla: 0**
- Chrome, uživo: **proizvod** (galerija 14 sličica, PhotoSwipe+zoom, 9 tabova prebacuje,
  breadcrumb, CTA) · **varijacije menjaju sliku** · **post** (GEO-intro, sidebar, „Brzi
  upit" forma sa svim poljima i submit-om) · **blog arhiva** (masonry, datum-badževi) ·
  **/katalog/** (kategorijska navigacija, mreža, filteri, sortiranje) · **/kontakt/**
  (CF7 učitan, 5 polja, submit) · **mega meni** se otvara
- **0 grešaka u konzoli** na proizvod i kontakt stranici
- Kontrolno: CF7 JS **jeste** na postu i kontaktu, **nije** na proizvodu/katalogu;
  `js_composer` CSS **jeste** na kontaktu/početnoj/kategoriji, **nije** na
  proizvodu/postu/blogu/katalogu

**Backup:** `antasline-backups/antasline_local_2026-08-11_pre-asset-diet.sql` +
`woodmart-child/functions.php.bak-2026-08-11-pre-asset-diet`

## Veze

- [[PROGRESS]] · [[DNEVNIK-NAPRETKA]] · [[dnevnik/PERFORMANCE-AUDIT]]
- [[reference/naucene-lekcije]] (3 nove gotcha-e)
- [[migracija/woodmart-sabloni]] (WoodMart enqueue prioriteti)
- [[dnevnik/2026-08-11-legacy-cpt-ciscenje]] (prethodni zadatak iste sesije)
