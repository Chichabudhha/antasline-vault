# SEO Progress — Antasline lokalni build

> 🔴 **GO-LIVE: PON 2026-08-24** (M pomerio nedelju ranije 2026-08-10, bilo 31.08).
> Content freeze **NED 16.08** · gate pregled **PET 21.08** · rezerva samo vikend 22–23.08.
> Svi raniji rokovi „pre 31.08" sada znače **pre 21.08**.

> **Plan projekta:** [[2026-07-06-MASTER-PLAN-V2]] (5 workstream-ova, migracija **2026-08-24**, gate kriterijumi) — stari [[2026-07-02-MASTER-PLAN-DO-LIVE]] je superseded.

## Urađeno

> Poslednja tri dana, jedan red po stavci. **Pun tekst za ceo avgust:**
> [[dnevnik/2026-08-arhiva-progress]] · jun–jul: [[dnevnik/2026-07-arhiva-progress]]
> · pune sesije: [[DNEVNIK-NAPRETKA]]

**2026-08-14**

- ✅ `[claude-code]` W3 — Prefiks `wpGs_`→`wpgs_` zatvoren u korenu: `wp-config.php` + 16 redova u bazi + sweep 13 dokumenata + `identifikatori.md` osvežen — [[dnevnik/2026-08-14-copilot-grok-delegati]]
- ✅ `[claude-code]` ALATI — Copilot/Grok kao read-only delegati + skill `/delegati`; prvi posao našao `wpGs_options` u 2 mysqli upita — [[dnevnik/2026-08-14-copilot-grok-delegati]]
- ✅ `[claude-code]` W1/BLOK C — Ergonomske podloge: `product_cat` 403 + 8 proizvoda (17838–17845) + hub 16672 prevezan — [[dnevnik/2026-08-14-ergonomske-podloge-proizvodi]]

**2026-08-13**

- ✅ `[cpanel-live]` staging.antasline.com V4 puno postavljanje — [[dnevnik/2026-08-13-staging-v4-puno-postavljanje]]
- ✅ `[claude-code]` W2/SEO stavka E — 5438 vratio basket-semantiku (78% klikova stranice) + planer link + FAQPage — [[dnevnik/2026-08-13-5438-basket-semantika-faqpage]]
- ✅ `[claude-code]` W2/SEO — treća FAQ stranica (17025) u hub, klaster zatvoren
- ✅ `[claude-code]` W2/SEO — FAQ klaster „izbor industrijskog poda" konsolidovan u hub
- ✅ `[claude-code]` W2/SEO stavka A — čist slug `/ergonomske-podloge/` + nalaz da 8 tipova nema proizvode
- ✅ `[claude-code]` Vault higijena — PROGRESS.md 1,4 MB → 247 KB, jun+jul u arhivu
- ✅ `[claude-code]` W2/SEO — čist slug za „preko starog parketa": 6588 preuzeo URL, 16613 ugašen
- ✅ `[claude-code]` W2/SEO — kanibalizacija: analiza 9 klastera + tri konsolidacije (C/D/B)
- ✅ `[claude-code]` W3 3.10 — dry-run `build-staging-package.sh`: 2 skrivena kvara + kvota ne staje — [[dnevnik/2026-08-13-dry-run-build-staging-package]]
- ✅ `[claude-code]` Dokumentacija — SEO plugin pravilo prepisano: Rank Math jedini, Yoast van upotrebe (M odluka) — [[dnevnik/2026-08-13-seo-plugin-pravilo-yoast-brisanje]]
- ✅ `[claude-code]` W3 3.10 — pun regression sweep posle FAZE 2 (239 str.): 0 regresija; −118 slika/str. objašnjeno; 301 mapa reverifikovana — [[dnevnik/2026-08-13-regression-sweep-post-faza2]]
- ✅ `[claude-code]` W2/SEO — meta description za 13 taksonomijskih arhiva (M odobrio isti dan)
- `[claude-code]` FAZA 1 — vizuali, mediji i Bergo blok (unos zatečen na DNU ledgera, prenet ovde 13.08)
- ✅ `[cpanel-live]` LiteSpeed prefetch/Instant Click provera (UŽIVO, read-only) — bezbedno, rizik iz Chrome-platform §3 zatvoren — [[dnevnik/2026-08-13-litespeed-prefetch-instant-click]]
- ✅ `[cpanel-live]` Disk kvota — zvaničan broj potvrđen (UŽIVO, read-only), pre-flight nalaz iz 08-12 potpuno zatvoren
- ✅ `[claude-code]` FAZA 2 — layout/CSS/UI: 6 zamerki na 5 stranica svedeno na 3 sistemska uzroka, popravljeno sitewide

**2026-08-12**

- ✅ `[cpanel-live]` Pre-flight infrastruktura (UŽIVO, read-only) — disk prostor rizik, JetBackup nedostupan iz shell-a
- ✅ `[claude-code]` W3 — `live-export.sh` gubio 145 od 170 galerijskih slika + prefiks baze ispravljen (`wpGs_`→`wpgs_`) — [[dnevnik/2026-08-12-live-export-galerije-prefiks]]
- ✅ `[claude-code]` ALATI — Antigravity (`agy`) kao delegat za masovno čitanje + pre-flight checklist za 24.08 — [[dnevnik/2026-08-12-agy-antigravity-delegat]]
- ✅ `[claude-code]` BLOK C — Vizuali referenci i ikonice kartica (homepage, O nama, padel, maloprodaja) — [[dnevnik/2026-08-12-vizuali-reference-ikonice]]
- ✅ `[claude-code]` W1 — Alt tekst na slikama proizvoda: 66 priloga popunjeno, 159 dekorativnih ikonica namerno ostavljeno prazno — [[dnevnik/2026-08-12-alt-tekst-slike-proizvoda]]
- `[claude-code]` W1 — red čekanja zatečen prazan, dva zastarela statusa ispravljena
- ✅ `[claude-code]` W1 quick-win — Chrome 149 `border-color` na tabelama proveren, build nije pogođen
- ✅ `[claude-code]` W5/GEO — GenAI baseline snimljen pre migracije — [[dnevnik/2026-08-12-genai-baseline-sesija]]
- ✅ `[claude-code]` ALATI — Chrome dokumentacija ugrađena u skilove + novi `/antasline-ads` playbook
- ✅ `[claude-code]` BLOK C — `product_brand` arhive napunjene (Ecotile 7, Ergomat 27), 301 cilj više nije prazna stranica — [[dnevnik/2026-08-12-product-brand-arhive]]

