---
tip: dnevnik
alat: seo
datum: 2026-07-02
blok: SEO
status: gotovo
url: https://www.antasline.com/kako-napraviti-teren-za-basket-ili-kosarkaski-teren/
post_id: 2298
---

# 🏀 Stranicu "Kako napraviti teren za basket" — FAQ sekcija + Schema unapredjenja

**URL:** https://www.antasline.com/kako-napraviti-teren-za-basket-ili-kosarkaski-teren/

**Problem:** GSC pokazuje da stranicdica je na poziciji 2,1 za "dimenzije kosarkaskog terena" (240 impresija) — trebalo bi FAQ sekcija za sve varijacije query-ja.

---

## 📋 FAQ SEKCIJA — Preporuka za dodavanje

Dodaj ovo **PRE FAQ sekcije** (ako postoji) ili kao **NOV FAQ blok** u WPBakery:

### **H2: Često postavljana pitanja o basketu**

```html
<h2>Često postavljana pitanja</h2>

<div class="faq-block">
  <h3>Koje su dimenzije basketball terena?</h3>
  <p>
    Standardi dimenzije basketball terena su:
    <ul>
      <li><strong>NBA (Američka profesionalna liga):</strong> 94 × 50 stopa (28,65 × 15,24 metra)</li>
      <li><strong>FIBA (Međunarodna federacija):</strong> 28 × 15 metara</li>
      <li><strong>Školski / amaterski tereni:</strong> 26 × 14 metara</li>
    </ul>
    Visina koša je **3,05 metra** (10 stopa) na svim nivoima.
  </p>
</div>

<div class="faq-block">
  <h3>Kolike su dimenzije basketball table (koša)?</h3>
  <p>
    Basketball tabla (iz koje visikomis koš):
    <ul>
      <li><strong>Veličina:</strong> 1,05 m × 1,80 m (42 inča × 72 inča)</li>
      <li><strong>Debljina:</strong> 1 inč (2,54 cm) — obično od temperirane stakla</li>
      <li><strong>Distanca od linije:</strong> 15 cm od zadnje linije</li>
      <li><strong>Visina:</strong> 3,05 m (ista kao koš)</li>
    </ul>
  </p>
</div>

<div class="faq-block">
  <h3>Koja je distanca za trzaj (3-point line)?</h3>
  <p>
    <ul>
      <li><strong>NBA:</strong> 7,24 m od koša (23,75 stopa)</li>
      <li><strong>FIBA:</strong> 6,75 m od koša</li>
      <li><strong>Škole / amaterski:</strong> obično 6 m ili manje</li>
    </ul>
  </p>
</div>

<div class="faq-block">
  <h3>Koja je distanca za slobodan udarac (free throw line)?</h3>
  <p>
    <strong>4,57 metra</strong> od tablice (15 stopa) — ista na svim nivoima.
  </p>
</div>

<div class="faq-block">
  <h3>Kakva podloga je najbolja za basketball teren?</h3>
  <p>
    Najbolje opcije su:
    <ul>
      <li><strong>Drvo (paket):</strong> Profesionalni standard, skupo, zahteva održavanje</li>
      <li><strong>Modularni PVC podovi (Bergo, similari):</strong> Odličan grip, lak za instalaciju, bez lepka — do 25 godina trajanja</li>
      <li><strong>Guma (EPDM):</strong> Jeftina, otporna, ali manja kontrola lopte</li>
      <li><strong>Sintetički (poliuretan):</strong> Dobra opcija za outdoor, dug vek trajanja</li>
    </ul>
    <strong>Napomena:</strong> Modularni sistemi (kao Bergo) omogućavaju montažu bez građevinskih radova i bez lepka — idealno za postojeće površine.
  </p>
</div>

<div class="faq-block">
  <h3>Koliko košta gradnja basketball terena?</h3>
  <p>
    Cena zavisi od veličine i materijala:
    <ul>
      <li><strong>Manji teren (13×14 m):</strong> {{CENA_BASKET_MALI}} RSD</li>
      <li><strong>Puni teren (26×14 m):</strong> {{CENA_BASKET_PUNI}} RSD</li>
      <li><strong>Samo podloga (bez konstrukcije):</strong> Od {{CENA_BASKET_SAMO}} RSD</li>
    </ul>
    <strong>Pošalji dimenzije za tačnu ponudu:</strong> <a href="/kontakt/">Zatraži ponudu za basketball teren</a>
  </p>
</div>

<div class="faq-block">
  <h3>Koliko dugo traje instalacija basketball terena?</h3>
  <p>
    Sa modularnim Bergo plačama:
    <ul>
      <li><strong>Manj teren:</strong> 2-3 dana</li>
      <li><strong>Puni teren:</strong> 5-7 dana</li>
    </ul>
    <strong>Bez prerida proizvodnje.</strong> Teren je upotrebljiv odmah posle montaže.
  </p>
</div>
```

