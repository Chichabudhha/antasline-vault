<?php
/**
 * W7 F3 — inspekcija stanja menija i siročadi.
 * php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file inspect_menu.php
 */

echo "=== LOKACIJE MENIJA ===\n";
$locations = get_nav_menu_locations();
print_r( $locations );
echo "registrovane lokacije teme: " . implode( ', ', array_keys( get_registered_nav_menus() ) ) . "\n";

echo "\n=== SVI MENIJI ===\n";
foreach ( wp_get_nav_menus() as $m ) {
	echo sprintf( "term %-4d %-30s stavki: %d\n", $m->term_id, $m->name, $m->count );
}

$active = isset( $locations['main-menu'] ) ? (int) $locations['main-menu'] : 0;
echo "\n=== AKTIVAN MENI (main-menu = term $active) ===\n";

foreach ( wp_get_nav_menus() as $m ) {
	$items = wp_get_nav_menu_items( $m->term_id, array( 'post_status' => 'publish,draft' ) );
	if ( ! $items ) { echo "\n--- term {$m->term_id} {$m->name}: PRAZAN\n"; continue; }
	echo "\n--- term {$m->term_id} \"{$m->name}\" (" . count( $items ) . " stavki) ---\n";
	// build depth map
	$by_id = array();
	foreach ( $items as $it ) { $by_id[ $it->ID ] = $it; }
	foreach ( $items as $it ) {
		$depth = 0; $p = (int) $it->menu_item_parent;
		while ( $p && isset( $by_id[ $p ] ) && $depth < 6 ) { $depth++; $p = (int) $by_id[ $p ]->menu_item_parent; }
		$title = trim( $it->title );
		$flag  = '';
		if ( $title === '' ) { $flag .= ' [PRAZAN NASLOV]'; }
		if ( $it->object === 'page' || $it->object === 'post' ) {
			$st = get_post_status( $it->object_id );
			if ( $st !== 'publish' ) { $flag .= " [cilj:$st]"; }
			if ( ! get_post( $it->object_id ) ) { $flag .= ' [CILJ NE POSTOJI]'; }
		}
		echo sprintf(
			"%3d| %s%-38s  type=%-12s obj=%-10s obj_id=%-6s url=%s%s\n",
			$it->menu_order,
			str_repeat( '    ', $depth ),
			$title === '' ? '(prazno)' : $title,
			$it->type,
			$it->object,
			$it->object_id,
			$it->url,
			$flag
		);
	}
	// rupe u menu_order
	$orders = wp_list_pluck( $items, 'menu_order' );
	sort( $orders );
	$missing = array();
	for ( $i = 1; $i <= max( $orders ); $i++ ) { if ( ! in_array( $i, $orders, true ) ) { $missing[] = $i; } }
	$dupes = array_keys( array_filter( array_count_values( $orders ), function ( $c ) { return $c > 1; } ) );
	echo "menu_order rupe: " . ( $missing ? implode( ',', $missing ) : '—' ) . " | duplikati: " . ( $dupes ? implode( ',', $dupes ) : '—' ) . "\n";
}
