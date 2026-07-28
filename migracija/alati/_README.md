---
tip: reference
azurirano: 2026-07-28
---

# Alati za kuriranje fotografija (F7.21)

Nastalo 2026-07-28 uz [[woodmart-sabloni]] F7.21. Namena: nastaviti pun prolaz
kroz stranice bez ponovnog skeniranja 1.807 fotki.

## Izvori fotografija

| Folder | Fotki | ≥1400px |
|---|---|---|
| `C:\Miroslav\Antas line` | ~800 | |
| `C:\Miroslav\Antas Line priprema za sajt` | ~1.000 | |
| **ukupno (bez `Backup/` i `AI/`)** | **1.807** | **364 (20%)** |

Popis sa dimenzijama: [[analiza/2026-07-28-foto-inventar.csv]]
(kolone: `putanja, folder, fajl, w, h, orijentacija, KB`).

**Hi-res folderi — odatle birati prvo:**

| Folder | ukupno | ≥1400px |
|---|---|---|
| `novo/slike bergo multisport` | 47 | **43** |
| `novo/ecotile` | 42 | **37** |
| `Karusel slike Dekorativne meni` | 20 | **18** |
| `slike 12-22/bergo ultimate` | 29 | 17 |
| `slike 12-22/bergo baste` | 31 | 14 |
| `novi sajt/Bergo` | 163 | 49 |
| `novi sajt/tereni za basket` | 91 | 29 |
| `slike 12-22/ecotile industrija` | 35 | 11 (max **7360px**) |
| `slike 12-22/esd antistatik` | 11 | 9 |

## Radni tok

**1. Suzi kandidate iz inventara** (PowerShell):

```powershell
$csv = Import-Csv analiza\2026-07-28-foto-inventar.csv
$csv | Where-Object { $_.folder -like "*bergo baste*" -and [int]$_.w -ge 1200 } |
  Sort-Object {[int]$_.w} -Descending | Select-Object -First 24 |
  ForEach-Object { $_.putanja } | Set-Content lista.txt -Encoding UTF8
```

**2. Napravi kontakt-list** — mozaik sličica, da se 24 fotke pregledaju kroz JEDNU
sliku umesto jedne po jedne (ovo štedi ogromno konteksta):

```powershell
php contact_sheet.php lista.txt sheet.jpg 4     # 4 kolone
```

Zatim `Read` nad `sheet.jpg`. Sličice nose redni broj, ime fajla i dimenzije.

> ⚠️ Vrlo velike slike (4608px+) ume GD da preskoči zbog memorije — u mozaiku
> ostane prazno polje. Nije greška izbora, samo je proveri posebno.

**3. Uvezi i ubaci.** `al_import.php` je generički (JSON posao), a
`primer-job-16657.php` je konkretan primer koji je stvarno pokrenut — obično je
lakše kopirati njega i izmeniti listu nego pisati JSON.

```powershell
php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job.php
```

## Alati (F7.22, 2026-07-28)

| Skript | Šta radi |
|---|---|
| `al_webp.php` | zajedničke funkcije: `al_target_ext/mime`, `al_ids_from_content` (hvata i `<img>` i `[gallery ids]`) |
| `al_import.php` | uvoz iz foldera + ubacivanje bloka; WebP izlaz, EXIF rotacija, kopira bez prekodiranja ako je izvor već WebP ≤1600px. Opcije posla: `before` (pre sekcije po nizu — traži POSLEDNJI pogodak), `section_class`, `label`, `raw`, `columns` |
| `al_move_section.php` | premešta `[vc_row]` sekciju unutar stranice (`16589,1,5 apply`) |
| `al_regen_sizes.php` | regeneriše SAMO `al-*` veličine (kao WebP). `content` \| `post:ID` \| `17031,17032` |
| `al_fix_missing_sizes.php` | popravlja priloge kojima u zapisu piše veličina bez fajla; korisno i kao provera zdravlja medijateke |
| `al_convert_webp.php` | ⛔ **superseded** — konvertuje sam original. Zadržan kao trag; v. dnevnik zašto je pristup napušten |
| `contact_sheet.php` | mozaik sličica za pregled kandidata |
| `job-15580-parking.php` | primer stvarno izvršenog posla (uz `primer-job-16657.php`) |

Poziv: `php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file <skript> [args]`
(prvo bez `apply` — svi skriptovi imaju probu).

> `mysql` mora biti na PATH-u: `$env:PATH="C:\xampp\mysql\bin;$env:PATH"`.

## Pravila kojih se držati

