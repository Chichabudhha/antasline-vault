<?php
/**
 * ads-url-audit.php — final URL audit oglasa pred migraciju (W4 4.10, checklist §A)
 *
 * Read-only. Ne dira ni Google Ads, ni bazu, ni .htaccess.
 *
 * Ulaz (bilo koji od tri):
 *   --json  izlaz skripte  .claude/skills/antasline-konektor/scripts/ads_final_urls.py
 *   --ga4   izlaz skripte  .claude/skills/antasline-konektor/scripts/ga4_paid_landing.py
 *   --txt   obican spisak URL-ova/putanja, jedan po liniji
 * Vise ulaza se moze kombinovati — izvor se belezi po URL-u.
 *
 * Za svaku putanju:
 *   1) HTTP status na lokalnom buildu (to je stanje POSLE migracije)
 *   2) da li je pokrivena nekim pravilom iz htaccess-301-DRAFT.txt (73 pravila)
 *   3) klasifikacija:
 *        OK              200 na buildu, oglas ne treba dirati
 *        PREPISATI       301 pravilo postoji -> oglas radi, ali treba ga prepisati
 *                        na cilj (redirect trosi latenciju i muti landing page experience)
 *        PUKAO           nije 200 i nema pravila -> 🔴 oglas vodi na 404 posle migracije
 *        REDIRECT-BUILD  build sam vraca 301 (nekanonicna putanja) -> prepisati na cilj
 *
 * Pokretanje:
 *   C:\xampp\php\php.exe migracija\alati\ads-url-audit.php --ga4 nesto.json --out izvestaj.csv
 */

$BASE  = 'http://localhost/antasline';
$DIR   = __DIR__ . '/..';
$DRAFT = $DIR . '/htaccess-301-DRAFT.txt';

// ---------- argumenti ----------
$inputs = ['json' => [], 'ga4' => [], 'txt' => []];
$out = null;
$argvv = array_slice($argv, 1);
for ($i = 0; $i < count($argvv); $i++) {
    $a = $argvv[$i];
    if ($a === '--out') { $out = $argvv[++$i] ?? null; continue; }
    if (in_array(substr($a, 2), ['json', 'ga4', 'txt'], true)) {
        $inputs[substr($a, 2)][] = $argvv[++$i] ?? '';
        continue;
    }
    fwrite(STDERR, "Nepoznat argument: $a\n");
    exit(1);
}
if (!array_filter($inputs)) {
    fwrite(STDERR, "GRESKA: nijedan ulaz. Koristi --json / --ga4 / --txt (v. zaglavlje fajla).\n");
    exit(1);
}

/** vraca host ili '' ako je putanja bez domena */
function url_host($u) {
    if (preg_match('~^https?://([^/]+)~i', trim($u), $m)) return strtolower($m[1]);
    return '';
}

/** da li host pripada nasem sajtu (www/bez www) */
function is_our_host($host) {
    return $host === '' || $host === 'antasline.com' || $host === 'www.antasline.com';
}

function norm_path($u) {
    $u = trim($u);
    if ($u === '' || $u === '(not set)') return '';
    if (preg_match('~^https?://[^/]*(/.*)?$~i', $u, $m)) $u = $m[1] ?? '/';
    $u = preg_replace('~[?#].*$~', '', $u);   // query/hash ne uticu na 301 mapu
    $u = rawurldecode($u);
    if ($u === '') $u = '/';
    if ($u[0] !== '/') $u = '/' . $u;
    return $u;
}

