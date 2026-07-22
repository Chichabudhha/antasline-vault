---
tip: prompt-za-cpanel
datum: 2026-07-22
namena: minimalna GEO-ojacanje izmena na LIVE conquest clanku 2542 (epoksid alternativa)
status: spreman-za-izvrsenje
---

# Prompt za cPanel Claude Code sesiju — GEO fix na live 2542 (epoksid conquest)

> Miroslav je eksplicitno odobrio ovaj live edit (2026-07-22 razgovor) — ovo JE
> taj eksplicitni [cpanel-live] zadatak koji dozvoljava izuzetak od pravila
> "live se ne dira". Obim je namerno malen i nizak-rizik: dve rečenice teksta,
> ne strukturna izmena.

## Kontekst (ne treba ponovo istraživati, samo primeniti)

Prvi mesečni AI test (2026-07-22, [[analiza/2026-07-22-ai-test-baseline]]) je
testirao 5 fiksnih promptova u ChatGPT-u. Prompt 3 — **"Alternativa epoksidnom
podu za proizvodnu halu"** — AntasLine nije pomenut, i AI uopšte ne zna da
modularne PVC/interlocking ploče (Ecotile kategorija) postoje kao alternativa
epoksidu. Ovo je tačno tema conquest članka `/epoksidni-podovi-ili-ecotile-podovi/`
(lokalni ID 2542, live je ista URL putanja) — ali GSC pozicija za "epoksi
podovi" je poz. 26, van AI vidokruga, i članak nigde ne koristi doslovnu frazu
"alternativa epoksidnom podu" niti reč "hala" — pa AI nema tekstualni signal
za poklapanje čak i kad bi crawlovao stranicu.

**AI (ChatGPT Search i sl.) indeksira LIVE antasline.com, ne lokalni build.**
Lokalni build je već ojačan istim izmenama (2026-07-22, ista sesija) — ali
efekat na AI vidljivost tog rada se ne vidi do migracije (2026-08-31) + par
nedelja indeksiranja. Ovaj live edit postoji da GEO "sat" počne da tiče ranije.

**Live i lokalni sadržaj 2542 VEROVATNO NISU identični** — lokalni je prošao
SEO refresh 2026-07-08 (title/meta/FAQ struktura), live možda nije. **Ne
pretpostavljaj live strukturu — pročitaj je prvo, pa se adaptiraj.**

## Cilj (dve male dopune, ništa više)

1. U uvodnom/lead pasusu (ili najbližem prirodnom mestu) dodati rečenicu koja
   sadrži doslovnu frazu **"alternativa epoksidnom podu za proizvodnu halu"**,
   povezanu sa Ecotile/modularnim PVC pločama (pomeni i reč "interlocking" ili
   "klik-sistem" ako prirodno stane).
2. Dodati JEDNO novo FAQ pitanje: **"Koja je alternativa epoksidnom podu za
   proizvodnu halu?"** sa odgovorom u istom stilu/dužini kao postojeća FAQ
   pitanja na toj stranici (2-3 rečenice), npr. u duhu:
   > "Glavna alternativa epoksidnom podu za proizvodnu halu su modularne PVC
   > ploče sistema interlocking (klik-sistem), kao što je Ecotile — postavljaju
   > se bez lepka i bez brušenja betona, direktno preko postojeće podloge, oko
   > mašina i regala, dok pogon ostaje u funkciji."
   Prilagodi formulaciju postojećem tonu stranice, ne kopiraj mehanički.

Ako stranica ima FAQPage JSON-LD (script tag, ili preko Yoast/plugina) —
dodaj isto pitanje i tamo, tačno kao vidljivi FAQ tekst (bez izmišljanja).
Ako nema JSON-LD na ovoj stranici, ne pravi ga — samo vidljiv tekst je dovoljno
za ovaj zadatak (GEO cilj je citabilnost, ne shema).

## Koraci (izvršiti u cPanel terminalu, u `~/public_html`)

