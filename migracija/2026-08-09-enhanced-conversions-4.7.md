---
tip: spec
naziv: W4 4.7 — Enhanced Conversions (Google Ads)
datum: 2026-08-09
status: lokalni deo ZATVOREN · GTM+Ads deo čeka dan migracije (2026-08-31)
vlasnik: CC (GTM) + M (Ads UI)
veze: "[[2026-07-06-MASTER-PLAN-V2]] W4 4.7 · [[CLAUDE]] §4.1"
---

# Enhanced Conversions — spec i stanje

**Šta je Enhanced Conversions (EC):** Google Ads uz konverziju prima heširane
(SHA-256) podatke koje je korisnik sam uneo — email i telefon. Google ih poredi
sa svojim ulogovanim nalozima i pripisuje konverzije koje inače propadnu
(drugi uređaj, obrisan kolačić, iOS). Efekat: tačnija atribucija, bolji ulaz
za Smart Bidding — što je direktno relevantno sad kad je prag 20–30 plaćenih
konverzija dostignut (4.8).

**Zašto je trebalo posebno rešenje:** konverzija je **pregled
`/hvala-za-poruku/`**, ne submit forme (BLOK A model). Do tog trenutka je
odrađen redirect i vrednosti forme više ne postoje na stranici — EC nema šta
da hešira. Zato se email/telefon prenose kroz `sessionStorage`.

---

## 1. Lokalni deo — ✅ ZATVORENO 2026-08-09

`woodmart-child/functions.php` (uz postojeći `wpcf7mailsent` redirect handler).
Backup: `functions.php.bak-2026-08-09-pre-enhanced-conversions`.

Na uspešno slanje forme **16593** (kontakt) ili **16737** (Brzi upit) — obe
imaju identična imena polja (`form-email`, `form-telefon`) — upisuje se u
`sessionStorage` pre redirecta:

| Ključ | Sadržaj | Za koga |
|---|---|---|
| `al_lead_em` | email, trim + lowercase | Google Ads EC |
| `al_lead_ph` | telefon u **E.164** (`+381692340072`) | Google Ads EC |
| `al_lead_ts` | `Date.now()` — za TTL proveru | oba |
| `al_am_em` | isti email | Meta Pixel (postojeći ugovor) |
| `al_am_ph` | telefon, cifre sa pozivnim (`381692340072`) | Meta Pixel |

**Podaci se šalju kao čist tekst — NE heširati unapred.** Google Ads tag sam
radi SHA-256; unapred heširana vrednost bi bila dvostruko heširana i ne bi se
poklopila ni sa čim.

### Normalizacija telefona (`alNormalizePhoneRS`)
Verifikovano na 12 graničnih slučajeva u pregledaču:

| Unos | Rezultat |
|---|---|
| `069 234 00 72` · `(069) 234-0072` · `069/234-00-72` | `+381692340072` |
| `+381 69 234 00 72` · `00381692340072` · `381692340072` | `+381692340072` |
| `69 234 00 72` (bez vodeće nule) | `+381692340072` |
| `0601234567` | `+381601234567` |
| `12` · `abc` · `""` · 17 cifara | `""` (odbačeno) |

Sanity granica je 9–15 cifara (E.164): bolje ne poslati ništa nego uneti
smeće u Ads match bazu.

### 🔴 Nalaz koji je promenio dizajn — zašto NE `al_am_*` ključevi
Prva ideja je bila iskoristiti postojeće Meta ključeve. **Izmereno uživo da to
ne radi:** GTM tag `Meta Pixel - Base Code` (okida na All Pages) čita i odmah
**briše** (`sessionStorage.removeItem`) `al_am_em`/`al_am_ph`. Na
`/hvala-za-poruku/` posle test-submita ostali su samo `al_lead_*` ključevi —
`al_am_*` su već bili pojedeni. Da EC deli ključeve sa Metom, zatekao bi
prazno i tiho slao konverzije bez EC podataka. Odvojen `al_lead_*` prostor
imena je zato **neophodan, ne stvar ukusa**.

### Usput poboljšano (Meta)
Stari GTM „Capture Lead Data" tag je pisao telefon kao gole cifre **bez
pozivnog broja** (`0692340072`) — Meta traži pozivni broj, pa je taj deo
match-a verovatno oduvek propadao. Lokalni kod sad piše `381692340072`.
Očekivan nusefekat: bolji Event Match Quality posle migracije.

### Verifikacija (2026-08-09, lokalni build, Chrome)
- Obe forme (16593 sa `/kontakt/`, 16737 sa `/industrijski-podovi/`): pravi
  submit → svih 5 ključeva tačno upisano → redirect na `/hvala-za-poruku/` →
  `al_lead_*` prisutni na odredištu ✅
