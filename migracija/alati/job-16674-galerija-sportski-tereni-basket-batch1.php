<?php
// W1 BLOK E — Galerija sportskih terena (16674, live "Galerija - sportski
// tereni" stranica) — prvi batch dopune "Tereni za basket" grida sa 6 novih,
// ranije nekorišćenih terena iz Downloads arhiva (foto-arhiva-inventar.md).
// Backup: antasline-backups/antasline_local_2026-08-07_pre-galerija-sportski-tereni-basket-batch1.sql

define('WP_USE_THEMES', false);
require 'C:/xampp/htdocs/antasline/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

global $wpdb;

function al_import_photo($src, $dest_filename, $alt, $post_id) {
    if (!file_exists($src)) {
        fwrite(STDERR, "Source not found: $src\n");
        exit(1);
    }
    $upload_dir = wp_upload_dir();
    $dest_path = $upload_dir['path'] . '/' . $dest_filename;
    if (!copy($src, $dest_path)) {
        fwrite(STDERR, "Copy failed: $src\n");
        exit(1);
    }
    $filetype = wp_check_filetype($dest_filename, null);
    $attachment = [
        'post_mime_type' => $filetype['type'],
        'post_title' => sanitize_file_name(pathinfo($dest_filename, PATHINFO_FILENAME)),
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
    update_post_meta($attach_id, '_wp_attachment_image_alt', $alt);
    $url = wp_get_attachment_url($attach_id);
    echo "Attachment #$attach_id -> $url\n";
    return $url;
}

$post_id = 16674;
$src_dir = 'C:/Users/Miroslav/AppData/Local/Temp/claude/C--projekti-antasline-vault/02be589f-94c8-4880-a93a-59c893ac2d63/scratchpad/basket-batch1/';

$photos = [
    [
        'src' => $src_dir . 'Bergo multisport - Despotovac.jpg',
        'dest' => 'bergo-multisport-teren-despotovac.jpg',
        'alt' => 'Bergo multisport teren, Despotovac',
        'title' => 'Despotovac',
    ],
    [
        'src' => $src_dir . 'Bergo multisport - Valjevo.jpg',
        'dest' => 'bergo-multisport-teren-valjevo.jpg',
        'alt' => 'Bergo multisport teren, Valjevo',
        'title' => 'Valjevo',
    ],
    [
        'src' => $src_dir . 'Bergo multisport - Bezdan.jpg',
        'dest' => 'bergo-multisport-teren-bezdan.jpg',
        'alt' => 'Bergo multisport teren, Bezdan',
        'title' => 'Bezdan',
    ],
    [
        'src' => $src_dir . 'Teren za basket Krk.jpg',
        'dest' => 'teren-za-basket-krk.jpg',
        'alt' => 'Teren za basket, ostrvo Krk',
        'title' => 'Krk',
    ],
    [
        'src' => $src_dir . 'Teren 3x3 Sremcica.jpg',
        'dest' => 'teren-3x3-sremcica.jpg',
        'alt' => '3x3 košarkaški teren, Sremčica',
        'title' => 'Sremčica',
    ],
    [
        'src' => $src_dir . 'teren fruska gora.jpg',
        'dest' => 'teren-za-basket-fruska-gora.jpg',
        'alt' => 'Teren za basket, Fruška gora',
        'title' => 'Fruška gora',
    ],
];

$cards = '';
foreach ($photos as $p) {
    $url = al_import_photo($p['src'], $p['dest'], $p['alt'], $post_id);
    $cards .= '<div class="al-card"><span class="al-card__media"><img src="' . $url . '" alt="' . $p['alt'] . '" /></span><span class="al-card__title">' . $p['title'] . '</span></div>';
}

$content = $wpdb->get_var($wpdb->prepare("SELECT post_content FROM {$wpdb->posts} WHERE ID = %d", $post_id));
if ($content === null) { fwrite(STDERR, "Post $post_id not found\n"); exit(1); }

// Umeće se pre zatvaranja grida "Tereni za basket" (pre paragrafa "Pogledajte i ...")
$marker = '</div><p style="margin-top:24px">Pogledajte i <a href="http://localhost/antasline/sportske-podloge/kosarkaske-konstrukcije/">košarkaške konstrukcije</a>';
$pos = strpos($content, $marker);
if ($pos === false) { fwrite(STDERR, "MARKER NOT FOUND na 16674 — abort\n"); exit(1); }

$content = substr($content, 0, $pos) . $cards . substr($content, $pos);
$updated = $wpdb->update($wpdb->posts, ['post_content' => $content], ['ID' => $post_id]);
clean_post_cache($post_id);
echo $updated !== false ? "OK — post $post_id azuriran\n" : "UPDATE FAILED 16674\n";
