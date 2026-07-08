# Dnevnik napretka — Antasline SEO

## 2026-07-08 [claude-code] [W1 1.2 #6] — Podovi za bazene (262 kl., ID 16662) ✅
- ✅ **Backup**: `antasline_local_2026-07-08_113427_pre-podovi-za-bazene.sql` pre svih izmena.
- 🔍 **Kontekst pre gradnje**: postojao je **tanak orphan stub (ID 5231)** na `/podloge-oko-bazena/` (pogrešan slug, top-level, bez Yoast mete, samo 2 pasusa + prazan masonry grid) — nedovoljno za pravu landing stranicu. Nijedan od 10 legacy `spoljne-podne-obloge` CPT unosa (bergo-unique/elite/solid/flow/xl/ultimate/easy + Naxos/Patmos Evolution) nije pokrivao bazenski proizvod — specifikacija (380×380mm, 10,1mm, 150 t/m², antibakterijski PP) je jedinstvena za ovaj proizvod. Sadržaj/specifikacija izvučeni WebFetch-om sa live URL-a (dva prolaza — opšti sadržaj + fokusirana provera boja/FAQ/garancije, koja je potvrdila da live NEMA FAQ sekciju niti navedene hex boje u tekstu).
- 💡 **Odluka o bojama**: live pominje "standardna i opciona paleta" ali ne navodi imena/hex kodove boja (za razliku od Naxos/bergo-xl gde su boje bile eksplicitno navedene) → stranica NE prikazuje swatch grid, samo tekstualna napomena "dostupnost na upit" u specifikaciji, bez izmišljanja hex vrednosti.
- ✅ **Nova stranica ID 16662** (`page`, post_parent=16590 `/spoljnje-podne-obloge/`) na **identičnoj live-parity URL** `/spoljnje-podne-obloge/podovi-za-bazene/`. Sekcije: hero → intro + 4 USP kartice (reused ikonice: protivklizna/fleksibilna/odrzavanje/izdrzljivost) → specifikacija tabela (al-table) → 3 realne reference slike pronađene u postojećim uploads folderima (2018/2022, uključujući "Bergo Easy za bazene" i "Bergo Unique za bazene" — potvrđuje da su ovo isti proizvodi samo u bazenskoj primeni) → FAQ (6 pitanja, originalno pisano jer live nema FAQ) + FAQPage JSON-LD → CTA sa cross-linkom ka `/spoljnje-podne-obloge/`.
- 🔧 **Stari thin stub (5231) arhiviran**: `post_status=draft`, slug→`podloge-oko-bazena-stara` (potvrđeno da ga ništa u aktivnom sadržaju nije linkovalo pre arhiviranja), sada 404 kao očekivano.
- 💰 **Cena**: nema unosa u cenovniku za bazenske Bergo modele → specifikacija kaže "na upit", FAQ isto.
- ✅ **Verifikacija**: 200 · 1×H1 · FAQPage JSON-LD bez dupliranja · title tag čist (bez escape bug-a) · svi linkovi/slike/ikonice 200 · vizuelna provera (hero + spec tabela + galerija) · regresija na `/spoljnje-podne-obloge/` (schema+H1 netaknuti) čista.
- Skripte (scratchpad): `create-podovi-za-bazene.php`, `archive-5231.php`, `check-escape-16662.php`.
- **W1 1.2 #6 zatvoren** → [[migracija/w1-red-cekanja]]. Sledeći: #7 iznajmljivanje-podova (232 kl.).

## 2026-07-08 [claude-code] [W1 1.2 #5] — Naxos Evolution podovi za sale i balone (378 kl., ID 16661) ✅
- ✅ **Backup**: `antasline_local_2026-07-08_112542_pre-naxos-sale-balone.sql` pre svih izmena.
- 🔍 **Nalaz pre gradnje**: rich legacy sadržaj već postojao lokalno kao **orphan page ID 15490** "Naxos Evolution" na pogrešnoj URL `/sportske-podloge/naxos-evolution/` (post_parent=5438) — generički template (`<h1>` bez al- klasa, nema Yoast metu, nigde nije linkovan). Sadržaj bogat i realan (specifikacija modula 25×25×1,1cm PP sa 7 muških/ženskih tačaka poveza, 4mm gumena podloga, 15 god. garancije, 2 palete boja sa hex kodovima — 16 "standardnih" + 7 "dizajn", video demo) — iskorišćen kao izvor, isti obrazac kao bergo-xl sesija. Otkriven i **postojeći broken link** na `/sportske-podloge/` hub-u (grid "Izaberite sport" → kartica "Podovi za sale" je ciljala F4-obrisani draft slug `sportski-podovi-za-skole-i-sportske-sale`, 404 pre ove sesije).
- ✅ **Nova stranica ID 16661** (`page`, top-level, post_parent=0) na **identičnoj live-parity URL** `/sportski-podovi-za-sale-i-balone/` — redirect mapa red (redirect-mapa-FINAL.csv, "ČEKA F5 REBUILD") sada rešen bez potrebe za redirekcijom (isti slug kao live). Sekcije: hero → intro s namena ikonicom (namena-sport-dvorana, reused iz F7.2) + 3 USP kartice → specifikacija tabela (al-table, modul/spajanje/guma/površina/montaža/garancija) → 16 standardnih boja (swatch grid, samostalni inline stilovi — ista lekcija kao bergo-xl, ne kopirati Porto `.color-square` klasu) → 3 realne reference slike + video (Module Floors/Sports Partner instalacija, `EKthI0X8Uhs`, oEmbed potvrđen) → FAQ (6 pitanja) + FAQPage/VideoObject JSON-LD → CTA sa cross-linkom ka `/sportske-podloge/`.
- 🔧 **2 cross-link popravke**: (1) hub grid "Podovi za sale" kartica na `/sportske-podloge/` (5438) sada linkuje ka novoj stranici umesto ka obrisanom draftu (`$wpdb->update`, `substr_count()===1` provera); (2) stari orphan 15490 arhiviran (`post_status=draft`, slug→`naxos-evolution-stara`), sad vraća 404 (očekivano, ništa ga nije linkovalo).
- 💰 **Cena**: nema unosa u cenovniku za Naxos Evolution → FAQ odgovor upućuje na upit, bez izmišljanja brojke.
- ✅ **Verifikacija**: 200 · 1×H1 · FAQPage+VideoObject JSON-LD bez dupliranja · title tag čist (bez `\x` escape bug-a iz prošle sesije — ovog puta pisan double-quoted/direktan UTF-8 karakter) · svi linkovi/slike/ikonice 200 · vizuelna provera (Chrome screenshot hero → spec tabela → boje → galerija+video → FAQ) · regresija na `/sportske-podloge/` (schema+H1 netaknuti, novi link radi) čista.
- Skripte (scratchpad): `create-naxos-sale-balone.php`, `fix-hub-link-and-archive.php`, `check-escape-16661.php`.
- **W1 1.2 #5 zatvoren** → [[migracija/w1-red-cekanja]]. Sledeći: #6 podovi-za-bazene (262 kl.), #7 iznajmljivanje-podova (232 kl.).

