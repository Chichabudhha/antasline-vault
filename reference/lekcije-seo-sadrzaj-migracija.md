---
name: lekcije-seo-sadrzaj-migracija
description: Tehnicki gotchas — SEO/Rank Math/Yoast, schema, redirect/301/htaccess, GSC, content parity, migracija sadrzaja. Deo 2/4 rascepa naucene-lekcije.md (2026-08-20, vault higijena).
---

# Naucene lekcije — SEO / sadrzaj / migracija

> 2/4 tematskog rascepa `reference/naucene-lekcije.md` (2026-08-20). Ostala tri: [[reference/lekcije-wp-db-tehnika]] · [[reference/lekcije-ads-tracking]] · [[reference/lekcije-alati-vault-delegati]]. Indeks: [[reference/naucene-lekcije]].

## Regression sweep protiv sitemap-a lažno prijavljuje "0 razlika" ako je sitemap zastareo (2026-08-20)

`regression-sweep.php` čita listu URL-ova isključivo iz `sitemap_index.xml`. Rank Math kešira
sitemap kao fajlove (`uploads/rank-math/*.xml`, gotcha zabeležen 18.08) — kad se proizvod objavi
(`draft` → `publish`), keš se ne osvežava sam. Posledica: sveži publish se **tiho ne proveri**,
a sweep i dalje prijavljuje "0 razlika naspram baseline-a" jer i baseline i novi sweep jednako
propuštaju istu stranicu — brojevi se poklapaju, ali oba su pogrešna. Konkretan slučaj: Onda
(17957) objavljena 20.08, prvi sweep isti dan pokazao 235/235 stranica i 0 razlika, dok stranica
uopšte nije bila u sitemap-u. **Pravilo: pre svakog potvrdnog sweep-a posle publish/draft/slug
promene prvo proveriti da je sveža stranica u relevantnom pod-sitemap-u** (`curl` + `grep` na
slug), po potrebi obrisati `uploads/rank-math/*.xml` da se keš regeneriše, tek onda pokrenuti
sweep. Detalji: [[dnevnik/2026-08-20-potvrdni-sweep-i-backup-posle-freeze]]

## Ne mapirati stranu fire-klasu na postojeći EN taksonomija termin bez potvrđene ekvivalencije (2026-08-20)

Codex Wall Mat spec navodi "Fire Retardant Class 1" — italijanska građevinska
klasifikacija ("Classe 1"), ne EN standard. Prvi pokušaj je ovo mapirao na
postojeći `pa_vatrootpornost` termin `Bfl-S1` (EN 13501-1, klasifikacija
SPECIFIČNO za podne pokrivače) — dvostruko pogrešno: Wall Mat nije pod nego
zidna obloga, a italijanska "Classe 1" i EN "Bfl-s1" nisu potvrđeno
ekvivalentne. Pravilo: kad izvor navodi klasifikaciju iz drugog nacionalnog/
regulatornog sistema, ne gura se u postojeći EN taxonomy termin "jer zvuči
slično" — ili se traži i potvrđuje prava ekvivalencija, ili ostaje kao
slobodan tekst u spec tabeli bez dodele atributa.

## Use-case landing stranice istog materijala treba spojiti dosledno na CELOJ liniji proizvoda, ne samo delimično (2026-08-20)

Pri uvozu Codex kataloga, 11 "use-case" stranica za Onda/Maxionda (zaštita
stubova, klupa, tribina...) ispravno je spojeno u "Primena" sekciju
roditeljskog proizvoda umesto da postanu 11 novih SKU-ova za isti fizički
materijal. Ista logika NIJE primenjena na Quadrio granu istog uvoza — 6
use-case stranica (Terasa, Gazebo, Atletska staza, Štale, Košarkaški teren,
Rampa) je prvo kreirano kao posebni draft proizvodi, iako je izvor
(`"Material": "Quadrio"`) jasno govorio da je materijal isti. Miroslav je to
uhvatio ("verovatno i tu ima duplikata") i tražio spajanje naknadno. Pravilo:
kad se odluka o spajanju use-case varijanti primeni na jednu granu
proizvoda u istom zadatku, provera "da li ista logika važi i za ostale
grane" ide **pre** kreiranja, ne posle — dosledna primena u jednom prolazu
štedi brisanje slika/proizvoda i drugi krug potvrde.

## Uslovni izuzetak u 301 mapi se proverava u bazi, ne u zapisu (2026-08-19)

Pravilo `/podovi-za-garaze/` je 11.08 izostavljeno iz `.htaccess` drafta *jer je taj
URL na buildu bio zauzet živom stranicom*; 18.08 je stranica draftovana i razlog je
pao, pa bi URL posle migracije bio 404. Provera pri svakom sweep-u je zato **upit u
bazu**, ne čitanje beleške: `SELECT ID, post_status FROM wpgs_posts WHERE post_name
IN (...)`. Isto važi obrnuto — vraćanje draftovane stranice u `publish` znači da
pravilo mora **da se isključi**. Svaki izuzetak oblika „ne prenosi se **jer** je X"
pada tiho kad X prestane da važi.

## Nula razlika u sweep-u ne znači nula promena (2026-08-19)

Sweep 19.08 je vratio 0 kvarova, ali **30 URL promena i 18 izmena title/meta** u
odnosu na baseline 13.08. Deo njih nije bio objašnjiv iz `PROGRESS`-a jer su
konsolidacione skripte 13.08 radile **posle** generisanja baseline CSV-a (15:47–18:35
vs snimak 17:24) — pa rad od 13.08 figurira kao „promena od 19.08". Kad diff pokaže
više redova nego što dnevnik sugeriše, prvo se poredi **vreme nastanka baseline-a**
sa vremenom izvršenja skripti tog dana, pa tek onda traži regresija.

## „Stranica ne postoji" se proverava u bazi, i to sa sinonimima (2026-08-19)

U jednom danu dva puta je plan tražio **novu stranicu** za upit koji je već imao rangiran URL:

| Plan je rekao | Stvarno stanje |
|---|---|
| `/podovi-za-radionice/` — „najveća prilika **bez namenske stranice**" | postoji na live-u i buildu: blog post **5637**, poz. **3,6** / CTR 7,6% — ali hobi tekst o privatnoj garaži, dok upit dolazi od auto-servisa i CNC radionica |
| `podovi za skladišta` — „rupa, poz. 14,6 / CTR 0%" | namenu pokriva **16687** (`podovi-za-magacine-i-hale`), poz. **4,0** / CTR 5,5%; „magacin" na njoj **18 puta**, „skladiš\*" **jednom** |

**Obrazac:** rupa u GSC-u skoro nikad ne znači „nema stranice" nego **nesklad namere ili
leksike sa stranicom koja je već rangirana**. Nova stranica u oba slučaja ne bi popunila
rupu nego podelila autoritet sa sopstvenom stranicom koja radi.

Obavezno pre svakog `wp_insert_post`:

```sql
SELECT ID,post_title,post_name,post_type,post_status FROM wpgs_posts
WHERE post_name LIKE '%pojam%' OR post_title LIKE '%pojam%';
```

plus `grep` po `migracija/parity-inventar.csv` (nosi i live URL-ove, ne samo lokalne) **i
provera sinonima** — magacin/skladište, radionica/servis, dvorište/terasa. Sinonim je ono
što je oba puta promaklo: `post_name LIKE '%skladis%'` vraća prazno, a stranica postoji
pod „magacin".

Kad je nalaz nesklad leksike, ispravka je **proširenje postojeće stranice** (sinonim u H2,
FAQ, title/meta, focus keyword), ne nov URL. Na 16687 je to bilo 1 → 25 pojavljivanja
„skladiš\*" bez ijednog novog URL-a, izmene u `.htaccess` ili reda u sitemap-u.

