---
tip: sesija
alat: claude-code
datum: 2026-08-07
blok: C
status: zavrseno
---

# Sesija — Sitewide dequeue WC add-to-cart JS steka + jQuery Migrate uklonjen

> W3 3.6 CWV linija — nastavak nakon M pitanja "šta se može isključiti u temi".

## Šta je urađeno

- Otvaranje sesije: pregled [[PROGRESS]] + [[2026-07-06-MASTER-PLAN-V2]] §2 (N5 nedelja, 04–10.08) + puni pregled zavisnosti §4 (na M zahtev, kao ponedeljak) — ništa kritično kasni, najstariji otvoren M13/M14 od 04.08.
- Glavni zadatak birao se između W1 Faza 4 (GEO-intro copywriting) i W4 4.3 (RSA Terase) — M je umesto toga preusmerio na istraživanje teme (WoodMart bloat).
- **Istraživanje**: proverena stvarna stanja u `xts-woodmart-options` (883 ključa), aktivni pluginovi, enqueue-ovani scripts/styles na 5 tipova stranica (curl). Emoji/dashicons/comment-reply/reCAPTCHA već isključeni od ranije; WC block CSS-ovi (product-gallery/filters) registrovani ali nikad enqueue-ovani.
- **Nalaz i akcija #1**: WC add-to-cart JS stek (`woocommerce`, `wc-add-to-cart`, `wc-add-to-cart-variation`, blockUI, js-cookie, VC-ov add-to-cart JS, 3× WoodMart cart JS) učitavao se na SVAKOJ stranici sitewide, ali katalog režim (M9) je odavno zamenio svako add-to-cart dugme linkom ka `/kontakt/` — potvrđeno da nema nijedne prave WC cart forme na sajtu, ni na 23 proizvoda sa pravom cenom. 10 handle-ova dequeue-ovano u `functions.php`.
- **Nalaz i akcija #2**: `jquery-migrate` uklonjen kao dependency `jquery` handle-a preko `wp_default_scripts` — rizičniji test, testiran uživo kroz Chrome (meni, mobilni nav, ajax pretraga, lightbox, court builder canvas) pre nego što je proglašen gotovim.
- Oba dequeue-a su u child temi (`woodmart-child/functions.php`) — preživljavaju parent theme update.

## Otvorene akcije
- [ ] `wc-add-to-cart-variation` i dalje se re-enqueue-uje na stranicama sa varijabilnim "Srodnim proizvodima" (poznat, van obima — treba diranje related-products rendera) #claude-code
- [ ] Brojčana Lighthouse LCP potvrda na produkciji posle UCSS re-enable (2026-08-07) #claude-code
- [ ] Ostali PROGRESS blokeri (Ecotile budžet, Ads Maximize Conversions, Customer Match test, konkurencija-analiza prioritet) #ceka-miroslav — nepromenjeno od ranije, ne dirano ovom sesijom

## Beleške / odluke
- Metod dokazivanja pre izmene: ne nagađati "možda je ovo mrtvo" — proveriti u bazi (`is_purchasable()`, `wc_get_page_id()`), u kodu (grep za override hook), i uživo (curl diff pre/posle). Ovo je isti standard kao ranija W3 3.6 sourcebuster/wc-order-attribution linija, samo prošireno sitewide.
- jQuery Migrate tretiran drugačije od WC dequeue-a: curl ne dokazuje da interaktivni JS radi, pa je pravi Chrome test bio uslov pre nego što je promena proglašena završenom (posebno court builder canvas kao najkompleksniji JS na sajtu).
- `al-harness.html` (390px mobile test iframe) i dalje postoji lokalno i korišćen je za mobilni test — podsetnik da mora biti obrisan sa produkcije pre migracije (već u W3 3.10 checklisti).

## Veze
- [[2026-07-06-MASTER-PLAN-V2]] W3 3.6
- [[PROGRESS]]
- [[DNEVNIK-NAPRETKA]] 2026-08-07
- [[reference/naucene-lekcije]]
