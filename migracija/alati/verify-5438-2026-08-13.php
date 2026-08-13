<?php
/**
 * verify-5438-2026-08-13.php — F7.14 verifikacioni set za `/sportske-podloge/` (5438).
 *
 * SAMO ČITA. Nema nijednog upisa u bazu — bezbedno pokretati koliko god puta.
 *
 * Postoji jer stavka E dira stranicu koja nosi **178 GSC klikova / 90d** (138 iz
 * basket klastera), a izmena umeće dve sekcije, menja FAQ par i gradi FAQPage
 * JSON-LD. Standardni `al_verify.php` hvata samo HTTP/h1/PHP greške — ovde treba
 * i: broj JSON-LD blokova sa `json_decode` proverom, FAQPage sa tačno 4 Question,
 * odsustvo golog JSON-a u tekstu (gotcha CB2/F7.15 — kses pojede `<script>` pa
 * JSON ispadne kao vidljiv tekst), preživeli video facade i `[al_skica]`, te
 * planer link koji stvarno vraća 200.
 *
 * Poziv:
 *   php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file verify-5438-2026-08-13.php
 */

$ID = 5438;

function al_get( $url, $head_only = false ) {
	$ch = curl_init( $url );
	curl_setopt_array( $ch, array(
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_TIMEOUT        => 60,
		CURLOPT_NOBODY         => $head_only,
		CURLOPT_SSL_VERIFYPEER => false,
	) );
	$body = curl_exec( $ch );
	$code = (int) curl_getinfo( $ch, CURLINFO_RESPONSE_CODE );
	curl_close( $ch );
	return array( 'code' => $code, 'body' => (string) $body );
}

global $wpdb;
$greske = 0;
$url    = get_permalink( $ID );

echo "=== verify-5438 ===\n";
printf( "URL: %s\n", $url );

/* --------------------------------------------------------------- sirov sadržaj */

$c = $wpdb->get_var( $wpdb->prepare( "SELECT post_content FROM {$wpdb->posts} WHERE ID=%d", $ID ) );
printf( "post_content: %d B · %d [vc_row] · %d <h2> · %d <h3> · %d <script>\n",
	strlen( $c ), substr_count( $c, '[vc_row ' ), substr_count( $c, '<h2' ),
	substr_count( $c, '<h3' ), substr_count( $c, '<script' ) );

$hero_css = get_post_meta( $ID, '_wpb_shortcodes_custom_css', true );
$hero_ok  = ( strpos( (string) $hero_css, 'vc_custom_heroF45438' ) !== false );
printf( "%s hero CSS pravilo (.vc_custom_heroF45438) u _wpb_shortcodes_custom_css\n", $hero_ok ? '✅' : '🔴' );
if ( ! $hero_ok ) { $greske++; }

/* ------------------------------------------------------------------- render */

$r = al_get( $url );
printf( "%s HTTP %d\n", 200 === $r['code'] ? '✅' : '🔴', $r['code'] );
if ( 200 !== $r['code'] ) { $greske++; echo "\n🔴 GREŠAKA: {$greske}\n"; return; }
$b = $r['body'];

$h1 = preg_match_all( '#<h1\b#i', $b );
printf( "%s %d×<h1>\n", 1 === $h1 ? '✅' : '🔴', $h1 );
if ( 1 !== $h1 ) { $greske++; }

printf( "   %d×<h2> · %d×<h3> u renderu\n",
	preg_match_all( '#<h2\b#i', $b ), preg_match_all( '#<h3\b#i', $b ) );

if ( preg_match( '#(Fatal error|Parse error|Warning:|Notice:|Deprecated:)#', $b, $m ) ) {
	printf( "🔴 PHP greška u renderu: %s\n", $m[1] );
	$greske++;
} else {
	echo "✅ nema PHP grešaka u renderu\n";
}

/* ------------------------------------------------------------------ JSON-LD */

preg_match_all( '#<script[^>]*type="application/ld\+json"[^>]*>(.*?)</script>#is', $b, $ld, PREG_SET_ORDER );
printf( "JSON-LD blokova: %d\n", count( $ld ) );

$faq_q = 0;
foreach ( $ld as $i => $blok ) {
	$data = json_decode( trim( $blok[1] ), true );
	if ( null === $data ) {
		printf( "  🔴 blok #%d — json_decode PUKAO (%s)\n", $i + 1, json_last_error_msg() );
		$greske++;
		continue;
	}
	$tipovi = array();
	$hodaj  = function ( $n ) use ( &$hodaj, &$tipovi, &$faq_q ) {
		if ( ! is_array( $n ) ) { return; }
		if ( isset( $n['@type'] ) ) {
			foreach ( (array) $n['@type'] as $t ) {
				$tipovi[] = $t;
				if ( 'Question' === $t ) { $faq_q++; }
			}
		}
		foreach ( $n as $v ) { $hodaj( $v ); }
	};
	$hodaj( $data );
	printf( "  ✅ blok #%d — json_decode OK · @type: %s\n", $i + 1, implode( ', ', array_unique( $tipovi ) ) );
}

