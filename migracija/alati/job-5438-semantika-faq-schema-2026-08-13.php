<?php
/**
 * job-5438-semantika-faq-schema-2026-08-13.php — stavka E iz plana kanibalizacije:
 * `/sportske-podloge/` (5438) vraća basket-semantiku, dobija planer link i FAQPage.
 *
 * DOSIJE ODLUKE (svež `gsc_page_queries.py` pull 13.08, period 15.05→12.08 — NE iz
 * vault CSV-ova, koji su tri puta u jednoj sesiji bili pogrešni):
 *   5438 live verzija: 1.422 prikaza / 178 klikova / 90d
 *   🔴 basket klaster nosi 138 od 178 klikova (78%), ne „skoro polovinu" kako plan
 *      `seo/2026-08-13-kanibalizacija-konsolidacija-plan.md` §3.8 procenjuje:
 *        podloga za kosarkaski teren       113 prikaza /  47 klikova / poz. 1,6
 *        podloga za kosarkaski teren cena  147 /  39 / 2,0
 *        podloga za basket                  31 /  14 / 1,3
 *        podloge za kosarkaski teren        37 /  13 / 1,5
 *        podloga za kosarku                 48 /  12 / 2,4
 *        podloga za kosarkaski teren        48 /  12 / 2,5
 *   Build 5438 NEMA H2 „Izgradnja sportskih terena za basket u vasem dvoristu!",
 *   nema „Vrste podloga za sportski teren?" i NE pominje `/planer-terena/`.
 *   Nov nalaz (nije u planu §3.8): stranica ima FAQ sa 4 pitanja ali NEMA FAQPage
 *   JSON-LD — samo Article i VideoObject. Hub `/industrijski-podovi/` ga je dobio
 *   13.08 (stavka K), ovde je propusten.
 *
 * STA RADI (jedna skripta, jer sva cetiri koraka diraju ISTI post_content, a schema
 * zavisi od rezultata izmene FAQ-a — razdvajanje bi znacilo tri $wpdb->update ciklusa
 * nad istim poljem):
 *   1) sekcija A „Vrste podloga za sportski teren?" (mist) — doslovan live tekst
 *   2) sekcija B „Izgradnja sportskih terena za basket u vasem dvoristu!" (paper) —
 *      doslovan live tekst + `<ul>` modela + CTA ka `/planer-terena/`
 *   3) FAQ par #3 „Sportski podovi — cena?" → „Koliko kosta podloga za kosarkaski
 *      teren?" (bukvalno GSC upit sa 39 klikova, ulazi u FAQPage schemu)
 *   4) FAQPage JSON-LD — gradi se PARSIRANJEM vidljivog teksta, ne rucnim prepisom
 *      (inace se vremenom raziđu, sto Google tretira kao neusklađenost)
 *
 * TEKST: doslovno sa live-a (`zn_page_builder_els`, live post 1849, Zion Builder, iz
 * `migracija/live-export-2026-07-05/live-pages-2026-07-05.xml`). Live markup se NE
 * kopira — samo tekst. Ispravljene dve ciste stamparske greske (`dicipline`→
 * `discipline`, dupli razmak). „Antas line" NAMERNO ostaje kako stoji na live-u.
 *
 * 🔴 PROJEKTNA PRAVILA (ne opciona):
 *   · Upis ISKLJUCIVO $wpdb->update() + clean_post_cache(). `wp_update_post()` skida
 *     `[al_skica]` i kvari `css=""`; `mysql` CLI kvari dijakritike (ć = HEX C487).
 *   · Citanje $wpdb->get_var(), ne get_post_field() (wptexturize).
 *   · Anchor-based umetanje (strpos/substr), nikad prekucani „old" literali.
 *     SVA SIDRA ASCII — s/c/— nikad ne prolaze kroz izvorni kod.
 *   · Nov HTML kao <<<'HTML' nowdoc, svaka sekcija u JEDNOJ liniji (prelom → <br>).
 *   · Nove sekcije su <h2>, nikad <h1>.
 *   · Bez `al-diag-*` (susedne sekcije nemaju rez → dupli dijagonalni rez).
 *   · `_wpb_shortcodes_custom_css` se PROVERAVA, NE regenerise — nove sekcije ne unose
 *     nijedan `css=""` atribut, a slepa regeneracija bi mogla obrisati
 *     `.vc_custom_heroF45438` (hero pozadina).
 *
 * UPOTREBA:
 *   C:\xampp\php\php.exe job-5438-semantika-faq-schema-2026-08-13.php          (probni prolaz)
 *   C:\xampp\php\php.exe job-5438-semantika-faq-schema-2026-08-13.php --write  (upis)
 *
 * Backup pre upisa: antasline-backups/antasline_local_2026-08-13_pre-5438-semantika.sql
 * Rollback bez restore-a baze: antasline-backups/5438-post_content_2026-08-13_pre.txt
 * Verifikacija: migracija/alati/verify-5438-2026-08-13.php
 */

