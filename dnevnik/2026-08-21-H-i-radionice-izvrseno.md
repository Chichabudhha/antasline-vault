---
tip: sesija
alat: claude-code
datum: 2026-08-21
blok: SEO / kanibalizacija
status: zavrseno
---

# Sesija — H (interni linkovi garaže/terase) + radionice post→page konverzija

## Šta je urađeno

M je tražio da se izvrše dve preostale stavke iz `seo/2026-08-13-kanibalizacija-konsolidacija-plan.md`
(H — nikad formalno odobreno) i `dnevnik/2026-08-19-skladista-16687.md` (radionice —
M odluka 19.08 bila "post-live", danas eksplicitno prepisana na "uradi sad").

### 🔴 Nalaz pre izvršenja — H više ne znači ono što je pisalo 13.08

13.08 analiza je predviđala linkovanje **16875 (garaže cena) ↔ 16664** i **2641 ↔ 16873
(terase cena)**. Provera u bazi je pokazala da su **16875 i 16873 draftovane 18.08**
(konsolidacija cenovnih stranica u hubove — `dnevnik/2026-08-18-konsolidacija-cenovnih-stranica.md`),
oba vraćaju 404 lokalno. Garaže: cenovni sadržaj već je fizički prebačen u 16664, dodatni
link ka draft/404 stranici je besmislen — **H-1 nema šta da se radi, već rešeno bolje**.
Terase: cenovni sadržaj je otišao u `/spoljnje-podne-obloge/` (16590), ne u 2641 kako je
13.08 analiza pretpostavljala. Realna, i dalje neurađena, verzija H-2: uzajamni link
**2641 ↔ 16590**.

### Izvršeno (backup pre svega: `antasline_local_2026-08-21_pre-H-radionice.sql`, 37,4 MB)

| # | Šta | Detalj |
|---|---|---|
| H-2 | 2641 → dodat link ka `/spoljnje-podne-obloge/` | nova rečenica posle postojećeg linka ka `/industrijski-podovi/` |
| H-2 | 16590 → dodat link ka `/pvc-podne-ploce-ili-gumeni-podovi/` | ubačeno u postojeću rečenicu o cenovnoj tabeli, pored linka ka parkiralištu |
| — | 16664 (garaže hub) → dodat link ka novoj radionice stranici | reč "radionice" u postojećoj uvodnoj rečenici postala link |
| — | 16567 (industrijski-podovi hub) → dodat link ka radionice | reč "radionice" u ćeliji tabele modela (500/7 red) postala link |
| **Radionice** | **5637 konvertovan `post` → `page`, isti ID i slug** | `post_parent=0` (flat URL očuvan), `comment_status`/`ping_status` → `closed`, category term relationship uklonjen (taxonomy se ne koristi za page), stara Porto `zn_*` meta i yoast content-score meta obrisani |
| **Radionice** | **Cela stranica napisana u al-* dizajn sistemu** (WPBakery `vc_row`/`vc_column_text`) | hero + rešenje (3 kartice namena) + tabela "koji model za koji tip radionice" (auto-servis/mašinska-CNC/stolarska → Ecotile 500/7 ili 500/10) + foto-grid sa 3 nove AI slike (17883/17884/17885, već su čekale od 18.08) + 4 kartice prednosti + cenovna tabela (4.600–5.500 za 500/7, 6.800 za 500/10) + srodne teme (garaže, epoksid conquest, PVC/guma) + 7-pitanja FAQ + FAQPage JSON-LD + finalni CTA |
| **Radionice** | Rank Math meta | `rank_math_title` (nov, nije postojao), `rank_math_description` (prepisan sa cenom), `rank_math_focus_keyword` nepromenjen ("podovi za radionice"), `_woodmart_title_off=on`, `_thumbnail_id` → 17883 (pod-za-auto-servis) |

## Gotcha — mysql -e sa direktnim UTF-8 string literalom kvari ćirilične karaktere

Prvi pokušaj upisa `rank_math_title`/`rank_math_description` preko `mysql -e "UPDATE ... SET
meta_value='...'"` (direktan string, ne HEX) je **tiho pokvario** em-dash i č/ž/² —
`monta<0xFF>a` umesto `montaža`, razlog: Windows/Git-Bash cp1250 prevod pre nego što string
stigne do MySQL klijenta. Ista klasa greške kao ranije dokumentovani `post_content` gotcha
(18.08, 19.08), samo prvi put uhvaćena na **postmeta** poljima. Fix: isti HEX/UNHEX round-trip,
verifikovan dekodiranjem nazad u fajl (ne u konzolu — i sama konzola mangluje print, pa se
verifikacija mora pisati u fajl i čitati `Read` alatom, ne ispisivati u bash).

