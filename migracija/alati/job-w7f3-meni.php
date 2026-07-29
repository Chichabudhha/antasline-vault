<?php
/**
 * W7 F3.2–3.4 — nov glavni meni.
 *
 * Gradi se kao NOV term; stari (67 „O firmi") ostaje netaknut dok M ne potvrdi,
 * pa je povratak jedna izmena opcije `nav_menu_locations`.
 *
 * Proba:      eval-file job-w7f3-meni.php
 * Izvršenje:  eval-file job-w7f3-meni.php apply
 */
$apply     = in_array( 'apply', $args, true );
$menu_name = 'Glavni meni 2026';

/**
 * Struktura. Nivo 0 = grupa, nivo 1 = kolona mega-menija (ili stavka kod
 * običnog padajućeg menija), nivo 2 = stranica u koloni.
 * 'design' => 'sized' pretvara decu u kolone (WoodMart mega-menu walker).
 */
$struktura = array(
	array(
		'naslov' => 'Sport',
		'page'   => 5438,
		'design' => 'sized',
		'width'  => 760,
		'deca'   => array(
			array(
				'naslov' => 'Tereni po sportu',
				'url'    => '#',
				'deca'   => array(
					array( 'naslov' => 'Košarkaški tereni i konstrukcije', 'page' => 16657 ),
					array( 'naslov' => 'Tereni za 3x3 košarku',           'page' => 16584 ),
					array( 'naslov' => 'Teniski tereni',                  'page' => 17028 ),
					array( 'naslov' => 'Padel tereni',                    'page' => 16670 ),
					array( 'naslov' => 'Pickleball tereni',               'page' => 16680 ),
					array( 'naslov' => 'Futsal tereni',                   'page' => 16581 ),
					array( 'naslov' => 'Hokejaški tereni',                'page' => 16582 ),
					array( 'naslov' => 'Stoni tenis',                     'page' => 16583 ),
					array( 'naslov' => 'Sportske sale i baloni',          'page' => 16661 ),
				),
			),
			array(
				'naslov' => 'Podloge i oprema',
				'url'    => '#',
				'deca'   => array(
					array( 'naslov' => 'Bergo Ultimate',              'page' => 15480 ),
					array( 'naslov' => 'Veštačka trava za fudbal',    'page' => 5119 ),
					array( 'naslov' => 'Oprema za sportske terene',   'page' => 16676 ),
					array( 'naslov' => 'Reflektori za terene',        'page' => 16677 ),
					array( 'naslov' => 'Galerija izvedenih terena',   'page' => 16674 ),
				),
			),
			array(
				'naslov' => 'Dimenzije terena',
				'url'    => '#',
				'deca'   => array(
					array( 'naslov' => 'Košarkaški teren',      'page' => 16586 ),
					array( 'naslov' => 'Tabla za košarku',      'page' => 16585 ),
					array( 'naslov' => 'Teniski teren',         'page' => 16688 ),
					array( 'naslov' => 'Fudbalski teren',       'page' => 17027 ),
				),
			),
		),
	),
	array(
		'naslov' => 'Industrija',
		'page'   => 16567,
		'design' => 'sized',
		'width'  => 760,
		'deca'   => array(
			array(
				'naslov' => 'Po delatnosti',
				'url'    => '#',
				'deca'   => array(
					array( 'naslov' => 'Magacini i proizvodne hale',      'page' => 16687 ),
					array( 'naslov' => 'Garaže i auto-servisi',           'page' => 16664 ),
					array( 'naslov' => 'Hemijska i prehrambena industrija', 'page' => 17017 ),
					array( 'naslov' => 'Zdravstveni objekti',             'page' => 17018 ),
					array( 'naslov' => 'Teretane i fitnes centri',        'page' => 17020 ),
					array( 'naslov' => 'Gumeni podovi za javne objekte',  'page' => 17029 ),
				),
			),
			array(
				'naslov' => 'Ecotile podne ploče',
				'url'    => '#',
				'deca'   => array(
					array( 'naslov' => 'PVC podne ploče — pregled', 'page' => 17026 ),
					array( 'naslov' => 'Ecotile 500/5',             'page' => 16682 ),
					array( 'naslov' => 'Ecotile 500/7',             'page' => 16660 ),
					array( 'naslov' => 'Ecotile 500/10',            'page' => 16678 ),
					array( 'naslov' => 'Antistatik (ESD) podovi',   'page' => 16658 ),
				),
			),
			array(
				'naslov' => 'Oprema i saveti',
				'url'    => '#',
				'deca'   => array(
					array( 'naslov' => 'Trake za obeležavanje',          'page' => 16666 ),
					array( 'naslov' => 'Bumperi i zaštita',              'page' => 16671 ),
					array( 'naslov' => 'Ergonomske podloge',             'page' => 16672 ),
					array( 'naslov' => 'Montaža preko oštećenog epoksida', 'page' => 16675 ),
					array( 'naslov' => 'Najčešća pitanja',               'page' => 17025 ),
				),
			),
		),
	),
	array(
		'naslov' => 'Terase i dom',
		'page'   => 16590,
		'design' => 'sized',
		'width'  => 540,
		'deca'   => array(
			array(
				'naslov' => 'Bergo ploče',
				'url'    => '#',
				'deca'   => array(
					array( 'naslov' => 'Bergo — pregled programa', 'page' => 17019 ),
					array( 'naslov' => 'Bergo Unique',             'page' => 16679 ),
					array( 'naslov' => 'Bergo Elite',              'page' => 16681 ),
					array( 'naslov' => 'Bergo XL',                 'page' => 16659 ),
				),
			),
			array(
				'naslov' => 'Po nameni',
				'url'    => '#',
				'deca'   => array(
					array( 'naslov' => 'Podovi oko bazena',           'page' => 16662 ),
					array( 'naslov' => 'Veštačka trava za terase',    'page' => 16673 ),
					array( 'naslov' => 'Veštačka trava — svi modeli', 'page' => 5455 ),
				),
			),
		),
	),
	array(
		'naslov' => 'Poslovni',
		'page'   => 16667,
		'design' => 'sized',
		'width'  => 540,
		'deca'   => array(
			array(
				'naslov' => 'EXPONA LVT program',
				'url'    => '#',
				'deca'   => array(
					array( 'naslov' => 'EXPONA Clic 19dB',    'page' => 16684 ),
					array( 'naslov' => 'EXPONA Commercial',   'page' => 16685 ),
					array( 'naslov' => 'EXPONA Flow',         'page' => 16668 ),
					array( 'naslov' => 'EXPONA Simplay 19dB', 'page' => 17252 ),
				),
			),
			array(
				'naslov' => 'Po nameni',
				'url'    => '#',
				'deca'   => array(
					array( 'naslov' => 'Kancelarije i poslovni prostori', 'page' => 16669 ),
					array( 'naslov' => 'Hoteli, restorani i kafići',      'page' => 16686 ),
					array( 'naslov' => 'Prodavnice i maloprodaja',        'page' => 16142 ),
					array( 'naslov' => 'Radnje — Ecotile ploče',          'page' => 16683 ),
				),
			),
		),
	),
	array(
		'naslov' => 'Specijalni',
		'url'    => '#',
		'design' => 'sized',
		'width'  => 540,
		'deca'   => array(
			array(
				'naslov' => 'Privremene i mobilne',
				'url'    => '#',
				'deca'   => array(
					array( 'naslov' => 'Isotrack — puteva i gradilišta', 'page' => 16111 ),
					array( 'naslov' => 'Sajmovi i manifestacije',        'page' => 16665 ),
					array( 'naslov' => 'Rentiranje podloga',             'page' => 16663 ),
				),
			),
			array(
				'naslov' => 'Spoljne površine',
				'url'    => '#',
				'deca'   => array(
					array( 'naslov' => 'Parkiralište i staze',    'page' => 16589 ),
					array( 'naslov' => 'Zaštita trave i pločnika', 'page' => 15793 ),
					array( 'naslov' => 'Štale i hipodromi',        'page' => 5791 ),
				),
			),
		),
	),
	array(
		'naslov' => 'Cene',
		'page'   => 17273,
		'design' => 'sized',
		'width'  => 540,
		'deca'   => array(
			array(
				'naslov' => 'Industrija',
				'url'    => '#',
				'deca'   => array(
					array( 'naslov' => 'Industrijski podovi', 'page' => 16874 ),
					array( 'naslov' => 'Podovi za garaže',    'page' => 16875 ),
				),
			),
			array(
				'naslov' => 'Spolja',
				'url'    => '#',
				'deca'   => array(
					array( 'naslov' => 'Gumeni podovi za terase', 'page' => 16873 ),
					array( 'naslov' => 'Podloge za parkiralište', 'page' => 16876 ),
				),
			),
		),
	),
);

