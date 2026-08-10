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

- [ ] **Svež backup live sajta** (db + wp-content) na 2 lokacije — `[cpanel-live]`
      sesija. 🔴 gate stavka. JetBackup 5 radi dnevno kod provajdera (potvrđeno
      27.07), ali za migraciju treba **ručan, datiran backup neposredno pre**,
      ne oslanjanje na noćni.
- [ ] **Backup finalnog lokalnog builda** na 2 lokacije — 🔴 poznato ograničenje:
      `nocni-backup.ps1` piše na SAMO JEDNU destinaciju (prioritet G:→OneDrive→lokalno).
      Za „2 lokacije" treba ručna druga kopija na dan zamrzavanja builda.
- [ ] **Rollback plan zatvoren** — 3 pitanja za M, rok **15.08**: WHM auto-backup ·
      CDN/edge keš sloj · ko izvršava ako M nije dostupan → [[migracija/rollback-plan]]
- [ ] **Enhanced Conversions — Ads UI** (M, 5 min): Goals → Conversions →
      „Lead - forma (GTM)" → Settings → Enhanced conversions → uključiti, metod
      **Google Tag Manager**, prihvatiti customer data terms.
- [ ] **Final URL audit oglasa — priprema**: izvezi sve final URL-ove iz obe
      kampanje i uporedi sa novim slugovima. Popravka ide na dan migracije (4.10),
      ali **spisak se pravi ranije**.
- [ ] **`.htaccess` 301 — poslednja reverifikacija** (`htaccess-301-DRAFT.txt` vs
      `redirect-mapa-FINAL.csv` + `redirect-mapa-HISTORIJSKI-65-FLAT.csv`).
      🔴 NE aktivirati pre dana migracije.
- [ ] **GSC priprema**: sitemap URL spreman za resubmit, alerti uključeni.

---

## B. DAN MIGRACIJE (24.08) — redosled izvršenja

### B1. Pre prebacivanja
- [ ] Backup live sajta ponovo, neposredno pre dodira (db + wp-content), datiran.
- [ ] Potvrditi da je lokalni build **zamrznut** (nema izmena posle 16.08 freeze-a).
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
- [ ] Aktivirati `.htaccess` 301 blok
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
- [ ] GSC sitemap resubmit
- [ ] UptimeRobot
- [ ] Dnevni 404 log pregled, prvih 14 dana
- [ ] 4.8 Maximize Conversions — **~01.09**, namerno posle slegnjivanja 301
      (v. [[2026-07-06-MASTER-PLAN-V2]] §4)

---

## Veze
[[2026-07-06-MASTER-PLAN-V2]] · [[migracija/rollback-plan]] ·
[[migracija/2026-08-09-enhanced-conversions-4.7]] · [[migracija/PARITY-PLAN]] ·
[[dnevnik/2026-08-10-w3-310-full-regression]]
