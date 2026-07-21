---
tip: analiza
datum: 2026-07-21
svrha: baseline pre migracije (2026-08-31) — W3 3.15
azurirano: 2026-07-21
---

# SERP snapshot — pre migracije (2026-07-21)

**Svrha:** [[2026-07-06-MASTER-PLAN-V2]] W3 3.15 — snimak pozicija za top upite PRE migracije,
da se posle 2026-08-31 zna da li eventualni pad dolazi od migracije (301/URL greška) ili od
promene na strani konkurencije (nezavisno od nas). Deo gate liste (sekcija 3).

**Izvor pozicija:** GSC preko Windsor.ai (`searchconsole`, `sc-domain:antasline.com`),
prozor poslednjih 28 dana (2026-06-24 → 2026-07-21). Ovo je zvanična Google pozicija za
stvarne pretraživače u Srbiji — najpouzdaniji broj za poređenje POSLE migracije.

**Napomena o "konkurencija" delu:** live Google pretrage su rađene kroz Chrome iz ovog
okruženja, koje **nije geolocirano u Srbiji** — `gl=rs`/`hl=sr` parametri su samo slab hint,
ne pravi IP iz RS. Zbog toga se poredak/prisustvo Antasline-a na ovim snapshot-ima delimično
razlikuje od GSC pozicije (npr. na jednom upitu Antasline uopšte nije bio vidljiv na prvoj
strani iako GSC kaže pozicija 1,4). **GSC brojevi u tabeli ispod su merodavni**, live SERP
provere služe samo kao kvalitativan kontekst "ko su konkurenti danas".

---

## 1. Top 20 upita po klikovima (28d, ukupno 451 klik / 15.582 impresija)

| # | Upit | Klikovi | Impresije | CTR | Pozicija |
|---|---|---:|---:|---:|---:|
| 1 | antas line *(brend)* | 20 | 37 | 54,05% | 1,1 |
| 2 | podloga za kosarkaski teren cena | 14 | 48 | 29,17% | 1,9 |
| 3 | gumeni podovi za terase cena | 13 | 211 | 6,16% | 1,4 |
| 4 | podloga za košarkaški teren | 13 | 37 | 35,14% | 1,5 |
| 5 | gumene podloge za terasu | 10 | 144 | 6,94% | 1,9 |
| 6 | plasticne staze za dvoriste | 10 | 91 | 10,99% | 1,2 |
| 7 | dimenzije table za kos | 9 | 100 | 9,00% | 1,3 |
| 8 | dimenzije kosarkaskog terena | 8 | 135 | 5,93% | 1,1 |
| 9 | gumeni tepih za terasu | 8 | 128 | 6,25% | 1,0 |
| 10 | podloga za kosarkaski teren | 8 | 18 | 44,44% | 2,3 |
| 11 | podloga za kosarku | 8 | 22 | 36,36% | 1,7 |
| 12 | izgradnja teniskog terena cena | 7 | 18 | 38,89% | 2,3 |
| 13 | košarkaška tabla dimenzije | 7 | 47 | 14,89% | 1,0 |
| 14 | kosevi sa konstrukcijom | 6 | 57 | 10,53% | 7,3 |
| 15 | plastika za parking | 6 | 23 | 26,09% | 1,0 |
| 16 | podloga za parking | 6 | 33 | 18,18% | 2,3 |
| 17 | sta staviti preko parketa | 6 | 33 | 18,18% | 1,9 |
| 18 | vestacka trava za fudbal | 6 | 17 | 35,29% | 1,3 |
| 19 | dimenzije padel terena | 5 | 104 | 4,81% | 1,1 |
| 20 | dimenzije terena za basket | 5 | 32 | 15,62% | 1,3 |

**Čitanje:** 17/20 upita je na poziciji ≤2,3 (dominacija sportskih podloga/dimenzija/terasa
klastera) — ovo je grupa najosetljivija na migracioni pad, jer nema kud da padne osim naniže.
Jedini outlier je #14 (kosevi sa konstrukcijom, poz. 7,3).

