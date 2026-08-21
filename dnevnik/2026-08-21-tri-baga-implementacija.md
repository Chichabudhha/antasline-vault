---
tip: template
alat: claude-code
datum: 2026-08-21
blok: C
status: zavrseno
---

# Sesija — Implementacija tri baga iz 20.08 (katalog sidenav, mobile-hide uklonjen, 3x3 planer)

> Nastavak [[dnevnik/2026-08-20-tri-baga-dijagnoza-katalog-hero-court]] — jučerašnja
> sesija je stala na punu dijagnozu bez ijedne izmene. Danas primenjen ceo plan za
> dve od tri stavke (treća, mobile hero, ostaje #ceka-miroslav — nije dirana).

## Šta je urađeno

**Backup:** ponovo iskorišćen `antasline_local_2026-08-20_pre-3bug-fixes.sql` (37,3 MB) —
baza nije menjana između jučerašnjeg backupa i početka današnje sesije, pa je i dalje
tačan pre-izmena snapshot. Nema novog backupa.

**1. `/katalog/` — sidenav umesto pokvarenog WC widgeta + uklonjen viewport-toggle:**
- `antas-design.css`: obrisano `.al-katalog-cats{display:none}` + ceo `@media(max-width:767px)`
  blok koji je sakrivao proizvode na mobilnom — kategorije i proizvodi sad vidljivi na SVIM
  veličinama ekrana (JS provera: nema više nijednog `al-katalog` selektora ni u jednom media
  bloku u učitanom stylesheet-u).
- Dodat `.al-katalog-layout` (flex) + `.al-katalog-sidenav` stilovi: `<details>`-baziran nav
  (native HTML, bez JS), `pointer-events:none` na `summary` od 768px naviše (stalna bočna
  kolona na desktopu, sažmi/razvij na mobilnom).
- `post_content` posta **16736** prepisan: novi `<nav class="al-katalog-sidenav">` sa svih 17
  kategorija (naziv + broj proizvoda iz `wpgs_term_taxonomy`, redosled kao u postojećem grid
  shortcode-u, linkovi na `/kategorija-proizvoda/{slug}/`) + zadržan originalni
  `[woodmart_categories]` grid shortcode. Upisano preko `UNHEX()` (Windows CRLF gotcha),
  pročitano nazad i bajt-za-bajt upoređeno sa namenjenim sadržajem — **match: True**.
- Rank Math sitemap keš obrisan (`uploads/rank-math/*.xml`) pre verifikacije — poznat gotcha
  (18.08, 20.08) da stari XML fajlovi maskiraju svežu izmenu.

**2. 3x3 planer terena — CSS fix:**
- `al-court-builder.css`: `.al-cb__svg{width:100%}` → `max-width:100%` (zadržano `height:auto`,
  `display:block`).
- Verifikovano JS-om: 3x3 SVG sad prirodne veličine **196×196px**, `wrap.scrollHeight ==
  wrap.clientHeight` (nema skrola). Regresiona provera na Košarci (FIBA, najveći SVG na sajtu,
  1050×560 prirodno) — i dalje se skalira dole na širinu kontejnera (898×479, isti aspect ratio),
  fix nije pokvario veće sportove.

**3. Mobile hero zatamnjenje — pravi uzrok nađen i ispravljen (M prijavio "zatamnjenje je samo
na nekim stranicama", nastavak iste sesije nakon prvog izveštaja):**
- Nije bio mobile-only bag i nije trebalo `resize_window` da bi se video — `.al-hero-photo::before`
  i `.al-plates::before` su na **istom `vc_row` elementu** na 53 od 54 hero stranica (sve sem
  početne, koja nema `.al-plates`). Element može imati samo jedan `::before` — kaskada ga sklapa
  **po svojstvu, ne po pravilu**, pa svojstva koja `.al-hero-photo::before` ne postavlja eksplicitno
  procure iz `.al-plates::before` (linija ~210): `transform: skewX(var(--al-skew))` i `opacity: 0.55`.
- Potvrđeno direktno u browseru (`getComputedStyle(el,'::before')`): početna (bez `.al-plates`) =
  `opacity:1, transform:none` (ispravno); `/industrijski-podovi/` i `/antistatik-i-elektroprovodljivi-podovi/`
  (obe sa `.al-plates`) = `opacity:0.55, transform:matrix(1,0,-0.1228,1,0,0)` pre fixa — zatamnjenje
  upola bleđe i koso (šuplji trouglovi u uglovima), i to **na desktopu i mobilnom podjednako**, ne
  samo na uskom viewportu.
- **Fix:** dodato `opacity: 1; transform: none;` direktno u `.al-hero-photo::before` (linija ~367,
  `antas-design.css`) — ista specifičnost kao `.al-plates::before`, kasnije u fajlu, pobeđuje bez
  `!important`. Mobilni `@media(max-width:767px)` blok nije diran (samo prepisuje `background`,
  nasleđuje ispravne `opacity`/`transform` sa baznog pravila). Verifikovano na 3 stranice (početna,
  industrijski-podovi, antistatik-i-elektroprovodljivi-podovi) — svuda `opacity:1, transform:none`.
  Screenshot na `/industrijski-podovi/`: pun pravougaoni gradijent bez koso odsečenih uglova, dekorativne
  `.al-plates` ploče i dalje vidljive nedirnuto (odvojen `::after`).

## Verifikacija

- `/katalog/` — HTTP 200, 1×H1, desktop: sidenav + grid uporedo (screenshot), product loop vidljiv
  ispod (paginacija 1–9)
- `/planer-terena/` — HTTP 200, 1×H1, 3x3 grid kompaktan bez skrola, Košarka i dalje ispravno skalirana
- Regresija: `/` (homepage) i `/industrijski-podovi/` — HTTP 200, 1×H1 (brz spot-check, ne pun sweep)

## Otvorene akcije
- [x] CSS izmena `al-court-builder.css` (`.al-cb__svg` width→max-width) #claude-code
- [x] CSS izmena `antas-design.css` (`.al-katalog-cats` + mobile-hide pravila uklonjeni) #claude-code
- [x] Novi `post_content` za post 16736 (sidenav + grid) #claude-code
- [x] Vizuelna verifikacija sve tri stavke u browseru #claude-code
- [x] Mobile hero zatamnjenje — pravi uzrok (`::before` kolizija sa `.al-plates`) nađen i ispravljen,
      verifikovano na 3 stranice #claude-code — **svih 5 stavki iz obe sesije (20.08+21.08) sada zatvoreno**

## Veze
- [[dnevnik/2026-08-20-tri-baga-dijagnoza-katalog-hero-court]] — puna dijagnoza i plan
- [[dnevnik/2026-08-20-batch-izmene-6-oblasti]] — sesija koja je uvela sve tri regresije
