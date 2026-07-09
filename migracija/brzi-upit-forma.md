---
tip: strategija-i-uputstvo
naziv: Brzi upit — dinamička kontakt forma na stranicama usluga
datum: 2026-07-09
status: implementirano na lokalnom buildu
---

# 📨 "Brzi upit" — jedna dinamička forma za sve stranice usluga

## Strategija (zašto ovako)

**Problem:** slanje posetioca sa stranice usluge na generičku `/kontakt/` stranicu = dodatni korak = izgubljene konverzije. Pravljenje 20 formi za 20 usluga = nepodrživo održavanje.

**Rešenje:** JEDNA kratka CF7 forma (**ID 16737, "Brzi upit"**) automatski ubačena na dno svake stranice usluge i blog posta jednim PHP hookom — bez editovanja ijedne stranice. Mejl adminu uvek javlja tačan izvor upita kroz CF7 **ugrađene special mail tagove** `[_post_title]` / `[_post_url]` — nula custom polja, nula JS-a za kontekst.

**Mehanika (verifikovano iz CF7 6.1.6 source koda):**
- CF7 pri renderu dodaje hidden polje `_wpcf7_container_post` = ID posta u kom je forma renderovana — ali **SAMO ako je render `in_the_loop()`** (`contact-form.php:719`)
- `[_post_title]`/`[_post_url]` u mail template-u se resolve-uju iz tog polja (`special-mail-tags.php:100-152`); fallback kad container ne postoji = prazan string (zato subject ima statičan prefiks "Brzi upit —")
- Zato hook ide na **`the_content` prio 12** (u loop-u; posle wpautop@10 i do_shortcode@11 → naš markup ne prolazi wpautop, shortcode renderujemo ručnim `do_shortcode()`). ⚠️ `wp_footer` NE radi — van loop-a, container = 0.

**Konverzioni model netaknut (BLOK A):** submit obe forme → JS `wpcf7mailsent` listener → redirect na `/hvala-za-poruku/` → GTM okida GA4 `generate_lead` na pageview-u. GA4 `page_referrer` na hvala stranici nosi URL izvorne stranice.

## Šta je implementirano (2026-07-09)

| Šta | Gde |
|---|---|
| CF7 forma "Brzi upit" (ID **16737**) | baza (`_form`/`_mail` postmeta, kreirano kroz `WPCF7_ContactForm` API) |
| `AL_QUICK_FORM_ID` konstanta + `the_content` prio 12 hook | `woodmart-child/functions.php` |
| Redirect listener proširen na `[16593, 16737]` | isti fajl, wp_footer blok |
| CTA scroll-to-#upit (progressive enhancement) | isti wp_footer JS blok |
| `.al-quick-quote` navy kartica + CF7 grid (`form-row`/`form-col-6` — do sada NISU bili stilizovani nigde) | `antas-design.css` |
| Skraćena forma 16593: ime+prezime+kompanija → jedno polje `form-ime-firma` "Ime i prezime / firma" | baza (`_form`/`_mail`) |
| Mail-log mu-plugin (SAMO lokal, XAMPP nema SMTP) | `wp-content/mu-plugins/al-local-mail-log.php` |

**Polja "Brzi upit" (runda 2, 2026-07-09):** Ime i prezime / firma* · Telefon* · **Email*** (obavezan — vidi auto-reply) · textarea "Opišite problem koji treba da se reši" (opciono). **Bez saglasnost checkbox-a** (M odluka).

**Sekcija je full-bleed (runda 3):** viewport breakout `width:100vw; margin-left:calc(50% - 50vw)` — pozadina od ivice do ivice ekrana, sadržaj forme na širini kontejnera (1192px). ⚠️ Na layoutima sa sidebar-om (blog postovi) kolona nije centrirana pa bi breakout bio iskošen — `body:has(.sidebar-container)` override tamo vraća formu kao karticu u koloni.

