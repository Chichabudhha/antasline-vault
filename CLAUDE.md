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
> `SHOW TABLES` na lokalu vraća `wpgs_posts`; isto stoji i u live/staging dump-ovima.
> Lokalni `wp-config.php` **ipak nosi `$table_prefix = 'wpGs_'`** i radi — ali samo zato
> što je MariaDB na Windows-u `lower_case_table_names=1` (provereno), pa ne razlikuje
> velika i mala slova. **Na Linux hostingu (cPanel/staging) razlikuje** — to je tačan
> uzrok „site not installed" greške pri probi migracije 2026-07-21.
> **Pravilo:** svaka skripta, `sed`, `wp search-replace` i `wp-config` za server piše
> **`wpgs_`**. Pogrešan case ne prijavi grešku — tiho preskoči zamenu ili uveze u
> pogrešne tabele.

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

**✅ Već implementirano i ožičeno u GTM-TRDT8K9 (potvrđeno direktno u UI, ne
pretpostavka):**
- `view_product_category` — GA4 Event tag, trigger "Window Loaded" (Page Path
  regex na ecotile/esd-pod/antistatik/industrijski-podovi/sportsk/tenis/padel/
  pickleball/odbojk/kosark/basket/3x3/bergo). Parametar `category_name` preko
  `{{RT - category_name}}` promenljive. Koristi se u publici "High-Intent B2B
  Bidders" (sek. 5) — više NIJE pretpostavka, potvrđeno da tag/trigger stvarno
  postoji.
- `epoxy_conquest_engagement` — GA4 Event tag, trigger Scroll Depth OR Timer
  (Timer: interval 30000ms, **Limit 1**, Page Path contains
  `/epoksidni-podovi-ili-ecotile-podovi`). Limit 1 potvrđuje "fires samo jednom"
  pravilo — filteri i dalje `count ≥ 1`, nikad `> 1`.
- `lead_form_start` — GA4 Event tag, trigger "Custom Event trigger" (Custom
  HTML tag "Lead Form Start" na All Pages šalje custom event u dataLayer).

**🆕 Dodato 2026-07-22 (DRAFT u Workspace, NIJE Submit-ovano — čeka odobrenje):**
- `pdf_download` — trigger "Klik na PDF" (Just Links, Click URL contains
  `.pdf`) + GA4 Event tag sa parametrima `link_url={{Click URL}}` i
  `link_text={{Click Text}}`. Pokriva sve postojeće PDF linkove (tehnički
  listovi, sertifikati) na proizvod-stranicama, potvrđeno curl-om na Ecotile
  E500/7 (5 PDF linkova).
- `gallery_view` — trigger "Klik na galeriju proizvoda" (All Elements, Click
  Classes contains `woocommerce-product-gallery` AND Page Path contains
  `/proizvod/`) + GA4 Event tag, **Tag firing options = Once per page** (da
  višestruki klik na thumbnail-e ne naduva brojku — isti obrazac kao epoxy
  "fires jednom"). Bez custom parametara (GA4 automatski hvata page_location).
  Potvrđeno na pravoj WooCommerce galeriji (`.woocommerce-product-gallery__image`
  klasa, PhotoSwipe lightbox).

🟢 **Ispravka 2026-07-22 (W3 3.10 regresija)**: stari "gtag stub id=DUMMY" gotcha
gore je bio zastareo/netačan u trenutku pisanja — u stvarnosti lokalni build
(`localhost`) nije imao NIKAKAV GTM/gtag kod, ni pravi ni DUMMY (BLOK A rad je
postojao samo u GTM UI, embed snippet je ostao na starom Porto/Kallyas buildu i
nikad nije prenet u WoodMart rebuild — nula analitike da je otišlo na migraciju
neprimećeno). Popravljeno preko `mu-plugins/al-tracking-gtm-consent.php`
(doslovna kopija live GTM+consent koda) — lokalni build sada učitava PRAVI
GTM-TRDT8K9 kontejner. GTM Preview/Tag Assistant protiv `localhost` nije
testiran ovu sesiju (moguće da i dalje ne radi iz drugih razloga — self-signed
okruženje, CORS i sl.); ako zatreba live-test triggera pre Submit-a, i dalje je
najsigurnija opcija GTM Preview protiv **live** antasline.com URL-a (read-only,
samo dodaje `gtm_debug` query param).

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
> Yoast. Stara odluka „Yoast ostaje (ne RankMath)" iz [[odluke/_pregled-odluka]]
> (28.06) je **ukinuta** — stajala je kao tvrdo pravilo 8 dana posle same
> migracije i bila aktivan izvor grešaka (13.08 umalo pogrešan meta ključ na 13
> arhiva).
>
> **Fajlovi obrisani 2026-08-13:** `wp-content/plugins/wordpress-seo` više ne
> postoji na buildu (bio 21 MB, v27.8, deaktiviran) — ne ide u migracioni paket
> 24.08. `_yoast_wpseo_*` postmeta **ostaje u bazi** (690 redova) i povratak je
> moguć raspakivanjem arhive
> `C:\xampp\htdocs\antasline-backups\yoast-wordpress-seo-27.8_2026-08-13.tar.gz`
> (postupak u [[odluke/_pregled-odluka]]). Brisano `rm -rf`-om, **ne**
> `wp plugin delete` — taj poziva uninstall rutinu koja može da obriše i podatke
> iz baze.

