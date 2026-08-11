---
tip: dnevnik
alat: claude-code
datum: 2026-08-11
blok: C
status: zavrseno
---

# Sesija — LiteSpeed CCSS/UCSS/LQIP/VPI status provera (uživo)

> Nastavak iste `[cpanel-live]` sesije (Redis/Web Cache Manager nalaz +
> backup + robots.txt, v. [[dnevnik/2026-08-11-litespeed-redis-web-cache-manager]]).
> Okidač: Miroslav primetio aktivnost na QUIC.cloud dashboardu i pitao da li
> ostale LiteSpeed optimizacije (LQIP, CCSS/UCSS/VPI) rade.

## Šta je urađeno

Provereno direktno kroz DB tabele plugina (`wp_litespeed_url_file`), fajlove
na disku (`wp-content/litespeed/{ccss,ucss,lqip,vpi}/`) i izvorni kod
(`wp-content/plugins/litespeed-cache/src/*.cls.php`) — ne kroz dashboard/UI.

| Funkcija | Status | Dokaz |
|---|---|---|
| CCSS | ✅ radi, aktivno | Novi fajlovi danas 11:23/11:40/11:56/17:57, cloud usage brojač raste |
| UCSS | 🟡 bilo mrtvo 11 dana (31.07→11.08), danas oživelo | Nov validan CSS fajl (46KB) u 17:57, verifikovan sadržaj |
| LQIP | 🔴 tiho zaglavljeno od 25.07 | 17 dana bez nove generacije uprkos novim proizvod-slikama (06–07.08) |
| VPI | ⚪ namerno isključen (`media-vpi`=prazno), nije pokvaren | Samo 1 fajl ikad (config marker) |
| Image Optimization (stari problem) | 🔴 nepromenjeno | Identično julskom tiketu: 1.157 RAW / 200 REQUESTED |

## Nalaz — LQIP uzrok

`placeholder.cls.php` (`_generate_placeholder()`) radi `File::is_404($url)`
proveru LOKALNO, PRE nego što se `curr_request`/cloud poziv uopšte desi. Ako
padne, slika se odmah trajno upisuje u `media-lqip_exc` (exclude listu) i
cloud se nikad ne kontaktira — zato cloud `last_request.lqip` ostaje
zamrznut na 25.07 iako se lokalno i dalje nešto "dešava" (odbija). Exclude
lista već sadrži slike sa datumima posle 25.07 (2026/01, 2026/03 upload
folderi) — dokaz da se ovo dešava na svežem sadržaju, ne stari zaostatak.
Ovo je **nov, drugačiji problem** od starog QUIC.cloud/firewall bloka
(image optimizacija) — lokalni bug u proveri postojanja slike, ne cloud
konekcija.

## Odluka

**Miroslav: LQIP fix se NE radi.** Nije prioritet — nije gate stavka, nema
merljiv uticaj na LCP gate (LQIP je kozmetički blur-up efekat, ne kritičan
render put). Nalaz ostaje dokumentovan za slučaj da neko kasnije pita "zašto
LQIP ne radi".

## Otvorene akcije
- [x] Status provera 4 funkcije #claude-code
- [x] Root-cause LQIP #claude-code
- ~~[ ] LQIP fix~~ — **eksplicitno odbijeno, ne raditi** (M, 2026-08-11)

## Beleške / odluke
- Redis Cache Manager nalaz (ista sesija) je u zasebnom fajlu, ne ovde.
- Nema izmena na buildu/bazi/kodu ove teme — čisto read-only istraživanje.

## Veze
- [[dnevnik/2026-08-11-litespeed-redis-web-cache-manager]]
- [[reference/naucene-lekcije]]
