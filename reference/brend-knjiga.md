# Brend — Knjiga grafičkih standarda (Antas line)

> Izvor: `Logo/Brand book.pdf` (13 str.) + `Logo/ANTAS LINE FINAL LOGO.pdf` (2 str., vertikalna + horizontalna varijanta). Dodato u vault 2026-07-05.

## Logo

- **Simbol:** teget ploča sa krstom (fuga između 4 ploče) + tri "listajuće" ploče iza (svetloplava → crvenonarandžasta → žutonarandžasta) — asocijacija na slojeve podnih ploča / strelice napred
- **Varijante:** vertikalna (simbol iznad teksta) i horizontalna (simbol levo od teksta); obe postoje i **monohromatski** (crna)
- **Pisanje imena u logotipu: "Antas line"** (dve reči, malo "l") — u vault-u i na sajtu se koristi "AntasLine"; za copy na sajtu zadržati postojeću konvenciju, za print/brending pratiti brand book

## Paleta boja

| Boja | CMYK | PANTONE | **HEX (pipeta iz logoa)** ⭐ | HEX (Pantone ref.) | Upotreba |
|---|---|---|---|---|---|
| Tamni teget (primarna) | 100/85/35/40 | 655 C | **`#0E2950`** | `#002554` | logotip tekst, glavna ploča, fascikla |
| Srednje plava | 100/80/20/20 | 655 C *(u PDF-u ponovljeno — verovatno greška, boja odgovara ~286/287 C)* | **`#0B3E75`** | ~`#1B4A9C` | druga ploča |
| Svetloplava | 70/40/10/0 | 279 C | **`#5287B7`** | `#418FDE` | treća ploča |
| Crvenonarandžasta | 0/85/100/0 | 172 C | **`#F04D22`** | `#FA4616` | akcenat, CTA (dugme "pozovite nas") |
| Žutonarandžasta | 0/45/100/0 | 137 C | **`#F89C1C`** | `#FFA300` | akcenat |

**⭐ Za web/temu koristiti "pipeta" kolonu** — izmereno 2026-07-05 iz piksela renderovanog `ANTAS LINE FINAL LOGO.pdf` (pdfium, CMYK→sRGB kroz profil). To su boje koje logo stvarno ima kad se izveze za ekran, pa se tema i logo asset poklapaju. Pantone HEX kolona je samo referenca — svetlija/zasićenija jer Pantone spot boje izlaze iz CMYK gamuta. Potvrđeno: vektorski PDF sadrži tačno 5 CMYK vrednosti iz brand book-a (+ belu), bez RGB definicija.

## Tipografija

- **Inter font family** (regular, medium/semibold, bold, extrabold) — jedina fontovska familija u brand book-u
- Relevantno za redizajn: Porto tema treba da koristi Inter (self-hosted zbog Core Web Vitals — bez Google Fonts CDN-a ako može)

## Web look & feel (str. 7)

- Header: horizontalni logo levo · meni HOME PONUDA USLUGE NOVOSTI KONTAKT · **crveno CTA dugme "pozovite nas" 069 234 00 72** (poklapa se sa lekcijom da 072 dominira tel klikovima)
- Hero: velika fotografija + crveni overlay blok sa H1 i uvodnim tekstom (primer: "Industrijski podovi" + Ecotile copy)

## Kontakt podaci iz brand book-a (vizitke, memorandumi, e-potpisi)

- **Antas line doo, Ulcinjska 13, Beograd**
- office@antasline.com · www.antasline.com
- +381 69 234 00 72 · +381 69 234 00 74
- Goran Tasić — direktor · Saša Bradić — prodaja / tehnička podrška

## Aplikacije pokrivene brand book-om

Vizitke, memorandum (opcija A i B), fascikla, brending (vozilo, roll-up, majica, radne pantalone, šlem), e-mail potpisi, Instagram/Facebook mockup-ovi.

## ⚠️ Uočene greške u PDF-u (javiti dizajneru pre štampe)

1. **"KNJGA"** umesto "KNJIGA" — na svakoj strani (footer + naslovna)
2. Pantone za srednju plavu ponovljen kao **655 C** (isti kao primarna) — verovatno treba 286/287 C
3. E-potpis: **"enviroment"** umesto "environment"
4. FB mockup: **"Informisite se"** umesto "Informišite se"

## Izvezeni logo asseti (2026-07-05, iz vektorskog PDF-a)

| Fajl | Format | Napomena |
|---|---|---|
| `Logo/antas-line-logo-vertikalni.svg` / `.png` | SVG vektor / PNG 668×777 transparent | boje = zvanična paleta iz ove tabele |
| `Logo/antas-line-logo-horizontalni.svg` / `.png` | SVG vektor / PNG 1360×435 transparent | za header (brand book web look&feel) |

Kopije za temu: `wp-content/themes/woodmart-child/images/`. Za web koristiti **SVG** gde god može (header builder preko theme URI); PNG je fallback za WP media library. Bela varijanta za navy pozadinu još nije napravljena.

## Povezano

- [[reference/identifikatori]] · [[reference/naucene-lekcije]] (072 telefon prioritet)
- Fajlovi: `Logo/ANTAS LINE FINAL LOGO.pdf`, `Logo/Brand book.pdf`
