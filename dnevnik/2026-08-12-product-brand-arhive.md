---
tip: sesija
alat: claude-code
datum: 2026-08-12
blok: C
status: zavrseno
---

# Sesija — `product_brand` arhive napunjene (Ecotile / Ergomat), 301 cilj postao ispravan

> Poslednji sadržajni prozor pre freeze-a 16.08. M odabrao opciju **(a)** iz
> blokera od 2026-08-11: dodeliti brend termine proizvodima umesto prepravke
> 301 cilja.

## Šta je urađeno

**Polazno stanje (izmereno, ne pretpostavljeno):** taksonomija `product_brand`
je registrovana (`public=true`, `rewrite=brend`, `object_type=product`), ali
nijedan od 94 objavljena proizvoda nije nosio brend termin. Brojači `Ergomat 25`
/ `Ecotile 3` dolazili su iz **7 priloga iz Porto ere** (`Hollywood-Monster-*`
slike i sl.), pa su obe arhive renderovale praznu WooCommerce petlju — a
`.htaccess` 301 draft (linije 25–26) vodi live `/бренд/ecotile/` i
`/бренд/ergomat/` baš tamo.

**Dodela termina** (`scratchpad/job-product-brand.php`, dry-run pa `--confirm`):

| Brend | Proizvoda | Kriterijum |
|---|---|---|
| Ecotile (135) | **7** — 16538, 16540, 16542, 16930, 16939, 16943, 16949 | „Ecotile" u naslovu proizvoda |
| Ergomat (73) | **27** — 16476–16528 (svi parni) | „Ergomat" u naslovu **ili** u `post_content` |

Namerno **izostavljeni** `16530` (Mosolut Heavy) i `16922` (PermaStripe/Heskins) —
oba pominju Ecotile samo u poredbenom tekstu, nisu Ecotile proizvodi. 7 priloga
skinuto sa `ergomat`. Brojači posle `wp_update_term_count_now()`: **7 / 27**.

**SEO dopuna arhiva** (`scratchpad/job-brand-seo.php`) — obe stranice su sada
indeksabilne i cilj su 301 pravila, pa nisu ostavljene na Rank Math generičkom
šablonu „%term% Arhive":

- `rank_math_title` / `rank_math_description` po terminu (56/49 i 156/149 karaktera),
  CTA `069 234 00 72` u oba opisa
- Term `description` — po jedan uvodni pasus koji nabraja stvarnu liniju proizvoda
  (GEO pravilo: prvi pasus = direktan odgovor). Nijedan podatak nije izmišljen,
  sve je izvedeno iz naslova/specifikacija postojećih proizvoda.
- `tax_product_brand_sitemap` `off` → **`on`** (razlog za „off" iz 08-11 — prazne
  arhive — više ne postoji). Sitemap index **6 → 7 child-ova**, isto koliko live
  emituje; ukupno **236 → 238 URL-ova**.

## Verifikacija

| Provera | Rezultat |
|---|---|
| `/brend/ecotile/` · `/brend/ergomat/` | HTTP 200 · 1×H1 · 7 odn. 12 (prva strana) kartica |
| `robots` meta | `index, follow` (bilo bi `noindex` po `noindex_empty_taxonomies` da su ostale prazne) |
| title/description u `<head>` | novi, potvrđeni curl-om |
| `/brend/ergomat/page/2/` | 200, 1×H1 |
| JSON-LD (obe arhive) | 2 bloka, oba validna, `CollectionPage` + `BreadcrumbList`, bez dupliranja |
| Regression: 2 proizvoda + `/kategorija-proizvoda/industrijski-podovi/` + `/katalog/` | 200 · 1×H1 · 1× `Product`, bez dupliranja |
| `product_brand-sitemap.xml` | 2 URL-a, oba 200 |

**Backup pre izmene:** `antasline-backups/antasline_local_2026-08-12_pre-product-brand.sql` (36,7 MB).

## Otvorene akcije
- [ ] `.htaccess` 301 draft **ne treba regenerisati** — ciljevi (`/brend/ecotile/`,
      `/brend/ergomat/`) nepromenjeni, samo više nisu prazni. Napomena u
      [[migracija/2026-08-10-pre-migration-checklist]] §B3 o „praznoj stranici"
      je time zastarela. #claude-code (upisano)
- [ ] Ostali brendovi (Bergo, Geoplast, Condor, Radici, Expona, R-Tile, Hoopair,
      Goalrilla, Goaliath, Heskins, Mosolut) i dalje nemaju termine — namerno
      **nije** dirano: novi termini = nove indeksabilne arhive 4 dana pred freeze.
      Kandidat za posle live-a. #ceka-miroslav

## Beleške / odluke
- 🆕 **Gotcha (Rank Math):** brisanje opcije `rank_math_sitemap_cache_files` i
  pražnjenje tabele `rank_math_sitemap_cache` **nije dovoljno** da se nov child
  sitemap pojavi u `sitemap_index.xml` — sam fajl (`product_brand-sitemap.xml`)
  se servira, ali index i dalje nabraja stari spisak. Potrebno je pozvati
  `\RankMath\Sitemap\Cache::invalidate_storage()`. Isto važi za korak B7 u
  migracionoj checklisti. → [[reference/naucene-lekcije]]
- Obim je svesno držan na dva postojeća termina: cilj je bio ispraviti 301 metu
  pre migracije, ne uvesti nov taksonomijski sloj pred freeze.

## Veze
- [[PROGRESS]] Blokeri (stavka „Brend arhive su prazne…", zatvorena ovom sesijom)
- [[dnevnik/2026-08-11-gsc-priprema-sitemap]] §4–5 (poreklo blokera)
- [[migracija/2026-08-10-pre-migration-checklist]] §B3
- [[dnevnik/2026-08-11-htaccess-301-reverifikacija]]
