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
| DB (lokalno) | MariaDB 10.4, prefiks **`wpgs_` (sve malim slovima)**, 78 tabela, uvezena iz `smartas_smartas_rs.sql` (46.6 MB), kolacija `utf8mb4_unicode_ci` |
| Stack (lokalno) | PHP 8.2.12, XAMPP, WoodMart 8.5.4 tema + child (design sistem `antas-design.css`, self-hosted Inter+Bebas) — napušten raniji Porto+WPBakery pristup |

> 🔴 **PREFIKS BAZE — `wpgs_`, ne `wpGs_` (ispravljeno 2026-08-12, provereno protiv baze).**
> `SHOW TABLES` na lokalu vraća `wpgs_posts`; isto stoji u live/staging dump-ovima.
> ✅ **Od 2026-08-14 i lokalni `wp-config.php` nosi `$table_prefix = 'wpgs_'`** — pre toga
> je stajalo `wpGs_` i „radilo" samo zato što je MariaDB na Windows-u
> `lower_case_table_names=1` (provereno), pa ne razlikuje velika i mala slova.
> **Na Linux hostingu (cPanel/staging) razlikuje** — to je tačan uzrok
> „site not installed" greške pri probi migracije 2026-07-21.
> **Pravilo:** svaka skripta, `sed`, `wp search-replace` i `wp-config` piše **`wpgs_`**.
> Pogrešan case ne prijavi grešku — tiho preskoči zamenu ili uveze u pogrešne tabele.
>
> 🔴 **Prefiks nije samo ime tabele.** WordPress od njega izvodi i **ključeve koji se
> čuvaju kao stringovi**: `<prefiks>capabilities` i `<prefiks>user_level` u `usermeta`,
> `<prefiks>user_roles` u `options`, plus `user-settings`, `dashboard_*`, `persisted_preferences`.
> SQL bi ih našao (kolacija `utf8mb4_general_ci` je case-neosetljiva), **ali WP meta keš je
> PHP niz, a ključevi PHP nizova jesu case-osetljivi** → `isset($cache['wpgs_capabilities'])`
> promašuje sačuvano `wpGs_capabilities` i **svi korisnici ostaju bez ijedne dozvole**
> (zaključan wp-admin). Zato je 14.08 uz `wp-config` preimenovano i **16 redova** u bazi.
> Ako se prefiks ikad opet menja — menjaju se **oba**, i uvek uz backup.

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

**Konektor za izveštavanje (od 2026-07-27, sopstveni — zamenio Windsor.ai,
koji je pre toga zamenio Supermetrics):** Windsor.ai je istekao 2026-07-27
(otkazan 2026-07-21). Zamena je `.claude/skills/antasline-konektor/` —
direktni pozivi GA4 Data API / Search Console API / Google Ads API /
Business Profile Performance API, bez trećih učesnika, kredencijali van
vault-a. Isti nalozi kao ranije:
- GA4 → property `292720335`
- Google Ads → nalog `156-886-0314`
- GSC → `sc-domain:antasline.com`

Setup: [[reference/api-konektor-setup.md]] · korišćenje: [[reference/identifikatori]]

Google My Business stranica: "Industrijski podovi AntasLine"

---

## 4. PRAĆENJE / KONVERZIJE — TRENUTNO STANJE (stvarno implementirano)

**BLOK A (tracking) je zatvoren.** GTM verzija 10 je objavljena:

- Consent Mode v2 (mu-plugin `al-tracking-gtm-consent.php`, banner sa kolačićem
  `antasline_consent`) — **default GRANTED za sve 4 kategorije** dok korisnik
  ne klikne (potvrđeno direktno iz live koda 2026-07-22, ranija napomena
  "default DENIED" ovde je bila netačna/pretpostavljena, nikad verifikovana
  protiv stvarnog koda). Skripta postavlja kolačić na potpuno odobreno ČIM se
  banner prikaže, pre bilo kakve korisnikove akcije — otvoreno pitanje da li je
  ovo namerno ili compliance bag, videti [[PROGRESS]] Blokeri
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

### 4.1 Napredniji eventi — stanje potvrđeno direktno u GTM-u 2026-07-22

