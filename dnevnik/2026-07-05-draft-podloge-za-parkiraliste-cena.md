---
tip: dnevnik
alat: claude-code
datum: 2026-07-05
blok: C3
status: draft-gotov
url_predlog: /podloge-za-parkiraliste-cena/
---

# 🅿️ Nova stranica — `/podloge-za-parkiraliste-cena/` (plan #6, TIER 1, C3-orig)

> Izvor: [[seo/plan-novih-stranica]] TIER 1 #6 — "cena + nosivost + reference"
> Status: **sadržaj napisan, čeka implementaciju na lokalnom buildu.**
> Postojeća `/podloge-za-parkiraliste/` ostaje info/hub stranica (na njoj je i GA4 publika "Parking & spoljne podloge") — ova nova hvata **cena intent** (klaster CTR 11,5% ⭐, najviši od svih klastera).

## Ciljni upiti (GSC 16m)

| Upit | Impresije | Pozicija |
|---|---|---|
| podloga za parking | 1.105 | 1,5 ⭐ |
| plastika za parking | 377 | 1,3 ⭐ |
| podloge za parking | 315 | 1,1 ⭐ |
| ploce/ploče/pločice za parking | ~570 | 6,3–8,8 |
| plasticne podloge za parking | 182 | 1,1 |
| plasticno sace za parking | 87 | 3,5 |
| šljunak/sljunak/tucanik za parking | ~700 | 5,9–7,0 |

Top upiti već poz. 1 sa odličnim CTR — cena stranica konvertuje taj saobraćaj, a "šljunak/tucanik" upiti se hvataju poređenjem (saće vs nasip).

## Predlog sadržaja

### Yoast SEO naslov (60)
`Podloge za parking — cena po m², plastično saće | Antas Line`

### Yoast meta opis (155)
`Cena plastičnih podloga (saća) za parking po m². Nosivost i do 200 t/m², ugradnja bez betoniranja, upija vodu — bez blata i barica. Zatražite ponudu odmah.`

### H1
`Podloge za parkiralište — cena po m² i nosivost`

### Uvod
Cena odmah: `{{CENA_PARKING_OD}}`–`{{CENA_PARKING_DO}}` RSD/m² (saće + ugradnja opciono). Jedna rečenica šta je: plastične rešetke (saće) koje se pune šljunkom ili travom — parking bez betona i bez blata.

### H2: Cena po varijanti (tabela)

| Varijanta | Cena RSD/m² | Napomena |
|---|---|---|
| Saće + ispuna šljunkom | `{{CENA_SACE_SLJ_OD}}`–`{{CENA_SACE_SLJ_DO}}` | najčešći izbor |
| Saće + travnata ispuna | `{{CENA_SACE_TR_OD}}`–`{{CENA_SACE_TR_DO}}` | zeleni parking |
| Samo ploče (materijal) | `{{CENA_SACE_MAT}}` | za samostalnu ugradnju |

+ priprema terena (tampon sloj) — posebna stavka ili uključeno? #ceka-miroslav

### H2: Nosivost i tehničke karakteristike
- nosivost do `{{NOSIVOST}}` t/m² (potvrditi iz spec-a proizvoda — putnička vozila, kamioni, vatrogasni pristup)
- propušta vodu → nema barica, blata, pucanja od mraza (vs beton/asfalt)
- UV stabilnost, radna temperatura
- brzina ugradnje: `{{M2_DAN}}` m²/dan, bez građevinske dozvole u većini slučajeva

### H2: Saće ili šljunak/tucanik nasut direktno? (hvata ~700 impr šljunak upita)
Poređenje: sam šljunak se razvlači, prave se kolotragovi, dosipava se svake godine; saće drži šljunak na mestu — trajno rešenje za istu ili nižu 5-godišnju cenu. Tabela troška 5 godina: nasip vs saće.

### H2: Reference (trust)
Izvedeni parkinzi — slike + kvadratura + namena (firma/restoran/kuća). #ceka-miroslav — koje reference smemo javno

### H2 + CTA
- forma "pošaljite kvadraturu i namenu (auto/kamion)" → `/hvala-za-poruku/`
- telefon **069 234 00 72**

