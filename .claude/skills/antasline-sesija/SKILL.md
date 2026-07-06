---
name: antasline-sesija
description: Master radni tok za AntasLine projekat (redizajn + SEO + Ads do live-a 2026-08-31). Koristi na početku svake radne sesije, kad Miroslav kaže "nastavljamo", "gde smo stali", "sledeći zadatak", "nastavi W1/W2/W3/W4/W5", ili imenuje bilo koji zadatak iz Master plana V2.
---

# AntasLine — protokol radne sesije

CLAUDE.md nosi pravila i kontekst (učitan je automatski — ne ponavljaj ga).
Ovaj skill nosi TOK: kako se sesija otvara, bira zadatak, izvršava po
workstream-u i zatvara. Jedan glavni zadatak po sesiji.

## 1. Otvaranje sesije (uvek, redom)

1. Pročitaj `[[PROGRESS]]` — poslednje urađeno + "Sledeće" + blokeri
2. Pročitaj `[[2026-07-06-MASTER-PLAN-V2]]` sekciju 2 (N-raspored) — uporedi
   današnji datum sa nedeljom (N1=07–13.07 … N8=25–30.08) i vidi šta je
   planirano za tekuću nedelju
3. Proveri zavisnosti (sekcija 4 plana): zadatak blokiran na #ceka-miroslav
   se ne bira; ako rok zavisnosti prolazi — podseti Miroslava i ponudi fallback
4. Predloži 1 glavni zadatak (+ eventualno 1 quick-win ≤15 min) i sačekaj
   potvrdu ako izbor nije očigledan iz Miroslavljeve poruke

## 2. Izvršavanje po workstream-u

### W1 — Dizajn/rebuild stranica
- OBAVEZNO prvo: `[[migracija/woodmart-sabloni]]` (šablon + svi gotcha-i)
- Content parity izvor: `migracija/live-export-2026-07-05/` (pages XML +
  inventar CSV sa Yoast metama)
- Za proizvode: pozovi skill **/obogati-proizvod** (ima svoj kompletan tok)
- Proveri `post_parent` pre linkovanja (child stranice imaju ugnježden URL)

### W2 — SEO content (C3 + GEO)
- Master lista: `[[seo/plan-novih-stranica]]` (20 stranica, 4 tijera, checkbox)
- Draftovi Tier1 postoje u `dnevnik/2026-07-05-draft-*.md` — ne pisati ispočetka
- Pravila po stranici: Yoast >80 · FAQ + FAQPage/Product schema · cena od–do
  ili placeholder · CTA 072 + forma · interni link ka `/industrijski-podovi/` ·
  prvi pasus = direktan odgovor (GEO) · anti-kanibalizacija provera
  (postojeći sadržaj na istu temu → skratiti + linkovati)
- GEO zadaci: `[[seo/geo-ai-plan]]`

### W3 — Tehnička + migracija (C1/C2 + CWV)
- Redirect mapa: `antasline-redirect-mapa-POPUNJENA.csv` (semicolon, UTF-8-BOM);
  AUTO-PREDLOG redovi se NE implementiraju dok C2 ne završi
- Kritična rupa: `/sportske-podloge/kosarkaske-konstrukcije/` (478 GSC klikova)
- CWV: prvo Lighthouse baseline u `dnevnik/PERFORMANCE-AUDIT.md`, pa optimizacija
- `.htaccess` 301 se generiše ali aktivira TEK na dan migracije

### W4 — Ads
- Hub: `[[dnevnik/ADS-DNEVNIK]]` (fazni plan, RSA banka, log — najnoviji unos gore)
- Windsor.ai = read za dijagnostiku (connector `google_ads`, account
  `['156-886-0314']`); write akcije samo pause/enable/budžet i SAMO uz
  eksplicitnu potvrdu Miroslava; sve ostalo su koraci za Ads UI koje radi on
- Maximize Clicks dok nema 20–30 plaćenih konverzija; broad tek uz Smart Bidding
- Pad merenih brojeva posle tracking čišćenja = tačnije merenje, ne reagovati budžetom

