---
tip: plan
naziv: Postovi iz ugašenog FAQ klastera „izbor industrijskog poda" — posle migracije
datum: 2026-08-13
status: planirano — izvršenje POSLE live-a (24.08), najranije ~01.09
vlasnik: CC
izvor: "[[seo/2026-08-13-kanibalizacija-konsolidacija-plan]]"
---

# Postovi iz ugašenog FAQ klastera — posle migracije

## Šta se desilo 13.08

Ceo FAQ klaster — **tri stranice** — ugašen je i 301-ovan na `/industrijski-podovi/` (M odluka):

| ID | URL | Stanje | GSC 12 meseci |
|---|---|---|---|
| 2622 | `/izbor-industrijskog-poda-tri-najcesca-pitanja/` | → draft 13.08 | 128 prikaza / **0 klikova** |
| 3274 | `/izbor-industrijskog-poda-tri-najcesca-pitanja-2/` | draft od 27.07 | 98 prikaza / **0 klikova** |
| 17025 | `/industrijski-podovi-najcesca-pitanja/` | → draft 13.08 | 4 prikaza / **0 klikova** |

Razlog: 2622 i 3274 obrađuju **ista tri pitanja** (3274 je prepričan 2622), a sve tri
stranice takmiče se za upit „industrijski podovi" sa poz. **20–80**, dok ga hub drži na
**6,7** (16.417 prikaza / 410 klikova). Tri slaba izvora cepala su signal protiv sopstvenog
huba, bez ijednog klika u 12 meseci.

**Tekst nije izgubljen** — sva tri posta stoje kao draft u bazi, plus backup-i
`antasline_local_2026-08-13_pre-faq-konsolidacija.sql` i `…_pre-faq-17025.sql`.

## Šta je preneto na hub odmah

Iz **2622/3274** — četiri pitanja koja hub nije imao; hub je usput dobio **FAQPage JSON-LD**
koji do tada uopšte nije imao (posle obe konsolidacije: **15 pitanja**):

1. Kako da izaberem industrijski pod — po čemu da se vodim?
2. Može li se pod postaviti na svež beton u novogradnji?
3. Koliko traje priprema u odnosu na premazne podove?
4. Šta biva sa starim podom kada ga menjam?

