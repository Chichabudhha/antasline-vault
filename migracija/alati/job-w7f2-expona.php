<?php
/**
 * W7 F2.1 — Expona: mreža od 12 tekstura → kartice stvarnih proizvoda.
 *
 * Odluka M (v. [[migracija/2026-07-28-W7-sanacija-builda]] F2.1): mreže dezena se
 * uklanjaju sa stranica, a same teksture se NE brišu — sele se u Woo galeriju
 * proizvoda, gde su na svom mestu kao izbor dezena.
 *
 * Zatečeno stanje (izmereno, razlikuje se od opisa u planu):
 *  - 16667 VEĆ ima sekciju „EXPONA program", ali sa pomešanim ciljevima (kartica
 *    „EXPONA Design" vodi na stranicu Commercial-a) i sa tekstom koji Simplay
 *    opisuje kao „klik sistem" — a Simplay je loose-lay, klik je Clic.
 *  - 16685 ima NEZATVOREN [vc_column_text] (6 otvorenih / 5 zatvorenih u celom
 *    post_content-u). Zamena cele sekcije to usput gasi.
 *  - 16668 nema mrežu tekstura — tamo se sekcija samo dodaje.
 *
 * Poziv:
 *   php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-w7f2-expona.php
 *   … isto + `apply` za upis.
 */

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once __DIR__ . '/al_webp.php';

$APPLY = in_array( 'apply', $args, true );
$BASE  = 'http://localhost/antasline';

// ---------------------------------------------------------------- 1. uvoz fotke
// `uploads/2026/2026/01/` je duplo ugnežden folder iz live importa — 2.492 fajla
// bez ijedne reference iz baze, nedostupni preko normalnih WP URL-ova. Fajl se
// zato uvozi kao svaka datoteka sa diska, čime uredno ulazi u medijateku.
$SRC  = 'C:/xampp/htdocs/antasline/wp-content/uploads/2026/2026/01/kancelarija-expona-clic.jpg';
$IMP  = array(
	'title' => 'Kancelarija sa EXPONA Clic podom',
	'alt'   => 'Open space kancelarija sa svetlim hrastovim EXPONA Clic 19dB podom',
);

function al_f2_import( $src, $title, $alt, $apply ) {
	$up   = wp_upload_dir();
	$slug = sanitize_title( $title );
	$ext  = al_target_ext( $src );
	$name = $slug . '.' . $ext;
	$dest = $up['path'] . '/' . $name;

	$existing = attachment_url_to_postid( $up['url'] . '/' . $name );
	if ( $existing ) { WP_CLI::log( "  prilog već postoji: #$existing $name" ); return $existing; }
	if ( ! file_exists( $src ) ) { WP_CLI::error( 'NEMA izvor: ' . $src ); }
	if ( ! $apply ) { WP_CLI::log( "  [proba] uvezao bih: $name" ); return 0; }

	$info = getimagesize( $src );
	list( $w, $h ) = $info;

	// EXIF rotacija se MORA primeniti ručno — WP_Image_Editor je ne radi pri load().
	$exifRot = false;
	if ( preg_match( '/\.jpe?g$/i', $src ) && function_exists( 'exif_read_data' ) ) {
		$ex      = @exif_read_data( $src );
		$exifRot = ! empty( $ex['Orientation'] ) && (int) $ex['Orientation'] > 1;
	}
	$editor = wp_get_image_editor( $src );
	if ( is_wp_error( $editor ) ) { WP_CLI::error( 'editor: ' . $src ); }
	if ( $exifRot && method_exists( $editor, 'maybe_exif_rotate' ) ) {
		$editor->maybe_exif_rotate();
		$sz = $editor->get_size(); $w = $sz['width']; $h = $sz['height'];
		WP_CLI::log( '  (EXIF rotacija primenjena)' );
	}
	if ( max( $w, $h ) > 1600 ) { $editor->resize( $w >= $h ? 1600 : null, $h > $w ? 1600 : null, false ); }
	$editor->set_quality( 82 );
	$saved = $editor->save( $dest, al_target_mime( $dest ) );
	if ( is_wp_error( $saved ) ) { WP_CLI::error( 'save: ' . $src ); }

	$id = wp_insert_attachment( array(
		'post_mime_type' => $saved['mime-type'],
		'post_title'     => $title,
		'post_status'    => 'inherit',
	), $saved['path'], 0 );
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $saved['path'] ) );
	update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	WP_CLI::log( sprintf( '  uvezeno #%d %s (%dx%d)', $id, $name, $saved['width'], $saved['height'] ) );
	return $id;
}

