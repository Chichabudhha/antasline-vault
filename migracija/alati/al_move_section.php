<?php
/**
 * Premešta jednu `[vc_row]` sekciju unutar stranice.
 *
 *   wp eval-file al_move_section.php 16589,1,5          # PROBA: iz indeksa 1 pre indeksa 5
 *   wp eval-file al_move_section.php 16589,1,5 apply
 *
 * Indeksi su iz podele `preg_split('#(?=\[vc_row)#')` — isti brojevi koje ispisuje
 * `struktura` skript. Cilj se računa NAD ORIGINALNOM podelom: sekcija se prvo izvadi
 * pa umetne pre elementa koji je u polaznom nizu bio na zadatom mestu.
 */
list( $id, $from, $to ) = array_map( 'intval', explode( ',', $args[0] ?? '' ) );
$apply = ( ( $args[1] ?? '' ) === 'apply' );

$post = get_post( $id );
if ( ! $post ) { WP_CLI::error( 'Nema posta ' . $id ); }

$parts = preg_split( '#(?=\[vc_row)#', $post->post_content );
if ( ! isset( $parts[ $from ] ) ) { WP_CLI::error( 'Nema dela ' . $from ); }

$moved  = $parts[ $from ];
$target = $parts[ $to ] ?? null;

array_splice( $parts, $from, 1 );
$at = ( null === $target ) ? count( $parts ) : array_search( $target, $parts, true );
if ( false === $at ) { WP_CLI::error( 'Ciljni deo nije nađen posle vađenja' ); }
array_splice( $parts, $at, 0, array( $moved ) );

preg_match( '#el_class="([^"]*)"#', $moved, $c );
WP_CLI::log( sprintf( 'premešta se: %s', $c[1] ?? '-' ) );
foreach ( $parts as $i => $s ) {
	if ( '' === trim( $s ) ) { continue; }
	preg_match( '#el_class="([^"]*)"#', $s, $cc );
	preg_match( '#<h([12])[^>]*>(.*?)</h\1>#is', $s, $h );
	WP_CLI::log( sprintf( '  [%d] %-50s %s', $i, $cc[1] ?? '-',
		mb_substr( trim( wp_strip_all_tags( $h[2] ?? '' ) ), 0, 46 ) ) );
}

if ( ! $apply ) { WP_CLI::success( 'PROBA — ništa nije upisano.' ); return; }

$bk = 'C:/Users/Miroslav/AppData/Local/Temp/al-content-backup';
if ( ! is_dir( $bk ) ) { mkdir( $bk, 0777, true ); }
file_put_contents( $bk . '/' . $id . '-' . date( 'Ymd-His' ) . '.html', $post->post_content );

wp_update_post( array( 'ID' => $id, 'post_content' => implode( '', $parts ) ) );
WP_CLI::success( sprintf( '%d: sekcija premeštena %d → %d', $id, $from, $at ) );
