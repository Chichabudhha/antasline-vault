<?php
/**
 * W7 F2.9 rep — nastavak "40 proizvoda bez slike": 14 proizvoda dobija pravu
 * fotografiju sa sajta proizvođača (Bergo Flooring, Geoplast, Radici Sport,
 * Ecotile, Heskins/PermaStripe). Slike već obrađene lokalno (1:1, max 1000px,
 * WebP) pre uvoza — ovaj skript samo ubacuje u medijateku i postavlja
 * _thumbnail_id.
 *
 * Poziv:  php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-w7f29-dobavljac-slike.php [apply]
 */

$APPLY = in_array( 'apply', (array) ( $args ?? array() ), true );

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$srcDir = 'C:/Users/Miroslav/.claude/jobs/ee33c1f1/tmp/product_imgs_processed';

$items = array(
	16800 => array( 'file' => '16800.webp', 'alt' => 'Bergo Ultimate PLUS by GreenMatter — tamnozelena eko sportska ploča', 'source' => 'bergoflooring.com' ),
	16836 => array( 'file' => '16836.webp', 'alt' => 'Bergo Excellence — ploča za brodske palube', 'source' => 'bergoflooring.com' ),
	16842 => array( 'file' => '16842.webp', 'alt' => 'Bergo Extreme IMO — sertifikovana brodska ploča', 'source' => 'bergoflooring.com' ),
	16907 => array( 'file' => '16907.webp', 'alt' => 'Geoplast Salvaverde Type A — travna rešetka', 'source' => 'geoplastglobal.com' ),
	16908 => array( 'file' => '16908.webp', 'alt' => 'Geoplast Salvaverde Type B — travna rešetka', 'source' => 'geoplastglobal.com' ),
	16910 => array( 'file' => '16910.webp', 'alt' => 'Geoplast Geograss — travna rešetka', 'source' => 'geoplastglobal.com' ),
	16894 => array( 'file' => '16894.webp', 'alt' => 'Radici ULTRAMIX EVO N.I. veštačka trava — teren za mali fudbal', 'source' => 'radicisport.it' ),
	16895 => array( 'file' => '16895.webp', 'alt' => 'Radici Tournament 20 — veštačka trava za tenis i padel', 'source' => 'radicisport.it' ),
	16922 => array( 'file' => '16922.webp', 'alt' => 'PermaStripe traka za obeležavanje poda', 'source' => 'heskins.com' ),
	16929 => array( 'file' => '16929.webp', 'alt' => 'SureGrip stepenišni protivklizni profil', 'source' => 'ecotileflooring.com' ),
	16930 => array( 'file' => '16930.webp', 'alt' => 'Ecotile E500 T-Joint rampa 500×90 mm', 'source' => 'shop.ecotileflooring.com' ),
	16939 => array( 'file' => '16939.webp', 'alt' => 'Ecotile E500 T-Joint ugaona rampa', 'source' => 'shop.ecotileflooring.com' ),
	16943 => array( 'file' => '16943.webp', 'alt' => 'Ecotile X500 X-Joint rampa 497×90 mm', 'source' => 'shop.ecotileflooring.com' ),
	16949 => array( 'file' => '16949.webp', 'alt' => 'Ecotile X500 X-Joint ugaona rampa', 'source' => 'shop.ecotileflooring.com' ),
);

echo $APPLY ? "=== REZIM: APPLY ===\n\n" : "=== PROBA (bez izmena) ===\n\n";

$uploadDir = wp_upload_dir();

foreach ( $items as $post_id => $it ) {
	$post = get_post( $post_id );
	if ( ! $post ) { echo "🔴 {$post_id}: nema posta\n"; continue; }

	$src = $srcDir . '/' . $it['file'];
	if ( ! file_exists( $src ) ) { echo "🔴 {$post_id}: nema fajla {$src}\n"; continue; }

	$existingThumb = get_post_meta( $post_id, '_thumbnail_id', true );
	if ( $existingThumb ) {
		echo "⚠ {$post_id} {$post->post_title}: već ima _thumbnail_id={$existingThumb}, preskačem\n";
		continue;
	}

	$slug = 'proizvodjac-' . sanitize_title( $it['alt'] );
	$name = $slug . '.webp';
	$dest = $uploadDir['path'] . '/' . $name;

	echo "── {$post_id}  {$post->post_title}\n   izvor: {$it['source']}  →  {$name}\n";

	if ( ! $APPLY ) { continue; }

	if ( ! copy( $src, $dest ) ) { echo "   🔴 kopiranje nije uspelo\n"; continue; }
	$info = getimagesize( $dest );

	$att = array(
		'post_mime_type' => 'image/webp',
		'post_title'     => $it['alt'],
		'post_content'   => '',
		'post_status'    => 'inherit',
	);
	$att_id = wp_insert_attachment( $att, $dest, $post_id );
	wp_update_attachment_metadata( $att_id, wp_generate_attachment_metadata( $att_id, $dest ) );
	update_post_meta( $att_id, '_wp_attachment_image_alt', $it['alt'] );
	update_post_meta( $post_id, '_thumbnail_id', $att_id );

	echo "   ✅ uvezeno #{$att_id} ({$info[0]}x{$info[1]}), postavljen kao _thumbnail_id\n";
}

echo $APPLY ? "\nGotovo.\n" : "\nProba gotova (ništa nije upisano) — pokreni sa 'apply'.\n";