**2026-08-11**

- ✅ `[claude-code]` W5 5.4 — korekcioni faktori upisani u skillove + mesečni snapshot za jul (kasnio 11 dana) — [[dnevnik/2026-08-11-mesecni-snapshot-jul]]
- ✅ `[cpanel-live]` Live backup (DB+wp-content) na 2 lokacije + robots.txt AI-crawler pravila aktivirana i ispravljena
- ✅ `[cpanel-live]` LiteSpeed CCSS/UCSS/LQIP/VPI status provera — UCSS oživeo posle 11 dana, LQIP nov lokalni bug nađen (fix odbijen) — [[dnevnik/2026-08-11-litespeed-ccss-ucss-lqip-vpi-status]]
- ✅ `[cpanel-live]` LiteSpeed Redis/Web Cache Manager (nove cPanel opcije) — istraženo, NE rešava stari QUIC.cloud problem, Redis odložen — [[dnevnik/2026-08-11-litespeed-redis-web-cache-manager]]
- ✅ `` W1 — Ergomat DuraStripe trake: slike po bojama + simple → variable
- ✅ `` W5 — inflacija `generate_lead` DIJAGNOSTIKOVANA: dva različita baga, jedan preživljava migraciju — [[dnevnik/2026-08-11-generate-lead-inflacija-dijagnoza]]
- `` W5 5.4 — ponovljen nedeljni izveštaj sirovim konektorom, obe današnje lekcije pregažene — [[dnevnik/2026-08-11-w5-nedeljni-izvestaj]]
- ✅ `[cpanel-live]` W6 / 4.9 — Customer Match upload pokušan uživo: blokiran na Data Manager API, koriguje raniju pretpostavku (Standard access) — [[dnevnik/2026-08-11-customer-match-data-manager-api]]
- ✅ `[claude-code]` W5 5.4 — nedeljni izveštaj (04–10.08); merenje „pravih konverzija" je naduvano ~3× — [[dnevnik/2026-08-11-w5-nedeljni-izvestaj]]
- ✅ `[claude-code]` GSC priprema — build je emitovao 3 sitemap-a gde live emituje 7; 27 URL-ova sa 79 klikova bilo van sitemap-a — [[dnevnik/2026-08-11-gsc-priprema-sitemap]]
- ✅ `[claude-code]` W4 4.10 — Final URL audit oglasa ZATVOREN: aktivna kampanja čista (7/7), 2 URL-a vode na tuđi domen — [[dnevnik/2026-08-11-ads-final-url-audit]]
- ✅ `[claude-code]` W3 3.9 — `.htaccess` 301 reverifikacija: draft je bio 8 pravila, treba 73; petlja i 2 pregažene stranice uhvaćene — [[dnevnik/2026-08-11-htaccess-301-reverifikacija]]
- ✅ `[claude-code]` W3 CWV — dijeta asseta: proizvod stranice lakše 46%, postovi 51%, blog arhiva 65% — [[dnevnik/2026-08-11-dijeta-asseta-tema]]
- ✅ `[claude-code]` Legacy CPT-ovi (Custom Post Type UI) obrisani iz builda i baze + 5 zamenjenih stranica u draft
- ✅ `[claude-code]` Rollback plan ZATVOREN (gate stavka, 4 dana pre roka) + sitewide provera dijakritike čista — [[dnevnik/2026-08-11-rollback-plan-i-dijakritika]]
- ✅ `[claude-code]` 6 stranica bez meta opisa (uklj. početnu) — napisani i objavljeni, bloker zatvoren

## Sledeće

