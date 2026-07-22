<?php
// Live GEO fix na post 2542 (epoksidni-podovi-ili-ecotile-podovi)
// Izvršiti preko: wp eval-file migracija/2026-07-22-live-2542-geo-fix.php
// Koristi $wpdb->update() direktno (ne wp_insert_post/wp_update_post) da izbegne
// kses stripovanje <script> JSON-LD taga van ulogovanog korisnika (poznat F7.15 gotcha).

global $wpdb;

$post_id = 2542;
$content = $wpdb->get_var( $wpdb->prepare( "SELECT post_content FROM {$wpdb->posts} WHERE ID = %d", $post_id ) );

if ( ! $content ) {
    fwrite( STDERR, "GRESKA: post_content za ID $post_id nije nadjen.\n" );
    exit( 1 );
}

// 1) Uvodni pasus — dodaje recenicu sa doslovnom frazom pre <!--more-->
$old_intro = 'Pruža određene prednosti sa kojima se epoksidni podovi jednostavno ne mogu takmičiti.<!--more-->';
$new_intro = 'Pruža određene prednosti sa kojima se epoksidni podovi jednostavno ne mogu takmičiti. Za proizvodne hale i pogone, Ecotile interlocking (klik-sistem) PVC ploče su najčešća <strong>alternativa epoksidnom podu za proizvodnu halu</strong> — montiraju se fazno, bez zastoja proizvodnje.<!--more-->';

if ( strpos( $content, $old_intro ) === false ) {
    fwrite( STDERR, "GRESKA: uvodni anchor nije nadjen, stajem.\n" );
    exit( 1 );
}
$content = str_replace( $old_intro, $new_intro, $content );

// 2) Novo FAQ pitanje — vidljiv tekst, ubaceno posle poslednjeg postojeceg FAQ pitanja
$old_faq_anchor = '<p>Da — „epoksi pod", „epoksidni pod" i „epoxy pod" su nazivi za isti premaz na bazi epoksidne smole, najčešće debljine 2–4 mm u industriji. Poređenje sa Ecotile PVC pločama iz ove tabele važi za sve njih.</p>';
$new_faq_block = $old_faq_anchor . "\n\n" . '<h3>Koja je alternativa epoksidnom podu za proizvodnu halu?</h3>
<p>Glavna alternativa epoksidnom podu za proizvodnu halu su modularne PVC ploče sistema interlocking (klik-sistem), kao što je Ecotile — postavljaju se bez lepka i bez brušenja betona, direktno preko postojeće podloge, oko mašina i regala, dok pogon ostaje u funkciji.</p>';

if ( strpos( $content, $old_faq_anchor ) === false ) {
    fwrite( STDERR, "GRESKA: FAQ anchor nije nadjen, stajem.\n" );
    exit( 1 );
}
$content = str_replace( $old_faq_anchor, $new_faq_block, $content );

// 3) Isto pitanje u FAQPage JSON-LD (posle poslednjeg postojeceg entry-ja)
$old_ld_anchor = '{"@type":"Question","name":"Da li je isto epoksi, epoksid i epoxy pod?","acceptedAnswer":{"@type":"Answer","text":"Da — epoksi pod, epoksidni pod i epoxy pod su nazivi za isti premaz na bazi epoksidne smole, najčešće debljine 2–4 mm u industriji."}}';
$new_ld_entry = $old_ld_anchor . ',
    {"@type":"Question","name":"Koja je alternativa epoksidnom podu za proizvodnu halu?","acceptedAnswer":{"@type":"Answer","text":"Glavna alternativa epoksidnom podu za proizvodnu halu su modularne PVC ploče sistema interlocking (klik-sistem), kao što je Ecotile — postavljaju se bez lepka i bez brušenja betona, direktno preko postojeće podloge, oko mašina i regala, dok pogon ostaje u funkciji."}}';

if ( strpos( $content, $old_ld_anchor ) === false ) {
    fwrite( STDERR, "GRESKA: JSON-LD anchor nije nadjen, stajem.\n" );
    exit( 1 );
}
$content = str_replace( $old_ld_anchor, $new_ld_entry, $content );

// Validacija JSON-LD pre upisa
if ( ! preg_match( '#<script type="application/ld\+json">(.*?)</script>#s', $content, $m ) ) {
    fwrite( STDERR, "GRESKA: JSON-LD script blok nije nadjen posle izmene.\n" );
    exit( 1 );
}
$decoded = json_decode( trim( $m[1] ), true );
if ( json_last_error() !== JSON_ERROR_NONE ) {
    fwrite( STDERR, "GRESKA: JSON-LD nije validan posle izmene: " . json_last_error_msg() . "\n" );
    exit( 1 );
}
$q_count = count( $decoded['mainEntity'] ?? [] );
fwrite( STDOUT, "JSON-LD validan, $q_count pitanja.\n" );

$updated = $wpdb->update(
    $wpdb->posts,
    [ 'post_content' => $content ],
    [ 'ID' => $post_id ]
);

clean_post_cache( $post_id );

fwrite( STDOUT, "Update rezultat: " . var_export( $updated, true ) . "\n" );
fwrite( STDOUT, "Nova duzina sadrzaja: " . strlen( $content ) . " (bila " . strlen( $wpdb->get_var( $wpdb->prepare( "SELECT post_content FROM {$wpdb->posts} WHERE ID = %d", $post_id ) ) ) . ")\n" );
