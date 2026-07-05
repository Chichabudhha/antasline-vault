---
tip: dnevnik
alat: claude-code
datum: 2026-07-05
blok: C3
status: draft-gotov
url_predlog: /podovi-za-garaze/
---

# 🚗 Nova stranica — `/podovi-za-garaze/` (plan #3, TIER 1)

> Izvor: [[seo/plan-novih-stranica]] TIER 1 #3 — "tipovi+cena+before/after. Klaster: 14k GSC impr + 16,8k RSD Ads bez konv → landing je rupa"
> Status: **sadržaj napisan, čeka implementaciju na lokalnom buildu.**
> Ovo je **najveća pojedinačna rupa u levku**: najviše para potrošeno u Ads (16,8k RSD, 0 konverzija) + organik poz. 8–10 na 14k impresija — sve zato što ne postoji posvećena stranica.

## Ciljni upiti (GSC 16m — konsolidovana landing hvata ceo klaster)

| Upit | Impresije | Pozicija |
|---|---|---|
| podovi za garaze / za garaže / za garazu | 3.197 | 8,1–22 |
| pod za garazu | 1.684 | 8,6 |
| gumeni podovi za garaze cena | 830 | 8,0 |
| pvc podovi za garaze (+cena, +varijante) | 1.693 | 8,4–9,3 |
| podloga za garazu / podne obloge za garazu | 988 | 8,2–8,4 |
| garazni/garažni podovi | 671 | 6,5–13,4 |
| gumeni pod za garazu | 365 | 7,5 |

Ceo klaster sedi na pozicijama 6–10 **bez stranice** — konsolidovana landing sa cenom je klasičan "page-gap" fix.

## Predlog sadržaja

### Yoast SEO naslov (55)
`Podovi za garaže — PVC ploče, cena po m² | Antas Line`

### Yoast meta opis (153)
`Kakav pod za garažu izabrati? PVC klik ploče preko betona — bez lepka i prašine, otporne na ulje i gume. Cena po m², ugradnja za jedan dan. Pogledajte.`

### H1
`Podovi za garaže — vrste i cena po m²`

### Uvod
Raspon cene odmah (`{{CENA_GARAZA_OD}}`–`{{CENA_GARAZA_DO}}` RSD/m²) + ključna prednost: postavlja se preko postojećeg betona bez razbijanja, garaža se koristi isti dan.

### H2: Vrste podova za garažu (poređenje)

| Rešenje | Prednosti | Mane |
|---|---|---|
| **PVC klik ploče (Ecotile)** ⭐ naša preporuka | preko starog betona, bez lepka, otporne na ulje/gume/so, zamena pojedinačne ploče | viša početna cena od premaza |
| Gumene ploče | jeftinije, dobre za radionicu | mekše, tragovi od točkova |
| Epoksidni premaz | — | traži savršen beton, prašina pri brušenju, puca od soli i vlage zimi → conquest link |
| Goli beton | besplatan | prašina, upija ulje, hladan |

### H2: Zašto PVC ploče preko betona (adresira "pvc podovi preko betona" upite)
- ne mora ravan/nov beton — ide preko pukotina i oštećenja
- montaža bez lepka: čekić i podloška, ~pola dana za standardnu garažu (20–30 m²)
- otpornost: ulje, kočiona tečnost, zimska so, vrele gume
- termoizolacija vs goli beton (toplija garaža-radionica)

### H2: Cena poda za garažu (tabela primera)

| Garaža | Kvadratura | Okvirna cena |
|---|---|---|
| Jedno vozilo | ~18 m² | `{{CENA_G18}}` RSD |
| Dva vozila | ~35 m² | `{{CENA_G35}}` RSD |
| Radionica/mali servis | 50+ m² | ponuda na upit (B2B rabat) |

### H2: Pre i posle (before/after galerija)
2–4 para slika: ispucao/prašnjav beton → gotov Ecotile pod. Ako nema garažnih slika u medijateci, koristiti autoservis reference (isti proizvod, jači trust). #ceka-miroslav slike

### H2 + CTA
- forma "pošaljite dimenzije garaže + sliku poda" → `/hvala-za-poruku/`
- telefon **069 234 00 72**

