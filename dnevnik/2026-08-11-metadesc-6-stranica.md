---
datum: 2026-08-11
tag: claude-code
oblast: W2 / SEO
status: zatvoreno
---

# 2026-08-11 — 6 stranica bez meta opisa: napisani i objavljeni

## Šta je urađeno

Zatvoren bloker od 2026-08-10 (rok: content freeze 16.08). Šest objavljenih stranica
renderovalo je **nula** `<meta name="description">` — `rank_math_description` postmeta
uopšte nije postojao, a Rank Math za njih nema fallback.

| ID | URL | Dužina | Iz čega je izveden opis |
|---|---|---|---|
| 16550 | `/` (početna) | 157 | hero sekcija: Ecotile 25 god. garancije · FIBA standardi · „ugradnja bez zatvaranja pogona" |
| 5455 | `/vestacka-trava/` | 153 | modeli Nature / Highlands / Springgrass + „nije joj potrebno zalivanje i šišanje, preko zime je zelena" |
| 5119 | `/vestacka-trava-za-fudbal/` | 156 | „radi se isključivo po projektu, po dimenzijama terena" · linije bela/crvena/plava, lepljene odvojeno · FIFA/ITF/IRB |
| 15480 | `/sportske-podloge/bergo-ultimate/` | 157 | FIBA i ITF sertifikati · preko 10 boja i tri modela · 15 godina garancije |
| 21 | `/aktuelnosti/` | 154 | sadržaj stranice je **prazan** (blog arhiva) → opis izveden iz stvarnih naslova postova u arhivi (dimenzije terena, ESD/antistatik montaže, izvedeni projekti) |
| 16612 | `/ftalati-stetnost-i-uticaj-na-ljudsko-zdravlje/` | 151 | postojeći `.al-geo-intro` pasus: omekšivači u PVC-u, endokrini/disajni sistem, zabranjene grupe u EU i Srbiji |

Pravilo koje je držano: **ništa izmišljeno** — svaka tvrdnja u opisu postoji u tekstu te
stranice. Nijedna cena nije pomenuta (izbegnuto vezivanje meta opisa za brojke koje se menjaju).

Sve dužine 151–157 znakova (ispod ~160 praga skraćivanja u SERP-u).

### Odbačeno: prepis live meta opisa
`live-inventar-2026-07-05.csv` ima metaopis za live `/vestacka-trava/`, ali **live i lokalna
stranica pod tim slugom nisu ista stranica** — live `/vestacka-trava/` ima title
„Veštačka trava za fudbalske terene i ostale sportove", lokalna je „Veštačka trava za terase
i bašte" (sport je izdvojen na `/vestacka-trava-za-fudbal/`, koje na live-u ne postoji).
Kopiranje bi dalo opis koji ne opisuje stranicu. Za ostalih 5 live nema opis (početna ima,
ali za stari sadržaj — lokalna početna je pun rebuild).

## Otvorene akcije

*(nema — jedina otvorena stavka razrešena istog dana, v. ispod)*

## Dodatak: 4 `noindex` stranice — provereno i zatvoreno istog dana

Nalaz je prvo delovao kao propust, pa je M rekao „nije namerno, stavi index yes".
Provera pre izvršenja pokazala je suprotno i odluka je promenjena — **ostaju noindex**.

**Svaka od 4 ima noviji, već indeksiran parnjak iz WoodMart rebuild-a:**