Šta je urađeno:
- Uvoz podataka izveden PROGRAMSKI preko Rank Math-ove sopstvene
  `\RankMath\Admin\Importers\Yoast` klase (Reflection poziv protected metoda
  `settings()`/`postmeta()`/`termmeta()`/`usermeta()` iz wp-cli eval-file,
  zaobiđen wp-admin wizard jer browser login nije bio dostupan). Pokrilo je
  7843 post meta zapisa, 12 term meta, 4 user meta, opšta podešavanja.
- **Gotcha #1**: `Meta` trait-ov `update_meta()` gate-uje na
  `is_protected_meta($key)`, koji je `false` za `rank_math_*` ključeve (nemaju
  `_` prefiks) van Rank Math-ovog sopstvenog AJAX/REST konteksta — prvi pokušaj
  uvoza je "uspeo" (tačni brojevi u rezultatu) ali upisao PRAZNE vrednosti.
  Fix: privremeni `add_filter('is_protected_meta', ...)` koji vraća `true` za
  `rank_math_` prefiks, samo za trajanje import skripte.
- **Gotcha #2**: Rank Math ne inicijalizuje `rank_math()->manager` (pa ni
  title/meta/schema izlaz na front-endu) dok se ne "poveže" nalog (Setup
  Wizard Connect korak) — bez toga su svi front-end filter-i (title, schema)
  tiho neaktivni iako je plugin "active" u `wp plugin list`. Fix: legitimna
  Rank Math opcija za preskakanje ovog koraka —
  `update_option('rank_math_registration_skip', true)` +
  `update_option('rank_math_is_configured', true)`.
- **Gotcha #3**: Local SEO modul (LocalBusiness/NAP schema) nije uključen po
  defaultu (`rank_math_modules` opcija) i Yoast import ga ne popunjava jer
  NAP podaci (telefon 069 234 00 72, Ulcinjska 13) su ranije bili ubačeni
  ručnim PHP filter-om (`wpseo_schema_organization` u child theme
  `functions.php`), ne kroz Yoast Local plugin — importer nema šta da povuče.
  Fix: `local-seo` dodat u `rank_math_modules`, NAP podaci ručno upisani u
  `rank-math-options-titles` (`local_address`, `phone_numbers`,
  `knowledgegraph_type=company`, `local_business_type=LocalBusiness`).
- Stari Yoast-specifični PHP u `functions.php` (custom Product JSON-LD
  fallback W2 2.7 + `wpseo_schema_organization` filter W2 2.8) je UKLONJEN —
  Rank Math native Schema modul pokriva oboje bogatije (GTIN, dimenzije,
  slike, offers za Product; Organization+LocalBusiness za NAP). Product
  schema se sad emituje bez obzira na cenu (stari price-based duplication
  guard više nema svrhu).
- Breadcrumbs NISU dirani — WoodMart tema ima sopstveni native breadcrumb
  (`woodmart_breadcrumbs()`) nezavisan od oba SEO plugina; theme opcije
  `yoast_*_breadcrumbs`/`rankmath_*_breadcrumbs` su bile isključene i pre i
  posle migracije, provereno u `xts-woodmart-options`.
- Sitemap URL nepromenjen (`sitemap_index.xml`, isti kao Yoast) — trebalo je
  `wp rewrite flush` posle uključivanja modula.
- Verifikovano posle migracije: naslov/meta na 10 kategorija proizvoda +
  homepage + kontakt + conquest članak (`/epoksidni-podovi-ili-ecotile-podovi/`)
  su identični Yoast originalima (uklj. ćirilične/dijakritičke znakove), JSON-LD
  na homepage i proizvod stranicama nema dupliranja (1× Organization/LocalBusiness,
  1× Product po proizvod stranici).

**Ostalo za proveru pri sledećem doticaju ove teme**: Rank Math admin UI
(Setup Wizard/Dashboard) nikad nije otvoren u browseru ovu sesiju — cela
migracija je urađena programski preko wp-cli. Ako nešto u UI izgleda
"nedovršeno" (npr. Connect banner), to je očekivano — funkcionalno je sve
aktivno preko `rank_math_registration_skip`.

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

