---
tip: sesija
alat: claude-code
datum: 2026-08-18
blok: W2 (content, post-live)
status: zavrseno
---

# Sesija — Istraživanje sekcije „Industrijski podovi" + ESD

## Šta je urađeno

Istraživanje za sadržaj sekcije **Industrijski podovi** (modularne PVC/LVT/ESD
obloge; epoksid izričito van opsega — ostaje conquest ugao). Rezultat je jedan
dokument: **[[seo/2026-08-18-istrazivanje-industrijski-podovi-esd]]**.

**Metod:** GSC 16m CSV (`seo/gsc-svi-upiti-16m-2026-07-04.csv`, query-level, ne
agregat) + postojeći vault materijal (`seo/2026-07-27-content-klasteri`,
`reference/konkurencija-trziste-analiza`, `analiza/2026-07-04-ads-st-analiza-16m`)
+ 8 web pretraga i 2 fetch-a naših live stranica. Ništa nije menjano na buildu —
čisto istraživanje.

### Sadržaj dokumenta

1. **Sedam problema koje kupac stvarno rešava** (montaža bez zaustavljanja
   proizvodnje · loša/vlažna podloga · epoksid koji se ljušti · ulja i hemikalije ·
   prašina/auditi · ESD · ergonomija-buka-klizanje), mapirano na segmente kupaca
   sa donosiocem odluke i porukom po segmentu.
2. **GSC tabela tražnje** za klaster INDUSTRIJSKI + ESD-ANTISTATIK sa čitanjem
   svakog reda.
3. **ESD dubinski deo** — fizika (pisana za kopi), tabela standarda i granica,
   terminološka tabela, „šta pod sam ne rešava", rizični sektori u Srbiji,
   štete + pet prodajnih uglova poređanih po jačini.
4. **Prioritizovana lista od 8 akcija za sajt** + Ads implikacija.
5. Otvorena pitanja i puna lista izvora (21 link).

## Ključni nalazi

🔴 **„Radionica" je najveća neopslužena prilika u sekciji.** Zbir svih varijanti
(`podovi za radionice`, `pod za radionicu`, `gumeni/pvc/podne obloge za radionice`,
`plocice za radionicu` + „cena" varijante) = **~4.700 prikaza / ~275 klikova**,
poz. 3,5–7, CTR do 9,8% — **bez namenske stranice**. To je više kvalifikovane
tražnje nego ceo Ecotile-PVC klaster (924 prikaza/90d).

🔴 **Head-termin `industrijski podovi` curi:** 6.321 prikaz, CTR **2,6%**, poz. 7,2.
Prateći `industrijski pod` još gore (1.613 prikaza, CTR 1,0%).

🔴 **Cenovni intent ~1.800 prikaza, potpuno neopslužen** (`industrijski podovi cena
po m2` 841 / CTR 1,8% · `industrijski podovi cena` 470 · `industrijski pod cena` 200
· regionalne `cijena` varijante ~300). Kontrola koja dokazuje da rešenje radi:
`antistatik pod cena` ima **CTR 23,5%** na poz. 2,4 — isti obrazac kao „koš za
dvorište".

🟢 **Na `antistatik*` upitima dominiramo** (`antistatik pod` 282 kl. / CTR 15,7% /
poz. 1,6; `antistatik podovi` CTR 21,1%) — publika već dolazi, ali se ne
kvalifikuje. Ads potvrda ranijeg nalaza: **ne licitirati** te termine (već zapisano
kao ~5k RSD/90d otpada u `analiza/2026-07-04-ads-st-analiza-16m`).

🎯 **Najjači sadržajni diferencijator: tabela `antistatik ≠ ESD ≠ elektroprovodljivo`.**
Samo dva od tri pojma imaju merljivu granicu (disipativno ~10⁶–10⁹ Ω, elektro-
provodljivo ~10⁴–10⁶ Ω); „antistatik" kao marketinški termin ne garantuje ništa.
Nijedan konkurent iz uzorka to ne razdvaja.

🎯 **Najjači prodajni ugao:** radnik ne oseti pražnjenje ispod ~3.000 V, a komponenta
otkazuje na 25–50 V (HDD ~10 V) → **do ~90% ESD kvarova je latentno**: prođe
kontrolu, otkaže kod kupca, reklamacija košta višestruko više od škarta u hali.

## Beleške / odluke

- **Standardi su prikupljeni iz javnih izvora, NISU verifikovani protiv deklaracije
  o performansama Ecotile serija koje prodajemo.** U dokumentu stoji eksplicitno
  upozorenje — nijedna brojka odatle ne sme na sajt bez provere kod dobavljača.
- **Quectel:** potvrđen samo **R&D centar u Beogradu** (100+ zaposlenih). Proizvodni
  pogon u Novom Sadu **nije potvrđen nijednim izvorom** — zapisano kao „ne tvrditi
  da postoji". Potvrđene proizvodne mete su Continental Novi Sad (Šangaj + Kać,
  ~1.500 zaposlenih, +165 mil. €) i ZF Pančevo (238 mil. €, energetska elektronika
  → SMT → ESD zone po definiciji).
