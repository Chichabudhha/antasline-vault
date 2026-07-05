---
tip: dnevnik
alat: claude-code
datum: 2026-07-05
blok: tehnicka
status: audit-gotov
tema: Porto → WoodMart prelazak (WPBakery ostaje)
---

# 🔍 Audit — prelazak Porto → WoodMart (lokalni build)

> Pitanje: koliko je sadržaja zaključano u Porto-specifičnim WPBakery elementima?
> Metod: read-only SQL nad `antasline_local` (wpGs_), regex ekstrakcija svih shortcode tagova iz 53 objavljena page/post.

## Rezultat u brojkama

| Kategorija | Broj | % | Trošak prelaska |
|---|---|---|---|
| Čist HTML (uglavnom blog postovi) | 28 | 53% | **nula** |
| Vanilla WPBakery (`vc_*` samo) | 5 | 9% | **nula** (vc_ radi u svakoj temi dok je WPBakery aktivan) |
| Sa `porto_*` elementima | 16 | 30% | ručna zamena elemenata |
| Woo proizvodi (37) + Yoast meta | — | — | **nula** (postmeta preživljava) |

## Porto elementi u upotrebi (8 različitih)

| Element | Stranica | Zamena u WoodMart |
|---|---|---|
| `porto_block` | 10 | inline-ovati sadržaj bloka (i sam je WPBakery) ili `woodmart_html_block` |
| `porto_image_gallery` | 6 | `vc_media_grid` / WoodMart galerija |
| `porto_ultimate_heading` | 4 | `vc_custom_heading` |
| `porto_info_box` | 3 | WoodMart info box |
| `porto_carousel`, `porto_interactive_banner(_layer)`, `porto_container` | samo home | home ionako ide ispočetka u redizajnu |

## 16 pogođenih stranica (ključne podebljane)

**/industrijski-podovi/ (4937 — silo!)**, **/spoljne-podne-obloge/**, **/podloge-za-parking/**, **/vestacka-trava/**, **/bergo-ultimate/**, **/kontakt/**, **/home/**, galerija-sportskih-terena, gumeni-podovi-za-stale, montazno-demontazne-podloge-u-plocama, naxos-evolution, podloge-oko-bazena, podne-obloge-za-promocije-i-sajmove, podovi-za-maloprodajne-objekte-i-hipermarkete, podovi-za-poslovni-prostor, vestacka-trava-za-fudbal

+ 19 `porto_builder` blokova (header/futer/CTA/product template) — **ne prenose se**, ali se header/footer ionako gradi novo u WoodMart header builderu.

## Procena posla

| Stavka | Vreme |
|---|---|
| 16 stranica × zamena porto elemenata (30–90 min/kom) | 1,5–2,5 dana |
| Header + footer + theme options (Inter + boje iz [[reference/brend-knjiga]]) | 1–2 dana |
| Home ispočetka | (već planirano u redizajnu) |
| Test 5 ključnih stranica + regression | 0,5 dana |
| **Ukupno** | **~3–5 radnih dana** |

## Zaključak

**Prelazak je jeftin: 62% sadržaja prolazi bez ikakvog posla, rok 2026-09-02 nije ugrožen.** Bonus: post 4937 je blokiran WPBakery JS bugom koji dolazi od starih/nepoznatih shortcode atributa — čišćenje `porto_*` elemenata na toj stranici verovatno usput rešava i bug (6 blokova čeka ubacivanje).

## Uslovi pre aktivacije WoodMart-a
1. `wp db export` snapshot (obavezno, [[CLAUDE]] §8.5)
2. Proveriti verziju bundlovanog `js_composer` u WoodMart-u vs trenutna ([[CLAUDE]] §7.3 — markup mora odgovarati verziji)
3. Kupiti licencu (~$59, ThemeForest)
4. Redosled: aktivacija na buildu → header/footer → 16 stranica po prioritetu (4937 prvi) → test

## Odluka
- [ ] Miroslav: GO / NO-GO za WoodMart #ceka-miroslav

## Veze
- [[reference/brend-knjiga]] — boje/font za theme options
- [[blokovi/BLOK-C-sledece]] — C2/C3 zavise od odluke (implementacija draftova čeka temu)
