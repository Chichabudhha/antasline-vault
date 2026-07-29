<?php
/**
 * W7 F2.3 (dopuna) — hub 16667 dobija link ka novoj Simplay stranici.
 *
 * Bez ovoga bi 17252 bila 27. „siroče" stranica (26 gotovih al-* stranica bez ijednog
 * inbound linka je nalaz iz W7 dijagnoze — v. plan §Usput nađeno). Kartice u sekciji
 * „EXPONA program" po odluci M vode na PROIZVODE, pa se pod-stranice moraju linkovati
 * iz proze — isti obrazac koji stranica već koristi za Flow.
 *
 * Poziv: php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-w7f2-simplay-link.php [apply]
 */

$APPLY = in_array( 'apply', $args, true );
$BASE  = 'http://localhost/antasline';
$LVT   = $BASE . '/lvt-podovi-za-komercijalne-i-javne-prostore';

kses_remove_filters();

$post = get_post( 16667 );
$c    = $post->post_content;
$orig = $c;

$anchor = 'Pogledajte detaljnu specifikaciju na stranici <a href="' . $LVT . '/vinil-podovi-objectflor/">EXPONA Flow</a>.';
$new    = 'Pogledajte detaljnu specifikaciju na stranicama <a href="' . $LVT . '/vinil-podovi-objectflor/">EXPONA Flow</a>, '
	. '<a href="' . $LVT . '/expona-click/">Expona Click</a>, '
	. '<a href="' . $LVT . '/vinil-podovi/">Expona Commercial</a> i '
	. '<a href="' . $LVT . '/expona-simplay/">EXPONA Simplay 19dB</a>.';

if ( false === strpos( $c, $anchor ) ) {
	WP_CLI::error( 'sidro nije nađeno na 16667 — sadržaj se promenio, proveriti ručno' );
}
$c = str_replace( $anchor, $new, $c );

WP_CLI::log( sprintf( '16667: %d → %d bajtova; linkova ka pod-stranicama u prozi: 1 → 4', strlen( $orig ), strlen( $c ) ) );

if ( $APPLY ) {
	$bk = 'C:/Users/Miroslav/AppData/Local/Temp/al-content-backup';
	if ( ! is_dir( $bk ) ) { mkdir( $bk, 0777, true ); }
	file_put_contents( $bk . '/16667-link-' . date( 'Ymd-His' ) . '.html', $orig );
	wp_update_post( array( 'ID' => 16667, 'post_content' => $c ) );
	WP_CLI::success( 'upisano' );
} else {
	WP_CLI::success( 'PROBA — ništa nije upisano (dodaj `apply`)' );
}
