---
tip: sesija
datum: 2026-08-11
oblast: W3 / 3.9 — .htaccess 301
tag: claude-code
status: zatvoreno
---

# W3 3.9 — reverifikacija 301 mape pred migraciju

Stavka iz [[migracija/2026-08-10-pre-migration-checklist]] sekcije A (do 21.08).
Poslednja verifikacija je bila **2026-07-21**, a `htaccess-301-DRAFT.txt` generisan
**2026-07-27** — od tada su obe mape menjane i build je značajno promenjen.

## Šta je urađeno

Napisana dva alata (oba read-only prema bazi i prema živom `.htaccess`-u):

- `migracija/alati/redirect-verify.php` — učita obe mape, normalizuje putanje
  (skida domen, dekodira `%XX`), pa traži: duplikate izvora · petlje i lance ·
  prefiks-kolizije · HTTP status svakog **cilja** · HTTP status svakog **izvora**
  (izvor koji vraća 200 znači da bi pravilo ubilo živu stranicu).
- `migracija/alati/htaccess-301-generate.php` — generiše `htaccess-301-DRAFT.txt`
  iz obe mape, uz **abort ako ijedan cilj nije 200**. Draft se od sada ne piše
  ručno; mape su izvor istine.

Draft regenerisan: **73 pravila** (14 iz `redirect-mapa-FINAL.csv`, 59 iz
`redirect-mapa-HISTORIJSKI-65-FLAT.csv`). Prethodna verzija je imala **8**.
Backup stare: `migracija/htaccess-301-DRAFT.txt.bak-2026-08-11`.

## Nalazi

### 🔴 1. Istorijskih 62 pravila uopšte nije bilo u draftu — a nestaju sa migracijom
Ta pravila danas žive u **Redirection pluginu na live-u**, dakle u bazi. Migracija
zamenjuje živu bazu lokalnom → plugin i njegova pravila nestaju. Draft od 27.07 ih
nije sadržao, pa bi na dan migracije **62 URL-a sa ~46.000 zabeleženih GSC pogodaka**
(uklj. `/sportski-podovi/` 7.800 i `/izgrdanja-sportskig-terena/` 6.043) tiho pala na
404. Analiza tih pravila je urađena još 21.07
([[migracija/2026-07-21-analiza-65-redirection-pravila]]), ali rezultat nikad nije
prenet u `.htaccess` draft.

### 🔴 2. Petlja između dve mape
`redirect-mapa-FINAL` red 2: `/na-kojoj-podlozi-se-igraju-turniri-u-3x3/` →
`/bergo-ultimate-i-ultimate-plus-nova-generacija-sportskih-podova/`
`redirect-mapa-HISTORIJSKI` red 47: **tačno obrnuto**.

Zajedno u `.htaccess` = beskonačna petlja na oba URL-a (Apache prekida sa greškom).
Razrešeno merenjem na buildu: članak živi na `/bergo-ultimate…/` (post 4813, HTTP 200),
a `/na-kojoj-podlozi…/` je 404 → **FINAL smer je tačan**, istorijsko pravilo je
zastareo ostatak i ne prenosi se.

### 🔴 3. Dva istorijska pravila bi ubila stranice koje smo izgradili
Provera **izvornih** URL-ova (ne samo ciljeva) na lokalu je otkrila:

| Izvorni URL | GSC | Šta je na buildu | Odluka |
|---|---|---|---|
| `/lvt-…/vinil-podovi-za-restorane-hotele-kafice-kancelarije-i-poslovne-prostore/` | 588 | **16686**, prava stranica (W2 2.5), 200 | pravilo se NE prenosi |
| `/podovi-za-garaze/` | 182 | **16875**, W2 Tier1 stranica, 200 | pravilo se NE prenosi |

Oba su pravila iz Redirection plugina napravljena pre nego što su te stranice
postojale. Da su prepisana mehanički, redirect bi pregazio dve stranice koje smo
namerno gradili.

### 🔴 4. `Redirect` je prefiks-match — 15 kolizija
Stari draft je koristio `Redirect 301`, koji u mod_alias radi **prefiksno** i lepi
ostatak putanje na cilj. Sa punom listom to daje 15 kolizija, npr.:

- `Redirect /podovi-za-terase/ /spoljnje-podne-obloge/` guta 4 specifičnija pravila
  (`/podovi-za-terase/bergo-multisport/` i dalje) i šalje ih na pogrešan URL
