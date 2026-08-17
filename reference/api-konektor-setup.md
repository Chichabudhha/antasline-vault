---
tip: referenca
datum: 2026-07-27
namena: Miroslavljev jednokratni setup checklist za sopstveni Google API konektor (zamena Windsor.ai)
status: skoro-gotovo — samo GMB preostaje
---

# 🔌 Setup — sopstveni Google API konektor

> Ovo je **tvoj** checklist — ja (Claude Code) nemam pristup tvom Google
> nalogu, pa svaki preostali korak ispod moraš ti da izvedeš. Kod
> (skripte) je već napisan i čeka u `.claude/skills/antasline-konektor/`.

**Zašto van vault-a**: `C:\Users\Miroslav\antasline-connector\` je
potpuno van git repozitorijuma — ništa odavde nikad ne ide na GitHub, čak
ni slučajno.

## ✅ Već gotovo (nađeno 2026-07-27 u `C:\Miroslav\Antas line\AI\Keys\`)

Imao si već napravljen GCP projekat **`mcp-za-claude`** sa 4 service
account ključa (ga4/gsc/ads/gmb) — kopirao sam ih u
`credentials/{ga4,gsc,gmb,ads}-service-account.json`. Testirano uživo:

- ✅ **GA4** — radi, potvrđeno pravim podacima (775 korisnika, 882
  sesija, 23 generate_lead za 20-26.07)
- ✅ **GSC** — radi, potvrđeno pravim podacima (top upiti sa
  prikazima/pozicijom za 29.06-24.07)
- 🔴 **GMB** — servisni ključ postoji, ali "My Business Account
  Management API" nije uključen u projektu → vidi Korak A ispod
- ⚠️ **Ads** — servisni ključ (`claude-mcp-ads`) **ne može da se koristi**
  za Google Ads API (Google to tehnički ne dozvoljava — Ads API zahteva
  isključivo OAuth sa pravim nalogom, ne service account, bez izuzetka).
  Taj ključ ostaje neiskorišćen za ovu svrhu.
- ✅ **OAuth Desktop klijent — ZAVRŠENO 2026-07-27** — Miroslav dao
  `client_secret_2_...json` iz istog `Keys` foldera, autorizacija
  odrađena (`authorize_oauth.py`), `token.json` sačuvan sa oba scope-a
  (`adwords` + `business.manage`). Korak C ispod je sad istorijski
  (samo za slučaj da se token ikad mora ponovo generisati).
  Preostaje SAMO Korak D (Ads developer token) za Ads, i Korak A/B za GMB.

## Korak A — Uključi preostale API-je u `mcp-za-claude` (5 min)

Idi na [console.cloud.google.com](https://console.cloud.google.com),
izaberi projekat **mcp-za-claude**, pa **APIs & Services → Library** —
uključi (Enable) svaki od ova 3 (GA4 Data API i Search Console API su
očigledno već uključeni, pošto rade):
- My Business Account Management API
- My Business Business Information API
- Business Profile Performance API

Direktan link za prvi (Google ga je već dao u poruci o grešci):
https://console.developers.google.com/apis/api/mybusinessaccountmanagement.googleapis.com/overview?project=561984657473

## Korak B — GMB service account kao Manager na Business Profile nalogu

Posle Koraka A, servisni nalog `id-business-profile-performanc@mcp-za-claude.iam.gserviceaccount.com`
i dalje verovatno neće imati pristup SAMOJ stranici "Industrijski podovi
AntasLine" dok ga ručno ne dodaš kao managera:

1. Idi na [business.google.com](https://business.google.com) →
   "Industrijski podovi AntasLine" → **Business Profile settings → People
   and access** (ili "Menadžeri")
2. Dodaj `id-business-profile-performanc@mcp-za-claude.iam.gserviceaccount.com`
   kao **Manager**
3. Ako opcija "dodaj email" ne prihvata service account adresu (ponekad
   UI to ne dozvoljava direktno) — javi mi, u tom slučaju GMB ide preko
   OAuth Desktop klijenta (Korak C ispod) umesto service account-a,
   `gmb_report.py` je već napisan da automatski proba oba puta.

## Korak C — OAuth Desktop klijent ✅ ZAVRŠENO 2026-07-27 (istorijski, samo ako treba ponoviti)

1. U `mcp-za-claude` projektu: **APIs & Services → Credentials → Create
   Credentials → OAuth client ID** — tip **Desktop app**, bilo koje ime
2. Ako prvi put praviš OAuth klijent u ovom projektu, tražiće da prvo
   podesiš **OAuth consent screen** — izaberi **External**, popuni samo
   obavezna polja, status može ostati "Testing" (dodaj svoj email u "Test
   users" listu)
3. Preuzmi JSON, sačuvaj TAČNO kao:
   `C:\Users\Miroslav\antasline-connector\credentials\oauth-client.json`
4. Pokreni (u terminalu):
   ```
   C:\Users\Miroslav\antasline-connector\venv\Scripts\python.exe "C:\Projekti\antasline-vault\.claude\skills\antasline-konektor\scripts\authorize_oauth.py"
   ```
5. Otvoriće se browser — uloguj se nalogom koji upravlja Ads nalogom
   156-886-0314 (i GMB stranicom ako Korak B nije uspeo), klikni
   "Allow"/"Dozvoli"
6. Kad terminal ispiše "Gotovo. Token sacuvan" — ovaj korak je završen

## Korak D — Google Ads developer token ✅ ZAVRŠENO 2026-07-27

Miroslav dao developer token, upisan u `ads-config.json`, testirano uživo
— **radi odmah, bez čekanja na odobrenje** (Basic access je očigledno već
bio aktivan). `ads_report.py` vraća prave brojeve (npr. 20-26.07: 6.030
RSD, 263 klika, 5 konverzija na 2 aktivne kampanje). Ceo Ads deo
konektora je sad gotov.

<details>
<summary>Istorijski koraci (za slučaj da treba nov token/nalog)</summary>

Jedini deo koji ne zavisi od nas — Google odobrava ručno, obično 1–3
radna dana.

1. Ako **nemaš** Manager (MCC) nalog povezan sa 156-886-0314: napravi ga
   besplatno na [ads.google.com/home/tools/manager-accounts](https://ads.google.com/home/tools/manager-accounts)
   i poveži postojeći nalog 156-886-0314 kao klijenta
2. U MCC nalogu: **Tools & Settings → Setup → API Center**
3. Zatraži **developer token** — popuni kratak formular (namena: interno
   izveštavanje sopstvenih kampanja, ne agencija/reseller)
4. Kad stigne odobrenje (email od Google-a), kopiraj developer token
5. Napravi fajl `C:\Users\Miroslav\antasline-connector\credentials\ads-config.json`
   sa sadržajem (zameni `TVOJ_TOKEN`, `login_customer_id` upiši SAMO ako
   si napravio MCC, bez crtica, inače izbriši taj red):
   ```json
   {
     "developer_token": "TVOJ_TOKEN",
     "login_customer_id": "MCC_ID_BEZ_CRTICA"
   }
   ```

**Dok token ne stigne**: `ads_report.py` javlja jasnu grešku, ne treba
pokušavati ranije — instant "test" developer token radi SAMO protiv test
naloga, ne protiv pravog 156-886-0314.

</details>

## Korak E — Kredencijali NA SERVERU (cPanel), za `scan_leads.py`/`customer_match_upload.py`

Ova dva skripta (email → Google Ads Customer Match) se pokreću **na
cPanel serveru** (`[cpanel-live]` sesija preko SSH-a), ne lokalno — mailbox
`office@antasline.com` fizički živi tamo. Server ima svoju odvojenu
kopiju konektor foldera, van git-a, po istom principu:

1. Na serveru napravi `~/antasline-connector/credentials/` (analogno
   lokalnom `C:\Users\Miroslav\antasline-connector\credentials\`).
2. Prekopiraj (scp/SFTP/cPanel File Manager) sa Windows mašine na server:
   - `oauth-client.json`
   - `token.json`
   - `ads-config.json`

   Ovo je isti Google Ads nalog (156-886-0314) — refresh token u
   `token.json` je prenosiv fajl, ne treba ponovni OAuth "Allow" korak na
   serveru (koji ionako nema interaktivni browser).
3. Ako `scan_leads.py --imap` fallback zatreba (Maildir čitanje sa diska
   ne uspe): dodaj `~/antasline-connector/credentials/mail-imap.json` sa
   `{"password": "..."}` (lozinka za `office@antasline.com` mailbox).
4. Proveri da `python3`/`pip` rade u cPanel Python App okruženju i
   instaliraj isti `requirements.txt` (`google-ads` biblioteka).

`leads.csv` i `scan-state.json` će se sami napraviti u
`~/antasline-connector/customer-match/` pri prvom pokretanju
`scan_leads.py` — `leads.csv` je obična čitljiva tabela, dostupna bilo kad
preko cPanel File Manager-a/SFTP-a/SSH-a.

## Korak F — Google traži verifikaciju aplikacije (✅ ZATVORENO 2026-08-17)

> 🟢 **2026-08-17 — app je prebačena u *In production*, ovaj korak više ne važi u praksi.**
> M je u konzoli (`APIs & Services → OAuth consent screen → Audience`) kliknuo
> **Publish app**. Posledica: refresh token **više ne ističe na 7 dana**, „App not
> verified" ekran i „Advanced → unsafe" zaobilaženje nisu više deo toka, a Test users
> lista je irelevantna. Verifikovano isti čas — `token.json` se sam osvežio i
> `ads_report.py` vratio pun izlaz, dakle **postojeći refresh token je preživeo
> prelazak** (nije trebalo ponovo pokretati `authorize_oauth.py`).
>
> 🔴 **Gotcha:** konzola je prvo vraćala **„You need additional access"** sa
> `oauthconfig.testusers.get` / `oauthconfig.verification.get` /
> `resourcemanager.projects.get` kao Missing. **Uzrok nije bio nedostatak role nego
> pogrešan Google nalog u Chrome-u** — projekat `mcp-za-claude` (`561984657473`) vidi
> samo nalog koji ga poseduje. Ako se ovaj ekran ikad ponovi: prvo prebaci nalog
> (avatar gore desno), pa tek onda razmišljaj o IAM-u.
>
> 🔴 **Verification Center se NE pokreće.** Puna Google verifikacija (demo video,
> privacy policy, nedelje čekanja) postoji za javne app sa >100 korisnika — nama ne
> treba i ne sme se startovati „za svaki slučaj".

**Tekst ispod je istorijski (stanje 2026-08-11), zadržan zbog konteksta:**

Pri ponovnoj autorizaciji (`authorize_oauth.py`, token istekao/opozvan)
Google je pokazao ekran **"App not verified"** umesto direktnog "Allow" —
OAuth consent screen za `mcp-za-claude` je i dalje u statusu **Testing**
(v. Korak C, tačka 2), a `adwords` scope je Google-ova "sensitive/restricted"
kategorija koja to okida.

**Brzi put (dovoljan za nas — jedan korisnik, ne javna app):**
1. [console.cloud.google.com](https://console.cloud.google.com) → projekat
   `mcp-za-claude` → **APIs & Services → OAuth consent screen** → **Audience**
   (ili "Test users" sekcija)
2. Proveri da je nalog kojim se logujemo (isti koji upravlja Ads
   156-886-0314) dodat u **Test users** listu
3. Na samom "App not verified" ekranu klikni **Advanced / Napredno** →
   **Go to mcp-za-claude (unsafe)** — bezbedno je, app je naša sopstvena,
   upozorenje je samo zato što nije prošla Google-ovu punu review proceduru
4. Nastavi normalan "Allow" flow posle toga

**Puna verifikacija (NIJE potrebna za nas)**: Google-ova review procedura
(sedmice, treba demo video + privacy policy URL) postoji samo za javne app
sa >100 korisnika. Test users limit (do 100 email-a) je više nego dovoljan
za jednu osobu — ne pokretati punu verifikaciju.

**#ceka-miroslav**: potvrditi da je nalog u Test users listi (korak 2) i
proći kroz "Advanced → unsafe" klik da se dobije novi `token.json`, pa ga
prekopirati na server (Korak E).

## Kad javiti da je gotovo

GA4/GSC/Ads već rade — mogu odmah da ih koristim za izveštaje. Preostaje
SAMO GMB: probaj `gmb_report.py` ponovo (kvota se možda već sredila) ili
javi ako i dalje javlja 429/403 pa gledamo Korak B.

## Veze
- `.claude/skills/antasline-konektor/SKILL.md` — kako se skripte pokreću
- `reference/identifikatori.md` — javni ID-evi (property/nalog brojevi)
