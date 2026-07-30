<?php
/**
 * W2 #10 piklbol — title/meta refresh na /teren-za-pickleball/ (ID 16616).
 *
 * Blokirano od 2026-07-08 (fake-review Product schema pitanje) do 2026-07-28
 * (M odluka: vratiti FAQPage+Product BEZ aggregateRating/recenzija — v.
 * PROGRESS Blokeri). Time je title/meta refresh odblokiran, ali nikad
 * izvršen (nalaz iz W5 5.4 izveštaja 2026-07-30: piklbol 160 impr/0 klikova,
 * 28d). Nova stranica /piklbol/ i dalje NAMERNO preskočena (kanibalizacija) —
 * ovo je refresh postojeće, ne nova stranica.
 *
 * Yoast title je bio PRAZAN (padao na WP/tema default). Novi title/meta
 * prati stil već primenjen na 16688/2699/4318 (direktan odgovor u title-u,
 * cena/CTA u opisu). Ništa izmišljeno: dimenzije (13,4×6,1 m, mreža 86 cm)
 * i naziv podloge su iz POSTOJEĆEG sadržaja stranice; cena ostaje "na upit"
 * (Bergo Ultimate FLOV — M odluka, cenovnik prazan za taj model), pa nije
 * ni pominjana u meta opisu.
 *
 * php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-piklbol-title-refresh.php          # proba
 * php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-piklbol-title-refresh.php apply    # upis
 */

global $wpdb;
$apply = ( ( $args[0] ?? '' ) === 'apply' );

$id = 16616;
$new_title = 'Teren za piklbol (pickleball) — dimenzije i sportska podloga';
$new_desc  = 'Teren za piklbol: dimenzije 13,4×6,1 m, mreža 86 cm. Bergo Ultimate FLOV™ sportska podloga za teren. Ponuda: 069 234 00 72.';

$old_title = get_post_meta( $id, '_yoast_wpseo_title', true );
$old_desc  = get_post_meta( $id, '_yoast_wpseo_metadesc', true );

echo "Title: '{$old_title}' -> '{$new_title}' (" . mb_strlen( $new_title ) . " chars)\n";
echo "Desc:  '{$old_desc}'\n   ->  '{$new_desc}' (" . mb_strlen( $new_desc ) . " chars)\n";

if ( $apply ) {
	update_post_meta( $id, '_yoast_wpseo_title', $new_title );
	update_post_meta( $id, '_yoast_wpseo_metadesc', $new_desc );
	clean_post_cache( $id );
	$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}yoast_indexable WHERE object_id = %d", $id ) );
	echo "\nUpisano.\n";
} else {
	echo "\nProba završena, ništa upisano (pokreni sa 'apply').\n";
}
