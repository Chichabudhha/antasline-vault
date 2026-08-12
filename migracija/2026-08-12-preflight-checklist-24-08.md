# PRE-FLIGHT CHECKLIST — migracija 2026-08-24

> Izvor: `agy` (Gemini 3.6 Flash) pročitao svih 87 .md fajlova iz `dnevnik/` i
> `migracija/` (2026-08-12). Sirov izlaz: `migracija/preflight.txt` (ispisan
> dvaput zbog TUI redraw-a). Ovaj fajl je očišćena, deduplikovana verzija.
> Provera i napomene na kraju: `[claude-code]`.

---

## Tabela rizika

| # | Rizik | Šta je puklo / šta se lako zaboravi | Kako se sprečava | Izvor |
|---|---|---|---|---|
| 1 | 🔴 | `al-local-mail-log.php` se prenese u `mu-plugins/` i **blokira sve forme** — presreće `wp_mail`, vraća uspeh bez slanja. Već se desilo 07.08 na staging V3: forme radile, niko nije primao mejlove. Komentar u samom fajlu da se mu-plugins ne prenosi je **netačan**. | Proveriti `wp-content/mu-plugins/` pre pakovanja, ukloniti `al-local-mail-log.php` + `mail-log.txt`. `build-staging-package.sh` ovo izbacuje automatski. | `migracija/2026-08-10-pre-migration-checklist.md` |
| 2 | 🔴 | Sintaksna greška u `.htaccess` obara **ceo sajt** (HTTP 500) — izvršava se pre WP-a. Stari `Redirect` sa prefix-match pravio kolizije na 15 pravila. | Generisati isključivo skriptom `htaccess-301-generate.php`; sidreni `RedirectMatch 301 "^/put/?$"`; iznad `# BEGIN WordPress`; testirati izolovano pre objave. | `dnevnik/2026-08-11-htaccess-301-reverifikacija.md` |
| 3 | 🔴 | **62 istorijska 301 pravila nestaju zamenom baze** — živela su u `Redirection` pluginu u produkcijskoj bazi. Preko **46.000 GSC pogodaka** bi palo na 404. | Svih 62 + 14 novih FINAL objedinjeno direktno u `.htaccess` (ukupno 73 pravila). | `dnevnik/2026-08-11-htaccess-301-reverifikacija.md`, `migracija/2026-07-21-analiza-65-redirection-pravila.md` |
| 4 | 🔴 | **27 backup fajlova** (`*.bak-*`, `*.old`, `*.orig`) u `wp-content` — Apache ih servira kao čist tekst. `functions.php.bak-…` → HTTP 200, 53 KB izvornog koda, otkriva logiku tokena, honeypot-a i rate-limita. | `find wp-content -name "*.bak*" -o -name "mail-log.txt"` i obrisati sve pre slanja. | `migracija/2026-08-10-pre-migration-checklist.md` |
| 5 | 🔴 | FTP upload paketa od **3.18 GB pukao usred prenosa** (`ftp-upload-chunks.sh`); FTP nalog `staging@` bio suspendovan ili pogrešno podešen. | Ne slati velike arhive preko FTP-a — presecati na manje celine ili ići preko SSH/rsync/cPanel File Manager-a. | `migracija/2026-08-06-prompt-staging-refresh.md` |
| 6 | 🔴 | `mysql -e` na Windows shell-u **uništava UTF-8 dijakritiku i ćirilicu** → mojibake/upitnici u bazi. | Izmene u bazi isključivo kroz PHP skripte ili WP-CLI sa eksplicitnim UTF-8 kontekstom. Nikad sirov `mysql -e` na Windows-u. | `dnevnik/2026-08-11-metadesc-6-stranica.md`, `dnevnik/2026-08-11-rollback-plan-i-dijakritika.md` |
| 7 | 🔴 | **FAQ JSON-LD ispisan kao vidljiv tekst** na `/planer-terena/` — `wp_insert_post()` bez ulogovanog korisnika pušta sadržaj kroz `kses` i briše `<script>`. | `<script>` u `post_content` upisivati **isključivo** preko `$wpdb->update()`, nikad `wp_insert_post()` iz CLI. | `migracija/w1-novi-proizvodi-court-builder.md`, `migracija/woodmart-sabloni.md` |
| 8 | 🔴 | **Trostruko dupliranje `generate_lead`** — live Kallyas ima dva GTM embed-a, ~3,9 eventa po sesiji. Posle migracije dupli embed nestaje → GA4 konverzije padaju ~70% (sa ~40 na ~12). | Obavestiti sve aktere **unapred** da to nije pad prodaje nego uklanjanje dupliranja. | `dnevnik/2026-08-11-generate-lead-inflacija-dijagnoza.md`, `dnevnik/2026-08-11-w5-nedeljni-izvestaj.md` |
| 9 | 🟠 | Prefiks baze je **`wpgs_` malim slovima**, ne `wpGs_`. Pogrešan case tiho sprečava zamenu prefiksa. | Pri `wp search-replace` i importu uvek `wpgs_`. | `migracija/2026-08-10-pre-migration-checklist.md`, `migracija/2026-08-06-prompt-staging-full-restore.md` |
| 10 | 🟠 | `sed` zamena prefiksa **zavisi od backtick-ova** oko imena tabela. Ako dump nema backtick-ove, `sed` tiho preskoči zamenu i import ode u pogrešne tabele. | Pre `sed`-a pročitati prve linije SQL dump-a i potvrditi backtick-ove. | `migracija/2026-08-06-prompt-staging-full-restore.md` |
| 11 | 🟠 | Rank Math sitemap index **ne prikazuje nove taksonomije** bez invalidacije keša — brisanje opcije i fajlova sa diska nije dovoljno. | `\RankMath\Sitemap\Cache::invalidate_storage()` preko WP-CLI odmah po prebacivanju baze. | `dnevnik/2026-08-12-product-brand-arhive.md`, `dnevnik/2026-08-11-gsc-priprema-sitemap.md` |
| 12 | 🟠 | **Google OAuth token pada svakih 5–7 dana** — GCP app je u statusu *Testing* → `invalid_grant`, konektor mrtav baš kad treba. | Pokrenuti `ads_report.py` **ujutru na dan migracije**; po potrebi `authorize_oauth.py` kroz pregledač. | `dnevnik/2026-08-11-ads-final-url-audit.md` |
| 13 | 🟠 | Oglasi u pauziranim kampanjama vode na **nepostojeće URL-ove i tuđi domen** (`ekopodneploce.rs`) — 6 URL-ova za prepis, 2 oglasa na eksterni domen. | Ne reaktivirati kampanje pre izmena po `analiza/2026-08-11-ads-url-audit.csv`. | `migracija/2026-08-11-ads-final-url-audit.md` |
| 14 | 🟠 | **2× Product JSON-LD** pri unosu cene — aktivirali se i Yoast i W2 2.7 hook. | Sanirano u `functions.php`; proveriti schema izlaz posle migracije. | `migracija/w1-novi-proizvodi-court-builder.md` |
| 15 | 🟡 | WoodMart bezuslovno učitava **437 KB WPBakery CSS-a** i renderuje `post_title` kao H1 → **2× H1** na stranicama sa sopstvenim H1. | `al-asset-diet.php` za dequeue; proveriti `_woodmart_title_off=on`. | `dnevnik/2026-08-11-dijeta-asseta-tema.md`, `migracija/woodmart-sabloni.md` |
| 16 | 🟡 | **Prazan `post_content`** na starim ZionBuilder stranicama (npr. post 6588) — sadržaj je u `zn_page_builder_els` postmeta. | Pre izmena proveriti `CHAR_LENGTH(post_content)=0`. | `migracija/w1-polish-red-cekanja.md` |
| 17 | 🟡 | Slomljene slike sa sufiksom `-1` na reimportovanim postovima (`-1024x678` → `-1-1024x678`) → 404. | `al_fix_missing_sizes.php`, `al_scan_lost_originals.php`. | `migracija/w1-polish-red-cekanja.md` |
| 18 | 🟡 | cPanel `lsws redisAble` iz terminala blokiran ("Parent check method… is not allowed"). | Ne pokušavati Redis iz terminala; anonimne posete pokriva LiteSpeed page-cache. | `dnevnik/2026-08-11-litespeed-redis-web-cache-manager.md` |
| 19 | 🟡 | Customer Match upload pao — `CUSTOMER_NOT_ALLOWLISTED_FOR_THIS_FEATURE`. | Ne tražiti Standard access, ne koristiti `OfflineUserDataJobService`; ići preko Data Manager API-ja. | `dnevnik/2026-08-11-customer-match-data-manager-api.md` |
| 20 | 🔴 | **Disk kvota na live serveru — 2,43 GB slobodno, migracija treba ~2,6 GB istovremeno** (novi wp-content paket ~1,3 GB + svež backup ~1,3 GB). Uzrok: `~/staging/` **3,4 GB** leftover od probne migracije 06–07.08 (nikad obrisan), `~/antasline-vault/antasline-backups/` 539 MB. | Obrisati `~/staging/` pre 24.08 (M odluka) — samo to rešava prostor sa rezervom. Ako se ne obriše, backup mora ići pull-pa-obriši sekvenca, ne oba istovremeno na disku. | `[cpanel-live]` pre-flight 2026-08-12, `DNEVNIK-NAPRETKA.md` |
| 21 | ⚪ | **JetBackup snapshot status (poslednji datum/retencija/off-site) nedostupan iz shell-a** — `uapi Backup` nema feature, `JetBackup5::wrapper` nedokumentovan. Checklist B1 pretpostavlja "snapshot <24h" kao proveriv korak, a nije proveriv sa ovog naloga. | Proveriti kroz WHM/cPanel UI (Backup Wizard/JetBackup stranica) pre 21.08 gate pregleda. | `[cpanel-live]` pre-flight 2026-08-12 |

