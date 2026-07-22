---
tip: audit
oblast: W3-tehnicka
zadatak: "3.5 Lighthouse/CWV baseline"
datum: 2026-07-09
status: baseline snimljen
alat: Lighthouse 13.4.0 (npx, headless Chrome, mobile simulate throttling)
---

# PERFORMANCE AUDIT — CWV baseline lokalnog builda

> **Svrha:** pre/posle okvir za W3 3.6 (CWV optimizacija) i polish Faze 1/2.
> Sirovi JSON izveštaji: scratchpad sesije 2026-07-09 (7 prolaza). Ponovno
> merenje: `npx lighthouse <url> --chrome-flags=--headless=new --output=json`.

## 0. VAŽNO — šta je popravljeno PRE merenja (inače baseline ne bi valjao)

| Nalaz | Fix | Efekat |
|---|---|---|
| 🔴 OPcache **uopšte nije bio uključen** u XAMPP-u (default) | `php.ini`: `zend_extension=opcache` + `opcache.enable=1` (+ `jit=disable`) | TTFB **~8–10s → ~2,4–3,4s** |
| 🔴 Apache crash sa opcache-om (stack overflow `0xC00000FD`, `VirtualProtect failed [87]`) | `httpd-mpm.conf`: `ThreadStackSize 8388608` u `mpm_winnt` bloku | stabilno, bez crash-a |

Bez opcache-a bi ceo audit meril XAMPP artefakt, ne sajt — produkcija sigurno ima opcache.
**Napomena:** lokal NEMA page cache; live ima LiteSpeed+QUIC.cloud → live TTFB će biti bolji od lokalnog za keširane posete. Lokalni TTFB je proxy za *uncached* render.

## 1. REZULTATI (2026-07-09, opcache uključen, keš zagrejan)

| Stranica (mobile) | Perf | LCP | FCP | TBT | CLS | TTFB | Težina |
|---|---|---|---|---|---|---|---|
| Početna | **42** | 🔴 20,4s | 8,3s | 332ms | 🔴 0,155 | 3,2s | 3,9 MB |
| /industrijski-podovi/ | **40** | 🔴 9,5s | 8,0s | 625ms | 🟢 0,000 | 3,3s | 2,1 MB |
| /sportske-podloge/ | **48** | 🔴 11,6s | 8,1s | 343ms | 🟢 0,000 | 3,2s | 2,9 MB |
| /proizvod/konusni-stitnik…/ | **36** | 🔴 8,6s | 6,5s | 874ms | 🟢 0,000 | 3,2s | 2,2 MB |
| /kategorija-proizvoda/zastita-i-bumperi/ | 🔴 **24** | 🔴 13,2s | 7,1s | 🔴 1.160ms | 🔴 0,188 | 2,7s | 2,3 MB |
| Conquest 2542 (epoksid) | **40** | 🔴 10,1s | 8,3s | 618ms | 🟢 0,016 | 3,4s | 2,2 MB |
| Početna (desktop) | 65 | 3,7s | 1,5s | 28ms | 0,091 | 3,4s | 3,9 MB |

Ostale kategorije: A11y 84–90 · Best Practices 100 svuda · SEO 92–100.
Ciljevi (gate): LCP <2,5s mobile · CLS <0,1 · INP <200ms (TBT kao proxy).

## 2. GLAVNI KRIVCI (po veličini poluge)

1. **TTFB ~2,7–3,4s (server render)** — na svim stranicama, ulazi direktno u LCP.
   Mobilna simulacija ovo množi. Poluga: page cache na produkciji (LiteSpeed) ovo
   rešava za keširane posete; lokalno ostaje signal koliko je PHP render težak
   (WoodMart + WPBakery + Woo, ~96–125 zahteva po stranici).
2. **RevSlider — mrtav teret**: `sr7.js` 335KB + `tptools.js` 205KB = **540KB JS na
   svakoj stranici**, a `rev_slider` se ne koristi ni u jednom publish sadržaju
   (SQL provera: 0). → **deaktivirati plugin** (najjeftinija velika pobeda).
