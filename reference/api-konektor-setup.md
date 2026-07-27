---
tip: referenca
datum: 2026-07-27
namena: Miroslavljev jednokratni setup checklist za sopstveni Google API konektor (zamena Windsor.ai)
status: čeka-izvršenje
---

# 🔌 Setup — sopstveni Google API konektor

> Ovo je **tvoj** checklist — ja (Claude Code) nemam pristup tvom Google
> nalogu, pa svaki korak ispod moraš ti da izvedeš. Kod (skripte) je već
> napisan i čeka u `.claude/skills/antasline-konektor/`. Nijedan korak
> ovde ne uključuje deljenje lozinke/tokena sa mnom — samo sačuvaj
> fajlove na tačne putanje ispod, ja ih čitam sa diska.

**Zašto van vault-a**: `C:\Users\Miroslav\antasline-connector\` je
potpuno van git repozitorijuma — ništa odavde nikad ne ide na GitHub, čak
ni slučajno.

## Korak 0 — Python zavisnosti (uradio sam ovo umesto tebe)

Venv je već kreiran na `C:\Users\Miroslav\antasline-connector\venv\` i
pakete sam instalirao. Ne treba ništa da radiš ovde osim ako želiš sam da
proveriš: `C:\Users\Miroslav\antasline-connector\venv\Scripts\python.exe --version`

## Korak 1 — Google Cloud projekat

1. Idi na [console.cloud.google.com](https://console.cloud.google.com)
   (isti Google nalog kojim upravljaš GA4/Ads/GSC/GMB za AntasLine)
2. Napravi novi projekat (ili iskoristi postojeći ako ga već imaš za
   nešto vezano za AntasLine — proveri padajući meni gore levo)
3. **APIs & Services → Library** — uključi (Enable) svaki od ova 4:
   - Google Analytics Data API
   - Google Search Console API
   - Google Ads API
   - Business Profile Performance API

## Korak 2 — Service account (za GA4 + GSC, BEZ OAuth-a, najlakši deo)

1. **IAM & Admin → Service Accounts → Create Service Account** — bilo
   koje ime (npr. `antasline-reporting`), ne treba mu nijedna posebna
   uloga na nivou projekta
2. Otvori taj service account → **Keys → Add Key → Create new key → JSON**
   — preuzima se fajl
3. Sačuvaj taj fajl TAČNO kao:
   `C:\Users\Miroslav\antasline-connector\credentials\service-account.json`
4. Otvori JSON fajl, nađi polje `"client_email"` (izgleda kao
   `nesto@tvoj-projekat.iam.gserviceaccount.com`) — to je "email" servisa
5. **GA4**: [analytics.google.com](https://analytics.google.com) → Admin
   → Property Access Management (property 292720335) → dodaj taj email
   kao **Viewer**
6. **GSC**: [search.google.com/search-console](https://search.google.com/search-console)
   → `sc-domain:antasline.com` → Settings → Users and permissions → Add
   user → dodaj isti email (Restricted je dovoljno, čitamo samo)

Posle ovoga, `ga4_report.py` i `gsc_report.py` bi trebalo odmah da rade —
nema čekanja na odobrenje.

## Korak 3 — OAuth Desktop klijent (za Ads + GMB)

Ovi API-ji ne rade preko service account-a — traže da se TI ulogujes i
odobriš pristup (jednom, posle toga se pamti).

1. **APIs & Services → Credentials → Create Credentials → OAuth client ID**
   — tip **Desktop app**, bilo koje ime
2. Ako prvi put praviš OAuth klijent u ovom projektu, tražiće da prvo
   podesiš **OAuth consent screen** — izaberi **External**, popuni samo
   obavezna polja (naziv app-a, tvoj email), status može ostati
   "Testing" (dovoljno je da dodaš svoj email u "Test users" listu na
   tom ekranu)
3. Preuzmi JSON, sačuvaj TAČNO kao:
   `C:\Users\Miroslav\antasline-connector\credentials\oauth-client.json`
4. Pokreni (u terminalu):
   ```
   C:\Users\Miroslav\antasline-connector\venv\Scripts\python.exe "C:\Projekti\antasline-vault\.claude\skills\antasline-konektor\scripts\authorize_oauth.py"
   ```
5. Otvoriće se browser — uloguj se nalogom koji upravlja Ads nalogom
   156-886-0314 i GMB stranicom "Industrijski podovi AntasLine", klikni
   "Allow"/"Dozvoli" na oba traženja pristupa
6. Kad terminal ispiše "Gotovo. Token sacuvan" — ovaj korak je završen,
   ne ponavlja se (osim ako sam obrišeš `token.json` ili opozoveš pristup)

## Korak 4 — Google Ads developer token 🔴 (jedini korak koji čeka Google)

Ovo je jedini deo koji ne zavisi od nas — Google odobrava ručno, obično
1–3 radna dana.

1. Ako **nemaš** Manager (MCC) nalog povezan sa 156-886-0314: napravi ga
   besplatno na [ads.google.com/home/tools/manager-accounts](https://ads.google.com/home/tools/manager-accounts)
   i poveži postojeći nalog 156-886-0314 kao klijenta
2. U MCC nalogu: **Tools & Settings → Setup → API Center**
3. Zatraži **developer token** — popuni kratak formular (namena: interno
   izveštavanje sopstvenih kampanja, ne agencija/reseller)
4. Kad stigne odobrenje (email od Google-a), kopiraj developer token
5. Napravi fajl `C:\Users\Miroslav\antasline-connector\credentials\ads-config.json`
   sa sadržajem (zameni `TVOJ_TOKEN`, `login_customer_id` upiši SAMO ako
   si napravio MCC u koraku 1, bez crtica, inače izbriši taj red):
   ```json
   {
     "developer_token": "TVOJ_TOKEN",
     "login_customer_id": "MCC_ID_BEZ_CRTICA"
   }
   ```

**Dok token ne stigne**: `ads_report.py` će javljati jasnu grešku
("Nedostaje ads-config.json"), ne treba pokušavati ranije — instant
"test" developer token postoji, ali radi SAMO protiv test naloga, ne
protiv pravog 156-886-0314, pa nema smisla za nas.

## Korak 5 — GMB (Business Profile) potvrda

Samo potvrdi da je Google nalog kojim si se ulogovao u koraku 3 zaista
verifikovani vlasnik/menadžer stranice "Industrijski podovi AntasLine"
(proveri na [business.google.com](https://business.google.com) da je
nalog tu). Ako je drugi nalog vlasnik GMB-a, javi mi — treba ponoviti
korak 3 sa tim nalogom (ili odobriti pristup dodatnom korisniku u GMB-u
pre autorizacije).

## Kad javiti da je gotovo

Javi koji koraci su gotovi (ne mora sve odjednom — GA4/GSC mogu da rade
i bez Ads/GMB dela). Testiraću onda direktno protiv pravih podataka i
uporediti sa poslednjim Windsor-baziranim izveštajem za razumnost brojeva.

## Veze
- `.claude/skills/antasline-konektor/SKILL.md` — kako se skripte pokreću
- `reference/identifikatori.md` — javni ID-evi (property/nalog brojevi)
