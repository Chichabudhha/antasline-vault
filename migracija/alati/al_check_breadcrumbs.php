<?php
/** W7 F3 — BreadcrumbList schema vs stvarni post_parent lanac, za sve ugnježdene stranice. */
global $wpdb;

$deca = get_posts( array(
	'post_type'   => 'page',
	'post_status' => 'publish',
	'numberposts' => -1,
	'orderby'     => 'ID',
	'order'       => 'ASC',
) );

$lose = 0; $ok = 0;
foreach ( $deca as $p ) {
	if ( ! $p->post_parent ) { continue; }

	// očekivan lanac iz post_parent-a
	$lanac = array();
	$cur   = $p;
	while ( $cur->post_parent ) {
		$cur = get_post( $cur->post_parent );
		if ( ! $cur ) { break; }
		array_unshift( $lanac, $cur->ID );
	}

	$r = wp_remote_get( get_permalink( $p->ID ), array( 'timeout' => 25 ) );
	if ( is_wp_error( $r ) ) { echo "  {$p->ID} greška fetch\n"; continue; }
	$h = wp_remote_retrieve_body( $r );

	$imena = array();
	if ( preg_match_all( '#<script type="application/ld\+json"[^>]*>(.*?)</script>#s', $h, $mm ) ) {
		foreach ( $mm[1] as $j ) {
			$d = json_decode( $j, true );
			if ( ! is_array( $d ) ) { continue; }
			$graf = isset( $d['@graph'] ) ? $d['@graph'] : array( $d );
			foreach ( $graf as $n ) {
				if ( isset( $n['@type'] ) && 'BreadcrumbList' === $n['@type'] ) {
					foreach ( $n['itemListElement'] as $i ) { $imena[] = $i['name']; }
				}
			}
		}
	}

	// očekivano: Početna + svaki predak + sama stranica
	$ocekivano = 1 + count( $lanac ) + 1;
	$stvarno   = count( $imena );
	if ( $stvarno < $ocekivano ) {
		$lose++;
		$preci = array();
		foreach ( $lanac as $a ) { $preci[] = "$a"; }
		echo sprintf( "  🔴 %-6d %-46s preci=[%s] očekivano %d koraka, u schemi %d\n     schema: %s\n",
			$p->ID, $p->post_name, implode( ',', $preci ), $ocekivano, $stvarno, implode( ' > ', $imena ) );
	} else {
		$ok++;
	}
}
echo "\nugnježdenih stranica: " . ( $ok + $lose ) . " | lanac tačan: $ok | nepotpun: $lose\n";
