<?php
/**
 * W7 F2.3 — nova pod-stranica `/lvt-podovi-za-komercijalne-i-javne-prostore/expona-simplay/`.
 *
 * Obrazac je 16684 (Expona Click): hero → rešenje → [galerija] → [tehničke] →
 * EXPONA program → dokumentacija → FAQ → CTA, uz strogu smenu paper/mist i
 * `al-diag-top` samo na mist sekcijama (F7.20 — dva reza ne smeju biti uzastopna;
 * par „FAQ ✂ → navy CTA ✂--rev" je zatečeni sitewide obrazac svih sestrinskih
 * stranica, pa se doslovno preslikava umesto da se izmišlja nov).
 *
 * Sve činjenice su doslovno iz proizvoda 16916 (post_content + post_excerpt) —
 * nijedna specifikacija nije izmišljena niti parafrazirana.
 *
 * 🔴 `kses_remove_filters()` je OBAVEZAN: WP-CLI radi bez ulogovanog korisnika, pa
 *    `current_user_can('unfiltered_html')` vraća false i kses pojede `<script>`
 *    omotač JSON-LD-a. To je tačno F7.15 bug sa `/teren-za-pickleball/` (5,3 KB
 *    golog JSON-a vidljivo na stranici, nijedna schema emitovana).
 *
 * Poziv: php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-w7f2-simplay-stranica.php [apply]
 */

$APPLY = in_array( 'apply', $args, true );
$BASE  = 'http://localhost/antasline';
$TEL   = 'tel:+381692340072';
$TELTX = '069 234 00 72';   // izmereno: 171 href `+38169…`, tema 15/15 — „072 234 00 72" je greška
$PARENT = 16667;
$SLUG   = 'expona-simplay';

kses_remove_filters();

$ICON = $BASE . '/wp-content/themes/woodmart-child/images/icons/';

// --- galerija: rezultat → referenca → proces/detalj (pravilo iz alati/_README) ---
$GAL = array(
	array( 7964, 'Kafe restoran sa EXPONA Simplay podom u svetlom dezenu' ),
	array( 7880, 'Recepcija ordinacije sa EXPONA Simplay podom u dezenu drveta' ),
	array( 7923, 'Prodajni prostor sa EXPONA Simplay podom u dezenu drveta' ),
	array( 5669, 'Daske EXPONA Simplay sa vidljivom IXPE akustičnom podlogom' ),
	array( 5670, 'Pločice EXPONA Simplay u dezenu betona sa akustičnom podlogom' ),
	array( 7942, 'Detalj ivice EXPONA Simplay daske sa slojevima' ),
);

$FAQ = array(
	array( 'Da li je za montažu EXPONA Simplay 19dB potreban lepak?',
	       'Ne — loose-lay sistem se fiksira tackifier lepljivom podlogom, bez punopovršinskog lepka.' ),
	array( 'Koliko smanjuje udarni zvuk?',
	       '19 dB (EN ISO 10140-3), zahvaljujući integrisanoj IXPE akustičnoj podlozi debljine 1 mm.' ),
	array( 'Koji dezeni su dostupni?',
	       '12 dezena u tri linije: Classic Wood, Nordic Wood i Concrete (industrijski beton izgled).' ),
	array( 'Da li se pod može koristiti odmah posle ugradnje?',
	       'Da — kod loose-lay sistema nema vremena vezivanja lepka, pa je pod pregaziv odmah, što skraćuje prekid rada u prodavnici ili kancelariji.' ),
);

// --------------------------------------------------------------------- sekcije
function al_sec( $class, $inner ) {
	return '[vc_row full_width="stretch_row" el_class="' . $class . '"][vc_column][vc_column_text]'
		. $inner . '[/vc_column_text][/vc_column][/vc_row]';
}

$s = array();

// 1 — hero (navy ✂bottom)
$s[] = al_sec( 'al-section al-section--navy al-plates al-diag-bottom',
	'<div class="al-hero"><span class="al-label">EXPONA Simplay 19dB</span>'
	. '<h1 class="al-display--xl">EXPONA Simplay 19dB — loose-lay LVT podovi bez lepka</h1>'
	. '<p class="al-hero__sub">Samopolažuća LVT pločica sa integrisanom IXPE akustičnom podlogom — smanjuje udarni zvuk za 19 dB, a pod je upotrebljiv odmah posle ugradnje.</p>'
	. '<div class="al-hero__cta"><a class="al-btn" href="' . $BASE . '/kontakt/">Zatražite ponudu</a>'
	. '<a class="al-btn al-btn--ghost" href="' . $TEL . '">' . $TELTX . '</a></div></div>' );

