<?php
/**
 * W1 Polish Faza 3, batch 2 (2026-07-30): retrofit al-geo-intro/al-cta-box
 * na 5 postova (GSC-osveženi redosled: 16611, 5637, 2641, 16609, 4318).
 *
 * Za razliku od batch 1, ovi postovi nemaju uniformno "Kratak odgovor" +
 * ad-hoc #EEF3F8 CTA box pattern — svaki post je proveren pojedinačno:
 *  - 16611 (pop-tenis): ima GEO intro, nema ad-hoc CTA box → samo intro wrap
 *  - 5637 (podovi-za-radionice): nema GEO intro, ima MRTVE dugme-klase
 *    (.btn.btn-primary, isti obrazac kao zn_contact_submit u batch1) →
 *    zatvarajući CTA odeljak dobija al-cta-box, dugme al-btn
 *  - 2641 (pvc-podne-ploce-ili-gumeni-podovi): nema GEO/CTA ad-hoc pattern,
 *    ali ima POKVAREN root-relativan link (href="/industrijski-podovi/" —
 *    na lokalu 404, jer WP živi u /antasline/ podfolderu, ne u korenu;
 *    izmereno curl-om) → samo link fix
 *  - 16609 (koji-pod-postaviti-u-garazu): nema ništa od gornjeg, svi linkovi
 *    već ispravni → BEZ IZMENA (verifikovano, ne preskočeno)
 *  - 4318 (podloga-za-odbojkaske-terene): GEO intro + isti root-relativni
 *    link bag kao 2641 (5 pojava, 4 različite putanje)
 *
 * Upis isključivo preko $wpdb->update (gotcha #9 / F7.24 pravilo — 16611 i
 * 4318 imaju <script> JSON-LD FAQPage, wp_update_post() bi rizikovao da
 * pokvari escape kao na 2298).
 *
 * php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-w1-polish-faza3-batch2.php          # proba
 * php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-w1-polish-faza3-batch2.php apply    # upis
 */

global $wpdb;
$apply = ( ( $args[0] ?? '' ) === 'apply' );

function al_b2_write( $id, $title, $old_content, $new_content, $apply ) {
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

function al_wrap_p_as_div( $content, $needle_start, $class ) {
	$start = strpos( $content, $needle_start );
	if ( $start === false ) {
		echo "  ⚠️ needle not found: {$needle_start}\n";
		return array( $content, false );
	}
	$end = strpos( $content, '</p>', $start );
	if ( $end === false ) {
		echo "  ⚠️ closing </p> not found after needle\n";
		return array( $content, false );
	}
	$end += 4; // include </p>
	$block = substr( $content, $start, $end - $start );
	$inner = substr( $block, 3, -4 ); // strip leading <p> (3) and trailing </p> (4)
	$new_block = '<div class="' . $class . '">' . $inner . '</div>';
	$content = substr_replace( $content, $new_block, $start, $end - $start );
	return array( $content, true );
}

// ---------- 16611: Padel tenis ----------
$post = get_post( 16611 );
$c = $post->post_content;
list( $c2, $ok ) = al_wrap_p_as_div( $c, '<p><strong>Kratak odgovor:</strong> padel teren je dug 20', 'al-geo-intro' );
al_b2_write( 16611, $post->post_title, $c, $c2, $apply );

// ---------- 5637: Podovi za radionice ----------
$post = get_post( 5637 );
$c = $post->post_content;
$h2_pos = strpos( $c, '<h2>Vreme je da pod dobije tuning' );
$section_end = strpos( $c, '</section><section class="comments-invite">' );
if ( $h2_pos === false || $section_end === false ) {
	echo "  ⚠️ 5637: anchors not found (h2={$h2_pos}, end={$section_end})\n";
	$c2 = $c;
} else {
	$h2_close = strpos( $c, '</h2>', $h2_pos ) + 5;
	$body = substr( $c, $h2_close, $section_end - $h2_close );
	$btn_count = substr_count( $body, 'class="btn btn-primary"' );
	if ( $btn_count !== 1 ) {
		echo "  ⚠️ 5637: expected 1x 'btn btn-primary', found {$btn_count}\n";
		$c2 = $c;
	} else {
		$body = str_replace( 'class="btn btn-primary"', 'class="al-btn"', $body );
		$new_body = '<div class="al-cta-box">' . $body . '</div>';
		$c2 = substr_replace( $c, $new_body, $h2_close, $section_end - $h2_close );
	}
}
al_b2_write( 5637, $post->post_title, $c, $c2, $apply );

// ---------- 2641: PVC podne ploče ili Gumeni podovi ----------
$post = get_post( 2641 );
$c = $post->post_content;
$old = 'href="/industrijski-podovi/"';
$new = 'href="http://localhost/antasline/industrijski-podovi/"';
$count = substr_count( $c, $old );
if ( $count !== 1 ) {
	echo "  ⚠️ 2641: expected 1x '{$old}', found {$count}\n";
	$c2 = $c;
} else {
	$c2 = str_replace( $old, $new, $c );
}
al_b2_write( 2641, $post->post_title, $c, $c2, $apply );

// ---------- 16609: Koji pod postaviti u garažu ----------
// Verifikovano: nema GEO intro / ad-hoc CTA / dead klase / pokvarene linkove — bez izmena.
$post = get_post( 16609 );
al_b2_write( 16609, $post->post_title, $post->post_content, $post->post_content, $apply );

// ---------- 4318: Odbojkaški teren — dimenzije i podloga ----------
$post = get_post( 4318 );
$c = $post->post_content;
list( $c2, $ok ) = al_wrap_p_as_div( $c, '<p><strong>Kratak odgovor:</strong> za odbojkaški teren', 'al-geo-intro' );

$link_fixes = array(
	'href="/lvt-podovi-za-komercijalne-i-javne-prostore/"' => array( 'href="http://localhost/antasline/lvt-podovi-za-komercijalne-i-javne-prostore/"', 1 ),
	'href="/sportski-podovi-za-sale-i-balone/"'             => array( 'href="http://localhost/antasline/sportski-podovi-za-sale-i-balone/"', 1 ),
	'href="/sportske-podloge/"'                              => array( 'href="http://localhost/antasline/sportske-podloge/"', 2 ),
	'href="/">antasline.com</a>'                              => array( 'href="http://localhost/antasline/">antasline.com</a>', 1 ),
);
foreach ( $link_fixes as $old => $spec ) {
	list( $new, $expected ) = $spec;
	$count = substr_count( $c2, $old );
	if ( $count !== $expected ) {
		echo "  ⚠️ 4318: expected {$expected}x '{$old}', found {$count}\n";
		continue;
	}
	$c2 = str_replace( $old, $new, $c2 );
}
al_b2_write( 4318, $post->post_title, $c, $c2, $apply );

echo $apply ? "\nUpisano.\n" : "\nProba završena, ništa upisano (pokreni sa 'apply').\n";
