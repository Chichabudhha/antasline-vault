<?php
/** W7 F3 — profil sumnjivih parova (H1, broj reči, Yoast, parity) pre odluke o mestu u meniju. */

$ids = array( 5754, 17028, 5769, 16665, 5512, 16667, 16683, 16171, 16674, 16660, 16687, 5455, 16673, 5119, 15480, 15580, 16589, 5791, 15793, 16682 );

foreach ( $ids as $id ) {
	$p = get_post( $id );
	if ( ! $p ) { echo "$id NE POSTOJI\n"; continue; }
	$c = apply_filters( 'the_content', $p->post_content );
	$words = str_word_count( wp_strip_all_tags( $c ), 0, 'ČĆŽŠĐčćžšđ' );
	preg_match_all( '#<h1[^>]*>(.*?)</h1>#is', $c, $h1 );
	preg_match_all( '#<h2[^>]*>(.*?)</h2>#is', $c, $h2 );
	$yt = get_post_meta( $id, '_yoast_wpseo_title', true );
	$ym = get_post_meta( $id, '_yoast_wpseo_metadesc', true );
	$thumb = get_post_thumbnail_id( $id );
	echo str_repeat( '=', 78 ) . "\n";
	echo "ID $id  [{$p->post_status}]  parent={$p->post_parent}  slug={$p->post_name}\n";
	echo "  post_title : {$p->post_title}\n";
	echo "  reči       : $words   thumb: " . ( $thumb ? $thumb : 'NEMA' ) . "\n";
	echo "  H1 u sadr. : " . ( $h1[1] ? implode( ' | ', array_map( 'wp_strip_all_tags', $h1[1] ) ) : '—' ) . "\n";
	echo "  H2         : " . mb_substr( implode( ' · ', array_map( 'wp_strip_all_tags', array_slice( $h2[1], 0, 8 ) ) ), 0, 240 ) . "\n";
	echo "  Yoast title: " . ( $yt ?: '—' ) . "\n";
	echo "  Yoast desc : " . mb_substr( $ym ?: '—', 0, 130 ) . "\n";
}
