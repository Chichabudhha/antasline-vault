---
tip: sesija
alat: claude-code
datum: 2026-08-10
blok: "-"
status: zavrseno
oblast: W3 3.10
naslov: Full regression nad celim buildom — 195 stranica, 4 bag-a nađena i popravljena
---

# W3 3.10 — full regression + pre-migration checklist

Prvi **pun** regression prolaz nad lokalnim buildom (raniji, od 2026-07-22, bio je
„rani start" i pokrivao 214 URL-ova ručno prikupljenih; ovaj kreće od sitemap-a i
meri svaku stranicu po istim kriterijumima).

## Šta je urađeno

### 1. Sweep alat
`migracija/alati/regression-sweep.php` — read-only (samo GET/HEAD), ne dira bazu.
Tri faze: sitemap_index → sve stranice (status, broj H1, JSON-LD validnost,
title/metadesc, popis slika i internih linkova) → HEAD nad svakom jedinstvenom
slikom i internim linkom.

**Namerno je napisan da se ponovo pokrene protiv produkcije posle migracije**
(promeniti `$BASE`) i uporedi sa baseline-om — to je stavka B6 u
[[migracija/2026-08-10-pre-migration-checklist]].

Baseline snimljen: `analiza/2026-08-10-regression-baseline-pages.csv` (195 redova)
+ `…-assets.json`.

### 2. Nalazi i popravke

| # | Nalaz | Obim | Status |
|---|---|---|---|
| 1 | `/spoljne-podne-obloge/` (bez j) → 404 u footeru | **svih 195 stranica** | ✅ popravljeno |
| 2 | 5 slika 404 (`2025/10/*`, `2022/05/teg-na-podu.jpg`) | 2 stranice | ✅ popravljeno |
| 3 | 3 interna linka idu kroz 301 na stari slug | 3 stranice | ✅ popravljeno |
| 4 | 27 `*.bak-*` fajlova u `wp-content` se serviraju kao izvorni kod | ceo build | ✅ pokriveno u paket-skripti |
| 5 | 6 stranica bez meta opisa (uklj. početnu) | 6 stranica | 🟡 #ceka-miroslav |

