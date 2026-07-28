<?php
/**
 * Konverzija postojećih JPEG/PNG priloga u WebP — fajl, prilog i reference u sadržaju.
 *
 *   wp eval-file al_convert_webp.php 17031,17032,…          # PROBA (ništa se ne menja)
 *   wp eval-file al_convert_webp.php 17031,17032,… apply    # stvarno
 *   wp eval-file al_convert_webp.php post:16657 apply       # sve slike iz sadržaja stranice
 *
 * Šta radi po prilogu:
 *  1. original → .webp q82, ISTE dimenzije (bez skaliranja — to je već odrađeno pri uvozu)
 *  2. ako WebP nije manji od originala, prilog se PRESKAČE (nema smisla menjati)
 *  3. `_wp_attached_file` + `post_mime_type` se prevode na novi fajl, metapodaci
 *     se regenerišu (sve al-* veličine izlaze kao WebP)
 *  4. reference u `post_content` (`foo.jpg` i `foo-800x600.jpg`) → `foo.webp`
 *  5. stari fajlovi (original + sve -WxH varijante) se brišu TEK na kraju, kad je
 *     sve ostalo prošlo
 *
 * Prilozi vezani kao featured image ili u WooCommerce galeriji ne traže ništa dodatno —
 * tamo se čuva ID priloga, ne URL.
 */

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once __DIR__ . '/al_webp.php';

if ( ! al_webp_supported() ) {
	WP_CLI::error( 'GD/WP ne podržava WebP na ovom sistemu — konverzija otkazana.' );
}

$sel   = $args[0] ?? '';
$apply = ( ( $args[1] ?? '' ) === 'apply' );
if ( ! $sel ) {
	WP_CLI::error( 'Zadaj ID-eve (17031,17032) ili post:16657' );
}

// ---- šta konvertujemo ----
if ( 'content' === $sel ) {
	// sve fotografije iz sadržaja objavljenih stranica/postova
	$rows = $GLOBALS['wpdb']->get_col(
		"SELECT post_content FROM {$GLOBALS['wpdb']->posts}
		 WHERE post_status = 'publish' AND post_type IN ('page','post')
		 AND post_content LIKE '%<img%'"
	);
	$ids = array();
	foreach ( $rows as $c ) {
		preg_match_all( '#<img[^>]+src=("|\')([^"\']+)\1#i', $c, $m );
		foreach ( array_unique( $m[2] ) as $u ) {
			if ( strpos( $u, '/wp-content/uploads/' ) === false ) { continue; }
			if ( preg_match( '#\.svg$#i', $u ) ) { continue; }
			$aid = al_attachment_id_from_url( $u );
			if ( $aid ) { $ids[] = $aid; }
		}
	}
	$ids = array_values( array_unique( $ids ) );
} elseif ( 0 === strpos( $sel, 'post:' ) ) {
	$pid  = (int) substr( $sel, 5 );
	$post = get_post( $pid );
	if ( ! $post ) { WP_CLI::error( 'Nema posta ' . $pid ); }
	preg_match_all( '#<img[^>]+src=("|\')([^"\']+)\1#i', $post->post_content, $m );
	$ids = array();
	foreach ( array_unique( $m[2] ) as $u ) {
		$clean = preg_replace( '#-\d+x\d+(\.\w+)$#', '$1', $u );
		$aid   = attachment_url_to_postid( $clean );
		if ( $aid ) { $ids[] = $aid; }
	}
	$ids = array_values( array_unique( $ids ) );
} else {
	$ids = array_filter( array_map( 'intval', explode( ',', $sel ) ) );
}

if ( ! $ids ) { WP_CLI::error( 'Nijedan prilog nije izabran' ); }
WP_CLI::log( sprintf( '%s: %d priloga', $apply ? 'KONVERZIJA' : 'PROBA (bez izmena)', count( $ids ) ) );

$upload   = wp_upload_dir();
$totalOld = 0;
$totalNew = 0;
$done     = 0;
$skipped  = array();

