---
tip: analiza
datum: 2026-07-27
blok: C3 / W2
izvor: "GSC API 90d (2026-04-26 → 2026-07-24), 1.624 upita / 156 stranica / 2.477 query-page parova + lokalni build inventar (wpGs_posts)"
status: aktivan
azurirano: 2026-07-27
---

# 🧩 Content klasteri iz GSC — šta postoji, šta fali

> Metod: svih 1.624 GSC upita (90d) svrstano u 17 tematskih klastera po
> prioritetnom keyword matchingu, pa ukršteno sa (a) live stranicama koje
> stvarno rangiraju za taj klaster i (b) inventarom lokalnog builda
> (71 page + 30 post, `wpGs_posts`).
>
> ⚠️ Ključna razlika koju ova analiza pravi, a ranije analize nisu:
> **GSC vidi samo live sajt.** Veliki deo W1/W2 rada postoji samo lokalno i
> ide u produkciju 2026-08-31 — te "rupe" se zatvaraju same. Prava rupa je
> samo ono što ne postoji ni live ni lokalno.

## 1. Klasteri (90 dana)

| # | Klaster | Prikazi | Klikovi | CTR | Ø poz | Upita |
|---|---|---:|---:|---:|---:|---:|
| 1 | SPORT-KOŠARKA | 11.956 | 685 | 5,73% | 3,2 | 213 |
| 2 | TERASE-DVORIŠTE | 8.349 | 318 | 3,81% | 7,8 | 205 |
| 3 | SPORT-OSTALO (fudbal/odbojka/šljaka) | 7.928 | 106 | **1,34%** | 4,1 | 139 |
| 4 | SPORT-PADEL-TENIS | 5.996 | 69 | **1,15%** | 4,7 | 107 |
| 5 | OSTALO (nerazvrstano) | 4.379 | 137 | 3,13% | 8,1 | 333 |
| 6 | EPOKSID-CONQUEST | 3.193 | 15 | **0,47%** | 8,9 | 86 |
| 7 | PARKING-GARAŽA | 3.142 | 132 | 4,20% | 7,8 | 125 |
| 8 | PODOVI-OPŠTE (head termini) | 1.977 | 48 | 2,43% | 18,7 | 136 |
| 9 | INDUSTRIJSKI | 1.537 | 42 | 2,73% | 10,7 | 28 |
| 10 | BAZEN | 1.181 | 32 | 2,71% | 9,5 | 41 |
| 11 | ECOTILE-PVC-PLOČE | 924 | 21 | 2,27% | **13,2** | 51 |
| 12 | ESD-ANTISTATIK | 914 | 39 | 4,27% | 8,4 | 22 |
| 13 | BERGO | 643 | 34 | 5,29% | 7,0 | 16 |
| 14 | VEŠTAČKA TRAVA | 641 | 54 | **8,42%** | 10,3 | 45 |
| 15 | GUMENE / TARTAN | 575 | 22 | 3,83% | 10,2 | 43 |
| 16 | LVT-VINIL | 375 | 8 | 2,13% | **19,5** | 29 |
| 17 | BREND | 235 | 116 | 49,36% | 1,1 | 5 |

**Struktura tražnje:** sport (klasteri 1+3+4+14) = **26.521 prikaza, 55%
ukupnog volumena**, ali nosi samo 914 klikova (CTR 3,4%). Komercijalni core
(industrijski + Ecotile/PVC + ESD) = 3.375 prikaza, 7%. Sajt organski nije
"prodavac podova" — on je **sportska enciklopedija** koja usput prodaje.

## 2. Klaster po klaster — postoji / fali

Legenda: ✅ live+lokal · 🟡 samo lokal (ide 31.08, rupa se zatvara sama) ·
🔴 ne postoji nigde = **prava rupa** · ⚠️ postoji ali ne radi

### 1. SPORT-KOŠARKA — 11.956 impr, poz 3,2 (najjači klaster)
Postoji: ✅ `/kako-napraviti-teren-za-basket…/` (9.500 impr, 397 kl — nosi
klaster sam) · ✅ `/sportske-podloge/kosarkaske-konstrukcije/` (1.855/115) ·
🟡 `/dimenzije-kosarkaskog-terena/` (16586) · 🟡 `/dimenzije-kosarkaske-table/`
(16585) · ✅ `/teren-za-basket-3x3/`

