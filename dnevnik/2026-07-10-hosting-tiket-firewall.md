# Tiket za hosting podršku (oblak.host) — nacrt

**Nalog:** antasline.com (cPanel, wp1.oblak.host)
**Problem:** Server firewall/Imunify360 blokira legitiman QUIC.cloud IP,
zbog čega LiteSpeed Cache image optimizacija ne radi.

---

## Šta se dešava

QUIC.cloud (CDN/optimizacioni servis vezan za LiteSpeed Cache plugin)
javlja grešku u svom dashboard-u:

> "Failed to notify WP to pick up optimized images. Unable to notify
> WordPress to pick up images from node 54.36.103.97. Check QUIC.cloud IPs
> are whitelisted at the firewall."

## Dokazi (prikupljeni danas, 2026-07-10)

1. **IP `54.36.103.97` JE zvanično važeći QUIC.cloud čvor** — potvrđeno
   direktnim proverom `https://quic.cloud/ips` (zvanična, uživo lista koju
   sam LiteSpeed Cache plugin koristi za validaciju). IP pripada OVH SAS
   (Francuska, Gravelines DC).
2. **Taj IP nikad nije ostavio nijedan trag u access logu** naloga
   (`~/access-logs/antasline.com-ssl_log` i prethodna rotacija do sredine
   juna) — ni jedan pokušaj konekcije. Ovo ukazuje da je zahtev blokiran
   PRE nego što je stigao do web servera (LiteSpeed/Apache) — dakle na
   nivou mrežnog firewall-a, ne WordPress/aplikacije.
3. **Za poređenje**: drugi QUIC.cloud IP-ovi sa iste zvanične liste, ali iz
   drugog opsega (Hetzner, Nemačka/Finska — `65.108.104.232`,
   `95.216.116.209`) **uspešno prolaze** — danas u 12:37 CEST su uspešno
   pozvali `notify_ccss`/`notify_ucss` rute (HTTP 200).
4. **Drugi konkretan primer, drugi provajder**: QUIC.cloud dashboard je
   prijavio i grešku "Failed to retrieve image
   .../Bumper_R30-100x100.jpg from node `185.53.57.89`" — taj IP je TAKOĐE
   na zvaničnoj QUIC.cloud listi (Krystal Hosting Ltd, Velika Britanija —
   **treći, potpuno drugačiji provajder od OVH-a**), slika stvarno postoji
   na disku i normalno se učitava kad se testira direktno (HTTP 200), ali
   ni ovaj IP nikad nije ostavio trag u access logu. Znači problem nije
   ograničen na jedan opseg (npr. samo OVH) — pogađa više različitih
   QUIC.cloud čvorova iz različitih provajdera, što jače ukazuje na
   IP-reputacioni firewall (Imunify360) nego na uzak, namenski blok
   jednog opsega.
5. Nalog ima **Imunify360 aktivan** (potvrđeno: `.imunify_patch_id`,
   `.myimunify_id` u home direktorijumu) — poznato je da Imunify360/slični
   serverski WAF-ovi po difoltu agresivnije tretiraju IP-ove sa
   lošijom/mešovitom reputacijom (deljeni cloud/hosting opsezi sa visokim
   udelom bot/scanner saobraćaja), što je verovatan uzrok selektivnog
   blokiranja više različitih QUIC.cloud čvorova.

## Molba

Možete li proveriti Imunify360 (ili drugi firewall na serverskom nivou,
van mog cPanel naloga) da li blokira IP-ove `54.36.103.97` i `185.53.57.89`
(potvrđeno različiti provajderi — OVH i Krystal Hosting — znači nije
jedan uzak opseg), i dodati ih na whitelist? U idealnom slučaju, cela
zvanična QUIC.cloud IP lista (`https://quic.cloud/ips`) bi trebalo da bude
izuzeta od automatskog blokiranja za ovaj nalog — to su poznati,
dokumentovani servisni čvorovi LiteSpeed/QUIC.cloud infrastrukture, ne
proizvoljni saobraćaj.

Bez ovoga, LiteSpeed image optimizacija (WebP konverzija) ostaje trajno
blokirana za sav novi sadržaj na sajtu.
