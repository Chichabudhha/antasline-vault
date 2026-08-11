---
tip: sesija
datum: 2026-08-11
tag: claude-code
oblast: W4 / migracija
naslov: Final URL audit oglasa — ZATVOREN (aktivna kampanja čista, 2 URL-a na tuđem domenu)
---

# 2026-08-11 [claude-code] W4 4.10 — Final URL audit oglasa, priprema

Šesta stavka istog dana. Izbor M: „Final URL audit oglasa — priprema"
(stavka iz [[migracija/2026-08-10-pre-migration-checklist]] §A).

## Šta je urađeno

**Napisane 3 nove alatke** (sve read-only):

| Alat | Šta radi |
|---|---|
| `.claude/skills/antasline-konektor/scripts/ads_final_urls.py` | Google Ads API: final + mobile URL svakog oglasa, keyword-level URL-ovi, sitelink/asset URL-ovi (kampanja/ad-grupa/nalog nivo), tracking template i final_url_suffix. Konektor to do sada nije imao. |
| `.claude/skills/antasline-konektor/scripts/ga4_paid_landing.py` | GA4 landing stranice za `sessionMedium=cpc`. Servisni nalog → radi bez OAuth-a. |
| `migracija/alati/ads-url-audit.php` | Poređenje bilo kog spiska URL-ova sa (a) lokalnim buildom i (b) 73 pravila iz `htaccess-301-DRAFT.txt` → klasifikacija `OK` / `PREPISATI` / `REDIRECT-BUILD` / `PUKAO` + CSV. |

## Rezultat — konačan (oba izvora, 41 URL)

```
PUKAO             1     /404.html (artefakt live 404 stranice, nije URL oglasa)
EKSTERNI-DOMEN    2     🔴 ekopodneploce.rs
REDIRECT-BUILD    0
PREPISATI         6     301 ih hvata, ali oglas treba prepisati
OK               32
```

