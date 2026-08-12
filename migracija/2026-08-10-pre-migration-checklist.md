---
tip: checklist
naziv: Pre-migration checklist — dan migracije (W3 3.10)
datum: 2026-08-10
go-live: 2026-08-24
status: aktivan — popunjava se do 21.08 (gate), izvršava 24.08
---

# ✅ Pre-migration checklist — PON 2026-08-24

Deo zadatka W3 3.10 iz [[2026-07-06-MASTER-PLAN-V2]]. Druga polovina 3.10 je
**full regression** (rezultati: [[dnevnik/2026-08-10-w3-310-full-regression]]).

> **Redosled je namerno ovakav:** sve što se može uraditi RANIJE (do 21.08) je
> odvojeno od onoga što se MORA na dan migracije. Na dan migracije se ne
> improvizuje.

---

## A. DO PET 21.08 (gate pregled) — može ranije, ne mora čekati

- [x] ✅ **Svež backup live sajta (db + wp-content) na 2 lokacije — ZATVORENO
      `[cpanel-live]` 2026-08-11.** Ručan DB dump + `wp-content` tar.gz, MD5
      provereno, skinuto na `C:\Miroslav\Antas line\Backup` + `G:\AntasLine-Backups`,
      server-kopije obrisane. Gate stavka zadovoljena — **B1 ispod i dalje traži
      NOVI backup na sam dan migracije** (24.08), ovaj ne zamenjuje taj korak.
- [ ] **Backup finalnog lokalnog builda** na 2 lokacije — 🔴 poznato ograničenje:
      `nocni-backup.ps1` piše na SAMO JEDNU destinaciju (prioritet G:→OneDrive→lokalno).
      Za „2 lokacije" treba ručna druga kopija na dan zamrzavanja builda.
- [x] ✅ **Rollback plan ZATVOREN 2026-08-11** (pre roka 15.08) — sva 3 pitanja:
      JetBackup 5 dnevni/off-site/90 dana · **nema CDN/edge sloja** (samo LiteSpeed,
      pa je „očisti keš" = LSCWP Purge All i ništa više) · M odluka **„migracija
      samo kad sam tu"** → [[migracija/rollback-plan]]
      🔴 **Nosi nov preduslov u B1 ispod** — v. „Dostupnost" stavka.
- [ ] 🆕 **Baseline: Generative AI performance report (GSC)** — očitati prikaze
      **po stranicama** pre migracije i snimiti u `analiza/`. Razlog: izveštaj
      je vezan za URL-ove, a migracija ih menja; bez baseline-a se posle live-a
      ne može odgovoriti „da li smo izgubili AI vidljivost". UI-only (API ga ne
      izlaže) → CC preko browsera ili M ručno. Kontrola *Search generative AI*
      je potvrđeno na „Include" (2026-08-12). v. [[seo/geo-ai-plan]] §0.1
- [ ] **Enhanced Conversions — Ads UI** (M, 5 min): Goals → Conversions →
      „Lead - forma (GTM)" → Settings → Enhanced conversions → uključiti, metod
      **Google Tag Manager**, prihvatiti customer data terms.