- `/home/industrijski-podovi/` guta 8 pravila iz iste grupe
- `/moj-nalog/narudzbine/` bi otišao na `/kontakt/narudzbine/` (404)

Novi draft koristi sidreni `RedirectMatch 301 "^/putanja/?$"` — kolizije nestaju i
**redosled linija postaje nebitan** (jedan izvor grešaka manje na dan migracije).

### 🟡 5. Pogrešan cilj u FINAL mapi (`spoljne-` bez „j")
Red 14 je ciljao `/spoljne-podne-obloge/bergo-easy/` → 301, ne 200. Ostatak M odluke
od 30.07 („neka budu spoljnje"). Ispravljeno u mapi (16665 je dete 16590
`spoljnje-podne-obloge`); cilj sad 200.

### 🟢 6. Ćirilične putanje rade — stara ograda skinuta
Draft od 27.07 je nosio upozorenje da `бренд/ecotile` treba testirati na staging-u
i da je fallback `RewriteRule` sa `\x` escape-om. Testirano direktno pod Apache-om:
`RedirectMatch` sa doslovnim UTF-8 ćiriličnim putem radi
(`/%D0%B1%D1%80%D0%B5%D0%BD%D0%B4/ecotile/` → 301 → `/brend/ecotile/`).

### ⚪ 7. Jedan „nalaz" koji je namerna odluka
`/sta-postaviti-preko-starog-parketa-ili-plocica/` (16613) vraća 200 na lokalu iako
ga pravilo preusmerava. To je M odluka od 30.07: stranica ostaje `noindex` kao
rezerva ako 301 zakaže, a redirect se aktivira tek na produkciji. Nije bug.

## Verifikacija

- `redirect-verify.php` posle popravki: **0 duplikata · 0 petlji/lanaca ·
  0 ciljeva ≠ 200** (44 jedinstvena cilja) · 1 izvor sa 200 = namerni slučaj iz #7.
- **Funkcionalni test pod Apache-om**: generisani draft prepisan u izolovan
  `htdocs/redirtest/` sa prefiksiranim putanjama; 8 reprezentativnih slučajeva
  (prefiks-zamka na 3 nivoa dubine, ćirilica, bez kose crte, `/home/` grupa) —
  **8/8 tačan 301 + tačan `Location`**; negativna kontrola (`/podovi-za-terase/xyz/`,
  `/moj-nalog/narudzbine/`, `/home/industrijski-podovi/xyz/`) — **0 lažnih 301**.
  Test folder obrisan posle merenja.
