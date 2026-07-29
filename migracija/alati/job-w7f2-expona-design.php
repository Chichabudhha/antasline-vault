<?php
/**
 * W7 F2.2 + F2.4 — EXPONA Design: dokumentacija, slike, ispravka netačne napomene.
 *
 * Nalazi koji menjaju plan (izmereno, ne pretpostavljeno):
 *  - Treći PDF iz plana (`2019/10/Brochure-EXPONA-FLOW-English…`) je BAJT-IDENTIČAN
 *    prilogu 5593 koji je već u medijateci (isti md5) — uvoz bi napravio duplikat,
 *    pa se preskače. Uvoze se samo dva Design PDF-a.
 *  - Napomena „tehnički list još nije dobavljen od distributera" je na 16918
 *    dokazano NETAČNA: `Expona-Design-tehnički-podaci.pdf` sadrži baš ono što
 *    napomena navodi kao nepoznato (42 dezena, klase 23/34/43, protivkliznost
 *    R10/DS, Indoor Air Comfort Gold). Napomena se briše, vrednosti ulaze u tabelu.
 *  - Na 16919 (Living Clic) ista napomena je TAČNA — za tu kolekciju na disku nema
 *    nijednog dokumenta ni fotografije. Ne dira se; rupa se prijavljuje M-u.
 *  - Za EXPONA Design u arhivi nema nijedne fotografije (sve `*design*` datoteke su
 *    zapravo Commercial, Simplay ili R-Tile). Slike se zato vade iz proizvođačeve
 *    brošure — to je objectflor-ova sopstvena fotografija njihovog proizvoda, isti
 *    izvor kao već korišćene Expona press fotke, ne tuđi izvedeni posao.
 *
 * Poziv: php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-w7f2-expona-design.php [apply]
 */

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once __DIR__ . '/al_webp.php';

$APPLY = in_array( 'apply', $args, true );
$BASE  = 'http://localhost/antasline';
$UPL   = 'C:/xampp/htdocs/antasline/wp-content/uploads';
$TMP   = 'C:/Users/Miroslav/.claude/jobs/ee33c1f1/tmp/design-img';

// ------------------------------------------------- 1. PDF-ovi (već leže u uploads)
// Fajlovi su u normalnom `uploads/2019/11/` i javno dostupni (oba 200) — nedostaje
// im samo zapis u medijateci, pa se registruju na mestu, bez kopiranja.
$PDFS = array(
	array( 'file' => '2019/11/BROCHURE-EXPONA-DESIGN.pdf',      'title' => 'Brošura EXPONA Design',        'label' => 'Brošura EXPONA Design (PDF, EN)' ),
	array( 'file' => '2019/11/Expona-Design-tehnički-podaci.pdf','title' => 'Tehnički list EXPONA Design', 'label' => 'Tehnički list EXPONA Design (PDF, EN)' ),
);

WP_CLI::log( '== 1. registracija PDF-ova ==' );
foreach ( $PDFS as $k => $p ) {
	$abs = $UPL . '/' . $p['file'];
	if ( ! file_exists( $abs ) ) { WP_CLI::error( 'nema fajl: ' . $abs ); }

	$existing = 0;
	$q = new WP_Query( array(
		'post_type'   => 'attachment', 'post_status' => 'inherit', 'posts_per_page' => 1,
		'meta_query'  => array( array( 'key' => '_wp_attached_file', 'value' => $p['file'] ) ),
		'fields'      => 'ids',
	) );
	if ( $q->posts ) { $existing = (int) $q->posts[0]; }

	if ( $existing ) {
		WP_CLI::log( "  već registrovan #$existing  " . $p['file'] );
		$PDFS[ $k ]['id'] = $existing;
		continue;
	}
	if ( ! $APPLY ) { WP_CLI::log( '  [proba] registrovao bih: ' . $p['file'] ); $PDFS[ $k ]['id'] = 0; continue; }

	$id = wp_insert_attachment( array(
		'post_mime_type' => 'application/pdf',
		'post_title'     => $p['title'],
		'post_status'    => 'inherit',
	), $abs, 0 );
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $abs ) );
	$PDFS[ $k ]['id'] = $id;
	WP_CLI::log( sprintf( '  registrovan #%d  %s (%d KB)', $id, $p['file'], filesize( $abs ) / 1024 ) );
}

