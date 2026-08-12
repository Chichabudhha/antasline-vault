---
name: antasline-ads
description: Google Ads rad na AntasLine nalogu (156-886-0314) — dijagnostika isporuke, licitiranje, negativne reči, RSA, budžeti, konverzije, migracija oglasa. Koristi kad Miroslav kaže "ads", "oglasi", "kampanja", "CPC", "budžet", "konverzije", "Maximize Conversions", "negativne", "RSA", "PMax", "Demand Gen" ili pri W4 zadacima.
---

# Google Ads — AntasLine playbook

CLAUDE.md nosi identifikatore i opšta pravila. Ovaj skill nosi **kako se donose
Ads odluke na ovom nalogu**: šta smem sam, šta ide Miroslavu, kojim redom se
dijagnostikuje i koje su zamke specifične za srpsko tržište i naš setup.

Nalog `156-886-0314` · valuta **RSD** · živi hub: `[[dnevnik/ADS-DNEVNIK]]`

## 1. Pre bilo kakve tvrdnje — povuci podatke

1. `[[dnevnik/ADS-DNEVNIK]]` — poslednji Log unos (najnoviji je na vrhu) daje
   stanje kampanja, otvorene `#ceka-miroslav` stavke i kumulativ konverzija
2. Skill **/antasline-konektor** → `ads_report.py` (Windsor je mrtav od
   2026-07-27; konektor je **read-only** za Ads)
3. Nikad ne mešaj GA4 i Ads brojeve konverzija u istoj rečenici bez ograde —
   v. §7

## 2. Podela odgovornosti — šta CC sme

| Akcija | Ko |
|---|---|
| Čitanje/dijagnostika/izveštaj | ✅ CC sam |
| Predlog izmene sa brojkama i alternativom | ✅ CC sam |
| Upis RSA teksta, izmena budžeta, pauza/paljenje, promena strategije licitiranja, nove kampanje | 🔴 **Miroslav u Ads UI** — CC priprema tekst spreman za paste, ne izvršava |
| GTM izmene za merenje | 🔴 ručno u GTM UI (JSON import na ovom kontejneru puca) |

Pravilo: **analiza → predlog → M odobrenje → izvršenje.** Konektor nema write
pristup i to je namerno.

## 3. Dijagnostika isporuke — redosled koji je već tri puta bio tačan

Kad padnu prikazi ili skoči CPC, ne nagađaj uzrok — proveri ovim redom:

1. **Da li obe kampanje pate istovremeno?**
   - DA → problem je na **nivou naloga** (balans, verifikacija oglašivača,
     policy). Potpis: visok impression share + sitni apsolutni prikazi + skok
     CPC. Presedani: jun 2026 (blokada), 05–21.07 (ispalo da je namerna pauza
     zbog godišnjeg — **uvek prvo pitaj Miroslava da li je namerno**, pre nego
     što se troši sesija na dijagnostiku)
   - NE, samo jedna → nastavi na 2
2. **`search_budget_lost_impression_share`, dnevni segment** (ne nedeljni
   prosek!). Nalaz iz 08.06: ECOTILE prosečno troši ~460 RSD/dan pri cap-u
   1.300, ali na 2 od 12 dana gubi **50% prikaza** zbog budžeta. Nedeljni
   prosek to potpuno sakriva — intraday spike na danima jake aukcije.
   - Simptom skupljeg prosečnog CPC-a bez pada obima = budžet nestane usred
     dana, "prežive" samo skuplji klikovi
3. **`search_rank_lost_impression_share`** — ako je gubitak rank-driven, to je
   Quality Score problem: **budžet ga ne rešava**, lek je RSA relevantnost +
   struktura ad grupa + landing parity
4. Tek onda tržište/sezona

## 4. Licitiranje — gde smo i kad se menja

- Trenutno: **Maximize Clicks**, namerno
- Prag za Maximize Conversions: **20–30 pravih plaćenih konverzija**
- 🔴🔴 **Prag NIJE dostignut — „26" je bila pogrešna brojka.** Snapshot od
  11.08 je našao da akcija `Klik na telefon (web)` ima
  `include_in_conversions_metric=True` + `primary_for_goal=True`, dakle ulazi u
  „Conversions" kolonu i u Smart Bidding — direktno protiv pravila iz
  [[CLAUDE]] §4. Od 01.06 do 10.08: **17 tel + 9 forma**. **Pravih plaćenih
  lidova ima 9**, ne 26. Svi izveštaji pre 11.08 su na ovome grešili.
  Prvo isključiti telefonske akcije iz konverzija (postoje **dve** aktivne),
  pa tek onda brojati ka pragu — v. [[PROGRESS]] Blokeri
- 🔴 **I nezavisno od praga, prelazak je odložen na ~01.09:**
  learning period traje do **2 nedelje za manje, do ~3 nedelje / 1–2
  konverziona ciklusa za veće promene**, a svaka značajna izmena ga restartuje.
  Migracija 24.08 menja finalne URL-ove svih oglasa — to je značajna izmena.
  Pokretanje Smart Bidding-a pre migracije znači da learning pada tačno na dan
  kad se menjaju URL-ovi = dupli reset. Redosled je: **migracija → stabilizacija
  → tek onda promena strategije.**
