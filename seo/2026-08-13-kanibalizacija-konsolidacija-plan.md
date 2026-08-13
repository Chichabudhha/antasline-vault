---
tip: analiza
naziv: Kanibalizacija, konsolidacija i URL higijena — nalazi + plan odluka
datum: 2026-08-13
status: čeka M odluke (rok NED 16.08 — content freeze)
izvor-podataka: GSC 2026-05-15→2026-08-12 (90d, `gsc_page_queries.py`) · Google Ads API (`ads_final_urls.py`, 13.08) · lokalna baza `antasline_local`
---

# Kanibalizacija i konsolidacija — nalazi 2026-08-13

> **Metodološka napomena koja menja ceo okvir:** stranice napravljene na buildu u
> julu (Tier1 „cena" stranice, dimenzije stranice) imaju **0 GSC prikaza jer ne
> postoje na live-u**. Kanibalizacija se zato **ne meri** — ona je *predviđanje*
> šta će se desiti 24.08 kad te stranice prvi put izađu pred Google. Svaka odluka
> ispod je zato „koliko rizikujemo postojeći saobraćaj", ne „šta pokazuju brojke o
> duplikatu".

---

## 1. URL higijena — `-2` slugovi

Ceo build ima **3** slug-a sa `-2` (publish+draft, page/post/product):

| ID | Slug | Status | GSC 90d | Odluka |
|---|---|---|---|---|
| 6588 | `sta-postaviti-preko-starog-parketa-ili-plocica-2` | publish | **249 prikaza / 13 klikova** | 🔴 **NE DIRATI.** `-2` je ovde **pobednička** verzija i to je URL koji live već ima. Duplikat bez `-2` (16613) ima 132/5, već je `noindex` + ima 301 pravilo. Preimenovanje bi bacilo 13 klikova/90d za kozmetiku. |
| 3274 | `izbor-industrijskog-poda-...-2` | **draft** | — | ✅ Nema URL-a, 301→2622 već u draftu. Ništa. |
| 16672 | `ergonomske-podloge-2` | publish | 1 prikaz | 🟢 **Jedini pravi kandidat.** `-2` postoji jer slug `ergonomske-podloge` drži **prilog** (attachment 12489), ne stranica. Fix: preimenovati slug priloga → stranica na čist `/ergonomske-podloge/` → 301 sa starog. ~15 min, rizik ~0 (1 prikaz). |

**Zaključak:** „dupli permalinci" nisu sistemski problem na ovom buildu — 1 stvarna
stavka, i to bezvredna po saobraćaju. Pravilo za ubuduće: pre kreiranja stranice
proveriti da slug ne drži **prilog** (WP tiho dodaje `-2` i ne prijavi ništa).

---

## 2. Google Ads — usaglašenost URL-ova (svež pull, 13.08)

`tracking_url_template` i `final_url_suffix` su **`null` na svih 14 kampanja** →
nema pokvarenog tracking template-a, auto-tagging (`gclid`) radi na nivou naloga i
🟢 već je izmereno 11.08 da `?gclid=` preživljava naš 301.

Nalazi koji krše Google smernice ili pucaju 24.08 — **svi u PAUZIRANIM kampanjama**,
pa ne troše ni dinar danas, ali **blokiraju reaktivaciju** (i 4.4):

| Nalaz | Gde | Zašto je problem |
|---|---|---|
| 🔴 Final URL na **tuđem domenu** `ekopodneploce.rs` | 3 oglasa (Ecotile kampanja) + 2 asseta (`E500-7.html`) | Google traži da final URL bude na istom domenu kao prikazani — ovo je odbijanje oglasa, ne samo loš UX |
| 🔴 Legacy `/home/…` putanje | 7 oglasa + 4 asseta (`/home/industrijski-podovi/`, `…/ecotile-5007/`, …) | Te putanje ne postoje ni na buildu; 301 draft ih ne pokriva → 404 posle migracije |
| 🟡 `http://` bez SSL-a | 4 URL-a | dodatni redirect skok pre 301 → gubitak brzine, rizik po atribuciju |
| 🟡 `trakezaobelezavanje` (bez crtica) | 1 asset | stara varijanta slug-a, danas `trake-za-obelezavanje` |

**Jedina ENABLED kampanja** („ECOTILE INDUSTRIJSKI PODOVI") pokazuje na
`/industrijski-podovi/` → 200 na buildu ✅ — potvrđuje nalaz od 11.08 da **za dan
migracije nema posla**, posao je uslov za *reaktivaciju*.

🟢 Usput verifikovano: **OAuth token konektora je živ** (13.08) — stavka iz
checkliste B1 koja inače pada za ~7 dana dok je consent screen u *Testing*.

---

## 3. Kanibalizacija po klasterima

### 3.1 🔴 NAJVEĆI RIZIK (nije bio u tvojoj listi) — 4 „dimenzije" stranice vs post 2298

`kako-napraviti-teren-za-basket…` (2298) je **najjači sadržaj na sajtu**:
**13.686 prikaza / 385 klikova / 90d**, i drži baš te upite na poziciji 1–2:

| Upit | Prikazi | Poz. | Nova stranica na buildu koja cilja isti upit |
|---|---|---|---|
| dimenzije fudbalskog terena | 2.174 | 1,6 | 🔴 17027 `/dimenzije-fudbalskog-terena/` |
| dimenzije košarkaškog terena (+ varijante) | 2.004 | 1,3–1,9 | 🔴 16586 `/dimenzije-kosarkaskog-terena/` |
| dimenzije košarkaške table / table za koš | 719 | 1,0–1,2 | 🔴 16585 `/dimenzije-kosarkaske-table/` |
| (tenis) | — | — | 🟡 16688 `/dimenzije-teniskog-terena/` (2298 ne cilja tenis) |

**Stanje na buildu:** sve 4 nove stranice su `index`, **bez canonical-a**, i
**nijedna ne linkuje ka 2298** (provereno u `post_content`). 2298 ne linkuje ka
njima. Dakle 24.08 izlaze 4 nove stranice koje se takmiče sa stranicom koja nam
donosi 385 klikova/90d sa pozicije 1.

**Preporuka (opcija B ispod):** ne brisati ih — pretvoriti u klaster: 2298 ostaje
hub i dobija linkove ka sve 4, svaka od 4 linkuje nazad ka 2298, i svaka se u
title/H1 pomera ka **transakcionoj** nameni („…i kako izgraditi teren"), da ne
gađa isti informativni upit. Bez toga bih ih pre live-a stavio na `noindex`.

### 3.2 Garaže

| URL | GSC 90d | Napomena |
|---|---|---|
| `/industrijski-podovi/garaze-i-autoservisi/` (16664) | 104 prikaza / 4 klika | postoji na live-u, **drži klaster** — „podovi za garaze" poz. 8,5 · „podovi za garazu" poz. 9 · „podloga za garažu" poz. 5 |
| `/podovi-za-garaze/` (16875) | **0** — ne postoji na live-u | nova Tier1 „cena" stranica |
| `/koji-pod-postaviti-u-garazu/` (16609, post) | — | treći dodir na istu temu |

Saobraćaj ide na **16664**, ne postoji „kuda ide" dilema. Rizik je da 16875 (kraći,
6.018 znakova, generičniji naslov „Podovi za garaže") preuzme prikaze od 16664
(7.425 znakova, poz. 5–9) i vrati nas na poziciju 20+ dok se Google ne odluči.
**Preporuka:** 16875 preusmeriti na cenovni intent — H1/title „Podovi za garaže —
cena po m²", canonical **ne** dirati, ali dodati uzajamne linkove sa 16664; 16664
ostaje primarna za sam klaster.

### 3.3 Gumeni podovi za terase

`/gumeni-podovi-za-terase-cena/` (16873) — **0 prikaza, nije na live-u.**
Upite drži **post 2641** `/pvc-podne-ploce-ili-gumeni-podovi/`: **829 prikaza / 14
klikova**, „gumene podloge za terasu" 213 prikaza poz. **8,9**, „gumene ploče za
pod" 70 poz. 7,6. Uz to `/spoljnje-podne-obloge/` (4.668 prikaza) drži ceo
terasa-klaster.

**301 na primarnu terasu se NE preporučuje** — 16873 je namenska cenovna stranica
sa drugim intentom (Tier1, W2 2.1) i nema šta da preusmeri (nema saobraćaj).
Pravi posao je **interno linkovanje**: 2641 (poz. 8,9 za komercijalni upit) treba
link ka 16873, i obrnuto. To je najjeftiniji način da se 2641 gurne sa 8,9 na 5–6.

### 3.4 🔴 Parkirališta — jedina stavka gde je rizik ozbiljan

| URL | GSC 90d |
|---|---|
| `/podloge-za-parkiraliste-i-staze/` (16589) | **1.197 prikaza / 98 klikova** — 3. najjača stranica u ovoj analizi. Poz. **1,0–1,8** za „podloge za parking", „plastika za parking", „plastične staze za dvorište" (275 prikaza, 27 klikova, poz. 1,3) |
| `/podloge-za-parkiraliste-cena/` (16876) | 0 — nova |

Ovo je jedini par gde nova stranica gađa upite na kojima smo **prvi**. „podloga za
parking" (95 prikaza, 13 klikova, poz. 1,8) i „podloge za parking" (poz. **1,0**)
su tačno ono za šta se 16876 optimizovala.

**Tvoj predlog (301 + prebaciti cenovni sadržaj na primarnu) je ovde tačan** i ja
ga preporučujem — jedina stavka na listi gde je 301 jasno isplativiji od
koegzistencije. Cenovna tabela se seli u 16589 kao sekcija „Cena po m²", 16876 →
301. Gubimo 0 (stranica nema saobraćaj), štitimo 98 klikova/90d.

### 3.5 Maloprodaja

| URL | GSC 90d | Dužina |
|---|---|---|
| `/podovi-za-radnje-i-maloprodajne-objekte/` (16142) | 31 prikaz / 0 klikova | 10.846 |
| `/industrijski-podovi/podovi-za-maloprodajne-objekte/` (16683) | **0** (nije na live-u) | 6.240 |

Obe su slabe. 16142 je **na live-u**, duža, i cilja Ads asset „Podovi za
supermarkete" (`asset 324404563530` pokazuje baš na nju). 16683 je nova, kraća,
pod silom `/industrijski-podovi/`.

**Preporuka:** primarna je **16142** (live URL + Ads odredište + 2× duža).
16683 → 301 na 16142, ili `canonical` na 16142 ako želiš da ostane u silo meniju.
Preporučujem 301 (canonical ostavlja stranicu koja se i dalje pojavljuje u
sitemap-u i troši crawl budžet ni za šta).

### 3.6 Bergo Easy → Iznajmljivanje

| URL | GSC 90d |
|---|---|
| `/spoljnje-podne-obloge/bergo-easy/` (16665) | 6 prikaza / 0 klikova |
| `/iznajmljivanje-podova/` (16663) | 6 prikaza / 0 klikova |

Obe su praktično nevidljive → 301 je **besplatan potez**, nema šta da se izgubi.
Dodatno: FAZA 1 (13.08) je već utvrdila da je **Bergo Easy diskontinuiran** i
uklonila link ka njemu sa 16590 — stranica je već sad siroče.

🟢 Tvoj predlog prihvatam u celosti: 16665 → 301 → 16663, a 16663 se nadograđuje
fokusom „podloge za manifestacije, sajmove i promocije" (16665 već ima 10.957
znakova takvog sadržaja — seli se, ne piše ispočetka). Napomena: naslov 16665
**već glasi** „Bergo Easy — podloge za manifestacije, sajmove i promocije", dakle
sadržaj postoji i samo menja domaćina.

### 3.7 Košarka — informativno vs transakciono

- `/sportske-podloge/kosarkaske-konstrukcije/` (16657): **2.421 prikaz / 148
  klikova**, upiti „konstrukcija za koš", „koš sa konstrukcijom", „koš za dvorište"
  — čisto **transakcioni** intent, poz. 6,5–12.
- `/kategorija-proizvoda/kosarkaske-konstrukcije/`: **0 prikaza** (Woo kategorija,
  nova struktura).
- `/dimenzije-kosarkaskog-terena/` (16586): 0 (nova) — v. §3.1.

**Kanibalizacije između informativnog i transakcionog ovde nema** — 16657 drži
transakcioni klaster ubedljivo, Woo kategorija ne rangira ni za šta. Pravi rizik
je onaj iz §3.1 (nove dimenzije stranice vs 2298), ne ovaj par. Jedina korisna
izmena: Woo kategorija treba **link ka 16657** (i obrnuto), da se transakcioni
signal skupi na jednu stranicu.

### 3.8 🔴 `/sportske-podloge/` — build je izgubio sadržaj koji donosi klikove

| | Live | Build (5438) |
|---|---|---|
| GSC 90d | **1.422 prikaza / 178 klikova** — najveći CTR u analizi | — |
| H2 struktura | „Podloge za sportske terene za otvorena i zatvorena igrališta" · „Vrste podloga za sportski teren?" · **„Izgradnja sportskih terena za basket u vašem dvorištu!"** | „Sportski podovi za spoljašnje i unutrašnje terene" · „Sportske podloge za svaku disciplinu" · „Bergo Ultimate — tehničke karakteristike" · FAQ · CTA |

Live drži poziciju **1,6** za „podloga za košarkaški teren" (47 klikova) i **2,0**
za „podloga za košarkaški teren cena" (39 klikova) — dakle skoro **polovina od 178
klikova dolazi iz basket-podloga klastera**, a taj H2 („Izgradnja sportskih terena
za basket u vašem dvorištu") **na buildu ne postoji**.

**Tvoja procena je tačna i ovo je, po mom sudu, najskuplja stavka na celoj listi.**
Ne predlažem vraćanje starog dizajna — predlažem da se u novi dizajn vrati
**semantika**: sekcija o basket terenu u dvorištu + fraza „vrste podloga za
sportski teren", plus link ka `/planer-terena/` (build 5438 ga trenutno **ne
pominje uopšte**, provereno u bazi).

---

## 4. Meni „Cene" — analiza

Segment: `Cene` (17421) → 2 grupna zaglavlja (`Industrija` 17422, `Spolja` 17425)
→ 4 stranice: 16874 industrijski · 16875 garaže · 16873 gumeni terase · 16876
parkiralište. Hub stranica `/cene/` (17273, 3.893 znaka).

Zapažanja:
- 🟡 Stavka **17424 nema naslov** (prazan `post_title`) — u meniju se renderuje kao
  prazan red. Bag bez obzira na odluku o segmentu.
- Sve 4 stranice su **Tier1 iz W2 2.1**, objavljene 10.07 sa `M1` fallback-om „na
  upit"; M10 je 29.07 potvrdio da već vuku prave cene iz WooCommerce-a.
- Uklanjanje segmenta iz menija **ne briše stranice** — one ostaju u sitemap-u i
  nastavljaju da ciljaju „…cena" upite („industrijski podovi cena po m2": 142
  prikaza poz. 6,6; „pvc podovi cena": 71 poz. 16,5 — oba trenutno na
  `/industrijski-podovi/`).

**Moja preporuka se delom razlikuje od tvog naloga** i zato je izdvajam:
uklanjanje „Cene" iz menija ✅ (skraćuje meni, cena nije kategorija proizvoda nego
atribut) — ali **integraciju cena u `/industrijski-podovi/` bih uradio linkom ka
16874, ne prepisivanjem tabele u 16874-ov hub**. Razlog: `/industrijski-podovi/`
je stranica sa 2.141 prikazom koja već rangira za „industrijski podovi cena po
m2" na poz. 6,6 — ubacivanje pune cenovne tabele u nju **pojačava** njen cenovni
signal, ali istovremeno čini 16874 suvišnom, a 16874 je namenski građena za taj
upit. Dve stranice sa istom tabelom = tačno ona kanibalizacija koju rešavamo.
Predlog: tabela ostaje na 16874, `/industrijski-podovi/` dobija kratak izvod
(3–4 reda: „od–do") + prominentan link. Schema `Product`/`Offer` se u tom slučaju
ne duplira — ostaje na 16874.

---

## 5. Šta staje pre freeze-a (ned 16.08)

| # | Stavka | Rizik | Vreme | Nepovratno? |
|---|---|---|---|---|
| A | `ergonomske-podloge-2` → čist slug + 301 | ~0 | 15 min | ne (301) |
| B | Bergo Easy → 301 → Iznajmljivanje + preseljenje sadržaja | ~0 | 45 min | ne |
| C | Parkiralište: cena-sadržaj u 16589, 16876 → 301 | nizak | 45 min | ne |
| D | Maloprodaja: 16683 → 301 → 16142 | nizak | 20 min | ne |
| E | 🔴 `/sportske-podloge/` — vratiti basket/„vrste podloga" semantiku + Planer link | **visok ako se NE uradi** | 1–1,5 h | ne |
| F | 🔴 Dimenzije klaster (4 str.) — uzajamni linkovi sa 2298 + pomak title-a | **visok ako se NE uradi** | 1 h | ne |
| G | Meni „Cene" — ukloniti segment + popraviti prazan naslov 17424 | nizak | 20 min | ne (meni se vraća) |
| H | Garaže/terase — uzajamni interni linkovi (16875↔16664, 2641↔16873) | ~0 | 30 min | ne |
| I | Ads: 6 URL-ova za prepis + 2 tuđi domen + `/home/` putanje | — | M, u Ads UI | — |

Ukupno CC posla: **~5 h**. Sve je povratno (301 se uklanja, meni se vraća,
tekst se vraća iz backup-a). Ništa ne dira bazu destruktivno bez `wp db export`.

**Redosled po vrednosti:** E → F → C → B → G → H → D → A.
