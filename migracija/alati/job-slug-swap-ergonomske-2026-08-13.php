<?php
/**
 * job-slug-swap-ergonomske-2026-08-13.php — čist slug za stranicu ergonomskih
 * podloga (stavka A iz [[seo/2026-08-13-kanibalizacija-konsolidacija-plan]] §1).
 *
 * STANJE PRE:
 *   16672 page       `ergonomske-podloge-2`  publish — „Ergonomski podovi", 7.329 zn.
 *   12489 attachment `ergonomske-podloge`    inherit — DRŽI čist slug i time tera `-2`
 *
 * 🔴 OVDE JE `-2` DRUGE PRIRODE nego kod „preko starog parketa": tamo je bio
 * namerno drugi članak, ovde je WP-ov automatski sufiks jer je slug zauzeo PRILOG.
 * Nema konsolidacije sadržaja — samo se oslobađa slug.
 *
 * 🟢 Preimenovanje priloga ne dira putanju fajla: `post_name` je slug attachment
 * STRANICE, ne ime fajla. Slike nastavljaju da rade sa istim URL-om.
 *
 * 🔴 `$wpdb->update`, ne `wp_update_post` — `wp_unique_post_slug()` za hijerarhijske
 * tipove proverava i `attachment`, pa bi vratio `-2` nazad. Redosled obavezan.
 *
 * GSC (provereno pre izvršenja, `gsc_page_queries.py`):
 *   90d  = 1 prikaz / 0 klikova · 12 meseci = 123 prikaza / 4 klika
 *   drži „ergonomske podloge" poz. 3,8 i „podloga za stajanje" poz. 6,5
 *   (podatak „110 klikova" iz `parity-inventar.csv` nije potvrđen ni na jednom prozoru)
 *
 * 301 se ovde NE piše — draft se generiše `htaccess-301-generate.php`-om posle
 * izmene mapa. Dva reda: novi `-2` → čist, i istorijski `/ergonomski-podovi/`
 * (160 pogodaka) se pretače sa `-2` na čist cilj.
 *
 * UPOTREBA:
 *   C:\xampp\php\php.exe job-slug-swap-ergonomske-2026-08-13.php          (probni prolaz)
 *   C:\xampp\php\php.exe job-slug-swap-ergonomske-2026-08-13.php --write  (upis)
 *
 * Backup pre upisa: antasline_local_2026-08-13_pre-slug-swap-ergonomske.sql
 */

require_once 'C:/xampp/htdocs/antasline/wp-load.php';

global $wpdb;
$WRITE = in_array('--write', $argv, true);

$CIST      = 'ergonomske-podloge';
$STARI     = 'ergonomske-podloge-2';
$PRILOG_NOV = 'ergonomske-podloge-foto';

function stanje($id) {
    global $wpdb;
    return $wpdb->get_row($wpdb->prepare(
        "SELECT ID, post_name, post_status, post_type FROM {$wpdb->posts} WHERE ID=%d", $id
    ));
}

printf("PRE:\n");
foreach ([16672, 12489] as $id) {
    $s = stanje($id);
    if (!$s) { printf("  🔴 post %d ne postoji — prekid\n", $id); exit(1); }
    printf("  %-6d %-12s %-10s %s\n", $s->ID, $s->post_type, $s->post_status, $s->post_name);
}

$stranica = stanje(16672);
$prilog   = stanje(12489);
if ($stranica->post_name !== $STARI || $prilog->post_name !== $CIST) {
    printf("\n⚠️  Nije polazno stanje — skripta je verovatno već izvršena. Prekid bez izmena.\n");
    exit(0);
}

if (!$WRITE) {
    printf("\n[probni prolaz] bi se uradilo:\n");
    printf("  1) 12489 (prilog): post_name → %s\n", $PRILOG_NOV);
    printf("  2) 16672 (stranica): post_name → %s\n", $CIST);
    printf("  3) 16567 `/industrijski-podovi/`: link /%s/ → /%s/\n", $STARI, $CIST);
    printf("\nPokreni sa --write za upis.\n");
    exit(0);
}

// 1) Prilog oslobađa slug.
$wpdb->update($wpdb->posts, ['post_name' => $PRILOG_NOV], ['ID' => 12489]);
clean_post_cache(12489);
printf("\n✅ 12489 (prilog) → %s\n", $PRILOG_NOV);

// 2) Stranica uzima čist slug.
$wpdb->update($wpdb->posts, ['post_name' => $CIST], ['ID' => 16672]);
clean_post_cache(16672);
printf("✅ 16672 (stranica) → %s\n", $CIST);

// 3) Jedini dolazni interni link u sadržaju (na /industrijski-podovi/).
$c = $wpdb->get_var($wpdb->prepare("SELECT post_content FROM {$wpdb->posts} WHERE ID=%d", 16567));
$novo = str_replace('/' . $STARI . '/', '/' . $CIST . '/', $c);
if ($novo !== $c) {
    $wpdb->update($wpdb->posts, ['post_content' => $novo], ['ID' => 16567]);
    clean_post_cache(16567);
    printf("✅ 16567 — link prevezan na čist slug\n");
} else {
    printf("⚠️  16567 — link nije nađen (proveriti ručno)\n");
}

printf("\nPOSLE:\n");
foreach ([16672, 12489] as $id) {
    $s = stanje($id);
    printf("  %-6d %-12s %-10s %s\n", $s->ID, $s->post_type, $s->post_status, $s->post_name);
}

// 4) Rank Math sitemap keš ne zna za direktan SQL upis (lekcija 2026-08-13).
if (class_exists('\RankMath\Sitemap\Cache')) {
    \RankMath\Sitemap\Cache::invalidate_storage();
    printf("\n✅ Rank Math sitemap keš invalidiran\n");
}

printf("\nSledeći koraci (ručno):\n");
printf("  · redirect-mapa-FINAL.csv — dodati red /%s/ → /%s/\n", $STARI, $CIST);
printf("  · redirect-mapa-HISTORIJSKI-65-FLAT.csv — /ergonomski-podovi/ cilj na /%s/\n", $CIST);
printf("  · php migracija/alati/htaccess-301-generate.php  &&  redirect-verify.php\n");
