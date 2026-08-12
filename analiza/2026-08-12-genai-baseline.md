---
tip: analiza
datum: 2026-08-12
izvor: Google Search Console — Performance › Generative AI features (Beta)
period: 3 meseca (≈18.05–09.08.2026), „Last update: 6 hours ago"
namena: baseline PRE migracije 2026-08-24 (URL-ovi se menjaju)
---

# Baseline — vidljivost u Google generativnim AI funkcijama

> Prvo očitavanje. **UI-only izveštaj** (nema ga u Search Analytics API-ju), pa
> je snimljen ručno preko browsera. Metrika je **samo prikazi** — nema klikova,
> CTR-a ni pozicije (v. [[seo/geo-ai-plan]] §0.1).
>
> 🔴 **Zašto pre migracije:** izveštaj je vezan za URL-ove. Posle 24.08 se
> slugovi menjaju, pa bez ovog snimka nema odgovora na pitanje „da li smo
> izgubili AI vidljivost preseljenjem".

## Ukupno

| | |
|---|---|
| Prikazi u AI funkcijama (3 mes.) | **~17.000** |
| Stranica sa bar jednim prikazom | **112** |
| Za poređenje: ukupno Web pretraga (isti period) | 129.000 prikaza / 6.250 klikova |
| Udeo AI prikaza u ukupnim | **~13%** |

⚠️ Nije zaseban saobraćaj — AI prikazi su **podskup** `Web` tipa, već uračunati
u onih 129K. Ne sabirati.

## Top stranice po prikazima

| # | Stranica | Prikazi |
|---|---|---|
| 1 | `/kako-napraviti-teren-za-basket-ili-kosarkaski-teren/` | **6.901** |
| 2 | `/pop-tenis/` | **2.250** |
| 3 | `/sportske-podloge/` | 821 |
| 4 | `/podloge-za-parkiraliste-i-staze/` | 796 |
| 5 | `/spoljnje-podne-obloge/` | 619 |
| 6 | `/podloga-za-teniske-terene/` | 584 |
| 7 | `/antistatik-i-elektroprovodljivi-podovi/` | 566 |
| 8 | `/epoksidni-podovi-ili-ecotile-podovi/` (conquest 2542) | 488 |
| 9 | `/sta-postaviti-preko-starog-parketa-ili-plocica-2/` | 459 |
| 10 | `/vestacka-trava/` | 371 |
| 11 | `/podloga-za-odbojkaske-terene/` | 356 |
| 12 | `/industrijski-podovi/` | 305 |
| 13 | `/` (početna) | 209 |
| 14 | `/sportske-podloge/kosarkaske-konstrukcije/` | **196** |
| 15 | `/koji-pod-postaviti-u-garazu/` | 145 |
| 16 | `/podovi-za-radionice/` | 138 |
| 17 | `/spoljnje-podne-obloge/bergo-xl/` | 132 |
| 18 | `/sportske-podloge/sportski-podovi-za-teniske-terene/` | 127 |
| 19 | `/spoljnje-podne-obloge/podovi-za-bazene/` | 120 |
| 20 | `/zasto-vam-je-potreban-esd-pod/` | 99 |
| 21 | `/industrijski-podovi-montaza-preko-ostecenog-epoksida/` | 97 |
| 22 | `/industrijski-podovi/trake-za-obelezavanje/` | 93 |
| 23 | `/ftalati-stetnost-i-uticaj-na-ljudsko-zdravlje/` | 89 |
| 24 | `/pvc-podne-ploce-ili-gumeni-podovi/` | 84 |
| 25 | `/sta-postaviti-preko-starog-parketa-ili-plocica/` | 81 |
| 26 | `/industrijski-podovi/industrijski-pod/` | 69 |
| 27 | `/spoljnje-podne-obloge/vestacka-trava-za-terase/` | 64 |
| 28 | `/podne-ploce-podovi-za-kontejnere-i-montazne-objekte/` | 61 |
| 29 | `/podovi-za-detailing-radionice-i-servise/` | 46 |
| 30 | `/podloge-za-krovove-i-terase/` | 43 |
| 31 | `/category/industrijski-podovi/` | 42 |
| 32 | `/zastitne-podloge-za-travu-i-plocnike/` | 42 |
| 33 | `/teren-za-basket-3x3/` | 41 |

Rep (79 stranica) je ispod 41 prikaza — nije snimljen pojedinačno, nema
analitičku vrednost na ovom volumenu.

## Nalazi

**1. Koncentracija je ekstremna.** Dve stranice nose **54%** svih AI prikaza
(basket 6.901 + pop-tenis 2.250 = 9.151 od ~17.000). Prvih 10 nosi ~80%.
Praktično: AI vidljivost ovog sajta **jeste** basket/tenis sadržaj, ne
industrijski podovi — obrnuto od komercijalnog prioriteta.

**2. 🔴 `/sportske-podloge/kosarkaske-konstrukcije/` ima 196 AI prikaza.** To je
ista stranica koja je u [[CLAUDE]] §7.4 označena kao **kritična rupa** (478 GSC
klikova, traži pravu landing stranicu, ne 301 na shop kategoriju). Sada ima i
drugu vrstu vrednosti koja se gubi ako redirect promaši — dodatan argument da se
F5 uradi kako treba.

**3. Duplikat parket/pločice se vidi i ovde.** `-2` varijanta 459 prikaza,
originalna 81 — isti odnos kao u GSC klikovima (3.353 vs 1.667 impr, odluka od
30.07 da `-2` ostaje). Konsolidacija je bila ispravna.

**4. Conquest članak radi i u AI odgovorima** — `/epoksidni-podovi-ili-ecotile-podovi/`
488 prikaza. Epoksid upiti stvarno dolaze do nas kroz AI, ne samo kroz klasičnu
pretragu.

**5. `/category/industrijski-podovi/` (42) je u igri** — arhivska stranica koju
niko nije optimizovao. Nizak volumen, ali proveriti da migracija ne ostavi 404.

## Sledeći korak

Ponoviti očitavanje **posle migracije** (predlog: 07.09, ~2 nedelje posle
live-a) i uporediti stranicu po stranicu. Pad na pojedinačnoj stranici uz
stabilan ukupan zbir = URL problem (301), ne sadržajni.

## Veze
- [[seo/geo-ai-plan]] §0.1 (šta izveštaj daje) · §0.2 (kontrola „Include")
- [[migracija/2026-08-10-pre-migration-checklist]] §A
- [[analiza/2026-07-22-ai-test-baseline]] — ChatGPT test (ne-Google asistenti)