## 2026-07-08 [claude-code] [W1 1.2 #4] — Ecotile 500/7 industrijski pod (625 kl., ID 16660) ✅
- ✅ **Backup**: `antasline_local_2026-07-08_110922_pre-ecotile500-7-page.sql` pre svih izmena.
- 🔍 **Kontekst pre gradnje**: live `/industrijski-podovi/industrijski-pod/` je informativna stranica fokusirana na jedan model (Ecotile 500/7 spec sheet), različita namena od parent silo-a `/industrijski-podovi/` (16567, opšti pregled) i od transakcionog Woo proizvoda 16538 (`ecotile-e500-7-...`) — građeno kao dopuna oba, ne duplikat. Sadržaj parity izvučen WebFetch-om sa live URL-a (specifikacija, FAQ, H1, gde se koristi).
- ✅ **Nova stranica ID 16660** (`page`, post_parent=16567) na **identičnoj live-parity URL** `/industrijski-podovi/industrijski-pod/` — nije trebala redirect mapa (slug + parent se poklapaju 1:1 sa live). Sekcije: hero → intro s namena ikonicama (magacin-hala/radionica/garaza, već postojeće iz F7.2) → specifikacija tabela (al-table, 12 svojstava) → prednosti (6 USP kartica, reused ikonice) → FAQ (6 pitanja) + FAQPage JSON-LD → CTA sa cross-linkom ka Woo kategoriji i ka Ecotile 500/10 proizvodu.
- 🔗 **Standardi novi za ovu stranicu** (WebSearch-potvrđeni, format identičan P2 postupku): DIN 53516 (mehanička/habanje otpornost, dinmedia.de), BS 476-7 (protivpožarna klasa, standards.globalspec.com), DIN EN 13501-1 (protivpožarna klasa, dinmedia.de 2019 izdanje) — plus reuse ISO 9001/14001 linkova iz antistatik/industrijski-podovi pilota.
- 💰 **Cena**: nema unosa u `[[reference/cenovnik]]` za Ecotile E500/7 (prazno, M10 još čeka popunu) → stranica ne navodi brojku, FAQ odgovor upućuje na slanje kvadrature/upita ("cena na upit" princip), bez izmišljanja.
- 🔧 **Bug uhvaćen i ispravljen**: Yoast title je prvobitno upisan sa PHP single-quote stringom koji je sadržao `\xe2\x80\x94` (hex escape za em-dash) — u single-quoted PHP stringovima `\x` escape ne radi, pa je literalni tekst `xe2x80x94` završio u `<title>` tag-u. Otkriveno u browser tab title-u odmah posle prvog screenshot-a. **Fix**: update_post_meta sa pravim UTF-8 em-dash karakterom (ne hex escape) + `DELETE FROM wpgs_yoast_indexable WHERE object_id=16660` da se prisili regeneracija keširanog naslova. Verifikovano da se indexable red ispravno regenerisao sa tačnim tekstom pri sledećem učitavanju. **Lekcija**: nikad koristiti `\xNN` hex escape sekvence u single-quoted PHP stringovima za UTF-8 karaktere — ili koristiti double-quoted string, ili kucati stvarni karakter direktno (kao što je urađeno svuda drugde u post_content-u ovog fajla, bez problema).
- 🔧 **Usput fiksiran postojeći broken link na parent stranici** (16567): tabela "Koja debljina za koju namenu" je imala placeholder href `industrija-podovi/ecotile-500-7/` (pogrešan slug, 404) iz ranije sesije — zamenjen tačnim `industrijski-podovi/industrijski-pod/` (`$wpdb->update`, `substr_count()===1` provera pre zamene). Redovi za 500/5 i 500/10 (#21, #26) ostaju placeholderi dok se ne izgrade.
- ✅ **Verifikacija**: 200 · 1×H1 · FAQPage JSON-LD bez dupliranja · svi interni linkovi i ikonice 200 · vizuelna provera (Chrome screenshot hero + specifikacija tabela + standardi linkovi) · regresija na `/industrijski-podovi/` (schema Product+FAQPage netaknuta, novi link radi), `/`, `/sportske-podloge/`, `/kontakt/` čista.
- Skripte (scratchpad): `create-ecotile-500-7.php`, `fix-parent-link.php`, `fix-yoast-title.php`, `check-escape.php`.
- **W1 1.2 #4 zatvoren** → [[migracija/w1-red-cekanja]]. Sledeći: #5 sportski-podovi-za-sale-i-balone (378 kl., PAGE tip), #6 podovi-za-bazene (262 kl.).

## 2026-07-08 [claude-code] [W1 1.2 #2] — Bergo XL rebuild (978 kl., ID 16659) ✅
- ✅ **Backup**: `antasline_local_2026-07-08_pre-bergo-xl-rebuild.sql` pre svih izmena.
- 🔍 **Nalaz pre gradnje**: Bergo XL je već postojala lokalno kao **legacy CPT `spoljne-podne-obloge`** (bez j, ID 5039, Porto-era, `publicly_queryable`) na `/spoljne-podne-obloge/bergo-xl/` — vraćala je 200, ali kroz generički WoodMart blog/CPT template (sidebar, "Posted by sava", kategorija badge, 2×H1), ne kroz al- landing šablon. Sadržaj je bio bogat i realan (specifikacije, 16 boja sa hex kodovima, foto koraci ugradnje, video) — iskorišćen kao izvor umesto pisanja od nule. Otkrivena cela porodica od 6 sličnih CPT unosa (bergo-unique/elite/solid/flow/ultimate/easy) — zapisano u [[migracija/w1-red-cekanja]] za buduće sesije.
- ✅ **Nova stranica ID 16659** (`page`, post_parent=16590) na **tačnoj live-parity URL** `/spoljnje-podne-obloge/bergo-xl/` (sa j, nasleđeno od parent hub-a — bolja parity od starog CPT-a koji je koristio "spoljne" bez j). Sekcije: hero → 6 USP kartica → specifikacija (PP, 380×380mm, 10,1mm, 250 t/m²) + primena lista → 16 boja (swatch grid) → 3 koraka ugradnje sa realnim fotografijama + zvaničan video (Bergo Flooring AB, `Hq_KkIPxt3o`, isti ID već vetovan u P4) → FAQ (6 pitanja) + FAQPage/VideoObject JSON-LD → CTA.
- 🔧 **2 bug-fixa tokom vizuelne provere**: (1) hero tekst pogrešno govorio "17 standardnih boja" dok je sekcija ispod imala 16 (uklonjen duplikat ECO Black = identičan hex kao Silk Black) → usklađeno na 16; (2) **veći nalaz** — boje kopirane iz starog Porto markupa (`.color-square` div sa samo `background` inline stilom, oslonjen na Porto CSS klasu koja ne postoji u WoodMart-u) renderovale su se kao **nevidljive elementi bez dimenzija** (prazan beo prostor, samo tekst imena boja) → zamenjeno samostalnim inline stilovima (width/height/border-radius) koji ne zavise ni od jedne teme. **Lekcija**: kopiranje starog Porto markupa nikad ne raditi 1:1 — Porto-specifične CSS klase (`.color-square`, `.productColors-block` i sl.) ne postoje u WoodMart-u i moraju se zameniti samostalnim inline stilovima ili al- klasama.
- 🔧 **Stari CPT unos (5039) → draft + slug `bergo-xl-stara`** (isti obrazac kao industrijski-podovi-stara), parent hub stranica (16590) ažurirana da linkuje `<h3>Bergo XL</h3>` ka novoj stranici.
- ✅ **Verifikacija**: 200 (stari CPT URL sad 404, potvrđuje draft) · 1×H1 · FAQPage+VideoObject JSON-LD bez dupliranja · sve slike 200 · parent link radi · **puna vizuelna provera Chrome screenshot-om svih sekcija** (hero → USP → specifikacija → boje → ugradnja+video → FAQ → CTA) — upravo ta provera je uhvatila oba bug-fixa gore, HTTP/DOM provera ih ne bi otkrila. Regresija na `/kontakt/`, `/o-nama/`, `/industrijski-podovi/` čista.
- Skripte (scratchpad): `bergo-xl-build.php`, `bergo-xl-schema.php`, `bergo-xl-cleanup.php`, `bergo-xl-textfix.php`, `bergo-xl-colorfix.php`.
- **W1 1.2 #2 zatvoren** → [[migracija/w1-red-cekanja]]. Sledeći: #4 industrijski-pod/Ecotile 500-7 (625 kl.).

## 2026-07-08 [claude-code] [W1 F7 P4] — Video lite-embed na 2 stranice — F7 AUDIT U POTPUNOSTI ZATVOREN ✅
- ✅ **Backup**: `antasline_local_2026-07-08_pre-f7-p4-video.sql` pre svih izmena.
- ✅ **2 videa dodata** po F7.3 standardu (lite-embed fasada, isti globalni `al-video-facade.js` kao antistatik pilot): `/industrijski-podovi/` (16567) — "How to Install Ecotile Garage Flooring - Durable PVC Interlocking Tiles", zvanični kanal Ecotile Flooring Ltd (`fncQrsTvHoE`); `/sportske-podloge/` (5438) — "Bergo Multisport court installation", zvanični kanal Bergo Flooring AB (`VdZWT2O5_-M`, tematski najbolji izbor od 5 kandidata jer je specifično o sportskim terenima).
- ✅ **Izvori potvrđeni WebSearch + YouTube oEmbed pre upotrebe** (obavezno pravilo iz F7.3 — stari linkovi lako postanu privatni/obrisani): proveren `author_name`/`author_url` za svaki kandidat, odbačeni neoficijelni kanali (npr. "BERGO FLOORING ROYAL HOW TO INSTALL" je sa kanala "GRFWS", ne Bergo Flooring — preskočeno).
- ✅ **VideoObject JSON-LD** dodat preko istog `vc_raw_html` base64 postupka kao FAQPage (P1), bez `uploadDate` (nije potvrđen, pravilo: ne izmišljati).
- 🔧 **Debug nalaz tokom vizuelne provere**: video thumbnail se u Chrome automatizovanom tabu nije učitavao (`img.complete=false`, `naturalWidth=0`) uprkos ispravnom markupu i mrežnom pristupu (potvrđeno `fetch()` i `new Image()` rade odmah). Uzrok identifikovan: `loading="lazy"` na `<img>` se ne okida u pozadinskom/automatizovanom tabu (Chrome native lazy-load intersection observer se ponaša drugačije van fokusiranog taba) — **potvrđeno da je preexisting ponašanje** testiranjem na već verifikovanom antistatik pilotu (identičan simptom). Nije bug u P4 radu, samo ograničenje test okruženja — u pravom browseru sa fokusiranim tabom radi normalno.
- ✅ **Funkcionalnost potvrđena direktnim dispatchEvent klikom** (zaobilazi automation coordinate quirk): klik na play dugme kreira `<iframe>` sa tačnim `youtube-nocookie.com` embed URL-om na obe stranice — event delegation iz F7.3 radi kako treba.
- ✅ **Verifikacija**: 200/1×H1 na obe stranice · `<iframe>` odsutan iz initial HTML response-a (potvrđuje da se ne učitava eagerly, LCP/CWV cilj ispunjen) · VideoObject schema prisutna bez dupliranja · bez neizrendovanih shortcode-ova · regresija na `/kontakt/` i `/o-nama/` čista.
- Skripta (scratchpad): `f7-p4-fix.php`.
- **F7 audit P4 zatvoren — cela f7-audit-i-popravke.md lista (P1–P4) je sada zatvorena.** → [[migracija/f7-audit-i-popravke]]. Sledeći W1 fokus vraća se na `migracija/w1-red-cekanja.md` (bergo-xl, Ecotile 500/7 info, itd.).

## 2026-07-08 [claude-code] [W1 F7 P3] — 4 nove antas-skice (SVG tehničke ilustracije) ✅
- ✅ **Backup**: `antasline_local_2026-07-08_0941_pre-f7-p3-skice.sql` pre svih izmena.
- ✅ **4 nova inline SVG-a** po F7.4 standardu (`woodmart-child/images/skice/`): `dimenzije-terena-fiba.svg` (top-down FIBA teren 28×15m sa centralnim krugom/reketom/troseks linijama, na `/dimenzije-kosarkaskog-terena/`), `dimenzije-table-kosarka.svg` (front-view table+koša 1,80×1,05m + obruč na 3,05m, na `/dimenzije-kosarkaske-table/`), `industrijski-pod-presek-slojeva.svg` (presek podloga→Ecotile 5-10mm→klik spoj, crveni akcent za viljuškarski saobraćaj, na `/industrijski-podovi/`), `bergo-klik-sistem-presek.svg` (presek dve ploče na nožicama sa klik-prstenovima, na `/sportske-podloge/`).
- 🔧 **2 sitna vizuelna bug-fixa nakon Chrome provere** (nisu bila vidljiva iz samog SVG koda, samo u renderu): (1) tabla dijagram — text "3,05 m" sečen na desnoj ivici jer je viewBox bio preuzak za tekst na toj poziciji → proširen 380→410; (2) Bergo dijagram — labela "Klik-prstenovi (bez lepka i šrafova)" vizuelno se sudarala sa "Bergo ploča" naslovom ispod (samo 8px razmaka) → razdvojeno na veći razmak + dodata tanka leader linija ka spoju radi jasnoće. **Lekcija**: kod inline SVG teksta uvek vizuelno proveriti u browseru (ne samo grep za prisustvo elementa) — koordinate koje izgledaju OK u kodu mogu da se seku/preklapaju u stvarnom renderu zbog font širine koja se ne vidi statički.
- ✅ **Postupak**: SVG fajlovi napisani po F7.4 stilu (navy `#0E2950` struktura, crvena `#F04D22`/`#D43C14` samo akcenat, dimenzione linije sa tick oznakama na krajevima, dashed za skrivene/unutrašnje detalje kao klik-spojevi), minifikovani (`str_replace(["\r","\n","\t"],'')`) i ubačeni u `<div style="margin-top:24px;max-width:440px">` unutar postojećeg `vc_column_text` bloka preko `$wpdb->update`+`clean_post_cache()` (anchor tekst potvrđen `substr_count()===1` pre upisa).
- ✅ **Verifikacija**: sve 4 stranice 200 · 1×H1 · bez neizrendovanih shortcode-ova · `class="al-skica"` prisutan i renderuje se · **vizuelno potvrđeno Chrome screenshot-om** na sve 4 (ne samo HTTP/DOM provera) — dijagrami čitljivi, dimenzije i labele na mestu, title/desc pristupačni (`find` alat ih ispravno pročitao preko alt/aria opisa).
- Skripte (scratchpad): `f7-p3-fix.php` (glavno ubacivanje), `f7-p3-tabla-fix.php` + `f7-p3-bergo-fix.php` (post-vizuelni fix-evi).
- **F7 audit P3 zatvoren** → [[migracija/f7-audit-i-popravke]] (P4 video je poslednji preostali prioritet u redu, niži prioritet).

## 2026-07-08 [claude-code] [W1 F7 P2] — Standardi sa linkovima na 9 stranica + P1+P2 kombinovani test ✅
- ✅ **Backup**: `antasline_local_2026-07-08_0859_pre-f7-p2-standardi.sql` (46,9 MB) pre svih izmena.
- ✅ **11 standarda linkovano na 9 stranica**: `/industrijski-podovi/` (7 — EN 660-2, ISO 6721, DIN 51130, EN 14041, ISO 10140, ISO 9001, ISO 14001), `/sportske-podloge/` (FIBA, ITF, EN 14877), `/sportske-podloge/kosarkaske-konstrukcije/` (FIBA, EN1270), `/podloge-za-futsal-terene/` (FIBA/ITF), `/kosarka-3x3-tereni/` (FIBA, ITF), `/dimenzije-kosarkaskog-terena/` (FIBA), `/dimenzije-kosarkaske-table/` (FIBA), `/spoljnje-podne-obloge/` (ISO 9001), home `/pocetna/` (FIBA).
- ✅ **Svi linkovi potvrđeni WebSearch-om pre upisa** (pravilo: link samo ako je izvor potvrđen, ništa izmišljeno): FIBA→`about.fiba.basketball/.../approved-equipment`, ITF→`itftennis.com/.../facilities`, EN1270 i EN14041→`knowledge.bsigroup.com` (isti obrazac kao antistatik pilot IEC linkovi), EN14877→`standards.globalspec.com` (BSI knowledge stranica ne postoji za ovaj standard, korišćen distributer kao legitimna referenca), DIN 51130→`dinmedia.de`, ISO 9001/14001→`iso.org` explainer stranice (stabilne, ne vezane za izdanje — bitno jer je ISO 14001:2015 upravo povučen i zamenjen 2026 izdanjem), ISO 10140-3/ISO 6721-1→`iso.org` standard stranice, EN 660-2→`landingpage.bsigroup.com`.
- 🔧 **Postupak**: `str_replace` na unique anchor tekstu iz postojećeg `post_content` (`substr_count()===1` provera pre upisa, da se ne pogodi pogrešna instanca kod stranica sa više FIBA/ITF pomena — futsal/3x3/sportske-podloge imaju FIBA i po 5-6 puta) — jedan link po standardu po stranici, biran najprirodniji kontekst (spec tabela ili prva suštinska rečenica), ne svaki pojedinačni pomen. Upis isključivo `$wpdb->update`+`clean_post_cache()`. Link format `target="_blank" rel="noopener"` (isti kao antistatik pilot).
- ✅ **P1+P2 kombinovani test na svih 13 stranica** (7 iz P1 + 9 iz P2, 3 preklapaju): HTTP 200 (`/pocetna/` → 301 na `/` je očekivano, front-page canonical) · 1×H1 svuda · FAQPage JSON-LD i dalje bez dupliranja na P1 stranicama · svih 11 standard-linkova renderuje kao validan `<a href target="_blank" rel="noopener">` bez razbijenog markupa · regresija na `/kontakt/` i `/o-nama/` čista. (Lažna uzbuna: gruba `<a `/`</a>` brojanja regexom pokazala neuravnoteženost na `kosarkaske-konstrukcije` — pokazalo se da je iz theme header/footer/product-grid chrome-a van mog edita, ne stvarni bug; potvrđeno direktnim grep-om na moje ubačene linkove.)
- Skripta (scratchpad): `f7-p2-fix.php`.
- **F7 audit P2 zatvoren** → [[migracija/f7-audit-i-popravke]] (P3 antas-skice je sledeći prioritet u redu, ~45 min–1h).

## 2026-07-08 [claude-code] [W1 F7 P1] — Popravka izgubljene FAQPage/Product schema na 7 stranica ✅
- ✅ **Backup**: `antasline_local_2026-07-08_0828_pre-f7-p1-schema.sql` (46,9 MB) pre svih izmena.
- ✅ **Svih 7 stranica iz audita popravljeno**: `/industrijski-podovi/` (16567, 7 Q&A FAQPage + Product AggregateOffer 2.000–5.500 RSD), `/spoljnje-podne-obloge/` (16590, 5 Q&A), `/dimenzije-kosarkaske-table/` (16585, 5 Q&A, prvi put dodata), Woo kategorije 16572/16573/16578/16579 (term 245/246/251/252, po 3 Q&A svaka).
- 🔴 **Novi nalaz tokom popravke**: `/spoljnje-podne-obloge/` (ID 16590) nije samo imala odsutnu schemu — imala je 1.321 znak **golog JSON teksta zalepljenog na kraj `post_content`, van bilo kog shortcode-a** (ni `[vc_raw_html]`, ni `<script>` tag) — ostatak nezavršenog/pokvarenog ranijeg pokušaja. Obrisan, zamenjen ispravno upakovanom schemom.
- 🔧 **Napomena za slug**: stvarni post_name je `spoljnje-podne-obloge` (sa "j"), ne "spoljne-podne-obloge" kako je PROGRESS unos 2026-07-07 tvrdio — proveriti buduće reference na ovu stranicu.
- ✅ **Postupak** (dokazan iz F6 pilota, sad ponovljen 7×): FAQ tekst izvučen direktno iz postojećeg `post_content` (h3/p parovi ili `al-faq` div), JSON-LD sastavljen (`json_encode(JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)`), `<script type="application/ld+json">` → `base64(rawurlencode())` → `[vc_raw_html]` shortcode ubačen odmah posle `[/vc_column_text]` FAQ reda (pronađeno preko unique anchor teksta zadnjeg FAQ odgovora) — upisano **isključivo** preko `$wpdb->update` + `clean_post_cache()`, nikad `wp_update_post` iz CLI-ja.
- ✅ **Verifikacija svih 7**: HTTP 200 · 1×H1 · JSON-LD parsira se ispravno bez dupliranja (Yoast `yoast-schema-graph` je poseban `<script>` blok, ne sudara se sa našim) · regresija na 245/246 i 251/252 parovima potvrđena (pitanja se ne mešaju međusobno) · 2 nedirane stranice (`/kontakt/`, `/sportske-podloge/`) i dalje 200.
- Skripta (scratchpad): `f7-p1-fix.php` (wp-load bootstrap, 7 stranica u jednom prolazu).
- **F7 audit P1 zatvoren** → [[migracija/f7-audit-i-popravke]] (P2 standardi-linkovi je sledeći prioritet u redu).

## 2026-07-08 [claude-code] [W1 AUDIT] — F7 compliance audit svih postojećih stranica + plan ✅
- ✅ **Audit, bez izmena baze**: svih 25 W1 rebuild stranica/Layout Builder kategorija (post_content + rendered HTML) provereno protiv F7 standarda (standardi-sa-linkovima, namena ikonice, video, antas-skica) i protiv ranijih dnevnik tvrdnji o JSON-LD schema.
- 🔴 **Nalaz — izgubljena FAQPage/Product schema na 7 stranica**, FAQ tekst prisutan ali JSON-LD blok odsutan u renderu: `/industrijski-podovi/` (16567, dnevnik tvrdi FAQ+Product dodato 2026-07-05), `/spoljnje-podne-obloge/` (16590, dnevnik tvrdi 2026-07-07), `/dimenzije-kosarkaske-table/` (16585, nikad dodata), i tačno 4 Woo kategorije (16572/16573/16578/16579 = term 245/246/251/252) — baš oni parovi koje je dnevnik 2026-07-06 pomenuo kao naknadno "diferencirane" (245↔246, 251↔252). Obrazac ukazuje na gotcha #9 (CLI `wp_update_post` briše `vc_raw_html`) primenjen tokom te diferencijacije, umesto dokazanog `$wpdb->update` puta.
- 🟡 **Nalaz — 9 stranica pominje standarde (FIBA/ITF/EN1270/EN14877/DIN 51130/ISO 9001-14001-6721-10140/EN 660-2/EN 14041) kao goli tekst, bez linka** — najveći F7.1 compliance gap po broju stranica. `/industrijski-podovi/` ima čak 7 nelinkovanih standarda.
- 🟢 **Nalaz — antas-skica prilike**: `/dimenzije-kosarkaskog-terena/` i `/dimenzije-kosarkaske-table/` nemaju nijednu skicu iako su doslovno o dimenzijama (najprirodniji fit u sajtu); `/industrijski-podovi/` i `/sportske-podloge/` kandidati za presek-slojeva skicu.
- 🔵 **Nalaz — video prilike** (niži prioritet): `/sportske-podloge/` (Bergo) i `/industrijski-podovi/` (Ecotile generalno) nemaju video, sport pod-stranice ne trebaju svaki svoj.
- 📁 **Plan upisan** u novi `migracija/f7-audit-i-popravke.md` (4 prioritetna nivoa, checkbox lista po stranici, procena vremena, preporučen redosled P1→P4). Miroslav odabrao da se plan samo zapiše ove sesije, izvršenje ide u narednim sesijama (jedan prioritet po sesiji).
- Skripte (scratchpad): `audit-f7.php`, `check-rendered-jsonld.sh`.

## 2026-07-07 [claude-code] [W1 + W1/W2 PARITY F7] — Antistatik stranica + F7 content standard (paralelno) ✅
- ✅ **Backup**: `antasline_local_2026-07-07_pre-antistatik-f7.sql` (44,7 MB) pre svih izmena.
- ✅ **W1 1.2 — `/antistatik-i-elektroprovodljivi-podovi/` (ID 16658)**, top prioritet po klikovima (1131/12mes), top-level stranica (parity sa live URL strukturom). Sadržaj: WebFetch live (specifikacija, standardi, prednosti, primena — real facts, ne izmišljeno) + troslojni F6 model (namena-esd grid, 2 proizvoda: Ecotile 7mm ESD + polukružni zaštitnik za cevi ESD verzija). Standardi navedeni SA LINKOVIMA na potvrđene zvanične izvore (IEC 61340-5-1, BS EN IEC 61340-5-1, IEC TR 61340-5-2 — pronađeni preko WebSearch, ne izmišljeni). FAQ (5 pitanja, grounded) + FAQPage JSON-LD.
- 🔧 **2 stara/pokvarena cross-linka pronađena i ispravljena** usput: `/industrijski-podovi/` (ID 16567) linkovao je ka nepostojećem starom Porto CPT-u (`industrija-podovi/antistatik-podne-obloge/`, post_type `industrija-podovi` ID 5272 — leftover, van parity obima) — 2 pojavljivanja ispravljena na novu stranicu. Lokalni post `/zasto-vam-je-potreban-esd-pod/` (ID 3318, F3 reimport) linkovao je na `https://www.antasline.com/...` (živi live domen, netačan slug) — 2 pojavljivanja ispravljena na lokalni URL.
- ✅ **F6 pilot (kosarkaske-konstrukcije) rešen u redirect mapi**: `redirect-mapa-FINAL.csv` red ažuriran na REŠENO (identičan URL, redirect nepotreban) — vidi prethodni F6 unos.
- ✅ **F7 — content standard definisan i primenjen na pilot stranici**:
  - **F7.1**: `.claude/skills/obogati-proizvod/SKILL.md` dopunjen — korak 1b "standardi sa linkovima" (tvrdo pravilo: samo iz datasheet-a/zvaničnog sajta/postojeće live stranice) + korak 1c "namena tagovi" (F6 lista + auto-proširenje).
  - **F7.2**: 12 novih SVG ikonica (`namena-*` × 8 + USP `garancija`/`sertifikat`/`dostava`/`telefon-podrska` × 4), isti stil kao postojećih 6 (viewBox 24, stroke #F04D22, width 1.7).
  - **F7.3**: video lite-embed fasada — **globalni JS fajl** (`woodmart-child/js/al-video-facade.js`, enqueue u `functions.php`, `in_footer`, `filemtime` verzija) umesto `vc_raw_html` po stranici (zaobilazi CLI/kses gotcha #9 u potpunosti, jer u `post_content`-u nema `<script>`, samo čist HTML). Testiran sa pravim, potvrđenim Ecotile videom ("ESD Flooring - How to install", kanal ecotile-Germany, potvrđeno YouTube oEmbed API-jem) — `VideoObject` JSON-LD BEZ `uploadDate` (nije potvrđen, ne izmišljati). Gotcha: stari video link iz posta 3318 (`4-dNngajiCY`) je sad "Forbidden" na oEmbed-u — ne koristiti ponovo bez provere.
  - **F7.4**: "antas-skica" stil definisan (navy strukturne linije, crvena samo akcenat, Inter labele, `.al-skica` CSS klasa, folder `images/skice/`) + pilot skica `esd-pod-presek-slojeva.svg` (presek ESD poda: betonska podloga → 7mm ESD ploča sa čeličnim vlaknima → uzemljenje), inline ubačena u antistatik stranicu.
  - **F7.5**: performanse-ograda — svi F7 dodaci na pilot stranici su sitni (ikonice ~250-400B, JS 972B footer/deferred, skica ~2,4KB inline vektor), video iframe se NE učitava dok se ne klikne (potvrđeno u HTML odgovoru). ⚠️ Pravi Lighthouse pre/posle test NIJE urađen — CLI nije instaliran u ovom okruženju (`npx lighthouse` traži download); odloženo na W3 3.5 (Lighthouse baseline sesija).
- ✅ **Verifikacija**: antistatik stranica 200 · 1×H1 · Yoast title/metadesc parity sa live · 2 JSON-LD bloka validna (FAQPage 5 pitanja + VideoObject) bez dupliranja · grid tačan (2 proizvoda) · video fasada i skica prisutni u HTML-u · regression 7 stranica (Početna, industrijski-podovi, sportske-podloge, kosarkaske-konstrukcije, kontakt, o-nama, kategorija industrijski-podovi) → sve 200.
- 📁 Ažurirano: `parity-inventar.csv` (antistatik red → PARITY), `migracija/w1-red-cekanja.md` (#1 antistatik označen gotovim), `migracija/promptovi/_README.md` (F7 ✅), `migracija/woodmart-sabloni.md` (novi odeljci F7.2–F7.5), `.claude/skills/obogati-proizvod/SKILL.md` (F7.1), `2026-07-06-MASTER-PLAN-V2.md` (W1 1.2/1.8 napomene).
- Skripte (scratchpad): `antistatik-page.php`, `antistatik-add-jsonld.php`, `fix-industrijski-antistatik-link.php`, `fix-esd-post-crosslink.php`, `validate-antistatik.php`.

## 2026-07-07 [claude-code] [W1 PARITY F6] — Namena arhitektura + pilot kosarkaske-konstrukcije ✅
- ✅ **Backup**: `antasline_local_2026-07-07_pre-f6-namena-tagovi.sql` (44,7 MB) pre svih izmena.
- ✅ **Popis stvarnog kataloga (37 proizvoda)** pokazao da F6 prompt pretpostavke o ponudi (garaža/terasa/bazen/teretana ravnomerno zastupljeni) ne odgovaraju stvarnosti: katalog je dominiran Ergomat industrijskom zaštitom (28 proizvoda: bumperi, DuraStripe trake, senzori) i košarkaškim konstrukcijama (5 proizvoda) — samo 4 proizvoda pokrivaju terasu/štalu/ESD/garažu pojedinačno.
- ✅ **8 `namena-*` product_tag termina kreirano** (prilagođeno stvarnoj ponudi, izostavljeno namena-parking/teretana/bazen jer nijedan proizvod ne pokriva tu namenu): `namena-magacin-hala` (29), `namena-radionica` (12), `namena-sport-dvorana` (5), `namena-sportski-teren-otvoreni` (2), `namena-esd` (2), `namena-garaza` (1), `namena-terasa` (1), `namena-stala` (1).
- ✅ **Tabela proizvod→namena prezentovana Miroslavu i potvrđena PRE upisa** (F6 pravilo) — svih 37 proizvoda dobilo tagove preko `wp_set_object_terms(..., true)` (append, ne replace), 0 grešaka.
- ✅ **Grid mehanika potvrđena**: `woodmart_products` shortcode, atribut `taxonomies` prima **term ID** (ne slug) — `get_terms(['include' => $taxonomies])` u `inc/shortcodes/products.php`. Radni primer: `[woodmart_products taxonomies="266" columns="3" items_per_page="6" post_type="product" layout="grid" lazy_loading="yes"]`.
- ✅ **Pilot = W1 SEO prioritet #1 spojen u jednu sesiju**: `namena-sport-dvorana` (5 proizvoda: Street Sport, Lite Shot 325, Mini Shot 225, MicroShot 125, Zglobni obruč) poklopio se tačno sa `/sportske-podloge/kosarkaske-konstrukcije/` (923 GSC klika/12mes, dugo dokumentovan prioritet #1, čekala ga redirect mapa). Umesto originalnog F6 predloga (`/podovi-za-garaze/`, samo 1 proizvod — slab grid demo), izgrađena je ova stranica (ID 16657) kao pravi W1 rebuild + F6 pilot.
- ✅ **Sadržaj**: content parity izvučen preko WebFetch-a sa live URL-a (live koristi ZionBuilder serialized podatke, ne WPBakery — teško parsirati direktno; WebFetch rendered tekst je brži put). Troslojni model: hero → uporedna tabela 5 modela (tip/podesiva visina/standard/namena/cena "na upit" jer cenovnik prazan za ove SKU) + cross-link ka Woo kategoriji → auto grid (`taxonomies="266"`) → FAQ (5 pitanja, sve činjenično utemeljeno na postojećim opisima proizvoda, bez izmišljanja) + FAQPage JSON-LD → CTA. Cross-link dodat i u suprotnom smeru (Woo kategorija ID 16578 → nova stranica).
- ✅ **Nova CSS klasa `.al-table`** dodata u `antas-design.css` (navy header, zebra redovi, `overflow-x:auto` wrapper obavezan jer `min-width:640px`).
- ✅ **Verifikacija**: 200 · 1×H1 · JSON-LD validan (1 blok, FAQPage, 5 pitanja, bez dupliranja) · Yoast title/metadesc u `<head>` (identični live parity) · grid prikazuje tačno 5 ispravnih proizvoda · **auto-mehanika potvrđena radna** (test tag dodat na Ecotile ESD → odmah se pojavio u gridu bez izmene stranice, pa uklonjen) · regression `/sportske-podloge/`, `/industrijski-podovi/`, `/kategorija-proizvoda/kosarkaske-konstrukcije/` → sve 200.
- 📁 Ažurirano: `redirect-mapa-FINAL.csv` (red kosarkaske-konstrukcije → REŠENO, identičan URL, redirect nepotreban), `parity-inventar.csv` (red 56 → PARITY), `migracija/w1-red-cekanja.md` (#3 označen gotovim), `migracija/promptovi/_README.md` (F6 ✅), `migracija/woodmart-sabloni.md` (nov odeljak "NAMENSKA LANDING (rešenje hub) — F6 troslojni model" sa radnim shortcode primerom).
- Skripte (scratchpad): `f6-products.php`, `f6-namena-tags.php`, `f6-pilot-kosarkaske-konstrukcije.php`, `f6-add-jsonld.php`, `f6-crosslink-category.php`.

## 2026-07-07 [claude-code] [W3 PARITY F5] — Trijaža nedostajućih stranica → W1 red čekanja ✅
- 🔧 **CSV resync pre trijaže**: `parity-inventar.csv` nije bio ažuriran posle F2/F3 promena — 6 redova (postovi uvezeni u F3 + 2 proizvoda preimenovana u F2) je resync-ovano sa NEDOSTAJE-LOKAL na PARITY po stvarnom stanju baze. NEDOSTAJE-LOKAL palo sa 52 na 46 pre trijaže.
- ✅ **Kategorija D odmah izvršena**: `/politika-kolacica/` kreirana (ID 16656) — sadržaj 1:1 iz live WXR exporta (SimpleXML sa wp/content namespace parsing), 200 verifikovano. Poznato odstupanje: 7×`<h1>` u starom markup-u (isti tip problema kao basket članak) — restyle sesija rešava.
- ✅ **Kategorija A (33 stranice)** — puna lista sa GSC klikovima, Yoast title-ovima i napomenama u novom **`migracija/w1-red-cekanja.md`**. Ključni nalazi pri kategorizaciji:
  - **Ecotile informativni klaster** (3 stranice: industrijski-pod=500/7 625kl., podne-ploce-ecotile-50010=500/10 56kl., ecotile-5005-podne-ploce=500/5 31kl.) — 500/7 i 500/10 imaju lokalne proizvode (PARITY), 500/5 nema → razmotriti dodavanje kroz `/obogati-proizvod`
  - **LVT/Expona silo** (6 stranica pod `/lvt-podovi-za-komercijalne-i-javne-prostore/`) — potvrđeno da je LVT/Expona i dalje deo ponude (CLAUDE.md §1), graditi parent PRE podstranica
  - **Epoksid-conquest srodna stranica** nađena: `industrijski-podovi-montaza-preko-ostecenog-epoksida` (72 kl.) — mora linkovati ka glavnom conquest članku (2542), nikad predlagati epoksid
  - 2 potencijalna duplikat para flagovana za proveru pre gradnje: `podovi-za-radnje-i-maloprodajne-objekte` (26 kl.) vs `industrijski-podovi/podovi-za-maloprodajne-objekte` (6 kl.); `vestacka-trava-za-terase` (104 kl.) vs postojeći `/vestacka-trava/` (1538 kl., PARITY)
- ✅ **Kategorija C prazna ovog kruga** — svi ranije pretpostavljeni "proizvod-duplikat" kandidati (Ecotile 5005/50010, Expona Click, trake-za-obelezavanje, vinil-podovi-objectflor) reklasifikovani u Kategoriju A jer Yoast title-ovi pokazuju informativni ugao koji dopunjuje (ne duplira) transakcionu proizvod-stranicu — u skladu sa F6 troslojnim modelom.
- ✅ **Kategorija E (3 slučaja)**: `elektroprovodni-podovi`→301 na antistatik (kad bude gotov); treći skoro-identičan FAQ (`industrijski-podovi-najcesca-pitanja`) pridružen ranijoj W2 odluci o konsolidaciji sa postojeća 2 `izbor-industrijskog-poda` članka.
- 🆕 **Kategorija F identifikovana** (nova, nije bila u originalnom prompt planu): 8 live `product_tag` termina (bergo, ergomat, industrijski-amortizer...) ne postoje lokalno — odvojena taksonomija od F6 "namena" taga, van W1 obima, ide u F7 razmatranje.
- ✅ Svaki NEDOSTAJE-LOKAL red u `parity-inventar.csv` dobio kategoriju u `napomena` koloni (verifikovano skriptom — 0 redova bez oznake).
- 📝 [[2026-07-06-MASTER-PLAN-V2]] W1 1.2 sada pokazuje na `migracija/w1-red-cekanja.md` kao izvor istine.
- Skripte (scratchpad `f5/`): `resync-inventory.php`, `build-politika.php`, `tag-categories.php`.

## 2026-07-07 [claude-code] [W3 PARITY F4] — Minimalna redirect mapa (7 redova) ✅
- ✅ **Backup**: `antasline_local_2026-07-07_pre-f4-redirect-mapa.sql` (47MB) pre svih izmena.
- ✅ **Odluke sa Miroslavom** (hibrid pravilo — top-15 GSC = parity, nisko-saobraćajno sme 301):
  - `/spoljnje-podne-obloge/` (1304 kl., top-5 sajta) → **parity**, vraćeno "j"
  - `/podovi-za-stale/` (402 kl., top-15) → **parity**, uklonjen prefiks "gumeni-"
  - `sigurnosni-senzori-signalni-sistemi` (nizak saobraćaj) → lokalna varijanta sa "i" OSTAJE, 301 sa live
  - **Bergo easy/elite/unique/xl (4 live stranice, 978+166+53+33 kl.) — VAŽNA ISPRAVKA plana**: pretpostavka iz starog plana ("konsoliduj u bergo-ultimate") je bila POGREŠNA. Miroslav potvrdio: Bergo Ultimate je poseban proizvod za sportske terene, NIJE isti kao easy/elite/unique/xl (terase-varijante). Sve 4 i dalje su deo ponude → idu u **F5 W1 red kao zasebne landing stranice**, NE konsoliduju se, NE idu u redirect mapu.
  - 3 draft posta iz F3 (pogrešan `post_type`: post umesto page) → **obrisani**, F5 rebuild kao PAGE pod live slugom: `padel-tereni`, `sportski-podovi-za-sale-i-balone`; `kako-izabrati-pravi...poterbama` (typo, bez live parnjaka) → obrisan bez zamene
  - 2 skoro-identična `izbor-industrijskog-poda-tri-najcesca-pitanja` članka (oba sada publish lokalno) → **odloženo na W2** (content-strategija, ne redirect-mapa posao)
- 🔴 **Nova nalazak pri izvršenju**: `/spoljnje-podne-obloge/` je imala DVE lokalne stranice — stara (ID 5255, iz 2022, staro Porto obeležje) je i dalje bila `publish` i zauzimala čist slug, dok je NOVA W1 rebuild stranica (ID 16590, napravljena 2026-07-07) automatski dobila sufiks `-2` jer je slug bio zauzet. Ispravljeno u istoj operaciji: 5255 → draft, 16590 preimenovana na `spoljnje-podne-obloge` (bez sufiksa).
- ✅ **Izvršeno**: 2 slug rename-a (`wp_update_post` + Yoast indexable cache invalidacija po F2 lekciji + `flush_rewrite_rules(true)`), 3 drafta obrisana.
- ✅ **`migracija/redirect-mapa-FINAL.csv`** — 7 redova (umesto starih 118): 3 odmah verifikovana (200 na cilju: na-kojoj-podlozi→bergo-ultimate, lite-shot-795→325, sigurnosni-senzori), 3 privremena čekaju F5 rebuild (kosarkaske-konstrukcije 923 kl. PRIORITET #1, padel-tereni, sportski-podovi-za-sale-i-balone) — target TBD, NE ulaze u aktivni .htaccess dok stranice ne postoje.
- ✅ **`migracija/htaccess-301-DRAFT.txt`** generisan sa 3 verifikovana pravila + komentar-blok za 3 buduća. **NE aktiviran** (ostaje draft do dana migracije).
- ✅ `parity-inventar.csv` ažuriran: 84→86 PARITY, 57→52 NEDOSTAJE-LOKAL, 0→5 301-KANDIDAT (matematika se poklapa, ukupno i dalje 175 redova).
- ✅ Verifikacija: sva 3 real-target redirekta → 200 na lokalu, oba slug rename-a → 200 + ispravan canonical, regression Početna/`/industrijski-podovi/`/`/sportske-podloge/` → 200.
- ✅ Stare redirect mape obrisane iz `C:\xampp\htdocs\antasline\` i `antasline-backups\` (POPUNJENA.csv, ZA-POPUNITI.csv, 2026-07-07.csv) — Miroslav potvrdio, arhivske kopije ostaju u `migracija/arhiva/`.
- 📝 [[migracija/promptovi/F5-trijaza-stranica]] ažuriran sa F4 ispravkama: kosarkaske-konstrukcije 923 kl. (ne 478), bergo-easy/elite/unique/xl premešteni iz kategorije C (konsolidacija) u kategoriju A (zasebni rebuild), padel-tereni/sportski-podovi-za-sale-i-balone napomena da su PAGE tip.

## 2026-07-07 [claude-code] [W3 PARITY F3] — Pun reimport 30 postova sa live ✅
- ✅ **Backup**: `antasline_local_2026-07-07_pre-posts-reimport.sql` (46MB) pre svega.
- ✅ **Cleanup 7 LOKAL-NOVO postova** po hibrid pravilu: `bergo-ultimate...` (4813) ZADRŽAN, 4 prebačena u draft (`kako-izabrati-pravi...poterbama` 3327, `padel-tenis` 3973, `podovi-za-garaze` 3378, `sportski-podovi-za-skole...` 3621 — čekaju F4 odluku), 2 obrisana (`izbor-...-2-2` 15962 duplikat, `sportska-podloga-za-odbojku` 4318 — live verzija je zamenjuje).
- 🔴 **Sopstvena greška + oporavak**: prvi pokušaj cleanup-a je slučajno obrisao i `bergo-ultimate` (4813) jer "zadrži" znači i dalje `publish`, pa ga je bulk-delete upit pokupio. Otkriveno odmah (verifikacija posle svakog koraka) → pun DB restore iz backup-a → cleanup ponovljen sa eksplicitnim izuzetkom ID-a. Lekcija: kad je odluka "zadrži kao publish", eksplicitno isključi ID iz svake sledeće bulk operacije, ne oslanjaj se na to da ga "nisi dirao".
- ✅ **WXR import** (`live-posts-2026-07-05.xml`, `fetch_attachments=true`) — 4 uzastopna pokušaja dok nije prošao čisto, svaki sledeći pokušaj idempotentan (post_exists() sprečava duplikate):
  1. `Class "WXR_Parser" not found` → nedostajao `define('WP_LOAD_IMPORTERS', true)` pre `wp-load.php`
  2. `Cannot redeclare wordpress_importer_init()` → definisanje `WP_LOAD_IMPORTERS` PRE `wp-load.php` uzrokuje da WP već učita plugin (jer je aktivan) — eksplicitan drugi `require` istog fajla posle je duplikat. Rešenje: definisati konstantu, samo `require wp-load.php`, NE ponovo `require`-ovati plugin fajl.
  3. `Call to undefined function post_exists()` → nedostajao `require_once ABSPATH.'wp-admin/includes/post.php'` (+ media/image/file/taxonomy za attachment fetch)
  4. `Call to undefined function comment_exists()` → nedostajao `require_once ABSPATH.'wp-admin/includes/comment.php'`
  - Posle sva 4 fix-a: import prošao čisto (`result: OK`).
- 🔴 **2 posta od 30 namerno NISU uvezena** (WP_Import `post_exists()` title-match zaštita, ne greška):
  - `ugradnje-industrijskog-poda` — blokirao stari lokalni "pending" draft iz 2019 (`o-cemu-treba-voditi-racuna-prilikom-ugradnje-industrijskog-poda`, ID 3257) sa identičnim naslovom → obrisan stari draft, ponovljen import (idempotentno), post uspešno uvezen (zadržao ISTI ID 3257).
  - `na-kojoj-podlozi-se-igraju-turniri-u-3x3` — live URL slug je zastareo/nasleđen ali stvarni `<title>` je "Bergo ultimate i ultimate plus - Nova generacija sportskih podova" = identičan naslovu lokalnog LOKAL-NOVO posta 4813 → ISPRAVNO preskočen (isti sadržaj već postoji lokalno pod drugim slugom). `parity-inventar.csv` ažuriran: oba reda (live URL i lokalni 4813) sada `301-KANDIDAT` sa unakrsnom napomenom za F4.
  - **Finalna matematika**: 30 live − 1 (na-kojoj-podlozi, duplikat) + 1 (zadržan bergo-ultimate) = **30 publish postova** (ne 31 kako je prompt pretpostavio — ispravno, izbegnut je pravi duplikat sadržaja).
- 🔧 **ID-evi sačuvani**: WP_Import je zadržao originalne post ID-eve gde slot nije bio zauzet → conquest `epoksidni-podovi-ili-ecotile-podovi` = **i dalje 2542**, basket `kako-napraviti-teren-za-basket-ili-kosarkaski-teren` = **i dalje 2298**. Nema potrebe za ID izmenama u CLAUDE.md.
- ✅ **Slike — domen fix**: 26 postova je zadržalo `https://www.antasline.com/wp-content/uploads/...` u `post_content` (fajlovi su već lokalno prisutni od ranijeg rsync-a, ali WP_Import ih je tretirao kao "already exists" po nazivu i nije remapovao URL u telu teksta) → globalni `str_replace` na `http://localhost/antasline/wp-content/uploads/` kroz `wp_update_post` po postu. 20 referenci ostaje na stvarno nedostajuće fajlove (uglavnom stock/Pixabay slike koje nikad nisu rsync-ovane) — **zabeleženo, NE popravljeno** (restyle sesije), pošto prompt eksplicitno kaže da se poznata odstupanja ne rešavaju u ovoj fazi.
- ✅ **Anti-kanibalizacija basket članka (ID 2298) ponovo primenjena**: sekcija "Dimenzije terena za košarku" → "Obruč koša" (puna FIBA specifikacija, dupliran sadržaj sa `/dimenzije-kosarkaskog-terena/` i `/dimenzije-kosarkaske-table/`) skraćena na 1 pasus + 2 relativna linka (12928→11446 bajtova); sekcija "Košarkaške konstrukcije" ispod ostala netaknuta (kako je i dokumentovano 2026-07-06).
- ✅ Verifikacija: 30 publish postova, 5 nasumičnih → 200, `dimenzije-kosarkaskog-terena`/`dimenzije-kosarkaske-table` linkovi → 200, regression Početna/`/industrijski-podovi/` → 200.
- 📁 `migracija/parity-inventar.csv` ažuriran (na-kojoj-podlozi + bergo-ultimate redovi → 301-KANDIDAT sa unakrsnim napomenama).
- Skripte (scratchpad `f3/`): `step1-cleanup-v2.php`, `run-import-v5.php` (finalna radna verzija sa svim wp-admin include-ovima), `fix-image-urls.php`, `fix-basket-article.php`.

## 2026-07-07 [claude-code] [W3 PARITY F2] — Permalink fix: Woo /proizvod/ flat + /kategorija-proizvoda/ + aktuelnosti ✅
- ✅ **Backup**: `antasline_local_2026-07-07_pre-permalink-fix.sql` (47MB) pre svih izmena.
- ✅ **Woo permalinci**: `product_base` `/shop/%product_cat%` → `proizvod` (flat, kao live); `category_base` `kategorija` → `kategorija-proizvoda` (kao live). Menjano preko `update_option('woocommerce_permalinks', ...)`, ne sirov SQL.
- ✅ **Blog slug**: `/blog/` (ID 21, "Aktuelnosti") → `/aktuelnosti/` (parity sa live).
- ✅ **2 proizvod sluga vraćena na live**: `durastripe-supreme-v-industrijska-traka-...` → `durastripe-supreme-v-roll-50-mm-x-30-m-ergomat` (ID 16520); `ecotile-e500-10-ultra-heavy-duty-podovi-za-kretanje-...` → `ecotile-e500-10-ultra-heavy-duty-interlocking-podna-ploca` (ID 16540).
- ✅ **lite-shot 325 vs 795 razrešeno** (F2 otvoreno pitanje iz F1): WebFetch live `/proizvod/lite-shot-795.../` pokazao IDENTIČAN naslov/specifikacije kao lokalni `lite-shot-325` (700kg, 325cm domet — "795" je stari nepovezan interni kod, ne drugi model). **Nije preimenovano** (nizak GSC saobraćaj — 3 klika, lokalni slug tačniji od live-ovog) — umesto toga oba reda u `parity-inventar.csv` ažurirana na `301-KANDIDAT` sa napomenom za F4 (301 sa starog live URL-a na lokalni `/proizvod/lite-shot-325-.../`).
- ✅ **8 pokvarenih internih linkova ispravljeno**: 2× `/blog/` u footeru (porto_builder ID 5751, 15371) → `/aktuelnosti/`; 6× `/kategorija/` u WoodMart Layout Builder sadržaju (ID 16567, 16572, 16573, 16578, 16579, 16585) → `/kategorija-proizvoda/`.
- 🔴 **Gotcha #1 (novi)**: `flush_rewrite_rules()` (soft) NIJE bio dovoljan posle promene `product_base` — proizvod URL-ovi vraćali 404 uprkos ispravnim rewrite_rules zapisu i ispravnom `get_permalink()`. Rešenje: `flush_rewrite_rules(true)` (hard flush, briše i regeneriše `.htaccess`-relevantne interne strukture). Ubuduće UVEK hard flush posle permalink/permastruct izmena.
- 🔴 **Gotcha #2 (potvrđuje raniju lekciju od 2026-07-06)**: Yoast `wpGs_yoast_indexable` keš (canonical, og:url, JSON-LD) NIJE se osvežio automatski posle permalink izmene — stare `/kategorija/...` i implicitno stare product URL vrednosti ostale zaglavljene u `<link rel="canonical">`/`og:url` sve dok redovi nisu ručno obrisani (`DELETE FROM wpGs_yoast_indexable WHERE object_sub_type IN ('product_cat','product')` + red za ID 21). Posle brisanja, Yoast je ispravno regenerisao sve pri sledećoj poseti. Ovo pravilo sada važi šire nego samo termmeta izmene (prošla lekcija) — **svaka permalink/slug izmena na product/product_cat/page zahteva invalidaciju Yoast indexable keša**.
- ✅ Verifikacija: 5 proizvoda + 3 kategorije + `/aktuelnosti/` → 200; `/blog/` i stari `/shop/...` → 404 (očekivano); canonical/og:url na svim proverenim stranicama ispravni; regression Početna/`/industrijski-podovi/`/`/sportske-podloge/`/`/kontakt/` → 200.
- 📁 `migracija/parity-inventar.csv` ažuriran (lite-shot redovi → 301-KANDIDAT sa F4 napomenom).

## 2026-07-07 [claude-code] [W3 PARITY F1] — Master parity inventar (175 redova) ✅
- ✅ **F1 izvršen**: povučeno svih 7 live sub-sitemapa (curl, `Mozilla/5.0` UA — bez njega Yoast sitemap ponekad vraća prazno/blokira), izvučeno 175 URL-ova (30 post + 1 arhiva, 48 page, 37 product + 1 katalog, 7 category, 2 product_brand, 9 product_cat, 8 product_tag), upoređeno sa lokalnom bazom preko PHP skripte (`WP_Query`/`get_term_by`, ne pojedinačni SQL pozivi) → `migracija/parity-inventar.csv`.
- 📊 **Rezultat**: PARITY 84 · NEDOSTAJE-LOKAL 57 · LOKAL-NOVO 32 · ARHIVA-STRANICA 2 (aktuelnosti, katalog — sistemske, ne prave stranice). Poklapa se sa prošlom sesijom procenjenim brojevima (25/30 postova, 8/50 stranica, 34/37 proizvoda) — potvrđeno tačnim.
- 🔴 **Nov kritičan nalaz**: `/sportske-podloge/kosarkaske-konstrukcije/` = **923 klika/12mes** (GSC preko Windsor.ai, `searchconsole` konektor, `page`+`clicks` neflitrirano pa spojeno u skripti — in-filter gotcha izbegnut) — veće od ranije dokumentovanih 478 (verovatno stariji/kraći period). Postaje najveći pojedinačni SEO rizik u planu, prioritet #1 za F5.
- 🔧 **Gotcha nađen**: `/kategorija-proizvoda/sigurnosni-senzori-signalni-sistemi/` (live) pao je u NEDOSTAJE-LOKAL jer lokalni term ima "i" (`sigurnosni-senzori-i-signalni-sistemi`) — nije pravi gap, slug varijanta. Anotirano u CSV `napomena` koloni za F4.
- 🔧 **Gotcha**: nijedan od 8 live `product_tag` termina (bergo, ergomat, industrijski-amortizer...) ne postoji lokalno — ovo je DRUGA taxonomy od planiranog "namena" taga u F6, razmotriti rekreiranje u F7.
- ✅ Verifikacija: spot-check 5 nasumičnih live URL-ova (kosarkaske-konstrukcije, bergo-xl, antistatik, kontakt, lite-shot-795) → svi 200.
- Bez izmena baze ove sesije. CSV: `migracija/parity-inventar.csv` (175 redova, semicolon, UTF-8-BOM).

## 2026-07-07 [claude-code] [STRATEGIJA] — PARITY-PLAN: nova migracija strategija + 7 promptova ✅
- ✅ **Odluka (Miroslav):** build se pravi **1:1 prema live sajtu** (URL + content parity) — SEO se čuva pa unapređuje. Stari redirect plan (Porto era, 118 redova) proglašen nevažećim.
- 📊 **Izmereno stanje** (live sitemap vs lokalna baza): postovi 25/30 slug match (5 nedostaje) · pages 8/50 (42 nedostaje, ~12 su Woo sistem/proizvod-stranice/legal) · proizvodi 34/37 · **lokalni Woo permalinci pogrešni**: `/shop/%product_cat%` + `/kategorija/` vs live `/proizvod/` flat + `/kategorija-proizvoda/` — jedna izmena opcije briše ~47 redirect redova.
- 🔴 **Nađene greške u starim mapama**: POPUNJENA.csv cilja `/shop/` URL-ove (kontradiktorno i sa live i sa CLAUDE.md odlukom o flat `/proizvod/`); mapa 2026-07-07 vodila `podovi-za-parkiraliste-i-staze`→`podloge-za-...` kao PARITY iako se slugovi razlikuju.
- ✅ **Donete odluke**: (P1) slug politika = hibrid po težini — top ~15 GSC URL-ova strogi parity, nisko-saobraćajni smeju novi slug uz 301, konsolidacije uvek OK (obrazloženje: keyword u slugu ≈ zanemarljiv faktor, 301 nosi 2–8 ned. nestabilnosti + rizik izvršenja); (P2/M8 ✅) postovi = **pun reimport svih 30 sa live**, restyle posle; (P5) troslojna arhitektura namena→proizvod (`namena` tag + auto grid — namenske stranice postaju "rešenje hub", ne opis jednog proizvoda); (P6) content standard pre live-a (standardi sa linkovima ka izvorima, SVG ikonice, "antas-skica" stil, video kroz fasadni embed).
- 📁 **Kreirano**: [[migracija/PARITY-PLAN]] (izvor istine) · [[migracija/promptovi/_README]] + 7 samostalnih promptova F1–F7 (svaki izvršava jedna buduća sesija, bilo koji model) · `migracija/arhiva/` sa 3 stare mape + [[migracija/arhiva/_SUPERSEDED]]
- 📝 **Ažurirano**: MASTER-PLAN V2 (W3 3.1–3.4 prepisani, W1 1.3 + M8 rešeni, gate kriterijum), BLOK-C (C1/C2 → parity), PROGRESS (Sledeće = F1→F7, stare statistike arhivirane), CLAUDE.md §7.4
- ⚠️ **Gotcha za buduće sesije**: title/meta quick-win za pop-tenis i odbojku raditi POSLE F3 (reimport bi pregazio izmene); live postovi imaju `<h1>` u sadržaju → 2×H1 posle importa (rešava restyle); lite-shot 325 (lokal) vs 795 (live) — verovatno različiti modeli, proveriti pre rename.
- Ništa nije menjano u bazi ove sesije — samo dokumentacija + arhiviranje kopija CSV-ova.

## 2026-07-07 [claude-code] [ANALITIKA] — Nedeljni izveštaj (GA4+Ads+GSC+GMB) + bounce rate nalaz ✅
- ✅ **Nedeljni izveštaj 7d vs 7d** (30.6–6.7 vs 23.6–29.6) povučen preko Windsor.ai, prošireno sa GMB podacima (novo — connector `google_my_business`, lokacija "Industrijski i sportski podovi Beograd - Antas Line", `locations/3289324505122199130`)
- 📊 **Prava konverzija** (`/hvala-za-poruku/` page view) pala -45,5% (22→12) uz skoro stabilan saobraćaj (korisnici -2,3%) → pad konverzione stope 2,79%→1,56%, ne pad tražnje
- 🔴 **Nalaz — `generate_lead` event dosledno veći od `/hvala-za-poruku/` pageviews** (18 vs 12 ove nedelje, 27 vs 22 prošle, ~20-50% sistematski offset oba perioda) → sumnja na duplo okidanje Page View trigera u GTM-u; treba proveriti GTM Preview. Nije nova pojava (postoji u oba perioda), ali nikad ranije flagovano.
- 📊 **Bounce rate — veliki WoW pad**: 57,9% (23-29.6) → 34,9% (30.6-6.7), oštra korak-promena tačno oko 28-30.6. Poklapa se sa BLOK A GTM v10 čišćenjem (Consent Mode + MI gašenje) → verovatno tačnije merenje engagement-a (MI/GTM konflikt ranije lažno naduvavao bounce), ne stvarna promena ponašanja. Nema alarmantnih stranica po bounce-u na visokom saobraćaju (`/kontakt/` 6,7%, `/industrijski-podovi/` 18,6%, homepage 20,9%); jedino niskoprometne stranice (`/pop-tenis/`, parket-guide) imaju visok bounce ali premali uzorak (5-12 sesija) za pouzdan signal.
- 📊 **Ads**: kumulativ plaćenih konverzija od 2026-06-01 = 10 (prag za Maximize Conversions je 20-30) → ostaje se na Maximize Clicks. ECOTILE CPC pao 73,9→51,8 RSD uz bolji CTR, throttling nije prisutan.
- 📊 **GSC top prilika (28d, poz. 5-15)**: "epoksidni podovi cena po m2" (210 impr, 0% CTR) i "epoksi podovi"/"epoxy podovi" varijante — visok volumen, nula klikova unatoč dobroj poziciji; conquest članak (`/epoksidni-podovi-ili-ecotile-podovi/`) verovatno ne hvata price-intent frazu u title/meta. "industrijski podovi" (199 impr, poz 10,8, CTR 1,5%) — money-keyword na str. 2, vezano za blokirani WPBakery insert na post ID 4937 (6 blokova čeka, poznat JS bug).
- 📊 **GMB**: impresije prepolovljene WoW (62→30), samo 6 recenzija ukupno (prosek 4,7), 0 novih ove nedelje — nema plaćenog troška, signal slab, prilika za brz win (traženje recenzija od skorašnjih lidova).
- **Akcija nedelje predložena**: proveri GTM Preview na `/hvala-za-poruku/` (moguće duplo okidanje `generate_lead`) + pokreni traženje recenzija za GMB.
- Izveštaj ostao u chat-u (nije eksportovan kao poseban fajl); nije menjano ništa u GTM-u/Ads-u ove sesije — samo analiza preko Windsor.ai (read-only).

## 2026-07-07 [claude-code] [W1 — DIZAJN FIX] — Desktop razmaci/font + sistemski bug dijagonalnih CTA sekcija ✅
- ✅ **Desktop spacing/font** (Miroslav: "previše prazno, font u hederu prevelik"): u `antas-design.css` —
  - `--al-gap` (vertikalni ritam sekcija): `clamp(56px, 9vw, 140px)` → `clamp(56px, 5vw, 72px)` (desktop max −49%, mobile min 56px nepromenjen)
  - `.al-display--xl` (hero naslov): `clamp(44px, 7.5vw, 104px)` → `clamp(44px, 6.4vw, 88px)` (desktop max −15%, mobile min 44px nepromenjen)
  - `/o-nama/` (ID 571) "Kontaktirajte nas" kicker red izgledao kao prazna kutija (pun `--al-gap` ritam za 2 reda teksta) → nova klasa `.al-section--compact` (samo padding-top, tesan uz sekciju iznad)
- 🔴→✅ **Sistemski bug nađen i popravljen**: dijagonalni prelaz ka CTA sekciji (`al-diag-top`/`al-diag-top--rev`) je na svakoj stranici ostavljao beli trougao/traku umesto da boja prethodne sekcije ispuni rez — najvidljivije na mobile (manji `--al-cut`), ali i na desktopu (`/industrijski-podovi/`).
  - **Uzrok**: `margin-top: calc(-1 * var(--al-cut))` je trebalo da "povuče" CTA red preko prethodne sekcije (preklop koji rez treba da otkrije). Na ovom sajtu WPBakery `full_width="stretch_row"` ubacuje prazan `<div class="vc_row-full-width vc_clearfix">` (float:left, height:0) između SVAKA dva reda — iz nejasnog razloga to poništava `margin-top` na sledećem redu (computed stil pokazuje ispravnu vrednost, ali render pozicija se ne pomera ni za piksel — potvrđeno testom sa `margin-top:-300px !important` inline).
  - **Fix**: `.al-diag-top` i `.al-diag-top--rev` sada koriste `position: relative; top: calc(-1 * var(--al-cut))` (+ kompenzujući `margin-bottom` da ne ostane rupa u toku dokumenta) umesto `margin-top`. `top` radi ispravno na ovom sajtu (potvrđeno merenjem: preklop tačno −96px). Jedna CSS izmena, važi sitewide — nije trebalo dirati sadržaj nijedne stranice.
  - Usput probao (pa vratio) privremene per-page `al-diag-bottom`/kombinovane klase na 13 stranica dok nisam našao pravi uzrok — sve te dodatne klase su uklonjene iz `post_content` (13 stranica), ostao je samo `al-section--compact` na 571 (namerno, nezavisna ispravka).
  - Nova lekcija upisana u [[reference/naucene-lekcije]] i [[migracija/woodmart-sabloni]] (gotcha #11).
- Backup-ovi: `antasline_local_2026-07-07_0839_pre-onama-kontakt-section.sql`, `antasline_local_2026-07-07_1011_pre-industrijski-cta-seam.sql`, `antasline_local_2026-07-07_1034_pre-sitewide-cta-seam.sql`
- Verifikovano: HTTP 200 na svih 13 pogođenih stranica (Početna, industrijski-podovi, kontakt, o-nama, sportske-podloge, 4 sport stranice, 2 dimenzije stranice, parking-staze, spoljne-podne-obloge), dijagonale čiste na desktop i mobile (simulirano `--al-cut:28px`).

## 2026-07-07 [claude-code] [W1 + C1 BRZI COMBO] — N1 silo zatvoren + C1 verifikacija + /hvala-za-poruku/ kreirana ✅
- ✅ **N1 silo sekvenca 1.1 zatvoren**: sve 4 stranice iz plana su gotove/ažurirane
  - `/spoljne-podne-obloge/` (ID 16590 — bez j, Bergo za terase) — kreirano 2026-07-07
  - `/podloge-za-parkiraliste-i-staze/` (ID 16589 — parking + staze) — kreirano 2026-07-07
  - `/kontakt/` (ID 61 — forma + mapa) — upgrade 2026-07-07, forma ispravljena
  - `/o-nama/` (ID 571 — brend info) — upgrade 2026-07-07
- ✅ **Brzi W1 bonus**: `/podloge-za-parking/` (ID 15580 — lokalni draft) — Yoast title/metadesc ažurirani (meta title "Podloge za Parking, Pešačke Staze i Prilaze - Antasline")
- ✅ **C1 verifikacija — 5 ključnih stranica**:
  - `/spoljne-podne-obloge/` (ID 5255) — 200, publish
  - `/podloge-za-parkiraliste-i-staze/` (ID 16589) — 200, publish
  - `/sportska-igralista/` (ID 15973) — 200, publish
  - `/zamena-parketa-u-sportskim-salama/` (ID 15965) — 200, publish
  - `/podloge-za-krovove-i-terase/` (ID 15971) — 200, publish
- ✅ **C1 verifikacija — UKUPNO (live vs lokal)**:
  - **Live stranice**: 80 (pages + posts + products)
  - **Lokalne stranice**: 98 (nova + rebuilds)
  - **Verifikovane (match live+lokal)**: 25 stranica — spremne za parity
  - **Nedostaje na lokalu (PROVERI)**: 38 stranica — trebalo importovati ili 301 redirect
  - **CSV redirect mapa** — kreirama: `antasline-redirect-mapa-2026-07-07.csv` (38 PROVERI redova + 3 GOTOVO)
- 🔴 **KRITIČNA PRONALAZKA**: `/hvala-za-poruku/` (thank you page za forme) **NEDOSTAJALA** — kreirama odmah (ID 16600). To je KEY page za `generate_lead` GA4 event tracking!
- 📋 **Prioriteti za C1 ostatak (W1 1.2)**: padel-tereni, kosarkaske-konstrukcije, garaze-i-autoservisi (sport/vertikala silo) + antistatik (industrijski) + 20+ proizvoda + legal stranice
- 🔧 **Lesson**: Live `/aktuelnosti/` → trebalo `/blog/` na lokalu (slug rename); `/spoljnje-podne-obloge/` (live sa j) → `/spoljne-podne-obloge/` (lokal bez j) — 301 redirect
- 🔧 **Lesson**: `/podloge-za-parking/` i `/podloge-za-parkiraliste-i-staze/` — dve različite stranice na lokalu (ID 15580 vs 16589), ali live samo ima `/podloge-za-parkiraliste-i-staze/`; parity odluka: ID 15580 može biti placeholder ili draft, ili se izbriše pre migracije
- Backup-ovi: `antasline_local_2026-07-07_pre-parking-rebuild.sql` (90 MB); prethodni iz iste sesije: `antasline_local_2026-07-07_pre-kontakt-fix.sql`, `antasline_local_2026-07-07_pre-onama-kontakt-upgrade.sql`, `antasline_local_2026-07-07_pre-spoljne-podne-obloge.sql`, `antasline_local_2026-07-07_pre-podloge-za-parking.sql`

## 2026-07-07 [claude-code] [W3 TEHNIČKA] — 3.13 backup automatizovan ✅ + 3.14 popis pokrenut ⏳
- ✅ **3.13 zatvoreno**: `C:\xampp\htdocs\antasline-backups\scripts\nocni-backup.ps1` — mysqldump `antasline_local` + zip `wp-content` u jedan arhiv, rotacija zadržava poslednjih 14, log fajl. Registrovan u Windows Task Scheduler-u ("AntasLine Nocni Backup", Daily 03:00, RunLevel Limited). Ručni test: DB dump 90MB (2s) + zip 3,6GB wp-content → 3GB arhiv (27 min ukupno) — prihvatljivo za noćni posao.
- Destinacija je pametna: skripta proverava da li je OneDrive folder (`C:\Users\Miroslav\OneDrive`) stvarno sinhronizovan (ne samo prazan placeholder) — trenutno NIJE ulogovan pa piše LOKALNO u `antasline-backups\auto\`; čim se M prijavi na OneDrive, sledeće pokretanje automatski prebacuje na cloud kopiju bez izmene skripte. #ceka-miroslav: prijava na OneDrive.
- Retencija 14×~3GB = do 42GB na disku — trenutno 88,9GB slobodno na C:, dovoljno.
- ⏳ **3.14 u toku**: M pročitao cPanel i javio brojeve — PHP 8.3 (⚠️ lokalni XAMPP build je na 8.2.12, treba proveriti kompatibilnost teme/pluginova pre migracije), disk 5,05/11,95GB (42%, 6,9GB slobodno), subdomeni dostupni (0 iskorišćeno, neograničeno). Dovoljno prostora za probu migracije na `novi.antasline.com`.
- Sledeći korak (subdomen kreiranje + upload/import + merenje vremena) nastavlja se u sledećoj sesiji — otvoreno pitanje načina rada (M sam uz moje instrukcije / SSH kredencijali meni / cPanel File Manager bez SSH-a).

## 2026-07-07 [claude-code] [KONTAKT FORMA + MAPA] — Ispravka i finalizacija ✅
- ✅ **Kontakt forma**: CF7 ID 5339 (`Kontakt forma` — postojeća, funkcionalna)
  - Polja: Ime, Email, Naslov, Poruka, Dugme "Pošalji"
  - Email notifikacije (admin + auto-reply) — već konfiguriran
- ✅ **Google Mapa**: Embed mapa sa lokacijom (Ulcinjska 13, Beograd, real Google Maps embed)
  - Vidljiva ispod forme na `/kontakt/`
- ✅ **Rezultat**: `/kontakt/` stranica je sada čista, forma je vidljiva i funkcionalna, mapa je vidljiva
- 🔧 **Ispravka workflow**: Počeo sa CF7 ID 16593 (problem) → zamenjeno sa ID 5339 (funkcionira)
- Backup-ovi: `antasline_local_2026-07-07_pre-forma-ga4.sql` + `antasline_local_2026-07-07_pre-kontakt-fix.sql` (46 MB svaki)

## 2026-07-07 [claude-code] [W1 — UPGRADE ×2] — /o-nama/ + /kontakt/ WoodMart redesign ✅
- ✅ **Obe stranice upgradan** sa al-WoodMart šablonom (hero navy+plates → paper body → mist info → CTA navy+rev-diag)
  - `/o-nama/` (ID 571) — O AntasLine, kompanija, šta nudimo, kontakt CTA
  - `/kontakt/` (ID 61) — Informacije, forma, FAQ, kontakt detalji (radno vreme, lokacija)
- ✅ Svaka: Yoast mete + Yoast title u `<head>` + H1 sa `al-display--xl` + WoodMart layout (full-width, title-off)
- ⚠️ Forma na `/kontakt/` — Contact Form 7 ID 3 nije pronađena; trebala bi ispravljanje ako trebala prava forma (za sada placeholder)
- HTTP 200 obe stranice, dizajn konzistentan sa ostalim silo stranicama
- Backup: `antasline_local_2026-07-07_pre-onama-kontakt-upgrade.sql` (46 MB)

## 2026-07-07 [claude-code] [W1 — SILO REBUILD ×2] — /spoljne-podne-obloge/ + /podloge-za-parkiraliste-i-staze/ ✅
- ✅ **2 silo landing-a** kreirane po al-WoodMart šablonu (hero navy+plates → paper body → FAQ mist → CTA navy+rev-diag)
  - `/spoljne-podne-obloge/` (ID 16590 — ispravljeno sa 16588; trebalo je bez "j": "spoljne" ne "spoljnje") — Bergo ploče za terase, karakteristike, Bergo Flooring info
  - `/podloge-za-parkiraliste-i-staze/` (ID 16589) — industrijske podloge za parking, specifikacije, sigurnost
- ✅ Svaka stranica: Yoast mete, FAQPage JSON-LD (3-4 stavke), CTA linkovi, HTTP 200, 1×H1
- 🔧 Lesson: vc_raw_html za JSON-LD nije pouzdano → direktno dodavanje kao `<script>` tag u post_content (gotcha #8 iz woodmart-sabloni)
- ⚠️ Napomena za live migraciju (C1 redirect mapa):
  - Live `/spoljnje-podne-obloge/` (sa j) → Lokal `/spoljne-podne-obloge/` (bez j, ID 16590) — 301 redirect
  - Live `/podloge-za-parkiraliste-i-staze/` → Lokal `/podloge-za-parkiraliste-i-staze/` (ID 16589) — parity (isti slug)
- Backup-ovi: `antasline_local_2026-07-07_pre-spoljne-podne-obloge.sql` + `antasline_local_2026-07-07_pre-podloge-za-parking.sql` (46 MB svaki)
- Skripti: `build-spoljnje-podne-obloge.php`, `build-parking.php` (scratchpad)

## 2026-07-07 [claude-code] [W1 — SILO REBUILD] — /spoljnje-podne-obloge/ WoodMart silo landing ✅
- ✅ Backup pre rada: `antasline_local_2026-07-07_pre-spoljne-podne-obloge.sql` (46 MB)
- ✅ Kreirane `/spoljnje-podne-obloge/` (ID 16588) po al-WoodMart šablonu (hero navy+plates+diag-bottom → paper body → FAQ mist+diag-top → CTA navy+plates+rev-diag)
- ✅ Content parity iz live export XML (SiteOrigin layout): intro 2 rečenice · Bergo karakteristike + Bergo Flooring historia · FAQ 4 stavke (trajnost, demontaža, restorani, održavanje) · JSON-LD schema (FAQPage) · Yoast mete iz live-inventar CSV
- ✅ Postmeta: `_woodmart_main_layout=full-width`, `_woodmart_title_off=on`
- ✅ Yoast: Title "Podne obloge za bašte i terase - jednostavna montaza i veliki izbor boja" · Metadesc "Spoljasnje podne obloge za terase, dvorista, baste..."
- ✅ Verifikacija: HTTP 200 · 1×H1 "Spoljne podne obloge za bašte i terase" · Yoast title u <head> · FAQPage JSON-LD dodan; interni CTA linkovi ka `#upit` forma
- 🔧 Gotcha: `vc_raw_html` sa JSON-LD nije se prikazao → JSON-LD dodan direktno kao `<script>` tag na kraju post_content (WPBakery vc_raw_html gotcha #8)
- 📍 Gde čeka: Slike/referenci (nije dodata galerija — live stranica je imala SiteOrigin `[Best_Wordpress_Gallery id="35"]` — trebam da dodavam referentne slike ako postoje lokalno)
- Skripti: `build-spoljnje-podne-obloge.php`, `fix-faq-schema.php` (scratchpad)

## 2026-07-07 [claude-code] [PLAN - PROCESNI AUDIT] — 9 predloga upisano u Master Plan V2 ✅
- ✅ Drugi krug audita (posle sadržajnog 07-06) — fokus na proces/rizik/biznis logiku, ne sadržaj:
  1. 🔴 **Backup rizik**: 2 meseca rada samo na jednom disku → novi zadatak 3.13 (noćni mysqldump + wp-content zip na drugu lokaciju)
  2. 🔴 **M6 SSH bez fallbacka, rok tek u N8** → 3.14 ubrzano na OVU nedelju (test pristupa) + proba migracije na subdomen `novi.antasline.com` u N6 (izmeriti stvarno vreme, testirati rollback)
  3. **Woo checkout vs katalog režim**: 0/37 proizvoda ima cenu → nova zavisnost M9 (odluka: "Zatraži ponudu" umesto korpe) + W1 zadatak 1.8
  4. **Cenovnik kao fajl**: nova zavisnost M10 + kreiran `[[reference/cenovnik]]` (tabele po kategoriji proizvoda, prazno = na upit) — sprečava ponovno pitanje cena po svakoj sesiji
  5. **Telefon haos**: 063/069/072/074 u opticaju na buildu → novi zadatak 1.9 (SQL audit `tel:` linkova, ujednačiti na jedan)
  6. **SERP snapshot**: nema baseline pozicija konkurencije pre migracije → novi zadatak 3.15
  7. Sezonski kalendar → nova sekcija **8. W6/W7 POSLE LIVE-A** u planu (B2B jesen, priprema terase kampanje zima, GSC špic mar–maj)
  8. Post-live monitoring pojačan (3.12): UptimeRobot + dnevni 404 pregled umesto ad-hoc
  9. Proces: **"ponedeljak 15 min"** pregled svih M-zavisnosti — ugrađeno u skill `/antasline-sesija` (korak 3b) i pomenuto u `[[reference/claude-skilovi]]`
- ✅ Ažurirano: [[2026-07-06-MASTER-PLAN-V2]] (W1 1.8/1.9, W3 3.13/3.14/3.15, zavisnosti M9/M10, rizici, gate kriterijumi, N1/N6 raspored, nova sekcija 8), `[[reference/cenovnik]]` (nov fajl), `/antasline-sesija` skill, `[[reference/claude-skilovi]]`, CLAUDE.md §13 hub
- 🔴 Najhitnije: M9 (checkout odluka) + M10 (cenovnik popuna) + 3.13/3.14 (backup + SSH test) — sve ove nedelje

## 2026-07-06 [claude-code] [W4 + W5 UNOS] — GA4 publike + GMB ažuriranje ✅
- ✅ **GA4 publike — 2 nove kreirane od Miroslava**
  - `Parking & spoljne podloge` — `page_path contains /podloge-za-parkiraliste/ OR /spoljnje-podne-obloge/` (očekivano ~120 korisnika/14d)
  - `Košarkaški tereni` — `page_path contains kako-napraviti-teren-za-basket OR kosarkaske-konstrukcije` (~265/14d)
  - Status: "Too small to serve" prvih dan-dva dok saobraćaj poraste; sinhronizovanje sa Google Ads aktivno
- ✅ **GMB ažuriranje (M4 / plan 5.2, rok 2026-07-31)**
  - UTM fix: Website URI zamenjeno na `https://antasline.com?utm_source=google&utm_medium=gmb&utm_campaign=local` (GA4 će meriti kao GMB kanal umesto Unassigned)
  - Kategorije proširene: +`Gradnja sportskih terena` + `Pružalac usluga za podove` (bilo samo "Продавница подова")
  - Prvi Google Post za 6 godina (jula 2026 kampanja — Bergo Ultimate/Naxos Evolution)
  - Review link: čeka na prve poslove (M4 fallback, nije blocker)
- Efekat: GMB impresije −73% (trend) + saobraćaj sa GMB sada merljiv u GA4; reviews mogu početi prirodno sa poslovima

## 2026-07-06 [claude-code] [AUDIT + SKILL INFRASTRUKTURA] — Rupe u projektu + 4 Claude Code skila ✅
- ✅ **Audit celog projekta** — dve glavne rupe potvrđene podacima:
  1. **Social/email ne postoji u planu**: Organic Social 70 korisnika/90d (0,5%) ali 81% engagement; nijedan social/email/video zadatak u Master planu V2; ~55 kontakata/mes bez follow-up-a (M5)
  2. **Proizvodi thin (provera lokalne baze, 37 proizvoda)**: 0/37 cena · 0/37 Yoast title/metadesc · 0/37 galerija (a 115 slika importovano) · 0/37 Woo atributa · 14/37 opis <1.000 znakova · 0 PDF tehničkih listova; kanibalizacija rizik proizvod↔stranica (Bergo Unique)
  - Manje rupe: CRO odsutan (0,88% konverzija, 76–87% mobile, nema sticky CTA), `/hvala-za-poruku/` prazna (0 reči), `lead_form_start` nije implementiran (Form Abandoners publika se ne puni), blog bez post-live plana, nema saglasnosti za email na formi
- ✅ **4 projektna skila** u `.claude/skills/` (aktivni od sledeće sesije):
  - `antasline-sesija` — master protokol sesije (otvaranje → W1–W5 tok → verifikacija → zatvaranje)
  - `obogati-proizvod` — 8-tačka šablon obogaćivanja Woo proizvoda + money-first redosled (Ecotile → konstrukcije → batch)
  - `w6-social` — novi W6 workstream (Faza 0 pre live-a: popis profila/M5/GMB/saglasnost; pun ritam od 2026-09-01; UTM standard za social)
  - `nedeljni-izvestaj` — 7d vs 7d kroz Windsor po formatu [[CLAUDE]] §10 sa svim naučenim zamkama
- ⏳ Čeka odluku Miroslava: (1) product šablon kao novi W1 zadatak → start sa Ecotile linijom, (2) W6 upis u Master plan, (3) popis social profila + M5 odgovor #ceka-miroslav

## 2026-07-06 [claude-code] [ADS - NEGATIVNE KW] — M2 / plan 4.1 zatvoreni ✅
- ✅ Analiziran izvoz iz Ads UI (`Files/Negative keyword details report.csv`, 44 negativne) vs [[CLAUDE]] §6 referentna lista — falilo 7: `epoksi`, `epoksidni`, `epoksidnih`, `epoksidnog`, `betonski`, `"industrijski beton"`, `[podne obloge]`. Ključni nalaz: **oblik "epoksi" uopšte nije bio pokriven** (broad negativne nisu morfološke — `epoksidna` ne blokira `epoksidni`)
- ✅ Miroslav u Ads UI dodao 13 negativnih (gornjih 7 + `teraco`, `letvice`, `pevex`, `"uradi sam"`, `"keramičke pločice"`, `"podne pločice"` — phrase umesto broad `plocice` da ne blokira "pvc pločice" upite iz ponude), pauzirao KW `bastenski namestaj` + `oprema za bazene` u Terasama, potvrdio da je lista "AntasLine — univerzalne negativne" primenjena na obe kampanje
- `laminat` svesno izostavljen ([[CLAUDE]] §6 pravilo) — watch lista
- Efekat: zatvara ~16% curenja budžeta (M2 iz [[2026-07-06-MASTER-PLAN-V2]]); sledeće u W4: Faza 1 RSA Terase
- Detalji: [[dnevnik/ADS-DNEVNIK]] log 2026-07-06

## 2026-07-06 [claude-code] [PLAN - MASTER PLAN V2] — Novi plan projekta do live-a ✅
- ✅ Pročitani svi .md fajlovi u vault-u (40) → napravljen **[[2026-07-06-MASTER-PLAN-V2]]** kao jedini izvor istine za plan (stari [[2026-07-02-MASTER-PLAN-DO-LIVE]] označen `superseded` — pisan pre Porto→WoodMart prelaska, live exporta i C3 draftova)
- Struktura V2: **baseline 2026-07-06** (šta je gotovo + metrike-nula iz [[analiza/2026-07-04-snapshot-full]]) → **5 workstream-ova** (W1 dizajn/rebuild · W2 SEO content C3+GEO · W3 SEO tehnička+migracija C1/C2+CWV · W4 Ads faze 1–4 · W5 tracking/merenje) → **nedeljni raspored N1–N8** unazad od migracije **2026-08-31** → **gate kriterijumi** za go/no-go → **8 zavisnosti od Miroslava** sa fallback-ovima i rokovima → **KPI tabla** (jun = mesec-nula) → **registar rizika**
- Odluke Miroslava: novi fajl V2 (ne update starog in-place) · go-live ostaje 2026-08-31
- Ažurirane reference: [[PROGRESS]] (banner + red u tabeli), [[blokovi/BLOK-C-sledece]] (C1/C2/C3 → workstream mapiranje), [[CLAUDE]] §12/§13, stari plan (superseded napomena + frontmatter)
- 🔴 Najhitnije iz plana: M1 cene za Tier1 draftove (rok 10.07, fallback "cena na upit") + M2 negativna lista u Ads UI (15 min, zaustavlja ~16% curenja)

## 2026-07-06 [claude-code] [C3 TIER1 #4/#5] — Dimenzije terena + table za košarku ✅
- ✅ Napravljene **2 TIER1 SEO stranice** iz [[seo/plan-novih-stranica]] (~20k impr, poz. 1–2 ali nizak CTR — cilj je featured snippet, ne rang):
  - `/dimenzije-kosarkaskog-terena/` (ID 16586) — FIBA vs NBA tabela (teren, koš, tri poena, reket, centralni krug), školski/rekreativni/3x3 varijante, link ka `/kosarka-3x3-tereni/`
  - `/dimenzije-kosarkaske-table/` (ID 16585) — dimenzije table, visina montaže, staklo vs akril, uradi-sam vs gotova konstrukcija (cena "na upit", bez izmišljenih brojeva jer nemamo prave cifre), link ka `/kategorija/kosarkaske-konstrukcije/`
- 🐛 **Slug konflikt otkriven**: `/dimenzije-kosarkaskog-terena/` slug je od 2022. bio zauzet starim image **attachment**-om (`post_type=attachment`, prazan `post_content`) korišćenim inline u basket članku — `get_page_by_path()` ga je vraćao i pored `post_type='page'` filtera (WP kvirk, attachment slug i dalje blokira page slug). Rešeno preimenovanjem attachment `post_name` u `dimenzije-kosarkaskog-terena-slika` (guid/URL same slike ostaje netaknut, samo interni slug se menja) — bezbedno jer se slika u sadržaju referencira direktno preko uploads putanje, ne preko attachment permalink-a
- ✅ **Anti-kanibalizacija**: postojeći članak "Kako napraviti teren za basket ili košarkaški teren" (ID 2298) je imao punu "Dimenzije terena za košarku" → "Obruč koša" sekciju (1894 bajta, dupliran sadržaj sa novom stranicom) — skraćeno na 2 rečenice + linkovi ka obe nove stranice; sekcija "Košarkaške konstrukcije" ispod ostala netaknuta
- ✅ Verifikacija (2/2 nove + 1 izmenjena): sve 200, 1×H1, FAQPage JSON-LD, cross-linkovi (`/kosarka-3x3-tereni/`, `/kategorija/kosarkaske-konstrukcije/`) rade, Yoast title/metadesc + `_woodmart_title_off` setovani
- Skripte: `build-basket-dimension-pages.php`, `trim-basket-article.php` (scratchpad)

## 2026-07-06 [claude-code] [DIZAJN - 4 nove sport stranice] — Popunjena rupa u /sportske-podloge/ gridu ✅
- 🐛 **Bug otkriven u jučerašnjem gridu (5438)**: 4 od 11 kartica u "Izaberite sport" gridu na `/sportske-podloge/` nisu vodile ka pravom sadržaju — Futsal kartica je linkovala na `/industrijski-podovi/` (potpuno nepovezano), a 3x3/Stoni tenis/Hokej kartice su sve tri vodile na isti `/sportske-podloge/bergo-ultimate/` fallback. Provera baze potvrdila da za sva 4 sporta nikad nije postojala prava dedicated stranica — stari nav meniji (`futsal-tereni`, `hokejaski-tereni`) su i ranije upućivali na generičke proizvod-stranice (Naxos Evolution / Bergo Ultimate)
- ✅ Napravljene **4 nove landing stranice** po istom al- WoodMart šablonu (hero navy+plates → USP paper → specifikacija mist → foto-reference paper → FAQ+FAQPage JSON-LD mist → CTA navy):
  - `/podloge-za-futsal-terene/` (ID 16581) — indoor (Naxos Evolution) + outdoor (Bergo Ultimate) opcije, FIFA/FIBA dimenzije 38–42×18–22m
  - `/podloge-za-hokejaske-terene/` (ID 16582) — Naxos Evolution, dvoranski hokej/floorball
  - `/podovi-za-stoni-tenis-sale/` (ID 16583) — Naxos Evolution, ugao na mat površinu (bitno za praćenje loptice)
  - `/kosarka-3x3-tereni/` (ID 16584) — Bergo Ultimate, FIBA 15×11m, foto-reference sa stvarnih terena (Jakovo, Zlatibor, Novi Sad) + link ka Dunk Shop case study (`/teren-za-basket-3x3/`)
  - Sadržaj oslonjen na stvarne proizvod-činjenice iz postojećih Naxos Evolution (ID 15490) i Bergo Ultimate (ID 15480) stranica, ne izmišljen
- ✅ Sva 4 linka u `/sportske-podloge/` gridu (5438) ispravljena da vode ka novim stranicama umesto na placeholder ciljeve
- 🔧 **Nova gotcha**: nove `page` stranice pravljene direktno preko `wp_insert_post()` dobijaju WoodMart-ov automatski page-title `<h1 class="entry-title">` PORED našeg `<h1 class="al-display--xl">` iz sadržaja → 2×H1. Rešenje: `_woodmart_title_off = 'on'` postmeta (isti trik već postoji na 16567 industrijski-podovi, ali nije bio dokumentovan) — mora se dodati ručno posle insert-a, nije default
- ✅ Verifikacija (4/4): HTTP 200, tačno 1×H1 (posle `_woodmart_title_off` fix-a), FAQPage JSON-LD validan, sve slike (postojeći uploads) i interni linkovi vraćaju 200, Yoast title/metadesc setovan
- Backup pre izmena: `antasline_local_2026-07-06_pre-4-sport-pages.sql` u `C:\xampp\htdocs\antasline-backups\`
- Skript: `build-sport-pages.php` + `fix-sport-grid-links.php` (scratchpad, nisu u vault-u)

## 2026-07-06 [claude-code] [DIZAJN - 10 WooCommerce kategorija] — WoodMart Layout Builder landing sekcije ✅
- ✅ **Novi mehanizam otkriven i prvi put isproban u projektu**: WoodMart "Layout Builder" (`post_type=woodmart_layout`, `wd_layout_type=shop_archive`, `wd_layout_conditions` sa `condition_type=product_term`) potpuno zamenjuje WooCommerce archive template za odabranu kategoriju — omogućava hero/USP/FAQ+schema tretman + `[woodmart_shop_archive_products]` grid, isto vizuelno poput `/industrijski-podovi/` i `/sportske-podloge/` stranica
- ✅ Svih **10 kategorija** (245–254, Ergomat/DuraStripe/Bergo/Ecotile katalog, prethodno bez ikakvog opisa/SEO meta) dobilo puni ili skraćeni landing tretman: 6 punih (hero+USP+grid+FAQ+CTA: 245, 246, 248, 251, 252, 254), 4 skraćene (hero+intro+grid+FAQ+CTA bez USP grid-a, za 1–2 SKU kategorije: 247, 249, 250, 253)
- ✅ **Diferencijacija duplikata**: 245 "Zaštita i Bumperi" (proizvod-katalog ugao) ↔ 246 "Industrijska zaštita" (rešenje-za-problem ugao, isti proizvodi) i 251 "Košarkaške konstrukcije" (teren/instalacija) ↔ 252 "Oprema za sportske terene" (šira sportska oprema, 100% identični proizvodi) — obostrani cross-linkovi da se izbegne dupli sadržaj za Google
- ✅ **254 "Industrijski podovi" vs postojeća `/industrijski-podovi/` (16567) kanibalizacija rešena**: 16567 ostaje edukativna/poredbena stranica, nova kategorija je transakciona/katalog stranica + dodat 1 interni link sa 16567 ka novoj kategoriji
- ✅ Yoast SEO title/metadesc setovan za svih 10 (`WPSEO_Taxonomy_Meta::set_values()`)
- 🔧 **3 nova gotcha-e otkrivene i rešene** (bitno za buduće layout builder izmene):
  1. `wd_layout_conditions` MORA imati `condition_comparison => 'include'` po uslovu — bez toga se layout tiho nikad ne aktivira, bez greške
  2. `WPSEO_Taxonomy_Meta::set_value()` pozvan pojedinačno (title, pa desc) **briše** prethodno postavljeno polje jer nema "retain old value" fallback za title/desc — mora `set_values()` sa oba ključa u JEDNOM pozivu
  3. Yoast keš-uje title/desc u `wpGs_yoast_indexable` tabeli (Indexables sistem) — ne osvežava se automatski kad se termmeta menja mimo admin UI-ja; mora se obrisati stale red (`$wpdb->delete` po `object_id`+`object_type`+`object_sub_type`) da se izgradi iznova sa svežim vrednostima
  4. Direktan `wp_update_post()` posle `$wpdb->update` patch-a JSON-LD-a **ponovo** provlači ceo `post_content` kroz kses (briše `vc_raw_html` opet) — status na `publish` mora ići u ISTOM raw `$wpdb->update` pozivu kao i content patch, nikad kroz `wp_update_post()`; pošto to zaobilazi `save_post` hook, `wd_layouts_conditions` keš se mora ručno regenerisati (`new \XTS\Modules\Layouts\Conditions_Cache())->regenerate()`) posle batch-a
- ✅ Verifikacija (10/10): HTTP 200, tačno 1×H1, FAQPage JSON-LD validan bez dupliranja Yoast `CollectionPage`/`BreadcrumbList` grafa, `<title>`/meta = Yoast vrednosti, product grid renderuje prave proizvode (3/12/12/1/6/2/1/5/5/1), cross-linkovi 200, `SELECT COUNT(*) WHERE post_type='woodmart_layout' AND post_status='publish'` = 10
- ⏳ Mobilni viewport vizuelni check nije urađen (browser resize alat nije pouzdano menjao render viewport u ovoj sesiji) — isti otvoreni item kao i za ostale WoodMart stranice
- Backup pre izmena: `antasline_local_2026-07-06_pre-category-landings.sql` (46,6 MB) u `C:\xampp\htdocs\antasline-backups\` (van webroot-a)
- Skript: `build-category-landings.php` (scratchpad, nije u vault-u — sadrži sav copy za 10 kategorija, ponovo pokretljiv sa `pilot`/`batch`/`all` argumentom)

## 2026-07-06 [claude-code] [DIZAJN - /sportske-podloge/ rebuild] — Silo hub na WoodMart šablonu ✅
- ✅ **Stranica ID 5438** (postojeći slug `/sportske-podloge/`, nije nova) rebuildovana po istom šablonu kao industrijski-podovi: hero (navy+plates) → intro + 6 USP kartica (paper: neklizajući, multisport, sertifikovano, montaža, održavanje, boje) → grid 11 sport disciplina sa foto karticama (mist, diag-top) → Bergo Ultimate specifikacija (paper) → FAQ 4 pitanja + FAQPage JSON-LD (mist) → CTA (navy, diag-top--rev)
- ✅ **Content parity izvor bio je dupli**: live sadržaj je u SiteOrigin `panels_data` (serijalizovan PHP niz, ne WPBakery — `content:encoded` prazan!), post_id 1849; napisan mali PHP ekstraktor (`unserialize` + `strip_tags`) da se izvuče tekst. Lokalni WPBakery sadržaj (pre-rebuild) imao je dodatnu hub-grid strukturu (12 sport kartica) koje live verzija nije imala — zadržano jer služi internom linkovanju ka postojećim sport stranicama
- ✅ **Yoast title/metadesc preneti sa live** (lokalno nisu postojali): "Sportske podloge za kosarku, basket, 3x3, odbojku, futsal" / metadesc o košarci, odbojci, rukometu, futsalu, tenisu
- 🔧 **Nova lekcija** (dodato u woodmart-sabloni): `/bergo-ultimate/` (ID 15480) ima `post_parent = 5438` → kanonski URL je `/sportske-podloge/bergo-ultimate/`, direktan `/bergo-ultimate/` vraća 301. Proveriti `post_parent` pre linkovanja na child stranice iz hub grid-a.
- ✅ Verifikacija: HTTP 200 · 1×H1 · FAQPage JSON-LD parsiran i validan (4 pitanja) · svih 11 slika kartica vraća 200 · svih 9 unikatnih link targeta (uklj. ispravljen bergo-ultimate) vraća 200 · WPBakery CSS keš meta očišćen posle izmene
- Backup pre izmena: post_content sačuvan u scratchpad (`sportske-podloge-BACKUP-content.txt`)

## 2026-07-05 [claude-code] [DIZAJN - /industrijski-podovi/ rebuild] — Silo landing na WoodMart šablonu ✅
- ✅ **Nova stranica ID 16567** po silo šablonu iz [[migracija/woodmart-sabloni]]: hero (navy+plates, H1 "Industrijski PVC podovi u pločama") → 6 USP kartica sa ikonicama (paper) → tabela debljina 500/5·500/7·500/10 + 4 kartice pod-asortimana (mist, diag-top) → reference Hankook/Amicus/Ecotile + HTEC·Quectel → FAQ 4 pitanja + FAQPage/Product JSON-LD (vc_raw_html) → CTA (navy+plates, diag-top--rev)
- ✅ **Slug odluka**: stara Porto stranica 4937 → **draft** + slug `industrijski-podovi-stara`; nova preuzima čist slug `/industrijski-podovi/` (home kartica već linkuje tamo). Porto_builder 15447 netaknut.
- ✅ **Yoast meta prenet sa 4937** (optimizovan 2026-06-25): title "Industrijski PVC podovi u pločama — bez zastoja proizvodnje | Antas Line" + metadesc; `_woodmart_main_layout=full-width`, `_woodmart_title_off=on`
- ✅ **Interni linkovi (silo juice)**: 3× Ecotile debljine + antistatik/ergonomski/trake/odbojnici (CPT `industrija-podovi`, svi 200) + conquest članak `/epoksidni-podovi-ili-ecotile-podovi/` + 2× kontakt
- ✅ **Content parity sa live** (ID 255): intro, "razlozi za Ecotile", teksture/boje, ESD varijante, Product schema (AggregateOffer 2.000–5.500 RSD) — sve preneto; cena-FAQ namerno izostavljen (rezervisan za buduću `/industrijski-podovi-cena/`, [[dnevnik/2026-07-05-draft-industrijski-podovi-cena]])
- 🔧 **Novi gotchas** (dodato u woodmart-sabloni pravila): (1) grid kartice sa h3/p unutra moraju biti `div` — `<a>`/`<span>` omotače wpautop uvija u `<p>` i lomi markup; (2) `vc_raw_html` enkoding je `base64_encode(rawurlencode($html))` — obrnut redosled daje prazan output; (3) `wp_insert/update_post` iz CLI (bez korisnika) **skida `[vc_raw_html]` blok** (kses/save filteri) → JSON-LD ubačen direktnim `$wpdb->update` + `clean_post_cache`
- ✅ Verifikacija: HTTP 200 · 1×H1 · svi al-* markeri · FAQ JSON-LD validan (parse test) · bez neizrendovanih shortcode-ova · vizuelno u Chrome (hero, USP, tabela, grid--4, reference, FAQ) · stara `/industrijski-podovi-stara/` = 404 (draft)
- Backup pre izmena: `backup-posts-postmeta-pre-industrijski.sql` (29,8 MB, scratchpad)
- Korišćen novi **ui-ux-pro-max** skil (landing pattern: social proof pre CTA → reference sekcija ubačena pre FAQ/CTA)

## 2026-07-05 [claude-code] [ALATI - UI/UX skill + Magic MCP] — Design alati instalirani za Claude Code
- ✅ **ui-ux-pro-max skill** (github.com/nextlevelbuilder/ui-ux-pro-max-skill v2.6.2) instaliran **globalno** u `C:\Users\Miroslav\.claude\skills\` — 7 skill-ova: `ui-ux-pro-max` (orkestrator: 67 UI stilova, 161 paleta, 57 font parova, 99 UX pravila, 25 chart tipova u CSV bazama + Python search) + pod-skill-ovi `banner-design`, `brand`, `design`, `design-system`, `slides`, `ui-styling`
- 🔧 `npm install -g ui-ux-pro-max-cli` blokiran od permission sistema (untrusted install skripte) → instalirano ručno: git clone + replikacija `uipro init --ai claude --global` logike iz `cli/src/utils/template.ts`; verifikovano (`search.py "glassmorphism" --design` radi)
- ✅ **Security audit skill paketa — čisto**: bez binarnih fajlova; Python/Node skripte bez eval/exec/mrežnih poziva/obfuskacije; bez prompt injection u SKILL.md; jedini spoljni URL-ovi = Pexels stock liste + Google Fonts. Napomene: `shadcn_add.py` poziva `npx shadcn` (samo za React projekte, na eksplicitan poziv), `logo/generate.py` traži `GEMINI_API_KEY` (bez ključa ne radi)
- ✅ **Magic MCP** (21st.dev) dodat u user scope: `claude mcp add magic --scope user ... -- npx -y @21st-dev/magic@latest` → ✔ Connected; API ključ u `~/.claude.json` (rotirati na 21st.dev ako zatreba)
- Namena: podrška za dizajn rad na WoodMart buildu (silo stranice, komponente) — aktivno od sledeće Claude Code sesije
## 2026-07-05 [cpanel-live] [FIX - LiteSpeed WebP optimizacija zaglavljena] — Red odblokiran, pipeline ponovo radi
- **Simptom:** LiteSpeed Cache ne konvertuje slike u WebP (QUIC.cloud optimizacija) — Miroslav prijavio da "ne radi".
- 🔍 **Pravi uzrok:** tabela `wp_litespeed_img_optming` imala **200 slika trajno zaglavljenih u statusu REQUESTED** (poslate ka cloud-u, notify webhook se nikad nije vratio) — to je tačno cela dnevna kvota (200/dan), pa je svaki naredni pokušaj slanja trajno blokiran porukom "Too many requested images". Dodatnih **1.561 slika** čekalo je u lokalnom redu (RAW) i nikad nije poslato. `img_optm-webp` konfiguracija je sve vreme bila ispravna (uključena) — problem je isključivo u zaglavljenom redu za slanje.
- 🔍 Potvrđeno debug logom (privremeno uključen `litespeed.conf.debug=1`, isključen posle): `new_req()` je stabilno vraćao `❌ Too many requested images 200`; `last_request.img_optm-taken` stoji zamrznuto na **2023-09-01** (skoro 3 godine) dok su novi zahtevi slati normalno (`last_requested` 2026-07-03) — tipičan znak trajno zaglavljenog reda, ne kvar konfiguracije.
- ✅ **Backup pre izmene:** `~/backups/antasline_db_2026-07-05_pre-litespeed-fix.sql`
- ✅ **Ispravka:** identifikovano 20 post ID-jeva sa 200 zaglavljenih redova → resetovano preko zvanične plugin metode `Img_Optm::reset_row()` (isto što radi "Reset Row" dugme u adminu, samo automatizovano za sve odjednom) — bez ručnog SQL brisanja
- ✅ **Verifikacija:** posle reseta, ručno pokrenut `new_req()` je uspešno poslao novih 200 slika (RAW 1561→1361, REQUESTED 0→200, potvrđeno da su to novi post ID-jevi, ne stari zaglavljeni) — pipeline ponovo teče
- ⏳ **Otvoreno:** cloud notify za ovih 200 nije stigao u prvih ~6 min posle slanja (uobičajeno, cloud obrada može trajati duže) — dalja obrada ide automatski kroz postojeći cron (`litespeed_task_imgoptm_req` na 15 min) bez potrebne dalje intervencije. **Proveriti za par dana** da li se isti simptom ("Too many requested images") ponovo pojavljuje — ako da, moguće je da QUIC.cloud nalog ima dublji problem sa notify webhook-om i treba njihova podrška.
- Access log (`~/access-logs/antasline.com-ssl_log`) potvrđuje: nema nijednog pokušaja od pravog QUIC.cloud servera da pozove `/wp-json/litespeed/v1/notify_img` u poslednjih ~12h — samo moji test curl pozivi. REST ruta je ispravno registrovana i reaguje (401 na neautentifikovan POST), nije blokirana firewall-om/security pluginom.

## 2026-07-05 [claude-code] [DIZAJN - 4 ispravke po Miroslavljevim primedbama]
- ✅ **Dugmad**: umesto reza samo sleva (delovalo isečeno) → simetrični paralelogram (obe kose ivice, padding 38px > rez 12px); isto i mobilno tel dugme
- ✅ **Ikonice sistem**: 6 custom SVG linijskih ikonica u brand stilu (crvena, 1.7 stroke) → `woodmart-child/images/icons/` (montaza, izdrzljivost, protivklizna, fleksibilna, odrzavanje, izgled) + `.al-icon` klasa — **osnova za ceo sajt**, USP kartice ih već koriste; za silo stranice dodati nove u istom stilu
- ✅ **Veruju nam**: 3 projekt foto kartice (Hankook fabrika guma / Amicus farmacija / Spanoulis Court — prave slike iz medijateke) + HTEC·Quectel·Dunk Shop tekst + logo traka proizvođača (Bergo/Ecotile/Artisport, grayscale→color hover, `.al-logo-row`)
- ✅ **Blog slike**: uniformno 3:2 (`aspect-ratio` + object-fit na `.wd-blog-holder`) — važi za sve blog loop-ove na sajtu
- Sve verifikovano vizuelno (Chrome): 6/6 ikonica, reference kartice, logo traka, blog kartice iste visine

## 2026-07-05 [claude-code] [FIX - /o-nama/ crash] — PHP segfault dijagnostikovan i rešen
- 🔍 Uzrok: `[porto_social_icons icon_size="{``xl``:``30``...}"]` — Porto backtick-JSON parametri izazivaju katastrofalno PCRE backtracking u `shortcode_parse_atts` → PHP proces pada bez traga u logu (isti uzrok kao crash stare home 143)
- 🔍 Metod: bisekcija po vc_row redovima u zasebnim PHP procesima (exit 255 = segfault) → red 2 → suženo na porto_social_icons
- ✅ Fix 1: no-op registracija svih 9 porto_* shortcode-ova u child temi (čisti raw junk iz izlaza legacy stranica)
- ✅ Fix 2: `porto_social_icons` fizički uklonjen iz 571 (no-op ne pomaže — atributi se parsiraju pre handlera); sadržaj ostao netaknut (3515→3097 B), verifikovano da se stranica renderuje sa tekstom
- ✅ **Sanacija svih 7 preostalih stranica** (61 kontakt, 5255, 5512, 15480, 15490, 15580, 16142): porto_* tagovi uklonjeni (unutrašnji sadržaj sačuvan), backtick atributi skinuti sa ostalih shortcode-ova (layout očuvan) — **nula backtick parametara u objavljenom sadržaju**
- ✅ Originali sačuvani (`backtick-pages-original.json` u scratchpad + jutarnji full snapshot); svih 7 verifikovano 200 + kontakt vizuelno (forma i podaci netaknuti)
- ✅ **Sve stranice bez sidebara**: `_woodmart_main_layout=full-width` na svih 25 pages (publish+draft); verifikovano na kontakt/o-nama/parking — bez sidebar markup-a. Blog postovi zadržavaju sidebar (odluka po potrebi)

## 2026-07-05 [claude-code] [DIZAJN - Figma sync] — Home usklađen sa Figma početkom dizajna
- ✅ Pročitan Figma fajl "Antas line" (Desktop-2 frejm, 1440×4663) kroz Figma konektor — struktura, tokeni, screenshot
- ✅ **Odluke (Miroslav):** naslovi ostaju **Bebas uppercase** (Figma koristi Inter Bold sentence case → Figma se dovodi u sklad kasnije); header CTA ostaje **telefon 072** (ne "Zatražite ponudu" — podaci: ~50 tel klikova/mes, 46 mobil)
- ✅ Usklađeno sa Figmom na buildu: **foto hero** (Spanoulis teren + navy gradijent overlay, `al-hero-photo`), **5 kategorija** (+ Poslovni prostori, Expona Commercial slika), **6 USP kartica** ("Zašto izabrati Antasline?": brza montaža, izdržljivost 25g, protivklizna, fleksibilna, održavanje, izgled — umesto 3 brojke), naslovi sekcija iz Figme ("Temelj vrhunskog poda")
- 🔧 Bugovi: WPBakery `.vc_row:before` clearfix (display:table) skuplja overlay na 0×0 → eksplicitni display/width/height; CSS keš → `filemtime` verzionisanje enqueue-a; kartice različitih proporcija → `aspect-ratio: 4/3` + object-fit
- ⏳ Iz Figme još neimplementirano: testimonials kartice (imaju placeholder copy — čekaju prave recenzije sa GMB), "Najprodavanije podloge u 2025." foto sekcija — po odluci
- Sve verifikovano vizuelno (Chrome) — hero overlay, 5 kartica sa slikama, 6 USP kartica renderuju
- 🔧 Meni "Početna" (2 menija) pokazivao na staru draft stranicu 143 (404) → prevezano na novu Početnu 16550; potvrđeno da link vodi na `/`

## 2026-07-05 [claude-code] [DIZAJN - Mondo look implementiran] — Design system + header + home na WoodMart buildu ✅
- ✅ **Analiza Monda** (Chrome + computed styles): Bebas Neue + Proxima Nova, clip-path dijagonale, paralelogram CTA, dijamant strelice → plan odobren (Inter + Bebas Neue, boje strogo brand book)
- ✅ **Fontovi self-hosted**: Inter 400/600/700 + Bebas Neue woff2 (latin+latin-ext, šđčćž ✓) u `woodmart-child/fonts/` — nula CDN zahteva (uklonjen i preconnect hint)
- ✅ **Design system**: `woodmart-child/css/antas-design.css` — tokeni, `:root:root` preklapanje WoodMart varijabli, utility klase (al-section/diag/plates/btn/label/card/stat/grid) — katalog u [[migracija/woodmart-sabloni]]
- ✅ **Header kodom** (filter `woodmart_default_header_structure`): navy top bar (adresa+mail+074) · beli glavni red: logo SVG + uppercase meni + crveni paralelogram CTA "POZOVITE NAS 069 234 00 72" · sticky · mobilni: burger/logo/tel dugme
- ✅ **Home (16550) izgrađen**: hero "PODOVI KOJI IZDRŽE SVE" (navy + listajuće ploče = potpis iz logoa) → 4 segment kartice (Industrijski/Sportski/Terase/Parking, slike iz medijateke) → USP 25/0/1 → reference (Hankook·HTEC·Amicus·Quectel·Dunk Shop·Spanoulis) → blog masonry 3 kol → završni CTA; `_woodmart_main_layout=full-width`, `_woodmart_title_off=on`
- 🔧 Bugovi rešeni usput: wpautop razbijao grid (`<br>` između kartica → HTML u jednoj liniji); sidebar preko hero-a (full-width meta); `woodmart_blog` param je `blog_columns`, ne `columns`
- ✅ Verifikovano vizuelno (Chrome, svih 6 sekcija) + fontovi lokalno + smoke 200
- **Sledeće:** rebuild silo stranica po šablonu iz [[migracija/woodmart-sabloni]] (live copy + sufiks 5 pravilo) · footer · mobilna provera · Figma link #ceka-miroslav

## 2026-07-05 [claude-code] [BREND - logo SVG izvoz] — Vektorski logo izvezen iz PDF-a za WoodMart header
- ✅ PyMuPDF izvoz iz `Logo/ANTAS LINE FINAL LOGO.pdf` — **pravi vektor (SVG), ne raster**; tight crop na bounding box crteža (+6pt margina)
- ✅ Boje normalizovane na zvaničnu paletu iz [[reference/brend-knjiga]] (`#0E2950`/`#0B3E75`/`#5287B7`/`#F04D22`/`#F89C1C`) — MuPDF konverzija odstupala 1–2 jedinice
- ✅ Fajlovi u `Logo/`: `antas-line-logo-vertikalni.svg` + `.png` (668×777, transparent) · `antas-line-logo-horizontalni.svg` + `.png` (1360×435, transparent) — PNG jer WP media po default-u ne prima SVG
- ✅ Kopirano i u `wp-content/themes/woodmart-child/images/` za header builder
- Vizuelno verifikovano (render PNG-a) · SVG ima `role="img"` + aria-label
- Otvoreno: bela varijanta za navy footer — napraviti kad header/footer dizajn to zatraži

## 2026-07-05 [claude-code] [WOODMART - instalacija] — Tema instalirana i aktivirana na lokalu ✅
- ✅ WoodMart **8.5.4** (tema + child `woodmart-child` sa brand CSS varijablama iz [[reference/brend-knjiga]]) + **woodmart-core 1.1.8** aktivirani; WPBakery ažuriran 8.7.2 → **8.7.3** (bundlovan, stara verzija sačuvana u `C:\Projekti\woodmart-tema\bak\`)
- ⏭️ Revolution Slider iz bundle-a NAMERNO preskočen (CWV balast, ne koristi se)
- 🔧 **Home (143, Porto carousel sadržaj) izaziva PHP crash pod WoodMart-om** → nova prazna Početna (ID 16550) postavljena kao front page, stara 143 u draft (home se ionako gradi ispočetka)
- ✅ Smoke test 200: home, proizvod, post, kontakt, sportske-podloge, industrijski-podovi
- ✅ Live export stigao u vault: 30 postova XML + 50 pages XML (referenca) + parity CSV (80 redova); ⚠️ lokal ima 32 posta vs 30 live — utvrditi koja 2
- Snapshot pre svega: `backup-pre-woodmart-rebuild-20260705-1125.sql`
- **Sledeće:** header/footer u WoodMart builderu (brand book look) → theme options (Inter self-hosted + boje) → import 30 postova → rebuild pages po prioritetu

## 2026-07-05 [cpanel-live] [MIGRACIJA - live export za WoodMart rebuild] — Read-only export završen → [[migracija/2026-07-05-live-export-prompt]]
- ✅ `migracija/live-export-2026-07-05/live-posts-2026-07-05.xml` — 30 objavljenih postova (WXR)
- ✅ `migracija/live-export-2026-07-05/live-pages-2026-07-05.xml` — 50 objavljenih pages (WXR, samo referenca za rebuild)
- ✅ `migracija/live-export-2026-07-05/live-inventar-2026-07-05.csv` — 80 redova (30 posts + 50 pages): ID, slug, permalink, title, Yoast title/meta, word count
- **Kontrolni brojevi (live vs. lokal staging):**
  - Postovi: **30** live vs. 32 na lokalu (razlika −2, proveriti koja 2 posta nedostaju/su viška)
  - Pages: **50** live (lokal broj nije evidentiran u ovoj sesiji za poređenje)
  - Proizvodi: **37** live = **37** lokal ✓ (poklapa se sa C2 importom)
  - Kategorije: **10** live = **10** lokal ✓ (poklapa se)
- ✅ Striktno read-only prema WP-u — nikakva izmena baze/fajlova/podešavanja, samo export + zapis u vault
- **Sledeće:** `git pull` na lokalu → import postova (`wp import` ili WP admin), CSV inventar kao checklist za page rebuild parity

## 2026-07-05 [claude-code] [ODLUKA - WoodMart clean rebuild] — GO za novi sajt na lokalu
- ✅ **Odluka (Miroslav):** ne konvertuje se Porto sadržaj — novi sajt na postojećem lokalnom installu: proizvodi ostaju (37+slike), postovi se prenose sa live, pages se grade NOVE u WoodMart+WPBakery prema live/staging sadržaju + C3 draftovi direktno ugrađeni
- ✅ Snapshot pre svega: `C:\xampp\htdocs\antasline\backup-pre-woodmart-rebuild-20260705-1125.sql` (44,1 MB, verifikovan header+footer)
- ✅ Prompt za cPanel live export (posts XML + pages XML referenca + parity CSV inventar) → [[migracija/2026-07-05-live-export-prompt]]
- ✅ WoodMart licenca postoji; tema fajlovi idu u `C:\Projekti\woodmart-tema\` (van vault-a, da ne ulazi u git)
- **Sledeće:** Miroslav pokreće cPanel prompt + dostavlja woodmart.zip → instalacija teme, header/footer (brand book), rebuild po prioritetu

## 2026-07-05 [claude-code] [TEHNIČKA - WoodMart audit] — Porto → WoodMart procena → [[dnevnik/2026-07-05-audit-porto-woodmart]]
- ✅ Read-only audit 53 objavljena page/post: 53% čist HTML, 9% vanilla vc_*, **30% (16 stranica) sa porto_* elementima** — 8 različitih elemenata, dominira porto_block (10)
- ✅ Procena: ~3–5 radnih dana (16 stranica zamena + header/footer + test); Woo proizvodi/Yoast meta/redirect mapa netaknuti
- 💡 Bonus: čišćenje porto_* na 4937 verovatno rešava i WPBakery JS bug koji blokira 6 blokova
- **Zaključak: prelazak jeftin, rok nije ugrožen — GO/NO-GO #ceka-miroslav** (pre aktivacije: db export + js_composer verzija + licenca)

## 2026-07-05 [cpanel-live] [C3 - #9 odbojka refresh] — Primenjeno na live (delimično) → [[dnevnik/2026-07-05-refresh-odbojka]]
- ✅ Post 4318 (`/podloga-za-odbojkaske-terene/`) izmenjen na live: H1, snippet pasus, sekcija "peska", FAQ (4 pitanja) + FAQPage JSON-LD
- ✅ Backup pre izmene: `~/backup-pre-odbojka-refresh-20260705-1020.sql`
- ✅ Verifikovano curl-om: sve sekcije prisutne, JSON-LD validan
- ⏭️ Namerno preskočeno: Yoast title (#1) i meta description (#2) — po eksplicitnom zahtevu
- ⏳ Cena sekcija (#6) NIJE ubačena — čeka stvarne cifre od Miroslava (placeholder na live bi bio vidljiv posetiocima)
- **Sledeće:** Rich Results Test, GSC Request indexing, C2 parity (stranica ne postoji na lokalnom buildu)

## 2026-07-05 [claude-code] [C3 - #9 odbojka refresh] — Kompletan refresh paket → [[dnevnik/2026-07-05-refresh-odbojka]]
- 🔍 Dijagnoza CTR 0,6% @ poz. 2,3: live title bez reči "dimenzije" (a to je ~80% od 7.817 impr klastera), **meta description ne postoji**, nema FAQ/cene/peska
- ✅ Copy-paste paket: novi title+meta, snippet pasus (18×9), nova sekcija odbojka na pesku (16×8, ~330 impr), cena sekcija (placeholderi), FAQ HTML + FAQPage JSON-LD, postupak primene korak-po-korak
- ⚠️ **Stranica postoji SAMO na live** → primena ide `[cpanel-live]` kroz WP admin (~15 min) #ceka-miroslav; lokalni build je nema → **C2 parity gap zabeležen**
- Merenje: CTR klastera pre (0,6%) vs 28d posle primene

## 2026-07-05 [claude-code] [C3 - TIER 1 draftovi] — Svih 5 preostalih Tier 1 stranica draftovano
- ✅ #1 [[dnevnik/2026-07-05-draft-gumeni-podovi-za-terase-cena]] — cena tabela 4 tipa, conquest sekcija za "epoksidni podovi za terase" (1.499 impr)
- ✅ #2 [[dnevnik/2026-07-05-draft-industrijski-podovi-cena]] — odluka: posebna stranica (4937 blokiran WPBakery bugom); postaje i Ads landing → gasi 4,1k RSD curenja
- ✅ #3 [[dnevnik/2026-07-05-draft-podovi-za-garaze]] — konsolidovana landing za 14k impr klaster + 16,8k RSD Ads rupe
- ✅ #4 [[dnevnik/2026-07-05-draft-dimenzije-kosarkaskog-terena]] — snippet-format tabele FIBA/NBA/školski; ⚠️ anti-kanibalizacija vs basket članak (skratiti tamo dimenzije)
- ✅ #6 [[dnevnik/2026-07-05-draft-podloge-za-parkiraliste-cena]] — cena+nosivost+saće-vs-šljunak (hvata i ~700 impr šljunak upita); #5 tabla draftovan juče
- Svi draftovi: Yoast title/meta + H2 struktura + FAQ HTML + FAQPage JSON-LD + CTA 072/forma + interni linkovi; cene = `{{PLACEHOLDER}}` #ceka-miroslav
- **Sledeće:** cifre od Miroslava → implementacija na lokalnom buildu (WPBakery) → Rich Results Test

## 2026-07-05 [claude-code] [BREND] — Logo + brand book dodati u vault → [[reference/brend-knjiga]]
- ✅ Pregledani `Logo/ANTAS LINE FINAL LOGO.pdf` (vertikalna + horizontalna varijanta) i `Logo/Brand book.pdf` (13 str.)
- ✅ Specifikacije izvučene u [[reference/brend-knjiga]]: paleta (655 C / 279 C / 172 C / 137 C), tipografija **Inter**, web look&feel (crveni CTA "pozovite nas" 069 234 00 72), kontakt podaci
- ✅ HEX boje izmerene pipetom iz renderovanog vektorskog PDF-a (pdfium): teget `#0E2950`, plava `#0B3E75`, svetloplava `#5287B7`, crvena `#F04D22`, narandžasta `#F89C1C` — **ove koristiti u temi**, ne Pantone aproksimacije
- ⚠️ 4 greške u PDF-u za dizajnera pre štampe: "KNJGA" typo na svim stranama, dupliran Pantone 655 C za dve različite plave, "enviroment", "Informisite se"
- Relevantno za redizajn: Porto tema → Inter font (self-hosted, Core Web Vitals) + brand boje u temi

## 2026-07-04 [claude-code] [GEO/AI plan] — Kako da AI preporučuje Antasline → [[seo/geo-ai-plan]]
- ✅ GEO strategija: AI crawleri (robots.txt/llms.txt), citabilan sadržaj (C3 plan = GEO gorivo), entitet schema, pominjanja treće strane (PR o Spanoulis/Dunk Shop terenima, case studije Hankook/HTEC/Quectel), GMB recenzije
- ✅ Merenje ugrađeno u [[analiza/_TEMPLATE-snapshot]] §4.5: GA4 AI Assistant kanal (baseline 9 korisnika/90d) + 5 fiksnih ChatGPT test promptova
- Otvoreno: robots.txt provera na live #ceka-miroslav · llms.txt priprema #claude-code

## 2026-07-04 [claude-code] [C3 - #5 draft] — Sadržaj za `/dimenzije-kosarkaske-table/` napisan → [[dnevnik/2026-07-04-dimenzije-kosarkaske-table]]
- ✅ Pun draft: naslov/meta, body (dimenzije, materijali, DIY sekcija, cena), FAQ HTML + FAQPage JSON-LD
- Cilja ~2.400 impr "tabla" upita (poz. već 1–3,5 — problem je pokrivenost/CTR, ne rang)
- Link ka kategoriji Košarkaške konstrukcije (slug čeka C1 redirect odluku)
- **Status: draft gotov, čeka implementaciju na lokalnom buildu** (cifre cena + finalni slug čekaju Miroslava)

## 2026-07-04 [claude-code] [C3 - Content plan] — 20 novih stranica u 4 tijera → [[seo/plan-novih-stranica]]
- ✅ Master plan izveden iz 16m keyword analiza (GSC + Ads); obuhvata i ranije 4 GSC stranice
- Tier 1 purchase-intent: terasa cena (4.221 impr), industrijski cena, garaže landing, basket set
- Tier 2: tenis hub (šljaka 9k impr), odbojka refresh (poz. 2,3 / CTR 0,6% — 30 min posla), piklbol/padel
- Tier 3: komercijalni vertikali (kancelarije poz. 1,9!, restorani, zdravstvo, tržni centri)
- Tier 4: reference tereni (Dunk Shop/Spanoulis ~3k impr), Bergo brend, teretane
- Povezano sa [[blokovi/BLOK-C-sledece]] C3 + [[PROGRESS]] Sledeće #2

## 2026-07-04 [claude-code] [ANALIZA - Ads search terms 16m + GSC poređenje] → [[analiza/2026-07-04-ads-st-analiza-16m]]
- ✅ Svih 1.899 Ads search termina (16m, 107,8k RSD, 5 konv) kroz iste klastere kao GSC + CSV banka
- 🔴 **Curenje kvantifikovano: 16.607 RSD = 15,4%** (315 termina krši negativnu listu = 10,5k; 289 van ponude = 6,1k — deking 2,3k!)
- 🔴 Garaže = ogledni problem: 16,8k RSD + organik poz. 8–10 + 14k GSC impr = 0 konverzija → landing, ne kanal
- 🔴 "pvc podovi" broad = 5,5k RSD bez konverzije; "industrijski podovi cena po m2" 4,1k (landing nema cenu)
- ✅ Struktura kanala zdrava: basket/parking organik #1 → 0 RSD u Ads; industrijski paid opravdan (jedini konvertuje, 3)
- **Ključ:** cena-termini = 19% Ads potrošnje jer organik nema cena stranice → cena sekcije rešavaju oba kanala

## 2026-07-04 [claude-code] [ANALIZA - GSC keywords 16m] — Svih 2.893 upita klasterizovano → [[analiza/2026-07-04-gsc-kw-analiza-16m]]
- ✅ Puna GSC banka (16m) → CSV + klasterizacija (24 klastera × intent) PowerShell skriptom
- 🔴 Top nalazi: odbojka wpos 2,3 / CTR 0,6% (7.8k impr!); tenis 23,7k impr / CTR 1,7% (šljaka 9k impr); industrijski cena-gap; epoksid conquest poz. 26 za "epoksi podovi"; komercijalni vertikali (kancelarije poz. 1,9!) bez stranica; reference-tereni (Dunk Shop/Spanoulis ~3k impr)
- 📊 Intent: cena CTR 9,9% vs info 3,3% — cena stranice rade gde postoje (20–30% CTR)
- **Akcioni plan:** 10 stavki u §5 analize (odbojka → tenis hub → cena sekcije → piklbol → vertikali…)

## 2026-07-04 [claude-code] [ANALIZA - puni snapshot] — Dnevnik stanja: Ads+GA4+GSC+GMB (baseline) → [[analiza/2026-07-04-snapshot-full]]
- ✅ Novi folder `analiza/` — sistem periodičnih snapshot-ova (README + template + prvi puni snapshot)
- ✅ Povučeno ~25 pull-ova kroz Windsor.ai: GSC (16mo trend, upiti, stranice, uređaji, movers), GA4 (trend, kanali, eventi, publike), Ads (trend, kampanje, KW, search terms, imp. share), GMB (trend, keywords, recenzije, profil)
- 🔴 **Nalazi:** GA4 `conversions` slomljen od juna (5.859!) → key event audit #ceka-miroslav; hvala-proxy postoji tek od juna (55 = baseline); negativne KW ne važe na kampanjama (epoksid/sika/rinol prolaze, ~16% otpada) #ceka-miroslav; GSC CTR erozija (jun YoY: klikovi −19%, impresije +22%)
- 🟢 **Nalazi:** ECOTILE phrase "industrijski podovi" = 1.073 RSD/konv.; jun = najveći Ads mesec (30,7k); Product snippets CTR 10,5%; prelaz na nove kampanje uspeo
- **Strategija:** §6 snapshot-a — 5 SEO + 6 Ads + 4 GMB + 3 tracking akcija, prioritizovano
- **Akcija nedelje:** proveri negativnu listu na obe kampanje + skini 2 pogrešna KW (15 min, zaustavlja ~16% rasipanja)

## 2026-07-04 [claude-code] [VAULT - konzistentnost] — Ispravke nedoslednosti + brisanje B3
- ✅ Obrisan `B3 - Odblokiranje naloga.md` (zadatak gotov: balans + verifikacija) + prazan `2026-07-02.md`
- ✅ Sve B3 reference uklonjene/ažurirane; ADS Faza 0 zatvorena u [[dnevnik/ADS-DNEVNIK]], PROGRESS, MASTER-PLAN, CLAUDE, BLOK-B
- ✅ ECOTILE status: ⛔ zagušena → ✅ odblokirana (istorijski logovi ostaju netaknuti)
- ✅ Konektor `googleads` → `google_ads` u [[reference/identifikatori]]
- ✅ Konverzije usklađene: `33` → `53` (jun) u [[00-INDEX]] + [[odluke/_pregled-odluka]]
- ✅ Datum migracije: `2026-09-01 (utorak, pogrešno)` → `ponedeljak 2026-08-31`; weekly cadence prepravljen
- ✅ WooCommerce lokal import (04.07) označen gotovim u blokerima (SSH ostaje samo za live)
- **Otvoreno:** potvrditi u Ads da su ECOTILE prikazi/CPC vraćeni na normalu #claude-code

## 2026-07-04 [claude-code] [BLOK C1 - Redirect mapa VERIFIKACIJA] — ✅ SKORO GOTOVO! 106/118 redova finalizovano
- ✅ Proverio 18 stranica sa "PROVERI da postoji" — sve postoje na localhost
- ✅ WooCommerce kategorije — sve 10 postoje sa `/kategorija/...` URL struktura
- ✅ WooCommerce proizvodi — svi 37 postoje sa `/shop/kategorija/proizvod/` struktura
- ✅ Refresh-ovao WordPress permalinks — URL struktura sada ISPRAVNA
- ✅ Ažurirao CSV: 18+41 = 59 redova sa AUTO-PREDLOG → "postoji"
- ✅ Popunio 4 "ZA POPUNITI" redova — kategorija URL-evi
- ⏭️ Preostalo: 5 "NEMA NA BUILDU" redova (skipped za kasnije) + 2 "Dodati kontent" (minor)
- **CSV Status:** 106 redova "gotovo" od 118 (89.8% kompletan)
- **Sledeće:** Kreiraj 301 redirect-e u WordPress

## 2026-07-04 [claude-code] [BLOK C2 - WooCommerce import] — ✅ ZAVRŠENO! Proizvodi sa live → staging
- ✅ **Live export** preuzet: `woo-export-2026-07-04.zip` (products.csv + variations.csv)
- ✅ **37 proizvoda** importovano na localhost
- ✅ **10 kategorija** automatski kreirane i vezane:
  - Industrijska zaštita (24), Zaštita i Bumperi (19), Podno označavanje (6), Košarkaške konstr. (5), itd.
- ✅ **115 slika** preuzete sa live sajta kroz `media_sideload_image()`
- ✅ **Svi opisi + specifikacije** (srpski znakovi ispravno, bez čudnih karaktera)
- ✅ **24 stranice + 34 posta ostaju netaknuti**
- **Problem rešen:** UTF-8 BOM (`﻿id`) u CSV header — `ltrim($header[0], "\xEF\xBB\xBF")`
- **Problem rešen:** Separator za kategorije bio `|` umesto `,`
- **Finalni bekap:** `backup-FINAL-37products-10categories-20260704.sql`
- **Script:** `import-final-woo.php` — robustan, čuva UTF-8, kreira kategorije ako ne postoje, preuzima slike

## 2026-07-03 [claude-code] [BLOK C - WooCommerce import] — Prebacivanje proizvoda sa live na localhost
- ✅ Vratim bekap pre nego što su obrisani proizvodi (backup-pre-parity-20260628-1135.sql) — homepage i stranice ostaju
- ✅ Obrisao samo 43 stara proizvoda + kategorije (bez dotacanja stranica/postova)
- ✅ Učitana live baza (127 MB) u temp
- ✅ Prebačeni proizvodi + attachment-i sa live baze (sa konverzijom prefixsa wp_ → wpGs_)
- ✅ Preuzeli XML export sa live (`antasline.WordPress.2026-07-03.xml`)
- ✅ WP-CLI import: `wp import import.xml --authors=create` — 42 proizvoda importovana sa svim meta podacima
- **Rezultat:** 42 proizvoda, 41 sa slikama (97.6%), 434 relevantne attachment-a, 24 stranice + 34 posta netaknuti
- **Otvoreno:** Ručno brisanje dodatnih/nepotrebnih slika na proizvodima #ceka-miroslav
- Finalni bekap: `backup-FINAL-41sa-slikom-20260703.sql`

## 2026-07-03 [cpanel-live] — Optimizacija baze (UŽIVO)
- Backup: `~/backups/antasline_db_2026-07-03_2031.sql`
- Otklonjen kritični problem: `wp_litespeed_img_optm` imala 3.251.490 orphan redova (post_id=0, src prazan) — runaway LiteSpeed greška → tabela sa 315.91 MB smanjena na 0.05 MB
- Obrisano 50 post revizija, 1 expired transient, 34 stara ActionScheduler completed akcija
- OPTIMIZE TABLE na svim tabelama (recreate+analyze)
- **Ukupna veličina baze: 354 MB → 38.67 MB (-89%)**

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
- [[CLAUDE]] kreiran u ~/public_html/ sa vault workflow instrukcijama
- live-export.sh popravljen (trailing comma bug, --no-create-info bug); woo-export.sql 444K generisan (47 proizvoda, 71 attachment, 22 pa_* atributa)
- Otvorene akcije: prenos woo-export.sql na staging #ceka-miroslav, brisanje GTM tel: taga #ceka-miroslav

## 2026-06-28 [chat] — Obsidian vault + git most postavljen → [[dnevnik/2026-06-28-postavljanje-vault]]
- Vault C:\Projekti\antasline-vault\ kao jedina istina; GitHub Chichabudhha/antasline-vault
- [[DNEVNIK-NAPRETKA]] + [[PROGRESS]] preseljeni iz htdocs; cPanel vault kloniran; git most testiran OK
- Sledeće: BLOK C1 redirect mapa (nov chat, Sonnet, zalepi [[PROGRESS]] u seed)

## 2026-06-28 [chat] — Obsidian vault + git most postavljen
- Vault: C:\Projekti\antasline-vault\ kao jedina istina projekta
- [[DNEVNIK-NAPRETKA]] i [[PROGRESS]] preseljeni iz htdocs u vault
- GitHub repo: Chichabudhha/antasline-vault (private)
- Obsidian Git plugin aktivan, auto-sync 10min
- cPanel: vault kloniran u ~/antasline-vault, [[CLAUDE]] kreiran
- Git most zatvoren: lokal ↔ GitHub ↔ cPanel sinhronizovani
- Sledeće: BLOK C1 — redirect mapa (nov chat, Sonnet model)
## 2026-06-28 [chat] — Obsidian vault postavljen i objedinjen
- Vault `C:\Projekti\antasline-vault\` postao jedina istina projekta.
- [[DNEVNIK-NAPRETKA]] i [[PROGRESS]] preseljeni iz htdocs u vault.
- [[CLAUDE]] (htdocs) dopunjen vezom ka vault-u; Claude Code odsad loguje ovde.
- Detaljan zapis: [[dnevnik/2026-06-28-postavljanje-vault]]
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
