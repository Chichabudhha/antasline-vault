---
tip: migracija
datum: 2026-07-21
namena: HITNO — obezbediti fajl sa DB kredencijalima nađen u public_html na live serveru, pa nastaviti probu migracije (M6/3.14) ako se pokaže da je lozinka za antasline_staging
izvor: Miroslav je fajl `ftp-staging-creds.txt` (uprkos imenu — sadrži DB kredencijale) našao u /home/antasline/public_html preko cPanel Claude Code sesije
---

# Prompt za cPanel Claude Code — hitno obezbediti izloženi fajl sa DB kredencijalima

Preduslov: fajl `ftp-staging-creds.txt` postoji u `/home/antasline/public_html/` —
to je **live docroot**, potencijalno javno dostupan preko
`https://antasline.com/ftp-staging-creds.txt`. Sadrži DB kredencijale (ne FTP,
uprkos imenu). Ovo je bezbednosni nalaz, prioritet ispred nastavka migracije.

Kopiraj sve iz bloka ispod u Claude Code sesiju na cPanel terminalu:

```
Radiš NA cPanel produkcionom serveru (wp1.oblak.host, nalog antasline).

STRIKTNO: ovo je bezbednosna intervencija na live public_html — jedina
dozvoljena akcija na public_html u ovoj sesiji je UKLANJANJE ovog jednog
fajla. Ne diraj ništa drugo u public_html niti u antasline_novabaza bazi.

KORAK 1 — proveri javnu izloženost PRE bilo kakve izmene:
  curl -I https://antasline.com/ftp-staging-creds.txt
  Zapiši status kod (200 = bio je javno dostupan, 403/404 = nije bio
  direktno servi ran, npr. zbog .htaccess pravila).

KORAK 2 — pročitaj sadržaj (samo da utvrdiš NA KOJU bazu se odnosi, ne
  štampaj lozinku u odgovoru van ove sesije):
  cat /home/antasline/public_html/ftp-staging-creds.txt
  Utvrdi: da li je DB_NAME `antasline_staging` (proba migracije) ili
  `antasline_novabaza` (živa produkciona baza) ili nešto treće. Ovo je
  ključna razlika za sledeći korak.

KORAK 3 — ukloni fajl iz public_html ODMAH (bez obzira na KORAK 1 rezultat):
  mv /home/antasline/public_html/ftp-staging-creds.txt /home/antasline/staging-db-credentials.txt
  (premesti ga van bilo kog web-servable docroot-a, u home folder — NE
  brisati odmah, možda ga koristimo u sledećem koraku)

KORAK 4a — AKO se u KORAKU 2 pokazalo da su to kredencijali za
  `antasline_staging`/`antasline_antasline` (proba migracije, očekivano):
  Nastavi tačno KORAK 2 iz [[migracija/2026-07-21-prompt-subdomen-import]]:
  wp config create --dbname=antasline_staging --dbuser=antasline_antasline \
    --dbpass=<lozinka iz fajla> --dbhost=localhost --dbprefix=wpGs_ \
    --path=/home/antasline/staging --skip-check
  Zatim nastavi Korake 3–9 iz istog prompta (import baze, URL rewrite,
  Basic Auth, čišćenje, verifikacija, vault append) bez ponovnog pitanja.

KORAK 4b — AKO su to kredencijali za `antasline_novabaza` (živa baza) ili
  bilo šta povezano sa produkcijom koja radi upravo sada:
  STANI. Ne koristi ih ni za šta. Ovo je ozbiljan nalaz — lozinka žive baze
  je bila u javnom docroot-u. Zapiši u dnevnik i traži od Miroslava da ODMAH
  promeni tu lozinku preko cPanel MySQL Databases UI (i ažurira
  wp-config.php na public_html-u sa novom lozinkom u istom koraku, inače
  live sajt puca).

KORAK 5 — vault:
  Append u ~/antasline-vault/DNEVNIK-NAPRETKA.md na vrh: rezultat KORAKA 1
  (javna izloženost da/ne), koja baza je bila u fajlu (4a ili 4b), šta je
  urađeno dalje, BEZ lozinke u tekstu. Ažuriraj PROGRESS.md (Blokeri
  sekcija) i 2026-07-06-MASTER-PLAN-V2.md (red 3.14) ako je 4a granata
  migracija nastavljena do kraja.

  git add -A && git commit -m "cpanel-live: hitno uklonjen izlozeni DB creds fajl iz public_html, [4a nastavljena migracija / 4b live lozinka treba rotaciju] (M6/3.14)" && git push

NE RADI: ništa drugo u public_html-u ili antasline_novabaza bazi van onoga
što je gore eksplicitno opisano. Ako nešto ne uspe, STANI i zapiši tačno šta
blokira.
```

## Posle (Miroslav)
1. Kopiraj blok gore u Claude Code na cPanel terminalu.
2. Kad završi i pushuje, `git pull` ovde pa mi javi ishod (4a ili 4b).
3. Ako je 4b (live lozinka bila izložena) — to postaje novi prioritet #1, ispred nastavka probe migracije.
