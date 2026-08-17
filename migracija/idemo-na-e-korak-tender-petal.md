# Stavka E — `/sportske-podloge/` (5438): vraćanje basket-semantike + FAQ + schema

## Context

Stranica `/sportske-podloge/` (page ID **5438**) je pri WoodMart redizajnu izgubila sadržaj koji nosi organske klikove. Live verzija drži **1.422 prikaza / 178 klikova / 90d** (potvrđeno svežim GSC pull-om 13.08 preko `gsc_page_queries.py`, period 15.05→12.08 — ne iz vault CSV-ova, koji su tri puta u jednoj sesiji bili pogrešni).

**Basket klaster nosi 138 od 178 klikova (78%)**, ne „skoro polovinu" kako plan `[[seo/2026-08-13-kanibalizacija-konsolidacija-plan]]` §3.8 procenjuje — potcenjeno:

| Upit | Prikazi | Klikovi | Poz. |
|---|---|---|---|
| podloga za košarkaški teren | 113 | **47** | 1,6 |
| podloga za kosarkaski teren cena | 147 | **39** | 2,0 |
| podloga za basket | 31 | 14 | 1,3 |
| podloge za kosarkaski teren | 37 | 13 | 1,5 |
| podloga za kosarku | 48 | 12 | 2,4 |
| podloga za kosarkaski teren | 48 | 12 | 2,5 |

Build 5438 **nema** H2 „Izgradnja sportskih terena za basket u vašem dvorištu!", nema „Vrste podloga za sportski teren?", i **ne pominje `/planer-terena/`**. Sopstveni head term „sportske podloge" stoji na **poz. 17,3** (96 prikaza / 2 klika).

**Nov nalaz, nije u planu §3.8:** stranica ima FAQ sa 4 pitanja ali **nema FAQPage JSON-LD** — samo `Article` i `VideoObject`. Hub `/industrijski-podovi/` ga je dobio 13.08 (stavka K), ovde je propušten.

Rok: **content freeze NED 2026-08-16**. Go-live 25.08.

### Odluke donete pre plana
1. **Obim:** semantika + FAQ + schema (2 nove sekcije, planer link, izmena FAQ cena-para, FAQPage).
2. **Tekst:** doslovno sa live-a, uz ispravku dve čiste štamparske greške (`dicipline`→`discipline`, dupli razmak). „Antas line" **namerno ostaje** kako stoji na live-u.
3. **Kolizija cena-H3:** live ima samo jedan cena-naslov. Sekcija B zadržava doslovan live H3; FAQ H3 postaje **„Koliko košta podloga za košarkaški teren?"** — bukvalno GSC upit sa 39 klikova, čime cena-pitanje ulazi u FAQPage schemu.

---

## Polazno stanje (provereno u bazi i renderu, ne pretpostavka)

`post_content` = **10.328 B**, 6 `[vc_row]` sekcija, WPBakery, **0 `[vc_raw_html]`**:

| # | `el_class` | Sadržaj |
|---|---|---|
| 0 | `navy al-hero-photo al-plates al-diag-bottom` | hero, H1, `css=".vc_custom_heroF45438"` |
| 1 | `paper` | Bergo intro + 6 USP `al-card`; već linkuje ka 2298 |
| 2 | `mist al-diag-top` | 11 disciplinskih kartica |
| 3 | `paper` | Bergo Ultimate spec + `[al_skica]` + video facade |
| 4 | `mist` | FAQ: 4× `<h3>` + `<p>` |
| 5 | `navy al-plates al-diag-top--rev` | CTA |

Render: HTTP 200 · **1×H1** · 6×H2 · 14×H3 · 2 JSON-LD bloka · **0× FAQPage** · 0 pomena planera.

**Cilj:** 8 sekcija, ~15.300 B, 8×H2, 11×H3, 3 JSON-LD bloka uklj. 1× FAQPage sa 4 Question.

---

## Izvor teksta

Doslovan live tekst je izvučen iz `zn_page_builder_els` (live post **1849**, Zion Builder) u `migracija/live-export-2026-07-05/live-pages-2026-07-05.xml`. Live markup se **ne kopira** — samo tekst.

---

## Fajlovi koji se prave

| Fajl | Uloga |
|---|---|
| `migracija/alati/job-5438-semantika-faq-schema-2026-08-13.php` | jedina skripta koja piše |
| `migracija/alati/verify-5438-2026-08-13.php` | samo čita, F7.14 set |
| `antasline-backups/5438-post_content_2026-08-13_pre.txt` | rollback bez restore-a baze |

Jedna skripta, ne više: sva četiri koraka diraju **isti** `post_content`, a schema zavisi od rezultata izmene FAQ-a. Razdvajanje bi značilo tri `$wpdb->update` ciklusa nad istim poljem.

