<?php
/**
 * W1 Polish Faza 3, batch 5/5 (2026-07-30, poslednji): 10 preostalih postova,
 * svi 0 GSC klikova (90d): 5411, 16614, 16608, 5163, 16610, 3257, 4813,
 * 6824, 6874, 17021.
 *
 * Dijagnostika potvrdila trend iz batch 2-4: NIJEDAN post nema
 * GEO-intro/CTA-box obrazac (obim retrofita iz batch 1 ostaje konačan).
 * Svih 10 nosi isti root-relativni link bag kao batch 2-4
 * (href="/slug/" bez /antasline/ prefiksa -> 404 na lokalnom XAMPP-u,
 * radiće ispravno na produkciji gde je koren = koren).
 * Dodatno: 5411 i 3257 imaju nbsp (\xc2\xa0) otpad usred rečenice
 * (vidljiv duplirani razmak), isti obrazac kao batch 1 (5170).
 * "antasline.com" pogodak u 5411/16614 je samo office@antasline.com
 * mailto/tekst adresa — nije live-domen link, ne dira se.
 * Svih 10 ciljnih linkova i 30 slika HEAD-ovano pre pisanja: 0 grešaka.
 *
 * php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-w1-polish-faza3-batch5.php          # proba
 * php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-w1-polish-faza3-batch5.php apply    # upis
 */

global $wpdb;
$apply = ( ( $args[0] ?? '' ) === 'apply' );

function al_b5_write( $id, $title, $old_content, $new_content, $apply ) {
	global $wpdb;
	if ( $old_content === $new_content ) {
		echo "NOOP {$id} ({$title}) — sadržaj nepromenjen\n";
		return;
	}
	echo "DIFF {$id} ({$title}): " . strlen( $old_content ) . " -> " . strlen( $new_content ) . " bajtova\n";
	if ( $apply ) {
		$wpdb->update( $wpdb->posts, array( 'post_content' => $new_content ), array( 'ID' => $id ) );
		clean_post_cache( $id );
		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}yoast_indexable WHERE object_id = %d", $id ) );
	}
}

function al_fix_counts( $c, array $fixes, $label ) {
	foreach ( $fixes as $old => $spec ) {
		list( $new, $expected ) = $spec;
		$count = substr_count( $c, $old );
		if ( $count !== $expected ) {
			echo "  ⚠️ {$label}: expected {$expected}x '{$old}', found {$count}\n";
			continue;
		}
		$c = str_replace( $old, $new, $c );
	}
	return $c;
}

$base = 'http://localhost/antasline';

// ---------- 5411: 2 root-relative linkovi + 11x nbsp otpad ----------
$post = get_post( 5411 );
$c = $post->post_content;
$c2 = al_fix_counts( $c, array(
	'href="/epoksidni-podovi-ili-ecotile-podovi/"' => array( 'href="' . $base . '/epoksidni-podovi-ili-ecotile-podovi/"', 1 ),
	'href="/industrijski-podovi/"'                 => array( 'href="' . $base . '/industrijski-podovi/"', 1 ),
), 5411 );
$nbsp_count = substr_count( $c2, "\xc2\xa0" );
if ( $nbsp_count === 11 ) {
	$c2 = str_replace( "\xc2\xa0", ' ', $c2 );
} else {
	echo "  ⚠️ 5411: expected 11x nbsp, found {$nbsp_count}\n";
}
al_b5_write( 5411, $post->post_title, $c, $c2, $apply );

// ---------- 16614: 1 root-relative link ----------
$post = get_post( 16614 );
$c = $post->post_content;
$c2 = al_fix_counts( $c, array(
	'href="/sportske-podloge/"' => array( 'href="' . $base . '/sportske-podloge/"', 1 ),
), 16614 );
al_b5_write( 16614, $post->post_title, $c, $c2, $apply );