### Interni linkovi
- → `/podloge-za-parkiraliste/` (info hub — uzajamno, "detaljne tehničke specifikacije")
- → `/spoljnje-podne-obloge/` (spoljne površine silo)
- → [[dnevnik/2026-07-05-draft-gumeni-podovi-za-terase-cena|/gumeni-podovi-za-terase-cena/]] (sibling spoljna cena stranica)
- `/industrijski-podovi/` — samo ako se pominje parking za viljuškare/magacinski krug, inače izostaviti

## FAQ blok (HTML)

```html
<h2>Često postavljana pitanja o podlogama za parking</h2>

<div class="faq-block">
  <h3>Kolika je cena podloge za parking po m²?</h3>
  <p>Plastično saće sa šljunkom košta {{CENA_SACE_SLJ_OD}}–{{CENA_SACE_SLJ_DO}}
  RSD/m² sa ugradnjom. Samo materijal (ploče) košta {{CENA_SACE_MAT}} RSD/m².
  Tačna cena zavisi od kvadrature i pripreme terena.</p>
</div>

<div class="faq-block">
  <h3>Koliku težinu izdržava plastično saće?</h3>
  <p>Kvalitetne rešetke nose i teretna vozila — nosivost je do {{NOSIVOST}} t/m²,
  što je dovoljno za automobile, kombije i kamione.</p>
</div>

<div class="faq-block">
  <h3>Da li je bolje saće ili samo nasuti šljunak?</h3>
  <p>Nasut šljunak se razvlači i prave se kolotragovi, pa se dosipava svake
  godine. Saće drži šljunak na mestu i površina ostaje ravna godinama — na
  period od 5 godina ukupan trošak je isti ili niži.</p>
</div>

<div class="faq-block">
  <h3>Treba li betoniranje ili građevinska dozvola?</h3>
  <p>Ne — saće se postavlja na pripremljen tampon sloj, bez betona. Voda se
  upija u zemlju, pa nema ni problema sa odvodnjavanjem.</p>
</div>

<div class="faq-block">
  <h3>Može li travnati parking?</h3>
  <p>Da — iste rešetke se pune zemljom i seju travom: zeleni parking koji
  izdržava vozila bez kolotraga.</p>
</div>
```

## FAQPage schema (JSON-LD)

```json
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {"@type": "Question", "name": "Kolika je cena podloge za parking po m²?",
     "acceptedAnswer": {"@type": "Answer", "text": "Plastično saće sa šljunkom košta {{CENA_SACE_SLJ_OD}}–{{CENA_SACE_SLJ_DO}} RSD/m² sa ugradnjom. Samo materijal košta {{CENA_SACE_MAT}} RSD/m²."}},
    {"@type": "Question", "name": "Koliku težinu izdržava plastično saće?",
     "acceptedAnswer": {"@type": "Answer", "text": "Nosivost je do {{NOSIVOST}} t/m² — dovoljno za automobile, kombije i kamione."}},
    {"@type": "Question", "name": "Da li je bolje saće ili samo nasuti šljunak?",
     "acceptedAnswer": {"@type": "Answer", "text": "Nasut šljunak se razvlači i dosipava svake godine. Saće drži šljunak na mestu — na 5 godina ukupan trošak je isti ili niži."}},
    {"@type": "Question", "name": "Treba li betoniranje ili građevinska dozvola?",
     "acceptedAnswer": {"@type": "Answer", "text": "Ne — saće se postavlja na tampon sloj bez betona, a voda se upija u zemlju."}},
    {"@type": "Question", "name": "Može li travnati parking?",
     "acceptedAnswer": {"@type": "Answer", "text": "Da — rešetke se pune zemljom i seju travom: zeleni parking bez kolotraga."}}
  ]
}
```

## Otvoreno pre implementacije
- [ ] Cene (5 placeholder-a) + nosivost iz spec-a + m²/dan ugradnje #ceka-miroslav
- [ ] Reference koje smeju javno (slike + lokacije) #ceka-miroslav
- [ ] Da li tampon priprema ulazi u cenu #ceka-miroslav
- [ ] Implementacija + Rich Results Test #claude-code

## Veze
- [[seo/plan-novih-stranica]] TIER 1 #6 · [[analiza/2026-07-04-gsc-kw-analiza-16m]] (klaster CTR 11,5%)
- Sibling: [[dnevnik/2026-07-05-draft-gumeni-podovi-za-terase-cena]] (spoljne cena stranice)
