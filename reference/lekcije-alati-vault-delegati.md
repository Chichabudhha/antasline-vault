---
name: lekcije-alati-vault-delegati
description: Tehnicki gotchas — vault/ledger workflow, delegat-agenti (ollama/agy/grok/copilot), browser automation, AI foto/video tooling (Gemini/Flow), opsti proces/verifikacija. Deo 4/4 rascepa naucene-lekcije.md (2026-08-20, vault higijena).
---

# Naucene lekcije — alati / vault workflow / delegat-agenti / proces

> 4/4 tematskog rascepa `reference/naucene-lekcije.md` (2026-08-20). Ostala tri: [[reference/lekcije-wp-db-tehnika]] · [[reference/lekcije-seo-sadrzaj-migracija]] · [[reference/lekcije-ads-tracking]]. Indeks: [[reference/naucene-lekcije]].

## Claude-in-Chrome `resize_window` ne menja stvarnu veličinu prozora u ovom okruženju (2026-08-20)

Testirano više puta (dva odvojena taba, vrednosti 390×844, 400×850, 1024×768,
1440×900) — alat vraća "Successfully resized" ali `window.innerWidth`/`innerHeight`
posle poziva i dalje pokazuju punu rezoluciju ekrana (1920×1080-ish), bez obzira
na traženu vrednost. CSS `zoom` na `documentElement` ni preko 400% ne menja
`window.innerWidth` niti `matchMedia('(max-width:767px)').matches`. Prozor je
verovatno zaključan na fiksnu rezoluciju od strane window managera ovog
sandboxed okruženja. **Posledica:** mobilni breakpoint (`@media max-width:767px`)
se ne može vizuelno potvrditi kroz Claude-in-Chrome u ovoj sesiji — dijagnoza
mobilnih bagova mora ići kroz čitanje CSS-a/DOM-a (`getComputedStyle`, DOM
introspekcija) umesto pravog screenshot-a na uskom viewport-u. Ako ovo ikad
proradi (nova sesija/okruženje), probaj ponovo pre nego što pretpostaviš da
i dalje ne radi.

## Proveri content freeze status pre publish-a, čak i za ad-hoc katalog rad (2026-08-20)

Codex Onda proizvod je objavljen (status `publish`, ne draft) 20.08 — baš
poslednji dan content freeze prozora (17.08 → ČET 20.08) — bez prethodne
provere `PROGRESS.md` header-a. Materijalni rizik je bio nizak (nov proizvod
van postojeće regression-sweep baseline stranice), ali procesno pravilo
važi bez obzira na procenjeni rizik: pre bilo kog `post_status='publish'`
upisa (ne draft) proveriti freeze prozor u `PROGRESS.md` header-u prvo,
i ako se poklapa sa freeze danima — ili sačekati M potvrdu, ili jasno
upisati u dnevnik da je publish namerno urađen unutar freeze prozora i zašto.

isti, uzrok nije.

## Sopstvena beleška se čita do kraja pre nego što se potroši novac (2026-08-18)

Memorijska beleška je jasno govorila: Gemini free tier **ne pokriva** generisanje slika,
`limit: 0`, mora se uključiti naplaćivanje. Kad je prvi API poziv prošao, zaključio sam
„beleška je zastarela" — a tačan zaključak je bio obrnut: **prošao je zato što naplaćivanje
jeste uključeno**, dakle svaka slika se plaća. Odatle je otišlo 8 poziva bez pitanja
(~0,04 USD po slici).

Pravilo: kad rezultat protivreči sopstvenoj belešci, prvo proveri da li beleška zapravo
**objašnjava** rezultat, pa tek onda da je pogrešna. I: poziv koji troši tuđi novac traži
najčešće na disku, ne u `wp_options`.

## SVG nije alat za dijagram koji mora da radi na telefonu (2026-08-18)

Dijagram skale otpora crtan kao SVG 1000×420 sa tekstom od 13–15 px izgleda odlično na
desktopu, a na 390 px širine natpisi padnu na ~5 px. **Uzrok je strukturni:** u SVG-u se
tekst skalira zajedno sa slikom, pa povećanje fonta ne pomaže dok je oblik široka
horizontalna traka.

Isti sadržaj kao HTML/CSS grid (komponenta `.al-scale`) rešava sve odjednom: font ostaje
u px bez obzira na širinu, kolone se prelamaju u jednu, tekst se selektuje, indeksira i
čita čitačem ekrana. **SVG čuvati za ono što jeste crtež** (presek poda, šema uzemljenja),
ne za tabelarni sadržaj koji samo izgleda kao grafika.

Uz to: WoodMart reset spljošti `<sup>` na baseline, pa `10⁴` bude renderovano kao `104` —
traži `vertical-align: super` u sopstvenom pravilu.

## Diff po rečenicama lažno prijavljuje da sadržaj nedostaje (2026-08-18)

Poređenje live i build stranice po rečenicama dalo je „42 od 52 rečenice sa live-a ne
postoje na buildu", iz čega je izvedeno osam nepostojećih rupa. Build je u stvari live
tekst **prepisao u tečnije pasuse**, pa se nijedna rečenica nije poklopila doslovno iako
je značenje preneto. Stvarnih rupa bilo je pet.

Parity se meri **po temama i entitetima** (pominje li stranica ATEX, zoniranje bojama,
uzemljenje na 80 m², referentne klijente), ne po podudaranju stringova. Pre prijave nalaza
u brauzeru i pogledati je — naročito kad natpis tvrdi nešto što slika treba da potvrdi.

## Ispravka na dnu append-only loga ne poništava tvrdnju sa vrha (2026-08-18)

`ADS-DNEVNIK.md` je 12.08 dobio uredno napisanu ispravku: „kumulativ 26 / prag pređen
NE VAŽI, pravih lidova ima 9". Ispravka je bila tačna, datirana i na pravom mestu — u
Log sekciji. Ipak je hub **šest dana** nastavio da se čita kao da je prag pređen, jer
zaglavlje fajla i stariji unosi (11.08, 06.08, 30.07) i dalje tvrde suprotno, a ispravka
je jedan blok među desetinama.

**Pravilo:** kad ispravka poništava **stanje** (a ne samo jedan istorijski unos), mora i
na **vrh** dokumenta — kao `[!warning]` blok ispod zaglavlja. Append-only log ostaje
netaknut (istorija se ne prepravlja), ali trenutno stanje ne sme da se rekonstruiše
čitanjem 40 redova unazad. Isti obrazac je 13.08 već jednom ugrizao (`DNEVNIK-NAPRETKA`
unos završio na dnu newest-on-top fajla → bio nevidljiv).

---

## Zastareli rokovi: živi dokumenti se ispravljaju, datirani se ne diraju (2026-08-18)

Pri zatvaranju konflikta „go-live 31.08 vs 25.08" ispalo je da `31.08` i dalje stoji u
6 fajlova. Pet su **datirani** sesijski planovi/promptovi (`2026-07-27-cpanel-sesija-plan.md`
i sl.) — to su istorijski zapisi, tačni za trenutak pisanja, i **ne prepravljaju se**.
Šesti, `w1-novi-proizvodi-court-builder.md`, je **živi red čekanja bez datuma u imenu** —
i baš je on bio jedini pravi problem, jer se čita kao aktuelno uputstvo.

