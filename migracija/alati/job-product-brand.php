<?php
/**
 * W3 / PROGRESS bloker — dodela product_brand termina proizvodima.
 * Ecotile (135) i Ergomat (73) arhive su prazne, a .htaccess 301 draft vodi na njih.
 * Pokretanje: php job-product-brand.php [--confirm]
 */
require_once 'C:/xampp/htdocs/antasline/wp-load.php';

$confirm = in_array('--confirm', $argv, true);

$ecotile = [16538, 16540, 16542, 16930, 16939, 16943, 16949];
$ergomat = [16476, 16478, 16480, 16482, 16484, 16486, 16488, 16490, 16492, 16494,
            16496, 16498, 16500, 16502, 16504, 16506, 16508, 16510, 16512, 16514,
            16516, 16518, 16520, 16522, 16524, 16526, 16528];

echo "taxonomy registrovana: " . (taxonomy_exists('product_brand') ? 'DA' : 'NE') . "\n";
$tax = get_taxonomy('product_brand');
if ($tax) {
    echo "  public=" . var_export($tax->public, true)
       . " rewrite=" . (is_array($tax->rewrite) ? $tax->rewrite['slug'] : var_export($tax->rewrite, true))
       . " object_type=" . implode(',', (array) $tax->object_type) . "\n";
}

$map = ['ecotile' => $ecotile, 'ergomat' => $ergomat];
foreach ($map as $slug => $ids) {
    $term = get_term_by('slug', $slug, 'product_brand');
    if (!$term) { echo "GRESKA: nema termina $slug\n"; continue; }
    foreach ($ids as $id) {
        $p = get_post($id);
        if (!$p || $p->post_type !== 'product' || $p->post_status !== 'publish') {
            echo "  PRESKOK $id (nije objavljen proizvod)\n";
            continue;
        }
        $has = wp_get_object_terms($id, 'product_brand', ['fields' => 'slugs']);
        if (in_array($slug, $has, true)) { echo "  = $id vec ima $slug\n"; continue; }
        if ($confirm) {
            $r = wp_set_object_terms($id, [$term->term_id], 'product_brand', true);
            echo (is_wp_error($r) ? "  GRESKA $id: " . $r->get_error_message() : "  + $id -> $slug") . "\n";
        } else {
            echo "  [dry] + $id -> $slug ({$p->post_title})\n";
        }
    }
}

// Porto-era ostatak: 7 priloga nose 'ergomat' i naduvavaju brojac na 25.
$att = get_objects_in_term([73], 'product_brand');
foreach ((array) $att as $oid) {
    $p = get_post($oid);
    if ($p && $p->post_type === 'attachment') {
        if ($confirm) {
            wp_remove_object_terms($oid, 73, 'product_brand');
            echo "  - prilog $oid skinut sa ergomat\n";
        } else {
            echo "  [dry] - prilog $oid ({$p->post_title}) skinuti sa ergomat\n";
        }
    }
}

if ($confirm) {
    wp_update_term_count_now([73, 135], 'product_brand');
    clean_taxonomy_cache('product_brand');
}

foreach (['ecotile', 'ergomat'] as $slug) {
    $t = get_term_by('slug', $slug, 'product_brand');
    echo "REZULTAT $slug: count={$t->count} url=" . get_term_link($t) . "\n";
}
