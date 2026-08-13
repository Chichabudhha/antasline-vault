---
tip: migracija
datum: 2026-08-13
namena: prompt za Claude Code na cPanel terminalu — PUNO postavljanje staging.antasline.com (V4), fajlovi ubačeni RUČNO preko cPanel File Manager-a
izvor: [[migracija/2026-08-06-prompt-staging-full-restore]] (V3, izvršen uspešno), [[dnevnik/2026-08-13-dry-run-build-staging-package]]
status: čeka izvršenje
---

# Prompt za cPanel Claude Code — staging.antasline.com V4 (ručni upload)

## Zašto V4 i šta je drugačije od V3 (06.08)

Staging je **obrisan pre nekoliko dana** (M, 13.08) — V3 postavljanje od 06.08 više
ne postoji. Zato je ovo opet **PUNO** postavljanje, ne delta.

Tri razlike u odnosu na V3 prompt:

1. **Fajlovi se ne šalju FTP-om nego ručno kroz cPanel File Manager** — nema
   `.part-*` delova ni `cat part-* >` sklapanja. Umesto md5 po delovima, integritet
   se proverava **md5 sumom cele arhive** (vrednosti dole) — obavezno, jer je prekinut
   upload kroz File Manager tih Dat.
2. **`.htaccess` više NIJE u kod paketu** (izbačen 13.08 posle dry-run nalaza — lokalni
   nosi `RewriteBase /antasline/` i oborio bi sajt u celosti). Posledica: raspakivanje
   **ne prepisuje** serverski `.htaccess`, pa Basic Auth blok **preživljava** — ali samo
   ako docroot nije obrisan zajedno sa njim (v. KORAK 0).
3. **`wp-config.php` nikad nije bio u paketu** i nije ni sad — staging dobija svoj.

## Sadržaj paketa (napravljeno 2026-08-13 lokalno)

| Fajl | Veličina (B) | md5 |
|---|---|---|
| `antasline-wp-code-2026-08-13.tar.gz` | 75.811.272 | `0f6c2dc3d3f3ffd95ac0b7a0a6d743bb` |
| `antasline-uploads-2026-08-13.tar.gz` | 2.838.417.542 | `d55c7d9ea99f5cf151c26806c05bbe51` |
| `antasline_staging_dump_2026-08-13.sql` | 37.640.983 | `a0f169d444c251f1198310cea87d15f0` |

Provereno u samim arhivama pre slanja:
- putanje počinju direktno sa `wp-admin/` odnosno `wp-content/uploads/` → **bez**
  `--strip-components`
- **nema** `wp-config.php`, **nema** root `.htaccess`, **nema** `*.bak-*`, **nema**
  `mu-plugins/al-local-mail-log.php`
