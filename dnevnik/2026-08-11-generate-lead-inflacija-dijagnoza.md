---
tip: dnevnik
alat: claude-code
datum: 2026-08-11
blok: "-"
status: zavrseno
---

# Sesija — Dijagnoza inflacije `generate_lead` na `/hvala-za-poruku/`

> Zatvara 🔴 bloker otvoren istog dana ujutru („merenje pravih konverzija naduvano ~3×,
> uzrok nije dijagnostikovan, dijagnoza pre freeze-a 16.08"). Read-only sesija —
> ništa nije menjano ni na buildu, ni u bazi, ni u GTM-u, ni na live-u.

## Metod

Namerno bez GTM Preview-a (traži M-a i pušta hitove u produkciju). Umesto toga:

1. **Čitanje objavljenog kontejnera** — `gtm.js?id=GTM-TRDT8K9` je javan URL; preuzet
   i parsiran (`parse_gtm.py`) u `macros`/`predicates`/`tags`/`rules`. Nula poslatih hitova.
2. **Merenje stvarnih hitova** — `analytics.google.com/g/collect` zahtevi u pregledaču,
   grupisani po `en=` (ime eventa) i `_s=` (redni broj hita u sesiji).
3. **Kontrolna grupa** — isto merenje na lokalnom WoodMart buildu, koji nosi doslovnu
   kopiju live GTM+consent koda, pa razlika izoluje ono što je specifično za live.
4. **GA4 potvrda** — `ga4_hvala_diag.py` (live-only, `hostName` filter): eventi na hvala
   stranici po danu, po uređaju, po pretraživaču, po `streamId`, tačne putanje.

## Šta je urađeno

- Potvrđeno da `generate_lead` okida **isključivo** na `/hvala-za-poruku/` (0 pojava
  igde drugde), sa jednog jedinog `streamId` — dakle nije reč o drugom stream-u ni o
  zaostalom pravilu sa `/kontakt/`.
- Iz kontejnera izvučeno pravilo koje pokriva hvala stranicu:
  `IF [event=gtm.js AND putanja sadrži /hvala-za-poruku]` → okida **četiri** taga:
  `generate_lead` (id 17) · `page_view` (id 18) · Ads konverzija `__awct` (id 20) ·
  `fbq('track','Lead')` (id 38). Nezavisno od toga, Google tag `G-H8BRCZN8W4` (id 11,
  okidač `gtm.init`) šalje **svoj automatski `page_view`** na svakoj stranici.
- Izmereno jedno učitavanje na oba okruženja:

 | | `page_view` | `generate_lead` | GTM embeda u HTML-u |
 |---|---|---|---|
 | Live (Kallyas) | 2× | **3×** | **2** + noscript |
 | Lokalni build (WoodMart) | 2× | **1×** | 1 + noscript |

  Live merenje se poklapa sa GA4 agregatom za 04–10.08 (26 `page_view` / 39
  `generate_lead` na 10 sesija).

### Nalaz A — suvišan `page_view` tag (id 18) · **preživljava migraciju**

Redosled hitova pri jednom učitavanju, identičan na live-u i na lokalu:
`_s=1 page_view` (Google tag, automatski) → `_s=2 generate_lead` → `_s=3 page_view`
(tag 18, suvišan).

Posledica: **hvala-proxy je tačno 2× stvaran broj dolazaka.** Time je objašnjen i
obrazac primećen ujutru — svi dnevni brojevi pregleda su parni.

### Nalaz B — trostruki `generate_lead` · **NE prenosi se migracijom**

Live Kallyas stranica nosi dva odvojena GTM embeda istog kontejnera — jedan iz teme
(`data-cfasync="false"`, `//www.googletagmanager.com/gtm.js`), drugi kroz
`litespeed/javascript` — pa `dataLayer` sadrži `gtm.js` dvaput. Lokalni WoodMart build
ima jedan embed i daje `generate_lead` 1×.

## Otvorene akcije

- [ ] **Obrisati GTM tag id 18** (`page_view` na hvala pravilu) — jedina potrebna izmena,
      ostavlja čisto 1 `page_view` + 1 `generate_lead` po dolasku. Preporuka: **na dan
      migracije**, u isti paket sa Enhanced Conversions i Meta Pixel čišćenjem (uredniji
      rez nego da nedelja pred freeze bude polovično merena). #ceka-miroslav
- [ ] Izmeriti da li se **Ads konverzija (`__awct`) i `fbq Lead`** isto multipliciraju na
      live-u — isto pravilo ih okida, ali Ads deduplicira konverzije po kliku, pa efekat
      nije nužno isti. Ne tvrditi bez merenja. #claude-code

## Beleške / odluke

- 🔴 **Za prvi post-live izveštaj:** posle 24.08 obe brojke padaju same od sebe —
  `generate_lead` na ~⅓, hvala-proxy na ~½ (ukupno ~⅙ ako se nalaz A popravi).
  **To nije pad konverzija.** Baseline „~55/mes" i gate KPI su mereni naduvanom serijom.
- **Metodološka zamka:** naknadna reprodukcija drugog embeda ne radi — ni ubacivanje
  drugog `gtm.js` skript-taga posle učitavanja, ni drugi `gtm.js` push u `dataLayer` ne
  okidaju ništa (GTM čuva `google_tag_manager[id]` i ne inicijalizuje se dvaput). Mora
  biti u početnom HTML-u, zato je live merenje bilo neophodno.
- Live je učitan u pregledaču 2×, što dodaje jednu sesiju u GA4 statistiku za 11.08.
  Ništa nije kliknuto niti poslato.
- Skripte su ad-hoc (scratchpad), namerno **nisu** upisane u konektor: `ga4_hvala_diag.py`,
  `parse_gtm.py`.

## Veze

- [[reference/naucene-lekcije]] — obe lekcije upisane (nalaz A i B)
- [[PROGRESS]] — bloker prebačen 🔴 → 🟡 (ostaje samo M odluka)
- [[DNEVNIK-NAPRETKA]] — ledger unos
- [[migracija/2026-08-09-enhanced-conversions-4.7]] — GTM paket za dan migracije
- [[dnevnik/2026-08-11-w5-nedeljni-izvestaj]] — izveštaj koji je otvorio nalaz
