---
name: nedeljni-izvestaj
description: Nedeljni mini-izveštaj za AntasLine — 7 dana vs prethodnih 7 (GA4 + Google Ads preko sopstvenog konektora) + GSC 28d prilike, po formatu CLAUDE §11. Koristi kad Miroslav kaže "nedeljni izveštaj", "izveštaj", "kako stojimo ove nedelje", "performanse" ili pri W5 zadatku 5.4.
---

# Nedeljni izveštaj — 7d vs 7d

Izvor podataka: sopstveni konektor (`[[.claude/skills/antasline-konektor]]`) —
direktno na GA4/GSC/Ads API-je, bez trećih učesnika. Windsor.ai je istekao
2026-07-27, više se ne koristi (videti [[DNEVNIK-NAPRETKA]]). Pun mesečni
snapshot je POSEBAN posao (`[[analiza/_TEMPLATE-snapshot]]`) — ovo je mini
verzija, ~15 min.

## 0. 🔴 DVA TVRDA PRAVILA — pročitati pre svakog povlačenja podataka

Oba su izmerena 2026-08-11 i oba su **već jednom pregažena isti dan** (izveštaj
poslat Miroslavu sa nefiltriranim totalima). Ne preskakati.

### 0.1 GA4 se UVEK poziva sa `--live-only`
Bez toga totali uključuju `localhost` (lokalni build od 22.07 nosi pravi
GTM-TRDT8K9 kontejner, pa šalje prave hitove u GA4). Izmereno:

| Period | Udeo `localhost` u pregledima |
|---|---|
| 28.07–03.08 | **42%** (1.068 od 2.572) |
| jul 2026 (ceo) | 15% (1.075 od 7.043) |

Korisnici/sesije trpe malo (par osoba), ali **pregledi, eventi i hvala-proxy
znatno** — a to je baš KPI serija. Izlaz uvek nosi `hosts` raspodelu; pogledaj
je i kad ti brojevi izgledaju normalno. Trajan filter (bez flag-a) čeka M
odluku — v. [[PROGRESS]] Blokeri.

### 0.2 Sirovi GA4 brojevi su naduvani — dva poznata baga
Dijagnostikovano 2026-08-11 mrežnim merenjem (`g/collect` po `en=`), nije
pretpostavka → [[dnevnik/2026-08-11-generate-lead-inflacija-dijagnoza]].

| Metrika | Faktor | Uzrok | Posle migracije 25.08 |
|---|---|---|---|
| hvala-proxy (pregledi) | **÷2** | suvišan GA4 `page_view` tag **id 18** na hvala pravilu | **ostaje** dok se tag 18 ne obriše (postoji i na buildu) |
| `generate_lead` | **÷3** | live Kallyas ima **dva GTM embeda** istog kontejnera | **nestaje sam** (build ima jedan embed → 1×) |

Praktično u izveštaju:
- „Prave konverzije" = **hvala-proxy ÷ 2**, ili tačnije `hvala_proxy_sessions`
  iz izlaza skripte (sesije, ne pregledi). Sirov broj pregleda se ne sme
  prikazati kao broj lidova.
- `generate_lead` se prikazuje kao **direkcioni** signal, uz napomenu o ÷3 —
  ne kao broj lidova.
- Ads-ova strana (uvezene konverzije) **nije** naduvana istim faktorom — broji
  svoje, ne deliti je.
- 🔴 **Prvi post-live izveštaj (posle 25.08):** `generate_lead` pada na ~⅓,
  hvala-proxy na ~½. **To nije pad konverzija** — obavezno napisati tu rečenicu
  u izveštaju, inače izgleda kao katastrofa.

## 1. Periodi

- Tekući: poslednjih 7 završenih dana (NE uključuj današnji delimičan dan)
- Prethodni: 7 dana pre toga — **uvek eksplicitni `date_from`/`date_to`
  (YYYY-MM-DD), nikad preset** za prethodni period (poznata Windsor zamka)
- GSC sekcija: poslednjih 28 dana (GSC podaci kasne 2–3 dana — pomeri prozor)

## 2. Šta se vuče (sopstveni konektor — `.claude/skills/antasline-konektor/scripts/`)

Svaka skripta se poziva DVA puta (tekući period + prethodni), uvek
eksplicitni `--from`/`--to`. Detalji pokretanja/kredencijala:
`[[.claude/skills/antasline-konektor]]`.

### GA4 — `ga4_report.py --from --to --live-only` 🔴 (flag obavezan, v. §0.1)
- Korisnici, sesije (totals za oba perioda)
- Eventi: skripta već povlači SVE evente nefiltrirano i agregira ih
  interno (rešava staru Windsor "in-filter nepouzdan" zamku) → vraća
  gotove `generate_lead`, `tel`, `mailto` brojeve
- Hvala-proxy (prava konverzija): `hvala_proxy_pageviews` polje u izlazu
  (već filtrirano na `pagePath contains "hvala"`) — 🔴 **podeliti sa 2**, ili
  koristiti `korekcija_merenja.hvala_proxy_sessions` (sesije, ne pregledi)
