---
tip: dnevnik-hub
alat: google-ads
datum: 2026-07-01
blok: ADS
status: u-toku
kampanje:
  - Podloge za terase i bazene
  - ECOTILE INDUSTRIJSKI PODOVI
prag-maximize-conversions: 20-30
azurirano: 2026-07-01
sinhronizovano-sa: [[DNEVNIK-NAPRETKA]], [[PROGRESS]]
---

# 📊 ADS — Dnevnik i plan optimizacije

> [!info] Kako se koristi ova beleška
> Ovo je **živi hub** za Google Ads. Snimci podataka i odluke se dopisuju u [[#🗒️ Log|Log]] na dnu (append-only, ne brišemo staro). Fazni plan su čekboksovi — štikliraj kad je urađeno. Veze u uglastim zagradama su sidra ka drugim beleškama u vaultu; ako ti se ime beleške razlikuje, samo preimenuj link.
>
> **INTEGRIRANO sa masternom istorijom:** Svaki novi Log unos automatski ide i u [[DNEVNIK-NAPRETKA]] kao `[ADS]` red + u [[PROGRESS]] kao ažurirana sekcija. Odavde proveravaj status, planove i RSA banke; ali čitaj master ledger za ceo projekat.
>
> Povezano: [[DNEVNIK-NAPRETKA]] · [[B3 - Odblokiranje naloga]] · [[BLOK A - Tracking (GTM v10)]] · [[BLOK B - Publike]] · [[Epoksid conquest - post 2542]]

Nalog: `156-886-0314` (Gogin Nalog) · Strategija: **Maximize Clicks** (namerno, do praga) · Valuta: RSD

---

## 🚦 Trenutno stanje (snimak: 2026-07-01)

| Kampanja | Stanje | Napomena |
|---|---|---|
| Podloge za terase i bazene | ✅ radi | CTR 19%, ali slaba konverzija → fokus na kreativu i strukturu |
| ECOTILE INDUSTRIJSKI PODOVI | ⛔ zagušena | prikazi −67%, CPC ≈3× → uzrok [[B3 - Odblokiranje naloga\|B3]], ne tržište |

> [!warning] Blokada na nivou naloga
> ECOTILE optimizacija **nema smisla dok se ne reši [[B3 - Odblokiranje naloga]]** (nizak balans + verifikacija oglašivača). Premalo prikaza → sistem ne može da uči kombinacije. Ne dizati bid/budžet da se "kompenzuje".

---

## 🎯 Fazni plan

### Faza 0 — Odblokiraj nalog `[preduslov]`
- [ ] Dopuna balansa → [[B3 - Odblokiranje naloga]]
- [x] Završena verifikacija oglašivača
- [ ] Potvrda da su ECOTILE prikazi/CPC vraćeni na normalu (uporedi sa snimkom u Logu)

### Faza 1 — RSA kreativa (Terase odmah, ECOTILE pripremljeno)
- [ ] "Podloge za terase i bazene": ubaci 15 headline-a + 4 description-a (banka niže) → cilj **Ad Strength ≥ Good**
- [ ] Bez nepotrebnog pinovanja (ako mora → 2–3 asseta po poziciji)
- [ ] ECOTILE RSA napisan i sačuvan (pali tek po Fazi 0)

### Faza 2 — Struktura ad grupa
- [ ] Terase → 3 ad grupe: `terase` · `bazeni` · `bergo/modularne`
- [ ] ECOTILE → 2 ad grupe: `industrijski` · `esd/antistatik` (po odblokiranju)
- [ ] Svaka ad grupa dobija svoj RSA (diže keyword relevance)
- [ ] Match tip: ostaje phrase/exact; broad tek uz Smart Bidding
- [ ] [[Negativne ključne reči]] potvrđene na obe kampanje

### Faza 3 — Merenje i priprema za Smart Bidding
- [ ] [[Enhanced Conversions - GTM]] (SHA-256 email+telefon iz forme)
- [ ] Primarna konverzija = isključivo uvoz `/hvala-za-poruku/`
- [ ] `lead_form_start` + `epoxy_conquest_engagement` = samo posmatranje (ne uvoziti)
- [ ] GA4 `tel` NE uvoziti kao Ads konverziju (već postoji GTM click-to-call)
- [ ] Na **20–30 pravih plaćenih konverzija** → prelazak na Maximize Conversions
- [ ] Posle 30 dana: pregled asset ocena, zameni "Low", ne diraj "Learning"

### Faza 4 — Geo, pozivi, publike
- [ ] Geo bid: ECOTILE +20–30% (BG, NS, Niš, KG, Šabac); Terase gore na zone sa kućama/bazenima
- [ ] Call asset `072`, radno vreme, mobilni bid +15–20%
- [ ] [[BLOK B - Publike]] zakačene na Search u **Observation** modu (ne Targeting)

---

## ✍️ Banka RSA asseta (za ponovnu upotrebu)

### Podloge za terase i bazene — Headlines
Podne obloge za terase · Podloge za bazene i dvorišta · Bergo modularne ploče · Montaža bez lepka i radova · Vodootporno i otporno na mraz · Klik-sistem, brza ugradnja · Uzorak i ponuda za 24h · Protivklizna površina · Za terase, bazene i spa · Odgovor na upit isti dan · UV postojano — ne bledi · Cena po m² na upit · Bergo XL / Unique / Elite · Made in EU kvalitet · Besplatna procena površine

### Podloge za terase i bazene — Descriptions
- Modularne podloge za terase i bazene — montaža bez građevinskih radova. Tražite ponudu.
- Vodootporne, protivklizne, UV postojane. Klik-sistem za brzu ugradnju. Uzorak na zahtev.
- Pošaljite dimenzije, dobijte ponudu istog dana. Isporuka u celoj Srbiji.
- Bergo XL, Unique i Elite — rešenje za terase, bazene i dvorišta.

### ECOTILE — Headlines (čeka Fazu 0)
Industrijski PVC podovi · Ecotile ploče za hale · Montaža bez prekida rada · Otpornost na viljuškare · ESD / antistatik podovi · Klik-ploče bez lepka · Za magacine i proizvodnju · Alternativa epoksidu · Cena po m² na upit

### ECOTILE — Descriptions (čeka Fazu 0)
- PVC industrijske ploče — ugradnja bez zatvaranja pogona. Otporne na viljuškare i hemikalije.
- ESD opcija za elektroniku i čiste sobe. Klik-sistem, bez lepka i sušenja.
- Alternativa epoksidu bez mirisa i čekanja — pod odmah upotrebljiv.

> [!note] "Alternativa epoksidu" = pozicioniranje u tekstu, NE ciljanje. Epoksid ostaje negativna reč; conquest na epoksid je SEO posao → [[Epoksid conquest - post 2542]].

---

## 🛡️ Pravila (ne kršiti)

- Prava konverzija = **samo `/hvala-za-poruku/`**. Istorijski "lidovi" pre ispravke pravila se ne računaju.
- Ne dupliraj konverzije: GA4 `tel` i `generate_lead` se NE uvoze kao dodatne Ads akcije.
- Pad merenih brojeva posle čišćenja trackinga = tačnije merenje, **ne** pad učinka → ne reaguj budžetom.
- Impression share gubitak je rank-driven (Quality Score) → budžet to ne rešava.
- Broad match tek uz Smart Bidding.

---

## 🗒️ Log

> Najnoviji unos na vrhu. Format: `### YYYY-MM-DD [izvor]`

### 2026-07-01 [chat]
**Snimak podataka (7d: 23–29.06 vs 16–22.06):**

| Metrika | 23–29.06 | 16–22.06 | Δ |
|---|---|---|---|
| Ads potrošnja (RSD) | 8.206 | 9.300 | −11,8% |
| Klikovi | 339 | 507 | −33,1% |
| Prikazi | 1.812 | 3.392 | −46,6% |
| CTR | 18,7% | 14,9% | +3,8 pp |
| CPC (RSD) | 24 | 18 | +33% |
| Uvezene konv. | 3 | 2 | +1 |
| GA4 generate_lead | 27 | 24 | +12,5% |
| Hvala proxy | 22 | 20 | +10,0% |

- ECOTILE zagušen: prikazi 802 → 261 (−67,5%), CPC 26 → 74 RSD. Uzrok = [[B3 - Odblokiranje naloga]].
- Terase: volumen klika dobar (296/ned), konverzija slaba (2) → prioritet kreativa + struktura.
- Pad korisnika −36% je efekat prelaska na GTM-only + Consent Mode (~27.06), ne pad saobraćaja.
- **Pravih konverzija u junu (1–29):** 53 (hvala-za-poruku). Plaćene i dalje daleko ispod praga 20–30.
- Napravljen fazni plan (0–4) i banka RSA asseta (gore).

**Sledeći korak:** napuni RSA za Terase + iscepkaj na 3 ad grupe (Faza 1–2, ne čeka odblokiranje).

<!-- ŠABLON ZA NOVI UNOS — kopiraj iznad ovog reda:
### YYYY-MM-DD [chat|claude-code|cpanel-live]
- 
**Sledeći korak:** 
-->