Pet eventa ožičeno u GTM-TRDT8K9: `view_product_category` i `epoxy_conquest_engagement`
i `lead_form_start` su LIVE (potvrđeno u UI); `pdf_download` i `gallery_view` su DRAFT
u Workspace, čekaju Submit. Enhanced Conversions (SHA-256 email/telefon hash) nije još
implementirano. Pun detalj (trigger konfiguracija, parametri, gotcha istorija):
[[reference/gtm-eventi-implementacija]]

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

Puna lista: [[reference/negativne-kljucne-reci]]

> Stanje potvrđeno u Ads UI 2026-07-06 (M2 zatvoren): lista primenjena na obe
> kampanje, dodato 13 negativnih koje su falile, pauzirani KW `bastenski
> namestaj` i `oprema za bazene` u kampanji Terase. `plocice` namerno kao
> phrase varijante (ne broad) — broad bi blokirao "pvc pločice" upite iz
> ponude. Detalji: [[dnevnik/ADS-DNEVNIK]].

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

### 7.1 SEO plugin — Rank Math (migrirano 2026-08-05)

**Lokalni build je prešao sa Yoast na Rank Math** (`rank_math_title` /
`rank_math_description` meta ključevi, zamenili `_yoast_wpseo_title` /
`_yoast_wpseo_metadesc`). Backup pre migracije:
`antasline-backups/antasline_local_2026-08-05_pre-rankmath-migration.sql`.

> 🔴 **M odluka 2026-08-13: Yoast je van upotrebe, ne vraća se.** Rank Math je
> jedini SEO plugin projekta — nova pravila: pisati isključivo u `rank_math_*`
> ključeve, verifikovati Rank Math izlaz u `<head>`, ne predlagati povratak na
> Yoast. `_yoast_wpseo_*` postmeta ostaje u bazi (690 redova, povratak moguć
> raspakivanjem arhive ako ikad zatreba) — plugin fajlovi su obrisani.

Uvoz je urađen programski preko Rank Math-ove Yoast importer klase (wp-cli
eval-file), sa 3 gotcha-a (protected-meta gate prazni upis, registration-skip
za front-end izlaz, ručni upis Local SEO/NAP podataka). Verifikovano: title/meta
na 10 kategorija + homepage + kontakt + conquest članak identični originalima,
JSON-LD bez dupliranja. Pun detalj i sve tri popravke: [[reference/rankmath-migracija-detalji]]

### 7.2 Struktura i konvencije (lokalni build)
- WooCommerce URL-ovi (parity sa live, od 2026-07-07): `/proizvod/` (flat) i
  `/kategorija-proizvoda/` — **ne** `/shop/` niti `/kategorija/`
- Blog arhiva: `/aktuelnosti/` OSTAJE (kao na live) — lokalni `/blog/` se
  preimenuje (parity odluka 2026-07-07, obrnuto od ranijeg plana)
- WoodMart tema (od jula 2026, zamenila Porto): renderuje `post_title` kao
  pravi `<h1>` na SVAKOM CPT/stranici/postu po default-u — obrnuto od stare
  Porto konvencije (koja je koristila `<h2 class="entry-title">` za postove).
  Svaka nova stranica MORA dobiti `_woodmart_title_off=on` postmeta ako
  sadržaj već ima svoj H1, inače nastaje 2×H1 duplikat — standardni HTTP/H1
  verifikacioni korak ovo hvata. Detalji: `[[migracija/woodmart-sabloni]]`

### 7.3 WPBakery — poznati problemi (istorijski, tema je od jula 2026 WoodMart)

Tema je prešla na WoodMart 8.5.4 + child (§2, `[[migracija/woodmart-sabloni]]`
nosi trenutne gotcha-e). Ako reimportovan post (F3, pun reimport sa live-a) i
dalje nosi stari WPBakery shortcode markup u `post_content` — pravila i JS
gotcha-i: [[reference/wpbakery-legacy-gotchas]]

### 7.4 Parity strategija (od 2026-07-07 — zamenila staru redirect mapu)
- **Build se pravi 1:1 prema live sajtu** (URL + content parity); redirect mapa
  se svodi na ~10–20 namernih promena. Izvor istine: `[[migracija/PARITY-PLAN]]`,
  izvršenje kroz promptove `[[migracija/promptovi/_README]]` (faze F1–F7).
