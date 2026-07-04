---
tip: dnevnik
alat: claude-code
datum: 2026-07-04
blok: C3
status: draft-gotov
url_predlog: /gumeni-podovi-za-terase-cena/
---

# 🏡 Nova stranica — `/gumeni-podovi-za-terase-cena/` (plan #1, TIER 1)

> Izvor: [[seo/plan-novih-stranica]] TIER 1 #1 (= C3-orig "terasa cena"). Status: **draft gotov, čeka implementaciju na lokalnom buildu.**

## Ciljni upiti (16m, iz CSV banke)

| Upit | Impresije | Poz. | CTR |
|---|---|---|---|
| gumeni podovi za terase cena | **4.221** | 4,0 | 5,9% |
| gumeni podovi za terase | 1.358 | 5,3 | 5,6% |
| gumeni tepih za terasu | ~1.500 (645/90d) | 1,1 | 6,5% |
| gumene podloge za terasu | ~1.200 (538/90d) | 1,6 | 4,3% |
| podne obloge za terasu cena | 204 | 7,5 | 2,9% |
| + gumene ploče/podne obloge za terase var. | ~400 | 3–8 | — |

**Zbir ~9k impresija.** Ads paralelno troši na iste termine ("gumeni podovi za terase cena" 867 RSD + pvc var. 1.422 RSD/16m, 0 konv) — ova stranica smanjuje CPC zavisnost.

## Zašto posebna stranica
- Najveći pojedinačni cena-upit sajta (4.221 impr) nema cena odgovor — landing je kategorija bez cifara
- Cena-upiti sa top 3 pozicijom na sajtu imaju CTR 20–30% (benchmark iz [[analiza/2026-07-04-gsc-kw-analiza-16m]] §2) — ovde smo poz. 4 sa 5,9%

## Predlog sadržaja

### Yoast SEO naslov (59 znakova)
`Gumeni podovi za terase — cena po m² i vrste | Antas Line`

### Yoast meta opis (156 znakova)
`Koliko koštaju gumeni podovi za terasu? Cene po m² za modularne Bergo ploče, gumene podloge i veštačku travu. Montaža bez lepka. Ponuda za 24h — pošalji m².`

### H1
`Gumeni podovi za terase — cena po m² i kompletan vodič`

### Uvod (direktan odgovor u 1. rečenici — GEO pravilo)
"Gumeni i modularni podovi za terase koštaju od `{{CENA_TERASA_OD}}` do `{{CENA_TERASA_DO}}` RSD/m², zavisno od tipa i debljine." + 2 rečenice konteksta.

### H2: Cene po tipu podloge (tabela — srce stranice)
| Tip | Cena/m² | Za koga |
|---|---|---|
| Bergo modularne ploče (klik) | `{{CENA_BERGO_OD}}`–`{{CENA_BERGO_DO}}` | terase, bazeni, spa |
| Gumene podloge/ploče | `{{CENA_GUMA_OD}}`–`{{CENA_GUMA_DO}}` | dvorišta, staze |
| Veštačka trava za terase | `{{CENA_TRAVA_OD}}`–`{{CENA_TRAVA_DO}}` | terase, krovovi |

+ napomena šta ulazi u cenu (materijal/isporuka/montaža — montaža klik-sistema često nije ni potrebna)

### H2: Šta utiče na cenu
Površina (popust na m²?), priprema podloge (ravan beton = nula pripreme), ivičnjaci/lajsne, boja/model (XL vs Unique vs Elite)

### H2: Zašto modularne ploče umesto lepljenih obloga
Bez lepka i radova · demontaža/selidba · drenaža (voda prolazi) · UV postojanost · mraz — sve već potvrđeno u RSA banci ([[dnevnik/ADS-DNEVNIK]])

### H2: Kalkulator / ponuda
"Pošalji dimenzije terase (dužina × širina) — ponuda istog dana." → forma + telefon **072**

