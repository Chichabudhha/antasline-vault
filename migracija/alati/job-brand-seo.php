<?php
/**
 * Nastavak: Rank Math title/description + term opis za dve brend arhive,
 * pa uključivanje brend sitemap-a (arhive vise nisu prazne).
 */
require_once 'C:/xampp/htdocs/antasline/wp-load.php';
$confirm = in_array('--confirm', $argv, true);

$data = [
    135 => [
        'title' => 'Ecotile industrijski podovi — ploče i rampe | Antas Line',
        'desc'  => 'Ecotile PVC interlocking ploče i rampe: E500/7, E500/10 za viljuškare i 7 mm ESD varijanta. Polažu se bez lepka, preko postojećeg poda. Upit: 069 234 00 72.',
        'term_desc' => '<p>Ecotile su PVC interlocking podne ploče koje se polažu bez lepka, direktno preko postojećeg betona ili starog poda. U ponudi su industrijska ploča E500/7, ultra heavy duty E500/10 za kretanje kamiona i viljuškara, ESD varijanta od 7 mm za antistatik prostore, kao i pripadajuće T-Joint i X-Joint rampe.</p>',
    ],
    73 => [
        'title' => 'Ergomat odbojnici i DuraStripe trake | Antas Line',
        'desc'  => 'Ergomat zaštitni odbojnici za stubove, ivice i uglove, DuraStripe trake za podno obeležavanje i industrijski senzori upozorenja. Upit: 069 234 00 72.',
        'term_desc' => '<p>Ergomat program obuhvata zaštitne odbojnike za stubove, I-grede, ivice i uglove u halama i magacinima, DuraStripe trake za podno obeležavanje (Xtreme, Supreme V, Mean Lean, Cold Storage) i industrijske senzore sa svetlosnim upozorenjem za pešačke zone.</p>',
    ],
];

foreach ($data as $tid => $d) {
    $t = get_term($tid, 'product_brand');
    if (!$t || is_wp_error($t)) { echo "GRESKA: nema termina $tid\n"; continue; }
    echo "{$t->name} (#{$tid})\n  title[" . mb_strlen($d['title']) . "] {$d['title']}\n  desc[" . mb_strlen($d['desc']) . "] {$d['desc']}\n";
    if ($confirm) {
        update_term_meta($tid, 'rank_math_title', $d['title']);
        update_term_meta($tid, 'rank_math_description', $d['desc']);
        wp_update_term($tid, 'product_brand', ['description' => $d['term_desc']]);
        echo "  upisano\n";
    }
}

// Brend sitemap: arhive vise nisu prazne -> ukljuciti
$s = get_option('rank-math-options-sitemap', []);
echo "\ntax_product_brand_sitemap (pre): " . var_export($s['tax_product_brand_sitemap'] ?? null, true) . "\n";
if ($confirm) {
    $s['tax_product_brand_sitemap'] = 'on';
    update_option('rank-math-options-sitemap', $s);
    delete_option('rank_math_sitemap_cache_files');
    global $wpdb;
    $wpdb->query("DELETE FROM {$wpdb->prefix}rank_math_sitemap_cache");
    echo "tax_product_brand_sitemap (posle): on (sitemap kes obrisan)\n";
}