// 2 — rešenje (paper); prvi pasus = direktan odgovor (GEO pravilo)
$s[] = al_sec( 'al-section al-section--paper',
	'<span class="al-label">Rešenje</span><h2 class="al-display--lg">Šta je EXPONA Simplay 19dB</h2>'
	. '<p>EXPONA Simplay 19dB je samopolažuća (loose-lay) LVT pločica ili daska sa integrisanom IXPE akustičnom podlogom. Postavlja se bez punopovršinskog lepka — fiksira se tackifier lepljivom podlogom — pa je pod pregaziv odmah posle ugradnje, bez čekanja da lepak veže. Zbog toga se najčešće bira tamo gde prekid rada košta: u prodavnicama, kancelarijama, ugostiteljstvu i zdravstvenim ustanovama.</p>'
	. '<div class="al-grid al-grid--4" style="margin-top: 32px">'
	. '<div class="al-card"><div class="al-card__body"><img class="al-icon" src="' . $ICON . 'montaza.svg" alt="" /><h3>Bez lepka</h3><p>Loose-lay fiksacija tackifier podlogom, manje pripreme podloge.</p></div></div>'
	. '<div class="al-card"><div class="al-card__body"><img class="al-icon" src="' . $ICON . 'odrzavanje.svg" alt="" /><h3>−19 dB</h3><p>Integrisana IXPE podloga od 1 mm smanjuje udarni zvuk.</p></div></div>'
	. '<div class="al-card"><div class="al-card__body"><img class="al-icon" src="' . $ICON . 'izdrzljivost.svg" alt="" /><h3>Odmah pregaziv</h3><p>Nema vremena vezivanja lepka — kraći prekid rada.</p></div></div>'
	. '<div class="al-card"><div class="al-card__body"><img class="al-icon" src="' . $ICON . 'izgled.svg" alt="" /><h3>12 dezena</h3><p>Classic Wood, Nordic Wood i Concrete linije.</p></div></div>'
	. '</div>'
	. '<p style="margin-top:24px">Pojedinačni elementi se zamenjuju bez remonta celog poda. Ako vam treba klik sistem umesto loose-lay, pogledajte <a href="' . $BASE . '/lvt-podovi-za-komercijalne-i-javne-prostore/expona-click/">Expona Click</a>; za lepljenu varijantu sa 80 dezena pogledajte <a href="' . $BASE . '/lvt-podovi-za-komercijalne-i-javne-prostore/vinil-podovi/">Expona Commercial</a>.</p>' );

// 3 — galerija (mist ✂)
$g = '<span class="al-label">Galerija</span><h2 class="al-display--lg">EXPONA Simplay u prostoru</h2>'
	. '<div class="al-grid al-grid--3" style="margin:24px 0">';
foreach ( $GAL as $it ) {
	// gol <img> — al_enhance_content_images() sam dodaje lightbox + srcset
	$src = wp_get_attachment_image_url( $it[0], 'full' );
	if ( ! $src ) { WP_CLI::warning( 'nema prilog #' . $it[0] ); continue; }
	$g .= '<img src="' . esc_url( $src ) . '" alt="' . esc_attr( $it[1] ) . '" />';
}
$g .= '</div>';
$s[] = al_sec( 'al-section al-section--mist al-diag-top', $g );

