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
