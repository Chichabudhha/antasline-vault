<?php
/** W7 F3 — provera po planu: naslovi, 26 siročadi, breadcrumb. */

$loc     = get_nav_menu_locations();
$menu_id = (int) $loc['main-menu'];
echo "aktivan main-menu: term $menu_id (" . wp_get_nav_menu_object( $menu_id )->name . ")\n\n";

echo "=== 1. Stavke bez naslova ===\n";
$items = wp_get_nav_menu_items( $menu_id );
$bez   = 0;
foreach ( $items as $it ) {
	$raw = get_post_field( 'post_title', $it->ID );
	if ( trim( $raw ) === '' ) { $bez++; echo "  PRAZAN: {$it->ID}\n"; }
	if ( ( 'page' === $it->object || 'post' === $it->object ) && 'publish' !== get_post_status( $it->object_id ) ) {
		echo "  🔴 cilj nije publish: {$it->title} → {$it->object_id}\n";
	}
}
echo 'stavki: ' . count( $items ) . " | bez naslova: $bez " . ( $bez ? '🔴' : '✅' ) . "\n";

echo "\n=== 2. Ranije siročad (26 al-* stranica) ===\n";
$siročad = array( 5119, 5512, 5754, 5769, 15480, 15580, 16171, 16581, 16582, 16583, 16584, 16585, 16586, 16600, 16675, 16677, 16683, 16686, 16687, 16688, 16736, 16873, 16874, 16875, 16876, 17004, 17017, 17018, 17019, 17020, 17025, 17026, 17028, 17029, 17252 );
$u_meniju = array();
foreach ( $items as $it ) { if ( 'page' === $it->object ) { $u_meniju[] = (int) $it->object_id; } }
$ok = 0; $ne = array();
foreach ( $siročad as $id ) {
	if ( in_array( $id, $u_meniju, true ) ) { $ok++; } else { $ne[] = $id; }
}
echo "ušlo u meni: $ok | ostalo van: " . count( $ne ) . "\n";
foreach ( $ne as $id ) { echo sprintf( "  %-6d %s\n", $id, get_post_field( 'post_name', $id ) ); }

echo "\n=== 3. Yoast breadcrumb (3 uzorka iz različitih grupa) ===\n";
foreach ( array( 16657 => 'Sport', 17020 => 'Industrija', 17252 => 'Poslovni' ) as $id => $grupa ) {
	$html = function_exists( 'yoast_breadcrumb' ) ? yoast_breadcrumb( '', '', false ) : '';
	// breadcrumb zavisi od globalnog upita — hvatamo ga kroz stvarnu stranicu
	$body = wp_remote_get( get_permalink( $id ), array( 'timeout' => 20 ) );
	$b    = is_wp_error( $body ) ? '' : wp_remote_retrieve_body( $body );
	if ( preg_match( '#<(p|nav|div)[^>]*(id|class)="[^"]*breadcrumb[^"]*"[^>]*>(.*?)</\1>#is', $b, $m ) ) {
		$txt = trim( preg_replace( '/\s+/', ' ', wp_strip_all_tags( $m[3] ) ) );
		echo sprintf( "  %-6d [%-10s] %s\n", $id, $grupa, mb_substr( $txt, 0, 110 ) );
	} else {
		echo sprintf( "  %-6d [%-10s] breadcrumb nije nađen u HTML-u\n", $id, $grupa );
	}
}

echo "\n=== 4. Novi Cene hub ===\n";
$cene = get_page_by_path( 'cene' );
echo '  ID ' . $cene->ID . ' → ' . get_permalink( $cene->ID ) . ' | title_off=' . get_post_meta( $cene->ID, '_woodmart_title_off', true ) . ' | layout=' . get_post_meta( $cene->ID, '_woodmart_main_layout', true ) . "\n";

echo "\n=== 5. 15580 ===\n";
echo '  noindex meta: ' . ( get_post_meta( 15580, '_yoast_wpseo_meta-robots-noindex', true ) ?: 'NEMA' ) . "\n";
$r = wp_remote_get( get_permalink( 15580 ), array( 'timeout' => 20 ) );
$h = is_wp_error( $r ) ? '' : wp_remote_retrieve_body( $r );
echo '  robots u <head>: ' . ( preg_match( '#<meta name="robots" content="([^"]*)"#i', $h, $m ) ? $m[1] : '—' ) . "\n";