> Prepisano 2026-08-12. Prethodno je ovo bila nenaslovljena gomila od ~15 stavki sa
> numeracijom `00000/0000/000/00/0a/0/1/1/2/2/2`, uglavnom zatvorenih, u kojoj su dva
> reda tvrdila da je W1 1.2 otvoren („31 stranica") mesec dana pošto je zatvoren —
> i to je 12.08 stvarno dovelo do pogrešnog izbora zadatka. **Aktuelno stanje je
> isključivo ovaj blok; sve ispod „Istorijske stavke" je arhiva, ne red čekanja.**

**Do content freeze-a (NED 16.08) — poslednji prozor za izmene sadržaja:**
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
- 🔵 Opciono: `15793` je jedina stranica u buildu sa legacy `productColors-block`
  markupom (swatch „Silk Black" renderuje prazan prostor) — čeka M odluku, v. Blokeri.
- 🟢 **(F) 4 „dimenzije" stranice vs post 2298 — M ODLUKA 14.08: ne diramo pre live-a.**
  Post-live zadatak (~01.09), rizik svesno prihvaćen. Pun opis: [[dnevnik/2026-08-arhiva-progress]]
  · analiza [[seo/2026-08-13-kanibalizacija-konsolidacija-plan]] §3.1 #posle-live
- 🟡🆕 **Ads — pre reaktivacije pauziranih kampanja (M):** 3 oglasa + 2 asseta vode na **tuđi
  domen `ekopodneploce.rs`** (kršenje Google smernica), **11 URL-ova** na mrtve `/home/…`
  putanje, 4 na `http://`. Jedina ENABLED kampanja (ECOTILE) je čista → **ne blokira 24.08**,
  blokira reaktivaciju i 4.4. §2 iste analize
- 🔵 Sitno, nije odobreno: meni stavka **17424 nema naslov** (prazan red u „Cene" segmentu).
  (Drugi deo ove stavke — `ergonomske-podloge-2` na čist slug — zatvoren 13.08.)

**N7' (17–21.08), freeze — samo migraciona priprema:**
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

**PON 24.08 — migracija (3.11):** koraci u checklisti §B. Uslov za pokretanje:
Miroslav ima ~6h slobodnih tog dana (M odluka 11.08, „migracija samo kad sam tu").

**Posle live-a (25.08+):** 3.12 post-live monitoring · 5.7 verifikacija merenja ·
4.10 final URL audit · zatim W6 (social), Meta/LinkedIn tagovi, Display remarketing.

**Čeka Miroslava** — puna lista u sekciji Blokeri; pred gate su bitni:
`Klik na telefon (web)` → Secondary · pauziranje 6 BROAD reči · OAuth *Publish app*
(inače token pada baš 24.08) · brisanje GTM taga id 18 · Enhanced Conversions toggle.

---

## Blokeri

> Samo otvorene stavke. **73 zatvorenih** (i „Istorijske stavke") izdvojeno
> 2026-08-13 u [[dnevnik/2026-08-arhiva-progress]].

- 🟡🆕 **Grok zabrane su van gita (2026-08-14).** Grok 1.0.3 **ne primenjuje** projektni `.grok/config.toml` (`grok inspect` → `0 loaded`), pa 19 deny pravila živi u `C:\Users\Miroslav\.grok\config.toml`. Posledica: važe za **sve** projekte na mašini, i **ne dolaze sa vault-om** ako se klonira na drugu mašinu. Projektna kopija je identična i aktivira se sama kad verzija to podrži. **#claude-code** — posle svakog `grok update` proveriti `grok inspect | Select-String Permissions`. Ne blokira migraciju.
- 🟡🆕 **Ads URL-ovi pre reaktivacije pauziranih kampanja (2026-08-13, svež pull).** 3 oglasa + 2 asseta imaju final URL na **tuđem domenu `ekopodneploce.rs`** (kršenje Google smernica — final URL mora biti na istom domenu kao prikazani), **11 URL-ova** vodi na mrtve `/home/…` putanje koje ne postoje ni na buildu ni u 301 draftu, 4 su na `http://`. 🟢 `tracking_url_template`/`final_url_suffix` su `null` na svih 14 kampanja, a jedina **ENABLED** kampanja (ECOTILE) je čista → **za 24.08 nema posla**; ovo je uslov za reaktivaciju i blokira W4 4.4. #ceka-miroslav
- 🟢 **Ikonice menija — M ODLUČIO 2026-08-13: ne vraćaju se pre live-a.** Uklanjanje od 12.08 je potvrđeno kao namerno; 79 SVG priloga i `uploads/meni-ikonice/` ostaju u medijateci nevezani ni za jednu `nav_menu_item` stavku (rollback moguć posle live-a). Meni ide u produkciju kao čist tekst. Dokumentacioni deo stavke ispod ostaje otvoren (nedostajući unosi za 12.08 rad).
- 🟡🆕 **Izmene builda od 12–13.08 koje nigde nisu evidentirane — nađene tek regression sweep-om (2026-08-13).** Sweep je pokazao **−118 slika na svakoj stranici**; uzrok je **uklanjanje ikonica iz mega menija 12.08**, o kome ne postoji nijedan unos ni u [[DNEVNIK-NAPRETKA]] ni ovde — jedini trag je ime backup fajla `antasline_local_2026-08-12_pre-uklanjanje-meni-ikonica.sql`. Isto važi za još 4 backup-a od 12.08 (`_pre-draft-5455`, `_pre-bergo-varijacije`, `_pre-15480-rebuild`, `_pre-stari-format-stranice`) i za **FAZU 1 od 13.08**, čiji je ledger unos završio **na dnu** `DNEVNIK-NAPRETKA.md` umesto na vrhu (fajl je newest-on-top) pa je bio nevidljiv — prenet u „Urađeno" tabelu 13.08. **Zašto je bitno 3 dana pred freeze:** build je jedini izvor istine za migraciju, a ono što nije zapisano ne može se ni proveriti ni vratiti; 79 SVG ikonica (`uploads/meni-ikonice/`) sada stoji u medijateci nevezano ni za jednu `nav_menu_item` stavku. **Ne blokira migraciju.** #ceka-miroslav — potvrditi da je uklanjanje ikonica bila namerna odluka (poklapa se sa #ceka-miroslav stavkom od 06.08 „ikonice za meni Miroslav sam bira") i da li ikonice treba vratiti pre live-a ili se skidaju trajno.
- 🟡 **FTP lozinka — IZMEŠTENA VAN VAULT-A 2026-08-13 (M zahtev, isti dan).** Nađena u **dva** fajla, ne jednom (`ftp-upload-chunks.sh` + `ftp-upload-resume.sh`, oba linija 7/8, verzionisana od 06.08). Sada u `C:\Users\Miroslav\antasline-ftp-creds.txt` (van repo stabla — git ga ni ne vidi), obe skripte ga `source`-uju preko `FTP_CREDS_FILE` sa podrazumevanom `~` putanjom i **tvrdo padaju sa `exit 1`** pre ijednog poziva ako fajla nema ili ne definiše `FTP_CREDS`. Usput: hardkodiran host → `$HOST`, a `ftp-upload-resume.sh` je imao i hardkodiran naziv arhive od 06.08 → sada prvi argument. Provereno: `grep` po celom radnom stablu ne nalazi lozinku nigde, učitavanje očuvalo specijalne znakove (`$$`, `&^`).
  🔴 **Ostaje otvoreno i traži M: izmeštanje NE briše lozinku iz git istorije** (`.git/` je i dalje ima, commit-ovi od 06.08 nadalje, a vault se sinhronizuje na hosting). Jedina prava sanacija je **promena FTP lozinke u cPanel-u** — preporuka: posle migracije 24.08, da se ne dira kanal prenosa pred sam prenos. Prepisivanje git istorije se **ne preporučuje** (vault ima 3 površine i Obsidian Git auto-sync — rewrite bi razbio sve tri). #ceka-miroslav
- ⚪🆕 **JetBackup snapshot status — nedostupan iz shell-a (2026-08-12).** `uapi Backup list_backups` → nalog nema feature "backup"; `JetBackup5::wrapper` traži nedokumentovan format (3 pokušaja); nema lokalnih artifact fajlova. Poslednji datum/retencija/off-site status i dalje **nepotvrđen** — treba proveriti kroz WHM/cPanel UI (Backup Wizard/JetBackup stranica), API odavde ne daje odgovor. #ceka-miroslav ili sledeća sesija sa UI pristupom.
- 🔴🆕 **Google Cloud app je u statusu *Testing* → OAuth pada svakih 5–7 dana (2026-08-12, potvrđeno i lekcijom 2026-08-11).** Konektor za Ads/GA4 gubi autorizaciju (`invalid_grant`). Ako padne na dan migracije, otpadaju tačke 3 i 10 iz pre-flight checklist-a (OAuth provera i Ads Final URL fix). Trajno rešenje je **`Testing` → `Published`** u Google Cloud konzoli, ne pokretanje `authorize_oauth.py` u panici na dan migracije. #ceka-miroslav — ~10 min klikanja u konzoli.
- 🟡🆕 **Headless `agy` (`-p`) još ne radi — fale dozvole za čitanje (2026-08-12).** `permissions.allow` u `~/.gemini/antigravity-cli/settings.json` ima `command(...)` unose (dodato, potvrđeno da sintaksa radi), ali ne i `read_file(*)`, `list_dir(*)`, `grep_search(*)`, `find_by_name(*)`. Claude Code harness blokira i `--dangerously-skip-permissions` i dalje širenje dozvola drugom agentu, pa ovo mora M ručno. Bez toga `agy` radi **samo kroz TUI** (M nalepi prompt i odobri) — što je i dalje upotrebljivo, samo se ne može skriptovati. #ceka-miroslav
- 🔵🆕 **Maximize Conversions — prag NIJE dostignut, odluka 4.8 se odlaže (2026-08-12).** Od 26 „plaćenih konverzija" **17 su bili `tel` klikovi**, pravih formi sa `/hvala-za-poruku/` ima **9**. Prag 20–30 nije ni blizu. Ostaje **Maximize Clicks**. Uz to: posle migracije GA4 konverzije padaju **~70%** (nestaje dupli GTM embed iz Kallyas teme) — to treba iskomunicirati unapred da se ne pročita kao pad prodaje. #claude-code — zatvoriti odluku 4.8 u [[odluke/_pregled-odluka]] kao „odloženo, prag nije dostignut".
- 🟡🆕 **Šta znači „stari format" na 5119 i 15793? — sesija PREKINUTA na zahtev M (2026-08-12).** M je prijavio da su `/vestacka-trava-za-fudbal/` (5119) i `/zastitne-podloge-za-travu-i-plocnike/` (15793) „u starom formatu". **Nije reprodukovano:** obe koriste aktuelan `al-*` dizajn sistem (hero, `al-section`, `al-label`, `al-display`, dijagonale, CTA) i imaju **identične `body` klase** kao rebuild-ovane stranice (`page-template-default`, `wrapper-full-width`, isti theme/child). Na pitanje šta konkretno štrči M je odgovorio **„Prekini"** → **0 izmena** na te dve stranice, baza nije dirana. **Nalazi za kad se tema otvori:** 🔴 **15793 je jedina stranica u buildu sa legacy markupom** — `<div id="colorBlock" class="productColors-block">` sa `.color-square` (Porto/Kallyas klase, ne postoje u WoodMart-u → swatch „Silk Black" renderuje **prazan prostor**); uz to spec kao običan `<ul>` umesto `al-table`, **dve galerije** na istoj stranici (2022 JPG + 2026 WebP), jedan `<h2>` bez `al-label` eyebrow-a, inline-stilizovan `<img style="width:100%">`, **nula `al-card`** (Bergo Solid i Mosolut Heavy opisani inline umesto karticama ka `/proizvod/`). **5119** nema nijedan legacy marker (12 `al-card`, 7 `al-hero`); jedino galerija je gola `<img>` mreža bez lightbox-a iako tekst kaže „Kliknite na sliku za uvećan prikaz". **Audit svih 53 objavljene stranice:** `productColors-block`/`color-square` **samo na 15793**; ⚠️ zastavica `porto` u auditu je **lažni pozitiv** (poklapa se unutar reči „s**porto**va"), a `raw-table` je legitiman sadržaj. #ceka-miroslav — reći šta se vidi (ili poslati screenshot), pa ciljano popraviti. v. [[dnevnik/2026-08-12-vizuali-reference-ikonice]] §3.
- 🟡🆕 **4 reference na `/o-nama/` nemaju ni fotku ni logo (2026-08-12).** **Beobasket · BG liga 3x3 · Hotel Prag Beograd · Restoran Sidro** — pretraženi `uploads`, cela DB i foto-arhiva `C:\Miroslav\`, nula pogodaka. Ostavljeni kao sitna linija „Takođe: …" ispod grida umesto da im se pripiše tuđa fotka. #ceka-miroslav — poslati fotke izvedenih radova ili logotipe (pretvaranje u kartice je ~15 min). Uz to: u arhivi postoji `novi sajt/tereni za basket/Teren 3x3 Soccer liga.jpg` — **nije** pripisana „BG liga 3x3" jer nije potvrđeno da je taj projekat; potvrditi ili demantovati. Ne blokira migraciju.
- 🔴🆕 **`Klik na telefon (web)` se broji kao Ads konverzija — 17 od „26 plaćenih" (2026-08-11).** Nađeno mesečnim snapshot-om: akcija ima `include_in_conversions_metric=True` i `primary_for_goal=True`, dakle **ulazi u „Conversions" kolonu i u Smart Bidding**, iako [[CLAUDE]] §4 izričito kaže „ne uvoziti GA4 `tel` kao Ads konverziju (double-counting)". Od 01.06 do 10.08: **17 tel + 9 forma**. **Posledica: prag 20–30 za 4.8 nije dostignut — pravih plaćenih lidova ima 9**, a ne 26 kako su svi dosadašnji izveštaji tvrdili. Postoje i **dve** aktivne telefonske akcije (`Klik na telefon (web)` WEBPAGE + `Pozivanje klikom na telefon na sajtu` CLICK_TO_CALL); druga ima 0 konverzija, ali ako proradi, telefon se broji dvaput. **#ceka-miroslav**: Ads → Goals → Conversions → „Klik na telefon (web)" → prebaciti u **Secondary action** (2 min, ne briše istorijske podatke). **Ne blokira migraciju**, ali blokira svaku odluku o bidding-u. v. [[analiza/2026-08-11-snapshot-jul]] §3.6.
- 🔴🆕 **KPI tabla u master planu meri preglede, ne lidove — traži ispravku (2026-08-11).** „Prave konverzije 55/mes (jun)" iz [[2026-07-06-MASTER-PLAN-V2]] §5 je broj **pregleda** `/hvala-za-poruku/`; stvarni jun je **24 sesije**, jul **16**, avgust (1–10) **11**, kumulativ 01.06–10.08 = **119 pregleda / 51 sesija**. Ciljevi „održati ≥55" i „+60d posle live-a 70+/mes" stoje na ~2× naduvanoj bazi i biće trajno crveni bez razloga — a posle migracije brojka pada još jednom (dupli `page_view` tag id 18). **#ceka-miroslav**: odobriti prepravku KPI reda na **sesije** (predlog: „≥25/mes, cilj 35+") ili svesno ostaviti kako jeste uz napomenu. Ne blokira migraciju. v. [[analiza/2026-08-11-snapshot-jul]] §2.3b.
- 🟡🆕 **6 BROAD ključnih reči potrošilo ~10.300 RSD/90d bez ijedne konverzije (2026-08-11).** `podovi za terase` · `industrijski podovi` · `podne obloge za terase` · `pvc podne ploče` · `podovi za hale` · `podovi za radionice cena` — sve BROAD, sve 0 konverzija, dok phrase parnjaci konvertuju (`industrijski podovi` phrase: 903 RSD/konv, najjeftinije u nalogu). Plan ionako kaže „broad tek uz Smart Bidding", a nalog je na Maximize Clicks — dakle radi suprotno sopstvenom pravilu. #ceka-miroslav: pauzirati ih. Uz to 4 nove negativne iz search terms-a (`deking`, `epoksidna smola` — 🔴 **ne** `epoksid` u celini zbog conquest-a, `jysk`, `kameni podovi`). Ne blokira migraciju. v. [[analiza/2026-08-11-snapshot-jul]] §3.3–3.4.
- 🔴🆕 **Customer Match upload blokiran — traži Data Manager API migraciju, ne samo Standard access (2026-08-11).** Uživo pokušan `--confirm` upload (`customer_match_upload.py --split-by-category`) posle uspešne re-autorizacije — pao na `CUSTOMER_NOT_ALLOWLISTED_FOR_THIS_FEATURE`. Ranija pretpostavka ([[2026-07-06-MASTER-PLAN-V2]] 4.9, 2026-08-07) da treba samo Standard developer token access je **verovatno netačna** — Google dokumentacija kaže da developer tokeni bez istorije Customer Match zahteva moraju na novi Data Manager API, bez obzira na tier. **#ceka-miroslav**: odluka da li ulažemo u migraciju (nov API enable, moguć nov OAuth scope, prepis dela koda `customer_match_upload.py`) ili čekamo/tražimo Standard access kao alternativu (nepotvrđeno da bi to samo po sebi rešilo problem). `leads.csv` (9 kontakata) čeka netaknut. v. [[dnevnik/2026-08-11-customer-match-data-manager-api]].
- 🟡 **Merenje „pravih konverzija" — ✅ DIJAGNOZA GOTOVA 2026-08-11 (uveče), ostaje samo odluka o GTM izmeni.** Dva odvojena baga, ne jedan: **(A)** suvišan `page_view` GA4 tag (id 18) na hvala pravilu duplira automatski `page_view` Google taga → hvala-proxy je tačno **2× stvaran broj dolazaka**; postoji **i na buildu**, dakle **preživljava migraciju**. **(B)** live Kallyas stranica ima **dva GTM embeda** istog kontejnera → `generate_lead` okida **3×**; lokalni WoodMart build ima jedan embed i daje **1×**, dakle ovaj bag **nestaje sam migracijom**. Izmereno u mreži (`g/collect` po `en=`), ne pretpostavljeno. 🔴 **Posledica: prvi post-live izveštaj će pokazati pad `generate_lead` na ~⅓ i hvala-proxy na ~½ — to nije pad konverzija.** #ceka-miroslav: obrisati tag id 18 iz GTM-a (jedina izmena, ostavlja 1 pv + 1 gl po dolasku) — pre ili na dan migracije. v. [[reference/naucene-lekcije]] + [[DNEVNIK-NAPRETKA]]. ~~Original (2026-08-11 popodne):~~
- 🟡🆕 **Konektorovi GA4 totali uključuju `localhost`/`staging` — odluka o trajnom `hostName` filteru (2026-08-11).** Prethodna nedelja: 1.068 pregleda sa localhost-a vs 1.504 sa live-a (42% ukupnog). Izveštaji se od sada filtriraju ručno; upis filtera u `ga4_report.py` menja izlaz **svih** budućih izveštaja pa nije izvršen bez odluke. Trajno rešenje posle live-a: GA4 filter internog saobraćaja ili odvojen Measurement ID za build. **#claude-code / #ceka-miroslav** (odluka koja varijanta).
- 🟡🆕 **Je li pauza kampanje „Podloge za terase i bazene" namerna? (2026-08-11)** Potrošnja je pala **−62%** (6.992 → 2.643 RSD) tokom nedelje 04–10.08, pa je kampanja pauzirana (zatečeno današnjim 4.10 auditom). Ako pauza nije namerna, gubimo ~158 klikova nedeljno na najjeftinijem CPC-u u nalogu (16,73 RSD vs 101,13 na ECOTILE). #ceka-miroslav.
- 🟡🆕 **GSC UI — 3 sitna koraka čekaju Miroslava (2026-08-11).** Nijedan ne blokira migraciju, svi traže pristup Search Console UI-ju (API ih ne pokriva): (1) obrisati zastareo **`http://www.antasline.com/sitemap_index.xml`** unos — submit-ovan **2018-04-09**, Google ga i dalje povlači (poslednji put 10.08); (2) pogledati **3 + 4 upozorenja** na oba sitemap-a — Sitemaps API vraća samo brojač, ne i tekst; (3) potvrditi da su **email alerti uključeni** (profilni meni → *Search Console preferences*) — na dan migracije je alert o skoku indexing grešaka prvi signal da 301 blok nije proradio. v. [[migracija/2026-08-10-pre-migration-checklist]] §A.
- 🔴🆕 **2 oglasna URL-a vode na TUĐI domen `ekopodneploce.rs` — odluka čeka Miroslava (2026-08-11).** Nađeno final URL auditom: `http://www.ekopodneploce.rs/` (3 oglasa u pauziranoj kampanji „Ecotile kampanja") i `http://www.ekopodneploce.rs/proizvodi/E%20500-7/E500-7.html` (sitelinkovi „Industrijski podovi" + „Podovi za magacine", nivo kampanje). Danas ne troše ništa jer je kampanja pauzirana, ali **301 mapa tu ne pomaže** (nije naš domen) — ako se kampanja ikad reaktivira, plaćen klik odlazi sa antasline.com. Takođe `http://`, ne `https://`. #ceka-miroslav: prepisati na antasline.com parnjak ili obrisati te objekte. **Ne blokira migraciju.** v. [[migracija/2026-08-11-ads-final-url-audit]] §2.2.
- 🟡🆕 **OAuth consent screen — *Publish app* čeka Miroslava (2026-08-11).** Token je danas bio mrtav (`invalid_grant`, osvežen 06.08 → pao 11.08 = 5 dana) i re-autorizovan; ali uzrok je sistemski: u statusu *Testing* Google gasi refresh token svakih **7 dana**. 🔴 **Znači da će pući i 24.08**, kad se radi 4.10 i verifikacija konverzija. Trajno rešenje, 2 minuta: Cloud Console → APIs & Services → OAuth consent screen → **Publish app** (*In production*); u skriptama se ne menja ništa. Ako se ne uradi — ponovna autorizacija je **obavezna ujutru 24.08** (dodata kao stavka B1 u [[migracija/2026-08-10-pre-migration-checklist]]). GA4/GSC ovo ne osećaju (servisni nalog). v. [[migracija/2026-08-11-ads-final-url-audit]] §5.
- 🆕 **Rank Math Redirections posle live-a — odluka čeka Miroslava (2026-08-11).** Poređenje `.htaccess` vs Rank Math urađeno (v. Urađeno tabela vrh). Preporuka: **do 24.08 ne dirati ništa** (uključivanje modula 5 dana pred freeze = nov modul + nove DB tabele + nov kod na svakom zahtevu, rizik bez dobitka), a **posle live-a** uključiti `redirections` + `404-monitor` u `rank_math_modules` da M sam rešava nove 404-ove kroz UI umesto ručnog pregleda serverskog loga (stavka B7 checkliste). Uz to ide 🔴 tvrdo pravilo: `.htaccess` = zamrznut migracioni skup, Rank Math = sve posle 24.08, **isti URL nikad na oba mesta** (`.htaccess` se izvršava prvi i tiho pobeđuje, pa pravilo u UI-ju izgleda „ne radi" bez ijedne poruke). #ceka-miroslav: „upiši u plan" ili „ostavi" — analiza je gotova, upis u [[migracija/2026-08-10-pre-migration-checklist]] §B7 i master plan 3.12 **namerno nije izvršen** bez odluke. **Ne blokira migraciju.** v. [[dnevnik/2026-08-11-htaccess-301-reverifikacija]].
- 🔴🆕 **ROKOVI SU OD 2026-08-10 NEDELJU KRAĆI (go-live 24.08).** Svaka stavka ispod koja kaže „pre 31.08" znači **pre 21.08**. Raspored po tipu odluke:
  - ~~**do 15.08** — rollback plan~~ ✅ **ZATVOREN 2026-08-11, 4 dana pre roka** (sva 3 pitanja: JetBackup 5 dnevni/off-site/90 dana · nema CDN/edge sloja, samo LiteSpeed · M odluka „migracija samo kad sam tu"). 🔴 **Nosi obavezu za dan migracije:** ne pokretati 3.11 ako M nema ~6h slobodnih ispred sebe — inače se migracija pomera. v. [[migracija/rollback-plan]] §4/§5
  - **do 16.08 (content freeze)** — sve što menja sadržaj sajta: trava-u-boji poreklo · F2.8 mapiranje veštačke trave · 14 proizvoda bez fotke · brisanje menija 67 · P3 metadesc · Gemini žig kadar 5 + tablica kombija · YouTube handle
  - **do 21.08 (gate)** — Enhanced Conversions Ads UI toggle · ECOTILE budžet · odobrenje za live kontakt-forma fix · svež live backup (`[cpanel-live]`)
  - **danas ili posle live-a** — 4.8 Maximize Conversions: uključeno sada, učenje (~14 dana) završava se tačno na dan migracije uz promenu URL-ova oglasa. **Preporuka: odložiti na ~01.09.**
  - v. [[2026-07-06-MASTER-PLAN-V2]] §4 tabela.
- 🆕🔴 **Gemini vodeni žig na kadru 5 — odluka čekala Miroslava (2026-08-10).** Kadar 5 basket videa je napravljen u Gemini-ju (Flow ostao bez kredita) i nosi vidljiv Gemini „sparkle" žig u donjem desnom uglu kroz ceo klip; Flow klipovi ga nemaju. Opcije: (1) ostaviti — žig je diskretan, na travi u uglu · (2) izbaciti kadar 5 i vratiti se na 30,5s verziju sa 4 kadra · (3) sačekati plaćene Flow kredite i renderovati čist kadar · (4) krop/`delogo` — **ne preporučeno** (gubi se kadar, a uklanjanje vidljivog žiga je zasebno pitanje uslova korišćenja). Ništa nije dirano. Usput, ista odluka: na kadru 4 (Ledine) čita se registarska tablica parkiranog kombija — zamutiti pre javne objave ili ostaviti. v. Urađeno tabela vrh + [[dnevnik/2026-08-10-kadar5-gemini-video-40s]].
- 🆕🟡 **Flow krediti — reset je ≈09–10h po lokalnom, ne u ponoć (potvrđeno i 2026-08-10).** Render u 06:45 pao na „You need more AI credits"; nije kraj kvote nego pacifička ponoć. Za sledeće video sesije: **ne planirati render pre ~10h.** Praktična posledica danas: kadar 5 bez Gemini žiga može se dobiti posle 10h za 10 kredita. v. [[seo/2026-08-09-video-obogacivanje-plan]] §2 + [[reference/naucene-lekcije]].
- 🆕 **YouTube handle — čeka Miroslava (2026-08-09), blokira prvu objavu videa.** Kanal postoji (`youtube.com/@antasline5676`, „mrtav" po [[reference/drustvene-mreze]]), handle je auto-generisan. Treba ga promeniti u **`@antasline` PRE prve objave** — od tog trenutka ulazi u embed URL-ove i `VideoObject` schema-u na sajtu, pa je jeftinije sada nego posle deset videa. Traži pristup Google nalogu koji je vlasnik kanala. Odluka o vidljivosti je već doneta: **javno, ne unlisted**. Stavka je ionako u W6 planu (Faza 1c), samo je iz „septembar" postala blokator. v. Urađeno tabela vrh + [[seo/2026-08-09-video-obogacivanje-plan]] §8.
- 🆕 **Enhanced Conversions (4.7) — Ads UI korak čeka Miroslava (2026-08-09).** Lokalni deo je gotov i verifikovan; GTM deo se izvršava na dan migracije po spec-u. Preostaje jednokratno u Google Ads: Goals → Conversions → **„Lead - forma (GTM)"** → Settings → Enhanced conversions → uključiti, metod **Google Tag Manager**, prihvatiti „customer data terms". Bez toga GTM šalje podatke a Ads ih ignoriše. Bezopasno i može bilo kad **pre 21.08** (rok pomeren sa 31.08 — v. bloker o pomeranju na vrhu; ne menja ništa dok GTM deo ne proradi). v. Urađeno tabela vrh + [[migracija/2026-08-09-enhanced-conversions-4.7]].
- 🟡 **GMB API kvota — četvrti retest 2026-08-09, i dalje 429** (`mybusinessaccountmanagement.googleapis.com`, „Requests per minute"). Nepromenjeno od 07-30; Google-ova ručna revizija Basic API Access zahteva još traje. Nema akcije na našoj strani — probati ponovo za nekoliko dana. (Zamenjuje raniji red o retestu 08-06.)
- 🆕🔴 **"Trava u boji" live slike — poreklo nepotvrđeno, čeka Miroslava (2026-08-08).** Live `/vestacka-trava/` "trava u boji" sekcija (6 slika) je dvostruko potvrđena kao **Edel Grass B.V.** (filename `EG-Colourful-*`, Edel Grass NIJE na `condor-group.eu/en/group/members` listi, u vlasništvu Oranjewoud grupe) — NE Condor Grass kako je pretpostavljeno. Boje se ni ne poklapaju 1:1 sa lokalnim Condor Schools/Playgrass setom (7 vs 6 boja, delimično preklapanje). #ceka-miroslav: proveriti kod pravog dobavljača poreklo tih live slika pre bilo kakvog daljeg koraka (ne dirati ni live ni lokal do tada). v. Urađeno tabela vrh + [[reference/konkurencija-trziste-analiza]] §4 + [[dnevnik/2026-08-08-condor-trava-variation-slike-i-brend-provera]].
- 🔴 **Customer Match — BLOKIRANO na developer token access level 2026-08-07 (ne na kodu).** Oba koda-bug-a popravljena (LEAD_SENDER, membership_life_span), prazna audience lista uspešno kreirana u pravom nalogu, ali stvaran upload odbijen: `CUSTOMER_NOT_ALLOWLISTED_FOR_THIS_FEATURE` — Basic developer token ne pokriva Customer Match. #ceka-miroslav: zatražiti Standard access u Ads UI → Tools & Settings → API Center (Google odobrava ručno, 1–3 dana) — tek onda ponovo probati `customer_match_upload.py --confirm`, `leads.csv` (6 kontakata) već čeka. V. Urađeno tabela vrh + [[reference/naucene-lekcije]].
- 🆕 **ECOTILE dnevni budžet — spike-dani gube 50% prikaza, odluka o povećanju čeka Miroslava (2026-08-06).** Uzrok CPC rasta (52,20→78,98 RSD) nađen: budžet 1.300 RSD/dan dovoljan prosečno, ali na 2/12 dana potrošen pre kraja dana (`search_budget_lost_impression_share` 50%). Nije nalog-širok throttling kao jun (Terase zdrava paralelno). #ceka-miroslav: povećati na 1.800–2.000 RSD ili prihvatiti povremeni gubitak. v. Urađeno tabela vrh + [[dnevnik/ADS-DNEVNIK]].
- 🆕 **Ads kumulativ konverzija dostigao prag 20–30 (2026-08-06: 24) — odluka o Maximize Conversions čeka Miroslava (zadatak 4.8).** v. Urađeno tabela vrh + [[dnevnik/ADS-DNEVNIK]].
- 🔴 **NOVO 2026-08-04 — kontakt forma na live `/kontakt/` tiho odbija validne unose (Firma/Ime, Kontakt telefon).** Potvrđeno uživo testom: `zn_validate_is_letters_ws` prihvata samo ASCII slova (odbija brojeve/tačke/dijakritiku/ćirilicu u nazivu firme), `zn_validate_is_numeric` odbacuje razmake u telefonu (a broj je svuda na sajtu ispisan baš sa razmacima). Kod pada validacije: crvena ivica, 0 AJAX poziva, nema poruke — korisnik ne zna zašto ništa ne radi. Nije nova regresija (JS nepromenjen), verovatno dugogodišnji baseline gubitak submit-ova. #ceka-miroslav: odobrenje za cpanel-live fix (ublažiti regex + dodati vidljivu poruku greške). Detalji: [[DNEVNIK-NAPRETKA]] 2026-08-04.
- 🆕 **F2.9 rep — „40 proizvoda bez slike": 26/40 sada ima sliku, 14 #ceka-miroslav (ažurirano 2026-07-30).** Pregledano po linijama (Bergo, Geoplast, Condor/Radici, R-Tile, sportska oprema); svaki preostali „ne" ima proveren razlog, ništa forsirano. **2026-07-30: +14 dobilo sliku sa zvaničnog sajta proizvođača** (Bergo Flooring, Geoplast, Radici Sport×2, Ecotile×5, Heskins — v. [[DNEVNIK-NAPRETKA]] 2026-07-30 za spisak i izvore) — uklonjeno iz spiska ispod: `16800`, `16836`, `16842`, `16907`, `16908`, `16910`, `16894`, `16895`, `16922`, `16929`, `16930`, `16939`, `16943`, `16949`. Spisak 14 koja i dalje čekaju pravu fotografiju:
  - **0 fotografija u celoj arhivi + web pretraga nije dala pouzdan proizvođački pogodak** — `16893` Condor shock-pad (condor-group.eu ima samo generičku fotografiju terena, ne izolovan snimak materijala) · 5× Radici tehnička trava BEZ specifičnog imena modela (`16899` rugbi, `16900` golf, `16901` hokej, `16902` Multisport MX, `16906` pejzažne površine — radicisport.it kategorije su JS-renderovane, generičko ime u katalogu ne mapira pouzdano na konkretan model, isti rizik kao poznat "Highlands" slučaj) · `16990` Tribina · `16991` Stolica za tribine · `16998` Go za mali fudbal · `17001`/`17002`/`17003` mreže tenis/padel/koš (hoopncourt.com proveren 2026-07-30, potvrđeno NE prodaje ovu opremu — nema poznatog dobavljača u katalogu, nasumičan izbor sa generičkog sajta bi rizikovao tuđ proizvod) · `16919` Expona Living Clic (poznato od ranije, objectflor.de floor-finder 404 za ovu liniju, i dalje čeka distributera)
  - **Dodatno, van brojača od 40** (nisu roditelj-proizvodi): 14 pojedinačnih Condor Schools/Playgrass varijacija boje (`16878`–`16884`, `16886`–`16892`) — roditelji imaju sliku (plava), pojedinačne boje ne
  - #ceka-miroslav: fotografije za gornji spisak (naročito sportska oprema — koji je pravi dobavljač?), ili svesna odluka da neki od njih ostanu bez slike trajno.
- 🆕 **W7 F3 — brisanje starih menija i dalje čeka M**: nov meni je term **390**; stari **67** („O firmi", 39 stavki) namerno ostaje kao rollback dok M ne potvrdi da navigacija radi kako treba (term **28** i 10 praznih Porto menija su već obrisani 2026-07-30, v. dole).
- 🆕 **W7 F2.8 — mapiranje veštačke trave i dalje čeka M (2026-07-29, nepromenjeno)**: 4 modela na `16673` (Highlands/Nature/Put/Springgrass) su **Condor Grass dekorativni modeli za koje u katalogu NE postoji nijedan proizvod**; u katalogu su `Condor Schools`/`Condor Playgrass` (trava u boji za igrališta, druga namena) i `Radici Landscape` (pejzažne površine). Kartice zato vode na kategoriju. Pitanje: napraviti 4 proizvoda, ili vezati za `Radici Landscape`? (AMSS logo stavka uklonjena odavde 2026-08-06 — zatvorena 2026-07-30, v. red iznad.)
- 🟡 **P3 metadesc prenos i dalje otvoren** — cPanel sesija nema mrežni pristup Miroslavljevoj lokalnoj XAMPP bazi, pa je preneto samo `_yoast_wpseo_title` za 2699/4318/1094, ne i `_yoast_wpseo_metadesc`. #ceka-miroslav: proslediti tačan metadesc tekst za sve tri (uključujući ispravku `072 234 00 72`→`069 234 00 72` na spoljnje-podne-obloge) ili čekati migraciju **24.08**.
- 🟡 **GMB API kvota — forma podneta 2026-07-30, i dalje 429 (Google revizija u toku).** M potvrdio da je popunio `support.google.com/business/contact/api_default` → "Application for Basic API Access". Retest 2026-07-30 (`gmb_report.py --from 2026-07-23 --to 2026-07-29`): i dalje `429 Quota exceeded`. Nije greška — Google-ova ručna revizija obično traje nekoliko dana. Probati ponovo za par dana, ne ranije. Windsor.ai se više ne koristi (istekao). Detalji: [[DNEVNIK-NAPRETKA]], [[reference/api-konektor-setup.md]]

- 🟡 **GTM Preview test (W5 5.6) i dalje čeka — sada iz suprotnog razloga (ažurirano 2026-08-13).** Stara formulacija („staging nema NIKAKAV GTM kod", nalaz 2026-07-23) je **prevaziđena**: staging je 13.08 ponovo postavljen iz aktuelnog builda, koji GTM/consent mu-plugin **ima**. Ali je isti taj mu-plugin te večeri **namerno ugašen** (`al-tracking-gtm-consent.php.off`, M odluka) da klijentov pregled ne bi ulazio u GA4/Ads kao stvaran podatak. Dakle `gallery_view`/`pdf_download` (DRAFT u GTM Workspace-u od 22.07) i dalje se ne mogu testirati, a Submit čeka test. Kad zatreba: `mv …php.off …php` na stagingu, test, pa vratiti — bez novog paketa. → [[dnevnik/2026-08-13-staging-v4-puno-postavljanje]]

- 🟡 **W5 5.6 GTM staging Preview test — sesija pauzirana 2026-07-22, Miroslav ide na cPanel.** Treba Basic Auth lozinka za `staging.antasline.com` (`~/staging-htaccess-creds.txt` na serveru, korisničko ime `stagingtest`) — lokalna sesija ne može SSH direktno na `wp1.oblak.host` (poznat port 22 timeout). Miroslav ili prosledi lozinku ovde, ili izvrši test direktno kroz cPanel-live sesiju. Detalji: [[DNEVNIK-NAPRETKA]]
- 🔴 **LiteSpeed image optimization — hosting odgovorio 2026-07-30: NEĆE pustiti zbog bezbednosnih napada.** M poslao tiket, oblak.host odgovorio da QUIC.cloud/image optimization drže zatvorenim zbog nekih hakerskih napada (šira mera predostrožnosti na nivou hostinga, ne specifično naš nalog). Ovo je sad **poznato spoljno ograničenje, ne otvoren zadatak** — LCP gate ostaje crveno dok hosting sam ne odluči da otvori port/servis, nema dalje akcije na našoj strani. Detalji: [[DNEVNIK-NAPRETKA]] 2026-07-10 [cpanel-live], ažurirano 2026-07-30. 🟡 **Napomena 2026-08-07**: UCSS (odvojena LiteSpeed funkcija, ista cloud infrastruktura) je tiho prestala da generiše fajlove istog dana (07-31) kad je stigao ovaj hosting odgovor — nije bilo jasno da li je to isti blok ili nusefekat. Ponovo uključena i testirana ovom sesijom, v. red iznad u Urađeno tabeli za rezultat.

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