- **Bez regresije na konverzijama** — na `/hvala-za-poruku/` okinuli:
  `analytics.google.com/g/collect` (GA4 `generate_lead`),
  `googleadservices.com/pagead/conversion/966742304/` (Ads),
  `googleadservices.com/ccm/conversion/966742304/`, `facebook.com/tr/` (Meta)
- GTM-TRDT8K9 potvrđeno učitan na localhost · PHP lint čist · 4 ključne
  stranice HTTP 200
- Dva test-lead-a su završila u lokalnom `mail-log.txt` (mu-plugin presreće
  slanje) — ništa nije stvarno poslato; fajl se ionako briše u 3.10.

---

## 2. GTM deo — ČEKA DAN MIGRACIJE (2026-08-31)

**Zašto se ne radi sada:** live sajt još koristi Zion Builder formu koja ne
piše `al_lead_*` ključeve, pa bi tagovi bili prazni hod — neproveren pokretni
deo u živom kontejneru pre migracije, bez ijedne koristi do 31.08.

### 2.1 Dve Custom JavaScript promenljive

`CJS - Lead Email (sessionStorage)`:
```js
function () {
  try {
    var ts = Number(sessionStorage.getItem('al_lead_ts') || 0);
    if (!ts || Date.now() - ts > 600000) return undefined;   // TTL 10 min
    return sessionStorage.getItem('al_lead_em') || undefined;
  } catch (e) { return undefined; }
}
```

`CJS - Lead Phone (sessionStorage)` — identično, samo `al_lead_ph`.

TTL 10 min sprečava da osvežavanje ili kasniji povratak na
`/hvala-za-poruku/` u istom tabu zakači stare podatke na novu konverziju.
`undefined` (ne prazan string) je namerno — GTM tako polje potpuno izostavi.

### 2.2 User-Provided Data promenljiva
Tip **User-Provided Data**, način **Manual configuration**:
- Email → `{{CJS - Lead Email (sessionStorage)}}`
- Phone Number → `{{CJS - Lead Phone (sessionStorage)}}`

### 2.3 Izmena postojećeg konverzionog taga
Samo na tagu za **lead** konverziju — `conversionId 966742304`, label
`ae_gCKL-3sAcEKCi_cwD` (interni `tag_id: 20`, „Lead - forma (GTM)"):
- uključiti „Include user-provided data from your website"
- izabrati User-Provided Data promenljivu iz 2.2

🔴 **NE dirati tag za telefon** (label `QQCBCNDQ_sUcEKCi_cwD`, `tag_id: 21`) —
klik na telefon nema podatke forme, EC tu nema izvor.

Stanje potvrđeno u živom kontejneru 2026-08-09: oba taga imaju
`enableEnhancedConversionsCheckbox: false`, dakle EC nigde nije uključen.

### 2.4 Pojednostavljenje koje ovo omogućava (Meta)
Pošto sajt sada sam piše `al_am_*`, na dan migracije se mogu **obrisati**:
- tag `Meta Pixel - Capture Lead Data`
- trigger `Klik na Posalji (Zion forma)`

`Meta Pixel - Base Code` ostaje **nepromenjen** (čita iste ključeve).
Time se gate stavka „Meta Pixel Manual Advanced Matching prepravka na dan
migracije" iz [[2026-07-06-MASTER-PLAN-V2]] §3 svodi sa „prepiši selektore za
CF7" na „obriši dva objekta" — manje posla i manje rizika na dan migracije.

---

## 3. Ads UI deo — #ceka-miroslav (može i pre migracije)

Google Ads → Goals → Conversions → konverzija **„Lead - forma (GTM)"** →
Settings → **Enhanced conversions**:
1. uključiti
2. metod: **Google Tag Manager**
3. prihvatiti „customer data terms" (Google traži jednokratnu saglasnost)

Bez ovog koraka GTM šalje podatke, a Ads ih ignoriše. Korak je bezopasan i
može se uraditi bilo kad pre 31.08 — ne menja ništa dok GTM deo ne proradi.

---

## 4. Otvoreno pitanje za M (nije blokator)

Kontakt forma nema checkbox za saglasnost za marketinško korišćenje podataka
(samo cookie politika) — isti nalaz koji je već evidentiran za Customer Match
([[.claude/skills/w6-social/SKILL.md]] Faza 0), gde je M 2026-08-07 svesno
odlučio da se nastavi. EC je merenje konverzija (pokriveno `ad_user_data`
consent signalom), pa je pravno lakši slučaj od Customer Match-a, ali je ista
tema — vredi rešiti jednim checkbox-om za oba, ako se već dira forma.

## Veze
[[2026-07-06-MASTER-PLAN-V2]] · [[PROGRESS]] · [[DNEVNIK-NAPRETKA]] · [[CLAUDE]] §4.1
