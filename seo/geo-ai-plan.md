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
- [x] ✅ ZATVORENO 2026-07-08 (lokal) + ✅ 2026-07-23 **deployovan direktno na LIVE** (`[cpanel-live]`, `~/public_html/llms.txt`, 200/text-plain, verifikovan) — ranije pravilo "aktivira se na migraciji" preskočeno na M zahtev, isti obrazac kao 2542 GEO fix (ranije GEO indeksiranje) → [[DNEVNIK-NAPRETKA]]
- [x] ✅ 2026-07-23 — **`llms-full.txt` (pratilac fajl, pun tekst 7 ključnih stranica) deployovan na LIVE**, `~/public_html/llms-full.txt`, 200/charset=utf-8 → [[DNEVNIK-NAPRETKA]]. Napomena: adoptacija ovog fajla od strane glavnih AI asistenata (ChatGPT/Perplexity/Google) NIJE zvanično potvrđena — nizak trošak, neizvestan efekat; realna GEO poluga ostaje stvarni sadržaj stranica + GMB recenzije + PR/direktorijumi (v. sekcije 2/4 ovog plana)
- Sajt je server-rendered WordPress ✓ (AI crawleri ne izvršavaju JS — mi smo OK)

### 2. Citabilan sadržaj ⭐⭐⭐ (već u toku kroz C3)
AI citira stranice sa jasnim činjenicama, tabelama i brojevima. **[[seo/plan-novih-stranica]] je istovremeno GEO plan** — "dimenzije terena", "šljaka", cena tabele = tačno pitanja koja ljudi kucaju u ChatGPT.
- [x] ✅ 2026-07-22 — audit 15 ključnih stranica: 12 W1-rebuild hub-ova već ispravno, 3 legacy posta (šljaka/padel/odbojka) popravljena sa "Kratak odgovor" uvodom. Pravilo ostaje aktivno za svaku BUDUĆU novu/refresh stranicu. #claude-code
- [ ] FAQ blokovi + FAQPage schema (već pravilo u content planu) ✓
- [x] ✅ 2026-07-22 — dodata doslovna fraza "alternativa epoksidnom podu za proizvodnu halu" (uvod + novo FAQ pitanje, vidljivo + JSON-LD) na lokalu; live deo pripremljen kao poseban prompt ([[migracija/2026-07-22-prompt-live-2542-geo-fix]]), #ceka-miroslav da pokrene. GSC pozicija (~26) i dalje otvorena — ovo je samo tekstualno GEO poklapanje, ne rang fix.

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
- [x] ✅ NOVO 2026-07-23 — `[[analiza/BOT-CRAWLER-LOG]]` pokrenut: access log analiza svih bot/crawler hitova (AI asistenti/search/SEO-alati), baseline presek + prati da li AI botovi uopšte povlače `llms.txt`/`llms-full.txt` (0 organskih hitova u prvom preseku, prerano za zaključak). Ponoviti presek za ~1 nedelju.
- [x] ✅ PRVI PUT IZVRŠENO 2026-07-22 — Mesečni AI test: 5 fiksnih promptova u ChatGPT (pravi Incognito, bez naloga). Rezultat: **2/5 pominjanja** (prompt 1 "industrijski PVC podovi" bez URL citata, prompt 5 "ko postavlja sportske terene" SA citatom na antasline.com). 🔴 2 gap-a otkrivena: prompt 3 (epoksid alternativa) AI ne pominje modularni PVC/Ecotile kategoriju uopšte; prompt 4 (terase bez lepljenja) AI misli samo na WPC deking, ne Bergo klik-sisteme. Detalji + puni odgovori: [[analiza/2026-07-22-ai-test-baseline]]. Ponoviti sledeći mesec istim promptovima za trend.

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