- [x] ✅ **Final URL audit oglasa — ZATVOREN 2026-08-11.** 41 URL (Ads API + GA4):
      `OK` 32 · `PREPISATI` 6 · `EKSTERNI-DOMEN` 2 · `PUKAO` 1 (artefakt) ·
      `REDIRECT-BUILD` 0. Spisak: `analiza/2026-08-11-ads-url-audit.csv`.
      🟢 **Za dan migracije nema posla:** jedina ENABLED kampanja („ECOTILE
      INDUSTRIJSKI PODOVI", 1 RSA + 6 sitelinkova) ima svih 7 URL-ova na 200.
      🔴 **Pre reaktivacije pauziranih kampanja** (uklj. W4 4.4): 6 URL-ova za
      prepis + 2 koja vode na tuđi domen `ekopodneploce.rs` (v. Blokeri u
      [[PROGRESS]]). 🟢 Izmereno da `?gclid=` **preživljava** 301.
      → [[migracija/2026-08-11-ads-final-url-audit]]
- [x] ✅ **`.htaccess` 301 — reverifikovan i REGENERISAN 2026-08-11.** Draft je bio
      **8 pravila, sada 73** (14 FINAL + 59 istorijskih). 🔴 62 istorijska pravila
      (Redirection plugin, ~46.000 GSC pogodaka) nisu bila u draftu, a nestaju sa
      bazom pri migraciji · razrešena petlja između dve mape · 2 pravila koja bi
      ubila stranice 16686/16875 izbačena · `Redirect` (prefiks-match, 15 kolizija)
      zamenjen sidrenim `RedirectMatch "^/put/?$"`. Draft se **generiše skriptom**
      `migracija/alati/htaccess-301-generate.php` (odbija upis ako ijedan cilj nije
      200) — ne pisati ga ručno. → [[dnevnik/2026-08-11-htaccess-301-reverifikacija]]
      🔴 NE aktivirati pre dana migracije.
      ⚠️ **Ako se do 24.08 promeni ijedan slug** — pustiti `redirect-verify.php` pa
      `htaccess-301-generate.php` ponovo.
- [x] ✅ **GSC priprema — ZATVORENA 2026-08-11 (CC deo).** 🟢 **URL za resubmit je
      nepromenjen**: `https://www.antasline.com/sitemap_index.xml`, i child-ovi
      nose identična imena fajlova kao Yoast na live-u → nijedan submit-ovan URL
      ne puca migracijom. 🔴 Usput nađena rupa: build je emitovao **3 sitemap-a
      gde live emituje 7** — Yoast→Rank Math import (05.08) nije preneo
      taksonomijska podešavanja, svih 12 `tax_*_sitemap` ključeva je bilo `off`.
      Pogađalo je 27 URL-ova sa **79 klikova / 2.583 prikaza** (GSC, 3 mes.),
      najjači `/category/industrijski-podovi/` (44 kl.). Uključeno `category` +
      `product_cat` + `product_tag` → sitemap 196 → **236 URL-ova**, svih 42
      novih verifikovano 200/1×H1/`index`. Brend sitemap namerno **ostaje off**
      (arhive prazne — v. B3 napomenu ispod). Nov read-only alat:
      `scripts/gsc_sitemaps.py`. → [[dnevnik/2026-08-11-gsc-priprema-sitemap]]
      **#ceka-miroslav (3 koraka u GSC UI, nijedan ne blokira migraciju):**
      - [ ] obrisati zastareo **`http://`** sitemap unos (submit-ovan 2018-04-09,
            Google ga i dalje povlači — poslednji put 2026-08-10)
      - [ ] pogledati **3+4 upozorenja** na oba sitemap-a (API vraća samo brojač)
      - [ ] potvrditi **email alerte**: profilni meni → *Search Console
            preferences* → notifikacije uključene. Na dan migracije je alert o
            skoku indexing grešaka prvi signal da 301 blok nije proradio.

---

## B. DAN MIGRACIJE (24.08) — redosled izvršenja

### B1. Pre prebacivanja
- [ ] 🔴 **DOSTUPNOST (prva stavka dana, M odluka 2026-08-11): ne pokretati
      migraciju ako Miroslav nema ~6h slobodnih ispred sebe.** Rollback plan
      nema rezervnog izvršioca — samo on ima cPanel/SSH i samo on donosi
      go/no-go. Rollback budžet je 35–50 min, ali tek posle dijagnostike i
      odluke. Kasno popodne/veče nije prihvatljiv start; ako tog dana nema
      prozora, **migracija se pomera** (isto pravilo kao gate).
- [ ] Provera u cPanel → JetBackup da poslednji automatski snapshot **nije
      stariji od 24h** — besplatan drugi primerak pored ručnog backup-a.
- [ ] Backup live sajta ponovo, neposredno pre dodira (db + wp-content), datiran.
- [ ] Potvrditi da je lokalni build **zamrznut** (nema izmena posle 16.08 freeze-a).
- [ ] 🔴 **Provera da je konektor OAuth token živ** (`ads_report.py --from … --to …`
      mora vratiti JSON, ne `invalid_grant`). Token pada za ~7 dana dok je consent
      screen u *Testing* statusu — bez njega ne rade ni 4.10 ni verifikacija
      konverzija u B6. Ako pukne: `authorize_oauth.py` (browser, 1 min).
- [ ] Napraviti paket: `migracija/alati/build-staging-package.sh full`
      🟢 Skripta od 2026-08-10 sama izbacuje lokalne artefakte (v. B2) — ne
      oslanjati se na ručno brisanje.

### B2. Fajlovi koji NE SMEJU na produkciju
> 🟢 **Sva tri su od 2026-08-10 pokrivena exclude pravilima u
> `build-staging-package.sh`** — checklist ostaje kao dvostruka provera, jer je
> `al-local-mail-log.php` već jednom (07.08, staging V3) prošao u paket i forme
> na staging-u tada nisu stvarno slale mejlove.

- [ ] `wp-content/mu-plugins/al-local-mail-log.php` — presreće **sve** `wp_mail`
      pozive i vraća „uspeh". Ako ode na produkciju, forme rade naizgled savršeno
      a **nijedan upit ne stiže**. Ostaje na lokalu (tamo je i dalje potreban).
      ⚠️ Komentar u samom fajlu tvrdi „mu-plugins se ne prenose" — **netačno**,
      prenose se sa `wp-content`. To je i bio uzrok 07.08 slučaja.
- [ ] `wp-content/mail-log.txt`
- [ ] `al-harness.html` (docroot) — vizuelni harness 1500/390px, alat za lokalni rad.
      🟢 Dvostruko pokriven: root whitelist ga ionako ne uključuje.
- [ ] **`*.bak-*` / `*.orig` / `*.old` / `*~` u `wp-content`** — izmereno 10.08:
      **27 fajlova**. Apache ih servira kao čist tekst (potvrđeno: `functions.php.bak-…`
      → HTTP 200, 53 KB PHP izvora). Nema kredencijala u njima, ali otkrivaju logiku
      court-builder tokena, honeypota i rate-limita.
- [ ] Provera posle raspakivanja na serveru:
      `find wp-content -name "*.bak*" -o -name "mail-log.txt" | head` → mora biti prazno.

### B3. Prebacivanje
- [ ] db + wp-content na produkciju
- [ ] URL zamena (`wp search-replace`), prefiks 🔴 **`wpgs_` malim slovom** — ne
      `wpGs_` kako dokumentacija na više mesta piše (potvrđeno 06.08 u dump-u)
- [ ] `wp rewrite flush --hard`
- [ ] Aktivirati `.htaccess` 301 blok — sadržaj `htaccess-301-DRAFT.txt`, **iznad
      `# BEGIN WordPress` bloka**. `RewriteBase` nije potreban (mod_alias, ne
      mod_rewrite). Posle aktivacije: spot-check 5 pravila sa najviše GSC pogodaka
      (`/sportski-podovi/`, `/izgrdanja-sportskig-terena/`,
      `/podovi-za-baste-splavove-bazene/`, `/home/industrijski-podovi/ecotile-5007/`,
      `/бренд/ecotile/`) — svako mora dati 301 na tačan `Location`.
      🟢 **2026-08-12: `/бренд/*` ciljevi REŠENI (M odluka, opcija a)** — Ecotile
      arhiva ima 7, Ergomat 27 proizvoda, obe 200 / 1×H1 / `index, follow` sa
      pravim title/meta. Ranije upozorenje („spot-check prolazi a stranica je
      prazna") više ne važi. Draft se **ne regeneriše** — ciljevi su nepromenjeni.
      🔴 Pouka za generator ostaje: `htaccess-301-generate.php` proverava samo da
      cilj vraća 200 — prazna WooCommerce arhiva to zadovoljava, pa 200 nije dokaz
      da je cilj koristan. v. [[dnevnik/2026-08-12-product-brand-arhive]]
- [ ] `wp litespeed-purge all` + regenerisati Critical CSS/UCSS

### B4. SMTP / forme — 🔴 prvo posle prebacivanja
- [ ] Potvrditi da produkcija **stvarno šalje mejl** (ne samo da forma prikazuje
      uspeh): pravi submit → proveriti `office@antasline.com` inbox.
- [ ] Obe forme: kontakt (16593) i „Brzi upit" (16737).
- [ ] Court builder mejl klijentu (PNG+PDF) — do sada je lokalno uvek išao kroz
      mail-log, **nikad nije poslat pravim SMTP-om**. Ovo je prvi pravi test.

### B5. GTM paket (po [[migracija/2026-08-09-enhanced-conversions-4.7]] §2)
- [ ] Dodati 2 Custom JavaScript promenljive (`CJS - Lead Email/Phone (sessionStorage)`)
- [ ] Dodati User-Provided Data promenljivu (Manual configuration)
- [ ] Na tagu „Lead - forma (GTM)" (`tag_id 20`, label `ae_gCKL-3sAcEKCi_cwD`)
      uključiti „Include user-provided data" 🔴 **NE dirati tag za telefon** (`tag_id 21`)
- [ ] Obrisati tag `Meta Pixel - Capture Lead Data` + trigger `Klik na Posalji (Zion forma)`
      (mrtva Zion forma). `Meta Pixel - Base Code` ostaje nepromenjen.
- [ ] Submit + Publish, pa GTM Preview na produkciji

### B6. Verifikacija posle prebacivanja
- [ ] Ponoviti **isti regression sweep** protiv produkcije
      (`scratchpad/regression-sweep.php`, promeniti `$BASE`) i uporediti sa
      lokalnim baseline-om od 10.08 — brojevi moraju biti isti ili bolji.
- [ ] `generate_lead` okida na `/hvala-za-poruku/` (GA4 real-time + Ads)
- [ ] Ads konverzija „Lead - forma (GTM)" prima hit
- [ ] Spot-check top 20 GSC URL-ova iz [[analiza/2026-07-21-serp-snapshot-pre-migracija]]
- [ ] 4.10 — ispraviti final URL-ove oglasa po spisku iz A

### B7. Post-live (25.08+)
- [ ] GSC sitemap resubmit — URL **`https://www.antasline.com/sitemap_index.xml`**
      (nepromenjen, v. §A). Pre resubmit-a proveriti da index vraća **7**
      child-ova (`post`/`page`/`product`/`category`/`product_brand`/`product_cat`/
      `product_tag`) i ~**238** URL-ova ukupno (ažurirano 2026-08-12 — brend
      sitemap uključen kad su arhive napunjene). 🔴 Ako ih je manje → Rank Math
      keš sitemap-a nije obrisan pri prebacivanju: obrisati opciju
      `rank_math_sitemap_cache_files` + fajlove
      `wp-content/uploads/rank-math/rank_math_*.xml`, **pa obavezno pozvati
      `\RankMath\Sitemap\Cache::invalidate_storage()`** — bez toga se child fajl
      servira ali ga index ne nabraja (izmereno 2026-08-12).
- [ ] Prored `product_tag` arhiva (18 kom., od toga 10 `namena-*`) — zaseban
      SEO zadatak, namerno odložen posle live-a; na migraciju je išao **parity**,
      ne promena indeksne politike. v. [[dnevnik/2026-08-11-gsc-priprema-sitemap]]
- [ ] UptimeRobot
- [ ] Dnevni 404 log pregled, prvih 14 dana
- [ ] 4.8 Maximize Conversions — **~01.09**, namerno posle slegnjivanja 301
      (v. [[2026-07-06-MASTER-PLAN-V2]] §4)

---

## Veze
[[2026-07-06-MASTER-PLAN-V2]] · [[migracija/rollback-plan]] ·
[[migracija/2026-08-09-enhanced-conversions-4.7]] · [[migracija/PARITY-PLAN]] ·
[[dnevnik/2026-08-10-w3-310-full-regression]]
