---
tip: prompt-za-cpanel
datum: 2026-07-22
namena: minimalna GEO-ojacanje izmena na LIVE stranici /spoljnje-podne-obloge/ (terase bez lepljenja / Bergo klik-sistem)
status: ceka-eksplicitnu-potvrdu-M
---

# Prompt za cPanel Claude Code sesiju — GEO fix na live `/spoljnje-podne-obloge/` (Bergo terase gap)

> ⚠️ Za razliku od `[[migracija/2026-07-22-prompt-live-2542-geo-fix]]`, Miroslav
> JOŠ NIJE eksplicitno odobrio ovaj konkretan live edit u razgovoru. Pre
> izvršenja u cPanel sesiji, potvrdi sa Miroslavom da ovaj prompt treba da se
> pokrene (ili čekaj da on to eksplicitno kaže na početku te sesije).

## Kontekst (ne treba ponovo istraživati, samo primeniti)

Prvi mesečni AI test (2026-07-22, [[analiza/2026-07-22-ai-test-baseline]]) —
prompt 4: **"Podloge za terasu koje se ne lepe — gde kupiti u Beogradu?"**
AntasLine nije pomenut. AI je "ne lepe" protumačio isključivo kao WPC deking
(ELLAdeck, Deking Zona, Deking.rs) — Bergo klik-podloge (AntasLine-ov tačan
proizvod za ovaj upit) nisu prepoznate kao kategorija.

Lokalni build je već dobio isti fix na istoj temi (2026-07-22, ID 16590, +1 FAQ,
vidljiv tekst + FAQPage JSON-LD) — ovaj prompt je live pandan, isti princip kao
2542 GEO fix (AI indeksira LIVE, ne lokal — efekat lokalnog rada se ne vidi do
migracije + indeksiranja).

**Provereno na live 2026-07-22 (read-only, cPanel sesija koja je pisala ovaj
prompt):** `https://www.antasline.com/spoljnje-podne-obloge/` (200 OK) VEĆ ima
dobar sadržaj — "Podloga se postavlja direktno na ravnu i tvrdu površinu, nije
potrebno lepiti ili šrafiti. Ploče se vezuju jedna za drugu klik sistemom" — ali:
1. Ne koristi doslovnu frazu "bez lepljenja" / "koje se ne lepe" (query-matching
   fraza za AI).
2. Nema FAQ sekcije ni FAQPage JSON-LD na ovoj stranici (GEO citabilnost signal
   koji conquest/silo stranice na lokalu već koriste).

Stranica je page-builder tip (verovatno Kallyas/WPBakery, ne standardni WP post/
page CPT — nije pronađena preko `wp post list --post_type=page`, proveriti
stvarni CPT/builder pre izmene, npr. `wp post list --post_type=any --s="Spoljašnje podne obloge"`).

## Cilj (dve male dopune, ništa više)

1. U postojećem pasusu o montaži (ili najbližem prirodnom mestu) dodati/
   dopuniti rečenicu tako da sadrži doslovnu frazu **"bez lepljenja"** ili
   **"podloge koje se ne lepe"**, prirodno vezanu za već postojeći opis
   klik-sistema. Ne prepisivati ceo pasus — samo umetnuti frazu gde prirodno
   stane (npr. "...nije potrebno lepiti ili šrafiti — idealno rešenje za
   terase bez lepljenja — ploče se vezuju klik sistemom...").
2. Dodati JEDNO novo FAQ pitanje: **"Postoje li podloge za terasu koje se ne
   lepe?"** sa odgovorom u istom stilu/dužini kao ostale FAQ na sajtu (2-3
   rečenice), npr. u duhu:
   > "Da — Bergo podloge se postavljaju bez lepljenja i bez šrafljenja, direktno
   > na ravnu podlogu, a ploče se međusobno spajaju klik sistemom. To ih čini
   > idealnim za terase, bašte i balkone gde ne želite trajnu, lepljenu
   > instalaciju."
   Prilagodi formulaciju postojećem tonu stranice, ne kopiraj mehanički.

