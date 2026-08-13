---
datum: 2026-08-13
tag: claude-code
oblast: W3 3.10 — migraciona priprema
status: zatvoreno
---

# Dry-run `build-staging-package.sh` — 2 skrivena kvara + kvota ne staje

Skripta je poslednji put pokrenuta **06.08**, a **10.08** su joj dodata dva exclude
pravila (`mu-plugins/al-local-mail-log.php`, `*.bak-*`/`*.orig`/`*.old`/`*~`) koja
**nikad nisu izvršena**. Preflight rizici **#1 i #4 (🔴🔴)** oslanjaju se baš na njih.
Isti razred kao `live-export.sh`, koji je pri prvom stvarnom pokretanju (12.08) gubio
145/170 galerijskih slika.

Read-only prema buildu i bazi — nijedna izmena na `C:\xampp\htdocs\antasline`.
Paketi napravljeni u scratchpad-u, obrisani po završetku.

## Šta je urađeno

### 1. Skripta se nije mogla ni pokrenuti za test
`WP_ROOT`/`OUT_DIR` su bili **hardkodirani**, pa se dry-run nije mogao pustiti van
produkcione izlazne fascikle — tačan razlog zašto skripta nikad nije testirana posle
izmena od 10.08. Popravljeno istim obrascem kao `PFX`/`OUT` u `live-export.sh`
(12.08): sada pregazivi preko okruženja, podrazumevane vrednosti nepromenjene.

### 2. Exclude pravila od 10.08 — RADE ✅ (rizici #1 i #4 zatvoreni)
Pun `full` prolaz, pa `tar -tzf` nad arhivom (22.936 unosa):

| Provera | Na buildu postoji | U paketu |
|---|---|---|
| `mu-plugins/al-local-mail-log.php` | da | ❌ nema ✅ |
| `wp-content/mail-log.txt` (15 KB) | da | ❌ nema ✅ |
| `*.bak-*` / `.orig` / `.old` / `~` u `wp-content` | **32** (checklist beleži 27 od 10.08 — porastao za 5 iz današnje FAZE 2 i Aria fiksa) | ❌ nijedan ✅ |
| `al-harness.html`, `harness390.html` | da | ❌ nema ✅ |
| ~20 debug/import PHP skripti u docroot-u | da | ❌ nema ✅ |
| `wp-config.php`, `wp-config – kopija.php` | da | ❌ nema ✅ |
| `wp-content/uploads`, `.git/`, `.claude/`, `*.sql` | da | 0 unosa ✅ |
| Yoast plugin (obrisan danas) | ne | ❌ nema ✅ |

Pozitivna strana: 2 teme (`woodmart` + `woodmart-child`), 10 plugina, tačno 2 ispravna
mu-plugina (`al-a11y-blog-archive-h2`, `al-tracking-gtm-consent`), 16 root fajlova.

Bezopasni ostaci, ne diraju se: 40 `wordpress-seo-*.mo/.po` prevoda u
`wp-content/languages/plugins` + 1 WoodMart admin sličica (sam plugin je van paketa);
`woodmart-core/vendor/opauth/opauth/example/.htaccess` (deo plugina kakav se isporučuje).

### 3. 🔴 `.htaccess` je bio u paketu — oborio bi produkciju
Root whitelist je uključivala `.htaccess`, a lokalni nosi:

```
RewriteBase /antasline/
RewriteRule . /antasline/index.php [L]
RedirectMatch 301 "^/antasline/vestacka-trava/?$" /antasline/spoljnje-podne-obloge/…
```

Build je u podfolderu; na produkciji bi **svaki zahtev** otišao na nepostojeći
`/antasline/index.php` — sajt pada u celosti. Uz to bi nestao produkcijski
`# BEGIN LSCACHE` blok (LiteSpeed kеširanje, na kome visi ceo LCP plan).
Checklist **B3** ionako kaže da se 301 blok **dodaje** u postojeći serverski
`.htaccess` iznad `# BEGIN WordPress` — dakle fajl se na serveru **edituje**, nikad
ne prenosi iz builda. Izbačen iz whitelist-e, sa objašnjenjem u kodu.

### 4. Chunk + md5 korak — ispravan ✅
136 delova (uploads) + 4 (kod), `md5sum -c` prolazi 4/4, a rekonstrukcija
`cat part-* > tar` daje **bajt-identičan** md5 originalu.