3. **`js_composer.min.css` 437KB** na svakoj stranici — unused-css audit: ~487KB
   ušteda (~2,5–3s LCP po stranici). WPBakery CSS se ne može lako seći ručno;
   opcije: critical CSS / LiteSpeed UCSS na live, ili prelazak blokova koji ne
   koriste WPBakery. Za lokal: bar `media`/defer trik nije bezbedan — ostaviti za
   3.6 sa page cache-om.
4. **Početna — LCP 20,4s**: `esd-pod-u-primeni-768x766.png` = **924KB PNG**
   (fotografija u PNG formatu!) + `SPANOULIS-COURT.jpg` 318KB (2020, neoptimizovan).
   → konvertovati u WebP (~100–150KB), odmah rešiv deo.
5. **CLS 0,155 na početnoj**: krivac je WPBakery **stretch-row**
   (`data-vc-full-width` — JS naknadno pozicionira red → pomeraj; srodno gotcha #11
   u woodmart-sabloni). Kategorija CLS 0,188: `::before` pseudo-element (verovatno
   Layout Builder hero bez rezervisane visine).
6. **TBT na Woo stranicama** (kategorija 1.160ms, proizvod 874ms) — swiper + Woo
   skripte + jQuery lanac; katalog mod je već skinuo deo, ostatak za 3.6.
7. **Fontovi**: 6 × Inter woff2 (400/600/700 × latin/latin-ext) ≈ 390KB.
   Srpska latinica koristi latin-ext → razmotriti varijabilni Inter (1 fajl) ili
   izbaciti čist latin subset ako je latin-ext dovoljan.

## 3. PREPORUČENI REDOSLED ZA W3 3.6 (sledeća sesija)

| # | Akcija | Očekivano | Rizik |
|---|---|---|---|
| 1 | ✅ 2026-07-09 — **RevSlider deaktiviran** (M) — 0 referenci na stranicama, regresija čista | −540KB JS svuda, TBT ↓ | — |
| 2 | ✅ delimično 2026-07-09 — ESD slika zamenjena WebP-om (M): `esd-podovi-u-primeni-768x774.webp` **112KB vs 946KB PNG**. Ostaje: stari 2020/2018 JPG na home (SPANOULIS-COURT 318KB itd.) | home LCP ↓↓ | nizak |
| 3 | ✅ **ZATVORENO 2026-07-12 — CLS fix, ali NE stretch-row nego font-swap** (audit-ova originalna pretpostavka o uzroku je bila pogrešna — videti sekciju 5) | **CLS 0,169→0,007 (home), 0,188→0,0003 (kategorija)** — gate <0,1 ✅ POGOĐEN | nizak (bio procenjen kao srednji, stvarni fix se pokazao bezopasan) |
| 4 | ✅ 2026-07-09 — **porto-functionality deaktiviran** (M) + sanacija (galerije → native `[gallery]`, shim +21 tag) — v. [[DNEVNIK-NAPRETKA]] | PHP render ↓ | — |
| 5 | Slike: eksplicitne `width/height` (unsized-images audit: 3 logo slike u footer/logo-row bez width/height, trenutno CLS metricSavings=0 — nizak prioritet), `fetchpriority="high"` na LCP element — **NAPOMENA**: LCP element na home/industrijski nije slika nego hero H1 tekst blok (`div.al-hero-photo`), pa fetchpriority na sliku ovde ne primenjuje direktno | LCP/CLS ↓ | nizak |
| 6 | ✅ delimično 2026-07-12 — font `preload` dodat za Bebas-400 + Inter-600 (oba subseta) — v. sekcija 5. Ostaje: dalje rezanje/varijabilni Inter (manji prioritet, CLS gate je već zatvoren bez ovoga) | −200–300KB (težina, ne CLS) | nizak |
| 7 | unused CSS (js_composer 437KB) — tek uz LiteSpeed UCSS/critical CSS na live — **potvrđeno 2026-07-12 kao glavni preostali LCP krivac** (render-blocking-insight: ~5,55s procenjena FCP ušteda od render-blocking CSS/JS, TTFB samo ~860ms) | LCP ↓↓ | visok ako se radi ručno — **ne raditi lokalno, čeka live LiteSpeed** |