### Interni linkovi
- → `/industrijski-podovi/` (isti proizvod, industrijska namena — silo juice)
- → `/epoksidni-podovi-ili-ecotile-podovi/` (conquest — epoksid red u tabeli)
- → [[dnevnik/2026-07-05-draft-industrijski-podovi-cena|/industrijski-podovi-cena/]] (za radionice/servise 50+ m²)
- → Ecotile kategorija proizvoda

## FAQ blok (HTML)

```html
<h2>Često postavljana pitanja o podovima za garaže</h2>

<div class="faq-block">
  <h3>Koji je najbolji pod za garažu?</h3>
  <p>Za garaže preporučujemo PVC klik ploče: postavljaju se preko postojećeg
  betona bez lepka, otporne su na ulje, so i vrele gume, a oštećena ploča se
  menja pojedinačno. Gumene ploče su jeftinija opcija za radionice bez vozila.</p>
</div>

<div class="faq-block">
  <h3>Kolika je cena poda za garažu?</h3>
  <p>PVC ploče koštaju {{CENA_GARAZA_OD}}–{{CENA_GARAZA_DO}} RSD/m². Garaža za
  jedno vozilo (~18 m²) okvirno {{CENA_G18}} RSD sa montažom.</p>
</div>

<div class="faq-block">
  <h3>Može li preko starog, ispucalog betona?</h3>
  <p>Da — to je glavna prednost. Ploče premošćuju pukotine i neravnine, bez
  brušenja, izlivanja mase i prašine. Garaža se koristi već isti dan.</p>
</div>

<div class="faq-block">
  <h3>Da li vrele gume i ulje oštećuju PVC pod?</h3>
  <p>Ne — industrijske PVC ploče su predviđene za viljuškarski saobraćaj, pa im
  putničko vozilo, ulje ili zimska so ne smetaju.</p>
</div>

<div class="faq-block">
  <h3>Zašto ne epoksid za garažu?</h3>
  <p>Epoksidni premaz traži savršeno pripremljen beton, a zimska so i vlaga ga
  vremenom ljušte i pucaju. PVC ploče nemaju taj problem i mogu da se demontiraju
  i ponesu pri selidbi.</p>
</div>
```

## FAQPage schema (JSON-LD)

```json
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {"@type": "Question", "name": "Koji je najbolji pod za garažu?",
     "acceptedAnswer": {"@type": "Answer", "text": "PVC klik ploče: postavljaju se preko postojećeg betona bez lepka, otporne su na ulje, so i vrele gume, a oštećena ploča se menja pojedinačno."}},
    {"@type": "Question", "name": "Kolika je cena poda za garažu?",
     "acceptedAnswer": {"@type": "Answer", "text": "PVC ploče koštaju {{CENA_GARAZA_OD}}–{{CENA_GARAZA_DO}} RSD/m². Garaža za jedno vozilo (~18 m²) okvirno {{CENA_G18}} RSD sa montažom."}},
    {"@type": "Question", "name": "Može li preko starog, ispucalog betona?",
     "acceptedAnswer": {"@type": "Answer", "text": "Da — ploče premošćuju pukotine i neravnine, bez brušenja i prašine. Garaža se koristi isti dan."}},
    {"@type": "Question", "name": "Da li vrele gume i ulje oštećuju PVC pod?",
     "acceptedAnswer": {"@type": "Answer", "text": "Ne — industrijske PVC ploče su predviđene i za viljuškarski saobraćaj."}},
    {"@type": "Question", "name": "Zašto ne epoksid za garažu?",
     "acceptedAnswer": {"@type": "Answer", "text": "Epoksid traži savršen beton, a so i vlaga ga ljušte. PVC ploče nemaju taj problem i sele se sa vlasnikom."}}
  ]
}
```

## Otvoreno pre implementacije
- [ ] Cene (`{{CENA_GARAZA_OD/DO}}`, `{{CENA_G18}}`, `{{CENA_G35}}`) #ceka-miroslav
- [ ] Before/after slike garaža ili autoservisa iz medijateke #ceka-miroslav
- [ ] Posle objave: Ads garaža-termini → ova landing (16,8k RSD je išlo u prazno) #ceka-miroslav
- [ ] Implementacija + Rich Results Test #claude-code

## Veze
- [[seo/plan-novih-stranica]] TIER 1 #3 · [[analiza/2026-07-04-ads-st-analiza-16m]] §garaže
- Sibling: [[dnevnik/2026-07-05-draft-industrijski-podovi-cena]] (isti proizvod, B2B)
