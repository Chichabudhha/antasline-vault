---
name: gemini-vizuali
description: Foto/video rad za AntasLine preko Gemini AI-ja — unapređenje postojećih proizvod fotografija, generisanje novih/sličnih varijanti, priprema video materijala za sajt/oglase/social. Koristi kad Miroslav kaže "gemini", "foto", "slike proizvoda", "unapredi fotografije", "video za sajt/oglase/social", "vizuali". Poziva generički `~/.claude/skills/ai-vizuali/` sloj (Gemini API) i dodaje AntasLine specifičnosti: proizvod-spec, WooCommerce upload putanja, GSC-bazirani red čekanja.
---

# gemini-vizuali — AntasLine foto/video preko Gemini AI-ja

Ovo je **projektni sloj** — poziva generičke skripte iz
`~/.claude/skills/ai-vizuali/` (user-level, cross-project) i dodaje sve što
je specifično za AntasLine: koji proizvod je sledeći, kakav mora biti
format, gde se snima fajl.

**Kredencijali** žive u `C:\Users\Miroslav\ai-tools\credentials\` (generički
folder, deljen sa svakim budućim projektom koji koristi isti sloj — **ne**
`antasline-connector`, taj je isključivo za Google Ads/GA4/GSC/GMB
izveštavanje). Setup checklist: `reference/gemini-vizuali-setup.md`.

## Prioritetni red (foto rad)

1. Izvor: GSC top-saobraćaj stranice (`antasline-konektor/scripts/gsc_report.py`)
   + W1/W2 plan stranica bez kvalitetnih slika + katalog proizvoda bez
   standardizovane slike (stari 2022 import, vidi memoriju "Product image
   spec").
2. Red čekanja se drži u `reference/gemini-red-cekanja.md` — ja ga
   ažuriram i javljam status na početku svake sesije ("sledećih N proizvoda
   spremno za Gemini batch").
3. Batch veličina ograničena dnevnom Gemini kvotom (500/dan, deljeno preko
   svih projekata — proveri `remaining_today()` pre planiranja batch-a).
4. **Rad ide automatski po ovom redu** — ne čeka se "sledeći" komanda za
   svaku stavku (Miroslavljeva odluka).

## Foto workflow (automatski, API)

1. Učitaj sledeći proizvod/stranicu iz reda čekanja.
2. Pozovi generički skript sa AntasLine parametrima:
   ```
   cd C:\Users\Miroslav\ai-tools
   venv\Scripts\python.exe "C:\Users\Miroslav\.claude\skills\ai-vizuali\scripts\gemini_image.py" ^
     --mode enhance --input <postojeca-slika> --out <ciljna-putanja>.webp ^
     --width 1080 --height 1080 --format webp ^
     --prompt "<prompt iz reference/standard-slika-proizvoda.md, popunjen imenom/bojom proizvoda>" ^
     --project antasline
   ```
   Za potpuno nove/slične varijante: `--mode generate` bez `--input`.
3. Spec (`reference/standard-slika-proizvoda.md`): 1080×1080, čista bela
   pozadina (#FFFFFF), ~15% margina, WebP, ime fajla
   `<slug-proizvoda>-<boja>.webp`.
4. Fajl iz koraka 2 ide u scratchpad, ne direktno u uploads — mora postati
   pravi WP attachment (ne samo fajl na disku) da bi se pojavio kao glavna
   slika: `php .claude/skills/gemini-vizuali/scripts/import-gemini-photo.php
   <product_id> <src.webp> <ime-fajla>.webp`
   (bootstrap-uje `wp-load.php`, `wp_insert_attachment()` +
   `wp_generate_attachment_metadata()` + `set_post_thumbnail()`). Skripta
   testirana i potvrđena 2026-08-05 (Ecotile E500/7 #16538, Bergo Ultimate
   #16770 — oba HTTP 200, HTML referencira novi fajl, `_thumbnail_id`
   ažuriran). **Bez traženja odobrenja pre postavljanja** (Miroslavljeva
   odluka), on pregleda naknadno na sajtu. Stara slika ostaje kao odvojen
   attachment (nije obrisana) — bezbedan rollback ako treba.
5. Upiši kratku belešku u `PROGRESS`/dnevnik šta je urađeno (koji proizvod,
   koliko slika, koliko kvote ostalo).

## Video workflow (ručno, browser — Veo nema free API)

1. Ja pripremam tačan prompt/shot-listu za Gemini app ili Google Flow
   (50 free kredita/dan).
2. Miroslav generiše u web UI, preuzima fajl.
3. Miroslav javlja da je gotovo → upućujem ga gde da snimi fajl
   (uploads/social folder), logujem u dnevnik.

**Video se NIKAD ne pokušava kroz API** — Veo nema free tier, ne graditi
lažnu automatizaciju.

## Foto rezerva (ručno, browser)

Microsoft Designer / Bing Image Creator (DALL-E 3, 15 brzih
generacija/nedeljno + spori red posle toga) — koristi se kad:
- Gemini dnevna kvota padne na 0, ili
- treba lifestyle/scenska slika (proizvod u realnom prostoru/hali/terenu)
  gde DALL-E 3 stil bolje pristaje od studijske proizvod-fotke.

Isti obrazac kao video: ja pripremam prompt, Miroslav generiše, javlja,
logujem.

## Kvota

Deljena preko svih projekata (isti Gemini nalog) — proveri
`ai-vizuali/scripts/quota_tracker.py: remaining_today()` pre planiranja
batch-a. Generički skript ispisuje `✓ Gemini {akcija} | +1 slika (N/500
danas) | reset za HH:MM PT` posle svakog poziva.

## Gotchas

- Srbija je podržan region za Gemini API — proxy nije potreban. Ako se
  pojavi "region not supported", to je verovatno false-positive po IP
  detekciji, ne stvarno ograničenje.
- Video (Veo) nema free API tier — samo web UI.
- Ako Gemini vrati format koji ne odgovara spec-u, generički skript radi
  eksplicitan resize/convert — ne treba ručna doobrada.
- 🆕 **Generisanje samo od-nule (`--mode generate`, bez `--input`) se NE
  koristi za konkretan brendiran/tehnički proizvod** — rizikuje pogrešnu
  boju/dimenziju/spoj. `--mode enhance` (postojeća prava fotografija na
  ulazu) je bezbedan jer model samo čisti kadar/pozadinu. Isto obrazloženje
  kao W7 F2.9 politika (ne gurati sliku koja implicira tuđi posao) —
  proizvodi bez ijedne fotografije u arhivi (`reference/gemini-red-cekanja.md`
  "Van reda") ostaju #ceka-miroslav, ne idu na Gemini.
- 🆕 **Generički `gemini_image.py` retko vrati odgovor bez `inline_data`**
  (potvrđeno 2026-08-05, `AttributeError: 'NoneType' object has no attribute
  'parts'`) čak i uz ispravan `finish_reason=STOP` — izgleda kao prolazna
  API varijacija, ne sistemska greška. Prost retry istog poziva je rešio
  problem oba puta testirano. Nije (još) potrebna izmena skripte, samo
  ponoviti poziv ako padne.
- 🆕 **`--mode enhance` fajl mora stvarno postati WP attachment**, ne samo
  fajl u uploads folderu — v. korak 4 iznad (`import-gemini-photo.php`,
  testiran 2026-08-05). Sam fajl na disku se neće pojaviti kao proizvod
  slika bez `wp_insert_attachment()`/`set_post_thumbnail()`.

- 🆕 **Označavanje AI-generisanih slika (Google politika, pročitano 2026-08-12).**
  Za običnu organsku pretragu nema obaveznog markupa. 🔴 **Ali Merchant Center
  traži** da AI-generisana slika nosi IPTC `DigitalSourceType` =
  `TrainedAlgorithmicMedia`, a AI-generisani podaci o proizvodu se posebno
  označavaju. Naši `--mode enhance` izlazi su izmenjene prave fotografije, ne
  čista sinteza, ali granica nije oštra — **ako projekat ikad krene sa
  Merchant Center feed-om, prvo proveriti koji od već objavljenih vizuala traži
  oznaku**, pre nego što feed ode. Do tada nema akcije. v.
  [[seo/geo-ai-plan]] §0.

## Veze
- `~/.claude/skills/ai-vizuali/` — generički Gemini image sloj (cross-project)
- `reference/gemini-vizuali-setup.md` — jednokratni setup checklist
- `reference/identifikatori.md` — javni model ID-evi
- `reference/standard-slika-proizvoda.md` — proizvod-slika spec
- `reference/gemini-red-cekanja.md` — red čekanja za foto rad
- `antasline-konektor` — GSC podaci za prioritetni red
- `w6-social` — video/vizuali za social objave
- `obogati-proizvod` — foto je deo product enrichment toka
- `blokovi/BLOK-E-ai-orkestracija.md` — pun kontekst odluka ove sesije
