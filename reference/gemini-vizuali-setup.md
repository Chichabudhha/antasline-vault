---
tip: referenca
datum: 2026-08-04
namena: Miroslavljev jednokratni setup checklist za Gemini foto/video sloj (ai-vizuali) + opciono CCR ruter
status: nije počelo — čeka Miroslavljeve korake
---

# 🔌 Setup — AI vizuali (Gemini) + opciono CCR ruter

> Ovo je **tvoj** checklist — ja (Claude Code) nemam pristup tvom Google/
> DeepSeek/Groq nalogu, pa svaki korak ispod moraš ti da izvedeš. Kod
> (skripte) je već napisan: generički deo u
> `~/.claude/skills/ai-vizuali/`, AntasLine specifičan deo u
> `.claude/skills/gemini-vizuali/`.

**Zašto poseban folder od `antasline-connector`**: ovaj sloj NIJE
AntasLine-specifičan — treba da radi i na budućim projektima bez ponovnog
setupa. Zato ključevi/venv/logovi žive u
`C:\Users\Miroslav\ai-tools\` (potpuno van git-a, van svakog projekta), ne u
`antasline-connector` (taj ostaje isključivo Google Ads/GA4/GSC/GMB
izveštavanje).

## Korak 1 — Gemini API ključ

1. Idi na [Google AI Studio](https://aistudio.google.com/) → napravi API
   ključ (besplatan, bez kartice za osnovni free tier).
2. Sačuvaj ključ kao čist tekst (bez navodnika, bez novog reda) u:
   `C:\Users\Miroslav\ai-tools\credentials\gemini_api_key.txt`

## Korak 2 — Python venv

```
cd C:\Users\Miroslav\ai-tools
python -m venv venv
venv\Scripts\pip.exe install -r "C:\Users\Miroslav\.claude\skills\ai-vizuali\requirements.txt"
```

## Korak 3 — test poziv

```
venv\Scripts\python.exe "C:\Users\Miroslav\.claude\skills\ai-vizuali\scripts\gemini_image.py" --mode generate --out test.webp --prompt "a simple red square on white background" --project test
```

Ako ispiše `✓ Gemini generate -> test.webp | +1 slika (1/500 danas) | reset za HH:MM PT` — setup radi.

## Korak 4 (opciono) — CCR ruter (DeepSeek/Gemini tekst rutiranje)

`claude-code-router` (CCR) menja koji model fizički izvršava određene
kategorije Claude Code poziva (`background`, `longContext`) kad se sesija
pokrene preko `ccr code` umesto `claude`. Normalan `claude` poziv ostaje
netaknut — ovo je opt-in po sesiji.

1. Proveri da imaš Node.js instaliran (`node -v`).
2. `npm install -g @musistudio/claude-code-router`
3. API ključevi za DeepSeek/Groq (ako ih koristiš) — isti obrazac kao Korak 1,
   u `C:\Users\Miroslav\ai-tools\credentials\deepseek_api_key.txt` /
   `groq_api_key.txt`.
4. Napravi `~/.claude-code-router/config.json` (van vault-a, van git-a):
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
   (Tačna imena provajder/model polja i eventualni fallback-sintaksa —
   proveriti u CCR dokumentaciji pri instalaciji, može se razlikovati po
   verziji.)
5. Test: `ccr code` u vault folderu, zadaj trivijalan zadatak, proveri u CCR
   logu da je stvarno otišao ka DeepSeek-u (ne Anthropic-u). Zatim proveri
   da običan `claude` poziv (bez `ccr`) i dalje radi nepromenjeno.

**Napomena o riziku**: kad je CCR aktivan, `background`/`longContext`
pozivi fizički putuju ka DeepSeek (Kina) / Google serverima umesto
Anthropic-u. Prihvatljivo za marketing/SEO rad, ali ne koristiti na
osetljivim zadacima bez razmišljanja o tome.

## Kad javiti da je gotovo

Korak 1–3 (Gemini) je dovoljno da foto rad krene — javi kad `gemini_image.py`
test prođe. Korak 4 (CCR) je potpuno opcion i nezavisan, može doći kasnije
ili nikad ako se DeepSeek eksperiment ne pokaže vrednim.

## Veze
- `~/.claude/skills/ai-vizuali/SKILL.md` — generički Gemini sloj
- `.claude/skills/gemini-vizuali/SKILL.md` — AntasLine specifičan sloj
- `reference/identifikatori.md` — javni model ID-evi
- `reference/standard-slika-proizvoda.md` — proizvod-slika spec
- `blokovi/BLOK-E-ai-orkestracija.md` — pun kontekst odluka
