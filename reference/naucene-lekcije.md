---
tip: reference
azurirano: 2026-08-20
---

# Naučene lekcije (tehnički gotchas)

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

## Proveri content freeze status pre publish-a, čak i za ad-hoc katalog rad (2026-08-20)

Codex Onda proizvod je objavljen (status `publish`, ne draft) 20.08 — baš
poslednji dan content freeze prozora (17.08 → ČET 20.08) — bez prethodne
provere `PROGRESS.md` header-a. Materijalni rizik je bio nizak (nov proizvod
van postojeće regression-sweep baseline stranice), ali procesno pravilo
važi bez obzira na procenjeni rizik: pre bilo kog `post_status='publish'`
upisa (ne draft) proveriti freeze prozor u `PROGRESS.md` header-u prvo,
i ako se poklapa sa freeze danima — ili sačekati M potvrdu, ili jasno
upisati u dnevnik da je publish namerno urađen unutar freeze prozora i zašto.

## Uslovni izuzetak u 301 mapi se proverava u bazi, ne u zapisu (2026-08-19)

Pravilo `/podovi-za-garaze/` je 11.08 izostavljeno iz `.htaccess` drafta *jer je taj
URL na buildu bio zauzet živom stranicom*; 18.08 je stranica draftovana i razlog je
pao, pa bi URL posle migracije bio 404. Provera pri svakom sweep-u je zato **upit u
bazu**, ne čitanje beleške: `SELECT ID, post_status FROM wpgs_posts WHERE post_name
IN (...)`. Isto važi obrnuto — vraćanje draftovane stranice u `publish` znači da
pravilo mora **da se isključi**. Svaki izuzetak oblika „ne prenosi se **jer** je X"
pada tiho kad X prestane da važi.

## `cd` u Bash alatu traje kroz pozive (2026-08-19)

Radni direktorijum se **pamti između poziva** Bash alata. Posle
`cd migracija/alati && php skripta.php`, sledeći poziv sa relativnom putanjom
(`grep ... migracija/htaccess-301-DRAFT.txt`) prijavljuje „No such file or
directory" iako fajl postoji — traži ga iz `migracija/alati/`. Simptom liči na
obrisan fajl, uzrok je cwd. Pravilo: apsolutne putanje, ili `cd` vraćati u istoj
komandi.

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

## `curl` u `while read` petlji vraća lažni `HTTP 000` (2026-08-19)

`while read -r u; do curl ... "$u"; done < lista.txt` — **curl čita isti stdin** i pojede
ostatak liste; rezultat je `000` na svim URL-ovima, identično kao kad je Apache ugašen.
Fix: `curl ... < /dev/null` unutar petlje.

Druga, nezavisna varijanta iste zablude: **Python `io.open(path,'w')` bez `newline='
'`**
na Windows-u upiše CRLF, pa `` završi **unutar URL-a** → opet `000`. Fix: `newline='
'`
pri pisanju ili `tr -d ''` pri čitanju.

🔴 Pravilo: pre nego što `000` protumačiš kao pad servera, pozovi **jedan** URL direktno.
17.08 je ista poruka značila ugašen Apache, 19.08 dve potpuno druge stvari — simptom je
isti, uzrok nije.

## Sopstvena beleška se čita do kraja pre nego što se potroši novac (2026-08-18)

Memorijska beleška je jasno govorila: Gemini free tier **ne pokriva** generisanje slika,
`limit: 0`, mora se uključiti naplaćivanje. Kad je prvi API poziv prošao, zaključio sam
„beleška je zastarela" — a tačan zaključak je bio obrnut: **prošao je zato što naplaćivanje
jeste uključeno**, dakle svaka slika se plaća. Odatle je otišlo 8 poziva bez pitanja
(~0,04 USD po slici).

