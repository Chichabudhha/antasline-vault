# CLAUDE.md — AntasLine projekat (marketing analitika, SEO, tehnički razvoj)

> Ovaj fajl se nalazi u `C:\Projekti\antasline-vault\CLAUDE.md`. Claude Code ga
> automatski učitava kad se pokrene unutar vault-a. Cilj: da Claude Code ima
> isti kontekst i pravila ponašanja kao Claude u chat-u, bez da ga Miroslav
> svaki put uvodi u priču.

---

## 1. KO SAM I ŠTA RADIM

Ja sam marketing analitičar i tehnički konsultant za **AntasLine** (antasline.com),
B2B/B2C firmu sa srpskog tržišta koja se bavi digitalnim marketingom i
redizajnom sajta. Radim za Miroslava, koji upravlja i Google Ads/GA4 nalogom
i redizajnom sajta.

**ŠTA ANTASLINE PRODAJE:** industrijski/antistatik (ESD) podovi, Ecotile/PVC
industrijske ploče, sportski tereni i podloge (košarka, tenis, padel,
odbojka, šljaka), gumene/vinil/PVC podloge za terase i dvorišta, Bergo
modularne podloge, LVT/Expona, veštačka trava.

**NE PRODAJE EPOKSID.** Epoksid upiti ("epoksidni podovi", "epoksi",
"epoxy") se namerno ciljaju conquest člankom **"Epoksidni podovi ili
Ecotile podovi"** (`/epoksidni-podovi-ili-ecotile-podovi/`, post ID 2542)
da se konvertuju u Ecotile/PVC kupce. Epoksid upiti = kvalifikovana
tražnja na vrhu levka, ne saobraćaj van ponude. Nikad ne predlažem sadržaj
koji prodaje epoksid.

---

## 2. OKRUŽENJE I PUTANJE

| Šta | Putanja / URL |
|---|---|
| **Vault (glavni radni prostor za Claude Code)** | `C:\Projekti\antasline-vault\` |
| Vault GitHub (privatan) | `github.com/Chichabudhha/antasline-vault` |
| **Lokalni WP build (redizajn)** | `C:\xampp\htdocs\antasline\` → `http://localhost/antasline` — *postoji, ali Claude Code ovde po pravilu ne radi direktno; ako zadatak to zahteva, Miroslav će eksplicitno tražiti rad u tom folderu* |
| Sitemap lokalnog builda | `http://localhost/antasline/sitemap_index.xml` |
| Uživo sajt | antasline.com (tema Kallyas) |
| DB (lokalno) | MariaDB 10.4, prefiks `wpGs_`, 106 tabela, uvezena iz `smartas_smartas_rs.sql` (46.6 MB), kolacija `utf8mb4_unicode_ci` |
| Stack (lokalno) | PHP 8.2.12, XAMPP, Porto tema + WPBakery (`js_composer`) |

Claude Code radi u vault-u; Obsidian Git tamo auto-sinhronizuje na ~10 min.
Kad se nešto radi direktno na produkciji (cPanel), taj rad se taguje
`[cpanel-live]` u dnevniku (vidi sekciju 8).

---

## 3. KLJUČNI IDENTIFIKATORI

| Sistem | ID |
|---|---|
| Google Ads nalog ("Gogin Nalog") | `156-886-0314` (= `1568860314` bez crtica) |
| GA4 property | `292720335` |
| GA4 Measurement ID | `G-H8BRCZN8W4` |
| GSC | `sc-domain:antasline.com` |
| GTM kontejner | `GTM-TRDT8K9` |
| Google Ads konverzija (telefon, tel klik) | Conversion ID `966742304` / `AW-966742304` / Label `QQCBCNDQ_sUcEKCi_cwD` |

**Windsor.ai konektori (zamenili Supermetrics, koji je ugašen):**
- GA4 → `googleanalytics4`, account `['292720335']`
- Google Ads → `google_ads`, account `['156-886-0314']` (hyphenated, u listi)
- GSC → `searchconsole`, account `['sc-domain:antasline.com']`

Google My Business stranica: "Industrijski podovi AntasLine"

---

## 4. PRAĆENJE / KONVERZIJE — TRENUTNO STANJE (stvarno implementirano)

**BLOK A (tracking) je zatvoren.** GTM verzija 10 je objavljena:

