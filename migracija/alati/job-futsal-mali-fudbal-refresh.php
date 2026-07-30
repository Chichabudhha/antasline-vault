<?php
/**
 * W2 quick-win — "mali fudbal" sinonim nedostajao na futsal stranici (ID 16581).
 *
 * Nalaz iz GSC signal provere 2026-07-30: upit "dimenzije terena za mali
 * fudbal" (152 impr/90d, poz. 21.8, 0 klikova) je praktično nevidljiv iako
 * stranica /podloge-za-futsal-terene/ već sadrži tačne dimenzije (38-42 x
 * 18-22 m) — samo pod imenom "futsal", bez kolokvijalnog sinonima "mali
 * fudbal" ijednom u Yoast title/meta/sadržaju (izmereno: 0 pojava).
 *
 * Ništa izmišljeno: dimenzije/materijali/cena-na-upit ostaju identični
 * postojećem sadržaju, samo je sinonim dodat na mesta gde je "futsal" već
 * bio prisutan (hero label, H2 naslov dimenzija, prvo FAQ pitanje).
 *
 * php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-futsal-mali-fudbal-refresh.php          # proba
 * php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-futsal-mali-fudbal-refresh.php apply    # upis
 */

global $wpdb;
$apply = ( ( $args[0] ?? '' ) === 'apply' );

$id = 16581;

// ---------- Yoast title/meta ----------
$old_title = get_post_meta( $id, '_yoast_wpseo_title', true );
$old_desc  = get_post_meta( $id, '_yoast_wpseo_metadesc', true );

$new_title = 'Futsal (mali fudbal) teren — dimenzije i podloga | Antas Line';
$new_desc  = 'Futsal (mali fudbal): dimenzije terena 38–42 × 18–22 m. Naxos Evolution za salu ili Bergo Ultimate za otvoren teren. Ponuda: 069 234 00 72.';

echo "Title: '{$old_title}'\n   ->  '{$new_title}' (" . mb_strlen( $new_title ) . " chars)\n";
echo "Desc:  '{$old_desc}'\n   ->  '{$new_desc}' (" . mb_strlen( $new_desc ) . " chars)\n\n";

// ---------- Sadržaj: dodaj sinonim na 3 mesta gde je "futsal" već prisutan ----------
$post = get_post( $id );
$c    = $post->post_content;

$content_fixes = array(
	'<span class="al-label">Futsal</span>' => '<span class="al-label">Futsal / Mali fudbal</span>',
	'<h2 class="al-display--lg">Dimenzije futsal terena i tehnički podaci</h2>' => '<h2 class="al-display--lg">Dimenzije futsal (mali fudbal) terena i tehnički podaci</h2>',
	'<h3>Koje su dimenzije futsal terena?</h3>' => '<h3>Koje su dimenzije terena za mali fudbal (futsal)?</h3>',
);

$c2 = $c;
foreach ( $content_fixes as $old => $new ) {
	$count = substr_count( $c2, $old );
	if ( $count !== 1 ) {
		echo "⚠️ expected 1x '{$old}', found {$count} — preskačem ovu izmenu\n";
		continue;
	}
	$c2 = str_replace( $old, $new, $c2 );
	echo "OK: '{$old}' -> '{$new}'\n";
}

$mentions_before = substr_count( strtolower( $c ), 'mali fudbal' );
$mentions_after  = substr_count( strtolower( $c2 ), 'mali fudbal' );
echo "\n'mali fudbal' pojave u sadržaju: {$mentions_before} -> {$mentions_after}\n";

echo "\nSadržaj: " . strlen( $c ) . " -> " . strlen( $c2 ) . " bajtova\n";

if ( $apply ) {
	update_post_meta( $id, '_yoast_wpseo_title', $new_title );
	update_post_meta( $id, '_yoast_wpseo_metadesc', $new_desc );
	if ( $c2 !== $c ) {
		$wpdb->update( $wpdb->posts, array( 'post_content' => $c2 ), array( 'ID' => $id ) );
	}
	clean_post_cache( $id );
	$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}yoast_indexable WHERE object_id = %d", $id ) );
	echo "\nUpisano.\n";
} else {
	echo "\nProba završena, ništa upisano (pokreni sa 'apply').\n";
}
