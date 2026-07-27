---
tip: radni-nalog
datum: 2026-07-27
blok: cpanel-live
status: čeka-izvršenje
izvor: "[[seo/2026-07-27-content-klasteri]] + GSC/GA4 dijagnostika 2026-07-27"
---

# 🔧 Radni nalog za `[cpanel-live]` sesiju

> **Za Claude-a na cPanel-u:** ovo je tvoj zadatak-spisak za sledeću produkcijsku
> sesiju. Radi zadatke redom (P1 → P5), svaki ima svoj kriterijum završetka.
> Pre svega pročitaj [[CLAUDE-CODE-instrukcija-CPANEL]] i [[PROGRESS]].
>
> ⚠️ **Ovo je ŽIVA produkcija.** `wp db export` pre svake izmene baze.
> Posle svake izmene: `wp litespeed-purge all`.
> Ne diraj ništa što nije na ovom spisku bez pitanja.

## Kontekst u dve rečenice

Nedeljni izveštaj 2026-07-27 je pokrenuo dubinsku GSC analizu (1.624 upita) koja
je otkrila da **produkcija gubi klikove na stvari koje su odavno popravljene
lokalno, ali nikad nisu otišle u produkciju** — a migracija je tek 2026-08-31.
Ovi zadaci vade tu vrednost 5 nedelja ranije, uz minimalan rizik (title/meta i
301 — ništa strukturno).

---

## P1 — 🔴 Audit draft-ali-indeksiranih stranica (root cause)

**Zašto:** Miroslav je 2026-07-27 objavio dve stranice koje su bile DRAFT na
produkciji dok su i dalje imale saobraćaj sa Google-a:

| URL | GSC 28d | bilo |
|---|---|---|
| `/sportske-podloge/sportski-podovi-za-teniske-terene/` | 262 impr / **12 kl** | draft → 404 |
| `/gumeni-podovi-javne-objekte-i-teretane/` | 176 impr / **12 kl** | draft → 404 |

**Zašto je ovo sistemski, a ne slučajnost:** live export od 2026-07-05
(`migracija/live-export-2026-07-05/`) sadrži **isključivo `publish` status**
(50 pages + 30 posts, 0 draftova — provereno). `parity-inventar.csv` je izveden
iz tog exporta → **svaka draft-ali-indeksirana stranica je nevidljiva za ceo
migracioni plan**: nema lokalni pandan, nema redirect pravilo, nema parity red.

### Uradi

1. Popiši SVE ne-published stranice/postove koji nisu smeće:
   ```
   wp post list --post_type=page,post --post_status=draft,pending,private \
      --fields=ID,post_title,post_name,post_status,post_modified --format=csv
   ```
2. Za dve gore navedene stranice pokušaj da utvrdiš **kada** su prebačene u draft:
   ```
   wp post list --post_type=revision --post_parent=<ID> \
      --fields=ID,post_date,post_modified --format=csv
   ```
   (napomena: `post_modified` na samom postu je od 2026-07-27 kad ih je Miroslav
   objavio — pregažen; revizije su jedini preostali trag. Ako ni revizija nema,
   napiši "nije utvrdivo" — **ne nagađaj**.)
3. Proveri da li postoji activity/audit log plugin (`wp plugin list | grep -i
   "activity\|audit\|simple-history\|stream"`) — ako da, tamo je tačan trag.
4. Za svaki nađeni draft: proveri ima li GSC saobraćaj (javi listu Miroslavu, ne
   objavljuj sam — objavljivanje je njegova odluka).

**Gotovo kada:** lista draftova + odgovor na "kada/kako su ove dve postale draft"
(ili eksplicitno "nije utvrdivo") upisani u dnevnik.

**Posle sesije (za lokalnu stranu, ne radi ovde):** obe stranice sada moraju u
`parity-inventar.csv` i moraju dobiti lokalni pandan — inače se 2026-08-31
ponovo gube. Označi ovo kao `#claude-code` u dnevniku.

---

## P2 — 🔴 Telefonski linkovi zamenjeni na `/kontakt/`

**Nalaz (potvrđen iz live HTML-a 2026-07-27):**

```html
<a href="tel:+381692340072">+381 69 234 00 74</a>   <!-- href 072, tekst 074 -->
<a href="tel:+381692340074">+381 69 234 00 72</a>   <!-- href 074, tekst 072 -->
```

Ko klikne broj koji vidi — zove drugi broj. Bitno jer je **072 dominantan kanal**
(~50 vs ~7 klikova, [[CLAUDE]] §9) i ~46/50 klikova dolazi sa mobilnog.

### Uradi
1. `wp db export` (obavezno).
2. Nađi post ID kontakt stranice (`wp post list --post_type=page --name=kontakt`).
3. Zameni tako da se **tekst poklopi sa href-om** — ne menjaj href vrednosti,
   samo tekst (ili obrnuto, ali dosledno; Miroslav preferira da 072 ostane prvi
   po redu jer je dominantan).
4. Proveri i ostale pojave po sajtu:
   ```
   wp db query "SELECT ID,post_title FROM wpGs_posts WHERE post_content LIKE '%234 00 7%' AND post_status='publish'"
   ```
   i widgete (`wp option get widget_custom_html --format=json`).
5. `wp litespeed-purge all`, pa `curl` + provera da href i tekst odgovaraju.

**Gotovo kada:** na `/kontakt/` svaki `tel:` href ima tekst istog broja.