### 5. 🔴 Paket je 2× veći nego što pre-flight pretpostavlja — kvota ne staje

| | Veličina |
|---|---|
| `antasline-wp-code-2026-08-13.tar.gz` | **72,3 MB** |
| `antasline-uploads-2026-08-13.tar.gz` | **2.706,9 MB** |
| Ukupno tar-ovi | **2.779,2 MB** |
| Pik na serveru ako se delovi sklope *pored* delova | **5.558,5 MB** |

Uploads na buildu je **2,9 GB** i praktično se ne kompresuje (slike su već
kompresovane). Disk-bloker je 13.08 zatvoren računicom „~1,3 GB paket + ~1,3 GB
backup ≈ 2,6 GB" — **stvarni paket je 2,7 GB sam za sebe**.

Slobodno na serveru: **5.867 MB** (kvota 12.240, iskorišćeno 6.373).

- **Naivan tok (delovi + sklopljen tar istovremeno):** 5.558 MB → ostaje 309 MB,
  pre svežeg backup-a (~1.310 MB) i pre raspakivanja → **ne staje**.
- **Disciplinovan tok:** backup napravljen → skinut → obrisan sa servera (0) →
  delovi 2.779 MB → raspakivanje uz progresivno brisanje delova → neto rast
  uploads-a ~+1,6 GB. Pik ≈ **4,4 GB < 5,87 GB** ✅.

Preporuka za 24.08, po redosledu:
1. **rsync/scp preko SSH-a** (pristup potvrđen M6, 21.07) — bez chunkovanja, bez
   sklapanja, najmanji pik. FTP chunking je bio zaobilaznica za nestabilnu
   data-konekciju, ne zahtev hostinga.
2. Ako ipak FTP: **ne sklapati tar pored delova** — streamovati
   `cat part-* | tar -xzf - -C …` i brisati delove u hodu.
3. Backup skinuti i obrisati sa servera **pre** početka uploada, ne posle.

## Otvorene akcije

- 🔴 **Disk prostor se reotvara kao rizik** — nije „potpuno zatvoren" kako je
  upisano 13.08 ujutru; brojka na kojoj je zatvoren (~1,3 GB paket) je 2× manja od
  stvarne. Ne blokira gate, ali diktira **redosled koraka** na dan migracije.
- 🟡 **`ftp-upload-chunks.sh` nosi FTP lozinku u čistom tekstu** (linija 8,
  `staging@antasline.com`) i verzionisana je u git-u vault-a. Vault je privatan, ali
  kredencijal ostaje u istoriji zauvek. Predlog: izmestiti u fajl van vault-a (isti
  princip kao `~/staging-htaccess-creds.txt` na serveru, 21.07) + promeniti lozinku
  posle migracije. #ceka-miroslav
- 🔵 `C:\xampp\htdocs\antasline-staging-upload\` drži **5,67 GB** zastarelih
  artefakata od 06.08 (tar + 136 delova). Lokalni disk je na 91% (44 GB slobodno) —
  nije hitno, ali može da se obriše.
- 🔵 Checklist B2 kaže „27 fajlova" — sada ih je 32. Broj raste svakom sesijom;
  provera treba da bude `find`, ne fiksan broj (skripta ionako radi po obrascu).

## Beleške / odluke

🔴 **Nova lekcija: ne editovati `.sh` dok se izvršava.** Prvi prolaz je pukao na
`ploads: command not found` — bash čita skriptu **inkrementalno po bajt-ofsetu**, pa
izmena u letu pomeri ostatak fajla i raspolovi komandu (`antasline-uploads-…`).
Nije bio kvar skripte nego moja izmena tokom rada. Pogoršava to što je proces
**izašao sa kodom 0** uprkos `set -euo pipefail`, pa bi u automatizovanom lancu
prošao kao uspeh. Drugi, čist prolaz je prošao end-to-end.

## Veze

- [[migracija/2026-08-10-pre-migration-checklist]] — B1 (paket), B2 (šta ne sme), B3 (`.htaccess` se edituje na serveru)
- [[migracija/2026-08-12-preflight-checklist-24-08]] — rizici #1 i #4
- [[dnevnik/2026-08-12-live-export-galerije-prefiks]] — isti razred baga u `live-export.sh`
- [[reference/naucene-lekcije]]
