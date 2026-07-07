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
- Bezbedan update: export `post_content` u `/tmp/`, splice novih blokova pre CTA, reimport `wp post update` — ne inline regex.
- Porto quirk: za `post_type=post` entry-title je `<h2 class="entry-title">`, ne `<h1>`. Ne tretirati kao nedostajući H1.
- Post lookup: `wp post list --name=slug` ume da vrati prazno za pages → fallback `wp eval 'echo url_to_postid("full-url");'`.
- **`margin-top` na `.vc_row` ne radi na ovom sajtu**: `full_width="stretch_row"` ubacuje prazan `<div class="vc_row-full-width vc_clearfix">` (float:left, height:0) između svaka dva reda — to poništava negativni `margin-top` na sledećem redu (computed stil je ispravan, render pozicija se ne pomera, potvrđeno testom `margin-top:-300px !important` inline → 0 efekta). Rešenje: `position: relative; top: ...` radi ispravno. Detalji: [[migracija/woodmart-sabloni]] gotcha #11.

## Permalink / rewrite izmene (parity F2, 2026-07-07)
- **Soft `flush_rewrite_rules()` nije dovoljan** posle promene WooCommerce `product_base`/`category_base` (permalink strukture) — proizvod URL-ovi vraćaju 404 uprkos ispravnom `get_permalink()` i ispravnim redovima u `rewrite_rules` opciji. Uvek koristiti `flush_rewrite_rules(true)` (hard flush) posle svake permastruct/permalink izmene.
- **Yoast `wpGs_yoast_indexable` keš se NE osvežava automatski** ni posle hard flush-a — canonical, `og:url` i JSON-LD ostaju na starim URL-ovima dok se stare redovi ručno ne obrišu: `DELETE FROM wpGs_yoast_indexable WHERE object_sub_type IN ('product_cat','product', ...)` (+ pojedinačni `object_id` za page/post slug izmene). Posle brisanja Yoast regeneriše ispravno na sledećoj poseti. Ovo proširuje raniju lekciju (2026-07-06, termmeta izmene) — pravilo važi za SVAKU izmenu koja menja permalink/slug bilo kog objekta (post, page, product, term).

## Telefon insight
- Broj 072 dominira klicima vs 074; 46/50 klikova sa mobilnog → istaći 072 u oglasima i call asset-ima.

## Sadržaj / HTML unos
- Nikad ne pisati `<p>` tekst preko više redova sa tvrdim prelomom (`\n`) radi čitljivosti u editoru — `wpautop` pretvara svaki pojedinačni `\n` unutar paragrafa u `<br>`, pa se rečenica prelama na sredini na živoj stranici. Rešenje: jedan pasus = jedan kontinuirani red (bez wrap-a) u izvornom HTML-u koji se ubacuje u `post_content`. `<script>` blokovi (JSON-LD) nisu pogođeni — wpautop ih preskače.

## Claude Code ograničenje
- Bash komande >~965 bajtova bacaju "Command too long for parsing" → koristiti Write/Edit alat ili `bash skripta.sh`.