- Stara mapa (118 redova, `/shop/` targeti, AUTO-PREDLOG redovi) je arhivirana u
  `migracija/arhiva/` — **ne koristiti je**.
- Slug politika: hibrid po težini — top ~15 GSC URL-ova strogi parity; nisko-
  saobraćajni smeju bolji slug uz 301; konsolidacije duplikata uvek OK.
- **Kritična rupa i dalje:** `/sportske-podloge/kosarkaske-konstrukcije/`
  (478 GSC klikova) → prava landing stranica (ne 301 na shop kategoriju), deo F5.
- `.htaccess` 301 se generiše (F4) ali aktivira TEK na dan migracije.

### 7.5 Content parity (lokalni build vs. live)
- Live sajt je autoritativan — ima znatno više proizvoda, blog postova i
  silo stranica od staging-a
- WooCommerce migracija: SQL dump metod, `wp_` → `wpgs_` prefix rewrite (malim slovima — v. §2),
  flat `/proizvod/` permalink struktura
- Slike proizvoda se rade posebno preko rsync `wp-content/uploads/`
- Otvoreno: 5 staging-only proizvoda (durastripe varijante, mosolut-heavy)
  će biti izgubljeno u clean-slate wipe-u osim ako se prvo ne dodaju na live

### 7.6 Core Web Vitals — status: CLS/TBT zatvoreni, LCP čeka produkciju
CLS <0,1 pogođen 2026-07-12 (font-preload fix), TBT/INP proxy zatvoren
2026-07-22 (dead JS dequeue). LCP <2,5s ostaje crveno — blokirano na
render-blocking CSS (`js_composer` 437KB), namerno odloženo na LiteSpeed
Critical CSS/UCSS na produkciji, nema više nizak-rizik lokalnih koraka.
Detalji: `[[dnevnik/PERFORMANCE-AUDIT]]`.

---

## 8. 🔴 KRITIČNO — LOKALNI BUILD JE STAGING, LIVE SE NE DIRA!

**PRAVILO:** Svi rad se radi na **LOKALNOM BUILD-u** (`http://localhost/antasline/`) dok se sajt potpuno ne redizajnira. **Live sajt se NE dira** dok se lokalni build ne završi (migracija **2026-09-08**, UTO, vidi [[2026-07-06-MASTER-PLAN-V2]] — M pomerio +14 dana 2026-08-20 sa 25.08 na 08.09, razlog nezabeležen; pre toga nedelju ranije 2026-08-10 na 24.08, pa dan kasnije 2026-08-17 na 25.08; raniji datumi 2026-08-24, 2026-08-31 i 2026-09-02 su zastareli). Content freeze **2026-09-03** (🔴 pomeren istim korakom sa 20.08, M odluka 20.08 — posle njega obavezan ponovni regression sweep i nov backup builda), gate pregled **2026-09-04**, **PON 07.09 = rezervni radni dan**.

```
LOKALNI BUILD (http://localhost/antasline)
  = Redizajn + testiranje SVE
  = WordPress fajlovi + baza + slike
  = Tehnička, SEO, Ads — sve se testira ovde

LIVE SAJT (antasline.com)
  = PRODUCTION — ČEKANJE
  = Tek posle 2026-09-08 migracija (1 dan!)
  = NE diram bazu, fajlove, domenе, DNS, SSL

VAULT (~/antasline-vault na hosting)
  = Samo dnevnici/planovi sinhronizovani
  = Dokumentacija, NE WordPress fajlovi
```

**Konsekvencu:**
- ✅ Fokus: Kvalitetan lokalni redizajn (Tehnička → SEO → Ads)
- ⚠️ SSH/cPanel pristup za live je potvrđen od 2026-07-21 (M6) — koristi se ISKLJUČIVO za eksplicitne `[cpanel-live]` zadatke (npr. bezbednosni incidenti, staging proba migracije), ne za redovan redizajn rad
- ❌ Nema live promene dok nije sve gotovo, osim eksplicitnih `[cpanel-live]` zadataka
- ❌ WooCommerce migracija je samo na lokalu (test)
- ✅ Posle 2026-09-08: Prebacujemo SVE kao bulk operacija

---

## 9. WORKFLOW I ALATI

> Podsekcije su do 2026-08-18 bile numerisane **8.1–8.7** (naslov je govorio 9,
> podnaslovi 8) — ispravljeno u 9.1–9.7. Puna mapa starih brojeva: §10 ispod.

