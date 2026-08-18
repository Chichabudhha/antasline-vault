# Chrome / web platforma — šta je novo i šta od toga koristimo

> Izvor: `developer.chrome.com/new` + release notes 148–151 + DevTools 151.
> Stanje: **2026-08-12**, instaliran Chrome **151.0.7922.110**.
> Ovo je filter, ne prepričavanje: svaka stavka nosi presudu **koristi / čekaj /
> ignoriši** za AntasLine.

## 0. Politika podrške (važi za sve ispod)

**Baseline Widely available** — B2B publika na srpskom tržištu, znatan udeo
starijih desktop browsera. Feature iz Chrome 148–151 je **Chrome-only** i sme
u produkciju samo kao *progressive enhancement*: sajt mora izgledati i raditi
ispravno bez njega, u `@supports` / feature-detection grani.

Isto zapisano u skillu **/modern-web-guidance** (lokalne napomene). Pre bilo
kog novog CSS/JS obrasca: `search` u tom skillu, pa provera protiv
**/woodmart-theme** §7 (specificity vs `base.css`) i §2 (`wpautop`).

---

## 1. ✅ KORISTI — direktna primena, sa fallback-om

| Feature | Chrome | Gde kod nas |
|---|---|---|
| **`shape-outside: path()` / `shape()`** | 149 | Dijagonalni rezovi sekcija (woodmart §7) — tekst koji prati kosinu umesto pravougaonog bloka. Bez podrške: tekst ostaje pravougaon, ništa se ne lomi |
| **`polygon()` corner rounding** | 150 | Isti dijagonalni/isečeni blokovi — zaobljeni uglovi bez SVG maske |
| **`background-clip: border-area`** | 150 | Gradijentni okviri kartica (Ecotile/sport kategorije) bez pseudo-element hakova |
| **`flex-wrap: balance`** | 150 | Nabrajanja/badge redovi koji ostavljaju siroče u drugom redu |
| **`text-fit`** | 150 | Kratki hero naslovi koji moraju da popune širinu kolone; **ne** za duge srpske naslove — proveri prelom |
| **Focusgroup** | 150 | Tabovi na proizvod stranici i akordeoni (FAQ) — strelice za navigaciju bez custom JS-a. A11y dobitak, degradira na Tab |
| **`aria-actions`** | 151 | Sekundarna dugmad u kartici proizvoda (uporedi/lista želja) — ekspozicija za čitače ekrana |
| **`image-rendering: crisp-edges`** | 149 | Tehnički crteži / šeme slaganja ploča gde interpolacija muti linije (sada nearest-neighbor, isto kao Firefox/Safari) |
| **`loading="lazy"` na `<video>`/`<audio>`** | 148 | Ako se ikad ubaci pravi `<video>` tag; YouTube fasada (woodmart §12) i dalje ostaje bolja za embed |
| **`@supports at-rule()` i `named-feature()`** | 148/150 | Čista feature-detection grana za sve gore — piši je odmah, ne naknadno |
| **Comma-separated container queries** | 150 | `@container` sa OR logikom — manje duplirane CSS-a u WPBakery grid karticama |
| **Container queries po imenu bez `container-type`** | 148 | Isto |

🔴 Za **svaki** red iznad: kod ide u child temu (`antas-design.css`), nikad u
parent WoodMart. Specificity protivnik je (0,2,0), ne (0,1,0).

---

## 2. 📊 MERENJE — utiče na W3 (CWV) i W5 (tracking)

- **`soft-navigation` + `interaction-contentful-paint` performance entry-ji**
  (151) — SPA/AJAX navigacije sada imaju svoj LCP-ekvivalent. Kod nas: layered
  nav filteri na kategorijama (woodmart §8) menjaju sadržaj bez pune
  navigacije; do sada je to bila crna rupa u merenju.
- **Soft FCP markeri u Performance panelu** (DevTools 151) — vizuelni par gore
  navedenom, uz postojeće Soft LCP markere.
- **`contentType` u Resource Timing** (148) — brza revizija da li se slike
  zaista isporučuju kao WebP (`image/webp`), bez ručnog gledanja Network taba
  red po red.
- **Container Timing API** (148, origin trial) — meri kada konkretna sekcija
  DOM-a završi prvi paint. Za dijagnostiku hero/LCP kandidata lokalno; OT =
  ne oslanjati se u produkciji.
- **Speculative load measurement / `performance.getSpeculations()`** (150, OT)
  — meri efikasnost prefetch/prerender-a. Bitno tek ako uvedemo speculation
  rules (v. §3).