$faqpage = substr_count( $b, '"FAQPage"' );
printf( "%s FAQPage blokova: %d · Question stavki: %d\n",
	( 1 === $faqpage && 4 === $faq_q ) ? '✅' : '⚠️ ', $faqpage, $faq_q );

/* ------------------------------------- gol JSON u tekstu (kses pojeo <script>) */

$bez_script = preg_replace( '#<script\b.*?</script>#is', '', $b );
$gol = ( strpos( $bez_script, '@context' ) !== false ) || ( strpos( $bez_script, '&quot;@context' ) !== false );
printf( "%s gol JSON u vidljivom tekstu: %s\n", $gol ? '🔴' : '✅', $gol ? 'DA' : 'ne' );
if ( $gol ) { $greske++; }

/* ------------------------------------------------- neizvršeni shortcode-ovi */

$neizvrseni = array();
foreach ( array( '[al_skica', '[vc_row', '[vc_column_text' ) as $sc ) {
	if ( strpos( $b, $sc ) !== false ) { $neizvrseni[] = $sc; }
}
printf( "%s neizvršeni shortcode-ovi u renderu: %s\n",
	$neizvrseni ? '🔴' : '✅', $neizvrseni ? implode( ' ', $neizvrseni ) : 'nema' );
if ( $neizvrseni ) { $greske++; }

/* ------------------------------------------------- ključni elementi sekcije [3] */

$provere = array(
	'video facade (data-yt-id=VdZWT2O5_-M)' => strpos( $b, 'VdZWT2O5_-M' ) !== false,
	// [al_skica] emituje <div class="al-skica-wrap"> + INLINE <svg> — ime skice se u
	// izlazu ne pojavljuje, pa se traži omotač i stvarni SVG unutar njega.
	'al_skica renderovana (al-skica-wrap + inline <svg>)' =>
		preg_match( '#<div class="al-skica-wrap"[^>]*>\s*<svg#i', $b ) === 1,
	'11 kartica disciplina (al-card__title)' => substr_count( $b, 'al-card__title' ) >= 11,
	'hero H1 (Sportski podovi za spoljasnju...)' => strpos( $b, 'al-display--xl' ) !== false,
);
foreach ( $provere as $ime => $ok ) {
	printf( "%s %s\n", $ok ? '✅' : '🔴', $ime );
	if ( ! $ok ) { $greske++; }
}

/* ------------------------------------------------------------- planer link */

$planer = substr_count( $b, '/planer-terena/' );
printf( "%s pomena /planer-terena/ u renderu: %d\n", $planer > 0 ? '✅' : '⚠️ ', $planer );
if ( $planer > 0 ) {
	$p = al_get( 'http://localhost/antasline/planer-terena/', true );
	printf( "%s /planer-terena/ HTTP %d\n", 200 === $p['code'] ? '✅' : '🔴', $p['code'] );
	if ( 200 !== $p['code'] ) { $greske++; }
}

/* ----------------------------------------------------------------- slike */

$img = array();
if ( preg_match_all( '#<img[^>]+src="([^"]+)"#i', $b, $m1 ) ) {
	foreach ( $m1[1] as $s ) { if ( 0 === strpos( $s, 'http' ) ) { $img[ $s ] = 1; } }
}
if ( preg_match_all( '#srcset="([^"]+)"#i', $b, $m2 ) ) {
	foreach ( $m2[1] as $set ) {
		foreach ( explode( ',', $set ) as $part ) {
			$u = trim( strtok( trim( $part ), ' ' ) );
			if ( $u && 0 === strpos( $u, 'http' ) ) { $img[ $u ] = 1; }
		}
	}
}
$lose = array();
foreach ( array_keys( $img ) as $u ) {
	if ( strpos( $u, 'localhost' ) === false ) { continue; } // eksterni (ytimg) se ne broje
	$ir = al_get( $u, true );
	if ( 200 !== $ir['code'] ) { $lose[] = "{$ir['code']}  {$u}"; }
}
printf( "%s lokalnih slika: %d · ≠200: %d\n", $lose ? '🔴' : '✅', count( $img ), count( $lose ) );
foreach ( $lose as $l ) { echo "   {$l}\n"; }
if ( $lose ) { $greske++; }

echo "\n" . ( $greske ? "🔴 GREŠAKA: {$greske}\n" : "✅ bez grešaka\n" );
