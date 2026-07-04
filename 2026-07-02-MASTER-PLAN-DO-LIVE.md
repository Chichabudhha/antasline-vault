---
tip: plan
naziv: 2-mesečni plan redizajna do live-a
datum-plan: 2026-07-02 do 2026-09-02
prioritet: Tehnička → SEO → Ads
timeline: 8 nedelja
status: aktivan
---

# 🚀 MASTER PLAN — Antasline redizajn do live-a (2 meseca)

**Početak:** 2026-07-02  
**Cilj:** Redizajn live na antasline.com  
**Prioritet:** Tehnička optimizacija → SEO → Ads optimizacija  
**Budžet Ads:** 40k RSD/mesec (bez povećanja dok se ne optimizuje)  
**Konačni cilj:** Maksimalna prodaja sa minimalnim budžetom (brz sajt, odličan SEO, precizna analitika)

---

## ⚠️ KRITIČNA PRAVILA ZA OVU NEDELJU

**🔴 LOKALNI BUILD JE STAGING — LIVE SE NE DIRA!**

```
LOKAL (http://localhost/antasline)
  = REDIZAJN + TESTIRANJE
  = SVE PROMENE SE RADE OVDE
  = WordPress fajlovi + baza + slike

LIVE (antasline.com)
  = PRODUCTION — NE DIRAM!
  = Čeka dok se lokalni build ne završi
  = Tek posle 2026-09-02 prebacujemo SVE

VAULT (~/antasline-vault na hosting)
  = SAMO dnevnici/planovi sinhronizovani
  = Dokumentacija, ne WP fajlovi ili baza
```

**Konsekvencu:** 
- ❌ Nema SSH pristupa za live bazu
- ❌ Nema WooCommerce migracije sada
- ❌ Nema rad na live sajtu dok se lokal ne završi
- ✅ Fokus: Kvalitetan lokalni redizajn

---

## 📊 TRENUTNO STANJE (2026-07-02)

