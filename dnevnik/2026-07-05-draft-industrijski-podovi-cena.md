---
tip: dnevnik
alat: claude-code
datum: 2026-07-05
blok: C3
status: draft-gotov
url_predlog: /industrijski-podovi-cena/
---

# 🏭 Nova stranica — `/industrijski-podovi-cena/` (plan #2, TIER 1)

> Izvor: [[seo/plan-novih-stranica]] TIER 1 #2 — "cena/m² po debljini, poređenje sa epoksidom (conquest ugao), B2B forma"
> Status: **sadržaj napisan, čeka implementaciju na lokalnom buildu.**
> **Odluka o formatu:** posebna stranica (ne cena sekcija na `/industrijski-podovi/`) — post ID 4937 je blokiran WPBakery JS bugom ([[CLAUDE]] §7.3), a posebna cena stranica može da gađa i Ads landing za cena-termine. Kad se 4937 odblokira, dodati tamo kratku cena sekciju + link ovamo.

## Ciljni upiti (GSC 16m)

| Upit | Impresije | Pozicija |
|---|---|---|
| industrijski podovi cena po m2 | 841 | 8,7 |
| industrijski podovi cena | 470 | 7,4 |
| industrijski pod cena | 200 | 8,0 |
| industrijski pvc podovi cena | 129 | 7,7 |

**+ Ads efekat:** "industrijski podovi cena po m2" i srodni cena-termini = **4,1k RSD potrošeno bez konverzije** jer landing nema cenu ([[analiza/2026-07-04-ads-st-analiza-16m]]). Ova stranica postaje Ads landing za te termine → rešava oba kanala. Cena upiti = 19% Ads potrošnje.

## Predlog sadržaja

### Yoast SEO naslov (57)
`Industrijski podovi — cena po m² (PVC ploče) | Antas Line`

### Yoast meta opis (156)
`Koliko košta industrijski pod? Cena Ecotile PVC ploča po m² i debljini, poređenje sa epoksidom, ugradnja bez zatvaranja pogona. Zatražite B2B ponudu danas.`

### H1
`Cena industrijskog poda po m² — Ecotile PVC ploče`

### Uvod
Odmah raspon: `{{CENA_IND_OD}}`–`{{CENA_IND_DO}}` RSD/m² zavisno od debljine i kvadrature. Jedna rečenica: cena uključuje materijal, a ugradnja ne zahteva zatvaranje pogona (ključni B2B argument).

### H2: Cena po debljini ploče (tabela)

| Debljina | Namena | Cena RSD/m² |
|---|---|---|
| 5 mm | lakši saobraćaj, magacini | `{{CENA_5MM_OD}}`–`{{CENA_5MM_DO}}` |
| 7 mm | standardna proizvodnja, viljuškari | `{{CENA_7MM_OD}}`–`{{CENA_7MM_DO}}` |
| 10/12 mm | težak transport, neravna podloga | `{{CENA_10MM_OD}}`–`{{CENA_10MM_DO}}` |

*(Debljine uskladiti sa stvarnim Ecotile asortimanom na buildu — proveriti pre objave.)*

### H2: Šta utiče na konačnu cenu
- kvadratura (rabat na veće površine — B2B signal)
- stanje postojeće podloge (Ecotile ide preko oštećenog betona bez sanacije — ušteda vs izlivanje)
- ESD/antistatik varijante = viša cena, posebna namena
- vođenje viljuškarskog saobraćaja, obeležavanje traka (žute/crvene ploče)

### H2: Ecotile ili epoksid — šta se više isplati? (conquest ugao)
Tabela poređenja na 10 godina: epoksid niža početna cena ali zahteva pripremu betona, zatvaranje pogona 3–7 dana, reaplikaciju na 3–5 god; Ecotile viša početna, bez downtime-a, 25 god garancije, seli se sa firmom. → link ka `/epoksidni-podovi-ili-ecotile-podovi/` za pun tekst. **Ne prodajemo epoksid — sekcija konvertuje epoksid tražnju.**

### H2: Kako izgleda ugradnja (trust)
- bez lepka, bez mirisa, pogon radi tokom ugradnje
- ~200–400 m²/dan montaže (potvrditi cifru)
- reference: Hankook, HTEC, Amicus (galerija kad stignu slike)

### H2 + CTA: Zatražite B2B ponudu
- forma: kvadratura + delatnost + stanje podloge → `/hvala-za-poruku/`
- telefon **069 234 00 72** · email office@antasline.com (mailto sa `?subject=Ponuda industrijski pod`)

