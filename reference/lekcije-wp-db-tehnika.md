---
name: lekcije-wp-db-tehnika
description: Tehnicki gotchas — WordPress core/DB/WP-CLI, WooCommerce, WoodMart/WPBakery theme dev, Windows/PowerShell/Bash tooling, backup/infra. Deo 1/4 rascepa naucene-lekcije.md (2026-08-20, vault higijena).
---

# Naucene lekcije — WordPress / baza / WP-CLI / theme dev / tooling

> 1/4 tematskog rascepa `reference/naucene-lekcije.md` (2026-08-20). Ostala tri: [[reference/lekcije-seo-sadrzaj-migracija]] · [[reference/lekcije-ads-tracking]] · [[reference/lekcije-alati-vault-delegati]]. Indeks: [[reference/naucene-lekcije]].

## Taksonomija: proveravaj postojeće termine po `slug`, ne po `name` (2026-08-21)

Upit `t.name LIKE 'namena-%'` na `wpgs_terms` je vratio "0 proizvoda ima
namena tag" za 7 Codex draft proizvoda — netačno. `name` je lokalizovan
prikazni tekst ("Igrališta", "Sportska dvorana"...) i ne nosi prefiks
konvencije taksonomije; `slug` ga nosi (`namena-igraliste`). Ispravan upit
(`t.slug LIKE 'namena-%'`) je pokazao da su termini već dodeljeni. Posledica:
gubitak vremena i par nepotrebnih (ne štetnih) `INSERT IGNORE` upisa na
osnovu lažnog nalaza. Pravilo: filter/provera taksonomije uvek na `slug`
koloni, `name` samo za prikaz. → [[dnevnik/2026-08-21-codex-drafts-publish]]

## `wp media import` puca na SVG preko "SVG Support" plugina van pune HTTP sesije (2026-08-20)

