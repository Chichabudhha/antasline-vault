# Instrukcija za CLAUDE.md NA cPANEL-u (produkcija)

> DOPIŠI blok ispod na kraj CLAUDE.md u radnom folderu cPanel Claude Code-a
> (najčešće `~/public_html/CLAUDE.md`). NE brisati postojeći sadržaj.
> Preduslov: vault je kloniran u `~/antasline-vault` (vidi setup u chatu).

---

```markdown
## Obsidian vault (preko git-a) — ⚠️ PRODUKCIJA UŽIVO
Ovo je ŽIVI server (antasline.com), NE staging. Mozak projekta je git repo
kloniran u: ~/antasline-vault  (isti repo kao lokalni vault, sinhron preko remote-a).

PRE SVAKOG RADA:
  cd ~/antasline-vault && git pull --no-edit
  (pročitaj PROGRESS.md i blokovi/BLOK-C-sledece.md)

PRE BILO KOJE IZMENE NA SAJTU:
  Obavezan backup — `wp db export` i/ili kopija fajla. Ovo je produkcija.

POSLE RADA (obavezno, ovim redom):
  1. cd ~/antasline-vault && git pull --no-edit
  2. DODAJ (append) na kraj DNEVNIK-NAPRETKA.md:
       ## YYYY-MM-DD [cpanel-live] — kratak opis (UŽIVO)
       - šta je TAČNO promenjeno na produkciji
       - otvorene akcije: #ceka-miroslav ili #claude-code
  3. Ako se stanje promenilo: prepiši PROGRESS.md
  4. git add -A && git commit -m "cpanel-live: opis" && git push

PRAVILA:
- Svaki [cpanel-live] unos MORA eksplicitno reći da je dirao živu produkciju.
- Nikad ne brisati tuđe ledger unose — samo dodavati.
- Koristi Write/Edit alat za pisanje (bash heredoc puca na ~965 B).
- NE diraj .obsidian/ ni .git/ ručno.
```
