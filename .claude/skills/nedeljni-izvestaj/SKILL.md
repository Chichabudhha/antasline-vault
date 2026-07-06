---
name: nedeljni-izvestaj
description: Nedeljni mini-izveštaj za AntasLine — 7 dana vs prethodnih 7 (GA4 + Google Ads kroz Windsor.ai) + GSC 28d prilike, po formatu CLAUDE §10. Koristi kad Miroslav kaže "nedeljni izveštaj", "izveštaj", "kako stojimo ove nedelje", "performanse" ili pri W5 zadatku 5.4.
---

# Nedeljni izveštaj — 7d vs 7d

Izvor podataka: Windsor.ai (read-only). Pun mesečni snapshot je POSEBAN
posao (`[[analiza/_TEMPLATE-snapshot]]`) — ovo je mini verzija, ~15 min.

## 1. Periodi

- Tekući: poslednjih 7 završenih dana (NE uključuj današnji delimičan dan)
- Prethodni: 7 dana pre toga — **uvek eksplicitni `date_from`/`date_to`
  (YYYY-MM-DD), nikad preset** za prethodni period (poznata Windsor zamka)
- GSC sekcija: poslednjih 28 dana (GSC podaci kasne 2–3 dana — pomeri prozor)

## 2. Šta se vuče (Windsor konektori)

### GA4 — `googleanalytics4`, account `['292720335']`
- Korisnici, sesije (totals za oba perioda)
- Eventi: povuci event_name + event_count **NEFILTRIRANO** pa agregiraj sam
  (`in` filter operator je nepouzdan — naučena lekcija) → izdvoj
  `generate_lead`, `tel`, `mailto`
- Hvala-proxy (prava konverzija): `screen_page_views` sa filterom
  `[["page_path", "contains", "hvala"]]`

### Google Ads — `google_ads`, account `['156-886-0314']`
- Potrošnja (RSD), klikovi, CTR, CPC, uvezene konverzije — po kampanji + total
- Ako kampanja vrati prazno: proveri spend+impressions pre nego što
  pretpostaviš grešku konektora (throttling istorija)
- Conversion action segmentacija vraća samo akcije sa ≥1 konverzijom u periodu

### GSC — `searchconsole`, account `['sc-domain:antasline.com']`
- Top upiti po prikazima sa **pozicijom 5–15 i niskim CTR-om** = prilike
- Uporedi YoY gde je moguće (sezonski špic mar–maj maskira trendove)

## 3. Format izveštaja (tačan redosled, [[CLAUDE]] §10)

1. **GA4 tabela**: korisnici · sesije · generate_lead · tel · mailto —
   kolone: tekućih 7d / prethodnih 7d / Δ%
2. **Ads tabela**: potrošnja (RSD) · klikovi · CTR · CPC · uvezene konverzije —
   po kampanji
3. Red ispod tabela: **"Ukupan broj pravih konverzija do sada: N"**
   (kumulativ hvala-proxy od juna 2026 = mesec-nula; plaćene posebno —
   prag za Smart Bidding je 20–30 plaćenih)
4. **SEO (GSC 28d)**: top 5 upita po prikazima na pozicijama 5–15 sa niskim
   CTR — tabela: upit · prikazi · pozicija · CTR · predlog
5. Kratke napomene (2–4 bullet-a max): šta je uzrok većih promena
6. Poslednja rečenica: **"Akcija nedelje: [jedan konkretan predlog]"**

## 4. Pravila interpretacije (ne kršiti)

- Promene **<5% = stabilno stanje**, ne trend — ne komentarisati kao rast/pad
- Konektor ne vrati podatke → napiši **"Nema podataka za [izvor]"** —
  NIKAD ne izmišljati brojeve
- Pad merenih brojeva posle tracking čišćenja (BLOK A, kraj juna 2026) =
  tačnije merenje — ne predlagati promenu budžeta zbog toga
- "Lidovi" pre BLOK A prevezivanja nisu validni — ne porediti sa njima
- Sve Ads vrednosti u RSD; nova skraćenica se objasni pri prvom pominjanju
- Bez uvoda i zaključka — odmah tabele
- Jul 2026+: proveri da li se GA4 `conversions` vratio na normalu (~60–160/mes)
  — zadatak 5.1 plana; jedna rečenica u napomenama dok se ne potvrdi

## 5. Posle izveštaja

- Izveštaj ide u chat (ne pravi se fajl osim ako Miroslav traži)
- Ako je otkriveno nešto što menja plan (npr. negativne opet cure, ECOTILE
  throttling) → unos u `[[dnevnik/ADS-DNEVNIK]]` log + pomeni u izveštaju
- Kumulativ plaćenih konverzija ≥20 → podseti: vreme za Maximize Conversions
  odluku (zadatak 4.8)
