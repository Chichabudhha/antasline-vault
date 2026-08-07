---
tip: analiza
datum: 2026-08-07
blok: strategija
izvor: "Web pretraga (WebSearch/WebFetch) 2026-08-07, 6 upita + 8 konkurentskih stranica direktno pregledane; ukršteno sa seo/2026-07-27-content-klasteri.md (GSC 90d) i analiza/2026-07-21-serp-snapshot-pre-migracija.md"
status: prvi-nacrt
azurirano: 2026-08-07
---

# Tržište i konkurencija — realna analiza (2026-08-07)

> **Ograničenja istraživanja (pročitati pre korišćenja nalaza):** ovo je jedan
> prolaz kroz ~15 domena i 8 direktno pregledanih stranica preko web pretrage,
> ne dubinski audit svakog konkurenta (nema pristupa njihovim GSC/Ads
> podacima, njihovim maržama, ni njihovom stvarnom obimu prodaje). Cenovni
> podaci i "koliko su dobri u SEO-u" su procene sa jednog snapshot-a, ne
> praćenje kroz vreme. Gde nešto nije provereno, piše "nije provereno" — ne
> izmišljati.

## 1. Šta AntasLine prodaje vs. ko se pojavljuje na istim upitima

| Niša (CLAUDE §1) | GSC 90d prikazi / CTR / poz (izvor: content-klasteri) | Ko se pojavljuje na istim upitima (web pretraga) |
|---|---|---|
| Sport (košarka+ostalo+padel/tenis) | 25.880 impr, CTR 1,3–5,7%, poz 3,2–4,7 | **3x3 Srbija** (specijalista, e-shop), **Pejkom** (specijalista, veliki B2B), Megapod (generalista tepisi/podovi, sport je sporedan) |
| Terase/dvorište | 8.349 impr, CTR 3,81%, poz 7,8 | **Sve za Pod** (specijalista guma), **Galerija Podova** (generalista, 30+ kategorija), IKEA (retail div.), Royal Boden/podne-obloge.com |
| Ecotile/PVC industrijske ploče | 924 impr, CTR 2,27%, **poz 13,2** 🔴 | **Royal Boden/podne-obloge.com** (regionalni B2B), pvcpodovi.rs (nije dublje provereno) |
| ESD/antistatik | 914 impr, CTR 4,27% (zdravo) | **PiK Group** (generalista podovi+tepisi, cene objavljene), **Madera Podovi** (specijalista, i epoksid i PVC), Levelo, Epoksan (nisu dublje provereni) |
| Bergo | 643 impr, CTR 5,29% | podovibergo.me, Ratković (regionalni distributeri, nisu dublje provereni) |
| Veštačka trava | 641 impr, **CTR 8,42%** (najbolji u portfoliju) | Galerija Podova, IKEA, Roda Shop — svi **generalisti za domaćinstvo**, nijedan specijalista nađen |
| Epoksid-conquest | 3.193 impr, **CTR 0,47%** (najgori) | **Madera Podovi** — prodaje epoksid direktno (suprotan ugao od AntasLine conquest strategije), Epoksan |

## 2. Konkurenti izbliza — dobre i loše prakse

### PiK Group (pikgroup.rs) — ESD/antistatik + generalna prodaja podova
- ✅ **Cene objavljene po proizvodu** ("Od 3.228,00 RSD", PDV uključen), funkcionalna online korpa, filteri po debljini/materijalu
- ✅ Edukativni tekst uz proizvod ("Zašto koristiti ESD i antistatik podloge")
- ❌ Nema FAQ sekciju, nema vidljiv schema markup
- Ton: hibrid B2B/B2C, pristupačan jezik uz industrijsku namenu
- **Zaključak:** direktan konkurent na ESD nišu, jači u transakcionoj UX (korpa+cena), slabiji u sadržaju/SEO strukturi od AntasLine-a