require_once 'C:/xampp/htdocs/antasline/wp-load.php';

global $wpdb;
$WRITE  = in_array( '--write', $argv, true );
$ID     = 5438;
$greske = 0;
$BACKUP = 'C:/xampp/htdocs/antasline-backups/5438-post_content_2026-08-13_pre.txt';

function sadrzaj( $id ) {
	global $wpdb;
	return $wpdb->get_var( $wpdb->prepare( "SELECT post_content FROM {$wpdb->posts} WHERE ID=%d", $id ) );
}
function upisi( $id, $novo, $WRITE ) {
	global $wpdb;
	if ( ! $WRITE ) { printf( "  [probno] %d bi dobio %d bajtova\n", $id, strlen( $novo ) ); return; }
	$wpdb->update( $wpdb->posts, array( 'post_content' => $novo ), array( 'ID' => $id ) );
	clean_post_cache( $id );
	printf( "  ✅ %d upisan (%d bajtova)\n", $id, strlen( $novo ) );
}

/* ═══════════════════════════════════════════════════════════ nov sadržaj (nowdoc) */

// Sekcija A — mist. Bez al-diag-*: susedna sekcija [3] nema rez.
$SEKCIJA_A = <<<'HTML'
[vc_row full_width="stretch_row" el_class="al-section al-section--mist"][vc_column][vc_column_text]<span class="al-label">Izbor podloge</span><h2 class="al-display--lg">Vrste podloga za sportski teren?</h2><p>Pri odabiru sportskih podloga veoma je bitan osećaj koji se pruža igračima, odskok i trenje, ali i drugi parametri kao što su održavanje podloge, kvalitet i dugotrajnost. Sportski pod Ultimate je jedinstvena sportska podloga na kojoj se mogu igrati različite sportske discipline. Napravljena je prema standardima svetskih sportskih asocijacija.</p><p>Pruža odličan osećaj tokom igre i pozitivno utiče na kolena i zglobove. Ultimate podovi nude visok nivo performansi uzimajući u obzir sve ove faktore. Montaža podova sama po sebi ne može biti jednostavnija što predstavlja još jednu veliku prednost. Jedinstvena izrada omogućava dobru izdržljivost, odskok i trenje.</p>[/vc_column_text][/vc_column][/vc_row]
HTML;

// Sekcija B — paper. „Modela sportskih podova: X / Y / Z" razlomljeno u <ul> (reči
// nepromenjene, samo „Modela"→„Modeli", padež posle uklanjanja kosih crta); <ul> u
// [vc_column_text] je već dokazan u sekciji [3]. CTA je kopija dokazanog obrasca sa
// 17019 (<div style="margin-top:20px"><a class="al-btn">), ne `.al-cta-box` — taj CSS
// je vezan za `.entry-content`/`.wd-entry-content` i nije verifikovan na full-width
// WPBakery stranici. Predzadnji <p> je jedina rečenica koja nije sa live-a (most ka CTA).
$SEKCIJA_B = <<<'HTML'
[vc_row full_width="stretch_row" el_class="al-section al-section--paper"][vc_column][vc_column_text]<span class="al-label">Izgradnja terena</span><h2 class="al-display--lg">Izgradnja sportskih terena za basket u vašem dvorištu!</h2><p>Antas line vam nudi kompletnu uslugu projektovanja i izgradnje sportskih terena. Na osnovu vaših želja i potreba pravimo projekat za izradu sportskog terena, vršimo montažu sportske podloge i prateće opreme. U ponudi imamo pored sportskih podova i opremu za košarkaške, odbojkaške, rukometne, teniske i fudbalske terene.</p><h3>Podovi za sportske terene cena - koliko košta da se napravi teren za basket?</h3><p>Cena izgradnje sportskog terena zavisi od više faktora. Mi u našoj ponudi imamo više modela sportskih podloga. Cena pored izbora modela sportske podloge zavisi i od lokacije gde se montira kao i od kvadrature terena. Modeli sportskih podova:</p><ul><li>pod za teniske terene</li><li>podloga za basket, odbojku, rukomet</li><li>multisport sportska podloga</li><li>sportska podloga za manje zahtevne terene</li><li>veštačka trava</li><li>sportski podovi za sale</li><li>akrilne podloge za tenis</li></ul><p>U ponudi imamo 10 standardnih boja koje se mogu kombinovati. Kombinacijom boja dobijate jedinstven izgled vašeg terena. Boja podloge ne utiče na cenu. Za cenu sportskog poda i više detalja možete nas pozvati ili poslati mail.</p><p>Ne morate čekati ponudu da biste videli obim posla — u planeru birate sport, dimenzije i boje i odmah dobijate broj ploča, m² i PDF specifikaciju.</p><div style="margin-top:20px"><a class="al-btn" href="http://localhost/antasline/planer-terena/">Otvorite planer terena</a></div>[/vc_column_text][/vc_column][/vc_row]
HTML;

