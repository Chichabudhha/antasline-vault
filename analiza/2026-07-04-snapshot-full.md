---
tip: analiza-snapshot
datum: 2026-07-04
izvori: [google-ads, ga4, gsc, gmb]
prozori: "16mo trend (2025-04 → 2026-07-03) · 90d (2026-04-05 → 2026-07-03) · 28d (06.06–03.07 vs 09.05–05.06)"
alat: windsor
status: gotovo
azurirano: 2026-07-04
---

# 📊 SNAPSHOT — Kompletno stanje: Ads · GA4 · GSC · GMB (2026-07-04)

> [!info] Kako čitati
> Ovo je prvi puni dnevnik stanja — **baseline** za sva buduća poređenja. Sve cifre su stvarni podaci iz Windsor.ai (ništa procenjeno). Sledeći snapshot: kopirati [[analiza/_TEMPLATE-snapshot]] i porediti sa ovim. Valuta: RSD.

---

## 0. REZIME — top nalazi

1. 🔴 **GSC erozija CTR-a**: jun YoY klikovi −19% (2599→2104) uz impresije +22% (36990→44991). CTR 7,0%→4,7%. Pozicije čak bolje (7,9→6,3) → klikove jedu SERP features/AI overviews + rast informacionih upita.
2. 🔴 **GA4 `conversions` metrika SLOMLJENA od juna**: jun = 5.859 (ranije 64–162). Neki event je pogrešno označen kao key event → **proveriti Admin → Key events** (`#ceka-miroslav`). Za prave konverzije koristiti samo hvala-proxy.
3. 🔴 **Pre juna 2026 ne postoji pouzdana serija konverzija**: `/hvala-za-poruku/` ima preglede tek od juna (55). Baseline pravih lidova = **~55/mesec, počinje jun 2026**.
4. 🟡 **Negativne ključne reči NE važe na aktivnim kampanjama**: "epoksidni podovi", "sika", "rinol", "poliuretan", "marmoleum", "topli podovi", "betonski" — svi prolaze kroz search terms i troše. Vidljivi otpad ≈ **2k RSD/90d** + "antistatik pod" broad 5k (0 konv., organski smo pos. 2,4) ≈ **16% potrošnje**.
5. 🟢 **Ads prelaz na nove kampanje početkom juna uspeo**: stare pauzirane ("Ecotile - Antas line" potrošila 10,1k/90d sa 0 konv. pre pauze), nove pune: jun = **30,7k RSD — najveći mesec u 16 meseci**. ECOTILE radi (CPC 33 RSD, 4 konv.).
6. 🟢 **ECOTILE phrase "industrijski podovi" = najbolji asset**: 3 konv. za 3.220 RSD → **1.073 RSD/konverziji**. Organski za isti pojam tek pozicija ~11 → plaćeno ovde ima smisla i treba ga skalirati.
7. 🟡 **Terase impression share samo 24%** (rank-lost 63%!) → Quality Score problem, ne budžet → Faza 1–2 (RSA + 3 ad grupe) je pravi lek.
8. 🟡 **Preklapanje Paid↔Organic na terasama**: plaćamo "gumeni podovi za terase cena" (organski pos. 1,4!), "podloge za terasu" (pos. 2,5–3,5). Za industrijske pojmove obrnuto — organski slab (pos. 11), plaćeno opravdano.
9. 🟡 **GMB zapušten**: impresije 1020→~300/mes, pozivi 17→~1/mes, samo **6 recenzija** (4,7★). UTM `utm_medium=gmb` → GA4 "Unassigned" (nemerljiv).
10. 🟢 **Rich snippets rade**: Review snippet 694 + Product snippet 362 klikova/90d (Product CTR 10,5%) → širiti Product schema na money stranice.
11. 📊 **Mobile je sve**: 76% GSC klikova, 87% Ads potrošnje, 74% GA4 korisnika → performanse lokalnog builda (LCP!) = najveća poluga konverzija.
12. 🆕 **AI Assistant kanal se pojavio** (chatgpt.com referrali, 9 korisnika/90d) — mali, ali FAQ/schema rad direktno pomaže.

