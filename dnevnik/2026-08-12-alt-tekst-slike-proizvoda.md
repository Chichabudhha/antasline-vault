---
tip: dnevnik
datum: 2026-08-12
oznaka: "[claude-code]"
oblast: W1 / a11y / SEO slika
status: zatvoreno
---

# Alt tekst na slikama proizvoda (66 priloga) + Chrome 149 tabele

Treća i četvrta stavka istog dana. Sesija je počela kao quick-win (Chrome 149),
prerasla u W1 zadatak (alt tekst) pošto se ispostavilo da je W1 red čekanja prazan.

## Šta je urađeno

### 1. Chrome 149 `border-color` na tabelama — bloker zatvoren, build nije pogođen
Read-only. Mereno u Chrome 151 preko `getComputedStyle` nad svakim `table/th/td`,
sa filterom „boja ivice == `color`" (pala na `currentColor`): **0 pogodaka** na 4
reprezentativne stranice (bare `<table>` 16567 · legacy `id=tabele width=95%` 16612 ·
legacy `width="872"` 6824, 39 elemenata · `.al-table` spec tabela na proizvodu).

Dva nezavisna razloga: nijedna objavljena stranica ne koristi HTML atribut `border=`
(SQL nad `wpGs_posts`) — a to je jedini slučaj koji je zavisio od UA pravila; i svaka
ivica ima eksplicitnu boju u autorskom CSS-u (WoodMart `var(--brdcolor-gray-200/300)`,
`.al-table` `rgba(22,40,60,0.12)`).

### 2. W1 red čekanja — zatečeno prazno, ispravljena dva zastarela reda
Predložen je zadatak „Polish Faza 4 — GEO-intro na 22 posta", koji je **bio zatvoren
2026-08-07 (22/22)**. Master plan 1.2 je stajao na „12/33, sledeći kancelarije/padel"
dok je red čekanja **33/33 od 2026-07-08** — zastarelo mesec dana. Oba reda ispravljena,
Faza 4 verifikovana i na buildu (`.al-geo-intro` 1× na 3388/16616/6824/16612), ne samo
u papiru. Stanje W1: red A 33/33 · Polish Faze 1–4 ✅ · novi proizvodi S1–S8 8/8 ·
Court builder CB1–CB3 ✅ → **nema poznatog otvorenog posla**.

