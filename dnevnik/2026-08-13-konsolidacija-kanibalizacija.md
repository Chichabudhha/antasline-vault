---
tip: sesija
datum: 2026-08-13
tag: "[claude-code]"
oblast: W2/SEO
naslov: Kanibalizacija — analiza 9 klastera + tri konsolidacije (C/D/B)
backup: antasline_local_2026-08-13_pre-konsolidacija-301.sql
---

# Konsolidacija duplikata i analiza kanibalizacije

Sedma stavka dana. Zadatak je došao kao M-ova lista od 9 tačaka (URL higijena,
Ads smernice, 6 klastera kanibalizacije, meni „Cene", live alignment
`/sportske-podloge/`). Obim je veći od jedne sesije i **menja sadržaj**, pa je
3 dana pred freeze rađeno po pravilu analiza → predlog → odobrenje → izvršenje.

## Šta je urađeno

### 1. Analiza (sve 9 tačaka) — [[seo/2026-08-13-kanibalizacija-konsolidacija-plan]]
Podaci: GSC 90d (15.05–12.08) po stranici (`gsc_page_queries.py`), Google Ads API
(`ads_final_urls.py`, svež pull), lokalna baza.

🔴 **Metodološki nalaz koji menja ceo okvir:** sve „cena" i „dimenzije" stranice
imaju **0 GSC prikaza jer ne postoje na live-u** (napravljene na buildu u julu).
Kanibalizacija se zato ne može *meriti* — može se samo *predvideti* za 24.08.
Svaka preporuka je zato „koliko rizikujemo postojeći saobraćaj".

🔴 **Najveći rizik nije bio na listi:** post 2298 (`kako-napraviti-teren-za-basket`)
nosi **13.686 prikaza / 385 klikova / 90d** i drži poziciju **1,0–1,9** za
„dimenzije košarkaškog terena", „dimenzije table za koš" i „dimenzije fudbalskog
terena" (2.174 prikaza). Build izbacuje **4 nove stranice na tačno te upite**
(16585, 16586, 16688, 17027) — sve `index`, **bez canonical-a**, i **nijedna ne
linkuje ka 2298**, niti 2298 ka njima. Ostaje **otvoreno** (M nije odobrio F).

🔴 **`/sportske-podloge/` je na buildu izgubila sadržaj koji nosi klikove:** live
drži poz. **1,6** za „podloga za košarkaški teren" (47 kl.) i **2,0** za „…cena"
(39 kl.) — skoro polovina od 178 klikova/90d dolazi iz basket klastera, a H2
„Izgradnja sportskih terena za basket u vašem dvorištu" na buildu **ne postoji**;
5438 ne pominje ni `/planer-terena/`. Ostaje **otvoreno** (M nije odobrio E).

Ostali nalazi: `-2` slugova ima **3**, od toga 1 draft, a `…plocica-2` je
**pobednička** verzija (249 prikaza/13 kl.) — ne dira se; jedini pravi kandidat je
`ergonomske-podloge-2` (1 prikaz), gde `-2` postoji jer slug drži **prilog**, ne
stranica. Ads: `tracking_url_template` `null` na svih 14 kampanja ✅, ali 3 oglasa
+ 2 asseta vode na **tuđi domen `ekopodneploce.rs`** i 11 URL-ova na mrtve
`/home/…` putanje — sve u PAUZIRANIM kampanjama, blokira reaktivaciju, ne 24.08.

### 2. Izvršeno (M odobrio C + D + B) — `migracija/alati/job-konsolidacija-301-2026-08-13.php`

| | Šta | Rezultat |
|---|---|---|
| **C** | Parkiralište: cenovni sadržaj sa 16876 → **16589** (1.197 prikaza / 98 kl., poz. 1,0–1,8), 16876 → draft | H2 „Cena podloge za parkiralište po m²" + tabela 4 modela + „saće ili nasut šljunak" + **2 nove FAQ stavke** (i u vidljivom tekstu i u JSON-LD → 4→6 pitanja) |
| **D** | Maloprodaja: 16683 → draft, primarna **16142** (live URL + Ads odredište + 2× duža) | self-link na 16142 razmotan, 17026 preusmeren, meni 17411 prevezan |
| **B** | Bergo Easy 16665 → draft (proizvod diskontinuiran), sadržaj → **16663** | lista primene +5 event namena, **8 event fotografija** preseljeno u „u praksi" sekciju, title/meta 16663 preuzeli „manifestacije, sajmove i promocije" |

