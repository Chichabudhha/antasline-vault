<?php
/**
 * Meni SVG ikonice — zamena FontAwesome (_menu_item_icon) custom brend SVG-ovima.
 *
 * Svaka od 79 stavki menija (term 390) dobija sopstvenu SVG ikonicu (24x24,
 * stroke #F04D22, isti stil kao postojeći icons/namena-*.svg) umesto
 * generičkih FontAwesome glifova — rešava duplikate (npr. tenis/stoni tenis
 * su imali istu ikonicu) i "previše generično" primedbu.
 *
 * Mehanizam: WoodMart nativna "Menu item icon" slika (_menu_item_image-type=image
 * + featured image), ne font-icon sistem. Fajlovi kopirani u uploads (pravi WP
 * attachment-i, isti obrazac kao import-gemini-photo.php), stari _menu_item_icon
 * (FA) obrisan da se ne renderuje duplo.
 *
 * Proba:      eval-file job-meni-svg-ikonice.php
 * Izvršenje:  eval-file job-meni-svg-ikonice.php apply
 */
$apply = in_array( 'apply', $args, true );

$theme_icons_dir = get_stylesheet_directory() . '/images/icons/';
$theme_icons_url = get_stylesheet_directory_uri() . '/images/icons/';

/**
 * ID stavke menija => putanja fajla relativna na images/icons/
 * ('menu/xyz.svg' = novo napravljene; ostalo = postojeći namena-*.svg reused).
 */
$map = array(
	// TOP (6)
	17349 => 'menu/top-sport.svg',
	17371 => 'menu/top-industrija.svg',
	17391 => 'menu/top-terase.svg',
	17401 => 'menu/top-poslovni.svg',
	17412 => 'menu/top-specijalni.svg',
	17421 => 'menu/top-cene.svg',
	// SUB (14)
	17350 => 'namena-sportski-teren-otvoreni.svg',
	17360 => 'menu/sub-podloge-oprema.svg',
	17366 => 'menu/sub-dimenzije.svg',
	17372 => 'menu/sub-po-delatnosti.svg',
	17379 => 'menu/sub-ecotile.svg',
	17385 => 'menu/sub-oprema-saveti.svg',
	17392 => 'menu/sub-bergo-ploce.svg',
	17397 => 'namena-terasa.svg',
	17402 => 'menu/sub-expona.svg',
	17407 => 'menu/sub-storefront.svg',
	17413 => 'menu/sub-truck.svg',
	17417 => 'menu/sub-road.svg',
	17422 => 'menu/sub-cene-industrija.svg',
	17425 => 'menu/sub-cene-spolja.svg',
	// LEAF (59)
	17351 => 'menu/leaf-basketball-ball.svg',
	17352 => 'menu/leaf-3x3.svg',
	17353 => 'menu/leaf-tennis.svg',
	17354 => 'menu/leaf-padel.svg',
	17355 => 'menu/leaf-pickleball.svg',
	17356 => 'menu/leaf-futsal.svg',
	17357 => 'menu/leaf-hockey.svg',
	17358 => 'menu/leaf-tabletennis.svg',
	17359 => 'namena-sport-dvorana.svg',
	17361 => 'menu/leaf-tile-studs.svg',
	17362 => 'menu/leaf-grass-wide.svg',
	17363 => 'menu/leaf-whistle.svg',
	17364 => 'menu/leaf-floodlight.svg',
	17365 => 'menu/leaf-gallery.svg',
	17367 => 'menu/leaf-ruler-ball.svg',
	17368 => 'menu/leaf-backboard-dim.svg',
	17369 => 'menu/leaf-ruler-vert.svg',
	17370 => 'menu/leaf-ruler-horiz.svg',
	17373 => 'namena-magacin-hala.svg',
	17374 => 'namena-garaza.svg',
	17375 => 'menu/leaf-flask.svg',
	17376 => 'menu/leaf-health.svg',
	17377 => 'menu/leaf-dumbbell.svg',
	17378 => 'menu/leaf-landmark.svg',
	17380 => 'menu/leaf-tile-overview.svg',
	17381 => 'menu/leaf-tile-thin.svg',
	17382 => 'menu/leaf-tile-medium.svg',
	17383 => 'menu/leaf-tile-thick.svg',
	17384 => 'namena-esd.svg',
	17386 => 'menu/leaf-tape.svg',
	17387 => 'menu/leaf-shield.svg',
	17388 => 'menu/leaf-footprints.svg',
	17389 => 'namena-radionica.svg',
	17390 => 'menu/leaf-question.svg',
	17393 => 'menu/leaf-tile-grid-overview.svg',
	17394 => 'menu/leaf-tile-dot.svg',
	17395 => 'menu/leaf-tile-diamond.svg',
	17396 => 'menu/leaf-tile-x.svg',
	17398 => 'menu/leaf-pool.svg',
	17399 => 'menu/leaf-leaf.svg',
	17400 => 'menu/leaf-grass-swatches.svg',
	17403 => 'menu/leaf-plank-clic.svg',
	17404 => 'menu/leaf-plank-commercial.svg',
	17405 => 'menu/leaf-plank-flow.svg',
	17406 => 'menu/leaf-plank-simplay.svg',
	17408 => 'menu/leaf-desk.svg',
	17409 => 'menu/leaf-cup.svg',
	17410 => 'menu/leaf-bag.svg',
	17411 => 'menu/leaf-cart.svg',
	17414 => 'menu/leaf-cone.svg',
	17415 => 'menu/leaf-tent.svg',
	17416 => 'menu/leaf-exchange.svg',
	17418 => 'menu/leaf-parking.svg',
	17419 => 'menu/leaf-grid-protect.svg',
	17420 => 'namena-stala.svg',
	17423 => 'menu/leaf-factory-small.svg',
	17424 => 'menu/leaf-car.svg',
	17426 => 'menu/leaf-rubber-tile.svg',
	17427 => 'menu/leaf-road-stripe.svg',
);

