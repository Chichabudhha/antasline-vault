---
tip: red-cekanja
naziv: W1 1.2 — red čekanja za rebuild nedostajućih live stranica
datum: 2026-07-07
izvor: migracija/parity-inventar.csv (F5 trijaža)
---

# 📋 W1 1.2 — Red čekanja za rebuild (F5 trijaža, 2026-07-07)

Sortirano po GSC klikovima (12 meseci). Pravila za svaku stranicu:

- Pre početka pročitati [[migracija/woodmart-sabloni]] (svi gotcha-i)
- Content parity: title/meta, H1/H2 struktura, obim teksta ≥ live, interni linkovi, schema
- Namenske/rešenje-hub stranice se grade po **troslojnom modelu** iz F6 (uporedna tabela + auto grid po `namena` tagu) — ne kao opis jednog proizvoda
- Standard verifikacije: 200 · 1×H1 · JSON-LD validan bez dupliranja · slike/linkovi 200 · Yoast title/metadesc
- Cene isključivo iz [[reference/cenovnik]] ili "na upit" — ništa se ne izmišlja
- Kad je stranica gotova: ako je bila u [[migracija/redirect-mapa-FINAL]] kao "čeka F5" red (kosarkaske-konstrukcije, padel-tereni, sportski-podovi-za-sale-i-balone) → vrati se i potvrdi da redirect cilj sad vraća 200, dopuni `.htaccess-301-DRAFT.txt`

---

## 🅰️ Kategorija A — pun rebuild (33 stranice, sortirano po klikovima)

