<?php
/**
 * W3 3.10 — full regression sweep. Nastalo 2026-08-10.
 * Read-only: samo GET/HEAD zahtevi, ne dira bazu ni fajlove sajta.
 *
 * UPOTREBA:
 *   php regression-sweep.php          (izlaz ide u folder gde je skripta)
 *
 * Za proveru PRODUKCIJE posle migracije: promeniti $BASE na
 * 'https://www.antasline.com' i uporediti brojeve sa lokalnim baseline-om.
 * Očekivano stanje lokalnog builda 2026-08-10 posle popravki:
 *   195 stranica · 0 non-200 · 0 stranica bez H1 · 0 sa 2×H1 ·
 *   0 nevalidnih JSON-LD · 0 slomljenih slika · 0 internih 404
 *
 * ⚠️ Dva bug-a u prvoj verziji (popravljena, ne vraćati):
 *   - `strip_tags()` ZADRŽAVA sadržaj <script> → provera „sirov JSON-LD u
 *     vidljivom tekstu" je davala lažni pozitiv na svih 195 stranica.
 *     Script/style blokovi se moraju ukloniti pre `strip_tags()`.
 *   - regex delimiter `#` sa znakom `#` u klasi → „Unknown modifier".
 *
 * Faza 1: sitemap_index -> svi URL-ovi
 * Faza 2: GET svake stranice -> status, 1xH1, JSON-LD validnost, popis slika/linkova
 * Faza 3: HEAD nad svim jedinstvenim slikama i internim linkovima
 */

$BASE = 'http://localhost/antasline';
$OUT  = __DIR__;
@mkdir($OUT, 0777, true);

function fetch($url, $method = 'GET', $timeout = 60) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_TIMEOUT        => $timeout,
        CURLOPT_NOBODY         => ($method === 'HEAD'),
        CURLOPT_USERAGENT      => 'AntasLine-Regression/1.0',
        CURLOPT_ENCODING       => '',
    ]);
    $body = curl_exec($ch);
    $info = curl_getinfo($ch);
    $err  = curl_error($ch);
    curl_close($ch);
    return ['body' => $body, 'code' => $info['http_code'], 'ct' => $info['content_type'] ?? '',
            'redir' => $info['redirect_url'] ?? '', 'err' => $err, 'time' => $info['total_time']];
}

// ---------- Faza 1: URL-ovi iz sitemap-a ----------
echo "Faza 1: citam sitemap...\n";
$idx = fetch("$BASE/sitemap_index.xml");
if ($idx['code'] != 200) { die("sitemap_index nedostupan: {$idx['code']}\n"); }
preg_match_all('#<loc>([^<]+)</loc>#i', $idx['body'], $m);
$subs = $m[1];
echo "  pod-sitemap-ova: " . count($subs) . "\n";

