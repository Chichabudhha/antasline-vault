---
tip: dnevnik
alat: claude-code
datum: 2026-08-12
blok: C
status: zavrseno
---

# Sesija — Vizuali referenci i ikonice kartica (homepage, O nama, padel, maloprodaja)

> Peta sesija istog dana. Ad-hoc polish po nalazima Miroslava dok gleda build —
> nije stavka iz reda čekanja, W1 ostaje zaključen. Sve na lokalnom buildu.

## Šta je urađeno

### 1. Homepage (16550) — sekcija „Veruju nam"
HTEC, Quectel i Dunk Shop stajali su kao gola tekstualna traka (`.al-ref-row`) ispod
tri foto-kartice. Pretvoreni u pune kartice → sekcija sada ima **6 referenci u dva reda**.

| Referenca | Slika | Odakle |
|---|---|---|
| HTEC — ESD pod, Niš | `2026/07/ugradnja-esd-poda-htec-nis.webp` | već u uploads |
| Quectel — ESD pod, Beograd | `2026/07/ecotile-esd-quectel-beograd.webp` | već u uploads |
| Dunk Shop — 3×3 teren | `2026/08/teren-3x3-dunk-shop.jpeg` | **falila lokalno**, povučena sa live-a |

🔴 **Dunk Shop fotka nije postojala na lokalu.** Ni u `uploads`, ni u DB (0 pogodaka za
`dunk` u `post_content`/`post_title`/`guid`/`postmeta`/`options`), ni u foto-arhivi
`C:\Miroslav\Antas line\`. Nađena je **u starijem vault SQL backup-u** (`_pre-expona-living-clic.sql`)
kao serijalizovana putanja ka live URL-u → `https://www.antasline.com/wp-content/uploads/2026/07/teren-dunk-shop.jpeg`.
Skinuta i uvezena u medijateku kao **ID 17808** sa alt tekstom.

### 2. O nama (571) — reference po delatnostima
Tri `.al-ref-row` liste (Industrija 10, Sport 6, Ugostiteljstvo 4 imena) zamenjene
**foto-karticama sa naših izvedenih radova** — 11 kartica ukupno.

- **Industrija (5):** HTEC · Apple servis Macola · AIK Bačka Topola · Orion telekom · Farmalogist
- **Sport (4):** Spanoulis Court · Maxima kamp Pecarski · Dunk Shop · Luštica Bay
- **Ugostiteljstvo (2):** Metropolis Caffe · Cafe Arabika Kruševac

Logo traka (Bosch, Vinča, Adient, Philip Morris, AMSS) **premeštena** iz uvoda u sekciju
Industrija, pod „Još industrijskih klijenata:". **Orion izvađen iz logo trake** jer je
dobio foto-karticu — inače bi se dvaput pojavio u istoj sekciji.

Dve fotke uvezene iz arhive (`slike 12-22/bergo baste/`): **17810** Metropolis, **17811** Arabika.

### 3. `/vestacka-trava-za-fudbal/` i `/zastitne-podloge-za-travu-i-plocnike/` — ⛔ PREKINUTO
M je prijavio da su „u starom formatu". **Nije reprodukovano** — obe stranice koriste
aktuelan `al-*` dizajn sistem (hero, `al-section`, `al-label`, `al-display`, dijagonale,
CTA) i imaju **identične `body` klase** kao rebuild-ovane stranice. Postavljeno pitanje
šta konkretno štrči → **M odgovorio „Prekini"**. Ništa nije menjano na te dve stranice.

Nalazi ostaju zapisani za kasnije:
- **15793** — jedini legacy markup u celom buildu: `<div id="colorBlock" class="productColors-block">`
  sa `.color-square` (Porto/Kallyas klase, ne postoje u WoodMart-u → swatch „Silk Black"
  se renderuje kao prazan prostor). Uz to: spec kao običan `<ul>` umesto `al-table`,
  **dve galerije** na istoj stranici (2022 JPG + 2026 WebP), jedan `<h2>` bez `al-label`,
  inline-stilizovan `<img style="width:100%">`, **nula `al-card`** (proizvodi opisani inline).
