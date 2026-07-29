<?php
/**
 * W7 F2.7 — grupa „spoljne obloge": thumbnail-i + cross-linkovi ka katalogu + dopuna hub-a.
 *
 * Poziv:  php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-w7f2-spoljne-obloge.php [apply]
 * Bez `apply` = proba (ispisuje šta bi uradio, ne dira bazu).
 *
 * Bekap post_content ide u scratchpad/content-backup/ pre svake izmene (isti obrazac kao al_import.php).
 */

$APPLY = in_array( 'apply', (array) ( $args ?? array() ), true );
$BASE  = 'http://localhost/antasline';
$BDIR  = WP_CONTENT_DIR . '/../scratchpad/content-backup';
if ( ! is_dir( $BDIR ) ) { mkdir( $BDIR, 0777, true ); }

echo $APPLY ? "=== REŽIM: APPLY ===\n\n" : "=== REŽIM: PROBA (bez izmena) ===\n\n";

/* ---------------------------------------------------------------- 1. THUMBNAIL-I */

$thumbs = array(
	16590 => array( 17100, 'hub — terasa sa pogledom na vodu (1600×800)' ),
	16659 => array( 16007, 'Bergo XL terasa (960×540) — zamenjuje pogrešnu bazensku 5057' ),
	16662 => array( 5045,  'Podne obloge oko bazena (800×500)' ),
	16665 => array( 17114, 'Štand na poslovnom sajmu (750×483)' ),
	16673 => array( 5409,  'Veštačka trava za baštu Highlands (786×246)' ),
	16679 => array( 5019,  'Podovi za bašte kafića — Bergo Unique (800×800)' ),
	16681 => array( 5032,  'Podne obloge za terasu Bergo Elite (800×500)' ),
);

echo "--- 1. _thumbnail_id ---\n";
foreach ( $thumbs as $post_id => $d ) {
	list( $att_id, $why ) = $d;

	$file = get_attached_file( $att_id );
	if ( ! $file || ! file_exists( $file ) ) {
		echo "  🔴 {$post_id}: prilog {$att_id} NEMA fajl na disku — preskačem\n";
		continue;
	}
	$old = get_post_meta( $post_id, '_thumbnail_id', true );
	if ( (string) $old === (string) $att_id ) {
		echo "  =  {$post_id}: već {$att_id}\n";
		continue;
	}
	echo "  →  {$post_id}: " . ( $old ? "{$old} → " : 'NEMA → ' ) . "{$att_id}  ({$why})\n";
	if ( $APPLY ) {
		update_post_meta( $post_id, '_thumbnail_id', $att_id );
	}
}

/* ------------------------------------------------- 2. CROSS-LINKOVI KA KATALOGU */

/**
 * Umeće HTML na kraj [vc_column_text] bloka one [vc_row] sekcije koja sadrži zadati anchor.
 */
function al_append_to_section( $content, $anchor, $html, &$log ) {
	$parts = preg_split( '#(?=\[vc_row)#', $content );
	$hit   = -1;
	foreach ( $parts as $i => $p ) {
		if ( $p !== '' && strpos( $p, $anchor ) !== false ) { $hit = $i; break; }
	}
	if ( $hit < 0 ) { $log = "anchor „{$anchor}" . "\" nije nađen"; return false; }

	$tail = '[/vc_column_text][/vc_column][/vc_row]';
	$pos  = strrpos( $parts[ $hit ], $tail );
	if ( $pos === false ) { $log = "sekcija {$hit} nema očekivan rep {$tail}"; return false; }

	$parts[ $hit ] = substr( $parts[ $hit ], 0, $pos ) . $html . substr( $parts[ $hit ], $pos );
	$log = "umetnuto u sekciju {$hit}";
	return implode( '', $parts );
}

$p = function ( $html ) { return "\n" . $html . "\n"; };

