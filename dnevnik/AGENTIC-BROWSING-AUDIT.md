---
tip: audit
oblast: W3-tehnicka
zadatak: "Accessibility + Lighthouse agentic-browsing scoring audit"
datum: 2026-07-30
status: baseline snimljen
alat: Lighthouse 13.4.1 (npx cache, headless Chrome 150), config-path agentic-browsing-config.js
---

# AGENTIC BROWSING AUDIT — baseline lokalnog builda

> **Svrha:** M dao URL `developer.chrome.com/docs/lighthouse/agentic-browsing/scoring`
> kao poseban W3-stil zadatak (nova Lighthouse kategorija, još u razvoju kod
> Google-a). Ovaj fajl je prvi baseline — po istom obrascu kao
> [[dnevnik/PERFORMANCE-AUDIT]] (CWV).

## 0. Šta ova kategorija stvarno meri

Nije klasičan 0–100 score nego **razlomak (fraction)** preko 6 provera, 3
grupe:

| Provera | Grupa | Šta traži |
|---|---|---|
| `agent-accessibility-tree` | Agent Accessibility | uži podskup ARIA/naming a11y pravila (29 pravila: `button-name`, `link-name`, `aria-*`, `label`, `document-title`...) — "da li AI agent može da razume šta je šta na strani" |
| `llms-txt` | Agent Accessibility | `/llms.txt` na ROOT-u domena — H1 header + bar 1 markdown link + >50 karaktera |
| `cumulative-layout-shift` | (bez grupe) | isti CLS metrika kao standardni CWV — "kritično jer agenti se oslanjaju na poziciju elemenata" |
| `webmcp-registered-tools` | WebMCP | koje WebMCP alate stranica registruje (Chrome DevTools Protocol) |
| `webmcp-form-coverage` | WebMCP | da li forme imaju WebMCP anotacije |
| `webmcp-schema-validity` | WebMCP | validnost WebMCP šema |

**Zahteva Chrome 150+** (lokalno instaliran: 150.0.7871.187 ✅) i WebMCP
origin trial za WebMCP provere. Nije ožičeno u `npx lighthouse` CLI kao
preset — mora `--config-path` direktno na
`node_modules/lighthouse/core/config/agentic-browsing-config.js` unutar
npx keša (`--only-categories=agentic-browsing`).

## 1. Rezultati (2026-07-30, 6 reprezentativnih stranica)

| Stranica | Kategorija score | `agent-accessibility-tree` | CLS | `llms.txt` |
|---|---|---|---|---|
| Početna | **1/1** | ✅ svi prošli | 0,005 | notApplicable (v. §2) |
| /industrijski-podovi/ | **1/1** | ✅ svi prošli | 0,000 | notApplicable |
| /sportske-podloge/ | **1/1** | ✅ svi prošli | 0,000 | notApplicable |
| Conquest 2542 (epoksid) | **1/1** | ✅ svi prošli | 0,001 | notApplicable |
| /kategorija-proizvoda/zastita-i-bumperi/ | **1/1** | ✅ svi prošli | 0,000 | notApplicable |
| /katalog/ | **1/1** | ✅ svi prošli | 0,008 | notApplicable |

**WebMCP sve 3 provere: notApplicable svuda** — sajt ne implementira WebMCP
(očekivano, nema forme/JS koje registruju tools). Ovo NIJE bug za popravku
— WebMCP je eksperimentalna, tek-nastajuća web platforma (isti orbit kao
BLOK D AI chat, [[blokovi/BLOK-D-ai-chat]]), implementacija bi bila
proizvodna/strateška odluka, ne tehnički fix.

## 2. 🔴 `llms.txt` notApplicable — LAŽNA CRVENA, potvrđeno lokalni path artefakt

Gatherer traži fajl na **korenu domena**: `new URL('/llms.txt',
finalDisplayedUrl)` → za lokalni build to je `http://localhost/llms.txt`,
**ne** `http://localhost/antasline/llms.txt`. Izmereno direktno:

```
curl -I http://localhost/llms.txt            → 404 (XAMPP docroot je htdocs/, ne htdocs/antasline/)
curl -I http://localhost/antasline/llms.txt  → 200
```

Fajl fizički postoji (`C:\xampp\htdocs\antasline\llms.txt`, kreiran W2 2.8
2026-07-08) i **sadržaj prolazi sve provere kad se pročita ručno**: ima H1
(`# AntasLine`), ima markdown linkove (17), dužina daleko preko 50
karaktera. Na produkciji je WP koren = domen koren (nema `/antasline/`
podfolder), pa će `/llms.txt` raditi ispravno **kad god se sadržaj lokalnog
fajla prenese na live** — sam fajl već napominje "aktivira se na migraciji
(2026-08-31)". **Nema akcije potrebne ovde**, isti obrazac kao ranije
lokal-vs-live path razlike u [[reference/naucene-lekcije]].

## 3. Odnos prema postojećem a11y skoru (W3 3.5 baseline, 2026-07-09)

Standardni Lighthouse Accessibility skor je bio **84–90** (ne 100) na
istim tipovima stranica — `agent-accessibility-tree` NIJE isto: to je uži
podskup od ~29 pravila fokusiran na imena/uloge/ARIA (šta agent MORA da
razume da bi interagovao), ne pun a11y audit (kontrast, tab red, itd.).
**1/1 ovde ne znači da je sajt 100% accessible** — znači da specifično taj
uži skup mašinski-kritičnih pravila prolazi, verovatno kao nusefekat W7/W8
polish rada (linkovi dobili podvlačenje+ime, forme imaju label, dugmad
imaju vidljiv tekst). Pun a11y audit ostaje na 84–90 baseline-u dok se ne
ponovi.

## 4. Zaključak i preporuka

- **Nema otvorenih akcija iz ove kategorije za lokalni build.** Sve
  merljivo (a11y podskup, CLS) već prolazi; jedina "crvena" stavka
  (`llms.txt`) je dokazano lažna zbog lokalne poddirektorijum strukture,
  ne stvarnog nedostatka.
- **Post-migracija provera (uz 3.12/5.7):** ponoviti ovaj audit na živom
  `antasline.com` posle 2026-08-31 — tada `llms.txt` provera treba da
  prođe stvarno (ne samo teorijski), pošto koren domena postaje pravi WP
  koren.
- **WebMCP ostaje van obima do BLOK D odluke** — pratiti kao mogući budući
  kanal (Chrome-only, eksperimentalno), ne graditi sad.
- Alat/metod za ponavljanje: `node_modules/.bin/lighthouse <url>
  --config-path=node_modules/lighthouse/core/config/agentic-browsing-config.js
  --only-categories=agentic-browsing --chrome-flags="--headless=new"
  --output=json` iz npx keš foldera (`ls
  ~/AppData/Local/npm-cache/_npx/` → naći folder sa `node_modules/lighthouse`).
- 🟡 Sitna gotcha: Lighthouse CLI baca benignu stack-trace grešku iz
  `chrome-launcher` `destroyTmp()` posle `--quiet` završetka na Windows-u
  (tmp folder cleanup race) — JSON izlaz se i dalje ispravno piše pre te
  greške, ne prekida rezultat, potvrđeno na svih 6 prolaza.

## Veze
[[PROGRESS]] · [[dnevnik/PERFORMANCE-AUDIT]] · [[blokovi/BLOK-D-ai-chat]] · [[reference/naucene-lekcije]]
