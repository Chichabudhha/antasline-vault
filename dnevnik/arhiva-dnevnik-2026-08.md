# Dnevnik napretka — arhiva 2026-08

> Izdvojeno iz [[DNEVNIK-NAPRETKA]] rotacijom (`skripte/rotiraj-dnevnik.py`).
> **Ništa nije skraćeno ni prepisano** — unosi su preneti doslovno, sortirani
> newest-on-top. Pun tekst svake sesije je i dalje u `dnevnik/YYYY-MM-DD-*.md`.

## 2026-08-14 [claude-code] W3 — Prefiks baze `wpGs_` → `wpgs_` zatvoren u korenu ✅

M odobrio obe popravke iz Copilot nalaza. Rešeno u `wp-config.php` umesto po fajlovima —
ali izmena **nije jednodelna**: WordPress od prefiksa izvodi i ključeve koji se čuvaju kao
stringovi, pa je uz config preimenovano i **16 redova u bazi** (`wpGs_capabilities` ×4,
`wpGs_user_roles`, `wpGs_user_level` ×4, +7 kozmetičkih). 🔴 **Zamka:** kolacija
`utf8mb4_general_ci` je case-neosetljiva pa bi SQL provera dala lažno zeleno — ali WP meta
keš je PHP niz, gde je case bitan, i promašaj bi ostavio **sve korisnike bez ijedne dozvole**
(zaključan wp-admin). Verifikovano `wp user list` kroz pun WP stek: obe admin role netaknute,
HTTP 200 na tri stranice, 0 pogodaka na `wpGs_` u temi i mu-pluginima. Backup 36 MB pre izmene.
[[CLAUDE]] §2 ispravljen — tvrdio je da lokalni config nosi `wpGs_`.
✅ **Rep zatvoren istog dana (M: „sweep svih promptova"):** 13 fajlova ispravljeno — F1/F2/F3
promptovi, tri prompta koja gađaju Linux (uz dodatu obaveznu proveru prefiksa protiv dump-a),
master skill sesije i `reference/identifikatori.md`; istorijski zapisi namerno ostavljeni.
Usput uhvaćena **pokvarena provera** u staging promptu (`grep -v wpGs_` na Linux-u ne bi
filtrirao `wpgs_` tabele) i osvežen `identifikatori.md`, gde su **3 od 5 tvrdnji** o lokalnom
okruženju bile netačne (106 tabela → **78**, Porto+WPBakery → **WoodMart 8.5.4**, Yoast →
**Rank Math**).
→ [[dnevnik/2026-08-14-copilot-grok-delegati]]

---

## 2026-08-14 [claude-code] ALATI — Copilot CLI i Grok CLI kao read-only delegati ✅

Dva CLI alata instalirana 13.08 uvedena u posao uz tvrdu ogradu: nijedan ne menja fajlove
ni ne čita kredencijale, provereno živim testovima (Copilot **pokušao pa blokiran** na
`write` i `shell`; `git status` čist posle svih testova). Nov skill `/delegati` je router
za četiri delegata — Copilot za kod, Grok za drugo mišljenje, `agy` za markdown, `ollama`
za sirove izlaze. Prvi posao odmah našao **`wpGs_options` u dva sirova `mysqli` upita**
(`job-plugin-cleanup-cron.php:12,33`), verifikovano nezavisnim grep-om — ista klasa greške
koja je oborila probu migracije 21.07.
🔴 **Gotcha:** projektni `.grok/config.toml` grok 1.0.3 **nađe ali ne primeni**
(`0 loaded`) — zabrane morale u `~/.grok/config.toml`, dakle van gita. Uz to: grok sandbox
na Windows-u ne postoji, Copilot podrazumevano izvozi sesije na GitHub, a delegat ume da
vrati uredan izveštaj „pregledano 0 fajlova" bez ijedne greške.
🔴 **Revizija premise na kraju sesije:** oba delegata su **Free** — Copilot ~50 zahteva
mesečno (≈1,6 dnevno, testiranje potrošilo ~5), Grok bez naplate ali sa ~23k tokena po
pozivu. „Rasterećenje Claude kvote" time otpada; delegati su specijalisti za par pitanja
mesečno, Claude Code ostaje nosilac posla. Router prepisan po **oskudnosti**
(`ollama` → `agy` → Grok → Copilot), a `ollama` time postaje najvredniji jer je jedini bez kvote.
→ [[dnevnik/2026-08-14-copilot-grok-delegati]]

---

## 2026-08-14 [claude-code] W1/BLOK C — Ergonomske podloge: nova Woo kategorija + 8 proizvoda ✅

Izvršen spec od 13.08 (M odobrio obim, izvršenje bilo odloženo) — poslednji radni dan pre
content freeze-a. `product_cat` **403** + proizvodi **17838–17845** (Diamond Allround, Soft
Air Meter, SuperSoft Smooth/Office, La Ola, La Ola Hygienic, Nitrile Walk, Solido I), svi
„cena na upit", svaki sa `al-table` specifikacijom, 2 FAQ pitanja i FAQPage schemom. Hub
**16672**, koji do danas nije imao **nijedan** interni link osim `/kontakt/` i `tel:`,
prevezan: 8 kartica → linkovi, nazivi u tabeli poređenja → linkovi, uzajamne veze ka
`/industrijski-podovi/` i ESD stranici (7.329 → 9.600 B). Naslov kategorije namerno pomeren
ka „asortiman i modeli" da ne kanibalizuje hub (poz. 3,8). Verifikacija 12 URL-ova
200/1×H1/0 grešaka + 4 regresione stranice čiste.
🔴 **Gotcha:** `wp_insert_post` bez prijavljenog korisnika primenjuje kses i **tiho briše
`<script type="application/ld+json">`** — prvi prolaz je izgledao uspešno a schema nije
postojala; fix `kses_remove_filters()`.
🟢 **Tri pitanja za M zatvorena istog dana:** La Ola/La Ola Hygienic ostaju na generičkoj fotki
(ergomat.com 403, `intl.ergomat.com` mrtav → dopuna slika iz spec-a nije bila izvodljiva) ·
namene SuperSoft Smooth/Office ostaju kako su na hub stranici · kategorija se ne dodaje u meni.
→ [[dnevnik/2026-08-14-ergonomske-podloge-proizvodi]]

---

## 2026-08-13 [cpanel-live] staging.antasline.com V4 puno postavljanje (ručni upload) ✅

Docroot bio obrisan, fajlovi ubačeni ručno kroz File Manager — MD5 potvrđen (referentni
fajl stigao tek mid-sesije preko `git pull`), Basic Auth zatečen mrtav pa odmah vraćen
sa novom lozinkom (nije u vault-u), GTM ugašen pre klijentskog pregleda iste večeri,
14.316 URL zamena, KORAK 10 verifikacija 10/10 (uklj. namensku proveru 5438).
→ [[dnevnik/2026-08-13-staging-v4-puno-postavljanje]]

---

## 2026-08-13 [claude-code] W2/SEO — stavka E: `/sportske-podloge/` (5438) vraća basket-semantiku + FAQPage ✅

Svež GSC pull pokazao da basket klaster nosi **138 od 178 klikova (78%)** te stranice, a
WoodMart build je izgubio baš tu semantiku i nije pominjao planer; usput nađeno da FAQ
nema FAQPage schemu. Vraćene dve sekcije doslovnim live tekstom + cena-pitanje prepisano
u bukvalan GSC upit sa 39 klikova, schema građena parsiranjem vidljivog teksta:
10.328 → 15.129 B, render 8×H2 i 3 JSON-LD bloka. K1–K9 zeleni (uklj. Chrome 1440/390 px);
checkpoint-i uhvatili dva sistemska baga u sopstvenim skriptama (v. dnevnik).
→ [[dnevnik/2026-08-13-5438-basket-semantika-faqpage]]

---

## 2026-08-13 [claude-code] W2/SEO — treća FAQ stranica (17025) u hub, klaster zatvoren ✅

**Kontekst:** Nastavak prethodne stavke istog dana. M: i `/industrijski-podovi-najcesca-pitanja/`
ide u hub, 301 na `/industrijski-podovi/`. Time je **ceo FAQ klaster konsolidovan** —
nijedna zasebna FAQ stranica na ovu temu više ne postoji.

**Preneto na hub — 4 pitanja koja nije imao** (od 7 na 17025):
samostalna montaža (ubodna testera/cirkular + gumeni čekić, klik-sistem, uputstvo uz
isporuku) · **spoljašnja upotreba — odgovor je NE**, proizvođač ne preporučuje (negativan
kvalifikator koji odbija pogrešne upite, korisniji od ćutanja) · postavljanje preko
farbanog betona, keramike, tepiha ili vinila · kada je lepak potreban (tačkasto opterećenje,
izolovan izvor toplote, direktna sunčeva svetlost; preporučeno **Uzin MK92S**).
**Nije preneto** ono što hub već ima: viljuškari + tabela debljina, upotreba odmah po
montaži, priprema podloge. **Hub sada nosi 15 FAQ pitanja.**

**🔴 Zamka koja je uhvaćena na vreme:** istorijsko pravilo
`/home/industrijski-podovi-najcesca-pitanja/` sa **615 GSC pogodaka** ciljalo je 17025.
Draftovanje stranice bez prepravke pravila poslalo bi tih 615 na **404** posle migracije.
Pretočeno na hub u istom potezu. Isti razred greške kao 4 pravila sa 365 pogodaka uhvaćena
jutros pri konsolidaciji C/D/B — **draftovanje stranice uvek traži proveru da li je neko
istorijsko pravilo cilja.**

**Izvršeno** (`migracija/alati/job-faq-17025-konsolidacija-2026-08-13.php`):
1. 16567 — 4 nova pitanja
2. 16567 — **FAQPage JSON-LD se briše i gradi iznova** nad svih 15 pitanja (skripta odbija
   upis ako parsira manje od 15) — dogradnja postojeće schema-e nije opcija jer bi se
   vidljivi tekst i schema razišli
3. **17025 → draft**
4. **Meni stavka 17390 obrisana** — hub je u meniju već 2× (16706, 17371), pa bi
   prevezivanje dalo treći duplikat. Provereno da stavka nema podstavki pre brisanja
5. 301: nov red 17025 → hub · istorijsko pravilo (615) pretočeno. Draft **79 pravila**,
   svi ciljevi 200 · verifikator **0 duplikata / 0 petlji / 43-43 / 0 kolizija**

**Verifikacija:** hub **200** / 1×H1 / **1× FAQPage · 15 Question · 15 Answer** · sve tri
FAQ stranice **404** · meni bez mrtvih stavki · sitemap bez 17025 · 0 dolaznih veza.

**🔵 Lažna uzbuna pri regresiji:** `/podovi-za-magacine-i-hale/` vraća **301** — nije
posledica ovog rada. 16687 je **child stranica huba**, pa flat oblik uvek 301-uje na
`/industrijski-podovi/podovi-za-magacine-i-hale/` (200). Upisano u plan da se linkuje
ugnježden oblik.

**Backup:** `antasline_local_2026-08-13_pre-faq-17025.sql`. Tekst sva tri članka ostaje
u bazi kao draft. Plan postova dopunjen → [[seo/posle-live-postovi-izbor-industrijskog-poda]].

---

## 2026-08-13 [claude-code] W2/SEO — FAQ klaster „izbor industrijskog poda" konsolidovan u hub ✅

**Kontekst:** M: oba članka `/izbor-industrijskog-poda-tri-najcesca-pitanja/` i `-2` imaju
istu tematiku i sličan FAQ → odgovori na FAQ sekciju `/industrijski-podovi/`, oba na 301
ka hubu, zapisati obrađena pitanja i planirati zasebne postove po temi **posle migracije**.

**🔴 Klaster je bio od TRI stranice, ne dve** — treća je `/industrijski-podovi-najcesca-pitanja/`
(17025). Svež GSC pull pre bilo kakve izmene:

| URL | 90d | 12 meseci | Pozicija |
|---|---|---|---|
| 2622 `/izbor-…-tri-najcesca-pitanja/` | 94 / 0 | **128 prikaza / 0 klikova** | 24–80 |
| 3274 `…-2` (draft od 27.07) | 50 / 0 | **98 / 0** | 20–76 |
| 17025 `/industrijski-podovi-najcesca-pitanja/` | 0 / 0 | **4 / 0** | 34–76 |
| **16567 `/industrijski-podovi/`** | — | **16.417 / 410** | „industrijski podovi" **6,7** |

Sve tri gađaju isti upit koji hub drži na 6,7 — tri slaba izvora cepaju signal protiv
sopstvenog huba, uz **nula klikova u 12 meseci**. Konsolidacija ne gubi ništa.
⚠️ **Treća pogrešna brojka u nizu:** obrazloženje reda 17 u `redirect-mapa-FINAL.csv`
tvrdi „311 klikova / poz. 6,9 / CTR 4,92%" — nije potvrđeno ni na jednom prozoru
(isti razred kao `gsc_klikovi` u `parity-inventar.csv`, dva puta danas).

**🔵 Nalaz o sadržaju:** oba članka obrađuju **ista tri pitanja** (3274 je prepričan 2622),
a jedini dodatak 3274 — Ecotile **500/5 · 500/7 · 500/10** po opterećenju — **hub već ima**
u sekciji „Koja debljina za koju namenu?". Od 7 postojećih FAQ pitanja na hubu nijedno se
nije dupliralo sa člancima, ali su pokrivala isti teren u direktnijem obliku.

**Izvršeno** (`migracija/alati/job-faq-konsolidacija-2026-08-13.php`, probni pa `--write`):
1. **16567 — 4 nova FAQ pitanja** koja hub nije imao: okvir odluke (3 pitanja) · svež beton
   u novogradnji (sazrevanje do godinu dana, polaganje bez lepka i hidroizolacije) ·
   priprema u odnosu na premaze (glodanje/brušenje, nedelje prašine) · otkup starog poda
2. **16567 — FAQPage JSON-LD sa 11 pitanja**, koji hub **do sada uopšte nije imao**
   (provereno: 0 FAQPage u renderu pre izmene). 🟢 Schema se **gradi parsiranjem vidljivog
   teksta**, ne ručnim prepisom — inače se vremenom raziđu, što Google čita kao neusklađenost;
   skripta odbija upis ako parsira manje od 8 parova
3. **2622 → draft** (3274 već bio)
4. **17025** — jedina stranica koja je linkovala ka oba članka, obe veze prevezane na hub
5. 301: red 17 preusmeren sa 2622 na **hub**, dodat nov red za 2622 → hub. Draft
   **78 pravila**, svi ciljevi 200 · verifikator **0 duplikata / 0 petlji / 44-44 / 0 kolizija**

**Verifikacija:** hub **200**, 1×H1, **1× FAQPage · 11 Question · 11 Answer** · oba članka
**404** na buildu · 17025 i dalje 200 bez mrtvih veza.

**Plan za posle live-a** → [[seo/posle-live-postovi-izbor-industrijskog-poda]]: zapis šta su
članci obrađivali (tabela po pitanju) + **5 predloženih postova** sa ciljnim upitima i
obrazloženjem zašto nisu duplikati, uz mapu postojeće pokrivenosti (14 stranica) protiv koje
se svaki proverava. Redosled P1 → P4 → P3 → P2 → P5, ~40–60 min po postu.
🔴 U plan je upisano tvrdo pravilo: **GSC provera pre svakog posta** — ceo posao je nastao
iz kanibalizacije i ne sme je proizvesti ponovo.

**🟡 Ostaje otvoreno (#ceka-miroslav):** 17025 je treća FAQ stranica istog klastera sa
**4 prikaza / 0 klikova / 12 meseci**, a hub sada nosi punu FAQ sekciju sa schema-om. Nije
dirana jer nalog nije obuhvatao nju. Ako se gasi, 🔴 **istorijsko pravilo sa 615 pogodaka**
(`/home/industrijski-podovi-najcesca-pitanja/`) mora se pretočiti na hub, plus stavka
menija 17390.

**Backup:** `antasline_local_2026-08-13_pre-faq-konsolidacija.sql` (37,6 MB).
Tekst oba članka ostaje u bazi kao draft — nije izgubljen.

---

## 2026-08-13 [claude-code] W2/SEO — stavka A: čist slug `/ergonomske-podloge/` + nalaz da 8 tipova nema proizvode ✅

**Kontekst:** Poslednja `-2` stavka iz §1 analize. M: „prepravi u `ergonomske-podloge`,
301 na čist, prilog preimenuj" + zapažanje da stranica **prikazuje 8 tipova podloga a
nema link ka stvarnim proizvodima**.

**🔵 Ovo je `-2` druge prirode nego „preko starog parketa":** tamo je bio namerno napisan
drugi članak, ovde je **WP-ov automatski sufiks** jer je čist slug držao **prilog**
(attachment 12489). Nema konsolidacije sadržaja — samo se oslobađa slug. Zato je i cena
poteza drugačija: kod parketa jedna 301 selidba rangirane stranice, ovde ništa.

**GSC provereno pre diranja URL-a** (`gsc_page_queries.py`): 90d **1 prikaz / 0 klikova** ·
12 meseci **123 prikaza / 4 klika**, drži „ergonomske podloge" poz. **3,8** i „podloga za
stajanje" poz. **6,5**. 🔴 **Usput opovrgnut podatak iz `parity-inventar.csv`** koji toj
stranici pripisuje **110 klikova** — nije potvrđen ni na jednom prozoru (90d ni 12 mes.).
Isti fajl je i kod parket stavke nosio zastarele `lokal_id` vrednosti (postovi 15977/15967
ne postoje) — **`gsc_klikovi` kolona iz F1 baseline-a nije pouzdana, proveriti pre
oslanjanja.**

**Izvršeno** (`migracija/alati/job-slug-swap-ergonomske-2026-08-13.php`, probni pa `--write`):
1. **12489** (prilog) → `ergonomske-podloge-foto` — 🟢 `post_name` je slug attachment
   *stranice*, ne ime fajla, pa putanje slika ostaju iste
2. **16672** (stranica) → čist slug `ergonomske-podloge`
3. **16567** `/industrijski-podovi/` — jedini dolazni link u sadržaju prevezan
4. `redirect-mapa-FINAL.csv` +1 red · `redirect-mapa-HISTORIJSKI-65-FLAT.csv` red 44:
   istorijsko pravilo `/ergonomski-podovi/` (**160 pogodaka**) **pretočeno sa `-2` na
   čist cilj** — da nije, 160 pogodaka bi posle migracije išlo na 404
5. Draft regenerisan: **77 pravila**, svi ciljevi 200 · verifikator **0/0/45-45/0**

**Verifikacija:** čist URL **200**, 1×H1, sve slike 200 · `-2` **404** · sitemap nosi čist
URL · meni stavka 17388 je `post_type` tip (prati ID, preživljava) · regresija na 4
nevezane stranice čista.

**🔴 Glavni nalaz nije bio slug nego katalog:** osam tipova sa stranice — Diamond Allround,
Soft Air Meter, SuperSoft Smooth, SuperSoft Office, La Ola, La Ola Hygienic, Nitrile Walk,
Solido I — **ne postoji nijedan kao proizvod**, ni na buildu ni na live-u (pretraga po
naslovima kroz sve `product`/`page`/`post`). Telo stranice nema **nijedan** interni link
osim `/kontakt/` i `tel:`. Dakle nije propust u linkovanju nego rupa u katalogu.
`/brend/ergomat/` nije zamena — tih 27 proizvoda su odbojnici, DuraStripe trake i senzori.
Imamo: **specifikacije** (na samoj stranici), **slike za 7 od 8** (fali La Ola), **nijednu
cenu** u cenovniku, i **nijednu odgovarajuću `product_cat`** od 16 postojećih.
**M odobrio obim, odložio izvršenje** → [[migracija/w1-ergonomske-podloge-proizvodi]]
(nova kategorija + 8 proizvoda, cena „na upit", slike i podaci sa **ergomat.com**).
⚠️ Menja sadržaj → pre **NED 16.08** ako ide u produkciju 24.08, inače post-live.
🟡 U spec je upisano i upozorenje da namene za **SuperSoft Smooth/Office** na stranici
deluju zamenjene — ukrstiti sa ergomat.com pre upisa, ne prepisivati slepo.

**Backup:** `antasline_local_2026-08-13_pre-slug-swap-ergonomske.sql` (37,6 MB).
🟢 Time su **oba živa `-2` slug-a na buildu rešena** (6588 i 16672); treći (3274) je draft
bez URL-a.

---

## 2026-08-13 [claude-code] Vault higijena — PROGRESS.md 1,4 MB → 247 KB, jun+jul u arhivu ✅

**Kontekst:** M je primetio „puno praznog i čudne linije" u PROGRESS-u. Provera je našla
**tri odvojena problema**, od kojih je jedan bio i tihi radni trošak na svakoj sesiji.

**1. 75% fajla su bili razmaci — 1.061.601 od 1.408.706 bajtova.** Uzrok:
plugin **`table-editor-obsidian` (Advanced Tables)** poravnava kolone dopunom razmacima
do širine najšire ćelije. Sa ćelijama od 1–4 hiljade znakova svaki red je dopunjen na
~4.800 znakova; najduži pojedinačni niz razmaka bio je **4.209 uzastopnih**.
🔴 **Zašto je to bilo bitno, a ne kozmetika:** CLAUDE.md §12 nalaže da se PROGRESS čita
**prvi na svakoj sesiji**, a fajl se u tom stanju **nije mogao otvoriti Read alatom**
(106.490 tokena naspram limita 25.000) — moralo se grepovati po sekcijama, što je tačno
onaj razred greške koji je 12.08 doveo do pogrešnog izbora zadatka.
Uklonjeno `sed`-om **samo u linijama koje počinju pipe-om** (prozni redovi sa `|` u
inline kodu, npr. `cat part-* | tar -xzf -`, namerno preskočeni). Dokaz da sadržaj nije
dirnut: **MD5 fajla sa uklonjenim svim razmacima identičan pre i posle**
(`382d557a8a968f0f305bbc6c302b9aae`).

**2. Jedan red je stvarno bio slomljen u prikazu.** Linija 34 je nosila **neescape-ovan**
`|` unutar inline koda — regex `^(llms(-full)?|robots)\.txt$` — pa je Obsidian tretirao
pipe kao granicu ćelije i prelomio ostatak teksta u fantomsku 4. kolonu (padding ga je
usput razmakao na `? | robots`). Ispravljeno u `\|`; tačan oblik regexa potvrđen iz
[[DNEVNIK-NAPRETKA]] (unos o `.htaccess` charset fix-u), ne iz pogađanja.
⚠️ **Ispravka ranije tvrdnje:** prijavio sam dva slomljena reda — linija 79
(`cat parts \| tar -xz`) je **već bila ispravno escape-ovana**, brojač pipe-ova ju je
lažno prijavio jer `\|` i dalje sadrži pipe znak.

**3. Tabela „Urađeno" imala je 273 reda** (3 jun · 180 jul · 90 avgust), prosečno ~1,4 KB
stvarnog teksta po redu — pravi izvor veličine i posle skidanja razmaka. **183 reda
(jun+jul) izmešteno u [[dnevnik/2026-07-arhiva-progress]]**; bili su neprekidni na dnu
tabele, pa je razdvajanje bilo čisto. Junska 3 reda idu zajedno sa julom jer su starija —
ostavljanje juna u tabeli a jula u arhivi dalo bi hronološku rupu. U PROGRESS-u je na
njihovo mesto upisan pokazivač. **Provera: MD5 sortiranog skupa linija identičan pre i
posle razdvajanja** (`c61c6190e9f5a849ab3888cd98b155d9`) → nijedna linija nije izgubljena
ni izmenjena. Jedan julski red namerno ostavljen — stoji van tabele, u sekciji
„Istorijske stavke".

**Rezultat:** PROGRESS **1.408.706 → 246.692 bajtova** (417 linija), arhiva 158.386 B.
Fajl je ponovo ispod 256 KB limita Read alata; struktura naslova i raspodela kolona po
redovima nepromenjene.

**Podešavanje:** `table-editor-obsidian/data.json` → `formatType` **`normal` → `weak`**
(prestaje poravnavanje kolona dopunom, navigacija Tab/Enter ostaje).
🔴 **Obsidian je bio pokrenut (od 08:27) — izmena važi tek posle restarta**, a ako se u
međuvremenu dira bilo šta u podešavanjima tog plugina, in-memory stanje prepiše fajl
nazad na `normal`.

**Kopija pre izmene:** `scratchpad/PROGRESS.md.pre-depad` (nije u git-u).

---

## 2026-08-13 [claude-code] W2/SEO — čist slug za „preko starog parketa": 6588 preuzeo URL, 16613 ugašen ✅

**Kontekst:** Osma stavka dana. M je iz stavke 1 (URL higijena) postavio pitanje o
`sta-postaviti-preko-starog-parketa-ili-plocica-2` sa pretpostavkom da su dve verzije
u međuvremenu spojene u jednu stranicu — i tražio: **sadržaj sa `-2` stranice zadržati,
ali `-2` iz slug-a ukloniti**.

**Provera je oborila pretpostavku o spajanju** — dva različita članka, i na live-u i na
buildu, oba HTTP 200 sa različitim canonical-om:

| | 16613 (`…-plocica`) | 6588 (`…-plocica-2`) |
|---|---|---|
| Nastanak | 2022-07-23 | 2025-09-17 (prepis) |
| Dužina | 4.940 zn. | **8.041 zn.** |
| SEO title | „PVC podovi i podovi od vinila" | „Šta postaviti preko starog parketa ili pločica?" |
| Robots na buildu | noindex (od 30.07) | index |
| Sadržaj | uvod + Objectflor Clic LVT + R-Tek | isto **+ Ecotile + FAQ (4 pitanja) + galerija primera** |

Spajanje je zapravo **već bilo izvedeno u smeru `-2`**: 6588 pokriva sve iz starog članka
u boljem obliku (stari nosi i tipfelere: „posojeći", „gradjevinskih", „sisitemom"). Jedino
što se gubi gašenjem 16613 je ciljanje fraze „PVC podovi i podovi od vinila" iz njegovog
SEO title-a — **132 prikaza / 5 klikova / 90d**.

**🔴 Ovo ukida odluku od 2026-07-30** (`redirect-mapa-FINAL.csv` red 18), koja je na osnovu
GSC preseka 01.01–27.07 (`-2`: 3.353 impr / **258 kl.** / poz. **5,5** nasuprot 1.667 / 84 /
7,6) zaključila da `-2` ostaje i da čist URL ide na 301. Smer je sada obrnut na M zahtev.

**🟢 Olakšavajuća okolnost koja je promenila procenu rizika:** cilj 301 **nije nov URL** —
`/…-plocica/` živi na produkciji od 2022, indeksiran je i nosi svojih 84 klika/god. Znači
konsolidacija dva Google-u poznata URL-a, ne selidba rangirane stranice na praznu adresu.
Posle migracije oba live URL-a ostaju ispravna: čist servira direktno, `-2` ide na 301.

**Izvršeno** (`migracija/alati/job-slug-swap-parket-2026-08-13.php`, probni prolaz pa `--write`):
1. **16613** → `draft` + slug `…-plocica-original-2022` (oslobađa čist slug)
2. **6588** → slug `…-plocica` (bez `-2`)
3. `redirect-mapa-FINAL.csv` red 18 → smer okrenut, obrazloženje prepisano
4. `htaccess-301-generate.php` → draft regenerisan, **76 pravila, svi ciljevi 200**;
   linija 34 sada `RedirectMatch 301 "^/…-plocica-2/?$" /…-plocica/`
5. `redirect-verify.php`: **0 duplikata · 0 petlji · 45/45 ciljeva 200 · 0 kolizija**
6. `parity-inventar.csv` (red `-2` → 301-KANDIDAT, čist red dobio `lokal_id` 6588),
   `regression-pages.csv` (URL za sledeći sweep)

**Verifikovano:** čist URL **200** sa novim sadržajem, 1×H1, `index` + prava Rank Math meta ·
`-2` **404** na buildu (301 ga hvata posle migracije) · `post-sitemap.xml` nosi čist URL,
31 URL nepromenjeno.

**🔴 Gotcha — zašto `$wpdb->update`, a ne `wp_update_post`:** `wp_unique_post_slug()` bi na
6588 zatekao slug koji drugi post drži i **tiho vratio `-2` nazad** — tačno ono što se
uklanja. Iz istog razloga je redosled obavezan: prvo 16613 pusti slug, pa ga 6588 uzme.

**🔴 Gotcha — Rank Math sitemap keš ne zna za direktan SQL upis:** posle zamene je
`post-sitemap.xml` još 15-ak minuta servirao stari `-2` URL. Hook-ovi ne okidaju pri
`$wpdb->update`, pa keš treba ručno oboriti:
`\RankMath\Sitemap\Cache::invalidate_storage()`. Isto važi za svaku buduću izmenu slug-a
ili statusa izvedenu direktnim upisom.

**🔵 Usput zatvoreno:** nedoslednost „5455 draftovan, 16613 publish+noindex" iz [[PROGRESS]]
Blokera — 16613 je sada draft, pa `redirect-verify.php` više ne prijavljuje upozorenje
(sekcija 5: 0 kolizija).

**🔵 Nova razlika koju treba držati na umu:** dva `-2` slug-a na buildu nastaju iz različitih
uzroka — `ergonomske-podloge-2` je WP-ov automatski sufiks jer slug drži **prilog** (čist
slug je besplatan, stavka A), a `…-plocica-2` je bio **namerno drugi post**. Prvo je
higijena, drugo je odluka o saobraćaju.

**Backup:** `antasline_local_2026-08-13_pre-slug-swap-parket.sql` (37,6 MB).
**Ostaje otvoreno pre freeze-a (16.08):** stavke **E** i **F** iz analize.
Detalji: [[seo/2026-08-13-kanibalizacija-konsolidacija-plan]] §1.

---

## 2026-08-13 [claude-code] W2/SEO — kanibalizacija: analiza 9 klastera + tri konsolidacije (C/D/B) ✅

**Kontekst:** Sedma stavka dana. M-ova lista od 9 tačaka (URL higijena, Ads smernice,
6 klastera kanibalizacije, meni „Cene", live alignment `/sportske-podloge/`) — obim veći
od jedne sesije i **menja sadržaj**, pa 3 dana pred freeze po pravilu analiza → predlog →
odobrenje → izvršenje. Podaci: GSC 90d po stranici (`gsc_page_queries.py`), Ads API
(`ads_final_urls.py`, svež pull — usput **potvrđeno da je OAuth token živ**, stavka iz
checkliste B1), lokalna baza.

**🔴 Metodološki nalaz koji menja ceo okvir:** sve „cena" i „dimenzije" stranice imaju
**0 GSC prikaza jer ne postoje na live-u** (napravljene na buildu u julu). Kanibalizacija
se ne može *meriti* — samo *predvideti* za 24.08.

**🔴 Najveći rizik nije bio na listi:** post 2298 (`kako-napraviti-teren-za-basket`) nosi
**13.686 prikaza / 385 klikova / 90d** i drži poz. **1,0–1,9** za „dimenzije košarkaškog
terena", „dimenzije table za koš", „dimenzije fudbalskog terena" (2.174 prikaza). Build
izbacuje **4 nove stranice na tačno te upite** (16585/16586/16688/17027) — `index`, **bez
canonical-a**, i **nijedna ne linkuje ka 2298** ni obrnuto. **Ostaje otvoreno** (stavka F,
M nije odobrio).

**🔴 `/sportske-podloge/` je na buildu izgubila sadržaj koji nosi klikove:** live drži poz.
**1,6** za „podloga za košarkaški teren" (47 kl.) i **2,0** za „…cena" (39 kl.) — skoro
polovina od 178 kl./90d dolazi iz basket klastera, a H2 „Izgradnja sportskih terena za
basket u vašem dvorištu" na buildu **ne postoji**; 5438 ne pominje ni `/planer-terena/`.
**Ostaje otvoreno** (stavka E).

**Izvršeno (M odobrio C+D+B)** — `migracija/alati/job-konsolidacija-301-2026-08-13.php`:
**C** Parkiralište — cenovni sadržaj sa 16876 preseljen u **16589** (1.197 prikaza / 98 kl.,
poz. 1,0–1,8): H2 „Cena podloge za parkiralište po m²" + tabela 4 modela + „saće ili nasut
šljunak" + 2 FAQ stavke (i vidljivo i u JSON-LD, 4→**6** pitanja); 16876 → draft (live 404,
301 ne treba). **D** Maloprodaja — 16683 → draft, primarna **16142** (live URL + Ads
odredište + 2× duža). **B** Bergo Easy 16665 → draft (proizvod diskontinuiran), sadržaj u
**16663**: +5 event namena, **8 event fotografija**, title/meta preuzeli „manifestacije,
sajmove i promocije". `htaccess-301-DRAFT.txt` **75 → 77 pravila**.

**Ostali nalazi:** `-2` slugova ima **3**, od toga 1 draft, a `…plocica-2` je **pobednička**
verzija (249 prikaza/13 kl.) — ne dira se; jedini kandidat je `ergonomske-podloge-2`
(1 prikaz), gde `-2` postoji jer slug drži **prilog**, ne stranica. Ads: `tracking_url_template`
**null na svih 14 kampanja** ✅, ali 3 oglasa + 2 asseta vode na **tuđi domen
`ekopodneploce.rs`**, 11 URL-ova na mrtve `/home/…` putanje, 4 na `http://` — sve u
PAUZIRANIM kampanjama, blokira **reaktivaciju**, ne 24.08.

**Gotcha-i:** 🔴 **nova `.al-section` namerno nije pravljena** — na obe stranice bi novi blok
pao između `paper` i `mist` i dao 144px mrtve trake (FAZA 2, isti dan); sadržaj ubačen
**unutar postojećih sekcija**, 0 CSS izmena · 🔴 **FAQPage JSON-LD je inline u
`post_content`**, ne u Rank Math meta — dodata FAQ stavka mora u oba mesta, inače se schema
i vidljiv tekst tiho raziđu · 🔴 `post_content` se čita/piše **direktno preko `$wpdb`**
(`get_post_field()` u `display` kontekstu pušta `wptexturize` koji obori `str_replace`) ·
🟡 **draftovanje stranice ostavlja mrtve stavke u meniju** — `nav_menu_item` ne prati status
cilja; nađene 4 (jedna draftovana, tri prevezane) · 🟢 skripta broji pogotke svakog obrasca
i puca ako ih nije tačno 1 — **0 promašaja**.

**Verifikacija:** 7 URL-ova **200 / 1×H1 / 0 PHP grešaka**, oba JSON-LD bloka na 16589
validna (FAQPage 6 pitanja + Rank Math `@graph`) · 3 draftovane **404** ✅ · **0** preostalih
veza ka draftovanim URL-ovima u celoj bazi i **0** stavki menija · 8 preseljenih fotografija
**200** · `wpautop` nije razbio tabelu · regresija (početna, kontakt, industrijski-podovi,
conquest 2542) 200.

**Backup:** `antasline_local_2026-08-13_pre-konsolidacija-301.sql` ·
Detalji: [[dnevnik/2026-08-13-konsolidacija-kanibalizacija]] ·
Analiza: [[seo/2026-08-13-kanibalizacija-konsolidacija-plan]]

---

## 2026-08-13 [claude-code] W3 3.10 — dry-run `build-staging-package.sh`: 2 skrivena kvara + kvota ne staje ✅

**Kontekst:** Šesta stavka dana, jedina 🔴🆕 otvorena stavka iz [[PROGRESS]] „Sledeće".
Skripta poslednji put pokrenuta **06.08**, a **10.08** su joj dodata dva exclude pravila
koja **nikad nisu izvršena** — a preflight rizici **#1 i #4 (🔴🔴)** oslanjaju se baš na
njih. **Read-only prema buildu i bazi** (0 izmena na `C:\xampp\htdocs\antasline`, nema
backup fajla); paketi napravljeni u scratchpad-u i obrisani po završetku.

**Nalaz 0 — skripta se nije mogla ni testirati:** `WP_ROOT`/`OUT_DIR` hardkodirani, pa
dry-run nije mogao van produkcione izlazne fascikle. Tačan razlog zašto nije testirana
posle 10.08. Popravljeno kao `PFX`/`OUT` u `live-export.sh` (12.08) — pregazivi preko
okruženja, podrazumevane vrednosti nepromenjene.

**Nalaz 1 — exclude pravila od 10.08 RADE ✅ (rizici #1 i #4 zatvoreni).** Pun `full`
prolaz, `tar -tzf` nad arhivom (22.936 unosa): `al-local-mail-log.php` ❌ · `mail-log.txt`
❌ · **32** `.bak-*`/`.orig`/`.old`/`~` fajla sa builda ❌ nijedan (checklist beleži 27 od
10.08 — porastao za 5 iz današnje FAZE 2 i Aria fiksa) · `al-harness.html`+`harness390.html`
❌ · ~20 debug/import PHP skripti ❌ · `wp-config*.php` ❌ · `uploads`/`.git`/`.claude`/`*.sql`
0 unosa · Yoast (obrisan danas) ❌. Pozitivno: 2 teme, 10 plugina, tačno 2 ispravna
mu-plugina, 16 root fajlova.

**Nalaz 2 🔴 — `.htaccess` je bio u paketu i oborio bi produkciju.** Lokalni nosi
`RewriteBase /antasline/` + `RewriteRule . /antasline/index.php` (build je u podfolderu):
na produkciji bi **svaki zahtev** otišao na nepostojeću putanju, uz gubitak produkcijskog
`# BEGIN LSCACHE` bloka (na kome visi ceo LCP plan). Checklist **B3** ionako kaže da se
301 blok **dodaje** u serverski `.htaccess` — fajl se na serveru edituje, ne prenosi iz
builda. Izbačen iz root whitelist-e.

**Nalaz 3 ✅ — chunk+md5 ispravan.** 136 delova (uploads) + 4 (kod), `md5sum -c` 4/4,
`cat part-* > tar` daje **bajt-identičan** md5 originalu.

**Nalaz 4 🔴 — paket je 2× veći nego što pre-flight računa, kvota ne staje.** Kod
**72,3 MB** + uploads **2.706,9 MB** = **2.779,2 MB**; uploads na buildu je 2,9 GB i
praktično se ne kompresuje. Disk-bloker je zatvoren jutros računicom „~1,3 GB paket +
~1,3 GB backup ≈ 2,6 GB" — **stvarni paket je 2,7 GB sam za sebe**. Slobodno na serveru
**5.867 MB**. Naivan tok (delovi + sklopljen tar istovremeno) = **5.558 MB** → ostaje
309 MB pre backup-a i pre raspakivanja → **ne staje**. Disciplinovan tok (backup skinut
i obrisan pre uploada · streamovati `cat part-* | tar -xzf -` bez sklapanja · brisati
delove u hodu) → pik **~4,4 GB** ✅. Prva preporuka za 24.08: **rsync/scp preko SSH-a**
(pristup potvrđen M6, 21.07) — FTP chunking je bio zaobilaznica za nestabilnu
data-konekciju, ne zahtev hostinga.

**Gotcha (nova lekcija):** prvi prolaz je pukao na `ploads: command not found` jer sam
editovao `.sh` **dok se izvršavao** — bash čita skriptu inkrementalno po bajt-ofsetu,
izmena pomeri ostatak i raspolovi komandu. Nije kvar skripte. Pogoršava to što je proces
izašao sa **kodom 0** uprkos `set -euo pipefail`. Drugi, čist prolaz prošao end-to-end.

**Dopuna iste sesije (M: „izmesti je van vaulta"):** FTP lozinka izmeštena iz vault-a —
nađena u **dva** fajla, ne jednom (`ftp-upload-chunks.sh` l. 8 + `ftp-upload-resume.sh`
l. 7, oba verzionisana od 06.08). Sada u `C:\Users\Miroslav\antasline-ftp-creds.txt` (van
repo stabla), obe skripte je `source`-uju preko `FTP_CREDS_FILE` i **padaju sa `exit 1`**
pre ijednog poziva ako fajla nema. Usput izvučeni hardkodiran host i hardkodiran naziv
arhive od 06.08. Verifikovano: `grep` po radnom stablu ne nalazi lozinku, specijalni
znakovi (`$$`, `&^`) očuvani pri učitavanju, `bash -n` prolazi na oba.
🔴 **Ne briše je iz git istorije** — jedina prava sanacija je **promena FTP lozinke u
cPanel-u**, preporučeno **posle** 24.08 (ne dirati kanal prenosa pred prenos); rewrite
istorije se ne preporučuje (tri površine + Obsidian Git auto-sync). #ceka-miroslav

**Otvoreno:** 🔴 disk prostor se reotvara kao rizik (redosled koraka na dan migracije) ·
🟡 promena FTP lozinke posle migracije #ceka-miroslav · 🔵 `antasline-staging-upload\` drži 5,67 GB zastarelih
artefakata od 06.08.

**Detalji:** [[dnevnik/2026-08-13-dry-run-build-staging-package]]

---

## 2026-08-13 [claude-code] DOKUMENTACIJA — SEO plugin pravilo prepisano: Rank Math jedini, Yoast van upotrebe (M odluka) ✅

**Kontekst:** Peta stavka dana, na M zahtev („Yoast je obrisan, Rank Math ostaje").
Zatvara konflikt **#1** iz [[migracija/2026-08-12-preflight-checklist-24-08]] §Konflikti.
**Samo dokumentacija** — 0 izmena na buildu/bazi, nema backup fajla.

**Problem:** migracija Yoast→Rank Math izvedena je **05.08** ([[CLAUDE]] §7.1), ali je
pravilo „Yoast ostaje (ne RankMath)" ostalo zapisano kao **tvrdo pravilo** u 7 fajlova
punih 8 dana — uključujući dva skila koja se učitavaju na početku svake sesije. Isti
razred aktivne mine kao pogrešan prefiks `wpGs_` (zatvoren 12.08): agent čita pravilo
kao autoritet i nasleđuje grešku. Već je jednom skoro udarilo — 13.08 pri upisu meta
opisa na 13 arhiva ključ je zamalo bio `_yoast_wpseo_metadesc`.

**Ispravljeno (7 fajlova, 12 mesta):**
- `odluke/_pregled-odluka` — odluka **prepisana**, ne obrisana: naslov „SEO plugin —
  Rank Math (Yoast van upotrebe)", stara odluka od 28.06 ostaje vidljiva kao zamenjena,
  uz razlog i zatečeno stanje fajlova
- [[CLAUDE]] §7.1 — nova 🔴 ograda sa M odlukom 13.08; uklonjena rečenica „Yoast je
  deaktiviran (ne obrisan — podaci ostaju za rollback)"
- [[2026-07-06-MASTER-PLAN-V2]] — §„Pravila koja važe kroz ceo plan" + W2 zaglavlje
  (`Yoast >80` → Rank Math SEO score >80)
- `/antasline-sesija` — tvrdo pravilo §6, W2 pravila po stranici, verifikaciona stavka
- `/obogati-proizvod` — tačka 5 (upis mete), gotcha blok, verifikacija, opis skila;
  dodata i `is_protected_meta()` zamka za `rank_math_*` ključeve (nemaju `_` prefiks →
  tihi prazan upis) i `\RankMath\Sitemap\Cache::invalidate_storage()` umesto
  `wpgs_yoast_indexable` brisanja
- `migracija/woodmart-sabloni` — helper `al_set_page()` je dokumentovan kao da piše
  **Yoast** title/metadesc; svaka nova stranica napravljena po tom opisu ostala bi bez
  mete koja se renderuje
- `reference/claude-skilovi`, `seo/plan-novih-stranica` — ista dva pravila

**Namerno NIJE dirano:** dnevnici, [[reference/naucene-lekcije]], W7 nalazi i analize —
tamo je Yoast tačan istorijski podatak o periodu kad je važio. Prepisivanje istorije bi
uništilo traživost uzroka starih bagova.

**🔴 Nalaz — Yoast NIJE obrisan, samo deaktiviran:** provereno protiv builda pre izmena,
`wp plugin list` → `wordpress-seo` **inactive, v27.8** (aktuelna 28.2), folder
`wp-content/plugins/wordpress-seo` **21 MB** na disku, `_yoast_wpseo_*` postmeta i dalje
u bazi; aktivan je `seo-by-rank-math` **1.0.275**. Bez odluke, 21 MB mrtvog plugina ide
u migracioni paket 24.08. Upisano kao nov bloker u [[PROGRESS]] sa tri opcije
(preporuka: `wp plugin delete wordpress-seo` uz backup baze pre pakovanja — postmeta
ostaje, pa je povratak i dalje moguć re-instalacijom).

**✅ Izvršeno isti dan (M: „obriši, ali ostavi da može da se vrati"):**
1. Backup baze: `antasline-backups/antasline_local_2026-08-13_pre-yoast-brisanje.sql` (37,7 MB)
2. Arhiva plugina: `antasline-backups/yoast-wordpress-seo-27.8_2026-08-13.tar.gz` (4,0 MB),
   integritet potvrđen **pre** brisanja — `tar -tzf` daje **2.308** unosa = 1.855 fajlova +
   453 foldera, tačno koliko `find` broji u samom folderu
3. `rm -rf wordpress-seo` — 🔴 **namerno ne `wp plugin delete`**: WP-CLI-jev `delete_plugins()`
   poziva uninstall rutinu plugina, koja sme da briše i podatke iz baze; `_yoast_wpseo_*`
   postmeta (**690 redova**) je uslov da arhiva uopšte vredi, pa baza nije smela da se dira
4. Verifikacija: `wp plugin list` → samo `seo-by-rank-math` **1.0.275 active** · 6 stranica
   (početna, `/kontakt/`, `/industrijski-podovi/`, Woo kategorija, proizvod, conquest 2542)
   **200 / 1×H1 / `<meta name="description">` u `<head>` / 0 PHP grešaka** · na proizvodu i
   dalje 2 `application/ld+json` bloka · `sitemap_index.xml` 200 sa **7 child-ova**
   (nepromenjeno, parity sa live) · `rank_math_*` postmeta **16.312** redova netaknuto

**Praktična posledica za 24.08:** 21 MB mrtvog plugina manje u migracionom paketu.
🔵 Arhiva je u `antasline-backups/` — **van git-a** (odluka 13.08 o `.gitignore`), dakle
postoji samo na lokalnom disku. Postupak povratka: [[odluke/_pregled-odluka]] §SEO plugin.

---

## 2026-08-13 [claude-code] W3 3.10 — Pun regression sweep posle FAZE 2 (239 str.): 0 regresija; −118 slika/str. objašnjeno, 301 mapa reverifikovana ✅

**Kontekst:** Četvrta stavka dana. FAZA 2 je popravljala u dizajn sistemu (CF7 part
na ~55 stranica, ritam sekcija na 14 + Woo kategorije, 17× `al-display--lg`), a
verifikovano je bilo 32 URL-a; pun sweep nije puštan od **10.08**, a u međuvremenu
su prošle i FAZA 1 i ceo 12.08. **Read-only** — 0 izmena na buildu/bazi, nema backup fajla.

**Sweep (239 stranica · 1.158 slika · 1.801 link):** status≠200 **0** · bez H1 **0** ·
2×H1 **0** · nevalidan JSON-LD **0** · slomljene slike **0** · bez `<title>` **0** ·
problematičnih linkova **1** (`http://localhost/antasline` → 301 na kosu crtu =
artefakt poddirektorijuma na lokalu, na produkciji je koren domena) · **bez meta
description 31**.

**Poređenje sa baseline-om 10.08 (skripta nepromenjena, provereno):** na 194
zajednička URL-a **0 razlika** u `code`/`h1`/`jsonld_bad`/`title`. 5 stranica je u
međuvremenu **dobilo** metadesc (uklj. početnu). FAZA 2 nije polomila ništa.

🔴 **Najveći nalaz i njegovo objašnjenje — −118 slika na SVAKOJ stranici** (ukupno
26.626→4.397), dok jedinstvenih slika ima skoro isto (1.182→1.158): nestao je jedan
**globalni blok**, ne same slike. Uzrok: **ikonice mega menija uklonjene 12.08**
(backup `..._2026-08-12_pre-uklanjanje-meni-ikonica.sql`; 59 linkova × 2 renderovanja
menija = 118). Provereno u bazi: 79 SVG priloga i `uploads/meni-ikonice/` i dalje
postoje, ali ih **nijedna `nav_menu_item` stavka ne referencira**. Isti uzrok nosi i
−2 interna linka po stranici i `imgs_noalt` **23.010→0**. 🔴 **Neevidentirano u
[[PROGRESS]]/ovom ledgeru** — jedini trag je ime backup fajla; isto važi za još 4
backup-a od 12.08 i za **FAZU 1 od danas, čiji je unos završio na DNU ovog fajla**
(ledger je newest-on-top) pa je praktično nevidljiv.

**Sitemap 195→239 (+45, −1):** rast je od ranije (`category`/`product_cat`/
`product_tag` uključeni 11.08, `product_brand` 12.08) + 2 nova proizvoda iz FAZE 1
(isotrack-l/x) i `brend/bergo`. **Nestao** `/vestacka-trava/` (5455, draftovan 12.08
kao duplikat) — 🟢 pokriveno, `htaccess-301-DRAFT.txt:98` ima baš to pravilo, a live
URL i dalje vraća 200 pa je 301 neophodan.

**Bez metadesc 6→31 — nov nalaz, nije regresija:** svih 31 su taksonomijske arhive
koje su tek 11–12.08 ušle u sitemap — 18 `product_tag` (prored već zakazan posle
live-a, checklist §B7) + **6 blog kategorija + 6 `product_cat` + `brend/bergo`**.
Tih 13 je jedini sadržajni posao koji još staje pre freeze-a. #ceka-miroslav

**Reverifikacija 301 mape** (`redirect-verify.php`, jer je posle 11.08 menjan status
stranica): ciljevi **45/45 → 200**, duplikata **0**, petlji **0**, 15 prefiks-kolizija
poznato i nebitno (draft koristi sidreni `RedirectMatch "^/put/?$"`). 🟡 Jedno
upozorenje: `/sta-postaviti-preko-starog-parketa-ili-plocica/` (16613) vraća 200 a
pravilo ga šalje na `-2` (6588, 84 kl.) — **namerna** konsolidacija od 30.07, 16613 je
publish+`noindex`; nedosledno u odnosu na 5455 (draft), ujednačiti posle live-a.
**Draft ostaje važeći, ne regeneriše se.**

**Quick-win:** odluka **4.8 (Maximize Conversions)** zatvorena u
[[odluke/_pregled-odluka]] kao „odloženo do posle live-a" — sa razlogom (17 od 26
„plaćenih" su `tel` klikovi → pravih lidova 9; serija naduvana tagom id 18; učenje
Smart Bidding-a bi se završilo baš na dan migracije) i 4 preduslova za ponovno otvaranje.

**Nov baseline za post-migracionu proveru: `analiza/2026-08-13-regression-post-faza2-*`**
(pages.csv / assets.json / summary.json) — ne više 10.08.

**Dopuna iste sesije — 2 M odluke i 13 meta description-a upisano:** (1) **ikonice
menija se NE vraćaju pre live-a** — ostaju skinute, 79 SVG priloga stoji u medijateci
za kasnije; (2) **opisi se pišu** → `rank_math_description` upisan za **6 blog
kategorija + 6 `product_cat` + `brend/bergo`** preko
`migracija/alati/job-metadesc-arhive.php` (probni prolaz pa `--write`; skripta odbija
>160 znakova i odbija da pregazi postojeći opis). Svaki opis pisan **iz naslova
stvarnih postova/proizvoda u terminu**, bez izmišljenih modela i bez cena (Bergo je
„na upit" po M11); dužina 103–134 znaka, stil preuzet od postojećih 12 opisa. 🔴
Ključ je **`rank_math_description`**, ne Yoast — build je na Rank Math-u od 05.08
([[CLAUDE]] §7.1), pa je „Yoast ostaje" u `/antasline-sesija` za ovaj slučaj
zastarelo. 🟢 **18 `product_tag` arhiva namerno preskočeno** (prored zakazan posle
live-a, checklist §B7). **Verifikovano:** svih 13 → 200 / 1×H1 / meta prisutan
(106–139 B) / 0 PHP grešaka; regresija na 3 nedirnute arhive + početnoj čista.
Backup: `antasline_local_2026-08-13_pre-metadesc-arhive.sql`.

Detalji: [[dnevnik/2026-08-13-regression-sweep-post-faza2]].

---

## 2026-08-13 [cpanel-live] — LiteSpeed prefetch provera: Instant Click bezbedan, prefetch ne prerender (UŽIVO, read-only) ✅

> Zatvara otvoren rizik iz [[reference/chrome-web-platform-2026]] §3 ("Isto važi za
> bilo koji prefetch/prerender... proveriti pre migracije 24.08"). Nastavak istog
> dana, druga `[cpanel-live]` read-only sesija.

- `wp option list --search="litespeed.conf.*"` na `wp1.oblak.host`/`~/public_html`:
  `litespeed.conf.util-instant_click=1` (Instant Click UKLJUČEN), `optm-dns_prefetch_ctrl=1`
  (auto DNS prefetch), `optm-dns_prefetch`/`optm-dns_preconnect` prazni nizovi (ništa
  ručno podešeno).
- `curl -sL https://www.antasline.com/` potvrđuje da se `instant_click.min.js`
  (LiteSpeed Cache 7.8.1) stvarno učitava na live stranici — konfiguracija nije
  samo upisana nego i aktivna na frontend-u.
- 🔴→✅ **Ključna provera — mehanizam, ne samo da li je uključeno.** Izvorni kod
  `instant_click.min.js` podržava native Speculation Rules API
  (`HTMLScriptElement.supports("speculationrules")`) i grana na `type="prerender"`
  ISKLJUČIVO ako `document.body.dataset.instantSpecrules === "prerender"`. Taj atribut
  **ne postoji nigde** — ni u `<body>` tagu (proverено na homepage i 404 stranici),
  ni u LiteSpeed config-u (plugin 7.8.1 UI uopšte ne izlaže tu opciju). Default grana:
  `_speculationRulesType = "prefetch"`. **Prefetch preko Speculation Rules API-ja
  dovlači HTML u pozadini ali NE izvršava JS te stranice** (za razliku od
  `"prerender"`, koje bi izvršilo GTM tagove pre stvarne posete) — `generate_lead`
  trigger na `/hvala-za-poruku/` page view (BLOK A, [[CLAUDE]] §4) ne može lažno
  da okine na hover/mousedown nad linkom.
- 🟢 **Sporedan nalaz, bez rizika**: DNS Prefetch Control (auto) emituje samo 1
  `dns-prefetch` tag na homepage (`fonts.googleapis.com` — WP core default, ne
  LiteSpeed-ov doprinos). `googletagmanager.com` (učitan na svakoj stranici preko
  GTM snippet-a) nema dns-prefetch/preconnect hint — očekivano, GTM ubacuje domen
  kroz inline JS string, ne kroz literalan `<script src=...>` u sirovom HTML-u,
  pa ga LiteSpeed-ov statički skener ne vidi. Sitna, ne-hitna optimizacija:
  ručno dodati `googletagmanager.com` u `optm-dns_preconnect` listu, nije urađeno
  ove sesije (van obima "provera").
- **Ništa nije menjano** — čisto čitanje (`wp option list`, `curl`), nema
  `wp option update`, nema izmene fajla/baze/plugin podešavanja.
- Ažurirano: [[reference/chrome-web-platform-2026]] §3 (rizik zatvoren za trenutnu
  konfiguraciju, uslov za ponovno otvaranje ako se ikad ručno doda
  `data-instant-specrules="prerender"`).

---

## 2026-08-13 [cpanel-live] — Kvota potvrđena: keš se osvežio, 5,7 GB slobodno (UŽIVO, read-only) ✅

> Nastavak nalaza iz 2026-08-12 (`~/staging` brisanje, 3,4 GB oslobođeno) — tada je
> `uapi Quota get_quota_info` i dalje prijavljivao stari broj (2.487,65 MB slobodno),
> keš nije bio real-time. Provereno ponovo danas direktno na `wp1.oblak.host`:

- `uapi Quota get_quota_info`: limit **12.240 MB**, iskorišćeno **6.372,93 MB**,
  slobodno **5.867,07 MB** (~5,7 GB, 52% iskorišćenost) — keš se osvežio, broj sad
  odgovara stvarnom stanju.
- `du -sh ~` (ceo home): **5,7 GB** — u skladu sa uapi brojem.
- Zaključak: dovoljno prostora za probu migracije/backup pre 24.08 go-live-a, nalaz
  iz 08-12 pre-flight-a je zatvoren.

---

## 2026-08-13 [claude-code] — FAZA 2: layout/CSS/UI popravke (5 stranica prijavljeno → 3 sistemska uzroka, popravljeno sitewide) ✅

> Prva sesija dana. M je dao listu od 6 zamerki na 5 stranica („prevelike praznine",
> „pravougaonik iznad polja Ime i prezime", „narandžasti pravougaonik sa uzvičnikom",
> „sredi dugmad u Dokumentaciji"). **Nijedna nije rešena po stranici** — sve su svedene
> na tri uzroka u dizajn sistemu / temi i popravljene tamo.
>
> **🔴 Preduslov (okruženje):** ni MySQL ni Apache nisu radili. MySQL je pao na
> `Aria recovery failed` — **treći put** (postoje `aria_log*.bak-20260710` i
> `.bak-20260721`). Isti fix: `aria_log.00000001` + `aria_log_control` preimenovani u
> `.bak-20260813`, servis odmah podignut. Aria u XAMPP-u nosi samo `mysql.*` sistemske
> tabele; WP podaci su InnoDB, ništa se ne gubi.
>
> **Uzrok 1 — dve susedne `.al-section` ISTOG tona daju 144px+ mrtve trake.**
> `.al-section` nosi `padding: var(--al-gap) 0` (72px). Kad se dve sekcije iste pozadine
> dodiruju bez dijagonalnog reza, korisnik vidi 72+72 jednobojne prazne trake i čita je
> kao rupu, ne kao razmak — nema linije ni promene boje da je opravda. Dodatno: WPBakery
> daje **svakom** `.wpb_content_element` `margin-bottom: 35px`, pa i poslednjem u sekciji,
> i `wpautop` ostavlja gole `<br>` (~18px) između full-width redova.
> Popravljeno u `antas-design.css` (nova sekcija „FAZA 2"): spoj istih tonova dobija
> **jedan** ritam umesto dva (`padding-top: 0` na drugoj sekciji, `al-diag-*` izuzete jer
> im padding-top nosi sam rez) · poslednji WPBakery blok u sekciji gubi 35px ·
> `.wpb-content-wrapper > br { display: none }`.
> **Domet:** 15 spojeva na 14 stranica (prebrojano SQL-om nad `post_content`) + Woo
> kategorija stranice (opis kategorije i grid proizvoda su takođe dva `--mist` reda,
> ne vide se u `post_content`).
>
> | Stranica (M zamerka) | Pre | Posle |
> |---|---|---|
> | `/kategorija-proizvoda/kosarkaske-konstrukcije/` (tekst → Filteri) | 199px | **92px** |
> | `/sportske-podloge/kosarkaske-konstrukcije/` (ispod tabele modela) | 179px | **72px** |
> | `/sportske-podloge/kosarkaske-konstrukcije/` (ispod galerije) | 179px | **96px** |
> | `/lvt.../vinil-podovi-objectflor/` (Kolekcije → Primena) | 179px | **76px** |
> | `/dimenzije-kosarkaskog-terena/` (Pitanja → Primeri) | 167px, bez granice | 144px **sa kontrastnom granicom** |
>
> 🔴 **Gotcha koji je pravilo skoro propustilo:** prva verzija selektora (`+` i
> `+ .vc_row-full-width +`) radila je na `/sportske-podloge/kosarkaske-konstrukcije/`,
> a **nije** na `/industrijski-podovi/` — iako je markup „isti". Razlika je goli `<br>`
> iz `wpautop` između redova (nastaje kad je `[/vc_row]` u `post_content` završen novim
> redom). CSS `+` traži **tačnu** susednost i ne preskače prazne markere. Rešeno
> nabrajanjem svih šest stvarno viđenih kombinacija (`br`, `.vc_row-full-width`, i
> njihove permutacije) po tonu. `display:none` na `<br>` **ne pomaže selektoru** —
> element i dalje stoji u DOM-u.
>
> **Kontrastna boja (M je tražio predlog):** sekcija „Primeri" na
> `/dimenzije-kosarkaskog-terena/` prebačena `al-section--paper` → **`al-section--mist`**
> (`#EEF3F8`, brend neutral koji ista stranica već koristi dvaput). Razmak sada čita kao
> namerna traka. Upis preko `$wpdb->update()` sa provetom jedinstvenosti sidra.
>
> **Uzrok 2 — WoodMart deregistruje CF7 CSS, a svoju zamenu enqueue-uje samo iz svog
> elementa.** `woodmart/inc/enqueue.php:591` radi
> `wp_deregister_style('contact-form-7')` i zamenjuje ga sa `css/parts/int-wpcf7.css`,
> koji se enqueue-uje **isključivo** iz `woodmart_shortcode_contact_form_7()`. „Brzi upit"
> (16737) renderujemo sirovim `do_shortcode('[contact-form-7 …]')` iz `the_content` prio 12,
> pa taj part nikad nije stizao. Posledica su **oba** M-ova pravougaonika, na svih ~55
> stranica sa formom:
> - prazan okvir iznad „Ime i prezime" = `<fieldset class="hidden-fields-container">`
>   (skriveni `_wpcf7_*` inputi) — gasi ga `div.wpcf7 .hidden-fields-container{display:none}`
>   iz `int-wpcf7.css`;
> - narandžasti okvir sa „!" ispod dugmeta = `.wpcf7-response-output`, koji iz
>   `parts/mod-notices-general.css` (**jeste** učitan) dobija `display:block` + warning žutu
>   (`#E0B252`) + `\f100` ikonicu; `int-wpcf7.css` ga gasi sa
>   `form div.wpcf7-response-output{display:none}` i pušta tek kad forma dobije
>   `.sent`/`.invalid`/… klasu.
>
> Fix: `woodmart_enqueue_inline_style('wpcf7')` u quick-quote filteru (`functions.php`) —
> izjednačavanje sa `/kontakt/`, koji formu renderuje kroz WPBakery CF7 element i part je
> oduvek imao. Naš styling polja/dugmeta/placeholdera ostaje jači po specifičnosti
> (`.al-section .wpcf7 input[type=submit]` = 0,3,1 vs teminih 0,1,2) — vizuelno se ništa
> drugo nije promenilo, provereno.
> ⚠️ Prvo je razmatran čist CSS fix (sakriti oba elementa iz `antas-design.css`); odbačen
> jer bi zamaskirao uzrok i ostavio i ostatak part-a (spinner, `not-valid-tip`) neaktivan.
>
> **Uzrok 3 — `clip-path` paralelogram odseca vertikalne krakove `inset` rama.**
> `.al-btn--ghost` crta ram sa `box-shadow: inset 0 0 0 2px currentColor`, a oblik dolazi
> od `clip-path: polygon(12px 0, 100% 0, calc(100% - 12px) 100%, 0 100%)`. Kosi rez pada
> tačno preko levog i desnog kraka rama → dugme se renderuje kao **dve odvojene vodoravne
> crte**. Na navy hero-u (jedno ghost dugme pored punog crvenog CTA) to prolazi kao
> potpis; u „Dokumentacija" gridu od 4 kartice čita se kao nedovršen okvir. Uz to su
> dugmad bila `inline-block` levo poravnata u vrhu kartice (kartice u gridu jednake
> visine → mrtva zona ispod), a dvoredna labela („Deklaracija o performansama") ispadala
> iz paralelograma crtanog za jedan red.
> Fix: telo kartice postaje flex-centar, dugme puna širina + centriran tekst + manji font
> (`clamp(18px,1.25vw,21px)`) i `line-height: 1.2`, **`clip-path: none`** → pun pravougaoni
> navy ram. Scope: `.al-card:has(> .al-card__body > .al-btn:only-child)` — prebrojano,
> **3 stranice** (16684 expona-click, 16685 vinil-podovi, 17252 expona-simplay).
> Usput: `.al-btn--ghost:hover` je postavljao `rgba(255,255,255,0.1)`, što je na
> `--paper`/`--mist` sekcijama **belo na belom = nikakav hover feedback**; na svetlim
> podlogama sada popunjava navy sa belim tekstom (ram prati `currentColor`).
>
> **Dodatno na M zahtev („uskladi") — 17 golih `<h2>` posle `.al-label`.** Prijavljena su
> dva (Reference na 16657, Primeri na 16586), SQL sken je našao **17** stranica sa istim
> obrascem — naslov sekcije bez `al-display--lg`, vizuelno 38px umesto 68px pored svih
> ostalih H2 na istoj stranici. Sve usklađene jednim prolazom; regex hvata isključivo
> `<h2>` **bez ijednog atributa** odmah iza `<span class="al-label">…</span>`, po jedan
> pogodak po stranici. Semantika netaknuta (`<h2>` ostaje `<h2>`), samo klasa.
> Pogođeni ID-evi: 16585, 16586, 16589, 16590, 16657, 16658, 16679, 16683, 16688, 17025,
> 571, 16660, 16665, 16666, 17019, 17026, 17027.
>
> **Verifikacija:** 17 + 15 URL-ova kroz `curl` — **svi 200 / 1×H1 / 0 PHP grešaka /
> 0 preostalih golih `<h2>`**. Vizuelno u Chrome-u: obe kosarkaske stranice, dimenzije,
> objectflor, expona-click, kategorija, homepage, `/kontakt/`, `/pvc-podne-ploce/`
> i jedan blog post sa sidebar-om (forma se tamo renderuje kao kartica, ne 100vw breakout
> — obe varijante čiste). Forma: `hidden-fields-container` i `wpcf7-response-output` oba
> `display:none`, dugme/polja nepromenjeni. Homepage nema nijedan spoj istog tona → 0
> izmenjenih sekcija, ništa nije regresiralo.
>
> **Backupi:** `antasline_local_2026-08-13_pre-faza2-layout.sql` (37,6 MB) ·
> `antasline_local_2026-08-13_pre-h2-uskladjivanje.sql` ·
> `antas-design.css.bak-2026-08-13-pre-faza2` · `functions.php.bak-2026-08-13-pre-faza2`.
> **Skripte:** `fix-16586.php` (Primeri → mist) i `uskladi-h2.php` (17 naslova) —
> jednokratne, u scratchpad-u, nisu vraćane u vault.
>
> Detalji: [[dnevnik/2026-08-13-faza2-layout-ui-fixes]].
>
> **Dopuna iste sesije — `antasline-backups/` izbačen iz git-a (odluka M, opcija a).**
> Usput nađeno pri commit-u: folder je **683 MB u 20 SQL dump-ova** i **verzionisan** —
> `.gitignore` ga nije pokrivao, pa je Obsidian Git auto-commit (`3beb20f`) pokupio i
> dva današnja backup-a (+75 MB). Isti folder je pre-flight 12.08 izmerio na **539 MB
> na serveru**, drugi po veličini odmah posle obrisanog `~/staging/`, dok je prostor
> bio 🔴 bloker pred 24.08.
> Izvršeno: `antasline-backups/*.sql` u `.gitignore` + `git rm --cached` nad svih 20
> fajlova → commit **`f2dde40`**. **Fajlovi ostaju na lokalnom disku** i dalje se prave
> pre svake izmene baze; prestaju samo da se verzionišu i sinhronizuju na hosting.
> Provereno: `git ls-files antasline-backups/` → **0**, `ls` → **20**, nov `.sql`
> u folderu se ne pojavljuje ni uz `--untracked-files=all`, radni direktorijum čist.
> 🔴 **Dve posledice:** (1) **rollback izvor je od sada lokalni disk, ne git** — klon
> vault-a na čistu mašinu ne donosi backupe; (2) na serveru se fajlovi brišu **tek pri
> sledećem `git pull`**, a `.git` se **neće smanjiti** (77 MB) jer istorija zadržava sve
> verzije — skraćivanje istorije nije rađeno (previše rizično 3 dana pred freeze, a
> kompresija ionako svodi 683 MB na 77 MB).

---

## 2026-08-13 [claude-code] — FAZA 1: Visual, Assets & Media Cleanup (lokalni build)

Backup pre rada: `antasline-backups/antasline_local_2026-08-12_pre-faza1-visual.sql`; drugi pre brisanja varijacija: `..._pre-bergo-varijacije.sql`.

**Slike i hero:**
- Stoni tenis (16583): hero zamenjen novom slikom iz `Slike/` (`dreamstime_l_12820288`) → `podovi-za-stoni-tenis-sala.webp` 1920×1080, 62 KB. Stari hero je bio 800×480 close-up sa vidljivim konkurentskim brendom (Sponeta). Postavljen i featured image.
- Kategorija Košarkaške konstrukcije (term 251): `kosarkaska-konstrukcija-sa-tablom-i-obrucem.webp` 1200×800, 35 KB (izvor `dreamstime_l_33377627` — prikazuje konstrukciju, ne igru). Prva kategorija uopšte sa slikom.
- R-Tile Urban (16920): loša AI feature slika (mutna, crni artefakti) zamenjena zvaničnim studijskim snimkom proizvođača **R-Tek Manufacturing** (`r-tekmanufacturingltd.com`), 1000×1000.
- Bergo Solid (16843): nedostajala slika same ploče — dodat zvanični render šestougaone HDPE ploče sa bergoflooring.com; raniji „featured" (montirana staza) prebačen u galeriju.

**Bergo blok:**
- 🔴 **7 feature slika bilo AI-generisano i netačno** (npr. XL rotiran u romb). Zamenjene zvaničnim renderima proizvođača: Unique, Ultimate, Ultimate PLUS, Ultimate FLOW, XL, Elite, Nova. AI fajlovi ostaju u medijateci (rollback).
- **Boje:** 68 varijacija boje, samo 2 imale sopstvenu fotografiju → dropdown je za sve ostale prikazivao istu ploču. Odluka M: varijabilni proizvodi pretvoreni u **proste**, `pa_boja` ostaje kao vidljiv (ne-varijacioni) atribut + rečenica u opisu („Dostupno u N boja: … pošaljite upit"). Cena/SKU preneti sa varijacija (bili identični).
- **Brend:** kreiran termin `product_brand` = **Bergo**, dodeljen svih 11 Bergo proizvoda (ranije nijedan nije imao brend; postojali samo Ecotile i Ergomat).

**Stranice:**
- Industrijski podovi (16567): dodata **Quectel Beograd** referentna kartica sa pravom fotografijom iz arhive (HTEC je već imao ispravnu). Reference sada 6 kartica.
- Spoljne podne obloge (16590): **Bergo Easy uklonjen** (diskontinuiran; link je i bio mrtav — ta stranica ne postoji nigde u bazi), „četiri modela" → „tri modela", lista modela pretvorena u vizuelne kartice sa renderima ploča.
- Parkiralište i staze (16589): Runfloor / Geocross / Geogravel / Geoflor dobili sliku pravog proizvoda + link ka proizvodu (ranije samo tekst).
- LVT komercijalni (16667): sekcija „Primena" iz gole liste u 4 kartice sa **pravim fotografijama iz objekata** (vinarija, kancelarija, maloprodaja, recepcija) iz `lvt podovi/` arhive.
- LVT ugostiteljstvo (16686): statički swatch-evi dezena zamenjeni karticama stvarnih proizvoda (Expona Commercial / Clic 19dB / Simplay 19dB / Flow) + link na katalog.
- Kancelarije (16669): 3 nepovezana swatch-a → 4 kartice proizvoda sa linkovima + link na kategoriju LVT.
- Isotrack (16111): **nije postojao nijedan Isotrack proizvod**. Kreirani `isotrack-l` (#17836) i `isotrack-x` (#17837) isključivo iz specifikacija sa same stranice (dimenzije, težina, nosivost, montaža), slike već bile u medijateci; stranica sada ima sekciju „Isotrack modeli u ponudi".

**Verifikacija:** svih 11 dirnutih stranica + 10 Bergo proizvoda vraćaju HTTP 200, tačno 1×H1, nula WooCommerce placeholder slika, nula preostalih variation dropdown-ova.

**Tehnički gotcha (novo, vredi zapamtiti):** `get_post_field('post_content', $id)` podrazumevano radi u `display` kontekstu i provlači sadržaj kroz `the_content` filtere — wptexturize iskrivi apostrofe unutar WPBakery `css=""` atributa, pa `str_replace` tiho promaši. Za programske izmene sadržaja čitati **raw** (`$wpdb`) i pisati direktno preko `$wpdb->update` + `clean_post_cache()`, jer `wp_update_post()` bez ulogovanog korisnika pušta kses koji čisti inline stilove iz builder markup-a.

#ceka-miroslav: hero stranice 16686 (ugostiteljstvo) je i dalje flat swatch dezena (`French-Vanilla-Oak-4058.jpg`) — predlog: zameniti pravom fotografijom vinarije (att. 17832), ali nije dirano jer nije bilo u zadatku.

## 2026-08-12 [cpanel-live] — `~/staging/` obrisan (3,4 GB), prostor oslobođen (UŽIVO) ✅

> Nastavak iste `[cpanel-live]` sesije, na M zahtev direktno posle pre-flight nalaza
> ispod. **Jedina izmena ove pod-sesije, van docroot-a `public_html`** — `staging.antasline.com`
> je leftover od probne migracije 06–07.08 (vizuelno već potvrđen tada), nije aktivan
> deo redovnog toka.
>
> **Izvršeno:** `rm -rf ~/staging` → `mkdir ~/staging` + `chmod 755` (prazan direktorijum
> ostaje, subdomen mapiranje netaknuto, samo sadržaj obrisan). Baza `antasline_staging`
> (**~58 MB**, zanemarljivo) **namerno ostavljena netaknuta** — nije tražena, van obima
> "disk prostor" nalaza. Kredencijali (`~/staging-htaccess-creds.txt`,
> `~/staging-db-credentials.txt`) takođe ostavljeni — sitni fajlovi, nisu deo prostor-nalaza.
>
> **Verifikacija:** `du -sh ~/staging` pre **3,4 GB** → posle **4,0K**; `du -sh ~`
> (ceo home) pre bilo koje operacije ~10,1 GB (staging+mail+public_html+vault+ostalo)
> → posle **6,2 GB**. 🟡 **`uapi Quota get_quota_info` i dalje prijavljuje identičan
> stari broj** (9.752,35 MB iskorišćeno / 2.487,65 MB slobodno) i posle ponovnog
> pollovanja par sekundi kasnije — cPanel-ov kvota-keš očigledno se ne osvežava
> real-time, nema root/WHM pristup da se to forsira. **Fajlsistemski dokaz (`du`)
> potvrđuje brisanje; zvanični kvota-brojač će se osvežiti sa kašnjenjem** (nepoznat
> interval, nije nešto što mogu izmeriti sa ovog naloga) — proveriti ponovo u sledećoj
> `[cpanel-live]` sesiji da potvrdi novi broj pre 24.08.
>
> Rešava 🔴 nalaz iz pre-flight-a ispod: prostor za istovremen backup+novi paket (~2,6 GB)
> sad ima veliku rezervu (bilo 2,43 GB slobodno pre brisanja, staging sam nosio 3,4 GB).

---

## 2026-08-12 [cpanel-live] — pre-flight infrastruktura (UŽIVO, read-only) — disk prostor rizik nađen, JetBackup nedostupan iz shell-a ✅

> Osma sesija istog dana, `[cpanel-live]` (`wp1.oblak.host`, `~/public_html` = live).
> **Read-only pre-flight provera pred migraciju 24.08 — ništa nije menjano:** nijedan
> fajl/`.htaccess`/tema/plugin/baza nije dirana, keš nije čišćen, `wp search-replace`
> nije pokretan, Redis nije diran, 301 blok nije pisan. Sedam merenja, svako sa
> 🟢/🟡/🔴/⚪ ocenom.
>
> **🔴 Prostor — glavni nalaz.** cPanel kvota (`uapi Quota get_quota_info`): limit
> **12.240 MB**, iskorišćeno **9.752,35 MB (79,7%)**, slobodno **2.487,65 MB (~2,43 GB)**.
> Inode limit **0 = neograničeno** (183.943 iskorišćeno) — nije usko grlo. Migracija
> treba istovremeno ~1,3 GB novi wp-content paket + ~1,3 GB svež backup (DB+wp-content)
> ≈ **2,6 GB — ne staje** u trenutnih 2,43 GB slobodno. `du -sh` po top-level folderima
> otkrio uzrok: **`~/staging/` 3,4 GB** (leftover od probne migracije 06–07.08, najviše
> `wp-content` 3,3 GB) i **`~/antasline-vault/antasline-backups/` 539 MB** (SQL dump-ovi
> lokalnog build-a sinhronizovani u vault). Brisanje `staging/` samo rešava prostor sa
> velikom rezervom — **nije obrisano ove sesije**, samo izmereno i predloženo.
>
> **🟢 Put prenosa paketa** (Miroslav nema SSH, FTP push već pukao na 3,18 GB —
> rizik #5 u pre-flight checklisti): `curl`/`wget`/`rsync`/`tar`/`unzip`/`zip`/`split`/
> `md5sum`/`sha256sum` svi prisutni; odlazni HTTPS radi (`curl -sI https://github.com`
> → `HTTP/2 200`). **Preporuka: pull sa servera (server inicira `curl`/`wget`/`rsync`/
> `scp` ka izvoru) kao primarni put 24.08, File Manager kao rezerva.**
>
> **⚪ JetBackup — Nema podataka.** `uapi Backup list_backups` → nalog nema feature
> "backup"; `JetBackup5::wrapper` traži nedokumentovan format funkcije (3 varijante
> pokušane, sve puknu na "Invalid or missing function"); `~/.jbm/downloads` prazan;
> nema drugih lokalnih log/artifact fajlova. Poslednji snapshot/retencija/off-site
> status zahteva WHM/cPanel UI pristup koji ne postoji sa ovog naloga — nije nagađano.
>
> **🟢 Speculative Loading:** WP core **7.0.4**; 11 aktivnih plugina (antasline-consent,
> wp-call-to-order, classic-editor, google-tag-manager, **litespeed-cache 7.8.1**, worker,
> redirection, svg-support, woo-variation-swatches, woocommerce, wordpress-seo) — nijedan
> sa "specul"/"prefetch"/"instant" u imenu, potvrđuje spoljni nalaz (prefetch/conservative).
>
> **🟡 LiteSpeed CCSS/UCSS:** oba uključena i aktivna (`litespeed.conf.optm-ccss_gen=1`,
> `optm-ucss=1`), poslednji cloud request identičan za oba — **2026-08-11 15:57 UTC**
> (`litespeed.css._summary`/`litespeed.ucss._summary`, epoch 1786463853/1786463852) —
> potvrđuje 08-11 nalaz da je UCSS "oživeo". LQIP i dalje zamrznut od **25.07** (poznato,
> M odluka da se ne dira, nije gate stavka). 🆕 **Instant Click uključen**
> (`litespeed.conf.util-instant_click=1`) — LiteSpeed hover-prefetch (dohvata HTML u
> pozadini na hover, ne izvršava JS/GTM dok se stvarno ne klikne) — niži rizik od prave
> Speculation Rules prerenderacije, ali vredi znati da radi. Fizički CCSS/UCSS fajlovi
> nisu na disku (`wp-content/cache/litespeed/` ne postoji) — server-level LSCache, izvan
> PHP-korisnika, potvrđeno isključivo preko DB opcija, ne fajlsistema.
>
> **🟢 Higijena docroot-a:** `find ~/public_html -maxdepth 3` za `*.bak*`/`*.orig`/
> `*.old`/`mail-log.txt`/`al-harness.html` → **0 pogodaka**.
>
> **🟢 Verzije/limiti:** PHP **8.2.31** (CLI; web vhost `ea-php82`, `php_fpm:0` → deli isti
> config; `staging.antasline.com` je na `ea-php74`, nevažno za produkciju) · WP-CLI
> **2.12.0** · DB `antasline_novabaza` **33 MB** · `memory_limit` **1024M** ·
> `max_execution_time` **0** (CLI vrednost, web SAPI nije odvojeno potvrđen) ·
> `upload_max_filesize`/`post_max_size` **512M**.
>
> **Najveći rizik za 24.08: disk prostor** — 2,43 GB slobodno naspram ~2,6 GB potrebnog
> za istovremen backup+novi paket, sa lako uklonjivim uzrokom (`~/staging/`, 3,4 GB).
> #ceka-miroslav: odluka da li se `~/staging/` briše pre 24.08 (predlog: da, izgleda
> kao leftover probne migracije, vizuelno već potvrđeno 06.08) i da li JetBackup
> snapshot status treba proveriti kroz cPanel UI (WHM Backup Wizard/JetBackup stranica)
> pošto shell/API ne daju odgovor.

---

## 2026-08-12 [claude-code] W3 — `live-export.sh` gubio galerijske slike + prefiks baze ispravljen ✅

> Sedma sesija istog dana. Zatvara **dva 🔴 blokera od jutros** (oba iz `agy` pre-flight
> nalaza). Bez izmena na buildu i bazi — izmenjene su samo migracione skripte i
> dokumentacija; jedini upis u bazu je nula, test export je pisao u scratchpad.

### Quick-win — prefiks baze je `wpgs_`, ne `wpGs_` (provereno protiv baze)

`SHOW TABLES` na lokalu vraća **`wpgs_posts`**; isto stoji u live/staging dump-ovima.
Lokalni `wp-config.php` ipak nosi `$table_prefix = 'wpGs_'` **i radi** — samo zato što
je MariaDB na Windows-u `lower_case_table_names=1` (provereno, vrednost `1`). Na Linux
hostingu razlikuje velika i mala slova — to je tačan uzrok „site not installed" greške
pri probi migracije 2026-07-21.

Ispravljeno: `CLAUDE.md` §2 (+ nova 🔴 ograda ispod tabele) i §7.5 · `/antasline-sesija`
§3 · `/obogati-proizvod` (2 mesta). Usput ispravljen i broj tabela u §2: **106 → 78**
(zaostalo od pre čišćenja 33 osirotele plugin-tabele).

🔴 **Isti bag nađen i u kodu, ne samo u dokumentaciji:** `staging-import.sh:19` je imao
`STG_PFX="wpGs_"`, a to je promenljiva kojom `sed` prepisuje imena tabela u dump-u —
dakle upravo ono „tiho uveze u pogrešne tabele" iz blokera, samo maskirano činjenicom
da se do sada pokretalo na Windows-u. Ispravljeno na `wpgs_`.

### W3 — `live-export.sh` (glavni zadatak)

**Izmereno pre popravke** (na lokalnoj bazi, ista struktura kao live):

| | |
|---|---|
| proizvodi + varijacije | 245 |
| attachmenti koje skripta hvata (`post_parent` + `_thumbnail_id`) | **196** |
| galerijskih slika ukupno | **170** |
| **od toga bi tiho nestalo iz exporta** | **145** |
| slike kategorija (`termmeta.thumbnail_id`) | 0 na lokalu (kod ipak dodat, live ih može imati) |

Bloker je opisivao rizik; brojka je **145 od 170**, dakle 85% galerijskih slika.
Uzrok tačno kako je prijavljeno: linije 24–36 skupljaju attachmente preko `post_parent`
i `_thumbnail_id`, komentar tvrdi „thumbnail + galerija", a `_product_image_gallery`
se nigde ne čita — galerijske slike bez `post_parent` veze ispadaju bez ijedne greške.

**Popravljeno:**
- (3) `GAL_IDS` — `_product_image_gallery` je zarezom razdvojena lista, splituje se u shell-u
- (4) `CAT_THUMB_IDS` — slike `product_cat` kategorija (isti razred baga, `termmeta.thumbnail_id` nema `post_parent`)
- **tvrda provera pred dump**: svaki galerijski ID mora biti u `ALL_ATTACH`, inače `exit 1`
- `PFX`/`OUT` se mogu pregaziti iz okruženja → skripta se **može testirati na lokalu**, što do sada nije bilo moguće

**Test uživo** (lokalni build, `PFX=wpgs_`, izlaz u scratchpad — baza nije dirana):
245 proizvoda · **341 attachment** (196 → 341, tačno +145) · 170 galerijskih.
Spot-check tri slike koje su ranije ispadale (2515 `bergo-unique-ploca-2`,
2681 `privatni-teren-cacak-multisport-bergo-2`, 2798 `bergo-kanjiza2`) — sve tri
sada u dump-u. `bash -n` čist na obe skripte.

### 🔴 Tri gotcha-a koje je otkrilo tek stvarno pokretanje (skripta do danas nikad nije testirana)

1. **Višelinijski SQL kroz `wp db query` vraća prazan rezultat sa exit kodom 0.**
   Najgora vrsta promašaja — nema greške, `set -e` ne reaguje, liste ID-eva samo
   ispadnu prazne. Isti upit u jednoj liniji radi. **Svi upiti spljošteni u jednu liniju.**
2. **`--no-create-info` WP-CLI 2.12 pretvara u `create-info=`** → `mysqldump: unknown
   variable 'create-info='`, export puca na samom pisanju dump-a. Radi kao
   **`--no-create-info=true`** (provereno: 0 `CREATE TABLE`, `INSERT` prisutan).
3. **Windows CRLF + prazan završni red.** `wp db query` na Windows-u vraća `\r\n` i
   završni prazan red; `grep '^[0-9]+$'` tada ne pogodi ništa (sve liste prazne), a
   `paste -sd, -` napravi završni zarez pa `IN (1,2,)` pukne. Rešeno omotačem
   `q()` (`sed 's/\r$//; /^[[:space:]]*$/d'`) u obe skripte.

Sve tri su bezopasne na Linux-u, ali su do danas činile da se skripta **ne može
proveriti** pre dana migracije — a upravo je to jedini dan kad se pokreće.

**Nije dirano:** baza, build, live. `migracija/alati/job-plugin-cleanup-cron.php` i dalje
piše `wpGs_options` — jednokratna već izvršena skripta, ostavljena kako jeste.

---

## 2026-08-12 [claude-code] ALATI — Antigravity (`agy`) kao delegat + pre-flight checklist za 24.08 ✅

> Šesta sesija istog dana. Krenulo kao pitanje „može li Gemini/GPT za SEO/GA4/Ads
> analizu", završilo kao stalna infrastruktura + isporučen checklist za migraciju.
> Bez izmena na buildu i bazi — sve read-only.
>
> **Inventar provereno, ne pretpostavljeno:** `gemini`/`openai`/`codex` CLI ne
> postoje; Ollama radi (`qwen3:30b`, `qwen2.5-coder`, `llama3.2:3b`, `gemma3:1b`).
> Antigravity nađen tek iz M-ove putanje — **`C:\Users\Miroslav\AppData\Local\agy\bin\agy.exe`**
> (178 MB, ažuriran isti dan); `%LOCALAPPDATA%\Antigravity\staging` je prazan mamac,
> registry nema unos.
>
> **`agy` nije samo IDE nego headless CLI** — `-p`, `--json-schema`, `--sandbox`,
> `--mode plan`, `--model`. Test print-mode **4,8 s**, autentifikovan. Modeli:
> Gemini 3.6/3.5 Flash, 3.1 Pro, Claude Sonnet/Opus 4.6, GPT-OSS 120B — **svi na
> istoj Google kvoti**. Time pada prvobitna zamerka „Gemini nema kontekst, ne vidi
> vault" — ona važi za goli Gemini API, ne za Antigravity.
>
> **Isporuka:** `agy` (Flash Medium) pročitao **87 .md fajlova** (`dnevnik/` 50 +
> `migracija/` 37, ~1 MB ≈ 250k tokena) → **19 rizika · 11 ručnih radnji na dan
> migracije · 6 konflikata**, svaki sa izvorom. Sirov izlaz `migracija/preflight.txt`,
> očišćeno u **[[migracija/2026-08-12-preflight-checklist-24-08]]**.
>
> **Napravljen skill `/agy-delegat`** (`SKILL.md` + `promptovi/_SABLON.txt` +
> `promptovi/preflight-migracija.txt`), upisan u [[reference/claude-skilovi]].
> Podela: `agy` = masovno+plitko+proverivo; Claude = odluke, Ads/GTM, baza, dan
> migracije. 🔴 **Nikad Claude modeli unutar `agy`** — Opus već imamo, to je čist
> gubitak Google kvote.
>
> 🔴 **Gotcha — headless `agy` sam sebi odbije alat** (`no output produced`).
> Sintaksa `permissions.allow` u `~/.gemini/antigravity-cli/settings.json`;
> `command(...)` **potvrđeno da radi** (dodato), alati za čitanje nisu — Claude Code
> harness blokira i `--dangerously-skip-permissions` i dalje širenje dozvola.
> Fallback: TUI, M nalepi prompt i odobri.
> 🔴 **Bez punih apsolutnih putanja `agy` pretražuje `C:\Users\Miroslav` rekurzivno**
> (potvrđeno u logu) — čisto trošenje kvote.
> 🟡 **TUI ispiše rezultat dvaput** (redraw) — izgleda kao da je pukao; druga kopija
> ume biti potpunija. 🟡 **ASCII tabela se raspada posle ~10 redova** → tražiti običnu
> pipe-tabelu. 🟡 `settings.json` default model je **3.1 Pro (Low)** — bez `--model`
> skup posao ode na Pro.
>
> ⚠️ **Ispravka sopstvene tvrdnje:** rekao sam da je `agy` „našao ono što nijedan
> audit nije imao". Nije bilo u **dva audita**, ali **jeste u [[reference/naucene-lekcije]]**
> (mu-plugins 08-10, `*.bak-*` 08-10, Redirection u bazi 08-11, OAuth *Testing* 08-11,
> `wpgs_` 08-06). Stvarni doprinos je **konsolidacija u dan-migracije checklist i
> izvlačenje konflikata**, ne novo otkriće.
>
> **Verifikovano protiv koda:** `live-export.sh:24-36` skuplja attachmente preko
> `post_parent` i `_thumbnail_id`, ali **nikad ne čita `_product_image_gallery`** —
> komentar na liniji 25 kaže „thumbnail + galerija", kod galeriju ne dodiruje.
> Galerijske slike bez `post_parent` veze tiho nestaju pri exportu.
>
> 🔴 **Konflikt koji pogađa `CLAUDE.md`:** §2 i §7.5 tvrde prefiks `wpGs_`; lekcija
> 08-06 i pre-migration checklist kažu **`wpgs_` malim slovima**. `CLAUDE.md` je
> autoritet koji čita svaki agent → mina pred 24.08.
> 🔴 **Maximize Conversions otpada** — od 26 „konverzija" **17 su bili `tel` klikovi**,
> pravih formi **9**. Prag 20–30 nije pređen. Detalji: [[dnevnik/2026-08-12-agy-antigravity-delegat]].

## 2026-08-12 [claude-code] BLOK C — Vizuali referenci i ikonice kartica (homepage, O nama, padel, maloprodaja) ✅

> Peta sesija istog dana. Ad-hoc polish po nalazima Miroslava dok gleda build — nije
> stavka iz reda čekanja, W1 ostaje zaključen. Backupi:
> `_pre-veruju-nam-slike.sql` · `_pre-onama-reference-slike.sql` ·
> `_pre-padel-ikonice.sql` · `_pre-maloprodaja-ikonice-proizvodi.sql` ·
> `antas-design.css.bak-2026-08-12-card-title-link`.
>
> **Gole tekstualne trake referenci (`.al-ref-row`) zamenjene foto-karticama.**
> Homepage „Veruju nam": HTEC / Quectel / Dunk Shop dobili kartice → **6 referenci**.
> O nama: tri liste (Industrija 10 · Sport 6 · Ugostiteljstvo 4 imena) → **11 kartica**
> sa fotkama naših izvedenih radova. Logo traka (Bosch, Vinča, Adient, Philip Morris,
> AMSS) **premeštena** iz uvoda u sekciju Industrija; **Orion izvađen iz nje** jer je
> dobio foto-karticu (inače bi bio dvaput u istoj sekciji). Posle ove sesije
> `.al-ref-row` se **nigde ne koristi** (CSS pravilo ostavljeno u fajlu).
>
> 🔴 **Dunk Shop fotka nije postojala na lokalu** — 0 pogodaka u `uploads`, DB
> (`post_content`/`post_title`/`guid`/`postmeta`/`options`) i foto-arhivi. Nađena kao
> serijalizovana **live** putanja u starijem vault SQL backup-u → skinuta sa
> antasline.com i uvezena (**17808**). Dve fotke kafića uvezene iz arhive
> `slike 12-22/bergo baste/` (**17810** Metropolis, **17811** Arabika).
>
> **Ikonice — set narastao 23 → 27.** Padel (16670): blok od 4 kartice nije imao
> **nijednu** ikonicu → 🆕 `brzina.svg` + 🆕 `odbijanje.svg`, plus postojeće
> `odrzavanje`/`ergonomija`. Maloprodaja (16142): 3 od 7 kartica bez ikonice →
> 🆕 `bez-pripreme.svg` + 🆕 `vatrootpornost.svg`, plus `fleksibilna`. Sve četiri
> crtane **ručno** po specifikaciji seta (24×24, `stroke #F04D22`, `stroke-width 1.7`,
> round) — generator iz `design` skila namerno preskočen, ne pogađa house stil.
> `vatrootpornost` ima unutrašnji plamen da se ne meša sa `odrzavanje.svg` (ista silueta).
>
> **Maloprodaja: dodata sekcija „R-Tile ploče iz ponude"** — stranica nije imala
> **nijedan** link ka `/proizvod/`. Dve kartice (16920 Urban, 16921 Design), opisi
> izvučeni iz `post_content` proizvoda, ništa izmišljeno. Pozadine tri sekcije ispod
> pomerene (`mist`→`paper`→`mist`→`paper`) da ritam i dijagonale ostanu netaknuti.
>
> 🔴 **Gotcha — `<a class="al-card">` ne sme da nosi blok sadržaj.** Kartica sa
> `<div class="al-card__body">` unutar anchora: **wpautop ubaci prazan `<p></p>` pre
> `div`-a**, parser zatvori anchor, telo ispadne iz grid ćelije — slike ostanu u redu
> od 2, tela se nasložu ispod preko cele širine. Vidi se **samo u browseru**, izvor
> izgleda ispravno. Rešenje: kartica je `<div>`, link na `.al-card__media` i u naslovu.
> Postojeće `a.al-card` kartice (homepage, padel) su bezbedne — imaju samo `<span>` decu.
>
> 🔴 **Gotcha — `:not(.klasa)` broji kao klasa.** Naslov-link je dobijao plavo
> podvlačenje od `.entry-content a:not(.al-btn):not(.al-card)` (`antas-design.css:1477`),
> specifičnost **(0,3,1)**; naivno `.al-card__title a` je (0,1,1) i tiho gubi. Izuzetak
> upisan uz postojeći za `.wd-post-title`/`.wd-entities-title`, istog oblika.
>
> 🟡 **Ikonice traže 5 iteracija i oko, ne kod.** `brzina` je prvo čitala kao
> **pola-popunjen krug**, `odbijanje` redom kao **kuka/laso**, **brda sa suncem**,
> **kvačica**. Renderovane na 46 px i 120 px pored postojećih iz seta pre prihvatanja
> (privremeni `icon-preview-tmp.html`, obrisan).
>
> ⛔ **`/vestacka-trava-za-fudbal/` (5119) i `/zastitne-podloge-za-travu-i-plocnike/`
> (15793) — PREKINUTO na zahtev M („Prekini"), 0 izmena.** Prijavljeno „stari format",
> **nije reprodukovano**: obe koriste aktuelan `al-*` sistem i imaju **identične `body`
> klase** kao rebuild-ovane stranice. Audit svih **53 objavljene stranice**: jedini pravi
> legacy markup u buildu je `productColors-block`/`color-square` (Porto/Kallyas) **samo
> na 15793** — swatch „Silk Black" se renderuje kao prazan prostor. Tu i: spec kao `<ul>`
> umesto `al-table`, **dve galerije** na istoj stranici, `<h2>` bez `al-label`, nula
> `al-card`. Na 5119 nijedan legacy marker. ⚠️ Zastavica `porto` u auditu je **lažni
> pozitiv** — poklapa se unutar reči „s**porto**va".
>
> **Verifikovano:** homepage 6 kartica / 0 `al-ref-row` · O nama 11 kartica / 0
> `al-ref-row` / 1×H1 / 11 slika 200 · padel 4 `al-icon` · maloprodaja 9 `al-icon`,
> kartice proizvoda jednake visine (599 px), naslovi bez podvlačenja, obe `/proizvod/`
> putanje 200. Detalji: [[dnevnik/2026-08-12-vizuali-reference-ikonice]].

## 2026-08-12 [claude-code] W1 — Alt tekst na slikama proizvoda: 66 priloga popunjeno, 159 dekorativnih ikonica namerno ostavljeno prazno ✅

> Red čekanja iz 07-30 a11y plana („alt tekst — poseban budući zadatak"), uzet pred
> content freeze uz M odobrenje. Backup:
> `antasline_local_2026-08-12_pre-alt-tekst-galerije.sql` · skripta:
> `migracija/alati/job-alt-tekst-galerije.php`.
>
> **Obim izmeren, ne prepisan iz plana.** „67/81 proizvoda" iz 07-30 je zastarelo —
> obogaćivanje proizvoda je u međuvremenu popunilo najveći deo. Audit gleda **kanale
> renderovanja**, ne medijateku (medijateka: 7.725 slika, 6.638 bez alta — uglavnom
> Porto-era veličine i neupotrebljeni prilozi):
>
> | Kanal | Bez alta | Odluka |
> |---|---|---|
> | `_thumbnail_id` proizvoda | 6 | ✅ popunjeno |
> | `_product_image_gallery` | 63 (66 uniq. priloga) | ✅ popunjeno |
> | `<img>` u sadržaju, sa `wp-image-ID` | 0 | — |
> | `<img>` u sadržaju, bez ID-a | 159 | 🟢 **namerno prazno** |
>
> 🟢 **159 „nedostajućih" su dekorativne SVG ikonice** (`montaza.svg` 28×,
> `odrzavanje.svg` 27×, `izdrzljivost.svg` 25×, `izgled.svg`, `protivklizna.svg`,
> `fleksibilna.svg`, `sertifikat.svg`, `namena-*.svg`) uz tekst koji ih već imenuje →
> `alt=""` je ISPRAVNO po WCAG (lekcija 2026-08-05). Popunjavanje bi bilo regresija
> pristupačnosti. **Audit po kanalu svodi posao sa 6.638 na 66.**
>
> **Izvor teksta — tri nivoa, ništa izmišljeno:** **override, vizuelno pregledano (10)**
> — slika otvorena pre pisanja opisa (konj na perforiranoj podlozi ispred štale ·
> magacinski prolaz sa Ecotile pločama i pešačkim zonama · hala sa viljuškarom · ESD
> radionica · vinarija · kancelarija · kantina · EXPONA Flow sa tri žute vaze + 2
> deljena priloga) · **oznaka dezena iz imena fajla (4)** (Eden Ash, Rice Wine Oak
> 9028, Treehouse Oak 9036, Commercial 12523 — imenuje se oznaka, ne opisuje izgled) ·
> **naslov proizvoda (29)** + **„— fotografija N" (23)**.
>
> 🔴 **Deljeni prilozi:** jedan prilog = jedan alt bez obzira na broj galerija. `12503`
> stoji u **3** galerije (16520/16522/16524), `16861` u **2** (16514/16516) — oba
> dobila **neutralan, ne-proizvodni** opis. Skripta puca i ne upisuje ništa ako neki
> deljeni prilog nije pokriven override mapom.
>
> **Verifikovano:** audit ponovljen → thumb **0**, galerija **0**, 159 ikonica
> netaknuto. 6 proizvod stranica: 200 · 1×H1 · **0 slika iz `uploads` sa `alt=""`**.
> Regresija (home, `/industrijski-podovi/`, `/katalog/`, kategorija) 200/1×H1;
> JSON-LD čist (1× Product / 1× BreadcrumbList / 1× Organization).
>
> Detalji: [[dnevnik/2026-08-12-alt-tekst-slike-proizvoda]].

---

## 2026-08-12 [claude-code] W1 — red čekanja zatečen prazan, dva zastarela statusa ispravljena ⚠️

> Predložen (i prihvaćen) zadatak „Polish Faza 4 — GEO-intro na 22 posta" **bio je
> zatvoren 2026-08-07 (22/22)**. Master plan 1.2 stajao na „12/33, sledeći
> kancelarije/padel" dok je red čekanja **33/33 od 2026-07-08** — zastarelo mesec dana.
> Oba reda ispravljena; Faza 4 verifikovana **na buildu** (`.al-geo-intro` 1× na
> 3388/16616/6824/16612), ne samo u dokumentaciji.
>
> **Stanje W1 posle provere:** red A **33/33** · Polish Faze **1–4** ✅ · novi
> proizvodi **S1–S8 8/8** · Court builder CB1–CB3 + CB2-fix ✅ → **nema poznatog
> otvorenog posla u W1.** Preostali kandidati dolaze iz nalaza, ne iz reda čekanja:
> alt tekst (uzet ove sesije) i `heading-order`/`target-size` na product karticama
> (WoodMart core layout, kandidat za posle live-a).
>
> Lekcija upisana: „Sledeće" liste trule tiše od „Urađeno" — zatvaranje zadatka mora
> ažurirati i red u „Sledeće" i statusnu ćeliju u master planu.

---

## 2026-08-12 [claude-code] W1 quick-win — Chrome 149 `border-color` na tabelama: bloker zatvoren, build nije pogođen ✅

> Bloker otvoren isti dan (Chrome 149 izbacio `border-color: gray` iz UA stila za
> tabele) — vizuelno/računski proveren, **nema posla**. Read-only: 0 izmena na
> buildu, bazi i CSS-u, nema backup fajla.
>
> **Zašto nas ne dodiruje — dva nezavisna razloga:**
> 1. **Nijedna objavljena stranica ne koristi HTML atribut `border=`** (SQL provera
>    nad `wpGs_posts`, `post_status='publish'`) — a to je jedini slučaj koji je
>    stvarno zavisio od UA pravila `border-color: gray`.
> 2. **Svaka ivica ima eksplicitnu boju u autorskom CSS-u.** WoodMart reset postavi
>    `border:0` na `table/th/td`, pa `table th/td` vrati `border-bottom` sa
>    `var(--brdcolor-gray-200/300)`; `.al-table` deklariše svoju
>    `rgba(22,40,60,0.12)` (`antas-design.css:582`). Ni stacked mobilna varijanta
>    (`:has(thead th:nth-child(4))`) ne ostavlja boju nedeklarisanu.
>
> **Izmereno u Chrome 151** (nosi izmenu iz 149), `getComputedStyle` nad svakim
> `table/th/td`, filter „boja ivice == `color` (tj. `currentColor`)":
>
> | Stranica | Tip tabele | Boja ivice ćelije | Sumnjivih |
> |---|---|---|---|
> | `/industrijski-podovi/` (16567) | bare `<table>` | `rgba(0,0,0,0.106)` | 0 |
> | `/ftalati…/` (16612) | legacy `id=tabele width=95%` | `rgba(0,0,0,0.106)` | 0 |
> | `/prednosti-r-tile…/` (6824) | legacy `width="872"`, 39 elemenata | `rgba(0,0,0,0.106)` | 0 |
> | `/proizvod/konusni-stitnik-za-i-profil/` | `.al-table` spec | `rgba(22,40,60,0.12)` | 0 |
>
> Sam `<table>` element svuda ima `border-style: none` (WoodMart reset), pa je
> njegova `border-color` (koja jeste `currentColor`) bez ikakvog efekta — što je i
> razlog zašto izmena prolazi nezapaženo.
>
> 🆕 **Gotcha (okruženje, ne tema):** XAMPP Apache nije bio pokrenut, a prvi zahtev
> posle hladnog starta traje **134s** (opcache prazan) — CDP `Runtime.evaluate`
> pukne na 45s timeout-u i izgleda kao „renderer je zamrznut". Drugi zahtev 11,7s,
> treći 6,4s. Pre bilo kakvog Chrome merenja na lokalu: prvo `curl` da se opcache
> zagreje, pa tek onda pregledač.
>
> Zatvara 🟡 bloker iz [[PROGRESS]] (2026-08-12). Ostaje kao pravilo: nove tabele
> uvek sa eksplicitnom bojom ivice, nikad `border: 1px solid` bez boje.

---

## 2026-08-12 [claude-code] W5/GEO — GenAI baseline snimljen pre migracije: 17K prikaza, 2 stranice nose 54% ✅

> Prvo očitavanje Search Console **Generative AI features** izveštaja. Read-only,
> preko browsera (izveštaj je UI-only, nema ga u Search Analytics API-ju), bez
> izmena na sajtu i bazi.
>
> **~17.000 prikaza / 112 stranica** za 3 meseca (≈18.05–09.08) = **~13%** od
> ukupnih 129K Web prikaza. 🔴 Nije dodatan saobraćaj — AI prikazi su **podskup**
> `Web` tipa, već uračunati; ne sabirati.
>
> **Koncentracija ekstremna:** `/kako-napraviti-teren-za-basket…/` **6.901** +
> `/pop-tenis/` **2.250** = **54% svih AI prikaza**; prvih 10 stranica ~80%.
> AI vidljivost ovog sajta je **sportski sadržaj**, ne industrijski podovi —
> obrnuto od komercijalnog prioriteta.
>
> 🔴 **`/sportske-podloge/kosarkaske-konstrukcije/` ima 196 AI prikaza** — ista
> stranica koja je u [[CLAUDE]] §7.4 kritična rupa redirect mape (478 GSC
> klikova). Dodatan argument da F5 dobije pravu landing stranicu, ne 301 na shop
> kategoriju. Conquest 2542 radi i u AI odgovorima (488). Duplikat parket/pločice
> potvrđen i ovde (`-2` 459 vs original 81) — konsolidacija od 30.07 bila ispravna.
>
> 🆕 **Gotcha:** Chrome je bio prijavljen na **`cpgujam@gmail.com`**, koji nema
> pristup property-ju — GSC vraća „немате приступ овом производу", što lako
> izgleda kao da izveštaj nije dostupan. Drugi nalog
> (`miroslav.markovic109@gmail.com`) je već bio prijavljen, prebacivanje kroz
> avatar meni; URL posle prebacivanja nosi `/u/1/`. Direktan URL
> `/search-console/generative-ai` je **404** — izveštaj se otvara iz Performance
> strane („Open report" banner) ili `/performance/search-analytics/ai`.
>
> Detalji: [[dnevnik/2026-08-12-genai-baseline-sesija]] ·
> baseline: [[analiza/2026-08-12-genai-baseline]] · checklist stavka A štiklirana ·
> ponovno očitavanje ~07.09 (pad na pojedinačnoj stranici uz stabilan zbir =
> 301 problem, ne sadržajni).

## 2026-08-12 [claude-code] ALATI — Chrome dokumentacija ugrađena u skilove + novi `/antasline-ads` playbook ✅

> Druga sesija istog dana, **read-only prema sajtu i bazi** — nijedna izmena na
> buildu, nijedan SQL upis, nema backup fajla jer nije bilo destruktivnog rada.
>
> **DevTools 151** (instaliran Chrome je 151.0.7922.110, sve odmah dostupno) →
> `/woodmart-theme`: specificity tooltip u §7 (razlaganje `(a,b,c)` na hover =
> najbrži dokaz `:is()` zamke sa `base.css`), safe-area preseti u §11, nova §13
> sa tabelom zadatak→alat (lazy rendering Styles taba za elemente >200 CSS
> property-ja — WPBakery to redovno prelazi, Soft FCP markeri, source maps po
> defaultu, `Copy as cURL --url`). `/antasline-sesija` W3: **Lighthouse 13.4.0**
> — upisati verziju uz svaki baseline, inače julska poređenja ne važe.
>
> **Modern Web Guidance** (`developer.chrome.com/docs/modern-web-guidance`) nije
> članak nego **paket skilova** — instaliran u `~/.claude/skills/`, ~140 vodiča
> sa Baseline podacima. Zvanični `npx … install` zaobiđen (delegira na
> interaktivni instalater koji visi u CC shellu) → `npm pack` + ručni copy.
> Povezan iz `/woodmart-theme` §14 i `/antasline-sesija` W1/W3.
>
> **Novo: `[[reference/chrome-web-platform-2026]]`** — Chrome 148–151 kao filter,
> ne prepričavanje: 12 stavki upotrebljivih uz fallback, sekcija merenja,
> ⚠️ prerender, čekaj-Baseline, ignoriši, deprecations provereni na buildu
> (`new FontFaceSet()` — čisto).
>
> **Prompt API / Gemini Nano: NE** (odluka u [[odluke/_pregled-odluka]]) —
> srpskog nema među podržanim jezicima, samo desktop Chrome (a ~46/50 klikova na
> telefon je sa mobilnog), hardver traži >4 GB VRAM / 16 GB RAM (izmereno na
> radnoj mašini: **2 GB / 15,7 GB**), i nije Baseline.
>
> **Novi skill `/antasline-ads`** (W4): redosled dijagnostike isporuke,
> licitiranje, srpska morfologija u negativnim rečima, RSA, migracija-checklist
> za oglase, podela CC/M odgovornosti. `[[reference/claude-skilovi]]` dopunjen
> (7 skilova je falilo) + dve zastarele Windsor reference ispravljene.
>
> 🔴 **Gotcha koji je sesija otkrila o sebi:** prva verzija `/antasline-ads` je
> iz `[[dnevnik/ADS-DNEVNIK]]` preuzela „kumulativ 26, prag pređen" — a
> [[PROGRESS]] Blokeri (11.08) to demantuju: 17 od 26 su **klikovi na telefon**
> (`include_in_conversions_metric=True`), pravih plaćenih lidova ima **9**.
> Skill ispravljen pre zatvaranja. **ADS-DNEVNIK i PROGRESS su se razišli** —
> skill građen iz jednog izvora nasleđuje njegovu grešku.
>
> **Dodatak iste sesije — Google Search Central (4 dokumenta):** 🔴 [AI
> optimization guide](https://developers.google.com/search/docs/fundamentals/ai-optimization-guide)
> kaže izričito da **Google Search ne koristi `llms.txt`** („niti štete niti
> pomažu") — a mi smo `llms.txt`+`llms-full.txt` deployovali na live 23.07
> (zadatak 2.8). Naše merenje je to već pokazalo: [[analiza/BOT-CRAWLER-LOG]]
> beleži **0 organskih hitova** kroz dva preseka. **Odluka: fajlovi ostaju**
> (statični, bez održavanja, mogu koristiti ne-Google asistentima), ali se ne
> proširuju i **ne prate više kao GEO poluga** — stavka „ponoviti presek zbog
> llms.txt" iz [[seo/geo-ai-plan]] §5 zatvorena. Isti dokument obara i
> „seckanje sadržaja za AI", „pisanje posebno za AI" i structured data kao
> uslov za AI vidljivost — sve tri smo srećom ionako izbegavali.
> 🆕 **Generative AI performance report u Search Console** = jedini legitiman
> izvor za AI vidljivost, ide u mesečni snapshot. 🆕 **Platform properties**
> (globalno od 29.07) → `/w6-social`: Instagram odmah posle live-a, YouTube kad
> Faza 1 oživi kanal; vrednost su **upiti**, ne vanity metrika; meri samo Google
> pretragu, ne preglede na platformi. 🆕 `/gemini-vizuali`: IPTC
> `DigitalSourceType=TrainedAlgorithmicMedia` za AI slike **ako se ikad krene sa
> Merchant Center-om**. [[seo/geo-ai-plan]] dobio novu §0.
>
> 🆕 **Generative AI performance report** (dokumentacija pročitana): pokriva AI
> Overviews + AI Mode, ali daje 🔴 **samo prikaze** — bez klikova/CTR/pozicije;
> **nije odvojen skup** (uključuje `Web` tip, dakle naši GSC brojevi već sadrže
> AI prikaze, samo neizdvojene); ❌ **nije u API-ju** → ručno mesečno očitavanje,
> `gsc_report.py` ga ne može povući. 🔴 Usput nađena kontrola koja se lako
> previdi: **Settings → Search generative AI** (Include/Exclude/Inherit,
> podrazumevano *Include*) — ako je ikad prebačena na *Exclude*, ceo GEO rad
> nema efekta na Google strani, a nigde drugde se to ne bi videlo (isključivanje
> ne dira rangiranje ni indeksiranje). ✅ **M potvrdio isti dan: kontrola je na
> „Include" i izveštaj JE dostupan** za `sc-domain:antasline.com` — obe stavke
> zatvorene bez odlaganja. Ostaje 🔵 **prvo očitavanje kao baseline PRE
> migracije 24.08** (posle promene URL-ova poređenje „pre/posle" bez baseline-a
> nije moguće). [[seo/geo-ai-plan]] §0.1/§0.2.
>
> Detalji: [[dnevnik/2026-08-12-chrome-docs-ads-skill]]

## 2026-08-12 [claude-code] W3/BLOK C — `product_brand` arhive napunjene (Ecotile 7, Ergomat 27), 301 cilj više nije prazna stranica ✅

> Poslednji sadržajni prozor pre freeze-a 16.08. M odabrao opciju **(a)** iz
> blokera od 11.08 (dodeliti brend termine), ne prepravku 301 cilja.
>
> **Nalaz potvrđen merenjem:** taksonomija `product_brand` je uredno registrovana
> (`public`, `rewrite=brend`, samo `product`), ali **nijedan od 94 objavljena
> proizvoda** nije nosio termin — brojači „Ergomat 25 / Ecotile 3" dolazili su
> od **7 priloga iz Porto ere** (`Hollywood-Monster-*`). Obe arhive su renderovale
> praznu Woo petlju, a `.htaccess` 301 draft (linije 25–26) vodi live
> `/бренд/ecotile/` i `/бренд/ergomat/` baš tamo.
>
> **Urađeno:** Ecotile → **7 proizvoda** (16538/16540/16542 + 4 T-Joint/X-Joint
> rampe), Ergomat → **27** (16476–16528). Kriterijum je brend u naslovu ili u
> `post_content`; `16530` (Mosolut) i `16922` (PermaStripe/Heskins) namerno
> izostavljeni — Ecotile pominju samo u poredbenom tekstu. 7 priloga skinuto sa
> termina, brojači prebrojani (`wp_update_term_count_now`).
>
> Pošto su arhive time postale **indeksabilne** (`noindex_empty_taxonomies` ih
> više ne hvata) i cilj su 301 pravila, nisu ostavljene na generičkom Rank Math
> šablonu „%term% Arhive": upisan `rank_math_title`/`rank_math_description` po
> terminu (CTA `069 234 00 72`) + uvodni pasus u `term_description` koji nabraja
> stvarnu liniju proizvoda (GEO pravilo, ništa izmišljeno).
> `tax_product_brand_sitemap` `off`→**`on`** — sitemap index **6→7 child-ova**
> (isto koliko live emituje), **236→238 URL-ova**.
>
> **Verifikovano:** obe arhive 200 / 1×H1 / `index, follow` / 7 odn. 12 kartica /
> 2 validna JSON-LD bloka (`CollectionPage`+`BreadcrumbList`, bez dupliranja);
> `/brend/ergomat/page/2/` 200; regression na 2 proizvoda + kategoriji + `/katalog/`
> čist (1× `Product`). Backup: `antasline_local_2026-08-12_pre-product-brand.sql`.
>
> 🟢 **`.htaccess` draft se NE regeneriše** — ciljevi nepromenjeni, samo više nisu
> prazni; ograda u [[migracija/2026-08-10-pre-migration-checklist]] §B3 postala
> zastarela i uklonjena.
>
> 🆕 **Gotcha:** brisanje `rank_math_sitemap_cache_files` + pražnjenje tabele
> `rank_math_sitemap_cache` **nije dovoljno** — child sitemap se servira, ali
> `sitemap_index.xml` i dalje nabraja stari spisak dok se ne pozove
> `\RankMath\Sitemap\Cache::invalidate_storage()`. Važi i za korak B7 checkliste.
>
> Detalji: [[dnevnik/2026-08-12-product-brand-arhive]]

## 2026-08-11 [claude-code] W5 5.4 — korekcioni faktori upisani u skillove + mesečni snapshot za jul ✅🔴

> Dvanaesta stavka istog dana. Dva dela, oba read-only prema sajtu/bazi.
>
> **(1) Faktori upisani — lekcije od danas postale izvršne, ne samo zapisane.**
> `ga4_report.py` dobio opcioni **`--live-only`** flag (isključuje
> `localhost`/`127.0.0.1`/`staging.`/`test.`/`dev.`); **bez flag-a izlaz je
> bajt-identičan ranijem**, pa odluka o *trajnom* filteru ostaje Miroslavu.
> Izlaz sad uvek nosi i `hosts` raspodelu (kontaminacija vidljiva bez traženja)
> i `korekcija_merenja` blok (faktori ÷2 / ÷3 + `hvala_proxy_sessions`) — skripta
> faktore **izlaže, ne primenjuje**, sirovi brojevi ostaju sirovi. Testirano na
> julu: 40→36 hvala-proxy, 56→54 `generate_lead`. Ista pravila upisana u
> `.claude/skills/nedeljni-izvestaj/SKILL.md` (nova §0 sa dva tvrda pravila +
> obaveza čitanja PROGRESS-a pre povlačenja podataka) i u
> `.claude/skills/antasline-konektor/SKILL.md`.
> 🆕 Usput: `hosts` je otkrio i **`old.antasline.com`** (1 korisnik / 2 pregleda
> 01.06–10.08) — filter ga NE hvata (nije na prefiks listi), zanemarljivo ali
> zabeleženo.
>
> **(2) Mesečni snapshot za jul** (kasnio 11 dana) → **[[analiza/2026-08-11-snapshot-jul]]**.
> GA4/GSC/Ads povučeni sopstvenim konektorom + 4 ad-hoc read-only skripte u
> scratchpad-u (`snap_ga4.py`, `snap_gsc.py`, `snap_ads.py`, `snap_ads_convcheck.py`)
> — namerno **nisu** upisane u konektor.
>
> 🔴 **Glavni nalaz — „26 plaćenih konverzija" nisu lidovi.** Konverziona akcija
> **„Klik na telefon (web)"** ima `include_in_conversions_metric=True` i
> `primary_for_goal=True`, dakle **ulazi u „Conversions" kolonu i u Smart Bidding** —
> direktno kršenje pravila iz [[CLAUDE]] §4 („ne uvoziti GA4 `tel` kao Ads
> konverziju"). Od 01.06 do 10.08: **17 tel + 9 forma = 26**. **Prag 20–30 za
> zadatak 4.8 nije ni dostignut — pravih plaćenih lidova ima 9.** Postoje i **dve**
> aktivne telefonske akcije (druga, `CLICK_TO_CALL`, trenutno 0 — ako proradi,
> telefon se broji dvaput). #ceka-miroslav: prebaciti akciju u *Secondary*.
>
> 🔴 **Drugi nalaz — KPI baseline je pogrešna jedinica.** Plan kaže „prave
> konverzije 55/mes (jun)"; to su **pregledi**. Jun = **24 sesije**, jul = **16**,
> avgust (1–10) = **11**, kumulativ 01.06–10.08 = **119 pregleda / 51 sesija**.
> Cela KPI tabla ([[2026-07-06-MASTER-PLAN-V2]] §5) meri pregled-brojku, pa su
> ciljevi „≥55" i „70+/mes" postavljeni na ~2× naduvan baseline.
>
> 🟢 **Treći nalaz — organski pad je SERP, ne mi.** Jul YoY: pozicija **8,2→6,0**,
> prikazi **+22%**, ali CTR **6,76%→4,52%** i klikovi **−18%**. Upiti na poziciji
> 1,0–1,9 imaju CTR 2,3–8,3% (`dimenzije košarkaškog terena`: poz 1,9 / 732
> prikaza / CTR 2,3%) — odgovor se čita u SERP-u. GA4 to potvrđuje iz drugog ugla:
> engagement rate **62,1%, najviši u celoj 16-mesečnoj seriji**. Korisnici −32% MoM
> ali **+5% YoY** (jul 2025: 2.694 → 2026: 2.833) = sezona, ne regresija.
>
> 🔴 **Ads:** ~10.300 RSD/90d potrošeno na **6 BROAD ključnih reči sa 0 konverzija**
> (plan kaže „broad tek uz Smart Bidding" — u praksi radi *sada*, na Maximize Clicks).
> `industrijski podovi` (phrase) i dalje najjeftinija konverzija (903 RSD). Pauzirana
> kampanja **Terase je efikasnija od jedine aktivne ECOTILE** (13 konv za 34.318 RSD
> uz CPC 18,3 vs 13 za 24.148 uz 41,9) — pojačava jutrošnje pitanje o pauzi.
> Izgubljeni prikazi zbog **ranga 52–55%** na obe (budžet samo 13–19%) → QS problem.
> `podne obloge za terasu`: **4.223 RSD, 237 klikova, 0 konverzija** — relevantan
> upit iz ponude, dakle problem landinga, ne targetiranja.
>
> ⚪ **`mailto` = 0 u julu — ispravljeno u toku sesije (M):** snapshot je to prvo
> prijavio kao „6 nedelja tišine, uzrok nedijagnostikovan", što je **netačno**.
> Uzrok je nađen 27.07 (event je pratio **MonsterInsights**, ne GTM — gašenje MI-ja
> u BLOK A ga je oborilo 27.06), a popravka izvršena **07.08** (nov trigger + tag,
> **GTM Version 14**) → [[dnevnik/2026-08-07-gtm-mailto-tag]]. Merenje po danu se
> poklapa u dan i **potvrđuje popravku**: prvi događaj posle nje 07.08, pa 09.08 —
> ~0,5/dan, ista stopa kao pre prekida (jun 16/mes). Time se zatvara i otvorena
> stavka iz te sesije („proveriti za par dana da count raste"). **Jul = 0 je
> artefakt prekida, ne pad** — ne porediti jul sa junom/avgustom na ovoj metrici.
> 🔵 **Zašto je promaklo:** kad je stavka zatvorena 07.08, redovi su iz [[PROGRESS]]
> Blokera **obrisani**, pa u fajlu koji se čita na otvaranju sesije nije ostao trag;
> dnevnik fajl jeste postojao, ali se on po protokolu čita samo na zahtev.
>
> 🟢 AI saobraćaj jul: **28 sesija** (ChatGPT 26) vs baseline 9/90d. ⚠️ Mesečni AI
> test (5 promptova, 5.5) **nije ponovljen** — ostaje zaseban zadatak.
> ⚪ GMB: **429 quota**, peti neuspeli retest, nepromenjeno od 30.07.
>
> **Bez izmena na buildu, bazi i live sajtu.** Izmenjeni fajlovi: `ga4_report.py`,
> 2 × `SKILL.md`, nov `analiza/2026-08-11-snapshot-jul.md`. Nema DB backup-a
> (nijedna izmena nije dirala WordPress/SQL). Skripte (scratchpad, namerno nisu
> upisane u konektor): `snap_ga4.py`, `snap_gsc.py`, `snap_ads.py`,
> `snap_ads_convcheck.py`, `mailto_check.py`.
> Detalji: [[dnevnik/2026-08-11-mesecni-snapshot-jul]] · [[analiza/2026-08-11-snapshot-jul]].

## 2026-08-11 [cpanel-live] LiteSpeed CCSS/UCSS/LQIP/VPI status provera — UCSS oživeo posle 11 dana, LQIP nov lokalni bug nađen (fix odbijen) ✅

> Nastavak iste `[cpanel-live]` sesije. Miroslav primetio aktivnost na QUIC.cloud dashboardu
> i pitao da li LQIP i Page Optimization (CCSS/UCSS/VPI) rade — provereno kroz DB tabele
> plugina, fajlove na disku i izvorni kod, ne kroz UI.
>
> **CCSS**: ✅ radi aktivno (novi fajlovi danas 11:23/11:40/11:56/17:57). **UCSS**: 🟡 bilo
> mrtvo 11 dana (31.07→11.08, poznat nalaz iz 08-07 unosa), danas u 17:57 prvi put oživelo —
> verovatno baš ono što je Miroslav video na dashboardu. **VPI**: ⚪ namerno isključen u
> konfiguraciji, nije pokvaren, nikad ozbiljno korišćen. **Image Optimization** (stari
> problem): 🔴 nepromenjeno — identično julskom tiketu (1.157 RAW / 200 REQUESTED).
>
> 🔴 **LQIP — nov nalaz, drugačiji od starog problema**: tiho zaglavljeno od 25.07 (17 dana),
> uprkos novim proizvod-slikama dodatim 06–07.08. Uzrok u kodu (`placeholder.cls.php`):
> lokalna `File::is_404()` provera se radi PRE cloud poziva — ako padne, slika ide trajno u
> exclude listu i cloud se nikad ne kontaktira (zato cloud `last_request.lqip` ostaje
> zamrznut na 25.07 iako se lokalno nešto stalno odbija). Exclude lista ima slike sa
> datumima posle 25.07 (2026/01, 2026/03) — dokaz da je ovo aktivan, ne istorijski problem.
> **Ovo NIJE isti kanal kao stari QUIC.cloud firewall blok** — lokalni bug u proveri
> postojanja slike, ne cloud konekcija.
>
> **M odluka: LQIP fix se NE radi** — nije gate stavka, LQIP je kozmetički blur-up efekat
> bez merljivog uticaja na LCP. Nalaz ostaje dokumentovan za buduću referencu.
>
> Bez izmena na buildu/bazi/kodu — čisto read-only istraživanje. Detalji:
> [[dnevnik/2026-08-11-litespeed-ccss-ucss-lqip-vpi-status]].

---

## 2026-08-11 [cpanel-live] Live backup (DB+wp-content) na 2 lokacije + robots.txt AI-crawler pravila aktivirana i ispravljena ✅

> Nastavak iste `[cpanel-live]` sesije (LiteSpeed nalaz ispod). Zatvara 2 od 3 preostale
> gate stavke iz [[2026-07-06-MASTER-PLAN-V2]] §3.
>
> **Backup**: ručan DB dump (`wp db export`, 17,3MB) + `wp-content` tar.gz (1,29GB,
> 38.475 fajlova, `tar tzf` integritet potvrđen), MD5 checksume za oba. Miroslav skinuo
> na `C:\Miroslav\Antas line\Backup` i `G:\AntasLine-Backups` (2 lokacije potvrđeno) —
> kopije zatim obrisane sa servera (kvota bila tesna, 3,15GB od 12GB slobodno).
>
> **robots.txt**: Miroslav dodao 9 AI-crawler pravila (GPTBot, ChatGPT-User, ClaudeBot,
> Claude-Web, anthropic-ai, PerplexityBot, Perplexity-User, Google-Extended, CCBot, svi
> `Allow: /`). Provera otkrila 2 sitna problema: (1) dupliran `User-agent: *` blok — nije
> fatalno (Google unija duplikate), Miroslav sam spojio u jedan; (2) mojibake u komentaru
> (`â€”` umesto crtice) — isti poznat charset-bag kao `llms.txt`
> ([[reference/naucene-lekcije]] 2026-07-23), `.htaccess` fix (`^llms(-full)?\.txt$`)
> nije pokrivao `robots.txt`. Fix: proširen regex na `^(llms(-full)?|robots)\.txt$` —
> potvrđeno `Content-Type: text/plain; charset=utf-8` na oba fajla, sajt živ (200).
>
> **Bez izmena na sadržaju/bazi** — samo `.htaccess` charset dodatak (bezopasan,
> ne dira postojeći `llms.txt` blok) i privremeni backup fajlovi (obrisani).

---

## 2026-08-11 [cpanel-live] LiteSpeed Redis/Web Cache Manager — nove cPanel opcije istražene, NE rešavaju stari QUIC.cloud problem, Redis odložen ✅

> Miroslav primetio dve nove stavke u cPanel-u ("LiteSpeed Redis Cache Manager", "LiteSpeed Web
> Cache Manager") i pitao da li rešavaju stari poznati problem (QUIC.cloud `notify_img`/UCSS
> blokiran hosting firewall-om, v. 2026-07-10/07-30 unosi ispod).
>
> **Nalaz 1:** pravi izvorni kod oba modula pročitan direktno na serveru
> (`/usr/local/cpanel/base/frontend/jupiter/ls_web_cache_manager/`, zvaničan LiteSpeed
> Technologies plugin v2.4.9.1, instaliran na hostu 2026-08-07 — otud "nove opcije"). Web Cache
> Manager upravlja LSCache page-cache-om i SSL-om za QUIC.cloud CDN feature — grep celog `core/`
> stabla za `notify_img`/`imageoptm`/`ucss`/`ccss` daje **0 pogodaka**, ne dodiruje stari problem.
> Redis Cache Manager je potpuno odvojena funkcija (object cache za DB/PHP upite), nezavisna od
> render-blocking CSS ili image optimizacije.
>
> **Nalaz 2 — pokušaj uključivanja Redis-a:** isti uapi poziv koji UI dugme šalje
> (`uapi lsws redisAble action=enablesvc user=antasline size=64`) vraćen sa "Parent check method:
> /usr/local/cpanel/cpanel, caller: /usr/local/cpanel/uapi is not allowed" — `REDIS_ABLE` i
> `PACKAGE_USER_SIZE` su privilegovani `lswsAdminBin` pozivi, cPanel ih prihvata samo iz prave
> `cpsrvd` browser-sesije, ne sa terminala (namerna zaštita, nije zaobiđena). Dodatno: nigde na
> disku ne postoji `redis.size` fajl — nalog nema dodeljenu Redis kvotu na paketu; UI kod ima
> tačno poruku za taj slučaj ("Redis must be configured for you by your administrator"), pa bi i
> pravo dugme u browseru verovatno pokazalo isto.
>
> **Odluka: Redis se NE traži od hostinga pre 24.08.** Object cache ne rešava LCP gate
> (render-blocking CSS je krivac, TTFB je mali deo per [[dnevnik/PERFORMANCE-AUDIT]]
> `lcp-breakdown-insight`); katalog mod (M9) je uklonio cart/checkout pa je skoro sav saobraćaj
> anoniman i već pokriven LSCache page-cache-om bez PHP/DB izvršavanja — Redis tu nema šta da
> ubrza. Isti hosting je prošli QUIC.cloud firewall tiket držao otvoren 3 nedelje — nema margine
> pred content freeze (16.08)/gate (21.08)/go-live (24.08) da se čeka eksterna podrška za nešto
> što ionako ne dira poznati crveni gate item. Revizitovati posle live-a ako brojčana LCP potvrda
> pokaže spor TTFB i na keširanim stranicama, ili ako se doda dinamički sadržaj.
>
> **Bez izmena na buildu/bazi/live sajtu** — read-only pregled servera + jedan probni, blokiran
> (dakle bezefektni) uapi poziv. Detalji: [[dnevnik/2026-08-11-litespeed-redis-web-cache-manager]].

## 2026-08-11 [claude-code] [W1] Ergomat DuraStripe trake — slike po bojama + simple → variable ✅

> Dvanaesta stavka istog dana. M donео nove fotografije u `C:\Miroslav\Antas line\Proizvodo\Ergomat trake\` sa instrukcijom „dodaj ih; ako nisu varijabilni proizvodi — neka budu".
>
> **Ulaz:** 11 fajlova, od kojih su `Zuta.webp` i `Žuta.webp` **bajt-identični duplikat** (isti SHA-256) → 10 jedinstvenih. Svi već **800×800 WebP 1:1** — po specifikaciji, bez konverzije. Dve linije: **Xtreme** = pune boje (6), **Supreme V** = dvobojne hazard rolne sa dijagonalnim prugama (4). Mapiranje potvrđeno vizuelnim pregledom slika, ne samo iz imena fajlova.
>
> **Izvršeno:** oba proizvoda su bila `simple` → prebačena na `variable` sa `pa_boja` kao atributom varijacije, po obrascu koji na buildu već koristi PermaStripe (16922). **#16518 Xtreme**: 11 varijacija, 6 sa slikom. **#16520 Supreme V**: 11 varijacija, 4 sa slikom. Za Supreme V napravljena **3 nova `pa_boja` termina** — `Crno-bela`, `Crveno-bela`, `Zeleno-bela` (`Crno-žuta` je već postojala); hazard varijante su odvojene od punih boja koje je proizvod već imao.
>
> **Odluka u toku rada:** boje koje proizvođač nudi a za koje nemamo fotku (Braon, Ljubičasta, Svetlo plava…) **dobile su varijaciju bez sopstvene slike** (fallback na glavnu sliku) umesto da budu izbačene iz ponude — ne briše se tačna informacija zbog toga što fali fotografija.
>
> **Gotcha-i koji su se aktivirali:** (1) `wc_product_attributes_lookup` postaje stale posle programskog `wp_set_object_terms()` — regenerisana preko `LookupDataStore::create_data_for_product()` + obrisani `_transient_wc_layered_nav_counts_*`, inače filteri po boji broje pogrešno (`woodmart-theme` §8). (2) Dijakritika upisana isključivo preko PHP-a uz `wp-load.php`, nikad `mysql -e` (§10) — provereno `HEX`-om u bazi: `Žuta`=`C5BD…`, `Ljubičasta`=`…C48D…`.
>
> **Verifikovano:** obe stranice 200 · tačno 1×H1 · **1× Product schema** (nema dupliranja uprkos promeni tipa proizvoda) · `variations_form` prisutan · birač boje testiran u pregledaču (`Plava` → `durastripe-xtreme-plava.webp`, `Crno-bela` → `durastripe-supreme-v-crno-bela.webp`) · 0 slomljenih slika · bez horizontalnog overflow-a. Regresija čista: PermaStripe, Mean Lean, `/kategorija-proizvoda/industrijski-podovi/`.
>
> 🟡 **Svesno ostavljeno (M: „ostavi tako"):** boje se prikazuju kao **padajući meni**, ne kao swatch kvadratići — `pa_boja` je u bazi `attribute_type=select`. Isto važi i za PermaStripe, dakle nije regresija; prebacivanje na `color` bi pogodilo sve proizvode koji koriste boju.
>
> **Backup:** `antasline-backups/antasline_local_2026-08-11_pre-ergomat-trake-varijacije.sql` (36,6 MB) · **skripta:** `migracija/alati/job-ergomat-trake-varijacije.php` (izmeštena iz docroot-a)

---

## 2026-08-11 [claude-code] [W5] Inflacija `generate_lead` DIJAGNOSTIKOVANA — dva različita baga, jedan preživljava migraciju ✅🔴

> Jedanaesta stavka istog dana, nastavak iste sesije. Zatvara 🔴 bloker otvoren jutros („uzrok nije dijagnostikovan, kandidat: dupli page_view / trigger koji okida 3×"). Metod: čitanje objavljenog `gtm.js` kontejnera (bez slanja ijednog hita) + merenje stvarnih `analytics.google.com/g/collect` zahteva po `en=`/`_s=` u pregledaču, na live-u **i** na lokalnom buildu kao kontrolnoj grupi.
>
> **Kontejner, pravilo za hvala stranicu:** `IF [event=gtm.js AND putanja sadrži /hvala-za-poruku]` okida **četiri** taga — `generate_lead` (id 17) · `page_view` (id 18) · Ads konverzija `__awct` (id 20) · `fbq('track','Lead')` (id 38). Nezavisno od toga, Google tag `G-H8BRCZN8W4` (id 11, okidač `gtm.init`) šalje **svoj automatski `page_view`** na svakoj stranici.
>
> 🔴 **Bag A — suvišan `page_view` tag (id 18). Postoji i na buildu → PREŽIVLJAVA migraciju.** Jedno učitavanje daje `_s=1 page_view` (Google tag) + `_s=2 generate_lead` + `_s=3 page_view` (tag 18). Identično live i lokal. **Zato je hvala-proxy tačno 2× stvaran broj dolazaka**, i zato su svi dnevni brojevi parni — obrazac koji je jutros primećen ali nije objašnjen. Popravka je brisanje jednog taga; **nije izvršeno** (GTM izmena na produkciji = M odluka).
>
> 🔴 **Bag B — trostruki `generate_lead`. Samo live → NE prenosi se.** Live Kallyas stranica nosi **dva odvojena GTM embeda** istog kontejnera (jedan iz teme sa `data-cfasync="false"`, drugi kroz `litespeed/javascript`) + noscript iframe; `dataLayer` sadrži `gtm.js` **dvaput**. Izmereno: live 1 učitavanje = 2× `page_view` + **3×** `generate_lead` (poklapa se sa GA4 agregatom 26 pv / 39 gl). Lokalni WoodMart build ima **jedan** embed → **1×** `generate_lead`.
>
> ⚠️ **Najvažnija posledica — za prvi post-live izveštaj:** posle 24.08 obe brojke padaju same od sebe (`generate_lead` na ~⅓; hvala-proxy na ~½ ako se bag A ne popravi, ukupno ~⅙ ako se popravi). **To nije pad konverzija.** Baseline „~55/mes" i gate KPI su mereni naduvanom serijom.
>
> **Metodološka napomena:** naknadna reprodukcija drugog embeda **ne radi** — ni ubacivanje drugog `gtm.js` skript-taga posle učitavanja, ni drugi `gtm.js` push u `dataLayer` ne okidaju ništa (GTM čuva `google_tag_manager[id]`). Mora biti u početnom HTML-u. Zato je live merenje bilo neophodno.
>
> **Neprovereno (ne tvrditi bez merenja):** da li se Ads konverzija i `fbq Lead` isto multipliciraju — isto pravilo ih okida, ali Ads deduplicira po kliku.
>
> **Bez izmena na buildu, u bazi, u GTM-u i na live-u.** Live je samo učitan u pregledaču (2 učitavanja) — dodaje jednu sesiju u GA4 statistiku 11.08. Skripte (scratchpad, ad-hoc): `ga4_hvala_diag.py`, `parse_gtm.py`.
>
> **#ceka-miroslav:** obrisati GTM tag id 18 — preporuka **na dan migracije**, u isti paket sa Enhanced Conversions i Meta Pixel čišćenjem.
>
> Detalji: [[dnevnik/2026-08-11-generate-lead-inflacija-dijagnoza]]

---

## 2026-08-11 [claude-code] [W5 5.4] Ponovljen nedeljni izveštaj sirovim konektorom — obe današnje lekcije pregažene istog dana ⚠️

> Deseta stavka istog dana. Sesija je pokrenuta `/antasline-konektor` pa `/nedeljni-izvestaj` **bez čitanja [[PROGRESS]]/[[DNEVNIK-NAPRETKA]] prvo** — izveštaj za isti period (04–10.08) je već bio urađen ranije danas (stavka „Nedeljni izveštaj (04–10.08)"), pažljivije.
>
> **Provera konektora (jedini nesporan rezultat):** venv + svih 7 kredencijala na mestu, `token.json` osvežen 11.08 u 17:12 (posle `[cpanel-live]` re-autorizacije). **Ads i GMB pozivi ponovo prolaze** — `invalid_grant` iz cPanel sesije je zatvoren, `ads_report.py` je vratio pune podatke.
>
> ⚠️ **Izveštaj poslat Miroslavu nosi sirove, nefiltrirane GA4 brojke** — 667/810 korisnika i 785/935 sesija umesto live-only 633/802 i 730/906; kumulativ hvala-proxy prijavljen kao **127 umesto 119** (live). Lekcija „GA4 totali iz konektora uključuju `localhost`" je bila upisana u [[reference/naucene-lekcije]] **istog dana, nekoliko sati ranije** — i svejedno je pregažena, jer `ga4_report.py` i dalje vraća nefiltrirane totale, a `/nedeljni-izvestaj` skill nigde ne pominje `hostName` filter.
>
> ⚠️ **Isti obrazac i sa drugom lekcijom:** nesklad `generate_lead` 41 vs 30 pregleda je u izveštaju predstavljen kao **nov nalaz** i stavljen u „Akciju nedelje" („proveri dupli Page View triger"), iako je ranije danas već izmeren dublje i tačnije (inflacija ~3×: 26 pregleda / 10 sesija / 39 evenata na live-u, deterministički odnos 1,5×). Zaključak se ne menja — dijagnoza pre migracije ostaje — ali je „otkriće" bilo ponovno otkrivanje.
>
> **Zaključak za način rada:** obe zamke su procesne, ne tehničke — brane se čitanjem [[PROGRESS]] pre svakog zadatka ([[CLAUDE]] §12), što ova sesija nije uradila. Trajna zakrpa je da filter prestane da bude stvar pamćenja: `hostName == www.antasline.com` ugraditi u `ga4_report.py` (ili bar kao `--live-only` flag) + jedan red u `/nedeljni-izvestaj` skill. **Nije urađeno — čeka odluku (#ceka-miroslav).**
>
> **Bez izmena na buildu, u bazi i u skriptama** — read-only sesija, nema backup fajla.

---

## 2026-08-11 [cpanel-live] [W6 / 4.9] Customer Match upload pokušan uživo — blokiran na Data Manager API migraciji, koriguje raniju pretpostavku (Standard access) ✅🔴

> Deveta stavka istog dana, jedina u pravoj `[cpanel-live]` sesiji (`wp1.oblak.host`) — nastavak `categorize_leads.py`/`customer_match_upload.py --split-by-category` rada napisanog u prethodnoj sesiji (commit `4067cd2`, još nije bio testiran uživo).

> `scan_leads.py --dry-run`: 0 novih kontakata, `leads.csv` na serveru već ima 9 (iz prethodne sesije) — 5 `nepoznato` · 3 `sportski-tereni` · 1 `terase-spoljne-podloge`, svih 9 `uploaded=False` cele sesije (nijedan upload nije uspeo).
>
> **Prvi `--confirm` pokušaj: `invalid_grant`** — token osvežen 07.08, mrtav 11.08 (4 dana), potvrđuje postojeću lekciju o *Testing* statusu. M re-autorizovao lokalno preko `authorize_oauth.py`, naišao na **novi ekran „App not verified"** (ista *Testing*-status posledica, nova varijanta) — rešeno „Advanced → Go to mcp-za-claude (unsafe)" (app je naša sopstvena, upozorenje je samo neprovereno stanje kod Google-a, ne stvarna opasnost); dokumentovano kao Korak F u [[reference/api-konektor-setup.md]]. Novi `token.json` prekopiran na server (19:17).
>
> 🔴 **Drugi `--confirm` pokušaj: auth prošao, ali `CUSTOMER_NOT_ALLOWLISTED_FOR_THIS_FEATURE`** — Google Ads API eksplicitno vraća „Customer Match uploads aren't supported... Use the Data Manager API". Nusefekat na **živom nalogu**: prazna user lista `AntasLine - Website Leads` (`customers/1568860314/userLists/9444454571`) je stigla da se kreira pre nego što je pao poziv za dodavanje članova — **M ručno obrisao u Ads UI-ju** (Audience manager → Segments), potvrđeno.
>
> 🔴 **Koriguje raniju pretpostavku iz [[2026-07-06-MASTER-PLAN-V2]] 4.9 (2026-08-07):** tada je isti error kod protumačen kao „Basic developer token, treba Standard access u API Center". Istraživanje danas (WebSearch/WebFetch nad zvaničnom Google dokumentacijom + migration guide-om) pokazuje drugačiji, dokumentovan uzrok: od GA Data Manager API-ja (dec. 2025) / obavezno od **01.04.2026**, developer tokeni koji **nikad ranije nisu slali Customer Match zahtev** preko starog `OfflineUserDataJobService`-a su blokirani **bez obzira na tier** — moraju na novi, poseban **Data Manager API**. Standard access zahtev možda uopšte nije potreban za ovo konkretno ograničenje (nije testirano, samo dokumentacija ukazuje).
>
> **Šta bi trebalo za migraciju** (nije urađeno ove sesije, samo istraženo): (1) uključiti `datamanager.googleapis.com` u `mcp-za-claude`, (2) verovatno nov OAuth scope → ponovna autorizacija, (3) `pip install google-ads-datamanager` lokalno i na serveru, (4) prepisati upload deo `customer_match_upload.py` — `IngestionServiceClient.ingest_audience_members()` zamenjuje `OfflineUserDataJobService` job-flow; kategorizacija (`categorize_leads.py`) i SHA-256 hešovanje ostaju nepromenjeni. Dokumentovano kao Korak G u [[reference/api-konektor-setup.md]].
>
> **Bez izmena u kodu ove sesije** (samo istraživanje + dokumentacija), `leads.csv` netaknut. Ranija sesija (isti dan, van cPanel-a): potvrđen i pushovan commit `4067cd2` (`categorize_leads.py` + `--split-by-category`).
>
> Detalji: [[dnevnik/2026-08-11-customer-match-data-manager-api]]

---

## 2026-08-11 [claude-code] [W5 5.4] Nedeljni izveštaj (04–10.08) — merenje „pravih konverzija" je naduvano ~3× ✅🔴

> Osma stavka istog dana. Izveštaj je kasnio 2 nedelje (poslednji 30.07 za 23–29.07), N6' ga izričito vodi kao „kasne". Format [[CLAUDE]] §10, izvor: sopstveni konektor.
>
> **Brojke (live-only, 04–10.08 vs 28.07–03.08):** korisnici 633 (802) · sesije 730 (906) · `tel` 9 (19) · `mailto` 2 (0) · hvala-proxy **26 pregleda / 10 sesija** (6 / 3). Ads: ukupno **6.890,61 RSD** (10.450,55) / 200 klikova (420) / **5 konverzija** (6). ECOTILE 4.247,67 RSD / 42 klika / **CPC 101,13** (64,04, **+58%**) / 2 konv · Terase 2.642,94 RSD / 158 klikova / CPC 16,73 / 3 konv.
>
> 🔴 **Glavni nalaz — brojka kojom merimo uspeh ceo projekat broji preglede, ne lidove.** Na live-u je ove nedelje **10 sesija / 8 korisnika** stiglo na `/hvala-za-poruku/`, a GA4 beleži **26 pregleda i 39 `generate_lead`** evenata (≈2,6 pregleda i ≈3,9 evenata po sesiji). Obrazac je **deterministički od jula**: svakog dana `generate_lead = 1,5 × broj pregleda`, a svi dnevni pregledi su parni brojevi — dakle nije nasumično osvežavanje stranice od strane korisnika. Kumulativ od 01.06: **119 pregleda = 51 sesija / 43 korisnika**. Uzrok NIJE dijagnostikovan ove sesije (kandidat: dupli `page_view` + GTM Page View trigger koji okida 3×) — ništa nije dirano.
>
> ⚠️ **Zašto je hitno pred migraciju:** (1) na dan migracije se pušta GTM paket Enhanced Conversions-a koji visi na istom `generate_lead` tagu; (2) svako post-live poređenje („da li su 301 oborile konverzije?") meri se baš ovom serijom; (3) baseline „~55/mes" i gate KPI su isto pregledima mereni. Ads-ova strana broji svoje (5 konverzija) i nije naduvana u istoj meri — ne izvoditi zaključak o Ads performansama iz GA4 brojača.
>
> 🔴 **Drugi nalaz — konektorovi totali nisu live brojke.** `ga4_report.py` vraća `activeUsers`/`sessions` **bez ijednog filtera**, a lokalni build od 22.07 nosi pravi GTM kontejner i šalje u istu property. Prethodna nedelja: **1.068 pregleda sa `localhost`** vs 1.504 sa live-a (42% ukupnog!), ova nedelja 213. Sirovi izlaz skripte je zato pokazivao 810→667 korisnika, a stvarni live pad je 802→633. Kontaminacija ključnih evenata je mala ali ne nula (2 `generate_lead` sa localhost-a, 2 `tel` sa staging-a).
>
> **Ads napomene:** ECOTILE CPC +58% uz manje klikova; 08–10.08 potrošio 2.357 RSD na 24 klika sa **0 konverzija** (budžet odluka od 06.08 i dalje čeka M). Terase su potrošnju prepolovile (−62%) pre nego što su pauzirane — pauza potvrđena današnjim 4.10 auditom, **nije potvrđeno da je namerna** (#ceka-miroslav). **Plaćene konverzije kumulativ: 26** (bilo 24 na 06.08) — prag 20–30 pređen, ali preporuka za 4.8 ostaje nepromenjena: **odložiti na ~01.09**, jer bi period učenja Smart Bidding-a pao tačno na dan migracije.
>
> **GSC 28d (12.07–08.08), pozicije 5–15 sa niskim CTR:** epoksidni podovi cena po m2 (361 pr. / poz. 9,6 / 0,83%) · podovi za terase (269 / 9,7 / 2,23%) · industrijski podovi (164 / **12,4** / 1,22%) · piklbol (134 / 14,2 / **0%**) · epoksidni podovi (125 / 9,9 / 0,80%). Dva od pet su epoksid-conquest upiti (post 2542).
>
> **Skripte (scratchpad, ad-hoc — nisu upisane u konektor):** `ga4_hostname_check.py` (eventi + pregledi po `hostName` i po danu), `ga4_live_only.py` (totali filtrirani na live + dnevna serija), `ga4_hvala_paths.py` (hvala po tačnoj putanji + sessions/users vs eventCount). Ako se `hostName` filter usvoji trajno, ide u `ga4_report.py` — nije menjano bez odluke.
>
> **Bez izmena na buildu i bez izmena u bazi** — read-only sesija, nema backup fajla.
>
> Detalji: [[dnevnik/2026-08-11-w5-nedeljni-izvestaj]]

---

## 2026-08-11 [claude-code] [W3 / checklist §A] GSC priprema — build je emitovao 3 sitemap-a gde live emituje 7 ✅🔴

> Sedma stavka istog dana. Poslednja neštriklirana CC stavka iz [[migracija/2026-08-10-pre-migration-checklist]] §A. Delovala je administrativno („sitemap URL spreman za resubmit, alerti uključeni") — ispala stvarna rupa.
>
> 🟢 **Dobra vest prvo:** URL za resubmit je **nepromenjen** (`https://www.antasline.com/sitemap_index.xml`), a child sitemap-i na buildu nose **identična imena fajlova** kao Yoast na live-u → nijedan submit-ovan URL ne puca migracijom.
>
> 🔴 **Nalaz: svih 12 `tax_*_sitemap` ključeva u `rank-math-options-sitemap` je bilo `off`.** Yoast→Rank Math importer (05.08) prenosi naslove/opise i opšta podešavanja, ali **ne** i koje taksonomije idu u sitemap — a Yoast ih je na live-u imao uključene. Live 7 child-ova / 145 URL, build 3 / 196.
>
> **Izmereno šta se gubilo** (GSC dimenzija `page`, 11.05–08.08): 27 URL-ova van sitemap-a nosi **79 klikova / 2.583 prikaza** — `/category/` 56 kl. (najjači `/category/industrijski-podovi/`: 44 kl. / 1.487 pr. / poz. 12,3), `/kategorija-proizvoda/` 21 kl., `/oznaka-proizvoda/` 2, `/бренд/` 0.
>
> ⚠️ **Ograda:** izostanak iz sitemap-a **nije `noindex`** — svih 27 URL-ova na buildu vraća `index, follow`. Gubilo se otkrivanje, ne indeksabilnost. Ali migracija je najgori mogući trenutak za to, jer se ceo URL skup tad ponovo pušta kroz crawl.
>
> 🔴 **Zašto nijedna dosadašnja provera ovo nije uhvatila:** i regression sweep (10.08) i dijakritika sweep (11.08) idu **kroz** sitemap. Ono čega u sitemap-u nema, sweep ne vidi — ista slepa tačka koja je 11.08 sakrila 2 slike 404 na `noindex` postu 16613.
>
> **Urađeno:** uključeni `tax_category` + `tax_product_cat` + `tax_product_tag` → **196 → 236 URL-ova** (6 child-ova). Svih **42** novih taksonomijskih URL-ova verifikovano: **200 / tačno 1×H1 / `index, follow`**, 0 problematičnih signala; svaka arhiva ima stvaran sadržaj (3–12 proizvoda / 3–10 postova).
>
> 🔴 **`tax_product_brand_sitemap` uključen pa ODMAH vraćen na `off`** — brend arhive su **prazne**: `/brend/ecotile/` renderuje „nema proizvoda" i 0 linkova ka proizvodima, dok live `/бренд/ecotile/` listira 3. Zamka u brojačima: `term_taxonomy.count` kaže Ergomat 25 / Ecotile 3, a stvarnih veza ima **0 proizvoda** (samo 7 **priloga**) — brojači zaostali iz Porto ere, nikad prebrojani.
>
> 🔴 **Posledica na 301 mapu (nalaz van prvobitnog obima):** draft linije 25–26 (`/бренд/ecotile/`→`/brend/ecotile/`, isto ergomat) **vode na prazne stranice**. `htaccess-301-generate.php` to nije uhvatio jer proverava samo da cilj vraća **200** — prazna Woo arhiva jeste 200. Gore od toga: `/бренд/ecotile/` je jedno od **5 pravila iz B3 spot-check liste** za dan migracije, pa bi spot-check uredno prošao („301 na tačan Location") uz beskorisno odredište. 30 prikaza / 0 klikova za 3 mes. → **ne blokira**, ništa nije menjano, **#ceka-miroslav** (3 opcije u [[PROGRESS]] Blokeri).
>
> **Nov alat:** `.claude/skills/antasline-konektor/scripts/gsc_sitemaps.py` — Sitemaps API, read-only; servisni nalog ima samo `webmasters.readonly` scope, pa skripta fizički **ne može** submit-ovati ni obrisati sitemap. Njime otkriveno da su u GSC-u **dva** submit-ovana sitemap-a: `http://` (submit **2018-04-09**, Google ga i dalje povlači — poslednji put 10.08, 3 upozorenja) i `https://` (2024-12-25, 4 upozorenja). Brisanje `http://` unosa, pregled upozorenja i provera email alerta idu u GSC UI → **#ceka-miroslav**.
>
> **Odluka koja je namerno NIJE doneta:** 18 `product_tag` arhiva (10 od njih novih `namena-*` iz W1 1.11) je tanko sa klasične SEO strane, ali su već `index, follow` i live ih ima u sitemap-u. Na migraciju ide **parity, ne optimizacija** — prored tag arhiva je zaseban zadatak posle live-a (upisan u checklist B7).
>
> ⚠️ **Nove gotcha-e:** (1) Yoast→Rank Math importer ne prenosi taksonomijske sitemap-e — proveriti `tax_*_sitemap` posle svake migracije SEO plugina; (2) **Rank Math kešira sitemap-e u fajlove** — izmena opcije direktno u bazi ne obara keš, treba obrisati opciju `rank_math_sitemap_cache_files` **i** `wp-content/uploads/rank-math/rank_math_*.xml`; (3) `wp_term_taxonomy.count` nije dokaz da termin ima sadržaj; (4) `301 → 200` nije dovoljna provera cilja redirekta; (5) sweep kroz sitemap ne može naći ono čega u sitemap-u nema; (6) `wp-load.php` bootstrap u `php -r` prešao 120s na ovom buildu — za jednu opciju brže direktno preko `mysql` + `unserialize()`.
>
> **Verifikacija:** 42/42 novih URL-ova čisto · `post`/`page`/`product` sitemap brojevi nedirnuti (31/70/95) · regresija `/`, `/industrijski-podovi/`, `/kontakt/`, `/kategorija-proizvoda/industrijski-podovi/` → 200/1×H1 · `robots.txt` već pokazuje na produkcijski sitemap URL, nedirnut.
>
> **Backup:** `antasline-backups/rank-math-options-sitemap_2026-08-11_pre-tax-sitemaps.sql` (gotov `UPDATE` sa originalnom vrednošću).
>
> Detalji: [[dnevnik/2026-08-11-gsc-priprema-sitemap]]

## 2026-08-11 [claude-code] [W4 4.10] Final URL audit oglasa — ZATVOREN: aktivna kampanja čista, 2 URL-a vode na tuđi domen ✅🔴

> Dopuna istog dana, posle M-ove re-autorizacije: Ads export je izvršen i audit je kompletan.
>
> **Konačan rezultat (41 URL):** `OK` 32 · `PREPISATI` 6 · **`EKSTERNI-DOMEN` 2** · `PUKAO` 1 (`/404.html`, artefakt live 404 stranice) · `REDIRECT-BUILD` 0. Spisak: `analiza/2026-08-11-ads-url-audit.csv`.
>
> 🟢 **Aktivni saobraćaj je čist — na dan migracije se ne dira ništa.** Od **14 kampanja samo je jedna ENABLED** („ECOTILE INDUSTRIJSKI PODOVI"): 1 RSA + 6 sitelinkova, svih 7 URL-ova 200 na buildu. ⚠️ Usput ispravka činjenice: **„Podloge za terase i bazene" je PAUSED** — [[CLAUDE]] §6 i master plan na više mesta govore o „obe aktivne kampanje", to više ne važi.
>
> 🔴 **2 URL-a vode na TUĐI domen:** `http://www.ekopodneploce.rs/` (3 oglasa u pauziranoj „Ecotile kampanja") i `.../proizvodi/E%20500-7/E500-7.html` (sitelinkovi „Industrijski podovi" + „Podovi za magacine"). Ne troši ništa danas, ali **301 mapa tu ne pomaže** — nije naš domen; reaktivacija te kampanje = plaćen klik koji odlazi sa antasline.com. Ništa nije dirano, **#ceka-miroslav**.
>
> **6 × PREPISATI** (sve pauzirano, 301 ih pokriva): `/home/industrijski-podovi/` (8 oglasa + 1 sitelink) · `/sportski-podovi/` (2+3) · `ecotile-5005-podne-ploce` · `ecotile-5007` · `trakezaobelezavanje` · `/ergonomski-podovi/`. Blokira reaktivaciju tih kampanja (uklj. W4 4.4), ne migraciju.
>
> **Provereno da NEMA** (ne pretpostavljeno): 0 keyword-level final URL-ova · 0 `tracking_url_template`/`final_url_suffix` na svih 14 kampanja · 0 `final_mobile_urls`.
>
> 🔴 **GA4 presek se pokazao nedovoljnim, crno na belo:** nije uhvatio **nijedan** od 8 problematičnih URL-ova (svi u pauziranim kampanjama i sitelinkovima bez klikova). Jutrošnja ograda „presek nije zamena za export" nije bila teorijska.
>
> ⚠️ 3 buga u sopstvenom kodu uhvaćena prvim pokretanjem: GAQL traži `campaign.status` u `SELECT` kad se po njemu filtrira · `print` puca na `UnicodeEncodeError` čim ćirilični izlaz ide u fajl na Windows-u · audit alat je normalizovao **eksterni domen u putanju** (dao bi lažan `PUKAO`) → dodata klasa `EKSTERNI-DOMEN`.
>
> **OAuth:** token je bio mrtav (v. ispod), M ga re-autorizovao; uzrok je sistemski (*Testing* status → 7 dana), pa je u checklistu dodata stavka **B1 — provera tokena pre svega ostalog na dan migracije**.

---

### Prvi deo iste sesije (pre re-autorizacije)

**Zadatak:** stavka „Final URL audit oglasa — priprema" iz [[migracija/2026-08-10-pre-migration-checklist]] §A (izvršenje je 4.10 na dan migracije). Šesta stavka istog dana.

**Napisane 3 nove alatke** (sve read-only): `scripts/ads_final_urls.py` (Google Ads API — final + mobile URL svakog oglasa, keyword-level URL-ovi, sitelink/asset URL-ovi na sva tri nivoa, tracking template; konektor ovo nije imao), `scripts/ga4_paid_landing.py` (GA4 landing stranice za `sessionMedium=cpc`, servisni nalog → radi bez OAuth-a) i `migracija/alati/ads-url-audit.php` (poredi bilo koji spisak URL-ova sa lokalnim buildom **i** sa 73 pravila iz `htaccess-301-DRAFT.txt` → `OK` / `PREPISATI` / `REDIRECT-BUILD` / `PUKAO` + CSV; prima `--json`, `--ga4`, `--txt`).

**Rezultat GA4 dela** (11.05–10.08): 31 jedinstvena landing putanja plaćenog saobraćaja → **29 `OK`, 0 `PREPISATI`, 0 `PUKAO`**; jedini „pad" je `/404.html` (artefakt live 404 stranice, 1 sesija, nije URL oglasa). Najprometnije odredište `/spoljnje-podne-obloge/` (1.423 sesije) i `/industrijski-podovi/` (575) — obe 200 na buildu. Detektor propušten kroz kontrolni spisak poznato-loših URL-ova (pravilo od jutros): tačna podela `PREPISATI` ×3 uklj. ćirilično `/бренд/ecotile/`, `PUKAO` ×2, `OK` ×1.

🟢 **`?gclid=` preživljava 301 — izmereno, ne pretpostavljeno.** Da `.htaccess` odseca query string, svaki preusmeren klik iz oglasa izgubio bi `gclid` i konverzija se ne bi pripisala Ads-u. Test u izolovanom `htdocs/redirtest2/` (obrisan posle merenja): `?gclid=…&utm_source=google` stiže netaknut u `Location`, i na običnom i na ćiriličnom pravilu — `mod_alias` sam dodaje originalni query kad cilj nema svoj. Draft se ne menja.

🔴 **Ads export nije izvršen — `invalid_grant`, token istekao/opozvan.** `token.json` osvežen 06.08, mrtav 11.08 = 5 dana; verovatan uzrok je OAuth consent screen u statusu *Testing* (Google gasi refresh token posle 7 dana). GA4/GSC to ne osećaju jer idu preko servisnog naloga. 🔴 **Isti problem udara i 24.08**, kad se radi 4.10 i verifikacija konverzija — mrtav token tada troši vreme u najgorem trenutku. Trajno rešenje: Cloud Console → OAuth consent screen → **Publish app** (*In production*), 2 min. Zakrpa: `authorize_oauth.py` ponovo + obavezno još jednom ujutru 24.08. ⚠️ Usput: `token.json` nosi samo scope `adwords`, `tagmanager.edit.containers` (dodat 27.07) nije u njemu.

⚠️ **GA4 presek NIJE zamena za Ads export** i audit se ne zatvara na njemu: ne vidi oglase/sitelinkove bez klikova (baš oni nose zaboravljene URL-ove), ni keyword-level URL-ove, ni tracking template, i beleži odredište **posle** redirekta — URL koji danas prolazi kroz Redirection plugin izgleda ispravno, a posle migracije živi samo ako je među 73 pravila.

**#ceka-miroslav:** re-autorizacija (komanda u dokumentu), pa se audit dovršava sa dve komande. Baseline sačuvan: `analiza/2026-08-11-ga4-paid-landing-3m.json` + `analiza/2026-08-11-ads-url-audit-ga4-deo.csv`. Detalji: [[migracija/2026-08-11-ads-final-url-audit]] · [[dnevnik/2026-08-11-ads-final-url-audit]].

## 2026-08-11 [claude-code] [W3 3.9] .htaccess 301 reverifikacija — draft je bio 8 pravila, treba 73; petlja i 2 pregažene stranice uhvaćene ✅🔴

**Zadatak:** stavka „`.htaccess` 301 poslednja reverifikacija" iz [[migracija/2026-08-10-pre-migration-checklist]] §A. Signal koji ju je izvukao: draft je od **27.07**, a `redirect-mapa-FINAL.csv` menjana **30.07** — draft je bio stariji od svog izvora.

**Napisana 2 alata** (read-only prema bazi i prema živom `.htaccess`-u): `migracija/alati/redirect-verify.php` (duplikati / petlje / lanci / prefiks-kolizije / HTTP status svakog cilja **i svakog izvora**) i `migracija/alati/htaccess-301-generate.php` (generiše draft iz obe mape, **odbija upis ako ijedan cilj nije 200**). Draft se od sada ne piše ručno — mape su izvor istine.

**Rezultat: 8 → 73 pravila.** Backup stare verzije: `htaccess-301-DRAFT.txt.bak-2026-08-11`.

**Nalazi:**
1. 🔴 **62 istorijska pravila uopšte nisu bila u draftu.** Ona danas žive u **Redirection pluginu na live-u, dakle u bazi** — a migracija zamenjuje živu bazu lokalnom, pa nestaju sa plugin-om. Bez ovog bloka bi na dan migracije ~46.000 zabeleženih GSC pogodaka (`/sportski-podovi/` 7.800, `/izgrdanja-sportskig-terena/` 6.043, `/podovi-za-baste-splavove-bazene/` 5.740 …) tiho palo na 404. Analiza tih pravila postoji od 21.07 ([[migracija/2026-07-21-analiza-65-redirection-pravila]]), samo nikad nije preneta u draft.
2. 🔴 **Petlja između dve mape:** FINAL red 2 vodi `/na-kojoj-podlozi-se-igraju-turniri-u-3x3/` → `/bergo-ultimate…/`, istorijski red 47 vodi **tačno obrnuto**. Zajedno = beskonačna petlja na oba URL-a. Razrešeno merenjem: članak živi na `/bergo-ultimate…/` (post 4813, 200), `/na-kojoj-podlozi…/` je 404 → FINAL smer tačan, istorijsko pravilo se ne prenosi.
3. 🔴 **Dva istorijska pravila bi ubila stranice koje smo izgradili** — nađeno tek zato što se proveravaju i **izvorni** URL-ovi, ne samo ciljevi: `/lvt-…/vinil-podovi-za-restorane-hotele-kafice…/` (588 GSC) je danas prava stranica **16686** (W2 2.5), `/podovi-za-garaze/` (182 GSC) je **16875** (W2 Tier1). Oba pravila su iz vremena pre nego što su te stranice postojale; mehanički prepis bi ih pregazio. Ne prenose se.
4. 🔴 **`Redirect` je prefiks-match → 15 kolizija.** `Redirect /podovi-za-terase/` guta 4 specifičnija pravila i lepi ostatak putanje na cilj; `/home/industrijski-podovi/` guta 8. Ceo draft prebačen na sidreni **`RedirectMatch 301 "^/putanja/?$"`** — kolizije nestaju i **redosled linija postaje nebitan**.
5. 🟡 **Pogrešan cilj u FINAL mapi:** red 14 je ciljao `/spoljne-podne-obloge/bergo-easy/` (bez „j") → 301 umesto 200; ostatak M odluke od 30.07. Ispravljeno u mapi.
6. 🟢 **Ćirilica radi — stara ograda skinuta.** Draft od 27.07 je tražio staging-test i `RewriteRule \x` fallback za `бренд/ecotile`; testirano pod Apache-om, `RedirectMatch` sa doslovnim UTF-8 putem daje ispravan 301.
7. ⚪ `/sta-postaviti-preko-starog-parketa-ili-plocica/` vraća 200 na lokalu iako ga pravilo preusmerava — **namerno** (M 30.07: 16613 ostaje noindex kao rezerva, 301 tek na produkciji). Nije bug.

**Verifikacija:** `redirect-verify.php` posle popravki → 0 duplikata / 0 petlji / 0 lanaca / 0 ciljeva ≠200 (44 jedinstvena cilja). **Funkcionalni test pod Apache-om:** draft prepisan u izolovan `htdocs/redirtest/` sa prefiksiranim putanjama → **8/8 tačan 301 i tačan `Location`** (prefiks-zamka na 3 nivoa, ćirilica, bez kose crte, `/home/` grupa), negativna kontrola 3/3 bez lažnog 301; folder obrisan posle merenja.

**Ostaje:** draft se NE aktivira do 24.08. Ako se do tada promeni ijedan slug — pustiti oba skripta ponovo (generator sam pukne ako cilj nije 200). Detalji: [[dnevnik/2026-08-11-htaccess-301-reverifikacija]].

**Dodatak iste sesije (M pitanje): `.htaccess` vs Rank Math Redirections — poređenje.** Provereno u kodu/bazi, ne iz sećanja: modul postoji u besplatnoj verziji ali je **isključen** (`rank_math_modules` nema ni `redirections` ni `404-monitor`) · izvršava se na **`add_action('wp', …, 11)`, dakle pun WP boot pre 301** · **ume da izveze u Apache format** (`class-export.php` → `RewriteRule … [R=301,L]`), pa nije „ili-ili" · nijedno od naših 73 pravila ne koristi query string, pa nam bogatije poklapanje ne treba. **Zaključak: podela po populaciji redirekta, ne po alatu** — migracioni skup (73, poznat, ~46.000 pogodaka, **mora raditi i ako WP padne**) ostaje u `.htaccess`; post-live ad-hoc 404-ovi idu u Rank Math jer ih Miroslav rešava sam kroz UI, a `404-monitor` pravi pravilo iz zabeleženog 404-a. 🔴 **Tvrdo pravilo protiv dvostrukog sloja:** isti URL nikad na oba mesta — `.htaccess` se izvršava prvi i tiho pobeđuje, pa bi pravilo u UI-ju izgledalo „ne radi" bez ijedne poruke. ⚠️ Ne uključivati modul pre migracije (nov modul + nove DB tabele 5 dana pred freeze = rizik bez dobitka). **#ceka-miroslav:** odobrenje da se tačke „uključiti `redirections`+`404-monitor` posle live-a" i tvrdo pravilo upišu u checklistu §B7 i master plan 3.12 — analiza je gotova, upis nije izvršen bez odluke.

---

## 2026-08-11 [claude-code] [W3 CWV] Dijeta asseta — proizvod stranice lakše za 46%, postovi 51%, blog arhiva 65% ✅🔴

**Zadatak (M):** „pregledaj temu i da li pokreće neke stvari koje se ne koriste, da i to isključimo da se ne učitava bez potrebe."

**Merenje (zbir stvarnih veličina svih JS+CSS fajlova po tipu stranice, pre/posle, reproducibilno u dva prolaza):** proizvod **1.117→606 KB (−511)** · post **924→456 KB (−468)** · blog arhiva **772→269 KB (−503)** · /katalog/ **1.061→552 KB (−509)** · kategorija −54 · početna −50 · silo/kontakt −14. Efekat je najveći baš na money stranicama.

**Šta je isključeno** (novi `woodmart-child/inc/al-asset-diet.php`):
1. 🔴 **WPBakery CSS 437 KB + JS 17 KB tamo gde nema nijednog `vc_` elementa.** WPBakery **sam ima** ispravnu proveru (`Vc_Base::enqueueStyle()` traži `[vc_row`), ali je **WoodMart pregazi** — `inc/enqueue.php:616` enqueue-uje bezuslovno. Izmereno u `<body>`: 94 proizvoda → 0 pojava, `/katalog/` i `/aktuelnosti/` → 0, 30/31 posta → 0, 67/71 stranice → ima (ostaju na punom). Arhive kategorija **namerno ne diram** (deo njih vuče WPBakery iz `cms_block` sloja koji se ne vidi iz `post_content`).
2. **WooCommerce Blocks CSS 13,7 KB sa svake stranice** — 0/196 stranica koristi `wp:woocommerce/*` blok.
3. **CF7 25,3 KB JS tamo gde forme nema** (proizvodi, kategorije, katalog, arhive). Detekcija čita **isti** `al_quick_form_excluded_slugs` filter kao i sama injekcija „Brzog upita", da se liste ne raziđu.
4. **4 mrtve WoodMart skripte preko native `scripts_not_use` opcije** (`add-to-cart-all-types`, `action-after-add-to-cart`, `quick-shop`, `woocommerce-quantity`) — mrtve jer je `catalog_mode`=true i `product_quantity`=false. Korišćen theme-ov mehanizam umesto dequeue hakova; `advanced_js` nije diran (provereno da je samo UI gate).

🔴 **Namerno NIJE isključeno — dequeue je bio izveden pa POVUČEN:** `wc-add-to-cart-variation` + `blockUI` + `underscore` + `wp-util` (~43,7 KB). Izgledalo je mrtvo (katalog režim, 0 `<form class="cart">` na sajtu) i radilo je, ali provera je pokazala da **20 varijabilnih proizvoda i dalje renderuje `variations_form`**, a `swatchesVariations.min.js` zavisi od `wc_add_to_cart_variation_params` iz baš te skripte. **Katalog režim skida DUGME, ne varijacijsku formu.** Potvrđeno uživo: izbor boje na Ecotile E500/7 menja sliku — da je dequeue ostao, funkcija od 08.08 (Condor/Tournament/Multisport) bi tiho crkla.

🔵 **Otvorena odluka za M — preostalih 404 KB/stranici:** WoodMart opcija `light_wpb_css` menja pun `js_composer.min.css` (437 KB) theme-ovim `light-wpbakery.css` (**33 KB**). Nije uključena jer lagana verzija **ne nosi 24 klase koje koristimo** — najvažnije `wpb_row` (447×) i `wpb_text_column` (442×), tj. donje margine svakog tekstualnog bloka na svim stranicama, plus `vc_btn3*` dugmad i `vc_masonry_grid`/`vc_media_grid`. Izvodljivo uz prepis ~10 pravila u `antas-design.css` + vizuelni prolaz (1–2h, realan rizik po izgled). **Preporuka: posle live-a**, osim ako M ne odluči drugačije.

🟢 **Usput popravljeno:** 2 slike 404 na postu 16613 (pre-postojeće, post izmenjen 07.08). Promakle su 10.08 jer je 16613 `noindex` a tadašnji sweep ide kroz sitemap — **tačno ona slepa tačka opisana istog dana**. Povučeni originali sa live-a na tačne putanje, bez prepisivanja `src` (isti postupak kao 10.08).

⚠️ **3 nove gotcha-e:** (1) dequeue **stilova** na `wp_enqueue_scripts` nema efekta — WoodMart kači na prioritetu **10000/10001**, naš prolaz mora na **10002** (prvi pokušaj na 100 je tiho prošao bez ijedne promene); (2) `wc-blocks-style` se ne može dequeue-ovati na `wp_enqueue_scripts` uopšte — WooCommerce ga stavlja u red na **`wp_head` 10**; (3) `curl -o fajl` u ovom git bash okruženju upisuje **0 bajtova** (dok `-o /dev/null -w '%{http_code}'` radi) → za analizu HTML-a koristiti PHP.

**Verifikovano:** `al_verify.php`+slike → 212 URL-ova, HTTP≠200: 0, ≠1×H1: 0, PHP greške: 0, slike≠200: 0. Chrome uživo: proizvod (galerija/PhotoSwipe/9 tabova/varijacije), post (GEO-intro, „Brzi upit" forma), blog arhiva (masonry), /katalog/ (mreža, filteri), /kontakt/ (CF7, 5 polja, submit), mega meni — **0 grešaka u konzoli**. Backup: `antasline_local_2026-08-11_pre-asset-diet.sql` + `functions.php.bak-2026-08-11-pre-asset-diet`. Detalji: [[dnevnik/2026-08-11-dijeta-asseta-tema]].

## 2026-08-11 [claude-code] [W3] Legacy CPT-ovi obrisani iz builda i baze + 5 zamenjenih stranica u draft ✅

**Zadatak (M):** „koristio sam custom post types pre tebe — proveri da li se to još koristi; duplirane i zamenjene stranice stavi u draft; ako nema potrebe za njima i pluginom, očisti to sa sajta i iz baze."

**Nalaz: 5 mrtvih CPT-ova iz Porto ere, plugin CPT UI 1.19.2 aktivan, 41 zapis — svi draft/pending, 0 objavljenih.** `vestacka-trava` (16) · `spoljne-podne-obloge` (10) · `industrija-podovi` (8) · `podovi-posl-prostor` (5) · `sportski-podovi2` (2). Nula stavki u menijima, nula u redirect mapi, i **svi slugovi 404 na live-u** (curl provera) → nema šta da se spasava.

🔴 **Nisu bili samo mrtvi nego i opasni:** to je isti mehanizam koji je 29.07 (W7 F2.9) oborio 6 pod-stranica na 404 — rewrite pravilo mrtvog CPT-a stoji ispred generičkog page pravila. Tada je zamka samo neutralisana filterom u child temi; sada je uklonjen uzrok.

**Urađeno:** sadržaj svih 41 zapisa arhiviran u [[migracija/arhiva/2026-08-11-legacy-cpt-sadrzaj]] (329 KB — ne zbog SEO-a nego zato što su ti draftovi bili **izvorni tekst** za nove stranice, npr. Naxos Evolution → `/sportski-podovi-za-sale-i-balone/` 378 klikova) · **211 priloga odvezano** (`post_parent`→0) pre brisanja da `wp_delete_post()` ne dodirne slike u aktivnoj upotrebi na Bergo stranicama (7.764 priloga pre = posle) · 41 zapis obrisan, 0 zaostalih redova (bilo 743 postmeta + 50 term_rel) · plugin deinstaliran + 3 `cptui_*` opcije obrisane (uklj. `cptui_post_types` 12,3 KB **autoload=yes**) · `rewrite flush` → 0 pravila sa mrtvim CPT-ovima.

**5 zamenjenih stranica `publish`+`noindex` → draft:** 5512 `/podovi-za-poslovni-prostor/` → 16667 · 5754 `/izgradnja-terena-za-tenis/` → 17028 · 5769 `/podne-obloge-za-promocije-i-sajmove/` → 16665 · 15580 `/podloge-za-parking/` → 16589 · 16171 `/galerija-sportskih-terena/` → 16674. Za svaku provereno: `LOKAL-NOVO` (ne postoji na live-u), 0 GSC klikova, 0 dolaznih linkova, nije u meniju, 301 cilj već u redirect mapi. 🔵 Ovo **menja jutrošnju odluku iste sesije** („ostaju noindex") — po M-ovoj novoj izričitoj instrukciji; noindex je bio tih signal, draft ih eksplicitno priprema za brisanje posle live-a. 301 redovi rade nezavisno od statusa (Apache pre WP-a).

🔴 **Namerno NIJE dirano: 16613** `/sta-postaviti-preko-starog-parketa-ili-plocica/` — jedini duplikat koji **postoji na live-u** (84 klika/1.667 impr.); odluka M od 30.07 stoji (noindex + 301 na 6588 na dan migracije), draft bi uklonio rezervu ako 301 zakaže. Isto i `/ergonomske-podloge-2/` (16672) — `-2` sufiks je pravi live URL, ne artefakt.

⚪ Filter `register_post_type_args` u child temi **zadržan** iako je sad no-op: bekapi pre 11.08 nose `cptui_post_types`, pa bi restore bez njega vratio i 404 zamku. Komentar u kodu dopunjen.

**Verifikovano:** `al_verify.php` 212 URL-ova → HTTP≠200: 0 · ≠1×H1: 0 · PHP greške: 0 · slike bez fajla: 0. Stari 404 bug i dalje popravljen (`/spoljnje-podne-obloge/` + svih 6 dece 200). 5 draft stranica → 404 kako je i cilj. **Backup:** `antasline_local_2026-08-11_pre-cpt-cleanup.sql` (36 MB). Detalji: [[dnevnik/2026-08-11-legacy-cpt-ciscenje]].

## 2026-08-11 [claude-code] [W3] Rollback plan ZATVOREN (gate stavka, 4 dana pre roka) + sitewide provera dijakritike čista ✅

**Zadatak (M):** „proveri sve titles sa ć, š, đ, ž i onda nastavi sa rollback planom" — druga i treća stavka iste sesije.

### Dijakritika — 0 nalaza na oba nivoa

**Baza:** `rank_math_title`, `rank_math_description`, `post_title` svih objavljenih page/post/product/product_variation → 0 mojibake, 0 znakova zamene, 0 `?` usred reči. Kontrolno: 112 title + 163 desc + 181 post_title stvarno nose dijakritiku i ispravni su.

**Renderovani izlaz:** sweep kroz svih 195 URL-ova iz sitemap-a (`<title>` + meta description) → **0/195 nalaza**. Ovaj nivo je rađen namerno odvojeno jer je 30.07 izvor kvara bila keš tabela, ne postmeta — baza čista ne znači izlaz čist.

🔴 **Gotcha koja je zamalo dala lažan alarm:** prvi prolaz prijavio ~385 „mojibake" redova. Sve lažni pozitivi — kolone su `utf8mb4_unicode_520_ci`, a ta kolacija je **akcent- i case-neosetljiva**, pa `LIKE '%Ä%'` uredno pogađa obično `a` (i `LIKE '%ć%'` broji svako `c`). Za forenziku enkodinga mora `LIKE BINARY`/`COLLATE utf8mb4_bin`. → [[reference/naucene-lekcije]]

🟢 Detektor posle nule proveren kontrolnim testom na namerno pokvarenim primerima — hvata ih. **Pouka: kad provera vrati 0, propustiti kroz nju bar jedan poznat-loš primer** (10.08 su dva „nalaza" bila bagovi u alatu, ne u sajtu).

**Dodatak — 4 „noindex" stranice: provereno pre izvršenja, ODLUKA PROMENJENA → ostaju noindex.** M je na nalaz iz prve stavke rekao „nije namerno, stavi index yes", ali provera pre izmene pokazala je da **svaka od 4 ima noviji, već indeksiran parnjak iz WoodMart rebuild-a**: 5512 → 16669 `/kancelarije-i-poslovni-prostori/` + 16667 + 16686 · 5754 → 17028 `/sportski-podovi-za-teniske-terene/` + 16688 + 2699 · 5769 → 16665 `/bergo-easy/` · 16171 → 16674 `/galerija/`. ID obrazac (5xxx stari lokalni build vs 166xx/170xx rebuild koji ih je zamenio) + **nijedna ne postoji na live-u** (nema ih u `live-inventar-2026-07-05.csv`) potvrđuju da je noindex bio nameran; 16171 uz to nema ni `<h1>`, samo 3 galerije bez teksta. Index bi 5 dana pre freeze-a napravio 4 duplikat-para prema stranicama koje smo namerno gradili — direktno protiv anti-kanibalizacione provere. **Ništa nije dirano** (M potvrdio: ostaviti noindex, pitanje zatvoreno). 🔵 Opciono posle live-a, ne blokira migraciju: 301 sa te 4 na parnjake. Nova lekcija: „nema meta opis + van sitemap-a" može biti tačan opis namerno penzionisane stranice — pre popravke potražiti noviji parnjak u istom klasteru (viši ID, isti pojmovi u slug-u).

### Rollback plan — sva 3 pitanja zatvorena, gate stavka ispunjena

1. ✅ **Hosting auto-backup — odgovor je već postojao 2 nedelje.** JetBackup 5, dnevni, off-site, 90 dana retencije; M proverio u cPanel-u 27.07 i upisao u M6 red master plana, ali nikad nije preneto u rollback plan. Ostaje **treća** linija odbrane, ne zamena za ručni backup (dnevna granularnost = do 24h gubitka).
2. ✅ **CDN/edge keš — NE POSTOJI.** Read-only provera live-a: DNS direktno na `138.201.234.168` (Hetzner, `ns1–ns4.oblak.host`), bez CNAME na CDN; zaglavlja `Server: LiteSpeed` + `X-LiteSpeed-Cache: hit`, **bez** `cf-ray`/`via`/`age`/`x-qc-cache`. Korak „očisti keš" se svodi na LSCWP Purge All. Upozorenje u planu: ako se QUIC.cloud uključi pre migracije, korak se menja.
3. ✅ **Ko izvršava ako M nije dostupan — M ODLUČIO: „migracija samo kad sam tu."** Od 3 ponuđene opcije izabrana treća; nema rezervne osobe niti dogovora sa hostingom unapred. 🔴 **Posledica koja ide u checklistu dana migracije:** ne pokretati 3.11 ako M nema ~6h slobodnih — kasno popodne/veče 24.08 nije prihvatljiv start, a ako tog dana nema prozora migracija se **pomera**. Prihvaćen rizik, eksplicitno zapisan: ako postane nedostupan usred incidenta, ostaje samo improvizovan poziv oblak.host podršci (SLA namerno neproveren).

**Gate posle ove sesije:** od 3 crvene stavke ostaju **2** — LCP (spoljno ograničenje, čeka produkciju) i svež live backup na 2 lokacije (`[cpanel-live]`).

**Ažurirano:** [[migracija/rollback-plan]] (status → zatvoren), [[migracija/2026-08-10-pre-migration-checklist]] (nova B1 stavka „Dostupnost" + JetBackup provera), [[2026-07-06-MASTER-PLAN-V2]] §3/§4, [[PROGRESS]], [[reference/naucene-lekcije]] (2 nove). Detalji: [[dnevnik/2026-08-11-rollback-plan-i-dijakritika]].

## 2026-08-11 [claude-code] [W2/SEO] 6 stranica bez meta opisa — napisani i objavljeni ✅

**Zadatak (M):** izbor sa otvaranja sesije — bloker od 10.08 („6 stranica bez meta opisa, uključujući početnu"), rok content freeze 16.08.

Napisan `rank_math_description` za svih 6, svaki izveden **isključivo iz teksta same stranice** (nijedan podatak nije izmišljen), dužine 151–157 znakova:

| ID | URL | Iz čega je izveden |
|---|---|---|
| 16550 | `/` (početna) | hero: Ecotile 25 god. garancije · FIBA · „ugradnja bez zatvaranja pogona" |
| 5455 | `/vestacka-trava/` | modeli Nature/Highlands/Springgrass + „bez zalivanja i šišanja, zelena preko zime" |
| 5119 | `/vestacka-trava-za-fudbal/` | „radi se po projektu, po dimenzijama terena" · lepljene linije bela/crvena/plava · FIFA/ITF/IRB |
| 15480 | `/sportske-podloge/bergo-ultimate/` | FIBA/ITF sertifikat · preko 10 boja · 15 god. garancije |
| 21 | `/aktuelnosti/` | blog arhiva (sadržaj stranice je prazan) → izveden iz stvarnih naslova postova u arhivi |
| 16612 | `/ftalati.../` | postojeći GEO-intro pasus (omekšivači, endokrini/disajni sistem, zabranjene grupe u EU/Srbiji) |

🔴 **Gotcha (nova, ide u [[reference/naucene-lekcije]]): UTF-8 ne sme kroz `mysql -e "..."` na ovom Windows shell-u.** Dva opisa upisana tim putem stigla su u bazu sa `?` umesto ć/š/ž (konzolni codepage jede dijakritike) — a `SELECT` u istoj konzoli to **ne otkriva**, jer i ispis prolazi kroz isti codepage, pa izgleda kao kozmetički problem prikaza. Otkriveno tek `curl`-om nad `<head>`. Ispravno: uvek `.sql` fajl sa `SET NAMES utf8mb4;` pa `mysql < fajl.sql`. Ista 4 opisa pisana iz fajla bila su ispravna iz prve.

⚠️ **Nalaz uz put — bloker je nabrajao 6, a bez `rank_math_description` je ukupno 11 objavljenih stranica.** Preostalih 5: `/katalog/` (ima opis preko Rank Math fallback-a, OK) i 4 stranice koje su **`noindex` i nisu u sitemap-u** — `/podovi-za-poslovni-prostor/`, `/izgradnja-terena-za-tenis/`, `/podne-obloge-za-promocije-i-sajmove/`, `/galerija-sportskih-terena/`. Zato ih regression sweep (koji ide kroz sitemap) nije ni prijavio. Nijedna od te 4 ne postoji na live-u (nema ih u `live-inventar-2026-07-05.csv`) → LOKAL-NOVO, noindex deluje namerno. **Ne diram ih; ako je noindex slučajan, to je zasebna M odluka pre freeze-a.**

**Verifikovano:** 6/6 HTTP 200 · 1×H1 · tačan opis u `<head>` sa ispravnom dijakritikom · regresija čista na `/industrijski-podovi/` i `/kontakt/` (opisi nepromenjeni).

**Usput:** Apache nije radio na početku sesije (curl exit 7) — pokrenut ručno; MySQL je radio.

**Backup:** `antasline-backups/antasline_local_2026-08-11_pre-metadesc-6-stranica.sql`

## 2026-08-10 [claude-code] [W3 3.10] Full regression — 195 stranica, 4 bag-a nađena i popravljena, build čist ✅

**Zadatak (M):** „w3 3.10" — treća stavka istog dana.

Napisan `migracija/alati/regression-sweep.php` (read-only): sitemap → svaka stranica (status, H1, JSON-LD, title/meta) → HEAD nad svakom slikom i internim linkom. Baseline snimljen u `analiza/2026-08-10-regression-baseline-pages.csv`; **namerno je napravljen da se isti sweep pusti protiv produkcije posle migracije** i uporedi (stavka B6 checkliste).

🔴 **Nalaz 1 — `/spoljne-podne-obloge/` (bez j) 404 na SVIH 195 stranica.** Isti tip bug-a koji je 07.08 nađen na staging-u. **Dva** widget-a su nosila stari slug, ne jedan: `widget_text` („Navigacija") i `widget_custom_html` („Podovi" → „Terase i dom"). Prva popravka je gledala samo prvi i provera je i dalje javljala 404 — otud drugi krug. Pouka: kod footer linkova proći kroz SVE `widget_*` opcije.

🟢 **Nalaz 2 — 5 slika 404, rešeno bez izmene sadržaja.** Sve postoje na live-u → originali povučeni na tačne putanje u `uploads`. Alternativa (prepis `<img src>` na `-1` varijante koje postoje lokalno, artefakt F3 reimporta) odbačena — razišla bi lokalni sadržaj od live-a bez potrebe.

**Nalaz 3:** 3 interna linka išla kroz 301 na stari slug (2699, 5438, 17026) → prevezana na finalni cilj, svi provereni 200 pre izmene.

🔴 **Nalaz 4 — 27 `*.bak-*` fajlova u `wp-content` se serviraju kao IZVORNI KOD.** Izmereno, ne pretpostavka: `functions.php.bak-…` → HTTP 200, 53 KB PHP izvora (Apache ne izvršava `.bak-*`). Kredencijala nema (proverio), ali otkriva logiku court-builder tokena, honeypota, rate-limita. **Paket-skripta ih je do danas sve pakovala.**

**`build-staging-package.sh` — dva exclude pravila dodata:** `mu-plugins/al-local-mail-log.php` (🔴 već jednom udarilo — otišao na staging 07.08 i forme tamo nisu stvarno slale mejlove; komentar u fajlu tvrdi „mu-plugins se ne prenose", što je netačno i bio je uzrok) i `*.bak-*`/`*.orig`/`*.old`/`*~`. Oba ostaju na lokalu gde su potrebni.

**Napravljena [[migracija/2026-08-10-pre-migration-checklist]]** — A: do 21.08 · B: dan migracije po redosledu. Uključuje neprovereno mesto: **court builder mejl klijentu nikad nije poslat pravim SMTP-om** (lokalno uvek kroz mail-log presretač).

**Verifikacija (pun ponovni prolaz):** 195 stranica — 0 non-200 · 0 bez H1 · 0 sa 2×H1 · 0 nevalidan JSON-LD · **0/1.182 slomljenih slika** · **0/1.145 internih 404** · 6 bez meta opisa. Van sweep-a: GTM+consent na 5 tipova stranice, CF7 forme rade, EC kod sitewide.

⚠️ **Dva bug-a u prvoj verziji sweep alata** (popravljena, upisana u lekcije): `strip_tags()` ZADRŽAVA sadržaj `<script>` → provera „sirov JSON-LD u vidljivom tekstu" davala lažni pozitiv na svih 195 stranica; regex delimiter `#` uz `#` u klasi → „Unknown modifier". Oba su bila u mom alatu, ne u sajtu.

🟡 **#ceka-miroslav: 6 stranica bez meta opisa, uključujući POČETNU (16550)** — `rank_math_description` prazan, nema ni fallback. Copywriting, ne mehanička izmena → nije rađeno bez odluke. Rok: content freeze 16.08.

Backup: `antasline-backups/antasline_local_2026-08-10_pre-w3-310-regression-fix.sql`. Skripte: `job-w3-310-regression-fix.php` (idempotentna, `--dry-run`), `regression-sweep.php`. Detalji: [[dnevnik/2026-08-10-w3-310-full-regression]].

---

## 2026-08-10 [claude-code] [PLAN] GO-LIVE POMEREN NEDELJU RANIJE: 31.08 → PON 24.08 🔴

**Odluka (M), na otvaranju sesije:** „Pomeramo launch za sedmicu ranije. Prilagodi plan." Nijedan izvršni zadatak nije rađen — samo prepravka planskih dokumenata.

**Nov raspored (zamenjuje N6/N7/N8):**

```
N6' 11–16.08  poslednji sadržajni prozor — glavno: W3 3.10 full regression
    NED 16.08 ⛔ CONTENT FREEZE (bilo 18.08)
N7' 17–21.08  freeze · .htaccess/301 finalna provera · svež live backup (cPanel) · GSC priprema
    PET 21.08 🚦 GATE PREGLED → GO/NO-GO. Rok za SVE M odluke.
    22–23.08  vikend = jedina rezerva
→   PON 24.08 MIGRACIJA → post-live 25.08+
```

🔴 **Cena pomeranja:** seče se cela N8 buffer nedelja — rezerva pada sa 5 radnih dana na 2 dana vikenda. Ako gate 21.08 padne na nečemu što se ne popravi za vikend, migracija se vraća na 31.08; pravilo „bilo koji gate crven → pomera se, ne gura na silu" ostaje netaknuto. Tri gate stavke su i dalje otvorene (LCP — spoljno hosting ograničenje, verovatno ide kao svestan rizik · svež live backup na 2 lokacije · rollback plan, 3 pitanja od 27.07).

⚠️ **Nalaz koji je pomeranje samo stvorilo — zadatak 4.8:** prag za Maximize Conversions je dostignut 06.08 (24 plaćene konverzije), ali Smart Bidding uči ~14 dana. Uključeno danas → period učenja se završava **tačno na dan migracije**, kad se menjaju URL-ovi oglasa: dva izvora šuma se preklapaju i ni jedan efekat se ne može pripisati uzroku. **Preporuka upisana u plan: odložiti 4.8 na ~01.09**, ne uključivati sada. To je promena preporuke u odnosu na 06.08, kad je datum live-a bio 5 dana kasnije.

**Šta se seče prvo ako tempo padne:** Tier4 i preostali nice-to-have content · video objava (ionako blokirana na YouTube handle) · W4 4.11/4.12 Meta/LinkedIn (blokirani na M13/M14) — sve posle live-a. **Ne seče se:** full regression, .htaccess/301, live backup, gate pregled, parity.

**Rokovi M odluka posle pomeranja** (tabela u [[2026-07-06-MASTER-PLAN-V2]] §4): 15.08 rollback plan · 16.08 sve što menja sadržaj (trava-u-boji, F2.8, 14 fotki, meni 67, P3 metadesc, Gemini žig, YouTube handle) · 21.08 Enhanced Conversions toggle, ECOTILE budžet, odobrenje za live kontakt-forma fix.

**Izmenjeni fajlovi:** [[2026-07-06-MASTER-PLAN-V2]] (frontmatter, §2 raspored, §3 gate, §4 tabela rokova, §7 ritam, §8) · [[CLAUDE]] §8/§12/§15 · [[PROGRESS]] (zaglavlje + nov bloker na vrhu + red u Urađeno) · [[migracija/rollback-plan]] · [[migracija/2026-08-09-enhanced-conversions-4.7]] · skill `/antasline-sesija` (N-raspored koji se čita na svakom otvaranju) · skill `/w6-social`. Istorijski dnevnički/prompt fajlovi sa datumom 31.08 namerno nisu dirani — oni beleže šta se znalo tog dana.

---

## 2026-08-10 [claude-code] [W2/W6 video] Video embed preduslovi ZATVORENI — lazy facade je već postojao, `VideoObject` napisan i pušten na 9 stranica ✅

**Zadatak (M):** „lazy facade" — druga stavka istog dana, oba preduslova iz [[seo/2026-08-09-video-obogacivanje-plan]] §4 za kačenje videa na stranicu.

🟢 **Preduslov 1 (lazy facade) je bio LAŽNO otvoren.** Jučerašnji plan ga je vodio kao 🔴 blokator, a infrastruktura postoji od **2026-07-07 (F7.3)** i radi na 9 stranica: `.al-video-facade` CSS u `antas-design.css` + globalni `woodmart-child/js/al-video-facade.js` (event delegation, klik/Enter/Space, `youtube-nocookie.com`, iframe se pravi tek na klik). Ništa nije trebalo graditi. **Pouka: pre nego što se nešto upiše u plan kao blokator, proveriti `woodmart-sabloni` — F-numerisane stavke pokrivaju više nego što se pamti.**

✅ **Preduslov 2 (`VideoObject`) stvarno je nedostajao i sada je gotov.** Potvrđeno prvo da **Rank Math besplatan (1.0.275) NEMA Video modul** (ni u `rank_math_modules` ni na disku — 23 modula, video nije među njima), pa schema mora ručno. Napisan `woodmart-child/inc/al-video-schema.php` (require iz `functions.php`): na `wp_footer` skenira `post_content` za `data-yt-id`, dedupe-uje i emituje `VideoObject` iz mape potvrđenih metapodataka; jedan video → objekat, više → `@graph`.

**Dizajn odluka:** schema se **izvodi iz markupa fasade**, ne upisuje u bazu po stranici (kako je F7.3 prvobitno predviđao preko base64/`vc_raw_html`). Nula izmena u bazi → nema kses rizika (F7.15), nema `wpautop` artefakata (F7.20c), nema backup rizika; svih 9 stranica pokriveno jednim fajlom, buduće fasade rade čim im se ID doda u mapu.

🔴 **Tvrdo pravilo ugrađeno u kod:** ID koji nije u mapi se **preskače**. `uploadDate`/`duration` isključivo sa javne `youtube.com/watch` stranice (`ytInitialPlayerResponse`), nikad iz procene — svih 8 videa provereno, svi aktivni (status OK), datumi 2014–2022. Za 3 videa bez YouTube opisa napisan opis izveden **samo iz naslova i kanala** („Uputstvo proizvođača Bergo za ugradnju Bergo XL modularnih podnih ploča.") — bez ijedne tvrdnje o sadržaju koji nisam gledao. `maxresdefault.jpg` naveden samo gde stvarno postoji (provereno HTTP kodom: 6/8 ima, 2 imaju samo `hqdefault`).

**Verifikovano 9/9:** HTTP 200 · 1×H1 · tačno 1×`VideoObject` · JSON validan · tačan `uploadDate` po stranici. Regresija na 3 stranice bez videa: 0×VideoObject, ostala schema (Article/FAQPage/BreadcrumbList/LocalBusiness) netaknuta. **Uživo u Chrome-u:** iframe se i dalje kreira tek na klik (0 youtube zahteva pre klika), `youtube-nocookie.com` domen, `is-playing` klasa radi, na strani tačno 2 ld+json bloka (Rank Math `@graph` + naš) — bez dupliranja.

⚠️ **Gotcha pri verifikaciji:** 4 od 9 stranica sa fasadom su **child stranice** (`/spoljnje-podne-obloge/bergo-*`) — na flat slugu vraćaju 301, pa je prvi prolaz lažno prijavio „nema schema-e" na njima. Uvek `get_permalink()`, ne slug.

**Backup:** `functions.php.bak-2026-08-10-pre-video-schema`. Skripte: scratchpad `yt-meta.py` (povlačenje metapodataka), `gen-video-map.py`, `verify-video.sh`. Detalji: [[migracija/woodmart-sabloni]] F7.3a.

**Ostaje za objavu basket videa:** YouTube handle (#ceka-miroslav) + odluka o Gemini vodenom žigu (v. unos ispod). Tehnički je stranica spremna da primi video.

---

## 2026-08-10 [claude-code] [W2/W6 video] Kadar 5 (hero) napravljen u Gemini-ju — video remontiran na 40s; 2 pretpostavke iz jučerašnjeg plana oborene ✅🔴

**Zadatak (M izbor):** „završiti kadar 5 u gemini kroz chrome" + quick-win GSC baseline.

**Izvršeno:** kadar 5 (`05-dvoriste.jpg`, tilt-up sa plavo-crvenog terena ka košu i nebu) izrenderovan **u Gemini-ju kroz Chrome automatizaciju** — 10s, 1280×720, 24 fps (Gemini daje 10s, Flow 8s). Podloga, boje i linije netaknute, nema izmišljenih ljudi/lopti (provereno na 4 frejma). Video remontiran na **5 kadrova / 40,0s**: `AntasLine-teren-za-basket-40s.mp4` (bez teksta) + `AntasLine-teren-za-basket-40s-tekst.mp4` (tekst po sekcijama članka, CTA `069 234 00 72` u centru na kadru 5, 32,0–39,8s). Stara 30,5s verzija ostaje netaknuta.

🟢 **Nalaz koji obara stari gotcha — Gemini upload JESTE automatizovan.** Jučerašnja beleška „Gemini nema `input[type=file]` u DOM-u pa je image-to-video zatvoren za automatizaciju" je **netačna**: input postoji, samo se kreira **tek pošto se otvori odgovarajući meni** (`+` → „Направи видео" → ikonica slike). Tada `file_upload` MCP alat radi normalno. Isti obrazac važi i za Flow („Upload media" u asset pickeru). **Provera DOM-a pre otvaranja menija daje lažno negativan rezultat.**

🔴 **Nalaz 1 — Gemini klipovi nose vidljiv Gemini „sparkle" vodeni žig** u donjem desnom uglu, kroz ceo klip. Flow klipovi ga **nemaju** (provereno na 2 klipa od juče, isti kadar-uglovi). Znači: tuđ brend na našem materijalu ako ide na sajt/Ads. Ne diram ga — uklanjanje vidljivog watermarka je odluka M-a, ne moja. **#ceka-miroslav.**

🟡 **Nalaz 2 — Flow u 06:45 lokalno i dalje bez kredita.** Pokušaj da se isti kadar renderuje i u Flow-u (čist, bez watermarka) pao je na „You need more AI credits to complete this request". **To NIJE dokaz da besplatni nalog nema dnevne kredite** — postojeća lekcija od 00:34 noćas kaže da reset ide po **pacifičkoj** ponoći, ≈09–10h po lokalnom vremenu. U 06:45 je jednostavno bilo prerano. Baner „Daily Bonus: **Paid plans** enjoy 50 extra credits (Resets daily)" govori o *dodatnih* 50 za plaćene planove i ne isključuje osnovnu besplatnu kvotu. **Praktična posledica: čist kadar 5 bez vodenog žiga verovatno je dostupan danas posle ~09–10h za 10 kredita** — to je najjeftinije rešenje nalaza 1, pre bilo kakve odluke o žigu.

🟢 **Potvrđeno:** gotcha 1 (agent se zaglavi bez dugmeta za odobrenje) i njegov lek (**nova sesija**, ikona olovke) — reprodukovano tačno, nova sesija odmah pokazala „1 video generation, costing 10 credits". Gotcha 2 (model se traži **u tekstu prompta**, ne kroz Agent settings) — takođe potvrđen, agent odgovorio „using the Veo 3.1 - Lite model".

**Quick-win — GSC baseline pre objave videa** (plan §7 korak 4): `/kako-napraviti-teren-za-basket-ili-kosarkaski-teren/`, 28 dana (11.07–07.08, prozor pomeren za kašnjenje GSC podataka): **4.019 prikaza / 114 klikova / CTR 2,84%**. 30 upita snimljeno u `analiza/2026-08-10-gsc-baseline-basket-pre-videa.json`. Bez ovoga se efekat videa kasnije ne može izmeriti. Obrazac curenja potvrđen: `dimenzije fudbalskog terena` 762 prikaza / **3 klika** / poz. 1,1 · `fudbalski teren dimenzije` 220 / **0** / poz. 1,1 · `visina koša` varijante 127+79+69 prikaza / **0 klikova**.

🟡 **Usput primećeno (nije nova stvar, bilo i u jučerašnjoj verziji):** na kadru 4 (Ledine) čita se registarska tablica parkiranog kombija. Ako video ide javno na YouTube, vredi odluka — zamutiti ili ostaviti.

**Fajlovi:** `AntasLine-teren-za-basket-40s{,-tekst}.mp4` + `Cinematic_slow_tilt_up_from_a.mp4` (sirov kadar 5) u `Downloads`. Skripte: scratchpad `montaza5.sh`, `filter5.txt`. ⚠️ ffmpeg 9.0 je uklonio `-filter_complex_script` — nova sintaksa je `-/filter_complex fajl.txt`.

**Detalji:** [[dnevnik/2026-08-10-kadar5-gemini-video-40s]] · [[seo/2026-08-09-video-obogacivanje-plan]]

---

## 2026-08-09 [claude-code] [W2/W6 video] Google Flow (Veo 3.1) uveden kao stalni alat — 4 klipa + izmontiran video od 30,5s za basket stranicu ✅

**Kontekst:** M pitao „google flow je besplatan, umeš li da ga koristiš?", pa tražio video za `/kako-napraviti-teren-za-basket-ili-kosarkaski-teren/`. Posle prvog uspešnog klipa doneo odluku da ovo uđe u stalni tok: **jedan video dnevno za stranice koje prave novac.**

**Odabran pristup (M izbor od 3 ponuđena):** image-to-video iz **naših pravih fotografija** izvedenih terena, ne AI generisanje od nule. Odbijena varijanta „instruktivna montaža" — Veo ne zna kako Bergo klik sistem izgleda i izmislio bi pogrešan mehanizam na stranici koju ljudi čitaju baš zbog tačnosti.

**Izvršeno:** 5 fotki izabrano iz arhive (~100 basket fotki, potvrđeno AntasLine poreklo preko EXIF/GPS i poznatih lokacija), iskropovano na 16:9 preko PIL skripte. **4 klipa izrenderovana** — Pelješac (Fast), Tara / Bajina Bašta / Ledine (Lite). Sve preuzeto kao MP4 i **izmontirano preko ffmpeg-a u jedan video: 30,5s, 1280×720, 24 fps, prelazi 0,5s, fade in/out, bez zvuka** (`AntasLine-teren-za-basket-32s.mp4`). Kadar 5 (hero, koš + nebo) ostaje za sutra — krediti potrošeni.

**Ključno pravilo prompta (razlog zašto podloga ostaje naša):** traži se **samo pokret kamere i ambijent** (vetar, oblaci, svetlo) + eksplicitno „keep the court surface, colours and markings exactly as in the photo / do not add any new objects, people or basketballs". Čim se zatraži radnja (igrač, lopta kroz obruč), Veo počne da izmišlja.

**Cene potvrđene merenjem (ne pretpostavka):** 50 besplatnih kredita dnevno, obnavljaju se. **Veo 3.1 Lite = 10 kredita, Fast = 20** po klipu od 8s. Na sporim pokretima kamere nad statičnim terenom razlika Lite/Fast se **ne vidi** → podrazumevano Lite, što menja kapacitet sa 2 na **5 klipova dnevno**. Prvi klip je nepotrebno pušten na Fast pre nego što je cena Lite-a bila poznata.

🔴 **Gotcha 1 — Flow-ov agent se zaglavljuje.** Dvaput najavio render („I'm going to animate…") i **nikad nije prikazao dugme za odobrenje**; sesija ostane mrtva. Lek: otvoriti **novu sesiju** (ikona olovke), ne nastavljati staru.

🔴 **Gotcha 2 — Agent settings „Save" ne hvata model.** Izbor Veo 3.1 Lite kroz Agent settings dvaput nije ostao sačuvan (agent je i dalje najavljivao Fast). Pouzdano je tražiti model **u samom tekstu prompta** („Using the Veo 3.1 - Lite model, …").

🔴 **Gotcha 3 — Gemini upload je zatvoren za browser automatizaciju.** `gemini.google.com` **nema `input[type=file]` u DOM-u** (provereno JS upitom, vraća prazan niz) — „Отпреми фајлове" otvara sistemski dijalog koji automatizacija ne vidi. Image-to-video preko Gemini-ja zato nije izvodljiv ovim putem. Nezavisno od toga, **i da radi ne bi trebalo koristiti ga za ove kadrove**: bez naše fotografije Gemini generiše izmišljen teren, što ruši ceo smisao materijala. Gemini ostaje korisna **odvojena besplatna Veo kvota** za slučajeve gde izmišljen kadar nije problem.

**Strateški nalaz (razlog zašto video uopšte vredi):** iz [[seo/2026-07-27-content-klasteri]] se vidi isti obrazac kroz skoro sve klastere — **rangiramo na poziciji 1–3 i ne dobijamo klik** jer Google odgovara direktno u rezultatima (`visina koša` 1.089 impr / 9 kl · `dimenzije fudbalskog terena` 2.409 / 7 · `šljaka` 1.739 / 2 · `dimenzije teniskog terena` 1.465 / 2 · epoksid ~800 / **0**). Još teksta to ne popravlja. Video + `VideoObject` schema menja **izgled samog rezultata** (sličica uz link) — to je hipoteza sa dobrim osnovom, ne garancija; merenje je GSC CTR 28d pre/posle, stop posle 3 stranice ako nema pomaka.

**Alati:** `ffmpeg 9.0` instaliran (`winget install Gyan.FFmpeg`, nije ga bilo). Skripte: `scratchpad/montaza.sh` (xfade lanac), PIL krop skripta. Plan sa redom čekanja stranica, budžetom i ostalim Google Labs alatima (NotebookLM kao najveći sledeći dobitak, Flow Music, Opal): [[seo/2026-08-09-video-obogacivanje-plan]] · shot lista i tačni promptovi: [[seo/2026-08-09-flow-promptovi-basket]].

**Usput nađeno:** `C:\Miroslav\Antas Line priprema za sajt\kosarkaski teren\` sadrži **originalna uputstva za montažu iz 2018** (`okretanje ploca.png`, `postavljanje kosarkaskog terena HI.png`, `uputstvo za postavljanje…`) — tačan prikaz Bergo klik sistema, bolji materijal za sekciju „Kako se postavljaju Bergo podloge" nego bilo koji AI klip.

**YouTube — rešeno:** kanal postoji (`youtube.com/@antasline5676`), status „mrtav" po [[reference/drustvene-mreze]]. Odluka: **objavljivati javno, ne unlisted** (materijal je ionako javan na sajtu; unlisted se ne pojavljuje u YouTube pretrazi, čime gubimo drugu površinu; oživljen kanal je preduslov za YouTube/Demand Gen oglase).

**#ceka-miroslav:** (1) promeniti YouTube handle `@antasline5676` → `@antasline` **pre prve objave** (posle toga ulazi u embed URL-ove i schema-u); traži pristup Google nalogu koji je vlasnik kanala. (2) Pre embed-a rešiti lazy „facade" (poster + iframe na klik) — LCP je već crven, YouTube iframe bi ga dodatno oborio. (3) Proveriti da li Rank Math besplatan ima Video sitemap modul; ako nema, `VideoObject` JSON-LD ide ručno kroz child temu.

## 2026-08-09 [claude-code] [W4 4.7] Enhanced Conversions — lokalni deo implementiran i verifikovan, GTM+Ads deo specificiran za dan migracije ✅

**Kontekst:** Izabrano po M zahtevu ("gmb api kvota pa 4.7"). Planirano za N6, ništa ne blokira. Uvodni GMB retest: **i dalje 429** (`mybusinessaccountmanagement.googleapis.com`, Requests per minute) — četvrti put bez promene od 07-30, Google Basic API Access revizija još traje, nema akcije na našoj strani.

**Problem koji 4.7 mora da reši:** konverzija je pregled `/hvala-za-poruku/` (BLOK A model), ne submit forme — do tog trenutka je odrađen redirect i vrednosti forme više ne postoje na stranici, pa Enhanced Conversions nema šta da hešira. Rešenje: prenos email/telefona kroz `sessionStorage`.

**Izvršeno (lokal):** `woodmart-child/functions.php`, uz postojeći `wpcf7mailsent` redirect handler (backup `functions.php.bak-2026-08-09-pre-enhanced-conversions`). Obe forme — 16593 (kontakt) i 16737 (Brzi upit) — imaju identična imena polja (`form-email`/`form-telefon`), pa ih jedna implementacija pokriva. Piše 5 ključeva: `al_lead_em` (trim+lowercase), `al_lead_ph` (E.164 `+381…`), `al_lead_ts` (za TTL), plus `al_am_em`/`al_am_ph` za Meta. Podaci se šalju kao **čist tekst — Ads sam heširа SHA-256**, unapred heširano bi bilo dvostruko heširano.

🔴 **Nalaz koji je promenio dizajn:** prva ideja (iskoristiti postojeće Meta ključeve `al_am_*`) je **izmerena kao neizvodljiva** — GTM tag `Meta Pixel - Base Code` okida na All Pages i posle čitanja odmah **briše** te ključeve (`removeItem`). Posle test-submita na `/hvala-za-poruku/` ostali su samo `al_lead_*`; `al_am_*` su već bili pojedeni. Da EC deli ključeve sa Metom, tiho bi slao konverzije bez EC podataka. Odvojen prostor imena je neophodan, ne stvar ukusa.

🟢 **Usput popravljeno (Meta):** stari GTM „Capture Lead Data" tag je pisao telefon kao gole cifre **bez pozivnog broja** (`0692340072`) — Meta traži pozivni, pa je taj deo match-a verovatno oduvek propadao. Lokalni kod piše `381692340072`. Očekivan bolji Event Match Quality posle migracije.

**Verifikacija (Chrome, pravi submit-ovi):** obe forme → svih 5 ključeva tačno → redirect → `al_lead_*` prisutni na odredištu. Normalizacija telefona testirana na 12 graničnih slučajeva (`069 234 00 72`, `(069) 234-0072`, `+381 69…`, `00381…`, bez vodeće nule, `0601234567` → svi tačni; `12`/`abc`/`""`/17 cifara → odbačeni, granica 9–15 cifara). **Bez regresije:** na `/hvala-za-poruku/` okinuli GA4 `g/collect` (`generate_lead`), Ads `pagead/conversion/966742304/` + `ccm/conversion/`, Meta `facebook.com/tr/`. GTM-TRDT8K9 potvrđen na localhost, PHP lint čist, 4 stranice HTTP 200.

**Stanje živog kontejnera (potvrđeno iz `gtm.js`):** oba `__awct` taga (`ae_gCKL-3sAcEKCi_cwD` = lead, `QQCBCNDQ_sUcEKCi_cwD` = tel) imaju `enableEnhancedConversionsCheckbox: false` — EC nigde nije bio uključen.

**Namerno NIJE rađeno sada:** GTM izmene. Live i dalje koristi Zion formu koja ne piše ove ključeve, pa bi tagovi bili prazan hod — neproveren pokretni deo u živom kontejneru bez ijedne koristi do 31.08. Pun spec (2 Custom JS promenljive sa TTL 10 min, User-Provided Data promenljiva, izmena SAMO lead taga — ne tel taga) → [[migracija/2026-08-09-enhanced-conversions-4.7]].

🟢 **Bonus — gate stavka olakšana:** pošto sajt sad sam piše `al_am_*`, migraciona stavka „Meta Pixel Manual Advanced Matching prepravka" (master plan §3) svodi se sa „prepiši sve selektore za CF7" na „obriši tag `Meta Pixel - Capture Lead Data` + trigger `Klik na Posalji (Zion forma)`"; `Meta Pixel - Base Code` ostaje nepromenjen.

**#ceka-miroslav:** Google Ads UI → konverzija „Lead - forma (GTM)" → Settings → Enhanced conversions → uključiti, metod **Google Tag Manager**, prihvatiti customer data terms. Bez toga GTM šalje a Ads ignoriše. Bezopasno, može bilo kad pre 31.08.

## 2026-08-08 [claude-code] [Condor Schools/Playgrass] Variation-slike po boji dodate (14 varijacija, 2 proizvoda) ✅

**Kontekst:** M zamolio da svaka color-varijacija Condor Schools (16877) i Condor Playgrass (16885) dobije SVOJU sliku (standardno WooCommerce variation-image ponašanje — slika se menja kad kupac izabere boju), umesto da sve varijacije dele istu parent sliku. Odvojeno pitanje od prethodnog "trava u boji" nalaza ispod (koje ostaje otvoreno #ceka-miroslav) — ovo je čisto UX/tehnički zadatak na proizvodima koji su već `publish`.

**Izvršeno:** Gemini `--mode enhance` (isti obrazac kao 2026-08-05 parent slike), input = postojeći parent attachment (17562 plava za Schools, 17563 zelena za Playgrass), prompt menja SAMO boju teksture (isti ugao/oblik/beli background/senka). 12 novih swatch-eva generisano (6 boja × 2 proizvoda — Plava/Schools i Zelena/Playgrass reuse-uju postojeći parent jer se već poklapaju). Kvota: 12/500 dnevno.

🔴 **Gotcha**: prvi pokušaj (1 PHP skripta, 12 slika u nizu preko `wp_generate_attachment_metadata()`) je pukao na WP `max_execution_time` 300s (kumulativni trošak bootstrap+12× resize u jednom request-u) — `wp_die()` "kritična greška" stranica umesto pravog PHP fatal-a, uzrok nađen u `debug.log`. Rešeno deljenjem na 12 pojedinačnih `php` procesa (isti `import-gemini-photo.php` skript kao 08-05, jedan poziv = jedan proizvod/varijacija, svaki dobija sopstveni 300s budžet) + 2 direktna `set_post_thumbnail()` poziva za reuse slučajeve.

**Verifikacija:** svih 14 varijacija (16878-16884, 16886-16892) ima `_thumbnail_id` popunjen (DB provera), oba proizvod-stranice HTTP 200/1×H1, `data-product_variations` JSON na obe stranice sadrži tačne per-boja URL-ove (npr. `condor-schools-trava-u-boji-crvena-600x600.webp`) — frontend variation-swap potvrđen na nivou markup-a (WooCommerce native ponašanje, nije custom JS). Backup: `antasline-backups/antasline_local_2026-08-08_pre-condor-variation-slike.sql`.

## 2026-08-08 [claude-code] [Konkurencija — trava u boji] Provera brenda "trava u boji" — live sajt koristi Edel Grass, ne Condor 🔴📋

**Kontekst:** M zamolio da se slike sa live sekcije "Veštačka trava u boji" (`/vestacka-trava/`) prebace na lokalne Condor Schools/Playgrass proizvode (16877/16885), verujući da su te slike od holandskog proizvođača Condor Grass.

**Nalaz (dvostruko proveren, ne izmišljeno):**
- Live galerija (6 slika) ima filename prefiks `EG-Colourful-*` (Turquoise/Silver/Pink/New-Lilac/Lime/Anthracite) — **EG = Edel Grass**, ne Condor. Nema teksta na stranici koji pominje brend uz te slike.
- Lokalni Condor proizvodi (16877 "Condor Schools trava u boji", 16885 "Condor Playgrass") imaju **7 varijacija**: Crvena/Žuta/Plava/Bela/Roze/Zelena/Braon — samo se Plava/Zelena/Roze delimično poklapaju sa Edel Grass setom, ostatak ne.
- Proverio condor-group.eu/en/group/members (10 članica: Condor Carpets, VEBE, Condor Cartex, Betap, **Condor Grass**, Timzo, Edel Carpets, Edel Yarns, Condor Techtex, Intercarpet) — **Edel Grass nije na listi** (postoje "Edel Carpets"/"Edel Yarns", vizuelno slično ime, druga firma).
- Web pretraga potvrđuje: **Edel Grass B.V.** (Genemuiden, osnovan 1989) je u vlasništvu Oranjewoud grupe, nepovezano sa Condor Group-om. Izvori: sapca.org.uk/members/condor-grass, crunchbase.com/organization/edel-grass-b-v, edelgrass.com/about-us.

**Odluka (M, 2026-08-08):** ne dirati lokalne proizvode (ostaju kako jesu, generičke slike od 08-05) — **live sajt ostaje kako jeste** (ne diramo live, pravilo §8 CLAUDE.md). M treba da proveri kod pravog dobavljača ko je zapravo isporučio tu obojenu travu koja je fotografisana za `/vestacka-trava/` na live-u — moguće da je Edel Grass stvarni dobavljač te konkretne partije/fotografija, ili da je AntasLine samo koristio dobavljačke stock-fotke bez tačne veze sa Condor imenom u tekstu.

**#ceka-miroslav:** poreklo/dobavljač obojene trave sa live `/vestacka-trava/` slika (Edel Grass vs. neko treći) — dok se ne razjasni, ne kačiti te slike ni na jedan lokalni proizvod (rizik pogrešnog brendiranja/boja, isti oprez kao ranija 07-29 AI-mapiranje odluka).

## 2026-08-07 [claude-code] [W1 Polish Faza 4, batch 1] Pravi GEO-intro "Kratak odgovor" pasus na 5 posta (3318/5276/5181/2622/3388) ✅

**Kontekst:** Faza 3 (2026-07-30/08-07) je pokrila SAV objavljen sadržaj generičkim `.al-cta-box` zatvarajućim CTA-om, ali 22 posta nikad nisu imala pravi `.al-geo-intro` "Kratak odgovor" pasus na vrhu (CLAUDE.md §10 GEO pravilo: prvi pasus = direktan odgovor). To je copywriting zadatak — Faza 4, otvorena na M zahtev 2026-08-07, izvodi se u batch-evima od 5, ništa izmišljeno.

**Izvršeno (batch 1, prioriteti 1–5 po GSC signalu iz 07-30 zapisa):**
- 3318 (zašto-vam-je-potreban-esd-pod) — pasus izveden iz postojećih brojki (25–50V oštećenje, 10.000V trenje o neprovodni pod, otpor 3,4×10⁴–5×10⁶ Ω/m², BS EN 61340-5-1/IEC 61340)
- 5276 (podloge-za-krovove-i-terase) — Bergo PVC, 2,6 kg/m², tri modela (XL/Unique/Elite)
- 5181 (podne-ploce-podovi-za-kontejnere) — tri varijante (Ecotile vinil 5-10mm / LVT Expona Clic / Bergo PVC)
- 2622 (izbor-industrijskog-poda-tri-najcesca-pitanja) — tri pitanja iz naslova (namena/brzina/vrednost)
- 3388 (podovi-za-stamparije) — hemijska otpornost + ESD verzija + montaža bez lepka

Svih 5 upisano preko `$wpdb->update()` (3388 ima FAQPage `<script>` JSON-LD u `post_content` — F7.24 gotcha zahteva zaobilaženje `wp_update_post()`/`wp_unslash()`; ostala 4 preko istog puta radi konzistentnosti). Dry-run pa apply, oba čista. Backup: `antasline-backups/antasline_local_2026-08-07_pre-w1-polish-faza4-batch1.sql`. Skripta: `migracija/alati/job-w1-polish-faza4-batch1.php`.

**Verifikacija:** 5/5 HTTP 200, 1×H1, `.al-geo-intro` prisutan tačno 1× po stranici, FAQPage JSON-LD na 3388 i dalje valid (`json_decode` OK, 4 pitanja), regresija čista (homepage/industrijski-podovi/conquest 2542 i dalje 200).

**Nastavak iste sesije (na M zahtev "nastavi fazu 4 do kraja"):** batch 2 i batch 3 urađeni odmah posle batch 1, GSC brojke nisu osvežavane (isti dan kao 08-07 zapisi, nema potrebe za re-fetch).

- **Batch 2 (prioriteti 6–11):** 16615 (detailing radionice — Bergo Ultimate/Ecotile 500/7), 16613 (preko starog parketa/pločica — Objectflor Clic/Ecotile/R-tek), 16612 (ftalati — EU/Srbija zabrana za dečije igračke, AntasLine podovi bez ftalata), 16616 (teren za pickleball — dimenzije 13,4×6,1m + Bergo Ultimate FLOV™, ⚠️ `@graph` FAQPage+Product `<script>`), 3398 (Bergo Solid za teška vozila — HDPE, 100m²/h), 2641 (PVC vs guma — krutost/vek trajanja/recikliranje). Skripta: `migracija/alati/job-w1-polish-faza4-batch2.php`.
- **Batch 3 (prioriteti 12–21, GSC 0-klik grupa) — zatvara CEO red čekanja:** 5411 (modularni podovi vs epoksid), 16614 (sportska igrališta/Bergo WISH kampanja), 16608 (oštećen industrijski pod — uzroci + Ecotile rešenje), 5163 (Quectel case study, Beograd), 16610 (Naxos preko starog parketa u salama), 3257 (ugradnja preko starih/vlažnih površina), 4813 (Bergo Ultimate/PLUS EN 14877), 6824 (R-Tile Design vs pločice u supermarketima), 6874 (Secure Innovation case study, UK — 660m²+87m² ESD ploča), 17021 (HTEC Niš case study). Skripta: `migracija/alati/job-w1-polish-faza4-batch3.php`.

Svih 16 upisano preko `$wpdb->update()` (isti F7.24 gotcha na 16616). Dry-run pa apply na oba batch-a, oba čista.

**Verifikacija (16/16):** HTTP 200, 1×H1, `.al-geo-intro` tačno 1× po stranici, `@graph` JSON-LD na 16616 i dalje valid (FAQPage + Product). Sitewide provera potvrdila 0 duplikata `.al-geo-intro` klase na bilo kom postu. Regresija čista (homepage 301→OK poznat redirect, industrijski-podovi/conquest 2542/3318/3388 svi 200).

**FAZA 4 U POTPUNOSTI ZATVORENA (22/22 posta, batch 1-3, sve u jednoj sesiji 2026-08-07).** Ceo W1 Polish red čekanja (Faze 1-4) je time zatvoren. Detalji: [[migracija/w1-polish-red-cekanja]] Faza 4.

## 2026-08-07 [cpanel-live] [W4/W6 Customer Match] Pravi --confirm upload pokušan — BLOKIRAN na developer token access level, ne na kodu 🔴

**Nastavak iste sesije** — Miroslav potvrdio "pošalji upload sad, --confirm" pošto je prethodni dry-run prošao čisto.

**Korak 1 — `google-ads` Python biblioteka nije bila instalirana na cPanel serveru** (ni `pip3` na PATH-u). Instalirano preko `python3 -m pip install --user -r requirements.txt` (ceo `antasline-konektor/requirements.txt`, 21 paket, u `~/.local/lib/python3.9/`) — bezbedno, `--user` scope, van sistemskog Python-a.

**Korak 2 — 🔴 bug: `membership_life_span = 10000`** ("Google Ads konvencija: bez isteka", komentar u kodu) odbijeno sa `RangeError.TOO_HIGH`. Google je od 2025-04-07 uveo tvrd max od **540 dana** za CRM-based (Customer Match) liste — stari sentinel je ukinut (potvrđeno WebSearch: [ppc.land](https://ppc.land/google-sets-new-540-day-limit-for-customer-match-data-retention/), zvanični Google Ads API docs). Fix: konstanta `NO_EXPIRY_MEMBERSHIP_LIFE_SPAN=10000` → `MAX_MEMBERSHIP_LIFE_SPAN_DAYS=540` u `customer_match_upload.py`.

**Korak 3 — 🔴🔴 pravi blokator, NIJE kod:** posle fixa, `mutate_user_lists` je USPEO — lista **"AntasLine - Website Leads" je stvarno kreirana u live nalogu 156-886-0314** (`customers/1568860314/userLists/9444454571`, prazna, 0 članova). Ali `OfflineUserDataJobService.create_offline_user_data_job()` (sledeći korak, stvarni upload hešovanih email-ova) je odbijen: `CUSTOMER_NOT_ALLOWLISTED_FOR_THIS_FEATURE` — "Customer Match uploads aren't supported in the Google Ads API for the developer token of the request. Use the Data Manager API for Customer Match workflows." Basic developer token access (odobren 2026-07-27, dovoljan za `ads_report.py` izveštavanje) NE pokriva Customer Match write operacije — treba Standard access (ručno Google odobrenje, Ads UI → Tools & Settings → API Center) ili migracija na poseban Data Manager API (veći posao, nov API/scope).

**Odluka (Miroslav, upitan direktno):** pauzirati ovde, ne tražiti Standard token niti istraživati Data Manager API ovu sesiju. #ceka-miroslav: zatražiti Standard developer token access u Ads UI API Center kad bude spreman da nastavi.

**Status ostavljen:** `leads.csv` (6 kontakata) i kredencijali i dalje na serveru, ništa markirano kao `uploaded` (job creation je pao pre tog koraka). Prazna audience lista ostaje u nalogu (bezopasna, 0 troška/članova) — nije brisana, može se ponovo iskoristiti kad token bude rešen. Detalji + lekcija: [[reference/naucene-lekcije]].

## 2026-08-07 [cpanel-live] [W4/W6 Customer Match] scan_leads.py bug fix + prvi batch pripremljen (6 kontakata), upload čeka M potvrdu ✅📋

**Kontekst:** Miroslav tražio "mejlove i google ads da radimo" — poklopilo se sa novim `scan_leads.py`/`customer_match_upload.py` skriptama (stigle git pull-om ovu sesiju), koje spajaju office@antasline.com mailbox sa Google Ads Customer Match audience-om.

**🔴 Bug nađen i popravljen:** `scan_leads.py` je hardkodovao `LEAD_SENDER = "wordpress@antasline.com"` (buduća WoodMart/CF7 pretpostavka) umesto stvarnog pošiljaoca live sajta, `no-reply@antasline.com` (potvrđeno P1 nalazom 2026-07-30, ali nije preneto u skriptu). Prvi `--dry-run` je tiho vratio "0 novih kontakata" na mailbox-u sa 65 poruka — izgledalo je kao "nema upita", zapravo je filter bio pogrešan. Fix + lekcija: [[reference/naucene-lekcije]].

**Izvršeno:**
- `scan_leads.py` LEAD_SENDER/INTERNAL_ADDRESSES ispravljeni, testirano `--dry-run` (6/6 nađeno posle fixa) pa pravi prolaz — `leads.csv` napravljen (`~/antasline-connector/customer-match/`, van git-a), 6 novih kontakata upisano (samo brojevi u dnevniku, ne adrese — privacy pravilo skripte/naloga)
- Miroslav prekopirao `oauth-client.json`/`token.json`/`ads-config.json` sa Windows mašine — prvi pokušaj u pogrešan folder (`~/antasline-connector/` umesto `.../credentials/`), premešteno na tačnu putanju
- `customer_match_upload.py` (bez `--confirm`) dry-run test: kredencijali ispravno prepoznati, 6 kandidata spremno za upload u listu "AntasLine - Website Leads" (nalog 156-886-0314)

**NIJE izvršeno (namerno):** stvaran `--confirm` upload — Miroslav eksplicitno odlučio da sačeka ("samo pripremi — ne šalji još"). #ceka-miroslav: kad odluči, upload je jedan poziv (`customer_match_upload.py --confirm`), kredencijali i leads.csv već stoje na serveru.

**Napomena:** poznat rizik iz `.claude/skills/antasline-konektor/SKILL.md` (kontakt forma nema marketing-consent checkbox, samo cookie policy) — Miroslavljeva svesna odluka od 2026-08-07 da se ipak nastavi, nije ponovo otvarano ovu sesiju.

Detalji: [[.claude/skills/antasline-konektor/SKILL.md]], [[reference/naucene-lekcije]]. Fajl izmenjen u vault-u: `scan_leads.py` (commit+push na kraju sesije, `[cpanel-live]` workflow).

## 2026-08-07 [claude-code] [W3 3.6 CWV] Sitewide dequeue WC add-to-cart JS steka + jQuery Migrate uklonjen ✅

**Kontekst:** Nastavak sesije posle W1 Faza 3 batch 6 zatvaranja — M pitao "šta se može isključiti u temi, višak CSS/JS?". Umesto nagađanja, provereno stvarno stanje u bazi/kodu/uživo (xts-woodmart-options, curl na 5 tipova stranice, Chrome).

**Nalaz #1 — WC add-to-cart JS stek učitava se SVUDA a nema šta da radi.** Katalog režim (WoodMart nativni `catalog_mode` opcija + child-theme override na `woocommerce_single_product_summary` prio 30, M9 odluka od 2026-07-07) zamenjuje SVAKO add-to-cart dugme (single + loop) linkom ka `/kontakt/?form-naslov=...`. Provereno direktno: nema nijedne `<form class="cart">`/`.single_add_to_cart_button` na sajtu, čak ni na 23/94 proizvoda koji imaju upisanu pravu `_price` vrednost (`is_purchasable()=true`, ali `wc_get_page_id('cart')`/`('checkout')` pokazuju na postove koji ne postoje u bazi — 1615/1616 obrisani). Core `woocommerce.min.js` selektori (`.woocommerce-ordering`, `.password-input`, `.remove_from_cart_button`, `.woocommerce-store-notice`) takođe bez mete (sort-by widget ugašen 2026-07-08, nema my-account/cart stranica).

**Izvršeno #1:** 10 handle-ova dequeue-ovano u `wp-content/themes/woodmart-child/functions.php` (novi blok posle postojećeg W3 3.6 `sourcebuster-js` dequeue-a, isti hook/prioritet `wp_enqueue_scripts` prio 100): `woocommerce`, `wc-add-to-cart`, `wc-add-to-cart-variation`, `wc-jquery-blockui`/`jquery-blockui`, `wc-js-cookie`/`js-cookie`, `vc_woocommerce-add-to-cart-js`, `wd-update-cart-fragments-fix`, `wd-action-after-add-to-cart`, `wd-add-to-cart-all-types`, `wd-sticky-add-to-cart`. CSS namerno nedirano (`wd-woocommerce-base` i dalje stilizuje cenu/grid/badge-ove — to jeste u upotrebi).

**Verifikacija #1 (curl, 5 tipova stranice):** sadržajna (`/industrijski-podovi/`), home, blog arhiva → 0 preostalih WC-cart handle-ova. Katalog + proizvod sa varijabilnim "Srodnim proizvodima" → 2 preostala (`wc-jquery-blockui` + `wc-add-to-cart-variation`) — **poznat, već dokumentovan slučaj** iz originalnog W3 3.6 nalaza (`woocommerce_variable_add_to_cart()` template funkcija re-enqueue-uje direktno POSLE ovog hook-a kad "Srodni proizvodi" widget prikazuje varijabilan proizvod, bez obzira na tip trenutnog proizvoda — dequeue na `wp_enqueue_scripts` ne pomaže, van obima ovog prolaza). Cena i dalje ispravno renderovana (`woocommerce-Price-amount`), HTTP 200/1×H1 svuda, nema PHP grešaka.

**Nalaz #2 — jQuery Migrate.** Preostali kandidat od uobičajenih WP performance stavki (emoji/dashicons/comment-reply/reCAPTCHA su već bili isključeni, WC block CSS-ovi registrovani ali nikad enqueue-ovani). Za razliku od WC dequeue-a, ovo NIJE curl-verifikabilno — greška bi bila tiha vizuelna/interaktivna, ne HTTP status.

**Izvršeno #2:** `add_action('wp_default_scripts', ...)` (frontend samo, `!is_admin()`) uklanja `jquery-migrate` iz `jquery` handle-ovog `deps` niza — WP core ga automatski vezuje po defaultu, ovo je standardan WP obrazac za uklanjanje.

**Verifikacija #2 — pravi browser test kroz Chrome (ne samo curl), pre nego što je ovo proglašeno gotovim:**
- Mega-meni dropdown (SPORT, 3 kolone, hover) ✅
- Mobilni off-canvas meni (390px, `al-harness.html` iframe harness) ✅
- AJAX pretraga (upit "ecotile" → live rezultati sa slikama/cenama) ✅
- PhotoSwipe lightbox na proizvod-galeriji (zoom/navigacija/fullscreen/close) ✅
- **Court builder canvas** (`/planer-terena/`, najkompleksniji JS na sajtu) — klik na boju obojio celu 14×14 mrežu, tabela ažurirana uživo (196/196 ploča, 27,61 m²) ✅
- 0 console grešaka na fresh page load (proveravano i na proizvod i na court builder stranici)

**Backup:** `functions.php.bak-2026-08-07-pre-wc-dequeue`, `functions.php.bak-2026-08-07-pre-jquery-migrate-dequeue` (obe u `wp-content/themes/woodmart-child/`).

**Gotcha za budućnost:** oba dequeue-a su u **child** temi (`woodmart-child/functions.php`), ne u parent `woodmart` temi — theme update na WoodMart ih neće prebrisati (za razliku od `class-breadcrumbs.php` vendor-fajl fixa od 2026-07-30, koji JESTE u parent temi i treba proveravati posle svakog update-a).

**Van obima/nedovršeno:** `wc-add-to-cart-variation` re-enqueue na stranicama sa varijabilnim "Srodnim proizvodima" ostaje nerešeno (poznato, dokumentovano dva puta sada) — zahtevalo bi diranje related-products rendera, ne samo dequeue hook. Sledeći korak za CWV liniju: brojčana Lighthouse LCP potvrda na produkciji (UCSS ponovo uključen 2026-08-07, ali numerička <2,5s potvrda posle toga nije merena).

---

## 2026-08-07 [claude-code] [W1 Polish Faza 3] Batch 6 — generički .al-cta-box na 22 posta bez GEO-intro/CTA teksta (M odluka: mehanički, bez novog teksta)

**Kontekst:** Otvaranje sesije predložilo nastavak "W1 Polish Faza 3" (PROGRESS blokeri red 2026-07-30, item "post tipografska neujednačenost"). DB provera pokazala da je F7.24 retrofit (`.al-geo-intro`/`.al-cta-box` na POSTOJEĆI ad-hoc GEO-intro tekst) već zatvoren 2026-07-30 (batch 1-5, 30/30) — ali samo 8/31 posta je stvarno dobilo te klase (imali su tekst za prevesti), preostalih 23 nikad nije imalo GEO-intro/CTA obrazac pa ih batch 2-5 nisu dirali (samo link/tipfeler fixevi). Znači "30/31 bez al-section" nalaz je i dalje bio tačan.

**M odluka (upitan direktno):** za tih ~23 posta ne izmišljati nov GEO-intro pasus (to bi bilo pisanje sadržaja, ne mehanička izmena) — samo dodati generički zatvarajući `.al-cta-box` na dno (telefon+email, isti generički tekst na svih).

**Izvršeno:** 22/23 posta dobilo generički CTA (`Imate pitanje ili vam treba ponuda za pod? Pozovite 069 234 00 72 ili pišite na office@antasline.com.`) — isti tekst svuda, bez per-post izmišljanja. `17027` (Dimenzije fudbalskog terena) namerno izostavljen — već ima pravi `al-hero` CTA blok (F6 šablon), dupli CTA bi bio suvišan. Dva posta (`3388` podovi-za-stamparije, `16616` teren-za-pickleball) imaju FAQPage JSON-LD `<script>` u sadržaju — CTA ubačen PRE script taga (ne posle) preko `$wpdb->update()` direktno (F7.24 gotcha: `wp_update_post()` kvari eskejpovane navodnike u JSON-LD kad dirne bilo koji deo posta). Ostalih 20 preko `wp_update_post()` (bez script-a, bezbedno).

**Verifikovano:** 22/22 HTTP 200, 1×H1, 1× al-cta-box render na svih 22 (curl+grep). Oba JSON-LD bloka na 3388/16616 i dalje `json_decode()`-valid (2 bloka svaki — sitewide Organization/LocalBusiness + post FAQPage), CTA potvrđeno TEKSTUALNO pre `<script>` u `post_content` (`LIKE '%al-cta-box%<script%'` = 1 na oba). Regresija: basket/fudbal-dimenzije/industrijski-podovi/kontakt i dalje 200/1×H1.

Backup: `antasline-backups/antasline_local_2026-08-07_pre-w1-polish-faza3-batch6.sql`. Skripta: `migracija/alati/job-w1-polish-faza3-batch6-cta-box.php` (dry-run pa `apply`, isti obrazac kao batch 1-5).

**Ovim je ceo W1 Polish Faza 3 (i original F7.24 retrofit i ova dopuna) zatvoren — svih 31/31 objavljenih postova sada ima bar jedan al-section-stil CTA element.** Preostalo van obima ove odluke: pravi GEO-intro "Kratak odgovor" pasus i dalje nedostaje na tih 22 (samo CTA dodat, ne i uvodni pasus) — ako se ikad odluči da vredi pisati taj sadržaj, to je nov, veći zadatak (copywriting, ne mehanička izmena).

---

## 2026-08-07 [claude-code] W6/BLOK — Customer Match pipeline (scan_leads.py + customer_match_upload.py) NAPISAN, ne pokrenut

**Kontekst:** Zatvara M5 pitanje iz W6 plana ("šta biva sa ~55 kontakata/mes") — email-ovi stižu kao CF7 lead-obaveštenja (forme 16593 "Kontakt", 16737 "Brzi upit") na `office@antasline.com`, cPanel-hostovan mailbox na istom serveru kao sajt.

**Izvršeno (prethodna sesija, pronađeno nekomitovano na otvaranju ove sesije):**
1. `scan_leads.py` (već bio komitovan u autobackup-u) — čita Maildir sa cPanel diska (fallback `--imap`), izvlači email iz CF7 tela poruke, inkrementalno puni `leads.csv`/`scan-state.json` **van git stabla** (`ANTASLINE_CONNECTOR_HOME/customer-match/`). stdout ispisuje samo brojeve, nikad email adrese — privatnost po dizajnu.
2. `customer_match_upload.py` (novo, ovaj put komitovano) — hešuje (SHA-256, lowercase+trim pre hash-a) i upload-uje `uploaded=False` kontakte u Google Ads Customer Match user listu ("AntasLine - Website Leads") preko `OfflineUserDataJobService`. **Dry-run podrazumevan**, `--confirm` neophodan za stvaran upload. Reuse-uje postojeći OAuth token (scope `adwords`).
3. Dokumentacija dopunjena: `antasline-konektor/SKILL.md` (novi red u tabeli skripti + write-upozorenje), `reference/api-konektor-setup.md` (Korak E: kredencijali NA cPanel serveru, odvojena kopija `oauth-client.json`/`token.json`/`ads-config.json` u `~/antasline-connector/credentials/`), `w6-social/SKILL.md` (M5 checkbox otkačkan).

**Pregled koda ove sesije:** oba fajla čitana u celosti — importi (`auth.get_ads_client`, `auth.friendly_api_error`, `auth.connector_home`, `auth.credentials_dir`, `auth.read_json`) se poklapaju sa stvarnim potpisima u `auth.py`. Logika ispravna (normalize→hash, get-or-create user lista, partial-failure upload, `uploaded`/`upload_date` upis posle uspešnog job kreiranja). **Nijedan poziv nije stvarno izvršen** (ni `--dry-run` scan, ni sam upload) — ne postoji dokaz da Maildir putanja na serveru radi, da IMAP fallback radi, ili da OAuth token ima dovoljan scope za `OfflineUserDataJobService` write.

🔴 **Poznat, svesno prihvaćen rizik (upisano u SKILL.md 2026-08-07):** kontakt forma trenutno nema consent checkbox za marketing korišćenje email-a (postoji samo cookie/politika kolačića) — Google Ads Customer Match zahteva zakonit osnov za korišćenje podataka. Miroslav je odlučio da pipeline ipak krene bez čekanja na taj checkbox; W6 SKILL.md red za "saglasnost za email" ostaje otvoren kao preporuka, ne blokada.

**#ceka-miroslav (novo, iz ove sesije):**
1. Prvi stvaran `scan_leads.py --dry-run` na cPanel serveru (`[cpanel-live]` sesija) — treba potvrditi Maildir putanju/permisije pre bilo kakvog pravog scan-a.
2. Kopiranje kredencijala na server (`~/antasline-connector/credentials/`) po `reference/api-konektor-setup.md` Korak E — nije potvrđeno da je urađeno.
3. Eksplicitna odluka da li se `customer_match_upload.py --confirm` pušta u produkciju sada ili se čeka na consent checkbox popravku.

Detalji koda: `.claude/skills/antasline-konektor/scripts/scan_leads.py`, `customer_match_upload.py`.

---

## 2026-08-07 [cpanel-live] LCP blok — UCSS (Unique CSS) ponovo uključen na produkciji, otkriven tihi prekid od 2026-07-31 (UŽIVO)

**Kontekst:** Nastavak CLAUDE.md §7.6 — LCP gate crveno, namerno odloženo na LiteSpeed Critical CSS/UCSS na produkciji (`js_composer.min.css` 437KB render-blocking). Ovo je prvi stvarni rad na toj stavci.

**Gotcha #0 (novi, nezavisan od zadatka):** `wp` CLI na ovom LIVE nalogu (na `staging` ovo ne važi) baca fatal error na bilo koju komandu koja bootstrap-uje WP bez `--skip-themes` — Kallyas Zion Builder `hg-framework` (`class-znhgfw.php:43`) konstruiše putanju koja duplira ABSPATH (`.../wp-content/plugins/home/antasline/public_html/wp-content/themes/...`), fajl ne postoji → fatal. **Ne utiče na prave posetioce** (curl na `https://www.antasline.com/` vraća čist 200, nema fatal error teksta u odgovoru) — čisto CLI-bootstrap razlika (verovatno `$_SERVER['SCRIPT_FILENAME']`-zavisna putanja u temi koja se ponaša drugačije van web request konteksta). **Pravilo ubuduće za live (ne staging) `wp` pozive: uvek dodati `--skip-themes`.**

**Nalaz:** `litespeed.conf.optm-ccss_gen`/`optm-css_async` (Critical CSS) su već uključeni i aktivni (199 CCSS fajlova, ~pokriva ceo sitemap od 196 URL-ova, crawler cron radi na 10 min intervalu). Ali `litespeed.conf.optm-ucss` (Unique/Unused CSS — ono što stvarno skida `js_composer` bloat sa render-blocking putanje) je bio **prazan (isključen)**. Provera fajl-timestamp-ova u `wp-content/litespeed/ucss/` pokazala da je UCSS **ranije bio aktivan i generisao fajlove sve do 2026-07-31 03:41** (205 fajlova), pa prestao — poklapa se sa danom kad je hosting odgovorio na LiteSpeed/QUIC.cloud tiket ("neće pustiti zbog bezbednosnih napada", v. unos 2026-07-30). Postojeći dnevnik unos od tog dana pominje samo **image optimization** kao ono što je hosting blokirao — nema eksplicitne odluke da se i UCSS gasi. Mogući tihi nusefekat te sesije (ili plugin je sam ugasio posle ponovljenih cloud grešaka) — nije bilo moguće utvrditi tačan uzrok unazad (samo današnji access log dostupan, raniji je rotiran).

**Izvršeno:**
1. Backup: `~/backups/pre-ucss-enable-20260807-1732.sql`
2. `litespeed.conf.optm-ucss` → `1`, `litespeed.conf.optm-ucss_async` → `1` (async generacija — novi posetioci ne čekaju generisanje, dobijaju fallback dok se UCSS ne izgeneriše u pozadini)
3. `wp litespeed-purge all`
4. Ručno okinut `litespeed_task_ucss` + `litespeed_task_crawler` cron

**Monitor rezultat (10 min prozor):** Nijedan nov `qcbot` poziv na `notify_ucss`/`notify_ccss`, nijedan nov fajl na disku — `last_request.ucss`/`last_request.ccss` u `litespeed.cloud._summary` ostali nepromenjeni na 2026-07-31 vremenima. Znači: **generisanje NOVOG UCSS-a (za stranice bez postojećeg keša ili posle promene sadržaja) ostaje neverifikovano** — moguće da je pogođen istim firewall blokom kao image optimizacija, moguće da prosto ni jedna testirana stranica nije zahtevala novu generaciju u tih 10 min.

**Ali — direktna provera live HTML-a pokazala je da UCSS ODMAH radi za postojeći keš** (nije trebalo čekati novu generaciju): homepage/`/industrijski-podovi/`/`/kontakt/`/proizvod stranica sve sada učitavaju `<link rel="preload" ... as="style" onload="...rel='stylesheet'">` ka `wp-content/litespeed/ucss/<hash>.css` (async, non-render-blocking) — **`js_composer.min.css` (53KB) više se NE učitava kao direktan render-blocking `<link>` ni na jednoj od 4 testirane stranice.** Homepage UCSS fajl: 39.592 bajta, 443 CSS pravila, sadržaj validan (nije PHP/HTML greška).

🔴 **Nalaz — rizik, nije samo teorijski:** grep homepage UCSS fajla za tipične interaktivne klase (`toggle`, `dropdown`, `hamburger`, `mobile-menu`, `slick`/`swiper`/`owl-` slajder klase) → **0 pogodaka.** Ovo je poznat LiteSpeed UCSS rizik (CSS za elemente koje JS naknadno prikazuje/menja se ne vidi u statičkom crawl-u, pa UCSS ga izbaci) — ovde ima konkretan trag da se možda desio, ne samo generička napomena. Nemam browser da vizuelno potvrdim sa SSH terminala.

**M odluka (upitan direktno):** ostaviti UCSS uključen, M sam proverava odmah na telefonu/desktopu (mobilni meni, hamburger dugme, hover podmeniji, bilo koji slajder). Ako nešto vizuelno ne radi → javiti odmah za trenutni rollback (`litespeed.conf.optm-ucss` → prazno + purge, jednostavna komanda, već testirano da radi).

✅ **M potvrdio (isti dan): "proverio, sve radi"** — meni/dropdown/slajderi vizuelno ispravni uz UCSS uključen. Sumnja na strip-ovan toggle/dropdown CSS (0 pogodaka u fajlu, v. gore) nije se materijalizovala kao vidljiv problem — ili su te klase pokrivene negde drugde (CCSS/base tema CSS), ili tema ne zavisi od tih tačnih naziva klasa. UCSS ostaje trajno uključen na produkciji.

🟡 **Ostaje otvoreno (niži prioritet, ne blokira zatvaranje ove stavke):**
- Numerička LCP potvrda (<2,5s) nije merena ovom sesijom — nema Lighthouse/CWV alat sa SSH terminala. Vizuelna ispravnost + uklonjen render-blocking `js_composer` su potvrđeni, ali sam brojčani gate iz CLAUDE.md §7.6 treba proveriti sledeći put kad je Lighthouse/PageSpeed dostupan (lokalno ili preko M-ovog browsera).
- Da li NOVO generisanje UCSS/CCSS (za buduće nove/izmenjene stranice) prolazi kroz hosting firewall — nije verifikovano ovom sesijom (10-min monitor nije uhvatio nov QUIC.cloud poziv, ali ni jedna testirana stranica možda nije ni trebalo da regeneriše). Vredi ponovo proveriti kad neka stranica dobije izmenu sadržaja koja invalidira njen keš.

## 2026-08-07 [claude-code] [BLOK A] — GTM `mailto` event vraćen u život (trigger+tag preko Chrome automatizacije) — ZATVORENO ✅

**Kontekst:** Poslednja otvorena stavka iz BLOK A čišćenja — `mailto` GA4 event je bio mrtav od 2026-06-27 (uzrok nađen 2026-07-27: pratio ga je MonsterInsights, ne GTM; gašenje MI-ja ga je oborilo na nulu dok su `generate_lead`/`tel` bili prevezani). Fix je čekao odobrenje za GTM Submit (`[[PROGRESS]]` Blokeri, redovi 300+306).

**Urađeno — direktno preko Chrome browser automatizacije (Claude-in-Chrome), bez API/OAuth koraka:**
- Ulogovan nalog u Tag Manageru je bio pogrešan (`cpgujam@gmail.com`, bez pristupa) — prebačeno na `miroslav.markovic109@gmail.com`, isti gotcha kao 2026-08-05 Meta Pixel sesija.
- Novi trigger **"Klik na mailto"** (Just Links, Click URL contains `mailto:`) — identičan obrazac kao postojeći `Klik na telefon`.
- Novi tag **"Analitika tag - mailto"** (Google Analytics: GA4 Event, Measurement ID `G-H8BRCZN8W4` preko "Google tag found in this container", Event Name `mailto`, parametar `email_address={{Click URL}}`) — identičan obrazac kao postojeći `Analitika tag - telefon`.
- **Testirano PRE objave** preko GTM Preview (Tag Assistant) na živom `/kontakt/`: klik na `mailto:office@antasline.com` link → tag se okinuo tačno 1×, hit `mailto` poslat na `G-H8BRCZN8W4` sa ispravnim `email_address` parametrom.
- **Submit + Publish**: Version 14 "mailto GA4 event", opis sa punim kontekstom uzroka i fix-a. Version Changes potvrđuje TAČNO 2 stavke (tag added + trigger added) — ništa drugo zahvaćeno. Usput potvrđeno da su `pdf_download`/`gallery_view` draftovi VEĆ bili objavljeni (Version 12, 2026-08-05) — nisu bili deo ovog Submit-a kako je stara PROGRESS napomena pretpostavljala.
- Posle objave: potvrđeno direktnim fetch-om `googletagmanager.com/gtm.js?id=GTM-TRDT8K9` da `mailto` string postoji u živom kontejneru (CDN propagacija prošla).

**🔴 Gotcha (browser automatizacija, ne GTM specifično):** Google Tag Manager-ov "Submit Changes" panel (Version Name/Description polja) je u jednom trenutku prestao da prima klik+type unos preko Claude-in-Chrome computer tool-a — `computer.left_click` na `ref` je vraćao uspeh, ali `document.activeElement` je i dalje bio prazan `<div tabindex="-1">`, a input vrednost ostajala prazna. Simptom se poklopio sa Chrome extension prozorom koji je počeo da vraća sumnjivo mali viewport (837×61) na screenshot-ima uprkos `resize_window` pozivu — verovatno privremeni desinhronizovan render state u ekstenziji, ne GTM UI bag. **Rešeno zaobilaznim putem**: `javascript_tool` sa `Object.getOwnPropertyDescriptor(el.__proto__,'value').set` (native setter, obavezan da bi Angular/React registrovao promenu) + ručni `dispatchEvent(new Event('input'/'change', {bubbles:true}))` — vrednost je ostala i Publish je prošao sa tačnim imenom/opisom verzije. Upisano u [[reference/naucene-lekcije]].

Nema DB backup-a (GTM-only izmena, nema WordPress/SQL rada ove stavke). Preostalo (van dosega Claude Code-a, potvrđeno na kraju prošle sesije): Meta Business Manager domain verifikacija, Event Match Quality, Conversions API — traže pristup Miroslavljevom Meta nalogu.

---

## 2026-08-07 [cpanel-live] Staging V3 šira provera — forme/linkovi/mobilni: 3 bag-a nađena i popravljena (UŽIVO)

**Kontekst:** Nastavak otvorene stavke iz [[PROGRESS]] 2026-08-06 (Staging V3 full setup) — #ceka-miroslav "šira provera (forme, linkovi, mobilni) ili eksplicitna potvrda pre konačnog zatvaranja". Miroslav prijavio "linkovi u meniju su pokvareni — imaju čudan nastavak" → istraga otkrila DVA odvojena nalaza, pa nastavljena puna šira provera na `staging.antasline.com`.

**Nalaz 1 — `/ergonomske-podloge-2/` slug kolizija (meni):** Stranica "Ergonomski podovi" (ID 16672) dobila `-2` sufiks jer je stara slika-prilog iz 2022 (ID 12489) već držala čist slug `ergonomske-podloge`. Fix: attachment preimenovan (`ergonomske-podloge-slika-2022`), stranici vraćen čist slug, `wp rewrite flush --hard`. 2 menu stavke koje linkuju po ID-u (16713, 17388) automatski su se ispravile. Backup: `~/backups/pre-menu-slug-fix-20260807-1653.sql`.

**Nalaz 2 — `?_gl=...` na linkovima — NIJE bag:** Miroslav pokazao `staging.antasline.com/bergo/?_gl=1*...` kao primer. Objašnjeno: GA4/Ads "linker" URL passthrough parametar (`_up*MQ..` flag, `_ga_H8BRCZN8W4` = pravi GA4 stream ID) — normalno ponašanje kad GA4 property (konfigurisan za `antasline.com`) tretira poddomen `staging.antasline.com` kao potencijalni cross-domain prelaz. Živi u GTM/GA4 admin UI (Cross-Domain Linking / Configure your domains), ne u WP kodu — pretraženo i potvrđeno da nema hardkodovanog `linker` podešavanja ni u staging ni u live temi/mu-pluginovima. Nije popravljivo iz SSH sesije, van obima ove sesije (konektor je read-only za GTM/GA4 podešavanja).

**Šira provera — Forme:** Kontakt forma (CF7 16593 na `/kontakt/`) testirana pravim REST submit-om (`status: mail_sent`). Ceo tok radi: standardni CF7 tipovi polja (nema Zion `zn_validate_*` ASCII-only bug-a sa live-a), `wpcf7mailsent` JS event redirect na `/hvala-za-poruku/` potvrđen — tačno mehanizam na koji se GA4 `generate_lead` model oslanja. 🔴 **Nalaz (nije popravljano, M odluka potrebna):** mu-plugin `al-local-mail-log.php` (komentar u kodu: "SAMO LOKALNO, OBRISATI PRE MIGRACIJE") slučajno prenet u V3 paket zajedno sa celim mu-plugins folderom — presreće `wp_mail()`, mejlovi se NIKAD stvarno ne šalju, samo loguju u `wp-content/mail-log.txt`. Test submit potvrđen u log fajlu. #ceka-miroslav: obrisati (za real-mail testiranje) ili ostaviti (da ne spamuje pravi inbox tokom staging testiranja).

**Šira provera — Linkovi:** Sitemap (post+page+product, 196 URL-ova) — svih 196 vraća 200, paralelni `xargs -P 15` sken. Dodatnih 28 internih linkova izvučeno iz 12 reprezentativnih stranica (home, glavne kategorije, proizvod, kontakt, aktuelnosti, katalog, paginacija) i provereno — otkrivena 2 sitewide bag-a:
- **Footer widget "Terase i dom"** (`widget_custom_html` instance 5, prikazan na SVAKOJ stranici preko sajta) linkovao `/spoljne-podne-obloge/` (bez "j") → 404, umesto tačnog `/spoljnje-podne-obloge/`. Fix preko `update_option()` (bezbedno za serijalizaciju, ne raw SQL REPLACE jer je dužina stringa različita).
- **`/industrijski-podovi/` (post 16567)** — 2 hardkodovana href-a u sadržaju (cross-link kartica + FAQ odgovor) i dalje su gledala u stari `ergonomske-podloge-2` slug posle Nalaza 1 (ID-referenca u meniju se sama ispravila preko `_menu_item_object_id`, sirovi href-ovi u `post_content` nisu). Fix preko `wp_update_post()` str_replace.
- 1 legitiman 301 (`sportska-podloga-za-odbojku` → `podloga-za-odbojkaske-terene`, cilj 200) — bez akcije.
Backup: `~/backups/pre-footer-slug-fix-20260807-1713.sql`, `~/backups/pre-industrijski-podovi-link-fix-20260807-1713.sql`. Sva 3 fixa verifikovana uživo posle izmene (footer na drugoj stranici, industrijski-podovi sadržaj, oba nova sluga 200).

**Šira provera — Mobilni:** Samo tehnički signali proverljivi sa SSH terminala (nema browser pristupa iz ove sesije) — viewport meta ispravan, WoodMart burger meni markup prisutan, server ne menja ponašanje na mobilni User-Agent (200). #ceka-miroslav: pravi vizuelni/dodirni test na telefonu ili sesija sa browser pristupom.

**Zatvara** [[PROGRESS]] 2026-08-06 stavku "šira provera" delimično — forme+linkovi pokriveni i popravljeni, mobilni ostaje #ceka-miroslav (vizuelni deo) + 1 M-odluka (mail-log mu-plugin).

---

## 2026-08-07 [claude-code] BLOK E — galerija batch 5: generičke sport-teren fotke (kancelarija ×3 + krovni teren ×2) — poslednji preostali red čekanja zatvoren ✅

**Kontekst:** Nova sesija, `/antasline-sesija` otvaranje. Iz `foto-arhiva-inventar.md` je ostao samo jedan otvoren, niže-prioritetan red: 5 fotografija bez imena lokacije u fajlu (`teren u kancelariji.jpg`/`2`/`3`, `teren na krovu.jpg`/`2`) — 2 dodatne "u dvorištu" varijante iz istog reda su se ispostavile kao već ranije uvezene i iskorišćene na drugim stranicama (post 5061, post 5438), nije trebalo ništa dodatno.

**Provera porekla pre uvoza (isti standard kao padel presedan):** vizuelni pregled je pokazao da "teren na krovu" ima arhitekturu u pozadini koja liči na južnoevropsku/iberijsku (ne srpsku) — rizik da je proizvođački/katalog materijal, ne prava AntasLine realizacija. Pitanje postavljeno M-u eksplicitno pre uvoza (uz putanje do fajlova za lični pregled). **M potvrdio: "ovo su sve radovi antasline. objavi slobodno."** — svih 5 uvezeno.

**Izvršeno:** `/galerija/` (16674) grid "Tereni za basket" 33→38 kartica. 3 kartice "Teren u kancelariji" (indoor Bergo-tip pločice u pretvorenom lofta prostoru, različiti uglovi) + 2 kartice "Krovni teren" (multisport krovna instalacija, koš + mali fudbal/rukomet gol). Skripta `migracija/alati/job-16674-galerija-sportski-tereni-basket-batch5-generic.php` (isti obrazac kao batch 1-4). Backup: `antasline-backups/antasline_local_2026-08-07_pre-galerija-batch5-generic.sql`. Verifikovano: 200/1×H1/38 al-card/5×5 nove slike 200/0 grešaka, regresija (kosarkaske-konstrukcije, industrijski-podovi, odbojka) čista.

**Ovim je `reference/foto-arhiva-inventar.md` red čekanja u potpunosti iscrpljen** — 0 preostalih stavki osim padel (svesno zatvoreno bez objave, v. red ispod).

---

## 2026-08-07 [claude-code] BLOK E — padel fotke: poreklo potvrđeno, objava ODBIJENA — foto arhiva u potpunosti zatvorena

**Kontekst:** Zatvaranje sesije. Poslednje otvoreno pitanje iz `reference/foto-arhiva-inventar.md` — poreklo 3 padel fotografije (`Padel-Club-Stockholm.jpg`, `padel tereni sa vestackom travom.webp`, `vestacka trava za padel terene notix safitex.jpg`). M potvrdio: "sa sajta proizvođača." Pre uvoza, provera EXIF podataka pokazala da jedna fotografija nosi `copyright: Matteo Zanga` (konkretan fotograf, ne generički proizvođački marketing) — drugačiji nivo od Geoplast/Ergomat presedana gde je M dao eksplicitnu dozvolu za javno korišćenje. Pitanje postavljeno M-u eksplicitno pre bilo kakve izmene baze; **M odgovorio da odgovor "sa sajta proizvođača" nije bio odobrenje za objavu** — ništa nije uvezeno, `/veštačka-trava-za-padel-terene/` (16670) ostaje nedirano.

**Ovim je BLOK E foto arhiva (Downloads inventar, otvorena 2026-08-05) u potpunosti zatvorena** — sve grupe obrađene (ESD, Geoplast, Ergomat, sport tereni-imenovane lokacije, odbojka) ili svesno zatvorene bez izmene (padel — dozvola odbijena; generičke fotografije bez lokacije — van obima, niža prioritetnost). Detalji: [[reference/foto-arhiva-inventar.md]].

## 2026-08-07 [claude-code] BLOK E — sport tereni: batch 4 (poslednji, imenovane lokacije) — RED ČEKANJA ZATVOREN

**Kontekst:** Nastavak iste sesije, M rekao "Završi". Poslednji batch imenovanih lokacija: Vršac, Pula, Jajinci, Čačak-Knić. Grid "Tereni za basket" 26→30 kartica (33 ukupno na strani sa 3x3 gridom).

Skripta: `migracija/alati/job-16674-galerija-sportski-tereni-basket-batch4.php`. Backup: `antasline-backups/antasline_local_2026-08-07_pre-galerija-batch4.sql`. Verifikovano: 200/1×H1/33 al-card/4 nove slike 200/0 grešaka, regresija (kosarkaske-konstrukcije, kosarka-3x3, podloga-za-odbojkaske-terene) čista.

**Ovim je red čekanja imenovanih lokacija za `/galerija/` zatvoren** — ukupno 4 batch-a isti dan, 24 nove fotografije na `/galerija/` (9→33 kartice) + 1 na odbojka stranici (4318). Preostaje samo generički materijal bez imena lokacije (teren u kancelariji, teren na krovu, teren u dvorištu varijante — niža prioritetnost, manje verodostojne kao "referenca", nije rađeno ovu sesiju) i padel poreklo #ceka-miroslav. **BLOK E foto arhiva (Downloads inventar) sada u potpunosti zatvoren** osim ta dva preostala niska-prioritetna otvorena pitanja.

## 2026-08-07 [claude-code] BLOK E — sport tereni: batch 3 na Galerija (16674), 8 dodatnih lokacija

**Kontekst:** Nastavak iste sesije, M rekao "Nastavi". Batch 3: 8 dodatnih terena, mešavina iz ZIP arhive i top-level Downloads fajlova (koji nisu bili u ranije analiziranom zipu) — Obrenovac, Pelješac, Užička (Beograd), kamp Pecarski (Zlatibor), Pionirski grad, Graz (3x3 cup), Švedska federacija, Avala. Grid "Tereni za basket" 18→26 kartica (29 ukupno na strani sa 3x3 gridom). Namerno preskočeno: "Bergo Multisport - Spanoulis teren - hotel jugoslavija.jpg" — iako druga fotografija/lokacija, isto ime igrača (Spanoulis) već jednom iskorišćeno kao hero slika stranice; drugi navrat bi mogao delovati kao preuveličana asocijacija sa igračem, preskočeno iz opreza.

Jedan kvalitetni nalaz: `3x3-Svedska-federacija.jpg` je niže rezolucije (400×225) od ostalih (700-4608px) — i dalje prihvatljivo za grid thumbnail, ali zabeleženo kao najslabija slika u ovom batch-u.

Skripta: `migracija/alati/job-16674-galerija-sportski-tereni-basket-batch3.php`. Backup: `antasline-backups/antasline_local_2026-08-07_pre-galerija-batch3.sql`. Verifikovano: 200/1×H1/29 al-card/8 nove slike 200/0 grešaka, regresija (kosarkaske-konstrukcije, podloga-za-odbojkaske-terene) čista.

**Preostalo u redu čekanja**: manji broj preostalih jedinstvenih imenovanih lokacija (Vršac, Pula, Jajinci, Čačak-Knić) + generičke bez imena lokacije (teren u kancelariji, teren na krovu varijante — niža prioritetnost, manje verodostojne kao "reference"). Padel poreklo i dalje #ceka-miroslav.

## 2026-08-07 [claude-code] BLOK E — sport tereni: batch 2 na Galerija (16674) + odbojka fotografija (4318)

**Kontekst:** Nastavak iste sesije, M rekao "idemo dalje". Batch 2 na `/galerija/`: 6 dodatnih imenovanih lokacija iz "tereni za basket" ZIP arhive — Vrčin, Kanjiža, Barajevo, Bajina Bašta (Drinska bajka), Irig, Coca-Cola Dobanovci. Grid "Tereni za basket" 12→18 kartica (ukupno na strani 21 sa 3x3 gridom). Sve fotke proverene pre uvoza (EXIF/kvalitet) — OnePlus telefon GPS podaci na Vrcin/Irig/Kanjiza potvrđuju terensko poreklo, Coca-Cola Dobanovci i Barajevo bez EXIF-a ali dovoljne rezolucije (720×960, 800×600).

Usput zatvorena i manja stavka iz reda čekanja: `podloga-za-odbojkaske-terene` (4318) dobila 1 novu fotografiju — teren za odbojku, dron snimak, Crna Gora (`Teren za odbojku CG.jpg`, DJI EXIF potvrđuje dron poreklo) — umetnuta posle postojeće poslednje fotke, pre FAQ sekcije.

Skripte: `migracija/alati/job-16674-galerija-sportski-tereni-basket-batch2.php`, `migracija/alati/job-4318-odbojka-teren-cg.php`. Backup: `antasline-backups/antasline_local_2026-08-07_pre-galerija-batch2-odbojka.sql`. Verifikovano: oba 200/1×H1, 6+1 novih slika 200, `/galerija/` 21 al-card ukupno, regresija (kosarkaske-konstrukcije, kosarka-3x3) čista.

**Preostalo u redu čekanja** (dodatne kandidat-lokacije iz ZIP arhive, ~15+ preostalih jedinstvenih): Užička Beograd, Graz 3x3 cup, Švedska federacija, Kanjiža (drugi kadar), Čačak-Knić, Pionirski grad, hotel Jugoslavija, Zlatibor kamp Pecarski, Jajinci, Pula, Obrenovac, Vršac. Padel poreklo i dalje #ceka-miroslav.

## 2026-08-07 [claude-code] BLOK E — sport tereni: izviđanje otkrilo da je većina već pokrivena iz ranijih sesija, prvi batch dopune na Galerija (16674)

**Kontekst:** Nastavak reda čekanja "sport tereni (~100 fotki) raspoređivanje po silo stranicama". Pre uvoza bilo koje slike, provereno svih glavnih sport-stranica (`kosarkaske-konstrukcije` 16657, `kosarka-3x3-tereni` 16584, `sportski-podovi-za-teniske-terene` 17028, `podloga-za-odbojkaske-terene` 4318, `sportska-podloga-za-pickleball` 16680, `teren-za-pickleball` 16616, `padel-tereni` 16670, `galerija` 16674) — **glavni nalaz: skoro sve već imaju referentne fotografije sa imenovanim lokacijama, iz ranijih W1/W2 sesija (2020/11 i 2021/03 upload datumi — dakle stariji od ove BLOK E arhive, ne novi rad)**. Kosarkaske-konstrukcije ima 9 kartica, kosarka-3x3 3 kartice, tenis 3 sistema sa fotkama (akril/veštačka trava/Bergo, sve imenovane lokacije: Opština UB, TK Slice Valjevo, Dom učenika Patrijarh Pavle), odbojka 5+ slika (dimenzije, mreža, pesak, sala, spolja), pickleball dve stranice sa 5 fotki ukupno. Padel (16670) ima samo 1 generičku proizvođačku fotografiju — ali dostupne Downloads alternative (Padel-Club-Stockholm, generički Safitex kadar) nisu pouzdano AntasLine-ove reference (nepoznato poreklo/klijent) — namerno preskočeno da se ne izmišlja veza koja ne postoji, ostaje #ceka-miroslav ako želi da potvrdi poreklo.

**Pravi otvoren prostor:** `/galerija/` (16674, "Galerija - sportski tereni", live stranica na koju cilja i M odluka "sport tereni idu na silo/kategorija stranice") — postojećih 9 kartica (3× 3x3, 6× basket) dopunjeno sa **6 novih, ranije nekorišćenih terena** iz "tereni za basket" ZIP arhive: Despotovac, Valjevo, Bezdan, Krk, Sremčica (3x3), Fruška gora — svi realni tereni sa OnePlus telefon-GPS EXIF podacima (potvrđuje da su AntasLine terenske fotografije, ne stock/proizvođački materijal). Grid "Tereni za basket" sad ima 12 kartica (bilo 6), 3x3 grid ostaje na 3 (nedirano).

Skripta: `migracija/alati/job-16674-galerija-sportski-tereni-basket-batch1.php`. Backup: `antasline-backups/antasline_local_2026-08-07_pre-galerija-sportski-tereni-basket-batch1.sql`. Verifikovano: 200/1×H1/15×15 al-card/6×6 nove slike 200/0 PHP grešaka, regresija (kosarkaske-konstrukcije, kosarka-3x3, industrijski-podovi) čista.

**Preostalo u redu čekanja** (ZIP arhiva ima ~94 fajla ukupno, iskorišćeno je sad ~21 jedinstvenih preko svih stranica): dodatne imenovane lokacije za buduće batch-eve na istoj `/galerija/` stranici — Obrenovac, Krk (drugi kadar), Vrčin, Peljesac, Užička Beograd, Graz 3x3 cup, Švedska federacija, Coca-Cola Dobanovci, Kanjiža, Čačak-Knić, Pionirski grad, hotel Jugoslavija, Zlatibor kamp Pecarski, Mali Požarevac (dodatni kadrovi), Vršac, Irig, Bajina Bašta, Pula. Odbojka: `Teren za odbojku CG.jpg` (Crna Gora) neiskorišćen, kandidat za dopunu `podloga-za-odbojkaske-terene` (4318) u narednoj sesiji. Padel: poreklo Downloads fotki ostaje #ceka-miroslav.

## 2026-08-07 [claude-code] BLOK E — Ergomat reference galerije (Isotrack + Mosolut Heavy), Geoplast provereno kao već gotovo

**Kontekst:** Nastavak istog dana, na M zahtev "nastavi sa Geoplast i Ergomat galerijama". Provera pre rada: `/podloge-za-parkiraliste-i-staze/` (16589, Geoplast hub) VEĆ ima 9 pravih terenskih fotografija + FAQPage schema iz ranije W2 sesije (2026-07) — nije uopšte iz ovog arhiva, ali funkcionalno pokriva istu potrebu. Zaključak: Geoplast ne treba dodatan rad, Downloads arhiv fotke za tu grupu su suvišne.

**Ergomat izvršeno:** Isotrack stranica (`/privremene-podloge-isotrack/`, 16111) dobila novu "Reference" sekciju (isti `al-section--paper`/`al-card` obrazac kao ESD pilot) sa 3 prave fotografije (Isotrack L na događaju, Isotrack X na peščanom terenu, Isotrack X na blatnjavom gradilištu) — umetnuta pre kontakt CTA sekcije. Mosolut Heavy proizvod (16530) dobio jednu referentnu fotografiju ("Iz prakse" figura pre "Cena na upit" pasusa — proizvod-opis je gola HTML struktura, nema Layout Builder sekcije, pa je iskorišćen jednostavniji format). Preskočeno: anotiran tehnički render (nije prava fotografija), skoro-duplikat kadar (isti event kao iskorišćena Isotrack L slika), `x-mat-f-3.jpg` (nema odgovarajuću stranicu u katalogu — X-Mat nije poseban proizvod).

Skripta: `migracija/alati/job-16111-16530-ergomat-reference-galerija.php`. Backup: `antasline-backups/antasline_local_2026-08-07_pre-ergomat-reference-galerija.sql`. Verifikovano: oba 200, 1×H1, 4/4 slike 200, 0 grešaka.

**BLOK E foto arhiva sada praktično zatvoren osim sport terena (~100 fotki, red čekanja za narednu sesiju).** Detalji: [[reference/foto-arhiva-inventar.md]].

## 2026-08-07 [claude-code] BLOK E — foto arhiva: sve 3 M-odluke zatvorene + ESD pilot galerija izvršena na /industrijski-podovi/

**Kontekst:** Nastavak iste sesije. Miroslav odgovorio na preostala 2 pitanja: (1) Geoplast+Ergomat materijal je "sve od proizvođača, imamo dozvolu za korišćenje" — sme se objaviti javno. (2) Format: ESD/Geoplast/Ergomat (jasno mapiranje na proizvode) → galerija po proizvodu; sport tereni (~100 fotki, nijedan fajl ne identifikuje tačan Bergo model) → kategorija/silo stranice po sportu, ne po proizvodu (M odluka posle predloga).

**ESD pilot izvršen:** `/industrijski-podovi/` (16567) je već imao gotovu "Reference" sekciju sa 3 kartice (Hankook, Amicus, 2018-stock generička Ecotile fotka) + tekstualne linkove (HTEC/Quectel) — nije građena nova sekcija, dopunjena postojeća. Stari 2018 stock kadar zamenjen pravom fotografijom proizvodne hale; dodate 2 nove kartice (HTEC Niš, Šimanovci) sa pravim terenskim fotografijama iz arhiva. Quectel fotke preskočene (jedine dostupne su 370×166 thumbnail isečci, prenisko za hero karticu) — postojeći tekstualni link ostaje. Skripta: `migracija/alati/job-16567-esd-reference-galerija.php`. Backup: `antasline-backups/antasline_local_2026-08-07_pre-esd-reference-galerija.sql`. Verifikovano: 200/1×H1/5×5 slika 200/0 grešaka, F7.21 `the_content` filter automatski primenio lightbox+srcset na nove slike bez dodatnog rada.

**Ostaje u redu čekanja** (van obima ove sesije, prevelik za jednu turu): Geoplast (~20) i Ergomat (~8) galerije po proizvodu, sport tereni (~100) raspoređivanje po silo/kategorija stranicama. Detalji + pun plan: [[reference/foto-arhiva-inventar.md]].

## 2026-08-07 [claude-code] BLOK E — foto arhiva (Downloads): kategorizacija 7 nejasnih fajlova zatvorena vizuelnim pregledom

**Kontekst:** [[reference/foto-arhiva-inventar.md]] (2026-08-05) je imao 3 otvorene #ceka-M stavke za ~185-fajlovni arhiv referentnih/instalacionih fotografija. Od tri, samo jedna je zahtevala stvaran Miroslavljev input koji Claude ne može sam utvrditi (poreklo/prava za Geoplast i Ergomat proizvođački materijal) i jedna čeka odluku o formatu korišćenja (portfolio stranica vs. proizvod-galerija) — ali kategorizacija 7 fajlova bez ključne reči u imenu je bila čisto vizuelno pitanje, rešivo direktnim pregledom slika.

**Urađeno:** Read alat (podržava sliku) na svih 7 fajlova (`Bela-Crkva.jpg`, `Srebrenica-2018.jpg`, `Bajna-Basta2-2022.jpg`, `British-Internatinal-School-Belgrade.jpg`, `Spanoulis-Belgrade-7.jpg`, `OS-Jovan-Cvijic.jpg`, `Dom-Ucenika-Patrijarh-Pavle-2-Bergo-tenis.jpg`) — svih 7 potvrđeno kao sportski tereni (košarka/tenis/3×3), nijedan nije pogrešno kategorisan ESD/Geoplast/Ergomat materijal. `reference/foto-arhiva-inventar.md` ažuriran: kategorizacija upisana po fajlu, stavka 2 iz "šta čeka M" markirana zatvorenom.

**Usput nalaz:** dve ZIP arhive "tereni za basket" (2026-01-29/2026-02-04) za koje je inventar tvrdio "isti sadržaj" nisu bajt-identične veličine (32.610.789 vs 32.601.134 bajtova, razlika ~9,6 KB) — verovatno bezazleno (metadata/kompresija iz dva odvojena Google Drive exporta), ali nije verifikovano otpakivanjem, pa **nije brisano ništa** (Downloads je Miroslavljev lični folder, brisanje duplikata bez pune potvrde nije rađeno). Napomena upisana u inventar.

**Ostaje otvoreno (nepromenjeno):** (1) poreklo/prava Geoplast+Ergomat materijala, (2) format korišćenja arhive na sajtu. Detalji: [[reference/foto-arhiva-inventar.md]], [[blokovi/BLOK-E-ai-orkestracija]].

## 2026-08-07 [claude-code] Trava-u-boji/Radici — 6/13 NO_THUMBNAIL zatvoreno generičkim dobavljačkim fotografijama (M eksplicitno odobrio)

**Kontekst:** Posle tržišno-konkurentske analize ([[reference/konkurencija-trziste-analiza]]) M pitao "zar nemamo veštačku travu?" — ispravka: niša postoji i radi (live, CTR 8,42%), samo je uzak set od "trava u boji"/Radici proizvoda čekao slike (ranije prijavljeno kao [[PROGRESS]] Blokeri "F2.9 rep" — namerno preskočeno jer dobavljački sajtovi (condor-group.eu, radicisport.it) nisu davali pouzdano model-specifično mapiranje, rizik od pogrešnog pripisivanja). M eksplicitno tražio: "dodaj te generičke sa sajta dobavljača, nema boljih, i objavi proizvode."

**Nalaz pre rada:** provera preko `wp-cli`/direktnog SQL upita pokazala da je svih 10 ciljanih proizvoda već `post_status=publish` (stara napomena iz `seo/2026-07-27-content-klasteri.md` o "16 draft zapisa" je zastarela — status je promenjen negde između 07-27 i sada, verovatno u sklopu W1 1.11 S2 kreiranja). Pravi blocker nije bio status objave nego `_thumbnail_id` — 6 proizvoda (16893, 16899, 16900, 16901, 16902, 16906) imalo NULL.

**Urađeno:** WebSearch+WebFetch nađene 3 generičke slike na dobavljačkim sajtovima — condor-group.eu presek infill/shockpad slojeva (za 16893), radicisport.it generička "Sport" kategorija-slika (fudbalski teren, koristi se za sva 4 Radici proizvoda 16899/16900/16901/16902 jer je `sport-en/` kategorija-stranica JS-renderovana i filter po sportu Rugby/Golf/Hockey/Multisport vraća prazan grid i kroz WebFetch i kroz direktnu proveru — nema pojedinačnih model-slika da se izvuku), radicisport.it generička "Landscape" slika (vrt sa bazenom, za 16906). Sve 3 preuzete, uvezene kao WP attachment preko postojećeg `import-gemini-photo.php` (generički skript, radi identično za bilo koji izvor fajla, ne samo Gemini) i postavljene kao `_thumbnail_id`. Verifikovano: 6/6 attachment URL-ova 200, 6/6 proizvod-stranica 200.

⚠️ **Poznato ograničenje, nije skriveno:** ovo su sirove scene-fotografije (teren/vrt), ne izolovan proizvod na beloj pozadini po `reference/standard-slika-proizvoda.md` — i radicisport.it slika je ista za 4 različita proizvoda (rugbi/golf/hokej/multisport), što znači da kupac ne vidi vizuelnu razliku između njih na nivou fotografije, samo kroz tekst. Ovo je svesan kompromis (M je eksplicitno rekao "nema boljih") a ne previd. Kandidat za kasniji Gemini `--mode enhance` prolaz ako se odluči da vredi dalje ulagati. Backup nije pravljen (samo dodavanje attachment redova + `_thumbnail_id`, ne menja postojeći sadržaj/strukturu). Detalji: [[reference/gemini-red-cekanja]] (ažurirano).

## 2026-08-07 [claude-code] [W3 CWV] Brzina testirana na staging.antasline.com preko Chrome — potvrđen glavni sumnjani nalaz: LiteSpeed Critical CSS/UCSS NIJE aktivan

**Kontekst:** Master Plan V2 W3 3.6 (CWV) ima LCP crveno, namerno odloženo na "LiteSpeed Critical CSS/UCSS na produkciji" jer se nije moglo testirati bez pravog LiteSpeed okruženja. Staging (`staging.antasline.com`, puna V3 kopija sa 2026-08-06) prvi put omogućava pravu proveru — isti hosting/LiteSpeed sloj kao buduća live produkcija. Miroslav zatražio test preko Chrome-a (kredencijali za Basic Auth već upamćeni u browseru, nisu deljeni sa Claude Code).

**Metod:** `claude-in-chrome` navigacija na staging (auth prošao bez unosa), pa merenje preko `performance` API-ja u stranici (Navigation Timing, Resource Timing) na `/` i `/industrijski-podovi/`. Prava Lighthouse LCP metrika NIJE bila moguća — automatizovani tab se stranici uvek javlja kao `document.visibilityState === 'hidden'` (poznato Chromium ponašanje za pozadinske/neaktivne tabove), pa se `largest-contentful-paint` observer nikad ne okida bez obzira na screenshot/wait pokušaje. Nema CLI Lighthouse/PageSpeed Insights opcije jer je staging iza Basic Auth (PSI ne može proći auth, lokalni `npx lighthouse` bi tražio plaintext kredencijale koje Miroslav namerno nije podelio).

**Nalazi (real, ne simulirano — desktop, neusporedivo direktno sa mobile-throttled Lighthouse baznim linijama):**
- TTFB 650–2080ms (varijabilno između poseta), DOMContentLoaded 1,6–3,0s, load event 2,4–4,4s na oba testirana URL-a (`/`, `/industrijski-podovi/`)
- Protokol `h3` (HTTP/3/QUIC) potvrđen na oba — QUIC.cloud/LiteSpeed CDN edge sloj RADI na transportnom nivou
- 🔴 **`js_composer.min.css` i dalje 437KB sirov, neminifikovan/nekombinovan preko mreže** — identičan broj kao lokalni Lighthouse baseline od 2026-07-12 koji je ovo označio kao glavni preostali LCP krivac. Ukupno 26–29 pojedinačnih CSS fajlova po stranici (585KB), 6 markiranih `render-blocking` — nema dokaza kombinovanja/critical-CSS ekstrakcije
- Zaključak: **LiteSpeed Cache plugin/QUIC.cloud Critical CSS + UCSS (unused CSS removal) NIJE uključen/podešen na staging okruženju** — ovo je bila prećutna pretpostavka plana ("čeka produkciju" = automatski će raditi), sada potvrđeno da NIJE default-on, nego zahteva eksplicitno podešavanje u LiteSpeed Cache admin panelu (ili QUIC.cloud dashboard-u) na dan/pre migracije, inače će live LCP nasleđivati isti problem kao lokal

**#ceka-miroslav / sledeći korak:** pre ili na dan migracije, neko mora ući u LiteSpeed Cache podešavanja (cPanel LiteSpeed plugin ili QUIC.cloud nalog) i eksplicitno uključiti Critical CSS + UCSS generisanje — trenutno nema potvrde da je ova opcija ikad bila aktivirana na ovom hosting nalogu. Ako Miroslav ima pristup QUIC.cloud/LSCache admin-u, vredi proveriti status pre W3 3.10 checklist-a. Nema izmena koda/baze ove sesije (samo read-only browser merenje).

## 2026-08-06 [cpanel-live] [W3 migracija] Staging puno postavljanje V3 IZVRŠENO — svih 11 koraka, verifikacija čista (0 slomljenih slika od 292 proverenih)

**Kontekst:** Izvršenje odobrenog prompta [[migracija/2026-08-06-prompt-staging-full-restore]] na cPanel terminalu (potvrđeno `hostname`=`wp1.oblak.host`), nastavak posle prethodnog poništenog pokušaja istog dana.

**Nalaz pre početka:** FTP nalog kvota 7GB, ali glavni cPanel nalog imao samo **4,6GB slobodno** (`uapi Quota get_quota_info`), ne 6GB kako je prompt pretpostavio. Prilagođen tok: umesto spajanja svih delova uploads paketa u pun `.tar.gz` PA raspakivanja (privremeno bi tražilo ~5,6GB, premašuje budžet), uploads paket (133 dela, 2,8GB) raspakovan direktno streaming-om (`cat part-* | tar -xz -C docroot`) bez ikad praviti pun merge-ovan fajl — integritet i dalje proveren preko MD5 na svakom pojedinačnom delu PRE raspakivanja (sva 139 delova sve 3 pakera: OK). Kod (77MB) i dump (37MB) paketi spojeni normalno (mali, bez rizika po prostor).

**Urađeno (KORAK 0-9):**
- Docroot potvrđen `/home/antasline/staging` (`uapi DomainInfo domains_data`).
- Kod paket raspakovan (25.314 fajlova, bez prefiksa). **Basic Auth blok izgubljen kao i očekivano** (poznat gotcha) — ručno vraćen na vrh `.htaccess` (`AuthType Basic`/`AuthUserFile`/`Require valid-user`, lozinka iz `~/staging-htaccess-creds.txt`). `RewriteBase`/`/antasline/` iz lokalnog builda se, kao i prošli put, sam ispravio na `/` posle `wp rewrite flush --hard` (Korak 7) — nije ručno dirano.
- `wp-config.php` **NIJE bio u paketu ovaj put** (popravka iz build skripte radi kako treba) — napravljen ispravan preko `wp config create` (DB_NAME=antasline_staging, DB_USER=antasline_antasline, DB_HOST=localhost). `$table_prefix` proveren direktno u dump-u pre pisanja: `wpgs_` (malo slovo), potvrđen isti obrazac kao ranije. Lozinka iz `~/staging-db-credentials.txt` radila bez problema (za razliku od 07-21 slučaja). Salt konstante regenerisane (`wp config shuffle-salts`).
- Uploads paket raspakovan direktno u `wp-content/uploads/` (2,9GB, streaming metoda gore) — `meni-ikonice/` folder potvrđeno prisutan.
- `wp db reset --yes` (baza je već bila prazna posle prethodnog revert-a, potvrđeno `SHOW TABLES` = 0 redova pre reset-a — **auto-mode klasifikator je automatski blokirao ovu komandu kao destruktivnu operaciju**, traženo i dobijeno eksplicitno odobrenje od M pre nastavka) + `wp db import` (78 tabela posle importa).
- `wp search-replace 'http://localhost/antasline' 'https://staging.antasline.com' --all-tables --precise` → 14.153 zamena. `wp rewrite flush --hard`. `siteurl`/`home` potvrđeni.
- Svi upload artefakti (md5 manifesti, `.tar.gz`/`.sql` delovi) obrisani sa FTP root-a (`/home/antasline/antasline.com/staging/`) odmah posle svake uspešne verifikacije — nikad nije prekoračena kvota tokom procesa.

**Verifikacija (KORAK 10 — punija nego prošli put):**
- `curl -I` bez auth → 401 ✓; sa `-u stagingtest:...` → 200 ✓.
- `/industrijski-podovi/`, `/katalog/`, `/kontakt/`, `/planer-terena/` → 200. `/proizvod/` → 404 (očekivano, flat parent slug bez arhive, poznato od 07-21).
- 6 nasumičnih slika iz baze (`ORDER BY RAND()`) uklj. foldere 2018/2020/2021 i 2026/08 → svih 6 200, uklj. `16919-gallery-1-300x300.webp` eksplicitno traženu u promptu.
- `meni-ikonice/meni-sub-storefront.svg` → 200 (folder koji je prošli put nedostajao u celosti).
- **Pun sken slomljenih slika (ne samo spot-check)**: homepage 76/76 slika 200, proizvod stranica (`ecotile-e500-7...`) 93/93, `/katalog/` 78/78, `/industrijski-podovi/` 62/62, `/kontakt/` 59/59 — **ukupno 0 slomljenih od 292 proverenih** (prošli put 82/108 na samoj početnoj).
- Homepage title: `Početna | Antas Line` (Rank Math, ne Yoast — lokalni build je migrirao 2026-08-05, dump nosi Rank Math metu, title tačan).
- 🟡 Favicon i dalje nepodešen (očekivano, dump ne nosi `site_icon`, nije nov bag) — nije dirano.

**Disk na kraju:** `/home/antasline/staging` 3,4GB ukupno, FTP root prazan.

**Zaključak:** V3 pun restore zatvoren tehnički — čeka **Miroslavljevu ličnu vizuelnu proveru** pre konačnog zatvaranja (isti korak koji je prošli put uhvatio problem koji su automatski testovi propustili).

**M vizuelna provera (2026-08-06, isti dan):** "za sada je sve ok na prvi pogled" — prva potvrda, **nije još pun regresioni pregled** (prošli put je baš dublji pregled, ne prvi utisak, uhvatio 82/108 slomljenih slika). Ne proglašavati V3 konačno zatvorenim dok se ne uradi šira provera (forme, linkovi, mobilni prikaz) ili dok M eksplicitno ne potvrdi da je gotovo.

---

## 2026-08-06 [claude-code] [W3 migracija] Staging puno postavljanje V3 — 3 čista paketa napravljena i prebačena na FTP, cPanel prompt spreman

**Kontekst:** Nastavak iste sesije — M odlučio da poveća FTP kvotu na 7GB i da se odmah pošalje pun paket (ne čeka se dalja diskusija oko diff-a, v. unos ispod za zašto je diff bio besmislen posle wipe-a).

**Urađeno:**
- `build-staging-package.sh` pokrenut u `full` modu — usput nađen i ispravljen bag: root-fajl whitelist je koristio golo `find`+word-splitting, što je pucalo na fajlu `wp-config – kopija.php` (razmaci/crtica u imenu). Popravljeno na eksplicitnu whitelist niz (bash array) SAMO pravih WP core fajlova — **namerno isključuje `wp-config.php`/`wp-config-sample.php`/"kopija" varijante** (staging dobija svoj wp-config, lokalni bi ga prepisao dev vrednostima — isti gotcha kao ranije danas) **i lokalne debug/import skripte** (`add-blocks-*.php`, `fix-*.php`, `import-*.php`, `restore-and-fix.php`, itd. — ostaci lokalnog rada, bezbednosni rizik ako slete na server).
- Rezultat: kod paket 77MB (4×20MB dela), uploads paket 2,6GB (133×20MB dela), + svež `mysqldump` od `antasline_local` 37MB (2 dela) — ovaj poslednji nije bio deo originalne skripte, napravljen posebno jer je pun restore trebao i bazu, ne samo fajlove.
- Očišćeni stari artefakti iz `antasline-staging-upload/` (zaostali `antasline-wp-site-2026-08-06.tar.gz` 3,4GB "smeće" tar cele XAMPP fascikle iz prethodnog pokušaja, stari `chunks/` folder, stari md5 fajlovi) — da se ne pobrka sa novim čistim paketima.
- **Sva 3 paketa (parts + md5 manifesti) uspešno prebačena preko FTP-a** (`ftp-upload-chunks.sh`, proširen da prima `DIR` kao 2. argument umesto hardkodovane putanje). Jedan tranzijentan pad na delu 002 koda (10/10 pokušaja, verovatno mrežni hiccup) — ručni retry odmah uspeo od tačke prekida (curl `-C -` resume).
- Napisan pun cPanel prompt: [[migracija/2026-08-06-prompt-staging-full-restore]] (V3) — pokriva pun restore (ne delta), fresh `wp-config.php` (bez prepisivanja lokalnim), proveru stvarnog `$table_prefix` u dump-u pre pisanja (potvrđeno `wpgs_` malim slovom na ovom dump-u, ali prompt insistira da se PONOVO proveri na serverskom fajlu, ne veruje se dokumentaciji), upravljanje diskom tokom raspakivanja (brisanje delova/arhiva odmah posle uspešne verifikacije, da se ne pređe 7GB kvota), i **širu verifikaciju slika/ikonica nego prošli put** (bar 5 nasumičnih putanja iz baze + eksplicitna provera `meni-ikonice` foldera) — cilj da se uhvati ista vrsta promašaja koja je prošli put prošla kroz sve automatske provere neopaženo.

**Sledeći korak (M):** otvoriti Claude Code na cPanel terminalu, nalepiti prompt iz [[migracija/2026-08-06-prompt-staging-full-restore]]. Posle izvršenja: **obavezno lično vizuelno pregledati staging.antasline.com** pre nego što se ovo proglasi zatvorenim (prošli put je baš taj korak uhvatio 82/108 slomljenih slika).

---

## 2026-08-06 [claude-code] [W3 migracija] Staging redo priprema — skripta za čist paket + ključan nalaz: diff mod je sad besmislen

**Kontekst:** Nastavak posle revert-a ispod. M odlučio da se ova sesija fokusira na *pripremu* sledećeg pokušaja (bez slanja na cPanel), da izvršenje kad M odluči (a) diff vs (b) puna arhiva bude brzo i tačno.

**Ključan nalaz — menja pitanje koje je bilo postavljeno M-u:** posle revert-a, staging docroot+baza su **potpuno prazni** (v. unos ispod). Diff mod ("samo fajlovi izmenjeni posle 21.07") pretpostavlja postojeću 07-21 osnovu na koju se lepi — te osnove više nema na serveru. **Dakle opcija (a) diff trenutno nije izvodljiva, bez obzira na M odluku** — sledeći refresh mora biti PUN paket dok se staging ne vrati u neko poznato stanje. Diff mod ostaje koristan tek za refresh POSLE tog punog postavljanja.

**Merena veličina punog paketa:** `wp-content/uploads` 2,9GB, ceo WP install (`C:\xampp\htdocs\antasline`) 3,4GB. FTP nalog `staging@antasline.com` ima kvotu ~530–560MB (nalaz iz prethodne sesije) — potrebno povećanje na bar 4–5GB, ne "malo više" kako je ranije uokvireno. #ceka-miroslav: (1) povećati FTP kvotu na taj red veličine (cPanel → FTP Accounts → Quota, ili proveriti da li `uapi Ftp` API to može uraditi iz cPanel-live sesije bez ručnog UI koraka), ili (2) alternativni transportni kanal koji zaobilazi tu FTP potkvotu (npr. direktno u cPanel home preko iste cPanel-live sesije koja već radi na `wp1.oblak.host`, ako postoji SCP/rsync put — lokalna sesija i dalje nema direktan SSH na server, port 22 timeout, poznato od ranije).

**Urađeno (priprema, ništa poslato):** `migracija/alati/build-staging-package.sh` — nova skripta koja popravlja oba uzroka pada iz prethodnog pokušaja: (1) kod paket pakuje SAMO `wp-admin`/`wp-includes`/`wp-content`+root fajlove iz `WP_ROOT`, eksplicitni exclude (`.git`,`.claude`,`*.sql`,`al-harness.html`,`wp-content/cache`,`mail-log.txt`) — ne više tar cele XAMPP fascikle; (2) uploads diff mod (kad postane relevantan) koristi `find -newermt` preko CELOG uploads stabla, ne filtriranje po imenu foldera (uzrok prethodnog promašaja: WP čuva fajlove u folderu originalnog uploada, ne datuma izmene, pa su webp-regen i novi `meni-ikonice` folder bili u "starim" folderima van filtera). Skripta chunk-uje oba paketa na 20MB delove + md5sum, isti obrazac kao postojeći `ftp-upload-chunks.sh`. Nije pokrenuta (velika/spora operacija, čeka M odluku o kvoti pre nego što ima smisla praviti pun 2,5–3GB gzip paket).

**Sledeći korak:** M odlučuje o kvoti/transportnom kanalu → onda `bash migracija/alati/build-staging-package.sh full` lokalno → `ftp-upload-chunks.sh` sa novim imenom paketa → cPanel-live sesija raspakuje (isti koraci kao [[migracija/2026-08-06-prompt-staging-refresh]], ali PUN docroot restore, ne delimičan, jer je trenutno stanje prazno).

---

## 2026-08-06 [cpanel-live] [W3 migracija] Staging refresh — PONIŠTEN posle vizuelne provere, docroot+baza vraćeni na prazno ⛔

**Kontekst:** Nastavak iste `[cpanel-live]` sesije, odmah posle unosa ispod ("ZATVOREN, delimičan preko FTP-a"). Miroslav vizuelno pregledao `staging.antasline.com` posle tog refresh-a i prijavio: nedostaju slike na svim stranicama, nema ikonica u meniju, nema favicona, linkovi nose čudan `?_gl=...` nastavak.

**Dijagnoza pre poništavanja (za buduću referencu, da se ne ponovi ista greška):**
- **82/108 jedinstvenih upload URL-ova na početnoj stranici vraćalo 404** — izmereno automatskim skenom, ne pretpostavka.
- **Uzrok #1**: `wp-content/uploads/meni-ikonice/` (69 SVG fajlova, meni ikonice iz istoimene sesije ranije istog dana) je CEO folder nedostajao — uploads-diff paket je bio filtriran samo na `2026/07/*` (posle 21.07) i `2026/08/*`, a ovo je custom folder van te šeme, pa filter nikad nije ni pogledao u njega.
- **Uzrok #2**: `.webp` verzije starih slika (2018/2020/2022/2025/2026-01 folderi) nedostaju iako `.jpg` postoji (npr. `ecotile-floor-1-600x371.jpg` postoji, `ecotile-floor-1-600x371.webp` ne) — nešto je na lokalu generisalo/regenerisalo WebP verzije kroz CELU biblioteku posle 07-21, ali pošto WordPress čuva originalni datum uploada u putanji foldera, te izmene su sletele u STARE datumske foldere koje filter (ograničen na `2026/07+`/`2026/08`) nije pokrivao.
- **Favicon**: `site_icon` opcija je `0` u samom dump-u — nije transfer bag, lokalni build trenutno nema podešen favicon uopšte.
- **`?_gl=` parametar na linkovima**: Google GA4/GTM automatska cross-domain "linker" dekoracija — `staging.antasline.com` nije u GTM kontejnerovoj listi konfigurisanih domena (samo produkcija verovatno jeste), pa gtag dekoriše SVE linkove uključujući interne. Očekivana nuspojava korišćenja ISTOG živog GTM kontejnera na drugom hostname-u, ne bag ovog transfera — ne vredi rešavati za privremeni staging.
- **Pravi koren problema**: strategija "diff po imenu datumskog foldera" (2026/07+, 2026/08) je pogrešna pretpostavka — pretpostavlja da se SVE izmene dešavaju samo u foldeima tekućeg meseca, a WordPress fizički čuva fajlove po datumu ORIGINALNOG uploada, ne datumu poslednje izmene. Prava dopuna mora ići preko `find -newer <referentni-fajl-sa-07-21-timestamp>` preko CELOG `wp-content/uploads` stabla, ne filtrirano po nazivu foldera.

**Dodatni nalaz usput (nezavisno od slika)**: sam raspakovani "kod paket" se ispostavio da je bio tar cele lokalne XAMPP docroot fascikle, ne čist WP install — docroot je posle raspakivanja nosio **4.8GB** smeća: 13+ starih `backup-*.sql` dump-ova (uklj. 127MB `antasline-live-FIXED.sql`), desetak debug/import PHP skripti (`add-blocks-*.php`, `import-*.php`, `fix-*.php`), `.claude/` folder, `wp.bat`, `CLAUDE.md`/`PROGRESS.md.bak`/`DNEVNIK-NAPRETKA.md.bak`, `scratchpad/` sa content-backup fajlovima. Ovo je bilo samo delimično bezbedno (Basic Auth blokira spoljni pristup) ali nikad ne bi trebalo da završi u paketu za deploy.

**M odluka: prekinuti, ne krpiti dalje.** "Obriši poslate fajlove u poslednjoj sesiji i radimo sve od početka na lokalu." Izvršeno:
- Docroot (`/home/antasline/staging/`) obrisan do gole kože — obrisano SVE osim `.htaccess`/`.htpasswd`/`.ftpquota`/`.well-known` (Basic Auth infrastruktura i FTP nalog metapodaci, nisu deo "poslatih fajlova" ove sesije, nema razloga da se ponovo grade). 4.8GB → 24KB.
- Baza `antasline_staging` DROP + CREATE prazna (utf8mb4/utf8mb4_unicode_ci) — bez WP tabela.
- FTP landing folder (`/home/antasline/antasline.com/staging/`) već je bio prazan (Korak 9 prethodnog unosa).
- Verifikovano: `curl -I` bez auth → 401 (Basic Auth i dalje aktivan, staging nije javno vidljiv iako prazan) · sa auth → 500 (očekivano, nema `index.php`/WP instalacije — isti obrazac kao prvobitno 07-21 pre-config stanje).

**Šta OSTAJE netaknuto**: `~/staging-db-credentials.txt` (ažuriran ranije ovu sesiju, tačan DB_USER/lozinka i dalje važe za sledeći pravi setup), `~/staging-htaccess-creds.txt` (Basic Auth `stagingtest` lozinka), MySQL korisnik `antasline_antasline` (samo baza prazna, nalog/lozinka nepromenjeni).

**Sledeći korak (lokal, nova sesija)**: pravi "od početka" pristup — ili (a) ispraviti diff generaciju na pravi `find -newer` preko celog uploads stabla umesto po imenu foldera, ili (b) povećati FTP kvotu i poslati punu svežu arhivu odjednom (M nije još odlučio između ova dva, videti [[PROGRESS]]). U svakom slučaju: **ne pakovati ceo XAMPP docroot** za kod paket — izdvojiti čist WP core+tema+plugin bez debug/backup smeća pre tar-ovanja. Novi Korak 0 setup je identičan 07-21 prvobitnom postavljanju (docroot prazan), ne "refresh".

## 2026-08-06 [claude-code] [W3/staging] Plugin+DB čišćenje lokalnog builda + staging refresh paket pripremljen i otpremljen na FTP — čeka cPanel sesiju 🔄

**Kontekst:** Nova sesija istog dana. M zahtev: "pripremi sve za upload na staging, pre toga očisti neiskorišćene plagine sa sajta i iz baze." Odgovoreno kroz AskUserQuestion na 4 pitanja (cilj = osvežiti postojeći staging.antasline.com probu, ne pomeriti pravu migraciju; ManageWP Worker/Duplicator/WPML sva tri M potvrdio da se uklone).

**Plugin/DB čišćenje (lokalni build, `antasline_local`):**
- Backup pre svega: `antasline-backups/antasline_local_2026-08-06_pre-plugin-cleanup.sql`
- 11 neaktivnih plagina obrisano sa diska: `duplicate-page`, `duplicator`, `media-sync`, `porto-functionality`, `revslider`, `sitepress-multilingual-cms`+`wp-seo-multilingual`+`wpml-media-translation` (WPML), `under-construction-page`, `wordpress-importer`, `worker` (ManageWP). **`wordpress-seo` (Yoast) namerno NIJE dirano** (rollback rezerva po CLAUDE.md §7.1).
- Baza: 33 osirotele plugin-tabele obrisane (18× `wpgs_icl_*`, 14× `wpgs_revslider_*`, 1× `wpgs_duplicator_packages`) + odgovarajući `wp_options`/`wp_postmeta`/`wp_usermeta` redovi (wpml/icl/porto/revslider/duplicator/mwp_ prefiksi) + 2 osirotela cron hook-a (preko `migracija/alati/job-plugin-cleanup-cron.php`).
- **Bonus nalaz zatvoren usput**: 9 "duh" `wp_*` (stari prefiks) tabela — poznat niskoprioritetan artefakt iz 2026-07-21 uvoza (nikad korišćen, `wp-config` koristi `wpGs_`) — sada obrisan iz izvora.
- DB 83,2 MB posle čišćenja (dump 37,7 MB, bilo ~51 MB). Verifikovano: `wp plugin list` čist (9 aktivnih + Yoast inactive + 3 mu-plugin-a), homepage/proizvod stranica 200, 0 fatal grešaka.

**🔴 FTP kvota nalaz (najveći deo sesije):** Prvi pokušaj — pun re-upload sveže arhive (3,18 GB, sve slike uključene) preko `staging@antasline.com` — je konzistentno padao usred transfera (`Send failure: Connection was aborted/reset`), izgledalo je kao mrežna/firewall nestabilnost (probano: chunk-ovanje na 50MB, resume preko `curl -C -`, retry loop). **Pravi uzrok otkriven tek kad je i 5-bajtni test fajl pao** sa `451 Error during write to file` — server-side greška, ne mreža. FTP nalog ima disk kvotu ~530–560 MB (`.ftpquota` fajl potvrđuje quota tracking), potvrđeno brisanjem dela sadržaja → test fajl odmah prošao. Nova lekcija upisana: [[reference/naucene-lekcije]] "FTP 451 = kvota, ne mreža".

**Rešenje — M uputio da staging već ima uploads od 07-21, treba samo razlika:**
- Kod-only paket (tema+plagini+WP core, BEZ `wp-content/uploads`) — 169 MB, 28.428 fajlova
- Uploads-diff paket (SAMO slike dodate/izmenjene posle 21.07 — `wp-content/uploads/2026/07/*` posle 21.07 + sav `2026/08/*`; isključeni lažni pogoci iz starih foldera kao `2015/11/` koji su bili mtime šum od ranijeg bulk restore-a, ne stvaran sadržaj) — 151 MB, 2.762 fajla
- SQL dump — 36 MB (nepromenjeno)
- Sva 3 paketa (kod+uploads-diff chunk-ovani na 20MB delove radi pouzdanosti veze, md5sum-ovani) uspešno otpremljena na FTP — ukupno ~356 MB, staje u kvotu. Svi delovi prošli iz prve (0 retry-a) posle prelaska na manji chunk size.

**Radni nalog kompletno prepisan** (delimičan refresh — kod+baza se menjaju u potpunosti, postojeći uploads se DOPUNJUJE ne briše): [[migracija/2026-08-06-prompt-staging-refresh]]. Sadrži: spajanje delova, md5sum verifikacija PRE raspakivanja, tar integritet provera, raspakivanje koda preko postojećeg (ne dira uploads), raspakivanje uploads-diff preko postojećeg (ne dira starije slike), DB reset+import, search-replace, rewrite flush, Basic Auth provera (nasleđen), čišćenje artefakata, verifikacija (200/401 + Yoast title sanity check + spot-check nove avgustovske slike), vault update.

**Fajlovi VEĆ NA FTP-u** (`staging@antasline.com`) — cPanel sesija ne treba ništa dodatno da otprema, samo da izvrši radni nalog.

**Sledeći korak (M):** otvoriti Claude Code na cPanel terminalu, nalepiti prompt iz [[migracija/2026-08-06-prompt-staging-refresh]]. Kad završi i pushuje, lokalna sesija proverava rezultat preko HTTP-a.

**Van obima ove sesije (namerno neizvršeno):** `porto`/`porto-child` legacy teme (37 MB, mrtve, zamenjene WoodMart-om još u julu) primećene tokom veličina-foldera provere ali NISU uklonjene — M nije to tražio ovom prilikom, ostaje kao mogući budući quick-win.

---

## 2026-08-06 [cpanel-live] [W3 migracija] Staging refresh — ZATVOREN, delimičan preko FTP-a, 3 nova gotcha-a ✅

**Kontekst:** Izvršen `[[migracija/2026-08-06-prompt-staging-refresh]]` na cPanel terminalu (`wp1.oblak.host`) — delimičan refresh postojećeg `staging.antasline.com` (živi od 07-21): kod+baza potpuno zamenjeni, `wp-content/uploads` samo dopunjen diff paketom (FTP kvota ~530-560MB je onemogućila slanje pune sveže arhive, v. [[reference/naucene-lekcije]] FTP kvota unos od ranije danas).

**Pre-koraka:** obrisana 2 stara 07-21 artefakta iz `~/` (3.0GB `antasline-wp-site-20260721.tar.gz` + 47MB `antasline_staging_dump_20260721.sql`) — potvrđeno bezbedno (već raspakovani 07-21, prompt Korak 9 ih i onako traži da se obrišu).

**Izvršeno 1:1 po promptu (Koraci 0-11):** docroot potvrđen nepromenjen (`/home/antasline/staging`, bez mismatch-a ovog puta) → 17 chunk delova (8 uploads-diff + 9 kod) spojeno, svi md5sum OK, oba tar.gz prošla integritet proveru → kod paket raspakovan (`--strip-components=1`) → uploads-diff raspakovan (595 fajlova u `2026/08/`) → DB reset + import (`antasline_staging_dump_2026-08-06.sql`) → `wp search-replace localhost/antasline → https://staging.antasline.com` (**14.124 zamena**) → rewrite flush --hard → cleanup svih upload artefakata sa FTP landing foldera i docroot-a → HTTP verifikacija.

**🔴 Gotcha #1 (poznat, ponovljen): kod paket je prepisao `.htaccess`** — Basic Auth blok izgubljen + `RewriteBase`/`index.php` target pokupili lokalni `/antasline/` prefiks iz izvornog XAMPP builda. Basic Auth blok vraćen ručno pre nastavka; RewriteBase se sam ispravio posle `wp rewrite flush --hard` (WordPress regeneriše taj blok iz tačnog `siteurl`-a).

**🔴🔴 Gotcha #2 (NOV, nije bio predviđen u promptu): kod paket je TAKOĐE prepisao `wp-config.php`** sa lokalnim dev vrednostima (`DB_NAME=antasline_local`, `DB_USER=root`, prazna lozinka) — jer je izvorni paket na lokalu pakovan iz celog docroot foldera uključujući `wp-config.php`, ne samo tema/plugin/core fajlove kako je pretpostavljeno. Ispravljeno na `DB_NAME=antasline_staging`, `DB_USER=antasline_antasline` (potvrđeno preko `uapi Mysql list_users` — **ne** preko imena FTP naloga `staging@`, to bi dalo pogrešnog korisnika).

**🔴🔴🔴 Gotcha #3 (NOV): `~/staging-db-credentials.txt` lozinka nije radila** — `Access denied for user antasline_antasline@localhost (using password: YES)`, potvrđeno i direktno preko `mysql` klijenta (ne samo WP-CLI), bez skrivenih CRLF/whitespace problema u fajlu (proveren `od -c`). Isti obrazac kao raniji `ftp-staging-creds.txt` slučaj (pogrešno označen sadržaj) — fajl je imao "username: staging" što ne odgovara stvarnom cPanel MySQL korisniku. **M odobrio reset preko cPanel-a** (`uapi Mysql set_password user=antasline_antasline`), nova lozinka potvrđena direktnim mysql konektom PRE upisa u `wp-config.php`, `~/staging-db-credentials.txt` ažuriran sa tačnim DB_NAME/DB_USER/DB_PASSWORD/DB_HOST i napomenom zašto je promenjeno.

**🔴🔴🔴🔴 Gotcha #4 (NOV, važno za pravu migraciju 31.08): tabela prefiks u dump-u je `wpgs_` (malo g), NE `wpGs_` kako CLAUDE.md/PROGRESS/skill svuda pišu.** Posle importa, WP-CLI je javljao "site not installed... Found installation with table prefix: wpgs_" — `SHOW TABLES` i `grep CREATE TABLE` u samom dump fajlu potvrdili da su tabele doslovno `wpgs_*` (proveren i `lower_case_table_names=0` na serveru, dakle MySQL ništa ne lowercase-uje sam — dump je od početka pisan malim slovom). `table_prefix` u `wp-config.php` ispravljen na `'wpgs_'` da odgovara stvarnom sadržaju. **Pravilo za 31.08 pravu migraciju: proveriti stvarni prefiks u dump/CREATE TABLE pre pisanja wp-config.php, ne pretpostaviti "wpGs_" iz dokumentacije.**

**Verifikacija (Korak 10):** 401 bez auth / 200 sa auth (`curl -I`) · `/industrijski-podovi/`, `/katalog/`, `/kontakt/` sve 200 · bare `/proizvod/` 404 (očekivano — samo permalink base slug, ne prava arhiva stranica, isto kao `/kategorija-proizvoda/` bare) ali `/proizvod/mrezica-za-kos/` (pravi proizvod slug) 200 potvrđuje da permalink struktura radi · homepage `<title>Početna | Antas Line</title>` (Rank Math aktivan, sanity check prošao) · nova avgustovska slika (`16919-gallery-1-300x300.webp`) 200 potvrđuje da je uploads-diff stvarno upisao fajlove.

**Backup napomena:** DB reset je bio bez `wp db export` pred-koraka (prompt ga nije tražio jer je staging jednosmerni radni prostor, ne izvor istine) — ako zatreba rollback na pre-refresh stanje, jedini put je ponovno postavljanje sa 07-21 arhivom koja je upravo obrisana; nije rizik po pravi sadržaj (lokal + live ostaju netaknuti).

Nove lekcije upisane u [[reference/naucene-lekcije]]. Sledeći korak: Miroslav vizuelno pregleda `staging.antasline.com` (auth `stagingtest`/kredencijali u `~/staging-htaccess-creds.txt`).

## 2026-08-06 [claude-code] [W1 meni] Mega meni — sticky header fix + ikonice, ikonice na kraju nerešene — SESIJA ZATVORENA 🟡

**Kontekst:** Nastavak sesije istog dana (posle nedeljnog izveštaja ispod). Glavni zadatak isprva bio "probna migracija na staging" — preusmereno na M zahtev da meni bude vizuelno doteran pre toga (ikonice, sticky header, veći dropdown).

**Trajno urađeno i zadovoljavajuće (nije vraćano nazad):**
- **Ceo header (topbar + glavni red) sad je sticky** — pre ove sesije bio je sticky samo glavni red. Pravi uzrok komplikacije: WoodMart header CSS se generiše JEDNOM i keš-uje u `wp_options` (`xts-default_header-css-data`/`-status`/`-version`/`-site-url`), bez auto-refresh na izmenu koda — prva izmena (`_menu_item_width`/`sticky` postmeta na top-bar redu) je tiho pala na stari keš (`--wd-top-bar-sticky-h: .00001px`). Fix: obrisan keš (4 opcije), forsirana regeneracija. **Nova lekcija upisana ispod.**
- **Logo suženo** (200→160px desktop, 150→120 sticky) i **CTA polje** — telefon zamenjen dugmetom "Zatraži ponudu" (`/kontakt/`) — desktop telefon slabo konvertuje naspram forme (prava konverzija = `/hvala-za-poruku/`, ne poziv). Telefon ostaje u topbar-u i mobilnom meniju.
- **Meni "Terase i dom" prelom teksta unutar stavke** — WoodMart `.nav-link-text` je `white-space:normal` + flex-shrink, pa je dvočlana labela pucala u 2 reda umesto da cela stavka prelomi u novi red. Fix: `white-space:nowrap !important` na `.wd-nav-main > li > a .nav-link-text`.
- **Dropdown paneli uvećani** — širina podignuta na izvoru (`_menu_item_width` postmeta: 760→900px za Sport/Industrija, 540→660px za ostale 4), padding/font-size kolona uvećani u CSS. 🔴 Prvi pokušaj (CSS `calc(var(--wd-dropdown-width) + 140px) !important` na `.wd-dropdown-menu`) je slomio layout (panel pao na ~186px, 3 kolone u 1) — self-referencing CSS custom property se nije razrešio kako se očekivalo. Rešeno menjanjem izvorne vrednosti umesto CSS var trika.

**Ikonice — 8+ rundi, na kraju nerešeno, Miroslav sam traži zamenu:**
1. FA font ikonice (6 top-level) → ✅ prihvaćeno
2. Proširenо na 14 podsekcija + 59 pojedinačnih linkova (custom SVG linijski stil, WoodMart native "Menu item icon" mehanizam — `_thumbnail_id`+`_menu_item_image-type=image`, ne font-icon) → ❌ "previše generičke, previše istih" (npr. tenis/stoni tenis ista ikonica)
3. Solid-fill Noun Project stil (14px) → ❌ "ne prepoznaje se šta je"
4. Linijski stil vraćen, tanje linije (1.3 umesto 1.7), uvećano (18-24px) → ❌ "vratio si stare, nije dobro" — traženi Noun Project primeri
5. Struktura promenjena po M zahtevu: top-level čist tekst (bez ikonica), naslovi kolona bez ikonica, ikonice SAMO na 59 pojedinačnih linkova, uvećano na 30px/17px font
6. Boja promenjena crvena→navy (`--al-navy #0E2950`), Sport ikonica zamenjena stvarnim Noun Project fajlom (`noun-soccer-ball-5709538`, Miroslavljev Pro nalog — attribution tekst bezbedno isečen jer ima Pro pretplatu) → ✅ ova jedna ikonica prihvaćena
7. **Design skillovi trajno uključeni** (M eksplicitni zahtev) — `~/.claude/settings.json` `skillOverrides` "off" unosi obrisani za design/ui-ux-pro-max/banner-design/brand/design-system/slides/ui-styling; `frontend-design@claude-plugins-official`→`true` u `.claude/settings.local.json`; `CLAUDE.md` §8.7 prepisan (auto-koristi kad treba dizajn, ne pitaj)
8. Ikonice generisane preko `design` skila (Gemini 3.1 Pro, SVG tekst) — **2 prava bug-a nađena i popravljena u `~/.claude/skills/design/scripts/icon/generate.py`** (SVG se sekao pre kraja markdown fence-a kad model potroši budžet na "thinking" tokene → dodat fallback ekstraktor; `--output` nije pravio folder → dodat `os.makedirs`). Batch od 59 (~35 min), **~18/59 vizuelno pokvareno** (isti detalj-boja-na-boju problem ili potpuno prazno, XML validan ali besmisleno) — zamenjeno ručnim dizajnom. → ❌ "ne valja i dalje"
9. Ručni redizajn preko `frontend-design` skila — potpis "kos ugao" (isti `clip-path` motiv kao sajt-ovi CTA dugmići) primenjen na 16 pločica/planki ikonica → ❌ "ne valja i dalje"

**Finalno stanje (svesno ostavljeno takvo):** top-level meni = čist tekst, 14 naslova kolona = čist tekst, **59 pojedinačnih linkova imaju ikonice** (mešano: par desetina AI+ručno, navy boja, `uploads/meni-ikonice/*.svg`, WP attachment-i 17606-17678 + 17620-17678). Miroslav je odlučio da **sam nađe adekvatne ikonice** (Noun Project ili drugde) i preda ih — čeka se predaja fajlova, ne nova runda dizajna.

**#ceka-miroslav:** ikonice za meni (Miroslav samostalno traži/bira). Kad stignu — samo prepisati sadržaj postojećih fajlova u `wp-content/uploads/meni-ikonice/meni-<naziv>.svg` (isti attachment ID-evi, nema DB izmene potrebne), isti postupak kao za `noun-soccer-ball-5709538`.

**Backup-ovi (baza, hronološki):** `antasline_local_2026-08-06_pre-meni-ikonice-postmeta.sql` → `-podsekcije.sql` → `-sve-stavke.sql` → `-svg-ikonice.sql` → `-samo-top-ikonice.sql` → `-meni-submeni-ikonice.sql` → `-meni-leaf-ikonice.sql`. CSS/PHP `.bak-2026-08-06-*` fajlovi u `woodmart-child/`.

**Migracija na staging (originalni zadatak) — NIJE izvršena ove sesije**, potisnuta menu poliranjem. Ostaje sledeći kandidat.

---

## 2026-08-06 [claude-code] [W5 5.4] Nedeljni izveštaj (30.07–05.08 vs 23–29.07) — SESIJA ZATVORENA ✅

**Kontekst:** Poslednji nedeljni izveštaj bio 07-30 (tačno nedelju dana ranije) — dospeo po ritmu, izabran kao glavni zadatak posle otvaranja sesije (W1/W3/C1-C3 iscrpljeni, W2 preostalo čeka M).

**Rezultat (preko sopstvenog konektora, GA4+Ads+GSC):**
- GA4: korisnici 790 (766, +3,1%), sesije 915 (878, +4,2%) — oboje stabilno. `generate_lead` 19 (18), `tel` 19 (19) skoro nepromenjeni. `mailto` i dalje 0 — čeka M odobrenje za GTM Submit (spojeno sa već odobrenim `pdf_download`/`gallery_view` draftovima).
- **Hvala-proxy (prava konverzija) 16 vs 12, +33%** — najjači realan signal nedelje, iznad generate_lead/tel.
- Ads: potrošnja 9.142,12 RSD (8.010,35, +14,1%), klikovi 345 (347, stabilno), **konverzije 6 vs 8 (−25%)** — ECOTILE CPC skočio 52,20→76,56 RSD (+47%) uz pad klikova (51→41). Jedna nedelja, ne trend — pratiti sledeći presek.
- 🔑 **Kumulativ (od 01.06, mesec-nula) preračunat direktno umesto sabiranja nedeljnih brojki**: hvala-proxy **109**, Ads uvezeno **24** — poslednje je ušlo u prag 20–30 za Smart Bidding odluku (zadatak 4.8, Maximize Clicks → Maximize Conversions). Ranije PROGRESS brojke (93/18, od 07-30) bile zastarele.
- GSC 28d (06.07–03.08): top 5 prilika i dalje dominirane epoksid upitima (4/5) — očekivano po conquest strategiji, ne prava rupa. `podovi za terase` (292 impr, poz 10,4) i `industrijski podovi` (175 impr, poz 11,7) su realni core kandidati za dalji refresh.

**Akcija nedelje predložena Miroslavu:** razmotriti prelazak na Maximize Conversions (Ads kumulativ 24, unutar praga).

**Nije urađeno dalje ove sesije** — nema drugog neblokiranog glavnog zadatka bez nove M odluke (pun spisak #ceka-M u [[PROGRESS]] Blokeri, W2 stavke iz prethodnog dela sesije: PR/direktorijumi/GMB recenzije/Ads landing preusmeravanje).

Detalji: [[PROGRESS]], [[dnevnik/ADS-DNEVNIK]]

---

## 2026-08-06 [claude-code] [W2 GEO §3] NAP eksterni direktorijumi provereni — 11000 potvrđen dominantan, 2 sitna van-kontrole nalaza ✅

**Kontekst:** Poslednja otvorena stavka iz `[[seo/geo-ai-plan]]` sekcije 3 (Entitet/NAP) — posle 07-27 fix-a na `/kontakt/` (11050→11000) ostalo je neprovereno da li GMB profil i eksterni direktorijumi (Yellow Pages i sl.) i dalje negde nose stari poštanski broj.

**Metod:** WebSearch/WebFetch (read-only, van naše kontrole — ne može se menjati odavde, samo dijagnostika).

**Nalaz:** 11000 je dominantan i tačan gde god postoji izvor — Google-ov sopstveni sažetak (verovatno iz GMB Knowledge panela) i `mojakompanija.com` oba eksplicitno "Ulcinjska 13, 11000 Beograd". `011info.com`/`daibau.rs` ne prikazuju poštanski broj uopšte.

🟡 **Dva sitna nalaza, van naše kontrole, #ceka-miroslav ako želi ispravku:**
1. `gradnja.rs/adresar` navodi broj **"Ulcinjska 13-15"** umesto "13"
2. `planplus.rs` (ulični/poštanski registar, ne firma-specifičan unos) tvrdi da je Ulcinjska ulica u Zvezdari zvanično pod poštanskim brojem **11050** — suprotno M odluci od 07-27. Nezavisan izvor, vredi da M svesno potvrdi da je 11000 i dalje namerno pojednostavljenje, ne da se ovaj nalaz protumači kao da je 07-27 odluka bila pogrešna.

**Nije urađeno:** izmena na eksternim direktorijumima (nema pristup njihovim panelima, to je M teren ako odluči da je vredno).

Zatvara poslednju otvorenu stavku [[seo/geo-ai-plan]] §3. Detalji: [[seo/geo-ai-plan]]

---

## 2026-08-06 [claude-code] GMB kvota retest — i dalje 429, sesija zatvorena bez daljeg rada 🟡

**Kontekst:** Otvaranje sesije preko `/antasline-sesija` — N5 (04–10.08) glavni zadatak je W3 3.6 CWV, ali LCP nema više nizak-rizik lokalnih koraka (blokirano na LiteSpeed, hosting eksplicitno odbio 07-30 zbog bezbednosnih napada). W1/W3/C1–C3 suštinski iscrpljeni, skoro sve preostalo u Blokerima čeka M odluku. Ponuđene opcije Miroslavu — izabrao GMB kvota quick-win retest.

**Urađeno:** `gmb_report.py --from 2026-07-30 --to 2026-08-05` — isti rezultat kao 07-30 i 08-05: `429 Quota exceeded (mybusinessaccountmanagement.googleapis.com, Requests per minute)`. Treći uzastopni retest bez promene — Google revizija/propagacija i dalje u toku, nema nove akcije na našoj strani. PROGRESS.md Blokeri red ažuriran (datum).

**Nije urađeno:** ništa dalje ove sesije — nema neblokiranog glavnog zadatka bez nove M odluke (v. Blokeri u [[PROGRESS]] za pun spisak #ceka-M stavki: BLOK E foto arhiva, mailto GTM Submit, kontakt forma validacija na live, veštačka trava mapiranje, 14 proizvoda bez slike).

Detalji: [[PROGRESS]]

---

## 2026-08-06 [claude-code] EXPONA Living Clic (16919) — materijal nađen na polyflor.se, uvezen; PROGRESS Blokeri čišćenje ✅

**Kontekst:** M pitao gde stojimo sa čekanjem-na-Miroslava stavkama; PROGRESS.md Blokeri je flagovao 16919 kao „nema materijala na disku, distributer nije dostavio" (zapis 2026-07-29). M tražio: proveriti na proizvođačevom sajtu pre nego što se proizvod prebaci u draft, i počistiti zastarele tragove u istoj sesiji.

**Nalaz:** ranija provera je gledala samo lokalni disk + `objectflor.de` (floor-finder 404 za ovu liniju, poznato od ranije). Nije probala `polyflor.se` — isti proizvođač (Polyflor/James Halstead grupa), ista šema kao ranija Commercial/Simplay EN brošura sesija (2026-07-30). Tamo postoji pun EN tehnički list (fajl ima "SE" u imenu ali sadržaj je engleski Polyflor Nordic obrazac), brošura i 8 fotografija — materijal postoji, nije trebalo prebacivati proizvod u draft.

**Urađeno:** backup (`antasline-backups/antasline_local_2026-08-06_pre-expona-living-clic.sql`), preuzeto sa `polyflor.se`: tehnički list + brošura (PDF, uvezeni u medijateku), glavna slika (600×600 uzorak dezena, već kvadratna) + 3 galerijske fotografije ugrađenog poda (1600×1400/1523×1333 → center-crop 1:1, max 1000px, WebP — isti obrazac kao `job-w7f29-dobavljac-slike.php`). Nova sekcija „Tehnička dokumentacija" dodata na stranicu. Skripta: `migracija/alati/job-16919-expona-living-clic.php`.

🔴 **Usput ispravljena netačna specifikacija**: postojeći tekst je tvrdio sloj za habanje **0,55 mm** — vrednost očigledno prepisana sa EXPONA Commercial (ista brojka), nikad proverena za Living Clic. Pravi tehnički list kaže **0,3 mm**. Cela tabela zamenjena stvarnim podacima iz PDF-a (18 dB redukcija zvuka, Bfl-s1 požarna klasa, R10/DS protivkliznost, 2G/5G Välinge sistem, IXPE 1,0mm, itd.) — ništa nije izmišljeno, sve direktno iz `Tekniskt-datablad_Expona-Living-Clic` PDF-a. „Tehnički list još nije dobavljen" napomena uklonjena, FAQ odgovor ažuriran (više ne upućuje na kontakt za nedostajuće podatke).

**Verifikacija:** HTTP 200, 1×H1, oba PDF linka 200, oba JSON-LD bloka (Rank Math Product + FAQPage) `json_decode` validna, slika korišćena 17× na stranici (thumbnail + galerija + lightbox varijante). Upis preko `$wpdb->update()` direktno (F7.24 pravilo, izbegnut `wp_update_post()` unslash rizik).

**PROGRESS Blokeri čišćenje (isti zahtev):** dva zastarela zapisa uklonjena/skraćena — (1) red „W7 F2 — dve preostale stavke" je i dalje sadržao AMSS logo pitanje iako je ono zatvoreno 2026-07-30 (zaseban red iznad) — uklonjen taj deo, ostavljena samo živa stavka (mapiranje veštačke trave). (2) ceo red „W7 — dve preostale odluke koje čekaju M" (2026-07-28) obrisan — obe njegove stavke (engleske Expona brošure, mapiranje trave) su duplikat/već zatvorene drugde u istoj sekciji.

Detalji: [[PROGRESS]], `migracija/alati/job-16919-expona-living-clic.php`

---

## 2026-08-05 [claude-code] [W4 4.11, M13] Meta Pixel ugrađen preko GTM — GTM-TRDT8K9 Version 12, potvrđeno da stiže na Meta strani ✅

**Kontekst:** M13 (Pixel ID od Miroslava) je bio blokator za W4 4.11 — stigao usred sesije (`179235072594933`), zajedno sa potvrdom da ide preko GTM-a (isti obrazac kao GA4/Ads), na lokalu vs. live (odgovor: "izgleda da je već na sajtu" — provereno da NIJE, ni na lokalu ni na live pre ove sesije).

**Urađeno:** dva nova GTM taga u GTM-TRDT8K9, isti obrazac kao postojeći GA4 setup:
- **Meta Pixel - Base Code** — Custom HTML (standardni fbq init + PageView), trigger *All Pages*
- **Meta Pixel - Lead** — `fbq('track','Lead')`, trigger *Page View - Thank You* (isti trigger kao `GA4 - Generate Lead`, `/hvala-za-poruku/`), sa Tag Sequencing (Base Code garantovano fajruje prvo)

**Verifikacija (troslojna):** (1) GTM Preview na live URL-u pre objave — oba taga fired 1×, `fbevents.js` učitan, `fbq.loaded=true`, queue prazan. (2) `curl` direktno na `googletagmanager.com/gtm.js?id=GTM-TRDT8K9` posle objave — potvrđen pixel ID i `connect.facebook.net` u živom kontejneru (CDN propagacija prošla). (3) Miroslav potvrdio u Meta Events Manager: **2 eventa stigla sa antasline.com**.

**Usput:** Submit je objavio i 2 ranije neodobrena drafta iz iste Workspace (`pdf_download`, `gallery_view`) po M odobrenju — sad su i oni live. GTM Version 12, opis verzije sadrži pun spisak. Pristup GTM-u zahtevao prebacivanje Chrome naloga (bio ulogovan pogrešan Google nalog `crpgujam@gmail.com` bez GTM pristupa; ispravan je `miroslav.markovic109@gmail.com`).

**Nije urađeno:** verifikacija domena u Meta Events Manager (Business Settings → Brand Safety), Event Match Quality provera, Conversions API (server-side) — nema pristup Meta Business nalogu, Miroslav mora sam. Enhanced Conversions za Google Ads (pomenuto u CLAUDE.md §4.1) ostaje odvojen, nedirnut zadatak.

**Dopuna, ista sesija — Advanced Matching (dupli sloj, Automatic + Manual):** Miroslav pokušao da uključi Automatic Advanced Matching u Meta Events Manager (Settings), prvi pokušaj se tiho vraćao na off bez poruke o grešci — drugi pokušaj uspeo (vrv. bio privremen glič ili je trebalo vreme da se propagira domain verifikacija). Odlučeno da se paralelno doda i **Manual Advanced Matching** preko GTM-a (Meta kombinuje oba signala, nema konflikta).

**GTM izmene (GTM-TRDT8K9, Version 13):**
- Novi trigger `Klik na Posalji (Zion forma)` — Click, All Elements, Click Classes contains `zn_contact_submit`
- Novi tag `Meta Pixel - Capture Lead Data` — Custom HTML, koristi built-in `{{Click Element}}` varijablu, na klik hvata email/telefon iz forme (`form.closest('form.zn_contact_form')`, polja preko `input.zn_validate_is_email`/`input[name*="telefon"]`), normalizuje (lowercase email; telefon → digits-only sa `381` prefiksom umesto vodeće nule), upisuje u `sessionStorage` (`al_am_em`/`al_am_ph`) — **samo** ako `data-redirect` atribut forme sadrži `hvala-za-poruku` (safety guard)
- `Meta Pixel - Base Code` izmenjen: čita `sessionStorage` na svakom page loadu, prosleđuje `em`/`ph` kao treći parametar `fbq('init', ...)` (Meta pixel.js sam hešuje SHA-256 u browseru), pa odmah briše sessionStorage da se ne provuče u nepovezane naredne page view-ove u istoj sesiji

**Verifikacija (bez slanja prave lažne prijave u office@antasline.com — namerno izbegnut klik na pravi Submit):**
1. Selektori/normalizacija testirani direktno preko JS na `/kontakt/` (test vrednosti upisane u polja, izvučene, normalizovane, pa odmah očišćene) — email `test.lead@example.com`, telefon `069 234 00 72` → `381692340072`, `data-redirect` ispravno pročitan
2. Read-strana testirana preko GTM Preview (Tag Assistant) na `/hvala-za-poruku/` sa ručno seed-ovanim `sessionStorage` — `fbq._instance.pixelsByID['179235072594933'].userData` potvrđeno sadrži `em`+`ph` posle page load-a, `sessionStorage` potvrđeno očišćen posle čitanja

**🔴 Poznato ograničenje, upisano u opis verzije:** selektori su vezani za trenutna imena polja na **live Zion Builder** formi (`zn_form_field_email_*`, `zn_form_field_kontakt_telefon_*`, klasa `zn_contact_submit`) — lokalni WoodMart build već koristi CF7 sa drugačijim imenima, pa će ovaj GTM tag morati da se prepravi na dan migracije (2026-08-31). Svesna odluka (M: "sada na live formi", ne čekati migraciju) — ubaciti u migracioni checklist kao stavku za taj dan.

---

## 2026-08-05 [claude-code] [W4 4.12] LinkedIn Insight Tag — GTM spec pripremljen, čeka Partner ID 📋

**Kontekst:** Pixel ID za Meta (4.11, M13) još nije stigao od Miroslava — dok se čeka, uzet paralelan zadatak 4.12 (LinkedIn Insight Tag), takođe blokiran na pristupu (M14), ali priprema spec-a ne zavisi od pristupa.

**Urađeno:** `[[reference/linkedin-insight-tag-prep]]` — pun GTM ožičavanje spec: Faza A (built-in "LinkedIn Insight Tag" tag, All Pages trigger, `ad_storage` consent gate — isti mehanizam kao postojeći GA4/tel tagovi, bez izmena na WP mu-plugin strani) + Faza B (Conversion Tracking, Lead-ekvivalent event na `/hvala-za-poruku/` Page View, mirror `generate_lead` triggera — odloženo do stvarnog Ads naloga). Konkretni koraci za Miroslava upisani u fajl (Campaign Manager pristup → Insight Tag → Partner ID).

**Nije urađeno:** stvarno kreiranje taga u GTM UI-ju — čeka Partner ID od Miroslava (M14 i dalje otvoreno), kao i kod 4.11.

---

## 2026-08-05 [claude-code] [BLOK E] Foto arhiva (Downloads) — inventar napravljen, čeka M odluku 📋

**Zadatak:** "Pogledaj opet one foldere sa fotkama, vidi da li Gemini tu može da pomogne da se još neka iskoristi za sajt." Gemini-vizuali skill učitan, ali postojeći proizvod-foto red (`reference/gemini-red-cekanja.md`) je već bio potpuno zatvoren (96/97, 2026-08-05) — trebalo je naći na šta se zahtev zapravo odnosi.

**Nalaz:** `C:\Users\Miroslav\Downloads` (deljen sa Miroslavljevim drugim poslovima — Qoltec elektronika, solarni paneli, Dahua/Hikvision kamere, R-Tek mreže, roofing stock foto) sadrži veliki, neiskorišćen arhiv **pravih referentnih/instalacionih fotografija** za AntasLine, potpuno odvojen od proizvod-kataloga: ~185 jedinstvenih fajlova posle uklanjanja duplikata i filtriranja nepovezanog sadržaja.

- **Sport tereni (Bergo/3×3/pickleball/tenis/odbojka) ~100** — najveći deo: 2× identična ZIP arhiva "tereni za basket" (Google Drive export, 92 fajla, koristiti samo jednu) + ~10 van zipa. Lokacije širom Srbije/regiona (Avala, Subotica, Zlatibor, Bezdan, Despotovac, Slankamen, Valjevo, Vrčin, Vrdnik, Kanjiža, Jakovo, Sremčica, Graz, Švedska federacija...).
- **ESD/antistatik industrijski ~10** — prave montaže kod klijenata: HTEC Niš, Quectel Beograd, Simanovci, Macola. Direktno pokriva CLAUDE.md §1 glavni B2B fokus.
- **Geoplast/Runfloor (terase/parking) ~20** — mešavina pravih instalacija i proizvođačkih instrukcionih dijagrama, poreklo treba proveriti pre javne upotrebe.
- **Ergomat linija (Isotrack/X-Mat/Mosolut) ~8** — verovatno proizvođački materijal, isto pitanje porekla.

**Odluka Miroslava (AskUserQuestion):** "Samo organizuj i sačekaj moju odluku" (ne postavljati ništa na sajt još) + Gemini `--mode enhance` selektivno kad se izabere konkretan set (ne trošiti kvotu unapred na ceo arhiv).

**Izvršeno:** pun kategorizovan inventar upisan u `reference/foto-arhiva-inventar.md` (kategorije, lokacije po imenu fajla, šta je namerno isključeno kao nepovezano/šum, otvorena pitanja). Bez izmena baze/sajta. Dodato u plan na zahtev: red u PROGRESS.md "Urađeno" tabeli + Blokeri sekciji, dopuna `blokovi/BLOK-E-ai-orkestracija.md` ("Šta čeka Miroslava").

**#ceka-M:** (1) poreklo/prava korišćenja za Geoplast i Isotrack/X-Mat/Mosolut materijal (proizvođački vs. sopstveni), (2) kategorizacija ~7 fotki bez jasne ključne reči u imenu fajla (Bela Crkva, Srebrenica, British International School Belgrade, Spanoulis-Belgrade-7, OS Jovan Cvijić), (3) format upotrebe — referenca-galerija na proizvod stranicama vs. posebna "Reference" portfolio stranica vs. blog ilustracije.

## 2026-08-05 [claude-code] [W3/a11y] Alt tekst u sadržaju postova — pravih 26/26 zatvoreno, 154 ikonice potvrđene ispravne ✅

**Zadatak:** Nastavak crvenog reda čekanja "180/684 slika u sadržaju sa `alt=""`" iz ranije 08-05 sesije. Prvo dijagnostika (novi alat, `scan-alt-empty.php`, WP-CLI eval-file preko wp-load bootstrap-a) — brojka 180 potvrđena tačno.

**Ključan nalaz koji menja obim zadatka:** od 180, **154 su dekorativne SVG ikonice** (`montaza.svg`, `izdrzljivost.svg`, `protivklizna.svg`, `odrzavanje.svg`, `izgled.svg`, `fleksibilna.svg`, `namena-*.svg`, `sertifikat.svg`, `garancija.svg`, `dostava.svg`) korišćene u `.al-card` F7 standard blokovima na ~33 stranice/posta — svaka stoji direktno pored `<h3>` naslova koji nosi ISTU informaciju (npr. `<img alt="" src=".../montaza.svg"/><h3>Montaža bez zastoja</h3>`). Prazan `alt` na dekorativnoj slici čija se informacija dupliraju u susednom tekstu je **ispravna WCAG praksa** (screen reader ne čita redundantno), ne bag — te 154 NISU dirane.

**Stvarni red čekanja bio je 26 pravih fotografija u 11 postova** (case-study slike, proizvod-u-primeni, sertifikat/garancija bedževi): `3388` štamparije (4), `16608` oštećen industrijski pod (4), `16609` garaža (4), `16611` padel tenis (3), `4813` Bergo ultimate sertifikati (3), `3318` ESD pod (2), `3398` Bergo Solid (2), `3257`/`5276`/`6874`/`16613` (1 svaki). Alt tekst izveden iz postojećeg vidljivog caption-a odmah ispod slike (WP caption obrazac, npr. "Amicus, Beograd", "Ecotile pod u Hankook fabrici nakon 10 godina korišćenja") ili najbližeg naslova — ništa izmišljeno van onoga što stranica već tvrdi.

Upis preko `$wpdb->update()` direktno (F7.24 pravilo — `wp_update_post()` bi pozvao `wp_unslash()` nad celim `post_content`-om). Proba pre upisa: 26/26 `<img>` tagova pronađeno tačno jednom po src-u, 0 preskočeno. Backup: `antasline_local_2026-08-05_pre-alt-tekst-sadrzaj-postovi.sql`.

**Verifikovano:** ponovni sken posle upisa → tačno 154 preostalih (33 postova, sve ikonice, kako je i predviđeno), 11/11 izmenjenih URL-ova 200/1×H1. Zadatak zatvoren u potpunosti — nema više preostalih pravih fotografija bez alt-a u sadržaju postova/stranica; preostalih 154 je namerno `alt=""` i ne treba dirati.

## 2026-08-05 [claude-code] [W3/a11y] PROGRESS Blokeri čišćenje — stara h3-pre-h1 stavka uklonjena ✅

**Zadatak:** Nova sesija (drugi `/antasline-sesija` poziv istog dana) izabrala "h3-pre-h1 istraga" kao glavni zadatak, na osnovu 🆕-obeležene stavke u PROGRESS Blokeri sekciji. Nezavisna istraga (`curl` + grep na `/epoksidni-podovi-ili-ecotile-podovi/`, pa `template-tags.php:1723`) je došla do IDENTIČNOG nalaza koji je ranija sesija istog dana već zatvorila (v. dole, "h3-pre-h1 nalaz na single post stranicama — PROVEREN, NIJE STVARAN BAG"): `woodmart_page_title()` ima hardkodovanu h3 granu za blog-single kontekst, ali Lighthouse `heading-order` flaguje samo skokove unapred — verifikovan non-issue.

**Uzrok neusklađenosti:** "Urađeno" tabela u PROGRESS.md i DNEVNIK su ažurirani kad je nalaz zatvoren, ali odgovarajuća stavka u Blokeri sekciji (🆕 red) nije uklonjena u istoj izmeni — ostala je da izgleda kao otvoren zadatak.

**Izvršeno:** stari 🆕 red obrisan iz PROGRESS.md Blokeri. Nema izmena koda/baze — čisto dokumentaciono usklađivanje. **Pravilo za ubuduće:** kad se stavka zatvara u "Urađeno", odmah proveriti da li ista stavka postoji i u Blokeri sekciji pa je ukloniti u istom prolazu.

## 2026-08-05 [claude-code] [a11y] Alt tekst za 76/94 proizvoda bez alt-a na glavnoj slici — ZATVORENO ✅

**Zadatak:** Sledeći stavak posle zatvaranja a11y reda čekanja iz 08-04/08-05 — "Alt tekst za slike (67/81 proizvoda bez alt-a)" je bio namerno van obima 07-30 Lighthouse a11y ture, poseban budući zadatak (v. `[[migracija/2026-07-30-lighthouse-a11y-plan]]`). Sveža provera (baza je od 07-30 narasla sa 81→94 objavljenih proizvoda, uglavnom kroz S-serije/Gemini foto rad): **76/94** i dalje bez alt-a na `_thumbnail_id` slici.

**Pristup:** alt = `post_title` (mehanički, ne izmišljena vizuelna deskripcija boje/materijala) — za razliku od ranijih ručno kuriranih alt-ova ("Bergo Excellence — ploča za brodske palube") koji su rađeni pojedinačno uz uvid u fotografiju. 76 proizvoda odjednom bez pregleda svake slike bi rizikovalo netačan opis (protiv CLAUDE.md pravila "ne izmišljati specifikacije") — naziv proizvoda je uvek tačan, sitewide minimum umesto delimičnog kuriranog rada.

**Izvršeno:** `migracija/alati/job-alt-tekst-proizvodi.php` (wp-cli eval-file, proba pa `apply`) — upisuje `_wp_attachment_image_alt` SAMO gde je prazno/NULL, ne dira postojećih 18 kuriranih alt-ova. Dry-run potvrdio listu od 76 (13 proizvoda bez `_thumbnail_id` uopšte, van obima ovog fixa — nepovezano sa F2.9 "40 proizvoda bez slike" redom, koji čeka M). Upis primenjen, verifikovano: DB re-count 0 preostalih bez alt-a, live spot-check (`konusni-stitnik-za-i-profil`) potvrdio `alt="Konusni štitnik za I-profil"` u `<img>`, regresija `/kategorija-proizvoda/industrijski-podovi/` 200. Backup: `antasline_local_2026-08-05_pre-alt-tekst-proizvodi.sql`.

**Preostalo van obima ove sesije:** 180/684 slika UNUTAR sadržaja postova/stranica sa `alt=""` — veći, ne-mehanički zadatak (svaka slika treba pregled konteksta, ne mehaničko popunjavanje kao proizvod-thumbnail), red čekanja za buduću sesiju, nije #ceka-miroslav.

## 2026-08-05 [claude-code] [W3/a11y] h3-pre-h1 nalaz na single post stranicama — PROVEREN, NIJE STVARAN BAG ✅

**Zadatak:** Poslednja stavka iz a11y reda čekanja (08-05 nalaz): single post stranice imaju `<h3 class="entry-title title">Aktuelnosti</h3>` PRE glavnog `<h1 class="wd-entities-title wd-post-title title">`.

**Uzrok nađen:** to je WoodMart core "page title" traka (`woodmart_page_title()`, hardkodovana h3 grana kod blog single konteksta, `inc/template-tags/template-tags.php:1723`) koja prikazuje naziv blog arhive ("Aktuelnosti") kao kontekst/breadcrumb iznad stvarnog članka — sličan obrazac kao WooCommerce shop naslov iznad kategorija, ali za blog je hardkodovan na h3 umesto promenljivog `$title_tag`.

**Provera (Lighthouse `heading-order` audit, axe-core pravilo):** pravilo flaguje SAMO skokove unapred (npr. H1→H3, kao što je bio slučaj sa `/katalog/` ranije u ovoj sesiji) — opadajući redosled (H3→H1→H2...) nije prekršaj. Pokrenut pun accessibility audit na `/epoksidni-podovi-ili-ecotile-podovi/`: **score 1.0, `heading-order` score=1, 0 stavki.** Nema šta da se popravlja — red čekanja se zatvara kao verifikovan non-issue, ne kao odloženi rad.

## 2026-08-05 [claude-code] [W3/a11y] color-contrast na "sa PDV" cena-sufiksu (mist sekcije) — ZATVORENO ✅

**Zadatak:** Nastavak iste sesije — nalaz otkriven usput dok se verifikovala regresija heading-order/target-size fixa (v. dole).

**Dijagnoza:** `.woocommerce-price-suffix` ("sa PDV") nasleđuje generičku sivu boju (#767676, WooCommerce/tema default) — na beloj pozadini prolazi (4.545:1), ali product grid na kategorija stranicama ide kroz `.al-section--mist` wrapper (`--al-mist: #EEF3F8`), gde isti tekst pada na 4.06:1 (min 4.5:1). Isti obrazac kao već zatvoren `.al-label` mist-fix iz 07-30 batch-a (v. `[[DNEVNIK-NAPRETKA]]` 2026-08-04 Lighthouse plan) — taj batch je pokrio custom `.al-` komponente, ne generičke WooCommerce elemente kao ovaj.

**Fix:** `.al-section--mist .woocommerce-price-suffix { color: #555; }` u `antas-design.css`, odmah posle postojećeg `.al-label` mist-fix reda (isti obrazac, isti `--color-gray-700` token). Testirano uživo pre upisa (screenshot: tekst vidljivo tamniji, bez pomeranja layout-a).

**Verifikovano:** `/kategorija-proizvoda/industrijski-podovi/` accessibility 1.0 → 1.0 (heading-order/target-size iz prethodnog fixa i dalje score=1, color-contrast sada takođe 1). Regresija: `kosarkaske-konstrukcije` i `vestacka-trava` kategorije 200.

## 2026-08-05 [claude-code] [W3/a11y] Sitewide heading-order (shop/search) + target-size (product kartice) — oba ZATVORENA ✅

**Zadatak:** Najavljena "sledeća a11y tura" iz 08-04/08-05 zapisa (PROGRESS Blokeri): sitewide `heading-order` + `target-size` na product karticama, WoodMart core layout.

**Dijagnoza (Lighthouse 13.4, `npx lighthouse --only-categories=accessibility`, JSON audit detalji):**
- `heading-order`: samo `/katalog/` (WooCommerce shop archive) i pretraga (`?s=...&post_type=product`) idu H1→H3 direktno (nema H2 između) — product-kategorija arhive (`/kategorija-proizvoda/...`) VEĆ imaju sopstveni H2 (al-display--lg sekcije), home/single-product/blog arhiva su čisti, nisu dirani.
- `target-size`: `h3.wd-entities-title a` (naslov proizvoda) i `.wd-product-cats a` (kategorija-tag) na SVAKOJ product kartici sitewide — goli tekst linkovi ~16-18px visoki, ispod Lighthouse minimuma 24×24px + nedovoljan razmak između njih.

**Fix (bez diranja vendor/core fajlova, isti obrazac kao 08-04/08-05 heading fixevi):**
- `mu-plugins/al-a11y-blog-archive-h2.php` proširen (bio samo za blog arhivu): sad ubacuje `<h2 class="al-sr-only">Lista proizvoda</h2>` i za `is_shop()`/`is_search()`, preko istog postojećeg core hook-a `woodmart_page_title_after_title`.
- `antas-design.css`: nova sitewide pravila `.wd-entities-title a, .wd-product-cats a { display: inline-block; padding: 4px 0; }` — raste stvarna klikljiva površina (title link 17.8px→29px, cats link 16.7px→27.9px, oba ≥24px) bez menjanja vidljivog font-size-a. Testirano uživo preko Chrome DevTools injekcije PRE upisa u fajl (screenshot pre/posle, razlika samo par px dodatnog razmaka između naslova i kategorije, grid se ne lomi).

**Verifikovano (Lighthouse re-run posle fixa):** `/katalog/` accessibility 1.0 (heading-order + target-size oba score=1, 0 stavki), pretraga heading-order/target-size score=1 (ukupan 0.97 zbog NEPOVEZANOG nalaza ispod). Regresija čista na 4 već-čiste stranice (home, kategorija, proizvod, blog arhiva) — heading-order/target-size ostaju score=1 svuda.

**🆕 Nov, odvojen nalaz (van obima, potvrđeno da NIJE regresija ovog fixa — isti score i u baseline pre-fix merenju):** `color-contrast` na `/kategorija-proizvoda/industrijski-podovi/` (i verovatno sve kategorije) — `.woocommerce-price-suffix` ("sa PDV" tekst uz cenu) 4.06:1 kontrast, ispod 4.5:1 minimuma (`#767676` na `#eef3f8` pozadini, 10.8px font). Red čekanja za narednu a11y sesiju, nije #ceka-miroslav.

## 2026-08-05 [claude-code] [W3/a11y] Blog arhiva (/aktuelnosti/) heading-order fix — H1→H3 skip zatvoren ✅

**Zadatak:** Red čekanja od 08-04 (v. [[reference/naucene-lekcije]]/PROGRESS Blokeri) — `/aktuelnosti/` je jedina stranica koja je preživela sitewide heading-order fix (widget H5→H3, 08-04) i dalje sa skip-om, drugačiji uzrok od widget-a.

**Dijagnoza potvrđena:** WoodMart core `templates/content-default.php` (i 5 sličnih varijanti) hardkoduje `<h3 class="wd-post-title">` za svaku karticu u blog loop-u — nema theme opcije analogne `widget_title_tag` za post-title u loop-u (proverено u `xts-woodmart-options`, postoje samo `page_title_tag` i `widget_title_tag`). Arhiva ima H1 (naslov "Aktuelnosti", `page_for_posts`=21) → odmah H3 kartice, nula H2 između.

**Fix (bez diranja vendor/core fajlova):** core funkcija `woodmart_page_title()` već zove `do_action('woodmart_page_title_after_title')` odmah posle H1, u svakoj grani (portfolio/shop/generic/blog). Novi mu-plugin `mu-plugins/al-a11y-blog-archive-h2.php` kači se na taj hook, ubacuje vizuelno sakriven `<h2 class="al-sr-only">Lista članaka</h2>` samo kad `is_home() && !is_front_page()` (=/aktuelnosti/, ne home). Nova utility klasa `.al-sr-only` (clip-based sakrivanje, standardni obrazac) dodata u `antas-design.css`. Ostaje i posle migracije (nije lokalni workaround kao mail-log/harness).

**Verifikovano:** `/aktuelnosti/` 200, heading niz sada H1→H2→H3 (curl potvrđen), 0 PHP grešaka/warning-a u izlazu. Regresija čista: home 200, proizvod-stranica 200, `/industrijski-podovi/` 200, `/pop-tenis/` 200, `/kontakt/` 200, conquest-post 200.

**Nov, odvojen nalaz (van obima ove sesije):** single post stranice (npr. `epoksidni-podovi-ili-ecotile-podovi/`) imaju `<h3 class="entry-title title">` PRE glavnog `<h1 class="wd-entities-title wd-post-title title">` u document-outline redosledu (verovatno related-posts/sličan blok iznad `<article>`, ne diranje ove sesije) — nema poznatog fix-a, ide u red čekanja za sledeću a11y turu, nije #ceka-miroslav.

## 2026-08-05 [claude-code] [BLOK E] Prva prava Gemini foto-batch — 5 proizvoda, pun tok od praznog reda do WooCommerce prikaza ✅

**Zadatak:** Nastavak BLOK E rada (M: "radili smo na gemini i rutiranju, želim da nastavimo"). `reference/gemini-red-cekanja.md` je od 08-04 bio prazan — popuniti ga i stvarno pustiti prvu foto-batch.

**Audit kataloga:** DB upit + `getimagesize()` na `_thumbnail_id` datoteku svih 94 objavljenih proizvoda protiv standarda (1080×1080, kvadrat, bela pozadina) → 9 OK_STANDARD, **72 NONSTANDARD** (prava fotografija, van spec-a — kandidat za `--mode enhance`), **13 NO_THUMBNAIL** (nema nijedne fotografije u arhivi). Red čekanja popunjen sa 4 tijera po poslovnom prioritetu (ESD/industrijski → sport/Bergo → terase/parking/LVT → Ergomat pribor).

**🔴 Nova politika (upisana u skill Gotchas):** `--mode enhance` (postojeća prava slika na ulazu) je bezbedan — model samo čisti kadar. `--mode generate` od-nule za **konkretan brendiran/tehnički proizvod** se NE koristi — rizikuje pogrešnu boju/dimenziju/spoj i implicitno predstavlja nešto što nismo fotografisali, isto obrazloženje kao ranija W7 F2.9 politika. Zato svih 13 NO_THUMBNAIL ostaje van ovog reda, i dalje #ceka-miroslav.

**Backup baze pre rada:** `antasline_local_2026-08-05_pre-gemini-foto-pilot.sql`.

**Pilot + batch, 5 proizvoda stvarno obrađeno:** Ecotile E500/7 (#16538→attach 17522), Ecotile E500/10 (#16540→17524), Ecotile ESD 7mm (#16542→17525), Bergo Ultimate (#16770→17523), Bergo Ultimate FLOW (#16801→17526). Svih 5 verifikovano: HTTP 200 na proizvod-stranici, HTML referencira novi fajl, `_thumbnail_id` ažuriran. Stare slike nisu obrisane (ostaju kao odvojeni attachment, rollback-safe).

**🔴🔴 Krupan nalaz — SKILL.md korak 4 je bio nepotpun/pogrešan.** "Snimi direktno u WooCommerce uploads folder" nije dovoljno — fajl na disku se ne pojavljuje kao glavna slika proizvoda bez pravog WP attachment zapisa. Napisan i testiran `.claude/skills/gemini-vizuali/scripts/import-gemini-photo.php` (bootstrap `wp-load.php` → `wp_insert_attachment()` + `wp_generate_attachment_metadata()` + `set_post_thumbnail()`), SKILL.md ažuriran da referencira pravi tok.

**🔴 Sitan nalaz — generički `gemini_image.py` je jednom pao** na `AttributeError: 'NoneType' object has no attribute 'parts'` (odgovor bez slike uprkos `finish_reason=STOP`, potvrđeno debug pozivom da je `content` bio prazan). Izgleda kao prolazna API varijacija — prost retry istog poziva je rešio (2/2 puta testirano), nije potrebna izmena skripte.

**Usput:** i MySQL i Apache su bili ugašeni na početku sesije (isti simptom kao ranije zabeleženi obrasci) — oba ručno pokrenuta pre bilo kakvog DB/HTTP rada.

**Kvota:** 493/500 preostalo posle batch-a (reset ponoć PT). Sledeći korak: nastaviti kroz `reference/gemini-red-cekanja.md` Tier 1 (preostala 4: rampe + SureGrip) pa dalje, isti tok, batch po sesiji.

## 2026-08-04 [claude-code] [BLOK E] Gemini foto sloj ZAVRŠEN i uživo testiran — API (uz billing) + Chrome chat fallback ✅

**Zadatak:** Nastavak istog dana — prethodna sesija implementirala skillove/skripte i stala na "čeka Miroslavljev API ključ". Ova sesija: pravi setup do kraja i testira uživo.

**Tok:** M napravio Gemini API ključ (Google AI Studio) → sačuvan kao `gemini_api_key.txt.txt` (Notepad dupli ekstenzion) → preimenovan u `gemini_api_key.txt`. `python -m venv venv` + `pip install -r requirements.txt` prošlo čisto. Prvi test poziv (`gemini_image.py --mode generate`) pukao na `ModuleNotFoundError: tzdata` (Windows nema ugrađenu IANA tz bazu, `zoneinfo` je oslonjen na sistemsku bazu koja postoji na Linux/Mac ali ne na Windows-u) → `tzdata` dodat u `requirements.txt` sa `sys_platform=="win32"` markerom.

**🔴 Glavni nalaz — originalna pretpostavka "besplatno bez kartice" je bila netačna za image generisanje.** Posle tzdata fixa, poziv je pao na `429 RESOURCE_EXHAUSTED, limit: 0` — ne privremeni rate-limit, nego **free tier ne pokriva image modele uopšte** (0 dozvoljenih zahteva/dan). Provereno da nije specifično za jedan model: i `gemini-2.5-flash-image` i `gemini-3.1-flash-lite-image` (noviji, iz Gemini 3 generacije koja je u međuvremenu izašla) vraćaju isti `limit: 0`. Kontrolni test potvrdio da tekst-only pozivi (`gemini-2.5-flash`, `generate_content` bez slike) rade normalno na free tier-u — problem je izolovan na image-generation modele, ne na ceo nalog/ključ. WebSearch rezultati o "500 besplatnih slika/dan" su se pokazali zastareli/netačni za ovaj nalog (verovatno SEO-marketing sadržaj API-preprodavaca, ne primarni izvor).

**Odluka (M, posle predočenih opcija):** dodao platnu karticu na GCP projekat vezan za API ključ. Posle toga test poziv prošao čisto — slika generisana i sačuvana. Usput drugi sitan bug: `quota_tracker.py` je pucao na `✓` karakteru (Windows cp1250 konzola ne zna UTF-8 podrazumevano) → `sys.stdout.reconfigure(encoding="utf-8")` dodat, ponovljen test čist.

**Dopunska putanja — Gemini Chat kroz Chrome (M pitao "da li Gemini chat može besplatno da pravi slike"):** potvrđeno da konzumerski gemini.google.com ima ODVOJENU besplatnu kvotu od developer API-ja (dva različita proizvoda, različite kvote — zato chat radi besplatno dok je API vraćao limit 0). Testirano uživo preko `claude-in-chrome` alata: navigacija na gemini.google.com (već ulogovan Miroslavljev nalog), prompt za sliku, generisanje ~15s, klik na download dugme → fajl pao u `~/Downloads/`. Radi, ali nema headless/skriptovan pristup (Google nema public API za konzumerski chat) — ovo je Claude-vođen ručni tok, jedna slika po ciklusu, ne za veliki batch. Dokumentovano kao svestan fallback (API budžet postane briga / eksperimentalni promptovi), ne kao default put.

**Zatvoreno:** Korak 1-3 iz `reference/gemini-vizuali-setup.md` gotovi, status ažuriran. `blokovi/BLOK-E-ai-orkestracija.md` dopunjen tačnim free-tier nalazom (zamenjuje raniju netačnu "~500/dan besplatno" tvrdnju) + Chrome fallback sekcijom. PROGRESS.md bloker red zatvoren. Test fajlovi (`test.webp`, Chrome download PNG) obrisani posle verifikacije, ništa nije ostalo u repo-u.

**Preostaje potpuno opciono:** CCR/DeepSeek (Korak 4 istog setup fajla), ne blokira foto rad.

---

## 2026-08-04 [claude-code] [BLOK E] AI orkestracija — Gemini foto/video sloj implementiran, CCR/DeepSeek dokumentovan (plan mode sesija) ✅

**Zadatak:** Miroslav zatražio da Gemini preuzme foto/video rad za AntasLine (unapređenje postojećih proizvod fotografija, nove/slične varijante, video za sajt/oglase/social) u free modu, uz Claude koji vodi prioritetni red i prati kvotu. Uzgred pomenut "proksi" se u razjašnjenju ispostavio da je zapravo **claude-code-router (CCR)** — alat za rutiranje Claude Code-ovih poziva ka drugim modelima (DeepSeek za kodiranje, Gemini za long-context tekst), ne mrežni proxy za regionalni pristup. Dodatan zahtev: setup mora biti ponovo upotrebljiv na budućim projektima.

**Istraženo (WebSearch):** Gemini image API (`gemini-2.5-flash-image`, "Nano Banana") ima solidan free tier (~500 poziva/dan, reset ponoć Pacific Time), Srbija je zvanično podržan region (proxy nije potreban). **Veo (video) nema free API tier** — besplatan video postoji samo kroz Gemini app/Google Flow web UI (50 kredita/dan) — video ostaje ručni rad. Šire poređenje uradjeno i za DeepSeek (5M token grant), Groq (brz, bez kineske infrastrukture — fallback za DeepSeek), Kimi/GLM/Qwen (rezerva), OpenAI free tier (zahteva data-sharing opt-in, namerno isključen), Microsoft Designer/Bing Image Creator (15 boost/nedeljno, ručna foto rezerva).

**Arhitektura — dva sloja** (da bi setup bio ponovo upotrebljiv van AntasLine):
1. Generički: `~/.claude/skills/ai-vizuali/` (user-level, cross-project) + `C:\Users\Miroslav\ai-tools\` (kredencijali/venv/logovi, namerno odvojeno od `antasline-connector` koji ostaje isključivo Google Ads/GA4/GSC/GMB).
2. Projektni: `.claude/skills/gemini-vizuali/` u vault-u — poziva generički sloj, dodaje proizvod-spec/WooCommerce putanju/GSC red čekanja.

**Implementirano:** `ai-vizuali` skill (SKILL.md + auth.py/gemini_image.py/quota_tracker.py + requirements.txt), `ai-tools/` folder struktura (prazna, čeka ključ), `gemini-vizuali` projektni skill, `reference/gemini-vizuali-setup.md` (checklist + opcioni CCR korak), `reference/gemini-red-cekanja.md` (prazan red), `reference/identifikatori.md` dopunjen, `blokovi/BLOK-E-ai-orkestracija.md` (pun kontekst), uklopljeno u `2026-07-06-MASTER-PLAN-V2` §8 + Veze. Memory housekeeping: postojeća memorija o proizvod-slika spec-u (1000×1000) flagovana kao neusaglašena sa `reference/standard-slika-proizvoda.md` (1080×1080) — proveriti pre prvog batch-a.

**#ceka-miroslav:** Gemini API ključ → `ai-tools/credentials/gemini_api_key.txt` + `python -m venv venv` u `ai-tools/` (koraci 1-3, `reference/gemini-vizuali-setup.md`). CCR/DeepSeek ostaje potpuno opciono, ne blokira foto rad.

**Ništa nije commit-ovano ovu sesiju** (nije traženo).

---

## 2026-08-04 [claude-code] [W3] Sitewide heading-order fix — widget naslovi H5→H3, jučerašnji Lighthouse a11y nalaz zatvoren ✅

**Zadatak:** nastavak jučerašnjeg (2026-08-04) Lighthouse a11y plana — otkriveni "novi nalaz za sledeću turu" (`heading-order` + `target-size` sitewide) sad dijagnostikovan i zatvoren u istoj sesiji.

**Dijagnoza:** curl-om izvučen redosled `<h1-6>` na home/kategorija/proizvod/blog stranicama pokazao dosledan obrazac — svuda gde je sidebar/footer/upsells widget prisutan, naslov skače sa H2 ili H3 (glavni sadržaj) direktno na **H5** (`widget-title`), preskačući nivo(e). Uzrok: WoodMart core `woodmart_get_widget_title_tag()` (`inc/integrations/woocommerce/helpers.php:10`) čita `xts-woodmart-options['widget_title_tag']`, eksplicitno postavljeno na `h5` u bazi (883-key opcioni niz), koristi ga **svih 8 sidebar/widget registracija** (glavni sidebar, shop sidebar, shop filteri, single-product sidebar, footer kolone ×N, mobile menu, full-screen menu) + "You may also like" upsells naslov na single-product.

**Fix:** jedna vrednost promenjena `h5`→`h3` u `xts-woodmart-options` (`update_option()`, ostatak niza netaknut). Sigurno po WCAG heading-order logici — dizanje nivoa (h5→h3) nikad ne može UNETI novi skip, samo ukloniti postojeći (potvrđeno merenjem: na sve testirane stranice nivo neposredno pre widget-a je H2 ili H3, nikad H4). Backup: `antasline_local_2026-08-04_pre-widget-title-tag-fix.sql` (`wpGs_options`).

**Verifikacija (Lighthouse 13.4, `--only-categories=accessibility`):** home i proizvod stranica sad **1.0 a11y score, heading-order i target-size oba PASS** (bili crveni pre fixa). Kategorija (`industrijski-podovi`) heading-order+target-size PASS. HTTP 200/1×H1 na 4 regresivne stranice (5754, 15480, kontakt, cene), 0 novih PHP grešaka u debug.log.

**🆕 Nov, odvojen nalaz (nije popravljen ovu sesiju):** blog arhiva (`/aktuelnosti/`) i dalje ima `heading-order` score 0 — H1 (naslov arhive) skače direktno na H3 (`wd-post-title`, post kartice), preskače H2. **Drugi uzrok** od widget-title problema (post-title tag je hardkodovan `<h3>` u `woocommerce_template_loop_product_title`-analognom kodu za blog, ne kroz `widget_title_tag` opciju) — treba posebnu dijagnozu (verovatno dodavanje H2 sekcijskog wrapper-a oko liste postova, ili provera da li WoodMart nudi opciju za post-title tag kao za proizvod). Nije #ceka-miroslav, samo red čekanja za narednu W3/a11y sesiju.

**Kvantifikacija obima (potvrđuje "veći zahvat" iz jučerašnje napomene):** izmena je pogodila SVE tipove stranica sa bilo kojim widget area-om — home, sve kategorije, svi proizvodi (upsells + sidebar), sve blog stranice (footer), praktično ceo sajt u jednom potezu umesto po-stranici izmena.

---

## 2026-08-04 [cpanel-live] 🔴 Nalaz: kontakt forma na `/kontakt/` tiho odbija validne unose — Firma/Ime i Kontakt telefon polja, bez poruke greške korisniku

**Povod:** nedeljni izveštaj pokazao pad `hvala-za-poruku` pageview-a (6 vs 16, -62%) i `generate_lead` (9 vs 20, -55%) ove nedelje. Umesto nagađanja, testirano uživo na `/kontakt/` (3 test-slanja kroz pravu formu, uz M odobrenje pre svakog — svi test podaci jasno označeni "TEST DIJAGNOSTIKA").

**Uzrok, potvrđen direktno u kombinovanom JS fajlu teme** (`wp-content/litespeed/js/e728…js`, Zion Builder form validacija):
```js
zn_validate_is_letters_ws → e.val().match(/[^A-Za-z\s]/i)   // Firma/Ime — SAMO ASCII slova+razmak
zn_validate_is_numeric    → isNaN(e.val())                   // Kontakt telefon — razmaci/crte/+ odbijeni
```
- **Firma/Ime**: bilo koji broj, tačka, "&", crta, ili **srpska slova sa dijakritikom / ćirilica** odbijaju unos ("Antas d.o.o." bi palo).
- **Kontakt telefon**: `isNaN()` odbacuje razmake — a broj je **svuda na sajtu ispisan baš sa razmacima** ("069 234 00 72"), pa korisnik koji kuca po tom uzoru dobija tihu blokadu.
- Kad validacija padne: JS doda crvenu ivicu (`zn_field_not_valid`) i **NIKAD ne pošalje AJAX ka `admin-ajax.php`** — nema poruke, nema redirect-a, korisnik ne zna zašto se ništa ne desilo. Potvrđeno network-om (0 zahteva ka serveru kod pada validacije).
- Test #2 (čist unos, samo slova) je **prošao ispravno** — AJAX poslat, redirect na `/hvala-za-poruku/` radi. Forma sama po sebi radi, problem je isključivo u prestrogoj/tihoj validaciji.

**Nije nova regresija ove nedelje** — nema `[cpanel-live]` izmena forme u dnevniku, JS je nepromenjen. Verovatno dugogodišnji UX bag koji stalno "krade" deo submit-ova (baseline gubitak), ne objašnjenje celog pada 16→6 ove nedelje (to ostaje moguće prosta varijacija saobraćaja).

**Napomena:** test #2 je poslao pravi test-mejl na `office@antasline.com` (Test Dijagnostika / test-diagnostika@example.com) i upisao +1 u `hvala-za-poruku` pageview brojaču — bezopasno, ignorisati/obrisati mejl.

**#ceka-miroslav odluka o fix pristupu** — nije diran live kod ove sesije (samo read-only browser test). Predlog kad se odobri: ublažiti regex (dozvoliti brojeve/tačke/dijakritiku u Firma/Ime, dozvoliti razmake/crte/+ u telefonu pre normalizacije) + dodati vidljivu poruku greške umesto samo crvene ivice. Detalji: v. [[PROGRESS]] Blokeri.

---

## 2026-08-04 [claude-code] [W3] Lighthouse Accessibility plan — sva 4 batch-a izvršena, skor 84–90 → 95–99 ✅

**Izvršen odobreni radni nalog od 07-30** ([[migracija/2026-07-30-lighthouse-a11y-plan]]) — svi batch-evi po planu, plus 3 novootkrivena nalaza usput (real Lighthouse run pokazao ono što statična analiza planom nije mogla). Backup pre svakog DB dela: `antasline_local_2026-08-04_pre-lighthouse-a11y-batch1.sql` (full) + `...batch2.sql` (posts tabela).

**Batch 1 — mehaničke ispravke.** `site_viewport` `not_scalable`→`scalable` preko privremenog mu-plugin-a (trosmerni merge, 883 ključa netaknuta, potvrđeno pre/posle). Court builder: aria-live na `.al-cb__warning`/`.al-cb__save-msg`, `aria-label` na equipment-qty input i palette swatch/erase dugmad, line-palette swatch 22×22→24×24px. Verifikovano: `<meta viewport>` bez `maximum-scale`/`user-scalable=no` sitewide.

**Batch 2 — heading-order + duplikat ID (4 stranice, DB upis preko `$wpdb->update()`).** 5754 (h3→h2 + h4→h3, `style="font-size:Npx"` da vizuelno ostane isto) i 15480 (h4→h3) — oba sad bez preskoka (`1>2>2>3>2>3>2>3>2` i `1>2>3>2>3>3>2`). Duplikat ID: 5769 `vestacka-trava` — plan je pretpostavio 2 pojave, u praksi 2 IZVORA (jedan goli `<span id>`, jedan WPBakery `el_id`) + 1 interni href; href je pokazivao na PRVI DOM-match (prazan span, pogrešna meta), preimenovanjem spana u `-2` href sad ispravno cilja pravu sekciju — usput ispravljen i UX bag, ne samo lint. 15580 `eluid54d67c12`: plan je rekao "2×", stvarno 4× (4 skoro-identična H2 bloka) — zadržan prvi, ostala 3 sufiksirana `-2/-3/-4`, bez href pogodaka nigde sitewide.

**Batch 3 — court builder tastatura, čisto dodavanje (mouse/touch handleri i `paintCell()` netaknuti).** Roving tabindex (`state.focusIdx`, clamp na resize), keydown na `gridWrap` (strelice pomeraju fokus + tabindex swap, Enter/Space poziva POSTOJEĆI `paintCell()`), `:focus-visible` + JS fallback klasa (focus/blur capture-listeneri, jer ti eventi ne buboluju). Testirano kroz pravi Chrome: 3× ArrowRight pomerilo fokus 0→3 sa tabindex swap + klasa, Enter obojio TAČNO fokusiranu ćeliju (fill boja + tally tabela ažurirana identično kliku).

**Batch 4 — kontrast, M odluka (dugme veći tekst, ne boja).** `.al-btn` `clamp(17px,1.4vw,21px)`→`clamp(24px,1.6vw,26px)`. 🔴 **3 nova nalaza koja plan nije predvideo** (otkrivena tek pravim `npx lighthouse` run-om, ne statičkom analizom):
1. `--al-red-dark` (plan: "4,71:1, prolazi") prolazi na BELOJ pozadini ali pada na **4,22:1 na `--al-mist` (#EEF3F8)** pozadini — `.al-label` i `.al-quick-quote__sub a` se oba renderuju na mist sekcijama. Fix: `.al-section--mist .al-label` i `.al-quick-quote__sub a` dobili dodatno zatamnjenu `#C73813` (izračunato, 4,70:1 na mist pozadini), `--al-red-dark` ostaje netaknut svuda drugde (npr. `.al-promo-product__price` je na paper/belo, prošao bez izmene).
2. CF7 submit dugme (`input[type="submit"]`, "Brzi upit" forma #16737) — bela na `--al-red`, 15px bold = 3,62:1, ispod praga. Font-size 15→19px (WCAG veliki-tekst-bold prag je 18,66px) — isti obrazac kao `.al-btn` M odluka, boja netaknuta.
3. **WoodMart CORE tema** (`--color-gray-400 #a5a5a5`, 2,46:1 na belom) za `.wd-product-cats` kategorija-link ispod naziva proizvoda na SVAKOJ product kartici — override u child temi (`.wd-product-cats a { color: var(--al-muted) }`, 5,46:1), ne u vendor fajlu (theme-update bi ga izgubio, isti gotcha kao breadcrumb schema 07-30).
`.al-mobile-tel` — plan je tražio potvrdu pre izmene: **potvrđeno da NE ispada u nijednom od 7 Lighthouse run-ova, nedirano.**

**Rezultat (Lighthouse standardna Accessibility, `--only-categories=accessibility`, 7 test-stranica):** Home/Industrijski/Sportske podloge/Conquest 2542/Planer terena **0,99** (bilo 0,84–0,90 07-09 baseline), Proizvod (konusni-štitnik) **0,99**, Kategorija (zaštita-i-bumperi) **0,95**. `color-contrast` PASS na svih 7. Preostalo (van obima ovog naloga, nov nalaz za buduću sesiju): `heading-order` i dalje crveno na SVE ostale stranice (plan je namerno skoping samo 2 posta, ne sitewide sweep) + `target-size` na product-karticama (`h3.wd-entities-title a` + `.wd-product-cats a` nedovoljan razmak, WoodMart core layout, sitewide, veći zahvat).

**Verifikacija:** 11 URL-ova (5 Lighthouse test + proizvod + kategorija + 4 Batch2 stranice) 200/1×H1/0 PHP grešaka, 0 console grešaka (Chrome), CTA dugme vizuelno provereno 1500px i 390px (harness `al-harness.html?w=390&u=...` — ispravan query format je `w`/`u`, ne `width`/`url`) bez preloma/overflow-a. Detalji: [[migracija/2026-07-30-lighthouse-a11y-plan]] (sad zatvoren).

---
