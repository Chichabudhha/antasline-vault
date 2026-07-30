<?php
/**
 * W1 Polish Faza 3, batch 1 (2026-07-30): retrofit al-geo-intro/al-cta-box
 * klase (definisane u antas-design.css istog dana) na 5 postova, ukloniti
 * ad-hoc inline stilove koje su te klase trebale da zamene.
 *
 * wp eval-file job-w1-polish-faza3-batch1.php          # proba (diff brojevi)
 * wp eval-file job-w1-polish-faza3-batch1.php apply    # upis
 */

$apply = ( ( $args[0] ?? '' ) === 'apply' );

/**
 * @param int $id
 * @param array<int, array{0:string,1:string,2:int}> $replacements [old, new, expected_count]
 */
function al_faza3_apply( $id, array $replacements, $apply ) {
	$post = get_post( $id );
	if ( ! $post ) {
		echo "SKIP {$id}: not found\n";
		return;
	}
	$content = $post->post_content;
	$ok      = true;

	foreach ( $replacements as $i => $r ) {
		list( $old, $new, $expected ) = $r;
		$count = substr_count( $content, $old );
		if ( $count !== $expected ) {
			echo "  ⚠️ {$id} repl#{$i}: expected {$expected}, found {$count}\n";
			$ok = false;
			continue;
		}
		$content = str_replace( $old, $new, $content );
	}

	echo ( $ok ? "OK   " : "FAIL " ) . "{$id} ({$post->post_title})\n";

	if ( $apply && $ok ) {
		kses_remove_filters();
		wp_update_post( array( 'ID' => $id, 'post_content' => $content ) );
		clean_post_cache( $id );
	}
}

// ---------- 2298: Kako napraviti teren za basket ----------
al_faza3_apply( 2298, array(
	array(
		'<p><strong>Kratak odgovor:</strong> teren za basket u dvorištu najjednostavnije se pravi Bergo modularnim sportskim podlogama — potrebna je ravna i tvrda podloga (beton, asfalt ili dobro utaban tucanik) sa blagim padom, a ploče se spajaju klik sistemom bez lepka, uz gumeni čekić kao jedini alat. Standardna dimenzija terena za basket (3x3) je 15 x 14 m, a u praksi se najčešće radi 15 x 10 m. Kompletno uputstvo za postavljanje sledi u nastavku.</p>',
		'<div class="al-geo-intro"><strong>Kratak odgovor:</strong> teren za basket u dvorištu najjednostavnije se pravi Bergo modularnim sportskim podlogama — potrebna je ravna i tvrda podloga (beton, asfalt ili dobro utaban tucanik) sa blagim padom, a ploče se spajaju klik sistemom bez lepka, uz gumeni čekić kao jedini alat. Standardna dimenzija terena za basket (3x3) je 15 x 14 m, a u praksi se najčešće radi 15 x 10 m. Kompletno uputstvo za postavljanje sledi u nastavku.</div>',
		1,
	),
	array(
		'<div style="background: #EEF3F8;border-left: 4px solid #F04D22;padding: 16px 20px;margin: 24px 0"><strong>Potrebna vam je pomoć ili ponuda za podlogu za basket?</strong> Pošaljite dimenzije terena i željene boje na <a href="mailto:office@antasline.com">office@antasline.com</a> ili pozovite <a href="tel:+381692340072">069 234 00 72</a> — napravićemo vam skicu i poslati ponudu.</div>',
		'<div class="al-cta-box"><p><strong>Potrebna vam je pomoć ili ponuda za podlogu za basket?</strong> Pošaljite dimenzije terena i željene boje na <a href="mailto:office@antasline.com">office@antasline.com</a> ili pozovite <a href="tel:+381692340072">069 234 00 72</a> — napravićemo vam skicu i poslati ponudu.</p></div>',
		1,
	),
	array(
		'<div style="background: #EEF3F8;border-left: 4px solid #F04D22;padding: 16px 20px;margin: 24px 0">Za sva ostala pitanja ili sugestije pozovite <a href="tel:+381692340072">069 234 00 72</a> ili nam pišite na <a href="mailto:office@antasline.com">office@antasline.com</a>.</div>',
		'<div class="al-cta-box"><p>Za sva ostala pitanja ili sugestije pozovite <a href="tel:+381692340072">069 234 00 72</a> ili nam pišite na <a href="mailto:office@antasline.com">office@antasline.com</a>.</p></div>',
		1,
	),
	array( 'class="al-grid al-grid--3" style="margin:24px 0"', 'class="al-grid al-grid--3"', 2 ),
), $apply );

