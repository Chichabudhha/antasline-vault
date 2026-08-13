---
tip: dnevnik
alat: claude-code
datum: 2026-08-14
blok: C
status: zavrseno
---

# Sesija — Ergonomske podloge: nova Woo kategorija + 8 proizvoda

Izvršenje spec-a [[migracija/w1-ergonomske-podloge-proizvodi]] (M odobrio obim 13.08,
izvršenje bilo odloženo). Poslednji radni dan pre content freeze-a (NED 16.08).

## Šta je urađeno

**Backup:** `antasline-backups/antasline_local_2026-08-14_pre-ergonomske-podloge.sql` (37,6 MB)

1. **Nova `product_cat` „Ergonomske podloge"** — term **403**, slug `ergonomske-podloge`,
   opis, `thumbnail_id=15913`, Rank Math meta.
2. **8 proizvoda** (brend Ergomat 73, katalog režim → „Zatražite ponudu"):

| ID | Proizvod | Slug |
|---|---|---|
| 17838 | Diamond Allround | `diamond-allround-ergonomska-podloga` |
| 17839 | Soft Air Meter | `soft-air-meter-ergonomska-podloga` |
| 17840 | SuperSoft Smooth | `supersoft-smooth-ergonomska-podloga` |
| 17841 | SuperSoft Office | `supersoft-office-ergonomska-podloga` |
| 17842 | La Ola (industrijska) | `la-ola-industrijska-ergonomska-podloga` |
| 17843 | La Ola Hygienic | `la-ola-hygienic-ergonomska-podloga` |
| 17844 | Nitrile Walk | `nitrile-walk-ergonomska-podloga` |
| 17845 | Solido I | `solido-i-drenazna-ergonomska-podloga` |

   Svaki: intro (GEO — direktan odgovor u prvom pasusu) · `al-table` tehničke
   karakteristike · Primena · 2 FAQ pitanja + **FAQPage JSON-LD** · „Cena na upit" + CTA
   `069 234 00 72` · cross-linkovi (hub, `/industrijski-podovi/`, ESD gde ima smisla).
   Cena prazna na svih 8 (M odluka 13.08, presedan M11).
   Atributi: `pa_debljina`, `pa_materijal`, `pa_antistatican` (taksonomijski) + custom
   „Masa" i „Verzije". Novi termini: debljina 13/14/12–14/20 mm, materijal Nitrilna
   guma / EPDM guma / Nitrilna guma ili EPDM, antistatičan „Opciono (ESD verzija)".
3. **Hub 16672 prevezan** — 8 kartica postalo `<a class="al-card">`, nazivi modela u
   tabeli poređenja postali linkovi, FAQ odgovori linkuju modele, dodat pasus sa
   uzajamnim linkovima ka `/industrijski-podovi/` i `/antistatik-i-elektroprovodljivi-podovi/`.
   Stranica je do sada imala **nula internih linkova** osim `/kontakt/` i `tel:`.
   7.329 → 9.600 B.
4. **Anti-kanibalizacija:** naslov nove kategorije pomeren sa „Ergonomske podloge za
   stajanje" na **„Ergomat podloge — asortiman i modeli"** da ne gađa upite koje hub
   drži (poz. 3,8 „ergonomske podloge", 6,5 „podloga za stajanje"). Presedan:
   `product_cat` 254 vs stranica `/industrijski-podovi/`.
5. Usput: tipfeler „untrašnja" → „unutrašnja" u `rank_math_description` hub-a.

## Verifikacija

12 URL-ova (8 proizvoda + hub + kategorija arhiva + 2 cilja cross-linkova):
**200 / 1×H1 / 0 PHP grešaka** · Rank Math title+description u `<head>` na svima ·
JSON-LD po proizvodu **Product 1× + FAQPage 1×**, bez dupliranja · slike 200 ·
kategorija arhiva prikazuje 8/8 proizvoda · `product-sitemap.xml` 8/8,
`product_cat-sitemap.xml` 1/1 · prefill `?form-naslov=Ponuda: …` radi.
Regresija: `bergo-ultimate-ploca`, `/katalog/`, `kategorija-proizvoda/industrijski-podovi/`,
`/sportske-podloge/` — sve 200 / 1×H1 / 0 grešaka.

## Otvorene akcije

> **Sve tri stavke koje su čekale M zatvorene su istog dana (M odgovor 14.08).**
> Nijedna ne ostaje otvorena pred content freeze.

- [x] **La Ola i La Ola Hygienic — generička fotka OSTAJE (M odluka 14.08).** Oba proizvoda
      koriste `Gumene-ergonomske-podloge-Rubbermats.webp` (15927), isto kao hub stranica od
      ranije. ergomat.com vraća **403**, `intl.ergomat.com` više ne postoji, a pretraga ne
      nalazi ove nazive modela ni kod jednog distributera — dopuna slika iz spec-a **nije bila
      izvodljiva** i više se ne traži.
- [x] **SuperSoft Smooth vs SuperSoft Office — namene OSTAJU kako su na hub stranici
      (M odluka 14.08).** Spec je tražio ukrštanje sa ergomat.com jer namene deluju zamenjene
      (po imenu bi „Office" bio kancelarijski, a tabela mu daje automobilsku/elektronsku
      industriju); izvor je nedostupan pa nije potvrđeno ni demantovano. Opisi ne tvrde ništa
      preko tabele, a FAQ na oba modela razliku svodi na debljinu i masu uz poziv na upit.
- [x] **Kategorija se NE dodaje u meni (M odluka 14.08).** Ulaz ostaje hub
      `/ergonomske-podloge/`; arhiva `/kategorija-proizvoda/ergonomske-podloge/` živi samo
      kroz linkove sa proizvoda i sitemap.
- [ ] Sitno: prilog 15922 se zove `Supersoft-Smooth-–-PU.webp` — **en-dash u imenu fajla**,
      emituje se neenkodiran u `src`. Radi (200 uz enkodiranje), postoji od 2022 i koristi se
      i na hub stranici — ali je klasa problema koja na Linux hostingu ume da zaškripi.
      Preimenovanje je post-live posao. #claude-code

## Beleške / odluke

- 🔴 **Nova lekcija:** `wp_insert_post` bez prijavljenog korisnika primenjuje kses i **tiho
  briše `<script type="application/ld+json">`** iz `post_content` — FAQPage schema ostane
  goli tekst kome `wptexturize` pretvori navodnike u tipografske (`&#8220;@type&#8220;`).
  Prvi prolaz je „uspeo" (8/8 kreirano, 200 na svima), a schema nije postojala. Uhvaćeno
  brojanjem `application/ld+json` u renderu (bilo 1 umesto 2). Fix: `kses_remove_filters()`
  pre upisa. Upisano u [[reference/naucene-lekcije]].
- **(F) kanibalizacija — M odluka 14.08: ne diramo sada.** 4 „dimenzije" stranice ostaju
  kako jesu; posle 16.08 traži odmrzavanje builda. Bloker prebačen u „odloženo M odlukom".
- Cene: svih 8 „na upit", nijedna nije u [[reference/cenovnik]].

## Veze

- Spec: [[migracija/w1-ergonomske-podloge-proizvodi]]
- Analiza iz koje je stavka izašla: [[seo/2026-08-13-kanibalizacija-konsolidacija-plan]] §1 stavka A
- Prethodna sesija na istoj stranici: [[dnevnik/2026-08-13-kanibalizacija-nastavak]]
- Obrazac proizvoda: [[migracija/woodmart-sabloni]] · presedan „na upit": [[migracija/w1-novi-proizvodi-court-builder]]
