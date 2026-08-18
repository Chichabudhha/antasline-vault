---
tip: sesija
datum: 2026-08-18
tag: claude-code
blok: W1 / W2 — ESD klaster
status: zavrseno
azurirano: 2026-08-18
---

# ESD klaster — zvanična dokumentacija, dva nova proizvoda, dopuna stranice 16658

## Odgovori na 4 `#ceka-miroslav` pitanja (zatvoreno)

M je dao izvore; vrednosti su izvučene iz zvaničnih Ecotile tehničkih listova, ne iz procene.

| # | Pitanje | Odgovor |
|---|---|---|
| 1 | Deklaracija ESD serije | X-Joint ESD: površinski **1,46 × 10⁶ Ω**, prema zemlji **9,3 × 10⁵ Ω** · E500/7 ESD: **2,2 × 10⁴ – 3 × 10⁶ Ω** / **2,9 × 10⁴ – 5,7 × 10⁵ Ω**. Metode EN/ISO 61340-5, EN 1081, EN 1815, EN 6356; usklađeno sa **BS EN / IEC 61340-5-1:2016** |
| 2 | Elektroprovodljivo / ATEX | **Na upit, ne paušalno.** Datasheet: standardna ploča se uzemljuje preko 1 MΩ otpornika, a Ecotile nudi rešenja „from conductive to the higher end of the ESD spectrum". Kopi sme „elektroprovodljiva varijanta na upit, uz deklaraciju za konkretnu isporuku"; **ne sme** „ATEX sertifikovano" |
| 3 | Merenje sa zapisnikom | **Da** — ušlo kao istaknuta usluga na stranicu, u FAQ i na oba nova proizvoda |
| 4 | Cena ESD | **Na upit** — ESD ne dobija cenovni red; industrijska linija zadržava 5.500/6.800 RSD/m² |
| 5 | Srpski propis | Live stranica se ne poziva ni na jedan domaći propis. Pravilo ostaje: **ne pisati „zakonska obaveza"**, nego „standard koji se traži u auditima" |

## Ispravka mog ranijeg nalaza o parity-ju

🔴 Prijavio sam da „42 od 52 rečenice sa live-a ne postoje na buildu" i izveo iz toga
osam rupa. **To je bilo pogrešno.** Poređenje je išlo po rečenicama, a build je live
tekst **prepisao, ne izbacio** — pa se ni jedna rečenica nije poklopila doslovno iako
je značenje preneto. Provera punog sadržaja 16658 pokazala je da build **već ima**:
ATEX/zapaljive materijale, zoniranje bojama, opremu na točkovima, MoD UK i listu
klijenata, 25 godina iskustva, premeštanje u drugi prostor (ugao za zakupce),
uzemljenje na min. 80 m², tepih/kvaka objašnjenje, cenu „zavisi od kvadrature".

Stvarnih rupa je bilo **pet**, i sve su zatvorene ovom sesijom.

## Šta je urađeno

**Backup:** `antasline-backups/antasline_local_2026-08-18_2015_pre-esd-rebuild.sql` (35,25 MB).

### 1. Sedam zvaničnih PDF-ova u medijateku
Preuzeto sa ecotileflooring.com, kopirano u `uploads/2026/08/`, kreirani attachment
zapisi (ID 17850–17856). Svi vraćaju HTTP 200.

Tehnički listovi: X-Joint ESD (SD130) · E500/7 ESD (SD100) · X-Joint antistatik (02/2026) ·
**zapisnik ispitivanja X500/7 ESD (7 strana)** · uputstva za ugradnju SD127 i SD113 ·
uputstvo za održavanje.

> Zapisnik ispitivanja je najvredniji dokument za B2B — traži ga inženjer kvaliteta uz
> auditsku dokumentaciju EPA zone. Stavljen je visoko, ne u podnožje.

### 2. Tri ploče, ne jedna — dva nova proizvoda

Datasheetovi su otkrili da su u pitanju **tri različite ploče**, a katalog je imao jednu:

| Ploča | Otpor | Uzemljenje | Status |
|---|---|---|---|
| X-Joint ESD 7 mm (16542) | 1,46 × 10⁶ / 9,3 × 10⁵ Ω | da | postojao |
| **E500/7 ESD** (17860) | 2,2×10⁴–3×10⁶ / 2,9×10⁴–5,7×10⁵ Ω | da | 🆕 dodat |
| **X-Joint antistatik** (17861) | ≈ 1 × 10⁹ Ω | **ne** | 🆕 dodat |

