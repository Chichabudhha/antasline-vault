<?php
/**
 * EXPONA Living Clic (16919) — materijal pronađen na polyflor.se (isti
 * proizvođač/porodica kao ostali Expona proizvodi, EN tehnički list čitljiv
 * uprkos "SE" u imenu fajla). Ranija provera je gledala samo objectflor.de
 * (404 za ovu liniju) i nije probala polyflor.se.
 *
 * Radi: (1) glavna slika + 3 galerijske (1:1, max 1000px, WebP, isti
 * obrazac kao job-w7f29-dobavljac-slike.php), (2) tehnički list + brošura
 * PDF u medijateku, (3) post_content zamenjen — uklonjena "nije dobavljeno"
 * napomena, tabela popunjena stvarnim podacima iz PDF-a (bilo pogrešno
 * 0,55mm sloj habanja, ispravno 0,3mm), dodata "Tehnička dokumentacija"
 * sekcija.
 *
 * Poziv:  php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-16919-expona-living-clic.php [apply]
 */

$APPLY = in_array( 'apply', (array) ( $args ?? array() ), true );

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';

$post_id = 16919;
$srcDir  = 'C:/Users/Miroslav/AppData/Local/Temp/claude/C--Projekti-antasline-vault/89a6e34c-32cf-47fb-a406-2be70b237384/scratchpad/expona-living-clic';

$images = array(
	array( 'file' => '16919-main.webp', 'alt' => 'EXPONA Living Clic — uzorak dezena, klik LVT daska', 'role' => 'thumbnail' ),
	array( 'file' => '16919-gallery-1.webp', 'alt' => 'EXPONA Living Clic — ugrađen pod, enterijer', 'role' => 'gallery' ),
	array( 'file' => '16919-gallery-2.webp', 'alt' => 'EXPONA Living Clic — ugrađen pod, enterijer 2', 'role' => 'gallery' ),
	array( 'file' => '16919-gallery-3.webp', 'alt' => 'EXPONA Living Clic — ugrađen pod, enterijer 3', 'role' => 'gallery' ),
);

$pdfs = array(
	array( 'file' => 'Expona-Living-Clic-tehnicki-podaci.pdf', 'title' => 'Tehnički list EXPONA Living Clic' ),
	array( 'file' => 'Expona-Living-Clic-brosura.pdf', 'title' => 'Brošura EXPONA Living Clic' ),
);

echo $APPLY ? "=== REZIM: APPLY ===\n\n" : "=== PROBA (bez izmena) ===\n\n";

$post = get_post( $post_id );
if ( ! $post ) { echo "🔴 post {$post_id} ne postoji\n"; return; }

$existingThumb = get_post_meta( $post_id, '_thumbnail_id', true );
if ( $existingThumb ) {
	echo "⚠ već ima _thumbnail_id={$existingThumb} — skripta neće menjati slike, samo tekst/PDF-ove\n";
	$images = array();
}

$uploadDir  = wp_upload_dir();
$galleryIds = array();

foreach ( $images as $it ) {
	$src = $srcDir . '/' . $it['file'];
	if ( ! file_exists( $src ) ) { echo "🔴 nema fajla {$src}\n"; continue; }

	$name = sanitize_title( pathinfo( $it['file'], PATHINFO_FILENAME ) ) . '.webp';
	$dest = $uploadDir['path'] . '/' . $name;

	echo "── {$it['role']}: {$it['file']} → {$name}\n";

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

	if ( 'thumbnail' === $it['role'] ) {
		update_post_meta( $post_id, '_thumbnail_id', $att_id );
	} else {
		$galleryIds[] = $att_id;
	}

	echo "   ✅ uvezeno #{$att_id} ({$info[0]}x{$info[1]})\n";
}

if ( $APPLY && $galleryIds ) {
	update_post_meta( $post_id, '_product_image_gallery', implode( ',', $galleryIds ) );
	echo "── galerija: " . implode( ',', $galleryIds ) . "\n";
}

$pdfUrls = array();
foreach ( $pdfs as $pdf ) {
	$src = $srcDir . '/' . $pdf['file'];
	if ( ! file_exists( $src ) ) { echo "🔴 nema fajla {$src}\n"; continue; }

	echo "── PDF: {$pdf['file']}\n";

	if ( ! $APPLY ) {
		$pdfUrls[ $pdf['file'] ] = $uploadDir['url'] . '/' . $pdf['file'];
		continue;
	}

	$dest = $uploadDir['path'] . '/' . $pdf['file'];
	if ( ! copy( $src, $dest ) ) { echo "   🔴 kopiranje nije uspelo\n"; continue; }

	$att = array(
		'post_mime_type' => 'application/pdf',
		'post_title'     => $pdf['title'],
		'post_content'   => '',
		'post_status'    => 'inherit',
	);
	$att_id = wp_insert_attachment( $att, $dest, $post_id );
	wp_update_attachment_metadata( $att_id, wp_generate_attachment_metadata( $att_id, $dest ) );

	$pdfUrls[ $pdf['file'] ] = wp_get_attachment_url( $att_id );
	echo "   ✅ uvezeno #{$att_id} → " . $pdfUrls[ $pdf['file'] ] . "\n";
}

$newContent = file_get_contents( $srcDir . '/new-content.html' );
if ( $APPLY && $pdfUrls ) {
	$newContent = str_replace(
		'http://localhost/antasline/wp-content/uploads/2026/08/Expona-Living-Clic-tehnicki-podaci.pdf',
		$pdfUrls['Expona-Living-Clic-tehnicki-podaci.pdf'] ?? 'http://localhost/antasline/wp-content/uploads/2026/08/Expona-Living-Clic-tehnicki-podaci.pdf',
		$newContent
	);
	$newContent = str_replace(
		'http://localhost/antasline/wp-content/uploads/2026/08/Expona-Living-Clic-brosura.pdf',
		$pdfUrls['Expona-Living-Clic-brosura.pdf'] ?? 'http://localhost/antasline/wp-content/uploads/2026/08/Expona-Living-Clic-brosura.pdf',
		$newContent
	);
}

echo "\n── post_content: " . strlen( $newContent ) . " karaktera (staro: " . strlen( $post->post_content ) . ")\n";

if ( $APPLY ) {
	global $wpdb;
	$wpdb->update(
		$wpdb->posts,
		array( 'post_content' => $newContent ),
		array( 'ID' => $post_id )
	);
	clean_post_cache( $post_id );
	echo "   ✅ post_content upisan preko \$wpdb->update()\n";
}

echo $APPLY ? "\nGotovo.\n" : "\nProba gotova (ništa nije upisano) — pokreni sa 'apply'.\n";
