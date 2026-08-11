---
tip: red-cekanja
blok: "W2/W6 video"
azurirano: 2026-08-11
status: aktivan
namena: Dnevni red čekanja za Flow/Veo video obogaćivanje stranica — jedan kadar dnevno
---

# 🎬 Video red čekanja — dnevni tempo

> Radni tok: `.claude/skills/dnevni-video/SKILL.md` (pokreni sa `/dnevni-video`).
> Strategija i obrazloženje: [[2026-08-09-video-obogacivanje-plan]].
> Radni folder (van vault-a, binarni fajlovi): `C:\Miroslav\AntasLine-video\`
> — `flow-in/` (16:9 krop) · `clips/` (sirovi 8s/10s) · `out/` (montaža).

## Pravila koja se ne pregovaraju

1. **Samo pokret kamere i ambijent** u promptu (vetar, oblaci, svetlo). Čim se
   traži radnja (igrač, lopta), Veo izmišlja i podloga prestaje da bude naša.
2. Uvek u prompt: `keep the surface, colours and markings exactly as in the
   photo` + `Do not add any new objects or people. No text, no logos.`
3. **Flow je izvor, Gemini rezerva** — Gemini lepi vidljiv „sparkle" žig.
4. **Model se traži u tekstu prompta** („Using the Veo 3.1 - Lite model…"),
   ne kroz Agent settings.
5. **Ne pre ~10h** — krediti se resetuju po pacifičkoj ponoći.
6. Podrazumevano **Lite (10 kredita)**; Fast (20) samo za hero kadar stranice.
7. Samo **naše izvedene realizacije**. Proizvođački materijal (Bergo studijske
   fotke, Geoplast, Ergomat) ne ide u video bez posebne provere — dozvola koja
   postoji za slike na sajtu nije data za AI-derivat.

## Dnevni red čekanja

Redosled je izveden iz GSC curenja (pozicija 1–3, skoro nula klikova), ne iz utiska.

| # | Dan | Stranica | GSC | Foto materijal (potvrđen na disku) | Status |
|---|---|---|---|---|---|
| 1 | 09–10.08 | `/kako-napraviti-teren-za-basket…/` | 9.500 impr / 397 kl | 5 kadrova (Pelješac, Tara, Bajina Bašta, Ledine, dvorište) | ✅ **finalno: 38,0s, 5×Flow, bez žiga** |
| 2 | 11.08 | **rerender kadra 5 u Flow-u** (skidanje Gemini žiga) | — | `flow-in/05-dvoriste.jpg` (4608×2592) | ✅ 10 kredita, video remontiran na **38,0s** |
| 3 | | `/sportske-podloge/kosarkaske-konstrukcije/` | 478 kl, ima cene = komercijalna | `Antas line/Proizvodo/kosarkaske kosevi/` — MicroShot 125, MiniShot 225, LiteShot 325, sklopljene/rasklopljene | ⏳ |
| 4 | | Košarkaški koš / „visina koša" (16586) | 1.089 impr / **9 kl**, poz 1–2 | isti folder + `teren za basket u dvoristu.jpg` (koš u kadru) | ⏳ |
| 5 | | `/podloga-za-teniske-terene/` (šljaka) | 1.739 impr / **2 kl**, poz 4,4 | `novi sajt/Teniski tereni/` — TK Slice Valjevo, TK Đukić Beograd, Dom učenika Patrijarh Pavle | ⏳ |
| 6 | | Tenis dimenzije | 1.465 impr / 2 kl | isto + `Teren za pickleball Kosmaj.jpg` (4032×3024) | ⏳ |
| 7 | | Odbojka (4318) | 490 impr / 3 kl, poz 1,1 | `Teren za odbojku CG.jpg` (dron, Crna Gora) | ⏳ |
| 8 | | `/podloge-za-parkiraliste-i-staze/` | zdrav klaster, CTR 4,2% | Runfloor/Geocross — ⚠️ proizvođački, v. pravilo 7 | ⏳ |
| 9 | | Podovi za garaže i auto-servise (16664) | 229 kl | `priprema za sajt/pod za garaze/` — AMSS serija (naša montaža) | ⏳ |
| 10 | | Industrijski / ESD (16567) | core biznis | `Antistatik-pod-HTEC-Nis.jpg`, `PR-DC-Simanovci-ESD-pod.jpg`, `novo/ecotile/` 4128×3096 | ⏳ |
| 11 | | `/epoksidni-podovi-ili-ecotile-podovi/` (2542) | 3.193 impr, **CTR 0,47%** — najgori | ⚠️ treba kadar Ecotile montaže (klik ploče), proveriti `ecotile 5007/` | ⏳ |
| 12 | | Fudbal / veštačka trava | 2.409 impr / 7 kl | `vestacka trava/` — ⚠️ deo je stock, proveriti poreklo | ⏳ |

> **Ecotile/PVC kao proizvod-kategorija je namerno nisko** iako je core biznis:
> na poziciji 13–22 video ne pomaže jer nas niko ne vidi. Tamo prvo rangiranje.

## Rezerva ideja kad se red iscrpi

- **Terase / balkoni (Bergo Unique, XL)** — `Bergo_XL_Graphitegrey_Stonegrey_balcony`,
  `Unique balcony after.jpg` (4724×3151). Pravilo 7: proizvođačke, treba dozvola.
- **„Pre i posle"** — `priprema za sajt/pre i posle/` + `Stepping-Stones-after2.jpg`.
  Jak format, ali traži dva kadra i cross-fade, ne jedan render.
- **Dečije igralište** (`Decije-igralise-AntasLine.jpg`) — nema svoju stranicu, kandidat za W6 social.
- **Reference brendovi** (Rolls-Royce, Adidas, Hitachi, Pirelli, Vinča logoi) —
  nije video materijal, ali je neiskorišćen trust signal na industrijskim stranicama.

## Merenje

Po stranici: GSC CTR na ciljanim upitima, **28 dana pre vs 28 posle** objave.
Baseline za #1: `analiza/2026-08-10-gsc-baseline-basket-pre-videa.json`
(4.019 prikaza / 114 kl / CTR 2,84%).
**Stop kriterijum: ako posle prve 3 stranice nema pomaka u CTR-u — stati i preispitati.**

## Blokatori objave (isti za sve stranice)

- [ ] YouTube handle `@antasline5676` → `@antasline` #ceka-miroslav
- [ ] Registarska tablica na kadru 4 (Ledine) #ceka-miroslav
- [x] Lazy facade embed — postoji od 07.07 (F7.3)
- [x] `VideoObject` schema — `woodmart-child/inc/al-video-schema.php` (F7.3a)

## Veze
- Plan: [[2026-08-09-video-obogacivanje-plan]]
- Promptovi (basket): [[2026-08-09-flow-promptovi-basket]]
- GSC klasteri: [[2026-07-27-content-klasteri]]
- Foto arhiva: [[foto-arhiva-inventar]] · [[antasline-foto-arhiva-lokacije]]
- Šabloni embed/schema: [[migracija/woodmart-sabloni]] F7.3 + F7.3a
