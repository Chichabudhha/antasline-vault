# Dnevnik napretka — Antasline SEO

## 2026-07-02 [chat] [Windsor/GA4+Ads+GSC + FAQ/Schema] — Kompletan pulov podataka + preporuke
- Povučeni podaci iz Windsor.ai: GA4 (30 stranica), Google Ads (56 dana), GSC (60 ključnih reči)
- Analiza top stranica: Spoljne podloge (1062 users), Industrijski (481), Sport (742), Parking (247)
- **Preporuka:** 5 novih GA4 publika — Spoljne/Industrijski+ESD/Sport/Parking/Bazen
- **GSC analiza:** 4 KRITIČNA priority-a za nove stranice: dimenzije basket terena (240 impr), cena terase (236 impr), dimenzije table (150 impr), gumeni tepih (160 impr)
- **Basketball stranica:** Kreirani FAQ + unapređena schema (FAQPage + HowTo + Product) za /kako-napraviti-teren-za-basket/ 
- Task #1: GA4 publike #ceka-miroslav
- Detaljni izveštaji: [[dnevnik/2026-07-02-analiza-segmentacije]] + [[dnevnik/2026-07-02-gsc-keywords-analiza]] + [[dnevnik/2026-07-02-basket-page-faq-schema]]
- Sledeće: Implementiraj FAQ + schema na stranici, kreiraj 4 nove stranice + Ads reorganizacija #claude-code

## 2026-07-01 [chat] [ADS] — Snimak podataka + fazni plan
- ECOTILE zagušen: prikazi −67%, CPC 26→74 RSD — uzrok je blokada naloga (balans/verifikacija)
- Terase: 296 klik/ned, CTR 19%, konverzija slaba (2/ned) → prioritet je kreativa
- Napravljen fazni plan 0–4 i banka RSA asseta za obe kampanje
- Detalji i banka asseta: [[dnevnik/ADS-DNEVNIK]]

## 2026-06-29 [cpanel-live] — GTM tel: tag obrisan (UŽIVO)
- GTM tag koji je okidao GA4 event "tel:+381692340072" obrisan iz GTM-TRDT8K9 i publishovan
- Verifikovano: event više ne okida u GA4 DebugView ✓

## 2026-06-28 [cpanel-live] — Opt-out consent model aktiviran (UŽIVO)
- Plugin antasline-consent prešao na opt-out: pri prvoj poseti kolačić se odmah postavlja na {ad:true, analytics:true}
- Consent Mode v2 default (nema kolačića): sve kategorije sada 'granted' umesto 'denied'
- Banner se i dalje prikazuje — posetilac može da klikne "Odbij sve" ili podesi po kategorijama
- Toggles u panelu podrazumevano checked=true kada nema kolačića
- Verifikacija: curl potvrđuje 'granted' u else grani ✓

## 2026-06-28 [cpanel-live] — SEO title fix, GA4 istraga, SSH most, WooCommerce export (UŽIVO)
- SEO: Obrisani duplikat/neispravni _yoast_wpseo_title na 6 postova (ID 2542 duplikat, 3327/3621 %%title%%, 3257/4813/6824 %%title%% %%page%% %%sep%%)
- GA4 event "tel:+381692340072" — utvrđeno: izvor je GTM tag (ne server/plugin); #ceka-miroslav da obriše tag u GTM UI
- SSH ključ ed25519 kreiran (~/.ssh/id_ed25519_github), GitHub autentikacija OK
- CLAUDE.md kreiran u ~/public_html/ sa vault workflow instrukcijama
- live-export.sh popravljen (trailing comma bug, --no-create-info bug); woo-export.sql 444K generisan (47 proizvoda, 71 attachment, 22 pa_* atributa)
- Otvorene akcije: prenos woo-export.sql na staging #ceka-miroslav, brisanje GTM tel: taga #ceka-miroslav

## 2026-06-28 [chat] — Obsidian vault + git most postavljen → [[2026-06-28-obsidian-vault-git-most]]
- Vault C:\Projekti\antasline-vault\ kao jedina istina; GitHub Chichabudhha/antasline-vault
- DNEVNIK + PROGRESS preseljeni iz htdocs; cPanel vault kloniran; git most testiran OK
- Sledeće: BLOK C1 redirect mapa (nov chat, Sonnet, zalepi PROGRESS.md u seed)

