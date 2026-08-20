---
tip: checklist
naziv: Pre-migration checklist — dan migracije (W3 3.10)
datum: 2026-08-10
go-live: 2026-09-08
status: aktivan — popunjava se do 04.09 (gate), rezervni dan PON 07.09, izvršava UTO 08.09
azurirano: 2026-08-20 (go-live 25.08 → 08.09, +14 dana, M odluka, razlog nezabeležen)
---

# ✅ Pre-migration checklist — UTO 2026-09-08

Deo zadatka W3 3.10 iz [[2026-07-06-MASTER-PLAN-V2]]. Druga polovina 3.10 je
**full regression** (rezultati: [[dnevnik/2026-08-10-w3-310-full-regression]]).

> 🆕 **2026-08-20 — svi datumi ispod pomereni +14 dana** (M odluka, razlog nezabeležen):
> gate 21.08→**04.09**, freeze 20.08→**03.09**, rezervni dan 24.08→**07.09**, migracija
> 25.08→**08.09**. Sadržaj i checkbox stanja ispod su i dalje tačni — čitaj svaki preostali
> datumski pomen (20.08/21.08/24.08/25.08) sa **+14 dana**, dok se checklist ne prepiše u
> posebnoj sesiji bliže novom gate-u.

> **Redosled je namerno ovakav:** sve što se može uraditi RANIJE (do gate-a) je
> odvojeno od onoga što se MORA na dan migracije. Na dan migracije se ne
> improvizuje.

> 🟢 **2026-08-17 — migracija pomerena PON 24.08 → UTO 25.08** (M odluka; datumi ovog bloka
> su istorijski, v. napomenu 20.08 iznad za važeći raspored). Gate ostaje
> **PET 21.08**, pa je **PON 24.08 od sada rezervni radni dan**:
> - ako gate padne u petak → popravka ide u ponedeljak, datum migracije se **ne** pomera
> - ako je gate čist → ponedeljak se koristi za **pripremu**, tj. one B1 korake koji
>   smeju dan ranije: svež live backup, JetBackup provera, provera OAuth tokena,
>   `build-staging-package.sh full`, postavka `rsync`/SSH prenosa
> 🔴 Šta se **ne sme** pomeriti na ponedeljak: sam prenos, `search-replace`, aktivacija
> `.htaccess` 301 bloka i GTM paket — to je sve utorak, u jednom prozoru, po §B redosledu.

---

## A. DO PET 21.08 (gate pregled) — može ranije, ne mora čekati

- [x] ✅ **Svež backup live sajta (db + wp-content) na 2 lokacije — ZATVORENO
      `[cpanel-live]` 2026-08-11.** Ručan DB dump + `wp-content` tar.gz, MD5
      provereno, skinuto na `C:\Miroslav\Antas line\Backup` + `G:\AntasLine-Backups`,
      server-kopije obrisane. Gate stavka zadovoljena — **B1 ispod i dalje traži
      NOVI backup na sam dan migracije** (25.08), ovaj ne zamenjuje taj korak.