$crosslinks = array(
	16659 => array(
		'anchor' => 'Tehnički podaci i primena',
		'html'   => '<p>Ploču iz kataloga sa punom specifikacijom pogledajte ovde: <a href="' . $BASE . '/proizvod/bergo-xl-ploca/">Bergo XL — podna ploča za terase i balkone</a>.</p>',
	),
	16679 => array(
		'anchor' => 'Tehnički podaci i primena',
		'html'   => '<p>Ploču iz kataloga sa punom specifikacijom pogledajte ovde: <a href="' . $BASE . '/proizvod/bergo-unique/">Bergo Unique</a>.</p>',
	),
	16681 => array(
		'anchor' => 'Tehnički podaci i primena',
		'html'   => '<p>Ploču iz kataloga sa punom specifikacijom pogledajte ovde: <a href="' . $BASE . '/proizvod/bergo-elite-ploca/">Bergo Elite — podna ploča za terase</a>.</p>',
	),
	16662 => array(
		'anchor' => 'Tehničke karakteristike',
		'html'   => '<p>Za prostore oko bazena koriste se dve perforirane ploče iz kataloga — <a href="' . $BASE . '/proizvod/bergo-unique/">Bergo Unique</a> i <a href="' . $BASE . '/proizvod/bergo-xl-ploca/">Bergo XL</a>; obe imaju drenažu ispod ploče i FDA odobren materijal.</p>',
	),
	16665 => array(
		'anchor' => 'Tehnička specifikacija Bergo Easy',
		// Bergo Easy nema svoj proizvod u katalogu (v. izveštaj na kraju).
		// Bergo Solid je poseban, teži model — navodi se kao takav, ne kao ista ploča.
		'html'   => '<p>Za teža opterećenja na istom događaju — prilazne staze, kamione i mašine — koristi se posebna, deblja ploča: <a href="' . $BASE . '/proizvod/bergo-solid-ploca/">Bergo Solid</a> (630 × 575 mm, 50 mm, HDPE).</p>',
	),
	16673 => array(
		'anchor' => 'Četiri modela za dvorište i terasu',
		// Mapiranje Highlands/Nature/Put/Springgrass na konkretne proizvode NIJE moguće
		// potvrditi iz specifikacija — linkuje se kategorija, ne pojedinačan model.
		'html'   => '<p>Sve modele veštačke trave iz ponude pogledajte u kategoriji <a href="' . $BASE . '/kategorija-proizvoda/vestacka-trava/">Veštačka trava</a>.</p>',
	),
);

echo "\n--- 2. cross-linkovi ka /proizvod/ ---\n";
foreach ( $crosslinks as $post_id => $d ) {
	$post = get_post( $post_id );
	if ( ! $post ) { echo "  🔴 {$post_id}: nema posta\n"; continue; }

	if ( strpos( $post->post_content, '/proizvod/' ) !== false
	     || strpos( $post->post_content, '/kategorija-proizvoda/' ) !== false ) {
		echo "  =  {$post_id}: već ima link ka katalogu — preskačem\n";
		continue;
	}

	$log = '';
	$new = al_append_to_section( $post->post_content, $d['anchor'], $p( $d['html'] ), $log );
	if ( $new === false ) { echo "  🔴 {$post_id}: {$log}\n"; continue; }

	echo "  →  {$post_id}: {$log}\n";
	if ( $APPLY ) {
		file_put_contents( "{$BDIR}/{$post_id}-" . date( 'Ymd-His' ) . '.txt', $post->post_content );
		wp_update_post( array( 'ID' => $post_id, 'post_content' => $new ) );
	}
}

/* -------------------------------------------------------- 3. HUB 16590 — 6/6 dece */

echo "\n--- 3. hub 16590 → linkovi ka svih 6 dece ---\n";

$hub = get_post( 16590 );
$c   = $hub->post_content;

