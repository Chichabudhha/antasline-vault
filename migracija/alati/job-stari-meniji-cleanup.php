<?php
/**
 * Brisanje starih/mrtvih menija (M odluka 2026-07-30, W7 F3 bloker stavka 1).
 *
 * term 28 "Glavni izbornik" (66 stavki) je mrtav od kad je term 390 postao
 * aktivan `main-menu` — potvrđeno: `theme_mods_woodmart-child.nav_menu_locations`
 * pokazuje SAMO main-menu=>390, `widget_nav_menu` je prazan, 0 postmeta
 * referenci na bilo koji od ovih term_id-eva. term 67 "O firmi" (stari
 * aktivan, 39 stavki) NAMERNO SE NE DIRA — ostaje rollback dok M ne kaže
 * drugačije (nije u ovom spisku). term 280 "Utility meni" (header builder,
 * druga namena) takođe se ne dira.
 *
 * 10 praznih Porto menija: Company(43), Main Menu(44), Services(45),
 * drugi meni(66), Bergo(258), Ecotile(259), Galerija(260), Gornji menu(261),
 * Menu 1(262), Social Networks(263) — nijedan nije u nav_menu_locations niti
 * u ijednom widgetu.
 *
 * `wp_delete_nav_menu()` briše term_taxonomy red + sve nav_menu_item postove
 * vezane za taj meni (WP native, ne ostavlja duhove).
 *
 * php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-stari-meniji-cleanup.php          # proba
 * php wp-cli.phar --path="C:\xampp\htdocs\antasline" eval-file job-stari-meniji-cleanup.php apply    # upis
 */

$apply = ( ( $args[0] ?? '' ) === 'apply' );

$ids = array( 28, 43, 44, 45, 66, 258, 259, 260, 261, 262, 263 );

foreach ( $ids as $id ) {
	$term = get_term( $id, 'nav_menu' );
	if ( ! $term || is_wp_error( $term ) ) {
		echo "⚠️ term {$id} ne postoji ili nije nav_menu, preskačem\n";
		continue;
	}
	$count = wp_count_posts( 'nav_menu_item' ); // not per-menu, just sanity
	$items = wp_get_nav_menu_items( $id );
	$item_count = $items ? count( $items ) : 0;
	echo "{$id} \"{$term->name}\" ({$item_count} stavki)";
	if ( $apply ) {
		$result = wp_delete_nav_menu( $id );
		echo $result && ! is_wp_error( $result ) ? " → obrisano\n" : " → GREŠKA\n";
	} else {
		echo " → (proba, nije obrisano)\n";
	}
}

echo $apply ? "\nGotovo.\n" : "\nProba završena, ništa upisano (pokreni sa 'apply').\n";
