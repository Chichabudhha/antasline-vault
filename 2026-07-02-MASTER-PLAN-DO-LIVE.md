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
- ❌ WooCommerce migracija — čeka SSH konfiguracija (tvoj-lokal host)
- ❌ Balans Google Ads — ECOTILE kolaps (-67% impressions)
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

#### 🔧 Tehnička (PRIORITET #1)
- [ ] **Identifikuj trenutne performance probleme**
  - Google PageSpeed: https://pagespeed.web.dev/
  - Web Vitals: LCP, CLS, INP
  - Lighthouse audit
  - `[[Tehnička optimizacija - audit]]` (novi fajl)
  
- [ ] **Lokalni build → live migracija — INFRASTRUKTURA**
  - Šta trebate da migrira? (WordPress fajlovi, baza, uploads, SSL sertifikat?)
  - Hosting? (cPanel je OK? ili novi hosting?)
  - Domain + DNS?
  - Napisati migration runbook

- [ ] **WooCommerce migracija — KRITIČNO**
  - SSH konfiguracija `tvoj-lokal` — **Miroslav mora da da IP/port/ključ**
  - Prenesi products + uploads sa live → local
  - Testiraj checkout sa Braintree/PayPal na local-u

#### 📝 SEO (PRIORITET #2 — paralelno sa tehnikom)
- [ ] **BLOK C1: Redirect mapa — ručna verifikacija**
  - Proceni 20 "PROVERI" redova — jesu li URL-ovi stvarni?
  - Kreiraj konačnu CSV sa svim 301 redirekcijama
  - Pripremi za `.htaccess` ili WordPress redirect plugin

- [ ] **GSC ključne reči — prioritet**
  - Kreiraj 4 nove landing stranice (iz 2026-07-02-gsc-keywords-analiza):
    1. `/dimenzije-basketskog-terena/` (240 impr, 2,5% CTR)
    2. `/terasa-cena/` ili `/spoljne-podloge-cena/` (236 impr)
    3. `/basketball-tabla-dimenzije/` (150 impr)
    4. `/podloge-za-parkiraliste-cena/` (ili merge sa parking)

#### 📊 Ads (PRIORITET #3 — čeka tehniku + balans)
- [ ] **Balans Google Ads — MIROSLAV MORA DA REŠI BLOKADU**
  - Dopuni nalog
  - Završi verifikaciju oglašivača
  - ECOTILE će se vratiti na normalnost

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
- [ ] **Basketball strannica** — dodaj FAQ + unapređena schema (iz `2026-07-02-basket-page-faq-schema.md`)
- [ ] **Top 10 stranica iz GA4** — refresh meta/title/H1-H2 struktura
- [ ] **Top 10 GSC query stranice** — povećaj CTR sa rich snippets

#### 🧪 Technical SEO
- [ ] XML sitemap generisanje + submission
- [ ] robots.txt + crawl budget optimizacija
- [ ] Mobile-first indexing verifikacija
- [ ] Structured data audit (FAQ, HowTo, Product, Article schema-a)

---

### FAZA 2: MIGRACIJA + OPTIMIZACIJA ADS (Nedelja 5-6 | do 2026-08-13)

#### 🚀 Live migracija (nakon što je lokalni build spreman)
- [ ] Pre-migracija backup live sajta
- [ ] DNS switch (antasline.com → novi hosting ili keep cPanel)
- [ ] 301 redirect mapa — aktivacija
- [ ] Sertifikat SSL (renewal/prenos)
- [ ] GA4 + GTM — verifikacija na live
- [ ] Test svih formi, checkout-a, analytics

#### 📢 Ads reorganizacija (posle balans + publike)
- [ ] 5 kampanja strukturu (umesto 2):
  1. **Terase** → 3 ad grupe (Bergo, Kunstrava, Bazen)
  2. **ECOTILE** → 2 ad grupe (Industrijski, ESD)
  3. **Sport** → 3 ad grupe (Basketball, Padel/Tenis, How-to)
  4. **Parking** → 1 ad grupa
  5. **(Opciono) Bazen** → 1 kampanja

- [ ] RSA asset dodavanje (uz exist. banka iz ADS-DNEVNIK.md)
- [ ] Bid adjustment po segment-u (industrija +20%, parking +15%, ostalo baseline)
- [ ] Conversion action verifikacija (samo `/hvala-za-poruku/`)

---

### FAZA 3: FINE-TUNING + MONITORING (Nedelja 7-8 | do 2026-09-02)

