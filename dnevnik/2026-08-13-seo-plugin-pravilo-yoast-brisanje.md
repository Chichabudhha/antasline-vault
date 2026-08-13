---
tip: sesija
alat: claude-code
datum: 2026-08-13
blok: "-"
status: zavrseno
---

# Sesija — SEO plugin pravilo prepisano (Rank Math jedini) + Yoast obrisan sa builda

> Peta stavka dana 13.08. Pokrenuta na M zahtev („Izmeni ovo za yoast. On je obrisan
> rankmath ostaje"), zatvara konflikt **#1** iz
> [[migracija/2026-08-12-preflight-checklist-24-08]] §Konflikti.

## Šta je urađeno

### 1. Dokumentacija — pravilo prepisano u 7 fajlova (12 mesta)

Migracija Yoast→Rank Math izvedena je **05.08** ([[CLAUDE]] §7.1), ali je pravilo
„Yoast ostaje (ne RankMath)" ostalo zapisano kao **tvrdo pravilo** punih 8 dana —
uključujući dva skila koja se učitavaju na početku svake sesije.

| Fajl | Bilo | Sad |
|---|---|---|
| `odluke/_pregled-odluka` | „SEO plugin — Yoast (NE RankMath)" | „Rank Math (Yoast van upotrebe)"; stara odluka **prepisana, ne obrisana** — ostaje vidljiva kao zamenjena, sa razlogom i postupkom povratka |
| [[CLAUDE]] §7.1 | „Yoast deaktiviran (ne obrisan — podaci za rollback)" | 🔴 ograda: Rank Math jedini, fajlovi obrisani 13.08 |
| [[2026-07-06-MASTER-PLAN-V2]] | §„Pravila koja važe kroz ceo plan"; W2 zaglavlje „Yoast >80" | Rank Math; „Rank Math SEO score >80" |
| `/antasline-sesija` | ❌ „RankMath se ne pominje kao akcija" | ✅ Rank Math, ne pisati u `_yoast_wpseo_*`; + W2 pravila + verifikaciona stavka |
| `/obogati-proizvod` | tačka 5 (upis mete), gotcha blok, verifikacija, opis skila | `rank_math_title`/`rank_math_description`; dodata `is_protected_meta()` zamka i `\RankMath\Sitemap\Cache::invalidate_storage()` |
| `migracija/woodmart-sabloni` | helper `al_set_page()` opisan kao da piše **Yoast** metu | piše Rank Math metu |
| `reference/claude-skilovi`, `seo/plan-novih-stranica` | ista dva pravila | ispravljeno |

**Namerno nije dirano:** dnevnici, [[reference/naucene-lekcije]], W7 nalazi, analize —
tamo je Yoast tačan istorijski podatak o periodu kad je važio.

### 2. Nalaz: Yoast nije bio obrisan, samo deaktiviran

Provera protiv builda **pre** izmena dokumentacije:

```
seo-by-rank-math   active     1.0.275
wordpress-seo      inactive   27.8      ← folder 21 MB u wp-content/plugins/
```

Bez odluke bi 21 MB mrtvog plugina (zastarela verzija — aktuelna je 28.2) otišlo u
migracioni paket 24.08. M odluka istog dana: **„obriši, ali ostavi da može da se vrati."**

### 3. Brisanje (izvršeno)

1. Backup baze: `antasline-backups/antasline_local_2026-08-13_pre-yoast-brisanje.sql` (37,7 MB)
2. Arhiva plugina: `antasline-backups/yoast-wordpress-seo-27.8_2026-08-13.tar.gz` (4,0 MB) —
   integritet potvrđen **pre** brisanja: `tar -tzf` daje **2.308** unosa = 1.855 fajlova +
   453 foldera, tačno koliko `find` broji u folderu
3. `rm -rf wordpress-seo`
4. `_yoast_wpseo_*` postmeta (**690 redova**) namerno ostavljena — bez nje arhiva ne vredi

**Verifikacija posle brisanja:** `wp plugin list` → samo `seo-by-rank-math` 1.0.275 active ·
6 stranica (početna, `/kontakt/`, `/industrijski-podovi/`, Woo kategorija, proizvod,
conquest 2542) **200 / 1×H1 / `<meta name="description">` u `<head>` / 0 PHP grešaka** ·
na proizvodu i dalje 2 `application/ld+json` bloka · `sitemap_index.xml` 200 sa **7
child-ova** (nepromenjeno, parity sa live) · `rank_math_*` postmeta **16.312** redova.

## Otvorene akcije

- [ ] Dry-run `build-staging-package.sh` — exclude pravila dodata 10.08 (`al-local-mail-log.php`,
      `*.bak-*`) **nikad nisu izvršena**; poslednje pokretanje skripte je 06.08. Preflight rizici
      #1 i #4 (🔴🔴) oslanjaju se na to da skripta to izbacuje. Isti razred kao `live-export.sh`,
      koji je pri prvom stvarnom pokretanju (12.08) gubio 145/170 galerijskih slika. #claude-code
- [ ] Kopija Yoast arhive na drugu lokaciju (npr. `G:`) ako se želi — trenutno postoji samo na
      lokalnom disku, van git-a. #ceka-miroslav (nije obavezno)

## Beleške / odluke

- 🔴 **Brisano `rm -rf`-om, namerno ne `wp plugin delete`.** WP-CLI-jev `delete_plugins()`
  poziva uninstall rutinu plugina, koja sme da briše i podatke iz baze. Postmeta je jedini
  razlog zbog kog arhiva ima smisla — nije smela da se dira.
- 🔵 Arhiva i DB backup su u `antasline-backups/`, što je od 13.08 **van git-a** (`.gitignore`
  odluka istog dana) — postoje samo na lokalnom disku; noćni backup ih ne pokriva jer nisu
  u vault-u. Rollback izvor = lokalni disk.
- Praktičan efekat za 24.08: **21 MB manje** u migracionom paketu.

## Veze

- Odluka: [[odluke/_pregled-odluka]] §SEO plugin — Rank Math (sadrži i postupak povratka)
- Pravilo/kontekst: [[CLAUDE]] §7.1 · [[2026-07-06-MASTER-PLAN-V2]] §„Pravila"
- Konflikt koji zatvara: [[migracija/2026-08-12-preflight-checklist-24-08]] §Konflikti #1
- Ledger: [[DNEVNIK-NAPRETKA]] 2026-08-13 · stanje: [[PROGRESS]]
