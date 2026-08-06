---
tip: migracija
datum: 2026-08-06
namena: prompt za Claude Code na cPanel terminalu — PUNO ponovno postavljanje staging.antasline.com preko FTP-a (docroot+baza su trenutno prazni posle revert-a istog dana)
izvor: [[migracija/2026-08-06-prompt-staging-refresh]] (poništena v2, delimičan pristup), [[migracija/2026-07-21-prompt-subdomen-import]] (prvobitni setup)
---

# Prompt za cPanel Claude Code — PUNO postavljanje staging.antasline.com (V3, preko FTP-a)

## Kontekst (pročitaj pre početka)

Ranije danas (2026-08-06) je urađen delimičan refresh koji je posle vizuelne
provere **poništen** — 82/108 slika 404, favicon nepodešen, kod-paket je bio
tar cele XAMPP fascikle (4.8GB smeća). Docroot je vraćen na golo (samo
`.htaccess`/`.htpasswd`/`.ftpquota`/`.well-known` ostaju), `antasline_staging`
baza je DROP+CREATE prazna. **Dakle ovo NIJE delta refresh — treba PUNO
postavljanje**, jer stara osnova na koju bi se lepio diff više ne postoji.

Ovaj put su napravljena **3 čista paketa** (skripta
`migracija/alati/build-staging-package.sh`, popravlja oba uzroka prošlog
pada):

1. **Kod paket** — SAMO `wp-admin`/`wp-includes`/`wp-content` (bez
   `uploads`/`cache`/`mail-log.txt`) + belilista root fajlova (core WP
   fajlovi, `.htaccess`, `robots.txt`, `llms.txt`, `license.txt`) — **NE
   `wp-config.php`** (staging dobija svoj, ne sme se prepisati lokalnim dev
   vrednostima — poznat gotcha) i **NE lokalni debug/import/backup skripte**
   (`add-blocks-*.php`, `fix-*.php`, `import-*.php`, "kopija" fajlovi —
   ostaci lokalnog rada, bezbednosni rizik ako ostanu javno dostupni).
2. **Uploads paket** — PUN `wp-content/uploads` (2,6GB gzip) — pun, ne diff,
   jer diff nema osnovu na koju bi se lepio.
3. **DB dump** — svež `mysqldump` od `antasline_local` (37MB), napravljen
   2026-08-06 posle plugin/DB čišćenja iz prethodne sesije istog dana.

FTP nalog `staging@antasline.com` sad ima **7GB kvotu** (povećana za ovaj
zadatak) — dovoljno za sve troje, ali **paziti na prostor tokom raspakivanja**
(v. Korak 4, brisati delove/arhive odmah posle uspešne verifikacije/raspakivanja).

## Preduslov — fajlovi su VEĆ na FTP-u, nema šta da se otprema

Na FTP root-u (`/home/antasline/antasline.com/staging` — poznat mismatch sa
pravim docroot-om, v. Korak 0) trenutno postoji:

- `antasline-wp-code-2026-08-06.tar.gz.part-000..003` (4 dela, 20MB osim
  poslednjeg) + `chunks-md5sums-antasline-wp-code-2026-08-06.txt`
- `antasline-uploads-2026-08-06.tar.gz.part-000..132` (133 dela, 20MB osim
  poslednjeg) + `chunks-md5sums-antasline-uploads-2026-08-06.txt`
- `antasline_staging_dump_2026-08-06.sql.part-000..001` (2 dela) +
  `chunks-md5sums-antasline_staging_dump_2026-08-06.txt`

Kopiraj sve iz bloka ispod u Claude Code sesiju na cPanel terminalu:

```
Radiš NA cPanel produkcionom serveru (wp1.oblak.host, nalog antasline).

STRIKTNO: public_html (živi sajt) i baza `antasline_novabaza` se NE DIRAJU
ničim, ni slučajno. Sav rad ide isključivo u staging docroot i u
`antasline_staging` bazu. Ovo je PUNO postavljanje — staging docroot je
trenutno prazan (samo .htaccess/.htpasswd/.ftpquota/.well-known), baza je
prazna posle DROP+CREATE.

KORAK 0 — potvrdi tačan docroot:
  Poznato iz 07-21/08-06: pravi document root je /home/antasline/staging
  (potvrđeno preko `uapi DomainInfo domains_data`). FTP nalog staging@
  uploaduje fajlove u DRUGI folder (/home/antasline/antasline.com/staging) —
  pronađi ih tamo.
  Proveri i trenutni disk prostor na nalogu pre početka: `df -h ~` ili
  `uapi Quota get_quota_info` — treba bar 6GB slobodno tokom raspakivanja
  (paketi ~2,7GB + privremeno oba oblika arhive+raspakovano tokom prelaska,
  v. Korak 4 za redosled brisanja koji drži ovo pod kontrolom).

KORAK 1 — spoji delove i PRVO verifikuj integritet PRE bilo kakvog raspakivanja:
  cd <folder gde su fajlovi>
  cat antasline-wp-code-2026-08-06.tar.gz.part-* > antasline-wp-code-2026-08-06.tar.gz
  cat antasline-uploads-2026-08-06.tar.gz.part-* > antasline-uploads-2026-08-06.tar.gz
  cat antasline_staging_dump_2026-08-06.sql.part-* > antasline_staging_dump_2026-08-06.sql

  md5sum -c chunks-md5sums-antasline-wp-code-2026-08-06.txt        # svi delovi OK
  md5sum -c chunks-md5sums-antasline-uploads-2026-08-06.txt        # svi delovi OK
  md5sum -c chunks-md5sums-antasline_staging_dump_2026-08-06.txt   # svi delovi OK

  tar -tzf antasline-wp-code-2026-08-06.tar.gz > /dev/null && echo "code OK"
  tar -tzf antasline-uploads-2026-08-06.tar.gz > /dev/null && echo "uploads OK"

  Ako BILO KOJA provera padne — STANI, ne raspakuj, javi problem u dnevnik.

  Čim su sve provere OK, obriši .part-* fajlove (nisu više potrebni, oslobađa
  prostor pre raspakivanja):
  rm antasline-wp-code-2026-08-06.tar.gz.part-* antasline-uploads-2026-08-06.tar.gz.part-* antasline_staging_dump_2026-08-06.sql.part-*

KORAK 2 — raspakuj KOD paket u prazan docroot (arhiva NEMA "antasline/"
  prefiks ovaj put — proveri prvo sa `tar -tzf antasline-wp-code-2026-08-06.tar.gz | head -3`,
  putanje treba da počnu direktno sa wp-admin/, wp-includes/, wp-content/,
  index.php... — ako ipak ima prefiks, dodaj --strip-components=1):
  cd <PRAVI_DOCROOT>
  tar -xzf <put-do>/antasline-wp-code-2026-08-06.tar.gz
  🔴 Arhiva SADRŽI `.htaccess` (iz lokalnog WP-a, samo rewrite pravila, BEZ
  Basic Auth) — ovo PREPISUJE postojeći .htaccess koji je čuvao Basic Auth
  blok. ODMAH posle raspakivanja proveri i vrati Basic Auth blok na vrh
  .htaccess-a (isti gotcha kao 07-21/08-06):
  head -20 .htaccess   # da li je Basic Auth blok (AuthType/AuthUserFile) na vrhu?
  Ako nije, dodaj ga nazad (format iz postojećeg .htpasswd/ranijih commit-a,
  ili pitaj Miroslava ako nisi siguran u tačan blok).
  Obriši kod arhivu posle uspešnog raspakivanja: rm antasline-wp-code-2026-08-06.tar.gz

KORAK 3 — raspakuj UPLOADS paket (isto, bez prefiksa, sleće direktno u
  wp-content/uploads/...):
  tar -xzf <put-do>/antasline-uploads-2026-08-06.tar.gz
  Obriši uploads arhivu posle uspešnog raspakivanja:
  rm antasline-uploads-2026-08-06.tar.gz
  (ovo je korak koji najviše troši privremeni prostor — ako df pokaže
  manje od ~1GB slobodno pre ovog koraka, STANI i javi, ne nastavljaj na baze)

KORAK 4 — wp-config.php (NOV, jer paket namerno NE sadrži wp-config.php):
  - DB_NAME: antasline_staging / DB_USER: antasline_antasline
  - DB_PASSWORD: proveri ~/staging-db-credentials.txt na serveru — NE
    pogađaj/ne izmišljaj, ako nema pitaj Miroslava
  - DB_HOST: localhost
  - $table_prefix: 🔴 NE prepisuj "wpGs_" ili "wpgs_" napamet — proveri
    STVARNI prefiks u dump-u pre pisanja (poznat gotcha, [[reference/naucene-lekcije]]):
    grep -o -m3 "CREATE TABLE \`[a-zA-Z_]*\`" antasline_staging_dump_2026-08-06.sql
    (na lokalu je ovaj put potvrđeno `wpgs_`, malo slovo — ali proveri ponovo
    na SERVERSKOM fajlu posle transfera, ne veruj ovoj napomeni bez provere)
  - Najjednostavnije: kopiraj `wp-config-sample.php` iz raspakovanog koda (ako
    postoji u arhivi) ili napravi ručno sa gornjim vrednostima; ne zaboravi
    `salt` konstante (generiši sa `wp config shuffle-salts` posle importa,
    ili ostavi placeholder — nije bezbednosno kritično za staging).

KORAK 5 — DROP + import baze:
  wp db reset --yes --path=<PRAVI_DOCROOT>
  wp db import antasline_staging_dump_2026-08-06.sql --path=<PRAVI_DOCROOT>
  rm antasline_staging_dump_2026-08-06.sql   # posle uspešnog importa

KORAK 6 — URL rewrite:
  wp search-replace 'http://localhost/antasline' 'https://staging.antasline.com' \
    --all-tables --precise --path=<PRAVI_DOCROOT>

KORAK 7 — flush + provera:
  wp rewrite flush --hard --path=<PRAVI_DOCROOT>
  wp option get siteurl --path=<PRAVI_DOCROOT>
  wp option get home --path=<PRAVI_DOCROOT>
  (oba moraju vratiti https://staging.antasline.com)

KORAK 8 — Basic Auth ponovna provera (v. Korak 2 napomenu) — potvrdi da
  curl bez auth vraća 401 (Korak 10) pre nego što nastaviš.

KORAK 9 — očisti sve upload artefakte (md5 fajlove, FTP root ostatke):
  rm chunks-md5sums-antasline-wp-code-2026-08-06.txt \
     chunks-md5sums-antasline-uploads-2026-08-06.txt \
     chunks-md5sums-antasline_staging_dump_2026-08-06.txt
  (proveri i FTP root — /home/antasline/antasline.com/staging — da nije
  ostalo ničeg od transfera)

KORAK 10 — verifikacija (ovaj put punija nego prošli put, da se ne ponovi
  ista lažna-zelena provera):
  curl -I https://staging.antasline.com/  → očekuj 401
  curl -u stagingtest:<lozinka> -sI https://staging.antasline.com/ → očekuj 200
  Proveri: /industrijski-podovi/, /proizvod/, /katalog/, /kontakt/,
  /planer-terena/ — svi 200 sa auth.
  🔴 Slike — proveri BAR 5 nasumičnih proizvod-slika na različitim
  stranicama (ne samo homepage), uklj. bar jednu iz "starog" datumskog
  foldera (npr. 2018/2020) i jednu avgustovsku (2026/08) — cilj je uhvatiti
  tačno onu vrstu promašaja koja je prošli put prošla neopaženo:
  curl -u stagingtest:<lozinka> -sI https://staging.antasline.com/wp-content/uploads/2026/08/16919-gallery-1-300x300.webp → 200
  (dopuni sa još par nasumičnih putanja iz baze — `wp db query "SELECT guid FROM wpgs_posts WHERE post_type='attachment' ORDER BY RAND() LIMIT 5"`)
  Meni ikonice (ranije 404-ovale u celom folderu): proveri da
  wp-content/uploads/meni-ikonice/ (ili tačna putanja ako je drugačija)
  stvarno postoji i vraća 200 na bar jednu ikonicu.
  Yoast title na homepage ("Početna | Antas Line") kao brz sanity check.
  🟡 Favicon — OČEKIVANO da NIJE podešen (dump sam ne nosi site_icon, poznato
  od pre — nije novi bag, ne trošiti vreme na fix ovde).
  🟡 `?_gl=` na linkovima — OČEKIVANO na drugom hostname-u (GA4/GTM
  cross-domain linker), nije bag.

KORAK 11 — vault:
  Append u ~/antasline-vault/DNEVNIK-NAPRETKA.md na vrh: šta je urađeno,
  rezultat KORAKA 10 verifikacije (posebno slike/ikonice, ovaj put pun
  spot-check ne samo homepage), bilo koji problem.
  Ažuriraj ~/antasline-vault/PROGRESS.md: staging puno postavljanje
  (2026-08-06 V3) zatvoreno, uz napomenu da čeka Miroslavljevu vizuelnu
  proveru pre nego što se proglasi konačno gotovim (isti korak koji je
  prošli put otkrio problem — ne preskočiti).

  git add -A && git commit -m "cpanel-live: staging.antasline.com puno postavljanje V3 (cist paket, pun uploads, fresh wp-config)" && git push

NE RADI: ništa na public_html-u ili antasline_novabaza bazi. Ako nešto ne
uspe (npr. DB lozinka nedostaje, docroot konfuzija, md5sum provera padne,
nema dovoljno disk prostora) — STANI i zapiši tačno šta blokira u dnevnik
umesto da nagađaš/zaobilaziš.
```

## Posle (Miroslav)

1. Otvori Claude Code na cPanel terminalu (fajlovi su VEĆ na FTP-u preko
   background upload-a ove sesije), nalepi blok gore.
2. Kad završi i pushuje, `git pull` ovde pa **prvo vizuelno pregledaj
   staging.antasline.com sam** pre nego što se ovo proglasi zatvorenim —
   prošli put je baš taj korak uhvatio 82/108 slomljenih slika koje su svi
   automatski testovi propustili.
