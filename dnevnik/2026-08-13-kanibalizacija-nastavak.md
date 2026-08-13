---
tip: dnevnik
alat: claude-code
datum: 2026-08-13
blok: C
status: zavrseno
oblast: W2/SEO + vault higijena
naslov: Kanibalizacija — nastavak (slugovi, FAQ klaster) + PROGRESS higijena
---

# Sesija — kanibalizacija, nastavak

Nastavak liste od 9 tačaka iz [[seo/2026-08-13-kanibalizacija-konsolidacija-plan]] (jutarnja
sesija zatvorila C/D/B). Pet celina, sve po M nalogu, sve povratne.

## Šta je urađeno

### 1. Čist slug „preko starog parketa" (stavka J)
M je pretpostavio da su dve verzije spojene u jednu stranicu i tražio da sadržaj ostane a
`-2` nestane iz sluga. **Provera je oborila pretpostavku** — dva različita članka, oba 200 sa
različitim canonical-om: **16613** (2022, 4.940 zn., SEO title „PVC podovi i podovi od vinila",
noindex od 30.07) i **6588** (prepis 09/2025, 8.041 zn., isti sadržaj **+ Ecotile + FAQ +
galerija**). Spajanje je zapravo već bilo izvedeno u smeru `-2`; u 16613 nema nijednog
jedinstvenog pasusa. 16613 → draft + `…-original-2022`, **6588 uzeo čist slug**, 301 okrenut.

🔴 **Ovo ukida odluku od 30.07** (`redirect-mapa-FINAL.csv` red 18), koja je na osnovu GSC
preseka (`-2`: 258 kl. / poz. 5,5 vs 84 / 7,6) zaključila obrnuto. 🟢 Olakšavajuća okolnost
koja je promenila procenu rizika: **cilj 301 nije nov URL** — živi na produkciji od 2022 i nosi
svojih 84 klika/god, dakle konsolidacija dva Google-u poznata URL-a.
Detalji: [[dnevnik/2026-08-13-slug-swap-parket]]

### 2. PROGRESS.md — 1,4 MB → 247 KB
M je prijavio „puno praznog i čudne linije". Tri odvojena problema:
**(a)** **75% fajla su bili razmaci** (1.061.601 od 1.408.706 B) — plugin **Advanced Tables**
poravnava kolone dopunom do širine najšire ćelije; sa ćelijama od 1–4 hiljade znakova svaki
red je naduvan na ~4.800 znakova. Fajl koji CLAUDE.md §12 nalaže da se čita **prvi na svakoj
sesiji** nije mogao da se otvori Read alatom (106.490 tokena vs limit 25.000).
**(b)** linija 34 je nosila **neescape-ovan `|`** u regexu → Obsidian lomio red u fantomsku
kolonu. ⚠️ Prijavio sam **dva** takva reda — linija 79 je već bila ispravna, brojač pipe-ova
ju je lažno prijavio.
**(c)** tabela je imala **273 reda** → **183 (jun+jul) izmešteno** u
[[dnevnik/2026-07-arhiva-progress]].
Podešeno `formatType` → `weak` da se dopuna ne vraća.

### 3. Čist slug „ergonomske podloge" (stavka A)
🔵 `-2` je ovde **druge prirode** nego kod parketa: WP-ov automatski sufiks jer je čist slug
držao **prilog** (12489), ne drugi članak. Prilog → `ergonomske-podloge-foto` (putanja fajla
nedirnuta), stranica uzela slug, **istorijsko pravilo `/ergonomski-podovi/` (160 pogodaka)
pretočeno** sa `-2` na nov cilj.

🔴 **Glavni nalaz nije bio slug nego katalog:** 8 tipova podloga sa te stranice (Diamond
Allround, Soft Air Meter, SuperSoft Smooth/Office, La Ola, La Ola Hygienic, Nitrile Walk,
Solido I) **nema nijedan proizvod** ni na buildu ni na live-u, a telo stranice nema **nijedan**
interni link osim kontakta i telefona. `/brend/ergomat/` nije zamena (27 proizvoda =
odbojnici/trake/senzori). M odobrio obim, **odložio izvršenje** →
[[migracija/w1-ergonomske-podloge-proizvodi]].

### 4–5. FAQ klaster „izbor industrijskog poda" — sve tri stranice u hub
| Stranica | GSC 12 meseci | Stanje |
|---|---|---|
| 2622 `/izbor-…-tri-najcesca-pitanja/` | 128 prikaza / **0 kl.** | draft → 301 na hub |
| 3274 `…-2` | 98 / **0 kl.** | draft (od 27.07) → 301 na hub |
| 17025 `/industrijski-podovi-najcesca-pitanja/` | 4 / **0 kl.** | draft → 301 na hub |
| **16567 `/industrijski-podovi/`** | **16.417 / 410 kl.** | **15 FAQ pitanja + FAQPage** |