> ⚠️ Build je prešao sa Porto+WPBakery na **WoodMart 8.5.4 + child** (vidi §2 i
> `[[migracija/woodmart-sabloni]]` za trenutne gotcha-e). Ovaj pod-odeljak je
> zadržan jer reimportovani postovi (F3, pun reimport sa live-a) mogu i dalje
> nositi stari WPBakery shortcode markup unutar `post_content` — ako se na to
> naiđe, važe pravila ispod. Post 4937 nalaz je potvrđeno **moot** (2026-07-22):
> `/industrijski-podovi/` je nova WoodMart stranica (ID 16567, rebuild
> 2026-07-05), 4937 je draft.
- JS greška "Cannot read properties of undefined" dolazi od nepoznatih/starih
  shortcode atributa ili nezatvorenih shortcode-ova
- Pre bilo kakvog programskog ubacivanja blokova: proveriti tačnu verziju
  `js_composer`, pisati markup koji odgovara toj verziji, regenerisati
  `_wpb_shortcodes_custom_css` i `_wpb_post_custom_css` post meta posle izmena
  sadržaja, **uvek prvo backup** (`wp db export`)

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

**PRAVILO:** Svi rad se radi na **LOKALNOM BUILD-u** (`http://localhost/antasline/`) dok se sajt potpuno ne redizajnira. **Live sajt se NE dira** dok se lokalni build ne završi (migracija **2026-08-24**, vidi [[2026-07-06-MASTER-PLAN-V2]] — M pomerio nedelju ranije 2026-08-10; raniji datumi 2026-08-31 i 2026-09-02 su zastareli). Content freeze od **2026-08-16**, gate pregled **2026-08-21**.