---

## Ručne radnje NA DAN migracije

1. **Go/no-go:** potvrditi da Miroslav ima najmanje **6h bez prekida**. Rollback zavisi isključivo od njega — bez njegovog prisustva se ne počinje.
2. **Svež backup produkcije:** cPanel → JetBackup 5, snapshot ne stariji od 24h + ručni DB dump i arhiva `wp-content` neposredno pre izmena.
3. **OAuth provera:** pokrenuti `ads_report.py`. Ako vrati `invalid_grant` → `authorize_oauth.py` kroz pregledač, pre bilo kakvog rada na oglasima.
4. **Staging paket bez dev fajlova:** `build-staging-package.sh full`, pa proveriti da `al-local-mail-log.php`, `mail-log.txt` i `*.bak-*` **nisu** u arhivi.
5. **Uvoz baze + search-replace:** paziti na stvarni prefiks `wpgs_` (malim slovima).
6. **Aktivacija `.htaccess` 301 bloka:** pravila iz `htaccess-301-DRAFT.txt` na sam vrh (iznad `# BEGIN WordPress`), pa testirati 5 top GSC URL-ova: `/sportski-podovi/`, `/izgrdanja-sportskig-terena/`, `/podovi-za-baste-splavove-bazene/`, `/home/industrijski-podovi/ecotile-5007/`, `/бренд/ecotile/`.
7. **LSCWP:** `wp litespeed-purge all` + regeneracija Critical CSS / UCSS.
8. **SMTP test:** stvarni submit na kontakt formi (16593) i "Brzom upitu" (16737); potvrditi dolazak u `office@antasline.com`.
9. **GTM + Enhanced Conversions:** CJS i User-Provided Data promenljive, `customer data` na tagu 20 (Lead - forma (GTM)), ukloniti stari Zion tag/trigger → Submit, Publish, Preview.
10. **Ads Final URL fix (4.10):** zameniti nevažeće odredišne URL-ove po `analiza/2026-08-11-ads-url-audit.csv`.
11. **Regression sweep:** `scratchpad/regression-sweep.php` prema produkciji, uporediti sa baseline-om od 10.08.

