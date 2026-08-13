---
tip: dnevnik
alat: claude-code
datum: 2026-08-13
blok: C
status: zavrseno
---

# Sesija — LiteSpeed prefetch/Instant Click provera (uživo)

> Treća `[cpanel-live]` stavka istog dana (posle disk kvota potvrde,
> v. [[DNEVNIK-NAPRETKA]] 2026-08-13). Zatvara otvoren rizik iz
> [[reference/chrome-web-platform-2026]] §3: "Isto važi za bilo koji
> prefetch/prerender koji bi neko uključio kroz LiteSpeed/optimizacioni
> plugin na produkciji — proveriti pre migracije 24.08."

## Šta je urađeno

Provereno direktno na `wp1.oblak.host` (`~/public_html`), read-only:
`wp option list` za LiteSpeed config, `curl` na live homepage da se potvrdi
šta se stvarno renderuje (ne samo šta config kaže), i pregled izvornog koda
`instant_click.min.js` (LiteSpeed Cache 7.8.1) da se utvrdi TAČAN mehanizam,
ne samo da li je funkcija uključena.

| Podešavanje | Vrednost | Izvor |
|---|---|---|
| `litespeed.conf.util-instant_click` | `1` (uključeno) | `wp option list` |
| `litespeed.conf.optm-dns_prefetch_ctrl` | `1` (auto) | `wp option list` |
| `litespeed.conf.optm-dns_prefetch` / `optm-dns_preconnect` | oba prazan niz | `wp option list` |
| `instant_click.min.js` na live stranici | učitava se stvarno | `curl -sL https://www.antasline.com/` |

## Nalaz — mehanizam (ključni deo)

`instant_click.min.js` podržava native **Speculation Rules API**
(`HTMLScriptElement.supports("speculationrules")`) i grana na
`type="prerender"` **isključivo** ako `document.body.dataset.instantSpecrules
=== "prerender"`. Taj atribut je proveren i **ne postoji nigde** — ni u
`<body>` tagu na live-u (homepage i 404 stranica provereni), ni u LiteSpeed
config-u (7.8.1 admin UI uopšte ne izlaže ovu opciju). Default grana (kad
atribut nedostaje): `_speculationRulesType = "prefetch"`.

**Zašto je razlika bitna**: `prefetch` preko Speculation Rules API-ja
dovlači HTML odredišne stranice u pozadini ali **ne izvršava JS** te
stranice. `prerender` bi je izvršio — uključujući GTM tagove — čim korisnik
pređe mišem preko linka, pre stvarne posete. Naša jedina prava konverzija
je *page view* na `/hvala-za-poruku/` ([[CLAUDE]] §4, BLOK A); da je bio
`prerender`, hover nad bilo kojim linkom ka toj stranici bi lažno okinuo
`generate_lead` bez stvarne posete — tačno inflacija koju je BLOK A
čišćenje ispravljalo. Potvrđeno da to NIJE slučaj, iz izvornog koda, ne
pretpostavke.

## Sporedan nalaz (bez rizika, nije dirano)

DNS Prefetch Control (auto) na homepage emituje samo 1 `dns-prefetch` tag
(`fonts.googleapis.com` — WP core default `wp_resource_hints()`, ne
LiteSpeed-ov doprinos). `googletagmanager.com` (učitan na svakoj stranici)
nema dns-prefetch/preconnect hint jer GTM snippet ubacuje domen kroz inline
JS string, ne kroz literalan `<script src=...>` u sirovom HTML-u — statički
skener ga ne vidi. Sitna moguća optimizacija (ručno dodati domen u
`optm-dns_preconnect` listu), van obima ove provere, nije urađena.

## Otvorene akcije
- [x] Provera da li je neki prefetch/prerender plugin/podešavanje aktivno na live-u #claude-code
- [x] Utvrditi tačan mehanizam (prefetch vs. prerender), ne samo uključeno/isključeno #claude-code
- [x] Ažurirati [[reference/chrome-web-platform-2026]] §3 sa nalazom #claude-code

## Beleške / odluke
- Nema izmena na buildu/bazi/config-u — čisto read-only istraživanje
  (`wp option list`, `curl`), nema `wp option update`.
- **Uslov za ponovno otvaranje**: ako se ikad ručno doda
  `data-instant-specrules="prerender"` u temu, ili LiteSpeed izloži tu
  opciju u budućem UI-ju — primeniti pravilo iz [[reference/chrome-web-platform-2026]]
  §3 (GTM trigger uslovljen na `document.prerendering === false` PRE
  uključivanja `prerender` moda).

## Veze
- [[DNEVNIK-NAPRETKA]] 2026-08-13
- [[reference/chrome-web-platform-2026]] §3
- [[reference/naucene-lekcije]]
