---
tip: reference
azurirano: 2026-08-14
---

# Identifikatori

> [!warning] BEZ LOZINKI
> Ovde idu samo javni ID-evi. Nikad lozinke, API ključeve, tokene.

> [!info] Provereno protiv sistema 2026-08-14
> Sekcija „Lokalno okruženje" je osvežena direktno iz baze i fajlova (tema,
> pluginovi, broj tabela, verzije), ne iz sećanja. Ranija verzija je bila od
> 27.07 i tvrdila „106 tabela, Porto + WPBakery + Yoast" — **sve tri stavke
> netačne**, a ovo je fajl koji agenti čitaju kao autoritet.

| Sistem | Vrednost |
|---|---|
| GA4 property | 292720335 |
| GA4 Measurement ID | G-H8BRCZN8W4 |
| GTM kontejner | GTM-TRDT8K9 (objavljena **Version 10**) |
| Google Ads nalog | 1568860314 ("Gogin Nalog") |
| GSC | sc-domain:antasline.com |
| Click-to-call Ads konv. ID | 966742304 |
| Click-to-call Label | QQCBCNDQ_sUcEKCi_cwD |

> GTM: `pdf_download` i `gallery_view` stoje kao **DRAFT u Workspace-u od 22.07**,
> nisu Submit-ovani (čekaju Preview test). v. [[CLAUDE]] §4.1

## Lokalno okruženje (build za migraciju)

- URL: `http://localhost/antasline`
- Root (ABSPATH): `C:\xampp\htdocs\antasline\`
- Stack: XAMPP · PHP **8.2.12** · MariaDB **10.4.32**
- DB: `antasline_local`, **78 tabela**, prefiks **`wpgs_`**
  🔴 malim slovima — i u bazi i u `wp-config.php` (ispravljeno 2026-08-14; uz
  config su preimenovani i prefiks-izvedeni ključevi u bazi, v. [[CLAUDE]] §2)
- MySQL CLI: `C:\xampp\mysql\bin\mysql.exe -u root antasline_local`
- Tema: **WoodMart 8.5.4** (`template`) + **`woodmart-child`** (`stylesheet`)
  — dizajn sistem `antas-design.css`, self-hosted Inter+Bebas
- SEO plugin: **Rank Math** (`seo-by-rank-math`) — 🔴 **Yoast je van upotrebe i
  obrisan sa builda 13.08**, ne vraća se; pisati isključivo u `rank_math_*`
  ključeve (v. [[CLAUDE]] §7.1)
- Sitemap: `http://localhost/antasline/sitemap_index.xml` — **7 child sitemapa /
  238 URL-ova** (parity sa live, postignuto 12.08)

### Aktivni pluginovi (9, stanje 2026-08-14)

`better-search-replace` · `contact-form-7` · `js_composer` (WPBakery) ·
`loco-translate` · `seo-by-rank-math` · `srlatin` · `svg-support` ·
`woocommerce` · `woodmart-core`

> `js_composer` je i dalje aktivan iako je builder WoodMart — nosi legacy
> shortcode markup iz reimportovanih postova. Njegov CSS (437 KB, render-blocking)
> je otvoren LCP bloker, v. [[CLAUDE]] §7.6.

## Live sajt (do migracije 2026-08-25)

- Domen: antasline.com
- Tema: **Kallyas** (menja se za WoodMart na dan migracije)
- CMP: sopstveni plugin `antasline-consent` · mu-plugin `al-tracking-gtm-consent.php`

## Konektori (sopstveni, Google API — od 2026-07-27)

- Windsor.ai je istekao 2026-07-27 (pretplata otkazana 2026-07-21) — više se ne koristi.
- Zamena: `.claude/skills/antasline-konektor/` — direktni pozivi GA4 Data API,
  Search Console API, Google Ads API, Business Profile Performance API preko
  lokalnih Python skripti. Kredencijali žive VAN vault-a
  (`C:\Users\Miroslav\antasline-connector\credentials\`), nikad u git-u.
- Setup checklist: [[reference/api-konektor-setup.md]]
- Isti javni ID-evi kao ranije — promenjena je samo mehanika povlačenja, ne nalozi.
- Konektor je READ-ONLY (kao i Windsor pre njega) — potvrđuje evente, ne menja GTM/GA4.

## AI vizuali / ruter (od 2026-08-04)

- Gemini image model: `gemini-2.5-flash-image` ("Nano Banana") — free tier
  ~500 poziva/dan, reset ponoć Pacific Time.
- Region: Srbija je zvanično podržan region za Gemini API — proxy/VPN nije
  potreban. („Region not supported", ako se pojavi, verovatno je false-positive
  po IP detekciji.)
- Kredencijali (Gemini/DeepSeek/Groq): `C:\Users\Miroslav\ai-tools\credentials\`
  — namerno odvojeno od `antasline-connector` (taj ostaje isključivo za
  Google Ads/GA4/GSC/GMB). Setup: [[reference/gemini-vizuali-setup.md]]
- `claude-code-router` (CCR) config: `~/.claude-code-router/config.json`
  (van vault-a) — opciono rutiranje `background`/`longContext` Claude Code
  poziva ka DeepSeek/Gemini. Detalji: [[blokovi/BLOK-E-ai-orkestracija]]

## Delegat-agenti (od 2026-08-14)

| Alat | Putanja / stanje |
|---|---|
| `agy` (Antigravity/Gemini) | `C:\Users\Miroslav\AppData\Local\agy\bin\agy.exe` |
| `ollama` (lokalno) | `C:\Users\Miroslav\AppData\Local\Programs\Ollama\ollama.exe` — 🟢 jedini bez kvote |
| GitHub Copilot CLI | na PATH-u · **Free plan, ~50 zahteva/mesec** |
| Grok CLI | `C:\Users\Miroslav\.grok\bin\grok.exe` (**nije na PATH-u**) · Free, OAuth |

Router i pravila bezbednosti: `.claude/skills/delegati/SKILL.md`
