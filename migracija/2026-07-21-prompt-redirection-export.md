---
tip: prompt-za-cpanel
datum: 2026-07-21
namena: izvoz 65 Redirection-plugin pravila sa live sajta u CSV (read-only)
status: spreman-za-izvrsenje
---

# Prompt za cPanel Claude Code sesiju — izvoz Redirection pravila

> Nalaz iz popisa 2026-07-21 (W3 3.14): plugin **Redirection** na live sajtu
> ima 65 postojećih pravila koja nisu bila uračunata u lokalni parity/redirect
> rad. Treba ih videti da bi se odlučilo da li ih spojiti sa migracionim
> `.htaccess`-om ili se preklapaju. Ovaj zadatak je **isključivo čitanje** —
> ne menja ništa na produkciji.

## Koraci (izvršiti u cPanel terminalu, u `~/public_html`)

1. `cd ~/antasline-vault && git pull --no-edit` (standardni preduslov)
2. Potvrdi tačno ime tabele (prefiks može biti drugačiji od `wpGs_` koji koristi
   lokalni build):
   ```
   wp db query "SHOW TABLES LIKE '%redirection_items%'"
   ```
3. Izvezi sva pravila u CSV (koristi ime tabele iz koraka 2). Kolone koje su
   bitne: `id`, `url` (izvor), `action_data` (cilj — često serialized/JSON,
   ne brini ako je sirov), `regex`, `status`, `match_type`, `group_id`,
   `last_count`, `last_access`. Primer (prilagodi ime tabele):
   ```
   wp db query "SELECT id, url, action_data, regex, status, match_type, group_id, last_count, last_access FROM wp_redirection_items ORDER BY id" --skip-column-names > ~/antasline-vault/migracija/redirection-live-export-2026-07-21.tsv
   ```
   (Ako `wp db query` ne podržava CSV/TSV export direktno na ovoj verziji,
   koristi `--csv` fleg ako postoji, ili preusmeri kao gore u TSV — TSV je OK,
   samo treba da bude čitljivo.)
4. Brzi sanity broj (mora biti ~65 ili blizu):
   ```
   wc -l ~/antasline-vault/migracija/redirection-live-export-2026-07-21.tsv
   ```
5. Append u `[[DNEVNIK-NAPRETKA]]` (na vrh, po standardnom cPanel protokolu):
   ```
   ## 2026-07-21 [cpanel-live] [W3 3.14 nastavak] — Redirection pravila izvezena (read-only) ✅
   - Izvezeno svih N pravila iz `<ime tabele>` u migracija/redirection-live-export-2026-07-21.tsv
   - Bez izmena baze/plugina — čisto SELECT.
   ```
6. `git add -A && git commit -m "cpanel-live: izvoz 65 Redirection pravila (read-only, W3 3.14 nastavak)" && git push`

## Šta NE raditi
- Ne menjati/brisati nijedno Redirection pravilo.
- Ne dirati `.htaccess` na live-u.
- Ne praviti pretpostavke o tome koja pravila su duplikati — to je sledeći
  korak (analiza), posle ovog izvoza, na lokalu.

## Sledeći korak (posle izvoza, radi se lokalno/CC)
Uporediti `redirection-live-export-2026-07-21.tsv` sa
`migracija/redirect-mapa-FINAL.csv` i `htaccess-301-DRAFT.txt` — naći
preklapanja/konflikte pre nego što se odluči da li se 65 pravila
prenose u migracioni `.htaccess` ili ostaju u Redirection plugin-u (koji
mora biti reaktiviran posle migracije da bi i dalje radila).