- Ako se menja više stvari odjednom, **promeni ih u istom potezu**, ne
  raspoređeno kroz nedelju — kraće ukupno učenje
- Broad match tek uz Smart Bidding. Do tada phrase/exact
- Dokazan obrazac sa ovog naloga: **phrase konvertuje, broad rasipa** —
  ECOTILE phrase „industrijski podovi" = 1.073 RSD/konv, dok je stara broad
  „antistatik pod" potrošila 5,0k RSD sa 0 konverzija

## 5. Negativne ključne reči — srpska morfologija je zamka

🔴 **Broad negative NIJE morfološka.** `epoksidna` ne blokira `epoksidni`,
`epoksidnog`, `epoksidnih`, `epoksi`. Na ovom nalogu je baš to curilo ~16%
budžeta dok se 06.07 nije dodalo 7 falećih oblika.

**Pravilo:** za svaki novi negativni pojam nabroji padežne/rodne oblike koji se
realno javljaju u upitima, ne samo osnovni oblik. Proveri protiv stvarnog
`search terms` izveštaja, ne iz glave.

- Format: reč bez navodnika = broad · `"fraza"` = phrase · `[pojam]` = exact
- `plocice` ide **phrase**, ne broad — broad bi ubio „pvc pločice" iz ponude
- `laminat` i `linoleum` namerno NISU dodati (mogu da konvertuju ka PVC pločama)
  — watch lista, dodati tek ako troše bez konverzija
- Puna lista: `[[reference/negativne-kljucne-reci]]`; pri svakoj reviziji
  izvezi `Negative keyword details report.csv` iz UI i uporedi red po red —
  „lista je primenjena" ne znači da je kompletna

## 6. RSA i struktura

- Cilj **Ad Strength ≥ Good**; 15 headline-a (≤30 karaktera) + 4 description-a
  (≤90) po ad grupi. **Proveri dužinu karakter po karakter pre predaje** —
  srpski dijakritici se broje normalno, ali duže reči lako probiju 30
- Bez nepotrebnog pinovanja (ako mora → 2–3 asseta po poziciji)
- Svaka ad grupa svoj RSA — to je ono što diže keyword relevance, tj. jedini
  pravi lek za rank-lost impression share
- Banka gotovih asseta (obe kampanje, srpski): `[[dnevnik/ADS-DNEVNIK]]` §Banka
- Posle 30 dana: zameni assete ocenjene „Low", **ne diraj „Learning"**
- „Alternativa epoksidu" sme u tekst oglasa kao pozicioniranje; epoksid ostaje
  negativna reč i conquest je SEO posao (post 2542), ne Ads

## 7. Merenje — šta se sme uvesti kao konverzija

- **Prava konverzija = page view na `/hvala-za-poruku/`**, uvezena iz GA4
- `tel` i `mailto` = observacija, **ne uvoziti** (double-counting sa GTM
  click-to-call)