Treća je **antistatik, ne ESD** — to je razlika koju stranica ranije nije objašnjavala,
a koja određuje da li prostor mora da se uzemlji. Oba nova proizvoda: pun tehnički
tabelarni prikaz iz deklaracije, linkovi na PDF-ove, unakrsni linkovi na ostale dve
ploče i na hub stranicu, Rank Math title/meta, cena „na upit" (bez `_price`, kao 16542),
9 atributa preko postojećih `pa_*` taksonomija + 4 nova terma (412–415).

### 3. Stranica 16658 — pet dopuna
- **Uporedna tabela antistatik / ESD / elektroprovodljivo** zamenila staru spec tabelu.
  🔴 Stara je nosila vrednost „otpornost 3,4×10⁴ – 5×10⁶ Ω" koja **ne postoji ni u jednom
  tehničkom listu** (preneta sa live-a) i „debljina 7 mm (dostupno i 5 mm)" — 5 mm ESD
  nije potvrđen nijednom deklaracijom. Zamenjeno deklarisanim vrednostima po seriji.
- **Sekcija „Merenje otpora sa zapisnikom"** — usluga, sa objašnjenjem zašto je zapisnik
  ono što zatvara posao (pod može biti ispravan, ali se bez zapisnika ne može dokazati).
- **Sekcija „Zašto ne epoksidni antistatik pod"** — conquest ugao koji je nedostajao, sa
  linkom na `/epoksidni-podovi-ili-ecotile-podovi/`.
- **Sekcija „Tehnička dokumentacija"** sa svih 7 PDF-ova.
- **FAQPage JSON-LD** — nije postojao (isti propust kao na 5438, nađen 13.08), plus dva
  nova pitanja: razlika antistatik vs. ESD i merenje sa zapisnikom. Ukupno 7 pitanja.

Stranica: 13.797 → 22.925 znakova.

### 4. Proizvod 16542 dopunjen
Dodata sekcija dokumentacije (4 PDF-a), deklarisane vrednosti otpora i unakrsni linkovi
na dve nove ploče i hub.

## Tri gotcha-a iz ove sesije

1. 🔴 **Windows CRLF konverzija kvari `post_content` pri čitanju iz `mysql -B --raw`.**
   Sadržaj 16658 ima interne `\r`/`\n`, koje pipe pretvara u `\r\n` — readback se ne
   poklapa sa upisom. Rešenje: čitati **`SELECT HEX(post_content)`** i dekodirati u
   Pythonu, pisati preko `UNHEX(...)`, pa obavezno uporediti. Helper: `wpdb.py`.
2. 🔴 **Novi proizvod ne ulazi u WooCommerce upite bez reda u `wpgs_wc_product_meta_lookup`.**
   SQL insert u `wpgs_posts` + `postmeta` + `term_relationships` nije dovoljan.
3. 🔴 **Rank Math kešira sitemap kao FAJLOVE** u `wp-content/uploads/rank-math/*.xml`.
   Brisanje opcije `rank_math_sitemap_cache_files` i transienata **ne pomaže** — sitemap
   je i dalje vraćao `lastmod` od 13.08. Tek `rm *.xml` iz tog foldera regeneriše.
   Isti obrazac kao „trebalo je `wp rewrite flush`" iz CLAUDE §7.1.

## Verifikacija

| Provera | Rezultat |
|---|---|
| 4 URL-a (stranica + 3 proizvoda) | 200, **1×H1** svuda |
| JSON-LD | stranica: FAQPage + Article + LocalBusiness, svi validni · proizvodi: Product + BreadcrumbList, validni |
| 7 PDF linkova | 200 |
| 6 internih linkova iz novog sadržaja | 200 |
| Grid na stranici | prikazuje sve tri ploče |
| Sitemap | oba nova proizvoda prisutna posle purge-a keša |

### 5. Slike proizvoda sa proizvođača (dopuna, ista sesija)

Šest fotografija preuzeto sa ecotileflooring.com i shop.ecotileflooring.com, obrađeno po
projektnom standardu — **1:1 center-crop, max 1000×1000, WebP**:

| Proizvod | Glavna | Galerija |
|---|---|---|
| E500/7 ESD (17860) | `e500-ploca` 705² | `e500-spajanje` 1000² · `e500-t-joint` 705² |
| X-Joint antistatik (17861) | `xjoint-ploca` 705² | `xjoint-spajanje` 705² · `xjoint-tamno-siva` 934² |