// 4 — ključne karakteristike (paper). Namerno kraća tabela nego na proizvodu 16916:
//     stranica nosi primenu i izbor, proizvod nosi pun tehnički list (anti-kanibalizacija).
$s[] = al_sec( 'al-section al-section--paper',
	'<span class="al-label">Specifikacija</span><h2 class="al-display--lg">Ključne karakteristike</h2>'
	. '<div style="overflow-x:auto"><table class="al-table"><tbody>'
	. '<tr><th>Tip poda</th><td>Loose-lay heterogeni PVC sa IXPE akustičnom podlogom</td></tr>'
	. '<tr><th>Ukupna debljina</th><td>5,0 mm (IXPE podloga 1,0 mm)</td></tr>'
	. '<tr><th>Sloj za habanje</th><td>0,55 mm</td></tr>'
	. '<tr><th>Format</th><td>Pločica 600×600 mm ili daska 177,8×1219,2 mm</td></tr>'
	. '<tr><th>Klasa upotrebe</th><td>EN ISO 10874: 23 / 33 / 42</td></tr>'
	. '<tr><th>Protivkliznost</th><td>R10 (DIN 51130) · DS (EN 13893)</td></tr>'
	. '<tr><th>Reakcija na vatru</th><td>Cfl-s1 (EN 13501-1)</td></tr>'
	. '<tr><th>Podno grejanje</th><td>Pogodno, do 27 °C (EN 1264-2)</td></tr>'
	. '</tbody></table></div>'
	. '<p style="margin-top:16px">Pun tehnički list sa svim vrednostima je na stranici proizvoda <a href="' . $BASE . '/proizvod/expona-simplay-19db-loose-lay-lvt/">EXPONA Simplay 19dB</a>.</p>' );

// 5 — EXPONA program (mist ✂) — 3 kartice, bez sopstvene; isti blok kao na sestrinskim stranicama
$PROG = array(
	array( 'EXPONA Clic 19dB', '/proizvod/expona-clic-19db-wood-klik-daska/', 'kancelarija-sa-expona-clic-podom',
	       'Open space kancelarija sa svetlim hrastovim EXPONA Clic 19dB podom',
	       'rigidna klik daska sa 5G-i Välinge sistemom bez lepka, IXPE akustična podloga, 100% vodootporna' ),
	array( 'EXPONA Commercial', '/proizvod/expona-commercial-lvt-vinil-plocice/', null,
	       'Kancelarija sa tamnim EXPONA Commercial LVT podom u dezenu drveta',
	       'heterogena PVC dizajn pločica za teški komercijalni saobraćaj, 80 dezena, sloj za habanje 0,55 mm' ),
	array( 'EXPONA Flow', '/proizvod/expona-flow-lvt-vinil-podovi-u-rolnama/', null,
	       'Kantina sa svetlim EXPONA Flow vinil podom položenim iz rolne',
	       'heterogena PVC obloga u rolnama, 50 dezena, sloj za habanje 0,7 mm, za jako opterećenje' ),
);
$ATT = array( 'EXPONA Commercial' => 10931, 'EXPONA Flow' => 8001 );

$p = '<span class="al-label">Kolekcije</span><h2 class="al-display--lg">EXPONA program</h2>'
	. '<div class="al-grid al-grid--3" style="margin-top: 32px">';
$prose = array();
foreach ( $PROG as $c ) {
	$aid = $c[2] ? attachment_url_to_postid( wp_upload_dir()['baseurl'] . '/2026/07/' . $c[2] . '.webp' ) : ( $ATT[ $c[0] ] ?? 0 );
	if ( ! $aid && $c[2] ) {
		// fotka je uvezena u F2.1 — nađi je po naslovu, ne po pretpostavljenoj putanji
		$q = get_posts( array( 'post_type' => 'attachment', 'post_status' => 'inherit', 'numberposts' => 1, 'title' => 'Kancelarija sa EXPONA Clic podom', 'fields' => 'ids' ) );
		$aid = $q ? (int) $q[0] : 0;
	}
	$src = $aid ? wp_get_attachment_image_url( $aid, 'full' ) : '';
	if ( ! $src ) { WP_CLI::warning( 'nema slika za ' . $c[0] ); }
	$p .= '<a href="' . $BASE . $c[1] . '" class="al-card">'
		. '<span class="al-card__media"><img src="' . esc_url( $src ) . '" alt="' . esc_attr( $c[3] ) . '" /></span>'
		. '<span class="al-card__title">' . esc_html( $c[0] ) . '</span></a>';
	$prose[] = '<strong>' . esc_html( $c[0] ) . '</strong> — ' . $c[4];
}
$p .= '</div><p style="margin-top:24px">' . implode( '. ', $prose ) . '.</p>';
$s[] = al_sec( 'al-section al-section--mist al-diag-top', $p );

