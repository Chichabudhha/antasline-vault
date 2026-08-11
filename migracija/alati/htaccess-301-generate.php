<?php
/**
 * htaccess-301-generate.php — generiše migracija/htaccess-301-DRAFT.txt
 * iz redirect-mapa-FINAL.csv + redirect-mapa-HISTORIJSKI-65-FLAT.csv (W3 3.9).
 *
 * Read-only prema bazi i prema živom .htaccess-u — piše SAMO draft fajl.
 *
 * Pravila generisanja:
 *  - `RedirectMatch 301 "^/putanja/?$"` umesto `Redirect 301` — mod_alias `Redirect`
 *    je PREFIKS-match, pa bi npr. /podovi-za-terase/ progutao 4 specifičnija pravila
 *    i lepio ostatak putanje na cilj. Sidrenje (^...$) to uklanja i čini redosled
 *    linija nebitnim.
 *  - Red iz HIST mape sa popunjenom 4. kolonom (`odluka_2026-08-11`) se PRESKAČE.
 *  - Red iz FINAL mape čiji status kaže "red se ne dodaje" (identičan URL) se preskače.
 *
 * Pokretanje: C:\xampp\php\php.exe migracija\alati\htaccess-301-generate.php
 */

$BASE = 'http://localhost/antasline';
$DIR  = __DIR__ . '/..';
$OUT  = $DIR . '/htaccess-301-DRAFT.txt';

function norm_path($u) {
    $u = trim($u);
    if ($u === '') return '';
    if (strpos($u, '(') === 0 && preg_match('~(/[^\s;]*)$~', $u, $m)) $u = $m[1];
    $u = trim(preg_replace('~\s*\(.*$~', '', $u));
    if (preg_match('~^https?://[^/]+(/.*)$~', $u, $m)) $u = $m[1];
    $u = rawurldecode($u);
    if ($u !== '' && $u[0] !== '/') $u = '/' . $u;
    return $u;
}
function http_code($base, $path) {
    $url = $base . implode('/', array_map('rawurlencode', explode('/', $path)));
    $ch = curl_init($url);
    curl_setopt_array($ch, [CURLOPT_NOBODY=>true, CURLOPT_FOLLOWLOCATION=>false,
        CURLOPT_RETURNTRANSFER=>true, CURLOPT_TIMEOUT=>20]);
    curl_exec($ch);
    $c = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return $c;
}
/**
 * putanja -> sidreni regex. Escape-uje SAMO stvarne metakaraktere; crtica van
 * karakter-klase nije metakarakter, pa se namerno ne escape-uje (preg_quote bi
 * je pretvorio u `\-` i učinio fajl nečitljivim na dan migracije).
 */
function rx($p) {
    $p = rtrim($p, '/');
    $p = addcslashes($p, '.^$*+?()[]{}|\\');
    return '^' . $p . '/?$';
}

$final = []; $hist = []; $skip_final = 0; $skip_hist = 0;

$f = fopen($DIR . '/redirect-mapa-FINAL.csv', 'r'); fgetcsv($f, 0, ';');
while (($r = fgetcsv($f, 0, ';')) !== false) {
    if (count($r) < 2 || trim($r[0]) === '') continue;
    $src = norm_path($r[0]); $dst = norm_path($r[1]);
    if (stripos($r[4] ?? '', 'red se ne dodaje') !== false || $src === $dst) { $skip_final++; continue; }
    $final[] = ['src'=>$src, 'dst'=>$dst, 'kl'=>(int)($r[3] ?? 0)];
}
fclose($f);

$f = fopen($DIR . '/redirect-mapa-HISTORIJSKI-65-FLAT.csv', 'r'); fgetcsv($f, 0, ';');
while (($r = fgetcsv($f, 0, ';')) !== false) {
    if (count($r) < 2 || trim($r[0]) === '') continue;
    if (trim($r[3] ?? '') !== '') { $skip_hist++; continue; }   // odluka: ne prenosi se
    $hist[] = ['src'=>norm_path($r[0]), 'dst'=>norm_path($r[1]), 'kl'=>(int)($r[2] ?? 0)];
}
fclose($f);

usort($hist, fn($a, $b) => $b['kl'] <=> $a['kl']);

