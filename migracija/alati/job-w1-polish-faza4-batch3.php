<?php
/**
 * W1 Polish Faza 4, batch 3 (2026-08-07): pravi GEO-intro "Kratak odgovor"
 * pasus (.al-geo-intro) na vrhu preostalih 10 posta (prioriteti 12-21,
 * GSC 0-klik grupa) — svaki pasus izveden isključivo iz postojećeg teksta
 * tog posta. Zatvara ceo red čekanja Faze 4 (22/22 posta).
 *
 * Nijedan od ovih 10 nema <script> u post_content-u, ali se upis i dalje
 * radi preko $wpdb->update() radi konzistentnosti sa batch 1/2.
 *
 * wp eval-file job-w1-polish-faza4-batch3.php          # proba
 * wp eval-file job-w1-polish-faza4-batch3.php apply    # upis
 */

global $wpdb;

$apply = ( ( $args[0] ?? '' ) === 'apply' );

$intros = array(
	5411  => '<div class="al-geo-intro"><strong>Kratak odgovor:</strong> modularni industrijski pod (Ecotile) su PVC ploče visoke izdržljivosti koje se spajaju klik sistemom i postavljaju direktno preko oštećenog, prašnjavog ili neravnog betona — bez razbijanja, lepka ili višednevnih radova. Montiraju se do 100–150 m² dnevno, podnose opterećenje viljuškara i teških mašina, imaju protivkliznu R10 površinu i traju 10+ godina; za razliku od epoksida ne pucaju kad beton „radi", a oštećena ploča se menja pojedinačno u minutima.</div>',
	16614 => '<div class="al-geo-intro"><strong>Kratak odgovor:</strong> kvalitetna, atraktivna sportska igrališta sa Bergo sportskim podlogama podstiču decu i mlade da budu fizički aktivniji, provode više vremena na otvorenom i manje pred ekranima — deo su Bergo WISH kampanje za zdravije okruženje za odrastanje dece u školama i gradovima.</div>',
	16608 => '<div class="al-geo-intro"><strong>Kratak odgovor:</strong> oštećen industrijski pod (pucanje betona, prašina, neravnine) najčešće nastaje zbog lošeg kvaliteta betona, velikih opterećenja (viljuškari, palete), vlage i temperaturnih promena — posebno su podložni pucanju epoksidni premazi. Ecotile PVC ploče se montiraju direktno preko oštećene, vlažne površine bez hidroizolacije i lepka, prostor je odmah upotrebljiv posle ugradnje, a otporne su na hemikalije, velika opterećenja i udarce.</div>',
	5163  => '<div class="al-geo-intro"><strong>Kratak odgovor:</strong> IT kompanija Quectel Wireless Solutions je za svoj istraživački centar u Beogradu ugradila Ecotile ESD pod (500×500 mm, debljina 7 mm) radi zaštite osetljive elektronske opreme od elektrostatičkog pražnjenja — ploče se ne lepe niti fiksiraju za podlogu, podnose kretanje viljuškara i kolica do 3,5 t, i mogu se po potrebi skinuti i premestiti.</div>',
	16610 => '<div class="al-geo-intro"><strong>Kratak odgovor:</strong> zamena starog sportskog parketa u školskim salama najčešće nije ni potrebna — Naxos polipropilenska sportska podloga (gumeni sloj 4 mm + modularne kocke) postavlja se direktno preko postojećeg parketa ili druge tvrde podloge bez hidroizolacije, brzinom do 500 m² dnevno, uz 10 godina garancije i vek trajanja preko 20 godina. Proizvedena je u Portugaliji po EU standardima.</div>',
	3257  => '<div class="al-geo-intro"><strong>Kratak odgovor:</strong> Ecotile industrijski pod se može postaviti direktno preko vlažnih, oštećenih i neravnih površina uz vrlo malo pripreme — podloga ne mora biti idealno ravna, podnosi saobraćaj viljuškara i teških vozila, dostupan je u protivkliznim i ESD verzijama, otporan je na ulje i hemikalije i lako se čisti. Montaža je moguća dok proizvodnja traje, a oštećena ploča se zamenjuje za nekoliko minuta.</div>',
	4813  => '<div class="al-geo-intro"><strong>Kratak odgovor:</strong> Bergo Ultimate™ je sportska podloga za sve vremenske prilike, testirana i sertifikovana po evropskom standardu EN 14877 (uklj. test klizanja na mokrom), otporna na sunce/kišu/hladnoću/led, montira se brzo bez lepka i hemikalija, ima integrisane aut-linije i 15 godina garancije. Bergo Ultimate PLUS™ ima istu gornju površinu ali ojačanu donju konstrukciju za veća opterećenja.</div>',
	6824  => '<div class="al-geo-intro"><strong>Kratak odgovor:</strong> R-Tile Design modularni podovi su bolji izbor od klasičnih keramičkih pločica za radnje i supermarkete — montiraju se bez lepljenja i sušenja (bez zatvaranja objekta), podnose kolica/paletare/viljuškare bez pucanja, protivklizni su, oštećena ploča se menja pojedinačno za nekoliko minuta, a dugoročni troškovi održavanja su niži.</div>',
	6874  => '<div class="al-geo-intro"><strong>Kratak odgovor:</strong> britanska kompanija Secure Innovation je u svom proizvodnom pogonu postavila 660 m² Ecotile ESD ploča (7 mm, tamno sivo) uz 87 m² kontrastnih ploča za obeležavanje prolaza, 13 ploča za uzemljenje i 2.000 m provodljive trake — čime je dobijena kontinuirana ESD-bezbedna mreža koja štiti osetljivu elektroniku od statičkog pražnjenja, uz dodatnih 104 m² ploča protiv zamora u ključnim radnim zonama.</div>',
	17021 => '<div class="al-geo-intro"><strong>Kratak odgovor:</strong> istraživačko-razvojna kompanija HTEC iz Niša ugradila je Ecotile ESD pod (500×500 mm, debljina 7 mm, PVC sa provodnim vlaknima od nerđajućeg čelika) za zaštitu osetljive elektronske opreme od elektrostatičkog pražnjenja — ploče se montiraju sistemom uklapanja bez lepljenja, brzo i bez prekida rada.</div>',
);

foreach ( $intros as $id => $intro_html ) {
	$post = get_post( $id );
	if ( ! $post ) {
		echo "SKIP {$id}: not found\n";
		continue;
	}

	$content = $post->post_content;

	if ( strpos( $content, 'al-geo-intro' ) !== false ) {
		echo "SKIP {$id} ({$post->post_title}): already has al-geo-intro\n";
		continue;
	}

	$new_content = $intro_html . "\n\n" . ltrim( $content );

	echo "OK   {$id} ({$post->post_title})\n";

	if ( $apply ) {
		$wpdb->update(
			$wpdb->posts,
			array( 'post_content' => $new_content ),
			array( 'ID' => $id )
		);
		clean_post_cache( $id );
	}
}

echo $apply ? "\nUpisano.\n" : "\nProba završena, ništa upisano (pokreni sa 'apply').\n";
