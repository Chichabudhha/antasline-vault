---
tip: sesija
alat: claude-code
datum: 2026-08-13
blok: C
status: zavrseno
---

# Sesija — staging.antasline.com V4 puno postavljanje (ručni upload preko File Manager-a)

> Rad izveden **na cPanel serveru** (`[cpanel-live]`), ne na lokalnom buildu — v.
> [[migracija/promptovi/2026-08-13-staging-full-restore-v4.md]] za pun prompt i
> tabelu MD5 vrednosti. Klijent je gledao sajt **isto veče**, pa je gašenje GTM-a
> pre bilo kakvog javnog pristupa bilo tvrd preduslov, ne opcioni korak.

## Šta je urađeno

**KORAK 0 — zatečeno stanje:** subdomen `staging.antasline.com` postoji
(docroot `/home/antasline/staging`, potvrđeno `uapi DomainInfo`). FTP folder
(`/home/antasline/antasline.com/staging`) prazan (samo `.ftpquota`, poznat
mismatch od ranije). Baza `antasline_staging` postoji, ali nosila je zastareo
sadržaj (pre-05.08, pre-Rank Math migracije) — očekivano po prompt-u, obrisana
u KORAKU 5, ništa nije pokušano da se spase iz nje. Kvota: **3.807 MB slobodno
od 12.240 MB limita** — ispod „bar 6GB" preporuke iz KORAKA 0, ali iznad 1GB
hard-stop praga iz KORAKA 3; u praksi dovoljno, extrakcija uploads paketa
(2,8GB) prošla bez zastoja.

**🔴 Referentni fajl sa MD5 tabelom nije postojao lokalno u vault-u na početku
sesije.** `migracija/promptovi/2026-08-13-staging-full-restore-v4.md` je
commit-ovan na GitHub tek u toku sesije (`git log origin/main` pokazao je
commit-ove koje lokalni klon nema) — `git pull` ga je povukao zajedno sa još
26 fajlova. Bez njega KORAK 1 (poređenje MD5) nije bio izvodljiv. Rešeno
pull-om pre nastavka, ne zaobiđeno/pretpostavljeno.

**MD5 sve tri arhive/dump poklopile su se tačno** sa tabelom u v4 promptu
(`0f6c2dc3…` kod, `d55c7d9e…` uploads, `a0f169d4…` sql) — upload kroz cPanel
File Manager nije bio prekinut na pola (za razliku od poznatog rizika iz
prompta). Tar struktura OK (putanje bez prefiksa, direktno `wp-admin/` itd.),
raspakovano u `/home/antasline/staging`.

**🔴 `.htaccess`/`.htpasswd` NISU preživeli brisanje starog docroot-a.** Paket
ovaj put namerno ne nosi `.htaccess` (izbačen 13.08 zbog `RewriteBase
/antasline/` gotcha-e iz dry-run sesije — v.
[[dnevnik/2026-08-13-dry-run-build-staging-package]]) — ali to je značilo da
se ništa nije imalo šta ni vratiti/sačuvati posle raspakivanja koda. Staging
je bio kratko potpuno otvoren (bez Basic Auth) dok se ovo nije primetilo.
Zaštita vraćena ODMAH, pre nastavka na uploads/bazu:
- Novi `.htaccess` sa Basic Auth blokom upisan ručno (`AuthType Basic` /
  `AuthUserFile` / `Require valid-user`).
- `.htpasswd` napravljen preko `openssl passwd -apr1` — server **nema
  `htpasswd` binarku** (`command not found`), ovo je bio validan zamenski put.
- Nova nasumična lozinka generisana (stara nije bila poznata niti zapisana
  bilo gde) — saopštena Miroslavu direktno u chat-u, **namerno NE upisana u
  vault** (isto pravilo kao za DB lozinku, v. prompt KORAK 4).
- Potvrđeno pre nastavka: `curl` bez auth → 401, sa auth → prolazi.

