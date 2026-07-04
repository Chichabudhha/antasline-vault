---
tip: analiza-keywords
datum: 2026-07-04
izvor: "seo/ads-search-terms-16m-2026-07-04.csv (1.899 termina, 16 meseci)"
alat: windsor + claude-code
status: gotovo
---

# 💰 Ads Search Terms — puna analiza 16m + poređenje sa GSC

**Metod:** svih 1.899 search termina (2025-04 → 2026-07-03) kroz **iste klastere** kao [[analiza/2026-07-04-gsc-kw-analiza-16m|GSC analiza]] + flagovi: `neg` (krši negativnu listu iz [[CLAUDE]] §6), `oow` (van ponude — deking/pločice/drvo/laminat…).
**Ukupno:** 5.122 klika · 31.426 impresija · **107.750 RSD** · **samo 5 konverzija** (uvoz konverzija radi tek od juna — istorija je nemerena, ne nula).

---

## 1. KLASTERI (sortirano po trošku)

| Klaster | Termina | Klikovi | Trošak | Konv. | CTR | CPC | Ocena |
|---|---|---|---|---|---|---|---|
| **industrijski** | 71 | 989 | **21.220** | **3** | 18,7% | 21,5 | 🟢 jedini koji konvertuje (7,1k/konv) |
| **garaža-servis** | 73 | 774 | **16.764** | **0** | 23,1% | 21,7 | 🔴 2. najskuplji, nula konv → landing |
| **pvc-plastika** | 99 | 764 | 15.987 | 0 | 16,3% | 20,9 | 🔴 generički "pvc podovi" preširok |
| terasa-balkon | 364 | 833 | 15.314 | 1 | 18,5% | 18,4 | 🟡 15,3k za 1 konv |
| ostalo | 659 | 532 | 11.831 | 0 | 10,8% | 22,2 | šum + "uradi sam" (689 RSD!) |
| radionice | 21 | 347 | 7.466 | 0 | 22,3% | 21,5 | 🟡 CTR sjajan, konv nula |
| **epoksid-conquest** | 153 | 301 | **6.536** | 1 | 11,5% | 21,7 | 🔴 negativi ne rade |
| lvt-vinil-parket | 155 | 189 | 4.073 | 0 | 12% | 21,6 | 🟡 |
| konkurenti | 81 | 125 | 2.565 | 0 | 11,5% | 20,5 | 🔴 (jysk/tarkett/galerija…) |
| antistatik-esd | 16 | 87 | 2.208 | 0 | 15,5% | 25,4 | 🟡 organik nam je poz. 2,4! |
| gumeni-podovi | 38 | 77 | 1.712 | 0 | 17,2% | 22,2 | — |
| dvorište-bašta | 76 | 69 | 1.177 | 0 | 15% | 17,1 | — |
| bazen | 62 | 15 | 313 | 0 | 7,8% | 20,9 | mali |
| **košarka + parking + brend** | 5 | 0 | **0** | — | — | — | ✅ ne plaćamo gde je organik #1 |

## 2. FLAGOVI — gde curi budžet

| Flag | Termina | Klikovi | Trošak | Konv. |
|---|---|---|---|---|
| 🔴 **Krši negativnu listu** (epoksid/sika/rinol/jysk/tarkett/poliuretan/topli pod/betonski/kupujemprodajem…) | **315** | 509 | **10.521 RSD** | 2* |
| 🔴 **Van ponude** (deking 2,3k!/pločice/drvo/laminat/ikea/wpc…) | **289** | 265 | **6.086 RSD** | 0 |
| **UKUPNO CURENJE** | 604 | 774 | **16.607 RSD = 15,4%** | |
| Cena termini | 187 | 905 | 20.390 | 2 |
| Info termini | 60 | 20 | 1.055 | 0 (✅ zanemarljivo) |

\* Nijansa: "industrijski betonski podovi" (65 RSD) i "cena epoksidnih podova" (91 RSD) su po 1 konvertovali — kupac sa betonskim/epoksid podom koji traži zamenu. Negativi ostaju opravdani (6,5k na epoksid termine za 1 konv = 3× skuplje od industrijskog klastera), ali "betonski" broad negativ pažljivo — phrase varijanta bolja.

**Top curenja pojedinačno:** epoksi/epoxy familija ≈ **5,5k RSD** · deking familija ≈ **2,3k** · jysk 0,7k · "uradi sam near me/novi beograd" 0,7k · pločice ≈ 1,5k · kupujemprodajem 0,4k · tarkett 0,3k · sika 0,3k · rinol/ibotac 0,4k

**Novi kandidati za negativnu listu** (nisu na postojećoj): `deking`, `wpc`, `plocice`, `pločice`, `drven`, `laminat`, `ikea`, `pevex`, `teraco`, `letvice`, `"uradi sam"`, `samolepljiv`, `behaton`, `kamen`, `near me`, `flooring` (eng. termini — 350+ RSD)

## 3. TOP TROŠAK BEZ KONVERZIJE (legitimni termini — problem je landing, ne termin)

