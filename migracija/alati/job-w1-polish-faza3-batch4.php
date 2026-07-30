<?php
/**
 * W1 Polish Faza 3, batch 4 (2026-07-30): 5 postova iz preostalih 15
 * (svi 0 GSC klikova 90d, top po impresijama): 5276, 5181, 2622, 3388, 16615.
 *
 * 🔴 Nalaz koji potvrđuje trend iz batch 2/3: NIJEDAN post u ovom batch-u
 * nema "Kratak odgovor"/ad-hoc CTA pattern — izvorni "Faza 3" obim
 * (GEO-intro/CTA-box retrofit) je suštinski iscrpljen posle batch 1.
 * Preostali rad je opšte QA/bugfix (root-relativni linkovi, tipfeleri u
 * link-tekstu), ne retrofit klasa. Beleženo u DNEVNIK/red čekanja.
 *
 *  - 5276 (podloge-za-krovove): 2 tipfelera u vidljivom tekstu —
 *    "Bero Elite" -> "Bergo Elite" (brend ime u link tekstu),
 *    "krovovoe" -> "krovove"
 *  - 5181, 16615: verifikovano čisti, BEZ IZMENA
 *  - 2622, 3388: root-relativni link `href="/industrijski-podovi/"` (404 na
 *    lokalu bez /antasline/ prefiksa) — isti bag kao batch 2/3. 3388 već ima
 *    ISPRAVNO omotan `<script>` JSON-LD (FAQPage) — ne dirati, samo link.
 *
 * php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-w1-polish-faza3-batch4.php          # proba
 * php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-w1-polish-faza3-batch4.php apply    # upis
 */

global $wpdb;
$apply = ( ( $args[0] ?? '' ) === 'apply' );

function al_b4_write( $id, $title, $old_content, $new_content, $apply ) {
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

// ---------- 5276: tipfeleri u vidljivom tekstu ----------
$post = get_post( 5276 );
$c = $post->post_content;
$c2 = al_fix_counts( $c, array(
	'>Bero Elite<' => array( '>Bergo Elite<', 1 ),
	'krovovoe'     => array( 'krovove', 1 ),
), 5276 );
al_b4_write( 5276, $post->post_title, $c, $c2, $apply );

// ---------- 5181, 16615: verifikovano čisti ----------
foreach ( array( 5181, 16615 ) as $id ) {
	$post = get_post( $id );
	al_b4_write( $id, $post->post_title, $post->post_content, $post->post_content, $apply );
}

// ---------- 2622: root-relative link fix ----------
$post = get_post( 2622 );
$c = $post->post_content;
$c2 = al_fix_counts( $c, array(
	'href="/industrijski-podovi/"' => array( 'href="http://localhost/antasline/industrijski-podovi/"', 1 ),
), 2622 );
al_b4_write( 2622, $post->post_title, $c, $c2, $apply );

// ---------- 3388: root-relative link fix (JSON-LD već ispravno omotan, ne dirati) ----------
$post = get_post( 3388 );
$c = $post->post_content;
$c2 = al_fix_counts( $c, array(
	'href="/industrijski-podovi/"' => array( 'href="http://localhost/antasline/industrijski-podovi/"', 1 ),
), 3388 );
al_b4_write( 3388, $post->post_title, $c, $c2, $apply );

echo $apply ? "\nUpisano.\n" : "\nProba završena, ništa upisano (pokreni sa 'apply').\n";
