<?php
/**
 * 16657 „Košarkaške konstrukcije" — nova sekcija „Naši izvedeni tereni".
 * Stranica ima 478 GSC klikova i do sada NIJEDNU fotografiju.
 *
 * Pozadina: sekcija 5 je --mist sa al-diag-top, sekcija 6 je --paper.
 * Nova ide između njih kao --paper BEZ reza (F7.20: dva dijagonalna reza ne smeju
 * biti uzastopna), pa je smena mist → paper → paper — isti obrazac kao već
 * postojeće sekcije 3 i 4.
 */
require_once ABSPATH . 'wp-admin/includes/image.php';

$IMGS = array(
	array( 'f' => 'novi sajt/tereni za basket/bergo-ultimate-basketball-sg-gg-yw-2.jpg',
	       'alt' => 'Košarkaški teren sa fiksnom konstrukcijom i tablom, Bergo Ultimate podloga',
	       't'   => 'Teren sa košarkaškom konstrukcijom — Bergo Ultimate' ),
	array( 'f' => 'novi sajt/tereni za basket/teren u kancelariji2.jpg',
	       'alt' => 'Zatvoreni košarkaški teren u hali sa profesionalnom mobilnom konstrukcijom',
	       't'   => 'Mobilna konstrukcija u zatvorenom terenu' ),
	array( 'f' => 'novi sajt/tereni za basket/teren u kancelariji3.jpg',
	       'alt' => 'Mobilna košarkaška konstrukcija sa zaštitnom oblogom stuba u sali',
	       't'   => 'Konstrukcija sa zaštitom stuba' ),
	array( 'f' => 'novi sajt/tereni za basket/teren na krovu.jpg',
	       'alt' => 'Košarkaški teren na krovu zgrade sa konstrukcijom i zaštitnom mrežom',
	       't'   => 'Teren na krovu sa konstrukcijom i mrežom' ),
	array( 'f' => 'novi sajt/tereni za basket/Mali Pozarevac teren.jpg',
	       'alt' => 'Koš za dvorište na privatnom terenu u Malom Požarevcu',
	       't'   => 'Koš u dvorištu — Mali Požarevac' ),
	array( 'f' => 'novi sajt/tereni za basket/Teren Tara 2022.jpg',
	       'alt' => 'Košarkaški teren sa konstrukcijom na Tari',
	       't'   => 'Teren sa košem — Tara' ),
	array( 'f' => 'novi sajt/tereni za basket/Teren za basket HN6.jpg',
	       'alt' => 'Košarkaški teren sa konstrukcijom pored mora, Herceg Novi',
	       't'   => 'Teren sa konstrukcijom — Herceg Novi' ),
	array( 'f' => 'novi sajt/tereni za basket/Teren za BG ligu.jpg',
	       'alt' => 'Košarkaški teren u boji sa konstrukcijom za beogradsku ligu',
	       't'   => 'Teren za BG ligu' ),
	array( 'f' => 'novi sajt/tereni za basket/Multisport subotica.jpg',
	       'alt' => 'Multisport teren sa košarkaškom konstrukcijom u Subotici',
	       't'   => 'Multisport teren — Subotica' ),
);

$ROOT = 'C:/Miroslav/Antas line/';
$up   = wp_upload_dir();
$ids  = array();

foreach ( $IMGS as $im ) {
	$src = $ROOT . $im['f'];
	if ( ! file_exists( $src ) ) { WP_CLI::warning( 'NEMA: ' . $src ); continue; }
	$name = sanitize_title( $im['t'] ) . '.jpg';
	$dest = $up['path'] . '/' . $name;

	$exists = attachment_url_to_postid( $up['url'] . '/' . $name );
	if ( $exists ) { $ids[] = array( $exists, $im['alt'] ); WP_CLI::log( 'postoji: ' . $name ); continue; }

	list( $w, $h ) = getimagesize( $src );
	$ed = wp_get_image_editor( $src );
	if ( is_wp_error( $ed ) ) { WP_CLI::warning( 'editor: ' . $src ); continue; }
	if ( max( $w, $h ) > 1600 ) { $ed->resize( $w >= $h ? 1600 : null, $h > $w ? 1600 : null, false ); }
	$ed->set_quality( 82 );
	$saved = $ed->save( $dest );
	if ( is_wp_error( $saved ) ) { WP_CLI::warning( 'save: ' . $src ); continue; }

	$id = wp_insert_attachment( array(
		'post_mime_type' => $saved['mime-type'],
		'post_title'     => $im['t'],
		'post_status'    => 'inherit',
	), $saved['path'], 0 );
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $saved['path'] ) );
	update_post_meta( $id, '_wp_attachment_image_alt', $im['alt'] );
	$ids[] = array( $id, $im['alt'] );
	WP_CLI::log( sprintf( '#%d %s  %dx%d → %dx%d', $id, $name, $w, $h, $saved['width'], $saved['height'] ) );
}

if ( count( $ids ) < 6 ) { WP_CLI::error( 'Premalo slika uvezeno: ' . count( $ids ) ); }

// ---- HTML nove sekcije ----
$g = '';
foreach ( $ids as list( $id, $alt ) ) {
	$g .= '<img src="' . esc_url( wp_get_attachment_image_url( $id, 'full' ) ) . '" alt="' . esc_attr( $alt ) . '" />';
}
$inner = '<span class="al-label">Reference</span>'
	. '<h2>Naši izvedeni tereni sa konstrukcijama</h2>'
	. '<p>Konstrukcije koje isporučujemo postavljene su na terenima širom Srbije i regiona — '
	. 'od dvorišnih koševa do fiksnih tabli na klupskim i školskim terenima. '
	. 'Kliknite na sliku za uvećan prikaz.</p>'
	. '<div class="al-grid al-grid--3" style="margin-top:24px">' . $g . '</div>';

$section = '[vc_row full_width="stretch_row" el_class="al-section al-section--paper"][vc_column][vc_column_text]'
	. $inner . '[/vc_column_text][/vc_column][/vc_row]';

// ---- ubaci PRE FAQ sekcije (indeks 5 u podeli po [vc_row) ----
$post = get_post( 16657 );
$c    = $post->post_content;
file_put_contents(
	'C:/Users/Miroslav/AppData/Local/Temp/claude/C--Projekti-antasline-vault/21101aee-b36f-40b9-be7a-84052e879608/scratchpad/content-backup/16657-' . date( 'Ymd-His' ) . '.html',
	$c
);

$parts = preg_split( '#(?=\[vc_row)#', $c );
$faq   = null;
foreach ( $parts as $i => $p ) {
	if ( strpos( $p, 'najčešća pitanja' ) !== false ) { $faq = $i; break; }
}
if ( null === $faq ) { WP_CLI::error( 'FAQ sekcija nije nađena' ); }
array_splice( $parts, $faq, 0, array( $section ) );
$new = implode( '', $parts );

wp_update_post( array( 'ID' => 16657, 'post_content' => $new ) );
WP_CLI::success( sprintf( '16657: %d slika, sekcija ubačena na poziciju %d (od %d)', count( $ids ), $faq, count( $parts ) ) );
