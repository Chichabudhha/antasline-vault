<?php
/**
 * W1 Polish Faza 3, batch 3 (2026-07-30): 5 postova (GSC top iz preostalih
 * 20: 16613, 16612, 16616, 3398, 3318 — brojke iz iste sesije, batch2 fetch).
 *
 * Nijedan od ova 5 nema "Kratak odgovor"/ad-hoc CTA obrazac iz batch 1/2:
 *  - 16613, 16612: verifikovano čisti, BEZ IZMENA
 *  - 16616 (teren-za-pickleball): 🔴🔴 NAJVEĆI nalaz cele Faze 3 —
 *    FAQPage+Product JSON-LD (dodat 2026-07-28 posle fake-review čišćenja)
 *    NIKAD nije bio omotan u <script>, pa se ceo blok (5 FAQ + Product,
 *    ~90 redova sirovog JSON-a) renderuje kao VIDLJIV TEKST na dnu stranice
 *    (potvrđeno u Chrome-u, wptexturize je pretvorio "" u „" pošto tekst
 *    nije bio u <script>/<pre>) — schema u međuvremenu NIJE ni funkcionisala
 *    kao structured data (Google ne čita plain tekst). Van izvornog obima
 *    Faze 3 (GEO-intro/CTA-box), ali prioritetan bag. Usput: tel: link sa
 *    razmacima (`tel:+381 69 234 00 72`, nevalidan po RFC 3966, nekonzistentan
 *    sa ostatkom sajta) → normalizovan bez razmaka.
 *  - 3398, 3318: isti root-relativni link bag kao batch 2
 *    (`href="/slug/"` bez `/antasline/` prefiksa → 404 na lokalu)
 *
 * php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-w1-polish-faza3-batch3.php          # proba
 * php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-w1-polish-faza3-batch3.php apply    # upis
 */

global $wpdb;
$apply = ( ( $args[0] ?? '' ) === 'apply' );

function al_b3_write( $id, $title, $old_content, $new_content, $apply ) {
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

// ---------- 16613, 16612: verifikovano čisti ----------
foreach ( array( 16613, 16612 ) as $id ) {
	$post = get_post( $id );
	al_b3_write( $id, $post->post_title, $post->post_content, $post->post_content, $apply );
}

// ---------- 16616: bare JSON-LD -> <script>, tel: link fix ----------
$post = get_post( 16616 );
$c = $post->post_content;

$embed_marker = '[embed]https://www.youtube.com/watch?v=rD1O3R9B0Sw[/embed]';
$embed_pos = strpos( $c, $embed_marker );
if ( $embed_pos === false ) {
	echo "  ⚠️ 16616: embed marker not found\n";
	$c2 = $c;
} else {
	$json_start = strpos( $c, '{', $embed_pos + strlen( $embed_marker ) );
	if ( $json_start === false ) {
		echo "  ⚠️ 16616: JSON start not found after embed\n";
		$c2 = $c;
	} else {
		$json_text = trim( substr( $c, $json_start ) );
		$decoded   = json_decode( $json_text );
		if ( json_last_error() !== JSON_ERROR_NONE ) {
			echo "  ⚠️ 16616: bare JSON does not decode: " . json_last_error_msg() . "\n";
			$c2 = $c;
		} else {
			$c2 = substr( $c, 0, $json_start ) . '<script type="application/ld+json">' . $json_text . '</script>';
		}
	}
}
$tel_old = 'href="tel:+381 69 234 00 72"';
$tel_new = 'href="tel:+381692340072"';
$tel_count = substr_count( $c2, $tel_old );
if ( $tel_count !== 1 ) {
	echo "  ⚠️ 16616: expected 1x tel link with spaces, found {$tel_count}\n";
} else {
	$c2 = str_replace( $tel_old, $tel_new, $c2 );
}
al_b3_write( 16616, $post->post_title, $c, $c2, $apply );

// ---------- 3398: root-relative link fix ----------
$post = get_post( 3398 );
$c = $post->post_content;
$old = 'href="/zastitne-podloge-za-travu-i-plocnike/"';
$new = 'href="http://localhost/antasline/zastitne-podloge-za-travu-i-plocnike/"';
$count = substr_count( $c, $old );
if ( $count !== 2 ) {
	echo "  ⚠️ 3398: expected 2x '{$old}', found {$count}\n";
	$c2 = $c;
} else {
	$c2 = str_replace( $old, $new, $c );
}
al_b3_write( 3398, $post->post_title, $c, $c2, $apply );

// ---------- 3318: root-relative link fix (2 different links) ----------
$post = get_post( 3318 );
$c = $post->post_content;
$c2 = $c;
$link_fixes = array(
	'href="/antistatik-i-elektroprovodljivi-podovi/"'        => array( 'href="http://localhost/antasline/antistatik-i-elektroprovodljivi-podovi/"', 1 ),
	'href="/lvt-podovi-za-komercijalne-i-javne-prostore/"'    => array( 'href="http://localhost/antasline/lvt-podovi-za-komercijalne-i-javne-prostore/"', 1 ),
);
foreach ( $link_fixes as $old => $spec ) {
	list( $new, $expected ) = $spec;
	$count = substr_count( $c2, $old );
	if ( $count !== $expected ) {
		echo "  ⚠️ 3318: expected {$expected}x '{$old}', found {$count}\n";
		continue;
	}
	$c2 = str_replace( $old, $new, $c2 );
}
al_b3_write( 3318, $post->post_title, $c, $c2, $apply );

echo $apply ? "\nUpisano.\n" : "\nProba završena, ništa upisano (pokreni sa 'apply').\n";
