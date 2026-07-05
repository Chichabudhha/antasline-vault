---
tip: dnevnik
alat: claude-code
datum: 2026-07-05
blok: C3
status: primenjeno-delimicno
url: /podloga-za-odbojkaske-terene/
lokacija: SAMO-LIVE
---

# 🏐 Refresh `/podloga-za-odbojkaske-terene/` (plan #9, TIER 2)

> Izvor: [[seo/plan-novih-stranica]] TIER 2 #9 — "najjeftinija pobeda — title/meta+FAQ+cena, ~30 min"
> ⚠️ **Stranica postoji SAMO na live sajtu** (nema je na lokalnom buildu — C2 parity gap, dodati na parity listu!). Live se ne dira ([[CLAUDE]] §8) → ovaj paket primenjuje **Miroslav kroz cPanel/WP admin** (`[cpanel-live]`) ili ulazi u C2 migraciju. Sve ispod je copy-paste spremno.

## Dijagnoza (zašto CTR 0,6% na poziciji 2,3)

Live stanje (provereno 2026-07-05):
- Title: `Podloga za terene za odbojku - Antas line doo` — **nema "dimenzije"**, a dimenzije-upiti su ~80% klastera (7.817 impr)
- **Meta description: ne postoji** — Google sam izvlači snippet
- Nema FAQ, nema cene, nema odbojke na pesku (~330 impr, poz. 1,5–2,8, već rangiramo!)
- Sadržaj je dobar: dimenzije 18×9 ✓, mreža 2,43/2,24 ✓, stubovi ✓, podloge (vinil/Bergo) ✓

| Top upiti | Impr | Poz. |
|---|---|---|
| dimenzije odbojkaškog terena (sve var.) | ~4.900 | 1,1–2,3 |
| teren za odbojku / odbojkaski teren | ~1.550 | 1,2–6,4 |
| odbojka na pesku dimenzije (var.) | ~330 | 1,5–2,8 |
| visina mreže (var.) | ~30 | 1–1,3 |

---

## 1. Yoast SEO title (59 znakova) — Preporuka

```
Dimenzije odbojkaškog terena i podloga — cena | Antas Line
```

## 2. Yoast meta description (152 znaka) — DODATI

```
Dimenzije odbojkaškog terena: 18×9 m + slobodna zona. Visina mreže 2,43 m (m) i 2,24 m (ž), teren na pesku 16×8 m. Podloge za salu i otvoreno — cena po m².
```

## 3. H1 — zameniti (opciono ali preporučeno)

`Sportska podloga za odbojku` → `Odbojkaški teren — dimenzije i podloga`

## 4. Snippet pasus — UBACITI ODMAH ISPOD H1 (featured snippet target)

```html
<p>Zvanične dimenzije odbojkaškog terena su <strong>18 × 9 metara</strong>, sa
slobodnom zonom od najmanje 3 m sa svih strana. Visina mreže je 2,43 m za
muškarce i 2,24 m za žene, a teren za odbojku na pesku je 16 × 8 m. U nastavku:
sve mere, izbor podloge za salu i otvoreno, i cena po m².</p>
```

## 5. Nova sekcija — Odbojka na pesku (ubaciti posle postojeće "Dimenzije terena za odbojku")

```html
<h2>Dimenzije terena za odbojku na pesku</h2>
<p>Teren za odbojku na pesku (beach volleyball) je <strong>16 × 8 metara</strong> —
manji od dvoranskog. Slobodna zona je 3–5 m, bez linije napada (u pesku ne
postoji zona za napad). Visina mreže je ista kao u dvorani: 2,43 m za muškarce
i 2,24 m za žene. Dubina peska za takmičarske terene je najmanje 40 cm.</p>
<p>Za rekreativne terene u dvorištima i kampovima teren se često skraćuje —
bitno je zadržati odnos 2:1 i slobodnu zonu od bar 3 m.</p>
```

## 6. Nova sekcija — Cena (pred kraj, iznad kontakta)

```html
<h2>Cena podloge za odbojkaški teren</h2>
<p>Cena zavisi od tipa podloge i kvadrature:</p>
<ul>
  <li>Vinilna sportska podloga za sale (rolne/ploče): {{CENA_VINIL_OD}}–{{CENA_VINIL_DO}} RSD/m²</li>
  <li>Bergo modularna podloga za otvorene terene: {{CENA_BERGO_SP_OD}}–{{CENA_BERGO_SP_DO}} RSD/m²</li>
  <li>Obeležavanje linija i montaža: {{CENA_MONTAZA_INFO}}</li>
</ul>
<p>Ceo teren sa slobodnom zonom je 260–360 m² — pošaljite nam dimenzije prostora
za tačnu ponudu: <a href="tel:+381692340072"><strong>069 234 00 72</strong></a>
ili preko <a href="/kontakt/">kontakt forme</a>.</p>
```

## 7. FAQ blok — DODATI NA KRAJ (pre footera)

