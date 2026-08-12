<?php
/**
 * Rebuild stranice 5119 (/vestacka-trava-za-fudbal/) sa Porto/legacy formata na
 * al-* standard (foto-hero + al-section sekcije), isti obrazac kao 16673
 * (/vestacka-trava-za-terase/).
 *
 * Šta se uklanja iz starog sadržaja:
 *  - `vc_single_image` 1/3 + `vc_column_text` 2/3 raspored (bez heroja)
 *  - `vc_btn` sa Porto ikonicom (`i_type="porto"`)
 *  - `vc_masonry_grid post_type="vestacka-trava"` — taj CPT ima 0 zapisa,
 *    grid se renderovao kao prazan blok (potvrđeno upitom); zamenjen karticama
 *    stvarnih WooCommerce proizvoda iz kategorije veštačka trava
 *  - dekorativni Porto row (`el_class="b-hide dark footer-top"`, plavi bg krug)
 *  - `porto_block id="4945"` — Porto blok, tema više nije Porto
 *
 * Sve činjenične tvrdnje (FIFA/LND/ITF/IRB, proizvodnja po projektu, boje linija,
 * odvojeno lepljenje linija) prenete su iz starog teksta — ništa nije dodato.
 *
 * Proba:      wp eval-file job-5119-vestacka-trava-fudbal-rebuild.php
 * Izvršenje:  wp eval-file job-5119-vestacka-trava-fudbal-rebuild.php apply
 */
$apply = in_array( 'apply', $args, true );
$id    = 5119;
$base  = 'http://localhost/antasline';
$up    = $base . '/wp-content/uploads';

$slike = array(
	$up . '/2026/07/trening-na-vestackoj-travi.webp',
	$up . '/2026/07/vlakno-vestacke-trave-izbliza.webp',
	$up . '/2026/07/travnata-povrsina-sa-rosom.webp',
	$up . '/2026/07/stadionska-instalacija.webp',
	$up . '/2026/07/trava-za-fudbalske-terene.webp',
	$up . '/2026/07/xj-performance-presek-vlakna.webp',
	$up . '/2026/08/radici-ultramix-evo.webp',
	$up . '/2026/08/radici-multisport-mx.webp',
	$up . '/2026/07/proizvodjac-radici-tournament-20-vestacka-trava-za-tenis-i-padel.webp',
	$up . '/2026/08/radici-vestacka-trava-rugbi.webp',
);
$nedostaje = array();
foreach ( $slike as $u ) {
	$putanja = str_replace( $base . '/', ABSPATH, $u );
	if ( ! file_exists( $putanja ) ) {
		$nedostaje[] = $u;
	}
}
echo '=== PROVERA SLIKA === ' . count( $slike ) . ' referenci, nedostaje: ' . count( $nedostaje ) . "\n";
foreach ( $nedostaje as $n ) {
	echo "  🔴 $n\n";
}
if ( $nedostaje ) {
	echo "Prekid — popravi reference pre primene.\n";
	return;
}

$hero_bg = $up . '/2026/07/trening-na-vestackoj-travi.webp';

$c  = '[vc_row full_width="stretch_row" el_class="al-section al-section--navy al-hero-photo al-plates al-diag-bottom" css=".vc_custom_heroF45119{background-image: url(\'' . $hero_bg . '\') !important;}"][vc_column][vc_column_text]';
$c .= '<div class="al-hero"><span class="al-label">Sport</span><h1 class="al-display--xl">Veštačka trava za fudbal i ostale sportove</h1>';
$c .= '<p class="al-hero__sub">Sportska veštačka trava po projektu — proizvodnja prema tačnim dimenzijama terena, sa linijama koje se ugrađuju odvojeno i ne blede kao farbane.</p>';
$c .= '<div class="al-hero__cta"><a class="al-btn" href="' . $base . '/kontakt/">Zatražite ponudu</a><a class="al-btn al-btn--ghost" href="tel:+381692340072">069 234 00 72</a></div></div>';
$c .= '[/vc_column_text][/vc_column][/vc_row]';

