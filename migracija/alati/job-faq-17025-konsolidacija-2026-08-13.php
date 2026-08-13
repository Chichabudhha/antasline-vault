<?php
/**
 * job-faq-17025-konsolidacija-2026-08-13.php — treća FAQ stranica klastera ide u hub.
 *
 * Nastavak `job-faq-konsolidacija-2026-08-13.php` (2622 + 3274). M odluka isti dan:
 * i `/industrijski-podovi-najcesca-pitanja/` (17025) ide u hub, 301 na `/industrijski-podovi/`.
 *
 * GSC 12 meseci: 17025 = **4 prikaza / 0 klikova**, dok hub drži „industrijski podovi"
 * na poz. 6,7 (16.417 prikaza / 410 klikova).
 *
 * 🔴 KRITIČNO — istorijsko pravilo sa 615 pogodaka: `/home/industrijski-podovi-najcesca-pitanja/`
 * je do sada ciljalo 17025. Kad 17025 postane draft, to pravilo vodi na 404 ako se ne
 * pretoči na hub. Radi se u `redirect-mapa-HISTORIJSKI-65-FLAT.csv` (ručno, posle ove skripte).
 *
 * ŠTA RADI:
 *   1) 16567 — 4 pitanja koja hub NEMA, prepisana sa 17025: samostalna montaža ·
 *      spoljašnja upotreba (odgovor je NE — važan negativan kvalifikator koji odbija
 *      pogrešne upite) · postavljanje preko farbanog betona/pločica/tepiha/vinila ·
 *      kada je lepak potreban (uklj. preporučeno lepilo Uzin MK92S)
 *      NE prenosi se: viljuškari (hub ima + tabelu debljina), „odmah nakon instalacije"
 *      (hub: „Da li montaža zaustavlja proizvodnju?"), priprema podloge (hub: „Mora li
 *      stari beton da se sanira?")
 *   2) 16567 — FAQPage JSON-LD se PONOVO GRADI (stari blok se briše) da obuhvati svih 15
 *      pitanja; ponovo parsiranjem vidljivog teksta, ne ručnim prepisom
 *   3) 17025 → draft
 *   4) Meni stavka 17390 („Najčešća pitanja") → brisanje. Hub je u meniju već 2× (16706,
 *      17371), pa bi prevezivanje napravilo treći duplikat.
 *
 * UPOTREBA:
 *   C:\xampp\php\php.exe job-faq-17025-konsolidacija-2026-08-13.php          (probni prolaz)
 *   C:\xampp\php\php.exe job-faq-17025-konsolidacija-2026-08-13.php --write  (upis)
 *
 * Backup pre upisa: antasline_local_2026-08-13_pre-faq-17025.sql
 */

require_once 'C:/xampp/htdocs/antasline/wp-load.php';

global $wpdb;
$WRITE = in_array('--write', $argv, true);
$greske = 0;

function sadrzaj($id) {
    global $wpdb;
    return $wpdb->get_var($wpdb->prepare("SELECT post_content FROM {$wpdb->posts} WHERE ID=%d", $id));
}
function upisi($id, $novo, $WRITE) {
    global $wpdb;
    if (!$WRITE) { printf("  [probno] %d bi dobio %d bajtova\n", $id, strlen($novo)); return; }
    $wpdb->update($wpdb->posts, ['post_content' => $novo], ['ID' => $id]);
    clean_post_cache($id);
    printf("  ✅ %d upisan (%d bajtova)\n", $id, strlen($novo));
}

$NOVA = <<<'HTML'

<h3>Mogu li sam da postavim pod?</h3>
<p>Montaža je jednostavna — potrebni su ubodna testera ili cirkular i gumeni čekić, a ploče se spajaju klik-sistemom. Uz svaku isporuku ide detaljno uputstvo za instalaciju, a na zahtev radimo i montažu.</p>
<h3>Mogu li se ploče postaviti napolju?</h3>
<p>Ne — proizvođač ne preporučuje ove podloge za spoljašnje prostore. Za terase, dvorišta i parkinge koristimo druge sisteme iz ponude.</p>
<h3>Mogu li ploče preko farbanog betona, pločica, tepiha ili vinila?</h3>
<p>Da — ploče se postavljaju preko svih tvrdih podloga bez problema, uključujući farban beton, keramičke pločice i vinil. Moguće je i preko tepiha, ali preporučujemo da se tepih prethodno ukloni.</p>
<h3>Kada je potreban lepak?</h3>
<p>U većini slučajeva nije — montaža bez lepka je jedna od prednosti sistema. Lepljenje je potrebno samo kod tačkastog opterećenja (npr. električni viljuškar na tri točka) ili kod ploča izloženih izolovanom izvoru toplote (katalizator, peć) ili direktnoj sunčevoj svetlosti. Preporučeno lepilo: Uzin MK92S.</p>
HTML;

// ─────────────────────────────────────────────────────────────────────────────
printf("=== 1) 16567 — 4 pitanja sa 17025\n");
$c = sadrzaj(16567);
if ($c === null) { printf("  🔴 16567 ne postoji\n"); exit(1); }

