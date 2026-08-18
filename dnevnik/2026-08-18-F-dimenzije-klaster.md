---
tip: sesija
datum: 2026-08-18
tag: claude-code
blok: W2 / SEO — kanibalizacija
stavke: "F (dimenzije klaster) · akcija #4 (title/meta industrijski podovi)"
status: zavrseno
azurirano: 2026-08-18
---

# F — dimenzije klaster ↔ post 2298, + title/meta `/industrijski-podovi/`

## Zašto

Post 2298 (`kako-napraviti-teren-za-basket…`) je najjači sadržaj na sajtu —
**13.686 prikaza / 385 klikova / 90d**, poz. 1–2 za „dimenzije fudbalskog terena"
(2.174 prikaza), „dimenzije košarkaškog terena" (2.004) i „dimenzije košarkaške
table" (719). Na buildu su četiri **nove** stranice koje gađaju baš te upite, a
25.08 izlaze pred Google **bez ijednog uzajamnog linka** sa 2298. Bez klaster
signala rizik je da Google počne da se koleba između njih i da izgubimo poziciju 1.

## Šta je urađeno

**Backup pre izmena:** `antasline-backups/antasline_local_2026-08-18_1830_pre-F-dimenzije.sql` (35,2 MB).

### 1. Uzajamno linkovanje (klaster oko huba 2298)

| Stranica | Pre | Posle |
|---|---|---|
| 2298 (hub) | linkovao 16585 + 16586 | + 16688 (tenis) + 17027 (fudbal) — rečenica u sekciji „Dimenzije terena za basket" |
| 16585 tabla | 0 odlaznih ka klasteru | → 2298 + 16586 + 16688 + 17027 |
| 16586 košarkaški teren | → 16585 | → 2298 + 16585 + 16688 + 17027 |
| 16688 teniski teren | 0 | → 2298 + 16585 + 16586 + 17027 |
| 17027 fudbalski teren | 0 | → 2298 + 16586 + 16688 |