---

## Konflikti u dokumentaciji

| # | Tema | Izvor A (zastareo/sporan) | Izvor B (noviji) |
|---|---|---|---|
| 1 | SEO plugin | `odluke/_pregled-odluka.md` (28.06), `migracija/2026-07-28-W7-sanacija-builda.md`: **Yoast**, Rank Math zabranjen | `dnevnik/2026-08-11-gsc-priprema-sitemap.md`, `2026-08-12-product-brand-arhive.md`: migracija na **Rank Math** izvedena 05.08, Yoast deaktiviran |
| 2 | Datum go-live | `migracija/w1-novi-proizvodi-court-builder.md`: **31.08** | `migracija/2026-08-10-pre-migration-checklist.md`: **24.08** (freeze 16.08, gate 21.08) |
| 3 | Prefiks baze | `migracija/2026-07-05-live-export-prompt.md`, `PARITY-PLAN.md`: **`wpGs_`** | `2026-08-10-pre-migration-checklist.md`, `2026-08-06-prompt-staging-full-restore.md`: stvarno **`wpgs_`** |
| 4 | Sloj 301 | `migracija/2026-07-21-analiza-65-redirection-pravila.md`: u **Redirection pluginu** | `dnevnik/2026-08-11-htaccess-301-reverifikacija.md`: prebačeno u **`.htaccess`** |
| 5 | `mu-plugins` prenos | komentar u `al-local-mail-log.php`: ne prenosi se | `2026-08-10-pre-migration-checklist.md`: **prenosi se** sa `wp-content` — 07.08 blokiralo mejlove |
| 6 | Zbir konverzija | `dnevnik/ADS-DNEVNIK.md`: **26** plaćenih, prag skoro pređen | `2026-08-11-snapshot-jul.md`, `2026-08-11-w5-nedeljni-izvestaj.md`: 17/26 bili `tel` klikovi → **samo 9 pravih formi**, prag NIJE pređen |

