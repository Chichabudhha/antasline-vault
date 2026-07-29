<?php
/**
 * W7 F3.5 — 15580 „Podloge za parking" → noindex, 301 na 16589 (odluka M 2026-07-28).
 * Proba: eval-file job-w7f3-15580.php   Izvršenje: ... apply
 */
$apply = in_array( 'apply', $args, true );

echo "=== POREĐENJE YOAST (plan tvrdi da je 15580 bolji) ===\n";
foreach ( array( 15580, 16589 ) as $id ) {
	echo "$id  title: " . ( get_post_meta( $id, '_yoast_wpseo_title', true ) ?: '—' ) . "\n";
	echo "      desc : " . ( get_post_meta( $id, '_yoast_wpseo_metadesc', true ) ?: '—' ) . "\n";
	echo "      noindex: " . ( get_post_meta( $id, '_yoast_wpseo_meta-robots-noindex', true ) ?: '(prazno = index)' ) . "\n";
}

echo "\n=== DOLAZNI LINKOVI ka /podloge-za-parking/ u sadržaju ===\n";
global $wpdb;
$hits = $wpdb->get_results(
	"SELECT ID, post_type, post_title FROM {$wpdb->posts}
	 WHERE post_status='publish' AND post_type IN ('page','post','product')
	   AND post_content LIKE '%/podloge-za-parking/%' AND ID <> 15580"
);
if ( $hits ) {
	foreach ( $hits as $h ) { echo "  {$h->ID} [{$h->post_type}] {$h->post_title}\n"; }
} else {
	echo "  nema — nijedna stranica ne linkuje 15580 iz sadržaja ✅\n";
}

echo "\n=== GALERIJA na 15580 (sadržaj koji bi 301 sklonio s vidika) ===\n";
$c = get_post_field( 'post_content', 15580 );
preg_match_all( '#<img[^>]+src="([^"]+)"#i', apply_filters( 'the_content', $c ), $m );
echo '  slika u sadržaju: ' . count( $m[1] ) . " | reči: " . str_word_count( wp_strip_all_tags( $c ) ) . "\n";
$c2 = get_post_field( 'post_content', 16589 );
preg_match_all( '#<img[^>]+src="([^"]+)"#i', apply_filters( 'the_content', $c2 ), $m2 );
echo '  16589 slika: ' . count( $m2[1] ) . " | reči: " . str_word_count( wp_strip_all_tags( $c2 ) ) . "\n";

if ( ! $apply ) { echo "\n(bez 'apply' — ništa nije upisano)\n"; return; }

// Bekap sadržaja stranica čije linkove menjamo.
$dir = 'C:/Projekti/antasline-vault/scratchpad/content-backup';
if ( ! is_dir( $dir ) ) { mkdir( $dir, 0777, true ); }

$stari = home_url( '/podloge-za-parking/' );
$novi  = get_permalink( 16589 );
foreach ( $hits as $h ) {
	$c = get_post_field( 'post_content', $h->ID );
	file_put_contents( "$dir/{$h->ID}-pre-w7f3-2026-07-29.txt", $c );
	$n = str_replace( $stari, $novi, $c );
	if ( $n === $c ) { echo "  ⚠️  {$h->ID}: link nije pronađen u tačnom obliku, preskočeno\n"; continue; }
	$wpdb->update( $wpdb->posts, array( 'post_content' => $n ), array( 'ID' => $h->ID ) );
	clean_post_cache( $h->ID );
	echo "  ✅ {$h->ID}: link prevezan na 16589 (" . substr_count( $c, $stari ) . "×)\n";
}

update_post_meta( 15580, '_yoast_wpseo_meta-robots-noindex', '1' );
$wpdb->delete( $wpdb->prefix . 'yoast_indexable', array( 'object_id' => 15580, 'object_type' => 'post' ) );
echo "\n✅ 15580 → noindex (Yoast indexable keš obrisan, regeneriše se sam)\n";
echo "ℹ️  Yoast title/metadesc NIJE prenet na 16589 — v. poređenje iznad.\n";