### Madera Podovi (madera-podovi.com) — antistatik + epoksid
- ❌ Cene NISU objavljene, striktno telefon-CTA
- ⚠️ **Prodaje i epoksidne (Peran ESD STB) i PVC/vinil-ester opcije na istoj stranici** — ovo je jedini nađeni konkurent koji direktno kombinuje oba ugla koje AntasLine razdvaja (Ecotile vs. epoksid conquest članak). Ako Madera aktivno ubeđuje kupce da epoksid nije loš izbor, epoxy-conquest članak se bori sa suprotnom porukom na tržištu, ne samo sa sopstvenim slabim CTR-om.
- Ton: isključivo B2B, industrijski, naglašava rizike/standarde
- **Zaključak:** ne toliko SEO pretnja (nema FAQ/schema/cene), koliko **poruka-pretnja** za epoxy-conquest strategiju

### Royal Boden / podne-obloge.com — regionalni (BiH-bazirani, radi i za Srbiju)
- ✅ Najširi asortiman od svih nađenih konkurenata (sport + industrijski/Bergo/Ecotile/Objectflor vinil + igrališta + bazeni + trava + oprema) — struktura najsličnija AntasLine-u
- ✅ FAQ sekcija (8 pitanja), blog, tehnički detalji o debljinama
- ❌ Cene nisu objavljene
- Ton: eksplicitno B2B (škole, bolnice, fabrike, skladišta)
- **Zaključak:** najbliži "pravi" konkurent po širini ponude, ali sedište/domenska autoritet je van Srbije (.com, BiH kontakt broj) — verovatno slabiji domaći brend-signal i lokalni SEO od AntasLine-a uprkos širem katalogu

### 3x3 Srbija (3x3srbija.rs) — sportske podloge, specijalista
- ✅ Cena objavljena (3.400 RSD/m², 29 EUR+PDV), "Add to cart", video, brošura za preuzimanje, FIBA sertifikacija istaknuta
- ✅ Reference: "700+ održanih turnira" — jak trust signal za institucionalne kupce (klubovi, opštine)
- Ton: B2B prema klubovima/opštinama, ali i e-shop za pojedince
- **Zaključak:** najjači pojedinačni konkurent na sport-terenima — kombinuje cenu, video, sertifikaciju i institucionalne reference, nešto što AntasLine trenutno nema na svojim sport stranicama

### Pejkom (pejkom.rs) — sportske podloge, veliki B2B specijalista
- ❌ Cene nisu objavljene nigde
- ✅✅ **Najjači portfolio/reference nađen u celom istraživanju**: 21 prikazan klijent uključujući Delta City, FK Voždovac, KK Crvenu zvezdu, 7 video zapisa realizacija
- ⚠️ Sadržaj po kategoriji plitak (kratki opisi, bez tehničkih specifikacija) — SEO snaga im dolazi verovatno iz brenda/backlinkova, ne iz on-page dubine
- **Zaključak:** ovo je institucionalni "safe choice" konkurent — kupac koji bira po poverenju/referencama (opština, veliki klub) verovatno ide kod Pejkom-a pre nego kod bilo koga bez vidljivog portfolija. AntasLine nema javno vidljive reference/case-studies ove vrste.

### Sve za Pod (svezapod.rs) — gumeni podovi, specijalista
- ✅ Pun e-commerce (korpa, plaćanje karticom, dostava)
- ⚠️ Cene iskazane po dužnom metru, ne po m² — kupac mora sam da računa (trenje u UX, ali i mogući signal namernog izbegavanja lake usporedbe cene/m²)
- **Zaključak:** transakciono jak, ali cenovna prezentacija otežava direktno poređenje — moguća prilika ako AntasLine iskaže jasnu cenu/m² tamo gde se takmiče

