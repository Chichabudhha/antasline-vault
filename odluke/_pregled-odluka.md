---
tip: odluke
azurirano: 2026-06-28
---

# Pregled odluka (i zašto)

## URL struktura — flat `/proizvod/`
**Odluka:** flat `/proizvod/` umesto silo-u-URL-u.
**Zašto:** silo autoritet se gradi internim linkovanjem, breadcrumb strukturom i schemom — URL path segment je zanemarljiv ranking faktor; flat struktura nosi manje operativnih rizika.

## SEO plugin — Yoast (NE RankMath)
**Odluka:** na redizajn buildu ostaje JEDINO Yoast (`_yoast_wpseo_title` / `_yoast_wpseo_metadesc`).
**Zašto:** Miroslav eksplicitno potvrdio; ne predlagati RankMath dok se to ne promeni.

## Epoksid — conquest, ne van ponude
**Odluka:** epoxy upiti se namerno targetiraju kroz `/epoksidni-podovi-ili-ecotile-podovi/` (post 2542) radi konverzije u Ecotile.
**Zašto:** AntasLine NE prodaje epoksid; epoxy upiti = kvalifikovana tražnja na vrhu levka. Nikad ne predlagati sadržaj koji prodaje epoksid.

## Bidding — ostati na Maximize Clicks
**Odluka:** ne prelaziti na Maximize Conversions pre 20–30 pravih formi sa `/hvala-za-poruku/`.
**Zašto:** jun 2026 = 53 ukupno ali samo 2–3 iz plaćenog — premalo signala za smart bidding.

## Prompt API / Gemini Nano u browseru — NE koristimo (2026-08-12)
**Odluka:** built-in AI (Prompt API, Gemini Nano) se ne uvodi ni na sajt ni kao
interni alat. Nema ponovnog razmatranja dok Chrome ne doda srpski jezik.
**Zašto:** (1) podržani jezici su EN/JA/ES/DE/FR — srpski nije na listi, a ceo
naš sadržaj i publika su na srpskom; (2) samo desktop Chrome (nema Android/iOS),
a naša publika je mobilna (~46 od 50 klikova na telefon dolazi sa mobilnog);
(3) hardverski prag >4 GB VRAM + 16 GB RAM + 22 GB slobodnog prostora — Miroslavljev
laptop ga ne prolazi (2 GB VRAM, 15,7 GB RAM), pa se ne bi mogao ni testirati;
(4) za sve što bi radio već imamo bolji alat u toku rada (Claude Code, Gemini API).
Detalji i alternativa: [[reference/chrome-web-platform-2026]] §7.

## Otvorene odluke (BLOK C)
Vidi [[blokovi/BLOK-C-sledece]] — dispozicija staging-only proizvoda, overwrite vs preserve postova.

## Veze
- [[blokovi/BLOK-C-sledece]]
