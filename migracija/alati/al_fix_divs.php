<?php
/**
 * Uklanja NESPAREN `</div>` iz `[vc_column_text]` blokova (W7 F1.4).
 *
 *   wp eval-file al_fix_divs.php               # PROBA nad svim stranicama
 *   wp eval-file al_fix_divs.php 16673         # PROBA nad jednom
 *   wp eval-file al_fix_divs.php 16673 apply
 *   wp eval-file al_fix_divs.php all apply
 *
 * Zašto uopšte smeta: WPBakery renderuje `[vc_column_text]` kao
 * `<div class="wpb_text_column"><div class="wpb_wrapper">…`. Jedan višak `</div>`
 * prerano zatvori `.wpb_wrapper`, pa ostatak sekcije ispadne iz kolone i na
 * stranici nastane velika bela rupa.
 *
 * 🔴 Pravilo NIJE „skini zatvarajuće tagove sa kraja bloka". Izmereno 2026-07-28:
 * na `16659` iza viška ide još jedan pasus, a na `17004` blok uopšte nema
 * otvarajući `<div>` — višak sedi ispred JSON-LD skripte. Zato se blok prolazi
 * redom i briše se TAČNO onaj `</div>` na kome bilans padne ispod nule; sve
 * ostalo ostaje nedirnuto.
 *
 * Namerno se NE dira ništa van `[vc_column_text]`: prazne `<div>` kockice u
 * paletama boja (`bergo-unique`, `ecotile-5005`, `podloge-za-parking` …) su
 * ispravne i naivna pretraga „praznih divova" ih lažno prijavljuje.
 */

$arg   = trim( $args[0] ?? '' );
$apply = ( ( $args[1] ?? '' ) === 'apply' );

if ( '' === $arg || 'all' === $arg ) {
	$ids = get_posts( array(
		'post_type'      => array( 'page', 'post' ),
		'post_status'    => array( 'publish', 'draft' ),
		'posts_per_page' => -1,
		'fields'         => 'ids',
	) );
} else {
	$ids = array_map( 'intval', array_filter( explode( ',', $arg ) ) );
}

/**
 * Vraća array( novi_sadržaj_bloka, broj_uklonjenih ).
 */
function al_fix_divs_block( $inner ) {
	$depth   = 0;
	$removed = 0;
	$out     = preg_replace_callback(
		'#<div\b[^>]*>|</div\s*>#i',
		function ( $m ) use ( &$depth, &$removed ) {
			if ( '<' === $m[0][0] && '/' === $m[0][1] ) {
				if ( 0 === $depth ) {
					$removed++;
					return '';           // nesparen — briše se
				}
				$depth--;
				return $m[0];
			}
			$depth++;
			return $m[0];
		},
		$inner
	);
	return array( $out, $removed );
}

$touched = 0;
$total   = 0;

foreach ( $ids as $id ) {
	$post = get_post( $id );
	if ( ! $post || false === strpos( $post->post_content, '[vc_column_text' ) ) {
		continue;
	}

	$page_removed = 0;
	$new = preg_replace_callback(
		'#(\[vc_column_text\b[^\]]*\])(.*?)(\[/vc_column_text\])#is',
		function ( $m ) use ( &$page_removed ) {
			list( $fixed, $n ) = al_fix_divs_block( $m[2] );
			$page_removed += $n;
			return $m[1] . $fixed . $m[3];
		},
		$post->post_content
	);

	if ( ! $page_removed ) {
		continue;
	}

	$touched++;
	$total += $page_removed;
	WP_CLI::log( sprintf( '%-6d %-52s uklonjeno </div>: %d', $id, $post->post_name, $page_removed ) );

	if ( ! $apply ) {
		continue;
	}

	$bk = 'C:/Users/Miroslav/AppData/Local/Temp/al-content-backup';
	if ( ! is_dir( $bk ) ) { mkdir( $bk, 0777, true ); }
	file_put_contents( $bk . '/' . $id . '-divs-' . date( 'Ymd-His' ) . '.html', $post->post_content );

	wp_update_post( array( 'ID' => $id, 'post_content' => $new ) );
}

if ( $apply ) {
	WP_CLI::success( sprintf( 'Upisano: %d stranica, %d nesparenih </div>.', $touched, $total ) );
} else {
	WP_CLI::success( sprintf( 'PROBA — %d stranica, %d nesparenih </div>. Ništa nije upisano.', $touched, $total ) );
}