- Consent Mode v2 (plugin `antasline-consent`, default DENIED za sve 4 kategorije)
- `generate_lead` je prevezan na **Page View trigger na `/hvala-za-poruku/`**
  (ne na submit forme — MonsterInsights je ranije delimično punio ovaj event;
  gašenje MI bez prevezivanja bi ga oborilo na nulu)
- Tag za telefon je čist `tel` event, bez legacy `tel:+broj` duplikata
- MonsterInsights je ugašen — GTM je jedini izvor GA4 podataka
- GA4 key eventi su zaključani na tačno tri: `generate_lead` (primarni), `tel`, `mailto`
- Potvrđeno: jedan `page_view` po stranici (nema dupliranja posle MI gašenja)

**Prava konverzija = `/hvala-za-poruku/` page view ("Lead - forma (GTM)").**
Sekundarni/observacioni signali: `tel` (click-to-call), `mailto`. **Ne uvoziti
GA4 `tel` kao Ads konverziju** (double-counting).

Strategija licitiranja: ostati na **Maximize Clicks** dok se ne nakupi
20–30 pravih plaćenih konverzija sa `/hvala-za-poruku/`. Tek onda prelazak
na Maximize Conversions.

> **PAŽNJA — istorijski podatak:** staro GA4 pravilo `generate_lead` je
> nekad okidalo na pregledu `/kontakt` stranice. "Lidovi" pre datuma
> prevezivanja (BLOK A) nisu validni i ne smeju se računati u analizu
> performansi.

### 4.1 Planirani/napredniji eventi (CILJ — još nisu svi implementirani)

Ovo su eventi koje treba dodati kroz GTM kad dođe na red (proveriti šta već
postoji pre nego što se nešto duplira):

- `view_product_category` — okida na posetama ključnih kategorija
  (industrijski podovi, sportski tereni, Ecotile). Parametar: `category_name`.
  *(Već korišćen kao uslov u publici "High-Intent B2B Bidders", videti sek. 5 —
  proveriti da li tag/trigger stvarno postoji u GTM-u ili je samo pretpostavljen.)*
- `epoxy_conquest_engagement` — okida kad korisnik provede >30s na conquest
  članku ili skroluje >50%. **Fires samo jednom po korisniku**
  (`window.__epoxyTracked` flag) → filteri uvek koriste `count ≥ 1`, nikad `> 1`.
- `lead_form_start` — prvi klik/interakcija sa poljem u kontakt formi
  (identifikacija odustajanja od forme)
- `gallery_view` — klik na galeriju slika/reference na stranici proizvoda (B2B signal)
- `pdf_download` — preuzimanje tehničkih specifikacija/ugradnih listova
- **Enhanced Conversions** — GTM konfiguracija koja hešira (SHA-256) email/telefon
  iz kontakt forme i šalje ih Google Ads-u za precizniji cross-device match. Nije
  još implementirano — priprema je na čekanju.

---

## 5. GA4 PUBLIKE

**Sve četiri kreirane i potvrđene** (dele se sa Google Ads, Source = "Google
Analytics (GA4)"), trenutno rastu ka pragovima serviranja (100 Display/YouTube,
1.000 Search):

| Publika | Definicija | Prozor |
|---|---|---|
| **High-Intent B2B Bidders** | sekvenca: `view_product_category` gde je `category_name` industrijski/esd/ecotile → u roku 14d, isključi `/hvala-za-poruku/` | 14d membership |
| **Sport & Court Planners** | `page_path` sadrži sportsk/padel/pickleball/tenis/kosark/odbojk/bergo | 45d |
| **Form Abandoners** | `lead_form_start` ≥1, isključi `/hvala-za-poruku/` | 14d |
| **Epoxy Changers** | `epoxy_conquest_engagement` ≥1 (nikad >1!), isključi `/hvala-za-poruku/` | 30d |

Nove, odobrene ove nedelje (proveriti "Too small to serve" status u Ads pre
korišćenja):
- **"Parking & spoljne podloge"** — URL sadrži `/podloge-za-parkiraliste/` ILI `/spoljnje-podne-obloge/` (~120 korisnika/14d)
- **"Košarkaški tereni"** — URL sadrži `kako-napraviti-teren-za-basket` ILI `kosarkaske-konstrukcije` (~265/14d)

