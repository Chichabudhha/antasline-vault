---
tip: referenca
azurirano: 2026-08-05
namena: Red čekanja za Gemini foto rad (prioritet + status) — održava Claude Code
---

# 📋 Gemini red čekanja — foto rad

> Popunjeno 2026-08-05: audit svih 94 objavljenih proizvoda (DB upit +
> `getimagesize()` na svaku `_thumbnail_id` datoteku) protiv standarda
> `reference/standard-slika-proizvoda.md` (1080×1080, kvadrat, bela
> pozadina). Rezultat: **9 OK_STANDARD** (ništa ne treba) · **72 NONSTANDARD**
> (postojeća prava fotografija proizvoda, samo van spec-a — kandidat za
> Gemini `--mode enhance`) · **13 NO_THUMBNAIL** (nema nijedne fotografije u
> arhivi uopšte — **NE ide na Gemini `--mode generate`**, v. napomena ispod).

## Pravilo — enhance da, generate od-nule ne (za specifične proizvode)

`--mode enhance` (postojeća prava fotografija → standardizovana kompozicija/
pozadina/rezolucija) je bezbedno: input je stvaran proizvod, model samo čisti
kadar. `--mode generate` (nema ulazne slike, čisto iz prompta) za **konkretan
brendiran/tehnički proizvod** bi rizikovao pogrešnu boju/dimenziju/spoj i
implicitno predstavljanje nečega što nismo fotografisali — isto obrazloženje
kao ranija W7 F2.9 politika ("prijaviti nedostatak, ne gurati sliku koja
implicira tuđi posao", v. [[PROGRESS]] Blokeri). Zato svih **13 NO_THUMBNAIL**
proizvoda ostaje van ovog reda, i dalje #ceka-miroslav (pravi izvor
fotografije), v. postojeći nalaz u [[PROGRESS]] Blokeri ("F2.9 rep").

## Kako se koristi