### Interni linkovi
- → `/spoljnje-podne-obloge/` (glavna kategorija) + Bergo XL / Unique / Elite pod-stranice
- → `/vestacka-trava/` i `/spoljnje-podne-obloge/vestacka-trava-za-terase/`
- → `/spoljnje-podne-obloge/podovi-za-bazene/` (srodna namena)
- → NE ka /industrijski-podovi/ (nerelevantno ovde)

## FAQ blok (HTML)

```html
<h2>Često postavljana pitanja o ceni podova za terase</h2>

<div class="faq-block">
  <h3>Koliko košta gumeni pod za terasu po kvadratu?</h3>
  <p>Od <strong>{{CENA_TERASA_OD}} do {{CENA_TERASA_DO}} RSD/m²</strong>, zavisno od tipa:
  modularne Bergo ploče {{CENA_BERGO_OD}}–{{CENA_BERGO_DO}}, gumene podloge
  {{CENA_GUMA_OD}}–{{CENA_GUMA_DO}}, veštačka trava {{CENA_TRAVA_OD}}–{{CENA_TRAVA_DO}} RSD/m².</p>
</div>

<div class="faq-block">
  <h3>Da li je u cenu uračunata montaža?</h3>
  <p>Bergo klik-sistem se postavlja bez lepka i alata — montažu najčešće radite sami za par
  sati. Za veće površine i veštačku travu nudimo montažu; cena zavisi od površine.</p>
</div>

<div class="faq-block">
  <h3>Može li se postaviti preko starih pločica ili betona?</h3>
  <p>Da — modularne ploče idu direktno preko ravne postojeće podloge, bez rušenja i
  građevinskih radova. Voda se drenira kroz ploče.</p>
</div>

<div class="faq-block">
  <h3>Kako da dobijem tačnu ponudu?</h3>
  <p>Pošaljite dimenzije terase (dužina × širina) kroz <a href="/kontakt/">formu</a> ili
  pozovite 069/234-0072 — ponudu šaljemo istog dana, uzorak na zahtev.</p>
</div>
```

## FAQPage schema (JSON-LD)

```json
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {"@type": "Question", "name": "Koliko košta gumeni pod za terasu po kvadratu?",
     "acceptedAnswer": {"@type": "Answer", "text": "Od {{CENA_TERASA_OD}} do {{CENA_TERASA_DO}} RSD/m², zavisno od tipa: modularne Bergo ploče, gumene podloge ili veštačka trava."}},
    {"@type": "Question", "name": "Da li je u cenu uračunata montaža?",
     "acceptedAnswer": {"@type": "Answer", "text": "Bergo klik-sistem se postavlja bez lepka i alata — montažu najčešće radite sami. Za veće površine nudimo montažu."}},
    {"@type": "Question", "name": "Može li se postaviti preko starih pločica ili betona?",
     "acceptedAnswer": {"@type": "Answer", "text": "Da — modularne ploče idu direktno preko ravne postojeće podloge, bez rušenja. Voda se drenira kroz ploče."}},
    {"@type": "Question", "name": "Kako da dobijem tačnu ponudu?",
     "acceptedAnswer": {"@type": "Answer", "text": "Pošaljite dimenzije terase kroz formu ili pozovite 069/234-0072 — ponuda istog dana."}}
  ]
}
```

## Otvoreno pre implementacije
- [ ] Cifre: `{{CENA_TERASA_OD/DO}}`, `{{CENA_BERGO_OD/DO}}`, `{{CENA_GUMA_OD/DO}}`, `{{CENA_TRAVA_OD/DO}}` #ceka-miroslav
- [ ] Implementacija na lokalnom buildu (WPBakery + Yoast + schema) #claude-code
- [ ] Meta refresh `/spoljnje-podne-obloge/` da ne kanibalizuje ("gumeni tepih za terasu" ostaje njen upit — snapshot §6.1) #claude-code

## Veze
- [[seo/plan-novih-stranica]] TIER 1 #1 · [[analiza/2026-07-04-gsc-kw-analiza-16m]] §2 · [[analiza/2026-07-04-ads-st-analiza-16m]] §4 (dupla zavisnost terase)
