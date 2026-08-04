<?php
// Usage: php import-gemini-photo.php <product_id> <source_webp_path> <dest_filename>
// Imports a Gemini-enhanced image as a proper WP attachment (with generated
// intermediate sizes) and sets it as the product's _thumbnail_id.
// Testirano i potvrđeno 2026-08-05 (Ecotile E500/7 #16538, Bergo Ultimate #16770).

define('WP_USE_THEMES', false);
require 'C:/xampp/htdocs/antasline/wp-load.php';
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$product_id = (int) $argv[1];
$src = $argv[2];
$dest_filename = $argv[3];

if (!file_exists($src)) {
    fwrite(STDERR, "Source not found: $src\n");
    exit(1);
}

$upload_dir = wp_upload_dir();
$dest_path = $upload_dir['path'] . '/' . $dest_filename;
if (!copy($src, $dest_path)) {
    fwrite(STDERR, "Copy failed\n");
    exit(1);
}

$filetype = wp_check_filetype($dest_filename, null);
$attachment = [
    'post_mime_type' => $filetype['type'],
    'post_title' => sanitize_file_name(pathinfo($dest_filename, PATHINFO_FILENAME)),
    'post_content' => '',
    'post_status' => 'inherit',
];
$attach_id = wp_insert_attachment($attachment, $dest_path, $product_id);
if (is_wp_error($attach_id)) {
    fwrite(STDERR, "wp_insert_attachment failed: " . $attach_id->get_error_message() . "\n");
    exit(1);
}

$attach_data = wp_generate_attachment_metadata($attach_id, $dest_path);
wp_update_attachment_metadata($attach_id, $attach_data);

$old_thumb_id = get_post_thumbnail_id($product_id);
set_post_thumbnail($product_id, $attach_id);

echo "OK product=$product_id new_attach=$attach_id old_thumb=$old_thumb_id url=" . wp_get_attachment_url($attach_id) . "\n";