## 2026-06-28 [chat] — Obsidian vault + git most postavljen
- Vault: C:\Projekti\antasline-vault\ kao jedina istina projekta
- DNEVNIK-NAPRETKA.md i PROGRESS.md preseljeni iz htdocs u vault
- GitHub repo: Chichabudhha/antasline-vault (private)
- Obsidian Git plugin aktivan, auto-sync 10min
- cPanel: vault kloniran u ~/antasline-vault, CLAUDE.md kreiran
- Git most zatvoren: lokal ↔ GitHub ↔ cPanel sinhronizovani
- Sledeće: BLOK C1 — redirect mapa (nov chat, Sonnet model)
## 2026-06-28 [chat] — Obsidian vault postavljen i objedinjen
- Vault `C:\Projekti\antasline-vault\` postao jedina istina projekta.
- DNEVNIK-NAPRETKA.md i PROGRESS.md preseljeni iz htdocs u vault.
- CLAUDE.md (htdocs) dopunjen vezom ka vault-u; Claude Code odsad loguje ovde.
- Detaljan zapis: [[2026-06-28-postavljanje-vault]]
- [ ] Aktivirati Dataview plugin #ceka-miroslav
- [ ] Izabrati BLOK C stavku (C1/C2/C3) #ceka-miroslav

## 2026-06-25 — Optimizacija /industrijski-podovi/ (Yoast meta)

**Stranica:** http://localhost/antasline/industrijski-podovi/ (ID 4937, post_type=post)

**Urađeno:**
- ✅ Yoast title: `Industrijski PVC podovi u pločama — bez zastoja proizvodnje | Antas Line` (69 znakova, optimalno)
- ✅ Yoast meta opis: `Industrijski PVC podovi Ecotile — montaža preko postojećeg betona bez zastoja proizvodnje i bez lepka. Otporni na viljuškare, hemikalije, R10. Brzo do upita.`
- ✅ Stranica radi ispravno za posetioce (karakteri, footer, width — sve OK)

**Nije urađeno:**
- ❌ 6 sadržajnih blokova (planiran): WPBakery visual editor ima JavaScript bug pri parsiranju shortcode-a (`Cannot read properties of undefined`). Programski pristup (PHP) pravi probleme sa editor-om, a manual unos je komplikovan zbog strukture.

**Zaključak:**
- Yoast SEO optimizacija je **ZAVRŠENA i aktivna**
- Blokovi se mogu dodati kasnije ručno kroz WPBakery editor (drag-and-drop), ili koristiti Text editor za ažuriranja
- Stranica je **sprema za produkciju sa SEO meta-om**

**Backup:** `backup-industrijski-20260625-1059.sql` (31.56 MB)

---

## 2026-06-25 — Pokušaj: Optimizacija /industrijski-podovi/ (6 sadržajnih blokova)

**Stranica:** http://localhost/antasline/industrijski-podovi/ (ID 4937, post_type=post)

**Izmene:**
- `_yoast_wpseo_title`: (stari/dugačak) → `Industrijski PVC podovi u pločama — bez zastoja proizvodnje | Antas Line` (69 znakova)
- `_yoast_wpseo_metadesc`: ažuriran sa fokusom na "bez zastoja", "bez lepka", "Ecotile", "R10"
- Dodati 6 WPBakery blokova (`[vc_row]` strukture) PRE FAQ sekcije:
  1. **Uporedna tabela** (PVC vs epoksid vs poliuretan vs mikrocement) — conquest za "epoksid" nameru
  2. **Cena blok** ({{CENA_PVC_OD}}–{{CENA_PVC_DO}} €/m² sa placeholder-ima za Miroslava)
  3. **Vrste industrijskih podova** — edukativni tekst o razlici između silo-pristupa
  4. **Namena grid** (magacini, proizvodnja, autoservisi, HACCP, farmacija, hladnjače, ESD)
  5. **Reference galerija** (sprema za slike: Hankook, HTEC, Amicus — trust signal)
  6. **Tehnička svojstva tabela** (R10, Bfl-s1, hemijska otpornost, debljine, OHSAS 18001, 25 godina trajanja)

**Verifikacija:**
- WPBakery struktura: 14 [vc_row] ↔ 14 [/vc_row] (integritet ✓)
- Svih 6 blokova prisutno u sadržaju ✓
- Yoast meta postavljeni ✓
- Bez broken shortcode-a ✓
- HTTP 200 pri učitavanju ✓

**Napomene:**
- Placeholder cene `{{CENA_PVC_OD}}` i `{{CENA_PVC_DO}}` ostavljeni za Miroslava da popuni sa realnim ciframa
- Reference galerija sprema za fotografije (nedostaju slike iz medijateke)
- Blok "Namena grid" može biti osnova za kasnije pod-stranice (/industrija-podovi/magacini/, itd.)
- Backup pre izmena: `backup-industrijski-20260625-1059.sql` (31.53 MB)

---

## 2026-06-23 — On-page popravka /pop-tenis/

**Stranica:** http://localhost/antasline/pop-tenis/ (ID 15966, post_type=post)

**Izmene:**
- `_yoast_wpseo_title`: (prazno) → `Teren za pop tenis i pickleball – dimenzije i izrada`
- `post_title` (= H2 entry-title): `Padel tenis` → `Teren za pop tenis i pickleball`
- `_yoast_wpseo_metadesc`: zadržan (pominje pickleball i pop tenis)
- Intro paragraf: dodata reč `piklbol` (fonetski oblik, 293 prikaza koji nisu hvatani)

**Verifikacija:**
- `<title>`: Teren za pop tenis i pickleball – dimenzije i izrada ✓
- `<h2 class="entry-title">`: Teren za pop tenis i pickleball ✓
- "Padel tenis" više nije title/H2 ✓
- "piklbol" prisutan u rendered HTML ✓
- Regression: industrijski-podovi i spoljnje-podne-obloge Yoast titles nepromenjeni ✓

**Napomene:**
- Porto tema renderuje entry-title kao `<h2>`, ne `<h1>` — `<h1>` je blog archive heading ("Aktuelnosti")
- Padel reference u body-ju ostavljene netaknute (upućuju na zaseban padel teren)
- Backup pre izmena: `backup-onpage-20260623.sql` (31.53 MB)