if (strpos($c, 'Mogu li sam da postavim pod') !== false) {
    printf("  ⚠️  već upisano — preskačem\n");
} else {
    // Sidro: kraj FAQ bloka. Schema blok je unutar istog vc_column_text, pa se
    // umeće PRE njega — inače bi nova pitanja završila posle </script>.
    $pos_naslov = strpos($c, 'Najčešća pitanja o industrijskim podovima');
    $pos_script = strpos($c, '<script type="application/ld+json">', $pos_naslov);
    $pos_umetni = ($pos_script !== false) ? $pos_script : strpos($c, '[/vc_column_text]', $pos_naslov);
    if ($pos_umetni === false) { printf("  🔴 mesto umetanja nije nađeno\n"); $greske++; }
    else {
        $c = substr($c, 0, $pos_umetni) . $NOVA . "\n" . substr($c, $pos_umetni);
        upisi(16567, $c, $WRITE);
    }
}

// ─────────────────────────────────────────────────────────────────────────────
printf("\n=== 2) 16567 — FAQPage JSON-LD se ponovo gradi\n");
// Stari blok se briše u celosti, pa se schema regeneriše nad SVIM pitanjima.
$c2 = preg_replace('~\s*<script type="application/ld\+json">.*?</script>\s*~s', "\n", $c, 1, $obrisano);
printf("  obrisan stari schema blok: %s\n", $obrisano ? 'da' : 'ne (nije ga ni bilo)');

$pos_naslov = strpos($c2, 'Najčešća pitanja o industrijskim podovima');
$pos_kraj   = strpos($c2, '[/vc_column_text]', $pos_naslov);
$blok = substr($c2, $pos_naslov, $pos_kraj - $pos_naslov);

preg_match_all('~<h3[^>]*>(.*?)</h3>\s*<p>(.*?)</p>~s', $blok, $m, PREG_SET_ORDER);
printf("  parsirano %d parova pitanje/odgovor\n", count($m));

if (count($m) < 15) {
    printf("  🔴 očekivano 15 (11 + 4 nova) — parsiranje sumnjivo, schema se NE upisuje\n");
    $greske++;
} else {
    $items = [];
    foreach ($m as $par) {
        $q = trim(html_entity_decode(strip_tags($par[1]), ENT_QUOTES, 'UTF-8'));
        $a = trim(html_entity_decode(strip_tags($par[2]), ENT_QUOTES, 'UTF-8'));
        if ($q === '' || $a === '') continue;
        $items[] = ['@type'=>'Question','name'=>$q,'acceptedAnswer'=>['@type'=>'Answer','text'=>$a]];
        printf("    · %s\n", mb_substr($q, 0, 68));
    }
    $json = json_encode(['@context'=>'https://schema.org','@type'=>'FAQPage','mainEntity'=>$items],
                       JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    $pos_kraj2 = strpos($c2, '[/vc_column_text]', strpos($c2, 'Najčešća pitanja o industrijskim podovima'));
    $c2 = substr($c2, 0, $pos_kraj2) . "\n<script type=\"application/ld+json\">\n" . $json . "\n</script>\n" . substr($c2, $pos_kraj2);
    upisi(16567, $c2, $WRITE);
}

// ─────────────────────────────────────────────────────────────────────────────
printf("\n=== 3) 17025 → draft\n");
$st = $wpdb->get_var($wpdb->prepare("SELECT post_status FROM {$wpdb->posts} WHERE ID=%d", 17025));
if ($st === 'draft') { printf("  ⚠️  već draft\n"); }
elseif (!$WRITE)     { printf("  [probno] 17025 (%s) → draft\n", $st); }
else {
    $wpdb->update($wpdb->posts, ['post_status' => 'draft'], ['ID' => 17025]);
    clean_post_cache(17025);
    printf("  ✅ 17025 → draft\n");
}

// ─────────────────────────────────────────────────────────────────────────────
printf("\n=== 4) Meni stavka 17390 → brisanje\n");
$deca = $wpdb->get_col($wpdb->prepare(
    "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_menu_item_menu_item_parent' AND meta_value=%s", '17390'));
if ($deca) {
    printf("  🔴 stavka ima %d podstavki (%s) — NE brišem, treba ručna odluka\n", count($deca), implode(',', $deca));
    $greske++;
} elseif (!$WRITE) {
    printf("  [probno] 17390 („Najčešća pitanja\") bi bila obrisana; hub je u meniju već 2× (16706, 17371)\n");
} else {
    wp_delete_post(17390, true);
    printf("  ✅ 17390 obrisana iz menija\n");
}

// ─────────────────────────────────────────────────────────────────────────────
printf("\n=== 5) Preostale veze ka 17025 u sadržaju\n");
$veze = $wpdb->get_col("SELECT ID FROM {$wpdb->posts} WHERE post_status='publish' AND post_content LIKE '%industrijski-podovi-najcesca-pitanja%'");
if ($veze) { printf("  🔴 još linkuju: %s — prevezati ručno\n", implode(', ', $veze)); $greske++; }
else       { printf("  ✅ nema dolaznih veza\n"); }

if ($WRITE && class_exists('\RankMath\Sitemap\Cache')) {
    \RankMath\Sitemap\Cache::invalidate_storage();
    printf("\n✅ Rank Math sitemap keš invalidiran\n");
}

printf("\n%s\n", $greske ? "🔴 GREŠAKA: $greske" : "✅ bez grešaka");
printf("\nSledeći koraci (ručno):\n");
printf("  · redirect-mapa-FINAL.csv — nov red /industrijski-podovi-najcesca-pitanja/ → /industrijski-podovi/\n");
printf("  · 🔴 redirect-mapa-HISTORIJSKI-65-FLAT.csv — /home/industrijski-podovi-najcesca-pitanja/ (615) cilj na hub\n");
printf("  · php migracija/alati/htaccess-301-generate.php  &&  redirect-verify.php\n");
