<?php
/**
 * W7 F3 — sirovi naslovi stavki menija + popis siročadi + provera ciljnih ID-eva iz plana.
 */
global $wpdb;

echo "=== SIROVI post_title stavki menija 67 (prazan = nasleđuje naslov cilja) ===\n";
$rows = $wpdb->get_results(
	"SELECT p.ID, p.post_title, p.menu_order, pm.meta_value AS obj_id, pm2.meta_value AS obj_type
	 FROM {$wpdb->posts} p
	 JOIN {$wpdb->term_relationships} tr ON tr.object_id = p.ID
	 JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id = tr.term_taxonomy_id AND tt.taxonomy='nav_menu'
	 LEFT JOIN {$wpdb->postmeta} pm ON pm.post_id=p.ID AND pm.meta_key='_menu_item_object_id'
	 LEFT JOIN {$wpdb->postmeta} pm2 ON pm2.post_id=p.ID AND pm2.meta_key='_menu_item_object'
	 WHERE p.post_type='nav_menu_item' AND tt.term_id=67
	 ORDER BY p.menu_order"
);
$prazni = 0;
foreach ( $rows as $r ) {
	if ( trim( $r->post_title ) === '' ) {
		$prazni++;
		$t = get_post( $r->obj_id );
		echo sprintf( "  PRAZAN: item %-6d order=%-3d cilj=%s %s -> \"%s\"\n", $r->ID, $r->menu_order, $r->obj_type, $r->obj_id, $t ? $t->post_title : '???' );
	}
}
echo "ukupno stavki: " . count( $rows ) . " | praznih post_title: $prazni\n";

echo "\n=== SVE OBJAVLJENE STRANICE (ID | parent | slug | naslov | u meniju 67?) ===\n";
$in_menu = array();
foreach ( wp_get_nav_menu_items( 67 ) as $it ) {
	if ( $it->object === 'page' || $it->object === 'post' ) { $in_menu[] = (int) $it->object_id; }
}
$pages = get_posts( array( 'post_type' => 'page', 'post_status' => 'publish', 'numberposts' => -1, 'orderby' => 'ID', 'order' => 'ASC' ) );
$orphans = array();
foreach ( $pages as $p ) {
	$flag = in_array( $p->ID, $in_menu, true ) ? '  ' : 'SIROCE';
	if ( $flag === 'SIROCE' ) { $orphans[] = $p->ID; }
	echo sprintf( "%-6s %-6d parent=%-6d %-52s %s\n", $flag, $p->ID, $p->post_parent, $p->post_name, mb_substr( $p->post_title, 0, 46 ) );
}
echo "\nukupno objavljenih stranica: " . count( $pages ) . " | siročadi: " . count( $orphans ) . "\n";
echo "siroče ID-evi: " . implode( ',', $orphans ) . "\n";

echo "\n=== PROVERA CILJNIH ID-eva IZ PLANA F3.3 ===\n";
$plan = array(
	'SPORT' => array( 5438, 16680, 16670, 17028, 16584, 16581, 16582, 16583, 16586, 16585, 16688, 17027, 16676, 16657, 16677, 16674 ),
	'INDUSTRIJA' => array( 16567, 17017, 17018, 16660, 16664, 17020, 16658, 16678, 17026, 16666, 16675, 17025 ),
	'TERASE' => array( 16590, 17019, 16679, 16681, 16659, 16662, 16673, 17029 ),
	'POSLOVNI' => array( 16667, 16684, 16685, 16668, 17252, 16142, 16683, 16686, 16669 ),
	'SPECIJALNI' => array( 16111, 16665, 16589, 16663, 5791, 15793 ),
	'CENE' => array( 16874, 16873, 16876, 16875 ),
);
foreach ( $plan as $grupa => $ids ) {
	echo "\n[$grupa]\n";
	foreach ( $ids as $id ) {
		$p = get_post( $id );
		if ( ! $p ) { echo sprintf( "  %-6d 🔴 NE POSTOJI\n", $id ); continue; }
		echo sprintf( "  %-6d %-9s parent=%-6d %-46s %s\n", $id, $p->post_status, $p->post_parent, $p->post_name, mb_substr( $p->post_title, 0, 40 ) );
	}
}
