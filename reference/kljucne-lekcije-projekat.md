---
name: kljucne-lekcije-projekat
description: Kurirane projektne lekcije po domenu (Tracking, Konektor/Ads dijagnostika, SEO, Telefon) + istorijska mapa renumeracije CLAUDE.md sekcija (staro §8.x/§9 → novo §9.x/§10-16). Izmešteno iz CLAUDE.md §10 2026-08-20 (vault higijena). Za sirove tehničke gotchas po datumu v. [[reference/naucene-lekcije]].
---

# Ključne lekcije (da se ne ponavljaju greške)

> 🔢 **Numeracija CLAUDE.md ispravljena 2026-08-18.** Sekcija "Ključne lekcije"
> je ranije nosila broj **9**, isti kao „WORKFLOW I ALATI" — pa je „§9" u
> starijim beleškama dvosmislen. Prevod starih brojeva na nove:
>
> | Staro | Novo | Sekcija |
> |---|---|---|
> | §9 (lekcije) | **§10** | KLJUČNE LEKCIJE — *telefon, silo, throttling, GTM* |
> | §9 (workflow) | §9 | WORKFLOW I ALATI — *tri-surface git, tokeni* |
> | §8.1–8.7 | §9.1–9.7 | podsekcije workflow-a |
> | §10 | **§11** | FORMAT IZVEŠTAVANJA |
> | §11 | **§12** | ULOGE |
> | §12 | **§13** | GDE PROVERITI TRENUTNO STANJE |
> | §13 | **§14** | KOMPLETAN HUB |
> | §14 | **§15** | ISTORIJSKI SNAPSHOT |
> | §15 | **§16** | ZA CLAUDE-A SLEDEĆI PUT |
>
> Datirane beleške u `dnevnik/` i arhivama **nisu prepravljane** — one su
> zapis o tome šta je tada urađeno; koristi ovu tabelu pri čitanju.

**Tracking:**
- Svaki GTM consent update handler mora slati eksplicitne vrednosti za sve
  4 kategorije — prazan `gtag('consent','update',{})` ne preklapa prethodno
  granted stanje; GTM Preview pokazuje "-" u On-page Update koloni ako je update prazan
- Ugašeni WP pluginovi ne izvršavaju PHP — ako baner ostane posle
  deaktivacije, grep-uj tekst banera, ne ime plugina
- GTM ručno pisani container JSON import puca sa "Error deserializing enum
  type [EventType]" na ovom kontejneru — ne pokušavati ponovo. Jedini pouzdani
  putevi: (A) ručno kreiranje u GTM UI, ili (B) Export kontejnera pa merge u
  tačnom formatu

**Konektor / Google Ads dijagnostika** (istorijski Windsor.ai lekcije, i
dalje važe principijelno — Windsor je istekao 2026-07-27, zamenjen
sopstvenim konektorom preko `.claude/skills/antasline-konektor/`, videti
[[reference/api-konektor-setup.md]]):
- ECOTILE kolaps isporuke (visok impression share + sitni apsolutni
  impressions + skok CPC) = throttling na nivou naloga (balans/verifikacija),
  ne pad tražnje na tržištu
- Prazan/nulti odgovor za kampanju ne znači grešku konektora — proveri
  spend+impressions pre nego što pretpostaviš kvar (throttling istorija)
- Konektor (i stari Windsor, i novi sopstveni) je read-only prema GTM/GA4
  — potvrđuje da eventi stižu, ali ne može da menja tagove/triggere/key
  event podešavanja
- GA4 audience membership size se i dalje ne izlaže direktno preko
  standardnog Data API runReport-a — `active_users` segmentiran po
  `audience_name` (custom dimenzija, ako postoji) ostaje najbliži proxy;
  prazne publike se jednostavno ne pojavljuju u rezultatima
- Conversion action segmentacija vraća samo akcije sa bar jednom konverzijom
  u traženom periodu — nove akcije sa nula konverzija se neće pojaviti
- GA4 `in`-operator filter je nepouzdan — povuci sve evente nefiltrirano i
  agregiraj u Python-u
- Week-over-week: koristi eksplicitne `date_from`/`date_to` (`YYYY-MM-DD`)
  za prethodni period, ne presets
- `/hvala-za-poruku/` conversion proxy: filter `[["page_path", "contains",
  "hvala"]]` na `screen_page_views`

**SEO:**
- Content parity je bitniji od 301 redirekcija samih po sebi — title/meta,
  H1/H2 struktura, broj reči, pokrivenost ključnih reči, interni linkovi,
  schema, indexability direktive — sve mora da se proveri stranica po stranicu
- Silo SEO benefit dolazi od internog linkovanja i breadcrumb schema-e, ne
  od kategorije-u-URL strukture
- `/sportske-podloge/kosarkaske-konstrukcije/` — visok organski saobraćaj
  (478 GSC klikova) bez potvrđenog redirect cilja — visok prioritet

**Telefon:**
- 🔴 **Oba broja su `069`** (potvrđeno na live-u i u temi 2026-07-29). „072" i
  „074" su skraćenice za POSLEDNJE DVE CIFRE, ne prefiksi:
  **linija 72** = `069 234 00 72` (`tel:+381692340072`) ·
  **linija 74** = `069 234 00 74` (`tel:+381692340074`).
  Nikad ne pisati „072 234 00 72" — takav zapis je stajao na 50 lokalnih
  stranica i 37 Yoast metaopisa, ispravljen 2026-07-29.
- **Linija 72 dominira** klikovima na telefon (~50 vs ~7 za liniju 74);
  ~46/50 klikova sa mobilnog → prioritet linije 72 u ad asset-ima i on-page CTA-ovima
- `mailto` sa pre-populate `?subject=` postoji na bar jednoj stranici
  proizvoda — vredi proširiti na ostale
