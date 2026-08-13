---
tip: sesija
alat: claude-code
datum: 2026-08-13
blok: C
status: zavrseno
---

# Sesija — FAZA 2: layout, CSS i UI popravke (5 stranica → 3 sistemska uzroka)

> M je dao listu od 6 zamerki na 5 stranica. Nijedna nije rešena po stranici — sve su
> svedene na tri uzroka u dizajn sistemu / temi i popravljene tamo, pa je popravka
> automatski pokrila i stranice koje nisu bile prijavljene.

## 0. Preduslov — okruženje nije radilo

Ni MySQL ni Apache nisu bili pokrenuti. Apache je startovao normalno; MySQL je pao na:

```
[ERROR] mysqld.exe: Aria recovery failed. Please run aria_chk -r on all Aria tables
        and delete all aria_log.######## files
[ERROR] Plugin 'Aria' registration as a STORAGE ENGINE failed.
[ERROR] Could not open mysql.plugin table. Some plugins may be not loaded
```

**Treći put** — u `C:\xampp\mysql\data\` već stoje `aria_log*.bak-20260710` i
`.bak-20260721`. Isti fix: `aria_log.00000001` i `aria_log_control` preimenovani u
`.bak-20260813`, MariaDB se odmah podigla. Aria u XAMPP-u nosi samo `mysql.*` sistemske
tabele — WP podaci su InnoDB, ništa se ne gubi. Poruka „Could not open mysql.plugin"
je **posledica**, ne uzrok; pravi red je onaj o Aria recovery-ju iznad njega.

## 1. Uzrok — dve susedne `.al-section` istog tona

### Nalaz

`.al-section` nosi `padding: var(--al-gap) 0` = 72px na desktopu. Kad se dve sekcije
**iste** pozadine (`--paper`+`--paper` ili `--mist`+`--mist`) dodiruju bez dijagonalnog
reza, dobija se 144px jednobojne prazne trake bez ijednog vizuelnog razloga. Uz to:

- WPBakery daje **svakom** `.wpb_content_element` `margin-bottom: 35px`, pa i poslednjem
  u sekciji → još 35px ispod tabela, galerija i grid-ova;
- `wpautop` ostavlja gole `<br>` (~18px) između full-width redova.

Sve četiri M-ove „praznine" su ovaj obrazac.

### Popravka (`antas-design.css`, nova sekcija „FAZA 2")

| # | Pravilo | Efekat |
|---|---|---|
| 1 | `.wpb-content-wrapper > br { display: none }` | ~18px po spoju |
| 2 | spoj istih tonova → `padding-top: 0` na drugoj sekciji | 72px po spoju |
| 3 | `.al-section .vc_column-inner > .wpb_wrapper > .wpb_content_element:last-child { margin-bottom: 0 }` | 35px po sekciji |

Sekcije sa `al-diag-*` su **izuzete** iz pravila 2 (`:not([class*="al-diag"])`) — njima
padding-top nosi sam rez (`calc(var(--al-cut) + 28px)`), skidanje bi poklopilo sadržaj.

Pravilo 3 je generalizacija postojećeg
`.wpb_raw_code:has(> .wpb_wrapper > script:only-child) { margin-bottom: 0 }` (F7, isti
razred problema — nevidljiv blok koji ipak zauzima 35px).

### Izmereno

| Stranica | Pre | Posle |
|---|---|---|
| `/kategorija-proizvoda/kosarkaske-konstrukcije/` (tekst → Filteri) | 199px | **92px** |
| `/sportske-podloge/kosarkaske-konstrukcije/` (ispod tabele modela) | 179px | **72px** |
| `/sportske-podloge/kosarkaske-konstrukcije/` (ispod galerije) | 179px | **96px** |
| `/lvt.../vinil-podovi-objectflor/` (Kolekcije → Primena) | 179px | **76px** |
| `/dimenzije-kosarkaskog-terena/` (Pitanja → Primeri) | 167px | 144px, **sa kontrastnom granicom** |

**Domet:** 15 spojeva na 14 stranica — prebrojano SQL-om nad `post_content`
(16586, 16657×2, 17025, 16111, 16567, 16665, 16666, 16668, 17017, 17018, 17019, 17020,
17026, 17027) — plus Woo kategorija stranice, gde su opis kategorije i grid proizvoda
takođe dva `--mist` reda (ne vidi se u `post_content`, otkriveno merenjem u browseru).

### 🔴 Gotcha — `+` kombinator puca na `wpautop` artefaktima

Prva verzija selektora (`+` i `+ .vc_row-full-width +`) radila je na
`/sportske-podloge/kosarkaske-konstrukcije/` a **nije** na `/industrijski-podovi/`,
iako je markup naizgled isti. Razlika: goli `<br>` iz `wpautop` između redova (nastaje
kad je `[/vc_row]` u `post_content` završen novim redom). CSS `+` traži **tačnu**
susednost i ne preskače prazne markere.

`display:none` iz pravila 1 **ne pomaže** — element i dalje stoji u DOM-u.

Rešenje: nabrojane sve stvarno viđene kombinacije po tonu (6 po tonu, 12 ukupno):

```
X + Y
X + br + Y
X + .vc_row-full-width + Y
X + br + .vc_row-full-width + Y
X + .vc_row-full-width + br + Y
X + br + .vc_row-full-width + br + Y
```

Provera je bila `getComputedStyle(sekcija).paddingTop` po stranici, ne oko — na
`/industrijski-podovi/` je vraćalo `72px` dok je pravilo „trebalo" da radi.

### Kontrastna boja (M je tražio predlog)

Sekcija „Primeri" na `/dimenzije-kosarkaskog-terena/`: `al-section--paper` →
**`al-section--mist`** (`#EEF3F8`). Izbor nije nov ton — ista stranica ga već koristi
dvaput („Tabela", „Varijante"), pa se ritam `paper → mist → paper → mist` nastavlja
umesto da se prekine. Razmak sada čita kao namerna traka, ne kao rupa.