---

## 1. GSC (sc-domain:antasline.com)

### 1.1 Trend 16 meseci

| Mesec | Klikovi | Impresije | CTR | Poz. |
|---|---|---|---|---|
| 2025-04 | 3.336 | 45.223 | 7,4% | 6,7 |
| 2025-05 | 3.215 | 45.100 | 7,1% | 7,2 |
| 2025-06 | 2.599 | 36.990 | 7,0% | 7,9 |
| 2025-07 | 2.340 | 34.437 | 6,8% | 8,2 |
| 2025-08 | 2.227 | 36.112 | 6,2% | 8,2 |
| 2025-09 | 2.148 | 35.308 | 6,1% | 6,9 |
| 2025-10 | 1.889 | 33.130 | 5,7% | 6,0 |
| 2025-11 | 1.735 | 33.392 | 5,2% | 5,4 |
| 2025-12 | 1.440 | 30.470 | 4,7% | 6,7 |
| 2026-01 | 1.646 | 32.249 | 5,1% | 6,5 |
| 2026-02 | 2.387 | 42.097 | 5,7% | 5,8 |
| 2026-03 | 3.071 | 49.790 | 6,2% | 5,5 |
| 2026-04 | 2.783 | 45.704 | 6,1% | 5,4 |
| 2026-05 | 2.523 | 44.275 | 5,7% | 5,8 |
| 2026-06 | 2.104 | 44.991 | 4,7% | 6,3 |

**Čitanje:** dno dec 2025 (1.440) → oporavak do marta (3.071) → ponovo klizi. Jun YoY: klikovi −19%, impresije +22%, CTR 7,0→4,7. Sezonalitet: proleće (mar–maj) je špic.

### 1.2 Top upiti 90d (klikovi)

| Upit | Kl. | Impr. | CTR | Poz. |
|---|---|---|---|---|
| antas line (brend) | 128 | 212 | 60% | 1,0 |
| gumeni podovi za terase cena | 67 | 940 | 7,1% | 1,4 |
| podloga za košarkaški teren | 63 | 147 | 43% | 1,6 |
| podloga za kosarkaski teren cena | 49 | 230 | 21% | 5,5 |
| gumeni tepih za terasu | 42 | 645 | 6,5% | 1,1 |
| teren za basket dimenzije | 42 | 253 | 17% | 1,4 |
| vestacka trava za fudbal cena | 34 | 171 | 20% | 2,3 |
| dimenzije table za kos | 32 | 519 | 6,2% | 1,3 |
| plasticne staze za dvoriste | 29 | 268 | 11% | 1,5 |
| dimenzije košarkaške table | 27 | 506 | 5,3% | 1,1 |
| antistatik pod | 25 | 274 | 9,1% | 2,4 |
| gumene podloge za terasu | 23 | 538 | 4,3% | 1,6 |
| kosevi/koš sa konstrukcijom (3 var.) | 48 | 551 | ~9% | 8–9 |

### 1.3 Najveće CTR rupe (visoke impresije, malo klikova) — 90d

