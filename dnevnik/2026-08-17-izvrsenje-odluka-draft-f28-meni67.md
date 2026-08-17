---
tip: sesija
datum: 2026-08-17
tag: claude-code
oblast: W1 / W7 / sadržaj
---

# 2026-08-17 [claude-code] Izvršenje 3 sadržajne odluke od 17.08 (draft · F2.8 · meni 67)

Sesija izvršava odluke koje su istog dana upisane u [[odluke/_pregled-odluka]], a čije je
izvršenje M eksplicitno odložio („akciju radimo u sledećoj sesiji"). Rok: content freeze **ČET 20.08**.

Backup pre svega: `antasline-backups/antasline_local_2026-08-17_pre-odluke-17-08.sql` (36,9 MB).

## Šta je urađeno

### 🔴 Nalaz koji je promenio odluku 1: spisak je bio zastareo — 13, ne 14, i samo 6 stvarno bez slike

Odluka od 17.08 je doneta nad spiskom iz [[PROGRESS]] koji je poslednji put ažuriran **30.07**.
Provera stvarnog stanja baze pokazala je dve stvari:

1. **Spisak nabraja 13 ID-eva, ne 14.** Brojka „14" u PROGRESS-u i u
   [[dnevnik/2026-08-17-backup-mysql-crash-pomeranje-roka]] je netačna —
   `16893` + 5× Radici (`16899`–`16902`, `16906`) + `16919` + `16990`/`16991`/`16998` +
   3 mreže (`17001`–`17003`) = **13**.
2. **7 od 13 je u međuvremenu dobilo sliku:**
   - `16919` EXPONA Living Clic — attach **17594** (`16919-main.webp`) + 3 galerijske
     enterijer slike, **06.08**
   - `16893`, `16899`, `16900`, `16901`, `16902`, `16906` — attach **17681–17686**, **07.08**,
     uz **eksplicitno M odobrenje** generičkih dobavljačkih fotografija bez tačnog
     model-mapiranja (v. [[reference/gemini-red-cekanja]] „Van reda — NO_THUMBNAIL",
     gde je taj deo spiska već prekrižen)

Draftovanje svih 13 bi ugasilo 7 proizvoda koji imaju slike — među njima i 6 za koje je M
sam pre deset dana odobrio rešenje. **Izvršeno je zato samo nad 6 koji stvarno nemaju
nijednu sliku**, što je doslovno ono što odluka kaže („proizvodi koji nemaju pravu
fotografiju idu u draft").

### 1. Draft — 6 proizvoda (generička sportska oprema)

`16990` Tribina montažno-demontažna · `16991` Stolica za tribine · `16998` Go za mali fudbal ·
`17001` Mreža za tenis · `17002` Mreža za padel · `17003` Mrežica za koš → **`draft`**.

Svih 6 su generička sportska oprema koja čeka **M12** (brendovi/dobavljači, pregovori u toku) —
dakle nemaju izvor fotografije ni u perspektivi, za razliku od 7 gore.

**Provere pre draftovanja (traži ih sama odluka):**
- 301 pravila: **nijedno** od 73 pravila u `.htaccess` draftu ne gađa te URL-ove ✅
- meni: 0 `_menu_item_object_id` referenci ✅
- interni linkovi: **4 nađena, svi na jednoj stranici** — `16676` `/sportske-podloge/opremazasportsketerene/`

**Sanacija hub stranice 16676** (da draft ne ostavi 4×404): kartice su zadržane —
tribine, oprema za tenis/padel i mrežica su i dalje deo ponude, samo proizvod-stranice
nemaju sliku. Linkovi prevezani na katalog-režim obrazac koji sajt već koristi:
`/kontakt/?form-naslov=Ponuda: …`. Tekstualni link „golovi za mali fudbal" u pasusu
pretvoren u običan tekst. Posle izmene: **0 preostalih linkova** ka draftovanim URL-ovima.

### 2. Trava u boji — Edel Grass B.V.

Bez izvršenja: odluka je **potvrda porekla**, ne izmena sadržaja. Bloker od 08.08 je zatvoren
upisom 17.08. Ništa na buildu ne treba menjati; ograda ostaje da se Edel Grass i lokalni
Condor Schools/Playgrass set (7 vs 6 boja) **ne mešaju**.

### 3. F2.8 — 4 modela veštačke trave → Radici Landscape

Stranica `16673` (`/spoljnje-podne-obloge/vestacka-trava-za-terase/`) — kartice
**Highlands · Nature · Put · Springgrass** prevezane na proizvod
`16906` „Radici veštačka trava za pejzažne površine" (`/proizvod/radici-vestacka-trava-za-pejzaz/`).

🔵 Sitna korekcija zapisa: PROGRESS je tvrdio da „kartice vode na kategoriju" — u markupu
te 4 kartice **uopšte nisu imale link** (`<span class="al-card__title">` bez `<a>`);
link ka kategoriji stoji na drugom mestu u tekstu. Sada nose link po istom obrascu kao
ostale `al-card` kartice na sajtu.

### 4. Stari meni 67 → obrisan

`wp_delete_nav_menu(67)` — „O firmi", **39 stavki**, obrisan zajedno sa `nav_menu_item`
postovima. Pre brisanja potvrđeno da **nije dodeljen nijednoj lokaciji**:
`theme_mods_woodmart-child` → `nav_menu_locations` = `main-menu → 390`.

Preostali meniji: **390** „Glavni meni 2026" (76 stavki, aktivan) · **280** „Utility meni" (4).
Time je W7 F3 stavka o brisanju starih menija zatvorena u celosti (term 28 i 10 praznih
Porto menija obrisani još 30.07).

## Verifikacija

| URL | HTTP | H1 | PHP greške |
|---|---|---|---|
| `/sportske-podloge/opremazasportsketerene/` (16676) | 200 | 1 | 0 |
| `/spoljnje-podne-obloge/vestacka-trava-za-terase/` (16673) | 200 | 1 | 0 · 4 linka ka Landscape |
| `/proizvod/radici-vestacka-trava-za-pejzaz/` (16906) | 200 | 1 | 0 |
| `/industrijski-podovi/` (regresija) | 200 | 1 | 0 |
| `/sportske-podloge/` (regresija) | 200 | 1 | 0 |

6 draftovanih proizvoda → **404** (očekivano, i bez ijednog internog linka ka njima).

## Otvorene akcije

- 🔴 **Ponovni full regression sweep** — obavezan posle **poslednje** sadržajne izmene, pre
  gate-a 21.08. Ova sesija ga namerno **ne pokreće** jer je prozor otvoren do 20.08 i
  očekuju se još izmene (P3 metadesc, 4 reference na `/o-nama/` ako stigne materijal).
  Uz njega ide **nov backup zamrznutog builda**.
- 🟡 Slugovi nisu dirani → `.htaccess` 301 draft (73 pravila) se **ne regeneriše**.
- 🟡 **#ceka-miroslav** — 7 proizvoda koji su ostali `publish` sa generičkim dobavljačkim
  fotografijama (`16893`, `16899`–`16902`, `16906`, `16919`): potvrditi da je to i dalje u
  redu, ili reći da i oni idu u draft. Odluka od 17.08 ih je obuhvatala samo zato što je
  spisak bio zastareo.
- 🟡 `16990`/`16991`/`16998`/`17001`–`17003` ostaju u draftu dok **M12** (dobavljači) ne da
  brend i fotografije — tada se vraćaju na `publish` i linkovi na 16676 se prevezuju nazad.

## Beleške / odluke

- **Zapisan spisak nije zamena za proveru baze.** Isti obrazac kao sistemski nalaz od 13.08
  („zapisane GSC brojke tri puta netačne → pre svake odluke ide svež pull"): ovde je zapis
  od 30.07 preživeo dve sesije koje su ga delimično obesmislile (06. i 07.08), i doveo do
  odluke nad 13 stavki umesto nad 6.
- Draft proizvoda sa internim linkovima traži **sanaciju hub stranice u istom koraku** —
  inače se 404 pojavljuje tek u sledećem regression sweep-u.

## Veze

- [[odluke/_pregled-odluka]] — 4 odluke od 17.08 (izvor)
- [[dnevnik/2026-08-17-backup-mysql-crash-pomeranje-roka]] — sesija koja je odluke upisala
- [[reference/gemini-red-cekanja]] — NO_THUMBNAIL spisak, 6/13 zatvoreno 07.08
- [[PROGRESS]] · [[2026-07-06-MASTER-PLAN-V2]]
