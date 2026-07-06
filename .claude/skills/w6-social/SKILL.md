---
name: w6-social
description: W6 workstream — društvene mreže, email i GMB za AntasLine (startuje punim tempom posle live-a 2026-08-31, jeftini koraci i ranije). Koristi kad Miroslav kaže "social", "društvene mreže", "Instagram/Facebook/LinkedIn/YouTube post", "newsletter", "email marketing", "Customer Match" ili "W6".
---

# W6 — Social / Email / GMB

Nastao iz audita 2026-07-06: Organic Social = 70 korisnika/90d (0,5%
saobraćaja) ali 81% engagement — publika je kvalitetna, kanala nema.
Nijedan social/email zadatak nije postojao u planu do W6.

**Pravilo tempa:** do migracije (2026-08-31) rade se SAMO Faza 0 koraci
(jeftini, ne jedu kapacitet W1–W5). Pun ritam od septembra.

## Faza 0 — Preduslovi (pre live-a, ukupno ~2h)

- [ ] **Popis profila** #ceka-miroslav: koji nalozi postoje (FB, IG, LinkedIn,
  YouTube), ko ima pristup, u kakvom su stanju. Bez ovoga ništa dalje.
- [ ] **M5 odgovor** #ceka-miroslav: šta biva sa ~55 kontakata/mes — gde stoje
  email-ovi (inbox? tabela?). Ovo je sirovina za Customer Match i newsletter.
- [ ] **GMB paket** (već u planu kao 5.2/5.3): UTM fix, kategorije, recenzije
  6→20+, mesečni post
- [ ] Saglasnost za email: checkbox na kontakt formi + dopuna politike
  privatnosti (postoji samo politika kolačića) — pripremi tekst, Miroslav odobrava
- [ ] `sameAs` u Organization JSON-LD (GEO paket 2.8) popuniti profilima iz popisa

## Faza 1 — Ritam objava (od 2026-09-01)

| Kanal | Ritam | Sadržaj |
|---|---|---|
| Instagram + Facebook | 2×/ned | B2C: terase/bazeni/sportski tereni — before/after, reference (Spanoulis teren, Dunk Shop 3x3, Jakovo, Zlatibor, NS), boje/dezeni, sezonske objave |
| LinkedIn | 1×/ned | B2B: case study (Quectel ESD!), tehnički saveti, "kako smo rešili X u hali Y" — cilja industrijske/ESD kupce |
| YouTube | 1×/mes | 60s video montaže klik-sistema po liniji proizvoda; služi i kao Ads asset (publike čekaju YouTube prag serviranja) |
| GMB | 1×/mes | Post sa referencom/projektom — profil deluje živ |

**Radni tok po objavi:** izvor = stvarna referenca/fotke iz uploads ili sa
terena (nikad stock bez oznake) → copy kratko, srpski, brend ton iz
`[[reference/brend-knjiga]]` → UTM na SVE linkove (vidi Merenje) → objavu
priprema Claude, objavljuje Miroslav (dok se ne uvede alat za zakazivanje).

**Pravilo sadržaja:** svaki završen posao = 1 post + 1 zahtev za recenziju.
Epoksid pravilo važi i ovde ([[CLAUDE]] §1) — "alternativa epoksidu" sme,
prodaja epoksida ne.

## Faza 2 — Email (od septembra, čim M5 da sirovinu)

1. Konsoliduj postojeće upite u listu (ime, email, šta je pitao, datum) —
   samo kontakti sa pravnom osnovom
2. **Customer Match** upload u Google Ads (zadatak 4.9 plana — zaobilazi
   prag publika!)
3. Follow-up sekvenca za nove upite: D+2 podsetnik ako nema odgovora,
   D+30 "da li je projekat još aktuelan"
4. Sezonski newsletter (mala lista = ručno): februar "spremite terasu"
   (GSC špic mar–maj), septembar B2B "zimska sanacija hale"

## Faza 3 — Meta Ads test (tek posle stabilne migracije, ~oktobar)

- Budžet: 5–10k RSD/mes test, NE iz postojećeg Google budžeta bez odluke
- Publika: remarketing posetioci terase/bazeni stranica + lookalike
- Kreativa: najbolje organske objave iz Faze 1 (dokazan engagement)
- Windsor konektor `facebook` podržava read + write (kampanje/budžeti) —
  write SAMO uz eksplicitnu potvrdu Miroslava

## Merenje

- **UTM standard** (da GA4 klasifikuje kao Organic Social, ne Unassigned):
  `utm_source=instagram|facebook|linkedin|youtube&utm_medium=social&utm_campaign=<tema-YYYYMM>`
- KPI mesečno (u punom snapshotu): Organic Social korisnici (baseline 70/90d),
  engagement rate (baseline 81%), lidovi sa social landing stranica,
  veličina email liste, GMB pozivi/recenzije
- Ne izmišljati brojeve; promene <5% = stabilno

## Zatvaranje sesije

Kao u `/antasline-sesija` §5: DNEVNIK-NAPRETKA + PROGRESS + štikliranje ovde
u skill fajlu (checkbox-ovi Faze 0) ili u Master planu kad W6 uđe u njega.