// ---------- 16608: 2 root-relative linkovi ----------
$post = get_post( 16608 );
$c = $post->post_content;
$c2 = al_fix_counts( $c, array(
	'href="/epoksidni-podovi-ili-ecotile-podovi/"' => array( 'href="' . $base . '/epoksidni-podovi-ili-ecotile-podovi/"', 1 ),
	'href="/industrijski-podovi/"'                 => array( 'href="' . $base . '/industrijski-podovi/"', 1 ),
), 16608 );
al_b5_write( 16608, $post->post_title, $c, $c2, $apply );

// ---------- 5163: 1 root-relative link ----------
$post = get_post( 5163 );
$c = $post->post_content;
$c2 = al_fix_counts( $c, array(
	'href="/antistatik-i-elektroprovodljivi-podovi/"' => array( 'href="' . $base . '/antistatik-i-elektroprovodljivi-podovi/"', 1 ),
), 5163 );
al_b5_write( 5163, $post->post_title, $c, $c2, $apply );

// ---------- 16610: 1 root-relative link ----------
$post = get_post( 16610 );
$c = $post->post_content;
$c2 = al_fix_counts( $c, array(
	'href="/sportski-podovi-za-sale-i-balone/"' => array( 'href="' . $base . '/sportski-podovi-za-sale-i-balone/"', 1 ),
), 16610 );
al_b5_write( 16610, $post->post_title, $c, $c2, $apply );

// ---------- 3257: 1 root-relative link + 4x nbsp otpad ----------
$post = get_post( 3257 );
$c = $post->post_content;
$c2 = al_fix_counts( $c, array(
	'href="/industrijski-podovi/"' => array( 'href="' . $base . '/industrijski-podovi/"', 1 ),
), 3257 );
$nbsp_count = substr_count( $c2, "\xc2\xa0" );
if ( $nbsp_count === 4 ) {
	$c2 = str_replace( "\xc2\xa0", ' ', $c2 );
} else {
	echo "  ⚠️ 3257: expected 4x nbsp, found {$nbsp_count}\n";
}
al_b5_write( 3257, $post->post_title, $c, $c2, $apply );

// ---------- 4813: 2 root-relative linkovi ----------
$post = get_post( 4813 );
$c = $post->post_content;
$c2 = al_fix_counts( $c, array(
	'href="/kontakt/"'                          => array( 'href="' . $base . '/kontakt/"', 1 ),
	'href="/sportske-podloge/bergo-ultimate/"'   => array( 'href="' . $base . '/sportske-podloge/bergo-ultimate/"', 1 ),
), 4813 );
al_b5_write( 4813, $post->post_title, $c, $c2, $apply );

// ---------- 6824: 1 root-relative link ----------
$post = get_post( 6824 );
$c = $post->post_content;
$c2 = al_fix_counts( $c, array(
	'href="/podovi-za-radnje-i-maloprodajne-objekte/"' => array( 'href="' . $base . '/podovi-za-radnje-i-maloprodajne-objekte/"', 1 ),
), 6824 );
al_b5_write( 6824, $post->post_title, $c, $c2, $apply );

// ---------- 6874: isti href 3x (mora replace_all, ne al_fix_counts sa expected=1) ----------
$post = get_post( 6874 );
$c = $post->post_content;
$needle = 'href="/antistatik-i-elektroprovodljivi-podovi/"';
$found = substr_count( $c, $needle );
if ( $found === 3 ) {
	$c2 = str_replace( $needle, 'href="' . $base . '/antistatik-i-elektroprovodljivi-podovi/"', $c );
} else {
	echo "  ⚠️ 6874: expected 3x '{$needle}', found {$found}\n";
	$c2 = $c;
}
al_b5_write( 6874, $post->post_title, $c, $c2, $apply );

// ---------- 17021: 1 root-relative link ----------
$post = get_post( 17021 );
$c = $post->post_content;
$c2 = al_fix_counts( $c, array(
	'href="/antistatik-i-elektroprovodljivi-podovi/"' => array( 'href="' . $base . '/antistatik-i-elektroprovodljivi-podovi/"', 1 ),
), 17021 );
al_b5_write( 17021, $post->post_title, $c, $c2, $apply );

echo $apply ? "\nUpisano.\n" : "\nProba završena, ništa upisano (pokreni sa 'apply').\n";
