---
datum: 2026-08-11
tag: claude-code
oblast: W3 / migracija / čišćenje builda
status: zatvoreno
---

# 2026-08-11 — Legacy CPT-ovi (Custom Post Type UI) obrisani + 5 zamenjenih stranica u draft

## Zadatak (M)

„Na ovom sajtu sam ja radio pre tebe, koristio sam custom post types i pravio neke
stranice uz pomoć toga. Proveri da li se to još uvek koristi. Sve duplirane stranice i
stranice kojima smo napravili zamenu stavi da su draft, da bismo mogli kasnije da ih
obrišemo ako se ne koriste. Ako nema potrebe za tim stranicama i pluginom, onda da to
očistimo sa sajta i iz baze."

## Nalaz: CPT-ovi su bili mrtvi, ali ne bezopasni

Plugin **Custom Post Type UI 1.19.2 je bio aktivan** i registrovao **5 tipova**, ostatak
starog Porto sajta:

| CPT | Zapisa | Status |
|---|---|---|
| `vestacka-trava` („Veštačke trave") | 16 | svi draft |
| `spoljne-podne-obloge` („Podovi za bašte") | 10 | 9 draft + 1 pending |
| `industrija-podovi` („Industrijski podovi") | 8 | svi draft |
| `podovi-posl-prostor` („Podovi za poslovni prostor") | 5 | svi draft |
| `sportski-podovi2` („Sportski Podovi") | 2 | svi draft |
| **ukupno** | **41** | **0 objavljenih** |

Sopstvenih taksonomija nije bilo (`cptui_taxonomies` prazna).

**Ništa od toga nije bilo u upotrebi:**
- 0 objavljenih zapisa, dakle nijedan URL koji CPT servira
- 0 stavki u menijima koje pokazuju na njih
- svi odgovarajući slugovi vraćaju **404 na live-u** (provereno curl-om:
  `/bergo-flow/`, `/expona-flow/`, `/patmos-evolution/`, `/xj-competition/`,
  `/vestacka-trava/highlands/`, `/spoljne-podne-obloge/bergo-flow/`)
- nijedan nije u `redirect-mapa-FINAL.csv` → 0 GSC vrednosti za spasavanje

**Zašto je ipak vredelo obrisati, a ne samo ostaviti:** ovih pet CPT-ova je uzrok
404 bug-a od 2026-07-29 (v. [[DNEVNIK-NAPRETKA]], W7 F2.9) — njihova rewrite pravila
stoje **ispred** generičkog page pravila, pa je svaka dvosegmentna putanja pod istim
slugom išla mrtvom CPT-u i završavala u 404. Tada je zamka samo *neutralisana*
filterom `register_post_type_args` u child temi; sada je uklonjen i sam uzrok.

## Šta je urađeno

| # | Šta |
|---|---|
| 1 | **Sadržaj arhiviran pre brisanja** — svih 41 zapisa (post_content, meta, slugovi) u `migracija/arhiva/2026-08-11-legacy-cpt-sadrzaj.md` (329 KB) + `.json`. Ne iz opreza oko SEO-a nego zato što su ti draftovi ranije korišćeni kao **izvorni tekst** za nove WoodMart stranice (npr. Naxos Evolution → `/sportski-podovi-za-sale-i-balone/`, 378 GSC klikova; „industrija-podovi" draft → 16666 Trake za obeležavanje) |
| 2 | **211 priloga odvezano** (`post_parent` → 0) **pre** brisanja postova. Mnoge od tih slika su u aktivnoj upotrebi na novim Bergo stranicama — odvezivanje je garantovalo da ih `wp_delete_post()` ne dodirne. Kontrolno: 7.764 priloga pre = 7.764 posle |
| 3 | **41 CPT zapis obrisan** (`wp_delete_post(force)`). Zaostalih redova: 0 u `postmeta` (bilo 743), 0 u `term_relationships` (bilo 50) |
| 4 | **Plugin deinstaliran** (deaktiviran + `plugin delete`) i **3 opcije obrisane**: `cptui_post_types` (12,3 KB, autoload=yes), `cptui_taxonomies`, `cptui_new_install` |
| 5 | **`rewrite flush`** — 0 pravila koja pominju mrtve CPT-ove (357 ukupno), svih 5 tipova nestalo iz `get_post_types()` |
| 6 | **5 zamenjenih stranica → draft** (v. ispod) |
| 7 | Filter `register_post_type_args` u `woodmart-child/functions.php` **zadržan i označen kao no-op** — v. „Odluke" |

## 5 zamenjenih stranica prebačenih u draft

Sve su bile `publish` + `noindex`. Svaka je već imala definisan 301 cilj u
`redirect-mapa-FINAL.csv` i M-ovu raniju odluku (28–29.07):

| ID | Bila | Zamenjena stranicom |
|---|---|---|
| 5512 | `/podovi-za-poslovni-prostor/` | 16667 `/lvt-podovi-za-komercijalne-i-javne-prostore/` |
| 5754 | `/izgradnja-terena-za-tenis/` | 17028 `/sportske-podloge/sportski-podovi-za-teniske-terene/` |
| 5769 | `/podne-obloge-za-promocije-i-sajmove/` | 16665 `/spoljne-podne-obloge/bergo-easy/` |
| 15580 | `/podloge-za-parking/` | 16589 `/podloge-za-parkiraliste-i-staze/` |
| 16171 | `/galerija-sportskih-terena/` | 16674 `/galerija/` |

Provereno pre izvršenja za svaku: `LOKAL-NOVO` u `parity-inventar.csv` (**ne postoji na
live-u**), **0 GSC klikova**, **0 dolaznih internih linkova**, nije u meniju, nema
objavljenih pod-stranica.

🔵 **Ovo menja jutrošnju odluku iste sesije** („ostaju noindex, ništa nije dirano") — ali
po M-ovoj novoj, izričitoj instrukciji i u istom duhu: noindex je bio tih signal, draft je
eksplicitan i priprema ih za brisanje posle live-a. 301 redovi u redirect mapi rade
nezavisno od statusa (Apache ih hvata pre WordPress-a).

## Nije dirano — i zašto

🔴 **16613 `/sta-postaviti-preko-starog-parketa-ili-plocica/`** — jedini duplikat koji
**postoji na live-u** (84 GSC klika, 1.667 impresija). Odluka M od 30.07 je već doneta:
ostaje `publish` + `noindex` lokalno, a 301 ka bogatijem parnjaku 6588 (`-2` slug,
258 klikova) aktivira se na dan migracije. Draft bi ovde uklonio rezervu ako 301 zakaže.

⚪ `/ergonomske-podloge-2/` (16672) — `-2` sufiks izgleda kao artefakt duplikata, ali je
**pravi live URL** iz parity inventara, nema parnjaka bez sufiksa. Ostaje kako jeste.

## Beleške / odluke

⚪ **Filter `register_post_type_args` (child tema, ~1184) namerno ZADRŽAN** iako je sada
no-op. Bekapi baze pre 11.08 i dalje nose `cptui_post_types`, pa bi restore bez tog bloka
vratio i 404 zamku. Ukloniti tek kad nijedan živ bekap ne sadrži te opcije. Komentar u
kodu dopunjen tim obrazloženjem.

⚠️ **`wp db export` puca sa „mysqldump is not recognized"** — XAMPP-ov `mysqldump` nije na
PATH-u. Rešenje: `export PATH="$PATH:/c/xampp/mysql/bin"` pre poziva. → [[reference/naucene-lekcije]]

⚠️ **`wp db query` tiho vraća prazan izlaz** na ovom setup-u (upit nad `wpGs_options`
vratio 0 redova iako redovi postoje). `eval-file` sa `$wpdb->get_results()` radi ispravno —
za dijagnostiku koristiti njega, ne `db query`.

⚠️ **`/galerija-sportskih-terena/` je učitavanje trajalo 35–37 s** i time oborilo prvi
prolaz `al_verify.php` (status 0 = timeout, ne 404). Pre-postojeće, nije od ovog rada —
2,7 MB stranica sa `[gallery ids="…"]` shortcode-om (poznato od 2026-07-29). Stranica je
sada draft, pa je pitanje samo odloženo, ne rešeno.

⚪ **Sitna rupa, ne blokira:** `/spoljne-podne-obloge/` (bez „j") vraća 404 na nivou huba,
dok sve **pod-stranice** bez „j" uredno 301-uju na „j" verziju. Pravi slug
`/spoljnje-podne-obloge/` i svih 6 dece → 200. Live koristi „j" verziju, pa nema
posledica; vredi dodati 301 i za sam hub ako se ikad pojavi u logovima.

## Verifikacija

- `al_verify.php` sitewide: **212 URL-ova · HTTP≠200: 0 · ≠1×H1: 0 · PHP greške: 0 ·
  naslovna slika bez fajla: 0** (212 = 217 pre, minus 5 prebačenih u draft)
- Stari 404 bug ostao popravljen: `/spoljnje-podne-obloge/` + svih 6 pod-stranica → 200
- 5 draft stranica → 404, kako je i cilj
- 0 rewrite pravila sa mrtvim CPT-ovima · 0 od 5 tipova u `get_post_types()`
- 7.764 priloga netaknuto

**Backup:** `antasline-backups/antasline_local_2026-08-11_pre-cpt-cleanup.sql` (36 MB)

## Veze

- [[PROGRESS]] · [[DNEVNIK-NAPRETKA]] · [[2026-07-06-MASTER-PLAN-V2]]
- [[migracija/arhiva/2026-08-11-legacy-cpt-sadrzaj]] (izvezen sadržaj 41 zapisa)
- [[migracija/redirect-mapa-FINAL]] (301 ciljevi za svih 5 draft stranica)
- [[dnevnik/2026-08-11-metadesc-6-stranica]] (jutrošnja odluka o iste 4 noindex stranice)
- [[reference/naucene-lekcije]] (2 nove gotcha: `mysqldump` PATH, `wp db query` prazan izlaz)
