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

| Posao | Delegat | Zašto baš on |
|---|---|---|
| Masovno čitanje `.md`, klasifikacija, content parity kroz 100+ stranica | **`agy`** (Gemini Flash) | uhodano, jeftin veliki kontekst · [[agy-delegat]] |
| Sažimanje sirovih API izlaza (GSC upiti, Ads search terms, 100+ redova) | **`ollama`** (lokalno) | besplatno, offline, nula kvote · [[ollama-lokalni]] |
| **Pregled koda** — PHP/CSS/JS child teme, mu-plugins, migracione skripte, diff review, git arheologija | **Copilot** | najjači na kodu, GitHub MCP, odvojena kvota |
| **Drugo mišljenje / velika sinteza** koju Gemini Flash ne izvuče | **Grok** | veliki kontekst, headless JSON, `--effort` |
| Odluke, budžeti, GTM/GA4, baza, WordPress build, `.htaccess`, dan migracije | **Claude Code** | nepovratno |

**Kad se dvoumiš između Copilot i agy:** ima li koda? → Copilot. Sam markdown? → agy.

**Kad se dvoumiš između Grok i agy:** treba li rasuđivanje ili samo češljanje?
Češljanje → agy (jeftinije). Rasuđivanje nad velikim materijalom → Grok.

### Zašto delegat NE donosi odluke

Nijedan delegat nema kontinuitet sesije ni istoriju projekta. Ne zna da „lidovi
pre BLOK A ne važe", da je „epoksid conquest a ne smeće", da je Yoast van
upotrebe od 13.08. Odlično češlja, ali ne zna šta je već odlučeno i zašto.

---

## 2. Kvota — proveriti pre oslanjanja

Stanje na 14.08.2026: Grok ulogovan (`auth.json` upisan 00:56), tier neproveren
— log od 13.08 (pre logovanja, anonimno) prijavljuje `tier: "Free"`.
Copilot ulogovan kao `Chichabudhha`, plan neproveren.

```powershell
# Grok — jedan minimalan poziv, `usage` i `total_cost_usd` su u JSON izlazu
& "C:\Users\Miroslav\.grok\bin\grok.exe" -p "Odgovori samo: OK" --output-format json --no-auto-update

# Copilot — u interaktivnoj sesiji
copilot        # pa ukucati /usage
```

| Nalaz | Posledica za router |
|---|---|
| Grok Free | Grok = „drugo mišljenje po potrebi", ne bulk čitač; bulk ostaje na `agy` |
| Grok plaćen | Grok preuzima velike sinteze |
| Copilot Free (~50 premium/mesec) | samo kratki ciljani pregledi koda; koristiti `-MaxCredits` |
| Copilot Pro/Pro+/Business | Copilot = glavni delegat za kod i git arheologiju |

**Upisati nalaz ovde kad se proveri.** Dok je neproveren, ne planirati veliki
posao ni na jednom od njih.

### 🔴 Grok se naplaćuje po tokenu — i ima skup start

Izmereno na trivijalnom pozivu (14.08.2026): **~23.000 ulaznih tokena, $0,060**.
Uzrok nije prompt nego to što grok na svakom pozivu učita `CLAUDE.md`
(~9.200 tokena) + `AGENTS.md` (~900) + skill definicije.

**Svaki grok poziv, ma koliko sitan, košta oko 6 centi.** Zato:
- nikad grok za posao koji `agy` ili `ollama` mogu — razlika je red veličine;
- nikad grok „da se proba" — svaka proba je 6 centi;
- ako ipak treba više koraka, koristiti `--resume <sessionId>` (skripta ga
  ispiše) umesto novog poziva — keš tada pokrije najveći deo.

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
- Copilot: **`$wpdb->prefix` kaskada** — lokalni `wp-config.php` nosi `wpGs_`
  (CLAUDE.md §2), pa svaki `{$wpdb->prefix}yoast_indexable` upit na Linux-u
  gađa nepostojeću tabelu. Jedan fajl to već rešava sa
  `strtolower($wpdb->prefix)` (`job-5438-...php:257`), ostali ne. Trebalo bi
  prebrojati koliko ih je i odlučiti da li se popravlja kod ili `wp-config.php`.
