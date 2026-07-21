---
tip: migracija
datum: 2026-07-21
namena: prompt za Claude Code na cPanel terminalu — raspakivanje/import probne migracije na staging.antasline.com (M6/3.14)
izvor: [[migracija/2026-07-21-cpanel-odgovori-subdomen]]
---

# Prompt za cPanel Claude Code — raspakivanje + import (proba migracije)

Preduslov: fajlovi su već otpremljeni preko FTP naloga `staging@antasline.com`
(kredencijali u `C:\Muzika\ftp-staging-creds.txt` na Windows mašini, ne u
vault-u) direktno u root tog FTP naloga:
- `antasline-wp-site-20260721.tar.gz` (3.188.336.722 B, WP core+tema+plugin+uploads,
  BEZ starih backup .sql fajlova, BEZ `wp-content/mu-plugins/al-local-mail-log.php`,
  BEZ `debug.log`/`mail-log.txt`)
- `antasline_staging_dump_20260721.sql` (48.495.193 B, svež mysqldump lokalne
  `antasline_local` baze, tabele već sa prefiksom `wpGs_`, `--single-transaction
  --routines --triggers`)

Kopiraj sve iz bloka ispod u Claude Code sesiju na cPanel terminalu:

```
Radiš NA cPanel produkcionom serveru (wp1.oblak.host, nalog antasline).

STRIKTNO: public_html (živi sajt) i baza `antasline_novabaza` se NE DIRAJU
ničim, ni slučajno. Sav rad ide isključivo u staging docroot i u
`antasline_staging` bazu.

KORAK 0 — potvrdi TAČAN docroot pre bilo čega:
  Raniji popis (07-21) je naveo /home/antasline/staging kao docroot subdomena,
  ali FTP nalog `staging@` se posle logina nalazi u
  /home/antasline/antasline.com/staging. Proveri koji je STVARNO konfigurisan
  document root za staging.antasline.com (WHM/cPanel Subdomains stranica,
  ili `cat ~/etc/antasline.com/*staging*` / `uapi DomainInfo domains_data` /
  `cat ~/.cpanel/datastore/*` ako je čitljivo) — ako se razlikuju, ili je
  jedno symlink na drugo, navedi to u odgovoru. Radi dalje u ONOM folderu koji
  je STVARNO document root (taj mora da odgovara onome što
  https://staging.antasline.com/ servira).

  Ako se pokaže da su fajlovi otpremljeni u pogrešan folder (nije isto kao
  pravi docroot), prvo ih premesti/kopiraj u pravi docroot pre nastavka.

KORAK 1 — raspakuj:
  cd <PRAVI_DOCROOT>
  tar -xzf antasline-wp-site-20260721.tar.gz
  (proveri da su wp-admin/wp-includes/wp-content sada tu, sa ownership
  antasline:antasline)

KORAK 2 — wp-config.php za staging (NOVI fajl, ne kopiraj lokalni):
  - DB_NAME: antasline_staging
  - DB_USER: antasline_antasline
  - DB_PASSWORD: (Miroslav ima lozinku od kreiranja baze preko cPanel UI —
    ako je NEMAŠ, proveri da li postoji ~/staging-db-credentials.txt na
    serveru od ranije; ako ni tu nije, stani i traži od Miroslava direktno,
    NE pogađaj/ne izmišljaj)
  - DB_HOST: localhost
  - $table_prefix = 'wpGs_';
  - fresh auth keys/salts (wp config shuffle-salts posle kreiranja, ili
    generiši preko wp-cli helpera)
  - koristi `wp config create --dbname=... --dbuser=... --dbpass=... \
    --dbhost=localhost --dbprefix=wpGs_ --path=<PRAVI_DOCROOT> --skip-check`
    umesto ručnog pisanja fajla ako je moguće (manje grešaka)

KORAK 3 — import baze:
  wp db import antasline_staging_dump_20260721.sql --path=<PRAVI_DOCROOT>

KORAK 4 — URL rewrite (lokalni URL je bio http://localhost/antasline, potvrđeno
  u wp_options siteurl/home):
  wp search-replace 'http://localhost/antasline' 'https://staging.antasline.com' \
    --all-tables --precise --path=<PRAVI_DOCROOT>

KORAK 5 — flush + provera:
  wp rewrite flush --hard --path=<PRAVI_DOCROOT>
  wp option get siteurl --path=<PRAVI_DOCROOT>
  wp option get home --path=<PRAVI_DOCROOT>
  (oba moraju vratiti https://staging.antasline.com)

KORAK 6 — zaštita od javnog indeksiranja/crawl-a (OVO JE PROBA, ne sme u
  Google index dok traje):
  Dodaj HTTP Basic Auth na ceo staging docroot (cPanel "Directory Privacy"
  preko UI, ili ručno: `htpasswd -bc <PRAVI_DOCROOT>/.htpasswd stagingtest
  <nasumična-lozinka>` + AuthType Basic blok na VRHU staging .htaccess-a, PRE
  WordPress-ovih rewrite pravila — pazi da ne polomiš postojeća WP rewrite
  pravila ispod). Sačuvaj korisničko ime/lozinku u
  ~/staging-htaccess-creds.txt na serveru (VAN vault-a, van git-a).

KORAK 7 — očisti veliku arhivu iz docroot-a (nema potrebe da 3GB tar.gz i
  46MB sql leže javno dostupni na disku posle importa):
  mv antasline-wp-site-20260721.tar.gz antasline_staging_dump_20260721.sql ~/
  (ili obriši ako je siguran da nije potreban — original ostaje na lokalu)

KORAK 8 — verifikacija:
  curl -I https://staging.antasline.com/  → očekuj 401 (Basic Auth aktivan)
  curl -u stagingtest:<lozinka> -sI https://staging.antasline.com/ → očekuj 200
  Proveri i par internih putanja (npr. /proizvod/, /industrijski-podovi/) sa
  istim -u flagom.

KORAK 9 — vault:
  Append u ~/antasline-vault/DNEVNIK-NAPRETKA.md na vrh: šta je urađeno, TAČAN
  docroot koji je korišćen (razrešeno pitanje iz Koraka 0), da li je Basic
  Auth aktivan (BEZ lozinke u tekstu — samo "kredencijali u
  ~/staging-htaccess-creds.txt"), rezultat verifikacije (200/401 kako treba),
  bilo koji problem na koji si naišao. Ažuriraj i status M6/3.14 u
  ~/antasline-vault/PROGRESS.md (Blokeri sekcija) i u
  ~/antasline-vault/2026-07-06-MASTER-PLAN-V2.md (red 3.14).

  git add -A && git commit -m "cpanel-live: proba migracije - staging.antasline.com popunjen (M6/3.14)" && git push

NE RADI: ništa na public_html-u ili antasline_novabaza bazi. Ako nešto ne
uspe (npr. DB lozinka nedostaje, docroot konfuzija se ne da razrešiti), STANI
i zapiši tačno šta blokira u dnevnik umesto da nagađaš/zaobilaziš.
```

## Posle (Miroslav)
1. Kopiraj blok gore u Claude Code na cPanel terminalu.
2. Kad završi i pushuje, ovde `git pull` pa mi javi.
3. Ja (lokalna sesija) proveravam rezultat i radim regresiju (200/1×H1/linkovi) preko HTTP-a na `staging.antasline.com` (uz Basic Auth kredencijale koje mi prosledi).