- [x] ✅ **Backup zamrznutog stanja (20.08) na 2 lokacije — IZVRŠEN 2026-08-20 uveče.**
      🔴 Nalaz pre pokretanja: noćni zakazani task je 20.08 03:00 startovao, DB dump uspeo,
      ali proces je prekinut usred zip-a (`LastTaskResult 3221225786`, mašina ugašena/uspavana)
      — 0-bajtni zip, nijedan uspešan backup za 20.08 pre ove sesije. G: disk nije bio prikačen;
      posle prikačivanja ručan `nocni-backup.ps1` run uspeo na obe lokacije u istom prolazu.
      🔵 Ovo NIJE backup „finalnog builda pred migraciju" — go-live je pomeren na 08.09
      (M odluka 20.08), pa pravi finalni backup ide bliže **novom** freeze-u (03.09).
      → [[dnevnik/2026-08-20-potvrdni-sweep-i-backup-posle-freeze]]
      ✅ **Ograničenje „samo jedna destinacija" POPRAVLJENO 2026-08-17.** Do tada je
      `nocni-backup.ps1` pisao na **jednu** destinaciju (G:→OneDrive→lokalno), pa se serija
      razlivala (10–12.08 na `C:`, 13–14.08 na `G:`, OneDrive folder ne postoji) — **nijedan
      datum nije imao dve kopije, iako je to ova gate stavka**. Skripta sada posle uspešnog
      zip-a kopira na drugu destinaciju uz proveru veličine (rotacija 3). Kopija
      `nocni-backup.ps1.bak-2026-08-17`.
      ✅ **Nova logika PROŠLA pun run 2026-08-17 18:08–18:36:** zip 2.810,4 MB na `G:`,
      druga kopija u `antasline-backups/auto/`, oba fajla **identična (2.946.948.322 B)**,
      arhive čitljive (102.488 unosa, dump 71,9 MB unutra), `_tmp` prazan, rotacija 13/3.
      🔵 Taj backup je **bezbednosna kopija tekućeg stanja, ne „finalni build"** (freeze je
      istog dana ponovo otvoren do 20.08) — zato ova stavka i dalje stoji neštriklovana.