// Zamena FAQ para #3. Live ima samo jedan cena-naslov, koji zadržava sekcija B; FAQ
// H3 postaje bukvalno GSC upit sa 39 klikova, čime cena-pitanje ulazi u FAQPage.
$FAQ_NOVI = <<<'HTML'
<h3>Koliko košta podloga za košarkaški teren?</h3><p>Cena zavisi od izabranog modela sportske podloge, kvadrature terena i lokacije montaže. Boja podloge ne utiče na cenu — u ponudi je 10 standardnih boja koje se mogu kombinovati. Pošaljite dimenzije terena ili složite teren u <a href="http://localhost/antasline/planer-terena/">planeru terena</a> i vraćamo ponudu sa specifikacijom.</p>
HTML;

/* ═══════════════════════════════════════════════════════════════════ polazno stanje */

printf( "=== 0) polazno stanje\n" );
$c = sadrzaj( $ID );
if ( null === $c ) { printf( "  🔴 %d ne postoji\n", $ID ); exit( 1 ); }
$PRE_LEN = strlen( $c );
printf( "  PRE: %d B · %d [vc_row] · %d h2 · %d h3 · %d script\n",
	$PRE_LEN, substr_count( $c, '[vc_row ' ), substr_count( $c, '<h2' ),
	substr_count( $c, '<h3' ), substr_count( $c, '<script' ) );

// Rollback snimak — pravi se samo pri prvom --write. Ponovno pokretanje (K5) ga NE
// pregazi, inače bi „backup" posle prvog upisa sadržao već izmenjenu verziju.
if ( $WRITE ) {
	if ( file_exists( $BACKUP ) ) {
		printf( "  ⚠️  rollback snimak već postoji (%d B) — ne diram\n", filesize( $BACKUP ) );
	} else {
		file_put_contents( $BACKUP, $c );
		printf( "  ✅ rollback snimak: %s (%d B)\n", basename( $BACKUP ), filesize( $BACKUP ) );
	}
}

/* ═══════════════════════════════════════════════ 1) sekcije A + B pre FAQ sekcije */

printf( "\n=== 1) sekcije A + B (semantika + planer link)\n" );

// Kraće sidro (`al-section--mist"]`) bi posle ovog koraka bilo dvosmisleno — sekcija A
// i sama nosi `mist`. Uz `<span class="al-label">Pitanja</span>` sidro ostaje jedinstveno
// zauvek (nove labele su „Izbor podloge" i „Izgradnja terena").
$SIDRO = 'el_class="al-section al-section--mist"][vc_column][vc_column_text]'
	. '<span class="al-label">Pitanja</span>';