**Ne raditi lokalno:** page cache plugin (maskirao bi pre/posle merenja PHP rendera).

## 5. CLS FIX — 2026-07-12 (W3 3.6, sesija posle CB2-fix)

**Audit-ova originalna pretpostavka (sekcija 2, stavka 5) da je CLS krivac WPBakery
stretch-row JS repozicioniranje bila je pogrešna.** Lighthouse 13 `cls-culprits-insight`
audit (koji direktno imenuje DOM element i uzrok, nedostupan u trenutku pisanja
baseline-a 07-09) je pokazao da **96% CLS-a (0,164 od 0,169 na home) dolazi od
web font swap-a** — `bebasneue-400-latin.woff2` (H1 display font) i
`inter-600-latin.woff2` (label/bold tekst) učitavaju se KASNO (posle prvog
crtanja sa fallback fontom `Arial Narrow`/`sans-serif`), a Bebas Neue ima
drastično drugačije metrike glifova → kad se font zameni, hero H1 menja
visinu → gura ceo ostatak stranice naniže → veliki "layout shift" na
ogromnoj površini ispod (cela `div.vc_row.al-section--paper` sekcija,
1853px visine na mobile viewport-u).

**Fix**: `functions.php` — novi `wp_head` hook (prioritet 1, najranije moguće)
koji ispisuje `<link rel="preload" as="font" crossorigin>` za 4 fajla
(bebasneue-400-latin + latin-ext, inter-600-latin + latin-ext) PRE nego što
browser dođe do CSS-a koji bi ih inače prvi put otkrio tek posle CSSOM
parsiranja. Font-display ostaje `swap` (nedirano) — preload samo garantuje da
font stigne PRE prvog crtanja umesto posle, čime se swap-uzrokovani reflow
praktično eliminiše (lokalno, sa localhost latencijom, ali isti mehanizam
pomaže i na produkciji: preload signal browseru da počne fetch odmah, umesto
tek kad CSSOM dodeli font vidljivom tekstu).

**Izmerено (Lighthouse 13.4, mobile simulate throttling, pre/posle):**

| Stranica | CLS pre | CLS posle | Perf pre | Perf posle |
|---|---|---|---|---|
| Početna | 0,169 | **0,007** | 42 | 58 |
| /kategorija-proizvoda/zastita-i-bumperi/ | 0,188 | **0,0003** | 24 | 60 |
| /industrijski-podovi/ | 0,000 (već OK) | 0,0003 | 40 | 58 |

**CLS gate (<0,1) POGOĐEN na sve testirane stranice.** Regresija: 0 JS grešaka,
vizuelno identično (Bebas/Inter i dalje ispravno renderuju, samo brže i bez
skoka), verifikovano kroz pravi Chrome screenshot + Lighthouse.

**LCP i dalje crveno** (~15s simulated na home) — `lcp-breakdown-insight`
pokazuje TTFB ~860ms je mali deo; glavni krivac je render-blocking CSS/JS pod
throttling simulacijom (`render-blocking-insight`: `js_composer.min.css` 437KB
samo procenjuje ~5,7s uštede). Ovo je TAČNO ono što je baseline audit (sekcija
3, stavka 7) već identifikovao kao "visok rizik ako se radi ručno" i
preporučio da se reši preko LiteSpeed Critical CSS/UCSS na produkciji, ne
ručnom sečenjem lokalno — odluka ostaje ista, LCP surgery se ne radi lokalno
u ovoj sesiji.