| Termin | Klaster | Kl. | Trošak | Napomena |
|---|---|---|---|---|
| pvc podovi | pvc | 263 | **5.468** | preširok — phrase/exact samo |
| industrijski podovi cena po m2 | industrijski | 181 | **4.128** | landing NEMA cenu → ne konvertuje |
| podovi za garaze | garaža | 166 | 3.629 | landing problem |
| pod za garazu | garaža | 130 | 2.833 | landing problem |
| podne obloge za terasu | terasa | 156 | 2.696 | — |
| podovi za radionice | radionice | 124 | 2.495 | — |
| antistatik pod/podovi | esd | 64 | 1.709 | organik poz. 2,4 — smanjiti bid |
| reciklirana guma za podove cena | guma | 29 | 635 | proveriti ponudu |

## 4. POREĐENJE GSC ↔ ADS po klasterima

| Klaster | GSC klikovi (16m) | GSC wpos | Ads trošak | Ads konv. | Verdikt |
|---|---|---|---|---|---|
| košarka | **3.845** | 2,5 | 0 | — | ✅ organik nosi sam — ne uvoditi u Ads |
| parking | 526 (CTR 11,5%) | 5,8 | 0 | — | ✅ isto |
| antistatik-esd | 533 (CTR 10,4%) | 6,4 | 2.208 (+5k stara kamp.) | 0 | 🔴 organik jak → Ads minimalno/pauza |
| industrijski | 382 | **9,6** | 21.220 | **3** | ✅ **paid opravdan** dok organik ne stigne; cena sekcija diže oba |
| garaža-servis | 491 | 9,6 | 16.764 | **0** | 🔴 oba kanala aktivna, nula rezultata → **landing je problem**, ne kanal |
| terasa-balkon | 1.344 | 9,8 | 15.314 | 1 | 🟡 dupla zavisnost; organik pozicije dići (title/meta), Ads na bazen/bergo fokus |
| radionice | 308 (CTR 6,7%) | 5,2 | 7.466 | 0 | 🟡 organik bolji posao — Ads bid ↓, phrase only |
| epoksid | 49 (CTR 0,6%) | 14,2 | 6.536 | 1 | 🔴 conquest gubi u OBA kanala → SEO refresh + negativi |
| tenis/padel/odbojka | ogromne impresije | 2–5 | ~0 | — | ✅ info intent — ne plaćati; SEO hvata |
| pvc generički | — | — | 15.987 | 0 | 🔴 samo phrase/exact sa čvrstim negativima |
| lvt/vertikali | 229 | 17,5 | 4.073 | 0 | 🟡 vertikalne stranice prvo (SEO), pa tek Ads |

### Sinteza — pravila koja podaci potvrđuju
1. **Kanali su komplementarni tamo gde treba** (basket/parking organik #1 → 0 RSD u Ads ✓) — struktura je zdrava, **execution curi** (negativi + landing).
2. **"Cena" tražnja je most između kanala**: u Ads cena-termini = 20,4k RSD (19% potrošnje!) jer organik nema cena stranice; u GSC cena-upiti imaju CTR 20–30% gde postoje. **Cena sekcije (industrijski, garaže, terase) smanjuju CPC zavisnost i dižu obe konverzije.**
3. **Garaže su ogledni slučaj problema**: 16,8k RSD + organik poz. 8–10 + 14k GSC impresija = tražnja ogromna, rezultat nula → landing bez cene/CTA. Popraviti stranicu PRE povećanja bilo čijeg budžeta.
4. **6,4k RSD/konv prosek vs 1.073 RSD/konv ECOTILE phrase** — budžet treba da prati dokazano: industrijski phrase + terase phrase, ostalo minimalno do Smart Bidding-a.

## 5. AKCIJE (dopuna [[analiza/2026-07-04-snapshot-full|snapshot]] §6.2)

1. 🔴 **Negativna lista** — potvrda + proširenje novim kandidatima iz §2 (deking, pločice, uradi sam, jysk, ikea, laminat, eng. flooring termini…) `#ceka-miroslav`
2. 🔴 **"pvc podovi" broad → pauza ili phrase** (5,5k RSD/16m bez konverzije)
3. 🟡 **Garaže landing fix pre budžeta** (cena sekcija + CTA) — pa tek onda Ads
4. 🟡 **Antistatik bid ↓** (organik poz. 2,4; ušteda ~2k+/kvartal)
5. 🟢 **Industrijski cena sekcija na sajtu** — "industrijski podovi cena po m2" sam pojeo 4,1k RSD; sa cenom na stranici i organik (841 GSC impr, poz. 8,7) i paid počinju da konvertuju
6. 🟢 Skaliraj industrijski phrase klaster (jedini sa konverzijama)

## Veze
- CSV banka: `seo/ads-search-terms-16m-2026-07-04.csv` (1.899 termina, flagovano)
- [[analiza/2026-07-04-gsc-kw-analiza-16m]] — GSC strana poređenja
- [[analiza/2026-07-04-snapshot-full]] §3 (Ads 90d) · [[dnevnik/ADS-DNEVNIK]] — fazni plan
