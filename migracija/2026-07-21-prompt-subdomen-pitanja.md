---
tip: migracija
datum: 2026-07-21
namena: prompt za Claude Code na cPanel terminalu — pitanja pre probne migracije na staging.antasline.com (M6/3.14)
---

# Prompt za cPanel Claude Code — pitanja pre probe migracije (subdomen)

Kopiraj sve iz bloka ispod u Claude Code sesiju na cPanel terminalu:

```
Radiš NA cPanel produkcionom serveru (wp1.oblak.host, nalog antasline).

KONTEKST: Miroslav je odobrio probu migracije lokalnog WoodMart builda
(C:\xampp\htdocs\antasline, Windows mašina) na prazan subdomen
staging.antasline.com (docroot /home/antasline/staging, kreiran 2026-07-21,
prazan folder bez index.php). Cilj sledeće faze: preneti WP fajlove + bazu sa
lokala na taj subdomen kao generalnu probu migracije pre 2026-08-31. Claude
Code na lokalnoj (Windows) mašini ne može direktno da se poveže SSH-om odavde
— pokušaj ka wp1.oblak.host:22 je timeout-ovao — pa su ti pitanja ispod
preduslov pre nastavka.

STRIKTNO READ-ONLY prema POSTOJEĆEM sadržaju: public_html (živi sajt) i baza
antasline_novabaza se NE DIRAJU ničim. Jedini izuzetak gde smeš da PRAVIŠ nove
stvari je unutar /home/antasline/staging i eventualno nova MySQL baza za
staging (vidi pitanje 5) — sve ostalo je read-only istraga.

PITANJA — odgovori upiši u nov fajl
~/antasline-vault/migracija/2026-07-21-cpanel-odgovori-subdomen.md, jasno
numerisano 1-6 + zaključak na kraju:

1. SSH pristup spolja: proveri cPanel "SSH Access" / "Manage SSH Keys"
   stranicu (ili `uapi SSH` ako je dostupno sa ove naloga) — koji HOST i koji
   PORT bi eksterni klijent (scp/rsync/sftp sa Windows mašine) trebalo da
   koristi da se poveže na ovaj nalog? Je li SSH uopšte omogućen za ovaj
   cPanel nalog spolja, ili je pristup ograničen samo na cPanel Terminal
   (jailed shell u browseru, bez pravog spoljnjeg SSH-a)? Ako postoji
   ograničenje po IP adresi (firewall/cPHulk/Imunify360 — već je viđen sličan
   problem 2026-07-10 sa QUIC.cloud IP-om), navedi to eksplicitno.

2. Da li postoji FTP/SFTP nalog kreiran za ovaj cPanel account (cPanel → FTP
   Accounts) kao alternativa SSH-u za prenos fajlova? Ako da, koji host/port
   se koristi.

3. Da li je `wp-cli` (`wp`) dostupan u ovoj shell sesiji? (`which wp` ili
   `wp --info`) Koja verzija — treba nam i za rad na novom staging installu,
   ne samo na postojećem public_html.

4. Potvrdi da je /home/antasline/staging i dalje prazan (bez index.php,
   provera `ls -la`) i koliko slobodnog prostora ima /home mount TRENUTNO
   (`df -h /home`) — za procenu da li stane pun build (WP core + tema +
   uploads + baza, red veličine par GB).

5. Da li možeš preko cPanel API-ja (`uapi Mysql create_database`,
   `create_user`, `set_privileges_on_database`) da napraviš NOVU praznu MySQL
   bazu + korisnika ZA STAGING, bez da diraš postojeću `antasline_novabaza`
   ili njenog korisnika? Ako da:
   - uradi to sada (ime baze npr. `antasline_staging`, korisnik nasumično
     generisan)
   - LOZINKU NE PIŠI u vault/git — sačuvaj je u fajlu VAN vault-a, npr.
     `~/staging-db-credentials.txt` (obično vlasništvo/read samo za ovaj
     nalog), i u odgovoru u vault-u navedi SAMO ime baze i korisničko ime +
     rečenicu "lozinka je u ~/staging-db-credentials.txt na serveru"

6. Ima li praktično ograničenje veličine upload-a kroz cPanel File Manager
   (Configuration → Max Upload Size, ili probaj da vidiš vrednost) — za
   slučaj da SSH/FTP ne rade i moramo ručno da upload-ujemo zip preko
   browsera.

Na kraju fajla dodaj jasan zaključak: koji metod prenosa fajlova preporučuješ
(SSH/SCP, SFTP, FTP, ili ručni File-Manager-upload) na osnovu onoga što si
zatekao, i zašto.

NE RADI: ništa na public_html-u, ništa na antasline_novabaza bazi, nijedan
plugin/config na živom sajtu, ništa što nije eksplicitno navedeno gore.

Kad završiš:
git add -A && git commit -m "cpanel-live: odgovori na pitanja za probu migracije subdomena (M6/3.14)" && git push
```

## Posle (Miroslav)
1. Kopiraj blok gore u Claude Code na cPanel terminalu, pusti da odgovori i pushuje.
2. Ovde (lokal/chat sesija) povuci: `git pull` u vault-u.
3. Javi mi da je pull gotov — nastavljam sa planiranjem/izvršenjem prenosa na osnovu odgovora.
