<?php
/**
 * W7 F2.5 — Bergo Unique (16679): galerija „u primeni" od postojećih priloga.
 *
 * Poziv:  php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-w7f2-bergo-unique.php [apply]
 *
 * Zašto ne al_import.php: sve fotografije su VEĆ u medijateci (arhiva 2018–2021),
 * pa nema šta da se uvozi — samo se ubacuje sekcija koja ih koristi.
 *
 * Obrazac sekcije = F7.23 kurirane galerije (gol <img> u .al-grid--3, koji
 * al_enhance_content_images() sam umota u lightbox), NE .al-card obrazac sa 16681
 * — kartice bez linka nemaju lightbox, a ovo je referentna galerija.
 */

$APPLY = in_array( 'apply', (array) ( $args ?? array() ), true );
$BASE  = 'http://localhost/antasline';
$BDIR  = WP_CONTENT_DIR . '/../scratchpad/content-backup';
if ( ! is_dir( $BDIR ) ) { mkdir( $BDIR, 0777, true ); }

echo $APPLY ? "=== REŽIM: APPLY ===\n\n" : "=== REŽIM: PROBA ===\n\n";

// Redosled: rezultat → reference → proces/detalj (pravilo iz alati/_README).
$slike = array(
	array( 8477,  'Terasa sa baštenskom garniturom na tamnosivim Bergo Unique pločama' ),
	array( 8450,  'Baštenska terasa sa ratan garniturom na svetlosivoj Bergo Unique oblozi' ),
	array( 8504,  'Ležaljke na terasi obloženoj sivim Bergo Unique pločama' ),
	array( 14602, 'Terasa kafića sa stolicama na plavo-sivim Bergo Unique pločama' ),
	array( 7415,  'Velika javna terasa sa crno-belom šahovskom šemom Bergo Unique ploča' ),
	array( 10029, 'Dvorište sa kamenim roštiljem i Bergo Unique pločama u boji cedra' ),
);

/* ---------------------------------------------------- provera priloga na disku */

echo "--- prilozi ---\n";
$imgs = array();
foreach ( $slike as $s ) {
	list( $att_id, $alt ) = $s;
	$file = get_attached_file( $att_id );
	$url  = wp_get_attachment_url( $att_id );
	if ( ! $file || ! file_exists( $file ) || ! $url ) {
		echo "  🔴 {$att_id}: fajl ne postoji — PREKID\n";
		return;
	}
	$sz = @getimagesize( $file );
	printf( "  ✓ %-6s %-9s %s\n", $att_id, $sz ? "{$sz[0]}×{$sz[1]}" : '?', str_replace( $BASE . '/wp-content/uploads/', '', $url ) );
	$imgs[] = array( 'url' => $url, 'alt' => $alt, 'id' => $att_id );

	if ( $APPLY ) {
		// alt ide i u prilog (medijateka) i u <img> u sadržaju
		update_post_meta( $att_id, '_wp_attachment_image_alt', $alt );
	}
}

/* ------------------------------------------------------------ sekcija galerije */

$html  = '[vc_row full_width="stretch_row" el_class="al-section al-section--paper"][vc_column][vc_column_text]';
$html .= '<span class="al-label">Reference</span><h2>Bergo Unique u primeni</h2>';
$html .= '<p>Iste ploče u stvarnim prostorima — od privatnih dvorišta i terasa do ugostiteljskih bašta i javnih površina. Ploče se sklapaju klik-sistemom, bez lepka, direktno preko postojeće podloge. Kliknite na sliku za uvećan prikaz.</p>';
$html .= '<div class="al-grid al-grid--3" style="margin:24px 0">';
foreach ( $imgs as $i ) {
	$html .= '<img src="' . esc_url( $i['url'] ) . '" alt="' . esc_attr( $i['alt'] ) . '" />';
}
$html .= '</div>[/vc_column_text][/vc_column][/vc_row]';

/* ----------------------------------------------------------------- ubacivanje */

$post  = get_post( 16679 );
$c     = $post->post_content;

if ( strpos( $c, 'Bergo Unique u primeni' ) !== false ) {
	echo "\n  = sekcija „Bergo Unique u primeni\" već postoji — preskačem\n";
	return;
}

$parts = preg_split( '#(?=\[vc_row)#', $c );

// tražimo sekciju „Montaža bez lepljenja" (mist) — galerija ide odmah iza nje
$after = -1;
foreach ( $parts as $i => $p ) {
	if ( $p !== '' && strpos( $p, 'Montaža bez lepljenja' ) !== false ) { $after = $i; break; }
}
if ( $after < 0 ) { echo "\n🔴 sekcija „Montaža bez lepljenja\" nije nađena — PREKID\n"; return; }

// FAQ sekcija je do sada bila `paper`; pošto galerija (paper) staje ispred nje,
// FAQ prelazi u `mist al-diag-top` da se sačuva smena paper/mist.
// Time 16679 dobija identičnu strukturu sekcija kao sestrinska 16681.
$faq = -1;
foreach ( $parts as $i => $p ) {
	if ( $p !== '' && strpos( $p, 'Najčešća pitanja o Bergo Unique' ) !== false ) { $faq = $i; break; }
}
if ( $faq < 0 ) { echo "\n🔴 FAQ sekcija nije nađena — PREKID\n"; return; }

$stara = 'el_class="al-section al-section--paper"';
$nova  = 'el_class="al-section al-section--mist al-diag-top"';
if ( strpos( $parts[ $faq ], $stara ) === false ) {
	echo "\n🔴 FAQ sekcija nema očekivanu klasu `--paper` — PREKID (ne pogađam)\n";
	return;
}
$parts[ $faq ] = preg_replace( '#' . preg_quote( $stara, '#' ) . '#', $nova, $parts[ $faq ], 1 );

array_splice( $parts, $after + 1, 0, array( $html ) );
$new = implode( '', $parts );

echo "\n--- izmene ---\n";
echo "  → galerija (6 fotki, .al-grid--3) umetnuta iza sekcije {$after} („Montaža bez lepljenja\")\n";
echo "  → FAQ sekcija: `--paper` → `--mist al-diag-top` (smena paper/mist, isto kao 16681)\n";

if ( $APPLY ) {
	file_put_contents( "{$BDIR}/16679-" . date( 'Ymd-His' ) . '.txt', $c );
	wp_update_post( array( 'ID' => 16679, 'post_content' => $new ) );
	echo "\n  upisano (bekap u scratchpad/content-backup/)\n";
} else {
	echo "\n  (proba — ništa nije upisano)\n";
}

/* -------------------------------------------------------------------- kontrola */

$c2 = get_post( 16679 )->post_content;
preg_match_all( '#\[vc_row[^\]]*el_class="([^"]*)"#', $c2, $m );
echo "\n--- struktura sekcija 16679 ---\n";
foreach ( $m[1] as $i => $cl ) { echo '  [' . ( $i + 1 ) . "] {$cl}\n"; }
echo '  <img> u sadržaju: ' . substr_count( $c2, '<img' ) . "\n";
