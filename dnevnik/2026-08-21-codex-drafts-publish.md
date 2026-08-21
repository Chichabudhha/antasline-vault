---
tip: template
alat: claude-code
datum: 2026-08-21
blok: C
status: zavrseno
---

# Sesija — 7 Codex draft proizvoda: minimalan fix + publish

## Šta je urađeno

Nastavak [[dnevnik/2026-08-20-codex-srl-uvoz-proizvoda]] — svih 7 draft proizvoda
(Quadrio 17888, Polyshock 17895, Interior 17902, Sport Roll 17920, Crossfit Floor
17926, Maxionda 17964, Wall Mat 17971) pregledano protiv `/obogati-proizvod`
standarda, otvorena 2 stvarna sadržajna nedostatka i M je odobrio "minimalan fix
pa publish" (isti bar kao Onda 17957, ne pun 8-tačka standard):

1. **Eksplicitna "Cena: na upit"** — svaki proizvod je imao samo generički CTA
   ("pošaljite upit preko kontakt forme") bez ijedne reči o statusu cene. Zamenjen
   zadnji pasus verzijom koja počinje `<strong>Cena: na upit</strong> — zavisi
   od količine, boje i konfiguracije...` + isti CTA (telefon/forma).
2. **Cross-link ka kategoriji** — dodat pasus "Deo je AntasLine ponude [sportskih
   podloga](kategorija-proizvoda/sportske-podloge/)" (5 proizvoda) ili
   "[zaštite i bumpera](kategorija-proizvoda/zastita-i-bumperi/)" (Maxionda, Wall Mat).
3. **`post_status` → `publish`** za svih 7.

Sve preko direktnog SQL-a (`UNHEX`/`UPDATE`, ne `wp-load` bootstrap — isti
gotcha kao 18.08 sesija), backup pre izmene:
`antasline-backups/antasline_local_2026-08-21_pre-codex-minimal-fix.sql` (37 MB).

## 🔴 Ispravka greške iz iste sesije — namena tag "nedostatak" je bio lažan nalaz

Prvobitna analiza je tvrdila "0/8 proizvoda ima `namena-*` tag" — **netačno**,
posledica bag-a u proveri: upit je filtrirao `t.name LIKE 'namena-%'`, a `name`
kolona nosi čitljiv srpski naziv termina ("Igrališta", "Sportska dvorana"...),
ne slug. Ispravan upit (`t.slug LIKE 'namena-%'`) je posle izvršene izmene
pokazao da je **svih 7 draft proizvoda + Onda već imalo bar 1 namena tag** iz
20.08 uvoza (Polyshock/Interior → `namena-igraliste`, Sport Roll/Crossfit
Floor/Wall Mat/Maxionda/Onda → `namena-sport-dvorana`, Quadrio →
`namena-sportski-teren-otvoreni`).

Posledica: na osnovu lažnog nalaza sam preko `INSERT IGNORE` dodao dodatne
namena tagove (Quadrio +terasa/štale/igrališta, Interior +sport-dvorana,
Maxionda +sportski-teren-otvoreni) — sadržajno tačno (svaki dodatak je
zasnovan na stvarnom tekstu opisa), ali **nepotreban korak** izveden na
pogrešnoj premisi, ne štetan (ništa nije obrisano/pregaženo, `INSERT IGNORE`
je no-op na već postojećim parovima). Dva ostala nalaza (nema "Cena na upit"
teksta, nema cross-link ka kategoriji) su bila **tačna** — potvrđena preko
`LOCATE()` na `post_content`, ne preko tag upita, i ta dva su stvarno
popravljena.

**Lekcija:** kod WP taksonomija uvek proveravati po `slug`, ne po `name` —
`name` je lokalizovan prikazni tekst i ne nosi prefiks konvencije
(`namena-*`). → dodaj u [[reference/lekcije-wp-db-tehnika]] ako se ponovi.

## Verifikacija

- [x] Svih 7 URL-ova (`proizvod/codex-*`) → HTTP 200
- [x] Tačno 1×H1 na svakoj stranici
- [x] Rank Math Product JSON-LD — 1 blok po stranici, bez dupliranja
- [x] "Cena: na upit" tekst prisutan tačno 1× po stranici
- [x] Cross-link ka kategoriji renderovan i cilja postojeći URL (oba 200)
- [x] `kontakt/` i obe kategorija-arhive i dalje 200
- [x] Regression spot-check: homepage, `/industrijski-podovi/`, Onda stranica — 200

Nijedna izmena nije dirala FAQ/JSON-LD ručni upis/PDF/Ugradnja sekciju —
to su preostale rupe protiv punog `/obogati-proizvod` standarda, svesno
izostavljene po M odluci (minimalan fix, ne pun standard).

## Otvorene akcije

- [x] ✅ Cena Onda potvrđena (M, 21.08): 14.088 je bez PDV, +20% (16.906 RSD)
  je ispravan obračun — nema izmene u bazi
- [ ] #claude-code — opciono kasnije: FAQ (3 pitanja) + "Ugradnja" sekcija
  za svih 7, ako se odluči pun `/obogati-proizvod` standard; PDF tehnički
  listovi ne postoje u medijateci za nijedan Codex proizvod (Codex sajt ih
  ni ne nudi kao zaseban download, samo HTML "Material Data Sheet" stranice)

## Beleške / odluke

- M odluka (`AskUserQuestion`): "Minimalan fix pa publish" — isti bar kao
  Onda, ne pun 8-tačka standard.
- Svih 8 Codex proizvoda (7 sada + Onda) je sada `publish`.

## Veze
- Prethodna sesija: [[dnevnik/2026-08-20-codex-srl-uvoz-proizvoda]]
- Standard: `.claude/skills/obogati-proizvod/SKILL.md`
- Backup: `antasline-backups/antasline_local_2026-08-21_pre-codex-minimal-fix.sql`
