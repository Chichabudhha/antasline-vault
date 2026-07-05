---
tip: migracija
datum: 2026-07-05
namena: WPBakery šabloni + design system klase za WoodMart rebuild stranica
---

# WoodMart rebuild — šabloni i pravila

> Design system: `woodmart-child/css/antas-design.css` (tokeni + utility klase) ·
> Fontovi self-hosted u `woodmart-child/fonts/` · Header definisan u `woodmart-child/functions.php`
> (filter `woodmart_default_header_structure`) · Boje: [[reference/brend-knjiga]]

## ⚠️ Tri pravila koja su već napravila bug (ne ponavljati)

1. **wpautop**: HTML unutar `[vc_column_text]` mora biti **u jednoj liniji** unutar grid/hero blokova — svaki prelom reda postaje `<br>`/`<p>` i razbija CSS grid (viđeno na home kartcama; ista lekcija kao odbojka refresh na live)
2. **Slug kolizija**: nove stranice se prave SAMO ako ne postoje; ako slug postoji → sufiks **5** (odluka Miroslava 2026-07-05)
3. **Copy izvor**: sadržaj stranica iz `migracija/live-export-2026-07-05/live-pages-2026-07-05.xml` + Yoast meta iz `live-inventar-2026-07-05.csv` (parity!)

## Utility klase (antas-design.css)

| Klasa | Efekat |
|---|---|
| `al-section` + `--navy/--blue/--mist/--paper` | sekcija sa vertikalnim ritmom i bojom |
| `al-diag-top` / `al-diag-top--rev` | kosi rez na vrhu (uvlači se u prethodnu sekciju) |
| `al-diag-bottom` / `--rev` | kosi rez na dnu |
| `al-plates` | potpis: nagnute "listajuće ploče" iz logoa (desno, skew -7°) — samo na navy sekcijama |
| `al-label` | narandžasti/crveni micro-label sa kosim crtama `/ TEKST /` |
| `al-display--xl` / `--lg` | Bebas displej naslovi (h1/h2 su ionako Bebas) |
| `al-btn` / `al-btn--ghost` | crveni CTA paralelogram / okvirna varijanta |
| `al-grid al-grid--4/--3` | responsive grid (4→2→1) |
| `al-card` + `al-card__media/__title/__body` | foto kartica sa overlay naslovom |
| `al-stat` + `al-stat__num/__cap` | USP brojka (Bebas crvena) + caption |
| `al-ref-row` | red logotipa/imena referenci |
| `al-hero` + `al-hero__sub/__cta` | hero blok |

## Šablon: SILO LANDING stranica (industrijski, sportski, terase, parking)

```
[vc_row full_width="stretch_row" el_class="al-section al-section--navy al-plates al-diag-bottom"][vc_column][vc_column_text]
<div class="al-hero"><span class="al-label">KATEGORIJA</span><h1 class="al-display--xl">NASLOV IZ LIVE YOAST TITLE</h1><p class="al-hero__sub">Intro iz live sadržaja, 2 rečenice.</p><div class="al-hero__cta"><a class="al-btn" href="[kontakt]">Zatražite ponudu</a><a class="al-btn al-btn--ghost" href="tel:+381692340072">069 234 00 72</a></div></div>
[/vc_column_text][/vc_column][/vc_row]
[vc_row full_width="stretch_row" el_class="al-section al-section--paper"][vc_column][vc_column_text]
... live body sadržaj (H2 sekcije, tabele, cena) — višelinijski OK van grid-ova ...
[/vc_column_text][/vc_column][/vc_row]
[vc_row full_width="stretch_row" el_class="al-section al-section--mist al-diag-top"][vc_column][vc_column_text]
... FAQ blok (iz C3 draftova gde postoje) + FAQPage JSON-LD u vc_raw_html ...
[/vc_column_text][/vc_column][/vc_row]
[vc_row full_width="stretch_row" el_class="al-section al-section--navy al-plates al-diag-top--rev"][vc_column][vc_column_text]
<div class="al-hero"><span class="al-label">Kontakt</span><h2 class="al-display--lg">Spremni za novi pod?</h2>...CTA isto kao home...</div>
[/vc_column_text][/vc_column][/vc_row]
```

Obavezno po stranici: `_woodmart_main_layout` = `full-width` · `_woodmart_title_off` = `on` ·
Yoast title/metadesc iz inventara (ili C3 draft ako je nova stranica) · interni linkovi po C3 pravilima.

## Home referenca (implementirano 2026-07-05)

Stranica ID **16550** — hero (navy+plates) → 4 segment kartice → USP 25/0/1 → reference row →
blog `[woodmart_blog items_per_page="3" blog_design="masonry" blog_columns="3" img_size="medium_large"]` → CTA (navy+plates, rev dijagonala).
Slike segmenata: ecotile-floor-1.jpg · Spanoulis-Belgrade-7-1.jpg · podovi-za-terase-i-balkone-1.jpg · parking-sa-runfloor-podlogom.webp

## Redosled rebuilda (prati [[seo/plan-novih-stranica]] + redirect mapu)

1. ✅ Home
2. /industrijski-podovi/ (postoji → novi slug `industrijski-podovi5`? — NE: stara stranica se šalje u draft pa novi uzima slug? **Odluka po stranici: ako staru čuvamo kao referencu → sufiks 5 na novoj; kad se stara obriše, slug se preuzima**)
3. Nove C3 stranice (ne postoje → čist slug): /industrijski-podovi-cena/, /podovi-za-garaze/, /gumeni-podovi-za-terase-cena/, /dimenzije-kosarkaskog-terena/, /dimenzije-kosarkaske-table/, /podloge-za-parkiraliste-cena/
4. Silo: sportske-podloge, spoljne-podne-obloge, podloge-za-parking, kontakt, o-nama
5. Ostale iz live inventara (50 pages)

## Otvoreno
- [ ] Mobilni viewport vizuelna provera (media queries napisani, nije snimljeno) #claude-code
- [ ] Bela varijanta logoa za navy footer #claude-code
- [ ] Footer builder (WoodMart HTML block) — još default #claude-code
- [ ] Figma sync — čeka link od Miroslava #ceka-miroslav
- [ ] Meni: proširiti sa novim silo stranicama kad se izgrade
