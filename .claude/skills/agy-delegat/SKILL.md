---
name: agy-delegat
description: Delegiranje masovnog čitanja/izvlačenja na Antigravity CLI (`agy`, Gemini) da se štedi Claude kontekst i Google kvota. Koristi kad zadatak znači "pročitaj mnogo fajlova i izvuci strukturiran spisak" (audit vault-a, pre-flight checklist, klasifikacija stotina GSC upita, content parity kroz 100+ stranica), ili kad Miroslav kaže "agy", "antigravity", "gemini", "delegiraj", "pusti na Gemini", "štedi tokene". NE koristi za odluke, Ads/GTM izmene, bazu ni bilo šta nepovratno.
---

# `agy` (Antigravity CLI) — delegat za masovno čitanje

Miroslav ima Antigravity CLI sa **malom besplatnom kvotom**. Vrednost nije u
tome što je Gemini pametniji — nego što proguta 250k tokena sirovog teksta
za deo cene, dok Claude kontekst ostaje slobodan za odluke.

**Putanja:** `C:\Users\Miroslav\AppData\Local\agy\bin\agy.exe`
**Podešavanja:** `C:\Users\Miroslav\.gemini\antigravity-cli\settings.json`
**Logovi (za dijagnozu odbijenih dozvola):** `C:\Users\Miroslav\.gemini\antigravity-cli\log\`

---

## 1. Kada delegirati — i kada ne

**✅ Delegiraj** (masovno + plitko + posle proverivo):

| Tip posla | Primer |
|---|---|
| Češljanje mnogo fajlova | pre-flight checklist iz 87 dnevnika (~1 MB) |
| Klasifikacija po fiksnom kriterijumu | stotine GSC upita → ecotile/sport/esd/epoksid/smeće |
| Traženje protivrečnosti | koji fajlovi tvrde različito o istoj stvari |
| Mehanička verifikacija | title/meta/H1/broj reči kroz 100+ stranica (content parity, F faze) |
| Sažimanje sirovih API izlaza | hiljade redova → 30 agregiranih |

**❌ Ne delegiraj** — ostaje kod Claude-a:
- Odluke o budžetu, licitiranju, strategiji
- GTM tagovi/trigeri, GA4 key eventi
- Izmene baze, WordPress builda, `.htaccess`
- Sam dan migracije
- Bilo šta nepovratno

**Razlog:** `agy` nema kontinuitet sesije ni istorijski kontekst iz
`CLAUDE.md` (npr. „lidovi pre BLOK A ne važe", „epoksid je conquest, ne
smeće"). Odlično češlja, ali ne zna šta je već odlučeno i zašto.

---

## 2. Modeli — tiering

Prebaciti se može u TUI (`/model` ili meni Switch Model) ili flag-om `--model`.

| Model | ID za `--model` | Kada |
|---|---|---|
| **Gemini 3.6 Flash** | `gemini-3.6-flash-low` / `-medium` / `-high` | **~95% posla.** Čitanje, izvlačenje, klasifikacija, poređenje |
| Gemini 3.1 Pro | `gemini-3.1-pro-low` / `-high` | samo stvarno rasuđivanje — čuvati kvotu |
| GPT-OSS 120B | `gpt-oss-120b-medium` | rezerva; slabiji na srpskom |
| Claude Sonnet/Opus 4.6 | — | 🔴 **NIKAD.** Opus već imamo u Claude Code-u; trošiti oskudnu Google kvotu na Claude je čist gubitak |

⚠️ `settings.json` drži podrazumevani model **`Gemini 3.1 Pro (Low)`** — uvek
eksplicitno proslediti `--model` ili proveriti u TUI da nije ostao na Pro.

---

## 3. Pravila štednje kvote (naučeno na svojoj koži)

1. **Uvek `-p` (print mode), nikad interaktivni razgovor.** Svaka poruka u
   razgovoru ponovo plaća ceo kontekst — 10 poruka = 10× isti trošak. Jedan
   veliki, potpun prompt je višestruko jeftiniji.
2. **Uvek pune apsolutne putanje u promptu.** Bez toga `agy` krene da
   pretražuje `C:\Users\Miroslav` rekurzivno tražeći folder — potvrđeno u
   logu 2026-08-12, čisto ćorkanje kvote.
3. **Izričito zabraniti pretragu van zadatih foldera.**
4. **Fiksirati format izlaza** u promptu (i/ili `--json-schema`) — loš format
   znači ponovljen poziv, tj. dvostruko plaćeno.
5. **Bez naknadnih pitanja.** Sve što treba mora biti u prvom promptu.
6. Meriti: `--print-timeout 8m` za velike poslove (default je 5m).

---

## 4. Dozvole — glavni gotcha

Headless (`-p`) **ne može da pita za dozvolu**, pa sam sebi odbije alat i
vrati `no output produced`. Log tada pokaže:
`permission check failed for command "..."` / `required the "read_file" permission`.

**Sintaksa je `permissions.allow` u `settings.json`** (potvrđeno da radi za
`command(...)` 2026-08-12):

```json
"permissions": {
  "allow": [
    "read_file(*)", "list_dir(*)", "grep_search(*)", "find_by_name(*)",
    "command(Get-ChildItem)", "command(Get-Content)", "command(Select-String)",
    "command(rg)", "command(grep)", "command(cat)", "command(ls)"
  ]
}
```

Stanje: `command(...)` unosi su **već dodati**. Alati za čitanje
(`read_file` i dr.) **nisu** — Miroslav ih mora dodati sam.

🔴 **`--dangerously-skip-permissions` ne koristiti** — Claude Code harness ga
blokira (i s pravom: „odobri sve" na vault-u pred migraciju). Ako dozvole
nisu podešene, **fallback je TUI**: Miroslav nalepi prompt i klikne odobri.

Uvek dodati `--sandbox --mode plan` (read-only) kad je posao čitanje.

**Radna komanda (verifikovano):**
```powershell
$p = Get-Content "prompt.txt" -Raw
Set-Location "C:\Projekti\antasline-vault"
& "C:\Users\Miroslav\AppData\Local\agy\bin\agy.exe" -p $p `
  --model gemini-3.6-flash-medium --sandbox --mode plan --print-timeout 8m `
  | Out-File "izlaz.md" -Encoding utf8
