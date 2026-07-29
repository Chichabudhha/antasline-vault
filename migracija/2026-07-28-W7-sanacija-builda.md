---
tip: plan
workstream: W7
azurirano: 2026-07-28
status: odobreno-ceka-izvrsenje
---

# W7 — Sanacija lokalnog builda (dizajn, sadržaj, navigacija)

Nastalo iz prolaza kroz sajt 2026-07-28 (M je nabrojao ~30 zamerki). Plan je
odobren istog dana, izvršenje ide po fazama. Nadređeni plan:
[[2026-07-06-MASTER-PLAN-V2]] · alati: [[migracija/alati/_README]] ·
šabloni: [[migracija/woodmart-sabloni]]

## Zašto ovako

~30 zamerki se svodi na **četiri sistemske greške**, ne na trideset zasebnih
problema. Zato plan ide po uzrocima: jedna CSS izmena zatvara 29 kartica na 4
stranice, jedan skript zatvara 11 stranica sa praznim prostorom.

Redosled je namerno „popravke pre sadržaja" — F1 menja izgled svih stranica
odjednom, pa se sadržajni rad u F2 radi nad već ispravnim rasterom.

---

## Dijagnoza — četiri sistemske greške

### 1. `.al-card__title` pada preko `.al-card__body`
`antas-design.css:290` — `position:absolute; bottom:16px; color:#fff`. Naslov je
pozicioniran u odnosu na `.al-card` (koja je `position:relative`), **a ne u odnosu
na `.al-card__media`**. Kad kartica ima i `body`, bela verzalna reč legne preko
sivog teksta na beloj podlozi → naslov nevidljiv, meta-tekst izgužvan.

To je tačno ono što M vidi na Expona stranici kao „tekst na slikama nije čitljiv".
Slike su čiste — swatch teksture nemaju utisnut tekst (provereno okom).

**Pogođeno: 29 kartica / 4 stranice** — `16684` expona-click (12), `16685`
vinil-podovi = Commercial (12), `16686` vinil-podovi-za-restorane (4), `5438`
sportske-podloge (1).

### 2. Nesparen `</div>` u `post_content`
WPBakery renderuje `[vc_column_text]` kao
`<div class="wpb_text_column"><div class="wpb_wrapper">…`. Jedan višak `</div>`
prerano zatvori `.wpb_wrapper`, ostatak sekcije ispadne iz kolone → **velika bela
rupa**. Potvrđeno vizuelno na `vestacka-trava-za-terase` (rupa odmah posle tabele
modela).

**Pogođeno: 11 stranica** — `16673` (+2), `16659`, `16669`, `16670`, `16672`,
`16675`, `16677`, `16678`, `16680`, `16687`, `17004` (svaka +1).

> Naivna regex pretraga „praznih divova" daje i lažne pogotke — `bergo-unique`,
> `bergo-elite`, `bergo-easy`, `ecotile-5005`, `zastitne-podloge`, `podloge-za-parking`
> izlaze samo zbog **color-swatch kockica** (namerno prazan `<div>`). Nisu bug.

### 3. Grupa „spoljne obloge" nikad nije povezana sa katalogom
7 stranica grupe → **0 linkova ka `/proizvod/`**, iako proizvodi postoje
(`16534` Bergo Unique, `16815` XL, `16823` Elite, `16843` Solid, Radici/Condor trave).
6 od 7 nema `_thumbnail_id`; jedina koja ima (`16659` bergo-xl) nosi **pogrešnu**
bazensku sliku (att. `5057`). Hub `16590` linkuje samo 3 od 6 svoje dece.