🔴 **Pravilo za ubuduće:** SVAKO pisanje bilo kog teksta sa dijakritikom/em-dash/²
u WP bazu — post_content **ili postmeta** — ide isključivo kroz HEX/UNHEX, nikad kroz
`mysql -e` string literal, bez izuzetka.

Drugi gotcha: `mysql -e` sa hex stringom dužim od ~26 KB (ceo `UPDATE ... UNHEX('...')` za
16567, 52 KB hex) puca na `Argument list too long` (Windows cmdline limit). Fix: pisati
SQL u privremeni `.sql` fajl i izvršiti `mysql < fajl.sql` umesto `-e`.

## Gotcha #3 — `post_title` ostaje "curi" nezavisno od `_woodmart_title_off`

Vizuelna provera u browseru (posle svih SQL izmena) je otkrila da je `_woodmart_title_off=on`
sprečio duplikat H1 u glavnom sadržaju (1×H1 potvrđeno), **ali stari `post_title`** (naslov
starog blog posta, "Podovi za radionice i garaže: kad tvoj auto zaslužuje bolje od običnog
betona") i dalje curi kroz sajt-vajd kontakt-formu na dnu stranice ("Zatražite ponudu: {naslov}"),
nezavisno od woodmart title-off flag-a. Ispravljeno: `post_title` prepisan da odgovara H1-u
("Podovi za radionice — auto-servisi, mašinske i stolarske radionice"), isti obrazac kao
16687 (`post_title` == tekst H1). Provereno u browseru posle ispravke — kontakt-forma sada
pokazuje ispravan naziv.

🔴 **Pravilo za ubuduće:** kod svake konverzije post→page (ili bilo koje veće izmene sadržaja
koja menja H1), `post_title` se mora eksplicitno uskladiti sa novim H1-om — `_woodmart_title_off`
rešava samo duplikat H1 u glavnom sadržaju, ne i sva ostala mesta koja čitaju `post_title`
(kontakt-forma widget, breadcrumbs, `<title>` fallback ako Rank Math title nije postavljen,
admin lista). Curl provera markup-a (H1 count, JSON-LD) ovo nije uhvatila — samo vizuelni
scroll kroz celu stranicu u browseru je otkrio problem.

## Verifikacija

- ✅ `/podovi-za-radionice/` → **200**, radi bez ručnog flush-a rewrite pravila
  (`DELETE FROM wpgs_options WHERE option_name='rewrite_rules'` je dovoljno — WP ih
  sam regeneriše na sledećem requestu, nije trebalo wp-cli koji i dalje visi na ovom buildu)
- ✅ tačno **1×H1**, 0 nerenderovanih `[vc_row]`/`[vc_column]` ostataka
- ✅ FAQPage JSON-LD validan, 7 pitanja, parsiran bez greške; stari `BlogPosting` schema
  (iz starog post sadržaja) uklonjen zajedno sa starim tekstom
- ✅ Sve 4 slike (900x600 varijante + hero puna rezolucija) → 200
- ✅ 8 dirnutih/povezanih URL-ova (radionice, garaže, industrijski-podovi, terase, PVC/guma
  post, epoksid conquest, industrijski-pod 500/7, 500/10 stranica) → svi 200, tačno 1×H1
  na svakom
- ✅ `page-sitemap.xml` sada sadrži `podovi-za-radionice` (1), `post-sitemap.xml` više ne (0)
- ✅ post_content i sve izmenjene postmeta vrednosti byte-verifikovane (dekodirano iz baze
  i upoređeno sa izvornim fajlom pre upisa)
- Backup pre izmena: `antasline-backups/antasline_local_2026-08-21_pre-H-radionice.sql` (37,4 MB)

## Dopuna iste sesije — meni, komentari, prevod (M primedba posle prve verzije)

M je posle prve verzije primetio tri stvari koje konverzija `post`→`page` nije sama rešila:

| # | Šta | Rešenje |
|---|---|---|
| 1 | Stranica nije bila u meniju | Nova `nav_menu_item` stavka (ID **17990**) ubačena u "Glavni meni 2026" (term_taxonomy_id 390), pod **Industrija → Po delatnosti**, odmah posle "Garaže i auto-servisi" (menu_order 27, sve stavke od 27 nadalje pomerene +1). Objekat cilja page 5637. |
| 2 | Stari komentari sa posta (2, oba stvarna — pitanje kupca + odgovor firme) su nestali kad je `comment_status` postavljen na `closed` tokom konverzije | `comment_status` vraćen na **`open`** — komentari su i dalje u bazi (nikad obrisani), sad se opet prikazuju. WoodMart `page.php` prikazuje komentarsku sekciju na stranicama kad je theme opcija `page_comments=1` (potvrđeno u `xts-woodmart-options`) **i** (`comments_open()` ili postoji bar 1 komentar) — oba uslova sad ispunjena. |
| 3 | Naslov komentarske sekcije bio je na engleskom ("2 thoughts on 'Naslov'") — WoodMart core string, `_nx()` sa kontekstom `comments title` | Nov filter `ngettext_with_context_woodmart` u `woodmart-child/functions.php` (odmah posle postojećeg `gettext_with_context_woodmart` bloka, isti obrazac kao ostali prevodi u fajlu) — "Jedan komentar na „X"" / "N komentara na „X"". Efekat je **sajt-vajd**, ne samo na ovoj stranici — svaki drugi post/page sa otvorenim komentarima dobija ispravan prevod istim filterom. |

Verifikovano u browseru: meni stavka vidljiva na tačnom mestu u dropdown-u, oba stara komentara i forma "Ostavite odgovor" prikazani, naslov sekcije na srpskom ("2 KOMENTARA NA „PODOVI ZA RADIONICE...""). `php -l` čist na izmenjenom `functions.php`.

**Dopuna 2 (M primedba, isti dan):** `#comments.comments-area` je imao `padding: 0` — naslov sekcije nalegao je direktno na red iznad (kontakt-forma widget), bez ijednog piksela razmaka. Dodato pravilo u `antas-design.css` (`.comments-area { padding: var(--al-gap) 0; }`) — ponovna upotreba postojećeg tokena za vertikalni ritam sekcija (`--al-gap`, isti kao `.al-section`), simetrično gore/dole. Pravilo je **globalno** (svaka stranica/post sa vidljivim komentarima), pa je regresija provizorno provereno na 2298 (`kako-napraviti-teren-za-basket...`, 40 komentara — najveći test slučaj na sajtu) — isti simetričan razmak, bez neželjenih efekata.

**Dopuna 3 (M primedba, isti dan) — "Predaj komentar" dugme bilo zeleno + audit stranice:**

Pregled cele stranice (console, network, 87 zahteva) — **nema zaostalog Porto/starog CSS-a ni JS-a, nema 404, nema grešaka u konzoli.** Sve učitano su ili WoodMart core delovi (`css/parts/*.css`, `al-asset-diet.php` učitava samo ono što stranica koristi), child theme fajlovi (`antas-design.css`, `al-video-facade.js`, `al-lightbox.js`), ili očekivani spoljni tracking (GTM/GA4/Ads/FB — deo BLOK A merenja, ne smeće).

Zeleno dugme **nije zaostatak** — WoodMart core (`post-types-mod-comments.css`) stilizuje `.comment-form .submit` preko globalne teme-vajd Customizer promenljive `--btn-accented-bgcolor: #83b735` (WoodMart demo zelena, nikad usklađena sa brendom). Ta promenljiva se koristi na **desetinama WooCommerce elemenata** (cart, checkout, product loop dugmad, wishlist, my-account...) — namerno **nije dirana globalno** da se ne pipne nešto neprovereno na kupovnom toku, van obima ovog zadatka.

Umesto toga: skopiran fix u `antas-design.css`, `#comments .comment-form .submit` → `var(--al-red)` / hover `var(--al-red-dark)` (isti par boja kao `.al-btn`). ID prefiks `#comments` je obavezan — parent-ov `post-types-mod-comments.css` ima istu specifičnost (2 klase) i učitava se POSLE child theme CSS-a u `<head>` (redosled potvrđen preko `document.querySelectorAll('link[rel="stylesheet"]')`), pa bez ID-a cascade order pobeđuje parent i boja ostaje zelena uprkos "ispravnom" pravilu. Verifikovano u browseru: dugme narandžasto (`rgb(240,77,34)` = `#F04D22`), uklapa se sa ostatkom CTA dugmadi na stranici.

## Napomena — svesno van obima

**16615** `/podovi-za-detailing-radionice-i-servise/` — otvorena stavka iz 19.08 ("spojiti u
5637 ili suziti, odlučiti zajedno") **nije dirana**. Ta odluka zahteva čitanje 16615 sadržaja
i procenu preklapanja, nije bila deo eksplicitnog naloga za ovu sesiju ("H i radionice").
Ostaje otvorena stavka za sledeću sesiju.

## Veze

- [[seo/2026-08-13-kanibalizacija-konsolidacija-plan]] §3.2/§3.3 (H, sada superseded ovim unosom)
- [[dnevnik/2026-08-19-skladista-16687]] (M odluka 19.08 "post-live", danas prepisana)
- [[dnevnik/2026-08-18-konsolidacija-cenovnih-stranica]] (razlog zašto je H-1 postao besmislen)
- [[seo/2026-08-18-ai-vizuali-promptovi]] §2 (poreklo 3 slike korišćene ovde)
- [[reference/cenovnik]] (izvor cena 500/7 i 500/10)
