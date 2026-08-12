---
tip: prompt
datum: 2026-08-12
tag: cpanel-live
namena: pre-flight provera infrastrukture pre migracije 24.08
status: spreman
---

# Prompt za Claude Code NA cPANEL-u — pre-flight infrastruktura (24.08)

> Nalepiti posle otvaranja `/antasline-sesija` u cPanel Claude Code-u.
> **Sve je read-only.** Nijedna stavka ne menja produkciju.

---

Radiš na ŽIVOJ produkciji (antasline.com). Ovo je **`[cpanel-live]` read-only
pre-flight provera** pred migraciju 2026-08-24 — ne menjaš ništa, samo meriš i
javljaš. Migracija je za 12 dana; cilj je da se problem koji bi pukao tog dana
otkrije danas.

## ⛔ Šta NE smeš (ni pod kojim izgovorom)

- Ne menjati nijedan fajl na sajtu, `.htaccess`, temu, plugin ni bazu
- Ne aktivirati/deaktivirati plugin, ne raditi Purge keša, ne raditi `wp search-replace`
- Ne dirati Redis iz terminala (`lsws redisAble` je blokiran — poznato, ne trošiti vreme)
- Ne pisati `.htaccess` 301 blok — on ide **isključivo na dan migracije**
- Ako neka provera traži izmenu da bi se izvela — **preskoči je i javi zašto**

## Šta treba izmeriti (redom)

### 1. 🔴 Prostor — hoće li paket od 24.08 uopšte stati
Migracija donosi `wp-content` ~1,3 GB **pored** postojećeg, plus backup arhivu.
- zauzeće i kvota diska (`quota -s`, `df -h ~`, ili cPanel `uapi Quota get_quota_info`)
- **broj inode-ova i inode kvota** — to je češći stvarni limit od GB-a
  (`uapi Quota get_quota_info`; `wp-content/uploads` ima desetine hiljada fajlova)
- javi: zauzeto / limit / **koliko ostaje slobodno** za sve tri stavke

### 2. 🔴 Kojim putem paket stiže na server
FTP push je već jednom pukao usred prenosa od 3,18 GB (rizik #5 pre-flight liste),
a Miroslav **nema SSH** — dakle `rsync`/`scp` sa njegove mašine ne dolaze u obzir.
Proveri da li je moguć **pull sa servera** (mnogo pouzdaniji od push-a):
- koji alati postoje: `which curl wget rsync tar unzip split md5sum`
- radi li odlazni HTTPS sa servera: `curl -sI https://github.com | head -1`
- ako radi → preporuči **pull sa URL-a** kao primarni put za 24.08 (paket se
  okači negde, server ga povuče, `md5sum` provera), a File Manager kao rezervu
- javi tačno šta je dostupno, bez pretpostavki

### 3. 🔴 JetBackup — je li rezerva stvarno tu
- datum/vreme **poslednjeg automatskog snapshot-a** (mora biti mlađi od 24h)
- retencija i da li je off-site (očekivano: dnevni, off-site, 90 dana)
- ako iz shell-a nije dostupno (`jetbackup5api` ume da bude zabranjen korisniku) —
  **nemoj nagađati**, napiši „Nema podataka iz shell-a, Miroslav proverava u cPanel UI"

### 4. 🟡 Speculative Loading — potvrditi da je prefetch, ne prerender
Već izmereno spolja 2026-08-12: live emituje
`{"prefetch": … "eagerness":"conservative"}` — WP core, **bezopasno**
(prefetch ne izvršava JS, pa `generate_lead` ne okida). Treba potvrditi izvor:
- `wp core version` (core Speculative Loading je od 6.8)
- `wp plugin list --status=active | grep -i -E "specul|prefetch|instant"` —
  ako postoji takav plugin, **on ima UI za prebacivanje na `prerender`**
- javi ime i status ako postoji; **ne diraj podešavanje**

> Zašto je važno: `/hvala-za-poruku/` page view je naša jedina prava konverzija.
> Prerender bi je okinuo na posetu koja se nije desila. Prefetch neće.

### 5. 🟡 LiteSpeed — stanje, bez ijedne izmene
- `wp option get litespeed.conf.optm-css_async` / `…ccss_gen` / `…ucss` (ili
  `wp litespeed-option get` ako postoji) — javi stanje CCSS/UCSS
- postoji li „Instant Click" (link preload na hover) i je li uključen
- 🟢 Već potvrđeno spolja: `<head>` ima **1 stylesheet** i to je UCSS,
  plus `CCSS loaded ✅` → sloj radi. Ovde samo beležimo podešavanja.

### 6. 🟡 Higijena docroot-a (isti razred bagova koji je pao na buildu)
Read-only provera da produkcija **već sad** nije izložena:
- `find ~/public_html -maxdepth 3 \( -name "*.bak*" -o -name "*.orig" -o -name "*.old" -o -name "mail-log.txt" -o -name "al-harness.html" \) | head -40`
- ako nešto postoji — **samo javi spisak**, ne briši (odluka je Miroslavljeva)

### 7. 🟢 Okruženje
- `php -v`, `wp --version`, `wp db size --human-readable`
- PHP `memory_limit`, `max_execution_time`, `upload_max_filesize`

## Kako da izvestiš

Kratko, tabelarno, brojevi. Za svaku stavku: **izmerena vrednost** + 🟢/🟡/🔴
ocena da li ugrožava 24.08. Gde podatak nije dostupan — doslovno
**„Nema podataka"**, nikad procena.

Na kraju: jedna rečenica — **„Najveći rizik za 24.08: …"**.

## Zatvaranje (obavezno)

1. `cd ~/antasline-vault && git pull --no-edit`
2. Dopiši **na kraj** [[DNEVNIK-NAPRETKA]]:
   `## 2026-08-12 [cpanel-live] — pre-flight infrastruktura (UŽIVO, read-only)`
   sa izmerenim brojevima i eksplicitnom rečenicom da **ništa nije menjano** na produkciji
3. Ako je nalaz menja stanje → dopuni [[PROGRESS]] Blokere i
   [[migracija/2026-08-12-preflight-checklist-24-08]]
4. `git add -A && git commit -m "cpanel-live: pre-flight infrastruktura 24.08" && git push`

Koristi Write/Edit alat za pisanje u vault — bash heredoc puca na ~965 B.
