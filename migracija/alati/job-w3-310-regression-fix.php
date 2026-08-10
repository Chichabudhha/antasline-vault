<?php
/**
 * W3 3.10 — popravke nađene full regression sweep-om (2026-08-10).
 *
 * 1. Footer widget "Navigacija" (`widget_text`): `/spoljne-podne-obloge/` (bez j)
 *    → `/spoljnje-podne-obloge/`. Bio je 404 na SVIH 195 stranica. Isti tip
 *    bug-a koji je 2026-08-07 nađen na staging-u; lokalni build ga je zadržao
 *    posle 07-30 odluke da kanonski slug ostane `spoljnje-` (sa j).
 *
 * 2. Tri interna linka koja gađaju stari slug pa idu kroz 301 — prevezani na
 *    finalni cilj (svi provereni 200 pre izmene):
 *      /podovi-za-baste-splavove-bazene/bergo-unique/ → /proizvod/bergo-unique/
 *      /podovi-za-maloprodajne-objekte/  → /industrijski-podovi/podovi-za-maloprodajne-objekte/
 *      /sportska-podloga-za-odbojku/     → /podloga-za-odbojkaske-terene/
 *
 * Slike (5 slomljenih) NISU dirane ovde — rešene su preuzimanjem originala sa
 * live-a na tačne putanje u `wp-content/uploads`, bez ijedne izmene sadržaja.
 *
 * Backup pre pokretanja:
 *   antasline-backups/antasline_local_2026-08-10_pre-w3-310-regression-fix.sql
 */

require_once 'C:/xampp/htdocs/antasline/wp-load.php';

$dry = in_array('--dry-run', $argv, true);
echo $dry ? "== DRY RUN ==\n\n" : "== IZVRSAVANJE ==\n\n";

// ---------- 1. Footer widgeti ----------
// 🔴 DVA su widget-a nosila stari slug, ne jedan:
//    `widget_text`        — kolona "Navigacija"
//    `widget_custom_html` — kolona "Podovi", link "Terase i dom"
// Prvi prolaz je popravio samo `widget_text` i provera je i dalje nalazila
// 404 na svakoj stranici. Zato se ovde obrađuju SVE `widget_*` opcije.
$widget_opts = ['widget_text', 'widget_custom_html', 'widget_block', 'widget_nav_menu'];
foreach ($widget_opts as $name) {
    $opt = get_option($name);
    if (!is_array($opt)) { continue; }
    $hit = 0;
    array_walk_recursive($opt, function (&$v) use (&$hit) {
        if (is_string($v) && strpos($v, '/spoljne-podne-obloge/') !== false) {
            $v = str_replace('/spoljne-podne-obloge/', '/spoljnje-podne-obloge/', $v);
            $hit++;
        }
    });
    echo "1. {$name}: {$hit} polja sa starim slugom\n";
    if (!$dry && $hit > 0) {
        update_option($name, $opt);
        echo "   upisano\n";
    }
}

// ---------- 2. Interni 301 linkovi ----------
$map = [
    '/podovi-za-baste-splavove-bazene/bergo-unique/' => '/proizvod/bergo-unique/',
    '/podovi-za-maloprodajne-objekte/'               => '/industrijski-podovi/podovi-za-maloprodajne-objekte/',
    '/sportska-podloga-za-odbojku/'                  => '/podloga-za-odbojkaske-terene/',
];
$ids = [2699, 5438, 16142, 17026];

echo "\n2. Interni linkovi:\n";
foreach ($ids as $id) {
    $p = get_post($id);
    if (!$p) { echo "   !! post {$id} ne postoji\n"; continue; }
    $c = $p->post_content;
    $n = 0;
    foreach ($map as $from => $to) {
        // već ispravni ugnježdeni URL se ne sme ponovo prepisati
        if ($from === '/podovi-za-maloprodajne-objekte/') {
            $c = preg_replace(
                '#(?<!/industrijski-podovi)/podovi-za-maloprodajne-objekte/#',
                $to, $c, -1, $k
            );
        } else {
            $c = str_replace($from, $to, $c, $k);
        }
        $n += $k;
    }
    echo "   {$id} ({$p->post_name}): {$n} zamena\n";
    if (!$dry && $n > 0) {
        $r = wp_update_post(['ID' => $id, 'post_content' => $c], true);
        echo is_wp_error($r) ? "     GRESKA: " . $r->get_error_message() . "\n" : "     upisano\n";
    }
}

echo "\nGotovo.\n";
