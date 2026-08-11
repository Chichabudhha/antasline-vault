<?php
/**
 * Ergomat DuraStripe trake — uvoz slika po bojama + prelazak simple → variable.
 *
 * 16518  DuraStripe Xtreme Roll      — pune boje (6 slika)
 * 16520  DuraStripe Supreme V        — dvobojne hazard rolne (4 slike)
 *
 * Pokretanje:  C:\xampp\php\php.exe job-ergomat-trake-varijacije.php
 * Backup pre:  antasline-backups/antasline_local_2026-08-11_pre-ergomat-trake-varijacije.sql
 */

require dirname(__FILE__) . '/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

if (!function_exists('wc_get_product')) {
    exit("GRESKA: WooCommerce nije aktivan.\n");
}

$SRC = 'C:\\Miroslav\\Antas line\\Proizvodo\\Ergomat trake\\';

/* --------------------------------------------------------------------------
 * Mapa: proizvod → boje (slug => [ime termina, fajl slike ili null])
 * null = boja koju proizvođač nudi ali za nju nemamo fotografiju;
 *        varijacija se svejedno pravi (ne brišemo informaciju o ponudi),
 *        samo pada na glavnu sliku proizvoda.
 * ------------------------------------------------------------------------ */
$PLAN = [
    16518 => [
        'naziv'  => 'DuraStripe Xtreme',
        'opis'   => 'rolna trake za obeležavanje poda',
        'prefix' => 'durastripe-xtreme',
        'boje'   => [
            'crna'         => ['Crna',         'DuraStripe Xtreme Floor Tape - Crna.webp'],
            'crvena'       => ['Crvena',       'DuraStripe Xtreme Floor Tape - Crvena.webp'],
            'plava'        => ['Plava',        'DuraStripe Xtreme Floor Tape - Plava.webp'],
            'siva'         => ['Siva',         'DuraStripe Xtreme Floor Tape - Siva.webp'],
            'zelena'       => ['Zelena',       'DuraStripe Xtreme Floor Tape - Zelena.webp'],
            'zuta'         => ['Žuta',         'DuraStripe Xtreme Floor Tape - Zuta.webp'],
            'bela'         => ['Bela',         null],
            'narandzasta'  => ['Narandžasta',  null],
            'svetlo-plava' => ['Svetlo plava', null],
            'ljubicasta'   => ['Ljubičasta',   null],
            'braon'        => ['Braon',        null],
        ],
    ],
    16520 => [
        'naziv'  => 'DuraStripe Supreme V',
        'opis'   => 'rolna trake za obeležavanje poda',
        'prefix' => 'durastripe-supreme-v',
        'boje'   => [
            'crno-bela'   => ['Crno-bela',   'DuraStripe Supreme V - crno bela.webp'],
            'crno-zuta'   => ['Crno-žuta',   'DuraStripe Supreme V - crno zuta.webp'],
            'crveno-bela' => ['Crveno-bela', 'DuraStripe Supreme V - crveno bela.webp'],
            'zeleno-bela' => ['Zeleno-bela', 'DuraStripe Supreme V - zeleno bela.webp'],
            'crna'        => ['Crna',        null],
            'crvena'      => ['Crvena',      null],
            'plava'       => ['Plava',       null],
            'zelena'      => ['Zelena',      null],
            'zuta'        => ['Žuta',        null],
            'bela'        => ['Bela',        null],
            'narandzasta' => ['Narandžasta', null],
        ],
    ],
];

/* ---------------------------------------------------------------- helperi */

function term_za_boju($slug, $ime) {
    $t = get_term_by('slug', $slug, 'pa_boja');
    if ($t) return (int) $t->term_id;
    $novi = wp_insert_term($ime, 'pa_boja', ['slug' => $slug]);
    if (is_wp_error($novi)) {
        echo "  ! termin '$ime' ($slug): " . $novi->get_error_message() . "\n";
        return 0;
    }
    echo "  + nov pa_boja termin: $ime ($slug)\n";
    return (int) $novi['term_id'];
}

function uvezi_sliku($putanja, $naziv_fajla, $naslov, $alt) {
    // vec uvezena? (idempotentno ponovno pokretanje)
    $post = get_page_by_path(sanitize_title(pathinfo($naziv_fajla, PATHINFO_FILENAME)), OBJECT, 'attachment');
    if ($post) {
        echo "  = slika vec postoji: {$naziv_fajla} (#{$post->ID})\n";
        return (int) $post->ID;
    }
    if (!file_exists($putanja)) {
        echo "  ! nema fajla: $putanja\n";
        return 0;
    }
    $up   = wp_upload_dir();
    $dest = trailingslashit($up['path']) . $naziv_fajla;
    $dest = wp_unique_filename($up['path'], $naziv_fajla);
    $dest = trailingslashit($up['path']) . $dest;
    if (!copy($putanja, $dest)) {
        echo "  ! kopiranje nije uspelo: $naziv_fajla\n";
        return 0;
    }
    $att_id = wp_insert_attachment([
        'post_mime_type' => 'image/webp',
        'post_title'     => $naslov,
        'post_content'   => '',
        'post_status'    => 'inherit',
    ], $dest);
    if (!$att_id || is_wp_error($att_id)) {
        echo "  ! wp_insert_attachment pao za $naziv_fajla\n";
        return 0;
    }
    wp_update_attachment_metadata($att_id, wp_generate_attachment_metadata($att_id, $dest));
    update_post_meta($att_id, '_wp_attachment_image_alt', $alt);
    echo "  + slika #{$att_id}: {$naziv_fajla}\n";
    return (int) $att_id;
}

