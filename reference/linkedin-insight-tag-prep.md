---
tip: reference
azurirano: 2026-08-05
---

# LinkedIn Insight Tag — priprema (W4 4.12)

> Spec za GTM ožičavanje, spreman da se izvrši čim M14 (pristup LinkedIn
> Campaign Manager nalogu) bude rešen. Isti obrazac kao 4.11 Meta Pixel —
> isti GTM kontejner (`GTM-TRDT8K9`), isti Consent Mode gate, iste postojeće
> `generate_lead`/`tel`/`mailto` trigere kao referentne tačke.

## Blokator (M14) — šta prvo treba od Miroslava

1. Proveri da li AntasLine već ima **LinkedIn Campaign Manager** nalog —
   campaign.linkedin.com, uloguj se sa nalogom koji administrira LinkedIn
   stranicu firme (`linkedin.com/company/antas-line-doo` — trenutno "mrtav"
   po [[reference/drustvene-mreze]], ali stranica postoji, Campaign Manager
   nalog je odvojen resurs).
2. Ako ne postoji: kreiraj Campaign Manager nalog, poveži LinkedIn stranicu
   firme.
3. Account Assets → **Insight Tag** → generiši/pronađi **Partner ID**
   (numerički, obično 6–7 cifara, format `_______`).
4. Pošalji Partner ID → nastavljam Fazu A (base tag, ispod).

Nije potreban aktivan Ads budžet za korak 1–3 (Insight Tag se može generisati
i bez pokrenute kampanje) — sam trošak kreće tek posebnom M odlukom (master
plan 4.12 napomena: CPC znatno skuplji od Google/Meta).

## Faza A — Base Insight Tag (čim stigne Partner ID)

GTM ima ugrađen tag tip **"LinkedIn Insight Tag"** u Tag Types galeriji (nije
potreban Custom HTML/Community Template).

| Polje | Vrednost |
|---|---|
| Tag Type | LinkedIn Insight Tag (built-in) |
| Partner ID | *(od Miroslava, korak 3 gore)* |
| Trigger | All Pages (isti obuhvat kao Meta Pixel PageView, 4.11) |
| Consent Settings | Additional Consent Checks → `ad_storage` required — isti mehanizam kao postojeći `tel`/`generate_lead` tagovi; mu-plugin `al-tracking-gtm-consent.php` već šalje `ad_storage` granted/denied preko `antasline_consent` kolačića, GTM sam gate-uje na tag nivou, nema izmena na WP strani potrebno |

## Faza B — Conversion Tracking (kad postoji aktivan Ads nalog)

LinkedIn nema "custom event" model kao Meta (`Lead`, `Contact`) — koristi
**Conversion ID** po definisanoj konverzijskoj radnji, generisan u Campaign
Manager. Preduslov: aktivan Ads nalog (ne samo Insight Tag pristup) →
Account Assets → Conversion Tracking → Create Conversion → izabrati
"Insight Tag" kao izvor → tip konverzije (npr. "Lead" / "Custom").

| Polje | Vrednost |
|---|---|
| Tag Type | LinkedIn Conversion Tracking (built-in, zahteva i Partner ID i Conversion ID) |
| Conversion ID | *(generiše se tek kad Ads nalog postoji — odvojen korak od Partner ID-a)* |
| Trigger | isti kao `generate_lead` — Page View `/hvala-za-poruku/` (mirror postojećeg pravila, ne Form Submit) |
| Consent Settings | isto `ad_storage` gate kao Faza A |

Faza B se ne pokreće dok ne postoji realan LinkedIn Ads budžet (master plan
4.12) — Faza A sama gradi matched audience/retargeting bazu unapred, bez
ikakvog troška.

## Veze
- [[2026-07-06-MASTER-PLAN-V2]] §W4 4.12, M14
- [[reference/naucene-lekcije]] — GTM ručni JSON import gotcha (ne pokušavati re-import, samo ručno kreiranje u UI)
- [[reference/drustvene-mreze]]
