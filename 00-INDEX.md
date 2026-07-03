---
tip: dashboard
azurirano: 2026-06-28
---

# AntasLine — komandni centar

Jedina istina projekta na disku. Sve sesije (terminal + chat) i odluke se slivaju ovde.

> [!info] Kako radi
> Tri nivoa, sve povezano: **[[PROGRESS]]** = trenutno stanje, **[[DNEVNIK-NAPRETKA]]** = hronološki ledger (svaka sesija, svaki alat), **`dnevnik/`** = detaljni zapisi velikih sesija. Dashboard se sam puni iz `alat:` oznaka.

---

## 📍 Trenutno stanje
> Ugrađeno iz [[PROGRESS]] — ažurira ga Claude Code.

![[PROGRESS]]

**Pun hronološki ledger:** [[DNEVNIK-NAPRETKA]]

---

## ⚙️ Zahtevani plugin
Aktiviraj **Dataview** (Settings → Community plugins → Browse → "Dataview" → Install → Enable). Bez njega tabele ispod ostaju kao goli kod. Opciono: **Tasks** za lepši pregled čekboksova.

---

## 📋 Sve sesije (najnovije prvo)

```dataview
TABLE alat AS "Alat", blok AS "Blok", status AS "Status", datum AS "Datum"
FROM "dnevnik"
WHERE tip != "template"
SORT datum DESC
```

## 🔧 Šta je rađeno kroz terminal (Claude Code)

```dataview
TABLE blok AS "Blok", status AS "Status", datum AS "Datum"
FROM "dnevnik"
WHERE alat = "claude-code"
SORT datum DESC
```

## 💬 Šta je rađeno kroz chat

```dataview
TABLE blok AS "Blok", status AS "Status", datum AS "Datum"
FROM "dnevnik"
WHERE alat = "chat"
SORT datum DESC
```

---

## ✅ Sve otvorene akcije (iz svih beležaka)

```dataview
TASK
WHERE !completed AND !contains(file.name, "TEMPLATE")
GROUP BY file.link
```

### Čeka Miroslava
```dataview
TASK
WHERE !completed AND !contains(file.name, "TEMPLATE") AND contains(text, "#ceka-miroslav")
```

### Za Claude Code
```dataview
TASK
WHERE !completed AND !contains(file.name, "TEMPLATE") AND contains(text, "#claude-code")
```

---

## 🧭 Brza navigacija
- [[blokovi/BLOK-A-tracking]] — tracking infrastruktura (zatvoreno)
- [[blokovi/BLOK-B-publike]] — GA4 publike + funnel (zatvoreno)
- [[blokovi/BLOK-C-sledece]] — sledeći korak (C1 redirect / C2 content / C3 on-page)
- [[odluke/_pregled-odluka]] — sve donete odluke + zašto
- [[reference/identifikatori]] — GA4/GTM/Ads ID-evi
- [[reference/naucene-lekcije]] — tehnički gotchas

## 📊 Stanje konverzija
Prag za Maximize Conversions: **20–30 pravih** konverzija sa `/hvala-za-poruku/`.
Trenutno (poslednji upis): **33 ukupno, samo 2 iz plaćenog** → ostati na Maximize Clicks.
