---
tip: dnevnik
alat: claude-code
datum: 2026-07-04
blok: C3
status: draft-gotov
url_predlog: /dimenzije-kosarkaske-table/
---

# 🏀 Nova stranica — `/dimenzije-kosarkaske-table/` (plan #5, TIER 1)

> Izvor: [[seo/plan-novih-stranica]] TIER 1 #5 — "merge svih tabla upita + link ka konstrukcijama (proizvodi!)"
> Status: **sadržaj napisan, čeka implementaciju na lokalnom buildu** (ova sesija radi u vault-u na hosting-u, bez pristupa lokalnom Windows/XAMPP build-u ni pravu da dira live — [[CLAUDE]] §8).

## Ciljni upiti (iz [[analiza/2026-07-04-gsc-kw-analiza-16m]] / CSV banke)

| Upit | Impresije (16m) | Trenutna pozicija |
|---|---|---|
| tabla za kos dimenzije | 1.086 | 1,01 |
| kosarkaska tabla dimenzije | 752 | 1,08 |
| košarkaška tabla dimenzije | 638 | 1,04 |
| tabla za košarku dimenzije | 352 | 1,03 |
| tabla za kos uradi sam | 484 | 3,54 |
| tabla za kosarku / tabla za košarku (var.) | ~130 | 16–18 |
| + ~15 manjih varijanti (koš i tabla, obruč i tabla, tabla cena...) | ~150 | razno |

**Ukupno ~2.400 impresija, prosečna pozicija već 1–3.5 na varijante sa najviše prometa — problem je CTR/pokrivenost, ne rangiranje.** Nema posvećene stranice; ovi upiti se verovatno delom hvataju na postojećem članku "Kako napraviti teren za basket" ([[dnevnik/2026-07-02-basket-page-faq-schema]]), koji ima generičku tabla-FAQ stavku ali ne linkuje ka proizvodima.

## Zašto posebna stranica (ne samo FAQ na postojećoj)

- "tabla za kos uradi sam" (484 impr, poz. 3,5) je jasna DIY namera — prilika da se ponudi gotova konstrukcija umesto da posetilac ode i sam kupuje delove
- Postojeći članak cilja teren u celini (dimenzije terena, linije) — razblažen fokus za "tabla"-specifične upite
- Ovde je smisleno mesto za direktan link ka proizvod-kategoriji **Košarkaške konstrukcije** (5 proizvoda) — postojeći članak to ne radi

---

## Predlog sadržaja stranice

### Yoast SEO naslov (58 znakova)
`Dimenzije table za košarku — koš, visina, cena | Antas Line`

### Yoast meta opis (154 znaka)
`Kolike su dimenzije table za košarku (koša)? NBA/FIBA standard, visina montaže, staklo vs akril, uradi sam ili gotova konstrukcija — sve na jednom mestu.`

### H1
`Dimenzije table za košarku — kompletan vodič`

### Uvod (kratak, 2-3 rečenice)
Kratko definiše šta stranica pokriva: standardne dimenzije, materijal, visina montaže, DIY vs gotova konstrukcija, i vodi ka ponudi.

### H2: Standardne dimenzije table za košarku
- **Širina × visina:** 1,80 m × 1,05 m (isti standard za FIBA i NBA)
- **Visina obruča (koša):** 3,05 m od poda
- **Donja ivica table:** 2,90 m od poda
- **Rastojanje table od end-line linije:** 1,20 m
- Školski/rekreativni tereni ponekad koriste manju tablu (1,20 m × 0,90 m) — napomenuti kao varijantu

### H2: Od čega se pravi tabla za košarku
- **Kaljeno (sigurnosno) staklo** — profesionalni standard, najbolji odboj lopte, otporno na vremenske uslove (outdoor)
- **Akril/pleksiglas** — lakše, jeftinije, rekreativni tereni i školska dvorišta
- **Čelični ram** — nosač table, dimenzionisan da izdrži udarce i vetar (outdoor konstrukcije)

### H2: Tabla za kos "uradi sam" — šta uzeti u obzir
Adresira DIY upit (484 impr) — ne odbija ga, nego edukuje i pozicionira gotovu konstrukciju kao bržu/sigurniju opciju:
- Nosivost rama i sidrenje u temelj (najčešća greška kod samogradnje — tabla vremenom "igra" ili se nagne)
- Ujednačenost visine i ugla (bitno za fer igru, posebno ako se koristi za treninge)
- Vremenska izloženost (UV, vlaga) kod materijala koji nisu za spoljnu upotrebu
- **Prelaz:** gotova modularna konstrukcija (koš + tabla + noseći ram) montira se za 1 dan, sa garantovanim dimenzijama — link ka kategoriji proizvoda

### H2: Koliko košta tabla za košarku / kompletna konstrukcija
- Samo tabla (zamena): od `{{CENA_TABLA_SAMO}}` RSD
- Kompletna konstrukcija (ram + tabla + koš): `{{CENA_KONSTRUKCIJA_OD}}`–`{{CENA_KONSTRUKCIJA_DO}}` RSD
- CTA: "Pošalji nam lokaciju i tip terena (indoor/outdoor) za tačnu ponudu" → forma / telefon **072**

