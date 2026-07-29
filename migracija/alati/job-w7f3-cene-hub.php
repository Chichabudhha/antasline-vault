<?php
/**
 * W7 F3 — nova „Cene" hub stranica.
 * Proba: eval-file job-w7f3-cene-hub.php    Izvršenje: ... job-w7f3-cene-hub.php apply
 */
$apply = in_array( 'apply', $args, true );

$home = home_url();

$kartice = array(
	array(
		'id'    => 16874,
		'h3'    => 'Industrijski podovi — cena',
		'p'     => 'Cena po m² za Ecotile PVC ploče u magacinima, halama i radionicama — po debljini ploče i opterećenju.',
	),
	array(
		'id'    => 16873,
		'h3'    => 'Gumeni podovi za terase — cena',
		'p'     => 'Cena podloga za terase, balkone i dvorišta — po vrsti ploče i kvadraturi.',
	),
	array(
		'id'    => 16876,
		'h3'    => 'Podloge za parkiralište — cena',
		'p'     => 'Geoplast rešetke za parking, prilaze i staze — cena po m² od 2.800 din.',
	),
	array(
		'id'    => 16875,
		'h3'    => 'Podovi za garaže — cena',
		'p'     => 'Cena podnih ploča za garaže i auto-servise — otporne na ulja, gume i točkove dizalice.',
	),
);

$cards = '';
foreach ( $kartice as $k ) {
	$p = get_post( $k['id'] );
	if ( ! $p || 'publish' !== $p->post_status ) {
		echo "🔴 STOP: ciljna stranica {$k['id']} ne postoji ili nije objavljena.\n";
		return;
	}
	$url    = get_permalink( $k['id'] );
	$cards .= '<a href="' . esc_url( $url ) . '" class="al-card"><span class="al-card__body"><h3>'
		. $k['h3'] . '</h3><p>' . $k['p'] . '</p></span></a>';
	echo "  kartica → {$k['id']} $url\n";
}

$content = '[vc_row full_width="stretch_row" el_class="al-section al-section--navy al-plates al-diag-bottom"][vc_column][vc_column_text]
<div class="al-hero"><span class="al-label">Cene</span><h1 class="al-display--xl">Cene podova — pregled po nameni</h1><p class="al-hero__sub">Cena poda zavisi od namene, opterećenja i kvadrature. Ispod su okvirne cene po m² za četiri najtraženije grupe — za tačnu ponudu pošaljite dimenzije prostora.</p><div class="al-hero__cta"><a class="al-btn" href="' . esc_url( home_url( '/kontakt/' ) ) . '">Zatražite ponudu</a><a class="al-btn al-btn--ghost" href="tel:+381692340072">069 234 00 72</a></div></div>
[/vc_column_text][/vc_column][/vc_row]
[vc_row full_width="stretch_row" el_class="al-section al-section--paper"][vc_column][vc_column_text]
<span class="al-label">Pregled</span>
<h2 class="al-display--lg">Izaberite namenu</h2>
<p>Svaka stranica nosi cenu po m², šta je uključeno u tu cenu i šta je utiče naviše ili naniže. Cene su okvirne i važe za standardne uslove montaže.</p>
<div class="al-grid al-grid--2" style="margin-top: 32px">' . $cards . '</div>
[/vc_column_text][/vc_column][/vc_row]
[vc_row full_width="stretch_row" el_class="al-section al-section--mist"][vc_column][vc_column_text]
<span class="al-label">Šta utiče na cenu</span>
<h2 class="al-display--lg">Zašto se cena razlikuje</h2>
<p>Četiri stvari pomeraju cenu poda, bez obzira na to koju grupu gledate:</p>
<ul>
<li><strong>Opterećenje.</strong> Viljuškar, teretno vozilo ili samo pešački saobraćaj određuju debljinu ploče — a debljina je najveća stavka u ceni.</li>
<li><strong>Kvadratura.</strong> Veće površine imaju nižu cenu po m², jer se fiksni troškovi montaže raspoređuju na više metara.</li>
<li><strong>Stanje podloge.</strong> Ecotile i Bergo ploče se postavljaju i preko oštećenog betona ili starog epoksida, bez skidanja — što je često najveća ušteda u odnosu na livene podove.</li>
<li><strong>Dodaci.</strong> Rampe, trake za obeležavanje, ESD uzemljenje i ivične ploče se računaju posebno.</li>
</ul>
<p>Za poređenje sa livenim podovima pogledajte i <a href="' . esc_url( home_url( '/epoksidni-podovi-ili-ecotile-podovi/' ) ) . '">epoksidni podovi ili Ecotile ploče</a>, a za tehničke detalje <a href="' . esc_url( home_url( '/industrijski-podovi/' ) ) . '">industrijske podove</a>.</p>
[/vc_column_text][/vc_column][/vc_row]
[vc_row full_width="stretch_row" el_class="al-section al-section--navy"][vc_column][vc_column_text]
<div class="al-cta"><h2 class="al-display--lg">Recite nam kvadraturu i namenu</h2><p>Dobijate cenu po m² i ukupan iznos u roku od jednog radnog dana.</p><div class="al-hero__cta"><a class="al-btn" href="' . esc_url( home_url( '/kontakt/' ) ) . '">Pošaljite upit</a><a class="al-btn al-btn--ghost" href="tel:+381692340072">069 234 00 72</a></div></div>
[/vc_column_text][/vc_column][/vc_row]';

$postoji = get_page_by_path( 'cene' );
if ( $postoji ) {
	echo "🔴 STOP: slug 'cene' je već zauzet (ID {$postoji->ID}).\n";
	return;
}

echo "\n--- PROBA ---\nslug: cene\nnaslov: Cene podova\ndužina sadržaja: " . strlen( $content ) . " B\n";

if ( ! $apply ) {
	echo "\n(bez 'apply' — ništa nije upisano)\n";
	return;
}

$id = wp_insert_post(
	array(
		'post_title'   => 'Cene podova',
		'post_name'    => 'cene',
		'post_content' => $content,
		'post_status'  => 'publish',
		'post_type'    => 'page',
		'post_author'  => 1,
	),
	true
);

if ( is_wp_error( $id ) ) {
	echo '🔴 GREŠKA: ' . $id->get_error_message() . "\n";
	return;
}

update_post_meta( $id, '_woodmart_title_off', 'on' );      // F7.14 — sadržaj ima svoj H1
update_post_meta( $id, '_woodmart_main_layout', 'full-width' );
update_post_meta( $id, '_yoast_wpseo_title', 'Cene podova — industrijski, terase, parking i garaže' );
update_post_meta( $id, '_yoast_wpseo_metadesc', 'Okvirne cene podova po m²: industrijski Ecotile, gumene podloge za terase, Geoplast resetke za parking i podovi za garaze. Ponuda za 1 radni dan.' );

echo "✅ Napravljeno: ID $id → " . get_permalink( $id ) . "\n";