Sve tri gađale su upit „industrijski podovi" sa poz. 20–80, koji hub drži na **6,7**.
2622 i 3274 obrađuju **ista tri pitanja** (3274 je prepričan 2622), a jedini dodatak 3274
(Ecotile 500/5–500/7–500/10) hub već ima.

**Preneto na hub — 8 pitanja koja nije imao:** okvir odluke · svež beton u novogradnji ·
priprema u odnosu na premaze · otkup starog poda · samostalna montaža · **spoljašnja upotreba
(odgovor NE)** · postavljanje preko farbanog betona/keramike/tepiha/vinila · kada je lepak
potreban (Uzin MK92S). **Hub je usput dobio FAQPage JSON-LD koji do tada uopšte nije imao.**

## Otvorene akcije

- [ ] **E** — `/sportske-podloge/` (5438): vratiti basket-semantiku + link na `/planer-terena/`, ~1,5 h, **rok 16.08** #claude-code
- [ ] **F** — dimenzije klaster (16585/16586/16688/17027) vs post 2298, ~1 h, **rok 16.08** #claude-code
- [ ] **Ergonomske podloge** — nova `product_cat` + 8 proizvoda, slike sa ergomat.com; **rok 16.08 ako ide u migraciju**, inače post-live #claude-code
- [ ] **Restart Obsidiana** da `formatType: weak` proradi #ceka-miroslav
- [ ] Postovi iz ugašenog FAQ klastera (P1–P5) — **posle live-a** → [[seo/posle-live-postovi-izbor-industrijskog-poda]] #claude-code

## Beleške / odluke

🔴 **Zapisane GSC brojke u migracionim CSV-ovima nisu pouzdane — tri promašaja u jednoj
sesiji.** `parity-inventar.csv` pripisuje `/ergonomske-podloge-2/` **110 klikova** (stvarno:
1 prikaz / 0 kl. u 90d, 123/4 u 12 mes.) i nosi `lokal_id` postova koji **ne postoje**
(15977/15967); `redirect-mapa-FINAL.csv` red 17 tvrdi „311 klikova / poz. 6,9 / CTR 4,92%" za
2622 (stvarno: **0 klikova u 12 meseci**). **Pre svake odluke ide svež pull, ne oslanjanje na
zapis.**

🔴 **Draftovanje stranice uvek traži proveru da li je neko istorijsko pravilo cilja.**
U ovoj sesiji dva puta: `/ergonomski-podovi/` (**160** pogodaka) i
`/home/industrijski-podovi-najcesca-pitanja/` (**615**). Bez pretakanja bi posle migracije
oba vodila na **404**. Isti razred kao 4 pravila sa 365 pogodaka uhvaćena jutros.

🔵 **Dva `-2` sluga, dva različita uzroka.** `ergonomske-podloge-2` = WP automatski sufiks
(slug drži prilog) → čist slug je besplatan. `…-plocica-2` = namerno drugi post → čist slug
košta jednu 301 selidbu. Prvo je higijena, drugo odluka o saobraćaju.

🟢 **FAQPage schema se gradi parsiranjem vidljivog teksta, ne ručnim prepisom** — inače se
vremenom raziđu, što Google čita kao neusklađenost. Pri drugoj izmeni se stari blok **briše i
gradi iznova**; skripte odbijaju upis ako ne parsiraju očekivan broj parova.

🔵 **Lažna uzbuna:** `/podovi-za-magacine-i-hale/` vraća 301 — 16687 je child stranica huba,
flat oblik uvek 301-uje na ugnježden (200). Nije posledica ovog rada.

**Backup-i:** `antasline_local_2026-08-13_pre-slug-swap-parket.sql` ·
`…_pre-slug-swap-ergonomske.sql` · `…_pre-faq-konsolidacija.sql` · `…_pre-faq-17025.sql`
**Skripte:** `job-slug-swap-parket-2026-08-13.php` · `job-slug-swap-ergonomske-2026-08-13.php` ·
`job-faq-konsolidacija-2026-08-13.php` · `job-faq-17025-konsolidacija-2026-08-13.php`

## Veze

- [[seo/2026-08-13-kanibalizacija-konsolidacija-plan]] (izvor, §1 i §5)
- [[dnevnik/2026-08-13-konsolidacija-kanibalizacija]] (jutarnja sesija, C/D/B)
- [[dnevnik/2026-08-13-slug-swap-parket]] · [[seo/posle-live-postovi-izbor-industrijskog-poda]]
- [[migracija/w1-ergonomske-podloge-proizvodi]] · [[reference/naucene-lekcije]]
