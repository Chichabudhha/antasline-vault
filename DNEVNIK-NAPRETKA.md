# Dnevnik napretka — tekući ledger

> **Newest-on-top.** Nov unos ide **NA VRH**, nikad na dno — i iz Claude Code-a
> i sa cPanel-a (`[cpanel-live]`). Unos na dnu je praktično nevidljiv: 13.08.2026
> je tako jedan propušten iz PROGRESS tabele.
>
> **Ovde stoji samo poslednjih ~5 dana.** Starije je DOSLOVNO preneto u mesečne
> arhive (ništa skraćeno ni prepisano): [[dnevnik/arhiva-dnevnik-2026-08]] · [[dnevnik/arhiva-dnevnik-2026-07]] · [[dnevnik/arhiva-dnevnik-2026-06]]
>
> Rotacija: `python skripte/rotiraj-dnevnik.py` (korak u `/zatvori-sesiju`).
> Pretraga cele istorije: `grep -rn "pojam" --include="*.md" .` — u kontekst
> ulaze samo pogođene linije, ne ceo fajl.

## 2026-08-18 [claude-code] [W1/W2] — ESD klaster: zvanična dokumentacija, dva nova proizvoda, dopuna stranice ✅

M je dao izvore i sva **4 `#ceka-miroslav` pitanja su zatvorena**: deklaracije (X-Joint ESD 1,46×10⁶ / 9,3×10⁵ Ω · E500/7 ESD 2,2×10⁴–3×10⁶ / 2,9×10⁴–5,7×10⁵ Ω, BS EN / IEC 61340-5-1:2016) · elektroprovodljivo **na upit, ne paušalno** (ATEX se ne sme tvrditi bez deklaracije za isporuku) · **merenje sa zapisnikom — da** · cena ESD **na upit**.

**Datasheetovi su otkrili da su u pitanju tri ploče, a katalog je imao jednu.** Dodati **E500/7 ESD** (17860) i **X-Joint antistatik** (17861, ≈1×10⁹ Ω, **bez uzemljenja** — server sale, kancelarije, servisi, štamparije). Treća je antistatik, ne ESD, i ta razlika određuje da li prostor mora da se uzemlji — stranica je ranije nije objašnjavala. Oba proizvoda: tehnička tabela iz deklaracije, PDF-ovi, unakrsni linkovi, Rank Math meta, 9 atributa + 4 nova terma, cena „na upit".

**7 zvaničnih PDF-ova** u medijateku (attachment 17850–17856), uključujući **zapisnik ispitivanja X500/7 ESD (7 strana)** — dokument koji traži inženjer kvaliteta uz auditsku dokumentaciju EPA zone. Stranica 16658 (13.797 → 22.925 zn.) dobila: uporednu tabelu antistatik/ESD/elektroprovodljivo · sekciju „Merenje otpora sa zapisnikom" · **„Zašto ne epoksidni antistatik pod"** (conquest ugao koji je falio) · sekciju dokumentacije · **FAQPage JSON-LD** koji nije postojao, sa 7 pitanja.

🔴 **Stara spec tabela nosila je vrednost „otpornost 3,4×10⁴ – 5×10⁶ Ω" koje nema ni u jednom tehničkom listu** (preneta sa live-a), i „debljina 7 mm (dostupno i 5 mm)" — 5 mm ESD nije potvrđen nijednom deklaracijom. Zamenjeno deklarisanim vrednostima po seriji.

🔴 **Ispravka mog ranijeg nalaza:** prijavio sam „42 od 52 rečenice sa live-a fale na buildu" i izveo 8 rupa — **pogrešno**. Diff je išao po rečenicama, a build je live tekst **prepisao, ne izbacio**. Build je već imao ATEX, zoniranje bojama, MoD UK, 25 godina, premeštanje, 80 m², tepih/kvaka. Stvarnih rupa bilo je pet, sve zatvorene.

🔴 Tri gotcha-a: (1) **Windows CRLF konverzija kvari `post_content`** iz `mysql -B --raw` — čitati `HEX()`, pisati `UNHEX()`, pa uporediti; (2) nov proizvod ne ulazi u WooCommerce upite bez reda u **`wpgs_wc_product_meta_lookup`**; (3) **Rank Math kešira sitemap kao fajlove** u `uploads/rank-math/*.xml` — brisanje opcije i transienata ne pomaže, `lastmod` je ostajao na 13.08 dok fajlovi nisu obrisani.

Backup `_pre-esd-rebuild.sql` (35,25 MB). Verifikovano: 4 URL-a 200 / 1×H1 · JSON-LD validan (FAQPage + Product) · 7 PDF-ova i 6 internih linkova 200 · grid prikazuje sve tri ploče · sitemap sadrži oba nova proizvoda.

