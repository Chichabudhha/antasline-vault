---
tip: sesija
alat: claude-code
datum: 2026-08-11
blok: C
status: zavrseno
---

# Sesija — GSC priprema: sitemap parity pre migracije (W3, checklist §A)

Sedma stavka istog dana. Zadatak: **„GSC priprema — sitemap URL spreman za
resubmit, alerti uključeni"** iz [[migracija/2026-08-10-pre-migration-checklist]] §A.

Ispalo je da nije bila administrativna stavka nego stvarna rupa: **build je
emitovao 3 sitemap-a tamo gde live emituje 7**, a Yoast→Rank Math migracija
(05.08) je tiho izgubila sva taksonomijska podešavanja.

## Šta je urađeno

### 1. Šta je stvarno submit-ovano u GSC-u (novi alat)

Konektor nije imao pristup Sitemaps API-ju → napisan
`.claude/skills/antasline-konektor/scripts/gsc_sitemaps.py` (read-only,
servisni nalog ima samo `webmasters.readonly` scope — skripta fizički **ne
može** submit-ovati ni obrisati sitemap).

Nalaz — **dva** submit-ovana sitemap-a, ne jedan:

| Sitemap | Submit-ovan | Google poslednji put povukao | Upozorenja |
|---|---|---|---|
| `http://www.antasline.com/sitemap_index.xml` | 2018-04-09 | 2026-08-10 | 3 |
| `https://www.antasline.com/sitemap_index.xml` | 2024-12-25 | 2026-08-05 | 4 |

Oba prijavljuju isti sadržaj (`web=145`, `image=252`). `http://` unos je
ostatak iz 2018 koji Google **i dalje povlači** — treba ga ukloniti u GSC UI
(API to ne može sa readonly scope-om, a i ne treba automatizovati).
Detalje upozorenja Sitemaps API ne izlaže — samo brojač; vide se isključivo
u GSC UI.

### 2. Struktura sitemap-a: live 7 child-ova vs build 3

| Child sitemap | LIVE (Yoast) | BUILD pre | BUILD posle |
|---|---|---|---|
| `post-sitemap.xml` | 31 | 31 | 31 |
| `page-sitemap.xml` | 50 | 70 | 70 |
| `product-sitemap.xml` | 38 | 95 | 95 |
| `category-sitemap.xml` | 7 | **—** | 6 |
| `product_cat-sitemap.xml` | 9 | **—** | 16 |
| `product_tag-sitemap.xml` | 8 | **—** | 18 |
| `product_brand-sitemap.xml` | 2 | **—** | — *(namerno, v. §4)* |
| **Ukupno URL** | 145 | 196 | **236** |

🔴 **Uzrok:** u `rank-math-options-sitemap` je **svih 12 `tax_*_sitemap`
ključeva bilo `off`**. Yoast importer (05.08) prenosi naslove/opise i opšta
podešavanja, ali **ne** prenosi koje taksonomije idu u sitemap — a Yoast ih
je na live-u imao uključene. Nije bilo vidljivo ni u jednoj dosadašnjoj
proveri jer sve provere idu **kroz** sitemap (regression sweep 10.08,
dijakritika sweep 11.08) — ono čega u sitemap-u nema, sweep ni ne vidi.

### 3. Koliko je to vredelo (mereno, ne procenjeno)

GSC, dimenzija `page`, 2026-05-11 → 2026-08-08 (3 meseca), za URL-ove kojih
nema u sitemap-u builda:

| Grupa | URL | Klikovi | Prikazi |
|---|---|---|---|
| `/category/` | 7 | **56** | 2.147 |
| `/kategorija-proizvoda/` | 12 | **21** | 337 |
| `/oznaka-proizvoda/` | 6 | 2 | 69 |
| `/бренд/` | 2 | 0 | 30 |
| **Ukupno** | **27** | **79** | **2.583** |

Najjači pojedinačno: `/category/industrijski-podovi/` — 44 klika / 1.487
prikaza / poz. 12,3.

⚠️ Bitna ograda: **izostanak iz sitemap-a nije `noindex`.** Provereno svih
27 URL-ova na buildu — svi vraćaju `index, follow` (osim
`/category/nekategorizovano/`, koja je `noindex` i zato je Rank Math
ispravno ne stavlja u sitemap). Dakle nije se gubila indeksabilnost, nego
otkrivanje — ali baš na migraciji, kad se ceo URL skup ponovo pušta kroz
crawl, to je najgori trenutak da 27 URL-ova sa istorijom nema u sitemap-u.

