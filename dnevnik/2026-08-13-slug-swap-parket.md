---
tip: dnevnik
datum: 2026-08-13
tag: "[claude-code]"
oblast: W2/SEO
naslov: Čist slug za „preko starog parketa" — 6588 preuzeo URL, 16613 ugašen
status: zatvoreno
---

# Čist slug za „preko starog parketa"

## Šta je urađeno

M je iz stavke 1 analize ([[seo/2026-08-13-kanibalizacija-konsolidacija-plan]] §1,
URL higijena) postavio pitanje o `sta-postaviti-preko-starog-parketa-ili-plocica-2`,
sa pretpostavkom da su dve verzije u međuvremenu spojene u jednu stranicu, i tražio:
**sadržaj sa `-2` stranice zadržati, ali `-2` iz slug-a ukloniti**.

### Provera je oborila pretpostavku o spajanju

Oba URL-a vraćaju 200 sa različitim canonical-om, i na live-u i na buildu:

| | 16613 (`…-plocica`) | 6588 (`…-plocica-2`) |
|---|---|---|
| Nastanak | 2022-07-23 | 2025-09-17 (prepis) |
| Dužina `post_content` | 4.940 zn. | **8.041 zn.** |
| SEO title | „PVC podovi i podovi od vinila" | „Šta postaviti preko starog parketa ili pločica?" |
| Robots na buildu | `noindex` (od 30.07) | `index` |
| Sadržaj | uvod + Objectflor Clic LVT + R-Tek | isto **+ Ecotile + FAQ (4 pitanja) + galerija primera** |
| Interni linkovi ka njoj | 0 | 0 |

Spajanje je zapravo **već bilo izvedeno u smeru `-2`** — 6588 je prepis koji pokriva
sve iz starog članka u boljem obliku (stari nosi i tipfelere: „posojeći",
„gradjevinskih", „zadovljni", „sisitemom", „trgovniske"). Gašenjem 16613 gubi se
samo ciljanje fraze iz njegovog SEO title-a: **132 prikaza / 5 klikova / 90d**.

### Odluka i zašto rizik nije onakav kakav je delovao

Ovo **ukida odluku od 2026-07-30** (`redirect-mapa-FINAL.csv` red 18), koja je na
osnovu GSC preseka 01.01–27.07 zaključila obrnuto:

| Verzija | Prikazi | Klikovi | Pozicija |
|---|---|---|---|
| `-2` (6588) | 3.353 | **258** | **5,5** |
| bez `-2` (16613) | 1.667 | 84 | 7,6 |

🟢 **Cilj 301 nije nov URL.** `/…-plocica/` živi na produkciji od 2022, indeksiran je
i nosi svojih 84 klika/god — dakle konsolidacija dva Google-u poznata URL-a, ne
selidba rangirane stranice na praznu adresu. Posle migracije oba live URL-a ostaju
ispravna: čist servira sadržaj direktno, `-2` ide na 301.

### Izvršenje

Skripta: `migracija/alati/job-slug-swap-parket-2026-08-13.php` (probni prolaz, pa `--write`).

1. **16613** → `draft` + slug `…-plocica-original-2022` (oslobađa čist slug)
2. **6588** → slug `…-plocica` (bez `-2`)
3. `redirect-mapa-FINAL.csv` red 18 → smer okrenut, obrazloženje prepisano
4. `htaccess-301-generate.php` → draft regenerisan: **76 pravila, svi ciljevi 200**;
   linija 34 sada `RedirectMatch 301 "^/…-plocica-2/?$" /…-plocica/`
5. `redirect-verify.php`: **0 duplikata · 0 petlji · 45/45 ciljeva 200 · 0 kolizija**
6. `parity-inventar.csv` (red `-2` → `301-KANDIDAT`; čist red dobio `lokal_id` 6588 —
   ranije upisani 15977/15967 su zastareli, ti postovi ne postoje),
   `regression-pages.csv` (URL za sledeći sweep)

### Verifikacija

- Čist URL **200**, novi sadržaj, **1×H1**, `index` + prava Rank Math meta u `<head>`
- `-2` **404** na buildu — 301 ga hvata posle migracije
- `post-sitemap.xml` nosi čist URL; **31 URL nepromenjeno**
- `sitemap_index.xml` 200

## Otvorene akcije

Nijedna iz ovog zadatka. Pre freeze-a (NED 16.08) i dalje stoje **E** i **F** iz
[[seo/2026-08-13-kanibalizacija-konsolidacija-plan]] §5.

## Beleške i odluke

🔴 **`$wpdb->update`, nikad `wp_update_post` za zamenu slug-a.**
`wp_unique_post_slug()` bi na 6588 zatekao slug koji (do prvog koraka) drži drugi
post i **tiho vratio `-2` nazad** — tačno ono što se uklanja. Iz istog razloga je
redosled obavezan: prvo 16613 pusti slug, pa ga 6588 uzme. Obrnuto bi dalo dva
posta sa istim `post_name`.

🔴 **Rank Math sitemap keš ne zna za direktan SQL upis.** Posle zamene je
`post-sitemap.xml` i dalje servirao stari `-2` URL — hook-ovi ne okidaju pri
`$wpdb->update`. Ruši se sa `\RankMath\Sitemap\Cache::invalidate_storage()`.
Važi za svaku buduću izmenu slug-a ili statusa izvedenu direktnim upisom.

🔵 **Dva `-2` slug-a na buildu, dva različita uzroka.** `ergonomske-podloge-2` je
WP-ov automatski sufiks jer slug drži **prilog** — čist slug je tu besplatan
(stavka A analize, 1 prikaz). `…-plocica-2` je bio **namerno drugi post**, pa čist
slug košta jednu 301 selidbu rangirane stranice. Prvo je higijena, drugo je odluka
o saobraćaju — ne mešati ih u istu stavku.

🔵 **Usput zatvoreno:** nedoslednost „5455 draftovan, 16613 ostavljen
publish+`noindex`" iz [[PROGRESS]] Blokera — 16613 je sada draft, pa oba duplikata
stoje isto i `redirect-verify.php` više ne prijavljuje upozorenje.

**Backup:** `antasline_local_2026-08-13_pre-slug-swap-parket.sql` (37,6 MB)

## Veze

- [[seo/2026-08-13-kanibalizacija-konsolidacija-plan]] §1
- [[dnevnik/2026-08-13-konsolidacija-kanibalizacija]] (stavke C/D/B, ista lista)
- [[reference/naucene-lekcije]]
- [[DNEVNIK-NAPRETKA]] 2026-08-13