$c .= '[vc_row full_width="stretch_row" el_class="al-section al-section--paper"][vc_column][vc_column_text]';
$c .= '<span class="al-label">Ponuda</span><h2 class="al-display--lg">Veštačke trave za sportske terene</h2>';
$c .= '<p><strong>U ponudi imamo široku paletu visoko kvalitetne veštačke trave — za fudbalska igrališta, tenis i ostale sportove, kao i za dvorišta i terase.</strong> Trava se može isporučiti i u drugim bojama osim zelene.</p>';
$c .= '<p>Veštačka trava za fudbal radi se isključivo po projektu koji odgovara traženim dimenzijama terena. Proizvodnja započinje onog trenutka kada se potvrde dimenzije terena (dužina i širina — vidi <a href="' . $base . '/dimenzije-fudbalskog-terena/">standardne dimenzije fudbalskog terena</a>) i dodatne potrebe kupca.</p>';
$c .= '<p>Rolne veštačke trave i linije lepe se odvojeno. Linije se rade posebno, pa ne postoji mogućnost da izblede tokom vremena kao kad se farbaju. Mogu se proizvesti u beloj, crvenoj i plavoj boji, što omogućava da se na istom terenu dodatno razdvoje sportovi.</p>';
$c .= '<p>Veštačka trava za sport zadovoljava stroge tehničke zahteve koje diktiraju FIFA, LND, ITF i IRB.</p>';
$c .= '[/vc_column_text][/vc_column][/vc_row]';

$c .= '[vc_row full_width="stretch_row" el_class="al-section al-section--mist al-diag-top"][vc_column][vc_column_text]';
$c .= '<span class="al-label">Modeli</span><h2 class="al-display--lg">Sportske trave iz ponude</h2>';
$c .= '<p>Model se bira prema sportu i intenzitetu korišćenja — od terena za mali fudbal i višenamenskih površina do tenisa, padela i rugbija.</p>';
$c .= '<div class="al-grid al-grid--4" style="margin-top: 32px">';
/* Kartica se linkuje kao ceo blok (`<a class="al-card">`), ne link u naslovu —
   isti obrazac kao 16667/16684; link u `al-card__title` bi dobio stil linka
   u sadržaju (boja + podvlačenje) i razbio izgled kartice. */
$c .= '<a href="' . $base . '/proizvod/radici-ultramix-evo-ni/" class="al-card"><span class="al-card__media"><img src="' . $up . '/2026/08/radici-ultramix-evo.webp" alt="Radici ULTRAMIX EVO N.I. veštačka trava za mali fudbal" /></span><span class="al-card__title">Radici ULTRAMIX EVO N.I.</span></a>';
$c .= '<a href="' . $base . '/proizvod/radici-multisport-mx/" class="al-card"><span class="al-card__media"><img src="' . $up . '/2026/08/radici-multisport-mx.webp" alt="Radici Multisport MX višenamenska veštačka trava" /></span><span class="al-card__title">Radici Multisport MX</span></a>';
$c .= '<a href="' . $base . '/proizvod/radici-tournament-20/" class="al-card"><span class="al-card__media"><img src="' . $up . '/2026/07/proizvodjac-radici-tournament-20-vestacka-trava-za-tenis-i-padel.webp" alt="Radici Tournament 20 veštačka trava za tenis i padel" /></span><span class="al-card__title">Radici Tournament 20</span></a>';
$c .= '<a href="' . $base . '/proizvod/radici-vestacka-trava-za-rugbi/" class="al-card"><span class="al-card__media"><img src="' . $up . '/2026/08/radici-vestacka-trava-rugbi.webp" alt="Radici veštačka trava za rugbi" /></span><span class="al-card__title">Radici trava za rugbi</span></a>';
$c .= '</div>';
$c .= '<p>Sve modele pogledajte u kategoriji <a href="' . $base . '/kategorija-proizvoda/vestacka-trava/">Veštačka trava</a>.</p>';
$c .= '[/vc_column_text][/vc_column][/vc_row]';

