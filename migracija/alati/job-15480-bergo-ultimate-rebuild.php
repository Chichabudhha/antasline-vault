<?php
/**
 * Rebuild 15480 (/sportske-podloge/bergo-ultimate/) sa legacy Porto formata na
 * al-* standard — struktura preslikana sa sestrinske stranice 16659 (Bergo XL).
 *
 * Šta se uklanja:
 *  - `video_bg` hero row: WoodMart ne podržava Porto `video_bg` parametar, pa se
 *    renderovao kao prazan blok od 15em paddinga sa jednom rečenicom (potvrđeno
 *    u browseru). Video se vraća niže, kao pravi `<video preload="none">` sa
 *    poster slikom — 16 MB fajl se ne učitava dok posetilac ne klikne.
 *  - `conditional_render="...administrator..."` na 4 row-a — WPBakery addon koji
 *    to tumači nije aktivan u WoodMart buildu pa je bio bezopasan, ali je ostatak
 *    Porto podešavanja i nema šta da traži u novom sadržaju
 *  - 3× `vc_btn` sa `i_type="porto"` ikonicom (jedan je imao i naslov linka
 *    "Pozovite - ecotile 500-7", zalutao sa druge stranice)
 *  - `vc_media_grid`, `vc_single_image`, dekorativni „b-hide" row sa plavim krugom
 *  - `#colorBlock .productColors-block` legacy markup → `al-swatches` komponenta
 *
 * Sve činjenične tvrdnje (FIBA/ITF, 15 god. garancije, 700 t/m², PP, dimenzije,
 * 16 boja, primena, doplata ispod 300 m²) prenete su iz starog sadržaja.
 *
 * Proba:      wp eval-file job-15480-bergo-ultimate-rebuild.php
 * Izvršenje:  wp eval-file job-15480-bergo-ultimate-rebuild.php apply
 */
$apply = in_array( 'apply', $args, true );
$id    = 15480;
$base  = 'http://localhost/antasline';
$up    = $base . '/wp-content/uploads';
$ikone = $base . '/wp-content/themes/woodmart-child/images/icons';

$hero_bg = $up . '/2026/07/belgrade-challenger-3x3.webp';
$video   = $up . '/2024/02/bergo-basketballcourt-3x3-1.mp4';
$poster  = $up . '/2026/07/nemacki-kosarkaski-savez-dbb.webp';

$galerija = array(
	array( 'kosarkaski-teren-u-dvoristu.webp', 'Plavo-crveni košarkaški teren sa konstrukcijom u dvorištu okruženom zelenilom' ),
	array( 'skolski-tereni-nikola-tesla-banja-luka.webp', 'Iz vazduha snimljeni školski sportski tereni u plavoj i crvenoj boji sa igračima' ),
	array( 'nemacki-kosarkaski-savez-dbb.webp', 'Igrač zakucava tokom utakmice na narandžastom Bergo Ultimate terenu u dvorani' ),
	array( 'belgrade-challenger-3x3.webp', 'Utakmica 3x3 košarke pred publikom na terenu sa Bergo Ultimate podlogom' ),
	array( 'teren-sa-klupskim-znakom.webp', 'Plavi košarkaški teren sa velikim klupskim znakom na sredini igrališta' ),
	array( 'teren-za-kosarku-brezovica.webp', 'Snimak iz vazduha plavo-crvenog košarkaškog terena sa reflektorima' ),
	array( 'krovni-teren-beograd-na-vodi.webp', 'Šareni sportski teren na krovu objekta u Beogradu na vodi' ),
	array( 'multisport-teren-bajina-basta.webp', 'Zeleno-ljubičasti multisport teren sa linijama za više sportova' ),
	array( 'ploce-linije-i-ivicnjaci-detalj.webp', 'Detalj Bergo Ultimate ploča u više boja sa ugrađenom linijom i ivičnom letvicom' ),
);

/* 16 boja — heks vrednosti prenete 1:1 iz starog `productColors-block` markupa */
$boje = array(
	'#FFFFFF' => 'True White',
	'#c3c5c6' => 'Stone Grey',
	'#aeaeb8' => 'Silver Grey',
	'#4b575f' => 'Graphite Grey',
	'#1a171b' => 'Silk Black',
	'#00336e' => 'Dark Blue',
	'#006ab3' => 'Light Blue',
	'#48667e' => 'Steel Blue',
	'#185b1a' => 'Green',
	'#467b22' => 'Spring Grass',
	'#ec7038' => 'Plain Orange',
	'#fedf11' => 'Plain Yellow',
	'#e70721' => 'Plain Red',
	'#bc4f33' => 'Tennisred',
	'#d9dadb' => 'Shadow Grey',
	'#c4b29c' => 'Cedar Wood',
);

