---
tip: plan
datum: 2026-08-09
blok: SEO / W2 content
status: prvi-nacrt
izvor: "Google Flow test 2026-08-09 (kadar Pelješac, Veo 3.1 Fast, uspešno) + seo/2026-07-27-content-klasteri.md (GSC 90d) + reference/konkurencija-trziste-analiza.md"
azurirano: 2026-08-09
---

# Plan: obogaćivanje stranica videom preko Google Flow (Veo 3.1)

## 0. Šta je dokazano 2026-08-09

Google Flow radi i daje upotrebljiv rezultat iz **naših pravih fotografija**
(image-to-video, ne generisanje od nule). Prvi klip — Pelješac, 8s, 16:9,
Veo 3.1 Fast — sačuvao je podlogu, boje i linije terena netaknute, bez
izmišljenih igrača ili objekata.

Ključno pravilo prompta koje je to omogućilo: **traži se samo pokret kamere i
ambijent** (vetar, oblaci, svetlo). Čim se zatraži radnja (igrač, lopta kroz
obruč), Veo počinje da izmišlja i podloga prestaje da bude naša realizacija.

Praktični detalji: [[2026-08-09-flow-promptovi-basket]] (shot lista + tačni promptovi)

---

## 1. Zašto video, konkretno za nas — nije estetika, nego CTR

Iz `seo/2026-07-27-content-klasteri.md` ponavlja se isti obrazac kroz skoro
svaki klaster: **rangiramo na poziciji 1–3 i ne dobijamo klik**, jer Google
odgovara direktno u rezultatima.

| Upit / grupa | Prikazi | Klikovi | Poz |
|---|---|---|---|
| `visina koša` mikro-klaster | ~1.089 | **9** | 1–2 |
| `dimenzije fudbalskog terena` + varijante | 2.409 | **7** | 1,3–1,8 |
| `šljaka` varijante | 1.739 | **2** | 4,4 |
| `dimenzije teniskog terena` | 1.465 | **2** | 2,3–3,2 |
| `dimenzije odbojkaškog terena` | ~490 | **3** | 1,1 |
| epoksid varijante | ~800 | **0** | 3–6 |

Tekstualni refresh ovo ne rešava — pozicija je već tu, problem je što plavi
link ne izgleda vredno klika naspram direktnog odgovora.

**Video je jedan od retkih poteza koji menja izgled samog rezultata.** Sa
`VideoObject` schema-om Google može da prikaže sličicu videa uz rezultat, što
tera oko na taj red. To je direktan napad na tačno ovaj problem, a ne još
jedan pasus teksta.

Sekundarne koristi: duže zadržavanje na stranici, materijal za Ads/social bez
dodatnog snimanja, i YouTube kanal kao druga površina pretrage (W6).

> ⚠️ Ovo je **hipoteza sa dobrim osnovom, ne garancija**. Google prikazuje
> video sličicu po sopstvenoj proceni i ne za svaki upit. Merenje: GSC CTR na
> ciljanim upitima, 28 dana pre vs. 28 dana posle objave, po stranici. Ako
> posle prve 3 stranice nema pomaka u CTR-u, stati i preispitati.

---

## 2. Realan budžet — koliko videa stvarno možemo

> 🟡 **DOPUNA 2026-08-10:** cene po klipu (Lite 10 / Fast 20) potvrđene, ali
> „50 dnevno" traži zvezdicu: **reset ide po pacifičkoj ponoći ≈ 09–10h po
> našem vremenu**, ne po lokalnoj (v. [[reference/naucene-lekcije]]). Render
> pokušan u 06:45 pao je na „You need more AI credits" — prerano, ne kraj kvote.
> **Ne planirati „uveče potrošim, ujutru nastavim" pre ~10h.**
>
> 🔴 **Druga besplatna kvota — Gemini — nije ekvivalent Flow-u:** Gemini
> klipovi nose **vidljiv „sparkle" vodeni žig** u donjem desnom uglu kroz ceo
> klip, Flow klipovi ne nose ništa. Za materijal koji ide na sajt/Ads/YouTube
> **Flow je izvor, Gemini rezerva**. Usput: Gemini vraća **10s** klipove, Flow 8s.
> v. [[dnevnik/2026-08-10-kadar5-gemini-video-40s]].

