---
tip: migracija
datum: 2026-07-21
namena: odgovori sa cPanel terminala na pitanja pre probne migracije na staging.antasline.com (M6/3.14)
izvor: [[migracija/2026-07-21-prompt-subdomen-pitanja]]
---

# Odgovori — proba migracije na subdomen (cPanel, wp1.oblak.host)

Izvršeno READ-ONLY prema `public_html` i `antasline_novabaza` — ništa dirano. Napravljen je jedino ovaj fajl. Pitanje 5 (nova MySQL baza) **nije izvršeno** — objašnjenje ispod.

## 1. SSH pristup spolja

Nisam mogao direktno da pročitam `/etc/ssh/sshd_config` niti da izlistam WHM-nivo SSH podešavanja iz ove sesije (cPanel `uapi SSH` modul nema funkcije za to na nivou običnog naloga — `has_root`/`list_ssh` ne postoje kao uapi pozivi; to je WHM/root podešavanje, ne nešto što nalog sam sebi čita).

Posredan dokaz iz `/etc/passwd`:
```
antasline:x:1034:1037::/home/antasline:/usr/bin/zsh
```
Shell je **pravi `/usr/bin/zsh`, ne `jailshell`** — ovo je jak signal da nalog ima pravu SSH shell dozvolu (da je ograničeno na jailed/browser-only Terminal, shell bi bio `/usr/local/cpanel/bin/jailshell`).

Host: `wp1.oblak.host` (razrešava na `138.201.234.168` / IPv6 `2a01:4f8:fff1:1c::2`). Standardni cPanel SSH port je `22` — nisam mogao da potvrdim da li je promenjen (čitanje sshd_config blokirano restrikcijama ove sesije).

**Najverovatnije objašnjenje za raniji timeout sa Windows mašine (port 22):** IP-based blokada (cPHulk/Imunify360 firewall), ne isključen SSH. Ovo se poklapa sa poznatim presedanom iz 2026-07-10 (QUIC.cloud IP je bio blokiran istim mehanizmom — vidi `[[reference/naucene-lekcije]]`). Preporuka: proveri/whitelistuj trenutnu javnu IP adresu Windows mašine u cPanel → Security → IP Blocker / cPHulk pre sledećeg pokušaja.

## 2. FTP/SFTP nalog

`uapi Ftp list_ftp` vraća samo:
- `main` — glavni FTP nalog, homedir `/home/antasline` (koristi isti username/password kao cPanel login)
- `antasline_logs` — samo pristup log fajlovima, nije relevantno za prenos

**Nema posebno kreiranog FTP naloga za migraciju.** Glavni FTP nalog postoji podrazumevano (host `wp1.oblak.host` ili `ftp.antasline.com`, port 21 za FTP / 990 za FTPS), ali deli kredencijale sa cPanel nalogom — ako se koristi, preporučljivo je napraviti poseban FTP nalog ograničen na `/home/antasline/staging` (cPanel → FTP Accounts) radi izolacije.

## 3. wp-cli

Dostupan:
```
/usr/local/bin/wp
WP-CLI version: 2.12.0
PHP binary: /opt/cpanel/ea-php80/root/usr/bin/php (PHP 8.0.30)
```
Radi po direktorijumu (`--path=` ili pokretanje iz foldera) — nije vezan samo za postojeći `public_html` install, koristiv je i za novi staging install.

## 4. Staging folder + slobodan prostor

`/home/antasline/staging`:
```
drwxr-xr-x 3 antasline antasline 4096 Jul 21 15:56 .
drwxr-xr-x 3 antasline antasline 4096 Jul 21 16:18 ..
drwxr-xr-x 3 antasline antasline 4096 Jul 21 15:56 .well-known
```
Potvrđeno prazan (bez `index.php`) — `.well-known/acme-challenge` je auto-generisan od strane AutoSSL/Let's Encrypt prilikom kreiranja subdomena, nije sadržaj koji je neko dodao.

