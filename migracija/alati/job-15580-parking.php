<?php
/**
 * 15580 „Podloge za Parking" — nova sekcija „Naši izvedeni parkinzi i staze".
 * 2.123 reči teksta, do sada NIJEDNA fotografija; poklapa se sa odobrenom GA4
 * publikom „Parking & spoljne podloge" (CLAUDE §5).
 *
 * Stranica je starijeg tipa (bez `al-section--paper/mist` klasa), pa nova sekcija
 * ide kao običan `[vc_row]` — `al-grid` i lightbox rade nezavisno od tih klasa.
 * Umeće se PRE sekcije [15] (`b-hide dark footer-top`), dakle na kraj sadržaja
 * a iznad podnožja.
 *
 * Izbor: rezultat (1–3) → reference (4–6) → proces i detalj (7–9). Kandidati
 * pregledani preko kontakt-lista iz `novi sajt/podloge za parking` (35 fotki).
 * Sve su već WebP i ispod 1600px, pa ih al_import kopira bez prekodiranja.
 */
require_once ABSPATH . 'wp-admin/includes/image.php';
require_once __DIR__ . '/al_webp.php';

$ROOT = 'C:/Miroslav/Antas line/novi sajt/podloge za parking/';

$IMGS = array(
	array( 'f' => 'parking sa geogravel podlogom.webp',
	       't' => 'Parking sa GEOGRAVEL podlogom',
	       'alt' => 'Popunjen parking sa vodopropusnom GEOGRAVEL podlogom ispunjenom šljunkom' ),
	array( 'f' => 'parking sa runfloor podlogom.webp',
	       't' => 'Travnato parkiralište sa RUNFLOOR podlogom',
	       'alt' => 'Parkirani automobili na travnatom parkiralištu izvedenom RUNFLOOR podlogom' ),
	array( 'f' => 'parking staza na travnjaku sa automobilom preko staze.webp',
	       't' => 'Staza za vozila preko travnjaka',
	       'alt' => 'Automobil prelazi preko staze od podloge položene u travnjaku' ),
	array( 'f' => 'geocross na javnoj povrsini.webp',
	       't' => 'GEOCROSS na javnoj zelenoj površini',
	       'alt' => 'Travnata javna površina ojačana GEOCROSS podlogom, sa šetačima u parku' ),
	array( 'f' => 'geogravel na parkingu stadiona juventusa.webp',
	       't' => 'Parking stadiona Juventus — GEOGRAVEL',
	       'alt' => 'Veliki parking ispred stadiona Juventusa izveden GEOGRAVEL podlogom' ),
	array( 'f' => 'geoflor kod krivog tornja u Pizi.webp',
	       't' => 'GEOFLOR kod Krivog tornja u Pizi',
	       'alt' => 'Zelena površina sa GEOFLOR podlogom ispred Krivog tornja u Pizi' ),
	array( 'f' => 'postavljanje geogravel podloge.webp',
	       't' => 'Postavljanje GEOGRAVEL podloge',
	       'alt' => 'Mini bager nasipa šljunak preko postavljene GEOGRAVEL podloge' ),
	array( 'f' => 'runfloor polaganje ploca.webp',
	       't' => 'Polaganje RUNFLOOR ploča',
	       'alt' => 'Radnik ručno spaja RUNFLOOR ploče pri postavljanju parking podloge' ),
	array( 'f' => 'geogravel sa sljunkom.webp',
	       't' => 'GEOGRAVEL ispunjen šljunkom — detalj',
	       'alt' => 'Detalj GEOGRAVEL podloge delimično ispunjene belim šljunkom' ),
);

$up  = wp_upload_dir();
$ids = array();

