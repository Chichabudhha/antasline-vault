---
tip: blok
blok: E
status: aktivno — Gemini foto sloj implementiran, CCR čeka Miroslavljev setup
azurirano: 2026-08-04
---

# BLOK E — AI orkestracija (Gemini foto/video + DeepSeek/CCR ruter)

> Nastalo iz sesije 2026-08-04 ("želim da iskoristim i Gemini AI za rad na
> AntasLine"). Miroslav potvrdio (AskUserQuestion + razjašnjenje): Gemini
> preuzima sav foto/video rad (unapređenje postojećih fotografija,
> generisanje novih/sličnih varijanti, video za sajt/oglase/social) u free
> modu, Claude vodi redosled i prati kvotu. DeepSeek je eksperimentalna
> ideja za rasterećenje kodiranja preko `claude-code-router` (CCR) —
> pomenuti "proksi" se ispostavio da je zapravo ovaj alat, ne mrežni proxy.
> Dodatan zahtev: setup treba da bude **ponovo upotrebljiv na budućim
> projektima**, ne samo AntasLine.

## Arhitektura — dva sloja

1. **Generički sloj (cross-project)** — `~/.claude/skills/ai-vizuali/`
   (user-level skill, važi u svakom projektu) + `C:\Users\Miroslav\ai-tools\`
   (kredencijali/venv/logovi, potpuno van git-a i van bilo kog konkretnog
   projekta). Sadrži goli Gemini image API poziv + kvota tracker, bez
   AntasLine pretpostavki.
2. **Projektni sloj (AntasLine)** — `.claude/skills/gemini-vizuali/` u ovom
   vault-u, poziva generički sloj i dodaje: proizvod-spec
   (`reference/standard-slika-proizvoda.md`), WooCommerce upload putanju,
   GSC-bazirani red čekanja (`reference/gemini-red-cekanja.md`).

Namerno odvojeno od `antasline-connector` (koji ostaje isključivo Google
Ads/GA4/GSC/GMB izveštavanje) — `ai-tools` folder je nov, generičan, da bi
budući projekat mogao da doda samo svoj tanak projektni skill bez ponovne
instalacije/kredencijala.

## Istraženo (WebSearch, 2026-08-04) — free tier poređenje

| Provajder | Free tier | Pogodno za | Napomena |
|---|---|---|---|
| **Gemini** (slike, `gemini-2.5-flash-image`) | ~500 poziva/dan, reset ponoć PT | Foto (primarno) | Najizdašniji free image API, Srbija podržan region |
| **Gemini** (tekst, Flash/Pro) | Visok rate limit, veliki kontekst | Long-context tekst | CCR `longContext` kandidat |
| **Gemini Veo** (video) | **Nema free API tier** | — | Samo web UI (Gemini app/Google Flow, 50 kredita/dan) — ručni tok |
| **DeepSeek** | 5M tokena, 30-dnevni grant po prijavi | Kodiranje | Dobar kvalitet, kineska infrastruktura |
| **Groq** | Free API, vrlo brz | Jeftini/brzi "background" pozivi | Fallback za DeepSeek, bez kineske infrastrukture |
| **Kimi/GLM/Qwen** | Razni free/trial | Kodiranje (rezerva) | CCR podržava, dodati tek ako DeepSeek+Groq ne dostaju |
| **OpenAI (GPT) API** | Free samo uz opt-in data-sharing | Kodiranje/tekst | Ne uključivati bez eksplicitnog OK (privatnost) |
| **Microsoft Designer/Bing Image Creator** | 15 boost/nedeljno + spor red, DALL-E 3 | Foto — rezerva | Web UI, ručni tok, dobar za lifestyle scene |

## Odluke Miroslava (potvrđeno, AskUserQuestion)

- [x] Foto/video ide **automatski po prioritetu** koji Claude definiše
  (GSC saobraćaj/W1-W2 plan) — ne čeka se komanda za svaku stavku.
- [x] **Bez odobrenja pre postavljanja** — Gemini izlaz ide direktno u
  WooCommerce uploads, Miroslav pregleda naknadno.
- [x] DeepSeek uloga: eksperimentalna, obim TBD — probaćemo uzgred preko CCR.
- [x] "Proksi" = CCR (`claude-code-router`), ne mrežni proxy za region —
  Gemini API ne treba proxy (Srbija podržan region).
- [x] Setup mora biti ponovo upotrebljiv na budućim projektima → dvoslojna
  arhitektura (generički + projektni).

## CCR routing (predlog, čeka Miroslavljev setup)

```json
{
  "Router": {
    "default": "anthropic,claude-sonnet-5",
    "background": "deepseek,deepseek-chat",
    "think": "anthropic,claude-sonnet-5",
    "longContext": "gemini,gemini-2.5-pro",
    "longContextThreshold": 60000
  }
}
```

`default`/`think` ostaju na Claude-u (glavna kompozicija se ne prosleđuje).
`background` → DeepSeek primarno, Groq kao fallback. `longContext` → Gemini
tekst (odvojeno od foto-generisanja). Aktivira se opt-in preko `ccr code`
umesto `claude` — normalan `claude` poziv ostaje netaknut.

## Šta je implementirano (2026-08-04)

- ✅ `~/.claude/skills/ai-vizuali/` — SKILL.md + `scripts/` (auth.py,
  gemini_image.py, quota_tracker.py) + requirements.txt
- ✅ `C:\Users\Miroslav\ai-tools\` — credentials/ + logs/ + README.md
  (folderi prazni, čekaju Miroslavljev API ključ)
- ✅ `.claude/skills/gemini-vizuali/` — AntasLine projektni skill
- ✅ `reference/gemini-vizuali-setup.md` — jednokratni checklist (Gemini +
  opciono CCR korak 4)
- ✅ `reference/identifikatori.md` — dopunjen javnim model ID-evima
- ✅ `reference/gemini-red-cekanja.md` — prazan red čekanja, popuniti prvom
  sledećom foto sesijom

## Šta čeka Miroslava

- Gemini API ključ → `ai-tools/credentials/gemini_api_key.txt` (Korak 1-3,
  `reference/gemini-vizuali-setup.md`)
- Python venv setup u `ai-tools/`
- (Opciono, kad/ako DeepSeek eksperiment krene) CCR instalacija + DeepSeek/
  Groq ključevi (Korak 4 istog fajla)

## Veze
- `.claude/skills/gemini-vizuali/SKILL.md`
- `~/.claude/skills/ai-vizuali/SKILL.md`
- `reference/gemini-vizuali-setup.md`
- `reference/identifikatori.md`
- `reference/gemini-red-cekanja.md`
- `reference/standard-slika-proizvoda.md`
- `2026-07-06-MASTER-PLAN-V2.md` §8
