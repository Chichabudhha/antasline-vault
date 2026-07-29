<?php
/** W7 F3 — regeneracija Yoast indexable hijerarhije za 4 stranice sa nepotpunim breadcrumb lancem. */
$apply = in_array( 'apply', $args, true );
global $wpdb;

$ids = array( 16664, 16671, 17018, 17020 );
$t_i = $wpdb->prefix . 'yoast_indexable';
$t_h = $wpdb->prefix . 'yoast_indexable_hierarchy';

echo "=== STANJE PRE ===\n";
foreach ( $ids as $id ) {
	$ind = $wpdb->get_row( $wpdb->prepare( "SELECT id, object_id, ancestors_are_set FROM $t_i WHERE object_id=%d AND object_type='post'", $id ) );
	if ( ! $ind ) { echo "  $id: nema indexable red\n"; continue; }
	$h = $wpdb->get_results( $wpdb->prepare( "SELECT ancestor_id, depth FROM $t_h WHERE indexable_id=%d ORDER BY depth", $ind->id ) );
	$s = array();
	foreach ( $h as $row ) { $s[] = "{$row->ancestor_id}(d{$row->depth})"; }
	echo sprintf( "  %-6d indexable_id=%-6d ancestors_are_set=%s  hijerarhija: %s\n", $id, $ind->id, $ind->ancestors_are_set, $s ? implode( ' ', $s ) : '(prazno)' );
}

// poređenje sa stranicom koja RADI
$ok  = 17017;
$ind = $wpdb->get_row( $wpdb->prepare( "SELECT id, ancestors_are_set FROM $t_i WHERE object_id=%d AND object_type='post'", $ok ) );
$h   = $wpdb->get_results( $wpdb->prepare( "SELECT ancestor_id, depth FROM $t_h WHERE indexable_id=%d ORDER BY depth", $ind->id ) );
$s   = array();
foreach ( $h as $row ) { $s[] = "{$row->ancestor_id}(d{$row->depth})"; }
echo "\n  kontrola $ok (breadcrumb ispravan): ancestors_are_set={$ind->ancestors_are_set} hijerarhija: " . ( $s ? implode( ' ', $s ) : '(prazno)' ) . "\n";

if ( ! $apply ) { echo "\n(bez 'apply' — ništa nije upisano)\n"; return; }

foreach ( $ids as $id ) {
	$ind = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM $t_i WHERE object_id=%d AND object_type='post'", $id ) );
	if ( $ind ) {
		$wpdb->delete( $t_h, array( 'indexable_id' => $ind->id ) );
		$wpdb->delete( $t_i, array( 'id' => $ind->id ) );
	}
	clean_post_cache( $id );
	echo "  ✅ $id: indexable + hijerarhija obrisani (Yoast ih regeneriše pri prvom pregledu)\n";
}
