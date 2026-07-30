<?php
/**
 * Engleske EXPONA brošure (Commercial + Simplay) — M odluka 2026-07-30 ("da").
 * Izvor: polyflor.com (isti proizvođač/grupa kao objectflor.de — James Halstead
 * Group, isti proizvodi pod regionalnim brendom; objectflor.de download-center
 * je JS-renderovan i nedostupan alatima, polyflor.com Product Specification PDF
 * je funkcionalni ekvivalent "katalog dezena" dokumenta).
 * PDF-ovi već preuzeti i verifikovani (pravi PDF, 212/102 str.) u
 * C:/Users/Miroslav/.claude/jobs/ee33c1f1/tmp/expona_docs/.
 *
 * Nemačke verzije se NE brišu — dodaje se engleska pored, oba označena (DE)/(EN).
 *
 * php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-expona-en-brosure.php          # proba
 * php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-expona-en-brosure.php apply    # upis
 */

global $wpdb;
$apply = ( ( $args[0] ?? '' ) === 'apply' );

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$srcDir = 'C:/Users/Miroslav/.claude/jobs/ee33c1f1/tmp/expona_docs';
$uploadDir = wp_upload_dir();

$docs = array(
	'commercial' => array(
		'file'  => 'expona-commercial-en.pdf',
		'name'  => 'Product-Specification-EXPONA-COMMERCIAL-English.pdf',
		'title' => 'Product Specification EXPONA COMMERCIAL (English)',
	),
	'simplay' => array(
		'file'  => 'expona-simplay-en.pdf',
		'name'  => 'Product-Specification-EXPONA-SIMPLAY-19dB-English.pdf',
		'title' => 'Product Specification EXPONA SIMPLAY 19dB (English)',
	),
);

echo $apply ? "=== APPLY ===\n\n" : "=== PROBA ===\n\n";

$att_ids = array();
foreach ( $docs as $key => $d ) {
	$src = "$srcDir/{$d['file']}";
	if ( ! file_exists( $src ) ) { echo "🔴 nema fajla: $src\n"; continue; }
	$dest = $uploadDir['path'] . '/' . $d['name'];
	echo "{$key}: {$d['file']} -> {$dest}\n";
	if ( ! $apply ) { continue; }
	if ( ! copy( $src, $dest ) ) { echo "  🔴 kopiranje nije uspelo\n"; continue; }
	$att = array(
		'post_mime_type' => 'application/pdf',
		'post_title'     => $d['title'],
		'post_content'   => '',
		'post_status'    => 'inherit',
	);
	$id = wp_insert_attachment( $att, $dest, 0 );
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $dest ) );
	$att_ids[ $key ] = array( 'id' => $id, 'url' => $uploadDir['url'] . '/' . $d['name'] );
	echo "  ✅ uvezeno #{$id}\n";
}

if ( ! $apply ) { echo "\nProba gotova, ništa upisano.\n"; return; }

$commercial_url = $att_ids['commercial']['url'];
$simplay_url    = $att_ids['simplay']['url'];

$dir = 'C:/Projekti/antasline-vault/scratchpad/content-backup';
if ( ! is_dir( $dir ) ) { mkdir( $dir, 0777, true ); }

function al_backup_and_update( $wpdb, $dir, $id, $new_content ) {
	$old = get_post_field( 'post_content', $id );
	file_put_contents( "$dir/{$id}-pre-expona-en-brosure-2026-07-30.txt", $old );
	$wpdb->update( $wpdb->posts, array( 'post_content' => $new_content ), array( 'ID' => $id ) );
	clean_post_cache( $id );
}

// 16914 (proizvod, <ul><li> stil) — relabel DE + dodaj EN
$c = get_post_field( 'post_content', 16914 );
$c = str_replace(
	'>Katalog dezena EXPONA Commercial (PDF, DE)</a></li>',
	'>Katalog dezena EXPONA Commercial (PDF, DE)</a></li><li><a href="' . esc_url( $commercial_url ) . '" target="_blank">Katalog dezena EXPONA Commercial (PDF, EN)</a></li>',
	$c
);
al_backup_and_update( $wpdb, $dir, 16914, $c );
echo "16914: " . ( strpos( $c, $commercial_url ) !== false ? '✅ dodato' : '🔴 anchor nije nađen' ) . "\n";

// 16685 (stranica, al-grid card stil) — relabel DE + dodaj EN karticu
$c = get_post_field( 'post_content', 16685 );
$c = str_replace(
	'rel="noopener">Brošura</a>',
	'rel="noopener">Brošura (DE)</a>',
	$c
);
$c = str_replace(
	'rel="noopener">Brošura (DE)</a></div></div>',
	'rel="noopener">Brošura (DE)</a></div></div><div class="al-card"><div class="al-card__body"><a class="al-btn al-btn--ghost" href="' . esc_url( $commercial_url ) . '" target="_blank" rel="noopener">Brošura (EN)</a></div></div>',
	$c
);
al_backup_and_update( $wpdb, $dir, 16685, $c );
echo "16685: " . ( strpos( $c, $commercial_url ) !== false ? '✅ dodato' : '🔴 anchor nije nađen' ) . "\n";

// 16916 (proizvod, <ul><li> stil) — relabel + dodaj EN
$c = get_post_field( 'post_content', 16916 );
$c = str_replace(
	'>Katalog dezena EXPONA Simplay 19dB (PDF)</a></li>',
	'>Katalog dezena EXPONA Simplay 19dB (PDF, DE)</a></li><li><a href="' . esc_url( $simplay_url ) . '" target="_blank">Katalog dezena EXPONA Simplay 19dB (PDF, EN)</a></li>',
	$c
);
al_backup_and_update( $wpdb, $dir, 16916, $c );
echo "16916: " . ( strpos( $c, $simplay_url ) !== false ? '✅ dodato' : '🔴 anchor nije nađen' ) . "\n";

// 17252 (stranica, al-grid card stil)
$c = get_post_field( 'post_content', 17252 );
$c = str_replace(
	'rel="noopener">Katalog dezena</a>',
	'rel="noopener">Katalog dezena (DE)</a>',
	$c
);
$c = str_replace(
	'rel="noopener">Katalog dezena (DE)</a></div></div>',
	'rel="noopener">Katalog dezena (DE)</a></div></div><div class="al-card"><div class="al-card__body"><a class="al-btn al-btn--ghost" href="' . esc_url( $simplay_url ) . '" target="_blank" rel="noopener">Katalog dezena (EN)</a></div></div>',
	$c
);
al_backup_and_update( $wpdb, $dir, 17252, $c );
echo "17252: " . ( strpos( $c, $simplay_url ) !== false ? '✅ dodato' : '🔴 anchor nije nađen' ) . "\n";

echo "\nGotovo.\n";