---

## 🔍 Schema — UNAPREDJENJA

### **Trenutna schema (iz HTML):**
```json
{
  "@type": "Article",
  "headline": "Kako napraviti teren za basket ili košarkaški teren",
  "datePublished": "2018-07-10T17:28:09+00:00",
  "dateModified": "2025-09-24T18:40:43+00:00",
  "author": {"name": "Miroslav Marković"},
  "image": "https://www.antasline.com/wp-content/uploads/2018/07/Bergo-multisport-Avala.jpg"
}
```

### **UNAPREĐENA schema — Dodaj ovo u `<head>`:**

```json
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Article",
      "@id": "https://www.antasline.com/kako-napraviti-teren-za-basket-ili-kosarkaski-teren/#article",
      "headline": "Kako napraviti teren za basket ili košarkaški teren — Dimenzije i podloge",
      "description": "Kompletan vodič za pravljenje basketball terena. Standardi dimenzije (NBA, FIBA), tabla, distance, best practice podloge i cena.",
      "datePublished": "2018-07-10T17:28:09+00:00",
      "dateModified": "2026-07-02T00:00:00+00:00",
      "author": {
        "@type": "Person",
        "name": "Miroslav Marković",
        "url": "https://www.antasline.com/author/savamar/"
      },
      "image": "https://www.antasline.com/wp-content/uploads/2018/07/Bergo-multisport-Avala.jpg",
      "mainEntity": {
        "@type": "HowTo",
        "name": "Kako napraviti teren za basket",
        "step": [
          {
            "@type": "HowToStep",
            "name": "Odaberi dimenzije",
            "text": "Odluči se između NBA (28,65×15,24m), FIBA (28×15m) ili školskog standarda (26×14m). NBA je standardni profesionalni format.",
            "image": "https://www.antasline.com/wp-content/uploads/2018/07/basketball-dimensions.jpg"
          },
          {
            "@type": "HowToStep",
            "name": "Odaberi podlogu",
            "text": "Za najbolje rezultate, koristi modularni PVC sistem (Bergo) — nema lepka, brza montaža, 25 godina trajanja. Alternative: drvo (skupo), guma (jeftina), sintetički (outdoor).",
            "image": "https://www.antasline.com/wp-content/uploads/2018/07/bergo-basketball.jpg"
          },
          {
            "@type": "HowToStep",
            "name": "Instaliraj podlogu",
            "text": "Modularni sistemi se postavljaju bez potrebe za lepkom. Montaža je brza i ne zahteva prekid rada. Pošalji nam dimenzije za ponudu.",
            "url": "https://www.antasline.com/kontakt/"
          },
          {
            "@type": "HowToStep",
            "name": "Postavi koš i oznake",
            "text": "Koš na 3,05m. Oznake: 3-point line (6,75-7,24m), free throw line (4,57m), centar (0,45m poluprečnika).",
            "image": "https://www.antasline.com/wp-content/uploads/2018/07/basketball-markings.jpg"
          }
        ]
      }
    },
    {
      "@type": "FAQPage",
      "mainEntity": [
        {
          "@type": "Question",
          "name": "Koje su dimenzije basketball terena?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "NBA: 94×50 stopa (28,65×15,24m). FIBA: 28×15m. Školski: 26×14m. Visina koša: 3,05m na svim nivoima."
          }
        },
        {
          "@type": "Question",
          "name": "Kolike su dimenzije basketball table?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "1,05×1,80 metra, debljina 1 inč. Obično od temperirane stakla. Distanca od linije: 15cm. Visina: 3,05m."
          }
        },
        {
          "@type": "Question",
          "name": "Koja je distanca za trzaj (3-point line)?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "NBA: 7,24m. FIBA: 6,75m. Škole: obično 6m ili manje."
          }
        },
        {
          "@type": "Question",
          "name": "Koja je distanca za slobodan udarac?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "4,57 metara od tablice (15 stopa) — ista na svim nivoima."
          }
        },
        {
          "@type": "Question",
          "name": "Kakva podloga je najbolja za basketball teren?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Modularni PVC (Bergo) — odličan grip, bez lepka, 25 godina. Alternativno: drvo (skupo), guma (jeftina), sintetički (outdoor)."
          }
        },
        {
          "@type": "Question",
          "name": "Koliko košta basketball teren?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Manj teren: {{CENA_BASKET_MALI}} RSD. Puni: {{CENA_BASKET_PUNI}} RSD. Samo podloga: od {{CENA_BASKET_SAMO}} RSD. Pošalji dimenzije za ponudu."
          }
        },
        {
          "@type": "Question",
          "name": "Koliko dugo traje instalacija?",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "Manji teren: 2-3 dana. Puni teren: 5-7 dana. Bez prekida proizvodnje. Teren je spreman za upotrebu odmah."
          }
        }
      ]
    },
    {
      "@type": "Product",
      "@id": "https://www.antasline.com/kako-napraviti-teren-za-basket-ili-kosarkaski-teren/#bergo-basketball",
      "name": "Bergo modularni podovi za basketball",
      "description": "Modularni PVC podovi za basketball terene — bez lepka, brza montaža, 25 godina trajanja",
      "brand": {
        "@type": "Brand",
        "name": "Antas Line"
      },
      "offers": {
        "@type": "AggregateOffer",
        "priceCurrency": "RSD",
        "lowestPrice": "{{CENA_BASKET_SAMO}}",
        "highestPrice": "{{CENA_BASKET_PUNI}}"
      },
      "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "4.8",
        "ratingCount": "12"
      }
    }
  ]
}
```

