Zatvaramo sesiju. Na osnovu onoga što smo danas uradili:

1. Ako je sesija imala suštinski rad, napravi `dnevnik/YYYY-MM-DD-naslov.md`
   po `[[dnevnik/_TEMPLATE-sesija]]` formatu. **Ovo je jedino mesto gde ide pun
   tekst** — opis, gotcha-i, komande, backup fajlovi, brojevi.
2. Dodaj unos u [[DNEVNIK-NAPRETKA]] NA VRH (`## YYYY-MM-DD [claude-code]
   [OBLAST] — naslov ✅`) — **2–3 rečenice + wikilink na dnevnik fajl iz koraka 1**.
   Ne prepisivati opis iz dnevnik fajla.
3. Ažuriraj [[PROGRESS]] — **prepiši, ne dodavaj**:
   - „Urađeno" = **jedna linija po stavci**, format
     `- ✅ \`[tag]\` Naslov — [[dnevnik/YYYY-MM-DD-naslov]]`. Bez pasusa, bez
     brojeva, bez objašnjenja — sve to već stoji u dnevnik fajlu.
     **Drži samo tekuću nedelju**; stariji dani idu u `dnevnik/YYYY-MM-arhiva-progress.md`.
   - „Urađeno" **ostaje lista, nikad tabela** — Obsidian Advanced Tables poravnava
     ćelije razmacima i naduva fajl ×5 (13.08.2026: 388 KB → 1,4 MB, od čega 1 MB
     samih razmaka).
   - „Blokeri" = **samo otvorene stavke**. Čim se bloker zatvori, premesti ga
     ISTOG DANA u `dnevnik/YYYY-MM-arhiva-progress.md` (sekcija „Zatvoreni blokeri"),
     ne ostavljaj ga precrtanog u PROGRESS-u.
   - Ciljna veličina celog fajla: **do ~50 KB**. Ako pređe, arhiviraj pre nego što
     nastaviš — CLAUDE.md §12 traži da se PROGRESS čita prvi na svakoj sesiji.
4. Štikliraj zadatak u [[2026-07-06-MASTER-PLAN-V2]]
5. Nova naučena lekcija → [[reference/naucene-lekcije]]
6. Otvorena pitanja → #ceka-miroslav + jasno reci šta se čeka

**Podela posla (da se isti tekst ne piše tri puta):**

| Fajl | Uloga | Obim po stavci |
|---|---|---|
| `dnevnik/YYYY-MM-DD-*.md` | pun tekst, gotcha-i, komande | koliko treba |
| `DNEVNIK-NAPRETKA` | hronologija — 2–3 rečenice + link | ~300 B |
| `PROGRESS` | snapshot — jedna linija + link | ~150 B |

Arhive: [[dnevnik/2026-07-arhiva-progress]] · [[dnevnik/2026-08-arhiva-progress]]
