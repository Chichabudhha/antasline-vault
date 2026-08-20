---
name: rankmath-migracija-detalji
description: Pun detalj Yoast→Rank Math migracije (05.08.2026) — importer gotcha-i, connect-skip, Local SEO modul, šta je obrisano. Izmešteno iz CLAUDE.md §7.1 2026-08-20 (vault higijena). Kratak sažetak (Rank Math je jedini SEO plugin) ostaje u CLAUDE.md §7.1.
---

# SEO plugin — Rank Math (migrirano 2026-08-05)

**Lokalni build je prešao sa Yoast na Rank Math** (`rank_math_title` /
`rank_math_description` meta ključevi, zamenili `_yoast_wpseo_title` /
`_yoast_wpseo_metadesc`). Backup pre migracije:
`antasline-backups/antasline_local_2026-08-05_pre-rankmath-migration.sql`.

> 🔴 **M odluka 2026-08-13: Yoast je van upotrebe, ne vraća se.** Rank Math je
> jedini SEO plugin projekta — nova pravila: pisati isključivo u `rank_math_*`
> ključeve, verifikovati Rank Math izlaz u `<head>`, ne predlagati povratak na
> Yoast. Stara odluka „Yoast ostaje (ne RankMath)" iz [[odluke/_pregled-odluka]]
> (28.06) je **ukinuta** — stajala je kao tvrdo pravilo 8 dana posle same
> migracije i bila aktivan izvor grešaka (13.08 umalo pogrešan meta ključ na 13
> arhiva).
>
> **Fajlovi obrisani 2026-08-13:** `wp-content/plugins/wordpress-seo` više ne
> postoji na buildu (bio 21 MB, v27.8, deaktiviran) — ne ide u migracioni paket
> 25.08. `_yoast_wpseo_*` postmeta **ostaje u bazi** (690 redova) i povratak je
> moguć raspakivanjem arhive
> `C:\xampp\htdocs\antasline-backups\yoast-wordpress-seo-27.8_2026-08-13.tar.gz`
> (postupak u [[odluke/_pregled-odluka]]). Brisano `rm -rf`-om, **ne**
> `wp plugin delete` — taj poziva uninstall rutinu koja može da obriše i podatke
> iz baze.

Šta je urađeno:
- Uvoz podataka izveden PROGRAMSKI preko Rank Math-ove sopstvene
  `\RankMath\Admin\Importers\Yoast` klase (Reflection poziv protected metoda
  `settings()`/`postmeta()`/`termmeta()`/`usermeta()` iz wp-cli eval-file,
  zaobiđen wp-admin wizard jer browser login nije bio dostupan). Pokrilo je
  7843 post meta zapisa, 12 term meta, 4 user meta, opšta podešavanja.
- **Gotcha #1**: `Meta` trait-ov `update_meta()` gate-uje na
  `is_protected_meta($key)`, koji je `false` za `rank_math_*` ključeve (nemaju
  `_` prefiks) van Rank Math-ovog sopstvenog AJAX/REST konteksta — prvi pokušaj
  uvoza je "uspeo" (tačni brojevi u rezultatu) ali upisao PRAZNE vrednosti.
  Fix: privremeni `add_filter('is_protected_meta', ...)` koji vraća `true` za
  `rank_math_` prefiks, samo za trajanje import skripte.
- **Gotcha #2**: Rank Math ne inicijalizuje `rank_math()->manager` (pa ni
  title/meta/schema izlaz na front-endu) dok se ne "poveže" nalog (Setup
  Wizard Connect korak) — bez toga su svi front-end filter-i (title, schema)
  tiho neaktivni iako je plugin "active" u `wp plugin list`. Fix: legitimna
  Rank Math opcija za preskakanje ovog koraka —
  `update_option('rank_math_registration_skip', true)` +
  `update_option('rank_math_is_configured', true)`.
- **Gotcha #3**: Local SEO modul (LocalBusiness/NAP schema) nije uključen po
  defaultu (`rank_math_modules` opcija) i Yoast import ga ne popunjava jer
  NAP podaci (telefon 069 234 00 72, Ulcinjska 13) su ranije bili ubačeni
  ručnim PHP filter-om (`wpseo_schema_organization` u child theme
  `functions.php`), ne kroz Yoast Local plugin — importer nema šta da povuče.
  Fix: `local-seo` dodat u `rank_math_modules`, NAP podaci ručno upisani u
  `rank-math-options-titles` (`local_address`, `phone_numbers`,
  `knowledgegraph_type=company`, `local_business_type=LocalBusiness`).
- Stari Yoast-specifični PHP u `functions.php` (custom Product JSON-LD
  fallback W2 2.7 + `wpseo_schema_organization` filter W2 2.8) je UKLONJEN —
  Rank Math native Schema modul pokriva oboje bogatije (GTIN, dimenzije,
  slike, offers za Product; Organization+LocalBusiness za NAP). Product
  schema se sad emituje bez obzira na cenu (stari price-based duplication
  guard više nema svrhu).
- Breadcrumbs NISU dirani — WoodMart tema ima sopstveni native breadcrumb
  (`woodmart_breadcrumbs()`) nezavisan od oba SEO plugina; theme opcije
  `yoast_*_breadcrumbs`/`rankmath_*_breadcrumbs` su bile isključene i pre i
  posle migracije, provereno u `xts-woodmart-options`.
- Sitemap URL nepromenjen (`sitemap_index.xml`, isti kao Yoast) — trebalo je
  `wp rewrite flush` posle uključivanja modula.
- Verifikovano posle migracije: naslov/meta na 10 kategorija proizvoda +
  homepage + kontakt + conquest članak (`/epoksidni-podovi-ili-ecotile-podovi/`)
  su identični Yoast originalima (uklj. ćirilične/dijakritičke znakove), JSON-LD
  na homepage i proizvod stranicama nema dupliranja (1× Organization/LocalBusiness,
  1× Product po proizvod stranici).

**Ostalo za proveru pri sledećem doticaju ove teme**: Rank Math admin UI
(Setup Wizard/Dashboard) nikad nije otvoren u browseru ovu sesiju — cela
migracija je urađena programski preko wp-cli. Ako nešto u UI izgleda
"nedovršeno" (npr. Connect banner), to je očekivano — funkcionalno je sve
aktivno preko `rank_math_registration_skip`.