/* ---------- 1. Provera svih ciljeva pre bilo kakvog upisa ---------- */
$svi_id = array();
$greske = array();

$skupi = function ( $cvorovi ) use ( &$skupi, &$svi_id, &$greske ) {
	foreach ( $cvorovi as $c ) {
		if ( isset( $c['page'] ) ) {
			$p = get_post( $c['page'] );
			if ( ! $p ) {
				$greske[] = "{$c['page']} ({$c['naslov']}) — NE POSTOJI";
			} elseif ( 'publish' !== $p->post_status ) {
				$greske[] = "{$c['page']} ({$c['naslov']}) — status {$p->post_status}";
			} else {
				if ( in_array( $c['page'], $svi_id, true ) ) {
					$greske[] = "{$c['page']} ({$c['naslov']}) — DUPLIKAT u meniju";
				}
				$svi_id[] = $c['page'];
			}
		}
		if ( ! empty( $c['deca'] ) ) { $skupi( $c['deca'] ); }
	}
};
$skupi( $struktura );

echo "=== PROVERA CILJEVA ===\nstavki sa stranicom: " . count( $svi_id ) . "\n";
if ( $greske ) {
	echo "🔴 GREŠKE:\n  " . implode( "\n  ", $greske ) . "\n";
	return;
}
echo "sve stranice postoje, objavljene su i nijedna se ne ponavlja ✅\n";