```
LOKALNI BUILD (http://localhost/antasline)
  = Redizajn + testiranje SVE
  = WordPress fajlovi + baza + slike
  = Tehnička, SEO, Ads — sve se testira ovde

LIVE SAJT (antasline.com)
  = PRODUCTION — ČEKANJE
  = Tek posle 2026-08-24 migracija (1 dan!)
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
- ✅ Posle 2026-08-24: Prebacujemo SVE kao bulk operacija

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

### 8.6 Token usage tracking
Log: `Token Logs/.token_log.jsonl` (vault root, append-only, JSONL). Posle
svake logičke akcije u sesiji ispiši na konzolu `✓ {akcija} | +Xk tokens |
Session: Yk` i append-uj log unos. Brojevi dolaze iz stvarnog usage polja u
Claude Code transkriptu sesije (`~/.claude/projects/<slug>/<session-id>.jsonl`),
ne iz procene. Preko 150k u sesiji → predloži `/clear`. Ne čitati log fajl
tokom rada osim na eksplicitan zahtev. Detalji i formula: [[reference/token-tracking]].

### 8.7 Design skillovi — uključeni 2026-08-06, koristi automatski kad treba dizajn
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

**Konektor / Google Ads dijagnostika** (istorijski Windsor.ai lekcije, i
dalje važe principijelno — Windsor je istekao 2026-07-27, zamenjen
sopstvenim konektorom preko `.claude/skills/antasline-konektor/`, videti
[[reference/api-konektor-setup.md]]):
- ECOTILE kolaps isporuke (visok impression share + sitni apsolutni
  impressions + skok CPC) = throttling na nivou naloga (balans/verifikacija),
  ne pad tražnje na tržištu
- Prazan/nulti odgovor za kampanju ne znači grešku konektora — proveri
  spend+impressions pre nego što pretpostaviš kvar (throttling istorija)
- Konektor (i stari Windsor, i novi sopstveni) je read-only prema GTM/GA4
  — potvrđuje da eventi stižu, ali ne može da menja tagove/triggere/key
  event podešavanja
- GA4 audience membership size se i dalje ne izlaže direktno preko
  standardnog Data API runReport-a — `active_users` segmentiran po
  `audience_name` (custom dimenzija, ako postoji) ostaje najbliži proxy;
  prazne publike se jednostavno ne pojavljuju u rezultatima
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
- 🔴 **Oba broja su `069`** (potvrđeno na live-u i u temi 2026-07-29). „072" i
  „074" su skraćenice za POSLEDNJE DVE CIFRE, ne prefiksi:
  **linija 72** = `069 234 00 72` (`tel:+381692340072`) ·
  **linija 74** = `069 234 00 74` (`tel:+381692340074`).
  Nikad ne pisati „072 234 00 72" — takav zapis je stajao na 50 lokalnih
  stranica i 37 Yoast metaopisa, ispravljen 2026-07-29.
- **Linija 72 dominira** klikovima na telefon (~50 vs ~7 za liniju 74);
  ~46/50 klikova sa mobilnog → prioritet linije 72 u ad asset-ima i on-page CTA-ovima
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
1. `[[2026-07-06-MASTER-PLAN-V2]]` — master plan do live-a (5 workstream-ova, N1–N7' raspored, gate kriterijumi; **go-live 2026-08-24**, pomereno 2026-08-10)
2. `[[PROGRESS]]` u vault-u — snapshot trenutnog stanja
3. `[[DNEVNIK-NAPRETKA]]` (append-only ledger, poslednji unosi)
4. Migracija/parity: `[[migracija/PARITY-PLAN]]` + status faza F1–F7 u `[[migracija/promptovi/_README]]`
5. Aktivni BLOK C pod-zadatak: `[[blokovi/BLOK-C-sledece]]` (C3 on-page; C1/C2 zamenjeni parity fazama)

---

## 13. KOMPLETAN HUB SVIH FAJLOVA (Wikilinks za navigaciju)

### 📋 OSNOVNO — Pročitaj prvo
- `[[00-INDEX]]` — Dashboard (Dataview tabele)
- `[[CLAUDE]]` — Ovo (instrukcije + kontekst)
- `[[PROGRESS]]` — Trenutno stanje
- `[[2026-07-06-MASTER-PLAN-V2]]` — Master plan do live-a (2026-08-24) — **aktivan**
- `[[2026-07-02-MASTER-PLAN-DO-LIVE]]` — Stari plan (⛔ superseded, istorijski snapshot)

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
- `[[reference/brend-knjiga]]` — Brand book: paleta boja, Inter tipografija, logo varijante, web look&feel (izvor: `Logo/*.pdf`)
- `[[reference/claude-skilovi]]` — Pregled Claude Code skilova (/antasline-sesija, /obogati-proizvod, /w6-social, /nedeljni-izvestaj)
- `[[reference/drustvene-mreze]]` — Popis social profila (W6 Faza 0, Miroslav popunjava)
- `[[reference/cenovnik]]` — Jedinstveni cenovnik (M10, Miroslav popunjava jednom, Claude vuče odatle)
- `[[reference/konkurencija-trziste-analiza]]` — Tržište i konkurencija po niši (2026-08-07): ko su konkurenti, gde smo šuplji/jači, preporuka fokusa
- `[[reference/token-tracking]]` — Token usage tracking konvencija (Token Logs/.token_log.jsonl)
- `[[reference/chrome-web-platform-2026]]` — Chrome 148–151 + DevTools 151: šta je upotrebljivo uz fallback, šta se meri, 🔴 prerender vs. konverzija

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

## 14. ⛔ ISTORIJSKI SNAPSHOT (2026-07-02) — SUPERSEDED, ne koristiti za "gde smo stali"

Arhivirano u [[dnevnik/2026-07-02-arhiva-snapshot]] (izmešteno tokom `/doctor`
čišćenja 2026-08-05). Za trenutno stanje uvek koristi §12 (redom:
[[2026-07-06-MASTER-PLAN-V2]] → [[PROGRESS]] → [[DNEVNIK-NAPRETKA]]).

---

## 15. ZA CLAUDE-A SLEDEĆI PUT

Kada otvorim CLAUDE.md sledeći put, znaću:

1. ✅ **Ko sam** — marketing analitičar za AntasLine
2. ✅ **Šta radim** — redizajn (WoodMart tema) + live migracija + SEO/Ads optimizacija
3. ✅ **Šta je gotovo** — BLOK A (tracking), BLOK B (publike), ceo W1 (rebuild 1.1–1.12), W2 content plan (20 stranica)
4. ✅ **Šta je u toku** — W3 (CWV/migracija priprema), W5 (nedeljni izveštaji), povremeni `[cpanel-live]` zadaci
5. ✅ **Šta je blokirano** — sve #ceka-miroslav stavke iz sekcije 4 ovog master plana (npr. Ads reaktivacija posle godišnjeg, cenovnik M10, live GEO fix preko cPanel)
6. ✅ **Gde su fajlovi** — Sve su linked-ovane kroz wikilinks
7. ✅ **Prioritet** — Tehnička → SEO → Ads
8. ✅ **Timeline** — go-live **2026-08-24** · content freeze 16.08 · gate pregled 21.08 (vidi [[2026-07-06-MASTER-PLAN-V2]] §2)
9. ✅ **Šta trebam od Miroslava** — vidi [[2026-07-06-MASTER-PLAN-V2]] §4 (zavisnosti)
10. ✅ **Šta radim sad** — [[PROGRESS]] (dnevni snapshot) + [[2026-07-06-MASTER-PLAN-V2]] (workstream-ovi W1–W5, nedelje N1–N7')
