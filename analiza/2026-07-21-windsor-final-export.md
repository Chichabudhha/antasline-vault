---
tip: analiza
datum: 2026-07-21
svrha: puna arhiva svih Windsor.ai konektora pre otkazivanja pretplate
azurirano: 2026-07-21
---

# Windsor.ai — finalni izvoz (pretplata se otkazuje)

Miroslav otkazuje Windsor.ai pretplatu. Ovo je poslednji pun izvoz preko sva 4 konektora
(`google_ads`, `googleanalytics4`, `searchconsole`, `google_my_business`) dok je pristup još
aktivan — sve što bi moglo zatrebati kasnije je ovde sačuvano lokalno. Posle otkazivanja: GA4,
Google Ads i GSC i dalje imaju svoje besplatne native UI (ništa se ne gubi tamo), ali gubi se
mogućnost da Claude Code direktno povlači/ukršta podatke iz sve 4 platforme u istoj sesiji —
ubuduće ručni izvoz ili copy-paste iz UI-a.

Vezano: [[analiza/2026-07-21-serp-snapshot-pre-migracija]] (top 20 GSC upita, isti dan) — nije
ponovljeno ovde, samo linkovano.

---

## 1. Google Ads (`156-886-0314`, "Gogin Nalog")

### 1.1 Kampanje — poslednjih 90 dana (24.04–21.07.2026)

| Kampanja | Status | Trošak (RSD) | Klikovi | Impresije | CTR | CPC | Konverzije |
|---|---|---:|---:|---:|---:|---:|---:|
| Podloge za terase i bazene | ENABLED | 20.248 | 1.119 | 6.350 | 17,62% | 18,09 | 4 |
| ECOTILE INDUSTRIJSKI PODOVI | ENABLED | 14.848 | 448 | 2.549 | 17,58% | 33,14 | 6 |
| Ecotile - Antas line | PAUSED | 5.625 | 281 | 1.917 | 14,66% | 20,02 | 0 |
| podovi za baste | PAUSED | 5 | 5 | 128 | 3,91% | 1,01 | 0 |

**Ukupno 90d: ~40.726 RSD, 1.853 klika, 10 uvezenih konverzija.**

### 1.2 🔴 Nalaz — spend je pao na nulu od 2026-07-05

Dnevni pregled (21.06–21.07) pokazuje da je stvarna isporuka stala posle **2026-07-04**:

| Period | Dnevni trošak |
|---|---|
| 21.06–04.07 | 145–1.129 RSD/dan (normalan ritam) |
| **05.07–21.07** | **0 RSD skoro svaki dan** (samo 5 dana sa 1 impresijom, 0 klikova, 0 troška) |

Kampanje i dalje pokazuju status `ENABLED` u Windsor-u, ali realna potrošnja je 17 dana ravna
nuli — poklapa se sa napomenom u dnevniku "Ads pauzirane (M godišnji odmor)", ali **nije
potvrđeno da li je ovo namerna pauza (budžet na 0, ručno zaustavljeno u Ads UI) ili tehnički
problem** (kao ranije ECOTILE throttling, [[reference/naucene-lekcije]]). **#ceka-miroslav:
proveriti u Ads UI po povratku — da li je ovo svesna odluka ili nešto što treba reaktivirati.**

### 1.3 Ključne reči — najveći trošak, 90 dana

| Kampanja | Ključna reč | Status | Klikovi | Trošak (RSD) | Konverzije |
|---|---|---|---:|---:|---:|
| ECOTILE INDUSTRIJSKI PODOVI | industrijski podovi | ENABLED | 58 | 3.369 | 5 |
| Podloge za terase i bazene | pvc podovi za bazene | ENABLED | 172 | 3.119 | 1 |
| Ecotile - Antas line | antistatik pod | ENABLED | 107 | 2.187 | 0 |
| Podloge za terase i bazene | podovi za terase | ENABLED | 131 | 2.300 | 1 |
| Podloge za terase i bazene | podne obloge za terase | ENABLED | 129 | 2.213 | 1 |
| Ecotile - Antas line | industrijski podovi | ENABLED | 83 | 1.667 | 0 |
| ECOTILE INDUSTRIJSKI PODOVI | podovi za radionice | ENABLED | 24 | 1.339 | 0 |
| Podloge za terase i bazene | podloga za terase | ENABLED | 67 | 1.169 | 0 |

