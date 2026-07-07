---
tip: plan
naziv: MASTER PLAN V2 — redizajn + SEO + Ads + tracking do live-a
datum-plan: 2026-07-06 do 2026-09-02
prioritet: Tehnička → SEO → Ads
go-live: 2026-08-31
status: aktivan
zamenjuje: "[[2026-07-02-MASTER-PLAN-DO-LIVE]]"
azurirano: 2026-07-06
---

# 🚀 MASTER PLAN V2 — do live-a 2026-08-31

**Jedini izvor istine za plan projekta.** Zamenjuje [[2026-07-02-MASTER-PLAN-DO-LIVE]] (pisan pre Porto→WoodMart prelaska). Dnevno stanje: [[PROGRESS]] · istorija: [[DNEVNIK-NAPRETKA]].

**Cilj:** redizajnirani sajt live 2026-08-31, bez gubitka organskog saobraćaja, sa Ads nalogom spremnim za Smart Bidding i čistim merenjem (GA4/GSC/Ads). Budžet Ads: 40k RSD/mes (ne diže se dok se ne optimizuje).

**Pravila koja važe kroz ceo plan** ([[CLAUDE]]): lokalni build = staging, live se ne dira (osim eksplicitnih `[cpanel-live]` zadataka) · Yoast ostaje (ne RankMath) · epoksid samo conquest · Maximize Clicks do praga 20–30 plaćenih konverzija · jun 2026 = mesec-nula za sve serije konverzija.

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
| Migracija priprema | Redirect mapa 106/118 · live export (posts XML, pages XML, inventar CSV) · porto backtick sanacija (7 stranica) |
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
| 1.2 | Preostale pages iz live inventara (~50) — po prioritetu GSC klikova | CC | prioritet: kosarkaske-konstrukcije, vestacka-trava, bergo-xl |
| 1.3 | Blog import (live-posts XML → WP import sa slikama) | CC | odluka overwrite/preserve za ručno rebuildovane #ceka-miroslav |
| 1.4 | Footer builder (WoodMart HTML block) + bela varijanta logoa | CC | otvoreno iz [[migracija/woodmart-sabloni]] |
| 1.5 | Meni: proširiti novim silo/C3 stranicama | CC | posle 1.1–1.2 |
| 1.6 | Mobile viewport vizuelni QA (sve rebuild stranice) | CC | media queries napisani, nije snimljeno |
| 1.7 | Figma sync (testimonials, "Najprodavanije 2025") | CC+M | čeka Figma link + GMB recenzije #ceka-miroslav |
| 1.8 | Woo katalog režim: "Dodaj u korpu" → "Zatraži ponudu" (pre-popunjena forma → `/hvala-za-poruku/`, postojeći tracking hvata sve) — manje frikcije za B2B, manje Woo skripti (LCP). ✅ Odluka M9 doneta 2026-07-07 — spremno za implementaciju | CC | uklapa se sa `/obogati-proizvod` (cena "na upit" polje) |
| 1.9 | Audit `tel:` linkova na celom buildu (SQL grep) — više brojeva u opticaju (063 stari live, 069/072 CTA standard, 074); ujednačiti na jedan primarni | CC | quick win, 30 min |

### W2 — SEO CONTENT (C3 + GEO)
Master lista: [[seo/plan-novih-stranica]] (20 stranica, 4 tijera). Pravila po stranici: Yoast >80, FAQ + FAQPage/Product schema, cena od–do gde ima smisla, CTA 072 + forma, interni link ka `/industrijski-podovi/`, prvi pasus = direktan odgovor (GEO pravilo).