### 4. Meni je zamenjen ali stari sadržaj nije povučen
Aktivan meni je term **67** („O firmi", lokacija `main-menu` u
`theme_mods_woodmart-child`); term **28** („Glavni izbornik", 66 stavki) je
**mrtav** — nije dodeljen nijednoj lokaciji.

Otud „stranice nema u meniju": `15580` podloge-za-parking postoji samo u mrtvom
meniju, a **već je zamenjena** novom `16589` koja jeste u aktivnom meniju.

Uz to: **26 gotovih `al-*` stranica nije nigde linkovano**, 5 stavki menija ima
prazan naslov, jedna je duplikat.

---

## Raspored

| Faza | Obim | Procena | Status |
|---|---|---|---|
| **F1** Globalne popravke (tema, CSS, prevodi, šablon) | 13 stavki | 1 sesija | ✅ 2026-07-28 |
| **F2** Sadržaj (Expona, Bergo, spoljne obloge, slike) | 9 stavki | 2–3 sesije | ✅ **ZATVOREN 2026-07-29** (2.1–2.4 · 2.5–2.7 · 2.9) — 2.8 blokiran na M, v. Otvoreno |
| **F3** Meni i navigacija | 5 stavki | 1 sesija | ✅ **2026-07-29** (3.1–3.4 + 15580 deo 3.5) — ostaje dizajn-parity za `5791`/`15793` |
| **F4** Hero fotografije po stranici | 2 stavke | 1 sesija | ⏳ |

---

## F1 — Globalne popravke — ✅ ZATVORENO 2026-07-28

> **Ispravke dijagnoze, izmerene pri izvršenju** (detalji: [[DNEVNIK-NAPRETKA]]):
> - **1.1 obim je 28 kartica / 3 stranice**, ne 29 / 4 — `5438` je lažan pogodak
>   (11 kartica media+naslov i 6 samo-telo, nikad u istoj kartici). Brojanje u
>   `post_content`-u ne dokazuje da su oba u ISTOJ `.al-card`; mereno nad DOM-om.
> - **1.6 prelom je 1024px, ne 767px**; `.wd-toolbar` je `height:55px`, a
>   `--wd-sticky-nav-h` ne postoji (`--wd-sticky-nav-w` je *širina* bočnog menija).
> - **1.8 je već bio rešen u F7.21** — dugme je `--al-red` sa `inset:0;margin:auto`,
>   odstupanje centra 0px; opis „plavo + `translate(-50%,-50%)`" je bio zastareo.
> - **1.4 pravilo** „ukloni zatvarajuće **na kraju bloka**" ne bi popravilo `16659`
>   (iza viška ide još pasus) ni `17004` (blok nema nijedan otvarajući `<div>`) —
>   alat briše `</div>` na kome bilans padne ispod nule.
> - **1.9 okrugla social dugmad su već bila okrugla** (`wd-shape-circle` → 50%).
> - **1.11**: `Search for posts` ne postoji nigde; `Products` postoji ali samo na
>   `/katalog/`. Trebalo je **tri** filtera (`gettext_`, `gettext_with_context_`
>   za `esc_attr_x` placeholder, `ngettext_` za srpsku množinu).
>
> **Tri odluke M-a, izvršene u istoj sesiji:** futer ikonice → **bele**
> (`filter: brightness(0) invert(1)`, bez diranja SVG fajlova) · play dugme →
> **narandžasto** (`--al-orange`) · katalog → **padajući panel ostaje**, filteri
> se NE sele u levi sidebar (mreža bi pala sa 4 na 3 kolone); widget „Kategorije"
> ostaje na vrhu panela. F1 zatvoren bez repova.

Sve u `woodmart-child` (`css/antas-design.css`, `functions.php`) osim 1.4 i 1.6.
Bekap oba fajla po konvenciji `*.bak-YYYY-MM-DD`.

| # | Stavka | Gde |
|---|---|---|
| 1.1 | Naslov kartice preko teksta (29 kartica) | `antas-design.css:290` |
| 1.2 | Tabele bez bočnog skrola na mobilnom | `antas-design.css:458` |
| 1.3 | Naslovi u tabeli `font-weight` 600 → **500** | `antas-design.css:473` |
| 1.4 | Prazan prostor — 11 stranica | nov `al_fix_divs.php` |
| 1.5 | Mreža 3 kolone sa 5 kartica | `16673` |
| 1.6 | Kolačići preko mobilnog sticky bara | `mu-plugins/al-tracking-gtm-consent.php:58` |
| 1.7 | Mobilni CTA — duplirane ikonice (crvene preko crnih) | WoodMart / child |
| 1.8 | YouTube dugme: brend boja + centriranje trouglića | `js/al-video-facade.js` |
| 1.9 | Futer: boja ikonica, okrugle social, automatska godina | `antas-design.css:797–830` |
| 1.10 | Kontakt: dugme brend + „Pošaljite poruku" + svetliji placeholderi | CF7 + CSS |
| 1.11 | Prevodi (`Products`, `Search for posts`, `Newer/Older`) | `functions.php:584` |
| 1.12 | Blog: sakriti „posted by + datum", ukloniti social share | WoodMart opcije |
| 1.13 | Katalog: sidebar kategorija, filteri levo, bez „Show 9/12" | `16736` + Shop layout |

**1.1** — kad kartica ima `body`, naslov ide u tok tamnom bojom, a tamni gradijent
preko slike se gasi (na ravnim teksturama izgleda kao kvar):
```css
.al-card:has(.al-card__body) .al-card__title { position: static; color: var(--al-navy);
  display: block; padding: 18px 22px 0; font-size: clamp(18px,1.4vw,22px); }
.al-card:has(.al-card__body) .al-card__media::after { display: none; }
.al-card:has(.al-card__body) .al-card__body { padding-top: 8px; }
```
Bez ijedne izmene sadržaja stranica.

**1.2** — `min-width:640px` tera bočni skrol. Izuzetak
`.single-product .al-table { min-width:0 }` (red 479) je isti problem već rešio na
proizvodima (M zamerka 2026-07-09) — **proširiti pravilo na sve tabele** umesto da
ostane izuzetak. Na ≤576px tabele sa >3 kolone prelaze u „stacked" prikaz
(`thead` sakriven, `td::before` nosi naziv kolone iz `data-label`); `data-label` se
generiše filterom u `the_content`, isti pristup kao `al_enhance_content_images()`.

**1.4** — nov `migracija/alati/al_fix_divs.php` po uzoru na `al_move_section.php`
(proba bez `apply`): broji `<div>`/`</div>` po `[vc_column_text]` bloku, uklanja
**samo** nesparene zatvarajuće tagove na kraju bloka. Redosled: prvo `16673`
(potvrđen slučaj), pa ostalih 10. Nikad „popravi sve naslepo" — proba pa diff.

**1.6** — `#asc-handle{position:fixed;bottom:12px;left:12px;z-index:99998}` sedi
tačno na WoodMart sticky bottom nav-u. Podići u `@media(max-width:767px)`:
`bottom: calc(var(--wd-sticky-nav-h, 56px) + 12px)`. Isto i `#asc-banner`
(z-index 99999, `bottom:0`) — treba mu donji `padding` za visinu bara.

**1.8** — trouglić: optičko centriranje traži `translate(-46%,-50%)`, ne `-50%,-50%`.
Boja: narandžasta `--al-orange` (plava se gubi na tamnim thumbnail-ima).

**1.12** — prvo tražiti WoodMart prekidač (Theme Settings → Blog), tek ako ga nema
`remove_action` u child temi. **Bez CSS `display:none`** — ostaje u DOM-u i u
čitačima ekrana.

---

## F2 — Sadržaj

> **2.1–2.4 ✅ ZATVORENO 2026-07-29.** Ispravke dijagnoze, izmerene pri izvršenju
> (detalji: [[DNEVNIK-NAPRETKA]]):
> - **2.1: `16667` VEĆ ima sekciju „EXPONA program"** — i to pokvarenu (kartica
>   „EXPONA Design" vodila na stranicu Commercial-a; proza opisivala Simplay kao
>   „klik sistem", a Simplay je `loose-lay`). Sekcija je **prepisana, ne dodata**.
> - **2.2: treći PDF je duplikat** — `2019/10/Brochure-EXPONA-FLOW-English…` je
>   bajt-identičan (isti md5) prilogu `5593`. Uvezena samo dva Design PDF-a.
> - **2.4: napomena je netačna SAMO na `16918`** — tehnički list sadrži baš ono što
>   navodi kao nepoznato (42 dezena, klase 23/34/43, R10/DS, IAC Gold). Na **`16919`
>   Living Clic je TAČNA** (nula dokumenata i fotki za tu kolekciju) → ostavljena.
> - **2.4: „slike iz `2020/12/`" za Design ne postoje** — sve `*design*` datoteke su
>   zapravo Commercial/Simplay/R-Tile („Designboden" = nemački „dizajn pod"). Fotke
>   su izvučene iz proizvođačeve brošure.
> - **Usput:** `16685` je imao nezatvoren `[vc_column_text]` (6/5) — zatvoren zamenom
>   sekcije. Kartice po odluci M vode na PROIZVODE, pa su pod-stranice linkovane iz
>   proze hub-a (inače bi nova 17252 bila 27. siroče).

### 2.1 Expona — zameniti dezene proizvodima
Odluka M: mreža od 12 tekstura se **uklanja**, umesto nje 4 stvarna proizvoda.
Nova sekcija „EXPONA program" na `16667`, `16684`, `16685`, `16668` (na pod-stranici
se izostavlja kartica te iste stranice → 3 kartice):

