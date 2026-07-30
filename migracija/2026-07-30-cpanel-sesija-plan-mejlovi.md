---
tip: radni-nalog
datum: 2026-07-30
blok: cpanel-live
status: čeka-izvršenje
izvor: "M5 (Master Plan V2 §4) — šta biva sa kontaktima posle upita"
---

# 🔧 Radni nalog za `[cpanel-live]` sesiju — audit mejlova sa kontakt formi

> **Za Claude-a na cPanel-u:** pre svega pročitaj [[CLAUDE-CODE-instrukcija-CPANEL]]
> i [[PROGRESS]]. Ovo je READ-ONLY istraga (čitanje mejlova) — nema izmena baze/sajta,
> pa `wp db export` nije neophodan, ali ipak uradi ga ako P5 otvori potrebu za bilo
> kakvom izmenom (ne bi trebalo).
>
> ⚠️ **Ovo je pristup pravoj poslovnoj prepisci sa klijentima.** Pravila u dnu
> ("Privatnost — obavezno pročitati pre pisanja dnevnika") nisu opciona.

## Kontekst u dve rečenice

Master Plan V2 §4 (M5) traži odgovor na "šta biva sa ~55–90 kontakata/mesečno" —
imamo broj upita (hvala-proxy, GA4) ali ne znamo da li se na njih odgovara, koliko
brzo, i da li se komunikacija nastavlja. Ovaj nalog to meri direktno iz mejl sandučeta,
bez nagađanja.

## Kako upiti stižu (potvrđeno iz baze, 2026-07-30)

Dve CF7 forme šalju mejl obaveštenje na `[_site_admin_email]` (WP admin mejl —
**proveriti tačnu vrednost na produkciji, P1**, lokalno je test-vrednost i ne
znači da je ista na live-u):

| Forma | ID | Subject šablon | Kada se okida |
|---|---|---|---|
| Kontakt (glavna) | 16593 | `[site_title] "[form-naslov]"` | `/kontakt/` i prefill iz proizvoda ("Zatražite ponudu") |
| Brzi upit | 16737 | `[site_title] Brzi upit — [post_title]` | dno svake usluge/post stranice |

Obe imaju `Reply-To: [form-email]` — **odgovor na mejl ide DIREKTNO klijentu**,
ne nazad na WordPress. To znači: ako je neko odgovorio, u Sent folderu postoji
mejl upućen klijentovoj adresi (ne WordPress-u), poslat posle vremena prijema.
Pošiljalac obaveštenja je `wordpress@antasline.com` — po tome se lead-mejlovi
lako filtriraju u Inbox-u.

Poznat javni kontakt mejl na sajtu (footer/kontakt/mailto linkovi): `office@antasline.com`
— **P1 mora utvrditi da li je ovo isti mejl koji prima CF7 obaveštenja, ili
poseban mailbox.** Ako su različiti, oba se moraju proveriti.

## Poznati brojevi za unakrsnu proveru (ne izmišljati, samo porediti)

- Hvala-proxy (screen_page_view na `/hvala-za-poruku/`, GA4) kumulativ od
  01.06.2026: **93** (v. [[PROGRESS]] ADS sekcija, ažurirano 2026-07-29)
- `lead_form_start` (GTM event, forma-početa) — dostupno u GA4 ako treba dodatan
  presek "počeo pa nije poslao"
- Ads uvezene plaćene konverzije kumulativ: **18/30**

Ako broj lead-mejlova u Inbox-u značajno odstupa od 93 (mnogo manje) — to je
sam po sebi nalaz (mejlovi se gube/spam-filtriraju), prijaviti kao P1.5 nalaz,
ne ćutati.

---

## P1 — 🔴 Utvrdi tačan mailbox i pristup (preduslov za sve ostalo)

1. `wp option get admin_email` na produkciji — koja je stvarna adresa koja
   prima CF7 obaveštenja.
2. cPanel → **Email Accounts** — popiši sve postojeće mailbox-ove na domenu
   (očekivano: `office@antasline.com`, možda i drugi). Proveri da li je
   `admin_email` iz koraka 1 jedan od njih, ili je eksterna adresa (npr. Gmail)
   — ako je eksterna, **cPanel nema pristup toj pošti**, javi Miroslavu da tu
   proveru mora da uradi on sam (ili proslijedi pristup) i preskoči P2–P4 za tu
   granu.
3. cPanel → **Webmail** (Roundcube/Horde, šta god je podrazumevano) na svaki
   relevantan mailbox koji JESTE hostovan na cPanel-u.
4. Proveri da li postoji SPF/DKIM/mejl-slanje problem koji bi objasnio da
   mejlovi ne stižu (cPanel → Email Deliverability) — relevantno ako P1.5
   pokaže veliki gap.

**Gotovo kada:** znaš tačno (a) koja adresa prima lead-ove, (b) da li je
dostupna preko cPanel Webmail-a, (c) ima li DNS/deliverability upozorenja.

---

## P2 — Popis lead-mejlova (Inbox), period 2026-06-01 → danas

1. U Webmail-u pretraga po pošiljaocu `wordpress@antasline.com` (ili subject
   sadrži "Brzi upit" ILI subject format iz P1 tabele), opseg datuma od
   **2026-06-01** (mesec-nula za sve serije konverzija u ovom projektu — v.
   [[CLAUDE]] §4) do danas.
