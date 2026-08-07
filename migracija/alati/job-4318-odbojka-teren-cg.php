<?php
// W1 BLOK E — Odbojkaški teren (4318) — dopuna sa 1 novom referentnom
// fotografijom (teren za odbojku, Crna Gora, dron snimak).
// Backup: antasline-backups/antasline_local_2026-08-07_pre-galerija-batch2-odbojka.sql

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

$post_id = 4318;
$src_dir = 'C:/Users/Miroslav/AppData/Local/Temp/claude/C--projekti-antasline-vault/02be589f-94c8-4880-a93a-59c893ac2d63/scratchpad/basket-batch2/';

$url = al_import_photo(
    $src_dir . 'Teren za odbojku CG.jpg',
    'teren-za-odbojku-crna-gora.jpg',
    'Teren za odbojku, snimak iz vazduha, Crna Gora',
    $post_id
);

$content = $wpdb->get_var($wpdb->prepare("SELECT post_content FROM {$wpdb->posts} WHERE ID = %d", $post_id));
if ($content === null) { fwrite(STDERR, "Post $post_id not found\n"); exit(1); }

// Umeće se posle poslednje postojeće fotke (TERENI-ODBOJKA-ADA-1), pre FAQ naslova
$marker = '<h2>Često postavljana pitanja o odbojkaškom terenu</h2>';
$pos = strpos($content, $marker);
if ($pos === false) { fwrite(STDERR, "MARKER NOT FOUND na 4318 — abort\n"); exit(1); }

$figure = '<img class="alignnone size-full" src="' . $url . '" alt="Teren za odbojku, snimak iz vazduha, Crna Gora" width="1080" height="608" />'
    . '<p style="font-size:14px;opacity:0.72;margin-top:8px">Teren za odbojku, snimak iz vazduha, Crna Gora</p>';

$content = substr($content, 0, $pos) . $figure . substr($content, $pos);
$updated = $wpdb->update($wpdb->posts, ['post_content' => $content], ['ID' => $post_id]);
clean_post_cache($post_id);
echo $updated !== false ? "OK — post $post_id azuriran\n" : "UPDATE FAILED 4318\n";
