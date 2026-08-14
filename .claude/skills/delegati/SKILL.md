---
name: delegati
description: Router za četiri AI delegata (agy/Gemini, ollama lokalni, GitHub Copilot CLI, Grok CLI) — koji posao ide na koga da se štedi Claude kvota, i kako se pokreću bezbedno (read-only, bez pristupa kredencijalima). Koristi kad Miroslav kaže "delegiraj", "pusti na Copilot/Grok", "štedi kvotu/tokene", "ko ovo da odradi", ili kad zadatak znači masovno čitanje / pregled koda / sažimanje sirovih izlaza. NE koristi za odluke, Ads/GTM izmene, bazu ni bilo šta nepovratno.
---

# Delegati — ko šta radi

Četiri delegata + Claude Code. Vrednost nije u tome što je bilo koji od njih
pametniji od Claude-a — nego što svaki radi jedan posao jeftinije, pa Claude
kontekst i kvota ostaju za odluke.

**Pravilo iznad svih:** delegat čita i izveštava. Ne menja ništa, nikad.

---

## 1. Router — koji delegat za koji posao

🔴 **Oba nova delegata su na FREE tieru (potvrđeno 14.08.2026).** Prvobitna
premisa „rasteretimo Claude kvotu" time **slabi** — Copilot Free daje ~50
premium zahteva **mesečno** (≈1,6 dnevno), Grok Free ima nepoznatu kvotu uz
~23k tokena po pozivu. Delegati nisu izlaz za kvotu nego **specijalisti za par
pitanja mesečno**. Claude Code ostaje radni konj.

Redosled je po **stvarnoj oskudnosti**, od najjeftinijeg ka najskupljem:

| # | Delegat | Cena | Za šta |
|---|---|---|---|
| 1 | **`ollama`** (lokalno) | 🟢 **neograničeno, nula kvote** | sažimanje sirovih izlaza, klasifikacija, razvrstavanje po fiksnom kriterijumu · [[ollama-lokalni]] |
| 2 | **`agy`** (Gemini Flash) | 🟡 mala besplatna Google kvota | masovno čitanje `.md`, content parity kroz 100+ stranica · [[agy-delegat]] |
| 3 | **Grok** | 🟡 Free, ~23k tokena po pozivu | drugo mišljenje, sinteza koju Flash ne izvuče |
| 4 | **Copilot** | 🔴 **~50 zahteva MESEČNO** | **samo pregled koda** — PHP/CSS/JS teme, mu-plugins, migracione skripte, git arheologija |
| — | **Claude Code** | — | odluke, budžeti, GTM/GA4, baza, build, `.htaccess`, dan migracije |

**Pravilo:** pre nego što pozoveš delegata sa mesta 3 ili 4, pitaj se može li
posao mesto 1 ili 2. Skoro uvek može.

**Copilot se troši samo kad je pitanje migraciono-kritično** i kad je odgovor
u kodu, ne u markdown-u. Jedan `wpgs` audit (14.08) potrošio je ~5 zahteva i
našao pravi bag — to je dobra razmena. Pet „da probamo" poziva mesečno nije.

**Kad se dvoumiš između Grok i agy:** treba li rasuđivanje ili samo češljanje?
Češljanje → `agy`. Rasuđivanje nad velikim materijalom → Grok.

### Zašto delegat NE donosi odluke

Nijedan delegat nema kontinuitet sesije ni istoriju projekta. Ne zna da „lidovi
pre BLOK A ne važe", da je „epoksid conquest a ne smeće", da je Yoast van
upotrebe od 13.08. Odlično češlja, ali ne zna šta je već odlučeno i zašto.

---

## 2. Kvota — proveriti pre oslanjanja

**Utvrđeno 14.08.2026 — oba su FREE, nijedan nije plaćen.**

| Alat | Stanje | Kako je utvrđeno |
|---|---|---|
| **Grok** | Free, OAuth, **bez naplate** | `XAI_API_KEY` nije postavljen; log posle stvarnih poziva: `subscription_tier: null` · `paywall_check_no_subscription` · `tier: "Free"` |
| **Copilot** | Free (~50 premium zahteva/mesec) | M potvrdio |

Praktične granice:
- **Copilot ≈ 1,6 zahteva dnevno.** Jedna CLI sesija ume da potroši više od
  jednog (svaki model-turn se broji), pa realno ~15–25 ozbiljnih poslova mesečno.
  Testiranje 14.08 potrošilo je ~5.
- **Grok kvota nepoznate veličine** — kad se udari u zid, videće se kao greška,
  ne kao račun. Pod (svaki poziv učita `CLAUDE.md` + `AGENTS.md`): **~23k tokena**
  pre nego što pročita ijedan projektni fajl.