### ✅ Završeno (BLOK A + B)
- ✅ GTM v10 (Consent Mode v2, Generate Lead na `/hvala-za-poruku/`)
- ✅ GA4 tracking (key events: generate_lead, tel, mailto)
- ✅ 6 GA4 publika (4 postojeće + 2 nove)
- ✅ Negative keywords lista
- ✅ Lokalni WordPress build (http://localhost/antasline)
- ✅ Obsidian vault organizacija

### ⏳ U TOKU (BLOK C)
- 🟡 **C1: Redirect mapa** — 117 redova, 20 trebaju ručnu verifikaciju, 5 nemaju target
- 🟡 **C2: Content parity** — 32 proizvoda live vs 10 staging (27 nedostaje)
- 🟡 **C3: On-page build** — WPBakery JS bug, landing stranice

### 🔴 BLOKIRANO
- ✅ ~~WooCommerce migracija na lokal~~ — urađeno 2026-07-04 (37 proizvoda + 10 kategorija + 115 slika, bez SSH-a, preko live export/import). Live prebacivanje ostaje za migraciju.
- ✅ ~~Balans + verifikacija Google Ads~~ — odblokirano 2026-07-04; ECOTILE više nije throttlovan
- ❌ GA4 publike (Task #1) — čeka Miroslava da kreira u GA4 UI
- ❌ RankMath prelazak — cilj ali nije prioritet

---

## 📈 KONVERZIJE — PROCENA IZ PODATAKA

### Trenutni konverzioni levak (juni 2026)
```
Organic traffic: ~1.500 posetioca/nedelja (iz GA4)
↓
/hvala-za-poruku/ konverzije: 53/mesec (1-29.06)
  • Organski: ~50 (94%)
  • Plaćeni Ads: ~3 (6%)
↓
Conversion rate: 53 / (1.500 × 4) = 0,88% (EKSTREMNO NISKO)
```

### Problem: Ne znamo šta se dešava POSLE forme
- Forma se popuni → `/hvala-za-poruku/` ✓
- Ali — **da li se to konvertuje u prodaju?**
- Nema CRM/SMS/Email follow-up tracking

### Preporuka za Miroslava (pitaj ga)
1. Od 53 kontakta (jun) — **koliko je postalo kupac?**
2. Kolika je prosečna vrednost narudžbe?
3. Da li postoji follow-up proces (email, telefon)?

**Za sada pretpostavljam:** 10-20% conversion (50-106 kupaca/mesec iz organskog)

---

## 🎯 AKCIJSKI PLAN — PREMA PRIORITETU

### FAZA 0: ODMAH (Nedelja 1-2 | do 2026-07-16)

#### 🔧 Tehnička (PRIORITET #1) — SVE NA LOKALU!
- [ ] **Identifikuj trenutne performance probleme na LOKALU**
  - PageSpeed Insights: http://localhost/antasline
  - Web Vitals: LCP, CLS, INP
  - Lighthouse audit (lokalno)
  - Napravi detaljnu analizu i spremi kao `PERFORMANCE-AUDIT.md` u dnevnik/
  
- [ ] **Lokalni build optimizacija (NE LIVE!)**
  - Porto tema — ukloniti nepotrebne CSS/JS
  - WPBakery optimizacija — slike, shortcodes
  - Plugin cleanup — deaktiviraj nepotrebne
  - PHP memory limit (ako trebalo)
  - Cache/compression setup (lokalno za test)

- [ ] **WooCommerce na LOKALU — TESTIRANJE**
  - ✅ Products već postoje (iz backup-a)
  - Testiraj checkout (lokalno)
  - Testiraj slike/uploads
  - Testiraj payment gateway (lokalno bez $)

#### 📝 SEO (PRIORITET #2 — paralelno sa tehnikom)
- [ ] **BLOK C1: Redirect mapa — ručna verifikacija**
  - Proceni 20 "PROVERI" redova — jesu li URL-ovi stvarni?
  - Kreiraj konačnu CSV sa svim 301 redirekcijama
  - Pripremi za `.htaccess` ili WordPress redirect plugin

- [ ] **GSC ključne reči — prioritet**
  - Kreiraj 4 nove landing stranice (iz [[dnevnik/2026-07-02-gsc-keywords-analiza]]):
    1. `/dimenzije-basketskog-terena/` (240 impr, 2,5% CTR)
    2. `/terasa-cena/` ili `/spoljne-podloge-cena/` (236 impr)
    3. `/basketball-tabla-dimenzije/` (150 impr)
    4. `/podloge-za-parkiraliste-cena/` (ili merge sa parking)

#### 📊 Ads (PRIORITET #3)
- [x] **Balans Google Ads + verifikacija oglašivača — REŠENO 2026-07-04**
  - Nalog dopunjen, verifikacija završena → nalog odblokiran
  - Preostaje: potvrditi da su ECOTILE prikazi/CPC vraćeni na normalu

- [ ] **Task #1: GA4 publike — Miroslav kreira u GA4 UI**
  - 4 nove publike (Industrija+ESD, Sport, Parking, Bazen)
  - Test connection sa Ads

---

### FAZA 1: SEO ACCELERATION (Nedelja 3-4 | do 2026-07-30)

#### 📄 On-page SEO — Nove stranice
- [ ] `/dimenzije-basketskog-terena/` — sa FAQ schema
- [ ] `/terasa-cena/` — sa price calculator
- [ ] `/basketball-tabla-dimenzije/` — sa video/3D model
- [ ] `/podloge-za-parkiraliste/` — sa galerije/testimonial-i

**Svaka strannica:**
- Yoast SEO >80 poena
- 2000+ reči
- Internal linking ka 3+ postojećih
- CTAs ka formi + telefon
- Slike sa alt tekst-om

#### 🔍 On-page optimizacija (postojeće)
- [ ] **Basketball strannica** — dodaj FAQ + unapređena schema (iz [[dnevnik/2026-07-02-basket-page-faq-schema]])
- [ ] **Top 10 stranica iz GA4** — refresh meta/title/H1-H2 struktura
- [ ] **Top 10 GSC query stranice** — povećaj CTR sa rich snippets

#### 🧪 Technical SEO
- [ ] XML sitemap generisanje + submission
- [ ] robots.txt + crawl budget optimizacija
- [ ] Mobile-first indexing verifikacija
- [ ] Structured data audit (FAQ, HowTo, Product, Article schema-a)

---

### FAZA 2: FINALIZACIJA LOKALA + ADS SETUP (Nedelja 5-6 | do 2026-08-13)

#### ✅ Lokalni build — FINALNI CHECKOUT
- [ ] **SVE na lokalu je testiran i gotov:**
  - Performanse: LCP <2.5s, CLS <0.1, INP <200ms
  - SEO: Sve stranice sa Yoast >80, svi linkovi internog, sitemap
  - Forme: Sve kontakt forme rade, `/hvala-za-poruku/` okida
  - WooCommerce: Svi proizvodi, checkout, payment gateway testiran
  - Analytics: GA4 + GTM live na localhost (test konverzije)
  - Mobile: Responsive, svi devices testiran

- [ ] **Backup lokalnog build-a pre finalne migracije**
  - `wp db export` — kompletan backup baze
  - `tar` ZIP kompletan `wp-content/` folder
  - Spremi na više lokacija

#### 📢 Ads reorganizacija (na LOKALNOM buildu)
- [ ] 5 kampanja strukturu (umesto 2):
  1. **Terase** → 3 ad grupe (Bergo, Kunstrava, Bazen)
  2. **ECOTILE** → 2 ad grupe (Industrijski, ESD)
  3. **Sport** → 3 ad grupe (Basketball, Padel/Tenis, How-to)
  4. **Parking** → 1 ad grupa
  5. **(Opciono) Bazen** → 1 kampanja

- [ ] RSA asset dodavanje (uz exist. banka iz [[dnevnik/ADS-DNEVNIK]])
- [ ] Bid adjustment po segment-u (industrija +20%, parking +15%, ostalo baseline)
- [ ] Conversion action verifikacija (samo `/hvala-za-poruku/`)

---

### FAZA 2.5: MIGRACIJA NA LIVE (ponedeljak 2026-08-31 — 1 DAN!)

**⚠️ SAMO KADA JE LOKALNI BUILD 100% GOTOV I TESTIRAN!**

- [ ] **Pre migracije:**
  - Backup LIVE sajta (cPanel → MySQL export)
  - Backup LIVE `wp-content/` (FTP)
  - Spremi DNS settings (ako se menja)

- [ ] **Migracija:**
  - `wp db export` sa lokala → upload na live (cPanel)
  - `wp-content/` copy sa lokala → live (FTP/cPanel)
  - URL zamena (http://localhost/antasline → https://antasline.com)
  - Test svi linkovi, forme, slike
  - Verifikacija GA4 + GTM na live
  - Aktivacija 301 redirect mape (iz C1)

- [ ] **Post-migracija (prvo nedelje):**
  - GSC resubmit sitemap
  - Crawl error monitoring
  - Analytics verifikacija
  - Live Ads test (prvo nedelje, mali budžet)

---

### FAZA 3: FINE-TUNING + MONITORING (Nedelja 7-8 | do 2026-09-02)

#### 🔍 Post-live monitoring (posle migracije)
- [ ] Core Web Vitals — sigurno >75 (Google)
- [ ] Crawl errors u GSC — ispravka
- [ ] 404s iz redirect mape — provera
- [ ] Analytics verifikacija (GA4 + GTM sinhronizacija)
- [ ] Ads performance — prva nedelja pre povećanja budžeta
- [ ] Korisnički feedback — forme, checkout, performance

#### 📈 Quick wins za conversion rate
- [ ] CTA buttons — veličina, boja, tekst (A/B test?)
- [ ] Trust signals — sertifikati, broj zadovoljnih klijenata, reference
- [ ] Telefon asset u Ads (072 prioritet — iz [[CLAUDE]])
- [ ] Checkout friction — form fields, payment options
- [ ] Email follow-up automation (nakon kontakta)

#### 📊 Konverzije — Setup za praćenje
- [ ] CRM ili Email tracking (da znamo šta se dešava sa 53 kontakta)
- [ ] UTM tracking za sve Ads kampanje
- [ ] Conversion value u GA4 (nije samo broj, već vrednost narudžbe)
- [ ] Cohort analiza — koji segment konvertuje najbolje?

---

## 🚨 KRITIČNI BLOKERI (Trebam Miroslava — ZA LOKAL!)

| Bloker | Šta čeka | Ko | Deadline | Impact |
|---|---|---|---|---|
| ~~Balans + Verifikacija Google Ads~~ | ✅ REŠENO 2026-07-04 — nalog odblokiran | — | — | — |
| GA4 publike (Task #1) | Ads segmentacija (lokal) | Miroslav | do 2026-07-09 | LOW — mogu paralelno sa SEO |
| Konverzioni levak info | Znamo li šta je prodaja? | Miroslav | do 2026-07-16 | MEDIUM — za CRM/follow-up setup |
| Cena za domene/hosting | Potvrda za live migraciju | Miroslav | do 2026-08-20 | MEDIUM — trebam datum/IP za migraciju |

---

## 📋 FAJLOVI U VAULTU (Referencija)

| Fajl | Sadržaj | Za koje faze |
|---|---|---|
| `[[dnevnik/2026-07-02-analiza-segmentacije]]` | 5 GA4 publika + Ads strategija | Faza 2 (Ads) |
| `[[dnevnik/2026-07-02-gsc-keywords-analiza]]` | 60 GSC queries + 4 kritična prioriteta | Faza 1 (SEO) |
| `[[dnevnik/2026-07-02-basket-page-faq-schema]]` | FAQ HTML + schema za basket stranicu | Faza 1 (On-page) |
| `[[dnevnik/ADS-DNEVNIK]]` | RSA asset banka, fazni plan 0-4 | Faza 2 (Ads) |
| `[[CLAUDE]]` | Sve instrukcije, identifikatori, lekcije | Sve faze |
| `[[PROGRESS]]` | Tekuće stanje — UV-uvek prvo pogledati | Sve faze |

---

## ⏱️ WEEKLY CADENCE (Nedelje 1-8)

```
Nedelja 1 (2-8 jul): Tehnička audit + C1 verifikacija
Nedelja 2 (9-15 jul): Tehnička setup + 4 nove GSC stranice (start)
Nedelja 3 (16-22 jul): GSC stranice (finish) + Ads struktura (start)
Nedelja 4 (23-29 jul): On-page SEO (svih top 10 iz GA4) + Ads RSA
Nedelja 5 (30 jul-5 avg): Finalizacija lokala + WooCommerce checkout test
Nedelja 6 (6-12 avg): Backup + Ads reorganizacija (5 kampanja)
Nedelja 7 (13-19 avg): Final testing lokala + priprema migracije
Nedelja 8 (20-26 avg): Buffer + zamrzavanje builda
→ LIVE MIGRACIJA: ponedeljak 2026-08-31 (1 dan) → post-live monitoring do 2026-09-02
```

---

## 🎯 SUCCESS CRITERIA (do 2026-09-02)

- ✅ Live sajt aktivan sa lokalnim build-om
- ✅ 301 redirect mapa aktivna
- ✅ 4 nove GSC landing stranice objavljene
- ✅ FAQ + schema na basketball stranici
- ✅ Ads reorganizovani u 5 kampanja
- ✅ Core Web Vitals >75
- ✅ GA4 + GTM spreman za post-launch monitoring
- ✅ CRM ili email tracking za kontakte
- ✅ Balans problem SOLVED (✅ urađeno 2026-07-04)

**BONUS (ako ima vremena):**
- RankMath prelazak (sa Yoast)
- Video content za top 5 proizvoda
- Customer testimonial sekcija
- B2B landing stranice (distributeri, reseljeri)

---

## 💬 PITANJA ZA MIROSLAVA

1. **SSH config (za live migraciju):** Koja je IP/port/ključ za produkcioni host? (WooCommerce import na lokal je već urađen bez SSH-a — ovo treba samo za finalno prebacivanje.)
2. **Konverzije:** Od 53 kontakta (jun) — koliko je postalo kupac? Kolika je prosečna vrednost?
3. **Hosting:** Ostajete na cPanel ili prelazite na novi hosting?
4. **Proizvodi:** Koji proizvodi su top 5 po prihodu?
5. **Follow-up:** Imate li CRM ili email follow-up proces za kontakte?