$c .= '[vc_row full_width="stretch_row" el_class="al-section al-section--paper"][vc_column][vc_column_text]';
$c .= '<span class="al-label">Galerija</span><h2 class="al-display--lg">Veštačka trava na fudbalskim terenima</h2>';
$c .= '<p>Trava za fudbal bira se po visini vlakna i nameni — od trening terena i škola fudbala do stadiona. Kliknite na sliku za uvećan prikaz.</p>';
$c .= '<div class="al-grid al-grid--3" style="margin-top: 24px">';
$c .= '<img src="' . $up . '/2026/07/trening-na-vestackoj-travi.webp" alt="Fudbaleri na treningu na terenu sa veštačkom travom, igrač zaustavlja loptu" />';
$c .= '<img src="' . $up . '/2026/07/vlakno-vestacke-trave-izbliza.webp" alt="Krupni plan vlakana veštačke trave za fudbalske terene" />';
$c .= '<img src="' . $up . '/2026/07/travnata-povrsina-sa-rosom.webp" alt="Površina veštačke trave sa kapima rose u jutarnjem svetlu" />';
$c .= '<img src="' . $up . '/2026/07/stadionska-instalacija.webp" alt="Postavljanje veštačke trave na stadionu sa tribinama u pozadini" />';
$c .= '<img src="' . $up . '/2026/07/trava-za-fudbalske-terene.webp" alt="Uzorak veštačke trave namenjene fudbalskim terenima" />';
$c .= '<img src="' . $up . '/2026/07/xj-performance-presek-vlakna.webp" alt="Presek veštačke trave XJ Performance sa prikazom visine vlakna i podloge" />';
$c .= '</div>[/vc_column_text][/vc_column][/vc_row]';

$c .= '[vc_row full_width="stretch_row" el_class="al-section al-section--navy al-plates al-diag-top--rev"][vc_column][vc_column_text]';
$c .= '<div class="al-hero"><span class="al-label">Kontakt</span><h2 class="al-display--lg">Planirate teren sa veštačkom travom?</h2>';
$c .= '<p class="al-hero__sub">Pošaljite dimenzije terena i sport — vraćamo ponudu sa modelom trave i linijama po projektu.</p>';
$c .= '<div class="al-hero__cta"><a class="al-btn" href="' . $base . '/kontakt/">Pošaljite upit</a><a class="al-btn al-btn--ghost" href="tel:+381692340072">Pozovite: 069 234 00 72</a></div></div>';
$c .= '[/vc_column_text][/vc_column][/vc_row]';

echo "\n=== NOVI SADRŽAJ === " . strlen( $c ) . " bajtova, sekcija: " . substr_count( $c, '[vc_row ' ) . "\n";
echo 'H1: ' . substr_count( $c, '<h1' ) . ' · H2: ' . substr_count( $c, '<h2' ) . ' · al-card: ' . substr_count( $c, 'al-card"' ) . "\n";

if ( ! $apply ) {
	echo "\n(bez 'apply' — ništa nije upisano)\n";
	return;
}

$r = wp_update_post( array( 'ID' => $id, 'post_content' => $c ), true );
if ( is_wp_error( $r ) ) {
	echo '🔴 ' . $r->get_error_message() . "\n";
	return;
}
/* WPBakery ne kompajlira `css=""` atribut na front-endu sam — pravilo mora
   postojati u `_wpb_shortcodes_custom_css` meta (isti obrazac kao 16673/17026),
   inače hero ostane ravno plav bez fotke. */
update_post_meta(
	$id,
	'_wpb_shortcodes_custom_css',
	".vc_custom_heroF45119{background-image: url('$hero_bg') !important;}"
);
delete_post_meta( $id, '_wpb_post_custom_css' );
update_post_meta( $id, '_woodmart_title_off', 'on' );
echo "✅ Upisano u post $id\n";
