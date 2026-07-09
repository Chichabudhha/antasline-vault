---
tip: reference
azurirano: 2026-06-28
---

# Naučene lekcije (tehnički gotchas)

## GTM
- Import ručno pisanog JSON-a NE prolazi — greška "Error deserializing enum type [EventType]". Pouzdano: (A) ručno u GTM UI ili (B) Export → ubaci evente u tačan format → Merge.
- GA4 consent update handler MORA slati eksplicitne vrednosti za sve 4 kategorije; prazan `gtag('consent','update',{})` ne poništava prethodni granted.

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

## Telefon insight
- Broj 072 dominira klicima vs 074; 46/50 klikova sa mobilnog → istaći 072 u oglasima i call asset-ima.

## Sadržaj / HTML unos
- Nikad ne pisati `<p>` tekst preko više redova sa tvrdim prelomom (`\n`) radi čitljivosti u editoru — `wpautop` pretvara svaki pojedinačni `\n` unutar paragrafa u `<br>`, pa se rečenica prelama na sredini na živoj stranici. Rešenje: jedan pasus = jedan kontinuirani red (bez wrap-a) u izvornom HTML-u koji se ubacuje u `post_content`. `<script>` blokovi (JSON-LD) nisu pogođeni — wpautop ih preskače.

## Claude Code ograničenje
- Bash komande >~965 bajtova bacaju "Command too long for parsing" → koristiti Write/Edit alat ili `bash skripta.sh`.