1. `cd ~/antasline-vault && git pull --no-edit` (standardni preduslov)
2. Nađi post:
   ```
   wp post list --post_type=post --s="epoksidni" --fields=ID,post_title,post_name,post_status
   ```
   (ili pretraga po slug-u `epoksidni-podovi-ili-ecotile-podovi` ako `--s` ne
   pogodi tačno). Potvrdi ID pre bilo kakve izmene.
3. **Backup pre izmene** — samo ovaj post, ne cela baza (nizak rizik, brzo):
   ```
   wp post get <ID> --field=post_content > ~/antasline-vault/migracija/backup-2542-live-pre-geo-fix-2026-07-22.html
   ```
4. Pročitaj pun sadržaj (`wp post get <ID> --field=post_content`) — utvrdi
   stvarnu strukturu: da li je Porto/Kallyas builder markup, gde je uvodni
   pasus, da li postoji vidljiv FAQ blok, da li postoji JSON-LD.
   🔴 **Poznat gotcha (Kallyas tema)**: `post_title` se već renderuje kao pravi
   `<h1>` od strane teme — NE dodavati nikakav `<h1>` u sadržaj (izazvalo bi
   2×H1, viđeno na `/politika-kolacica/` incidentu 2026-07-11).
5. Primeni dve dopune (1. i 2. iz sekcije "Cilj") preko `$wpdb->update()` ili
   `wp post update <ID> --post_content=...` sa fajlom (NE inline `-r`/`--post_content` sa
   ugnježdenim navodnicima — poznat incident gde je to obrisalo ceo sadržaj,
   uvek kroz privremeni PHP/HTML fajl).
6. Verifikuj:
   ```
   curl -s -o /dev/null -w "%{http_code}\n" https://www.antasline.com/epoksidni-podovi-ili-ecotile-podovi/
   curl -s https://www.antasline.com/epoksidni-podovi-ili-ecotile-podovi/ | grep -o "<h1" | wc -l   # mora biti tačno 1
   curl -s https://www.antasline.com/epoksidni-podovi-ili-ecotile-podovi/ | grep -o "alternativa epoksidnom podu za proizvodnu halu"
   ```
   Ako je bio JSON-LD i menjan je, validiraj da je i dalje ispravan JSON (dekoduj i `json_decode`/`php -r` proveru, ne pretpostaviti).
7. `wp litespeed-purge all` (ili ekvivalent cache purge ako LiteSpeed keš postoji) da se izmena odmah vidi, pa ponovi curl proveru iz koraka 6 posle purge-a.
8. Append u `[[DNEVNIK-NAPRETKA]]` na vrh (standardni cPanel protokol):
   ```
   ## 2026-07-22 [cpanel-live] [W2 GEO] — Live 2542 GEO fix (alternativa epoksidu fraza) ✅
   - Dodata rečenica u uvod + 1 novo FAQ pitanje sa frazom "alternativa epoksidnom podu za proizvodnu halu" (izvor: AI test gap, [[analiza/2026-07-22-ai-test-baseline]])
   - Backup: migracija/backup-2542-live-pre-geo-fix-2026-07-22.html
   - Verifikovano: 200 / 1×H1 / fraza prisutna / [JSON-LD i dalje validan, ako je menjan]
   ```
9. `git add -A && git commit -m "cpanel-live: GEO fix na 2542 (alternativa epoksidu fraza za AI vidljivost)" && git push`

## Šta NE raditi
- Ne restruktuirati stranicu, ne dirati title/meta (poseban zadatak, van obima).
- Ne dodavati novi `<h1>`/`<h2>` koji bi dupliralo postojeću strukturu.
- Ne diraj ništa drugo na sajtu osim ovog jednog posta.
- Ne izmišljaj brojeve/statistike/cene u novom FAQ odgovoru.
- Ako post 2542 na live-u uopšte ne postoji na toj putanji ili je struktura
  toliko drugačija da se dopuna ne uklapa prirodno — stani i prijavi umesto
  da forsiraš izmenu.

## Sledeći korak (posle ovoga, na lokalu)
Ništa automatsko — samo napomena u [[PROGRESS]] da je live deo GEO fix-a
zatvoren (lokalni deo je već zatvoren u paralelnoj sesiji istog dana).