Za svaku su **generisane WP veličine** (150/300/400/600/768/900 px) i upisan pun
`_wp_attachment_metadata` — bez toga bi grid vukao punu sliku umesto 300 px varijante.
Alt tekstovi na srpskom, opisni. Attachment ID 17870–17875, `post_parent` = proizvod.

⚠️ **Napomena o izvoru:** Ecotile ne objavljuje zasebnu fotografiju antistatik ploče —
X-Joint ploča je vizuelno identična ESD verziji (razlika je u materijalu: antistatik PVC
bez čeličnih vlakana). Za 17861 su zato uzete fotografije X-Joint formata **bez priključka
za uzemljenje**, jer se ta ploča ne uzemljuje; fotografija sa uzemljivačkim priključkom
namerno **nije** korišćena na antistatik proizvodu.

Verifikovano: obe stranice 200, glavna slika i po 3 slike u galeriji, `srcset` radi u
gridu (300w/150w/400w/600w/705w), **Product schema sada nosi `image`**.

### 6. SRPS provera (dopuna, ista sesija) — poslednja otvorena stavka zatvorena

| | |
|---|---|
| **Na snazi** | **SRPS EN 61340-5-1:2017** + ispravka **/AC:2020** (stadijum 60.60), identično preuzet EN 61340-5-1:2016 / IEC 61340-5-1:2016 |
| **U toku** | `dnaSRPS EN IEC 61340-5-1:2024` — objavljeno 07.08.2024, stadijum 50.99, **još nije zamenilo** izdanje iz 2017 |
| **Prateći** | SRPS CLC/TR 61340-5-2:2012 (tehnički izveštaj, uputstvo za primenu) |
| **Pravni status** | Primena SRPS-a je po *Zakonu o standardizaciji* (Sl. glasnik RS 36/2009, 46/2015) **dobrovoljna**, osim kad se tehnički propis izričito pozove na standard. Za ESD pod takav propis **nije nađen** |

🔴 **Ali obaveza postoji u susednom polju.** Propis o preventivnim merama za rad u
eksplozivnim atmosferama obavezuje poslodavca da proceni i kontroliše izvore paljenja,
**u koje izričito spada elektrostatičko pražnjenje**. To je stvarna zakonska dužnost — ali
na kontroli statike u tim prostorima, ne na kupovini određenog poda.

**Pravilo za kopi, konačno:**
- ✅ sme „SRPS EN 61340-5-1:2017" kao domaća oznaka — traže je službe nabavke i kontrole kvaliteta
- ✅ sme „u eksplozivnim atmosferama poslodavac je dužan da kontroliše elektrostatičko pražnjenje kao izvor paljenja"
- ❌ **ne sme** „ESD pod je zakonska obaveza"

Oba dozvoljena iskaza su upisana na stranicu 16658 (23.815 znakova).

### 7. Vizuelno presecanje teksta (M primedba: „izgleda kao jedan veliki blok teksta")

Posle svih dopuna gornji deo stranice je bio ~10k znakova čistog teksta bez ijedne slike.
Dodato tri vizuala, svaki na mestu gde nešto objašnjava — ne kao ukras:

1. **Skala otpora** — prvo urađena kao SVG, pa **odbačena i prerađena** (v. ispod).
   Ecotile ima sličan dijagram, ali na engleskom i u svojoj zelenoj — zato je rađen naš.
