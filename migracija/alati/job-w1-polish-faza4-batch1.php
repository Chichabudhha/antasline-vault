<?php
/**
 * W1 Polish Faza 4, batch 1 (2026-08-07): pravi GEO-intro "Kratak odgovor"
 * pasus (.al-geo-intro) na vrhu 5 posta — svaki pasus izveden isključivo iz
 * postojećeg teksta tog posta, ništa izmišljeno (ni cena, ni spec koji nije
 * već u sadržaju). Isti obrazac kao originalni Faza 3 batch 1 (2298/2542/...):
 * div se ubacuje kao PRVA linija post_content-a, ispred svega ostalog
 * (uključujući Gutenberg block-komentare gde postoje).
 *
 * 3388 ima FAQPage <script type="application/ld+json"> u post_content —
 * upis ide isključivo preko $wpdb->update() (F7.24 gotcha: wp_update_post()
 * zove wp_unslash() nad celim post_content-om i kvari escapovane navodnike
 * u JSON-LD). Ostala 4 posta nemaju <script> — i njih upisujemo preko
 * $wpdb->update() radi konzistentnosti (nema razloga za dva puta).
 *
 * wp eval-file job-w1-polish-faza4-batch1.php          # proba
 * wp eval-file job-w1-polish-faza4-batch1.php apply    # upis
 */

global $wpdb;

$apply = ( ( $args[0] ?? '' ) === 'apply' );

$intros = array(
	3318 => '<div class="al-geo-intro"><strong>Kratak odgovor:</strong> ESD pod je neophodan svuda gde statičko pražnjenje ugrožava elektroniku ili predstavlja rizik od požara/eksplozije — elektronske komponente mogu biti oštećene već pri 25–50 V (kod osetljivijih i pri 10 V), dok čovek pri kretanju po neprovodnom podu može proizvesti i preko 10.000 V. Ecotile ESD PVC ploče sadrže milione provodnih vlakana od nerđajućeg čelika koja uzemljuju naboj, sa otporom u opsegu 3,4×10⁴–5×10⁶ Ω/m² (u skladu sa BS EN 61340-5-1 i IEC 61340 standardima).</div>',
	5276 => '<div class="al-geo-intro"><strong>Kratak odgovor:</strong> za ravan krov ili terasu najbolja opcija su PVC podloge švedskog proizvođača Bergo flooring (modeli Bergo XL, Bergo Unique, Bergo Elite) — postavljaju se klik sistemom bez lepljenja ili fiksiranja za podlogu, izuzetno su lagane (2,6 kg/m²) pa minimalno opterećuju krovnu konstrukciju, antiklizne su, otporne na UV zrake/toplotu/hladnoću i apsorbuju buku pri hodanju.</div>',
	5181 => '<div class="al-geo-intro"><strong>Kratak odgovor:</strong> za montažne objekte i kontejnere preporučuju se tri varijante poda: Ecotile vinil ploče 500×500 mm (debljina 5, 7 ili 10 mm zavisno od opterećenja kretanja, uklj. viljuškare/vozila do 3,5 t), LVT Expona Clic podloga (30+ dezena drveta ili betona, više slojeva sa zvučnom i zaštitnom funkcijom) ili Bergo PVC podloge. Sve tri se montiraju preko postojećeg poda bez lepljenja i mogu se kasnije skinuti i premestiti u drugi objekat.</div>',
	2622 => '<div class="al-geo-intro"><strong>Kratak odgovor:</strong> pri izboru industrijskog poda odlučuju tri pitanja — (1) funkcija prostora (otpornost na viljuškare, ulja/hemikalije, klizanje, ili ESD/antistatik svojstva za elektroniku i štamparije), (2) brzina postavljanja (Ecotile sistem spojivih PVC ploča montira se direktno na beton bez lepka i hidroizolacije, bez čekanja da beton sazri i do godinu dana), i (3) ukupna vrednost tokom veka trajanja (plastične ploče se lako čiste, oštećena ploča se pojedinačno menja bez zaustavljanja proizvodnje, a Ecotile pri zameni otkupljuje stari pod).</div>',
	3388 => '<div class="al-geo-intro"><strong>Kratak odgovor:</strong> pod za štampariju mora biti otporan na boje, rastvarače i druge hemikalije prisutne u proizvodnji, otporan na mrlje i klizanje, a tamo gde su u pogonu osetljive mašine i računari — i antistatik (ESD) izvedbe koja sprovodi naelektrisanje do uzemljenja. Ecotile PVC ploče postavljaju se bez lepka i hidroizolacije (moguća ugradnja u sekcijama bez zaustavljanja proizvodnje), a oštećena ploča se pojedinačno zamenjuje bez dodatnog zastoja.</div>',
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
