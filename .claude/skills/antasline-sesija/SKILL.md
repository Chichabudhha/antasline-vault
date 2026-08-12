---
name: antasline-sesija
description: Master radni tok za AntasLine projekat (redizajn + SEO + Ads do live-a 2026-08-24). Koristi na početku svake radne sesije, kad Miroslav kaže "nastavljamo", "gde smo stali", "sledeći zadatak", "nastavi W1/W2/W3/W4/W5", ili imenuje bilo koji zadatak iz Master plana V2.
---

# AntasLine — protokol radne sesije

CLAUDE.md nosi pravila i kontekst (učitan je automatski — ne ponavljaj ga).
Ovaj skill nosi TOK: kako se sesija otvara, bira zadatak, izvršava po
workstream-u i zatvara. Jedan glavni zadatak po sesiji.

## 1. Otvaranje sesije (uvek, redom)

1. Pročitaj `[[PROGRESS]]` — poslednje urađeno + "Sledeće" + blokeri
2. Pročitaj `[[2026-07-06-MASTER-PLAN-V2]]` sekciju 2 (N-raspored) — uporedi
   današnji datum sa nedeljom (N1=07–13.07 … N5=04–10.08, pa **prepravljeno
   2026-08-10**: N6'=11–16.08, freeze od 16.08, N7'=17–21.08, gate PET 21.08,
   **migracija PON 24.08**) i vidi šta je planirano za tekuću nedelju
3. Proveri zavisnosti (sekcija 4 plana): zadatak blokiran na #ceka-miroslav
   se ne bira; ako rok zavisnosti prolazi — podseti Miroslava i ponudi fallback
3b. **Ako je danas ponedeljak:** pre izbora zadatka, brzi pregled cele
   sekcije 4 (zavisnosti) naglas — rok/status/fallback po stavci, ne samo
   ono što direktno blokira ovu sesiju. Cilj: da kašnjenje isplivava odmah,
   ne u gužvi pred gate (21.08). Rokovi su od 2026-08-10 nedelju kraći —
   videti tabelu M odluka u [[2026-07-06-MASTER-PLAN-V2]] §4.
4. Predloži 1 glavni zadatak (+ eventualno 1 quick-win ≤15 min) i sačekaj
   potvrdu ako izbor nije očigledan iz Miroslavljeve poruke

## 2. Izvršavanje po workstream-u

### W1 — Dizajn/rebuild stranica
- OBAVEZNO prvo: `[[migracija/woodmart-sabloni]]` (šablon + svi gotcha-i)
- Pre pisanja novog CSS/HTML/JS obrasca (modal, tooltip, akordeon, tabovi,
  kartice, galerija, scroll efekat): skill **/modern-web-guidance** →
  `search "<šta gradiš>"` pa `retrieve "<id>"`. Cilj: native `<dialog>`/
  Popover API/container queries umesto novog JS-a i nove zavisnosti.
  Policy: **Baseline Widely available** (v. napomene u tom skillu)
- Šta je od novog u Chrome-u realno upotrebljivo kod nas (sa fallback-om) i
  šta je zamka: `[[reference/chrome-web-platform-2026]]`
- Content parity izvor: `migracija/live-export-2026-07-05/` (pages XML +
  inventar CSV sa Yoast metama)
- Za proizvode: pozovi skill **/obogati-proizvod** (ima svoj kompletan tok)
- Proveri `post_parent` pre linkovanja (child stranice imaju ugnježden URL)

### W2 — SEO content (C3 + GEO)
- Master lista: `[[seo/plan-novih-stranica]]` (20 stranica, 4 tijera, checkbox)
- Draftovi Tier1 postoje u `dnevnik/2026-07-05-draft-*.md` — ne pisati ispočetka
- Pravila po stranici: Yoast >80 · FAQ + FAQPage/Product schema · cena od–do
  ili placeholder · CTA `069 234 00 72` + forma · interni link ka `/industrijski-podovi/` ·
  prvi pasus = direktan odgovor (GEO) · anti-kanibalizacija provera
  (postojeći sadržaj na istu temu → skratiti + linkovati)
- Cene: prvo proveri `[[reference/cenovnik]]` (M10) pre nego što tražiš od
  Miroslava — ako je polje prazno tamo, tek onda je "na upit" placeholder
- GEO zadaci: `[[seo/geo-ai-plan]]`

### W3 — Tehnička + migracija (C1/C2 + CWV)
- Redirect mapa: `antasline-redirect-mapa-POPUNJENA.csv` (semicolon, UTF-8-BOM);
  AUTO-PREDLOG redovi se NE implementiraju dok C2 ne završi