2. Za svaki nađeni mejl zabeleži (u privatnu radnu tabelu, NE u dnevnik — v.
   Privatnost dole): datum, koja forma (kontakt/brzi upit), da li je bio u
   Inbox-u ili u Spam/Junk folderu (**posebno proveri Spam folder** — ako
   lead-ovi tamo završavaju, to je sam po sebi veliki nalaz).
3. Prebroj ukupno i po nedelji/mesecu.

**Gotovo kada:** imaš kompletan broj lead-mejlova za period, uporediv sa 93 iz
konteksta iznad, i znaš da li ijedan sistematski završava u Spam-u.

---

## P3 — Da li je odgovoreno (Sent folder cross-reference)

Za svaki lead iz P2:

1. Izvuci klijentovu email adresu iz tela mejla (`Email: [form-email]` polje).
2. Pretraži **Sent** folder za mejl upućen TOJ adresi, poslat **posle**
   vremena prijema lead-a (Reply-To znači da je odgovor direktno njoj, ne
   WordPress-u — obična "reply" pretraga po threadu neće raditi, mora po
   primaocu).
3. Klasifikuj svaki lead: `odgovoreno <24h` / `odgovoreno 1-7 dana` /
   `odgovoreno >7 dana` / `nema odgovora nađenog`.
4. Ako Miroslav odgovara i sa drugog mejla/telefona (npr. direktno pozove pošto
   vidi broj telefona iz forme) — to se neće videti u Sent folderu. Napomeni
   ovo ograničenje eksplicitno u nalazu, ne prikazuj "nema odgovora" kao
   definitivan zaključak nego kao "nema traga u mejlu" (telefonski odgovor je
   moguć i legitiman, samo nevidljiv ovom metodom).

**Gotovo kada:** imaš agregatnu tabelu (brojevi po kategoriji, ne pojedinačni
klijenti) — % odgovoreno, prosečno vreme odgovora, % bez traga odgovora.

---

## P4 — Da li se komunikacija nastavila (dubina thread-a)

Za lead-ove koji JESU dobili odgovor (iz P3): proveri da li postoji **drugi**
mejl od klijenta posle prvog odgovora (novi inbound u Inbox-u sa iste adrese,
u sledećih 14 dana) — to znači da je razgovor nastavljen (pregovori, dodatna
pitanja), ne samo jednokratan odgovor koji je ostao bez daljeg traga.

Klasifikuj: `1 razmena (mi odgovorili, ćutanje)` / `2+ razmene (nastavljen
razgovor)` / `nastavljeno telefonom (napomena u mejlu to sugeriše — npr. "kao
što smo se čuli telefonom")`.

**Gotovo kada:** imaš procenat lead-ova gde se vidi da je razgovor stvarno
otišao dalje od prvog odgovora.

---

## P5 — Sinteza i prijava (bez izmene sajta)

Sastavi kratak nalaz (agregatno, v. Privatnost):

- Ukupno lead-mejlova u periodu vs. 93 hvala-proxy — gap objašnjen ili ne
- % odgovoreno / prosečno vreme / % bez traga
- % gde je razgovor nastavljen
- Bilo kakav sistemski nalaz (Spam folder, mejlovi koji uopšte ne stižu, SPF/DKIM
  problem, obrazac da se određena vrsta upita ignoriše)

Ovo direktno zatvara (ili delimično zatvara, ako mailbox nije dostupan) M5 iz
Master Plan V2 §4 — upiši nalaz tamo i u [[PROGRESS]].

---

## 🔒 Privatnost — obavezno pročitati pre pisanja dnevnika

Vault je git repo (privatan, ali sinhronizovan i čuva punu istoriju). **Nikad
ne kopirati u DNEVNIK-NAPRETKA/PROGRESS**: imena klijenata, email adrese,
telefone, sadržaj poruka/pregovora. Piši isključivo agregatne brojeve i
obrasce ("38/93 odgovoreno u 24h", ne "Petar Petrović iz firme X nije dobio
odgovor"). Ako je neki pojedinačan slučaj toliko bitan da mora da se pomene
(npr. veliki propušten posao), formuliši ga anonimizovano ("jedan B2B upit za
industrijski pod od >500m² ostao bez odgovora 12 dana") ili ga prijavi
Miroslavu direktno u razgovoru, ne u fajl.

## Rekapitulacija: šta NE raditi u ovoj sesiji

- ❌ Ne odgovarati na stare lead-ove niti slati bilo kakav mejl u klijentovo ime
- ❌ Ne menjati CF7 podešavanja (recipient/Reply-To) — ovo je čisto čitanje
- ❌ Ne kopirati sadržaj mejlova (PII) u vault fajlove
- ❌ Ne diplomatizovati/nagađati "verovatno je odgovorio telefonom" kao
  potvrđen zaključak — samo kao mogućnost, jasno razdvojeno od onoga što je
  stvarno viđeno u mejlu

## Posle sesije

Prati [[CLAUDE-CODE-instrukcija-CPANEL]]: `git pull` → append u
[[DNEVNIK-NAPRETKA]] sa `[cpanel-live]` tagom (agregatni nalaz, v. Privatnost
gore) → osveži [[PROGRESS]] i M5 red u [[2026-07-06-MASTER-PLAN-V2]] → commit+push.