// 6 — dokumentacija (paper) — isti oblik kao 16684
$DOCS = array(
	array( 'Katalog dezena', '2022/06/EXPONA-SIMPLAY-19dB-_obj_SIMP_19db_BRO_200319_Internet.pdf' ),
	array( 'Deklaracija o performansama', '2022/06/DOP-EXPONA-SIMPLAY-19dB_DoP-EXPONA-SIMPLAY-19db_2020_all-languages.pdf' ),
	array( 'Uputstvo za ugradnju', '2022/06/Installation-guide-_-EXPONA-SIMPLAY-19dB_V_SIMP19dB_D_191127.pdf' ),
);
$d = '<span class="al-label">Dokumentacija</span><h2 class="al-display--lg">Preuzmite dokumentaciju</h2>'
	. '<div class="al-grid al-grid--3" style="margin-top:32px">';
foreach ( $DOCS as $doc ) {
	$d .= '<div class="al-card"><div class="al-card__body"><a class="al-btn al-btn--ghost" href="'
		. $BASE . '/wp-content/uploads/' . $doc[1] . '" target="_blank" rel="noopener">' . $doc[0] . '</a></div></div>';
}
$d .= '</div>';
$s[] = al_sec( 'al-section al-section--paper', $d );

// 7 — FAQ (mist ✂) + FAQPage JSON-LD
$f = '<span class="al-label">Pitanja</span><h2 class="al-display--lg">Najčešća pitanja o EXPONA Simplay podu</h2>';
$ent = array();
foreach ( $FAQ as $qa ) {
	$f .= '<h3>' . $qa[0] . '</h3><p>' . $qa[1] . '</p>';
	$ent[] = array( '@type' => 'Question', 'name' => $qa[0],
		'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $qa[1] ) );
}
$f .= '<script type="application/ld+json">'
	. wp_json_encode( array( '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $ent ), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES )
	. '</script>';
$s[] = al_sec( 'al-section al-section--mist al-diag-top', $f );

// 8 — CTA (navy ✂--rev)
$s[] = al_sec( 'al-section al-section--navy al-plates al-diag-top--rev',
	'<h2 class="al-display--lg">Spremni za EXPONA Simplay pod?</h2>'
	. '<p>Pošaljite dimenzije prostora — dobijate ponudu i uzorke dezena.</p>'
	. '<div class="al-hero__cta"><a class="al-btn" href="' . $BASE . '/kontakt/?form-naslov=Ponuda: EXPONA Simplay 19dB">Zatražite ponudu</a>'
	. '<a class="al-btn al-btn--ghost" href="' . $TEL . '">' . $TELTX . '</a></div>' );

$content = implode( '', $s );

// ------------------------------------------------------------------ upis
$exists = get_page_by_path( 'lvt-podovi-za-komercijalne-i-javne-prostore/' . $SLUG );
WP_CLI::log( sprintf( 'sekcija: %d · sadržaj: %d bajtova · postoji već: %s',
	count( $s ), strlen( $content ), $exists ? '#' . $exists->ID : 'ne' ) );

if ( ! $APPLY ) { WP_CLI::success( 'PROBA — ništa nije upisano (dodaj `apply`)' ); return; }

$pid = $exists ? $exists->ID : 0;
$data = array(
	'post_title'   => 'EXPONA Simplay 19dB — loose-lay LVT podovi bez lepka',
	'post_name'    => $SLUG,
	'post_content' => $content,
	'post_status'  => 'publish',
	'post_type'    => 'page',
	'post_parent'  => $PARENT,
);
if ( $pid ) { $data['ID'] = $pid; $pid = wp_update_post( $data ); }
else        { $pid = wp_insert_post( $data ); }
if ( is_wp_error( $pid ) ) { WP_CLI::error( $pid->get_error_message() ); }

update_post_meta( $pid, '_woodmart_title_off', 'on' );        // F7.14 — inače 2×H1
update_post_meta( $pid, '_woodmart_main_layout', 'full-width' );
update_post_meta( $pid, '_thumbnail_id', 7964 );
update_post_meta( $pid, '_yoast_wpseo_title', 'EXPONA Simplay 19dB — loose-lay LVT pod %%sep%% %%sitename%%' );
update_post_meta( $pid, '_yoast_wpseo_metadesc', 'Samopolažuća LVT pločica bez lepka sa IXPE podlogom: udarni zvuk manji za 19 dB, 12 dezena, pod pregaziv odmah po ugradnji. Ponuda i uzorci na upit.' );

WP_CLI::success( sprintf( 'stranica #%d objavljena: /%s/%s/', $pid, 'lvt-podovi-za-komercijalne-i-javne-prostore', $SLUG ) );
