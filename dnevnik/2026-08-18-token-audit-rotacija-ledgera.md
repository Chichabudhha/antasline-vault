---
tip: sesija
alat: claude-code
datum: 2026-08-18
blok: "-"
status: zavrseno
---

# Sesija — Token audit starta sesije + rotacija ledgera + numeracija CLAUDE.md

> Oblast: ALATI / vault higijena. Povod: „kad pokrenemo sesiju potroši se
> neuobičajeno mnogo tokena — analiziraj".

## Šta je urađeno

### 1. Dijagnoza — odakle 60k tokena pre prve reči

Izmereno iz **transkripata sesija** (`~/.claude/projects/C--Projekti-antasline-vault/*.jsonl`,
zbir `input + cache_creation + cache_read` na prvoj poruci), ne procenom:

| Sesija | Kontekst na prvoj poruci |
|---|---|
| 6bf8e3fd / 389980aa / 9adc6ad8 / 56ec830d / a416d838 | 59,2–59,9k |
| cdb84b71 / 761fb799 / 18b06798 | 53,8–55,6k |

**Baseline ~54–60k pre bilo kakvog rada.** Rastav:

| Stavka | ~Tokena | Kontrola |
|---|---|---|
| Sistemski prompt + definicije alata + ugrađeni skilovi | ~30–35k | ne |
| `CLAUDE.md` (37 KB) | ~12k | da |
| Opisi 21 skila (9.088 B ukupno) | ~2,7k | delimično |
| Memory index, git status | <1k | ne vredi |

Sesije sa `cache_read=0` plaćaju punih ~59k kao **cache write** (1,25×) — svaki hladan
start, za razliku od `/clear` unutar istog sata.

### 2. Pravi trošak nije bio baseline nego putanja otvaranja

Izvučeni prvi `Read` pozivi iz 5 poslednjih sesija:

| Sesija | Rast u prvih 12 poruka | Šta je čitano |
|---|---|---|
| `56ec830d` | **+70k** | PROGRESS (bez limita), master plan (bez limita), pa **opet** master plan `[limit 150]` |
| `9adc6ad8` | **+65k** | isto |
| `389980aa` / `6bf8e3fd` / `cdb84b71` | +23k / +20k / +14k | bez `Read`-a |

Dakle: dve od pet sesija su na otvaranju potrošile više nego ceo baseline, i to
na **PROGRESS + master plan**, pri čemu je master plan čitan dvaput (prvo ceo,
pa isti fajl sa limitom — prvo čitanje čist gubitak).

**Odvojena, još veća mina:** `DNEVNIK-NAPRETKA.md` = 988 KB / 6.320 linija.
`Read` bez `limit` povlači prvih 2000 linija = **160 KB ≈ 52k tokena**, a
potrebno je ~10 poslednjih unosa. U uzorku od 5 sesija nije eksplodirala, ali je
čekala.

### 3. Rotacija ledgera (`skripte/rotiraj-dnevnik.py`)

Nov skript, stalni alat u vault-u. Parsira `## YYYY-MM-DD` naslove, **sortira po
datumu** (ne po poziciji u fajlu), zadržava poslednjih N dana, starije prenosi
**doslovno** u `dnevnik/arhiva-dnevnik-YYYY-MM.md`.

| Fajl | Pre | Posle |
|---|---|---|
| `DNEVNIK-NAPRETKA.md` | 965 KB / 357 unosa | **20 KB / 13 unosa** (14–18.08) |
| `dnevnik/arhiva-dnevnik-2026-08.md` | — | 300 KB / 97 unosa |
| `dnevnik/arhiva-dnevnik-2026-07.md` | — | 639 KB / 238 unosa |
| `dnevnik/arhiva-dnevnik-2026-06.md` | — | 7 KB / 9 unosa |

**Verifikacija:** 357 unosa pre = 357 posle, `izgubljeno 0 / izmenjeno 0`,
955.509 B teksta identično bajt u bajt. Ceo rezultat je jednom rekonstruisan iz
backupa od nule da se to dokaže.

### 4. Popravljena hronologija — pravi bag, ne kozmetika

Ledger je newest-on-top, ali su **4 unosa bila na dnu fajla**:

```
linija 6231  2026-06-23
linija 6253  2026-07-10  [cpanel-live]
linija 6267  2026-07-30  [cpanel-live]
linija 6291  2026-08-13  [claude-code] FAZA 1
```

Unos od 13.08 je zbog toga bio „praktično nevidljiv" i **već je bio propušten iz
PROGRESS tabele** (dokumentovano u `dnevnik/2026-08-arhiva-progress.md`).
Rotacija ih je vratila na mesto — sada **0 prekida hronologije** u sva 4 fajla.

**Uzrok pronađen doslovno u uputstvima:**

| Fajl | Pisalo | Sada |
|---|---|---|
| `CLAUDE-CODE-instrukcija-CPANEL.md:23` | „DODAJ **(append) na kraj**" | „NA VRH … ne na kraj" |
| `CLAUDE-CODE-instrukcija.md:27` | „DODAJ (append, ne overwrite) red **na kraj**" | „NA VRH … ne na kraj" |
| `CLAUDE.md` §9.1 | „→ **append** `[cpanel-live]` unos →" | „`[cpanel-live]` unos **NA VRH**" |

### 5. Čišćenje otpada u tekstu

Skenirano 184 `.md` fajla / 3,26 MB. Ukupan otpad **7,2 KB = 0,2%** — čišćenje
tabela od 13.08 je držalo. Zero-width znakova **0**.

| Tip | Pre | Posle |
|---|---|---|
| Razmaci za poravnanje tabela | 6.952 B | 0 |
| Trailing razmaci | 44 B | 0 |
| BOM | 1 | 0 |

