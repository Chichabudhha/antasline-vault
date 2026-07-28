<?php
/**
 * Regeneriše SAMO al-* veličine (xs/sm/md/lg/lb) — kao WebP, preko
 * `image_editor_output_format` filtera iz woodmart-child/functions.php.
 *
 *   wp eval-file al_regen_sizes.php content         # PROBA
 *   wp eval-file al_regen_sizes.php content apply
 *   wp eval-file al_regen_sizes.php post:16657 apply
 *   wp eval-file al_regen_sizes.php 17031,17032 apply
 *
 * Original se NE dira (ni fajl, ni `_wp_attached_file`, ni `post_mime_type`), pa se
 * ne dira ni `post_content` — `al_enhance_content_images()` sam čita nove veličine
 * iz metapodataka. Ostale veličine (WooCommerce, tema) ostaju kakve jesu.
 *
 * Stari al-* fajlovi se brišu tek kad novi uspešno legne.
 */

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once __DIR__ . '/al_webp.php';

$AL_SIZES = array( 'al-xs', 'al-sm', 'al-md', 'al-lg', 'al-lb' );

$sel   = $args[0] ?? '';
$apply = ( ( $args[1] ?? '' ) === 'apply' );
if ( ! $sel ) { WP_CLI::error( 'Zadaj: content | post:ID | 17031,17032' ); }

// ---- izbor priloga ----
if ( 'content' === $sel ) {
	// `[gallery]` MORA da bude u uslovu: stranica sa galerijom često nema nijedan
	// <img> u post_content-u (WP ga generiše tek pri renderovanju), pa ju je raniji
	// filter samo na '<img' potpuno preskakao — tako je /galerija-sportskih-terena/
	// sa 2,7 MB slika ispala iz prve regeneracije.
	$rows = $GLOBALS['wpdb']->get_col(
		"SELECT post_content FROM {$GLOBALS['wpdb']->posts}
		 WHERE post_status = 'publish' AND post_type IN ('page','post')
		 AND ( post_content LIKE '%<img%' OR post_content LIKE '%[gallery%' )"
	);
	$ids = array();
	foreach ( $rows as $c ) {
		$ids = array_merge( $ids, al_ids_from_content( $c ) );
	}
	$ids = array_values( array_unique( $ids ) );
} elseif ( 0 === strpos( $sel, 'post:' ) ) {
	$post = get_post( (int) substr( $sel, 5 ) );
	if ( ! $post ) { WP_CLI::error( 'Nema posta' ); }
	$ids = al_ids_from_content( $post->post_content );
} else {
	$ids = array_filter( array_map( 'intval', explode( ',', $sel ) ) );
}

WP_CLI::log( sprintf( '%s: %d priloga', $apply ? 'REGENERACIJA' : 'PROBA (bez izmena)', count( $ids ) ) );

$oldTotal = 0;
$newTotal = 0;
$done     = 0;
$problems = array();

foreach ( $ids as $id ) {
	$file = get_attached_file( $id );
	$mime = get_post_mime_type( $id );
	if ( ! $file || ! file_exists( $file ) ) { $problems[] = "$id: nema fajl"; continue; }
	if ( ! in_array( $mime, array( 'image/jpeg', 'image/webp' ), true ) ) {
		continue;   // PNG/GIF/SVG namerno ne diramo (v. komentar uz filter)
	}

	$meta = wp_get_attachment_metadata( $id );
	if ( ! is_array( $meta ) ) { $problems[] = "$id: nema metapodatke"; continue; }
	$dir = dirname( $file );

	// koliko al-* trenutno zauzima
	$oldFiles = array();
	foreach ( $AL_SIZES as $s ) {
		if ( ! empty( $meta['sizes'][ $s ]['file'] ) ) {
			$p = $dir . '/' . $meta['sizes'][ $s ]['file'];
			if ( file_exists( $p ) ) { $oldFiles[ $s ] = $p; $oldTotal += filesize( $p ); }
		}
	}

	if ( ! $apply ) {
		$done++;
		continue;
	}

	$editor = wp_get_image_editor( $file );
	if ( is_wp_error( $editor ) ) { $problems[] = "$id: editor — " . $editor->get_error_message(); continue; }

	$req = array();
	foreach ( $AL_SIZES as $s ) {
		$dim = wp_get_registered_image_subsizes()[ $s ] ?? null;
		if ( $dim ) { $req[ $s ] = $dim; }
	}

	$made = $editor->multi_resize( $req );
	if ( ! $made ) { $problems[] = "$id: multi_resize prazan"; continue; }

	foreach ( $made as $s => $info ) {
		$new = $dir . '/' . $info['file'];
		if ( file_exists( $new ) ) { $newTotal += filesize( $new ); }
		$meta['sizes'][ $s ] = $info;
	}

	// 🔴 Brisanje starih al-* fajlova TEK POSLE ažuriranja svih zapisa, i samo ako
	// nijedna DRUGA veličina ne pokazuje na isti fajl.
	//
	// WordPress deli isti fajl između veličina koje daju identične dimenzije: kod
	// priloga 16621 su i `al-sm` i `woocommerce_single` bili `…-600x400.jpg`. Prvo
	// izdanje ovog skripta je taj fajl obrisalo kao „stari al-sm" i time razvalilo
	// WooCommerce sliku — 404 koji je uhvatila tek provera svih URL-ova slika.
	foreach ( $oldFiles as $s => $oldPath ) {
		if ( ! file_exists( $oldPath ) ) { continue; }
		if ( isset( $meta['sizes'][ $s ]['file'] ) && $dir . '/' . $meta['sizes'][ $s ]['file'] === $oldPath ) {
			continue;                       // novi fajl je isti kao stari
		}
		$stillUsed = false;
		foreach ( $meta['sizes'] as $other => $oi ) {
			if ( ! empty( $oi['file'] ) && $dir . '/' . $oi['file'] === $oldPath ) { $stillUsed = true; break; }
		}
		if ( ! $stillUsed ) { @unlink( $oldPath ); }
	}

	wp_update_attachment_metadata( $id, $meta );
	$done++;
	if ( 0 === $done % 25 ) { WP_CLI::log( "  … $done" ); }
}

foreach ( array_slice( $problems, 0, 20 ) as $p ) { WP_CLI::warning( $p ); }
if ( count( $problems ) > 20 ) { WP_CLI::warning( '… i još ' . ( count( $problems ) - 20 ) ); }

if ( $apply ) {
	WP_CLI::success( sprintf(
		'Regenerisano %d priloga. al-* veličine: %.1f MB → %.1f MB (%+d%%). Problema: %d.',
		$done, $oldTotal / 1048576, $newTotal / 1048576,
		$oldTotal ? -round( ( 1 - $newTotal / $oldTotal ) * 100 ) : 0,
		count( $problems )
	) );
} else {
	WP_CLI::success( sprintf( 'PROBA: %d priloga, al-* trenutno zauzima %.1f MB.', $done, $oldTotal / 1048576 ) );
}