### 9.1 Tri-surface Git workflow
- **Lokal** — Claude Code piše u vault, Obsidian Git auto-sync na ~10 min
- **Chat** — Claude u chatu na kraju sesije daje dated `.md` fajl za `dnevnik/`
  koji Miroslav ubacuje u vault
- **cPanel** — pull → rad na produkciji → `[cpanel-live]` unos **NA VRH**
  ledgera (ne append na dno — v. §9.2) → commit → push

Tagovi u dnevniku: `[claude-code]` = lokalni terminal, `[chat]` = chat
sesija, `[cpanel-live]` = live produkcija.

### 9.2 Obsidian struktura
- Vault: `C:\Projekti\antasline-vault\`
- [[PROGRESS]] — snapshot trenutnog stanja (**izvor istine za "gde smo
  stali"** — pre svakog zadatka proveriti ovaj fajl)
- [[DNEVNIK-NAPRETKA]] — append-only ledger, **newest-on-top**, `merge=union`
  u `.gitattributes`. Nov unos ide **NA VRH** iz svih tri površine — unos
  koji završi na dnu je praktično nevidljiv (13.08.2026 tako propušten iz
  PROGRESS tabele). Rotira se sa `python skripte/rotiraj-dnevnik.py` kad
  pređe ~40 KB; starije ide doslovno u `dnevnik/arhiva-dnevnik-YYYY-MM.md`.
  🔴 **Arhive se NE čitaju `Read`-om** (290–620 KB po fajlu) — samo `grep`.
- Dataview plugin je potreban za dashboard upite
- Wikilinks: `[[blokovi/BLOK-A-tracking]]`,
  `[[blokovi/BLOK-B-publike]]`, `[[DNEVNIK-NAPRETKA]]`
- [[dnevnik/ADS-DNEVNIK]]: living hub sa YAML frontmatter, Faze 0–4 checkbox plan,
  RSA asset bank na srpskom za obe kampanje, hard rules/guardrails, append-only
  dated log

### 9.3 Blok organizacija projekta
- **BLOK A** — tracking (zatvoren)
- **BLOK B** — publike (suštinski zatvoren)
- **BLOK C** — redirect mapa (C1) / content parity (C2) / on-page build (C3) —
  aktivan, biraj jedan zadatak po sesiji

### 9.4 Claude Code bash ograničenja
- Komande preko ~965 bajtova → "Command too long for parsing" — koristi
  Write/Edit alate za sadržaj fajla, ili napiši `.sh` fajl pa `bash script.sh`
- Velike fajlove čitaj preko Read alata po putanji, ne `cat`/pipe
- Brace expansion `{a,b}` pravi **literalne** foldere umesto ekspanzije —
  koristi `for` petlju

### 9.5 Analiza pre implementacije
Claude analizira i predlaže opcije → Miroslav odobrava → Claude Code izvršava
lokalno. Ne izvršavati destruktivne/nepovratne izmene na bazi bez prethodnog
backup-a i bez odobrenja.

### 9.6 Token usage tracking
Log: `Token Logs/.token_log.jsonl` (vault root, append-only, JSONL). Posle
svake logičke akcije u sesiji ispiši na konzolu `✓ {akcija} | +Xk tokens |
Session: Yk` i append-uj log unos. Brojevi dolaze iz stvarnog usage polja u
Claude Code transkriptu sesije (`~/.claude/projects/<slug>/<session-id>.jsonl`),
ne iz procene. Preko 150k u sesiji → predloži `/clear`. Ne čitati log fajl
tokom rada osim na eksplicitan zahtev. Detalji i formula: [[reference/token-tracking]].

### 9.7 Design skillovi — uključeni 2026-08-06, koristi automatski kad treba dizajn
Ranije (od 2026-08-05) su ovi skillovi bili isključeni po defaultu (`/doctor`
čišćenje, nula poziva u 50 skeniranih sesija) i trebalo je pitati Miroslava
pre uključivanja. **Posle sesije doterivanja meni ikonica (2026-08-06,
mnogo rundi ručnog SVG rada) Miroslav je eksplicitno tražio da se svi trajno
uključe** — `skillOverrides` "off" unosi uklonjeni iz `~/.claude/settings.json`,
`frontend-design@claude-plugins-official` postavljen na `true` u
`.claude/settings.local.json`. **Od sada: kad zadatak liči na bilo šta od
opisanog ispod (dizajn, ikonice, baneri, UI, brend, prezentacije), koristi
dotični skill direktno — ne pitaj za dozvolu, ne pretpostavljaj da je i
dalje isključen.**

- **`magic` (MCP server)** — `@21st-dev/magic`, AI generator UI komponenti
  iz opisa/screenshot-a. Kad: generisanje/pretraga gotove UI komponente
  (frontend build, novi WoodMart blok, vizuelni prototip). Napomena: ovaj
  MCP server nema lokalni `.mcp.json` u vault-u — ako se ne pojavljuje u
  alatima, Miroslav treba jednom da pokrene `/mcp enable magic` ručno
  (slash komanda, Claude Code je ne može izvršiti sam).
- **`design` (globalni skill)** — brand identity, design token-i, UI
  styling, generisanje loga (55 stilova), CIP (50 deliverable-a), HTML
  prezentacije, banner/icon dizajn, social foto. Kad: sveobuhvatan
  brend/dizajn zadatak (npr. rad na brend knjizi, logu, CIP-u, ikonicama
  za meni/UI).
- **`ui-ux-pro-max` (globalni skill)** — UI/UX intelligence: 67 stilova,
  161 paleta, 57 font-parova, 25 chart tipova, 21 stack (React, Vue,
  Tailwind, shadcn/ui...). Kad: build/review/fix UI koda, izbor
  palete/tipografije/layout-a za WoodMart rebuild stranice.
- **`banner-design` (globalni skill)** — baneri za social/ads/web
  hero/print, više art-direction opcija. Kad: W6 social/Ads kreativa.
- **`brand` (globalni skill)** — brand voice, vizuelni identitet,
  messaging framework, konzistentnost brenda. Kad: pitanja tona/glasa
  brenda, brend usklađenost sadržaja.
- **`design-system` (globalni skill)** — arhitektura design token-a,
  specifikacije komponenti, generisanje slajdova. Kad: sistematizacija
  dizajna preko više stranica/komponenti.
- **`slides` (globalni skill)** — strateške HTML prezentacije sa
  Chart.js, copywriting formule. Kad: Miroslav traži prezentaciju/izveštaj
  u slide formatu.
- **`ui-styling` (globalni skill)** — shadcn/ui, Tailwind CSS,
  canvas-based dizajn, dark mode, teme. Kad: implementacija UI komponenti
  sa shadcn/Tailwind stack-om.
- **`frontend-design` (plugin skill, `frontend-design@claude-plugins-official`)**
  — vođenje ka nešablonskom, autorskom vizuelnom pravcu (paleta,
  tipografija, layout) pri gradnji nove ili preoblikovanju postojeće UI.
  Kad: potreban je izražen estetski pravac, ne generički template.

---

## 10. KLJUČNE LEKCIJE (da se ne ponavljaju greške)

Kurirane lekcije po domenu (Tracking · Konektor/Ads dijagnostika · SEO ·
Telefon) + istorijska mapa renumeracije sekcija (staro §8.x/§9 → novo
§9.x/§10–16, ako naiđeš na „§9" u starijoj belešci): [[reference/kljucne-lekcije-projekat]]

🔴 Dva pravila odavde vredi ponoviti jer se stalno koriste: **telefon je uvek
`069`** (linija 72 = `069 234 00 72`, linija 74 = `069 234 00 74` — „072"/„074"
su poslednje dve cifre, ne prefiks) i **GTM ručni JSON import na ovom
kontejneru puca** — izmene idu kroz UI ili export/merge, ne import.

---

## 11. FORMAT IZVEŠTAVANJA I KOMUNIKACIJA

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

## 12. ULOGE (u zavisnosti od zadatka)

- **E-commerce menadžer / UX/UI** — optimizacija levka (one-page checkout,
  trust badges, tekstualni wireframe modeli)
- **SEO i tehnički konsultant** — čišćenje koda, migracija, schema, terminal
  automatizacija
- **Copywriter** — SEO sadržaj u skladu sa strategijom (fokus Ecotile
  konverzija + tehničke specifikacije)
- **B2B komunikacija** — cold email-ovi za domaće distributere (XML feed,
  rabati, odloženo plaćanje)

---

## 13. GDE PROVERITI TRENUTNO STANJE

Ovaj fajl nosi *pravila ponašanja i istorijski kontekst* — ne menja se često.
Za **"gde smo stali danas"** uvek prvo pogledaj:
1. `[[2026-07-06-MASTER-PLAN-V2]]` — master plan do live-a (5 workstream-ova, N1–N8'' raspored, gate kriterijumi; **go-live 2026-09-08**, pomereno 2026-08-10 pa 2026-08-17 pa 2026-08-20)
2. `[[PROGRESS]]` u vault-u — snapshot trenutnog stanja
3. `[[DNEVNIK-NAPRETKA]]` (append-only ledger, poslednji unosi)
4. Migracija/parity: `[[migracija/PARITY-PLAN]]` + status faza F1–F7 u `[[migracija/promptovi/_README]]`
5. Aktivni BLOK C pod-zadatak: `[[blokovi/BLOK-C-sledece]]` (C3 on-page; C1/C2 zamenjeni parity fazama)

---

## 14. KOMPLETAN HUB SVIH FAJLOVA (Wikilinks za navigaciju)

**Pročitaj prvo:** `[[00-INDEX]]` (Dashboard) · `[[CLAUDE]]` (ovo) · `[[PROGRESS]]`
(trenutno stanje) · `[[2026-07-06-MASTER-PLAN-V2]]` (master plan, aktivan) ·
`[[DNEVNIK-NAPRETKA]]` (append-only ledger).

**Strategija/odluke:** `[[odluke/_pregled-odluka]]` · `[[reference/identifikatori]]` ·
`[[reference/naucene-lekcije]]` (tehnički gotchas) · `[[reference/cenovnik]]` (M10) ·
`[[reference/claude-skilovi]]`.

Pun spisak (hronologija po datumu, blok organizacija, brend/design reference,
dokumentacija, spoljni linkovi): [[reference/hub-navigacija]]

---

## 15. ⛔ ISTORIJSKI SNAPSHOT (2026-07-02) — SUPERSEDED, ne koristiti za "gde smo stali"

Arhivirano u [[dnevnik/2026-07-02-arhiva-snapshot]] (izmešteno tokom `/doctor`
čišćenja 2026-08-05). Za trenutno stanje uvek koristi §13 (redom:
[[2026-07-06-MASTER-PLAN-V2]] → [[PROGRESS]] → [[DNEVNIK-NAPRETKA]]).

---

## 16. ZA CLAUDE-A SLEDEĆI PUT

Kada otvorim CLAUDE.md sledeći put, znaću:

1. ✅ **Ko sam** — marketing analitičar za AntasLine
2. ✅ **Šta radim** — redizajn (WoodMart tema) + live migracija + SEO/Ads optimizacija
3. ✅ **Šta je gotovo** — BLOK A (tracking), BLOK B (publike), ceo W1 (rebuild 1.1–1.12), W2 content plan (20 stranica)
4. ✅ **Šta je u toku** — W3 (CWV/migracija priprema), W5 (nedeljni izveštaji), povremeni `[cpanel-live]` zadaci
5. ✅ **Šta je blokirano** — sve #ceka-miroslav stavke iz sekcije 4 ovog master plana (npr. Ads reaktivacija posle godišnjeg, cenovnik M10, live GEO fix preko cPanel)
6. ✅ **Gde su fajlovi** — Sve su linked-ovane kroz wikilinks
7. ✅ **Prioritet** — Tehnička → SEO → Ads
8. ✅ **Timeline** — go-live **2026-09-08** (UTO, pomereno +14 dana 2026-08-20 sa 25.08) · content freeze **03.09** · gate pregled 04.09 · rezerva 05–07.09 (vidi [[2026-07-06-MASTER-PLAN-V2]] §2)
9. ✅ **Šta trebam od Miroslava** — vidi [[2026-07-06-MASTER-PLAN-V2]] §4 (zavisnosti)
10. ✅ **Šta radim sad** — [[PROGRESS]] (dnevni snapshot) + [[2026-07-06-MASTER-PLAN-V2]] (workstream-ovi W1–W5, nedelje N1–N7')
