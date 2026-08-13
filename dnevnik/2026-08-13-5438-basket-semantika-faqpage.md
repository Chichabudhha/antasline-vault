# 2026-08-13 `[claude-code]` — Stavka E: `/sportske-podloge/` (5438) vraća basket-semantiku + FAQPage

**Kontekst:** stavka E iz `[[seo/2026-08-13-kanibalizacija-konsolidacija-plan]]` §3.8.
Plan izvršenja: `[[migracija/idemo-na-e-korak-tender-petal]]`. Korak-po-korak sa
stajanjem na K3/K4/K6/K7, po zahtevu M.

## Zašto

Svež `gsc_page_queries.py` pull (15.05→12.08, **ne** vault CSV-ovi — bili pogrešni
tri puta u jednoj sesiji): live 5438 drži **1.422 prikaza / 178 klikova / 90d**.

🔴 **Basket klaster nosi 138 od 178 klikova (78%)**, ne „skoro polovinu" kako je plan
§3.8 procenio — stavka je bila **potcenjena**:

| Upit | Prikazi | Klikovi | Poz. |
|---|---|---|---|
| podloga za košarkaški teren | 113 | 47 | 1,6 |
| podloga za kosarkaski teren cena | 147 | 39 | 2,0 |
| podloga za basket | 31 | 14 | 1,3 |
| podloge za kosarkaski teren | 37 | 13 | 1,5 |
| podloga za kosarku | 48 | 12 | 2,4 |
| podloga za kosarkaski teren | 48 | 12 | 2,5 |

Build je pri WoodMart redizajnu izgubio H2 „Izgradnja sportskih terena za basket u
vašem dvorištu!" i „Vrste podloga za sportski teren?", i **nije uopšte pominjao**
`/planer-terena/`.

**Nov nalaz, nije bio u planu:** stranica je imala FAQ sa 4 pitanja ali **nijedan
FAQPage JSON-LD** — samo `Article` i `VideoObject`. Hub `/industrijski-podovi/` ga je
dobio isti dan (stavka K), ovde je bio propušten.

## Šta je urađeno

Jedna skripta — `migracija/alati/job-5438-semantika-faq-schema-2026-08-13.php` — jer
sva četiri koraka diraju **isti** `post_content`, a schema zavisi od rezultata izmene
FAQ-a; razdvajanje bi značilo tri `$wpdb->update` ciklusa nad istim poljem.

1. **Sekcija A** (`mist`) — „Vrste podloga za sportski teren?", doslovan live tekst
2. **Sekcija B** (`paper`) — „Izgradnja sportskih terena za basket u vašem dvorištu!",
   doslovan live tekst + `<ul>` sa 7 modela (svaka stavka nosi ključnu reč) + CTA ka
   `/planer-terena/`
3. **FAQ par #3** — „Sportski podovi — cena?" → **„Koliko košta podloga za košarkaški
   teren?"** (bukvalno GSC upit sa 39 klikova), sa inline linkom na planer
4. **FAQPage JSON-LD** — građen **parsiranjem vidljivog teksta**, ne ručnim prepisom
   (inače se vremenom raziđu, što Google tretira kao neusklađenost)

Tekst je doslovno sa live-a (`zn_page_builder_els`, live post 1849, Zion Builder, iz
`live-export-2026-07-05/live-pages-2026-07-05.xml`) — live markup se **nije** kopirao,
samo tekst. Ispravljene dve čiste štamparske greške (`dicipline`→`discipline`, dupli
razmak); „Antas line" **namerno ostaje** kako stoji na live-u.

**Rezultat:** 10.328 → **15.129 B** · 6 → **8 `[vc_row]`** · render **8×H2** (bilo 6),
**15×H3** (bilo 14), **3 JSON-LD bloka** (bilo 2) uklj. 1× FAQPage sa 4 Question.

## Verifikacija (K1–K9, svi zeleni)

Nova skripta `migracija/alati/verify-5438-2026-08-13.php` (samo čita, F7.14 set):
HTTP · 1×h1 · `json_decode` svakog JSON-LD bloka · FAQPage/Question brojanje · gol JSON
u vidljivom tekstu · neizvršeni shortcode-ovi · video facade · `[al_skica]` · planer
link · slike.