### 3. Alt tekst na slikama proizvoda — 66 priloga (glavni zadatak)
Red čekanja iz [[migracija/2026-07-30-lighthouse-a11y-plan]] („alt tekst — poseban
budući zadatak", van obima te ture). M odobrio da se uzme sada, pred content freeze.

**Obim je prvo izmeren, ne preuzet iz plana.** Brojka „67/81 proizvoda" iz 07-30 bila
je zastarela — u međuvremenu je obogaćivanje proizvoda popunilo najveći deo. Napisan
read-only audit koji gleda **kanale renderovanja**, ne medijateku:

| Kanal | Bez alta | Odluka |
|---|---|---|
| `_thumbnail_id` proizvoda | 6 | ✅ popunjeno |
| `_product_image_gallery` | 63 (66 uklj. deljene) | ✅ popunjeno |
| `<img>` u `post_content`, sa `wp-image-ID` | 0 | — |
| `<img>` u `post_content`, bez ID-a | 159 | 🟢 **namerno NIJE dirano** |

🟢 **159 „nedostajućih" alt-ova u sadržaju su dekorativne SVG ikonice** —
`montaza.svg` (28×), `odrzavanje.svg` (27×), `izdrzljivost.svg` (25×), `izgled.svg`,
`protivklizna.svg`, `fleksibilna.svg`, `sertifikat.svg`, `namena-*.svg`. Stoje uz
tekst koji ih već imenuje, pa je `alt=""` **ispravno po WCAG** (lekcija od 2026-08-05).
Popunjavanje bi bilo regresija pristupačnosti, ne popravka. Medijateka inače ima
**7.725 slika, 6.638 bez alta** — ogromna većina su Porto-era veličine i neupotrebljeni
prilozi; audit po kanalu renderovanja svodi posao sa 6.638 na 66.

**Izvor teksta — ništa izmišljeno, tri nivoa:**
- **override, vizuelno pregledano (10)** — slika otvorena Read alatom pre pisanja
  opisa, tvrdi se samo ono što se vidi: konj na perforiranoj podlozi ispred štale ·
  magacinski prolaz sa Ecotile pločama i pešačkim zonama · proizvodna hala sa
  viljuškarom · ESD radionica za montažu elektronike · vinarija · kancelarija ·
  kantina · EXPONA Flow sa tri žute vaze · 2 deljena priloga
- **oznaka dezena iz imena fajla (4)** — Eden Ash, Rice Wine Oak 9028, Treehouse Oak
  9036, Commercial 12523; imenuje se proizvođačka oznaka, ne opisuje se izgled
- **naslov proizvoda (29)** + **„— fotografija N" (23)** za dodatne fotke istog
  proizvoda (`…-2.webp`)

🔴 **Deljeni prilozi:** jedan prilog = jedan alt, bez obzira na to u koliko galerija
stoji. `12503` stoji u **3** galerije (16520/16522/16524), `16861` u **2**
(16514/16516) — oba su dobila **neutralan, ne-proizvodni** opis. Skripta ima tvrdu
proveru: puca i ne upisuje ništa ako neki deljeni prilog nije pokriven override mapom.

**Verifikovano posle upisa:** audit ponovljen → thumb **0**, galerija **0** (159
ikonica netaknuto). 6 proizvod stranica (Mosolut/Ecotile 500/10/Radici pejzaž/EXPONA
Flow/EXPONA Clic/Hoopair D60): **HTTP 200 · 1×H1 · 0 slika iz `uploads` sa `alt=""`**,
nov tekst se renderuje. Regresija: home, `/industrijski-podovi/`, `/katalog/`,
`/kategorija-proizvoda/industrijski-podovi/` sve 200/1×H1; JSON-LD na proizvodu čist
(1× Product, 1× BreadcrumbList, 1× Organization, bez dupliranja).

## Otvorene akcije

- Nijedna iz ove sesije. Alt tekst kanal „proizvodi" je zatvoren.
- 🔵 Van obima (kandidat posle live-a): `heading-order` + `target-size` na product
  karticama — zadire u WoodMart core layout, veći zahvat od ostatka a11y ture.

## Beleške i odluke

- **Obim se meri, ne prepisuje iz plana.** „67/81" je bilo tačno 30.07 i netačno 12.08.
  Isti obrazac kao „12/33" u master planu — svaka brojka u planu je snimak, ne stanje.
- **Audit po kanalu renderovanja, ne po medijateci** — razlika 66 vs 6.638.
- **Prazan alt ume da bude tačan odgovor.** 159 od 225 nalaza su bila ispravna stanja.
- Skripta: `migracija/alati/job-alt-tekst-galerije.php` (proba/`apply`, ne dira
  postojeći alt). Prethodnik `job-alt-tekst-proizvodi.php` (samo thumbnail, alt=naslov)
  ostaje — ovaj ga proširuje na galerije i uvodi vizuelno proverene opise.
- Backup: `antasline-backups/antasline_local_2026-08-12_pre-alt-tekst-galerije.sql`

## Veze

- [[migracija/2026-07-30-lighthouse-a11y-plan]] — red čekanja iz kog je zadatak došao
- [[reference/naucene-lekcije]] — 3 nove lekcije (hladan start XAMPP-a, deprecation
  provera `getComputedStyle`-om, „Sledeće" liste trule tiše od „Urađeno")
- [[migracija/w1-polish-red-cekanja]] · [[migracija/w1-red-cekanja]] — potvrđeno zatvoreni
- [[PROGRESS]] · [[DNEVNIK-NAPRETKA]]
