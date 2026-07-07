---
tip: arhiva
status: superseded
datum: 2026-07-07
---

# ⛔ ARHIVA — stare redirect mape (superseded 2026-07-07)

Ove tri CSV mape **više nisu izvor istine**. Zamenjene su parity pristupom:
**[[migracija/PARITY-PLAN]]** + `migracija/parity-inventar.csv` (generiše se u fazi F1).

| Fajl | Šta je bio | Zašto je odbačen |
|---|---|---|
| `antasline-redirect-mapa-POPUNJENA.csv` (118 redova) | mapa za staro Porto restrukturiranje | cilja `/shop/%product_cat%/` proizvode i `/kategorija/` bazu — kontradiktorno i sa live (`/proizvod/` flat, `/kategorija-proizvoda/`) i sa novom parity strategijom; AUTO-PREDLOG redovi bespredmetni |
| `antasline-redirect-mapa-ZA-POPUNITI.csv` | radna verzija iste mape | isto |
| `antasline-redirect-mapa-2026-07-07.csv` (41 red) | prva parity provera live vs lokal | sadrži grešku: `podovi-za-parkiraliste-i-staze` (live) vs `podloge-za-...` (lokal) označeno PARITY iako se slugovi razlikuju; zamenjuje je puni F1 inventar |

**Vredne beleške iz POPUNJENA.csv koje NE treba izgubiti** (prenete u PARITY-PLAN/F4-F5 promptove):
- `/galerija/` — nedostaju slike, Porto placeholder umesto njih (uporedi i zameni)
- `/sportske-podloge/kosarkaske-konstrukcije/` (478 GSC kl.) — treba PRAVA landing, ne 301 na tanku shop kategoriju
- bergo-easy/elite/unique/xl → bergo-ultimate konsolidacija
- dva "izbor-industrijskog-poda-tri-najcesca-pitanja" članka → konsolidovati u jedan
- `/antistatik-i-elektroprovodljivi-podovi/` (250 kl.) — novi build mora isti obim teksta + "antistatik/ESD" u title/H1
- `/aktuelnosti/` ostaje (ne menja se u /blog/) — Miroslavljeva beleška u CSV-u

Originali u `C:\xampp\htdocs\antasline\` i `antasline-backups\` brišu se tek posle Miroslavljeve potvrde (F4 sesija).
