---
tip: strategija
datum: 2026-07-24
blok: W2/W6
naziv: Off-page / backlink plan — podizanje autoriteta domena
status: aktivan
azurirano: 2026-07-24
---

# 🔗 Backlink / off-page plan — antasline.com

> Nastalo iz nalaza 2026-07-24: `[[seo/geo-ai-plan]]` sekcija 4 ("Pominjanja treće
> strane") je jedino mesto u planu koje se doticalo eksternih linkova, i to samo
> iz GEO (AI-vidljivost) ugla, ne kao sistematski off-page SEO rad. Ovaj fajl je
> namenski backlink/domain-authority plan — dopunjuje, ne zamenjuje GEO plan.
>
> **Tvrdo pravilo (isto kao svuda u projektu):** nikakvi kupljeni linkovi, PBN,
> Fiverr "100 backlinks" paketi, masovni direktorijum-spam. Rizik Google spam
> penala > kratkoročna korist — isti princip kao odluka o fake recenzijama na
> `/teren-za-pickleball/`.

## Kako se koristi

Taktike su podeljene u 4 tijera po trudu/riziku/brzini efekta. Štikliraj kad
je urađeno, upisuj nalaze/odgovore od Miroslava direktno ispod taktike (isti
obrazac kao [[seo/plan-novih-stranica]]).

---

## Tier 1 — Brzo i skoro besplatno

- [ ] **Dobavljač "pronađi distributera" linkovi** — Bergo, Ecotile (i ostali
  brendovi koje AntasLine zastupa) imaju "Find a dealer/distributor" stranice
  na svojim međunarodnim sajtovima. Mejl da dodaju/ažuriraju AntasLine sa
  linkom ka antasline.com. Najjači i najrelevantniji link koji je realno
  dostupan (isti brend, ista niša, autoritativan domen). #ceka-miroslav (ko
  šalje mejl, kontakt osoba kod dobavljača)
- [ ] **NAP konzistentnost** (ime/adresa/telefon) — identično na sajtu, GMB,
  svuda gde se firma pominje. Poznat nalaz: `/kontakt/` "11050" vs "11000"
  Beograd nekonzistentnost (2026-07-23 llms-full.txt sesija) — treba se
  razjasniti i ispraviti svuda pre nego što se count-uje kao gotovo.
- [ ] **Privredna komora Srbije (PKS)** — proveriti status članstva/profila;
  ako postoji registar sa javnim profilom firme, treba link ka sajtu.
- [x] GMB profil postoji (UTM fix + kategorije već urađeno, W5 5.2) — nije
  backlink sam po sebi, ali je deo istog NAP/autoritet seta signala.

## Tier 2 — PR i pominjanja treće strane

- [ ] **Sportski projekti → sportski portali** — Spanoulis Court, Dunk Shop
  3x3 teren. Kontakt lokalnih sportskih/košarkaških portala/grupa sa kratkom
  pričom + fotkama, traži pomen+link. #ceka-miroslav (koji portal, ko piše
  pitch — mogu ja napisati template kad se izabere konkretan portal)
- [ ] **B2B klijenti → "izvođač" pomen** — Quectel, HTEC, Adient Kragujevac,
  Philip Morris Niš, AIK Bačka Topola i dr. Zamoliti kontakt osobu da na
  svom "o nama"/press/partneri delu pomene AntasLine kao izvođača, sa linkom.
  Meko pitanje, 1 mejl po klijentu. #ceka-miroslav
- [ ] **Građevinski/industrijski portali** — provериti da li postoje aktivni
  srpski portali sa realnom SEO vrednošću (ne upisivati na napuštene/mrtve
  direktorijume — link sa spam/mrtvog domena ne pomaže, može i štetiti).
  Nema još identifikovanog konkretnog portala — istraživanje pre upisa.

## Tier 3 — Sadržaj koji sam privlači linkove (organsko)

- [x] **Court builder alat** (`/planer-terena/`) — već postoji (RP2 zatvoren
  2026-07-12), klasičan "linkable asset". Nedostaje: aktivna promocija ka
  ciljnim linkerima (sportski klubovi/škole/blogovi) — to je Tier 2 posao.
- [x] **Dimenzije/cena tabele** (W2 content plan) — već postoje, prirodni
  citation-magnet za forume/blogove o istoj temi.
- [x] **Case study stranice** (Quectel, HTEC) — postoje, interno linkovane.
  Sledeći korak je da SE PROMOVIŠU (Tier 2 #6), ne da se prave nove — ovo je
  već "gotov materijal koji čeka distribuciju".

## Tier 4 — Ambijentalno (kontinuirano)

- [ ] GMB recenzije 6 → 20+ (W5 5.3, M5/M4 zavisnost) — nije backlink, isti
  autoritet signal za lokalni pack.
- [ ] Social profili sa linkom u bio + UTM (W6 Faza 0) — nofollow, ne prenosi
  direktan SEO "juice", ali generiše referral saobraćaj i brand pominjanja.

## Šta izbegavati

🔴 Kupljeni linkovi, Fiverr "100 backlinks" paketi, PBN mreže, masovni
direktorijum-spam. Google Penguin/spam penal je realan i teško se oporavlja.

## Merenje

- **Google Search Console → Links report** (sc-domain:antasline.com) —
  besplatno, pokazuje broj referring domains + top linkovane stranice. Nema
  potrebe za plaćenim alatima (Ahrefs/Semrush su i inače blokirani botovima
  na sajtu, v. `[[analiza/BOT-CRAWLER-LOG]]`).
- Baseline referring domains: **nije još izmereno** — prva stavka sledeće
  sesije koja se ovoga dotiče, pre bilo kakve taktike, da se zna od čega
  krećemo.
- Pratiti mesečno u punom snapshotu ([[analiza/_TEMPLATE-snapshot]]) kad
  baseline postoji.

## Redosled

1. **Odmah/ova nedelja:** Tier 1 #1 (dobavljač mejlovi) + #2 (NAP provera) —
   najjeftinije, najviši potencijalni ROI.
2. **Do migracije (2026-08-31):** Tier 1 #3 (PKS) + Tier 2 #6 (B2B klijenti).
3. **Posle migracije / W6:** Tier 2 #5, #7 + promocija Tier 3 materijala kroz
   redovan social/content ritam koji već postoji u planu.

## Otvoreno — #ceka-miroslav (sumarno)

- Kontakt osoba/mejl kod Bergo i Ecotile za dealer-locator upis
- Odluka koji sportski portal prvi kontaktirati (Tier 2 #5)
- Odobrenje/kontakt za B2B klijent mejlove (Tier 2 #6) — ili da Miroslav sam
  pošalje pošto je odnos već postoji
- Provera PKS članstva
- Razjašnjenje 11050/11000 NAP nekonzistentnosti

## Veze

- [[seo/geo-ai-plan]] sekcija 4 — GEO ugao istih taktika (AI pominjanja)
- [[seo/plan-novih-stranica]] — Tier 3 sadržaj koji ovaj plan promoviše
- [[.claude/skills/w6-social/SKILL.md|/w6-social]] — social distribucija (Tier 4)
- [[2026-07-06-MASTER-PLAN-V2]] — gde je ovo ukačeno u workstream raspored
- [[analiza/BOT-CRAWLER-LOG]] — zašto se ne koriste Ahrefs/Semrush za merenje
