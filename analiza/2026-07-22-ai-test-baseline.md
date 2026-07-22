---
tip: analiza
datum: 2026-07-22
naziv: Prvi mesečni AI test — 5 fiksnih promptova (ChatGPT, bez naloga)
izvor: [[seo/geo-ai-plan]] §5
status: gotovo
---

# 🤖 AI test baseline — 2026-07-22

Prvi put izvršen mesečni AI test iz [[seo/geo-ai-plan]] §5 (metod ranije definisan,
nikad izvršen). Alat: ChatGPT (chatgpt.com), **pravi Incognito prozor bez Google
naloga** (Claude-in-Chrome ekstenzija nema podrazumevanu dozvolu za Incognito —
prva dva pokušaja su pogrešno pogodila ulogovan Miroslavljev nalog, ispravljeno
tek posle uključivanja "Dozvoli u anonimnom režimu" u `chrome://extensions`).
Svaki prompt u **novom, čistom razgovoru** (ne nastavak iste konverzacije) da
se izbegne kontaminacija odgovora prethodnim pitanjem.

## Rezultati

| # | Prompt | AntasLine pomenut? | Detalji |
|---|---|---|---|
| 1 | "Ko prodaje industrijske PVC podove u Srbiji?" | ✅ Da (2. mesto) | Google Maps kartica + tekst lista, 4.7★. Opis: "bavi se industrijskim podnim rešenjima, uključujući PVC sisteme". **Bez URL citata** — izvori u odgovoru: woodmaster.rs, pvcpodovi.rs (ne antasline.com) |
| 2 | "Koja je najbolja podloga za košarkaški teren u dvorištu i koliko košta?" | ❌ Ne | Potpuno generički odgovor (beton/asfalt/akril/PP pločice/guma + cene €/m²), bez ijednog brenda/dobavljača, bez web pretrage/citata |
| 3 | "Alternativa epoksidnom podu za proizvodnu halu" | ❌ Ne | 🔴 Nabrojane samo PU, PU-cement, polirani beton, kvarcni pod, MMA — **modularni PVC/interlocking (Ecotile kategorija) nije ni pomenut kao opcija**. Direktno relevantno za conquest članak 2542 |
| 4 | "Podloge za terasu koje se ne lepe - gde kupiti u Beogradu?" | ❌ Ne | 🔴 AI "ne lepe" tumači isključivo kao WPC deking (ELLAdeck, Deking Zona, Deking.rs) — Bergo klik-podloge (AntasLine-ov proizvod tačno za ovaj upit) nisu prepoznate kao kategorija |
| 5 | "Ko postavlja sportske terene u Srbiji?" | ✅ Da (2. mesto) | **Sa pravim URL citatom** (`antasline.com`, izvor "A"), uz tereni.rs/inteko.co/stolarije.com. Opis: "izvodi montažu sportskih podloga za otvorene i zatvorene terene, multifunkcionalne terene i dečija igrališta" |

## Skor: 2/5 pominjanja (1 sa URL citatom, 1 bez)

## Nalazi

- **Najjača pozicija**: "Ko postavlja sportske terene" — pravi citat na antasline.com. Sportski tereni klaster (najveći GSC saobraćaj, 923+ klika na kosarkaske-konstrukcije) se poklapa sa najjačim AI signalom — konzistentno sa organskim rezultatima.
- **🔴 Conquest gap (prompt 3)**: AI ne zna da modularni PVC/Ecotile postoji kao alternativa epoksidu — ni AntasLine ni ijedan konkurent nije pomenut u toj kategoriji. Ovo je upravo scenario za koji postoji conquest članak (`/epoksidni-podovi-ili-ecotile-podovi/`, post 2542) — GSC pozicija za "epoksi podovi" je poz. 26 (van AI vidokruga). Potvrđuje raniju CLAUDE.md napomenu da conquest treba SEO refresh.
- **🔴 Bergo/terase gap (prompt 4)**: "podloge koje se ne lepe" AI automatski povezuje sa WPC deking kategorijom, ne sa klik-sistemima kao što je Bergo. Vredi razmotriti da li postojeći sadržaj na `/spoljnje-podne-obloge/` dovoljno eksplicitno koristi frazu "bez lepljenja"/"klik sistem" za bolje AI poklapanje.
- **Prompt 2 (košarkaški teren dvorište + cena)** — nijedan brend pomenut, cisto generička tabela. Moguće da je ChatGPT ovde odgovorio iz internog znanja bez web pretrage (nema Maps kartica kao kod 1/4/5) — vredi ponoviti test i videti da li se ponaša drugačije.

## Metodološka napomena za sledeći mesec
- Ponoviti identičnim promptovima (fiksni skup, ne menjati redosled/formulaciju) radi uporedivosti trenda
- Pratiti da li Incognito i dalje radi (ekstenzija dozvola je sad uključena — ne bi trebalo da se ponovi problem iz ove sesije)
- Razmotriti dodavanje 6. prompta ciljano na "bez lepljenja terasa" pošto je ovaj mesec otkrio jasan gap

## Veze
[[seo/geo-ai-plan]] · [[CLAUDE]] §1 (conquest strategija) · [[analiza/2026-07-04-snapshot-full]] §2.2 (AI Assistant GA4 kanal, baseline 9 korisnika/90d)