| Kartica | Fotografija (`uploads/`) | Vodi na |
|---|---|---|
| EXPONA Clic 19dB | `2026/2026/01/kancelarija-expona-clic.jpg` | `/proizvod/expona-clic-19db-wood-klik-daska/` |
| EXPONA Commercial | `2021/09/podovi-za-kancelariju-expona-commercial.jpg` | `/proizvod/expona-commercial-lvt-vinil-plocice/` |
| EXPONA Flow | `2020/12/Expona-Flow-Cafeteria-9862-gross.jpg` | `/proizvod/expona-flow-lvt-vinil-podovi-u-rolnama/` |
| EXPONA Simplay 19dB | `2022/06/LVT-pod-za-kafice-expona-simplay.jpg` | `/proizvod/expona-simplay-19db-loose-lay-lvt/` |

> 🔴 `uploads/2026/2026/01/` je **duplo ugnežden folder** — 2.492 fajla, **0 referenci
> iz baze**, nedostupni preko normalnih WP URL-ova (posledica live importa). Sadrži
> upotrebljive ambijentalne fotke. Uvoze se kroz `al_import.php` kao svaka datoteka
> sa diska, čime ulaze u medijateku ispravno.

12 tekstura se **ne briše** — sele se u Woo galeriju `16917` (Clic) odnosno `16914`
(Commercial), gde su na svom mestu kao izbor dezena.

