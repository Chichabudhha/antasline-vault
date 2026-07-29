<?php
/**
 * W7 F3.5 — dizajn-parity za 5791 (podovi-za-stale).
 * Isti tekst, nov al-* omotač (odluka M, 2026-07-28). Uklanja i dupli naslov
 * (nedostajalo _woodmart_title_off) i mrtav [porto_block]/[vc_btn porto] CTA
 * (oba no-op shortcode-a, F7.12 zaključak — renderovala se prazna ljubičasta traka).
 *
 * Poziv: php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-w7f35-podovi-za-stale.php [apply]
 */

$APPLY = in_array( 'apply', (array) ( $args ?? array() ), true );
$ID    = 5791;
$BDIR  = WP_CONTENT_DIR . '/../scratchpad/content-backup';
if ( ! is_dir( $BDIR ) ) { mkdir( $BDIR, 0777, true ); }

echo $APPLY ? "=== REŽIM: APPLY ===\n\n" : "=== REŽIM: PROBA ===\n\n";

$post = get_post( $ID );
if ( ! $post ) { echo "🔴 {$ID}: post ne postoji\n"; return; }

$home = home_url();
$tel  = 'tel:+381692340072';

$mosolut_id  = 16530;
$mosolut_url = get_permalink( $mosolut_id );
$mosolut_img = wp_get_attachment_image_url( get_post_thumbnail_id( $mosolut_id ), 'medium' );
if ( ! $mosolut_url || ! $mosolut_img ) { echo "🔴 proizvod {$mosolut_id} ili slika nedostaje — PREKID\n"; return; }