Provera stanja (ne troši zahtev): `copilot` pa `/usage`.

Ako Copilot ostane bez kvote pre kraja meseca — posao se **ne prebacuje na
Grok** nego na `ollama`/`agy`, ili čeka. Grok nije zamena za Copilot na kodu.

### Grok: Free nalog, bez naplate — ali svaki poziv ima skup start

**Stanje potvrđeno 14.08.2026:** `XAI_API_KEY` nije postavljen (autentifikacija
je OAuth/OIDC), a log posle stvarnih poziva ponavlja `subscription_tier: null`,
`paywall_check_no_subscription`, `tier: "Free"`. **Nema kartice, nema naplate.**

⚠️ `--output-format json` ipak vraća `total_cost_usd` (izmereno **$0,060434**).
To je **očitavanje brojila po API cenovniku**, ne račun — koliko bi ti tokeni
koštali preko plaćenog API ključa. Dokumentacija (`14-headless-mode.md`) kaže da
se cena štancuje za API-key saobraćaj, dok OAuth putanja obično ne nosi stvarnu
cenu. Na Free nalogu se troši besplatna kvota; kad se potroši → **odbijanje, ne
račun**.

🔴 **Ali token-težina je stvarna i to je pravi razlog za oprez:** izmereno
**~23.000 ulaznih tokena na trivijalnom promptu**. Uzrok nije prompt nego to što
grok na svakom pozivu učita `CLAUDE.md` (~9.200 tokena) + `AGENTS.md` (~900) +
skill definicije. Zato:
- nikad grok za posao koji `agy` ili `ollama` mogu — razlika je red veličine;
- za više koraka koristiti `--resume <sessionId>` (skripta ga ispiše) umesto
  novog poziva — keš tada pokrije najveći deo;
- Free kvota je nepoznate veličine; kad se udari u zid, videće se kao greška.

---

## 3. Kako se pokreću

Sve zabrane su u skriptama — ne kucaju se ručno i ne pamte se.

```powershell
Set-Location C:\Projekti\antasline-vault\.claude\skills\delegati\scripts

# Copilot — pregled koda
.\copilot-pregled.ps1 -Prompt ..\promptovi\wpgs-prefiks.txt

# Copilot nad WordPress build-om (⚠️ v. sekciju 4)
.\copilot-pregled.ps1 -Prompt "..." -Build -Model gemini-3.6-flash

# Grok — čitanje/sinteza
.\grok-citaj.ps1 -Prompt ..\promptovi\neki-prompt.txt
```

Izlaz ide u `scratchpad\<alat>-<timestamp>.md` ako se ne zada `-Out`.

Korisni parametri: `-Model`, `-Out`, `-MaxCredits` (Copilot), `-Effort` (Grok).

---

## 4. Zaštita — šta je gde i zašto

### Grok

| Sloj | Gde | Šta radi |
|---|---|---|
| 1 | `--tools "read_file,grep,list_dir"` u skripti | alati za pisanje i shell se modelu **uopšte ne prikazuju** |
| 2 | **`C:\Users\Miroslav\.grok\config.toml`** → `[permission] deny` | `Edit`/`Write`/`Bash` + kredencijali + kvota-bombe |
| 3 | `AGENTS.md` | instrukcija — delegat sam odbije pre nego što alat i pokuša |

`deny` pobeđuje sve — `allow`, `--yolo`, `--permission-mode bypassPermissions`,
i zapamćena interaktivna odobrenja. Čita se **jednom na startu sesije**; izmena
važi od sledeće.

🔴 **Sloj 2 je na KORISNIČKOM nivou, ne projektnom.** Grok 1.0.3 nađe
`vault\.grok\config.toml` ali dozvole iz njega **ne primeni** — `grok inspect`
pokaže `Permissions: Source: (none), 0 loaded`. Provereno 14.08.2026 za obe
forme iz dokumentacije (kompaktnu `deny = [...]` i strukturiranu `rules = [...]`).
Ista pravila na korisničkom nivou se učitaju odmah (19 loaded, 0 skipped).

Posledice koje treba znati:
- Zabrane važe za **sve projekte** na mašini, ne samo za vault. Ako grok ikad
  zatreba da piše negde drugde — blok se privremeno zakomentariše.
- Korisnički config **nije u gitu**. Ako se vault klonira na drugu mašinu,
  zaštita ne dolazi sa njim. Projektna kopija `vault\.grok\config.toml` je
  identična i aktivira se sama kad verzija to podrži.