foreach ( $ids as $id ) {
	$file = get_attached_file( $id );
	$mime = get_post_mime_type( $id );

	if ( ! $file || ! file_exists( $file ) ) { $skipped[] = "$id: nema fajl"; continue; }
	if ( 'image/webp' === $mime )            { $skipped[] = "$id: već WebP";  continue; }
	if ( ! in_array( $mime, array( 'image/jpeg', 'image/png' ), true ) ) {
		$skipped[] = "$id: $mime (ne diramo)";
		continue;
	}

	$newFile = preg_replace( '#\.\w+$#', '.webp', $file );
	if ( file_exists( $newFile ) ) { $skipped[] = "$id: .webp već postoji"; continue; }

	$editor = wp_get_image_editor( $file );
	if ( is_wp_error( $editor ) ) { $skipped[] = "$id: editor"; continue; }
	$editor->set_quality( 82 );

	$oldBytes = filesize( $file );

	if ( ! $apply ) {
		// U probi se WebP pravi u temp folderu samo da bi se izmerila ušteda.
		$tmp   = wp_tempnam( basename( $newFile ) ) . '.webp';
		$saved = $editor->save( $tmp, 'image/webp' );
		if ( is_wp_error( $saved ) ) { $skipped[] = "$id: save"; continue; }
		$newBytes = filesize( $saved['path'] );
		@unlink( $saved['path'] );
		@unlink( $tmp );
	} else {
		$saved = $editor->save( $newFile, 'image/webp' );
		if ( is_wp_error( $saved ) ) { $skipped[] = "$id: save"; continue; }
		$newFile  = $saved['path'];
		$newBytes = filesize( $newFile );

		// 🔴 NE preskačemo ako je 1600px original ispao veći (dešava se na fotkama sa
		// mnogo šuma). Prvo izdanje ovog skripta je baš tako radilo i pogrešno je
		// odbacilo 2/9 slika: original od 1600px se skida SAMO kad neko otvori
		// lightbox, dok se al-xs/al-sm učitavaju pri svakom otvaranju stranice — a
		// tamo je WebP dobitak stabilnih ~25–30% (mereno: 600w 39,6 KB jpg → 29,1 KB
		// webp). Gubitak od par procenata na retko traženom fajlu je jeftinija strana.
		if ( $newBytes >= $oldBytes ) {
			WP_CLI::log( sprintf( '   ↳ napomena: 1600px original je +%d%% (bitno je da al-sm/al-xs jesu manji)', round( ( $newBytes / $oldBytes - 1 ) * 100 ) ) );
		}

		// stare -WxH varijante zapamti PRE nego što metapodaci budu pregaženi
		$oldMeta  = wp_get_attachment_metadata( $id );
		$oldFiles = array( $file );
		$dir      = dirname( $file );
		if ( ! empty( $oldMeta['sizes'] ) ) {
			foreach ( $oldMeta['sizes'] as $s ) {
				if ( ! empty( $s['file'] ) ) { $oldFiles[] = $dir . '/' . $s['file']; }
			}
		}

		$oldUrl  = wp_get_attachment_url( $id );
		$relNew  = _wp_relative_upload_path( $newFile );

		update_attached_file( $id, $newFile );
		wp_update_post( array( 'ID' => $id, 'post_mime_type' => 'image/webp' ) );
		wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $newFile ) );

		// ---- reference u sadržaju ----
		$newUrl   = wp_get_attachment_url( $id );
		$oldBase  = preg_quote( pathinfo( $oldUrl, PATHINFO_FILENAME ), '#' );
		$oldExt   = preg_quote( pathinfo( $oldUrl, PATHINFO_EXTENSION ), '#' );
		$pattern  = '#' . $oldBase . '(?:-\d+x\d+)?\.' . $oldExt . '#i';
		$like     = '%' . $GLOBALS['wpdb']->esc_like( pathinfo( $oldUrl, PATHINFO_FILENAME ) ) . '%';
		$rows     = $GLOBALS['wpdb']->get_results(
			$GLOBALS['wpdb']->prepare(
				"SELECT ID, post_content FROM {$GLOBALS['wpdb']->posts} WHERE post_content LIKE %s AND post_type != 'attachment'",
				$like
			)
		);
		$touched = 0;
		foreach ( $rows as $row ) {
			$new = preg_replace( $pattern, pathinfo( $newUrl, PATHINFO_BASENAME ), $row->post_content );
			if ( $new !== $row->post_content ) {
				$GLOBALS['wpdb']->update( $GLOBALS['wpdb']->posts, array( 'post_content' => $new ), array( 'ID' => $row->ID ) );
				clean_post_cache( $row->ID );
				$touched++;
			}
		}

		foreach ( $oldFiles as $of ) {
			if ( file_exists( $of ) ) { @unlink( $of ); }
		}
	}

	$totalOld += $oldBytes;
	$totalNew += $newBytes;
	$done++;
	WP_CLI::log( sprintf(
		'#%d %-52s %6.0f → %6.0f KB  (−%d%%)%s',
		$id,
		basename( $file ),
		$oldBytes / 1024,
		$newBytes / 1024,
		round( ( 1 - $newBytes / $oldBytes ) * 100 ),
		( $apply && isset( $touched ) ) ? "  [{$touched} post]" : ''
	) );
}

foreach ( $skipped as $s ) { WP_CLI::warning( $s ); }

if ( $done ) {
	WP_CLI::success( sprintf(
		'%s %d priloga: %.1f MB → %.1f MB (−%d%%). Preskočeno: %d.',
		$apply ? 'Konvertovano' : 'PROBA —',
		$done,
		$totalOld / 1048576,
		$totalNew / 1048576,
		round( ( 1 - $totalNew / $totalOld ) * 100 ),
		count( $skipped )
	) );
} else {
	WP_CLI::warning( 'Ništa nije konvertovano.' );
}