---

## P3 — 🟡 Prenos Yoast title/meta sa lokalnog builda (3 stranice)

**Zašto:** dijagnostika 2026-07-27 je pokazala da refresh-evi od 2026-07-08
(zadaci W2 #7 i #9) **nisu uticali na CTR jer nikad nisu otišli na produkciju** —
lokalni build ima nove title/meta, produkcija i dalje servira stare. Google
prikazuje stari snippet, pa CTR matematički ne može da se pomeri.

| Post | Live title (sada) | Lokalni title (prenesi ovaj) | Klaster |
|---|---|---|---|
| 2699 `/podloga-za-teniske-terene/` | `Podloga za teniske terene - Antas Line` | `Šljaka za teniski teren — cena i ostale podloge za tenis` | 1.739 impr / **2 kl** |
| 4318 `/podloga-za-odbojkaske-terene/` | `Podloga za terene za odbojku - Antas line doo` | `Dimenzije odbojkaškog terena, mreže i podloge` | ~490 impr / **3 kl** |
| `/spoljnje-podne-obloge/` | `Podne obloge za bašte i terase - jednostavna montaza i veliki izbor boja` | `Podovi za terase, dvorišta i bašte – Bergo PVC podne obloge` | ~950 impr dvorište klaster |

Meta description takođe prenesi (pune vrednosti su u lokalnoj bazi —
`_yoast_wpseo_title` / `_yoast_wpseo_metadesc`; Miroslav ih može izvući lokalno i
proslediti, ili ih uzmi iz [[seo/2026-07-27-content-klasteri]]).

⚠️ **Prenosi SAMO title/meta — ne sadržaj, ne strukturu.** Sadržaj ide na
migraciju 2026-08-31 kao celina; ovde vadimo samo CTR polugu, reverzibilno.

⚠️ Posle upisa **obavezno obriši keširani Yoast red** (gotcha #12 iz
[[migracija/woodmart-sabloni]]) — inače stari naslov ostaje u `<title>`:
```
wp db query "DELETE FROM wpgs_yoast_indexable WHERE object_id IN (2699,4318,<id_spoljnje>)"
```
(ime tabele proveri — na produkciji može biti drugi prefiks.)

🔴 Usput ispravi: lokalna meta za `/spoljnje-podne-obloge/` sadrži
**„Pozovite 072 234 00 72"** — pogrešan format broja (pravi je `069 234 00 72`).
Ispravi pri prenosu.

**Gotovo kada:** `curl` na sve 3 stranice vraća novi `<title>`, keš purge-ovan.

---

## P4 — 🟡 Sitni 404 (WooCommerce, ~330 prikaza / 7 klikova / 16 meseci)

Potvrđeni 404 sa GSC saobraćajem (sken svih 136 URL-ova sa ≥20 prikaza, 2026-07-27):

| URL | impr | kl |
|---|---|---|
| `/proizvod/bergo-unique-cedar-wood/` | 79 | 2 |
| `/kategorija-proizvoda/audio-led-senzori-bezbednost/` | 69 | 1 |
| `/proizvod/bergo-unique-teget/` | 51 | 1 |
| `/proizvod/bergo-unique-bela/` | 44 | 0 |
| `/kategorija-proizvoda/bergo-podovi-za-baste/` | 32 | 3 |
| `/kategorija-proizvoda/industrijska-zastita/podno-obelezavanje/durastripe/` | 31 | 0 |
| `/kategorija-proizvoda/podloge-za-stale/` | 25 | 0 |

Tri Bergo Unique varijante → 301 na `/spoljnje-podne-obloge/bergo-unique/`.
Kategorije → najbliža živa kategorija (proveri `wp term list product_cat`).
**Nizak prioritet** — uradi samo ako ostane vremena posle P1–P3.

---

## P5 — 🟢 UTM varijanta početne indeksirana kao zasebna stranica

`/?utm_source=google%20my%20business&utm_medium=gmb&utm_campaign=gmb_page&...`
ima **337 prikaza** u INDUSTRIJSKI klasteru + 186 u BREND + 22 u EPOKSID.
Parametarska varijanta početne se indeksira odvojeno i cepa signale.

Proveri da li početna ima `<link rel="canonical" href="https://www.antasline.com/">`
bez parametara. Ako Yoast već postavlja ispravan canonical — **nema akcije**,
samo zabeleži (Google će sam konsolidovati). Ako ne — to je Yoast podešavanje,
javi Miroslavu, ne diraj sam.

---

## Rekapitulacija: šta NE raditi u ovoj sesiji

- ❌ Ne dirati `/teren-za-pickleball/` (fake-review pitanje, M odluka — [[PROGRESS]] Blokeri)
- ❌ Ne prenositi sadržaj stranica sa lokala (samo title/meta iz P3)
- ❌ Ne aktivirati `.htaccess` 301 iz `htaccess-301-DRAFT.txt` (to je za 31.08)
- ❌ Ne objavljivati draftove iz P1 bez Miroslavljeve potvrde

## Posle sesije

Prati [[CLAUDE-CODE-instrukcija-CPANEL]]: `git pull` → append u
[[DNEVNIK-NAPRETKA]] sa `[cpanel-live]` tagom → osveži [[PROGRESS]] → commit+push.
Označi u dnevniku šta ostaje kao `#claude-code` (lokalna strana) — naročito
parity red iz P1.