// -------------------------------------------------------- 2. slike iz brošure
$IMGS = array(
	array( 'f' => 'img09.jpg', 'main' => true,
	       't' => 'Lounge sa EXPONA Design podom',
	       'a' => 'Lounge prostor sa foteljama i svetlim EXPONA Design vinil podom u dezenu drveta' ),
	array( 'f' => 'img15.jpg',
	       't' => 'Kantina sa EXPONA Design podom',
	       'a' => 'Kantina sa dugačkim stolovima i EXPONA Design podom u dezenu betona' ),
	array( 'f' => 'img12.jpg',
	       't' => 'Poslovni prostor sa EXPONA Design podom',
	       'a' => 'Otvoreni poslovni prostor sa sedećim delom i EXPONA Design podom u dezenu drveta' ),
	array( 'f' => 'img05.jpg',
	       't' => 'Prodajni prostor sa EXPONA Design podom',
	       'a' => 'Veliki prodajni prostor sa stubovima i tamnim EXPONA Design vinil podom' ),
	array( 'f' => 'img16.jpg',
	       't' => 'Kafić sa EXPONA Design podom',
	       'a' => 'Kafić sa lučnim prozorom, barskim stolicama i tamnim EXPONA Design podom' ),
	array( 'f' => 'img22.jpg',
	       't' => 'Salon sa EXPONA Design podom',
	       'a' => 'Salon sa belim stolicama i EXPONA Design podom u dezenu betona' ),
);

function al_f2d_import( $src, $title, $alt, $apply ) {
	$up   = wp_upload_dir();
	$name = sanitize_title( $title ) . '.' . al_target_ext( $src );
	$dest = $up['path'] . '/' . $name;
	$ex   = attachment_url_to_postid( $up['url'] . '/' . $name );
	if ( $ex ) { WP_CLI::log( "  postoji #$ex $name" ); return $ex; }
	if ( ! file_exists( $src ) ) { WP_CLI::warning( 'NEMA: ' . $src ); return 0; }
	if ( ! $apply ) { WP_CLI::log( "  [proba] uvezao bih $name" ); return 0; }

	$editor = wp_get_image_editor( $src );
	if ( is_wp_error( $editor ) ) { WP_CLI::warning( 'editor: ' . $src ); return 0; }
	$sz = $editor->get_size();
	if ( max( $sz['width'], $sz['height'] ) > 1600 ) {
		$editor->resize( $sz['width'] >= $sz['height'] ? 1600 : null, $sz['height'] > $sz['width'] ? 1600 : null, false );
	}
	$editor->set_quality( 82 );
	$saved = $editor->save( $dest, al_target_mime( $dest ) );
	if ( is_wp_error( $saved ) ) { WP_CLI::warning( 'save: ' . $src ); return 0; }

	$id = wp_insert_attachment( array(
		'post_mime_type' => $saved['mime-type'], 'post_title' => $title, 'post_status' => 'inherit',
	), $saved['path'], 0 );
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $saved['path'] ) );
	update_post_meta( $id, '_wp_attachment_image_alt', $alt );
	WP_CLI::log( sprintf( '  uvezeno #%d %s (%dx%d)', $id, $name, $saved['width'], $saved['height'] ) );
	return $id;
}

WP_CLI::log( PHP_EOL . '== 2. slike EXPONA Design (iz brošure) ==' );
$mainId = 0; $gal = array();
foreach ( $IMGS as $im ) {
	$id = al_f2d_import( $TMP . '/' . $im['f'], $im['t'], $im['a'], $APPLY );
	if ( ! $id ) { continue; }
	if ( ! empty( $im['main'] ) ) { $mainId = $id; } else { $gal[] = $id; }
}

