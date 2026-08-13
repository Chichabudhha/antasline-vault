<?php
/**
 * job-faq-konsolidacija-2026-08-13.php — FAQ klaster „izbor industrijskog poda"
 * konsoliduje se u hub `/industrijski-podovi/` (M odluka 2026-08-13).
 *
 * POLAZNO STANJE (GSC provereno pre izvršenja, `gsc_page_queries.py`):
 *   2622  /izbor-industrijskog-poda-tri-najcesca-pitanja/    publish  90d 94/0   · 12mes 128 prikaza / 0 klikova
 *   3274  /izbor-industrijskog-poda-tri-najcesca-pitanja-2/  draft    90d 50/0   · 12mes  98 prikaza / 0 klikova
 *   17025 /industrijski-podovi-najcesca-pitanja/             publish  90d  0/0   · 12mes   4 prikaza / 0 klikova
 *   16567 /industrijski-podovi/                              publish  12mes 16.417 prikaza / 410 klikova, „industrijski podovi" poz. 6,7
 *
 * 🔴 Sve tri FAQ stranice gađaju upit „industrijski podovi" sa poz. 24–80, dok ga
 * hub drži na 6,7 — tri slaba izvora cepaju signal protiv sopstvenog huba, uz
 * NULA klikova u 12 meseci. Konsolidacija ne gubi ništa.
 *
 * ⚠️ Brojka „311 klikova / poz. 6,9 / CTR 4,92%" iz `redirect-mapa-FINAL.csv` reda 17
 * NIJE potvrđena svežim podacima (isti razred greške kao `gsc_klikovi` kolona u
 * `parity-inventar.csv`, v. lekcije 2026-08-13).
 *
 * ŠTA RADI:
 *   1) 16567 — FAQ sekcija dobija 4 pitanja koja hub NEMA, izvedena iz oba članka:
 *      okvir odluke · svež beton u novogradnji · priprema u odnosu na premaze ·
 *      otkup starog poda. (Ecotile podela po debljini i 7 postojećih pitanja se NE
 *      dupliraju — hub ih već ima.)
 *   2) 16567 — FAQPage JSON-LD, koji hub do sada NIJE imao (provereno: 0 FAQPage u
 *      renderovanom izlazu). Schema se gradi PARSIRANJEM vidljivog teksta, ne ručnim
 *      prepisom — inače se vremenom raziđu, što Google tretira kao neusklađenost.
 *   3) 2622 → draft (3274 je već draft od 2026-07-27)
 *   4) 17025 — jedina stranica koja linkuje ka oba članka; linkovi se prevezuju na hub
 *
 * 🔴 `<script>` u `post_content` ide ISKLJUČIVO kroz $wpdb->update — `wp_insert_post`/
 * `wp_update_post` iz CLI konteksta kses-uje script tagove i ostavi goli JSON na
 * stranici (gotcha CB2/F7.15). Iz istog razloga se sadržaj i čita direktno, ne preko
 * `get_post_field()` (wptexturize kvari WPBakery `css=""` atribute).
 *
 * 301 se ovde NE piše — mape se menjaju ručno pa `htaccess-301-generate.php`.
 *
 * UPOTREBA:
 *   C:\xampp\php\php.exe job-faq-konsolidacija-2026-08-13.php          (probni prolaz)
 *   C:\xampp\php\php.exe job-faq-konsolidacija-2026-08-13.php --write  (upis)
 *
 * Backup pre upisa: antasline_local_2026-08-13_pre-faq-konsolidacija.sql
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

// ─────────────────────────────────────────────────────────────────────────────
// 1) Nova pitanja na hub-u
// ─────────────────────────────────────────────────────────────────────────────
$NOVA = <<<'HTML'

<h3>Kako da izaberem industrijski pod — po čemu da se vodim?</h3>
<p>Tri pitanja odlučuju. Prvo, šta se u prostoru radi: viljuškarski saobraćaj, ulja i hemikalije, klizanje, ili ESD zahtevi u elektronici i štampariji. Drugo, koliko brzo pod mora da proradi. Treće, koliko košta kroz ceo vek — ne samo pri ugradnji. Pošaljite namenu i kvadraturu i predlog modela stiže isti dan.</p>
<h3>Može li se pod postaviti na svež beton u novogradnji?</h3>
<p>Da. Betonu treba i do godinu dana da potpuno sazri, a Ecotile ploče se polažu bez lepka i hidroizolacije, pa beton nastavlja da diše i steže se ispod njih. Pogon ne mora da čeka sazrevanje da bi proradio.</p>
<h3>Koliko traje priprema u odnosu na premazne podove?</h3>
<p>Kod epoksidnih premaza i boja za podove ulja, vlaga i hemikalije moraju se neutralisati iz podloge, uz glodanje ili brušenje — postupak koji stvara puno prašine i ume da traje nedeljama. Ploče se polažu direktno na neujednačen beton, bez te pripreme.</p>
<h3>Šta biva sa starim podom kada ga menjam?</h3>
<p>Ecotile otkupljuje stari pod pri zameni i uračunava ga u cenu novog. Ploče su reciklabilne, pa ne završavaju kao otpad.</p>
HTML;

printf("=== 1) 16567 — nova FAQ pitanja\n");
$c = sadrzaj(16567);
if ($c === null) { printf("  🔴 16567 ne postoji\n"); exit(1); }

if (strpos($c, 'Kako da izaberem industrijski pod') !== false) {
    printf("  ⚠️  već upisano — preskačem\n");
} else {
    // Sidro: kraj FAQ bloka = prvi [/vc_column_text] posle FAQ naslova.
    $pos_naslov = strpos($c, 'Najčešća pitanja o industrijskim podovima');
    if ($pos_naslov === false) { printf("  🔴 FAQ naslov nije nađen\n"); $greske++; }
    else {
        $pos_kraj = strpos($c, '[/vc_column_text]', $pos_naslov);
        if ($pos_kraj === false) { printf("  🔴 kraj FAQ bloka nije nađen\n"); $greske++; }
        else {
            $novo = substr($c, 0, $pos_kraj) . $NOVA . "\n" . substr($c, $pos_kraj);
            upisi(16567, $novo, $WRITE);
            $c = $novo;
        }
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// 2) FAQPage JSON-LD — gradi se iz VIDLJIVOG teksta
// ─────────────────────────────────────────────────────────────────────────────
printf("\n=== 2) 16567 — FAQPage JSON-LD\n");
if (strpos($c, 'FAQPage') !== false) {
    printf("  ⚠️  schema već postoji — preskačem\n");
} else {
    $pos_naslov = strpos($c, 'Najčešća pitanja o industrijskim podovima');
    $pos_kraj   = strpos($c, '[/vc_column_text]', $pos_naslov);
    $blok = substr($c, $pos_naslov, $pos_kraj - $pos_naslov);

    preg_match_all('~<h3[^>]*>(.*?)</h3>\s*<p>(.*?)</p>~s', $blok, $m, PREG_SET_ORDER);
    printf("  parsirano %d parova pitanje/odgovor\n", count($m));

    if (count($m) < 8) {
        printf("  🔴 očekivano najmanje 8 (7 starih + 4 nova) — parsiranje sumnjivo, preskačem\n");
        $greske++;
    } else {
        $items = [];
        foreach ($m as $par) {
            $q = trim(html_entity_decode(strip_tags($par[1]), ENT_QUOTES, 'UTF-8'));
            $a = trim(html_entity_decode(strip_tags($par[2]), ENT_QUOTES, 'UTF-8'));
            if ($q === '' || $a === '') continue;
            $items[] = [
                '@type' => 'Question',
                'name'  => $q,
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $a],
            ];
            printf("    · %s\n", mb_substr($q, 0, 70));
        }
        $schema = [
            '@context'   => 'https://schema.org',
            '@type'      => 'FAQPage',
            'mainEntity' => $items,
        ];
        $json = json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        $blok_script = "\n<script type=\"application/ld+json\">\n" . $json . "\n</script>\n";

        $pos_kraj2 = strpos($c, '[/vc_column_text]', strpos($c, 'Najčešća pitanja o industrijskim podovima'));
        $novo = substr($c, 0, $pos_kraj2) . $blok_script . substr($c, $pos_kraj2);
        upisi(16567, $novo, $WRITE);
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// 3) 2622 → draft
// ─────────────────────────────────────────────────────────────────────────────
printf("\n=== 3) 2622 → draft\n");
$st = $wpdb->get_var($wpdb->prepare("SELECT post_status FROM {$wpdb->posts} WHERE ID=%d", 2622));
if ($st === 'draft') {
    printf("  ⚠️  već draft\n");
} elseif (!$WRITE) {
    printf("  [probno] 2622 (%s) → draft\n", $st);
} else {
    $wpdb->update($wpdb->posts, ['post_status' => 'draft'], ['ID' => 2622]);
    clean_post_cache(2622);
    printf("  ✅ 2622 → draft\n");
}

// ─────────────────────────────────────────────────────────────────────────────
// 4) 17025 — jedini dolazni linkovi ka oba članka
// ─────────────────────────────────────────────────────────────────────────────
printf("\n=== 4) 17025 — prevezivanje linkova na hub\n");
$c17 = sadrzaj(17025);
$pre = substr_count($c17, 'izbor-industrijskog-poda-tri-najcesca-pitanja');
if ($pre === 0) {
    printf("  ⚠️  nema veza — preskačem\n");
} else {
    printf("  nađeno %d veza\n", $pre);
    $novo17 = preg_replace(
        '~https?://[^"\']*?/izbor-industrijskog-poda-tri-najcesca-pitanja(-2)?/~',
        'http://localhost/antasline/industrijski-podovi/',
        $c17
    );
    upisi(17025, $novo17, $WRITE);
    if ($WRITE) {
        $posle = substr_count(sadrzaj(17025), 'izbor-industrijskog-poda-tri-najcesca-pitanja');
        printf("  preostalo veza: %d\n", $posle);
        if ($posle > 0) { printf("  🔴 nisu sve prevezane\n"); $greske++; }
    }
}

// ─────────────────────────────────────────────────────────────────────────────
if ($WRITE && class_exists('\RankMath\Sitemap\Cache')) {
    \RankMath\Sitemap\Cache::invalidate_storage();
    printf("\n✅ Rank Math sitemap keš invalidiran\n");
}

printf("\n%s\n", $greske ? "🔴 GREŠAKA: $greske" : "✅ bez grešaka");
printf("\nSledeći koraci (ručno):\n");
printf("  · redirect-mapa-FINAL.csv — red 17 cilj na /industrijski-podovi/ + nov red za 2622\n");
printf("  · php migracija/alati/htaccess-301-generate.php  &&  redirect-verify.php\n");