- **Lighthouse 13.4.0** je u DevTools 151 — uz svaki novi baseline u
  `[[dnevnik/PERFORMANCE-AUDIT]]` upiši verziju; skorovi preko major/minor
  promena nisu uporedivi sa julskim snimcima.

---

## 3. ⚠️ OPREZ — dodiruje konverzije, ne uvoditi bez analize

**Speculation Rules `form_submission`** (Chrome 151) — prerenderuje odredišnu
stranicu na submit forme. Zvuči idealno za CF7 → `/hvala-za-poruku/`.

🔴 **Ali: naša jedina prava konverzija je _page view_ na `/hvala-za-poruku/`**
([[CLAUDE]] §4). Prerenderovana stranica se izvršava pre nego što je korisnik
stvarno stigne — GTM tag koji ne poštuje `document.prerendering` /
`prerenderingchange` može da okine `generate_lead` na posetu koja se nikad ne
dogodi. Rezultat bi bio naduvan broj lidova, tj. tačno ono što je BLOK A
čišćenje ispravljalo.

**Ako se ikad uvodi**: prvo GTM trigger uslovljen na `document.prerendering ===
false`, pa tek onda speculation rules. Ne obrnuto. Do tada — ne dirati.

Isto važi za bilo koji prefetch/prerender koji bi neko uključio kroz
LiteSpeed/optimizacioni plugin na produkciji — proveriti pre migracije 25.08.

✅ **Provereno 2026-08-13 [cpanel-live], live produkcija — BEZBEDNO.** LiteSpeed
Cache 7.8.1 ima `util-instant_click=1` (Instant Click) aktivan, `instant_click.min.js`
se stvarno učitava na live stranicama (potvrđeno curl-om). Pregledan izvorni kod
skripte: podržava native Speculation Rules API (`HTMLScriptElement.supports("speculationrules")`)
i grana na `"prerender"` SAMO ako `document.body.dataset.instantSpecrules === "prerender"`
— taj atribut **ne postoji nigde** (ni u temi, ni u LiteSpeed config-u, plugin UI
uopšte ne izlaže tu opciju u 7.8.1). Default grana je `_speculationRulesType =
"prefetch"`, ne `"prerender"` — **prefetch preko Speculation Rules API-ja dovlači
HTML u pozadini ali NE izvršava JS te stranice**, pa GTM/`generate_lead` na
`/hvala-za-poruku/` ne može lažno da okine na hover/mousedown. Rizik iz ovog
odeljka zatvoren za trenutnu LiteSpeed konfiguraciju. **Ako se ikad ručno doda
`data-instant-specrules="prerender"` (ili LiteSpeed izloži tu opciju u budućem
UI-ju) — ponovo primeniti pravilo iznad (GTM trigger na `document.prerendering
=== false` PRE uključivanja).** Usput nalaz bez rizika: DNS Prefetch Control
(`optm-dns_prefetch_ctrl=1`, auto) emituje samo 1 `dns-prefetch` tag na live
(`fonts.googleapis.com`, WP core default) — `googletagmanager.com` nema hint jer
GTM snippet ubacuje domen kroz inline JS, ne kroz `<script src=...>` u sirovom
HTML-u, pa ga LiteSpeed-ov statički skener ne vidi (očekivano, nije bag).
Detalji: [[DNEVNIK-NAPRETKA]] 2026-08-13.

---

## 4. ⏸️ ČEKAJ BASELINE — zanimljivo, ne sada

- **CSS gap decorations** (149) — linije/razdelnici u grid/flex razmacima
  (`column-rule-inset`). Zameniće hrpu border hakova u tabelama specifikacija,
  ali Chrome-only.
- **Animatable `zoom`** (150), **`image()` funkcija** (150), **`light-dark()`
  za slike** (150), **`ruby-overhang`** (151) — nema trenutnu primenu.
- **`revert-rule`** (148) — korisno baš za specificity ratove sa `base.css`,
  ali premlado.
- **CSS `url()` request modifiers** — `integrity()`, `cross-origin()`,
  `referrer-policy()` (150). Vredi zapamtiti za SRI na eksternim fontovima ako
  ih ikad vratimo (sada su self-hosted).
- **Declarative CSS module scripts** (148, OT), **HTML-in-canvas** (148, OT),
  **`<usermedia>`** (151), **out-of-order streaming `<template for>`** (150).

---