### Galerija Podova (galerijapodova.com) — generalista za domaćinstvo, veštačka trava
- ✅ Cene objavljene (969–5.050 RSD/m²), korpa, "plaćanje na 12 rata", 40 prodavnica/32 grada (fizička prisutnost/trust)
- ❌ Sadržajno plitko po proizvodu (specifikacije samo u filterima)
- Ton: B2C domaćinstvo ("dvorište, terasa", "bez košenja")
- **Zaključak:** na veštačkoj travi (AntasLine-ov najbolji CTR klaster, 8,42%) konkurencija su generalisti sa fizičkim prodavnicama i ratama — jaki na trust/pristupačnost, slabi na specijalizovan sadržaj. **Ispravka (2026-08-07, posle M pitanja):** niša veštačke trave nije prazna — `/vestacka-trava/` postoji live sa tri linije za fudbal (XJ Performance, XT Competition, XWR) + tenis segment + dekorativna trava u bojama, tehničke specifikacije, EMF partnerstvo. Ono na šta je originalna verzija ovog izveštaja pogrešno referisala kao "cela niša u draft-u" je uži podskup: **16 pojedinačnih proizvod-zapisa** (Condor Schools 16877 i Condor Playgrass 16885 — svaki variable proizvod sa 7 boja: crvena/žuta/plava/bela/roze/zelena/braon — plus Condor shock-pad 16893 i par Radici tehničkih trava), kreirani 2026-07-11, i dalje neobjavljeni jer **nemaju prave fotografije**. Foto-batch 2026-07-29 je pokušao AI-dopunu (Gemini enhance) i uspeo samo za 2/10 — ostatak namerno preskočen zbog rizika pogrešnog pripisivanja modela/boje (dobavljački sajtovi condor-group.eu/radicisport.it su JS-renderovani ili generički). Ovo je dopuna postojeće ponude (specifično "trava u boji" asortiman), ne pokretanje niše od nule.

## 3. Da li ima veze sa SEO rangiranjem — realna ocena

Nema dovoljno podataka da se tvrdi čvrsta kauzalnost (nema pristupa njihovim Ads/GSC brojevima), ali obrazac koji se ponavlja kroz uzorak:

- **Konkurenti sa objavljenom cenom + korpom** (PiK Group, 3x3 Srbija, Sve za Pod, Galerija Podova) su svi transakciono jači od AntasLine-a, koji na proizvod stranicama (potvrđeno na Ecotile 500/5) **nema cenu, samo telefon/formular**. Ovo se poklapa sa GSC signalom — upiti sa "cena" u tekstu (`pvc podovi cena` poz 17,1, `gumeni podovi za terase cena` 236 impr CTR 8,9%, `podloga za kosarkaski teren cena`) sistemski slabije konvertuju od varijanti bez "cena".
- **AntasLine-ov jedini dokazano uspešan protivotrov je već primenjen na jednom mestu**: sekcija "koš za dvorište" na `/sportske-podloge/kosarkaske-konstrukcije/` sa pravim cenama (167.790–549.900 RSD) — ovo je isti obrazac koji PiK Group/3x3 Srbija/Galerija Podova rade sistemski, a AntasLine tek počinje da širi.
- **FAQ/schema strukturu** ima samo jedan konkurent u uzorku (Royal Boden, 8 pitanja) — AntasLine ovde ima realnu prednost (FAQPage schema već sistemski primenjena kroz content-klaster rad, npr. 16586/16657/17026/17027), nijedan drugi nađen konkurent to ne radi na ovom nivou.
- Nijedan konkurent u uzorku ne pokazuje znake sistemskog GSC-vođenog content rada (klaster analiza, praćenje CTR-a po upitu) — njihove stranice deluju kao statični katalozi. Ovo je stvarna, ali **teško merljiva** prednost — ne nešto što se vidi spolja, samo kroz brzinu pomaka u pozicijama tokom vremena.

**Realno, ne optimistično:** AntasLine gubi na najvidljivijem i najlakše kopirivom faktoru (cena na stranici), a dobija na teže kopirivom, sporijem faktoru (struktura sadržaja/schema). Kupac koji uspoređuje 3 sajta za brzu odluku (najčešći B2C/mali B2B slučaj) verovatno prvo klikne na onaj sa vidljivom cenom — to nije SEO gubitak, to je gubitak na klik/konverziju posle dolaska, ali GSC podaci (CTR pada baš na "cena" upitima) sugerišu da se ovo dešava i pre klika, verovatno kroz to da Google/kupac u SERP-u prepoznaje/traži cenovni signal koji AntasLine retko nudi eksplicitno u title/meta.

## 4. Gde smo šuplji