⭐ "industrijski podovi" (ECOTILE kampanja) ostaje najefikasnija: 5 konverzija za 3.369 RSD =
~674 RSD/konverzija — potvrđuje raniji nalaz da je ovo najjeftiniji B2B klaster (v. [[analiza/2026-07-04-ads-st-analiza-16m]]).

Mnogo REMOVED ključnih reči vidljivo u istom pull-u (npr. "podne ploče za terasu", "industrijski
podovi" pod starim ECOTILE kampanjama) — ovo je trag ad-grupe restrukturiranja (Faza 2, M2/4.4)
koje je već ranije sprovedeno, potvrda da čišćenje drži.

### 1.4 Search termini — posle negativne liste (06.07–21.07, period posle M2 čišćenja)

Samo **2 reda ukupno** u ovom prozoru — "industrijski podovi beograd" (1 impresija, 0 klika) i
1 red bez search term-a. Toliko malo termina je direktna posledica nalaza 1.2 (spend = 0) —
nema dovoljno isporuke da bi se ocenila efikasnost negativne liste na svežim podacima. Ranija
ocena curenja (16.607 RSD / 15,4% pre čišćenja) ostaje jedini pouzdan benchmark, sačuvan u
[[analiza/2026-07-04-ads-st-analiza-16m]].

### 1.5 Istorijski trend kampanja — 16 meseci (mart 2025 – jun 2026)

| Mesec | Kampanja | Trošak (RSD) | Klikovi | Konverzije |
|---|---|---:|---:|---:|
| Mar 2025 | Website traffic-Performance Max-Industrijski podovi | 4.822 | 2.161 | 35 |
| Apr 2025 | Website traffic-PMax | 1.258 | 591 | 6 |
| Apr 2025 | podovi za baste | 3.362 | 178 | 0 |
| Apr 2025 | Ecotile - Antas line | 1.635 | 76 | 0 |
| May 2025 | podovi za baste | 2.669 | 181 | 0 |
| May 2025 | Ecotile - Antas line | 336 | 16 | 0 |
| Sep 2025 | podovi za baste | 1.213 | 47 | 0 |
| Sep 2025 | Ecotile - Antas line | 11.859 | 558 | 0 |
| Oct 2025 | Ecotile - Antas line | 14.067 | 666 | 0 |
| Nov 2025 | Ecotile - Antas line | 15.234 | 721 | 0 |
| Dec 2025 | Ecotile - Antas line | 18.864 | 887 | 0 |
| Jan 2026 | Ecotile - Antas line | 13.855 | 658 | 0 |
| Jan 2026 | Website traffic-PMax | 220 | 24 | 0 |
| Feb 2026 | Ecotile - Antas line | 11.801 | 606 | 0 |
| Mar 2026 | Ecotile - Antas line | 9.926 | 504 | 0 |
| Mar 2026 | podovi za baste | 2.657 | 112 | 0 |
| Apr 2026 | podovi za baste | 1.218 | 6 | 0 |
| Apr 2026 | Ecotile - Antas line | 7.493 | 369 | 0 |
| May 2026 | podovi za baste | 5 | 4 | 0 |
| May 2026 | Ecotile - Antas line | 3.817 | 189 | 0 |
| Jun 2026 | Ecotile - Antas line | 206 | 11 | 0 |
| Jun 2026 | podovi za baste | 0,09 | 1 | 0 |
| **Jun 2026** | **ECOTILE INDUSTRIJSKI PODOVI** (nova) | 12.791 | 409 | 4 |
| **Jun 2026** | **Podloge za terase i bazene** (nova) | 17.687 | 974 | 3 |

**Čitanje:** "Ecotile - Antas line" je bila glavna kampanja od apr 2025 do maj 2026, uvek sa 0
uvezenih konverzija (Ads konverzioni uvoz je proradio tek od juna 2026, v. [[CLAUDE]] §4) — u
junu 2026 zamenjena sa dve novije kampanje ("ECOTILE INDUSTRIJSKI PODOVI" + "Podloge za terase i
bazene") koje odmah pokazuju konverzije. Nema praznina u mesečnom nizu koja bi ukazivala na
propušten period bez ikakve aktivnosti — jedina prava praznina je jul 2025–avg 2025 (samo dve
manje kampanje aktivne).

---

## 2. GA4 (`292720335`, "AntasLine - GA4")

### 2.1 Sesije/korisnici — 16 meseci (mart 2025 – jun 2026)

| Mesec | Sesije | Korisnici |
|---|---:|---:|
| Mar 2025 | 5.633 | 4.311 |
| Apr 2025 | 6.226 | 4.767 |
| May 2025 | 6.151 | 4.740 |
| Jun 2025 | 4.663 | 3.613 |
| Jul 2025 | 3.250 | 2.841 |
| Aug 2025 | 3.446 | 2.804 |
| Sep 2025 | 4.603 | 3.531 |
| Oct 2025 | 4.599 | 3.468 |
| Nov 2025 | 4.281 | 3.300 |
| Dec 2025 | 3.863 | 2.878 |
| Jan 2026 | 3.897 | 2.993 |
| Feb 2026 | 5.034 | 3.862 |
| Mar 2026 | 6.208 | 4.869 |
| Apr 2026 | 5.270 | 4.215 |
| May 2026 | 4.766 | 3.769 |
| Jun 2026 | 5.376 | 4.328 |

Sezonski obrazac potvrđen: proleće (mart–maj) dosledno najjači period oba puta (2025 i 2026),
leto (jul–avg) pad ~40-45% — u skladu sa GSC špic mar–maj napomenom u master planu §7 (W7).

### 2.2 Key eventi — 16 meseci (generate_lead / tel / mailto)

| Mesec | generate_lead | tel | mailto |
|---|---:|---:|---:|
| Mar 2025 | 99 | 81 | 9 |
| Apr 2025 | 93 | 80 | 14 |
| May 2025 | 92 | 93 | 13 |
| Jun 2025 | 74 | 76 | 17 |
| Jul 2025 | 88 | 37 | 6 |
| Aug 2025 | 64 | 37 | 5 |
| Sep 2025 | 89 | 54 | 7 |
| Oct 2025 | 138 | 82 | 7 |
| Nov 2025 | 128 | 58 | 4 |
| Dec 2025 | 162 | 39 | 2 |
| Jan 2026 | 121 | 39 | 4 |
| Feb 2026 | 107 | 84 | 10 |
| Mar 2026 | 141 | 104 | 14 |
| Apr 2026 | 125 | 77 | 9 |
| May 2026 | 159 | 81 | 9 |
| Jun 2026 | 124 | 69 | 16 |

⚠️ **Napomena za tumačenje**: `generate_lead` pre 27.06.2026 je merio staru definiciju (pregled
`/kontakt`), ne pravi submit forme (BLOK A prevezivanje) — brojevi pre tog datuma NISU
uporedivi sa julom 2026 na dalje (v. [[CLAUDE]] §4). Jul 2026 (do 20.7) = 124 generate_lead + 69
tel + 16 mailto po ranijoj W5 5.1 proveri, projekcija ~92/mes na novoj definiciji — bitno niže
od istorijskih brojeva jer stara definicija (kontakt-pregled) preterano broji.

### 2.3 Kanali (90d, session_default_channel_group)

| Kanal | Sesije | Korisnici | Engagement rate |
|---|---:|---:|---:|
| Organic Search | 7.203 | 5.836 | 65,06% |
| Direct | 3.461 | 3.012 | 38,34% |
| Paid Search | 1.665 | 1.562 | 63,00% |
| Unassigned | 1.314 | 1.246 | 1,45% |
| Referral | 88 | 25 | 72,73% |
| Organic Social | 68 | 65 | 83,82% |
| **AI Assistant** | 28 | 22 | 67,86% |
| Cross-network | 24 | 23 | 75,00% |

Organic 90d udeo ≈ 55% sesija (7.203/13.061 ukupno u ovoj tabeli) — u skladu sa "94% organski"
napomenom iz starijeg jun-baseline-a (taj broj se odnosio na lead-ove, ne na sve sesije).
**"AI Assistant" kanal potvrđen aktivan** (28 sesija/90d) — GEO paket (llms.txt, LocalBusiness
schema, W2 2.8) generiše merljiv, mada mali, saobraćaj; baseline za mesečni AI test (5.5).

### 2.4 Uređaji (90d)

| Kategorija | Sesije | Korisnici | Bounce rate |
|---|---:|---:|---:|
| Mobile | 9.716 | 7.818 | 47,08% |
| Desktop | 4.170 | 2.671 | 50,77% |
| Tablet | 73 | 53 | 49,32% |

Mobile udeo = 69,5% sesija (90d) — nešto niže od jun-baseline "74% GA4", ali i dalje dominantan
kanal; potvrđuje da CWV mobile-first prioritet (W3 3.6) ostaje ispravan fokus.

### 2.5 Landing page → key event (90d, top redovi)

| Landing page | Event | Broj |
|---|---|---:|
| /kontakt | generate_lead | 139 |
| / (početna) | generate_lead | 47 |
| /vestacka-trava | tel | 35 |
| /sportske-podloge | tel | 35 |
| /industrijski-podovi | tel | 25 |
| /sportske-podloge | generate_lead | 21 |
| / (početna) | tel | 21 |
| /spoljnje-podne-obloge | generate_lead | 20 |
| /sportske-podloge/kosarkaske-konstrukcije | generate_lead | 11 |
| /industrijski-podovi | generate_lead | 11 |
| /spoljnje-podne-obloge/bergo-xl | generate_lead | 9 |
| /spoljnje-podne-obloge | tel | 9 |

`/kontakt` dominira generate_lead (očekivano — to je prefill odredište iz "Zatražite ponudu",
W1 1.8) — potvrđuje da katalog_mode tok radi kako je dizajniran. `/vestacka-trava` i
`/sportske-podloge` su najjači tel-izvori van kontakt forme, dobri kandidati za dodatni CTA
fokus ako se traži brz dobitak.

---

## 3. Google Search Console (`sc-domain:antasline.com`)

Top 20 upita (28d) + rizik-grupa već sačuvani u [[analiza/2026-07-21-serp-snapshot-pre-migracija]].
Ovde dopuna: stranice, uređaji, sitemap status (90d prozor osim gde je naznačeno).

### 3.1 Top stranice po klikovima (90d)

| Stranica | Klikovi | Impresije | CTR | Pozicija |
|---|---:|---:|---:|---:|
| /kako-napraviti-teren-za-basket-ili-kosarkaski-teren | 973 | 28.458 | 3,42% | 2,66 |
| /sportske-podloge | 907 | 8.420 | 10,77% | 5,68 |
| /podloge-za-parkiraliste-i-staze | 626 | 7.131 | 8,78% | 5,11 |
| /sportske-podloge/kosarkaske-konstrukcije | 429 | 6.617 | 6,48% | 8,52 |
| /spoljnje-podne-obloge | 350 | 10.796 | 3,24% | 10,27 |
| /vestacka-trava | 332 | 3.944 | 8,42% | 5,82 |
| (početna) | 274 | 3.368 | 8,14% | 7,24 |
| /spoljnje-podne-obloge/bergo-xl | 266 | 4.967 | 5,36% | 5,29 |
| /industrijski-podovi | 205 | 4.909 | 4,18% | 11,16 |
| /antistatik-i-elektroprovodljivi-podovi | 170 | 2.570 | 6,61% | 6,47 |
| /podloga-za-teniske-terene | 162 | 6.000 | 2,70% | 5,95 |

🔴 **Nalaz vredan pažnje**: `/industrijski-podovi` (silo hub) na poziciji 11,16 i
`/spoljnje-podne-obloge` (silo hub) na poziciji 10,27 — oba glavna silo huba su van top10,
uprkos visokim impresijama (4.909 i 10.796). Ovo je dobar kandidat za budući W2/W3 zadatak
(interno linkovanje/sadržaj hub stranica) kad se prioriteti otvore posle migracije — nije
hitno, ali vredi zabeležiti dok je podatak svež.

### 3.2 Uređaji (90d)

| Uređaj | Klikovi | Impresije | CTR | Pozicija |
|---|---:|---:|---:|---:|
| Mobile | 4.979 | 99.606 | 5,00% | 5,26 |
| Desktop | 1.619 | 29.829 | 5,43% | 8,09 |
| Tablet | 38 | 654 | 5,81% | 8,62 |

Mobile = 75,4% klikova — potvrđuje jun-baseline "76% GSC" (v. [[CLAUDE]] §0.11), stabilno.
Zanimljivo: desktop i tablet imaju blago BOLJI CTR ali lošiju prosečnu poziciju — mobile
dominira volumenom, ne kvalitetom pozicije.

### 3.3 Sitemap status

| Sitemap | Poslato URL-ova | Greške | Upozorenja | Poslednje preuzimanje |
|---|---:|---:|---:|---|
| antasline.com/sitemap_index.xml (http, staro) | 395 | 0 | 3 | 2026-07-20 |
| antasline.com/sitemap_index.xml (https, aktivno) | 395 | 0 | 4 | 2026-07-21 |

0 grešaka na oba — potvrđuje da W3 3.7 čišćenje (25 orphan CPT postova → draft, isti dan) nije
pokvarilo ništa, i da Google redovno crawluje (poslednje preuzimanje = danas). 3-4 upozorenja
postoje ali nisu specificirana poljima ovog konektora — proveriti direktno u GSC UI ako zatreba
detalj (Windsor ne izlaže tekst upozorenja).

---

## 4. Google My Business ("Industrijski i sportski podovi Beograd - Antas Line")

### 4.1 🔴 Recenzije i objave — Windsor NE vraća podatke

`Reviews` i `LocalPosts` tabele (`review_comment`, `review_star_rating`, `post_summary`, itd.)
vraćaju **prazan rezultat** iz ovog konektora — nema podataka za recenzije ni objave preko
Windsor-a (moguće ograničenje scope-a autorizacije, ne greška u upitu — polja postoje u
`get_fields`, samo API ne vraća redove). Pošto CLAUDE.md pominje "6 recenzija" i "prvi post
kreiran" kao poznato stanje — **posle otkazivanja pretplate, recenzije/objave se prate isključivo
direktno kroz Google Business Profile dashboard**, Windsor nikad nije bio pouzdan izvor za ovo.

### 4.2 Performanse — 16 meseci

| Mesec | Pozivi | Klik na sajt | Zahtevi za pravac | Impr. desktop maps | Impr. desktop search | Impr. mobile maps | Impr. mobile search |
|---|---:|---:|---:|---:|---:|---:|---:|
| Mar 2025 | 47 | 35 | 26 | 18 | 69 | 226 | 2.007 |
| Apr 2025 | 17 | 23 | 54 | 20 | 102 | 77 | 821 |
| May 2025 | 3 | 18 | 21 | 31 | 109 | 56 | 402 |
| Jun 2025 | 4 | 5 | 36 | 10 | 100 | 41 | 299 |
| Jul 2025 | 2 | 7 | 60 | 21 | 64 | 53 | 175 |
| Aug 2025 | 1 | 10 | 57 | 22 | 104 | 40 | 347 |
| Sep 2025 | 0 | 8 | 55 | 25 | 109 | 49 | 274 |
| Oct 2025 | 0 | 8 | 19 | 25 | 121 | 44 | 308 |
| Nov 2025 | 1 | 4 | 69 | 13 | 79 | 63 | 236 |
| Dec 2025 | 1 | 6 | 80 | 32 | 81 | 48 | 183 |
| Jan 2026 | 0 | 3 | 61 | 16 | 81 | 48 | 157 |
| Feb 2026 | 1 | 0 | 56 | 7 | 97 | 54 | 600 |
| Mar 2026 | 0 | 2 | 68 | 11 | 77 | 41 | 246 |
| Apr 2026 | 1 | 11 | 24 | 20 | 91 | 55 | 157 |
| May 2026 | 3 | 4 | 46 | 24 | 100 | 78 | 172 |
| Jun 2026 | 1 | 10 | 55 | 13 | 83 | 64 | 130 |

**Čitanje:** poziv-klikovi (call_clicks) su drastično opali posle marta 2025 (47→jednocifreno)
i nikad se nisu oporavili — ovo je konzistentno sa UTM-fix napomenom u [[CLAUDE]] §5.2/M4 (GMB
UTM ispravljen tek 2026-07-06), pre toga GMB saobraćaj verovatno nije bio pravilno atribuiran u
GA4, ali sam GMB profil poziv-broj ne zavisi od UTM-a — pad je realan i vredi pomena kao
otvoreno pitanje (W5 5.3 M zadatak, recenzije→poverenje→pozivi lanac). Direction requests
(zahtevi za pravac) su relativno stabilni (19-80/mes) — ljudi i dalje pronalaze lokaciju.

### 4.3 Pretraga — pojmovi kojima ljudi nalaze profil (16mo, top po mesecu)

| Mesec | Vodeći pojam | Vrednost |
|---|---|---:|
| Mar 2025 | industrijski i sport | 1.592 |
| Apr 2025 | industrijski i sport | 456 |
| May 2025 | industrijski podovi | 171 |
| Jun 2025 | industrijski podovi | 134 |
| Jul 2025 | industrijski podovi | 54 |
| Aug 2025 | industrijski podovi | 108 |
| Sep 2025 | industrijski podovi | 114 |
| Oct 2025 | industrijski podovi | 123 |
| Nov 2025 | industrijski podovi | 87 |
| Dec 2025 | industrijski podovi | 68 |
| Jan 2026 | industrijski podovi | 70 |
| **Feb 2026** | **industrijski podovi** | **455** (skok) |
| Mar 2026 | industrijski podovi | 124 |
| Apr 2026 | industrijski podovi | 74 |
| May 2026 | industrijski podovi | 75 |
| Jun 2026 | industrijski podovi | 82 |
| Jun 2026 | antas line (brend) | 22 |

"industrijski podovi" dominira sitewide kao GMB search term svaki mesec (osim marta 2025 kad je
"industrijski i sport" — verovatno pun naziv profila kao pretraga, ne organska fraza). Zanimljiv
skok u februaru 2026 (455 vs tipičnih ~70-120) — nije istražen razlog, van obima ove sesije, ali
vredi zabeležiti ako neko kasnije traži objašnjenje sezonskog outlier-a. "epoxy floor" se
pojavljuje povremeno kao search term (16-18/mes u nekoliko meseci) — dodatna potvrda da epoksid
tražnja realno postoji i da conquest strategija (post 2542) ima na šta da cilja.

---

## 5. Šta uraditi posle otkazivanja pretplate

- **GA4, Google Ads, GSC**: nastaviti direktno kroz native UI (besplatno, bez vremenskog
  ograničenja) — Windsor je bio samo sloj za brže ukrštanje i Claude Code automatizaciju, ne
  jedini pristup podacima.
- **GMB**: recenzije/objave se i dalje prate isključivo kroz Google Business Profile app/dashboard
  (Windsor ih nikad nije ni vraćao pouzdano, v. §4.1).
- **Nedeljni izveštaj (W5 5.4)** i **mesečni AI test (5.5)** će ubuduće zahtevati ručni copy-paste
  iz native UI umesto automatskog Windsor pull-a — format ostaje isti ([[CLAUDE]] §10), samo je
  izvor podataka ručni.
- Ovaj fajl + [[analiza/2026-07-21-serp-snapshot-pre-migracija]] su poslednji Windsor-generisani
  snapshot-ovi — svi budući snapshot-ovi (`analiza/_TEMPLATE-snapshot.md`) će morati novi metod
  prikupljanja podataka definisan pre sledeće upotrebe.

## Veze
[[CLAUDE]] · [[PROGRESS]] · [[analiza/_README]] · [[analiza/2026-07-04-snapshot-full]] · [[analiza/2026-07-21-serp-snapshot-pre-migracija]] · [[dnevnik/ADS-DNEVNIK]] · [[reference/naucene-lekcije]]
