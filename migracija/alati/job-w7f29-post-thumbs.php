<?php
/**
 * W7 F2.9 — naslovne slike (_thumbnail_id) za postove koji ih nemaju.
 *
 * Poziv:  php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-w7f29-post-thumbs.php [apply]
 * Bez `apply` = popis kandidata (šta post ima u sadržaju), ne dira bazu.
 *
 * Pravilo iz [[migracija/alati/_README]]: slika se bira po H1/sadržaju stranice,
 * nikad po slugu. Zato ovaj skript u probi ISPISUJE naslov + sve slike iz sadržaja,
 * da se izbor napravi nad onim što post stvarno govori.
 */

$APPLY = in_array( 'apply', (array) ( $args ?? array() ), true );

$posts = array( 2699, 3257, 3318, 3398, 16609, 16610, 16612, 16614, 17027 );

/* Popunjeno posle popisa — post_id => attachment_id (obrazloženje uz svaki) */
$izbor = array(
	2699  => 15307, // plastična podloga za tenis — proizvod koji stvarno prodajemo,
	                // a ne šljaka/beton koje članak samo upoređuje
	3257  => 17268, // Ecotile u proizvodnoj hali — jedina slika u članku, registrovana ovom sesijom
	3318  => 6180,  // antistatik Ecotile pod — direktno tema članka (ESD)
	3398  => 6201,  // Bergo Solid 960×640, jedina prava 3:2 od tri montažne
	16609 => 6454,  // Ecotile u garaži — naš proizvod, ne generička „lux" garaža
	16610 => 17270, // Naxos sportski pod u hali; #7596 je presek-dijagram, loš kao hero
	16612 => 17269, // laboratorija — jedina tematski tačna (ftalati/ispitivanje);
	                // #5008 reciklaza je 225×225, premala za 3:2 karticu
	16614 => 16013, // sportski teren 1996×1496
	17027 => 17147, // teren sa veštačkom travom (Šabac) 1600×900 — naš izveden teren
);

echo $APPLY ? "=== REŽIM: APPLY ===\n\n" : "=== POPIS (bez izmena) ===\n\n";

foreach ( $posts as $id ) {
	$post = get_post( $id );
	if ( ! $post ) { echo "🔴 {$id}: nema posta\n"; continue; }

	echo "── {$id}  {$post->post_title}\n";
	echo "   slug: {$post->post_name}\n";

	if ( isset( $izbor[ $id ] ) ) {
		$att  = $izbor[ $id ];
		$file = get_attached_file( $att );
		if ( ! $file || ! file_exists( $file ) ) {
			echo "   🔴 izabran prilog {$att} nema fajl na disku — preskačem\n\n";
			continue;
		}
		list( $w, $h ) = array_slice( (array) getimagesize( $file ), 0, 2 );
		echo "   → postavljam _thumbnail_id = {$att} ({$w}×{$h})\n";
		if ( $APPLY ) { update_post_meta( $id, '_thumbnail_id', $att ); }
		echo "\n";
		continue;
	}

	/* Popis kandidata iz sadržaja */
	preg_match_all( '#<img[^>]+src="([^"]+)"#i', $post->post_content, $m );
	if ( ! $m[1] ) {
		echo "   ⚠ nema nijedne <img> u sadržaju\n\n";
		continue;
	}
	$seen = array();
	foreach ( $m[1] as $src ) {
		if ( isset( $seen[ $src ] ) ) { continue; }
		$seen[ $src ] = 1;
		$att = attachment_url_to_postid( $src );
		$dim = '';
		if ( $att ) {
			$f = get_attached_file( $att );
			if ( $f && file_exists( $f ) ) {
				$s   = getimagesize( $f );
				$dim = " {$s[0]}×{$s[1]}";
			}
		}
		printf( "   %-7s %s%s\n", $att ? "#{$att}" : '(van DB)', basename( $src ), $dim );
	}
	echo "\n";
}

echo $APPLY ? "Gotovo.\n" : "Popis gotov (ništa nije upisano).\n";
