---
tip: blok
blok: C
status: ceka
azurirano: 2026-06-28
---

# BLOK C — Sledeći korak (izabrati JEDNU stavku)

## C1: Redirect mapa
`antasline-redirect-mapa-POPUNJENA.csv` (118 redova).
- ~15 redova `bezredirekta` gde se stari/novi URL razlikuju (svaki postaje 404 bez 301).
- Inconsistency `industrijski-podovi` vs `industrija-podovi` između notes i target URL-ova.
- [ ] KRITIČNO: `/sportske-podloge/kosarkaske-konstrukcije/` (478 GSC klikova) nema redirect target #claude-code

## C2: Content parity (live→staging)
Crawl otkrio: 32 proizvoda na live vs 10 na stagingu (27 nedostaje, 5 samo na stagingu), ~19 blog postova, ~20 silo/landing stranica nedostaju.
- Tier 1: WooCommerce CSV (proizvodi)
- Tier 2: WXR export (blog)
- Tier 3: ručni rebuild landing/silo u WPBakery
- [ ] Odluka: dispozicija 5 staging-only proizvoda #ceka-miroslav
- [ ] Odluka: overwrite vs preserve ručno rebuilt Porto verzija postova #ceka-miroslav

## C3: On-page build
**📝 Master content plan: [[seo/plan-novih-stranica]]** — 20 stranica u 4 tijera (iz 16m keyword analize), obuhvata i ranije 4 GSC stranice. Redosled: odbojka refresh → terasa cena → industrijski cena → garaže landing.
- [ ] 6 WPBakery blokova za `/industrijski-podovi/` (ID 4937) — JS bug, blokirano #claude-code
- [ ] Čišćenje "Smartas" iz naslova homepage-a #claude-code
- [ ] Landing za `/sportske-podloge/kosarkaske-konstrukcije/` (103 korisnika/14d, jak organski, bez prave stranice) #claude-code
- [ ] Title/meta prepis 4 stranice (pop-tenis, odbojka, spoljne-obloge, conquest) — snapshot §6.1 #claude-code

## Odluka
- [ ] Izabrati C1 / C2 / C3 za sledeću sesiju #ceka-miroslav

## Veze
- [[odluke/_pregled-odluka]] — flat `/proizvod/` struktura
