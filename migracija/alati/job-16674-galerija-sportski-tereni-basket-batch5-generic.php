<?php
// W1 BLOK E — Galerija sportskih terena (16674) — batch 5: generičke (bez imena
// lokacije) fotke iz reference/foto-arhiva-inventar.md reda čekanja — indoor
// teren u kancelariji (3 ugla) + krovni multisport teren (2 ugla). M potvrdio
// 2026-08-07 da su svi radovi AntasLine.
// Backup: antasline-backups/antasline_local_2026-08-07_pre-galerija-batch5-generic.sql

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
$src_dir = 'C:/projekti/antasline-vault/.scratch/foto-generic-check/';

$photos = [
    [
        'src' => $src_dir . 'teren u kancelariji2.jpg',
        'dest' => 'teren-u-kancelariji-1.jpg',
        'alt' => 'Indoor košarkaški teren u kancelarijskom prostoru',
        'title' => 'Teren u kancelariji',
    ],
    [
        'src' => $src_dir . 'teren u kancelariji.jpg',
        'dest' => 'teren-u-kancelariji-2.jpg',
        'alt' => 'Indoor teren u kancelarijskom prostoru, širi kadar sa lounge zonom',
        'title' => 'Teren u kancelariji',
    ],
    [
        'src' => $src_dir . 'teren u kancelariji3.jpg',
        'dest' => 'teren-u-kancelariji-3.jpg',
        'alt' => 'Indoor teren u kancelarijskom prostoru, pogled na ulaz',
        'title' => 'Teren u kancelariji',
    ],
    [
        'src' => $src_dir . 'teren na krovu.jpg',
        'dest' => 'krovni-multisport-teren-1.jpg',
        'alt' => 'Krovni multisport teren, košarka i mali fudbal/rukomet',
        'title' => 'Krovni teren',
    ],
    [
        'src' => $src_dir . 'teren na krovu2.jpg',
        'dest' => 'krovni-multisport-teren-2.jpg',
        'alt' => 'Krovni multisport teren, detalj koša i podloge',
        'title' => 'Krovni teren',
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