Upis: `$wpdb->update()` uz `substr_count()` proveru da je sidro jedinstveno (F7.24).

## 2. Uzrok — WoodMart deregistruje CF7 CSS, zamenu enqueue-uje samo iz svog elementa

### Nalaz

`woodmart/inc/enqueue.php:591-594`:

```php
wp_deregister_style( 'contact-form-7' );
wp_dequeue_style( 'contact-form-7' );
wp_deregister_style( 'contact-form-7-rtl' );
wp_dequeue_style( 'contact-form-7-rtl' );
```

Zamena je `woodmart/css/parts/int-wpcf7.css`, a nju enqueue-uje **isključivo**
`woodmart_shortcode_contact_form_7()` (`woodmart_enqueue_inline_style( 'wpcf7' )`).
„Brzi upit" (16737) renderujemo sirovim `do_shortcode('[contact-form-7 …]')` iz
`the_content` prio 12 — dakle mimo theme elementa, pa part nikad nije stizao.

Posledica su **oba** M-ova pravougaonika, i to na svih ~55 stranica sa formom:

| Element | Zašto se vidi | Šta ga gasi |
|---|---|---|
| prazan okvir **iznad** „Ime i prezime" | `<fieldset class="hidden-fields-container">` sa `_wpcf7_*` inputima renderuje se kao vidljiv fieldset | `div.wpcf7 .hidden-fields-container{display:none}` iz `int-wpcf7.css` |
| narandžasti okvir sa „!" **ispod** dugmeta | `.wpcf7-response-output` iz `parts/mod-notices-general.css` (**jeste** učitan) dobija `display:block`, `--notices-warning-bg` (`#E0B252`) i `\f100` ikonicu | `form div.wpcf7-response-output{display:none}` iz `int-wpcf7.css`; prikazuje se tek uz `.sent`/`.invalid`/… na formi |

Dijagnostika je išla preko `getComputedStyle` u browseru, ne čitanjem CSS-a — u HTML-u
se `int-wpcf7.css` **pojavljuje** kao string (WoodMart lazy lista), ali nema `<link>` tag.
`grep -o '<link[^>]*int-wpcf7[^>]*>'` na renderu vraća prazno; to je bio dokaz.

`/kontakt/` (forma 16593 kroz WPBakery CF7 element) part **ima** oduvek — otud je bag
bio nevidljiv na jedinoj stranici koja se najčešće proverava.

### Popravka

`woodmart_enqueue_inline_style( 'wpcf7' )` u quick-quote filteru
(`woodmart-child/functions.php`, pre sastavljanja `$html`), uz `function_exists` gard.

Naš styling ostaje jači po specifičnosti — `.al-section .wpcf7 input[type=submit]`
je (0,3,1) vs teminih `div.wpcf7 input[type=submit]` (0,1,2) — pa se dugme, polja,
placeholderi i `not-valid-tip` nisu promenili. Provereno vizuelno pre/posle.

⚠️ Razmatrana i odbačena alternativa: sakriti oba elementa iz `antas-design.css`.
Odbačena jer maskira uzrok i ostavlja ostatak part-a (spinner, `submitting` stanje)
neaktivnim.

## 3. Uzrok — `clip-path` paralelogram odseca vertikalne krakove `inset` rama

### Nalaz

`.al-btn--ghost` crta ram sa `box-shadow: inset 0 0 0 2px currentColor`, a oblik dolazi
od `clip-path: polygon(12px 0, 100% 0, calc(100% - 12px) 100%, 0 100%)`. Kosi rez pada
tačno preko levog i desnog kraka rama → dugme se renderuje kao **dve odvojene vodoravne
crte**.

Na navy hero-u (jedno ghost dugme pored punog crvenog CTA) to prolazi kao potpis; u
„Dokumentacija" gridu od 4 kartice čita se kao nedovršen okvir. Uz to:

- dugmad su bila `inline-block` levo poravnata u vrhu kartice, a kartice u gridu jednake
  visine → mrtva zona ispod dugmeta (kartica 139px, dugme 68px);