- **5119** — nijedan legacy marker; jedino galerija je gola `<img>` mreža bez lightbox-a
  iako tekst kaže „Kliknite na sliku za uvećan prikaz".
- **Audit svih 53 objavljenih stranica:** `productColors-block`/`color-square` postoji
  **samo na 15793**. Ostale zastavice su lažni pozitivi (`porto` se poklapa unutar reči
  „s**porto**va") ili legitiman sadržaj (`<table>` u tehničkim specifikacijama).

### 4. `/sportske-podloge/padel-tereni/` (16670) — ikonice kartica
Blok od 4 kartice nije imao **nijednu** ikonicu. Dve su postojale u setu, dve nacrtane:

| Kartica | Ikonica |
|---|---|
| Brzina igre | 🆕 `brzina.svg` — lopta + tri linije brzine |
| Kontrolisano odbijanje | 🆕 `odbijanje.svg` — putanja lopte koja pada, udara podlogu i odskače |
| Malo održavanja | `odrzavanje.svg` (postojeća) |
| Zaštita zglobova | `ergonomija.svg` (postojeća) |

### 5. `/podovi-za-radnje-i-maloprodajne-objekte/` (16142) — ikonice + proizvodi
Od 7 kartica, 4 su imale ikonice; 3 u sekciji „Tehnička specifikacija i primena" nisu:

| Kartica | Ikonica |
|---|---|
| Primena | `fleksibilna.svg` (postojeća) |
| Bez pripreme podloge | 🆕 `bez-pripreme.svg` — ploča preko neravne podloge |
| Toplotna i vatrootpornost | 🆕 `vatrootpornost.svg` — plamen sa unutrašnjim jezgrom |

**Stranica nije imala nijedan link ka `/proizvod/`.** Dodata sekcija „R-Tile ploče iz
ponude" sa dve kartice: **R-Tile Urban** (16920) i **R-Tile Design** (16921). Opisi
izvučeni iz `post_content` samih proizvoda (6,5 mm / 100% reciklirani PVC / do 1.200 kg;
mat PU / skriveni interlocking / 12 dekora / ~200 m²/dan) — **ništa izmišljeno**.

Pozadine tri sekcije ispod pomerene (`mist`→`paper`→`mist`→`paper`) da naizmenični
ritam i dijagonalni prelazi ostanu netaknuti posle umetanja nove sekcije.

## Gotcha-i

### 🔴 `<a class="al-card">` ne sme da nosi blok sadržaj — wpautop raspadne karticu
Prva verzija proizvod-kartica bila je `<a class="al-card">` sa `<div class="al-card__body">`
unutra (da naslov ide u telo tamnom bojom — bela verzala preko studijske fotke na beloj
pozadini je bila nečitljiva). **wpautop ubaci prazan `<p></p>` pre tog `<div>`-a**, parser
zatvori anchor, i telo kartice ispadne iz grid ćelije: slike ostanu u redu od 2, a tela se
nasložu ispod preko cele širine. Uhvaćeno tek u browseru (`outerHTML` grida), ne u izvoru.

**Rešenje:** kartica je `<div class="al-card">`, a link stoji na `.al-card__media` i unutar
`.al-card__title`. Postojeće `<a class="al-card">` kartice (homepage, padel) su bezbedne
**jer nemaju blok decu** — samo `<span>`-ove.

### 🔴 `.al-card__title a` gubi od pravila za linkove u sadržaju (0,3,1)
Naslov-link je dobijao plavo podvlačenje od `.entry-content a:not(.al-btn):not(.al-card)`
(`antas-design.css:1477`) — specifičnost **(0,3,1)**, jer svaki `:not(.klasa)` broji kao
klasa. Naivno `.al-card__title a` je **(0,1,1)** i tiho gubi.
**Rešenje:** izuzetak upisan uz postojeći izuzetak za `.wd-post-title`/`.wd-entities-title`,
istog oblika (`.entry-content .al-card__title a:not(.al-btn):not(.al-card)`), sa
`color: inherit` + `text-decoration: none` + `font-weight: inherit`, hover `var(--al-red)`.

### 🟡 Ikonice — 5 iteracija dok nisu čitljive
`brzina` i `odbijanje` su prošle kroz 5 verzija uz vizuelnu proveru na 46 px i 120 px:
prva verzija `brzina` je sa unutrašnjim „šavom" čitala kao **pola-popunjen krug**,
`odbijanje` je redom čitalo kao **kuka/laso**, pa kao **brda sa suncem**, pa kao
**kvačica**. **Pravilo: ikonica se ne prihvata iz koda — renderuje se na obe veličine
pored postojećih iz seta i gleda.** Privremeni `icon-preview-tmp.html` u root-u builda,
obrisan na kraju.

`vatrootpornost` je namerno crtana sa unutrašnjim plamenom da se ne meša sa
`odrzavanje.svg` (kap) — iste su siluete.

## Otvorene akcije
- [ ] **Definisati šta znači „stari format"** na 5119 i 15793 #ceka-miroslav
- [ ] **Fotke/logotipi za 4 klijenta bez materijala:** Beobasket, BG liga 3x3,
      Hotel Prag Beograd, Restoran Sidro #ceka-miroslav
- [ ] Potvrditi da li je `novi sajt/tereni za basket/Teren 3x3 Soccer liga.jpg`
      iz arhive zapravo BG liga 3x3 #ceka-miroslav
- [ ] 🔵 Opciono: Dunk Shop fotka je 600×600, a `.al-card__media` je 4/3 sa
      `object-fit: cover` → vrh/dno kadra se seku. Zameniti ako postoji šira. #ceka-miroslav

## Beleške / odluke
- `.al-ref-row` CSS pravilo (`antas-design.css:451`) ostaje u fajlu iako se posle ove
  sesije **nigde ne koristi** — nije brisano za slučaj da zatreba.
- Ikonice crtane ručno po specifikaciji postojećeg seta (24×24 viewBox, `stroke #F04D22`,
  `stroke-width 1.7`, round caps/joins, bez fill-a). Generator iz `design` skila
  (Gemini 3.1 Pro → SVG) namerno **nije korišćen** — ne pogađa taj house stil.
- Set ikonica je sa 23 narastao na **27** (`images/icons/`).

## Backup i fajlovi
- `antasline-backups/antasline_local_2026-08-12_pre-veruju-nam-slike.sql`
- `antasline-backups/antasline_local_2026-08-12_pre-onama-reference-slike.sql`
- `antasline-backups/antasline_local_2026-08-12_pre-padel-ikonice.sql`
- `antasline-backups/antasline_local_2026-08-12_pre-maloprodaja-ikonice-proizvodi.sql`
- `themes/woodmart-child/css/antas-design.css.bak-2026-08-12-card-title-link`
- Novi prilozi u medijateci: **17808** (Dunk Shop), **17810** (Metropolis), **17811** (Arabika)
- Nove ikonice: `brzina.svg` · `odbijanje.svg` · `bez-pripreme.svg` · `vatrootpornost.svg`

## Verifikacija
- Homepage: 6 `al-card__title`, 0 `al-ref-row`, sve slike 200
- O nama: 11 `al-card__title`, 0 `al-ref-row`, 1×H1, svih 11 slika 200
- Padel: 4 `al-icon` učitane (`naturalWidth` 150)
- Maloprodaja: 9 `al-icon`, kartice proizvoda jednake visine (599 px), naslovi bez
  podvlačenja (`textDecorationLine: none`, boja `rgb(14,41,80)`), obe `/proizvod/` putanje 200

## Veze
- [[DNEVNIK-NAPRETKA]] 2026-08-12
- [[reference/naucene-lekcije]] — dve nove lekcije (wpautop/anchor, `:not()` specifičnost)
- [[PROGRESS]] — Blokeri (#ceka-miroslav stavke gore)