$urls = [];
$srcMap = [];
foreach ($subs as $s) {
    $r = fetch($s);
    if ($r['code'] != 200) { echo "  !! sub sitemap {$s} -> {$r['code']}\n"; continue; }
    preg_match_all('#<loc>([^<]+)</loc>#i', $r['body'], $mm);
    $name = basename(parse_url($s, PHP_URL_PATH));
    foreach ($mm[1] as $u) {
        $u = html_entity_decode($u, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        if (!isset($srcMap[$u])) { $urls[] = $u; $srcMap[$u] = $name; }
    }
    echo "  " . str_pad($name, 34) . count($mm[1]) . "\n";
}
echo "  UKUPNO jedinstvenih URL-ova: " . count($urls) . "\n\n";

// ---------- Faza 2: pregled svake stranice ----------
echo "Faza 2: pregled stranica...\n";
$pages = [];
$allImgs = [];
$allLinks = [];
$n = 0;
foreach ($urls as $u) {
    $n++;
    $r = fetch($u);
    $row = [
        'url' => $u, 'sitemap' => $srcMap[$u], 'code' => $r['code'], 'redir' => $r['redir'],
        'h1' => 0, 'h1_texts' => '', 'jsonld' => 0, 'jsonld_bad' => 0, 'jsonld_types' => '',
        'imgs' => 0, 'imgs_noalt' => 0, 'links_int' => 0, 'title' => '', 'metadesc' => '',
        'bytes' => strlen((string)$r['body']), 'time' => round($r['time'], 2), 'notes' => [],
    ];
    $html = (string)$r['body'];

    if ($r['code'] == 200 && stripos($r['ct'], 'html') !== false) {
        // H1
        if (preg_match_all('#<h1\b[^>]*>(.*?)</h1>#is', $html, $h1m)) {
            $row['h1'] = count($h1m[0]);
            $row['h1_texts'] = implode(' | ', array_map(function ($t) {
                return trim(preg_replace('/\s+/u', ' ', strip_tags($t)));
            }, $h1m[1]));
        }
        // title / metadesc
        if (preg_match('#<title[^>]*>(.*?)</title>#is', $html, $tm)) {
            $row['title'] = trim(html_entity_decode(strip_tags($tm[1]), ENT_QUOTES, 'UTF-8'));
        }
        if (preg_match('#<meta[^>]+name=["\']description["\'][^>]+content=["\'](.*?)["\']#is', $html, $dm)) {
            $row['metadesc'] = trim(html_entity_decode($dm[1], ENT_QUOTES, 'UTF-8'));
        }
        // JSON-LD
        if (preg_match_all('#<script[^>]+type=["\']application/ld\+json["\'][^>]*>(.*?)</script>#is', $html, $jm)) {
            $row['jsonld'] = count($jm[1]);
            $types = [];
            foreach ($jm[1] as $raw) {
                $data = json_decode(trim($raw), true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $row['jsonld_bad']++;
                    $row['notes'][] = 'JSON-LD parse: ' . json_last_error_msg();
                    continue;
                }
                array_walk_recursive($data, function ($v, $k) use (&$types) {
                    if ($k === '@type') { $types[] = is_array($v) ? implode('/', $v) : $v; }
                });
            }
            $row['jsonld_types'] = implode(',', array_unique($types));
        }
        // goli JSON-LD van script taga (F7.15 obrazac)
        // ⚠️ strip_tags() ZADRŽAVA sadržaj <script> — bez ovog uklanjanja
        // provera daje lažni pozitiv na svakoj stranici koja ima schema-u.
        $visible = preg_replace('#<(script|style)\b[^>]*>.*?</\1>#is', '', $html);
        if (preg_match('#"@context"\s*:\s*"https?://schema\.org#i', strip_tags($visible))) {
            $row['notes'][] = 'SIROV JSON-LD u vidljivom tekstu (kses pojeo script?)';
        }
        // slike
        if (preg_match_all('#<img\b[^>]*>#is', $html, $im)) {
            $row['imgs'] = count($im[0]);
            foreach ($im[0] as $tag) {
                if (!preg_match('#\balt\s*=#i', $tag)) { $row['imgs_noalt']++; }
                if (preg_match('#\bsrc\s*=\s*["\']([^"\']+)["\']#i', $tag, $sm)) {
                    $src = html_entity_decode($sm[1], ENT_QUOTES, 'UTF-8');
                    if (strpos($src, 'data:') === 0) { continue; }
                    if (strpos($src, '//') === 0) { $src = 'http:' . $src; }
                    if (strpos($src, 'http') !== 0) { $src = rtrim($BASE, '/') . '/' . ltrim($src, '/'); }
                    if (strpos($src, 'localhost') !== false) {
                        $allImgs[$src][] = $u;
                    }
                }
            }
        }
        // interni linkovi
        if (preg_match_all('#<a\b[^>]+href\s*=\s*["\']([^"\']+)["\']#is', $html, $am)) {
            foreach ($am[1] as $href) {
                $href = html_entity_decode($href, ENT_QUOTES, 'UTF-8');
                // delimiter NE sme biti # — u klasi je i sam znak #
                if (preg_match('~^(mailto:|tel:|javascript:|#)~i', $href)) { continue; }
                if (strpos($href, 'http') !== 0) {
                    if (strpos($href, '/') !== 0) { continue; }
                    $href = 'http://localhost' . $href;
                }
                if (strpos($href, 'http://localhost/antasline') !== 0) { continue; }
                $href = strtok($href, '#');
                // razmaci u query stringu (npr. ?form-naslov=Ponuda: X) —
                // pregledač ih sam kodira, curl ne
                $href = str_replace(' ', '%20', $href);
                $row['links_int']++;
                $allLinks[$href][] = $u;
            }
        }
    }
    $row['notes'] = implode(' ; ', $row['notes']);
    $pages[] = $row;
    if ($n % 25 === 0) { echo "  ...{$n}/" . count($urls) . "\n"; }
}
echo "  gotovo: {$n} stranica\n\n";

// ---------- Faza 3: slike i linkovi ----------
echo "Faza 3: provera " . count($allImgs) . " slika i " . count($allLinks) . " linkova...\n";
$badImgs = [];
$i = 0;
foreach ($allImgs as $src => $on) {
    $i++;
    $r = fetch($src, 'HEAD', 20);
    if ($r['code'] != 200) { $badImgs[] = ['url' => $src, 'code' => $r['code'], 'na' => array_slice(array_unique($on), 0, 5), 'broj' => count(array_unique($on))]; }
    if ($i % 200 === 0) { echo "  slike ...{$i}\n"; }
}
$badLinks = [];
$i = 0;
foreach ($allLinks as $href => $on) {
    $i++;
    $r = fetch($href, 'HEAD', 20);
    if ($r['code'] != 200) { $badLinks[] = ['url' => $href, 'code' => $r['code'], 'redir' => $r['redir'], 'na' => array_slice(array_unique($on), 0, 5), 'broj' => count(array_unique($on))]; }
    if ($i % 200 === 0) { echo "  linkovi ...{$i}\n"; }
}
echo "  slomljenih slika: " . count($badImgs) . " | problematicnih linkova: " . count($badLinks) . "\n\n";

// ---------- Izlaz ----------
$fp = fopen("$OUT/regression-pages.csv", 'w');
fwrite($fp, "\xEF\xBB\xBF");
fputcsv($fp, array_keys($pages[0]), ';');
foreach ($pages as $p) { fputcsv($fp, $p, ';'); }
fclose($fp);

file_put_contents("$OUT/regression-assets.json", json_encode([
    'bad_images' => $badImgs, 'bad_links' => $badLinks,
    'total_images' => count($allImgs), 'total_links' => count($allLinks),
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));

// ---------- Sažetak ----------
$bad = ['status' => [], 'h1_0' => [], 'h1_multi' => [], 'jsonld_bad' => [], 'notes' => [], 'no_title' => [], 'no_meta' => []];
foreach ($pages as $p) {
    if ($p['code'] != 200)            { $bad['status'][]     = "{$p['code']} {$p['url']}"; continue; }
    if ($p['h1'] == 0)                { $bad['h1_0'][]       = $p['url']; }
    if ($p['h1'] > 1)                 { $bad['h1_multi'][]   = "{$p['h1']}x {$p['url']} :: {$p['h1_texts']}"; }
    if ($p['jsonld_bad'] > 0)         { $bad['jsonld_bad'][] = "{$p['jsonld_bad']} {$p['url']}"; }
    if ($p['notes'] !== '')           { $bad['notes'][]      = "{$p['url']} :: {$p['notes']}"; }
    if ($p['title'] === '')           { $bad['no_title'][]   = $p['url']; }
    if ($p['metadesc'] === '')        { $bad['no_meta'][]    = $p['url']; }
}
echo "===== SAZETAK =====\n";
echo "Stranica ukupno: " . count($pages) . "\n";
foreach ($bad as $k => $v) { echo str_pad($k, 14) . count($v) . "\n"; }
file_put_contents("$OUT/regression-summary.json", json_encode($bad, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
echo "\nFajlovi: regression-pages.csv, regression-assets.json, regression-summary.json\n";