`/home` mount:
```
Filesystem            Size  Used Avail Use%
/dev/mapper/vg0-root  200G   82G  119G  41%
```
119 GB slobodno — više nego dovoljno za pun build (WP core + tema + uploads + baza, reda veličine par GB).

## 5. Nova MySQL baza za staging — ZATVORENO (kreirana ručno preko cPanel UI)

Automatsko kreiranje preko `uapi Mysql create_database` je prvobitno bilo blokirano sandbox/permission slojem ove sesije — nisam pokušavao zaobilazak (vidi napomenu ispod). Miroslav je bazu napravio ručno preko cPanel UI.

Potvrđeno naknadnim (read-only) `uapi Mysql list_databases`:
```
database: antasline_staging
disk_usage: 0
users: antasline_antasline
```
Baza postoji, prazna je, korisnik `antasline_antasline` je već pridružen sa privilegijama na nju. Odvojena je od `antasline_novabaza` (koju koristi `majmun22_miroslav`) — nema preklapanja.

**Lozinka za `antasline_antasline` nije zapisana ovde niti negde u vault-u/git-u** — Miroslav je ima kod sebe sa kreiranja preko UI. Kad se pravi `wp-config.php` na stagingu, lozinku treba uneti ručno (ili je privremeno staviti u fajl van vault-a na serveru, npr. `~/staging-db-credentials.txt`, pa obrisati posle upotrebe).

## 6. Max upload size (fallback File Manager)

`php.ini` koji koristi `ea-php80` (aktivna PHP verzija naloga):
```
upload_max_filesize = 512M
post_max_size = 512M
```
`uapi Filemanager get_uploaded_files_setting` nije dostupan na ovom serveru (modul nije instaliran), ali gornja php.ini vrednost je praktični limit i za File Manager upload. 512M je dovoljno za pojedinačne delove (npr. tema+plugin zip, SQL dump kompresovan), ali pun uploads folder će verovatno morati da se deli na više zip-ova ako se ide ovim putem.

## Zaključak — preporučeni metod prenosa

**Prioritet 1 — SSH/SCP ili SFTP-preko-SSH.** Nalog ima pravu shell dozvolu (zsh, ne jailshell), što znači da SSH arhitekturno treba da radi. Raniji timeout sa Windows mašine je najverovatnije IP/firewall blokada (poznat obrazac, cPHulk/Imunify360), ne isključen servis. **Sledeći korak: Miroslav proveri cPanel → Security → IP Blocker / cPHulk Protection i whitelistuje trenutnu javnu IP adresu Windows mašine, zatim ponovi `scp`/`sftp` pokušaj ka `wp1.oblak.host:22`** (WHM-nivo "SSH Access" stranicu ne mogu da proverim iz ove sesije — to zahteva pristup van uapi-ja dostupnog običnom nalogu).

**Prioritet 2 (fallback) — FTP** preko glavnog naloga (`wp1.oblak.host`, port 21, cPanel kredencijali) ili posebno kreiranog FTP naloga ograničenog na `/home/antasline/staging`. Radi bez dodatnih provera, sporije je za velike foldere (uploads) od SCP/SFTP.

**Prioritet 3 (poslednja opcija) — ručni File Manager upload** zip fajlova, limit 512MB po fajlu — zahteva deljenje builda (core+tema, plugins, uploads, SQL) na više arhiva. Koristiti samo ako i SSH i FTP ne uspeju.

**Status 2026-07-21 (dopuna):** pitanje 5 zatvoreno — `antasline_staging` baza kreirana ručno preko cPanel UI, korisnik `antasline_antasline` pridružen. Sledeći blokер je i dalje SSH pristup (Prioritet 1 gore) — Miroslav treba da proveri IP Blocker/cPHulk i ponovi SCP/SFTP pokušaj pre nego što se krene sa stvarnim prenosom fajlova.