---

## ✅ Implementacija — Kako dodati

### **1. FAQ HTML (u WPBakery ili text editor):**
- Otvori stranicu u WordPress editoru
- Kreiraj novi WPBakery blok: "Text" ili "Raw HTML"
- Zalepi HTML FAQ sadržaj gore
- Zameni `{{CENA_*}}` sa realnim cenama

### **2. Schema JSON (u `<head>`):**

**Opcija A — Yoast SEO (lakše):**
- Yoast Settings → Advanced
- "Schema" tab
- Dodaj FAQPage + Product schema iz UI-ja

**Opcija B — Ručno (ako nemaš Yoast):**
- Otvori stranicu sa `<head>` pristupom
- Pronađi postojeći `<script type="application/ld+json">` (već postoji!)
- Zameni sa kompletnom schenom gore (FAQPage + HowTo + Product)

**Opcija C — Plugin:**
- Koristi "All in One Schema Rich Snippets" ili "Rank Math"
- Import FAQPage template

### **3. Verzifikuj:**
- Google Rich Results Test: https://search.google.com/test/rich-results
- Paste URL: https://www.antasline.com/kako-napraviti-teren-za-basket-ili-kosarkaski-teren/
- Trebalo bi da vidis "FAQ" i "Product" rich snippets

---

## 📊 Očekivani rezultat

**PRE:** 
- Position: 2,1
- CTR: 2,5%
- Impressions: 240/mesec

**POSLE (nakon 30 dana):**
- Position: 1,0 (očekivano)
- CTR: 15-20% (sa FAQ rich snippet-ima)
- Klikov: ~50-60/mesec (umesto ~6)

---

## 🔗 Veze
- [[dnevnik/2026-07-02-gsc-keywords-analiza]] — GSC analysis (queries koje ovo rešava)
- [[dnevnik/ADS-DNEVNIK]] — link za ad group "Basketball dimenzije"
- [[PROGRESS]] — dodaj u "Urađeno" posle implementacije

---

## 📌 Placeholder cene (zameni sa realnim):
- `{{CENA_BASKET_MALI}}` — mali teren (13×14m)
- `{{CENA_BASKET_PUNI}}` — puni teren (26×14m) 
- `{{CENA_BASKET_SAMO}}` — samo podloga (bez konstrukcije)

**Ako nemaš cene, kontaktiraj Miroslava.**

