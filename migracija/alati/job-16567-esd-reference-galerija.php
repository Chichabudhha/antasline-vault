<?php
// W1 BLOK E — ubacuje prave referentne fotografije (HTEC Niš, Šimanovci, generički
// proizvodni pogon) u postojeću "Reference" sekciju na /industrijski-podovi/ (16567),
// zamenjujući stari 2018 stock kadar i dopunjujući grid sa 3 na 5 kartica.
// Backup: antasline-backups/antasline_local_2026-08-07_pre-esd-reference-galerija.sql

define('WP_USE_THEMES', false);
require 'C:/xampp/htdocs/antasline/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

global $wpdb;

$post_id = 16567;

$imports = [
    [
        'src' => 'C:/Users/Miroslav/Downloads/Antistatik-pod-HTEC-Nis.jpg',
        'dest' => 'antistatik-pod-htec-nis.jpg',
        'alt' => 'Antistatik ESD pod ugrađen u HTEC Niš',
    ],
    [
        'src' => 'C:/Users/Miroslav/Downloads/PR-DC-Simanovci-ESD-pod.jpg',
        'dest' => 'esd-pod-distributivni-centar-simanovci.jpg',
        'alt' => 'ESD pod u distributivnom centru, Šimanovci',
    ],
    [
        'src' => 'C:/Users/Miroslav/Downloads/industrijski podovi postavljeni u proizvodnji ecotile.webp',
        'dest' => 'ecotile-industrijski-pod-proizvodna-hala.webp',
        'alt' => 'Ecotile industrijski pod u proizvodnoj hali',
    ],
];

$upload_dir = wp_upload_dir();
$urls = [];

foreach ($imports as $i => $item) {
    if (!file_exists($item['src'])) {
        fwrite(STDERR, "Source not found: {$item['src']}\n");
        exit(1);
    }
    $dest_path = $upload_dir['path'] . '/' . $item['dest'];
    if (!copy($item['src'], $dest_path)) {
        fwrite(STDERR, "Copy failed: {$item['src']}\n");
        exit(1);
    }
    $filetype = wp_check_filetype($item['dest'], null);
    $attachment = [
        'post_mime_type' => $filetype['type'],
        'post_title' => sanitize_file_name(pathinfo($item['dest'], PATHINFO_FILENAME)),
        'post_content' => '',
        'post_status' => 'inherit',
    ];
    $attach_id = wp_insert_attachment($attachment, $dest_path, $post_id);
    if (is_wp_error($attach_id)) {
        fwrite(STDERR, "wp_insert_attachment failed: " . $attach_id->get_error_message() . "\n");
        exit(1);
    }
    $attach_data = wp_generate_attachment_metadata($attach_id, $dest_path);
    wp_update_attachment_metadata($attach_id, $attach_data);
    update_post_meta($attach_id, '_wp_attachment_image_alt', $item['alt']);

    $urls[$i] = wp_get_attachment_url($attach_id);
    echo "Attachment #$attach_id -> {$urls[$i]}\n";
}

// --- Update post_content: replace old stock card + append 2 new cards ---
$content = $wpdb->get_var($wpdb->prepare("SELECT post_content FROM {$wpdb->posts} WHERE ID = %d", $post_id));
if ($content === null) {
    fwrite(STDERR, "Post $post_id not found\n");
    exit(1);
}

// 1) Zameni stari 2018 stock kadar (3. kartica) novim proizvodnim halom
$old_img = 'src="http://localhost/antasline/wp-content/uploads/2018/10/ecotile-floor-1.jpg" alt="Ecotile industrijski pod u proizvodnom pogonu"';
$new_img = 'src="' . $urls[2] . '" alt="' . esc_attr($imports[2]['alt']) . '"';
if (strpos($content, $old_img) === false) {
    fwrite(STDERR, "MARKER NOT FOUND (old stock img) — abort, nista nije upisano\n");
    exit(1);
}
$content = str_replace($old_img, $new_img, $content);

// 2) Ubaci 2 nove kartice (HTEC, Simanovci) posle "Proizvodni pogoni — Ecotile</span></div>",
//    pre zatvaranja .al-grid diva
$marker = 'Proizvodni pogoni';
$marker_pos = mb_strpos($content, $marker, 0, 'UTF-8');
if ($marker_pos === false) {
    fwrite(STDERR, "MARKER NOT FOUND (Proizvodni pogoni) — abort, nista nije upisano\n");
    exit(1);
}
// prvi "</div></div>" posle markera zatvara .al-card pa .al-grid
$close_pos = strpos($content, '</div></div>', $marker_pos);
if ($close_pos === false) {
    fwrite(STDERR, "MARKER NOT FOUND (closing div par) — abort, nista nije upisano\n");
    exit(1);
}
$split_at = $close_pos + strlen('</div>'); // posle prvog </div> (zatvara .al-card), pre drugog (.al-grid)

$new_cards = '<div class="al-card"><span class="al-card__media"><img src="' . $urls[0] . '" alt="' . esc_attr($imports[0]['alt']) . '" /></span><span class="al-card__title">HTEC Niš &#8212; antistatik pod</span></div>'
    . '<div class="al-card"><span class="al-card__media"><img src="' . $urls[1] . '" alt="' . esc_attr($imports[1]['alt']) . '" /></span><span class="al-card__title">Šimanovci &#8212; ESD distributivni centar</span></div>';

$content = substr($content, 0, $split_at) . $new_cards . substr($content, $split_at);

$updated = $wpdb->update($wpdb->posts, ['post_content' => $content], ['ID' => $post_id]);
clean_post_cache($post_id);

echo $updated !== false ? "OK — post $post_id azuriran\n" : "UPDATE FAILED\n";
