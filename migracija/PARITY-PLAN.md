---
tip: plan
naziv: PARITY-PLAN — build 1:1 prema live sajtu
datum: 2026-07-07
status: aktivan
zamenjuje: stara redirect mapa (migracija/arhiva/)
veza: "[[2026-07-06-MASTER-PLAN-V2]]"
---

# 🎯 PARITY-PLAN — novi build 1:1 prema live sajtu

**Izvor istine za migracioni pristup od 2026-07-07.** Zamenjuje staru redirect mapu
(`migracija/arhiva/` — vidi [[migracija/arhiva/_SUPERSEDED]]).

**Strategija (Miroslavljeva odluka 2026-07-07):** novi WoodMart build se pravi
**1:1 prema live sajtu i njegovom sadržaju** — URL parity + content parity —
da se SEO sačuva u celosti, pa onda unapredi. Redirect mapa se svodi na minimum:
samo namerne, dokumentovane promene URL-a.

**Izvršenje:** faze F1–F7, svaka ima svoj samostalan prompt u
`migracija/promptovi/` — vidi [[migracija/promptovi/_README]]. Jedna faza = jedna sesija.

---

## 1. Potvrđene odluke

| # | Odluka | Detalj |
|---|---|---|
| P1 | **Slug politika = hibrid po težini** | Top ~15 URL-ova po GSC klikovima: strogi parity 1:1, bez izuzetka. Nizak saobraćaj: novi slug ako je jasno bolji, uz 301. Konsolidacije duplikata uvek dozvoljene. Obrazloženje: ključna reč u slugu je zanemarljiv ranking faktor; 301 nosi 2–8 nedelja nestabilnosti po stranici + rizik izvršenja. |
| P2 | **Postovi = pun reimport svih 30 sa live** | Lokalni postovi (stari stil) se brišu i uvoze sveži iz WXR exporta. Restyle na WoodMart ide posle, iterativno. Ovim je zavisnost **M8 rešena**. |
| P3 | **Live sitemap = izvor istine za inventar** | Yoast, 7 sub-sitemapa na `antasline.com/sitemap_index.xml` — ništa ne treba dodavati na live. |
| P4 | **Parity je jednosmeran** | Sve što postoji na live mora postojati lokalno (ili imati nameran 301). Novi lokalni sadržaj (C3 stranice, novi proizvodi) je dobitak, ne rizik. |
| P5 | **Troslojna arhitektura namena→proizvod** | Namenske stranice prestaju da opisuju jedan proizvod; postaju "rešenje hub" sa auto-gridom po `namena` tagu (F6). |
| P6 | **Content standard pre live-a** | Proizvodi maksimalno obogaćeni (standardi sa linkovima), SVG ikonice, skice u jedinstvenom stilu, video kroz fasadni embed (F7). |

## 2. Izmereno stanje — FINALNO (F1 izvršen 2026-07-07)

`migracija/parity-inventar.csv` — 175 redova, svih 7 live sub-sitemapa (post/page/product/category/product_brand/product_cat/product_tag) upoređeno sa lokalnom bazom + GSC klikovi (12 meseci, Windsor.ai) spojeni po URL-u.

| Tip | Live | PARITY | NEDOSTAJE-LOKAL | LOKAL-NOVO | Napomena |
|---|---|---|---|---|---|
| Postovi | 30 (+1 arhiva) | 25 | 5 | 7 | F3 |
| Stranice | 48 | 8 | 40 | 22 | F5 |
| Proizvodi | 37 (+1 katalog) | 34 | 3 | 3 | F2 |
| Blog kategorije (`category`) | 7 | 7 | 0 | — | parity potpun |
| Woo kategorije (`product_cat`) | 9 | 8 | 1 | — | 1 = slug varijanta (i/bez i), ne pravi gap |
| Woo brendovi (`product_brand`) | 2 | 2 | 0 | — | parity potpun |
| Woo tagovi (`product_tag`) | 8 | 0 | 8 | — | 🔴 nijedan ne postoji lokalno — F6/F7 razmotriti |

**Ključni nalaz (menja prioritet u F5):** `/sportske-podloge/kosarkaske-konstrukcije/`
ima **923 klika/12mes** — veće od ranije dokumentovanih 478 (stariji/kraći period).
Ovo je sada najveći pojedinačni SEO rizik u celom planu.