## 2. Rizik-grupa: visoke impresije, slaba pozicija (van top 20 po klikovima, ali vredi pratiti)

| Upit | Impresije (28d) | Pozicija | CTR |
|---|---:|---:|---:|
| podovi za terase | 274 | 11,7 | 1,82% |
| kosevi za dvoriste | 21 | 9,6 | 23,81% |
| koš za dvorište | 36 | 9,6 | 13,89% |
| koš sa konstrukcijom | 53 | 8,6 | 9,43% |
| gumeni podovi za teretane | 16 | 12,1 | 25,00% |
| bergo podloge cena | 34 | 3,4 | 11,76% |

Ovi upiti nisu u top20-po-klikovima (jer je pozicija slaba pa CTR nizak), ali "podovi za terase"
ima najveći volumen impresija van top grupe (274/28d) — vredi ih uporediti posle migracije
odvojeno od top20, jer bi mali pomak pozicije (npr. 11,7→14) mogao biti nezapažen u top-klik listi.

## 3. Live SERP kontekst — ključni konkurenti danas (spot-check, 3 upita)

### "podloga za kosarkaski teren cena" (GSC poz. 1,9)
Antas Line se pojavljuje kao **#1 organski rezultat** (potvrđuje GSC broj). Ostali na prvoj
strani: **3x3 Srbija** (3400 RSD/m²), **Boma-Court**, **megapod.rs**, **podne-obloge.com**,
**OnCourt Online**, **podovibergo.me**, **Plastik Gogić**. AI Overview navodi cenovni raspon
1.300–6.000+ RSD/m² zavisno od materijala (klik-klak/akril/EPDM) — citira "3x3 Srbija".

### "gumeni podovi za terase cena" (GSC poz. 1,4)
🔴 **Antas Line se NE pojavljuje na prvoj strani** u ovom (negeolociranom) pretraživanju —
razlika od GSC pozicije 1,4 potvrđuje da ova provera nije 1:1 uporediva sa realnim RS
rezultatima, ne znak da je nešto pokvareno. Vidljivi igrači: **sve za pod**, **KROV.rs**,
**otiraci-shop.rs**, **Jonimpex**, **PiK Group**, **megapod.rs**, **Market Parket**,
**KupujemProdajem**. AI Overview cenovni raspon 850–4.200 RSD/m², citira Market Parket.

### "podovi za terase" (GSC poz. 11,7 — rizik grupa)
Antas Line na **5. mestu** (blizu GSC prosečne pozicije van top3). Ispred: **Alpod.rs**
(deking/drvo), **IKEA**, **KupujemProdajem**, **podne-obloge.com**. Iza: **Madera Podovi**
(⚠️ direktan konkurent koji cilja "epoksidni podovi za terase" — isti epoksid-conquest ugao
kao Antasline-ov post 2542, ali suprotan predznak — oni prodaju epoksid, mi ga ne prodajemo),
**Galerija Podova**, **JBA d.o.o.** (deking).

## 4. Kako koristiti posle migracije (3.12 post-live monitoring)

1. Povuci isti GSC pull (28d prozor) ~14–21 dana posle 2026-08-31 (dovoljno da se Google
   re-indeksira posle 301 mape).
2. Uporedi poziciju red-po-red za svih 20+6 upita iz ovog fajla.
3. Pad >2 pozicije na ≥3 upita u top20 grupi (koja je danas skoro sva poz. ≤2,3) = crvena
   zastava za 301/tehnički problem, ne sezonski šum — istražiti odmah (crawl errors, redirect
   lanci, canonical).
4. Ako padne samo poneki upit dok većina drži poziciju → verovatnije je promena na strani
   konkurencije (npr. novi sadržaj kod Boma-Court/3x3 Srbija/megapod.rs), ne naš bug.

## Veze
[[2026-07-06-MASTER-PLAN-V2]] · [[migracija/PARITY-PLAN]] · [[analiza/2026-07-04-gsc-kw-analiza-16m]] · [[PROGRESS]]
