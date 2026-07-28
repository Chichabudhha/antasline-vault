<?php
/**
 * Uvoz fotografija iz foldera u WP medijateku + ubacivanje galerije u stranicu.
 *
 * Ulaz: JSON posao (putanja se prosleđuje kao $args[0] preko `wp eval-file … <json>`)
 * {
 *   "post_id": 16657,
 *   "anchor": "<h2>Naši izvedeni tereni</h2>",   // posle čega se ubacuje (opciono)
 *   "position": "after|end",
 *   "heading": "Naši izvedeni tereni",
 *   "intro": "…",
 *   "columns": 3,
 *   "images": [ {"src":"C:/…/foo.jpg","alt":"…","title":"…"}, … ]
 * }
 *
 * Pravila:
 *  - originali se ne diraju, kopija ide u uploads/YYYY/MM/
 *  - max 1600px duže stranice (lightbox meta), WebP q82 — bez uvećavanja
 *  - alt je OBAVEZAN (SEO/a11y); title priloga služi kao natpis u lightbox-u
 *  - ako fajl sa istim ciljnim imenom već postoji, prilog se ponovo koristi (bez duplikata)
 */

$jsonPath = $args[0] ?? '';
if ( ! $jsonPath || ! file_exists( $jsonPath ) ) { WP_CLI::error( 'Nema JSON posla: ' . $jsonPath ); }
$job = json_decode( file_get_contents( $jsonPath ), true );
if ( ! $job ) { WP_CLI::error( 'Neispravan JSON' ); }

require_once ABSPATH . 'wp-admin/includes/image.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/media.php';
require_once __DIR__ . '/al_webp.php';

$post = get_post( $job['post_id'] );
if ( ! $post ) { WP_CLI::error( 'Nema posta ' . $job['post_id'] ); }

// ---- backup sadržaja pre izmene ----
// Stabilna putanja — ranije je pokazivala na scratchpad JEDNE sesije, pa je backup
// posle te sesije padao u folder koji više niko ne gleda.
$bkDir = 'C:/Users/Miroslav/AppData/Local/Temp/al-content-backup';
if ( ! is_dir( $bkDir ) ) { mkdir( $bkDir, 0777, true ); }
file_put_contents( $bkDir . '/' . $post->ID . '-' . date( 'Ymd-His' ) . '.html', $post->post_content );

$uploadDir = wp_upload_dir();
$ids = array();

foreach ( $job['images'] as $img ) {
	$src = $img['src'];
	if ( ! file_exists( $src ) ) { WP_CLI::warning( 'NEMA: ' . $src ); continue; }

	// ciljno ime: slug iz alt-a (čitljivo, SEO), bez dijakritika
	$slug = sanitize_title( $img['title'] ?? pathinfo( $src, PATHINFO_FILENAME ) );
	$ext  = al_target_ext( $src );
	$name = $slug . '.' . $ext;
	$dest = $uploadDir['path'] . '/' . $name;

	// već uvezeno ranije? iskoristi postojeći prilog
	$existing = attachment_url_to_postid( $uploadDir['url'] . '/' . $name );
	if ( $existing ) {
		$ids[] = array( 'id' => $existing, 'alt' => $img['alt'] );
		WP_CLI::log( 'postoji: ' . $name );
		continue;
	}

	// ---- skaliranje na max 1600px duže stranice, bez uvećavanja ----
	$info = getimagesize( $src );
	if ( ! $info ) { WP_CLI::warning( 'nije slika: ' . $src ); continue; }
	list( $w, $h ) = $info;

	// 🔴 EXIF orijentacija se MORA primeniti ručno. `WP_Image_Editor` je ne primenjuje
	// pri `load()` — WordPress to radi samo kroz `wp_create_image_subsizes()`, a ovde
	// se editor poziva direktno. Veliki deo arhive snimljen telefonom nosi
	// `Orientation: 6` (rotacija za 90°): bez ovoga fotke legnu BOČNO na stranicu, a
	// `getimagesize()` i dalje prijavljuje "landscape" pa se ni po brojkama ne primeti.
	$exifRot = false;
	if ( preg_match( '/\.jpe?g$/i', $src ) && function_exists( 'exif_read_data' ) ) {
		$ex      = @exif_read_data( $src );
		$exifRot = ! empty( $ex['Orientation'] ) && (int) $ex['Orientation'] > 1;
	}

	// Deo arhive (npr. `novi sajt/podloge za parking`) je VEĆ WebP i već ispod 1600px.
	// Tu nema šta da se radi — prekodiranje bi bilo čist gubitak generacije, pa se
	// fajl samo kopira.
	$srcMime  = $info['mime'] ?? '';
	$needsFit = max( $w, $h ) > 1600;
	if ( ! $needsFit && ! $exifRot && $srcMime === al_target_mime( $dest ) ) {
		if ( ! copy( $src, $dest ) ) { WP_CLI::warning( 'kopija: ' . $src ); continue; }
		$saved = array( 'path' => $dest, 'width' => $w, 'height' => $h, 'mime-type' => $srcMime );
		WP_CLI::log( '  (kopirano bez prekodiranja)' );
	} else {
		$editor = wp_get_image_editor( $src );
		if ( is_wp_error( $editor ) ) { WP_CLI::warning( 'editor: ' . $src ); continue; }

		if ( $exifRot && method_exists( $editor, 'maybe_exif_rotate' ) ) {
			$editor->maybe_exif_rotate();
			$sz = $editor->get_size();          // posle rotacije se širina i visina zamene
			$w  = $sz['width'];
			$h  = $sz['height'];
			$needsFit = max( $w, $h ) > 1600;
			WP_CLI::log( '  (EXIF rotacija primenjena)' );
		}

		if ( $needsFit ) {
			$editor->resize( $w >= $h ? 1600 : null, $h > $w ? 1600 : null, false );
		}
		$editor->set_quality( 82 );
		$saved = $editor->save( $dest, al_target_mime( $dest ) );
		if ( is_wp_error( $saved ) ) { WP_CLI::warning( 'save: ' . $src ); continue; }
	}
	$dest = $saved['path'];

	$att = array(
		'post_mime_type' => $saved['mime-type'],
		'post_title'     => $img['title'] ?? $img['alt'],
		'post_content'   => '',
		'post_status'    => 'inherit',
	);
	$id = wp_insert_attachment( $att, $dest, 0 );
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $dest ) );
	update_post_meta( $id, '_wp_attachment_image_alt', $img['alt'] );
	$ids[] = array( 'id' => $id, 'alt' => $img['alt'] );
	WP_CLI::log( sprintf( 'uvezeno #%d  %s  (%dx%d → %s)', $id, $name, $w, $h, $saved['width'] . 'x' . $saved['height'] ) );
}