```html
<h2>Često postavljana pitanja o odbojkaškom terenu</h2>

<div class="faq-block">
  <h3>Koje su dimenzije odbojkaškog terena?</h3>
  <p>Teren je <strong>18 × 9 metara</strong>, podeljen mrežom na dve polovine
  9 × 9 m. Sa slobodnom zonom (min. 3 m) potreban prostor je oko 24 × 15 m.</p>
</div>

<div class="faq-block">
  <h3>Kolika je visina odbojkaške mreže?</h3>
  <p><strong>2,43 m za muškarce i 2,24 m za žene</strong>, merena na sredini
  terena. Mreža je dugačka 9,5 m i široka 1 m.</p>
</div>

<div class="faq-block">
  <h3>Koje su dimenzije terena za odbojku na pesku?</h3>
  <p>Teren na pesku je <strong>16 × 8 m</strong> — manji od dvoranskog. Visina
  mreže je ista (2,43/2,24 m), a dubina peska najmanje 40 cm.</p>
</div>

<div class="faq-block">
  <h3>Koja je najbolja podloga za odbojkaški teren?</h3>
  <p>Za sale: vinilne sportske podloge sa amortizacijom (odskok lopte, kontrolisano
  klizanje). Za otvorene terene: Bergo modularne podloge — ne zadržavaju vodu i
  UV su stabilne.</p>
</div>

<div class="faq-block">
  <h3>Koliko košta izgradnja odbojkaškog terena?</h3>
  <p>Podloga košta {{CENA_VINIL_OD}}–{{CENA_BERGO_SP_DO}} RSD/m² zavisno od tipa,
  a ceo teren sa slobodnom zonom je 260–360 m². Pošaljite dimenzije prostora za
  tačnu ponudu.</p>
</div>
```

## 8. FAQPage schema (JSON-LD) — u head ili HTML blok

```json
{
  "@context": "https://schema.org",
  "@type": "FAQPage",
  "mainEntity": [
    {"@type": "Question", "name": "Koje su dimenzije odbojkaškog terena?",
     "acceptedAnswer": {"@type": "Answer", "text": "Teren je 18 × 9 metara, podeljen mrežom na dve polovine 9 × 9 m. Sa slobodnom zonom (min. 3 m) potreban prostor je oko 24 × 15 m."}},
    {"@type": "Question", "name": "Kolika je visina odbojkaške mreže?",
     "acceptedAnswer": {"@type": "Answer", "text": "2,43 m za muškarce i 2,24 m za žene, merena na sredini terena. Mreža je dugačka 9,5 m i široka 1 m."}},
    {"@type": "Question", "name": "Koje su dimenzije terena za odbojku na pesku?",
     "acceptedAnswer": {"@type": "Answer", "text": "Teren na pesku je 16 × 8 m. Visina mreže je ista kao u dvorani (2,43/2,24 m), a dubina peska najmanje 40 cm."}},
    {"@type": "Question", "name": "Koja je najbolja podloga za odbojkaški teren?",
     "acceptedAnswer": {"@type": "Answer", "text": "Za sale vinilne sportske podloge sa amortizacijom, za otvorene terene Bergo modularne podloge koje ne zadržavaju vodu."}},
    {"@type": "Question", "name": "Koliko košta izgradnja odbojkaškog terena?",
     "acceptedAnswer": {"@type": "Answer", "text": "Podloga košta {{CENA_VINIL_OD}}–{{CENA_BERGO_SP_DO}} RSD/m² zavisno od tipa; ceo teren sa slobodnom zonom je 260–360 m²."}}
  ]
}
```

---

## Postupak primene (Miroslav, WP admin na live — ~15 min)

1. Stranica → Yoast: nalepiti title (§1) i meta description (§2)
2. Sadržaj: snippet pasus ispod H1 (§4), sekcija pesak (§5), cena (§6 — **prvo zameniti {{placeholder}} cifre!**), FAQ (§7)
3. Schema (§8): Custom HTML blok sa `<script type="application/ld+json">...</script>` na kraju sadržaja
4. Posle objave: Rich Results Test + GSC Inspect URL → Request indexing
5. Dnevnik unos sa tagom `[cpanel-live]`

## Primenjeno [cpanel-live] — 2026-07-05
- ✅ H1/post_title: `Sportska podloga za odbojku` → `Odbojkaški teren — dimenzije i podloga`
- ✅ Snippet pasus ubačen ispod H1 (featured snippet target)
- ✅ Nova sekcija "Dimenzije terena za odbojku na pesku" ubačena pre H2 "Mreža za odbojkaške terene"
- ✅ FAQ blok (4 pitanja) + FAQPage JSON-LD ubačeni pre kontakt pasusa na kraju — JSON validiran (python `json.loads`)
- ✅ Backup pre izmene: `~/backup-pre-odbojka-refresh-20260705-1020.sql`
- ✅ Verifikovano na live (curl): title, snippet, sekcija peska, FAQ, FAQPage sve prisutni u renderovanom HTML-u
- 🔧 **Ispravka (isti dan):** originalni insert je imao tvrde prelome linija (`\n`) unutar `<p>` tagova → wpautop ih pretvarao u `<br>` na sredini rečenice; spojeno u kontinuirani tekst (7 pasusa), potvrđeno curl-om da nema više `<br>` u sadržaju
- ⏭️ **Preskočeno namerno:** #1 Yoast title i #2 Yoast meta description — nisu menjani na zahtev

## Otvoreno
- [ ] **Cena sekcija (§6) NIJE ubačena** — zahteva stvarne cifre za `{{CENA_VINIL_OD/DO}}`, `{{CENA_BERGO_SP_OD/DO}}`, `{{CENA_MONTAZA_INFO}}`; ubacivanje placeholder teksta na live stranicu bi bilo vidljivo posetiocima #ceka-miroslav
- [ ] **C2 parity:** stranica NE postoji na lokalnom buildu — dodati na parity listu (redirect mapa je verovatno vodi kao "PROVERI") #claude-code
- [ ] Rich Results Test za FAQPage schema na živoj stranici #claude-code
- [ ] GSC Inspect URL → Request indexing posle Rich Results provere #ceka-miroslav
- [ ] Merenje: CTR klastera pre (0,6%) vs 28d posle primene

## Veze
- [[seo/plan-novih-stranica]] TIER 2 #9 · [[analiza/2026-07-04-gsc-kw-analiza-16m]]
- Sibling drafts: [[dnevnik/2026-07-05-draft-dimenzije-kosarkaskog-terena]] (isti snippet metod)