/* ---------- 2. Pokrivenost: koje objavljene stranice ostaju van menija ---------- */
$namerno_van = array(
	16550 => 'Početna — logo/utility meni',
	21    => 'Aktuelnosti — utility meni',
	61    => 'Kontakt — utility meni + futer',
	571   => 'O nama — utility meni',
	16656 => 'Politika kolačića — futer',
	16736 => 'Katalog — futer (Woo shop arhiva)',
	16600 => 'Hvala za poruku — GA4 cilj, nikad u meni',
	17004 => 'Planer terena — alat, CTA sa sportskih stranica',
	15580 => 'Podloge za parking — 301 na 16589 (odluka M)',
	5754  => 'DUPLIKAT 17028 — čeka odluku M',
	5769  => 'DUPLIKAT 16665 — čeka odluku M',
	5512  => 'DUPLIKAT 16667 — čeka odluku M',
	16171 => 'DUPLIKAT 16674 — čeka odluku M',
);

$sve = get_posts( array( 'post_type' => 'page', 'post_status' => 'publish', 'numberposts' => -1, 'fields' => 'ids' ) );
$van = array_diff( $sve, $svi_id );
echo "\n=== POKRIVENOST ===\nobjavljenih stranica: " . count( $sve ) . " | u meniju: " . count( $svi_id ) . " | van: " . count( $van ) . "\n";
$neobjasnjeno = 0;
foreach ( $van as $id ) {
	$razlog = isset( $namerno_van[ $id ] ) ? $namerno_van[ $id ] : '🔴 NEOBJAŠNJENO';
	if ( ! isset( $namerno_van[ $id ] ) ) { $neobjasnjeno++; }
	echo sprintf( "  %-6d %-46s %s\n", $id, get_post_field( 'post_name', $id ), $razlog );
}
echo $neobjasnjeno ? "🔴 $neobjasnjeno stranica bez objašnjenja\n" : "svaka stranica van menija ima razlog ✅\n";

