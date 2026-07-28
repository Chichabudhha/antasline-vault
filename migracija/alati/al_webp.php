<?php
/**
 * Zajedničke pomoćne funkcije za WebP izlaz (al_import.php i al_convert_webp.php).
 *
 * Zašto WebP: na istom kvalitetu (q82) daje ~30% manji fajl od JPEG-a, a LCP je
 * na ovom sajtu i dalje crven (CLAUDE §7.6). Podrška je univerzalna od 2020 —
 * jedini pravi rizik je da GD build nema imagewebp(), zato provera pa tih pad
 * nazad na JPEG umesto pucanja usred uvoza.
 */

if ( ! function_exists( 'al_webp_supported' ) ) {
	function al_webp_supported() {
		static $ok = null;
		if ( null === $ok ) {
			$ok = function_exists( 'imagewebp' )
				&& wp_image_editor_supports( array( 'mime_type' => 'image/webp' ) );
		}
		return $ok;
	}
}

if ( ! function_exists( 'al_target_ext' ) ) {
	/**
	 * Ciljna ekstenzija. SVG i GIF se NE diraju: SVG je vektor (konverzija bi ga
	 * rasterizovala), a animirani GIF bi kroz GD ostao bez animacije.
	 */
	function al_target_ext( $src ) {
		$in = strtolower( pathinfo( $src, PATHINFO_EXTENSION ) );
		if ( in_array( $in, array( 'svg', 'gif' ), true ) ) {
			return $in;
		}
		if ( ! al_webp_supported() ) {
			return 'png' === $in ? 'png' : 'jpg';
		}
		return 'webp';
	}
}

if ( ! function_exists( 'al_ids_from_content' ) ) {
	/**
	 * Svi ID-evi priloga upotrebljenih u jednom `post_content`.
	 *
	 * Mora da hvata OBA oblika. Prvo izdanje je gledalo samo `<img src>` i time
	 * promašilo celu `/galerija-sportskih-terena/` (2,7 MB slika): tamo je
	 * `[gallery ids="…"]` shortcode, koji u `post_content` uopšte nema <img> tag —
	 * WordPress ga generiše tek pri renderovanju.
	 */
	function al_ids_from_content( $content ) {
		$ids = array();

		preg_match_all( '#<img[^>]+src=("|\')([^"\']+)\1#i', $content, $m );
		foreach ( array_unique( $m[2] ) as $u ) {
			if ( strpos( $u, '/wp-content/uploads/' ) === false ) { continue; }
			if ( preg_match( '#\.svg$#i', $u ) ) { continue; }
			$aid = al_attachment_id_from_url( $u );
			if ( $aid ) { $ids[] = $aid; }
		}

		if ( preg_match_all( '#\[gallery[^\]]*\bids=("|\')([0-9,\s]+)\1#i', $content, $g ) ) {
			foreach ( $g[2] as $list ) {
				foreach ( array_filter( array_map( 'intval', preg_split( '#[,\s]+#', $list ) ) ) as $gid ) {
					$ids[] = $gid;
				}
			}
		}

		return array_values( array_unique( $ids ) );
	}
}

if ( ! function_exists( 'al_target_mime' ) ) {
	function al_target_mime( $path ) {
		switch ( strtolower( pathinfo( $path, PATHINFO_EXTENSION ) ) ) {
			case 'webp':
				return 'image/webp';
			case 'png':
				return 'image/png';
			case 'gif':
				return 'image/gif';
			default:
				return 'image/jpeg';
		}
	}
}
