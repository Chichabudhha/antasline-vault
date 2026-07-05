---
tip: migracija
datum: 2026-07-05
namena: prompt za Claude Code na cPanel terminalu — export postova sa live za WoodMart rebuild
---

# Prompt za cPanel Claude Code — live export (READ-ONLY)

Kopiraj sve iz bloka ispod u Claude Code sesiju na cPanel terminalu:

```
Radiš na LIVE produkciji (antasline.com) — STRIKTNO READ-ONLY prema WordPress-u:
ništa ne menjaš u bazi, fajlovima, pluginovima ni podešavanjima. Jedino pišeš
nove fajlove u vault (~/antasline-vault) i commit/push.

KONTEKST: Na lokalnom buildu se gradi novi sajt (WoodMart tema). Proizvodi već
postoje lokalno; postovi se prenose sa live; pages se prave nove prema live
sadržaju. Tebi treba da izvezeš materijal.

ZADACI:

1. Napravi folder ~/antasline-vault/migracija/live-export-2026-07-05/

2. XML export postova (za import na lokal):
   wp export --dir=~/antasline-vault/migracija/live-export-2026-07-05/ \
     --post_type=post --post_status=publish --filename_format='live-posts-{date}.xml'
   (Ako wp-cli nije u PATH, probaj `php ~/wp-cli.phar` ili nađi binarnu putanju.
   Ako wp export ne radi, fallback: mysqldump samo wp posts+postmeta tabela
   postova — kredencijali su u wp-config.php, čitaj ih, ne menjaj.)

3. XML export pages (SAMO KAO REFERENCA za rebuild, neće se importovati):
   isti postupak, --post_type=page, filename live-pages-{date}.xml

4. CSV inventar za content parity — za SVE objavljene posts+pages, kolone:
   ID;post_type;slug;permalink;post_title;yoast_title;yoast_metadesc;word_count
   (yoast meta: _yoast_wpseo_title i _yoast_wpseo_metadesc iz postmeta;
   word_count = broj reči u post_content bez taga/shortcode-a)
   Snimi kao live-inventar-2026-07-05.csv (UTF-8, semicolon delimiter).

5. Kontrolni brojevi (u poruci i u dnevnik unos):
   - broj objavljenih postova (lokal staging ima 32 — javi razliku)
   - broj objavljenih pages
   - broj objavljenih proizvoda i kategorija (za poređenje sa lokalnih 37/10)

6. Vault: appenduj unos u DNEVNIK-NAPRETKA.md na vrh (tag [cpanel-live],
   datum 2026-07-05, šta je izvezeno + kontrolni brojevi), pa:
   git add -A && git commit -m "cpanel: live export za woodmart rebuild 2026-07-05" && git push

NE RADI: nikakve izmene na live sajtu, nikakav plugin install, nikakav
wp cache flush, ništa što piše u WP bazu.
```

## Posle (na lokalu, Claude Code u vault-u)
1. `git pull` u vault-u → stižu XML + CSV
2. Import postova: WP admin → Tools → Import → WordPress (uz "Download and import file attachments" — slike se vuku sa live URL-ova) ili `wp import`
3. CSV inventar = checklist za rebuild pages (title/meta parity po stranici)