average_rating, total_sales, tax_status, tax_class`).

## Rank Math kešira sitemap kao FAJLOVE, ne kao opciju (2026-08-18)

Posle dodavanja dva proizvoda sitemap je i dalje vraćao `lastmod` od pre pet dana. Brisanje
opcije `rank_math_sitemap_cache_files` i svih `_transient_*sitemap*` **nije promenilo ništa**
— pravi keš su XML fajlovi u `wp-content/uploads/rank-math/*.xml`. Tek `rm *.xml` iz tog
foldera natera regeneraciju.

Isti obrazac kao „trebalo je `wp rewrite flush`" (§7.1): kad izlaz ne prati bazu, keš je
sadržajem i navodnicima radije zameniti `Write` alatom.

## Protivrečnost slike i natpisa se ne vidi iz HTML provere (2026-08-18)

Fotografija sa Ecotile ESD stranice, imenovana kao „X-Joint ploča", zapravo prikazuje
**pribor za uzemljenje** (bakarna traka, priključak, kabl). Postavljena je kao glavna slika
antistatik proizvoda — onog koji se **ne uzemljuje** — i kao prva kartica ispod natpisa
„bez uzemljenja". Sve provere su bile zelene: HTTP 200, 1×H1, `srcset` radi, alt postoji.

Vizuelni sadržaj traži vizuelnu proveru. Posle svakog postavljanja slika otvoriti stranicu
---

## Klaster agregat krije pod-klastere — prilika se vidi tek na query nivou (2026-08-18)
- Simptom: u [[seo/2026-07-27-content-klasteri]] klaster INDUSTRIJSKI stoji kao 1.537 prikaza / 90d — mali, neprioritetan. Pogled na **query nivo** u istom izvoru (`seo/gsc-svi-upiti-16m-2026-07-04.csv`) pokazuje da unutar njega „radionica" varijante (`podovi za radionice`, `pod za radionicu`, `gumeni/pvc/podne obloge za radionice`, `plocice za radionicu`, + „cena" varijante) nose **~4.700 prikaza / ~275 klikova sa poz. 3,5–7 i CTR do 9,8%** — i **nemaju nijednu namensku stranicu**.
- Uzrok: klasterizacija po prioritetnom keyword matchingu svrstava sve „industrijsko" u jednu kantu, pa se pod-intent sa sopstvenim rečnikom („radionica", a ne „industrijski pod") rastvori u proseku. Isti agregat istovremeno skriva i da head-termin `industrijski podovi` (6.321 prikaz) curi sa CTR 2,6%.
- Ovo se dogodilo **drugi put**: revizija „dvorište" (27.07) je oborila preporuku za novu stranicu upravo tako što je sišla na query→page parove i našla tri intenta sa tri različita vlasnika.
- **Pravilo:** pre bilo koje odluke „ovaj klaster nije vredan" ili „napravi novu stranicu za ovaj klaster" — sići na query nivo i grupisati po **rečniku kojim kupac govori**, ne po našoj taksonomiji proizvoda. Agregat služi za redosled rada, ne za odluku o pojedinačnoj stranici.
posle učitavanja.

## Zapisane GSC brojke u migracionim CSV-ovima nisu pouzdane — svež pull pre svake odluke (2026-08-13)
- **Tri promašaja u jednoj sesiji**, sva tri bi vodila na pogrešnu odluku:
  `parity-inventar.csv` pripisuje `/ergonomske-podloge-2/` **110 klikova** (stvarno **1 prikaz /
  0 klikova** u 90d, 123/4 u 12 mes.) · isti fajl nosi `lokal_id` postova koji **ne postoje**
  (15977/15967 za parket redove) · `redirect-mapa-FINAL.csv` red 17 tvrdi „311 klikova / poz. 6,9
  / CTR 4,92%" za post 2622, koji u 12 meseci ima **0 klikova**.
- Uzrok: kolone su popunjene jednokratno pri F1 baseline-u (21.07) ili ranije, iz različitih
  perioda, i **nikad se ne osvežavaju** — a dokument izgleda autoritativno.
- **Pravilo: pre svake odluke o URL-u pokreni `gsc_page_queries.py`** na 90d **i** 12 meseci.
  Dva prozora, jer stranice odumiru — 90d sam može da prikaže mrtvom stranicu koja je nedavno
  radila, a 12 meseci sam prikriva svež pad.
- `PYTHONIOENCODING=utf-8` je obavezan kad se prosleđuje više `--page` argumenata, inače puca
  na `UnicodeEncodeError` pri ispisu ćiriličnih upita.

## Draftovanje stranice traži proveru da li je neko istorijsko pravilo cilja (2026-08-13)
- Kad stranica ode u draft, **svako 301 pravilo koje na nju pokazuje počinje da vodi na 404** —
  i to se vidi tek posle migracije, kad je najskuplje.
- U jednoj sesiji tri puta: `/ergonomski-podovi/` (**160** pogodaka) · `/home/industrijski-podovi-najcesca-pitanja/`
  (**615**) · jutros 4 pravila sa ukupno **365** pogodaka. Sve pretočeno na nov cilj u istom potezu.
- Provera pre draftovanja: `grep -n "<slug>" migracija/redirect-mapa-HISTORIJSKI-65-FLAT.csv
  migracija/redirect-mapa-FINAL.csv migracija/htaccess-301-DRAFT.txt` — traži slug i kao **cilj**,
  ne samo kao izvor. `redirect-verify.php` hvata ciljeve koji nisu 200, ali tek posle regeneracije.
  ako nisu, izlaz bez izmena) čini je bezbednom za ponovno pokretanje.

## Rank Math sitemap keš ne zna za direktan SQL upis (2026-08-13)
- Posle zamene slug-a preko `$wpdb->update`, `post-sitemap.xml` je i dalje servirao **stari**
  URL — WP hook-ovi na kojima Rank Math visi ne okidaju pri direktnom upisu u bazu.
- Rušenje keša: `\RankMath\Sitemap\Cache::invalidate_storage()` (kroz `php -r` sa
  `wp-load.php`). Važi za svaku izmenu slug-a, statusa ili `noindex`-a izvedenu direktno.
- Praktična posledica: sitemap se **mora ponovo proveriti** posle takvih izmena, inače u
  razlogom. Inače se za mesec dana ne zna da li je promena bila namerna.

## Sitewide skok u regression brojkama je prvo pitanje „šta je uklonjeno", ne „šta je pokvareno" (2026-08-13)
- Sweep je pokazao **−118 slika na svakoj stranici** u odnosu na baseline od 3 dana ranije. Prvo čitanje je „nešto je masovno polomljeno".
- Razrešeno **jednom brojkom**: jedinstvenih slika 1.182 → 1.158 (samo −24). Da su slike stvarno nestale, jedinstveni skup bi pao za ~118. Konstantna delta po stranici + skoro nepromenjen jedinstveni skup = **nestao globalni blok** (zaglavlje/podnožje/meni), a slike i dalje postoje na svojim stranicama.
- Uzrok nađen za 2 minuta — **po imenu backup fajla** (`..._pre-uklanjanje-meni-ikonica.sql`), ne u dokumentaciji, jer unosa nije ni bilo. **Pravilo: `ls antasline-backups/` je brži izvor istine o tome šta se juče radilo nego dnevnik**, kad dnevnik zataji.
- **Pravilo: pre nego što se zaključi „te slike nemamo", grep-uj backup-e po imenu pojma**, pa skini original sa live-a i uvezi kroz `wp media import` (ne ručnim `cp` u `uploads` — inače nema priloga, ni alt teksta, ni generisanih veličina).

## Alt tekst se broji po KANALU RENDEROVANJA, ne po medijateci — razlika je 66 vs 6.638 (2026-08-12)
- Medijateka ima **7.725 slika, 6.638 bez alta**. Zvuči kao nedeljni posao. Stvarnih slika koje se **renderuju** a nemaju alt bilo je **66**.
- Razlog: WordPress registruje svaku generisanu veličinu kao zaseban prilog (Porto-era artefakt), plus godinama nagomilani neupotrebljeni uploadi. Nijedno od toga korisnik nikad ne vidi.
- **Metod: broji se po kanalu kroz koji slika stiže na stranicu** — `_thumbnail_id` proizvoda · `_product_image_gallery` · `<img>` u `post_content`. Sve ostalo u medijateci je šum.
- 🟢 **Prazan alt je često TAČAN odgovor, ne bag.** Od 225 nalaza, **159 su bile dekorativne SVG ikonice** (`montaza.svg`, `odrzavanje.svg`, `izdrzljivost.svg`…) uz tekst koji ih već imenuje — `alt=""` je za njih ispravno po WCAG. Popunjavanje bi bilo **regresija** pristupačnosti (v. lekciju od 2026-08-05). Uvek prvo grupisati nalaze **po imenu fajla**: ponavljanje istog `.svg` 25–28 puta je najbrži signal da je reč o ikonici, ne o fotografiji.
- 🔴 **Jedan prilog = jedan alt, bez obzira na broj galerija u kojima stoji.** Dva priloga su bila u 2 odn. 3 galerije — njima alt **ne sme** biti naslov jednog proizvoda, mora biti neutralan opis onoga što se vidi. Skripta zato ima tvrdu proveru: deljeni prilog bez neutralnog opisa = prekid bez upisa, ne tiho pisanje pogrešnog teksta.
- **Pravilo: kad kanal zavisi od on/off prekidača kod trećeg lica, prekidač se verifikuje pre nego što se u kanal uloži rad, i upisuje se u dokumentaciju sa datumom provere.** Isti obrazac već postoji u ovom projektu: `noindex` na stranici, `include_in_conversions_metric` na Ads akciji, `tax_*_sitemap` ključevi posle Rank Math importa — sve „tihi prekidači" koji su nas već koštali vremena.

## Konvencija koju niko zvanično nije potvrdio ≠ standard — `llms.txt` je bio nagađanje industrije (2026-08-12)
- `llms.txt` + `llms-full.txt` su napravljeni i deployovani na live (23.07) kao deo „GEO paketa", uz ogradu u [[seo/geo-ai-plan]] da adoptacija „nije zvanično potvrđena".
- **Naše sopstveno merenje je odgovorilo pre dokumentacije:** [[analiza/BOT-CRAWLER-LOG]] je kroz dva preseka pokazao **0 organskih hitova** — nijedan AI bot nije zatražio fajlove, iako su svi aktivno crawlovali sajt u istim prozorima.
- Tri nedelje kasnije Google to i napismeno potvrđuje: *„Google Search doesn't use them"* — niti štete niti pomažu.
- **Pravilo: kad se uvodi konvencija koju nijedan proizvođač nije zvanično podržao, uz nju ide merenje koje može da je opovrgne, i unapred definisan trenutak odluke.** Ovde je to slučajno urađeno kako treba (bot log je pokrenut dan posle deploy-a) — otud je zatvaranje stavke koštalo jedno čitanje, ne ponovnu raspravu.
- Vezano: [[analiza/2026-08-11-snapshot-jul]] §2.3b · [[dnevnik/2026-08-11-generate-lead-inflacija-dijagnoza]].

## Pad organskih klikova uz RAST pozicije nije regresija sajta — proveri CTR pre nego što tražiš krivca (2026-08-11)
- Jul 2026 vs jul 2025: pozicija **8,2 → 6,0** (bolje), prikazi **+22%**, a klikovi **−18%**, jer je CTR pao 6,76% → 4,52%. Isti obrazac na nivou pojedinačnog upita: `dimenzije košarkaškog terena` je na poziciji **1,9** sa CTR-om **2,3%** (732 prikaza, 17 klikova) — na prvoj poziciji CTR bi trebalo da bude 25–35%.
- To je SERP koji troši klik (AI Overviews, snippet, PAA), ne naš rad. Najizloženiji su tačno informativni upiti (dimenzije, „kako se pravi") — a to su naše najjače stranice po prikazima.
- **Pravilo: pre nego što se pad klikova pripiše izmeni na sajtu, razložiti ga na pozicija × prikazi × CTR.** Ako pozicija raste a CTR pada, uzrok je van sajta i akcija je drugačija (razlog za klik — cena, kalkulator, galerija — a ne „više sadržaja").
- Neproverено: da li se Ads konverzija (`__awct`) i `fbq Lead` isto multipliciraju na live-u — isto pravilo ih okida, ali Ads sam deduplicira konverzije po kliku, pa efekat nije nužno isti. Ne tvrditi bez merenja.

## Yoast→Rank Math importer NE prenosi taksonomijske sitemap-e (GSC priprema, 2026-08-11)
- Posle migracije SEO plugina (05.08) svih 12 `tax_*_sitemap` ključeva u `rank-math-options-sitemap` je bilo `off`, dok je Yoast na live-u imao uključene `category`/`product_cat`/`product_tag`/`product_brand`.
- Rezultat: build je emitovao **3 child sitemap-a tamo gde live emituje 7**. Pogađalo je 27 URL-ova sa izmerenih **79 klikova / 2.583 prikaza** (GSC, 3 meseca).
- Importer prenosi naslove/opise i opšta podešavanja i **ne javlja** da sitemap deo nije pokriven.
- ⚠️ Nije `noindex` — te stranice su i dalje `index, follow`; gubi se **otkrivanje**, ne indeksabilnost. Ali na migraciji, kad ceo URL skup ide ponovo kroz crawl, to je najgori trenutak.
- **Pravilo:** posle svake migracije SEO plugina eksplicitno pročitati `tax_*_sitemap` i `pt_*_sitemap` ključeve i uporediti sa brojem child-ova u starom sitemap indexu.

## Rank Math kešira sitemap-e u FAJLOVE — izmena opcije mimo admin UI ne obara keš (2026-08-11)
- Posle direktnog `UPDATE` nad `rank-math-options-sitemap`, `sitemap_index.xml` je i dalje vraćao stara 3 child-a. Invalidacija se okida tek na snimanje kroz admin UI.
- Keš je na **dva** mesta i moraju oba: opcija `rank_math_sitemap_cache_files` (mapa hash→tip) + fajlovi `wp-content/uploads/rank-math/rank_math_*.xml`.
- Isto važi za dan migracije: ako posle prebacivanja sitemap index pokaže manje child-ova nego što treba, prvo osumnjičiti keš, ne podešavanja.
- 🆕 **Dopuna 2026-08-12: ni ta dva mesta nisu dovoljna.** Posle uključivanja `tax_product_brand_sitemap` + brisanja opcije **i** pražnjenja tabele `rank_math_sitemap_cache`, `product_brand-sitemap.xml` se **servirao ispravno** (2 URL-a) ali ga `sitemap_index.xml` i dalje **nije nabrajao** — index se generiše iz zasebnog keša. Rešava tek poziv **`\RankMath\Sitemap\Cache::invalidate_storage()`** (pa `flush_rewrite_rules`). Simptom je podmukao jer child fajl radi, pa deluje da je sve u redu dok se ne otvori sam index.

## `wp_term_taxonomy.count` nije dokaz da termin ima sadržaj (2026-08-11)
- `product_brand` je pokazivao `Ergomat 25` / `Ecotile 3`, pa su termini delovali popunjeno. Stvarne veze u `term_relationships`: **0 proizvoda**, samo 7 **priloga** (`attachment`).
- Brojači su zaostali iz starog (Porto) builda i nikad nisu prebrojani — `wp term recount` se ne izvršava sam pri SQL importu.
- **Pravilo:** kad se odlučuje sudbina arhive (sitemap, redirect cilj, brisanje), brojati kroz `term_relationships` + `posts` sa filterom na `post_type`/`post_status` — ili, najpouzdanije, otvoriti stranicu i pogledati.

## `301 → 200` nije dovoljna provera cilja redirekta — prazna arhiva je takođe 200 (2026-08-11)
- `htaccess-301-generate.php` odbija upis ako cilj nije 200. `/бренд/ecotile/` → `/brend/ecotile/` je prošlo: cilj **jeste** 200, ali je prazna WooCommerce arhiva („nema proizvoda", 0 linkova ka proizvodima).
- Gore: `/бренд/ecotile/` je jedno od 5 pravila u B3 spot-check listi za dan migracije — spot-check bi prijavio uspeh („301 na tačan `Location`") uz beskorisno odredište.
- **Pravilo:** za redirect ciljeve koji su **arhive** (kategorija/tag/brend), pored statusa proveriti i da listing nije prazan. Statusni kod ne razlikuje „stranica radi" od „stranica je prazna".
- ✅ **Konkretan slučaj rešen 2026-08-12** (obe arhive napunjene, 7+27 proizvoda) — ali pravilo i ograničenje generatora ostaju: `htaccess-301-generate.php` i dalje proverava samo status.

## Sweep kroz sitemap ne može naći ono čega u sitemap-u nema (2026-08-11)
- Ni regression sweep (10.08) ni dijakritika sweep (11.08) nisu prijavili da 27 URL-ova nedostaje — oba uzimaju listu URL-ova **iz** sitemap-a.
- Ista slepa tačka je istog dana sakrila 2 slike 404 na `noindex` postu 16613.
- Setup plan za migraciju: [[reference/api-konektor-setup.md]] Korak G.

## `RedirectMatch` ČUVA query string — `?gclid=` preživljava 301 (2026-08-11)
- Bitno jer bi suprotno značilo da svaki preusmeren klik iz oglasa gubi `gclid` → konverzija se ne pripisuje Ads-u, a to bi se otkrilo tek posle migracije.
- Izmereno u izolovanom Apache folderu: `/sportski-podovi/?gclid=X&utm_source=google` → `Location: /sportske-podloge/?gclid=X&utm_source=google`. Važi i za ćirilična pravila.
- Lek na vrhu skripte: `sys.stdout.reconfigure(encoding="utf-8")`. Obavezno u svakoj konektor skripti koja može vratiti ćirilične nazive (Ads ad grupe su „Огласна група 1").

## Rank Math Redirections: pun WP boot po redirektu, ali ume da izveze u Apache — nije „ili-ili" (2026-08-11)
- Modul postoji u **besplatnoj** verziji; na ovom buildu je **isključen** (`rank_math_modules` nema ni `redirections` ni `404-monitor`).
- Izvršava se na **`add_action('wp', 'do_redirection', 11)`** — dakle pun WP bootstrap + parsiranje upita **pre** nego što se izda 301. Najskuplji mogući način da se vrati 301, i **pada zajedno sa WP-om**.
- `class-export.php` izvozi u Apache (`RewriteRule … [R=301,L]`) ili nginx `.conf` → pravila se mogu **autorisati u UI-ju, a isporučiti u `.htaccess`**. Poređenje „plugin ili `.htaccess`" je lažna dilema.
- Podela koja stvarno radi je **po populaciji redirekta, ne po alatu**: poznat zamrznut skup sa velikim saobraćajem (migracija) → `.htaccess`, jer mora raditi i kad WP padne; nepredvidivi post-live 404-ovi → plugin, jer ih vlasnik sajta rešava sam kroz UI i `404-monitor` pravi pravilo iz zabeleženog 404-a.
- 🔴 **Isti URL nikad na oba sloja.** `.htaccess` se izvršava prvi i tiho pobeđuje — pravilo u UI-ju tada izgleda „ne radi" bez ijedne poruke o grešci.
- ⚠️ Opcija `disable_auto_redirect` gasi WP-ov core `wp_old_slug_redirect` (uslovno, ne automatski).

## Redirect pravila koja žive u BAZI (Redirection plugin) nestaju sa migracijom — moraju u `.htaccess` (W3 3.9, 2026-08-11)
- Migracija zamenjuje živu bazu lokalnom. Sve što Redirection plugin drži u tabelama odlazi sa njom — **62 pravila sa ~46.000 zabeleženih GSC pogodaka** bi tiho palo na 404.
- Analiza tih pravila je postojala od 21.07, ali nikad nije preneta u `htaccess-301-DRAFT.txt`. **Analiza ≠ implementacija** — dok pravilo nije u fajlu koji se aktivira, ono ne postoji.
- Opštije: pred migraciju napraviti popis **svega što živi u bazi a ponaša se kao konfiguracija** (redirekti, plugin opcije, cron unosi), jer se to ne vidi ni u jednoj proveri fajlova.

## Dve redirect mape zajedno mogu napraviti PETLJU — proveriti A→B / B→A pre spajanja (W3 3.9, 2026-08-11)
- `redirect-mapa-FINAL` je vodila `/na-kojoj-podlozi…/` → `/bergo-ultimate…/`, a istorijska mapa **tačno obrnuto**. Svaka mapa je sama po sebi bila tačna u trenutku pisanja.
- Rezultat spajanja: beskonačna petlja na oba URL-a. Ni jedna ni druga mapa se ne bi otkrila proverom „da li cilj vraća 200".
- Razrešava se merenjem na buildu, ne rasuđivanjem: koji od dva URL-a je danas 200, taj je kanonski.

## Proveravati i IZVORNE URL-ove redirekta, ne samo ciljeve (W3 3.9, 2026-08-11)
- Standardna provera („svaki cilj vraća 200") je propuštala **2 pravila koja bi ubila stranice koje smo u međuvremenu izgradili**: `/lvt-…/vinil-podovi-za-restorane…/` (588 GSC) je sada stranica 16686, `/podovi-za-garaze/` (182 GSC) je 16875.
- Pravila su bila starija od tih stranica. Mehanički prepis starih redirekta u novi build **pregazi sopstveni rad** — a HTTP provera ciljeva to ne vidi jer su ciljevi ispravni.
- Pravilo: izvor koji na novom buildu vraća 200 = pravilo se preispituje, ne prepisuje.

## mod_alias `Redirect` je PREFIKS-match — koristiti sidreni `RedirectMatch "^/put/?$"` (W3 3.9, 2026-08-11)
- `Redirect 301 /podovi-za-terase/ /spoljnje-podne-obloge/` hvata i `/podovi-za-terase/bilo-sta/` i **lepi ostatak putanje na cilj** → `/spoljnje-podne-obloge/bilo-sta/` (404). Na punoj listi: **15 kolizija**, najgore u `/home/industrijski-podovi/` grupi (8 pravila) i `/podovi-za-terase/` grupi (4).
- Sa `Redirect` redosled linija postaje kritičan (specifičnija pravila moraju iznad). Sa sidrenim `RedirectMatch "^/put/?$"` **redosled prestaje da bude bitan** — jedan izvor grešaka manje na dan migracije. `/?$` pokriva i varijantu bez kose crte.
- U generatoru escape-ovati samo prave metakaraktere (`addcslashes`, ne `preg_quote`) — `preg_quote` pretvara svaku crticu u `\-` i fajl postaje nečitljiv baš onda kad se čita pod pritiskom.
- Ćirilične putanje rade doslovno (`RedirectMatch "^/бренд/ecotile/?$"`) — stara ograda o `RewriteRule` sa `\x` escape-om nije potrebna, testirano pod Apache-om.

## Draft koji se piše ručno tiho zastari — generisati ga iz izvora istine (W3 3.9, 2026-08-11)
- `htaccess-301-DRAFT.txt` je bio od 27.07, a njegov izvor `redirect-mapa-FINAL.csv` menjan 30.07. Datum fajla je bio jedini signal — sadržaj je izgledao uredno.
- Lek: `migracija/alati/htaccess-301-generate.php` generiše draft iz obe mape i **odbija upis ako ijedan cilj nije 200**. Nemoguće je dobiti draft koji je stariji od mapa ili koji cilja na 404.
- Eksplicitan `UPDATE post_parent=0` pre `wp_delete_post(force)` uklanja svako nagađanje o tome šta core radi sa decom-prilozima. Kontrola posle: broj priloga pre = posle (7.764 = 7.764).

## Mrtav draft nije nužno smeće — može biti izvorni tekst novih stranica (2026-08-11)
- Legacy CPT draftovi su ranije korišćeni kao **izvor sadržaja** za WoodMart rebuild (Naxos Evolution → `/sportski-podovi-za-sale-i-balone/`, 378 GSC klikova).
- `wp db query` je vratio **0 redova** za upit nad `wpGs_options` iako redovi postoje, bez greške. Za dijagnostiku koristiti `eval-file` sa `$wpdb->get_results()`, ne `db query`.

## „Nema meta opis + van sitemap-a" nije nužno bag — može biti tačan opis penzionisane stranice (2026-08-11)
- 4 lokalne stranice (`podovi-za-poslovni-prostor`, `izgradnja-terena-za-tenis`, `podne-obloge-za-promocije-i-sajmove`, `galerija-sportskih-terena`) izgledale su kao propust: `noindex`, bez meta opisa, nevidljive regression sweep-u.
- Provera pre popravke: **svaka ima noviji, već indeksiran parnjak iz WoodMart rebuild-a** (ID obrazac 5xxx = stari build vs 166xx/170xx = rebuild), i nijedna ne postoji na live-u. Noindex je bio nameran.
- Uključivanje indeksa bi napravilo 4 duplikat-para pred migraciju — suprotno anti-kanibalizacionom pravilu.
- Jedina pouzdana provera je **van baze**: `curl` nad renderovanom stranicom i pogled u `<head>`.

## Sitemap-based regression sweep ne vidi `noindex` stranice — po definiciji (meta opisi, 2026-08-11)
- `migracija/alati/regression-sweep.php` obilazi sitemap. Stranice sa `robots: noindex` nikad nisu u sitemap-u, pa ne postoje ni u izveštaju.
- Konkretno: sweep je prijavio 6 stranica bez meta opisa, a stvarno ih je 11 (6 + 4 `noindex` + 1 sa Rank Math fallback-om).
- **Pravilo:** `.bak` kopije su alat za lokalni rad i ne smeju u paket. `build-staging-package.sh` od 10.08 ima exclude za `*.bak-*`/`*.orig`/`*.old`/`*~`. Ako se ikad pakuje ručno — proveriti `find wp-content -name "*.bak*"` pre slanja.

## Isti pokvaren link ume da živi u VIŠE `widget_*` opcija — popraviti jednu nije dovoljno (W3 3.10, 2026-08-10)
- `/spoljne-podne-obloge/` (bez j) → 404 na **svih 195 stranica**. Prva popravka je gledala `widget_text` („Navigacija"), upisala 1 zamenu — i provera je i dalje javljala 404, jer je drugi pogodak bio u `widget_custom_html` (kolona „Podovi", tekst linka „Terase i dom").
- Isti tip bug-a je 2026-08-07 nađen na staging-u i tada je popravljen samo taj jedan pogodak.
- Isti obrazac se već ponavljao (v. „Refresh-evi od 2026-07-08", „4 duplikat-stranice", „`15580`→`16589`") — **plan koji nije proveren protiv stvarnog stanja stvara lažne blokatore**, i oni onda blokiraju stvarno.

## Rank Math besplatan (1.0.275) NEMA Video modul — `VideoObject` ide ručno (2026-08-10)
- Provereno na dva mesta, ne po sećanju: opcija `rank_math_modules` i `seo-by-rank-math/includes/modules/` na disku — 23 modula, `video` nije među njima.
- Rešenje koje se pokazalo boljim od ručnog upisa po stranici: **schema se izvodi iz markupa koji već stoji u sadržaju** (`woodmart-child/inc/al-video-schema.php` skenira `data-yt-id` na `wp_footer`). Nula izmena u bazi → zaobiđeni i kses (F7.15) i `wpautop` (F7.20c) i potreba za backup-om.
- **Prenosivo pravilo:** kad schema treba na N postojećih stranica, prvo pitati može li se izvesti iz postojećeg markupa umesto da se upisuje u `post_content`. Izmena koda je reverzibilna, izmena baze nije.
- `uploadDate` za YouTube video se ne dobija preko oEmbed-a (ne postoji u odgovoru) — dolazi sa javne `watch` stranice iz `ytInitialPlayerResponse` → `microformat.playerMicroformatRenderer.publishDate`. `duration` iz `videoDetails.lengthSeconds` (pretvoriti u ISO 8601). Bez API ključa.
- Bash/Windows gotcha usput: `"$VAR\\$file"` (backslash pred `$` u double-quoted stringu) se ne escape-uje kako se očekuje u Git Bash — koristiti forward-slash MSYS putanje (`/c/Users/...`) dosledno kroz ceo poziv, PHP na Windows-u ih prihvata bez problema.

## Sličan brend-naziv ≠ ista firma — proveriti member-listu holding grupe pre pretpostavke o poreklu slike (2026-08-08)
- M je pretpostavio da su "trava u boji" slike na `/vestacka-trava/` (live) od holandskog Condor Grass-a (dobavljača lokalnih Condor Schools/Playgrass proizvoda) jer se ime "Condor Group" pojavljuje u istom kontekstu holding-a koji ima i "Edel Carpets"/"Edel Yarns" članice. Filename prefiks na slikama (`EG-Colourful-*`) je zapravo od **Edel Grass B.V.** — posve odvojene firme (u vlasništvu Oranjewoud grupe), koja slučajno ima slično ime kao "Edel Carpets"/"Edel Yarns" (koje JESU Condor Group članice) ali sama nije na `condor-group.eu/en/group/members` listi.
- **Pravilo ubuduće (posebno bitno za 31.08 pravu migraciju)**: pre pisanja `$table_prefix` u `wp-config.php`, proveriti stvarni sadržaj dump-a — `grep -o "CREATE TABLE \`[a-zA-Z_]*\`" dump.sql | head -3` — i koristiti TAČNO to, ne prepisivati "wpGs_" iz dokumentacije bez provere. Ako se ovaj mismatch nikad ne pojavi na pravoj live→produkcija migraciji (mogu biti različiti dump-ovi/export putevi), ovo pravilo se briše kao irelevantno; do tada je aktivna provera.

## `alt=""` na slikama u sadržaju NIJE uvek bag — dekorativna ikonica pored istog teksta je ispravna WCAG praksa (alt-tekst red čekanja, 2026-08-05)
- Red čekanja "180 slika bez alt-a u sadržaju" (nasleđen iz 07-30 a11y plana) je pretpostavljao da je svaka prazna `alt=""` propust. Merenje je pokazalo da je **154/180 dekorativna F7-standard ikonica** (`montaza.svg`, `izdrzljivost.svg` itd.) koja stoji direktno pored `<h3>` naslova sa ISTOM informacijom (npr. `<img alt="" src=".../montaza.svg"/><h3>Montaža bez zastoja</h3>`) — prazan `alt` tu je namerna, ispravna WCAG praksa (screen reader ne duplira info), ne propust.
- Pravi obim je bio samo 26 slika (prave fotografije/case-study/sertifikat bedževi u 11 blog postova) — 7× manji nego što je red čekanja tvrdio.
- **Pravilo ubuduće:** pre nego što se "N slika bez alt-a" tretira kao mehanički/veliki zadatak, prvo grupisati po `src` basename-u — ako se isti fajl (ikonica) ponavlja na desetine stranica pored teksta koji već nosi tu informaciju, to nije red čekanja nego već-zatvoren nalaz. Samo jedinstveni content-fotografije idu u pravi red čekanja.
- Alt tekst za prave fotografije je najbezbednije izvući iz **postojećeg vidljivog caption-a/naslova odmah posle slike** (WP-ov caption obrazac, npr. "Amicus, Beograd") — ništa se ne izmišlja van onoga što stranica već tvrdi.

## Pre uvoza "nove" foto arhive na sport-stranice: proveriti upload datum postojećih slika, ne verovati inventaru na reč (BLOK E sport tereni, 2026-08-07)
- Inventar (`reference/foto-arhiva-inventar.md`, pisan 2026-08-05) je procenio "~100 sport-teren fotki čeka raspoređivanje" — sesija koja je krenula da to izvrši je PRVO proverila 8 ciljnih stranica i otkrila da većina već ima referentne fotografije, samo iz ranijih W1/W2 sesija (upload datumi 2020/11, 2021/03, 2022/03 — mnogo stariji od BLOK E arhive otvorene 2026-08-05).
- **Signal koji je ovo otkrio**: upload datum na postojećim slikama u `post_content` (`/wp-content/uploads/YYYY/MM/`) — 2026/07+ znači "iz ove BLOK E arhive", bilo šta starije znači "već urađeno u ranijoj sesiji, nezavisno". Provera je trajala par minuta po stranici (SELECT post_content + pregled) i uštedela dupliran rad na 6+ stranica.
- **Pravilo ubuduće**: pre bilo kakvog "red čekanja" zadatka koji uvozi sadržaj na postojeće stranice, prvo proveriti ciljne stranice direktno (ne verovati proceni napisanoj u prošloj sesiji na reč) — inventari/planovi stare i do par dana mogu biti zastareli ako je paralelno rađeno nešto slično iz drugog ugla.

## Potvrda POREKLA fotografije nije isto što i DOZVOLA za objavu — pitati eksplicitno kad su različiti nivoi poverenja (BLOK E padel fotke, 2026-08-07)
- Kad je M na pitanje "da li su ove fotke AntasLine reference ili sa sajta proizvođača" odgovorio "sa sajta proizvođača", to je odgovorilo na PREGLED (odakle su), ne na DOZVOLU (da li se smeju objaviti). Naknadno eksplicitno pitanje ("da li imamo dozvolu da objavimo") dobilo je suprotan odgovor od očekivanog — M je rekao da prva izjava nije bila odobrenje.
- Razlikuje se i po manufacturer-u: Geoplast/Ergomat dozvola (2026-08-07, ranije u istoj sesiji) NIJE automatski važila i za drugog proizvođača (Safitex/padel) — svaki izvor treba sopstvenu potvrđenu dozvolu, ne generalizovati sa jednog na drugi samo zato što je isti obrazac pitanja.
- **`wd-post-title` skip REŠEN 2026-08-05** — za razliku od `widget_title_tag`, ne postoji theme opcija za blog-loop post-title tag (potvrđeno: `xts-woodmart-options` ima samo `page_title_tag`/`widget_title_tag`). Umesto override-a `templates/content-default.php` (i 5 sličnih core varijanti, rizik na theme update), obrazac je: core `woodmart_page_title()` već zove `do_action('woodmart_page_title_after_title')` odmah posle H1 u SVAKOJ grani (portfolio/shop/generic/blog) — mu-plugin kači vizuelno sakriven `<h2 class="al-sr-only">` na taj hook, uslovljeno `is_home()`. **Opšti obrazac za buduće slične slučajeve**: pre override-a core template fajla, proveriti da li core funkcija već ima `do_action`/`apply_filters` hook na tačno tom mestu — mu-plugin na postojeći hook je uvek jeftiniji i update-safe u odnosu na child-theme kopiju celog template fajla.

## WoodMart VENDOR tema (ne child) generiše nevalidnu BreadcrumbList schema — dvostruko ugnježden niz (pickleball/pop-tenis, 2026-07-30)
- `wp-content/themes/woodmart/inc/modules/seo-scheme/class-breadcrumbs.php:56` ručno dodaje `[`/`]` oko `wp_json_encode($this->schema_items)` u `itemListElement` — ali `$this->schema_items` je već niz asocijativnih nizova, pa `wp_json_encode()` sam vrati `[{...}]`; rezultat je `"itemListElement": [[{...}]]`, nevalidna structured data (Rich Results/schema validator bi ovo odbio). Ovo je NEZAVISNO od Yoast-ove sopstvene `yoast-schema-graph` (koja je ispravna) — WoodMart ubacuje DRUGI, sopstveni `<script type="application/ld+json">` blok preko `add_filter('wp_footer', ...)`.
- **Aktivira se samo gde se `woodmart_breadcrumbs()` template-tag stvarno renderuje** (`inc/template-tags/template-tags.php:2067`) — potvrđeno na `/teren-za-pickleball/` i `/pop-tenis/`, ODSUTNO na `/kontakt/`, `/industrijski-podovi/`, `/dimenzije-fudbalskog-terena/` (verovatno drugačiji breadcrumb render put za te šablone/tipove). Ne pretpostavljati sitewide bez provere — testirati bar jednu stranicu svakog šablona.
- **Nije vidljivo korisniku** (ostaje unutar ispravno zatvorenog `<script>` taga) — razlikovati od pravog "sirov JSON kao tekst" bag-a (v. [[#Schema može mesecima da „postoji" a da nikad nije emitovana (pickleball 16616, 2026-07-28)]]); ovaj je čisto validacioni problem, ne vizuelni.
- **Meni na 1500px ima ~673px** (uz logo i telefonski CTA u istom redu). Šest grupa staje samo sa kratkim nazivima — izmeriti zbir `getBoundingClientRect().width` stavki pre nego što se doda grupa; prelamanje u drugi red povećava visinu headera na svakoj stranici.

## Yoast indexable hijerarhija — BreadcrumbList tiho gubi pretka (W7 F3, 2026-07-29)
- 🔴 **`post_parent` može biti tačan a breadcrumb pogrešan.** 4 stranice su emitovale `Početna > [stranica]` bez međukoraka, iako im je `post_parent` ispravan. Uzrok: `wpgs_yoast_indexable_hierarchy.ancestor_id` pokazuje na **indexable koji više ne postoji** (ostatak re-parentovanja sa starog draft-a). Yoast tada **ne pada i ne loguje ništa** — samo izostavi pretka. Popravka: `UPDATE … SET ancestor_id = <id novog pretka>`, ili brisanje samo **detetovog** reda da ga Yoast regeneriše.
- 🔴 **`ancestor_id = 0` nije sirak nego koren.** Uslov „nađi redove čiji predak ne postoji" (`LEFT JOIN … WHERE a.id IS NULL`) pokupi i sve stranice najvišeg nivoa. Kod nas je zbog toga obrisano ~290 indexable redova umesto 4, i pokvarenih stranica je poraslo sa 4 na 26. **Svaki „nađi siročiće" uslov mora eksplicitno izuzeti nulu** (`AND h.ancestor_id <> 0`), a brisati se sme **detetov** red, nikad pretkov.
- Vraćanje samo Yoast tabela iz punog dumpa: `awk '/^-- Table structure for table \`wpgs_yoast_indexable\`$/,/^-- Table structure for table \`wpgs_yoast_migrations\`$/' dump.sql > yoast.sql` pa `mysql < yoast.sql` — brže i bezbednije od punog restore-a.
- **Gola JSON-LD tekst u `post_content` (bez `<script>` taga) = dvostruko slomljena schema** (nađeno 2026-07-08, F3-reimportovan sadržaj, `/podloga-za-odbojkaske-terene/`): ako se FAQPage/schema JSON zalepi kao plain tekst u sadržaj klasičnog posta (ne u `[vc_raw_html]` ni u pravi `<script>` tag), `wpautop` ga razbija u `<p>`/`<br>` a `wptexturize` menja prave navodnike `"` u kucane `„…"` — rezultat je i vidljiv iskvaren tekst NA STRANICI (posetioci ga vide) i potpuno nefunkcionalna schema (Google ne parsira JSON van `<script>`). Ovo je vrlo verovatno identično na live sajtu ako je F3 reimport povukao 1:1. Provera: `curl stranica | grep "@context"` — ako se pojavi van `<script type="application/ld+json">`, popraviti. Fix: `$wpdb->update` sa `json_encode(..., JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)` obavijenim u pravi `<script>` tag (izbegava kses jer ide direktno u bazu, ne kroz `wp_update_post`).

## Permalink / rewrite izmene (parity F2, 2026-07-07)
- **Soft `flush_rewrite_rules()` nije dovoljan** posle promene WooCommerce `product_base`/`category_base` (permalink strukture) — proizvod URL-ovi vraćaju 404 uprkos ispravnom `get_permalink()` i ispravnim redovima u `rewrite_rules` opciji. Uvek koristiti `flush_rewrite_rules(true)` (hard flush) posle svake permastruct/permalink izmene.
- Format-konverzija (.jpg→.webp) menja ekstenziju u fajl-imenu → svaki hardkodovan `<img src>` u `post_content` koji ne ide preko attachment ID-a (npr. ručno ubačene "al-card" reference-foto kartice) ostaje na starom imenu i puca u 404. Posle svake batch konverzije formata: `SELECT ... WHERE post_content LIKE '%stari-fajl.jpg%'` (i `option_value` za widgete/theme mods) pre nego što se stari fajl smatra sigurnim za brisanje.

## Telefon insight
- Broj 072 dominira klicima vs 074; 46/50 klikova sa mobilnog → istaći 072 u oglasima i call asset-ima.

## Sadržaj / HTML unos
- **Pravilo za ubuduće**: svaki budući fizički `.txt`/statički fajl u docroot-u koji sadrži srpsku latinicu (isti obrazac kao `llms.txt`/`robots.txt`) treba ovu istu charset proveru pre nego što se proglasi gotovim — ne pretpostaviti da je UTF-8 fajl na disku dovoljan, proveriti stvarni HTTP response header.

## F1 parity provera — DB path string ≠ stvarna HTTP rezolucija (2026-07-21)
- **`get_page_by_path($slug, OBJECT, 'page')` sa SAMO leaf slug-om (bez punog path-a) pretražuje isključivo `post_parent=0`** — WP core hijerarhijsko poklapanje zahteva pun path kad ga zoveš sa jednim segmentom. Posledica: masovni lažni `NEDOSTAJE-LOKAL` na SVAKOJ ugnježdenoj W1 stranici (bergo-xl, industrijski-pod, kosarkaske-konstrukcije...) dok se ne prosledi pun `$path` umesto `$slug`. Prvi F1 run 2026-07-21 pao sa 129 na (lažnih) 105 PARITY pre fix-a.
- **DB-only poređenje putanje (string exact-match) za hijerarhijske taxonomy arhive (category, verovatno i product_cat) daje lažne "path mismatch" nalaze** — WP-ov rewrite sistem prihvata proizvoljan/netačan roditeljski prefiks u URL-u i svejedno servira ispravan term po zadnjem slug-u (`/category/pogresan-roditelj/pravi-slug/` i `/category/pravi-slug/` vraćaju identičan `<title>`, oba 200). Pravilo: svaki "path mismatch" nalaz iz DB poređenja MORA se potvrditi pravim `curl` pre nego što uđe u redirect mapu — 5 od 6 takvih nalaza 2026-07-21 bili su lažni.
- **UVEK prvo `git diff`/`git show HEAD:<put>` pre nego što se prepiše postojeći CSV sa "osveženim" podacima** — parity-inventar.csv nosi rukom upisane Miroslavljeve odluke (kolona `odluka`) i `LOKAL-NOVO` redove koje fresh re-generacija iz sitemap-a ne rekreira (jednosmeran pravac). Prvi pokušaj merge-a ove sesije je greškom overwrite-ovao fajl pre nego što su stare odluke pravilno pročitane (BOM na prvom header polju `\xEF\xBB\xBFlive_url` je pokvario `array_combine` lookup) — spašeno preko `git checkout -- <fajl>` jer je poslednja dobra verzija bila komitovana.

## Legacy CPT sitemap-ovi — orphan content posle W1 rebuild-a (2026-07-21)
- **Kad se nova stranica gradi iz starog CPT posta kao izvora (industrija-podovi/podovi-posl-prostor/spoljne-podne-obloge/vestacka-trava), stari post treba ručno prebaciti na `draft` — ako se to preskoči, ostaje `publish` i Yoast ga i dalje ubacuje u zaseban `{cpt}-sitemap.xml`**, indeksibilan i bez ijednog internog linka (thin/duplicate content rizik pred migraciju). Nađeno 25 takvih (3+4+2 iz tri CPT-a korišćena kao izvor za W1 stranice + svih 16 `vestacka-trava` postova, koji su čista zaboravljena legacy grupa bez ikakve veze sa novim proizvodima S2/S3 sesija).
- **Widget sadržaj u `wp_options` (`widget_block` i sl.) je serialized PHP niz — NIKAD ručni string-replace na sirovoj DB vrednosti** (menja dužinu stringa, `s:N:"..."` prefiks postaje neispravan i ceo niz se ne može unserialize-ovati → widget nestaje). Ispravan put: `get_option('widget_block')` (WP ga vrati već unserialized), izmeni `content` polje, `update_option()` (WP ponovo serializuje ispravno).

## Live SERP provere kroz Chrome nisu geolocirane u Srbiju (SERP snapshot, 2026-07-21)
- **`claude-in-chrome` pretrage na google.com sa `?hl=sr&gl=rs` parametrima NE garantuju RS-lokalizovan SERP** — to su slabi hintovi, ne pravi IP u Srbiji. Potvrđeno na "gumeni podovi za terase cena": GSC kaže pozicija 1,4, ali Antas Line se uopšte nije pojavio na prvoj strani u browser proveri (dok se na drugom upitu, "podloga za kosarkaski teren cena", poziciranje poklopilo #1 sa GSC 1,9 — nekonzistentno, ne pouzdano).
- **Pravilo**: GSC pozicija (preko Windsor.ai `searchconsole`) je jedini merodavan broj za sopstvenu poziciju. Live browser SERP provera je korisna SAMO za kvalitativan kontekst (koji se konkurenti pojavljuju, cenovni rasponi u AI Overview-u), nikad kao provera/osporavanje GSC pozicije.
- **Ispravan put**: PHP **double-quoted** stringovi (`"...\n..."`, PHP interpretira kao pravi newline) za anchor/replacement tekst kad se menja `post_content` preko `wp_update_post()`.

## Content-parity provere protiv live sajta — WebFetch parafrazira (antistatik fix, 2026-07-22)
- **`WebFetch` na živoj stranici NE vraća doslovan tekst čak i kad se to eksplicitno traži u promptu** — model iza alata sažima/parafrazira sadržaj (potvrđeno na `/antistatik-i-elektroprovodljivi-podovi/`: traženo "doslovan tekst, ne parafraza", dobijen sažetak sa izmenjenim rečenicama). Za bilo kakvu content-parity proveru (live vs. lokal 1:1) ovo je neupotrebljivo — razlike koje treba uočiti su baš na nivou tačnih formulacija.
- **Ispravan put**: `curl -A "Mozilla/5.0" <live-url> -o file.html`, pa Python (`re.sub` da se ukloni `<script>`/`<style>`, tagovi zamene za newline, `<[^>]+>` strip) da se dobije čist tekst red-po-red iz stvarnog HTML-a. Pouzdano i brzo (~100-tak linija za tipičnu stranicu), ne zavisi od modela koji "prepričava".

## FAQPage JSON-LD mora ostati sinhronizovan sa vidljivim FAQ tekstom (antistatik fix, 2026-07-22)
- Kad se FAQ odgovor u vidljivom HTML-u menja na WoodMart stranici koja FAQPage schema čuva kao base64-enkodiran `[vc_raw_html]` blob (isti obrazac kao F7.4 pilot na antistatik stranici), **schema se NE menja automatski** — ostaje stara verzija dok se ručno ne ažurira, što stvara vidljiv-sadržaj-vs-schema mismatch (Google penalizuje/ignoriše neusklađenu schema).
- 🔴 **Dopuna 2026-07-30 — keš tabela može sama biti pokvarena (mojibake), ne samo zastarela.** Nađeno sitewide (93 naslova/103 opisa): postmeta je bio čist UTF-8, ali `title`/`description` u `wpgs_yoast_indexable` su bili DVOSTRUKO-enkodovani (em-crta → "ÔÇö", č/š/ž → "─Ź"/"┼í" i sl.) — tačan mehanizam nastanka nije rekonstruisan, ali ograničen isključivo na `object_type='post'`, samo ta dva polja (breadcrumb_title/og/twitter/term nedirnuti). U ovom slučaju **DELETE + auto-regeneracija nije bezbedna preporuka** (kako gornji fix predlaže) — briše `id`, a `[[naucene-lekcije#Yoast indexable hijerarhija]]`-tip bag (ancestor_id pokazuje na obrisan red) se tad može ponoviti za bilo koju stranicu čiji je breadcrumb zavisio od tog ID-ja. Sigurniji fix za OVU vrstu kvara (postmeta ispravan, keš pokvaren): direktan `UPDATE wpgs_yoast_indexable yi JOIN wp{prefix}_postmeta pm ON pm.post_id=yi.object_id AND pm.meta_key='_yoast_wpseo_title' SET yi.title=pm.meta_value WHERE yi.title != pm.meta_value` (isto za `_yoast_wpseo_metadesc`→`description`) — zadržava `id`/hijerarhiju, samo ispravlja sadržaj polja. `wp yoast index --reindex` (CLI) takođe briše i ponovo gradi SVE redove (isti hijerarhija-rizik u širem obimu) i dodatno je nepouzdan na ovom sajtu — probni poziv je pogodio `Fatal error: Maximum execution time of 300 seconds exceeded` u `js_composer` color-picker modulu tokom WP bootstrap-a.

## Isti tekst (NAP adresa) živeo u 5 odvojenih DB redova istovremeno — SQL `REPLACE()` bez re-serijalizacije kad su string dužine jednake (NAP fix `/kontakt/`, 2026-07-27)
- Live `/kontakt/` (post 558, Kallyas/SiteOrigin tema) je čuvao istu grešku ("11050 Beograd" umesto tačnog "11000") u **5 nezavisnih mesta odjednom**: `panels_data` (aktivni SiteOrigin builder sadržaj, dupliran u 2 postmeta reda — poznat "dupli postmeta" obrazac), `_panels_data_preview` (admin-editor preview, takođe dupliran u 2 reda) i `zn_page_builder_els` (orphan Zion Builder sadržaj od ranijeg pokušaja, nije renderovan ali postoji). Šesti izvor (`widget_sow-editor` opcija, neaktivan sidebar widget) imao je isti tekst ali drugačiji telefonski broj — dokaz da je ovo star, ručno kopiran sadržaj iz više faza sajta, ne jedan izvor istine.
- **Provera šta se STVARNO renderuje pre svake izmene**: `curl` na pravu URL i `grep -c` za obe varijante broja — pokazalo tačno JEDNO mesto gde se "11050" pojavljuje na strani (unutar `panels_data`), dok header top-bar (drugi, nezavisan izvor) već ispravno prikazuje "11000". Bez ovog koraka lako se prevideti dupli/orphan redovi koji ne utiču na frontend, ili obrnuto — promeniti pogrešan izvor i misliti da je live popravljen.
- **Trik za bezbednu izmenu serijalizovanih PHP polja (`panels_data`, `zn_page_builder_els` i sl.)**: ako je zamenski string ISTE dužine kao originalni (`"11050"` → `"11000"`, oba 5 karaktera), `UPDATE ... SET meta_value = REPLACE(meta_value, 'stari', 'novi')` je bezbedan — PHP serijalizacija čuva `s:N:"..."` length-prefiks koji ostaje tačan bez ikakve re-serijalizacije. Da su dužine različite, direktan `REPLACE()` bi pokvario ceo builder (WordPress/SiteOrigin bi odbio da deserijalizuje polje). Za nejednake dužine potrebna je prava re-serijalizacija (PHP `unserialize()`/`serialize()` ciklus), ne string replace.
- Prateće pravilo: **izmena skripte koja se ne pokrene nije popravka nego pretpostavka.** Posle svake izmene migracione skripte — jedan pun prolaz u scratchpad, pa `tar -tzf`/`diff` nad rezultatom.

## Lokalni `.htaccess` nikad ne sme u migracioni paket — nosi podfolder `RewriteBase` i briše serverski LSCACHE blok (dry-run, 2026-08-13)
- Build je u podfolderu (`localhost/antasline`), pa lokalni `.htaccess` nosi `RewriteBase /antasline/` i `RewriteRule . /antasline/index.php [L]`. Da je taj fajl prepisao serverski, **svaki zahtev na produkciji** bi otišao na nepostojeću putanju — pad sajta u celosti, odmah, na dan migracije. Uz to bi nestao produkcijski `# BEGIN LSCACHE` blok (LiteSpeed keširanje, na kome visi ceo LCP plan) i cPanel PHP handler.
- Zbunjujuće je što lokalni `.htaccess` **izgleda produkcijski** — sadrži nasleđen cPanel `ea-php81` handler i stari `katastarrudarskogotpada.rs` redirect, jer je nekad kopiran sa live-a. Samo je WordPress blok lokalan. Vizuelna provera „liči na serverski" nije dokaz.
- 🔴 **Izmeštanje ne briše tajnu iz git istorije.** Jedina prava sanacija je rotacija (promena lozinke kod provajdera). Prepisivanje istorije ovde nije opcija — vault ima tri površine (lokal / cPanel / GitHub) + Obsidian Git auto-sync, rewrite bi razbio sve tri. Rotaciju planirati **posle** migracije: ne dirati kanal prenosa neposredno pred prenos.

## Draftovanje stranice ne prekida ni jednu vezu koja na nju pokazuje — meni, interni linkovi i 301 mapa ostaju da vise (konsolidacija, 2026-08-13)
- WordPress `nav_menu_item` zapis nosi samo `_menu_item_object_id`; kad cilj ode u `draft`, stavka **ostaje u meniju** i renderuje mrtav link. Isto važi za `<a href>` u `post_content` drugih stranica.
- Najskuplji sloj je treći i najlakše se previdi: **301 mapa**. Draftovanje 16665 i 16683 je učinilo da **4 istorijska pravila sa ukupno 365 GSC pogodaka** (268 + 54 + 43 + jedno bez brojača) pokazuju na stranice koje sada vraćaju 404 — lanac `stari URL → draftovana stranica → nova stranica` u najboljem slučaju gubi vrednost, u najgorem staje na 404. Uhvatio ih je `redirect-verify.php`, ne pregled.
- **Pravilo: uz svaki `publish → draft` idu 4 provere** — (1) `post_content LIKE '%/slug/%'` po celoj bazi, (2) `nav_menu_item` sa tim `_menu_item_object_id`, (3) **da li je taj URL cilj nekog 301 pravila** (ako jeste — spljoštiti lanac na konačno odredište), (4) da li je Ads final URL.

## 301 draft se generiše iz CSV-a — ručna izmena `.txt` fajla ne stigne do verifikatora (konsolidacija, 2026-08-13)
- `htaccess-301-DRAFT.txt` je **izlaz**, izvor istine su `redirect-mapa-FINAL.csv` + `redirect-mapa-HISTORIJSKI-65-FLAT.csv`. `redirect-verify.php` čita **CSV-ove**, ne draft.
- Posledica: ručno ispravljen draft prolazi vizuelnu proveru, ali verifikator i dalje prijavljuje stari nalaz — i sledeće pokretanje generatora **tiho vraća staro stanje**. Izgubljeno je 10-ak minuta na „zašto i dalje puca" pre nego što je pročitano zaglavlje verifikatora.
- **Pravilo: menja se CSV, pa se pokrene generator, pa verifikator.** Draft se nikad ne edituje rukom.

## Stranica sa 0 GSC prikaza nije dokaz da nema kanibalizacije — može značiti samo da još nije objavljena (kanibalizacija, 2026-08-13)
- Sve „cena" i „dimenzije" stranice napravljene na buildu u julu imaju **0 prikaza jer ne postoje na live-u**. Čitano naivno, to izgleda kao „nema preklapanja" — a zapravo znači da se preklapanje **tek dešava, na dan migracije**, kad 4 nove stranice izađu na upite koje postojeća stranica drži sa pozicije 1.
- Ispravno pitanje za build-only stranicu nije „koliko saobraćaja ima", nego **„koji upit cilja i ko ga danas drži"** — a to daje `gsc_page_queries.py` nad **postojećom** stranicom, ne nad novom.
- **Pravilo za izmene vault/velikih fajlova iz skripte:** sastaviti ceo sadržaj u promenljivu, upisati u `p + '.tmp'`, pa `os.replace(tmp, p)` (atomično). Emoji se piše kao **stvarni znak**, ne kao `\uXXXX` escape par.

## Live export sa samo `publish` statusom = tih blind spot u celom migracionom planu (draft blind spot, 2026-07-28)
- Live export od 2026-07-05 (`migracija/live-export-2026-07-05/`), izvor istine za `parity-inventar.csv` i ceo F1–F7 tok, sadrži **isključivo postove sa statusom `publish`**. Dve stranice sa realnim GSC saobraćajem (`/sportske-podloge/sportski-podovi-za-teniske-terene/` 552 impr u Q1, `/gumeni-podovi-javne-objekte-i-teretane/` 433 impr / 12 kl) bile su tada `draft` na live-u — nikad nisu ušle u inventar, pa bi 2026-08-31 nestale bez ijednog upozorenja. Otkrivene tek slučajno, kroz 404 dijagnostiku 27.07.
- **Pravilo: svaki naredni live export mora uključivati i draftove** (i `private`/`pending`), pa ih tek onda svesno filtrirati uz zabelešku — a ne ih nikad ni ne videti. Isto važi za bilo koji „popis live stanja": sitemap i sitemap-bazirani skenovi po definiciji ne vide draftove.
- **Šta bi ovo uhvatilo ranije**: unakrsna provera GSC URL liste (28d/90d) protiv `parity-inventar.csv` — svaki live URL sa nenultim impresijama koji NIJE u inventaru je alarm. Ta provera je jednokratno urađena 27.07 na 136 GSC URL-ova (nema drugih velikih slučajeva), ali nije deo rutine.

## GSC podatak „koja stranica drži koji upit" je ono što odlučuje rebuild vs 301 (2026-07-28)
- Standardni `gsc_report.py` agregira po upitu preko celog sajta i ne može da odgovori na pitanje koje se stvarno postavlja pri migracionoj odluci. Skripta `gsc_page_queries.py` (dodata u konektor 2026-07-28) filtrira po `page` dimenziji i vraća upitni klaster po konkretnoj stranici.
- **Zašto to menja odluku, konkretno**: `/gumeni-podovi-javne-objekte-i-teretane/` je „stranica o teretanama" po naslovu, pa je 301 na postojeći lokalni `/industrijski-podovi/podovi-za-teretane-i-fitnes-centre/` delovao očigledno. Upitni podatak pokazuje suprotno — klaster je **materijal** („gumeni podovi" 28 impr, poz. 8,8), ne namena, a ciljna stranica prodaje PVC, ne gumu. 301 bi bio tematski promašaj i izgubio bi ceo klaster. Isti alat je 27.07 rešio i „podovi za terase" kanibalizaciju.
- **Provera koja ovo hvata**: HTTP 200 + 1×H1 + prisustvo klase u `el_class` **nije dovoljno** — sve troje je bilo zeleno na ova 3 posta. Jedino Chrome vizuelni pregled (slika se ne vidi) ili direktna provera `SELECT meta_value FROM wpGs_postmeta WHERE post_id={ID} AND meta_key='_wpb_shortcodes_custom_css'` (ili grep na `<style data-type="vc_shortcodes-custom-css">` u renderovanom HTML-u) otkriva problem. **Pravilo: posle svake programske izmene koja unosi WPBakery `css=` atribut, provera mora uključiti i taj postmeta ključ, ne samo status/H1/klasu.**

## Schema može mesecima da „postoji" a da nikad nije emitovana — postmeta/sadržaj nije dokaz (pickleball 16616, 2026-07-28)
- Na `/teren-za-pickleball/` je `kses` pojeo oba `<script type="application/ld+json">` omotača (F7.15 obrazac), pa je stranica istovremeno: (a) prikazivala **5,3 KB sirovog JSON-a kao vidljiv tekst** posetiocu i (b) emitovala **nula** custom scheme. Trajalo je mesecima a niko nije primetio — jer je JSON bio na samom dnu, ispod kontakt-bloka, i jer standardne provere gledaju „ima li schema u sadržaju", ne „šta stranica stvarno emituje".
- **Provera koja ovo hvata** (dodata u verifikacioni skript): iz renderovanog HTML-a izvući sve `<script application/ld+json>` blokove i `json_decode`-ovati ih; zatim iz istog HTML-a skinuti sve tagove i tražiti `@context` / `"@type"` / `acceptedAnswer` u **vidljivom tekstu** — ako se tamo pojave, `<script>` omotač je nestao.
- **Sistemska posledica**: jedan otvoreni „rizik" iz [[PROGRESS]] (izmišljene recenzije kao Product `aggregateRating` na toj stranici) sve vreme uopšte nije bio aktivan, jer Google taj blok nikad nije parsirao. **Pre nego što se pokvarena schema „samo vrati u `<script>`", proveriti šta se tim vraćanjem PRVI PUT aktivira** — u ovom slučaju bi to bile fabrikovane recenzije, cena `0.00 EUR / InStock` na katalog-režim sajtu i `image` koja pokazuje na nepostojeći fajl. Popravka pokvarenog bloka nije neutralna operacija.