WP_CLI::log( '== 1. uvoz fotke za EXPONA Clic ==' );
$clicId = al_f2_import( $SRC, $IMP['title'], $IMP['alt'], $APPLY );

// ------------------------------------------------------- 2. definicija 4 kartice
// Tekst je doslovno iz post_excerpt-a samih proizvoda (16917/16914/16915/16916),
// ne parafraza — da se ne ponovi zatečena greška „Simplay = klik sistem".
$CARDS = array(
	'clic' => array(
		'title' => 'EXPONA Clic 19dB',
		'url'   => $BASE . '/proizvod/expona-clic-19db-wood-klik-daska/',
		'att'   => $clicId,
		'alt'   => $IMP['alt'],
		'desc'  => 'rigidna klik daska sa 5G-i Välinge sistemom bez lepka, IXPE akustična podloga, 100% vodootporna',
	),
	'commercial' => array(
		'title' => 'EXPONA Commercial',
		'url'   => $BASE . '/proizvod/expona-commercial-lvt-vinil-plocice/',
		'att'   => 10931,
		'alt'   => 'Kancelarija sa tamnim EXPONA Commercial LVT podom u dezenu drveta',
		'desc'  => 'heterogena PVC dizajn pločica za teški komercijalni saobraćaj, 80 dezena, sloj za habanje 0,55 mm',
	),
	'flow' => array(
		'title' => 'EXPONA Flow',
		'url'   => $BASE . '/proizvod/expona-flow-lvt-vinil-podovi-u-rolnama/',
		'att'   => 8001,
		'alt'   => 'Kantina sa svetlim EXPONA Flow vinil podom položenim iz rolne',
		'desc'  => 'heterogena PVC obloga u rolnama, 50 dezena, sloj za habanje 0,7 mm, za jako opterećenje',
	),
	'simplay' => array(
		'title' => 'EXPONA Simplay 19dB',
		'url'   => $BASE . '/proizvod/expona-simplay-19db-loose-lay-lvt/',
		'att'   => 5697,
		'alt'   => 'Kafić sa EXPONA Simplay LVT podom u dezenu drveta',
		'desc'  => 'loose-lay pločica bez lepka, integrisana IXPE podloga smanjuje udarni zvuk za 19 dB, 12 dezena',
	),
);

/** Sekcija „EXPONA program"; $skip = ključ kartice koja se izostavlja (sopstvena stranica). */
function al_f2_section( $cards, $skip, $extraProse = '', $sectionClass = 'al-section al-section--mist al-diag-top' ) {
	$use = array_filter( $cards, function ( $k ) use ( $skip ) { return $k !== $skip; }, ARRAY_FILTER_USE_KEY );
	$cols = count( $use );

	$html  = '<span class="al-label">Kolekcije</span>';
	$html .= '<h2 class="al-display--lg">EXPONA program</h2>';
	$html .= '<div class="al-grid al-grid--' . $cols . '" style="margin-top: 32px">';
	foreach ( $use as $c ) {
		$src = wp_get_attachment_image_url( $c['att'], 'full' );
		if ( ! $src ) { WP_CLI::warning( 'nema prilog #' . $c['att'] . ' za ' . $c['title'] ); }
		// ceo karton je link (a.al-card, antas-design.css:396) — veća meta od samog naslova.
		// al_enhance_content_images() vidi <img> unutar <a> kao navigacionu, pa je ne
		// pretvara u lightbox, samo doda srcset/width/height.
		$html .= '<a href="' . esc_url( $c['url'] ) . '" class="al-card">';
		$html .= '<span class="al-card__media"><img src="' . esc_url( $src ) . '" alt="' . esc_attr( $c['alt'] ) . '" /></span>';
		$html .= '<span class="al-card__title">' . esc_html( $c['title'] ) . '</span></a>';
	}
	$html .= '</div>';

	$parts = array();
	foreach ( $use as $c ) { $parts[] = '<strong>' . esc_html( $c['title'] ) . '</strong> — ' . $c['desc']; }
	$html .= '<p style="margin-top:24px">' . implode( '. ', $parts ) . '.</p>';
	if ( $extraProse ) { $html .= $extraProse; }

	return '[vc_row full_width="stretch_row" el_class="' . esc_attr( $sectionClass ) . '"]'
		. '[vc_column][vc_column_text]' . $html . '[/vc_column_text][/vc_column][/vc_row]';
}

