---
tip: dnevnik
alat: claude-code
datum: 2026-07-05
blok: C3
status: draft-gotov
url_predlog: /dimenzije-kosarkaskog-terena/
---

# 🏀 Nova stranica — `/dimenzije-kosarkaskog-terena/` (plan #4, TIER 1, C3-orig)

> Izvor: [[seo/plan-novih-stranica]] TIER 1 #4 — "FIBA/NBA/školski tabele + crtež + FAQ → featured snippet"
> Status: **sadržaj napisan, čeka implementaciju na lokalnom buildu.**
> ⚠️ **Kanibalizacija:** ovi upiti sada rangiraju poz. 1,2–1,7 preko članka "Kako napraviti teren za basket". Nova stranica mora biti ODGOVOR-stranica (čiste tabele, featured snippet meta), a postojeći članak ostaje KAKO-stranica (izgradnja, cena) — uzajamni linkovi + iz članka skloniti/skratiti dimenzije sekciju da se ne takmiče.

## Ciljni upiti (GSC 16m — najveći sportski klaster)

| Upit | Impresije | Pozicija |
|---|---|---|
| dimenzije košarkaškog terena (+lat.) | **8.655** | 1,4–1,7 |
| kosarkaski teren dimenzije (+ćir./var.) | 5.788 | 1,2–1,5 |
| duzina kosarkaskog terena | 1.007 | 1,8 (CTR ~0!) |
| dimenzije košarkaškog igrališta | 909 | 1,2 |
| dimenzije ... u evropi / fiba / u nba | 2.078 | 1,5–2,4 |
| dimenzije terena za kosarku · velicina · var. | ~1.300 | 1,4 |

**~20k impresija, pozicija 1–2, a CTR nizak** — klasičan slučaj za featured snippet + klikabilan title. Cilj nije rang (imamo ga) nego CTR i snippet.

## Predlog sadržaja

### Yoast SEO naslov (59)
`Dimenzije košarkaškog terena — FIBA, NBA, školski [tabela]`

### Yoast meta opis (149)
`Dimenzije košarkaškog terena: FIBA 28×15 m, NBA 28,65×15,24 m. Tabela svih mera — reket, linija za tri poena, visina koša + crtež terena za preuzimanje.`

### H1
`Dimenzije košarkaškog terena (FIBA, NBA, školski)`

### Uvod — SNIPPET PASUS (prve 2 rečenice = odgovor)
"Zvanične dimenzije košarkaškog terena po FIBA pravilima su **28 × 15 metara**. NBA teren je nešto veći: 28,65 × 15,24 m (94 × 50 stopa), dok školski i rekreativni tereni obično koriste 26 × 14 m ili manje."

### H2: Tabela — sve dimenzije na jednom mestu (srce stranice, snippet target)

| Element | FIBA (Evropa) | NBA |
|---|---|---|
| Teren (dužina × širina) | 28 × 15 m | 28,65 × 15,24 m |
| Visina koša (obruča) | 3,05 m | 3,05 m |
| Linija za tri poena | 6,75 m (6,60 u uglu) | 7,24 m (6,70 u uglu) |
| Linija slobodnih bacanja | 5,80 m od osnovne linije | 5,80 m |
| Reket (širina × dužina) | 4,90 × 5,80 m | 4,88 × 5,79 m |
| Centralni krug (prečnik) | 3,60 m | 3,66 m |
| Polukrug bez probijanja | 1,25 m | 1,22 m |
| Širina linija | 5 cm | 5 cm |

*(Pre objave uporediti sa zvaničnim FIBA Official Basketball Rules — Basketball Equipment dokumentom i staviti izvor-link — Miroslav traži linkovane izvore.)*

### H2: Školski, rekreativni i dvorišni tereni
- školska sala/dvorište: 26 × 14 m ili 20 × 12 m
- polu-teren (half-court) za dvorište: od 10 × 7 m — realna minimalna mera za jedan koš
- **prelaz ka ponudi:** "projektujemo i teren po meri vašeg placa" → link ka basket članku + kontakt

### H2: Dimenzije 3x3 terena (rastući format, Olimpijske igre)
- 15 × 11 m, jedan koš, FIBA 3x3 standard — kratka sekcija, hvata "3x3 teren dimenzije" long-tail

### H2: Crtež terena (vizuelni asset)
Kotirana skica terena sa svim merama — SVG/PNG za preuzimanje. Ovo je i link-magnet (škole, treneri, seminarski). Skica u brand bojama ([[reference/brend-knjiga]]: teget `#0E2950`, linije `#F04D22`). #claude-code može generisati SVG

