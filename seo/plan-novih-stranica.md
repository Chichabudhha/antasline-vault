---
tip: content-plan
datum: 2026-07-04
blok: C3
izvor: "[[analiza/2026-07-04-gsc-kw-analiza-16m]] + [[analiza/2026-07-04-ads-st-analiza-16m]]"
status: aktivan
azurirano: 2026-07-04
---

# 📝 Plan novih stranica — popunjavanje gap-a (C3)

> Izvedeno iz pune 16m keyword analize (2.893 GSC upita + 1.899 Ads termina). Impresije = GSC 16 meseci. Obuhvata i 4 ranije planirane C3 stranice (označene *(C3-orig)*).

## Pravila za SVAKU stranicu

- Yoast >80 · na **lokalnom buildu** (ne live!)
- FAQ blok + **FAQPage/Product schema** (Product snippet CTR 10,5% — dokazano u [[analiza/2026-07-04-snapshot-full]] §1.5)
- Cena od–do po m² gde ima smisla (`{{CENA_OD}}–{{CENA_DO}}` placeholder dok Miroslav ne da cifre)
- Interni link ka `/industrijski-podovi/` gde ima smisla (organik poz. 11 — treba juice)
- CTA: telefon **072** + forma → `/hvala-za-poruku/`
- ❌ Ništa što prodaje epoksid (conquest pravilo, [[CLAUDE]] §1)

---

## TIER 1 — Purchase intent, odmah (⭐⭐⭐)

| # | URL | Ciljni upiti (impr 16m) | Trenutno | Format / napomena |
|---|---|---|---|---|
| 1 | `/gumeni-podovi-za-terase-cena/` *(C3-orig "terasa cena")* | gumeni podovi za terase cena (**4.221**) + pvc/podne obloge za terasu cena (~700) | poz. 4, CTR 5,9% | cena tabela + kalkulator + "pošalji dimenzije" forma |
| 2 | `/industrijski-podovi-cena/` ili cena sekcija na postojećoj | industrijski podovi cena po m2 + var. (~1.800) | poz. 7–22, CTR ~2% | cena/m² po debljini, poređenje sa epoksidom (conquest ugao), B2B forma. **Ubija i 4,1k RSD Ads curenja** |
| 3 | `/podovi-za-garaze/` — konsolidovana landing | podovi za garaze (2.244), pod za garazu (1.684), cena var. (1.515) | poz. 8–10 | tipovi+cena+before/after. Klaster: 14k GSC impr + 16,8k RSD Ads bez konv → **landing je rupa** |
| 4 | `/dimenzije-kosarkaskog-terena/` *(C3-orig)* | dimenzije košarkaškog terena (4.792) + dužina/širina/veličina (~2.500) | poz. 1,7 CTR 2,6% | FIBA/NBA/školski tabele + crtež + FAQ → featured snippet |
| 5 | `/dimenzije-kosarkaske-table/` *(C3-orig "basketball-tabla")* | tabla varijante (~2.400) | poz. 1–1,3 | merge svih tabla upita + link ka konstrukcijama (proizvodi!) |
| 6 | `/podloge-za-parkiraliste-cena/` *(C3-orig)* | parking cena + plasticno saće (~600) | klaster CTR 11,5% ⭐ | cena + nosivost + reference |

## TIER 2 — Rankiramo bez stranice (⭐⭐)