Kanonski obrazac za kostur: **`migracija/alati/job-faq-konsolidacija-2026-08-13.php`** (doc-blok sa dosijeom odluke, `$WRITE` gate, ispis PRE, idempotentnost, dry-run pa `exit(0)`, ispis POSLE, lista ručnih koraka). Obrazac za brisanje+ponovnu izgradnju schema bloka: `job-faq-17025-konsolidacija-2026-08-13.php`.

### Obavezna pravila (projektna, ne opciona)
- Upis **isključivo** `$wpdb->update()` + `clean_post_cache()`. `wp_update_post()` skida `[al_skica]` i kvari `css=""`; `mysql` CLI kvari dijakritike (ć = HEX `C487`).
- Čitanje `$wpdb->get_var()`, ne `get_post_field()` (wptexturize).
- **Anchor-based** umetanje (`strpos`/`substr`), nikad prekucani „old" literali. **Sva sidra ASCII** — `š/ć/—` nikad ne prolaze kroz izvorni kod.
- Nov HTML kao `<<<'HTML'` nowdoc, svaka sekcija u **jednoj liniji** (prelom → `<br>`).
- Nove sekcije su `<h2>`, nikad `<h1>`.
- Bez `al-diag-*` (susedne sekcije nemaju rez → dupli dijagonalni rez).

---

## Markup

### Sekcija A — `mist`, umeće se pre sekcije [4]

```
[vc_row full_width="stretch_row" el_class="al-section al-section--mist"][vc_column][vc_column_text]<span class="al-label">Izbor podloge</span><h2 class="al-display--lg">Vrste podloga za sportski teren?</h2><p>Pri odabiru sportskih podloga veoma je bitan osećaj koji se pruža igračima, odskok i trenje, ali i drugi parametri kao što su održavanje podloge, kvalitet i dugotrajnost. Sportski pod Ultimate je jedinstvena sportska podloga na kojoj se mogu igrati različite sportske discipline. Napravljena je prema standardima svetskih sportskih asocijacija.</p><p>Pruža odličan osećaj tokom igre i pozitivno utiče na kolena i zglobove. Ultimate podovi nude visok nivo performansi uzimajući u obzir sve ove faktore. Montaža podova sama po sebi ne može biti jednostavnija što predstavlja još jednu veliku prednost. Jedinstvena izrada omogućava dobru izdržljivost, odskok i trenje.</p>[/vc_column_text][/vc_column][/vc_row]
```

### Sekcija B — `paper`, odmah posle A

```
[vc_row full_width="stretch_row" el_class="al-section al-section--paper"][vc_column][vc_column_text]<span class="al-label">Izgradnja terena</span><h2 class="al-display--lg">Izgradnja sportskih terena za basket u vašem dvorištu!</h2><p>Antas line vam nudi kompletnu uslugu projektovanja i izgradnje sportskih terena. Na osnovu vaših želja i potreba pravimo projekat za izradu sportskog terena, vršimo montažu sportske podloge i prateće opreme. U ponudi imamo pored sportskih podova i opremu za košarkaške, odbojkaške, rukometne, teniske i fudbalske terene.</p><h3>Podovi za sportske terene cena - koliko košta da se napravi teren za basket?</h3><p>Cena izgradnje sportskog terena zavisi od više faktora. Mi u našoj ponudi imamo više modela sportskih podloga. Cena pored izbora modela sportske podloge zavisi i od lokacije gde se montira kao i od kvadrature terena. Modeli sportskih podova:</p><ul><li>pod za teniske terene</li><li>podloga za basket, odbojku, rukomet</li><li>multisport sportska podloga</li><li>sportska podloga za manje zahtevne terene</li><li>veštačka trava</li><li>sportski podovi za sale</li><li>akrilne podloge za tenis</li></ul><p>U ponudi imamo 10 standardnih boja koje se mogu kombinovati. Kombinacijom boja dobijate jedinstven izgled vašeg terena. Boja podloge ne utiče na cenu. Za cenu sportskog poda i više detalja možete nas pozvati ili poslati mail.</p><p>Ne morate čekati ponudu da biste videli obim posla — u planeru birate sport, dimenzije i boje i odmah dobijate broj ploča, m² i PDF specifikaciju.</p><div style="margin-top:20px"><a class="al-btn" href="http://localhost/antasline/planer-terena/">Otvorite planer terena</a></div>[/vc_column_text][/vc_column][/vc_row]
```

Napomene:
- Live „Modela sportskih podova: X / Y / Z" razlomljeno u `<ul>` — **reči nepromenjene**, samo „Modela"→„Modeli" (padež posle uklanjanja kosih crta). Svaka stavka nosi ključnu reč. `<ul>` u `[vc_column_text]` je već dokazan u sekciji [3].
- CTA je kopija **dokazanog obrasca sa 17019** (`<div style="margin-top:20px"><a class="al-btn">`), ne `.al-cta-box` — taj CSS je vezan za `.entry-content`/`.wd-entry-content` i nije verifikovan na full-width WPBakery stranici.
- Predzadnji `<p>` je jedina rečenica koja nije sa live-a (most ka CTA-u). Ako se traži nulti autorski tekst — izbaciti ga, ostaviti samo dugme.