/* ------------------------------------------------------------------- rad */

foreach ($PLAN as $pid => $cfg) {
    $proizvod = wc_get_product($pid);
    if (!$proizvod) { echo "GRESKA: proizvod $pid ne postoji\n"; continue; }

    echo "\n=== #{$pid} {$cfg['naziv']} (trenutno: {$proizvod->get_type()}) ===\n";

    // 1) termini + slike
    $term_ids = [];
    $slike    = [];
    foreach ($cfg['boje'] as $slug => $par) {
        list($ime, $fajl) = $par;
        $tid = term_za_boju($slug, $ime);
        if (!$tid) continue;
        $term_ids[] = $tid;
        if ($fajl) {
            $naziv_fajla = $cfg['prefix'] . '-' . $slug . '.webp';
            $slike[$slug] = uvezi_sliku(
                $GLOBALS['SRC'] . $fajl,
                $naziv_fajla,
                $cfg['naziv'] . ' — ' . $ime,
                $cfg['naziv'] . ' ' . $cfg['opis'] . ' — ' . mb_strtolower($ime, 'UTF-8')
            );
        }
    }
    wp_set_object_terms($pid, $term_ids, 'pa_boja', false);
    echo "  · pa_boja dodeljeno: " . count($term_ids) . " boja\n";

    // 2) pa_boja postaje atribut varijacije (ostali atributi netaknuti)
    $attrs = get_post_meta($pid, '_product_attributes', true);
    if (!is_array($attrs)) $attrs = [];
    if (!isset($attrs['pa_boja'])) {
        $attrs['pa_boja'] = [
            'name' => 'pa_boja', 'value' => '', 'position' => count($attrs),
            'is_visible' => 1, 'is_variation' => 1, 'is_taxonomy' => 1,
        ];
    } else {
        $attrs['pa_boja']['is_variation'] = 1;
        $attrs['pa_boja']['is_visible']   = 1;
    }
    update_post_meta($pid, '_product_attributes', $attrs);

    // 3) simple → variable
    wp_set_object_terms($pid, 'variable', 'product_type', false);
    echo "  · tip proizvoda: variable\n";

    // 4) varijacije po boji
    $postojece = [];
    foreach (get_children(['post_parent' => $pid, 'post_type' => 'product_variation', 'numberposts' => -1]) as $v) {
        $b = get_post_meta($v->ID, 'attribute_pa_boja', true);
        if ($b) $postojece[$b] = (int) $v->ID;
    }

    $red = 0;
    foreach ($cfg['boje'] as $slug => $par) {
        list($ime, $fajl) = $par;
        if (isset($postojece[$slug])) {
            $var = new WC_Product_Variation($postojece[$slug]);
            $akcija = 'azurirana';
        } else {
            $var = new WC_Product_Variation();
            $var->set_parent_id($pid);
            $akcija = 'nova';
        }
        $var->set_attributes(['pa_boja' => $slug]);
        $var->set_menu_order($red++);
        $var->set_status('publish');
        if (!empty($slike[$slug])) {
            $var->set_image_id($slike[$slug]);
        }
        $vid = $var->save();
        echo "  · varijacija {$ime}: #{$vid} ({$akcija}" . (!empty($slike[$slug]) ? ', sa slikom' : ', bez slike') . ")\n";
    }

    // 5) sinhronizacija + lookup tabela (§8 gotcha)
    WC_Product_Variable::sync($pid);
    wc_delete_product_transients($pid);
    clean_post_cache($pid);

    try {
        $store = wc_get_container()->get(\Automattic\WooCommerce\Internal\ProductAttributesLookup\LookupDataStore::class);
        $store->create_data_for_product(wc_get_product($pid));
        echo "  · lookup tabela osvezena\n";
    } catch (\Throwable $e) {
        echo "  ! lookup tabela: " . $e->getMessage() . "\n";
    }
}

// layered nav brojaci
global $wpdb;
$wpdb->query("DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_wc_layered_nav_counts%'");
wc_delete_product_transients();

echo "\nGOTOVO.\n";
