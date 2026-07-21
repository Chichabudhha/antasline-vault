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
> Povezano: [[DNEVNIK-NAPRETKA]] · [[blokovi/BLOK-A-tracking]] · [[blokovi/BLOK-B-publike]]

Nalog: `156-886-0314` (Gogin Nalog) · Strategija: **Maximize Clicks** (namerno, do praga) · Valuta: RSD

---

## 🚦 Trenutno stanje (snimak: 2026-07-01)

| Kampanja | Stanje | Napomena |
|---|---|---|
| Podloge za terase i bazene | ✅ radi | CTR 19%, ali slaba konverzija → fokus na kreativu i strukturu |
| ECOTILE INDUSTRIJSKI PODOVI | ✅ odblokirana | Nalog odblokiran 2026-07-04 (balans + verifikacija) → proveriti da su prikazi/CPC vraćeni na normalu |

> [!note] Nalog odblokiran (2026-07-04)
> Dopuna balansa i verifikacija oglašivača su završene — throttling na nivou naloga više nije uzrok pada prikaza. Sledeći korak: potvrditi u Ads da su ECOTILE prikazi/CPC vraćeni na normalu (uporedi sa snimkom 2026-07-01 u Logu), pa tek onda dizati kreativu/strukturu.

---

## 🎯 Fazni plan

### Faza 0 — Odblokiraj nalog `[✅ zavrseno 2026-07-04]`
- [x] Dopuna balansa
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
- [x] Negativne ključne reči potvrđene na obe kampanje ✅ 2026-07-06 (vidi log + [[CLAUDE]] sekcija 6)

### Faza 3 — Merenje i priprema za Smart Bidding
- [ ] Enhanced Conversions (SHA-256 email+telefon iz forme) — trebalo bi da se implementira kroz GTM
- [ ] Primarna konverzija = isključivo uvoz `/hvala-za-poruku/`
- [ ] `lead_form_start` + `epoxy_conquest_engagement` = samo posmatranje (ne uvoziti)
- [ ] GA4 `tel` NE uvoziti kao Ads konverziju (već postoji GTM click-to-call)
- [ ] Na **20–30 pravih plaćenih konverzija** → prelazak na Maximize Conversions
- [ ] Posle 30 dana: pregled asset ocena, zameni "Low", ne diraj "Learning"

### Faza 4 — Geo, pozivi, publike
- [ ] Geo bid: ECOTILE +20–30% (BG, NS, Niš, KG, Šabac); Terase gore na zone sa kućama/bazenima
- [ ] Call asset `072`, radno vreme, mobilni bid +15–20%
- [ ] [[blokovi/BLOK-B-publike]] zakačene na Search u **Observation** modu (ne Targeting)

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

> [!note] "Alternativa epoksidu" = pozicioniranje u tekstu, NE ciljanje. Epoksid ostaje negativna reč; conquest na epoksid je SEO posao (post 2542 na live sajtu).

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

### 2026-07-21 [claude-code] — ✅ RAZREŠENO: blackout od 07-05 = namerna pauza (M godišnji odmor), NE throttling
- Windsor potvrđuje: 0 RSD/0 prikaza kontinuirano 07-16 → 07-21 (17 dana), pre toga sporadični stray prikazi bez potrošnje 07-07/08/10/11/15.
- **Miroslav potvrdio 2026-07-21: oglasi svesno isključeni zbog godišnjeg odmora** — nije nalog throttling (kao jun), nije billing/verifikacija problem. Ne treba dalja Ads UI provera po ovom nalazu.
- ⚠️ Napomena za buduće izveštaje: kumulativ plaćenih konverzija (10 od 01.06) i dalje zamrznut dok se kampanje ne reaktiviraju — Smart Bidding prag (20-30) se ne pomera tokom pauze, ne računati kao "usporavanje" nego kao očekivanu posledicu.
- **Sledeći korak:** čekati M da reaktivira kampanje kad se vrati; kad se to desi, potvrditi da su ECOTILE/Terase prikazi/CPC vraćeni na normalu (isti check kao posle junskog odblokiranja).

### 2026-07-10 [claude-code] — 🔴 ISPORUKA STALA od 2026-07-05 (nalaz nedeljnog izveštaja)
- Windsor dnevni raspad (2026-06-26 → 07-09): obe kampanje normalno isporučivale do **2026-07-04** (poslednji dan: Terase 621 RSD/123 impr, ECOTILE 380 RSD/40 impr), a od **2026-07-05 potpuni mrak** — 05/06/09.07 nula redova, 07–08.07 po 1 prikaz i 0 RSD potrošnje.
- Obe kampanje istovremeno = problem na nivou NALOGA, ne kampanja (isti potpis kao jun throttling: balans/billing/verifikacija). Windsor vraća podatke (nije konektor).
- Posledica u GA4: pad korisnika/sesija/lidova ove nedelje delom je direktno ovo (5 dana bez plaćenog saobraćaja).
- **#ceka-M: otvoriti Ads UI → Billing/Balans i proveriti zašto nalog ne servira od 05.07.** Ako je balans — dopuna; ako je nešto treće (policy/verifikacija ponovo), javiti pa dijagnostika.
- Kumulativ plaćenih konverzija od 01.06: **10** (prag za Maximize Conversions: 20–30) — blackout direktno usporava put ka Smart Bidding-u.