301 pravila dodata (74 → **76**, detalji u odeljku „301 mapa" ispod): bergo-easy →
iznajmljivanje, podovi-za-maloprodajne-objekte → podovi-za-radnje. 16876 **nema**
pravilo — `/podloge-za-parkiraliste-cena/` vraća **404 na live-u**, nikad nije
objavljena.

## Gotcha-i

- 🔴 **Nova `.al-section` se namerno NIJE pravila** ni na jednoj stranici — dve
  susedne sekcije istog tona daju 144px mrtve trake (FAZA 2, isti dan), a i na
  16589 i na 16663 bi novi blok pao između `paper` i `mist`. Rešenje: sadržaj je
  ubačen **unutar postojećih sekcija** (cena iza uporedne tabele, galerija u
  „u praksi" bloku) → ritam tonova netaknut, 0 CSS izmena.
- 🔴 **Inline FAQPage JSON-LD je u `post_content`**, ne u Rank Math schema meta —
  dodavanje FAQ stavke koja se ne doda i u `<script>` blok tiho razilazi schemu i
  vidljiv sadržaj. Skripta menja oba na istom mestu.
- 🔴 `post_content` se čita i piše **direktno preko `$wpdb`** — `get_post_field()`
  u `display` kontekstu pušta `wptexturize`, koji iskrivi apostrofe u WPBakery
  `css=""` i tiho obori `str_replace` (gotcha iz FAZE 1, isti dan).
- 🟡 **Draftovanje stranice ostavlja mrtve stavke u meniju** — `nav_menu_item`
  zapisi ne prate status cilja. Nađene 4 (17427, 17411, 16724, 17415): jedna
  draftovana, tri prevezane. Svaki naredni draft mora proći istu proveru.
- 🟢 Skripta broji pogotke svakog `str_replace` obrasca i prijavljuje ako ih nije
  tačno 1 — tiho promašen obrazac je glavni rizik ovog tipa izmene. 0 promašaja.

### 301 mapa — dva kruga ispravki

`redirect-verify.php` je posle draftovanja prijavio **2 cilja 404** i time otkrio
da **4 istorijska pravila sa 365 GSC pogodaka** (268 + 54 + 43 + 1 bez brojača)
vode baš na 16665 i 16683. Lanci spljošteni na konačna odredišta.

🔴 **Dva sopstvena kvara usput:**
1. Draft je prvo ispravljan **ručno** — a `redirect-verify.php` čita **CSV izvore**,
   ne `.txt`. Ručna izmena je zato prošla vizuelno a verifikator je i dalje pucao;
   uz to bi je sledeće pokretanje generatora tiho vratilo. Ispravljeno u CSV-ovima.
2. Obrazloženje je upisano u **4. kolonu** HIST CSV-a — a ta kolona je
   **skip-zastavica** u generatoru (`htaccess-301-generate.php:68`), ne polje za
   belešku: prvi regen je izbacio **5 pravila** (mapa 74 → 71). Kolona ispražnjena,
   obrazloženje živi ovde.

Konačno stanje: **76 pravila** (74 + 2 nova), generisano skriptom iz CSV-a,
svi ciljevi 200. Backup pre izmene: `redirect-mapa-*.bak-2026-08-13`,
`htaccess-301-DRAFT.bak-2026-08-13` (u `antasline-backups/`).

## Verifikacija

- 7 izmenjenih/susednih URL-ova: **200 · 1×H1 · 0 PHP grešaka**; oba JSON-LD bloka
  na 16589 validna (`FAQPage` sa 6 pitanja + Rank Math `@graph`)
- 3 draftovane stranice: **404** na buildu ✅
- **0 preostalih veza** ka draftovanim URL-ovima u celoj bazi, **0** stavki menija
- 8 preseljenih fotografija: sve **200**
- markup nije razbijen `wpautop`-om (tabela bez ubačenih `<p>`)
- regresija: početna, `/kontakt/`, `/industrijski-podovi/`, conquest 2542 → 200

## Otvorene akcije

- 🔴 **E — `/sportske-podloge/`**: vratiti basket/„vrste podloga" semantiku + link
  na `/planer-terena/`. **Najskuplja stavka na listi** (~90 kl./90d rizika).
  Mora **pre freeze-a 16.08** ako se radi.
- 🔴 **F — dimenzije klaster vs 2298**: uzajamni linkovi + pomak title-a ka
  transakcionoj nameni, ili `noindex` na 4 nove stranice. Isti rok.
- 🟡 G/H/A (meni „Cene", uzajamni linkovi garaže/terase, `ergonomske-podloge-2`)
  — nisu odobrene, nisu hitne.
- 🟡 **Ads pre reaktivacije kampanja** (M): 2 URL-a na tuđem domenu, 11 na
  `/home/…`, 4 na `http://`.
- 🟡 Meni stavka **17424 nema naslov** (prazan red u „Cene" segmentu) — bag bez
  obzira na odluku o segmentu.

## Veze
- [[seo/2026-08-13-kanibalizacija-konsolidacija-plan]] — pun nalaz sa GSC brojkama
- [[migracija/2026-08-10-pre-migration-checklist]] · [[PROGRESS]]
