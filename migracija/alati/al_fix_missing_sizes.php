<?php
/**
 * Popravlja priloge kojima u metapodacima piše veličina čiji fajl ne postoji.
 *
 *   wp eval-file al_fix_missing_sizes.php          # PROBA
 *   wp eval-file al_fix_missing_sizes.php apply
 *
 * Nastalo 2026-07-28: prvo izdanje `al_regen_sizes.php` brisalo je stari al-* fajl
 * ne proverivši da li ista putanja služi i nekoj drugoj veličini. WordPress deli
 * fajl između veličina istih dimenzija (`al-sm` i `woocommerce_single` su oba bili
 * `…-600x400.jpg`), pa je brisanje ostavljalo 404 na WooCommerce slikama.
 * `al_regen_sizes.php` je popravljen; ovaj skript sanira zatečeno stanje i korisno
 * je pustiti ga i kao običnu proveru zdravlja medijateke.
 */

require_once ABSPATH . 'wp-admin/includes/image.php';

// Opseg: podrazumevano SAMO prilozi kroz koje je prošao al_regen_sizes.php (imaju
// `al-xs`) — to je jedini skup koji je ovaj bag mogao da ošteti. `all` pregleda celu
// medijateku, ali tamo ima i zatečenih rupa (npr. `-scaled.jpg` prilozi kojima fali
// svih 26 veličina) koje nisu posledica ovog rada i ne treba ih mešati u popravku.
$apply = in_array( 'apply', (array) $args, true );
$all   = in_array( 'all', (array) $args, true );

$ids = $GLOBALS['wpdb']->get_col(
	"SELECT ID FROM {$GLOBALS['wpdb']->posts}
	 WHERE post_type = 'attachment' AND post_mime_type LIKE 'image/%'"
);
if ( ! $all ) {
	$ids = array_values( array_filter( $ids, function ( $id ) {
		$m = wp_get_attachment_metadata( $id );
		return is_array( $m ) && isset( $m['sizes']['al-xs'] );
	} ) );
}
WP_CLI::log( sprintf( '%s: %d priloga za pregled', $apply ? 'POPRAVKA' : 'PROBA (bez izmena)', count( $ids ) ) );

$brokenAtt = 0;
$brokenSz  = 0;
$fixed     = 0;
$failed    = array();

foreach ( $ids as $id ) {
	$file = get_attached_file( $id );
	$meta = wp_get_attachment_metadata( $id );
	if ( ! is_array( $meta ) || empty( $meta['sizes'] ) || ! $file ) { continue; }

	$dir     = dirname( $file );
	$missing = array();
	foreach ( $meta['sizes'] as $name => $s ) {
		if ( empty( $s['file'] ) || ! file_exists( $dir . '/' . $s['file'] ) ) {
			$missing[ $name ] = $s;
		}
	}
	if ( ! $missing ) { continue; }

	$brokenAtt++;
	$brokenSz += count( $missing );

	if ( ! $apply ) {
		if ( $brokenAtt <= 15 ) {
			WP_CLI::log( sprintf( '  #%d %s → %s', $id, basename( $file ), implode( ', ', array_keys( $missing ) ) ) );
		}
		continue;
	}

	if ( ! file_exists( $file ) ) { $failed[] = "$id: nema ni original"; continue; }

	$editor = wp_get_image_editor( $file );
	if ( is_wp_error( $editor ) ) { $failed[] = "$id: editor"; continue; }

	// tražimo tačno one dimenzije koje su upisane u metapodacima, da ostane isti kadar
	$req = array();
	foreach ( $missing as $name => $s ) {
		$reg = wp_get_registered_image_subsizes()[ $name ] ?? null;
		$req[ $name ] = $reg ? $reg : array(
			'width'  => (int) $s['width'],
			'height' => (int) $s['height'],
			'crop'   => true,
		);
	}

	$made = $editor->multi_resize( $req );
	if ( ! $made ) { $failed[] = "$id: multi_resize prazan"; continue; }

	foreach ( $made as $name => $info ) {
		$meta['sizes'][ $name ] = $info;
		$fixed++;
	}
	// veličine koje ni posle ovoga nemaju fajl — bolje ih ukloniti iz zapisa nego
	// ostaviti WP da generiše URL ka nepostojećem fajlu
	foreach ( array_keys( $missing ) as $name ) {
		if ( ! isset( $made[ $name ] ) && isset( $meta['sizes'][ $name ] )
			&& ! file_exists( $dir . '/' . $meta['sizes'][ $name ]['file'] ) ) {
			unset( $meta['sizes'][ $name ] );
		}
	}
	wp_update_attachment_metadata( $id, $meta );
}

foreach ( array_slice( $failed, 0, 15 ) as $f ) { WP_CLI::warning( $f ); }

WP_CLI::success( sprintf(
	'%s priloga sa nedostajućim veličinama: %d (ukupno %d veličina).%s',
	$apply ? 'Popravljeno' : 'Nađeno',
	$brokenAtt,
	$brokenSz,
	$apply ? sprintf( ' Regenerisano %d, neuspelo %d.', $fixed, count( $failed ) ) : ''
) );
