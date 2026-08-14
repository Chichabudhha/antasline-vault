---
tip: migracija
datum: 2026-08-06
namena: prompt za Claude Code na cPanel terminalu — OSVEŽAVANJE probne migracije na staging.antasline.com preko FTP-a (delimičan refresh: kod + nove slike, POSTOJEĆI uploads i baza ostaju kao osnova)
izvor: [[migracija/2026-07-21-prompt-subdomen-import]] (prvobitni setup)
---

# Prompt za cPanel Claude Code — OSVEŽAVANJE staging.antasline.com (V2, preko FTP-a)

Ovo NIJE prvo postavljanje — `staging.antasline.com` subdomen + Basic Auth +
pun sadržaj (uklj. sve slike) već postoje od 2026-07-21 (M6/3.14). Ovo je
**delimičan refresh preko FTP-a**: samo kod (tema/plagini/WP core) i baza se
menjaju u potpunosti, slike (`wp-content/uploads`) se NE brišu — samo se
DOPUNJUJU novim/izmenjenim fajlovima od 21.07.

## 🔴 VAŽNO — zašto ovaj put NIJE poslata cela arhiva (pročitaj pre početka)

Prvobitni plan (v1 ovog naloga) je bio poslati kompletnu svežu arhivu
(3,18 GB, sve slike uključene) preko FTP-a. **To je puklo** — FTP nalog
`staging@antasline.com` ima **disk kvotu od otprilike 530–560 MB**
(otkriveno empirijski: transferi su počeli da padaju sa `451 Error during
write to file` čim je ukupno otpremljeno prešlo tu granicu, potvrđeno da
je uzrok kvota a ne mreža time što je i 5-bajtni test fajl pao dok je nalog
bio "pun", pa prošao čim je oslobođeno mesta). Zato je strategija promenjena:

- **Kod-only paket** (tema+plagini+WP core, BEZ `wp-content/uploads`) — 169 MB
- **Uploads-diff paket** (SAMO slike dodate/izmenjene posle 2026-07-21,
  filtrirano na `wp-content/uploads/2026/07/*` posle 21.07 i sav
  `wp-content/uploads/2026/08/*` — starije lažne "izmene" iz npr.
  `2015/11/` foldera su isključene, to je bio mtime šum od nekog ranijeg
  bulk restore/copy procesa, ne stvaran novi sadržaj) — 151 MB, 2.762 fajla
- Baza (nepromenjeno) — 36 MB

Ukupno ~356 MB — staje u kvotu. **Ako se ikad zatraži puna sveža arhiva u
budućnosti (npr. za pravu migraciju 31.08), prvo proveriti/povećati FTP
kvotu u cPanel-u** (FTP Accounts → uredi `staging@antasline.com` → Quota),
inače će isti problem da se ponovi.

## Šta je urađeno lokalno pre ovoga (2026-08-06)

- **11 neaktivnih plagina obrisano** sa diska lokalnog builda: `duplicate-page`,
  `duplicator`, `media-sync`, `porto-functionality`, `revslider`,
  `sitepress-multilingual-cms` (WPML), `under-construction-page`,
  `wordpress-importer`, `worker` (ManageWP), `wp-seo-multilingual`,
  `wpml-media-translation`. **`wordpress-seo` (Yoast) NAMERNO OSTAJE**
  (rollback rezerva, [[CLAUDE]] §7.1).
  > 🔵 **Nadgrađeno 2026-08-13 (M odluka):** Yoast je van upotrebe i **obrisan sa
  > diska** istog dana (21 MB) — više ga nema u `wp-content/plugins/`, pa ni u
  > paketima koji se prave po ovom nalogu. Povratak samo iz arhive
  > `antasline-backups/yoast-wordpress-seo-27.8_2026-08-13.tar.gz`
  > (v. [[odluke/_pregled-odluka]] §SEO plugin).
- **Baza očišćena**: 33 osirotele plugin-tabele + prateći options/postmeta/
  usermeta redovi + 2 osirotela cron hook-a + 9 "duh" `wp_*` (stari prefiks)
  tabela (poznat artefakt iz 07-21 uvoza, nikad korišćen). Backup pre svega:
  `antasline-backups/antasline_local_2026-08-06_pre-plugin-cleanup.sql`.
- Verifikovano lokalno: `wp plugin list` čist, homepage/proizvod 200, 0 grešaka.

## Preduslov — fajlovi VEĆ SU na FTP-u (staging@antasline.com), nema šta da se otprema

