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
| 3 | CLS fix: stretch-row na početnoj (position/width bez JS init-a) + hero visina na kategorijama | CLS <0,1 | srednji (vizuelna regresija) |
| 4 | ✅ 2026-07-09 — **porto-functionality deaktiviran** (M) + sanacija (galerije → native `[gallery]`, shim +21 tag) — v. [[DNEVNIK-NAPRETKA]] | PHP render ↓ | — |
| 5 | Slike: eksplicitne `width/height`, `fetchpriority="high"` na LCP sliku, lazy ispod folda | LCP/CLS ↓ | nizak |
| 6 | Fontovi: varijabilni Inter ili rezanje subsetova + `preload` glavnog | −200–300KB | nizak |
| 7 | unused CSS (js_composer 437KB) — tek uz LiteSpeed UCSS/critical CSS na live | LCP ↓↓ | visok ako se radi ručno |

**Ne raditi lokalno:** page cache plugin (maskirao bi pre/posle merenja PHP rendera).

## 4. NAPOMENE O METODU

- 7 prolaza: 6 stranica mobile (default simulate throttling) + početna desktop preset.
- Sve stranice zagrejane pre merenja (opcache + WP transients).
- LH 13 više nema klasične image audits (modern-image-formats itd. premešteni u
  insights) — nalazi o slikama izvučeni iz network-requests liste.
- Prva poseta posle Apache restarta traje 12s+ (hladan opcache) — uvek zagrejati
  pre merenja.