| | |
|---|---|
| Besplatni krediti | **50 dnevno**, reset ≈09–10h po lokalnom (pacifička ponoć) |
| Veo 3.1 – Fast | **20 kredita** / 8s klip (potvrđeno 2026-08-09) |
| Veo 3.1 – Lite | **10 kredita** / 8s klip (potvrđeno 2026-08-09) |
| Omni Flash | jeftinije, nije mereno |
| Veo 3.1 – Quality | znatno skuplje, ne koristiti za ovo |

**Znači: 5 klipova dnevno na Lite, ili 2 na Fast.** Lite je u praksi (test
2026-08-09, kadrovi 2–4) sasvim dovoljan za spore pokrete kamere nad statičnim
terenom — razlika prema Fast-u se ne vidi na ovakvom materijalu. **Podrazumevano
koristiti Lite**, Fast čuvati za hero kadar stranice.

### Druga besplatna kvota — Gemini

Gemini (gemini.google.com, kroz Chrome) takođe generiše video preko Veo-a, i to
je **odvojena besplatna kvota od Flow-ovih 50 kredita**. Praktično: dnevni
kapacitet je zbir to dvoje, ne 50.

⚠️ Ali za spajanje klipova Gemini **nije** rešenje — on generiše, ne montira.
Za sklapanje 3–5 klipova u jedan video postoje dva puta:
- **Flow-ov sopstveni timeline** (u editoru klipa, `+` pored trake) — besplatno,
  bez instalacije, ali grubo.
- **ffmpeg lokalno** — precizno, skriptabilno, radi i tekst preko i fade
  prelaze. **Nije instaliran** (provereno 2026-08-09) — treba `winget install
  ffmpeg` pre prve montaže.

Iz toga slede dve realne opcije:

- **A) Jedna stranica dnevno, 1 hero klip od 8s** (+20 kredita rezerve za
  ponavljanje ako prvi pokušaj promaši). Ovo je tempo koji si tražio i drži se.
- **B) Jedna stranica na 2–3 dana, video od 3–5 klipova (24–40s).** Bogatije,
  ali sporije.

**Preporuka: A za većinu stranica, B samo za top 3.** Za `VideoObject` schema
i sličicu u rezultatima jedan dobar 8s klip je dovoljan — dužina nije rangirni
faktor.

---

## 3. Red čekanja stranica (po veličini curenja × izvodljivosti)

Redosled je izveden iz GSC brojeva gore, ne iz utiska.

| # | Stranica | Zašto | Foto materijal |
|---|---|---|---|
| 1 | `/kako-napraviti-teren-za-basket…/` | 9.500 impr / 397 kl — najjači klaster | ✅ ~100 fotki |
| 2 | `/sportske-podloge/kosarkaske-konstrukcije/` | 478 kl, ima prave cene = komercijalna | ✅ |
| 3 | Košarkaški koš / „visina koša" (16586) | 1.089 impr, 9 klikova, poz 1–2 | ✅ |
| 4 | `/podloga-za-teniske-terene/` (šljaka) | 1.739 impr, 2 klika | ✅ tenis/Kosmaj |
| 5 | Tenis dimenzije | 1.465 impr, 2 klika | ✅ |
| 6 | `/epoksidni-podovi-ili-ecotile-podovi/` | 3.193 impr, CTR 0,47% (najgori) | ⚠️ treba Ecotile montaža |
| 7 | Odbojka (4318) | 490 impr, 3 klika | ✅ Teren za odbojku CG |
| 8 | Fudbal / veštačka trava | 2.409 impr, 7 klikova | ⚠️ proveriti |
| 9 | `/podloge-za-parkiraliste/` + garaže | zdrav klaster, CTR 4,2% | ⚠️ proveriti |
| 10 | Ecotile / PVC ploče | najveći komercijalni propust, ali poz 13–22 | ⚠️ |

> Ecotile/PVC je namerno nisko **iako je core biznis**: na poziciji 22 video
> ne pomaže jer nas niko i ne vidi. Tamo prvo treba rangiranje (tekst,
> interni linkovi, cena), pa tek onda video. Ne trošiti kredite tamo sada.

---

## 4. Radni tok po stranici (ponovljiv, ~30–40 min)

