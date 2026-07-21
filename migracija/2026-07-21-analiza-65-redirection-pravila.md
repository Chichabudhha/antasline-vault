---
tip: analiza
datum: 2026-07-21
naziv: Analiza 65 Redirection-plugin pravila (live) — nastavak M6/3.14
status: zavrseno-ceka-1-odluku
---

# Analiza 65 postojećih Redirection pravila (live)

Izvor: `migracija/redirection-live-export-2026-07-21.tsv` (izvezeno preko
cPanel-live sesije, read-only, `[[2026-07-21-prompt-redirection-export]]`).
Rezultat: `migracija/redirect-mapa-HISTORIJSKI-65-FLAT.csv` (62 pravila,
lančano razrešena, verifikovana).

## TL;DR

- 65 pravila u bazi = **62 jedinstvena izvorna URL-a** (3 imaju duplirane
  redove, videti "Nalazi" ispod).
- **25 od 62 pravila su lanci** (2–4 preusmerenja jedno za drugim) —
  sve razrešeno na **jedan direktan cilj** u flattened CSV-u.
- **Svih 33 jedinstvenih finalnih ciljeva verifikovano HTTP-om**: 32×200 na
  live-u odmah, 1×404 očekivano (nova stranica postoji samo na lokalnom
  buildu, ide live tek na dan migracije).
- **2 pravila su vodila na mrtav (404) cilj na samom live sajtu, VEĆ SADA** —
  ispravljena u flattened fajlu (videti "Nalazi #1/#2").
- **1 stvarni sukob** koji čeka odluku (videti "Nalazi #3").
- Nema akcije na produkciji — sve je read-only analiza + pripremljen fajl za
  ugradnju u migracioni `.htaccess` (3.9/3.11).

## Nalazi