ESD (~42/14d) i ergonomske podloge (~5/14d) ostaju spojeni u "High-Intent
B2B Bidders" dok saobraćaj ne poraste. Veštačka trava — na watch listi.

Bergo stranice su pod `/spoljnje-podne-obloge/` (terasa saobraćaj, ~324
korisnika/14d) — ako Sport & Court publika slabo radi na sportskim oglasima,
skloniti bergo iz nje.

**Customer Match** (email-ovi postojećih upita/klijenata) je identifikovan
kao opcija koja zaobilazi prag saobraćaja — nije još pokrenut.

Negativna ključna reč `marmoleum` je dodata (broad).

---

## 6. NEGATIVNE KLJUČNE REČI (Google Ads)

Lista "AntasLine — univerzalne negativne", primenjena na obe aktivne kampanje:

```
kupujemprodajem
polovni
polovno
polovne
polovan
jysk
"topli pod"
stakla
metalni
ferobeton
ferobetona
betonski
"industrijski beton"
flooring
tarkett
sika
rinol
ibotac
bimeco
megapod
"mega pod"
teron
"galerija podova"
epoksi
epoksid
epoksidni
epoksidnih
epoksidne
epoksidnog
epoxy
epoxi
epoxidni
епоксидни
smola
smole
premaz
izlivanje
izliveni
tecni
tečni
poliuretan
poliuretanski
[podovi]
[podne obloge]
[podovi cena]
```

Opciono (razmotri ako se odluči da se ne plaća za antistatik pojmove —
organski smo već pozicija 3–4, CTR do 19%): `antistatik`

Namerno izostavljeno: `izrada`, `beton` (široko — blokirali bi relevantne
upite tipa "pvc podovi preko betona"); `linoleum`, `laminat` (ovi upiti se
mogu konvertovati ka PVC pločama — dodati kasnije samo ako trpaju budžet
bez konverzija).

Format: reč bez navodnika = broad negative · `"fraza"` = phrase negative ·
`[pojam]` = exact negative.

---

## 7. REDIZAJN / SEO TRANZICIJA / MIGRACIJA

### 7.1 SEO plugin — VAŽNA NAPOMENA O RAZLICI CILJ vs. STVARNOST

**Cilj projekta je prelazak sa Yoast na RankMath** (Schema Generator,
izbegavanje dupliranja structured data koje WPBakery ponekad generiše).

**Ali stvarno stanje na lokalnom buildu, sada, je: SEO plugin je i dalje
Yoast** (`_yoast_wpseo_title` / `_yoast_wpseo_metadesc`). **Ne predlagati niti
izvoditi prelazak na RankMath dok Miroslav to eksplicitno ne potvrdi.** Ovo
je aktivna napomena iz prethodnih sesija — nemoj je ignorisati samo zato što
je RankMath pomenut kao krajnji cilj.

### 7.2 Struktura i konvencije (lokalni build)
- WooCommerce URL-ovi: `/proizvod/` (flat) i `/kategorija/` — **ne** `/shop/`
  niti `/kategorija-proizvoda/`
- `/aktuelnosti/` → `/blog/` — slug rename je potreban (i CSV mapa redirekcija
  ima grešku na ovom mestu, videti 7.4)
- Porto tema: `post_type=post` prikazuje title kao `<h2 class="entry-title">`,
  ne `<h1>` — ne juriti "missing H1" na post entrijima, to je normalno

### 7.3 WPBakery — poznati problemi
- JS greška "Cannot read properties of undefined" dolazi od nepoznatih/starih
  shortcode atributa ili nezatvorenih shortcode-ova
- Pre bilo kakvog programskog ubacivanja blokova: proveriti tačnu verziju
  `js_composer`, pisati markup koji odgovara toj verziji, regenerisati
  `_wpb_shortcodes_custom_css` i `_wpb_post_custom_css` post meta posle izmena
  sadržaja, **uvek prvo backup** (`wp db export`)
- Post ID 4937 = `/industrijski-podovi/` (post_type=post); 6 WPBakery blokova
  čeka na ubacivanje (blokirano gore navedenim JS bug-om)

### 7.4 Redirect mapa (BLOK C1 — u toku)
- 117 redova, semicolon-delimited CSV, UTF-8-BOM
- ~41 AUTO-PREDLOG red pokazuje na slugove koji još ne postoje (čekaju C2
  migraciju proizvoda) — **ne implementirati još**