2. **Foto-red tri ploče** posle uporedne tabele, sa natpisima koji nose poruku
   („bez uzemljenja" / „uzemljuje se" / „priključak ima samo ESD verzija").
3. **Foto mreže bakarnih traka** (naša ugradnja, HTEC Niš) uz sekciju o merenju — pokazuje
   ono što tekst tvrdi: bez trake nema uzemljenja, bez uzemljenja nema zapisnika.

🔴 **Ispravka opsega, izašla iz crtanja dijagrama.** Da bih ucrtao trake morao sam da
fiksiram granice — i tada se videlo da kopi tvrdi **„elektroprovodljivo = ispod 10⁶ Ω"**,
dok **sama Ecotile skala kaže 10¹–10⁴ Ω** (ESD 10⁴–10⁹, antistatik 10⁹–10¹¹, izolativno
preko 10¹¹). Ispravljeno na tri mesta, uključujući FAQ JSON-LD. Vrednost „ispod 10⁶" je
došla iz istraživanja od 18.08 i bila je preuzeta iz sekundarnih izvora, ne iz deklaracije.

🔴 **Pogrešna fotografija uhvaćena tek na ekranu.** Slika `xjoint-ploca` (sa Ecotile ESD
stranice) **ne prikazuje golu ploču nego pribor za uzemljenje** — bakarnu traku, žuti
priključak i kabl. Bila je postavljena kao glavna slika **antistatik** proizvoda i kao prva
kartica u foto-redu, tačno ispod natpisa „bez uzemljenja". Protivrečnost se ne vidi iz HTML
provere, samo okom. Ispravljeno: pribor prebačen u galeriju **ESD** proizvoda 16542 (gde i
pripada), antistatik dobio čistu fotku spojenih ploča, naslovi i alt tekstovi usklađeni.

### 8. SVG skala odbačena — zamenjena HTML/CSS komponentom `.al-scale`

M primedba: *„ne objašnjava dobro, a na mobilnom se slabo vidi jer je sitno."* Oba nalaza
tačna, i drugi je **strukturni, ne stvar podešavanja**: u SVG-u se tekst skalira zajedno
sa slikom, pa širok dijagram od 1000 px na telefonu od 390 px pretvara natpis od 13 px u
~5 px. Nijedno povećanje fonta to ne rešava dok je oblik horizontalna traka.

**Rešenje:** ista informacija kao **pravi HTML tekst** u novoj komponenti dizajn sistema
`.al-scale` (dodata u `css/antas-design.css`, prati postojeće `.al-grid` konvencije):
4 kolone na desktopu → 2 na ≤992 px → **1 kolona na ≤576 px**. Font ostaje 13–17 px
bez obzira na širinu, tekst se selektuje, čita čitačem ekrana i indeksira.

**I sadržaj je prerađen da objašnjava, ne samo da prikazuje.** Umesto gole ose sa
brojevima, svaka kolona sada odgovara na pitanje kupca:

| | Elektroprovodljivo | ESD | Antistatik | Izolativno |
|---|---|---|---|---|
| Opseg | 10¹–10⁴ Ω | 10⁴–10⁹ Ω | 10⁹–10¹¹ Ω | preko 10¹¹ Ω |
| Šta radi | najbrže odvodi | **kontrolisano** odvodi | sprečava nakupljanje | ne odvodi ništa |
| Za koga | ATEX zone | elektronika, laboratorije, EPA | server sale, kancelarije | običan pod — tu nastaje problem |
| Uzemljenje | obavezno | obavezno, 1 MΩ | nije potrebno | nema svrhe |
| Naše | po zahtevu | X-Joint ESD · E500/7 ESD | X-Joint antistatik | — |

Ključna poruka koju stara verzija nije nosila: **„niže nije automatski bolje"** — u EPA
zoni se traži kontrolisano odvođenje, a u kancelariji je dovoljno sprečiti nakupljanje.
Dodata je i četvrta, „izolativna" kolona kao kontrast — pokazuje zašto običan pod nije opcija.

🔴 Gotcha: WoodMart reset spljošti `<sup>` na baseline, pa je `10⁴` renderovano kao `104`.
Vidi se samo na ekranu. Popravljeno pravilom `.al-scale sup { vertical-align: super }`.

🔴 Gotcha: neuspeo bash heredoc je upisao doslovnu liniju `grep -c "al-scale sup" …`
**u CSS fajl**. Nije prijavio grešku, a nevalidan red u CSS-u može oboriti parsiranje
pravila ispod. Obrisano. Pouka: posle svakog `>>` na produkcioni fajl proveriti `tail`.

Stranica: 23.815 → 28.062 znakova (HTML komponenta je kraća od SVG-a).
Vizuelno provereno u brauzeru — desktop presek potvrđen; **širina od 390 px nije uspela
da se testira** (`resize_window` nije promenio viewport u ovoj sesiji), ali su prelomne
tačke iste kao kod `.al-grid--3`, koji je već proveren na buildu.

## Otvoreno

- **Video** — M predložio Flow/Veo ili Gemini. Nije rađeno ove sesije; predlog kadrova je
  u izveštaju (skala otpora kao animacija + polaganje bakarne trake iz naših fotki HTEC/Quectel).
  Traži M-ov Flow nalog i kredite → [[dnevni-video]] tok.
