---
tip: radni-nalog
datum: 2026-07-30
blok: W1
status: odobreno-ceka-izvrsenje
izvor: "M tražio usklađivanje sajta sa Lighthouse Accessibility + Agentic Browsing smernicama"
---

# 🔧 Radni nalog — Lighthouse Accessibility + Agentic-Browsing usklađivanje

> Plan odobren u istoj sesiji (2026-07-30), izvršenje odloženo jer je sesija zatvorena
> pre starta. Sledeća sesija koja radi na ovome: pročitaj ovo u celosti, izvršavaj batch
> po batch, prati verifikaciju na kraju svakog.

## Context

M je tražio usklađivanje lokalnog build-a (`C:\xampp\htdocs\antasline`, WoodMart tema) sa
Google Lighthouse smernicama: [Accessibility scoring](https://developer.chrome.com/docs/lighthouse/accessibility/scoring)
i [Agentic Browsing scoring](https://developer.chrome.com/docs/lighthouse/agentic-browsing/scoring).

**Agentic-browsing kategorija je već zatvorena** (baseline 2026-07-30, [[dnevnik/AGENTIC-BROWSING-AUDIT]]):
1/1 na `agent-accessibility-tree` + CLS na svih 6 test-stranica. WebMCP namerno van obima
(čeka BLOK D AI-chat odluku), `llms.txt` je dokazano lažna crvena (lokalni `/antasline/`
podfolder, na produkciji će proći). **Nema koda za promenu u toj kategoriji.**

**Standardna Accessibility kategorija** poslednji put merena 2026-07-09 (skor 84–90,
[[dnevnik/PERFORMANCE-AUDIT]]), nikad ponovo. Istraživanje (2026-07-30) je našlo konkretne,
file:line potvrđene probleme — ovaj plan ih zatvara.

**Odluke M-a (2026-07-30 razgovor):**
- Kontrast CTA dugmeta (`.al-btn`, brend boja `#F04D22`) → **uvećati tekst dugmeta** da se
  kvalifikuje za WCAG "veliki tekst" izuzetak (3:1 prag), NE menjati brend boju.
- Alt tekst za slike (67/81 proizvoda bez alt-a, 180/684 slika u sadržaju sa `alt=""`) →
  **van obima ove ture** — samo kod/CSS ispravke (Batch 1–4), alt tekst poseban budući
  zadatak.

---

## Batch 1 — Mehaničke, gotovo bezrizične ispravke

### 1.1 Viewport meta — potvrđena aktivna greška (`meta-viewport` audit)
`wp-content\themes\woodmart\inc\template-tags\template-tags.php:187-201` emituje
`maximum-scale=1.0, user-scalable=no` jer je WoodMart-ov fabrički default za opciju
`site_viewport` doslovno `'not_scalable'` (nikad svesno birano — potvrđeno u DB-u). Blokira
pinch-zoom, direktan garantovan Lighthouse fail.

**Postupak — koristiti već dokumentovanu, proverenu proceduru iz `[[migracija/woodmart-sabloni]]`
("🔴 KRITIČAN gotcha — xts-woodmart-options je SVE-ILI-NIŠTA, ne merge") — NE raw
`update_option()`, briše ~883 drugih ključeva i tiho obara footer:**

1. Kreirati privremeni `wp-content/mu-plugins/zz-fix-viewport-TEMP.php`:
   ```php
   add_action('init', function(){
       $defaults = \XTS\Admin\Modules\Options::get_options();
       $current  = get_option('xts-woodmart-options') ?: [];
       $overrides = ['site_viewport' => 'scalable'];
       update_option('xts-woodmart-options', array_merge($defaults, $current, $overrides));
   }, 105); // između load_defaults@100 i load_options@110, TROSMERNA merge (dopuna 07-10)
   ```
2. Jedan `curl http://localhost/antasline/` da se `init` hook izvrši.
3. **Odmah obrisati** `zz-fix-viewport-TEMP.php`.
4. Ako se nešto vizuelno promeni (CSS keš): reset preko
   `Styles_Storage('theme_settings_default')->reset_data()->delete_css()`.
5. Verifikacija: `view-source` → `<meta name="viewport">` bez `maximum-scale`/`user-scalable=no`;
   WoodMart admin (Theme Settings → General → Viewport) pokazuje "Scalable".

### 1.2–1.5 Court builder (`/planer-terena/`) — sitne, izolovane ispravke
Fajl: `wp-content\themes\woodmart-child\inc\court-builder\js\al-court-builder.js`
(+ `al-court-builder.css`). Sve su dodavanje atributa, bez logičkih promena:

- **1.2 aria-live regioni** (linije ~792, ~809): `.al-cb__warning` i `.al-cb__save-msg`
  dobijaju `role="status" aria-live="polite"` — postojeći `textContent` update-ovi (JS
  ~291-292, 709, 727, 751, 756) postaju automatski najavljeni.
- **1.3 label za količinu opreme** (linija ~493): `<input type="number">` bez labela →
  dodati `aria-label="Količina — ' + item.name + '"` (isti obrazac kao postojeći
  neeskejpovan `item.name` na liniji ~491).
- **1.4 pristupačno ime za dugmad** (linija ~253 erase-swatch, ~249-250 glavne palete):
  vidljivi znak "×" pobeđuje `title` u accessible-name računanju → dodati eksplicitan
  `aria-label="Obriši boju"` na erase-dugme, `aria-label="' + c.name + '"` na swatch dugmad.
- **1.5 veličina dodira** (`al-court-builder.css:162-165`): `.al-cb__line-palette
  .al-cb__swatch` je 22×22px, Lighthouse minimum 24×24 — podići na 24×24 (glavna paleta
  je već 32×32, ispravno).

**Verifikacija 1.1–1.5:** Lighthouse standardni Accessibility na 7 test-stranica (dole).
Očekivano: `meta-viewport` fix sitewide, `label`/`button-name`/`target-size` fix na
`/planer-terena/`.

---

## Batch 2 — Heading order i duplikat ID (proveri pa izmeni)

### 2.1 Heading order — 2 stranice
`wp-content\themes\woodmart\css\style.css:424-446`: h2/h3/h4 imaju RAZLIČITE veličine
fonta (24/22/18px), child tema (`antas-design.css:132-143`) menja samo
font-family/weight/transform, ne veličinu. **Prost tag-swap menja vizuelnu veličinu.**

- Post `5754` (izgradnja-terena-za-tenis): `h1→h3→h2→h4→h2→h3→h2→h3→h2` — 2 preskoka.
- Post `15480` (bergo-ultimate): `h1→h2→h4→h2→h3→h3→h2` — 1 preskok.

Postupak: pročitati `post_content` (read-only WP-CLI eval-file), utvrditi da li je heading
čist HTML tag (treba pridružiti `style="font-size:Npx"` da vizuelno ostane isto) ili
WPBakery `[vc_custom_heading]` sa eksplicitnim `font_size:` u `font_container` (tad je
swap `tag:h4`→`tag:h3` unutar stringa vizuelno no-op — najsigurnije, uraditi prvo ako
postoji). Backup `post_content` pre upisa, upis preko `$wpdb->update()` direktno (ne
`wp_update_post()` — poznat `wp_unslash()`-nad-celim-sadržajem bag).

### 2.2 Duplikat ID — 2 stranice
- Post `5769`: `id="vestacka-trava"` 2×.
- Page `15580`: `id="eluid54d67c12"` 2× (WPBakery leftover anchor ID).

Pre preimenovanja: sitewide pretraga `post_content` za `href="#vestacka-trava"` i
`href="#eluid54d67c12"`. Bez pogodaka → preimenovati DRUGO pojavljivanje (`-2` sufiks).
Sa pogotkom → ažurirati `href` i `id` istovremeno (atomski).

**Verifikacija:** Lighthouse `heading-order`/`duplicate-id` na te 4 stranice; vizuelni
before/after screenshot gde je korišćen size-preserving override.

---

## Batch 3 — Court builder tastatura (SVG mreža ćelija)

Najveći kod-zahvat, striktno DODAVANJE (ne dira mouse/touch handlere ~632-692 ni
`paintCell()`/`buildGrid()` logiku):

1. U `buildGrid()` (~308-313): svaka `<rect>` ćelija dobija `tabindex="-1"` osim jedne
   (`state.focusIdx`, init 0) koja dobija `tabindex="0"` — roving tabindex.
2. Jedan `keydown` listener na `els.gridWrap`:
   - Strelice: pomeraju `state.focusIdx` po `state.cols`/`state.rows`, `tabindex` swap
     0↔-1, `.focus()` na novu ćeliju, `e.preventDefault()`.
   - `Enter`/`Space`: ako `state.detailMode` uključen, pozvati POSTOJEĆI
     `paintCell(state.focusIdx, state.activeColor)` — identičan efekat kao mousedown
     (linija 641), nema nove logike bojenja.
3. Fokus-indikator: sitewide `:focus-visible` (`antas-design.css:510-513`) ne pokriva SVG
   `<rect>` (scoped na `a, button, .al-btn`). Dodati u `al-court-builder.css`:
   `.al-cb__cell:focus-visible{outline:3px solid var(--al-orange);outline-offset:-2px}`
   + JS fallback klasa `.al-cb__cell--focus` (stroke umesto outline).
4. **Ne raditi** puni `role="grid"`/`role="gridcell"` ARIA rewrite ovu turu — čista
   tastaturna operabilnost (WCAG 2.1.1) je ono što se boduje.

**Verifikacija:** ručni Tab test na `/planer-terena/` (fokus ulazi u mrežu, strelice
pomeraju vidljiv fokus, Enter/Space boji identično kliku), Lighthouse na ovu stranicu
(dodaje se u test-set kao 7.).

---

## Batch 4 — Kontrast (M odluka primenjena)

**Dugme (`.al-btn`, `antas-design.css:234-248`)** — Opcija 2 (M odluka): povećati min.
veličinu fonta sa `clamp(17px,1.4vw,21px)` na garantovano ≥24px na svim širinama (npr.
`clamp(24px,1.6vw,26px)`) — `#F04D22` netaknut, kvalifikuje se za "veliki tekst" 3:1 prag.
**Proveriti pre commit-a**: prelom/overflow na header CTA i mobilnim CTA redovima, i CLS
na Home (hero CTA above-the-fold — jedina izmena u planu koja realno može pomeriti layout).

**Preostale 3 manje instance teksta** (nisu brend-knjiga-kritične — knjiga vezuje
`#F04D22` specifično za CTA dugme, ne za ove): `.al-label` (262-269),
`.al-quick-quote__sub a` (1138-1143), `.al-promo-product__price` (1310-1316) → zatamniti
`color: var(--al-red)` → `color: var(--al-red-dark)` (već postoji, prolazi 4.71:1).

**`.al-mobile-tel` ikona (bela na `--al-red`, ~455-456)** — PRE izmene proveriti u
stvarnom Lighthouse run-u da li se uopšte flaguje: ako je izloženo kao dekorativna
grafika, primenljiv kriterijum je 1.4.11 (non-text, 3:1) koji ≈3.63:1 već zadovoljava —
možda nije stvaran problem. Ne menjati dok se ne potvrdi.

**Verifikacija:** Lighthouse `color-contrast` na svih 7 stranica; vizuelna provera da
veće dugme ne lomi layout; CLS re-check na Home.

---

## Van obima ove ture (M odluka)

- Alt tekst za slike (67/81 proizvoda, 180/684 slika u sadržaju) — poseban budući zadatak
  (mehanički skript za proizvode + ručna procena za sadržaj slike).
- CF7 form labeli (Kontakt #16593, Brzi upit #16737) — dobra praksa, ne garantovan
  Lighthouse gubitak boda — niska prednost.
- Puna ARIA `role="grid"` semantika na court builder-u — nice-to-have.

---

## Kritični fajlovi

- `wp-content\themes\woodmart-child\css\antas-design.css`
- `wp-content\themes\woodmart-child\inc\court-builder\js\al-court-builder.js`
- `wp-content\themes\woodmart-child\inc\court-builder\css\al-court-builder.css`
- `wp-content\mu-plugins\zz-fix-viewport-TEMP.php` (privremen, kreirati pa obrisati)
- `[[migracija/woodmart-sabloni]]` (izvor proverene `xts-woodmart-options` procedure)
- 2 posta za heading-order (`5754`, `15480`), 2 za duplikat ID (`5769`, `15580`) — DB upis
  preko `$wpdb->update()` sa backup-om pre svake izmene

## Verifikacija (kraj svakog batch-a)

- Standardni Accessibility: `npx lighthouse <url> --chrome-flags=--headless=new --output=json`
- Agentic-browsing (samo potvrda da nema regresije): `--config-path=node_modules/lighthouse/core/config/agentic-browsing-config.js --only-categories=agentic-browsing` iz npx keš foldera
- **7 test-stranica**: Home, `/industrijski-podovi/`, `/sportske-podloge/`, proizvod
  `konusni-stitnik-...`, `/kategorija-proizvoda/zastita-i-bumperi/`, Conquest 2542,
  **`/planer-terena/`** (novo dodato)
- Baseline za poređenje: Accessibility 84–90 (2026-07-09), agentic-browsing 1/1+CLS-prolaz
  (2026-07-30) — pratiti deltu po stranici po batch-u
- Poznat bezopasan šum: Windows `chrome-launcher`/`destroyTmp()` stack-trace posle
  `--quiet` (JSON izlaz je već ispravno upisan pre te greške)
- Backup baze pre svake DB izmene (Batch 2)

## Posle izvršenja

Prati standardni obrazac (skill `/antasline-sesija` §5): DNEVNIK-NAPRETKA unos na vrh sa
rezultatima po batch-u + realnim before/after Lighthouse brojevima, PROGRESS.md red +
skinuti ovaj radni nalog sa "čeka izvršenje", nova lekcija (ako se nešto neočekivano nađe)
→ [[reference/naucene-lekcije]].
