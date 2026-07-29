<?php
echo "=== stavke sa praznim post_title ===\n";
$items = wp_get_nav_menu_items( 389 );
$map   = array();
foreach ( $items as $it ) { $map[ (int) $it->ID ] = $it; }
foreach ( array( 17286, 17287, 17288, 17306, 17319, 17320, 17321, 17324, 17345, 17346, 17347 ) as $id ) {
	$obj = get_post_meta( $id, '_menu_item_object_id', true );
	$p   = get_post( $obj );
	echo sprintf( "  item %-6d → %-6d\n     post_title stranice : \"%s\"\n     renderovana labela  : \"%s\"\n", $id, $obj, $p->post_title, isset( $map[ $id ] ) ? $map[ $id ]->title : '???' );
}

echo "\n=== Yoast breadcrumb podešavanje ===\n";
$opt = get_option( 'wpseo_titles' );
echo '  breadcrumbs-enable: ' . var_export( isset( $opt['breadcrumbs-enable'] ) ? $opt['breadcrumbs-enable'] : null, true ) . "\n";
echo '  yoast_breadcrumb postoji: ' . ( function_exists( 'yoast_breadcrumb' ) ? 'da' : 'ne' ) . "\n";
echo '  WoodMart breadcrumb opcija: ' . var_export( woodmart_get_opt( 'page_title_breadcrumbs' ), true ) . "\n";

echo "\n=== <head> 15580 — robots/canonical ===\n";
$r = wp_remote_get( get_permalink( 15580 ), array( 'timeout' => 25 ) );
$h = wp_remote_retrieve_body( $r );
if ( preg_match( '#<head.*?</head>#is', $h, $m ) ) {
	preg_match_all( '#<meta[^>]*(robots|description)[^>]*>|<link[^>]*canonical[^>]*>#i', $m[0], $mm );
	echo '  ' . ( $mm[0] ? implode( "\n  ", $mm[0] ) : '(nema robots/canonical/description tagova)' ) . "\n";
	echo '  Yoast blok prisutan: ' . ( strpos( $m[0], 'Yoast' ) !== false ? 'da' : 'NE' ) . "\n";
}
echo "\n=== poredjenje: <head> 16589 (normalna stranica) ===\n";
$r2 = wp_remote_get( get_permalink( 16589 ), array( 'timeout' => 25 ) );
$h2 = wp_remote_retrieve_body( $r2 );
if ( preg_match( '#<head.*?</head>#is', $h2, $m2 ) ) {
	preg_match_all( '#<meta[^>]*(robots|description)[^>]*>|<link[^>]*canonical[^>]*>#i', $m2[0], $mm2 );
	echo '  ' . ( $mm2[0] ? implode( "\n  ", $mm2[0] ) : '(nema)' ) . "\n";
	echo '  Yoast blok prisutan: ' . ( strpos( $m2[0], 'Yoast' ) !== false ? 'da' : 'NE' ) . "\n";
}