- **Dve zamerke na postojeći live sadržaj** (`/zasto-vam-je-potreban-esd-pod/`):
  (a) argument o „posledicama po nervni sistem" je slab i osporiv — predlog da se
  oslabi ili izbaci; (b) reference Siemens/Toyota/GKN/Lockheed su reference
  **proizvođača Ecotile**, ne naši klijenti — moraju biti jasno označene kao takve.
- **Ugao koji niko na tržištu ne koristi:** reverzibilnost za **zakupljene hale** —
  modularni pod se demontira i seli sa firmom, liveni je nepovratna investicija u
  tuđu imovinu.
- **Ovo je post-live materijal.** Ne dira se build pred gate 21.08; predložene
  akcije (nova `/podovi-za-radionice/`, cenovni blokovi, ESD tabela) su kandidati
  za sadržajni red čekanja posle migracije 25.08.
- Metodološka napomena: nalaz o „radionici" se **ne vidi u klaster agregatu** —
  INDUSTRIJSKI klaster (1.537 prikaza/90d) izgleda mali dok se ne pogleda
  query-level. Isti obrazac kao revizija „dvorište" 27.07.

## Otvorene akcije

- [ ] Deklaracija o performansama za ESD seriju koju prodajemo — tačne vrednosti
      otpora, metod merenja, da li postoji disipativna i elektroprovodljiva verzija #ceka-miroslav
- [ ] Da li radimo **elektroprovodljivu** verziju (≤ 10⁶ Ω) za ATEX zone — bez
      potvrde se ATEX segment (boje/lakovi/gorivo/municija) ne sme ciljati #ceka-miroslav
- [ ] Da li nudimo **merenje otpora sa zapisnikom** posle ugradnje + ugradnju
      uzemljenja (bakarna traka) — jedini argument koji zatvara posao kod inženjera
      kvaliteta, nijedan konkurent ga nema #ceka-miroslav
- [ ] Cenovni raspon za industrijsku i ESD liniju (veza: [[reference/cenovnik]]) #ceka-miroslav
- [ ] Posle live-a: nova stranica `/podovi-za-radionice/` #claude-code
- [ ] Posle live-a: cenovni blok + `cena po m2` odgovor na `/industrijski-podovi/` #claude-code
- [ ] Provera pre nego što se CTR na `industrijski podovi` pripiše title/meta-i:
      pogledati SERP — deo volumena možda traži **liveni** pod (nekvalifikovan intent) #claude-code

## Veze
- Istraživanje: [[seo/2026-08-18-istrazivanje-industrijski-podovi-esd]]
- Podloga: [[seo/2026-07-27-content-klasteri]] · [[reference/konkurencija-trziste-analiza]] · [[analiza/2026-07-04-ads-st-analiza-16m]]
- Cenovnik: [[reference/cenovnik]]
- Plan: [[2026-07-06-MASTER-PLAN-V2]] (W2)
