---
tip: plan
naziv: Rollback plan — migracija 2026-08-24
datum: 2026-07-27
azurirano: 2026-08-11 (sva 3 otvorena pitanja zatvorena)
status: ✅ zatvoren 2026-08-11 — gate stavka ispunjena pre roka 15.08
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
- [x] ✅ **ZATVORENO 2026-08-11 — hosting ima sopstveni automatski backup.**
  Pitanje je zapravo bilo odgovoreno još 2026-07-27 (M proverio direktno u
  cPanel-u, upisano u [[2026-07-06-MASTER-PLAN-V2]] §4 red M6, ali nije
  preneto ovamo): **JetBackup 5**, cPanel-dostupan (ne WHM-only), **dnevni**
  backup, **remote/off-site** lokacija kod provajdera, **90 dana retencije**.
  To je treća linija odbrane — **ne zamenjuje** ručni backup iznad, jer je
  granularnost dnevna (najgori slučaj: gubitak do 24h live sadržaja) i zavisi
  od provajderove infrastrukture koja je možda deo istog incidenta.
  🟢 Praktična posledica za dan migracije: **pre 3.11 proveriti u cPanel →
  JetBackup da poslednji snapshot nije stariji od 24h** — to je besplatan
  drugi primerak i ako ga ima, ručni backup je duplo osiguran.

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
4. **Očisti keš** (5 min) — ✅ **razrešeno 2026-08-11: CDN/edge sloja NEMA**,
   jedini keš je LiteSpeed na samom serveru. Dakle: `wp litespeed-purge all`
   (ili LSCWP UI → Purge All) i to je ceo korak — nema drugog sloja koji bi
   posle purge-a i dalje servirao staru verziju.
   *Dokaz:* `antasline.com` i `www` razrešavaju direktno na `138.201.234.168`
   (Hetzner, nameserveri `ns1–ns4.oblak.host`), bez CNAME na CDN; HTTP
   zaglavlja live sajta daju `Server: LiteSpeed` + `X-LiteSpeed-Cache: hit`,
   a **nema** `cf-ray`, `via`, `age` ni `x-qc-cache` (QUIC.cloud) — tj. ni
   Cloudflare ni QUIC.cloud CDN nisu ispred sajta.
   ⚠️ Ako se pre migracije uključi QUIC.cloud CDN (LSCWP ga nudi u istom
   pluginu), ovaj korak se menja — tada purge mora i na QUIC.cloud strani.
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

🔵 **Odluka M 2026-08-11: odluku i izvršenje drži isključivo Miroslav, bez
zamene — zato je njegova dostupnost uslov za pokretanje migracije** (v. §5).

Miroslav donosi go/no-go odluku za rollback (nije automatska stavka). Ako
CC sesija otkrije trigger-uslov iz §1 tokom post-live monitoringa (3.12), javlja
odmah i predlaže rollback, ali ne izvršava bez njegove eksplicitne potvrde —
isto pravilo kao svaka druga nepovratna akcija.

## 5. Otvoreno (čeka Miroslava — **rok 2026-08-15**, pre content freeze-a 16.08)

- [x] ✅ **ZATVORENO 2026-08-11** — hosting automatski backup: **JetBackup 5,
  dnevni, off-site, 90 dana retencije** (v. §2). Odgovor je postojao od
  2026-07-27 u M6 redu master plana, samo nije bio prenet ovamo.
- [x] ✅ **ZATVORENO 2026-08-11** — **CDN/edge sloja nema**, jedini keš je
  LiteSpeed na serveru (v. korak 4 za dokaz). Korak 4 je time pojednostavljen.
- [x] ✅ **ODLUČENO 2026-08-11 (M): „migracija samo kad sam tu."** Nema
  rezervne osobe i ne traži se od hostinga ništa unapred — **umesto toga,
  dostupnost Miroslava postaje uslov za pokretanje migracije.** Opcija 3 od
  tri ponuđene; opcije 1 (rezervna osoba sa pristupom) i 2 (tiket hostingu
  unapred) svesno odbačene.

  **Šta ovo znači operativno — obavezno na dan migracije (3.11):**
  - Migracija se **ne pokreće** ako Miroslav nema ispred sebe ~6h slobodnih
    (rollback budžet je 35–50 min, ali tek posle dijagnostike i odluke).
    Kasno popodne/veče PON 24.08 nije prihvatljiv start.
  - Ako se pokaže da tog dana nema tog prozora → migracija se **pomera**, isto
    pravilo kao gate ("ne gura se na silu").
  - Ako Miroslav postane nedostupan **usred** incidenta, ostaje jedino
    hosting podrška oblak.host / JetBackup restore — **kao improvizacija, ne
    kao pripremljen put**. Njihov SLA i kanal van radnog vremena namerno nisu
    proveravani unapred; to je prihvaćen rizik ove odluke.

## Veze
[[2026-07-06-MASTER-PLAN-V2]] §3 (gate kriterijumi) · [[PROGRESS]] Blokeri (WHM backup pitanje) · W3 3.11 (migracija) · W3 3.13 (noćni backup builda)
