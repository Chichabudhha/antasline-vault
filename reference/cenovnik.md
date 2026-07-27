---
tip: referenca
datum: 2026-07-07
namena: Jedinstveni cenovnik — izvor istine za sve cene na stranicama i proizvodima (M10)
status: čeka-popunu
---

# 💰 Cenovnik — AntasLine

> **Miroslav popunjava JEDNOM ovde.** Cilj: da se cena za bilo koji
> proizvod/uslugu ne mora ponovo tražiti u svakoj sesiji — Claude Code vuče
> direktno odavde za W2 stranice (Tier1 draftovi čekaju M1) i za
> `/obogati-proizvod` (proizvod cene, M10). Prazno polje = "na upit" ostaje
> dok se ne popuni, sajt se ne blokira.

## Kako popuniti

- Cena od–do po m² ili po komadu — šta god je merna jedinica u prodaji
- Ako cena zavisi od količine/projekta previše da bi imala raspon → ostavi
  prazno, upiši "isključivo na upit" u napomenu
- Menja se retko — kad se promeni, samo ažuriraj ovaj fajl, Claude prenosi
  na stranice u sledećoj sesiji koja ih dotiče

## Terase i spoljne podloge (W2 Tier1 #1, #6)

| Proizvod/usluga | Cena od (RSD) | Cena do (RSD) | Jedinica | Napomena |
|---|---|---|---|---|
| Bergo XL | | | m² | |
| Bergo Elite | | | m² | |
| Bergo Unique | | | m² | |
| Bergo Easy | | | m² | |
| Montaža (rad) | | | m² ili paušal | |
| Podloge za parking/staze | | | m² | |

## Industrijski podovi (W2 Tier1 #2)

| Proizvod | Cena od (RSD) | Cena do (RSD) | Jedinica | Napomena |
|---|---|---|---|---|
| Ecotile E500/7 | | | m² | |
| Ecotile E500/10 | | | m² | |
| ESD 7mm ploče | | | m² | |
| Mosolut Heavy | | | m² | |
| Montaža (rad) | | | m² ili paušal | |

## Garaže (W2 Tier1 #3)

| Rešenje | Cena od (RSD) | Cena do (RSD) | Jedinica | Napomena |
|---|---|---|---|---|
| PVC ploče za garažu | | | m² | |
| Gumeni pod za garažu | | | m² | |

## Sportski tereni / konstrukcije

| Proizvod | Cena od (RSD) | Cena do (RSD) | Jedinica | Napomena |
|---|---|---|---|---|
| Bergo Ultimate (sportski pod, court builder pločica) | | | m² | čeka M11 — `al_cb_prices` opcija u WP adminu ("Cene planera") trenutno prazna, 0 redova u bazi |
| Bergo Ultimate FLOW (pickleball podloga) | | | m² | čeka M11, isto kao gore |
| Ecotile rampe/završni profili (4 proizvoda, 16930/16939/16943/16949) | | | m ili kom | čeka M11 — nema cene ni u WC ni u planeru |
| Lite Shot 325 (koš) | | | kom | legacy proizvod (pre S7), cena nikad upisana |
| Mini Shot 225 (koš) | | | kom | legacy proizvod (pre S7), cena nikad upisana |
| MicroShot 125 (koš) | | | kom | legacy proizvod (pre S7), cena nikad upisana |
| Koš na kolicima "Street Sport" | | | kom | legacy proizvod (pre S7), cena nikad upisana |
| Zglobni obruč za koš | | | kom | legacy proizvod (pre S7), cena nikad upisana |

### Hoop n Court koševi — VEĆ POTVRĐENO (S7, 2026-07-11, ne čeka M11)

Ove cene već postoje kao pravi WooCommerce `_regular_price` na proizvodima (M dao punu
cenovnu tabelu tada, EUR baza × ~117,5 kurs × 1,2 PDV) i Court builder ih **već vuče
direktno odatle** (ne preko `al_cb_prices`) — upisano ovde samo radi centralne evidencije,
ne treba dalja akcija dok se dobavljačka cena ne promeni.

| Proizvod | Cena (RSD, sa PDV) | ID |
|---|---|---|
| Hoopair D72 profesionalni koš | 349.680 | 16952 |
| Hoopair D60 koš | 320.070 | 16959 |
| Hoopair D54-F koš sa ankerima | 313.020 | 16966 |
| Goalrilla DC72E1 profesionalni koš | 549.900 | 16973 |
| Goalrilla CV72 koš (Clear View) | 458.250 | 16978 |
| Goaliath GB60 koš | 246.750 | 16984 |
| Goaliath Gotek 54 koš | 167.790 | 16986 |
| Goalrilla LED rasvetna oprema | 116.325 | 16988 |

### Generička oprema (S8, 8 proizvoda) — čeka M12

Tribina, stolica za tribine, go za mali fudbal, golovi rukomet/futsal, zaštitna mreža,
mreža za tenis, mreža za padel, mrežica za koš — svi namerno bez brenda/cene dok se
pregovori sa dobavljačima ne završe (M12). `_al_cb_equipment=1` flag postavljen, Court
builder ih već prikazuje kao "na upit".

## Ergomat zaštita / DuraStripe

| Proizvod | Cena od (RSD) | Cena do (RSD) | Jedinica | Napomena |
|---|---|---|---|---|
| Odbojnici/bumperi (razne varijante) | | | kom | ima 15+ varijanti — ili paušalno "od X do Y po tipu" ili batch popuniti pri /obogati-proizvod sesiji |
| DuraStripe trake za obeležavanje | | | m ili rolna | |

## Veštačka trava

| Usluga | Cena od (RSD) | Cena do (RSD) | Jedinica | Napomena |
|---|---|---|---|---|
| Veštačka trava (dvorište/teren) | | | m² | |

## Veze

[[2026-07-06-MASTER-PLAN-V2]] (M10) · [[seo/plan-novih-stranica]] · skill `/obogati-proizvod` · [[reference/claude-skilovi]]
