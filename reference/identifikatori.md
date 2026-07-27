---
tip: reference
azurirano: 2026-07-27
---

# Identifikatori

> [!warning] BEZ LOZINKI
> Ovde idu samo javni ID-evi. Nikad lozinke, API ključeve, tokene.

| Sistem | Vrednost |
|---|---|
| GA4 property | 292720335 |
| GA4 Measurement ID | G-H8BRCZN8W4 |
| GTM kontejner | GTM-TRDT8K9 (Version 10) |
| Google Ads nalog | 1568860314 ("Gogin Nalog") |
| GSC | sc-domain:antasline.com |
| Click-to-call Ads konv. ID | 966742304 |
| Click-to-call Label | QQCBCNDQ_sUcEKCi_cwD |

## Lokalno okruženje
- URL: `http://localhost/antasline`
- Root (ABSPATH): `C:\xampp\htdocs\antasline\` *(potvrditi `wp eval "echo ABSPATH;"`)*
- Stack: XAMPP, PHP 8.2.12, MariaDB 10.4
- DB: 106 tabela, prefiks `wpGs_`
- Tema/builder: Porto + WPBakery (`js_composer`) + Yoast
- Sitemap: `http://localhost/antasline/sitemap_index.xml`

## Live sajt
- Domen: antasline.com
- Tema: Kallyas
- CMP: sopstveni plugin `antasline-consent`

## Konektori (sopstveni, Google API — od 2026-07-27)
- Windsor.ai je istekao 2026-07-27 (pretplata otkazana 2026-07-21) — više se ne koristi.
- Zamena: `.claude/skills/antasline-konektor/` — direktni pozivi GA4 Data API, Search Console API, Google Ads API, Business Profile Performance API preko lokalnih Python skripti. Kredencijali žive VAN vault-a (`C:\Users\Miroslav\antasline-connector\credentials\`), nikad u git-u.
- Setup checklist: [[reference/api-konektor-setup.md]]
- Isti javni ID-evi kao ranije (GA4 292720335, Ads 156-886-0314, GSC sc-domain:antasline.com) — samo je mehanika povlačenja podataka promenjena, ne nalozi sami.
- Konektor je READ-ONLY (kao i Windsor pre njega) — potvrđuje evente, ne menja GTM/GA4.
