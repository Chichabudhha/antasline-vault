---
tip: blok
blok: E
status: aktivno — Gemini foto sloj RADI, ceo red čekanja (96/97 proizvoda, Tier 1-4) obrađen 2026-08-05, CCR odbačen (M odluka 2026-08-05, nije potreban)
azurirano: 2026-08-05
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
| **Gemini API** (slike, `gemini-2.5-flash-image`/`3.1-flash-lite-image`) | 🔴 **`limit: 0` — API nema besplatnu image kvotu za ovaj nalog**, obara originalnu pretpostavku "~500/dan besplatno". Tekst-only pozivi (`gemini-2.5-flash`) rade na free tier bez problema. | Foto (primarno) — **zahteva billing** | Testirano uživo 2026-08-04 protiv 2 modela (2.5 i 3.1 lite), oba `RESOURCE_EXHAUSTED`/limit 0. M dodao platnu karticu na GCP projekat → API radi (potvrđeno, jeftino po slici, ~500/dan i dalje važi kao SOFT limit u `quota_tracker.py`, sad je to naš izbor koliko trošimo, ne Google-ov free-tier plafon) |
| **Gemini CHAT** (gemini.google.com, konzumerski nalog) | Odvojena besplatna kvota od API-ja, bez kartice | Foto — **fallback/ručna dopuna preko Chrome automatizacije** | Testirano uživo 2026-08-04: prompt → generisanje (~15s) → download dugme → fajl u `Downloads/`. Radi, ali nije skriptabilno (nema headless pristup) — Claude vozi kroz `claude-in-chrome` alat po potrebi, jedna slika po ciklusu, ne za veliki batch |
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
- ✅ `C:\Users\Miroslav\ai-tools\` — credentials/ + logs/ + venv/ (svi
  popunjeni, setup ZAVRŠEN)
- ✅ `.claude/skills/gemini-vizuali/` — AntasLine projektni skill
- ✅ `reference/gemini-vizuali-setup.md` — jednokratni checklist (Gemini +
  opciono CCR korak 4)
- ✅ `reference/identifikatori.md` — dopunjen javnim model ID-evima
- ✅ `reference/gemini-red-cekanja.md` — prazan red čekanja, popuniti prvom
  sledećom foto sesijom
- ✅ **Gemini API image generisanje — RADI, testirano uživo 2026-08-04.**
  Put do rada: free tier `limit:0` otkriven → M dodao billing karticu na
  GCP projekat → test poziv uspešan (`test.webp` generisan i obrisan).
  Usput 2 bug-fixa u skripti: (1) Windows nema IANA tz bazu → `tzdata`
  paket dodat u `requirements.txt` uz `sys_platform=="win32"` marker,
  (2) `quota_tracker.py` je pucao na `✓` karakteru u Windows cp1250
  konzoli → `sys.stdout.reconfigure(encoding="utf-8")` dodat.
- ✅ **Gemini CHAT (Chrome) fallback — testiran uživo 2026-08-04.**
  `claude-in-chrome` alat otvara gemini.google.com (već ulogovan
  Miroslavljev nalog), prompt → generisanje (~15s) → download dugme
  (gornji desni ugao slike) → fajl pada u `~\Downloads\`. Nema headless/
  skriptovan pristup (Google nema public API za konzumerski chat) — ovo
  je ručni Claude-vođeni tok za pojedinačne slike, ne za veliki batch.
  Koristiti kad: API budžet/kvota postane briga, ili za eksperimentalne
  prompte pre nego što se puste na API trošak.

## Odluka o rutiranju foto rada (2026-08-04)

**Primarno: Gemini API** (skriptabilno, pouzdano, jeftino po slici — sad
uz billing). **Sekundarno/fallback: Gemini Chat kroz Chrome** (besplatno,
ali ručno, jedna slika po ciklusu) — koristi se svesno, ne default.

## Prva prava foto-batch — 2026-08-05

Red čekanja popunjen (`reference/gemini-red-cekanja.md`): audit svih 94
objavljenih proizvoda (`getimagesize()` na svaku `_thumbnail_id` datoteku)
protiv standarda (1080×1080, kvadrat, bela pozadina) → **9 već OK, 72
kandidata za `--mode enhance`, 13 bez ijedne fotografije u arhivi** (ti
ostaju van reda, ne idu na Gemini — v. politika ispod).

**Pravilo utvrđeno ovom sesijom: `--mode enhance` da, `--mode generate`
od-nule ne za konkretan brendiran/tehnički proizvod** (rizik pogrešne boje/
dimenzije/spoja, isto obrazloženje kao ranija W7 F2.9 politika o nefotografisanim
proizvodima). Upisano u skill Gotchas.

**Prvih 5 proizvoda stvarno obrađeno (backup baze pre rada:
`antasline_local_2026-08-05_pre-gemini-foto-pilot.sql`)**: Ecotile E500/7
(#16538, attach 17522), Ecotile E500/10 (#16540, attach 17524), Ecotile ESD
7mm (#16542, attach 17525), Bergo Ultimate (#16770, attach 17523), Bergo
Ultimate FLOW (#16801, attach 17526). Svih 5: HTTP 200, `_thumbnail_id`
ažuriran, HTML referencira novi fajl — verifikovano end-to-end. Stare slike
nisu obrisane (ostaju kao odvojeni attachment-i, bezbedan rollback).

🔴🔴 **Nov nalaz: SKILL.md korak 4 je bio pogrešan** — "snimi direktno u
uploads folder" nije dovoljno da se slika pojavi kao glavna slika proizvoda;
mora postati pravi WP attachment. Napisan i testiran
`.claude/skills/gemini-vizuali/scripts/import-gemini-photo.php`
(`wp_insert_attachment()` + `wp_generate_attachment_metadata()` +
`set_post_thumbnail()`), SKILL.md ažuriran da ga referencira.

🔴 **Nov nalaz: generički `gemini_image.py` je jednom pao** na
`AttributeError: 'NoneType' object has no attribute 'parts'` (odgovor bez
slike uprkos `finish_reason=STOP`) — izgleda kao prolazna API varijacija,
prost retry je rešio (potvrđeno 2x). Nije potrebna izmena skripte za sada.

Kvota posle batch-a: 493/500 preostalo (kvota se resetuje ponoć PT).

## Foto-batch #2 i #3 — 2026-08-05 (isti dan, nastavak sesije) — CEO RED ZATVOREN

**Batch #2** (5 slika): preostalih 5 proizvoda Tier 1 (Ecotile T-Joint/X-Joint
rampe + SureGrip profil) — Tier 1 time potpuno zatvoren (8/8). Kvota posle:
12/500 danas.

**Batch #3** (61 slika, jedan skriptovani prolaz): ceo ostatak reda čekanja
odjednom — Tier 2 (16/17, Bergo linija + košarkaške konstrukcije), Tier 3
(17/17, Geoplast travne rešetke + EXPONA/R-Tile LVT + veštačka trava),
Tier 4 (28/28, Ergomat bumperi/DuraStripe trake/senzori). Napravljen
`run_batch3.py` — Python driver koji direktno importuje `call_gemini()` i
`save_formatted()` iz generičkog `gemini_image.py` (izbegava subprocess
overhead za CLI po stavci), sa retry (2 pokušaja) po stavci na
`AttributeError`/API grešku, zatim poziva `import-gemini-photo.php` preko
`subprocess` za svaki uspešan Gemini poziv. **0 grešaka na 61 poziva**,
sve HTTP 200 na spot-proveri. Preskočen #16536 (Zglobni obruč za koš) —
outofstock, ostaje #ceka-miroslav (potvrda da je proizvod aktivan) pre
nego što se ulaže Gemini rad na njega, po istoj logici kao NO_THUMBNAIL
politika.

Kvota posle batch #3: **73/500 danas, 427 preostalo.** Ceo katalog (96/97
stavki, sve osim outofstock #16536) sada ima standardizovanu 1080×1080
belu glavnu sliku. Sledeći foto rad: novi proizvodi kako se dodaju u
katalog, ili re-run kad Miroslav odobri stvarne fotografije za 13
NO_THUMBNAIL proizvoda.

## CCR — ODUSTALO 2026-08-05 (nakon istrage, M odluka)

Miroslavljev prvi pokušaj instalacije se srušio usred rada; u nastavku
(sledeća sesija) dijagnostikovano:

- ✅ `~/.claude/settings.json` — čist, NIJE prepisan, ostao netaknut
- 🔴 `ccr` komanda nije nikad ostala instalirana (npm paket se nije
  dovršio pre pada) — nema rezidualnog stanja, čist povratak na nulu
- **Verovatan uzrok kvara** (istraženo WebSearch/WebFetch, GitHub README +
  ClaudeLog): CCR nema način da "default" rutira kroz postojeću Claude Code
  pretplatu/login — svaka kategorija, uklj. `default`/`think`, mora imati
  pravi provajder+API ključ u `config.json`. Ako je "anthropic" ostao kao
  default bez posebnog Anthropic Console API ključa (odvojenog, plativog
  po tokenu, različitog od pretplate), CCR traži ključ koji Miroslav nema
  → otud "traži API" i nepovezani/zbunjeni odgovori od pogrešno
  konfigurisanog provajdera.

**M odluka (2026-08-05, posle razjašnjenja):** CCR se **ne instalira**.
Nema stvarne potrebe — foto/video rad već ide direktno kroz
`~/.claude/skills/ai-vizuali/` (Gemini API poziv iz skripte, bez ikakvog
proxy sloja), a istraživanje/kod/planiranje već radi Claude sâm. CCR bi
doneo samo optimizaciju troška (jeftiniji model za pozadinske Claude Code
pozive), što je bila eksperimentalna, ne stvarna potreba — rizik/setup
(API ključevi, config, prošli pad) ne vredi za to. Routing predlog iznad
u fajlu ostaje kao istorijska referenca, ne kao aktivan plan.

## Foto arhiva (Downloads) — inventar 2026-08-05, čeka M odluku

Van postojećeg proizvod-foto reda: `C:\Users\Miroslav\Downloads` sadrži
~185 pravih referentnih/instalacionih fotografija (sport tereni Bergo/3×3/
pickleball/tenis ~100, ESD/antistatik montaže ~10, Geoplast/Runfloor ~20,
Ergomat Isotrack/X-Mat/Mosolut ~8) koje nikad nisu ušle u sajt. Pun inventar
i kategorizacija: `reference/foto-arhiva-inventar.md`. Samo organizovano,
ništa postavljeno — v. #ceka-M ispod.

## Šta čeka Miroslava

- Foto arhiva (Downloads) — ✅ kategorizacija ~7 fotki bez jasne ključne reči
  u imenu ZATVORENA 2026-08-07 (vizuelni pregled, svih 7 potvrđeno kao
  sportski tereni). I dalje otvoreno: (1) poreklo/prava za Geoplast i
  Ergomat materijal, (2) format upotrebe (referenca-galerija na proizvodima
  / posebna "Reference" stranica / blog). Detalji: `reference/foto-arhiva-inventar.md`.

## Veze
- `.claude/skills/gemini-vizuali/SKILL.md`
- `~/.claude/skills/ai-vizuali/SKILL.md`
- `reference/gemini-vizuali-setup.md`
- `reference/identifikatori.md`
- `reference/gemini-red-cekanja.md`
- `reference/standard-slika-proizvoda.md`
- `2026-07-06-MASTER-PLAN-V2.md` §8