Očišćen 21 fajl, −6.997 B. Najveći: `reference/cenovnik.md` −2.191 B,
`seo/2026-07-27-content-klasteri.md` −1.832 B, `reference/drustvene-mreze.md` −1.224 B.
**Nijedan startni fajl nije bio zahvaćen.** Emoji provereni i namerno ostavljeni
(~300 tokena ukupno u tri startna fajla, nose informaciju).

### 6. Numeracija `CLAUDE.md` — razrešeno duplo §9

`CLAUDE.md` je imao **dve sekcije numerisane 9** (WORKFLOW I ALATI, KLJUČNE
LEKCIJE), a podsekcije workflow-a su nosile brojeve **8.1–8.7**. Posledica:
10 referenci na „CLAUDE §9" iz drugih fajlova, dvosmislenih u oba smera
(telefon/silo/throttling → lekcije; tri-surface workflow → workflow).

Sada 1–16 bez ponavljanja: 8.1–8.7 → **9.1–9.7**; lekcije → **§10**;
FORMAT → §11, ULOGE → §12, GDE PROVERITI → §13, HUB → §14, ISTORIJSKI → §15,
SLEDEĆI PUT → §16. Mapa prevoda starih brojeva stoji u samom `CLAUDE.md` §10.

Ažurirano **14 živih referenci** (skilovi, komande, master plan, `analiza/_README`,
`chrome-web-platform-2026`, `naucene-lekcije`, `ADS-DNEVNIK`) — uključujući
`description:` skila `/nedeljni-izvestaj`, koja se učitava na svakom startu.
**114 referenci u 38 datiranih fajlova** (dnevnik, arhive, `analiza/2026-*`)
namerno **nije** dirano — to su zapisi šta je tada urađeno, mapa ih pokriva.

Usput ispravljena **postojeća pogrešna referenca**:
`migracija/w1-polish-red-cekanja.md:97` je tvrdio da je GEO pravilo „prvi pasus =
direktan odgovor" u `CLAUDE.md` §10 — nikad nije bilo tamo (to je format
izveštavanja); pravilo živi u `/antasline-sesija` W2. Preusmereno.

### 7. Rezultat

| Putanja otvaranja | Pre | Posle |
|---|---|---|
| PROGRESS | 38.890 B | 38.766 B |
| Ledger | **160.722 B** (Read = 2000 linija) | **19.635 B** (ceo fajl) |
| Master plan | 77.219 B (ceo) | 3.350 B (§2 preko `sed`) |
| **Ukupno** | **276.831 B ≈ 87k tokena** | **61.751 B ≈ 19k tokena** |

**−67k po otvaranju (−78%).** Fiksni deo starta (~55–60k) je **nepromenjen**;
`CLAUDE.md` je čak narastao 37,1 → 38,5 KB zbog mape numeracije (+~450 tokena
po sesiji, otplativo brisanjem mape posle migracije).

## Otvorene akcije

- [ ] `PROGRESS.md` → sekcija „Blokeri" je **27.107 B = 70% fajla**, 45 stavki,
      prosek **588 B po stavci** naspram konvencije od ~150 B. Skraćivanje na
      konvenciju vraća ~20 KB (~6k tokena) sa **svakog** otvaranja. #claude-code
- [ ] `CLAUDE.md` skratiti na ≤12 KB izmeštanjem §4.1, §7.1, §7.3, §10 (lekcije),
      §14 (hub), §15 u `reference/` — ~8–9k tokena po sesiji. Lekcije iz §10
      **nisu** duplirane u `reference/naucene-lekcije.md` (provereno), pa je to
      prenos, ne brisanje. #claude-code
- [ ] `reference/naucene-lekcije.md` (233 KB / 1.483 linije) staje u 2000 linija →
      svako čitanje povlači **ceo fajl ≈ 75k tokena**. Iseći na 4 tematska fajla. #claude-code
- [ ] `migracija/woodmart-sabloni.md` (79 KB) je „OBAVEZNO prvo" za svaki W1
      zadatak ≈ 25k tokena po W1 sesiji → kratak checklist + duboka referenca. #claude-code
- [ ] Posle migracije 25.08: obrisati mapu numeracije iz `CLAUDE.md` §10 kad stare
      reference izađu iz upotrebe. #claude-code

## Beleške / odluke

- **Rotacija je ušla u ritual** — `/zatvori-sesiju` korak 7 (prag 40 KB) i
  `/antasline-sesija` §5. Ne zavisi od toga da li se neko seti.
- **`head -200` umesto `Read`** upisano u `/gde-smo-stali` i `/antasline-sesija` §1;
  master plan se čita `sed -n` opsegom. §2 (N-raspored) je samo **3,3 KB** —
  §1 WORKSTREAM-OVI je 49 KB od 78 KB i **ne treba ga čitati na otvaranju**.
- **Arhive ledgera se NIKAD ne čitaju `Read`-om** (290–620 KB po fajlu) — samo
  `grep -rn`, gde u kontekst ulaze isključivo pogođene linije.
- `.gitattributes` proširen: `dnevnik/arhiva-dnevnik-*.md merge=union`, isto
  kao glavni ledger (append-only sa dve površine).
- Backup originalnog ledgera: scratchpad `DNEVNIK-NAPRETKA.bak.md` + `git HEAD`.
- Ništa nije komitovano ručno — Obsidian Git auto-sync.

## Veze

- Alat: `skripte/rotiraj-dnevnik.py`
- Arhive ledgera: `dnevnik/arhiva-dnevnik-2026-06.md` · `-2026-07.md` · `-2026-08.md`
- Konvencija tokena: [[reference/token-tracking]] · [[CLAUDE]] §9.6
- Numeracija i mapa starih brojeva: [[CLAUDE]] §10