- `hosts` — raspodela po hostname-u; uvek pogledati (§0.1)
- `korekcija_merenja` — faktori i estimacija; skripta ih ne primenjuje sama
  na `events`/`hvala_proxy_pageviews`, sirovi brojevi ostaju sirovi

### Google Ads — `ads_report.py --from --to`
- Potrošnja (RSD), klikovi, CTR, CPC, konverzije — po kampanji + `totals`
- Ako skripta javi grešku o `ads-config.json` → developer token još nije
  odobren (čeka Google, [[reference/api-konektor-setup.md]] korak 4) —
  napiši "Nema podataka za Ads" u izveštaju, ne izmišljaj
- Ako kampanja vrati 0/prazno ALI skripta radi: proveri spend+impressions
  pre nego što pretpostaviš grešku (throttling istorija — [[reference/naucene-lekcije]])

### AI kanal — `ai_report.py --from --to` (🔴 obavezno, svake nedelje)
- Dodato 2026-08-11 jer se AI deo *stalno zaboravljao* (mesečni test 5.5 je
  preskočen ceo jul→avgust ciklus). Sada je deo standardnog povlačenja, ne
  poseban zadatak.
- Poziva se DVA puta kao i ostalo (tekućih 7d + prethodnih 7d). Vraća
  `ai_sessions_total`, `po_izvoru`, `top_landing`, `eventi`.
- ⚠️ **Brojke su male (jednocifrene po nedelji) — nedeljni Δ je skoro uvek
  šum.** Zato se u izveštaju prikazuje **31d rolling** prozor (tekućih 31 vs
  prethodnih 31), ne 7d. Jedina 7d stvar vredna pomena: pojava **novog izvora**
  (Claude, Copilot, Perplexity) ili nula sesija dve nedelje zaredom.
- Kanal „AI Assistant" u GA4 potcenjuje stvarno ~3× — koristi
  `ai_sessions_total` iz skripte, ne GA4 kanal.

### 🔴 Mesečni AI test 5.5 — provera roka (svake nedelje, 30 sekundi)
Pogledaj datum poslednjeg fajla `analiza/*-ai-test-*.md`.

| Prošlo od poslednjeg testa | Šta uraditi |
|---|---|
| < 30 dana | ništa, ne pominjati u izveštaju |
| 30–37 dana | jedan red u napomenama: „AI test dospeva za N dana" |
| **> 37 dana** | 🔴 **„Akcija nedelje" je AI test** — ne predlagati ništa drugo dok se ne odradi |

Metod i 5 fiksnih promptova: `[[seo/geo-ai-plan]]` §5, poslednji rezultat
`[[analiza/2026-07-22-ai-test-baseline]]`. Promptove **ne menjati** (uporedivost
trenda); jedini dozvoljeni dodatak je 6. prompt o „bez lepljenja" iz baseline
preporuke. Izvršava se u ChatGPT-u, **Incognito bez naloga**, svaki prompt u
novom razgovoru.

### GSC — `gsc_report.py --from --to`
- Vraća već filtrirane `opportunities` (pozicija 5–15, sortirano po
  prikazima) — top upiti sa niskim CTR-om
- Pomeri `--to` 2–3 dana unazad (GSC kašnjenje)
- Uporedi YoY gde je moguće (sezonski špic mar–maj maskira trendove)

## 3. Format izveštaja (tačan redosled, [[CLAUDE]] §11)

1. **GA4 tabela**: korisnici · sesije · generate_lead · tel · mailto —
   kolone: tekućih 7d / prethodnih 7d / Δ%
2. **Ads tabela**: potrošnja (RSD) · klikovi · CTR · CPC · uvezene konverzije —
   po kampanji
3. Red ispod tabela: **"Ukupan broj pravih konverzija do sada: N"**
   (kumulativ hvala-proxy od juna 2026 = mesec-nula; plaćene posebno —
   prag za Smart Bidding je 20–30 plaćenih)
4. **SEO (GSC 28d)**: top 5 upita po prikazima na pozicijama 5–15 sa niskim
   CTR — tabela: upit · prikazi · pozicija · CTR · predlog
5. **AI kanal (31d rolling)**: tabela — period · AI sesije · po izvoru ·
   top landing. Ispod: status mesečnog testa 5.5 (datum poslednjeg + „dospeva
   za N dana", ako je prošlo ≥30 dana)
6. Kratke napomene (2–4 bullet-a max): šta je uzrok većih promena
7. Poslednja rečenica: **"Akcija nedelje: [jedan konkretan predlog]"**

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
- 🔴 **Pre povlačenja podataka pročitaj [[PROGRESS]] (Urađeno vrh + Blokeri).**
  2026-08-11 je isti period obrađen dvaput jer to nije urađeno — drugi izveštaj
  je pregazio obe lekcije iz §0 i otišao Miroslavu sa naduvanim brojevima.

## 5. Posle izveštaja

- Izveštaj ide u chat (ne pravi se fajl osim ako Miroslav traži)
- Ako je otkriveno nešto što menja plan (npr. negativne opet cure, ECOTILE
  throttling) → unos u `[[dnevnik/ADS-DNEVNIK]]` log + pomeni u izveštaju
- Kumulativ plaćenih konverzija ≥20 → podseti: vreme za Maximize Conversions
  odluku (zadatak 4.8)