- Kritična rupa: `/sportske-podloge/kosarkaske-konstrukcije/` (478 GSC klikova)
- CWV: prvo Lighthouse baseline u `dnevnik/PERFORMANCE-AUDIT.md`, pa optimizacija
  - Chrome 151 nosi Lighthouse **13.4.0** — uz svaki novi baseline upiši verziju
    Lighthouse-a, inače poređenje sa julskim snimcima nije validno
  - Performance panel ima **Soft FCP markere** (uz Soft LCP) — koristi za
    layered-nav/AJAX filtere gde nema pune navigacije
  - Pre optimizacije: **/modern-web-guidance** `search` za konkretan simptom
    (LCP slika, long task/INP, offscreen render, preload na hover) — kategorija
    `performance` ima 24 vodiča sa gotovim obrascima
  - LCP je blokiran na render-blocking CSS i namerno odložen na LiteSpeed CCSS/
    UCSS na produkciji — lokalni Performance snimak služi za relativno
    poređenje pre/posle, ne kao apsolutan broj (nema CDN/keš sloja)
- `.htaccess` 301 se generiše ali aktivira TEK na dan migracije

### W4 — Ads
- **Pozovi skill /antasline-ads** (ima ceo playbook: dijagnostika isporuke,
  licitiranje, negativne, RSA, migracija-checklist, podela CC/M odgovornosti)
- Hub: `[[dnevnik/ADS-DNEVNIK]]` (fazni plan, RSA banka, log — najnoviji unos gore)
- Podaci: sopstveni konektor (`ads_report.py`), **read-only** — Windsor istekao
  2026-07-27. Sve write akcije (RSA upis, budžet, pauza, strategija) radi
  Miroslav u Ads UI; CC priprema tekst i brojke
- Maximize Clicks dok nema 20–30 plaćenih konverzija; broad tek uz Smart Bidding
- Pad merenih brojeva posle tracking čišćenja = tačnije merenje, ne reagovati budžetom

### W5 — Tracking/merenje
- Ključni eventi: samo `generate_lead` (hvala-za-poruku) · `tel` · `mailto`;
  `tel` se NE uvozi u Ads
- 🔴 **Prerender/prefetch je opasan za našu konverziju**: `/hvala-za-poruku/`
  page view = jedini pravi lid. Speculation rules (`form_submission`, Chrome
  151) ili prefetch iz optimizacionog plugina mogu okinuti `generate_lead` na
  posetu koja se nije desila. Uslov pre bilo kakvog uvođenja: GTM trigger
  gate-ovan na `document.prerendering === false`. Detalji:
  `[[reference/chrome-web-platform-2026]]` §3 — proveriti i LiteSpeed
  podešavanja pre migracije 24.08
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
| DB | `antasline_local`, prefiks **`wpgs_`** (malim slovima — lokalni `wp-config` nosi `wpGs_` i radi samo zbog Windows case-insensitivity; na Linux serveru puca), MariaDB |
| MySQL CLI | `/c/xampp/mysql/bin/mysql -u root antasline_local` |
| PHP skripte | scratchpad + `C:\xampp\php\php.exe skripta.php` (wp-load bootstrap) |
| Backup | `mysqldump -u root antasline_local > C:\xampp\htdocs\antasline-backups\antasline_local_YYYY-MM-DD_pre-<opis>.sql` |
| Tema | WoodMart 8.5.4 + child; design system `antas-design.css`; `_woodmart_title_off=on` protiv 2×H1 |
| CTA telefon | **`069 234 00 72`** → `tel:+381692340072` (tzv. „linija 72"; druga linija je `069 234 00 74`). 🔴 Prefiks je **069**, ne 072 — v. [[CLAUDE]] §9 |

Bash ograničenja: komande >965 bajtova pucaju (piši fajl pa izvrši); brace
expansion `{a,b}` pravi literalne foldere; velike fajlove čitaj Read alatom.

## 4. Standard verifikacije (svaka izmenjena stranica)

- [ ] HTTP 200 · tačno 1×H1 · JSON-LD validan bez dupliranja
- [ ] Slike i interni linkovi vraćaju 200
- [ ] Yoast title/metadesc u `<head>`
- [ ] wpautop nije razbio markup (HTML u jednoj liniji unutar grid blokova)
- [ ] Regression: 1–2 ranije stranice i dalje ispravne

Kad izmena dira CSS/layout (ne samo sadržaj) — DevTools 151 prečice:
- Specificity tooltip u Styles tabu (hover nad selektorom) pre nego što se
  poseže za `!important` — dokazuje `:is()` sudar sa `base.css`
- Mobilni test preko osveženih iPhone/Pixel preseta (verifikovani safe-area
  inseti), ne preko ručno ukucane širine
- Detalji i puna tabela: skill **/woodmart-theme** §13

## 5. Zatvaranje sesije (uvek, redom)

1. `[[DNEVNIK-NAPRETKA]]` — novi unos NA VRH: `## YYYY-MM-DD [claude-code]
   [OBLAST] — naslov ✅` sa: šta je urađeno, gotcha-i, backup fajl, skripte
1b. Ako je sesija imala suštinski rad (ne trivijalna izmena) — napravi i
   `dnevnik/YYYY-MM-DD-kratak-naslov.md` po formatu iz
   `[[dnevnik/_TEMPLATE-sesija]]` (Šta je urađeno / Otvorene akcije /
   Beleške-odluke / Veze). Ovo je odvojeno od ledger linije u
   DNEVNIK-NAPRETKA — ledger ostaje kratak i jeftin za čitanje na svakom
   otvaranju sesije, detaljan fajl se čita samo na zahtev.
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
