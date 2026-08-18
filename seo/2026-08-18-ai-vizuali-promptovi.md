---
tip: radni-materijal
datum: 2026-08-18
naziv: Promptovi za Gemini — AI vizuali
status: ceka-miroslava
folder: 'C:\Projektintasline-ai-slike'
azurirano: 2026-08-18
---

> Radna kopija; original stoji uz sam folder u `C:\Projektintasline-ai-slike\PROMPTOVI.md`.

# Promptovi za Gemini — AI vizuali za AntasLine

> Napravljeno 2026-08-18. Radni tok: Miroslav generiše u Geminiju → skida u
> odgovarajući podfolder → Claude Code obrađuje (1:1 ili 3:2 crop, WebP, WP veličine,
> alt tekst, attachment, ubacivanje na stranicu).

---

## 🔴 Pravilo pre svega: gde AI slika SME, a gde NE SME

| Sme | Ne sme |
|---|---|
| Ilustracija pojma („ovako izgleda EPA zona") | **Galerija „Izvedeni ESD podovi"** i svaka druga sekcija referenci |
| Sekcija „gde se koristi", uvodni vizual, pozadina | Bilo šta što tvrdi „ovo smo mi uradili" |
| Prikaz radnog procesa uopšte | Prikaz konkretnog klijenta ili lokacije |

Razlog: reference su prodajni dokaz. AI slika u galeriji izvedenih radova je lažan dokaz
— i prvi kupac koji pita „gde je ovo izvedeno" ruši poverenje u ceo sajt. Naše prave fotke
(Quectel, HTEC) ostaju jedini sadržaj galerije.

## Opšta pravila za svaki prompt

- **Bez teksta u slici.** AI generisan tekst je skoro uvek izobličen. Ako scena traži
  natpis (npr. „ESD PROTECTED AREA"), tražiti da znak bude **neizoštren ili odsečen**.
- **Bez logotipa i brendova.** Nikakvi prepoznatljivi znaci na opremi ili odeći.
- **Bez lica u fokusu.** Ljudi iz leđa, iz profila ili sa maskom/kapom — izbegavamo
  problem sa prepoznatljivim licem i „AI lice" izgled.
- **Pod je uvek isti:** tamno siva modularna PVC ploča 50 × 50 cm sa **vidljivim
  spojevima u obliku slagalice** i blago zrnastom teksturom. To je naš proizvod — ako
  pod izgleda kao gladak epoksid ili kao vinil u pločama bez spoja, slika je promašena.
- Format: traži **3:2 horizontalno** (za sekcije) ili **1:1** (za kartice). Ja ću seći.
- Ako izlaz ne valja → u `_odbaceno/`, ne brisati; korisno je da vidim šta model greši.

---

# 1. ESD stranica → folder `01-esd/`

Stranica `/antistatik-i-elektroprovodljivi-podovi/` je vizuelno već dobro pokrivena
(hero, presek, skala, tri ploče, uzemljenje, galerija, video). Fale samo **dve** scene
za koje nemamo nijednu fotku, a ciljamo ih u tekstu.

### 1.1 `esd-server-sala.webp` — server sala sa ESD podom
> **Zašto:** „server sale" su prvi segment u tekstu i u naslovu stranice, a nemamo
> nijednu fotografiju server sale.

```
Photorealistic interior photograph of a small server room. Two rows of black
server racks with subtle blue status LEDs, cables neatly routed overhead in
cable trays. The floor is dark grey modular interlocking PVC tiles, roughly
50x50 cm, with clearly visible jigsaw-shaped interlocking joints between tiles
and a slightly textured matte surface. Cool white LED ceiling lighting, faint
reflection of the racks on the floor. No people. No text, no signage, no logos.
Wide angle at eye level, sharp focus throughout, professional architectural
photography, 3:2 aspect ratio.
```

### 1.2 `esd-radno-mesto-montaza.webp` — radno mesto za montažu elektronike
> **Zašto:** ceo ESD argument („zaštita osetljive elektronike") nema nijednu sliku
> koja pokazuje šta se zapravo štiti.

```
Photorealistic photograph of an electronics assembly workstation in a clean
production facility. A technician seen from behind in an ESD lab coat, wearing
a grounded wrist strap with a coiled cord clipped to the bench. On the bench:
a green printed circuit board, a soldering station, a microscope, anti-static
component trays. The floor is dark grey modular interlocking PVC tiles about
50x50 cm with clearly visible jigsaw-shaped joints. Bright even industrial
lighting, shallow depth of field on the background. No visible face, no text,
no logos, no brand names. 3:2 aspect ratio, professional industrial photography.
```

### 1.3 `esd-ulaz-u-zonu.webp` — ulaz u EPA zonu *(opciono)*
> **Zašto:** vizuelno objašnjava pojam „elektrostatički zaštićena zona". Imamo sličan
> kadar sa Quectela u galeriji, pa je ovo niži prioritet.

```
Photorealistic photograph of an entrance to an electrostatic protected area in
a factory. Yellow and black floor marking tape on the floor forming a boundary
line, a warning sign mounted on the wall that is deliberately out of focus and
unreadable. Beyond the boundary the floor is dark grey modular interlocking PVC
tiles about 50x50 cm with visible jigsaw joints; in front of the boundary the
floor is plain grey concrete, so the difference between the two surfaces is
obvious. Industrial lighting, no people, no readable text. 3:2 aspect ratio.
```

---

# 2. „Podovi za radionice" → folder `02-radionice/`

> 🔴 **Ovo je, po GSC brojkama, najveća pojedinačna prilika koju imamo:**
> ~4.700 prikaza / ~275 klikova, prosečna pozicija 3,5, **a nemamo namensku stranicu.**
> Slike su preduslov da ta stranica ne bude zid teksta.

### 2.1 `radionica-auto-servis.webp`
```
Photorealistic photograph of a car repair workshop interior. A car raised on a
two-post lift, a tool trolley and a workbench with tools to the side. The entire
floor is dark grey modular interlocking PVC tiles about 50x50 cm with clearly
visible jigsaw-shaped joints, slightly textured matte surface, with a few oil
stains wiped clean. Bright ceiling lights, one open roller shutter door letting
in daylight. No people, no text, no logos, no car brand badges visible.
3:2 aspect ratio, professional industrial photography.
```

### 2.2 `radionica-masinska-cnc.webp`
```
Photorealistic photograph of a metalworking workshop with two CNC milling
machines. Metal chips swept into a small pile, a workbench with a vice along
the wall. The floor is dark grey modular interlocking PVC tiles about 50x50 cm
with visible jigsaw joints; a bright yellow tile line runs across the floor
marking a walkway. Industrial ceiling lighting, no people, no text, no logos.
3:2 aspect ratio.
```

### 2.3 `radionica-stolarska.webp`
```
Photorealistic photograph of a woodworking workshop. A table saw and a workbench
with clamps, planks stacked against the wall, fine sawdust on surfaces. The floor
is dark grey modular interlocking PVC tiles about 50x50 cm with clearly visible
jigsaw joints. Warm daylight from a side window mixed with ceiling lights. No
people, no text, no logos. 3:2 aspect ratio.
```

---

# 3. Magacini i skladišta → folder `03-magacini/`

> **Zašto:** `podovi za skladišta` stoji na poziciji 14,6 sa **CTR 0%** — prava rupa.
> `podovi za magacine` 436 prikaza, pozicija 4,0.

### 3.1 `magacin-zoniranje-bojama.webp`
> Ovo je naš najjači neiskorišćeni argument — zoniranje bojama umesto farbanja linija.
```
Photorealistic photograph of a warehouse interior with tall pallet racking on
both sides. The floor is modular interlocking PVC tiles about 50x50 cm with
clearly visible jigsaw joints: dark grey across most of the floor, with a wide
bright yellow lane of the same tiles marking a forklift route, and a smaller
blue tiled zone near the racks. A forklift parked in the background. Even
industrial ceiling lighting. No people, no text, no logos, no readable labels.
3:2 aspect ratio, professional industrial photography.
```

### 3.2 `magacin-preko-starog-betona.webp`
> Ilustruje ključnu primedbu koju obaramo: „moraću da rušim i da stanem sa radom".
```
Photorealistic photograph of a warehouse floor during renovation, showing a
clear before-and-after split down the middle of the frame. On the left: old
cracked grey concrete floor with dust and patches. On the right: new dark grey
modular interlocking PVC tiles about 50x50 cm with visible jigsaw joints, laid
directly over the same concrete, with the leading edge of the tiled area
visible mid-installation. A rubber mallet resting on the tiles. No people, no
text, no logos. 3:2 aspect ratio, sharp detail on the floor transition.
```

---

## Kad slike stignu — šta ja radim

1. Pregledam svaku i odbacim one gde pod ne izgleda kao modularna ploča (najčešća greška).
2. Crop na 1:1 ili 3:2, konverzija u WebP, max 1000 px, generisanje WP veličina 150–900 px.
3. Alt tekst na srpskom, opisni.
4. Attachment zapis + ubacivanje na odgovarajuće mesto, sa natpisom ispod slike.
5. Upis u dnevnik, uz **napomenu da je slika AI generisana** — da za pola godine niko ne
   pomisli da je referenca.
