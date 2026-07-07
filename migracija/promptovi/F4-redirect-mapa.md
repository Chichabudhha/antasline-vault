---
tip: prompt
faza: F4
naziv: Minimalna redirect mapa + .htaccess (ne aktivira se!)
menja-bazu: ne (osim eventualnih slug rename posle Miroslavljevih odluka)
preduslov: F1–F3 gotovi · 🔴 Miroslav u sesiji (granične odluke)
---

# F4 — Minimalna redirect mapa

**Cilj:** iz `migracija/parity-inventar.csv` izvući SAMO redove gde se live i lokalni
URL namerno razlikuju → `migracija/redirect-mapa-FINAL.csv` (~10–20 redova umesto
starih 118) + generisan `.htaccess` blok koji se **NE aktivira do dana migracije**.
Strategija i hibrid pravilo: [[migracija/PARITY-PLAN]] §1 (P1).

## Hibrid pravilo (podsetnik)

- **Top ~15 live URL-ova po GSC klikovima → strogi parity** (lokalni slug se vraća na live slug, red NESTAJE iz mape)
- Nizak saobraćaj → novi slug sme ako je jasno bolji, uz 301 red
- Konsolidacije duplikata → uvek dozvoljene, uz 301

## Koraci

1. Učitaj `migracija/parity-inventar.csv`, sortiraj po `gsc_klikovi`.
2. **Granični slučajevi — prezentuj Miroslavu tabelu i sačekaj odluku po stavci:**
   | Live URL | Lokal | GSC težina | Opcije |
   |---|---|---|---|
   | `/spoljnje-podne-obloge/` | `/spoljne-podne-obloge/` | ~11k impr — verovatno top 15 | (a) vrati "j" = parity · (b) zadrži ispravku + 301 |
   | `/podovi-za-parkiraliste-i-staze/` | `/podloge-za-parkiraliste-i-staze/` | niska | (a) rename lokal na "podovi" · (b) 301 |
   | `/padel-tereni/` (page) | `padel-tenis` (draft post iz F3) | srednja | (a) rebuild pod live slugom · (b) novi slug + 301 |
   | `/sportski-podovi-za-sale-i-balone/` | `...-za-skole-i-sportske-sale` | niska | novi slug bolji → 301 (preporuka) |
   | duplikati `izbor-industrijskog-poda-...` (live ima 2 verzije) | konsolidacija u jedan | — | oba live URL-a → 301 na konsolidovani |
   | bergo-easy/elite/unique/xl (live pages) | `bergo-ultimate` | — | konsolidacija → 301 (potvrđeno ranije) |
   | `/kako-izabrati-pravi-...-poterbama` (draft) | typo u slugu | — | obrisati ili konsolidovati |
3. Posle odluka: gde je odluka "parity" → izvrši slug rename na lokalu (backup pre!),
   red ne ulazi u mapu; gde je "301" → red ulazi u `redirect-mapa-FINAL.csv`:
   ```
   stari_url;novi_url;razlog;gsc_klikovi
   ```
4. **Posebni slučajevi koji UVEK idu u mapu** (iz stare mape, i dalje važe):
   - `/sportske-podloge/kosarkaske-konstrukcije/` (478 kl.) → cilj = nova landing iz F5 (NE tanka shop kategorija!)
   - live proizvod-stranice koje su lokalno Woo proizvodi (bergo-easy itd.) → 301 na `/proizvod/<slug>/` ili konsolidovanu stranicu
   - `/na-kojoj-podlozi-se-igraju-turniri-u-3x3/` → `/teren-za-basket-3x3/` (ako se ne uveze kao post — proveri F3 ishod)
5. **Generiši `.htaccess` blok** iz finalne mape (Redirect 301 / RewriteRule), snimi kao
   `migracija/htaccess-301-DRAFT.txt`. 🔴 **NE dodavati u živi .htaccess** — aktivira se
   isključivo na dan migracije (MASTER-PLAN 3.9/3.11).
6. Testiraj logiku mape na lokalu: za svaki `novi_url` → curl 200.

## Verifikacija

- [ ] Svaki red mape: `novi_url` vraća 200 na lokalu
- [ ] Nema redirect lanaca (novi_url se ne pojavljuje ni u jednom starom_url)
- [ ] Zbir provere: svaki live URL iz inventara je ili PARITY ili u mapi ili NEDOSTAJE-LOKAL (za F5)
- [ ] `.htaccess` draft sintaksno ispravan (test na lokalnom Apache-u u izolovanom folderu ako moguće)

## Zatvaranje sesije

1. [[DNEVNIK-NAPRETKA]] — unos NA VRH (`[W3 PARITY F4]`): odluke po graničnom slučaju, broj redova finale mape
2. [[PROGRESS]] + štikliraj F4 u [[migracija/promptovi/_README]]
3. Upiši odluke u `odluka` kolonu parity-inventar.csv
4. Sada sme brisanje originala starih mapa iz `C:\xampp\htdocs\antasline\` (uz Miroslavljevu potvrdu) — arhivske kopije su u `migracija/arhiva/`