| # | Live URL | Klikovi | Yoast title (live) | Napomena |
|---|---|---|---|---|
| 1 | ~~`/antistatik-i-elektroprovodljivi-podovi/`~~ | ~~1131~~ | "Antistatik pod i ESD podovi za server sale i kancelarije" | ✅ **GOTOVO 2026-07-07** (ID 16658) — namenska landing, namena-esd grid, video+skica |
| 2 | ~~`/spoljnje-podne-obloge/bergo-xl/`~~ | ~~978~~ | "Podovi za terase i baste" | ✅ **GOTOVO 2026-07-08** (ID 16659) — nova WoodMart stranica, identičan URL kao live (`/spoljnje-podne-obloge/`, sa j — parent 16590). Stari CPT unos (`spoljne-podne-obloge`, ID 5039, bez j) draftovan kao `bergo-xl-stara` |
| 3 | ~~`/sportske-podloge/kosarkaske-konstrukcije/`~~ | ~~923~~ | "Košarkaške konstrukcije, profesionalni koševi i table..." | ✅ **GOTOVO 2026-07-07** (ID 16657, F6 pilot) — namenska landing, redirect mapa red rešen (identičan URL) |
| 4 | ~~`/industrijski-podovi/industrijski-pod/`~~ | ~~625~~ | "Podovi za magacine i radionice" (H1: Ecotile 500/7 podne ploče) | ✅ **GOTOVO 2026-07-08** (ID 16660) — identičan URL kao live (redirect nepotreban), spec tabela + FAQ + 3 nova standarda (DIN 53516, BS 476-7, DIN EN 13501-1). Deo Ecotile 500/5-7-10 informativnog klastera (v. #21, #26, još nisu građeni) |
| 5 | ~~`/sportski-podovi-za-sale-i-balone/`~~ | ~~378~~ | "Podovi za sportske dvorane" | ✅ **GOTOVO 2026-07-08** (ID 16661) — identičan URL kao live (redirect nepotreban), Naxos Evolution specifikacija+boje+video+FAQ, izvučeno iz orphan legacy sadržaja (15490) |
| 6 | ~~`/spoljnje-podne-obloge/podovi-za-bazene/`~~ | ~~262~~ | "Neklizajuce podloge oko bazena" | ✅ **GOTOVO 2026-07-08** (ID 16662) — identičan URL kao live, Bergo bazenska podloga, stari thin stub (5231) arhiviran |
| 7 | ~~`/iznajmljivanje-podova/`~~ | ~~232~~ | "Rentiranje Mobilnih Podloga za Događaje i Teške Terene" | ✅ **GOTOVO 2026-07-08** (ID 16663) — identičan URL kao live, servisna stranica (Bergo Solid rental). Isotrack/privremene podloge srodna tema (v. #28) i dalje otvorena, van obima ove sesije |
| 8 | ~~`/industrijski-podovi/garaze-i-autoservisi/`~~ | ~~229~~ | "Podovi za garaže i auto-servise" | ✅ **GOTOVO 2026-07-08** (ID 16664) — Ecotile 500/7 informativna sub-stranica. F6 troslojni model razmatran ali odbačen (`namena-garaza` ima samo 1 tagovan proizvod, live sam pokriva jedan proizvod, ne hub) |
| 9 | ~~`/spoljnje-podne-obloge/bergo-easy/`~~ | ~~166~~ | "Podloge za manifestacije, sajmove ili promocije" | ✅ **GOTOVO 2026-07-08** (ID 16665) — izvučeno iz legacy CPT (5830, sad arhiviran `-stara`), 7 boja + zvanični video |
| 10 | ~~`/industrijski-podovi/trake-za-obelezavanje/`~~ | ~~153~~ | "Obeležavanje podova u magacinima i garažama" | ✅ **GOTOVO 2026-07-08** (ID 16666) — vodič za izbor + auto-grid (term 248), diferencirano od Woo kategorije |
| 11 | ~~`/lvt-podovi-za-komercijalne-i-javne-prostore/vinil-podovi-objectflor/`~~ | ~~150~~ | "Vinil podovi Objectflor - Flow" | ✅ **GOTOVO 2026-07-08** (ID 16668) — IAC Gold sertifikat (pravi PDF), child #12. Deo LVT silo klastera (preostaje #13,29,30,32) |
| 12 | ~~`/lvt-podovi-za-komercijalne-i-javne-prostore/`~~ | ~~144~~ | "Vinil podovi Objectflor" | ✅ **GOTOVO 2026-07-08** (ID 16667) — **LVT silo parent**, 4 EXPONA kolekcije, sadrži samo link ka #11 (jedina izgrađena podstranica) |
| 13 | ~~`/lvt-podovi-za-komercijalne-i-javne-prostore/kancelarije-i-poslovni-prostori/`~~ | ~~128~~ | "Podovi za kancelarije i poslovne prostore" | ✅ **GOTOVO 2026-07-08** (ID 16669) — LVT silo podstranica, EXPONA Clic/Cavalio/Simplay, cross-link na #12 dodat |
| 14 | ~~`/sportske-podloge/padel-tereni/`~~ | ~~119~~ | "Vestacka trava za padel terene" (Sit-in dobavljač) | ✅ **GOTOVO 2026-07-08** (ID 16670) — Notiks Tvist/Safitex. Usput ispravljen broken link u `/sportske-podloge/` gridu (pokazivao na staru `/padel-tenis/` umesto nove stranice) |
| 15 | ~~`/industrijski-podovi/bumperi-zastita-za-police-regale-i-zidove/`~~ | ~~113~~ | "Bumperi - zaštita za police, regale i zidove" | ✅ **GOTOVO 2026-07-08** (ID 16671) — prvi F6 troslojni model posle pilota, 19 postojećih Ergomat proizvoda auto-grid (taxonomies=245), cross-link oba smera sa Woo kategorijom |
| 16 | ~~`/ergonomske-podloge-2/`~~ | ~~110~~ | "Ergonomski podovi" | ✅ **GOTOVO 2026-07-08** (ID 16672) — 8 tipova Ergomat podloga, 🔴 nema lokalnih Woo proizvoda (gap za `/obogati-proizvod`), cena na upit |
| 17 | ~~`/spoljnje-podne-obloge/vestacka-trava-za-terase/`~~ | ~~104~~ | "Veštačka trava za terase" | ✅ **GOTOVO 2026-07-08** (ID 16673) — potvrđeno NIJE duplikat `/vestacka-trava/` (Condor Grass dekorativna linija vs. sportska trava). Usput ispravljena 3 stara nav menu item-a (DRAFT ciljevi) |
| 18 | ~~`/galerija/`~~ | ~~88~~ | "Galerija - sportski tereni" | ✅ **GOTOVO 2026-07-08** (ID 16674) — 9 pravih fotografija terena iz media library-a (live ima pokvarene placeholdere i dalje) |
| 19 | ~~`/industrijski-podovi-montaza-preko-ostecenog-epoksida/`~~ | ~~72~~ | "Popravka oštećenog epoksidnog poda postavljanjem podnih ploča" | ✅ **GOTOVO 2026-07-08** (ID 16675) — cross-link u OBA smera sa conquest člankom (2542), stara sekcija u članku ažurirana da linkuje ovamo |
| 20 | ~~`/sportske-podloge/opremazasportsketerene/reflektori-za-sportske-terene/`~~ | ~~71~~ | "LED reflektori za sportske terene" | ✅ **GOTOVO 2026-07-08** (ID 16677, child #23) — Ritelite Sports-Lite, puna specifikacija + cena, 🔴 bez lokalnih slika proizvoda |
| 21 | ~~`/industrijski-podovi/podne-ploce-ecotile-50010/`~~ | ~~56~~ | "Podovi za kretanje teških kamiona i viljuškara" (Ecotile 500/10) | ✅ **GOTOVO 2026-07-08** (ID 16678) — cross-link ka Woo proizvodu 16540 i ka #19 |
| 22 | ~~`/spoljnje-podne-obloge/bergo-unique/`~~ | ~~53~~ | "Podovi za baste i terase Bergo Unique" | ✅ **GOTOVO 2026-07-08** (ID 16679) — legacy CPT (4936) kao izvor, 25 boja, diferencirano od Bergo XL (nosivost + dezen), cross-link oba smera, stara CPT stavka → draft |
| 23 | ~~`/sportske-podloge/opremazasportsketerene/`~~ | ~~48~~ | "Oprema za sportske terene i igralista" | ✅ **GOTOVO 2026-07-08** (ID 16676) — silo parent izgrađen pre deteta #20, kao kod LVT #12/#11 |
| 24 | ~~`/sportske-podloge/sportska-podloga-za-pickleball/`~~ | ~~41~~ | "Gumirana podloga za pickleball" (Bergo Ultimate Flow) | ✅ **GOTOVO 2026-07-08** (ID 16680) — legacy CPT (5053) kao izvor. 🔴 Nalaz: `/teren-za-pickleball/` post već ima Product schema sa verovatno izmišljenim recenzijama — nije dirano ovu sesiju, čeka odluku (v. PROGRESS Blokeri) |
| 25 | ~~`/spoljnje-podne-obloge/bergo-elite/`~~ | ~~33~~ | "BERGO Elite - podloga za terase kafića" | ✅ **GOTOVO 2026-07-08** (ID 16681) — legacy CPT (5028) kao izvor, 26 boja (7 std + 19 dizajn), jedini model sa mogućnošću brendiranja logotipom, stari CPT unos → draft |
| 26 | ~~`/industrijski-podovi/ecotile-5005-podne-ploce/`~~ | ~~31~~ | "Ecotile 500/5 podne ploče - javni objekti" | ✅ **GOTOVO 2026-07-08** (ID 16682) — legacy CPT (5301) kao izvor, najlakša Ecotile verzija (5mm), NIJE za viljuškare (max paletar 300kg), nema Woo proizvod (čisto informativna). Usput ispravljen dupli broken-link na hub-u (500/5 + 500/10), stari CPT unosi (5301, 5298) → draft |
| 27 | `/podovi-za-radnje-i-maloprodajne-objekte/` | 26 | "Podovi za maloprodajne objekte" (R-tile kolekcija) | ⚠️ Proveri preklapanje sa #31 pre gradnje oba |
| 28 | `/privremene-podloge-isotrack/` | 16 | "Isotrack ploče - privremeni putevi" | Srodno sa #7 (iznajmljivanje) |
| 29 | `/lvt-podovi-za-komercijalne-i-javne-prostore/expona-click/` | 12 | "Expona Click" | LVT silo podstranica |
| 30 | `/lvt-podovi-za-komercijalne-i-javne-prostore/vinil-podovi/` | 7 | "Expona Commercial" | LVT silo podstranica |
| 31 | `/industrijski-podovi/podovi-za-maloprodajne-objekte/` | 6 | "Podovi za poslovne prostore" | ⚠️ Proveri preklapanje sa #27 pre gradnje oba |
| 32 | `/lvt-podovi-za-komercijalne-i-javne-prostore/vinil-podovi-za-restorane-hotele-kafice-kancelarije-i-poslovne-prostore/` | — | "Vinil podovi Objectflor Expona commercial" | LVT silo podstranica |
| 33 | `/industrijski-podovi/podovi-za-magacine-i-hale/` | — | "Podovi za magacine i hale" | Ecotile za magacine/hale |

## 🅱️ Kategorija B — sistemske/Woo stranice (ne treba ručni sadržaj)

- `/katalog/` — WooCommerce shop arhiva, radi automatski posle F2 permalink fix-a
- `/moj-nalog/` — WooCommerce My Account, radi automatski
- `/home/` — front-end alias, Početna (16550) je već rebuild-ovana
- `/aktuelnosti/` — ✅ rešeno F2 (blog arhiva)

## 🅲 Kategorija C — proizvod-stranice

Nijedan čist kandidat ovog kruga — sve stranice koje su ranije pretpostavljene kao "duplikat proizvoda" (Ecotile 5005/50010, Expona Click, trake-za-obelezavanje, vinil-podovi-objectflor) su **reklasifikovane u Kategoriju A** jer prema Yoast title-ovima nose informativni ugao (specifikacije, primena) koji dopunjuje transakcionu proizvod-stranicu, ne dupliraju je — u skladu sa troslojnim modelom (proizvod / kategorija / namenska landing).

## 🅳 Kategorija D — legal/utility

- ✅ **`/politika-kolacica/` — GOTOVO 2026-07-07** (ID 16656, sadržaj 1:1 sa live). Poznato odstupanje: 7×`<h1>` u starom markup-u sadržaja — restyle sesija rešava (isto kao basket članak).
- `/galerija/` — videti Kategoriju A #18 (nije čist "legal" tip, potrebne prave slike)

## 🅴 Kategorija E — duplikati / konsolidacija (301, ne rebuild)

| Live URL | Cilja na | Napomena |
|---|---|---|
| `/industrijski-podovi/elektroprovodni-podovi/` | `/antistatik-i-elektroprovodljivi-podovi/` | Isto značenje (elektroprovodni = antistatik/ESD) — 301 kad #1 bude gotov |
| `/industrijski-podovi-najcesca-pitanja/` | TBD — konsolidacija | **Treća** skoro-identična FAQ varijanta (uz postojeće `izbor-industrijskog-poda-tri-najcesca-pitanja` i `-2`, oba već PARITY) — odloženo na **W2** po M odluci iz F4 (content-strategija, ne redirect posao) |
| `/podovi-za-radnje-i-maloprodajne-objekte/` ↔ `/industrijski-podovi/podovi-za-maloprodajne-objekte/` | TBD | Moguć duplikat par (v. A #27/#31) — odluka o konsolidaciji vs 2 zasebne stranice na buildu pre gradnje |

## 🅵 Kategorija F — product_tag termini (NE W1 posao, ide u F6/F7)

Live ima 8 `product_tag` termina (`bergo`, `ergomat`, `industrijski-amortizer`, `zastita-kablova`, `zastitnik-cevi`, `samolepljiva-zastita`, `konusni-stitnik`, `industrijski-bumper`) koji ne postoje lokalno kao termini. Ovo je **odvojena taksonomija od F6 "namena" taga** (bliža brend/tip-proizvoda oznaci) — razmotriti rekreiranje pri budućem obogaćivanju proizvoda (F7), ne blokira W1 rebuild.

---

## Sažetak brojeva

| Kategorija | Broj stranica |
|---|---|
| A — rebuild | 33 |
| B — sistemske | 0 (već rešeno/automatski) |
| C — proizvod-stranice | 0 (sve premeštene u A) |
| D — legal/utility | 1 gotovo, 1 delom u A |
| E — konsolidacija | 3 para/slučaja |
| F — product_tag | 8 termina, van W1 obima |

**Top prioritet za sledeću W1 sesiju:** #27/#31 — proveriti preklapanje (`podovi-za-radnje-i-maloprodajne-objekte` ↔ `industrijski-podovi/podovi-za-maloprodajne-objekte`) pre odluke da li graditi jednu ili obe. (#1-26 ✅ svi gotovi.)

**🔍 Nalaz 2026-07-08 (bergo-xl sesija) — legacy `spoljne-podne-obloge` CPT porodica:** postoji ceo stari custom post type (bez j u slugu, Porto-era, publicly_queryable) sa 6 unosa: `bergo-unique` (4936, sad draft/`-stara`), `bergo-elite` (5028, sad draft/`-stara`), `bergo-xl` (5039, sad draft/`-stara`), `bergo-solid` (5051, publish), `bergo-flow` (5053, publish), `bergo-ultimate` (5061, draft), `bergo-easy` (5830, draft). Svi renderuju kroz generički WoodMart blog/CPT template (sidebar, "Posted by", kategorija badge) — **ne** kroz al- landing šablon, i nemaju Yoast metu. Sadrže realan, bogat sadržaj (specifikacije, boje sa hex kodovima, foto koraci ugradnje) koji je iskorišćen pri rebuild-u #9 (bergo-easy), #22 (bergo-unique), #25 (bergo-elite) — isti postupak svaki put (nova `page` pod parent 16590, stari CPT unos → draft `-stara`).

**✅ REŠENO 2026-07-08 (bergo-elite sesija):** `bergo-solid`/`bergo-flow` proverena protiv `migracija/parity-inventar.csv` (F1 live sitemap inventar) — **nemaju live URL**, za razliku od elite/unique/xl/easy koji su svi potvrđeni sa live URL-om (status NEDOSTAJE-LOKAL). Zaključak: nisu deo trenutne ponude za W1 parity rebuild, ne dodaju se u red čekanja. Napomena za buduću sesiju: hub `/spoljnje-podne-obloge/` (16590) tekstualno pominje četvrti model "Bergo Soft" za bazenske ivice (mešavina gume i PP) — ime se ne poklapa tačno sa solid/flow CPT nazivima i moguće je preklapanje sa već izgrađenom `/spoljnje-podne-obloge/podovi-za-bazene/` (#6) — nije istraženo, van obima ove sesije.

## Veze
[[migracija/PARITY-PLAN]] · [[migracija/parity-inventar.csv]] · [[migracija/redirect-mapa-FINAL]] · [[migracija/woodmart-sabloni]] · [[2026-07-06-MASTER-PLAN-V2]] W1 1.2
