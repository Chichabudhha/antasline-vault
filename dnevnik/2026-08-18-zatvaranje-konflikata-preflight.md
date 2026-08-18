---
tip: dnevnik-sesija
datum: 2026-08-18
tag: claude-code
oblast: W3
status: zatvoreno
---

# Sesija — Zatvaranje 6 konflikata u migracionoj dokumentaciji + provera menija 17424

## Šta je urađeno

Zatvoreni **svi preostali konflikti** iz `migracija/2026-08-12-preflight-checklist-24-08.md`
§„Konflikti u dokumentaciji" (bio zatvoren samo #1). Svaki je pre zatvaranja proveren
protiv **stvarnog stanja** — koda, baze ili grep-a — ne protiv druge beleške.

| # | Tema | Nalaz | Akcija |
|---|---|---|---|
| 2 | Datum go-live | `w1-novi-proizvodi-court-builder.md` je bio jedini **živi (nedatiran)** dokument sa 31.08. Usput: gate-napomena „CB3 mora ≥2 nedelje pre go-live" je bespredmetna — CB3 zatvoren **11.07**. | Ispravljeno na **25.08** + CB3 označen ✅ |
| 3 | Prefiks baze | `wpGs_` više **ne postoji ni u jednoj izvršnoj putanji** — skripte sređene 12.08, `wp-config` + 16 ključeva u bazi 14.08, 13 dokumenata istog dana. `PARITY-PLAN.md` i `2026-07-05-live-export-prompt.md` (navedeni kao sporni) su **već čisti**. Ostatak pogodaka = dnevnici. | Obeleženo rešenim, bez izmena |
| 4 | Sloj 301 | **Lažni pozitiv.** Zabuna dolazi od *naslova* fajla `2026-07-21-analiza-65-redirection-pravila.md`; sam tekst (linije 28/97/107/110) već kaže da su sva pravila spljoštena u `.htaccess` i da se plugin „sam po sebi ne mora migrirati". Izvori se slažu. | Obeleženo kao nije-konflikt |
| 5 | `mu-plugins` prenos | **Pravi konflikt.** Zaglavlje `al-local-mail-log.php` tvrdilo „mu-plugins se ne prenose" — netačno (`mu-plugins` je unutar `wp-content`), i baš to je **07.08 oborilo sve mejlove na produkciji**. | Komentar prepisan; verifikovano da exclude postoji (`build-staging-package.sh:92-93`) i da stavka B2 stoji u checklisti |
| 6 | Zbir konverzija | `odluke/_pregled-odluka.md` zatvorio 4.8 još 13.08, ali je **`ADS-DNEVNIK.md` na vrhu ćutao** — ispravka je živela samo u Log unosu od 12.08, ispod ~40 redova starijih „prag pređen" tvrdnji. | Dodat `[!warning]` blok odmah ispod zaglavlja hub-a: **9 pravih lidova, ne 26** |

Uz to ažurirana dva reda u „Tabeli rizika" istog fajla (#1 mail-log komentar, #9 prefiks).

## Quick-win — meni stavka 17424: 🟢 LAŽNA UZBUNA, nula izmena na buildu

[[PROGRESS]] je vodio „meni stavka **17424** nema naslov (prazan red u „Cene" segmentu)".
Provereno u bazi i u renderu:

- `wpgs_posts.post_title` **jeste** prazan za 17424 — ali i za **još 8** stavki
  (17361–17363, 17381, 17394–17396, 17399). Sve su `_menu_item_type=post_type`.
- Kod te vrste stavki WordPress **pada na naslov povezane stranice**, pa render nije prazan.
- Render potvrđen `curl`-om: `17424` → `<a href="…/podovi-za-garaze/">Podovi za garaže</a>`,
  susedni `17423` → „Industrijski podovi" (`/industrijski-podovi-cena/`).
- Sva tri URL-a „Cene" segmenta vraćaju **200**.

**Zaključak: nema šta da se popravi.** Prazan `post_title` na `post_type` stavkama je
normalno WP stanje, ne bag. Stavka se skida iz reda čekanja. Build **nije diran** —
što je i najbolji ishod četiri dana pred gate.

## Beleške / odluke

- **Datirani vs. nedatirani dokumenti.** Preostali `31.08` pogoci žive u datiranim
  sesijskim planovima/promptovima (07-21, 07-22, 07-27, 08-06). Ti fajlovi su
  **istorijski zapisi** i namerno se ne prepravljaju. Konflikt #2 je i nastao baš zato
  što je jedan **živi** dokument (`w1-novi-proizvodi-court-builder.md`, bez datuma u imenu)
  nosio zastarelu tvrdnju. → **Pravilo: zastarele rokove ispravljati u živim dokumentima,
  datirane ostaviti kakvi jesu.**
- **Ispravka na dnu loga se ne vidi.** Konflikt #6 je preživeo 6 dana iako je ispravka
  bila uredno upisana — samo je bila u append-only logu ispod desetina starijih tvrdnji.
  Kad ispravka poništava **stanje**, a ne samo jedan unos, mora i na **vrh** dokumenta.

## Otvorene akcije

- [ ] 🟡 `/padel-tenis/` sukob (#3 iz analize 65 pravila) — jedina prava otvorena tačka
      tog fajla; fallback je automatski, ne blokira migraciju. #claude-code
- [ ] `Klik na telefon (web)` → *Secondary action* u Ads UI — uslov za ponovno
      otvaranje odluke 4.8 #ceka-miroslav

## Veze
- [[migracija/2026-08-12-preflight-checklist-24-08]] · [[migracija/2026-08-10-pre-migration-checklist]]
- [[dnevnik/ADS-DNEVNIK]] · [[odluke/_pregled-odluka]] · [[migracija/w1-novi-proizvodi-court-builder]]
