---
tip: sesija
alat: claude-code
datum: 2026-06-28
blok: C
status: u-toku
---

## Šta je urađeno

- Pronađen WooCommerce export SQL: `woo-export.sql` (441 KB)
- Pripremljene `scp` komande za prenos na `tvoj-lokal` server
- Testirano dostupnost alata: `scp` dostupan, `rsync` nije instaliran

## Otvorene akcije

- [ ] #ceka-miroslav — Pojašnjenje SSH konfiguracije za `tvoj-lokal` host
  - Koja je IP adresa ili hostname?
  - Koji SSH port?
  - Koji SSH ključ/lozinka?
- [ ] #ceka-miroslav — Ponovna pokušaj prenosa kada bude SSH konfiguracija jasna
- [ ] #ceka-miroslav — Provera `wp-content/uploads/` folder veličine pre prenosa

## Beleške / odluke

- **Problem:** `tvoj-lokal` nije pronađen kao SSH host — "Name or service not known"
  - Trebam da ide pojašnjenje: IP/hostname ili SSH config alias
  - Komande su spremne, samo čekaju na SSH konfiguraciju
- Alternativa: ako nema SSH pristupa, koristiti SFTP ili drugi metod

## Veze

- [[BLOK-C-sledece]] — WooCommerce migracija
- [[local_setup]] — lokalni setup koji se migrira
