---
tip: referenca
datum: 2026-07-06
namena: Pregled Claude Code skilova za AntasLine projekat
---

# 🛠️ Claude Code skilovi — pregled

> Skilovi žive u `.claude/skills/<ime>/SKILL.md` (skriveni folder — u
> Obsidian-u se ne vidi, otvara se iz Claude Code-a ili file explorera).
> Pozivaju se sa `/<ime>` u Claude Code-u ili prirodnom rečenicom iz
> kolone "Okidač". Ovaj fajl je podsetnik ŠTA koji skill radi.

| Skill | Okidač (primeri) | Šta radi |
|---|---|---|
| `/antasline-sesija` | "nastavljamo", "gde smo stali", "sledeći zadatak" | Master protokol sesije |
| `/obogati-proizvod` | "obogati proizvode", "sredi Ecotile proizvode" | Šablon obogaćivanja Woo proizvoda |
| `/w6-social` | "social", "newsletter", "Instagram post", "W6" | Social/email/GMB workstream |
| `/nedeljni-izvestaj` | "nedeljni izveštaj", "kako stojimo" | 7d vs 7d izveštaj kroz Windsor |

---

## /antasline-sesija — master protokol

**Otvaranje:** čita [[PROGRESS]] → poredi datum sa N1–N8 rasporedom iz
[[2026-07-06-MASTER-PLAN-V2]] → preskače #ceka-miroslav blokirano →
predlaže 1 glavni zadatak.

**Izvršavanje:** mini-protokol po workstream-u (W1 dizajn sa
[[migracija/woodmart-sabloni]] · W2 content sa [[seo/plan-novih-stranica]] ·
W3 tehnička/migracija · W4 Ads sa [[dnevnik/ADS-DNEVNIK]] · W5 tracking) +
brza referenca okruženja (DB, backup, PHP) + standard verifikacije
(200, 1×H1, JSON-LD, linkovi).

**Zatvaranje:** unos u [[DNEVNIK-NAPRETKA]] → red u [[PROGRESS]] →
štikliranje u Master planu → lekcije u [[reference/naucene-lekcije]].

**Tvrda pravila:** live se ne dira · epoksid se ne prodaje · bez izmišljenih
brojeva · Yoast ostaje · backup pre destruktivnog.

## /obogati-proizvod — Woo proizvodi

Stanje pre (audit 2026-07-06): 37 proizvoda — 0 cena, 0 Yoast meta,
0 galerija, 0 atributa, 14 thin opisa.

**Šablon (8 tačaka):** globalni atributi (debljina/dimenzije/nosivost/boje…) →
cena od–do ili "na upit" → galerija 3–6 slika → strukturiran opis (intro →
tabela specifikacija → primene → ugradnja → 3 FAQ → CTA 072) → Yoast →
Product JSON-LD → PDF tehnički list → cross-link trougao
(proizvod ↔ kategorija ↔ silo).

**Redosled:** Ecotile linija → košarkaške konstrukcije → Mosolut/Bergo →
DuraStripe batch → Ergomat batch → senzori. ~30–45 min po proizvodu,
batch 3–5 po sesiji.

## /w6-social — društvene mreže, email, GMB

**Faza 0 (pre live-a, jeftino):** popis profila → [[reference/drustvene-mreze]] ·
M5 odgovor (gde stoje email-ovi upita) · GMB paket (UTM fix + recenzije 6→20) ·
saglasnost checkbox na formi · `sameAs` u JSON-LD.

**Faza 1 (od 01.09):** IG+FB 2×/ned (B2C reference, before/after) ·
LinkedIn 1×/ned (B2B case study — Quectel!) · YouTube 1×/mes (video montaže) ·
GMB 1×/mes. Svaki završen posao = post + recenzija.

**Faza 2:** email lista → Customer Match u Ads (zaobilazi prag publika) →
follow-up sekvenca → sezonski newsletter (feb: terase).

**Faza 3 (~oktobar):** Meta Ads test 5–10k RSD/mes, remarketing terase/bazeni.

**UTM standard:** `utm_source=<mreža>&utm_medium=social&utm_campaign=<tema-YYYYMM>`
— inače GA4 baca u Unassigned.

## /nedeljni-izvestaj — 7d vs 7d

Vuče kroz Windsor: GA4 (korisnici, sesije, generate_lead/tel/mailto,
hvala-proxy) · Ads po kampanji (RSD, klikovi, CTR, CPC, konverzije) ·
GSC 28d prilike (pozicije 5–15, nizak CTR).

Format: tabele → "Ukupan broj pravih konverzija do sada: N" → SEO prilike →
napomene → **"Akcija nedelje: …"**. Pravila: <5% = stabilno · "Nema podataka
za [izvor]" umesto izmišljanja · pad posle tracking čišćenja ≠ pad performansi.

---

## Održavanje

- Novi skill → dodaj red u tabelu gore + sekciju ispod
- Izmena skila → ažuriraj i ovaj pregled (da ne laže)
- Skilovi postaju vidljivi Claude Code-u od sledeće sesije posle kreiranja
