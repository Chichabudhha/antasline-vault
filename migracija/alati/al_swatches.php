<?php
/**
 * W7 F2.6 — inline kockice boja → `.al-swatch` komponenta.
 *
 * Poziv:  php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file al_swatches.php [ID|all] [apply]
 *
 * Pretvara:
 *   <div style="display:flex;flex-wrap:wrap;gap:16px;margin-top:20px">
 *     <div style="width:96px;text-align:center">
 *       <div style="width:48px;…;background:#c3c5c6;border:1px solid rgba(0,0,0,0.08);"></div>
 *       <span style="font-size:12px;color:#16283C">Stone Grey</span>
 *     </div>…
 *   </div>
 * u:
 *   <div class="al-swatches">
 *     <div class="al-swatch"><span class="al-swatch__chip" style="background-color:#c3c5c6"></span>
 *       <span class="al-swatch__name">Stone Grey</span></div>…
 *   </div>
 *
 * 🔴 Border NIJE uniforman u izvoru: 16659 ima 2 kockice sa `#D9D9D9` umesto
 * `rgba(0,0,0,0.08)` (bele boje, kojima treba vidljiva ivica). Regex zato prihvata
 * bilo koju vrednost bordera — CSS je posle svejedno ujednačava. Da je regex bio
 * zakucan na `rgba(0,0,0,0.08)`, te 2 kockice bi tiho ostale inline.
 *
 * Skript NE dira ništa što ne poklopi u celosti — ako je broj poklopljenih manji
 * od broja kockica na stranici, prijavljuje razliku umesto da nagađa.
 */

$argv0   = (array) ( $args ?? array() );
$APPLY   = in_array( 'apply', $argv0, true );
$target  = 'all';
foreach ( $argv0 as $a ) { if ( $a !== 'apply' ) { $target = $a; } }

$BDIR = WP_CONTENT_DIR . '/../scratchpad/content-backup';
if ( ! is_dir( $BDIR ) ) { mkdir( $BDIR, 0777, true ); }

echo $APPLY ? "=== REŽIM: APPLY ===\n\n" : "=== REŽIM: PROBA ===\n\n";

global $wpdb;
if ( $target === 'all' ) {
	$ids = $wpdb->get_col(
		"SELECT ID FROM {$wpdb->posts}
		 WHERE post_status='publish' AND post_content LIKE '%width:48px;height:48px%'
		 ORDER BY ID"
	);
} else {
	$ids = array_map( 'intval', explode( ',', $target ) );
}

$re_wrap = '~<div style="display:flex;flex-wrap:wrap;gap:16px;margin-top:\d+px">~';
$re_chip = '~<div style="width:96px;text-align:center"><div style="width:48px;height:48px;border-radius:6px;margin:0 auto 8px;background:([^;"]+);border:1px solid [^;"]+;"></div><span style="font-size:12px;color:#16283C">([^<]*)</span></div>~';

$uk_pre = 0; $uk_post = 0;

foreach ( $ids as $id ) {
	$post = get_post( $id );
	if ( ! $post ) { echo "🔴 {$id}: nema posta\n"; continue; }
	$c   = $post->post_content;
	$pre = substr_count( $c, 'width:48px;height:48px' );
	$uk_pre += $pre;

	$n_chip = 0;
	$new = preg_replace_callback( $re_chip, function ( $m ) use ( &$n_chip ) {
		$n_chip++;
		$boja = trim( $m[1] );
		$ime  = $m[2];
		return '<div class="al-swatch"><span class="al-swatch__chip" style="background-color:' . $boja . '"></span>'
			. '<span class="al-swatch__name">' . $ime . '</span></div>';
	}, $c );

	$n_wrap = 0;
	$new = preg_replace_callback( $re_wrap, function () use ( &$n_wrap ) {
		$n_wrap++;
		return '<div class="al-swatches">';
	}, $new );

	$ostalo = substr_count( $new, 'width:48px;height:48px' );
	$uk_post += $ostalo;

	printf( "%-6s kockica=%-3d pretvoreno=%-3d omotaca=%d %s\n",
		$id, $pre, $n_chip, $n_wrap,
		$ostalo ? "  🔴 OSTALO INLINE: {$ostalo}" : '' );

	if ( $ostalo ) {
		echo "        (ne diram ovu stranicu — prvo utvrditi varijantu)\n";
		continue;
	}
	if ( $n_chip === 0 ) { continue; }

	if ( $APPLY ) {
		file_put_contents( "{$BDIR}/{$id}-swatch-" . date( 'Ymd-His' ) . '.txt', $c );
		wp_update_post( array( 'ID' => $id, 'post_content' => $new ) );
	}
}

echo "\nkockica pre: {$uk_pre}  ·  preostalo inline posle: {$uk_post}\n";
echo $APPLY ? "upisano.\n" : "(proba — ništa nije upisano)\n";