### W5 — Tracking/merenje
- Ključni eventi: samo `generate_lead` (hvala-za-poruku) · `tel` · `mailto`;
  `tel` se NE uvozi u Ads
- Windsor gotchas: `[[reference/naucene-lekcije]]` (in-filter nepouzdan →
  povuci sve pa agregiraj; eksplicitni date_from/date_to za poređenja;
  hvala-proxy = `[["page_path","contains","hvala"]]` na `screen_page_views`)
- Nedeljni izveštaj: format [[CLAUDE]] §10 (7d vs 7d, RSD, "Nema podataka za
  [izvor]" umesto izmišljanja, na kraju "Akcija nedelje: …")
- GTM izmene: ručno u GTM UI (JSON import na ovom kontejneru puca — ne pokušavati)

## 3. Okruženje — brza referenca

| Šta | Vrednost |
|---|---|
| WP build | `C:\xampp\htdocs\antasline` → `http://localhost/antasline` |
| DB | `antasline_local`, prefiks `wpGs_`, MariaDB |
| MySQL CLI | `/c/xampp/mysql/bin/mysql -u root antasline_local` |
| PHP skripte | scratchpad + `C:\xampp\php\php.exe skripta.php` (wp-load bootstrap) |
| Backup | `mysqldump -u root antasline_local > C:\xampp\htdocs\antasline-backups\antasline_local_YYYY-MM-DD_pre-<opis>.sql` |
| Tema | WoodMart 8.5.4 + child; design system `antas-design.css`; `_woodmart_title_off=on` protiv 2×H1 |
| CTA telefon | 072 → `tel:+381692340072` |

Bash ograničenja: komande >965 bajtova pucaju (piši fajl pa izvrši); brace
expansion `{a,b}` pravi literalne foldere; velike fajlove čitaj Read alatom.

## 4. Standard verifikacije (svaka izmenjena stranica)

- [ ] HTTP 200 · tačno 1×H1 · JSON-LD validan bez dupliranja
- [ ] Slike i interni linkovi vraćaju 200
- [ ] Yoast title/metadesc u `<head>`
- [ ] wpautop nije razbio markup (HTML u jednoj liniji unutar grid blokova)
- [ ] Regression: 1–2 ranije stranice i dalje ispravne

## 5. Zatvaranje sesije (uvek, redom)

1. `[[DNEVNIK-NAPRETKA]]` — novi unos NA VRH: `## YYYY-MM-DD [claude-code]
   [OBLAST] — naslov ✅` sa: šta je urađeno, gotcha-i, backup fajl, skripte
2. `[[PROGRESS]]` — red na vrh "Urađeno" tabele; ažuriraj "Sledeće"/"Blokeri"
   ako se promenilo
3. `[[2026-07-06-MASTER-PLAN-V2]]` — štikliraj/označi zadatak (✅ + datum);
   isto u `[[seo/plan-novih-stranica]]` ako je W2
4. Nova naučena lekcija → `[[reference/naucene-lekcije]]` ili woodmart-sabloni
5. Otvorena pitanja → #ceka-miroslav + jasno reci Miroslavu šta čekaš
6. Git: Obsidian Git auto-sync (~10 min) — ne komituj ručno osim na zahtev

## 6. Tvrda pravila (prekršaj = greška sesije)

- ❌ Live sajt (antasline.com) se NE dira — samo lokalni build; `[cpanel-live]`
  isključivo kad Miroslav eksplicitno traži
- ❌ Epoksid se ne prodaje — epoksid upiti idu na conquest (post 2542)
- ❌ Ne izmišljati brojeve, cene, specifikacije — "Nema podataka" / "na upit"
- ❌ RankMath se ne pominje kao akcija — Yoast ostaje do eksplicitne odluke
- ✅ Backup pre svake destruktivne izmene baze
- ✅ Analiza → predlog → Miroslavljevo odobrenje → izvršenje (za nepovratno)
- Jezik: srpski ekavica · kratko · tabele · bez uvoda i zaključka
