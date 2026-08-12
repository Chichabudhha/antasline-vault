---
datum: 2026-08-12
tip: sesija
tag: claude-code
oblast: W3 — migracija
status: zatvoreno
---

# W3 — `live-export.sh` gubio galerijske slike + prefiks baze ispravljen

Sedma sesija istog dana. Zatvara dva 🔴 blokera otvorena jutros (oba iz `agy`
pre-flight nalaza, v. [[dnevnik/2026-08-12-agy-antigravity-delegat]]).

## Šta je urađeno

### 1. Quick-win — prefiks baze `wpGs_` → `wpgs_`

Provereno **protiv baze**, ne prepisano iz dokumentacije:

- `SHOW TABLES` → `wpgs_posts` (78 tabela, sve `wpgs_`)
- `@@lower_case_table_names` = **1** → Windows ne razlikuje case, otud je pogrešan
  zapis godinu dana prolazio neprimećeno
- lokalni `wp-config.php:67` i dalje nosi `$table_prefix = 'wpGs_'` i **radi** —
  isključivo zbog gornjeg

Na Linux hostingu case se razlikuje; to je tačan uzrok „site not installed" greške
pri probi migracije 2026-07-21 (v. [[DNEVNIK-NAPRETKA]] tog datuma).

Ispravljeno u: `CLAUDE.md` §2 (+ nova 🔴 ograda) i §7.5 · `.claude/skills/antasline-sesija`
§3 · `.claude/skills/obogati-proizvod` (2 mesta). Usput ispravljen broj tabela
u §2: **106 → 78**.

🔴 **Bag nije bio samo u dokumentaciji:** `staging-import.sh:19` je imao
`STG_PFX="wpGs_"` — promenljivu kojom `sed` prepisuje imena tabela u dump-u pre
importa. To je doslovno scenario iz blokera („tiho uveze u pogrešne tabele"),
samo maskiran time što se skripta do sada pokretala na Windows-u. Ispravljeno.

`migracija/alati/job-plugin-cleanup-cron.php` i dalje piše `wpGs_options` —
jednokratna, već izvršena skripta; namerno nije dirana.

### 2. `live-export.sh` — galerijske slike

**Merenje pre popravke** (lokalna baza, ista struktura kao live):

| Stavka | Broj |
|---|---|
| proizvodi + varijacije | 245 |
| attachmenti koje skripta hvata (`post_parent` + `_thumbnail_id`) | 196 |
| galerijskih slika ukupno | 170 |
| **tiho bi nestalo iz exporta** | **145 (85%)** |
| slike kategorija (`termmeta.thumbnail_id`, `product_cat`) | 0 na lokalu |

**Popravke:**

- `GAL_IDS` — `_product_image_gallery` (zarezom razdvojena lista, split u shell-u)
- `CAT_THUMB_IDS` — slike `product_cat` kategorija; isti razred baga (nema
  `post_parent` vezu). Na lokalu ih nema, na live-u ih može biti — zato je kod dodat.
- **tvrda provera pred dump**: svaki galerijski ID mora biti u `ALL_ATTACH`, inače
  `exit 1`. Bolje da export pukne nego da se otkrije posle migracije.
- `PFX`/`OUT` pregazivi iz okruženja → skripta se **može testirati na lokalu**.

**Test uživo** (`PFX=wpgs_`, izlaz u scratchpad, baza netaknuta):
245 proizvoda · **341 attachment** (196 → 341, tačno +145) · 170 galerijskih ·
dump 1,7 MB. Spot-check tri ranije ispadajuće slike — 2515 `bergo-unique-ploca-2`,
2681 `privatni-teren-cacak-multisport-bergo-2`, 2798 `bergo-kanjiza2` — sve tri
prisutne u dump-u. `bash -n` čist na obe skripte.

## 🔴 Gotcha-i (otkriveni tek stvarnim pokretanjem)

Skripta do danas **nikad nije testirana** — sledeći put kad bi se pokrenula bio bi
dan migracije.

1. **Višelinijski SQL kroz `wp db query` vraća prazno sa exit kodom 0.**
   Nema greške, `set -e` ne reaguje, liste ID-eva samo ispadnu prazne. Isti upit u
   jednoj liniji radi. Svi upiti u obe skripte spljošteni u jednu liniju.
2. **`--no-create-info` WP-CLI 2.12 pretvara u `create-info=`** →
   `mysqldump: unknown variable 'create-info='` i export puca na pisanju dump-a.
   Radi kao **`--no-create-info=true`** (provereno: 0 `CREATE TABLE`, `INSERT` prisutan).
3. **Windows CRLF + prazan završni red.** `grep '^[0-9]+$'` ne pogodi ništa (sve liste
   prazne), a `paste -sd, -` napravi završni zarez → `IN (1,2,)` pukne sa
   „syntax error near ')'". Rešeno omotačem `q()` u obe skripte.

Gotcha 1 i 3 su bezopasni na Linux-u, ali su činili da se skripta **ne može proveriti**
unapred — što je i razlog zašto je bag preživeo do 12 dana pred migraciju.

## Otvorene akcije

- Nijedna iz ove sesije. Oba blokera zatvorena.
- Ostaje ranije poznato: prefiks u `wp-config.php` lokalnog builda i dalje `wpGs_`
  (radi, ne dirati bez potrebe) — ali `wp-config` **za server** mora `wpgs_`.

## Beleške / odluke

- Merenje uvek pre popravke: bloker je opisivao rizik, merenje je dalo 145/170.
- Skripte koje se pokreću jednom, na dan migracije, moraju imati način da se
  testiraju ranije (`PFX`/`OUT` iz okruženja) — inače se prvi put izvršavaju baš kad
  je cena greške najveća.

## Veze

- [[dnevnik/2026-08-12-agy-antigravity-delegat]] — poreklo oba blokera
- [[migracija/2026-08-12-preflight-checklist-24-08]]
- [[reference/naucene-lekcije]]
- [[2026-07-06-MASTER-PLAN-V2]] 3.10 / 3.11
