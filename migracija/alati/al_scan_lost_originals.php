<?php
/**
 * Provera zdravlja medijateke: prilozi čiji ORIGINAL ne postoji na disku.
 *
 * Nastalo 2026-07-29 iz F2 verifikacije — prilog 3331 je pokazivao na
 * `…-10mm-1.jpg` kog nema, dok `…-10mm-1.webp` leži pored njega. To je rep
 * napuštenog `al_convert_webp.php` pristupa (konvertovao je SAM original, v.
 * dnevnik 2026-07-28 zašto je pristup odbačen): fajl je prekodiran, a
 * `_wp_attached_file` je ostao na staroj ekstenziji.
 *
 * Razlika u odnosu na `al_fix_missing_sizes.php`: tamo fali IZVEDENA veličina,
 * ovde fali sam original — što obara i sve veličine odjednom.
 *
 * Poziv: … eval-file al_scan_lost_originals.php [apply]
 */

$APPLY = in_array( 'apply', $args, true );
$up    = wp_upload_dir();

global $wpdb;
$rows = $wpdb->get_results( "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key='_wp_attached_file'" );

$missing = array(); $fixable = array();
foreach ( $rows as $r ) {
	$abs = $up['basedir'] . '/' . $r->meta_value;
	if ( file_exists( $abs ) ) { continue; }

	// postoji li blizanac sa drugom ekstenzijom?
	$twin = '';
	foreach ( array( 'webp', 'jpg', 'jpeg', 'png' ) as $ext ) {
		$cand = preg_replace( '/\.[a-z0-9]+$/i', '.' . $ext, $abs );
		if ( $cand !== $abs && file_exists( $cand ) ) { $twin = $cand; break; }
	}
	if ( $twin ) { $fixable[] = array( $r->post_id, $r->meta_value, $twin ); }
	else         { $missing[] = array( $r->post_id, $r->meta_value ); }
}

WP_CLI::log( sprintf( 'priloga ukupno: %d', count( $rows ) ) );
WP_CLI::log( sprintf( 'original fali, ima blizanca (popravljivo): %d', count( $fixable ) ) );
foreach ( $fixable as $f ) {
	WP_CLI::log( sprintf( '   #%d  %s  →  %s', $f[0], $f[1], basename( $f[2] ) ) );
}
WP_CLI::log( sprintf( 'original fali, nema blizanca (mrtav zapis): %d', count( $missing ) ) );
foreach ( array_slice( $missing, 0, 15 ) as $m ) {
	WP_CLI::log( sprintf( '   #%d  %s', $m[0], $m[1] ) );
}

if ( ! $APPLY ) { WP_CLI::success( 'PROBA — ništa nije upisano (dodaj `apply`)' ); return; }

require_once ABSPATH . 'wp-admin/includes/image.php';
foreach ( $fixable as $f ) {
	list( $id, $old, $twin ) = $f;
	$rel = ltrim( str_replace( $up['basedir'], '', $twin ), '/\\' );
	update_post_meta( $id, '_wp_attached_file', $rel );
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $twin ) );
	$mime = wp_check_filetype( $twin );
	if ( ! empty( $mime['type'] ) ) {
		wp_update_post( array( 'ID' => $id, 'post_mime_type' => $mime['type'] ) );
	}
	WP_CLI::log( sprintf( '  popravljen #%d → %s', $id, $rel ) );
}
WP_CLI::success( sprintf( 'popravljeno %d priloga; %d mrtvih zapisa NIJE dirano', count( $fixable ), count( $missing ) ) );