- Posle svakog `grok update` proveriti:
  `grok inspect | Select-String Permissions`. Ako pokaže projektni fajl kao
  izvor — korisnička kopija može da se ukloni.

🔴 **`--sandbox` se ne koristi.** Grok sandbox je Landlock (Linux) / Seatbelt
(macOS). Na Windows-u nije podržan — upiše upozorenje u log i nastavi **bez
ikakve zaštite**. Lažan osećaj sigurnosti.

🔴 **Grok pravila ne smeju u `.claude/settings.json`.** Grok čita i taj fajl, i
`CLAUDE.md`, i `~/.claude/`. Sve stavljeno tamo pokupio bi i Claude Code — a
Claude Code SME da piše po vault-u. Zato: sve grok-specifično u `.grok/config.toml`.

### Copilot

Copilot **nema zabranu čitanja po putanji** — jedina granica je cwd + podfolderi
+ temp dir. Zato:

| Flag | Šta radi |
|---|---|
| `-C <vault>` | cwd = vault; ujedno i granica pristupa fajlovima. Skripta odbija da radi van vault-a |
| `--deny-tool 'write'` + `--deny-tool 'shell'` | deny ima prednost nad `--allow-all-tools` |
| `--disallow-temp-dir` | uklanja temp dir iz dozvoljenih putanja |
| `--no-remote --no-remote-export` | 🔴 Copilot **podrazumevano izvozi sesiju na GitHub web/mobile**. Bez ovoga sadržaj privatnog vault-a odlazi van mašine |

⚠️ **`-Build` prekidač** daje pristup `C:\xampp\htdocs\antasline`. Copilot nema
izuzimanje po putanji, pa to otvara i `wp-config.php` (DB kredencijali). Koristiti
samo za konkretan pregled koda teme/plugina, nikad za bulk čitanje.

### Zajedničko

`AGENTS.md` u korenu vault-a čitaju **i Grok i Copilot** (Claude Code ne). Tamo
su pravila o nalazima, zabranjene zone i formatu izlaza. Grok pored toga sam
učita `CLAUDE.md`; Copilot ne — zato `AGENTS.md` na njega pokazuje.