### Interni linkovi
- → `/industrijski-podovi/` (glavni silo — obavezno, treba mu juice, poz. 11)
- → `/epoksidni-podovi-ili-ecotile-podovi/` (conquest)
- → kategorija Ecotile proizvoda (`/kategorija/...` po C1 odluci)

## FAQ blok (HTML)

```html
<h2>Često postavljana pitanja o ceni industrijskih podova</h2>

<div class="faq-block">
  <h3>Kolika je cena industrijskog poda po m²?</h3>
  <p>Ecotile PVC ploče koštaju {{CENA_IND_OD}}–{{CENA_IND_DO}} RSD/m² zavisno od
  debljine (5–12 mm) i kvadrature. Za veće površine odobravamo rabat —
  zatražite ponudu sa tačnom kvadraturom.</p>
</div>

<div class="faq-block">
  <h3>Da li je u cenu uključena i ugradnja?</h3>
  <p>{{ODGOVOR_UGRADNJA — Miroslav: da li se montaža naplaćuje posebno?}}
  Ugradnja ne zahteva lepak ni zatvaranje pogona — proizvodnja radi normalno.</p>
</div>

<div class="faq-block">
  <h3>Šta je jeftinije — epoksid ili PVC ploče?</h3>
  <p>Epoksid ima nižu početnu cenu, ali zahteva pripremu betona, zaustavljanje
  pogona nekoliko dana i obnavljanje premaza na 3–5 godina. Ecotile ploče imaju
  višu početnu cenu, ali traju 25 godina bez obnavljanja i mogu da se demontiraju
  i presele. Na period od 10 godina PVC ploče su isplativije.</p>
</div>

<div class="faq-block">
  <h3>Mora li se stari beton sanirati pre postavljanja?</h3>
  <p>Ne — Ecotile ploče se postavljaju direktno preko oštećenog ili prašnjavog
  betona, bez izlivanja mase za nivelisanje. To je najveća ušteda u odnosu na
  klasične sisteme.</p>
</div>

<div class="faq-block">
  <h3>Izdržava li PVC pod viljuškare?</h3>
  <p>Da — ploče od 7 mm i deblje su predviđene za viljuškarski saobraćaj i teški
  industrijski transport. Koriste ih pogoni poput Hankook-a i HTEC-a.</p>
</div>
```

## FAQPage schema (JSON-LD)

```json
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {"@type": "Question", "name": "Kolika je cena industrijskog poda po m²?",
     "acceptedAnswer": {"@type": "Answer", "text": "Ecotile PVC ploče koštaju {{CENA_IND_OD}}–{{CENA_IND_DO}} RSD/m² zavisno od debljine (5–12 mm) i kvadrature. Za veće površine odobravamo rabat."}},
    {"@type": "Question", "name": "Da li je u cenu uključena i ugradnja?",
     "acceptedAnswer": {"@type": "Answer", "text": "{{ODGOVOR_UGRADNJA}} Ugradnja ne zahteva lepak ni zatvaranje pogona."}},
    {"@type": "Question", "name": "Šta je jeftinije — epoksid ili PVC ploče?",
     "acceptedAnswer": {"@type": "Answer", "text": "Epoksid ima nižu početnu cenu, ali traži pripremu betona, zaustavljanje pogona i obnavljanje na 3–5 godina. Ecotile traje 25 godina bez obnavljanja — na 10 godina PVC ploče su isplativije."}},
    {"@type": "Question", "name": "Mora li se stari beton sanirati pre postavljanja?",
     "acceptedAnswer": {"@type": "Answer", "text": "Ne — Ecotile ploče se postavljaju direktno preko oštećenog betona, bez mase za nivelisanje."}},
    {"@type": "Question", "name": "Izdržava li PVC pod viljuškare?",
     "acceptedAnswer": {"@type": "Answer", "text": "Da — ploče od 7 mm i deblje su predviđene za viljuškare i težak transport."}}
  ]
}
```

## Otvoreno pre implementacije
- [ ] Cene po debljini (6 placeholder-a) + odgovor o naplati ugradnje #ceka-miroslav
- [ ] Potvrditi Ecotile debljine u asortimanu (5/7/10/12 mm?) na buildu #claude-code
- [ ] Posle objave: preusmeriti Ads cena-termine na ovu stranicu (final URL) #ceka-miroslav
- [ ] Implementacija + Rich Results Test #claude-code

## Veze
- [[seo/plan-novih-stranica]] TIER 1 #2 · [[analiza/2026-07-04-ads-st-analiza-16m]] (4,1k RSD curenje)
- Sibling: [[dnevnik/2026-07-05-draft-podovi-za-garaze]] (isti Ecotile proizvod, B2C ugao)
