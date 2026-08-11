---
name: dnevni-video
description: Dnevni video zadatak za AntasLine — jedan Flow/Veo kadar dnevno iz naših pravih fotografija, za stranicu iz reda čekanja. Koristi kad Miroslav kaže "dnevni video", "video za danas", "sledeći kadar", "Flow", "animiraj fotku" ili pri W2/W6 video radu.
---

# Dnevni video (Flow / Veo 3.1)

Cilj: **jedan kadar dnevno**, iz naše prave fotografije izvedenog projekta,
za stranicu koja curi klikove u GSC-u. Nije estetika — napad na CTR.

Red čekanja i pravila: `seo/video-red-cekanja.md` (**izvor istine**, ažuriraj ga na kraju).
Strategija: `seo/2026-08-09-video-obogacivanje-plan.md`.

## Preduslovi (proveri, ne pretpostavljaj)

- **Sat**: krediti se resetuju po pacifičkoj ponoći ≈ **09–10h po našem**.
  Pre 10h render pada na „You need more AI credits" — to nije kraj kvote.
- **Chrome dozvola** za `labs.google` u Claude ekstenziji (site-level permission).
  Bez nje svaki `computer`/`read_page` poziv vraća „Permission denied for this domain".
- **ffmpeg** instaliran (9.0, `winget install Gyan.FFmpeg`).
- Radni folder: `C:\Miroslav\AntasLine-video\{flow-in,clips,out}`.

## Koraci

### 1. Izaberi stavku
Prvi `⏳` red iz tabele u `seo/video-red-cekanja.md`. Ako je M imenovao stranicu — ta.

### 2. Nađi i pripremi fotku
- Arhive: `C:\Miroslav\Antas line\novi sajt\`, `C:\Miroslav\Antas Line priprema za sajt\`.
- Kriterijum: **naša izvedena realizacija**, širina ≥2400px, pejzažni format, jak kontrast.
  Proizvođački materijal (Bergo studio, Geoplast, Ergomat) **ne** bez posebne dozvole.
- Krop na 16:9 u `flow-in/`:

```python
from PIL import Image
im = Image.open(SRC); w,h = im.size
th = int(w*9/16); top = int((h-th)*0.45)   # 0.45 zadržava nebo/vertikalu
im.crop((0,top,w,top+th)).save(DST, quality=92)
```

- **Pogledaj krop pre uploada** (Read alat na jpg) — da li je predmet u kadru.

### 3. Napiši prompt
Šablon (engleski — srpski govor Veo izgovara loše, klipovi su ionako nemi):

```
Using the Veo 3.1 - Lite model. <POKRET KAMERE> over/toward a <OPIS PODLOGE>.
Gentle continuous camera motion only. <AMBIJENT: clouds drift, leaves sway,
soft moving shadows>. Photorealistic documentary style. Keep the surface,
colours and markings exactly as in the photo. Do not add any new objects or
people. No text, no logos, no captions.
```

Pokreti koji su radili: `slow tilt up`, `slow cinematic push-in`, `dolly forward
low over the surface`, `aerial drone slowly descending and pushing forward`,
`top-down drone slowly rising and rotating a few degrees`.

**Nikad** ne traži igrača, loptu, radnika ili bilo kakvu radnju.

### 4. Render u Flow-u
`labs.google/fx/tools/flow` → `+` u prompt boksu → **Upload media** (file input
postoji tek kad se meni otvori — provera DOM-a pre toga daje lažno negativan
rezultat) → nalepi prompt → 16:9 → odobri kredite (Lite 10 / Fast 20).

**Ako se agent zaglavi** (najavi render, ne pokaže dugme za odobrenje):
nova sesija (ikona olovke pored naslova panela), ne nastavljati staru.

Skini MP4 u `clips/`.

### 5. Provera pre montaže
Frejmovi na 0/⅓/⅔/kraj (`ffmpeg -ss X -frames:v 1`), pa Read alatom pogledaj:
- podloga, boje i linije netaknute?
- nema izmišljenih ljudi/lopti/objekata?
- tekst na zidovima/tablama ne „pluta"?
- **nema tuđeg vodenog žiga** (Gemini lepi „sparkle" dole desno; Flow ne lepi ništa)

Ako bilo šta pukne — prompt nazad na korak 3, ne krpiti u montaži.

### 6. Montaža (kad stranica ima ≥2 kadra)
ffmpeg, prelazi 0,5s, fade in/out, `-an`. Filter ide u fajl:
`ffmpeg ... -/filter_complex fajl.txt` (**ne** `-filter_complex_script` — nema ga u 9.0).
Bash ograničenje ~965 bajtova → filter uvek preko fajla, ne u komandnoj liniji.
Trajanja: Flow **8s**, Gemini **10s** po klipu.

### 7. Objava (kad blokatori padnu)
1. YouTube (javno, ne unlisted) — kanal `@antasline5676`.
2. Fasada u sadržaj stranice: standardni `.al-video-facade` markup (F7.3).
3. YouTube ID u mapu u `woodmart-child/inc/al-video-schema.php` — `VideoObject`
   se emituje sam, **nula izmena u bazi** (F7.3a).
4. GSC baseline snimi **pre** objave (28d, ciljani upiti stranice) u `analiza/`.

### 8. Zatvori dan
- Ažuriraj red čekanja: `⏳` → `✅`, upiši šta je potrošeno u kreditima.
- Kratak unos u `dnevnik/YYYY-MM-DD-*.md` + `DNEVNIK-NAPRETKA`.

## Budžet

50 kredita/dan = **5 klipova na Lite** ili 2 na Fast. Na sporim pokretima nad
statičnim terenom razlika Lite/Fast se **ne vidi** — Lite je podrazumevano.
Gemini je odvojena besplatna kvota (dnevni kapacitet je zbir), ali samo kao
rezerva zbog vodenog žiga.

## Šta ovaj skill NIJE

Ne pokreće se sam iz oblaka — traži lokalni Chrome (Flow), lokalni ffmpeg i
lokalnu foto-arhivu. Pokreće ga Miroslav sa `/dnevni-video`.