### Zamena FAQ para #3

```
<h3>Koliko košta podloga za košarkaški teren?</h3><p>Cena zavisi od izabranog modela sportske podloge, kvadrature terena i lokacije montaže. Boja podloge ne utiče na cenu — u ponudi je 10 standardnih boja koje se mogu kombinovati. Pošaljite dimenzije terena ili složite teren u <a href="http://localhost/antasline/planer-terena/">planeru terena</a> i vraćamo ponudu sa specifikacijom.</p>
```

---

## Mehanika skripte

**Sidro za umetanje** (provereno **jedinstveno**, čist ASCII):
```php
$SIDRO = 'el_class="al-section al-section--mist"][vc_column][vc_column_text]'
       . '<span class="al-label">Pitanja</span>';
if (substr_count($c, $SIDRO) !== 1) { /* 🔴 prekid */ }
$pos = strpos($c, '[vc_row full_width="stretch_row" ' . $SIDRO);
$c = substr($c, 0, $pos) . $SEKCIJA_A . $SEKCIJA_B . substr($c, $pos);
```
Kraće sidro (`al-section--mist"]`) bi posle koraka 1 bilo dvosmisleno — sekcija A i sama nosi `mist`. Uz `<span class="al-label">Pitanja</span>` sidro ostaje jedinstveno zauvek (nove labele su „Izbor podloge" i „Izgradnja terena").

**Zamena FAQ para** — hvata se raspon između dva ASCII sidra, bez prekucavanja:
```php
$p = strpos($c, '<h3>Sportski podovi');   $k = strpos($c, '</p>', $p);
$stari = substr($c, $p, $k + 4 - $p);
if (strlen($stari) > 400 || stripos($stari, 'cena') === false) { /* 🔴 prekid */ }
```

**FAQPage** — stari blok se prvo briše u celosti, pa gradi iznova (idempotentno i pri budućim izmenama FAQ-a):
```php
$c = preg_replace('~\s*<script type="application/ld\+json">.*?</script>\s*~s', "\n", $c, 1);
$pn = strpos($c, 'Postavljanje podloge za sportski teren');
$pk = strpos($c, '[/vc_column_text]', $pn);
preg_match_all('~<h3[^>]*>(.*?)</h3>\s*<p>(.*?)</p>~s', substr($c, $pn, $pk - $pn), $m, PREG_SET_ORDER);
if (count($m) !== 4) { /* 🔴 schema se NE upisuje */ }
```
Parse **isključivo nad `substr` FAQ bloka** — sekcija [1] ima 6 USP kartica sa `<h3>`+`<p>`, a nova sekcija B takođe ima `<h3>`+`<p>`. Strogo `!== 4`: višak je jednako sumnjiv kao manjak (znači da je opseg iscurio).

**Keševi** (samo pod `--write`): `DELETE FROM wpgs_yoast_indexable WHERE object_id=5438` + `\RankMath\Sitemap\Cache::invalidate_storage()`.

**`_wpb_shortcodes_custom_css` — proveriti, NE regenerisati.** Nove sekcije ne unose nijedan `css=""` atribut, pa meta nema šta da dobije; slepa regeneracija bi mogla obrisati `.vc_custom_heroF45438` (hero pozadina). Skripta samo asertuje da je pravilo prisutno.

---

## Koraci sa checkpoint-ima

| K | Radnja | Checkpoint |
|---|---|---|
| **K1** | `wp db export` → `antasline-backups/antasline_local_2026-08-13_pre-5438-semantika.sql` | fajl postoji, > 30 MB |
| **K2** | `verify-5438` **pre** izmene | 200 · 1×h1 · **0× FAQPage** · 2 JSON-LD · slike 200 — sačuvati ispis kao referencu |
| **K3** | probni prolaz (bez `--write`) | `sidro jedinstveno: 1` · stari cena-par prepoznatljiv · `parsirano 4 parova` sa novim 3. pitanjem · `POSLE ~15.300 B · 8 [vc_row] · 8 h2 · 11 h3` · **0 grešaka** |
| **K4** | `--write` | `✅ 5438 upisan` · `✅ hero CSS pravilo prisutno` · `yoast_indexable obrisan` · `5438-…-pre.txt` = **10.328 B** |
| **K5** | **istu komandu `--write` ponovo** | tri `⚠️ već upisano` · `strlen` **identičan** K4. Različit → skripta nije idempotentna → rollback |
| **K6** | `verify-5438` posle | 200 · **1×h1** · 3 JSON-LD, svi `json_decode` OK · **1× FAQPage sa 4 Question** · 0 golog JSON-a u tekstu · planer link 200 · video facade + `[al_skica]` netaknuti |
| **K7** | Chrome, 1440 i 390 px | nema horizontalnog scrolla na 390 · 11 kartica u [2] nerazbijene · video facade radi · `al-btn` crven (nije nasledio link-stil) · 0 console grešaka |
| **K8** | `al_verify.php 5438,17004,2298,17019,16676,17027` | HTTP≠200 = 0 · ≠1×h1 = 0 · PHP greške = 0 |
| **K9** | `al_verify.php slike` (pun sweep) | bez novih nalaza |
| **K10** | dnevnik + PROGRESS red + `build-staging-package.sh` | paket nastaje **posle** izmene, inače freeze hvata staru verziju |