🆕 **Dopuna iste sesije — slike sa proizvođača.** Šest fotografija sa ecotileflooring.com obrađeno po projektnom standardu (1:1, max 1000×1000, WebP), sa **generisanim WP veličinama** 150–900 px i punim `_wp_attachment_metadata` — bez toga grid vuče punu sliku umesto 300 px varijante. Po proizvodu: glavna + 2 u galeriji, alt na srpskom, `post_parent` = proizvod (ID 17870–17875). ⚠️ Ecotile ne objavljuje zasebnu fotku antistatik ploče (vizuelno identična ESD verziji) — za 17861 uzete su fotke X-Joint formata **bez priključka za uzemljenje**, jer se ta ploča ne uzemljuje. Product schema sada nosi `image`.

→ [[dnevnik/2026-08-18-esd-dokumentacija-i-proizvodi]]


## 2026-08-18 [claude-code] [W2/SEO] — Stavka F zatvorena: dimenzije klaster ↔ hub 2298 + title/meta `/industrijski-podovi/` ✅

Poslednja otvorena stavka iz plana kanibalizacije. Post **2298** (`kako-napraviti-teren-za-basket…`) je najjači sadržaj na sajtu — **13.686 prikaza / 385 klikova / 90d**, poz. 1–2 za „dimenzije fudbalskog terena" (2.174), „dimenzije košarkaškog terena" (2.004) i „dimenzije košarkaške table" (719) — a četiri **nove** stranice na buildu (16585 · 16586 · 16688 · 17027) gađaju baš te upite i 25.08 bi izašle pred Google **bez ijednog uzajamnog linka** sa njim. Sve četiri sada linkuju ka hubu i međusobno (pasus ispred zatvarajuće CTA sekcije, vodi ka izvođenju — služi i korisniku), hub dobio linkove ka tenisu i fudbalu. Title 16586 i 17027 pomeren ka **izgradnji** („…i izgradnja"); 16585 (već ima „cena") i 16688 (2298 ne cilja tenis) namerno nedirnuti.

Usput i **akcija #4 iz istraživanja 18.08**: `/industrijski-podovi/` (16567) — head termin curi na **6.321 prikaz / CTR 2,6% / poz. 7,2**. Live naslov je 78 znakova, seče se u SERP-u, bez cene i bez diferencijatora. Novo: `Industrijski podovi preko postojećeg betona — bez zastoja` + opis `Ecotile PVC ploče od 5.500 RSD/m² — … bez lepka i bez zastoja proizvodnje. Oštećena ploča se menja pojedinačno` + focus keyword koji nije postojao.

🔴 **M odluka 18.08 — cena ide u opis, ne u title.** Prva verzija je nosila cifru u naslovu (po CTR nalazu iz istraživanja: `antistatik pod cena` ima CTR 23,5%). M je to oborio jačim argumentom: **cena u title-u diskvalifikuje pre klika** — kome se cifra ne svidi neće kliknuti, a kamoli pozvati da čuje zašto je takva; Ecotile jeste skuplji po m², ali rešava problem dugoročnije i to se ne staje u 60 znakova. Podela: **title tera na klik** (obara primedbu „moraću da rušim i da stanem sa radom"), **opis nosi cenu** — Google i dalje vidi cenovni odgovor i servira stranicu za `industrijski podovi cena po m2`, a kvalifikacija se dešava u snippetu. Cifra nije izmišljena (5.500/6.800 RSD/m² stoje u telu stranice); 16874 je jutros draftovana pa nema dupliranja.

🔴 **Dve greške zatvorene u toku rada.** (1) `Get-Content -Raw | mysql` duplo enkodira UTF-8 → naslovi upisani kao „koĹˇarkaĹˇkog", a anchor sa dijakritikom se nije poklopio pa se izmena na 2298 **tiho preskočila**; baza vraćena iz backup-a i sve ponovljeno kroz Bash redirekciju (`mysql … < fajl.sql`). (2) Hero heading 17027 prebačen sa `<h2>` na `<h1>` kao „propust" — ali 17027 je `post`, a **`_woodmart_title_off` radi samo za `page`**; WoodMart svejedno daje `wd-post-title` H1 → nastao 2×H1, vraćeno. **Pravilo je već bilo zapisano** — `migracija/woodmart-sabloni.md` **F7.18**, i to na primeru baš ovog posta (27.07); promašeno jer taj fajl (79 KB, „OBAVEZNO prvo”) nije otvoren pre izmene. Argument više za njegovo cepanje na kratak checklist + duboku referencu (vault higijena u [[PROGRESS]]).

Backup `_pre-F-dimenzije.sql` (35,2 MB). Verifikovano HTTP-om: 5 URL-ova 200, **1×H1 svuda**, link ka hubu na sve 4, dijakritika čista na renderu.

⚠️ **Ograničenje:** interno linkovanje smanjuje, ali ne uklanja rizik da Google posle 25.08 privremeno zameni 2298 nekom od četiri stranice. Merenje: GSC poz. za 6 upita nedeljno; ako 2298 padne ispod poz. 3 → `noindex` na 17027 i 16586 dok se ne slegne.

→ [[dnevnik/2026-08-18-F-dimenzije-klaster]]


## 2026-08-18 [claude-code] [W2/W3] — Cenovne stranice konsolidovane u hubove + vraćeno 301 pravilo koje je odluka od 11.08 isključila ✅

M primedba („zašto dodajemo kontent koji se sukobi sa postojećim?") pokrenula proveru koja je pokazala da **nijedna od 4 „cena" stranice ne postoji na live-u** (sve 404) — napravljene 10.07 samo na buildu, prvi put bi otišle uživo 25.08, dakle **0 GSC istorije**. U isto vreme hub `/industrijski-podovi/` već drži „industrijski podovi cena po m2" na **poz. 6,6 bez ijedne cene na stranici**. Time je preporuka od 13.08 („tabela ostaje na cenovnoj stranici, hub dobija link") oborena. Cene prešle u hubove (`/industrijski-podovi/` kao 4. kolona postojeće tabele debljina · garaže dobile sekciju po kvadraturi · `/spoljnje-podne-obloge/` sekciju po vrsti podloge · parking hub već imao sve), 5 stranica draftovano, segment „Cene" obrisan iz menija (77 → 70 stavki, nestala i prazna stavka 17424).

🔴 **Glavni nalaz:** odluka od 11.08 da se istorijsko pravilo `/podovi-za-garaze/` (**182 GSC pogotka**) **ne prenosi** u `.htaccess` imala je za razlog baš to što je URL na buildu bio zauzet stranicom 16875 — draftovanjem je razlog pao, a URL bi posle migracije bio 404. Pravilo vraćeno (cilj namerno prebačen sa blog posta na hub garaža), draft regenerisan: **79 → 80 pravila, svi ciljevi 200**. Pouka: svaki izuzetak oblika „ne prenosi se **jer** je URL zauzet" je uslovan i pada tiho.

🔴 Gotcha: `wp-load.php` iz PHP CLI-ja **visi** (5+ min uz 4 s CPU) — izmene izvedene bez WP bootstrap-a, `mysql --raw` → Python → `UPDATE … UNHEX(...)` uz obavezno čitanje nazad i poređenje. Backup `_pre-konsolidacija-cena.sql` (36,95 MB). Verifikovano 8 URL-ova: 200 / 1×H1 / 0 nevalidnih JSON-LD / 0 mrtvih linkova; svih 5 draftovanih → 404, sitemap 61 URL.

→ [[dnevnik/2026-08-18-konsolidacija-cenovnih-stranica]]


## 2026-08-18 [claude-code] [ALATI] — `PROGRESS` „Blokeri" 27 KB → 7,5 KB, ceo fajl −46% ✅

Nastavak jutrošnjeg token audita, druga po redu stavka vault higijene. „Blokeri" je bio **27 KB = 70% celog `PROGRESS`-a** (45 stavki, prosek 588 B naspram konvencije ~150 B), a fajl se čita **prvi na svakoj sesiji**. Ceo tekst je prvo doslovno prenet u [[dnevnik/2026-08-arhiva-progress]] („Blokeri — pun tekst pre skraćivanja"), pa je sekcija prepisana kao jednoredni unosi grupisani po tome **koga čekaju i da li blokiraju 25.08**: pred-gate akcije u tuđem UI-ju · sadržaj do 20.08 · Ads · merenje · alati · spoljna ograničenja · video/social · live fix.

**6 stavki je bilo zatvoreno ili nadjačano novijim nalazom**, pa su izmeštene: OAuth *Publish app* (urađeno 17.08) · ikonice menija (M odluka 13.08) · poreklo trave (Edel Grass, potvrđeno 17.08) · Customer Match na developer tokenu (07.08, nadjačano nalazom 11.08 o Data Manager API-ju) · „je li pauza Terase namerna" (11.08, nadjačano nalazom 18.08 da troši uz `PAUSED`) · staging Basic Auth (22.07, nadjačano stanjem od 13.08). Još 3 para su spojena u po jedan red (GMB kvota ×2, `ekopodneploce.rs` u Ads URL stavku, Flow krediti u Gemini žig).

**Rezultat:** 45 → 28 stavki, sekcija 27 → 7,5 KB, `PROGRESS.md` **41,2 → 22,1 KB (−46%)** ≈ −6k tokena po otvaranju sesije. Nijedna otvorena stavka nije izgubljena — provereno jedna po jedna protiv originala; sve što je skraćeno ima link na dnevnik sa punim opisom.

## 2026-08-18 [claude-code] [ALATI] — Token audit starta sesije: ledger 965 KB → 20 KB ✅

Start sesije nosi ~54–60k tokena pre prve reči, a otvaranje je dodavalo do +70k — najviše na punom `Read`-u ledgera (988 KB) i master plana. Ledger rotiran u mesečne arhive (357 unosa doslovno, 0 izgubljeno) uz ispravku hronologije, konvencija „unos NA VRH“ popravljena na tri mesta, razrešeno duplo `§9` u CLAUDE.md. Otvaranje sesije: 87k → 19k tokena.

→ [[dnevnik/2026-08-18-token-audit-rotacija-ledgera]]

## 2026-08-18 [claude-code] [W3] — Zatvorena svih 6 konflikata u migracionoj dokumentaciji + 17424 lažna uzbuna ✅

Preflight checklist je od 12.08 nosio 6 konflikata u dokumentaciji, zatvoren je bio samo #1;
preostalih 5 zatvoreno danas, svaki proveren protiv stvarnog stanja (kod / baza / grep), ne
protiv druge beleške. Jedan je bio pravi (#5: zaglavlje `al-local-mail-log.php` tvrdilo da se
`mu-plugins` ne prenosi — netačno, i baš to je 07.08 oborilo sve mejlove na produkciji), jedan
lažni pozitiv (#4), a #6 je pokazao da ispravka na dnu append-only loga ne poništava tvrdnju sa
vrha. Quick-win 17424 se pokazao kao lažna uzbuna — prazan `post_title` ima 9 `post_type` stavki,
WP pri renderu pada na naslov stranice; **build nije diran, regression baseline od 13.08 i dalje važi**.

→ [[dnevnik/2026-08-18-zatvaranje-konflikata-preflight]]

## 2026-08-18 [claude-code] W2 CONTENT — Istraživanje sekcije „Industrijski podovi" + ESD ✅

Istraživanje za sadržaj (modularne PVC/LVT/ESD obloge, epoksid van opsega): sedam problema kupca po segmentima, ESD dubinski deo (standardi IEC 61340-5-1 / EN 1081 / EN 14041 / ATEX, rizični sektori u Srbiji, pet prodajnih uglova) i 8 prioritizovanih akcija za sajt. 🔴 Glavni nalaz je u GSC-u na query nivou, nevidljiv u klaster agregatu: **„radionica" nosi ~4.700 prikaza / 275 klikova sa poz. 3,5–7 bez ijedne namenske stranice** — više kvalifikovane tražnje nego ceo Ecotile-PVC klaster; uz to head-termin `industrijski podovi` curi (6.321 prikaz, CTR 2,6%) i ~1.800 prikaza čistog cenovnog intenta stoji neopsluženo, dok kontrolni `antistatik pod cena` ima CTR 23,5%. Standardi su iz javnih izvora i **nisu verifikovani protiv deklaracije Ecotile serija** — 4 pitanja za M pre bilo kakve objave. Post-live materijal, build nije diran.

→ [[dnevnik/2026-08-18-istrazivanje-industrijski-podovi-esd]]

---

## 2026-08-18 [claude-code] W5 5.4 — Nedeljni izveštaj + 🔴 kampanja „Terase i bazene" troši uz PAUSED status ✅

Izveštaj 11–17.08: saobraćaj +15% (728 korisnika), ali `generate_lead` 39→6 — **nije pad nego povratak u normalu** (nedeljni niz hvala-proxy: 4·6·12·6·**26**·4, prošla nedelja je bila izuzetak). Kumulativ pravih konverzija od 01.06 = **53**; pravih plaćenih formi ≈10 od potrebnih 20–30, prag 4.8 i dalje nedostignut. 🔴 Glavni nalaz: „Podloge za terase i bazene" je 11.08 u Ads API-ju bila `campaign_status: PAUSED` a **tog istog dana potrošila 222 RSD** — dnevni presek pokazuje 4.571 RSD / 250 klikova u dve nedelje (najveći dan naloga: 17.08, 1.643 RSD), uz `ad_group_status`/`ad_status` = ENABLED ispod pauzirane kampanje. Ne blokira migraciju (svih 5 njenih final URL-ova = 200 na buildu), ali je kampanja sa najjeftinijim CPC-om u nalogu (20,96 vs 94,41) sedam dana bila nevidljiva za analizu. Usklađeni ADS-DNEVNIK, PROGRESS i audit §2.1; `CLAUDE.md` §6 namerno nepromenjen.

→ [[dnevnik/2026-08-18-nedeljni-izvestaj-terase-status]]

---

## 2026-08-17 [claude-code] SADRŽAJ — 3 odluke od 17.08 izvršene (draft · F2.8 · meni 67) ✅

🔴 Glavni nalaz nije izvršenje nego **zastareo spisak**: odluka „14 proizvoda bez fotografije → draft" pisana je nad zapisom od 30.07, a spisak nabraja **13** ID-eva od kojih je **7 u međuvremenu dobilo sliku** (`16919` 06.08 · `16893`/`16899`–`16902`/`16906` 07.08, uz eksplicitno M odobrenje generičkih dobavljačkih fotki). Draftovano je zato **6 koji stvarno nemaju nijednu sliku** — sve generička sportska oprema koja čeka M12. Usput sanirana hub stranica 16676 (4 linka → `/kontakt/?form-naslov=`, 0 preostalih 404). **F2.8**: kartice Highlands/Nature/Put/Springgrass na 16673 prevezane na `16906` Radici Landscape (nisu vodile na kategoriju kako je zapisano — uopšte nisu imale link). **Meni 67** („O firmi", 39 stavki) obrisan, nije bio dodeljen nijednoj lokaciji; ostaju 390 (aktivan) i 280. Backup `_pre-odluke-17-08.sql` (36,9 MB), verifikovano 5 URL-ova 200/1×H1/0 grešaka.

→ [[dnevnik/2026-08-17-izvrsenje-odluka-draft-f28-meni67]]

---

## 2026-08-17 [claude-code] W1 quick-win — 15793: legacy `productColors-block` swatch popravljen ✅

Stranica `/zastitne-podloge-za-travu-i-plocnike/` (15793) bila je jedina u buildu sa Porto/Kallyas markupom — `.color-square` ne postoji ni u jednoj temi (potvrđeno grep-om), pa je swatch „Silk Black" renderovao prazan prostor; zamenjen samostalnim inline swatch-om, usput popravljen goli `<h2>` i dupli „Galerija" eyebrow. Verifikovano 200 / 1×H1 / 1 JSON-LD + 2 regresione stranice čiste. 🔴 Glavni gotcha nije bio u kodu nego u verifikaciji — `curl` je vratio `HTTP 000` (Apache ugašen) a `grep` ispisao uredne brojke iz zaostalog `/tmp/p.html` od ranije sesije.

→ [[dnevnik/2026-08-17-oauth-publish-i-15793-swatch]]

---

## 2026-08-17 [claude-code] KONEKTOR — OAuth consent screen `mcp-za-claude`: Testing → In production ✅

Zatvara 🔴 bloker od 12.08: u statusu *Testing* Google gasi refresh token na 7 dana, a poslednji je bio od 13.08 — pad je bio zakazan za ~20.08, tik pred gate i migraciju. M kliknuo **Publish app**, verifikovano živim `ads_report.py` pozivom (35 kampanja, 1.467,48 RSD) i `token.json` se sam osvežio, dakle refresh token je preživeo prelazak. 🔴 Gotcha: konzola je vraćala „You need additional access" sa 3 missing `oauthconfig.*` permission-a — uzrok nije bio IAM nego pogrešan Google nalog u Chrome-u.

→ [[dnevnik/2026-08-17-oauth-publish-i-15793-swatch]]

---

## 2026-08-17 [claude-code] W3 3.10 — Noćni backup nije radio 3 dana: korumpirane Aria sistemske tabele ✅

Zadatak je bio §A stavka „backup finalnog builda na 2 lokacije". Pri startu nalaz da **poslednji
uspešan backup nije od danas nego od 14.08 03:00** — u logu nema unosa za 15., 16. ni 17.08, ni
na `G:` ni u lokalnom `auto\backup-log.txt`. Task je jutros okinuo u 08:04:56 i pao sa
`0xC000013A` (proces prekinut, ne greška skripte); 15–16.08 su subota/nedelja, mašina ugašena.

🔴 **Pravi uzrok nije bio u skripti.** Ručno pokretanje reprodukovalo kvar: mysqld se **ruši
u startu** (`mysqld.dmp`), pa skripta puca na „MySQL se nije pokrenuo ni posle 30 s". Start u
prvom planu dao tačan razlog: `Table '.\mysql\db' is marked as crashed and last (automatic?)
repair failed` → `Can't open and lock privilege tables` → `Aborting`. Uz to
`InnoDB: Starting crash recovery`, a `ibdata1` poslednji put pisan **14.08 07:00** — XAMPP je
ubijen gašenjem mašine.

**Stanje Aria sistemskih tabela:** `mysql.db` korumpirana (`Key tree 1/2 is empty`, `error 176
when reading datafile`) · `mysql.tables_priv` korumpirana (`Bitmap at page 0 has pages reserved
outside of data file length`) · ~14 ostalih `marked as crashed`. **Nijedna nije AntasLine tabela**
— InnoDB deo (sav sadržaj builda) se uredno vratio crash recovery-jem.

**Popravka:** hladna kopija `mysql\data` pre ijedne izmene (448 fajlova / 217,9 MB, server ugašen
= najsigurniji backup, popravka time potpuno povratna) → `aria_chk -r -f mysql\*.MAI` **iz
`data\`** (iz `data\mysql\` ne nalazi `aria_log_control`) popravio 16 tabela → za 4 koje su pale
na `aria_sort_buffer_size` (🔴 parametar `--sort_buffer_size` se **ignoriše**, ostaje 16384)
`aria_chk -o -f` safe-recover. `mysql.db` izgubila 3 reda (`Wrong CRC on datapage`) — bezopasno,
XAMPP tu drži samo podrazumevane grantove za `test` bazu, root privilegije su u `global_priv`.

**Verifikacija:** MySQL startovao iz prve · `CHECK TABLE` nad **svih 78** `antasline_local`
tabela → **0 zamerki** · 8.552 posts / 104 objavljena proizvoda / 66 stranica / 47.426 postmeta /
245 termina / 1.005 options · dump 71,93 MB (14.08 bio 71,92) — **bez gubitka**.
🟢 **Freeze verifikovan (B1 stavka):** 0 izmena od 16.08, i na fajlovima i u bazi.

🔴 **Nezavisan nalaz — gate stavka „2 lokacije" nikad nije bila ispunjena.** Skripta je birala
**jednu** destinaciju (G:→OneDrive→lokalno), pa se serija razlivala: 10–12.08 na `C:`, 13–14.08
na `G:`, OneDrive folder **ne postoji**. Nijedan datum nije imao dve kopije, iako to checklist
traži od 10.08. Ista klasa greške kao `build-staging-package.sh` 13.08 („pokriveno skriptom", a
skripta nikad nije pokrenuta).

**Tri popravke u `nocni-backup.ps1`** (kopija `.bak-2026-08-17`, sintaksa provereno čista):
(1) kopija na drugu destinaciju posle uspešnog zip-a uz proveru veličine i zasebnu rotaciju (3);
(2) čišćenje zaostalih `_tmp` dump-ova starijih od 12h — dump se brisao samo na uspehu, zatečeno
**13 fajlova / 751,3 MB**; (3) pri padu MySQL-a u log se upisuje rep `mysql_error.log`-a, jer je
„MySQL se nije pokrenuo" danas koštalo pola sata dijagnostike.

**Rezultat (pun run 18:08–18:36, prvi test nove logike):** zip **2.810,4 MB** → `G:` u 18:33:49,
druga kopija na `C:\...uto\` u 18:35:59. Oba fajla **identična, 2.946.948.322 B**, arhive
čitljive (**102.488 unosa**, dump unutra 71,9 MB), `_tmp` prazan, rotacija 13 (G:) / 3 (C:).
🔵 Backup je **bezbednosna kopija tekućeg stanja, ne „finalni build"** — freeze je istog dana
ponovo otvoren do 20.08, pa §A stavka ostaje otvorena do tada.
🔵 Zapaženo: `Compress-Archive` drži celu arhivu u memoriji (~2,6 GB pika) i upisuje na kraju —
zato fajl stoji na 0 B ~25 min. Radi, ali je krhko; zamena mehanizma **nije** dirana 4 dana pred
gate (namerno).
→ [[dnevnik/2026-08-17-backup-mysql-crash-pomeranje-roka]]

---

## 2026-08-17 [claude-code] ODLUKE — 4 sadržajne odluke upisane (izvršenje sledeća sesija) 🟢

M doneo četiri odluke koje su blokirale sadržaj, neke od 29.07. **Upisane su samo odluke —
izvršenje je namerno odloženo** na sledeću sesiju po M zahtevu („akciju radimo u sledećoj
sesiji"). Sve četiri menjaju sadržaj → rok je **novi content freeze, ČET 20.08**.

| # | Odluka | Bila otvorena od |
|---|---|---|
| 1 | **14 proizvoda bez fotografije → `draft`** (16893, 16899–16902, 16906, 16990, 16991, 16998, 17001–17003, 16919) | 30.07 |
| 2 | **„Trava u boji" poreklo = Edel Grass B.V.**, ne Condor Grass | 08.08 |
| 3 | **F2.8: 4 modela trave (Highlands/Nature/Put/Springgrass) → `Radici Landscape`**, ne novi proizvodi | 29.07 |
| 4 | **Stari meni term 67 se briše** (nov je 390) | 30.07 |

**Logika koja povezuje 1 i 3:** obe izbegavaju praznu karticu u katalogu. Za 14 proizvoda je
pretražena cela foto-arhiva, baza i sajtovi proizvođača — nasumična slika bi rizikovala **tuđ
proizvod** na našoj stranici; za 4 modela trave u katalogu ne postoji nijedan proizvod, pa bi
pravljenje novih dalo četiri kartice bez slike i specifikacije.

🔴 **Zamke zabeležene za izvršenje:** (1) pre draftovanja 14 proizvoda proveriti da nijedan
interni link ni ijedno od **73** `.htaccess` pravila ne gađa te URL-ove — draft vraća 404;
(2) brisanje menija 67 je nepovratno na nivou baze → backup pre; (3) Edel Grass boje se **ne**
poklapaju 1:1 sa lokalnim Condor Schools/Playgrass setom (7 vs 6 boja) — ne mešati setove.

Odluka 2 usput **zatvara bloker od 08.08** i potvrđuje da je ranija pretpostavka „Condor Grass"
bila netačna.
→ [[odluke/_pregled-odluka]] · [[dnevnik/2026-08-17-backup-mysql-crash-pomeranje-roka]]

---

## 2026-08-17 [claude-code] PLAN — CONTENT FREEZE PONOVO OTVOREN: 16.08 → ČET 20.08 🔴

M odluka, ista sesija kao pomeranje go-live-a. Povod: pregled §4 pokazao da je **8 stavki
propustilo prvi freeze (16.08)**, ali da **7 od 8 nije bilo blokirano vremenom nego materijalom
od M** — fotke (14 proizvoda, 4 reference na `/o-nama/`), metadesc tekstovi (2699/4318/1094),
poreklo „trava u boji“ slika, odluka o mapiranju veštačke trave (F2.8), potvrda za brisanje
menija 67, definicija „starog formata“ za 5119. Prozor **17–20.08** služi da ti materijali uđu.
**Gate ostaje PET 21.08**, migracija UTO 25.08, rezerva 22–24.08.

🔵 **Jedina stavka koja ne čeka M:** `15793` (`/zastitne-podloge-za-travu-i-plocnike/`) —
jedina stranica u buildu sa legacy `productColors-block` markupom iz Porto/Kallyas ere, swatch
„Silk Black“ renderuje prazan prostor. Lokalizovano na jednu stranicu, bez promene sluga.

🔴 **Cena ponovnog otvaranja — tri obaveze koje moraju stati u 20–21.08:**
1. **Ponovni full regression sweep** — baseline od 13.08
   (`analiza/2026-08-13-regression-post-faza2-*`) prestaje da važi u trenutku prve izmene, a on
   je referenca za post-migracionu proveru (§B6 checkliste).
2. **Ako se promeni ijedan slug** → `redirect-verify.php` pa `htaccess-301-generate.php` ponovo
   (draft = 73 pravila, generator odbija upis ako cilj nije 200).
3. **Nov backup zamrznutog builda na 2 lokacije** — backup od 17.08 prestaje da bude „finalni
   build“ iz §A.

Svesno prihvaćen rizik: sadržajne izmene ulaze **bez rezervnog dana za regresije** (gate je dan
posle freeze-a). Zato pravilo za prozor: izmene što manje, što lokalnije, **bez dirania slugova**.

Ažurirano: master plan (§2 raspored, §4 uvod + nova napomena sa obavezama), [[PROGRESS]]
(header + „Sledeće“ prepisan u sadržajni prozor), [[CLAUDE]] §8/§15, pre-migration checklist
(B1 provera zamrznutosti gleda 20.08 ne 16.08, nova §A stavka sa tri obaveze), preflight
checklist, skill `/antasline-sesija`.
→ [[dnevnik/2026-08-17-backup-mysql-crash-pomeranje-roka]]

---

## 2026-08-17 [claude-code] PLAN — GO-LIVE POMEREN DAN KASNIJE: PON 24.08 → UTO 25.08 🟢

M odluka. Suprotno pomeranju od 10.08 (koje je **skraćivalo** rok), ovo je **dobijen dan**:
gate ostaje **PET 21.08** i freeze ostaje **NED 16.08**, pa rezerva raste sa „samo vikend
22–23.08“ na **vikend + ceo PON 24.08 = 3 dana**. **Ponedeljak 24.08 je od sada rezervni
radni dan** — prelivanje ako gate padne (bez pomeranja celog datuma), ili B1 priprema ako je
gate čist (svež live backup, JetBackup provera, OAuth token, `build-staging-package.sh full`,
rsync postavka). 🔴 Ono što se **ne sme** raditi u ponedeljak: sam prenos,
`wp search-replace`, aktivacija `.htaccess` 301 bloka i GTM paket — to je utorak, u jednom
prozoru, po §B redosledu.

**Rokovi M odluka se NISU pomerili** — 21.08 ostaje tvrd rok, namerno, da rezerva ostane
rezerva a ne razvučen rok.

**Sweep kroz vault:** 156 pojava datuma u 46 fajlova, ažurirano **24 fajla** — master plan
(frontmatter `go-live`, §2 raspored, §3 gate, §8 post-live, 3.11), [[PROGRESS]], [[CLAUDE]] §8/§12/§15,
pre-migration checklist (naslov, §A/§B, B7 → 26.08+), preflight checklist, rollback plan,
Ads final URL audit, Enhanced Conversions spec, `AGENTS.md`, 6 skillova (`/antasline-sesija`,
`/antasline-ads`, `/antasline-konektor`, `/nedeljni-izvestaj`, `/w6-social`, `/agy-delegat`),
`reference/` ×4, `seo/` ×3. **Datirani zapisi (`dnevnik/`, `analiza/`) namerno nisu prepisivani** —
oni beleže šta je bilo tačno tog dana; merodavni su plan i checklist. Ime fajla
`2026-08-12-preflight-checklist-24-08.md` ostaje nepromenjeno zbog link-stabilnosti, uz napomenu
u naslovu.
→ [[dnevnik/2026-08-17-backup-mysql-crash-pomeranje-roka]]

---

## 2026-08-14 [claude-code] W3 — Prefiks baze `wpGs_` → `wpgs_` zatvoren u korenu ✅

M odobrio obe popravke iz Copilot nalaza. Rešeno u `wp-config.php` umesto po fajlovima —
ali izmena **nije jednodelna**: WordPress od prefiksa izvodi i ključeve koji se čuvaju kao
stringovi, pa je uz config preimenovano i **16 redova u bazi** (`wpGs_capabilities` ×4,
`wpGs_user_roles`, `wpGs_user_level` ×4, +7 kozmetičkih). 🔴 **Zamka:** kolacija
`utf8mb4_general_ci` je case-neosetljiva pa bi SQL provera dala lažno zeleno — ali WP meta
keš je PHP niz, gde je case bitan, i promašaj bi ostavio **sve korisnike bez ijedne dozvole**
(zaključan wp-admin). Verifikovano `wp user list` kroz pun WP stek: obe admin role netaknute,
HTTP 200 na tri stranice, 0 pogodaka na `wpGs_` u temi i mu-pluginima. Backup 36 MB pre izmene.
[[CLAUDE]] §2 ispravljen — tvrdio je da lokalni config nosi `wpGs_`.
✅ **Rep zatvoren istog dana (M: „sweep svih promptova"):** 13 fajlova ispravljeno — F1/F2/F3
promptovi, tri prompta koja gađaju Linux (uz dodatu obaveznu proveru prefiksa protiv dump-a),
master skill sesije i `reference/identifikatori.md`; istorijski zapisi namerno ostavljeni.
Usput uhvaćena **pokvarena provera** u staging promptu (`grep -v wpGs_` na Linux-u ne bi
filtrirao `wpgs_` tabele) i osvežen `identifikatori.md`, gde su **3 od 5 tvrdnji** o lokalnom
okruženju bile netačne (106 tabela → **78**, Porto+WPBakery → **WoodMart 8.5.4**, Yoast →
**Rank Math**).
→ [[dnevnik/2026-08-14-copilot-grok-delegati]]

---

## 2026-08-14 [claude-code] ALATI — Copilot CLI i Grok CLI kao read-only delegati ✅

Dva CLI alata instalirana 13.08 uvedena u posao uz tvrdu ogradu: nijedan ne menja fajlove
ni ne čita kredencijale, provereno živim testovima (Copilot **pokušao pa blokiran** na
`write` i `shell`; `git status` čist posle svih testova). Nov skill `/delegati` je router
za četiri delegata — Copilot za kod, Grok za drugo mišljenje, `agy` za markdown, `ollama`
za sirove izlaze. Prvi posao odmah našao **`wpGs_options` u dva sirova `mysqli` upita**
(`job-plugin-cleanup-cron.php:12,33`), verifikovano nezavisnim grep-om — ista klasa greške
koja je oborila probu migracije 21.07.
🔴 **Gotcha:** projektni `.grok/config.toml` grok 1.0.3 **nađe ali ne primeni**
(`0 loaded`) — zabrane morale u `~/.grok/config.toml`, dakle van gita. Uz to: grok sandbox
na Windows-u ne postoji, Copilot podrazumevano izvozi sesije na GitHub, a delegat ume da
vrati uredan izveštaj „pregledano 0 fajlova" bez ijedne greške.
🔴 **Revizija premise na kraju sesije:** oba delegata su **Free** — Copilot ~50 zahteva
mesečno (≈1,6 dnevno, testiranje potrošilo ~5), Grok bez naplate ali sa ~23k tokena po
pozivu. „Rasterećenje Claude kvote" time otpada; delegati su specijalisti za par pitanja
mesečno, Claude Code ostaje nosilac posla. Router prepisan po **oskudnosti**
(`ollama` → `agy` → Grok → Copilot), a `ollama` time postaje najvredniji jer je jedini bez kvote.
→ [[dnevnik/2026-08-14-copilot-grok-delegati]]

---

## 2026-08-14 [claude-code] W1/BLOK C — Ergonomske podloge: nova Woo kategorija + 8 proizvoda ✅

Izvršen spec od 13.08 (M odobrio obim, izvršenje bilo odloženo) — poslednji radni dan pre
content freeze-a. `product_cat` **403** + proizvodi **17838–17845** (Diamond Allround, Soft
Air Meter, SuperSoft Smooth/Office, La Ola, La Ola Hygienic, Nitrile Walk, Solido I), svi
„cena na upit", svaki sa `al-table` specifikacijom, 2 FAQ pitanja i FAQPage schemom. Hub
**16672**, koji do danas nije imao **nijedan** interni link osim `/kontakt/` i `tel:`,
prevezan: 8 kartica → linkovi, nazivi u tabeli poređenja → linkovi, uzajamne veze ka
`/industrijski-podovi/` i ESD stranici (7.329 → 9.600 B). Naslov kategorije namerno pomeren
ka „asortiman i modeli" da ne kanibalizuje hub (poz. 3,8). Verifikacija 12 URL-ova
200/1×H1/0 grešaka + 4 regresione stranice čiste.
🔴 **Gotcha:** `wp_insert_post` bez prijavljenog korisnika primenjuje kses i **tiho briše
`<script type="application/ld+json">`** — prvi prolaz je izgledao uspešno a schema nije
postojala; fix `kses_remove_filters()`.
🟢 **Tri pitanja za M zatvorena istog dana:** La Ola/La Ola Hygienic ostaju na generičkoj fotki
(ergomat.com 403, `intl.ergomat.com` mrtav → dopuna slika iz spec-a nije bila izvodljiva) ·
namene SuperSoft Smooth/Office ostaju kako su na hub stranici · kategorija se ne dodaje u meni.
→ [[dnevnik/2026-08-14-ergonomske-podloge-proizvodi]]

---