🔴 **Rupa: "visina koša" mikro-klaster** — `visina košarkaškog koša` 445 impr
**1 klik** poz 2,1 + `visina kosa` 345 impr **0 klikova** poz 1,1 + `visina koša`
182/1 + `dimenzije koša` 117/7 = **~1.089 prikaza, 9 klikova (0,8% CTR) na
poziciji 1–2**. Rangiramo prvi i ne dobijamo ništa — Google odgovara direktno
u snippet-u. Lek nije nova stranica nego **odgovor-blok + FAQPage schema**
na 16586 formatiran tako da snippet vodi na klik (tabela uzrasnih kategorija,
ne jedan broj).

### 2. TERASE-DVORIŠTE — 8.349 impr
Postoji: ✅ `/spoljnje-podne-obloge/` (4.571/112) · ✅ `/spoljnje-podne-obloge/bergo-xl/`
(3.312/151) · 🟡 `/gumeni-podovi-za-terase-cena/` (16873) · ✅ `/podloge-za-krovove-i-terase/`

### ⚠️ Dvorište — REVIDIRANO 2026-07-27, NE praviti novu stranicu

Prvobitna preporuka u ovoj analizi je bila „nova `/podne-obloge-za-dvoriste/`".
Provera na nivou **query→page parova** (a ne samo agregata) je to oborila —
dvorište nije jedan intent nego **tri, i sva tri već imaju vlasnika**:

| Pod-klaster | Prikazi | Vlasnik | Pozicija |
|---|---:|---|---|
| podne obloge / podloge za dvorište | ~900 | `/spoljnje-podne-obloge/` | **1,1–5,7** |
| staze za auto / plastične ploče za dvorište | ~400 | `/podloge-za-parkiraliste-i-staze/` | **1,5–6,1** (CTR 9,6%) |
| **koš za dvorište** | ~370 | `/sportske-podloge/kosarkaske-konstrukcije/` | 8,8–12,6 |

