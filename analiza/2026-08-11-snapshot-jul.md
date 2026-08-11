---
tip: analiza-snapshot
datum: 2026-08-11
period: "jul 2026 (mesečni) · 90d preseci 13.05–10.08 · GSC trend 16mo"
izvori: [ga4, gsc, google-ads, gmb]
alat: sopstveni konektor (`.claude/skills/antasline-konektor`)
status: gotov
prethodni: "[[analiza/2026-07-04-snapshot-full]]"
---

# 📊 SNAPSHOT — jul 2026 (Ads · GA4 · GSC · GMB)

> Mesečni snapshot za **jul**, rađen sa zakašnjenjem od 11 dana (W5 5.4).
> Baseline za poređenje: [[analiza/2026-07-04-snapshot-full]].
> 🔴 **Svi GA4 brojevi su `--live-only`** (bez `localhost`/`staging`) — jul je
> imao 1.075 pregleda sa lokalnog builda (15% ukupnog). Sirovi brojevi iz
> ranijih izveštaja nisu uporedivi sa ovima.
> 🔴 **Korekcija merenja primenjena gde je naznačeno** (hvala-proxy ÷2,
> `generate_lead` ÷3) — v. [[dnevnik/2026-08-11-generate-lead-inflacija-dijagnoza]].

---

## 0. REZIME — top nalazi