- [ ] 🆕 **Posle novog content freeze-a (ČET 20.08) — obaveze koje nosi ponovno otvaranje:**
      - [x] ✅ **IZVRŠEN 2026-08-19, dan PRE freeze-a** (namerno ranije — da ostane vreme za
            popravku pre gate-a 21.08). **235 stranica / 1.174 slike / 1.799 linkova: 0 non-200 ·
            0 bez H1 · 0×2H1 · 0 nevalidnih JSON-LD · 0 slomljenih slika · 0 internih 404.**
            Bez metadesc 18 (bilo 31) — samo `product_tag` arhive, posle live-a. Diff vs 13.08:
            30 URL promena + 18 meta izmena, **sve vezane za dokumentovane odluke, nula
            neplaniranih**. 🔴 **Nov baseline za §B6 je `analiza/2026-08-19-regression-pre-freeze-*`**,
            ne snimak od 13.08. → [[dnevnik/2026-08-19-regression-sweep-pre-freeze]]
      - [x] ✅ **IZVRŠEN 2026-08-20 uveče** (M eksplicitno tražio, ne čekati novi freeze 03.09).
            **236 stranica** (235 + Onda, koja je objavljena 20.08), 0 non-200/h1_0/h1_multi/
            jsonld_bad. Prvi prolaz je lažno prijavio 235/0 razlika — Rank Math sitemap keš je
            bio zastareo, Onda nije bila u sitemap-u (poznat gotcha, v. §7.1 CLAUDE.md);
            posle brisanja keš fajlova drugi prolaz je našao pravi nalaz: mrtav interni link
            Onda→draft Maxionda (404), fixovan (link uklonjen, tekst ostao). Nov baseline za
            §B6: `analiza/2026-08-20-regression-confirmatory-*`. → [[dnevnik/2026-08-20-potvrdni-sweep-i-backup-posle-freeze]]
      - [x] ✅ **301 mapa REVERIFIKOVANA 2026-08-19** (`redirect-verify.php`): **80 pravila ·
            43 jedinstvena cilja svi 200 · 0 duplikata izvora · 0 petlji · 0 kolizija sa živim
            stranicama.** Upozorenje za 16613 iz provere 13.08 više se ne javlja (stranica
            draftovana, slug swap izveden) — stanje ispravnije nego tada. Uslovni izuzetak
            provereni u bazi: **16875/16874/17273 su i dalje `draft`**, pa pravilo
            `/podovi-za-garaze/` ispravno stoji. **Draft se NE regeneriše.**
            🔴 Ponoviti samo ako se 20.08 promeni ijedan slug ili neka draftovana stranica
            vrati u `publish`.
      - [x] ✅ **nov backup zamrznutog builda na 2 lokacije** (v. stavka iznad, ista sesija)
      🔴 Sve tri staju u **jedan dan pre gate-a (21.08)** — zato izmene 17–20.08 držati
      lokalnim i bez dirania slugova.
      🔴 **18.08 — sadržajna izmena se DESILA, sweep više nije uslovan nego siguran:**
      konsolidacija cenovnih stranica u hubove (M odluka) izmenila je 5 objavljenih stranica,
      draftovala 5 i skratila glavni meni (77 → 70 stavki) →
      [[dnevnik/2026-08-18-konsolidacija-cenovnih-stranica]].
      🔴 **Ista sesija je otkrila uslovan izuzetak u redirect mapi koji je tiho pao.** Pravilo
      `/podovi-za-garaze/` (182 GSC pogotka) bilo je 11.08 isključeno iz drafta *jer je taj URL
      na buildu bio zauzet stranicom 16875*; draftovanjem te stranice URL je opet prazan, pa bi
      bez pravila posle migracije bio **404**. Vraćeno (cilj: hub garaža, ne stari blog post),
      draft regenerisan — **79 → 80 pravila, svi ciljevi 200**, diff = tačno jedna nova linija.
      ⚠️ Uz to važi obrnuto: **ako se ijedna od 5 draftovanih stranica vrati u `publish`, pravilo
      se mora ponovo isključiti.** Isti oblik izuzetka („ne prenosi se **jer** je URL zauzet")
      postoji u istorijskoj mapi još jednom — proveriti oba pri svakoj promeni statusa stranica.
- [x] ✅ **Rollback plan ZATVOREN 2026-08-11** (pre roka 15.08) — sva 3 pitanja:
      JetBackup 5 dnevni/off-site/90 dana · **nema CDN/edge sloja** (samo LiteSpeed,
      pa je „očisti keš" = LSCWP Purge All i ništa više) · M odluka **„migracija
      samo kad sam tu"** → [[migracija/rollback-plan]]
      🔴 **Nosi nov preduslov u B1 ispod** — v. „Dostupnost" stavka.
- [x] ✅ **Baseline: Generative AI performance report — SNIMLJEN 2026-08-12.**
      ~17.000 prikaza / 112 stranica za 3 meseca (≈13% od 129K Web prikaza —
      podskup, ne dodatan saobraćaj). Koncentracija ekstremna: basket
      (6.901) + pop-tenis (2.250) = **54% svih AI prikaza**. 🔴
      `/sportske-podloge/kosarkaske-konstrukcije/` ima **196** — ista stranica
      koja je kritična rupa u redirect mapi. Pun spisak:
      [[analiza/2026-08-12-genai-baseline]]. Ponoviti očitavanje ~07.09 i
      uporediti po stranici.
- [x] ✅ **Regression sweep PONOVLJEN 2026-08-13** posle FAZE 1 i FAZE 2 (prethodni
      je bio 10.08, pre tri sitewide sesije). **239 stranica: 0 non-200 · 0 bez H1 ·
      0×2H1 · 0 nevalidan JSON-LD · 0 slomljenih slika (1.158) · 0 internih 404
      (1.801 link).** Protiv baseline-a 10.08: **0 razlika** u statusu/H1/JSON-LD/title
      na 194 zajednička URL-a. Prividna regresija „−118 slika/str." = uklonjene
      ikonice mega menija 12.08, ne kvar. 🆕 31 arhiva bez metadesc (18 `product_tag`
      → posle live-a; 13 ostalih → ✅ zatvoreno 13.08).
      **Nov baseline za post-migracionu proveru: `analiza/2026-08-13-regression-post-faza2-*`**
      (na dan migracije `$BASE` → `https://www.antasline.com` i poredi se sa OVIM,
      ne sa 10.08). → [[dnevnik/2026-08-13-regression-sweep-post-faza2]]
- [x] ✅ **301 mapa reverifikovana 2026-08-13** (`redirect-verify.php`) jer je posle
      regeneracije 11.08 draftovana stranica 5455: **45/45 ciljeva → 200**, 0
      duplikata izvora, 0 petlji. **Draft se NE regeneriše.** 🟡 Jedno očekivano
      upozorenje: 16613 vraća 200 (publish+`noindex`) a pravilo ga šalje na `-2` —
      namerna konsolidacija od 30.07.
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
      ⚠️ **Ako se do 25.08 promeni ijedan slug** — pustiti `redirect-verify.php` pa
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

## B. DAN MIGRACIJE (UTO 25.08) — redosled izvršenja

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
- [ ] Potvrditi da je lokalni build **zamrznut** (nema izmena posle **20.08** freeze-a —
      🔴 freeze je 17.08 ponovo otvoren sa 16.08 na 20.08, M odluka; zato se proverava
      20.08, ne 16.08). Provera: rekurzivni sweep `LastWriteTime` po buildu **i**
      `SELECT … WHERE post_modified >= '2026-08-21'` u bazi — oba moraju biti prazna.
- [ ] 🟡 **Provera da je konektor OAuth token živ** (`ads_report.py --from … --to …`
      mora vratiti JSON, ne `invalid_grant`). Bez njega ne rade ni 4.10 ni
      verifikacija konverzija u B6. Ako pukne: `authorize_oauth.py` (browser, 1 min).
      🟢 **Rizik znatno smanjen 2026-08-17** — consent screen `mcp-za-claude` je
      prebačen iz *Testing* u **In production**, pa refresh token **više ne ističe
      na 7 dana**; provera ostaje kao rutinska, ne kao tempirana bomba.
- [ ] Napraviti paket: `migracija/alati/build-staging-package.sh full`
      🟢 Skripta od 2026-08-10 sama izbacuje lokalne artefakte (v. B2) — ne
      oslanjati se na ručno brisanje.
      ✅ **Dry-run izveden 2026-08-13, exclude pravila potvrđena** (22.936 unosa
      pregledano `tar -tzf`-om; 32 `.bak`-klase fajla, mail-logger, `mail-log.txt`,
      harness i debug skripte — nijedan nije ušao). Usput popravljeno: `.htaccess`
      izbačen iz whitelist-e (v. B3), `WP_ROOT`/`OUT_DIR` postali pregazivi.
      → [[dnevnik/2026-08-13-dry-run-build-staging-package]]
- [ ] 🔴 **REDOSLED PRENOSA — paket je 2,7 GB, ne 1,3 GB (izmereno 13.08).**
      Kod 72,3 MB + uploads **2.706,9 MB**; slike se ne kompresuju (2,9 GB na disku
      → 2,71 GB u arhivi). Slobodno na serveru **5.867 MB**.
      🔴 Naivan tok — uploadovati delove pa ih sklopiti u tar **pored** delova —
      traži **5.558 MB**, pre svežeg backup-a i pre raspakivanja → **NE STAJE**.
      Ispravan redosled:
      1. **Prvi izbor: `rsync`/`scp` preko SSH-a** (pristup potvrđen M6, 21.07) —
         bez chunkovanja i sklapanja. FTP chunking (`ftp-upload-chunks.sh`) je bio
         zaobilaznica za nestabilnu data-konekciju 06.08, ne zahtev hostinga.
      2. Ako ipak FTP: **ne sklapati tar** — `cat part-* | tar -xzf - -C …` uz
         brisanje delova u hodu.
      3. Svež backup **skinuti lokalno i obrisati sa servera PRE** uploada.
      Disciplinovan tok: pik ~4,4 GB ✅.

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
      `# BEGIN WordPress` bloka**.
      🔴 **Serverski `.htaccess` se EDITUJE, nikad ne prenosi iz builda** (potvrđeno
      13.08 dry-run-om: lokalni nosi `RewriteBase /antasline/` i
      `RewriteRule . /antasline/index.php` jer je build u podfolderu — prepisivanje
      bi oborilo sajt u celosti i obrisalo produkcijski `# BEGIN LSCACHE` blok).
      Skripta ga od 13.08 više ne pakuje, ali pravilo važi i za ručno kopiranje. `RewriteBase` nije potreban (mod_alias, ne
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

### B7. Post-live (26.08+)
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
