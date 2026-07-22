---
tip: blok
blok: D
status: odlozeno-W7
azurirano: 2026-07-22
---

# BLOK D — AI chat za posetioce sajta (odlučeno, W7 posle live-a)

> Nastalo iz sesije 2026-07-22 ("razmisli da se doda neki AI čet"). Miroslav potvrdio
> (AskUserQuestion): **customer-facing chatbot** — odgovara na pitanja posetilaca o
> proizvodima/ceni/specifikacijama i kvalifikuje lead-ove. Nije internа analitika (to bi
> bio drugačiji alat, odbačena opcija ove sesije). **Implementacija nije počela.**
>
> **Sve 4 otvorene odluke potvrđene istog dana (druga sesija, AskUserQuestion):**
> timing = posle live-a (W7) · budžet/API ključ vlasnik = nerešeno, odlaže se do
> stvarnog početka implementacije · obim MVP = Q&A + lead-kvalifikacija (forma unutar
> chata odmah, ne samo katalog) · `al_interest` kolačić = DA, bot ga koristi za kontekst
> posetioca (isti mehanizam kao [[migracija/...|hvala-za-poruku]] personalizacija).
> Ne bira se kao glavni zadatak dok W1–W5 traju — samo se planira unapred da W7 ne
> zatekne nespremne (isto obrazloženje kao W7 sezonski kalendar u Master Planu §8).

## Zašto sad nije urgentno

Prioritet projekta je i dalje **Tehnička → SEO → Ads** do go-live-a (2026-08-31, ~5-6
nedelja). Chatbot ne blokira ništa na tom putu i nema postojeću infrastrukturu (nula koda,
nula troška do sad) — preporuka je da se **MVP prototip pravi posle live-a** (W7,
paralelno sa W6 social ritmom), osim ako Miroslav želi da ga forsira pre migracije.

## Arhitektura (predlog)

**Nije fine-tuning — nema "treniranja" u ML smislu.** Pristup je RAG (Retrieval Augmented
Generation): LLM dobija pitanje posetioca + relevantne delove kataloga/FAQ-a kao kontekst
u svakom pozivu, i odgovara na osnovu toga. "Trening" se svodi na:

1. **Baza znanja** (retrieval izvor) — generiše se iz onoga što već postoji, ne piše se
   ručno ispočetka:
   - WooCommerce katalog: naziv, atributi, specifikacije, kategorija, cena (gde postoji,
     inače "na upit" — isti catalog_mode princip kao sajt)
   - FAQ sadržaj sa FAQPage schema stranica (već strukturiran, lako se izvlači)
   - Silo/kategorija stranice (industrijski, sport, terase, ESD) — glavni opisni tekst
   - Kontakt info, radno vreme, telefon (072 prioritet — [[reference/naucene-lekcije]])
2. **Pretraga** — s obzirom na veličinu kataloga (~150 proizvoda, ~100-tak stranica),
   puna vektorska baza je verovatno prekomplikovana za MVP. Jednostavnija opcija: MySQL
   FULLTEXT pretraga (WP već ima `wpGs_posts`/`wpGs_postmeta`) ili laka embedding pretraga
   nad malim brojem dokumenata (može stati u SQLite/JSON fajl, bez posebnog vektor
   servisa). Odluka o ovome je implementaciona, ne menja pristup.
3. **LLM poziv** — Claude API (Haiku klasa dovoljna za B2B kvalifikaciju upita, jeftinije
   od Sonnet/Opus) sa sistem-promptom koji sadrži: ko je AntasLine, tvrda pravila (vidi
   ispod), i rezultate pretrage kao kontekst za trenutno pitanje.
4. **Frontend** — floating widget (donji desni ugao), JS + WP REST API custom ruta
   (`/wp-json/al/v1/chat`), isti `al-` dizajn sistem kao ostatak sajta (ne novi UI jezik).
5. **Lead capture** — kad bot proceni da je posetilac ozbiljan upit (traži cenu/kontakt),
   ponudi formu (ime+telefon+email) koja se uklapa u **postojeći konverzioni model**:
   submit → redirect `/hvala-za-poruku/?src=chat` → GTM `generate_lead` (isti obrazac kao
   Brzi upit i Court Builder, vidi `functions.php` redirect logiku).

## Tvrda pravila za sistem-prompt (ne pregovarati)

- **Nikad ne izmišljati cene, specifikacije ili dostupnost** — isto pravilo kao svaki
  ljudski pisan sadržaj na sajtu (CLAUDE.md, ponovljeno kroz ceo projekat). Ako podatak
  nije u bazi znanja, bot kaže "pošaljite upit za tačnu ponudu", ne nagađa.
  - **Epoksid = conquest, ne prodaja** — ako posetilac pita za epoksidne podove, bot
    NIKAD ne sme delovati kao da ih AntasLine prodaje; usmerava ka Ecotile/PVC alternativi
    i članku `/epoksidni-podovi-ili-ecotile-podovi/`, isto pravilo kao SEO/Ads (CLAUDE.md §1).
- Uvek nudi telefon **072** kao brzu alternativu (dominantan kanal, [[reference/naucene-lekcije]]).
- Ne tvrdi identitet ("Ja sam Miroslav" i sl.) — jasno je AI asistent, ne osoba.
- Ako pitanje izlazi iz domena (van proizvoda/usluga/kontakta), bot preusmerava na
  kontakt formu umesto da nagađa.

## Trening/održavanje posle lansiranja (ne jednokratno)

"Trening" u praksi = nedeljni/mesečni pregled transkripata razgovora (slično nedeljnom
izveštaju, [[dnevnik/ADS-DNEVNIK]] ritam): koja pitanja bot nije mogao dobro da odgovori →
dopuni bazu znanja (nova FAQ stavka, precizniji opis proizvoda) → ne menja se model, menja
se SADRŽAJ koji model vidi. Ovo je kontinuiran, jeftin proces — ne treba ML ekspertizu.

## Odluke (potvrđeno 2026-07-22, AskUserQuestion)

- [x] **Timing**: posle live-a (2026-08-31), kao W7 — ne forsira se pre migracije.
- [x] **Budžet**: API ključ/nalog vlasnik nerešen — odlaže se do stvarnog početka
  implementacije, nije blokator za planiranje.
- [x] **Obim MVP-a**: Q&A + lead-kvalifikacija odmah (forma unutar chata kad bot proceni
  ozbiljan upit), ne samo pasivni katalog Q&A.
- [x] **`al_interest` kolačić**: DA — bot sme da ga koristi za kontekst posetioca (isti
  mehanizam kao hvala-za-poruku personalizacija, 2026-07-22 sesija).
