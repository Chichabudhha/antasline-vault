<?php
/**
 * Uklanjanje ikonica IZ MENIJA (samo meni — ikonice na stranicama ostaju).
 *
 * Briše WoodMart "Menu item icon" meta sa nav_menu_item stavki:
 *   _thumbnail_id, _menu_item_image-type, _menu_item_icon
 * Attachment-i u uploads/meni-ikonice/ i SVG fajlovi u child temi se NE diraju
 * (namena-*.svg se koriste i drugde na sajtu).
 *
 * Proba:      eval-file job-meni-ukloni-ikonice.php
 * Izvršenje:  eval-file job-meni-ukloni-ikonice.php apply
 */
$apply = in_array( 'apply', $args, true );

global $wpdb;
$ids = $wpdb->get_col(
	"SELECT DISTINCT p.ID FROM {$wpdb->posts} p
	 JOIN {$wpdb->postmeta} pm ON pm.post_id = p.ID
	 WHERE p.post_type = 'nav_menu_item'
	   AND pm.meta_key IN ( '_thumbnail_id', '_menu_item_image-type', '_menu_item_icon' )"
);

echo '=== NAĐENO === stavki menija sa ikonicom: ' . count( $ids ) . "\n";

if ( ! $apply ) {
	echo "(bez 'apply' — ništa nije obrisano)\n";
	return;
}

$obrisano = 0;
foreach ( $ids as $id ) {
	delete_post_meta( $id, '_thumbnail_id' );
	delete_post_meta( $id, '_menu_item_image-type' );
	delete_post_meta( $id, '_menu_item_icon' );
	$obrisano++;
}

wp_cache_flush();
echo "=== OČIŠĆENO === $obrisano stavki\n";
