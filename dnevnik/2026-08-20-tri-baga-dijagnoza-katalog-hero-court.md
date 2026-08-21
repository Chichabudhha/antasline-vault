---
tip: template
alat: claude-code
datum: 2026-08-20
blok: C
status: u-toku
---

# Sesija — Tri prijavljena baga: katalog stranica, mobile hero, 3x3 planer terena (dijagnoza, bez izmena)

> M prijavio tri bага u istoj poruci na lokalnom buildu. Sesija je stigla do
> pune dijagnoze sve tri stavke i napravila backup baze — **implementacija je
> planirana ali NIJE izvršena** (prekinuto na usage-limit checkpoint pre nego
> što je ijedna izmena upisana u fajl ili bazu).

## Šta je urađeno

**Backup pre planiranih DB izmena:** `antasline-backups/antasline_local_2026-08-20_pre-3bug-fixes.sql` (37,3 MB). Baza i fajlovi build-a **nisu menjani** ovu sesiju — samo dijagnostika + backup.

**1. `/katalog/` — prikazuje sve proizvode umesto kategorija, mobilno bez ijednog proizvoda:**
- Post 16736 ("Katalog") je istovremeno **WooCommerce shop page** (`woocommerce_shop_page_id=16736`) — WooCommerce automatski renderuje `post_content` iznad product loop-a preko `woocommerce_product_archive_description()` (core mehanizam), pa se `post_content` (kategorija-grid shortcode) prikazuje ZAJEDNO SA punim product loop-om ispod, ne umesto njega.
- Jutrošnja sesija (batch izmene, FAZA 4) je dodala CSS koji ovo namerno preokreće po viewport-u: `.al-katalog-cats{display:none}` na desktopu + `@media(max-width:767px){.al-katalog-mobile .products, .woocommerce-pagination, .shop-toolbar, .wd-active-filters{display:none}}` na mobilnom. Rezultat tačno matches oba M-ova simptoma: desktop = samo proizvodi (kategorije sakrivene), mobile = samo kategorije (proizvodi sakriveni).
- **"Filteri" panel (filters-area sidebar)** postoji i sadrži pravi `woocommerce_product_categories-1` widget — ali kad se otvori (potvrđeno u browseru, `document.querySelector('a.open-filters').click()`), widget ispisuje **"Ne postoji kategorija proizvoda."** — prazan rezultat. Reprodukovano i preko `wp-cli.phar eval` (izolovan poziv `WC_Widget_Product_Categories->widget()`, isti prazan izlaz, `LEN=99`, exit 0) — bag je u samom widget-u/WooCommerce-WoodMart facet logici, ne u query kontekstu stranice. **Ne vredi ga debug-ovati dalje** — bezbednije zaobići.
- 17 top-level kategorija (iste koje već koristi `[woodmart_categories ids="303,403,246,254,251,371,252,370,253,250,248,372,249,302,369,247,245"]` grid u post_content) IMAJU proizvode (`wpgs_term_taxonomy.count` 1–24, potvrđeno upitom) — grid shortcode radi ispravno, samo je CSS-om sakriven na desktopu.
- **Plan (nije izvršen):** (a) ukloniti `display:none`/mobile-hide CSS iz `antas-design.css` tako da kategorije i proizvodi budu vidljivi na SVIM veličinama ekrana; (b) zaobići pokvareni WooCommerce widget — ugraditi kompaktan `<nav class="al-katalog-sidenav">` sa istih 17 kategorija (naziv/slug/broj proizvoda već izvučeni iz baze, vidi tabelu ispod), `<details>`-baziran (native HTML, bez JS) da na mobilnom radi kao pravi "side meni" (sažmi/razvij), na desktopu prikazan kao stalna bočna kolona preko `pointer-events:none` na `<summary>`.

Podaci za sidenav (redosled kao u postojećem grid shortcode-u):
`brodske-palube`(2) · `ergonomske-podloge`(8) · `industrijska-zastita`(24) · `industrijski-podovi`(10) · `kosarkaske-konstrukcije`(13) · `lvt-podovi`(8) · `oprema-za-sportske-terene`(7) · `parking-i-travne-resetke`(7) · `podloge-za-baste`(4) · `podloge-za-stale-i-trave`(1) · `podno-obelezavanje`(7) · `rampe-i-zavrsni-profili`(5) · `sigurnosni-senzori-i-signalni-sistemi`(2) · `sportske-podloge`(4) · `vestacka-trava`(10) · `zastita-cevi-i-kablova`(1) · `zastita-i-bumperi`(20). URL pattern potvrđen iz renderovane stranice: `/kategorija-proizvoda/{slug}/`.