## 5. ❌ IGNORIŠI — nema veze sa ovim projektom

WebGPU, WebUSB/Web Serial, WebRTC (SCTP, diagnostic logging), WebAuthn/
passkeys i Email Verification Protocol (nemamo korisničke naloge), PWA origin
migracija, Payment Request / Secure Payment Confirmation (nemamo online
plaćanje), Prompt API i on-device AI (nije za produkcioni sajt), WebMCP /
agentic forms, Gamepad, DeviceOrientation.

---

## 6. Deprecations — provereno na našem build-u

| Promena | Chrome | Status kod nas |
|---|---|---|
| `new FontFaceSet()` baca `TypeError` | 151 | ✅ Čisto — grep po `wp-content/themes/**/*.js` nema pojavljivanja (provereno 2026-08-12) |
| `direct-sockets-private` → `local-network` / `loopback-network` permission policy | 151 | Nije primenjivo (ne koristimo) |
| Kraj podrške za macOS 12 | 151 | Nije primenjivo |
| Uklonjeno `border-color: gray` iz UA stila za tabele | 149 | ⚠️ Tabele specifikacija proizvoda koje su se oslanjale na UA default sada nemaju ivicu — proveriti pri sledećem prolazu kroz proizvod stranice |
| SVG filteri na iframe/plugin sadržaju onemogućeni | 150 | Nije primenjivo |
| WebSocket više ne blokira bfcache | 149 | Neutralno-pozitivno (brži back/forward) |

---

## 7. Prompt API / Gemini Nano u browseru — analizirano, NE koristimo

Povod: [CyberAgent case study](https://developer.chrome.com/blog/prompt-api-blog-cyberagent)
— Chrome ekstenzija koja blogerima na Ameba platformi predlaže naslove, nastavak
pasusa i popravke teksta, sve lokalno preko Gemini Nano, bez servera i bez cene
po pozivu.

**Presuda: ne uvodimo — ni na sajt, ni kao interni alat.** Četiri nezavisna
razloga, svaki dovoljan sam za sebe:

| Prepreka | Detalj |
|---|---|
| 🔴 **Jezik** | Podržani: EN, JA, ES, DE, FR. **Srpskog nema.** Ceo naš sadržaj i publika su na srpskom |
| 🔴 **Platforma** | Samo desktop Chrome (Win 10/11, macOS 13+, Linux, Chromebook Plus). **Nema Android ni iOS.** Naša publika je mobilna — ~46 od 50 klikova na telefon dolazi sa mobilnog ([[CLAUDE]] §10) |
| 🔴 **Hardver** | >4 GB VRAM · 16 GB RAM · 4 jezgra · 22 GB slobodnog prostora (model se sam briše ispod 10 GB). Provereno 2026-08-12: radna mašina ima **2 GB VRAM, 15,7 GB RAM, 25,1 GB slobodno** — ne prolazi prag, ne može ni da se testira |
| 🔴 **Politika podrške** | Nije Baseline; feature koji radi za par procenata posetilaca traži server fallback — a ako svakako gradiš server put, on pokriva 100% i lokalni model ne donosi ništa |

Sam CyberAgent u članku navodi da Nano daje **nekonzistentan izlaz na isti
prompt** (ume da vrati Markdown umesto JSON-a), da context window traži pažljivo
merenje i da manji model traži više konteksta u promptu nego serverski —
odnosno, i tamo gde su svi uslovi ispunjeni, to je posao održavanja, ne besplatan
dobitak.

**Šta radimo umesto toga:** ono što već radimo — generisanje/doterivanje
sadržaja ide kroz Claude Code (tekst, SEO) i Gemini API (`/gemini-vizuali`,
slike/video). Oba rade na srpskom, bez hardverskog praga, i ne zavise od toga
šta posetilac ima u browseru.

**Kad ponovo otvoriti pitanje:** kad Chrome doda srpski na listu podržanih
jezika I kad API izađe na Android. Do tada — nema šta da se testira.

## Veze

- Skill **/modern-web-guidance** — pretraga ~140 vodiča sa Baseline podacima
  (`npx -y modern-web-guidance@latest search "<upit>"`, **PowerShell**, ne Bash)
- Skill **/woodmart-theme** §13 (DevTools 151 alati) i §14 (kad zvati guidance)
- `[[dnevnik/PERFORMANCE-AUDIT]]` — CWV baseline
- `[[CLAUDE]]` §4 (konverzije — bitno za §3 gore), §7.6 (CWV status)