**Stajemo i proveravamo posle K3, K4, K6 i K7** — korisnik je tražio korak po korak.

Komande:
```
C:\xampp\php\php.exe -d max_execution_time=0 -d memory_limit=512M "C:\Projekti\antasline-vault\migracija\alati\job-5438-semantika-faq-schema-2026-08-13.php"
C:\xampp\php\php.exe -d max_execution_time=0 -d memory_limit=512M "C:\Projekti\antasline-vault\migracija\alati\job-5438-semantika-faq-schema-2026-08-13.php" --write
php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file "C:\Projekti\antasline-vault\migracija\alati\verify-5438-2026-08-13.php"
```

---

## Rollback

**Nivo 1 (preferirani, sekundni)** — vrati samo `post_content` iz `.txt` snimka, kroz PHP (dijakritici netaknuti):
```php
$staro = file_get_contents('…/antasline-backups/5438-post_content_2026-08-13_pre.txt');
if (strlen($staro) !== 10328) die("🔴 backup nije 10328 B — prekid\n");
$wpdb->update($wpdb->posts, ['post_content' => $staro], ['ID' => 5438]);
clean_post_cache(5438);
$wpdb->query("DELETE FROM {$wpdb->prefix}yoast_indexable WHERE object_id=5438");
```

**Nivo 2** — pun restore: `wp db import …_pre-5438-semantika.sql` (**nikad** `mysql.exe <` — razbija `ć`).

Posle bilo kog rollback-a ponoviti K6.

---

## Rizici specifični za 5438

| Rizik | Kontrola |
|---|---|
| **11 `al-card` bez `__body`** u [2] — `.al-card__title` je `position:absolute` sa `:has()` prekidačem; kartice se lako raspadnu | umetanje je izvan te sekcije (sidro = početak `[vc_row` sekcije [4]); K7 vizuelno potvrđuje |
| **Video facade** (`data-yt-id="VdZWT2O5_-M"`) i **`[al_skica]`** u [3] | nijedan regex ne prelazi granicu sekcije [3]; verify traži oba u renderu i da `[al_skica` nije ostao neizvršen |
| **`css=""` hero** (`vc_custom_heroF45438`) | meta se proverava, ne regeneriše |
| **Regex hvata tuđe `<h3>`** (USP kartice, sekcija B) | parse samo nad `substr` FAQ bloka + strogo `count !== 4` |
| **Unicode u sidrima** | sva sidra ASCII |
| **`wpautop` u `<script>`** | WP `_autop_newline_preservation_helper` čuva prelome; K6 ipak `json_decode`-uje svaki blok |
| **Stranica nosi 178 klikova/90d** | K5 (idempotentnost) + K8 (regresija) neizostavni; `.txt` snimak daje rollback bez baze |

---

## Šta plan svesno NE radi

- **Ne dira `rank_math_title` / meta description.** Head term „sportske podloge" na poz. 17,3 bi profitirao, ali je van odobrenog obima — predlog za zasebnu stavku.
- **Ne dodaje slike** (nema kandidata iz odobrenog teksta).
- **Ne dira 301 mape** (nema promene sluga ni statusa).
- **Ne linkuje `<ul>` stavke** ka disciplinskim stranicama — sekcija [2] to već radi sa 11 kartica; dupliranje bi razvodnilo signal.
- **Ne dira stavku F** (dimenzije klaster vs 2298) — sledeća na redu, ~1 h.

## Nalaz za kasnije, van obima

GSC pokazuje **tartan klaster** na ovoj stranici bez ijedne namenske sekcije: „tartan podloga" 45 prikaza / poz. 15,6 · „tartan cena za m2" 39 / poz. 10,4 · „tartan kocke" 19 / poz. 8,6 · „tartan cena" 14 / poz. 9,1 — ukupno **117 prikaza, 6 klikova, sve na poz. 9–16**. Kandidat za zasebnu stavku posle live-a.
