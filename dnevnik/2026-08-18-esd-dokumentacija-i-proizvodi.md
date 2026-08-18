---
tip: sesija
datum: 2026-08-18
tag: claude-code
blok: W1 / W2 — ESD klaster
status: zavrseno
azurirano: 2026-08-18
---

# ESD klaster — zvanična dokumentacija, dva nova proizvoda, dopuna stranice 16658

## Odgovori na 4 `#ceka-miroslav` pitanja (zatvoreno)

M je dao izvore; vrednosti su izvučene iz zvaničnih Ecotile tehničkih listova, ne iz procene.

| # | Pitanje | Odgovor |
|---|---|---|
| 1 | Deklaracija ESD serije | X-Joint ESD: površinski **1,46 × 10⁶ Ω**, prema zemlji **9,3 × 10⁵ Ω** · E500/7 ESD: **2,2 × 10⁴ – 3 × 10⁶ Ω** / **2,9 × 10⁴ – 5,7 × 10⁵ Ω**. Metode EN/ISO 61340-5, EN 1081, EN 1815, EN 6356; usklađeno sa **BS EN / IEC 61340-5-1:2016** |
| 2 | Elektroprovodljivo / ATEX | **Na upit, ne paušalno.** Datasheet: standardna ploča se uzemljuje preko 1 MΩ otpornika, a Ecotile nudi rešenja „from conductive to the higher end of the ESD spectrum". Kopi sme „elektroprovodljiva varijanta na upit, uz deklaraciju za konkretnu isporuku"; **ne sme** „ATEX sertifikovano" |
| 3 | Merenje sa zapisnikom | **Da** — ušlo kao istaknuta usluga na stranicu, u FAQ i na oba nova proizvoda |
| 4 | Cena ESD | **Na upit** — ESD ne dobija cenovni red; industrijska linija zadržava 5.500/6.800 RSD/m² |
| 5 | Srpski propis | Live stranica se ne poziva ni na jedan domaći propis. Pravilo ostaje: **ne pisati „zakonska obaveza"**, nego „standard koji se traži u auditima" |

## Ispravka mog ranijeg nalaza o parity-ju

🔴 Prijavio sam da „42 od 52 rečenice sa live-a ne postoje na buildu" i izveo iz toga
osam rupa. **To je bilo pogrešno.** Poređenje je išlo po rečenicama, a build je live
tekst **prepisao, ne izbacio** — pa se ni jedna rečenica nije poklopila doslovno iako
je značenje preneto. Provera punog sadržaja 16658 pokazala je da build **već ima**:
ATEX/zapaljive materijale, zoniranje bojama, opremu na točkovima, MoD UK i listu
klijenata, 25 godina iskustva, premeštanje u drugi prostor (ugao za zakupce),
uzemljenje na min. 80 m², tepih/kvaka objašnjenje, cenu „zavisi od kvadrature".

Stvarnih rupa je bilo **pet**, i sve su zatvorene ovom sesijom.

## Šta je urađeno

**Backup:** `antasline-backups/antasline_local_2026-08-18_2015_pre-esd-rebuild.sql` (35,25 MB).

### 1. Sedam zvaničnih PDF-ova u medijateku
Preuzeto sa ecotileflooring.com, kopirano u `uploads/2026/08/`, kreirani attachment
zapisi (ID 17850–17856). Svi vraćaju HTTP 200.

Tehnički listovi: X-Joint ESD (SD130) · E500/7 ESD (SD100) · X-Joint antistatik (02/2026) ·
**zapisnik ispitivanja X500/7 ESD (7 strana)** · uputstva za ugradnju SD127 i SD113 ·
uputstvo za održavanje.

> Zapisnik ispitivanja je najvredniji dokument za B2B — traži ga inženjer kvaliteta uz
> auditsku dokumentaciju EPA zone. Stavljen je visoko, ne u podnožje.

### 2. Tri ploče, ne jedna — dva nova proizvoda

Datasheetovi su otkrili da su u pitanju **tri različite ploče**, a katalog je imao jednu:

| Ploča | Otpor | Uzemljenje | Status |
|---|---|---|---|
| X-Joint ESD 7 mm (16542) | 1,46 × 10⁶ / 9,3 × 10⁵ Ω | da | postojao |
| **E500/7 ESD** (17860) | 2,2×10⁴–3×10⁶ / 2,9×10⁴–5,7×10⁵ Ω | da | 🆕 dodat |
| **X-Joint antistatik** (17861) | ≈ 1 × 10⁹ Ω | **ne** | 🆕 dodat |

Treća je **antistatik, ne ESD** — to je razlika koju stranica ranije nije objašnjavala,
a koja određuje da li prostor mora da se uzemlji. Oba nova proizvoda: pun tehnički
tabelarni prikaz iz deklaracije, linkovi na PDF-ove, unakrsni linkovi na ostale dve
ploče i na hub stranicu, Rank Math title/meta, cena „na upit" (bez `_price`, kao 16542),
9 atributa preko postojećih `pa_*` taksonomija + 4 nova terma (412–415).

