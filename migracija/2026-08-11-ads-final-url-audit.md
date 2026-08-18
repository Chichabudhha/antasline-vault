---
tip: audit
naziv: Final URL audit oglasa — priprema (W4 4.10 / checklist §A)
datum: 2026-08-11
go-live: 2026-08-25
status: ZATVORENO 2026-08-11 — oba izvora povučena, spisak za dan migracije napravljen
---

# Final URL audit oglasa — priprema

Stavka „**Final URL audit oglasa — priprema**" iz
[[migracija/2026-08-10-pre-migration-checklist]] §A. Popravka samih oglasa je
zadatak **4.10 na dan migracije**; ovde se pravi **spisak** i alat kojim se
spisak pretvara u odluku po URL-u.

**Zašto uopšte:** posle migracije menjaju se slugovi. Oglas koji pokazuje na
stari URL i dalje „radi" (301 ga hvata), ali Google meri *landing page
experience* na finalnom URL-u, redirect dodaje latenciju na mobilnom (87%
Ads saobraćaja), a URL koji ni 301 ne pokriva postaje **404 plaćen po kliku**.

---

## 1. Metod — tri izvora, jedan alat

| Izvor | Šta daje | Pouzdanost |
|---|---|---|
| **Google Ads API** (`ads_final_urls.py`, nova) | final URL svakog oglasa + mobile URL + keyword-level URL + sitelink/asset URL + tracking template | 🟢 autoritativno — vidi i oglase/sitelinkove **bez ijednog klika** |
| **GA4 paid landing** (`ga4_paid_landing.py`, nova) | na koje URL-ove korisnici stvarno sleću iz `medium=cpc` | 🟡 presek — vidi samo ono što ima klikove, i vidi URL **posle** redirekta |
| **73 pravila iz `htaccess-301-DRAFT.txt`** | šta će 301 pokriti na dan migracije | 🟢 isti fajl koji se aktivira 25.08 |

Alat: **`migracija/alati/ads-url-audit.php`** (read-only). Prima `--json`
(Ads), `--ga4`, `--txt` (ručni spisak), sve tri se mogu kombinovati.
Za svaku putanju: HTTP status na lokalnom buildu (= stanje posle migracije)
+ poklapanje sa 301 pravilom → klasifikacija:

| Klasa | Značenje | Akcija na dan migracije |
|---|---|---|
| `OK` | 200 na buildu | ništa |
| `PREPISATI` | 301 pravilo postoji | oglas radi, ali **prepisati final URL na cilj** |
| `REDIRECT-BUILD` | build sam vraća 301 (nekanonična putanja) | prepisati na cilj |
| `EKSTERNI-DOMEN` | URL nije na antasline.com | 🔴 odluka, 301 mapa tu ne pomaže |
| `PUKAO` | nije 200 i **nema pravila** | 🔴 obavezna ručna popravka pre puštanja |

Izlaz: konzola + `--out izvestaj.csv` (semicolon, UTF-8-BOM, kao ostale mape).

---

## 2. REZULTAT — oba izvora (41 URL)

```
PUKAO             1     /404.html (artefakt live 404 stranice, 1 sesija; nije URL oglasa)
EKSTERNI-DOMEN    2     🔴 ekopodneploce.rs
REDIRECT-BUILD    0
PREPISATI         6     301 ih hvata, ali oglas treba prepisati
OK               32
```

Podaci: `analiza/2026-08-11-ads-final-urls.json` (Ads) ·
`analiza/2026-08-11-ga4-paid-landing-3m.json` (GA4) ·
**`analiza/2026-08-11-ads-url-audit.csv` (spisak za dan migracije)**.

### 2.1 🟢 Aktivni saobraćaj je čist — ne dira se ništa

Od **14 kampanja u nalogu samo je jedna ENABLED**: „ECOTILE INDUSTRIJSKI
PODOVI" — 1 RSA oglas + 6 sitelinkova. **Svih 7 URL-ova = 200 na buildu.**

| Objekat | URL |
|---|---|
| RSA 811598156128 | `/industrijski-podovi/` |
| Sitelink „Podovi Za Magacine" / „Podovi za hale" | `/industrijski-podovi/industrijski-pod/` |
| Sitelink „Antistatik podovi" | `/antistatik-i-elektroprovodljivi-podovi/` |
| Sitelink „Pod za velika opterećenja" | `/industrijski-podovi/podne-ploce-ecotile-50010/` |
| Sitelink „Obeležavanje podova" | `/industrijski-podovi/trake-za-obelezavanje/` |
| Sitelink „Bumperi za regale" | `/industrijski-podovi/bumperi-zastita-za-police-regale-i-zidove/` |

🔴 **DOPUNA 2026-08-18 — tvrdnja ispod je bila tačna u trenutku pulla, ali vodi na pogrešan zaključak.**
Ads API je 11.08 stvarno vratio `campaign_status: PAUSED` za „Podloge za terase
i bazene" (provereno u `analiza/2026-08-11-ads-final-urls.json`) — to nije bila
greška skripte. **Ali je kampanja istog tog dana potrošila 222 RSD / 14 klikova.**
Dnevni presek pokazuje isprekidanu isporuku, ne pauzu:

| Dan | 08.08 | 09.08 | 10.08 | 11.08 | 12–15.08 | 16.08 | 17.08 |
|---|---|---|---|---|---|---|---|
| RSD | 225 | — | 897 | 222 | — | 63 | **1.643** |
| Klikovi | 15 | 0 | 52 | 14 | 0 | 4 | **74** |

Dakle „PAUSED" status i potrošnja postoje **istovremeno**. Zaključak „sve osim
ECOTILE je pauzirano" i posledično „`[[CLAUDE]]` §6 više ne važi" **se povlače**
— §6 („obe aktivne kampanje") ostaje kakav jeste, ne menjati ga.
Puna analiza: [[dnevnik/ADS-DNEVNIK]] 2026-08-18.

> ~~⚠️ **Usput ispravka činjenice u planu:** „Podloge za terase i bazene" je~~
> ~~**PAUSED**. [[CLAUDE]] §6 i master plan na više mesta govore o „obe aktivne~~
> ~~kampanje" — to više ne važi. Sve ostalo osim ECOTILE je pauzirano.~~

🟢 **Šta se NE menja:** URL nalazi ovog audita ostaju validni. Final URL-ovi
kampanje „Podloge za terase i bazene" (`/spoljnje-podne-obloge/` +
`bergo-xl` / `bergo-unique` / `podovi-za-bazene` / `bergo-elite`) su **svi 200
na buildu** — v. redove 11, 13, 14, 16, 19 u
`analiza/2026-08-11-ads-url-audit.csv`. Problemi iz §2.2 i §2.3
(`ekopodneploce.rs`, `/home/…`) tiču se drugih kampanja i migraciju ne blokiraju.

🔴 **Metodološka lekcija:** `campaign.status` iz Ads API-ja **nije dokaz da
kampanja ne troši**. Status i potrošnja se moraju čitati zajedno (`ads_report.py`
po danu), inače se pauzirana-na-papiru kampanja ispusti iz svake analize.

### 2.2 🔴 2 URL-a vode na TUĐI domen — `ekopodneploce.rs`

| Objekat | URL |
|---|---|
| 3 oglasa u „Ecotile kampanja" (PAUSED) | `http://www.ekopodneploce.rs/` |
| Sitelink „Industrijski podovi" + „Podovi za magacine" | `http://www.ekopodneploce.rs/proizvodi/E%20500-7/E500-7.html` |

Kampanja je pauzirana, pa **ne troši ništa danas**. Ali: 301 mapa tu ne
pomaže (nije naš domen), a ako se kampanja ikad reaktivira, plaća se klik
koji odlazi sa antasline.com. Takođe `http://`, ne `https://`.
**#ceka-miroslav:** prepisati na antasline.com parnjak ili obrisati te
objekte. Ništa nije dirano.

### 2.3 6 × PREPISATI (sve u pauziranim kampanjama, 301 ih pokriva)

| Stari URL | → cilj po 301 mapi | Koristi |
|---|---|---|
| `/home/industrijski-podovi/` | `/industrijski-podovi/` | 8 oglasa + 1 sitelink |
| `/sportski-podovi/` | `/sportske-podloge/` | 2 oglasa + 3 sitelinka |
| `/home/industrijski-podovi/ecotile-5005-podne-ploce/` | `/industrijski-podovi/ecotile-5005-podne-ploce/` | 2 sitelinka |
| `/home/industrijski-podovi/ecotile-5007/` | `/industrijski-podovi/industrijski-pod/` | 1 sitelink |
| `/industrijski-podovi/trakezaobelezavanje/` | `/industrijski-podovi/trake-za-obelezavanje/` | 1 sitelink |
| `/ergonomski-podovi/` | `/ergonomske-podloge-2/` | 1 sitelink |

🟡 Napomena uz `ecotile-5007`: 301 ga vodi na `/industrijski-podovi/industrijski-pod/`,
ne na proizvod stranicu — to je namerno spljoštavanje iz istorijske mape
(3.759 GSC pogodaka na tom starom URL-u). Za SEO je u redu; **za oglas** bi
precizniji cilj bio sama proizvod stranica. Niska hitnost (pauzirano).

### 2.4 Čega nema (provereno, ne pretpostavljeno)

- **0 keyword-level final URL-ova** — nijedan keyword ne pregazi URL oglasa
- **0 `tracking_url_template` / `final_url_suffix`** na svih 14 kampanja
- **0 `final_mobile_urls`** — nema zasebnog mobilnog URL-a nigde

---

## 3. Presek GA4 strane (istorijski deo, pisan pre Ads pull-a)

Period **11.05–10.08.2026** (3 meseca, `sessionMedium=cpc`):
52 reda / **31 jedinstvena landing putanja**.

