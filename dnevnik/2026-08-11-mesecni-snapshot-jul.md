---
tip: sesija
alat: claude-code
datum: 2026-08-11
blok: "-"
status: zavrseno
---

# Sesija — W5 5.4: korekcioni faktori u skillove + mesečni snapshot za jul

> Dvanaesta stavka istog dana. Read-only prema buildu, bazi i live sajtu —
> izmenjeni su samo konektor skripta, dva `SKILL.md` fajla i vault dokumenti.
> Pun izveštaj: **[[analiza/2026-08-11-snapshot-jul]]**.

## Šta je urađeno

### 1. Korekcioni faktori postali izvršni (ne samo zapisani)
- `ga4_report.py` dobio opcioni **`--live-only`** flag — isključuje
  `localhost` / `127.0.0.1` / `staging.` / `test.` / `dev.` hostname-ove.
  **Bez flag-a izlaz je bajt-identičan ranijem**, pa odluka o *trajnom*
  filteru ostaje Miroslavu (v. Blokeri u [[PROGRESS]]).
- Izlaz sada uvek nosi dva nova polja: **`hosts`** (raspodela po hostname-u —
  kontaminacija je vidljiva i kad je niko ne traži) i **`korekcija_merenja`**
  (faktori ÷2 / ÷3, `hvala_proxy_sessions`, `stvarni_dolasci_est`).
  🔴 Skripta faktore **izlaže, ne primenjuje** — sirovi brojevi ostaju sirovi,
  korekcija je posao izveštaja.
- Testirano na julu: hvala-proxy **40 → 36**, `generate_lead` **56 → 54**.
- Ista pravila upisana u `.claude/skills/nedeljni-izvestaj/SKILL.md`
  (nova **§0** sa dva tvrda pravila + obaveza čitanja [[PROGRESS]] pre
  povlačenja podataka) i u `.claude/skills/antasline-konektor/SKILL.md`.

### 2. Mesečni snapshot za jul (kasnio 11 dana)
Podaci: sopstveni konektor + 4 ad-hoc read-only skripte u scratchpad-u
(`snap_ga4.py`, `snap_gsc.py`, `snap_ads.py`, `snap_ads_convcheck.py`,
`mailto_check.py`) — **namerno nisu upisane u konektor**.

🔴 **Nalaz 1 — „26 plaćenih konverzija" nisu lidovi.** Konverziona akcija
`Klik na telefon (web)` ima `include_in_conversions_metric=True` i
`primary_for_goal=True`, dakle ulazi u „Conversions" kolonu **i u Smart
Bidding**, suprotno [[CLAUDE]] §4. Od 01.06 do 10.08: **17 tel + 9 forma**.
Prag 20–30 za zadatak **4.8 nije ni dostignut — pravih plaćenih lidova ima 9**.
Postoje **dve** aktivne telefonske akcije (druga, `CLICK_TO_CALL`, trenutno 0).

🔴 **Nalaz 2 — KPI tabla meri preglede, ne lidove.** „55/mes (jun)" su
pregledi `/hvala-za-poruku/`; stvarno: jun **24 sesije** · jul **16** ·
avgust (1–10) **11** · kumulativ 01.06–10.08 **119 pregleda = 51 sesija**.
KPI red u [[2026-07-06-MASTER-PLAN-V2]] §5 prepravljen (baseline 24 · cilj
≥25 · +60d 35+), stare vrednosti ostavljene precrtane — čeka M potvrdu.

🟢 **Nalaz 3 — organski pad je SERP, ne mi.** Jul YoY: pozicija **8,2 → 6,0**,
prikazi **+22%**, CTR **6,76% → 4,52%**, klikovi **−18%**. Upiti na poziciji
1,0–1,9 imaju CTR 2,3–8,3% (`dimenzije košarkaškog terena`: poz. 1,9 / 732
prikaza / **2,3%**). GA4 potvrđuje iz drugog ugla: engagement **62,1% —
najviši u celoj 16-mesečnoj seriji**; korisnici −32% MoM ali **+5% YoY**.