| `noindex` (stari build) | Indeksiran parnjak |
|---|---|
| 5512 `/podovi-za-poslovni-prostor/` | 16669 `/kancelarije-i-poslovni-prostori/` · 16667 `/lvt-podovi-za-komercijalne-i-javne-prostore/` · 16686 `/vinil-podovi-za-restorane-hotele-kafice…/` |
| 5754 `/izgradnja-terena-za-tenis/` | 17028 `/sportski-podovi-za-teniske-terene/` · 16688 `/dimenzije-teniskog-terena/` · 2699 `/podloga-za-teniske-terene/` |
| 5769 `/podne-obloge-za-promocije-i-sajmove/` | 16665 `/bergo-easy/` („podloge za manifestacije, sajmove i promocije") |
| 16171 `/galerija-sportskih-terena/` | 16674 `/galerija/` („Galerija - sportski tereni") |

Potvrde da je noindex bio nameran: ID obrazac (5xxx = stari lokalni build,
166xx/170xx = rebuild koji ih je zamenio), **nijedna ne postoji na live-u**
(nema ih u `live-inventar-2026-07-05.csv`), i 16171 nema ni `<h1>` — samo tri
galerije bez teksta.

Uključivanje indeksa bi 5 dana pre content freeze-a stvorilo 4 duplikat-para
prema stranicama koje su namerno građene — direktno protiv anti-kanibalizacione
provere iz W2 pravila.

🔵 **Opciono posle live-a (ne blokira migraciju):** 301 sa te 4 na parnjake —
čistiji signal od tihog noindex-a, ali bez hitnosti jer te URL-ove niko ne
linkuje niti postoje na live-u.

**Pouka:** „stranica nema meta opis i van je sitemap-a" nije po sebi bag —
može biti tačan opis namerno penzionisane stranice. Pre popravke proveriti ima
li stranica novijeg parnjaka (isti klaster, viši ID).

## Beleške / odluke

🔴 **Nova gotcha — UTF-8 ne sme kroz `mysql -e "..."` na ovom Windows shell-u.**
Dva opisa upisana tako stigla su u bazu sa `?` umesto `ć/š/ž`. Podmuklo je što
`SELECT` u istoj konzoli to **ne otkriva** — i ispis prolazi kroz isti codepage, pa `?`
izgleda kao kozmetika prikaza, ne kao stvarno oštećenje podatka. Otkriveno tek `curl`-om
nad `<head>`. Ispravan put: `.sql` fajl sa `SET NAMES utf8mb4;` → `mysql < fajl.sql`
(ista 4 opisa pisana iz fajla bila su ispravna iz prve). Ide u
[[reference/naucene-lekcije]].

⚠️ **Sitemap-based regression sweep ima slepu tačku po definiciji.** Bloker je nabrajao 6
stranica jer `regression-sweep.php` ide kroz sitemap; stranice sa `noindex` nikad ne prođu
kroz njega. Bez opisa je zapravo 11 objavljenih stranica (6 + 4 noindex + `/katalog/`
koji ima Rank Math fallback). Za pre-migracioni audit vredi bar jednom porediti
**sitemap skup vs. `post_status='publish'` skup iz baze**.

⚙️ Apache nije radio na početku sesije (`curl` exit 7, MySQL je radio) — pokrenut ručno
preko `httpd.exe`. Uzrok nije istraživan.

`wp-load.php` bootstrap PHP skripta je pukla na „kritična greška" i visila preko 2 min
(isti obrazac kao poznati `js_composer` fatal u CLI kontekstu) — sadržaj stranica je na
kraju čitan direktnim SQL upitom, što je za ovaj zadatak bilo dovoljno.

## Verifikacija

6/6 HTTP 200 · tačno 1×H1 · tačan opis u `<head>` sa ispravnom dijakritikom ·
regresija čista na `/industrijski-podovi/` i `/kontakt/` (opisi nepromenjeni).

**Backup:** `antasline-backups/antasline_local_2026-08-11_pre-metadesc-6-stranica.sql`

## Veze

- [[PROGRESS]] · [[DNEVNIK-NAPRETKA]] · [[2026-07-06-MASTER-PLAN-V2]]
- [[reference/naucene-lekcije]] (nova gotcha: `mysql -e` i UTF-8)
- [[dnevnik/2026-08-10-w3-310-full-regression]] (odakle je bloker došao)
