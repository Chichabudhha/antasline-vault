---
tip: blok
blok: A
status: zavrseno
azurirano: 2026-06-27
---

# BLOK A — Tracking infrastruktura (ZATVOREN)

GTM Version 10 objavljen. Kontejner `GTM-TRDT8K9`.

## Stanje
- `generate_lead` rewire: okida na Page View `/hvala-za-poruku/`, NE Form Submit — potvrđeno u Realtime.
- GA4 key events: tačno tri — `generate_lead` (primarna), `tel` (sekundarna), `mailto` (sekundarna).
- MonsterInsights ISKLJUČEN; GTM jedini izvor GA4 podataka; DebugView potvrdio 1× page_view po stranici.
- `tel` tag obnovljen ("Analitika tag - telefon", parametar `tel_number = {{Click URL}}`, okidač "Klik na telefon"), čist bez MI legacy duplikata.
- CMP: sopstveni plugin `antasline-consent` (Consent Mode v2, default DENIED za sva 4 tipa, url_passthrough + ads_data_redaction).
- Click-to-call Ads konverzija: "Klik na telefon (web)", ID 966742304, Label QQCBCNDQ_sUcEKCi_cwD — sekundarni/observation.

## Otvorene akcije
- [ ] Verifikacija posle Consent Mode + gašenja MI: očekivati blag pad apsolutnih konverzija = tačnije merenje, NE pad performansi. Ne reagovati budžetom. #ceka-miroslav

## Veze
- [[reference/naucene-lekcije]] — GTM import gotchas, consent handler
- [[reference/identifikatori]]