// --- verifikacija pred upis ---
$bad = [];
foreach (array_merge($final, $hist) as $r) {
    $c = http_code($BASE, $r['dst']);
    if ($c != 200) $bad[] = "{$r['src']} -> {$r['dst']} (cilj HTTP $c)";
}
if ($bad) {
    echo "🔴 PREKID — ciljevi koji nisu 200 na lokalu:\n";
    foreach ($bad as $b) echo "   $b\n";
    exit(1);
}

$d = date('Y-m-d');
$L = [];
$L[] = "# ⛔ NE AKTIVIRATI DIREKTNO — ovo je DRAFT, ne deo živog .htaccess-a.";
$L[] = "# Aktivira se TEK na dan migracije (MASTER-PLAN V2, 3.9/3.11), iznad `# BEGIN WordPress` bloka.";
$L[] = "#";
$L[] = "# GENERISANO: $d skriptom migracija/alati/htaccess-301-generate.php";
$L[] = "# Izvori: redirect-mapa-FINAL.csv (" . count($final) . " pravila) +";
$L[] = "#         redirect-mapa-HISTORIJSKI-65-FLAT.csv (" . count($hist) . " pravila).";
$L[] = "# Svi ciljevi provereni HTTP 200 na lokalnom buildu na dan generisanja.";
$L[] = "#";
$L[] = "# 🔴 ZAŠTO RedirectMatch, a ne Redirect:";
$L[] = "#    mod_alias `Redirect` radi PREFIKS-match i lepi ostatak putanje na cilj.";
$L[] = "#    Npr. `Redirect /podovi-za-terase/` bi progutao /podovi-za-terase/bergo-multisport/";
$L[] = "#    i poslao ga na pogrešan URL. Sidreni `^...\/?$` to sprečava i čini redosled";
$L[] = "#    linija nebitnim. Isti razlog i za /home/industrijski-podovi/ grupu (8 pravila).";
$L[] = "#";
$L[] = "# 🔴 NAPOMENA: putanje su za PRODUKCIJU (antasline.com root), ne za lokalni";
$L[] = "#    /antasline/ podfolder.";
$L[] = "";
$L[] = "<IfModule mod_alias.c>";
$L[] = "";
$L[] = "# ================= A. PARITY ODLUKE (redirect-mapa-FINAL.csv) =================";
foreach ($final as $r) {
    $L[] = sprintf('RedirectMatch 301 "%s" %s', rx($r['src']), $r['dst']);
}
$L[] = "";
$L[] = "# ====== B. ISTORIJSKA PRAVILA — preuzeta iz Redirection plugina sa live-a ======";
$L[] = "# 🔴 Bez ovog bloka se GUBE: migracija zamenjuje živu bazu lokalnom, a sa njom";
$L[] = "#    nestaju i pravila Redirection plugina. Sortirano po zabeleženim GSC pogocima.";
foreach ($hist as $r) {
    $L[] = sprintf('RedirectMatch 301 "%s" %s   # %d', rx($r['src']), $r['dst'], $r['kl']);
}
$L[] = "";
$L[] = "</IfModule>";
$L[] = "";
$L[] = "# --- NE DODAVATI (dokumentovano da se ne bi vratilo greškom) ---";
$L[] = "# 3 istorijska pravila su namerno izostavljena — v. kolonu `odluka_2026-08-11`";
$L[] = "# u redirect-mapa-HISTORIJSKI-65-FLAT.csv (2 bi ubila postojeće stranice,";
$L[] = "# 1 pravi petlju sa FINAL mapom).";
$L[] = "# 3 reda iz FINAL mape nemaju pravilo jer stranica živi na IDENTIČNOM URL-u";
$L[] = "# (/sportske-podloge/kosarkaske-konstrukcije/, /sportske-podloge/padel-tereni/,";
$L[] = "#  /sportski-podovi-za-sale-i-balone/).";
$L[] = "";

file_put_contents($OUT, implode("\n", $L));

printf("✓ Upisano %s\n", $OUT);
printf("  FINAL:  %d pravila (%d preskočeno — identičan URL)\n", count($final), $skip_final);
printf("  HIST:   %d pravila (%d preskočeno — odluka 2026-08-11)\n", count($hist), $skip_hist);
printf("  UKUPNO: %d RedirectMatch linija, svi ciljevi 200.\n", count($final) + count($hist));