foreach ( $IMGS as $im ) {
	$src = $ROOT . $im['f'];
	if ( ! file_exists( $src ) ) { WP_CLI::warning( 'NEMA: ' . $src ); continue; }

	$name = sanitize_title( $im['t'] ) . '.' . al_target_ext( $src );
	$dest = $up['path'] . '/' . $name;

	$exists = attachment_url_to_postid( $up['url'] . '/' . $name );
	if ( $exists ) { $ids[] = array( $exists, $im['alt'] ); WP_CLI::log( 'postoji: ' . $name ); continue; }

	$info = getimagesize( $src );
	if ( ! $info ) { WP_CLI::warning( 'nije slika: ' . $src ); continue; }
	list( $w, $h ) = $info;

	$needsFit = max( $w, $h ) > 1600;
	if ( ! $needsFit && ( $info['mime'] ?? '' ) === al_target_mime( $dest ) ) {
		if ( ! copy( $src, $dest ) ) { WP_CLI::warning( 'kopija: ' . $src ); continue; }
		$saved = array( 'path' => $dest, 'width' => $w, 'height' => $h, 'mime-type' => $info['mime'] );
	} else {
		$ed = wp_get_image_editor( $src );
		if ( is_wp_error( $ed ) ) { WP_CLI::warning( 'editor: ' . $src ); continue; }
		if ( $needsFit ) { $ed->resize( $w >= $h ? 1600 : null, $h > $w ? 1600 : null, false ); }
		$ed->set_quality( 82 );
		$saved = $ed->save( $dest, al_target_mime( $dest ) );
		if ( is_wp_error( $saved ) ) { WP_CLI::warning( 'save: ' . $src ); continue; }
	}

	$id = wp_insert_attachment( array(
		'post_mime_type' => $saved['mime-type'],
		'post_title'     => $im['t'],
		'post_status'    => 'inherit',
	), $saved['path'], 0 );
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $saved['path'] ) );
	update_post_meta( $id, '_wp_attachment_image_alt', $im['alt'] );
	$ids[] = array( $id, $im['alt'] );
	WP_CLI::log( sprintf( '#%d %s  %dx%d', $id, $name, $saved['width'], $saved['height'] ) );
}

if ( count( $ids ) < 6 ) { WP_CLI::error( 'Premalo slika: ' . count( $ids ) ); }

// ---- HTML sekcije ----
$g = '';
foreach ( $ids as list( $id, $alt ) ) {
	$g .= '<img src="' . esc_url( wp_get_attachment_image_url( $id, 'full' ) ) . '" alt="' . esc_attr( $alt ) . '" />';
}
$inner = '<h2>Naši izvedeni parkinzi, prilazi i staze</h2>'
	. '<p>Vodopropusne podloge za parkirališta i prilaze koje smo isporučili i postavili — '
	. 'od travnatih parkinga i staza kroz zelene površine do šljunčanih parkirališta i '
	. 'velikih javnih površina. Kliknite na sliku za uvećan prikaz.</p>'
	. '<div class="al-grid al-grid--3" style="margin-top:24px">' . $g . '</div>';

$section = '[vc_row][vc_column][vc_column_text]' . $inner . '[/vc_column_text][/vc_column][/vc_row]';

// ---- umetanje pre sekcije `footer-top` ----
$post = get_post( 15580 );
$bk   = 'C:/Users/Miroslav/AppData/Local/Temp/claude/C--Projekti-antasline-vault/145243d5-ba94-443b-9220-b1bf2da0a715/scratchpad/content-backup';
if ( ! is_dir( $bk ) ) { mkdir( $bk, 0777, true ); }
file_put_contents( $bk . '/15580-' . date( 'Ymd-His' ) . '.html', $post->post_content );

$parts = preg_split( '#(?=\[vc_row)#', $post->post_content );
$at    = null;
foreach ( $parts as $i => $p ) {
	if ( strpos( $p, 'footer-top' ) !== false ) { $at = $i; break; }
}
if ( null === $at ) { $at = count( $parts ); }
array_splice( $parts, $at, 0, array( $section ) );

wp_update_post( array( 'ID' => 15580, 'post_content' => implode( '', $parts ) ) );
WP_CLI::success( sprintf( '15580: %d slika, sekcija na poziciju %d (od %d)', count( $ids ), $at, count( $parts ) ) );