### 2.2 Expona — engleske brošure
`5641 Broschure-EXPONA-COMMERCIAL-Deutsch…` je jedina nemačka; Flow već ima englesku
(`5593`). Traže se EN verzije Commercial i Simplay brošure na objectflor.de.

🔴 **Preuzimanje fajla sa interneta traži M-ovo odobrenje** — javiti tačan URL,
naziv i veličinu, pa tek onda uvoz i zamena linka u sekciji „Dokumentacija".

Na disku već leže **tri PDF-a van medijateke** koji se uvoze bez skidanja:
`2019/11/BROCHURE-EXPONA-DESIGN.pdf` · `2019/11/Expona-Design-tehnički-podaci.pdf` ·
`2019/10/Brochure-EXPONA-FLOW-English…pdf`.

### 2.3 Expona — Simplay pod-stranica
Kartica na `16667` sad vodi u prazno. Napraviti `/lvt-podovi…/expona-simplay/` po
obrascu Click stranice (proizvod `16916` ima thumb + 4 galerijske + 4 PDF-a).

### 2.4 Proizvodi 16918 Design i 16919 Living Clic
Bez slike i bez galerije. Uvezti `BROCHURE-EXPONA-DESIGN.pdf` + tehnički list i slike
iz `2020/12/`. **Ukloniti iz opisa netačnu rečenicu** „tehnički list još nije dobavljen
od distributera" — dokument leži na disku.