**wp-config.php:** paket, za razliku od V3 (06.08), ne nosi ni
`wp-config-sample.php` — generisan preko `wp config create`. DB lozinka iz
`~/staging-db-credentials.txt` je ovaj put radila iz prve (bez potrebe za
reset, za razliku od 06.08 kad je bila „Access denied"). Prefiks pročitan
direktno iz dump-a (`grep CREATE TABLE`), potvrđen `wpgs_` malim slovima —
nije pretpostavljen napamet. Salt-ovi promešani (`wp config shuffle-salts`).

**Baza:** `wp db reset --yes` + `wp db import` (37,6 MB dump) prošlo bez
grešaka. `wp search-replace 'http://localhost/antasline' →
'https://staging.antasline.com' --all-tables --precise`: **14.316 zamena**
(poklapa se sa očekivanih „~14.000" iz prompta, blizu 06.08 broja 11.451 —
razlika objašnjena novijim/većim dump-om). `siteurl`/`home` oba potvrđena
`https://staging.antasline.com` posle `wp rewrite flush --hard`.

**GTM ugašen** (M odluka 13.08, tvrd preduslov zbog klijentskog pregleda iste
večeri — svaki njegov klik bi inače ušao kao stvaran podatak u pravi GA4/Ads
nalog, a merenje je i bez toga već naduvano, v. `--live-only` filter u
konektoru): `wp-content/mu-plugins/al-tracking-gtm-consent.php` →
`.php.off`. Potvrđeno `curl ... | grep -c "GTM-TRDT8K9"` = **0** na homepage.
Posledica: GTM Preview test na stagingu (čeka ga stavka 5.6 — `gallery_view`/
`pdf_download` DRAFT u Workspace-u od 22.07) i dalje NIJE moguć dok je
mu-plugin ugašen — vraća se `mv`-om nazad kad zatreba, bez ponovnog paketa.

**Cleanup (KORAK 9):** ni docroot ni FTP folder nisu zadržali tar/sql
ostatke transfera — provereno eksplicitno posle svakog koraka brisanja, ne
samo na kraju.

**KORAK 10 verifikacija — sve prošlo:**
- 401 bez auth / 200 sa auth na homepage
- 5 ključnih stranica (`/industrijski-podovi/`, `/katalog/`, `/kontakt/`,
  `/planer-terena/`, `/sportske-podloge/`) + 1 proizvod stranica
  (`/proizvod/goaliath-gb60-kos-za-kosarku/`) — svih 6 **200**
- 7 nasumičnih slika (2018×2, 2020, 2022, 2026/08×2, plus prvobitnih 5 iz
  `ORDER BY RAND()`) — svih **200**, uklj. namerno i stare datumske foldere i
  avgustovske (izbegnut isti razred greške koja je 06.08 propustila 82/108
  slomljenih slika)
- Namenska provera stranice 5438 (`/sportske-podloge/`, dump izvezen POSLE
  jutrošnje izmene te stranice — v.
  [[dnevnik/2026-08-13-5438-basket-semantika-faqpage]]): tačno 1×H1, ≥1
  pojava „Izgradnja sportskih terena za basket", tačno 1×FAQPage schema, ≥2
  linka na `/planer-terena/` — sve četiri prošle tačno kako je prompt
  očekivao

## Otvorene akcije

- [ ] #ceka-miroslav — nova `stagingtest` Basic Auth lozinka je saopštena
      samo u chat-u ove sesije, **nema zapisane kopije nigde** (namerno, po
      pravilu da se lozinke ne pišu u vault). Ako se izgubi, jedini put je
      generisati novu (`openssl passwd -apr1` → prepisati `.htpasswd` na
      serveru).
- [ ] #claude-code — favicon i dalje nepodešen (očekivano, dump ne nosi
      `site_icon` — nije novi bag, ne trošiti vreme na fix, isto kao 06.08).
- [ ] #claude-code — kad staging treba GTM Preview test za stavku 5.6
      (`gallery_view`/`pdf_download`), prvo vratiti mu-plugin: `mv
      wp-content/mu-plugins/al-tracking-gtm-consent.php.off
      wp-content/mu-plugins/al-tracking-gtm-consent.php`.

## Beleške / odluke

🔴 **Nova lekcija:** kad prompt referencira konkretan vault fajl (posebno
jedan sa brojevima/tabelom potrebnim za integritetnu proveru) koji naizgled
ne postoji — prvo proveriti `git fetch`/`git log origin/main` pre nego što se
proglasi blokerom. Tri-surface workflow (lokal → GitHub → cPanel, Obsidian
Git auto-sync na ~10 min) znači da fajl napravljen malo pre početka sesije
možda još nije stigao na granu koju cPanel klon vidi — ovde je stigao
doslovno usred sesije. Preneto u [[reference/naucene-lekcije]].

🔴 **Druga lekcija:** kad paket namerno izostavlja `.htaccess` da bi sačuvao
POSTOJEĆI serverski fajl (ispravna odluka za normalan slučaj), to
pretpostavlja da serverski fajl **postoji**. Ako je ceo docroot prethodno
obrisan (ovde: staging obrisan par dana ranije), ta pretpostavka pada tiho —
nema greške, samo prazan direktorijum posle raspakivanja koda. KORAK 2 u
promptu je ipak eksplicitno tražio proveru (`head -20 .htaccess`), pa je
uhvaćeno pre nego što je iko spolja mogao da pristupi otvorenom stagingu —
ali vredi zapamtiti obrazac za sledeći sličan „ne prepisuj postojeće" korak.

Server **nema `htpasswd` CLI binarku** — `openssl passwd -apr1
'<lozinka>'` daje kompatibilan Apache-ov hash, upisan ručno u
`user:hash` formatu.

## Veze

- Prompt (pun tekst, MD5 tabela): [[migracija/promptovi/2026-08-13-staging-full-restore-v4.md]]
- Prethodna verzija istog postupka (V3, 06.08): [[migracija/2026-08-06-prompt-staging-full-restore]]
- Dry-run koji je pripremio ovaj paket: [[dnevnik/2026-08-13-dry-run-build-staging-package]]
- Sadržaj dump-a uključuje jutrošnju izmenu: [[dnevnik/2026-08-13-5438-basket-semantika-faqpage]]
- [[reference/naucene-lekcije]]
