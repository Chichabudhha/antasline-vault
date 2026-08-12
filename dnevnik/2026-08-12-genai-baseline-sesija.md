---
tip: sesija
alat: claude-code
datum: 2026-08-12
blok: "-"
status: zavrseno
---

# Sesija — GenAI baseline pre migracije (W5/GEO)

Treća sesija istog dana (prve dve: `product_brand` arhive, Chrome dokumentacija
+ `/antasline-ads`). **Read-only prema sajtu, bazi i Google nalozima** — samo
čitanje izveštaja u browseru, bez izmena, bez backup-a jer nije bilo
destruktivnog rada.

## Šta je urađeno

Prvo očitavanje Search Console **Performance › Generative AI features (Beta)**
za `sc-domain:antasline.com`, period 3 meseca (≈18.05–09.08.2026).

- **~17.000 prikaza / 112 stranica** = **~13%** od ukupnih 129K Web prikaza
- Snimljeno u [[analiza/2026-08-12-genai-baseline]] (top 33 stranice; rep ispod
  41 prikaza nije snimljen pojedinačno — nema analitičku vrednost)
- Stavka **A** u [[migracija/2026-08-10-pre-migration-checklist]] štiklirana
- Zadatak **5.5** u master planu dopunjen

Razlog za rok: izveštaj je vezan za URL-ove, a migracija 24.08 ih menja. Bez
ovog snimka posle live-a nema odgovora na „da li smo izgubili AI vidljivost
preseljenjem".

## Otvorene akcije

- [ ] Ponoviti očitavanje **~07.09** (~2 nedelje posle live-a) i uporediti
      stranicu po stranicu #claude-code
- [ ] Glavni zadatak koji je predložen a nije započet: **provera ivica na
      tabelama specifikacija** (Chrome 149 izbacio `border-color: gray` iz UA
      stila) — CSS izmena, mora **pre freeze-a 16.08** #claude-code

## Beleške / odluke

**1. AI prikazi NISU dodatan saobraćaj.** Podskup su `Web` tipa i već su
uračunati u 129K. Ne sabirati ih sa Web prikazima ni u jednom izveštaju.

**2. Koncentracija je ekstremna i ne prati komercijalni prioritet.** Dve
stranice nose **54%** svih AI prikaza (basket 6.901 + pop-tenis 2.250), prvih
10 ≈80%. AI vidljivost ovog sajta je sportski sadržaj, ne industrijski podovi.
To nije nužno loše (sport je stvarna ponuda), ali znači da bi gubitak baš tih
URL-ova u migraciji odneo polovinu AI vidljivosti odjednom.

**3. 🔴 `/sportske-podloge/kosarkaske-konstrukcije/` = 196 AI prikaza.** Ista
stranica koju [[CLAUDE]] §7.4 vodi kao kritičnu rupu redirect mape (478 GSC
klikova, traži pravu landing stranicu umesto 301 na shop kategoriju). Sada ima
i drugu vrstu vrednosti koja se gubi ako F5 promaši.

**4. Conquest radi i u AI odgovorima** — `/epoksidni-podovi-ili-ecotile-podovi/`
488 prikaza. Epoksid tražnja stiže kroz AI, ne samo kroz klasičnu pretragu.

**5. Duplikat parket/pločice potvrđen nezavisnim izvorom** — `-2` varijanta 459
prikaza vs originalna 81, isti odnos kao u GSC klikovima. Odluka od 30.07 da
`-2` ostaje bila je ispravna.

**6. 🆕 Gotcha — pogrešan Google nalog u browseru.** Chrome je bio prijavljen na
`cpgujam@gmail.com` (nema pristup property-ju) → GSC vraća „немате приступ овом
производу", što se lako pročita kao „izveštaj nije dostupan". Drugi nalog je već
bio prijavljen; prebacivanje kroz avatar meni, bez unosa lozinke. URL posle
prebacivanja nosi `/u/1/` — to je najbrža provera. Zapisano u
[[reference/naucene-lekcije]].

**7. Direktan URL izveštaja se ne pogađa.** `search.google.com/search-console/generative-ai`
je **404**; tačan put je `/performance/search-analytics/ai` ili „Open report"
banner na Performance strani.

## Veze
- [[analiza/2026-08-12-genai-baseline]] — sam baseline
- [[seo/geo-ai-plan]] §0.1/§0.2
- [[analiza/2026-07-22-ai-test-baseline]] — ChatGPT test (ne-Google asistenti)
- [[migracija/2026-08-10-pre-migration-checklist]] §A
