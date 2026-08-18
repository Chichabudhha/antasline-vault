---
tip: sesija
datum: 2026-08-18
tag: claude-code
oblast: W2 / W3
naslov: Cenovne stranice konsolidovane u hubove + vraćeno 301 pravilo koje je odluka od 11.08 isključila
---

# 2026-08-18 — Konsolidacija cenovnih stranica u hubove

## Šta je urađeno

Sesija je počela kao SEO zadatak; pravac je dala **M primedba**: *„Ne želim odvojenu stranicu
industrijski podovi cena jer već imamo stranicu sa industrijskim podovima na kojoj je cena.
Zašto da dodajemo kontent koji se sukobi sa postojećim?"*

Provera pre izvršenja je pokazala da je primedba jača nego što je i ranija analiza slutila:

- **Nijedna od četiri „cena" stranice ne postoji na live-u** — `/industrijski-podovi-cena/`,
  `/gumeni-podovi-za-terase-cena/`, `/podloge-za-parkiraliste-cena/` i zbirni `/cene/` vraćaju
  **404** na produkciji. Napravljene su 10.07 kao W2 Tier1 paket, samo na buildu, i prvi put bi
  otišle uživo 25.08. Dakle: **0 GSC istorije, 0 klikova koje bi izgubile.**
- **Hub `/industrijski-podovi/` već pobeđuje za cenovne upite** — drži „industrijski podovi cena
  po m2" na **poz. 6,6**, a na stranici **nije imao nijednu cenu**, samo link ka cenovnoj stranici.
  Najgora kombinacija: obećanje u naslovu („…standardi i cena po m²"), ispunjenje na drugom URL-u.
- Preporuka od 13.08 („tabela ostaje na cenovnoj stranici, hub dobija izvod + link") je time
  **oborena**: oslanjala se na to da je cenovna stranica „namenski građena za taj upit" —
  namenski građena ≠ zarađuje.

### Izvršeno

| Draftovano | Sadržaj prešao u | Šta je konkretno dodato |
|---|---|---|
| `/industrijski-podovi-cena/` | `/industrijski-podovi/` | cena kao **4. kolona postojeće tabele debljina** (500/5 na upit · 500/7 4.600–5.500 · 500/10 6.800), napomena o rabatu i ESD-u, brojke u FAQ odgovor „Koliko košta pod po m²?", novo pitanje „Da li je u cenu uključena i ugradnja?" (+ JSON-LD, 15 → 16 pitanja) |
| `/podovi-za-garaze/` | `/industrijski-podovi/garaze-i-autoservisi/` | nova sekcija „Koliko košta pod za garažu?" sa tabelom po kvadraturi (jedno vozilo / dva vozila / radionica 50+ m²) + FAQ pitanje o ceni |
| `/gumeni-podovi-za-terase-cena/` | `/spoljnje-podne-obloge/` | nova sekcija „Cena spoljnih podloga po m²" (Bergo 3.300–5.800 · veštačka trava 3.200–4.500 · gumene i PVC na upit) |
| `/podloge-za-parkiraliste-cena/` | `/podloge-za-parkiraliste-i-staze/` | **ništa** — hub je taj sadržaj (cene po modelu, nosivost, „saće ili šljunak") već imao; stranica je bila čist duplikat i već je stajala kao draft |
| `/cene/` (zbirni hub) | — | draftovan |

Uz to: **segment „Cene" obrisan iz glavnog menija** (7 stavki — 17421 „Cene", 17422 „Industrija",
17425 „Spolja" i 4 lista; meni 390: 77 → 70 stavki). Time je nestala i prazna stavka menija
**17424**, koja je jutros bila zavedena kao „lažna uzbuna" — prazan `post_title` jeste bio
bezopasan, ali je stavka ionako vodila na stranicu koja se gasi.

Prevezani interni linkovi na 4 objavljene stranice koje su vodile na sada draftovane:
`/spoljnje-podne-obloge/`, `/industrijski-podovi/garaze-i-autoservisi/`, `/pvc-podne-ploce/`
i conquest članak `/epoksidni-podovi-ili-ecotile-podovi/`.

Sve cene su iz `[[reference/cenovnik]]` (M10), nijedna nije izmišljena; ESD linija ostaje
**„na upit"** kako cenovnik i kaže.

## 🔴 Glavni nalaz — odluka od 11.08 je pala sa svojim razlogom

Istorijsko pravilo `/podovi-za-garaze/` → (blog) `/koji-pod-postaviti-u-garazu/` sa **182 GSC
pogotka** bilo je 11.08 **namerno isključeno** iz `.htaccess` drafta, uz obrazloženje: *„izvorni
URL na novom buildu postoji kao prava stranica (16875), redirect bi je ubio."*

Draftovanjem te stranice **razlog je prestao da postoji**, a URL je ostao prazan — bez vraćanja
pravila bio bi **404 posle migracije, sa 182 pogotka**. Pravilo je vraćeno u
`redirect-mapa-HISTORIJSKI-65-FLAT.csv`, cilj **namerno promenjen** sa blog posta na hub garaža
(upit „podovi za garaze" je komercijalan, a hub od danas nosi i cene), pa je draft regenerisan
skriptom `htaccess-301-generate.php`: **79 → 80 pravila, svi ciljevi 200**, diff pokazuje tačno
jednu novu liniju.

**Pouka šire od ovog slučaja:** svaka stavka u redirect mapama koja nosi obrazloženje oblika
„ne prenosi se **jer** je URL zauzet" je **uslovna** — promena statusa te stranice je tiho
poništava. Takvih izuzetaka u istorijskoj mapi ima još jedan (2 su ukupno preskočena).

## Otvorene akcije

- 🔴 **Regression sweep i backup builda ostaju obavezni posle 20.08** — ova sesija je dirala
  sadržaj (5 stranica izmenjeno, 5 draftovano, meni skraćen), pa baseline od 13.08 više ne važi.
- 🟡 **Ads:** dva stara `#ceka-miroslav` zadatka iz `[[seo/plan-novih-stranica]]` tražila su da se
  cena-termini i garaža-termini preusmere na cenovne landing stranice — **te landing stranice više
  ne postoje**, pa novi ciljevi treba da budu hubovi (`/industrijski-podovi/`,
  `/industrijski-podovi/garaze-i-autoservisi/`). Ne blokira migraciju (pauzirane kampanje).
- ⚪ Ako se ijedna od 5 draftovanih stranica ikad vrati u `publish`, mora se **ponovo isključiti**
  pravilo za `/podovi-za-garaze/` iz `.htaccess` drafta.

## Beleške / odluke

- **Backup pre izmena:** `antasline_local_2026-08-18_pre-konsolidacija-cena.sql` (36,95 MB).
- 🔴 **Gotcha — `wp-load.php` iz PHP CLI-ja visi.** Prvi pokušaj izmene išao je kroz standardni
  PHP skript sa `require wp-load.php`; proces je stajao **preko 5 minuta uz 4 s CPU vremena**
  (čeka na nešto spolja, ne računa). Ubijen je i zamenjen putem bez WP bootstrap-a: sadržaj se
  čita `mysql --raw`, transformiše u Pythonu, upisuje kao `UPDATE … SET post_content=UNHEX('…')`.
  **Hex upis rešava sve probleme sa escape-om** (navodnici, ćirilica, `–`, `²`), a posle svakog
  upisa skripta pročita sadržaj nazad i uporedi sa očekivanim — ne veruje se `mysql` izlazu.
  Round-trip provereno na kontrolnoj brojci `CHAR_LENGTH` pre svake izmene.
- **Ritam sekcija je čuvan** (pravila iz FAZE 2, 13.08): na garažama je FAQ red prebačen sa
  `mist` na `paper` da nova cenovna sekcija (`mist al-diag-top`) ne bi napravila dva ista
  pozadinska tona jedan do drugog; na terasama je cenovni red ubačen između `paper` i završnog
  `navy al-diag-top--rev`.
- Tabele su upisane kao `al-table` unutar `overflow-x:auto` omotača — hub `/industrijski-podovi/`
  je do sada imao **goli `<table>`** bez klase dizajn sistema (jedini takav od 42 tabele), pa je
  usput usklađen; sa 4. kolonom mu je horizontalni skrol na mobilnom i potreban.
- Epoksid-poređenje sa cenovne stranice **namerno nije prenošeno** u hub — kanonski je conquest
  članak `/epoksidni-podovi-ili-ecotile-podovi/` (post 2542), hub na njega već linkuje.

## Verifikacija

- **8 URL-ova: 200 · tačno 1×H1 · 0 nevalidnih JSON-LD blokova · 0 linkova ka draftovanim stranicama**
  (3 izmenjena huba, 2 stranice sa prevezanim linkovima, 3 regresione: `/kontakt/`,
  `/antistatik-i-elektroprovodljivi-podovi/`, `/podloge-za-parkiraliste-i-staze/`).
- FAQPage schema na `/industrijski-podovi/` validan sa **16 pitanja** (bilo 15).
- Svih 5 draftovanih URL-ova → **404** lokalno; `page-sitemap.xml` → **61 URL**, nijedan od njih.
- `.htaccess` draft: **80 pravila, svi ciljevi 200**, diff = tačno jedna nova linija.

## Veze

- [[odluke/_pregled-odluka]] — odluka 18.08 i zašto obara preporuku od 13.08
- [[seo/2026-08-13-kanibalizacija-konsolidacija-plan]] §4 — ranija (sada oborena) preporuka
- [[seo/plan-novih-stranica]] — stavke #1, #2, #3, #6 anotirane
- [[migracija/redirect-mapa-HISTORIJSKI-65-FLAT]] — vraćen red `/podovi-za-garaze/`
- [[reference/cenovnik]] — izvor svih brojki
