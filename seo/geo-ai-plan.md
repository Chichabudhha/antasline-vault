---
tip: strategija
datum: 2026-07-04
naziv: GEO / AI vidljivost — da AI preporučuje Antasline
status: aktivan
azurirano: 2026-07-04
---

# 🤖 GEO plan — kako da AI (ChatGPT, Gemini, Perplexity…) preporučuje Antasline

**Dokaz da se već dešava:** GA4 kanal "AI Assistant" — chatgpt.com referrali, 9 korisnika/90d, engagement 100% ([[analiza/2026-07-04-snapshot-full]] §2.2). Za nišni srpski B2B ovo je asimetrična prilika: AI odgovori na srpskom se oslanjaju na malo dostupnih izvora — ko je najbolje strukturiran izvor u niši, dobija nesrazmerno mnogo preporuka.

## Kako AI bira koga preporučuje

| Kanal | Odakle vuče | Brzina efekta |
|---|---|---|
| ChatGPT Search / Perplexity / AI Overviews | živi web (crawl + citati) | nedelje |
| Gemini / Google AI lokalni odgovori | GMB + recenzije + web | nedelje |
| Trening podataka budućih modela | web arhive, forumi, direktorijumi, PR | meseci–godine |

---

## Akcije

### 1. Tehnički pristup za AI crawlere ⭐⭐⭐ (5 min + odluka)
- [ ] `robots.txt` na LIVE sajtu: proveriti/dozvoliti `GPTBot`, `OAI-SearchBot`, `PerplexityBot`, `ClaudeBot`, `Google-Extended` #ceka-miroslav
- [x] ✅ ZATVORENO 2026-07-08 — `llms.txt` kreiran u root-u lokalnog builda (produkcioni domen, aktivira se na migraciji) → [[DNEVNIK-NAPRETKA]]
- Sajt je server-rendered WordPress ✓ (AI crawleri ne izvršavaju JS — mi smo OK)

### 2. Citabilan sadržaj ⭐⭐⭐ (već u toku kroz C3)
AI citira stranice sa jasnim činjenicama, tabelama i brojevima. **[[seo/plan-novih-stranica]] je istovremeno GEO plan** — "dimenzije terena", "šljaka", cena tabele = tačno pitanja koja ljudi kucaju u ChatGPT.
- [ ] Svaka nova/refresh stranica: prvi pasus = direktan odgovor na pitanje u 1–2 rečenice (AI izvlači prvi jasan odgovor) #claude-code
- [ ] FAQ blokovi + FAQPage schema (već pravilo u content planu) ✓
- [ ] Conquest članak 2542 refresh — kad neko pita AI "epoksid ili alternativa za halu", ovaj članak je citat-kandidat, ali poz. 26 za "epoksi podovi" = AI ga ne vidi. SEO refresh iz kw-analize #7 rešava i GEO. #claude-code

### 3. Entitet — nedvosmislen identitet ⭐⭐ (1–2h)
- [x] ✅ ZATVORENO 2026-07-08 — `Organization` (Yoast, već postojao sa `sameAs`) proširen filterom na `LocalBusiness` + adresa/telefon (NAP) → [[DNEVNIK-NAPRETKA]]
- [x] ✅ Već gotovo od 2026-07-07 — "O nama" ima proverljive činjenice (15+ godina, brendovi Ecotile/Bergo/Sit-in, imenovane reference HTEC/Bosch/Institut Vinča itd.)
- [ ] NAP (ime/adresa/telefon 069 2340072) identičan: sajt = GMB = direktorijumi #ceka-miroslav

### 4. Pominjanja treće strane ⭐⭐⭐ (najjači signal, traje)
AI za "najbolji X u Srbiji" agregira tuđe liste, portale, forume:
- [ ] **PR o projektima**: Spanoulis court + Dunk Shop teren → sportski/lokalni portali (besplatna priča!) #ceka-miroslav
- [x] **Case studije sa imenima** — delimično ✅ 2026-07-12: Quectel (već postojao, 5163) + HTEC (novo, post 17021) imaju dovoljno realnog materijala (fotke koje otkrivaju kontekst) za poštenu case-study stranicu, oba sad linkovana sa `/industrijski-podovi/` reference reda. Hankook i Amicus ostaju otvoreni — samo generička referentna fotka bez detalja, nedovoljno za naraciju bez izmišljanja → #ceka-miroslav (konkretni detalji projekta: kada, koliko m², koji problem su rešili)
- [ ] Upisi u domaće direktorijume + građevinske portale #ceka-miroslav
- [ ] GMB recenzije 6 → 20+ (već u [[analiza/2026-07-04-snapshot-full]] §6.3) #ceka-miroslav

### 5. Merenje ⭐⭐ (u snapshot rutini)
- [ ] GA4: pratiti "AI Assistant" kanal mesečno — baseline **9 korisnika/90d**
- [ ] Mesečni AI test (dodato u [[analiza/_TEMPLATE-snapshot]]): 5 fiksnih promptova u ChatGPT (bez naloga/incognito) + zabeležiti da li se Antasline pominje i koji URL citira

**Fiksni test promptovi:**
1. "Ko prodaje industrijske PVC podove u Srbiji?"
2. "Koja je najbolja podloga za košarkaški teren u dvorištu i koliko košta?"
3. "Alternativa epoksidnom podu za proizvodnu halu"
4. "Podloge za terasu koje se ne lepe — gde kupiti u Beogradu?"
5. "Ko postavlja sportske terene u Srbiji?"

## Veze
- [[seo/plan-novih-stranica]] — content = GEO gorivo
- [[analiza/2026-07-04-snapshot-full]] §2.2 (AI Assistant kanal) + §6.3 (GMB)
- [[analiza/2026-07-04-gsc-kw-analiza-16m]] — info klasteri (dimenzije/šljaka) = AI-citation mamci
