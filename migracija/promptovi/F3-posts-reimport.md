---
tip: prompt
faza: F3
naziv: Pun reimport 30 postova sa live
menja-bazu: DA ⚠️ (briše postove!)
preduslov: F1 gotov (LOKAL-NOVO statusi) + backup baze
---

# F3 — Pun reimport postova sa live

**Cilj:** lokalni blog = tačna kopija live bloga (30 postova, sa slikama).
Miroslavljeva odluka 2026-07-07: pun reimport, ne krpljenje — postojeći lokalni
postovi su "stari stil" i ionako idu na restyle. Strategija: [[migracija/PARITY-PLAN]].

**Izvor:** `migracija/live-export-2026-07-05/live-posts-2026-07-05.xml`
(WXR: 30 postova + 179 attachment-a). Ako je export stariji od ~2 nedelje u trenutku
izvršavanja, prvo povuci svež export sa live (Tools→Export na live admin-u traži
Miroslava — ili radi sa postojećim i zabeleži datum).

## Okruženje

| Šta | Vrednost |
|---|---|
| Lokalni WP | `C:\xampp\htdocs\antasline` → `http://localhost/antasline` |
| DB | `antasline_local`, prefiks `wpGs_` |
| PHP skripte | scratchpad + `C:\xampp\php\php.exe skripta.php` (`require 'C:/xampp/htdocs/antasline/wp-load.php';`) |
| Backup | `mysqldump -u root antasline_local > C:\xampp\htdocs\antasline-backups\antasline_local_YYYY-MM-DD_pre-posts-reimport.sql` |

## Koraci

1. 🔴 **BACKUP baze.**

2. **Zabeleži ID mapu PRE brisanja** — vault dokumenti referenciraju lokalne ID-eve:
   - conquest članak `epoksidni-podovi-ili-ecotile-podovi` (istorijski ID 2542 — isti kao live jer je baza bila uvezena sa live)
   - basket članak `kako-napraviti-teren-za-basket-...` (ID 2298)
   Posle importa upiši mapu staro→novo u dnevnik i ažuriraj pominjanja u
   `CLAUDE.md` (§1 pominje post ID 2542) i [[PROGRESS]] ako se ID promenio.

3. **Odluči sudbinu 7 lokalnih postova bez live parnjaka** (konsultuj F1
   `LOKAL-NOVO` statuse; podrazumevano po hibrid pravilu):
   | Lokalni post | Podrazumevano |
   |---|---|
   | `bergo-ultimate-i-ultimate-plus-...` | ZADRŽATI (namerno nov sadržaj) |
   | `izbor-industrijskog-poda-tri-najcesca-pitanja-2-2` | OBRISATI (treći duplikat; konsolidacija duplikata ide u F4) |
   | `kako-izabrati-pravi-industrijski-pod-...-poterbama` | prebaci u **draft** (odluka u F4 — ima typo u slugu "poterbama") |
   | `padel-tenis` | prebaci u **draft** (granični slučaj za F4: live page je `padel-tereni`) |
   | `podovi-za-garaze` | prebaci u **draft** (live parnjak je page `garaze-i-autoservisi` — F5 rebuild) |
   | `sportska-podloga-za-odbojku` | OBRISATI — live `podloga-za-odbojkaske-terene` (2.668 impr) ga zamenjuje pod live slugom |
   | `sportski-podovi-za-skole-i-sportske-sale` | prebaci u **draft** (granični slučaj za F4: live je `sportski-podovi-za-sale-i-balone`) |
   Draft = sadržaj sačuvan, URL oslobođen, odluka odložena — sigurnije od brisanja.

4. **Obriši preostale lokalne publish postove** (samo `post_type='post'`; pages i
   products se NE diraju): PHP petlja `wp_delete_post($id, true)`.

5. **Import WXR** — WordPress Importer:
   - Proveri da li je plugin instaliran: `wp-content/plugins/wordpress-importer/`.
     Ako nije: preuzmi sa `https://downloads.wordpress.org/plugin/wordpress-importer.latest-stable.zip`, raspakuj u plugins, aktiviraj kroz PHP (`activate_plugin`).
   - Pokreni import kroz PHP skriptu (klasa `WP_Import`, `fetch_attachments = true`) —
     179 slika se povlači sa live; može trajati 10–20 min.
   - Autor mapiranje: na postojećeg lokalnog admina.

6. **Anti-kanibalizacija re-primena** na svež basket članak (izmena od 2026-07-06
   koju reimport briše): skratiti sekciju o dimenzijama i dodati linkove ka
   `/dimenzije-kosarkaskog-terena/` i `/dimenzije-kosarkaske-table/`.
   Detalji izmene: [[DNEVNIK-NAPRETKA]] unos 2026-07-06 (C3 TIER1 #4/#5).

7. **Poznata odstupanja** (zabeleži, ne popravljaj sad — restyle sesije to rešavaju):
   - Neki live postovi imaju `<h1>` u samom sadržaju (npr. conquest 2542, post 2622) →
     sa WoodMart naslovom to daje 2×H1. Privremeno rešenje ako treba: `_woodmart_title_off=on`
     postmeta ILI h1→h2 u sadržaju; sistemski se rešava pri restyle-u.
   - Stilovi su live/Kallyas markup — restyle na WoodMart je poseban W1/W2 batch.

## Verifikacija

- [ ] `SELECT COUNT(*) FROM wpGs_posts WHERE post_type='post' AND post_status='publish'` = 30 + zadržani LOKAL-NOVO (bergo-ultimate post) = **31**
- [ ] Svih 30 live slugova postoji lokalno (uporedi sa post-sitemap.xml)
- [ ] 5 nasumičnih postova: 200, slike se učitavaju sa lokala (ne sa antasline.com!)
- [ ] Conquest članak: sadržaj identičan live verziji (spot-check naslova i prvog pasusa)
- [ ] Kategorije postova prenete (nisu sve "Uncategorized")
- [ ] Basket članak ima anti-kanibalizacija linkove (korak 6)
- [ ] Regression: Početna, `/industrijski-podovi/` → 200

## Zatvaranje sesije

1. [[DNEVNIK-NAPRETKA]] — unos NA VRH (`[W3 PARITY F3]`): broj uvezenih, ID mapa (2542→?, 2298→?), sudbina 7 lokalnih, backup fajl
2. Ažuriraj ID reference u `CLAUDE.md` i drugde ako su se promenile
3. [[PROGRESS]] — red u "Urađeno"; M8 zavisnost označiti rešenom i u [[2026-07-06-MASTER-PLAN-V2]] §4
4. Štikliraj F3 u [[migracija/promptovi/_README]]
