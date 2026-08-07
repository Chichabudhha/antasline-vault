<?php
/**
 * W1 Polish Faza 4, batch 2 (2026-08-07): pravi GEO-intro "Kratak odgovor"
 * pasus (.al-geo-intro) na vrhu 6 posta (prioriteti 6-11 iz reda čekanja) —
 * svaki pasus izveden isključivo iz postojećeg teksta tog posta.
 *
 * 16616 (teren-za-pickleball) ima @graph FAQPage+Product <script
 * type="application/ld+json"> u post_content — upis ide isključivo preko
 * $wpdb->update() (F7.24 gotcha). Ostalih 5 nema <script>, ali se i njih
 * upisuje preko $wpdb->update() radi konzistentnosti sa batch 1.
 *
 * wp eval-file job-w1-polish-faza4-batch2.php          # proba
 * wp eval-file job-w1-polish-faza4-batch2.php apply    # upis
 */

global $wpdb;

$apply = ( ( $args[0] ?? '' ) === 'apply' );

$intros = array(
	16615 => '<div class="al-geo-intro"><strong>Kratak odgovor:</strong> za detailing radionice i servise preporučuju se dve vrste PVC podnih ploča koje se montiraju klik sistemom bez lepljenja, direktno preko betona, pločica, asfalta ili ispucalih/zamašćenih površina: Bergo Ultimate (Bergo flooring, ploče 38×38 cm, debljina 11 mm, za unutrašnje i spoljašnje prostore) i Ecotile 500/7 (500×500×7 mm, čisti ili reciklirani vinil, za unutrašnju upotrebu). Obe imaju širok izbor boja i mogu se dodatno brendirati.</div>',
	16613 => '<div class="al-geo-intro"><strong>Kratak odgovor:</strong> preko starog parketa ili pločica najbrže se postavljaju tri vrste podova bez lepka i bez oštećenja postojeće podloge: Objectflor Clic LVT pod (30+ dezena drveta/betona, gumeni sloj protiv buke), Ecotile vinil ploče (500×500 mm, debljine 5, 7 ili 10 mm) i R-tek PVC ploče (za maloprodajne i javne objekte, brza montaža bez zatvaranja prostora). Sve tri se vezuju klik sistemom direktno na ravnu i tvrdu površinu.</div>',
	16612 => '<div class="al-geo-intro"><strong>Kratak odgovor:</strong> ftalati su omekšivači koji se dodaju PVC proizvodima da bi ih učinili fleksibilnijim, a postepeno se ispuštaju iz materijala i mogu štetno delovati na endokrini i disajni sistem — u EU i Srbiji su zato zabranjene određene grupe ftalata (DEHP, DBP, BBP, DIPN, DIDP, DnOP) u dečijim igračkama i proizvodima za negu dece. AntasLine PVC podovi su bez ftalata.</div>',
	16616 => '<div class="al-geo-intro"><strong>Kratak odgovor:</strong> teren za pickleball ima dimenzije 13,4 × 6,1 m sa mrežom visine 86 cm; postavlja se na beton, asfalt ili drugu ravnu površinu, uz sportsku podlogu za bolji odskok i zaštitu zglobova. Za tu namenu preporučuje se Bergo Ultimate FLOV™ (Bergo Flooring, Švedska) — klik sistem bez lepka, EN 14877 sertifikat, otpornost na sve vremenske uslove i 15 godina garancije.</div>',
	3398  => '<div class="al-geo-intro"><strong>Kratak odgovor:</strong> za kretanje teških vozila (kamiona, viljuškara) po nepristupačnim ili neuređenim površinama (blato, trava, pesak) koristi se montažno-demontažni pod Bergo Solid od tvrde plastike (HDPE), razvijen prvobitno za potrebe UN u vanrednim situacijama. Postavlja se preko peska, zemlje, betona, pločica, asfalta ili trave bez lepljenja — jedan čovek može montirati 100 m² za sat vremena.</div>',
	2641  => '<div class="al-geo-intro"><strong>Kratak odgovor:</strong> PVC podne ploče i gumeni podovi izgledaju slično, ali su suprotni po svojstvima — PVC je krut i čvrst (injektirana/ekstrudirana plastika), guma je fleksibilna i mekana (gumeni granulat spojen poliuretanskim lepkom). PVC ploče traju preko 20 godina, ne zahtevaju hidroizolaciju, otporne su na vodu, ulja i hemikalije, lako se čiste (nepropusna površina) i mogu se reciklirati i posle 10 godina upotrebe — što ih čini boljim izborom za industrijsku primenu od gume.</div>',
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
