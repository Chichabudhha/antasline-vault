---
tip: zadatak
naziv: Ergonomske podloge — nova Woo kategorija + 8 proizvoda
datum: 2026-08-13
status: planirano (M odobrio obim 13.08, izvršenje odloženo)
vlasnik: CC
zavisi-od: "M potvrda da su svih 8 tipova u ponudi"
---

# Ergonomske podloge — nova kategorija + 8 proizvoda

## Zašto

`/ergonomske-podloge/` (16672) prikazuje **tabelu poređenja 8 tipova podloga**, a
**nijedan od njih ne postoji kao proizvod** — ni na buildu, ni na live-u (provereno
pretragom po naslovima kroz sve `product`/`page`/`post`). Telo stranice nema **nijedan**
interni link osim `/kontakt/` i `tel:`. Stranica time nabraja robu koju posetilac ne može
ni da otvori ni da uporedi, a drži „ergonomske podloge" na poz. **3,8** i „podloga za
stajanje" na poz. **6,5** (GSC 12 meseci: 123 prikaza / 4 klika).

🔴 **`/brend/ergomat/` NIJE zamena za ovo.** Tih 27 proizvoda su odbojnici, DuraStripe
trake i senzori — zaštitni program. Link tamo vodi na pogrešnu robu.

**M odluka 2026-08-13:** praviti 8 proizvoda + novu kategoriju, cena **„na upit" na svih 8**
(isti obrazac kao Bergo Ultimate/FLOW, M11), slike i podatke **dopuniti sa ergomat.com kao
najrelevantnijim izvorom**. Izvršenje odloženo — nije rađeno u sesiji 13.08.

## Obim

1. **Nova `product_cat`** — „Ergonomske podloge" (nijedna od 16 postojećih je ne pokriva).
   Postaviti opis, sliku termina i `rank_math_description` kao kod ostalih kategorija.
2. **8 proizvoda**, brend `Ergomat` (termin postoji), katalog režim → „Zatražite ponudu"
   → `/kontakt/?form-naslov=`.
3. **Prevezati 16672** — svaki red tabele poređenja dobija link na svoj proizvod; dodati
   i sekciju kartica (`al-card`) kao na ostalim hub stranicama.
4. Uzajamni linkovi sa `/antistatik-i-elektroprovodljivi-podovi/` (ESD verzije) i
   `/industrijski-podovi/`.

## Specifikacije — prepisane sa same stranice 16672

| # | Model | Debljina | Težina | Namena (sa stranice) | Verzije |
|---|---|---|---|---|---|
| 1 | Diamond Allround | 14 mm | 5,7 kg/m² | Suva radna mesta, montaža i pakovanje | standardna, sa žutim ivicama, antistatik |
| 2 | Soft Air Meter | 10 mm | 2,4 kg/m² | Maloprodaja, lakše pakovanje | sivo ili plavo, rebrasto/šljunčano |
| 3 | SuperSoft Smooth | 13 mm | 5,8 kg/m² | Servisi, kancelarije, laboratorije, apoteke | standardna, antistatik/ESD, vatrootporna, kombinovana |
| 4 | SuperSoft Office | 12–14 mm | 5,5 kg/m² | Automobilska, elektronska, metaloprerađivačka industrija | standardna, antistatik/ESD, vatrootporna, kombinovana |
| 5 | La Ola (industrijska) | 14 mm | 7 kg/m² | Teška upotreba, ulja i tečnosti | nitrilna guma ili EPDM (prehrambena) |
| 6 | La Ola Hygienic | 14 mm | 7 kg/m² | Prehrambena industrija, kuhinje, bolnice | EPDM, DGUV Hygienic Test sertifikat |
| 7 | Nitrile Walk | 10 mm | 8 kg/m² | Hodanje/okretanje oko mašina, ulja i tečnosti | standardna, ESD opcija, čvrsta donja strana |
| 8 | Solido I | 20 mm | 9,5 kg/m² | Mokri, zauljeni, teško opterećeni prostori | sa otvorima za drenažu ulja i strugotina |

🟡 **Proveriti pre upisa:** namene za **SuperSoft Smooth** i **SuperSoft Office** deluju
zamenjeno — „Office" na stranici nosi tešku industriju, a „Smooth" kancelarije/apoteke.
Ergomat-ova podela je obrnuta po imenu. **Ne prepisivati slepo sa stranice** — ukrstiti sa
ergomat.com pre nego što ode u proizvod.

## Slike — šta već imamo u medijateci

| Model | Prilog | ID |
|---|---|---|
| Diamond Allround | `diamond-allround` | 15912 |
| Soft Air Meter | `soft-air-meter` · `-2` · `-postavljene` | 15916 · 15919 · 15918 |
| SuperSoft Smooth | `supersoft-smooth-pu` | 15922 |
| SuperSoft Office | `ergonomske-podloge-za-kancelarije-supersoft-office` | 15925 |
| Nitrile Walk | `nitrile-walk` | 15928 |
| Solido I | `solido-1` | 15929 |
| **La Ola / La Ola Hygienic** | 🔴 **nema nijednu** | — |
| Opšte/ambijent | `ergonomske-podloge-foto` · `…-u-fabrikama` · `stajanje-na-ergonomskim-podlogama` · `gumene-ergonomske-podloge-rubbermats` | 12489 · 15913 · 15914 · 15927 |

**Izvor za dopunu: ergomat.com** (M odluka). Treba: La Ola i La Ola Hygienic obavezno,
ostalima po potrebi bolji studijski kadar + tehnički listovi (PDF → `pdf_download` event
već postoji u GTM-u).

🔴 **Standard slike proizvoda:** 1:1, maks. 1000×1000, **WebP** — sirove slike sa
proizvođačevog sajta ovo po pravilu ne ispunjavaju (isti problem kao S7 Hoop n Court,
34 slike na čekanju). Batch konverzija pre upisa, alat `migracija/alati/al_convert_webp.php`.

## Cene

**„Na upit" na svih 8** (M odluka 13.08). Nijedna nije u [[reference/cenovnik]]. Katalog
režim znači da se cena ne prikazuje, pa dopisivanje kasnije ne traži prepravku stranica —
samo upis u proizvod.

## Rok

⚠️ **Menja sadržaj → ako ide u produkciju 24.08, mora biti gotovo pre content freeze-a
NED 16.08.** Posle tog datuma je post-live zadatak (prvi prozor ~01.09). Procena: **2–3 h**
uz već pripremljene specifikacije, plus vreme za skidanje/obradu slika sa ergomat.com.

## Veze

- Stranica: `/ergonomske-podloge/` (16672) — čist slug od 2026-08-13, v.
  [[seo/2026-08-13-kanibalizacija-konsolidacija-plan]] §1 stavka A
- Obrazac za proizvode: skill `/obogati-proizvod` · [[migracija/woodmart-sabloni]]
- Presedan za „na upit": [[migracija/w1-novi-proizvodi-court-builder]] (M11)