Nije preneto ono što hub već ima: Ecotile podela **500/5 · 500/7 · 500/10** po opterećenju
(sekcija „Koja debljina za koju namenu?") i 7 postojećih FAQ pitanja.

## Zapis — šta su ta dva članka stvarno obrađivala

**Okosnica (oba članka, identična):** tri pitanja pri izboru industrijskog poda.

| # | Pitanje | Šta je bilo u odgovoru |
|---|---|---|
| 1 | **Koja je funkcija prostora / vrsta objekta?** | viljuškarski saobraćaj · otpornost na ulja i hemikalije · protivklizno · ESD/antistatik za elektroniku i štampariju · obeležavanje bezbednih zona i znakova u podu · skladišta i magacini uz 24/7 viljuškare · ispucao pod = bezbednosni rizik pri prenosu tereta |
| 2 | **Koliko brzo pod mora da proradi?** | beton u novogradnji sazreva **do godinu dana** · beton se steže odozgo nadole · Ecotile se polaže **bez lepka i hidroizolacije**, beton nastavlja da diše ispod · priprema za epoksid/boju traži neutralizaciju ulja, vlage i hemikalija + **glodanje ili brušenje**, puno prašine, **nedeljama** · ploče se prilagođavaju neujednačenom podu |
| 3 | **Koliko izbor prave podloge stvarno vredi?** | cena nije samo materijal + ugradnja · troškovi čišćenja i održavanja kroz vek · **zamena pojedinačne oštećene ploče** bez zaustavljanja pogona · čišćenje ručno ili rotacionom mašinom · **Ecotile otkupljuje stari pod** i uračunava ga u cenu novog (održivost) |

**Dodatno u 3274 (nije u 2622):** Ecotile 500/5 za manja opterećenja (radionice,
kancelarije) · 500/7 za magacine i hale sa viljuškarima do 3,5 t · 500/10 za teška vozila.
→ **Hub ovo već ima**, ne treba prenositi.

**Pobrojane alternative (oba članka, bez poređenja u dubinu):** boje za podove · epoksidna
smola · ferobeton · polirani beton · drvena građa · keramičke pločice · asfalt.

## Predlog postova — po jedna tema po postu

🔴 **Pravilo za svaki: pre pisanja obavezno GSC provera upita + provera protiv postojeće
pokrivenosti ispod.** Ceo ovaj posao nastao je iz kanibalizacije — ne sme da je proizvede
ponovo. Svaki post linkuje ka `/industrijski-podovi/`, hub linkuje nazad.

| # | Tema | Ciljni upiti (za proveru) | Zašto nije duplikat |
|---|---|---|---|
| P1 | **Postavljanje poda na svež beton u novogradnji** | „koliko beton sazreva", „pod na nov beton", „kada se može postaviti pod posle betoniranja" | 🟢 čista rupa — 16675 pokriva **oštećen epoksid**, ne nov beton |
| P2 | **Šta industrijski pod košta kroz ceo vek** (TCO) | „održavanje industrijskog poda", „vek trajanja industrijskog poda", „zamena oštećene ploče" | 🟡 16874 `/industrijski-podovi-cena/` pokriva **nabavnu** cenu; ovo je trošak vlasništva — razgraničiti u naslovu |
| P3 | **Otkup i reciklaža starog poda** | „šta sa starim podom", „reciklaža PVC poda", „otkup starog industrijskog poda" | 🟢 čista rupa — nigde na sajtu |
| P4 | **Obeležavanje zona i bezbednosnih staza u hali** | „obeležavanje u hali", „trake za podno obeležavanje", „sigurnosne zone u magacinu" | 🟢 rupa + **komercijalna veza**: DuraStripe proizvodi (16518–16524) i kategorija „Podno obeležavanje" |
| P5 | **Poređenje sa alternativama koje nisu epoksid** (polirani beton, ferobeton, keramika, asfalt) | „polirani beton ili pvc", „keramika u hali", „ferobeton pod" | 🟡 2542 pokriva **samo epoksid** (conquest). ⚠️ Epoksid se ne prodaje — ovaj post ga ne obrađuje, upiti idu na 2542 |

**Redosled po vrednosti:** P1 → P4 → P3 → P2 → P5. Procena **40–60 min po postu**.

## Postojeća pokrivenost — protiv koje se svaki predlog proverava

`/industrijski-podovi/` (16567, hub) · `/industrijski-pod/` (16660) ·
`/industrijski-podovi-cena/` (16874) · `/industrijski-podovi-montaza-preko-ostecenog-epoksida/`
(16675) · `/podovi-za-magacine-i-hale/` (16687, ⚠️ **child stranica huba** — flat URL uvek
301-uje na `/industrijski-podovi/podovi-za-magacine-i-hale/`, linkovati ugnježden oblik) · `/antistatik-i-elektroprovodljivi-podovi/` (16658) ·
`/epoksidni-podovi-ili-ecotile-podovi/` (2542, conquest) · `/osteceni-industrijski-pod/`
(16608) · `/ugradnje-industrijskog-poda/` (3257) · `/modularni-industrijski-podovi/` (5411) ·
`/zasto-vam-je-potreban-esd-pod/` (3318) + 3 ESD reference posta (6874, 5163, 17021)

## ✅ Treća stranica — zatvoreno istog dana

`/industrijski-podovi-najcesca-pitanja/` (17025, **4 prikaza / 0 klikova / 12 meseci**)
takođe je ugašena i 301-ovana na hub (M odluka 13.08, ista sesija). **Ceo klaster je sada
konsolidovan — nijedna zasebna FAQ stranica više ne postoji.**

Preneta su 4 pitanja koja hub nije imao: samostalna montaža · spoljašnja upotreba
(odgovor je **NE** — negativan kvalifikator koji odbija pogrešne upite) · postavljanje
preko farbanog betona, pločica, tepiha ili vinila · kada je lepak potreban (uklj.
preporučeno lepilo **Uzin MK92S**). Nije preneto ono što hub već ima: viljuškari + tabela
debljina, upotreba odmah po montaži, priprema podloge.

🔴 **Istorijsko pravilo sa 615 pogodaka** (`/home/industrijski-podovi-najcesca-pitanja/`)
pretočeno sa 17025 na hub u istom potezu — bez toga bi tih 615 posle migracije išlo na 404.
Stavka menija **17390** obrisana (hub je u meniju već 2×, prevezivanje bi dalo treći duplikat).

**Hub sada nosi 15 FAQ pitanja + FAQPage schema.** To je gornja granica — svako sledeće
pitanje na ovu temu ide u zaseban post iz tabele „Predlog postova" iznad, ne na hub.

## Veze

- [[seo/2026-08-13-kanibalizacija-konsolidacija-plan]] · [[DNEVNIK-NAPRETKA]] 2026-08-13
- Skripte: `migracija/alati/job-faq-konsolidacija-2026-08-13.php` (2622+3274) ·
  `job-faq-17025-konsolidacija-2026-08-13.php` (17025)
- Backup-i: `antasline_local_2026-08-13_pre-faq-konsolidacija.sql` · `…_pre-faq-17025.sql`