---

## Napomene `[claude-code]` — provera i dopune

**🔴 `CLAUDE.md` je na pogrešnoj strani konflikta #3.** Sekcije §2 i §7.5 tvrde prefiks
**`wpGs_`**. Po ovom nalazu stvarni prefiks je `wpgs_` malim slovima. Pošto svaki
agent (i ja) čita `CLAUDE.md` kao autoritet, ovo je aktivna mina — treba ispraviti
u `CLAUDE.md` ili eksplicitno potvrditi protiv same baze pre 24.08.

**Konflikt #6 poništava odluku o Maximize Conversions.** Prag 20–30 nije dostignut
(9, ne 26). Ne prelaziti na Maximize Conversions. Isto vredi za `odluke/`
stavku 4.8 — treba je zatvoriti kao "odloženo, prag nije dostignut".

**Nije u ovom spisku, a jeste rizik od gubitka podataka:** `live-export.sh:24-36`
skuplja attachmente preko `post_parent` i `_thumbnail_id`, ali **nikad ne čita
`_product_image_gallery`** — komentar na liniji 25 kaže "thumbnail + galerija",
kod galeriju ne dodiruje. Galerijske slike bez `post_parent` veze tiho nestaju
pri exportu. Verifikovano u kodu 2026-08-12. (Nalaz iz zasebnog `agy` audita
vault-a, `analiza_vaulta_i_mana.md`.)

**Stavka 12 (OAuth) je jedina koja može da obori i sam checklist** — ako konektor
padne na dan migracije, tačke 3 i 10 ostaju neizvodljive. Trajno rešenje je
prebaciti GCP app iz *Testing* u *Published*, ne pokretati `authorize_oauth.py`
u panici na dan migracije.