// ------------------------------------------------------- 3. sadržaj proizvoda 16918
$post = get_post( 16918 );
$c    = $post->post_content;
$orig = $c;

// (a) netačna napomena — briše se u celosti
$note = '#<p><em>Napomena: kompletan tehnički list.*?</em></p>\s*#s';
if ( preg_match( $note, $c ) ) {
	$c = preg_replace( $note, '', $c, 1 );
	WP_CLI::log( PHP_EOL . '== 3. sadržaj 16918 ==' . PHP_EOL . '  netačna napomena uklonjena' );
} else {
	WP_CLI::warning( 'napomena nije nađena na 16918 — proveriti ručno' );
}

// (b) tabela dobija vrednosti koje se u tehničkom listu čitaju nedvosmisleno.
//     Požarna klasa je NAMERNO izostavljena: u izvučenom tekstu stoji „B - s1", a za
//     podne obloge EN 13501-1 koristi indeks „fl" (Bfl-s1) koji se pri izvlačenju
//     teksta gubi — radije prazno nego pogrešna klasa. Ostatak pokriva link na PDF.
$rowAnchor = '<tr><th>Montaža</th><td>Punopovršinsko lepljenje</td></tr>';
$newRows   = '<tr><th>Broj dezena</th><td>42</td></tr>'
	. '<tr><th>Klasa upotrebe (EN ISO 10874)</th><td>23 (stambeno) / 34 (komercijalno) / 43 (industrijsko)</td></tr>'
	. '<tr><th>Protivkliznost</th><td>R10 (DIN 51130) · DS (EN 13893)</td></tr>'
	. $rowAnchor;
if ( false !== strpos( $c, $rowAnchor ) ) {
	$c = str_replace( $rowAnchor, $newRows, $c );
	WP_CLI::log( '  tabela dopunjena sa 3 reda iz tehničkog lista' );
}

// (c) sekcija dokumentacije — isti obrazac kao 16917 (h2 + ul, posle „Standardi")
$docs = '<h2>Tehnička dokumentacija</h2><ul>';
foreach ( $PDFS as $p ) {
	$url = $BASE . '/wp-content/uploads/' . str_replace( '%2F', '/', rawurlencode( $p['file'] ) );
	$docs .= '<li><a href="' . $url . '" target="_blank">' . esc_html( $p['label'] ) . '</a></li>';
}
$docs .= '</ul>' . "\n\n";

if ( false === strpos( $c, 'Tehnička dokumentacija' ) ) {
	$c = preg_replace( '#(?=<h2>Česta pitanja</h2>)#', $docs, $c, 1 );
	WP_CLI::log( '  dodata sekcija „Tehnička dokumentacija" (2 PDF-a)' );
}

WP_CLI::log( sprintf( '  sadržaj %d → %d bajtova', strlen( $orig ), strlen( $c ) ) );

if ( $APPLY ) {
	$bk = 'C:/Users/Miroslav/AppData/Local/Temp/al-content-backup';
	if ( ! is_dir( $bk ) ) { mkdir( $bk, 0777, true ); }
	file_put_contents( $bk . '/16918-' . date( 'Ymd-His' ) . '.html', $orig );
	wp_update_post( array( 'ID' => 16918, 'post_content' => $c ) );
	if ( $mainId ) { update_post_meta( 16918, '_thumbnail_id', $mainId ); }
	if ( $gal )    { update_post_meta( 16918, '_product_image_gallery', implode( ',', $gal ) ); }
	WP_CLI::log( sprintf( '  upisano — glavna slika #%d, galerija %d slika', $mainId, count( $gal ) ) );
}

WP_CLI::log( PHP_EOL . '🔴 16919 EXPONA Living Clic: nijedan dokument ni fotografija za tu kolekciju'
	. PHP_EOL . '   ne postoje na disku — napomena je tamo TAČNA i ostaje. Prijaviti M-u.' );

WP_CLI::success( $APPLY ? 'F2.2 + F2.4 upisano' : 'PROBA — ništa nije upisano (dodaj `apply`)' );
