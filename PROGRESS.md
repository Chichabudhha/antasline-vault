# SEO Progress — Antasline lokalni build

> 🟢 **GO-LIVE: UTO 2026-08-25** (M pomerio dan kasnije 2026-08-17, bilo PON 24.08;
> pre toga 31.08 → 24.08 odlukom 2026-08-10).
> 🔴 **Content freeze PONOVO OTVOREN 2026-08-17: 16.08 → ČET 20.08** (M odluka, ista sesija) —
> da materijali koji su propustili prvi freeze još uđu. Gate pregled **PET 21.08 nepromenjen**;
> rezerva je **vikend 22–23.08 + PON 24.08 (rezervni radni dan)**.
> 🟢 **Dve od tri obaveze zatvorene 19.08, dan pre freeze-a:** pun regression sweep (0 kvarova,
> nov baseline `analiza/2026-08-19-regression-pre-freeze-*`) i reverifikacija 301 mape (80 pravila,
> 43 cilja 200). 🔴 **Posle 20.08 ostaje:** brz potvrdni sweep + **nov backup zamrznutog builda na
> 2 lokacije**; regeneracija `.htaccess` drafta samo ako 20.08 promeni ijedan slug.
> Svi raniji rokovi „pre 31.08" i dalje znače **pre 21.08** — rokovi M odluka se NISU pomerili.

> **Plan projekta:** [[2026-07-06-MASTER-PLAN-V2]] (5 workstream-ova, migracija **2026-08-25**, gate kriterijumi) — stari [[2026-07-02-MASTER-PLAN-DO-LIVE]] je superseded.

## Urađeno

> Poslednja tri dana, jedan red po stavci. **Pun tekst za ceo avgust:**
> [[dnevnik/2026-08-arhiva-progress]] · jun–jul: [[dnevnik/2026-07-arhiva-progress]]
> · pune sesije: [[DNEVNIK-NAPRETKA]]

**2026-08-20**

- ✅ `[claude-code]` [C] Codex srl uvoz: 8 proizvoda, Onda objavljena sa cenom — [[dnevnik/2026-08-20-codex-srl-uvoz-proizvoda]]

**2026-08-19**

- ✅ `[claude-code]` W3 3.10 — regression sweep pre freeze-a (0 kvarova) + reverifikacija 301 mape; nov baseline za §B6 — [[dnevnik/2026-08-19-regression-sweep-pre-freeze]]
- ✅ `[claude-code]` W2/SEO — „skladišta" na 16687; nova stranica napuštena (URL bi kanibalizovao postojeći) — [[dnevnik/2026-08-19-skladista-16687]]

**2026-08-18**

