<?php
/**
 * job-slug-swap-parket-2026-08-13.php — čist slug za članak o postavljanju
 * preko starog parketa/pločica (M odluka 2026-08-13).
 *
 * STANJE PRE:
 *   6588  `…-plocica-2`  publish, index,  8.041 znakova — prepis iz 09/2025
 *                        (LVT + Ecotile + R-Tek + FAQ + galerija) = sadržaj koji želimo
 *   16613 `…-plocica`    publish, noindex, 4.940 znakova — original iz 07/2022,
 *                        nema nijedan pasus koji ne postoji bolje u 6588
 *
 * STANJE POSLE: sadržaj 6588 živi na čistom URL-u, `-2` više ne postoji na buildu.
 *
 * 🔴 ZAŠTO $wpdb->update, a NE wp_update_post: `wp_unique_post_slug()` bi na 6588
 * zatekao slug koji (do koraka 1) drži drugi post i tiho vratio `-2` nazad —
 * tačno ono što uklanjamo. Direktan upis zaobilazi tu logiku u celosti.
 *
 * 🔴 REDOSLED JE OBAVEZAN: prvo 16613 pusti slug, pa ga tek onda 6588 uzme.
 * Obrnuto bi dalo dva posta sa istim `post_name`.
 *
 * 301 se ovde NE piše — draft se generiše sa `htaccess-301-generate.php` posle
 * izmene reda 18 u `redirect-mapa-FINAL.csv` (smer se okreće: `-2` → čist).
 *
 * UPOTREBA:
 *   C:\xampp\php\php.exe job-slug-swap-parket-2026-08-13.php          (probni prolaz)
 *   C:\xampp\php\php.exe job-slug-swap-parket-2026-08-13.php --write  (upis)
 *
 * Backup pre upisa: antasline_local_2026-08-13_pre-slug-swap-parket.sql
 */

require_once 'C:/xampp/htdocs/antasline/wp-load.php';

global $wpdb;
$WRITE = in_array('--write', $argv, true);

$CIST   = 'sta-postaviti-preko-starog-parketa-ili-plocica';
$STARI  = 'sta-postaviti-preko-starog-parketa-ili-plocica-2';
$ARHIVA = 'sta-postaviti-preko-starog-parketa-ili-plocica-original-2022';

function stanje($id) {
    global $wpdb;
    return $wpdb->get_row($wpdb->prepare(
        "SELECT ID, post_name, post_status, LENGTH(post_content) AS len FROM {$wpdb->posts} WHERE ID=%d", $id
    ));
}

printf("PRE:\n");
foreach ([6588, 16613] as $id) {
    $s = stanje($id);
    if (!$s) { printf("  🔴 post %d ne postoji — prekid\n", $id); exit(1); }
    printf("  %-6d %-52s %-8s %d znakova\n", $s->ID, $s->post_name, $s->post_status, $s->len);
}

// Provera preduslova — ako je skripta već jednom prošla, ne raditi ništa.
$a = stanje(6588); $b = stanje(16613);
if ($a->post_name !== $STARI || $b->post_name !== $CIST) {
    printf("\n⚠️  Slugovi nisu u očekivanom polaznom stanju — skripta je verovatno već izvršena. Prekid bez izmena.\n");
    exit(0);
}

if (!$WRITE) {
    printf("\n[probni prolaz] bi se uradilo:\n");
    printf("  1) 16613: post_name → %s, post_status → draft\n", $ARHIVA);
    printf("  2) 6588:  post_name → %s\n", $CIST);
    printf("\nPokreni sa --write za upis.\n");
    exit(0);
}

// 1) Stari članak oslobađa slug i gasi se.
$wpdb->update($wpdb->posts, ['post_name' => $ARHIVA, 'post_status' => 'draft'], ['ID' => 16613]);
clean_post_cache(16613);
printf("\n✅ 16613 → draft, slug %s\n", $ARHIVA);

// 2) Dobar članak uzima oslobođen čist slug.
$wpdb->update($wpdb->posts, ['post_name' => $CIST], ['ID' => 6588]);
clean_post_cache(6588);
printf("✅ 6588 → slug %s\n", $CIST);

printf("\nPOSLE:\n");
foreach ([6588, 16613] as $id) {
    $s = stanje($id);
    printf("  %-6d %-52s %-8s %d znakova\n", $s->ID, $s->post_name, $s->post_status, $s->len);
}

printf("\nSledeći koraci (ručno):\n");
printf("  · redirect-mapa-FINAL.csv red 18 → okrenuti smer na /%s/ → /%s/\n", $STARI, $CIST);
printf("  · php migracija/alati/htaccess-301-generate.php\n");
printf("  · php migracija/alati/redirect-verify.php\n");
