---
tip: blok
blok: B
status: zavrseno
azurirano: 2026-06-27
---

# BLOK B — GA4 publike + funnel (ZATVOREN)

Sve 4 publike kreirane i vidljive u Ads Audience manageru (Source = "Google Analytics (GA4)"), status "Too small to serve" — čekaju prag (100 Display/YT, 1.000 Search).

## Publike
1. **High-Intent B2B Bidders** — sequence view_product_category (industrijski/esd/ecotile), 14d
2. **Sport & Court Planners** — page_path OR: sportsk/padel/pickleball/tenis/kosark/odbojk/bergo, 45d
3. **Form Abandoners** — lead_form_start ≥1, exclude /hvala-za-poruku, 14d
4. **Epoxy Changers** — epoxy_conquest_engagement ≥1, exclude /hvala-za-poruku, 30d

## Funnel
- "Levak B2B posetioci industrijski/esd/ecotile/spor" — aktivan od 2026-06-27.

## Otvorene akcije
- [ ] Fino podešavanje (nije hitno): Bergo pod `/spoljnje-podne-obloge/` vuče terasni saobraćaj (~324 korisnika/14d). Ako Sport & Court slabo konvertuje, izbaciti bergo iz te publike. #claude-code
- [x] Odblokiranje naloga (dopuna balansa + verifikacija oglašivača) — ✅ urađeno 2026-07-04

## Veze
- [[reference/naucene-lekcije]] — `epoxy_conquest_engagement` count ≥1, 4.3K = procena pool-a
