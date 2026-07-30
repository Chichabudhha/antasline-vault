<?php
/**
 * AMSS pravi logo dodat u klijenti red na "O nama" (571) — M dao izvor
 * (amss.org.rs), 2026-07-30. Postojeći "amss-logo.webp" (#15347) je
 * pogrešan fajl (žuti "AMCC" znak, otkriveno 2026-07-29) i NIGDE nije
 * referenciran u sadržaju — ostavljen netaknut, samo se dodaje novi
 * ispravan prilog pored postojećih 5 klijent-logoa.
 *
 * php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-amss-logo.php          # proba
 * php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-amss-logo.php apply    # upis
 */

global $wpdb;
$apply = ( ( $args[0] ?? '' ) === 'apply' );

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$src = 'C:/Users/Miroslav/.claude/jobs/ee33c1f1/tmp/amss_logo/amss-logo-processed.webp';
if ( ! file_exists( $src ) ) { echo "🔴 nema fajla: $src\n"; return; }

$uploadDir = wp_upload_dir();
$name = 'amss-logo-zvanicni.webp';
$dest = $uploadDir['path'] . '/' . $name;

$info = getimagesize( $src );
echo "Izvor: {$info[0]}x{$info[1]}\n";
echo "Cilj: $dest\n";

$post = get_post( 571 );
$c = $post->post_content;
$needle = '<img src="http://localhost/antasline/wp-content/uploads/2023/01/orion-logo.webp" alt="Orion telekom" width="250" height="75" loading="lazy" />';
$found = strpos( $c, $needle ) !== false;
echo 'Orion anchor pronađen: ' . ( $found ? 'da' : 'NE — STOP' ) . "\n";

if ( ! $apply ) { echo "\nProba gotova, ništa upisano.\n"; return; }
if ( ! $found ) { echo "🔴 anchor nije nađen, ne upisujem\n"; return; }

if ( ! copy( $src, $dest ) ) { echo "🔴 kopiranje nije uspelo\n"; return; }

$att = array(
	'post_mime_type' => 'image/webp',
	'post_title'     => 'AMSS logo (Auto-moto savez Srbije)',
	'post_content'   => '',
	'post_status'    => 'inherit',
);
$att_id = wp_insert_attachment( $att, $dest, 571 );
wp_update_attachment_metadata( $att_id, wp_generate_attachment_metadata( $att_id, $dest ) );
update_post_meta( $att_id, '_wp_attachment_image_alt', 'AMSS — Auto-moto savez Srbije' );

$new_img = '<img src="' . esc_url( $uploadDir['url'] . '/' . $name ) . '" alt="AMSS" width="' . $info[0] . '" height="' . $info[1] . '" loading="lazy" />';

$dir = 'C:/Projekti/antasline-vault/scratchpad/content-backup';
if ( ! is_dir( $dir ) ) { mkdir( $dir, 0777, true ); }
file_put_contents( "$dir/571-pre-amss-logo-2026-07-30.txt", $c );

$new_c = str_replace( $needle, $needle . $new_img, $c );
$wpdb->update( $wpdb->posts, array( 'post_content' => $new_c ), array( 'ID' => 571 ) );
clean_post_cache( 571 );

echo "✅ uvezeno #{$att_id}, dodato u klijenti red na 571\n";