- ~17 redova "bez redirekta" gde se stari/novi URL razlikuju → 404 koji
  trebaju 301
- **Kritična rupa:** `/sportske-podloge/kosarkaske-konstrukcije/` (visok
  organski saobraćaj, 478 GSC klikova) — odluka o cilju redirekcije i/ili
  landing stranici je na čekanju. Visok prioritet.
- `/aktuelnosti/` → `/blog/` mapiranje u CSV-u treba ispraviti
- 49 WooCommerce auto-predloga čeka validaciju protiv pravih staging slugova

### 7.5 Content parity (lokalni build vs. live)
- Live sajt je autoritativan — ima znatno više proizvoda, blog postova i
  silo stranica od staging-a
- WooCommerce migracija: SQL dump metod, `wp_` → `wpGs_` prefix rewrite,
  flat `/proizvod/` permalink struktura
- Slike proizvoda se rade posebno preko rsync `wp-content/uploads/`
- Otvoreno: 5 staging-only proizvoda (durastripe varijante, mosolut-heavy)
  će biti izgubljeno u clean-slate wipe-u osim ako se prvo ne dodaju na live

### 7.6 Core Web Vitals (cilj, nije još urađeno)
Gašenje nepotrebnih modula/skripti u Porto temi i WPBakery-ju, lazy loading,
optimizacija fontova — za LCP/CLS/INP.

---

## 8. 🔴 KRITIČNO — LOKALNI BUILD JE STAGING, LIVE SE NE DIRA!

**PRAVILO:** Svi rad se radi na **LOKALNOM BUILD-u** (`http://localhost/antasline/`) dok se sajt potpuno ne redizajnira. **Live sajt se NE dira** dok se lokalni build ne završi (2026-09-02).

```
LOKALNI BUILD (http://localhost/antasline)
  = Redizajn + testiranje SVE
  = WordPress fajlovi + baza + slike
  = Tehnička, SEO, Ads — sve se testira ovde

LIVE SAJT (antasline.com)
  = PRODUCTION — ČEKANJE
  = Tek posle 2026-09-02 migracija (1 dan!)
  = NE diram bazu, fajlove, domenе, DNS, SSL

VAULT (~/antasline-vault na hosting)
  = Samo dnevnici/planovi sinhronizovani
  = Dokumentacija, NE WordPress fajlovi
```

**Konsekvencu:**
- ✅ Fokus: Kvalitetan lokalni redizajn (Tehnička → SEO → Ads)
- ❌ Nema SSH pristupa za live bazu
- ❌ Nema live promene dok nije sve gotovo
- ❌ WooCommerce migracija je samo na lokalu (test)
- ✅ Posle 2026-09-02: Prebacujemo SVE kao bulk operacija

---

## 9. WORKFLOW I ALATI

### 8.1 Tri-surface Git workflow
- **Lokal** — Claude Code piše u vault, Obsidian Git auto-sync na ~10 min
- **Chat** — Claude u chatu na kraju sesije daje dated `.md` fajl za `dnevnik/`
  koji Miroslav ubacuje u vault
- **cPanel** — pull → rad na produkciji → append `[cpanel-live]` unos →
  commit → push

Tagovi u dnevniku: `[claude-code]` = lokalni terminal, `[chat]` = chat
sesija, `[cpanel-live]` = live produkcija.

