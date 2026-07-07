---
tip: prompt
faza: F6
naziv: Arhitektura namena→proizvodi (tag + auto grid)
menja-bazu: DA ⚠️ (tagovi na proizvodima)
preduslov: F2 gotov (permalinci) · backup baze
---

# F6 — Arhitektura "namena → proizvodi"

**Problem koji se rešava:** live stranice tipa `/podovi-za-garaze/` opisuju JEDAN
proizvod (Ecotile 5), a ponuda se proširila — istu namenu sad pokriva više podloga.
Uz to, `/industrijski-podovi/` postoji i kao stranica i kao Woo kategorija.
Strategija: [[migracija/PARITY-PLAN]] §1 (P5).

## Troslojni model (standard za ceo sajt)

| Sloj | URL primer | Namera posete | Sadržaj |
|---|---|---|---|
| Proizvod | `/proizvod/ecotile-e500-5/` | transakciona | tehnički opis, Product schema, "Zatraži ponudu" |
| Woo kategorija | `/kategorija-proizvoda/industrijski-podovi/` | browse | Layout Builder hero+USP+FAQ (postoji od 06.07.) + grid |
| **Namenska landing** | `/podovi-za-garaze/`, `/industrijski-podovi/` (page) | informativno-komercijalna | direktan odgovor (GEO) → **uporedna tabela svih pogodnih sistema** → auto grid proizvoda → FAQ → CTA 072 + forma |

**Stranica vs istoimena kategorija:** stranica = edukativna, cilja informativni upit,
nosi istoriju rangiranja; kategorija = transakciona/browse. Različit H1 i Yoast title,
obavezni cross-linkovi u oba smera. (Obrazac već primenjen na paru 254↔16567.)

## Koraci

1. 🔴 **BACKUP baze.**
2. **Kreiraj product_tag termine za namene** (taxonomy `product_tag`, slugovi sa prefiksom):
   `namena-garaza`, `namena-terasa`, `namena-magacin-hala`, `namena-sport-sala`,
   `namena-parking`, `namena-stala`, `namena-radionica`, `namena-esd`,
   `namena-teretana`, `namena-bazen` — listu proširi po stvarnoj ponudi.
3. **Dodeli tagove svih 37 proizvoda**: napravi tabelu proizvod→namene (izvedi iz
   kategorije, opisa i zdravog razuma; npr. Ecotile 500/5 → garaza+magacin-hala+radionica;
   Bergo → terasa+bazen+sport-sala; DuraStripe → magacin-hala; bumperi → magacin-hala).
   **Prezentuj tabelu Miroslavu na potvrdu PRE upisa** — on zna ponudu bolje.
   Upis: `wp_set_object_terms($product_id, [...], 'product_tag', true)`.
4. **Grid mehanika**: na namenskim stranicama WoodMart `products` shortcode filtriran
   po tagu, npr:
   ```
   [products taxonomies="product_tag:namena-garaza" columns="3" items_per_page="6" ...]
   ```
   (Proveri tačnu sintaksu WoodMart shortcode-a u aktivnoj verziji teme pre upotrebe —
   WoodMart ima svoj `woodmart_products` / WPBakery element; dokumentuj radni primer.)
5. **Pilot**: primeni pun troslojni obrazac na JEDNU stranicu (predlog: `/koji-pod-postaviti-u-garazu/`
   post ili buduća `/podovi-za-garaze/` — šta god postoji posle F3/F5) — uporedna
   tabela sistema (Ecotile vs Bergo vs gumeni: cena od–do ili "na upit" iz
   [[reference/cenovnik]], opterećenje, montaža, kada koji) + grid + FAQ.
6. **Dokumentuj obrazac** u [[migracija/woodmart-sabloni]]: novi odeljak
   "Namenska landing (rešenje hub)" sa radnim shortcode primerom i pravilima
   diferencijacije stranica/kategorija — buduće W1 sesije ga koriste za SVE landing stranice.

## Pravila

- Novi proizvodi kojih nema na live slobodno se dodaju lokalno (kroz `/obogati-proizvod`) —
  parity je jednosmeran; svaki nov proizvod ODMAH dobija namena tagove.
- Uporedne tabele: cene isključivo iz [[reference/cenovnik]] ili "na upit" — ništa se ne izmišlja.
- Grid ne sme da razbije LCP: lazy load slika u gridu.

## Verifikacija

- [ ] Svih 37 proizvoda ima bar 1 namena tag
- [ ] Pilot stranica: 200 · 1×H1 · grid prikazuje tačne proizvode · JSON-LD validan
- [ ] Dodavanje test-taga na proizvod → proizvod se pojavi u gridu bez izmene stranice (auto mehanika radi)
- [ ] woodmart-sabloni dopunjen obrascem
- [ ] Regression: 2 ranije stranice → 200

## Zatvaranje sesije

1. [[DNEVNIK-NAPRETKA]] — unos NA VRH (`[W1 PARITY F6]`): tabela tagova, pilot, gotcha-i, backup fajl
2. [[PROGRESS]] + štikliraj F6 u [[migracija/promptovi/_README]]
