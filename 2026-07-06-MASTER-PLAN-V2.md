---
tip: plan
naziv: MASTER PLAN V2 — redizajn + SEO + Ads + tracking do live-a
datum-plan: 2026-07-06 do 2026-08-26
prioritet: Tehnička → SEO → Ads
go-live: 2026-08-24
go-live-raniji: 2026-08-31 (pomereno nedelju ranije, M odluka 2026-08-10)
status: aktivan
zamenjuje: "[[2026-07-02-MASTER-PLAN-DO-LIVE]]"
azurirano: 2026-08-10
---

# 🚀 MASTER PLAN V2 — do live-a 2026-08-24

> 🔴 **2026-08-10 — GO-LIVE POMEREN NEDELJU RANIJE: 31.08 → PON 24.08** (M odluka).
> Seče se **N8 buffer nedelja** (25–30.08) — jedina rezerva u planu. Nova rezerva je
> samo vikend **22–23.08**. Posledice u §2 (novi raspored), §3 (gate rokovi) i §4
> (svi „pre 31.08" rokovi → **pre 21.08**).

**Jedini izvor istine za plan projekta.** Zamenjuje [[2026-07-02-MASTER-PLAN-DO-LIVE]] (pisan pre Porto→WoodMart prelaska). Dnevno stanje: [[PROGRESS]] · istorija: [[DNEVNIK-NAPRETKA]].

**Cilj:** redizajnirani sajt live 2026-08-24, bez gubitka organskog saobraćaja, sa Ads nalogom spremnim za Smart Bidding i čistim merenjem (GA4/GSC/Ads). Budžet Ads: 40k RSD/mes (ne diže se dok se ne optimizuje).

**Pravila koja važe kroz ceo plan** ([[CLAUDE]]): lokalni build = staging, live se ne dira (osim eksplicitnih `[cpanel-live]` zadataka) · **SEO plugin = Rank Math** (migracija izvedena 05.08; staro pravilo „Yoast ostaje, ne RankMath" ukinuto M odlukom 13.08) · epoksid samo conquest · Maximize Clicks do praga 20–30 plaćenih konverzija · jun 2026 = mesec-nula za sve serije konverzija.

---

## 0. BASELINE — stanje na dan 2026-07-06

### ✅ Gotovo
| Oblast | Šta |
|---|---|
| Tracking (BLOK A) | GTM v10, Consent Mode v2, `generate_lead` na `/hvala-za-poruku/`, 3 key eventa, MI ugašen |
| Publike (BLOK B) | 6 GA4 publika kreirano (ali ispod pragova serviranja — vidi W4) |
| Tema | WoodMart 8.5.4 + child + design system `antas-design.css`, self-hosted Inter+Bebas, header, logo SVG |
| Rebuild | Home (16550) · `/industrijski-podovi/` (16567) · `/sportske-podloge/` (5438) · 4 sport stranice (futsal/hokej/stoni tenis/3x3) · 10 Woo kategorija (Layout Builder) · C3 #4/#5 dimenzije stranice |
| Woo | 37 proizvoda + 10 kategorija + 115 slika importovano na lokal |
| Migracija priprema | 🔄 od 2026-07-07: **parity strategija** [[migracija/PARITY-PLAN]] (stara redirect mapa arhivirana) · live export (posts XML, pages XML, inventar CSV) · porto backtick sanacija (7 stranica) |
| Ads | Faza 0 zatvorena (nalog odblokiran 2026-07-04) |

### 📊 Metrike-nula (za merenje uspeha)
| Metrika | Baseline | Izvor |
|---|---|---|
| Prave konverzije (hvala-proxy) | ~55/mes (jun) | [[analiza/2026-07-04-snapshot-full]] §2.3 |
| Plaćene konverzije (Ads uvoz) | 6/mes (jun) | §3.1 |
| GSC jun YoY | klikovi −19%, CTR 7,0→4,7% | §1.1 |
| Mobilni LCP (lokal) | ~7,3s 🔴 | §5.6 |
| Ads curenje budžeta | ~16% (negativne ne važe + van-ponude) | §3.4 |
| GMB | 6 recenzija, ~1 poziv/mes | §4 |
| Mobile udeo | 76% GSC · 87% Ads · 74% GA4 | §0.11 |

---

## 1. WORKSTREAM-OVI (5 paralelnih traka)

> Svaka traka ima vlasnika: **CC** = Claude Code (lokal) · **M** = Miroslav · **CP** = `[cpanel-live]` sesija. Jedan glavni zadatak po sesiji, backup baze pre svake destruktivne izmene.

### W1 — DIZAJN / REBUILD (lokal)
Šablon i pravila: [[migracija/woodmart-sabloni]] (obavezno pročitati pre svake stranice — 10 dokumentovanih gotcha-a).

| # | Zadatak | Vlasnik | Napomena |
|---|---|---|---|
| 1.1 | Silo rebuild: `/spoljnje-podne-obloge/` → `/podloge-za-parking/` → `/kontakt/` → `/o-nama/` | CC | ✅ 2026-07-07 — sve 4 gotove (spoljne bez j, parking-staze, kontakt forma, o-nama). C1 parity: live `/spoljnje-*` → lokal `/spoljne-*` |
| 1.2 | ✅ **ZATVORENO — 33/33** (potvrđeno 2026-08-12 u [[migracija/w1-red-cekanja]], svih 33 nose ✅ sa datumom 2026-07-07/08). ⚠️ Ovaj red je do 2026-08-12 stajao na „12/33, sledeći kancelarije/padel" — bio je zastareo mesec dana i naveo na pogrešan izbor zadatka; ispravljen tog dana. F5 trijaža zatvorena 2026-07-07 → [[migracija/w1-red-cekanja]] ostaje izvor istine za redosled | CC | Svaka nova namenska landing prati F7 standard (ikonice/skica/video iz woodmart-sabloni) |
| 1.3 | ✅ ODLUČENO 2026-07-07 (M8): **pun reimport svih 30 postova sa live** — izvršenje po [[migracija/promptovi/F3-posts-reimport]] | CC | restyle postova ide posle, iterativno |
| 1.4 | ✅ ZATVORENO 2026-07-08 — Footer builder (5 kolona: logo+adresa/Antas Line/Podovi/kontakt/social) + bela varijanta logoa | CC | detalji [[migracija/woodmart-sabloni]] |
| 1.5 | ✅ ZATVORENO 2026-07-08 — Meni proširen na 5-kategorijsku strukturu (Sport/Terase i dom/Industrija/Poslovni prostori/Specijalni podovi, parity sa live) | CC | 43 stavke, 3 nivoa |
| 1.6 | ✅ ZATVORENO 2026-07-10 — 15 stranica mobile smoke (0 overflow, 1×H1, 0 slomljenih slika) + vizuelno (toolbar, filteri, spec tabele, futer akordeoni); gettext fix za 2 neprevedena stringa | CC | metod: iframe 390px harness (resize_window ne radi) |
| 1.7 | Figma sync (testimonials, "Najprodavanije 2025") | CC+M | ✅ ZATVORENO 2026-07-22 — testimonials (GMB preko Windsor-a, 2 prave recenzije, F7.16) + "Najprodavanije 2025" foto baner (3 linka ka realnim kategorijama, F7.17). Detalji: [[migracija/woodmart-sabloni]] |
| 1.8 | ✅ ZATVORENO 2026-07-08 (polish Faza 0) — WoodMart `catalog_mode` + "Zatražite ponudu" na svakom proizvodu → `/kontakt/?form-naslov=Ponuda: X` (prefill `default:get`) → submit redirektuje na `/hvala-za-poruku/` (BLOK A tracking hvata). Compare/wishlist/reviews ugašeni. Usput: CF7 forma 16593 popravljena (bila nefunkcionalna — prazan `_form`/`_mail` postmeta) i shop stranica kreirana (`/katalog/` bio 404). **Polish Faza 1 ✅ ZATVORENA 2026-07-10: 47/47 proizvoda obogaćeno + 8 shop filtera na /katalog/** | CC | polish Faza 2 (postovi restyle): [[migracija/w1-polish-red-cekanja]] |
| 1.9 | ✅ ZATVORENO 2026-07-08 — audit `tel:` linkova: 063 se nigde ne pojavljuje lokalno, jedini nalaz je header top-bar koji je koristio 074 dok CTA/mobilna ikonica koriste 072 — ujednačeno na 072 sitewide (`functions.php`) | CC | quick win, 30 min |
| 1.10 | ✅ ZATVORENO 2026-07-09 — **"Brzi upit" dinamička forma** (CF7 16737) na dnu svih usluga/postova, mejl javlja izvor kroz `[_post_title]`/`[_post_url]`; 16593 skraćena (jedno ime/firma polje); CTA scroll-to-#upit; tag_base parity fix usput → [[migracija/brzi-upit-forma]] | CC | ⚠️ mu-plugin mail logger obrisati pre migracije (u 3.10) |
| 1.11 | 🆕 2026-07-11 — **Novi proizvodi (7 dobavljača) + generička oprema**: Condor trave u boji, Radici Sport trava, Geoplast parking, Expona LVT (podno grejanje 27°C!), R-Tile, Hoop n Court koševi, Ecotile rampe + generička oprema (tribine/stolice/golovi/mreže, "na upit"). ~46 proizvoda, ~78 varijacija, sesije S1–S8 → **[[migracija/w1-novi-proizvodi-court-builder]]** | CC | ✅ **S1 taksonomija ZATVOREN 2026-07-11** (4 kategorije 369–372 + 2 atributa id 20/21 + nosivost/roze termini) — gate za S2–S8 otključan. **S8 mora pre CB2 (1.12)**. Ne prekida Fazu 2 postova — sesije se ubacuju naizmenično |
| 1.12 | ✅ ZATVORENO 2026-07-11 (+ CB2-fix 2026-07-12) — **Court builder 2D** (`/planer-terena/`, samo Bergo Ultimate 16770 + FLOW 16801): SVG dizajner sa sport šablonima, obračun ploča/rampi/opreme, zaključan dizajn u WP adminu, mejl klijentu PNG+PDF (bez edit linka), token-link za novu verziju, cene → automatski predračun. CB1+CB2+CB3 sve zatvorene, testirano end-to-end kroz Chrome (3 puna ciklusa + bezbednosni testovi: honeypot/rate-limit/PNG+grid validacija). **CB2-fix (2026-07-12)**: klik-na-boju farba ceo teren odjednom + trajni checkbox za detaljno farbanje + fix bug-a (promena boje linija terena brisala obojene ploče) + auto-zadržavanje boje pri resize — **RP2 sada u potpunosti zatvoren, nema više otvorenih CB stavki.** Faze → **[[migracija/w1-novi-proizvodi-court-builder]]** | CC | ⚠️ Preostaje: SMTP konfiguracija na live pre javnog puštanja (lokalno mail-log.php presreće slanje, mora se ukloniti na produkciji — u 3.10 checklisti). 3D varijanta EKSPLICITNO posle live-a (W6+ era, van V2 obima) |

> 🟢 **W1 ZAKLJUČEN — 2026-08-12.** Provereno stavku po stavku: red čekanja A **33/33**
> ([[migracija/w1-red-cekanja]]) · Polish **Faze 1–4** ✅ (poslednja, GEO-intro na 22
> posta, 2026-08-07) · novi proizvodi **S1–S8 8/8** · Court builder **CB1–CB3 + CB2-fix** ✅.
> **Nema poznatog otvorenog posla u W1.** Dva statusa u ovoj tabeli bila su zastarela
> mesec dana (1.2 „12/33") i ispravljena su tog dana.
>
> Poslednji dodatak van reda čekanja: ✅ **alt tekst na slikama proizvoda (2026-08-12)** —
> 66 priloga (6 glavnih + 63 galerijske) popunjeno, **159 dekorativnih SVG ikonica
> namerno ostavljeno na `alt=""`** (ispravno po WCAG, popunjavanje bi bilo regresija).
>
> ✅ **Vizuali referenci i ikonice kartica (2026-08-12, ad-hoc po nalazima M)** — gole
> tekstualne trake referenci (`.al-ref-row`) zamenjene foto-karticama na **homepage-u**
> (6 referenci) i **„O nama"** (11 kartica, logo traka premeštena u Industrija); ikonice
> dopunjene na **padel** (16670, 4 kartice) i **maloprodaji** (16142, 3 kartice), set
> ikonica **23 → 27**; maloprodaja dobila sekciju **„R-Tile ploče iz ponude"** (nije
> imala nijedan link ka `/proizvod/`). 🟡 Ostaje da čeka M: **4 reference bez ikakvog
> materijala** (Beobasket, BG liga 3x3, Hotel Prag, Restoran Sidro) i **definicija
> „starog formata"** za 5119/15793 — oboje u [[PROGRESS]] Blokeri.
> Detalji: [[dnevnik/2026-08-12-vizuali-reference-ikonice]].
> Zatvara poslednju otvorenu stavku iz [[migracija/2026-07-30-lighthouse-a11y-plan]].
> 🔵 Jedini preostali a11y nalaz — `heading-order` + `target-size` na product karticama
> (WoodMart core layout) — **svesno odložen na posle live-a**, veći zahvat nego što
> vredi 4 dana pred gate.
>
> ✅ **FAZA 2 — layout/CSS/UI popravke (2026-08-13, ad-hoc po listi M).** 6 zamerki na
> 5 stranica svedeno na **3 sistemska uzroka** i popravljeno u dizajn sistemu, ne po
> stranici: **(1)** dve susedne `.al-section` istog tona daju 144px mrtve trake
> (+35px WPBakery `margin-bottom` na poslednjem bloku, +18px goli `<br>` iz `wpautop`) —
> **15 spojeva na 14 stranica** + Woo kategorija stranice; **(2)** WoodMart deregistruje
> CF7 CSS i zamenjuje ga part-om koji stiže samo kroz theme element → dva neostilizovana
> CF7 elementa na **svih ~55 stranica** sa „Brzim upitom" (`hidden-fields-container`
> iznad polja, prazan warning `wpcf7-response-output` ispod dugmeta); **(3)** `clip-path`
> paralelogram odseca krakove `inset` rama → ghost dugmad u „Dokumentacija" gridu
> (3 Expona stranice) + nevidljiv hover na svetlim sekcijama. Usput na M zahtev:
> **17 golih `<h2>`** posle `.al-label` dobilo `al-display--lg` (prijavljena 2).
> Verifikovano 17+15 URL-ova (200 / 1×H1 / 0 PHP grešaka); **nijedan tekst nije menjan**
> — samo klase, 3 dana pred content freeze.
> Detalji: [[dnevnik/2026-08-13-faza2-layout-ui-fixes]] · 6 novih lekcija u
> [[reference/naucene-lekcije]]. → [[dnevnik/2026-08-12-alt-tekst-slike-proizvoda]]

### W2 — SEO CONTENT (C3 + GEO)
Master lista: [[seo/plan-novih-stranica]] (20 stranica, 4 tijera). Pravila po stranici: Rank Math SEO score >80 (do 05.08 Yoast), FAQ + FAQPage/Product schema, cena od–do gde ima smisla, CTA 072 + forma, interni link ka `/industrijski-podovi/`, prvi pasus = direktan odgovor (GEO pravilo).

| # | Zadatak | Vlasnik | Zavisi od |
|---|---|---|---|
| 2.1 | ✅ ZATVORENO 2026-07-10 — sve 4 objavljene (16873/16874/16875/16876) sa M1 fallback-om "na upit"; parking sa pravim cenama sa hub-a (2.800–4.200 din/m² PDV). Kad stignu cene (M10) → samo upis u tabele | CC | ~~cene od M~~ → naknadni upis |
| 2.2 | ✅ ZATVORENO (datum nepoznat, potvrđeno 2026-07-27) — Odbojka refresh (#9), post 4318 na lokalu: Yoast title/meta cilja pravi klaster + FAQ+schema+cena u sadržaju. Red ovde bio zastareo ("samo na live") — stranica postoji i refresh je odavno urađen | CC | — |
| 2.3 | ✅ ZATVORENO 2026-07-08 — Title/meta prepis 4 stranice: /pop-tenis/, /podloga-za-odbojkaske-terene/, /spoljnje-podne-obloge/, conquest 2542 (GSC query-level podaci pre pisanja, dedup Yoast postmeta, 074→072 fix u 2542) | CC | +500–700 kl./90d očekivano, prati se |
| 2.4 | ✅ ZATVORENO 2026-07-30 — Tier2: šljaka hub (#7 ✅07-08), tenis dimenzije (#8 ✅07-08), piklbol (#10 ✅07-30, title/meta refresh na postojećoj, ne nova stranica), padel refresh (#11 ✅07-08) | CC | [[seo/plan-novih-stranica]] |
| 2.5 | ✅ ZATVORENO 2026-07-12 — Tier3: #12 kancelarije (title/meta refresh na postojećoj) → #13 restorani (Yoast title bug-fix) → #14 hemijska/prehrambena industrija (nova, ID 17017) → #15 radnje (deprioritizovano, nema GSC potražnje) → #16 zdravstvo (nova, ID 17018) → #17 štamparije (FAQ+schema dopuna) | CC | GSC provera pre svake stavke otkrila da je plan (07-04) delom zastareo — 2 stavke već imale stranice napravljene posle plana (07-08), refresh umesto novih stranica (anti-kanibalizacija) |
| 2.6 | ✅ ZATVORENO 2026-07-12 — Tier4: #18 reference stranice (deprioritizovano, postojeća pokrivenost dovoljna) · #19 /bergo/ brend (nova, ID 17019) · #20 teretane (nova, ID 17020, product-fit potvrđen sa M) | CC | Detalji: [[seo/plan-novih-stranica]] · [[DNEVNIK-NAPRETKA]] |
| 2.7 | ✅ ZATVORENO 2026-07-08 — Product schema na sve WooCommerce proizvode (globalni `functions.php` fix, WC native izlaz se nije renderovao) | CC | Product snippet CTR 10,5% vs prosek 5,5%; bez aggregateRating (nema pravih recenzija) i bez izmišljene cene |
| 2.8 | ✅ POTPUNO ZATVORENO 2026-08-11 · 🔵 **anotacija 2026-08-12:** Google-ov [AI optimization guide](https://developers.google.com/search/docs/fundamentals/ai-optimization-guide) izričito kaže da **Search ne koristi `llms.txt`** („niti štete niti pomažu"), što se poklapa sa našim merenjem ([[analiza/BOT-CRAWLER-LOG]]: 0 organskih hitova / 2 preseka). Fajlovi ostaju (statični, bez održavanja, moguća korist kod ne-Google asistenata) ali **više nisu GEO poluga** — ostatak paketa (LocalBusiness+NAP, „O nama", `robots.txt`) je i po Google-ovim uputstvima ispravan. v. [[seo/geo-ai-plan]] §0 — GEO paket: `llms.txt` kreiran (07-08) + Organization proširen na LocalBusiness+NAP (filter, sitewide). "O nama" bio već gotov od ranije. `robots.txt` na live-u aktiviran `[cpanel-live]` 2026-08-11 (M dodao 9 AI-crawler pravila, provera otkrila i ispravila dupli `User-agent: *` blok + charset mojibake). Preostaje samo PR/GMB deo (van SEO/tehničkog obima) | CC+M | — |
| 2.9 | ✅ ZATVORENO 2026-07-10 (spojeno sa W1 Faza 2 #1 restyle) — GEO intro, goli FAQ JSON-LD popravljen u script tag, al-table, link na /industrijski-podovi-cena/, dupli _thumbnail_id dedupe | CC | title/meta već iz 2.3 |

> 🆕 **2026-08-13 — kanibalizacija i konsolidacija duplikata (ad-hoc po M listi od 9 tačaka).**
> Analiza sa GSC 90d podacima po stranici → [[seo/2026-08-13-kanibalizacija-konsolidacija-plan]].
> **Izvršeno (M odobrio 3 od 8 predloženih):** cenovni sadržaj sa 16876 → **16589**
> (1.197 prikaza / 98 kl., poz. 1,0–1,8) uz 2 nove FAQ stavke · **16683 → draft**, primarna
> **16142** · **Bergo Easy 16665 → draft** (diskontinuiran), sadržaj + 8 event fotografija →
> **16663**. 301 mapa dopunjena i **4 istorijska pravila sa 365 GSC pogodaka spljoštena**
> (vodila su na upravo draftovane stranice).
>
> **✅ Nastavak iste liste, sesija 13.08 popodne (M odobrio još 3 stavke):**
> **(A)** `/ergonomske-podloge/` — čist slug (prilog 12489 je držao slug, ne druga stranica),
> istorijsko pravilo `/ergonomski-podovi/` (160 pogodaka) pretočeno na nov cilj ·
> **(J)** `/sta-postaviti-preko-starog-parketa-ili-plocica/` — sadržaj iz `-2` (prepis 09/2025)
> preseljen na čist slug, original iz 2022 draftovan; **ovo je ukinulo odluku od 30.07** ·
> **FAQ klaster u celosti** — sve **tri** stranice (2622 · 3274 · 17025, zajedno **0 klikova
> u 12 meseci**) draftovane i 301-ovane na `/industrijski-podovi/`, koji drži „industrijski
> podovi" na poz. **6,7** (16.417 prikaza / 410 kl.); hub dobio **8 novih FAQ pitanja
> (ukupno 15) i FAQPage JSON-LD koji do tada uopšte nije imao**. 🔴 Istorijsko pravilo sa
> **615 pogodaka** pretočeno na hub — bez toga bi posle migracije išlo na 404.
> **Time su svi `-2` slugovi na buildu rešeni.** 301 draft: **79 pravila**, svi ciljevi 200.
> → [[dnevnik/2026-08-13-kanibalizacija-nastavak]] · [[seo/posle-live-postovi-izbor-industrijskog-poda]]
>
> ⚠️ **Sistemski nalaz iz iste sesije:** zapisane GSC brojke u migracionim CSV-ovima su se
> **tri puta** pokazale netačne (`gsc_klikovi` u `parity-inventar.csv` 2×, obrazloženje reda 17
> u `redirect-mapa-FINAL.csv` 1×). **Pre svake odluke ide svež pull**, ne oslanjanje na zapis.
>
> ✅ **(E) ZATVORENA 2026-08-13** — `/sportske-podloge/` (5438) vratila basket-semantiku
> (dve sekcije doslovnim live tekstom), planer link i FAQPage schemu; 10.328 → 15.129 B,
> render 8×H2 / 3 JSON-LD. 🔴 Usput ispravljena brojka iz same ove analize: basket klaster
> nosi **138 od 178 kl./90d (78%)**, ne „~90" kako je gore procenjeno — stavka je bila
> potcenjena. → [[dnevnik/2026-08-13-5438-basket-semantika-faqpage]]
>
> 🔴 **Jedna stavka ostaje otvorena, rok content freeze (16.08):** **(F)** 4 nove „dimenzije" stranice
> (16585/16586/16688/17027) gađaju upite koje post **2298** drži sa pozicije **1,0–1,9**
> (13.686 prikaza / 385 kl./90d), bez canonical-a i bez ijedne uzajamne veze.
> 🟡 Ads: 2 URL-a na tuđem domenu + 11 na mrtvim `/home/…` putanjama — blokira **reaktivaciju**
> pauziranih kampanja (4.4), ne migraciju. → [[dnevnik/2026-08-13-konsolidacija-kanibalizacija]]

### W3 — SEO TEHNIČKA + MIGRACIJA (C1/C2 + CWV)
> 🔄 **2026-07-07: C1/C2 pristup zamenjen parity strategijom** — build se pravi 1:1 prema live sajtu, redirect mapa se svodi na minimum. Izvor istine: [[migracija/PARITY-PLAN]], izvršenje kroz promptove [[migracija/promptovi/_README]] (F1–F7). Stare mape arhivirane u `migracija/arhiva/`.

| # | Zadatak | Vlasnik | Napomena |
|---|---|---|---|
| 3.1 | ✅ ZATVORENO 2026-07-21 — F1 reosvežen (2 nedelje posle baseline-a): live URL skup nepromenjen, PARITY skočio 47→129/143 zahvaljujući W1/W2 radu, svi preostali non-PARITY redovi već imaju odluku u redirect-mapa-FINAL.csv osim 1 poznatog M-odluka stavke (FAQ konsolidacija, 15 kl., ne blokira). 5 lažnih "path mismatch" nalaza razrešeno (WP rewrite prihvata i ugnježden i flat oblik). Detalji: [[migracija/PARITY-PLAN]] §2.1 · **DOPUNA 2026-07-28**: inventar je imao blind spot — live export od 07-05 sadrži samo `publish` status, pa 2 stranice koje su bile draft (objavljene 27.07) nisu bile u njemu. Obe rebuild-ovane i upisane (17028, 17029), 175→177 redova. **Pravilo: svaki naredni live export mora uključivati i draftove.** | CC | [[migracija/promptovi/F1-parity-inventar]] |
| 3.2 | ✅ ZATVORENO 2026-07-07 — `/sportske-podloge/kosarkaske-konstrukcije/` (923 GSC kl.) rešeno kao F6 pilot (ID 16657, namenska landing, identičan URL kao live → redirect nepotreban) | CC | [[migracija/promptovi/F6-namena-arhitektura]] |
| 3.3 | 🔄 OBRNUTO: parity kaže `/aktuelnosti/` OSTAJE (lokalni `/blog/` se preimenuje) — deo F2 | CC | [[migracija/promptovi/F2-permalink-fix]] |
| 3.4 | 🔄 ZAMENJEN → **F2 permalink fix**: Woo `product_base` → `/proizvod/` flat + `category_base` → `/kategorija-proizvoda/` (kao live) — briše ~47 redirect redova jednom izmenom | CC | [[migracija/promptovi/F2-permalink-fix]] |
| 3.5 | ✅ ZATVORENO 2026-07-09 — Lighthouse 13.4 baseline (7 prolaza) → [[dnevnik/PERFORMANCE-AUDIT]]. Usput: XAMPP opcache uključen (TTFB 8–10s → 2,4–3,4s) + ThreadStackSize crash fix. Mobile Perf 24–48, LCP 8,6–20,4s | CC | redosled za 3.6 u auditu (top: RevSlider off, home PNG→WebP, CLS stretch-row) |
| 3.6 | CWV optimizacija: LCP <2,5s mobile (slike, lazy load, skripte, fontovi ✓ već lokalni), CLS <0,1, INP <200ms | CC | ✅ **CLS gate pogođen 2026-07-12** (font-preload fix, pravi uzrok bio font-swap ne stretch-row kako je baseline pretpostavio — videti [[dnevnik/PERFORMANCE-AUDIT]] sek. 5). ✅ **Nizak-rizik dorada zatvorena 2026-07-21**: unsized-images (4 slike, score 0,5→1,0) + latin-ext font subsetting (85KB→3,6KB/fajl, samo srpska latinica ćčđšž). ✅ **TBT/INP proxy re-meren i praktično zatvoren 2026-07-22**: `wc-order-attribution`+`sourcebuster-js` (mrtav sitewide JS, catalog_mode uklonio checkout) dequeue-ovan — home/kategorija TBT sad ispod 200ms gate-a, proizvod stranice poboljšane 874→350ms ali formalno iznad (WooCommerce related-products re-enqueue `wc-add-to-cart-variation`, dokumentovan a ne rešen — niska šteta). Detalji: [[dnevnik/PERFORMANCE-AUDIT]] sek. 6. 🔴 LCP i dalje crveno, blokirano na render-blocking CSS (js_composer 437KB) — namerno odloženo na LiteSpeed Critical CSS/UCSS na produkciji (visok rizik ako se radi ručno lokalno), najveća poluga konverzija (mobile = 3/4 svega). ✅ **Dodatan nizak-rizik lokalni korak ipak nađen i zatvoren 2026-08-07**: sitewide dequeue celog WC add-to-cart JS steka (10 handle-ova, mrtav teret otkako katalog režim/M9 zamenio cart dugmad linkovima) + `jquery-migrate` uklonjen kao jQuery dependency, oba u child temi, oba testirana (WC dequeue curl-om, jQuery Migrate uživo kroz Chrome uklj. court builder canvas). Detalji: [[DNEVNIK-NAPRETKA]] 2026-08-07. Sledeći napredak na samom LCP gate-u i dalje čeka produkciju (brojčana potvrda posle UCSS re-enable 2026-08-07) |
| 3.7 | ✅ POTPUNO ZATVORENO 2026-08-11 — sitemap čišćenje (25 orphan legacy CPT posta publish→draft, 4 prazna sitemap-a nestala iz indexa) + fizički `robots.txt` (AI crawleri GPTBot/ClaudeBot/PerplexityBot/Google-Extended/CCBot + Sitemap linija, isti obrazac kao llms.txt) na lokalu (07-21), **live aktivacija završena `[cpanel-live]` 2026-08-11** (v. 2.8) | CC+M | — |
| 3.8 | ✅ ZATVORENO 2026-07-21 — N/A u izvornom obliku: catalog_mode (M9) je uklonio cart/checkout/my-account stranice u potpunosti (ID-evi u opcijama ne postoje u bazi) — pravi tok je "Zatražite ponudu"→`/kontakt/?form-naslov=`→submit→`/hvala-za-poruku/`, testiran end-to-end (prefill potvrđen). F2 permalink regresija: 6 nasumičnih proizvoda + 4 kategorije pod `/proizvod/`/`/kategorija-proizvoda/` sve 200, Product schema 1× bez dupliranja | CC | — |
| 3.9 | ✅ **REVERIFIKOVANO I REGENERISANO 2026-08-11** — provera od 21.07 je gledala samo `redirect-mapa-FINAL.csv` (7 ciljeva) i propustila da draft ne sadrži **62 istorijska pravila iz Redirection plugina** (~46.000 GSC pogodaka), koja nestaju zajedno sa živom bazom pri migraciji. Draft **8 → 73 pravila**. Usput razrešeno: 🔴 petlja između dve mape (`/na-kojoj-podlozi…/` ↔ `/bergo-ultimate…/`) · 🔴 2 istorijska pravila koja bi pregazila stranice 16686 (588 GSC) i 16875 (182 GSC) izgrađene posle tih pravila · 🔴 `Redirect` je prefiks-match (15 kolizija) → sidreni `RedirectMatch "^/put/?$"`, redosled linija više nije bitan · 🟡 pogrešan cilj `spoljne-` bez „j" u FINAL mapi · 🟢 ćirilične putanje testirane pod Apache-om, stara „fallback `RewriteRule \x`" ograda skinuta. Draft se od sada **generiše skriptom** (`migracija/alati/htaccess-301-generate.php`, odbija upis ako cilj nije 200); verifikacija `migracija/alati/redirect-verify.php`. Funkcionalni test u izolovanom Apache folderu: 8/8 tačan 301, 3/3 negativna kontrola. Detalji: [[dnevnik/2026-08-11-htaccess-301-reverifikacija]] | CC | aktivira se TEK na dan migracije, iznad `# BEGIN WordPress` |
| 3.10 | 🔄 **DELIMIČNO ZATVORENO 2026-08-10 — regression ✅, checklist napisan ✅, izvršenje na dan migracije ⏳** · Pre-migration checklist + full regression (forme, GTM, linkovi, slike) + 🔴 obrisati `al-harness.html` iz docroot-a (alat za vizuelnu proveru na 1500/390px, W7 F3 2026-07-29 — koristan lokalno, ne sme na produkciju) + 🔴 obrisati `mu-plugins/al-local-mail-log.php` i `wp-content/mail-log.txt` (lokalni mail logger presreće SVE mejlove — na produkciji forme ne bi slale ništa) + verifikovati stvarno slanje mejla na produkciji + 🆕 **GTM paket za dan migracije** (Enhanced Conversions promenljive/tag + brisanje mrtvih Meta Zion objekata) po [[migracija/2026-08-09-enhanced-conversions-4.7]] | CC | ✅ **REGRESSION DEO ZATVOREN 2026-08-10** — pun sweep nad 195 stranica (`migracija/alati/regression-sweep.php`, baseline u `analiza/2026-08-10-regression-baseline-pages.csv`): 4 bag-a nađena i popravljena (sitewide 404 u footeru na svih 195 str · 5 slomljenih slika · 3 interna 301 · 27 `.bak` fajlova koji se serviraju kao izvorni kod). Posle popravki: 0 non-200 / 0 bez H1 / 0×2H1 / 0 nevalidan JSON-LD / 0 slomljenih slika / 0 internih 404. Paket-skripta dobila exclude za `al-local-mail-log.php` i `*.bak-*`. **Checklist deo napisan i živi odvojeno: [[migracija/2026-08-10-pre-migration-checklist]]** (A: do 21.08 · B: dan migracije). Detalji: [[dnevnik/2026-08-10-w3-310-full-regression]].<br>N7 — 🔄 **rani start 2026-07-22**: GTM/Consent kritičan nalaz+fix, forma end-to-end (kontakt→hvala-za-poruku→generate_lead potvrđen preko network requesta), sitewide 214-URL sweep (0 pokvarenih, 7×2H1 fix, 13×putanja fix) — sve gotovo osim: WooCommerce checkout (N/A, catalog_mode uklonio), mail-log.php brisanje (namerno čeka do dana migracije, lokalno testiranje ga i dalje treba), finalni URL audit oglasa (čeka aktivne kampanje)<br>🆕 **2026-08-12 — DOPUNA, ne zamena: [[migracija/2026-08-12-preflight-checklist-24-08]]**. Postojeći `2026-08-10-pre-migration-checklist` je pisan unapred („šta treba uraditi"); ovaj je izvučen unazad iz **87 dnevnika** (`dnevnik/` 50 + `migracija/` 37) i beleži **šta je već jednom puklo** — 19 rizika sa izvorom i datumom, 11 ručnih radnji na dan migracije, **6 konflikata u dokumentaciji**. Posao delegiran na `agy`/Gemini Flash (v. skill `/agy-delegat`), nalazi ukršteni sa [[reference/naucene-lekcije]] i verifikovani protiv koda. 🔴 Tri stavke otvorene kao blokeri u [[PROGRESS]]: pogrešan prefiks `wpGs_` u [[CLAUDE]] · `live-export.sh` ne skuplja `_product_image_gallery` · GCP app u statusu *Testing*. → [[dnevnik/2026-08-12-agy-antigravity-delegat]] |
| 3.10b | ✅ **REGRESSION SWEEP PONOVLJEN 2026-08-13, posle FAZE 1 i FAZE 2** — prethodni je bio od 10.08, a u međuvremenu su prošle tri sesije koje diraju sitewide (vizuali/mediji, alt tekst/brend arhive, layout u dizajn sistemu). **239 stranica · 1.158 slika · 1.801 link: 0 non-200 · 0 bez H1 · 0×2H1 · 0 nevalidan JSON-LD · 0 slomljenih slika · 0 internih 404.** Protiv baseline-a 10.08 na 194 zajednička URL-a **0 razlika** u statusu/H1/JSON-LD/title → **FAZA 2 nije polomila ništa**. 🔴 Prividna regresija **−118 slika po stranici** objašnjena: uklonjene **ikonice mega menija** 12.08 (59 linkova × 2 renderovanja), jedinstvenih slika 1.182→1.158 — nestao globalni blok, ne slike; usput `imgs_noalt` **23.010→0**. 🆕 **31 arhiva bez meta description** (18 `product_tag` = poznato, posle live-a; 6 blog kategorija + 6 `product_cat` + `brend/bergo` = jedini sadržajni posao koji staje pre freeze-a). **301 mapa reverifikovana** (`redirect-verify.php`, jer je 5455 draftovan posle regeneracije 11.08): **45/45 ciljeva 200**, 0 duplikata/petlji → draft ostaje važeći, ne regeneriše se. Nov baseline: `analiza/2026-08-13-regression-post-faza2-*`. → [[dnevnik/2026-08-13-regression-sweep-post-faza2]] | CC | zamenjuje 10.08 baseline za post-migracionu proveru |
| 3.10c | ✅ **DRY-RUN `build-staging-package.sh` IZVRŠEN 2026-08-13** — skripta poslednji put pokrenuta 06.08, exclude pravila dodata 10.08 **nikad izvršena**, a preflight rizici #1/#4 (🔴🔴) vise baš na njima. **Rezultat: pravila rade** — u arhivi (22.936 unosa) nema `al-local-mail-log.php`, `mail-log.txt`, nijednog od **32** `.bak`-klase fajla, `al-harness.html`, ~20 debug PHP skripti, `wp-config*.php`, ni Yoasta; chunk+md5 ispravan (`cat part-*` bajt-identičan). Usput **2 kvara koje skripta nije mogla sama da prijavi**: **(a)** hardkodiran `WP_ROOT`/`OUT_DIR` → dry-run se nije mogao pustiti van produkcione fascikle (tačan razlog zašto nikad nije testirana), popravljeno kao `PFX`/`OUT` u `live-export.sh`; **(b)** 🔴 **`.htaccess` u root whitelist-i** — lokalni nosi `RewriteBase /antasline/` + `RewriteRule . /antasline/index.php`, prepisao bi serverski i oborio sajt u celosti, uz gubitak `# BEGIN LSCACHE` bloka (checklist B3 ionako kaže da se serverski fajl **edituje**) → izbačen. **(c) 🔴 Kvota:** paket je **2.779,2 MB** (uploads 2.706,9 + kod 72,3), ne ~1,3 GB kako je pre-flight računao; naivan tok (delovi + sklopljen tar) traži 5.558 MB od 5.867 MB slobodnih → ne staje uz backup. Preporuka za 24.08: rsync/scp preko SSH-a, ili stream-raspakivanje bez sklapanja + brisanje delova u hodu (pik ~4,4 GB ✅). → [[dnevnik/2026-08-13-dry-run-build-staging-package]] | CC | preduslov za 3.11, zatvara preflight #1/#4 |
| 3.10a | ✅ ZATVORENO 2026-08-12 — **migracione skripte popravljene i prvi put testirane**: `live-export.sh` je gubio **145 od 170** galerijskih slika (nikad nije čitao `_product_image_gallery`; dodate i `product_cat` slike iz `termmeta`, tvrda provera pred dump, `PFX`/`OUT` pregazivi radi testiranja) · `staging-import.sh` je prepisivao prefiks u **`wpGs_`** umesto `wpgs_` (na Linux-u pravi pogrešne tabele bez greške). Testirano uživo na lokalnom buildu: 196→**341** attachment. 🔴 Tri gotcha-a otkrivena tek pokretanjem (višelinijski `wp db query` = prazno sa exit 0 · `--no-create-info` mangla WP-CLI 2.12 · Windows CRLF prazni liste). → [[dnevnik/2026-08-12-live-export-galerije-prefiks]] | CC | preduslov za 3.11 |
| 3.11 | **MIGRACIJA 2026-08-24** (1 dan, pomereno sa 31.08): backup live → db+wp-content prebacivanje → URL zamena → 301 aktivacija → verifikacija | CC+M | SSH/hosting info #ceka-miroslav |
| 3.12 | Post-live (od 2026-08-25): GSC sitemap resubmit, crawl errors, GA4/GTM verify, CWV field data + UptimeRobot (besplatan) + dnevni 404 log pregled prvih 14 dana (umesto ad-hoc) | CC+CP | 🔄 **GSC priprema ZATVORENA 2026-08-11** (checklist §A): resubmit URL **nepromenjen** (`sitemap_index.xml`, ista imena child fajlova kao Yoast) → nijedan submit-ovan URL ne puca migracijom. 🔴 Usput nađeno da su svi `tax_*_sitemap` ključevi bili `off` posle Yoast→Rank Math importa — build je emitovao **3 sitemap-a gde live emituje 7**, van pokrivenosti 27 URL-ova sa **79 klikova / 2.583 prikaza** (3 mes.); uključeno `category`+`product_cat`+`product_tag`, sitemap 196→**236 URL-ova**, 42/42 verifikovano. ~~Brend sitemap namerno off (arhive prazne → i 301 cilj pogrešan, #ceka-miroslav).~~ ✅ **REŠENO 2026-08-12 (M odluka, opcija a):** Ecotile arhiva dobila **7** proizvoda, Ergomat **27** (nijedan od 94 proizvoda nije nosio `product_brand` termin — brojači 25/3 bili su 7 Porto priloga); obe arhive 200 / 1×H1 / `index, follow` sa pravim title/meta/uvodnim pasusom, `tax_product_brand_sitemap` uključen → sitemap **7 child-ova / 238 URL-ova** (parity sa live). 301 ciljevi `/бренд/*` više ne vode na praznu stranicu, draft se ne regeneriše. → [[dnevnik/2026-08-12-product-brand-arhive]]. Nov alat `scripts/gsc_sitemaps.py`. → [[dnevnik/2026-08-11-gsc-priprema-sitemap]] |
| 3.13 | ✅ ZATVORENO — ⚠️ reotvoreno i ponovo zatvoreno 2026-07-09: scheduled task NIKAD nije stvarno radio (odbijan — baterija uslov + bez catch-up-a; popravljeno `Set-ScheduledTask`). Nova politika (M): destinacija **eksterni HDD G: "Maxtor" kad je prikačen** → OneDrive → lokalno; propušteni backup izvršen na G: 2026-07-09. Proveriti `LastTaskResult=0` posle sledeće noći | CC | OneDrive više nije blokator (G: je druga lokacija) |
| 3.14 | ✅ ZATVORENO 2026-07-21 — proba migracije na `staging.antasline.com` kompletirana: wp-config kreiran (DB lozinka dobijena od M), 118-tabelni import, URL rewrite 11.451 zamena, rewrite flush, Basic Auth aktivan (`stagingtest`, kredencijali u `~/staging-htaccess-creds.txt` na serveru, van vault-a), arhiva/sql sklonjeni iz docroot-a. Verifikovano: 401 bez auth / 200 sa auth na homepage i `/industrijski-podovi/`, ispravan naslov "Početna \| Antas Line". 🟡 Nusnalaz: import je stvorio 9 "duh" tabela sa starim `wp_` prefiksom (mnogo manje redova od pravih `wpgs_` tabela — npr. 1964 vs 7992 posts) — verovatno artefakt generisanja dump-a na lokalu, ne brisano (nije korišćeno od WP-a, niska prioritetna čistka za kasnije) | CC+M | — |
| 3.15 | ✅ ZATVORENO 2026-07-21 — top 20 GSC upita (klikovi, 28d) + rizik-grupa 6 upita (visoke impresije/slaba pozicija) snimljeni kao baseline; live SERP spot-check za kontekst konkurenata (napomena: browser nije RS-geolociran, GSC pozicija merodavna) → [[analiza/2026-07-21-serp-snapshot-pre-migracija]] | CC | bez ovoga ne znamo da li post-migracija pad je naš (301) ili konkurent skočio |

### W4 — ADS
Fazni plan i RSA banka: [[dnevnik/ADS-DNEVNIK]]. Strategija ostaje **Maximize Clicks** do 20–30 plaćenih konverzija.

| # | Zadatak | Vlasnik | Efekat |
|---|---|---|---|
| 4.1 | ✅ ZATVORENO 2026-07-06 — lista potvrđena na obe kampanje, +13 novih negativnih (epoksi+padeži, betonski, "industrijski beton", [podne obloge], teraco, letvice, pevex, "uradi sam", pločice-phrase), KW "bastenski namestaj"/"oprema za bazene" pauzirani; `laminat` svesno izostavljen (watch) | M (15 min) | −16% curenja odmah |
| 4.2 | ✅ ZATVORENO 2026-08-06 — nalog-širok throttling potvrđeno prošao (odblokiranje 07-04 drži), ALI CPC nastavio da raste (52,20→78,98 RSD) zbog kampanja-specifičnog uzroka: dnevni budžet 1.300 RSD gubi 50% prikaza na spike-danima (`search_budget_lost_impression_share`). Terase paralelno zdrava. #ceka-miroslav: budžet povećanje odluka (ne blokira gate) | CC (konektor) | zatvara Fazu 0 |
| 4.3 | Faza 1: RSA Terase (15 headlines + 4 desc iz banke) → Ad Strength ≥ Good | M+CC | QS ↑ |
| 4.4 | Faza 2: ad grupe — Terase → terase/bazeni/bergo · ECOTILE → industrijski/esd; svaka grupa svoj RSA; "pvc podovi za bazene" → svoja grupa + landing /podovi-za-bazene/ | M+CC | IS 24%→40%+ isti budžet |
| 4.5 | Skalirati ECOTILE phrase "industrijski podovi" (1.073 RSD/konv ⭐) + geo BG/NS/Niš | M | najjeftinije B2B konverzije |
| 4.6 | ✅ RE-VERIFIKOVANO 2026-08-06 — pravilo i dalje tačno u smeru, brojke ažurirane sa sveže GSC (06.07–03.08): terase-cena klaster top-3 (poz. 1,6–3,2, NE plaćati) · terase/industrijski mid-tail slab (poz. 7,1–13,4, vredi plaćati) · trenutna Ads struktura ostaje ispravna | CC (analiza) | — |
| 4.7 | ✅ **LOKALNI DEO ZATVOREN 2026-08-09** — Faza 3: Enhanced Conversions (SHA-256 email/tel kroz GTM). Prenos email/telefona kroz `sessionStorage` (`al_lead_*`) od `wpcf7mailsent` do `/hvala-za-poruku/`, jer redirect briše vrednosti forme pre konverzije; obe CF7 forme pokrivene, normalizacija telefona na E.164 verifikovana na 12 graničnih slučajeva, bez regresije na postojećim konverzijama. 🔴 Ključni nalaz: Meta ključevi `al_am_*` se NE mogu deliti (GTM Meta base code ih briše posle čitanja). GTM izmene namerno odložene za dan migracije (live Zion forma ne piše ključeve → prazan hod). Pun spec: [[migracija/2026-08-09-enhanced-conversions-4.7]]. **#ceka-miroslav: Ads UI korak** (konverzija „Lead - forma (GTM)" → Enhanced conversions → on, metod GTM, prihvatiti customer data terms) | CC | preciznija atribucija |
| 4.8 | Na 20–30 plaćenih konverzija → Maximize Conversions; broad tek tada | M | 🔴 **2026-08-11 — PRAG NIJE DOSTIGNUT, ranija tvrdnja „26, prag pređen" je netačna.** Od „26 plaćenih konverzija" **17 su klikovi na telefon** — akcija `Klik na telefon (web)` ima `include_in_conversions_metric=True`, dakle ulazi u „Conversions" i u Smart Bidding, suprotno [[CLAUDE]] §4. **Pravih plaćenih lidova (forma): 9.** Preporuka „odložiti na ~01.09" ostaje, ali iz jačeg razloga. 🔴 Usput: **broad već radi sada** (6 BROAD reči, ~10.300 RSD/90d, **0 konverzija**) iako pravilo kaže „broad tek uz Smart Bidding". #ceka-miroslav: prebaciti tel akciju u Secondary + pauzirati broad. v. [[analiza/2026-08-11-snapshot-jul]] §3.3/§3.6 |
| 4.9 | Faza 4: call asset 072, mobilni bid +15–20%, publike u Observation, Customer Match (email-ovi iz upita) | M+CC | zaobilazi prag publika — 🔴 Customer Match deo, ažurirano 2026-08-11: uživo `--confirm` pokušaj (posle uspešne re-autorizacije) i dalje pada na isti `CUSTOMER_NOT_ALLOWLISTED_FOR_THIS_FEATURE` kao 2026-08-07, ali **korigovana dijagnoza** — Google dokumentacija (WebSearch/WebFetch potvrđeno) kaže da developer tokeni bez istorije Customer Match zahteva preko starog `OfflineUserDataJobService`-a moraju na nov **Data Manager API**, bez obzira na Basic/Standard tier; ranija pretpostavka „treba Standard access" nije potvrđena kao rešenje. #ceka-miroslav: odluka o migraciji na Data Manager API (setup plan: Korak G u [[reference/api-konektor-setup.md]]) — v. [[PROGRESS]] Blokeri, [[dnevnik/2026-08-11-customer-match-data-manager-api]] |
| 4.10 | Na dan migracije: final URL audit svih oglasa (novi slugovi!) — 🔄 **PRIPREMA DELIMIČNO GOTOVA 2026-08-11**: napisane 3 read-only alatke (`ads_final_urls.py` Ads API, `ga4_paid_landing.py`, `migracija/alati/ads-url-audit.php` koji poredi spisak URL-ova sa buildom **i** sa 73 pravila iz drafta). GA4 presek plaćenog saobraćaja (3 mes., 31 putanja): **29 OK / 0 PREPISATI / 0 PUKAO**. 🟢 Izmereno da `?gclid=` preživljava 301 (inače bi preusmeren klik izgubio atribuciju konverzije). **Konačan rezultat (41 URL): `OK` 32 · `PREPISATI` 6 · `EKSTERNI-DOMEN` 2 · `PUKAO` 1 (artefakt) · `REDIRECT-BUILD` 0.** 🟢 **Za dan migracije nema posla** — od 14 kampanja **samo je jedna ENABLED** („ECOTILE INDUSTRIJSKI PODOVI", 1 RSA + 6 sitelinkova), svih 7 URL-ova 200 na buildu. ⚠️ „Podloge za terase i bazene" je **PAUSED** (§6 ovog plana i [[CLAUDE]] §6 govore o „obe aktivne kampanje" — zastarelo). 🔴 **Pre reaktivacije pauziranih kampanja** (blokira i 4.4): 6 URL-ova za prepis + **2 koja vode na tuđi domen `ekopodneploce.rs`** gde 301 mapa ne pomaže. → [[migracija/2026-08-11-ads-final-url-audit]] | CC+M | čuva QS posle migracije |
| 4.11 | 🆕 2026-08-04 — **Meta (FB) Pixel** preko GTM-a (isti kontejner, isti Consent Mode gate): `PageView` svuda + `Lead` na `/hvala-za-poruku/` (isti trigger kao `generate_lead`) + `Contact` na tel/mailto — ista taksonomija kao GA4, gradi audience/Lookalike podatke i pre nego što krenu pravi Meta Ads budžeti. Pokriva i Instagram (isti Meta Business Manager/Pixel, nema poseban rad). Faza B (Conversions API, server-side) odložena do stvarnog Meta Ads budžeta — traži Business Manager + verifikovan domen + bezbedno čuvan access token | M+CC | zaobilazi iOS/ad-blocker gubitak (Faza B), gradi retargeting bazu ranije (Faza A) |
| 4.12 | 🆕 2026-08-04 — **LinkedIn Insight Tag** preko GTM-a (isti kontejner, isti Consent Mode gate) — gradi matched audience/retargeting bazu za B2B segment (ESD/industrijski podovi, poslovni prostori), gde LinkedIn targeting (industrija/veličina firme/pozicija) bolje pogađa nego Meta/Google demografija. Sadi se sad, koristi se kad krene pravi LinkedIn Ads budžet — CPC znatno skuplji od Google/Meta, pa plaćene kampanje čekaju posebnu M odluku | M+CC | retargeting baza spremna unapred za B2B; ne pokreće trošak sam po sebi |
| 4.13 | 🆕 2026-08-04 — **Display remarketing (Google Ads)**, NE cold-prospecting. Uslov za start: (a) 4.8 zatvoreno (Maximize Conversions dostignuto) I (b) bar jedna GA4 publika pređe prag serviranja (100 za Display/YouTube — trenutno sve 4 ispod praga). Cilja postojeće publike (Form Abandoners, High-Intent B2B Bidders) — CPM, ne konkuriše Search budžetu | M+CC | jeftin, visoko-kvalifikovan dodatni kanal, tek posle preduslova |
| 4.14 | 🆕 2026-08-04 — **Video/YouTube oglasi** — čeka i budžet i pravi video materijal (court builder demo, montaža terena, pre/posle transformacija poda); generički/stock video za nišni B2B proizvod (ESD/industrijski podovi) obično ne opravdava CPM. Ne pokretati bez konkretne kreative | M+CC | potencijalno jak kanal za demo-proizvod, ali samo uz pravu produkciju |<br>🟢 **2026-08-09 — polovina blokade skinuta: kreativa se sada pravi.** Google Flow (Veo 3.1) daje 8s klipove iz naših pravih fotografija terena, besplatno (50 kredita/dan, Lite 10 / Fast 20). Prvi materijal postoji: 4 klipa + izmontiran video 30,5s za basket. **I dalje NE pokretati** — (a) budžet nije odobren, (b) Ads ostaje na Maximize Clicks dok 4.8 ne prođe, (c) GA4 publike su ispod praga serviranja 100 za YouTube. Vertikalni 9:16 rez je poseban render, ne besplatan krop. v. [[seo/2026-08-09-video-obogacivanje-plan]] §5<br>🟢 **2026-08-10 — prvi video KOMPLETAN** (5 kadrova, 40s, sa tekstom i CTA `069 234 00 72`), a **sajt je tehnički spreman da ga primi**: lazy facade (F7.3) + `VideoObject` schema (F7.3a) verifikovani na 9 stranica. 🟢 **2026-08-11 — žig rešen (opcija 3): kadar 5 rerenderovan u Flow-u, video remontiran na 38,0s, potpuno bez žiga** (v. [[dnevnik/2026-08-11-flow-kadar5-rerender-dnevni-tok]]). Objava još čeka **YouTube handle** i odluku o **registarskoj tablici kombija na kadru 4**. Ads pokretanje **i dalje ne** — uslovi (a) budžet, (b) 4.8 Maximize Conversions, (c) publike ispod praga 100 — nepromenjeni. |

### W5 — TRACKING / MERENJE (GA4 + GSC + GMB)
| # | Zadatak | Vlasnik | Napomena |
|---|---|---|---|
| 5.1 | ✅ ZATVORENO 2026-07-21 — jul (1.–20.) čist: sumirano generate_lead+tel+mailto = 59/20 dana → projekcija ~92/mes, u okviru cilja. 🔴 Usput nalaz: Windsor `conversions` agregatno polje je bilo kontaminirano 06-17→06-22 (8 dodatnih evenata pogrešno markirano kao key event u GA4 adminu, do 1212/dan) — self-rešeno do 06-23, jul čist, nova lekcija u [[reference/naucene-lekcije]] | CC (Windsor) | sredina jula |
| 5.2 | ✅ URAĐENO 2026-07-06 — GMB: UTM fix (`utm_source=google&utm_medium=gmb&utm_campaign=local`), kategorije proširene (+Gradnja sportskih terena, +Pružalac usluga), prvi post kreiran (Bergo/Naxos kampanja); review link spreman za korisnike | M | Efekat: GMB saobraćaj merljiv u GA4, prvi post live, review kampanja čeka poslove |
| 5.3 | GMB recenzije 6 → 20+ (email posle svakog posla) | M | trust + local pack + GEO |
| 5.4 | Nedeljni mini-izveštaj (7d vs 7d po formatu [[CLAUDE]] §10) + mesečni puni snapshot ([[analiza/_TEMPLATE-snapshot]]) | CC | ✅ **2026-08-11 — izveštaj 04–10.08 vs 28.07–03.08** (kasnio 2 nedelje). 🔴 Glavni nalaz nije performansa nego **merenje**: `/hvala-za-poruku/` ima 10 sesija a GA4 beleži 26 pregleda i 39 `generate_lead` — cela KPI serija „prave konverzije" broji preglede (kumulativ od 01.06: 119 pregleda = **51 sesija**). Obrazac deterministički od jula (`generate_lead = 1,5 × pregleda`). Dijagnoza pre freeze-a 16.08, jer GTM paket EC-a (4.7) visi na istom tagu. 🔴 Drugi nalaz: konektorovi totali uključuju `localhost` (prethodna nedelja 42% pregleda!) → svi izveštaji se filtriraju na `hostName`. Ads: ECOTILE CPC +58% (101 RSD), Terase pauzirana, plaćene kumulativ **26**. → [[dnevnik/2026-08-11-w5-nedeljni-izvestaj]].<br>✅ **DIJAGNOZA ZATVORENA 2026-08-11 (uveče), 5 dana pre roka** — dva odvojena baga: **(A)** suvišan GA4 `page_view` tag **id 18** na hvala pravilu duplira automatski `page_view` Google taga → hvala-proxy je tačno **2×** stvaran broj dolazaka; postoji **i na buildu** → **preživljava migraciju**. **(B)** live Kallyas ima **dva GTM embeda** istog kontejnera → `generate_lead` 3×; WoodMart build ima jedan → 1× → **nestaje sam migracijom**. Izmereno u mreži (`g/collect` po `en=`), sa lokalnim buildom kao kontrolnom grupom. 🔴 **Post-live: `generate_lead` pada na ~⅓, hvala-proxy na ~½ — nije pad konverzija.** #ceka-miroslav: brisanje taga id 18, **kandidat za dan migracije** uz EC (4.7) i Meta Pixel čišćenje — namerno **nije** upisano u migracioni checklist bez odluke. → [[dnevnik/2026-08-11-generate-lead-inflacija-dijagnoza]].<br>~~✅ 2026-07-30 — 23-29.07 vs 16-22.07, plaćene konv. kumulativ 18/30.~~<br>✅ **MESEČNI SNAPSHOT (jul) URAĐEN 2026-08-11** (kasnio 11 dana) → [[analiza/2026-08-11-snapshot-jul]]. 🔴 **Dva nalaza koja menjaju plan:** **(1)** „26 plaćenih konverzija" = **17 tel + 9 forma** — akcija `Klik na telefon (web)` ulazi u „Conversions" kolonu i u Smart Bidding, suprotno [[CLAUDE]] §4 → **prag za 4.8 nije dostignut, pravih plaćenih lidova ima 9**; #ceka-miroslav (prebaciti u Secondary). **(2)** KPI tabla §5 ovog plana meri **preglede**, ne lidove — jun je 24 sesije, ne 55; traži prepravku (v. §5 napomenu). 🟢 Organski: jul YoY pozicija **8,2→6,0**, prikazi **+22%**, CTR **6,76→4,52%** → pad klikova je SERP (AI Overviews), ne regresija sajta; GA4 engagement **62,1% = najviši u 16 meseci**, korisnici **+5% YoY**. 🔴 Ads: ~10.300 RSD/90d na 6 BROAD reči sa 0 konverzija; pauzirana Terase efikasnija od aktivne ECOTILE; izgubljeni prikazi zbog **ranga** 52–55% (ne budžeta). Usput: `ga4_report.py` dobio `--live-only` flag + korekcioni faktori upisani u `/nedeljni-izvestaj` i `/antasline-konektor`. |
| 5.5 | ✅ PRVI PUT IZVRŠENO 2026-07-22 — Mesečni AI test (5 fiksnih promptova): 2/5 pominjanja (1 sa URL citatom na antasline.com), 2 sadržajna gap-a otkrivena (epoksid-conquest, Bergo terase) → [[analiza/2026-07-22-ai-test-baseline]]. Ponoviti sledeći mesec za trend · 🆕 **2026-08-12: test dobija Google parnjaka** — GSC **Generative AI performance report** je potvrđeno dostupan za našu property i kontrola *Search generative AI* stoji na „Include". Daje **samo prikaze po stranicama** (bez klikova/CTR/pozicije) i **nije u API-ju** → ručno očitavanje uz mesečni snapshot; ChatGPT test ostaje jer meri ne-Google asistente. ✅ **BASELINE SNIMLJEN 2026-08-12** (stavka A checkliste): ~17.000 prikaza / 112 stranica za 3 meseca ≈ **13%** od 129K Web prikaza (podskup, ne dodatak). **Dve stranice nose 54%** — basket 6.901 + pop-tenis 2.250; 🔴 `/sportske-podloge/kosarkaske-konstrukcije/` **196** (ista stranica = kritična rupa redirect mape). Ponoviti ~07.09 i uporediti po stranici. → [[analiza/2026-08-12-genai-baseline]] · v. [[seo/geo-ai-plan]] §0.1/§0.2 | CC | — |
| 5.6 | 🔄 2026-07-22 — `gallery_view`+`pdf_download` napravljeni u GTM UI (trigger+tag), DRAFT u Workspace-u, **NIJE Submit-ovano**. M odluka 2026-07-22: test se radi kad `staging.antasline.com` bude live (GTM Preview ne radi na localhost) — Submit čeka do tada | M+CC | #ceka-miroslav: staging live, pa GTM Preview test, pa Submit |
| 5.7 | Post-live: GA4 real-time verifikacija, GTM preview na produkciji, key eventi okidaju, Ads import radi | CC+CP | dan migracije |
| 5.8 | Konverzioni levak downstream: šta biva sa ~55 kontakata/mes (CRM/email follow-up?) | M | odgovor oblikuje Fazu 4 |
| 5.9 | 🆕 2026-08-04 — **GA4 → BigQuery export** (Daily export, besplatan tier 10GB/1TB upita mesečno). Korak 1 (M): GCP projekat + omogućen Cloud Billing nalog (platna kartica, i dalje besplatan tier posle) → GA4 Admin → BigQuery Linking. Korak 2 (CC): Python povlačenje preko `google-cloud-bigquery`, isti servisni nalog princip kao `antasline-konektor`, dodaje se kao novi izvor pored GA4/GSC/Ads/GMB. Koristi se za: tačnu audience membership veličinu (GA4 Data API je ne izlaže direktno), multi-touch put do leada (sekvenca evenata po `user_pseudo_id`), spajanje sa GSC/Ads podacima u jednom upitu umesto ručno u Python-u | M+CC | raw event-level podaci bez API sampling/threshold ograničenja |

---

## 2. VREMENSKI PLAN — 7 nedelja unazad od 2026-08-24

> 🔄 **Prepravljeno 2026-08-10** (go-live 31.08 → 24.08). N1–N5 su istorija i ostaju
> kako su odrađene. Menjaju se **samo poslednje dve nedelje**: stari N6/N7/N8 se
> sabijaju u **N6' i N7'**, buffer nedelja nestaje.

```
N1  07–13.07  W2: Tier1 (čim stignu cene) + 2.3 title/meta ×4 · W1: silo rebuild + 1.9 tel audit · W4: 4.1 negativne ✅ + 4.3 RSA · W3: 3.5 Lighthouse baseline + 🔴3.13 backup automation + 🔴3.14 SSH test
N2  14–20.07  W2: Tier2 (odbojka/tenis/šljaka/padel) · W4: 4.4 ad grupe · W1: blog import · W3: 3.4 Woo slugovi
N3  21–27.07  W1: preostale pages (top GSC prioritet) · W2: 2.7 Product schema + 2.8 GEO paket · W3: 3.3 blog slug
N4  28.07–03.08  W2: Tier3 vertikali · W3: 3.1–3.2 C1 finalna verifikacija + konstrukcije odluka · W5: 5.2–5.3 GMB paket
N5  04–10.08  W3: 3.6 CWV optimizacija · W1: footer/meni/mobile QA ✅ rani start 2026-07-29 · W3: 3.8 checkout test ✅
─────────── ↓ ODAVDE PREPRAVLJENO (2026-08-10) ↓ ───────────
N6' 11–16.08  POSLEDNJI SADRŽAJNI PROZOR. W3: **3.10 full regression** (glavno) · W4: 4.7 EC Ads UI toggle (M) · W5: nedeljni izveštaj + jul snapshot (kasne) · 🔴 gate: rollback plan zatvoriti do **15.08** (bilo „pre N7") · Tier4/nice-to-have samo ako regression prođe čisto
    NED 16.08  ⛔ CONTENT FREEZE počinje (bilo 18.08)
N7' 17–21.08  FREEZE. W3: 3.9 .htaccess finalna provera + 3.10 checklist do kraja + 🔴 svež live backup (cPanel) · GSC priprema · W4: 4.10 priprema URL audita
    PET 21.08  🚦 GATE PREGLED (sekcija 3) → GO/NO-GO. Rok za SVE M odluke.
    22–23.08   Vikend = jedina rezerva (bila cela N8 nedelja). Ništa se ne planira ovde — samo prelivanje ako gate padne.
→   PON 24.08  MIGRACIJA (1 dan) → post-live monitoring 25.08+ (3.12, 5.7, 4.10)
```

**Šta je izgubljeno pomeranjem:** cela N8 buffer nedelja (5 radnih dana). To je bila
rezerva za „gate padne u petak, popravljamo ponedeljak–sreda, migriramo naredni
ponedeljak". Sada je ta rezerva **2 dana vikenda**. Praktično: ako gate 21.08 padne
na nečemu što nije popravivo za vikend, migracija se pomera na **PON 31.08** —
tj. vraćamo se na originalni datum, ne guramo na silu (pravilo iz §3 ostaje).

**Kapacitet-realnost:** ~40 min–1h po C3 stranici, 30–90 min po rebuild stranici.
Sa jednom nedeljom manje, **seče se prvo:** Tier4 i svaki preostali nice-to-have
content → posle live-a; video objava (čeka YouTube handle ionako) → posle live-a;
W4 4.11/4.12 (Meta/LinkedIn, blokirani na M13/M14) → posle live-a.
**Ne seče se:** full regression, .htaccess/301, live backup, gate pregled, parity.

---

## 3. GATE KRITERIJUMI — go/no-go za migraciju (pregled **PET 21.08**, bilo N8)

> 🔴 **2026-08-10:** pomeranjem na 24.08 gate pregled se seli sa 25–30.08 na
> **petak 21.08**. Tri stavke su i dalje otvorene i sada imaju **11 dana**, ne 18:
> LCP (spoljno ograničenje — hosting, verovatno ostaje crveno i ide kao svestan
> rizik) · svež live backup na 2 lokacije (traži `[cpanel-live]` sesiju) ·
> rollback plan (3 pitanja čekaju M od 27.07 — **rok sada 15.08**, pre freeze-a).

- [x] ✅ (2026-07-21, reosveženo posle F1 baseline-a 2026-07-07) `parity-inventar.csv` kompletan (svaki live URL ima status) + minimalna redirect mapa (F4) potvrđena + .htaccess generisan i testiran na lokalu — v. [[migracija/PARITY-PLAN]] §2.1
- [ ] CWV lokal: 🔴 LCP <2,5s mobile (blokirano, čeka LiteSpeed na produkciji) · ✅ CLS <0,1 (2026-07-12) · ✅ INP/TBT proxy <200ms na home+kategorija (2026-07-22, proizvod stranice i dalje formalno crvene ali niska šteta — v. 3.6)
- [x] ✅ (reosveženo 2026-07-27) Sve Tier1 + Tier2 stranice žive na buildu — Tier1 (2.1) ✅, Tier2 #7/#8/#11 ✅, **#9 odbojka refresh potvrđeno GOTOVO na lokalu** (post 4318, Yoast title/meta+FAQ+cena provereno direktno u bazi 2026-07-27 — red u W2 tabeli §2.2 "samo na live" je bio zastareo, ispravljen), #10 piklbol namerno preskočen (M odluka, postojeća `/teren-za-pickleball/` već pokriva klaster). Tier3/4 nisu blokeri.
- [x] ✅ (reosveženo 2026-07-27) Content parity checklist — `parity-inventar.csv` (F1, 2026-07-21): 135 PARITY + 7 301-KANDIDAT (svi već imaju odluku u redirect-mapa-FINAL.csv) + 2 ARHIVA-STRANICA (sistemske, OK) + 29 LOKAL-NOVO (dodatne stranice, ne štete parity-ju). Poslednji preostali NEDOSTAJE-LOKAL red (`/industrijski-podovi-najcesca-pitanja/`, FAQ konsolidacija) **REŠEN 2026-07-27**: rekreiran lokalno (ID 17025) — ispalo je da je to najbolji-performans od 3 skoro-identične FAQ stranice (pos 6.9/CTR 4.92%), ne najslabiji kako je plan pretpostavljao; pravi duplikat (3274) draftovan + 301→2622 pripremljen. **Parity gate sada 100% zatvoren, 0 preostalih NEDOSTAJE-LOKAL redova.**
- [x] ✅ (2026-07-22) Forme rade + `/hvala-za-poruku/` okida `generate_lead` + GTM verifikovan na buildu — ali OVAJ gate je do 2026-07-22 bio lažno-zeleno-po-defaultu (GTM uopšte nije postojao lokalno pre W3 3.10 ranog starta, videti [[reference/naucene-lekcije]] "GTM UI konfiguracija ≠ embed"); sad stvarno verifikovano end-to-end (network requesti potvrđuju `generate_lead`+`page_view` na GA4 G-H8BRCZN8W4 + Ads AW-966742304 konverzija)
- [x] ✅ (2026-07-21, v. W3 3.8) Woo checkout — N/A u izvornom obliku (catalog_mode/M9 uklonio cart/checkout u potpunosti), pravi tok "Zatraži ponudu"→kontakt→hvala-za-poruku testiran end-to-end
- [x] ✅ **Svež backup live sajta (db + wp-content) na 2 lokacije — ZATVORENO `[cpanel-live]` 2026-08-11.** Ručan DB dump (17,3MB) + `wp-content` tar.gz (1,29GB), MD5 provereno, skinuto na `C:\Miroslav\Antas line\Backup` + `G:\AntasLine-Backups`, kopije obrisane sa servera. Backup finalnog **lokalnog builda** na 2 lokacije ostaje odvojeno pitanje (isti skript-ograničenje, nije dirano ove sesije) — v. napomenu ispod.
- [x] ✅ POPRAVLJENO 2026-07-27 — Automatski noćni backup builda: **nađen i popravljen pravi bag** — poslednji uspešan run bio 2026-07-22 (5 dana pauze), 07-27 su oba pokušaja (07:58, 08:04) pukla na `mysqldump exit code 2` jer XAMPP MySQL nije Windows servis i nije bio pokrenut u trenutku kad je task okinuo. Fix: `nocni-backup.ps1` sad proverava (`mysqladmin ping`) i sam pokreće MySQL headless pre dump-a ako ne radi (do 30s čekanja). Testirano uživo posle fixa — pun backup pokrenut (DB+wp-content, destinacija eksterni HDD "Maxtor", trenutno prikačen). Backup skripte: `.bak-2026-07-27` kopija sačuvana pre izmene. Detalji: [[DNEVNIK-NAPRETKA]]
- [x] ✅ **ROLLBACK PLAN ZATVOREN 2026-08-11** (pre roka 15.08) — sva 3 otvorena pitanja odgovorena. Draft napisan 2026-07-27 → [[migracija/rollback-plan]] (trigger uslovi, prereq backup checklist, koraci <1h budžet, ko odlučuje). ✅ WHM/auto-backup: **JetBackup 5, dnevni, off-site, 90 dana** (odgovor je postojao u M6 redu od 07-27, samo nije bio prenet u plan). ✅ CDN/edge keš: **ne postoji** — DNS ide direktno na `138.201.234.168` (Hetzner/oblak.host), zaglavlja daju `Server: LiteSpeed` + `X-LiteSpeed-Cache`, bez `cf-ray`/`via`/`age`/`x-qc-cache`; korak „očisti keš" se svodi na LSCWP Purge All. ✅ **Ko izvršava rollback — ODLUČENO 2026-08-11 (M): „migracija samo kad sam tu."** Nema rezervne osobe niti dogovora sa hostingom unapred; umesto toga **dostupnost Miroslava (~6h slobodnih) postaje uslov za pokretanje migracije 24.08** — ako tog dana nema tog prozora, migracija se pomera. Prihvaćen rizik: ako postane nedostupan usred incidenta, ostaje samo improvizovan poziv oblak.host podršci (SLA namerno neproveren). Detalji: [[migracija/rollback-plan]] §4 i §5
- [x] ✅ ZATVORENO 2026-07-21 — SSH/hosting pristup potvrđen (M) + proba migracije na subdomen izvedena (3.14, `staging.antasline.com`, M vizuelno potvrdio) — checkbox ovde bio zastareo, posao odavno gotov (v. §1 W3 3.14 + §4 M6)
- [x] ✅ ZATVORENO 2026-07-21 — SERP snapshot top 20 upita snimljen pre migracije (3.15) — checkbox ovde bio zastareo (v. §1 W3 3.15)
- [ ] 🆕 2026-08-05 — **GTM Meta Pixel Manual Advanced Matching prepravka na dan migracije** — 🟢 **POJEDNOSTAVLJENO 2026-08-09 (W4 4.7)**: lokalni build sada sam piše `al_am_em`/`al_am_ph` u `sessionStorage` iz CF7 `wpcf7mailsent` handler-a, pa selektore više ne treba prepisivati. Na dan migracije samo **obrisati** tag `Meta Pixel - Capture Lead Data` i trigger `Klik na Posalji (Zion forma)` (oba vezana za mrtvu Zion formu); `Meta Pixel - Base Code` ostaje **nepromenjen** — čita iste ključeve. Usput ispravljeno: stari tag je slao telefon bez pozivnog broja (`0692340072`), lokalni kod šalje `381692340072` → bolji Event Match Quality. Isti korak nosi i GTM deo Enhanced Conversions-a → izvršiti oba po [[migracija/2026-08-09-enhanced-conversions-4.7]]. Poreklo stavke: [[DNEVNIK-NAPRETKA]] 2026-08-05 "Meta Pixel Advanced Matching"

**Bilo koji gate crven → migracija se pomera za sledeći ponedeljak, ne gura se na silu.**

**Reosveženo 2026-08-11 (uveče, `[cpanel-live]` sesija)**: od 11 gate stavki, stvarno crveno/otvoreno ostaje samo **1**: 🔴 **LCP** (blokirano na produkciju, poznato — CCSS radi, UCSS oživeo 2026-08-11 posle 11 dana tišine, ali numerička <2,5s potvrda i dalje nedostaje). Preostale 2 zatvorene isti dan: ✅ live backup+2-lokacije (ručan DB+wp-content backup uživo, v. Urađeno tabela) · ✅ rollback plan (zatvoren ranije 2026-08-11, sva 3 pitanja odgovorena). Ostalih 8 je ili već bilo gotovo (samo neštriklovano — W1/W2/W3 rad odavno zatvoren) ili popravljeno u međuvremenu.

---

## 4. ZAVISNOSTI — šta čeka Miroslava (sa fallback-om)

> 🔴 **2026-08-10 — SVI ROKOVI POMERENI NEDELJU RANIJE.** Svako „pre 31.08" u
> ovom planu i u [[PROGRESS]] sada znači **pre PET 21.08** (gate pregled). Odluke
> koje utiču na sadržaj sajta moraju stići **pre NED 16.08** (content freeze).
>
> **Kritičan raspored M odluka posle pomeranja:**
>
> | Do kada | Šta | Zašto taj rok |
> |---|---|---|
> | ~~15.08~~ ✅ | Rollback plan — **sva 3 pitanja zatvorena 2026-08-11, 4 dana pre roka** (JetBackup dnevni/off-site · nema CDN sloja · „migracija samo kad je M tu") | ~~gate stavka~~ ispunjeno |
> | **16.08** | Sve što menja sadržaj: trava-u-boji poreklo, F2.8 mapiranje trave, 14 fotki proizvoda, meni 67 brisanje, P3 metadesc | posle ovog datuma build je zamrznut |
> | **16.08** | Gemini žig kadar 5 + tablica kombija · YouTube handle | ako ne stigne → video objava ide **posle** live-a (nije blokator migracije) |
> | **21.08** | Enhanced Conversions Ads UI toggle · ECOTILE budžet · live kontakt-forma fix odobrenje | poslednji dan pre migracije |
> | **odmah ili nikad** | 4.8 Maximize Conversions — v. napomena ispod | Smart Bidding uči ~14 dana |
>
> ⚠️ **4.8 je pomeranjem postao odluka sa rokom „danas".** Smart Bidding učenje traje
> ~2 nedelje. Uključeno danas (10.08) → period učenja se završava **tačno na dan
> migracije**, kad se menjaju URL-ovi oglasa — najgori mogući preklop. **Preporuka:
> odložiti 4.8 na posle live-a** (npr. 01.09, kad se 301 slegnu i konverzije se
> vrate na normalu), a ne uključivati sada. Ako se ipak uključuje sada, mora se
> računati sa dvostrukim šumom u brojkama krajem avgusta.

| # | Odluka/input | Blokira | Rok | Fallback ako kasni |
|---|---|---|---|---|
| M1 | 🔴 Cene za Tier1 draftove (#1, #2, #3, #6) | W2 najvredniji deo | 2026-07-10 | objaviti sa "cena na upit" + forma, cene se dodaju naknadno |
| M2 | ✅ ~~Negativna lista potvrda u Ads UI~~ — urađeno 2026-07-06 | ~~16% budžeta curi svaki dan~~ | — | — |
| M3 | ✅ ZATVORENO 2026-07-11 — primena gotova od 07-05, Rich Results/schema provera zatvorena `[cpanel-live]` 07-11; ostaje samo cena-sekcija (čeka M10) i GSC indexing zahtev | ~~7.817 impr. quick-win~~ | — | — |
| M4 | ✅ URAĐENO 2026-07-06 (3/4) — GMB: recenzije kampanja (spreman, čeka poslove), UTM fix (gotovo), kategorije (gotovo), post (gotovo) | W5 quick-win | 2026-07-31 | review link čeka — ne blocker |
| M5 | 🟡 DELIMIČNO ZATVORENO 2026-07-30 `[cpanel-live]` — mehanizam dostave razjašnjen (sve 49 formi → `office@antasline.com`, SPF/DKIM ok), ALI stopa odgovora se ne može meriti sa cPanel strane: mailbox ima samo ~2 nedelje istorije (11 lead-mejlova 16-30.07 od 93 hvala-proxy kumulativ), Sent/Archive/Trash prazni za sva vremena (verovatno POP "delete from server" na Miroslavljevom uređaju). Detalji: [[DNEVNIK-NAPRETKA]] 2026-07-30 | Fazu 4 / CRM odluku | čeka proveru POP podešavanja | plan radi i bez toga, ali vrednost/konv ostaje nepoznata |
| M6 | ✅ POTPUNO ZATVORENO 2026-07-27 — cPanel/SSH pristup potvrđen, subdomen `staging.antasline.com` kreiran, **proba migracije izvedena i M vizuelno potvrdio regresiju** — postupak validiran kao template za 2026-08-31 → [[DNEVNIK-NAPRETKA]]. **65 Redirection pravila analizirana i razrešena** (62 jedinstvena, lanci spljošteni, 2 mrtva cilja ispravljena, sve verifikovano 200) → [[migracija/2026-07-21-analiza-65-redirection-pravila]], samo 1 sitna otvorena odluka (padel-tenis sukob, ima automatski fallback). **Backup raspored na produkciji potvrđen 2026-07-27**: JetBackup 5 (cPanel-dostupan, ne WHM-only), dnevni backup, remote/off-site lokacija kod provajdera, 90 dana retencije — M proverio direktno u cPanel-u | migraciju (N8) | 2026-08-20 | — |
| M7 | ✅ ZATVORENO 2026-07-22 — Figma link dat, testimonials sadržaj sa GMB umesto čekanja na M copy | ~~W1 poliranje~~ | — | — |
| M8 | ✅ ODLUČENO 2026-07-07 — **pun reimport svih 30 postova sa live** (lokalni "stari stil" se briše, restyle posle) | ~~blog import~~ | — | — |
| M9 | ✅ ODLUČENO 2026-07-07 — **katalog režim** ("Zatraži ponudu" umesto korpe/cene) | W1 zadatak 1.8, W3 zadatak 3.8 | — | — |
| M10 | ✅ ZATVORENO 2026-07-29 — `[[reference/cenovnik]]` popunjen; provereno da su Tier1 W2 stranice (16873/16874/16875/16876) već vukle iste brojke iz WC-a ranije (podudaranje 1:1, ništa nije trebalo menjati na strankama) | ~~W2 Tier1 (M1) + obogaćivanje proizvoda~~ | — | — |
| M11 | ✅ DELIMIČNO ZATVORENO 2026-07-29 — Ecotile rampe cena (1560) primenjena u `al_cb_prices` (`ramp`/`ramp_corner` = 1300 bez PDV + 20%); Bergo Ultimate/FLOW **ostaju "na upit" kao M-ova konačna odluka** (potvrđeno 2x u cenovniku), `tile:16770`/`tile:16801` namerno prazni — Court builder i dalje prikazuje "na upit" samo za taj deo predračuna | ništa ne blokira — samo kvalitet outputa 1.12 | pre live-a | PDF ostaje delimično "na upit" za tile cenu dok M ne odluči da li ide fiksna m² cena ili ostaje projektna |
| M12 | 🆕 2026-07-11 — Brendovi/dobavljači za tribine, stolice, golove, mreže (pregovori u toku) | ništa — proizvodi se prave generički (1.11 S8) | kad se pregovori završe | ostaju generički "na upit"; dopuna brendom naknadno |
| M13 | 🆕 2026-08-04 — Pristup Meta Business Manager nalogu (M proverava da li već postoji, još nema pristup) — potreban za Pixel ID pre GTM ožičavanja (4.11 Faza A) | W4 4.11 | otvoreno | čeka — Faza A ne može početi bez Pixel ID-a |
| M14 | 🆕 2026-08-04 — Pristup LinkedIn Campaign Manager nalogu (postoji li već, ko ima pristup) — potreban za Insight Tag ID pre GTM ožičavanja (4.12). Spec spreman za izvršenje čim ID stigne: [[reference/linkedin-insight-tag-prep]] | W4 4.12 | otvoreno | čeka — 4.12 ne može početi bez Insight Tag ID-a |
| M15 | 🆕 2026-08-04 — GCP projekat + omogućen Cloud Billing nalog (platna kartica) za GA4 BigQuery export (5.9) — export sam ostaje besplatan tier, ali GCP traži povezanu karticu da se uključi | W5 5.9 | otvoreno | čeka — 5.9 korak 2 (Python povlačenje) ne može početi bez linkovanja |

---

## 5. KPI TABLA (jun 2026 = mesec-nula)

> 🔴 **2026-08-11 — DVA REDA OVE TABLE MERE POGREŠNU STVAR** (nalaz mesečnog
> snapshot-a, [[analiza/2026-08-11-snapshot-jul]]). Ispravka čeka M odluku, pa su
> originalni redovi ostavljeni netaknuti sa korekcijom ispod svakog:
> - **Prave konverzije** — „55/mes (jun)" su **pregledi** `/hvala-za-poruku/`, ne
>   lidovi. Stvarno: jun **24 sesije** · jul **16** · avg 1–10 **11** (kumulativ
>   01.06–10.08: 119 pregleda = **51 sesija**). Predlog nove skale: baseline **24**,
>   cilj do live-a **≥25/mes**, +60d **35+/mes**. Posle migracije brojka pada još
>   jednom (dupli `page_view` tag id 18) — to nije pad konverzija.
> - **Plaćene konverzije** — „kumulativ 26" je **17 tel-klikova + 9 forma-lidova**;
>   akcija `Klik na telefon (web)` je greškom u „Conversions" koloni (v. §1 W4 4.8
>   i [[PROGRESS]] Blokeri). Pravi brojač za prag 20–30 je **9**.

| KPI | Baseline (jun) | Cilj do live-a | Cilj +60d posle live-a |
|---|---|---|---|
| Prave konverzije (hvala-proxy) | ~~55/mes~~ **24 sesije/mes** | ~~održati ≥55~~ **≥25** | ~~70+/mes~~ **35+/mes** |
| Plaćene konverzije (kumulativno od juna) | 6 | 15–20 | **20–30 → Smart Bidding** — 🔴 trenutno **9 pravih** (ne 26) |
| GSC klikovi/mes | 2.104 (−19% YoY) | zaustaviti pad | +500–700 kl./90d (title/meta) + klaster rast |
| Mobilni LCP | ~7,3s lokal | **<2,5s** | <2,5s field data |
| Ads curenje | ~16% | **<5%** | <3% |
| Terase impression share | 24% | — | 40%+ (posle Faza 1–2) |
| GMB recenzije / pozivi | 6 / ~1 mes | 12+ / merljivo (UTM fix) | 20+ / 5+ mes |
| AI Assistant kanal | 9 korisnika/90d | — | rast (mesečni AI test) |

**Podsetnik:** pad merenih brojeva posle tracking čišćenja = tačnije merenje, ne pad performansi — ne reagovati budžetom. Promene <5% = stabilno stanje.

---

## 6. RIZICI I MITIGACIJE

| Rizik | Verovatnoća | Mitigacija |
|---|---|---|
| Gubitak rankinga posle migracije | srednja | 301 mapa 100% + content parity checklist + title/meta parity po stranici + GSC monitoring prvih 14 dana |
| LiteSpeed/QUIC.cloud red se ponovo zaglavi | srednja | provera ~2026-07-08; ako da → QUIC.cloud podrška (poznat simptom "Too many requested images") |
| Slug kolizije pri migraciji | niska | pravilo: postojeći slug → sufiks 5; attachment slug gotcha dokumentovan |
| Kanibalizacija novih stranica | niska | parovi dokumentovani (245↔246, 251↔252, 254↔16567, 2298↔dimenzije) — obrasci diferencijacije postoje |
| Ads konverzije padnu posle URL promena | srednja | 4.10 final URL audit na dan migracije + 301 hvata zaostale |
| Miroslavljeve odluke kasne | srednja | fallback po stavci (sekcija 4); samo M6 (SSH) nema fallback |
| WPBakery/WoodMart tehnički bug usred rebuilda | niska | backup pre svake sesije + 10 gotcha pravila u [[migracija/woodmart-sabloni]] |
| GSC sezonski pad maskira efekat rada | visoka | poređenje uvek YoY + pozicije, ne samo klikovi (špic je mar–maj) |
| 🔴 Gubitak 2 meseca rada (disk otkaz, bez druge kopije) | niska ali egzistencijalna | 3.13 automatski backup na drugu lokaciju |
| 🔴 SSH/hosting pristup ne radi kad dođe N8 | nepoznata (netestirano) | 3.14 test OVE nedelje + proba migracije na subdomen u N6 — pretvara pretpostavku u izmerenu činjenicu |
| Post-migracija pad rangiranja se ne može objasniti (naš bug vs konkurent) | niska | 3.15 SERP snapshot pre migracije |

---

## 7. RITAM RADA

- **Ponedeljak (prvih 15 min sesije):** pregled sekcije 4 (zavisnosti) — rok, status, fallback po stavci; ono što kasni se eskalira odmah, ne čeka N8 iznenađenje. Ugrađeno u skill `/antasline-sesija` otvaranje.
- **Po sesiji:** jedan glavni zadatak iz jedne trake · backup pre destruktivnog · verifikacija (200, 1×H1, JSON-LD, linkovi) · unos u [[DNEVNIK-NAPRETKA]] + update [[PROGRESS]] + štiklirati ovde/u [[seo/plan-novih-stranica]]
- **Nedeljno:** mini-izveštaj 7d vs 7d (format [[CLAUDE]] §10) + pregled tempa vs sekcija 2
- **Mesečno:** puni snapshot ([[analiza/_TEMPLATE-snapshot]]) + AI test + KPI tabla update
- **PET 21.08:** gate pregled (sekcija 3) → GO/NO-GO odluka sa Miroslavom (bilo N8)

---

## 8. W6/W7 — POSLE LIVE-A (2026-08-25+, bilo 09-02)

Detaljan social/email tok: skill `/w6-social`. Ovde samo ono što se planira
UNAPRED da se ne dočeka nespremno.

### W6 — Social / Email (fazni detalj u skill-u `/w6-social`)
- Faza 0 (pre live-a, jeftino): popis profila ✅ 2026-07-07 → [[reference/drustvene-mreze]] · M5 (kontakti) · GMB paket ✅ · saglasnost checkbox na formi
- **Ključni nalaz popisa:** FB+IG su aktivni (objave na ~7 dana) ali bez UTM/CTA — problem je merenje, ne prisustvo. Ožičavanje (UTM, link-in-bio) ide ODMAH, ne čeka septembar.
- Puni ritam (IG/FB 2×/ned, LinkedIn 1×/ned, YouTube 1×/mes) od 2026-09-01

### BLOK D — AI chat za posetioce (odlučeno 2026-07-22, gradi se u W7)
Customer-facing chatbot (Q&A + lead-kvalifikacija), RAG nad katalogom/FAQ, timing posle
live-a. Detalji + tvrda pravila (nikad ne izmišljati cenu/epoksid conquest): [[blokovi/BLOK-D-ai-chat]].

### BLOK E — AI orkestracija (Gemini foto/video + DeepSeek/CCR ruter, odlučeno 2026-08-04)
Gemini preuzima foto/video rad (unapređenje postojećih proizvod fotografija,
nove/slične varijante, video za sajt/oglase/social), Claude vodi prioritetni
red i prati free kvotu (~500 slika/dan). DeepSeek eksperiment za kodiranje
preko `claude-code-router` (CCR), opt-in po sesiji. Dvoslojna arhitektura
(generički `~/.claude/skills/ai-vizuali/` + projektni
`.claude/skills/gemini-vizuali/`) namerno gradi ovo tako da bude ponovo
upotrebljivo na budućim projektima. **Aktivno odmah** (foto rad na
postojećem katalogu ne čeka live) — čeka samo Miroslavljev Gemini API ključ.
Detalji: [[blokovi/BLOK-E-ai-orkestracija]].

### W7 — Sezonski kalendar (veže W2/W4/W6, sprečava da 2027 GSC špic zatekne nespremne)
| Period | Fokus | Zašto |
|---|---|---|
| Sep–Nov 2026 | B2B sezona: industrijski/ESD sadržaj + Ads (sanacije hala van proizvodne sezone) | GSC špic za terase je mar–maj, van-sezone B2B ima manje konkurencije |
| Dec 2026–Jan 2027 | Priprema terase kampanje: content, email lista, Meta Ads kreativa spremni PRE špica | Ako se čeka do marta, propušten je najjači mesec |
| Feb 2027 | Sezonski newsletter "spremite terasu" (već u `/w6-social` Fazi 2) + budžet gore na Terase kampanju | GSC špic mar–maj |
| Kroz celu godinu | Mesečni AI test (5.5) + puni snapshot + KPI tabla ažuriranje | rani signal da li GEO/schema rad deluje |

### Post-live monitoring (prvih 14 dana, uz 3.12/5.7)
- UptimeRobot (besplatan) — alert ako sajt padne
- Dnevni pregled 404 loga (ne ad-hoc) — hvata zaostale redirect rupe brzo
- GSC email alerti (crawl errors, security issues)

## Veze
[[PROGRESS]] · [[DNEVNIK-NAPRETKA]] · [[blokovi/BLOK-C-sledece]] · [[blokovi/BLOK-E-ai-orkestracija]] · [[seo/plan-novih-stranica]] · [[seo/geo-ai-plan]] · [[dnevnik/ADS-DNEVNIK]] · [[analiza/2026-07-04-snapshot-full]] · [[migracija/woodmart-sabloni]] · [[odluke/_pregled-odluka]]
