<?php
/**
 * 2298: FAQPage JSON-LD ima neeskejpovan " unutar teksta odgovora.
 *
 * 🔴 Pravi uzrok (2026-07-30, otkriven POSLE prvog pokušaja): original je
 * imao ISPRAVNO eskejpovan `\"` u JSON bloku (potvrđeno iz mysql dump-a
 * pre bilo kakve izmene — prikazan kao `\\"` jer mysql CLI duplira stvarni
 * backslash u tab-output prikazu). Prvi prolaz W1 Polish Faza 3 batch 1
 * (2698/2542/2699/5170/6588 preko wp_update_post()) je nenamerno pokvario
 * OVAJ već ispravan escape na 2298 — wp_update_post() zove wp_unslash() nad
 * CELIM post_content-om pre upisa, ne samo nad delom koji se menja, pa je
 * postojeći `\"` postao `"` iako ga nijedan str_replace cilj nije dirao.
 * Isti razlog zašto je i PRVI pokušaj ove skripte (preko wp_update_post)
 * tiho promašio — dodati `\"` je opet skinut istim unshlash prolazom.
 * Fix ide direktno kroz $wpdb->update (gotcha #9 iz woodmart-sabloni.md),
 * koji ne zove wp_unslash.
 */
$apply = ( ( $args[0] ?? '' ) === 'apply' );

global $wpdb;
$content = $wpdb->get_var( $wpdb->prepare( "SELECT post_content FROM {$wpdb->posts} WHERE ID=%d", 2298 ) );

$old = '"text":"Da. Bergo ploče se spajaju klik sistemom po principu „uradi sam" — od alata je potreban samo gumeni čekić, a ako ima ukrajanja i ubodna testera. Uz teren dobijate skicu po kojoj se ploče i ekspanzije pravilno ređaju."}}';
$new = '"text":"Da. Bergo ploče se spajaju klik sistemom po principu „uradi sam\" — od alata je potreban samo gumeni čekić, a ako ima ukrajanja i ubodna testera. Uz teren dobijate skicu po kojoj se ploče i ekspanzije pravilno ređaju."}}';

$count = substr_count( $content, $old );
echo "count: {$count}\n";

if ( $count === 1 ) {
	$content = str_replace( $old, $new, $content );

	if ( preg_match( '#<script type="application/ld\+json">(.*?)</script>#s', $content, $m ) ) {
		json_decode( $m[1] );
		echo 'json_last_error (pre-save check): ' . json_last_error_msg() . "\n";
	}

	if ( $apply ) {
		$wpdb->update( $wpdb->posts, array( 'post_content' => $content ), array( 'ID' => 2298 ) );
		clean_post_cache( 2298 );
		echo "upisano (preko \$wpdb->update)\n";
	}
} else {
	echo "SKIP — expected 1 match, found {$count}\n";
}