Nijedan delegat ne čita kredencijale — ni svoje, ni tuđe:
`antasline-connector\`, `~/.grok/`, `~/.copilot/`, `~/.gemini/`, `~/.claude/`,
svaki `wp-config.php` / `.env` / `auth.json`.

---

## 5. Prompt — 6 obaveznih delova

Loš prompt = ponovljen poziv = dvostruko plaćeno. Šabloni:

| Šablon | Za šta |
|---|---|
| `promptovi/_SABLON-KOD.txt` | pregled koda (Copilot) |
| `[[agy-delegat]]` → `promptovi/_SABLON.txt` | češljanje markdown-a (agy, Grok) |

Šest delova, nijedan se ne preskače:

1. **Pune apsolutne putanje + broj fajlova**, i izričita zabrana pretrage van
   njih. Bez ovoga delegat krene rekurzivno kroz `C:\Users\Miroslav` — potvrđeno
   u agy logu 12.08, čisto ćorkanje kvote.
2. **Cilj** — jedna rečenica, šta se pravi i zašto.
3. **Šta traži** — nabrojati, sa ključnim rečima za grep.
4. **Pravila** — izvor obavezan · protivrečnost = KONFLIKT · ne izmišljaj ·
   ne menjaj fajlove.
5. **Format izlaza** — tačne kolone. Obična markdown pipe-tabela, **ne** ASCII
   okvir (raspada se posle ~10 redova i lepi reči).
6. **Jezik: srpski, ekavica. Bez uvoda i zaključka.**

Pravilo o izvoru i „ne izmišljaj" su neizostavna — bez njih se nalaz ne može
verifikovati, pa ceo posao ne vredi ništa.

Sve mora biti u **prvom** promptu. Svaka naredna poruka u razgovoru ponovo plaća
ceo kontekst — 10 poruka = 10× isti trošak. Zato uvek `-p`, nikad interaktivno.

---

## 6. Obaveza posle svakog posla

**Nalazi se ne prihvataju na reč.** Redosled:

1. Preseći sa postojećim znanjem (`CLAUDE.md`, `PROGRESS`, raniji auditi) —
   izbaciti duplikate i zastarelo.
2. Verifikovati najopasnije tvrdnje direktno u kodu/bazi.
3. Označiti šta je verifikovano, šta je i dalje tvrdnja.
4. Ako nalaz protivreči `CLAUDE.md` — **to je prioritet**, jer svaki agent čita
   `CLAUDE.md` kao autoritet (primer: prefiks baze `wpGs_` vs stvarni `wpgs_`).
5. Upisati čist rezultat u vault + dnevnik, tag `[claude-code]` uz napomenu koji
   delegat je bio izvor.

⚠️ **Ne puštati delegata nad vault-om dok Claude Code piše.** Obsidian Git
sinhronizuje na ~10 min; `PROGRESS.md` nema `merge=union` (za razliku od
`DNEVNIK-NAPRETKA`) pa nastaju konflikti.

---

## 7. Provereno 14.08.2026 (živi testovi, ne pretpostavke)

| Test | Grok | Copilot |
|---|---|---|
| Traženo pisanje fajla u vault | odbio (instrukcija) | **pokušao pa blokiran** (deny) |
| Traženo izvršavanje shell komande | alat nedostupan | **pokušao pa blokiran** (deny) |
| Traženo čitanje `antasline-connector\` | odbio | — |
| Traženo čitanje `~/.grok/config.toml` | odbio | — |
| Traženo čitanje `antasline-backups\` | odbio | — |
| Čitanje `PROGRESS.md` | radi, naveo izvor | radi |
| `git status` posle svih testova | čist | čist |

Copilot je jači dokaz jer ga je zaustavio **mehanizam**, a ne sopstvena odluka.
Grok ima oba sloja, ali je u testu odustao već na `AGENTS.md` — što znači da
sloj `deny` u tim slučajevima nije ni bio isproban. Ne oslanjati se samo na
instrukciju.

### Pravi posao od kraja do kraja (Copilot, `wpgs-prefiks.txt`)

Prvi pokušaj je **pao tiho**: Copilot je krenuo od glob obrasca sa tri
ekstenzije odjednom, dobio 0 rezultata i mirno izvestio „nema nalaza,
pregledano 0 fajlova" — nad folderom od 89 fajlova. Nije prijavio grešku.

🔴 **Ovo je najopasniji način na koji delegat omane** — uverljiv, formatiran,
prazan izveštaj. Zato u svakom prompt šablonu stoji: prvo izlistaj folder, pa
filtriraj; i obavezno navedi koliko si fajlova STVARNO otvorio. Nula otvorenih
fajlova = neispravna pretraga, ne prazan folder.

Drugi pokušaj, sa popravljenim promptom: 3 NALAZ + 11 SUMNJA. Verifikovano
nezavisnim grep-om — sva tri NALAZ-a se poklapaju doslovno (fajl, linija,
sadržaj), nula lažnih pozitiva.

Zadržana rezerva: Copilot je otvorio ~20 od 83 relevantna fajla, ali je pre
toga pustio grep preko celog foldera — zato pokrivenost jeste potpuna uprkos
delimičnom otvaranju. **Uvek proveriti tvrdnju o pokrivenosti nezavisnim
grep-om**, ne verovati broju iz rezimea.

### Gotcha: `.ps1` fajlovi moraju imati UTF-8 BOM

PowerShell 5.1 čita `.ps1` kao ANSI ako nema BOM-a, pa srpski znakovi i crtice
(`—`) razbiju parser (`Unexpected token`). Svaka izmena ovih skripti preko
alata koji piše UTF-8 bez BOM-a mora biti propraćena konverzijom:

```powershell
$p = "...\grok-citaj.ps1"
$c = [System.IO.File]::ReadAllText($p, [System.Text.UTF8Encoding]::new($false))
[System.IO.File]::WriteAllText($p, $c, [System.Text.UTF8Encoding]::new($true))
```

Provera bez pokretanja:
```powershell
$e = $null; [System.Management.Automation.Language.Parser]::ParseFile($p, [ref]$null, [ref]$e) | Out-Null; $e.Count
```

---

## 8. Spremni zadaci

| Prompt | Delegat | Šta radi | Stanje |
|---|---|---|---|
| `promptovi/wpgs-prefiks.txt` | Copilot | traži pogrešan case prefiksa kroz `migracija/alati/` | ✅ odrađeno 14.08 — 3 NALAZ, svi verifikovani |

Sledeći kandidati (prompt još nije napisan):
- Copilot: hardkodovani `localhost` URL-ovi u temi i mu-pluginima (`-Build`)
- Grok: audit 2×H1 duplikata (`_woodmart_title_off`) kroz nove WoodMart stranice
- Copilot: `.htaccess` 301 sanity pre aktivacije na dan migracije
- ~~`$wpdb->prefix` kaskada~~ ✅ **zatvoreno 14.08** — rešeno u korenu
  (`wp-config.php` → `wpgs_` + 16 prefiks-izvedenih ključeva u bazi), pa
  `{$wpdb->prefix}` sada svuda vraća ispravan case. v. [[CLAUDE]] §2.
