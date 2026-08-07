<?php
/**
 * W1 Polish Faza 3, batch 6 (2026-08-07): mehanički generički .al-cta-box na
 * dnu 22 posta koja NEMAJU postojeći GEO-intro/CTA tekst (batch 2-5 su
 * potvrdili da taj obrazac ne postoji kod njih — ovo NIJE ponavljanje tog
 * rada, nego nova, uža odluka: samo dodati zatvarajući CTA, bez izmišljanja
 * GEO-intro pasusa po M odluci 2026-08-07).
 *
 * 17027 (Dimenzije fudbalskog terena) namerno izostavljen iz liste — već ima
 * pravi al-hero CTA blok na dnu (F6 troslojni šablon), ne treba dupli CTA.
 *
 * 3388 i 16616 imaju JSON-LD <script> u post_content — CTA se ubacuje PRE
 * poslednjeg <script> taga (ne posle), i upis ide isključivo preko
 * $wpdb->update() (F7.24 gotcha: wp_update_post() zove wp_unslash() nad
 * celim post_content-om i kvari postojeće escapovane navodnike u JSON-LD).
 * Ostalih 20 nema <script> u sadržaju — wp_update_post() je bezbedan.
 *
 * wp eval-file job-w1-polish-faza3-batch6-cta-box.php          # proba
 * wp eval-file job-w1-polish-faza3-batch6-cta-box.php apply    # upis
 */

global $wpdb;

$apply = ( ( $args[0] ?? '' ) === 'apply' );

$cta_html = '<div class="al-cta-box"><p>Imate pitanje ili vam treba ponuda za pod? Pozovite <a href="tel:+381692340072">069 234 00 72</a> ili pišite na <a href="mailto:office@antasline.com">office@antasline.com</a>.</p></div>';

$ids_with_script = array( 3388, 16616 );

$ids = array(
	2622, 2641, 3257, 3318, 3388, 3398, 4813, 5163, 5181, 5276, 5411,
	6824, 6874, 16608, 16609, 16610, 16612, 16613, 16614, 16615, 16616, 17021,
);

foreach ( $ids as $id ) {
	$post = get_post( $id );
	if ( ! $post ) {
		echo "SKIP {$id}: not found\n";
		continue;
	}

	$content = $post->post_content;

	if ( strpos( $content, 'al-cta-box' ) !== false ) {
		echo "SKIP {$id} ({$post->post_title}): already has al-cta-box\n";
		continue;
	}

	$has_script = in_array( $id, $ids_with_script, true );

	if ( $has_script ) {
		$pos = strrpos( $content, '<script' );
		if ( $pos === false ) {
			echo "FAIL {$id}: expected <script> not found\n";
			continue;
		}
		$new_content = substr( $content, 0, $pos ) . $cta_html . "\n\n" . substr( $content, $pos );
	} else {
		$new_content = rtrim( $content ) . "\n\n" . $cta_html;
	}

	echo 'OK   ' . ( $has_script ? '[script-safe] ' : '' ) . "{$id} ({$post->post_title})\n";

	if ( $apply ) {
		if ( $has_script ) {
			$wpdb->update(
				$wpdb->posts,
				array( 'post_content' => $new_content ),
				array( 'ID' => $id )
			);
		} else {
			kses_remove_filters();
			wp_update_post( array( 'ID' => $id, 'post_content' => $new_content ) );
		}
		clean_post_cache( $id );
	}
}

echo $apply ? "\nUpisano.\n" : "\nProba završena, ništa upisano (pokreni sa 'apply').\n";