| # | URL | Ciljni upiti (impr) | Trenutno | Napomena |
|---|---|---|---|---|
| 7 | ✅ ZATVORENO 2026-07-08 — refresh `/podloga-za-teniske-terene/` (ID 2699), NE nova stranica | sljaka+šljaka (**9.022**), sljaka cena (614), šta je šljaka | poz. 4–6, CTR bio 0,08% | Postojeća stranica VEĆ rangira poz.4-5 (GSC potvrđeno) — nova stranica bi kanibalizovala. Title/meta+FAQ+schema dodat, usput ispravljen mrtav CTA link (4×) i 2×H1 bug → [[DNEVNIK-NAPRETKA]] |
| 8 | ✅ ZATVORENO 2026-07-08 — `/dimenzije-teniskog-terena/` (ID 16688) | tenis dimenzije var. (~4.700) | poz. 2–7, CTR ~0% | Nalaz: `/pop-tenis/` je pogrešno dominirao ovaj klaster (intent mismatch, padel≠tenis) — nova stranica + cross-link oba smera. Sekcije "najbrža podloga" i "US Open" uključene → [[DNEVNIK-NAPRETKA]] |
| 9 | **refresh** `/podloga-za-odbojkaske-terene/` | odbojka klaster (**7.817**) | **poz. 2,3 / CTR 0,6%** | najjeftinija pobeda — title/meta+FAQ+cena, ~30 min |
| 10 | 🔴 **PRESKOČENO 2026-07-08** — NE praviti `/piklbol/` | piklbol (383, poz. 11), oprema (325, poz. 17) | poz. 9–17 | GSC otkrio da `/teren-za-pickleball/` VEĆ postoji i dominira ceo klaster (piklbol 404 impr, oprema 269 impr) — nova stranica bi kanibalizovala. Ali ta stranica ima nerešen blokator (izmišljene recenzije u Product schema, v. PROGRESS Blokeri) — M odlučio 2026-07-08 da se ništa ne dira na njoj (ni title/meta) dok se recenzije pitanje ne reši. #ceka-miroslav |
| 11 | **refresh** padel sadržaja (dimenzije sekcija + FAQ) | padel dimenzije var. (**~8.000**) | poz. 1,2–1,5 / CTR 1,6% | rank postoji — klikabilan title + FAQ schema |

## TIER 3 — Komercijalni vertikali: LVT/Ecotile silo (⭐⭐ za B2B lidove)

> ⚠️ **2026-07-12 nalaz**: ovaj plan je pisan 2026-07-04, ali dosta W1 LVT-silo stranica napravljeno je 2026-07-08 (posle plana) — #12 i #13 VEĆ imaju stranice koje plan nije znao. Pre gradnje bilo koje preostale stavke (#15–17), proveri GSC + pretraži bazu da li već postoji stranica pre nego što se pravi nova (anti-kanibalizacija).

