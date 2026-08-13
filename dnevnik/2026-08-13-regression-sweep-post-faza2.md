---
tip: dnevnik
alat: claude-code
datum: 2026-08-13
blok: W3
zadatak: "3.10"
status: zavrseno
---

# Sesija — Pun regression sweep posle FAZE 2 (239 stranica) + reverifikacija 301 mape

> Četvrta stavka dana (posle FAZE 2 layout/UI i dve `[cpanel-live]` provere).
> **Read-only prema sajtu i bazi** — nijedna izmena na buildu, nema SQL upisa,
> nema backup fajla. Dirani samo vault dokumenti + `odluke/_pregled-odluka`.

## Zašto sada

Jutrošnja FAZA 2 ([[dnevnik/2026-08-13-faza2-layout-ui-fixes]]) je popravljala u
**dizajn sistemu**, ne po stranici: CF7 part na ~55 stranica, ritam sekcija na 14
stranica + Woo kategorije, `al-display--lg` na 17 mesta. Verifikovano je bilo
**32 URL-a**. Pun sweep (`migracija/alati/regression-sweep.php`) nije puštan od
**2026-08-10**, a od tada su prošle i FAZA 1 (vizuali/mediji, isti dan) i ceo
12.08 (alt tekst, brend arhive, ikonice kartica). Poslednji prozor je pre
content freeze-a **16.08**.

## Rezultat sweep-a (239 stranica, 1.158 slika, 1.801 link)

| Provera | Rezultat |
|---|---|
| HTTP status ≠ 200 | **0** |
| Stranica bez H1 / sa 2×H1 | **0 / 0** |
| Nevalidan JSON-LD | **0** |
| Slomljene slike | **0** od 1.158 |
| Problematični interni linkovi | **1** (artefakt, v. ispod) |
| Bez `<title>` | **0** |
| Bez meta description | **31** (svi su taksonomijske arhive — nov nalaz, v. §3) |

Jedini „problematičan" link je `http://localhost/antasline` → 301 na verziju sa
kosom crtom (124 pojave) — **artefakt lokala**: home URL builda je putanja u
poddirektorijumu, na produkciji je koren domena i redirekcije nema.

Artefakti (nov baseline za poređenje posle migracije):
`analiza/2026-08-13-regression-post-faza2-pages.csv` · `…-assets.json` ·
`…-summary.json`.

## Poređenje sa baseline-om 10.08 — 0 regresija, 3 objašnjena pomeranja

Skripta je **nepromenjena** od 10.08 (`ls`+`git log` provereni), pa je poređenje
validno. Na **194 zajednička URL-a**: `code` 0 razlika · `h1` 0 · `jsonld_bad` 0 ·
`title` 0. FAZA 2 nije polomila ništa.

### 1. −118 slika po stranici na SVAKOJ stranici — nije regresija
Ukupno slika po stranicama: **26.626 → 4.397**. Delta je **tačno 118 na svakoj
stranici** (i na blogu, i na proizvodima, i na silo stranicama), a **jedinstvenih**
slika je skoro isto (**1.182 → 1.158**) — dakle nestao je jedan globalni blok koji
se ponavljao svuda, a ne same slike.

