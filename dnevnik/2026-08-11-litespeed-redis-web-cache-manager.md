---
tip: dnevnik
oblast: W3-tehnicka / CWV
tag: cpanel-live
datum: 2026-08-11
status: istraženo, odluka doneta (ne raditi sada)
---

# LiteSpeed Web/Redis Cache Manager — nove cPanel opcije istražene, NE rešavaju stari QUIC.cloud problem

**Kontekst:** Miroslav primetio dve nove stavke u cPanel-u — "LiteSpeed Redis Cache Manager" i
"LiteSpeed Web Cache Manager" — i pitao da li rešavaju stari poznati problem (QUIC.cloud
notify_img/UCSS blokiran hosting firewall-om, v. [[dnevnik/2026-07-10-hosting-tiket-firewall]],
PROGRESS 2026-07-30 unos).

## Nalaz 1 — poreklo i obim oba modula

Pravi izvorni kod plugina pročitan direktno na serveru (ne dokumentacija):
`/usr/local/cpanel/base/frontend/jupiter/ls_web_cache_manager/` — zvaničan LiteSpeed
Technologies "LSCM" plugin v2.4.9.1, instaliran na hostu **2026-08-07** (otud "nove opcije").
Oba cPanel meni-unosa (Web Cache Manager, Redis Cache Manager) su isti aplikacioni kod, samo
različit `redisOnly` mod.

- **Web Cache Manager**: skenira WP instalacije naloga, upravlja LSCache (page cache)
  direktorijumom, SSL sertifikati za QUIC.cloud CDN feature. Grep celog `core/` stabla za
  `notify_img`/`imageoptm`/`ucss`/`ccss`/`critical css` → **0 pogodaka**. Ne dodiruje image
  optimizaciju ni Critical CSS/UCSS — to ostaje isključivo unutar WP LiteSpeed Cache plugina i
  njegovog QUIC.cloud notify kanala, koji je i dalje blokiran istim hosting firewall-om.
- **Redis Cache Manager**: potpuno odvojena funkcija — uključuje/isključuje "caged" Redis servis
  (object cache za DB/PHP upite) preko `uapi lsws redisAble`. Nema nikakve veze sa render-blocking
  CSS ili image optimizacijom.

## Nalaz 2 — pokušaj uključivanja Redis-a, blokirano na permisijama (ne na nama)

Isti uapi poziv koji UI dugme šalje (`uapi lsws redisAble action=enablesvc user=antasline size=64`)
vraćen sa:

```
Parent check method: /usr/local/cpanel/cpanel, caller: /usr/local/cpanel/uapi is not allowed
```

`REDIS_ABLE` i `PACKAGE_USER_SIZE` (koji određuje dozvoljenu Redis kvotu) su privilegovani
`lswsAdminBin` pozivi koje cPanel prihvata SAMO iz prave `cpsrvd` browser-sesije, ne sa
terminala/uapi CLI-ja — namerna bezbednosna zaštita, nije zaobiđena (deljeni hosting, tuđa
kontrola, nije naše da probijamo).

Dodatno: nigde na disku ne postoji `redis.size` fajl (`/tmp/redis/`, `~/.cagefs/tmp/redis/`) —
nalog nema dodeljenu Redis kvotu na paketu. UI kod ima tačno ovu poruku za taj slučaj: *"Redis
must be configured for you by your administrator."* Znači: čak i klik na pravo dugme u browseru
(Miroslav) će verovatno pokazati istu poruku — treba tiket hostingu da se prvo dodeli Redis paket.

## Odluka — Redis se NE traži od hostinga sada

**Zašto ne vredi truda pre 24.08:**
1. Redis (object cache) pomaže DB/PHP izvršavanje kod dinamičkih/ulogovanih zahteva. Otkad je
   katalog mod (M9) uklonio cart/checkout, skoro sav saobraćaj je anoniman/statičan — LSCache
   page cache već servira ceo HTML bez ikakvog PHP/DB izvršavanja za taj saobraćaj. Redis tu nema
   šta da ubrza.
2. TTFB nije glavni LCP krivac — [[dnevnik/PERFORMANCE-AUDIT]] `lcp-breakdown-insight`: TTFB ~860ms
   od ~15s simulirano. Pravi krivac je render-blocking CSS (`js_composer` 437KB), već u toku
   rešavanja preko Critical CSS/UCSS (uključeno 2026-08-07, čeka brojčanu Lighthouse potvrdu).
3. Isti hosting je već jednom (QUIC.cloud firewall tiket, 07-10→07-30) držao tiket otvoren 3
   nedelje pre odgovora. Content freeze 16.08, gate pregled 21.08, go-live 24.08 — nema margine
   da se čeka na eksternu podršku za nešto što ionako ne dira poznati crveni gate item (LCP/CSS).

**Kad revizitovati:** posle 24.08, ako brojčana LCP potvrda pokaže spor TTFB i na keširanim
stranicama, ili ako se doda dinamički sadržaj (login, personalizacija) koji bi realno koristio
object cache.

**Bez izmena na buildu/bazi/live sajtu ove sesije** — čisto istraživanje (read-only pregled
servera + jedan probni, neuspešan/blokiran uapi poziv koji ništa nije promenio).
