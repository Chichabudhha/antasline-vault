---
name: obogati-proizvod
description: Obogaćivanje WooCommerce proizvoda na lokalnom AntasLine buildu po standardnom šablonu (atributi, galerija, opis struktura, Yoast, Product schema, cross-linkovi). Koristi kad Miroslav kaže "obogati proizvod X", "sredi proizvode", "product enrichment" ili pri radu na W1 zadatku obogaćivanja proizvoda.
---

# Obogaćivanje proizvoda — AntasLine lokalni build

Radni tok za dovođenje Woo proizvoda na "obogaćen" standard. Jedan batch
(3–5 proizvoda iste linije) po sesiji. Radi se ISKLJUČIVO na lokalnom
buildu (`C:\xampp\htdocs\antasline`, DB `antasline_local`, prefiks `wpGs_`),
nikad na live sajtu.

## Preduslovi (uvek, redom)

1. Pročitaj `[[migracija/woodmart-sabloni]]` — 10 dokumentovanih gotcha-a važi i ovde
2. Backup baze pre bilo kakve izmene:
   `C:\xampp\mysql\bin\mysqldump -u root antasline_local > C:\xampp\htdocs\antasline-backups\antasline_local_YYYY-MM-DD_pre-<opis>.sql`
3. Proveri trenutno stanje proizvoda upitom (opis_len, kratak_len, galerija, atributi, cena, Yoast) pre izmena — ne diraj ono što je već obogaćeno

## Standard "obogaćen proizvod" (svih 8 tačaka)