| Upit / stranica | Impr. | CTR | Poz. | Napomena |
|---|---|---|---|---|
| sljaka (+šljaka) | 1.752 | 0,2% | 4,4 | informaciono, ali "sljaka za teniski teren cena" konvertuje |
| dimenzije fudbalskog terena | 1.266 | 0,3% | 2,0 | info intent, mala ponuda |
| dimenzije teniskog terena (2 var.) | 1.350 | ~0,1% | 2,5–4 | → landing tenis dimenzije + cena |
| visina koša (4 var.) | ~1.300 | <0,5% | 1–2 | FAQ/featured snippet šansa |
| epoksidni/epoxy upiti (zbir) | ~2.400 | ~0,2% | 4–12 | conquest članak pos. 8,6 — treba push |
| **/pop-tenis/** | 5.503 | 0,58% | 3,1 | title/meta refresh HITNO |
| **/podloga-za-odbojkaske-terene/** | 2.668 | 1,3% | 3,0 | title/meta refresh |
| **/spoljnje-podne-obloge/** | 11.019 | 3,5% | 9,4 | glavna kategorija — meta + pozicija |
| /epoksidni-podovi-ili-ecotile-podovi/ | 4.401 | 0,57% | 8,6 | conquest — interni linkovi + refresh |
| /sportske-podloge/kosarkaske-konstrukcije/ | 6.905 | 6,7% | 8,5 | C3 landing → pozicija 4–5 = ~2× klikova |

### 1.4 Top stranice 90d (klikovi)

| Stranica | Kl. | Impr. | CTR | Poz. |
|---|---|---|---|---|
| /kako-napraviti-teren-za-basket…/ | 1.058 | 29.286 | 3,6% | 2,7 |
| /sportske-podloge/ | 1.054 | 9.061 | 11,6% | 5,7 |
| /podloge-za-parkiraliste-i-staze/ | 693 | 7.482 | 9,3% | 5,1 |
| /sportske-podloge/kosarkaske-konstrukcije/ | 461 | 6.905 | 6,7% | 8,5 |
| /vestacka-trava/ | 400 | 4.299 | 9,3% | 6,2 |
| /spoljnje-podne-obloge/ | 386 | 11.019 | 3,5% | 9,4 |
| /spoljnje-podne-obloge/bergo-xl/ | 299 | 5.155 | 5,8% | 5,1 |
| /industrijski-podovi/ | 208 | 5.102 | 4,1% | **11,0** |
| /antistatik-i-elektroprovodljivi-podovi/ | 184 | 2.661 | 6,9% | 6,3 |
| /podloga-za-teniske-terene/ | 165 | 5.867 | 2,8% | 6,0 |

### 1.5 Breakdowns 90d

- **Uređaj:** Mobile 5.456 kl. (75,5%, poz. 5,2) · Desktop 1.733 (poz. 7,9) · Tablet 44
- **Zemlja:** Srbija 5.763 kl. (CTR 6,3%) · Hrvatska 321 · BiH 242 · CG 12
- **Search appearance:** Review snippet 694 kl. / 13.535 impr. · **Product snippet 362 kl. / CTR 10,5%** ⭐
- **Brend (jun):** nonbranded 579 kl. od prijavljenih upita (ostatak = anonimizovani upiti — GSC ne prijavljuje sve)
- **Movers 28d (06.06–03.07 vs 09.05–05.06):** ukupno hlađenje — "gumeni podovi za terase cena" 23→17 kl. (impr. 321→222, sezona), "antas line" 48→37, basket klaster stabilan. Ništa nije palo pozicijom — pad je tražnja/sezona.

---

## 2. GA4 (292720335)

### 2.1 Trend 16 meseci

| Mesec | Korisnici | Novi | Sesije | Eng. rate | Pregledi |
|---|---|---|---|---|---|
| 2025-04 | 4.575 | 4.435 | 6.475 | 51% | 8.801 |
| 2025-05 | 4.561 | 4.397 | 6.143 | 52% | 8.654 |
| 2025-06 | 3.496 | 3.357 | 4.700 | 45% | 6.284 |
| 2025-07 | 2.716 | 2.599 | 3.271 | 59% | 6.280 |
| 2025-08 | 2.668 | 2.553 | 3.478 | 54% | 5.666 |
| 2025-09 | 3.408 | 3.300 | 4.664 | 50% | 6.597 |
| 2025-10 | 3.335 | 3.207 | 4.603 | 50% | 6.492 |
| 2025-11 | 3.199 | 3.123 | 4.375 | 48% | 5.870 |
| 2025-12 | 2.796 | 2.705 | 3.866 | 53% | 5.368 |
| 2026-01 | 2.896 | 2.825 | 3.935 | 50% | 5.449 |
| 2026-02 | 3.722 | 3.623 | 5.120 | 48% | 6.937 |
| 2026-03 | 4.668 | 4.548 | 6.271 | 49% | 8.815 |
| 2026-04 | 4.097 | 4.070 | 5.326 | 48% | 7.610 |
| 2026-05 | 3.617 | 3.851 | 4.736 | 49% | 7.219 |
| 2026-06 | 4.180 | 4.361 | 5.430 | 55% | 8.036 |

**Jun YoY: korisnici +19,6%** (3.496→4.180) — dok GSC organski pada −19% → razliku nose Paid (30,7k RSD spend) i Direct. ⚠️ `conversions` kolona namerno izostavljena — slomljena od juna (5.859; ranije 64–162).

### 2.2 Kanali 90d

| Kanal | Korisnici | Sesije | Eng. rate |
|---|---|---|---|
| Organic Search | 6.133 | 7.399 | 64% |
| Direct | 3.632 | 4.064 | 38% |
| Paid Search | 1.724 | 1.833 | 62% |
| **Unassigned** | 1.528 | 1.608 | **1,5%** ⚠️ |
| Organic Social | 70 | 74 | 81% |
| Referral | 25 | 83 | 72% |
| AI Assistant 🆕 | 9 | 11 | 100% |

⚠️ Unassigned sa 1,5% engagementa = ghost/consent sesije + GMB `utm_medium=gmb` (nestandardan → GA4 ne klasifikuje). Landing "(not set)" = 1.766 sesija, eng. 2,5% — isti artefakt.

### 2.3 Ključni eventi po mesecima (event_count)

| Mesec | generate_lead* | tel | mailto | Hvala proxy** |
|---|---|---|---|---|
| 2025-04 | 37 | 28 | — | — |
| 2025-05 | 30 | 26 | — | — |
| 2025-06 | 15 | 12 | 6 | — |
| 2025-07 | 34 | — | — | — |
| 2025-08 | 9 | — | — | — |
| 2025-09 | 24 | — | — | — |
| 2025-10 | 79 | 29 | — | — |
| 2025-11 | 64 | 6 | — | — |
| 2025-12 | 95 | — | — | — |
| 2026-01 | 40 | 12 | — | — |
| 2026-02 | 46 | 26 | — | — |
| 2026-03 | 65 | 23 | — | — |
| 2026-04 | 53 | 20 | — | — |
| 2026-05 | 126 | 17 | — | — |
| 2026-06 | 78 | 7 | — | **55** |
| 2026-07 (1–3) | — | — | — | 4 |

\* generate_lead pre ~27.06.2026 = stara definicija (pregled /kontakt) — **NIJE uporedivo** sa novom (/hvala-za-poruku/). Skok maj (126) = stara definicija, ne rast.
\** Hvala proxy (`page_path contains "hvala"`) = jedina prava serija konverzija — **postoji tek od juna 2026** (stranica/redirect ranije nisu postojali). **Baseline: ~55/mesec.**

Novi eventi uživo: `view_product_category` (jun 300, jul 1–3 već 378 — implementiran nedavno), `lead_form_start` (jul: 7), `epoxy_conquest_engagement` (90d: 9). Legacy `tel:+381692340072` pucao još u junu (14×) — obrisan 29.06, jul čist ✓

### 2.4 Ključni eventi po kanalu 90d (⚠️ direkciono — `in` filter nepouzdan)

generate_lead: Direct 215 · Organic 125 · Paid 30 · Referral 16 — Direct dominacija = consent-mode gubitak atribucije (opt-out model od 28.06 treba da popravi ubuduće). tel: Organic 118 · Direct 51 · Paid 27.

### 2.5 Publike (active_users proxy, 90d)

| Publika | Aktivni |
|---|---|
| All Users | 11.513 |
| 25+ god (stara) | 4.418 |
| Form Abandoners | 12 |
| Epoxy Changers | 3 |
| High-Intent B2B / Sport & Court / Parking / Košarkaški | **ne pojavljuju se = prazne** ⚠️ |

→ Pragovi serviranja (100/1.000) nedostižni organski u doglednom roku. **Customer Match ostaje jedini prečac.**

### 2.6 Geo + uređaji 90d

Mobile 8.639 korisnika (74%) · Desktop 2.881 · Beograd 5.728 · Novi Sad 802 · Umka 390 · Zagreb 341 · Podgorica 285 · Niš 204 · Kragujevac 179. Regionalni saobraćaj (HR/BiH/CG/MK) realan — isporuka van Srbije = pitanje za Ads geo.

---

## 3. GOOGLE ADS (156-886-0314) — sve u RSD

### 3.1 Trend (meseci sa potrošnjom)

| Mesec | Potrošnja | Kl. | Impr. | CTR | CPC | Konv.* |
|---|---|---|---|---|---|---|
| 2025-04 | 6.256 | 845 | 14.917 | 5,7% | 7,4 | 6 |
| 2025-05 | 3.005 | 197 | 1.650 | 11,9% | 15,3 | 0 |
| 2025-06/08 | 0 | — | — | — | — | — |
| 2025-09 | 13.073 | 605 | 4.011 | 15,1% | 21,6 | 0 |
| 2025-10 | 14.067 | 666 | 3.565 | 18,7% | 21,1 | 0 |
| 2025-11 | 15.234 | 721 | 3.843 | 18,8% | 21,1 | 0 |
| 2025-12 | 18.864 | 887 | 5.232 | 17,0% | 21,3 | 0 |
| 2026-01 | 14.075 | 682 | 9.579 | 7,1% | 20,6 | 0 |
| 2026-02 | 11.801 | 606 | 3.366 | 18,0% | 19,5 | 0 |
| 2026-03 | 12.583 | 616 | 3.773 | 16,3% | 20,4 | 0 |
| 2026-04 | 8.711 | 375 | 2.549 | 14,7% | 23,2 | 0 |
| 2026-05 | 3.822 | 193 | 1.377 | 14,0% | 19,8 | 0 |
| **2026-06** | **30.684** | **1.395** | **8.214** | **17,0%** | **22,0** | **6** |
| 2026-07 (1–3) | 3.616 | 146 | 626 | 23,3% | 24,8 | 1 |

\* Uvoz konverzija radi tek od juna 2026 — istorijske nule ≠ nula lidova, nego nemerenje.

**Jun = najveći spend mesec u 16 meseci.** Prelaz na nove kampanje početkom juna (28d prethodni prozor: nove kampanje ~200 RSD ukupno; 28d tekući: 33,9k).

### 3.2 Kampanje 90d

| Kampanja | Status | Potr. | Kl. | CTR | CPC | Konv. | Impr. share | Rank lost | Budget lost |
|---|---|---|---|---|---|---|---|---|---|
| Podloge za terase i bazene | ✅ | 19.627 | 1.088 | 17,5% | 18,0 | 3 | **24%** | **63%** ⚠️ | 15% |
| ECOTILE INDUSTRIJSKI PODOVI | ✅ | 14.468 | 441 | 17,6% | 32,8 | 4 | 33% | 46% | 4% |
| Ecotile - Antas line (stara) | ⏸️ | 10.133 | 498 | 15,1% | 20,3 | 0 | 48% | 53% | 3% |
| podovi za baste (stara) | ⏸️ | 922 | 10 | 5,3% | 92,2 | 0 | 12% | 62% | 14% |

90d ukupno: **45,1k RSD, 7 konverzija → 6.446 RSD/konv.** Obe aktivne kampanje = 1 ad grupa (potvrđuje Fazu 2). Rank-lost IS dominira nad budget-lost → **Quality Score, ne budžet**.

### 3.3 Ključne reči — pobednici i gubitnici 90d

**🏆 Pobednici:**
| KW (kampanja) | Match | Potr. | Kl. | Konv. | RSD/konv. |
|---|---|---|---|---|---|
| industrijski podovi (ECOTILE) | PHRASE | 3.220 | 55 | **3** | **1.073** ⭐ |
| podovi za radionice (ECOTILE) | BROAD | 1.734 | 71 | 1 | 1.734 |
| podne obloge za terase (Terase) | PHRASE | 2.151 | 126 | 1 | 2.151 |
| podovi za terase (Terase) | PHRASE | 2.161 | 124 | 1 | 2.161 |
| plasticne podloge za terase (Terase) | BROAD | 531 | 35 | 1 | 531 |

**💸 Gubitnici (potrošnja bez konverzija):**
| KW | Potr. | Kl. | Problem |
|---|---|---|---|
| antistatik pod BROAD (stara Ecotile) | **5.023** | 239 | organski pos. 2,4 — plaćali za ono što imamo |
| pvc podovi za bazene BROAD (Terase) | 2.854 | 159 | najveći KW Terasa — treba svoja ad grupa + landing |
| pvc podne ploče BROAD (ECOTILE) | 1.500 | 59 | preširoko |
| bastenski namestaj PHRASE (Terase) | 164 | 8 | ⚠️ pogrešan KW — ukloniti |
| oprema za bazene PHRASE (Terase) | 144 | 8 | ⚠️ pogrešan KW — ukloniti |

Obrazac: **PHRASE konvertuje, BROAD rasipa** (u ovom nalogu, pre Smart Bidding-a).

### 3.4 Search terms — dokaz da negativne NE važe (90d)

Prošli i trošili (svi su na negativnoj listi!): epoksidni podovi (3 var. + sika + smola + epoxidni) ≈ **668 RSD** · poliuretan/poliester ≈ 237 · rinol 44 · ibotac 32 · marmoleum 45 · "topli podovi" 28 · betonski 30 · teraco 28. Plus kandidati za NOVE negativne: **deking** (432!), drvene obloge (74), plocice za terasu (264), letvice (42), pevex (36).
**Vidljivi otpad ≈ 2,0k RSD/90d** (samo termini ≥2 klika — stvarno više) + antistatik 5k = **~16% potrošnje**.

### 3.5 Uređaji 90d

Mobile: 39,6k RSD (87%!), 1.837 kl., CTR 17,4%, 6 konv. · Desktop: 5,4k, 192 kl., 1 konv.

---

## 4. GOOGLE MY BUSINESS ("Industrijski i sportski podovi Beograd - Antas Line")

### 4.1 Trend (izbor meseci)

| Mesec | Impresije | Search (m+d) | Maps (m+d) | Pozivi | Web kl. | Rute |
|---|---|---|---|---|---|---|
| 2025-04 | 1.020 | 923 | 97 | **17** | 23 | 54 |
| 2025-08 | 513 | 451 | 62 | 1 | 10 | 57 |
| 2025-12 | 344 | 264 | 80 | 1 | 6 | 80 |
| 2026-02 | 758 | 697 | 61 | 1 | 0 | 56 |
| 2026-04 | 323 | 248 | 75 | 1 | 11 | 24 |
| 2026-05 | 374 | 272 | 102 | 3 | 4 | 46 |
| 2026-06 | 278 | 201 | 77 | 1 | 7 | 55 |

**Impresije −73% od apr 2025; pozivi 17→~1/mes.** Rute stabilne (~50/mes) — ljudi i dalje dolaze u radnju, ali profil ne generiše nove pozive.

### 4.2 Ostalo

- **Keywords (apr–jun):** "industrijski podovi" **149** korisnika · "antas line" 40 · "industrijski pod" 37 — profil se rankira za glavni B2B pojam ✓
- **Recenzije: samo 6 ukupno, prosek 4,7★** — premalo za local pack protiv konkurencije
- **Profil:** kategorija "Продавница подова" (samo jedna!), telefon 069 2340072 ✓ (dominantni broj), opis pominje sportske terene/Ecotile/LVT ✓
- ⚠️ **Website URI ima `utm_medium=gmb`** → GA4 ga baca u "Unassigned" → GMB saobraćaj je nemerljiv u analitici

---

## 5. CROSS-SOURCE ANALIZA

1. **Organik pada − Paid raste = ukupan saobraćaj stabilan.** GSC jun −19% YoY, GA4 korisnici +20% YoY. Zavisnost od plaćenog raste — redizajn (CWV + content) mora vratiti organski rast pre migracije.
2. **CTR erozija je globalni SERP problem, ne penal**: pozicije bolje nego 2025, impresije rekordne, klikovi manji. Odgovor: rich snippets (Product schema već daje CTR 10,5%), FAQ featured snippets, title/meta prepisi na 4 stranice sa najvećim rupama (pop-tenis, odbojka, spoljne obloge, conquest).
3. **Basket klaster = najjači organski asset bez monetizacije**: vodič 1.058 kl./90d (poz. 2,7), a konstrukcije/koševi (upiti sa purchase intentom: "koš sa konstrukcijom" 551 impr. na poz. 8–9!) nemaju landing → C3 landing + 4 GSC stranice direktno adresiraju ovo.
4. **Paid/Organic overlap mapa**: terase-cena upiti organski poz. 1–1,5 (plaćanje = kanibalizacija) vs "industrijski podovi" organski poz. 11 (plaćanje = opravdano i konvertuje po 1.073 RSD). Pravilo: **plati gde je organik >poz. 5, štedi gde je organik top 3**.
5. **Konverziono merenje tek počinje**: hvala-proxy od juna (55), Ads import od juna (6), GA4 `conversions` slomljen, kanal-atribucija iskrivljena consent-om. Svaka analiza pre juna 2026 = nagađanje. **Jun 2026 je mesec-nula.**
6. **Mobile-first realnost**: 76% GSC / 87% Ads / 74% GA4 → mobilni LCP na lokalnom buildu (trenutno ~7,3s!) je verovatno najskuplji pojedinačni problem u celom levku.
7. **Publike mrtve na pragu** → Faza 4 (publike na Search) nerealna bez Customer Match → email-ovi iz upita su asset koji se već skuplja kroz formu.
8. **GMB je najjeftiniji quick-win**: B2B kupci proveravaju firmu pre poziva; 6 recenzija + 1 kategorija + slomljen UTM = popravke od par sati.
9. **Regionalni saobraćaj postoji** (HR 321 + BiH 242 + CG 12 GSC kl.; Zagreb/Podgorica/Sarajevo u GA4 top 10) — odluka o isporuci/ciljanju regiona = potencijalno tržište bez dodatnog sadržaja.
10. **AI kanali klijaju**: chatgpt referrali + AI overviews u SERP-u → strukturiran, citabilan sadržaj (FAQ, tabele, schema) je odbrana i ofanziva istovremeno.

---

## 6. STRATEGIJA — putevi ka više kvalitetnog saobraćaja

### 6.1 SEO (redosled po ROI)

| # | Akcija | Nalaz | Očekivano |
|---|---|---|---|
| 1 | **Title/meta prepis 4 stranice**: /pop-tenis/ (5.503 impr, 0,6% CTR), /podloga-za-odbojkaske-terene/ (2.668, 1,3%), /spoljnje-podne-obloge/ (11.019, 3,5%), conquest članak (4.401, 0,6%) | CTR rupe §1.3 | +500–700 kl./90d bez ijedne nove stranice |
| 2 | **C3: landing kosarkaske-konstrukcije** + 4 GSC stranice (dimenzije basket terena, terasa cena, basket tabla, parking cena) | poz. 8,5 na 6.905 impr; purchase-intent upiti bez stranice | ~2× klikova klastera; poz. 8→4 |
| 3 | **Product schema na sve money stranice** (Bergo, Ecotile, konstrukcije) | Product snippet CTR 10,5% vs prosek 5,5% | CTR boost na postojećim pozicijama |
| 4 | **FAQ blokovi ciljano na featured snippet**: visina koša, dimenzije (tenis/odbojka/padel), šljaka | ~4.500 impr./90d info upita gde smo poz. 1–4 a CTR <1% | featured snippet = brend + AI-citati |
| 5 | **Interni linkovi ka /industrijski-podovi/** (organski poz. 11!) sa basket/parking/spoljne stranica | najvredniji B2B pojam najslabije rankiran | poz. 11→6-7 = 2–3× klikova |

### 6.2 ADS (redosled po hitnosti)

| # | Akcija | Nalaz | Očekivano |
|---|---|---|---|
| 1 | **Proveri da li je lista "AntasLine — univerzalne negativne" stvarno zakačena na obe aktivne kampanje** `#ceka-miroslav` | epoksid/sika/rinol/poliuretan/marmoleum prolaze (§3.4) | −2k+/90d otpada |
| 2 | **Dodaj negativne**: deking, drvene, plocice, teraco, letvice, pevex; **ukloni KW**: "bastenski namestaj", "oprema za bazene" | §3.3–3.4 | još −1k/90d |
| 3 | **Faza 1–2 (RSA + 3 ad grupe Terase)**: bazen KW (1.040 impr!) → svoja grupa sa landing /podovi-za-bazene/ | rank-lost 63% = QS problem | IS 24%→40%+ za isti budžet |
| 4 | **Skaliraj ECOTILE phrase "industrijski podovi"** (+geo BG/NS/Niš) | 1.073 RSD/konv. ⭐ | najjeftinije B2B konverzije |
| 5 | **Ne uvodi novi broad pre Smart Bidding-a**; postojeći broad drži samo uz ispravne negativne | phrase konvertuje, broad rasipa | čistiji signal za prag 20–30 |
| 6 | Ostati na **Maximize Clicks** — 7 konv./90d je daleko od praga 20–30 | §3.2 | — |

### 6.3 GMB (quick wins, ~2h)

1. **Popravi UTM**: `utm_medium=gmb` → `utm_medium=organic&utm_source=google_business` (ili ukloni) — da GA4 meri
2. **Kampanja za recenzije**: 6→20+ (email posle svakog završenog posla; link za recenziju iz profila) `#ceka-miroslav`
3. **Dodaj kategorije**: "Izvođač sportskih terena" / "Podne obloge — veleprodaja" uz postojeću
4. Mesečni GMB post (referenca/projekat) — profil deluje živ

### 6.4 Tracking (preduslov za sve)

1. 🔴 **GA4 key events audit** — naći šta pravi 5.859 "konverzija" u junu i skinuti ga sa key event liste `#ceka-miroslav`
2. Držati tačno 3 key eventa: generate_lead / tel / mailto (po CLAUDE §4)
3. Enhanced Conversions (SHA-256) — priprema kroz GTM za precizniju Ads atribuciju

---

## 7. AKCIJA NEDELJE

**Proveri u Google Ads UI da li je negativna lista zakačena na obe aktivne kampanje + skini "bastenski namestaj"/"oprema za bazene" KW — 15 minuta posla koje odmah zaustavlja ~16% rasipanja budžeta.**

---

## Veze
- [[analiza/_README]] — kako se ovaj sistem koristi
- [[analiza/_TEMPLATE-snapshot]] — šablon za sledeći snapshot
- [[dnevnik/ADS-DNEVNIK]] — fazni plan 0–4 (Faza 0 ✅) i RSA banka
- [[dnevnik/2026-07-02-gsc-keywords-analiza]] — prethodna GSC analiza (30d)
- [[2026-07-02-MASTER-PLAN-DO-LIVE]] — 2-mesečni plan
- [[PROGRESS]] · [[DNEVNIK-NAPRETKA]]