**Ostali visoko-prometni NEDOSTAJE-LOKAL** (GSC 12mes): `/spoljnje-podne-obloge/`
(1304 — graniči sa top15, F4 granični slučaj), `/antistatik-i-elektroprovodljivi-podovi/`
(1131), `/spoljnje-podne-obloge/bergo-xl/` (978), `/industrijski-podovi/industrijski-pod/`
(625), `/podovi-za-stale/` (402), `/sportski-podovi-za-sale-i-balone/` (378).

## 3. Šta je odbačeno iz starog plana

- `POPUNJENA.csv` (118 redova) — pisana za Porto restrukturiranje; `/shop/` targeti,
  AUTO-PREDLOG redovi, `/kategorija/` baza → sve bespredmetno. Arhivirana.
- Mapa 2026-07-07 (41 red) — greška: `podovi-za-parkiraliste-i-staze` označen PARITY
  iako se slug razlikuje od lokalnog `podloge-za-...`. Arhivirana.
- MASTER-PLAN V2 zadaci W3 3.1/3.4 u starom obliku — prepisani na parity pristup.
- Zastarele redirect statistike u PROGRESS Napomenama.

## 4. Faze (prompt po fazi u `migracija/promptovi/`)

| Faza | Šta | Prompt | Preduslov |
|---|---|---|---|
| **F1** | Master parity inventar: 7 live sitemapa vs lokal → `parity-inventar.csv` sa GSC težinama | [[migracija/promptovi/F1-parity-inventar]] | — |
| **F2** | Permalink fix: Woo `/proizvod/` flat + `/kategorija-proizvoda/`, blog→`/aktuelnosti/`, 2–3 proizvod sluga | [[migracija/promptovi/F2-permalink-fix]] | backup |
| **F3** | Pun reimport 30 postova sa live (WXR + slike), ID reference update, anti-kanibalizacija re-primena | [[migracija/promptovi/F3-posts-reimport]] | F1 (LOKAL-NOVO statusi), backup |
| **F4** | Minimalna redirect mapa (~10–20 redova namernih promena) + `.htaccess` (NE aktivira se) | [[migracija/promptovi/F4-redirect-mapa]] | F1–F3; **Miroslav potvrđuje granične slugove** |
| **F5** | Trijaža ~42 nedostajuće stranice → W1 1.2 red čekanja po GSC klikovima | [[migracija/promptovi/F5-trijaza-stranica]] | F1 |
| **F6** | Arhitektura namena→proizvodi: `namena` tag na 37 proizvoda + auto grid + pravila stranica/kategorija | [[migracija/promptovi/F6-namena-arhitektura]] | F2 |
| **F7** | Content standard: obogaćivanje proizvoda (standardi sa linkovima), SVG ikonice, antas-skica stil, video | [[migracija/promptovi/F7-content-standard]] | F6 (namena tag) |

Redosled: F1 → F2 → F3 → F4 → F5 → F6 → F7. F4–F7 mogu da se preklapaju sa tekućim W1/W2 radom.

## 5. Veza sa gate kriterijumima (MASTER-PLAN V2 §3)

- "Redirect mapa 118/118 verifikovana" → **postaje**: "parity-inventar.csv kompletan
  (svaki live URL ima status) + minimalna redirect mapa potvrđena + .htaccess generisan i testiran"
- "Content parity checklist" → parity-inventar.csv JE checklist (statusi NEDOSTAJE-LOKAL na nuli ili sa 301)
- Ostali gate-ovi nepromenjeni.

## 6. Tvrda pravila (važe u svim fazama)

- ❌ Live sajt se NE dira
- ✅ Backup baze pre svake destruktivne izmene (`mysqldump -u root antasline_local > C:\xampp\htdocs\antasline-backups\antasline_local_YYYY-MM-DD_pre-<opis>.sql`)
- ❌ Ne izmišljati brojeve/cene/standarde — samo potvrđeno iz datasheet-ova
- `.htaccess` 301 se aktivira TEK na dan migracije
- Restyle postova NIJE deo F3 — poseban W1/W2 batch

## Veze
[[2026-07-06-MASTER-PLAN-V2]] · [[migracija/promptovi/_README]] · [[migracija/arhiva/_SUPERSEDED]] · [[migracija/woodmart-sabloni]] · [[PROGRESS]] · [[DNEVNIK-NAPRETKA]]
