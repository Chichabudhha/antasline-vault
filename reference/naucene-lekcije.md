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

## Telefon insight
- Broj 072 dominira klicima vs 074; 46/50 klikova sa mobilnog → istaći 072 u oglasima i call asset-ima.

## Claude Code ograničenje
- Bash komande >~965 bajtova bacaju "Command too long for parsing" → koristiti Write/Edit alat ili `bash skripta.sh`.