1. 🔴 **Cenovna netransparentnost na proizvod-stranicama** — jedini nađeni obrazac koji se ponavlja kod skoro svih ozbiljnih konkurenata (PiK Group, 3x3 Srbija, Sve za Pod, Galerija Podova) je da cena stoji na stranici. AntasLine ima ovo rešeno tačkasto (koš za dvorište, S7 upisi) ali ne sistemski. GSC dokazuje da je tražnja za cenom ogromna u skoro svakom klasteru.
2. 🔴 **Core biznis (PVC/Ecotile) slabo rangira** (poz 13,2, `pvc podovi` poz 22,3) dok je istovremeno jedina niša gde AntasLine ima realnog, uporedivog konkurenta na dubini sadržaja (Royal Boden). Ovo je najveći poslovni rizik iz cele analize — najviše-margina proizvod (pretpostavka na osnovu CLAUDE §1 opisa firme, marže nisu provereno) na najslabijoj poziciji.
3. 🟡 **Nema javno vidljivih referenci/case-studies** — Pejkom pokazuje da je ovo jak trust-signal za institucionalne kupce (opštine, klubovi, veliki lanci). AntasLine nema ekvivalent (koliko je vidljivo spolja).
4. 🟡 **Veštačka trava u boji** — najbolji CTR u portfoliju (8,42%) i niša postoji (`/vestacka-trava/` live, tri linije + dekorativne boje), ali konkretan "trava u boji" asortiman (Condor Schools/Playgrass, 16 zapisa) je zaglavljen u draft-u zbog nedostatka pravih fotografija (2/10 rešeno AI-dopunom). Nije prazna niša, nego nepotpuna ponuda unutar postojeće — jeftin dovršetak, ne novo pokretanje.
5. 🟡 **Epoxy-conquest poruka ima aktivnog protivnika** (Madera Podovi prodaje epoksid otvoreno) — CTR 0,47% na klasteru nije samo pitanje title/meta, moguće je da tržišna poruka gubi konkurenciju sa direktnim epoksid-prodavcima, ne samo sa Google snippet-om.

## 5. Gde smo iznad konkurencije

1. ✅ **Širina asortimana + on-page SEO struktura kombinovano** — nijedan pojedinačan konkurent u uzorku ne pokriva industrijski+sport+terase+trava sa FAQPage schema i sistemskim internim linkovanjem. Royal Boden ima širinu bez te SEO dubine; PiK Group/3x3 Srbija imaju transakcionu UX bez širine.
2. ✅ **Dokazano radni content-obrazac** (informativni upit → namenska stranica sa tabelom → cross-link na komercijalnu stranicu → FAQ schema) — basket dimenzije, fudbal dimenzije, koš za dvorište su već izmereni uspesi. Nijedan konkurent ne pokazuje ekvivalentan sistematski pristup.
3. ✅ Postojeća marketing infrastruktura (GA4 publike, consent mode, GTM eventi) — nevidljivo spolja, ali daje sposobnost preciznog re-targetiranja i merenja koju generalisti (Galerija Podova, Megapod) verovatno nemaju na ovom nivou.

## 6. Na koji segment se fokusirati — realna preporuka

**Prioritet 1: PVC/Ecotile industrijske ploče (core biznis).** Najveći gap između poslovnog značaja i trenutne pozicije (poz 13,2, `pvc podovi` poz 22). Konkurencija ovde je najslabija u smislu SEO strukture (samo Royal Boden je uporediv, i taj je regionalni ne domaći igrač) — realno osvojivo. `/pvc-podne-ploce/` hub je već napravljen (17026, 2026-07-27) — sledeći korak je **dodati cenu/cenovni raspon** na ovu i povezane proizvod-stranice, jer je to jedina stvar koju konkurenti rade bolje.

**Prioritet 2: Terase/dvorište — proširiti cenovni obrazac koji već radi.** Sekcija "koš za dvorište" sa pravim cenama je dokazan model. Isti pristup (realne cene u tabeli, ne "cena na upit") treba proširiti na Bergo terase proizvode gde je GSC tražnja za cenom velika (`gumeni podovi za terase cena` 236 impr).

**Sekundarno, nisko-trud, brzo: dovršiti "trava u boji" asortiman.** Niša veštačke trave već postoji i radi (najbolji CTR u portfoliju). Ono što nedostaje je uzak set od 16 proizvod-varijanti (Condor Schools/Playgrass u bojama) blokiran na fotografijama, ne na sadržaju — nabaviti prave fotografije od dobavljača (umesto rizičnog AI-mapiranja) i objaviti, ne razvoj od nule.

