---
tip: referenca
azurirano: 2026-08-04
namena: Red čekanja za Gemini foto rad (prioritet + status) — održava Claude Code
---

# 📋 Gemini red čekanja — foto rad

> Prazan na startu (2026-08-04) — implementacija `gemini-vizuali` skilla je
> tek napravljena, foto batch rad još nije počeo. Popuniti prvom sledećom
> sesijom koja radi na ovome: povući GSC top-saobraćaj stranice
> (`antasline-konektor/scripts/gsc_report.py`) + proveriti koji proizvodi u
> katalogu nemaju standardizovanu sliku (`reference/standard-slika-proizvoda.md`).

## Kako se koristi

- Status kolona: `⏳ čeka` → `🔄 u radu` → `✅ gotovo`
- Redosled = prioritet (najvažnije na vrhu), ne hronologija
- Batch veličina po sesiji ograničena dnevnom Gemini kvotom (500/dan,
  deljeno preko svih projekata koji koriste isti ključ)

## Red

| # | Proizvod/stranica | Razlog prioriteta | Status |
|---|---|---|---|
| — | (popuniti) | — | — |

## Veze
- `.claude/skills/gemini-vizuali/SKILL.md`
- `reference/standard-slika-proizvoda.md`