- 🔴 **Profil stranice (H1/H2) PRE izbora fotografija — nikad po slugu.**
  `/zastitne-podloge-za-travu-i-plocnike/` zvuči kao rešetke za travu, a H1 je
  „Bergo Solid" (zaštitne ploče za teret). Izbor po slugu bi bio potpuno pogrešan.
- 🔴 **EXIF orijentacija:** `WP_Image_Editor` je ne primenjuje kad se poziva
  direktno; `al_import.php` je zato radi sam. Ali EXIF u ovoj arhivi **nije
  pouzdan** — postoje fajlovi sa `Orientation: 6` čiji su pikseli već uspravni,
  gde bi rotacija pokvarila sliku. Zato `contact_sheet.php` prikazuje sliku POSLE
  rotacije: bira se nad onim što uvoz stvarno daje.
- **Redosled u galeriji:** rezultat → reference → proces/detalj.
- **Ne praviti galeriju bez materijala.** Ako za temu stranice ne postoje prave
  fotografije, bolje je prijaviti nedostatak nego popuniti stranicu slikama koje
  impliciraju posao koji nije naš (v. 16677, 16671).

- **`alt` je obavezan**, na srpskom, opisuje ŠTA se vidi (ne ime fajla). Ide i u
  `_wp_attachment_image_alt` i u `<img alt>`.
- Naslov priloga = ljudski natpis **sa razmacima** — koristi se kao natpis u
  lightbox-u (`al_image_caption()` odbacuje naslove koji liče na ime fajla).
- Skaliranje na **max 1600px** duže stranice, q82, **bez uvećavanja**.
  1600 = `al-lb`, verzija koju otvara lightbox. Izvor koji je već WebP i ispod
  1600px se **kopira**, ne prekodira (prekodiranje = čist gubitak generacije).
- **Original se NE konvertuje u WebP.** WebP izlaze samo izvedene `-WxH` veličine,
  preko `image_editor_output_format` filtera u `woodmart-child/functions.php`.
  Konvertovanje originala je probano i izmereno kao pogrešno (−5%, dve slike veće,
  palette PNG fatalno ruši GD) — v. dnevnik 2026-07-28.
- **Posle svakog masovnog rada nad medijatekom** pustiti i proveru slika
  (`src`+`srcset`+`href` sa svih 199 URL-ova → HEAD svake slike), ne samo proveru
  stranica: 404 na slici **ne** obara HTTP status stranice, pa ga standardna
  provera ne vidi. Tako je uhvaćeno 212 pokvarenih `woocommerce_single` slika.
- Slike u sadržaj ubacivati kao **gol `<img>`** — `al_enhance_content_images()`
  ih sam umota u lightbox link i doda `srcset`/`width`/`height`/`lazy`.
- Nova sekcija = zaseban `[vc_row el_class="al-section al-section--paper"]`
  umetnut na tačan indeks (`preg_split('#(?=\[vc_row)#')`), **bez `al-diag-*`**
  ako susedna sekcija već nosi rez (F7.20). Poštuj smenu `paper`/`mist`.
- **Backup sadržaja stranice pre svake izmene** (skripte to već rade u
  `scratchpad/content-backup/`).
- Posle svake serije: sitewide provera (199 URL-ova, HTTP 200 / 1×H1 / 0 PHP
  grešaka) + Chrome vizuelna potvrda.

## Stanje kuriranja

**31 → 8 stranica bez slika** (2026-07-28). Preostalih 8 je namerno bez galerije.

- [x] **16657** Košarkaške konstrukcije · **15580** Podloge za parking
- [x] **21 stranica** u prolazu F7.23: 16589 · 15793 · 16658 · 15480 · 16665 ·
  17019 · 16590 · 16660 · 17026 · 17025 · 6588 · 16586 · 16585 · 16688 · 17027 ·
  5754 · 5119 · 5455 · 5512 · 16683 · 16666 · 571
- [ ] **16 stranica sa 1–2 slike** — dopuna; najveće: `industrijski-podovi-cena`
  (997 reči, 1 sl.) · `podloge-za-parkiraliste-cena` (958, 2) ·
  `industrijski-podovi-montaza-preko-ostecenog-epoksida` (906, 1)
- [ ] 🔴 **#ceka-miroslav — nedostaje foto materijal:**
  - `16677 reflektori-za-sportske-terene` — nema nijedne fotke mobilnog LED
    reflektora (ono što pretraga nađe su LED senzori za pešake, drugi proizvod)
  - `16671 bumperi…` — samo 1 fotka u primeni, a stranica već ima mrežu proizvoda

Bez fotografija po prirodi: `politika-kolacica`, `kontakt`, `hvala-za-poruku`,
`katalog`, `aktuelnosti`, `planer-terena`.