```
PUKAO            1      (/404.html — artefakt live 404 stranice, 1 sesija; nije URL oglasa)
REDIRECT-BUILD   0
PREPISATI        0
OK              29      (+ 1 red "(not set)", 18 sesija, bez putanje)
```

**Svih 29 stvarnih odredišta plaćenog saobraćaja postoji na buildu kao 200.**
Najprometnija: `/spoljnje-podne-obloge/` (1.423 sesije) · `/industrijski-podovi/`
(575) · `/spoljnje-podne-obloge/bergo-xl/` (49) · `/spoljnje-podne-obloge/bergo-unique/`
(31) · `/industrijski-podovi/industrijski-pod/` (19).

🔴 **Ovaj presek je bio nedovoljan — i to se posle videlo crno na belo.**
GA4 nije uhvatio **nijedan** od 8 problematičnih URL-ova iz §2.2/§2.3: svi
žive u pauziranim kampanjama i sitelinkovima bez klikova, a `ekopodneploce.rs`
ne bi bio primećen nikad. Vrednost preseka je što potvrđuje da **odredišta
koja stvarno primaju klikove** rade — ne što zatvara audit.

## 4. Nalaz koji ne bi bio uočen bez testa: `?gclid=` preživljava 301

Ako bi `.htaccess` odsecao query string, svaki preusmeren klik iz oglasa bi
izgubio `gclid` → **konverzija se ne bi pripisala Ads-u**. Izmereno u
izolovanom Apache folderu (`htdocs/redirtest2/`, obrisan posle merenja):

```
/sportski-podovi/?gclid=EAIaIQobTEST&utm_source=google
  → 301 Location: /sportske-podloge/?gclid=EAIaIQobTEST&utm_source=google   ✅
/бренд/ecotile/?gclid=ABC123
  → 301 Location: /brend/ecotile/?gclid=ABC123                              ✅
```

`mod_alias` sam dodaje originalni query kad cilj nema svoj. Važi i za
ćirilična pravila. **Nema šta da se menja u draftu.**

---

## 5. OAuth token — rešeno danas, ali ostaje obaveza za 25.08

Ads export je prvo pukao na `invalid_grant: Token has been expired or revoked`.
`token.json` je bio osvežen **06.08**, mrtav **11.08** — 5 dana. Rešeno
ponovnim pokretanjem `authorize_oauth.py` (M, browser consent); audit je posle
toga izvršen normalno.

**Uzrok je sistemski, ne jednokratan:** OAuth consent screen je verovatno u
statusu *Testing*, gde Google gasi refresh token posle **7 dana**. Servisni
nalozi (GA4/GSC) to ne osećaju — zato je `ga4_paid_landing.py` radio dok
`ads_report.py` nije.

🔴 **Posledica za dan migracije:** 25.08 se radi 4.10 i verifikacija
konverzija; mrtav token tada znači browser-consent u najgorem trenutku.
Uvedena stavka **B1** u [[migracija/2026-08-10-pre-migration-checklist]] —
provera da je token živ, pre svega ostalog.

**Trajno rešenje (2 min, preporuka):** Cloud Console → *APIs & Services* →
*OAuth consent screen* → **Publish app** (*In production*). Refresh token tada
ne ističe po vremenu, u skriptama se ne menja ništa.

## 6. Ponovno pokretanje audita

```
venv\Scripts\python.exe ...\scripts\ads_final_urls.py > ads-urls.json
venv\Scripts\python.exe ...\scripts\ga4_paid_landing.py --from D1 --to D2 > ga4-paid.json
C:\xampp\php\php.exe migracija\alati\ads-url-audit.php --json ads-urls.json --ga4 ga4-paid.json --out izvestaj.csv
```

⚠️ **Ako se do 25.08 promeni ijedan slug** — pustiti ponovo (isto pravilo kao
za `.htaccess` generator).

## 7. Akcije za dan migracije (4.10)

1. 🟢 **Aktivna ECOTILE kampanja — ništa.** Svih 7 URL-ova već pokazuje na
   stranice koje posle migracije daju 200.
2. 🔴 **Pre reaktivacije bilo koje pauzirane kampanje**: prepisati 6 URL-ova
   iz §2.3 i rešiti 2 `ekopodneploce.rs` URL-a iz §2.2. Ovo **ne blokira
   migraciju** — pauzirane kampanje ne troše ništa — ali blokira njihovo
   ponovno paljenje (uklj. W4 4.4 restrukturiranje ad grupa).
3. Posle prebacivanja: ponoviti audit protiv produkcije (`$BASE` u skripti) i
   uporediti sa `analiza/2026-08-11-ads-url-audit.csv`.

---

## Veze
[[migracija/2026-08-10-pre-migration-checklist]] ·
[[dnevnik/2026-08-11-htaccess-301-reverifikacija]] ·
[[2026-07-06-MASTER-PLAN-V2]] (W4 4.10) · [[dnevnik/ADS-DNEVNIK]] ·
[[reference/api-konektor-setup.md]]