- 🔴 **Pravilo je prekršeno u nalogu i to još nije ispravljeno**: `Klik na
  telefon (web)` se broji kao konverzija (17 od „26"). Pri svakom radu sa
  konverzijama **prvo proveri `include_in_conversions_metric` i
  `primary_for_goal` po akciji**, ne oslanjaj se na ime akcije — brojka u
  „Conversions" koloni ne znači da su to lidovi iz forme
- 🔴 **Hvala-proxy je 2× naduvan** suvišnim `page_view` GA4 tagom (id 18) —
  preživljava migraciju. Live dodatno ima dva GTM embeda → `generate_lead` 3×,
  ali taj deo nestaje migracijom. Za brojanje lidova koristi **sesije** na
  `/hvala-za-poruku/`, ne preglede
- `lead_form_start`, `epoxy_conquest_engagement` = samo publike/observacija
- 🔴 **Otvoreno od 11.08: GA4 brojač `generate_lead` je naduvan ~3×** (10
  sesija na hvala stranici → 39 eventa). Ads-ova strana broji svoje i nije
  naduvana isto. Dok se uzrok ne nađe: **ne izvoditi zaključke o Ads učinku iz
  GA4 brojača**, koristi Ads konverzije za Ads odluke
- 🔴 **Prerender/prefetch može da naduva konverzije** — Speculation Rules
  (`form_submission`, Chrome 151) ili prefetch iz LiteSpeed-a izvršavaju
  `/hvala-za-poruku/` pre nego što korisnik stigne. Pre bilo kakvog uvođenja:
  GTM trigger gate-ovan na `document.prerendering === false`. Proveriti
  LiteSpeed podešavanja pre migracije 24.08. Detalji:
  `[[reference/chrome-web-platform-2026]]` §3
- **Enhanced Conversions** (Faza 3, još neimplementirano): dopunjava postojeću
  konverziju heširanim (SHA-256) first-party podacima iz forme — email,
  telefon, ime, adresa — radi boljeg cross-device matcha. Dve varijante:
  *for web* (naš slučaj — forma na sajtu) i *for leads* (offline zatvaranje
  posla, uvoz kasnije). Implementacija ide kroz GTM ručno. Preduslov: podaci
  se šalju samo uz odgovarajući consent — a naš banner je trenutno **default
  GRANTED pre korisnikove akcije**, što je otvoreno pitanje u [[PROGRESS]]
  Blokeri; rešiti to pre nego što se počne slati PII, makar heširan

## 8. Migracija 24.08 — Ads deo (uraditi pre, ne posle)

- [ ] Popisati sve **finalne URL-ove** u oglasima i asset-ima; svaki koji se
      menja pripremiti unapred
- [ ] Proveriti da nijedan finalni URL ne pada na 301 lanac (Ads ne voli
      lance, a i gubi se deo signala)
- [ ] Sitelink/callout/structured snippet asseti nose sopstvene URL-ove —
      lako se previde
- [ ] Ne pokretati Smart Bidding u istoj nedelji (v. §4)
- [ ] Posle migracije: pratiti disapproval-e (nova landing stranica = nova
      policy provera) i Quality Score prvih 7 dana
- [ ] Tek kad su brojke stabilne ~1 nedelju → odluka o Maximize Conversions

## 9. Geo, uređaji, publike

- Mobilni = **87% potrošnje**; linija 72 dominira klikovima na telefon
  (~50 vs ~7) i ~46/50 tih klikova je sa mobilnog → call asset i mobilni bid
  imaju stvarnu težinu
- Geo bid predlog (Faza 4, neurađeno): ECOTILE +20–30% na BG, NS, Niš, KG,
  Šabac; Terase gore na zone sa kućama/bazenima
- GA4 publike (`[[blokovi/BLOK-B-publike]]`) idu na Search u **Observation**
  modu, ne Targeting — targeting bi na ovom volumenu ugušio isporuku
- **Customer Match** zaobilazi prag saobraćaja za publike (email-ovi
  postojećih upita) — identifikovan kao opcija, nije pokrenut

## 10. Tipovi kampanja koje još ne koristimo — kad ima smisla

- **Performance Max**: jedna kampanja preko celog inventara (Search, YouTube,
  Display, Discover, Gmail, Maps). Traži konverzijsko praćenje koje radi,
  assete (tekst, slike, video) i audience signale. **Ne pre** nego što Smart
  Bidding proradi na Search-u — PMax bez pouzdanog konverzijskog signala
  troši na najjeftinije placement-e. Realno: posle live-a, kad ima video
  materijala iz `/dnevni-video`
- **Demand Gen**: vizuelni gornji levak (YouTube/Discover). Ima smisla tek uz
  banku video/foto materijala i uz jasan remarketing lanac
- **Shopping/Merchant Center**: tek ako se cene stvarno objave na sajtu
  (`[[reference/cenovnik]]`) — bez cena feed nema smisla

## 11. Tvrda pravila

- ❌ Pad merenih brojeva posle čišćenja trackinga = **tačnije merenje**, ne pad
  učinka → ne reagovati budžetom
- ❌ Rank-driven gubitak prikaza se **ne rešava budžetom**
- ❌ Ne izmišljati brojeve — „Nema podataka za [izvor]"
- ❌ Ne uvoditi epoksid kampanje/oglase; epoksid ostaje negativna reč
- ❌ Ne uvoziti `tel` kao Ads konverziju
- ✅ Sve vrednosti u RSD; promene ispod 5% = stabilno stanje, ne trend
- ✅ Svaki nalaz → novi Log unos na vrh `[[dnevnik/ADS-DNEVNIK]]` +
  `[ADS]` red u `[[DNEVNIK-NAPRETKA]]`

## 12. Otvoreno (`#ceka-miroslav`, stanje 2026-08-12)

- 🔴 Isključiti **dve** telefonske konverzijske akcije iz „Conversions"
  (`include_in_conversions_metric`) — dok se to ne uradi, Smart Bidding bi učio
  na klikovima na telefon umesto na formama
- 🟡 6 BROAD ključnih reči potrošilo ~10.300 RSD/90d bez ijedne konverzije
  (`podovi za terase`, `industrijski podovi`, `podne obloge za terase`,
  `pvc podne ploče`, `podovi za hale`, `podovi za radionice cena`) — pauzirati;
  phrase parnjaci konvertuju
- 🔴 2 oglasna URL-a vode na tuđi domen `ekopodneploce.rs` (pauzirana „Ecotile
  kampanja" + sitelinkovi na nivou kampanje) — 301 mapa tu ne pomaže
- ECOTILE dnevni budžet **1.300 → 1.800–2.000 RSD** (ili svesno prihvatiti
  ~50% gubitka prikaza na spike danima)
- Da li je pauza kampanje „Podloge za terase i bazene" namerna — ako nije,
  gubi se ~158 klikova nedeljno na najjeftinijem saobraćaju u nalogu
  (CPC 16,73 vs ECOTILE 101,13)
- Faza 1 RSA Terase: tekst je pripremljen i provereno stane u limite — čeka
  upis u UI i povratnu Ad Strength ocenu
