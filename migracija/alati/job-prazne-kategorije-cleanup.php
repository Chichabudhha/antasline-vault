<?php
/**
 * Brisanje 5 praznih kategorija (M odluka 2026-07-30, W7 F2 bloker stavka 2).
 *
 * basta(58), Trava u boji(60), Podloge za bazene(61), Poslovni prostor(65),
 * Specijalni podovi(138) — 0 objavljenih postova u sve 5 (verifikovano
 * COUNT(p.ID) sa post_status='publish' JOIN uslovom, ne cache-ovana `count`
 * kolona). Relacije koje postoje su isključivo drafts iz već mrtvih legacy
 * CPT-ova (spoljne-podne-obloge/vestacka-trava, ugašeni public/rewrite
 * filterom u W7 F2.9) i attachment prilozi — brisanje termina ne dira
 * nijedan živi/objavljeni sadržaj. Nijedna nije u parity-inventar.csv.
 *
 * php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-prazne-kategorije-cleanup.php          # proba
 * php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-prazne-kategorije-cleanup.php apply    # upis
 */

$apply = ( ( $args[0] ?? '' ) === 'apply' );

$ids = array( 58, 60, 61, 65, 138 );

foreach ( $ids as $id ) {
	$term = get_term( $id, 'category' );
	if ( ! $term || is_wp_error( $term ) ) {
		echo "⚠️ term {$id} ne postoji ili nije category, preskačem\n";
		continue;
	}
	echo "{$id} \"{$term->name}\" (cache count={$term->count})";
	if ( $apply ) {
		$result = wp_delete_term( $id, 'category' );
		echo $result && ! is_wp_error( $result ) ? " → obrisano\n" : " → GREŠKA: " . ( is_wp_error( $result ) ? $result->get_error_message() : 'nepoznato' ) . "\n";
	} else {
		echo " → (proba, nije obrisano)\n";
	}
}

echo $apply ? "\nGotovo.\n" : "\nProba završena, ništa upisano (pokreni sa 'apply').\n";