- dvoredna labela („Deklaracija o performansama") ispadala je iz paralelograma crtanog
  za jedan red;
- `.al-btn--ghost:hover` postavlja `rgba(255,255,255,0.1)` — na `--paper`/`--mist`
  sekcijama **belo na belom = nikakav hover feedback**.

### Popravka

```css
.al-card:has(> .al-card__body > .al-btn:only-child) { display: flex; }
.al-card:has(> .al-card__body > .al-btn:only-child) > .al-card__body {
	display: flex; align-items: center; justify-content: center;
	width: 100%; padding: 18px;
}
.al-card__body > .al-btn:only-child {
	display: block; width: 100%; text-align: center;
	font-size: clamp(18px, 1.25vw, 21px); line-height: 1.2;
	padding: 16px 24px 14px;
	clip-path: none;
}
```

+ hover na svetlim sekcijama:

```css
.al-section--paper .al-btn--ghost:hover,
.al-section--mist .al-btn--ghost:hover,
.entry-content .al-btn--ghost:hover,
.wd-entry-content .al-btn--ghost:hover { background: var(--al-navy); color: #fff; }
```

Ram prati `currentColor`, pa na hover postaje beo na navy podlozi — bez dodatnog pravila.

**Domet:** obrazac `.al-card__body` sa samo jednim `.al-btn` prebrojan u `post_content` —
**3 stranice**: 16684 `expona-click` (4), 16685 `vinil-podovi` (5), 17252
`expona-simplay` (4). Hover promena važi za sva ghost dugmad na svetlim sekcijama.

## 4. Usklađivanje golih `<h2>` (M: „Uskladi")

Prijavljena su dva naslova („Reference" na 16657, „Primeri" na 16586) kao vizuelno manji
od ostalih H2 na istim stranicama. SQL sken obrasca
`<span class="al-label">…</span>` + `<h2>` **bez ijednog atributa** našao je **17**
stranica, po jedan pogodak svaka — 38px umesto 68px.

Sve usklađene jednim prolazom (`al-display--lg` dodat). Semantika netaknuta — `<h2>`
ostaje `<h2>`, menja se samo klasa, pa nema uticaja na strukturu naslova za SEO.

ID-evi: 16585, 16586, 16589, 16590, 16657, 16658, 16679, 16683, 16688, 17025, 571,
16660, 16665, 16666, 17019, 17026, 17027.

## 5. Verifikacija

- **HTTP:** 17 + 15 URL-ova kroz `curl -sL` — svi **200 / 1×H1 / 0 PHP grešaka /
  0 preostalih golih `<h2>`**.
- **Vizuelno (Chrome):** obe `kosarkaske-konstrukcije` stranice, `dimenzije-kosarkaskog-terena`,
  `vinil-podovi-objectflor`, `expona-click`, kategorija, `industrijski-podovi`,
  homepage, `/kontakt/`, `/pvc-podne-ploce/` i blog post sa sidebar-om.
- **Forma:** `hidden-fields-container` i `wpcf7-response-output` oba `display:none` i u
  100vw varijanti (stranice) i u kartica-varijanti (postovi sa sidebar-om).
- **Regresija:** homepage nema nijedan spoj istog tona → 0 izmenjenih sekcija.
  `/kontakt/` isto (0 sekcija sa `padding-top: 0`), forma nepromenjena.

**Backupi:**
`antasline-backups/antasline_local_2026-08-13_pre-faza2-layout.sql` (37,6 MB) ·
`antasline-backups/antasline_local_2026-08-13_pre-h2-uskladjivanje.sql` ·
`woodmart-child/css/antas-design.css.bak-2026-08-13-pre-faza2` ·
`woodmart-child/functions.php.bak-2026-08-13-pre-faza2`

**Skripte:** `fix-16586.php`, `uskladi-h2.php` — jednokratne, ostale u scratchpad-u.

## Otvorene akcije
- [x] FAZA 2, stavke 1.1–1.4 i 2.1–2.2 #claude-code
- [x] Usklađivanje H2 (17 stranica) #claude-code
- [ ] Vizuelno prihvatanje sitewide izmene ritma sekcija — pravila 1–3 iz §1 pogađaju
      14 stranica koje nisu bile prijavljene; ako negde razmak deluje pretesno, javiti
      koja stranica #ceka-miroslav

## Beleške / odluke
- Sve tri popravke idu u **dizajn sistem**, ne po stranici. Alternativa (5 pojedinačnih
  CSS override-a) bi ostavila isti bag na 14 neprijavljenih stranica i 3 Expona stranice.
- Jedina izmena sadržaja u bazi je klasa (`--paper`→`--mist` na jednoj sekciji,
  `al-display--lg` na 17 naslova). Nijedan tekst nije menjan — bitno 3 dana pred
  content freeze (16.08).

## Veze
- [[migracija/woodmart-sabloni]] — F7 standard, WoodMart gotcha-i
- [[reference/naucene-lekcije]] — 5 novih lekcija iz ove sesije
- [[migracija/brzi-upit-forma]] — CF7 16737, kontekst §2
