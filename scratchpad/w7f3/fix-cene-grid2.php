<?php
/**
 * W7 F3 — vraćanje 17273 iz bekapa + ISPRAVNA zamena samo unutrašnjosti mreže.
 * Prethodna verzija je upisala sam isečak kao ceo post_content i obrisala
 * hero + dve sekcije. Ovde se menja SAMO sadržaj `.al-grid--2`, ostatak ostaje.
 */
$apply = in_array( 'apply', $args, true );
$id    = 17273;
$bak   = 'C:/Projekti/antasline-vault/scratchpad/content-backup/17273-pre-grid-fix-2026-07-29.txt';

if ( ! file_exists( $bak ) ) { echo "🔴 STOP: nema bekapa $bak\n"; return; }
$orig = file_get_contents( $bak );

echo "=== BEKAP (izvorno stanje) ===\n";
echo '  vc_row ' . substr_count( $orig, '[vc_row' ) . '/' . substr_count( $orig, '[/vc_row]' )
	. ' · vc_column_text ' . substr_count( $orig, '[vc_column_text]' ) . '/' . substr_count( $orig, '[/vc_column_text]' )
	. ' · dužina ' . strlen( $orig ) . " B\n";

$kartice = array(
	array( 16874, 'Industrijski podovi — cena', 'Cena po m² za Ecotile PVC ploče u magacinima, halama i radionicama — po debljini ploče i opterećenju.' ),
	array( 16873, 'Gumeni podovi za terase — cena', 'Cena podloga za terase, balkone i dvorišta — po vrsti ploče i kvadraturi.' ),
	array( 16876, 'Podloge za parkiralište — cena', 'Geoplast rešetke za parking, prilaze i staze — cena po m² od 2.800 din.' ),
	array( 16875, 'Podovi za garaže — cena', 'Cena podnih ploča za garaže i auto-servise — otporne na ulja, gume i točkove dizalice.' ),
);
$novi = '';
foreach ( $kartice as $k ) {
	$novi .= '<div class="al-card"><div class="al-card__body"><h3><a href="' . esc_url( get_permalink( $k[0] ) ) . '">'
		. $k[1] . '</a></h3><p>' . $k[2] . '</p></div></div>';
}

// Zameni SAMO unutrašnjost mreže, sve ostalo ostaje netaknuto.
$novo = preg_replace_callback(
	'#(<div class="al-grid al-grid--2"[^>]*>)(.*?)(</div>)#s',
	function ( $m ) use ( $novi ) { return $m[1] . $novi . $m[3]; },
	$orig,
	1,
	$count
);

echo "\n=== POSLE ZAMENE ===\n";
echo "  pogodaka mreže: $count\n";
echo '  vc_row ' . substr_count( $novo, '[vc_row' ) . '/' . substr_count( $novo, '[/vc_row]' )
	. ' · vc_column_text ' . substr_count( $novo, '[vc_column_text]' ) . '/' . substr_count( $novo, '[/vc_column_text]' )
	. ' · dužina ' . strlen( $novo ) . " B\n";
echo '  <span class="al-card__body">: ' . substr_count( $novo, '<span class="al-card__body">' ) . " (mora 0)\n";
echo '  <div class="al-card">: ' . substr_count( $novo, '<div class="al-card">' ) . " (mora 4)\n";

if ( 1 !== $count
	|| substr_count( $novo, '[vc_row' ) !== substr_count( $orig, '[vc_row' )
	|| substr_count( $novo, '[vc_column_text]' ) !== substr_count( $orig, '[vc_column_text]' ) ) {
	echo "🔴 STOP: bilans šortkodova se ne poklapa sa izvornim — ništa nije upisano.\n";
	return;
}

if ( ! $apply ) { echo "\n(bez 'apply' — ništa nije upisano)\n"; return; }

global $wpdb;
$wpdb->update( $wpdb->posts, array( 'post_content' => $novo ), array( 'ID' => $id ) );
clean_post_cache( $id );
echo "\n✅ 17273 vraćen iz bekapa sa ispravnom mrežom.\n";