- jedina dva `.htaccess` unosa su bezopasna i očekivana: `woodmart-core/vendor/opauth/…/example/`
  (deo plugina kakav se isporučuje) i `wp-content/uploads/.htaccess` (tri reda, WebP
  Express „plugin is deactivated")

Dump je izvezen **posle** današnje izmene stranice `/sportske-podloge/` (5438) — vraćena
basket-semantika + FAQPage schema, v. [[dnevnik/2026-08-13-5438-basket-semantika-faqpage]].
Zato KORAK 10 ima namensku proveru baš te stranice.

---

## Blok za kopiranje u Claude Code sesiju na cPanel terminalu

```
Radiš NA cPanel serveru (wp1.oblak.host, nalog antasline).

🔴 STRIKTNO: `public_html` (ŽIVI sajt antasline.com) i baza `antasline_novabaza`
se NE DIRAJU ničim, ni slučajno, ni read-write ni "samo da proverim". Sav rad ide
isključivo u staging docroot i u bazu `antasline_staging`. Ako neki korak izgleda
kao da traži dodirivanje public_html-a — STANI i pitaj.

KORAK 0 — utvrdi šta uopšte postoji (staging je obrisan pre par dana, ne
  pretpostavljaj u kakvom je stanju):
  uapi DomainInfo domains_data | grep -i -A3 staging     # da li subdomen postoji
  ls -la /home/antasline/staging                          # pravi docroot (07-21/08-06)
  ls -la /home/antasline/antasline.com/staging            # FTP folder, poznat mismatch
  uapi Mysql list_databases | grep -i staging             # da li baza postoji
  df -h ~ ; uapi Quota get_quota_info                     # treba bar 6 GB slobodno

  🔴 Ako subdomen NE postoji — STANI. Njegovo kreiranje je posao za Miroslava u
  cPanel UI-ju (Domains → Create A Domain), ne za ovu sesiju.

  🟢 BAZA `antasline_staging` POSTOJI — M potvrdio 13.08. Sadržaj joj je ZASTAREO
  (nosi još Yoast podatke, bez Rank Math-a, dakle stanje pre 05.08 migracije). To je
  OČEKIVANO i nije problem: KORAK 5 je briše u celosti (`wp db reset`) i uvozi svež
  dump. Ne pokušavaj ništa da spasavaš iz nje i ne zaključuj po njoj u kakvom je
  stanju build. Ako baze ipak NEMA — STANI i javi (kreiranje baze + korisnika +
  privilegija je cPanel UI korak).

  Zapiši u dnevnik šta si zatekao PRE nego što bilo šta menjaš.

KORAK 1 — nađi ručno ubačene fajlove i proveri integritet PRE raspakivanja:
  Fajlovi su ubačeni preko cPanel File Manager-a (pitaj Miroslava u koji tačno
  folder ako nisu u docroot-u ni u FTP folderu iz KORAKA 0).

  md5sum antasline-wp-code-2026-08-13.tar.gz
  md5sum antasline-uploads-2026-08-13.tar.gz
  md5sum antasline_staging_dump_2026-08-13.sql

  Uporedi sa vrednostima iz tabele u vault fajlu
  `migracija/promptovi/2026-08-13-staging-full-restore-v4.md`.
  🔴 Ako se BILO KOJA md5 ne poklapa — upload je prekinut na pola. STANI, ne
  raspakuj, javi Miroslavu da ponovi upload tog fajla. (Isto se desilo lokalno pri
  pravljenju paketa — arhiva je izgledala kao arhiva, a bila odsečena na 1 MB.)

  tar -tzf antasline-wp-code-2026-08-13.tar.gz > /dev/null && echo "code struktura OK"
  tar -tzf antasline-uploads-2026-08-13.tar.gz > /dev/null && echo "uploads struktura OK"

KORAK 2 — raspakuj KOD u docroot:
  Prvo proveri da putanje počinju direktno sa wp-admin/ wp-includes/ wp-content/:
  tar -tzf antasline-wp-code-2026-08-13.tar.gz | head -3
  (ako ipak ima "antasline/" prefiks — dodaj --strip-components=1)

  cd /home/antasline/staging        # ili tačan docroot iz KORAKA 0
  tar -xzf <put-do>/antasline-wp-code-2026-08-13.tar.gz

  🟢 Arhiva ovaj put NE sadrži `.htaccess` (izbačen 13.08) — postojeći serverski
  `.htaccess` se NE prepisuje. Ipak proveri da Basic Auth blok stoji:
  head -20 .htaccess     # očekuj AuthType Basic / AuthUserFile
  ls -la .htpasswd

  🔴 Ako .htaccess ili .htpasswd NE postoje (obrisani zajedno sa docroot-om):
  staging je OTVOREN — Google ga može indeksirati i to direktno šteti live sajtu
  pred migraciju. Vrati zaštitu PRE nego što nastaviš:
    AuthType Basic
    AuthName "Staging"
    AuthUserFile /home/antasline/staging/.htpasswd
    Require valid-user
  a `.htpasswd` napravi sa `htpasswd -c /home/antasline/staging/.htpasswd stagingtest`
  (lozinku traži od Miroslava — NE izmišljaj je i NE upisuj je u vault).

  rm <put-do>/antasline-wp-code-2026-08-13.tar.gz    # tek posle uspešnog raspakivanja

KORAK 3 — raspakuj UPLOADS (najveći potrošač privremenog prostora):
  df -h ~     # ako je slobodno manje od ~1 GB — STANI, javi
  cd /home/antasline/staging
  tar -xzf <put-do>/antasline-uploads-2026-08-13.tar.gz
  rm <put-do>/antasline-uploads-2026-08-13.tar.gz

KORAK 4 — wp-config.php (paket ga namerno NE sadrži):
  - DB_NAME: antasline_staging
  - DB_USER: antasline_antasline
  - DB_PASSWORD: pročitaj ~/staging-db-credentials.txt
    🔴 Poznato od 06.08: ta lozinka ume da NE radi (Access denied). Ako ne prolazi,
    NE pogađaj — traži od Miroslava reset (`uapi Mysql set_password`) i ažuriraj fajl.
  - DB_HOST: localhost
  - $table_prefix: 🔴 NE piši napamet. Pročitaj iz samog dump-a:
    grep -o -m3 "CREATE TABLE \`[a-zA-Z_]*\`" antasline_staging_dump_2026-08-13.sql
    (očekuje se `wpgs_` MALIM slovima; na Linux-u je case OSETLJIV, za razliku od
    lokalnog Windows MySQL-a gde `wpGs_` prolazi — to je tačan uzrok „site not
    installed" greške pri probi 21.07)
  - Salt konstante: `wp config shuffle-salts` posle importa.

KORAK 5 — import baze:
  wp db reset --yes --path=/home/antasline/staging
  wp db import antasline_staging_dump_2026-08-13.sql --path=/home/antasline/staging
  rm antasline_staging_dump_2026-08-13.sql

KORAK 6 — URL rewrite (očekuj ~14.000 zamena, kao 06.08):
  wp search-replace 'http://localhost/antasline' 'https://staging.antasline.com' \
    --all-tables --precise --path=/home/antasline/staging

KORAK 7 — flush + osnovna provera:
  wp rewrite flush --hard --path=/home/antasline/staging
  wp option get siteurl --path=/home/antasline/staging
  wp option get home --path=/home/antasline/staging
  (oba moraju vratiti https://staging.antasline.com)

KORAK 8 — 🔴 GASI GTM (M odluka 13.08, nije opciono):
  Staging nosi pravi kontejner GTM-TRDT8K9. Klijent gleda sajt večeras, pa bi svaki
  njegov klik ušao kao stvaran podatak u GA4 i Google Ads — a merenje je i bez toga
  već naduvano (v. `--live-only` filter u konektoru).

  cd /home/antasline/staging
  mv wp-content/mu-plugins/al-tracking-gtm-consent.php \
     wp-content/mu-plugins/al-tracking-gtm-consent.php.off

  Potvrdi da je stvarno ugašeno (mora vratiti 0):
  curl -u stagingtest:<lozinka> -s https://staging.antasline.com/ | grep -c "GTM-TRDT8K9"

  ⚠️ Posledica koju treba znati: dok je ovako, GTM Preview test na stagingu NIJE
  moguć — a stavka 5.6 (`gallery_view`/`pdf_download`, DRAFT u GTM Workspace-u od
  22.07) čeka baš njega. Kad zatreba, vraća se `mv`-om nazad, bez ponovnog paketa.

KORAK 9 — počisti artefakte transfera:
  Proveri i docroot i FTP folder (/home/antasline/antasline.com/staging) da nije
  ostalo tar/sql/part fajlova. Ništa od transfera ne sme ostati javno dostupno.

KORAK 10 — verifikacija (ne preskakati, prošli put je površna provera propustila
  82/108 slomljenih slika):
  curl -sI https://staging.antasline.com/                      → očekuj 401
  curl -u stagingtest:<lozinka> -sI https://staging.antasline.com/   → očekuj 200

  Stranice (sve sa auth, sve 200): /industrijski-podovi/ · /katalog/ · /kontakt/ ·
  /planer-terena/ · /sportske-podloge/ · jedan pravi /proizvod/<slug>/

  🔴 Slike — BAR 5 nasumičnih sa različitih stranica, uključujući jednu iz starog
  datumskog foldera (2018/2020) i jednu iz 2026/08:
  wp db query "SELECT guid FROM wpgs_posts WHERE post_type='attachment' ORDER BY RAND() LIMIT 5" --path=/home/antasline/staging
  pa curl -u ... -sI na svaku.

  🔴 Namenska provera današnje izmene (5438) — mora proći sve četiri:
  curl -u stagingtest:<lozinka> -s https://staging.antasline.com/sportske-podloge/ > /tmp/s5438.html
  grep -c "<h1" /tmp/s5438.html                          → tačno 1
  grep -c "Izgradnja sportskih terena za basket" /tmp/s5438.html   → najmanje 1
  grep -c "FAQPage" /tmp/s5438.html                      → tačno 1
  grep -c "planer-terena" /tmp/s5438.html                → najmanje 2
  (ako je FAQPage 0 — schema je pojedena pri importu, javi, ne popravljaj napamet)

  🟡 OČEKIVANO, nije bag: favicon nije podešen (dump ne nosi site_icon) ·
  `?_gl=` na linkovima (GA4 cross-domain linker na drugom hostname-u).

KORAK 11 — vault:
  Append u ~/antasline-vault/DNEVNIK-NAPRETKA.md: šta je zatečeno u KORAKU 0, šta je
  urađeno, pun rezultat KORAKA 10, svaki problem. Ažuriraj PROGRESS.md.
  git add -A && git commit -m "cpanel-live: staging.antasline.com V4 puno postavljanje (rucni upload)" && git push

NE RADI: ništa na public_html-u ni na bazi antasline_novabaza. Ako nešto ne uspe
(md5 ne odgovara, subdomen/baza ne postoje, DB lozinka ne radi, nema disk prostora)
— STANI i zapiši tačno šta blokira, umesto da nagađaš ili zaobilaziš.
```

## Posle (Miroslav)

1. Ubaci tri fajla kroz cPanel File Manager.
2. Otvori Claude Code na cPanel terminalu, nalepi blok gore.
3. Kad završi, `git pull` lokalno pa **sam vizuelno pregledaj staging** pre nego što
   ga pokažeš klijentu — taj korak je 06.08 uhvatio 82/108 slomljenih slika koje su
   svi automatski testovi propustili.
4. Klijentu daj `stagingtest` + lozinku (Basic Auth), inače vidi samo prozor za
   prijavu.