if ( ! $ids ) { WP_CLI::error( 'Nijedna slika nije uvezena' ); }

// ---- sastavi HTML blok ----
$cols = (int) ( $job['columns'] ?? 3 );
$html = '';
if ( ! empty( $job['label'] ) ) {
	$html .= '<span class="al-label">' . esc_html( $job['label'] ) . '</span>';
}
if ( ! empty( $job['heading'] ) ) {
	$html .= '<h2>' . esc_html( $job['heading'] ) . '</h2>';
}
if ( ! empty( $job['intro'] ) ) {
	$html .= '<p>' . $job['intro'] . '</p>';
}
$html .= '<div class="al-grid al-grid--' . $cols . '" style="margin:24px 0">';
foreach ( $ids as $it ) {
	// namerno gола <img> — al_enhance_content_images() ga sam umotava u lightbox
	// link i dodaje srcset/width/height, isto kao za sve ostale slike na sajtu
	$src = wp_get_attachment_image_url( $it['id'], 'full' );
	$html .= '<img src="' . esc_url( $src ) . '" alt="' . esc_attr( $it['alt'] ) . '" />';
}
$html .= '</div>';

// ---- ubaci u sadržaj ----
//
// `before` je u praksi jedini oblik koji treba: stranice su građene od `[vc_row]`
// sekcija, pa se nova galerija umeće kao ZASEBNA sekcija pre one koja je prepoznata
// po nizu (npr. "footer-top", "najčešća pitanja"). Dodavanje na kraj `post_content`-a
// bi je ubacilo IZVAN poslednjeg `[vc_row]`, gde nema ni širinu ni razmake sekcije.
$content = $post->post_content;

// Stranice građene po WoodMart šablonu traže `al-section` klasu (i smenu paper/mist);
// starije stranice su običan [vc_row]. Bez `section_class` ostaje neutralan red.
// F7.20: NE dodavati `al-diag-*` ako susedna sekcija već nosi rez.
// `raw` za postove koji uopšte nemaju WPBakery markup (npr. 6588): tamo ubacivanje
// `[vc_row]` nosi rizik da se shortcode ne obradi i ispiše kao goli tekst.
if ( ! empty( $job['raw'] ) ) {
	$section = $html;
} elseif ( empty( $job['section_class'] ) ) {
	$section = '[vc_row][vc_column][vc_column_text]' . $html . '[/vc_column_text][/vc_column][/vc_row]';
} else {
	$section = '[vc_row full_width="stretch_row" el_class="' . esc_attr( $job['section_class'] ) . '"][vc_column][vc_column_text]'
		. $html . '[/vc_column_text][/vc_column][/vc_row]';
}

if ( ! empty( $job['before'] ) ) {
	// 🔴 Traži se POSLEDNJI pogodak, ne prvi. Namera je uvek „pred kraj stranice", a
	// isti niz se po pravilu javlja i ranije: na 16589 je `al-section--navy` i hero
	// sekcija sa H1 i završni CTA — prvi pogodak je ubacio galeriju IZNAD H1.
	$parts = preg_split( '#(?=\[vc_row)#', $content );
	$at    = null;
	foreach ( $parts as $i => $p ) {
		if ( false !== mb_stripos( $p, $job['before'] ) ) { $at = $i; }
	}
	if ( null === $at ) {
		WP_CLI::warning( 'Nije nađeno "' . $job['before'] . '" — sekcija ide na kraj' );
		$at = count( $parts );
	}
	array_splice( $parts, $at, 0, array( $section ) );
	$content = implode( '', $parts );
	$where   = sprintf( 'pre "%s" (pozicija %d/%d)', $job['before'], $at, count( $parts ) );
} elseif ( ! empty( $job['anchor'] ) && strpos( $content, $job['anchor'] ) !== false ) {
	$content = str_replace( $job['anchor'], $job['anchor'] . $html, $content );
	$where = 'posle sidra';
} else {
	$content .= $section;
	$where = 'na kraj kao nova sekcija';
}

wp_update_post( array( 'ID' => $post->ID, 'post_content' => $content ) );
WP_CLI::success( sprintf( '%d: ubačeno %d slika (%s)', $post->ID, count( $ids ), $where ) );
