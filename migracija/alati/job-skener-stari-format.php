<?php
/**
 * Skener: koje objavljene stranice su još u "starom formatu".
 *
 * Novi (WoodMart/antas) format = hero blok `al-hero` + bar jedna `al-section`
 * sekcija u post_content. Stari = goli WPBakery `vc_row/vc_column_text` bez
 * al-* dizajn sistema (Porto-era uvoz).
 *
 * Napomena: `wp db query` sa LIKE '%...%' na Windows-u guta `%`, pa se skeniranje
 * radi u PHP-u nad post_content-om, ne SQL LIKE-om.
 *
 * Pokretanje: wp eval-file job-skener-stari-format.php [posts]
 */
$sa_postovima = in_array( 'posts', $args, true );

global $wpdb;
$tipovi = $sa_postovima ? "'page','post'" : "'page'";
$rows   = $wpdb->get_results(
	"SELECT ID, post_type, post_name, post_title, post_content
	 FROM {$wpdb->posts}
	 WHERE post_type IN ($tipovi) AND post_status = 'publish'
	 ORDER BY post_type, ID"
);

$stari = array();
$novi  = 0;
foreach ( $rows as $r ) {
	$c        = $r->post_content;
	$hero     = false !== strpos( $c, 'al-hero' );
	$sekcija  = false !== strpos( $c, 'al-section' );
	$kartica  = false !== strpos( $c, 'al-card' );
	if ( $hero && $sekcija ) {
		$novi++;
		continue;
	}
	$stari[] = array(
		'id'    => $r->ID,
		'tip'   => $r->post_type,
		'slug'  => $r->post_name,
		'title' => $r->post_title,
		'len'   => strlen( $c ),
		'flags' => ( $hero ? 'hero ' : '' ) . ( $sekcija ? 'section ' : '' ) . ( $kartica ? 'card' : '' ),
	);
}

echo 'Skenirano: ' . count( $rows ) . " | novi format: $novi | stari/nepotpuni: " . count( $stari ) . "\n\n";
echo "ID\ttip\tdužina\tdelimično\tslug\n";
usort( $stari, function ( $a, $b ) { return $b['len'] - $a['len']; } );
foreach ( $stari as $s ) {
	echo $s['id'] . "\t" . $s['tip'] . "\t" . $s['len'] . "\t" . ( $s['flags'] ?: '-' ) . "\t" . $s['slug'] . "\n";
}