### #1 🔴 — `/gumeni-podovi/` vodio na već-mrtav (404) cilj
Pravilo: `/gumeni-podovi/` → `/gumeni-podovi-javne-objekte-i-teretane/`.
Taj cilj vraća **404 na live-u već sada** (potvrđeno i ranije, 2026-07-12 Tier4
#20 sesija, i ponovo sada). 0 zabeleženih hitova na ovom pravilu — nije aktivno
štetno, ali je trulo. **Fix u flattened fajlu**: preusmereno direktno na novu
stranicu `/industrijski-podovi/podovi-za-teretane-i-fitnes-centre/` (ID 17020,
napravljena upravo za ovu temu, W2 Tier4). Ta stranica postoji SAMO na lokalu
(404 na live danas — očekivano, postaje 200 na dan migracije).

### #2 🔴 — `/naslovna/.../podovi-za-radnje-i-prodavnice/` vodio na mrtav cilj, SA 43 REALNA HITA
Lanac: `/naslovna/industrijski-podovi/podovi-za-radnje-i-prodavnice/` →
`/home/industrijski-podovi/podovi-za-maloprodajne-objekte/` — ovaj **drugi
korak cilja na URL koji ne postoji nigde u 65 pravila niti na live sajtu**
(404, verifikovano). 43 istorijska hita su išla u prazno. **Fix**: direktno
na `/industrijski-podovi/podovi-za-maloprodajne-objekte/` (bez `/home/`
prefiksa) — potvrđeno 200 na live, i to je tačan lokalni parity URL (ID 16683,
parity-inventar #31).

### #3 🟡 — SUKOB na `/padel-tenis/` — čeka odluku
Dva aktivna pravila za isti izvor:
| ID | Cilj | Hitovi | Poslednji pristup |
|---|---|---|---|
| 65 | `/pop-tenis/` | **963** | 2026-07-15 (aktivno, stvarno se koristi) |
| 70 | `/sportske-podloge/padel-tereni/` | 0 | nikad |

`/pop-tenis/` (post 15966) je stari članak čiji je sadržaj *zapravo o padel
tenisu* (naslov preimenovan još 2026-06-23) — realan, redovno posećivan,
aktivno se radi na njemu (title/meta W2 2.3, FAQ #11). `/sportske-podloge/
padel-tereni/` (page 16670) je nova F5 parity-rebuild stranica, takođe live,
119 GSC klikova SOPSTVENOG saobraćaja (ne preko ovog pravila).

Znači: na sajtu trenutno postoje **DVE odvojene, žive stranice o
padelu/padel terenima** (`/pop-tenis/` i `/sportske-podloge/padel-tereni/`),
i staro pravilo `/padel-tenis/` u praksi vodi na `/pop-tenis/` (963 hita),
dok drugo pravilo ka `/sportske-podloge/padel-tereni/` nikad ne okida (mrtvo,
0 hitova, verovatno dodato greškom ili zaboravljeno posle F5 rebuild-a).

**#ceka-miroslav odluka:**
1. Ostaviti kao što realno radi — `/padel-tenis/` → `/pop-tenis/` (flattened
   fajl trenutno tako ima, po pravilu "veći broj hitova pobeđuje"), obrisati
   mrtvo pravilo #70 pri migraciji. Dve stranice o padelu ostaju odvojene
   (rizik: mogu se takmičiti jedna s drugom u rangiranju — nije nova stvar,
   nije uzrokovano ovom analizom, samo je učinjeno vidljivim).
2. Ili: iskoristiti migraciju kao priliku da se konsoliduje — `/pop-tenis/`
   sadržaj/SEO vrednost prenese/spoji u `/sportske-podloge/padel-tereni/`
   (novija, bolje strukturirana F5 stranica), pa SVE stare URL-ove (uklj.
   `/padel-tenis/` i `/pop-tenis/` sam) preusmeriti tamo. Veći posao, ali
   rešava dupli-sadržaj rizik trajno.

Fallback ako se ne odluči do migracije: opcija 1 se primenjuje automatski
(nema promene ponašanja u odnosu na danas).

### #4 🟢 — 2 bezopasna literal-duplikata (informativno, ne treba odluka)
- `/podovi-za-terase/bergo-multisport/ispunjava-fiba-standard/` — 2 identična
  pravila (id 22, 24), isti cilj, 0 hitova oba. Čist plugin-otpad.
- `/podovi-za-garaze-i-servise/` — 2 pravila (id 68, 69) sa istim ciljem u
  suštini (relativan vs apsolutan URL istog puta). Bezopasno.

### #5 🟢 — 2 pravila se oslanjaju na WordPress-ov ugrađeni canonical redirect
`/podovi-za-terase/bergo-easy/podloge-za-sajmove-i-promocije/` i
`/podovi-za-terase/za-vlazne-prostore-bergo-soft/` vode na URL-ove pod starim
roditeljskim slugom (`/podovi-za-baste-splavove-bazene/...`) koji sami VIŠE
NE POSTOJE kao takvi — ali live ipak vraća 200 jer WordPress-ov sopstveni
`redirect_canonical()` prepoznaje da je roditelj preimenovan i sam ubacuje
DODATNI 301 do pravog trenutnog URL-a (`/spoljnje-podne-obloge/...`), van
Redirection plugin-a. **U flattened fajlu su ionako spljošteni direktno** na
finalni cilj (izbegava se nepotreban dodatni hop), ali vredi znati da ovaj
mehanizam postoji — znači da post_parent hijerarhija na lokalnom buildu MORA
da se poklopi sa live-om da bi ovo nastavilo da radi i posle migracije (već
je tako urađeno, F2/F5 parity rad).

## Šta dalje

- `migracija/redirect-mapa-HISTORIJSKI-65-FLAT.csv` je gotov i verifikovan —
  treba ga spojiti sa postojećim `migracija/redirect-mapa-FINAL.csv` (7 redova,
  drugi sloj: trenutni-live→lokal mismatch) u finalni `.htaccess` na dan
  migracije (3.9/3.11). Dva sloja se NE preklapaju (različiti izvori/ciljevi),
  samo se dodaju jedan za drugim.
- Redirection plugin sam PO SEBI ne mora da se migrira/reaktivira — sve je
  pretvoreno u obične `Redirect 301` linije, isti obrazac kao postojećih 7.
- Jedina otvorena tačka: **odluka o `/padel-tenis/` sukobu (#3)** — ne
  blokira ništa do migracije, fallback je automatski.
- M6/3.14 dependency red u [[2026-07-06-MASTER-PLAN-V2]] ("65 postojećih
  Redirection pravila odluka") sada praktično zatvoren — ostaje samo #3 kao
  mala otvorena stavka, ne kao istraživanje.
