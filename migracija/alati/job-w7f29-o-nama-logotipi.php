<?php
/**
 * W7 F2.9 — „O nama" (571): logotipi umesto golog teksta.
 *
 * Poziv:  php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-w7f29-o-nama-logotipi.php [apply]
 * Bez `apply` = proba.
 *
 * Dve komponente, obe već postoje u dizajn sistemu (`.al-logo-row`, antas-design.css:441 —
 * grayscale + hover, visina normalizovana na 46px):
 *   (a) logotipi proizvođača koje stranica IMENUJE u pasusu „Ko smo mi?"
 *   (b) logotipi klijenata koji su VEĆ navedeni imenom u „Reference → Industrija"
 *
 * 🔴 Pravilo: dodaje se samo logotip firme koju stranica već pominje imenom.
 * `Mup-logo.webp` (15351) postoji u medijateci ali MUP nije naveden među referencama
 * → NIJE dodat (ne izmišlja se klijent kog M nije naveo).
 */

$APPLY = in_array( 'apply', (array) ( $args ?? array() ), true );
$ID    = 571;
$BDIR  = WP_CONTENT_DIR . '/../scratchpad/content-backup';
if ( ! is_dir( $BDIR ) ) { mkdir( $BDIR, 0777, true ); }

echo $APPLY ? "=== REŽIM: APPLY ===\n\n" : "=== REŽIM: PROBA (bez izmena) ===\n\n";

/* ---------------------------------------------------------------- logotipi */

$dobavljaci = array(
	5962  => 'Bergo Flooring',
	5980  => 'Ecotile Flooring',
	12258 => 'Sit-in by Radici Group',
);

$klijenti = array(
	15343 => 'Robert Bosch',
	12350 => 'Institut Vinča',
	15350 => 'Adient Kragujevac',
	15348 => 'Philip Morris Niš',
	15347 => 'AMSS',
	15346 => 'Orion telekom',
);

/** Gradi `.al-logo-row` iz priloga; preskače ono što nema fajl na disku. */
function al_logo_row( array $logos, &$fale ) {
	$out = '';
	foreach ( $logos as $att => $naziv ) {
		$file = get_attached_file( $att );
		if ( ! $file || ! file_exists( $file ) ) { $fale[] = "{$att} ({$naziv})"; continue; }
		$src  = wp_get_attachment_url( $att );
		$size = @getimagesize( $file );
		$out .= sprintf(
			'<img src="%s" alt="%s" width="%d" height="%d" loading="lazy" />',
			esc_url( $src ), esc_attr( $naziv ), $size[0], $size[1]
		);
	}
	return $out ? '<div class="al-logo-row">' . $out . '</div>' : '';
}

$fale = array();
$row_dob = al_logo_row( $dobavljaci, $fale );
$row_kli = al_logo_row( $klijenti, $fale );

if ( $fale ) { echo "🔴 bez fajla na disku, preskočeno: " . implode( ', ', $fale ) . "\n\n"; }

/* ------------------------------------------------------------------ umetanje */

$post = get_post( $ID );
$c    = $post->post_content;
$log  = array();

/* (a) proizvođači — iza pasusa „Ko smo mi?", pre „Šta nudimo?" */
$marker_a = '<h2>Šta nudimo?</h2>';
if ( strpos( $c, 'al-logo-row' ) !== false ) {
	echo "  =  stranica već ima .al-logo-row — ništa se ne dira\n";
} elseif ( strpos( $c, $marker_a ) === false ) {
	echo "  🔴 marker „Šta nudimo?\" nije nađen — logotipi proizvođača NISU dodati\n";
} elseif ( $row_dob ) {
	$c     = str_replace( $marker_a, $row_dob . "\n\n" . $marker_a, $c );
	$log[] = 'logotipi proizvođača (' . count( $dobavljaci ) . ') umetnuti pre „Šta nudimo?"';
}

/* (b) klijenti — odmah iza <h2>Reference</h2>, pre <h3>Industrija</h3> */
$marker_b = '<h3>Industrija</h3>';
if ( strpos( $c, 'al-logo-row--klijenti' ) !== false ) {
	echo "  =  red sa klijentima već postoji\n";
} elseif ( strpos( $c, $marker_b ) === false ) {
	echo "  🔴 marker „<h3>Industrija</h3>\" nije nađen — logotipi klijenata NISU dodati\n";
} elseif ( $row_kli ) {
	$uvod  = '<p>Deo firmi za koje smo izveli podove — pun spisak referenci po delatnostima je ispod.</p>' . "\n";
	$blok  = $uvod . str_replace( 'al-logo-row', 'al-logo-row al-logo-row--klijenti', $row_kli ) . "\n\n";
	$c     = str_replace( $marker_b, $blok . $marker_b, $c );
	$log[] = 'logotipi klijenata (' . count( $klijenti ) . ') umetnuti iza „Reference"';
}

foreach ( $log as $l ) { echo "  →  {$l}\n"; }

if ( $log && $APPLY ) {
	file_put_contents( "{$BDIR}/{$ID}-" . date( 'Ymd-His' ) . '.txt', $post->post_content );
	wp_update_post( array( 'ID' => $ID, 'post_content' => $c ) );
	echo "\n  upisano (bekap u {$BDIR})\n";
}

/* ------------------------------------------------------------------ kontrola */

echo "\n--- kontrola ---\n";
$now = get_post( $ID )->post_content;
printf( "  .al-logo-row redova: %d\n", substr_count( $now, 'class="al-logo-row' ) );
printf( "  <img> ukupno:        %d\n", substr_count( $now, '<img' ) );
echo "\nGotovo." . ( $APPLY ? '' : ' (proba — ništa nije upisano)' ) . "\n";
