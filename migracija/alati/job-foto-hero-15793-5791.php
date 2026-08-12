<?php
/**
 * Foto-hero za stranice koje su imale al-* sekcije ali ravan navy hero bez slike
 * (15793 Bergo Solid, 5791 podovi za štale) — 57 od 71 stranice već koristi
 * `al-hero-photo` + `css=""` pozadinu, ove dve su ostale na praznom plavom bloku
 * i zato "izgledaju staro".
 *
 * Mehanizam (isti kao 16673/17026):
 *   1. row dobija klasu `al-hero-photo` + `vc_custom_heroF4<ID>`
 *   2. `css=""` atribut u shortcode-u
 *   3. pravilo se DODAJE u `_wpb_shortcodes_custom_css` post meta — bez toga
 *      WPBakery ne emituje pozadinu na front-endu (potvrđeno na 5119)
 *
 * Uz to: na 15793 se `<h4>Karakteristike:</h4>` podiže na `<h3>` — bio je jedini
 * H4 na stranici, u paru sa `<h3>Primena:</h3>` u istom gridu (preskočen nivo).
 *
 * Proba:      wp eval-file job-foto-hero-15793-5791.php
 * Izvršenje:  wp eval-file job-foto-hero-15793-5791.php apply
 */
$apply = in_array( 'apply', $args, true );
$base  = 'http://localhost/antasline';
$up    = $base . '/wp-content/uploads';

$mapa = array(
	15793 => $up . '/2026/07/kamion-na-bergo-solid-podlozi.webp',
	5791  => $up . '/2025/12/paddock-i-stable-podloge-za-stale-na-travi.webp',
);

foreach ( $mapa as $id => $bg ) {
	$post = get_post( $id );
	if ( ! $post ) {
		echo "🔴 $id — ne postoji\n";
		continue;
	}
	$putanja = str_replace( $base . '/', ABSPATH, $bg );
	if ( ! file_exists( $putanja ) ) {
		echo "🔴 $id — nema slike: $bg\n";
		continue;
	}

	$c = $post->post_content;
	if ( false !== strpos( $c, 'al-hero-photo' ) ) {
		echo "⏭ $id — već ima foto-hero\n";
		continue;
	}

	$klasa = 'vc_custom_heroF4' . $id;
	$novo  = preg_replace(
		'/^\[vc_row full_width="stretch_row" el_class="al-section al-section--navy /',
		'[vc_row full_width="stretch_row" el_class="al-section al-section--navy al-hero-photo ',
		$c,
		1
	);
	if ( $novo === $c ) {
		echo "🔴 $id — hero row nije prepoznat, preskačem\n";
		continue;
	}
	/* css atribut ide na isti (prvi) row, odmah posle el_class="..." */
	$novo = preg_replace(
		'/^(\[vc_row full_width="stretch_row" el_class="[^"]*")/',
		'$1 css=".' . $klasa . '{background-image: url(\'' . $bg . '\') !important;}"',
		$novo,
		1
	);

	if ( 15793 === $id ) {
		$novo = str_replace( '<h4>Karakteristike:</h4>', '<h3>Karakteristike:</h3>', $novo );
	}

	$pravilo = '.' . $klasa . "{background-image: url('$bg') !important;}";
	$stari_css = get_post_meta( $id, '_wpb_shortcodes_custom_css', true );

	echo "== $id " . $post->post_name . "\n";
	echo '   hero bg: ' . basename( $bg ) . "\n";
	echo '   sadržaj: ' . strlen( $c ) . ' → ' . strlen( $novo ) . " bajtova\n";

	if ( ! $apply ) {
		continue;
	}

	$r = wp_update_post( array( 'ID' => $id, 'post_content' => $novo ), true );
	if ( is_wp_error( $r ) ) {
		echo '   🔴 ' . $r->get_error_message() . "\n";
		continue;
	}
	update_post_meta( $id, '_wpb_shortcodes_custom_css', trim( $stari_css . "\n" . $pravilo ) );
	echo "   ✅ upisano\n";
}

if ( ! $apply ) {
	echo "\n(bez 'apply' — ništa nije upisano)\n";
}