if ( strpos( $c, 'Vrste podloga za sportski teren' ) !== false ) {
	printf( "  ⚠️  već upisano — preskačem\n" );
} else {
	$n = substr_count( $c, $SIDRO );
	printf( "  sidro jedinstveno: %d\n", $n );
	if ( 1 !== $n ) {
		printf( "  🔴 sidro nije jedinstveno (%d) — prekid koraka\n", $n );
		$greske++;
	} else {
		$pos = strpos( $c, '[vc_row full_width="stretch_row" ' . $SIDRO );
		if ( false === $pos ) {
			printf( "  🔴 početak [vc_row] FAQ sekcije nije nađen\n" );
			$greske++;
		} else {
			$c = substr( $c, 0, $pos ) . $SEKCIJA_A . $SEKCIJA_B . substr( $c, $pos );
			printf( "  umetnuto %d B pre FAQ sekcije\n", strlen( $SEKCIJA_A ) + strlen( $SEKCIJA_B ) );
			upisi( $ID, $c, $WRITE );
		}
	}
}

/* ═══════════════════════════════════════════════════════ 2) zamena FAQ para #3 */

printf( "\n=== 2) FAQ par #3 → cena-pitanje sa 39 klikova\n" );

// Raspon između dva ASCII sidra, bez prekucavanja starog teksta. `<h3>Sportski podovi`
// je jedinstveno: sekcija B počinje sa `<h3>Podovi za sportske terene cena`.
if ( strpos( $c, '<h3>Sportski podovi' ) === false ) {
	printf( "  ⚠️  već zamenjeno — preskačem\n" );
} else {
	$p = strpos( $c, '<h3>Sportski podovi' );
	$k = strpos( $c, '</p>', $p );
	if ( false === $k ) {
		printf( "  🔴 kraj para (</p>) nije nađen\n" );
		$greske++;
	} else {
		$stari = substr( $c, $p, $k + 4 - $p );
		printf( "  stari par (%d B): %s\n", strlen( $stari ), mb_substr( strip_tags( $stari ), 0, 80 ) );
		if ( strlen( $stari ) > 400 || stripos( $stari, 'cena' ) === false ) {
			printf( "  🔴 raspon sumnjiv (>400 B ili bez „cena\") — prekid koraka\n" );
			$greske++;
		} else {
			$c = substr( $c, 0, $p ) . $FAQ_NOVI . substr( $c, $k + 4 );
			upisi( $ID, $c, $WRITE );
		}
	}
}

/* ═════════════════════════════════════════════════════════════ 3) FAQPage JSON-LD */

printf( "\n=== 3) FAQPage JSON-LD (gradi se iz vidljivog teksta)\n" );

$c_pre_schema = $c;

// Stari blok se prvo briše u celosti, pa gradi iznova — idempotentno i pri budućim
// izmenama FAQ-a. (Na 5438 ga trenutno nema; kod je isti kao na 16567 zbog budućih izmena.)
// Zamena je PRAZAN string, ne "\n": umetanje ispod nosi svoj vodeći prelom, pa bi "\n"
// pri svakom ponovnom pokretanju dodavalo po jedan bajt (uhvaćeno na K5 13.08 —
// 15.129 → 15.130 B). Ovako je ciklus brisanje+gradnja bajt-identičan.
$c2 = preg_replace( '~\s*<script type="application/ld\+json">.*?</script>\s*~s', '', $c, 1, $obrisano );
printf( "  obrisan stari schema blok: %s\n", $obrisano ? 'da' : 'ne (nije ga ni bilo)' );

// 🔴 Parse ISKLJUČIVO nad substr FAQ bloka — sekcija [1] ima 6 USP kartica sa <h3>+<p>,
// a nova sekcija B takođe ima <h3>+<p>. Strogo !== 4: višak je jednako sumnjiv kao
// manjak (znači da je opseg iscurio izvan FAQ sekcije).
$pn = strpos( $c2, 'Postavljanje podloge za sportski teren' );
$pk = ( false !== $pn ) ? strpos( $c2, '[/vc_column_text]', $pn ) : false;