🔴 **Nalaz 4 — Ads.** ~**10.300 RSD/90d na 6 BROAD ključnih reči sa 0
konverzija** (plan kaže „broad tek uz Smart Bidding" — u praksi radi *sada*).
`industrijski podovi` (phrase) i dalje najjeftinija konverzija (**903 RSD**).
Pauzirana **Terase je efikasnija od jedine aktivne ECOTILE** (CPC 18,3 vs
41,9 uz iste 13 konverzija). Izgubljeni prikazi **zbog ranga 52–55%**, zbog
budžeta samo 13–19% → QS problem, ne budžetski. `podne obloge za terasu`:
4.223 RSD / 237 klikova / **0 konverzija**.

🟢 AI saobraćaj jul: **28 sesija** (ChatGPT 26) vs baseline 9/90d.
⚪ GMB: **429 quota**, peti neuspeli retest, nepromenjeno od 30.07.
⚠️ Mesečni AI test (5 promptova, zadatak 5.5) **nije ponovljen** — zaseban zadatak.

### 3. Ispravka u toku sesije (M)
Snapshot je `mailto` = 0 u julu prvo prijavio kao **nov nalaz sa
nedijagnostikovanim uzrokom** — netačno. Uzrok je nađen 27.07 (event je
pratio **MonsterInsights**, gašenje MI-ja u BLOK A ga oborilo **27.06**),
popravka izvršena **07.08** (GTM **Version 14**) →
[[dnevnik/2026-08-07-gtm-mailto-tag]]. Merenje po danu se poklapa u dan i
**potvrđuje popravku** (prvi događaj posle nje 07.08, pa 09.08 ≈ 0,5/dan =
ista stopa kao pre prekida), čime je zatvorena i otvorena provera iz te
sesije. Ispravljeno na 5 mesta (PROGRESS ×2, snapshot ×3, ledger, dnevnik
07.08, lekcije).

## Otvorene akcije
- [ ] **Ads → Goals → Conversions → `Klik na telefon (web)` → Secondary action**
      (2 min, ne briše istorijske podatke) #ceka-miroslav
- [ ] Potvrditi prepravku **KPI table na sesije** (baseline 24 · ≥25 · 35+)
      ili svesno ostaviti staru skalu #ceka-miroslav
- [ ] Pauzirati **6 BROAD ključnih reči** sa 0 konverzija + dodati 4 negativne
      (`deking`, `epoksidna smola` — 🔴 **ne** `epoksid`, `jysk`, `kameni podovi`)
      #ceka-miroslav
- [ ] Odluka o **trajnom `hostName` filteru** u `ga4_report.py` (sada opcioni
      flag) #ceka-miroslav
- [ ] Mesečni **AI test 5.5** (5 promptova) — nije rađen ovog meseca #claude-code
- [ ] Post-live: `old.antasline.com` host (1 korisnik / 2 pregleda) — filter ga
      ne hvata, zanemarljivo #claude-code

## Beleške / odluke
- **Nijedna Ads izmena nije izvršena** — svi nalazi su predlozi, po pravilu
  „analiza → predlog → odobrenje → izvršenje".
- `--live-only` je namerno **opcioni** flag, a ne podrazumevano ponašanje:
  trajna promena bi izmenila izlaz svih budućih izveštaja i time presekla
  otvorenu M odluku.
- Faktori se **ne primenjuju automatski** na `events`/`hvala_proxy_pageviews`
  jer posle migracije (24.08) prestaju da važe u istom obliku:
  `generate_lead` ÷3 nestaje sam (build ima jedan GTM embed), hvala-proxy ÷2
  ostaje dok se GA4 tag **id 18** ne obriše.
- Nema DB backup-a — nijedna izmena nije dirala WordPress/SQL.
- 🔴 **Podsetnik za prvi post-live izveštaj:** `generate_lead` pada na ~⅓,
  hvala-proxy na ~½. To **nije** pad konverzija.

## Veze
- Pun izveštaj: [[analiza/2026-08-11-snapshot-jul]]
- Prethodni snapshot: [[analiza/2026-07-04-snapshot-full]]
- [[dnevnik/2026-08-11-w5-nedeljni-izvestaj]] · [[dnevnik/2026-08-11-generate-lead-inflacija-dijagnoza]]
- [[dnevnik/2026-08-07-gtm-mailto-tag]] (mailto popravka)
- [[2026-07-06-MASTER-PLAN-V2]] §5 (KPI tabla) · W4 4.8 · W5 5.4
- [[reference/naucene-lekcije]] (4 nove lekcije) · [[DNEVNIK-NAPRETKA]] · [[PROGRESS]]