### 3. Stranica 16658 — pet dopuna
- **Uporedna tabela antistatik / ESD / elektroprovodljivo** zamenila staru spec tabelu.
  🔴 Stara je nosila vrednost „otpornost 3,4×10⁴ – 5×10⁶ Ω" koja **ne postoji ni u jednom
  tehničkom listu** (preneta sa live-a) i „debljina 7 mm (dostupno i 5 mm)" — 5 mm ESD
  nije potvrđen nijednom deklaracijom. Zamenjeno deklarisanim vrednostima po seriji.
- **Sekcija „Merenje otpora sa zapisnikom"** — usluga, sa objašnjenjem zašto je zapisnik
  ono što zatvara posao (pod može biti ispravan, ali se bez zapisnika ne može dokazati).
- **Sekcija „Zašto ne epoksidni antistatik pod"** — conquest ugao koji je nedostajao, sa
  linkom na `/epoksidni-podovi-ili-ecotile-podovi/`.
- **Sekcija „Tehnička dokumentacija"** sa svih 7 PDF-ova.
- **FAQPage JSON-LD** — nije postojao (isti propust kao na 5438, nađen 13.08), plus dva
  nova pitanja: razlika antistatik vs. ESD i merenje sa zapisnikom. Ukupno 7 pitanja.

Stranica: 13.797 → 22.925 znakova.

### 4. Proizvod 16542 dopunjen
Dodata sekcija dokumentacije (4 PDF-a), deklarisane vrednosti otpora i unakrsni linkovi
na dve nove ploče i hub.

## Tri gotcha-a iz ove sesije

1. 🔴 **Windows CRLF konverzija kvari `post_content` pri čitanju iz `mysql -B --raw`.**
   Sadržaj 16658 ima interne `\r`/`\n`, koje pipe pretvara u `\r\n` — readback se ne
   poklapa sa upisom. Rešenje: čitati **`SELECT HEX(post_content)`** i dekodirati u
   Pythonu, pisati preko `UNHEX(...)`, pa obavezno uporediti. Helper: `wpdb.py`.
2. 🔴 **Novi proizvod ne ulazi u WooCommerce upite bez reda u `wpgs_wc_product_meta_lookup`.**
   SQL insert u `wpgs_posts` + `postmeta` + `term_relationships` nije dovoljan.
3. 🔴 **Rank Math kešira sitemap kao FAJLOVE** u `wp-content/uploads/rank-math/*.xml`.
   Brisanje opcije `rank_math_sitemap_cache_files` i transienata **ne pomaže** — sitemap
   je i dalje vraćao `lastmod` od 13.08. Tek `rm *.xml` iz tog foldera regeneriše.
   Isti obrazac kao „trebalo je `wp rewrite flush`" iz CLAUDE §7.1.

## Verifikacija

| Provera | Rezultat |
|---|---|
| 4 URL-a (stranica + 3 proizvoda) | 200, **1×H1** svuda |
| JSON-LD | stranica: FAQPage + Article + LocalBusiness, svi validni · proizvodi: Product + BreadcrumbList, validni |
| 7 PDF linkova | 200 |
| 6 internih linkova iz novog sadržaja | 200 |
| Grid na stranici | prikazuje sve tri ploče |
| Sitemap | oba nova proizvoda prisutna posle purge-a keša |

### 5. Slike proizvoda sa proizvođača (dopuna, ista sesija)

Šest fotografija preuzeto sa ecotileflooring.com i shop.ecotileflooring.com, obrađeno po
projektnom standardu — **1:1 center-crop, max 1000×1000, WebP**:

| Proizvod | Glavna | Galerija |
|---|---|---|
| E500/7 ESD (17860) | `e500-ploca` 705² | `e500-spajanje` 1000² · `e500-t-joint` 705² |
| X-Joint antistatik (17861) | `xjoint-ploca` 705² | `xjoint-spajanje` 705² · `xjoint-tamno-siva` 934² |

Za svaku su **generisane WP veličine** (150/300/400/600/768/900 px) i upisan pun
`_wp_attachment_metadata` — bez toga bi grid vukao punu sliku umesto 300 px varijante.
Alt tekstovi na srpskom, opisni. Attachment ID 17870–17875, `post_parent` = proizvod.

⚠️ **Napomena o izvoru:** Ecotile ne objavljuje zasebnu fotografiju antistatik ploče —
X-Joint ploča je vizuelno identična ESD verziji (razlika je u materijalu: antistatik PVC
bez čeličnih vlakana). Za 17861 su zato uzete fotografije X-Joint formata **bez priključka
za uzemljenje**, jer se ta ploča ne uzemljuje; fotografija sa uzemljivačkim priključkom
namerno **nije** korišćena na antistatik proizvodu.

Verifikovano: obe stranice 200, glavna slika i po 3 slike u galeriji, `srcset` radi u
gridu (300w/150w/400w/600w/705w), **Product schema sada nosi `image`**.

## Otvoreno

- Nije proveren srpski SRPS koji preuzima IEC 61340-5-1 — pravilo „ne pisati zakonska
  obaveza" ostaje na snazi.