| # | URL | Impr | Trenutno |
|---|---|---|---|
| 12 | ✅ ZATVORENO 2026-07-12 — refresh postojeće `/lvt-podovi-za-komercijalne-i-javne-prostore/kancelarije-i-poslovni-prostori/` (ID 16669), NE nova stranica | 764 + poslovni prostori 422 | GSC: poz. **2,1** (290 impr/6m) ali CTR samo 3,4% → title/meta osvežen za bolji CTR |
| 13 | ✅ ZATVORENO 2026-07-12 — Yoast title fix na postojećoj `/lvt-podovi-za-komercijalne-i-javne-prostore/vinil-podovi-za-restorane-hotele-kafice-kancelarije-i-poslovne-prostore/` (ID 16686), NE nova stranica | 440+ | 🔴 title bio potpuno pogrešan ("Vinil podovi Objectflor Expona commercial") → ispravljen na temu stranice |
| 14 | ✅ ZATVORENO 2026-07-12 — nova stranica `/industrijski-podovi/podovi-za-hemijsku-i-prehrambenu-industriju/` (ID 17017) | 489 | Pravi gap potvrđen (hub rangira poz. 5, 38 impr, bez dedikovane stranice) — tabela hemijske otpornosti iz postojećeg PDF-a, 3 real reference fotke, FAQ+schema |
| 15 | 🔴 DEPRIORITIZOVANO 2026-07-12 — GSC provera ("prodavnic"/"maloprodaj"/"trgovin") vratila **0 rezultata** u 6 meseci, nema merljive potražnje; `/podovi-za-maloprodajne-objekte/` (16683) već tematski pokriva radnje/prodavnice | 329+ (plan-procena, ne potvrđeno GSC-om) | Ostaje otvoreno, čeka GSC signal — ne graditi dok se ne pojavi potražnja |
| 16 | ✅ ZATVORENO 2026-07-12 — nova stranica `/industrijski-podovi/podovi-za-zdravstvene-objekte/` (ID 17018) | ~600 (+Ads "ordinacije") | Pravi gap potvrđen (upiti rasprsени preko 6+ stranica, svuda 0 klikova, poz. 11–72) — tabela dezinfekcionih sredstava (vodonik peroksid, hlor, amonijak) iz istog PDF izvora kao #14, real foto zubarske ordinacije |
| 17 | ✅ ZATVORENO 2026-07-12 — FAQ+FAQPage dodat na postojeću `/podovi-za-stamparije/` (title/meta+dedupe već urađeno jutros u W1 Faza 2 batch #8) | 546 | poz. 19–49 — 4 pitanja iz postojećeg teksta (hemijska otpornost/ESD/brzina/zamena ploče), ništa izmišljeno |

## TIER 4 — Brend imovina (⭐)

| # | URL | Zašto |
|---|---|---|
| 18 | 🔴 DEPRIORITIZOVANO 2026-07-12 — `/reference/dunk-shop-teren/` + `/reference/spanoulis-court/` | GSC: "dunk shop" već dobro rangira na `/teren-za-basket-3x3/` (202–537 impr/upit, poz. 5,5–9, realni klikovi); "spanoulis" upiti ~5–17 impr/upit (mnogo manje od plan-procene 3k), hvata ih početna strana. Nova stranica bi verovatno kanibalizovala uz malu dodatnu vrednost. |
| 19 | ✅ ZATVORENO 2026-07-12 — nova stranica `/bergo/` (ID 17019, top-level) | Generički "bergo" upit rasprsен preko 15+ stranica sa realnim klikovima ali bez jedinstvenog huba — distributer-formulacija preuzeta sa `/o-nama/`, grid 6 modela + auto-grid + cross-link ka planer-terena |
| 20 | ✅ ZATVORENO 2026-07-12 — nova stranica `/industrijski-podovi/podovi-za-teretane-i-fitnes-centre/` (ID 17020) | Stara live URL sad 404 (WebFetch potvrdio, ne parity slučaj) — product-fit pitanje (gumeni vs PVC) rešeno uz Miroslavljevu potvrdu, Ecotile PVC pozicioniran pošteno bez tvrdnje o gumi |

---

## Redosled izvršavanja

1. **Ova nedelja:** #9 (odbojka refresh, 30 min) → #1 → #2 → #3
2. **Sledeća:** #4–6 (C3-orig basket/parking set) → #7–8 (tenis hub)
3. **Do kraja jula:** #10–11 (piklbol/padel) → #12–14 (vertikali start)
4. **Avgust (pre migracije):** #15–20

Svaka završena stranica: red u [[DNEVNIK-NAPRETKA]] + update [[PROGRESS]] + štiklirati ovde.

- [x] #9 odbojka refresh — ✅ title/meta + FAQ schema fix 2026-07-08 (stranica sada postoji lokalno posle F3 reimporta, `[cpanel-live]` napomena zastarela). Title/meta prepisan (ciljao "dimenzije" klaster), i usput otkriven i ispravljen bug: FAQPage JSON-LD bio izložen kao vidljiv, iskvaren tekst na stranici (wpautop+wptexturize su ga mangle-ovali) umesto da radi kao schema — sada pravi `<script type="application/ld+json">`. Cena i dalje "na upit" (čeka M10 cenovnik) → [[DNEVNIK-NAPRETKA]]
- [x] #1 terasa cena — ✅ 2026-07-10 `/gumeni-podovi-za-terase-cena/` (ID 16873), M1 fallback "na upit" — kad stignu cene, upisati u tabelu → [[DNEVNIK-NAPRETKA]]
- [x] #2 industrijski cena — ✅ 2026-07-10 `/industrijski-podovi-cena/` (ID 16874), "na upit" + tabela po debljini sa linkovima na 500/5/7/10 info stranice; ⚠️ #ceka-M: preusmeriti Ads cena-termine (4,1k RSD curenje) na ovu landing → [[DNEVNIK-NAPRETKA]]
- [x] #3 garaže landing — ✅ 2026-07-10 `/podovi-za-garaze/` (ID 16875), "na upit", Pre/Posle ecotile fotke, diferencirana od `/industrijski-podovi/garaze-i-autoservisi/` (cross-link); ⚠️ #ceka-M: Ads garaža-termini (16,8k RSD išlo u prazno) → ova landing → [[DNEVNIK-NAPRETKA]]
- [x] #4 dimenzije terena — ✅ implementirano `/dimenzije-kosarkaskog-terena/` (ID 16586) 2026-07-06, anti-kanibalizacija urađena (basket članak 2298 skraćen) → [[DNEVNIK-NAPRETKA]]
- [x] #5 tabla — ✅ implementirano `/dimenzije-kosarkaske-table/` (ID 16585) 2026-07-06, cena "na upit" (nema pravih brojeva) → [[DNEVNIK-NAPRETKA]]
- [x] #6 parking cena — ✅ 2026-07-10 `/podloge-za-parkiraliste-cena/` (ID 16876) — **sa PRAVIM cenama** (Runfloor 2.800–3.400, Geoflor 3.400, Geogravel 4.000, Geocross 4.200 din/m² sa PDV; nosivosti 600/400/100 t/m² — izvor: hub 16589 parity sadržaj, draft je pogrešno pretpostavljao 200 t/m²) → [[DNEVNIK-NAPRETKA]]
- [x] #7–8 tenis hub — ✅ oba zatvorena 2026-07-08 (videti Tier2 tabelu redovi #7/#8 gore — stale checkbox ispravljen 2026-07-12)
- [x] #11 padel refresh — ✅ 2026-07-08: title/meta prepisan (`/pop-tenis/`, cilja "dimenzije padel terena" klaster ~8.000 impr, poz. 1,2–1,5, CTR je bila 1,6%) + dodat FAQ blok (4 pitanja) + FAQPage JSON-LD (nije postojao ranije) → [[DNEVNIK-NAPRETKA]]
- [ ] #10 piklbol — nova stranica `/piklbol/`, još nije rađeno, blokirano fake-review pitanjem #ceka-miroslav
- [x] #12 kancelarije — ✅ 2026-07-12 title/meta refresh na postojećoj stranici (videti Tier3 tabelu)
- [x] #13 restorani/kafići — ✅ 2026-07-12 Yoast title bug-fix na postojećoj stranici (videti Tier3 tabelu)
- [x] #14 hemijska/prehrambena industrija — ✅ 2026-07-12 nova stranica ID 17017 (videti Tier3 tabelu)
- [ ] #15 tržni centri/radnje — deprioritizovano 2026-07-12 (GSC 0 rezultata, videti Tier3 tabelu), ne graditi dok se ne pojavi potražnja
- [x] #16 zdravstvo — ✅ 2026-07-12 nova stranica ID 17018 (videti Tier3 tabelu)
- [x] #17 štamparije refresh — ✅ 2026-07-12 FAQ+schema dopuna (title već ranije istog dana)
- [ ] #18 reference stranice — deprioritizovano 2026-07-12 (GSC pokazuje da postojeća pokrivenost dovoljna, videti Tier4 tabelu)
- [x] #19 /bergo/ brend hub — ✅ 2026-07-12 nova stranica ID 17019 (videti Tier4 tabelu)
- [x] #20 teretane — ✅ 2026-07-12 nova stranica ID 17020, product-fit pitanje rešeno uz Miroslavljevu potvrdu (videti Tier4 tabelu)

## Status 2026-07-12: W2 content plan praktično iscrpljen

Svih 20 stavki obrađeno (zatvoreno, refresh-ovano, ili svesno deprioritizovano/blokirano uz razlog).
Otvoreno ostaje samo: **#10 piklbol** (blokirano fake-review Product schema pitanjem, #ceka-miroslav),
**#15 tržni centri** (nema GSC potražnje, čeka signal), **#18 reference** (postojeća pokrivenost dovoljna).
Sledeći W2 rad: pratiti GSC posle migracije za nove signale, ili se vratiti na #10/#15/#18 ako se prilika promeni.

## Veze
- [[blokovi/BLOK-C-sledece]] — C3 hub
- [[analiza/2026-07-04-gsc-kw-analiza-16m]] · [[analiza/2026-07-04-ads-st-analiza-16m]] — podaci iza plana
- [[dnevnik/2026-07-02-basket-page-faq-schema]] — gotov FAQ/schema materijal za basket stranice
- CSV banke: `seo/gsc-svi-upiti-16m-2026-07-04.csv` · `seo/ads-search-terms-16m-2026-07-04.csv`