1. **Globalni Woo atributi** — konačan set POMIREN 2026-07-09 (odluka M,
   polish Faza 1 batch #1). 18 `pa_*` taksonomija u dve grupe:
   - **Filter-set (8)** — idu u shop layered-nav widgete (Faza 1 #8):
     `pa_debljina` · `pa_materijal` · `pa_boja` · `pa_montaza` ·
     `pa_protivklizna-svojstva` · `pa_vatrootpornost` · `pa_antistatican` ·
     `pa_sertifikacija`
   - **Spec-only (10)** — samo za tabelu specifikacija, NE u filtere:
     `pa_dimenzije-ploce` · `pa_nosivost` · `pa_oblik` · `pa_sirina` ·
     `pa_duzina-rolne` · `pa_otpornost-na-udar` · `pa_otpornost-na-hemikalije` ·
     `pa_tvrdoca-shore-a` · `pa_zakosene-ivice` · `pa_elektricni-otpor`
   - NE kreirati: `pa_primena` (pokriveno F6 `namena-*` tagovima) · `pa_boje`
     (postoji `pa_boja`) · `pa_garancija`/`pa_poreklo` (tek uz datasheet
     potvrdu, additive kasnije). Dupli `pa_color` obrisan 2026-07-09; 251
     import-smeće term_relationships red (attachment/orphan object_id-jevi)
     očišćen — postojeći termini su realan live vokabular, dodele kreću od 0.
   - Dodela: termini + `wp_set_object_terms` + **obavezno `_product_attributes`
     postmeta** (serialized niz — bez njega se atribut ne prikazuje na stranici).
   Popuni SAMO iz stvarnih podataka (postojeći opisi, live stranice, tehnička
   dokumentacija proizvođača) — nikad ne izmišljaj specifikacije.
1b. **Standardi SA LINKOVIMA** (F7, 2026-07-07): svaki proizvod gde je primenjivo
   dobija sekciju sa relevantnim standardom(ima), link ka zvaničnom izvoru i
   jednu rečenicu ZAŠTO je standard bitan kupcu. Tipični standardi u branši:
   DIN 51130 (klizavost R9–R13) · EN 13501-1 (požarna klasa Bfl-s1...) ·
   IEC 61340-5-1 / BS EN 61340-5-1 / BS EN 61340-5-2 (ESD zaštita) ·
   EN 14041 (podne obloge — CE) · EN 1270 (košarkaške konstrukcije) ·
   EN 14877 (spoljašnji sportski podovi) · ISO 9001/14001 (proizvođač) ·
   REACH (hemijska bezbednost).
   Izvor podataka: WebSearch/WebFetch proizvođačkih datasheet-ova
   (`ecotileflooring.com`, `bergoflooring.com`, `ergomat.com`) ili WebFetch
   postojeće live antasline.com stranice ako već navodi standard za taj proizvod.
   🔴 **TVRDO PRAVILO: standard/spec se navodi SAMO ako je potvrđen u
   datasheet-u, na zvaničnom sajtu proizvođača, ili na postojećoj live
   stranici. Bez potvrde → ne pominje se. Ništa se ne izmišlja.**
1c. **Namena tagovi** (F6, `product_tag` taxonomy, prefiks `namena-`): svaki
   proizvod — postojeći ili nov — dobija bar 1 `namena-*` tag preko
   `wp_set_object_terms($id, [...], 'product_tag', true)` (append, ne
   replace). Trenutna lista termina (2026-07-07): `namena-magacin-hala`,
   `namena-radionica`, `namena-sport-dvorana`, `namena-sportski-teren-otvoreni`,
   `namena-esd`, `namena-garaza`, `namena-terasa`, `namena-stala` — proširi
   listu kad novi proizvod pokrije namenu koje nema (novi termin + ikonica
   istog stila u `woodmart-sabloni` F7.2 setu). Ako proizvod već ima namensku
   landing stranicu (F6 troslojni model), tag ga automatski uvlači u njen grid
   bez izmene te stranice.
2. **Cena**: od–do po m² SAMO ako je Miroslav dao cifre — proveri
   `[[reference/cenovnik]]` (M10) pre svega. Inače bez `_price`, a u opisu
   blok "Cena na upit" + CTA forma. Ne izmišljati cene.
   **M9 odluka (2026-07-07): katalog režim** — Woo "Dodaj u korpu" dugme se
   menja na "Zatraži ponudu" (pre-popunjena forma → `/hvala-za-poruku/`,
   vidi W1 zadatak 1.8). Ovaj šablon i ta odluka su usklađeni po defaultu.
3. **Galerija**: 3–6 slika u `_product_image_gallery` (attachment ID-jevi,
   zarezom odvojeni). Slike biraj iz postojećih uploads (115 importovanih) —
   pretraži `wpGs_posts` `post_type=attachment` po imenu proizvoda/linije.
4. **Struktura opisa** (`post_content`):
   - intro pasus = direktan odgovor šta je proizvod i za koga (GEO pravilo)
   - HTML tabela specifikacija (iz atributa)
   - "Primena" sekcija (use-cases, interni linkovi)
   - "Ugradnja" sekcija (klik-sistem, bez lepka — gde važi)
   - 3 FAQ pitanja
   - CTA: telefon **072** (`tel:+381692340072`) + link ka kontakt formi
   - `post_excerpt` (kratak opis) = 1–2 rečenice benefita
5. **Yoast**: `_yoast_wpseo_title` + `_yoast_wpseo_metadesc` postmeta za svaki
   proizvod. Title ≤60 znakova sa ključnom reči, metadesc ≤155.
6. **Product JSON-LD schema**: brand, name, description, image, offers
   (`priceSpecification` samo ako ima cene, inače bez offers cene). FAQ blok
   dobija FAQPage schema — pazi da se ne duplira sa temom.
7. **PDF tehnički list**: ako postoji PDF u uploads — linkuj; ako ne postoji,
   zabeleži u dnevnik kao #ceka-miroslav. Ne praviti prazan link.
8. **Cross-link trougao**: opis proizvoda linkuje ka svojoj kategoriji
   (Layout Builder landing postoji za svih 10) i ka relevantnoj silo stranici
   (`/industrijski-podovi/`, `/sportske-podloge/`…); silo stranica dobija link
   nazad ako ga nema.

## Tehničke gotcha-e (naučeno, ne ponavljati greške)

- `wp_insert_post`/`wp_update_post` iz CLI **skida raw HTML/JSON-LD** (kses bez
  ulogovanog korisnika) → sadržaj sa `<script type="application/ld+json">`
  upisuj direktnim `$wpdb->update` na `post_content` + `clean_post_cache($id)`
- Yoast taksonomija koristi `WPSEO_Taxonomy_Meta::set_values()` (množina!);
  za postove/proizvode je obično `update_post_meta` na `_yoast_wpseo_*`
- Yoast indexable keš ručno invalidirati posle direktnih DB izmena
- Slug kolizije: postojeći slug (i attachment slug!) blokira novi — proveri
  `get_page_by_path` ponašanje; pravilo sufiks `5` ako mora novi slug
- PHP skripte piši u scratchpad, pokreći sa `C:\xampp\php\php.exe skripta.php`
  (wp-load.php bootstrap); komande >965 bajtova pucaju — uvek fajl, ne inline
- Bash brace expansion `{a,b}` pravi literalne foldere — koristi petlje

## Verifikacija (obavezno po proizvodu, pre upisa u dnevnik)

- [ ] `http://localhost/antasline/proizvod/<slug>/` vraća 200
- [ ] Tačno 1×H1 na stranici
- [ ] Product + FAQPage JSON-LD validan i bez dupliranja (grep rendered HTML)
- [ ] Sve slike galerije se učitavaju (200)
- [ ] Interni linkovi vraćaju 200
- [ ] Yoast title/metadesc u `<head>`
- [ ] Tabela specifikacija se renderuje (wpautop nije razbio markup —
  HTML u jednoj liniji unutar grid blokova)

## Zatvaranje sesije

1. Unos u `[[DNEVNIK-NAPRETKA]]` (vrh fajla): koji proizvodi, šta je dodato,
   koje gotcha-e, ime backup fajla, skripte
2. Red u `[[PROGRESS]]` tabeli "Urađeno"
3. Štikliraj napredak u `[[2026-07-06-MASTER-PLAN-V2]]` (W1 zadatak)
4. Otvorena pitanja → #ceka-miroslav tag (cene, PDF-ovi, slike koje fale)

## Redosled obrade (money-first)

1. Ecotile linija: E500/7 (16538), ESD 7mm (16542), E500/10 (16540)
2. Košarkaške konstrukcije: Lite Shot 325 (16544), Mini Shot 225 (16546),
   MicroShot 125 (16548), koš Street Sport (16532), zglobni obruč (16536)
3. Mosolut Heavy (16530) + Bergo Unique (16534 — pazi na kanibalizaciju sa
   stranicom `/spoljnje-podne-obloge/bergo-unique/`: proizvod = transakcioni
   ugao, stranica = edukativni, obostrani cross-link)
4. DuraStripe trake (16518–16524) — batch, sličan sadržaj
5. Ergomat odbojnici/bumperi (16476–16516) — batch šablon, kratke varijante
6. Senzori (16526, 16528)

## Tvrda pravila

- ❌ Ništa što prodaje epoksid ([[CLAUDE]] §1 — conquest pravilo)
- ❌ Ne izmišljati cene, specifikacije, garancije — "na upit" / #ceka-miroslav
- ❌ Live sajt se ne dira
- ✅ Backup pre svake destruktivne izmene
- ✅ Yoast ostaje (ne RankMath)