🔴 **Nalaz 1 — dva widget-a, ne jedan.** Isti tip bug-a koji je 2026-08-07 nađen na
staging-u (footer „Terase i dom"). Prvi prolaz popravke gledao je samo
`widget_text` („Navigacija") — i provera je i dalje javljala 404, jer je drugi
pogodak bio u `widget_custom_html` (kolona „Podovi"). **Pouka: kod footer/widget
linkova nikad ne pretpostavljati jedan izvor — proći kroz sve `widget_*` opcije.**
Skripta to sada radi. Odluka od 2026-07-30 („kanonski slug ostaje `spoljnje-` sa j")
je i dalje ta koja važi; ovo je bio zaostali link, ne nova odluka.

🟢 **Nalaz 2 — rešeno bez izmene sadržaja.** Sve 3 datoteke postoje na live-u
(provereno HTTP-om), pa su originali povučeni na **tačne putanje** u
`wp-content/uploads`. Alternativa je bila prepisivanje `<img src>` na `-1`
varijante koje postoje lokalno (artefakt F3 reimporta) — odbačeno jer bi
razišlo lokalni sadržaj od live-a bez potrebe. Nula izmena u bazi za ovaj nalaz.

🔴 **Nalaz 4 — izmereno, nije pretpostavka.** `functions.php.bak-2026-08-10-…`
vraća **HTTP 200 i 53 KB čistog PHP izvora** (Apache ne izvršava `.bak-*`).
Provereno da unutra **nema kredencijala** (jedini pogoci su imena promenljivih
tipa `$token = get_post_meta`), ali sadržaj otkriva logiku court-builder tokena,
honeypota i rate-limita. `build-staging-package.sh` ih je do danas sve pakovao.

### 3. Paket-skripta — dva exclude pravila dodata
`migracija/alati/build-staging-package.sh`:
- `wp-content/mu-plugins/al-local-mail-log.php` — 🔴 **ovo je već jednom udarilo**:
  otišao je na staging u V3 paketu 2026-08-07 i forme tamo nisu stvarno slale
  mejlove. Komentar u samom fajlu tvrdi „mu-plugins se ne prenose" — netačno,
  prenose se sa `wp-content`, i to je bio uzrok.
- `*.bak-*` / `*.orig` / `*.old` / `*~` (nalaz 4)

Fajlovi ostaju na lokalu gde su i dalje potrebni; samo više ne mogu u paket.
`al-harness.html` je bio dvostruko pokriven (root whitelist ga ionako ne uzima).

### 4. Pre-migration checklist
[[migracija/2026-08-10-pre-migration-checklist]] — podeljena na **A: do 21.08**
(backup, rollback plan, EC toggle, priprema URL audita) i **B: dan migracije** po
redosledu izvršenja. Sadrži i stavku koju do sada niko nije testirao: **court
builder mejl klijentu (PNG+PDF) nikad nije poslat pravim SMTP-om** — lokalno je
uvek išao kroz mail-log presretač.

## Verifikacija (pun ponovni prolaz posle popravki)

```
195 stranica
status (non-200)        0
bez H1                  0
sa 2×H1                 0
nevalidan JSON-LD       0
sirov JSON-LD u tekstu  0
bez <title>             0
slomljene slike         0 / 1.182
interni 404             0 / 1.145
bez meta opisa          6   ← jedina otvorena stavka
```

Dodatno, van sweep-a: GTM `GTM-TRDT8K9` + consent kod prisutni na svih 5 tipova
stranice · CF7 forme rade na `/kontakt/` i `/industrijski-podovi/` · Enhanced
Conversions `al_lead_*` kod prisutan sitewide.

Jedini preostali „301" u izveštaju je `http://localhost/antasline` → `/antasline/`,
tj. `home_url()` bez kose crte. **Nije bag i ne postoji na produkciji** —
provereno: `https://www.antasline.com` vraća 200 bez preusmeravanja. Lokalni 301
je artefakt instalacije u pod-folderu.

## Gotcha-i (upisani i u [[reference/naucene-lekcije]])

1. **`strip_tags()` zadržava sadržaj `<script>`** — prva verzija provere „sirov
   JSON-LD u vidljivom tekstu" (F7.15 obrazac) davala je lažni pozitiv na svih
   195 stranica. Script/style blokovi se moraju ukloniti pre `strip_tags()`.
2. **Regex delimiter `#` uz `#` u klasi** → „Unknown modifier ')'". Filter za
   `mailto:`/`tel:`/`#` sidra je tiho padao.
3. **Footer linkovi žive u više `widget_*` opcija** (v. nalaz 1).

## Otvorene akcije

- [ ] 🟡 **6 stranica bez meta opisa, uključujući početnu (16550)** #ceka-miroslav
      `rank_math_description` je prazan i Rank Math nema fallback za njih.
      Ostale: `/vestacka-trava/`, `/vestacka-trava-za-fudbal/`,
      `/sportske-podloge/bergo-ultimate/`, `/aktuelnosti/`, `/ftalati-…/`.
      Copywriting, ne mehanička izmena → nije rađeno bez odluke.
      **Rok: content freeze 16.08.** Odgovor koji tražim: „napiši ih" ili „ostavi".
- [ ] Preostali deo 3.10 (dan migracije) #claude-code — izvršava se 24.08 po
      [[migracija/2026-08-10-pre-migration-checklist]], ne u ovoj sesiji.
- [ ] Ponoviti isti sweep protiv produkcije posle migracije #claude-code
      (stavka B6 checkliste) i uporediti sa baseline-om od danas.

## Backup i skripte

- `antasline-backups/antasline_local_2026-08-10_pre-w3-310-regression-fix.sql` (36 MB)
- `migracija/alati/job-w3-310-regression-fix.php` (idempotentna, ima `--dry-run`)
- `migracija/alati/regression-sweep.php` (read-only)

## Veze
[[2026-07-06-MASTER-PLAN-V2]] · [[migracija/2026-08-10-pre-migration-checklist]] ·
[[migracija/rollback-plan]] · [[reference/naucene-lekcije]] · [[PROGRESS]]