- Status kolona: `⏳ čeka` → `🔄 u radu` → `✅ gotovo`
- Redosled = prioritet (najvažnije na vrhu), ne hronologija
- Batch veličina po sesiji ograničena dnevnom Gemini kvotom (500/dan,
  deljeno preko svih projekata koji koriste isti ključ) — **427 preostalo**
  posle treće batch (61 slika, 2026-08-05, ukupno 73/500 danas). Ceo red
  čekanja (Tier 1-4, 96/97 stavki, sve osim #25 outofstock) je sada gotov
  u jednoj sesiji — 0 grešaka na 61 poziva.
- Prioritet po poslovnoj vrednosti (CLAUDE.md §1 šta se prodaje + GA4
  publike §5): **Tier 1 ESD/industrijski** (glavni B2B fokus) → **Tier 2
  sport/Bergo** (Sport & Court Planners publika) → **Tier 3 terase/parking/
  LVT** → **Tier 4 Ergomat industrijski pribor** (niša, niski individualni
  saobraćaj, ali kompletna linija uvezena odjednom)

## Tier 1 — ESD / industrijski (najviši prioritet) — ✅ ZATVOREN 2026-08-05 (8/8)

| # | ID | Proizvod | Trenutno | Status |
|---|---|---|---|---|
| 1 | 16538 | Ecotile E500/7 industrijska ploča | 700×700 | ✅ gotovo (attach 17522, 2026-08-05) |
| 2 | 16540 | Ecotile E500/10 Ultra Heavy Duty | 705×705 | ✅ gotovo (attach 17524, 2026-08-05 — napomena: blaži ugao snimka, više praznog prostora nego 15% standard, i dalje velika nadogradnja u odnosu na original) |
| 3 | 16542 | Ecotile 7mm ESD Floor Tiles | 700×700 | ✅ gotovo (attach 17525, 2026-08-05) |
| 4 | 16930 | Ecotile E500 T-Joint rampa | 700×700 | ✅ gotovo (attach 17527, 2026-08-05) |
| 5 | 16939 | Ecotile E500 T-Joint ugaona rampa | 700×700 | ✅ gotovo (attach 17528, 2026-08-05) |
| 6 | 16943 | Ecotile X500 X-Joint rampa | 700×700 | ✅ gotovo (attach 17529, 2026-08-05) |
| 7 | 16949 | Ecotile X500 X-Joint ugaona rampa | 700×700 | ✅ gotovo (attach 17530, 2026-08-05) |
| 8 | 16929 | SureGrip stepenišni protivklizni profil | 518×518 | ✅ gotovo (attach 17531, 2026-08-05) |

## Tier 2 — Sport / Bergo / košarkaške konstrukcije — ✅ ZATVOREN 2026-08-05 (16/17, 1 preskočen)

| # | ID | Proizvod | Trenutno | Status |
|---|---|---|---|---|
| 9 | 16770 | Bergo Ultimate | 960×718 | ✅ gotovo (attach 17523, 2026-08-05) |
| 10 | 16801 | Bergo Ultimate FLOW | 860×576 | ✅ gotovo (attach 17526, 2026-08-05) |
| 11 | 16534 | Bergo Unique | 304×304 (najlošija rezolucija u katalogu) | ✅ gotovo (attach 17532, 2026-08-05) |
| 12 | 16815 | Bergo XL | 576×359 | ✅ gotovo (attach 17533, 2026-08-05) |
| 13 | 16823 | Bergo Elite | 576×359 | ✅ gotovo (attach 17534, 2026-08-05) |
| 14 | 16843 | Bergo Solid | 768×725 | ✅ gotovo (attach 17535, 2026-08-05) |
| 15 | 16786 | Bergo Ultimate PLUS | 2560×1706 (visoka rez., pogrešan format) | ✅ gotovo (attach 17536, 2026-08-05) |
| 16 | 16830 | Bergo Nova | 2485×2560 (visoka rez., pogrešan format) | ✅ gotovo (attach 17537, 2026-08-05) |
| 17 | 16532 | Košarkaški koš „Street Sport" | 600×600 | ✅ gotovo (attach 17538, 2026-08-05) |
| 18 | 16544 | Lite Shot 325 konstrukcija | 600×600 | ✅ gotovo (attach 17539, 2026-08-05) |
| 19 | 16546 | Mini Shot 225 konstrukcija | 600×600 | ✅ gotovo (attach 17540, 2026-08-05) |
| 20 | 16548 | MicroShot 125 konstrukcija | 600×600 | ✅ gotovo (attach 17541, 2026-08-05) |
| 21 | 16973 | Goalrilla DC72E1 koš | 706×706 | ✅ gotovo (attach 17542, 2026-08-05) |
| 22 | 16984 | Goaliath GB60 koš | 832×832 | ✅ gotovo (attach 17543, 2026-08-05) |
| 23 | 16986 | Goaliath Gotek 54 koš | 858×858 | ✅ gotovo (attach 17544, 2026-08-05) |
| 24 | 16988 | Goalrilla LED rasveta za koš | 730×730 | ✅ gotovo (attach 17545, 2026-08-05) |
| 25 | 16536 | Zglobni obruč za koš | 600×600 | ⏳ **preskočen namerno** — outofstock, #ceka-miroslav da potvrdi da li je proizvod aktivan pre nego što se ulaže Gemini rad |
| 26 | 16999 | Golovi za rukomet i futsal | 1600×1200 | ✅ gotovo (attach 17546, 2026-08-05) |
| 27 | 17000 | Zaštitna mreža za terene | 960×1280 | ✅ gotovo (attach 17547, 2026-08-05) |

## Tier 3 — Terase / parking / LVT — ✅ ZATVOREN 2026-08-05 (17/17)

| # | ID | Proizvod | Trenutno | Status |
|---|---|---|---|---|
| 28 | 16907 | Geoplast Salvaverde Type A | 600×600 | ✅ gotovo (attach 17548, 2026-08-05) |
| 29 | 16908 | Geoplast Salvaverde Type B | 600×600 | ✅ gotovo (attach 17549, 2026-08-05) |
| 30 | 16909 | Geoplast Runfloor | 600×600 | ✅ gotovo (attach 17550, 2026-08-05) |
| 31 | 16910 | Geoplast Geograss | 600×600 | ✅ gotovo (attach 17551, 2026-08-05) |
| 32 | 16911 | Geoplast Geocross | 600×600 | ✅ gotovo (attach 17552, 2026-08-05) |
| 33 | 16912 | Geoplast Geogravel | 600×600 | ✅ gotovo (attach 17553, 2026-08-05) |
| 34 | 16913 | Geoplast Geoflor | 600×600 | ✅ gotovo (attach 17554, 2026-08-05) |
| 35 | 16914 | EXPONA Commercial | 576×359 | ✅ gotovo (attach 17555, 2026-08-05) |
| 36 | 16915 | EXPONA Flow | 576×359 | ✅ gotovo (attach 17556, 2026-08-05) |
| 37 | 16916 | EXPONA Simplay 19dB | 576×359 | ✅ gotovo (attach 17557, 2026-08-05) |
| 38 | 16917 | EXPONA Clic 19dB Wood | 576×359 | ✅ gotovo (attach 17558, 2026-08-05) |
| 39 | 16918 | EXPONA Design | 816×886 | ✅ gotovo (attach 17559, 2026-08-05) |
| 40 | 16920 | R-Tile Urban | 629×286 | ✅ gotovo (attach 17560, 2026-08-05) |
| 41 | 16921 | R-Tile Design | 731×1012 | ✅ gotovo (attach 17561, 2026-08-05) |
| 42 | 16877 | Condor Schools trava u boji | 576×359 | ✅ gotovo (attach 17562, 2026-08-05) |
| 43 | 16885 | Condor Playgrass | 576×359 | ✅ gotovo (attach 17563, 2026-08-05 — napomena: isti izvorni fajl kao #42, Gemini je i dalje generisao zasebnu varijantu) |
| 44 | 16894 | Radici ULTRAMIX EVO N.I. | 819×819 | ✅ gotovo (attach 17564, 2026-08-05) |

## Tier 4 — Ergomat industrijski pribor (bumperi, DuraStripe, senzori) — ✅ ZATVOREN 2026-08-05 (28/28)

| # | ID | Proizvod | Trenutno | Status |
|---|---|---|---|---|
| 45 | 16476 | Konusni štitnik za I-profil | 600×600 | ✅ gotovo (attach 17565, 2026-08-05) |
| 46 | 16478 | Polukružni zaštitnik za cevi | 600×600 | ✅ gotovo (attach 17566, 2026-08-05) |
| 47 | 16480 | Polukružni odbojnik HCIB120 | 600×600 | ✅ gotovo (attach 17567, 2026-08-05) |
| 48 | 16482 | Kvadratni odbojnik SCIB120 | 600×600 | ✅ gotovo (attach 17568, 2026-08-05) |
| 49 | 16484 | Konusni odbojnik CCIB120 | 600×600 | ✅ gotovo (attach 17569, 2026-08-05) |
| 50 | 16486 | Odbojnik za ivice ECB120 | 600×600 | ✅ gotovo (attach 17570, 2026-08-05) |
| 51 | 16488 | Konusni odbojnik CCP120 | 600×600 | ✅ gotovo (attach 17571, 2026-08-05) |
| 52 | 16490 | Okrugli odbojnik za uglove | 600×600 | ✅ gotovo (attach 17572, 2026-08-05) |
| 53 | 16492 | Kvadratni odbojnik SCBP120 | 600×600 | ✅ gotovo (attach 17573, 2026-08-05) |
| 54 | 16494 | Veliki kvadratni odbojnik | 600×600 | ✅ gotovo (attach 17574, 2026-08-05) |
| 55 | 16496 | Veliki zaobljeni odbojnik | 600×600 | ✅ gotovo (attach 17575, 2026-08-05) |
| 56 | 16498 | Ekstra veliki kvadratni odbojnik | 600×600 | ✅ gotovo (attach 17576, 2026-08-05) |
| 57 | 16500 | Konusni površinski odbojnik | 600×600 | ✅ gotovo (attach 17577, 2026-08-05) |
| 58 | 16502 | Pravougaoni površinski odbojnik | 600×600 | ✅ gotovo (attach 17578, 2026-08-05) |
| 59 | 16504 | Veliki pravougaoni površinski odbojnik | 600×600 | ✅ gotovo (attach 17579, 2026-08-05) |
| 60 | 16506 | Zaobljeni ivični odbojnik | 600×600 | ✅ gotovo (attach 17580, 2026-08-05) |
| 61 | 16508 | Okrugli površinski odbojnik | 600×600 | ✅ gotovo (attach 17581, 2026-08-05) |
| 62 | 16510 | Cart Stopper | 600×600 | ✅ gotovo (attach 17582, 2026-08-05) |
| 63 | 16512 | Ergo T-Slot Snap-In Bumper | 600×600 | ✅ gotovo (attach 17583, 2026-08-05) |
| 64 | 16514 | DuraStripe Edge Protector 25×122 | 600×600 | ✅ gotovo (attach 17584, 2026-08-05) |
| 65 | 16516 | DuraStripe Edge Protector 10×122 | 600×600 | ✅ gotovo (attach 17585, 2026-08-05) |
| 66 | 16518 | DuraStripe Xtreme Roll | 600×600 | ✅ gotovo (attach 17586, 2026-08-05) |
| 67 | 16520 | DuraStripe Supreme V | 600×600 | ✅ gotovo (attach 17587, 2026-08-05) |
| 68 | 16522 | DuraStripe Mean Lean Roll | 600×600 | ✅ gotovo (attach 17588, 2026-08-05) |
| 69 | 16524 | DuraStripe Cold Storage Roll | 600×600 | ✅ gotovo (attach 17589, 2026-08-05) |
| 70 | 16526 | Bežični LED signalni senzor | 800×800 | ✅ gotovo (attach 17590, 2026-08-05) |
| 71 | 16528 | IQSENSOR senzor pokreta | 466×466 | ✅ gotovo (attach 17591, 2026-08-05) |
| 72 | 16530 | Mosolut Heavy | 800×800 | ✅ gotovo (attach 17592, 2026-08-05) |

## Van reda — NO_THUMBNAIL (13, ne diraj sa Gemini, čeka pravi izvor)

16893 (Condor shock-pad) · 16899/16900/16901/16902/16906 (5× Radici bez
pouzdanog model-mapiranja) · 16919 (EXPONA Living Clic) · 16990/16991
(tribina/stolica) · 16998/17001/17002/17003 (generička sportska oprema).
Isti spisak i razlog kao [[PROGRESS]] Blokeri "F2.9 rep".

## Veze
- `.claude/skills/gemini-vizuali/SKILL.md`
- `reference/standard-slika-proizvoda.md`
- [[PROGRESS]] Blokeri (F2.9 rep — izvor za NO_THUMBNAIL spisak)
