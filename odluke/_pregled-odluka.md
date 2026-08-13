---
tip: odluke
azurirano: 2026-08-13
---

# Pregled odluka (i zašto)

## URL struktura — flat `/proizvod/`
**Odluka:** flat `/proizvod/` umesto silo-u-URL-u.
**Zašto:** silo autoritet se gradi internim linkovanjem, breadcrumb strukturom i schemom — URL path segment je zanemarljiv ranking faktor; flat struktura nosi manje operativnih rizika.

## SEO plugin — Rank Math (Yoast van upotrebe)
**Odluka (2026-08-13, M — zamenjuje odluku od 28.06 „Yoast, ne RankMath"):** na buildu
je JEDINI SEO plugin **Rank Math** (`rank_math_title` / `rank_math_description`). Yoast
je van upotrebe i ne vraća se; ne pisati više u `_yoast_wpseo_*` ključeve i ne
proveravati Yoast izlaz u verifikaciji.
**Zašto:** migracija Yoast→Rank Math je izvedena 2026-08-05 ([[CLAUDE]] §7.1, 7843 post
meta zapisa preneto, verifikovano na 10 kategorija + homepage + kontakt + conquest 2542),
Rank Math je od tada aktivan i emituje title/meta/schema/sitemap. Stara odluka je ostala
zapisana kao pravilo 8 dana posle migracije i bila je aktivan izvor grešaka — 13.08 je
zbog nje umalo upisan pogrešan meta ključ na 13 arhiva.
**Stanje fajlova:** `wp-content/plugins/wordpress-seo` je **deaktiviran** i još stoji na
disku (21 MB, v27.8); `_yoast_wpseo_*` postmeta ostaje u bazi (nije brisana). Brisanje sa
diska — v. [[PROGRESS]] Blokeri.

## Epoksid — conquest, ne van ponude
**Odluka:** epoxy upiti se namerno targetiraju kroz `/epoksidni-podovi-ili-ecotile-podovi/` (post 2542) radi konverzije u Ecotile.
**Zašto:** AntasLine NE prodaje epoksid; epoxy upiti = kvalifikovana tražnja na vrhu levka. Nikad ne predlagati sadržaj koji prodaje epoksid.

## Bidding — ostati na Maximize Clicks
**Odluka:** ne prelaziti na Maximize Conversions pre 20–30 pravih formi sa `/hvala-za-poruku/`.
**Zašto:** jun 2026 = 53 ukupno ali samo 2–3 iz plaćenog — premalo signala za smart bidding.

### Dopuna 2026-08-13 — 4.8 ODLOŽENO, prag nije dostignut (nije „skoro pa ispunjen")
**Odluka:** zadatak 4.8 iz [[2026-07-06-MASTER-PLAN-V2]] se **odlaže na posle live-a
(~01.09)**; do tada nalog ostaje na **Maximize Clicks**. Ranija tvrdnja „26 plaćenih
konverzija, prag pređen" (stajala u [[dnevnik/ADS-DNEVNIK]] i prepisivana u izveštaje)
je **netačna** i ne sme se više koristiti kao osnov za odluku o licitiranju.
**Zašto:**
1. Od „26 plaćenih konverzija" (01.06–10.08) **17 su klikovi na telefon** — akcija
   `Klik na telefon (web)` ima `include_in_conversions_metric=True`, dakle ulazi u
   „Conversions" kolonu i u Smart Bidding, suprotno [[CLAUDE]] §4 („ne uvoziti GA4
   `tel` kao Ads konverziju"). **Pravih plaćenih lidova sa forme: 9.**
2. Čak i taj broj je meren naduvanom serijom — dupli GA4 `page_view` tag (id 18) na
   `/hvala-za-poruku/`; posle migracije `generate_lead` pada na ~⅓, hvala-proxy na ~½
   (v. [[dnevnik/2026-08-11-generate-lead-inflacija-dijagnoza]]). Prelazak na Smart
   Bidding pre te korekcije značio bi učenje na pogrešnim brojkama.
3. Vreme: Smart Bidding uči ~14 dana. Uključeno u avgustu → period učenja se završava
   tačno kad se menjaju URL-ovi oglasa (migracija 24.08) — najgori mogući preklop.
**Preduslovi za ponovno otvaranje:** (a) `Klik na telefon (web)` prebačen u
**Secondary action** · (b) tag id 18 obrisan iz GTM-a · (c) 301 slegnute posle
migracije · (d) 20–30 **pravih** formi izmereno na očišćenoj seriji.
**Usput, ista logika:** 6 BROAD ključnih reči troši ~10.300 RSD/90d sa **0 konverzija**
dok nalog radi na Maximize Clicks — plan kaže „broad tek uz Smart Bidding", dakle
nalog radi suprotno sopstvenom pravilu. #ceka-miroslav (pauziranje).
v. [[analiza/2026-08-11-snapshot-jul]] §3.3/§3.6 · [[PROGRESS]] Blokeri.

## Prompt API / Gemini Nano u browseru — NE koristimo (2026-08-12)
**Odluka:** built-in AI (Prompt API, Gemini Nano) se ne uvodi ni na sajt ni kao
interni alat. Nema ponovnog razmatranja dok Chrome ne doda srpski jezik.
**Zašto:** (1) podržani jezici su EN/JA/ES/DE/FR — srpski nije na listi, a ceo
naš sadržaj i publika su na srpskom; (2) samo desktop Chrome (nema Android/iOS),
a naša publika je mobilna (~46 od 50 klikova na telefon dolazi sa mobilnog);
(3) hardverski prag >4 GB VRAM + 16 GB RAM + 22 GB slobodnog prostora — Miroslavljev
laptop ga ne prolazi (2 GB VRAM, 15,7 GB RAM), pa se ne bi mogao ni testirati;
(4) za sve što bi radio već imamo bolji alat u toku rada (Claude Code, Gemini API).
Detalji i alternativa: [[reference/chrome-web-platform-2026]] §7.

## Otvorene odluke (BLOK C)
Vidi [[blokovi/BLOK-C-sledece]] — dispozicija staging-only proizvoda, overwrite vs preserve postova.

## Veze
- [[blokovi/BLOK-C-sledece]]