### Interni linkovi (obavezno)
- → kategorija proizvoda **Košarkaške konstrukcije** (predlog URL na redizajnu: `/kategorija/kosarkaske-konstrukcije/` — **treba potvrditi finalni slug**, zavisi od BLOK C1 odluke o `/sportske-podloge/kosarkaske-konstrukcije/` redirect cilju, videti [[blokovi/BLOK-C-sledece]])
- → [[dnevnik/2026-07-02-basket-page-faq-schema|članak "Kako napraviti teren za basket"]] (dimenzije terena — sibling stranica, uzajamni link)
- → `/industrijski-podovi/` NIJE relevantno ovde (izostaviti — nema smisla forsirati taj link samo zbog pravila iz šablona)

---

## FAQ blok (HTML, za WPBakery)

```html
<h2>Često postavljana pitanja o tabli za košarku</h2>

<div class="faq-block">
  <h3>Koje su dimenzije table za košarku?</h3>
  <p>Standardna tabla (FIBA i NBA) je <strong>1,80 m široka i 1,05 m visoka</strong>.
  Školske/rekreativne table su često manje, oko 1,20 m × 0,90 m.</p>
</div>

<div class="faq-block">
  <h3>Na kojoj visini se montira tabla za koš?</h3>
  <p>Obruč (koš) je na <strong>3,05 m</strong> od poda na svim nivoima takmičenja.
  Donja ivica table je na 2,90 m, a tabla je udaljena 1,20 m od end-line linije.</p>
</div>

<div class="faq-block">
  <h3>Staklena ili akrilna tabla — šta izabrati?</h3>
  <p>Kaljeno staklo daje najbolji odboj i profesionalni je standard, ali je skuplje
  i teže za montažu. Akril/pleksiglas je lakši i jeftiniji, dobar izbor za
  rekreativne i školske terene.</p>
</div>

<div class="faq-block">
  <h3>Mogu li sam da napravim/montiram tablu za koš?</h3>
  <p>Moguće je, ali najčešće greške su nedovoljno sidrenje rama i neujednačena
  visina/ugao table. Gotova modularna konstrukcija (ram + tabla + koš) montira
  se za 1 dan sa garantovanim dimenzijama — <a href="/kontakt/">zatraži ponudu</a>.</p>
</div>

<div class="faq-block">
  <h3>Koliko košta tabla za košarku ili kompletna konstrukcija?</h3>
  <p>Samo tabla (zamena): od {{CENA_TABLA_SAMO}} RSD. Kompletna konstrukcija
  (ram + tabla + koš): {{CENA_KONSTRUKCIJA_OD}}–{{CENA_KONSTRUKCIJA_DO}} RSD,
  zavisno od tipa terena (indoor/outdoor).</p>
</div>
```

## FAQPage schema (JSON-LD)

```json
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {
      "@type": "Question",
      "name": "Koje su dimenzije table za košarku?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Standardna tabla (FIBA i NBA) je 1,80 m široka i 1,05 m visoka. Školske/rekreativne table su često manje, oko 1,20 m × 0,90 m."
      }
    },
    {
      "@type": "Question",
      "name": "Na kojoj visini se montira tabla za košarku?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Obruč je na 3,05 m od poda. Donja ivica table je na 2,90 m, a tabla je udaljena 1,20 m od end-line linije."
      }
    },
    {
      "@type": "Question",
      "name": "Staklena ili akrilna tabla — šta izabrati?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Kaljeno staklo daje najbolji odboj i profesionalni je standard. Akril/pleksiglas je lakši i jeftiniji, dobar za rekreativne i školske terene."
      }
    },
    {
      "@type": "Question",
      "name": "Mogu li sam da napravim/montiram tablu za koš?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Moguće je, ali najčešće greške su nedovoljno sidrenje rama i neujednačena visina/ugao table. Gotova modularna konstrukcija montira se za 1 dan sa garantovanim dimenzijama."
      }
    },
    {
      "@type": "Question",
      "name": "Koliko košta tabla za košarku ili kompletna konstrukcija?",
      "acceptedAnswer": {
        "@type": "Answer",
        "text": "Samo tabla (zamena): od {{CENA_TABLA_SAMO}} RSD. Kompletna konstrukcija (ram + tabla + koš): {{CENA_KONSTRUKCIJA_OD}}–{{CENA_KONSTRUKCIJA_DO}} RSD, zavisno od tipa terena."
      }
    }
  ]
}
```

---

## Otvoreno pre implementacije

- [ ] `{{CENA_TABLA_SAMO}}`, `{{CENA_KONSTRUKCIJA_OD}}`, `{{CENA_KONSTRUKCIJA_DO}}` — cifre od Miroslava #ceka-miroslav
- [ ] Finalni slug kategorije "Košarkaške konstrukcije" na redizajnu (zavisi od C1 redirect odluke) #ceka-miroslav
- [ ] Implementacija na lokalnom buildu (WPBakery stranica + Yoast + schema u head) — sledeći put kad je lokal dostupan #claude-code
- [ ] Posle implementacije: Rich Results Test + štiklirati u [[seo/plan-novih-stranica]] + red u [[PROGRESS]]

## Veze
- [[seo/plan-novih-stranica]] — TIER 1 #5
- [[dnevnik/2026-07-02-basket-page-faq-schema]] — sibling stranica (dimenzije terena, #4 u planu)
- [[blokovi/BLOK-C-sledece]] — C3 hub + C1 zavisnost (slug konstrukcija)
- [[analiza/2026-07-04-gsc-kw-analiza-16m]] — izvor upita