Uzrok nađen: **ikonice mega menija su uklonjene 12.08** — postoji backup
`antasline-backups/antasline_local_2026-08-12_pre-uklanjanje-meni-ikonica.sql`,
59 linkova × 2 renderovanja menija (desktop + mobilni) = 118. Provereno u bazi:
`meni-ikonice/*.svg` postoji kao **79 priloga** i `uploads/meni-ikonice/` ima 79
fajlova, ali ih **nijedna `nav_menu_item` stavka više ne referencira** (0 pogodaka
u `post_content` i u `postmeta` menija). Poklapa se sa #ceka-miroslav stavkom iz
06.08 („ikonice za meni Miroslav sam bira").

🔴 **Neevidentirano**: ni [[DNEVNIK-NAPRETKA]] ni [[PROGRESS]] nemaju unos o ovom
uklanjanju — jedini trag je ime backup fajla. Isto važi za draftovanje 5455
(v. §2) i još 3 backup-a od 12.08 (`_pre-faza1-visual`, `_pre-bergo-varijacije`,
`_pre-15480-rebuild`, `_pre-stari-format-stranice`).

Usput isti uzrok objašnjava i **−2 interna linka** po stranici i **`imgs_noalt`
23.010 → 0** (ikonice su bile jedini `<img>` bez `alt` atributa u zaglavlju).

### 2. Sitemap 195 → 239 URL-ova (+45, −1)
Rast je posledica ranijeg rada, ne današnjeg: `category`/`product_cat`/
`product_tag` sitemap-i uključeni 11.08, `product_brand` 12.08, plus dva nova
proizvoda iz FAZE 1 (`isotrack-l` 17836, `isotrack-x` 17837) i `brend/bergo`
(termin kreiran u FAZI 1, 11 proizvoda).

**Nestao 1 URL:** `/vestacka-trava/` (5455) — prebačen u **draft** 12.08
(konsolidacija duplikata, kanonska je 16673
`/spoljnje-podne-obloge/vestacka-trava-za-terase/`). 🟢 Provereno da je pokriveno:
linija 98 u `migracija/htaccess-301-DRAFT.txt` nosi baš to pravilo. Na live-u
`/vestacka-trava/` i dalje vraća **200**, dakle 301 je neophodan — i postoji.

### 3. Bez meta description: 6 → 31 (nov nalaz, nije regresija)
Svih 31 su **taksonomijske arhive koje su tek 11–12.08 ušle u sitemap**:

| Grupa | Broj | Napomena |
|---|---|---|
| `product_tag` (`/oznaka-proizvoda/*`) | 18 | poznata stavka — prored arhiva je već zakazan **posle live-a** (checklist §B7) |
| `category` (blog, `/category/*`) | 6 | indeksabilne, u sitemap-u, bez opisa |
| `product_cat` | 6 | ostalih 10 kategorija **ima** opis |
| `product_brand` | 1 | `brend/bergo` (nov termin iz FAZE 1; Ecotile i Ergomat imaju opis) |

Nije bag i ne blokira migraciju (Google generiše opis sam), ali je **jedini
preostali sadržajni posao koji staje pre freeze-a** i tiče se 13 stranica koje
nisu tagovi (6 blog kategorija + 6 `product_cat` + `brend/bergo`).
#ceka-miroslav / kandidat za sledeću sesiju.

## Reverifikacija 301 mape (`redirect-verify.php`)

Pokrenuta jer je posle 11.08 (kad je draft regenerisan) menjan status stranica —
checklist §A izričito traži ponovnu proveru u tom slučaju.

- **Ciljevi:** 45 jedinstvenih, **0 problematičnih** (svi 200 na buildu) — draftovanje
  5455 nije obesmislilo nijedan cilj.
- **Duplikati izvora:** 0 · **petlje/lanci:** 0.
- **Prefiks-kolizije:** 15 — poznato i nebitno, draft koristi sidreni
  `RedirectMatch "^/put/?$"`, ne prefiks-match `Redirect` (odluka 11.08).
- 🟡 **1 upozorenje:** `/sta-postaviti-preko-starog-parketa-ili-plocica/` (16613)
  vraća 200 na buildu, a pravilo bi ga preusmerilo na `-2` verziju (6588, 84 kl.).
  **Namerno** — to je konsolidacija duplikata od 30.07, 16613 je `noindex` i nije u
  sitemap-u (zato ga sweep i ne vidi). Razlika u odnosu na 5455: 5455 je draftovan,
  16613 je ostavljen kao publish+noindex. Nije greška, ali je **nedosledno** —
  vredi ujednačiti posle live-a.

**Zaključak: `.htaccess` draft ostaje važeći, ne treba ga regenerisati.**

## Otvorene akcije
- [ ] Meta description za 13 arhiva (6 blog kategorija + 6 `product_cat` + `brend/bergo`) — pre freeze-a 16.08 ako se odobri #ceka-miroslav
- [ ] Ujednačiti 16613 sa 5455 (draft vs publish+noindex) — **posle live-a**, ne pred gate #claude-code
- [x] Pun sweep posle FAZE 2 #claude-code
- [x] Reverifikacija 301 mape posle promene statusa stranica #claude-code
- [x] Odluka 4.8 (Maximize Conversions) zatvorena kao „odloženo" u [[odluke/_pregled-odluka]] #claude-code

## Beleške / odluke
- **Quick-win izvršen:** odluka **4.8** upisana u [[odluke/_pregled-odluka]] —
  odloženo do posle live-a, sa razlogom (17 od 26 „plaćenih konverzija" su `tel`
  klikovi → pravih lidova 9) i sa 4 preduslova za ponovno otvaranje.
- Sweep i verify su **read-only** (GET/HEAD). Trajanje sweep-a >10 min — pušten u
  pozadini; `| tail` bafer sakrije napredak do kraja, sledeći put pisati direktno u fajl.
- Nov baseline za post-migracionu proveru je **2026-08-13**, ne 2026-08-10 — posle
  24.08 se `$BASE` menja na `https://www.antasline.com` i poredi sa ovim fajlovima.

## Veze
- [[DNEVNIK-NAPRETKA]] 2026-08-13
- [[dnevnik/2026-08-13-faza2-layout-ui-fixes]]
- [[migracija/2026-08-10-pre-migration-checklist]] §A
- [[dnevnik/2026-08-11-htaccess-301-reverifikacija]]
- [[2026-07-06-MASTER-PLAN-V2]] W3 3.10
