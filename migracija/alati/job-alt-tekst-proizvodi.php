<?php
/**
 * Alt tekst za 76/94 proizvoda bez alt-a na glavnoj (_thumbnail_id) slici —
 * red čekanja od 07-30 Lighthouse a11y plana ("van obima te ture, poseban
 * budući zadatak"). Alt = post_title (mehanički, bezbedan minimum — ne
 * izmišlja se boja/materijal koji nije verifikovan na fotografiji, za
 * razliku od ranijih ručno kuririsanih alt-ova kao "Bergo Excellence —
 * ploča za brodske palube"). Ne dira postojeći alt (samo prazne/NULL).
 *
 * php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-alt-tekst-proizvodi.php          # proba
 * php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-alt-tekst-proizvodi.php apply    # upis
 */

$apply = ( ( $args[0] ?? '' ) === 'apply' );

$q = new WP_Query( array(
	'post_type'      => 'product',
	'post_status'    => 'publish',
	'posts_per_page' => -1,
	'fields'         => 'ids',
) );

$total = 0;
$fixed = 0;
$skipped_no_thumb = 0;

foreach ( $q->posts as $pid ) {
	$total++;
	$thumb_id = get_post_thumbnail_id( $pid );
	if ( ! $thumb_id ) {
		$skipped_no_thumb++;
		continue;
	}
	$existing = get_post_meta( $thumb_id, '_wp_attachment_image_alt', true );
	if ( $existing !== '' ) {
		continue;
	}
	$title = get_the_title( $pid );
	echo "#{$pid} (att {$thumb_id}): \"\" -> \"{$title}\"\n";
	$fixed++;
	if ( $apply ) {
		update_post_meta( $thumb_id, '_wp_attachment_image_alt', $title );
	}
}

echo "\nUkupno proizvoda: {$total} | bez thumbnail-a: {$skipped_no_thumb} | " . ( $apply ? 'upisano' : 'za upis' ) . ": {$fixed}\n";
if ( ! $apply ) { echo "Proba gotova, ništa upisano.\n"; }