Pasus je ubačen ispred zatvarajuće CTA sekcije svake stranice („Od dimenzija do
gotovog terena" / „Treba vam konstrukcija…" / „Gradite fudbalski teren?"), tako
da vodi ka izvođenju — dakle služi i korisniku, ne samo linku.

### 2. Pomak title-a ka transakcionoj nameni

| ID | Pre | Posle |
|---|---|---|
| 16586 | `Dimenzije košarkaškog terena — FIBA, NBA, školski [tabela]` | `Dimenzije košarkaškog terena i izgradnja — FIBA, NBA \| Antas Line` |
| 17027 | `Dimenzije fudbalskog terena — standard, gol, šesnaesterac \| Antas Line` | `Dimenzije fudbalskog terena i izgradnja — mere, gol, podloga \| Antas Line` |
| 16585 | `Dimenzije table za košarku — koš, visina, cena \| Antas Line` | **nepromenjen** — „cena" je već transakcioni diferencijator |
| 16688 | `Dimenzije teniskog terena — singl, dubl i mreža [tabela]` | **nepromenjen** — 2298 ne cilja tenis (🟡 u analizi), nema šta da se razdvaja |

### 3. Akcija #4 — `/industrijski-podovi/` (16567)

Head termin curi: **6.321 prikaz, CTR 2,6%, poz. 7,2**.

- **Live naslov** (onaj koji pravi taj CTR): `PVC Industrijski podovi | Podovi za Fabrike, Magacine i Radionice - Antas Line`
  — 78 znakova, seče se u SERP-u, bez cene i bez ijednog diferencijatora.
- Build je već imao bolji (`Industrijski PVC podovi — Ecotile, standardi i cena po m²`), ali
  bez konkretne cifre i sa „PVC" umetnutim usred head termina.

| | Novo |
|---|---|
| `rank_math_title` | `Industrijski podovi — od 5.500 RSD/m², montaža bez zastoja` (58 zn.) |
| `rank_math_description` | `Ecotile PVC ploče za magacine, proizvodnju i radionice — od 5.500 RSD/m². Klik-montaža preko postojećeg betona, bez lepka i bez zastoja proizvodnje. R10, VOC E1.` |
| `rank_math_focus_keyword` | `industrijski podovi` (nije postojao) |

Cifra **nije izmišljena** — 5.500 RSD/m² (500/7) i 6.800 (500/10) već stoje u telu
16567. Namenska stranica 16874 je 18.08 prebačena u draft (konsolidacija cenovnih
stranica), pa hub nosi cenovni intent sam — nema dupliranja.

🔴 **Održavanje:** cena je sada u `<title>`. Kad se cenovnik promeni, meni ovo mora
ući u isti potez, inače SERP obećava staru cifru.

## Dve greške u toku rada (i kako su zatvorene)

1. **Mojibake kroz PowerShell pipe.** `Get-Content -Raw | mysql` je duplo enkodirao
   UTF-8 → naslovi su upisani kao „koĹˇarkaĹˇkog", a anchor sa dijakritikom (za 2298)
   se nije poklopio pa se ta izmena tiho preskočila. Baza je vraćena iz backup-a i
   sve ponovljeno kroz **Bash redirekciju** (`mysql … < fajl.sql`), koja prosleđuje
   sirove bajtove. → Pravilo: SQL sa dijakritikom ide **isključivo** `<` redirekcijom.
2. **2×H1 na 17027.** Postavio sam hero heading sa `<h2 class="al-display--xl">` na
   `<h1>` misleći da je propust — ali 17027 je `post`, a `_woodmart_title_off` radi
   samo za `page`; WoodMart svejedno renderuje `wd-post-title` H1. `<h2>` je bio
   ispravan. Vraćeno. → **Pravilo je već postojalo**: `migracija/woodmart-sabloni.md` **F7.18**
   („`_woodmart_title_off` NE radi za postove, samo za stranice”, 27.07) — zapisano baš na
   primeru posta 17027. Promašeno jer taj fajl nije otvoren pre izmene (79 KB, „OBAVEZNO
   prvo”). Ne treba nova beleška, nego da checklist bude čitljiv — v. vault higijenu u [[PROGRESS]].

## Verifikacija (HTTP, lokalni build)

| URL | HTTP | H1 | Link ka hubu |
|---|---|---|---|
| `/dimenzije-fudbalskog-terena/` | 200 | 1 | ✅ |
| `/dimenzije-kosarkaskog-terena/` | 200 | 1 | ✅ |
| `/dimenzije-kosarkaske-table/` | 200 | 1 | ✅ |
| `/dimenzije-teniskog-terena/` | 200 | 1 | ✅ |
| `/kako-napraviti-teren-za-basket-ili-kosarkaski-teren/` | 200 | 1 | — (hub) |
| `/industrijski-podovi/` | 200 | — | title/meta potvrđeni u `<head>` |

Dijakritika u ubačenim pasusima i naslovima proverena na renderovanoj stranici — čisto.

## Ograničenje ovog rešenja

Interno linkovanje + pomak title-a **smanjuju**, ali ne uklanjaju rizik da Google
posle 25.08 privremeno zameni 2298 nekom od četiri nove stranice za „dimenzije *"
upite. To se ne može isključiti unapred; jedina tvrda alternativa je bila `noindex`
na sve četiri, što ubija njihov sopstveni potencijal. **Merenje posle live-a:** GSC
poz. za 6 upita iz §3.1 na nedeljnom preseku — ako 2298 padne ispod poz. 3, sledeći
korak je `noindex` na 17027 i 16586 dok se ne slegne.

## Statusi ažurirani

- `seo/2026-08-13-kanibalizacija-konsolidacija-plan.md` — E (bio zastareo red: gotov 13.08), F ✅, G ✅. Preostalo CC posla pre freeze-a: **0**.