if ( ! $apply ) {
	echo "\n=== STRUKTURA (proba) ===\n";
	$ispisi = function ( $cvorovi, $d = 0 ) use ( &$ispisi ) {
		foreach ( $cvorovi as $c ) {
			$cilj = isset( $c['page'] ) ? '→ ' . get_permalink( $c['page'] ) : '(' . ( isset( $c['url'] ) ? $c['url'] : '#' ) . ')';
			echo str_repeat( '    ', $d ) . '· ' . $c['naslov'] . '  ' . $cilj . "\n";
			if ( ! empty( $c['deca'] ) ) { $ispisi( $c['deca'], $d + 1 ); }
		}
	};
	$ispisi( $struktura );
	echo "\n(bez 'apply' — ništa nije upisano)\n";
	return;
}

/* ---------- 3. Izgradnja ---------- */
$stari = wp_get_nav_menu_object( $menu_name );
if ( $stari ) {
	echo "\n🔴 STOP: meni „$menu_name\" već postoji (term {$stari->term_id}). Obriši ga pa ponovi.\n";
	return;
}

$menu_id = wp_create_nav_menu( $menu_name );
if ( is_wp_error( $menu_id ) ) {
	echo '🔴 GREŠKA: ' . $menu_id->get_error_message() . "\n";
	return;
}
echo "\n✅ Meni napravljen: term $menu_id\n";

$order = 0;
$ubaci = function ( $cvorovi, $parent = 0 ) use ( &$ubaci, $menu_id, &$order ) {
	foreach ( $cvorovi as $c ) {
		$order++;
		$data = array(
			'menu-item-title'     => $c['naslov'],
			'menu-item-parent-id' => $parent,
			'menu-item-position'  => $order,
			'menu-item-status'    => 'publish',
		);
		if ( isset( $c['page'] ) ) {
			$data['menu-item-type']      = 'post_type';
			$data['menu-item-object']    = 'page';
			$data['menu-item-object-id'] = $c['page'];
		} else {
			$data['menu-item-type'] = 'custom';
			$data['menu-item-url']  = isset( $c['url'] ) ? $c['url'] : '#';
		}
		$item_id = wp_update_nav_menu_item( $menu_id, 0, $data );
		if ( is_wp_error( $item_id ) ) {
			echo '  🔴 ' . $c['naslov'] . ': ' . $item_id->get_error_message() . "\n";
			continue;
		}
		if ( ! empty( $c['design'] ) ) {
			update_post_meta( $item_id, '_menu_item_design', $c['design'] );
			if ( ! empty( $c['width'] ) ) {
				update_post_meta( $item_id, '_menu_item_width', (int) $c['width'] );
			}
		}
		if ( ! empty( $c['deca'] ) ) { $ubaci( $c['deca'], $item_id ); }
	}
};
$ubaci( $struktura );

echo "upisano stavki: $order\n";

$loc = get_theme_mod( 'nav_menu_locations', array() );
$loc = is_array( $loc ) ? $loc : array();
echo 'stara lokacija main-menu: ' . ( isset( $loc['main-menu'] ) ? $loc['main-menu'] : '—' ) . "\n";
$loc['main-menu'] = $menu_id;
set_theme_mod( 'nav_menu_locations', $loc );
echo "✅ main-menu → term $menu_id\n";