// ---------- 2542: Epoksidni podovi ili Ecotile (conquest) ----------
al_faza3_apply( 2542, array(
	array(
		'<p><strong>Kratak odgovor:</strong> najbolja alternativa epoksidnom podu za proizvodnu halu su Ecotile modularne PVC ploče (interlocking sistem) — postavljaju se preko postojeće, čak i oštećene podloge bez zaustavljanja proizvodnje, popravljaju se zamenom pojedinačne ploče za 5 minuta i sele se sa firmom, dok epoksidni pod traži pripremljen beton, pražnjenje prostora i danima sušenja. Detaljno poređenje po ceni, montaži i trajnosti sledi u nastavku.</p>',
		'<div class="al-geo-intro"><strong>Kratak odgovor:</strong> najbolja alternativa epoksidnom podu za proizvodnu halu su Ecotile modularne PVC ploče (interlocking sistem) — postavljaju se preko postojeće, čak i oštećene podloge bez zaustavljanja proizvodnje, popravljaju se zamenom pojedinačne ploče za 5 minuta i sele se sa firmom, dok epoksidni pod traži pripremljen beton, pražnjenje prostora i danima sušenja. Detaljno poređenje po ceni, montaži i trajnosti sledi u nastavku.</div>',
		1,
	),
	array(
		'<div style="background: #EEF3F8;border-left: 4px solid #F04D22;padding: 16px 20px;margin: 24px 0"><strong>Birate između epoksida i PVC ploča?</strong> Pozovite <a href="tel:+381692340072">069 234 00 72</a> ili pišite na <a href="mailto:office@antasline.com">office@antasline.com</a> — pošaljite kvadraturu i namenu prostora i dobićete preporuku i okvirnu ponudu.</div>',
		'<div class="al-cta-box"><p><strong>Birate između epoksida i PVC ploča?</strong> Pozovite <a href="tel:+381692340072">069 234 00 72</a> ili pišite na <a href="mailto:office@antasline.com">office@antasline.com</a> — pošaljite kvadraturu i namenu prostora i dobićete preporuku i okvirnu ponudu.</p></div>',
		1,
	),
), $apply );

// ---------- 2699: Podloga za teniske terene ----------
al_faza3_apply( 2699, array(
	array(
		'<p><strong>Kratak odgovor:</strong> za teniske terene se najčešće koristi klasična šljaka (tradicionalni izbor na profesionalnim i klupskim terenima), veštačka trava, tvrda US Open podloga ili polipropilenske (PP) modularne podloge — poslednje su sve popularniji izbor u svetu zbog brže i jednostavnije ugradnje bez lepka.</p>',
		'<div class="al-geo-intro"><strong>Kratak odgovor:</strong> za teniske terene se najčešće koristi klasična šljaka (tradicionalni izbor na profesionalnim i klupskim terenima), veštačka trava, tvrda US Open podloga ili polipropilenske (PP) modularne podloge — poslednje su sve popularniji izbor u svetu zbog brže i jednostavnije ugradnje bez lepka.</div>',
		1,
	),
	// zn_contact_submit/btn-fullcolor su mrtve Zion Builder klase (theme promenjen na WoodMart) —
	// renderovale su se kao neobojen pravougaonik bez ijednog stila. Isto dugme 4x kroz post.
	array( '<a class="zn_contact_submit btn btn-fullcolor btn--rounded  " href="tel:+381692340072">Pozovi</a>', '<a class="al-btn" href="tel:+381692340072">Pozovi</a>', 4 ),
	array( '<a class="zn_contact_submit btn btn-fullcolor btn--rounded  " href="mailto:office@antasline.com">Pošalji upit</a>', '<a class="al-btn al-btn--ghost" href="mailto:office@antasline.com">Pošalji upit</a>', 4 ),
	array( '<a class="zn_contact_submit btn btn-fullcolor btn--rounded  " href="http://localhost/antasline/sportske-podloge/">Saznaj više</a>', '<a class="al-btn al-btn--ghost" href="http://localhost/antasline/sportske-podloge/">Saznaj više</a>', 4 ),
), $apply );

// ---------- 5170: Teren za basket (3x3) TC Galerija ----------
al_faza3_apply( 5170, array(
	array( '<p style="text-align: left">Sredinom 2022. godine u saradnji sa Dunk Shop-om smo postavili teren za basket na krovu TC Galerija.</p>', '<p>Sredinom 2022. godine u saradnji sa Dunk Shop-om smo postavili teren za basket na krovu TC Galerija.</p>', 1 ),
	array( '<p style="text-align: left">Teren je napravljen za potrebe promocije Dunk Shop-a ali i za promociju basketa. Kupci kao i posetioci TC Galerija mogu da se oprobaju i da igraju basket.</p>', '<p>Teren je napravljen za potrebe promocije Dunk Shop-a ali i za promociju basketa. Kupci kao i posetioci TC Galerija mogu da se oprobaju i da igraju basket.</p>', 1 ),
	array( '<p style="text-align: left">Postavljena je podloga za basket poslednje generacije Švedskog proizvodjača Bergo flooring. Na istoj podlozi će se u septembru 2022 godine u Austriji odigrati finale 3x3 evropskog prvenstva. Podloga je rađena po FIBA standardima sa gumenim slojem izmedju betonskih ploča i podloge za basket. Svečanom otvaranju radnje kao i terena za basket prisustvovali su poznati iz sveta košarke i 3x3.</p>', '<p>Postavljena je podloga za basket poslednje generacije Švedskog proizvodjača Bergo flooring. Na istoj podlozi će se u septembru 2022 godine u Austriji odigrati finale 3x3 evropskog prvenstva. Podloga je rađena po FIBA standardima sa gumenim slojem izmedju betonskih ploča i podloge za basket. Svečanom otvaranju radnje kao i terena za basket prisustvovali su poznati iz sveta košarke i 3x3.</p>', 1 ),
	array(
		'Saznaj više o podlogama za basket : <span style="color: #ff0000"><em> <a style="color: #ff0000" href="http://localhost/antasline/sportske-podloge/">http://localhost/antasline/sportske-podloge/</a></em></span>',
		'<p>Saznaj više o <a href="http://localhost/antasline/sportske-podloge/">podlogama za basket</a>.</p>',
		1,
	),
), $apply );

// ---------- 6588: Šta postaviti preko starog parketa ili pločica ----------
al_faza3_apply( 6588, array(
	array( 'class="al-grid al-grid--3" style="margin:24px 0"', 'class="al-grid al-grid--3"', 1 ),
), $apply );

echo $apply ? "\nUpisano.\n" : "\nProba završena, ništa upisano (pokreni sa 'apply').\n";