1. 🔴 **„26 plaćenih konverzija" nisu lidovi — 17 od 26 su klikovi na telefon.**
   Konverziona akcija **„Klik na telefon (web)"** ima `include_in_conversions_metric = True`
   i `primary_for_goal = True`, dakle **ulazi u „Conversions" kolonu i u Smart
   Bidding**. To je direktno kršenje pravila iz [[CLAUDE]] §4 („Ne uvoziti GA4
   `tel` kao Ads konverziju — double-counting"). Od 01.06 do 10.08: **17 tel +
   9 forma**. **Prag 20–30 za zadatak 4.8 NIJE dostignut — pravih plaćenih
   lidova ima 9.** Ovo menja preporuku za 4.8 iz „odloži zbog tajminga" u
   „odloži jer prag nije ni pređen".
2. 🔴 **KPI baseline „55 pravih konverzija/mes (jun)" je pregled-brojka, ne
   broj lidova.** Jun: 55 pregleda = **24 sesije** (est. ~28 dolazaka).
   Jul: 36 pregleda = **16 sesija** (est. 18). Cela KPI tabla u
   [[2026-07-06-MASTER-PLAN-V2]] §5 meri pogrešnu jedinicu — ciljevi
   „održati ≥55" i „70+/mes" su postavljeni na naduvan baseline.
3. 🟢 **Organski: pozicija se popravlja, klikovi padaju — i to je očekivano.**
   Jul YoY: pozicija **8,2 → 6,0**, prikazi **34.595 → 42.168 (+22%)**, ali
   CTR **6,76% → 4,52%** i klikovi **2.339 → 1.908 (−18%)**. Više smo vidljivi
   nego ikad, a manje klikani — klasičan obrazac AI Overviews / SERP funkcija.
   Rad na title/meta (2.3) nije uzrok pada.
4. 🟢 **Ads je u julu bio dvostruko efikasniji nego u junu**: 14 konverzija za
   16.679 RSD (jul) vs 7 za 30.684 RSD (jun) — pola potrošnje, duplo konverzija.
   ⚠️ Ali 11 od tih 14 su tel-klikovi (v. nalaz 1); pravih forma-lidova: 3.
5. 🔴 **ECOTILE CPC eskalira treći mesec zaredom: 31 → 54 → 90 RSD.**
   Terase stabilne na 17–19 RSD. Uzrok (budžet gubi 50% prikaza na spike-danima)
   dijagnostikovan 06.08, odluka o budžetu i dalje čeka.
6. ⚪ **`mailto` = 0 u julu je artefakt, ne pad** — event je bio mrtav 27.06→06.08
   jer ga je pratio MonsterInsights (gašenje MI-ja u BLOK A ga je oborilo), a
   popravljen je **07.08** (GTM Version 14). Merenje po danu to potvrđuje: prvi
   događaj posle popravke 07.08, stopa se vratila na ~0,5/dan.
   → [[dnevnik/2026-08-07-gtm-mailto-tag]]
7. 🟢 **AI saobraćaj raste**: jul 28 sesija (baseline jul 2026 u planu: 9/90d).
   ChatGPT nosi 26 od 28.

---

## 1. GSC

### 1.1 Trend po mesecima (16 meseci)

| mesec | klikovi | prikazi | CTR |
|---|---|---|---|
| 2025-04 | 3.228 | 43.856 | 7,36% |
| 2025-05 | 3.230 | 45.009 | 7,18% |
| 2025-06 | 2.631 | 37.271 | 7,06% |
| 2025-07 | 2.339 | 34.595 | 6,76% |
| 2025-08 | 2.198 | 35.711 | 6,15% |
| 2025-09 | 2.160 | 35.388 | 6,10% |
| 2025-10 | 1.915 | 33.191 | 5,77% |
| 2025-11 | 1.739 | 33.497 | 5,19% |
| 2025-12 | 1.452 | 30.880 | 4,70% |
| 2026-01 | 1.608 | 31.449 | 5,11% |
| 2026-02 | 2.347 | 41.883 | 5,60% |
| 2026-03 | 3.111 | 50.186 | 6,20% |
| 2026-04 | 2.792 | 45.731 | 6,11% |
| 2026-05 | 2.484 | 44.022 | 5,64% |
| 2026-06 | 2.149 | 45.028 | 4,77% |
| **2026-07** | **1.908** | **42.168** | **4,52%** |
| 2026-08 (1–8) | 427 | 9.739 | 4,38% |

**Čitanje:** prikazi su na nivou prošle godine i više (jun 2026 je bio
**rekordan mesec po prikazima** u celoj seriji — 45.028), ali CTR pada
monotono od aprila 2025 (7,36% → 4,52%, **−38% relativno**). Klikovi prate CTR,
ne prikaze.

### 1.1b YoY jul — jedina poštena komparacija (sezonski špic je mar–maj)

| | klikovi | prikazi | CTR | pozicija |
|---|---|---|---|---|
| jul 2025 | 2.339 | 34.595 | 6,76% | 8,2 |
| **jul 2026** | **1.908** | **42.168** | **4,52%** | **6,0** |
| Δ | −18% | **+22%** | −2,24 p.p. | **−2,2 (bolje)** |

🔴 **Ovo je najvažniji SEO nalaz snapshot-a:** rangiranje se **popravilo za
2,2 pozicije**, vidljivost porasla 22%, a klikovi ipak pali 18%. Kad pozicija
raste a CTR pada, uzrok nije na sajtu — klik se troši u SERP-u (AI Overviews,
People Also Ask, Maps paket). Praktična posledica: **GEO/AI vidljivost (2.8,
5.5) prestaje da bude nice-to-have** i postaje glavni kanal odbrane.

### 1.2 Top upiti 90d (13.05–10.08, po klikovima)

| upit | kl. | prikazi | CTR | poz |
|---|---|---|---|---|
| antas line | 108 | 195 | 55,4% | 1,5 |
| gumeni podovi za terase cena | 51 | 805 | 6,3% | 1,5 |
| podloga za košarkaški teren | 45 | 113 | 39,8% | 1,7 |
| podloga za kosarkaski teren cena | 41 | 153 | 26,8% | 2,0 |
| gumeni tepih za terasu | 39 | 469 | 8,3% | 1,1 |
| teren za basket dimenzije | 28 | 188 | 14,9% | 1,5 |
| plasticne staze za dvoriste | 27 | 280 | 9,6% | 1,3 |
| dimenzije table za kos | 24 | 363 | 6,6% | 1,2 |
| dimenzije kosarkaskog terena | 22 | 554 | 4,0% | 1,3 |
| gumene podloge za terasu | 21 | 482 | 4,4% | 1,7 |
| vestacka trava za fudbal | 21 | 63 | 33,3% | 1,3 |
| koš sa konstrukcijom | 15 | 173 | 8,7% | 8,6 |

⚠️ **Obrazac vredan pažnje:** upiti na poziciji **1,0–1,7** imaju CTR od 2,3%
do 8,3% (`dimenzije košarkaškog terena`: poz **1,9** → CTR **2,3%**, 732
prikaza, 17 klikova). Na prvoj poziciji CTR bi trebalo da bude 25–35%.
Potvrđuje nalaz 1.1b iz drugog ugla: **odgovor se čita u SERP-u** (dimenzije
su tačno tip upita koji AI Overview/snippet reši bez klika).

### 1.3 CTR rupe 90d (poz. 5–15, ≥200 prikaza) — prilike

| upit | kl. | prikazi | CTR | poz | napomena |
|---|---|---|---|---|---|
| epoksidni podovi | 4 | 830 | 0,5% | 11,3 | conquest 2542 |
| industrijski podovi | 9 | 686 | 1,3% | 11,3 | glavni B2B klaster |
| podovi za terase | 15 | 606 | 2,5% | 12,1 | |
| epoksidni podovi cena po m2 | 4 | 596 | 0,7% | 10,0 | conquest 2542 |
| podne obloge za dvoriste | 7 | 477 | 1,5% | **5,4** | najbliža prilika |
| vinil podovi za terase | 4 | 313 | 1,3% | **5,1** | najbliža prilika |
| podne obloge za terasu | 6 | 271 | 2,2% | 14,4 | |
| podovi za radionice | 3 | 217 | 1,4% | 6,6 | |
| konstrukcija za kos | 9 | 216 | 4,2% | 6,5 | |
| podloga za terasu | 9 | 211 | 4,3% | 11,2 | |
| ftalati | 4 | 210 | 1,9% | 5,8 | informativan, nisko-vredan |
| bergo podloge | 9 | 200 | 4,5% | 6,8 | brend |

Dva **epoksid** upita zajedno: 1.426 prikaza, 8 klikova. To je najveći
pojedinačni izvor prikaza u ovoj tabeli i cilja ga postojeći conquest članak —
ali sa pozicije 10–11 conquest ne radi ništa. Isti nalaz je izašao i u
nedeljnom izveštaju 11.08.

### 1.4 Top stranice 90d

| stranica | kl. | prikazi | CTR | poz |
|---|---|---|---|---|
| /kako-napraviti-teren-za-basket-ili-kosarkaski-teren | 840 | 26.256 | 3,2% | 2,6 |
| /sportske-podloge/ | 794 | 7.764 | 10,2% | 5,7 |
| /podloge-za-parkiraliste-i-staze/ | 557 | 6.836 | 8,1% | 5,2 |
| /sportske-podloge/kosarkaske-konstrukcije/ | 389 | 6.306 | 6,2% | 8,4 |
| /spoljnje-podne-obloge/ | 312 | 10.278 | 3,0% | 11,2 |
| /vestacka-trava/ | 296 | 3.660 | 8,1% | 5,4 |
| / | 258 | 2.878 | 9,0% | 7,2 |
| /spoljnje-podne-obloge/bergo-xl/ | 226 | 4.590 | 4,9% | 5,2 |
| /industrijski-podovi/ | 204 | 4.858 | 4,2% | 11,4 |
| /podloga-za-teniske-terene/ | 180 | 6.019 | 3,0% | 5,9 |
| /antistatik-i-elektroprovodljivi-podovi/ | 154 | 2.538 | 6,1% | 6,7 |
| /iznajmljivanje-podova/ | 81 | 272 | **29,8%** | 4,7 |

Jedna stranica (`basket`) nosi **26.256 prikaza = 62% ukupnih prikaza top-20**
uz CTR 3,2% — najveća pojedinačna CTR poluga na sajtu. `/industrijski-podovi/`
i `/spoljnje-podne-obloge/` su na poziciji **11+** uprkos rebuild-u: to su
stranice koje treba gurati posle migracije.

### 1.5 Uređaji 90d

| uređaj | kl. | prikazi | CTR | poz |
|---|---|---|---|---|
| MOBILE | 4.548 | 96.981 | 4,7% | 5,4 |
| DESKTOP | 1.508 | 28.240 | 5,3% | 8,2 |
| TABLET | 37 | 693 | 5,3% | 13,0 |

Mobilni udeo prikaza **77%** — nepromenjeno od baseline-a (76%). Potvrđuje da
je LCP na mobilnom (jedina crvena gate stavka) najskuplji otvoreni tehnički dug.

### 1.6 Movers, 28d (12.07–08.08) vs prethodnih 28d

| upit | pre | sad | Δ |
|---|---|---|---|
| gumeni tepih za terasu | 8 | 20 | **+12** |
| dimenzije terena za basket | 5 | 10 | +5 |
| podloga za parking | 2 | 7 | +5 |
| kosarkaski kos za dvoriste | 0 | 5 | +5 |
| izgradnja teniskog terena cena | 8 | 2 | −6 |
| podloga za košarkaški teren | 21 | 8 | **−13** |
| antas line | 33 | 18 | **−15** |

Pad brend upita (`antas line` −15) uz istovremen pad `podloga za košarkaški
teren` je sezonski (kraj sportske sezone), ne signal problema — pratiti posle
migracije.

---

## 2. GA4 (live-only)

### 2.1 Trend po mesecima

| mesec | korisnici | sesije | pregledi | engagement |
|---|---|---|---|---|
| 2025-07 | 2.694 | 3.271 | 6.280 | 58,6% |
| 2025-08 | 2.650 | 3.478 | 5.666 | 54,1% |
| 2026-03 | 4.644 | 6.271 | 8.815 | 49,0% |
| 2026-04 | 4.058 | 5.326 | 7.610 | 48,3% |
| 2026-05 | 3.599 | 4.736 | 7.219 | 48,6% |
| 2026-06 | 4.158 | 5.430 | 8.036 | 54,6% |
| **2026-07** | **2.833** | **3.322** | **5.968** | **62,1%** |

- MoM: korisnici **−32%** (4.158 → 2.833) — izgleda dramatično
- **YoY: +5%** (jul 2025: 2.694 → jul 2026: 2.833) — sezonski pad, ne regresija
- 🟢 **Engagement rate je najviši u celoj seriji: 62,1%** (prosek ~50%). Manje
  posetilaca, ali kvalitetnijih — poklapa se sa GSC nalazom da nam ostaju
  klikovi sa jače namere dok informativne odgovara SERP.

### 2.2 Kanali

| kanal | jul: sesije | 90d: sesije |
|---|---|---|
| Organic Search | 2.386 (72%) | 6.746 |
| Paid Search | 520 (16%) | 2.164 |
| Direct | 379 (11%) | 2.703 |
| Referral | 29 | 82 |
| AI Assistant | 25 | 36 |
| Organic Social | 14 | 61 |
| Unassigned | 16 | 956 |

Organic nosi **skoro tri četvrtine** saobraćaja — potvrđuje prioritet
„ne izgubiti ništa na migraciji" nad svime ostalim.

### 2.3 Ključni eventi po mesecima (SIROVO — v. korekciju ispod)

| mesec | generate_lead | tel | mailto |
|---|---|---|---|
| 2026-03 | 141 | 104 | 14 |
| 2026-04 | 125 | 77 | 9 |
| 2026-05 | 159 | 81 | 9 |
| 2026-06 | 124 | 69 | **16** |
| **2026-07** | **54** | **59** | **0** |

⚪ **`mailto` = 0 u julu je artefakt prekida, ne pad.** Event je pratio
MonsterInsights; gašenje MI-ja (BLOK A) ga je oborilo **27.06**, a GTM zamena
(nov trigger + tag, **Version 14**) objavljena je **07.08** →
[[dnevnik/2026-08-07-gtm-mailto-tag]]. Merenje po danu (live-only) potvrđuje
popravku: poslednji događaj pre prekida **26.06**, prvi posle **07.08**, pa
09.08 — **2 događaja / 4 dana ≈ 0,5/dan, ista stopa kao u junu** (16/mes).
🔴 **Ne porediti jul sa junom ni avgustom na ovoj metrici** — meri se prekid,
ne ponašanje korisnika.

⚠️ `generate_lead` pre jula sadrži i istorijski rep (event je nekad okidao na
`/kontakt/`) — brojevi do juna nisu uporedivi sa julskim ni posle korekcije.

### 2.3b Hvala-proxy — jedina serija koja stvarno meri lidove

| mesec | pregledi | **sesije** | est. dolasci (pv÷2) |
|---|---|---|---|
| 2026-06 | 55 | **24** | 28 |
| 2026-07 | 36 | **16** | 18 |
| 2026-08 (1–10) | 28 | **11** | 14 |
| **kumulativ 01.06–10.08** | **119** | **51** | **60** |

🔴 **Ovim je KPI baseline oboren:** plan kaže „prave konverzije 55/mes (jun),
cilj održati ≥55, +60d posle live-a 70+/mes". Stvarni jun je **24 sesije**.
Realan cilj treba postaviti na tu skalu (npr. „≥25/mes, cilj 35+"), inače je
KPI tabla trajno crvena bez razloga.

Jul (16) je **−33% vs jun** (24), što prati opšti sezonski pad saobraćaja
(−32% korisnika) skoro tačno — **stopa konverzije je stabilna**, pao je promet.

### 2.4 Uređaji i geo 90d

| uređaj | sesije | udeo |
|---|---|---|
| mobile | 8.953 | 70% |
| desktop | 3.719 | 29% |
| tablet | 61 | 1% |

| zemlja | sesije |
|---|---|
| Srbija | 9.991 |
| Hrvatska | 595 |
| BiH | 569 |
| Crna Gora | 414 |
| Nemačka | 111 |

Region (HR+BA+ME) = **1.578 sesija/90d ≈ 12%** — nezanemarljivo, a nema
nijedne stranice ni kampanje koja ih cilja. Kandidat za post-live razmatranje.

### 2.5 Top landing stranice — jul

| putanja | sesije |
|---|---|
| /spoljnje-podne-obloge | 600 |
| /kako-napraviti-teren-za-basket… | 349 |
| /sportske-podloge | 304 |
| /podloge-za-parkiraliste-i-staze | 241 |
| / | 193 |
| /industrijski-podovi | 167 |
| /spoljnje-podne-obloge/bergo-xl | 153 |
| /sportske-podloge/kosarkaske-konstrukcije | 138 |

### 2.6 Publike
**Nema podataka** — GA4 Data API ne izlaže veličinu publike; `active_users`
po `audience_name` traži custom dimenziju koja ne postoji. Poznato ograničenje
([[reference/naucene-lekcije]]), rešava se tek kroz BigQuery export (5.9, čeka M15).

---

## 3. GOOGLE ADS (RSD)

### 3.1 Trend po mesecima

| mesec | potrošnja | klikovi | konverzije | RSD/konv |
|---|---|---|---|---|
| 2026-05 | 3.822 | 193 | 0 | — |
| 2026-06 | 30.684 | 1.395 | 7 | 4.383 |
| **2026-07** | **16.679** | **688** | **14** | **1.191** |
| 2026-08 (1–10) | 11.309 | 379 | 5 | 2.262 |

🟢 Jul je **najefikasniji mesec u seriji**: pola junske potrošnje, duplo
konverzija, cena po konverziji pala **−73%**.
⚠️ Uz obaveznu ogradu iz nalaza 1: 11 od 14 julskih „konverzija" su tel-klikovi.

### 3.2 Kampanje 90d (13.05–10.08)

| kampanja | status | RSD | kl. | konv | IS | izgub. rank | izgub. budžet |
|---|---|---|---|---|---|---|---|
| Podloge za terase i bazene | **PAUSED** | 34.318 | 1.874 | 13 | 26% | 55% | 19% |
| ECOTILE INDUSTRIJSKI PODOVI | ENABLED | 24.148 | 576 | 13 | 36% | 52% | 13% |
| Ecotile - Antas line | PAUSED | 2.175 | 111 | 0 | 47% | 44% | 8% |
| podovi za baste | PAUSED | 3 | 3 | 0 | 10% | 88% | 8% |

🔴 **Terase su pauzirane a nose isto konverzija kao ECOTILE uz 3× jeftiniji
klik** (18,3 vs 41,9 RSD). Pitanje „je li pauza namerna" stoji otvoreno od
jutros — ovaj presek ga pojačava: pauzirana je efikasnija kampanja.

**Izgubljeni prikazi zbog ranga** su 52–55% na obe glavne kampanje — to je
Quality Score/bid problem, ne budžetski (budžet gubi 13–19%). Faza 1–2 iz
[[dnevnik/ADS-DNEVNIK]] (RSA + ad grupe) cilja baš to.

### 3.3 Ključne reči 90d (po potrošnji)

| ključna reč | tip | RSD | kl. | konv | RSD/konv | CPC |
|---|---|---|---|---|---|---|
| pvc podovi za bazene | BROAD | 10.197 | 545 | 6 | 1.700 | 19 |
| **industrijski podovi** | **PHRASE** | **8.130** | **123** | **9** | **903** ⭐ | 66 |
| podovi za terase | PHRASE | 4.489 | 255 | 2 | 2.244 | 18 |
| podne obloge za terase | PHRASE | 4.116 | 232 | 3 | 1.372 | 18 |
| podovi za radionice | PHRASE | 3.702 | 58 | 2 | 1.851 | 64 |
| podovi za terase | BROAD | 2.440 | 104 | **0** | — | 23 |
| podloga za terase | PHRASE | 2.289 | 126 | 1 | 2.289 | 18 |
| industrijski podovi | BROAD | 1.645 | 66 | **0** | — | 25 |
| podne obloge za terase | BROAD | 1.610 | 82 | **0** | — | 20 |
| pvc podne ploče | BROAD | 1.500 | 59 | **0** | — | 25 |
| podovi za hale | BROAD | 1.397 | 56 | **0** | — | 25 |
| antistatik pod | PHRASE | 1.299 | 21 | **0** | — | 62 |

🟢 **`industrijski podovi` (phrase) ostaje najjeftinija konverzija u nalogu**
(903 RSD/konv) — potvrđuje zadatak 4.5 (skalirati je).
🔴 **BROAD varijante su čist gubitak:** 6 broad ključnih reči potrošilo
**~10.300 RSD za 0 konverzija**. Pravilo iz plana („broad tek uz Smart
Bidding") je prekršeno u praksi — broad radi *sada*, dok je nalog na Maximize
Clicks. Najjeftinija pojedinačna ušteda u celom nalogu.

### 3.4 Search terms 90d — otpad i kandidati za negativne

| upit | RSD | kl. | konv |
|---|---|---|---|
| podne obloge za terasu | 4.223 | 237 | **0** |
| industrijski podovi | 2.393 | 46 | 4 |
| podovi za terase | 1.546 | 89 | **0** |
| industrijski podovi cena po m2 | 1.396 | 25 | **0** |
| gumeni podovi za terase cena | 833 | 49 | **0** |
| vinilis podovi | 619 | 34 | **0** |
| deking cena | 354 | 2 | **0** |
| epoksidna smola za beton cena | 233 | 9 | **0** |
| jysk vestacka trava za terasu | 212 | 3 | **0** |
| kameni podovi za terase | 195 | 2 | **0** |

**Kandidati za negativne** (van ponude, potrošili bez ijedne konverzije):
`deking` · `epoksidna smola` (⚠️ *ne* `epoksid` u celini — conquest!) ·
`jysk` · `kameni podovi`. Ukupno ~1.000 RSD/90d — malo, ali besplatno.
🔴 Veći problem nije otpad nego **`podne obloge za terasu`: 4.223 RSD, 237
klikova, 0 konverzija** — to je relevantan upit iz ponude sa nula rezultata,
dakle problem landinga ili ponude, ne targetiranja.

### 3.5 Uređaji 90d

| uređaj | RSD | kl. | prikazi | konv |
|---|---|---|---|---|
| MOBILE | 53.444 | 2.327 | 12.256 | 22 |
| DESKTOP | 6.996 | 228 | 1.686 | 4 |
| TABLET | 204 | 9 | 91 | 0 |

Mobilni = **88% potrošnje** (baseline 87%). Zadatak 4.9 (mobilni bid +15–20%)
i dalje ima smisla, ali tek posle LCP-a — plaćamo premium za saobraćaj koji
sleće na najsporiju verziju sajta.

### 3.6 🔴 Konverzione akcije — šta se zapravo broji

| akcija | tip | u „Conversions"? | primarna? | konv 01.06–10.08 |
|---|---|---|---|---|
| **Klik na telefon (web)** | WEBPAGE | **DA** | DA | **17** |
| **Lead - forma (GTM)** | WEBPAGE | **DA** | DA | **9** |
| Pozivanje klikom na telefon na sajtu | CLICK_TO_CALL | DA | DA | 0 |
| YouTube channel subscriptions | — | DA | DA | 0 |
| YouTube follow-on views | — | DA | DA | 0 |
| Clicks to call | GOOGLE_HOSTED | ne | DA | 0 (3 „all") |
| Local actions ×3 | GOOGLE_HOSTED | ne | DA | 0 (4+1+6 „all") |

Po mesecima: jun **5 forma + 2 tel** · jul **3 forma + 11 tel** · avgust (1–10)
**1 forma + 4 tel**.

🔴 **Posledice:**
1. Svaki dosadašnji izveštaj koji je rekao „plaćene konverzije: 26" mešao je
   dve stvari. Pravi broj plaćenih **lidova** od mesec-nule je **9**.
2. Prag za **4.8 (Maximize Conversions) nije dostignut** — Smart Bidding bi
   učio na signalu koji je 65% tel-klikovi. Preporuka „odložiti na ~01.09"
   ostaje, ali sada iz jačeg razloga.
3. Postoje **dve** aktivne telefonske akcije (`Klik na telefon (web)` +
   `Pozivanje klikom na telefon na sajtu`) — druga trenutno ima 0, ali ako
   proradi, telefon se broji dvaput.
4. ⚠️ GA4 `tel` je verovatno i sam naduvan (live ima dva GTM embeda, v.
   dijagnozu 11.08) — **nije izmereno za `tel`**, samo za `generate_lead`.
   Ads strana je `ONE_PER_CLICK`, pa je otpornija.

**#ceka-miroslav:** isključiti `Klik na telefon (web)` iz „Conversions"
(Ads → Goals → Conversions → akcija → *Secondary action*). Bezopasno, ne
briše podatke, vraća izveštavanje u sklad sa [[CLAUDE]] §4.

---

## 4. GMB
**Nema podataka za GMB** — `mybusinessaccountmanagement.googleapis.com` vraća
**429 (quota)**, peti neuspeli retest zaredom (nepromenjeno od 30.07). Google-ova
ručna revizija Basic API Access zahteva još traje; nema akcije na našoj strani.

---

## 4.5 AI VIDLJIVOST (GEO)

| metrika | jul 2026 | ranije |
|---|---|---|
| AI sesije (stvarne, `ai_report.py`) | **28** | baseline 9 korisnika/90d |
| GA4 kanal „AI Assistant" | 25 | — |
| podbačaj kanala | 3 | ranije mereno ~3× |

Po izvoru: **chatgpt.com 26** · gemini.google.com 1 · perplexity 1.
Top landing: `/` (11) · `/o-nama` (3) · `/podloge-za-parkiraliste-i-staze` (2) ·
`/proizvod/ecotile-e500-10…` (2).

⚠️ **Mesečni AI test (5 fiksnih promptova, zadatak 5.5) NIJE ponovljen ovog
meseca** — poslednji je 22.07 ([[analiza/2026-07-22-ai-test-baseline]], 2/5
pominjanja). Ostaje kao zaseban zadatak; ne izmišljam rezultate.

---

## 5. CROSS-SOURCE

- **GSC pozicija ↑ + CTR ↓ + GA4 engagement ↑** su tri merenja iste pojave:
  gubimo informativne klikove (odgovor u SERP-u), zadržavamo transakcione.
  Strategija koja iz toga sledi nije „više sadržaja", nego **više razloga za
  klik** (cena, kalkulator, planer terena, galerija) na stranicama koje već
  rangiraju u top 3.
- **Ads i organik se preklapaju na terasama**: organski smo poz. 1,1–1,7 na
  `gumeni tepih za terasu`/`gumeni podovi za terase cena`, a plaćamo
  4.223 RSD za `podne obloge za terasu` bez konverzije. Pravilo 4.6
  (ne plaćati terase-cena klaster) i dalje važi i sad ima svežu potvrdu.
- **Provera prošlih preporuka:**
  - 4.1 negativne (06.07) — ✅ radi: otpad u search terms je pao na ~1.000
    RSD/90d, nema više „bastenski namestaj"/„bazeni" klase upita
  - 2.3 title/meta (08.07) — ⚠️ **efekat se ne vidi u klikovima** jer ga
    poklapa CTR erozija cele SERP-e; pozicija jeste bolja (8,2→6,0 YoY)
  - 4.2 ECOTILE budžet (06.08) — ❌ nije izvršeno, CPC nastavio 54 → 90

---

## 6. STRATEGIJA

### 6.1 SEO
| Nalaz | Akcija | Očekivano |
|---|---|---|
| basket stranica: 26.256 prikaza / CTR 3,2% / poz 2,6 | title/meta + FAQ rich snippet refresh **posle live-a** | +1 p.p. CTR = ~260 kl./90d |
| `/industrijski-podovi/` poz. 11,4 uz 4.858 prikaza | interno linkovanje + refresh posle migracije | najveći B2B potencijal |
| epoksid klaster: 1.426 prikaza, poz. 10–11 | conquest 2542 treba ojačati (ne novu stranicu) | conquest ne radi sa poz. 11 |
| CTR erozija sitewide | GEO paket (2.8 ✅) + mesečni AI test (5.5) | odbrana kanala |

### 6.2 Ads
1. 🔴 **Isključiti `Klik na telefon (web)` iz „Conversions"** (M, 2 min) — bez
   toga svaka buduća odluka o bidding-u ide na pogrešnom signalu
2. 🔴 **Pauzirati 6 BROAD ključnih reči** sa 0 konverzija (~10.300 RSD/90d
   spaseno) — plan ionako kaže „broad tek uz Smart Bidding"
3. 🟡 Odluka o pauzi kampanje **Terase** (efikasnija je od ECOTILE-a)
4. 🟡 ECOTILE budžet (odluka od 06.08 još stoji) — CPC 90 RSD i raste
5. 4 negativne: `deking`, `epoksidna smola`, `jysk`, `kameni podovi`
6. **4.8 NE pokretati** — pravih plaćenih lidova je 9, ne 26

### 6.3 GMB
Blokirano na Google kvoti. Jedina neblokirana stavka ostaje 5.3 (recenzije
6 → 20+), koja ne zavisi od API-ja.

### 6.4 Tracking
1. Obrisati GA4 tag **id 18** (dupli `page_view`) — kandidat za dan migracije
2. Ispraviti KPI tablu u planu na **sesije** umesto pregleda (§2.3b)
3. ✅ `mailto` — nema šta da se proverava, popravljen 07.08 (GTM v14) i
   merenje potvrđuje povratak na normalnu stopu; jul = 0 je artefakt prekida
4. Odluka o trajnom `hostName` filteru (danas dodat kao `--live-only` flag)

---

## 7. AKCIJA NEDELJE
**Isključiti `Klik na telefon (web)` iz „Conversions" kolone u Google Ads-u —
dve minute rada koje vraćaju jedini brojač po kom se donosi odluka o Smart
Bidding-u (9 pravih lidova, ne 26) u sklad sa pravilom iz [[CLAUDE]] §4.**

---

## Veze
[[analiza/2026-07-04-snapshot-full]] (prethodni) ·
[[dnevnik/2026-08-11-w5-nedeljni-izvestaj]] ·
[[dnevnik/2026-08-11-generate-lead-inflacija-dijagnoza]] ·
[[2026-07-06-MASTER-PLAN-V2]] §5 (KPI tabla — traži ispravku) ·
[[dnevnik/ADS-DNEVNIK]] · [[PROGRESS]]