function http_probe($base, $path) {
    $enc = implode('/', array_map('rawurlencode', explode('/', $path)));
    $ch = curl_init($base . $enc);
    curl_setopt_array($ch, [
        CURLOPT_NOBODY => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 25,
        CURLOPT_HEADER => true,
    ]);
    $raw  = (string)curl_exec($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $loc = '';
    if (preg_match('~^Location:\s*(.+)$~mi', $raw, $m)) $loc = trim($m[1]);
    return [$code, $loc];
}

// ---------- 1. ucitavanje 301 pravila iz drafta ----------
$rules = [];
foreach (file($DRAFT, FILE_IGNORE_NEW_LINES) as $line) {
    if (!preg_match('~^\s*RedirectMatch\s+301\s+"([^"]+)"\s+(\S+)~', $line, $m)) continue;
    $rules[] = ['re' => $m[1], 'dst' => $m[2]];
}
if (!$rules) { fwrite(STDERR, "GRESKA: 0 pravila procitano iz $DRAFT\n"); exit(1); }

function match_rule($rules, $path) {
    foreach ($rules as $r) {
        if (@preg_match('~' . str_replace('~', '\~', $r['re']) . '~u', $path)) return $r;
    }
    return null;
}

// ---------- 2. ucitavanje ulaza ----------
$urls = []; // path => ['izvori'=>[], 'raw'=>[], 'tezina'=>int]

function add_url(&$urls, $raw, $izvor, $tezina = 0) {
    $host = url_host($raw);
    // Eksterni domen se NE normalizuje u putanju — inace bi ekopodneploce.rs/x/
    // bio proveren kao /x/ na nasem sajtu i dao lazan nalaz.
    $key = is_our_host($host) ? norm_path($raw) : 'EKST::' . rtrim(trim($raw));
    if ($key === '') return;
    if (!isset($urls[$key])) $urls[$key] = ['izvori' => [], 'raw' => [], 'tezina' => 0, 'host' => $host];
    $urls[$key]['izvori'][$izvor] = true;
    $urls[$key]['raw'][$raw] = true;
    $urls[$key]['tezina'] += $tezina;
}

foreach ($inputs['json'] as $f) {
    $d = json_decode(file_get_contents($f), true);
    foreach (($d['unique_final_urls'] ?? []) as $row) {
        foreach ($row['koriste'] as $k) add_url($urls, $row['url'], 'ads: ' . $k);
    }
}
foreach ($inputs['ga4'] as $f) {
    $d = json_decode(file_get_contents($f), true);
    foreach (($d['unique_paths'] ?? []) as $row) {
        add_url($urls, $row['path'], 'ga4 cpc landing', (int)$row['sessions']);
    }
}
foreach ($inputs['txt'] as $f) {
    foreach (file($f, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#')) continue;
        add_url($urls, $line, 'txt: ' . basename($f));
    }
}

// ---------- 3. provera ----------
$res = [];
foreach ($urls as $path => $meta) {
    // eksterni domen — ne proverava se protiv naseg builda
    if (str_starts_with($path, 'EKST::')) {
        $raw = substr($path, 6);
        $res[] = [
            'klasa'  => 'EKSTERNI-DOMEN',
            'path'   => $raw,
            'http'   => 0,
            'akcija' => '',
            'tezina' => $meta['tezina'],
            'izvori' => implode(' | ', array_keys($meta['izvori'])),
        ];
        continue;
    }

    [$code, $loc] = http_probe($BASE, $path);
    $rule = match_rule($rules, $path);

    if ($code === 200) {
        $klasa = 'OK';
        $akcija = '';
    } elseif ($rule) {
        $klasa = 'PREPISATI';
        $akcija = $rule['dst'];
    } elseif ($code >= 300 && $code < 400 && $loc !== '') {
        $klasa = 'REDIRECT-BUILD';
        $akcija = preg_replace('~^https?://[^/]*/antasline~', '', $loc);
    } else {
        $klasa = 'PUKAO';
        $akcija = '';
    }

    $res[] = [
        'klasa'   => $klasa,
        'path'    => $path,
        'http'    => $code,
        'akcija'  => $akcija,
        'tezina'  => $meta['tezina'],
        'izvori'  => implode(' | ', array_keys($meta['izvori'])),
    ];
}

$red = ['PUKAO' => 0, 'EKSTERNI-DOMEN' => 1, 'REDIRECT-BUILD' => 2, 'PREPISATI' => 3, 'OK' => 4];
usort($res, fn($a, $b) => [$red[$a['klasa']], -$a['tezina']] <=> [$red[$b['klasa']], -$b['tezina']]);

// ---------- 4. izlaz ----------
$sum = [];
foreach ($res as $r) $sum[$r['klasa']] = ($sum[$r['klasa']] ?? 0) + 1;

echo "=== ADS FINAL URL AUDIT — " . date('Y-m-d H:i') . " ===\n";
echo "Build: $BASE  ·  pravila u draftu: " . count($rules) . "  ·  URL-ova: " . count($res) . "\n\n";
foreach (['PUKAO', 'EKSTERNI-DOMEN', 'REDIRECT-BUILD', 'PREPISATI', 'OK'] as $k) {
    printf("%-16s %d\n", $k, $sum[$k] ?? 0);
}
echo "\n";
foreach ($res as $r) {
    if ($r['klasa'] === 'OK') continue;
    printf("[%s] %s  (HTTP %d, tezina %d)\n", $r['klasa'], $r['path'], $r['http'], $r['tezina']);
    if ($r['akcija']) printf("    -> %s\n", $r['akcija']);
    printf("    izvor: %s\n", $r['izvori']);
}

if ($out) {
    $fh = fopen($out, 'w');
    fwrite($fh, "\xEF\xBB\xBF");
    fputcsv($fh, ['klasa', 'putanja', 'http', 'novi_url', 'tezina', 'izvori'], ';');
    foreach ($res as $r) {
        fputcsv($fh, [$r['klasa'], $r['path'], $r['http'], $r['akcija'], $r['tezina'], $r['izvori']], ';');
    }
    fclose($fh);
    echo "\nCSV: $out\n";
}