#### 🔍 Post-live monitoring
- [ ] Core Web Vitals — sigurno >75 (Google)
- [ ] Crawl errors u GSC — ispravka
- [ ] 404s iz redirect mape — provera
- [ ] Analytics verifikacija (GA4 + GTM sinhronizacija)
- [ ] Ads performance — prvo nedelje pre povećanja budžeta

#### 📈 Quick wins za conversion rate
- [ ] CTA buttons — veličina, boja, tekst (A/B test?)
- [ ] Trust signals — sertifikati, broj zadovoljnih klijenata, reference
- [ ] Telefon asset u Ads (072 prioritet — iz CLAUDE.md)
- [ ] Checkout friction — form fields, payment options
- [ ] Email follow-up automation (nakon kontakta)

#### 📊 Konverzije — Setup za praćenje
- [ ] CRM ili Email tracking (da znamo šta se dešava sa 53 kontakta)
- [ ] UTM tracking za sve Ads kampanje
- [ ] Conversion value u GA4 (nije samo broj, već vrednost narudžbe)
- [ ] Cohort analiza — koji segment konvertuje najbolje?

---

## 🚨 KRITIČNI BLOKERI (Trebam Miroslava)

| Bloker | Šta čeka | Ko | Deadline | Impact |
|---|---|---|---|---|
| SSH config (tvoj-lokal) | WooCommerce migracija | Miroslav | ASAP | HIGH — bez toga nema live migracije |
| Balans + Verifikacija Google Ads | ECOTILE kampanja živi | Miroslav | ASAP | MEDIUM — može se čekati ali bolje brže |
| GA4 publike (Task #1) | Ads segmentacija | Miroslav | do 2026-07-09 | LOW — može paralelno |
| Konverzioni levak info | Znamo li šta je prodaja? | Miroslav | do 2026-07-16 | MEDIUM — trebam za CRM setup |

---

## 📋 FAJLOVI U VAULTU (Referencija)

| Fajl | Sadržaj | Za koje faze |
|---|---|---|
| `[[2026-07-02-analiza-segmentacije]]` | 5 GA4 publika + Ads strategija | Faza 2 (Ads) |
| `[[2026-07-02-gsc-keywords-analiza]]` | 60 GSC queries + 4 kritična prioriteta | Faza 1 (SEO) |
| `[[2026-07-02-basket-page-faq-schema]]` | FAQ HTML + schema za basket stranicu | Faza 1 (On-page) |
| `[[ADS-DNEVNIK]]` | RSA asset banka, fazni plan 0-4 | Faza 2 (Ads) |
| `[[CLAUDE.md]]` | Sve instrukcije, identifikatori, lekcije | Sve faze |
| `[[PROGRESS.md]]` | Tekuće stanje — UV-uvek prvo pogledati | Sve faze |

---

## ⏱️ WEEKLY CADENCE (Nedelje 1-8)

```
Nedelja 1 (2-8 jul): Tehnička audit + SSH config + C1 verifikacija
Nedelja 2 (9-15 jul): Tehnička setup + 4 nove GSC stranice (start)
Nedelja 3 (16-22 jul): GSC stranice (finish) + Ads struktura (start)
Nedelja 4 (23-29 jul): On-page SEO (svih top 10 iz GA4) + Ads RSA
Nedelja 5 (30-5 avg): LIVE MIGRACIJA (ponedeljak 30. jul)
Nedelja 6 (6-12 avg): Live monitoring + Ads reorganizacija
Nedelja 7 (13-19 avg): Fine-tuning conversion + CRM setup
Nedelja 8 (20-2 sep): Final testing + Ads optimization setup
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
- ✅ Balans problem SOLVED

**BONUS (ako ima vremena):**
- RankMath prelazak (sa Yoast)
- Video content za top 5 proizvoda
- Customer testimonial sekcija
- B2B landing stranice (distributeri, reseljeri)

---

## 💬 PITANJA ZA MIROSLAVA

1. **SSH config:** Koja je IP/port/ključ za `tvoj-lokal` host?
2. **Konverzije:** Od 53 kontakta (jun) — koliko je postalo kupac? Kolika je prosečna vrednost?
3. **Hosting:** Ostajete na cPanel ili prelazite na novi hosting?
4. **Proizvodi:** Koji proizvodi su top 5 po prihodu?
5. **Follow-up:** Imate li CRM ili email follow-up proces za kontakte?