Ako stranica nema postojeći FAQ blok/JSON-LD — dodati samo vidljiv Q&A blok
(GEO cilj je citabilnost teksta, ne shema po svaku cenu); FAQPage JSON-LD samo
ako se uklapa bez rizika od dupliranja postojeće scheme.

## Koraci (izvršiti u cPanel terminalu, u `~/public_html`)

1. `cd ~/antasline-vault && git pull --no-edit` (standardni preduslov)
2. Nađi tačan post/stranicu i CPT:
   ```
   wp post list --post_type=any --s="Spoljašnje podne obloge" --fields=ID,post_type,post_title,post_name,post_status --allow-root
   ```
   Potvrdi ID i post_type pre bilo kakve izmene (ovo NIJE standardni `page` CPT
   po nalazu iz istraživačke sesije — verovatno Kallyas Zion/page-builder tip).
3. **Backup pre izmene** — samo ovaj post:
   ```
   wp post get <ID> --field=post_content --allow-root > ~/antasline-vault/migracija/backup-spoljnje-terase-live-pre-geo-fix-2026-07-22.html
   ```
4. Pročitaj pun sadržaj — utvrdi builder markup (Zion/WPBakery/Kallyas), gde je
   tačno pasus o montaži, da li postoji FAQ blok, da li postoji JSON-LD.
   🔴 **Poznat gotcha (Kallyas tema)**: `post_title` se već renderuje kao pravi
   `<h1>` — NE dodavati nikakav `<h1>` u sadržaj (2×H1 rizik, viđeno na
   `/politika-kolacica/` incidentu 2026-07-11).
5. Primeni dve dopune (1. i 2. iz sekcije "Cilj") preko fajla, NE inline
   `-r`/`--post_content` sa ugnježdenim navodnicima (poznat incident — obrisalo
   ceo sadržaj). Koristi privremeni HTML/PHP fajl + `wp post update`.
6. Verifikuj:
   ```
   curl -s -o /dev/null -w "%{http_code}\n" https://www.antasline.com/spoljnje-podne-obloge/
   curl -s https://www.antasline.com/spoljnje-podne-obloge/ | grep -o "<h1" | wc -l   # mora biti tačno 1
   curl -s https://www.antasline.com/spoljnje-podne-obloge/ | grep -io "bez lepljenja\|koje se ne lepe"
   ```
   Ako je dodat JSON-LD, validiraj (`json_decode`/`php -r`, ne pretpostaviti).
7. `wp litespeed-purge all --allow-root` (ili ekvivalent), pa ponovi curl proveru iz koraka 6.
8. Append u `[[DNEVNIK-NAPRETKA]]` na vrh (standardni cPanel protokol):
   ```
   ## 2026-07-22 [cpanel-live] [W2 GEO] — Live spoljnje-podne-obloge GEO fix (bez lepljenja / Bergo klik-sistem) ✅
   - Dodata fraza "bez lepljenja"/"koje se ne lepe" u postojeći pasus o montaži + 1 novo FAQ pitanje (izvor: AI test gap prompt 4, [[analiza/2026-07-22-ai-test-baseline]])
   - Backup: migracija/backup-spoljnje-terase-live-pre-geo-fix-2026-07-22.html
   - Verifikovano: 200 / 1×H1 / fraza prisutna / [JSON-LD ako menjan]
   ```
9. `git add -A && git commit -m "cpanel-live: GEO fix na spoljnje-podne-obloge (bez lepljenja fraza za AI vidljivost)" && git push`

## Šta NE raditi
- Ne restruktuirati stranicu, ne dirati title/meta (poseban zadatak, van obima).
- Ne dodavati novi `<h1>`/`<h2>` koji bi dupliralo postojeću strukturu.
- Ne diraj ništa drugo na sajtu osim ove jedne stranice.
- Ne izmišljaj brojeve/statistike/cene u novom FAQ odgovoru.
- Ako se struktura ne uklapa prirodno za dopunu — stani i prijavi umesto da
  forsiraš izmenu.

## Sledeći korak (posle ovoga)
Napomena u [[PROGRESS]] da je live deo Bergo/terase GEO fix-a zatvoren
(lokalni deo, ID 16590, već zatvoren istog dana u paralelnoj sesiji).
