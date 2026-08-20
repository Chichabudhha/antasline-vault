---
name: wpbakery-legacy-gotchas
description: Istorijski WPBakery/js_composer gotcha-i, relevantni samo ako reimportovan post (F3, pun reimport sa live-a) i dalje nosi stari shortcode markup u post_content. Izmešteno iz CLAUDE.md §7.3 2026-08-20 (vault higijena). Aktuelna tema je WoodMart 8.5.4, v. [[migracija/woodmart-sabloni]].
---

# WPBakery — poznati problemi (istorijski, tema je od jula 2026 WoodMart)

> ⚠️ Build je prešao sa Porto+WPBakery na **WoodMart 8.5.4 + child** (vidi
> `[[CLAUDE]]` §2 i `[[migracija/woodmart-sabloni]]` za trenutne gotcha-e). Ovaj
> fajl je zadržan jer reimportovani postovi (F3, pun reimport sa live-a) mogu i
> dalje nositi stari WPBakery shortcode markup unutar `post_content` — ako se
> na to naiđe, važe pravila ispod. Post 4937 nalaz je potvrđeno **moot**
> (2026-07-22): `/industrijski-podovi/` je nova WoodMart stranica (ID 16567,
> rebuild 2026-07-05), 4937 je draft.

- JS greška "Cannot read properties of undefined" dolazi od nepoznatih/starih
  shortcode atributa ili nezatvorenih shortcode-ova
- Pre bilo kakvog programskog ubacivanja blokova: proveriti tačnu verziju
  `js_composer`, pisati markup koji odgovara toj verziji, regenerisati
  `_wpb_shortcodes_custom_css` i `_wpb_post_custom_css` post meta posle izmena
  sadržaja, **uvek prvo backup** (`wp db export`)