**Kontra boja + dijagonala (runda 4):** sekcija je SVETLA (`--al-mist`) sa `al-diag-top` dijagonalnim rezom — kontrast između navy CTA sekcija iznad i tamnog futera ispod (prethodna navy verzija se slivala u jedno plavo). Post kartica: bez dijagonale, gradient akcent traka. ⚠️ Gotcha: theme `:is(.entry-content...) > :where(:last-child)` reset gazi diag negativni margin-bottom — selektor sa 3 klase (`.al-section.al-diag-top.al-quick-quote`) ga vraća, inače bela traka pred futerom.

**Mail subject:** `[_site_title] Brzi upit — [_post_title]` · **body:** izvorna stranica (naslov + URL), ime/firma, telefon, email, problem · Reply-To: `[form-email]`.

**Auto-reply pošiljaocu (mail_2, obe forme):** subject `[_site_title] — primili smo Vaš upit`, body sa porukom "odgovorićemo u najkraćem mogućem roku" + prepisan upit + telefon 072. ⚠️ **Zbog mail_2 je email polje OBAVEZNO u obe forme** — CF7 ne šalje mail_2 uslovno; prazan `[form-email]` recipient bi oborio ceo submit u `mail_failed` (nema redirecta, nema konverzije).

## Ključna pravila održavanja

- **Nova stranica usluge automatski dobija formu** — nikakva akcija nije potrebna.
- **Exclusion lista** (slugovi bez forme) u `functions.php`: `kontakt, hvala-za-poruku, politika-kolacica, katalog, pocetna, aktuelnosti, galerija, o-nama` (+ filter `al_quick_form_excluded_slugs`). `/hvala-za-poruku/` NIKAD ne sme dobiti formu (petlja konverzija).
- **`/kontakt/` zadržava punu formu 16593** — tamo živi `form-naslov default:get` prefill sa proizvoda (`?form-naslov=Ponuda: X`). Ne dirati taj mehanizam.
- Izmene formi ISKLJUČIVO kroz `WPCF7_ContactForm::get_instance() → set_properties() → save()` (CF7 čita iz `_form`/`_mail` postmeta — upis u post_content ćutke pravi praznu formu).
- CF7 tag gramatika: opcije PRE quoted vrednosti, dvotačka sintaksa (`autocomplete:tel`); HTML-atribut sintaksa obara ceo tag.

## ⚠️ PRE MIGRACIJE NA PRODUKCIJU (dodati u 3.10 checklist)

1. **Obrisati `mu-plugins/al-local-mail-log.php`** — presreće SVE mejlove (loguje u `wp-content/mail-log.txt` i sprečava stvarno slanje). Na produkciji mejlovi moraju stvarno da idu.
2. Obrisati `wp-content/mail-log.txt` (test podaci).
3. Verifikovati SMTP/mail slanje na produkciji pa tek onda pustiti saobraćaj na forme.

## Scroll-to-#upit ponašanje

In-content linkovi koji se završavaju na `/kontakt/` (bez query stringa!) na stranicama koje imaju `#upit` skroluju na formu umesto navigacije. Namerno netaknuti: header/footer "Kontakt" meni (van `.wd-content-area`), product "Zatražite ponudu" (nosi `?form-naslov=`). Bez JS-a sve radi kao pre.

🔴 Gotcha: `scrollIntoView({behavior:'smooth'})` se NE animira u automatizovanom Chrome tabu (rAF ne radi u nefokusiranom tabu) — ista familija kao `loading="lazy"` nalaz iz F7 P4. Instant varijanta radi (target tačan), za pravog korisnika smooth radi normalno.

## Verifikacija izvedena 2026-07-09

- Container post tačan na 3 tipa: hub 16567, child 16687, blog post 2542 ✔
- Test submit ×3 (REST): mejl nosi tačan naslov+URL izvora, UTF-8 (ćšžđč) ispravan ✔
- Regresija 16593: submit radi, prefill `?form-naslov=` radi ✔
- Exclusion: kontakt/hvala/katalog/home bez forme ✔ · 1×H1 očuvan ✔
- Vizuelno (Chrome): navy kartica + gradient traka, personalizovan naslov ✔ · mobilni stack na 380px bez horizontalnog overflow-a ✔

## Veze
[[2026-07-06-MASTER-PLAN-V2]] W1 · [[migracija/woodmart-sabloni]] F7.9 · [[DNEVNIK-NAPRETKA]] 2026-07-09
