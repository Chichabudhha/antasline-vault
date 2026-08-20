# 2026-08-20 [claude-code] — Potvrdni regression sweep + backup zamrznutog builda na 2 lokacije

**Workstream:** W3 3.10 (pre-migration checklist §A) — poslednja gate stavka vezana za stari freeze (20.08)
**Status:** ✅ zatvoreno

## Kontekst

Ovo je nastavak sesije u kojoj je prvo pomeren go-live (25.08 → 08.09, v. [[odluke/_pregled-odluka]]
2026-08-20), pa je M eksplicitno tražio da se svejedno uradi planirani potvrdni sweep + backup —
ne čekati novi freeze (03.09), zaključati današnje stanje sada.

## 1. Regression sweep — dva prolaza, prvi lažno "čist"

Prvi prolaz (`regression-sweep.php`, ~21:00) je pokazao **235 stranica, 0 razlika** naspram
baseline-a od 19.08 — izgledalo je kao savršena potvrda. **Bio je lažan nalaz.** Onda (17957),
objavljena danas, nije se pojavila u `product-sitemap.xml` — poznati Rank Math keš gotcha
(zabeleženo već 18.08: sitemap se kešira kao fajlovi u `uploads/rank-math/*.xml`, brisanje opcije/
transienata ne pomaže, samo brisanje fajlova). Sweep čita iz sitemap-a, pa je tiho preskočio
proizvod koji je trebalo da proveri.

**Fix:** obrisano svih 8 `rank_math_*.xml` fajlova iz `uploads/rank-math/`, sitemap se regenerisao
na sledeći zahtev — `product-sitemap.xml` 101 → **102**, Onda uključena.

**Drugi prolaz** (posle fixa): **236 stranica** (235 + Onda), i dalje **0 non-200 / 0 h1_0 /
0 h1_multi / 0 jsonld_bad / 0 no_title**, `no_meta` i dalje 18 (isključivo `product_tag` arhive,
poznato, posle live-a). URL diff naspram 19.08 baseline-a: **+1 (Onda), 0 uklonjenih.**

## 2. Pravi nalaz — mrtav interni link na draft proizvod

Drugi prolaz je prijavio **2 problematична linka** (bilo 1 u prvom, lažno čistom prolazu):
poznati benigni `http://localhost/antasline` bez crte (301, 130 pojava) + **nov, stvaran nalaz**:
`codex-maxionda-28mm-antitrauma-zastita` → **404** (proizvod 17964 je namerno `draft`, čeka M
publish odluku iz [[dnevnik/2026-08-20-codex-srl-uvoz-proizvoda]]), sa jednim ulaznim linkom —
iz `post_content` Onde (17957): `Za veći rizik od udara postoji deblja varijanta <a href="…
codex-maxionda-28mm-antitrauma-zastita/">Codex Maxionda</a> (28 mm).`

**Fix:** uklonjen `<a>` tag, tekst ostao ("Codex Maxionda" bez linka) — bezbedno rešenje dok
Maxionda ne dobije publish odluku (posle toga link se može vratiti). Izmena preko `UNHEX()`
upisa (isti obrazac kao 18.08 ESD rad — `mysql -B --raw` HEX čitanje, PHP `str_replace`, `.sql`
fajl sa `UNHEX()` da se izbegne Windows CRLF enkodiranje). Verifikovano: 200 / 1×H1 / link nestao
sa live stranice. **Nije rađen treći pun sweep** za ovako lokalizovanu izmenu — ciljana provera
je dovoljna (isti princip kao spot-check posle sitnih fix-eva u ranijim sesijama).

**Baseline za §B6 postaje `analiza/2026-08-20-regression-confirmatory-*`** (236 stranica,
snimljeno PRE fix-a mrtvog linka — dokumentuje da je nalaz postojao, ne da je i dalje otvoren).

## 3. Backup zamrznutog builda na 2 lokacije

🔴 **Nalaz pre početka:** noćni zakazani task ("AntasLine Nocni Backup") je danas okinuo **03:00:12**,
DB dump uspeo (**03:00:17**), ali proces je **prekinut usred zip-a** — `Get-ScheduledTaskInfo`
vraća `LastTaskResult 3221225786` (0xC000013A, isti kod kao pad od 14–17.08: proces prekinut,
verovatno mašina ugašena/uspavana pre nego što je 20–40 min zip stigao da završi). Rezultat:
0-bajtni `antasline_backup_2026-08-20_0300.zip` na G:, **nijedan uspešan backup za 20.08 pre
ove sesije.**

G: disk ("Maxtor") nije bio prikačen na početku ove sesije — M ga je prikačio na zahtev.
Ručno pokrenut `nocni-backup.ps1` (21:04) posle toga: DB dump OK (71,23 MB), zip na obe lokacije
u istom prolazu (G: primarno, C: `antasline-backups/auto/` kao druga lokacija — mehanizam iz
popravke 17.08).

## Otvorene akcije

- ⚪ Ništa — ovo zatvara poslednju gate stavku vezanu za stari freeze. Sledeći put kad treba
  ponoviti (bliže novom freeze-u 03.09), sweep treba raditi u ISTOM redosledu: **prvo proveriti
  sveže objavljene stranice ulaze u sitemap** (curl `product-sitemap.xml` i grep za očekivan slug),
  tek onda pokretati pun sweep — inače se nov publish tiho preskoči.
- 🟡 #ceka-miroslav (nepromenjeno iz Codex uvoza): publish odluka za preostalih 7 draft Codex
  proizvoda, uklj. Maxiondu — kad se ta odluka donese, link ka Maxiondi na Onda stranici se
  može vratiti.

## Beleške / odluke

- 🔴 **Nova lekcija:** regression sweep koji čita URL-ove isključivo iz sitemap-a može lažno
  prijaviti "0 razlika" ako je sitemap zastareo (Rank Math keš) — sveže objavljena stranica se
  tiho ne proveri umesto da se prijavi kao nedostajuća. Pre svakog potvrdnog sweep-a posle
  publish/draft promene: prvo osvežiti sitemap (obrisati `uploads/rank-math/*.xml`), tek onda
  sweep. → upisano u [[reference/naucene-lekcije]]
- Noćni backup treba proveravati posle svakog gašenja/uspavljivanja mašine, ne pretpostaviti da
  je zakazan task = uspešan task — `Get-ScheduledTaskInfo` `LastTaskResult` je brz način provere
  (0 = uspeh, `3221225786` = prekinut).

## Veze

- [[dnevnik/2026-08-19-regression-sweep-pre-freeze]] (prethodni baseline, 19.08)
- [[dnevnik/2026-08-20-codex-srl-uvoz-proizvoda]] (Onda/Maxionda poreklo)
- [[odluke/_pregled-odluka]] (go-live pomeranje, isti dan)
- [[migracija/2026-08-10-pre-migration-checklist]] §A
- [[PROGRESS]]
