<?php
/**
 * Pravi kontakt-list (mozaik sličica) iz liste fajlova — da se mnogo kandidata
 * pregleda kroz JEDNU sliku umesto da se svaka otvara posebno.
 * Poziv: php contact_sheet.php <lista.txt> <izlaz.jpg> [kolona]
 */
$list = file($argv[1], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$out  = $argv[2];
$cols = (int)($argv[3] ?? 4);
$cw = 320; $ch = 240; $pad = 6; $labelH = 26;

$rows = (int)ceil(count($list) / $cols);
$W = $cols * ($cw + $pad) + $pad;
$H = $rows * ($ch + $labelH + $pad) + $pad;
$sheet = imagecreatetruecolor($W, $H);
imagefill($sheet, 0, 0, imagecolorallocate($sheet, 24, 28, 34));
$white = imagecolorallocate($sheet, 235, 240, 245);

foreach ($list as $i => $path) {
    $path = trim($path);
    if (!is_file($path)) { continue; }
    $info = @getimagesize($path);
    if (!$info) { continue; }
    $src = match ($info[2]) {
        IMAGETYPE_JPEG => @imagecreatefromjpeg($path),
        IMAGETYPE_PNG  => @imagecreatefrompng($path),
        IMAGETYPE_WEBP => @imagecreatefromwebp($path),
        default => null,
    };
    if (!$src) { continue; }
    $c = $i % $cols; $r = intdiv($i, $cols);
    $x = $pad + $c * ($cw + $pad);
    $y = $pad + $r * ($ch + $labelH + $pad);

    // fit unutar okvira
    $sr = $info[0] / $info[1]; $dr = $cw / $ch;
    if ($sr > $dr) { $dw = $cw; $dh = (int)round($cw / $sr); } else { $dh = $ch; $dw = (int)round($ch * $sr); }
    $ox = $x + intdiv($cw - $dw, 2); $oy = $y + intdiv($ch - $dh, 2);
    imagecopyresampled($sheet, $src, $ox, $oy, 0, 0, $dw, $dh, $info[0], $info[1]);
    imagedestroy($src);

    $label = ($i + 1) . '. ' . basename($path) . '  [' . $info[0] . 'x' . $info[1] . ']';
    imagestring($sheet, 3, $x + 2, $y + $ch + 6, substr($label, 0, 52), $white);
}
imagejpeg($sheet, $out, 80);
echo "kontakt-list: $out  (" . count($list) . " slika, {$W}x{$H})\n";