`wp-cli media import fajl.svg` na ovom buildu baca fatalnu grešku ("Call to a
member function sanitize() on null" u `svg-support/functions/attachment.php`)
— plugin-ov `wp_handle_sideload_prefilter` hook očekuje objekat koji se
inicijalizuje samo u punom `wp-admin` request kontekstu, a WP-CLI ga nema.
PNG/JPG uvoz kroz isti put radi normalno, problem je specifičan za SVG.
Zaobilazno rešenje: ne ići kroz `media_handle_sideload()` uopšte — ručno
kopirati fajl u `wp_upload_dir()` putanju i pozvati `wp_insert_attachment()`
direktno (isti obrazac koji bi `wp media import` inače radio interno, samo
bez sideload prefilter hook lanca). Ne diraj sam plugin da bi se ovo
zaobišlo — dovoljan je alternativni upisni put za taj jedan fajl.

## Court builder `court_m` prvi element je dubina-od-osnovne-linije, ne "širina" u svakodnevnom smislu (2026-08-20)

`al-court-builder.js` čita sportske šablone kao `court_m: [Cw, Ch]` i taj par
se direktno mapira na `state.widthM`/`state.heightM`, koji ujedno određuju i
pikselsku širinu/visinu SVG platna (`cols`/`rows`) i osu duž koje `lines()`
crta ključ/luk (rect/halfArc primitivi mere "dubinu od osnovne linije" duž
Cw/prve ose). Za pun teren (`kosarka: [28,15]`) se to poklapa sa intuicijom
jer je 28m baš osa na kojoj se dubina od svake od dve osnovne linije meri. Za
polukort (3x3) intuicija puca: FIBA 3x3 je "širok" 15m (linija-do-linije) i
"dubok" 11m (od jedne osnovne linije) — ali pošto Cw/prvi element MORA biti
dubina-osa da bi `rect()`/`halfArc()` geometrija ostala tačna (isti kod se
deli sa punim terenom), ispravan zapis je `court_m: [11, 15]`, ne intuitivni
`[15, 11]`. Posledica: UI polje "Širina (m)" će za 3x3 prikazati 11, ne 15 —
to je ispravno s obzirom na to kako ovaj fajl interno definiše "širinu" za
SVAKI sport (ista stvar važi i za tenis: `[23.77, 10.97]`, gde je 23.77
zvanična dužina terena, ne širina). Pre menjanja bilo kog `court_m` para,
prvo proveri kako `lines()` koristi Cw/Ch za taj sport, ne samo koje su
"tačne" dimenzije sa spec lista.

## `cd` u Bash alatu traje kroz pozive (2026-08-19)

Radni direktorijum se **pamti između poziva** Bash alata. Posle
`cd migracija/alati && php skripta.php`, sledeći poziv sa relativnom putanjom
(`grep ... migracija/htaccess-301-DRAFT.txt`) prijavljuje „No such file or
directory" iako fajl postoji — traži ga iz `migracija/alati/`. Simptom liči na
obrisan fajl, uzrok je cwd. Pravilo: apsolutne putanje, ili `cd` vraćati u istoj
komandi.

## `curl` u `while read` petlji vraća lažni `HTTP 000` (2026-08-19)

`while read -r u; do curl ... "$u"; done < lista.txt` — **curl čita isti stdin** i pojede
ostatak liste; rezultat je `000` na svim URL-ovima, identično kao kad je Apache ugašen.
Fix: `curl ... < /dev/null` unutar petlje.

Druga, nezavisna varijanta iste zablude: **Python `io.open(path,'w')` bez `newline='
'`**
na Windows-u upiše CRLF, pa `
` završi **unutar URL-a** → opet `000`. Fix: `newline='
'`
pri pisanju ili `tr -d '
'` pri čitanju.

🔴 Pravilo: pre nego što `000` protumačiš kao pad servera, pozovi **jedan** URL direktno.
17.08 je ista poruka značila ugašen Apache, 19.08 dve potpuno druge stvari — simptom je
odobrenje čak i kad je iznos mali — vlasnik računa odlučuje, ne izvršilac.

## `mysql -B --raw` kroz Windows pipe kvari `post_content` (2026-08-18)

Sadržaj stranica sadrži `\r` i `\n`; Windows pipe ih pri čitanju pretvara u `\r\n`, pa
tekst koji vratiš u bazu **nije** tekst koji si pročitao. PowerShell varijanta je gora —
`Get-Content -Raw | mysql` duplo enkodira UTF-8, pa ćirilica/dijakritika odu u mojibake
(„koĹˇarkaĹˇkog"), a `REPLACE()` sa dijakritikom u ancoru **tiho promaši** i izmena se
preskoči bez greške.

Ispravno: `SELECT HEX(post_content)` → `binascii.unhexlify` u Pythonu → izmena → upis preko
`CONVERT(UNHEX('…') USING utf8mb4)` → **obavezno čitanje nazad i poređenje sa upisanim**.
Helper `wpdb.py` iz sesije 18.08. SQL fajlovi sa dijakritikom idu isključivo Bash
redirekcijom (`mysql … < fajl.sql`), nikad PowerShell pipe-om.

## Nov WooCommerce proizvod ne postoji dok nema reda u `wc_product_meta_lookup` (2026-08-18)

Upis u `wpgs_posts` + `wpgs_postmeta` + `wpgs_term_relationships` napravi proizvod koji se
otvara na svom URL-u i ima ispravan schema izlaz — ali **ne ulazi u WooCommerce upite**:
ne pojavljuje se u `[woodmart_products]` gridu, u kategoriji ni u pretrazi. Woo čita
`wpgs_wc_product_meta_lookup`, a taj red pravi samo `WC_Product::save()`.

Pri programskom upisu proizvoda dodati i taj red (kolone: `product_id, sku, virtual,
downloadable, min_price, max_price, onsale, stock_quantity, stock_status, rating_count,
pročitati puni sadržaj obe strane — automatski diff je ovde alat za sumnju, ne za zaključak.

## Neuspeo heredoc upisuje sopstvenu komandu u ciljni fajl (2026-08-18)

`cat >> fajl.css <<'CSS'` koji se ne zatvori kako treba završi tako što u CSS upiše
doslovnu liniju `grep -c "…" antas-design.css`. Bash prijavi samo `warning`, izlazni kod je
0, a nevalidan red u CSS-u može oboriti parsiranje pravila ispod njega.

Posle svakog `>>` na fajl koji ide u produkciju — `tail` na fajl. I: heredoc sa dugačkim
---

## Prazan `post_title` na `nav_menu_item` nije bag (2026-08-18)

Meni stavka 17424 je mesecima vođena kao „nema naslov, prazan red u meniju". U bazi
`post_title` **jeste** prazan — kao i na još 8 stavki istog menija. Ali sve su
`_menu_item_type=post_type`, a tu WordPress pri renderu pada na naslov povezane
stranice: 17424 se prikazuje kao „Podovi za garaže".

**Pravilo:** nalaz iz baze o meni stavkama se potvrđuje **u renderu** (`curl` + grep po
`menu-item-<ID>`) pre nego što se upiše kao zadatak. Ista logika važi za svako polje
koje WP ima kao fallback.

Ovde: `wp_update_post` na 6 proizvoda + prepis 4 linka na `16676` u jednoj skripti.

## Verifikacija na zaostalom fajlu = lažno zeleno (2026-08-17)

`curl -o /tmp/p.html … ` je vratio **`HTTP: 000`** (Apache nije radio), ali su
`grep`-ovi u istom bloku ipak ispisali `H1: 1 | H2: 4 | JSON-LD: 2` — **iz
`/tmp/p.html` zaostalog od ranije sesije**. Brojke izgledaju kao uredna
verifikacija i odnose se na potpuno drugu stranicu.

**Pravila:**
1. `rm -f` izlazni fajl **pre** svakog `curl`-a.
2. Čitaj `%{http_code}` i **stani** ako nije 200/3xx — ne nastavljaj na `grep`.
3. Piši u scratchpad, ne u `/tmp` (deli se između sesija).
4. Posle MySQL crash-a XAMPP Apache **ne mora biti pokrenut** — proveri
   `Get-Process httpd` pre HTTP verifikacije.

## Mrtve CSS klase ne prijavljuju grešku (2026-08-17)

Porto/Kallyas markup (`productColors-block` / `color-list` / `color-square`) na
stranici 15793 renderovao je **prazan prostor** umesto swatch-a: `.color-square`
je div bez sopstvenih dimenzija, a klasa ne postoji ni u jednoj temi. Nema PHP
greške, nema 404, sweep prolazi čisto — vidi se samo okom.
**Provera pre dodavanja/nasleđivanja bilo kog markupa:**
v. [[dnevnik/2026-08-14-copilot-grok-delegati]]

## `Select-String` je podrazumevano case-NEosetljiv (2026-08-14)

Posle sweep-a `wpGs_` → `wpgs_` provera je pokazala da su „popravljeni" fajlovi
i dalje puni pogodaka. Fajlovi su bili ispravni — **provera nije**: PowerShell
`Select-String` bez `-CaseSensitive` broji i `wpgs_` i `wpGs_`, pa je izgledalo
kao da nijedna izmena nije prošla.

```powershell
Select-String -Pattern 'wpGs_' -CaseSensitive        # tačno
Get-ChildItem ... | Select-String 'wpGs_'            # laže na case-razlici
```

Ista zamka važi za `-match`, `-like`, `-eq` i `-replace` u PowerShell-u — svi su
case-neosetljivi po podrazumevanoj vrednosti (case-osetljive varijante su
`-cmatch`, `-clike`, `-ceq`, `-creplace`). I za MySQL `LIKE` pod `_ci` kolacijom
(rešenje: `COLLATE utf8mb4_bin`).

**Pravilo:** kad je ceo zadatak razlika u veličini slova, **alat za proveru mora
biti eksplicitno case-osetljiv** — inače potvrđuje sam sebe.
v. [[dnevnik/2026-08-14-copilot-grok-delegati]]

## Prefiks baze nije samo ime tabele — WP od njega izvodi i ključeve-stringove (2026-08-14)

Pri promeni `$table_prefix` sa `wpGs_` na `wpgs_` nije dovoljno izmeniti
`wp-config.php`. WordPress od prefiksa gradi i **ključeve koji se u bazi čuvaju
kao obični stringovi**:

| Ključ | Tabela | Ako je promašen |
|---|---|---|
| `<prefiks>capabilities` | `usermeta` | **svi korisnici bez ijedne dozvole → zaključan wp-admin** |
| `<prefiks>user_roles` | `options` | **nestaju definicije rola** |
| `<prefiks>user_level` | `usermeta` | legacy nivo |
| `<prefiks>user-settings`, `…-time`, `dashboard_*`, `persisted_preferences` | `usermeta` | kozmetika |

🔴 **Zašto SQL provera daje lažno zeleno:** kolacija je `utf8mb4_general_ci`,
dakle case-**ne**osetljiva — `WHERE meta_key='wpgs_capabilities'` uredno nađe
sačuvano `wpGs_capabilities`. Ali WP meta keš je **PHP niz**:
`update_meta_cache()` ga puni imenima kakva vrati baza, a `get_metadata_raw()`
traži `isset($meta_cache[$meta_key])` — **ključevi PHP nizova su case-osetljivi**,
pa lookup promašuje i vraća prazno.

**Postupak (redosled je bitan):** backup → preimenovati ključeve u bazi →
**tek onda** `wp-config.php`. Obrnuto ostavlja prozor u kom WP traži ključeve
kojih još nema.

```sql
-- COLLATE utf8mb4_bin je obavezan: bez njega LIKE case-neosetljivo
-- pogodi i redove koji su već ispravni
UPDATE wpgs_usermeta SET meta_key = CONCAT('wpgs_', SUBSTRING(meta_key, 6))
WHERE meta_key COLLATE utf8mb4_bin LIKE 'wpGs\_%';
```

**Verifikacija koja stvarno dokazuje:** `wp user list --fields=ID,user_login,roles`
— ide kroz pun WP stek i baš kroz meta keš. HTTP 200 nije dovoljan (sajt se
prikazuje i kad su role pale; puca tek wp-admin).

⚠️ Usput: `wp eval` na ovom buildu pada na **300s timeout** u
`woocommerce/src/Proxies/LegacyProxy.php:53`, dok `wp user list` prolazi.
Za brzu proveru koristiti konkretne `wp` komande, ne `eval`.
v. [[dnevnik/2026-08-14-copilot-grok-delegati]]

## Maskiranje tajni mora da pokrije ugnežđene objekte (2026-08-14)

Pri proveri tipa autentifikacije ispisan je `auth.json` uz maskiranje po imenu
polja (`token|secret|key|jwt`). Maska je gađala **samo prvi nivo**, a ceo
kredencijal je bio u **ugnežđenom objektu** pod ključem koji je izgledao
bezopasno (`https://auth.x.ai::<uuid>`). Rezultat: JWT i **refresh token**
(ne ističe sam) završili u transkriptu sesije.

**Pravilo:** kredencijal-fajl se ne ispisuje ni maskiran. Ako treba samo
utvrditi *tip* autentifikacije, dovoljno je:
```powershell
[bool]$env:XAI_API_KEY            # postoji li API ključ
Test-Path "$env:USERPROFILE\.grok\auth.json"
```
a za detalje čitati **log**, ne sam kredencijal. Ako se ipak procuri —
v. [[dnevnik/2026-08-14-copilot-grok-delegati]]

## PowerShell 5.1 i srpski tekst — dva tiha razbijača skripti (2026-08-14)

**BOM.** `.ps1` bez UTF-8 BOM-a PowerShell 5.1 čita kao ANSI, pa dijakritika i
crtica `—` daju `Unexpected token`. Alati koji pišu UTF-8 bez BOM-a traže
konverziju posle svake izmene:

```powershell
$c = [System.IO.File]::ReadAllText($p, [System.Text.UTF8Encoding]::new($false))
[System.IO.File]::WriteAllText($p, $c, [System.Text.UTF8Encoding]::new($true))
```

**Navodnici.** Neparan broj `"` u argumentu razbije prosleđivanje native exe-u
(Copilot javi `Invalid command format … prompt was not quoted`). Srpski tekst to
pravi sam od sebe: `„ovako"` ima **tipografski** otvarač i **ASCII** zatvarač.
Simptom je varljiv — puca po dužini isečka (800 ok / 1200 puca / 1600 ok), pa
liči na ograničenje dužine. Fix: `$tekst -replace '"', '\"'` pre poziva.

## `wp_insert_post` iz CLI skripte tiho briše `<script>` iz sadržaja (2026-08-14)

Skripta pokrenuta preko `php skripta.php` nema prijavljenog korisnika, pa `wp_insert_post` /
`wp_update_post` primenjuju **kses** — a kses uklanja `<script type="application/ld+json">`.
Rezultat: FAQPage schema ostane kao **goli tekst** u sadržaju, kome `wptexturize` još i
pretvori navodnike u tipografske, pa se u renderu vidi `&#8220;@type&#8220;` umesto validnog
JSON-a. Ništa ne prijavi grešku: upis „uspe", stranice vrate 200, 1×H1, Rank Math meta u
`<head>` — sve zeleno, a schema ne postoji.

```php
kses_remove_filters();          // pre svakog wp_insert_post/wp_update_post iz CLI-ja
```

**Kako se hvata:** verifikacija mora brojati `application/ld+json` u renderu (očekivano 2 na
proizvodu — Rank Math Product + naš FAQPage), ne samo „ima li JSON-LD". Provera „HTTP 200 +
1×H1" ovaj kvar ne vidi. Isto važi za svaki HTML koji kses filtrira (`<iframe>`, `<style>`).
v. [[dnevnik/2026-08-14-ergonomske-podloge-proizvodi]]

## Ime tabele se nikad ne poredi sa `$wpdb->prefix` strogo (2026-08-13)

`wp-config` lokalnog builda nosi `$table_prefix = 'wpGs_'`, a MySQL na Windows-u
(`lower_case_table_names=1`) vraća `wpgs_`. Zato

```php
$t = $wpdb->prefix . 'yoast_indexable';
if ( $wpdb->get_var( "SHOW TABLES LIKE '{$t}'" ) === $t ) { ... }   // 🔴 nikad true
```

**tiho** ispadne i skripta prijavi „tabela ne postoji" — a tabela postoji. Nema greške,
nema upozorenja, samo preskočen korak (13.08 preskočeno brisanje `yoast_indexable` reda).
Isti razred greške koji na Linux hostingu, gde je case stvarno osetljiv, obara migraciju.
Ispravno: `strtolower()` sa obe strane poređenja, ili koristiti vraćeno ime tabele.
v. [[dnevnik/2026-08-13-5438-basket-semantika-faqpage]]

## „Obriši pa izgradi iznova" nije idempotentno dok se ne izmeri u bajtovima (2026-08-13)

Obrazac za regenerisanje JSON-LD bloka (`preg_replace` starog `<script>` → pa umetanje
novog) izgleda idempotentno i **jeste** po sadržaju, ali je 13.08 pri svakom ponovnom
`--write` prolazu dodavao **po jedan bajt**: brisanje je ostavljalo `"
"` kao zamenu, a
umetanje nosi svoj vodeći prelom. Zamena mora biti prazan string kad umetanje samo
obezbeđuje razmak.

🔴 **Isti obrazac stoji i u `job-faq-17025-konsolidacija-2026-08-13.php`** (16567) — ako se
ta skripta ikad pusti dvaput, dodaće bajt po prolazu. Nije popravljeno tamo jer je skripta
već izvršena i zapisana; popraviti pre eventualnog ponovnog pokretanja.

Praktično pravilo: checkpoint „pusti isti `--write` opet" ne proverava samo da nema
duplikata u sadržaju nego da je **`strlen` identičan**. Bez toga se drift ne vidi.
  Pre prijave pogledati da li ispred pipe-a stoji `\`.

## Zamena slug-a ide `$wpdb->update`-om, i to određenim redosledom (2026-08-13)
- `wp_update_post()` prolazi kroz `wp_unique_post_slug()`, koji ako zatekne slug zauzet
  drugim postom **tiho vrati `-2` nazad**. Kod zadatka „skini `-2` sa slug-a" to znači da
  skripta prijavi uspeh, a stanje ostane nepromenjeno. Direktan `$wpdb->update` zaobilazi
  tu logiku u celosti (uz `clean_post_cache()` posle).
- Redosled je obavezan: **prvo stari post pusti slug** (preimenovanje + `draft`), pa ga tek
  onda novi uzme. Obrnuto daje dva posta sa istim `post_name`.
- Provera preduslova na početku skripte (jesu li slugovi u očekivanom polaznom stanju →
  migracioni paket ode spisak URL-ova koji više ne postoje.

## `wp plugin delete` nije „obriši fajlove" — briše i podatke (2026-08-13)
- WP-CLI-jev `delete_plugins()` poziva **uninstall rutinu plugina** (`uninstall.php` /
  registrovani uninstall hook) pre brisanja foldera. Ako plugin tu čisti svoje opcije i
  postmeta, „brisanje plugina" postaje i brisanje podataka — bez upozorenja i bez pitanja.
- Kad je cilj „skini fajlove, zadrži mogućnost povratka": arhivirati folder (`tar -czf`),
  pa `rm -rf` folder. Baza ostaje netaknuta i povratak je raspakivanje + aktivacija.
- Provera integriteta arhive ide **pre** brisanja, ne posle: `tar -tzf | wc -l` mora da se
  poklopi sa `find <folder> -type f | wc -l` + `-type d | wc -l`. Posle `rm -rf` nema sa čim
- **Pravilo pri zatvaranju sesije: posle upisa proveriti `grep -n "^## " DNEVNIK-NAPRETKA.md | head -3` — tvoj unos mora biti prvi.**

## Regression sweep se pušta u fajl, ne kroz `| tail` (2026-08-13)
- `php regression-sweep.php | tail -60` na 239 stranica traje >10 min i **ne ispisuje ništa do kraja** (`tail` bafer) — izgleda kao da je zamrznuto, a background izlazni fajl ostaje na 0 bajtova.
- **Pravilo: pre nego što napišeš override za prijavljenu stranicu, prebroj koliko stranica nosi isti obrazac.** Jedan `SELECT` je jeftiniji od otkrivanja istog baga za tri nedelje.

## CSS `+` puca na `wpautop` `<br>` — pravilo radi na jednoj stranici, ne na drugoj sa „istim" markupom (2026-08-13)
- Selektor `.al-section--paper + .vc_row-full-width + .al-section--paper` radio je na `/sportske-podloge/kosarkaske-konstrukcije/`, a **nije** na `/industrijski-podovi/`. Razlika je goli `<br>` koji `wpautop` ostavi između redova kad je `[/vc_row]` u `post_content` završen novim redom.
- `+` traži **tačnu** susednost i ne preskače prazne markere. `display: none` na tom `<br>` **ne pomaže** — element i dalje stoji u DOM-u.
- Rešenje: nabrojati sve stvarno viđene kombinacije (`br`, `.vc_row-full-width`, i permutacije). Verzija koja radi na jednoj stranici nije dokaz — proveri `getComputedStyle` na **svakoj** stranici koju pravilo treba da pogodi.

## Tema ume da DEREGISTRUJE plugin CSS i zameni ga svojim — koji stiže samo kroz njen element (2026-08-13)
- WoodMart (`inc/enqueue.php:591`) radi `wp_deregister_style('contact-form-7')` i nudi svoj `css/parts/int-wpcf7.css`. Taj part enqueue-uje **isključivo** `woodmart_shortcode_contact_form_7()`. Forma koju renderujemo sirovim `do_shortcode('[contact-form-7 …]')` ostaje **bez ijednog CF7 stila** — ni plugin-ovog, ni teminog.
- Posledica su bila dva vidljiva artefakta na ~55 stranica: `<fieldset class="hidden-fields-container">` kao prazan okvir iznad prvog polja, i `.wpcf7-response-output` koji iz `parts/mod-notices-general.css` (koji **jeste** učitan) dobija `display:block` + warning žutu + ikonicu, pa stoji prazan ispod dugmeta.
- 🔴 Bag je bio nevidljiv na `/kontakt/`, jedinoj stranici koja se rutinski proverava, jer ona formu renderuje kroz WPBakery CF7 element i part **ima**.
- Fix je jedna linija (`woodmart_enqueue_inline_style('wpcf7')`), ne CSS override — override bi zamaskirao uzrok i ostavio ostatak part-a (spinner, `submitting` stanje) neaktivnim.
- **Pravilo: kad plugin izgleda „neostilizovano", prvo `grep -rn "deregister_style"` u temi**, pa tek onda piši sopstveni CSS.

## „Fajl je u renderu" nije isto što i „stil je primenjen" — proveri `<link>`, ne string (2026-08-13)
- `grep -o 'parts/[a-z0-9-]*\.css' render.html` je vratio `int-wpcf7.css` i navelo na zaključak da je part učitan. Nije bio — ime se pojavljuje u WoodMart-ovoj JS listi za lazy učitavanje, bez `<link>` taga.
- Dokaz je `grep -o '<link[^>]*int-wpcf7[^>]*>'` (prazno) i `[...document.styleSheets].some(s => s.href && s.href.includes('int-wpcf7'))` (`false`), ne prisustvo imena u HTML-u.
- Isti razred kao lekcija o `getComputedStyle`-u (Chrome 149 tabele, 2026-08-12): **stanje se meri u renderu, ne čita iz izvora.**

## `clip-path` paralelogram odseca vertikalne krakove `inset` box-shadow rama (2026-08-13)
- `.al-btn--ghost` crta ram sa `box-shadow: inset 0 0 0 2px currentColor`, a oblik dolazi od `clip-path: polygon(12px 0, 100% 0, calc(100% - 12px) 100%, 0 100%)`. Kosi rez pada tačno preko levog i desnog kraka rama → dugme se renderuje kao **dve odvojene vodoravne crte**.
- Na navy hero-u (jedno ghost dugme pored punog crvenog CTA) to prolazi kao potpis; u gridu od 4 kartice čita se kao nedovršen okvir. Isti `clip-path` je i pretpostavljao **jedan red teksta** — dvoredna labela ispada iz oblika.
- Kad ghost dugme ide u grid/karticu: `clip-path: none` + pun ram. Rez je hero-potez, ne komponenta za ponavljanje.

## MySQL u XAMPP-u pada na „Aria recovery failed" — treći put, isti fix (2026-08-13)
- `[ERROR] Aria recovery failed… delete all aria_log.######## files` → `Plugin 'Aria' registration failed` → `Could not open mysql.plugin table`. **Poslednja poruka je posledica, ne uzrok** — čitati odozgo.
- Fix: preimenovati `aria_log.00000001` i `aria_log_control` u `xampp/mysql/data/` (npr. `.bak-RRRRMMDD`), MariaDB ih ponovo napravi pri startu. Aria u XAMPP-u nosi samo `mysql.*` sistemske tabele; WP podaci su InnoDB, ništa se ne gubi.
- Ponovilo se **10.07, 21.07, 13.08** (`.bak` fajlovi su trag). Ako se ponovi na dan migracije — to je 2 minuta, ne incident.
- Uz to: tvrda provera pred dump (`exit 1` ako ijedan galerijski ID nije ušao u export) — bolje da pukne glasno nego da se otkrije posle migracije.

## `wp db query` sa višelinijskim SQL-om vraća PRAZNO sa exit kodom 0 (2026-08-12)
- Najgora vrsta promašaja: nema poruke, `set -euo pipefail` ne reaguje, liste ID-eva samo ispadnu prazne i export nastavi da radi „uspešno". Isti upit u **jednoj liniji** radi.
- Pravilo: svaki SQL koji ide kroz `wp db query` piše se u jednoj liniji, bez obzira koliko je ružan.
- Srodno: `VAR=$(wp db query ... | paste ...)` maskira izlazni kod — status je od poslednje komande u pipe-u, pa `set -e` ne pomaže ni kad `wp` stvarno pukne.

## WP-CLI 2.12 mangla `--no-create-info` u `create-info=` (2026-08-12)
- `wp db export - --no-create-info` → `mysqldump: unknown variable 'create-info='` i export pukne na pisanju dump-a. WP-CLI tretira `--no-<flag>` kao negaciju pa prosledi prazan `create-info=`.
- Radi kao **`--no-create-info=true`** (provereno: 0 `CREATE TABLE`, `INSERT` prisutan).

## Prefiks baze je `wpgs_`, malim slovima — `wpGs_` prolazi samo na Windows-u (2026-08-12)
- `SHOW TABLES` → `wpgs_posts`; `@@lower_case_table_names` = **1** na XAMPP/Windows, pa case ne igra ulogu lokalno. Na Linux hostingu igra — to je tačan uzrok „site not installed" greške pri probi migracije 21.07.
- Lokalni `wp-config.php` i dalje nosi `wpGs_` i radi; `wp-config` **za server** mora `wpgs_`.
- 🔴 Opasan oblik nije u dokumentaciji nego u kodu: `staging-import.sh` je imao `STG_PFX="wpGs_"` — promenljivu kojom `sed` prepisuje imena tabela u dump-u pre importa. Pogrešan case tu ne prijavi grešku, samo napravi pogrešne tabele.

## Windows CRLF u izlazu CLI alata pravi prazne liste i završne zareze (2026-08-12)
- `wp db query --skip-column-names` na Windows-u vraća `
` i završni prazan red. Posledice: `grep -E '^[0-9]+$'` ne pogodi **ništa** (sve liste ID-eva prazne), a `paste -sd, -` pretvori prazan red u završni zarez pa `IN (1,2,)` pukne sa „syntax error near ')'".
- Omotač koji rešava oboje: `q() { wp db query "$1" --skip-column-names | sed 's/
- Hardver ove mašine (i5-11320H, 15,7 GB RAM, MX450 2 GB): `qwen3:4b` je jedini praktično upotrebljiv, `qwen3:8b` >10 min po pozivu, **`qwen3:30b` (18 GB) uopšte ne staje u RAM**.

## `python skripta.py | Out-File -Encoding utf8` u PowerShell-u duplo enkoduje — ćirilica postane `ĐşĐľŃĐ°Ń€` (2026-08-12)
- PowerShell dekodira Python-ov UTF-8 izlaz kao cp1252, pa ga ponovo enkoduje kao UTF-8. GSC vraća i ćirilične upite („кошаркашки терен"), koji tako postanu smeće — a JSON ostane sintaksno validan, pa ništa ne pukne.
- Fix pre svakog pokretanja: `$env:PYTHONIOENCODING="utf-8"` + `[Console]::OutputEncoding=[System.Text.Encoding]::UTF8`, i `Set-Content -Encoding utf8` umesto `Out-File`.
- Isti obrazac je već zabeležen za „Sledeće" liste (2026-08-12) i za skill građen iz jednog izvora (2026-08-12) — tri pojave istog problema u istom danu.

## `<a class="al-card">` ne sme da nosi blok sadržaj — wpautop raspadne karticu, a izvor izgleda ispravno (2026-08-12)
- Proizvod-kartica je napisana kao `<a class="al-card">` sa `<div class="al-card__body">` unutra (naslov u telu tamnom bojom — bela verzala preko studijske fotke na beloj pozadini je nečitljiva). **wpautop ubaci prazan `<p></p>` pre tog `<div>`-a**, parser zatvori anchor, i telo kartice ispadne iz grid ćelije: slike ostanu u redu od 2, a tela se nasložu ispod preko cele širine.
- 🔴 **U `post_content`-u sve izgleda tačno.** Vidi se samo u renderovanom DOM-u (`document.querySelector('.al-grid--2').outerHTML`) — otud pravilo: **posle svakog ubacivanja kartica sa telom, pogledati grid u browseru, ne samo `curl`-ovati HTML i brojati klase.**
- **Rešenje:** kartica je `<div class="al-card">`, a link stoji na `.al-card__media` i unutar `.al-card__title`. Ceo blok time nije klikljiv, što je prihvatljivo i poklapa se sa obrascem koji već koriste reference-kartice na „O nama".
- 🟢 Postojeće `a.al-card` kartice (homepage „Šta radimo", padel modeli) su **bezbedne** — imaju samo `<span>` decu, pa wpautop nema gde da ubaci `<p>`. Pravilo važi samo za kombinaciju anchor + blok dete.

## `:not(.klasa)` broji kao klasa — pravilo za linkove u sadržaju je (0,3,1) i tiho gazi naivan override (2026-08-12)
- Naslov-link u `.al-card__title` je dobijao plavo podvlačenje od `.entry-content a:not(.al-btn):not(.al-card)` (`antas-design.css:1477`). Specifičnost tog selektora je **(0,3,1)** — jedna klasa plus **dva `:not()` koja svako broje kao klasa**.
- Naivni override `.al-card__title a` je **(0,1,1)** i gubi bez ijedne poruke; `getComputedStyle` pokaže `textDecorationLine: underline` iako je pravilo naizgled specifičnije jer je „bliže" elementu.
- **Metod za dijagnozu:** proći kroz `document.styleSheets`, filtrirati pravila koja postavljaju `text-decoration`, pa `element.matches(r.selectorText)` — dobija se tačan spisak pravila koja se takmiče, poređan po kaskadi. Brže i pouzdanije od čitanja CSS fajla.
- **Rešenje:** izuzetak se piše **istog oblika kao pravilo koje gazi** (`.entry-content .al-card__title a:not(.al-btn):not(.al-card)`), uz postojeće izuzetke za `.wd-post-title`/`.wd-entities-title`. Srodno F7.20 pravilu o `:is()` zamkama iz `base.css`.

## Ikonica se ne prihvata iz koda — mora se renderovati na obe veličine pored postojećeg seta (2026-08-12)
- Četiri nove SVG ikonice (`brzina`, `odbijanje`, `bez-pripreme`, `vatrootpornost`) tražile su **5 iteracija**. Redom su, iako je putanja u kodu bila „logična": `brzina` sa unutrašnjim šavom čitala kao **pola-popunjen krug**; `odbijanje` kao **kuka/laso**, pa kao **brda sa suncem**, pa kao **kvačica**.
- **Metod:** privremen HTML u root-u builda koji prikazuje nove ikonice na **46 px (stvarna veličina u kartici) i 120 px (za detalj)**, uvek **pored 2–3 postojeće iz seta** — poređenje težine linije i „gustine" je ono što otkriva grešku, ne gledanje same ikonice. Fajl se briše na kraju sesije.
- 🔴 **Semantički sudar se ne vidi u kodu:** `vatrootpornost` (plamen) i `odrzavanje` (kap) imaju **istu siluetu** na 46 px. Rešeno unutrašnjim plamenom. Uvek proveriti da nova ikonica nema blizanca u setu.
- Generator iz `design` skila (Gemini 3.1 Pro → SVG) je namerno preskočen: kad set ima čvrstu specifikaciju (24×24 viewBox, `stroke #F04D22`, `stroke-width 1.7`, round caps/joins, bez fill-a), ručno crtanje pogađa stil iz prve, a AI izlaz traži prepravku svejedno.

## Slika koje nema na lokalu može postojati u starom vault SQL backup-u kao live putanja (2026-08-12)
- Fotka „Dunk Shop" nije postojala nigde na lokalu: 0 pogodaka u `wp-content/uploads`, u DB (`post_content`, `post_title`, `guid`, `postmeta`, `options`) i u foto-arhivi `C:\Miroslav\Antas line\`.
- Nađena je `grep`-om kroz **starije SQL backup-e u vault-u** (`antasline-backups/*.sql`) — kao **serijalizovana apsolutna live putanja** (`s:73:"https://www.antasline.com/wp-content/uploads/2026/07/teren-dunk-shop.jpeg"`) zaostala u meta podacima iz nekog ranijeg uvoza sa live-a.
- Srodno: „Skill građen iz JEDNOG izvora nasleđuje njegovu grešku" (2026-08-12) i lekcija o obrisanim blokerima bez ✅ traga (2026-08-11) — iste porodice, sve tri o tome da dokumentacija laže tamo gde je niko ne gleda.

## Hladan start XAMPP-a: prvi zahtev 134s, a CDP timeout izgleda kao pokvaren pregledač (2026-08-12)
- Chrome merenje na lokalu palo je sa `CDP sendCommand "Runtime.evaluate" timed out after 45000ms` + „The renderer may be frozen or unresponsive". Renderer nije bio zamrznut — Apache je bio ugašen, pa je posle pokretanja prvi zahtev sa **praznim opcache-om trajao 134s**. Drugi 11,7s, treći 6,4s.
- 🔴 Zamka je u poruci: govori o pregledaču, a uzrok je na serveru. Lako vodi u pogrešnu dijagnostiku (restart ekstenzije, drugi tab, „CDP je nepouzdan").
- **Pravilo: pre bilo kakvog Chrome merenja na lokalnom buildu prvo `curl -o /dev/null -w "%{http_code} %{time_total}s"` na ciljni URL** — dokazuje da Apache radi i zagreva opcache. Tek kad `curl` vrati razuman broj, otvarati pregledač.
- Povezano: XAMPP opcache je uključen 2026-07-09 baš zbog TTFB-a (v. `## XAMPP / lokalno okruženje`) — ali opcache je **per-proces**, pa svako gašenje Apache-a vraća punu cenu prvog zahteva.

## Deprecation u pregledaču se proverava `getComputedStyle`-om, ne čitanjem CSS-a (Chrome 149 tabele, 2026-08-12)
- Chrome 149 je izbacio `border-color: gray` iz UA stila za tabele. Zvučalo je kao rizik za sve spec tabele na proizvodima; stvarni odgovor je bio **0 pogođenih ivica**.
- Metod koji je to dokazao za ~20 min, umesto vizuelnog pregledanja stranica: `getComputedStyle` nad svakim `table/th/td`, pa filter **„boja ivice == `color` elementa"** (tj. pala je na `currentColor`). Nula pogodaka = nema oslanjanja na UA default. Isti obrazac radi za svaku buduću UA deprecation — traži se **posledica**, ne pravilo.
- Dva razloga zašto nas nije dodirnulo, oba vredna kao pravilo: (1) **nijedna objavljena stranica ne koristi HTML atribut `border=`** — a samo je taj slučaj zavisio od UA boje; (2) i WoodMart (`var(--brdcolor-gray-200/300)`) i `.al-table` (`rgba(22,40,60,0.12)`) deklarišu boju eksplicitno.
- 🟢 Usput potvrđeno zašto izmena prolazi nezapaženo i tamo gde bi „trebalo" da se vidi: WoodMart reset postavlja `border:0` na `table/th/td`, pa sam `<table>` ima `border-style: none` — njegova `border-color` (koja jeste `currentColor`) nema šta da oboji.
- Vezano: [[analiza/2026-08-11-snapshot-jul]] §1.1b/§1.2.

## LiteSpeed LQIP može izgledati "mrtvo" na cloud strani a stvarno padati lokalno (2026-08-11)
- `placeholder.cls.php` (`_generate_placeholder()`) radi `File::is_404($url)` proveru **lokalno, PRE** bilo kakvog QUIC.cloud poziva. Ako padne, slika ide odmah trajno u `media-lqip_exc` (exclude listu) i cloud se **nikad ne kontaktira** — `curr_request`/`last_request.lqip` timestamp u `litespeed.cloud._summary` se ažurira tek POSLE tog 404-checka, pa ostaje zamrznut dok se lokalni problem ne reši.
- Posledica: gledanje samo cloud usage brojača/`last_request` timestampa daje lažan utisak "ništa se ne dešava" iako se lokalno stalno nešto pokušava i odbija — obrnuto od starog poznatog QUIC.cloud/firewall obrasca (gde je problem bio na cloud/mrežnoj strani, ne lokalnoj).
- **Provera koja ovo hvata**: uporediti `wp-content/litespeed/{ccss,ucss,lqip,vpi}/*.css`/slika mtime na disku (stvarna generisana aktivnost) sa `litespeed.cloud._summary` timestampovima (šta cloud misli da se dešava) — razlika između njih je signal da problem nije mrežni/cloud nego lokalni. Rastuća `media-lqip_exc` lista sa datumima NOVIJIM od poslednjeg uspešnog `last_request` je dokaz aktivnog, ne istorijskog problema.
- Vezano: [[dnevnik/2026-08-11-litespeed-ccss-ucss-lqip-vpi-status]].

## cPanel privilegovani uapi pozivi (`lswsAdminBin`) se izvršavaju SAMO iz prave cpsrvd browser-sesije, ne sa terminala (2026-08-11)
- `uapi lsws redisAble` i `uapi lsws packageUserSize` (koje "LiteSpeed Redis Cache Manager" UI dugme zove) vraćaju grešku `Parent check method: /usr/local/cpanel/cpanel, caller: /usr/local/cpanel/uapi is not allowed` kad se pozovu direktno preko SSH/terminal `uapi` CLI-ja, čak i kao vlasnik naloga.
- Ovo je namerna cPanel bezbednosna zaštita (privilegovani `lswsAdminBin` pozivi), ne bag i ne nešto što treba zaobilaziti — na deljenom hostingu terminal ima manje ovlašćenja od prave UI sesije za određene admin funkcije.
- **Praktična posledica za buduće `[cpanel-live]` sesije**: ako neka cPanel funkcija (dugme u panelu) ne radi kad se pokuša automatizovati preko terminala istim uapi pozivom, prvo proveriti da li greška pominje "Parent check method"/"is not allowed" — ako da, to je znak da ta akcija zahteva da je Miroslav sam klikne u browseru, ne dalje debug-ovanje poziva.
- Nije opšte SQL ponašanje — specifično za GAQL, i pogađa svaki upit nad `ad_group_criterion` / `campaign_asset` / `ad_group_asset`.

## Python `print` na Windows-u puca čim ćirilični izlaz ode u fajl (2026-08-11)
- Konzola je cp1250; dok se gleda na ekranu radi, ali `> fajl.json` daje `UnicodeEncodeError` i **prazan fajl uz exit 1**.
- Test bez staging-a: prepisati pravila u izolovan `htdocs/<test>/` folder sa prefiksiranim putanjama i pustiti curl. Testira stvarni Apache (sintaksa, sidrenje, ćirilica) bez diranja živog `.htaccess`-a — plus **negativna kontrola** (URL-ovi koji NE smeju dati 301).

## WoodMart kači stilove na prioritetu 10000 — dequeue na „normalnom" prioritetu tiho ne radi ništa (2026-08-11)
- `woodmart_enqueue_base_styles` → **`wp_enqueue_scripts` prioritet 10000** (tu ide i `js_composer_front`), `woodmart_force_enqueue_styles` → **10001**. Naš prolaz mora na **10002**.
- Prvi pokušaj na prioritetu 100 je prošao **bez ijedne greške i bez ijedne promene** — merenje asseta je jedini način da se to primeti.
- `wc-blocks-style` je poseban slučaj: WooCommerce ga stavlja u red iz `Blocks/Domain/Services/Notices.php` na **`wp_head` prioritet 10**, dakle posle celog `wp_enqueue_scripts` ciklusa — nijedan prioritet tamo ga ne hvata, hook mora biti `wp_head` 11.
- Dijagnostički trik: hook na `wp_head` 999 i ispis `in_array($h, $wp_styles->queue)` vs `->done` pokazuje da li je stavka još u redu i da li je već odštampana.

## Katalog režim skida DUGME, ne varijacijsku formu — `wc-add-to-cart-variation` nije mrtav (2026-08-11)
- Sve je delovalo mrtvo: `catalog_mode`=true, **0** `<form class="cart">` na celom sajtu. Dequeue je izveden i „radio".
- Ali 20 varijabilnih proizvoda i dalje renderuje `variations_form`, a WoodMart `swatchesVariations.min.js` zavisi od `wc_add_to_cart_variation_params` iz baš te skripte → izbor boje prestaje da menja sliku. Vraćeno.
- Pravilo: odsustvo add-to-cart forme **ne dokazuje** da je varijacijski JS nepotreban. Pre gašenja bilo koje WooCommerce skripte — pravi klik u pregledaču na varijabilnom proizvodu.

## Meriti `vc_` markup isključivo u `<body>` — `<head>` daje lažni pozitiv (2026-08-11)
- Inline CSS u `<head>` sadrži `vc_row`/`vc_column` **selektore**, pa brojanje po celom dokumentu javlja „ima WPBakery" i na stranicama koje nemaju nijedan element.
- Ista zamka važi za svaku „koristi li se X" proveru koja gleda sirov HTML: prvo odseći `<head>`.
- Usput: WPBakery **sam** ima ispravnu proveru (`Vc_Base::enqueueStyle()` traži `[vc_row` u sadržaju) — WoodMart je pregazi bezuslovnim enqueue-om. Vredi proveriti da li tema gazi plugin pre nego što se piše sopstvena logika.

## `curl -o fajl` u ovom git bash okruženju upisuje 0 bajtova (2026-08-11)
- `curl -s -o /dev/null -w '%{http_code}'` radi normalno (status kodovi tačni), ali `-o neki/fajl.html` da prazan fajl — i u `/tmp` i u job folderu.
- Posledica: `curl ... | grep` analiza HTML-a tiho vraća 0 pogodaka i deluje kao da traženog obrasca nema.
- Za analizu HTML-a koristiti PHP (`file_get_contents`, `curl_multi`), ne bash curl.

## Mrtav CPT nije neutralan — brisati postove je nedovoljno, plugin i `cptui_*` opcije nose zamku dalje (2026-08-11)
- Legacy CPT sa **0 objavljenih postova** i dalje registruje rewrite pravilo koje stoji **ispred** generičkog page pravila → svaka dvosegmentna putanja pod istim slugom tiho postaje 404 (tako je 29.07 oboreno 6 pod-stranica).
- Pravilo živi u keširanoj `rewrite_rules` opciji i pojavljuje se **tek na flush** — kvar isplivava sesiju-dve kasnije, naizgled bez uzroka.
- Potpuno čišćenje = obrisati postove **+ deinstalirati plugin + obrisati `cptui_post_types`/`cptui_taxonomies`/`cptui_new_install`** (prva je bila 12,3 KB sa `autoload=yes`, dakle na svakom zahtevu) **+ `rewrite flush`** pa provera `get_post_types()`.
- Filter koji gasi `public`/`rewrite` je dobra privremena mera, ali ga zadržati i posle brisanja: stari bekapi baze i dalje nose `cptui_*`, pa bi restore bez njega vratio zamku.

## Pre brisanja postova sa prilozima: odvezati priloge (`post_parent`→0), ne oslanjati se na WP ponašanje (2026-08-11)
- 41 CPT zapis je držao **211 priloga** kao decu; mnoge od tih slika su u aktivnoj upotrebi na *novim* stranicama (Bergo galerije).
- Zato izvoz sadržaja u vault (`.md` + `.json`) pre brisanja, nezavisno od toga što je SEO vrednost nula. SQL bekap je za rollback, arhiva je za čitanje.

## XAMPP: `wp db export` puca na „mysqldump is not recognized", `wp db query` tiho vraća prazno (2026-08-11)
- `mysqldump` nije na PATH-u → pre poziva `export PATH="$PATH:/c/xampp/mysql/bin"`.
- Košta 30 sekundi i jedina je razlika između „provereno" i „izgleda kao da je provereno".

## `LIKE '%Ä%'` u WP bazi lovi i obično `a` — kolacija je akcent-neosetljiva (provera dijakritike, 2026-08-11)
- Kolone `wpGs_posts.post_title` i `wpGs_postmeta.meta_value` su `utf8mb4_unicode_520_ci` — **akcent- i case-neosetljiva** kolacija. Zato `LIKE '%Ä%'` uredno pogađa `a`/`A`, a `LIKE '%ć%'` broji i svako `c`.
- Praktična posledica: prva provera „ima li mojibake u naslovima" prijavila je ~385 lažnih pozitiva i izgledala kao veliki incident. Ponovljena sa `LIKE BINARY` → **0 nalaza**.
- Pravilo: kad se traži **oblik zapisa** (mojibake, dijakritika, znak zamene), uvek `LIKE BINARY` ili `COLLATE utf8mb4_bin`. Obično `LIKE` je za značenje/pretragu, ne za forenziku enkodinga.

## UTF-8 ne sme kroz `mysql -e "..."` na ovom Windows shell-u — a `SELECT` to NE otkriva (meta opisi, 2026-08-11)
- Srpski tekst prosleđen kao `mysql -u root baza -e "UPDATE ... SET meta_value='… ć š ž …'"` stiže **u bazu** sa `?` umesto dijakritika — konzolni codepage ga pojede pre nego što stigne do klijenta.
- Podmuklo: provera `SELECT`-om u istoj konzoli pokazuje iste `?`, pa izgleda kao kozmetički problem **prikaza**, ne kao stvarno oštećenje podatka. Zaključak „samo konzola ne ume da prikaže" je pogrešan i propušta bag.
- Ispravno: napisati `.sql` fajl (Write alat) koji počinje sa `SET NAMES utf8mb4;` pa `mysql --default-character-set=utf8mb4 -u root baza < fajl.sql`. Tako pisani upisi su bili tačni iz prve.
- Za pre-migracioni audit bar jednom uporediti **sitemap skup vs. `post_status='publish'` iz baze** — razlika su stranice koje niko ne proverava.

## mu-plugins SE prenose sa `wp-content` — komentar u fajlu koji tvrdi suprotno je bio uzrok pravog kvara (W3 3.10, 2026-08-10)
- `al-local-mail-log.php` presreće **svaki** `wp_mail` poziv i vraća „uspeh" (XAMPP nema SMTP). U svojoj glavi nosi komentar: *„OBRISATI PRE MIGRACIJE — mu-plugins se ne prenose, ali za svaki slučaj…"*.
- Ta tvrdnja je netačna: `mu-plugins` je pod-folder `wp-content`, i putuje sa svakim `wp-content` paketom. Tako je 2026-08-07 otišao na staging V3, gde su forme prikazivale uspeh a **nijedan mejl nije stvarno poslat** — otkriveno tek ručnim testom.
- **Pravilo:** ne oslanjati se na komentar u fajlu ni na „obrisaću pre migracije" (na dan migracije se ne pamti). Zaštita mora biti u alatu: `build-staging-package.sh` od 10.08 ima exclude za ovaj mu-plugin i za `mail-log.txt`.
- Šire: **lokalni presretači koji lažu o uspehu su najopasnija klasa** — ne ruše ništa vidljivo, samo tiho gutaju. Posle svake migracije forma se mora testirati pravim submit-om i proverom inbox-a, ne gledanjem „hvala" stranice.

## `*.bak-*` fajlovi u `wp-content` se serviraju kao ČIST IZVORNI KOD (W3 3.10, 2026-08-10)
- Izmereno, ne pretpostavka: `GET /wp-content/themes/woodmart-child/functions.php.bak-2026-08-10-…` → **HTTP 200, 53 KB PHP izvora**. Apache izvršava samo `.php`; `.bak-…` završetak znači „nepoznat tip" → servira se kao tekst.
- Naša konvencija „backup pre svake izmene" je do 10.08 proizvela **27 takvih fajlova** u `wp-content` (child tema, mu-plugins, CSS). Paket-skripta ih je sve pakovala za produkciju.
- U ovom slučaju nije bilo kredencijala unutra (provereno), ali sadržaj otkriva logiku court-builder tokena, honeypota i rate-limita.
- **Pravilo:** za footer/sidebar linkove proći kroz sve `widget_*` opcije (`widget_text`, `widget_custom_html`, `widget_block`, `widget_nav_menu`), pa TEK ONDA verifikovati HTTP-om. I obavezno verifikovati posle popravke — brojač zamena „1" ne znači da je link nestao sa stranice.

## `strip_tags()` ZADRŽAVA sadržaj `<script>` — provera „sirov JSON-LD u tekstu" daje lažni pozitiv (W3 3.10, 2026-08-10)
- Provera za F7.15 obrazac (kses pojeo `<script>` omotač pa se JSON vidi kao tekst) pisana je kao `preg_match('#"@context"…#', strip_tags($html))` → prijavila je problem na **svih 195 stranica**, tj. na svakoj koja uopšte ima schema-u.
- `strip_tags()` uklanja tagove ali ostavlja njihov tekstualni sadržaj, uključujući telo `<script>`.
- **Pravilo:** prvo `preg_replace('#<(script|style)\b[^>]*>.*?</\1>#is', '', $html)`, pa onda `strip_tags()`.
- Šire pravilo: pre nego što se obeća „uradiću to kroz browser", proveriti da li stranica uopšte izlaže file input u DOM-u — nema smisla trošiti runde na klikanje po meniju koji vodi u nativni dijalog.

## Bulk WP attachment import (12+ Gemini slika u JEDNOM PHP procesu) puca na 300s execution limit — deliti na pojedinačne pozive (2026-08-08)
- Pokušaj da se 12 novogenerisanih color-swatch slika (Condor Schools/Playgrass variation-slike) uveze u jednoj PHP skripti (petlja preko `wp_insert_attachment()`+`wp_generate_attachment_metadata()`) je pukao na `PHP Fatal error: Maximum execution time of 300 seconds exceeded` — WP je to prikazao kao generičku `wp_die()` "kritična greška" stranicu na stdout-u (exit 255), bez ijedne linije stvarnog izlaza skripte, pravi uzrok vidljiv jedino u `wp-content/debug.log`.
- **Pravilo ubuduće:** kad treba uvesti VIŠE od par slika kao WP attachment (svaka nosi `wp_generate_attachment_metadata()` resize trošak + WP bootstrap overhead), pokretati **jedan PHP proces po slici** (kao već testiran `import-gemini-photo.php <post_id> <src> <dest>` obrazac iz 2026-08-05), ne petljati sve u jednom skriptu — svaki poziv dobija sopstveni 300s budžet umesto da se troši kumulativno. Prvo proveriti `debug.log` kad `wp-load.php` skripta pukne bez izlaza (WP guta pravi PHP fatal iza "kritična greška" stranice).
- Usput: `credentials/` folder na cPanel serveru (`~/antasline-connector/credentials/`) mora biti pravi poddirektorijum — fajlovi prekopirani direktno u `~/antasline-connector/` (bez `credentials/` nivoa) ne rade, `auth.py`/`credentials_dir()` ih ne vidi bez jasne greške dok se ne pokuša pristup.

## Katalog režim (M9) je učinio ceo WC add-to-cart JS stek mrtvim sitewide — proveriti pre nagađanja "šta se može isključiti" (2026-08-07)
- Kad je M pitao za višak CSS/JS u temi, prva pretpostavka bi bila da je WC-ov add-to-cart JS potreban bar na proizvod-stranicama. Provera je pokazala suprotno: `catalog_mode` (WoodMart nativna opcija + child-theme override na `woocommerce_single_product_summary` prio 30) zamenjuje SVAKO add-to-cart dugme (single + loop, čak i na proizvodima sa pravom cenom) linkom ka `/kontakt/` — nema nijedne `<form class="cart">`/`.single_add_to_cart_button` na sajtu. `wc_get_page_id('cart')`/`('checkout')` pokazuju na postove koji fizički ne postoje u bazi.
- **Pravilo ubuduće**: pre dequeue-a bilo kog "očigledno WooCommerce" skripta/stila, prvo proveriti da li postoji STVARNA funkcionalna meta na sajtu (`is_purchasable()` sam po sebi ne dokazuje ništa ako custom kod već preusmerava UI drugde) — grep za override hook-ove u child temi (`woocommerce_single_product_summary`, `catalog_mode`) pre curl provere enqueue liste.
- Dequeue uvek u **child** temi (`woodmart-child/functions.php`), nikad u parent `woodmart` temi — parent theme update briše sve, child preživljava. Postojeći W3 3.6 `sourcebuster-js`/`wc-order-attribution` dequeue je isti obrazac, ovaj nalaz ga samo proširio sitewide.

## jQuery Migrate/interaktivni JS dequeue zahteva pravi browser test, curl ne dokazuje ispravnost (2026-08-07)
- Za razliku od WC script dequeue-a (dokazivo curl-om — handle ili jeste ili nije u HTML-u), uklanjanje `jquery-migrate` kao dependency ne može se smatrati bezbednim samo zato što se stranica i dalje učitava 200 — greška bi bila tiha (dropdown se ne otvara, canvas ne reaguje na klik), ne HTTP status.
- **Pravilo ubuduće**: svaki dequeue koji dira ZAJEDNIČKU (ne feature-specifičnu) skriptu poput `jquery`/`jquery-migrate` mora se testirati uživo kroz Chrome na najkompleksnijoj interaktivnoj komponenti na sajtu (ovde: court builder canvas — klik-farbanje mreže, live tabela) PRE nego što se izmena proglasi gotovom, ne samo na jednostavnim linkovima/formama.
- **Pravilo ubuduće:** posle svakog `computer` click+type unosa u formu koja izgleda "nemo" (nema vizuelne potvrde ili je screenshot sumnjivo mali/prazan), PRE bilo kakve nepovratne akcije (Submit/Publish/Save) proveriti stvarnu DOM vrednost preko `javascript_tool` (`document.querySelector(...).value`) — ne verovati samo uspešnom povratu `computer` tool-a. Ako je prazno, koristiti native-setter+dispatchEvent obrazac umesto ponovnog pokušaja klika.

## FTP "451 Error during write to file" na velikom transferu = disk kvota, NE mreža/firewall (staging refresh, 2026-08-06)
- Pokušaj slanja pune 3,18 GB staging arhive preko FTP-a (`staging@antasline.com`) je konzistentno padao usred transfera (`curl: (55) Send failure: Connection was aborted/reset`), uvek posle kratke inicijalne eksplozije podataka pa potpunog zastoja ~15-20s pa reseta — simptom koji IZGLEDA identično nestabilnoj wifi/NAT/firewall konekciji (baš je i takva dijagnoza prvo pretpostavljena, uz pokušaje chunk-ovanja/resume-a preko `-C -`).
- **Pravi uzrok otkriven tek kad je i 5-bajtni test fajl pao** sa `451 Error during write to file` — to je server-side (Pure-FTPd) greška, ne mrežni prekid. FTP nalog je imao disk kvotu (~530–560 MB, `.ftpquota` fajl vidljiv u root listing-u tog naloga) koja se tiho ispraznila oko pola giga uploadovanog sadržaja; svaki dalji pisanje (čak i 5 bajtova) je odbijeno. Potvrđeno brisanjem dela već-otpremljenog sadržaja → test fajl odmah prošao.
- **Pravilo ubuduće:** kad veliki FTP transfer konzistentno umire na (grubo) istoj kumulativnoj količini podataka bez obzira na chunk veličinu/resume pokušaje — prvo posumnjati na kvotu naloga (probaj upload trivijalnog test fajla), tek onda trošiti vreme na mrežnu dijagnostiku (chunking, resume, retry loops). `curl -I ftp://.../fajl` vraća `Content-Length` preko `SIZE` komande — koristan brz način provere koliko je stvarno stiglo na server bez punog re-liste.
- **Rešenje kad je kvota stvarni limit:** ne šalji sve odjednom — razdvoji na (a) kod-only paket (tema/plagini/WP core, bez media biblioteke) i (b) diff-only paket (samo NOVI/izmenjeni fajlovi od poslednjeg punog uploada, filtrirano po mtime + putanji na trenutni mesec/godinu folder da se izbegnu lažni pogoci od starih fajlova čiji je mtime dirnut nekim ranijim bulk restore/copy procesom bez stvarne izmene sadržaja).

## Staging kod-paket sa lokala nosi i `wp-config.php`/`.htaccess` — oba se moraju ručno ispraviti posle raspakivanja (staging refresh, 2026-08-06)
- Kod-only tar.gz (tema/plugin/core) pakovan direktno iz lokalnog XAMPP docroot foldera povlači i `wp-config.php` (DB_NAME/DB_USER/lozinka za `antasline_local`/`root`) i `.htaccess` (WordPress blok sa `RewriteBase /antasline/`, i briše Basic Auth blok koji na stagingu stoji IZNAD WordPress bloka). Poznat je bio samo `.htaccess` gotcha (07-21); `wp-config.php` prepis nije bio predviđen u planu i probio je Korak 4 ("samo proveri da radi") jer je fajl izgledao kao da postoji ali su vrednosti tihe pogrešne.
- **Pravilo ubuduće za svaki sledeći FTP/kod-paket refresh**: posle raspakivanja koda, PRVO proveriti `grep DB_NAME wp-config.php` (očekivati `antasline_staging`, ne `antasline_local`) PRE bilo kakvog `wp option get` poziva — greška je vidljiva odmah kao "Access denied for root@localhost" a ne kao suptilan podatak problem.
- `.htaccess` Basic Auth se popravlja ručno odmah; `RewriteBase`/`index.php` deo WordPress bloka se sam ispravi posle `wp rewrite flush --hard` (Korak 7) jer WP taj blok generiše iz tekućeg `siteurl`-a — ne treba ga ručno dirati, samo ne preskočiti flush korak.

## Tabela prefiks u SQL dump-u može biti `wpgs_` (malo slovo) iako je dokumentacija svuda "wpGs_" (staging refresh, 2026-08-06)
- `lower_case_table_names=0` na ovom MySQL serveru (Linux, cPanel) — dakle server ništa ne lowercase-uje sam; ako je stvarni sadržaj dump-a `wpgs_*`, to je tako pisano od izvora (verovatno kako se ispostavilo da lokalni MariaDB export ozbiljno tretira case iz nekog ranijeg koraka gde je prefiks svuda upisan malim slovom, uprkos tome što se u pisanoj dokumentaciji — CLAUDE.md, PROGRESS, DNEVNIK — svuda referiše kao "wpGs_").
- Simptom: `wp option get siteurl` posle importa vraća "The site you have requested is not installed... Found installation with table prefix: wpgs_" iako `$table_prefix` u `wp-config.php` piše `'wpGs_'` slovo-po-slovo iz uputstva.
- "Proksi" u razgovoru se ispostavio da znači **claude-code-router (CCR)** — lokalni alat koji rutira Claude Code-ove sopstvene tekst/coding pozive ka drugim provajderima (DeepSeek/Gemini/Groq) po kategoriji zadatka (`default`/`background`/`think`/`longContext`), ne mrežni proxy za regionalni pristup. Kad korisnik pomene "proksi"/"ruter" u kontekstu AI alata, proveriti da li misli na network proxy ili na model-routing alat pre planiranja.

## Zion Builder forma na live sajtu tiho odbija validne unose — regex validacija bez poruke greške (kontakt forma, 2026-08-04)
- `zn_validate_is_letters_ws` (Firma/Ime) je `e.val().match(/[^A-Za-z\s]/i)` — prihvata SAMO ASCII slova+razmak. Brojevi, tačke, "&", crte, pa i **srpska slova sa dijakritikom (ćčžšđ) i ćirilica** odbijaju unos ("Antas d.o.o." bi palo).
- `zn_validate_is_numeric` (Kontakt telefon) je `isNaN(e.val())` — razmaci/crte/`+` odbijaju unos. Ironično: broj je svuda na sajtu ispisan baš sa razmacima ("069 234 00 72"), pa korisnik koji kuca po tom uzoru dobija tihu blokadu.
- Kad validacija padne: JS doda crvenu ivicu (`zn_field_not_valid`) na polje i **NIKAD ne pošalje AJAX ka `admin-ajax.php`** — nema poruke, nema redirect-a. Dokazivo samo network-om (0 zahteva), ne vizuelnim pregledom (polje samo ima suptilnu crvenu ivicu).
- **Test protokol koji je ovo otkrio:** popuniti formu čistim tekstom prvo (baseline uspešan submit + redirect potvrđen), pa ciljano probati "prljave" varijante (crta/tačka/razmak) jedno po jedno da se izoluje TAČNO koje polje/karakter puca — ne menjati više polja odjednom, inače se ne zna koje je uzrok.
- Nije nova regresija — JS nepromenjen, dugogodišnji baseline gubitak submit-ova. Primenjivo na SVAKU Zion Builder/Kallyas formu na live sajtu (49 formi sitewide koriste isti obrazac po M5 audit-u od 07-30), ne samo `/kontakt/`.

## WoodMart core widget naslov tag je JEDNA opcija koja pokriva 8+ widget area-a odjednom — `widget_title_tag` (heading-order fix, 2026-08-04)
- `woodmart_get_widget_title_tag()` (`inc/integrations/woocommerce/helpers.php:10`) čita `xts-woodmart-options['widget_title_tag']` (default `h5` ako nije eksplicitno setovano, ali na ovom sajtu JESTE eksplicitno `h5` u 883-key opcionom nizu). Koristi ga: glavni sidebar, portfolio sidebar, shop sidebar, shop filteri, single-product sidebar, my-account (deprecated), full-screen menu, mobile-menu-widgets, SVE footer kolone, + "You may also like" upsells naslov na proizvodu — jedna promena pogađa praktično ceo sajt.
- Bio je uzrok sitewide `heading-order` Lighthouse a11y crvenog nalaza: svuda gde H2/H3 glavni sadržaj prethodi widget-u (skoro svaka stranica sa sidebar-om ili footer-om), skače se na H5, WCAG heading-order violation.
- **Fix je bezbedan po konstrukciji**: dizanje nivoa (h5→h3) nikad ne UNOSI nov skip, samo uklanja postojeći — potvrđeno da je nivo neposredno pre widget-a na testiranim stranicama uvek H2 ili H3 (nikad H4), pa h3 posle njih je uvek validno. Ne treba proveravati svaku stranicu pojedinačno pre primene ovakve promene, dovoljno je proveriti par reprezentativnih tipova.
- Promena je `update_option('xts-woodmart-options', ...)` sa samo tim jednim ključem izmenjenim (ne dirati ostatak 883-key niza) — ne postoji poseban Customizer URL/settings panel prečica za ovo, ide direktno kroz opcioni niz.
- **Ne meša se sa `wd-post-title`** (blog/post kartice) — to je ODVOJEN hardkodovan `<h3>` koji ne ide kroz ovu opciju, pa blog arhiva (`/aktuelnosti/`) i dalje ima svoj heading-order problem (H1→H3 skip) nedirnut ovim fix-om. Kad se traži "sve heading-order nalaze odjednom", proveriti oba mehanizma odvojeno.
- ⚠️ **Fix je u VENDOR fajlu, ne child temi** — `woodmart` theme update će ga prebrisati. Backup ostavljen kao `class-breadcrumbs.php.bak-2026-07-30`; proveriti ovo mesto (`json_decode()` test na bilo kojoj stranici koja koristi native breadcrumb) posle svakog WoodMart ažuriranja, uključujući migraciju na live.

## Lighthouse "agentic-browsing" kategorija — nije CLI preset, i lokalni podfolder je lažira `llms.txt` proveru (2026-07-30)
- Nova Lighthouse 13.4 kategorija (`agentic-browsing`) nije ožičena kao `--only-categories` preset dostupan iz kutije — mora se pozvati direktno preko `--config-path=node_modules/lighthouse/core/config/agentic-browsing-config.js` (naći pravi npx keš folder: `~/AppData/Local/npm-cache/_npx/<hash>/node_modules/lighthouse`, grep `package.json` za `"lighthouse"`). Zahteva Chrome 150+.
- 🔴 **`llms-txt` audit fetch-uje `/llms.txt` na KORENU domena** (`new URL('/llms.txt', finalDisplayedUrl)`), ne relativno na testiranu stranicu. Na lokalnom XAMPP buildu WP živi u `htdocs/antasline/` podfolderu, pa `localhost/llms.txt` 404-uje dok `localhost/antasline/llms.txt` vraća 200 — audit javlja `notApplicable` iako fajl postoji i sadržajno prolazi sve kriterijume (H1+link+dužina). Ista klasa greške kao ostale lokal-vs-live path razlike u ovom fajlu — **proveriti ručno sadržaj pre nego što se poveruje "crvenoj" oceni koja zavisi od apsolutnog path-a**. Na produkciji (koren=koren) proći će stvarno.
- `agent-accessibility-tree` audit koristi UŽI podskup ARIA/naming pravila (~29, npr. `link-name`/`label`/`document-title`) nego pun Accessibility kategorija — 1/1 ovde NE znači pun a11y skor 100 (baseline W3 3.5 je 84–90), znači samo da mašinski-kritična imena/uloge prolaze.
- CLI cleanup na Windows-u posle `--quiet` završetka ume da baci benignu stack-trace iz `chrome-launcher` `destroyTmp()` (tmp folder race) — JSON izlaz je već ispravno napisan PRE te greške, ne prekida rezultat. Detalji: [[dnevnik/AGENTIC-BROWSING-AUDIT]]

## Deljene WoodMart CSS promenljive imaju širi domet nego što ime kaže (W8 polish, 2026-07-29)
- 🔴 **`--wd-title-font` nije "font naslova" — koristi ga i `table th`, `.wd-nav-tabs>li>a` (tab navigacija), `.title`, `.font-primary`, `legend`, cart-block naslovi.** Kad je promenljiva globalno postavljena na Bebas Neue (za H1/H2), Bebas + faux-bold (weight 600 na fontu koji ima samo 400) je tiho procurio i u tabele tehničkih karakteristika i u Opis/Dodatne informacije tabove — "nabijena slova" žalba je zapravo ovaj font na pogrešnom mestu, ne CSS bag u letter-spacing-u. **Pre nego što se globalna WoodMart promenljiva prepravi, grep-ovati pun spisak selektora koji je koriste u `style.min.css`** (`grep -o "[^}{]*{[^}]*var(--wd-ime)[^}]*}"`) da se zna stvarni domet pre izmene. Fix ovde je bio TARGETIRAN (`table th`, `.wd-nav-tabs>li>a { font-family: var(--al-text) }`), ne menjanje same promenljive (previše potrošača, nepoznat rizik).
- WooCommerce native `.shop_attributes th/td` NIJE bio pogođen istim curenjem — ima `font-family:inherit` sa višom specifičnošću (`.shop_attributes :is(th,td)` > bare `table th`), pa nasleđuje od roditelja umesto promenljive. Pre popravke proveriti da li je nešto već slučajno imuno, da se ne pravi nepotrebna izmena.

## Hero foto overlay — horizontalni gradijent se lomi na mobilnom (W8 polish, 2026-07-29)
- **Overlay gradijent dizajniran za desktop raspored (tekst levo ~40%, slika desno) ima jak alfa levo i slab desno** (`linear-gradient(90deg, rgba(navy,.94) 0%, ... .28 100%)`). Na mobilnom tekst zauzima punu širinu i upada i u slabi pojas — čitljivost pada tačno na uskim ekranima gde je najbitnija. Ispod breakpoint-a (767px) overlay treba da postane UJEDNAČEN (vertikalni gradijent, viša minimalna neprozirnost), ne isti horizontalni recept skaliran na uži ekran.
- **`text-shadow` je jeftina, foto-nezavisna sigurnosna mera** za tekst preko fotografija: neke fotke imaju svetle/bele oblasti (npr. bela garažna vrata) tačno iza naslova gde ni tamniji overlay nije dovoljan. Dodat kao dodatni sloj, ne zamena za overlay.
- 🔴 **Iframe QA harness (`al-harness.html`) i dalje ima dokumentovan render-artefakt na TEKSTU pri uskim širinama** (ista zamka kao N5 sesija 2026-07-29: "iframe... pokazao teško preklopljen H1 tekst... direktna navigacija potvrdila da je stranica potpuno čista"). `resize_window` alat i dalje ne radi (tiho ne menja veličinu, screenshot ostaje na desktop širini bez greške). Kad je pitanje SPECIFIČNO o čitljivosti teksta (ne o layout/overflow), matematička provera kompozitne boje (overlay % preko uzorka piksela fotografije) je pouzdanija od iframe screenshot-a.

## WPBakery hero `background-image` se otkriva kasnije od `.al-section--navy` boje → vizuelni blesak (W8 polish, 2026-07-29)
- Po-stranici hero pozadina živi u WPBakery `css` atributu (`.vc_custom_heroFxxxxx{background-image:url(...)}`), koji je deo istog render-blocking CSS lanca kao poznati W3 3.6 nalaz (js_composer 437KB, namerno odloženo na LiteSpeed produkciju). Rezultat: navy boja (mala, brzo-parsirana pravila) se crta pre nego browser otkrije URL fotografije.
- **Fix bez diranja redosleda/bundle-a CSS-a** (rizično, već procenjeno u ranijem auditu): `wp_head` filter koji regex-om izvuče URL iz `post_content` (`.vc_custom_hero\w+{...background-image:url(...)`) i emituje `<link rel="preload" as="image">` — čisto aditivno, daje browseru raniji hint, ne utiče na LCP merenje niti na postojeći render-blocking lanac.

## Meni — WoodMart mega-meni i WP jezgro (W7 F3, 2026-07-29)
- 🔴 **WoodMart walker ne resetuje `design` između grupa.** `class-mega-menu-walker.php`: `if ( 0 === $depth && $design ) { $this->design = $design; }` — `$this->design` je **svojstvo instance** i ostaje postavljeno kad sledeća grupa najvišeg nivoa nema svoj `_menu_item_design`. Posledica: grupa bez dizajna se renderuje kao `wd-design-sized` (nasledi od suseda) ali **bez `--wd-dropdown-width`**, pa se panel skupi na ~182px i stavke se lome u dva reda. **Pravilo: svaka grupa najvišeg nivoa dobija eksplicitan `_menu_item_design`** — nikad se ne oslanjati na podrazumevano.
- **Mega-meni = 3 nivoa bez trećeg `menu_item_parent` nivoa.** `_menu_item_design = 'sized'` (+ `_menu_item_width` u px) na grupi pretvara **decu na dubini 1 u kolone** (`wd-col` u `wd-grid-f-inline`), a unuci su stavke u kolonama. Kolona je fiksno ~200px; širinu panela računati kao `broj_kolona × 200 + ~70` (3 kolone → 760px, 2 → 540px), inače ostane veliki prazan prostor.
- `_menu_item_width` **radi samo** za `sized`/`aside`/`full-width`/`full-height`; kod `default` dizajna se ignoriše (`--wd-dropdown-width: 220px` je zakucano u temi).
- 🔴 **Prazan `post_title` stavke menija nije defekt.** `wp_update_nav_menu_item()` namerno upisuje prazan `post_title` kad je prosleđena labela **identična** naslovu ciljne stranice — stavka onda nasleđuje naslov i renderuje se ispravno. **Meriti renderovanu labelu (`.nav-link-text` u HTML-u), ne sirov `post_title`**, inače se dobija lažan spisak „stavki bez naslova".
- **Meni dodeljen preko header builder-a ne vidi se u `get_nav_menu_locations()`.** WoodMart header elementi referenciraju meni **po ID-u**, pa je „utility meni" (Početna/Aktuelnosti/O nama/Kontakt) bio potpuno nevidljiv proveri lokacija — i 4 stranice su pogrešno izgledale kao siročad. Proveriti i renderovani HTML (`<ul id="menu-…">`), ne samo lokacije.
- **Rebuild menija raditi kao NOV term, pa prebaciti lokaciju.** Povratak je onda jedna izmena `nav_menu_locations`, bez vraćanja `wp_posts` iz dumpa. Stari term ostaviti dok se novi ne potvrdi.
- Trajna provera: `migracija/alati/al_check_breadcrumbs.php` (BreadcrumbList schema iz živog HTML-a vs `post_parent` lanac, za sve ugnježdene stranice).

## Rewrite pravila i mrtvi CPT-ovi (W7 F2.9, 2026-07-29)
- 🔴 **Mrtav CPT nije inertan zapis nego aktivna zamka.** Legacy CPT `spoljne-podne-obloge` iz Custom Post Type UI (ostatak Porto sajta, **0 objavljenih postova**) registruje pravilo `spoljne-podne-obloge/([^/]+)/?$ → index.php?spoljne-podne-obloge=$matches[1]`, koje stoji **ispred** generičkog page pravila. Rezultat: **svih 6 pod-stranica stranice sa istim slugom vraćalo je 404**, dok je hub (jedan segment) radio — pa se kvar nije video na njemu. Yoast je usput hub-u lepio `noindex, follow`, što je nestalo čim je rutiranje popravljeno.
- **Baza može biti potpuno ispravna dok URL 404-uje.** `post_parent`/`post_name`/`post_status` tačni, `get_page_by_path()` **i** `WP_Query(pagename=…)` oba nalaze pravu stranicu — a zahtev i dalje pada. Dijagnostika koja odmah pokaže krivca: proći kroz `get_option('rewrite_rules')` i ispisati **prvi** obrazac koji `preg_match`-uje traženu putanju.
- 🔴 **Rewrite pravila se regenerišu tek na flush** — zato kvar isplivava sa zakašnjenjem. U ovom slučaju prethodna sesija je brisala taksonomijske termine (što okida flush), pa je pravilo mrtvog CPT-a tek tada ušlo u tabelu i oborilo stranice koje su do tada uredno radile i prošle proveru. **Pravilo: posle svake izmene termina, slugova ili permalink strukture → `wp rewrite flush` pa ponovna provera URL-ova**, inače se kvar pripisuje sledećoj sesiji.
- ⚠️ **`wp rewrite flush` ume da pukne u pola posla** (kod nas timeout na 2 min): pravila ostanu delimično upisana i stranice krenu da rade **301 na početnu** umesto 404. Ako se posle flush-a vidi masovno 301→home, flush nije završio — ponoviti sa većim timeout-om, ne tražiti uzrok drugde.
- Popravka bez brisanja: filter `register_post_type_args` u child temi gasi `public`/`publicly_queryable`/`rewrite`/`has_archive` za mrtve CPT-ove. Reverzibilno (uklanjanje bloka), ne dira podatke plugina.

## Medijateka — provere koje lažu (W7 F2.9, 2026-07-29)
- 🔴 **`_thumbnail_id` koji postoji ≠ slika koja postoji.** Upit „koliko postova nema naslovnu sliku" vraćao je **0**, a dva posta (`6588`, `16608`) su pokazivala na prilog **bez fajla na disku** → kartica u `/aktuelnosti/` prazna. **Nijedna standardna provera ovo ne vidi**: stranica je 200, ima 1×`<h1>`, a slike nema ni u `<img src>` pa je ne hvata ni provera slika. Proveravati `file_exists(get_attached_file($thumb))`, ne prisustvo meta ključa (ugrađeno u `alati/al_verify.php`).
- 🔴 **`wp media import --skip-copy` je pokvaren na Windows-u.** Pojede obrnute kose crte iz putanje (`_wp_attached_file` postane `C:xampphtdocsantaslinewp-contentuploads201912/fajl…`) i upiše ekstenziju **`.webp` umesto `.jpg`**, jer `image_editor_output_format` filter (F7.22) važi i za original. Posle uvoza obavezno: ispraviti `_wp_attached_file` na relativnu putanju sa `/` i pravom ekstenzijom, pa `wp media regenerate`.
- **Consent Mode default vrednost mora se proveriti iz PRAVOG koda, ne iz dokumentacije** (2026-07-22): CLAUDE.md je tvrdio "default DENIED za sve 4 kategorije", ali izvučen live kod pokazuje default **GRANTED** (i to tiho postavljeno kroz `setCon(true,true)` ČIM se banner prikaže, pre bilo kakve korisnikove akcije) — dokumentacija nikad nije bila verifikovana protiv stvarnog koda. Ako CLAUDE.md tvrdi nešto o consent/tracking ponašanju, a nije eksplicitno tagovano "potvrđeno direktno u UI/kodu [datum]" (kao §4.1 primeri), tretirati kao pretpostavku dok se ne provери.

## CSS specifičnost u WoodMart-u
- 🔴 **`:is()` u WoodMart `base.css` je skriveni protivnik (0,1,0)** (2026-07-28, F7.21). `base.css` ima `:is(.btn, .button, button, [type="submit"], [type="button"]) { position: relative }`. `:is()` uzima specifičnost **najjačeg argumenta**, dakle (0,1,0) — izjednačeno sa bilo kojom našom jedno-klasnom selekcijom (`.al-lb-close`, `.al-video-facade__play`), a `base.css` se učitava POSLE naše `antas-design.css` → **pobeđuje na jednakosti**. Posledica u ovom slučaju: dugmad lightboxa ispala iz uglova u normalan tok, a **play dugme video fasada bilo je sivo (#F3F3F3) umesto brend-crvenog mesecima**, neprimećeno.
  **Pravilo:** svako pravilo koje stilizuje `<button>` ili `.btn`/`.button` u child temi piši sa **(0,2,0)** (`.roditelj .dugme`), ne (0,1,0). Ovo je četvrti slučaj istog obrasca (F7.10, F7.19, F7.20, F7.21) — u `entry-content` protivnik je (0,2,0), kod dugmadi (0,1,0).
  **Dijagnostika koja ga nađe za 10 sekundi:** proći kroz `document.styleSheets` i za svako pravilo proveriti `el.matches(r.selectorText)` + da li dira sporno svojstvo — vraća i fajl i selektor krivca.

## Prevodi (gettext) u WoodMart-u / WooCommerce-u (W7 F1.11, 2026-07-28)
- 🔴 **Jedna mapa na `gettext_<domen>` NE hvata sve** — WP ima tri porodice filtera, po jednoj za svaku porodicu funkcija:
  - `__()` / `_e()` / `esc_html__()` → `gettext_<domen>`
  - `_x()` / `esc_attr_x()` → **`gettext_with_context_<domen>`** (3 argumenta: `$translation, $text, $context`)
  - `_n()` / `_nx()` → **`ngettext_<domen>`** (4 argumenta: `$translation, $single, $plural, $number`)
  Placeholder pretrage (`esc_attr_x('Search for products','submit button','woodmart')`) je zato ostao engleski uprkos tačnom unosu u `gettext_woodmart` mapi. Ako string uporno „ne prima" prevod — pogledaj kojom se funkcijom emituje, ne da li je ključ tačan.
- **Ista reč ume da dolazi iz dva domena.** Brojač „2 products" na `/katalog/` je WoodMart `_n('product','products',…,'woodmart')` (bez `%s`), a WooCommerce ima svoj `_n('%s product','%s products',…,'woocommerce')`. Pokriti oba.
- **Srpska množina:** `1 proizvod` / `2–4 proizvoda` / `5+ proizvoda` / `21 proizvod`. Pošto su „few" i „other" ista reč, pravilo je `($n % 10 === 1 && $n % 100 !== 11) ? 'proizvod' : 'proizvoda'`.
- ✅ **Svaka gettext mapa mora imati `is_admin()` izlaz.** Generičke reči (`Blog`, `Home`, `Page`, `Products`) postoje i kao nazivi kontrola u podešavanjima teme — bez izlaza se preimenjuje i administracija.
- **Ne tražiti stringove naslepo po planu** — skenirati renderovane stranice po TIPU (početna, arhiva, pojedinačan post, `/katalog/`, proizvod, pretraga). U ovoj sesiji `Search for posts` nije postojao nigde, a `Products` se pojavio tek na `/katalog/`, koji prvi sken nije obuhvatio.

## WoodMart — prekidači umesto CSS zakrpa (W7 F1.12/F1.13, 2026-07-28)
- **Pre `display:none` i pre kopiranja šablona, potraži `woodmart_get_opt()` gate u samom šablonu.** Ceo blog meta blok (autor + datum + deljenje) zatvara `parts_meta`; „Show 9/12" zatvara `per_page_links`.
- 🔴 **Isti loop prop tema postavlja na tri mesta, i sva tri se moraju pokriti:**
  1. arhiva → opcija (`woodmart_main_loop()`)
  2. ostale petlje (srodni postovi) → `woodmart_setup_loop()` default; hvata se sa `add_action('wp', …, 51)` jer setup visi na 50 i **sam izlazi** ako je `$GLOBALS['woodmart_loop']` već postavljen
  3. `[woodmart_blog]` šortkod → sopstveni atribut. Filter `shortcode_atts_woodmart_blog` **ne postoji** — tema zove `shortcode_atts()` bez trećeg argumenta.
- 🔴 **Šortkod atributi su stringovi: `parts_meta="false"` je ISTINIT.** Tema koristi `1`/`0` (`true_state`/`false_state` u VC mapi). Uvek proveriti mapu u `inc/integrations/visual-composer/maps/`.
- **`copyrights` opcija prolazi kroz `do_shortcode()`** — tekuća godina ide kao sopstveni shortcode, ne kao ukucan broj i ne kao filter nad izlazom.

## WordPress core — markup zamke
- 🔴 **`wp_get_attachment_link()` piše `href` JEDNOSTRUKIM navodnicima** (2026-07-28, F7.21). WP core generiše `<a href='…jpg'>` za `[gallery link="file"]`. Regex pisan samo na `"` tiho promašuje **sve** galerijske linkove. Podmuklo: `<img>` **unutar** tog anchor-a jeste obrađen (jer WP njega piše dvostrukim navodnicima), pa na prvi pogled izgleda da filter radi. **Svaki regex nad renderovanim WP HTML-om piši kao `("|\')…\1`, nikad samo na `"`.**
- **`[gallery]` slike ne postoje kao `<img>` u `post_content`** — audit koji broji `<img>` u bazi ih neće videti (stranica izgleda „bez slika" a ima 42). Uvek proveriti i `[gallery ids="…"]`, `_product_image_gallery` i `_thumbnail_id`.
- **Podrazumevani naslov priloga je ime fajla** ("Final-3x3-Graz") — ne koristiti ga kao natpis bez filtriranja. Odbaci ako je slugifikovan naslov = ime fajla, ili ako nema nijedan razmak a ima crticu.
- 🔴 **`wp_get_attachment_image_src()` NE vraća `false` kad veličina ne postoji** (2026-07-28, F7.22) — vrati URL **originala** sa dimenzijama umanjenim na traženu meru. U `srcset`-u je to laž koja se obija o glavu: `al-xs` je bio upisan kao „400w" a pokazivao na fajl od 1600px/366 KB, pa bi ga browser birao baš kao najjeftiniju opciju. **Za `srcset` uvek `image_get_intermediate_size()`**, koji vrati `false` ako fajla nema.
- 🔴 **WordPress DELI isti fajl između veličina istih dimenzija** (2026-07-28, F7.22) — kod priloga 16621 su i `al-sm` i `woocommerce_single` bili `…-600x400.jpg`. Skript koji „briše staru svoju veličinu" je time razvalio **212 WooCommerce slika**. Pre `unlink()` obavezno proveriti da nijedan drugi zapis u `metadata['sizes']` ne pokazuje na istu putanju.
- 🔴 **Pod WP-CLI-jem `wp_insert_post()`/`wp_update_post()` gubi `<script>`** (2026-07-29, W7 F2.3) — CLI radi bez ulogovanog korisnika, pa `current_user_can('unfiltered_html')` vraća **false** i kses pojede omotač JSON-LD-a. To je mehanizam F7.15 buga sa `/teren-za-pickleball/` (5,3 KB golog JSON-a vidljivo kao tekst, nijedna schema emitovana). **Svaka skripta koja upisuje schemu mora pozvati `kses_remove_filters()`**, i provera mora biti „koliko puta se `FAQPage` javlja VAN `<script>`" — brojanje pojavljivanja u HTML-u ne razlikuje ta dva slučaja.
- 🔴 **`al_convert_webp.php` je ostavio dve vrste repova** (2026-07-29) — pristup je odbačen 2026-07-28, ali je već bio prošao kroz deo medijateke: (a) **6 priloga** sa `_wp_attached_file` na `.jpg` kog nema dok `.webp` blizanac leži pored — *tihi* kvar, javni URL nigde ne puca pa ga provera stranica ne vidi, ali `get_attached_file()` puca (isti obrazac kao 13 apsolutnih putanja od 07-22, pukao bi na dan migracije); (b) **zakucani `.jpg` URL-ovi na izvedene veličine u `post_content`-u** — jedini vidljiv 404. Popravlja `al_scan_lost_originals.php` (traži blizanca po ekstenziji), ali (b) se mora tražiti u sadržaju posebno.
- 🔴 **`default_category` štiti kategoriju od brisanja** (2026-07-29, W7 F2.9) — WP odbija brisanje terma upisanog u opciju `default_category`. Kad se čisti duplikat „Uncategorized", **prvo** prebaciti `default_category` na onaj koji se stvarno koristi, **pa** brisati; obrnut redosled tiho ne uradi ništa. Svaki WP mora imati podrazumevanu kategoriju — ne može se obrisati bez zamene.
- 🔴 **`count` u `wpGs_term_taxonomy` ume da bude ustajao** (2026-07-29) — brojač se ne osvežava kad se postovi masovno prebace u `draft` direktnim SQL-om ili `wp post update`-om. Simptom je kontraintuitivan: brojka **padne** posle dodavanja posta u kategoriju, jer `wp post term set` usput prebroji baš taj termin i time popravi staru laž. Ne panično tražiti regresiju — prvo prebrojati relacije direktno (`JOIN wpGs_term_relationships` + `post_status`). Globalna popravka: `wp term recount category` (bezbedno, samo prepiše brojače).
- 🔴 **`wpautop` razbija mrežu kad su blok-tagovi unutar inline omotača** (2026-07-29, W7 F3) — kartice pisane kao `<a class="al-card"><span class="al-card__body"><h3>…</h3><p>…</p></span></a>` završe tako što je **svaki `<a>` posle prvog umotan u `<p>`**, pa mreža dobije duplo dece i pola polja ostane prazno. Uzrok **nije prelom reda** (markup je bio u jednoj liniji) nego `<h3>`/`<p>` unutar `<span>`. Postojeće stranice rade jer koriste `<div class="al-card"><div class="al-card__body">`. **Pravilo: unutrašnjost `.al-card` mora biti blok (`<div>`) čim sadrži `<h3>`/`<p>`.** Provera: `document.querySelector('.al-grid--2').children.length` mora biti jednak broju kartica.
- 🔴 **Kad se menja deo `post_content`-a, uporediti bilans šortkodova pre i posle** (2026-07-29, W7 F3) — skripta koja je „popravljala mrežu" upisala je `$m[1] . $novi . $m[3]` (sam isečak) kao **ceo** `post_content` i time obrisala hero i dve sekcije; `[vc_row]` je pao sa 4 na 0. Sitewide provera to **ne vidi** (stranica i dalje 200 sa 1×H1). Zamenu raditi kroz `preg_replace_callback` nad **celim** sadržajem i tvrdo odbiti upis ako `substr_count('[vc_row')` / `[vc_column_text]` posle nije identično onom pre.
- **Konverzija originala u WebP je skoro uvek pogrešan potez** (2026-07-28, F7.22): original je već kompresovan JPEG, pa prekodiranje daje −5% (a ume i da poveća fajl), palette PNG **fatalno ruši** GD (`Palette image not supported by webp`), i traži prepisivanje URL-ova kroz `post_content`. Umesto toga `image_editor_output_format` filter (WP ≥5.8): original ostaje netaknut, WebP izlaze samo izvedene `-WxH` veličine — one koje se stvarno učitavaju.

## Obrada slika pri uvozu (F7.23, 2026-07-28)
- 🔴 **`WP_Image_Editor` NE primenjuje EXIF orijentaciju** kad se poziva direktno — WordPress to radi samo unutar `wp_create_image_subsizes()`. Arhiva snimljena telefonom masovno nosi `Orientation: 6`; bez ručnog `maybe_exif_rotate()` fotke legnu **bočno**, a `getimagesize()` i dalje prijavljuje „landscape" pa se po brojkama ništa ne primeti.
- 🔴 **EXIF nije pouzdan u oba smera.** U ovoj arhivi postoje fajlovi sa `Orientation: 6` čiji su pikseli **već uspravni** (verovatno ih je neki alat rotirao a ostavio metapodatak) — tamo bi rotacija pokvarila sliku. Nema automatskog pravila. Rešenje: **alat za pregled mora da prikazuje isto što alat za uvoz proizvodi** (`contact_sheet.php` rotira isto kao `al_import.php`), pa se izbor potvrđuje okom.
- **Slug stranice ume da vara.** `/zastitne-podloge-za-travu-i-plocnike/` ima H1 „Bergo Solid" i govori o zaštitnim pločama za teret, ne o rešetkama za travu. Uvek pročitati H1/H2 pre nego što se biraju fotografije.
- **Umetanje „pre sekcije X" traži POSLEDNJI pogodak, ne prvi** — isti niz (`al-section--navy`) po pravilu nosi i hero sekcija sa H1 i završni CTA; prvi pogodak ubaci sadržaj iznad H1.
- **Prekodiranje bez potrebe je gubitak.** Ako je izvor već u ciljnom formatu i ispod granice veličine, fajl treba kopirati, ne ponovo enkodovati.

## Responsivne slike (`srcset`/`sizes`)
- 🔴 **`sizes` je ono što stvarno bira fajl, ne `srcset`** (2026-07-28, F7.22). Browser računa potrebnu širinu iz `sizes` × DPR i uzme prvi kandidat koji je pokriva — ako `sizes` laže, sve ostalo je uzalud. Zatečeno na 16657: slika se crta na **381 px**, `sizes` tvrdio **760 px** → skidao se 900w umesto 400w, **1.038 KB za 9 slika**. Posle ispravke (+ `al-xs` 400 + WebP): **233 KB, −78%**.
- **`sizes` mora da prati stvarni raspored**, dakle i broj kolona grida, ne samo „kartica vs. sadržaj". Ćelija = `(širina sadržaja − razmak×(N−1))/N`; za 1192 px i 3 kolone to je 381 px.
- **Provera je trivijalna, radi je uvek:** u konzoli uporedi `img.getBoundingClientRect().width` sa onim što `sizes` tvrdi, i pogledaj `img.currentSrc`. Ako je izabrani fajl bitno širi od prikaza — `sizes` laže.
- **Ne juriti „što manju sliku" ispod stvarne potrebe:** na 381 px prikaza fajl od 300w se **uvećava** i vidi se. Na retina telefonu (DPR 2–3) browser legitimno uzima 900w — to nije rasipanje nego smisao `srcset`-a.
- NE uvoziti GA4 `tel` event kao Ads konverziju — duplo brojanje sa "Klik na telefon (web)".

## WordPress / WPBakery
- Deaktiviran plugin ne izvršava PHP — ako banner iskače posle deaktivacije, izvor je drugde. Dijagnostika: `curl` test + grep po tekstu bannera, ne po imenu plugina.
- WPBakery unos: proveriti verziju `js_composer`, backup baze pre unosa, regenerisati `_wpb_shortcodes_custom_css` i `_wpb_post_custom_css` posle izmene.
- Shortcode integritet: `grep -o '\[vc_row' | wc -l` mora = `grep -o '\[/vc_row\]' | wc -l`.
- Slike sa non-ASCII karakterom u imenu fajla (npr. en-dash `–` u `Supersoft-Smooth-–-PU.webp`) vraćaju 403 ako se literalni karakter stavi direktno u `<img src>` — mora se URL-encode-ovati (`%E2%80%93`) u samom src atributu (2026-07-08, ergonomske-podloge-2 sesija).
- Bezbedan update: export `post_content` u `/tmp/`, splice novih blokova pre CTA, reimport `wp post update` — ne inline regex.
- Porto quirk: za `post_type=post` entry-title je `<h2 class="entry-title">`, ne `<h1>`. Ne tretirati kao nedostajući H1.
- Post lookup: `wp post list --name=slug` ume da vrati prazno za pages → fallback `wp eval 'echo url_to_postid("full-url");'`.
- **`margin-top` na `.vc_row` ne radi na ovom sajtu**: `full_width="stretch_row"` ubacuje prazan `<div class="vc_row-full-width vc_clearfix">` (float:left, height:0) između svaka dva reda — to poništava negativni `margin-top` na sledećem redu (computed stil je ispravan, render pozicija se ne pomera, potvrđeno testom `margin-top:-300px !important` inline → 0 efekta). Rešenje: `position: relative; top: ...` radi ispravno. Detalji: [[migracija/woodmart-sabloni]] gotcha #11.
- **Dijakritici mogu biti dekomponovan Unicode (NFD) umesto precomponovanog (NFC)** (nađeno 2026-07-22, post 4318 odbojka): "ć" je u bazi čuvano kao `c` + combining acute accent (U+0301, 2 bajta `cc81`) umesto standardnog jednog karaktera `ć` (U+0107, `c487`). Ručno otkucan `str_replace`/`strpos` anchor sa precomponovanim karakterom TIHO promašuje (nema greške, samo `false`) jer su bajtovi različiti iako izgledaju identično na ekranu. Fix/prevencija: kad anchor tekst sadrži č/ć/š/đ/ž, izvući ga programski direktno iz stvarnog `post_content` (`mb_substr`) umesto ručnog kucanja — izbegava celu klasu ove greške bez potrebe za normalizacijom.
- **`[vc_raw_html]BASE64[/vc_raw_html]` koristi `rawurlencode`, ne `urlencode`** (nađeno 2026-07-22, `/spoljnje-podne-obloge/` FAQ JSON-LD dopuna): sadržaj je `base64(rawurlencode(html))` — razmak postaje `%20`. Ako se pri re-enkodiranju upotrebi `urlencode()` (razmak→`+`), dekoder na render strani (koji koristi `rawurldecode`) NE vraća `+` nazad u razmak — rezultat je vidljiv literalni "+" u renderovanom JSON-LD tekstu. Fix/provera: dekodiraj postojeći blok sa `urldecode(base64_decode())` (bezbedno i za rawurlencode-ovan sadržaj jer nema literalnih `+` karaktera u izvoru), izmeni, pa re-enkoduj isključivo sa `base64_encode(rawurlencode())`, i uvek round-trip proveri (`rawurldecode(base64_decode($novi))` sadrži očekivan tekst) PRE upisa u bazu.
- **Yoast `wpGs_yoast_indexable` keš se NE osvežava automatski** ni posle hard flush-a — canonical, `og:url` i JSON-LD ostaju na starim URL-ovima dok se stare redovi ručno ne obrišu: `DELETE FROM wpGs_yoast_indexable WHERE object_sub_type IN ('product_cat','product', ...)` (+ pojedinačni `object_id` za page/post slug izmene). Posle brisanja Yoast regeneriše ispravno na sledećoj poseti. Ovo proširuje raniju lekciju (2026-07-06, termmeta izmene) — pravilo važi za SVAKU izmenu koja menja permalink/slug bilo kog objekta (post, page, product, term).

## WordPress Importer (WXR) — CLI izvršavanje (parity F3, 2026-07-07)
Redosled include-ova koji radi za programski WXR import van admin UI-ja:
```php
define('WP_LOAD_IMPORTERS', true);   // MORA pre wp-load.php
require 'wp-load.php';               // ovo automatski učitava wordpress-importer.php JER je već aktivan plugin — ne require-ovati ga ponovo (Cannot redeclare fatal)
require_once ABSPATH.'wp-admin/includes/post.php';     // post_exists()
require_once ABSPATH.'wp-admin/includes/comment.php';  // comment_exists()
require_once ABSPATH.'wp-admin/includes/media.php';    // attachment fetch
require_once ABSPATH.'wp-admin/includes/image.php';
require_once ABSPATH.'wp-admin/includes/file.php';
require_once ABSPATH.'wp-admin/includes/taxonomy.php';
```
- `WP_LOAD_IMPORTERS` definisan PRE `wp-load.php` znači da WP već učita `wordpress-importer.php` tokom normalnog plugin bootstrap-a (pošto je aktivan) — eksplicitan drugi `require` istog fajla posle izaziva "Cannot redeclare".
- Bez `wp-admin/includes/post.php` i `comment.php`: `WP_Import->process_posts()` puca na `post_exists()`/`comment_exists()` — funkcije koje CLI kontekst ne učitava automatski (samo admin UI).
- Fatal greške se ne vide u terminalu ako je `WP_DEBUG_DISPLAY=false` (WP-ov "kritična greška" wp_die ekran guta stack trace) — proveri `wp-content/debug.log` (`WP_DEBUG_LOG=true`) za pravi uzrok, ne samo stdout/stderr skripte.
- `WP_Import` je idempotentan (`post_exists()` po title+content+date) — bezbedno ponovo pokrenuti ceo import posle otklanjanja blokatora; već uvezene stavke se preskaču, samo nedostajuće se dodaju.
- `post_exists()` matchuje po NASLOVU, ne po slugu — ako lokalni sadržaj ima isti naslov kao live stavka ali drugačiji slug (bilo namerno zadržan LOKAL-NOVO post, bilo stari zaboravljen draft), import će tu stavku TIHO preskočiti kao duplikat. Ne pretpostavljaj da "nedostaje u bazi" = "nedostaje slug" — proveri naslove pre nego što tražiš zašto fali N od M stavki.
- `fetch_attachments=true` ne remapuje uvek URL-ove postojećih slika u `post_content` na lokalni domen kad je attachment prepoznat kao "already exists" (title match) — ako su fajlovi već rsync-ovani lokalno, ostaje `https://[live-domen]/wp-content/uploads/...` u tekstu iako je fajl fizički prisutan. Fix: `str_replace` live domena na lokalni kroz `wp_update_post` (isti obrazac kao F2 link fix).
- Kad je odluka "zadrži post kao publish" tokom cleanup-a pred reimport: eksplicitno izuzeti taj ID iz SVAKE sledeće bulk-delete petlje (npr. `if ($p->ID === $keepId) continue;`) — "nisam ga menjao" ne znači da ga bulk-delete WHERE upit neće pokupiti.

## WooCommerce varijacije + katalog režim (2026-07-10)
- **Varijacija BEZ cene je nevidljiva po WC default-u** — `data-product_variations="[]"` i prazan select boja, bez ikakve greške. U katalog režimu (cene namerno nema) obavezni filteri: `woocommerce_variation_is_visible` + `woocommerce_variation_is_active` → true, `woocommerce_hide_invisible_variations` → false (child functions.php).
- **mysql CLI na Windows konzoli mangle-uje UTF-8 u `-e` stringu** — č/š/ž stižu u bazu kao `?` iako je `--default-character-set=utf8mb4` prosleđen (konzolni encoding lomi PRE mysql-a). Svaki upis sa dijakriticima ide kroz PHP fajl (UTF-8), nikad inline mysql -e.
- **Widget sa sopstvenim `<link>` stylesheet-om u telu stranice gazi child CSS** — WoodMart `[social_buttons]` pre-renderovan u custom_html widget nosi svoj `el-social-icons.css` koji se učitava u FUTERU (posle head child CSS-a) → override zahteva `!important`.
- **WebFetch sažetak može da vrati zastarele PDF URL-ove** (halucinacija/keš izvora) — svaki preuzet "PDF" proveriti sa `file -b` (4/5 Ecotile linkova bilo HTML 404 stranica); prave linkove tražiti na downloads/support stranici proizvođača.
- **Otvoren NATIVE select dropdown (OS-level) zamrzava CDP screenshot** — Chrome automation timeout-uje dok se dropdown ne zatvori (Escape). Select vrednosti postavljati kroz JS `dispatchEvent(new Event('change', {bubbles:true}))`, ne klikom na native dropdown.

## WooCommerce atributi (polish Faza 1, 2026-07-09)
- **SQL dump import prenosi `term_relationships` sa live object_id-jevima** — posle importa u bazu sa drugačijim ID prostorom, dodele pokazuju na pogrešne objekte (kod nas: 251 pa_ dodela na attachment-ima i orphan ID-jevima). `tt.count` kolona pri tom izgleda "puna" — uvek verifikuj `JOIN wpGs_posts ON ID=object_id` + `post_type` pre nego što zaključiš da su atributi/tagovi stvarno dodeljeni.
- **Atribut se NE prikazuje na proizvodu bez `_product_attributes` postmeta** — sama term dodela (`wp_set_object_terms` na `pa_*` taksonomiju) nije dovoljna; serialized niz `['pa_x' => ['name','value'=>'','position','is_visible'=>1,'is_variation'=>0,'is_taxonomy'=>1]]` je ono što puni "Dodatne informacije" tab. Zato je audit "0/37 atributa" bio tačan iako su taksonomije imale termine.
- **FAQPage JSON-LD na proizvodima**: proizvodi nisu WPBakery — nema vc_raw_html puta. Radi jednolinijski `<div><script type="application/ld+json">…</script></div>` u post_content preko `$wpdb->update` (wpautop ne dira jer nema newline-ova, div je block element). Product schema NE dodavati u content — globalni `functions.php` hook (W2 2.7) je već generiše za sve proizvode.

## PHP/Windows putanje pri obradi slika (2022-image-audit, 2026-07-22)
- **`get_attached_file()` na ovom XAMPP-u vraća MEŠOVITE separatore** (`C:\xampp\htdocs\antasline/wp-content/uploads/...` — backslash pa forward slash). Ako se novo ime fajla rekonstruiše preko `pathinfo()['dirname'] . DIRECTORY_SEPARATOR . ...` i onda poredi sa originalnom putanjom preko `!==` (string poređenje) da bi se odlučilo "da li treba obrisati stari fajl", Windows i dalje razrešava OBA zapisa na ISTI fizički fajl, ali string poređenje kaže "različito" → skripta obriše fajl koji je TEK napisala (samo-sabotaža, tiha, bez PHP greške). Pravilo: pre bilo kakvog "da li se putanja promenila" poređenja u batch obradi slika, normalizuj oba stringa (`str_replace('\\','/', ...)` + lowercase) pre `===`/`!==`, nikad ne poredi sirove putanje. Uhvaćeno na test-slici pre batch primene (jedina zaštita koja je ovo sprečila da pogodi 21 proizvod odjednom) — uvek testiraj na 1 fajlu/proizvodu pre batch operacije koja piše/briše fajlove.
- Bash komande >~965 bajtova bacaju "Command too long for parsing" → koristiti Write/Edit alat ili `bash skripta.sh`.

## XAMPP / lokalno okruženje (CWV baseline, 2026-07-09)
- **XAMPP po default-u NEMA uključen OPcache** — WP render je zbog toga bio ~8–10s TTFB po stranici (prvi zahtevi posle Apache restarta vise i >60s). Fix u `C:\xampp\php\php.ini`: odkomentarisati `zend_extension=opcache` + `opcache.enable=1` (+ `opcache.jit=disable`). Efekat: TTFB ~2,4–3,4s. Svako lokalno merenje performansi bez opcache-a meri XAMPP artefakt, ne sajt.
- **OPcache + XAMPP Apache = crash bez fixa**: worker threadovi imaju premali stack → PHP puca sa `0xC00000FD` (stack overflow) + `VirtualProtect() failed [87]` u error.log, a curl dobija connection reset (000) bez HTTP odgovora. Fix: `conf/extra/httpd-mpm.conf` → dodati `ThreadStackSize 8388608` u `<IfModule mpm_winnt_module>` blok, pa restart Apache-a.
- XAMPP Apache NIJE Windows servis (`httpd -k restart` javlja "No installed service") — restart = `Stop-Process -Name httpd` pa start `httpd.exe` detached (ili XAMPP Control Panel).
- Posle Apache restarta prva poseta traje 12s+ (hladan opcache) — pre bilo kakvog merenja zagrejati sve ciljne stranice curl-om.
- Lighthouse 13 nema klasične image audite (`modern-image-formats` itd. premešteni u insights) — nalaze o slikama vaditi iz `network-requests` liste u JSON-u.
- Dijagnostika "gde WP zahtev visi": privremeni mu-plugin koji na `-99999` prioritetu markira microtime po hook-ovima (muplugins_loaded → shutdown) + `pre_http_request`/`http_api_debug` za odlazne HTTP pozive → log fajl pokaže tačnu fazu. Obrisati posle upotrebe.
- **ergomat.com scraping recept** (2026-07-10): WebFetch dobija 403, `curl` sa browser User-Agent prolazi. Kategorije: `GET /en/Category/List?id=X` MORA imati `X-Requested-With: XMLHttpRequest` header (inače vraća layout bez proizvoda). Proizvod: JSON API `GET /en/Product/GetDetails?id=X&langId=3` (product id iz `product-id-prop` atributa na stranici, langId iz `settings-prop`) → polja `Photo` (slika na `/Content/images/products/{Photo}.jpg`), `KnowledgeSpec` (PDF putanja), `AvailableOptions` (dimenzije). PDF-ovi čitljivi kroz `pdftotext`.
- **US retail specifikacije ≠ zvanični datasheet** (2026-07-10): za DuraStripe Xtreme više US shop izvora tvrdilo 30 mil, a zvanični Ergomat PDF kaže 19 mil (0,48 mm) — retail agregatori znaju da pomešaju modele. Kad se izvori ne slažu, jedino proizvođačev datasheet prelama; do tada se vrednost izostavlja.
- **MariaDB "Aria recovery failed" = mysqld se ne podiže posle neurednog gašenja XAMPP-a** (2026-07-10): log kaže `Cannot find checkpoint record` + `Could not open mysql.plugin table` i proces odmah izlazi. Fix: **preimenovati** (ne obrisati — reverzibilno) `aria_log.########` i `aria_log_control` u `C:\xampp\mysql\data\`, pa restart — Aria redo logovi se regenerišu, InnoDB podaci (sve wpGs_ tabele) netaknuti. Simptom se prepoznaje po `ERROR 2002 Can't connect (10061)` na prvi mysql/mysqldump poziv u sesiji.

## Porto-functionality deaktivacija (2026-07-09)
- **No-op shortcode shim u child temi mora da pokrije SVE porto_* tagove iz baze** — shim registruje tag samo ako ne postoji (`!shortcode_exists`), pa dok je porto plugin bio aktivan, pravi shortcode je imao prednost; posle deaktivacije svaki tag VAN shim liste curi kao go tekst (potvrđeno: `[porto_product]`) + nosi PCRE segfault rizik (backtick-JSON parametri). Popis tagova: `SELECT post_content ... LIKE '%[porto_%'` pa regex preko svih publish redova.
- **Legacy CPT-ove registruje CPT UI, ne porto** (industrija-podovi, podovi-posl-prostor, spoljne-podne-obloge, vestacka-trava, sportski-podovi2) — prežive deaktivaciju. `portfolio` i `porto_builder` su Portovi — gube javni URL ali sadržaj ostaje u bazi kao izvor.
- **`[porto_image_gallery images="..."]` → native `[gallery ids="..." columns="4" size="medium" link="file"]`** je čista 1:1 zamena (isti attachment ID-evi); native default `size` je thumbnail 150×150 (premalo) — uvek eksplicitno `size="medium"`.
- Blok "CTA pri dnu" (porto_builder 4945, referenciran na 6 starih stranica) je imao `conditional_render=administrator` bug — treći put da ovaj obrazac maskira sadržaj (v. #27/#28 orphan nalaze): pre panike "izgubili smo sekciju" proveriti da li je posetilac ikad i video sekciju.

## Kallyas tema (live sajt) — 2×H1 gotcha (2026-07-11)
- **Kallyas (live antasline.com) automatski renderuje `post_title` kao `<h1 class="page-title kl-blog-page-title">`** na svakoj stranici/postu — nezavisno od toga da li `post_content` ima svoj `<h1>`. Isti obrazac kao WoodMart `_woodmart_title_off` gotcha, ali BEZ ekvivalentne postmeta opcije nađene do sad na Kallyas-u. Fix: svaki `<h1>` unutar `post_content` na live sajtu mora biti `<h2>` (ne `<h1>`) — teorijski H1 dolazi isključivo iz teme. Provera: `curl live-url | grep -o '<h1[^>]*>.*</h1>'` mora vratiti tačno 1 red i to sa klasom `page-title`/`kl-blog-page-title`.
- Nalazi se na `/politika-kolacica/` (post 7295): originalni sadržaj imao 7×h1 U SADRŽAJU + 1 od teme = 8 ukupno; ispravljeno na 1 (tema) posle demotea sadržajnih h1→h2.

## Bash inline `php -r` sa sadržajem koji ima ugnježdene navodnike (2026-07-11)
- **Veliki `php -r "..."` pozvan direktno kroz Bash tool sa string sadržajem koji meša jednostruke/dvostruke navodnike (HTML `alt="..."`, WPBakery markup) je krhak** — shell-escaping kroz slojeve (Bash tool → sh → php -r) je pojeo/iskvario deo koda i rezultat je bio da je `post_content` stranice 16676 upisan kao doslovno `"1"` (ceo sadržaj izgubljen) umesto namenjenog HTML-a. Uzrok nije definitivno utvrđen (verovatno kolizija ugnježdenih `\"` sekvenci), ali posledica je bila tiha (skripta je "uspešno" odradila `$wpdb->update` sa pogrešnim sadržajem, bez PHP greške).
- **Fix i pravilo ubuduće**: SVAKI upis koji menja `post_content` postojeće stranice preko `str_replace`/`$wpdb->update` MORA ići kroz `.php` fajl (Write alat), nikad kroz inline `php -r "..."` kad string sadržaj ima HTML atribute sa navodnicima — ovo je već pravilo iz CLAUDE.md §9.4 za >965B komande, ali se pokazalo da važi i za KRAĆE komande čim se pojave ugnježdeni navodnici, ne samo zbog dužine.
- **Oporavak**: pošto je backup baze napravljen neposredno pre izmene (standardni protokol), sadržaj je vraćen tako što je backup `.sql` uvezen u privremenu bazu (`CREATE DATABASE antasline_restore_tmp` → `mysql < backup.sql`), pravi `post_content` pročitan odatle i upisan nazad u živu tabelu, pa je temp baza obrisana. Ovo je razlog zašto se backup pravi PRE svake sesije koja menja bazu, čak i za naizgled bezopasne string-replace izmene.

## Task Scheduler / backup (2026-07-09)
- **"Registrovan u Task Scheduler + ručni test prošao" ≠ "backup radi"** — noćni backup nikad nije izvršen kao scheduled run: default `New-ScheduledTaskSettingsSet` nosi `DisallowStartIfOnBatteries=True` (laptop na bateriji u 03:00 → task odbijen, `LastTaskResult=0x800710E0`) i `StartWhenAvailable=False` (propušten termin se ćutke preskače). Za backup taskove uvek: `-AllowStartIfOnBatteries -DontStopIfGoingOnBatteries -StartWhenAvailable`, pa PROVERITI `Get-ScheduledTaskInfo LastTaskResult` posle prve noći (0 = uspeh), ne samo ručni test skripte.
- Za mesečno poređenje uvek sumirati `conversions_generate_lead + conversions_tel + conversions_mailto` po danu, ne uzimati agregatni `conversions` field direktno — čak i kad je trenutno čist, jedan loš dan u GA4 adminu ga tiho pokvari bez upozorenja.

## robots.txt na lokalnom XAMPP buildu — WordPress ne generiše virtuelni fajl u poddirektorijumu (2026-07-21)
- **`http://localhost/antasline/robots.txt` vraća 404 kroz WP (ne Apache) jer je WordPress instaliran u poddirektorijumu** — `wp-includes/class-wp-rewrite.php` eksplicitno prazni `$robots_rewrite` osim ako `home_path` nije prazan ili `/` (v. komentar "robots.txt -- only if installed at the root"). `flush_rewrite_rules()` ovo ne rešava, jer nije bug nego namerno ograničenje. Simptom: 404 ali sa WP headerima (`X-Powered-By: PHP`, `Link: wp-json`), ne Apache-ov go 404.
- **Fix koji radi i lokalno i na live-u**: fizički `robots.txt` fajl direktno u document root-u (isti obrazac kao `llms.txt`, v. W2 2.8) — Apache ga servira direktno, zaobilazi WP rewrite sloj potpuno, i ponaša se identično kad sajt bude na pravom domenu (bez poddirektorijuma). Sadržaj referencira produkcioni domen (`https://www.antasline.com/sitemap_index.xml`) već sada, aktivira se na migraciji.

## Fizički .txt fajl u docroot-u bez eksplicitnog charset-a → mojibake za srpsku latinicu (2026-07-23)
- **Statički `.txt` fajl (npr. `llms.txt`) koji Apache/LiteSpeed servira direktno (bez WP-a) šalje `Content-Type: text/plain` BEZ `charset` po default-u na ovom hostingu** — fajl na disku je ispravan UTF-8, ali bez eksplicitnog charset header-a klijenti (browseri, neki AI crawleri) nagađaju enkodiranje i često padnu na Latin-1/Windows-1252, pa se č/š/đ/ž/ć prikazuju kao mojibake iako je sadržaj tehnički ispravan. Za poređenje: `/robots.txt` koji generiše WP kroz `wpseo`/core ima `charset=utf-8` eksplicitno (WP to sam doda), zato taj fajl nikad nije imao ovaj problem.
- **Fix**: `<Files "ime.txt"><IfModule mod_headers.c>Header set Content-Type "text/plain; charset=utf-8"</IfModule></Files>` blok u `.htaccess`, ubačen VAN svih plugin-upravljanih markera (LSCACHE/WordPress/rlrsssl itd. blokova) da ga auto-regeneracija tih plugina ne prepiše — najsigurnije na sam kraj fajla.
- **Provera pre svake sledeće sesije koja koristi CPT kao izvor**: `SELECT post_type, post_status, COUNT(*) FROM wpGs_posts WHERE post_type IN (...) GROUP BY 1,2` — ako ima `publish` redova bez inbound linkova (`LEFT JOIN` na `post_content LIKE '%/{type}/{slug}/%'`), prebaciti na draft + `TRUNCATE wpGs_yoast_indexable` da se sitemap keš osveži odmah.

## Font subsetting + serialized widget opcije (W3 3.6, 2026-07-21)
- **Sužavanje `unicode-range`/font fajla na "šta stvarno treba" mora biti potvrđeno skenom stvarnog sadržaja, ne pretpostavkom jezika.** Google Fonts latin-ext raspon (U+0100–02FF) pokriva desetak jezika; sken celog published `post_content`+`post_title`+Yoast meta preko PHP `mb_ord` po karakteru je jedini pouzdan način da se zna da srpska ekavica realno koristi samo ćčđšž — bez tog skena, sužavanje bi moglo tiho slomiti retku reč sa nepokrivenim karakterom (npr. strano ime u nekom budućem tekstu) u vidljiv tofu-box.
- **`fonttools`/`pyftsubset` (`pip install fonttools brotli`, pa `python -m fontTools.subset`) radi direktno na `.woff2` bez potrebe za izvornim TTF-om** — output isto `.woff2` preko `--flavor=woff2`. Windows put mora ići sa forward-slash-evima kroz Git Bash (`\f` u dvostrukom navodniku se tumači kao escape i briše backslash), ne sa `\\`.
- Isto ograničenje verovatno važi i za `WebSearch` tool (dokumentovano "only available in the US") — ne koristiti ni njega za RS-specifičnu SERP proveru.

## `mysql -N -e "SELECT..."` batch-mode escape-uje newline bajtove (Figma testimonials, 2026-07-22)
- **Kad se `mysql` klijent koristi non-interaktivno (redirect u fajl, `-N -e`), MySQL automatski escape-uje specijalne karaktere u output-u** — pravi newline bajt (`0x0A`) u `post_content` polju izlazi kao literalna 2-karakter sekvenca `\n` (backslash + n), tab kao `\t`, itd. Ovo je dokumentovano MySQL batch-mode ponašanje, ne bug.
- **Posledica**: dump preko `mysql -N -e "SELECT post_content..." > file.txt` IZGLEDA kao da sadržaj nema pravih newline-ova (npr. `wc -l` javlja 1 red) — ali stvarna DB vrednost IMA prave newline bajtove. Ako se taj dump doslovno prekopira u PHP **single-quoted** string (`'...\n...'`, gde `\n` ostaje 2 literalna karaktera) kao anchor za `str_replace()`, poređenje tiho promaši jer se literalni `\n` upoređuje protiv pravog newline bajta u DB-u.
- **Provera pre pisanja replace anchor-a**: direktan PHP dump stvarnog sadržaja (`bin2hex($content)` ili `var_export()`) preko `wp-load.php` bootstrap-a — `0a` u hex-u = pravi newline, `5c6e` = literalni `\n` tekst. Ne verovati mysql CLI batch-output transkripciji za bilo šta osim brzog vizuelnog pregleda.
- **Postupak ažuriranja**: `re.search` na `\[vc_raw_html\]([A-Za-z0-9+/=]+)\[/vc_raw_html\]` da se izvuku blobovi (obično 2: VideoObject pa FAQPage, po redosledu pojavljivanja) → `base64.b64decode` → `urllib.parse.unquote` → `json.loads` na JSON deo (posle skidanja `<script type="application/ld+json">...</script>` omotača) → izmeni `mainEntity[i].acceptedAnswer.text` po `name` (pitanju) → `json.dumps(ensure_ascii=False)` → ponovo `<script>` omotač → `urllib.parse.quote` → `base64.b64encode` → zameni stari blob u sadržaju. Uvek proveriti da broj izmenjenih Q&A odgovara očekivanom pre upisa u bazu.

## `_wp_attached_file` apsolutna putanja — tih bag koji `wp_get_attachment_url()` maskira (W3 3.10 sitewide audit, 2026-07-22)
- **`_wp_attached_file` postmeta MORA biti relativna putanja (npr. `2026/07/fajl.pdf`), nikad apsolutna** — 13 priloga (PDF-ovi dodati preko batch skripte u S6/S7 eri) je imalo upisanu punu Windows putanju (`C:/xampp/htdocs/antasline/wp-content/uploads/2026/07/fajl.pdf`). `wp_get_attachment_url()`/`guid` i dalje rade ispravno (idu drugim kodnim putem), pa se ovo NE vidi na javnom sajtu — ali `get_attached_file()` (koristi ga regenerate-thumbnails, migracioni alati, admin media editor) vraća pokvarenu DUPLU putanju (`.../uploads/C:/xampp/.../uploads/...`) jer funkcija konkatenira `basedir + '/' + meta_value` bez provere da li je `meta_value` već apsolutna. Na dan migracije (drugi OS/putanja) bi ovo definitivno puklo za svaki alat koji čita fizički fajl po attachment ID-ju.
- **Provera**: `file_exists($base . '/' . $meta_value)` sam po sebi daje lažne "MISSING" rezultate za apsolutne putanje (duplira prefiks) — prvo proveriti `preg_match('#^([A-Za-z]:/|/)#', $val)` da se razdvoje pravi nedostajući fajlovi od pogrešno-formatiranih putanja koje ustvari postoje.
- **Fix**: `wp_normalize_path()` + strip `basedir` prefiksa → upiši nazad kao relativnu. Verifikovati kroz `get_attached_file()` (ne samo `wp_get_attachment_url()`) posle fix-a, jer baš ta funkcija je bila slomljena.

## curl na Windows/Git-Bash enkoduje non-ASCII URL karaktere pogrešnim kodnim rasporedom (W3 3.10 link crawl, 2026-07-22)
- **`curl` u Git Bash na Windows enkoduje karaktere van ASCII-ja (npr. en-dash "–", U+2013) koristeći sistemski codepage (cp1252 → `%96`), NE UTF-8 (`%E2%80%93`)** — kad se takav URL testira (npr. iz `href` atributa izvučenog sa stranice), Apache ne nalazi fajl čije je ime stvarno UTF-8-enkodovano na disku i vraća 403 (ne 404, zbog kako Windows/Apache tumači nevalidan bajt). Pravi browser ovo NE radi (šalje ispravno UTF-8 enkodovanje jer je stranica `charset=UTF-8`) — rezultat je lažna uzbuna, ne pravi sajt bag.
- **Provera pre nego što se "403 na sliku" prijavi kao bag**: ručno percent-enkodovati problematični karakter u UTF-8 (`%E2%80%93` za en-dash) i ponovo testirati — ako sad vrati 200, radi se o test-alat artefaktu, ne o sajt bagu.

## Zion Builder (live Kallyas stranice) — sadržaj nije u `post_content`, ne pretpostavljati builder tip (spoljnje-podne-obloge live fix, 2026-07-22)
- Na LIVE sajtu (Kallyas tema), `page`/`post` tipovi mogu koristiti **Zion Builder**, čiji stvarni sadržaj živi u serialized PHP nizu `zn_page_builder_els` postmeta — **ne** u `post_content` niti u `panels_data` (SiteOrigin legacy polje). `wp post get <ID> --field=post_content` i grep na njemu mogu vratiti potpuno prazan/nepovezan rezultat dok je pravi tekst i dalje na sajtu — ovo je isti obrazac kao raniji nalaz na lokalnom postu 6588 (SiteOrigin serialized postmeta), sad potvrđen i za Zion Builder na live-u.
- **Kako prepoznati**: body class na renderovanoj stranici sadrži `wp-theme-kallyas`/`theme-kallyas` + element klase oblika `eluid<hex>`/`znColumnElement` (Zion Builder potpis). `post_content`/`panels_data` bez traga očekivanog teksta je znak da treba proveriti `zn_page_builder_els`.
- **Kako naći tačan čvor**: `get_post_meta($id, 'zn_page_builder_els', true)` vraća već-unserializovan PHP niz (WP radi unserialize automatski) — rekurzivno pretražiti niz po ključnoj reči (`stripos` na string vrednostima), ne pokušavati ručno parsirati serialized string.
- **Kako izmeniti bez rizika**: NIKAD ručni `str_replace` na sirovom serialized stringu (menja dužinu stringa bez ažuriranja `s:N:` prefiksa → corrupt unserialize). Umesto toga: `get_post_meta()` → izmeni PHP niz po referenci → `update_post_meta($id, 'zn_page_builder_els', $izmenjeni_niz)` — WP sam serijalizuje ispravno.
- **Pravilo**: kad treba pravi tekst iz PDF URL-a, prvo `WebFetch` (da se fajl preuzme i keš-putanja dobije), zatim `Read` na vraćenu lokalnu putanju za stvarni sadržaj — ne osloniti se na WebFetch-ov tekstualni rezime za PDF-ove.

## Korumpirane Aria **sistemske** tabele obaraju ceo mysqld iako su podaci netaknuti — simptom je „backup ne radi" (2026-08-17)

Noćni backup builda nije radio 3 dana; skripta je prijavljivala samo „MySQL se nije pokrenuo ni
posle 30 s". Pravi uzrok: `mysql.db` i `mysql.tables_priv` (Aria, `.MAD`/`.MAI`) korumpirane posle
ubijanja XAMPP-a gašenjem mašine → `Can't open and lock privilege tables` → `Aborting`. **InnoDB
podaci su bili potpuno čitavi** (crash recovery prošao, `CHECK TABLE` 78/78 bez zamerki) — pao je
samo sloj privilegija.

**Postupak (offline, server ugašen):**
```
# 1. hladna kopija data dir-a PRE svega (popravka time postaje povratna)
# 2. iz C:\xampp\mysql\data  — NE iz data\mysql\, tamo ne nalazi aria_log_control
aria_chk -r -f mysql\*.MAI
# 3. za ono što padne na aria_sort_buffer_size:
aria_chk -o -f mysql\db.MAI mysql\columns_priv.MAI ...
```
🔴 **Dve zamke:** (a) `aria_chk` se **mora** pokretati iz `data\`, inače ne vidi
`aria_log_control` i ne može da očisti transakcione ID-eve; (b) `--sort_buffer_size` se
**ignoriše** — ostaje 16384 i `-r` puca na „aria_sort_buffer_size is too small"; rešenje je `-o`
(safe-recover), ne veći broj.
🟢 Gubitak redova u `mysql.db` je na XAMPP-u bezopasan — tamo su samo podrazumevani grantovi za
`test` bazu; root privilegije žive u `global_priv`.
🔵 Nije jednokratno: `db.MAD-260707173248.BAK` i `db.MAD-260721115741.BAK` su ostaci automatskih


## `wp-load.php` bootstrap se zaglavio na CLI pozivu (visi bez greške), Yoast ima sopstvenu keš tabelu nezavisnu od postmeta (podovi-za-terase fix, 2026-07-27)
- **`require_once wp-load.php` pozvan iz gole PHP CLI skripte se zaglavio** (proces živ, `Responding: True`, CPU raste sporo ali sadržajno ne napreduje ni posle nekoliko minuta) — nijedna od mojih izmena (ni prvi `update_post_meta()` poziv) nije stigla do baze pre nego što je proces ubijen, znači zaglavilo se u samom bootstrap-u, pre mog koda. Verovatan uzrok: neki mu-plugin/plugin radi neuslovljen mrežni poziv na `init`/`plugins_loaded` (license check, update ping i sl.) koji nema internet u ovom okruženju pa čeka na timeout. **Rešenje koje je odmah proradilo**: zaobići WordPress bootstrap potpuno — čist `mysqli`/PDO na `antasline_local` bazu, bez `wp-load.php`, isti princip kao ranije utvrđeno pravilo "koristi `$wpdb->update()` direktno" samo doveden do kraja (ni `$wpdb` ne treba, samo sirov mysqli).
- **Yoast SEO (14+) drži sopstvenu keš tabelu `wp{prefix}_yoast_indexable`** (+ `_hierarchy`, `_seo_links` itd.) sa već-izračunatim `title`/`description`/`open_graph_*`/`twitter_*` poljima po `object_id`. Frontend čita IZ OVE TABELE, ne uvek direktno iz `_yoast_wpseo_title`/`_yoast_wpseo_metadesc` postmeta. **Direktna SQL izmena postmeta bez prolaska kroz WP `save_post`/Yoast hook-ove ostavlja ovu keš tabelu zastarelu** — promena je u bazi, ali se ne vidi na sajtu dok se keš ne osveži. Simptom: `curl` na stranicu i dalje pokazuje STARI `<title>` iako `SELECT` na `_yoast_wpseo_title` postmeta pokazuje nov tekst.
- **Fix**: `DELETE FROM wp{prefix}_yoast_indexable WHERE object_id IN (...)` za pogođene postove — Yoast sam regeneriše red iz trenutnih postmeta vrednosti na sledećem frontend pozivu (potvrđeno: `curl` odmah posle DELETE-a pokazao ispravan novi title/meta description). Ne treba ručno popunjavati `og_title`/`twitter_title` polja — ako su NULL (uobičajeno), Yoast ih izvodi iz `title`/`description` u trenutku renderovanja.
- **Provera pre zaključka "izmena nije uspela"**: uvek proveriti direktno SQL vrednost postmeta PRE nego što se posumnja da UPDATE nije prošao — u ovom slučaju baza je bila tačna, problem je bio isključivo u prezentacionom kešu.
- **Backend-only polja (`woocommerce_store_postcode`, `woocommerce_pos_store_address`) namerno ostavljena netaknuta** — potvrđeno grep-om kroz temu/mu-plugins da se nigde ne koriste za frontend render ni schema izlaz, van obima ovog fix-a (čisto WooCommerce admin podešavanje).

## `uapi Quota get_quota_info` zaostaje za stvarnim brisanjem — `du -sh` je pouzdan odmah, zvaničan brojač se osvežava sa kašnjenjem (2026-08-12/13)
- Posle `rm -rf ~/staging` (3,4 GB) na live cPanel-u, `du -sh ~` je odmah pokazao tačno novo stanje (10,1 GB → 6,2 GB), ali `uapi Quota get_quota_info` je i dalje, i posle ponovnog pollovanja par sekundi kasnije, vraćao identičan stari broj (2.487,65 MB slobodno) — kvota-keš na cPanel-u se ne osvežava real-time i nema WHM/root pristup da se to forsira.
- Broj se sam ispravio do sledeće `[cpanel-live]` sesije narednog dana (2026-08-13: 5.867,07 MB slobodno, tačno u skladu sa `du`) — nepoznat tačan interval osvežavanja, ali je prošao unutar ~24h.
- **Pravilo: posle bilo kog brisanja na live serveru, veruj `du -sh` za trenutnu potvrdu; `uapi Quota` koristi samo za zvaničan/citatan broj u izveštaju, i to tek u sledećoj sesiji, ne odmah posle brisanja.**

## LiteSpeed "Instant Click" config flag ne kaže da li koristi `prefetch` ili rizičan `prerender` — mora se čitati izvorni kod skripte (2026-08-13)
- `litespeed.conf.util-instant_click=1` samo kaže da je funkcija uključena, ne KOJI mehanizam koristi. `instant_click.min.js` (LiteSpeed Cache 7.8.1) podržava native Speculation Rules API i grana na rizičan `type="prerender"` (izvršava JS odredišne stranice pre stvarnog klika — GTM `generate_lead` bi tad okidao na hover) samo ako `document.body.dataset.instantSpecrules === "prerender"`. Plugin admin UI **ne izlaže** tu opciju — jedini način da se potvrdi koja grana se stvarno koristi je čitanje minifikovanog JS izvora i provera da li taj `data-*` atribut postoji igde na `<body>` (temi, config-u).
- **Isti obrazac kao [[reference/naucene-lekcije]] „Schema može mesecima da postoji" nalaz** — config/postmeta/„uključeno" status nije dokaz šta se stvarno izvršava. Pravilo: kad podešavanje dodiruje konverzije/tracking, provera mora ići do izvornog koda ili stvarnog mrežnog ponašanja (Network tab / curl na renderovan HTML), ne samo do vrednosti opcije u bazi.
- Kontekst: [[reference/chrome-web-platform-2026]] §3 je unapred upozorio da bilo koji LiteSpeed prefetch/prerender treba proveriti pre 25.08 zbog rizika naduvavanja `generate_lead` — provera je potvrdila da je trenutna konfiguracija bezbedna (`prefetch`, ne `prerender`), ali samo zato što je neko stvarno otvorio JS fajl. v. [[dnevnik/2026-08-13-litespeed-prefetch-instant-click]].

## Bash čita skriptu inkrementalno po bajt-ofsetu — editovanje `.sh` fajla dok se izvršava raspolovi komande, i to sa exit 0 (dry-run `build-staging-package.sh`, 2026-08-13)
- Skripta je pokrenuta u pozadini; dok je pravila 2,7 GB tar, editovan je isti fajl (dodavanje komentara i izmena whitelist-e). Bash ne učitava skriptu u memoriju odjednom nego čita dalje **od zapamćenog bajt-ofseta** — svaka izmena iznad te tačke pomeri ostatak fajla, pa je interpreter nastavio da čita iz sredine reda: `antasline-uploads-…` je pročitano kao `ploads: command not found`.
- 🔴 **Proces je izašao sa kodom 0** uprkos `set -euo pipefail` (greška je nastala u okruženju koje `errexit` ne hvata, a `tail` na kraju cevi je vratio svoj status) — u automatizovanom lancu bi to prošlo kao **uspeh**. Izlazni kod nije dokaz da je skripta odradila posao; proveravaj artefakte koje je trebalo da napravi.
- **Pravilo: nikad ne editovati `.sh` koji se trenutno izvršava.** Sačekaj kraj ili radi na kopiji pa zameni. Ako je izmena hitna — prekini proces, izmeni, pusti ispočetka.

## Skripta bez pregazivih putanja se u praksi nikad ne testira (dry-run `build-staging-package.sh`, 2026-08-13)
- `build-staging-package.sh` je imao hardkodiran `OUT_DIR` (produkciona izlazna fascikla), pa se dry-run nije mogao pustiti nigde drugde. Posledica: dva exclude pravila dodata 10.08 stajala su **nikad izvršena** tri dana, a dva pre-flight rizika (🔴🔴) oslanjala su se baš na njih. Identično je bilo sa `live-export.sh` pre popravke 12.08 (`PFX`/`OUT`), koji je pri prvom stvarnom pokretanju gubio 145/170 galerijskih slika.
- **Pravilo: svaka migraciona/destruktivna skripta mora imati `VAR="${VAR:-podrazumevano}"` za izvor i odredište.** Podrazumevane vrednosti ostaju iste (poziv se ne menja), ali skripta postaje pokretljiva u scratchpad — bez toga „napisana i dodata pravila" nije isto što i „radi".
- **Pravilo: `.htaccess` se na serveru EDITUJE, nikad ne prenosi iz builda** (301 blok se dodaje iznad `# BEGIN WordPress`, kako checklist B3 i kaže). Isto važi za `wp-config.php` — oba su per-okruženje, ne per-sadržaj.

## Arhiva slika se ne kompresuje — „paket ≈ pola veličine foldera" je pogrešna pretpostavka za kvotu (dry-run, 2026-08-13)
- `wp-content/uploads` je 2,9 GB na disku; `tar.gz` je **2,71 GB** (ušteda ~6%). Slike su već kompresovane (JPEG/WebP), gzip nema šta da stegne. Pre-flight računica je pretpostavljala ~1,3 GB paket i na osnovu toga je disk-bloker proglašen zatvorenim.
- Drugi deo zamke: obrazac „chunkuj pa sklopi na serveru" **udvostručuje** pik (delovi + sklopljen tar postoje istovremeno) — 2,78 GB postaje 5,56 GB, od 5,87 GB slobodnih, pre backup-a i pre raspakivanja.
- **Pravilo: veličinu paketa izmeriti stvarnim prolazom pre nego što se kvota proglasi dovoljnom**, i planirati pik (delovi + sklopljeno + backup + raspakovano), ne zbir arhiva. Ako postoji SSH, `rsync` izbegava ceo problem — chunkovanje je bilo zaobilaznica za nestabilnu FTP data-konekciju, ne zahtev hostinga.

## Kredencijal u pomoćnoj skripti se ne nađe traženjem — nađe se čitanjem koda iz drugog razloga (FTP lozinka, 2026-08-13)
- FTP lozinka je stajala u čistom tekstu u `ftp-upload-chunks.sh` i `ftp-upload-resume.sh` od 06.08, verzionisana u git-u i sinhronizovana na hosting. Otkrivena je slučajno — skripta je otvarana da bi se videlo **kako se delovi sklapaju na serveru** tokom dry-run-a, ne u nekakvoj bezbednosnoj proveri.
- Bila je u **dva** fajla; prvi nalaz je prijavio samo jedan. **Pravilo: kad nađeš kredencijal u jednom fajlu, odmah `grep` po celom stablu za samu vrednost** (ne za ime promenljive) — pomoćne skripte se kopiraju jedna iz druge, pa se i tajna kopira.
- Obrazac izmeštanja koji je usvojen: vrednost u fajl van repo stabla (`~/antasline-ftp-creds.txt`), skripta ga `source`-uje preko `VAR_FILE="${VAR_FILE:-$HOME/…}"` i **tvrdo pada sa `exit 1` pre ijednog mrežnog poziva** ako fajl nedostaje ili ne definiše očekivanu promenljivu. Tiho nastavljanje sa praznim kredencijalom je gore od pada.
- Isto važi za smer 301: `/podloge-za-parkiraliste-cena/` vraća **404 na live-u**, pa joj 301 pravilo uopšte ne treba — dovoljno je draftovati. Pre svakog „dodaj 301", proveriti `curl` na **live** URL: pravilo treba samo onom URL-u koji zaista postoji napolju.

## `open(putanja, 'w')` prazni fajl pre nego što skripta stigne da pukne — vault fajl od 375 KB nestao na `UnicodeEncodeError` (2026-08-13)
- Python `open(p,'w')` **truncate-uje odmah**, pre prvog `write()`. Jednolinijski obrazac `open(p,'w',encoding='utf-8').write(novo)` je zato mina: ako se izraz `novo` sastavlja u istoj liniji i baci izuzetak (ovde: surogatni parovi iz `🔴` escape-ova u emoji-ju), fajl ostaje na **0 bajtova**, a original je nepovratno otišao.
- Spaseno `git checkout -- PROGRESS.md` (Obsidian Git commit star ~40 min) — ali samo zato što je fajl bio verzionisan. Isti obrazac nad `antasline-backups/*.sql` (od 13.08 u `.gitignore`) bi bio trajan gubitak.
- **Obrazac**: pre svake odluke „301 na nešto slično" — povuci upite obe stranice i uporedi klastere, ne naslove.

## Unutar PHP **single-quoted** stringa navodnik se piše go, bez beksleša (2026-07-28)
- Pri programskoj prepravci generisanog PHP-a ubačen je `<p style=\"…\">` unutar single-quoted PHP stringa. PHP u single-quoted stringu razrešava SAMO `\\` i `\'` — `\"` ostaje doslovno, pa bi u HTML izašao literalni beksleš i pokvario atribut.
- Rođak je postojećeg gotcha-e #12 iz [[migracija/woodmart-sabloni]] (`\xNN` hex escape isto ne radi u single-quoted stringu). Ista klasa greške ponovila se istog dana i u Python heredoc-u pri upisu ove lekcije (`\x` u tripl-quoted stringu = `SyntaxError`) — **kad se piše kod koji generiše kod, sadržaj ide u fajl pa se čita, ne u ugnježden string literal.**
- Uhvaćeno pre nego što je bilo vidljivo, jer verifikaciona skripta eksplicitno traži `style=\"` artefakt u HTML izlazu — vredi zadržati tu proveru u standardu verifikacije novih stranica.

## WPBakery `_wpb_shortcodes_custom_css` se ponekad tiho ne regeneriše posle `wp_update_post()` iz CLI-ja — HTTP/H1 provera to ne hvata (W7 F4.1, 2026-07-29)
- Kod 3 od 32 programski izmenjene stranice (`5438`, `16684`, `16685`) je `el_class`/`css=".vc_custom_heroF4{ID}{background-image:...}"` atribut ispravno sačuvan u `post_content`, red se renderovao sa tačnom klasom (`vc_custom_heroF4{ID}` je bio u `class=` atributu HTML-a), ali `<style data-type="vc_shortcodes-custom-css">` blok koji bi taj CSS stvarno emitovao **nikad se nije pojavio** — stranica je ostala vizuelno navy uprkos ispravnom markup-u.
- Uzrok nije regex/format: ručan poziv `wpbakery()->parseShortcodesCss($post->post_content, 'custom')` nad istim (aktuelnim) sadržajem radi ispravno i vraća tačan CSS string. Problem je u `Vc_Base::buildShortcodesCss()` — funkciji koju `save_post` hook zove da izračuna i upiše `_wpb_shortcodes_custom_css` postmeta; kod ova 3 posta je taj poziv (u trenutku `wp_update_post()`) očigledno video prazan/star sadržaj, izračunao prazan CSS i legitimno **obrisao** meta ključ (`delete_metadata` grana kad je `empty($css)`) — potvrđeno proverom `wpGs_postmeta`: ključ `_wpb_shortcodes_custom_css_updated=1` postoji, ali `_wpb_shortcodes_custom_css` sam nedostaje. Zašto baš ova 3 od 32 (ne svih 32, ne random) nije utvrđeno.
- **Fix**: `wpbakery()->buildShortcodesCss($id, 'custom')` pozvano ručno POSLE izmene, nad već sačuvanim sadržajem — pouzdano piše ispravnu vrednost.
- **Pravilo: kad prompt referencira konkretan vault fajl koji izgleda da ne postoji, prvo `git pull`/`git fetch` pre nego što se to prijavi kao STANI-blokada.** Ovo posebno važi za pakete/prompt-ove pravljene "sada" na drugoj površini (lokal) — sinhronizacija ima kašnjenje od par minuta do ~10 min, ne odmah.

## Server nema `htpasswd` CLI — `openssl passwd -apr1` daje kompatibilan Apache hash (staging V4, 2026-08-13)
- `htpasswd -bc` je vratio `command not found` na cPanel serveru. Zamena bez dodatnog paketa: `openssl passwd -apr1 '<lozinka>'` generiše APR1-MD5 hash koji Apache/LiteSpeed prihvata identično, upisan ručno u `.htpasswd` kao `korisnik:hash`.
- Vezano: kad se paket namerno NE prepisuje serverski `.htaccess` (ispravna odluka da se sačuva postojeći Basic Auth), ta odluka tiho pretpostavlja da serverski fajl **postoji**. Ako je ceo docroot ranije obrisan (kao ovde — staging obrisan par dana pre ove sesije), pretpostavka pada bez greške: raspakivanje samo ostavlja prazninu, staging ostaje kratko potpuno otvoren dok se to eksplicitno ne proveri (`head -20 .htaccess` posle svakog raspakivanja koda kad prompt to traži — ne preskakati ovaj korak ni kad "izgleda kao formalnost").
- Vezano: rotacija/sortiranje takvog fajla mora ići **po datumu parsiranom iz naslova**, nikad po poziciji linije — inače skript zalutale unose arhivira u pogrešan mesec.

## Bash heredoc u Claude Code alatu skuplja dvostruke obrnute kose crte — za skripte sa escape-ovima koristi Write (token audit, 2026-08-18)
- `python - <<'EOF'` sa `'EOF'` u navodnicima treba da prenese sadržaj doslovno, ali je `\n` u izvoru stizalo do Pythona kao pravi prelom reda, a `.replace('\','/')` kao neterminisan string. Jednostruki `\d` u regexu prolazi; dvostruki ne.
- **Fix:** skripte sa escape sekvencama pisati `Write` alatom u fajl pa pokrenuti `python fajl.py`. Isto važi i za srpske navodnike: `„tekst"` unutar Python stringa u dvostrukim navodnicima prekida string na ASCII `"` — koristiti `'...'` ili `„...“`.

## Mešani CRLF/LF fajlovi: normalizacija celog fajla ga naduva (token audit, 2026-08-18)
- Skript za čišćenje razmaka je u prvom prolazu **povećao** `migracija/arhiva/2026-08-11-legacy-cpt-sadrzaj.md` za 2.354 B, jer taj fajl ima **1.144 CRLF i 2.354 LF** reda, a logika „ako fajl sadrži CRLF, vrati sve na CRLF“ je konvertovala i LF redove.
- **Pravilo: čuvaj završetak reda po liniji** (`cr = seg.endswith('


## Deljen `::before` između dve klase na istom elementu — svojstva se kaskadiraju po svojstvu, ne po pravilu (2026-08-21)
- M je prijavio "zatamnjenje na mobilnom je samo na nekim stranicama" na foto-hero sekcijama. Uzrok nije bio mobile-specifičan i nije trebalo `resize_window` da bi se video: `.al-hero-photo::before` (navy overlay) i `.al-plates::before` (dekorativne kose ploče, brend motiv) su na **istom `vc_row` elementu** na 53 od 54 hero stranica (sve sem početne, koja jedina nema `.al-plates`).
- Element ima samo jedan `::before`. Kad ga dve klase ciljaju odvojenim pravilima, browser ne bira "pobedničko pravilo" u celini — sklapa konačni pseudo-element **svojstvo po svojstvo**, po istoj cascade logici kao i za obično pravilo/pravilo. `.al-hero-photo::before` (kasnije u fajlu) je pobeđivao za `background`/`inset`/`width`/`height`, ali nikad nije eksplicitno postavljao `transform` ni `opacity` — pa su ta dva svojstva procurela iz `.al-plates::before` (`transform: skewX(...)`, `opacity: 0.55`), praveći upola bleđe i koso zatamnjenje (šuplji trouglovi u uglovima) na svakoj stranici koja nosi obe klase.
- Potvrđeno `getComputedStyle(el, '::before')` u browseru — brz i pouzdan način da se proveri KOJA svojstva stvarno stižu do pseudo-elementa, bez nagađanja iz izvornog CSS-a (koje pravilo "izgleda" da pobeđuje nije isto što i šta se stvarno renderuje kad dve klase dele isti pseudo-selektor).
- **Pravilo:** kad se dve dekorativne/overlay klase kombinuju na istom elementu i obe koriste `::before` (ili `::after`), svako pravilo mora **eksplicitno postaviti SVA vizuelno bitna svojstva** (`transform`, `opacity`, `filter`, `mix-blend-mode`...), ne samo ona koja ga razlikuju od default vrednosti — inače susedno pravilo tiho popuni prazninu. Alternativa: razdvojiti na `::before`/`::after` parove tako da se nikad ne preklapaju na istom elementu.
- Fix (bez `!important`, ista specifičnost/kasnije u fajlu pobeđuje): [[dnevnik/2026-08-21-tri-baga-implementacija]]