🟢 **Aktivni saobraćaj je čist.** Od **14 kampanja samo je jedna ENABLED**
(„ECOTILE INDUSTRIJSKI PODOVI") — 1 RSA + 6 sitelinkova, **svih 7 URL-ova
200 na buildu**. Na dan migracije se za aktivnu kampanju ne dira ništa.
⚠️ Usput: „Podloge za terase i bazene" je **PAUSED** — plan i [[CLAUDE]] §6 na
više mesta govore o „obe aktivne kampanje", to više ne važi.

🔴 **2 URL-a vode na TUĐI domen** — `http://www.ekopodneploce.rs/` (3 oglasa u
pauziranoj „Ecotile kampanja") i `.../proizvodi/E%20500-7/E500-7.html`
(sitelinkovi „Industrijski podovi" + „Podovi za magacine"). Ne troši ništa
danas jer je pauzirano, ali 301 mapa tu **ne pomaže** (nije naš domen) — ako se
kampanja ikad reaktivira, plaćen klik odlazi sa antasline.com. Ništa nije
dirano. **#ceka-miroslav.**

**6 × PREPISATI** (sve u pauziranim kampanjama): `/home/industrijski-podovi/`
(8 oglasa + 1 sitelink) → `/industrijski-podovi/` · `/sportski-podovi/`
(2 oglasa + 3 sitelinka) → `/sportske-podloge/` ·
`/home/industrijski-podovi/ecotile-5005-podne-ploce/` ·
`/home/industrijski-podovi/ecotile-5007/` ·
`/industrijski-podovi/trakezaobelezavanje/` · `/ergonomski-podovi/`.

**Provereno da NEMA** (ne pretpostavljeno): 0 keyword-level final URL-ova ·
0 `tracking_url_template`/`final_url_suffix` na svih 14 kampanja ·
0 `final_mobile_urls`.

## Rezultat — GA4 presek (rađen prvi, pre re-autorizacije)

Period (11.05–10.08, 3 meseca plaćenog saobraćaja):
31 jedinstvena landing putanja → **29 `OK`**, 1 `(not set)`, 1 `/404.html`
(artefakt live 404 stranice, 1 sesija — nije URL oglasa). **0 `PREPISATI`,
0 `PUKAO`.** Glavnina budžeta ide na `/spoljnje-podne-obloge/` (1.423 sesije)
i `/industrijski-podovi/` (575) — obe 200 na buildu.

**Detektor proveren poznato-lošim primerima** (pravilo od jutros: kad provera
vrati nulu, propusti kroz nju loš primer). Kontrolni spisak od 6 URL-ova dao
tačno očekivanu podelu: `PREPISATI` ×3 (uklj. ćirilično `/бренд/ecotile/`),
`PUKAO` ×2, `OK` ×1 — pri čemu je `?gclid=` ispravno ignorisan pri
normalizaciji putanje.

## Nalazi

🟢 **`?gclid=` preživljava 301 — provereno, ne pretpostavljeno.** Da
`.htaccess` odseca query string, svaki preusmeren klik iz oglasa izgubio bi
`gclid` i konverzija se ne bi pripisala Ads-u. Test u izolovanom
`htdocs/redirtest2/` (obrisan posle merenja): `?gclid=…&utm_source=google`
stiže netaknut u `Location`, i na običnom i na ćiriličnom pravilu — `mod_alias`
sam dodaje originalni query kad cilj nema svoj. Ništa se ne menja u draftu.

🔴 **GA4 presek se pokazao nedovoljnim — crno na belo.** Prvo je izgledalo
čisto (29/29), ali GA4 **nije uhvatio nijedan** od 8 problematičnih URL-ova:
svi žive u pauziranim kampanjama i sitelinkovima bez klikova, a
`ekopodneploce.rs` ne bi bio primećen nikad. GA4 vidi samo ono što ima
klikove i beleži odredište **posle** redirekta. Da je audit zatvoren na tim
podacima, nalaz bi izostao.

🔴 **OAuth token je bio mrtav** (`invalid_grant`) — `token.json` osvežen 06.08,
pao 11.08 = 5 dana. Rešeno re-autorizacijom (M, browser consent), posle čega
je export prošao. Uzrok je sistemski: consent screen verovatno u statusu
*Testing*, gde Google gasi refresh token posle 7 dana; GA4/GSC to ne osećaju
jer idu preko servisnog naloga. 🔴 **Udariće ponovo 24.08**, kad se radi 4.10
i verifikacija konverzija → dodata stavka **B1** u checklistu (provera tokena
pre svega ostalog). Trajno rešenje: Cloud Console → OAuth consent screen →
**Publish app**, 2 min.

⚠️ Dva buga u sopstvenoj skripti, oba uhvaćena prvim pokretanjem: GAQL traži
`campaign.status` u `SELECT` kad se po njemu filtrira (3 upita), i `print` na
Windows-u puca na `UnicodeEncodeError` čim se izlaz sa ćirilicom preusmeri u
fajl (`sys.stdout.reconfigure(encoding="utf-8")`). Treći, u audit alatu:
eksterni domen se normalizovao u putanju, pa bi `ekopodneploce.rs/proizvodi/…`
bio proveren kao putanja na **našem** sajtu → lažan `PUKAO`. Dodata klasa
`EKSTERNI-DOMEN`.

## Otvorene akcije

- **#ceka-miroslav:** 2 URL-a na `ekopodneploce.rs` — prepisati na antasline.com
  parnjak ili obrisati te objekte (3 oglasa + 2 sitelinka).
- **#ceka-miroslav:** *Publish app* na OAuth consent screen-u (2 min) — inače
  ponovna autorizacija ujutru 24.08.
- Pre reaktivacije bilo koje pauzirane kampanje (uklj. W4 4.4): prepisati
  6 URL-ova iz `PREPISATI` grupe. Ne blokira migraciju.
- Posle migracije: isti audit protiv produkcije, poređenje sa
  `analiza/2026-08-11-ads-url-audit.csv`.

## Beleške / odluke

- Alat namerno prima i `--txt` (ručni spisak) — da se isti mehanizam može
  pustiti nad bilo kojim skupom URL-ova (npr. spisak iz Ads UI-ja ručno
  izvezen, ako API ostane nedostupan).
- `analiza/2026-08-11-ga4-paid-landing-3m.json` +
  `analiza/2026-08-11-ads-url-audit-ga4-deo.csv` sačuvani kao baseline za
  poređenje posle migracije.

## Veze
[[migracija/2026-08-11-ads-final-url-audit]] ·
[[migracija/2026-08-10-pre-migration-checklist]] ·
[[dnevnik/2026-08-11-htaccess-301-reverifikacija]] ·
[[2026-07-06-MASTER-PLAN-V2]] (W4 4.10) · [[reference/api-konektor-setup.md]]
