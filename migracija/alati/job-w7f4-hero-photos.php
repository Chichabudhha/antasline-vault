<?php
/**
 * W7 F4.1 — foto hero na svim `al-section--navy` landing stranicama.
 *
 * Bira NAJVEĆU (širina) fotografiju koja već postoji u sadržaju te iste stranice
 * (curated u F7.21–F7.23, dakle već izabrana po H1 temi) i stavlja je kao
 * background-image na hero `[vc_row]` (segment koji sadrži `<h1`), preko
 * WPBakery `css=".vc_custom_heroID{...}"` atributa — isti mehanizam kao ručni
 * Design Options u WPBakery editoru, samo generisan skriptom.
 *
 * Prag: min 1100px širine, aspect-ratio 1.15–3.2 (izbegava portret/kvadrat
 * dijagrame). Stranica bez kandidata ostaje na običnoj plavoj (`al-plates`)
 * pozadini — nema izmišljanja fotke.
 *
 * Poziv:  php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-w7f4-hero-photos.php [apply]
 * Bez `apply` = proba (samo izveštaj, ne dira bazu).
 */

$APPLY = in_array( 'apply', (array) ( $args ?? array() ), true );
$BASE  = 'http://localhost/antasline';
$UP_URL  = $BASE . '/wp-content/uploads/';
$UP_PATH = WP_CONTENT_DIR . '/uploads/';
$BDIR  = WP_CONTENT_DIR . '/../scratchpad/content-backup';
if ( ! is_dir( $BDIR ) ) { mkdir( $BDIR, 0777, true ); }

$MIN_W     = 1100;
$MIN_RATIO = 1.15;
$MAX_RATIO = 3.2;

echo $APPLY ? "=== REŽIM: APPLY ===\n\n" : "=== REŽIM: PROBA (bez izmena) ===\n\n";