// ---------------------------------------------------------------- 3. po stranici
// Na 16667 (roditelj) idu sve 4; na pod-stranici se izostavlja sopstvena kartica.
//
// Klasa sekcije: kod zamene se nasleđuje klasa sekcije koja se menja (ritam stranice
// ostaje netaknut). Kod umetanja na 16668 susedna sekcija [3] je već `mist al-diag-top`
// — nova zato ide kao `paper` BEZ reza: F7.20 zabranjuje dva uzastopna dijagonalna reza.
// Isti presedan kao `primer-job-16657.php` (mist → paper → paper).
$CUT = 'al-section al-section--mist al-diag-top';
$JOBS = array(
	16667 => array( 'skip' => null,         'mode' => 'replace', 'idx' => 3, 'class' => $CUT ),
	16684 => array( 'skip' => 'clic',       'mode' => 'replace', 'idx' => 3, 'class' => $CUT ),
	16685 => array( 'skip' => 'commercial', 'mode' => 'replace', 'idx' => 3, 'class' => $CUT ),
	16668 => array( 'skip' => 'flow',       'mode' => 'insert',  'idx' => 4, 'class' => 'al-section al-section--paper' ),
);

$bkDir = 'C:/Users/Miroslav/AppData/Local/Temp/al-content-backup';
if ( ! is_dir( $bkDir ) ) { mkdir( $bkDir, 0777, true ); }

WP_CLI::log( PHP_EOL . '== 2. stranice ==' );
foreach ( $JOBS as $pid => $j ) {
	$post  = get_post( $pid );
	if ( ! $post ) { WP_CLI::warning( "nema posta $pid" ); continue; }
	$parts = preg_split( '#(?=\[vc_row)#', $post->post_content );

	// Prozu sa 16667 (cross-linkovi ka Flow stranici, „Poručite direktno" spisak)
	// vredi zadržati — nosi interne linkove ka 8 proizvoda.
	$extra = '';
	if ( 16667 === $pid && isset( $parts[ $j['idx'] ] ) ) {
		if ( preg_match_all( '#<p>Pogledajte detaljnu.*?</p>|<p style="margin-top:16px"><strong>Poručite direktno.*?</p>#s', $parts[ $j['idx'] ], $m ) ) {
			$extra = implode( '', $m[0] );
			WP_CLI::log( "  $pid: zadržano " . count( $m[0] ) . ' pasusa postojeće proze' );
		}
	}

	$section = al_f2_section( $CARDS, $j['skip'], $extra, $j['class'] );

	if ( 'replace' === $j['mode'] ) {
		$old = $parts[ $j['idx'] ] ?? '';
		$imgs = preg_match_all( '#<img #', $old );
		WP_CLI::log( sprintf( '  %d: zamena dela %d (%d bajtova, %d slika) → %d bajtova', $pid, $j['idx'], strlen( $old ), $imgs, strlen( $section ) ) );
		$parts[ $j['idx'] ] = $section;
	} else {
		WP_CLI::log( sprintf( '  %d: umetanje pred deo %d', $pid, $j['idx'] ) );
		array_splice( $parts, $j['idx'], 0, array( $section ) );
	}

	if ( $APPLY ) {
		file_put_contents( $bkDir . '/' . $pid . '-' . date( 'Ymd-His' ) . '.html', $post->post_content );
		wp_update_post( array( 'ID' => $pid, 'post_content' => implode( '', $parts ) ) );
		WP_CLI::log( "     upisano" );
	}
}

// ------------------------------------------- 4. teksture → Woo galerija proizvoda
// Redosled je onaj sa stranice (beton → metal → škriljac → drvo), ne po ID-u.
$MOVE = array(
	16917 => array( 5540, 5541, 5542, 5543, 5554, 5556, 5557, 5558, 5562, 5563, 5564, 5565 ), // Clic
	16914 => array( 5649, 5650, 5651, 5652, 5654, 5655, 5656, 5657, 5660, 5661, 5662, 5663 ), // Commercial
);

WP_CLI::log( PHP_EOL . '== 3. teksture u Woo galerije ==' );
foreach ( $MOVE as $prod => $add ) {
	$cur = array_filter( explode( ',', (string) get_post_meta( $prod, '_product_image_gallery', true ) ) );
	$new = array_values( array_unique( array_merge( $cur, array_map( 'strval', $add ) ) ) );
	WP_CLI::log( sprintf( '  %d: %d → %d slika u galeriji', $prod, count( $cur ), count( $new ) ) );
	if ( $APPLY ) { update_post_meta( $prod, '_product_image_gallery', implode( ',', $new ) ); }
}

WP_CLI::success( $APPLY ? 'F2.1 upisano' : 'PROBA — ništa nije upisano (dodaj `apply`)' );
