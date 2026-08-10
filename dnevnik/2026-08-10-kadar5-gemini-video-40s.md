---
tip: sesija
alat: claude-code
datum: 2026-08-10
blok: "W2/W6 video"
status: zatvorena
---

# Sesija — kadar 5 (Gemini) + video 40s + GSC baseline

## Šta je urađeno

- **Kadar 5 izrenderovan u Gemini-ju** (`gemini.google.com` → „Направи видео"),
  image-to-video iz `05-dvoriste.jpg`: 10s, 1280×720, 24 fps, 16:9.
  Prompt isti kao u [[seo/2026-08-09-flow-promptovi-basket]] §5, uz dopunu
  „keep the court surface, colours and markings exactly as in the photo".
- **Vizuelna provera** na 4 frejma (0/3/6/9s): podloga, boje i linije terena
  netaknute, nema izmišljenih ljudi ni lopti, tilt-up prati zadati pokret.
- **Video remontiran na 5 kadrova, 40,0s** (ffmpeg, prelazi 0,5s, fade in/out):
  - `AntasLine-teren-za-basket-40s.mp4` — bez teksta
  - `AntasLine-teren-za-basket-40s-tekst.mp4` — tekst po sekcijama članka,
    CTA `AntasLine · 069 234 00 72` u centru na kadru 5 (32,0–39,8s)
  - stara 30,5s verzija (4 kadra) ostaje netaknuta kao rezerva
- **GSC baseline snimljen** pre objave videa (quick-win):
  `analiza/2026-08-10-gsc-baseline-basket-pre-videa.json`

### GSC baseline — `/kako-napraviti-teren-za-basket-ili-kosarkaski-teren/`
Period 2026-07-11 → 2026-08-07 (28 dana, prozor pomeren zbog kašnjenja podataka).

| | |
|---|---|
| Prikazi | **4.019** |
| Klikovi | **114** |
| CTR | **2,84%** |

Najveće curenje (pozicija odlična, klikova skoro nema):

| Upit | Prikazi | Klikovi | Poz |
|---|---|---|---|
| dimenzije fudbalskog terena | 762 | 3 | 1,1 |
| fudbalski teren dimenzije | 220 | 0 | 1,1 |
| dimenzije košarkaškog terena (ć/c varijante) | 396 | 13 | 1,3–1,8 |
| visina koša / visina koša u košarci / visina košarkaškog koša | 275 | 0 | 1,2–2,1 |

Merenje efekta: isti upit-set, 28 dana posle objave videa, isti izvor.

## Otvorene akcije

- [ ] Odluka o Gemini vodenom žigu na kadru 5 (v. dole) #ceka-miroslav
- [ ] Odluka o registarskoj tablici na kadru 4 (Ledine) pre javne objave #ceka-miroslav
- [ ] Promeniti YouTube handle `@antasline5676` → `@antasline` #ceka-miroslav
- [x] ✅ Lazy „facade" embed — **već postojao** (F7.3, od 2026-07-07), ništa nije trebalo graditi
- [x] ✅ `VideoObject` JSON-LD — Rank Math nema Video modul, napisan `inc/al-video-schema.php`, 9/9 stranica verifikovano (v. [[migracija/woodmart-sabloni]] F7.3a)

## Beleške / odluke

### 🟢 Ispravka: Gemini upload JESTE automatizovan
Jučerašnja beleška („Gemini nema `input[type=file]` u DOM-u, provereno JS-om, pa
je image-to-video preko njega zatvoren za automatizaciju") je **netačna**.
Input postoji — ali se kreira **tek kad se otvori odgovarajući meni**:
`+` → „Направи видео" → ikonica slike ispod prompt polja. Tek tada
`file_upload` MCP alat radi normalno (5 MB JPG prošao bez problema).

Isti obrazac važi za Flow: `+` u prompt boksu → „Upload media" → input se pojavi.

**Pravilo:** provera DOM-a **pre** otvaranja menija daje lažno negativan
rezultat. Nikad ne zaključivati „nema file input-a" iz jednog `querySelectorAll`
na zatvorenom UI-ju.

### 🔴 Gemini stavlja vidljiv vodeni žig, Flow ne
Gemini klipovi nose „sparkle" ikonicu u **donjem desnom uglu, kroz ceo klip**.
Provereno poređenjem istog ugla kadra: Gemini klip ima, oba juče proverena Flow
klipa nemaju.

Praktično: tuđ brend na našem materijalu ako video ide na sajt, YouTube ili u
Ads. Opcije (odluka je M-ova, ništa nije dirano):
1. ⭐ **Renderovati isti kadar u Flow-u posle ~09–10h** kad se krediti resetuju
   (10 kredita, Lite) — čist klip, remontaža je jedna komanda. **Preporuka.**
2. **Ostaviti kako jeste** — žig je diskretan, na travi u uglu.
3. **Izbaciti kadar 5** iz montaže i vratiti se na 30,5s verziju sa 4 kadra.
4. Krop/`delogo` — **ne preporučeno**: gubi se kadar, a uklanjanje vidljivog
   žiga je odvojeno pitanje uslova korišćenja, ne tehničko.

### 🟡 Flow u 06:45 još bez kredita — ali to je pitanje sata, ne plana
Pokušaj da se kadar 5 renderuje i u Flow-u (radi čiste verzije) pao je na
**„You need more AI credits to complete this request"**.

**Prvi zaključak („besplatni nalog nema dnevne kredite") je bio pogrešan** i
ispravljen je pre upisa: [[reference/naucene-lekcije]] već sadrži nalaz od
00:34 noćas da se Flow krediti resetuju po **pacifičkoj** ponoći, što je
≈09–10h po lokalnom vremenu. U 06:45 je prosto bilo prerano. Baner „Daily
Bonus: **Paid plans** enjoy 50 extra credits (Resets daily)" govori o *dodatnih*
50 za plaćene planove i ne isključuje osnovnu besplatnu kvotu.

**Praktično:** čist kadar 5 (bez vodenog žiga) verovatno se može renderovati
danas posle ~09–10h za 10 kredita. To je najjeftinije rešenje nalaza o žigu i
treba ga probati **pre** bilo koje druge opcije.

### 🟢 Dva jučerašnja Flow gotcha-a potvrđena
- **Agent se zaglavi** (najavi render, nikad ne pokaže dugme za odobrenje) —
  reprodukovano tačno. **Lek radi**: nova sesija (ikona olovke pored naslova
  panela) odmah je dala „1 video generation, costing 10 credits".
- **Model se traži u tekstu prompta**, ne kroz Agent settings — potvrđeno,
  agent odgovorio „using the Veo 3.1 - Lite model".

### ⚠️ ffmpeg 9.0 — promenjena sintaksa
`-filter_complex_script fajl.txt` više ne postoji („Unrecognized option").
Nova sintaksa: **`-/filter_complex fajl.txt`**. Bitno jer se filter za tekst
preko videa ne može proslediti u komandnoj liniji (Claude Code bash ograničenje
~965 bajtova).

### Trajanje klipova — Gemini 10s vs Flow 8s
Gemini vraća **10-sekundne** klipove (i sa audio stream-om, koji se ionako
odbacuje `-an`), Flow 8-sekundne. Zato je finalni video 40,0s a ne 38,5s.

## Veze
- Plan: [[seo/2026-08-09-video-obogacivanje-plan]]
- Shot lista + promptovi: [[seo/2026-08-09-flow-promptovi-basket]]
- Prethodna sesija: [[dnevnik/2026-08-09-google-flow-video-basket]]
- Lekcije: [[reference/naucene-lekcije]]
