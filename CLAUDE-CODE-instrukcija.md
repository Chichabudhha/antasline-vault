# Instrukcija za CLAUDE.md (u htdocs)

> [!warning] DOPIŠI NA KRAJ — NE BRISATI
> Blok ispod se **dodaje na kraj** postojećeg `C:\xampp\htdocs\antasline\CLAUDE.md`. Sav stari sadržaj MORA ostati. Prvo napravi backup (`copy CLAUDE.md CLAUDE.md.bak`), pa append (`>>`), nikad overwrite (`>`).

Ovaj fajl NIJE deo vault-a po sadržaju — to je blok teksta koji se dopisuje u postojeći `CLAUDE.md` (koji Claude Code auto-učitava). Time povezuješ disk-stranu sa vault-om: Claude Code radi po kodu u htdocs, ali loguje u vault.

---

DOPIŠI BLOK ISPOD NA KRAJ `C:\xampp\htdocs\antasline\CLAUDE.md` (postojeći sadržaj ostaje iznad):

```markdown
## Obsidian vault — mozak projekta
Istorija, odluke, dnevnik i progress NISU više u ovom folderu — preseljeni su u
zasebni Obsidian vault: `C:\Projekti\antasline-vault\`
(U htdocs ostaju samo `.bak` kopije kao mreža — ne koristi ih za rad.)

KANONSKI FAJLOVI (u vault root-u):
- `PROGRESS.md`         = trenutno stanje (prepisuje se svaki put)
- `DNEVNIK-NAPRETKA.md` = hronološki ledger (samo se DODAJE na kraj)

PRAVILA:
1. PRE rada pročitaj:
   - `C:\Projekti\antasline-vault\PROGRESS.md` (gde smo)
   - `C:\Projekti\antasline-vault\blokovi\BLOK-C-sledece.md` (prioritet)
2. POSLE rada:
   a) DODAJ (append, ne overwrite) red na kraj `DNEVNIK-NAPRETKA.md`:
      `## YYYY-MM-DD [claude-code] — kratak opis`
      pa 1–3 bullet-a šta je urađeno + otvorene akcije sa
      `#claude-code` ili `#ceka-miroslav`.
   b) PREPIŠI `PROGRESS.md` da odražava novo stanje.
   c) (Opciono, za velike sesije) napiši detaljan fajl u `dnevnik\`
      imena `YYYY-MM-DD-naslov.md` i linkuj ga iz ledger reda: `[[naslov]]`.
3. Koristi Write/Edit alat (bash heredoc puca na ~965 B).
4. NE diraj `.obsidian\` folder.
5. Wikilink `[[ime]]` i callout `> [!note]` su dozvoljeni.
```

## ADS workflow (kada radiš na Google Ads)

PRE: pročitaj `C:\Projekti\antasline-vault\dnevnik\ADS-DNEVNIK.md` (stanje + fazni plan).

POSLE ADS rada:
1. **ADS-DNEVNIK.md `## 🗒️ Log`** — dodaj unos na vrh (format: `### YYYY-MM-DD [izvor]`)
   - Ažuriraj `## 🚦 Trenutno stanje` tabelu ako se promenilo
   - Štikliraj završene checkboxe u faznom planu
2. **DNEVNIK-NAPRETKA.md** — dodaj kratak red `## YYYY-MM-DD [...] [ADS] — opis` s linkom na ADS-DNEVNIK
3. **PROGRESS.md `## ADS`** — prepiši sekciju da odražava novo stanje