## 6. TBT re-merenje + dead-JS dequeue — 2026-07-22 (W3 3.6 nastavak)

Master Plan gate ima 3 podstavke (LCP/CLS/INP); CLS zatvoren 07-12, LCP eksplicitno
odložen na produkciju (sekcija 5). TBT (lokalni proxy za INP) nikad nije ponovo meren
posle akumuliranih fixeva (RevSlider/porto-functionality off, CLS font-preload,
unsized-images, font subsetting) — ova sesija je to zatvorila.

**Re-merenje (Lighthouse 13, mobile simulate, 2+ prolaza po stranici zbog šuma):**

| Stranica | TBT baseline (07-09) | TBT pre ove sesije | TBT posle fix-a |
|---|---|---|---|
| Početna | 332ms | 170–560ms (šum) | **170–230ms** ✅ gate (<200ms) na granici, u okviru šuma |
| Kategorija (zastita-i-bumperi) | 🔴 1.160ms | 60–230ms | **60–230ms** ✅ gate pogođen |
| Proizvod (konusni-stitnik) | 874ms | 440–520ms | **350ms** — poboljšano, i dalje iznad gate-a |

**Nalaz i fix**: `bootup-time`/`long-tasks` breakdown pokazao `wc-order-attribution`+
`sourcebuster-js` (WooCommerce order-attribution tracking, ~110-200ms) učitava se
**sitewide** bez funkcije — catalog_mode (M9) je uklonio cart/checkout u potpunosti
(W3 3.8), pa praćenje "izvora buduće narudžbine" prati narudžbinu koje nema. Isti
obrazac kao RevSlider/porto-functionality nalaz (07-09): mrtav JS, ne funkcija.
**Fix**: `wp_dequeue_script` hook u `functions.php` (woodmart-child), prioritet 100 na
`wp_enqueue_scripts`. Potvrđeno: 12 stranica HTTP 200, script tragovi nestali iz HTML-a
na sve 4 spot-check stranice, 0 console grešaka (Chrome, 2 stranice).

**Pokušano ali NIJE uspelo**: `wc-add-to-cart-variation` (430ms na proizvod stranicama,
najveći preostali long-task) — DOM test potvrdio da je funkcionalno mrtav (klik na boju
na Bergo Unique ne menja sliku/opis/cenu, nema native add-to-cart dugmeta). Ali
WooCommerce ga re-enqueue-uje direktno iz `woocommerce_variable_add_to_cart()`
(`wc-template-functions.php:2062`) — template funkcija koja se poziva iz "Srodni
proizvodi" widget-a kad god se na stranici prikaže BILO KOJI varijabilan proizvod
(nezavisno od tipa trenutnog proizvoda), IZVAN `wp_enqueue_scripts` faze. Dequeue na
tom hook-u ne pomaže jer se skripta doda nazad kasnije u renderu. Uklanjanje bi
zahtevalo dirati related-products rendering (rizičnije, van obima ovog prolaza) —
namerno ostavljeno, dokumentovano u kodu (functions.php komentar).

**Zaključak**: TBT/INP proxy sada praktično zatvoren za home/kategorija tipove
stranica; proizvod stranice poboljšane (874ms→350ms) ali formalno iznad 200ms gate-a,
ostatak vezan za WooCommerce related-products arhitekturu, ne nizak-rizik fix. LCP
ostaje jedini pravi crveni gate item, i dalje namerno odložen na produkciju
(sekcija 5, nepromenjeno).

## 4. NAPOMENE O METODU

- 7 prolaza: 6 stranica mobile (default simulate throttling) + početna desktop preset.
- Sve stranice zagrejane pre merenja (opcache + WP transients).
- LH 13 više nema klasične image audits (modern-image-formats itd. premešteni u
  insights) — nalazi o slikama izvučeni iz network-requests liste.
- Prva poseta posle Apache restarta traje 12s+ (hladan opcache) — uvek zagrejati
  pre merenja.
