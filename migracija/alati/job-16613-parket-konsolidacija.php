<?php
/**
 * Duplikat "Šta postaviti preko starog parketa ili pločica" — 16613 → noindex,
 * interni linkovi prevezani na 6588, 301 unos za dan migracije.
 *
 * Odluka (2026-07-30, GSC presek): 6588 (lokalno /-2/ slug) na produkciji
 * dominira klaster (3.353 impr/258 kl/poz 5,5) nad 16613-ekvivalentom
 * (1.667 impr/84 kl/poz 7,6) — jasna kanibalizacija istog upita. 6588 je i
 * sadržajno bogatiji (671 reči, 6 kuriranih fotki, ispravan Yoast title),
 * 16613 stariji (382 reči) sa nezamenjenim Yoast šablonom
 * "PVC podovi i podovi od vinila %%sep%% %%sitename%%" (v. PROGRESS Blokeri
 * W7 F2 stavka 3).
 *
 * Isti obrazac kao 15580→16589 (scratchpad/w7f3/job-w7f3-15580.php):
 * noindex + interni linkovi prevezani + 301 red u redirect-mapa-FINAL na dan
 * migracije (live se ne dira sada).
 *
 * php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-16613-parket-konsolidacija.php          # proba
 * php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-16613-parket-konsolidacija.php apply    # upis
 */

global $wpdb;
$apply = ( ( $args[0] ?? '' ) === 'apply' );

$old_id = 16613;
$new_id = 6588;

echo "=== Poređenje Yoast ===\n";
foreach ( array( $old_id, $new_id ) as $id ) {
	echo "$id  title: " . ( get_post_meta( $id, '_yoast_wpseo_title', true ) ?: '—' ) . "\n";
	echo "     desc : " . ( get_post_meta( $id, '_yoast_wpseo_metadesc', true ) ?: '—' ) . "\n";
	echo "     noindex: " . ( get_post_meta( $id, '_yoast_wpseo_meta-robots-noindex', true ) ?: '(prazno = index)' ) . "\n";
}

$old_url = home_url( '/sta-postaviti-preko-starog-parketa-ili-plocica/' );
$new_url = get_permalink( $new_id );
echo "\nStari URL: {$old_url}\nNovi URL:  {$new_url}\n";

echo "\n=== Dolazni linkovi ka {$old_url} u sadržaju (van 16613 samog) ===\n";
$hits = $wpdb->get_results(
	$wpdb->prepare(
		"SELECT ID, post_type, post_title FROM {$wpdb->posts}
		 WHERE post_status='publish' AND post_type IN ('page','post','product')
		   AND post_content LIKE %s AND ID <> %d",
		'%' . $wpdb->esc_like( '/sta-postaviti-preko-starog-parketa-ili-plocica/' ) . '%',
		$old_id
	)
);
if ( $hits ) {
	foreach ( $hits as $h ) { echo "  {$h->ID} [{$h->post_type}] {$h->post_title}\n"; }
} else {
	echo "  nema — nijedna druga stranica ne linkuje 16613 iz sadržaja\n";
}

if ( ! $apply ) {
	echo "\nProba završena, ništa upisano (pokreni sa 'apply').\n";
	return;
}

$dir = 'C:/Projekti/antasline-vault/scratchpad/content-backup';
if ( ! is_dir( $dir ) ) { mkdir( $dir, 0777, true ); }

foreach ( $hits as $h ) {
	$c = get_post_field( 'post_content', $h->ID );
	file_put_contents( "$dir/{$h->ID}-pre-16613-konsolidacija-2026-07-30.txt", $c );
	$n = str_replace( $old_url, $new_url, $c );
	if ( $n === $c ) { echo "  ⚠️ {$h->ID}: link nije pronađen u tačnom obliku, preskočeno\n"; continue; }
	$wpdb->update( $wpdb->posts, array( 'post_content' => $n ), array( 'ID' => $h->ID ) );
	clean_post_cache( $h->ID );
	echo "  ✅ {$h->ID}: link prevezan na {$new_id} (" . substr_count( $c, $old_url ) . "×)\n";
}

update_post_meta( $old_id, '_yoast_wpseo_meta-robots-noindex', '1' );
$wpdb->delete( $wpdb->prefix . 'yoast_indexable', array( 'object_id' => $old_id, 'object_type' => 'post' ) );
echo "\n✅ {$old_id} → noindex (Yoast indexable keš obrisan, regeneriše se sam)\n";
echo "ℹ️ 301 red za dan migracije treba upisati ručno u migracija/redirect-mapa-FINAL.csv\n";
