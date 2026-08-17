---
tip: dnevnik
alat: claude-code
datum: 2026-08-17
blok: C
status: zavrseno
---

# Sesija — OAuth *Publish app* + 15793 legacy swatch

> Druga sesija istog dana (prva: [[dnevnik/2026-08-17-backup-mysql-crash-pomeranje-roka]]).
> Kontekst: N7', produžen sadržajni prozor 17–20.08, gate 21.08, migracija 25.08.

## Šta je urađeno

### 1. OAuth consent screen `mcp-za-claude`: Testing → In production

Zatvara 🔴 bloker od 2026-08-12 („OAuth pada svakih 5–7 dana"). U statusu *Testing*
Google gasi refresh token na 7 dana; `token.json` je poslednji put osvežen **13.08 15:15**,
dakle pad je bio zakazan za **~20.08** — pre gate-a (21.08) i pre migracije (25.08), tačno
preko tačaka 3 i 10 pre-flight checklist-a (OAuth provera, Ads Final URL audit).

**Podela posla:** CC otvorio konzolu i dijagnostikovao, **klikove radio M**
(`APIs & Services → OAuth consent screen → Audience → Publish app`).

🔴 **Gotcha koji je koštao prvi pokušaj.** Konzola je vratila **„You need additional access"**
za projekat `561984657473` sa tri Missing permission-a:

```
oauthconfig.testusers.get     (Missing)
oauthconfig.verification.get  (Missing)
resourcemanager.projects.get  (Missing)
```

…i ponudila dugme **„Request access"** plus role `OAuth Config Viewer` / `OAuth Config Editor`.
**To je bio lažan trag.** Nije nedostajala IAM rola nego je Chrome bio ulogovan na **drugi
Google nalog** — projekat vidi samo nalog koji ga poseduje. Indikacije koje su to odale:
gore je pisalo **„Select a project"** umesto imena projekta, i stajao je banner
**„Start your Free Trial with $300"** (nalog koji nikad nije koristio GCP).
**Pravilo: na ovom ekranu prvo proveri nalog, pa tek onda IAM.**

**Verification Center namerno NIJE pokretan.** Puna Google verifikacija (demo video,
privacy policy URL, nedelje čekanja) postoji za javne app sa >100 korisnika; naša ima jednog.
Publish je dovoljan i sam po sebi rešava istek tokena.

**Verifikacija (isti čas):**

```bash
cd /c/Projekti/antasline-vault/.claude/skills/antasline-konektor/scripts
/c/Users/Miroslav/antasline-connector/venv/Scripts/python.exe \
  ads_report.py --from 2026-08-14 --to 2026-08-16
```

Vratio pun JSON — 35 kampanja, ukupno **1.467,48 RSD / 20 klikova / 119 prikaza**.
`token.json` mtime skočio sa `13.08 15:15` na `17.08 19:49` → **postojeći refresh token je
preživeo prelazak**, ponovna autorizacija (`authorize_oauth.py`) nije bila potrebna.

> ⚠️ Sitnica u API-ju: `ads_report.py` traži `--from` / `--to`, ne `--date_from` / `--date_to`.

### 2. 15793 — legacy `productColors-block` swatch

Stranica `/zastitne-podloge-za-travu-i-plocnike/` (15793, „Bergo Solid") bila je **jedina u
buildu** sa Porto/Kallyas markupom: `<div id="colorBlock" class="productColors-block">` →
`.color-list` → `.color-square`. Swatch „Silk Black" renderovao je **prazan prostor**.

**Provera pre izmene** (potvrda da su klase stvarno mrtve, ne samo pretpostavka):

```bash
cd /c/xampp/htdocs/antasline/wp-content/themes
grep -rn "color-square\|productColors\|color-list" --include=*.css --include=*.php .
# → nula pogodaka u obe teme (woodmart, woodmart-child)
```

`.color-square` je dakle div bez sopstvenih dimenzija → visina 0.

**Rešenje:** samostalan inline-stilizovan swatch (56×56 px, `border-radius:8px`, `border`
`rgba(14,41,80,.18)` da crna bude vidljiva na svetloj `al-section--paper` sekciji),
`aria-hidden="true"` na kvadratu jer boju nosi tekst pored njega.

**Namerno bez nove klase u `antas-design.css`** — komponenta bi se koristila na tačno jednoj
stranici, a plan za prozor 17–20.08 izričito traži „izmene što manje i što lokalnije, bez
diranja slugova"; stranica ionako već koristi inline stilove u istom gridu.
Provereno i da u dizajn sistemu ne postoji gotov swatch obrazac — `al-grid--5` („palete boja",
W7 F1.5) koristi **foto-kartice** (`al-card` + `al-card__media`), ne ravne kvadrate boje,
i živi samo na 16673.

**Dva nusnalaza popravljena usput:**

1. `<h2>Bergo Solid u primeni</h2>` bio je **goli h2 bez eyebrow-a i bez `al-display--lg`** —
   isti obrazac koji je sitewide popravljen 13.08 (17 h2 u FAZI 2), ova stranica je promašena.
2. Posle te popravke stranica je imala **dva identična „Galerija" eyebrow-a** (prva galerija =
   fotografije proizvoda, druga = primena) → drugi prepravljen u **„U primeni"**.

**Način upisa:** `$wpdb->update` + `clean_post_cache`, **ne** `wp_update_post` — potonji
primenjuje kses filtere, a u CLI kontekstu nema ulogovanog korisnika pa bi inline stilovi
mogli tiho otpasti. Skripta radi doslovan `str_replace` i **prekida se sa porukom ako blok
nije nađen doslovno** (nema tihog no-op-a).

## Gotcha-i

🔴 **Verifikacija na zaostalom fajlu = lažno zeleno.** Prva provera:

```bash
curl -s -o /tmp/p.html -w "HTTP: %{http_code}\n" "$U"
echo "H1: $(grep -o '<h1' /tmp/p.html | wc -l)  |  H2: $(grep -o '<h2' /tmp/p.html | wc -l)"
```

→ ispisalo **`HTTP: 000`** ali i **`H1: 1 | H2: 4 | JSON-LD: 2`**. Apache nije radio
(posledica jutrošnjeg MySQL crash-a — v. prvu sesiju dana), `curl` nije upisao ništa, a
`grep` je čitao **`/tmp/p.html` zaostao od ranije sesije**. Brojke izgledaju kao uredna
verifikacija a odnose se na potpuno drugu stranicu. Prava vrednost za 15793 je H2 **9**, ne 4.

Pravila (upisana u [[reference/naucene-lekcije]]): `rm -f` izlazni fajl pre `curl`-a ·
čitaj `%{http_code}` i stani ako nije 200/3xx · piši u scratchpad, ne u `/tmp` (deli se
između sesija) · posle MySQL crash-a proveri `Get-Process httpd` pre HTTP verifikacije.

🟡 **Mrtve CSS klase ne prijavljuju grešku.** Nema PHP greške, nema 404, regression sweep
prolazi čisto — prazan swatch se vidi samo okom. Zato grep po temama pre nasleđivanja
bilo kakvog zatečenog markupa.

🟡 **`16673` daje 301, ali to nije regresija.** `/vestacka-trava-za-terase/` → 301 →
`/spoljnje-podne-obloge/vestacka-trava-za-terase/`, krajnji 200. To je `post_parent`
konvencija (ugnježden URL child stranice), moja URL pretpostavka je bila pogrešna.

## Verifikacija

| Stranica | HTTP | H1 | JSON-LD | PHP greške | legacy klase |
|---|---|---|---|---|---|
| 15793 (izmenjena) | 200 | 1 | 1 validan | 0 | 0 |
| 5119 (regresija) | 200 | 1 | 1 | 0 | 0 |
| 16673 (regresija) | 301 → 200 | 1 | 1 | 0 | 0 |

JSON-LD `@graph` na 15793: `Place` · `LocalBusiness+Organization` · `WebSite` · `ImageObject` ·
`BreadcrumbList` · `WebPage` · `Person` · `Article` — bez dupliranja.
wpautop nije razbio markup (ubacio samo prelom reda između `<h3>` i `<div>`, bez `<p>` omotača).
Eyebrow-i posle izmene: Zaštitne podloge · Šta je · Prednosti · Galerija · Specifikacija ·
Alternativa · Kako biramo · **U primeni** · Kontakt.

**Backup:** `C:\xampp\htdocs\antasline-backups\antasline_local_2026-08-17_pre-15793-swatch.sql`
(37,7 MB, uzet pre izmene).

**Skripte (scratchpad, jednokratne):** `fix-15793.php` (swatch + h2), `fix2-15793.php` (eyebrow).

## Otvorene akcije

- [ ] Proveriti status kampanje **„Podloge za terase i bazene"** u Ads UI — ima potrošnju
      14–16.08 (63,33 RSD / 4 klika / 28 prikaza / CPC 15,83) iako se od 11.08 vodi kao
      **PAUZIRANA**. Ili je reaktivirana bez upisa, ili pauza nije potpuna. #ceka-miroslav
- [ ] Definicija „starog formata" za 15793 — ostatak nalaza nije diran: specifikacija kao
      običan `<ul>` umesto `al-table` · dve galerije na istoj stranici (2022 JPG + 2026 WebP) ·
      inline `<img style="width:100%">` · nula `al-card` (Bergo Solid i Mosolut Heavy opisani
      inline umesto karticama ka `/proizvod/`). #ceka-miroslav
- [ ] Izvršenje 4 odluke od 17.08 (14 proizvoda → draft · meni 67 · F2.8 preveza) — rok
      **ČET 20.08**, M rekao „akciju radimo u sledećoj sesiji" #claude-code
- [ ] Posle poslednje sadržajne izmene u prozoru: **ponovni full regression sweep** + nov
      backup builda pre gate-a 21.08 #claude-code

## Beleške / odluke

- Publish app **ne zahteva** Google verifikaciju — to su dve različite stvari i verifikacija
  se ne pokreće „za svaki slučaj" (nedelje čekanja bez ikakve koristi za nas).
- Swatch rešen inline umesto kroz dizajn sistem — svesna odluka zbog blizine freeze-a;
  ako se ikad pojavi druga stranica sa paletom boja, tada se izdvaja u `.al-swatch` komponentu.
- Apache je zatečen ugašen posle jutrošnjeg MySQL crash-a; pokrenut headless
  (`Start-Process C:\xampp\apache\bin\httpd.exe -WindowStyle Hidden`).

## Veze

- Prva sesija istog dana: [[dnevnik/2026-08-17-backup-mysql-crash-pomeranje-roka]]
- Odluke: [[odluke/_pregled-odluka]] (4 odluke od 17.08, izvršenje sledeća sesija)
- Konektor: [[reference/api-konektor-setup.md]] Korak F (prepisan)
- Checklist: [[migracija/2026-08-10-pre-migration-checklist]] §B1 (🔴 → 🟡)
- Lekcije: [[reference/naucene-lekcije]] (2 nove)
- Plan: [[2026-07-06-MASTER-PLAN-V2]]
