<?php
/**
 * Alt tekst za slike proizvoda koje se STVARNO renderuju, a nemaju alt
 * (glavna slika + galerija). Nastavak reda čekanja iz 2026-07-30 Lighthouse
 * a11y plana ("alt tekst — poseban budući zadatak"), izvršen 2026-08-12.
 *
 * OBIM — namerno ograničen na 2 kanala:
 *   1. `_thumbnail_id` proizvoda (6 komada)
 *   2. `_product_image_gallery` proizvoda (63 komada)
 * NIJE u obimu: <img> u `post_content`. Audit je pokazao da su tamo praktično
 * sve preostale prazne alt vrednosti DEKORATIVNE SVG ikonice (montaza.svg,
 * odrzavanje.svg, izdrzljivost.svg…) uz isti tekst koji već stoji pored njih —
 * `alt=""` je za njih ISPRAVNO po WCAG (v. [[reference/naucene-lekcije]]
 * 2026-08-05). Popunjavanje bi bilo regresija pristupačnosti, ne popravka.
 *
 * IZVOR TEKSTA — ništa izmišljeno:
 *   - override mapa ispod: slike koje su VIZUELNO pregledane (Read alat nad
 *     fajlom) pre pisanja opisa; opis tvrdi samo ono što se na slici vidi
 *   - dezen-imena (Eden Ash, Rice Wine Oak 9028…) dolaze iz imena fajla, koje
 *     je proizvođačka oznaka dezena — ne opisuje se izgled koji nije proveren
 *   - sve ostalo: alt = naslov proizvoda (mehanički bezbedan minimum, isti
 *     princip kao raniji `job-alt-tekst-proizvodi.php`)
 *   - dodatne fotografije istog proizvoda (`…-2.webp`): "<naslov> — fotografija N"
 *
 * DELJENI PRILOZI: jedan prilog = jedan alt, bez obzira na to u koliko galerija
 * stoji. Zato deljeni prilozi MORAJU imati neutralan (ne-proizvodni) opis u
 * override mapi — skripta puca ako neki deljeni prilog nije pokriven.
 *
 * Ne dira postojeći alt (samo prazan/NULL).
 *
 * php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-alt-tekst-galerije.php        # proba
 * php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-alt-tekst-galerije.php apply  # upis
 */

$apply = ( ( $args[0] ?? '' ) === 'apply' );

/** Vizuelno pregledane slike + proizvođačke oznake dezena. */
$override = array(
	// --- deljeni prilozi (obavezno neutralno, stoje u više galerija) ---
	12503 => 'Zelena i crvena ergonomska podloga sa žutim ivicama u magacinskom prolazu',
	16861 => 'Crno-žuta protivklizna zaštita ivice na industrijskoj platformi',

	// --- vizuelno pregledane scene ---
	15790 => 'Konj na perforiranoj gumenoj podlozi u ispustu ispred štale',
	6075  => 'Magacinski prolaz sa Ecotile podnim pločama u više boja i obeleženim pešačkim zonama',
	4900  => 'Proizvodna hala sa sivim Ecotile podom, viljuškarom i crveno obeleženom zonom',
	4995  => 'Radionica za montažu elektronike sa antistatik (ESD) podom i radnim stolovima',
	10961 => 'Vinarija sa EXPONA vinil podom u dezenu drveta i zidom punim flaša',
	10931 => 'Kancelarija sa EXPONA Commercial vinil podom u dezenu tamnog drveta',
	8001  => 'Kantina sa EXPONA Flow vinil podom u sivom betonskom dezenu',
	10910 => 'EXPONA Flow vinil pod u tamnom dezenu sa tri žute dekorativne vaze',

	// --- proizvođačke oznake dezena (iz imena fajla) ---
	7980  => 'EXPONA Clic 19dB Wood — dezen Eden Ash',
	5563  => 'EXPONA Clic 19dB Wood — dezen Rice Wine Oak 9028',
	5565  => 'EXPONA Clic 19dB Wood — dezen Treehouse Oak 9036',
	9656  => 'EXPONA Commercial LVT vinil pločice — dezen 12523',
);

/** Prikupi kandidate: att_id => ['alt'=>…, 'proizvodi'=>[…], 'fajl'=>…, 'kanal'=>…] */
$kand = array();

