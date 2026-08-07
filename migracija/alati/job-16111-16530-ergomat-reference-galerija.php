<?php
// W1 BLOK E — Ergomat reference galerije: Isotrack (16111, nova "Reference" sekcija
// pre CTA-a) + Mosolut Heavy (16530, jedna referentna fotografija pre cene).
// Geoplast (16589) je vec pokriven iz ranije sesije (9 fotki, FAQPage), ne dira se.
// Backup: antasline-backups/antasline_local_2026-08-07_pre-ergomat-reference-galerija.sql

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

// ================= 16111 — Isotrack =================
$post_id = 16111;

$isotrack_url1 = al_import_photo(
    'C:/Users/Miroslav/Downloads/Isotrack L.webp',
    'isotrack-l-privremena-podloga-dogadjaj.webp',
    'Isotrack L privremena podloga postavljena na događaju, dizalice na podlozi',
    $post_id
);
$isotrack_url2 = al_import_photo(
    'C:/Users/Miroslav/Downloads/isotrack X.webp',
    'isotrack-x-teski-kamion-pescana-podloga.webp',
    'Isotrack X ploče kao privremeni put za teški kamion na peščanom terenu',
    $post_id
);
$isotrack_url3 = al_import_photo(
    'C:/Users/Miroslav/Downloads/ISOTRACK-X.jpg',
    'isotrack-x-gradiliste-blatnjav-teren.jpg',
    'Isotrack X podloga kao privremeni put za dizalicu na blatnjavom terenu',
    $post_id
);

$content = $wpdb->get_var($wpdb->prepare("SELECT post_content FROM {$wpdb->posts} WHERE ID = %d", $post_id));
if ($content === null) { fwrite(STDERR, "Post $post_id not found\n"); exit(1); }

$marker = '[vc_row full_width="stretch_row" el_class="al-section al-section--navy al-plates al-diag-top--rev"][vc_column][vc_column_text]<div class="al-hero"><span class="al-label">Kontakt</span><h2 class="al-display--lg">Potrebna vam je privremena podloga za teren?';
$pos = strpos($content, $marker);
if ($pos === false) { fwrite(STDERR, "MARKER NOT FOUND na 16111 — abort\n"); exit(1); }

$gallery_section = '[vc_row full_width="stretch_row" el_class="al-section al-section--paper"][vc_column][vc_column_text]'
    . '<span class="al-label">Reference</span><h2 class="al-display--lg">Isotrack u primeni</h2>'
    . '<div class="al-grid al-grid--3" style="margin-top: 32px">'
    . '<div class="al-card"><span class="al-card__media"><img src="' . $isotrack_url1 . '" alt="Isotrack L privremena podloga postavljena na događaju, dizalice na podlozi" /></span><span class="al-card__title">Isotrack L &#8212; privremena podloga na događaju</span></div>'
    . '<div class="al-card"><span class="al-card__media"><img src="' . $isotrack_url2 . '" alt="Isotrack X ploče kao privremeni put za teški kamion na peščanom terenu" /></span><span class="al-card__title">Isotrack X &#8212; peščani teren</span></div>'
    . '<div class="al-card"><span class="al-card__media"><img src="' . $isotrack_url3 . '" alt="Isotrack X podloga kao privremeni put za dizalicu na blatnjavom terenu" /></span><span class="al-card__title">Isotrack X &#8212; gradilište, blatnjav teren</span></div>'
    . '</div>[/vc_column_text][/vc_column][/vc_row]';

$content = substr($content, 0, $pos) . $gallery_section . substr($content, $pos);
$updated = $wpdb->update($wpdb->posts, ['post_content' => $content], ['ID' => $post_id]);
clean_post_cache($post_id);
echo $updated !== false ? "OK — post $post_id azuriran\n" : "UPDATE FAILED 16111\n";

// ================= 16530 — Mosolut Heavy =================
$post_id2 = 16530;

$mosolut_url = al_import_photo(
    'C:/Users/Miroslav/Downloads/mosolut heavy 123.webp',
    'mosolut-heavy-pod-skladiste.webp',
    'Mosolut Heavy ploče postavljene u skladištu, ručna paletarka na podu',
    $post_id2
);

$content2 = $wpdb->get_var($wpdb->prepare("SELECT post_content FROM {$wpdb->posts} WHERE ID = %d", $post_id2));
if ($content2 === null) { fwrite(STDERR, "Post $post_id2 not found\n"); exit(1); }

$marker2 = '<p><strong>Cena na upit.</strong>';
$pos2 = strpos($content2, $marker2);
if ($pos2 === false) { fwrite(STDERR, "MARKER NOT FOUND na 16530 — abort\n"); exit(1); }

$ref_block = '<h2>Iz prakse</h2><figure style="max-width:480px;margin:0 0 24px"><img src="' . $mosolut_url . '" alt="Mosolut Heavy ploče postavljene u skladištu, ručna paletarka na podu" /><figcaption style="font-size:14px;color:#6b7280;margin-top:8px">Mosolut Heavy ploče u skladišnom prostoru</figcaption></figure>';

$content2 = substr($content2, 0, $pos2) . $ref_block . substr($content2, $pos2);
$updated2 = $wpdb->update($wpdb->posts, ['post_content' => $content2], ['ID' => $post_id2]);
clean_post_cache($post_id2);
echo $updated2 !== false ? "OK — post $post_id2 azuriran\n" : "UPDATE FAILED 16530\n";
