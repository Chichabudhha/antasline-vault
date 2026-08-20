---
name: lekcije-ads-tracking
description: Tehnicki gotchas — Google Ads API, GA4, GTM, konektor (Windsor/sopstveni), Customer Match, publike/tracking. Deo 3/4 rascepa naucene-lekcije.md (2026-08-20, vault higijena).
---

# Naucene lekcije — Ads / GA4 / GTM / konektor / tracking

> 3/4 tematskog rascepa `reference/naucene-lekcije.md` (2026-08-20). Ostala tri: [[reference/lekcije-wp-db-tehnika]] · [[reference/lekcije-seo-sadrzaj-migracija]] · [[reference/lekcije-alati-vault-delegati]]. Indeks: [[reference/naucene-lekcije]].

- Srodno: „Zapisane GSC brojke u migracionim CSV-ovima nisu pouzdane — svež pull pre svake odluke (2026-08-13)".

## `campaign.status` iz Ads API-ja nije dokaz da kampanja ne troši (2026-08-18)
- Simptom: kampanja „Podloge za terase i bazene" je 11.08 vraćena kao `campaign_status: PAUSED`, i taj status je ušao u [[migracija/2026-08-11-ads-final-url-audit]] §2.1 kao „od 14 kampanja samo je jedna ENABLED", pa i u [[dnevnik/ADS-DNEVNIK]] i [[PROGRESS]]. Sedam dana kasnije (18.08) `ads_report.py` je pokazao da je **istog tog 11.08** potrošila 222 RSD / 14 klikova, a 17.08 čak 1.643 RSD / 74 klika — najveći dan u nalogu.
- Uzrok nije bug u skripti: API je vratio tačno ono što je bilo u nalogu. Ispod PAUSED kampanje `ad_group_status` i `ad_status` su oba bila **ENABLED**, a isporuka je isprekidana (dani sa nulom se smenjuju sa danima od 900–1.600 RSD) — dakle status na nivou kampanje ne opisuje šta se stvarno servira.
- Posledica: cela nedelja analiza je kampanju tretirala kao nepostojeću — nije ušla ni u nedeljni izveštaj, ni u URL audit kao rizik, ni u razmatranje budžeta, iako ima **najjeftiniji CPC u nalogu** (20,96 vs 94,41 RSD) i donela je 3 od 5 uvezenih konverzija u nedelji 04–10.08.
- **Pravilo:** status i potrošnja se čitaju **zajedno**, i to **po danu**, ne samo za ceo period. Nijedna kampanja se ne isključuje iz analize na osnovu `campaign.status` — isključuje se tek kad `spend + impressions = 0` kroz ceo posmatrani period. Isti princip već postoji u ovom fajlu za obrnut slučaj („prazan odgovor za kampanju ne znači grešku konektora — proveri spend+impressions"); ovde važi u drugom smeru.
- Usput: direktan URL `search.google.com/search-console/generative-ai` je **404**. Tačan put je `/performance/search-analytics/ai`, ili banner „Open report" na Performance strani. Ne izmišljati URL-ove Google konzola po analogiji — otvarati kroz navigaciju.

## Podešavanje koje tiho gasi ceo kanal, a ne ostavlja nikakav trag u podacima (2026-08-12)
- Search Console ima **Settings → Search generative AI** (Include / Exclude / Inherit, podrazumevano *Include*) — određuje da li sadržaj sme u AI Overviews / AI Mode / Discover AI.
- Opasnost nije u podešavanju nego u njegovoj **nevidljivosti**: isključivanje **ne dira ni rangiranje ni indeksiranje**, pa se u GSC izveštajima, GA4-u i rankingu ne bi videlo ništa. Sav GEO rad bi bio bez efekta na Google strani, a dijagnostika bi tražila uzrok u sadržaju.
- Provereno 2026-08-12: kod nas stoji na „Include". Trošak provere — jedan klik.
- Šire: svaki `#ceka-miroslav` nalaz koji menja raniji zaključak treba **istovremeno** upisati i u tematski hub, ne samo u Blokere.

## Prerender/prefetch okida thank-you stranicu bez posetioca — konverzija se naduva (2026-08-12)
- Chrome 151 uvodi Speculation Rules `form_submission` (prerender odredišta submit-a forme); isto rade i prefetch opcije optimizacionih plugina (LiteSpeed, WP Rocket, Perfmatters).
- Kad se konverzija meri kao **page view na thank-you stranici** — a to je i naš setup i najčešći WooCommerce/CF7 setup — prerender izvrši tu stranicu **pre** nego što posetilac stigne, i tag okine na posetu koja se možda nikad ne dogodi.
- **Redosled je obavezan: prvo GTM trigger gate-ovan na `document.prerendering === false` (ili odložen na `prerenderingchange`), pa tek onda prerender.** Nikad obrnuto.
- Proveriti i pri prelasku na hosting/plugin sa agresivnim prefetch default-om — uključi se bez pitanja i tiho promeni brojke.

## Popravljen tracking bag ostavlja rupu u podacima — zatvaranje stavke mora ostaviti ✅ trag u [[PROGRESS]], ne biti obrisano (2026-08-11)
- `mailto` je bio mrtav **27.06–06.08** (pratio ga MonsterInsights, gašenje MI-ja u BLOK A ga oborilo), dijagnostikovan 27.07, popravljen 07.08 (GTM Version 14). Sve uredno u [[dnevnik/2026-08-07-gtm-mailto-tag]].
- Ali kad je stavka zatvorena, redovi su iz **PROGRESS Blokera obrisani**. Mesečni snapshot (11.08) je istu rupu ponovo otkrio u podacima i prijavio kao **nov, nedijagnostikovan nalaz** — dvaput istraženo, jednom pogrešno prijavljeno Miroslavu.
- Zašto dnevnik fajl nije pomogao: po protokolu (`/antasline-sesija` §1) na otvaranju se čitaju PROGRESS + master plan + ledger; pojedinačni `dnevnik/*.md` se čita **samo na zahtev**. Ako trag nije u ta tri fajla, praktično ne postoji.
- **Pravilo: bag koji je ostavio rupu u istorijskim podacima se ne briše iz Blokera — prepiše se u ✅ red sa TAČNIM datumskim opsegom rupe.** Analiza koja te datume ne zna nužno ih otkriva iznova i tumači kao ponašanje korisnika (npr. „`mailto` pao na nulu") umesto kao prekid merenja.
- Isti obrazac preti i drugim serijama sa poznatim prekidom: `generate_lead` (istorijski rep na `/kontakt/` do BLOK A), Windsor `conversions` kontaminacija 17–22.06, hvala-proxy pre migracije (÷2).

## „Konverzije" u Google Ads-u nisu ono što misliš dok ne pogledaš `conversion_action` podešavanja (2026-08-11)
- Kolona **„Conversions"** (i signal koji Smart Bidding uči) sadrži **samo** akcije sa `include_in_conversions_metric = True`. Kolona „All conversions" sadrži sve. Ime akcije ne govori ništa o tome u kojoj je koloni — `Clicks to call` (Google-hosted) je bila *van* kolone, a `Klik na telefon (web)` (naša, WEBPAGE) *unutra*.
- Posledica koja je stvarno nastala: pravilo iz [[CLAUDE]] §4 („ne uvoziti GA4 `tel` kao Ads konverziju") je **verovano na reč** mesecima, a u nalogu je `tel` sve vreme bio primarna konverzija — 17 od „26 plaćenih konverzija" (01.06–10.08). Ceo prag za 4.8 (Maximize Conversions) je meren pogrešnim brojačem.
- **Provera koja ovo hvata** (~30 sekundi, GAQL):
  ```
  SELECT conversion_action.name, conversion_action.type, conversion_action.category,
         conversion_action.primary_for_goal, conversion_action.include_in_conversions_metric,
         conversion_action.counting_type
  FROM conversion_action WHERE conversion_action.status = 'ENABLED'
  ```
  plus segmentacija `SELECT segments.conversion_action_name, metrics.conversions FROM customer` — bez segmentacije agregat izgleda kao jedan broj.
- ⚠️ **Segmentacija po akciji vraća samo akcije sa bar jednom konverzijom u periodu** (poznato od ranije) — akcija sa 0 konverzija postoji i može proraditi sutra; zato uvek gledati i `conversion_action` tabelu, ne samo segmentaciju. Konkretno: postoje **dve** telefonske akcije u „Conversions", druga trenutno na 0.
- **Pravilo: pre svake tvrdnje „imamo N plaćenih konverzija" — segmentiraj po akciji.** Agregat je zbir stvari koje ne merimo istom svrhom.
- Vezano: [[analiza/2026-08-11-snapshot-jul]] §3.6.

## Metrika u KPI tabli mora nositi jedinicu — „55 konverzija" je bilo 55 *pregleda* (2026-08-11)
- Hvala-proxy je definisan kao `screenPageViews` na `/hvala-za-poruku/`. To je bila razumna aproksimacija dok se ne pojavi razlog da ne bude — a razlog je postojao od početka: **jedan dolazak = 2 pregleda** (dupli GA4 `page_view` tag). Jun „55" = **24 sesije**.
- Greška nije u merenju nego u imenovanju: red u KPI tabli je pisao „Prave konverzije", pa je 15 meseci niko nije preispitivao. Da je pisalo „pregledi hvala stranice", nesklad sa brojem mejlova u inboxu (11 lead-mejlova za 16–30.07, M5) bi iskočio odmah.
- **Pravilo: KPI red nosi jedinicu u imenu** (`sesije`, `pregledi`, `mejlovi`), a gde postoji nezavisan izvor iste veličine (inbox, Ads konverzije) — bar jednom ih uporediti. Dve metrike koje mere „isto" a razlikuju se 2× su dijagnoza, ne šum.
- Vezano: [[dnevnik/2026-08-11-litespeed-redis-web-cache-manager]].

## GA4 totali iz konektora UKLJUČUJU `localhost` i `staging` — bez `hostName` filtera brojke su tuđe (nedeljni izveštaj, 2026-08-11)
- `ga4_report.py` vraća `activeUsers`/`sessions` bez ijednog filtera, a lokalni build od 2026-07-22 nosi **pravi** GTM-TRDT8K9 kontejner (mu-plugin `al-tracking-gtm-consent.php`) i šalje u istu GA4 property.
- Izmereno: nedelja 28.07–03.08 imala je **1.068 pregleda sa `localhost`** na 1.504 sa live-a — skoro 42% ukupnih pregleda je bio naš rad. Nedelja 04–10.08: 213 lokalnih.
- Posledica: neko poređenje 7d vs 7d može pokazati „pad saobraćaja" koji je zapravo samo **manje našeg lokalnog testiranja** te nedelje.
- Ključni eventi su manje pogođeni (2 `generate_lead` sa localhost-a od 41 u nedelji 04–10.08), ali nisu nula — `staging.antasline.com` je dao i 2 `tel` eventa.
- **Pravilo:** svaki GA4 izveštaj koji ide Miroslavu filtrira se na `hostName == www.antasline.com`. Kad ta razlika postoji, ona se i navede u napomenama.
- Trajno rešenje (nije urađeno, kandidat posle live-a): GA4 filter za interni saobraćaj ili odvojen Measurement ID za lokalni build.
- 🔴 **Dopuna istog dana (2026-08-11, uveče): ova lekcija je pregažena nekoliko sati posle nego što je zapisana.** Nova sesija je pokrenula `/nedeljni-izvestaj` bez čitanja [[PROGRESS]] i poslala Miroslavu sirove brojke (667/810 korisnika, kumulativ 127 umesto 119). Zapis u vault-u očigledno **nije dovoljna odbrana** dok je filter stvar pamćenja: `ga4_report.py` i dalje vraća nefiltrirane totale, a `/nedeljni-izvestaj` skill nigde ne pominje `hostName`. Zakrpa koja stvarno drži: filter (ili `--live-only` flag) ugrađen u samu skriptu + jedan red u skill-u. **Čeka odluku #ceka-miroslav.**

## `generate_lead` i hvala-proxy broje PREGLEDE, ne lidove — inflacija ~3× po sesiji (nedeljni izveštaj, 2026-08-11)
- Nedelja 04–10.08 na live-u: `/hvala-za-poruku/` ima **10 sesija / 8 korisnika**, ali **26 pregleda** i **39 `generate_lead`** evenata. Prethodna nedelja isto: 3 sesije → 6 pregleda → 9 evenata.
- Obrazac je **deterministički od jula**: svakog dana važi `generate_lead = 1,5 × broj pregleda`, a svi dnevni pregledi su parni brojevi. To ne liči na osvežavanje stranice od strane korisnika.
- Znači da je cela KPI serija „prave konverzije" (baseline „~55/mes", kumulativ 119 od 01.06) merena **pregledima** — po sesijama je to 51.
- ⚠️ Ne meša se sa junskim istorijskim šumom: `generate_lead` je u junu okidao i na `/kontakt/` (staro pravilo, [[CLAUDE]] §4), pa kumulativ evenata (220) ima i taj rep — obrazac 1,5× važi tek od jula.
- ⚠️ Ads-ova strana broji svoje (5 konverzija te nedelje) i **nije** naduvana u istoj meri — ne izvoditi zaključak o Ads performansama iz GA4 brojača.
- ✅ **UZROK DIJAGNOSTIKOVAN 2026-08-11 (uveče).** Nisu dva ista baga nego **dva različita**, jedan od kojih preživljava migraciju a drugi ne:
  - **Bag A — suvišan `page_view` tag, postoji i na buildu, PREŽIVLJAVA migraciju.** GTM pravilo `IF [event=gtm.js AND putanja sadrži /hvala-za-poruku]` okida **četiri** taga: `generate_lead` (id 17) · **`page_view` (id 18)** · Ads konverzija `__awct` (id 20) · `fbq('track','Lead')` (id 38). Ali Google tag `G-H8BRCZN8W4` (id 11, okidač `gtm.init`) **već sam šalje `page_view`** na svakoj stranici → na hvala stranici ih ima dva. Izmereno u mreži: jedno učitavanje = `_s=1 page_view` (Google tag) + `_s=2 generate_lead` + `_s=3 page_view` (suvišan tag 18). Identično na live-u i na lokalnom buildu. **Zato je hvala-proxy tačno 2× stvaran broj dolazaka** — i zato su svi dnevni brojevi parni.
  - **Bag B — trostruki `generate_lead`, SAMO na live-u, NE prenosi se.** Live Kallyas stranica ima **dva odvojena GTM embeda** istog kontejnera (jedan iz teme sa `data-cfasync`, drugi kroz `litespeed/javascript`) + noscript; `dataLayer` sadrži `gtm.js` **dvaput**. Izmereno na live-u: jedno učitavanje = 2× `page_view` + **3×** `generate_lead`. Lokalni WoodMart build ima **jedan** embed i daje 1× `generate_lead`.
- 🔴 **Posledica za post-live poređenje (najvažnije):** posle migracije obe brojke padaju same od sebe — `generate_lead` na ~⅓, hvala-proxy na ~½ (ako se bag A ne popravi) ili na ~⅙ ukupno (ako se popravi). **To NIJE pad konverzija i ne sme se tako čitati** u prvom post-live izveštaju.
- Naknadna reprodukcija „drugog embeda" ubacivanjem posle učitavanja stranice **ne radi** (GTM čuva `google_tag_manager[id]` i ne inicijalizuje se dvaput; ni drugi `gtm.js` push u `dataLayer` ne okida ništa) — mora biti u početnom HTML-u, kao na live-u. Za dokazivanje ovakvih stvari: meriti `analytics.google.com/g/collect` zahteve po `en=` i `_s=`, ne pretpostavljati iz konfiguracije.
- **Pravilo:** za pitanja **pokrivenosti** (a ne ispravnosti) uzeti nezavisan izvor URL-ova: live sitemap, GSC `page` dimenzija, ili `wp post list` — nikad sopstveni sitemap.

## Google OAuth u statusu *Testing* gasi refresh token posle 7 dana — konektor Ads/GMB tiho umire (2026-08-11)
- Simptom: `invalid_grant: Token has been expired or revoked` iz `ads_report.py` / `gmb_report.py` / `ads_final_urls.py`, dok GA4/GSC skripte rade savršeno.
- Razlog za tu asimetriju: GA4/GSC idu preko **servisnog naloga** (ne ističe), Ads/GMB preko **OAuth Desktop klijenta**.
- Izmereno: `token.json` osvežen 06.08, mrtav 11.08 = 5 dana. Nije jednokratni kvar, ponoviće se.
- Zakrpa: `authorize_oauth.py` (browser, 1 min). **Trajno rešenje: Cloud Console → OAuth consent screen → Publish app** (*In production*) — refresh token tada ne ističe po vremenu, u skriptama se ne menja ništa.
- 🔴 **Planska posledica:** proveriti token **na dan migracije pre početka** (stavka B1 checkliste) — 4.10 i verifikacija konverzija zavise od njega, a to je najgori trenutak za browser-consent.
- Usput: `token.json` nosi samo scope-ove za koje je poslednji put autorizovan (trenutno samo `adwords`; `tagmanager.edit.containers` iz 27.07 nije u njemu).
- **Dopuna 2026-08-11:** ista *Testing*-status posledica ima i drugu formu — pri ponovnoj autorizaciji Google prvo pokaže ekran **„App not verified"** (jer je `adwords` scope "sensitive"), ne direktno "Allow". Rešenje nije puna Google verifikacija (nepotrebna za app sa jednim korisnikom) nego: potvrditi da je nalog u **Test users** listi (OAuth consent screen → Audience) i kliknuti **Advanced → Go to [app] (unsafe)**. v. [[reference/api-konektor-setup.md]] Korak F.

## Google Ads Customer Match preko starog Ads API-ja je blokiran za developer tokene bez istorije — obavezan Data Manager API (2026-08-11)
- Simptom: `customer_match_upload.py --confirm` (uz **važeći** OAuth token) pada na `GoogleAdsException` sa `CUSTOMER_NOT_ALLOWLISTED_FOR_THIS_FEATURE`: "Customer Match uploads aren't supported in the Google Ads API for the developer token of the request. Use the Data Manager API for Customer Match workflows."
- Isti error kod se prvi put pojavio 2026-08-07 i bio protumačen kao „Basic developer token, treba Standard access u Ads API Center" — ta pretpostavka **nije potvrđena** kao ispravna.
- Google-ova zvanična dokumentacija (Data Manager API GA dec. 2025, obavezno od **2026-04-01**): developer tokeni koji **nikad ranije nisu slali Customer Match zahtev** preko starog `OfflineUserDataJobService`-a su blokirani **bez obzira na tier** (Basic ili Standard) — moraju na nov, poseban **Data Manager API** (`datamanager.googleapis.com`, paket `google-ads-datamanager`, klijent `IngestionServiceClient.ingest_audience_members()`). Ne treba developer token uopšte — čist OAuth 2.0.
- ⚠️ **Nusefekat na živi nalog:** `OfflineUserDataJobService` stigne da kreira **praznu user listu** u Ads UI-ju (Audience manager → Segments) pre nego što padne poziv za dodavanje članova — bezopasno, ali ostaje vidljivo dok se ručno ne obriše.
- **Pravilo:** pre nego što se pretpostavi da developer token tier (Basic/Standard) rešava neki Ads API blokator, proveriti da li je konkretan endpoint/feature u međuvremenu migriran na poseban API (Google redovno izdvaja funkcionalnost iz glavnog Ads API-ja u satelitske API-je — Data Manager je jedan primer).
- `mod_alias` dodaje originalni query samo kad **cilj nema svoj** query — ako se ikad napiše pravilo sa `?` u cilju, originalni parametri se **gube**.

## GA4 landing stranice nisu zamena za Ads final URL export — dokazano istog dana (2026-08-11)
- GA4 vidi samo ono što **ima klikove**, i beleži URL **posle** redirekta — pa oglas koji danas prolazi kroz Redirection plugin izgleda savršeno ispravno.
- Ne vidi: oglase/sitelinkove bez klikova (baš oni nose zaboravljene stare URL-ove), keyword-level final URL-ove, `tracking_url_template` / `final_url_suffix`, pauzirane kampanje.
- 🔴 **Izmereno, ne teorijski:** GA4 presek je dao 29/29 čisto, a Ads export je na istom nalogu našao **8 problematičnih URL-ova** — uključujući 2 koja vode na **tuđi domen**. GA4 nije uhvatio nijedan.
- Koristan kao presek najprometnijih odredišta i kao baseline, nikad kao razlog da se audit zatvori.

## Oglasni URL može voditi na TUĐI domen — 301 mapa tu ne pomaže (2026-08-11)
- Nađeno u pauziranoj kampanji: 3 oglasa i 2 sitelinka pokazuju na `ekopodneploce.rs`, ne na antasline.com.
- Nijedna redirect mapa, parity provera ni regression sweep to ne hvata — svi rade nad **našim** domenom. Vidi se isključivo iz Ads export-a.
- 🔴 Posledica za alate: **ne normalizovati URL u putanju pre provere hosta.** Prvi prolaz `ads-url-audit.php` je `ekopodneploce.rs/proizvodi/…` proverio kao putanju na našem sajtu i prijavio lažan `PUKAO`. Dodata zasebna klasa `EKSTERNI-DOMEN`.

## Google Ads API (GAQL): polje po kom se filtrira mora biti i u `SELECT` (2026-08-11)
- `WHERE campaign.status != 'REMOVED'` bez `campaign.status` u `SELECT` puca sa „The following field must be present in SELECT clause".
- **Pravilo ubuduće:** kad se pretpostavlja da je neka slika/proizvod od dobavljača X jer "ime zvuči povezano" ili je pomenuto u istom pasusu — proveriti zvaničnu member/brand listu holding grupe (ako postoji) PRE kačenja slike na proizvod. Vizuelno/zvučno slično ime (Edel Grass vs Edel Carpets/Yarns) je čest izvor lažne veze, pogotovo u multi-brand holding strukturama (Condor Group ima 10 članica). Boje/varijante iz dva izvora koje se ne poklapaju 1:1 (ovde: lokalni Condor set od 7 boja vs. live Edel Grass set od 6, samo delimično preklapanje) su dodatni signal da su u pitanju dva različita proizvoda, ne isti.

## Customer Match upload zahteva Standard developer token (Basic access ne radi) + membership_life_span max je 540 dana, ne 10000 (2026-08-07)
- Prvi stvaran `customer_match_upload.py --confirm` pokušaj (posle scan_leads.py fix-a) pao je u dva koraka:
  1. `user_list.membership_life_span = 10000` ("konvencija za bez isteka") odbijeno sa `RangeError.TOO_HIGH` — Google je od **2025-04-07** uveo tvrd max od **540 dana** za CRM-based (Customer Match) liste, stari "10000 = trajno" sentinel je ukinut. Fix: konstanta promenjena na `540`.
  2. Posle tog fixa, sama lista se USPEŠNO kreirala u pravom nalogu (prazna, `AntasLine - Website Leads`), ali `OfflineUserDataJobService.create_offline_user_data_job()` je odbijen sa `CUSTOMER_NOT_ALLOWLISTED_FOR_THIS_FEATURE`: "Customer Match uploads aren't supported in the Google Ads API for the developer token of the request. Use the Data Manager API for Customer Match workflows." — Basic developer token access (odobren 2026-07-27 za izveštavanje) ne pokriva Customer Match; potreban je Standard access (ručno odobrenje, Ads UI → Tools & Settings → API Center) ili migracija na poseban Data Manager API.
- **Pravilo:** developer token "access level" (Basic/Standard/Advanced) nije isto što i "token radi/ne radi" — token može uspešno raditi za READ operacije (izveštaji, `ads_report.py`) a istovremeno biti odbijen za specifične WRITE feature-e (Customer Match) koji traže viši nivo. Ne pretpostaviti da je jednom odobren token dovoljan za sve buduće write operacije.
- Nusfekat koji treba znati: prazna user lista OSTAJE kreirana u live nalogu i posle neuspelog upload pokušaja (mutate_user_lists je uspeo, samo posledeći offline data job korak je pao) — nije automatski očišćena, nije štetna (0 članova, 0 troška), ali je stvarna izmena na produkciji koju treba pomenuti transparentno, ne samo "pokušaj nije uspeo".
- #ceka-miroslav: zatražiti Standard developer token pristup u Ads UI API Center pre sledećeg pokušaja.

## Nova skripta pisana na osnovu STARE pretpostavke, ne provere žive baze — pošiljalac lead-mejlova (Customer Match, 2026-08-07)
- `scan_leads.py` (dodat u konektor 2026-08-07) je hardkodovao `LEAD_SENDER = "wordpress@antasline.com"` — tačno vrednost koju bi budući WoodMart/CF7 build slao, ali NE ono što live sajt (i dalje stari Kallyas/Zion Builder) stvarno šalje. P1 nalaz iz 2026-07-30 audita mejlova je već utvrdio pravi pošiljalac (`no-reply@antasline.com`, potvrđeno na svih 49 formi), ali ta činjenica nije prenesena u skriptu napisanu nedelju+ dana kasnije.
- Simptom je bio tih: `--dry-run` nije bacio grešku, samo je vratio "0 novih kontakata" na mailbox-u koji je stvarno imao 65 poruka/6 pravih leadova — lako se protumači kao "nema novih upita" umesto "filter je pogrešan".
- **Pravilo:** kad skripta filtrira po pošiljaocu/formatu koji potiče sa live produkcije (ne sa lokalnog build-a), pre prvog pokretanja proveriti PRAVU vrednost direktno iz mailbox-a/baze (`python3 -c "import mailbox; ..."` na par poruka), ne preuzimati vrednost iz ranijeg plana/dokumentacije bez re-verifikacije — pogotovo ako je ta dokumentacija pisana za drugačiji (budući) sistem.
- **Ime fajla nije dokaz šta je na slici.** `2023/01/amss-logo.webp` sadrži žut znak sa natpisom **„AMCC"**, a AMSS ima sasvim drugačiji amblem. Pre nego što se logotip stavi pod nečijim imenom — pogledati ga uvećano (`img.style.height='220px'; filter:none` pa screenshot). Tuđ znak pod tuđim imenom je gora greška od izostavljenog logotipa.

## GTM
- Import ručno pisanog JSON-a NE prolazi — greška "Error deserializing enum type [EventType]". Pouzdano: (A) ručno u GTM UI ili (B) Export → ubaci evente u tačan format → Merge.
- GA4 consent update handler MORA slati eksplicitne vrednosti za sve 4 kategorije; prazan `gtag('consent','update',{})` ne poništava prethodni granted.
- ⚠️ **Ispravka (2026-07-22, W3 3.10)**: raniji unos ovde tvrdio je da lokalni build ima gtag snippet stubovan na `id=DUMMY` — netačno/nikad verifikovano. Stvarno stanje do 2026-07-22: lokalni build NIJE imao NIKAKAV GTM kod (videti novi unos ispod, "GTM UI konfiguracija ≠ embed na sajtu"). Od 2026-07-22 lokalni build ima PRAVI GTM-TRDT8K9 kontejner (preko `mu-plugins/al-tracking-gtm-consent.php`). GTM Preview/Tag Assistant protiv `localhost` NIJE testiran posle ovog fix-a — moguće da i dalje ne radi iz drugih razloga (network/CORS), ali premisa "DUMMY stub" više ne važi. Za live-test triggera pre Submit-a, i dalje najsigurnija opcija je GTM Preview protiv pravog **antasline.com** URL-a (read-only, samo dodaje `gtm_debug` query param, ne menja sajt).
- Pre dodavanja "planiranog" eventa iz CLAUDE.md §4.1 — proveriti GTM UI direktno (Tags + Triggers liste), ne verovati listi u CLAUDE.md bez provere: `view_product_category`/`epoxy_conquest_engagement`/`lead_form_start` su se ispostavili već potpuno izgrađeni i ožičeni iako je stara napomena govorila "proveriti da li postoji ili je pretpostavljeno".
- **GTM UI konfiguracija ≠ embed na sajtu — proveriti oba posle SVAKOG rebuild-a teme** (2026-07-22, W3 3.10): lokalni WoodMart build nije imao NIKAKAV GTM/gtag kod (ni pravi, ni DUMMY stub — stari CLAUDE.md gotcha o tome je bio netačan/nikad verifikovan) uprkos tome što je BLOK A tracking rad (GTM v10, Consent Mode v2, svi eventi) bio potpuno završen — jer taj rad postoji SAMO u GTM UI-ju, a embed `<script>` snippet je fizički ostao na starom Porto/Kallyas builda i niko ga nije preneo u WoodMart rebuild. Da je ovo prošlo neprimećeno na migraciju: nula analitike od dana 1, tiho, bez ijedne greške. **Provera koja bi ovo uhvatila ranije**: `curl <lokalni-url> | grep "GTM-"` posle SVAKE promene teme/buildera, ne samo posle GTM UI izmena. Fix ovog slučaja: `mu-plugins/al-tracking-gtm-consent.php`, doslovna kopija tačnog live koda (izvučeno `curl` + Chrome DevTools `document.getElementById(...).textContent` za JS-injektovan CSS koji `curl` ne vidi).
- Više Google naloga u istom Chrome profilu: podrazumevani (`authuser=0`) često NIJE onaj sa pristupom (video se `cpgujam@gmail.com` bez pristupa GTM/GSC dok je pravi nalog `miroslav.markovic109@gmail.com` na `authuser=1`) — proveriti prava pre nego što se zaključi "nemam pristup".

## GA4 / publike
- `epoxy_conquest_engagement` okida samo 1× po korisniku (`window.__epoxyTracked` flag) → audience count `≥ 1`, NE `> 1`.
- 4.3K / 99.8% pri kreiranju publike = GA4 procena addressable pool-a, NE stvarna veličina. Dokaz da filteri rade = "Too small to serve" u Ads.
- Backup destinacija (M politika od 2026-07-09): eksterni HDD `G:` "Maxtor" kad god je prikačen → OneDrive → lokalni fallback. Skripta sama bira (Get-Volume check), ništa se ne menja kad se disk doda/skine.

## GA4 Windsor.ai — `conversions` polje se ne sme slepo verovati (2026-07-21)
- **Windsor `conversions` total polje može biti kontaminirano lažnim key eventima** — 2026-06-17 do 06-22 polje je pokazivalo 800–1200/dan (umesto normalnih 0–10) jer je u GA4 adminu privremeno bilo označeno 8+ dodatnih evenata kao "key event" (`page_view`, `session_start`, `user_engagement`, `first_visit`, `scroll`, `form_start`, `click`, `view_search_results`) pored zaključana tri (`generate_lead`/`tel`/`mailto`). Self-rešeno do 06-23 (potvrđeno praznim pull-om za taj period), ali `conversions` polje samo po sebi ne pokazuje UZROK skoka. **Provera**: `get_data` sa `fields:["date","event_name","event_count","is_conversion_event"]` i `filters:[["is_conversion_event","eq","true"]]` — ako se pojavi bilo šta osim generate_lead/tel/mailto, key eventi u GA4 adminu nisu zaključani na tačno tri kako CLAUDE §4 nalaže.
- Isti kontaminirani prozor (06-17/06-18) pokazao je i `tel:+381692340074` (stari broj) pored `tel:+381692340072` — GTM v10 čist `tel` event bez `tel:+broj` duplikata je i dalje potvrđen za jul (07-14→07-20 pull čist), pa je ovo verovatno bio artefakt iste privremene admin-konfiguracije, ne regresija taga.
- Pre bilo kakve izmene, backup sirove vrednosti preko `$wpdb->get_var()` (ne `wp post meta get` CLI ispis, koji može da izgubi/izmeni whitespace u velikom serialized bloku).

## Sopstveni Google API konektor — Ads API ne prihvata service account, GMB/My Business ima nizak default kvota (2026-07-27)
- **Google Ads API tehnički ne podržava service account autentifikaciju uopšte** — bez obzira što je Miroslav imao gotov `claude-mcp-ads` service account ključ u istom GCP projektu kao i GA4/GSC (koji rade odlično preko service account-a), taj ključ je neupotrebljiv za Ads. Jedini put je OAuth 2.0 sa pravim korisničkim nalogom (Desktop/Web client + refresh token) + poseban developer token. Ne gubiti vreme pokušavajući service account rutu za Ads.
- **GA4 Data API i Search Console API prihvataju service account odmah** — dovoljno je dodati service account email kao Viewer/User u GA4 Property Access Management odn. GSC Settings→Users, bez ikakvog OAuth koraka. Ovo je najbrži put kad god je dostupan.
- **"My Business" familija API-ja (`mybusinessaccountmanagement`, `mybusinessbusinessinformation`, `businessprofileperformance`) ima svoj tok grešaka**: prvo `403 SERVICE_DISABLED` dok API nije uključen u projektu (Google daje direktan link za aktivaciju u error poruci — koristan i pouzdan), a ODMAH POSLE uključivanja često sledi `429 Quota exceeded — Requests per minute`. **Ispravka 2026-07-27 (posle 4 neuspela pokušaja kroz 2 sesije + Miroslavljevi screenshotovi Quotas stranice)**: ovo NIJE propagaciono kašnjenje koje samo prođe — kvota je doslovno **0** ("Requests per minute", Value 0, Current usage 0) i tako ostaje. Poseban ručni review proces je potreban, odvojen od običnog "Enable API" koraka i od standardnog Cloud quota-increase toka. **Pažnja — stari link zastareo**: `developers.google.com/my-business/content/prereqs#request-access` je legacy forma i **ZATVORENA** (Google-ova sopstvena stranica na tom URL-u to potvrđuje). Pravi tekući put: `support.google.com/business/contact/api_default` → iz padajućeg menija izabrati **"Application for Basic API Access"** (NE "Quota Increase Request" — ta opcija je samo za naloge koji su već allowlisted i imaju kvotu >0; ako je kvota na 0 kao ovde, ide se preko "Basic API Access"). Ne gubiti vreme na ponovne retry-eve konektora dok ta forma nije popunjena i odobrena.