Na FTP root-u (`/home/antasline/antasline.com/staging` — poznat mismatch sa
pravim docroot-om, v. Korak 0) trenutno postoji:

- `antasline_staging_dump_2026-08-06.sql` (37.720.095 B, ceo fajl, ne treba spajanje)
- `antasline-uploads-diff-2026-08-06.tar.gz.part-000` do `part-007` (8 delova,
  20 MB svaki osim poslednjeg) + `chunks-md5sums-uploads.txt`
- `antasline-wp-code-2026-08-06.tar.gz.part-000` do `part-008` (9 delova,
  20 MB svaki osim poslednjeg) + `chunks-md5sums-code.txt`

Kopiraj sve iz bloka ispod u Claude Code sesiju na cPanel terminalu:

```
Radiš NA cPanel produkcionom serveru (wp1.oblak.host, nalog antasline).

STRIKTNO: public_html (živi sajt) i baza `antasline_novabaza` se NE DIRAJU
ničim, ni slučajno. Sav rad ide isključivo u staging docroot i u
`antasline_staging` bazu. Ovo je DELIMIČAN REFRESH postojećeg staging-a —
NE brišeš ceo docroot ovaj put, samo kod fajlove i bazu; postojeći
wp-content/uploads OSTAJE i samo se dopunjuje.

KORAK 0 — potvrdi tačan docroot (isti kao 07-21, ali proveri ponovo):
  Poznato iz 07-21: pravi document root je /home/antasline/staging (potvrđeno
  preko uapi DomainInfo domains_data). FTP nalog staging@ uploaduje fajlove u
  DRUGI folder (/home/antasline/antasline.com/staging) — pronađi ih tamo i
  premesti/kopiraj u pravi docroot pre nastavka (ili radi spajanje direktno
  tamo pa premesti gotove tar.gz fajlove).

KORAK 1 — spoji delove i PRVO verifikuj integritet PRE bilo kakvog raspakivanja:
  cd <folder gde su fajlovi>
  cat antasline-uploads-diff-2026-08-06.tar.gz.part-* > antasline-uploads-diff-2026-08-06.tar.gz
  cat antasline-wp-code-2026-08-06.tar.gz.part-* > antasline-wp-code-2026-08-06.tar.gz

  md5sum -c chunks-md5sums-uploads.txt   # svih 8 delova mora reci OK
  md5sum -c chunks-md5sums-code.txt      # svih 9 delova mora reci OK

  tar -tzf antasline-uploads-diff-2026-08-06.tar.gz > /dev/null && echo "uploads-diff OK"
  tar -tzf antasline-wp-code-2026-08-06.tar.gz > /dev/null && echo "code OK"

  Ako BILO KOJA provera padne — STANI, ne raspakuj, javi problem u dnevnik.

KORAK 2 — raspakuj KOD paket preko postojećeg staging sadržaja (prepisuje
  wp-core/plugin/theme fajlove, NE dira wp-content/uploads jer taj paket ga
  uopšte ne sadrži):
  cd <PRAVI_DOCROOT>
  tar -xzf <put-do>/antasline-wp-code-2026-08-06.tar.gz --strip-components=1
  (--strip-components=1 skida "antasline/" prefiks iz arhive — proveri prvo
  sa `tar -tzf ... | head -3` da li arhiva ima taj prefiks, prilagodi ako ne)
  NAPOMENA: WP core-ov sopstveni .htaccess unutar arhive može pokušati da
  prepiše postojeći — proveri POSLE raspakivanja da je Basic Auth blok i
  dalje na vrhu .htaccess-a (isti gotcha kao 07-21), popravi ako je prepisano.

KORAK 3 — raspakuj UPLOADS-DIFF paket TAKOĐE preko postojećeg docroot-a
  (dodaje/prepisuje SAMO nove/izmenjene slike iz jula/avgusta, ne dira
  starije postojeće slike koje već stoje od 07-21):
  tar -xzf <put-do>/antasline-uploads-diff-2026-08-06.tar.gz --strip-components=0
  (proveri prefiks isto kao gore — cilj je da fajlovi slete u
  <PRAVI_DOCROOT>/wp-content/uploads/2026/07/... i /2026/08/...)

KORAK 4 — wp-config.php (isti DB kredencijali kao 07-21, PONOVO KORISTI):
  - DB_NAME: antasline_staging / DB_USER: antasline_antasline
  - DB_PASSWORD: proveri ~/staging-db-credentials.txt na serveru — NE
    pogađaj/ne izmišljaj, ako nema pitaj Miroslava
  - DB_HOST: localhost / $table_prefix = 'wpgs_';
    🔴 MALIM slovima. Na Linux-u je case OSETLJIV (za razliku od lokalnog
    Windows MySQL-a); pogrešan case daje „site not installed" bez druge greške.
    Pre pisanja potvrdi protiv samog dump-a:
    grep -o -m3 "CREATE TABLE \`[a-zA-Z_]*\`" antasline_staging_dump_2026-08-06.sql
  - Ako wp-config.php već postoji od 07-21 i dalje je ispravan (isti DB), NE
    mora se ponovo praviti — samo proveri da radi (`wp option get siteurl`).

KORAK 5 — DROP + import nove baze (baza se u potpunosti zamenjuje):
  wp db reset --yes --path=<PRAVI_DOCROOT>
  wp db import antasline_staging_dump_2026-08-06.sql --path=<PRAVI_DOCROOT>
  Posle importa, proveri da NEMA wp_* (stari prefiks) tabela pored wpgs_*
  (`wp db tables --path=<PRAVI_DOCROOT> | grep -v wpgs_`) — ne bi trebalo,
  već obrisane iz lokalnog izvora ovog puta.

KORAK 6 — URL rewrite:
  wp search-replace 'http://localhost/antasline' 'https://staging.antasline.com' \
    --all-tables --precise --path=<PRAVI_DOCROOT>

KORAK 7 — flush + provera:
  wp rewrite flush --hard --path=<PRAVI_DOCROOT>
  wp option get siteurl --path=<PRAVI_DOCROOT>
  wp option get home --path=<PRAVI_DOCROOT>
  (oba moraju vratiti https://staging.antasline.com)

KORAK 8 — Basic Auth provera (nasleđen iz postojećeg .htaccess-a, v. napomena
  u Koraku 2) — SAMO proveri da nije izgubljen. Kredencijali i dalje u
  ~/staging-htaccess-creds.txt (korisničko ime stagingtest) — ne menjaj ih.

KORAK 9 — očisti sve upload artefakte iz docroot-a/FTP root-a:
  rm antasline-wp-code-2026-08-06.tar.gz.part-* antasline-uploads-diff-2026-08-06.tar.gz.part-*
  rm antasline-wp-code-2026-08-06.tar.gz antasline-uploads-diff-2026-08-06.tar.gz
  rm antasline_staging_dump_2026-08-06.sql chunks-md5sums-uploads.txt chunks-md5sums-code.txt
  (obriši i stare 07-21 fajlove ako su još na serveru:
  ~/antasline-wp-site-20260721.tar.gz, ~/antasline_staging_dump_20260721.sql)

KORAK 10 — verifikacija:
  curl -I https://staging.antasline.com/  → očekuj 401
  curl -u stagingtest:<lozinka> -sI https://staging.antasline.com/ → očekuj 200
  Proveri i: /industrijski-podovi/, /proizvod/, /katalog/, /kontakt/ — svi 200
  sa auth. Proveri Yoast title na homepage ("Početna | Antas Line") kao brz
  sanity check da je import svež. Proveri da bar jedna nova avgustovska slika
  postoji (npr. curl -u ... -sI .../wp-content/uploads/2026/08/16919-gallery-1-300x300.webp
  → 200) kao potvrda da je uploads-diff korak stvarno upisao fajlove.

KORAK 11 — vault:
  Append u ~/antasline-vault/DNEVNIK-NAPRETKA.md na vrh: šta je urađeno, da
  li je docroot bio na istom mestu kao 07-21, rezultat verifikacije, bilo
  koji problem. Ažuriraj ~/antasline-vault/PROGRESS.md i napomeni da je
  staging refresh (2026-08-06, delimičan preko FTP-a zbog kvote) zatvoren.

  git add -A && git commit -m "cpanel-live: staging.antasline.com delimican refresh (kod+uploads-diff, FTP kvota radionica)" && git push

NE RADI: ništa na public_html-u ili antasline_novabaza bazi. Ako nešto ne
uspe (npr. DB lozinka nedostaje, docroot konfuzija, md5sum provera padne),
STANI i zapiši tačno šta blokira u dnevnik umesto da nagađaš/zaobilaziš.
```

## Posle (Miroslav)
1. Otvori Claude Code na cPanel terminalu (fajlovi su VEĆ na FTP-u, ne treba
   ništa dodatno otpremati), nalepi blok gore.
2. Kad završi i pushuje, ovde `git pull` pa mi javi — proveravam rezultat
   preko HTTP-a na `staging.antasline.com`.
