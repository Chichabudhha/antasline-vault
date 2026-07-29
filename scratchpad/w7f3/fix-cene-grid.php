<?php
/**
 * W7 F3 — popravka mreže na /cene/ (17273).
 * `<span class="al-card__body">` sa <h3>/<p> unutra → wpautop umota svaki <a> u <p>
 * i napravi prazna polja mreže. Blok-obrazac (<div class="al-card"><div class="al-card__body">)
 * je isti koji koriste 16684 i ostale stranice i wpautop ga ne dira.
 */
$apply = in_array( 'apply', $args, true );
$id    = 17273;

$c   = get_post_field( 'post_content', $id );
$dir = 'C:/Projekti/antasline-vault/scratchpad/content-backup';
if ( ! is_dir( $dir ) ) { mkdir( $dir, 0777, true ); }

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

if ( ! preg_match( '#(<div class="al-grid al-grid--2"[^>]*>)(.*?)(</div>\s*\n\[/vc_column_text\])#s', $c, $m ) ) {
	echo "🔴 STOP: mreža nije pronađena u sadržaju.\n";
	return;
}

echo "=== PROBA ===\n";
echo 'staro (prvih 160 B): ' . substr( $m[2], 0, 160 ) . "\n\n";
echo 'novo  (prvih 160 B): ' . substr( $novi, 0, 160 ) . "\n";
echo 'blok-tagova u inline <span>: staro ' . preg_match_all( '#<span class="al-card__body">#', $m[2] ) . ' → novo 0' . "\n";

if ( ! $apply ) { echo "\n(bez 'apply' — ništa nije upisano)\n"; return; }

file_put_contents( "$dir/$id-pre-grid-fix-2026-07-29.txt", $c );
global $wpdb;
$wpdb->update( $wpdb->posts, array( 'post_content' => $m[1] . $novi . $m[3] ), array( 'ID' => $id ) );
clean_post_cache( $id );
echo "\n✅ 17273: mreža prepisana blok-obrascem (bekap u scratchpad/content-backup/)\n";
