<?php
/**
 * W7 F3 — 4 reda u yoast_indexable_hierarchy pokazuju na obrisan indexable (id 42),
 * pa Yoast ispušta međukorak „Industrijski podovi" iz BreadcrumbList schema.
 * Brisanje redova → Yoast ih regeneriše iz post_parent-a pri prvom pregledu.
 */
$apply = in_array( 'apply', $args, true );
global $wpdb;

$t_i = 'wpgs_yoast_indexable';
$t_h = 'wpgs_yoast_indexable_hierarchy';

$siroci = $wpdb->get_results(
	"SELECT h.indexable_id, h.ancestor_id, h.depth, i.object_id
	 FROM $t_h h
	 JOIN $t_i i ON i.id = h.indexable_id
	 LEFT JOIN $t_i a ON a.id = h.ancestor_id
	 WHERE a.id IS NULL"
);

echo "=== redovi hijerarhije sa nepostojećim pretkom ===\n";
foreach ( $siroci as $r ) {
	echo sprintf( "  indexable_id=%-5d (stranica %-6d) → ancestor_id=%-5d OBRISAN\n", $r->indexable_id, $r->object_id, $r->ancestor_id );
}
echo 'ukupno: ' . count( $siroci ) . "\n";

if ( ! $apply ) { echo "\n(bez 'apply' — ništa nije upisano)\n"; return; }

foreach ( $siroci as $r ) {
	$wpdb->delete( $t_h, array( 'indexable_id' => $r->indexable_id ) );
	// Obriši i sam indexable da Yoast ponovo izgradi ceo lanac, ne samo rupu.
	$wpdb->delete( $t_i, array( 'id' => $r->indexable_id ) );
	clean_post_cache( $r->object_id );
	echo "  ✅ stranica {$r->object_id}: indexable + hijerarhija obrisani\n";
}