**Sport — održavanje, ne ekspanzija.** Sport nosi najviše saobraćaja (55% impresija) ali najslabije konvertuje (CTR 3,4%) i ima najjačeg institucionalnog konkurenta (Pejkom sa referencama koje AntasLine nema). Realno: ne graditi nove sport-stranice, popravljati CTR na postojećim (nastavak već započetog "visina koša" obrasca), i razmotriti da li vredi ulagati u vidljive reference/case-studies da bi se takmičilo sa Pejkom-om na institucionalnom segmentu — ali to je veći, sporiji projekat (prikupljanje dozvola za korišćenje logoa klijenata, fotografija realizacija) koji zahteva M odluku pre početka.

**Epoxy-conquest — ne menjati pre re-merenja.** GEO fix je urađen 2026-07-22, content-klasteri napomena kaže "premeriti krajem avgusta pre nego što se menja išta" — ovo istraživanje ne menja tu preporuku, samo dodaje kontekst da Madera Podovi aktivno prodaje suprotnu poruku (nije razlog za paniku, samo objašnjenje zašto CTR možda ostane nizak i posle title/meta fixa).

## 7. Kako se dalje unaprediti (van SEO-a)

- **Cenovna transparentnost** je tema koja se ponavlja u skoro svakoj sekciji gore — ovo nije samo SEO pitanje, nego i UX/konverzija pitanje. Vredi razmotriti da li je razlog za "cena na upit" politiku (npr. B2B pregovaranje po projektu) i dalje validan za sve linije proizvoda, ili se može uvesti orijentacioni raspon ("od X RSD/m²") bez punog price commit-a, po uzoru na sopstveni uspešan primer (koš za dvorište).
- **Vidljive reference/case-studies** — čak i skroman set (5-10 realizovanih sportskih terena sa fotografijom i imenom klijenta, gde je dozvoljeno) bi zatvorio deo gap-a prema Pejkom-u na institucionalnom segmentu.
- **Ne kopirati Sve za Pod-ov obrazac cene-po-dužnom-metru** — ovo je trenje u njihovoj UX, prilika da se AntasLine razlikuje jasnijom cena/m² prezentacijom gde se ta dva sajta direktno takmiče (terase guma).

## Napomene o metodu i nepouzdanosti

- Ovo je **jedan** snapshot pretrage (2026-08-07), ne praćenje kroz vreme — pozicije/asortiman konkurenata se menjaju.
- Cene konkurenata su preuzete sa njihovih javnih stranica u trenutku pretrage; nije proveravano da li su cenovnici ažurni.
- Marže/profitabilnost po niši za AntasLine nisu procenjene ovde jer podaci o troškovima/maržama nisu dostupni ovom istraživanju (samo `reference/cenovnik.md`, koji sadrži prodajne cene, ne troškove) — "profitabilnost niše" u ovom dokumentu je proxy izveden iz (GSC tražnja × slabost konkurencije × pozicija AntasLine-a), ne stvarna marža.
- Nisu dublje istraženi: Levelo, Epoksan, pvcpodovi.rs, podovibergo.me, Ratković, Boma-Court, OnCourt Online, Plastik Gogić (identifikovani po imenu u `analiza/2026-07-21-serp-snapshot-pre-migracija.md`, ali stranice nisu ponovo posećene u ovoj sesiji).
- Megapod.rs stranica za sport nije mogla da se učita (tehnička greška pri dohvatanju) — profilisan samo iz search snippet-a (generalista tepisi/podovi sa sportskim podlogama kao delom ponude), ne iz direktnog pregleda stranice.

## Veze
- [[analiza/2026-07-21-serp-snapshot-pre-migracija]] — prethodni, uži SERP snapshot (3 upita)
- [[seo/2026-07-27-content-klasteri]] — izvor GSC brojeva korišćenih ovde
- [[reference/negativne-kljucne-reci]] — postojeći registar konkurentskih brendova (Ads kontekst)
- [[PROGRESS]] · [[DNEVNIK-NAPRETKA]]