$kartice = array(
	array( 'sertifikat.svg', 'FIBA i ITF sertifikat', 'Podloga je izrađena po standardima FIBA i ITF — isti kvalitet za profesionalne centre i dvorišne terene.' ),
	array( 'montaza.svg', 'Laka i brza montaža', 'Klik sistem vezivanja ploča, montažno-demontažna podloga koja pokriva pukotine i mrlje na postojećoj podlozi.' ),
	array( 'garancija.svg', '15 godina garancije', '30 godina iskustva proizvođača, proizvedeno u Švedskoj po EU standardima kvaliteta.' ),
	array( 'fleksibilna.svg', 'Radi na otvorenom', 'Sistem dozvoljava skupljanje i širenje ploča pri temperaturnim razlikama, a dizajn omogućava trenutnu drenažu po vlažnom vremenu.' ),
	array( 'odrzavanje.svg', 'Bez dodatnog održavanja', 'UV stabilna podloga — nije potrebno posebno održavanje tokom godine.' ),
	array( 'izgled.svg', 'Preko 15 boja i logo', 'Teren se prilagođava bojama kluba ili kompanije, uz mogućnost štampe logotipa na podlozi.' ),
);

/* ---------- provera fajlova ---------- */
$provera = array( $hero_bg, $video, $poster, $up . '/2025/12/ultimate-plus-sertifikati-small.jpg.webp' );
foreach ( $galerija as $g ) {
	$provera[] = $up . '/2026/07/' . $g[0];
}
foreach ( $kartice as $k ) {
	$provera[] = $ikone . '/' . $k[0];
}
$nedostaje = array();
foreach ( $provera as $u ) {
	if ( ! file_exists( str_replace( $base . '/', ABSPATH, $u ) ) ) {
		$nedostaje[] = $u;
	}
}
echo '=== PROVERA FAJLOVA === ' . count( $provera ) . ' referenci, nedostaje: ' . count( $nedostaje ) . "\n";
foreach ( $nedostaje as $n ) {
	echo "  🔴 $n\n";
}
if ( $nedostaje ) {
	echo "Prekid.\n";
	return;
}

/* ---------- sadržaj ---------- */
$c  = '[vc_row full_width="stretch_row" el_class="al-section al-section--navy al-hero-photo al-plates al-diag-bottom" css=".vc_custom_heroF4' . $id . '{background-image: url(\'' . $hero_bg . '\') !important;}"][vc_column][vc_column_text]';
$c .= '<div class="al-hero"><span class="al-label">Sportske podloge</span><h1 class="al-display--xl">Bergo Ultimate</h1>';
$c .= '<p class="al-hero__sub">Vrhunske sportske podloge za spoljašnje terene — FIBA i ITF sertifikat, „stop and go" završni sloj i pravilan odskok lopte, za profesionalne centre i dvorišna igrališta.</p>';
$c .= '<div class="al-hero__cta"><a class="al-btn" href="' . $base . '/kontakt/">Zatražite ponudu</a><a class="al-btn al-btn--ghost" href="tel:+381692340072">069 234 00 72</a></div></div>';
$c .= '[/vc_column_text][/vc_column][/vc_row]';

$c .= '[vc_row full_width="stretch_row" el_class="al-section al-section--paper"][vc_column][vc_column_text]';
$c .= '<span class="al-label">Šta je</span><h2 class="al-display--lg">Bergo Ultimate — podloga za profesionalce i dvorišta</h2>';
$c .= '<p><strong>Podloge Bergo Ultimate namenjene su i profesionalnim sportistima i privatnim igralištima.</strong> Njihova specijalna struktura pruža optimalno trenje i odlična svojstva „stop and go", što ih čini savršenim za <strong>basket, fudbal, tenis, badminton, rukomet, odbojku, aerobik</strong> i mnoge druge sportove.</p>';
$c .= '<p>Ove podloge garantuju <strong>pravilan odskok lopte</strong>, sigurnost i prijatan osećaj tokom igre, dok smanjuju klizanje i povećavaju kontrolu pokreta. <strong>FIBA i ITF sertifikati</strong> potvrđuju kvalitet, čineći Bergo Ultimate idealnim izborom za profesionalne sportske centre, škole, ali i za svakodnevnu igru u dvorištu.</p>';
$c .= '<p>U ponudi je <strong>preko 10 boja i tri modela</strong>, što omogućava da se teren prilagodi okruženju ili bojama vašeg tima ili kompanije. Nudimo i <strong>izgradnju sportskih terena po principu „ključ u ruke"</strong>, uključujući kompletnu opremu: koševe, konstrukcije za koš, golove, mreže, mrežice, pa čak i lopte za željeni sport.</p>';
$c .= '<p>Ploče iz kataloga sa punom specifikacijom: <a href="' . $base . '/proizvod/bergo-ultimate-ploca/">Bergo Ultimate</a>, <a href="' . $base . '/proizvod/bergo-ultimate-plus/">Ultimate PLUS</a>, <a href="' . $base . '/proizvod/bergo-ultimate-plus-greenmatter/">Ultimate PLUS by GreenMatter</a> i <a href="' . $base . '/proizvod/bergo-ultimate-flow/">Ultimate FLOW</a>.</p>';
$c .= '[/vc_column_text][/vc_column][/vc_row]';