/* ---------- 1. Provera: svi fajlovi postoje, svi ID-evi su pravi menu item-i ---------- */
$greske = array();
foreach ( $map as $item_id => $rel ) {
	if ( ! file_exists( $theme_icons_dir . $rel ) ) {
		$greske[] = "$item_id -> $rel — FAJL NE POSTOJI";
	}
	$p = get_post( $item_id );
	if ( ! $p || 'nav_menu_item' !== $p->post_type ) {
		$greske[] = "$item_id — nije nav_menu_item";
	}
}
echo '=== PROVERA === stavki: ' . count( $map ) . "\n";
if ( $greske ) {
	echo "🔴 GREŠKE:\n  " . implode( "\n  ", $greske ) . "\n";
	return;
}
echo "svi fajlovi postoje, svi ID-evi su nav_menu_item ✅\n";

if ( ! $apply ) {
	echo "\n(bez 'apply' — ništa nije upisano)\n";
	return;
}

/* ---------- 2. Upload dir za ikonice (dedikovan podfolder, ne meša se sa ostalim medijima) ---------- */
$upload_dir = wp_upload_dir();
$target_dir = $upload_dir['basedir'] . '/meni-ikonice';
if ( ! file_exists( $target_dir ) ) {
	wp_mkdir_p( $target_dir );
}

$path_to_attachment = array(); // dedupe: isti fajl -> isti attachment ID
$upisano = 0;
$greske_upis = array();

foreach ( $map as $item_id => $rel ) {
	$src = $theme_icons_dir . $rel;

	if ( isset( $path_to_attachment[ $rel ] ) ) {
		$attach_id = $path_to_attachment[ $rel ];
	} else {
		$filename  = 'meni-' . sanitize_file_name( basename( $rel ) );
		$dest      = $target_dir . '/' . $filename;
		if ( ! file_exists( $dest ) ) {
			copy( $src, $dest );
		}
		$file_url = $upload_dir['baseurl'] . '/meni-ikonice/' . $filename;

		$attachment = array(
			'post_mime_type' => 'image/svg+xml',
			'post_title'      => sanitize_file_name( basename( $rel, '.svg' ) ),
			'post_content'    => '',
			'post_status'     => 'inherit',
			'guid'            => $file_url,
		);
		$attach_id = wp_insert_attachment( $attachment, $dest );
		if ( is_wp_error( $attach_id ) ) {
			$greske_upis[] = "$item_id ($rel): " . $attach_id->get_error_message();
			continue;
		}
		update_post_meta( $attach_id, '_wp_attached_file', str_replace( $upload_dir['basedir'] . '/', '', $dest ) );
		$path_to_attachment[ $rel ] = $attach_id;
	}

	update_post_meta( $item_id, '_thumbnail_id', $attach_id );
	update_post_meta( $item_id, '_menu_item_image-type', 'image' );
	delete_post_meta( $item_id, '_menu_item_icon' ); // ukloni stari FA icon da se ne duplira

	$upisano++;
}

echo "\n=== UPISANO === $upisano / " . count( $map ) . " stavki\n";
echo 'jedinstvenih attachment-a: ' . count( $path_to_attachment ) . "\n";
if ( $greske_upis ) {
	echo "🔴 Greške pri upisu:\n  " . implode( "\n  ", $greske_upis ) . "\n";
}