> **2.5–2.7 ✅ ZATVORENO 2026-07-29** (izvršeno jutarnjom sesijom 09:33–09:41, verifikovano
> i upisano popodnevnom). Dijagnoza je i ovde bila delom zastarela: **svih 7 stranica grupe
> je već imalo `_thumbnail_id`** (ne 1 od 7), `16659` više nije nosila pogrešnu bazensku
> `5057`, a **hub je linkovao dvosegmentne `/spoljnje-` URL-ove** kojih na lokalu nema
> (lokalni slug je oduvek `spoljne-`, bez „j") — to su bili mrtvi linkovi, ne 3-od-6 pitanje.
> Posle popravke hub linkuje **6/6** dece, 5 od 7 stranica linkuje svoj Woo proizvod.
> `.al-swatch` komponenta zamenila **84 inline kvadrata na 5 stranica**.
>
> 🔴 **2.7, poslednji red (slug `spoljnje-` → `spoljne-`) je pogrešno postavljen.** Lokal je
> već `spoljne-`, live je `spoljnje-` — dakle pitanje nije „da li preimenovati lokal" nego
> **„da li prihvatiti razliku prema live-u i upisati 301"**. Ostaje M-ova odluka.

> **2.9 ✅ ZATVORENO 2026-07-29.** 9 postova bez naslovne slike → 0 · `term recount` (5
> ustajalih brojki, 5 kategorija ispalo prazno) · „O nama" dobio dva reda logotipa.
> **40 proizvoda bez slike ostaje** — to je serijski posao za skil `/obogati-proizvod`,
> vodi se odvojeno. Usput: 🔴 **mrtvi legacy CPT-ovi su 404-ovali celu grupu „spoljne
> obloge"** (v. [[DNEVNIK-NAPRETKA]] 2026-07-29) — popravljeno filterom u child temi.

### 2.5 Bergo Unique (`16679`) — slike proizvoda i upotrebe
Sad ima samo 6 SVG ikonica + 3 montažne fotke + YT thumb. Dodati galeriju iz
postojećih: `11492` tabela boja · `14148` ploča · `2020/11/bergo-Unique-cedar-wood.jpg` ·
`14705` terasne ploče · `2021/07/Bergo-unique-ceedar-wood-Tara1.jpg` ·
`2025/04/restoran-Rakovica-Bergo-unique.jpg`. Link ka proizvodu `16534`.

### 2.6 Bergo Unique — kockice boja
Sad inline `48×48px` kvadrati sa `gap:16px`, 25 komada, bez ijedne CSS klase.
Prebaciti u `.al-swatch` u `antas-design.css`: **oblik Bergo ploče** (zaobljeni uglovi
+ mreža rebara kao SVG `background-image`), **veće** (72×72px), **manji razmak**
(`gap:10px`), i `display:grid; grid-template-columns:repeat(auto-fill,minmax(84px,1fr))`.

### 2.7 Grupa „spoljne obloge" — slike, modeli, cross-linkovi
Važi za svih 7 (`16590` hub, `16659`, `16662`, `16665`, `16673`, `16679`, `16681`):
- svaka dobija `_thumbnail_id`; `16659` gubi pogrešnu bazensku `5057`
- hub `16590` dopunjuje linkove ka svih 6 dece (sad ima 3 — fale `bergo-easy`,
  `podovi-za-bazene`, `vestacka-trava-za-terase`)
- svaka linkuje svoj Woo proizvod; `16662` dobija link ka `bergo-unique` (kartica
  „Bergo Unique za bazene" sad **nije** link, a nosi Unique sliku)
- 🔴 slug `spoljnje-` → `spoljne-` menja URL hub-a **i svih 6 dece** — vidi Otvoreno

### 2.8 Veštačka trava — 4 modela → proizvodi (`16673`)
Highlands / Nature / Put / Springgrass vezati za Radici/Condor proizvode
(`16877`, `16885`, `16894`–`16906`). Mapiranje potvrditi iz **tabele specifikacija**
na stranici (visina trave, broj uboda/m²), ne po imenu naslepo.

### 2.9 Slike i kategorije koje nedostaju
- **9 postova bez `_thumbnail_id`**: `17027`, `16614`, `16612`, `16610`, `3398`,
  `16609`, `3318`, `3257`, `2699`
- **40 od 94 proizvoda bez glavne slike** — Geoplast (7), Radici/Condor (9), Bergo (5),
  Ecotile rampe (4), sportska oprema (6), Expona (2), R-Tile (2), ostalo. Serijama po
  liniji, skil `/obogati-proizvod`
- ✅ **ZATVORENO 2026-07-29** — **2 posta samo u „nekategorizovano"**: `6824` → **Pod za
  prodavnice i radnje** (141), `6874` → **Industrijski podovi** (51); obrisane prazne
  `tereni` (59), `pod-za-garaze` (52, duplikat živog „Garažni podovi" 140) i
  `Uncategorized @sr` (1). 15 → 12 kategorija.
  🔴 Term 1 je bio `default_category` — WP ne dozvoljava brisanje podrazumevane
  kategorije, pa je redosled morao biti: prvo `default_category` → 64, pa brisanje.
  Term 64 preimenovan `Некатегоризовано` → `Nekategorizovano`, ostaje kao fallback (0 postova).
  🔴 **Rep za sledeću F2 sesiju:** `count` u `wpGs_term_taxonomy` je ustajao sitewide
  (`Poslovni prostor` 65 tvrdi 4, ima 0 publish) → `wp term recount category`, nije
  rađeno bez najave jer menja brojke svuda.
- **Reference na „O nama" (`571`)** — 20 imena klijenata kao goli tekst, bez ijednog
  logotipa. Home (`16550`) već ima `.al-logo-row` (grayscale + hover,
  `antas-design.css:421`) i 3 foto-kartice. Preneti obe komponente na „O nama".
  Galerija „Iz naših radova" tvrdi „kliknite na sliku" a slike **nisu klikabilne** —
  umotati u lightbox

---

## F3 — Meni i navigacija

> **✅ ZATVORENO 2026-07-29** (osim dizajn-parity dela 3.5). Ispravke dijagnoze,
> izmerene pri izvršenju (detalji: [[DNEVNIK-NAPRETKA]]):
> - **3.2 „5 stavki bez naslova" nije defekt** — `wp_update_nav_menu_item()` namerno
>   prazni `post_title` kad je labela ista kao naslov ciljne stranice; stavka nasleđuje
>   naslov i renderuje se ispravno. Isti tip lažnog pogotka kao F1.1.
> - **3.2 rupa u `menu_order` i duplikata redosleda nije bilo** (0/0). Duplikat je bio
>   *cilja*: „Sport" i „Sportske podloge" obe na `5438`.
> - **Stvarno stanje gore od dijagnoze:** term 67 je imao **31 dete pod jednom grupom**,
>   gnežđenje pomereno („Veštačka trava" pod *Industrijom*, „basket" pod *Terasama*).
> - **3.4 siročadi je 40, ne 26** — razliku od 14 pokrivaju **utility meni** (term 280,
>   ide preko WoodMart header builder-a a NE preko `nav_menu_locations` — zato ga
>   `get_nav_menu_locations()` ne prijavljuje) i futer.
> - **3.5: `15580` nema bolji Yoast.** `16589` ima merljivo bolji (nosi cenu
>   2.800 din/m² + imena Geoplast modela) → **prenos nije izvršen**, suprotno planu.
>   Umesto toga: `15580` → `noindex`, dolazni linkovi sa `16550` i `16876` prevezani
>   na `16589`, 301 upisan u [[migracija/redirect-mapa-FINAL.csv]].
> - **Nova struktura ima 6 grupa, ne 5+Cene sa običnim padajućim menijima** — sve
>   grupe su mega-meni (`_menu_item_design=sized`) jer WoodMart walker **ne resetuje
>   `design` između grupa**; grupa bez eksplicitnog dizajna nasledi susedov i ostane
>   bez širine (panel se skupi na 182px). Vidi [[reference/naucene-lekcije]].
> - **Labele skraćene** jer se meni prelamao u drugi red na 1500px: „Poslovni prostori"
>   → **Poslovni**, „Specijalni podovi" → **Specijalni**.
>
> Struktura živi u skripti `migracija/alati/job-w7f3-meni.php` (jedini izvor istine —
> meni se ne uređuje ručno u adminu, nego se skripta pusti ponovo).

**3.1 Bekap menija** pre svega — `mysqldump` samo `wpGs_terms`, `wpGs_term_taxonomy`,
`wpGs_term_relationships` + `wpGs_posts`/`wpGs_postmeta` gde je
`post_type='nav_menu_item'`.

**3.2 Popravke postojećeg** — 5 stavki bez naslova (`16697`, `16701`, `16702`,
`16703`, `16711`, `16713`), duplikat „Sportske podloge" (`16694`), rupe u
`menu_order` (12, 18, 28, 34).

**3.3 Nova struktura** — 5 grupa + „Cene", 2 nivoa (WoodMart mega-meni udobno nosi 2;
treći nivo se rešava **kolonama mega-menija**, ne trećim
`_menu_item_menu_item_parent` nivoom):

```
SPORT (5438)          ▸ Tereni: 16680 pickleball · 16670 padel · 17028 tenis
                        16584 basket 3x3 · 16581 futsal · 16582 hokej · 16583 stoni tenis
                      ▸ Dimenzije: 16586 teren · 16585 tabla · 16688 tenis · 17027 fudbal
                      ▸ Oprema: 16676 · 16657 konstrukcije · 16677 reflektori · 16674 galerija
INDUSTRIJA (16567)    ▸ Po delatnosti: 17017 hemijska/prehrambena · 17018 zdravstvo
                        16660 hale · 16664 garaže · 17020 teretane
                      ▸ Po proizvodu: 16658 ESD · 16678 ploče · 17026 PVC ploče · 16666 obeležavanje
                      ▸ Saveti: 16675 preko epoksida · 17025 najčešća pitanja
TERASE I DOM (16590)  ▸ Bergo: 17019 hub · 16679 Unique · 16681 Elite · 16659 XL
                      ▸ 16662 bazeni · 16673 veštačka trava · 17029 gumeni podovi
POSLOVNI (16667)      ▸ LVT: 16684 Clic · 16685 Commercial · 16668 Flow · NOVA Simplay
                      ▸ 16142 maloprodaja · 16683 maloprodajni objekti · 16686 restorani
                        16669 kancelarije
SPECIJALNI (#)        ▸ 16111 Isotrack · 16665 sajmovi · 16589 parking · 16663 rentiranje
                        5791 štale · 15793 zaštita trave
CENE (nov hub)        ▸ 16874 industrijski · 16873 terase · 16876 parkiralište · 16875 garaže
```

**3.4 26 siročadi — raspored.** 23 ulaze u meni po gornjoj šemi. Tri idu
kontekstualno, ne u navigaciju:
- `16600` hvala-za-poruku — **nikad u meni**, to je GA4 `generate_lead` cilj
- `17004` planer-terena — alat, CTA sa sportskih stranica
- `16875` podovi-za-garaze — u „Cene" hub (preklapa se sa `16664` garaže-i-autoservisi)

Nova **„Cene" hub stranica** se pravi u F3 (`al-grid--2`, 4 kartice + kratak uvod) —
4 cena-stranice su gotove i nose komercijalne upite, a sad su nevidljive.

**3.5 Stare stranice — dizajn-parity** (odluka M: isti tekst, nov `al-*` omotač):
- `5791` podovi-za-stale — prevesti WPBakery u `al-section`; dodatno **nema
  `_woodmart_title_off=on`** (otud dupla naslovna traka) i nema `_yoast_wpseo_metadesc`
- `15793` zaštitne-podloge — isto (H1 je „Bergo Solid", ne rešetke za travu!)
- `15580` podloge-za-parking → `noindex` + 301 na `16589`, uz **prenos njenog boljeg
  Yoast title/metadesc na 16589** (sad postoji SEO kanibalizacija na „parking" upitima)

---

## F4 — Hero fotografije

**4.1** — sve stranice dele istu plavu `al-section--navy` pozadinu. Posle F7.23
postoji ~170 kuriranih WebP fotografija. Dodati `.al-hero--photo` po uzoru na
postojeći `.al-promo-photo` (`antas-design.css:1099` — `background-image` + tamni
`::before` sloj radi kontrasta teksta) i dodeliti fotografiju po temi stranice.

🔴 **Izbor fotke po H1, nikad po slugu** — pravilo iz [[migracija/alati/_README]].

**4.2** — home hero (`16550`) je dobra fotka ali mutna na desktopu. Naći original u
hi-res folderima preko [[analiza/2026-07-28-foto-inventar.csv]], uvezti kroz
`al_import.php`. Za hero podići granicu na **2400px** (galerije ostaju 1600px).

---

## Alati (ne pisati nove)

| Alat | Za šta ovde |
|---|---|
| `alati/al_import.php` | uvoz fotki + `al-section` blok (JSON posao) — F2 svuda |
| `alati/al_move_section.php` | premeštanje sekcija + inspekcija strukture |
| `alati/contact_sheet.php` | mozaik kandidata pre izbora fotke (štedi kontekst) |
| `alati/al_regen_sizes.php` | regeneracija `al-*` veličina posle uvoza |
| skil `/obogati-proizvod` | 40 proizvoda bez slike (F2.9) |
| **nov** `alati/al_fix_divs.php` | F1.4, po uzoru na `al_move_section.php` |

Poziv: `php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file <skript>`,
uz `$env:PATH="C:\xampp\mysql\bin;$env:PATH"`. **`wp` nije globalno instaliran**;
DB `antasline_local`, root bez lozinke, `C:\xampp\mysql\bin\mysql.exe`.

---

## Provera (posle svake faze)

1. `mysqldump` pre izmena; bekap `post_content` (skripte to rade same); bekap tema
   fajlova kao `*.bak-YYYY-MM-DD`
2. **Sitewide 199 URL-ova** — HTTP 200 · tačno 1× `<h1>` · 0 PHP grešaka.
   Posle F2 i F4 **obavezno i provera slika** (`src`+`srcset`+`href` → `HEAD`):
   404 na slici ne obara status stranice, pa ga standardna provera ne vidi — tako je
   uhvaćeno 212 pokvarenih `woocommerce_single` slika
3. **Chrome vizuelno na 1500px i 390px** — obavezno za F1 (4 od 13 stavki su čisto mobilne)
4. Po fazi:
   - **F1** — `expona-click` (naslovi čitljivi), `vestacka-trava-za-terase` (nema rupe),
     bilo koja tabela na 390px (bez bočnog skrola), futer, `/kontakt/`, `/katalog/`
   - **F2** — 0 stranica grupe bez `_thumbnail_id`; svaka linkuje ≥1 proizvod
   - **F3** — `wp_get_nav_menu_items` vraća 0 stavki bez naslova; svih 26 siročadi ima
     ulaz; Yoast breadcrumb tačan na 3 uzorka iz različitih grupa
   - **F4** — LCP na home ne raste; `fetchpriority=high` i `preload` samo na hero slici

---

## Otvoreno — traži M-ovu odluku u toku rada

1. **Slug `spoljnje-` → `spoljne-`** (F2.7) menja URL hub-a i svih 6 pod-stranica.
   Predlog: uraditi (pravopisna greška, grupa nije u top-15 GSC URL-ova), ali uz 301
   unos u [[migracija/redirect-mapa-FINAL.csv]]. Javiti pre izvršenja.
2. **Engleske Expona brošure** (F2.2) — skidanje sa objectflor.de je radnja ka spolja;
   javiti URL/naziv/veličinu i čekati „da".
3. **Mapiranje 4 modela veštačke trave na proizvode** (F2.8) — ako specifikacije ne
   poklapaju nijedan proizvod jednoznačno, pitati umesto nagađati.
4. **40 proizvoda bez slike** (F2.9) — ako za neku liniju nema fotografija u arhivi,
   prijaviti nedostatak umesto stavljati sliku koja implicira tuđi posao (pravilo iz
   [[migracija/alati/_README]]; već važi za `16677` i `16671`).
