---
tip: strategija
datum: 2026-07-04
naziv: GEO / AI vidljivost — da AI preporučuje Antasline
status: aktivan
azurirano: 2026-08-12
---

# 🤖 GEO plan — kako da AI (ChatGPT, Gemini, Perplexity…) preporučuje Antasline

**Dokaz da se već dešava:** GA4 kanal "AI Assistant" — chatgpt.com referrali, 9 korisnika/90d, engagement 100% ([[analiza/2026-07-04-snapshot-full]] §2.2). Za nišni srpski B2B ovo je asimetrična prilika: AI odgovori na srpskom se oslanjaju na malo dostupnih izvora — ko je najbolje strukturiran izvor u niši, dobija nesrazmerno mnogo preporuka.

## 0. Google-ova zvanična uputstva (pročitano 2026-08-12) — šta potvrđuju, šta obaraju

Google je objavio [AI optimization guide](https://developers.google.com/search/docs/fundamentals/ai-optimization-guide).
Poruka je izričita: **generativne funkcije se oslanjaju na iste ranking sisteme
kao Search**, pa „AEO/GEO taktike" kao zaseban posao nisu potrebne.

**Potvrđuje ono što već radimo** (sekcije 2–4 ovog plana): jedinstven sadržaj sa
stvarnim stavom umesto prepričavanja · jasna struktura sa opisnim naslovima ·
kvalitetne slike/video uz tekst · stranica mora biti indeksirana i **eligible za
snippet**.

🔴 **Obara (ili bar degradira) ove pretpostavke:**

| Pretpostavka | Google kaže |
|---|---|
| `llms.txt` / `llms-full.txt` pomažu AI vidljivosti | **„Google Search ih ne koristi"** — niti štete niti pomažu |
| Sadržaj treba „iseckati" u sitne delove radi AI parsiranja | Nije potrebno |
| Treba pisati posebno za AI | Ne — nema posebnog stila ni markupa |
| Structured data je poluga za AI vidljivost | Opciono; korisno za rich results, **nije uslov** za AI |
| Ciljati svaku varijantu ključne reči | To pravi commodity sadržaj i pada pod **scaled content abuse** |

**Šta to znači za nas konkretno:** naš `llms.txt`/`llms-full.txt`
(deployovan 23.07) i naše sopstveno merenje se poklapaju —
[[analiza/BOT-CRAWLER-LOG]] beleži **0 organskih hitova** na oba fajla kroz dva
preseka. **Odluka: fajlovi ostaju** (statični, bez održavanja, mogu koristiti
ne-Google asistentima koji nisu pokriveni ovim dokumentom), ali se **ne
proširuju i ne prate više kao GEO poluga**. Stavku iz sekcije 5 ovog plana
(„prati da li AI botovi povlače llms.txt") time zatvaramo — pitanje je
odgovoreno, ne treba više preseka radi njega.

🆕 **Novo merenje:** Search Console ima **Generative AI performance report**.
To je jedini legitiman izvor za vidljivost u AI odgovorima — treći alati koji
tvrde da imaju „pristup Google-ovim internim metrikama" se ignorišu. Dodati u
mesečni snapshot uz „AI Assistant" GA4 kanal (sekcija 5) i uz mesečni ChatGPT
test promptova, koji ostaje jer meri **ne-Google** asistente.

### 0.1 Generative AI performance report — šta stvarno daje (dokumentacija, 2026-08-12)

Najavljen u junu 2026, [dokumentacija za Search](https://support.google.com/webmasters/answer/16984139)
(postoji i zasebna verzija za Discover).

| | |
|---|---|
| **Pokriva** | AI Overviews i **AI Mode**. Search Labs eksperimenti su izričito isključeni |
| **Metrika** | 🔴 **Samo prikazi (impressions)** — koliko puta je link ka nama prikazan u AI funkciji. **Nema klikova, CTR-a ni pozicije** |
| **Dimenzije** | stranice · zemlje · uređaji · datumi (Pacific Time) |
| **Odnos prema glavnom izveštaju** | Nije odvojen skup: **uključuje podatke iz `Web` tipa** glavnog Performance izveštaja. Dakle naši dosadašnji GSC brojevi **već sadrže** AI prikaze — ovaj izveštaj ih samo izdvaja |
| **API** | ❌ Nije pomenut u dokumentaciji — praktično **UI-only**. Naš `gsc_report.py` (Search Analytics API, `dimensions: ["query"]`) ga ne može povući |
| **Ograničenja** | Rollout je delimičan (nemaju sve property-je) · traži dovoljan broj prikaza da se uopšte pojavi · standardno ograničenje 1.000 redova · najnoviji podaci su preliminarni |

**Šta to znači za nas praktično:** ovo je **ručno očitavanje iz UI-ja jednom
mesečno**, ne nešto što konektor može da automatizuje. Vrednost je u
**stranicama** — koje naše stranice AI uopšte citira — jer se to poredi sa
mesečnim ChatGPT testom i pokazuje da li isti sadržaj prolazi kod oba tipa
asistenta. Bez klikova, ovo **nije kanal za konverzije**, nego signal
vidljivosti; ne graditi očekivanja o saobraćaju na njemu.

### 0.2 🔴 Provera koja se lako previdi — *Search generative AI control*

U Search Console-u postoji podešavanje **Settings → Search generative AI**
([direktan link](https://search.google.com/search-console/settings/search-gen-ai))
koje određuje da li naš sadržaj sme da se pojavi u AI Overviews / AI Mode /
Discover AI funkcijama.

- Opcije: **Include** (podrazumevano) · Exclude · Inherit from parent
- Isključivanje **ne utiče** na obično rangiranje ni indeksiranje — kontrola se
  ne koristi kao ranking signal
- 🔴 **Vredi jednom potvrditi da stoji na „Include"** za `sc-domain:antasline.com`
  — ceo ovaj plan pretpostavlja da smemo da se pojavimo u AI odgovorima. Ako je
  neko (ili neki alat) to ikad prebacio, sav GEO rad je bez efekta na Google
  strani, a nigde drugde se to ne bi videlo
- Ide u isti prolaz kroz GSC UI kao 3 već otvorene stavke (zastareo sitemap
  unos, upozorenja, email alerti) — v. [[PROGRESS]] Blokeri

### Sadržaj pisan uz pomoć AI-ja — [gen-AI politika](https://developers.google.com/search/docs/fundamentals/using-gen-ai-content)
- AI kao alat je **dozvoljen**; problem je *scaled content abuse* — mnogo
  stranica bez dodate vrednosti. Naš W2 model (20 stranica, svaka sa svojim
  klasterom, cenama i FAQ-om) nije to, ali „generiši 50 varijanti za svaki grad"
  bi bilo — ne raditi.
- Preporučena transparentnost o načinu nastanka sadržaja.
- 🔴 **Ako se ikad krene sa Merchant Center-om**: AI-generisane slike moraju
  nositi IPTC `DigitalSourceType` = `TrainedAlgorithmicMedia`, a AI-generisani
  podaci o proizvodu se posebno označavaju. Tiče se `/gemini-vizuali` izlaza.

## Kako AI bira koga preporučuje

| Kanal | Odakle vuče | Brzina efekta |
|---|---|---|
| ChatGPT Search / Perplexity / AI Overviews | živi web (crawl + citati) | nedelje |
| Gemini / Google AI lokalni odgovori | GMB + recenzije + web | nedelje |
| Trening podataka budućih modela | web arhive, forumi, direktorijumi, PR | meseci–godine |

---

## Akcije

### 1. Tehnički pristup za AI crawlere ⭐⭐⭐ (5 min + odluka)
- [x] ✅ ZATVORENO — potvrđeno 2026-07-27 (curl na `https://www.antasline.com/robots.txt`): live fajl (fizički od 2026-07-23) blokira samo `AhrefsBot`/`SemrushBot`/`DotBot`, generički `User-agent: *` blok dozvoljava sve ostalo osim par WooCommerce/admin putanja (bez uticaja na sadržaj) — `GPTBot`/`OAI-SearchBot`/`PerplexityBot`/`ClaudeBot`/`Google-Extended` nikad nisu bili eksplicitno blokirani, prolaze kroz wildcard. Nema potrebe za posebnim allow-pravilima.
- [x] ✅ ZATVORENO 2026-07-08 (lokal) + ✅ 2026-07-23 **deployovan direktno na LIVE** (`[cpanel-live]`, `~/public_html/llms.txt`, 200/text-plain, verifikovan) — ranije pravilo "aktivira se na migraciji" preskočeno na M zahtev, isti obrazac kao 2542 GEO fix (ranije GEO indeksiranje) → [[DNEVNIK-NAPRETKA]]
- [x] ✅ 2026-07-23 — **`llms-full.txt` (pratilac fajl, pun tekst 7 ključnih stranica) deployovan na LIVE**, `~/public_html/llms-full.txt`, 200/charset=utf-8 → [[DNEVNIK-NAPRETKA]]. Napomena: adoptacija ovog fajla od strane glavnih AI asistenata (ChatGPT/Perplexity/Google) NIJE zvanično potvrđena — nizak trošak, neizvestan efekat; realna GEO poluga ostaje stvarni sadržaj stranica + GMB recenzije + PR/direktorijumi (v. sekcije 2/4 ovog plana)
- Sajt je server-rendered WordPress ✓ (AI crawleri ne izvršavaju JS — mi smo OK)

### 2. Citabilan sadržaj ⭐⭐⭐ (već u toku kroz C3)
AI citira stranice sa jasnim činjenicama, tabelama i brojevima. **[[seo/plan-novih-stranica]] je istovremeno GEO plan** — "dimenzije terena", "šljaka", cena tabele = tačno pitanja koja ljudi kucaju u ChatGPT.
- [x] ✅ 2026-07-22 — audit 15 ključnih stranica: 12 W1-rebuild hub-ova već ispravno, 3 legacy posta (šljaka/padel/odbojka) popravljena sa "Kratak odgovor" uvodom. Pravilo ostaje aktivno za svaku BUDUĆU novu/refresh stranicu. #claude-code
- 🔁 **Standing pravilo, ne jednokratan zadatak** — FAQ blok + FAQPage schema je deo F7 content standarda (`[[migracija/woodmart-sabloni]]`), primenjuje se na svaku novu/refresh stranicu pojedinačno (verifikovano po stranici u odgovarajućim sesijama, npr. štamparije/#17, 2542/#3.10). Uklonjena kućica 2026-07-27 — nema jednog "gotovo" stanja da se štriklira, prati se kroz [[seo/plan-novih-stranica]] po stavci.
- [x] ✅ 2026-07-22 — dodata doslovna fraza "alternativa epoksidnom podu za proizvodnu halu" (uvod + novo FAQ pitanje, vidljivo + JSON-LD) na lokalu; live deo pripremljen kao poseban prompt ([[migracija/2026-07-22-prompt-live-2542-geo-fix]]), #ceka-miroslav da pokrene. GSC pozicija (~26) i dalje otvorena — ovo je samo tekstualno GEO poklapanje, ne rang fix.

### 3. Entitet — nedvosmislen identitet ⭐⭐ (1–2h)
- [x] ✅ ZATVORENO 2026-07-08 — `Organization` (Yoast, već postojao sa `sameAs`) proširen filterom na `LocalBusiness` + adresa/telefon (NAP) → [[DNEVNIK-NAPRETKA]]
- [x] ✅ Već gotovo od 2026-07-07 — "O nama" ima proverljive činjenice (15+ godina, brendovi Ecotile/Bergo/Sit-in, imenovane reference HTEC/Bosch/Institut Vinča itd.)
- [x] ✅ ZATVORENO 2026-07-27 `[cpanel-live]` — poštanski broj na `/kontakt/` ispravljen 11050→11000 (post 558, 5 DB redova: `panels_data`×2, `_panels_data_preview`×2, orphan `zn_page_builder_els`; backup pre izmene, verifikovano 0×"11050"/200/regresija čista) → [[reference/naucene-lekcije]], [[DNEVNIK-NAPRETKA]].
- [x] ✅ ZATVORENO 2026-08-06 — GMB profil i eksterni direktorijumi provereni preko web pretrage (WebSearch/WebFetch, read-only). **11000 je dominantan i tačan svuda gde ima izvor** — Google-ov sopstveni sažetak (verovatno vuče iz GMB Knowledge panela), `mojakompanija.com` oba eksplicitno "Ulcinjska 13, 11000 Beograd". 🟡 **Dva manja nalaza, van naše kontrole (ne WP sajt), #ceka-miroslav ako želi da ih ispravi**: (1) `gradnja.rs/adresar` navodi broj **"Ulcinjska 13-15"** umesto "13" — nepoznato da li je greška direktorijuma ili stariji podatak; (2) `planplus.rs` (uličnI/poštanski registar, ne firma-specifičan unos) navodi da je Ulcinjska ulica u opštini Zvezdara zvanično pod poštanskim brojem **11050**, što je suprotno M odluci od 07-27 da 11000 ostaje — moguće da je M odluka namerno pojednostavljenje (11000 = generički Beograd kod koji ljudi/Google bolje prepoznaju) a ne greška, ali vredi da M to eksplicitno potvrdi s obzirom na nezavisan izvor. `011info.com`/`daibau.rs` ne prikazuju poštanski broj uopšte (nema šta da se ispravi). Nema izmena sajta ove sesije (eksterni direktorijumi nisu pod našom kontrolom, uređivanje zahteva M pristup njihovim panelima).

### 4. Pominjanja treće strane ⭐⭐⭐ (najjači signal, traje)
AI za "najbolji X u Srbiji" agregira tuđe liste, portale, forume:
- [ ] **PR o projektima**: Spanoulis court + Dunk Shop teren → sportski/lokalni portali (besplatna priča!) #ceka-miroslav
- [x] **Case studije sa imenima** — delimično ✅ 2026-07-12: Quectel (već postojao, 5163) + HTEC (novo, post 17021) imaju dovoljno realnog materijala (fotke koje otkrivaju kontekst) za poštenu case-study stranicu, oba sad linkovana sa `/industrijski-podovi/` reference reda. Hankook i Amicus ostaju otvoreni — samo generička referentna fotka bez detalja, nedovoljno za naraciju bez izmišljanja → #ceka-miroslav (konkretni detalji projekta: kada, koliko m², koji problem su rešili)
- [ ] Upisi u domaće direktorijume + građevinske portale #ceka-miroslav
- [ ] GMB recenzije 6 → 20+ (već u [[analiza/2026-07-04-snapshot-full]] §6.3) #ceka-miroslav — isto kao Master Plan M4/5.3, rok 2026-07-31, review link već spreman, čeka da se posla obave

### 5. Merenje ⭐⭐ (u snapshot rutini)
- [ ] GA4: pratiti "AI Assistant" kanal mesečno — baseline **9 korisnika/90d** — nije poseban zadatak, ide u sledeći puni mesečni snapshot (početak avgusta, [[PROGRESS]] W5 5.4)
- [x] ✅ NOVO 2026-07-23 — `[[analiza/BOT-CRAWLER-LOG]]` pokrenut: access log analiza svih bot/crawler hitova (AI asistenti/search/SEO-alati), baseline presek. ✅ **Pod-pitanje „da li botovi povlače `llms.txt`" ZATVORENO 2026-08-12** — 0 organskih hitova kroz 2 preseka, a Google je u međuvremenu napismeno potvrdio da Search te fajlove ne koristi (v. §0). Ne ponavljati presek radi ovoga; bot log ostaje koristan za ostale crawlere.
- [x] ✅ **2026-08-12 — M potvrdio: `Settings → Search generative AI` je na „Include"** (v. §0.2). Sadržaj sme u AI Overviews/AI Mode; GEO plan nema skrivenu kočnicu. Ne otvarati ponovo osim ako neko ne dirne podešavanje.
- [x] ✅ **BASELINE SNIMLJEN 2026-08-12** → [[analiza/2026-08-12-genai-baseline]]. ~17.000 prikaza / 112 stranica (3 mes.), ≈13% od ukupnih Web prikaza. **Dve stranice nose 54%** (basket 6.901 + pop-tenis 2.250) — AI vidljivost ovog sajta je sportski sadržaj, ne industrijski podovi. Conquest 2542 radi i u AI odgovorima (488). Ponoviti ~07.09, uporediti po stranici: pad na pojedinačnoj uz stabilan zbir = 301 problem, ne sadržajni.
- [ ] 🆕 **Generative AI performance report — ✅ DOSTUPAN za našu property** (M potvrdio 2026-08-12; nismo ispali iz delimičnog rollout-a). **UI-only, API ga ne izlaže** (v. §0.1) → ručno očitavanje u mesečnom snapshotu: prikazi **po stranicama**, pa poređenje sa mesečnim ChatGPT testom (isti sadržaj kod Google i ne-Google asistenta). 🔵 **Prvo očitavanje još nije urađeno.** Vredi uzeti **baseline pre migracije 24.08** — posle promene URL-ova poređenje „pre/posle" bez baseline-a nije moguće. #claude-code (preko browsera) ili M ručno
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
