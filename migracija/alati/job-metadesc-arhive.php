<?php
/**
 * job-metadesc-arhive.php — Rank Math meta description za 13 taksonomijskih arhiva
 * koje su 11–12.08 ušle u sitemap bez opisa (nalaz regression sweep-a 2026-08-13).
 *
 * Obim: 6 blog kategorija + 6 product_cat + 1 product_brand.
 * NAMERNO IZOSTAVLJENO: 18 `product_tag` arhiva — prored im je zakazan posle live-a
 * (pre-migration checklist §B7), nema smisla pisati opise za nešto što se gasi.
 *
 * Svaki opis je pisan iz STVARNOG sadržaja termina (naslovi postova/proizvoda
 * izlistani iz baze pre pisanja) — bez izmišljenih modela, cena i tvrdnji.
 *
 * UPOTREBA:
 *   php job-metadesc-arhive.php          (probni prolaz, ništa ne upisuje)
 *   php job-metadesc-arhive.php --write  (upis)
 *
 * Backup pre upisa: antasline_local_2026-08-13_pre-metadesc-arhive.sql
 */

require_once 'C:/xampp/htdocs/antasline/wp-load.php';

$WRITE = in_array('--write', $argv, true);

$opisi = [
    // --- blog kategorije (category) ---
    ['category', 'industrijski-podovi',
     'Saveti i primeri iz prakse za industrijske podove: ESD i antistatik podovi, PVC ploče preko starog poda, izbor materijala i ugradnja.'],
    ['category', 'sportski-tereni',
     'Podloge i dimenzije sportskih terena — fudbal, tenis, padel, piklbol, odbojka i košarka, uz primere izvedenih radova.'],
    ['category', 'kosarkaski-tereni',
     'Kako se gradi košarkaški teren: dimenzije i podloga, modularne Bergo ploče i primer izvedenog 3x3 terena u TC Galerija.'],
    ['category', 'teniski-teren',
     'Podloge za teniske, padel i piklbol terene — izbor završnog sloja, dimenzije terena i priprema podloge.'],
    ['category', 'garazni-podovi',
     'Podovi za garaže, servise i detailing radionice: PVC ploče preko betona, poređenje sa gumenim podovima i montaža bez lepka.'],
    ['category', 'pod-za-prodavnice-i-radnje',
     'Podne obloge za prodavnice i maloprodajne prostore: R-Tile vinil pločice umesto klasičnih pločica, polaganje preko postojećeg poda.'],

    // --- kategorije proizvoda (product_cat) ---
    ['product_cat', 'sportske-podloge',
     'Bergo Ultimate, Ultimate PLUS i FLOW modularne sportske ploče za terene i dvorane. Pošaljite upit za cenu po m².'],
    ['product_cat', 'brodske-palube',
     'Bergo Excellence i Extreme IMO ploče za brodske palube — klik montaža bez lepka, IMO sertifikat. Pošaljite upit za cenu.'],
    ['product_cat', 'vestacka-trava',
     'Radici i Condor veštačka trava za fudbal, tenis, padel, hokej, golf i pejzažne površine, uz shock-pad podlogu. Pošaljite upit za cenu.'],
    ['product_cat', 'parking-i-travne-resetke',
     'Geoplast travne rešetke Runfloor, Geocross, Geoflor, Geogravel i Salvaverde za zeleni i šljunčani parking. Pošaljite upit za cenu.'],
    ['product_cat', 'lvt-podovi',
     'EXPONA LVT vinil pločice, rigidne klik daske i rolne, uz R-Tile vinil pločice za komercijalne prostore. Pošaljite upit za cenu po m².'],
    ['product_cat', 'rampe-i-zavrsni-profili',
     'Ecotile T-Joint i X-Joint rampe, ugaoni elementi i SureGrip protivklizni stepenišni profil. Pošaljite upit za cenu.'],

    // --- brend (product_brand) ---
    ['product_brand', 'bergo',
     'Bergo Flooring modularne ploče: Ultimate sportske podloge, XL i Elite za terase, Nova za javne prostore, Excellence za brodske palube.'],
];

printf("%s\n\n", $WRITE ? '=== UPIS ===' : '=== PROBNI PROLAZ (bez upisa) ===');

$ok = 0; $skip = 0; $err = 0;
foreach ($opisi as [$tax, $slug, $desc]) {
    $term = get_term_by('slug', $slug, $tax);
    if (!$term || is_wp_error($term)) {
        printf("  ❌ %-14s %-28s termin ne postoji\n", $tax, $slug);
        $err++;
        continue;
    }

    $len      = mb_strlen($desc);
    $postojeci = get_term_meta($term->term_id, 'rank_math_description', true);

    if ($postojeci !== '') {
        printf("  ⏭  %-14s %-28s već ima opis, preskačem\n", $tax, $slug);
        $skip++;
        continue;
    }
    if ($len > 160) {
        printf("  ⚠️  %-14s %-28s %d znakova (>160) — NE upisujem\n", $tax, $slug, $len);
        $err++;
        continue;
    }

    if ($WRITE) {
        update_term_meta($term->term_id, 'rank_math_description', $desc);
        $provera = get_term_meta($term->term_id, 'rank_math_description', true);
        if ($provera !== $desc) {
            printf("  ❌ %-14s %-28s upis nije prošao\n", $tax, $slug);
            $err++;
            continue;
        }
    }
    printf("  ✅ %-14s %-28s %3d zn. · %d stavki\n", $tax, $slug, $len, $term->count);
    $ok++;
}

printf("\nUkupno: %d ok · %d preskočeno · %d grešaka\n", $ok, $skip, $err);
if (!$WRITE) { echo "\nNišta nije upisano. Pokreni sa --write za upis.\n"; }