// Sve publish stranice sa standardnim navy+plates hero šablonom, minus home (16550, već ima foto hero).
global $wpdb;
$ids = $wpdb->get_col( "
	SELECT ID FROM {$wpdb->posts}
	WHERE post_status = 'publish' AND post_type = 'page'
	AND post_content LIKE '%al-section--navy%al-plates%'
	AND ID != 16550
	ORDER BY ID
" );

// 16684/16685: jedina fotka ≥1100px na stranici je Expona-Flow-Cafeteria — instalacija
// SUSEDNOG proizvoda (Flow), ne njihovog. Prikazivanje tuđe instalacije kao svoje je
// pogrešno predstavljanje (isto načelo kao 16677/16671/16919), pa ostaju bez foto heroa.
$EXCLUDE = array( 16684, 16685 );

$done = array();
$skip_no_hero = array();
$skip_no_photo = array();
$skip_has_photo = array();
$skip_excluded = array();

foreach ( $ids as $id ) {
	$post = get_post( $id );
	$parts = preg_split( '#(?=\[vc_row)#', $post->post_content );

	$hero_i = -1;
	foreach ( $parts as $i => $p ) {
		if ( strpos( $p, '<h1' ) !== false ) { $hero_i = $i; break; }
	}
	if ( $hero_i < 0 ) { $skip_no_hero[] = $id; continue; }

	if ( strpos( $parts[ $hero_i ], 'al-hero-photo' ) !== false ) { $skip_has_photo[] = $id; continue; }
	if ( in_array( $id, $EXCLUDE, true ) ) { $skip_excluded[] = $id; continue; }

	// Kandidati: svi <img src="...uploads..."> iz CELE stranice (ne samo hero segmenta) —
	// najveća curated fotka na stranici je i dalje najbolji izbor za pozadinu, ma u kojoj sekciji živela.
	preg_match_all( '#<img[^>]+src="([^"]*uploads[^"]+)"#i', $post->post_content, $m );
	$best = null;
	foreach ( $m[1] as $url ) {
		if ( preg_match( '#\.svg(\?|$)#i', $url ) ) { continue; }
		$path = str_replace( $UP_URL, $UP_PATH, $url );
		if ( ! file_exists( $path ) ) { continue; }
		$dim = @getimagesize( $path );
		if ( ! $dim ) { continue; }
		list( $w, $h ) = $dim;
		if ( $w < $MIN_W ) { continue; }
		$ratio = $h > 0 ? $w / $h : 0;
		if ( $ratio < $MIN_RATIO || $ratio > $MAX_RATIO ) { continue; }
		if ( ! $best || $w > $best['w'] ) { $best = array( 'url' => $url, 'w' => $w, 'h' => $h ); }
	}

	if ( ! $best ) { $skip_no_photo[] = $id; continue; }

	$done[ $id ] = array( 'hero_i' => $hero_i, 'img' => $best );
}

echo "--- Dobija foto hero (" . count( $done ) . ") ---\n";
foreach ( $done as $id => $d ) {
	printf( "  %-6d %4dpx  %s\n", $id, $d['img']['w'], basename( $d['img']['url'] ) );
}

echo "\n--- Preskočeno: nema fotke ≥{$MIN_W}px odgovarajućeg odnosa strana (" . count( $skip_no_photo ) . ") ---\n";
foreach ( $skip_no_photo as $id ) { echo "  {$id}  " . get_the_title( $id ) . "\n"; }

if ( $skip_no_hero ) {
	echo "\n--- Preskočeno: nema <h1> segmenta, šablon ne odgovara (" . count( $skip_no_hero ) . ") ---\n";
	foreach ( $skip_no_hero as $id ) { echo "  {$id}  " . get_the_title( $id ) . "\n"; }
}
if ( $skip_has_photo ) {
	echo "\n--- Preskočeno: već ima foto hero (" . count( $skip_has_photo ) . ") ---\n";
	foreach ( $skip_has_photo as $id ) { echo "  {$id}\n"; }
}
if ( $skip_excluded ) {
	echo "\n--- Preskočeno: ručni izuzetak, jedina fotka je suseda proizvoda (" . count( $skip_excluded ) . ") ---\n";
	foreach ( $skip_excluded as $id ) { echo "  {$id}  " . get_the_title( $id ) . "\n"; }
}

if ( ! $APPLY ) {
	echo "\nPROBA — ništa nije upisano. Pusti sa 'apply' da se izvrši.\n";
	return;
}

echo "\n--- Upis ---\n";
foreach ( $done as $id => $d ) {
	$post  = get_post( $id );
	$parts = preg_split( '#(?=\[vc_row)#', $post->post_content );
	$seg   = $parts[ $d['hero_i'] ];

	$pattern = '#^\[vc_row full_width="stretch_row" el_class="al-section al-section--navy([^"]*)"\]#';
	if ( ! preg_match( $pattern, $seg, $mm ) ) {
		echo "  🔴 {$id}: hero vc_row otvarajući tag ne odgovara očekivanom obrascu — preskačem\n";
		continue;
	}

	$css_class = ".vc_custom_heroF4{$id}";
	$replacement = '[vc_row full_width="stretch_row" el_class="al-section al-section--navy al-hero-photo' . $mm[1] . '" css="' . $css_class . '{background-image: url(\'' . esc_url_raw( $d['img']['url'] ) . '\') !important;}"]';
	$parts[ $d['hero_i'] ] = preg_replace( $pattern, $replacement, $seg, 1 );

	$new_content = implode( '', $parts );

	file_put_contents( "{$BDIR}/{$id}-" . date( 'Ymd-His' ) . '-pre-f4hero.txt', $post->post_content );
	wp_update_post( array( 'ID' => $id, 'post_content' => $new_content ) );
	wpbakery()->buildShortcodesCss( $id, 'custom' );

	$meta_ok = strpos( (string) get_post_meta( $id, '_wpb_shortcodes_custom_css', true ), $css_class ) !== false;
	printf( "  %s %-6d %dpx %s\n", $meta_ok ? '✅' : '⚠️ meta-fail', $id, $d['img']['w'], basename( $d['img']['url'] ) );
}

echo "\nGotovo.\n";
