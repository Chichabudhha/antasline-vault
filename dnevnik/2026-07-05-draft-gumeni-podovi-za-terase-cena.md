---
tip: dnevnik
alat: claude-code
datum: 2026-07-05
blok: C3
status: draft-gotov
url_predlog: /gumeni-podovi-za-terase-cena/
---

# 🏡 Nova stranica — `/gumeni-podovi-za-terase-cena/` (plan #1, TIER 1)

> Izvor: [[seo/plan-novih-stranica]] TIER 1 #1 — "cena tabela + kalkulator + pošalji dimenzije forma"
> Status: **sadržaj napisan, čeka implementaciju na lokalnom buildu.**

## Ciljni upiti (GSC 16m)

| Upit | Impresije | Pozicija |
|---|---|---|
| gumeni podovi za terase cena | **4.221** | 3,96 |
| gumeni tepih za terasu | 1.945 | 1,05 ⭐ |
| gumene podloge za terasu | 1.637 | 1,37 ⭐ |
| gumeni podovi za terase | 1.358 | 5,34 |
| vinil podovi za terase | 1.216 | 8,6 |
| podne obloge za terasu cena (var.) | ~700 | 9–12 |

Šire terasa okruženje (podovi/podloge/obloge za terasu, ~13k impr, poz. 9–25) ostaje na `/spoljnje-podne-obloge/` — ova stranica cilja **cena-intent** (cena upiti CTR 9,9% vs info 3,3%). "epoksidni podovi za terase" (1.499 impr, poz. 8,9) hvata conquest sekcija, ne prodaja epoksida.

## Predlog sadržaja

### Yoast SEO naslov (59)
`Gumeni podovi za terase — cena po m² i vrste | Antas Line`

### Yoast meta opis (151)
`Koliko koštaju gumeni podovi za terasu? Cena po m² za gumene ploče, PVC/vinil i Bergo modularne podloge + kalkulator. Pošaljite dimenzije za tačnu ponudu.`

### H1
`Gumeni podovi za terase — cena po m² (2026)`

### Uvod (2–3 rečenice)
Odmah odgovor: okvirni raspon `{{CENA_TERASA_OD}}`–`{{CENA_TERASA_DO}}` RSD/m² zavisno od tipa, pa najava tabele i kalkulatora. Bez praznog uvoda — cena intent traži cifru u prvom ekranu.

### H2: Cena po vrsti podloge (tabela — srce stranice)

| Vrsta | Cena RSD/m² | Za koga |
|---|---|---|
| Gumene ploče (SBR/EPDM) | `{{CENA_GUMA_OD}}`–`{{CENA_GUMA_DO}}` | terase, dvorišta, oko bazena |
| PVC/vinil spoljne obloge | `{{CENA_PVC_T_OD}}`–`{{CENA_PVC_T_DO}}` | natkrivene terase |
| Bergo modularne podloge | `{{CENA_BERGO_OD}}`–`{{CENA_BERGO_DO}}` | terase, balkoni, krovne terase |
| Veštačka trava | `{{CENA_TRAVA_OD}}`–`{{CENA_TRAVA_DO}}` | dvorišta, dečji kutak |

+ red "montaža" ako se naplaćuje posebno. Napomena ispod tabele: cena zavisi od kvadrature, pripreme podloge i debljine.

### H2: Šta ulazi u cenu (edukacija pre CTA)
- priprema postojeće površine (beton/pločice — može preko, bez razbijanja)
- transport i montaža (klik/interlock sistemi = brza ugradnja bez lepka)
- drenaža/odvod vode kod otvorenih terasa (perforirane vs pune ploče)

### H2: Kalkulator — koliko će me koštati terasa
Jednostavan blok: unos m² × izbor tipa → okvirni raspon. Ako je kalkulator komplikovan za WPBakery, zameniti tabelom "primeri: 10 m² / 20 m² / 50 m²" sa gotovim računicama.

### H2: Epoksid za terasu? (conquest sekcija, 1.499 impr)
2–3 pasusa: zašto epoksidni premaz na spoljnoj terasi puca (UV, mraz, dilatacije) i šta koristiti umesto toga → link ka `/epoksidni-podovi-ili-ecotile-podovi/`. **Ne prodajemo epoksid.**

### H2 + CTA: Pošaljite dimenzije — vraćamo tačnu ponudu
- forma (dimenzije + slika terase) → `/hvala-za-poruku/`
- telefon **069 234 00 72** (klik-to-call, 072 prioritet)