Pravilo: kad rezultat protivreči sopstvenoj belešci, prvo proveri da li beleška zapravo
**objašnjava** rezultat, pa tek onda da je pogrešna. I: poziv koji troši tuđi novac traži
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
average_rating, total_sales, tax_status, tax_class`).

## Rank Math kešira sitemap kao FAJLOVE, ne kao opciju (2026-08-18)

Posle dodavanja dva proizvoda sitemap je i dalje vraćao `lastmod` od pre pet dana. Brisanje
opcije `rank_math_sitemap_cache_files` i svih `_transient_*sitemap*` **nije promenilo ništa**
— pravi keš su XML fajlovi u `wp-content/uploads/rank-math/*.xml`. Tek `rm *.xml` iz tog
foldera natera regeneraciju.

Isti obrazac kao „trebalo je `wp rewrite flush`" (§7.1): kad izlaz ne prati bazu, keš je
najčešće na disku, ne u `wp_options`.

## SVG nije alat za dijagram koji mora da radi na telefonu (2026-08-18)

Dijagram skale otpora crtan kao SVG 1000×420 sa tekstom od 13–15 px izgleda odlično na
desktopu, a na 390 px širine natpisi padnu na ~5 px. **Uzrok je strukturni:** u SVG-u se
tekst skalira zajedno sa slikom, pa povećanje fonta ne pomaže dok je oblik široka
horizontalna traka.

Isti sadržaj kao HTML/CSS grid (komponenta `.al-scale`) rešava sve odjednom: font ostaje
u px bez obzira na širinu, kolone se prelamaju u jednu, tekst se selektuje, indeksira i
čita čitačem ekrana. **SVG čuvati za ono što jeste crtež** (presek poda, šema uzemljenja),
ne za tabelarni sadržaj koji samo izgleda kao grafika.

Uz to: WoodMart reset spljošti `<sup>` na baseline, pa `10⁴` bude renderovano kao `104` —
traži `vertical-align: super` u sopstvenom pravilu.

## Diff po rečenicama lažno prijavljuje da sadržaj nedostaje (2026-08-18)

Poređenje live i build stranice po rečenicama dalo je „42 od 52 rečenice sa live-a ne
postoje na buildu", iz čega je izvedeno osam nepostojećih rupa. Build je u stvari live
tekst **prepisao u tečnije pasuse**, pa se nijedna rečenica nije poklopila doslovno iako
je značenje preneto. Stvarnih rupa bilo je pet.

Parity se meri **po temama i entitetima** (pominje li stranica ATEX, zoniranje bojama,
uzemljenje na 80 m², referentne klijente), ne po podudaranju stringova. Pre prijave nalaza
pročitati puni sadržaj obe strane — automatski diff je ovde alat za sumnju, ne za zaključak.

## Neuspeo heredoc upisuje sopstvenu komandu u ciljni fajl (2026-08-18)

`cat >> fajl.css <<'CSS'` koji se ne zatvori kako treba završi tako što u CSS upiše
doslovnu liniju `grep -c "…" antas-design.css`. Bash prijavi samo `warning`, izlazni kod je
0, a nevalidan red u CSS-u može oboriti parsiranje pravila ispod njega.

Posle svakog `>>` na fajl koji ide u produkciju — `tail` na fajl. I: heredoc sa dugačkim
sadržajem i navodnicima radije zameniti `Write` alatom.

## Protivrečnost slike i natpisa se ne vidi iz HTML provere (2026-08-18)

Fotografija sa Ecotile ESD stranice, imenovana kao „X-Joint ploča", zapravo prikazuje
**pribor za uzemljenje** (bakarna traka, priključak, kabl). Postavljena je kao glavna slika
antistatik proizvoda — onog koji se **ne uzemljuje** — i kao prva kartica ispod natpisa
„bez uzemljenja". Sve provere su bile zelene: HTTP 200, 1×H1, `srcset` radi, alt postoji.

Vizuelni sadržaj traži vizuelnu proveru. Posle svakog postavljanja slika otvoriti stranicu
u brauzeru i pogledati je — naročito kad natpis tvrdi nešto što slika treba da potvrdi.

## Ispravka na dnu append-only loga ne poništava tvrdnju sa vrha (2026-08-18)

`ADS-DNEVNIK.md` je 12.08 dobio uredno napisanu ispravku: „kumulativ 26 / prag pređen
NE VAŽI, pravih lidova ima 9". Ispravka je bila tačna, datirana i na pravom mestu — u
Log sekciji. Ipak je hub **šest dana** nastavio da se čita kao da je prag pređen, jer
zaglavlje fajla i stariji unosi (11.08, 06.08, 30.07) i dalje tvrde suprotno, a ispravka
je jedan blok među desetinama.

**Pravilo:** kad ispravka poništava **stanje** (a ne samo jedan istorijski unos), mora i
na **vrh** dokumenta — kao `[!warning]` blok ispod zaglavlja. Append-only log ostaje
netaknut (istorija se ne prepravlja), ali trenutno stanje ne sme da se rekonstruiše
čitanjem 40 redova unazad. Isti obrazac je 13.08 već jednom ugrizao (`DNEVNIK-NAPRETKA`
unos završio na dnu newest-on-top fajla → bio nevidljiv).

---

## Zastareli rokovi: živi dokumenti se ispravljaju, datirani se ne diraju (2026-08-18)

Pri zatvaranju konflikta „go-live 31.08 vs 25.08" ispalo je da `31.08` i dalje stoji u
6 fajlova. Pet su **datirani** sesijski planovi/promptovi (`2026-07-27-cpanel-sesija-plan.md`
i sl.) — to su istorijski zapisi, tačni za trenutak pisanja, i **ne prepravljaju se**.
Šesti, `w1-novi-proizvodi-court-builder.md`, je **živi red čekanja bez datuma u imenu** —
i baš je on bio jedini pravi problem, jer se čita kao aktuelno uputstvo.

**Pravilo:** kod pomeranja roka grep-ovati stari datum, pa razdvojiti pogotke po tome da
li fajl ima datum u imenu. Bez te podele se ili prepravlja istorija, ili se propušta
jedini fajl koji stvarno vara.

---

## Prazan `post_title` na `nav_menu_item` nije bag (2026-08-18)

Meni stavka 17424 je mesecima vođena kao „nema naslov, prazan red u meniju". U bazi
`post_title` **jeste** prazan — kao i na još 8 stavki istog menija. Ali sve su
`_menu_item_type=post_type`, a tu WordPress pri renderu pada na naslov povezane
stranice: 17424 se prikazuje kao „Podovi za garaže".

**Pravilo:** nalaz iz baze o meni stavkama se potvrđuje **u renderu** (`curl` + grep po
`menu-item-<ID>`) pre nego što se upiše kao zadatak. Ista logika važi za svako polje
koje WP ima kao fallback.

---

## Klaster agregat krije pod-klastere — prilika se vidi tek na query nivou (2026-08-18)
- Simptom: u [[seo/2026-07-27-content-klasteri]] klaster INDUSTRIJSKI stoji kao 1.537 prikaza / 90d — mali, neprioritetan. Pogled na **query nivo** u istom izvoru (`seo/gsc-svi-upiti-16m-2026-07-04.csv`) pokazuje da unutar njega „radionica" varijante (`podovi za radionice`, `pod za radionicu`, `gumeni/pvc/podne obloge za radionice`, `plocice za radionicu`, + „cena" varijante) nose **~4.700 prikaza / ~275 klikova sa poz. 3,5–7 i CTR do 9,8%** — i **nemaju nijednu namensku stranicu**.
- Uzrok: klasterizacija po prioritetnom keyword matchingu svrstava sve „industrijsko" u jednu kantu, pa se pod-intent sa sopstvenim rečnikom („radionica", a ne „industrijski pod") rastvori u proseku. Isti agregat istovremeno skriva i da head-termin `industrijski podovi` (6.321 prikaz) curi sa CTR 2,6%.
- Ovo se dogodilo **drugi put**: revizija „dvorište" (27.07) je oborila preporuku za novu stranicu upravo tako što je sišla na query→page parove i našla tri intenta sa tri različita vlasnika.
- **Pravilo:** pre bilo koje odluke „ovaj klaster nije vredan" ili „napravi novu stranicu za ovaj klaster" — sići na query nivo i grupisati po **rečniku kojim kupac govori**, ne po našoj taksonomiji proizvoda. Agregat služi za redosled rada, ne za odluku o pojedinačnoj stranici.
- Srodno: „Zapisane GSC brojke u migracionim CSV-ovima nisu pouzdane — svež pull pre svake odluke (2026-08-13)".

## `campaign.status` iz Ads API-ja nije dokaz da kampanja ne troši (2026-08-18)
- Simptom: kampanja „Podloge za terase i bazene" je 11.08 vraćena kao `campaign_status: PAUSED`, i taj status je ušao u [[migracija/2026-08-11-ads-final-url-audit]] §2.1 kao „od 14 kampanja samo je jedna ENABLED", pa i u [[dnevnik/ADS-DNEVNIK]] i [[PROGRESS]]. Sedam dana kasnije (18.08) `ads_report.py` je pokazao da je **istog tog 11.08** potrošila 222 RSD / 14 klikova, a 17.08 čak 1.643 RSD / 74 klika — najveći dan u nalogu.
- Uzrok nije bug u skripti: API je vratio tačno ono što je bilo u nalogu. Ispod PAUSED kampanje `ad_group_status` i `ad_status` su oba bila **ENABLED**, a isporuka je isprekidana (dani sa nulom se smenjuju sa danima od 900–1.600 RSD) — dakle status na nivou kampanje ne opisuje šta se stvarno servira.
- Posledica: cela nedelja analiza je kampanju tretirala kao nepostojeću — nije ušla ni u nedeljni izveštaj, ni u URL audit kao rizik, ni u razmatranje budžeta, iako ima **najjeftiniji CPC u nalogu** (20,96 vs 94,41 RSD) i donela je 3 od 5 uvezenih konverzija u nedelji 04–10.08.
- **Pravilo:** status i potrošnja se čitaju **zajedno**, i to **po danu**, ne samo za ceo period. Nijedna kampanja se ne isključuje iz analize na osnovu `campaign.status` — isključuje se tek kad `spend + impressions = 0` kroz ceo posmatrani period. Isti princip već postoji u ovom fajlu za obrnut slučaj („prazan odgovor za kampanju ne znači grešku konektora — proveri spend+impressions"); ovde važi u drugom smeru.
- Srodno: [[reference/naucene-lekcije]] „Odluka se donosi nad bazom, ne nad zapisom u vault-u (2026-08-17)" — isti obrazac, drugi izvor istine.

## Odluka se donosi nad bazom, ne nad zapisom u vault-u (2026-08-17)

M odluka „14 proizvoda bez fotografije → draft" bila je pisana nad spiskom iz
[[PROGRESS]] poslednji put ažuriranim **30.07**. Provera baze pred izvršenje:
spisak nabraja **13** ID-eva (ne 14), a **7 ih je u međuvremenu dobilo sliku** —
06. i 07.08, pri čemu je 6 od tih 7 rešeno **eksplicitnim M odobrenjem** generičkih
dobavljačkih fotografija. Doslovno izvršenje bi ugasilo 7 ispravnih proizvoda.

Isti obrazac kao sistemski nalaz od 13.08 („zapisane GSC brojke tri puta netačne
→ pre svake odluke ide svež pull"). **Pravilo se proširuje sa brojki na spiskove
ID-eva:** pre izvršenja bilo koje bulk izmene (draft, brisanje, prevezivanje),
regeneriši spisak upitom nad bazom i uporedi sa zapisom — razlika je nalaz, ne šum.

Prateće pravilo: **draft proizvoda sa internim linkovima traži sanaciju hub
stranice u istom koraku**, inače se 404 pojavi tek u sledećem regression sweep-u.
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
`grep -rn "klasa" wp-content/themes --include=*.css --include=*.php`.

## Reference koje agent čita kao autoritet ustaju tiho (2026-08-14)

`reference/identifikatori.md` je 14.08 zatečen sa datumom 27.07 i **tri od pet
tvrdnji o lokalnom okruženju netačne**:

| Tvrdnja | Pisalo | Stvarno |
|---|---|---|
| Broj tabela | 106 | **78** |
| Tema/builder | Porto + WPBakery | **WoodMart 8.5.4 + child** |
| SEO plugin | Yoast | **Rank Math** (Yoast obrisan 13.08) |
| Prefiks baze | `wpGs_` | **`wpgs_`** |

Nijedna izmena stack-a (tema u julu, Rank Math 05.08, brisanje Yoasta 13.08)
nije povukla ažuriranje ove reference. Isti razred problema kao `CLAUDE.md` §2,
koji je do 12.08 tvrdio pogrešan prefiks.

🔴 **Zašto je opasno:** za razliku od koda, ustajala referenca ne baca grešku —
agent je pročita, poveruje joj i **radi po njoj**. Pogrešan prefiks iz `CLAUDE.md`
je 13.08 umalo doveo do upisa u pogrešan meta ključ na 13 arhiva.

**Pravilo:** pri svakoj većoj promeni stack-a (tema, SEO plugin, prefiks, PHP,
verzija baze) osvežiti `reference/identifikatori.md` i `CLAUDE.md` §2 —
**provereno protiv sistema, ne iz sećanja**:

```sql
SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='antasline_local';
SELECT option_name, option_value FROM wpgs_options WHERE option_name IN ('template','stylesheet');
SELECT option_value FROM wpgs_options WHERE option_name='active_plugins';
```
(`wp eval` na ovom buildu pada na 300s timeout — koristiti direktan SQL.)
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
v. [[dnevnik/2026-08-14-copilot-grok-delegati]] · [[CLAUDE]] §2

## Cena u izlazu alata nije račun — prvo se proveri način autentifikacije (2026-08-14)

Grok je na trivijalan poziv vratio `total_cost_usd: 0.060434` i ja sam iz toga
zaključio „naplaćuje se ~$0,06 po pozivu" — upisao to u skill, u blokere i u
preporuku da se alat štedi **zbog novca**. M je rekao da nema nikakvu pretplatu.
Provera je pokazala da je bio u pravu: `XAI_API_KEY` **nije postavljen**
(autentifikacija je OAuth/OIDC), a log posle stvarnih poziva ponavlja
`subscription_tier: null` · `paywall_check_no_subscription` · `tier: "Free"`.

Broj je bio **očitavanje brojila po API cenovniku** — koliko bi ti tokeni koštali
preko plaćenog ključa. Grok dokumentacija to i kaže (`14-headless-mode.md`):
cena se štancuje za API-key saobraćaj, dok OAuth putanja obično ne nosi stvarnu
cenu. Na Free nalogu se troši kvota; kad se potroši → **odbijanje, ne račun**.

**Pravilo:** pre nego što se bilo čija potrošnja proglasi novcem, proveriti
**čime je autentifikovan** (`env` ključ vs OAuth) i **šta server prijavljuje o
tieru**. Polje `cost` u izlazu alata je metrika, ne faktura. Obrnuto takođe važi:
odsustvo cene ne znači besplatno.

🟢 Deo zaključka koji je preživeo: **token-težina je stvarna** (~23k ulaznih
tokena po grok pozivu, jer učita `CLAUDE.md` + `AGENTS.md` svaki put). Alat se
i dalje štedi — samo je razlog kvota, ne novac.
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
sanacija je rotacija (`logout` → `login`), ne brisanje ispisa.

## Delegat-agent ume da vrati uredan izveštaj nad nula fajlova (2026-08-14)

Copilot je na prvom `wpgs` auditu krenuo od glob obrasca sa tri ekstenzije
odjednom, dobio 0 rezultata i vratio **formatiranu, samouverenu tabelu**
„nema nalaza · pregledano 0 fajlova" — nad folderom od **89 fajlova**. Nijedna
greška, nijedno upozorenje. Ovo je najopasniji način otkaza jer izgleda kao uspeh.

**Obavezno u svakom prompt šablonu za delegata:**
1. „Prvo izlistaj ceo folder, pa TEK ONDA filtriraj po ekstenziji."
2. „Ako pretraga vrati 0 fajlova, to je greška TVOJE pretrage, ne prazan folder."
3. „U rezimeu navedi koliko si fajlova STVARNO otvorio."

**I posle toga:** tvrdnja o pokrivenosti se proverava nezavisnim grep-om, ne
prima na reč. (Drugi pokušaj: 3 NALAZ, sva tri tačna — ali je alat prijavio
20 otvorenih fajlova od 83 relevantna; pokrivenost je spasao grep koji je pustio
usput, ne otvaranje fajlova.)
v. [[dnevnik/2026-08-14-copilot-grok-delegati]]

## Zabrane za delegat-agente: dokumentacija nije dokaz, `inspect` jeste (2026-08-14)

Grok 1.0.3 **nađe** projektni `.grok/config.toml` i **ne primeni** dozvole iz
njega — `grok inspect` javi `Permissions: Source: (none), 0 loaded`, iako
dokumentacija izričito tvrdi da projektni opseg radi. Testirane obe forme
(`deny = [...]` i `rules = [{ action, tool }]`). Ista pravila u
`~/.grok/config.toml` → **19 loaded, 0 skipped**.

Isto važi za sandbox: `--sandbox` je Landlock/Seatbelt, dakle **Linux/macOS**.
Na Windows-u grok samo upiše upozorenje u log i **nastavi bez zaštite** — flag
koji izgleda kao zaštita, a nije.

**Pravilo:** svaka ograda za delegata se posle podešavanja **testira živo**
(traži od agenta da uradi zabranjenu stvar), a ne zaključuje iz dokumentacije.
Copilot je pri istom testu **pokušao pa bio blokiran** (mehanizam), Grok je
odustao na instrukciji iz `AGENTS.md` — što znači da mu `deny` sloj tada nije
ni bio isproban.

Uzgred, ista klasa: `.claude/settings.json` **čita i Grok**, ne samo Claude Code
— pravila za delegate tamo procure na alat koji sme da piše.
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
v. [[dnevnik/2026-08-13-5438-basket-semantika-faqpage]]

## Provera koja meri pogrešnu stvar je gora od nikakve provere (2026-08-13)

Dva puta u istoj sesiji je „crveno" došlo od merenja, ne od stranice:
`[al_skica]` je proveravan tražeći ime skice u renderu — a shortcode emituje
`<div class="al-skica-wrap">` + **inline** `<svg>`, ime se nigde ne pojavljuje; a kartice
su na 1440 px izmerene kao 67 px široke sa 11 odsečenih naslova, jer je iframe **prvo
učitan na 390 px pa proširen**, pa je layout ostao ustajao. Sveže učitavanje na 1440 daje
grid `280px ×4`.

Pravilo: pre nego što se prijavi regresija, proveriti da li provera meri ono što misli da
meri — i responsive merenja raditi **svežim učitavanjem na ciljanoj širini**, ne resize-om
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
- Isto važi za **stavke menija** (`_menu_item_object_id`) i dolazne linkove u `post_content`.

## Advanced Tables tiho naduva markdown fajl 3–4× razmacima (2026-08-13)
- Plugin `table-editor-obsidian` poravnava kolone **dopunom razmacima do širine najšire
  ćelije**. U tabeli sa ćelijama od 1–4 hiljade znakova to znači da se **svaki** red
  dopuni na tu širinu. PROGRESS.md je tako narastao na **1,4 MB, od čega 1,06 MB razmaka
  (75%)** — jedan niz razmaka bio je 4.209 uzastopnih.
- Šteta nije kozmetička: fajl koji CLAUDE.md §13 nalaže da se čita **prvi na svakoj
  sesiji** prestao je da može da se otvori Read alatom (106k tokena vs limit 25k), pa se
  čitao parcijalno kroz grep — isti razred greške koji je 12.08 doveo do pogrešnog izbora
  zadatka.
- Skidanje dopune: `sed -E '/^\|/ { s/[[:space:]]+\|/ |/g; s/\|[[:space:]]+/| /g }'` —
  **ograničeno na linije koje počinju pipe-om**, inače pokvari proznu rečenicu sa `|` u
  inline kodu (`cat part-* | tar -xzf -`).
- Dokaz da je promenjen samo razmak: `tr -d ' ' < fajl | md5sum` mora biti **isti** pre i
  posle. Za premeštanje redova između fajlova: `tr -d ' ' | sort | md5sum` nad unijom.
- Trajno gašenje: `formatType` → `"weak"` u `data.json`. 🔴 Ako je Obsidian pokrenut,
  izmena važi tek posle restarta, a dodirivanje podešavanja tog plugina u UI-ju vrati
  in-memory `normal` nazad u fajl.

## Literalan `|` u ćeliji markdown tabele lomi red, i to nevidljivo (2026-08-13)
- `^(llms(-full)?|robots)\.txt$` unutar inline koda u ćeliji: Obsidian tretira pipe kao
  granicu ćelije i prelomi ostatak teksta u fantomsku kolonu. Inline kod **ne štiti** od
  toga — escape mora biti `\|`.
- Otkriva se sa `awk -F'|' 'NF!=ocekivano {print NR}'`, ali pazi na **lažni pozitiv**:
  već ispravno escape-ovan `\|` i dalje sadrži pipe znak, pa ga brojač prijavi kao grešku.
  Pre prijave pogledati da li ispred pipe-a stoji `\`.

## Zamena slug-a ide `$wpdb->update`-om, i to određenim redosledom (2026-08-13)
- `wp_update_post()` prolazi kroz `wp_unique_post_slug()`, koji ako zatekne slug zauzet
  drugim postom **tiho vrati `-2` nazad**. Kod zadatka „skini `-2` sa slug-a" to znači da
  skripta prijavi uspeh, a stanje ostane nepromenjeno. Direktan `$wpdb->update` zaobilazi
  tu logiku u celosti (uz `clean_post_cache()` posle).
- Redosled je obavezan: **prvo stari post pusti slug** (preimenovanje + `draft`), pa ga tek
  onda novi uzme. Obrnuto daje dva posta sa istim `post_name`.
- Provera preduslova na početku skripte (jesu li slugovi u očekivanom polaznom stanju →
  ako nisu, izlaz bez izmena) čini je bezbednom za ponovno pokretanje.

## Rank Math sitemap keš ne zna za direktan SQL upis (2026-08-13)
- Posle zamene slug-a preko `$wpdb->update`, `post-sitemap.xml` je i dalje servirao **stari**
  URL — WP hook-ovi na kojima Rank Math visi ne okidaju pri direktnom upisu u bazu.
- Rušenje keša: `\RankMath\Sitemap\Cache::invalidate_storage()` (kroz `php -r` sa
  `wp-load.php`). Važi za svaku izmenu slug-a, statusa ili `noindex`-a izvedenu direktno.
- Praktična posledica: sitemap se **mora ponovo proveriti** posle takvih izmena, inače u
  migracioni paket ode spisak URL-ova koji više ne postoje.

## `wp plugin delete` nije „obriši fajlove" — briše i podatke (2026-08-13)
- WP-CLI-jev `delete_plugins()` poziva **uninstall rutinu plugina** (`uninstall.php` /
  registrovani uninstall hook) pre brisanja foldera. Ako plugin tu čisti svoje opcije i
  postmeta, „brisanje plugina" postaje i brisanje podataka — bez upozorenja i bez pitanja.
- Kad je cilj „skini fajlove, zadrži mogućnost povratka": arhivirati folder (`tar -czf`),
  pa `rm -rf` folder. Baza ostaje netaknuta i povratak je raspakivanje + aktivacija.
- Provera integriteta arhive ide **pre** brisanja, ne posle: `tar -tzf | wc -l` mora da se
  poklopi sa `find <folder> -type f | wc -l` + `-type d | wc -l`. Posle `rm -rf` nema sa čim
  da se poredi. (Yoast 27.8: 2.308 unosa = 1.855 fajlova + 453 foldera.)

## Pravilo koje nadživi svoju odluku je aktivna mina, ne zastarela beleška (2026-08-13)
- Migracija Yoast→Rank Math izvedena 05.08, ali je „Yoast ostaje (ne RankMath)" ostalo kao
  **tvrdo pravilo** u 7 fajlova — uključujući `/antasline-sesija` i `/obogati-proizvod`, koji
  se učitavaju na početku svake sesije. Isti razred kao pogrešan prefiks `wpGs_` (12.08).
- Šteta nije teorijska: 13.08 je pri upisu meta opisa na 13 arhiva ključ zamalo bio
  `_yoast_wpseo_metadesc`. Upis u ključ mrtvog plugina **ne puca** — samo se tiho ne renderuje.
- **Pravilo: onaj ko izvrši migraciju alata isti dan pretražuje vault za starim pravilom**
  (`grep -rn -i "<stari alat>" --include=*.md` po `odluke/`, `.claude/skills/`, master planu,
  `reference/`) — dnevnički zapis da je migracija urađena nije zamena za brisanje pravila.
- Odluku **prepisati, ne obrisati**: stara ostaje vidljiva kao zamenjena, sa datumom i
  razlogom. Inače se za mesec dana ne zna da li je promena bila namerna.

## Sitewide skok u regression brojkama je prvo pitanje „šta je uklonjeno", ne „šta je pokvareno" (2026-08-13)
- Sweep je pokazao **−118 slika na svakoj stranici** u odnosu na baseline od 3 dana ranije. Prvo čitanje je „nešto je masovno polomljeno".
- Razrešeno **jednom brojkom**: jedinstvenih slika 1.182 → 1.158 (samo −24). Da su slike stvarno nestale, jedinstveni skup bi pao za ~118. Konstantna delta po stranici + skoro nepromenjen jedinstveni skup = **nestao globalni blok** (zaglavlje/podnožje/meni), a slike i dalje postoje na svojim stranicama.
- Uzrok nađen za 2 minuta — **po imenu backup fajla** (`..._pre-uklanjanje-meni-ikonica.sql`), ne u dokumentaciji, jer unosa nije ni bilo. **Pravilo: `ls antasline-backups/` je brži izvor istine o tome šta se juče radilo nego dnevnik**, kad dnevnik zataji.
- Potvrda u bazi je obavezna pre zaključka: `uploads/meni-ikonice/` ima 79 fajlova i 79 priloga, ali **0 referenci** iz `nav_menu_item` (`post_content` + `postmeta`). Fajlovi koji postoje ≠ fajlovi koji se renderuju.

## Ledger unos na dnu fajla je nevidljiv unos (2026-08-13)
- [[DNEVNIK-NAPRETKA]] je newest-on-top. Unos „FAZA 1 — Visual, Assets & Media Cleanup" (13.08) završio je **na dnu fajla**, iza unosa iz juna — posao je bio uredno zapisan, ali ga nijedno otvaranje sesije ne bi videlo, i nije stigao u [[PROGRESS]].
- Isti razred greške kao „Sledeće liste trule tiše od Urađeno" (12.08): dokumentacija koja postoji ali se ne čita jednaka je nepostojećoj.
- **Pravilo pri zatvaranju sesije: posle upisa proveriti `grep -n "^## " DNEVNIK-NAPRETKA.md | head -3` — tvoj unos mora biti prvi.**

## Regression sweep se pušta u fajl, ne kroz `| tail` (2026-08-13)
- `php regression-sweep.php | tail -60` na 239 stranica traje >10 min i **ne ispisuje ništa do kraja** (`tail` bafer) — izgleda kao da je zamrznuto, a background izlazni fajl ostaje na 0 bajtova.
- Pisati `> izlaz.txt` pa čitati fajl, ili pustiti bez `tail`. Isto važi za svaku dugu skriptu u ovom shell-u.

## Ista zamerka na 5 stranica je jedan uzrok, ne pet popravki (2026-08-13)
- M je prijavio 4 odvojene „prevelike praznine" na 4 stranice. Sve četiri su bile isti obrazac: **dve susedne `.al-section` istog tona** (`--paper`+`--paper` ili `--mist`+`--mist`) daju 72+72 = 144px jednobojne trake bez linije ili promene boje koja bi je opravdala. Uz to WPBakery `margin-bottom: 35px` na poslednjem bloku u sekciji i goli `<br>` iz `wpautop` (~18px).
- Popravka po stranici bi rešila 4 prijavljene i ostavila **15 spojeva na 14 stranica** (prebrojano SQL-om nad `post_content`) plus Woo kategorija stranice netaknutim. Popravka u dizajn sistemu rešava sve odjednom.
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
- Uz to (2026-08-12 lekcija o hladnom startu): posle podizanja Apache-a prvi zahtev traje 100s+, pa `curl` pre Chrome merenja.

## Skripta koja se pokreće samo na dan migracije mora imati način da se testira ranije (2026-08-12)
- `live-export.sh` je do 12.08 gubio **145 od 170** galerijskih slika (čitao `post_parent` + `_thumbnail_id`, nikad `_product_image_gallery`). Bag je preživeo mesecima jer se skripta pokreće **jednom, na dan migracije** — dakle prvi put bi se izvršila baš kad je cena greške najveća.
- Fix koji vredi više od samog baga: `PFX`/`OUT` se sada mogu pregaziti iz okruženja (`PFX=wpgs_ OUT=/tmp/t.sql bash live-export.sh`), pa se skripta vrti nad lokalnim buildom. **Tek to pokretanje je otkrilo tri dodatna baga** (ispod) koje čitanje koda nije videlo.
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
$//; /^[[:space:]]*$/d'; }`. Bezopasno na Linux-u.

## Ušteda tokena dolazi od AGREGACIJE, ne od lokalnog modela — pravila su pobedila qwen3 za 1300× (2026-08-12)
- Cilj je bio da lokalni Ollama model razvrstava GSC/Ads/GA4 podatke umesto mene. Izmereno na pravom izvozu (400 upita, 37 KB): **regex pravila razvrstala 93,6% prikaza za 0,2 s**, `qwen3:4b` dodao +2% za **475 s** uz ~50% grešaka na spornim upitima (`table za kos` → vinil, `tartan kocke` → veštačka trava).
- 🔴 Stvarna ušteda je bila u **sažimanju**: 37 KB sirovog JSON-a (~12k tokena) → 3,5 KB izveštaja (~700 tokena). To radi obična Python agregacija, bez ijednog poziva modela.
- Prava uloga malog lokalnog modela je **rudarenje pravila jednom**, ne klasifikacija svaki put — predlog korena reči se upiše u kod i od tada je besplatan zauvek. Skill: `[[ollama-lokalni]]`.
- Pouka šire: pre nego što se na problem baci model, proveriti da li je problem uopšte semantički. Ovaj nije bio.

## Ollama 0.18 sam bira `num_ctx` iz maksimuma modela — model od 3B traži 15 GiB i „ne staje u RAM" (2026-08-12)
- `llama3.2:3b` (2 GB) je pukao sa `model requires more system memory (15.3 GiB) than is available`. Izgleda kao premali RAM — nije. Ollama je uzeo modelov maksimalni kontekst (qwen3 = 262k) kao podrazumevani.
- Fix: **uvek eksplicitno slati `options.num_ctx`** (kod nas 8192). Sa tim isti model radi na ~15 tok/s.
- Druga polovina iste zamke: `num_predict` prekratak → odsečen izlaz → neispravan JSON → ceo poziv (minuti CPU vremena) bačen. `qwen3:4b` probije 2048 tokena već na 60 upita.
- Hardver ove mašine (i5-11320H, 15,7 GB RAM, MX450 2 GB): `qwen3:4b` je jedini praktično upotrebljiv, `qwen3:8b` >10 min po pozivu, **`qwen3:30b` (18 GB) uopšte ne staje u RAM**.

## `python skripta.py | Out-File -Encoding utf8` u PowerShell-u duplo enkoduje — ćirilica postane `ĐşĐľŃĐ°Ń€` (2026-08-12)
- PowerShell dekodira Python-ov UTF-8 izlaz kao cp1252, pa ga ponovo enkoduje kao UTF-8. GSC vraća i ćirilične upite („кошаркашки терен"), koji tako postanu smeće — a JSON ostane sintaksno validan, pa ništa ne pukne.
- Fix pre svakog pokretanja: `$env:PYTHONIOENCODING="utf-8"` + `[Console]::OutputEncoding=[System.Text.Encoding]::UTF8`, i `Set-Content -Encoding utf8` umesto `Out-File`.
- Srodno sa ranijom lekcijom „Python `print` na Windows-u puca čim ćirilični izlaz ode u fajl (2026-08-11)" — ista klasa, drugi ishod: tamo pukne, ovde **tiho pokvari podatke**.

## Klasifikacija srpskih upita: koren reči koji zvuči očigledno ume da pojede tuđu kategoriju (2026-08-12)
- `odbojn` hvata i **odbojku** (sport) i **odbojnike za zid** (industrija) — rešeno sa `odbojnic` za industriju uz `odbojk` za sport.
- `odbojnik` ne hvata množinu `odbojnici` (58 prikaza je tiho padalo u „ostalo"). Isto: `industriski` bez `j`, `bastu` vs `basten`, `epox` vs `epoksid`.
- Ćirilicu treba **transliterisati pre poređenja**, inače latinična pravila ne vide ništa.
- Kad `ostalo` pređe ~10% prikaza, to je signal da **fale pravila**, ne da treba jači model.

## Delegirani agent (`agy`) bez apsolutnih putanja krene da pretražuje ceo `C:\Users\` — kvota ode na lutanje (2026-08-12)
- Prompt je rekao „foldere `dnevnik/` i `migracija/` ovog vault-a". `agy` je pokrenuo `Get-ChildItem -Path C:\Users\Miroslav -Directory -Filter "dnevnik" -Recurse -Depth 4` — dakle **tražio je folder umesto da ode na njega**. Potvrđeno u `~/.gemini/antigravity-cli/log/`.
- 🔴 Delegirani agent **ne nasleđuje tvoj radni direktorijum kao kontekst** ni kad je pokrenut iz njega. Prompt mora dati **pune apsolutne putanje + broj fajlova** i **izričito zabraniti** pretragu van njih („NE pretražuj C:\Users\Miroslav").
- Isto pravilo važi za bilo koji spoljni agent/CLI, ne samo `agy`.

## Headless agent koji ne može da pita za dozvolu — sam sebi odbije alat i vrati prazan izlaz (2026-08-12)
- `agy -p` (print mode) je vratio `no output produced — a tool required the "command" permission that headless mode cannot prompt for`. Izlazni fajl **0 bajtova**, exit bez greške — lako se pročita kao „model nije uspeo", a zapravo nikad nije ni pokušao.
- Dijagnoza je **isključivo u logu**: `~/.gemini/antigravity-cli/log/cli-*.log` → `permission check failed for command "..."`. Log pokaže i **tačnu komandu** koja je odbijena, što odmah otkriva i lutanje iz prethodne lekcije.
- Rešenje: `permissions.allow` u `~/.gemini/antigravity-cli/settings.json`. **Sintaksa `command(Get-ChildItem)` je potvrđeno ispravna** — posle nje je odbijanje prešlo na sledeći alat (`read_file`), što je dokaz da je prva kapija prošla.
- 🔴 `--dangerously-skip-permissions` **ne koristiti** (i Claude Code harness ga blokira): to je „odobri sve" nad vault-om pred migraciju. Fallback koji uvek radi: **TUI**, gde agent pita a čovek odobri.

## Isti model kroz TUI ispiše rezultat dvaput, a „lepa" tabela se raspadne posle ~10 redova (2026-08-12)
- Izlaz od 404 linije izgledao je kao da je posao pukao na pola („quota reached"). Nije: **TUI redraw ispiše ceo rezultat dvaput**, a obe kopije se završavaju istom stavkom. Provera potpunosti = uporediti poslednju stavku obe polovine, ne gledati dužinu.
- 🟡 **Druga kopija ume biti potpunija** od prve (imala je rečenicu o suspendovanom FTP nalogu koje u prvoj nema) — čistiti od druge, ne od prve.
- ASCII-uokvirena tabela se **prelomi posle ~10 redova** u sirov markdown sa polepljenim rečima (`pokrenutiauthorize_oauth.py`, `Izvršiti\RankMath\Sitemap\Cache::invalidate_storage()preko`). Sadržaj ostaje čitljiv, formatiranje ne. **Tražiti običnu markdown pipe-tabelu**, izričito zabraniti „lepu" tabelu.

## Audit koji „nalazi" probleme može samo da re-otkriva ono što već stoji u `naucene-lekcije` (2026-08-12)
- `agy` pre-flight checklist je izgledao kao serija novih otkrića (mu-plugins blokira mejlove, `*.bak-*` servira izvorni kod, 62 Redirection pravila nestaju sa bazom, OAuth pada na 7 dana, `wpgs_` vs `wpGs_`). **Svih pet su već bile zapisane lekcije** (2026-08-06 do 2026-08-11).
- 🔴 Pre nego što se nalaz spoljnog alata proglasi otkrićem — **grep-ovati `reference/naucene-lekcije` i `PROGRESS`**. Precenjen alat vodi ka pogrešnoj podeli posla.
- 🟢 Stvarna vrednost je bila realna, samo drugačija: **konsolidacija razbacanih lekcija u jedan izvršiv dan-migracije checklist + izvlačenje konflikata između fajlova**. To je posao koji niko nije uradio jer je dosadan i obiman, ne zato što je težak.

## Lekcija zapisana u `naucene-lekcije` ne znači da je `CLAUDE.md` ispravljen — prefiks baze je preživeo 6 dana (2026-08-12)
- Lekcija „prefiks je `wpgs_`, ne `wpGs_`" postoji od **2026-08-06**. `CLAUDE.md` §2 i §7.5 i dalje tvrde **`wpGs_`** — a `CLAUDE.md` je fajl koji svaki agent učitava kao autoritet, dok `naucene-lekcije` čita samo kad ga neko pošalje.
- 🔴 **Zapisati lekciju nije isto što i zatvoriti je.** Ako lekcija protivreči `CLAUDE.md`, `PROGRESS`-u ili nekom skillu, ispravka tih fajlova je **deo lekcije**, ne poseban zadatak. Inače greška živi dalje kroz svaku novu skriptu koja se piše „po dokumentaciji".
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
- **Pravilo: pre nego što se zaključi „te slike nemamo", grep-uj backup-e po imenu pojma**, pa skini original sa live-a i uvezi kroz `wp media import` (ne ručnim `cp` u `uploads` — inače nema priloga, ni alt teksta, ni generisanih veličina).

## Alt tekst se broji po KANALU RENDEROVANJA, ne po medijateci — razlika je 66 vs 6.638 (2026-08-12)
- Medijateka ima **7.725 slika, 6.638 bez alta**. Zvuči kao nedeljni posao. Stvarnih slika koje se **renderuju** a nemaju alt bilo je **66**.
- Razlog: WordPress registruje svaku generisanu veličinu kao zaseban prilog (Porto-era artefakt), plus godinama nagomilani neupotrebljeni uploadi. Nijedno od toga korisnik nikad ne vidi.
- **Metod: broji se po kanalu kroz koji slika stiže na stranicu** — `_thumbnail_id` proizvoda · `_product_image_gallery` · `<img>` u `post_content`. Sve ostalo u medijateci je šum.
- 🟢 **Prazan alt je često TAČAN odgovor, ne bag.** Od 225 nalaza, **159 su bile dekorativne SVG ikonice** (`montaza.svg`, `odrzavanje.svg`, `izdrzljivost.svg`…) uz tekst koji ih već imenuje — `alt=""` je za njih ispravno po WCAG. Popunjavanje bi bilo **regresija** pristupačnosti (v. lekciju od 2026-08-05). Uvek prvo grupisati nalaze **po imenu fajla**: ponavljanje istog `.svg` 25–28 puta je najbrži signal da je reč o ikonici, ne o fotografiji.
- 🔴 **Jedan prilog = jedan alt, bez obzira na broj galerija u kojima stoji.** Dva priloga su bila u 2 odn. 3 galerije — njima alt **ne sme** biti naslov jednog proizvoda, mora biti neutralan opis onoga što se vidi. Skripta zato ima tvrdu proveru: deljeni prilog bez neutralnog opisa = prekid bez upisa, ne tiho pisanje pogrešnog teksta.
- Brojke iz starijeg plana (07-30: „67/81 proizvoda") bile su **zastarele za 13 dana** — obogaćivanje proizvoda ih je usput popunilo. **Obim se meri neposredno pre izvršenja, nikad ne prepisuje iz plana.**

## „Sledeće" liste truli tiše od „Urađeno" — zatvaranje zadatka mora ažurirati OBA mesta (2026-08-12)
- Predložen je (i prihvaćen) zadatak „W1 Polish Faza 4 — GEO-intro na 22 posta", koji je **bio zatvoren 5 dana ranije (2026-08-07, 22/22)**. Isto i master plan 1.2: stajao je na „12/33, sledeći kancelarije/padel" dok je red čekanja bio **33/33 od 2026-07-08** — zastareo mesec dana.
- Uzrok obrasca: sesija se zatvara upisom u **Urađeno** tabelu i u fajl reda čekanja, a red u **„Sledeće"** i statusna ćelija u master planu ostanu kako su bili. Urađeno raste i vidi se; „Sledeće" niko ne čita dok ne zatreba — a tada je pogrešno.
- 🔴 Cena nije samo izgubljeno vreme: pogrešan predlog deluje **verodostojno** jer dolazi iz zvaničnog izvora istine, pa se prihvati bez provere.
- **Pravilo: pri zatvaranju zadatka obavezno obrisati/preškrabati i njegov red u „Sledeće" i statusnu ćeliju u [[2026-07-06-MASTER-PLAN-V2]], ne samo dodati red u Urađeno.** A pri izboru zadatka na početku sesije: **status iz „Sledeće" se pre predlaganja proverava u fajlu reda čekanja** (jedan `grep`), nikad ne uzima zdravo za gotovo.
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
- **Pravilo za nov markup: nikad `border: 1px solid` bez boje.** Boja ivice se deklariše uvek, i u temi i u sadržaju.

## Browser automatizacija nad Google alatima: prvo proveri KOJI je nalog aktivan (2026-08-12)
- Otvaranje GSC-a preko Chrome automatizacije vratilo je „Упс, немате приступ овом производу" — Chrome je bio prijavljen na **`cpgujam@gmail.com`**, a property je pod `miroslav.markovic109@gmail.com`.
- 🔴 Opasnost je u pogrešnom zaključku: ta poruka lako se pročita kao „izveštaj/property nije dostupan" i završi u dnevniku kao nalaz, umesto kao pogrešan nalog. Isti obrazac važi za GA4, Ads i GMB UI.
- Rešenje: oba naloga su već bila prijavljena → prebacivanje kroz avatar meni, bez ikakvog unosa lozinke. **URL posle prebacivanja nosi `/u/1/`** — to je i najbrža provera da si na pravom nalogu.
- **Pravilo: pre bilo kakvog čitanja podataka iz Google UI-ja kroz browser, potvrdi aktivni nalog** (avatar ili `/u/N/` u URL-u), pa tek onda tumači ono što stranica prikazuje.
- Usput: direktan URL `search.google.com/search-console/generative-ai` je **404**. Tačan put je `/performance/search-analytics/ai`, ili banner „Open report" na Performance strani. Ne izmišljati URL-ove Google konzola po analogiji — otvarati kroz navigaciju.

## Podešavanje koje tiho gasi ceo kanal, a ne ostavlja nikakav trag u podacima (2026-08-12)
- Search Console ima **Settings → Search generative AI** (Include / Exclude / Inherit, podrazumevano *Include*) — određuje da li sadržaj sme u AI Overviews / AI Mode / Discover AI.
- Opasnost nije u podešavanju nego u njegovoj **nevidljivosti**: isključivanje **ne dira ni rangiranje ni indeksiranje**, pa se u GSC izveštajima, GA4-u i rankingu ne bi videlo ništa. Sav GEO rad bi bio bez efekta na Google strani, a dijagnostika bi tražila uzrok u sadržaju.
- Provereno 2026-08-12: kod nas stoji na „Include". Trošak provere — jedan klik.
- **Pravilo: kad kanal zavisi od on/off prekidača kod trećeg lica, prekidač se verifikuje pre nego što se u kanal uloži rad, i upisuje se u dokumentaciju sa datumom provere.** Isti obrazac već postoji u ovom projektu: `noindex` na stranici, `include_in_conversions_metric` na Ads akciji, `tax_*_sitemap` ključevi posle Rank Math importa — sve „tihi prekidači" koji su nas već koštali vremena.

## Konvencija koju niko zvanično nije potvrdio ≠ standard — `llms.txt` je bio nagađanje industrije (2026-08-12)
- `llms.txt` + `llms-full.txt` su napravljeni i deployovani na live (23.07) kao deo „GEO paketa", uz ogradu u [[seo/geo-ai-plan]] da adoptacija „nije zvanično potvrđena".
- **Naše sopstveno merenje je odgovorilo pre dokumentacije:** [[analiza/BOT-CRAWLER-LOG]] je kroz dva preseka pokazao **0 organskih hitova** — nijedan AI bot nije zatražio fajlove, iako su svi aktivno crawlovali sajt u istim prozorima.
- Tri nedelje kasnije Google to i napismeno potvrđuje: *„Google Search doesn't use them"* — niti štete niti pomažu.
- **Pravilo: kad se uvodi konvencija koju nijedan proizvođač nije zvanično podržao, uz nju ide merenje koje može da je opovrgne, i unapred definisan trenutak odluke.** Ovde je to slučajno urađeno kako treba (bot log je pokrenut dan posle deploy-a) — otud je zatvaranje stavke koštalo jedno čitanje, ne ponovnu raspravu.
- Trošak greške je bio nizak jer je fajl statičan. Ista logika ne važi za konvencije koje traže izmene šablona, schema-e ili build procesa — tamo se čeka potvrda izvora.

## Skill građen iz JEDNOG izvora nasleđuje njegovu grešku — hub i [[PROGRESS]] se razilaze (2026-08-12)
- Novi `/antasline-ads` je pisan iz [[dnevnik/ADS-DNEVNIK]] (251 red istorije, deluje kao autoritativan izvor za Ads) i preuzeo je iz njega „kumulativ 26 plaćenih konverzija, prag 20–30 pređen".
- [[PROGRESS]] Blokeri od **11.08** to demantuju: `Klik na telefon (web)` ima `include_in_conversions_metric=True`, pa je **17 od 26** klik na telefon — pravih plaćenih lidova ima **9**. Ispravka je stigla u Blokere, ali **ne i u ADS-DNEVNIK**, čiji poslednji Log unos (11.08) i dalje tvrdi „prag pređen".
- Uhvaćeno tek pri zatvaranju sesije, kad je protokol naložio čitanje PROGRESS-a. Da nije, skill bi tu brojku ponavljao svaki put kad se pozove — trajno, i sa autoritetom „to piše u skillu".
- **Pravilo: pre nego što se brojka ili zaključak upiše u skill (koji se čita svaki put), ukrstiti izvor sa [[PROGRESS]] Blokerima.** Tematski hub beleži šta je tada izmereno; Blokeri beleže šta je u međuvremenu opovrgnuto. Kad se raziđu, Blokeri su noviji.
- Šire: svaki `#ceka-miroslav` nalaz koji menja raniji zaključak treba **istovremeno** upisati i u tematski hub, ne samo u Blokere.

## Prerender/prefetch okida thank-you stranicu bez posetioca — konverzija se naduva (2026-08-12)
- Chrome 151 uvodi Speculation Rules `form_submission` (prerender odredišta submit-a forme); isto rade i prefetch opcije optimizacionih plugina (LiteSpeed, WP Rocket, Perfmatters).
- Kad se konverzija meri kao **page view na thank-you stranici** — a to je i naš setup i najčešći WooCommerce/CF7 setup — prerender izvrši tu stranicu **pre** nego što posetilac stigne, i tag okine na posetu koja se možda nikad ne dogodi.
- **Redosled je obavezan: prvo GTM trigger gate-ovan na `document.prerendering === false` (ili odložen na `prerenderingchange`), pa tek onda prerender.** Nikad obrnuto.
- Proveriti i pri prelasku na hosting/plugin sa agresivnim prefetch default-om — uključi se bez pitanja i tiho promeni brojke.

## Popravljen tracking bag ostavlja rupu u podacima — zatvaranje stavke mora ostaviti ✅ trag u [[PROGRESS]], ne biti obrisano (2026-08-11)
- `mailto` je bio mrtav **27.06–06.08** (pratio ga MonsterInsights, gašenje MI-ja u BLOK A ga oborilo), dijagnostikovan 27.07, popravljen 07.08 (GTM Version 14). Sve uredno u [[dnevnik/2026-08-07-gtm-mailto-tag]].
- Ali kad je stavka zatvorena, redovi su iz **PROGRESS Blokera obrisani**. Mesečni snapshot (11.08) je istu rupu ponovo otkrio u podacima i prijavio kao **nov, nedijagnostikovan nalaz** — dvaput istraženo, jednom pogrešno prijavljeno Miroslavu.
- Zašto dnevnik fajl nije pomogao: po protokolu (`/antasline-sesija` §1) na otvaranju se čitaju PROGRESS + master plan + ledger; pojedinačni `dnevnik/*.md` se čita **samo na zahtev**. Ako trag nije u ta tri fajla, praktično ne postoji.
- **Pravilo: bag koji je ostavio rupu u istorijskim podacima se ne briše iz Blokera — prepiše se u ✅ red sa TAČNIM datumskim opsegom rupe.** Analiza koja te datume ne zna nužno ih otkriva iznova i tumači kao ponašanje korisnika (npr. „`mailto` pao na nulu") umesto kao prekid merenja.
- Isti obrazac preti i drugim serijama sa poznatim prekidom: `generate_lead` (istorijski rep na `/kontakt/` do BLOK A), Windsor `conversions` kontaminacija 17–22.06, hvala-proxy pre migracije (÷2).

## „Konverzije" u Google Ads-u nisu ono što misliš dok ne pogledaš `conversion_action` podešavanja (2026-08-11)
- Kolona **„Conversions"** (i signal koji Smart Bidding uči) sadrži **samo** akcije sa `include_in_conversions_metric = True`. Kolona „All conversions" sadrži sve. Ime akcije ne govori ništa o tome u kojoj je koloni — `Clicks to call` (Google-hosted) je bila *van* kolone, a `Klik na telefon (web)` (naša, WEBPAGE) *unutra*.
- Posledica koja je stvarno nastala: pravilo iz [[CLAUDE]] §4 („ne uvoziti GA4 `tel` kao Ads konverziju") je **verovano na reč** mesecima, a u nalogu je `tel` sve vreme bio primarna konverzija — 17 od „26 plaćenih konverzija" (01.06–10.08). Ceo prag za 4.8 (Maximize Conversions) je meren pogrešnim brojačem.
- **Provera koja ovo hvata** (~30 sekundi, GAQL):
  ```
  SELECT conversion_action.name, conversion_action.type, conversion_action.category,
         conversion_action.primary_for_goal, conversion_action.include_in_conversions_metric,
         conversion_action.counting_type
  FROM conversion_action WHERE conversion_action.status = 'ENABLED'
  ```
  plus segmentacija `SELECT segments.conversion_action_name, metrics.conversions FROM customer` — bez segmentacije agregat izgleda kao jedan broj.
- ⚠️ **Segmentacija po akciji vraća samo akcije sa bar jednom konverzijom u periodu** (poznato od ranije) — akcija sa 0 konverzija postoji i može proraditi sutra; zato uvek gledati i `conversion_action` tabelu, ne samo segmentaciju. Konkretno: postoje **dve** telefonske akcije u „Conversions", druga trenutno na 0.
- **Pravilo: pre svake tvrdnje „imamo N plaćenih konverzija" — segmentiraj po akciji.** Agregat je zbir stvari koje ne merimo istom svrhom.
- Vezano: [[analiza/2026-08-11-snapshot-jul]] §3.6.

## Metrika u KPI tabli mora nositi jedinicu — „55 konverzija" je bilo 55 *pregleda* (2026-08-11)
- Hvala-proxy je definisan kao `screenPageViews` na `/hvala-za-poruku/`. To je bila razumna aproksimacija dok se ne pojavi razlog da ne bude — a razlog je postojao od početka: **jedan dolazak = 2 pregleda** (dupli GA4 `page_view` tag). Jun „55" = **24 sesije**.
- Greška nije u merenju nego u imenovanju: red u KPI tabli je pisao „Prave konverzije", pa je 15 meseci niko nije preispitivao. Da je pisalo „pregledi hvala stranice", nesklad sa brojem mejlova u inboxu (11 lead-mejlova za 16–30.07, M5) bi iskočio odmah.
- **Pravilo: KPI red nosi jedinicu u imenu** (`sesije`, `pregledi`, `mejlovi`), a gde postoji nezavisan izvor iste veličine (inbox, Ads konverzije) — bar jednom ih uporediti. Dve metrike koje mere „isto" a razlikuju se 2× su dijagnoza, ne šum.
- Vezano: [[analiza/2026-08-11-snapshot-jul]] §2.3b · [[dnevnik/2026-08-11-generate-lead-inflacija-dijagnoza]].

## Pad organskih klikova uz RAST pozicije nije regresija sajta — proveri CTR pre nego što tražiš krivca (2026-08-11)
- Jul 2026 vs jul 2025: pozicija **8,2 → 6,0** (bolje), prikazi **+22%**, a klikovi **−18%**, jer je CTR pao 6,76% → 4,52%. Isti obrazac na nivou pojedinačnog upita: `dimenzije košarkaškog terena` je na poziciji **1,9** sa CTR-om **2,3%** (732 prikaza, 17 klikova) — na prvoj poziciji CTR bi trebalo da bude 25–35%.
- To je SERP koji troši klik (AI Overviews, snippet, PAA), ne naš rad. Najizloženiji su tačno informativni upiti (dimenzije, „kako se pravi") — a to su naše najjače stranice po prikazima.
- **Pravilo: pre nego što se pad klikova pripiše izmeni na sajtu, razložiti ga na pozicija × prikazi × CTR.** Ako pozicija raste a CTR pada, uzrok je van sajta i akcija je drugačija (razlog za klik — cena, kalkulator, galerija — a ne „više sadržaja").
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
- Vezano: [[dnevnik/2026-08-11-litespeed-redis-web-cache-manager]].

## GA4 totali iz konektora UKLJUČUJU `localhost` i `staging` — bez `hostName` filtera brojke su tuđe (nedeljni izveštaj, 2026-08-11)
- `ga4_report.py` vraća `activeUsers`/`sessions` bez ijednog filtera, a lokalni build od 2026-07-22 nosi **pravi** GTM-TRDT8K9 kontejner (mu-plugin `al-tracking-gtm-consent.php`) i šalje u istu GA4 property.
- Izmereno: nedelja 28.07–03.08 imala je **1.068 pregleda sa `localhost`** na 1.504 sa live-a — skoro 42% ukupnih pregleda je bio naš rad. Nedelja 04–10.08: 213 lokalnih.
- Posledica: neko poređenje 7d vs 7d može pokazati „pad saobraćaja" koji je zapravo samo **manje našeg lokalnog testiranja** te nedelje.
- Ključni eventi su manje pogođeni (2 `generate_lead` sa localhost-a od 41 u nedelji 04–10.08), ali nisu nula — `staging.antasline.com` je dao i 2 `tel` eventa.
- **Pravilo:** svaki GA4 izveštaj koji ide Miroslavu filtrira se na `hostName == www.antasline.com`. Kad ta razlika postoji, ona se i navede u napomenama.
- Trajno rešenje (nije urađeno, kandidat posle live-a): GA4 filter za interni saobraćaj ili odvojen Measurement ID za lokalni build.
- 🔴 **Dopuna istog dana (2026-08-11, uveče): ova lekcija je pregažena nekoliko sati posle nego što je zapisana.** Nova sesija je pokrenula `/nedeljni-izvestaj` bez čitanja [[PROGRESS]] i poslala Miroslavu sirove brojke (667/810 korisnika, kumulativ 127 umesto 119). Zapis u vault-u očigledno **nije dovoljna odbrana** dok je filter stvar pamćenja: `ga4_report.py` i dalje vraća nefiltrirane totale, a `/nedeljni-izvestaj` skill nigde ne pominje `hostName`. Zakrpa koja stvarno drži: filter (ili `--live-only` flag) ugrađen u samu skriptu + jedan red u skill-u. **Čeka odluku #ceka-miroslav.**

## `generate_lead` i hvala-proxy broje PREGLEDE, ne lidove — inflacija ~3× po sesiji (nedeljni izveštaj, 2026-08-11)
- Nedelja 04–10.08 na live-u: `/hvala-za-poruku/` ima **10 sesija / 8 korisnika**, ali **26 pregleda** i **39 `generate_lead`** evenata. Prethodna nedelja isto: 3 sesije → 6 pregleda → 9 evenata.
- Obrazac je **deterministički od jula**: svakog dana važi `generate_lead = 1,5 × broj pregleda`, a svi dnevni pregledi su parni brojevi. To ne liči na osvežavanje stranice od strane korisnika.
- Znači da je cela KPI serija „prave konverzije" (baseline „~55/mes", kumulativ 119 od 01.06) merena **pregledima** — po sesijama je to 51.
- ⚠️ Ne meša se sa junskim istorijskim šumom: `generate_lead` je u junu okidao i na `/kontakt/` (staro pravilo, [[CLAUDE]] §4), pa kumulativ evenata (220) ima i taj rep — obrazac 1,5× važi tek od jula.
- ⚠️ Ads-ova strana broji svoje (5 konverzija te nedelje) i **nije** naduvana u istoj meri — ne izvoditi zaključak o Ads performansama iz GA4 brojača.
- ✅ **UZROK DIJAGNOSTIKOVAN 2026-08-11 (uveče).** Nisu dva ista baga nego **dva različita**, jedan od kojih preživljava migraciju a drugi ne:
  - **Bag A — suvišan `page_view` tag, postoji i na buildu, PREŽIVLJAVA migraciju.** GTM pravilo `IF [event=gtm.js AND putanja sadrži /hvala-za-poruku]` okida **četiri** taga: `generate_lead` (id 17) · **`page_view` (id 18)** · Ads konverzija `__awct` (id 20) · `fbq('track','Lead')` (id 38). Ali Google tag `G-H8BRCZN8W4` (id 11, okidač `gtm.init`) **već sam šalje `page_view`** na svakoj stranici → na hvala stranici ih ima dva. Izmereno u mreži: jedno učitavanje = `_s=1 page_view` (Google tag) + `_s=2 generate_lead` + `_s=3 page_view` (suvišan tag 18). Identično na live-u i na lokalnom buildu. **Zato je hvala-proxy tačno 2× stvaran broj dolazaka** — i zato su svi dnevni brojevi parni.
  - **Bag B — trostruki `generate_lead`, SAMO na live-u, NE prenosi se.** Live Kallyas stranica ima **dva odvojena GTM embeda** istog kontejnera (jedan iz teme sa `data-cfasync`, drugi kroz `litespeed/javascript`) + noscript; `dataLayer` sadrži `gtm.js` **dvaput**. Izmereno na live-u: jedno učitavanje = 2× `page_view` + **3×** `generate_lead`. Lokalni WoodMart build ima **jedan** embed i daje 1× `generate_lead`.
- 🔴 **Posledica za post-live poređenje (najvažnije):** posle migracije obe brojke padaju same od sebe — `generate_lead` na ~⅓, hvala-proxy na ~½ (ako se bag A ne popravi) ili na ~⅙ ukupno (ako se popravi). **To NIJE pad konverzija i ne sme se tako čitati** u prvom post-live izveštaju.
- Naknadna reprodukcija „drugog embeda" ubacivanjem posle učitavanja stranice **ne radi** (GTM čuva `google_tag_manager[id]` i ne inicijalizuje se dvaput; ni drugi `gtm.js` push u `dataLayer` ne okida ništa) — mora biti u početnom HTML-u, kao na live-u. Za dokazivanje ovakvih stvari: meriti `analytics.google.com/g/collect` zahteve po `en=` i `_s=`, ne pretpostavljati iz konfiguracije.
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
- **Pravilo:** za pitanja **pokrivenosti** (a ne ispravnosti) uzeti nezavisan izvor URL-ova: live sitemap, GSC `page` dimenzija, ili `wp post list` — nikad sopstveni sitemap.

## Google OAuth u statusu *Testing* gasi refresh token posle 7 dana — konektor Ads/GMB tiho umire (2026-08-11)
- Simptom: `invalid_grant: Token has been expired or revoked` iz `ads_report.py` / `gmb_report.py` / `ads_final_urls.py`, dok GA4/GSC skripte rade savršeno.
- Razlog za tu asimetriju: GA4/GSC idu preko **servisnog naloga** (ne ističe), Ads/GMB preko **OAuth Desktop klijenta**.
- Izmereno: `token.json` osvežen 06.08, mrtav 11.08 = 5 dana. Nije jednokratni kvar, ponoviće se.
- Zakrpa: `authorize_oauth.py` (browser, 1 min). **Trajno rešenje: Cloud Console → OAuth consent screen → Publish app** (*In production*) — refresh token tada ne ističe po vremenu, u skriptama se ne menja ništa.
- 🔴 **Planska posledica:** proveriti token **na dan migracije pre početka** (stavka B1 checkliste) — 4.10 i verifikacija konverzija zavise od njega, a to je najgori trenutak za browser-consent.
- Usput: `token.json` nosi samo scope-ove za koje je poslednji put autorizovan (trenutno samo `adwords`; `tagmanager.edit.containers` iz 27.07 nije u njemu).
- **Dopuna 2026-08-11:** ista *Testing*-status posledica ima i drugu formu — pri ponovnoj autorizaciji Google prvo pokaže ekran **„App not verified"** (jer je `adwords` scope "sensitive"), ne direktno "Allow". Rešenje nije puna Google verifikacija (nepotrebna za app sa jednim korisnikom) nego: potvrditi da je nalog u **Test users** listi (OAuth consent screen → Audience) i kliknuti **Advanced → Go to [app] (unsafe)**. v. [[reference/api-konektor-setup.md]] Korak F.

## Google Ads Customer Match preko starog Ads API-ja je blokiran za developer tokene bez istorije — obavezan Data Manager API (2026-08-11)
- Simptom: `customer_match_upload.py --confirm` (uz **važeći** OAuth token) pada na `GoogleAdsException` sa `CUSTOMER_NOT_ALLOWLISTED_FOR_THIS_FEATURE`: "Customer Match uploads aren't supported in the Google Ads API for the developer token of the request. Use the Data Manager API for Customer Match workflows."
- Isti error kod se prvi put pojavio 2026-08-07 i bio protumačen kao „Basic developer token, treba Standard access u Ads API Center" — ta pretpostavka **nije potvrđena** kao ispravna.
- Google-ova zvanična dokumentacija (Data Manager API GA dec. 2025, obavezno od **2026-04-01**): developer tokeni koji **nikad ranije nisu slali Customer Match zahtev** preko starog `OfflineUserDataJobService`-a su blokirani **bez obzira na tier** (Basic ili Standard) — moraju na nov, poseban **Data Manager API** (`datamanager.googleapis.com`, paket `google-ads-datamanager`, klijent `IngestionServiceClient.ingest_audience_members()`). Ne treba developer token uopšte — čist OAuth 2.0.
- ⚠️ **Nusefekat na živi nalog:** `OfflineUserDataJobService` stigne da kreira **praznu user listu** u Ads UI-ju (Audience manager → Segments) pre nego što padne poziv za dodavanje članova — bezopasno, ali ostaje vidljivo dok se ručno ne obriše.
- **Pravilo:** pre nego što se pretpostavi da developer token tier (Basic/Standard) rešava neki Ads API blokator, proveriti da li je konkretan endpoint/feature u međuvremenu migriran na poseban API (Google redovno izdvaja funkcionalnost iz glavnog Ads API-ja u satelitske API-je — Data Manager je jedan primer).
- Setup plan za migraciju: [[reference/api-konektor-setup.md]] Korak G.

## `RedirectMatch` ČUVA query string — `?gclid=` preživljava 301 (2026-08-11)
- Bitno jer bi suprotno značilo da svaki preusmeren klik iz oglasa gubi `gclid` → konverzija se ne pripisuje Ads-u, a to bi se otkrilo tek posle migracije.
- Izmereno u izolovanom Apache folderu: `/sportski-podovi/?gclid=X&utm_source=google` → `Location: /sportske-podloge/?gclid=X&utm_source=google`. Važi i za ćirilična pravila.
- `mod_alias` dodaje originalni query samo kad **cilj nema svoj** query — ako se ikad napiše pravilo sa `?` u cilju, originalni parametri se **gube**.

## GA4 landing stranice nisu zamena za Ads final URL export — dokazano istog dana (2026-08-11)
- GA4 vidi samo ono što **ima klikove**, i beleži URL **posle** redirekta — pa oglas koji danas prolazi kroz Redirection plugin izgleda savršeno ispravno.
- Ne vidi: oglase/sitelinkove bez klikova (baš oni nose zaboravljene stare URL-ove), keyword-level final URL-ove, `tracking_url_template` / `final_url_suffix`, pauzirane kampanje.
- 🔴 **Izmereno, ne teorijski:** GA4 presek je dao 29/29 čisto, a Ads export je na istom nalogu našao **8 problematičnih URL-ova** — uključujući 2 koja vode na **tuđi domen**. GA4 nije uhvatio nijedan.
- Koristan kao presek najprometnijih odredišta i kao baseline, nikad kao razlog da se audit zatvori.

## Oglasni URL može voditi na TUĐI domen — 301 mapa tu ne pomaže (2026-08-11)
- Nađeno u pauziranoj kampanji: 3 oglasa i 2 sitelinka pokazuju na `ekopodneploce.rs`, ne na antasline.com.
- Nijedna redirect mapa, parity provera ni regression sweep to ne hvata — svi rade nad **našim** domenom. Vidi se isključivo iz Ads export-a.
- 🔴 Posledica za alate: **ne normalizovati URL u putanju pre provere hosta.** Prvi prolaz `ads-url-audit.php` je `ekopodneploce.rs/proizvodi/…` proverio kao putanju na našem sajtu i prijavio lažan `PUKAO`. Dodata zasebna klasa `EKSTERNI-DOMEN`.

## Google Ads API (GAQL): polje po kom se filtrira mora biti i u `SELECT` (2026-08-11)
- `WHERE campaign.status != 'REMOVED'` bez `campaign.status` u `SELECT` puca sa „The following field must be present in SELECT clause".
- Nije opšte SQL ponašanje — specifično za GAQL, i pogađa svaki upit nad `ad_group_criterion` / `campaign_asset` / `ad_group_asset`.

## Python `print` na Windows-u puca čim ćirilični izlaz ode u fajl (2026-08-11)
- Konzola je cp1250; dok se gleda na ekranu radi, ali `> fajl.json` daje `UnicodeEncodeError` i **prazan fajl uz exit 1**.
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
- Eksplicitan `UPDATE post_parent=0` pre `wp_delete_post(force)` uklanja svako nagađanje o tome šta core radi sa decom-prilozima. Kontrola posle: broj priloga pre = posle (7.764 = 7.764).

## Mrtav draft nije nužno smeće — može biti izvorni tekst novih stranica (2026-08-11)
- Legacy CPT draftovi su ranije korišćeni kao **izvor sadržaja** za WoodMart rebuild (Naxos Evolution → `/sportski-podovi-za-sale-i-balone/`, 378 GSC klikova).
- Zato izvoz sadržaja u vault (`.md` + `.json`) pre brisanja, nezavisno od toga što je SEO vrednost nula. SQL bekap je za rollback, arhiva je za čitanje.

## XAMPP: `wp db export` puca na „mysqldump is not recognized", `wp db query` tiho vraća prazno (2026-08-11)
- `mysqldump` nije na PATH-u → pre poziva `export PATH="$PATH:/c/xampp/mysql/bin"`.
- `wp db query` je vratio **0 redova** za upit nad `wpGs_options` iako redovi postoje, bez greške. Za dijagnostiku koristiti `eval-file` sa `$wpdb->get_results()`, ne `db query`.

## „Nema meta opis + van sitemap-a" nije nužno bag — može biti tačan opis penzionisane stranice (2026-08-11)
- 4 lokalne stranice (`podovi-za-poslovni-prostor`, `izgradnja-terena-za-tenis`, `podne-obloge-za-promocije-i-sajmove`, `galerija-sportskih-terena`) izgledale su kao propust: `noindex`, bez meta opisa, nevidljive regression sweep-u.
- Provera pre popravke: **svaka ima noviji, već indeksiran parnjak iz WoodMart rebuild-a** (ID obrazac 5xxx = stari build vs 166xx/170xx = rebuild), i nijedna ne postoji na live-u. Noindex je bio nameran.
- Uključivanje indeksa bi napravilo 4 duplikat-para pred migraciju — suprotno anti-kanibalizacionom pravilu.
- Pravilo: pre „popravke" SEO propusta na staroj stranici, potraži ima li novijeg parnjaka u istom klasteru (viši ID, isti pojmovi u slug-u). Ako ga ima, propust je verovatno odluka.

## Provera koja vrati „0 nalaza" mora prvo dokazati da ume da nađe nalaz (2026-08-11)
- „Čisto" i „alat ne radi" izgledaju identično u izlazu. Ovo je već udarilo 10.08 dvaput u regression sweep-u (`strip_tags` lažni pozitiv, pogrešan regex delimiter) — tada u suprotnom smeru.
- Praksa: pre nego što se nula upiše kao rezultat, propustiti kroz istu proveru **bar jedan namerno pokvaren primer** (npr. `PodloÅ¾ni Ä‡ilim` za mojibake, `ko?arku` za izgubljenu dijakritiku). Ako ih ne uhvati, nula ne znači ništa.
- Košta 30 sekundi i jedina je razlika između „provereno" i „izgleda kao da je provereno".

## `LIKE '%Ä%'` u WP bazi lovi i obično `a` — kolacija je akcent-neosetljiva (provera dijakritike, 2026-08-11)
- Kolone `wpGs_posts.post_title` i `wpGs_postmeta.meta_value` su `utf8mb4_unicode_520_ci` — **akcent- i case-neosetljiva** kolacija. Zato `LIKE '%Ä%'` uredno pogađa `a`/`A`, a `LIKE '%ć%'` broji i svako `c`.
- Praktična posledica: prva provera „ima li mojibake u naslovima" prijavila je ~385 lažnih pozitiva i izgledala kao veliki incident. Ponovljena sa `LIKE BINARY` → **0 nalaza**.
- Pravilo: kad se traži **oblik zapisa** (mojibake, dijakritika, znak zamene), uvek `LIKE BINARY` ili `COLLATE utf8mb4_bin`. Obično `LIKE` je za značenje/pretragu, ne za forenziku enkodinga.

## UTF-8 ne sme kroz `mysql -e "..."` na ovom Windows shell-u — a `SELECT` to NE otkriva (meta opisi, 2026-08-11)
- Srpski tekst prosleđen kao `mysql -u root baza -e "UPDATE ... SET meta_value='… ć š ž …'"` stiže **u bazu** sa `?` umesto dijakritika — konzolni codepage ga pojede pre nego što stigne do klijenta.
- Podmuklo: provera `SELECT`-om u istoj konzoli pokazuje iste `?`, pa izgleda kao kozmetički problem **prikaza**, ne kao stvarno oštećenje podatka. Zaključak „samo konzola ne ume da prikaže" je pogrešan i propušta bag.
- Ispravno: napisati `.sql` fajl (Write alat) koji počinje sa `SET NAMES utf8mb4;` pa `mysql --default-character-set=utf8mb4 -u root baza < fajl.sql`. Tako pisani upisi su bili tačni iz prve.
- Jedina pouzdana provera je **van baze**: `curl` nad renderovanom stranicom i pogled u `<head>`.

## Sitemap-based regression sweep ne vidi `noindex` stranice — po definiciji (meta opisi, 2026-08-11)
- `migracija/alati/regression-sweep.php` obilazi sitemap. Stranice sa `robots: noindex` nikad nisu u sitemap-u, pa ne postoje ni u izveštaju.
- Konkretno: sweep je prijavio 6 stranica bez meta opisa, a stvarno ih je 11 (6 + 4 `noindex` + 1 sa Rank Math fallback-om).
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
- **Pravilo:** `.bak` kopije su alat za lokalni rad i ne smeju u paket. `build-staging-package.sh` od 10.08 ima exclude za `*.bak-*`/`*.orig`/`*.old`/`*~`. Ako se ikad pakuje ručno — proveriti `find wp-content -name "*.bak*"` pre slanja.

## Isti pokvaren link ume da živi u VIŠE `widget_*` opcija — popraviti jednu nije dovoljno (W3 3.10, 2026-08-10)
- `/spoljne-podne-obloge/` (bez j) → 404 na **svih 195 stranica**. Prva popravka je gledala `widget_text` („Navigacija"), upisala 1 zamenu — i provera je i dalje javljala 404, jer je drugi pogodak bio u `widget_custom_html` (kolona „Podovi", tekst linka „Terase i dom").
- Isti tip bug-a je 2026-08-07 nađen na staging-u i tada je popravljen samo taj jedan pogodak.
- **Pravilo:** za footer/sidebar linkove proći kroz sve `widget_*` opcije (`widget_text`, `widget_custom_html`, `widget_block`, `widget_nav_menu`), pa TEK ONDA verifikovati HTTP-om. I obavezno verifikovati posle popravke — brojač zamena „1" ne znači da je link nestao sa stranice.

## `strip_tags()` ZADRŽAVA sadržaj `<script>` — provera „sirov JSON-LD u tekstu" daje lažni pozitiv (W3 3.10, 2026-08-10)
- Provera za F7.15 obrazac (kses pojeo `<script>` omotač pa se JSON vidi kao tekst) pisana je kao `preg_match('#"@context"…#', strip_tags($html))` → prijavila je problem na **svih 195 stranica**, tj. na svakoj koja uopšte ima schema-u.
- `strip_tags()` uklanja tagove ali ostavlja njihov tekstualni sadržaj, uključujući telo `<script>`.
- **Pravilo:** prvo `preg_replace('#<(script|style)\b[^>]*>.*?</\1>#is', '', $html)`, pa onda `strip_tags()`.
- Uz to, u istom alatu: regex delimiter `#` sa znakom `#` unutar klase (`'#^(mailto:|tel:|#)#i'`) → „Unknown modifier ')'", filter tiho pada. Koristiti `~` kao delimiter.

## Pre nego što nešto upišeš u plan kao blokator — pretraži `woodmart-sabloni` (2026-08-10)
- Plan od 09.08 je vodio „lazy facade embed" kao 🔴 tehnički blokator za kačenje videa. Rešenje je stajalo u [[migracija/woodmart-sabloni]] pod **F7.3 od 2026-07-07** — CSS + globalni JS, radi na 9 stranica. Izgubljen ceo jedan zapis u planu i deo sesije na proveru nečega što je odavno gotovo.
- **Pravilo:** F-numerisane stavke u `woodmart-sabloni` pokrivaju više nego što se pamti (F7.1–F7.21+). Pre upisa „treba napraviti X" u bilo koji plan: `grep -i X migracija/woodmart-sabloni.md`.
- Isti obrazac se već ponavljao (v. „Refresh-evi od 2026-07-08", „4 duplikat-stranice", „`15580`→`16589`") — **plan koji nije proveren protiv stvarnog stanja stvara lažne blokatore**, i oni onda blokiraju stvarno.

## Rank Math besplatan (1.0.275) NEMA Video modul — `VideoObject` ide ručno (2026-08-10)
- Provereno na dva mesta, ne po sećanju: opcija `rank_math_modules` i `seo-by-rank-math/includes/modules/` na disku — 23 modula, `video` nije među njima.
- Rešenje koje se pokazalo boljim od ručnog upisa po stranici: **schema se izvodi iz markupa koji već stoji u sadržaju** (`woodmart-child/inc/al-video-schema.php` skenira `data-yt-id` na `wp_footer`). Nula izmena u bazi → zaobiđeni i kses (F7.15) i `wpautop` (F7.20c) i potreba za backup-om.
- **Prenosivo pravilo:** kad schema treba na N postojećih stranica, prvo pitati može li se izvesti iz postojećeg markupa umesto da se upisuje u `post_content`. Izmena koda je reverzibilna, izmena baze nije.
- `uploadDate` za YouTube video se ne dobija preko oEmbed-a (ne postoji u odgovoru) — dolazi sa javne `watch` stranice iz `ytInitialPlayerResponse` → `microformat.playerMicroformatRenderer.publishDate`. `duration` iz `videoDetails.lengthSeconds` (pretvoriti u ISO 8601). Bez API ključa.
- `maxresdefault.jpg` **ne postoji za svaki video** (2/8 u našem slučaju) — proveriti HTTP kodom pre nego što uđe u `thumbnailUrl`, i uvek navesti `hqdefault` kao rezervu.

## Gemini/Flow: `input[type=file]` postoji, ali tek POSLE otvaranja menija — plus Gemini žigoše klipove (2026-08-10)
- 🟢 **Ispravka ranije beleške.** Tvrdnja „Gemini nema `input[type=file]` u DOM-u (provereno JS-om), pa je image-to-video zatvoren za automatizaciju" je **netačna**. Input se **kreira dinamički tek kad se otvori odgovarajući meni**: `+` → „Направи видео" → ikonica slike ispod prompt polja. Tek tada `document.querySelectorAll('input[type=file]')` vraća pogodak i `file_upload` MCP alat radi normalno (5 MB JPG prošao bez problema). Isti obrazac u Flow-u: `+` → „Upload media".
- **Pravilo:** provera DOM-a na *zatvorenom* UI-ju daje **lažno negativan** rezultat. Nikad ne zaključivati „nema file input-a" iz jednog `querySelectorAll` pre nego što se meni otvori. Isto važi za `accept` atribut — prvi input koji se nađe (npr. Gemini „Отпреми фајлове" za dokumente) ima `accept` bez slika, što dodatno navodi na pogrešan zaključak.
- 🔴 **Gemini klipovi nose vidljiv „sparkle" vodeni žig** u donjem desnom uglu, kroz ceo klip. **Flow klipovi ga nemaju** (provereno poređenjem istog ugla kadra na 2 Flow klipa). Za materijal koji ide na sajt/Ads/YouTube: **Flow je izvor, Gemini rezerva.**
- **Trajanje se razlikuje:** Gemini vraća **10s** klipove (sa audio stream-om, koji se ionako odbacuje `-an`), Flow **8s**. Bitno pri računanju `xfade` offset-a u montaži.
- ⚠️ Kad Flow javi „You need more AI credits", **prvo proveriti koliko je sati** — reset je po pacifičkoj ponoći (v. lekcija ispod), a ne kraj kvote. Zaključak „besplatni nalog nema dnevne kredite" je izveden i **oboren u istoj sesiji**.

## Brend font za video natpise: Google Fonts woff2 podskupovi + varijabilni font — tri zamke u nizu (ffmpeg drawtext, 2026-08-10)
- Child tema drži brend fontove **samo kao `.woff2`** (`woodmart-child/fonts/`), a ffmpeg `drawtext` traži TTF/OTF. Konverzija je moguća bez instalacije: `fontTools.ttLib.TTFont(x.woff2)` → `font.flavor=None` → `save(x.ttf)`.
- 🔴 **Zamka 1 — podskupovi.** Google Fonts deli font na `latin` i `latin-ext`. **`latin-ext` sadrži SAMO dijakritiku** (č/ć/š/ž/đ), bez osnovnih slova. Ako se uzme samo taj fajl, ffmpeg iscrta **kutiju bez teksta** (ili samo jedno slovo) — a `box=1` i dalje radi, pa na prvi pogled izgleda kao da je problem u boji/alfi. **Uvek spojiti oba podskupa** (`fontTools.merge.Merger`).
- 🔴 **Zamka 2 — provera koja laže.** Provera „ima li font dijakritiku" prolazi i na golom `latin-ext` fajlu jer dijakritika tamo POSTOJI. **Proveravati ceo tekst koji se stvarno crta**, ne samo dijakritiku: `missing=[c for c in probe if ord(c) not in cmap]`.
- 🔴 **Zamka 3 — varijabilni font.** Inter woff2 je varijabilan; `Merger().merge()` puca sa `AttributeError: type object 'VarStore' has no attribute 'mergeMap'`. Lek: prvo `instancer.instantiateVariableFont()` pa merge. **Podrazumevana vrednost `wght` ose je 400 iako se fajl zove `inter-700`** — ako se ne zada `{"wght":700}` eksplicitno, dobija se tanak font pod imenom bold.
- **Uvek vizuelno proveriti rezultat** (`ffmpeg -ss T -frames:v 1` pa pogledati kadar) — tekstualni izlaz ffmpeg-a je „OK" i kad se ne iscrta nijedno slovo.
- ffmpeg 9.0 usput: **`-filter_complex_script` više ne postoji**, zamenjeno opštim `-/filter_complex fajl.txt` oblikom (čita vrednost opcije iz fajla) — nezamenljivo kad filter lanac ima navodnike i zareze koje shell inače izmrcvari.

## Google Flow — dnevni krediti se NE resetuju po lokalnoj ponoći (2026-08-10)
- U 00:34 po lokalnom vremenu (CEST, 22:34 UTC) nalog je i dalje pokazivao **0 kredita** iako je „datum" prešao u novi dan. Reset ide po Google-ovoj zoni (pacifička ponoć ≈ 09–10h ujutru po lokalnom), ne po korisnikovoj.
- **Praktično:** ne planirati „uveče potrošim, u ponoć nastavim". Dnevni budžet od 50 kredita je stvarno jedan po kalendarskom danu **pacifičke** zone.
- Usput: otvaranje starog Flow projekta ume da vrati `Application error: a client-side exception has occurred` (bela/crna stranica). Lek: otići na `labs.google/fx/tools/flow` (spisak projekata), ne direktno na `/project/<id>` URL.

## Google Flow (Veo 3.1) — agent se zaglavljuje, model se bira u promptu, Lite = Fast na sporim kadrovima (2026-08-09)
- **Agent se zaglavljuje**: dvaput je najavio render („I'm going to animate your photo…") i **nikad nije prikazao dugme za odobrenje** — sesija ostane mrtva, kredit se ne potroši ali ni video ne nastane. **Lek: otvoriti novu sesiju** (ikona olovke u panelu), ne nastavljati staru i ne guraje dodatnim porukama.
- **Agent settings → „Save" ne hvata izbor modela.** Prebacivanje na Veo 3.1 Lite kroz podešavanja dvaput nije ostalo sačuvano (agent je i dalje najavljivao Fast). Pouzdano je tražiti model **u samom tekstu prompta**: `Using the Veo 3.1 - Lite model, animate this exact photo…`.
- **Cene (izmereno, ne pretpostavka)**: 50 besplatnih kredita dnevno · **Lite 10** · **Fast 20** po klipu od 8s. Na sporim pokretima kamere nad statičnim objektom (teren, pod) **razlika u kvalitetu se ne vidi** → podrazumevano Lite, Fast samo za hero kadar. To je 5 klipova dnevno umesto 2.
- **Prompt pravilo koje čuva autentičnost fotke**: traži **samo pokret kamere i ambijent** (vetar, oblaci, svetlo) + eksplicitno „keep the surface, colours and markings exactly as in the photo" i „do not add any new objects, people or basketballs". Čim se zatraži radnja (igrač, lopta), Veo počne da izmišlja i podloga prestaje da bude naša realizacija.
- Ulaznu sliku **iskropovati na ciljani odnos (16:9) pre uploada** — Veo inače sam odlučuje šta da odseče.

## Gemini web UI nema `input[type=file]` u DOM-u — upload slike je zatvoren za browser automatizaciju (2026-08-09)
- Dopuna ranije lekcije „Gemini Veo nema free API tier — samo web UI" (2026-08-04): **ni web UI nije upotrebljiv za automatizovan image-to-video**. `document.querySelectorAll('input[type=file]')` na `gemini.google.com` vraća **prazan niz** — dugme „Отпреми фајлове" otvara **sistemski dijalog** koji automatizacija ne vidi ni ne može da popuni.
- Praktično: za image-to-video ide **Flow** (ima pravi file input, `file_upload` radi). Gemini ostaje korisna **odvojena besplatna Veo kvota** samo za tekst→video, gde izmišljen kadar nije problem.
- Šire pravilo: pre nego što se obeća „uradiću to kroz browser", proveriti da li stranica uopšte izlaže file input u DOM-u — nema smisla trošiti runde na klikanje po meniju koji vodi u nativni dijalog.

## Bulk WP attachment import (12+ Gemini slika u JEDNOM PHP procesu) puca na 300s execution limit — deliti na pojedinačne pozive (2026-08-08)
- Pokušaj da se 12 novogenerisanih color-swatch slika (Condor Schools/Playgrass variation-slike) uveze u jednoj PHP skripti (petlja preko `wp_insert_attachment()`+`wp_generate_attachment_metadata()`) je pukao na `PHP Fatal error: Maximum execution time of 300 seconds exceeded` — WP je to prikazao kao generičku `wp_die()` "kritična greška" stranicu na stdout-u (exit 255), bez ijedne linije stvarnog izlaza skripte, pravi uzrok vidljiv jedino u `wp-content/debug.log`.
- **Pravilo ubuduće:** kad treba uvesti VIŠE od par slika kao WP attachment (svaka nosi `wp_generate_attachment_metadata()` resize trošak + WP bootstrap overhead), pokretati **jedan PHP proces po slici** (kao već testiran `import-gemini-photo.php <post_id> <src> <dest>` obrazac iz 2026-08-05), ne petljati sve u jednom skriptu — svaki poziv dobija sopstveni 300s budžet umesto da se troši kumulativno. Prvo proveriti `debug.log` kad `wp-load.php` skripta pukne bez izlaza (WP guta pravi PHP fatal iza "kritična greška" stranice).
- Bash/Windows gotcha usput: `"$VAR\\$file"` (backslash pred `$` u double-quoted stringu) se ne escape-uje kako se očekuje u Git Bash — koristiti forward-slash MSYS putanje (`/c/Users/...`) dosledno kroz ceo poziv, PHP na Windows-u ih prihvata bez problema.

## Sličan brend-naziv ≠ ista firma — proveriti member-listu holding grupe pre pretpostavke o poreklu slike (2026-08-08)
- M je pretpostavio da su "trava u boji" slike na `/vestacka-trava/` (live) od holandskog Condor Grass-a (dobavljača lokalnih Condor Schools/Playgrass proizvoda) jer se ime "Condor Group" pojavljuje u istom kontekstu holding-a koji ima i "Edel Carpets"/"Edel Yarns" članice. Filename prefiks na slikama (`EG-Colourful-*`) je zapravo od **Edel Grass B.V.** — posve odvojene firme (u vlasništvu Oranjewoud grupe), koja slučajno ima slično ime kao "Edel Carpets"/"Edel Yarns" (koje JESU Condor Group članice) ali sama nije na `condor-group.eu/en/group/members` listi.
- **Pravilo ubuduće:** kad se pretpostavlja da je neka slika/proizvod od dobavljača X jer "ime zvuči povezano" ili je pomenuto u istom pasusu — proveriti zvaničnu member/brand listu holding grupe (ako postoji) PRE kačenja slike na proizvod. Vizuelno/zvučno slično ime (Edel Grass vs Edel Carpets/Yarns) je čest izvor lažne veze, pogotovo u multi-brand holding strukturama (Condor Group ima 10 članica). Boje/varijante iz dva izvora koje se ne poklapaju 1:1 (ovde: lokalni Condor set od 7 boja vs. live Edel Grass set od 6, samo delimično preklapanje) su dodatni signal da su u pitanju dva različita proizvoda, ne isti.

## Customer Match upload zahteva Standard developer token (Basic access ne radi) + membership_life_span max je 540 dana, ne 10000 (2026-08-07)
- Prvi stvaran `customer_match_upload.py --confirm` pokušaj (posle scan_leads.py fix-a) pao je u dva koraka:
  1. `user_list.membership_life_span = 10000` ("konvencija za bez isteka") odbijeno sa `RangeError.TOO_HIGH` — Google je od **2025-04-07** uveo tvrd max od **540 dana** za CRM-based (Customer Match) liste, stari "10000 = trajno" sentinel je ukinut. Fix: konstanta promenjena na `540`.
  2. Posle tog fixa, sama lista se USPEŠNO kreirala u pravom nalogu (prazna, `AntasLine - Website Leads`), ali `OfflineUserDataJobService.create_offline_user_data_job()` je odbijen sa `CUSTOMER_NOT_ALLOWLISTED_FOR_THIS_FEATURE`: "Customer Match uploads aren't supported in the Google Ads API for the developer token of the request. Use the Data Manager API for Customer Match workflows." — Basic developer token access (odobren 2026-07-27 za izveštavanje) ne pokriva Customer Match; potreban je Standard access (ručno odobrenje, Ads UI → Tools & Settings → API Center) ili migracija na poseban Data Manager API.
- **Pravilo:** developer token "access level" (Basic/Standard/Advanced) nije isto što i "token radi/ne radi" — token može uspešno raditi za READ operacije (izveštaji, `ads_report.py`) a istovremeno biti odbijen za specifične WRITE feature-e (Customer Match) koji traže viši nivo. Ne pretpostaviti da je jednom odobren token dovoljan za sve buduće write operacije.
- Nusfekat koji treba znati: prazna user lista OSTAJE kreirana u live nalogu i posle neuspelog upload pokušaja (mutate_user_lists je uspeo, samo posledeći offline data job korak je pao) — nije automatski očišćena, nije štetna (0 članova, 0 troška), ali je stvarna izmena na produkciji koju treba pomenuti transparentno, ne samo "pokušaj nije uspeo".
- #ceka-miroslav: zatražiti Standard developer token pristup u Ads UI API Center pre sledećeg pokušaja.

## Nova skripta pisana na osnovu STARE pretpostavke, ne provere žive baze — pošiljalac lead-mejlova (Customer Match, 2026-08-07)
- `scan_leads.py` (dodat u konektor 2026-08-07) je hardkodovao `LEAD_SENDER = "wordpress@antasline.com"` — tačno vrednost koju bi budući WoodMart/CF7 build slao, ali NE ono što live sajt (i dalje stari Kallyas/Zion Builder) stvarno šalje. P1 nalaz iz 2026-07-30 audita mejlova je već utvrdio pravi pošiljalac (`no-reply@antasline.com`, potvrđeno na svih 49 formi), ali ta činjenica nije prenesena u skriptu napisanu nedelju+ dana kasnije.
- Simptom je bio tih: `--dry-run` nije bacio grešku, samo je vratio "0 novih kontakata" na mailbox-u koji je stvarno imao 65 poruka/6 pravih leadova — lako se protumači kao "nema novih upita" umesto "filter je pogrešan".
- **Pravilo:** kad skripta filtrira po pošiljaocu/formatu koji potiče sa live produkcije (ne sa lokalnog build-a), pre prvog pokretanja proveriti PRAVU vrednost direktno iz mailbox-a/baze (`python3 -c "import mailbox; ..."` na par poruka), ne preuzimati vrednost iz ranijeg plana/dokumentacije bez re-verifikacije — pogotovo ako je ta dokumentacija pisana za drugačiji (budući) sistem.
- Usput: `credentials/` folder na cPanel serveru (`~/antasline-connector/credentials/`) mora biti pravi poddirektorijum — fajlovi prekopirani direktno u `~/antasline-connector/` (bez `credentials/` nivoa) ne rade, `auth.py`/`credentials_dir()` ih ne vidi bez jasne greške dok se ne pokuša pristup.

## Katalog režim (M9) je učinio ceo WC add-to-cart JS stek mrtvim sitewide — proveriti pre nagađanja "šta se može isključiti" (2026-08-07)
- Kad je M pitao za višak CSS/JS u temi, prva pretpostavka bi bila da je WC-ov add-to-cart JS potreban bar na proizvod-stranicama. Provera je pokazala suprotno: `catalog_mode` (WoodMart nativna opcija + child-theme override na `woocommerce_single_product_summary` prio 30) zamenjuje SVAKO add-to-cart dugme (single + loop, čak i na proizvodima sa pravom cenom) linkom ka `/kontakt/` — nema nijedne `<form class="cart">`/`.single_add_to_cart_button` na sajtu. `wc_get_page_id('cart')`/`('checkout')` pokazuju na postove koji fizički ne postoje u bazi.
- **Pravilo ubuduće**: pre dequeue-a bilo kog "očigledno WooCommerce" skripta/stila, prvo proveriti da li postoji STVARNA funkcionalna meta na sajtu (`is_purchasable()` sam po sebi ne dokazuje ništa ako custom kod već preusmerava UI drugde) — grep za override hook-ove u child temi (`woocommerce_single_product_summary`, `catalog_mode`) pre curl provere enqueue liste.
- Dequeue uvek u **child** temi (`woodmart-child/functions.php`), nikad u parent `woodmart` temi — parent theme update briše sve, child preživljava. Postojeći W3 3.6 `sourcebuster-js`/`wc-order-attribution` dequeue je isti obrazac, ovaj nalaz ga samo proširio sitewide.

## jQuery Migrate/interaktivni JS dequeue zahteva pravi browser test, curl ne dokazuje ispravnost (2026-08-07)
- Za razliku od WC script dequeue-a (dokazivo curl-om — handle ili jeste ili nije u HTML-u), uklanjanje `jquery-migrate` kao dependency ne može se smatrati bezbednim samo zato što se stranica i dalje učitava 200 — greška bi bila tiha (dropdown se ne otvara, canvas ne reaguje na klik), ne HTTP status.
- **Pravilo ubuduće**: svaki dequeue koji dira ZAJEDNIČKU (ne feature-specifičnu) skriptu poput `jquery`/`jquery-migrate` mora se testirati uživo kroz Chrome na najkompleksnijoj interaktivnoj komponenti na sajtu (ovde: court builder canvas — klik-farbanje mreže, live tabela) PRE nego što se izmena proglasi gotovom, ne samo na jednostavnim linkovima/formama.
- `resize_window` ne menja stvarni viewport iframe/tab sadržaja u ovom okruženju (poznato od ranije, W1 1.6) — za mobilni test i dalje treba `al-harness.html` (390px iframe harness, i dalje postoji lokalno, mora se obrisati sa produkcije pre migracije po W3 3.10 checklisti). Query param je `u=` (URL-enkodovana putanja), ne `url=`.

## Chrome browser automatizacija (Claude-in-Chrome): klik+type na neke web-app inpute ne registruje unos — proveriti pre Submit-a (GTM mailto tag, 2026-08-07)
- Prilikom popunjavanja GTM "Submit Changes" panela (Version Name/Description) preko `computer` tool-a, `left_click`+`type` je prijavljivao uspeh, ali `document.activeElement` je i dalje bio prazan `<div tabindex="-1">` i input vrednosti su ostajale prazne — simptom se poklopio sa Chrome extension prozorom koji je vraćao sumnjivo mali viewport (837×61) na screenshot-ima uprkos `resize_window` pozivu (verovatno privremen desinhronizovan render state u ekstenziji, ne bag ciljne web app).
- **Rešeno preko `javascript_tool`**: native setter (`Object.getOwnPropertyDescriptor(el.__proto__,'value').set`) da se React/Angular kontrola stvarno registruje kao promenjena, plus ručni `dispatchEvent(new Event('input'/'change', {bubbles:true}))` — bez toga framework ne vidi promenu iako je DOM `value` atribut fizički postavljen.
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
- **Pravilo ubuduće:** kad se sadržaj uvozi sa spoljnog izvora (proizvođač, stock, treća strana), poreklo i dozvola su DVA odvojena pitanja — postaviti oba eksplicitno, ne pretpostaviti da odgovor na jedno pokriva drugo. EXIF `copyright` polje na fotografiji (kad postoji) je koristan rani signal da dozvola nije data automatski.

## Gemini Veo (video) nema free API tier — samo web UI; DeepSeek/Groq nemaju regionalno ograničenje za Srbiju (AI orkestracija istraživanje, 2026-08-04)
- Gemini **slike** (`gemini-2.5-flash-image`, "Nano Banana") imaju solidan free API tier (~500/dan) i Srbija je zvanično podržan region — proxy/VPN nepotreban za foto rad.
- Gemini **Veo (video)** nema free API tier uopšte — besplatan video generisan Veo modelom postoji SAMO kroz Gemini app / Google Flow **web interfejs** (50 kredita/dan). Ne pokušavati video kroz API dok Google ne uvede free tier — pravi se lažna automatizacija koja pada.
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
- **`wd-post-title` skip REŠEN 2026-08-05** — za razliku od `widget_title_tag`, ne postoji theme opcija za blog-loop post-title tag (potvrđeno: `xts-woodmart-options` ima samo `page_title_tag`/`widget_title_tag`). Umesto override-a `templates/content-default.php` (i 5 sličnih core varijanti, rizik na theme update), obrazac je: core `woodmart_page_title()` već zove `do_action('woodmart_page_title_after_title')` odmah posle H1 u SVAKOJ grani (portfolio/shop/generic/blog) — mu-plugin kači vizuelno sakriven `<h2 class="al-sr-only">` na taj hook, uslovljeno `is_home()`. **Opšti obrazac za buduće slične slučajeve**: pre override-a core template fajla, proveriti da li core funkcija već ima `do_action`/`apply_filters` hook na tačno tom mestu — mu-plugin na postojeći hook je uvek jeftiniji i update-safe u odnosu na child-theme kopiju celog template fajla.

## WoodMart VENDOR tema (ne child) generiše nevalidnu BreadcrumbList schema — dvostruko ugnježden niz (pickleball/pop-tenis, 2026-07-30)
- `wp-content/themes/woodmart/inc/modules/seo-scheme/class-breadcrumbs.php:56` ručno dodaje `[`/`]` oko `wp_json_encode($this->schema_items)` u `itemListElement` — ali `$this->schema_items` je već niz asocijativnih nizova, pa `wp_json_encode()` sam vrati `[{...}]`; rezultat je `"itemListElement": [[{...}]]`, nevalidna structured data (Rich Results/schema validator bi ovo odbio). Ovo je NEZAVISNO od Yoast-ove sopstvene `yoast-schema-graph` (koja je ispravna) — WoodMart ubacuje DRUGI, sopstveni `<script type="application/ld+json">` blok preko `add_filter('wp_footer', ...)`.
- **Aktivira se samo gde se `woodmart_breadcrumbs()` template-tag stvarno renderuje** (`inc/template-tags/template-tags.php:2067`) — potvrđeno na `/teren-za-pickleball/` i `/pop-tenis/`, ODSUTNO na `/kontakt/`, `/industrijski-podovi/`, `/dimenzije-fudbalskog-terena/` (verovatno drugačiji breadcrumb render put za te šablone/tipove). Ne pretpostavljati sitewide bez provere — testirati bar jednu stranicu svakog šablona.
- **Nije vidljivo korisniku** (ostaje unutar ispravno zatvorenog `<script>` taga) — razlikovati od pravog "sirov JSON kao tekst" bag-a (v. [[#Schema može mesecima da „postoji" a da nikad nije emitovana (pickleball 16616, 2026-07-28)]]); ovaj je čisto validacioni problem, ne vizuelni.
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
- **Meni na 1500px ima ~673px** (uz logo i telefonski CTA u istom redu). Šest grupa staje samo sa kratkim nazivima — izmeriti zbir `getBoundingClientRect().width` stavki pre nego što se doda grupa; prelamanje u drugi red povećava visinu headera na svakoj stranici.

## Yoast indexable hijerarhija — BreadcrumbList tiho gubi pretka (W7 F3, 2026-07-29)
- 🔴 **`post_parent` može biti tačan a breadcrumb pogrešan.** 4 stranice su emitovale `Početna > [stranica]` bez međukoraka, iako im je `post_parent` ispravan. Uzrok: `wpgs_yoast_indexable_hierarchy.ancestor_id` pokazuje na **indexable koji više ne postoji** (ostatak re-parentovanja sa starog draft-a). Yoast tada **ne pada i ne loguje ništa** — samo izostavi pretka. Popravka: `UPDATE … SET ancestor_id = <id novog pretka>`, ili brisanje samo **detetovog** reda da ga Yoast regeneriše.
- 🔴 **`ancestor_id = 0` nije sirak nego koren.** Uslov „nađi redove čiji predak ne postoji" (`LEFT JOIN … WHERE a.id IS NULL`) pokupi i sve stranice najvišeg nivoa. Kod nas je zbog toga obrisano ~290 indexable redova umesto 4, i pokvarenih stranica je poraslo sa 4 na 26. **Svaki „nađi siročiće" uslov mora eksplicitno izuzeti nulu** (`AND h.ancestor_id <> 0`), a brisati se sme **detetov** red, nikad pretkov.
- Vraćanje samo Yoast tabela iz punog dumpa: `awk '/^-- Table structure for table \`wpgs_yoast_indexable\`$/,/^-- Table structure for table \`wpgs_yoast_migrations\`$/' dump.sql > yoast.sql` pa `mysql < yoast.sql` — brže i bezbednije od punog restore-a.
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
- **Ime fajla nije dokaz šta je na slici.** `2023/01/amss-logo.webp` sadrži žut znak sa natpisom **„AMCC"**, a AMSS ima sasvim drugačiji amblem. Pre nego što se logotip stavi pod nečijim imenom — pogledati ga uvećano (`img.style.height='220px'; filter:none` pa screenshot). Tuđ znak pod tuđim imenom je gora greška od izostavljenog logotipa.

## GTM
- Import ručno pisanog JSON-a NE prolazi — greška "Error deserializing enum type [EventType]". Pouzdano: (A) ručno u GTM UI ili (B) Export → ubaci evente u tačan format → Merge.
- GA4 consent update handler MORA slati eksplicitne vrednosti za sve 4 kategorije; prazan `gtag('consent','update',{})` ne poništava prethodni granted.
- ⚠️ **Ispravka (2026-07-22, W3 3.10)**: raniji unos ovde tvrdio je da lokalni build ima gtag snippet stubovan na `id=DUMMY` — netačno/nikad verifikovano. Stvarno stanje do 2026-07-22: lokalni build NIJE imao NIKAKAV GTM kod (videti novi unos ispod, "GTM UI konfiguracija ≠ embed na sajtu"). Od 2026-07-22 lokalni build ima PRAVI GTM-TRDT8K9 kontejner (preko `mu-plugins/al-tracking-gtm-consent.php`). GTM Preview/Tag Assistant protiv `localhost` NIJE testiran posle ovog fix-a — moguće da i dalje ne radi iz drugih razloga (network/CORS), ali premisa "DUMMY stub" više ne važi. Za live-test triggera pre Submit-a, i dalje najsigurnija opcija je GTM Preview protiv pravog **antasline.com** URL-a (read-only, samo dodaje `gtm_debug` query param, ne menja sajt).
- Pre dodavanja "planiranog" eventa iz CLAUDE.md §4.1 — proveriti GTM UI direktno (Tags + Triggers liste), ne verovati listi u CLAUDE.md bez provere: `view_product_category`/`epoxy_conquest_engagement`/`lead_form_start` su se ispostavili već potpuno izgrađeni i ožičeni iako je stara napomena govorila "proveriti da li postoji ili je pretpostavljeno".
- **GTM UI konfiguracija ≠ embed na sajtu — proveriti oba posle SVAKOG rebuild-a teme** (2026-07-22, W3 3.10): lokalni WoodMart build nije imao NIKAKAV GTM/gtag kod (ni pravi, ni DUMMY stub — stari CLAUDE.md gotcha o tome je bio netačan/nikad verifikovan) uprkos tome što je BLOK A tracking rad (GTM v10, Consent Mode v2, svi eventi) bio potpuno završen — jer taj rad postoji SAMO u GTM UI-ju, a embed `<script>` snippet je fizički ostao na starom Porto/Kallyas builda i niko ga nije preneo u WoodMart rebuild. Da je ovo prošlo neprimećeno na migraciju: nula analitike od dana 1, tiho, bez ijedne greške. **Provera koja bi ovo uhvatila ranije**: `curl <lokalni-url> | grep "GTM-"` posle SVAKE promene teme/buildera, ne samo posle GTM UI izmena. Fix ovog slučaja: `mu-plugins/al-tracking-gtm-consent.php`, doslovna kopija tačnog live koda (izvučeno `curl` + Chrome DevTools `document.getElementById(...).textContent` za JS-injektovan CSS koji `curl` ne vidi).
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
- **Lazy-load slike nemaju `currentSrc`** dok ne uđu u vidno polje — pri merenju prvo `img.loading='eager'` pa `scrollIntoView()`, inače dobiješ prazne vrednosti i pogrešan zaključak.

## Claude-in-Chrome / browser automation
- 🔴 **Snimak ekrana ume da stigne PRE iscrtavanja** (2026-07-28) — ekstenzija je više puta vratila zastareo kadar: prozirna pozadina lightboxa (a `getComputedStyle` je pokazivao `rgba(11,20,32,0.94)`, `elementFromPoint` da overlay pokriva ceo ekran) i tamna kutija umesto učitanog video thumbnail-a (a `img.complete === true`, `naturalWidth 480`). **Dva puta je zamalo dovelo do „popravljanja" nepostojećeg baga.** Pravilo: **kad se snimak ne slaže sa izračunatim stilovima, prvo ponoviti snimak**, pa tek onda menjati kod. Odlučujući test ako i dalje sumnjaš: privremeno postavi svojstvo na neospornu vrednost (npr. puna crvena pozadina) i snimi — ako se promena vidi, iscrtavanje radi i problem je bio u snimku.
- **Ekstenzija po defaultu NEMA dozvolu za Incognito prozore** (2026-07-22, AI test sesija) — kad zadatak eksplicitno traži "bez naloga/incognito" test (npr. mesečni AI test u [[seo/geo-ai-plan]]), prvo proveriti da li nova kartica stvarno pokazuje prazno/odjavljeno stanje pre slanja bilo kakvog prompta — ako se vidi tuđa/postojeća istorija naloga, to NIJE Incognito, samo nova kartica u istom profilu. Fix: `chrome://extensions` → Claude-in-Chrome → "Dozvoli u anonimnom režimu" → korisnik otvori nov Incognito prozor (Ctrl+Shift+N) → tek onda kreni.
- Više Google naloga u istom Chrome profilu: podrazumevani (`authuser=0`) često NIJE onaj sa pristupom (video se `cpgujam@gmail.com` bez pristupa GTM/GSC dok je pravi nalog `miroslav.markovic109@gmail.com` na `authuser=1`) — proveriti prava pre nego što se zaključi "nemam pristup".

## GA4 / publike
- `epoxy_conquest_engagement` okida samo 1× po korisniku (`window.__epoxyTracked` flag) → audience count `≥ 1`, NE `> 1`.
- 4.3K / 99.8% pri kreiranju publike = GA4 procena addressable pool-a, NE stvarna veličina. Dokaz da filteri rade = "Too small to serve" u Ads.
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
- **Gola JSON-LD tekst u `post_content` (bez `<script>` taga) = dvostruko slomljena schema** (nađeno 2026-07-08, F3-reimportovan sadržaj, `/podloga-za-odbojkaske-terene/`): ako se FAQPage/schema JSON zalepi kao plain tekst u sadržaj klasičnog posta (ne u `[vc_raw_html]` ni u pravi `<script>` tag), `wpautop` ga razbija u `<p>`/`<br>` a `wptexturize` menja prave navodnike `"` u kucane `„…"` — rezultat je i vidljiv iskvaren tekst NA STRANICI (posetioci ga vide) i potpuno nefunkcionalna schema (Google ne parsira JSON van `<script>`). Ovo je vrlo verovatno identično na live sajtu ako je F3 reimport povukao 1:1. Provera: `curl stranica | grep "@context"` — ako se pojavi van `<script type="application/ld+json">`, popraviti. Fix: `$wpdb->update` sa `json_encode(..., JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)` obavijenim u pravi `<script>` tag (izbegava kses jer ide direktno u bazu, ne kroz `wp_update_post`).

## Permalink / rewrite izmene (parity F2, 2026-07-07)
- **Soft `flush_rewrite_rules()` nije dovoljan** posle promene WooCommerce `product_base`/`category_base` (permalink strukture) — proizvod URL-ovi vraćaju 404 uprkos ispravnom `get_permalink()` i ispravnim redovima u `rewrite_rules` opciji. Uvek koristiti `flush_rewrite_rules(true)` (hard flush) posle svake permastruct/permalink izmene.
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
- Format-konverzija (.jpg→.webp) menja ekstenziju u fajl-imenu → svaki hardkodovan `<img src>` u `post_content` koji ne ide preko attachment ID-a (npr. ručno ubačene "al-card" reference-foto kartice) ostaje na starom imenu i puca u 404. Posle svake batch konverzije formata: `SELECT ... WHERE post_content LIKE '%stari-fajl.jpg%'` (i `option_value` za widgete/theme mods) pre nego što se stari fajl smatra sigurnim za brisanje.

## Telefon insight
- Broj 072 dominira klicima vs 074; 46/50 klikova sa mobilnog → istaći 072 u oglasima i call asset-ima.

## Sadržaj / HTML unos
- Nikad ne pisati `<p>` tekst preko više redova sa tvrdim prelomom (`\n`) radi čitljivosti u editoru — `wpautop` pretvara svaki pojedinačni `\n` unutar paragrafa u `<br>`, pa se rečenica prelama na sredini na živoj stranici. Rešenje: jedan pasus = jedan kontinuirani red (bez wrap-a) u izvornom HTML-u koji se ubacuje u `post_content`. `<script>` blokovi (JSON-LD) nisu pogođeni — wpautop ih preskače.

## Claude Code ograničenje
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
- Backup destinacija (M politika od 2026-07-09): eksterni HDD `G:` "Maxtor" kad god je prikačen → OneDrive → lokalni fallback. Skripta sama bira (Get-Volume check), ništa se ne menja kad se disk doda/skine.

## GA4 Windsor.ai — `conversions` polje se ne sme slepo verovati (2026-07-21)
- **Windsor `conversions` total polje može biti kontaminirano lažnim key eventima** — 2026-06-17 do 06-22 polje je pokazivalo 800–1200/dan (umesto normalnih 0–10) jer je u GA4 adminu privremeno bilo označeno 8+ dodatnih evenata kao "key event" (`page_view`, `session_start`, `user_engagement`, `first_visit`, `scroll`, `form_start`, `click`, `view_search_results`) pored zaključana tri (`generate_lead`/`tel`/`mailto`). Self-rešeno do 06-23 (potvrđeno praznim pull-om za taj period), ali `conversions` polje samo po sebi ne pokazuje UZROK skoka. **Provera**: `get_data` sa `fields:["date","event_name","event_count","is_conversion_event"]` i `filters:[["is_conversion_event","eq","true"]]` — ako se pojavi bilo šta osim generate_lead/tel/mailto, key eventi u GA4 adminu nisu zaključani na tačno tri kako CLAUDE §4 nalaže.
- Isti kontaminirani prozor (06-17/06-18) pokazao je i `tel:+381692340074` (stari broj) pored `tel:+381692340072` — GTM v10 čist `tel` event bez `tel:+broj` duplikata je i dalje potvrđen za jul (07-14→07-20 pull čist), pa je ovo verovatno bio artefakt iste privremene admin-konfiguracije, ne regresija taga.
- Za mesečno poređenje uvek sumirati `conversions_generate_lead + conversions_tel + conversions_mailto` po danu, ne uzimati agregatni `conversions` field direktno — čak i kad je trenutno čist, jedan loš dan u GA4 adminu ga tiho pokvari bez upozorenja.

## robots.txt na lokalnom XAMPP buildu — WordPress ne generiše virtuelni fajl u poddirektorijumu (2026-07-21)
- **`http://localhost/antasline/robots.txt` vraća 404 kroz WP (ne Apache) jer je WordPress instaliran u poddirektorijumu** — `wp-includes/class-wp-rewrite.php` eksplicitno prazni `$robots_rewrite` osim ako `home_path` nije prazan ili `/` (v. komentar "robots.txt -- only if installed at the root"). `flush_rewrite_rules()` ovo ne rešava, jer nije bug nego namerno ograničenje. Simptom: 404 ali sa WP headerima (`X-Powered-By: PHP`, `Link: wp-json`), ne Apache-ov go 404.
- **Fix koji radi i lokalno i na live-u**: fizički `robots.txt` fajl direktno u document root-u (isti obrazac kao `llms.txt`, v. W2 2.8) — Apache ga servira direktno, zaobilazi WP rewrite sloj potpuno, i ponaša se identično kad sajt bude na pravom domenu (bez poddirektorijuma). Sadržaj referencira produkcioni domen (`https://www.antasline.com/sitemap_index.xml`) već sada, aktivira se na migraciji.

## Fizički .txt fajl u docroot-u bez eksplicitnog charset-a → mojibake za srpsku latinicu (2026-07-23)
- **Statički `.txt` fajl (npr. `llms.txt`) koji Apache/LiteSpeed servira direktno (bez WP-a) šalje `Content-Type: text/plain` BEZ `charset` po default-u na ovom hostingu** — fajl na disku je ispravan UTF-8, ali bez eksplicitnog charset header-a klijenti (browseri, neki AI crawleri) nagađaju enkodiranje i često padnu na Latin-1/Windows-1252, pa se č/š/đ/ž/ć prikazuju kao mojibake iako je sadržaj tehnički ispravan. Za poređenje: `/robots.txt` koji generiše WP kroz `wpseo`/core ima `charset=utf-8` eksplicitno (WP to sam doda), zato taj fajl nikad nije imao ovaj problem.
- **Fix**: `<Files "ime.txt"><IfModule mod_headers.c>Header set Content-Type "text/plain; charset=utf-8"</IfModule></Files>` blok u `.htaccess`, ubačen VAN svih plugin-upravljanih markera (LSCACHE/WordPress/rlrsssl itd. blokova) da ga auto-regeneracija tih plugina ne prepiše — najsigurnije na sam kraj fajla.
- **Pravilo za ubuduće**: svaki budući fizički `.txt`/statički fajl u docroot-u koji sadrži srpsku latinicu (isti obrazac kao `llms.txt`/`robots.txt`) treba ovu istu charset proveru pre nego što se proglasi gotovim — ne pretpostaviti da je UTF-8 fajl na disku dovoljan, proveriti stvarni HTTP response header.

## F1 parity provera — DB path string ≠ stvarna HTTP rezolucija (2026-07-21)
- **`get_page_by_path($slug, OBJECT, 'page')` sa SAMO leaf slug-om (bez punog path-a) pretražuje isključivo `post_parent=0`** — WP core hijerarhijsko poklapanje zahteva pun path kad ga zoveš sa jednim segmentom. Posledica: masovni lažni `NEDOSTAJE-LOKAL` na SVAKOJ ugnježdenoj W1 stranici (bergo-xl, industrijski-pod, kosarkaske-konstrukcije...) dok se ne prosledi pun `$path` umesto `$slug`. Prvi F1 run 2026-07-21 pao sa 129 na (lažnih) 105 PARITY pre fix-a.
- **DB-only poređenje putanje (string exact-match) za hijerarhijske taxonomy arhive (category, verovatno i product_cat) daje lažne "path mismatch" nalaze** — WP-ov rewrite sistem prihvata proizvoljan/netačan roditeljski prefiks u URL-u i svejedno servira ispravan term po zadnjem slug-u (`/category/pogresan-roditelj/pravi-slug/` i `/category/pravi-slug/` vraćaju identičan `<title>`, oba 200). Pravilo: svaki "path mismatch" nalaz iz DB poređenja MORA se potvrditi pravim `curl` pre nego što uđe u redirect mapu — 5 od 6 takvih nalaza 2026-07-21 bili su lažni.
- **UVEK prvo `git diff`/`git show HEAD:<put>` pre nego što se prepiše postojeći CSV sa "osveženim" podacima** — parity-inventar.csv nosi rukom upisane Miroslavljeve odluke (kolona `odluka`) i `LOKAL-NOVO` redove koje fresh re-generacija iz sitemap-a ne rekreira (jednosmeran pravac). Prvi pokušaj merge-a ove sesije je greškom overwrite-ovao fajl pre nego što su stare odluke pravilno pročitane (BOM na prvom header polju `\xEF\xBB\xBFlive_url` je pokvario `array_combine` lookup) — spašeno preko `git checkout -- <fajl>` jer je poslednja dobra verzija bila komitovana.

## Legacy CPT sitemap-ovi — orphan content posle W1 rebuild-a (2026-07-21)
- **Kad se nova stranica gradi iz starog CPT posta kao izvora (industrija-podovi/podovi-posl-prostor/spoljne-podne-obloge/vestacka-trava), stari post treba ručno prebaciti na `draft` — ako se to preskoči, ostaje `publish` i Yoast ga i dalje ubacuje u zaseban `{cpt}-sitemap.xml`**, indeksibilan i bez ijednog internog linka (thin/duplicate content rizik pred migraciju). Nađeno 25 takvih (3+4+2 iz tri CPT-a korišćena kao izvor za W1 stranice + svih 16 `vestacka-trava` postova, koji su čista zaboravljena legacy grupa bez ikakve veze sa novim proizvodima S2/S3 sesija).
- **Provera pre svake sledeće sesije koja koristi CPT kao izvor**: `SELECT post_type, post_status, COUNT(*) FROM wpGs_posts WHERE post_type IN (...) GROUP BY 1,2` — ako ima `publish` redova bez inbound linkova (`LEFT JOIN` na `post_content LIKE '%/{type}/{slug}/%'`), prebaciti na draft + `TRUNCATE wpGs_yoast_indexable` da se sitemap keš osveži odmah.

## Font subsetting + serialized widget opcije (W3 3.6, 2026-07-21)
- **Sužavanje `unicode-range`/font fajla na "šta stvarno treba" mora biti potvrđeno skenom stvarnog sadržaja, ne pretpostavkom jezika.** Google Fonts latin-ext raspon (U+0100–02FF) pokriva desetak jezika; sken celog published `post_content`+`post_title`+Yoast meta preko PHP `mb_ord` po karakteru je jedini pouzdan način da se zna da srpska ekavica realno koristi samo ćčđšž — bez tog skena, sužavanje bi moglo tiho slomiti retku reč sa nepokrivenim karakterom (npr. strano ime u nekom budućem tekstu) u vidljiv tofu-box.
- **`fonttools`/`pyftsubset` (`pip install fonttools brotli`, pa `python -m fontTools.subset`) radi direktno na `.woff2` bez potrebe za izvornim TTF-om** — output isto `.woff2` preko `--flavor=woff2`. Windows put mora ići sa forward-slash-evima kroz Git Bash (`\f` u dvostrukom navodniku se tumači kao escape i briše backslash), ne sa `\\`.
- **Widget sadržaj u `wp_options` (`widget_block` i sl.) je serialized PHP niz — NIKAD ručni string-replace na sirovoj DB vrednosti** (menja dužinu stringa, `s:N:"..."` prefiks postaje neispravan i ceo niz se ne može unserialize-ovati → widget nestaje). Ispravan put: `get_option('widget_block')` (WP ga vrati već unserialized), izmeni `content` polje, `update_option()` (WP ponovo serializuje ispravno).

## Live SERP provere kroz Chrome nisu geolocirane u Srbiju (SERP snapshot, 2026-07-21)
- **`claude-in-chrome` pretrage na google.com sa `?hl=sr&gl=rs` parametrima NE garantuju RS-lokalizovan SERP** — to su slabi hintovi, ne pravi IP u Srbiji. Potvrđeno na "gumeni podovi za terase cena": GSC kaže pozicija 1,4, ali Antas Line se uopšte nije pojavio na prvoj strani u browser proveri (dok se na drugom upitu, "podloga za kosarkaski teren cena", poziciranje poklopilo #1 sa GSC 1,9 — nekonzistentno, ne pouzdano).
- **Pravilo**: GSC pozicija (preko Windsor.ai `searchconsole`) je jedini merodavan broj za sopstvenu poziciju. Live browser SERP provera je korisna SAMO za kvalitativan kontekst (koji se konkurenti pojavljuju, cenovni rasponi u AI Overview-u), nikad kao provera/osporavanje GSC pozicije.
- Isto ograničenje verovatno važi i za `WebSearch` tool (dokumentovano "only available in the US") — ne koristiti ni njega za RS-specifičnu SERP proveru.

## `mysql -N -e "SELECT..."` batch-mode escape-uje newline bajtove (Figma testimonials, 2026-07-22)
- **Kad se `mysql` klijent koristi non-interaktivno (redirect u fajl, `-N -e`), MySQL automatski escape-uje specijalne karaktere u output-u** — pravi newline bajt (`0x0A`) u `post_content` polju izlazi kao literalna 2-karakter sekvenca `\n` (backslash + n), tab kao `\t`, itd. Ovo je dokumentovano MySQL batch-mode ponašanje, ne bug.
- **Posledica**: dump preko `mysql -N -e "SELECT post_content..." > file.txt` IZGLEDA kao da sadržaj nema pravih newline-ova (npr. `wc -l` javlja 1 red) — ali stvarna DB vrednost IMA prave newline bajtove. Ako se taj dump doslovno prekopira u PHP **single-quoted** string (`'...\n...'`, gde `\n` ostaje 2 literalna karaktera) kao anchor za `str_replace()`, poređenje tiho promaši jer se literalni `\n` upoređuje protiv pravog newline bajta u DB-u.
- **Provera pre pisanja replace anchor-a**: direktan PHP dump stvarnog sadržaja (`bin2hex($content)` ili `var_export()`) preko `wp-load.php` bootstrap-a — `0a` u hex-u = pravi newline, `5c6e` = literalni `\n` tekst. Ne verovati mysql CLI batch-output transkripciji za bilo šta osim brzog vizuelnog pregleda.
- **Ispravan put**: PHP **double-quoted** stringovi (`"...\n..."`, PHP interpretira kao pravi newline) za anchor/replacement tekst kad se menja `post_content` preko `wp_update_post()`.

## Content-parity provere protiv live sajta — WebFetch parafrazira (antistatik fix, 2026-07-22)
- **`WebFetch` na živoj stranici NE vraća doslovan tekst čak i kad se to eksplicitno traži u promptu** — model iza alata sažima/parafrazira sadržaj (potvrđeno na `/antistatik-i-elektroprovodljivi-podovi/`: traženo "doslovan tekst, ne parafraza", dobijen sažetak sa izmenjenim rečenicama). Za bilo kakvu content-parity proveru (live vs. lokal 1:1) ovo je neupotrebljivo — razlike koje treba uočiti su baš na nivou tačnih formulacija.
- **Ispravan put**: `curl -A "Mozilla/5.0" <live-url> -o file.html`, pa Python (`re.sub` da se ukloni `<script>`/`<style>`, tagovi zamene za newline, `<[^>]+>` strip) da se dobije čist tekst red-po-red iz stvarnog HTML-a. Pouzdano i brzo (~100-tak linija za tipičnu stranicu), ne zavisi od modela koji "prepričava".

## FAQPage JSON-LD mora ostati sinhronizovan sa vidljivim FAQ tekstom (antistatik fix, 2026-07-22)
- Kad se FAQ odgovor u vidljivom HTML-u menja na WoodMart stranici koja FAQPage schema čuva kao base64-enkodiran `[vc_raw_html]` blob (isti obrazac kao F7.4 pilot na antistatik stranici), **schema se NE menja automatski** — ostaje stara verzija dok se ručno ne ažurira, što stvara vidljiv-sadržaj-vs-schema mismatch (Google penalizuje/ignoriše neusklađenu schema).
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
- Pre bilo kakve izmene, backup sirove vrednosti preko `$wpdb->get_var()` (ne `wp post meta get` CLI ispis, koji može da izgubi/izmeni whitespace u velikom serialized bloku).

## Sopstveni Google API konektor — Ads API ne prihvata service account, GMB/My Business ima nizak default kvota (2026-07-27)
- **Google Ads API tehnički ne podržava service account autentifikaciju uopšte** — bez obzira što je Miroslav imao gotov `claude-mcp-ads` service account ključ u istom GCP projektu kao i GA4/GSC (koji rade odlično preko service account-a), taj ključ je neupotrebljiv za Ads. Jedini put je OAuth 2.0 sa pravim korisničkim nalogom (Desktop/Web client + refresh token) + poseban developer token. Ne gubiti vreme pokušavajući service account rutu za Ads.
- **GA4 Data API i Search Console API prihvataju service account odmah** — dovoljno je dodati service account email kao Viewer/User u GA4 Property Access Management odn. GSC Settings→Users, bez ikakvog OAuth koraka. Ovo je najbrži put kad god je dostupan.
- **"My Business" familija API-ja (`mybusinessaccountmanagement`, `mybusinessbusinessinformation`, `businessprofileperformance`) ima svoj tok grešaka**: prvo `403 SERVICE_DISABLED` dok API nije uključen u projektu (Google daje direktan link za aktivaciju u error poruci — koristan i pouzdan), a ODMAH POSLE uključivanja često sledi `429 Quota exceeded — Requests per minute`. **Ispravka 2026-07-27 (posle 4 neuspela pokušaja kroz 2 sesije + Miroslavljevi screenshotovi Quotas stranice)**: ovo NIJE propagaciono kašnjenje koje samo prođe — kvota je doslovno **0** ("Requests per minute", Value 0, Current usage 0) i tako ostaje. Poseban ručni review proces je potreban, odvojen od običnog "Enable API" koraka i od standardnog Cloud quota-increase toka. **Pažnja — stari link zastareo**: `developers.google.com/my-business/content/prereqs#request-access` je legacy forma i **ZATVORENA** (Google-ova sopstvena stranica na tom URL-u to potvrđuje). Pravi tekući put: `support.google.com/business/contact/api_default` → iz padajućeg menija izabrati **"Application for Basic API Access"** (NE "Quota Increase Request" — ta opcija je samo za naloge koji su već allowlisted i imaju kvotu >0; ako je kvota na 0 kao ovde, ide se preko "Basic API Access"). Ne gubiti vreme na ponovne retry-eve konektora dok ta forma nije popunjena i odobrena.
- **Kredencijali van vault-a rade glatko u praksi**: `ANTASLINE_CONNECTOR_HOME` env var + fajlovi u `C:\Users\Miroslav\antasline-connector\credentials\` — nijednom nije bilo potrebe da tajna dotakne git stablo, čak ni privremeno, čitav tok (kopiranje service account ključeva, OAuth client secret, token.json posle autorizacije) prošao je kroz Bash/Read alate direktno na fajlsistem.

## WebFetch ne čita PDF sadržaj, ali sačuva fajl — koristiti Read na sačuvanoj putanji (Bergo Soft istraga, 2026-07-27)
- `WebFetch` na direktan PDF URL (proizvođački spec-list) vratio je samo opis PDF binarne/metapodatak strukture ("Adobe Illustrator, sv-SE locale...") umesto stvarnog teksta — mali/brzi model iza WebFetch-a ne parsira PDF stream sadržaj.
- Ipak, alat je sačuvao pun binarni fajl lokalno (putanja data u odgovoru, `tool-results/webfetch-*.pdf`) — `Read` alat na TOJ putanji je uspešno izvukao ceo čitljiv tekst (PDF podrška ugrađena u Read, ne u WebFetch).
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
popravki od 07.07 i 21.07.

## Gate stavka može stajati kao „pokriveno skriptom" a da skripta to nikad nije radila (backup „2 lokacije", 2026-08-17)

Checklist je od 10.08 tvrdio da backup builda ide **na 2 lokacije**. Kod je birao **jednu**
destinaciju (`G:` → OneDrive → lokalno), pa se serija razlivala: 10–12.08 na `C:`, 13–14.08 na
`G:`, OneDrive folder uopšte ne postoji. **Nijedan datum nije imao dve kopije.** Ista klasa
greške kao `build-staging-package.sh` 4 dana ranije (exclude pravila „pokrivena", a skripta
nikad pokrenuta).

**Pravilo:** kad checklist tvrdi da nešto radi automatski, dokaz je **artefakt na disku**, ne
red u dokumentu — nabroj fajlove i uporedi veličine. Ovde: dva fajla istog imena i **identičnih
2.946.948.322 B**, arhive otvorene i prebrojane (102.488 unosa), pa onda ✅.
🔵 Uz to: `Compress-Archive` drži celu arhivu u **memoriji** (~2,6 GB pika za 2,8 GB zip) i
upisuje na kraju — ciljni fajl stoji na **0 B** ~25 min, što izgleda kao da je posao stao.
🔴 I: rutina koja briše temp fajl samo na uspehu curi na svakom padu — zatečeno 13 dump-ova /
751 MB.


## `wp-load.php` bootstrap se zaglavio na CLI pozivu (visi bez greške), Yoast ima sopstvenu keš tabelu nezavisnu od postmeta (podovi-za-terase fix, 2026-07-27)
- **`require_once wp-load.php` pozvan iz gole PHP CLI skripte se zaglavio** (proces živ, `Responding: True`, CPU raste sporo ali sadržajno ne napreduje ni posle nekoliko minuta) — nijedna od mojih izmena (ni prvi `update_post_meta()` poziv) nije stigla do baze pre nego što je proces ubijen, znači zaglavilo se u samom bootstrap-u, pre mog koda. Verovatan uzrok: neki mu-plugin/plugin radi neuslovljen mrežni poziv na `init`/`plugins_loaded` (license check, update ping i sl.) koji nema internet u ovom okruženju pa čeka na timeout. **Rešenje koje je odmah proradilo**: zaobići WordPress bootstrap potpuno — čist `mysqli`/PDO na `antasline_local` bazu, bez `wp-load.php`, isti princip kao ranije utvrđeno pravilo "koristi `$wpdb->update()` direktno" samo doveden do kraja (ni `$wpdb` ne treba, samo sirov mysqli).
- **Yoast SEO (14+) drži sopstvenu keš tabelu `wp{prefix}_yoast_indexable`** (+ `_hierarchy`, `_seo_links` itd.) sa već-izračunatim `title`/`description`/`open_graph_*`/`twitter_*` poljima po `object_id`. Frontend čita IZ OVE TABELE, ne uvek direktno iz `_yoast_wpseo_title`/`_yoast_wpseo_metadesc` postmeta. **Direktna SQL izmena postmeta bez prolaska kroz WP `save_post`/Yoast hook-ove ostavlja ovu keš tabelu zastarelu** — promena je u bazi, ali se ne vidi na sajtu dok se keš ne osveži. Simptom: `curl` na stranicu i dalje pokazuje STARI `<title>` iako `SELECT` na `_yoast_wpseo_title` postmeta pokazuje nov tekst.
- **Fix**: `DELETE FROM wp{prefix}_yoast_indexable WHERE object_id IN (...)` za pogođene postove — Yoast sam regeneriše red iz trenutnih postmeta vrednosti na sledećem frontend pozivu (potvrđeno: `curl` odmah posle DELETE-a pokazao ispravan novi title/meta description). Ne treba ručno popunjavati `og_title`/`twitter_title` polja — ako su NULL (uobičajeno), Yoast ih izvodi iz `title`/`description` u trenutku renderovanja.
- **Provera pre zaključka "izmena nije uspela"**: uvek proveriti direktno SQL vrednost postmeta PRE nego što se posumnja da UPDATE nije prošao — u ovom slučaju baza je bila tačna, problem je bio isključivo u prezentacionom kešu.
- 🔴 **Dopuna 2026-07-30 — keš tabela može sama biti pokvarena (mojibake), ne samo zastarela.** Nađeno sitewide (93 naslova/103 opisa): postmeta je bio čist UTF-8, ali `title`/`description` u `wpgs_yoast_indexable` su bili DVOSTRUKO-enkodovani (em-crta → "ÔÇö", č/š/ž → "─Ź"/"┼í" i sl.) — tačan mehanizam nastanka nije rekonstruisan, ali ograničen isključivo na `object_type='post'`, samo ta dva polja (breadcrumb_title/og/twitter/term nedirnuti). U ovom slučaju **DELETE + auto-regeneracija nije bezbedna preporuka** (kako gornji fix predlaže) — briše `id`, a `[[naucene-lekcije#Yoast indexable hijerarhija]]`-tip bag (ancestor_id pokazuje na obrisan red) se tad može ponoviti za bilo koju stranicu čiji je breadcrumb zavisio od tog ID-ja. Sigurniji fix za OVU vrstu kvara (postmeta ispravan, keš pokvaren): direktan `UPDATE wpgs_yoast_indexable yi JOIN wp{prefix}_postmeta pm ON pm.post_id=yi.object_id AND pm.meta_key='_yoast_wpseo_title' SET yi.title=pm.meta_value WHERE yi.title != pm.meta_value` (isto za `_yoast_wpseo_metadesc`→`description`) — zadržava `id`/hijerarhiju, samo ispravlja sadržaj polja. `wp yoast index --reindex` (CLI) takođe briše i ponovo gradi SVE redove (isti hijerarhija-rizik u širem obimu) i dodatno je nepouzdan na ovom sajtu — probni poziv je pogodio `Fatal error: Maximum execution time of 300 seconds exceeded` u `js_composer` color-picker modulu tokom WP bootstrap-a.

## Isti tekst (NAP adresa) živeo u 5 odvojenih DB redova istovremeno — SQL `REPLACE()` bez re-serijalizacije kad su string dužine jednake (NAP fix `/kontakt/`, 2026-07-27)
- Live `/kontakt/` (post 558, Kallyas/SiteOrigin tema) je čuvao istu grešku ("11050 Beograd" umesto tačnog "11000") u **5 nezavisnih mesta odjednom**: `panels_data` (aktivni SiteOrigin builder sadržaj, dupliran u 2 postmeta reda — poznat "dupli postmeta" obrazac), `_panels_data_preview` (admin-editor preview, takođe dupliran u 2 reda) i `zn_page_builder_els` (orphan Zion Builder sadržaj od ranijeg pokušaja, nije renderovan ali postoji). Šesti izvor (`widget_sow-editor` opcija, neaktivan sidebar widget) imao je isti tekst ali drugačiji telefonski broj — dokaz da je ovo star, ručno kopiran sadržaj iz više faza sajta, ne jedan izvor istine.
- **Provera šta se STVARNO renderuje pre svake izmene**: `curl` na pravu URL i `grep -c` za obe varijante broja — pokazalo tačno JEDNO mesto gde se "11050" pojavljuje na strani (unutar `panels_data`), dok header top-bar (drugi, nezavisan izvor) već ispravno prikazuje "11000". Bez ovog koraka lako se prevideti dupli/orphan redovi koji ne utiču na frontend, ili obrnuto — promeniti pogrešan izvor i misliti da je live popravljen.
- **Trik za bezbednu izmenu serijalizovanih PHP polja (`panels_data`, `zn_page_builder_els` i sl.)**: ako je zamenski string ISTE dužine kao originalni (`"11050"` → `"11000"`, oba 5 karaktera), `UPDATE ... SET meta_value = REPLACE(meta_value, 'stari', 'novi')` je bezbedan — PHP serijalizacija čuva `s:N:"..."` length-prefiks koji ostaje tačan bez ikakve re-serijalizacije. Da su dužine različite, direktan `REPLACE()` bi pokvario ceo builder (WordPress/SiteOrigin bi odbio da deserijalizuje polje). Za nejednake dužine potrebna je prava re-serijalizacija (PHP `unserialize()`/`serialize()` ciklus), ne string replace.
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
- Prateće pravilo: **izmena skripte koja se ne pokrene nije popravka nego pretpostavka.** Posle svake izmene migracione skripte — jedan pun prolaz u scratchpad, pa `tar -tzf`/`diff` nad rezultatom.

## Lokalni `.htaccess` nikad ne sme u migracioni paket — nosi podfolder `RewriteBase` i briše serverski LSCACHE blok (dry-run, 2026-08-13)
- Build je u podfolderu (`localhost/antasline`), pa lokalni `.htaccess` nosi `RewriteBase /antasline/` i `RewriteRule . /antasline/index.php [L]`. Da je taj fajl prepisao serverski, **svaki zahtev na produkciji** bi otišao na nepostojeću putanju — pad sajta u celosti, odmah, na dan migracije. Uz to bi nestao produkcijski `# BEGIN LSCACHE` blok (LiteSpeed keširanje, na kome visi ceo LCP plan) i cPanel PHP handler.
- Zbunjujuće je što lokalni `.htaccess` **izgleda produkcijski** — sadrži nasleđen cPanel `ea-php81` handler i stari `katastarrudarskogotpada.rs` redirect, jer je nekad kopiran sa live-a. Samo je WordPress blok lokalan. Vizuelna provera „liči na serverski" nije dokaz.
- **Pravilo: `.htaccess` se na serveru EDITUJE, nikad ne prenosi iz builda** (301 blok se dodaje iznad `# BEGIN WordPress`, kako checklist B3 i kaže). Isto važi za `wp-config.php` — oba su per-okruženje, ne per-sadržaj.

## Arhiva slika se ne kompresuje — „paket ≈ pola veličine foldera" je pogrešna pretpostavka za kvotu (dry-run, 2026-08-13)
- `wp-content/uploads` je 2,9 GB na disku; `tar.gz` je **2,71 GB** (ušteda ~6%). Slike su već kompresovane (JPEG/WebP), gzip nema šta da stegne. Pre-flight računica je pretpostavljala ~1,3 GB paket i na osnovu toga je disk-bloker proglašen zatvorenim.
- Drugi deo zamke: obrazac „chunkuj pa sklopi na serveru" **udvostručuje** pik (delovi + sklopljen tar postoje istovremeno) — 2,78 GB postaje 5,56 GB, od 5,87 GB slobodnih, pre backup-a i pre raspakivanja.
- **Pravilo: veličinu paketa izmeriti stvarnim prolazom pre nego što se kvota proglasi dovoljnom**, i planirati pik (delovi + sklopljeno + backup + raspakovano), ne zbir arhiva. Ako postoji SSH, `rsync` izbegava ceo problem — chunkovanje je bilo zaobilaznica za nestabilnu FTP data-konekciju, ne zahtev hostinga.

## Kredencijal u pomoćnoj skripti se ne nađe traženjem — nađe se čitanjem koda iz drugog razloga (FTP lozinka, 2026-08-13)
- FTP lozinka je stajala u čistom tekstu u `ftp-upload-chunks.sh` i `ftp-upload-resume.sh` od 06.08, verzionisana u git-u i sinhronizovana na hosting. Otkrivena je slučajno — skripta je otvarana da bi se videlo **kako se delovi sklapaju na serveru** tokom dry-run-a, ne u nekakvoj bezbednosnoj proveri.
- Bila je u **dva** fajla; prvi nalaz je prijavio samo jedan. **Pravilo: kad nađeš kredencijal u jednom fajlu, odmah `grep` po celom stablu za samu vrednost** (ne za ime promenljive) — pomoćne skripte se kopiraju jedna iz druge, pa se i tajna kopira.
- Obrazac izmeštanja koji je usvojen: vrednost u fajl van repo stabla (`~/antasline-ftp-creds.txt`), skripta ga `source`-uje preko `VAR_FILE="${VAR_FILE:-$HOME/…}"` i **tvrdo pada sa `exit 1` pre ijednog mrežnog poziva** ako fajl nedostaje ili ne definiše očekivanu promenljivu. Tiho nastavljanje sa praznim kredencijalom je gore od pada.
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
- Isto važi za smer 301: `/podloge-za-parkiraliste-cena/` vraća **404 na live-u**, pa joj 301 pravilo uopšte ne treba — dovoljno je draftovati. Pre svakog „dodaj 301", proveriti `curl` na **live** URL: pravilo treba samo onom URL-u koji zaista postoji napolju.

## `open(putanja, 'w')` prazni fajl pre nego što skripta stigne da pukne — vault fajl od 375 KB nestao na `UnicodeEncodeError` (2026-08-13)
- Python `open(p,'w')` **truncate-uje odmah**, pre prvog `write()`. Jednolinijski obrazac `open(p,'w',encoding='utf-8').write(novo)` je zato mina: ako se izraz `novo` sastavlja u istoj liniji i baci izuzetak (ovde: surogatni parovi iz `🔴` escape-ova u emoji-ju), fajl ostaje na **0 bajtova**, a original je nepovratno otišao.
- Spaseno `git checkout -- PROGRESS.md` (Obsidian Git commit star ~40 min) — ali samo zato što je fajl bio verzionisan. Isti obrazac nad `antasline-backups/*.sql` (od 13.08 u `.gitignore`) bi bio trajan gubitak.
- **Pravilo za izmene vault/velikih fajlova iz skripte:** sastaviti ceo sadržaj u promenljivu, upisati u `p + '.tmp'`, pa `os.replace(tmp, p)` (atomično). Emoji se piše kao **stvarni znak**, ne kao `\uXXXX` escape par.

## Live export sa samo `publish` statusom = tih blind spot u celom migracionom planu (draft blind spot, 2026-07-28)
- Live export od 2026-07-05 (`migracija/live-export-2026-07-05/`), izvor istine za `parity-inventar.csv` i ceo F1–F7 tok, sadrži **isključivo postove sa statusom `publish`**. Dve stranice sa realnim GSC saobraćajem (`/sportske-podloge/sportski-podovi-za-teniske-terene/` 552 impr u Q1, `/gumeni-podovi-javne-objekte-i-teretane/` 433 impr / 12 kl) bile su tada `draft` na live-u — nikad nisu ušle u inventar, pa bi 2026-08-31 nestale bez ijednog upozorenja. Otkrivene tek slučajno, kroz 404 dijagnostiku 27.07.
- **Pravilo: svaki naredni live export mora uključivati i draftove** (i `private`/`pending`), pa ih tek onda svesno filtrirati uz zabelešku — a ne ih nikad ni ne videti. Isto važi za bilo koji „popis live stanja": sitemap i sitemap-bazirani skenovi po definiciji ne vide draftove.
- **Šta bi ovo uhvatilo ranije**: unakrsna provera GSC URL liste (28d/90d) protiv `parity-inventar.csv` — svaki live URL sa nenultim impresijama koji NIJE u inventaru je alarm. Ta provera je jednokratno urađena 27.07 na 136 GSC URL-ova (nema drugih velikih slučajeva), ali nije deo rutine.

## GSC podatak „koja stranica drži koji upit" je ono što odlučuje rebuild vs 301 (2026-07-28)
- Standardni `gsc_report.py` agregira po upitu preko celog sajta i ne može da odgovori na pitanje koje se stvarno postavlja pri migracionoj odluci. Skripta `gsc_page_queries.py` (dodata u konektor 2026-07-28) filtrira po `page` dimenziji i vraća upitni klaster po konkretnoj stranici.
- **Zašto to menja odluku, konkretno**: `/gumeni-podovi-javne-objekte-i-teretane/` je „stranica o teretanama" po naslovu, pa je 301 na postojeći lokalni `/industrijski-podovi/podovi-za-teretane-i-fitnes-centre/` delovao očigledno. Upitni podatak pokazuje suprotno — klaster je **materijal** („gumeni podovi" 28 impr, poz. 8,8), ne namena, a ciljna stranica prodaje PVC, ne gumu. 301 bi bio tematski promašaj i izgubio bi ceo klaster. Isti alat je 27.07 rešio i „podovi za terase" kanibalizaciju.
- **Obrazac**: pre svake odluke „301 na nešto slično" — povuci upite obe stranice i uporedi klastere, ne naslove.

## Unutar PHP **single-quoted** stringa navodnik se piše go, bez beksleša (2026-07-28)
- Pri programskoj prepravci generisanog PHP-a ubačen je `<p style=\"…\">` unutar single-quoted PHP stringa. PHP u single-quoted stringu razrešava SAMO `\\` i `\'` — `\"` ostaje doslovno, pa bi u HTML izašao literalni beksleš i pokvario atribut.
- Rođak je postojećeg gotcha-e #12 iz [[migracija/woodmart-sabloni]] (`\xNN` hex escape isto ne radi u single-quoted stringu). Ista klasa greške ponovila se istog dana i u Python heredoc-u pri upisu ove lekcije (`\x` u tripl-quoted stringu = `SyntaxError`) — **kad se piše kod koji generiše kod, sadržaj ide u fajl pa se čita, ne u ugnježden string literal.**
- Uhvaćeno pre nego što je bilo vidljivo, jer verifikaciona skripta eksplicitno traži `style=\"` artefakt u HTML izlazu — vredi zadržati tu proveru u standardu verifikacije novih stranica.

## WPBakery `_wpb_shortcodes_custom_css` se ponekad tiho ne regeneriše posle `wp_update_post()` iz CLI-ja — HTTP/H1 provera to ne hvata (W7 F4.1, 2026-07-29)
- Kod 3 od 32 programski izmenjene stranice (`5438`, `16684`, `16685`) je `el_class`/`css=".vc_custom_heroF4{ID}{background-image:...}"` atribut ispravno sačuvan u `post_content`, red se renderovao sa tačnom klasom (`vc_custom_heroF4{ID}` je bio u `class=` atributu HTML-a), ali `<style data-type="vc_shortcodes-custom-css">` blok koji bi taj CSS stvarno emitovao **nikad se nije pojavio** — stranica je ostala vizuelno navy uprkos ispravnom markup-u.
- Uzrok nije regex/format: ručan poziv `wpbakery()->parseShortcodesCss($post->post_content, 'custom')` nad istim (aktuelnim) sadržajem radi ispravno i vraća tačan CSS string. Problem je u `Vc_Base::buildShortcodesCss()` — funkciji koju `save_post` hook zove da izračuna i upiše `_wpb_shortcodes_custom_css` postmeta; kod ova 3 posta je taj poziv (u trenutku `wp_update_post()`) očigledno video prazan/star sadržaj, izračunao prazan CSS i legitimno **obrisao** meta ključ (`delete_metadata` grana kad je `empty($css)`) — potvrđeno proverom `wpGs_postmeta`: ključ `_wpb_shortcodes_custom_css_updated=1` postoji, ali `_wpb_shortcodes_custom_css` sam nedostaje. Zašto baš ova 3 od 32 (ne svih 32, ne random) nije utvrđeno.
- **Fix**: `wpbakery()->buildShortcodesCss($id, 'custom')` pozvano ručno POSLE izmene, nad već sačuvanim sadržajem — pouzdano piše ispravnu vrednost.
- **Provera koja ovo hvata**: HTTP 200 + 1×H1 + prisustvo klase u `el_class` **nije dovoljno** — sve troje je bilo zeleno na ova 3 posta. Jedino Chrome vizuelni pregled (slika se ne vidi) ili direktna provera `SELECT meta_value FROM wpGs_postmeta WHERE post_id={ID} AND meta_key='_wpb_shortcodes_custom_css'` (ili grep na `<style data-type="vc_shortcodes-custom-css">` u renderovanom HTML-u) otkriva problem. **Pravilo: posle svake programske izmene koja unosi WPBakery `css=` atribut, provera mora uključiti i taj postmeta ključ, ne samo status/H1/klasu.**

## Schema može mesecima da „postoji" a da nikad nije emitovana — postmeta/sadržaj nije dokaz (pickleball 16616, 2026-07-28)
- Na `/teren-za-pickleball/` je `kses` pojeo oba `<script type="application/ld+json">` omotača (F7.15 obrazac), pa je stranica istovremeno: (a) prikazivala **5,3 KB sirovog JSON-a kao vidljiv tekst** posetiocu i (b) emitovala **nula** custom scheme. Trajalo je mesecima a niko nije primetio — jer je JSON bio na samom dnu, ispod kontakt-bloka, i jer standardne provere gledaju „ima li schema u sadržaju", ne „šta stranica stvarno emituje".
- **Provera koja ovo hvata** (dodata u verifikacioni skript): iz renderovanog HTML-a izvući sve `<script application/ld+json>` blokove i `json_decode`-ovati ih; zatim iz istog HTML-a skinuti sve tagove i tražiti `@context` / `"@type"` / `acceptedAnswer` u **vidljivom tekstu** — ako se tamo pojave, `<script>` omotač je nestao.
- **Sistemska posledica**: jedan otvoreni „rizik" iz [[PROGRESS]] (izmišljene recenzije kao Product `aggregateRating` na toj stranici) sve vreme uopšte nije bio aktivan, jer Google taj blok nikad nije parsirao. **Pre nego što se pokvarena schema „samo vrati u `<script>`", proveriti šta se tim vraćanjem PRVI PUT aktivira** — u ovom slučaju bi to bile fabrikovane recenzije, cena `0.00 EUR / InStock` na katalog-režim sajtu i `image` koja pokazuje na nepostojeći fajl. Popravka pokvarenog bloka nije neutralna operacija.
- **Kad se na jednoj stranici otkrije da su recenzije, cena i slika izmišljeni — ostatak istog bloka tretirati kao neproveren.** Ovde su `sku`/`mpn` ostali, ali označeni za potvrdu, ne prihvaćeni kao tačni.

## Referentni vault fajl koji "ne postoji" možda samo nije stigao još — proveri `git pull` pre nego što proglasiš blokerom (staging V4, 2026-08-13)
- cPanel prompt je referencirao `migracija/promptovi/2026-08-13-staging-full-restore-v4.md` (MD5 tabela, obavezna za integritetnu proveru pre raspakivanja) — fajl nije postojao u lokalnom kloniranom vault-u na cPanel serveru kad je sesija počela. `git fetch`/`git log origin/main` je pokazao da je commit koji ga dodaje stigao na GitHub tek posle početka sesije (Obsidian Git auto-sync na ~10 min, tri-surface workflow iz [[CLAUDE]] §9). `git pull` ga je povukao usred sesije.
- **Pravilo: kad prompt referencira konkretan vault fajl koji izgleda da ne postoji, prvo `git pull`/`git fetch` pre nego što se to prijavi kao STANI-blokada.** Ovo posebno važi za pakete/prompt-ove pravljene "sada" na drugoj površini (lokal) — sinhronizacija ima kašnjenje od par minuta do ~10 min, ne odmah.

## Server nema `htpasswd` CLI — `openssl passwd -apr1` daje kompatibilan Apache hash (staging V4, 2026-08-13)
- `htpasswd -bc` je vratio `command not found` na cPanel serveru. Zamena bez dodatnog paketa: `openssl passwd -apr1 '<lozinka>'` generiše APR1-MD5 hash koji Apache/LiteSpeed prihvata identično, upisan ručno u `.htpasswd` kao `korisnik:hash`.
- Vezano: kad se paket namerno NE prepisuje serverski `.htaccess` (ispravna odluka da se sačuva postojeći Basic Auth), ta odluka tiho pretpostavlja da serverski fajl **postoji**. Ako je ceo docroot ranije obrisan (kao ovde — staging obrisan par dana pre ove sesije), pretpostavka pada bez greške: raspakivanje samo ostavlja prazninu, staging ostaje kratko potpuno otvoren dok se to eksplicitno ne proveri (`head -20 .htaccess` posle svakog raspakivanja koda kad prompt to traži — ne preskakati ovaj korak ni kad "izgleda kao formalnost").


## `Read` bez `limit` povlači 2000 linija — kod nas to zna biti 50k+ tokena iz jednog poziva (token audit, 2026-08-18)
- `DNEVNIK-NAPRETKA.md` je bio 988 KB / 6.320 linija. Jedan `Read` bez `limit`-a = prvih 2000 linija = **160 KB ≈ 52k tokena**, a na otvaranju sesije treba ~10 poslednjih unosa. Fajl koji staje u 2000 linija (`reference/naucene-lekcije.md`, 233 KB / 1.483 linije) ulazi **ceo** — ~75k tokena.
- Izmereno iz transkripata (`~/.claude/projects/<slug>/*.jsonl`, `usage` polje): dve od pet sesija su u prvih 12 poruka narasle **+65k i +70k**, a prvi `Read` pozivi su bili PROGRESS i master plan **bez limita** — master plan čak dvaput (prvo ceo, pa isti fajl sa `limit 150`; prvo čitanje čist gubitak).
- **Pravilo: pre `Read`-a na nepoznat fajl uraditi `wc -c`.** Preko ~40 KB → `head -N`, `sed -n 'OD,DOp'` ili `grep -rn` (kod `grep`-a u kontekst ulaze samo pogođene linije, bez obzira na veličinu fajla).

## „Append na kraj“ u append-only ledgeru koji je newest-on-top = tiho nevidljivi unosi (token audit, 2026-08-18)
- `DNEVNIK-NAPRETKA` je newest-on-top, ali su tri uputstva doslovno nalagala suprotno: `CLAUDE-CODE-instrukcija-CPANEL.md` („DODAJ (append) **na kraj**“), `CLAUDE-CODE-instrukcija.md` („red **na kraj**“) i `CLAUDE.md` §9.1 („→ append `[cpanel-live]` unos“). Rezultat: 4 unosa (06-23, 07-10, 07-30, 08-13) završila su na dnu fajla.
- Posledica nije bila kozmetička — unos od 13.08 je bio „praktično nevidljiv“ i **propušten iz PROGRESS tabele**, što je otkriveno tek naknadno. Ni `Read` ga ne bi našao: bio je na liniji 6291, a `Read` staje na 2000.
- **Pravilo: kad se popravlja simptom u podacima, potraži tekst uputstva koji ga proizvodi.** Rotacija bi sredila fajl, ali bi ga sledeći `[cpanel-live]` unos ponovo pokvario.
- Vezano: rotacija/sortiranje takvog fajla mora ići **po datumu parsiranom iz naslova**, nikad po poziciji linije — inače skript zalutale unose arhivira u pogrešan mesec.

## Bash heredoc u Claude Code alatu skuplja dvostruke obrnute kose crte — za skripte sa escape-ovima koristi Write (token audit, 2026-08-18)
- `python - <<'EOF'` sa `'EOF'` u navodnicima treba da prenese sadržaj doslovno, ali je `\n` u izvoru stizalo do Pythona kao pravi prelom reda, a `.replace('\','/')` kao neterminisan string. Jednostruki `\d` u regexu prolazi; dvostruki ne.
- **Fix:** skripte sa escape sekvencama pisati `Write` alatom u fajl pa pokrenuti `python fajl.py`. Isto važi i za srpske navodnike: `„tekst"` unutar Python stringa u dvostrukim navodnicima prekida string na ASCII `"` — koristiti `'...'` ili `„...“`.

## Mešani CRLF/LF fajlovi: normalizacija celog fajla ga naduva (token audit, 2026-08-18)
- Skript za čišćenje razmaka je u prvom prolazu **povećao** `migracija/arhiva/2026-08-11-legacy-cpt-sadrzaj.md` za 2.354 B, jer taj fajl ima **1.144 CRLF i 2.354 LF** reda, a logika „ako fajl sadrži CRLF, vrati sve na CRLF“ je konvertovala i LF redove.
- **Pravilo: čuvaj završetak reda po liniji** (`cr = seg.endswith('
')`), ne po fajlu. Vault ima obe konvencije (136 LF : 47 CRLF : 1 mešan), `core.autocrlf=true` ih normalizuje tek pri commit-u.

## Pre renumeracije sekcija — inventar referenci sa kontekstom, ne globalna zamena (CLAUDE.md, 2026-08-18)
- `CLAUDE.md` je imao **dve sekcije numerisane 9** (WORKFLOW I ALATI i KLJUČNE LEKCIJE), a podsekcije workflow-a su nosile brojeve 8.1–8.7. Deset spoljnih referenci na „§9“ bilo je dvosmisleno u oba smera.
- Od 15 pogodaka na `§10` u vault-u, **samo 9 je gledalo u `CLAUDE.md`** — ostalo su bile reference na `/woodmart-theme` §13/§14, `chrome-web-platform-2026` §12 i „§6 ovog plana“. Globalna zamena bi ih polomila.
- **Postupak koji je prošao čisto:** (1) inventar `(fajl, linija, broj, kontekst)`, (2) razdvajanje živih uputstava od datiranih zapisa — zapisi se **ne** prepravljaju, (3) imenovane zamene sa `assert count == 1` po svakoj, (4) završna semantička provera: svaka živa referenca upoređena sa stvarnim naslovom ciljne sekcije, (5) mapa starih→novih brojeva ostavljena u samom dokumentu.
- Usput se ispostavilo da je jedna referenca bila pogrešna i **pre** renumeracije (`migracija/w1-polish-red-cekanja.md` je GEO pravilo pripisivao `CLAUDE.md` §10, a ono živi u `/antasline-sesija` W2) — inventar sa kontekstom hvata i takve, globalna zamena ne.
