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
 *  - max 1600px duže stranice (lightbox meta), JPEG q82 — bez uvećavanja
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

$post = get_post( $job['post_id'] );
if ( ! $post ) { WP_CLI::error( 'Nema posta ' . $job['post_id'] ); }

// ---- backup sadržaja pre izmene ----
$bkDir = 'C:/Users/Miroslav/AppData/Local/Temp/claude/C--Projekti-antasline-vault/21101aee-b36f-40b9-be7a-84052e879608/scratchpad/content-backup';
if ( ! is_dir( $bkDir ) ) { mkdir( $bkDir, 0777, true ); }
file_put_contents( $bkDir . '/' . $post->ID . '-' . date( 'Ymd-His' ) . '.html', $post->post_content );

$uploadDir = wp_upload_dir();
$ids = array();

foreach ( $job['images'] as $img ) {
	$src = $img['src'];
	if ( ! file_exists( $src ) ) { WP_CLI::warning( 'NEMA: ' . $src ); continue; }

	// ciljno ime: slug iz alt-a (čitljivo, SEO), bez dijakritika
	$slug = sanitize_title( $img['title'] ?? pathinfo( $src, PATHINFO_FILENAME ) );
	$ext  = strtolower( pathinfo( $src, PATHINFO_EXTENSION ) ) === 'png' ? 'png' : 'jpg';
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
	$editor = wp_get_image_editor( $src );
	if ( is_wp_error( $editor ) ) { WP_CLI::warning( 'editor: ' . $src ); continue; }
	if ( max( $w, $h ) > 1600 ) {
		$editor->resize( $w >= $h ? 1600 : null, $h > $w ? 1600 : null, false );
	}
	$editor->set_quality( 82 );
	$saved = $editor->save( $dest );
	if ( is_wp_error( $saved ) ) { WP_CLI::warning( 'save: ' . $src ); continue; }
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
$content = $post->post_content;
if ( ! empty( $job['anchor'] ) && strpos( $content, $job['anchor'] ) !== false ) {
	$content = str_replace( $job['anchor'], $job['anchor'] . $html, $content );
	$where = 'posle sidra';
} elseif ( ( $job['position'] ?? 'end' ) === 'end' ) {
	$content .= $html;
	$where = 'na kraj';
} else {
	WP_CLI::error( 'Sidro nije nađeno: ' . $job['anchor'] );
}

wp_update_post( array( 'ID' => $post->ID, 'post_content' => $content ) );
WP_CLI::success( sprintf( '%d: ubačeno %d slika (%s)', $post->ID, count( $ids ), $where ) );