1. **Izbor fotke** iz arhive — prednost: visoka rezolucija, pejzažni format,
   jak kontrast, naša potvrđena realizacija (EXIF/GPS ili poznata lokacija).
   Izvori: `C:\Miroslav\Antas line\novi sajt\`, `C:\Miroslav\Antas Line priprema za sajt\`,
   `Downloads\tereni za basket*.zip`. Inventar: [[foto-arhiva-inventar]]
2. **Krop na 16:9** (skripta u [[2026-08-09-flow-promptovi-basket]], PIL).
3. **Flow**: upload → prikači uz prompt → Veo 3.1 Fast → 16:9 → odobri 20 kredita.
4. **Download MP4**, pregled: da li je podloga ostala netaknuta, ima li
   izmišljenih ljudi/objekata, da li tekst na zidovima „pluta".
5. **Objava**: YouTube → embed na stranicu. Kanal:
   `youtube.com/@antasline5676` (postoji, status „mrtav" po
   [[drustvene-mreze]]). **Pre prve objave promeniti handle u `@antasline`** —
   to je već stavka u W6 planu, a od sada handle ulazi u embed URL-ove i
   schema-u na sajtu, pa je jeftinije uraditi sada nego posle 10 videa.
   Objavljivati **javno, ne unlisted** (obrazloženje u §8).
6. **`VideoObject` JSON-LD** na stranicu (naziv, opis, thumbnail, trajanje,
   `uploadDate`, `contentUrl`/`embedUrl`).
7. **Upis u dnevnik** + zabeležiti GSC CTR na ciljanim upitima kao baseline.

### ✅ Dve tehničke prepreke — OBE ZATVORENE 2026-08-10

- ✅ **Core Web Vitals / lazy „facade"** — **nije ni bila otvorena stavka.**
  Fasada postoji od 2026-07-07 (`woodmart-sabloni` F7.3): `.al-video-facade`
  CSS + globalni `al-video-facade.js`, iframe se pravi **tek na klik**, domen
  `youtube-nocookie.com`. Radi na 9 stranica, provereno uživo 10.08 (0 youtube
  zahteva pre klika). Ovaj red je 09.08 upisan bez provere šablona.
- ✅ **VideoObject schema** — potvrđeno da Rank Math besplatan (1.0.275)
  **nema Video modul**, pa je napisan `woodmart-child/inc/al-video-schema.php`:
  schema se **izvodi iz markupa fasade** na `wp_footer`, bez ijedne izmene u
  bazi. 9/9 stranica verifikovano (200 / 1×H1 / 1×VideoObject / validan JSON /
  potvrđen `uploadDate`). Detalji i pravila: `woodmart-sabloni` **F7.3a**.

**Praktično za sledeći video:** kad basket video ode na YouTube, dovoljno je
(1) ubaciti standardni markup fasade u sadržaj stranice i (2) dodati njegov ID
u mapu u `al-video-schema.php`. Schema se emituje sama.

---

## 5. Google Ads — isti materijal, druga namena

Isti klipovi pokrivaju i Ads bez dodatnog troška:

- **Demand Gen / YouTube kampanje** — traže video asset; sada ga nemamo.
- **Performance Max** — video asset u grupi podiže domet; ako se ne priloži,
  Google generiše sopstveni (obično loš).
- **Vertikalni 9:16 rez** za Shorts/Reels — Flow podržava 9:16 direktno, ali
  to je **poseban render (još 20 kredita)**, ne besplatan rez postojećeg.
  Alternativa: krop 16:9 klipa u montaži, jeftinije ali gubi kadar.

⚠️ Ads je i dalje na **Maximize Clicks** dok se ne skupi 20–30 pravih
konverzija (CLAUDE §4). Video kampanje ne pokretati pre toga — potrošile bi
budžet bez signala za optimizaciju.

CTA u svim video asset-ima: **linija 72** (`069 234 00 72`) — dominira
klikovima na telefon (~50 vs ~7).

---

## 6. Šta još iz Google ekosistema vredi (istraženo 2026-08-09)

Izvori: labs.google (direktno) i juliangoldie.com lista. Ocena korisnosti je
moja, na osnovu naših stvarnih rupa.

### Vredi probati — konkretan povod kod nas

| Alat | Zašto baš nama |
|---|---|
| **NotebookLM** | Imamo gomilu PDF-ova (Bergo tehnički listovi, Ecotile sertifikati, Brockfill, REACH Condor) koje niko ne čita. Ubaciti ih → izvući FAQ pitanja, uporedne tabele, specifikacije za proizvod-stranice. Direktno gađa rupu „nema dubine sadržaja" iz analize konkurencije. **Najveći odnos vrednost/trud na listi.** |
| **Flow Music** | Naši klipovi su nemi. Besplatna muzika bez problema sa autorskim pravima, umesto traženja stock podloge. |
| **Opal** (mini-aplikacije) | Već imamo `planer-terena-basket3x3.png` / `planer-terena-tenis.png` kao **statične slike**. Interaktivni planer/kalkulator terena na sajtu je pravi lead magnet i tačno ono što konkurencija nema. |
| **Pomelli** | Brendirani marketing sadržaj — kandidat za W6 social kreativu. Neproveren. |
| **Stitch** | Ideja → UI. Može pomoći kod prelamanja novih WoodMart stranica. |

### Sporedno / preskočiti za sada

- **Whisk, Imagen** — preklapaju se sa onim što već radimo kroz
  [[gemini-red-cekanja]] i `ai-vizuali` skill.
- **Google Vids** — traži Workspace nalog, nemamo potrebu.
- **Jules, Gemini Code Assist** — razvojni alati, ne marketinški.
- Ostali labs eksperimenti (Dreambeans, Mixboard, Stax, Vantage, Project
  Genie, Literature Insights) — nema veze sa našim poslom.

> Napomena o izvoru: juliangoldie.com je SEO listicle, ne Google-ova
> dokumentacija. Sve što odatle ide u rad prvo proveriti na samom alatu —
> spiskovi te vrste često navode alate koji su promenili uslove ili nestali.

---

## 7. Sledeći korak

1. ✅ **2026-08-10 — video za stranicu #1 (basket) je gotov**: svih 5 kadrova,
   `AntasLine-teren-za-basket-40s{,-tekst}.mp4` (40,0s). Kadar 5 napravljen u
   Gemini-ju (Flow ostao bez kredita) → nosi Gemini vodeni žig, **odluka M**.
2. Promeniti YouTube handle `@antasline5676` → `@antasline` (§8). #ceka-miroslav
3. **Pre objave** rešiti lazy-facade embed i `VideoObject` JSON-LD (§4). #claude-code
4. ✅ **2026-08-10 — GSC CTR baseline snimljen**: 4.019 prikaza / 114 klikova /
   CTR 2,84% (11.07–07.08, 30 upita) →
   `analiza/2026-08-10-gsc-baseline-basket-pre-videa.json`
5. Tek posle merenja na prve 3 stranice odlučiti da li se red čekanja nastavlja.

## 8. YouTube — rešeno 2026-08-09

Kanal **postoji**: `youtube.com/@antasline5676`, status „mrtav" po
[[drustvene-mreze]] (popis 2026-07-07). Handle je auto-generisan (nasumične
cifre), nije brendiran.

**Odluka: objavljivati javno, ne unlisted.** Obrazloženje:

- Materijal je ionako već javan — iste te fotografije terena stoje na sajtu i
  u `/galerija/`. Unlisted ne krije ništa od konkurencije, samo nas košta.
- Unlisted video se **ne pojavljuje u YouTube pretrazi ni u predlozima** —
  time gubimo drugu površinu pretrage, koja je i jedan od glavnih razloga
  zašto uopšte pravimo video.
- Oživljen kanal je preduslov za YouTube/Demand Gen oglase (§5) i za W6 ritam
  „YouTube 1×/mes" — publike ionako čekaju prag serviranja od 100 korisnika.

Zavisnost: **promeniti handle pre prve objave.** Od tog trenutka handle ulazi
u embed URL-ove i `VideoObject` schema-u na sajtu, pa je jeftinije sada nego
posle deset objavljenih videa. Već je stavka u W6 planu
(`.claude/skills/w6-social/SKILL.md`, Faza 1c).

## 9. Otvoreno / #ceka-miroslav

- Cena Veo 3.1 Lite / Omni Flash — nije potvrđena, proveriti pri sledećem
  renderu; ako je Lite ~10 kredita, tempo se duplira.
- Ko menja YouTube handle (traži pristup Google nalogu koji je vlasnik kanala).
