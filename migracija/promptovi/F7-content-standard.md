---
tip: prompt
faza: F7
naziv: Content standard pre live-a (proizvodi, ikonice, skice, video)
menja-bazu: delimično (skill/CSS/fajlovi + kasnije proizvodi iterativno)
preduslov: F6 gotov (namena tagovi) · [[reference/brend-knjiga]] pročitana
---

# F7 — Standard kvaliteta sadržaja pre live-a

**Miroslavljev zahtev (2026-07-07):** proizvodi trenutno imaju "nabacan tekst" —
pre live-a moraju maksimalno unapređeni: tehnički detalji, standardi linkovani ka
stvarnim izvorima, slike/ikonice/skice u jedinstvenom stilu, video gde ima smisla.

**Ova sesija definiše standarde i alate (7.2–7.5 + skill update). Samo obogaćivanje
proizvoda (7.1) ide posle, iterativno kroz `/obogati-proizvod` sesije.**

## 7.1 Standard obogaćivanja proizvoda (definisati ovde, primenjivati iterativno)

Svaki proizvod pre live-a dobija:
- **Tehničku tabelu**: dimenzije, debljina, materijal, nosivost/opterećenje,
  temperaturni opseg, klizavost (R-klasa), požarna klasa, garancija, zemlja porekla
- **Standarde SA LINKOVIMA** ka stvarnim izvorima + jedna rečenica ZAŠTO je standard
  bitan kupcu. Tipični standardi u ovoj branši:
  - DIN 51130 (klizavost R9–R13) · EN 13501-1 (požarna klasa Bfl-s1...) ·
    IEC 61340-5-1 / ANSI ESD S20.20 (ESD zaštita) · EN 14041 (podne obloge — CE) ·
    ISO 9001/14001 (proizvođač) · REACH (hemijska bezbednost)
- **Izvor podataka**: WebSearch/WebFetch proizvođačkih datasheet-ova —
  `ecotileflooring.com`, `bergoflooring.com`, `ergomat.com`, `geoplastglobal.com`, `hoopncourt.com`, `www.condor-group.eu`, `www.radicisport.it`, `objectflor.de`, `r-tileretailflooring.com`
  🔴 **TVRDO PRAVILO: standard/spec se navodi SAMO ako je potvrđen u datasheet-u ili
  na zvaničnom sajtu proizvođača. Bez potvrde → ne pominje se. Ništa se ne izmišlja.**
- **Prioritet obogaćivanja**: 1) Ecotile E500 5/7/10 + ESD 7mm · 2) Bergo Ultimate/XL ·
  3) košarkaške konstrukcije · 4) DuraStripe · 5) bumperi/senzori (batch, kraći format)

**Zadatak u ovoj sesiji:** ažuriraj skill `.claude/skills/obogati-proizvod/SKILL.md` —
dodaj korak "standardi sa linkovima" (istraživanje datasheet-a + tvrdo pravilo) i
korak "namena tagovi" (F6). Ostatak skill toka ne dirati.

## 7.2 SVG ikonice — brend set (uraditi u ovoj sesiji)

- Jedan konzistentan set: paleta iz [[reference/brend-knjiga]], jedinstvena debljina
  linije, isti vizuelni jezik. Format: inline SVG ili sprite u
  `C:\xampp\htdocs\antasline\wp-content\themes\woodmart-child\images\icons\`
- Potrebne ikonice (min. set): garaža, terasa, magacin/hala, sportska sala, parking,
  štala, radionica, ESD/munja, teretana, bazen (= namena tagovi iz F6) + USP set:
  brza montaža, nosivost, garancija, sertifikat, dostava, telefon/podrška
- Pomoć: skill `design` (icon design, SVG) ako je dostupan; u suprotnom pisati SVG ručno
- Dokumentuj upotrebu (kako se ubacuje u WPBakery/WoodMart blok) u [[migracija/woodmart-sabloni]]

## 7.3 Video strategija (dokumentovati; embed ide pri rebuild-ovima)

Claude ne snima video — realne opcije po isplativosti:
1. **Embed proizvođačkih YouTube videa** (Ecotile/Bergo montažni videi) kroz
   **lite-embed fasadu** (thumbnail + play dugme, iframe se učitava tek na klik —
   LCP zaštita) + `VideoObject` JSON-LD. Napravi radni HTML/JS obrazac fasade i
   dokumentuj u woodmart-sabloni.
2. **Miroslav snima ugradnje telefonom** → materijal za stranice + GMB + social (W6).
   Claude radi: plan kadrova, titlove, opise, schema. → dodaj podsetnik u
   [[reference/drustvene-mreze]] / W6 tok.
3. **SVG/CSS animacije** (uklapanje ploča, presek slojeva) kao "video-osećaj" bez troška.
4. AI video (Veo/Runway) — opciono, eksterno, samo ako M odluči; Claude priprema scenario.

## 7.4 "antas-skica" stil za postove (uraditi u ovoj sesiji)

- Definisati JEDNOM vizuelni standard tehničkih ilustracija: line-art, debljina
  linije (npr. 2px), brend boje iz brend-knjige, Inter font za labele, bela/transparent
  pozadina, isti stil strelica i kota
- Tipovi skica: presek slojeva poda (podloga→ploča→završna), dimenzije terena
  (FIBA/ITF/padel mere), koraci montaže (1-2-3), uporedni dijagrami
- Napravi 1–2 primer skice (SVG) kao šablon + dokumentuj standard u
  [[migracija/woodmart-sabloni]] (novi odeljak "antas-skica")
- Pravilo za buduće post-restyle sesije: svaki refresh/rebuild posta dobija bar 1 skicu

## 7.5 Performanse-ograda (važi za SVE iz F7)

- Slike: WebP + lazy load (osim LCP elementa) · video: isključivo fasadni embed ·
  SVG inline ili sprite (bez dodatnih HTTP zahteva po ikonici)
- Ništa iz F7 ne sme pogoršati LCP (trenutno ~7,3s, cilj <2,5s — W3 3.6)

## Verifikacija (za ovu sesiju)

- [ ] Skill `obogati-proizvod` ažuriran (standardi-sa-linkovima + namena korak)
- [ ] Icon set postoji u child temi, prikazan na 1 test stranici, validan SVG
- [ ] Lite-embed obrazac radi na 1 test stranici (video se ne učitava pre klika — proveri Network tab)
- [ ] 1–2 antas-skica šablona postoje; standard dokumentovan u woodmart-sabloni
- [ ] Test stranica LCP nije pogoršan (Lighthouse pre/posle)

## Zatvaranje sesije

1. [[DNEVNIK-NAPRETKA]] — unos NA VRH (`[W1/W2 PARITY F7]`)
2. [[PROGRESS]] + štikliraj F7 u [[migracija/promptovi/_README]]
3. U [[2026-07-06-MASTER-PLAN-V2]] dodaj napomenu da W1 1.8/obogaćivanje koristi F7 standard