### Interni linkovi
- → `/spoljnje-podne-obloge/` (hub za terase — uzajamno linkovanje)
- → `/epoksidni-podovi-ili-ecotile-podovi/` (conquest)
- → Bergo proizvodi (slug po C1/C2 odluci)
- `/industrijski-podovi/` — NE (nije relevantno, ne forsirati)

## FAQ blok (HTML)

```html
<h2>Često postavljana pitanja — gumeni podovi za terase</h2>

<div class="faq-block">
  <h3>Kolika je cena gumenih podova za terasu po m²?</h3>
  <p>Gumene ploče koštaju {{CENA_GUMA_OD}}–{{CENA_GUMA_DO}} RSD/m², PVC/vinil
  {{CENA_PVC_T_OD}}–{{CENA_PVC_T_DO}} RSD/m², a Bergo modularne podloge
  {{CENA_BERGO_OD}}–{{CENA_BERGO_DO}} RSD/m². Tačna cena zavisi od kvadrature
  i pripreme podloge.</p>
</div>

<div class="faq-block">
  <h3>Može li gumeni pod da se postavi preko postojećih pločica ili betona?</h3>
  <p>Da — gumene i modularne podloge se postavljaju direktno preko ravne
  betonske površine ili pločica, bez razbijanja i bez lepka kod klik sistema.</p>
</div>

<div class="faq-block">
  <h3>Da li gumene podloge propuštaju vodu?</h3>
  <p>Perforirane (drenažne) ploče propuštaju vodu i suše se brzo — preporuka za
  otkrivene terase. Pune ploče se koriste na natkrivenim terasama.</p>
</div>

<div class="faq-block">
  <h3>Da li je epoksidni pod dobar za terasu?</h3>
  <p>Epoksidni premazi na spoljnim terasama vremenom pucaju zbog UV zračenja,
  mraza i širenja betona. Za spoljnu upotrebu su trajnije gumene, PVC ili
  modularne podloge koje prate rad podloge.</p>
</div>

<div class="faq-block">
  <h3>Koliko traje montaža?</h3>
  <p>Prosečna terasa (do 30 m²) se postavlja za jedan dan. Pošaljite nam
  dimenzije i sliku terase za tačnu ponudu i termin.</p>
</div>
```

## FAQPage schema (JSON-LD)

```json
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {"@type": "Question", "name": "Kolika je cena gumenih podova za terasu po m²?",
     "acceptedAnswer": {"@type": "Answer", "text": "Gumene ploče koštaju {{CENA_GUMA_OD}}–{{CENA_GUMA_DO}} RSD/m², PVC/vinil {{CENA_PVC_T_OD}}–{{CENA_PVC_T_DO}} RSD/m², a Bergo modularne podloge {{CENA_BERGO_OD}}–{{CENA_BERGO_DO}} RSD/m²."}},
    {"@type": "Question", "name": "Može li gumeni pod da se postavi preko postojećih pločica ili betona?",
     "acceptedAnswer": {"@type": "Answer", "text": "Da — postavljaju se direktno preko ravne betonske površine ili pločica, bez razbijanja i bez lepka kod klik sistema."}},
    {"@type": "Question", "name": "Da li gumene podloge propuštaju vodu?",
     "acceptedAnswer": {"@type": "Answer", "text": "Perforirane (drenažne) ploče propuštaju vodu — preporuka za otkrivene terase. Pune ploče su za natkrivene terase."}},
    {"@type": "Question", "name": "Da li je epoksidni pod dobar za terasu?",
     "acceptedAnswer": {"@type": "Answer", "text": "Epoksidni premazi na spoljnim terasama pucaju zbog UV zračenja i mraza. Za spoljnu upotrebu su trajnije gumene, PVC ili modularne podloge."}},
    {"@type": "Question", "name": "Koliko traje montaža?",
     "acceptedAnswer": {"@type": "Answer", "text": "Prosečna terasa do 30 m² se postavlja za jedan dan."}}
  ]
}
```

## Otvoreno pre implementacije
- [ ] 8 cena placeholder-a (guma/PVC/Bergo/trava od–do) #ceka-miroslav
- [ ] Kalkulator: pravi (JS) ili tabela primera? — preporuka: tabela primera za v1
- [ ] Bergo slug na redizajnu (C1/C2) #ceka-miroslav
- [ ] Implementacija na lokalnom buildu + Rich Results Test #claude-code

## Veze
- [[seo/plan-novih-stranica]] TIER 1 #1 · [[analiza/2026-07-04-gsc-kw-analiza-16m]]
- Sibling: [[dnevnik/2026-07-05-draft-podloge-za-parkiraliste-cena]] (spoljne površine)
