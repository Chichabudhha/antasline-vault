<?php
/**
 * Sitewide provera builda — HTTP status, broj <h1>, PHP greške, (opciono) slike.
 *
 * Poziv:
 *   php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file al_verify.php
 *   …eval-file al_verify.php slike        # + HEAD svake slike (src/srcset/href)
 *   …eval-file al_verify.php 571,2699     # samo zadati ID-evi
 *
 * Postoji jer se ista provera do sada pisala iznova svake sesije. Pokriva sve
 * `publish` stranice, postove, proizvode i arhive kategorija proizvoda.
 *
 * 🔴 Provera slika nije opciona posle rada nad medijatekom: 404 na slici NE obara
 * HTTP status stranice, pa je standardna provera ne vidi (tako je 2026-07-28
 * promaklo 212 pokvarenih `woocommerce_single` slika).
 */

$argv_in   = (array) ( $args ?? array() );
$CHECK_IMG = in_array( 'slike', $argv_in, true );
$ONLY      = array();
foreach ( $argv_in as $a ) {
	if ( preg_match( '#^\d+(,\d+)*$#', $a ) ) { $ONLY = array_map( 'intval', explode( ',', $a ) ); }
}

/* --------------------------------------------------------------- skup URL-ova */

$urls = array();

if ( $ONLY ) {
	foreach ( $ONLY as $id ) { $urls[ get_permalink( $id ) ] = $id; }
} else {
	$ids = get_posts( array(
		'post_type'      => array( 'page', 'post', 'product' ),
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'fields'         => 'ids',
	) );
	foreach ( $ids as $id ) { $urls[ get_permalink( $id ) ] = $id; }

	foreach ( get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true ) ) as $t ) {
		$urls[ get_term_link( $t ) ] = "cat:{$t->term_id}";
	}
}
$urls = array_filter( $urls, function ( $u ) { return is_string( $u ) && $u; }, ARRAY_FILTER_USE_KEY );

echo 'Provera ' . count( $urls ) . ' URL-ova' . ( $CHECK_IMG ? ' + slike' : '' ) . "\n\n";

/* ------------------------------------------------------- paralelno preuzimanje */

/** Vraća array(url => array('code'=>int,'body'=>string)). */
function al_fetch_all( array $list, $concurrency = 8, $head_only = false ) {
	$out   = array();
	$queue = array_values( $list );
	$mh    = curl_multi_init();
	$open  = array();

	$add = function () use ( &$queue, &$open, $mh, $head_only ) {
		if ( ! $queue ) { return; }
		$url = array_shift( $queue );
		$ch  = curl_init( $url );
		curl_setopt_array( $ch, array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_TIMEOUT        => 60,
			CURLOPT_NOBODY         => $head_only,
			CURLOPT_SSL_VERIFYPEER => false,
		) );
		curl_multi_add_handle( $mh, $ch );
		$open[ (int) $ch ] = array( $ch, $url );
	};

	for ( $i = 0; $i < $concurrency; $i++ ) { $add(); }

	do {
		curl_multi_exec( $mh, $running );
		curl_multi_select( $mh, 0.5 );
		while ( $info = curl_multi_info_read( $mh ) ) {
			$ch  = $info['handle'];
			$key = (int) $ch;
			$url = isset( $open[ $key ] ) ? $open[ $key ][1] : '';
			$out[ $url ] = array(
				'code' => (int) curl_getinfo( $ch, CURLINFO_RESPONSE_CODE ),
				'body' => (string) curl_multi_getcontent( $ch ),
			);
			curl_multi_remove_handle( $mh, $ch );
			curl_close( $ch );
			unset( $open[ $key ] );
			$add();
			$running = 1;
		}
	} while ( $running || $open || $queue );

	curl_multi_close( $mh );
	return $out;
}

$res = al_fetch_all( array_keys( $urls ) );

/* ------------------------------------------------------------------- provera */

$bad_code = $bad_h1 = $php_err = array();
$img_urls = array();

foreach ( $urls as $url => $id ) {
	$r = isset( $res[ $url ] ) ? $res[ $url ] : array( 'code' => 0, 'body' => '' );

	if ( 200 !== $r['code'] ) { $bad_code[] = "{$id}  {$r['code']}  {$url}"; continue; }

	$h1 = preg_match_all( '#<h1\b#i', $r['body'] );
	if ( 1 !== $h1 ) { $bad_h1[] = "{$id}  {$h1}×h1  {$url}"; }

	if ( preg_match( '#(Fatal error|Parse error|Warning:|Notice:|Deprecated:)#', $r['body'], $m ) ) {
		$php_err[] = "{$id}  {$m[1]}  {$url}";
	}

	if ( $CHECK_IMG ) {
		if ( preg_match_all( '#<img[^>]+src="([^"]+)"#i', $r['body'], $m1 ) ) {
			foreach ( $m1[1] as $s ) { $img_urls[ $s ] = 1; }
		}
		if ( preg_match_all( '#srcset="([^"]+)"#i', $r['body'], $m2 ) ) {
			foreach ( $m2[1] as $set ) {
				foreach ( explode( ',', $set ) as $part ) {
					$u = trim( strtok( trim( $part ), ' ' ) );
					if ( $u ) { $img_urls[ $u ] = 1; }
				}
			}
		}
		if ( preg_match_all( '#<a[^>]+class="al-lb"[^>]+href="([^"]+)"#i', $r['body'], $m3 ) ) {
			foreach ( $m3[1] as $s ) { $img_urls[ $s ] = 1; }
		}
	}
}

printf( "HTTP ≠200 : %d\n", count( $bad_code ) );
foreach ( $bad_code as $l ) { echo "   {$l}\n"; }
printf( "≠1×<h1>   : %d\n", count( $bad_h1 ) );
foreach ( $bad_h1 as $l ) { echo "   {$l}\n"; }
printf( "PHP greške: %d\n", count( $php_err ) );
foreach ( $php_err as $l ) { echo "   {$l}\n"; }

if ( $CHECK_IMG ) {
	$img_urls = array_keys( array_filter( $img_urls, function ( $k ) {
		return strpos( $k, 'http' ) === 0 && strpos( $k, 'data:' ) !== 0;
	}, ARRAY_FILTER_USE_KEY ) );

	echo "\nSlika za proveru: " . count( $img_urls ) . "\n";
	$ires = al_fetch_all( $img_urls, 12, true );
	$bad  = array();
	foreach ( $img_urls as $u ) {
		$c = isset( $ires[ $u ] ) ? $ires[ $u ]['code'] : 0;
		if ( 200 !== $c ) { $bad[] = "{$c}  {$u}"; }
	}
	printf( "Slike ≠200: %d\n", count( $bad ) );
	foreach ( array_slice( $bad, 0, 40 ) as $l ) { echo "   {$l}\n"; }
	if ( count( $bad ) > 40 ) { echo '   … i još ' . ( count( $bad ) - 40 ) . "\n"; }
}

echo "\nGotovo.\n";