$content = '[vc_row full_width="stretch_row" el_class="al-section al-section--navy al-plates al-diag-bottom"][vc_column][vc_column_text]
<div class="al-hero"><span class="al-label">Specijalni podovi</span><h1 class="al-display--xl">Gumeni podovi za štale, za konje i poljoprivredne površine</h1><p class="al-hero__sub">Specijalni sistemi podnih ploča za konje i jahališta, dizajnirani za velika opterećenja — podloga za pesak i druge slojeve u arenama za jahanje, halama i na stazama za konje.</p><div class="al-hero__cta"><a class="al-btn" href="' . esc_url( $home . '/kontakt/' ) . '">Zatražite ponudu</a><a class="al-btn al-btn--ghost" href="' . esc_url( $tel ) . '">069 234 00 72</a></div></div>
[/vc_column_text][/vc_column][/vc_row]
[vc_row full_width="stretch_row" el_class="al-section al-section--paper"][vc_column width="1/3"][vc_single_image image="5793" img_size="full" alignment="center"][/vc_column][vc_column width="2/3"][vc_column_text]<p>Specijalni sistemi podnih ploča za konje i jahališta su dizajnirani za velika opterećenja. Ploče služe kao podloga za pesak i druge specijalne slojeve i mogu se koristiti, između ostalog, za arene za jahanje, hale, staze za konje, prelazne staze za konje, trotoare za staze ili trotoare na farmama, kao i za skladištenje itd.</p><p>Podloga nudi životinjama udobnost tokom stajanja na otvorenom ili lako kretanje poljoprivrednim mašinama ili drugih teških vozila. Ploče se lako montiraju i demontiraju.</p>[/vc_column_text][/vc_column][/vc_row]
[vc_row content_placement="middle" full_width="stretch_row" el_class="al-section al-section--mist"][vc_column width="1/2"][vc_column_text]<h3>Prednosti</h3><ul><li>Odlična toplotna izolacija</li><li>Visoka sposobnost ploča protiv klizanja</li><li>Hemijska otpornost</li><li>Lako održavanje i čišćenje</li><li>Brza montaža i demontaža</li><li>Veoma dobra drenaža vode</li></ul>[/vc_column_text][/vc_column][vc_column width="1/2"][vc_column_text]<h3>Gumeni podovi za štale</h3><p>Gumeni podovi za štale su pogodne za razne poljoprivredne svrhe, kao i za stabilizaciju terena ili za vožnju teških vozila. Služe kao osnovni sloj za pesak i druge posebne slojeve i mogu se koristiti, između ostalog, za arene za jahanje, zatvorene arene za jahanje, staze za konje, prelazne staze za konje, trotoare staza ili trotoara na farmama kao i skladišne prostore.</p><p>Veoma su efikasna alternativa tradicionalnim betonskim, drvenim ili prljavim podlogama i nude mnoge prednosti.</p>[/vc_column_text][/vc_column][/vc_row]
[vc_row full_width="stretch_row" el_class="al-section al-section--paper"][vc_column][vc_column_text]<span class="al-label">Modeli</span><h2 class="al-display--lg">Stable i Paddock podloge</h2>[/vc_column_text][/vc_column][/vc_row]
[vc_row full_width="stretch_row" el_class="al-section al-section--paper"][vc_column width="1/2"][vc_column_text]<h3>Stable podloge</h3><p>Podloga je namenjena za velika opterećenja do 60 tona/m². Ploče služe kao osnovni sloj za pesak i druge posebne slojeve.</p><ul><li>Arene za jahanje</li><li>zatvorene arene za jahanje</li><li>staze za konje</li><li>prelazne staze za konje</li><li>trotoari staza na farmama</li><li>skladišta</li></ul><h4>Specifikacija</h4><ul><li><strong>Materijal:</strong> 100% reciklirani PE &amp; PP</li><li><strong>Dimenzije:</strong> 500 × 500 mm</li><li><strong>Masa:</strong> 26,8 kg/m²</li><li><strong>Debljina:</strong> 53 mm</li><li><strong>Sertifikati:</strong> SLW 60 i Blue Angel</li><li><strong>Reakcija na vatru:</strong> DIN 4102 - B2</li></ul>[/vc_column_text][vc_single_image image="5797" img_size="large" alignment="center" style="vc_box_shadow"][/vc_column][vc_column width="1/2"][vc_column_text]<h3>Paddock podloge</h3><p>Specijalna ploča za teške uslove za konja i jahanje. Namenjeno za visoko opterećenje od 3,3 tone/dm². Ploče služe kao osnovni sloj za pesak i druge posebne slojeve.</p><ul><li>Parkour sportovi</li><li>zatvorene arene za jahanje</li><li>prelazne staze i staze za konje</li><li>omogućen prolaz vode (rupe Ø 25 mm)</li></ul><h4>Specifikacija</h4><ul><li><strong>Materijal:</strong> PVC</li><li><strong>Dimenzije:</strong> 1200 × 800 mm</li><li><strong>Masa:</strong> 29,17 kg/m²</li><li><strong>Debljina:</strong> 43 mm</li><li><strong>Sertifikati:</strong> SLW 60 i Blue Angel</li><li><strong>Reakcija na vatru:</strong> Otporno na plamen (Bfl-S1)</li><li><strong>Antikliznost:</strong> R10</li><li><strong>Korisna površina:</strong> 1160 × 760 mm (ploča ima spojeve)</li></ul>[/vc_column_text][vc_single_image image="5796" img_size="full" alignment="center" style="vc_box_shadow"][/vc_column][/vc_row]
[vc_row full_width="stretch_row" el_class="al-section al-section--mist al-diag-top"][vc_column][vc_column_text]<span class="al-label">Galerija</span><h2 class="al-display--lg">Podloge za štale u primeni</h2><p>Kliknite na sliku za uvećan prikaz.</p>[/vc_column_text][/vc_column][/vc_row]
[vc_row full_width="stretch_row" el_class="al-section al-section--mist"][vc_column][vc_column_text][gallery ids="15788,15789,15790,15791" columns="4" size="medium" link="file"][/vc_column_text][/vc_column][/vc_row]
[vc_row full_width="stretch_row" el_class="al-section al-section--mist"][vc_column][vc_column_text][gallery ids="5794,5795,5798,5793" columns="4" size="medium" link="file"][/vc_column_text][/vc_column][/vc_row]
[vc_row full_width="stretch_row" el_class="al-section al-section--paper"][vc_column][vc_column_text]<span class="al-label">Dodatna opcija</span><h2 class="al-display--lg">Za teže uslove: Mosolut Heavy</h2><div class="al-card" style="max-width:360px"><div class="al-card__media"><img src="' . esc_url( $mosolut_img ) . '" alt="Mosolut Heavy — robusne dvostrane PVC ploče za štale" /></div><div class="al-card__body"><h3><a href="' . esc_url( $mosolut_url ) . '">Mosolut Heavy</a></h3><p>Robusne dvostrane PVC ploče za štale, otporne na trošenje i vremenske uslove. Cena na upit.</p></div></div>[/vc_column_text][/vc_column][/vc_row]
[vc_row full_width="stretch_row" el_class="al-section al-section--navy al-plates al-diag-top--rev"][vc_column][vc_column_text]<div class="al-hero"><span class="al-label">Kontakt</span><h2 class="al-display--lg">Spremni za pod za štale i farme?</h2><p class="al-hero__sub">Pošaljite dimenzije prostora i namenu — odgovaramo u roku od jednog radnog dana.</p><div class="al-hero__cta"><a class="al-btn" href="' . esc_url( $home . '/kontakt/' ) . '">Pošaljite upit</a><a class="al-btn al-btn--ghost" href="' . esc_url( $tel ) . '">069 234 00 72</a></div></div>[/vc_column_text][/vc_column][/vc_row]';

echo "--- PROBA ---\n";
echo 'dužina novog sadržaja: ' . strlen( $content ) . " B (staro: " . strlen( $post->post_content ) . " B)\n";
echo "Mosolut proizvod: {$mosolut_url}\nMosolut slika: {$mosolut_img}\n";

if ( ! $APPLY ) {
	echo "\n(bez 'apply' — ništa nije upisano)\n";
	return;
}

file_put_contents( "{$BDIR}/{$ID}-" . date( 'Ymd-His' ) . '.txt', $post->post_content );

wp_update_post( array( 'ID' => $ID, 'post_content' => $content ) );
update_post_meta( $ID, '_woodmart_title_off', 'on' );
update_post_meta( $ID, '_woodmart_main_layout', 'full-width' );
update_post_meta( $ID, '_yoast_wpseo_title', 'Podloge za štale, hipodrome i teška vozila %%sep%% %%sitename%%' );
update_post_meta( $ID, '_yoast_wpseo_metadesc', 'Podloge za štale, hipodrome i prostore za teška vozila i poljoprivredne mašine. Trpe velika opterećenja i lako se postavljaju.' );

global $wpdb;
$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}yoast_indexable WHERE object_id=%d AND object_type='post'", $ID ) );
clean_post_cache( $ID );

echo "\n✅ upisano ($ID). Bekap u scratchpad/content-backup/.\n";