### 8.2 Obsidian struktura
- Vault: `C:\Projekti\antasline-vault\`
- [[PROGRESS]] — snapshot trenutnog stanja (**izvor istine za "gde smo
  stali"** — pre svakog zadatka proveriti ovaj fajl)
- [[DNEVNIK-NAPRETKA]] — append-only ledger, `merge=union` u `.gitattributes`
- Dataview plugin je potreban za dashboard upite
- Wikilinks: `[[blokovi/BLOK-A-tracking]]`,
  `[[blokovi/BLOK-B-publike]]`, `[[DNEVNIK-NAPRETKA]]`
- [[dnevnik/ADS-DNEVNIK]]: living hub sa YAML frontmatter, Faze 0–4 checkbox plan,
  RSA asset bank na srpskom za obe kampanje, hard rules/guardrails, append-only
  dated log

### 8.3 Blok organizacija projekta
- **BLOK A** — tracking (zatvoren)
- **BLOK B** — publike (suštinski zatvoren)
- **BLOK C** — redirect mapa (C1) / content parity (C2) / on-page build (C3) —
  aktivan, biraj jedan zadatak po sesiji

### 8.4 Claude Code bash ograničenja
- Komande preko ~965 bajtova → "Command too long for parsing" — koristi
  Write/Edit alate za sadržaj fajla, ili napiši `.sh` fajl pa `bash script.sh`
- Velike fajlove čitaj preko Read alata po putanji, ne `cat`/pipe
- Brace expansion `{a,b}` pravi **literalne** foldere umesto ekspanzije —
  koristi `for` petlju

### 8.5 Analiza pre implementacije
Claude analizira i predlaže opcije → Miroslav odobrava → Claude Code izvršava
lokalno. Ne izvršavati destruktivne/nepovratne izmene na bazi bez prethodnog
backup-a i bez odobrenja.

---

## 9. KLJUČNE LEKCIJE (da se ne ponavljaju greške)

**Tracking:**
- Svaki GTM consent update handler mora slati eksplicitne vrednosti za sve
  4 kategorije — prazan `gtag('consent','update',{})` ne preklapa prethodno
  granted stanje; GTM Preview pokazuje "-" u On-page Update koloni ako je update prazan
- Ugašeni WP pluginovi ne izvršavaju PHP — ako baner ostane posle
  deaktivacije, grep-uj tekst banera, ne ime plugina
- GTM ručno pisani container JSON import puca sa "Error deserializing enum
  type [EventType]" na ovom kontejneru — ne pokušavati ponovo. Jedini pouzdani
  putevi: (A) ručno kreiranje u GTM UI, ili (B) Export kontejnera pa merge u
  tačnom formatu

**Windsor.ai / Google Ads dijagnostika:**
- ECOTILE kolaps isporuke (visok impression share + sitni apsolutni
  impressions + skok CPC) = throttling na nivou naloga (balans/verifikacija),
  ne pad tražnje na tržištu
- Windsor.ai vraća prazne podatke za throttled kampanje — proveri
  spend+impressions pre nego što pretpostaviš grešku konektora
- Windsor.ai je read-only prema GTM/GA4 — potvrđuje da eventi stižu, ali ne
  može da menja tagove/triggere/key event podešavanja
- Windsor.ai ne izlaže GA4 audience membership size direktno — koristi
  `active_users` segmentiran po `audience_name` kao proxy; prazne publike se
  jednostavno ne pojavljuju u rezultatima
- Conversion action segmentacija vraća samo akcije sa bar jednom konverzijom
  u traženom periodu — nove akcije sa nula konverzija se neće pojaviti
- GA4 `in`-operator filter je nepouzdan — povuci sve evente nefiltrirano i
  agregiraj u Python-u
- Week-over-week: koristi eksplicitne `date_from`/`date_to` (`YYYY-MM-DD`)
  za prethodni period, ne presets
- `/hvala-za-poruku/` conversion proxy: filter `[["page_path", "contains",
  "hvala"]]` na `screen_page_views`

**SEO:**
- Content parity je bitniji od 301 redirekcija samih po sebi — title/meta,
  H1/H2 struktura, broj reči, pokrivenost ključnih reči, interni linkovi,
  schema, indexability direktive — sve mora da se proveri stranica po stranicu
- Silo SEO benefit dolazi od internog linkovanja i breadcrumb schema-e, ne
  od kategorije-u-URL strukture
- `/sportske-podloge/kosarkaske-konstrukcije/` — visok organski saobraćaj
  (478 GSC klikova) bez potvrđenog redirect cilja — visok prioritet

**Telefon:**
- Broj 072 dominira klikovima na telefon (~50 vs ~7 za 074); ~46/50 klikova
  sa mobilnog → prioritet 072 u ad asset-ima i on-page CTA-ovima
- `mailto` sa pre-populate `?subject=` postoji na bar jednoj stranici
  proizvoda — vredi proširiti na ostale

---

## 10. FORMAT IZVEŠTAVANJA I KOMUNIKACIJA

- **Jezik: srpski, ekavica** (ili engleski, po potrebi razgovora)
- Kratko, skenabilno, tabele, brojevi. **Bez uvoda i zaključka.**
- Performanse (7 dana vs prethodnih 7): GA4 (korisnici, sesije,
  `generate_lead`, `phone_click`/`tel`, `email_click`/`mailto`) i Ads
  (potrošnja, klikovi, CTR, CPC, uvezene konverzije). Na kraju sekcije: ukupan
  broj pravih konverzija do sada.
- SEO (GSC poslednjih 28 dana): top upiti po prikazima sa niskim CTR-om na
  pozicijama 5–15.
- Na samom kraju izveštaja: jedna rečenica — **"Akcija nedelje: [predlog]"**.
- Ako konektor ne vrati podatke → napiši "Nema podataka za [izvor]" —
  **nikad ne izmišljati brojeve**.
- Promene ispod 5% = stabilno stanje, ne trend.
- Sve vrednosti iz Google Ads-a u RSD.
- Objasni novi žargon (npr. RSA — Responsive Search Ads) kad se prvi put pojavi.
- **Merena "manja" konverzija posle BLOK A čišćenja (Consent Mode + MI
  gašenje + key event cleanup) = tačnije merenje, ne pad performansi. Ne
  reagovati promenom budžeta.**

---

## 11. ULOGE (u zavisnosti od zadatka)

- **E-commerce menadžer / UX/UI** — optimizacija levka (one-page checkout,
  trust badges, tekstualni wireframe modeli)
- **SEO i tehnički konsultant** — čišćenje koda, migracija, schema, terminal
  automatizacija
- **Copywriter** — SEO sadržaj u skladu sa strategijom (fokus Ecotile
  konverzija + tehničke specifikacije)
- **B2B komunikacija** — cold email-ovi za domaće distributere (XML feed,
  rabati, odloženo plaćanje)

---

## 12. GDE PROVERITI TRENUTNO STANJE

Ovaj fajl nosi *pravila ponašanja i istorijski kontekst* — ne menja se često.
Za **"gde smo stali danas"** uvek prvo pogledaj:
1. `[[2026-07-02-MASTER-PLAN-DO-LIVE]]` — 2-mesečni plan (Tehnička → SEO → Ads)
2. `[[PROGRESS]]` u vault-u — snapshot trenutnog stanja
3. `[[DNEVNIK-NAPRETKA]]` (append-only ledger, poslednji unosi)
4. Aktivni BLOK C pod-zadatak: `[[blokovi/BLOK-C-sledece]]` (C1 redirect / C2 content / C3 on-page)

---

## 13. KOMPLETAN HUB SVIH FAJLOVA (Wikilinks za navigaciju)

### 📋 OSNOVNO — Pročitaj prvo
- `[[00-INDEX]]` — Dashboard (Dataview tabele)
- `[[CLAUDE]]` — Ovo (instrukcije + kontekst)
- `[[PROGRESS]]` — Trenutno stanje
- `[[2026-07-02-MASTER-PLAN-DO-LIVE]]` — 2-mesečni plan do live-a

### 📖 HRONOLOGIJA — Šta je urađeno po datumima
- `[[DNEVNIK-NAPRETKA]]` — Append-only ledger (svaka sesija)
- `[[dnevnik/2026-07-02-analiza-segmentacije]]` — GA4 publike + Ads strategija
- `[[dnevnik/2026-07-02-gsc-keywords-analiza]]` — 60 GSC queries + 4 kritična prioriteta
- `[[dnevnik/2026-07-02-basket-page-faq-schema]]` — FAQ + schema za basketball
- `[[dnevnik/2026-06-28-postavljanje-vault]]` — Vault setup + GitHub most
- `[[dnevnik/2026-06-28-db-backup-woo]]` — WooCommerce backup
- `[[dnevnik/2026-06-28-woo-transfer-attempt]]` — WooCommerce migracija (čeka SSH)
- `[[dnevnik/ADS-DNEVNIK]]` — Living hub za Google Ads, RSA banka, Faze 0-4

### 🧱 BLOK ORGANIZACIJA — Rad po prioritetima
- `[[blokovi/BLOK-A-tracking]]` — ✅ ZATVOREN (GTM v10, Consent, key events)
- `[[blokovi/BLOK-B-publike]]` — ✅ ZATVOREN (6 GA4 publika)
- `[[blokovi/BLOK-C-sledece]]` — ⏳ AKTIVNO (C1 redirect / C2 content / C3 on-page)

### 🎯 STRATEGIJA I ODLUKE
- `[[odluke/_pregled-odluka]]` — Sve donete odluke + zašto
- `[[reference/identifikatori]]` — Google Ads/GA4/GSC/GTM ID-evi
- `[[reference/naucene-lekcije]]` — Tehnički gotchas (GTM, Windsor, SEO, telefon)

### 📚 DOKUMENTACIJA
- `[[briefs/_README]]` — (ako postoji brief za kampanje)
- `[[seo/_README]]` — SEO strategija
- `[[CLAUDE-CODE-instrukcija]]` — Instrukcije za Claude Code rad
- `[[CLAUDE-CODE-instrukcija-CPANEL]]` — cPanel live rad instrukcije

### 🔗 VAŽNI ESTERNI LINKOVI (U tekstu)
- Live sajt: https://www.antasline.com
- Lokalni build: http://localhost/antasline
- Google Ads nalog: 156-886-0314 (vidi `[[reference/identifikatori]]`)
- GA4: 292720335
- GTM: GTM-TRDT8K9
- GSC: sc-domain:antasline.com
- GitHub vault: github.com/Chichabudhha/antasline-vault (privatan)

---

## 14. TRENUTNI STATUS NA JEDNOJ STRANICI (2026-07-02)

### 🎯 Konačan cilj
Redizajn sajta + live deployment (do 2026-09-02) za maksimalnu prodaju sa minimalnim budžetom.  
Prioritet: **Tehnička → SEO → Ads**

### ✅ Gotovo
- BLOK A (Tracking) + BLOK B (Publike)
- Lokalni WordPress build
- Vault organizacija
- 8 nedeljnih dnevnika sa planovima

### ⏳ U toku
- BLOK C (Redirect mapa, Content parity, On-page)
- 4 nove landing stranice (iz GSC keywords)
- FAQ + schema za basketball

### 🔴 Kritični blokeri (Čeka Miroslava)
1. ✅ ~~SSH config — WooCommerce migracija~~ — import na lokal urađen 2026-07-04 (bez SSH, export/import). SSH ostaje samo za live prebacivanje.
2. ✅ ~~Google Ads balans + verifikacija~~ — odblokirano 2026-07-04; ECOTILE više nije throttlovan.
3. GA4 publike (Task #1) — Miroslav kreira u GA4 UI
4. Konverzije info — Šta se dešava sa 53 kontakta?

### 📊 Metriken (juni 2026)
- 53 kontakta/mesec (`/hvala-za-poruku/`)
- 94% organski, 6% plaćeni
- Conversion rate: 0,88% (NISKO)
- Ads spend: 40k RSD/mesec

### 📅 Nedelja 1 (do 2026-07-16)
- [ ] Tehnička audit (PageSpeed, Web Vitals)
- [x] WooCommerce import na lokal (gotovo 2026-07-04)
- [ ] C1 verifikacija (20 redova redirect mape)
- [ ] 4 nove GSC stranice (start)
- [ ] Task #1 — GA4 publike (Miroslav)

---

## 15. ZA CLAUDE-A SLEDEĆI PUT

Kada otvorim CLAUDE.md sledeći put, znaću:

1. ✅ **Ko sam** — marketing analitičar za AntasLine
2. ✅ **Šta radim** — redizajn + live migracija (2 meseca) + SEO/Ads optimizacija
3. ✅ **Šta je gotovo** — BLOK A (tracking), BLOK B (publike), lokalni build
4. ✅ **Šta je u toku** — BLOK C (redirect, content, on-page)
5. ✅ **Šta je blokirano** — GA4 publike (Task #1) + konverzije info (SSH i balans/verifikacija rešeni 2026-07-04)
6. ✅ **Gde su fajlovi** — Sve su linked-ovane kroz wikilinks
7. ✅ **Prioritet** — Tehnička → SEO → Ads
8. ✅ **Timeline** — 2 meseca (do 2026-09-02)
9. ✅ **Šta trebam od Miroslava** — 5 konkretnih odgovora
10. ✅ **Šta radim sad** — [[2026-07-02-MASTER-PLAN-DO-LIVE]] (Faza 0-3)