- Generator sam odbija da upiše fajl ako ijedan cilj nije 200 (testirano — prvi
  prolaz je pukao na dva cilja iz nalaza #2 i #5).

## Dodatak — `.htaccess` vs Rank Math Redirections (M pitanje iste sesije)

Provereno direktno u kodu i bazi, ne iz sećanja:

| Činjenica | Nalaz |
|---|---|
| Rank Math Redirections modul | postoji u **besplatnoj** verziji, trenutno **ISKLJUČEN** (`rank_math_modules` nema ni `redirections` ni `404-monitor`) |
| Kada se izvršava | `add_action('wp', 'do_redirection', 11)` → **pun WP boot + parsiranje upita** pre nego što se izda 301 |
| Izvoz u `.htaccess` | **da** — `class-export.php` emituje `RewriteRule … [R=301,L]` (Apache) ili `.conf` (nginx) |
| Tipovi poklapanja | exact / contains / starts / ends / regex + query-string uslovi + 410/451 |
| Naša 73 pravila | **0 koristi query string** → bogatije poklapanje nam ne treba |
| Redirection plugin na lokalu | **ne postoji** (samo na live-u) — nezavisna potvrda nalaza #1 gore |

**Prvo: nije „ili-ili"** — Rank Math ima Apache izvoz, pa se pravila mogu praviti u UI-ju i *isporučiti* u `.htaccess`.

### `.htaccess` — za
1. **Ne boot-uje WordPress** (mod_alias, faza prevođenja URL-a) — bez PHP-a, baze i teme.
2. 🔴 **Radi i kad WP ne radi** — beli ekran / PHP greška / plugin sukob / baza pala → i dalje 301, ne 500. Najviša težina baš na dan migracije, kad je WP najnestabilniji.
3. **Ne zavisi od keša** — LiteSpeed ne kešira ne-200 odgovore, pa je svaki hit kroz plugin nekeširan pun PHP zahtev.
4. **Verzionisano u git-u** — diff, trag izmene, code review. DB pravila se ne vide ni u jednom diff-u.
5. **Testivo pre aktivacije** (generator + izolovan Apache test — danas dokazano).
6. **Rollback = brisanje bloka, ~30 s, ne traži da WP radi** (rollback budžet je 35–50 min).

### `.htaccess` — protiv
1. 🔴 **Sintaksna greška = HTTP 500 na CEO sajt.** Rizik snižen generatorom (ručno kucanje izbačeno) + izolovanim testom.
2. **Miroslav ne može sam da doda pravilo** — treba cPanel/FTP i tačna sintaksa. Visoka težina **posle** live-a.
3. Nema statistike pogodaka po pravilu.
4. Živi izvan baze → DB rollback ne vraća `.htaccess` i obrnuto.
5. Panel/plugin koji „resetuje `.htaccess`" može obrisati blok (nizak rizik — naš blok je van `# BEGIN WordPress` markera).

### Rank Math — za
1. 🔴 **Pravila putuju SA bazom**, a lokalna baza je ta koja ide na live — tačno obrnuto od kvara iz nalaza #1.
2. **Miroslav rešava nov 404 kroz UI**, bez terminala. Visoka težina posle live-a.
3. **404 Monitor** — pravilo se pravi direktno iz zabeleženog 404-a; zamenjuje „dnevni pregled 404 loga prvih 14 dana" ručnim radom sa serverskim logom.
4. Greška u pravilu obara **to pravilo**, ne sajt.

### Rank Math — protiv
1. 🔴 **Pun WP boot po redirektu** — najskuplji način da se izda 301, na ~46.000 pogodaka.
2. 🔴 **Ako WP padne, redirekti padaju s njim** — tačno scenario zbog kog rollback plan i postoji.
3. 🔴 **Uključivanje modula 5 dana pred freeze** = nov modul + nove DB tabele + nov kod na svakom zahtevu, netestirano. Visoka težina **sada**, nikakva posle live-a.
4. Pravila nisu u git-u — nema diff-a ni traga ko je i kad dodao.
5. ⚠️ Opcija `disable_auto_redirect` gasi WP-ov `wp_old_slug_redirect` (core auto-redirect pri promeni sluga) — uslovno, ne automatski.

### Zaključak — podela po *populaciji* redirekta, ne po alatu

| | Migracioni skup (73) | Post-live ad-hoc |
|---|---|---|
| Priroda | jednokratan, poznat, zamrznut | nepredvidiv, kaplje mesecima |
| Saobraćaj | ~46.000 pogodaka | pojedinačni |
| Ko ga pravi | CC, iz mapa, testirano | Miroslav, iz 404 monitora |
| Mora raditi kad WP padne | **da** | ne |
| → alat | **`.htaccess`** | **Rank Math** |

🔴 **Tvrdo pravilo protiv dvostrukog sloja:** `.htaccess` = zamrznut migracioni skup, Rank Math = sve nastalo posle 24.08. Isti URL nikad na oba mesta — `.htaccess` se izvršava prvi i tiho pobeđuje, pa bi pravilo u UI-ju izgledalo „ne radi" bez ijedne poruke.

Predloženo i za kasnije: kvartalno Rank Math → Redirections → Export (Apache) i preseliti u `.htaccess` ono što je postalo saobraćajno.

## Otvorene akcije

- Draft se **i dalje ne aktivira** — tek na dan migracije, iznad `# BEGIN WordPress`
  bloka. `RewriteBase` nije potreban jer se koristi mod_alias, ne mod_rewrite.
- Ako se između sada i 24.08 promeni ijedan slug, **ponovo pustiti oba skripta**
  (generator sam pukne ako cilj nije 200).
- Na dan migracije, posle aktivacije: pustiti `regression-sweep.php` protiv
  produkcije + spot-check 5 pravila sa najviše GSC pogodaka.
- **#ceka-miroslav:** odobrenje da se u [[migracija/2026-08-10-pre-migration-checklist]]
  §B7 i master plan 3.12 upiše post-live tačka „uključiti `redirections` +
  `404-monitor` module" + tvrdo pravilo protiv dvostrukog sloja. Analiza je gotova,
  **upis nije izvršen** bez odluke. Do 24.08 se ništa ne menja u svakom slučaju.

## Veze
[[migracija/2026-08-10-pre-migration-checklist]] ·
[[migracija/2026-07-21-analiza-65-redirection-pravila]] ·
[[migracija/redirect-mapa-FINAL]] · [[2026-07-06-MASTER-PLAN-V2]] ·
[[reference/naucene-lekcije]]