| # | Zadatak | Vlasnik | Zavisi od |
|---|---|---|---|
| 2.1 | Tier1 implementacija: #1 terase-cena, #2 industrijski-cena, #3 garaže, #6 parking-cena (draftovi gotovi ✍️) | CC | 🔴 **cene od M** |
| 2.2 | Odbojka refresh (#9) — paket spakovan | CP | stranica samo na live → [cpanel-live] #ceka-miroslav |
| 2.3 | Title/meta prepis 4 stranice: /pop-tenis/, /podloga-za-odbojkaske-terene/, /spoljnje-podne-obloge/, conquest 2542 | CC | ništa — +500–700 kl./90d, najbrži ROI |
| 2.4 | Tier2: šljaka hub (#7), tenis dimenzije (#8), piklbol (#10), padel refresh (#11) | CC | — |
| 2.5 | Tier3: vertikali (kancelarije #12 → restorani #13 → industrija #14 → radnje #15 → zdravstvo #16 → štamparije #17) | CC | — |
| 2.6 | Tier4: reference stranice (#18), /bergo/ brend (#19), teretane (#20) | CC | — |
| 2.7 | Product schema na sve money stranice (Bergo, Ecotile, konstrukcije) | CC | Product snippet CTR 10,5% vs prosek 5,5% |
| 2.8 | GEO paket ([[seo/geo-ai-plan]]): `llms.txt` + Organization/LocalBusiness JSON-LD sitewide + "O nama" činjenice | CC | — |
| 2.9 | Conquest članak 2542 refresh (poz. 26 za "epoksi podovi" → SEO+GEO) | CC | — |

### W3 — SEO TEHNIČKA + MIGRACIJA (C1/C2 + CWV)
| # | Zadatak | Vlasnik | Napomena |
|---|---|---|---|
| 3.1 | C1: 20 PROVERI redova redirect mape — ručna verifikacija | CC | blokira go-live |
| 3.2 | C1: rešiti `/sportske-podloge/kosarkaske-konstrukcije/` (478 GSC kl.!) — kandidat target: kategorija 251 ili nova landing | CC+M | odluka target-a |
| 3.3 | C1: `/aktuelnosti/` → `/blog/` ispravka u CSV + slug rename na buildu | CC | — |
| 3.4 | C1: 49 Woo auto-predloga validirati protiv stvarnih lokalnih slugova | CC | proizvodi su sada na lokalu — može odmah |
| 3.5 | Lighthouse/PageSpeed baseline audit lokala → `dnevnik/PERFORMANCE-AUDIT.md` | CC | pre optimizacije |
| 3.6 | CWV optimizacija: LCP <2,5s mobile (slike, lazy load, skripte, fontovi ✓ već lokalni), CLS <0,1, INP <200ms | CC | najveća poluga konverzija (mobile = 3/4 svega) |
| 3.7 | XML sitemap + robots.txt (+ AI crawleri: GPTBot, PerplexityBot, ClaudeBot…) + llms.txt | CC | robots na live = [cpanel-live] odluka #ceka-miroslav |
| 3.8 | Woo checkout + product page test na lokalu | CC | — |
| 3.9 | `.htaccess` 301 fajl generisan iz finalne CSV mape | CC | aktivira se TEK na dan migracije |
| 3.10 | Pre-migration checklist + full regression (forme, GTM, linkovi, slike) | CC | N7 |
| 3.11 | **MIGRACIJA 2026-08-31** (1 dan): backup live → db+wp-content prebacivanje → URL zamena → 301 aktivacija → verifikacija | CC+M | SSH/hosting info #ceka-miroslav |
| 3.12 | Post-live (do 2026-09-02+): GSC sitemap resubmit, crawl errors, GA4/GTM verify, CWV field data + UptimeRobot (besplatan) + dnevni 404 log pregled prvih 14 dana (umesto ad-hoc) | CC+CP | — |
| 3.13 | ✅ ZATVORENO 2026-07-07 — `nocni-backup.ps1` (mysqldump + zip wp-content, rotacija 14) registrovan u Task Scheduler-u, radi svako veče u 03:00. Test uspešan: 90MB DB + 3,6GB wp-content → 3GB zip, 27 min. Trenutna destinacija: LOKALNO (`antasline-backups\auto\`) — OneDrive nije ulogovan, skripta automatski prebacuje na OneDrive čim se M prijavi (proverava svako pokretanje) | CC | ⏳ #ceka-miroslav: prijava na OneDrive za pravu cloud kopiju |
| 3.14 | ⏳ U TOKU — popis panela (M čita cPanel): **PHP 8.3** (⚠️ lokal je 8.2.12 — proveriti kompatibilnost pre migracije), disk 5,05/11,95GB (42%, 6,9GB slobodno — dovoljno za subdomen probu), subdomeni dostupni (0 iskorišćeno). Sledeći korak (SSH/upload/import na `novi.antasline.com`) nastavlja se sutra — čeka odluku načina rada (M izvršava uz vođstvo / SSH pristup / cPanel File Manager) | CC+M | jedina zavisnost bez fallbacka |
| 3.15 | SERP snapshot: top 20 GSC upita → pozicije/konkurencija danas, pre migracije → `analiza/` | CC | bez ovoga ne znamo da li post-migracija pad je naš (301) ili konkurent skočio |

### W4 — ADS
Fazni plan i RSA banka: [[dnevnik/ADS-DNEVNIK]]. Strategija ostaje **Maximize Clicks** do 20–30 plaćenih konverzija.

| # | Zadatak | Vlasnik | Efekat |
|---|---|---|---|
| 4.1 | ✅ ZATVORENO 2026-07-06 — lista potvrđena na obe kampanje, +13 novih negativnih (epoksi+padeži, betonski, "industrijski beton", [podne obloge], teraco, letvice, pevex, "uradi sam", pločice-phrase), KW "bastenski namestaj"/"oprema za bazene" pauzirani; `laminat` svesno izostavljen (watch) | M (15 min) | −16% curenja odmah |
| 4.2 | Potvrda: ECOTILE prikazi/CPC vraćeni na normalu posle odblokiranja | CC (Windsor) | zatvara Fazu 0 |
| 4.3 | Faza 1: RSA Terase (15 headlines + 4 desc iz banke) → Ad Strength ≥ Good | M+CC | QS ↑ |
| 4.4 | Faza 2: ad grupe — Terase → terase/bazeni/bergo · ECOTILE → industrijski/esd; svaka grupa svoj RSA; "pvc podovi za bazene" → svoja grupa + landing /podovi-za-bazene/ | M+CC | IS 24%→40%+ isti budžet |
| 4.5 | Skalirati ECOTILE phrase "industrijski podovi" (1.073 RSD/konv ⭐) + geo BG/NS/Niš | M | najjeftinije B2B konverzije |
| 4.6 | Pravilo overlap-a: NE plaćati gde je organik top 3 (terase-cena poz. 1,4), plaćati gde je organik >poz. 5 (industrijski poz. 11) | CC (analiza) | — |
| 4.7 | Faza 3: Enhanced Conversions (SHA-256 email/tel kroz GTM) — priprema na lokalu, aktivacija posle migracije | CC | preciznija atribucija |
| 4.8 | Na 20–30 plaćenih konverzija → Maximize Conversions; broad tek tada | M | — |
| 4.9 | Faza 4: call asset 072, mobilni bid +15–20%, publike u Observation, Customer Match (email-ovi iz upita) | M+CC | zaobilazi prag publika |
| 4.10 | Na dan migracije: final URL audit svih oglasa (novi slugovi!) | CC+M | čuva QS posle migracije |

### W5 — TRACKING / MERENJE (GA4 + GSC + GMB)
| # | Zadatak | Vlasnik | Napomena |
|---|---|---|---|
| 5.1 | Potvrditi GA4 key events popravku na julskim podacima (`conversions` treba nazad na ~60–160) | CC (Windsor) | sredina jula |
| 5.2 | ✅ URAĐENO 2026-07-06 — GMB: UTM fix (`utm_source=google&utm_medium=gmb&utm_campaign=local`), kategorije proširene (+Gradnja sportskih terena, +Pružalac usluga), prvi post kreiran (Bergo/Naxos kampanja); review link spreman za korisnike | M | Efekat: GMB saobraćaj merljiv u GA4, prvi post live, review kampanja čeka poslove |
| 5.3 | GMB recenzije 6 → 20+ (email posle svakog posla) | M | trust + local pack + GEO |
| 5.4 | Nedeljni mini-izveštaj (7d vs 7d po formatu [[CLAUDE]] §10) + mesečni puni snapshot ([[analiza/_TEMPLATE-snapshot]]) | CC | sledeći puni: početak avgusta |
| 5.5 | Mesečni AI test (5 fiksnih promptova iz [[seo/geo-ai-plan]]) + praćenje "AI Assistant" kanala (baseline 9/90d) | CC | — |
| 5.6 | Planirani eventi po potrebi: `gallery_view`, `pdf_download` (GTM UI, ne JSON import!) | M+CC | tek kad sadržaj postoji |
| 5.7 | Post-live: GA4 real-time verifikacija, GTM preview na produkciji, key eventi okidaju, Ads import radi | CC+CP | dan migracije |
| 5.8 | Konverzioni levak downstream: šta biva sa ~55 kontakata/mes (CRM/email follow-up?) | M | odgovor oblikuje Fazu 4 |

---

## 2. VREMENSKI PLAN — 8 nedelja unazad od 2026-08-31

```
N1  07–13.07  W2: Tier1 (čim stignu cene) + 2.3 title/meta ×4 · W1: silo rebuild + 1.9 tel audit · W4: 4.1 negativne ✅ + 4.3 RSA · W3: 3.5 Lighthouse baseline + 🔴3.13 backup automation + 🔴3.14 SSH test
N2  14–20.07  W2: Tier2 (odbojka/tenis/šljaka/padel) · W4: 4.4 ad grupe · W1: blog import · W3: 3.4 Woo slugovi
N3  21–27.07  W1: preostale pages (top GSC prioritet) · W2: 2.7 Product schema + 2.8 GEO paket · W3: 3.3 blog slug
N4  28.07–03.08  W2: Tier3 vertikali · W3: 3.1–3.2 C1 finalna verifikacija + konstrukcije odluka · W5: 5.2–5.3 GMB paket
N5  04–10.08  W3: 3.6 CWV optimizacija · W1: footer/meni/mobile QA · W3: 3.8 checkout test
N6  11–17.08  W2: Tier4 · W4: 4.7 Enhanced Conversions priprema · W3: full regression start + 3.14 proba migracije na subdomen + 3.15 SERP snapshot
N7  18–24.08  CONTENT FREEZE · W3: 3.9 .htaccess + 3.10 checklist + backupi · GSC priprema
N8  25–30.08  Buffer + zamrzavanje builda · GATE PREGLED (sekcija 3)
→   PON 31.08  MIGRACIJA (1 dan) → post-live monitoring 01–02.09+ (3.12, 5.7, 4.10)
```

**Kapacitet-realnost:** ~40 min–1h po C3 stranici, 30–90 min po rebuild stranici. Ako N-tempo padne, seče se: Tier4 → posle live-a, Tier3 delimično → posle live-a. **Ne seče se:** C1 verifikacija, CWV, Tier1, parity, regression.

---

## 3. GATE KRITERIJUMI — go/no-go za migraciju (pregled u N8)

- [ ] Redirect mapa 118/118 verifikovana, .htaccess generisan i testiran na lokalu
- [ ] CWV lokal: LCP <2,5s mobile · CLS <0,1 · INP <200ms
- [ ] Sve Tier1 + Tier2 stranice žive na buildu (Tier3/4 nisu blokeri)
- [ ] Content parity checklist prošao: svaka live stranica ima parnjaka ili 301 (inventar CSV = checklista)
- [ ] Forme rade + `/hvala-za-poruku/` okida `generate_lead` + GTM verifikovan na buildu
- [ ] Woo checkout testiran
- [ ] Svež backup live sajta (db + wp-content) + backup finalnog builda na 2 lokacije
- [ ] Automatski noćni backup builda radi i testiran (3.13)
- [ ] Rollback plan: live backup se vraća u <1h ako nešto pukne
- [ ] SSH/hosting pristup potvrđen (M) + proba migracije na subdomen izvedena (3.14)
- [ ] SERP snapshot top 20 upita snimljen pre migracije (3.15)

**Bilo koji gate crven → migracija se pomera za sledeći ponedeljak, ne gura se na silu.**

---

## 4. ZAVISNOSTI — šta čeka Miroslava (sa fallback-om)

| # | Odluka/input | Blokira | Rok | Fallback ako kasni |
|---|---|---|---|---|
| M1 | 🔴 Cene za Tier1 draftove (#1, #2, #3, #6) | W2 najvredniji deo | 2026-07-10 | objaviti sa "cena na upit" + forma, cene se dodaju naknadno |
| M2 | ✅ ~~Negativna lista potvrda u Ads UI~~ — urađeno 2026-07-06 | ~~16% budžeta curi svaki dan~~ | — | — |
| M3 | Odbojka refresh primena `[cpanel-live]` | 7.817 impr. quick-win | 2026-07-13 | ostaje u paketu, primeni se sa migracijom |
| M4 | ✅ URAĐENO 2026-07-06 (3/4) — GMB: recenzije kampanja (spreman, čeka poslove), UTM fix (gotovo), kategorije (gotovo), post (gotovo) | W5 quick-win | 2026-07-31 | review link čeka — ne blocker |
| M5 | Konverzije info: šta biva sa 55 kontakata/mes | Fazu 4 / CRM odluku | 2026-07-31 | plan radi i bez toga, ali vrednost/konv ostaje nepoznata |
| M6 | SSH/hosting pristup + potvrda hostinga | migraciju (N8) | 2026-08-20 | 🔴 nema fallback — bez ovoga nema migracije |
| M7 | Figma link + testimonials copy | W1 poliranje | 2026-08-10 | sekcije ostaju izostavljene, nisu gate |
| M8 | Odluka overwrite/preserve rebuild postova (C2) | blog import | 2026-07-15 | import samo postova koji ne postoje lokalno |
| M9 | ✅ ODLUČENO 2026-07-07 — **katalog režim** ("Zatraži ponudu" umesto korpe/cene) | W1 zadatak 1.8, W3 zadatak 3.8 | — | — |
| M10 | 🆕 Popuni `[[reference/cenovnik]]` (jedna tabela, sve cene odjednom) — sprečava ping-pong "koja je cena za X" po svakoj sesiji | W2 Tier1 (M1) + obogaćivanje proizvoda | 2026-07-10 | isto kao M1 — "na upit" placeholder |

---

## 5. KPI TABLA (jun 2026 = mesec-nula)

| KPI | Baseline (jun) | Cilj do live-a | Cilj +60d posle live-a |
|---|---|---|---|
| Prave konverzije (hvala-proxy) | 55/mes | održati ≥55 | 70+/mes |
| Plaćene konverzije (kumulativno od juna) | 6 | 15–20 | **20–30 → Smart Bidding** |
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
- **N8:** gate pregled (sekcija 3) → GO/NO-GO odluka sa Miroslavom

---

## 8. W6/W7 — POSLE LIVE-A (2026-09-02+)

Detaljan social/email tok: skill `/w6-social`. Ovde samo ono što se planira
UNAPRED da se ne dočeka nespremno.

### W6 — Social / Email (fazni detalj u skill-u `/w6-social`)
- Faza 0 (pre live-a, jeftino): popis profila ✅ 2026-07-07 → [[reference/drustvene-mreze]] · M5 (kontakti) · GMB paket ✅ · saglasnost checkbox na formi
- **Ključni nalaz popisa:** FB+IG su aktivni (objave na ~7 dana) ali bez UTM/CTA — problem je merenje, ne prisustvo. Ožičavanje (UTM, link-in-bio) ide ODMAH, ne čeka septembar.
- Puni ritam (IG/FB 2×/ned, LinkedIn 1×/ned, YouTube 1×/mes) od 2026-09-01

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
[[PROGRESS]] · [[DNEVNIK-NAPRETKA]] · [[blokovi/BLOK-C-sledece]] · [[seo/plan-novih-stranica]] · [[seo/geo-ai-plan]] · [[dnevnik/ADS-DNEVNIK]] · [[analiza/2026-07-04-snapshot-full]] · [[migracija/woodmart-sabloni]] · [[odluke/_pregled-odluka]]