**2. Mobile hero zatamnjenje ("kao na desktopu") — verovatno već zatvoreno pre ove poruke:**
- `.al-hero-photo::before` (54 stranice koriste ovu klasu, uklj. home) već ima mobile-specifičan `@media(max-width:767px)` overlay — uveden 2026-07-29 (W8 polish), **pojačan JUTROS istog dana** (`rgba(14,41,80,.62)`→`.8`, plus text-shadow) u istoj batch-izmene sesiji. Trenutna mobilna vrednost (0.8–0.88 uniformno) je VIŠA/tamnija od desktop peak vrednosti (0.94 na levoj ivici, opada na 0.28 desno) — kod izgleda ispravno.
- **Nisam mogao vizuelno potvrditi na uskom viewport-u** — `resize_window` alat u ovom browser-automation okruženju ne menja stvarnu veličinu prozora (ostaje zaključan na 1920×1080/1920×945 bez obzira na traženu vrednost, testirano na dva odvojena taba). CSS `zoom` trik na `documentElement` takođe ne menja `window.innerWidth`. Nisam pronašao way da forsiram media-query breakpoint u ovom okruženju.
- **Otvoreno pitanje za M:** da li je ovo I DALJE problem posle jutrošnje `.8` izmene, ili je poruka pisana pre nego što je ta izmena primećena? Ako i dalje nedovoljno tamno — treba mi screenshot sa stvarnog uskog viewporta (moj alat ne ume to da simulira u ovoj sesiji).

**3. Planer terena — 3x3 "čudnog izgleda", prevelike kocke, mora da se skroluje:**
- Jutrošnja izmena `court_m: [15,11]→[11,15]` (naučene-lekcije unos 2026-08-20) je **geometrijski ispravna** — proverio sam `lines()` funkciju za `basket3x3`: `rect(0,cy-2.45,5.8,4.9)` (ključ, 5.8m dubina od osnovne linije) i `halfArc(1.575,cy,6.75,1)` (FIBA 3x3 luk, radijus 6.75m) se crtaju DUŽ Cw ose — moraju imati Cw≥~8.3m dubine da geometrija ima smisla; sa Cw=11 (dubina) to je tačno, sa Cw=15 (kako je bilo pre) luk/ključ bi bili pogrešno proporcionalni. **Court_m array se NE vraća nazad.**
- **Pravi uzrok "prevelikih kocki + skrola" je u CSS-u**, ne u meters mapiranju: `al-court-builder.css` → `.al-cb__svg{width:100%;height:auto}` (dodato JUTROS kao "responsive fix") silom rasteže SVG na 100% širine kontejnera BEZ OBZIRA na prirodnu veličinu (`cols×14px`). Pošto 3x3 ima najmanji Cw (11m) od SVIH sportova na sajtu (kosarka 28, tenis 23.77, padel 20, rukomet 40, odbojka 18, badminton 13.41) → najmanje kolona → svaka ćelija (fiksnih 14px prirodno) se rasteže NAJVIŠE od svih sportova da popuni istu širinu kontejnera. Istovremeno redovi (Ch=15, najveći od svih "height" vrednosti) guraju SVG visinu daleko iznad `max-height:70vh` na `.al-cb__grid-wrap` (koji već ima `overflow:auto`) — otud i uvećanje i skrol, oba iz iste `width:100%` stretch mehanike.
- **Plan (nije izvršen):** `.al-cb__svg{width:100%}` → `max-width:100%` (zadržati `height:auto`, `display:block`) — sportovi sa PRIRODNO širim SVG-om (tenis, kosarka...) se i dalje skaliraju dole preko max-width kad treba; sportovi sa PRIRODNO uskim SVG-om (3x3) se više NE rastežu naviše, ćelije ostaju na konzistentnoj ~14px veličini kao kod ostalih sportova.

## Otvorene akcije
- [ ] Primeniti CSS izmenu u `al-court-builder.css` (`.al-cb__svg` width→max-width) #claude-code
- [ ] Primeniti CSS izmenu u `antas-design.css` (`.al-katalog-cats` + mobile-hide pravila) #claude-code
- [ ] Napisati i upisati novi `post_content` za post 16736 (sidenav + grid, jedna linija zbog wpautop-a) #claude-code
- [ ] Vizuelna verifikacija sve tri stavke u browseru posle izmene #claude-code
- [ ] Potvrditi da li mobile hero zatamnjenje i dalje nedovoljno — poslati screenshot ako da #ceka-miroslav

## Beleške / odluke
- `resize_window` (Claude-in-Chrome MCP alat) ne funkcioniše u ovom okruženju — prozor ostaje na fiksnoj rezoluciji bez obzira na traženu veličinu. Zabeleženo kao gotcha (v. dole) — svaka buduća mobilna vizuelna provera treba unapred da računa sa ovim ograničenjem.
- Sve tri dijagnoze su urađene isključivo čitanjem koda + DB upitima + browser JS introspekcijom (getComputedStyle, DOM pretraga), bez oslanjanja na nagađanje.

## Veze
- Jutrošnja sesija koja je uvela sve tri regresije: [[dnevnik/2026-08-20-batch-izmene-6-oblasti]]
- Backup: `C:\xampp\htdocs\antasline-backups\antasline_local_2026-08-20_pre-3bug-fixes.sql`