// (a) Bergo Easy kao model-kartica, umetnuta ISPRED „Bergo Soft" (Soft se ne dira —
//     njegov sadržaj je #ceka-miroslav, v. PROGRESS Blokeri).
$easy_card = '<h3><a href="' . $BASE . '/spoljnje-podne-obloge/bergo-easy/">Bergo Easy</a></h3>' . "\n"
	. '<p>Tanja klik-ploča (302 × 302 mm, 14 mm) za privremene i povremene postavke — sajmove, promocije i manifestacije. Postavlja se i preko zemlje, trave ili peska, pa se rastavlja i koristi ponovo na drugoj lokaciji.</p>' . "\n\n";

// (b) preostala dva deteta + kategorija kataloga, iza „Bergo Soft" pasusa
$rest = "\n" . '<p>U istu grupu spadaju i <a href="' . $BASE . '/spoljnje-podne-obloge/podovi-za-bazene/">podne obloge oko bazena i za vlažne prostore</a> i <a href="' . $BASE . '/spoljnje-podne-obloge/vestacka-trava-za-terase/">veštačka trava za dvorišta i bašte</a>. Sve ploče iz ove linije nalaze se u kategoriji <a href="' . $BASE . '/kategorija-proizvoda/podloge-za-baste/">Podloge za bašte</a>.</p>' . "\n";

$done = array();

if ( strpos( $c, '/spoljnje-podne-obloge/bergo-easy/' ) === false ) {
	$marker = '<h3>Bergo Soft</h3>';
	if ( strpos( $c, $marker ) !== false ) {
		$c      = str_replace( $marker, $easy_card . $marker, $c );
		$done[] = 'Bergo Easy kartica umetnuta ispred „Bergo Soft"';
	} else {
		echo "  🔴 marker „Bergo Soft\" nije nađen — Easy kartica NIJE dodata\n";
	}
} else { echo "  =  bergo-easy već linkovan\n"; }

if ( strpos( $c, '/spoljnje-podne-obloge/podovi-za-bazene/' ) === false ) {
	// iza pasusa koji opisuje Bergo Soft, pre sledećeg <h2>
	if ( preg_match( '#(<h3>Bergo Soft</h3>\s*<p>.*?</p>)#s', $c, $m ) ) {
		$c      = str_replace( $m[1], $m[1] . $rest, $c );
		$done[] = 'pasus sa bazenima + veštačkom travom + kategorijom dodat';
	} else {
		echo "  🔴 pasus „Bergo Soft\" nije nađen — dopuna NIJE dodata\n";
	}
} else { echo "  =  podovi-za-bazene već linkovan\n"; }

foreach ( $done as $d ) { echo "  →  {$d}\n"; }

if ( $done && $APPLY ) {
	file_put_contents( "{$BDIR}/16590-" . date( 'Ymd-His' ) . '.txt', $hub->post_content );
	wp_update_post( array( 'ID' => 16590, 'post_content' => $c ) );
}

/* --------------------------------------------------------------- 4. KONTROLA */

echo "\n--- 4. kontrola (posle izmena) ---\n";
foreach ( array( 16590, 16659, 16662, 16665, 16673, 16679, 16681 ) as $id ) {
	$post = get_post( $id );
	$t    = get_post_meta( $id, '_thumbnail_id', true );
	$n    = substr_count( $post->post_content, '/proizvod/' ) + substr_count( $post->post_content, '/kategorija-proizvoda/' );
	printf( "  %-6s thumb=%-6s linkova-ka-katalogu=%d\n", $id, $t ? $t : 'NEMA', $n );
}

$hubc  = get_post( 16590 )->post_content;
$deca  = array( 'bergo-unique', 'bergo-xl', 'bergo-elite', 'bergo-easy', 'podovi-za-bazene', 'vestacka-trava-za-terase' );
$fale  = array();
foreach ( $deca as $s ) {
	if ( strpos( $hubc, "/spoljnje-podne-obloge/{$s}/" ) === false ) { $fale[] = $s; }
}
echo '  hub 16590 linkuje ' . ( 6 - count( $fale ) ) . "/6 dece" . ( $fale ? ' — fale: ' . implode( ', ', $fale ) : '' ) . "\n";

echo "\nGotovo." . ( $APPLY ? '' : ' (proba — ništa nije upisano)' ) . "\n";