- ✅ `[claude-code]` W1/W2 ESD — 3 ploče u katalogu (2 nova proizvoda), 7 zvaničnih PDF-ova, uporedna tabela + FAQPage schema, SRPS provera — [[dnevnik/2026-08-18-esd-dokumentacija-i-proizvodi]]
- ✅ `[claude-code]` VIZUALI — 8 AI slika obrađeno, 5 postavljeno (16658 · 16567); 3 čekaju stranicu radionica — [[dnevnik/2026-08-18-esd-dokumentacija-i-proizvodi]]
- ✅ `[claude-code]` W2/SEO — stavka F zatvorena (dimenzije klaster ↔ hub 2298) + title/meta `/industrijski-podovi/` — [[dnevnik/2026-08-18-F-dimenzije-klaster]]
- ✅ `[claude-code]` W2/W3 — **cenovne stranice konsolidovane u hubove** (4 „cena” stranice + `/cene/` draftovane; nijedna nije postojala na live-u, a hub već rangira poz. 6,6 za cenovni upit); segment „Cene” obrisan iz menija (77 → 70 stavki); 🔴 vraćeno 301 pravilo `/podovi-za-garaze/` (182 pogotka) koje je odluka od 11.08 isključila **jer je URL bio zauzet baš tom stranicom** — draft 79 → 80 pravila — [[dnevnik/2026-08-18-konsolidacija-cenovnih-stranica]]
- ✅ `[claude-code]` ALATI — `PROGRESS` „Blokeri" skraćen 27 → 7,5 KB (45 → 28 stavki, 6 zatvorenih/nadjačanih izmešteno); ceo fajl **41,2 → 22,1 KB**, pun tekst doslovno u arhivi — [[dnevnik/2026-08-arhiva-progress]]
- ✅ `[claude-code]` ALATI — token audit starta sesije: ledger 965 KB → 20 KB (357 unosa doslovno u arhive, 4 zalutala unosa vraćena), otvaranje sesije −67k tokena; duplo `§9` u CLAUDE.md razrešeno — [[dnevnik/2026-08-18-token-audit-rotacija-ledgera]]
- ✅ `[claude-code]` W3 — zatvorena **svih 6 konflikata** u migracionoj dokumentaciji (#5 `mu-plugins` bio pravi, #4 lažni pozitiv); meni 17424 = lažna uzbuna, build nije diran — [[dnevnik/2026-08-18-zatvaranje-konflikata-preflight]]
- ✅ `[claude-code]` W2 CONTENT — istraživanje „Industrijski podovi" + ESD: „radionica" ~4.700 prikaza, 8 akcija, 4 pitanja za M (🔴 „bez stranice" ispravljeno 19.08 — URL postoji) — [[dnevnik/2026-08-18-istrazivanje-industrijski-podovi-esd]]
- ✅ `[claude-code]` W5 5.4 — nedeljni izveštaj + nalaz: „Terase i bazene" troši uz PAUSED status — [[dnevnik/2026-08-18-nedeljni-izvestaj-terase-status]]

**2026-08-17**

- ✅ `[claude-code]` SADRŽAJ — 3 odluke od 17.08 izvršene: 6 proizvoda → draft (ne 14 — spisak zastareo, 7 ima slike) · F2.8 kartice → Radici Landscape · meni 67 obrisan — [[dnevnik/2026-08-17-izvrsenje-odluka-draft-f28-meni67]]
- ✅ `[claude-code]` W1 quick-win — 15793: legacy `productColors-block` swatch popravljen — [[dnevnik/2026-08-17-oauth-publish-i-15793-swatch]]
- ✅ `[claude-code]` KONEKTOR — OAuth consent screen Testing → In production, token više ne ističe — [[dnevnik/2026-08-17-oauth-publish-i-15793-swatch]]
- ✅ `[claude-code]` W3 3.10 — noćni backup nije radio 3 dana: uzrok korumpirane Aria sistemske tabele (`mysql.db`), popravljeno, 78/78 tabela čisto; „2 lokacije" nikad nije radilo — popravljeno i testirano — [[dnevnik/2026-08-17-backup-mysql-crash-pomeranje-roka]]
- ✅ `[claude-code]` PLAN — go-live **24.08 → UTO 25.08** (M): PON 24.08 = rezervni radni dan, 24 fajla ažurirano
- ✅ `[claude-code]` PLAN — content freeze **ponovo otvoren 16.08 → ČET 20.08** (M) + 3 obaveze koje to nosi (regression sweep, 301 draft, nov backup)
- ✅ `[claude-code]` ODLUKE — 4 sadržajne odluke upisane (14 proizvoda → draft · Edel Grass · F2.8 → Radici Landscape · meni 67 se briše); izvršenje sledeća sesija — [[odluke/_pregled-odluka]]

## Sledeće

> Prepisano 2026-08-12. Prethodno je ovo bila nenaslovljena gomila od ~15 stavki sa
> numeracijom `00000/0000/000/00/0a/0/1/1/2/2/2`, uglavnom zatvorenih, u kojoj su dva
> reda tvrdila da je W1 1.2 otvoren („31 stranica") mesec dana pošto je zatvoren —
> i to je 12.08 stvarno dovelo do pogrešnog izbora zadatka. **Aktuelno stanje je
> isključivo ovaj blok; sve ispod „Istorijske stavke" je arhiva, ne red čekanja.**

**Sadržajni prozor 17–20.08 (freeze ponovo otvoren M odlukom 17.08) — šta može da uđe:**
- ✅ **17.08 izvršeno 4 od 7:** trava-u-boji poreklo (Edel Grass, bez izmene na buildu) ·
  **F2.8 mapiranje veštačke trave** · **proizvodi bez fotke → draft** (6, ne 14 — spisak bio
  zastareo) · **brisanje menija 67**. → [[dnevnik/2026-08-17-izvrsenje-odluka-draft-f28-meni67]]
- 🟡 **Preostale 3 stavke čekaju MATERIJAL OD MIROSLAVA, ne vreme.** Ako ne stigne do 20.08,
  fallback koji je već aktivan ostaje trajno: **4 reference na `/o-nama/`** (fotke/logotipi) ·
  **P3 metadesc** (2699/4318/1094) · **definicija „starog formata" za 5119**.
- ✅ **Regression sweep IZVRŠEN 19.08 (dan pre freeze-a):** 235 stranica / 1.174 slike / 1.799 linkova —
  **0 kvarova**; 30 URL promena i 18 meta izmena vs 13.08 — sve vezane za dokumentovane odluke, nula
  neplaniranih. 301 mapa reverifikovana: 80 pravila, 43 cilja 200, 0 kolizija. Nov baseline za §B6:
  `analiza/2026-08-19-regression-pre-freeze-*`. → [[dnevnik/2026-08-19-regression-sweep-pre-freeze]]
- 🔴 Posle freeze-a 20.08 ostaje: **brz potvrdni sweep** + **nov backup zamrznutog builda na 2 lokacije**
  (jedina gate stavka koja se ne može zatvoriti pre freeze-a).

**Vault higijena — van sadržajnog prozora, ne blokira gate** (nalazi 18.08, [[dnevnik/2026-08-18-token-audit-rotacija-ledgera]]):
- ✅ ~~`PROGRESS` „Blokeri" = 27 KB = 70% ovog fajla, 45 stavki~~ — **SKRAĆENO 2026-08-18** na ~7,5 KB / 28 stavki; ceo fajl 41,2 → 22,1 KB (**−46%**, ≈−6k tokena po sesiji). Pun tekst pre skraćivanja doslovno u [[dnevnik/2026-08-arhiva-progress]]
- 🟡 `CLAUDE.md` (38,5 KB ≈ 12k tokena, čita se svaki start) → izmestiti §4.1/§7.1/§7.3/§10/§14/§15 u `reference/`
- 🟡 `reference/naucene-lekcije.md` (230 KB) staje u 2000 linija → svako čitanje povlači ceo fajl (~75k tokena); iseći na 4 tematska fajla
- 🟡 `migracija/woodmart-sabloni.md` (79 KB) je „OBAVEZNO prvo" za svaki W1 → kratak checklist + duboka referenca
- ⚪ Posle 25.08: obrisati mapu numeracije iz `CLAUDE.md` §10

**Zatvoreno pre prvog freeze-a (16.08):**
- Ništa nije obavezno otvoreno. W1 (red A 33/33, Polish 1–4, S1–S8, Court builder,
  alt tekst ✅ 12.08, FAZA 1 vizuali ✅ 13.08, FAZA 2 layout/CSS/UI ✅ 13.08,
  **ergonomske podloge — 8 proizvoda ✅ 14.08**) i W2 (20/20 content plan) su iscrpljeni.
  **Pun regression sweep ✅ 13.08 — 0 regresija na 239 stranica**, `.htaccess` 301 mapa
  reverifikovana (45/45 ciljeva 200). Zatvorene stavke od 13–14.08 sa punim opisom:
  [[dnevnik/2026-08-arhiva-progress]] (sekcija „Zatvorene stavke iz Sledeće").
- ✅ **Ikonice menija — M odluka 13.08: ne vraćaju se pre live-a.** Ostaju skinute
  (79 SVG priloga stoji u medijateci nevezano, za posle live-a ako se poželi).
- 🔵 **Vizuelno prihvatanje FAZE 2 (13.08):** pravila ritma sekcija pogađaju **14
  stranica koje M nije prijavio** (isti obrazac, popravljen u dizajn sistemu umesto po
  stranici) + Woo kategorija stranice. Ako negde razmak sada deluje pretesno — javiti
  koja stranica, rollback je jedan CSS blok. Ne blokira ništa.
- 🟡 **Čeka M, blokira sadržaj:** 4 reference na `/o-nama/` nemaju ni fotku ni logo
  (Beobasket · BG liga 3x3 · Hotel Prag Beograd · Restoran Sidro) — stoje kao tekstualna
  linija „Takođe: …" ispod grida. Čim stignu materijali → kartice (15 min posla).
  Isto i definicija „starog formata" za 5119/15793 (v. Blokeri).
- 🔵 Opciono, ako se ukaže sesija: `heading-order` + `target-size` na product karticama
  (WoodMart core layout, veći zahvat — **preporuka: posle live-a**, ne 4 dana pred gate).
- 🟢 **(F) 4 „dimenzije" stranice vs post 2298 — M ODLUKA 14.08: ne diramo pre live-a.**
  Post-live zadatak (~01.09), rizik svesno prihvaćen. Pun opis: [[dnevnik/2026-08-arhiva-progress]]
  · analiza [[seo/2026-08-13-kanibalizacija-konsolidacija-plan]] §3.1 #posle-live
- 🟡🆕 **Ads — pre reaktivacije pauziranih kampanja (M):** 3 oglasa + 2 asseta vode na **tuđi
  domen `ekopodneploce.rs`** (kršenje Google smernica), **11 URL-ova** na mrtve `/home/…`
  putanje, 4 na `http://`. Jedina ENABLED kampanja (ECOTILE) je čista → **ne blokira 25.08**,
  blokira reaktivaciju i 4.4. §2 iste analize
- ✅ ~~Sitno: meni stavka **17424 nema naslov**~~ — **LAŽNA UZBUNA, zatvoreno 2026-08-18.** Prazan `post_title` ima **9** stavki, sve `post_type` — WP pada na naslov povezane stranice; render 17424 = „Podovi za garaže“, svi URL-ovi 200. Build nije diran.
  (Drugi deo ove stavke — `ergonomske-podloge-2` na čist slug — zatvoren 13.08.)

**N7' (17–20.08 sadržaj + migraciona priprema, 21.08 gate):**
- W3 **3.10** — pre-migration checklist §A do kraja → [[migracija/2026-08-10-pre-migration-checklist]]
- ✅ **Dry-run `build-staging-package.sh` — IZVRŠEN 13.08.** Exclude pravila od 10.08 **rade**
  (preflight rizici **#1 i #4** zatvoreni: ni mail-logger, ni `mail-log.txt`, ni ijedan od **32**
  `.bak`-klase fajla nisu u arhivi). Usput uhvaćena **2 kvara koja skripta nije mogla sama da prijavi**:
  hardkodiran `OUT_DIR` (zato nikad nije testirana) i 🔴 **`.htaccess` u paketu** — lokalni nosi
  `RewriteBase /antasline/`, prepisao bi serverski i oborio sajt u celosti. Oba popravljena.
  → [[dnevnik/2026-08-13-dry-run-build-staging-package]]
- 🔴🆕 **Redosled koraka na dan migracije — paket je 2,7 GB, ne 1,3 GB (13.08).** Disk-bloker je
  jutros zatvoren računicom „~1,3 GB paket + ~1,3 GB backup ≈ 2,6 GB"; izmereno: kod **72,3 MB** +
  uploads **2.706,9 MB**. Naivan tok (FTP delovi + sklopljen tar istovremeno) traži **5.558 MB** od
  **5.867 MB** slobodnih — pre svežeg backup-a i pre raspakivanja → **ne staje**. Nije gate stavka,
  ali **diktira redosled** u checklisti §B: (1) preporučeno **rsync/scp preko SSH-a** umesto FTP
  chunkovanja (pristup potvrđen M6 21.07; chunking je bio zaobilaznica za nestabilnu data-konekciju,
  ne zahtev hostinga) · (2) ako ipak FTP — **ne sklapati tar pored delova**, nego
  `cat part-* | tar -xzf -` uz brisanje delova u hodu · (3) backup skinuti i **obrisati sa servera pre**
  uploada. Disciplinovan tok: pik ~4,4 GB ✅. #claude-code
- 🔴 **PET 21.08 — gate pregled.** Od 11 stavki otvorena je još samo **LCP**
  (blokirano na produkciju, ide kao svestan rizik).

**PON 24.08 — 🆕 rezervni radni dan (M odluka 17.08).** Ako gate 21.08 padne — popravka
ide ovde, bez pomeranja celog datuma. Ako je čisto — B1 priprema: svež live backup,
provera OAuth tokena, `build-staging-package.sh full`, rsync postavka.

**UTO 25.08 — migracija (3.11):** koraci u checklisti §B. Uslov za pokretanje:
Miroslav ima ~6h slobodnih tog dana (M odluka 11.08, „migracija samo kad sam tu").

**Posle live-a (26.08+):** 3.12 post-live monitoring · 5.7 verifikacija merenja ·
4.10 final URL audit · zatim W6 (social), Meta/LinkedIn tagovi, Display remarketing.

**Čeka Miroslava** — puna lista u sekciji Blokeri; pred gate su bitni:
`Klik na telefon (web)` → Secondary · pauziranje 6 BROAD reči · ~~OAuth *Publish app*~~
✅ **urađeno 17.08** · brisanje GTM taga id 18 · Enhanced Conversions toggle.

---

## Blokeri

> Samo otvorene stavke, **jedan red po stavci** (skraćeno 2026-08-18 sa 27 KB na ~7 KB).
> Pun tekst svake stavke pre skraćivanja + zatvoreni blokeri →
> [[dnevnik/2026-08-arhiva-progress]] („Blokeri — pun tekst pre skraćivanja" i „Zatvoreni blokeri").
> Detalji su u linkovanim dnevnicima — ovde stoji samo **šta čeka, koga čeka i da li blokira 25.08**.

**🔴 Pred gate 21.08 — kratke akcije u tuđem UI-ju (M, po 2–5 min):**
- `Klik na telefon (web)` → **Secondary action** u Ads UI. Broji se kao konverzija (17 od „26 plaćenih"), pa je pravih plaćenih lidova **9**, ne 26 — prag za 4.8 nije dostignut. Blokira svaku bidding odluku, ne migraciju. → [[analiza/2026-08-11-snapshot-jul]] §3.6
- **Enhanced Conversions toggle**: Goals → Conversions → „Lead - forma (GTM)" → Settings → Enhanced conversions → metod **Google Tag Manager** + customer data terms. Bez toga GTM šalje, Ads ignoriše. → [[migracija/2026-08-09-enhanced-conversions-4.7]]
- **Obrisati GTM tag id 18** (suvišan `page_view` na hvala pravilu) — duplira brojku, **postoji i na buildu pa preživljava migraciju**. Pre ili na dan migracije. → [[reference/naucene-lekcije]]
- **GSC UI, 3 koraka:** obrisati zastareo `http://` sitemap unos (submit 2018) · pogledati 3+4 upozorenja · potvrditi email alerte (prvi signal ako 301 blok ne proradi). → [[migracija/2026-08-10-pre-migration-checklist]] §A
- **ECOTILE dnevni budžet:** 1.300 RSD/dan gubi 50% prikaza na 2/12 dana → 1.800–2.000 RSD ili svesno prihvatiti gubitak. → [[dnevnik/ADS-DNEVNIK]]

**🟡 Sadržaj — rok ČET 20.08 (posle toga freeze, ide posle live-a):**
- **4 reference na `/o-nama/`** bez fotke/loga (Beobasket · BG liga 3x3 · Hotel Prag · Restoran Sidro) — sada tekstualna linija „Takođe: …"; materijal → kartice (15 min). Uz to: je li `Teren 3x3 Soccer liga.jpg` baš „BG liga 3x3"? → [[dnevnik/2026-08-12-vizuali-reference-ikonice]]
- **7 proizvoda sa generičkim dobavljačkim fotkama** (`16893` `16899`–`16902` `16906` `16919`) — ostaju `publish` ili i oni u draft? → [[dnevnik/2026-08-17-izvrsenje-odluka-draft-f28-meni67]]
- **P3 metadesc** za 2699/4318/1094 (uklj. ispravku `072`→`069 234 00 72`) — cPanel nema pristup lokalnoj bazi, preneti su samo naslovi. Ako ne stigne → posle migracije.
- **„Stari format" na 5119 i 15793** — nije reprodukovano (obe na aktuelnom `al-*` sistemu); legacy swatch na 15793 ✅ popravljen 17.08. Treba screenshot/opis šta konkretno štrči. → [[dnevnik/2026-08-12-vizuali-reference-ikonice]] §3
- 🟡 **`/podovi-za-radionice/` — najveća otvorena SEO prilika:** ~4.700 prikaza / ~275 klikova, poz. 3,6. 🔴 **Ispravka 19.08: URL POSTOJI** (blog post **5637**, hobi tekst o privatnoj garaži) — nije prazna rupa nego **nesklad namere**; nova stranica bi ga kanibalizovala. Preporuka: konverzija `post`→`page` uz isti ID i slug. 3 AI slike već obrađene i čekaju. **M odluka 19.08: post-live.** → [[dnevnik/2026-08-19-skladista-16687]]
- 🟡 **Video — čeka M odluku o stranici i Flow kredite.** Predlog: kadar polaganja bakarne trake iz naših **pravih** fotki (HTEC/Quectel), ne iz AI slika. → [[dnevni-video]]
- 🔴 **AI slike preko API-ja se PLAĆAJU** (`gemini-2.5-flash-image`, ~0,04 USD/sl.; 93 poziva od 04.08). Od 18.08: generisanje samo uz izričito M odobrenje; besplatna varijanta je Gemini chat.
- 🆕 **Codex Onda cena (17957)** — potvrdi 16.906 RSD sa PDV (preračunato od datih 14.088+PDV) ili ispravi ručno. → [[dnevnik/2026-08-20-codex-srl-uvoz-proizvoda]]
- 🆕 **7 Codex draft proizvoda** (Quadrio/Polyshock/Interior/Sport Roll/Crossfit Floor/Maxionda/Wall Mat) — pregled i publish odluka. → [[dnevnik/2026-08-20-codex-srl-uvoz-proizvoda]]
- 🆕 **Onda objavljena baš na 20.08** (poslednji dan freeze prozora) — uključi u brz potvrdni sweep + nov backup posle freeze-a. → [[dnevnik/2026-08-20-codex-srl-uvoz-proizvoda]]

**🟡 Ads — ne blokira 25.08, blokira reaktivaciju pauziranih kampanja:**
- 🔴🆕 **„Podloge za terase i bazene" troši uz `PAUSED` status** (18.08): 4.571 RSD / 250 klikova u dve nedelje, ad grupe i asseti ispod nje ENABLED, najjeftiniji CPC u nalogu (20,96 vs 94,41). Pali li se ručno i hvata li pauza ad grupe/assete? (Zamenjuje stavku od 11.08 „je li pauza namerna".) → [[dnevnik/2026-08-18-nedeljni-izvestaj-terase-status]]
- **Final URL-ovi pauziranih kampanja:** 3 oglasa + 2 asseta na **tuđem domenu `ekopodneploce.rs`** (kršenje smernica; 301 mapa tu ne pomaže) · 11 na mrtve `/home/…` · 4 na `http://`. Jedina ENABLED (ECOTILE) je čista → za 25.08 nema posla. → [[migracija/2026-08-11-ads-final-url-audit]]
- **6 BROAD reči, ~10.300 RSD/90d, 0 konverzija** (`podovi za terase` · `industrijski podovi` · `podne obloge za terase` · `pvc podne ploče` · `podovi za hale` · `podovi za radionice cena`) — pauzirati; phrase parnjaci konvertuju. + 4 nove negativne (`deking`, `epoksidna smola` — 🔴 **ne** `epoksid` u celini zbog conquest-a, `jysk`, `kameni podovi`). → [[analiza/2026-08-11-snapshot-jul]] §3.3–3.4
- **4.8 Maximize Conversions** — preporuka: **odložiti na ~01.09**; uključeno sada, učenje (~14 dana) završilo bi se tačno na dan migracije uz promenu URL-ova oglasa.
- **Customer Match** — `CUSTOMER_NOT_ALLOWLISTED_FOR_THIS_FEATURE` i posle re-autorizacije; verovatno traži **Data Manager API migraciju**, ne samo Standard access. Odluka: ulažemo u migraciju ili čekamo. `leads.csv` (9 kontakata) čeka netaknut. → [[dnevnik/2026-08-11-customer-match-data-manager-api]]

**🟡 Merenje / dokumentacija:**
- **KPI tabla u master planu meri preglede, ne lidove** — „55/mes (jun)" je broj pregleda; stvarno jun 24 sesije, jul 16. Odluka: prepraviti red na sesije („≥25/mes, cilj 35+") ili svesno ostaviti uz napomenu. → [[analiza/2026-08-11-snapshot-jul]] §2.3b
- 🔴 **Posle migracije `generate_lead` pada na ~⅓ a hvala-proxy na ~½ — to nije pad konverzija** (live Kallyas ima dva GTM embeda, build jedan; plus tag id 18). Zapisati pre prvog post-live izveštaja.
- **GA4 totali uključuju `localhost`/`staging`** (prošla nedelja 1.068 vs 1.504 sa live-a) — filtrira se ručno; trajan `hostName` filter u `ga4_report.py` menja **sve** buduće izveštaje pa čeka odluku (filter u GA4 ili odvojen Measurement ID za build).
- **Rank Math Redirections** — do 25.08 ne dirati; posle live-a uključiti `redirections` + `404-monitor`. 🔴 Tvrdo pravilo: `.htaccess` = zamrznut migracioni skup, Rank Math = sve posle 25.08, **isti URL nikad na oba mesta** (`.htaccess` se izvršava prvi i tiho pobeđuje). Upis u §B7 čeka „upiši u plan / ostavi". → [[dnevnik/2026-08-11-htaccess-301-reverifikacija]]
- **Izmene builda 12–13.08 bez ledger unosa** — uzrok („−118 slika/str." = uklanjanje ikonica menija) razjašnjen, M ga je 13.08 potvrdio kao namernu odluku i ikonice se **ne vraćaju pre live-a**; 79 SVG priloga ostaje u medijateci nevezano.

**🟡 Alati / infrastruktura:**
- **FTP lozinka je i dalje u git istoriji** (izmeštena iz radnog stabla 13.08, ali commit-ovi od 06.08 je nose, a vault se sinhronizuje na hosting). Jedina prava sanacija = **promena lozinke u cPanel-u, preporučeno posle 25.08**. Prepisivanje istorije se **ne preporučuje** (3 površine + Obsidian auto-sync).
- **GTM Preview test (5.6)** ne može dok je consent mu-plugin na stagingu namerno ugašen (`al-tracking-gtm-consent.php.off`, da klijentov pregled ne ulazi u GA4/Ads). `gallery_view`/`pdf_download` su DRAFT od 22.07 i čekaju test pre Submit-a. Kad zatreba: `mv …php.off …php`, test, vrati. → [[dnevnik/2026-08-13-staging-v4-puno-postavljanje]]
- **Headless `agy` (`-p`)** — fale `read_file(*)` / `list_dir(*)` / `grep_search(*)` / `find_by_name(*)` u `~/.gemini/antigravity-cli/settings.json`; harness ne dozvoljava da ih Claude doda. Bez toga `agy` radi samo kroz TUI.
- **Grok deny pravila (19) žive samo u `C:\Users\Miroslav\.grok\config.toml`** — Grok 1.0.3 ne učitava projektni `.grok/config.toml`, pa ne dolaze sa vault-om na drugu mašinu. Posle svakog `grok update`: `grok inspect | Select-String Permissions`. #claude-code
- ⚪ **JetBackup detalji nedostupni iz shell-a** — raspored je M potvrdio kroz cPanel UI (dnevni, off-site, 90 dana, M6 27.07); API odavde ne odgovara, pa svaka dalja provera ide kroz UI.

**🔴 Spoljna ograničenja (nema akcije na našoj strani):**
- **LiteSpeed image optimization** — oblak.host odbio 30.07 (QUIC.cloud zatvoren zbog napada, mera na nivou hostinga). **LCP gate ostaje crven kao svestan rizik.** UCSS (ista infrastruktura) oživeo 11.08.
- **GMB API 429** — forma za Basic API Access podneta 30.07, četvrti retest 09.08 i dalje 429. Google-ova ručna revizija, probati povremeno.

**🟡 Video / social (ne blokira migraciju):**
- **Gemini žig na kadru 5** basket videa (+ čitljiva registarska tablica kombija na kadru 4): ostaviti · vratiti se na 30,5s verziju sa 4 kadra · re-render u Flow-u (krediti se resetuju ≈09–10h po lokalnom, ne u ponoć) · krop/`delogo` **ne preporučeno**. → [[dnevnik/2026-08-10-kadar5-gemini-video-40s]]
- **YouTube handle** `@antasline5676` → `@antasline` **pre prve objave** (od tog trenutka ulazi u embed URL-ove i `VideoObject` schema-u). Traži pristup Google nalogu vlasnika kanala. Objava je ionako post-live.

**🔴 Live sajt — čeka odobrenje za `[cpanel-live]` fix:**
- **Kontakt forma na `/kontakt/` tiho odbija validne unose** (naziv firme sa brojem/tačkom/dijakritikom, telefon sa razmacima — a broj je svuda na sajtu ispisan sa razmacima). Crvena ivica, 0 AJAX poziva, nema poruke. Nije nova regresija — verovatno dugogodišnji gubitak submit-ova. Fix = ublažiti regex + vidljiva poruka greške. → [[DNEVNIK-NAPRETKA]] 2026-08-04

> **Rokovi:** svako „pre 31.08" u starijim beleškama znači **pre PET 21.08** (gate).
> Sadržajne odluke → **ČET 20.08**. Puna tabela M odluka: [[2026-07-06-MASTER-PLAN-V2]] §4.

## Napomene

**Redirect/parity (od 2026-07-07):** stara mapa i statistike su arhivirane
(`migracija/arhiva/` + [[migracija/arhiva/_SUPERSEDED]]). Aktuelno stanje parity-ja
živi u `migracija/parity-inventar.csv` (generiše F1). Izmereno 2026-07-07:
postovi 25/30 match · pages 8/50 · proizvodi 34/37 · Woo permalinci lokalno pogrešni
(`/shop/` umesto `/proizvod/` — F2 fix).

---

## ADS — Trenutno stanje

| Kampanja | Stanje | Napomena |
|---|---|---|
| Podloge za terase i bazene | ⏸️ **PAUZIRANA** (zatečeno 2026-08-11) | 04–10.08: 2.642,94 RSD/158 klikova/CTR 17,19%/CPC 16,73/3 konv — potrošnja **−62%** u odnosu na prethodnu nedelju (6.992,60) pre pauze. Najjeftiniji CPC u nalogu. #ceka-miroslav: je li pauza namerna |
| ECOTILE INDUSTRIJSKI PODOVI | ✅ radi — jedina ENABLED kampanja od 14 | 04–10.08: 4.247,67 RSD/42 klika/CTR 20,69%/2 konv — **CPC 64,04 → 101,13 (+58%)**, potvrđen drugi presek zaredom (nije jednonedeljni šum). 08–10.08: 2.357 RSD / 24 klika / **0 konverzija**. Uzrok od 06.08 nepromenjen: dnevni budžet 1.300 RSD gubi ~50% prikaza na spike-danima |

**Aktivna faza:** 1 (RSA Terase — može odmah) + 2 (struktura ad grupa); Faza 0 (odblokiranje) ✅ zatvorena 2026-07-04
**Reaktivacija posle godišnjeg (M):** blackout 07-05→07-21, kampanje ponovo pune isporuke od ~07-22 (v. [[dnevnik/ADS-DNEVNIK]] log 2026-07-30)
**Pravih konverzija (kumulativ od 01.06, hvala-proxy):** **119 pregleda = 51 sesija / 43 korisnika** (⚠️ serija je naduvana ~2,6× po sesiji — v. Blokeri, nalaz od 2026-08-11) / Ads uvezeno kumulativ **26** (mereno 11.08) / prag Smart Bidding: 20–30 plaćenih — **prag dostignut 2026-08-06, čeka M odluku o 4.8; preporuka nepromenjena: odložiti na ~01.09** (period učenja bi inače pao tačno na dan migracije)
**Snapshot podataka (16mo, sva 4 izvora):** [[analiza/2026-07-04-snapshot-full]] — jun = najveći Ads mesec (30,7k RSD); ECOTILE phrase "industrijski podovi" = 1.073 RSD/konv. ⭐; Terase imp. share 24% (QS problem); ~16% budžeta curi kroz neaktivne negativne

## ADS — Sledeće

1. **Faza 0 — preduslov** ✅ ZAVRŠENO 2026-07-04
   - Dopuna balansa + verifikacija oglašivača gotovi → nalog odblokiran
   - Preostaje: potvrditi da su ECOTILE prikazi/CPC vraćeni na normalu
2. **Faza 1 — RSA Terase** (ne čeka odblokiranje)
   - 15 headlines + 4 descriptions → Ad Strength ≥ Good
3. **Faza 2 — Struktura ad grupe** (Terase → 3 grupe: terase | bazeni | bergo/modularne)

## ADS — Blokeri

- Nema — nalog odblokiran 2026-07-04 (balans + verifikacija). Preostaje samo potvrda da su ECOTILE prikazi/CPC vraćeni na normalu.

**Detalji, plan i RSA banka:** [[dnevnik/ADS-DNEVNIK]]