if ( false === $pn || false === $pk ) {
	printf( "  🔴 FAQ blok nije nađen — schema se NE upisuje\n" );
	$greske++;
} else {
	$blok = substr( $c2, $pn, $pk - $pn );
	preg_match_all( '~<h3[^>]*>(.*?)</h3>\s*<p>(.*?)</p>~s', $blok, $m, PREG_SET_ORDER );
	printf( "  parsirano %d parova pitanje/odgovor\n", count( $m ) );

	if ( 4 !== count( $m ) ) {
		printf( "  🔴 očekivano tačno 4 — opseg je iscurio ili je FAQ izmenjen; schema se NE upisuje\n" );
		$greske++;
	} else {
		$items = array();
		foreach ( $m as $par ) {
			$q = trim( html_entity_decode( strip_tags( $par[1] ), ENT_QUOTES, 'UTF-8' ) );
			$a = trim( html_entity_decode( strip_tags( $par[2] ), ENT_QUOTES, 'UTF-8' ) );
			if ( '' === $q || '' === $a ) { continue; }
			$items[] = array(
				'@type'          => 'Question',
				'name'           => $q,
				'acceptedAnswer' => array( '@type' => 'Answer', 'text' => $a ),
			);
			printf( "    · %s\n", mb_substr( $q, 0, 70 ) );
		}
		$json = json_encode(
			array( '@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $items ),
			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
		);
		$c2 = substr( $c2, 0, $pk ) . "\n<script type=\"application/ld+json\">\n" . $json . "\n</script>\n" . substr( $c2, $pk );

		if ( $c2 === $c_pre_schema ) {
			printf( "  ⚠️  već upisano — sadržaj nepromenjen\n" );
		} else {
			$c = $c2;
			upisi( $ID, $c, $WRITE );
		}
	}
}

/* ═══════════════════════════════════════════════════════ 4) meta, keševi, izveštaj */

printf( "\n=== 4) hero CSS meta (proverava se, NE regeneriše)\n" );
$hero = get_post_meta( $ID, '_wpb_shortcodes_custom_css', true );
if ( strpos( (string) $hero, 'vc_custom_heroF45438' ) !== false ) {
	printf( "  ✅ hero CSS pravilo prisutno (%d B) — nove sekcije ne unose nijedan css=\"\" atribut\n", strlen( $hero ) );
} else {
	printf( "  🔴 hero CSS pravilo NESTALO — .vc_custom_heroF45438 nije u _wpb_shortcodes_custom_css\n" );
	$greske++;
}

if ( $WRITE ) {
	// 🔴 Istorijski (do 2026-08-14): lokalni wp-config je nosio `wpGs_`, a MySQL vraćao `wpgs_` (Windows
	// lower_case_table_names=1). Strogo poređenje sa $wpdb->prefix ovde je 13.08 tiho
	// preskočilo brisanje — isti razred greške koji na Linux hostingu obara migraciju.
	$tabela = strtolower( $wpdb->prefix ) . 'yoast_indexable';
	if ( strtolower( (string) $wpdb->get_var( "SHOW TABLES LIKE '{$tabela}'" ) ) === $tabela ) {
		$obr = $wpdb->query( $wpdb->prepare( "DELETE FROM {$tabela} WHERE object_id=%d", $ID ) );
		printf( "\n✅ yoast_indexable obrisan (%d red)\n", (int) $obr );
	} else {
		printf( "\n⚠️  yoast_indexable tabela ne postoji — preskačem\n" );
	}
	if ( class_exists( '\RankMath\Sitemap\Cache' ) ) {
		\RankMath\Sitemap\Cache::invalidate_storage();
		printf( "✅ Rank Math sitemap keš invalidiran\n" );
	}
}

printf( "\n=== POSLE\n" );
$fin = $WRITE ? sadrzaj( $ID ) : $c;
printf( "  %s: %d B (PRE %d, %+d) · %d [vc_row] · %d h2 · %d h3 · %d script\n",
	$WRITE ? 'u bazi' : 'u memoriji (probno)',
	strlen( $fin ), $PRE_LEN, strlen( $fin ) - $PRE_LEN,
	substr_count( $fin, '[vc_row ' ), substr_count( $fin, '<h2' ),
	substr_count( $fin, '<h3' ), substr_count( $fin, '<script' ) );

printf( "\n%s\n", $greske ? "🔴 GREŠAKA: $greske" : '✅ bez grešaka' );
printf( "\nSledeći koraci (ručno):\n" );
printf( "  · verify-5438-2026-08-13.php (K6) — 3 JSON-LD bloka, 1× FAQPage sa 4 Question\n" );
printf( "  · Chrome 1440/390 px (K7) — al-btn crven, 11 kartica cele, 0 console grešaka\n" );
printf( "  · al_verify.php 5438,17004,2298,17019,16676,17027 (K8)\n" );
printf( "  · 🔴 build-staging-package.sh POSLE ove izmene, inače freeze hvata staru verziju\n" );