### 4. Ispravka

Uključeno u `rank-math-options-sitemap`: `tax_category_sitemap`,
`tax_product_cat_sitemap`, `tax_product_tag_sitemap`.

🔴 **`tax_product_brand_sitemap` uključen pa ODMAH VRAĆEN na `off`** — brend
arhive na buildu su **prazne**. Otkriveno tek pošto je stranica stvarno
otvorena: `/brend/ecotile/` renderuje `woocommerce-info` („nema proizvoda"),
0 linkova ka proizvodima, prazan `<h1>`; live parnjak
`/бренд/ecotile/` listira 3 proizvoda.

Zamka u brojačima: `wpGs_term_taxonomy.count` kaže **Ergomat 25 / Ecotile 3**,
pa termini deluju popunjeno. Stvarne veze u `term_relationships`:
**0 proizvoda**, jedino 7 **priloga** (`attachment`). Brojači su zaostali iz
Porto ere i nikad nisu prebrojani.

### 5. Šta to znači za 301 mapu (novi nalaz, van prvobitnog obima)

Draft `htaccess-301-DRAFT.txt` linije 25–26:

```
RedirectMatch 301 "^/бренд/ecotile/?$"  /brend/ecotile/
RedirectMatch 301 "^/бренд/ergomat/?$"  /brend/ergomat/
```

Oba **vode na prazne arhive**. `htaccess-301-generate.php` ovo nije uhvatio
jer proverava samo da cilj vraća **200** — prazna arhiva jeste 200.
Dodatno, `/бренд/ecotile/` je jedno od **5 pravila iz B3 spot-check liste**
za dan migracije: taj spot-check bi prošao („301 na tačan `Location`") a
korisnik bi svejedno stigao na praznu stranicu.

Saobraćaj je mali (30 prikaza / 0 klikova za 3 meseca), pa ovo **ne blokira
migraciju** — ali cilj je pogrešan. Dve opcije, obe za M (v. Otvorene akcije).

### 6. Verifikacija

- Svih **42** URL-a iz novih taksonomijskih sitemap-a: **200 / tačno 1×H1 /
  `index, follow`** — 0 problematičnih signala.
- Svaka arhiva ima stvaran sadržaj (3–12 proizvoda odnosno 3–10 postova);
  prazne su bile samo dve brend arhive, koje su i izbačene.
- Regresija: `/`, `/industrijski-podovi/`, `/kontakt/`,
  `/kategorija-proizvoda/industrijski-podovi/` → 200 / 1×H1.
- `post`/`page`/`product` sitemap brojevi nepromenjeni (31/70/95) — izmena
  nije dirala postojeće.
- 🟢 Sitemap **URL za resubmit ostaje isti**: `https://www.antasline.com/sitemap_index.xml`,
  i child-ovi nose **identična imena fajlova** kao Yoast na live-u
  (`post-`/`page-`/`product-`/`category-`/`product_cat-`/`product_tag-sitemap.xml`).
  Nijedan submit-ovan URL ne puca migracijom.
- `robots.txt` na buildu već pokazuje na produkcijski
  `https://www.antasline.com/sitemap_index.xml` — ispravno, ne dirati.

## Otvorene akcije

- [ ] **Ukloniti `http://` sitemap unos iz GSC-a** (submit-ovan 2018, Google ga
      i dalje povlači) — GSC UI, Sitemaps → obrisati red
      `http://www.antasline.com/sitemap_index.xml`. Bezbedno i pre migracije. #ceka-miroslav
- [ ] **Pogledati 3+4 upozorenja** na oba sitemap-a u GSC UI — API vraća samo
      brojač, ne i tekst. #ceka-miroslav
- [ ] **GSC email alerti** — nema ih u API-ju; provera je ručna: GSC →
      ⚙ Settings → *Users and permissions* (da je nalog vlasnik) i profilni
      meni → *Search Console preferences* → email notifikacije uključene.
      Bitno na dan migracije: alert o skoku 404/indexing grešaka je prvi
      signal ako 301 blok ne proradi. #ceka-miroslav
- [ ] 🔴 **Brend taksonomija — odluka** #ceka-miroslav
      - (a) dodeliti `product_brand` termine proizvodima na buildu (live ima
        Ecotile na 3 proizvoda) → 301 postaje ispravan, `tax_product_brand_sitemap`
        se može uključiti; **mora pre freeze-a 16.08** jer menja sadržaj
      - (b) prepraviti 301 cilj u `redirect-mapa-*` na neku postojeću stranicu
        (npr. `/kategorija-proizvoda/industrijski-podovi/`) pa regenerisati draft
      - (c) prihvatiti kako jeste — 0 klikova za 3 meseca, prazna stranica
        posle redirekta
      Ništa nije menjano bez odluke; brend sitemap je u međuvremenu `off`.

## Beleške / odluke

- **Zašto su tag arhive ipak ušle u sitemap.** 18 `product_tag` arhiva (od
  toga 10 novih `namena-*` iz W1 1.11) je tanko sa klasične SEO strane, ali:
  već su `index, follow` na buildu, live ih ima u sitemap-u, i svaka ima
  1–12 proizvoda. Migracija nije trenutak za promenu indeksne politike —
  cilj je bio **parity, ne optimizacija**. Prored tag arhiva (noindex ili
  izbacivanje) je zaseban SEO zadatak **posle live-a**.
- **Zašto brojevi nisu isti kao live** (236 vs 145): build ima više
  proizvoda (95 vs 38), više stranica (70 vs 50) i više Woo kategorija
  (16 vs 9) — to je očekivan efekat W1/W2 rada, ne greška.
- Provereno usput: `/kategorija-proizvoda/sigurnosni-senzori-signalni-sistemi/`
  je **404 na buildu** ali to je uredno — slug je namerno preimenovan u
  `...-senzori-i-signalni-sistemi` (M odluka 30.07) i 301 pravilo postoji u
  draftu (linija 23), cilj vraća 200.

## Gotcha-i (novi)

1. 🔴 **Yoast→Rank Math importer ne prenosi taksonomijske sitemap-e.** Posle
   svake migracije SEO plugina proveriti `tax_*_sitemap` ključeve u
   `rank-math-options-sitemap` — importer ćuti, a sitemap tiho izgubi ceo
   sloj URL-ova.
2. 🔴 **Rank Math kešira sitemap-e u fajlove.** Izmena opcije direktno u bazi
   (mimo admin UI) **ne** obara keš — `sitemap_index.xml` je i dalje
   prikazivao stara 3 child-a. Očistiti oboje: opcija
   `rank_math_sitemap_cache_files` + fajlovi
   `wp-content/uploads/rank-math/rank_math_*.xml`.
3. 🔴 **`wp_term_taxonomy.count` nije dokaz da termin ima sadržaj.** Ergomat
   je pokazivao 25, stvarno 0 proizvoda (brojač zaostao iz starog builda).
   Kad se odlučuje o arhivi, brojati kroz `term_relationships` + `posts`,
   ili — najpouzdanije — otvoriti stranicu.
4. 🟡 **`301 → 200` nije dovoljna provera cilja.** Prazna WooCommerce arhiva
   vraća 200. Generator redirekta koji verifikuje samo status kod propušta
   „tehnički ispravan redirekt na beskorisnu stranicu".
5. 🟡 **Sweep kroz sitemap ne može naći ono čega u sitemap-u nema.** Ista
   slepa tačka koja je 11.08 sakrila 2 slike 404 na `noindex` postu 16613.
   Za pokrivenost treba nezavisan izvor URL-ova (live sitemap, GSC `page`
   dimenzija).
6. ⚪ `wp-load.php` bootstrap u `php -r` je na ovom buildu prešao 120s i
   morao u pozadinu — za čitanje/pisanje jedne opcije brže je i pouzdanije
   ići direktno preko `mysql` + `unserialize()`.

## Backup

`C:\xampp\htdocs\antasline-backups\rank-math-options-sitemap_2026-08-11_pre-tax-sitemaps.sql`
— gotov `UPDATE` sa originalnom vrednošću opcije (jedan red, restore = pustiti fajl).

## Veze

[[migracija/2026-08-10-pre-migration-checklist]] ·
[[dnevnik/2026-08-11-htaccess-301-reverifikacija]] ·
[[2026-07-06-MASTER-PLAN-V2]] (W3 3.12, gate §3) ·
[[reference/naucene-lekcije]] · [[.claude/skills/antasline-konektor/SKILL]]