Nova stranica bi kanibalizovala `/spoljnje-podne-obloge/` koji već rangira 1,1–5,7 —
ista greška koju je W2 plan svesno izbegao dva puta (#7 šljaka, #10 piklbol).

**Štaviše, fix već postoji lokalno:** lokalni Yoast title za `/spoljnje-podne-obloge/`
je `Podovi za terase, dvorišta i bašte – Bergo PVC podne obloge` (sadrži „dvorišta"),
a live i dalje servira `Podne obloge za bašte i terase…` (ne sadrži). Rupa se
zatvara na migraciji — ili ranije, prenosom title/meta (P3 u
[[migracija/2026-07-27-cpanel-sesija-plan]]).

🔴 **Prava, neposlužena prilika u ovom klasteru je druga:** `koš za dvorište`
(~370 prikaza, 30 klikova, poz 8,8–12,6) — **kupovna namera** (traže koš, ne pod)
koja pada na stranicu na poziciji ~10. Ta stranica ima i **nezamenjen Yoast title
šablon** (`%%sep%% %%sitename%%`). Ovo je jeftina komercijalna pobeda i uklapa se
u „sportska enciklopedija" pozicioniranje.

🔴 Manja rupa: `vinil podovi za terase` 399 impr poz 4,4 — nijedna stranica
ne cilja vinil+terasa kombinaciju.

### 3. SPORT-OSTALO — 7.928 impr, CTR 1,34% (najveće curenje u apsolutnom broju)
🔴 **Najveća pojedinačna rupa u celoj analizi: `dimenzije fudbalskog terena`
1.939 impr + `fudbalski teren dimenzije` 470 = 2.409 prikaza, 7 klikova
(0,3%), pozicija 1,3–1,8.** Rangira na basket članku (3.599 impr / 9 kl) —
Google nas drži na vrhu za upit o kojem nemamo stranicu.
Komercijalno je pokriveno: `vestacka trava za fudbal` ima CTR 35% (26 kl/73 impr).
→ **nova `/dimenzije-fudbalskog-terena/`** + cross-link na `/vestacka-trava-za-fudbal/`.
Isti obrazac koji je već dokazano radio za basket (#4/#5 iz W2 plana).

⚠️ `šljaka` 1.269 + `šljaka` (ćir./lat. varijante) 470 = **1.739 prikaza, 2 klika,
poz 4,4**. Stranica `/podloga-za-teniske-terene/` refreshovana 2026-07-08 baš
za ovaj klaster — **CTR se nije popravio**. Treba revizija (upit "šljaka" je
dvosmislen: ugalj/šljaka vs teniska šljaka — verovatno pogrešan intent, ne
loš title).

⚠️ `dimenzije odbojkaškog terena` 261 + varijante ≈ 490 impr, **3 klika**, poz 1,1.
Post 4318 refreshovan — isto, rank je tu, klik nije.

🔴 `dimenzije terena za mali fudbal` 150 impr poz 22 — futsal stranica (16581)
postoji lokalno ali ne cilja dimenzije.

### 4. SPORT-PADEL-TENIS — 5.996 impr, CTR 1,15%
🟡 `dimenzije teniskog terena` 1.121 + 344 = **1.465 impr, 2 klika**, poz 2,3–3,2
→ `/dimenzije-teniskog-terena/` (16688) **već napravljena lokalno 07-08**, samo
nije live. Ovo je najveća "sama se zatvara" stavka — ~1.500 prikaza čeka 31.08.
⚠️ `/pop-tenis/` i dalje absorbuje klaster na live-u (4.012 impr / 28 kl).

⚠️ **Piklbol — dupla stranica + nula klikova:** `piklbol` 341 impr **0 kl** poz 10,1
+ `oprema za piklbol` 156 **0 kl** poz 17,7. Postoje DVE: `/teren-za-pickleball/`
(16616, Yoast title **NULL**) i `/sportska-podloga-za-pickleball/` (16680).
Kanibalizacija + prazan title. Blokirano fake-review pitanjem (#ceka-miroslav) —
ali **postavljanje Yoast title-a na 16616 ne dira recenzije** i može odmah.

### 5. EPOKSID-CONQUEST — 3.193 impr, CTR 0,47% (najgori CTR)
Postoji: ✅ samo `/epoksidni-podovi-ili-ecotile-podovi/` (2542) — 3.164 od 3.193
prikaza. Jedan članak nosi ceo klaster.
Title je dobar posle GEO fixa ("Epoksidni pod ili Ecotile PVC ploče? Cena po m² | Antas Line").

⚠️ `epoxy podovi` 339 impr **0 klikova** poz 5,7 · `epoksidni pod` 306 **0 kl** poz 3,2 ·
`epoksi pod` 155 **0 kl** poz 4,6 — **~800 prikaza na poziciji 3–6 sa nula klikova**.
Rangiramo dobro na varijantama, ne klikću. 90d prozor uključuje i period pre
GEO fixa (07-22) → **premeriti krajem avgusta pre nego što se menja išta**.

🔴 Rupa: `epoksidni podovi za terase` 157 impr poz 9,9 — conquest ugao za
spoljnu upotrebu ne postoji (2542 je o industrijskoj primeni).

### 6. PARKING-GARAŽA — 3.142 impr, CTR 4,2% (zdrav klaster)
Postoji: ✅ `/podovi-za-radionice/` · ✅ `/podloge-za-parkiraliste-i-staze/` (602/60) ·
✅ `/koji-pod-postaviti-u-garazu/` · 🟡 `/podovi-za-garaze/` (16875) ·
🟡 `/podloge-za-parkiraliste-cena/` (16876)
Rupa: nema. Klaster je pokriven, čeka migraciju.

### 7. INDUSTRIJSKI — 1.537 impr, poz 10,7 (core biznis, slaba pozicija)
Postoji: ✅ `/industrijski-podovi/` (1.116/35) — 16567 rebuild, dobar title ·
🟡 `/industrijski-podovi-cena/` (16874) · 🟡 `/podovi-za-magacine-i-hale/` (16687)
🔴 `industrijski linoleum` 51 impr **8 klikova** poz 3,1 (CTR 15,7%!) — najviši
CTR u klasteru, nijedna stranica ne cilja "linoleum" formulaciju. Sitno ali
besplatno (linoleum je namerno izostavljen iz negativnih KW — vidi CLAUDE §6).

### 8. ECOTILE-PVC-PLOČE — 924 impr, poz 13,2 🔴 **NAJVEĆI KOMERCIJALNI PROPUST**
`montažni podovi` 236 impr poz 12,5 · `pvc podovi` 80 poz **22,3** ·
`pvc podovi cena` 62 poz **17,1** · `pvc ploce za pod` 45 poz 8,0 ·
`pvc plocice za pod` 25 poz 10,3
Postoji: samo pojedinačni modeli (`/ecotile-5005-podne-ploce/`,
`/podne-ploce-ecotile-50010/`) i poređenje `/pvc-podne-ploce-ili-gumeni-podovi/`.
🔴 **Ne postoji komercijalni hub za PVC podne ploče kao kategoriju.** Ovo je
glavni proizvod firme i za generički upit "pvc podovi" smo na poziciji 22.
→ **nova `/pvc-podne-ploce/`** (hub: tipovi, debljine, primene, cena od–do,
grid modela) — najvažnija nova stranica iz cele analize po poslovnoj vrednosti.

### 9. ESD-ANTISTATIK — 914 impr, CTR 4,27% (dobar)
Postoji: ✅ `/antistatik-i-elektroprovodljivi-podovi/` (769/39)
⚠️ `/zasto-vam-je-potreban-esd-pod/` — 249 prikaza, **0 klikova**. Yoast title je
`%%sep%% %%sitename%%` šablon (nije prepisan). Najjeftiniji fix u analizi.
🔴 `antistatik` (sam) 149 impr poz 16,7 — head termin slab.

### 10–17. Ostalo
- **BAZEN** ✅ pokriven, `/spoljnje-podne-obloge/podovi-za-bazene/` radi poz 1–1,5.
- **BERGO** 🟡 `/bergo/` hub (17019) čeka migraciju; `bergo podloge cena` 161/15 poz 3,7 dobro.
- **VEŠTAČKA TRAVA** — najbolji CTR (8,42%). ⚠️ Ali head termin `vestacka trava`
  24 impr poz 23,9 / `veštačka trava` 23 poz 26,3 dok long-tail rangira 1,4–2,5.
  Napomena: 16 zapisa CPT `vestacka-trava` je u **draft** statusu na lokalu.
- **GUMENE/TARTAN** 🔴 tartan mikro-klaster (~130 impr, poz 9–18) bez stranice. Nisko.
- **LVT-VINIL** ⚠️ najgore pozicije (19,5) uprkos 5 stranica lokalno.
  `expona clic` 106 impr poz 29 iako `/expona-click/` postoji. Stranice postoje,
  autoritet ne.
- **PODOVI-OPŠTE** (poz 18,7) — head termini ("podne obloge" poz 34), nisu
  kratkoročno osvojivi, ne graditi za njih.

## 3. Nalazi koji nisu content (a krvare sada)

| #   | Nalaz                                            | Dokaz                                                                                                                                                                                                                                                                            | Akcija                                                                                                                                                         |
| --- | ------------------------------------------------ | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| T1  | 🔴 **Dva live URL-a sa saobraćajem vraćaju 404** | `/sportske-podloge/sportski-podovi-za-teniske-terene/` = **262 impr / 12 klikova u 28d**, `/gumeni-podovi-javne-objekte-i-teretane/` = **176 impr / 12 kl**. Oba `curl` → 404. Nema ih ni u `redirect-mapa-FINAL.csv` (12 redova) ni u `parity-inventar.csv` (175) ni na lokalu. | **~24 klika/28d pada u 404 danas.** 301 na `/podloga-za-teniske-terene/` i `/podovi-za-teretane-i-fitnes-centre/` — `[cpanel-live]` zadatak, ne čeka migraciju |
| T2  | 🔴 Duplikat stranica, oba objavljena na lokalu   | `sta-postaviti-preko-starog-parketa-ili-plocica` (16613) i `-2` (6588) — **isti `post_title`**. 16613 ima pogrešan Yoast title (`PVC podovi i podovi od vinila`), 6588 ima ispravan. Live oba dobijaju prikaze (137+90).                                                         | Konsolidovati u jedan, 301 drugi                                                                                                                               |
| T3  | ⚠️ GMB utm URL indeksiran kao zasebna stranica   | `/?utm_source=google%20my%20business&utm_medium=gmb…` = 337 impr u INDUSTRIJSKI klasteru + 186 u BREND + 22 u EPOKSID                                                                                                                                                            | Canonical na `/` — utm varijante troše crawl i cepaju signale početne                                                                                          |
| T4  | ⚠️ Yoast title šabloni nezamenjeni               | 16616 `teren-za-pickleball` = **NULL**, 3318 `zasto-vam-je-potreban-esd-pod` = `%%sep%% %%sitename%%`, 16613 = pogrešna tema                                                                                                                                                     | 3 fixa × 5 min                                                                                                                                                 |
| T5  | ⚠️ Zaostali `porto_builder` zapis                | ID 15447, slug `industrijski-podovi` — isti slug kao 16567 (aktivna stranica)                                                                                                                                                                                                    | Obrisati pre migracije (kolizija sluga)                                                                                                                        |
| T6  | ℹ️ PDF-ovi rangiraju                             | `bergo-ultimate-flow-pickleball-leaflet-lr.pdf` 282 impr, `Katalog-Objectflor-LVT-podovi.pdf` 172 impr                                                                                                                                                                           | Obavezno preneti `wp-content/uploads/` — inače gubimo i ovo                                                                                                    |

## 4. Šta napraviti — prioritet

| Prio | Šta | Klaster | Potencijal (90d prikaza) | Status |
|---|---|---|---|---|
| **1** | Dva 404 URL-a | 3, 4 | ~440 impr / 24 kl u 28d | ✅ **REŠENO 2026-07-27** — Miroslav objavio obe (bile DRAFT, ne obrisane); oba sad 200. Root-cause audit = P1 u [[migracija/2026-07-27-cpanel-sesija-plan]] |
| **2** | **`/pvc-podne-ploce/`** — materijalni hub | 8 | ~500 (poz 22 → top 10) | ✅ **NAPRAVLJENO 2026-07-27** (ID 17026) |
| **3** | **`/dimenzije-fudbalskog-terena/`** | 3 | **2.409** | ✅ **NAPRAVLJENO 2026-07-27** (post ID 17027, +futsal tabela → hvata i „mali fudbal" 150 impr) |
| **4** | "visina koša" odgovor-blok + FAQ na 16586 | 1 | ~1.283 | ✅ **URAĐENO 2026-07-28** — sekcija „Visina koša — 3,05 m, i kada nije" sa **tabelom po uzrastu** (odgovor koji snippet ne može da zatvori) + mere table/obruča + FAQ 7 pitanja |
| **5** | ~~`/podne-obloge-za-dvoriste/`~~ → **`koš za dvorište` na 16657** | 2, 1 | ~370 (kupovna namera) | ✅ **URAĐENO 2026-07-28** — sekcija sa 7 dvorišnih koševa i **pravim cenama** (167.790–549.900 RSD) + Yoast title/meta prepisani |
| **6** | Yoast title fixevi (16616, 3318, 16613) + T2 dedupe | 4, 9 | ~500 | ⏳ otvoreno — 16657 je rešen u okviru #5; ostaju 3 |
| **7** | ~~Revizija šljaka/odbojka refresha~~ | 3 | ~2.230 | ✅ **DIJAGNOSTIKOVANO 2026-07-27** — refresh nije „pao", nikad nije ni otišao na produkciju (lokalni title ≠ live title). Fix = P3 u cPanel nalogu |
| 8 | `/epoksidni-podovi-za-terase/` conquest | 5 | 157 | ⏳ tek posle premeravanja GEO fixa (kraj avgusta) |
| 9 | `industrijski linoleum` sekcija | 7 | 51 (CTR 15,7%) | ⏳ sitno, besplatno |

### Napravljeno 2026-07-27

| Stranica | ID | Tip | Cilja | Verifikacija |
|---|---|---|---|---|
| `/pvc-podne-ploce/` | 17026 | page | `pvc podovi` (poz 22,3), `montažni podovi` (236 impr), `pvc podovi cena` | 200 · 1×H1 · FAQPage 6 pitanja · 21/21 linkova 200 |
| `/dimenzije-fudbalskog-terena/` | 17027 | post (Sportski tereni) | `dimenzije fudbalskog terena` (2.409), `dimenzije terena za mali fudbal` (150) | 200 · 1×H1 · FAQPage 6 pitanja · 9/9 linkova 200 |

Cross-linkovi dodati: 16567 → PVC hub · 16874 → PVC hub · 5119 (veštačka trava za
fudbal) → dimenzije post. Regresija čista na sve tri.

### Dorađeno 2026-07-28

| Stranica | ID | Šta je dodato | Verifikacija |
|---|---|---|---|
| `/sportske-podloge/kosarkaske-konstrukcije/` | 16657 | sekcija „Koš za dvorište" — 7 modela sa **pravim cenama** (167.790–549.900 RSD, iz S7 upisa) + „šta proveriti pre kupovine"; Yoast title/meta prepisani (bio nezamenjen `%%sep%%` šablon); +3 FAQ → JSON-LD 8 pitanja | 200 · 1×H1 · 15/15 linkova · mobilni 390px čist |
| `/dimenzije-kosarkaskog-terena/` | 16586 | sekcija „Visina koša — 3,05 m, i kada nije" — tabela po uzrastu + „kako se meri" + mere table/obruča; +2 FAQ → JSON-LD 7 pitanja | 200 · 1×H1 · 19/19 linkova · mobilni 390px čist |

**Zašto tabela po uzrastu, a ne bolji tekst:** klaster „visina koša" je rangirao
**poz 1–2 sa ~2 klika na 1.283 prikaza** — problem nije rangiranje nego to što
Google odgovori „3,05 m" u snippet-u. Jedan broj se može zatvoriti u SERP-u,
tabela sa četiri uzrasne kategorije ne može — korisnik mora da klikne da nađe svoju.
Poslednji red tabele (podesivi dvorišni koševi 1,50–3,05 m) je komercijalni izlaz
ka 16657, isti obrazac kao basket→konstrukcije i fudbal→veštačka trava.

⚠️ **Efekat se ne vidi pre migracije**: „visina koša" trenutno rangira na **2298**
(`/kako-napraviti-teren-za-basket…/`) jer 16586 postoji samo lokalno. Ovo je
priprema za preuzimanje 31.08, ne očekivati pomak u GSC-u ranije.

💡 Otvoreno (#ceka-miroslav): `namena-dvoriste` product_tag za tih 7 koševa, da i
grid na 16657 prikazuje dvorišne modele umesto dvoranskih (`taxonomies="266"` =
`namena-sport-dvorana`). F6 pravilo traži njegovu potvrdu mapiranja proizvod→namena
pre upisa, pa je zasad urađena statička tabela — koja i bolje služi cenovnim upitima.
🟢 Usput potvrđeno: lokalni basket članak 2298 **više ne pominje fudbal** (skraćen
pri ranijoj anti-kanibalizaciji) — fudbal upiti na migraciji gube slučajnog
domaćina i prelaze na namensku stranicu bez sudara.

**Ne graditi:** head termine iz PODOVI-OPŠTE (poz 18,7+), tartan, LVT nove
stranice (5 postojećih već ne rangira — problem je autoritet, ne pokrivenost).

## 4.1 Pozicioniranje: sportska **i** industrijska enciklopedija

Odluka Miroslava 2026-07-27: sajt treba da bude enciklopedija za **oba** sveta —
sportske podloge i industrijske/podne obloge — a ne da sport bude slučajni
saobraćaj koji se trpi.

Šta to menja u odnosu na §1 („sajt je sportska enciklopedija koja usput prodaje"):
sportski saobraćaj se **ne obeshrabruje niti preusmerava**, nego se svaki
informacioni klaster gradi sa komercijalnim izlazom u istom tekstu. Obrazac koji
je već dokazano radio (basket dimenzije → konstrukcije) i koji je primenjen na
fudbal (dimenzije → veštačka trava, poz 1,4 / CTR 35%):

```
[informacioni upit] → namenska stranica sa tabelom/odgovorom
                    → cross-link na komercijalnu stranicu iste teme
                    → FAQ + FAQPage schema (hvata snippet, ne gubi klik)
```

Praktične posledice za redosled rada:
- Industrijska strana je **nedograđena enciklopedija** — 1.537 prikaza, poz 10,7,
  a to je core biznis. `/pvc-podne-ploce/` je prvi kamen; sledeći kandidati su
  `industrijski linoleum` (#9) i epoksid pod-intenti (#8).
- Sportska strana je **jaka ali procurela** — 26.521 prikaz uz CTR 3,4%; tu se
  ne grade nove stranice nego se popravlja CTR (#4 visina koša, P3 title/meta).
- Ne graditi za head termine bez namere (`podne obloge`, `pod`) — enciklopedija
  se gradi po **konkretnim pitanjima**, ne po generičkim pojmovima.

## 5. Metodološka napomena

90-dnevni prozor (26.04–24.07) pokriva sezonski špic mart–maj samo delimično —
sportski klasteri su verovatno **potcenjeni** u odnosu na godišnji prosek.
Za buduće poređenje: ista skripta, isti prozor
(`scratchpad/gsc_dump.py` + `cluster.py`, kopije nisu u vaultu — regenerisati
iz `.claude/skills/antasline-konektor`).

## Veze
- [[seo/plan-novih-stranica]] — W2 plan (iscrpljen 2026-07-12); ova analiza je njegov naslednik
- [[migracija/PARITY-PLAN]] · `migracija/parity-inventar.csv` — T1 nalaz nije u njima
- [[DNEVNIK-NAPRETKA]] · [[PROGRESS]]
