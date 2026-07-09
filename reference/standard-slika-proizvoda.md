---
tip: referenca
datum: 2026-07-09
namena: Standard za slike proizvoda (AI generisanje + obrada) — odluka M 2026-07-09
---

# 📸 Standard slika proizvoda — AntasLine katalog

> Odluka M (2026-07-09): sve glavne slike proizvoda u katalogu prate ovaj
> standard. Primer-referenca: `C:\Users\Miroslav\Downloads\primer proizvoda.webp`
> (crna interlocking ploča, čista bela pozadina, meka senka).

## Format

- **Kanvas: 1080 × 1080 px (kvadrat)**
- **Čista bela pozadina** (pure white, #FFFFFF)
- Proizvod centriran, **~15% praznog prostora (margina) sa svih strana**
- Studijsko osvetljenje, meke senke, oštar fokus
- Izvoz: WebP (manja težina — CWV), fallback JPG po potrebi

## Prompt šablon (AI generisanje)

```
A professional e-commerce product photograph of [IME PROIZVODA].
Centered composition, looking clean and high-end. Pure white background.
The product is placed within a square 1080x1080 canvas, surrounded by a
consistent, balanced margin of empty white space (negative space) on all
sides, accounting for roughly 15% of the frame. Studio lighting, soft
shadows, sharp focus, 8k resolution, photorealistic.
```

`[IME PROIZVODA]` = engleski opis proizvoda sa ključnim vizuelnim detaljima,
npr. "a dark grey PVC interlocking floor tile 500x500mm with T-Joint
connectors visible on two edges" — što precizniji opis oblika/boje/spojeva,
to verniji rezultat. Za varijacije u boji: isti prompt, promeni samo boju.

## Pravila upotrebe

- Glavna slika proizvoda (`_thumbnail_id`) = standardizovana bela slika
- Galerija = standardizovana slika + stvarne fotografije primene/reference
  (postojeći uploads) — reference fotke se NE menjaju ovim standardom
- Slika varijacije (boja) = ista kompozicija u toj boji
- Ime fajla: `<slug-proizvoda>-<boja>.webp` (npr. `ecotile-e500-7-crna.webp`)
- Alt tekst: ime proizvoda + boja na srpskom

## Veze
Skill `/obogati-proizvod` (tačka 3 — galerija) · [[migracija/w1-polish-red-cekanja]]