$c .= '[vc_row full_width="stretch_row" el_class="al-section al-section--mist al-diag-top"][vc_column][vc_column_text]';
$c .= '<span class="al-label">Zašto Bergo Ultimate</span><h2 class="al-display--lg">Šest razloga za Bergo Ultimate</h2>';
$c .= '<div class="al-grid al-grid--3" style="margin-top: 32px">';
foreach ( $kartice as $k ) {
	$c .= '<div class="al-card"><div class="al-card__body"><img class="al-icon" src="' . $ikone . '/' . $k[0] . '" alt="" /><h3>' . $k[1] . '</h3><p>' . $k[2] . '</p></div></div>';
}
$c .= '</div>';
$c .= '<p style="margin-top:24px">Podloga Ultimate ima klik sistem vezivanja ploča jednu za drugu, sa sistemom koji dozvoljava da se ploče lagano skupljaju i šire usled promena temperatura. Zato može biti postavljena na spoljnim površinama gde ima velikih temperaturnih razlika tokom cele godine.</p>';
$c .= '<h3>Primena</h3><ul><li>Košarkaški tereni</li><li>Basket 3x3 tereni</li><li>Tereni za odbojku</li><li>Tereni za rukomet</li><li>Tereni za futsal</li><li>Tereni za sportske manifestacije</li><li>Tereni za sportske centre</li></ul>';
$c .= '[/vc_column_text][/vc_column][/vc_row]';

$c .= '[vc_row full_width="stretch_row" el_class="al-section al-section--paper"][vc_column][vc_column_text]';
$c .= '<span class="al-label">Specifikacija</span><h2 class="al-display--lg">Tehnički podaci Bergo Ultimate</h2>';
$c .= '<table class="al-table"><thead><tr><th>Svojstvo</th><th>Vrednost</th></tr></thead><tbody>';
$c .= '<tr><td>Materijal</td><td>PP — polipropilen, UV stabilan</td></tr>';
$c .= '<tr><td>Dimenzije ploče</td><td>300–380 mm</td></tr>';
$c .= '<tr><td>Debljina</td><td>10–14 mm</td></tr>';
$c .= '<tr><td>Izdržljivost</td><td>700 t/m² (7.000 kg/dm² = 95 kg/cm²)</td></tr>';
$c .= '<tr><td>Sertifikati</td><td>FIBA, ITF, ISO</td></tr>';
$c .= '<tr><td>Garancija</td><td>15 godina</td></tr>';
$c .= '<tr><td>Završni sloj</td><td>„Stop and go" — za sve sportove</td></tr>';
$c .= '</tbody></table>';
$c .= '<div class="al-grid al-grid--2" style="margin-top:32px"><div><img src="' . $up . '/2025/12/ultimate-plus-sertifikati-small.jpg.webp" alt="Sertifikati za sportsku podlogu Bergo Ultimate PLUS" /></div>';
$c .= '<div><p>Puni tehnički list proizvođača sa svim vrednostima:</p><p><a class="al-btn" href="https://smartas.rs/wp-content/uploads/2022/04/ultimate_technical_facts.pdf" target="_blank" rel="noopener">Preuzmite tehničku dokumentaciju (PDF)</a></p></div></div>';
$c .= '[/vc_column_text][/vc_column][/vc_row]';

$c .= '[vc_row full_width="stretch_row" el_class="al-section al-section--mist al-diag-top"][vc_column][vc_column_text]';
$c .= '<span class="al-label">Boje</span><h2 class="al-display--lg">Napravi dizajn — biraj boju</h2>';
$c .= '<p>U saradnji sa najboljim arhitektama i dizajnerima razvijena je široka i atraktivna paleta boja koje se mogu kombinovati u prepoznatljiv dizajn terena.</p>';
$c .= '<div class="al-swatches">';
foreach ( $boje as $hex => $ime ) {
	$c .= '<div class="al-swatch"><span class="al-swatch__chip" style="background-color:' . $hex . '"></span><span class="al-swatch__name">' . $ime . '</span></div>';
}
$c .= '</div>';
$c .= '<p style="margin-top:24px"><em>Kada birate boju koja je izvan standardnih boja ploča, porudžbine ispod 300 m² se dodatno naplaćuju.</em></p>';
$c .= '[/vc_column_text][/vc_column][/vc_row]';

