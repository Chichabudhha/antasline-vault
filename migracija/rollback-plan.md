---
tip: plan
naziv: Rollback plan — migracija 2026-08-31
datum: 2026-07-27
status: draft — čeka Miroslavljevu potvrdu detalja pre N7 content freeze-a
---

# 🔙 Rollback plan — ako migracija pukne

Deo gate kriterijuma [[2026-07-06-MASTER-PLAN-V2]] §3. Cilj: **live sajt vraćen u
prethodno stanje za <1h** ako se posle prebacivanja (3.11) otkrije kritičan
problem (sajt dole, WooCommerce/porudžbine slomljene, baza oštećena,
kritičan gubitak sadržaja).

## 1. Kad se poziva rollback (trigger)

Miroslav donosi odluku, ali sledeći znaci su automatski "da":
- Sajt vraća 500/white screen duže od 5 min bez očiglednog brzog fix-a
- Baza ne učitava (konekcija puca, tabele nedostaju/korumpirane)
- Forme/kontakt ne rade i ne mogu se brzo popraviti (izgubljen lead-tok)
- Nešto kritično od sadržaja nestalo bez brzog uzroka (npr. loš SQL import)

Kozmetički bagovi (razmak, boja, 1 slomljena slika) **NE** povlače rollback —
popravljaju se uživo posle migracije (v. 3.12 post-live monitoring).

## 2. Šta MORA postojati PRE migracije (prereq, ne posle)

- [ ] **Svež pun live backup** (DB + `wp-content` + `.htaccess`/`wp-config.php`) —
  uzet **istog dana** ili dan pre migracije, ne stariji izvezeni fajl. Poslednji
  poznati ručni backup na serveru je od 2026-07-10 (`~/backups/`, star 17+ dana
  na dan pisanja ovog plana) — **nedovoljno svež**, mora se ponoviti tik pre 3.11.
- [ ] Taj backup **preuzet lokalno** (ne samo ostavljen na serveru — ako server
  padne, backup na istom disku ne pomaže) — najmanje **2 lokacije**: server
  (`~/backups/`) + lokalni PC/eksterni HDD (isti `G:\` Maxtor koji već čuva
  build backup-e, v. 3.13).
- [ ] Backup **testiran da se raspakuje/importuje** bar jednom pre dana migracije
  (ne prvi put probati import usred krize) — dovoljan brz test: importovati DB
  dump u praznu test-bazu i proveriti da WP učita bez greške.
- [ ] cPanel/SSH pristup potvrđen **na dan migracije** (ne samo da je radio
  2026-07-21 — proveriti da kredencijali/2FA i dalje važe)
- [ ] Otvoreno pitanje iz M6 (Master Plan §4): **WHM Backup Wizard/JetBackup
  raspored na serveru i dalje nije potvrđen** (PROGRESS Blokeri, nalaz
  2026-07-21) — proveriti pre migracije da li hosting sam pravi snapshot-e;
  ako da, to je dodatna (3.) linija odbrane, ne zamena za ručni backup iznad.

## 3. Koraci rollback-a (redosled, cilj <1h)

Sve ovo je `[cpanel-live]` rad — izvršava se u eksplicitnoj cPanel-live sesiji,
ne odavde. Redosled:

1. **Zaustavi krvarenje** (2 min) — ako je moguće, uključi Maintenance
   mode/privremenu statičku stranicu umesto da posetioci vide grešku dok se
   radi popravka
2. **Vrati bazu** (10–15 min) — import `mysqldump` fajla preko `wp db import`
   (WP-CLI, ako dostupan na serveru) ili phpMyAdmin import; ako je stara baza
   još netaknuta, prvo `wp db export` trenutnog (poremećenog) stanja kao
   safety-net PRE prepisivanja — ne brisati dokaz šta je pošlo po zlu
2. **Vrati fajlove** (10–20 min, zavisi od veličine/brzine servera) — raspakuj
   `wp-content` iz backup arhive preko File Manager-a ili SSH `unzip`,
   prepiši trenutni (slomljeni) `wp-content`
3. **Vrati konfiguraciju** (5 min) — `.htaccess` i `wp-config.php` na
   pre-migracionu verziju ako su menjani (URL rewrite, DB kredencijali)
4. **Očisti keš** (5 min) — LiteSpeed cache purge (`wp litespeed-purge all`
   ili LSCWP UI), i ako postoji bilo kakav CDN/edge keš (proveriti da li
   trenutni hosting ima CDN sloj — nije potvrđeno u dosadašnjim popisima)
5. **Verifikuj** (10 min) — homepage 200, 3–5 nasumičnih stranica 200,
   kontakt forma šalje, GA4 real-time pokazuje traffic, DNS/SSL i dalje važe
6. **Javi Miroslavu** da je rollback završen + kratak nalaz šta je pošlo po zlu
   (za post-mortem, ne za ponovni pokušaj migracije istog dana)

**Budžet vremena**: ~35–50 min za korake 1–5 na papiru — realno zavisi od
brzine servera (isti `wp1.oblak.host` gde je 3.14 proba migracije trajala
~34 min samo za zip/import dela, v. DNEVNIK 2026-07-21) — **<1h cilj je
ostvariv ali TESAN**, ne ostavljati rezervu za iznenađenja ako se migracija
radi kasno uveče kad niko drugi nije dostupan za pomoć.

## 4. Ko odlučuje

Miroslav donosi go/no-go odluku za rollback (nije automatska stavka). Ako
CC sesija otkrije trigger-uslov iz §1 tokom post-live monitoringa (3.12), javlja
odmah i predlaže rollback, ali ne izvršava bez njegove eksplicitne potvrde —
isto pravilo kao svaka druga nepovratna akcija.

## 5. Otvoreno (čeka Miroslava pre N7 content freeze-a)

- [ ] Potvrditi da li hosting (WHM) ima sopstveni automatski backup/snapshot
  (M6 otvoreno pitanje) — ako da, dodaje dodatnu sigurnosnu mrežu
- [ ] Potvrditi da li postoji CDN/edge keš sloj ispred `wp1.oblak.host` (nije
  ranije popisano) — utiče na korak 4 (čišćenje keša)
- [ ] Odlučiti KO fizički izvršava rollback ako Miroslav nije dostupan u tom
  trenutku (backup kontakt na hostingu?) — trenutno nema odgovora

## Veze
[[2026-07-06-MASTER-PLAN-V2]] §3 (gate kriterijumi) · [[PROGRESS]] Blokeri (WHM backup pitanje) · W3 3.11 (migracija) · W3 3.13 (noćni backup builda)
