# 2026-08-19 [claude-code] — Regression sweep pre freeze-a + reverifikacija 301 mape

**Workstream:** W3 3.10 (pre-migration checklist §A)
**Status:** ✅ zatvoreno, bez nalaza koji traže popravku

## Šta je urađeno

### 1. Pun regression sweep (`migracija/alati/regression-sweep.php`)

Pušten **19.08, dan pre content freeze-a**, svesno ranije nego što obaveza traži.
Razlog: baseline od 13.08 prestao je da važi prvom sadržajnom izmenom 18.08, a sweep
je referenca za §B6 (post-migraciona provera protiv produkcije). Da je pušten tek
20.08 uveče, na popravku bilo kog nalaza ostalo bi manje od jednog dana pre gate-a
(21.08). Sutra se ponavlja kao brza potvrda protiv ovog baseline-a.

| Provera | 13.08 | **19.08** |
|---|---|---|
| Stranica (sitemap) | 239 | **235** |
| non-200 | 0 | **0** |
| bez H1 | 0 | **0** |
| 2×H1 | 0 | **0** |
| nevalidan JSON-LD | 0 | **0** |
| slomljene slike | 0 / 1.158 | **0 / 1.174** |
| interni 404 | 0 / 1.801 | **0 / 1.799** |
| bez metadesc | 31 | **18** |

`no_meta` = 18 su isključivo `product_tag` arhive (`oznaka-proizvoda/*`) — poznata
stavka, ide posle live-a. Preostalih 13 iz baseline-a (`category/*`,
`kategorija-proizvoda/*`, `brend/bergo`) zatvoreno je 13.08 **posle** generisanja
baseline CSV-a, pa se u diff-u prikazuju kao „promena od 19.08".

Jedini unos u `bad_links`: `http://localhost/antasline` (bez završne kose crte) →
301 na verziju sa crtom, pojavljuje se na 129 stranica. Identičan nalaz stoji i u
baseline-u 13.08 (124 stranice) → normalizacija početne stranice, ne regresija.

### 2. Diff protiv baseline-a 13.08 — 30 URL promena, sve objašnjene

| Promena | Broj | Uzrok |
|---|---|---|
| + ergonomski proizvodi | 9 | rad 14.08 (posle baseline-a) |
| + `/ergonomske-podloge/` + Woo kategorija | 2 | slug swap 13.08 (`-2` → čist slug) |
| + 2 ESD proizvoda | 2 | ESD klaster 18.08 |
| + `/sta-postaviti-preko-starog-parketa-ili-plocica/` | 1 | slug swap 13.08 |
| − 5 cenovnih stranica | 5 | konsolidacija u hubove 18.08 |
| − 6 proizvoda bez fotki | 6 | draft 17.08 (M odluka) |
| − FAQ / maloprodaja / bergo-easy | 4 | konsolidacija 13.08 |
| − 2 stara `-2` sluga | 2 | slug swap 13.08 |

**18 stranica sa izmenjenim title/metadesc:** 13 je popunjavanje praznog metadesc-a
na arhivama (rad 13.08), 5 su namerne izmene — `/dimenzije-fudbalskog-terena/` i
`/dimenzije-kosarkaskog-terena/` (stavka F, 18.08), `/industrijski-podovi/` (18.08),
`/podovi-za-magacine-i-hale/` (19.08, „skladišta"), `/iznajmljivanje-podova/`
(preuzeo title/meta od bergo-easy, 13.08).

**Nula neplaniranih izmena na buildu.**

### 3. Provera četiri nestala URL-a koja nisu bila objašnjiva iz PROGRESS-a

`industrijski-podovi-najcesca-pitanja` (17025) · `podovi-za-maloprodajne-objekte`
(16683) · `izbor-industrijskog-poda-tri-najcesca-pitanja` (2622) · `bergo-easy`
(16665). Provereno pojedinačno u bazi i protiv 301 drafta: **sva četiri su `draft`
i sva četiri imaju 301 pravilo.**

Uzrok „iznenađenja": konsolidacione skripte 13.08 radile su **posle** generisanja
baseline CSV-a (15:47–18:35 vs snimak 17:24), pa deo tog rada figurira kao promena
nastala 19.08. Nije kvar, ali je razlog zašto diff ima više redova nego što
PROGRESS sugeriše.

### 4. Reverifikacija 301 mape (`migracija/alati/redirect-verify.php`)

- **80 pravila** u `migracija/htaccess-301-DRAFT.txt` (79 → 80 od 18.08)
- **43 jedinstvena cilja — svi 200**
- 0 duplikata izvora · 0 petlji/lanaca · **0 kolizija sa živim stranicama**
- Prefiks-kolizije (15) su poznat i benigni nalaz — draft koristi
  `RedirectMatch ^…$`, ne `Redirect`, pa se ne „gutaju"

🔵 Upozorenje za **16613** iz provere 13.08 („publish + noindex, a pravilo ga šalje
na `-2`") **više se ne javlja** — stranica je u međuvremenu draftovana i slug swap
je izveden, pa je stanje ispravnije nego tada. Nije izgubljeno pravilo: linija 34
drafta i dalje šalje `/…-plocica-2/` → `/…-plocica/`.

🔵 Uslovni izuzetak od 18.08 proveren direktno u bazi: **16875 (`podovi-za-garaze`)
je i dalje `draft`**, pa 301 pravilo za taj URL (182 GSC pogotka) ispravno stoji.
Isto važi za 16874 i 17273.

## Otvorene akcije

- 🔴 **Posle freeze-a 20.08:** ponovni (brz) sweep kao potvrda + **nov backup
  zamrznutog builda na 2 lokacije** — jedina gate stavka koja se ne može zatvoriti
  pre freeze-a. Ako se do tada ne promeni nijedan slug, `redirect-verify` /
  `htaccess-301-generate` ne treba ponavljati.
- ⚪ 18 `product_tag` arhiva bez metadesc — svesno posle live-a.

## Beleške / odluke

- **Nov baseline za §B6 je `analiza/2026-08-19-regression-pre-freeze-*`**, ne snimak
  od 13.08. Na dan migracije `$BASE` → `https://www.antasline.com` i poredi se sa
  ovim fajlovima.
- 🔵 **Gotcha (alat, ne projekat):** `cd` u Bash alatu **traje kroz pozive**. Posle
  `cd migracija/alati` sledeći relativni `grep` je tiho promašio
  (`htaccess-301-DRAFT.txt` „ne postoji", iako fajl postoji). Koristiti apsolutne
  putanje ili vraćati `cd` u istoj komandi.
- Sweep traje ~50 min end-to-end na 235 stranica / 1.174 slike / 1.799 linkova;
  Faza 3 ne ispisuje ništa između poslednjeg `...1600` i sažetka, pa tišina od
  nekoliko minuta na kraju nije znak da je pukao.

## Veze

- [[migracija/2026-08-10-pre-migration-checklist]] §A
- [[dnevnik/2026-08-13-regression-sweep-post-faza2]] (prethodni baseline)
- [[dnevnik/2026-08-18-konsolidacija-cenovnih-stranica]] (uslovni izuzetak 301)
- [[dnevnik/2026-08-19-skladista-16687]] (izmena koja je oborila baseline)
- [[PROGRESS]]