### 2026-07-06 [claude-code] — NEGATIVNE KW FIX ✅ (M2 / plan 4.1 zatvoreni)
- Izvoz iz Ads UI (`Files/Negative keyword details report.csv`, 44 negativne u listi) uporedjen sa [[CLAUDE]] §6 → **falilo 7**: `epoksi`, `epoksidni`, `epoksidnih`, `epoksidnog`, `betonski`, `"industrijski beton"`, `[podne obloge]`. Kritično: baš oblik **"epoksi" nije bio pokriven** — broad negativne nisu morfološke, `epoksidna` ne blokira `epoksidni`.
- Miroslav dodao u listu **13 negativnih**: gornjih 7 + `teraco`, `letvice`, `pevex`, `"uradi sam"`, `"keramičke pločice"`, `"podne pločice"` (phrase umesto broad `plocice` — da ne blokira "pvc pločice" upite iz ponude). Već postojale od ranije: deking, decking, wpc, ikea, marmoleum.
- Pauzirani pogrešni KW u Terasama: `bastenski namestaj` (164 RSD), `oprema za bazene` (144 RSD) — pauza, ne brisanje (čuva istoriju).
- Lista "AntasLine — univerzalne negativne" **potvrđena primenjena na obe aktivne kampanje**.
- `laminat` svesno NIJE dodat ([[CLAUDE]] §6: upiti se mogu konvertovati ka PVC) — watch: dodati tek ako troši bez konverzija.
- Efekat: zatvara ~16% curenja budžeta identifikovanog u snapshot-u 2026-07-04.

**Sledeći korak:** Faza 1 — RSA Terase (15 headlines + 4 descriptions iz banke) → Faza 2 ad grupe (terase/bazeni/bergo).

### 2026-07-04 [claude-code] — PUNI SNAPSHOT (16mo) → [[analiza/2026-07-04-snapshot-full]]
- **Jun = najveći spend mesec u 16 meseci (30,7k RSD)**; prelaz na nove kampanje početkom juna uspeo (stare pauzirane; "Ecotile - Antas line" pre pauze potrošila 10,1k/90d sa 0 konv — od toga "antistatik pod" broad 5,0k).
- **ECOTILE phrase "industrijski podovi" = 1.073 RSD/konv. (3 konv.)** ⭐ — skalirati. Obrazac: phrase konvertuje, broad rasipa.
- **Terase imp. share 24%, rank-lost 63%** → Quality Score problem → Faza 1–2 je pravi lek, ne budžet.
- 🔴 **Negativne NE važe:** epoksid/sika/rinol/poliuretan/marmoleum/topli-podovi prolaze kroz search terms (~2k RSD/90d vidljivo). Proveri listu na obe kampanje #ceka-miroslav
- 🔴 Pogrešni KW u Terasama: "bastenski namestaj" (164 RSD), "oprema za bazene" (144) — ukloniti. Novi kandidati za negativne: deking, drvene, plocice, teraco, letvice, pevex.
- "pvc podovi za bazene" broad = najveći KW Terasa (1.040 impr, 2,9k RSD, 0 konv) → svoja ad grupa + landing /podovi-za-bazene/ u Fazi 2.
- Uvoz konverzija radi tek od juna (6 konv; jul 1) — istorijske nule su nemerenje, ne nula lidova. Mobile = 87% potrošnje.

**Sledeći korak:** negativna lista fix (15 min) → Faza 1 RSA Terase → Faza 2 ad grupe (terase/bazeni/bergo).

### 2026-07-04 [claude-code]
- **Faza 0 zatvorena:** nalog odblokiran — dopuna balansa + verifikacija oglašivača završeni. Beleška `B3 - Odblokiranje naloga` obrisana (zadatak gotov), sve reference u vaultu ažurirane.
- ECOTILE više nije throttlovan na nivou naloga → status u tabeli: ⛔ zagušena → ✅ odblokirana.
- **Sledeći korak:** potvrditi u Ads da su ECOTILE prikazi/CPC vraćeni na normalu (uporedi sa snimkom 23–29.06), pa krenuti Faza 1 (RSA Terase) + Faza 2 (struktura ad grupa).

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

- ECOTILE zagušen: prikazi 802 → 261 (−67,5%), CPC 26 → 74 RSD. Uzrok = blokada naloga (balans + verifikacija; odblokirano 2026-07-04).
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