### H2: Od dimenzija do gotovog terena (CTA sekcija)
Kratko: podloga (Bergo modularna), konstrukcije, obeležavanje linija → linkovi ka ponudi + telefon **069 234 00 72** + forma → `/hvala-za-poruku/`

### Interni linkovi
- → članak "Kako napraviti teren za basket" (KAKO-stranica; uzajamno)
- → [[dnevnik/2026-07-04-dimenzije-kosarkaske-table|/dimenzije-kosarkaske-table/]] (sibling — tabla mere)
- → kategorija **Košarkaške konstrukcije** (slug po C1 odluci)
- → Bergo sportske podloge
- Spoljni izvori (E-E-A-T po Miroslavljevoj sugestiji): FIBA official rules PDF, NBA rulebook

## FAQ blok (HTML)

```html
<h2>Često postavljana pitanja o dimenzijama košarkaškog terena</h2>

<div class="faq-block">
  <h3>Koje su dimenzije košarkaškog terena?</h3>
  <p>Po FIBA standardu (važi za Evropu i Srbiju) teren je <strong>28 metara dug
  i 15 metara širok</strong>. NBA teren je 28,65 × 15,24 m.</p>
</div>

<div class="faq-block">
  <h3>Kolika je visina koša?</h3>
  <p>Obruč je na <strong>3,05 m</strong> od poda — isto u FIBA, NBA i školskoj
  košarci.</p>
</div>

<div class="faq-block">
  <h3>Na kojoj udaljenosti je linija za tri poena?</h3>
  <p>FIBA: 6,75 m od centra koša (6,60 m u uglovima). NBA: 7,24 m (6,70 m u
  uglovima).</p>
</div>

<div class="faq-block">
  <h3>Koliki je teren za basket u dvorištu?</h3>
  <p>Za jedan koš (half-court) dovoljno je od 10 × 7 m. Pun rekreativni teren
  je obično 26 × 14 m. Teren se može prilagoditi placu — pošaljite nam dimenzije.</p>
</div>

<div class="faq-block">
  <h3>Koje su dimenzije 3x3 terena?</h3>
  <p>Zvanični FIBA 3x3 teren je <strong>15 × 11 m</strong> sa jednim košem.</p>
</div>
```

## FAQPage schema (JSON-LD)

```json
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {"@type": "Question", "name": "Koje su dimenzije košarkaškog terena?",
     "acceptedAnswer": {"@type": "Answer", "text": "Po FIBA standardu teren je 28 metara dug i 15 metara širok. NBA teren je 28,65 × 15,24 m."}},
    {"@type": "Question", "name": "Kolika je visina koša?",
     "acceptedAnswer": {"@type": "Answer", "text": "Obruč je na 3,05 m od poda — isto u FIBA, NBA i školskoj košarci."}},
    {"@type": "Question", "name": "Na kojoj udaljenosti je linija za tri poena?",
     "acceptedAnswer": {"@type": "Answer", "text": "FIBA: 6,75 m od centra koša (6,60 m u uglovima). NBA: 7,24 m (6,70 m u uglovima)."}},
    {"@type": "Question", "name": "Koliki je teren za basket u dvorištu?",
     "acceptedAnswer": {"@type": "Answer", "text": "Za jedan koš dovoljno je od 10 × 7 m; pun rekreativni teren je obično 26 × 14 m."}},
    {"@type": "Question", "name": "Koje su dimenzije 3x3 terena?",
     "acceptedAnswer": {"@type": "Answer", "text": "Zvanični FIBA 3x3 teren je 15 × 11 m sa jednim košem."}}
  ]
}
```

## Otvoreno pre implementacije
- [ ] Proveriti sve mere protiv zvaničnog FIBA dokumenta + dodati izvor-linkove #claude-code
- [ ] U postojećem basket članku skratiti dimenzije sekciju na 2 rečenice + link ovamo (anti-kanibalizacija) #claude-code
- [ ] Kotirana SVG skica terena u brand bojama #claude-code
- [ ] Slug kategorije Košarkaške konstrukcije (C1) #ceka-miroslav
- [ ] Implementacija + Rich Results Test #claude-code

## Veze
- [[seo/plan-novih-stranica]] TIER 1 #4 · [[dnevnik/2026-07-02-basket-page-faq-schema]] (postojeći članak)
- Sibling: [[dnevnik/2026-07-04-dimenzije-kosarkaske-table]] (#5, tabla)
