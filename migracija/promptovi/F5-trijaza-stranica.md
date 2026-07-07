---
tip: prompt
faza: F5
naziv: Trijaža nedostajućih stranica → W1 red čekanja
menja-bazu: minimalno (legal/parity placeholder stranice)
preduslov: F1 gotov
---

# F5 — Trijaža nedostajućih live stranica

**Cilj:** svaku live stranicu sa statusom `NEDOSTAJE-LOKAL` iz
`migracija/parity-inventar.csv` svrstati u kategoriju i napraviti
**`migracija/w1-red-cekanja.md`** — prioritetnu listu rebuilda za W1 1.2 sesije,
sortiranu po GSC klikovima. Strategija: [[migracija/PARITY-PLAN]].

## Kategorije trijaže

| Kategorija | Šta se radi | Poznati kandidati (2026-07-07) |
|---|---|---|
| **A — REBUILD** (W1 1.2 lista) | puna WoodMart stranica po [[migracija/woodmart-sabloni]], content parity iz `migracija/live-export-2026-07-05/live-pages-2026-07-05.xml` + live inventar CSV (Yoast mete!) | `kosarkaske-konstrukcije` (478 kl. 🔴), `antistatik-i-elektroprovodljivi-podovi` (250 kl.), `padel-tereni`, `garaze-i-autoservisi`, `iznajmljivanje-podova` (79 kl.), `reflektori-za-sportske-terene` (51 kl.), `podovi-za-magacine-i-hale`, `kancelarije-i-poslovni-prostori`, `podovi-za-bazene`, `vinil-podovi-za-restorane-...`, `sportski-podovi-za-sale-i-balone`*, `podloga-za-odbojkaske-terene`*, … (*zavisno od F4 odluka) |
| **B — SISTEM/WOO** | postoji kroz WP/Woo funkcionalnost, ne treba page | `home`, `katalog` (shop arhiva), `moj-nalog`, `aktuelnosti` (blog arhiva — F2) |
| **C — PROIZVOD-STRANICE** | live page čiji je sadržaj lokalno Woo proizvod → 301 u F4 mapu ili konsolidacija | `bergo-easy`, `bergo-elite`, `bergo-unique`, `bergo-xl`, `ecotile-5005-podne-ploce`, `podne-ploce-ecotile-50010`, `expona-click`, `trake-za-obelezavanje`, `vinil-podovi-objectflor` |
| **D — LEGAL/UTILITY** | kreirati odmah, tanak parity sadržaj | `politika-kolacica` (obavezno pre live-a!), `galerija` (⚠️ proveri šta je na live — u staroj mapi beleška: slike falile i na live, Porto placeholderi) |
| **E — DUPLIKATI** | 301 na glavnu verziju (F4 mapa) | `elektroprovodni-podovi` (=antistatik), `ergonomske-podloge-2`, `industrijski-pod`, `opremazasportsketerene` |

## Koraci

1. Učitaj parity-inventar.csv, filtriraj `NEDOSTAJE-LOKAL`, dodeli kategoriju svakom redu (upiši u `napomena`).
2. Za kategoriju A: sortiraj po `gsc_klikovi` opadajuće → upiši u `migracija/w1-red-cekanja.md` kao checkbox listu sa: live URL, GSC klikovi, izvor sadržaja (pages XML), Yoast title/metadesc iz live inventara, posebne napomene.
3. Za kategoriju C: svaki red dobija predlog cilja (`/proizvod/<slug>/`) → prosledi u F4 mapu.
4. Za kategoriju D: kreiraj `politika-kolacica` stranicu odmah (sadržaj sa live, pages XML) — jedina izmena baze u ovoj fazi (backup ako radiš više od toga).
5. Ažuriraj [[2026-07-06-MASTER-PLAN-V2]] W1 1.2 red da pokazuje na `migracija/w1-red-cekanja.md`.

## Pravila za buduće rebuild sesije (upiši u zaglavlje w1-red-cekanja.md)

- Pre svake stranice pročitati [[migracija/woodmart-sabloni]] (svi gotcha-i)
- Content parity: title/meta, H1/H2 struktura, obim teksta ≥ live, interni linkovi, schema
- Namenske stranice se grade po **troslojnom modelu** iz F6 (rešenje hub + auto grid) — ne kao opis jednog proizvoda
- Standard verifikacije: 200 · 1×H1 · JSON-LD validan · slike/linkovi 200 · Yoast mete

## Verifikacija

- [ ] Svaki NEDOSTAJE-LOKAL red ima kategoriju
- [ ] w1-red-cekanja.md postoji, sortiran po klikovima, sa izvorima sadržaja
- [ ] `politika-kolacica` → 200 na lokalu
- [ ] Kategorija C predlozi prosleđeni u F4 (ili zabeleženi ako F4 već gotov → dopuna mape)

## Zatvaranje sesije

1. [[DNEVNIK-NAPRETKA]] — unos NA VRH (`[W3 PARITY F5]`): brojevi po kategoriji, top 5 prioriteta
2. [[PROGRESS]] — "Sledeće" ažurirati sa top 3 rebuild stranice
3. Štikliraj F5 u [[migracija/promptovi/_README]]
