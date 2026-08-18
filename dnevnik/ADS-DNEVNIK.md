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
azurirano: 2026-08-18
skill: antasline-ads
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

> [!warning] 🔴 Prag 20–30 NIJE dostignut — pravih plaćenih lidova ima **9**, ne 26 (stanje 2026-08-18)
> Svi Log unosi **zaključno sa 11.08** broje `Klik na telefon (web)` kao konverziju
> (akcija ima `include_in_conversions_metric=True` + `primary_for_goal=True`, protiv
> pravila [[CLAUDE]] §4). Od 01.06 do 10.08: **17 tel + 9 forma**. Kad u starijem unosu
> pročitaš „kumulativ 24/26, prag pređen" — **to ne važi**, v. ispravku 12.08 u Logu.
> Odluka **4.8 (Maximize Conversions) je odložena na ~01.09**, zatvorena u
> [[odluke/_pregled-odluka]] 13.08. Uslov za ponovno otvaranje: `Klik na telefon (web)`
> prebačen u *Secondary action* (#ceka-miroslav) **i** 20–30 lidova iz forme.
> 🟡 Uz to: posle migracije 25.08 GA4 `generate_lead` pada na ~⅓ a hvala-proxy na ~½
> (nestaje dupli GTM embed + tag id 18) — **to nije pad prodaje.**
> *(Upisano 2026-08-18 — konflikt #6 iz [[migracija/2026-08-12-preflight-checklist-24-08]].)*

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
- [x] Potvrda da su ECOTILE prikazi/CPC vraćeni na normalu — ✅ 2026-08-06: nalog-širok throttling potvrđeno prošao, ALI CPC i dalje raste (52,20→78,98 RSD) zbog kampanja-specifičnog dnevnog budžet cap-a (1.300 RSD) koji na spike-danima gubi 50% prikaza — v. Log 2026-08-06, #ceka-miroslav odluka o povećanju budžeta

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

### 2026-08-18 [claude-code] — 🔴 „Podloge za terase i bazene": status PAUSED i potrošnja postoje ISTOVREMENO (isprekidana isporuka, 4.571 RSD u 2 nedelje)

**Period 11–17.08 vs 04–10.08** (izvor: sopstveni konektor, `ads_report.py`,
nedeljni izveštaj W5 5.4).

Sumnja podignuta 17.08 (63 RSD u tri dana) je bila **potcenjena**:

| Period | Potrošnja | Klikovi | Prikazi | CTR | CPC | Konv. |
|---|---|---|---|---|---|---|
| 11–17.08 | 1.928,33 | 92 | 317 | 29,02% | 20,96 | 0 |
| 04–10.08 | 2.642,94 | 158 | 919 | 17,19% | 16,73 | 3 |

**Ključni nalaz — nije ni „pauzirana" ni „aktivna", nego oboje.** Ads API je
11.08 vratio `campaign_status: PAUSED` (provereno u
`analiza/2026-08-11-ads-final-urls.json` — dakle unos od 11.08 u ovom hubu i
audit §2.1 nisu pogrešno pročitali API), **ali je kampanja tog istog dana
potrošila 222 RSD / 14 klikova.** Dnevni presek:

| Dan | 08.08 | 09.08 | 10.08 | 11.08 | 12–15.08 | 16.08 | 17.08 |
|---|---|---|---|---|---|---|---|
| RSD | 225 | — | 897 | 222 | — | 63 | **1.643** |
| Klikovi | 15 | 0 | 52 | 14 | 0 | 4 | **74** |

Obrazac je isprekidan (pauze 09.08 i 12–15.08), sa **najvećim danom naloga
17.08** — 1.643 RSD / 74 klika, dan pre ovog izveštaja. Dve mogućnosti:
kampanja se ručno pali/gasi, ili pauza ne hvata sve (npr. deo asseta/ad grupa
ostaje ENABLED — `ad_group_status` i `ad_status` su 11.08 oba bili **ENABLED**
ispod PAUSED kampanje).

- 🟢 **Ne blokira migraciju 25.08.** Final URL-ovi kampanje su čisti:
  `/spoljnje-podne-obloge/` + `bergo-xl` / `bergo-unique` / `podovi-za-bazene` /
  `bergo-elite` — svi **200 na buildu** (`analiza/2026-08-11-ads-url-audit.csv`).
  Problem `ekopodneploce.rs` / mrtvih `/home/…` putanja tiče se drugih kampanja.
- 💰 **Najjeftiniji CPC u nalogu:** 20,96 vs 94,41 RSD na ECOTILE-u. Prošle
  nedelje je donela **3 od 5** uvezenih konverzija naloga. Ako se gasi, gasi se
  svesno — ne zato što „valjda već jeste pauzirana".
- 🟡 Prikazi 919 → 317 (−66%) uz CPC +25% i CTR 17% → 29% — ali to je ovde
  najverovatnije posledica dana-bez-isporuke, ne throttlinga kao kod ECOTILE-a
  u julu (v. Log 2026-08-06). Ne tumačiti kao pad tražnje.

🔴 **Metodološka lekcija (ide i u [[reference/naucene-lekcije]]):**
`campaign.status` iz Ads API-ja **nije dokaz da kampanja ne troši**. Status i
potrošnja se čitaju zajedno, po danu. Ovaj hub je 7 dana (11→18.08) vodio
kampanju kao pauziranu dok je trošila budžet, a isti zaključak je prepisan i u
[[migracija/2026-08-11-ads-final-url-audit]] §2.1 (tamo dopunjeno 18.08).
`[[CLAUDE]]` §6 („obe aktivne kampanje") je sve vreme bio bliži istini i
**ostaje nepromenjen**.

**Sledeći korak:** #ceka-miroslav — u Ads UI: (1) da li je kampanja ručno
paljena 10.08/17.08, (2) ako treba da bude pauzirana, proveriti status ad
grupa/asseta ispod nje, (3) ako treba da radi — vratiti je u evidenciju kao
aktivnu i pustiti Fazu 1 RSA banku koja je za nju spremna (v. Log 2026-08-05).

### 2026-08-12 [claude-code] — 🔴 ISPRAVKA: „kumulativ 26 / prag pređen" iz unosa 11.08 NE VAŽI — pravih plaćenih lidova ima 9

Unos od 11.08 (i svi raniji koji su brojali ka pragu 20–30) računa kao
konverzije i **klikove na telefon**. Mesečni snapshot istog dana je našao da
akcija `Klik na telefon (web)` ima `include_in_conversions_metric=True` +
`primary_for_goal=True`, dakle ulazi u „Conversions" kolonu **i u Smart
Bidding** — protiv pravila iz [[CLAUDE]] §4. Od 01.06 do 10.08:
**17 tel + 9 forma**.

- **Prag 20–30 NIJE dostignut.** Odluka 4.8 (Maximize Conversions) ostaje
  zatvorena dok se ne isključe **dve** aktivne telefonske akcije iz konverzija
  i dok se ne nakupi 20–30 lidova **iz forme**.
- Nalaz je bio upisan u [[PROGRESS]] Blokere 11.08, ali ne i ovde — pa je ovaj
  hub pet dana tvrdio suprotno. Otud ova ispravka; lekcija o razilaženju
  izvora: [[reference/naucene-lekcije]] (2026-08-12).
- Isto važi za GA4 stranu: hvala-proxy je **2× naduvan** suvišnim `page_view`
  tagom (id 18) koji preživljava migraciju. Za brojanje lidova koristiti
  **sesije** na `/hvala-za-poruku/`, ne preglede.

Novi skill **/antasline-ads** nosi ovu ispravku i pravilo: pri radu sa
konverzijama uvek proveriti `include_in_conversions_metric` /
`primary_for_goal` po akciji, ne verovati imenu akcije ni „Conversions" koloni.

**Sledeći korak:** #ceka-miroslav — isključiti obe telefonske akcije iz
„Conversions" u Ads UI.

### 2026-08-11 [claude-code] — W5 5.4 nedeljni presek: ECOTILE CPC 101 RSD (treći presek zaredom naviše), Terase pauzirana, plaćene kumulativ 26

**Period 04–10.08 vs 28.07–03.08** (izvor: sopstveni konektor, `ads_report.py`).

| Kampanja | Potrošnja RSD | Klikovi | CTR | CPC | Konv. |
|---|---|---|---|---|---|
| ECOTILE INDUSTRIJSKI PODOVI | 4.247,67 *(3.457,95)* | 42 *(54)* | 20,69% *(19,29%)* | **101,13** *(64,04)* | 2 *(2)* |
| Podloge za terase i bazene | 2.642,94 *(6.992,60)* | 158 *(366)* | 17,19% *(21,00%)* | 16,73 *(19,11)* | 3 *(4)* |
| **Ukupno** | **6.890,61** *(10.450,55)* | **200** *(420)* | — | — | **5** *(6)* |

**ECOTILE CPC serija se nastavlja naviše:** 52,20 (23–29.07) → 76,56 (30.07–05.08) → 78,98 (01–05.08 podskup) → **101,13 (04–10.08)**. Nije šum. Unutar nedelje: 08–10.08 potrošeno **2.357 RSD na 24 klika sa 0 konverzija** (CPC ~98). Uzrok dijagnostikovan još 06.08 i nepromenjen — dnevni budžet 1.300 RSD gubi ~50% prikaza na spike-danima. **#ceka-miroslav: 1.800–2.000 RSD ili svesno prihvatiti gubitak.**

⏸️ **„Podloge za terase i bazene" je PAUZIRANA** (zatečeno današnjim 4.10 auditom). Potrošnja je pala **−62%** *pre* pauze (6.992 → 2.643 RSD), klikovi 366 → 158, ali je kampanja trošila sve do 10.08. CPC joj je **16,73 RSD** — šestostruko jeftiniji od ECOTILE. **#ceka-miroslav: je li pauza namerna?** Ako nije, gubi se ~158 klikova nedeljno na najjeftinijem saobraćaju u nalogu.

**Plaćene konverzije kumulativ (od 01.06): 26** (bilo 24 na 06.08) — prag 20–30 pređen. **Preporuka za 4.8 se NE menja: odložiti Maximize Conversions na ~01.09**, jer bi period učenja (~14 dana) pao tačno na dan migracije kad se menjaju URL-ovi oglasa.

🔴 **Ograda na GA4 stranu izveštaja:** GA4 brojači konverzija su naduvani ~3× po sesiji (10 sesija na `/hvala-za-poruku/` → 39 `generate_lead`). **Ads-ova strana broji svoje (5) i nije naduvana isto** — ne izvoditi zaključke o Ads performansama iz GA4 brojača dok se uzrok ne nađe. v. [[dnevnik/2026-08-11-w5-nedeljni-izvestaj]].

---

### 2026-08-06 [claude-code] — W4 4.2 ECOTILE potvrda: CPC trend NIJE stao na 76,56, popeo se na 78,98 — drugi throttling potpis potvrđen + W4 4.6 overlap pravilo re-verifikovano

**4.2 — ECOTILE prikazi/CPC posle odblokiranja (2026-07-04): NIJE se vratilo na normalu.**
| Period | Impr. | CPC (RSD) | Napomena |
|---|---|---|---|
| 16–22.07 | 12 | 38,00 | blackout rep, n=1, šum |
| 23–29.07 | 246 | 52,20 | prva puna nedelja posle reaktivacije |
| 30.07–05.08 | 228 | 76,56 | +47% (već upisano juče) |
| 01–05.08 (podskup) | 161 | 78,98 | **potvrđuje trend, nije bio jednonedeljni šum** |

Za poređenje (iz junskog throttling incidenta, već u dnevniku): pre-throttle **802 impr / ~26 RSD CPC**, throttling špic **261 impr / 74 RSD CPC**. Trenutne brojke (150–250 impr, CPC sad **78,98 — iznad** junskog špica od 74) nose isti potpis koji [[CLAUDE]] §10 opisuje: "visok impression share + sitni apsolutni impressions + skok CPC = throttling na nivou naloga, ne pad tražnje". Terase kampanja u istom periodu ide suprotnim smerom (CPC 20,11→18,07→17,95 opadajuće, impresije rastu 218→1268→1065) — dakle problem NIJE tržišni niti nalog-širok (odblokiranje 07-04 je i dalje na snazi), izolovan je na ECOTILE kampanju specifično. Mogući uzroci van dosega konektora (read-only): dnevni budžet cap koji je postao preplitak za novi (viši) CPC opseg, ili Quality Score/Ad Rank efekat na uskom setu ključnih reči. **Nije izvršena nikakva izmena** (konektor je read-only za Ads write akcije) — upisano kao nalaz za Miroslava, v. Bloker ispod.

**4.6 — Pravilo preklapanja organik/plaćeno — sveže GSC (06.07–03.08) potvrđuje raniji zaključak iz [[analiza/2026-07-04-snapshot-full]], sa ažuriranim brojkama:**
- **Terase-cena klaster i dalje organski dominantan (NE plaćati baš na ove tačne fraze):** "gumeni podovi za terase cena" poz. 1,6 (215 impr, CTR 5,12%) · "gumene podloge za terasu" poz. 2,1 · "gumeni podovi za terase" poz. 3,2 · "podloge za terasu" poz. 2,6 — svi top-3.
- **Terase — mid-tail i dalje slab organski (vredi plaćati, i to Ads trenutno pokriva):** "podovi za terase" poz. 10,4 (292 impr) · "podne obloge za terasu" poz. 12,9 · "podloga za terasu" poz. 11,0 · "vinil podovi za terase" poz. 13,4 · "pvc podovi za terase" poz. 12,5.
- **Industrijski — široka fraza i dalje slaba (vredi plaćati, potvrđuje ECOTILE targeting):** "industrijski podovi" poz. 11,7 (175 impr) · "industrijski pod" poz. 11,4 · "industrijski podovi cena po m2" poz. 7,1. Uža fraza "pvc industrijski podovi" je top-3 (poz. 2,3) ali premali volumen/preusko da bi se posebno targetiralo.
- **Zaključak:** pravilo iz plana §4.6 i dalje tačno u smeru, brojke su ažurirane (industrijski poz. 11,7 umesto stare "poz. 11", terase-cena poz. 1,6–3,2 umesto stare "poz. 1,4"). Trenutna Ads targeting struktura (broad/phrase mid-tail termini) je ispravno pozicionirana — problem u ECOTILE-u je isporuka (4.2), ne pogrešan izbor ključnih reči.

**Dopuna — uzrok nađen preko `search_budget_lost_impression_share` (dnevni segment, 23.07–04.08):** ECOTILE dnevni budžet je **1.300 RSD** (Terase 800 RSD). Prosečna dnevna potrošnja ECOTILE-a (~460 RSD/dan) je duboko ispod tog cap-a, ALI na 2 od 12 dana (26.07, 31.07) budžet je izgubio **50% mogućih prikaza** zbog budžeta (ostali dani 0–12%). Dakle nije trajno budžetsko ograničenje (kao junski throttling na nivou celog naloga), nego **povremeni intraday spike** — dani sa visokom aukcijskom konkurencijom guraju CPC gore i budžet od 1.300 RSD se potroši pre kraja dana, gubeći polovinu prikaza baš tog dana. To objašnjava rastući prosečni CPC (skuplji klikovi preživljavaju kad budžet postane oskudan usred dana) bez pravog pada obima. **Ovo NIJE identičan potpis junskom throttling-u** (koji je bio nalog-širok, blokada/verifikacija) — ovo je kampanja-specifičan, budžet-nivo nalaz.

**Sledeći korak:** #ceka-miroslav — razmotriti podizanje ECOTILE dnevnog budžeta (npr. 1.300→1.800–2.000 RSD) da se apsorbuju spike dani bez gubitka prikaza; ne diram budžet sam (write akcija, M odluka). Alternativa bez trošenja više: ništa, prihvatiti povremeni 50% gubitak prikaza na najskupljim danima kao cenu trenutnog budžeta.

### 2026-08-06 [claude-code] — Kumulativ konverzija dostigao prag 20–30 (24) — odluka 4.8 otvorena za Miroslava
- 30.07–05.08 vs 23–29.07: potrošnja **9.142,12 RSD vs 8.010,35** (+14,1%), klikovi **345 vs 347** (stabilno), uvezene konverzije **6 vs 8** (−25%, jedna nedelja).
- **Terase**: 6.003,17 RSD / 304 klika / CTR 19,88% / CPC 19,75 / 5 konv (prethodno 5.348,35 / 296 / 23,34% / 18,07 / 3).
- **ECOTILE**: 3.138,95 RSD / 41 klik / CTR 17,98% / CPC 76,56 / 1 konv (prethodno 2.662 / 51 / 20,73% / 52,20 / 5) — CPC +47% uz pad klikova, prati se sledeći presek pre zaključka o uzroku.
- **Kumulativ (01.06→05.08, preračunat direktno iz GA4/Ads API-ja, ne sabiranjem nedeljnih brojki)**: hvala-proxy **109**, Ads uvezeno **24** — **prag 20–30 dostignut**.
- **Sledeći korak:** odluka 4.8 (Maximize Clicks → Maximize Conversions) otvorena — čeka Miroslavljevu potvrdu, promena strategije licitiranja ide kroz Ads UI (M akcija).

### 2026-08-05 [claude-code] — Faza 1 RSA Terase pripremljena za upis (char-limit provera)
- RSA banka za "Podloge za terase i bazene" (postoji od 2026-07-01) provereno karakter-po-karakter: 15/15 headline-a ≤30 karaktera, 4/4 descriptions ≤90 karaktera — ništa nije trebalo skraćivati, banka je spremna za copy-paste bez izmena.
- Upis u Ads UI ostaje M akcija (nije nešto što se piše preko API-ja bez eksplicitne potvrde). Predat Miroslavu formatiran tekst spreman za paste.
- **Sledeći korak:** M upisuje u Ads UI → javlja Ad Strength ocenu → CC upisuje rezultat i prelazi na Fazu 2 (ad grupe terase/bazeni/bergo).

### 2026-07-30 [claude-code] — Prva puna nedelja reaktivacije: potrošnja 8,7× veća, kumulativ 18 (blizu praga 20-30)
- 23–29.07 vs 16–22.07: potrošnja **8.010,35 RSD vs 922,65** (+768%), klikovi **347 vs 45** (+671%), uvezene konverzije **8 vs 0**.
- Obe kampanje pune isporuke: **Terase** 5.348,35 RSD / 296 klika / CTR 23,34% / CPC 18,07 / 3 konv · **ECOTILE** 2.662 RSD / 51 klik / CTR 20,73% / CPC 52,20 / 5 konv.
- ECOTILE CPC 52,20 RSD ostaje u istom opsegu kao prošlonedeljni nalaz (49,84) — potvrđuje se kao "novi normal" posle reaktivacije, ne novo zagušenje (junski throttling špic bio 74).
- **Plaćene konverzije kumulativ od 01.06: 18** (bilo 15) — **prag za Maximize Conversions (20–30) je sad na dohvat ruke**, pri ovom tempu (~8/ned od pune reaktivacije) prag se dostiže za manje od nedelju dana.
- **Sledeći korak:** ako sledeći izveštaj pokaže kumulativ ≥20, otvoriti odluku 4.8 (prelazak na Maximize Conversions) sa Miroslavom.

### 2026-07-27 [claude-code] — 🟢 KAMPANJE REAKTIVIRANE (kraj godišnjeg) — prvi podaci preko sopstvenog konektora
- 20–26.07 vs 13–19.07: potrošnja **6.029,65 RSD vs 0**, klikovi **263 vs 0**, prikazi 1.211 vs 1, uvezene konverzije **5 vs 0**. Blackout od 07-05 (22 dana) je završen.
- Reaktivacija je krenula **posle 21.07**: kumulativ 01.06→26.07 = 41.330,64 RSD, a kroz 21.07 je bio 35.301 RSD — razlika je tačno potrošnja ove nedelje.
- Obe kampanje rade: **Terase** 4.434,65 RSD / 231 klik / CTR 22,00% / CPC 19,20 / 2 konv · **ECOTILE** 1.595 RSD / 32 klika / CTR 19,88% / CPC 49,84 / 3 konv.
- ECOTILE CPC 49,84 RSD (junski throttling špic je bio 74, pre toga ~26) uz visok CTR i mali apsolutni volumen prikaza (161) — pratiti sledeću nedelju pre nego što se zaključi da je ovo novi normal, ne novo zagušenje.
- **Plaćene konverzije kumulativ od 01.06: 15** (bilo 10) — prag za Maximize Conversions (20–30) se ponovo pomera; pri ovom tempu (~5/ned) donja granica pada za ~1 nedelju.
- Prvi Ads izveštaj povučen sopstvenim konektorom (Windsor istekao 07-27) — OAuth + developer token rade, `ads_report.py` vraća pune podatke po kampanji.
- **Sledeći korak:** izveštaj sledeće nedelje = prva čista nedelja pune isporuke → tada odluka o Maximize Conversions (zadatak 4.8) ako kumulativ pređe 20.

### 2026-07-22 [claude-code] — Nedeljni izveštaj potvrđuje: pauza traje, još reaktivacije
- Windsor 15–21.07 vs 08–14.07: obe kampanje 0 RSD/0 klikova oba perioda (samo 1–2 stray impresije/nedelja). Miroslav potvrdio ove sesije: "ads je na pauzi" — i dalje namerno.
- Plaćene konverzije kumulativ jun–21.07 i dalje 10 (35.301 RSD) — nepromenjeno od 07-21 nalaza, očekivano dok se ne reaktivira.
- Nema akcije dok M ne da signal za reaktivaciju.

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
