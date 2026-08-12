<?php
/**
 * 15793 (/zastitne-podloge-za-travu-i-plocnike/) — prva sekcija posle heroja bila
 * je gola gomila `<p>` bez `al-label` + `<h2>` otvarača (jedina takva među
 * stranicama u standardu), a slika u njoj je bila inline-stilovana na 480px iz
 * izvora 576×359 (mutna i vizuelno "odlepljena" levo).
 *
 * Menja se:
 *  - dodat `al-label` + `h2.al-display--lg` otvarač sekcije
 *  - slika zamenjena većim izvorom (1240×823) i normalizovana bez inline max-width
 *
 * Proba:      wp eval-file job-15793-body-polish.php
 * Izvršenje:  wp eval-file job-15793-body-polish.php apply
 */
$apply = in_array( 'apply', $args, true );
$id    = 15793;
$base  = 'http://localhost/antasline';
$up    = $base . '/wp-content/uploads';

$post = get_post( $id );
$c    = $post->post_content;
$novo = $c;

/* 1. otvarač sekcije */
$sidro  = '[vc_column_text]<p>Podloga Bergo SOLID';
$zamena = '[vc_column_text]<span class="al-label">Šta je</span><h2 class="al-display--lg">Bergo Solid — zaštita trave i pločnika</h2><p>Podloga Bergo SOLID';
if ( false !== strpos( $novo, $sidro ) && false === strpos( $novo, 'Bergo Solid — zaštita trave' ) ) {
	$novo = str_replace( $sidro, $zamena, $novo );
} else {
	echo "⏭ otvarač sekcije: sidro nije nađeno ili je već dodat\n";
}

/* 2. slika u telu */
$stara_slika = '<img src="' . $up . '/2022/03/podloga-na-travi-za-kamion.jpg" alt="Kamionska podloga za travu" style="max-width:480px;border-radius:8px;margin-top:16px" />';
$nova_slika  = '<img src="' . $up . '/2025/12/bergo-solid-na-gradilistu-.webp" alt="Bergo Solid podloga postavljena na gradilištu preko zemlje" style="width:100%;height:auto;border-radius:8px;margin-top:24px" />';
if ( false !== strpos( $novo, $stara_slika ) ) {
	$novo = str_replace( $stara_slika, $nova_slika, $novo );
} else {
	echo "⏭ slika u telu: sidro nije nađeno (verovatno već zamenjena)\n";
}

echo "sadržaj: " . strlen( $c ) . ' → ' . strlen( $novo ) . " bajtova\n";
echo 'H1: ' . substr_count( $novo, '<h1' ) . ' · H2: ' . substr_count( $novo, '<h2' ) . ' · H4: ' . substr_count( $novo, '<h4' ) . "\n";

if ( ! $apply ) {
	echo "\n(bez 'apply' — ništa nije upisano)\n";
	return;
}
if ( $novo === $c ) {
	echo "nema izmena\n";
	return;
}
$r = wp_update_post( array( 'ID' => $id, 'post_content' => $novo ), true );
echo is_wp_error( $r ) ? '🔴 ' . $r->get_error_message() . "\n" : "✅ upisano\n";
