---
tip: prompt
faza: F1
naziv: Master parity inventar (live vs lokal)
menja-bazu: ne
preduslov: nema
---

# F1 — Master parity inventar

**Cilj:** jedan CSV koji za SVAKI live URL kaže da li i kako postoji na lokalnom
buildu → `migracija/parity-inventar.csv`. Ovo je novi izvor istine umesto stare
redirect mape. Strategija: [[migracija/PARITY-PLAN]].

## Okruženje

| Šta | Vrednost |
|---|---|
| Lokalni WP | `C:\xampp\htdocs\antasline` → `http://localhost/antasline` |
| DB | `antasline_local`, prefiks `wpGs_`, MariaDB · CLI: `/c/xampp/mysql/bin/mysql -u root antasline_local` |
| PHP skripte | piši u scratchpad, izvrši `C:\xampp\php\php.exe skripta.php` (bootstrap: `require 'C:/xampp/htdocs/antasline/wp-load.php';`) |
| Live sitemap | `https://www.antasline.com/sitemap_index.xml` (Yoast, 7 sub-sitemapa) |

Bash ograničenja: komande >965 bajtova pucaju — piši skriptu u fajl pa izvrši.

## Koraci

1. **Povuci svih 7 live sub-sitemapa** (curl, sačuvaj XML-ove u scratchpad):
   `post-sitemap.xml`, `page-sitemap.xml`, `product-sitemap.xml`,
   `category-sitemap.xml`, `product_brand-sitemap.xml`, `product_cat-sitemap.xml`,
   `product_tag-sitemap.xml`. Izvuci sve `<loc>` URL-ove.

2. **Za svaki live URL** izvuci putanju i poslednji slug, pa proveri lokal:
   - post/page/product: `SELECT ID, post_name, post_status FROM wpGs_posts WHERE post_name='<slug>' AND post_type IN ('post','page','product')`
   - kategorije/tagovi/brendovi: `wpGs_terms` JOIN `wpGs_term_taxonomy` po odgovarajućem taxonomy
   - Najpouzdanije kroz jedan PHP skript sa wp-load (može `get_page_by_path`, `get_term_by`), ne 100 pojedinačnih mysql poziva.

3. **Statusi** (kolona `status`):
   - `PARITY` — slug postoji lokalno, publish, isti path
   - `PARITY-DRUGI-PATH` — slug postoji ali na drugoj putanji (npr. live proizvod `/proizvod/x/` vs lokal `/shop/.../x/` — pre F2 ovo je očekivano za sve proizvode; zabeleži, F2 rešava)
   - `301-KANDIDAT` — lokal ima očigledan preimenovan parnjak (upiši lokalni slug u `lokal_slug`)
   - `NEDOSTAJE-LOKAL` — nema ničeg srodnog lokalno
4. **Obrni smer** — lokalni publish sadržaj (post/page/product) bez live parnjaka →
   dodatni redovi sa statusom `LOKAL-NOVO` (nove C3 stranice itd. — one se NE diraju,
   ali F3 mora da zna koji lokalni postovi su namerno novi).

5. **GSC težine** (kolona `gsc_klikovi`): povuci page-level klikove kroz Windsor.ai
   MCP — konektor `searchconsole`, account `['sc-domain:antasline.com']`, polja
   page + clicks, eksplicitni `date_from`/`date_to` za poslednjih ~12 meseci.
   ⚠️ Gotcha: `in`-filter je nepouzdan — povuci SVE stranice neflitrirano pa spoji
   po URL-u u skripti. Ako Windsor ne vrati podatke → upiši prazno, NIKAD ne izmišljaj.

6. **Upiši CSV** `migracija/parity-inventar.csv` — semicolon delimiter, UTF-8-BOM:
   ```
   live_url;post_type;live_slug;lokal_id;lokal_slug;status;gsc_klikovi;odluka;napomena
   ```
   Kolona `odluka` ostaje prazna — popunjava se u F4 (Miroslav).

7. **Poznate činjenice za proveru u inventaru** (izmereno 2026-07-07, mora da se poklopi):
   - 30 live postova: 25 slug match, 5 nedostaje (`esd-podovi-prica-kupca`, `na-kojoj-podlozi-se-igraju-turniri-u-3x3`, `podloga-za-odbojkaske-terene`, `prednosti-r-tile-design-...`, `ugradnje-industrijskog-poda`)
   - 50 live pages: samo 8 slug match
   - 37 live proizvoda: 34 match, 3 razlike (`durastripe-supreme-v-roll-...`, `ecotile-e500-10-...-interlocking-...`, `lite-shot-795-...`)
   - live `podovi-za-parkiraliste-i-staze` ≠ lokal `podloge-za-parkiraliste-i-staze` (podovi/podloge!) — ovo je ranije pogrešno vođeno kao PARITY

## Verifikacija

- [ ] Broj redova = broj live URL-ova iz svih 7 sitemapa + LOKAL-NOVO redovi
- [ ] Nijedan red bez statusa
- [ ] Spot-check 5 nasumičnih redova ručno (curl live URL → 200; lokal po slugu)
- [ ] Poklapanje sa "poznatim činjenicama" iz koraka 7 — ako se ne poklapa, istraži zašto pre upisa

## Zatvaranje sesije

1. [[DNEVNIK-NAPRETKA]] — unos NA VRH: `## YYYY-MM-DD [claude-code] [W3 PARITY F1] — ...` (broj redova po statusu, gde je CSV)
2. [[PROGRESS]] — red u "Urađeno" + ažuriraj "Sledeće"
3. Štikliraj F1 u [[migracija/promptovi/_README]]
4. U [[migracija/PARITY-PLAN]] §2 upiši finalne brojeve ako se razlikuju
