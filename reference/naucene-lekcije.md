---
tip: reference
azurirano: 2026-08-20
---

# Naučene lekcije (tehnički gotchas) — indeks

> 🆕 **2026-08-20 (vault higijena):** ovaj fajl je bio 251 KB / 1.758 linija —
> preko `Read` limita od 2000 linija, ~75k tokena u jednom pozivu. Sadržaj je
> doslovno prenet (0 izgubljenih unosa, potvrđeno pokrivenošću linija) u 4
> tematska fajla ispod, sortirano po istom redosledu kao original (najnovije
> na vrhu unutar svake teme). Ovaj fajl ostaje kao indeks — ne čitati ga kao
> zamenu za sadržaj, otvoriti direktno relevantan tematski fajl.

## Gde tražiti

| Fajl | Tema | Entries |
|---|---|---|
| [[reference/lekcije-wp-db-tehnika]] | WordPress core/DB/WP-CLI, WooCommerce, WoodMart/WPBakery theme dev, Windows/PowerShell/Bash tooling, backup/infra | 103 |
| [[reference/lekcije-seo-sadrzaj-migracija]] | SEO (Rank Math/Yoast), schema, redirect/301/.htaccess, GSC, content parity, migracija sadržaja | 55 |
| [[reference/lekcije-ads-tracking]] | Google Ads API, GA4, GTM, konektor (Windsor/sopstveni), Customer Match, publike | 19 |
| [[reference/lekcije-alati-vault-delegati]] | Vault/ledger workflow, delegat-agenti (ollama/agy/grok/copilot), browser automation, AI foto/video tooling, opšti proces | 46 |

**Kako tražiti konkretnu lekciju:** `grep -rn "pojam" --include="*.md" reference/lekcije-*.md`
— u kontekst ulaze samo pogođene linije, ne ceo fajl. Ako ne znaš temu, grepuj
sva četiri odjednom (isti obrazac kao `grep -rn "pojam" .` za ceo vault).

**Nova lekcija ide u tematski fajl koji odgovara oblasti** (ne ovde). Ako je
nejasno u koju kategoriju spada — pogledaj najbliži postojeći unos u svakom
fajlu kao presedan pre nego što otvoriš peti fajl.