$prods = get_posts( array(
	'post_type'   => 'product',
	'post_status' => 'publish',
	'numberposts' => -1,
	'fields'      => 'ids',
) );

$dodaj = function ( $att_id, $pid, $kanal ) use ( &$kand ) {
	if ( ! $att_id ) {
		return;
	}
	$post_alt = get_post_meta( $att_id, '_wp_attachment_image_alt', true );
	if ( trim( (string) $post_alt ) !== '' ) {
		return; // ne diramo postojeći
	}
	if ( ! isset( $kand[ $att_id ] ) ) {
		$kand[ $att_id ] = array(
			'proizvodi' => array(),
			'fajl'      => basename( (string) get_post_meta( $att_id, '_wp_attached_file', true ) ),
			'kanal'     => $kanal,
		);
	}
	$kand[ $att_id ]['proizvodi'][] = $pid;
};

foreach ( $prods as $pid ) {
	$dodaj( get_post_thumbnail_id( $pid ), $pid, 'thumb' );
	$gal = (string) get_post_meta( $pid, '_product_image_gallery', true );
	foreach ( array_filter( array_map( 'intval', explode( ',', $gal ) ) ) as $gid ) {
		$dodaj( $gid, $pid, 'galerija' );
	}
}

/** Izračunaj alt po prilogu. */
$greske = array();
foreach ( $kand as $att_id => &$k ) {
	$k['proizvodi'] = array_values( array_unique( $k['proizvodi'] ) );
	$deljen         = count( $k['proizvodi'] ) > 1;

	if ( isset( $override[ $att_id ] ) ) {
		$k['alt']   = $override[ $att_id ];
		$k['izvor'] = 'override';
		continue;
	}

	if ( $deljen ) {
		$greske[] = "Prilog {$att_id} ({$k['fajl']}) stoji u " . count( $k['proizvodi'] )
			. ' galerije (' . implode( ', ', $k['proizvodi'] ) . '), a nema neutralan opis u override mapi.';
		continue;
	}

	$naslov = get_the_title( $k['proizvodi'][0] );
	$naslov = html_entity_decode( $naslov, ENT_QUOTES, 'UTF-8' );
	// Naslov ume da nosi marketinški rep posle "|" ("… | Trajna i uklonjiva") —
	// to je copy za SERP, ne opis slike.
	$naslov = trim( explode( '|', $naslov )[0] );

	// "…-2.webp" = dodatna fotografija istog proizvoda (broj do 12, da se ne
	// pomeša sa oznakom dezena tipa "Rice-Wine-Oak-9028")
	if ( preg_match( '/-(\d{1,2})\.[a-z]+$/i', $k['fajl'], $m ) && (int) $m[1] >= 2 && (int) $m[1] <= 12 ) {
		$k['alt']   = $naslov . ' — fotografija ' . (int) $m[1];
		$k['izvor'] = 'fotografija N';
	} else {
		$k['alt']   = $naslov;
		$k['izvor'] = 'naslov';
	}
}
unset( $k );

if ( $greske ) {
	echo "🔴 PREKID — deljeni prilog bez neutralnog opisa:\n";
	foreach ( $greske as $g ) {
		echo "  - {$g}\n";
	}
	echo "\nDopuni override mapu pa ponovi. Ništa nije upisano.\n";
	return;
}

/** Izlaz. */
$po_izvoru = array();
$n         = 0;
foreach ( $kand as $att_id => $k ) {
	$po_izvoru[ $k['izvor'] ] = ( $po_izvoru[ $k['izvor'] ] ?? 0 ) + 1;
	$n++;
	printf(
		"%-8s %-6d %-46.46s -> %s\n",
		$k['kanal'],
		$att_id,
		$k['fajl'],
		$k['alt']
	);
	if ( $apply ) {
		update_post_meta( $att_id, '_wp_attachment_image_alt', $k['alt'] );
	}
}

echo "\nPriloga: {$n} | " . ( $apply ? 'UPISANO' : 'za upis' ) . "\n";
foreach ( $po_izvoru as $izvor => $c ) {
	echo "  {$izvor}: {$c}\n";
}
if ( ! $apply ) {
	echo "\nProba — ništa nije upisano. Dodaj 'apply' za upis.\n";
}
