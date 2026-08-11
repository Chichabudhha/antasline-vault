---
datum: 2026-08-11
tag: claude-code
oblast: W3 / migracija + SEO QA
status: delimično zatvoreno (1 pitanje ostaje na M)
---

# 2026-08-11 — Rollback plan (2/3 pitanja zatvorena) + sitewide provera dijakritike

Druga i treća stavka iste sesije (posle 6 meta opisa, v.
[[dnevnik/2026-08-11-metadesc-6-stranica]]).

## A. Provera dijakritike u title/meta — sitewide

Povod: gotcha iz prvog dela sesije (UTF-8 pokvaren kroz `mysql -e`) otvorila je
pitanje da li je isti obrazac ranije oštetio i druge naslove/opise — pogotovo jer
je 2026-07-30 već postojao sličan incident (mojibake u Yoast keš tabeli, 93
naslova / 103 opisa).

**Nivo 1 — baza.** Skenirani `rank_math_title`, `rank_math_description` i
`post_title` svih objavljenih `page`/`post`/`product`/`product_variation` na:
dvostruko enkodovan UTF-8 (`Ä`/`Å`/`Ã`/`â€`), znak zamene `�`, i `?` usred reči.
**Rezultat: 0 nalaza.** Kontrolno: 112 `rank_math_title` + 163
`rank_math_description` + 181 `post_title` stvarno nose dijakritiku i ispravni su.

🔴 **Gotcha koja je zamalo dala lažan alarm:** prvi prolaz je prijavio ~385
"mojibake" redova. Sve lažni pozitivi — kolone su
`utf8mb4_unicode_520_ci`, a ta kolacija je **akcent- i case-neosetljiva**, pa
`LIKE '%Ä%'` uredno pogađa obično `a`/`A`. Za bilo koju proveru *oblika zapisa*
(ne značenja) mora `LIKE BINARY` ili `COLLATE utf8mb4_bin`. Ista zamka pogađa i
naizgled bezazleno `LIKE '%ć%'` — ono broji i sve `c`.

**Nivo 2 — renderovani izlaz. ✅ 195/195 čisto.** Baza može biti čista a izlaz
pokvaren (tačno to se desilo 07-30, gde je izvor bio keš tabela). Zato pušten i
sweep kroz svih 195 URL-ova iz sitemap-a (`sweep-dijakritika.sh`): za svaku
stranicu `<title>` + meta description → provera na mojibake / `�` / `?` usred
reči / prazan title / prazan opis. **Rezultat: 0 nalaza na svih 195.**

Detektor je posle toga proveren kontrolnim testom na namerno pokvarenim
primerima (`PodloÅ¾ni Ä‡ilim`, `ko?arku`) — hvata ih, dakle nula je stvarna a ne
bag u alatu. **Pouka za ubuduće: kad provera vrati 0 nalaza, propustiti kroz nju
bar jedan poznat-loš primer** — isti tip greške (alat, ne sajt) već je udario
10.08 dva puta u regression sweep-u.

## B. Rollback plan — 2 od 3 otvorena pitanja zatvorena

[[migracija/rollback-plan]] je od 2026-07-27 stajao kao draft sa 3 pitanja za
Miroslava (rok 15.08, gate stavka). Dva su zatvorena bez njegovog vremena:

**1. Ima li hosting sopstveni automatski backup? — DA, odgovor je već postojao.**
JetBackup 5, cPanel-dostupan, **dnevni**, remote/off-site kod provajdera, **90
dana retencije** — M je to proverio direktno u cPanel-u još 2026-07-27 i upisao u
[[2026-07-06-MASTER-PLAN-V2]] §4 red M6, ali nikad nije preneto u rollback plan,
pa je pitanje 2 nedelje stajalo kao otvoreno bez potrebe.
⚠️ Ostaje treća linija odbrane, **ne zamena** za ručni backup: granularnost je
dnevna (najgori slučaj gubitak do 24h live sadržaja) i deli infrastrukturu sa
provajderom. Praktična dopuna plana: pre 3.11 pogledati u JetBackup da poslednji
snapshot nije stariji od 24h.

**2. Postoji li CDN/edge keš sloj? — NE.** Utvrđeno read-only proverom live-a:
`antasline.com` i `www` razrešavaju direktno na `138.201.234.168` (Hetzner,
nameserveri `ns1–ns4.oblak.host`), bez CNAME na CDN; HTTP zaglavlja daju
`Server: LiteSpeed` + `X-LiteSpeed-Cache: hit`, a **nema** `cf-ray`, `via`, `age`
ni `x-qc-cache` — dakle ni Cloudflare ni QUIC.cloud. Korak 4 rollback-a
("očisti keš") se time svodi na LSCWP Purge All i nema drugog sloja koji bi posle
toga servirao staru verziju. Upozorenje upisano u plan: ako se QUIC.cloud uključi
pre migracije (LSCWP ga nudi u istom pluginu), korak se menja.

**3. Ko izvršava rollback ako Miroslav nije dostupan? — ✅ ODLUČENO ISTOG DANA
(M): „migracija samo kad sam tu."** Od tri ponuđene opcije izabrana treća.
Nema rezervne osobe sa cPanel pristupom i ne traži se ništa od hostinga unapred
— umesto toga **dostupnost Miroslava postaje uslov za pokretanje migracije**.

Operativna posledica koju odluka nosi na dan migracije (upisana u §5 plana):
migracija 3.11 se **ne pokreće** ako M nema ~6h slobodnih ispred sebe (rollback
budžet je 35–50 min, ali tek posle dijagnostike i odluke) — kasno popodne/veče
24.08 nije prihvatljiv start; ako tog dana nema tog prozora, migracija se
pomera, isto pravilo kao gate.

Prihvaćen rizik, eksplicitno: ako M postane nedostupan **usred** incidenta,
ostaje jedino improvizovan poziv oblak.host podršci / JetBackup restore. Njihov
SLA i kanal van radnog vremena namerno **nisu** proveravani unapred.

**Time je rollback plan zatvoren u celini — gate stavka ispunjena 4 dana pre
roka (15.08).** Od 3 crvene gate stavke ostaju 2: LCP (spoljno ograničenje,
čeka produkciju) i svež live backup na 2 lokacije (traži `[cpanel-live]` sesiju).

## Otvorene akcije

- 🔴 **Za dan migracije (24.08):** provera dostupnosti M pre starta — to je sada
  formalni preduslov, ne neformalna procena.
- 🟡 **#ceka-miroslav, rok 16.08:** 4 `noindex` stranice van sitemap-a (iz
  prvog dela sesije) — namerno ili ne.

## Veze
[[migracija/rollback-plan]] · [[2026-07-06-MASTER-PLAN-V2]] §3 i §4 ·
[[dnevnik/2026-08-11-metadesc-6-stranica]] · [[reference/naucene-lekcije]]
