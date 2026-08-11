<?php
/**
 * redirect-verify.php — reverifikacija 301 mape pred migraciju (W3 3.9)
 *
 * Read-only. Ne dira bazu, ne dira .htaccess.
 *
 * Šta radi:
 *  1) učita redirect-mapa-FINAL.csv + redirect-mapa-HISTORIJSKI-65-FLAT.csv
 *  2) normalizuje stare/nove putanje (skida domen, dekodira %XX)
 *  3) detektuje: duplikate izvora, petlje (A→B i B→A), lance (A→B, B→C)
 *  4) HTTP provera SVAKOG cilja na lokalnom buildu (mora 200, ne 301/404)
 *  5) HTTP provera SVAKOG izvora na lokalnom buildu (ako je 200 → kolizija:
 *     redirect bi ubio stranicu koja postoji)
 *  6) prefiks-kolizije za Apache `Redirect` direktivu (prefix match!)
 *
 * Pokretanje:  C:\xampp\php\php.exe migracija\alati\redirect-verify.php
 */

$BASE = 'http://localhost/antasline';
$DIR  = __DIR__ . '/..';

function norm_path($u) {
    $u = trim($u);
    if ($u === '') return '';
    // "(lokalni URL, nema live parnjaka) /x/" -> "/x/"
    if (preg_match('~(/[^\s;]*)$~', $u, $m) && strpos($u, '(') === 0) $u = $m[1];
    // "/x/ (identican URL)" -> "/x/"
    $u = preg_replace('~\s*\(.*$~', '', $u);
    $u = trim($u);
    if (preg_match('~^https?://[^/]+(/.*)$~', $u, $m)) $u = $m[1];
    $u = rawurldecode($u);
    if ($u !== '' && $u[0] !== '/') $u = '/' . $u;
    return $u;
}

function http_code($base, $path) {
    $enc = implode('/', array_map('rawurlencode', explode('/', $path)));
    $url = $base . $enc;
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_NOBODY => true,
        CURLOPT_FOLLOWLOCATION => false,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_HEADER => true,
    ]);
    $out = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $loc = '';
    if (preg_match('~^Location:\s*(.+)$~mi', (string)$out, $m)) $loc = trim($m[1]);
    return [$code, $loc];
}

// ---------- učitavanje ----------
$rows = []; // ['src'=>, 'dst'=>, 'izvor'=>, 'klik'=>, 'napomena'=>]

$f = fopen($DIR . '/redirect-mapa-FINAL.csv', 'r');
fgetcsv($f, 0, ';');
while (($r = fgetcsv($f, 0, ';')) !== false) {
    if (count($r) < 2 || trim($r[0]) === '') continue;
    $src = norm_path($r[0]);
    $dst = norm_path($r[1]);
    $status = $r[4] ?? '';
    $skip = (stripos($status, 'red se ne dodaje') !== false) || ($src === $dst);
    $rows[] = ['src'=>$src, 'dst'=>$dst, 'izvor'=>'FINAL', 'klik'=>(int)($r[3] ?? 0),
               'skip'=>$skip, 'napomena'=>$status];
}
fclose($f);

$f = fopen($DIR . '/redirect-mapa-HISTORIJSKI-65-FLAT.csv', 'r');
fgetcsv($f, 0, ';');
while (($r = fgetcsv($f, 0, ';')) !== false) {
    if (count($r) < 2 || trim($r[0]) === '') continue;
    // 4. kolona `odluka_2026-08-11` popunjena = pravilo se namerno NE prenosi
    $odluka = trim($r[3] ?? '');
    $rows[] = ['src'=>norm_path($r[0]), 'dst'=>norm_path($r[1]), 'izvor'=>'HIST',
               'klik'=>(int)($r[2] ?? 0), 'skip'=>($odluka !== ''), 'napomena'=>$odluka];
}
fclose($f);

$aktivni = array_values(array_filter($rows, fn($x) => !$x['skip']));
printf("Ukupno redova: %d  (aktivnih pravila: %d, preskočeno: %d)\n\n",
    count($rows), count($aktivni), count($rows) - count($aktivni));

// ---------- 1. duplikat izvora ----------
echo "=== 1. DUPLIKAT IZVORA ===\n";
$bysrc = [];
foreach ($aktivni as $r) $bysrc[$r['src']][] = $r;
$dupe = 0;
foreach ($bysrc as $s => $g) {
    if (count($g) > 1) {
        $dupe++;
        echo "  DUPLIKAT $s\n";
        foreach ($g as $x) echo "     -> {$x['dst']}  [{$x['izvor']}]\n";
    }
}
if (!$dupe) echo "  0 duplikata\n";

// ---------- 2. petlje i lanci ----------
echo "\n=== 2. PETLJE I LANCI ===\n";
$map = [];
foreach ($aktivni as $r) $map[$r['src']] = $r['dst'];
$prob = 0;
foreach ($map as $s => $d) {
    if (isset($map[$d])) {
        if ($map[$d] === $s) { echo "  🔴 PETLJA: $s <-> $d\n"; }
        else { echo "  🟡 LANAC: $s -> $d -> {$map[$d]}\n"; }
        $prob++;
    }
}
if (!$prob) echo "  0 petlji/lanaca\n";

// ---------- 3. prefiks-kolizije (Apache `Redirect` je prefix-match) ----------
echo "\n=== 3. PREFIKS-KOLIZIJE (bitno samo ako se koristi `Redirect`, ne `RedirectMatch ^..$`) ===\n";
$srcs = array_keys($map);
$pk = 0;
foreach ($srcs as $a) foreach ($srcs as $b) {
    if ($a === $b) continue;
    if (strpos($b, $a) === 0) { echo "  $a  guta  $b\n"; $pk++; }
}
if (!$pk) echo "  0 prefiks-kolizija\n";

// ---------- 4. HTTP provera ciljeva ----------
echo "\n=== 4. CILJEVI NA LOKALU (mora 200) ===\n";
$badDst = [];
$seen = [];
foreach ($aktivni as $r) {
    if (isset($seen[$r['dst']])) continue;
    $seen[$r['dst']] = 1;
    [$c, $loc] = http_code($BASE, $r['dst']);
    if ($c != 200) {
        $badDst[] = [$r['dst'], $c, $loc, $r['izvor'], $r['klik']];
        printf("  🔴 %-70s %s %s\n", $r['dst'], $c, $loc);
    }
}
printf("  provereno %d jedinstvenih ciljeva, problematičnih: %d\n", count($seen), count($badDst));

// ---------- 5. IZVORI koji na lokalu postoje (kolizija) ----------
echo "\n=== 5. IZVORI KOJI NA LOKALU VRAĆAJU 200 (redirect bi ubio živu stranicu) ===\n";
$col = 0;
foreach ($aktivni as $r) {
    [$c, $loc] = http_code($BASE, $r['src']);
    if ($c == 200) {
        printf("  🔴 %-70s 200  (pravilo bi ga preusmerilo na %s) [%s, %d kl.]\n",
            $r['src'], $r['dst'], $r['izvor'], $r['klik']);
        $col++;
    }
}
if (!$col) echo "  0 kolizija\n";

echo "\nGOTOVO.\n";
