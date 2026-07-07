---
tip: prompt
faza: F2
naziv: Permalink fix — Woo /proizvod/ flat + /kategorija-proizvoda/ + blog→aktuelnosti
menja-bazu: DA ⚠️
preduslov: backup baze
---

# F2 — Permalink ispravke na lokalu

**Cilj:** lokalne URL strukture izjednačiti sa live sajtom, čime ~47 potencijalnih
301 redova nestaje. Strategija: [[migracija/PARITY-PLAN]].

**Trenutno pogrešno na lokalu:** Woo `product_base` je `/shop/%product_cat%` (live je
flat `/proizvod/`), `category_base` je `kategorija` (live je `kategorija-proizvoda`),
blog arhiva je `/blog/` (live je `/aktuelnosti/`).

## Okruženje

| Šta | Vrednost |
|---|---|
| Lokalni WP | `C:\xampp\htdocs\antasline` → `http://localhost/antasline` |
| DB | `antasline_local`, prefiks `wpGs_` · CLI: `/c/xampp/mysql/bin/mysql -u root antasline_local` |
| PHP skripte | scratchpad + `C:\xampp\php\php.exe skripta.php` (`require 'C:/xampp/htdocs/antasline/wp-load.php';`) |
| Backup | `mysqldump -u root antasline_local > C:\xampp\htdocs\antasline-backups\antasline_local_YYYY-MM-DD_pre-permalink-fix.sql` |

## Koraci

1. 🔴 **BACKUP baze** (komanda gore) — proveri da fajl ima ~90MB.

2. **Woo permalinci** — PHP skripta (opcija je serijalizovan niz, NE menjati sirovim SQL-om):
   ```php
   $p = get_option('woocommerce_permalinks');
   $p['product_base'] = 'proizvod';           // bilo: /shop/%product_cat%
   $p['category_base'] = 'kategorija-proizvoda'; // bilo: kategorija
   update_option('woocommerce_permalinks', $p);
   flush_rewrite_rules();
   ```

3. **Blog arhiva → /aktuelnosti/**: proveri `get_option('page_for_posts')` —
   ako pokazuje na lokalnu stranicu sluga `blog`, promeni slug te stranice u
   `aktuelnosti` (`wp_update_post` sa `post_name`), pa flush rewrite rules.
   Proveri i interni linkovi/meni da ne ostane `/blog/`.

4. **Proizvod slugovi — vraćanje na live** (2 sigurna + 1 za proveru):
   | Lokalni slug (menja se) | Live slug (cilj) |
   |---|---|
   | `durastripe-supreme-v-industrijska-traka-za-podno-obelezavanje-trajna-i-uklonjiva` | `durastripe-supreme-v-roll-50-mm-x-30-m-ergomat` |
   | `ecotile-e500-10-ultra-heavy-duty-podovi-za-kretanje-kamiona-i-viljuskara` | `ecotile-e500-10-ultra-heavy-duty-interlocking-podna-ploca` |
   | ⚠️ `lite-shot-325-...` | **PRVO PROVERI**: live ima `lite-shot-795-...` — 325 i 795 su verovatno RAZLIČITI modeli (Sure Shot serija). Ako jesu: lokalni 325 OSTAJE (LOKAL-NOVO), a live 795 ide u F5 listu za dodavanje. Ne preimenovati slepo! |
   `wp_update_post(['ID'=>..., 'post_name'=>...])` po proizvodu.

5. **Interni linkovi** — grep baze na stare putanje i ispravi:
   ```sql
   SELECT ID, post_type FROM wpGs_posts WHERE post_status='publish'
     AND (post_content LIKE '%/shop/%' OR post_content LIKE '%/kategorija/%'
          OR post_content LIKE '%/blog/%');
   ```
   ⚠️ `/kategorija/` LIKE hvata i `/kategorija-proizvoda/` — filtriraj lažne pogotke.
   Izmene sadržaja preko `wp_update_post`, ne direktan SQL (čuva revisions/hooks).

6. **Flush rewrite rules** još jednom na kraju (ili poseti Settings→Permalinks).

## Verifikacija

- [ ] 5 nasumičnih proizvoda: `http://localhost/antasline/proizvod/<slug>/` → 200
- [ ] 3 kategorije: `/kategorija-proizvoda/<slug>/` → 200
- [ ] `/aktuelnosti/` → 200 i lista postove; `/blog/` → 404 (na lokalu je to OK)
- [ ] Stari `/shop/...` URL → 404 (očekivano)
- [ ] 2 preimenovana proizvoda rade pod novim slugom
- [ ] Regression: Početna, `/industrijski-podovi/`, `/sportske-podloge/` → 200
- [ ] Nema preostalih `/shop/` ili `/blog/` internih linkova u publish sadržaju

## Zatvaranje sesije

1. [[DNEVNIK-NAPRETKA]] — unos NA VRH (`[W3 PARITY F2]`): šta je promenjeno, backup fajl, lite-shot odluka
2. [[PROGRESS]] — red u "Urađeno"
3. Štikliraj F2 u [[migracija/promptovi/_README]]
4. Ako je lite-shot 795 ≠ 325 → dodaj ga u F5 listu nedostajućeg (napomena u parity-inventar.csv)
