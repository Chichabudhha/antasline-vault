<?php
/**
 * W7 F3 — 4 lokalne duplikat-stranice (LOKAL-NOVO, bez live parnjaka) → noindex
 * + interni linkovi prevezani na jaču verziju. Isti obrazac kao 15580→16589 i
 * 16613→6588 (2026-07-30). Nema redirect-mapa-FINAL reda jer nijedna od ove
 * 4 nikad nije postojala na live-u — nema šta da se redirektuje na dan migracije,
 * noindex je dovoljan (stranica ostaje dostupna direktno preko URL-a, samo van
 * navigacije i van indeksa).
 *
 * M odluka 2026-07-30: "stavi jače verzije".
 *
 * php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-w7f3-4-duplikata-konsolidacija.php          # proba
 * php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-w7f3-4-duplikata-konsolidacija.php apply    # upis
 */

global $wpdb;
$apply = ( ( $args[0] ?? '' ) === 'apply' );

$pairs = array(
	5754  => 17028, // Izgradnja terena za tenis -> Sportski podovi za teniske terene
	5769  => 16665, // Podne obloge za promocije i sajmove -> Bergo Easy
	5512  => 16667, // Podovi za poslovni prostor -> LVT podovi za komercijalne i javne prostore
	16171 => 16674, // Galerija sportskih terena -> Galerija
);

$dir = 'C:/Projekti/antasline-vault/scratchpad/content-backup';
if ( ! is_dir( $dir ) ) { mkdir( $dir, 0777, true ); }

foreach ( $pairs as $old_id => $new_id ) {
	$old = get_post( $old_id );
	$new = get_post( $new_id );
	if ( ! $old || ! $new ) { echo "🔴 {$old_id}/{$new_id}: nedostaje post\n\n"; continue; }

	$old_url = home_url( '/' . $old->post_name . '/' );
	$new_url = get_permalink( $new_id );
	echo "── {$old_id} \"{$old->post_title}\" → {$new_id} \"{$new->post_title}\"\n";
	echo "   {$old_url} → {$new_url}\n";
	echo "   trenutni noindex: " . ( get_post_meta( $old_id, '_yoast_wpseo_meta-robots-noindex', true ) ?: '(prazno = index)' ) . "\n";

	$hits = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT ID, post_type, post_title FROM {$wpdb->posts}
			 WHERE post_status='publish' AND post_type IN ('page','post','product')
			   AND post_content LIKE %s AND ID <> %d",
			'%' . $wpdb->esc_like( '/' . $old->post_name . '/' ) . '%',
			$old_id
		)
	);
	if ( $hits ) {
		foreach ( $hits as $h ) { echo "   dolazni link: {$h->ID} [{$h->post_type}] {$h->post_title}\n"; }
	} else {
		echo "   dolazni linkovi: nema\n";
	}

	if ( $apply ) {
		foreach ( $hits as $h ) {
			$c = get_post_field( 'post_content', $h->ID );
			file_put_contents( "$dir/{$h->ID}-pre-{$old_id}-konsolidacija-2026-07-30.txt", $c );
			$n = str_replace( $old_url, $new_url, $c );
			if ( $n === $c ) { echo "   ⚠️ {$h->ID}: link nije pronađen u tačnom obliku, preskočeno\n"; continue; }
			$wpdb->update( $wpdb->posts, array( 'post_content' => $n ), array( 'ID' => $h->ID ) );
			clean_post_cache( $h->ID );
			echo "   ✅ {$h->ID}: link prevezan (" . substr_count( $c, $old_url ) . "×)\n";
		}
		update_post_meta( $old_id, '_yoast_wpseo_meta-robots-noindex', '1' );
		$wpdb->delete( $wpdb->prefix . 'yoast_indexable', array( 'object_id' => $old_id, 'object_type' => 'post' ) );
		echo "   ✅ {$old_id} → noindex (Yoast indexable keš obrisan)\n";
	}
	echo "\n";
}

echo $apply ? "Gotovo.\n" : "Proba gotova, ništa upisano (pokreni sa 'apply').\n";