```

---

## 5. Šablon prompta

Svaki prompt za `agy` mora imati svih 6 delova:

```
1. PUNE APSOLUTNE PUTANJE + broj fajlova
   "Pročitaj SVE .md fajlove u TAČNO ova dva foldera:
      C:\Projekti\antasline-vault\dnevnik\   (50 fajlova)
    NE pretražuj nijedan drugi folder. NE pretražuj C:\Users\Miroslav.
    Idi direktno na njih, bez traženja."

2. CILJ — jedna rečenica, konkretno šta se pravi i zašto

3. ŠTA TRAŽI — nabrojati, uključujući ključne reči
   (gotcha, greška, puklo, propalo, nije uspelo, zaboravljeno, pažnja...)

4. PRAVILA (obavezno sva četiri):
   - Svaka stavka MORA imati izvor: naziv fajla + datum. Bez izvora ne upisuj.
   - Ako su dva fajla u protivrečnosti, upiši OBA i označi kao KONFLIKT.
   - Ne izmišljaj. Ako nešto nije zapisano, ne postoji.
   - Ne menjaj nijedan fajl. Samo čitaj i ispiši rezultat.

5. FORMAT IZLAZA — tačne kolone tabele + dodatne sekcije

6. "Jezik: srpski, ekavica. Bez uvoda i zaključka."
```

Pravilo o izvoru i pravilo „ne izmišljaj" su **neizostavna** — bez njih se
nalazi ne mogu verifikovati, a onda ceo posao ne vredi ništa.

---

## 6. Gotcha-i izlaza

- **Dupli ispis.** TUI ume da ispiše ceo rezultat dvaput (redraw). Izgleda
  kao da je posao stao/pukao — nije. Proveriti da li se obe polovine
  završavaju istom stavkom. Druga kopija ume biti **potpunija** od prve.
- **Tabela se raspadne posle ~10 redova** — ASCII okvir se prelomi u sirov
  markdown sa polepljenim rečima (`pokrenutiauthorize_oauth.py`). Sadržaj je
  tu, formatiranje nije. Zato tražiti **običnu markdown pipe-tabelu**, ne
  „lepu" tabelu, i po mogućstvu ispis direktno u fajl.
- Izlaz uvek prečistiti u pravi `.md` u vault-u; sirov ispis ostaviti kao
  arhivu (npr. `migracija/preflight.txt`).

---

## 7. Obaveza posle svakog `agy` posla

**Nalazi se ne prihvataju na reč — proveravaju se protiv koda.** Tako je
2026-08-12 potvrđen `live-export.sh` bug (komentar na liniji 25 kaže
„thumbnail + galerija", kod nikad ne čita `_product_image_gallery`).

Redosled:
1. Preseći sa postojećim znanjem (`CLAUDE.md`, `PROGRESS`, raniji auditi) —
   izbaciti duplikate i zastarelo.
2. Verifikovati najopasnije tvrdnje direktno u kodu/bazi.
3. Označiti šta je verifikovano, šta je i dalje tvrdnja.
4. Ako nalaz protivreči `CLAUDE.md` — **to je prioritet**, jer svaki agent
   čita `CLAUDE.md` kao autoritet (primer: prefiks baze `wpGs_` vs stvarni
   `wpgs_`).
5. Upisati čist rezultat u vault + dnevnik, tag `[claude-code]` uz napomenu
   da je izvor `agy`.

⚠️ **Ne puštati `agy` interaktivno nad vault-om dok Claude Code radi** —
Obsidian Git sinhronizuje na ~10 min, `PROGRESS.md` nema `merge=union` (za
razliku od `DNEVNIK-NAPRETKA`) pa nastaju konflikti. Ako `agy` ide preko
Claude poziva, redosled je kontrolisan.

---

## 8. Šta je već urađeno preko `agy`

| Datum | Posao | Rezultat |
|---|---|---|
| ~2026-08-12 | Audit celog vault-a | `analiza_vaulta_i_mana.md` — našao inflaciju `generate_lead`, `live-export.sh` gallery bug, OAuth istek na 7 dana, `ekopodneploce.rs` u sitelinkovima, isključene taksonomijske sitemape, `llms.txt` kao izgubljeno vreme |
| 2026-08-12 | Pre-flight checklist za 24/25.08 (87 fajlova) | `migracija/2026-08-12-preflight-checklist-24-08.md` — 19 rizika, 11 ručnih radnji, 6 konflikata |

Oba su našla stvari koje Claude audit sa tri paralelna Explore agenta
(`~/.claude/plans/pro-i-kroz-ceo-vault-sharded-otter.md`, 2026-08-07) nije.
To je i poenta podele: `agy` sistematski češlja, Claude odlučuje i pamti.