- **K1** backup `antasline_local_2026-08-13_pre-5438-semantika.sql` (37,6 MB)
- **K6** 200 · 1×h1 · 3 JSON-LD svi OK · 1× FAQPage / 4 Question · 0 golog JSON-a ·
  planer link 200 · video facade i `[al_skica]` netaknuti · 31 slika sve 200
- **K7** Chrome 1440 i 390 px: 0 horizontalnog scrolla, 11 kartica cele (1440: grid
  `280px ×4`, sve 280×210; 390: sve 256 px), `al-btn` planera `rgb(240,77,34)` —
  računski identičan hero dugmetu, nije nasledio link-stil; klik na video facade
  otvara `youtube-nocookie.com/embed/VdZWT2O5_-M`; **0 console poruka** bilo koje vrste
- **K8** `al_verify 5438,17004,2298,17019,16676,17027` → 0/0/0
- **K9** pun sweep: 207 URL-ova, 3.267 slika → 0 nalaza

## Dve greške uhvaćene tokom rada (vredi zapamtiti)

🔴 **`wpgs_` vs `wpGs_` je progutao brisanje keša (K4).** Provera
`$wpdb->get_var("SHOW TABLES LIKE '{$tabela}'") === $wpdb->prefix.'yoast_indexable'`
je **tiho ispala** i skripta je javila „tabela ne postoji" — a tabela postoji. Uzrok:
lokalni `wp-config` nosi `$table_prefix = 'wpGs_'`, MySQL na Windows-u
(`lower_case_table_names=1`) vraća `wpgs_`, pa strogo poređenje ne prolazi. Tačno
razred greške iz CLAUDE.md §2 koji na Linux hostingu obara migraciju. Popravljeno
poređenjem u malim slovima (`strtolower`) — posle toga red stvarno obrisan.
**Pravilo za buduće skripte: nikad ne porediti ime tabele sa `$wpdb->prefix` strogo.**

🔴 **Ciklus „obriši pa ponovo izgradi schema blok" nije bio bajt-idempotentan (K5).**
Drugi `--write` prolaz je dao **+1 bajt** (15.129 → 15.130): brisanje je ostavljalo
`"\n"` kao zamenu, a umetanje nosi svoj vodeći prelom. Isti obrazac stoji i u
`job-faq-17025-konsolidacija-2026-08-13.php` — ako se ta skripta ikad pusti dvaput,
dodaće bajt po prolazu. Popravljeno zamenom praznim stringom; posle toga tri uzastopna
`--write` prolaza daju **+0 B** i tri `⚠️ već upisano`.

Manja: prva verzija verify skripte je tražila ime skice (`bergo-klik-sistem-presek`) u
renderu — `[al_skica]` emituje `<div class="al-skica-wrap">` + **inline** `<svg>`, ime
se u izlazu ne pojavljuje. Provera je bila pogrešna, ne render.

## Rollback

Nivo 1 (sekundni, bez restore-a baze):
`antasline-backups/5438-post_content_2026-08-13_pre.txt` (**10.328 B**, tačan) →
`$wpdb->update` + `clean_post_cache(5438)`. Snimak se pravi samo pri **prvom**
`--write`; ponovna pokretanja ga ne pregaze.
Nivo 2: `wp db import antasline_local_2026-08-13_pre-5438-semantika.sql`
(**nikad** `mysql.exe <` — razbija `ć`). Posle bilo kog rollback-a ponoviti K6.

## Van obima (kandidati za zasebne stavke)

- **`rank_math_title` / meta description nisu dirani.** Sopstveni head term „sportske
  podloge" stoji na **poz. 17,3** (96 prikaza / 2 klika) i profitirao bi, ali je van
  odobrenog obima.
- **Tartan klaster** na ovoj stranici bez ijedne namenske sekcije: „tartan podloga"
  45 prikaza / poz. 15,6 · „tartan cena za m2" 39 / 10,4 · „tartan kocke" 19 / 8,6 ·
  „tartan cena" 14 / 9,1 — ukupno **117 prikaza, 6 klikova, sve na poz. 9–16**.
  Kandidat posle live-a.
- Stavka F (dimenzije klaster vs 2298) — sledeća na redu, ~1 h.
