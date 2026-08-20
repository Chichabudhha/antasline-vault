---
name: gtm-eventi-implementacija
description: Detaljno stanje naprednih GTM eventa (view_product_category, epoxy_conquest_engagement, lead_form_start, pdf_download, gallery_view) — potvrđeno direktno u GTM UI 2026-07-22. Izmešteno iz CLAUDE.md §4.1 2026-08-20 (vault higijena).
---

# Napredniji GTM eventi — stanje potvrđeno direktno u GTM-u 2026-07-22

> Kratak sažetak stanja ostaje u `[[CLAUDE]]` §4. Ovaj fajl nosi pun detalj po
> eventu — otvori ga samo kad radiš direktno na GTM-TRDT8K9 tagovima/triggerima.

**✅ Već implementirano i ožičeno u GTM-TRDT8K9 (potvrđeno direktno u UI, ne
pretpostavka):**
- `view_product_category` — GA4 Event tag, trigger "Window Loaded" (Page Path
  regex na ecotile/esd-pod/antistatik/industrijski-podovi/sportsk/tenis/padel/
  pickleball/odbojk/kosark/basket/3x3/bergo). Parametar `category_name` preko
  `{{RT - category_name}}` promenljive. Koristi se u publici "High-Intent B2B
  Bidders" (CLAUDE §5) — više NIJE pretpostavka, potvrđeno da tag/trigger stvarno
  postoji.
- `epoxy_conquest_engagement` — GA4 Event tag, trigger Scroll Depth OR Timer
  (Timer: interval 30000ms, **Limit 1**, Page Path contains
  `/epoksidni-podovi-ili-ecotile-podovi`). Limit 1 potvrđuje "fires samo jednom"
  pravilo — filteri i dalje `count ≥ 1`, nikad `> 1`.
- `lead_form_start` — GA4 Event tag, trigger "Custom Event trigger" (Custom
  HTML tag "Lead Form Start" na All Pages šalje custom event u dataLayer).

**🆕 Dodato 2026-07-22 (DRAFT u Workspace, NIJE Submit-ovano — čeka odobrenje):**
- `pdf_download` — trigger "Klik na PDF" (Just Links, Click URL contains
  `.pdf`) + GA4 Event tag sa parametrima `link_url={{Click URL}}` i
  `link_text={{Click Text}}`. Pokriva sve postojeće PDF linkove (tehnički
  listovi, sertifikati) na proizvod-stranicama, potvrđeno curl-om na Ecotile
  E500/7 (5 PDF linkova).
- `gallery_view` — trigger "Klik na galeriju proizvoda" (All Elements, Click
  Classes contains `woocommerce-product-gallery` AND Page Path contains
  `/proizvod/`) + GA4 Event tag, **Tag firing options = Once per page** (da
  višestruki klik na thumbnail-e ne naduva brojku — isti obrazac kao epoxy
  "fires jednom"). Bez custom parametara (GA4 automatski hvata page_location).
  Potvrđeno na pravoj WooCommerce galeriji (`.woocommerce-product-gallery__image`
  klasa, PhotoSwipe lightbox).

🟢 **Ispravka 2026-07-22 (W3 3.10 regresija)**: stari "gtag stub id=DUMMY" gotcha
gore je bio zastareo/netačan u trenutku pisanja — u stvarnosti lokalni build
(`localhost`) nije imao NIKAKAV GTM/gtag kod, ni pravi ni DUMMY (BLOK A rad je
postojao samo u GTM UI, embed snippet je ostao na starom Porto/Kallyas buildu i
nikad nije prenet u WoodMart rebuild — nula analitike da je otišlo na migraciju
neprimećeno). Popravljeno preko `mu-plugins/al-tracking-gtm-consent.php`
(doslovna kopija live GTM+consent koda) — lokalni build sada učitava PRAVI
GTM-TRDT8K9 kontejner. GTM Preview/Tag Assistant protiv `localhost` nije
testiran ovu sesiju (moguće da i dalje ne radi iz drugih razloga — self-signed
okruženje, CORS i sl.); ako zatreba live-test triggera pre Submit-a, i dalje je
najsigurnija opcija GTM Preview protiv **live** antasline.com URL-a (read-only,
samo dodaje `gtm_debug` query param).

- **Enhanced Conversions** — GTM konfiguracija koja hešira (SHA-256) email/telefon
  iz kontakt forme i šalje ih Google Ads-u za precizniji cross-device match. Nije
  još implementirano — priprema je na čekanju.