$c .= '[vc_row full_width="stretch_row" el_class="al-section al-section--paper"][vc_column][vc_column_text]';
$c .= '<span class="al-label">Video</span><h2 class="al-display--lg">Bergo Ultimate na 3x3 terenu</h2>';
$c .= '<video controls preload="none" playsinline poster="' . $poster . '" style="width:100%;height:auto;border-radius:8px;margin-top:16px"><source src="' . $video . '" type="video/mp4" />Vaš pregledač ne podržava video.</video>';
$c .= '[/vc_column_text][/vc_column][/vc_row]';

$c .= '[vc_row full_width="stretch_row" el_class="al-section al-section--mist al-diag-top"][vc_column][vc_column_text]';
$c .= '<span class="al-label">Galerija</span><h2 class="al-display--lg">Bergo Ultimate na izvedenim terenima</h2>';
/* Napomena o klikanju je kućna konvencija (20 stranica) — tema sama omotava
   slike iz sadržaja u `a.al-lb` lightbox linkove, pa hint nije prazan obećanje. */
$c .= '<p>Tereni postavljeni Bergo Ultimate pločama — od dvorišnih i školskih terena do FIBA 3x3 turnira i klupskih dvorana. Kliknite na sliku za uvećan prikaz.</p>';
$c .= '<div class="al-grid al-grid--3" style="margin-top: 24px">';
foreach ( $galerija as $g ) {
	$c .= '<img src="' . $up . '/2026/07/' . $g[0] . '" alt="' . $g[1] . '" />';
}
$c .= '</div>[/vc_column_text][/vc_column][/vc_row]';

$c .= '[vc_row full_width="stretch_row" el_class="al-section al-section--navy al-plates al-diag-top--rev"][vc_column][vc_column_text]';
$c .= '<div class="al-hero"><span class="al-label">Kontakt</span><h2 class="al-display--lg">Kreirajte jedinstven teren po vašoj meri</h2>';
$c .= '<p class="al-hero__sub">Pošaljite dimenzije i željene boje — pripremamo personalizovanu skicu terena. Dodajte logo, naziv kluba ili slogan i teren dobija prepoznatljiv identitet.</p>';
$c .= '<div class="al-hero__cta"><a class="al-btn" href="' . $base . '/kontakt/">Pošaljite upit</a><a class="al-btn al-btn--ghost" href="tel:+381692340072">Pozovite: 069 234 00 72</a></div></div>';
$c .= '[/vc_column_text][/vc_column][/vc_row]';

echo "\n=== NOVI SADRŽAJ === " . strlen( $c ) . ' bajtova (staro: ' . strlen( get_post_field( 'post_content', $id ) ) . ")\n";
echo 'sekcija: ' . substr_count( $c, '[vc_row ' ) . ' · H1: ' . substr_count( $c, '<h1' ) . ' · H2: ' . substr_count( $c, '<h2' ) . ' · H3: ' . substr_count( $c, '<h3' ) . "\n";
echo 'kartica: ' . substr_count( $c, 'class="al-card"' ) . ' · boja: ' . substr_count( $c, 'al-swatch"' ) . ' · slika u galeriji: ' . count( $galerija ) . "\n";
/* "porto" kao gola reč hvata i "sportova" — traže se stvarni Porto artefakti */
echo 'porto ostaci: ' . ( substr_count( $c, 'i_type="porto"' ) + substr_count( $c, 'porto_block' ) + substr_count( $c, 'conditional_render' ) + substr_count( $c, 'vc_btn' ) + substr_count( $c, 'video_bg' ) ) . "\n";

if ( ! $apply ) {
	echo "\n(bez 'apply' — ništa nije upisano)\n";
	return;
}

$r = wp_update_post( array( 'ID' => $id, 'post_content' => $c ), true );
if ( is_wp_error( $r ) ) {
	echo '🔴 ' . $r->get_error_message() . "\n";
	return;
}
update_post_meta(
	$id,
	'_wpb_shortcodes_custom_css',
	'.vc_custom_heroF4' . $id . "{background-image: url('$hero_bg') !important;}"
);
delete_post_meta( $id, '_wpb_post_custom_css' );
update_post_meta( $id, '_woodmart_title_off', 'on' );
echo "✅ Upisano u post $id\n";
