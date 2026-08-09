---
tip: sesija
alat: claude-code
datum: 2026-08-09
blok: "-"
status: u-toku
---

# Sesija — Google Flow (Veo 3.1) video za basket stranicu

## Šta je urađeno

- **Provereno da Google Flow radi i da je upotrebljiv** za AntasLine: besplatan
  nalog, **50 kredita dnevno**, image-to-video iz naših pravih fotografija.
- **Izabran pristup** (M od 3 ponuđene opcije): hero kadrovi iz arhive pravih
  realizacija, 16:9, za stranicu `/kako-napraviti-teren-za-basket-ili-kosarkaski-teren/`.
  Odbijena „instruktivna montaža" — Veo bi izmislio pogrešan Bergo klik sistem.
- **5 fotki izabrano i iskropovano na 16:9** (PIL): Pelješac, Tara, Bajina Bašta,
  Ledine, dvorište. Izbor po rezoluciji, pejzažnom formatu i potvrđenom
  AntasLine poreklu (EXIF/GPS, poznate lokacije).
- **4 klipa izrenderovana** (8s svaki): Pelješac (Fast, 20 kredita), Tara /
  Bajina Bašta / Ledine (Lite, 10 svaki). Ukupno 50 kredita = ceo dnevni budžet.
- **ffmpeg 9.0 instaliran** (`winget install Gyan.FFmpeg`) — nije ga bilo.
- **Izmontiran finalni video**: `AntasLine-teren-za-basket-32s.mp4` —
  30,5s, 1280×720, 24 fps, prelazi 0,5s, fade in/out, **bez zvuka**
  (muzika + tekst se dodaju posle).
- **Napisan plan** za sistematsko obogaćivanje stranica videom →
  [[seo/2026-08-09-video-obogacivanje-plan]], sa redom čekanja izvedenim iz
  GSC brojeva, budžetom, Ads primenom i pregledom ostalih Google Labs alata.
- **Istraženo šta još iz Google ekosistema vredi** (labs.google direktno +
  juliangoldie lista): **NotebookLM je najveći sledeći dobitak** (imamo gomilu
  nepročitanih PDF-ova — Bergo tehnički listovi, Ecotile sertifikati, Brockfill,
  REACH Condor — iz kojih se vade FAQ i uporedne tabele), zatim Flow Music
  (klipovi su nemi) i Opal (interaktivni planer terena umesto statičnih
  `planer-terena-*.png` slika).

## Otvorene akcije

- [ ] Kadar 5 (dvorište, hero: koš + nebo) — 10 kredita, sutra #claude-code
- [ ] Remontirati video sa 5 kadrova kad kadar 5 bude gotov #claude-code
- [ ] Lazy „facade" embed (poster + iframe na klik) pre kačenja na stranicu #claude-code
- [ ] Provera da li Rank Math besplatan ima Video sitemap; ako ne — ručni `VideoObject` JSON-LD #claude-code
- [ ] Zabeležiti GSC CTR baseline za ciljane upite basket stranice pre objave #claude-code
- [ ] Promeniti YouTube handle `@antasline5676` → `@antasline` (traži pristup Google nalogu vlasnika kanala) #ceka-miroslav

## Beleške / odluke

### Cene modela — izmereno, ne pretpostavljeno
| Model | Krediti / 8s klip |
|---|---|
| Veo 3.1 – Lite | **10** |
| Veo 3.1 – Fast | **20** |

Na sporim pokretima kamere nad statičnim terenom **razlika Lite/Fast se ne vidi**.
Podrazumevano Lite → **5 klipova dnevno**, ne 2. Prvi klip je nepotrebno pušten
na Fast pre nego što je cena Lite-a bila poznata.

### Pravilo prompta koje čuva autentičnost
Traži se **samo pokret kamere i ambijent**, plus eksplicitno „keep the court
surface, colours and markings exactly as in the photo" i „do not add any new
objects, people or basketballs". Čim se zatraži radnja (igrač, lopta kroz
obruč), Veo izmišlja i podloga prestaje da bude naša realizacija.

### Zašto video, konkretno (nije estetika)
Kroz skoro sve GSC klastere isti obrazac: **pozicija 1–3, skoro nula klikova**,
jer Google odgovara direktno u rezultatima. Video + `VideoObject` schema menja
**izgled samog rezultata** (sličica uz link) — jedan od retkih poteza koji
napada baš taj problem. **Hipoteza, ne garancija** — merenje je GSC CTR 28d
pre/posle po stranici, stop posle 3 stranice ako nema pomaka.

### YouTube
Kanal postoji (`youtube.com/@antasline5676`), „mrtav", handle auto-generisan.
Odluka: **javno, ne unlisted** — materijal je ionako javan na sajtu, unlisted se
ne pojavljuje u YouTube pretrazi (gubimo drugu površinu), a oživljen kanal je
preduslov za YouTube/Demand Gen oglase i W6 ritam.

### Usput nađeno
`C:\Miroslav\Antas Line priprema za sajt\kosarkaski teren\` ima **originalna
uputstva za montažu iz 2018** — tačan prikaz Bergo klik sistema, bolji materijal
za „Kako se postavljaju Bergo podloge" nego bilo koji AI klip.

## Veze
- Plan: [[seo/2026-08-09-video-obogacivanje-plan]]
- Shot lista + promptovi: [[seo/2026-08-09-flow-promptovi-basket]]
- GSC osnova za red čekanja: [[seo/2026-07-27-content-klasteri]]
- Foto izvori: [[reference/foto-arhiva-inventar]]
- YouTube/social stanje: [[reference/drustvene-mreze]]
- Lekcije: [[reference/naucene-lekcije]]
