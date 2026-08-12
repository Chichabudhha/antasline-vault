---
tip: referenca
datum: 2026-08-12
namena: Pregled Claude Code skilova za AntasLine projekat
---

# 🛠️ Claude Code skilovi — pregled

> Skilovi žive u `.claude/skills/<ime>/SKILL.md` (skriveni folder — u
> Obsidian-u se ne vidi, otvara se iz Claude Code-a ili file explorera).
> Pozivaju se sa `/<ime>` u Claude Code-u ili prirodnom rečenicom iz
> kolone "Okidač". Ovaj fajl je podsetnik ŠTA koji skill radi.

| Skill                | Okidač (primeri)                                  | Šta radi                          |
| -------------------- | ------------------------------------------------- | --------------------------------- |
| `/antasline-sesija`  | "nastavljamo", "gde smo stali", "sledeći zadatak" | Master protokol sesije            |
| `/obogati-proizvod`  | "obogati proizvode", "sredi Ecotile proizvode"    | Šablon obogaćivanja Woo proizvoda |
| `/w6-social`         | "social", "newsletter", "Instagram post", "W6"    | Social/email/GMB workstream       |
| `/nedeljni-izvestaj` | "nedeljni izveštaj", "kako stojimo"               | 7d vs 7d izveštaj kroz konektor   |
| `/antasline-ads`     | "ads", "oglasi", "CPC", "budžet", "RSA", "PMax"   | W4 Google Ads playbook            |
| `/antasline-konektor`| "konektor", "poveži API", "povuci podatke"        | GA4/GSC/Ads/GMB podaci (read-only)|
| `/gemini-vizuali`    | "slike proizvoda", "unapredi fotke", "vizuali"    | Foto/video preko Gemini API       |
| `/dnevni-video`      | "dnevni video", "Flow", "animiraj fotku"          | Jedan Veo kadar dnevno            |
| `/agy-delegat`       | "agy", "antigravity", "delegiraj", "štedi tokene" | Masovno čitanje **fajlova** na Gemini (kvota!)|
| `/ollama-lokalni`    | "ollama", "lokalni model", "qwen", "razvrstaj upite" | Sažimanje **brojki** iz konektora, lokalno i bez kvote |
| `/woodmart-theme`    | rad na temi, CSS/builder/CF7 problemi             | Gotcha-i WoodMart teme (globalni) |
| `/modern-web-guidance`| pre novog CSS/JS obrasca                         | Chrome vodiči + Baseline podaci   |

---

## /antasline-sesija — master protokol

**Otvaranje:** čita [[PROGRESS]] → poredi datum sa N1–N8 rasporedom iz
[[2026-07-06-MASTER-PLAN-V2]] → preskače #ceka-miroslav blokirano →
**ponedeljkom: brzi pregled svih zavisnosti (sekcija 4)** da kašnjenje
isplivava odmah, ne u N8 gužvi → predlaže 1 glavni zadatak.

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

## /antasline-ads — W4 Google Ads playbook

Nalog `156-886-0314`, RSD. Ono što skill nosi, a dnevnik ne:

**Redosled dijagnostike** kad padnu prikazi / skoči CPC: obe kampanje istovremeno
= nivo naloga (balans/verifikacija/**ili namerna pauza — prvo pitaj M**) →
`search_budget_lost_impression_share` **po danu, ne nedeljni prosek** (spike dan
sakriven u proseku: ECOTILE gubi 50% prikaza na 2 od 12 dana pri cap-u 1.300 RSD)
→ `rank_lost` = Quality Score, budžet ga ne rešava.

**Srpska morfologija u negativnim rečima**: broad negative nije morfološka —
`epoksidna` ne blokira `epoksidni/epoksidnog/epoksi`. Curelo ~16% budžeta dok
se 06.07 nije dodalo 7 falećih oblika. Za svaki pojam nabroj padežne oblike.

**Licitiranje**: prag 20–30 plaćenih konverzija je pređen (26), ali prelazak na
Maximize Conversions odložen na ~01.09 — learning traje do ~3 nedelje i svaka
značajna izmena ga restartuje, a migracija 24.08 menja finalne URL-ove svih
oglasa. Redosled: migracija → stabilizacija → strategija.

**Migracija-checklist za Ads** (finalni URL-ovi, sitelink asseti, 301 lanci,
disapproval-i posle nove landing stranice) + kad ima smisla PMax/Demand Gen/
Shopping (nijedan još).

🔴 Write akcije (RSA upis, budžet, pauza, strategija) = **Miroslav u Ads UI**;
CC priprema tekst i brojke. Konektor je read-only namerno.

## /nedeljni-izvestaj — 7d vs 7d

Vuče kroz sopstveni konektor (Windsor istekao 2026-07-27): GA4 (korisnici,
sesije, generate_lead/tel/mailto,
hvala-proxy) · Ads po kampanji (RSD, klikovi, CTR, CPC, konverzije) ·
GSC 28d prilike (pozicije 5–15, nizak CTR).

Format: tabele → "Ukupan broj pravih konverzija do sada: N" → SEO prilike →
napomene → **"Akcija nedelje: …"**. Pravila: <5% = stabilno · "Nema podataka
za [izvor]" umesto izmišljanja · pad posle tracking čišćenja ≠ pad performansi.

---

## Reference koje skilovi koriste (nisu skilovi, ali su ulaz)

| Fajl | Šta | Ko popunjava |
|---|---|---|
| [[reference/cenovnik]] | Jedinstveni cenovnik (M10) — Claude vuče odavde za W2 stranice i proizvode, umesto da pita svaki put | Miroslav, jednom |
| [[reference/drustvene-mreze]] | Popis social profila (W6 Faza 0) — popunjeno 2026-07-07 | Miroslav |

## Održavanje

- Novi skill → dodaj red u tabelu gore + sekciju ispod
- Izmena skila → ažuriraj i ovaj pregled (da ne laže)
- Skilovi postaju vidljivi Claude Code-u od sledeće sesije posle kreiranja