**Pravilo:** kod pomeranja roka grep-ovati stari datum, pa razdvojiti pogotke po tome da
li fajl ima datum u imenu. Bez te podele se ili prepravlja istorija, ili se propušta
jedini fajl koji stvarno vara.

- Srodno: [[reference/naucene-lekcije]] „Odluka se donosi nad bazom, ne nad zapisom u vault-u (2026-08-17)" — isti obrazac, drugi izvor istine.

## Odluka se donosi nad bazom, ne nad zapisom u vault-u (2026-08-17)

M odluka „14 proizvoda bez fotografije → draft" bila je pisana nad spiskom iz
[[PROGRESS]] poslednji put ažuriranim **30.07**. Provera baze pred izvršenje:
spisak nabraja **13** ID-eva (ne 14), a **7 ih je u međuvremenu dobilo sliku** —
06. i 07.08, pri čemu je 6 od tih 7 rešeno **eksplicitnim M odobrenjem** generičkih
dobavljačkih fotografija. Doslovno izvršenje bi ugasilo 7 ispravnih proizvoda.

Isti obrazac kao sistemski nalaz od 13.08 („zapisane GSC brojke tri puta netačne
→ pre svake odluke ide svež pull"). **Pravilo se proširuje sa brojki na spiskove
ID-eva:** pre izvršenja bilo koje bulk izmene (draft, brisanje, prevezivanje),
regeneriši spisak upitom nad bazom i uporedi sa zapisom — razlika je nalaz, ne šum.

Prateće pravilo: **draft proizvoda sa internim linkovima traži sanaciju hub
stranice u istom koraku**, inače se 404 pojavi tek u sledećem regression sweep-u.
`grep -rn "klasa" wp-content/themes --include=*.css --include=*.php`.

## Reference koje agent čita kao autoritet ustaju tiho (2026-08-14)

`reference/identifikatori.md` je 14.08 zatečen sa datumom 27.07 i **tri od pet
tvrdnji o lokalnom okruženju netačne**:

| Tvrdnja | Pisalo | Stvarno |
|---|---|---|
| Broj tabela | 106 | **78** |
| Tema/builder | Porto + WPBakery | **WoodMart 8.5.4 + child** |
| SEO plugin | Yoast | **Rank Math** (Yoast obrisan 13.08) |
| Prefiks baze | `wpGs_` | **`wpgs_`** |

Nijedna izmena stack-a (tema u julu, Rank Math 05.08, brisanje Yoasta 13.08)
nije povukla ažuriranje ove reference. Isti razred problema kao `CLAUDE.md` §2,
koji je do 12.08 tvrdio pogrešan prefiks.

🔴 **Zašto je opasno:** za razliku od koda, ustajala referenca ne baca grešku —
agent je pročita, poveruje joj i **radi po njoj**. Pogrešan prefiks iz `CLAUDE.md`
je 13.08 umalo doveo do upisa u pogrešan meta ključ na 13 arhiva.

**Pravilo:** pri svakoj većoj promeni stack-a (tema, SEO plugin, prefiks, PHP,
verzija baze) osvežiti `reference/identifikatori.md` i `CLAUDE.md` §2 —
**provereno protiv sistema, ne iz sećanja**:

```sql
SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='antasline_local';
SELECT option_name, option_value FROM wpgs_options WHERE option_name IN ('template','stylesheet');
SELECT option_value FROM wpgs_options WHERE option_name='active_plugins';
```
(`wp eval` na ovom buildu pada na 300s timeout — koristiti direktan SQL.)
v. [[dnevnik/2026-08-14-copilot-grok-delegati]] · [[CLAUDE]] §2

## Cena u izlazu alata nije račun — prvo se proveri način autentifikacije (2026-08-14)

Grok je na trivijalan poziv vratio `total_cost_usd: 0.060434` i ja sam iz toga
zaključio „naplaćuje se ~$0,06 po pozivu" — upisao to u skill, u blokere i u
preporuku da se alat štedi **zbog novca**. M je rekao da nema nikakvu pretplatu.
Provera je pokazala da je bio u pravu: `XAI_API_KEY` **nije postavljen**
(autentifikacija je OAuth/OIDC), a log posle stvarnih poziva ponavlja
`subscription_tier: null` · `paywall_check_no_subscription` · `tier: "Free"`.

Broj je bio **očitavanje brojila po API cenovniku** — koliko bi ti tokeni koštali
preko plaćenog ključa. Grok dokumentacija to i kaže (`14-headless-mode.md`):
cena se štancuje za API-key saobraćaj, dok OAuth putanja obično ne nosi stvarnu
cenu. Na Free nalogu se troši kvota; kad se potroši → **odbijanje, ne račun**.

**Pravilo:** pre nego što se bilo čija potrošnja proglasi novcem, proveriti
**čime je autentifikovan** (`env` ključ vs OAuth) i **šta server prijavljuje o
tieru**. Polje `cost` u izlazu alata je metrika, ne faktura. Obrnuto takođe važi:
odsustvo cene ne znači besplatno.

🟢 Deo zaključka koji je preživeo: **token-težina je stvarna** (~23k ulaznih
tokena po grok pozivu, jer učita `CLAUDE.md` + `AGENTS.md` svaki put). Alat se
i dalje štedi — samo je razlog kvota, ne novac.
sanacija je rotacija (`logout` → `login`), ne brisanje ispisa.

## Delegat-agent ume da vrati uredan izveštaj nad nula fajlova (2026-08-14)

Copilot je na prvom `wpgs` auditu krenuo od glob obrasca sa tri ekstenzije
odjednom, dobio 0 rezultata i vratio **formatiranu, samouverenu tabelu**
„nema nalaza · pregledano 0 fajlova" — nad folderom od **89 fajlova**. Nijedna
greška, nijedno upozorenje. Ovo je najopasniji način otkaza jer izgleda kao uspeh.

**Obavezno u svakom prompt šablonu za delegata:**
1. „Prvo izlistaj ceo folder, pa TEK ONDA filtriraj po ekstenziji."
2. „Ako pretraga vrati 0 fajlova, to je greška TVOJE pretrage, ne prazan folder."
3. „U rezimeu navedi koliko si fajlova STVARNO otvorio."

**I posle toga:** tvrdnja o pokrivenosti se proverava nezavisnim grep-om, ne
prima na reč. (Drugi pokušaj: 3 NALAZ, sva tri tačna — ali je alat prijavio
20 otvorenih fajlova od 83 relevantna; pokrivenost je spasao grep koji je pustio
usput, ne otvaranje fajlova.)
v. [[dnevnik/2026-08-14-copilot-grok-delegati]]

## Zabrane za delegat-agente: dokumentacija nije dokaz, `inspect` jeste (2026-08-14)

Grok 1.0.3 **nađe** projektni `.grok/config.toml` i **ne primeni** dozvole iz
njega — `grok inspect` javi `Permissions: Source: (none), 0 loaded`, iako
dokumentacija izričito tvrdi da projektni opseg radi. Testirane obe forme
(`deny = [...]` i `rules = [{ action, tool }]`). Ista pravila u
`~/.grok/config.toml` → **19 loaded, 0 skipped**.

Isto važi za sandbox: `--sandbox` je Landlock/Seatbelt, dakle **Linux/macOS**.
Na Windows-u grok samo upiše upozorenje u log i **nastavi bez zaštite** — flag
koji izgleda kao zaštita, a nije.

**Pravilo:** svaka ograda za delegata se posle podešavanja **testira živo**
(traži od agenta da uradi zabranjenu stvar), a ne zaključuje iz dokumentacije.
Copilot je pri istom testu **pokušao pa bio blokiran** (mehanizam), Grok je
odustao na instrukciji iz `AGENTS.md` — što znači da mu `deny` sloj tada nije
ni bio isproban.

Uzgred, ista klasa: `.claude/settings.json` **čita i Grok**, ne samo Claude Code
— pravila za delegate tamo procure na alat koji sme da piše.
v. [[dnevnik/2026-08-13-5438-basket-semantika-faqpage]]

## Provera koja meri pogrešnu stvar je gora od nikakve provere (2026-08-13)

Dva puta u istoj sesiji je „crveno" došlo od merenja, ne od stranice:
`[al_skica]` je proveravan tražeći ime skice u renderu — a shortcode emituje
`<div class="al-skica-wrap">` + **inline** `<svg>`, ime se nigde ne pojavljuje; a kartice
su na 1440 px izmerene kao 67 px široke sa 11 odsečenih naslova, jer je iframe **prvo
učitan na 390 px pa proširen**, pa je layout ostao ustajao. Sveže učitavanje na 1440 daje
grid `280px ×4`.

Pravilo: pre nego što se prijavi regresija, proveriti da li provera meri ono što misli da
meri — i responsive merenja raditi **svežim učitavanjem na ciljanoj širini**, ne resize-om
- Isto važi za **stavke menija** (`_menu_item_object_id`) i dolazne linkove u `post_content`.

## Advanced Tables tiho naduva markdown fajl 3–4× razmacima (2026-08-13)
- Plugin `table-editor-obsidian` poravnava kolone **dopunom razmacima do širine najšire
  ćelije**. U tabeli sa ćelijama od 1–4 hiljade znakova to znači da se **svaki** red
  dopuni na tu širinu. PROGRESS.md je tako narastao na **1,4 MB, od čega 1,06 MB razmaka
  (75%)** — jedan niz razmaka bio je 4.209 uzastopnih.
- Šteta nije kozmetička: fajl koji CLAUDE.md §13 nalaže da se čita **prvi na svakoj
  sesiji** prestao je da može da se otvori Read alatom (106k tokena vs limit 25k), pa se
  čitao parcijalno kroz grep — isti razred greške koji je 12.08 doveo do pogrešnog izbora
  zadatka.
- Skidanje dopune: `sed -E '/^\|/ { s/[[:space:]]+\|/ |/g; s/\|[[:space:]]+/| /g }'` —
  **ograničeno na linije koje počinju pipe-om**, inače pokvari proznu rečenicu sa `|` u
  inline kodu (`cat part-* | tar -xzf -`).
- Dokaz da je promenjen samo razmak: `tr -d ' ' < fajl | md5sum` mora biti **isti** pre i
  posle. Za premeštanje redova između fajlova: `tr -d ' ' | sort | md5sum` nad unijom.
- Trajno gašenje: `formatType` → `"weak"` u `data.json`. 🔴 Ako je Obsidian pokrenut,
  izmena važi tek posle restarta, a dodirivanje podešavanja tog plugina u UI-ju vrati
  in-memory `normal` nazad u fajl.

## Literalan `|` u ćeliji markdown tabele lomi red, i to nevidljivo (2026-08-13)
- `^(llms(-full)?|robots)\.txt$` unutar inline koda u ćeliji: Obsidian tretira pipe kao
  granicu ćelije i prelomi ostatak teksta u fantomsku kolonu. Inline kod **ne štiti** od
  toga — escape mora biti `\|`.
- Otkriva se sa `awk -F'|' 'NF!=ocekivano {print NR}'`, ali pazi na **lažni pozitiv**:
  već ispravno escape-ovan `\|` i dalje sadrži pipe znak, pa ga brojač prijavi kao grešku.
  da se poredi. (Yoast 27.8: 2.308 unosa = 1.855 fajlova + 453 foldera.)

## Pravilo koje nadživi svoju odluku je aktivna mina, ne zastarela beleška (2026-08-13)
- Migracija Yoast→Rank Math izvedena 05.08, ali je „Yoast ostaje (ne RankMath)" ostalo kao
  **tvrdo pravilo** u 7 fajlova — uključujući `/antasline-sesija` i `/obogati-proizvod`, koji
  se učitavaju na početku svake sesije. Isti razred kao pogrešan prefiks `wpGs_` (12.08).
- Šteta nije teorijska: 13.08 je pri upisu meta opisa na 13 arhiva ključ zamalo bio
  `_yoast_wpseo_metadesc`. Upis u ključ mrtvog plugina **ne puca** — samo se tiho ne renderuje.
- **Pravilo: onaj ko izvrši migraciju alata isti dan pretražuje vault za starim pravilom**
  (`grep -rn -i "<stari alat>" --include=*.md` po `odluke/`, `.claude/skills/`, master planu,
  `reference/`) — dnevnički zapis da je migracija urađena nije zamena za brisanje pravila.
- Odluku **prepisati, ne obrisati**: stara ostaje vidljiva kao zamenjena, sa datumom i
- Potvrda u bazi je obavezna pre zaključka: `uploads/meni-ikonice/` ima 79 fajlova i 79 priloga, ali **0 referenci** iz `nav_menu_item` (`post_content` + `postmeta`). Fajlovi koji postoje ≠ fajlovi koji se renderuju.

## Ledger unos na dnu fajla je nevidljiv unos (2026-08-13)
- [[DNEVNIK-NAPRETKA]] je newest-on-top. Unos „FAZA 1 — Visual, Assets & Media Cleanup" (13.08) završio je **na dnu fajla**, iza unosa iz juna — posao je bio uredno zapisan, ali ga nijedno otvaranje sesije ne bi videlo, i nije stigao u [[PROGRESS]].
- Isti razred greške kao „Sledeće liste trule tiše od Urađeno" (12.08): dokumentacija koja postoji ali se ne čita jednaka je nepostojećoj.
- Pisati `> izlaz.txt` pa čitati fajl, ili pustiti bez `tail`. Isto važi za svaku dugu skriptu u ovom shell-u.

## Ista zamerka na 5 stranica je jedan uzrok, ne pet popravki (2026-08-13)
- M je prijavio 4 odvojene „prevelike praznine" na 4 stranice. Sve četiri su bile isti obrazac: **dve susedne `.al-section` istog tona** (`--paper`+`--paper` ili `--mist`+`--mist`) daju 72+72 = 144px jednobojne trake bez linije ili promene boje koja bi je opravdala. Uz to WPBakery `margin-bottom: 35px` na poslednjem bloku u sekciji i goli `<br>` iz `wpautop` (~18px).
- Popravka po stranici bi rešila 4 prijavljene i ostavila **15 spojeva na 14 stranica** (prebrojano SQL-om nad `post_content`) plus Woo kategorija stranice netaknutim. Popravka u dizajn sistemu rešava sve odjednom.
- Uz to (2026-08-12 lekcija o hladnom startu): posle podizanja Apache-a prvi zahtev traje 100s+, pa `curl` pre Chrome merenja.

## Skripta koja se pokreće samo na dan migracije mora imati način da se testira ranije (2026-08-12)
- `live-export.sh` je do 12.08 gubio **145 od 170** galerijskih slika (čitao `post_parent` + `_thumbnail_id`, nikad `_product_image_gallery`). Bag je preživeo mesecima jer se skripta pokreće **jednom, na dan migracije** — dakle prvi put bi se izvršila baš kad je cena greške najveća.
- Fix koji vredi više od samog baga: `PFX`/`OUT` se sada mogu pregaziti iz okruženja (`PFX=wpgs_ OUT=/tmp/t.sql bash live-export.sh`), pa se skripta vrti nad lokalnim buildom. **Tek to pokretanje je otkrilo tri dodatna baga** (ispod) koje čitanje koda nije videlo.
$//; /^[[:space:]]*$/d'; }`. Bezopasno na Linux-u.

## Ušteda tokena dolazi od AGREGACIJE, ne od lokalnog modela — pravila su pobedila qwen3 za 1300× (2026-08-12)
- Cilj je bio da lokalni Ollama model razvrstava GSC/Ads/GA4 podatke umesto mene. Izmereno na pravom izvozu (400 upita, 37 KB): **regex pravila razvrstala 93,6% prikaza za 0,2 s**, `qwen3:4b` dodao +2% za **475 s** uz ~50% grešaka na spornim upitima (`table za kos` → vinil, `tartan kocke` → veštačka trava).
- 🔴 Stvarna ušteda je bila u **sažimanju**: 37 KB sirovog JSON-a (~12k tokena) → 3,5 KB izveštaja (~700 tokena). To radi obična Python agregacija, bez ijednog poziva modela.
- Prava uloga malog lokalnog modela je **rudarenje pravila jednom**, ne klasifikacija svaki put — predlog korena reči se upiše u kod i od tada je besplatan zauvek. Skill: `[[ollama-lokalni]]`.
- Pouka šire: pre nego što se na problem baci model, proveriti da li je problem uopšte semantički. Ovaj nije bio.

## Ollama 0.18 sam bira `num_ctx` iz maksimuma modela — model od 3B traži 15 GiB i „ne staje u RAM" (2026-08-12)
- `llama3.2:3b` (2 GB) je pukao sa `model requires more system memory (15.3 GiB) than is available`. Izgleda kao premali RAM — nije. Ollama je uzeo modelov maksimalni kontekst (qwen3 = 262k) kao podrazumevani.
- Fix: **uvek eksplicitno slati `options.num_ctx`** (kod nas 8192). Sa tim isti model radi na ~15 tok/s.
- Druga polovina iste zamke: `num_predict` prekratak → odsečen izlaz → neispravan JSON → ceo poziv (minuti CPU vremena) bačen. `qwen3:4b` probije 2048 tokena već na 60 upita.
- Srodno sa ranijom lekcijom „Python `print` na Windows-u puca čim ćirilični izlaz ode u fajl (2026-08-11)" — ista klasa, drugi ishod: tamo pukne, ovde **tiho pokvari podatke**.

## Klasifikacija srpskih upita: koren reči koji zvuči očigledno ume da pojede tuđu kategoriju (2026-08-12)
- `odbojn` hvata i **odbojku** (sport) i **odbojnike za zid** (industrija) — rešeno sa `odbojnic` za industriju uz `odbojk` za sport.
- `odbojnik` ne hvata množinu `odbojnici` (58 prikaza je tiho padalo u „ostalo"). Isto: `industriski` bez `j`, `bastu` vs `basten`, `epox` vs `epoksid`.
- Ćirilicu treba **transliterisati pre poređenja**, inače latinična pravila ne vide ništa.
- Kad `ostalo` pređe ~10% prikaza, to je signal da **fale pravila**, ne da treba jači model.

## Delegirani agent (`agy`) bez apsolutnih putanja krene da pretražuje ceo `C:\Users\` — kvota ode na lutanje (2026-08-12)
- Prompt je rekao „foldere `dnevnik/` i `migracija/` ovog vault-a". `agy` je pokrenuo `Get-ChildItem -Path C:\Users\Miroslav -Directory -Filter "dnevnik" -Recurse -Depth 4` — dakle **tražio je folder umesto da ode na njega**. Potvrđeno u `~/.gemini/antigravity-cli/log/`.
- 🔴 Delegirani agent **ne nasleđuje tvoj radni direktorijum kao kontekst** ni kad je pokrenut iz njega. Prompt mora dati **pune apsolutne putanje + broj fajlova** i **izričito zabraniti** pretragu van njih („NE pretražuj C:\Users\Miroslav").
- Isto pravilo važi za bilo koji spoljni agent/CLI, ne samo `agy`.

## Headless agent koji ne može da pita za dozvolu — sam sebi odbije alat i vrati prazan izlaz (2026-08-12)
- `agy -p` (print mode) je vratio `no output produced — a tool required the "command" permission that headless mode cannot prompt for`. Izlazni fajl **0 bajtova**, exit bez greške — lako se pročita kao „model nije uspeo", a zapravo nikad nije ni pokušao.
- Dijagnoza je **isključivo u logu**: `~/.gemini/antigravity-cli/log/cli-*.log` → `permission check failed for command "..."`. Log pokaže i **tačnu komandu** koja je odbijena, što odmah otkriva i lutanje iz prethodne lekcije.
- Rešenje: `permissions.allow` u `~/.gemini/antigravity-cli/settings.json`. **Sintaksa `command(Get-ChildItem)` je potvrđeno ispravna** — posle nje je odbijanje prešlo na sledeći alat (`read_file`), što je dokaz da je prva kapija prošla.
- 🔴 `--dangerously-skip-permissions` **ne koristiti** (i Claude Code harness ga blokira): to je „odobri sve" nad vault-om pred migraciju. Fallback koji uvek radi: **TUI**, gde agent pita a čovek odobri.

## Isti model kroz TUI ispiše rezultat dvaput, a „lepa" tabela se raspadne posle ~10 redova (2026-08-12)
- Izlaz od 404 linije izgledao je kao da je posao pukao na pola („quota reached"). Nije: **TUI redraw ispiše ceo rezultat dvaput**, a obe kopije se završavaju istom stavkom. Provera potpunosti = uporediti poslednju stavku obe polovine, ne gledati dužinu.
- 🟡 **Druga kopija ume biti potpunija** od prve (imala je rečenicu o suspendovanom FTP nalogu koje u prvoj nema) — čistiti od druge, ne od prve.
- ASCII-uokvirena tabela se **prelomi posle ~10 redova** u sirov markdown sa polepljenim rečima (`pokrenutiauthorize_oauth.py`, `Izvršiti\RankMath\Sitemap\Cache::invalidate_storage()preko`). Sadržaj ostaje čitljiv, formatiranje ne. **Tražiti običnu markdown pipe-tabelu**, izričito zabraniti „lepu" tabelu.

## Audit koji „nalazi" probleme može samo da re-otkriva ono što već stoji u `naucene-lekcije` (2026-08-12)
- `agy` pre-flight checklist je izgledao kao serija novih otkrića (mu-plugins blokira mejlove, `*.bak-*` servira izvorni kod, 62 Redirection pravila nestaju sa bazom, OAuth pada na 7 dana, `wpgs_` vs `wpGs_`). **Svih pet su već bile zapisane lekcije** (2026-08-06 do 2026-08-11).
- 🔴 Pre nego što se nalaz spoljnog alata proglasi otkrićem — **grep-ovati `reference/naucene-lekcije` i `PROGRESS`**. Precenjen alat vodi ka pogrešnoj podeli posla.
- 🟢 Stvarna vrednost je bila realna, samo drugačija: **konsolidacija razbacanih lekcija u jedan izvršiv dan-migracije checklist + izvlačenje konflikata između fajlova**. To je posao koji niko nije uradio jer je dosadan i obiman, ne zato što je težak.

## Lekcija zapisana u `naucene-lekcije` ne znači da je `CLAUDE.md` ispravljen — prefiks baze je preživeo 6 dana (2026-08-12)
- Lekcija „prefiks je `wpgs_`, ne `wpGs_`" postoji od **2026-08-06**. `CLAUDE.md` §2 i §7.5 i dalje tvrde **`wpGs_`** — a `CLAUDE.md` je fajl koji svaki agent učitava kao autoritet, dok `naucene-lekcije` čita samo kad ga neko pošalje.
- 🔴 **Zapisati lekciju nije isto što i zatvoriti je.** Ako lekcija protivreči `CLAUDE.md`, `PROGRESS`-u ili nekom skillu, ispravka tih fajlova je **deo lekcije**, ne poseban zadatak. Inače greška živi dalje kroz svaku novu skriptu koja se piše „po dokumentaciji".
- Brojke iz starijeg plana (07-30: „67/81 proizvoda") bile su **zastarele za 13 dana** — obogaćivanje proizvoda ih je usput popunilo. **Obim se meri neposredno pre izvršenja, nikad ne prepisuje iz plana.**

## „Sledeće" liste truli tiše od „Urađeno" — zatvaranje zadatka mora ažurirati OBA mesta (2026-08-12)
- Predložen je (i prihvaćen) zadatak „W1 Polish Faza 4 — GEO-intro na 22 posta", koji je **bio zatvoren 5 dana ranije (2026-08-07, 22/22)**. Isto i master plan 1.2: stajao je na „12/33, sledeći kancelarije/padel" dok je red čekanja bio **33/33 od 2026-07-08** — zastareo mesec dana.
- Uzrok obrasca: sesija se zatvara upisom u **Urađeno** tabelu i u fajl reda čekanja, a red u **„Sledeće"** i statusna ćelija u master planu ostanu kako su bili. Urađeno raste i vidi se; „Sledeće" niko ne čita dok ne zatreba — a tada je pogrešno.
- 🔴 Cena nije samo izgubljeno vreme: pogrešan predlog deluje **verodostojno** jer dolazi iz zvaničnog izvora istine, pa se prihvati bez provere.
- **Pravilo: pri zatvaranju zadatka obavezno obrisati/preškrabati i njegov red u „Sledeće" i statusnu ćeliju u [[2026-07-06-MASTER-PLAN-V2]], ne samo dodati red u Urađeno.** A pri izboru zadatka na početku sesije: **status iz „Sledeće" se pre predlaganja proverava u fajlu reda čekanja** (jedan `grep`), nikad ne uzima zdravo za gotovo.
- **Pravilo za nov markup: nikad `border: 1px solid` bez boje.** Boja ivice se deklariše uvek, i u temi i u sadržaju.

## Browser automatizacija nad Google alatima: prvo proveri KOJI je nalog aktivan (2026-08-12)
- Otvaranje GSC-a preko Chrome automatizacije vratilo je „Упс, немате приступ овом производу" — Chrome je bio prijavljen na **`cpgujam@gmail.com`**, a property je pod `miroslav.markovic109@gmail.com`.
- 🔴 Opasnost je u pogrešnom zaključku: ta poruka lako se pročita kao „izveštaj/property nije dostupan" i završi u dnevniku kao nalaz, umesto kao pogrešan nalog. Isti obrazac važi za GA4, Ads i GMB UI.
- Rešenje: oba naloga su već bila prijavljena → prebacivanje kroz avatar meni, bez ikakvog unosa lozinke. **URL posle prebacivanja nosi `/u/1/`** — to je i najbrža provera da si na pravom nalogu.
- **Pravilo: pre bilo kakvog čitanja podataka iz Google UI-ja kroz browser, potvrdi aktivni nalog** (avatar ili `/u/N/` u URL-u), pa tek onda tumači ono što stranica prikazuje.
- Trošak greške je bio nizak jer je fajl statičan. Ista logika ne važi za konvencije koje traže izmene šablona, schema-e ili build procesa — tamo se čeka potvrda izvora.

## Skill građen iz JEDNOG izvora nasleđuje njegovu grešku — hub i [[PROGRESS]] se razilaze (2026-08-12)
- Novi `/antasline-ads` je pisan iz [[dnevnik/ADS-DNEVNIK]] (251 red istorije, deluje kao autoritativan izvor za Ads) i preuzeo je iz njega „kumulativ 26 plaćenih konverzija, prag 20–30 pređen".
- [[PROGRESS]] Blokeri od **11.08** to demantuju: `Klik na telefon (web)` ima `include_in_conversions_metric=True`, pa je **17 od 26** klik na telefon — pravih plaćenih lidova ima **9**. Ispravka je stigla u Blokere, ali **ne i u ADS-DNEVNIK**, čiji poslednji Log unos (11.08) i dalje tvrdi „prag pređen".
- Uhvaćeno tek pri zatvaranju sesije, kad je protokol naložio čitanje PROGRESS-a. Da nije, skill bi tu brojku ponavljao svaki put kad se pozove — trajno, i sa autoritetom „to piše u skillu".
- **Pravilo: pre nego što se brojka ili zaključak upiše u skill (koji se čita svaki put), ukrstiti izvor sa [[PROGRESS]] Blokerima.** Tematski hub beleži šta je tada izmereno; Blokeri beleže šta je u međuvremenu opovrgnuto. Kad se raziđu, Blokeri su noviji.
- Pravilo: pre „popravke" SEO propusta na staroj stranici, potraži ima li novijeg parnjaka u istom klasteru (viši ID, isti pojmovi u slug-u). Ako ga ima, propust je verovatno odluka.

## Provera koja vrati „0 nalaza" mora prvo dokazati da ume da nađe nalaz (2026-08-11)
- „Čisto" i „alat ne radi" izgledaju identično u izlazu. Ovo je već udarilo 10.08 dvaput u regression sweep-u (`strip_tags` lažni pozitiv, pogrešan regex delimiter) — tada u suprotnom smeru.
- Praksa: pre nego što se nula upiše kao rezultat, propustiti kroz istu proveru **bar jedan namerno pokvaren primer** (npr. `PodloÅ¾ni Ä‡ilim` za mojibake, `ko?arku` za izgubljenu dijakritiku). Ako ih ne uhvati, nula ne znači ništa.
- Uz to, u istom alatu: regex delimiter `#` sa znakom `#` unutar klase (`'#^(mailto:|tel:|#)#i'`) → „Unknown modifier ')'", filter tiho pada. Koristiti `~` kao delimiter.

## Pre nego što nešto upišeš u plan kao blokator — pretraži `woodmart-sabloni` (2026-08-10)
- Plan od 09.08 je vodio „lazy facade embed" kao 🔴 tehnički blokator za kačenje videa. Rešenje je stajalo u [[migracija/woodmart-sabloni]] pod **F7.3 od 2026-07-07** — CSS + globalni JS, radi na 9 stranica. Izgubljen ceo jedan zapis u planu i deo sesije na proveru nečega što je odavno gotovo.
- **Pravilo:** F-numerisane stavke u `woodmart-sabloni` pokrivaju više nego što se pamti (F7.1–F7.21+). Pre upisa „treba napraviti X" u bilo koji plan: `grep -i X migracija/woodmart-sabloni.md`.
- `maxresdefault.jpg` **ne postoji za svaki video** (2/8 u našem slučaju) — proveriti HTTP kodom pre nego što uđe u `thumbnailUrl`, i uvek navesti `hqdefault` kao rezervu.

## Gemini/Flow: `input[type=file]` postoji, ali tek POSLE otvaranja menija — plus Gemini žigoše klipove (2026-08-10)
- 🟢 **Ispravka ranije beleške.** Tvrdnja „Gemini nema `input[type=file]` u DOM-u (provereno JS-om), pa je image-to-video zatvoren za automatizaciju" je **netačna**. Input se **kreira dinamički tek kad se otvori odgovarajući meni**: `+` → „Направи видео" → ikonica slike ispod prompt polja. Tek tada `document.querySelectorAll('input[type=file]')` vraća pogodak i `file_upload` MCP alat radi normalno (5 MB JPG prošao bez problema). Isti obrazac u Flow-u: `+` → „Upload media".
- **Pravilo:** provera DOM-a na *zatvorenom* UI-ju daje **lažno negativan** rezultat. Nikad ne zaključivati „nema file input-a" iz jednog `querySelectorAll` pre nego što se meni otvori. Isto važi za `accept` atribut — prvi input koji se nađe (npr. Gemini „Отпреми фајлове" za dokumente) ima `accept` bez slika, što dodatno navodi na pogrešan zaključak.
- 🔴 **Gemini klipovi nose vidljiv „sparkle" vodeni žig** u donjem desnom uglu, kroz ceo klip. **Flow klipovi ga nemaju** (provereno poređenjem istog ugla kadra na 2 Flow klipa). Za materijal koji ide na sajt/Ads/YouTube: **Flow je izvor, Gemini rezerva.**
- **Trajanje se razlikuje:** Gemini vraća **10s** klipove (sa audio stream-om, koji se ionako odbacuje `-an`), Flow **8s**. Bitno pri računanju `xfade` offset-a u montaži.
- ⚠️ Kad Flow javi „You need more AI credits", **prvo proveriti koliko je sati** — reset je po pacifičkoj ponoći (v. lekcija ispod), a ne kraj kvote. Zaključak „besplatni nalog nema dnevne kredite" je izveden i **oboren u istoj sesiji**.

## Brend font za video natpise: Google Fonts woff2 podskupovi + varijabilni font — tri zamke u nizu (ffmpeg drawtext, 2026-08-10)
- Child tema drži brend fontove **samo kao `.woff2`** (`woodmart-child/fonts/`), a ffmpeg `drawtext` traži TTF/OTF. Konverzija je moguća bez instalacije: `fontTools.ttLib.TTFont(x.woff2)` → `font.flavor=None` → `save(x.ttf)`.
- 🔴 **Zamka 1 — podskupovi.** Google Fonts deli font na `latin` i `latin-ext`. **`latin-ext` sadrži SAMO dijakritiku** (č/ć/š/ž/đ), bez osnovnih slova. Ako se uzme samo taj fajl, ffmpeg iscrta **kutiju bez teksta** (ili samo jedno slovo) — a `box=1` i dalje radi, pa na prvi pogled izgleda kao da je problem u boji/alfi. **Uvek spojiti oba podskupa** (`fontTools.merge.Merger`).
- 🔴 **Zamka 2 — provera koja laže.** Provera „ima li font dijakritiku" prolazi i na golom `latin-ext` fajlu jer dijakritika tamo POSTOJI. **Proveravati ceo tekst koji se stvarno crta**, ne samo dijakritiku: `missing=[c for c in probe if ord(c) not in cmap]`.
- 🔴 **Zamka 3 — varijabilni font.** Inter woff2 je varijabilan; `Merger().merge()` puca sa `AttributeError: type object 'VarStore' has no attribute 'mergeMap'`. Lek: prvo `instancer.instantiateVariableFont()` pa merge. **Podrazumevana vrednost `wght` ose je 400 iako se fajl zove `inter-700`** — ako se ne zada `{"wght":700}` eksplicitno, dobija se tanak font pod imenom bold.
- **Uvek vizuelno proveriti rezultat** (`ffmpeg -ss T -frames:v 1` pa pogledati kadar) — tekstualni izlaz ffmpeg-a je „OK" i kad se ne iscrta nijedno slovo.
- ffmpeg 9.0 usput: **`-filter_complex_script` više ne postoji**, zamenjeno opštim `-/filter_complex fajl.txt` oblikom (čita vrednost opcije iz fajla) — nezamenljivo kad filter lanac ima navodnike i zareze koje shell inače izmrcvari.

## Google Flow — dnevni krediti se NE resetuju po lokalnoj ponoći (2026-08-10)
- U 00:34 po lokalnom vremenu (CEST, 22:34 UTC) nalog je i dalje pokazivao **0 kredita** iako je „datum" prešao u novi dan. Reset ide po Google-ovoj zoni (pacifička ponoć ≈ 09–10h ujutru po lokalnom), ne po korisnikovoj.
- **Praktično:** ne planirati „uveče potrošim, u ponoć nastavim". Dnevni budžet od 50 kredita je stvarno jedan po kalendarskom danu **pacifičke** zone.
- Usput: otvaranje starog Flow projekta ume da vrati `Application error: a client-side exception has occurred` (bela/crna stranica). Lek: otići na `labs.google/fx/tools/flow` (spisak projekata), ne direktno na `/project/<id>` URL.

## Google Flow (Veo 3.1) — agent se zaglavljuje, model se bira u promptu, Lite = Fast na sporim kadrovima (2026-08-09)
- **Agent se zaglavljuje**: dvaput je najavio render („I'm going to animate your photo…") i **nikad nije prikazao dugme za odobrenje** — sesija ostane mrtva, kredit se ne potroši ali ni video ne nastane. **Lek: otvoriti novu sesiju** (ikona olovke u panelu), ne nastavljati staru i ne guraje dodatnim porukama.
- **Agent settings → „Save" ne hvata izbor modela.** Prebacivanje na Veo 3.1 Lite kroz podešavanja dvaput nije ostalo sačuvano (agent je i dalje najavljivao Fast). Pouzdano je tražiti model **u samom tekstu prompta**: `Using the Veo 3.1 - Lite model, animate this exact photo…`.
- **Cene (izmereno, ne pretpostavka)**: 50 besplatnih kredita dnevno · **Lite 10** · **Fast 20** po klipu od 8s. Na sporim pokretima kamere nad statičnim objektom (teren, pod) **razlika u kvalitetu se ne vidi** → podrazumevano Lite, Fast samo za hero kadar. To je 5 klipova dnevno umesto 2.
- **Prompt pravilo koje čuva autentičnost fotke**: traži **samo pokret kamere i ambijent** (vetar, oblaci, svetlo) + eksplicitno „keep the surface, colours and markings exactly as in the photo" i „do not add any new objects, people or basketballs". Čim se zatraži radnja (igrač, lopta), Veo počne da izmišlja i podloga prestaje da bude naša realizacija.
- Ulaznu sliku **iskropovati na ciljani odnos (16:9) pre uploada** — Veo inače sam odlučuje šta da odseče.

## Gemini web UI nema `input[type=file]` u DOM-u — upload slike je zatvoren za browser automatizaciju (2026-08-09)
- Dopuna ranije lekcije „Gemini Veo nema free API tier — samo web UI" (2026-08-04): **ni web UI nije upotrebljiv za automatizovan image-to-video**. `document.querySelectorAll('input[type=file]')` na `gemini.google.com` vraća **prazan niz** — dugme „Отпреми фајлове" otvara **sistemski dijalog** koji automatizacija ne vidi ni ne može da popuni.
- Praktično: za image-to-video ide **Flow** (ima pravi file input, `file_upload` radi). Gemini ostaje korisna **odvojena besplatna Veo kvota** samo za tekst→video, gde izmišljen kadar nije problem.
- `resize_window` ne menja stvarni viewport iframe/tab sadržaja u ovom okruženju (poznato od ranije, W1 1.6) — za mobilni test i dalje treba `al-harness.html` (390px iframe harness, i dalje postoji lokalno, mora se obrisati sa produkcije pre migracije po W3 3.10 checklisti). Query param je `u=` (URL-enkodovana putanja), ne `url=`.

## Chrome browser automatizacija (Claude-in-Chrome): klik+type na neke web-app inpute ne registruje unos — proveriti pre Submit-a (GTM mailto tag, 2026-08-07)
- Prilikom popunjavanja GTM "Submit Changes" panela (Version Name/Description) preko `computer` tool-a, `left_click`+`type` je prijavljivao uspeh, ali `document.activeElement` je i dalje bio prazan `<div tabindex="-1">` i input vrednosti su ostajale prazne — simptom se poklopio sa Chrome extension prozorom koji je vraćao sumnjivo mali viewport (837×61) na screenshot-ima uprkos `resize_window` pozivu (verovatno privremen desinhronizovan render state u ekstenziji, ne bag ciljne web app).
- **Rešeno preko `javascript_tool`**: native setter (`Object.getOwnPropertyDescriptor(el.__proto__,'value').set`) da se React/Angular kontrola stvarno registruje kao promenjena, plus ručni `dispatchEvent(new Event('input'/'change', {bubbles:true}))` — bez toga framework ne vidi promenu iako je DOM `value` atribut fizički postavljen.
- **Pravilo ubuduće:** kad se sadržaj uvozi sa spoljnog izvora (proizvođač, stock, treća strana), poreklo i dozvola su DVA odvojena pitanja — postaviti oba eksplicitno, ne pretpostaviti da odgovor na jedno pokriva drugo. EXIF `copyright` polje na fotografiji (kad postoji) je koristan rani signal da dozvola nije data automatski.

## Gemini Veo (video) nema free API tier — samo web UI; DeepSeek/Groq nemaju regionalno ograničenje za Srbiju (AI orkestracija istraživanje, 2026-08-04)
- Gemini **slike** (`gemini-2.5-flash-image`, "Nano Banana") imaju solidan free API tier (~500/dan) i Srbija je zvanično podržan region — proxy/VPN nepotreban za foto rad.
- Gemini **Veo (video)** nema free API tier uopšte — besplatan video generisan Veo modelom postoji SAMO kroz Gemini app / Google Flow **web interfejs** (50 kredita/dan). Ne pokušavati video kroz API dok Google ne uvede free tier — pravi se lažna automatizacija koja pada.
- **Lazy-load slike nemaju `currentSrc`** dok ne uđu u vidno polje — pri merenju prvo `img.loading='eager'` pa `scrollIntoView()`, inače dobiješ prazne vrednosti i pogrešan zaključak.

## Claude-in-Chrome / browser automation
- 🔴 **Snimak ekrana ume da stigne PRE iscrtavanja** (2026-07-28) — ekstenzija je više puta vratila zastareo kadar: prozirna pozadina lightboxa (a `getComputedStyle` je pokazivao `rgba(11,20,32,0.94)`, `elementFromPoint` da overlay pokriva ceo ekran) i tamna kutija umesto učitanog video thumbnail-a (a `img.complete === true`, `naturalWidth 480`). **Dva puta je zamalo dovelo do „popravljanja" nepostojećeg baga.** Pravilo: **kad se snimak ne slaže sa izračunatim stilovima, prvo ponoviti snimak**, pa tek onda menjati kod. Odlučujući test ako i dalje sumnjaš: privremeno postavi svojstvo na neospornu vrednost (npr. puna crvena pozadina) i snimi — ako se promena vidi, iscrtavanje radi i problem je bio u snimku.
- **Ekstenzija po defaultu NEMA dozvolu za Incognito prozore** (2026-07-22, AI test sesija) — kad zadatak eksplicitno traži "bez naloga/incognito" test (npr. mesečni AI test u [[seo/geo-ai-plan]]), prvo proveriti da li nova kartica stvarno pokazuje prazno/odjavljeno stanje pre slanja bilo kakvog prompta — ako se vidi tuđa/postojeća istorija naloga, to NIJE Incognito, samo nova kartica u istom profilu. Fix: `chrome://extensions` → Claude-in-Chrome → "Dozvoli u anonimnom režimu" → korisnik otvori nov Incognito prozor (Ctrl+Shift+N) → tek onda kreni.
- Nikad ne pisati `<p>` tekst preko više redova sa tvrdim prelomom (`\n`) radi čitljivosti u editoru — `wpautop` pretvara svaki pojedinačni `\n` unutar paragrafa u `<br>`, pa se rečenica prelama na sredini na živoj stranici. Rešenje: jedan pasus = jedan kontinuirani red (bez wrap-a) u izvornom HTML-u koji se ubacuje u `post_content`. `<script>` blokovi (JSON-LD) nisu pogođeni — wpautop ih preskače.

## Claude Code ograničenje
- **Kredencijali van vault-a rade glatko u praksi**: `ANTASLINE_CONNECTOR_HOME` env var + fajlovi u `C:\Users\Miroslav\antasline-connector\credentials\` — nijednom nije bilo potrebe da tajna dotakne git stablo, čak ni privremeno, čitav tok (kopiranje service account ključeva, OAuth client secret, token.json posle autorizacije) prošao je kroz Bash/Read alate direktno na fajlsistem.

## WebFetch ne čita PDF sadržaj, ali sačuva fajl — koristiti Read na sačuvanoj putanji (Bergo Soft istraga, 2026-07-27)
- `WebFetch` na direktan PDF URL (proizvođački spec-list) vratio je samo opis PDF binarne/metapodatak strukture ("Adobe Illustrator, sv-SE locale...") umesto stvarnog teksta — mali/brzi model iza WebFetch-a ne parsira PDF stream sadržaj.
- Ipak, alat je sačuvao pun binarni fajl lokalno (putanja data u odgovoru, `tool-results/webfetch-*.pdf`) — `Read` alat na TOJ putanji je uspešno izvukao ceo čitljiv tekst (PDF podrška ugrađena u Read, ne u WebFetch).
popravki od 07.07 i 21.07.

## Gate stavka može stajati kao „pokriveno skriptom" a da skripta to nikad nije radila (backup „2 lokacije", 2026-08-17)

Checklist je od 10.08 tvrdio da backup builda ide **na 2 lokacije**. Kod je birao **jednu**
destinaciju (`G:` → OneDrive → lokalno), pa se serija razlivala: 10–12.08 na `C:`, 13–14.08 na
`G:`, OneDrive folder uopšte ne postoji. **Nijedan datum nije imao dve kopije.** Ista klasa
greške kao `build-staging-package.sh` 4 dana ranije (exclude pravila „pokrivena", a skripta
nikad pokrenuta).

**Pravilo:** kad checklist tvrdi da nešto radi automatski, dokaz je **artefakt na disku**, ne
red u dokumentu — nabroj fajlove i uporedi veličine. Ovde: dva fajla istog imena i **identičnih
2.946.948.322 B**, arhive otvorene i prebrojane (102.488 unosa), pa onda ✅.
🔵 Uz to: `Compress-Archive` drži celu arhivu u **memoriji** (~2,6 GB pika za 2,8 GB zip) i
upisuje na kraju — ciljni fajl stoji na **0 B** ~25 min, što izgleda kao da je posao stao.
🔴 I: rutina koja briše temp fajl samo na uspehu curi na svakom padu — zatečeno 13 dump-ova /
751 MB.
- **Kad se na jednoj stranici otkrije da su recenzije, cena i slika izmišljeni — ostatak istog bloka tretirati kao neproveren.** Ovde su `sku`/`mpn` ostali, ali označeni za potvrdu, ne prihvaćeni kao tačni.

## Referentni vault fajl koji "ne postoji" možda samo nije stigao još — proveri `git pull` pre nego što proglasiš blokerom (staging V4, 2026-08-13)
- cPanel prompt je referencirao `migracija/promptovi/2026-08-13-staging-full-restore-v4.md` (MD5 tabela, obavezna za integritetnu proveru pre raspakivanja) — fajl nije postojao u lokalnom kloniranom vault-u na cPanel serveru kad je sesija počela. `git fetch`/`git log origin/main` je pokazao da je commit koji ga dodaje stigao na GitHub tek posle početka sesije (Obsidian Git auto-sync na ~10 min, tri-surface workflow iz [[CLAUDE]] §9). `git pull` ga je povukao usred sesije.


## `Read` bez `limit` povlači 2000 linija — kod nas to zna biti 50k+ tokena iz jednog poziva (token audit, 2026-08-18)
- `DNEVNIK-NAPRETKA.md` je bio 988 KB / 6.320 linija. Jedan `Read` bez `limit`-a = prvih 2000 linija = **160 KB ≈ 52k tokena**, a na otvaranju sesije treba ~10 poslednjih unosa. Fajl koji staje u 2000 linija (`reference/naucene-lekcije.md`, 233 KB / 1.483 linije) ulazi **ceo** — ~75k tokena.
- Izmereno iz transkripata (`~/.claude/projects/<slug>/*.jsonl`, `usage` polje): dve od pet sesija su u prvih 12 poruka narasle **+65k i +70k**, a prvi `Read` pozivi su bili PROGRESS i master plan **bez limita** — master plan čak dvaput (prvo ceo, pa isti fajl sa `limit 150`; prvo čitanje čist gubitak).
- **Pravilo: pre `Read`-a na nepoznat fajl uraditi `wc -c`.** Preko ~40 KB → `head -N`, `sed -n 'OD,DOp'` ili `grep -rn` (kod `grep`-a u kontekst ulaze samo pogođene linije, bez obzira na veličinu fajla).

## „Append na kraj“ u append-only ledgeru koji je newest-on-top = tiho nevidljivi unosi (token audit, 2026-08-18)
- `DNEVNIK-NAPRETKA` je newest-on-top, ali su tri uputstva doslovno nalagala suprotno: `CLAUDE-CODE-instrukcija-CPANEL.md` („DODAJ (append) **na kraj**“), `CLAUDE-CODE-instrukcija.md` („red **na kraj**“) i `CLAUDE.md` §9.1 („→ append `[cpanel-live]` unos“). Rezultat: 4 unosa (06-23, 07-10, 07-30, 08-13) završila su na dnu fajla.
- Posledica nije bila kozmetička — unos od 13.08 je bio „praktično nevidljiv“ i **propušten iz PROGRESS tabele**, što je otkriveno tek naknadno. Ni `Read` ga ne bi našao: bio je na liniji 6291, a `Read` staje na 2000.
- **Pravilo: kad se popravlja simptom u podacima, potraži tekst uputstva koji ga proizvodi.** Rotacija bi sredila fajl, ali bi ga sledeći `[cpanel-live]` unos ponovo pokvario.
')`), ne po fajlu. Vault ima obe konvencije (136 LF : 47 CRLF : 1 mešan), `core.autocrlf=true` ih normalizuje tek pri commit-u.

## Pre renumeracije sekcija — inventar referenci sa kontekstom, ne globalna zamena (CLAUDE.md, 2026-08-18)
- `CLAUDE.md` je imao **dve sekcije numerisane 9** (WORKFLOW I ALATI i KLJUČNE LEKCIJE), a podsekcije workflow-a su nosile brojeve 8.1–8.7. Deset spoljnih referenci na „§9“ bilo je dvosmisleno u oba smera.
- Od 15 pogodaka na `§10` u vault-u, **samo 9 je gledalo u `CLAUDE.md`** — ostalo su bile reference na `/woodmart-theme` §13/§14, `chrome-web-platform-2026` §12 i „§6 ovog plana“. Globalna zamena bi ih polomila.
- **Postupak koji je prošao čisto:** (1) inventar `(fajl, linija, broj, kontekst)`, (2) razdvajanje živih uputstava od datiranih zapisa — zapisi se **ne** prepravljaju, (3) imenovane zamene sa `assert count == 1` po svakoj, (4) završna semantička provera: svaka živa referenca upoređena sa stvarnim naslovom ciljne sekcije, (5) mapa starih→novih brojeva ostavljena u samom dokumentu.
- Usput se ispostavilo da je jedna referenca bila pogrešna i **pre** renumeracije (`migracija/w1-polish-red-cekanja.md` je GEO pravilo pripisivao `CLAUDE.md` §10, a ono živi u `/antasline-sesija` W2) — inventar sa kontekstom hvata i takve, globalna zamena ne.
