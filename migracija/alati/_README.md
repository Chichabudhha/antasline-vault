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

## Pravila kojih se držati

- **`alt` je obavezan**, na srpskom, opisuje ŠTA se vidi (ne ime fajla). Ide i u
  `_wp_attachment_image_alt` i u `<img alt>`.
- Naslov priloga = ljudski natpis **sa razmacima** — koristi se kao natpis u
  lightbox-u (`al_image_caption()` odbacuje naslove koji liče na ime fajla).
- Skaliranje na **max 1600px** duže stranice, JPEG q82, **bez uvećavanja**.
  1600 = `al-lb`, verzija koju otvara lightbox.
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

- [x] **16657** Košarkaške konstrukcije — 9 fotki, sekcija „Naši izvedeni tereni"
- [ ] ~28 stranica bez ijedne slike (v. popis u [[DNEVNIK-NAPRETKA]] 2026-07-28)
- [ ] dopuna stranica koje imaju malo/slabe slike
