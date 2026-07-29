---
tags: [reference, workflow]
---

# Token usage tracking (Claude Code sesije)

Uveden 2026-07-29, na Miroslavov zahtev. Cilj: pratiti potrošnju tokena
tokom rada na AntasLine projektu bez zatrpavanja konteksta, i dobiti signal
kad je vreme za `/clear`.

## Gde

- Log fajl: `Token Logs/.token_log.jsonl` (vault root, JSONL, append-only)
- Ne čita se tokom rada osim ako Miroslav eksplicitno ne traži ("Pokaži mi
  token log", "koje akcije su najviše potrošile")

## Izvor brojeva — VAŽNO

Claude Code ne izlaže token usage kroz nijedan alat u realnom vremenu.
Jedini pouzdan izvor stvarnih (ne izmišljenih) brojeva je transcript JSONL
koji Claude Code sam piše po sesiji:
`C:\Users\<user>\.claude\projects\<project-slug>\<session-id>.jsonl`

Svaki `"type":"assistant"` red ima `message.usage` sa `input_tokens`,
`cache_creation_input_tokens`, `cache_read_input_tokens`, `output_tokens`.

Formula za log:
- `effective_input = input_tokens + cache_creation_input_tokens + cache_read_input_tokens`
  (ovo je stvarna veličina konteksta poslatog modelu tog poziva)
- `output_tokens` = kao što piše
- `total_session_tokens` = `effective_input + output_tokens` iz **poslednjeg**
  zabeleženog reda u transkriptu (ne kumulativni zbir — cache_read već
  odražava rastući kontekst, sabiranje delta-i bi duplo brojalo)
- Prikazana "+Xk tokens" na konzoli = razlika `total_session_tokens` u
  odnosu na prethodni log unos

**Granularnost:** usage podaci postoje samo PO ZAVRŠENOM potezu (turn), ne
po pojedinačnom tool pozivu unutar poteza — transkript se ažurira tek kad
Claude završi odgovor. Zato se logovanje radi jednom po logičkoj akciji/
potezu (npr. "GTM setup analiza"), ne posle svakog alata u nizu.

## Format loga

```json
{"timestamp": "2026-07-29T20:35:00", "action": "GTM setup", "input_tokens": 45000, "output_tokens": 2300, "total_session_tokens": 67300}
```

## Konzola (posle svake logičke akcije)

```
✓ GTM setup | +2.3k tokens | Session: 67.3k
```

Ako `total_session_tokens` > 150k: dodati sugestiju "Token usage je 150k+.
Clear bi mogao biti dobar za novu akciju."

## Ograničenje koje Miroslav treba da zna

Ovo je best-effort tracking iz transkripta, ne zvanični billing API. Tačno
je (nije procena), ali prvi log unos u svakoj sesiji nema prethodni baseline
u toj sesiji, pa "+X" za prvu akciju odražava sve što je učitano do tog
trenutka (CLAUDE.md, memory, itd.), ne samo tu jednu akciju.
