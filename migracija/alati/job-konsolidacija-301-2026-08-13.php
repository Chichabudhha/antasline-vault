<?php
/**
 * job-konsolidacija-301-2026-08-13.php — tri konsolidacije duplikata (C + D + B)
 * po nalazima iz [[seo/2026-08-13-kanibalizacija-konsolidacija-plan]].
 *
 * C — Parkiralište:  cenovni sadržaj sa 16876 → 16589, 16876 → draft
 * D — Maloprodaja:   16683 → draft, sve veze → 16142 (primarna, live + Ads odredište)
 * B — Bergo Easy:    16665 → draft (proizvod diskontinuiran), event reference + primena → 16663
 *
 * ZAŠTO OVIM REDOM: prvo se sadržaj PRESELI, pa tek onda izvor draftuje —
 * obrnut redosled bi ostavio prazan prozor u kome stranica nije ni na jednom mestu.
 *
 * 🔴 post_content se čita i piše DIREKTNO preko $wpdb — `get_post_field()` u
 * `display` kontekstu pušta wptexturize, koji iskrivi apostrofe u WPBakery
 * `css=""` atributima i tiho obori str_replace (gotcha iz FAZE 1, 2026-08-13).
 *
 * 301 pravila se ovde NE pišu — draft `htaccess-301-DRAFT.txt` se generiše
 * skriptom `htaccess-301-generate.php`; ova skripta samo ispisuje redove koje
 * treba dodati, i za koje URL-ove (16665, 16683 postoje na live-u; 16876 je
 * live 404 → njemu 301 ne treba).
 *
 * UPOTREBA:
 *   php job-konsolidacija-301-2026-08-13.php          (probni prolaz)
 *   php job-konsolidacija-301-2026-08-13.php --write  (upis)
 *
 * Backup pre upisa: antasline_local_2026-08-13_pre-konsolidacija-301.sql
 */

require_once 'C:/xampp/htdocs/antasline/wp-load.php';

global $wpdb;
$WRITE = in_array('--write', $argv, true);
$BASE  = 'http://localhost/antasline';
$greske = 0;

function sadrzaj($id) { global $wpdb; return $wpdb->get_var($wpdb->prepare("SELECT post_content FROM {$wpdb->posts} WHERE ID=%d", $id)); }

function upisi_sadrzaj($id, $novo, $WRITE) {
    global $wpdb;
    if (!$WRITE) { printf("  [probno] %d bi dobio %d bajtova\n", $id, strlen($novo)); return; }
    $wpdb->update($wpdb->posts, ['post_content' => $novo], ['ID' => $id]);
    clean_post_cache($id);
    printf("  ✅ %d upisan (%d bajtova)\n", $id, strlen($novo));
}

/** Zameni tačno jednom; prijavi ako obrazac nije nađen (tiho promašen str_replace = glavni rizik). */
function zameni($sadrzaj, $trazi, $zameni, $opis, &$greske) {
    $n = substr_count($sadrzaj, $trazi);
    if ($n !== 1) { printf("  🔴 PROMAŠAJ (%d pogodaka): %s\n", $n, $opis); $greske++; return $sadrzaj; }
    printf("  · %s\n", $opis);
    return str_replace($trazi, $zameni, $sadrzaj);
}

echo "=== C — PARKIRALIŠTE (16876 → 16589) ===\n";

$c = sadrzaj(16589);

// 1. Uklanjanje linka ka stranici koja se draftuje (ostaje link ka katalogu).
$c = zameni($c,
 '<p>Koliko košta parking po m² i koja varijanta ispune se bira: <a href="' . $BASE . '/podloge-za-parkiraliste-cena/">podloge za parkiralište — cena i nosivost</a>. Svi proizvodi su dostupni',
 '<p>Cene po modelu i varijanti ispune su niže na strani, u tabeli <a href="#cena-parking">Cena podloge za parkiralište po m²</a>. Svi proizvodi su dostupni',
 'link ka 16876 zamenjen sidrom na novu cenovnu sekciju', $greske);

// 2. Cenovna sekcija — ubacuje se UNUTAR postojeće paper sekcije, iza uporedne tabele.
//    Nova `.al-section` se namerno NE pravi: dve susedne sekcije istog tona daju
//    144px mrtve trake (FAZA 2, 2026-08-13), a ovde bi paper došao uz paper.
$cena_blok = <<<'HTML'
<h2 id="cena-parking">Cena podloge za parkiralište po m²</h2>
<p>Plastične podloge (saće) za parking koštaju <strong>od 2.800 do 4.200 din/m² sa PDV</strong>, zavisno od modela i ispune — rešetke se pune šljunkom ili travom i daju parking bez betona, blata i barica.</p>
<div style="overflow-x:auto"><table class="al-table"><thead><tr><th>Varijanta</th><th>Model</th><th>Nosivost</th><th>Cena (din/m², sa PDV)</th></tr></thead><tbody><tr><td>Travnata ispuna — zeleni parking</td><td>Runfloor</td><td>do 600 t/m²</td><td><strong>2.800–3.400</strong></td></tr><tr><td>Šljunčana ispuna — najčešći izbor</td><td>Geogravel</td><td>do 400 t/m²</td><td><strong>4.000</strong></td></tr><tr><td>Vozila preko travnate površine</td><td>Geocross</td><td>do 100 t/m²</td><td><strong>4.200</strong></td></tr><tr><td>Krovovi i zelene površine</td><td>Geoflor</td><td>—</td><td><strong>3.400</strong></td></tr></tbody></table></div>
<p><em>Cene se odnose na materijal, sa PDV-om. Ugradnja i priprema terena se ugovaraju uz ponudu — pošaljite kvadraturu i namenu (putnička vozila ili kamioni) i vraćamo ukupnu cenu u roku od jednog radnog dana.</em></p>

<h3>Saće ili nasut šljunak?</h3>
<p>Sam nasut šljunak se razvlači pod točkovima: prave se kolotragovi i udarne rupe, pa se šljunak dosipava i ravna svake godine. Saćasta rešetka drži šljunak na mestu — površina ostaje ravna godinama, bez dosipavanja i bez erozije. Zato je na duži period ukupan trošak saća uporediv ili niži od običnog nasipa, uz površinu koja je stalno uredna i prohodna.</p>

HTML;

$c = zameni($c, "<h2>Gde se koriste?</h2>", $cena_blok . "<h2>Gde se koriste?</h2>",
    'cenovna sekcija + „saće ili šljunak" ubačeni pre „Gde se koriste?"', $greske);

// 3. Dva FAQ pitanja (vidljiv tekst) — cena i saće/šljunak, oba iz 16876.
$faq_novo = '<div class="al-faq__item"><h3>Kolika je cena podloge za parking po m²?</h3><p>Materijal košta od 2.800 do 4.200 din/m² sa PDV, zavisno od modela: Runfloor (travnata ispuna) 2.800–3.400, Geoflor 3.400, Geogravel (šljunčana ispuna) 4.000, Geocross 4.200. Ugradnja se ugovara uz ponudu — pošaljite kvadraturu i vraćamo ukupnu cenu.</p></div>'
          . '<div class="al-faq__item"><h3>Da li je bolje saće ili samo nasuti šljunak?</h3><p>Nasut šljunak se razvlači i prave se kolotragovi, pa se dosipava svake godine. Saće drži šljunak na mestu i površina ostaje ravna godinama — na duži period ukupan trošak je uporediv ili niži.</p></div></div>';

$c = zameni($c,
 '<p>Da, ćelije se pune šljunkom bilo koje veličine i boje po želji, tako da se površina estetski uklopi u okruženje.</p></div></div>',
 '<p>Da, ćelije se pune šljunkom bilo koje veličine i boje po želji, tako da se površina estetski uklopi u okruženje.</p></div>' . $faq_novo,
 '2 FAQ stavke dodate u .al-faq', $greske);

// 4. Ista 2 pitanja u inline FAQPage JSON-LD — inače schema i vidljiv sadržaj razilaze.
$jsonld_novo = ',{"@type":"Question","name":"Kolika je cena podloge za parking po m²?","acceptedAnswer":{"@type":"Answer","text":"Materijal košta od 2.800 do 4.200 din/m² sa PDV, zavisno od modela: Runfloor (travnata ispuna) 2.800–3.400, Geoflor 3.400, Geogravel (šljunčana ispuna) 4.000, Geocross 4.200. Ugradnja se ugovara uz ponudu — pošaljite kvadraturu i vraćamo ukupnu cenu."}},{"@type":"Question","name":"Da li je bolje saće ili samo nasuti šljunak?","acceptedAnswer":{"@type":"Answer","text":"Nasut šljunak se razvlači i prave se kolotragovi, pa se dosipava svake godine. Saće drži šljunak na mestu i površina ostaje ravna godinama — na duži period ukupan trošak je uporediv ili niži."}}]}</script>';

$c = zameni($c,
 'tako da se površina estetski uklopi u okruženje."}}]}</script>',
 'tako da se površina estetski uklopi u okruženje."}}' . $jsonld_novo,
 '2 pitanja dodata u FAQPage JSON-LD', $greske);

upisi_sadrzaj(16589, $c, $WRITE);

// 5. Veze ka 16876 sa druge dve stranice → 16589.
foreach ([16873, 17273] as $id) {
    $s = sadrzaj($id);
    $n = substr_count($s, '/podloge-za-parkiraliste-cena/');
    $s = str_replace($BASE . '/podloge-za-parkiraliste-cena/', $BASE . '/podloge-za-parkiraliste-i-staze/', $s);
    printf("  · %d: %d veza preusmereno na 16589\n", $id, $n);
    upisi_sadrzaj($id, $s, $WRITE);
}

echo "\n=== D — MALOPRODAJA (16683 → 16142) ===\n";

foreach ([16142, 17026] as $id) {
    $s = sadrzaj($id);
    $n = substr_count($s, '/industrijski-podovi/podovi-za-maloprodajne-objekte/');
    if ($n === 0) { printf("  · %d: nema veza\n", $id); continue; }
    // 16142 je sama cilj — link na sebe se ne pravi, nego se uklanja omotač <a>.
    if ($id === 16142) {
        $s = preg_replace('~<a href="' . preg_quote($BASE, '~') . '/industrijski-podovi/podovi-za-maloprodajne-objekte/"[^>]*>(.*?)</a>~s', '$1', $s);
        printf("  · %d: %d self-link(ova) razmotano (ostaje tekst)\n", $id, $n);
    } else {
        $s = str_replace($BASE . '/industrijski-podovi/podovi-za-maloprodajne-objekte/', $BASE . '/podovi-za-radnje-i-maloprodajne-objekte/', $s);
        printf("  · %d: %d veza preusmereno na 16142\n", $id, $n);
    }
    upisi_sadrzaj($id, $s, $WRITE);
}

echo "\n=== B — BERGO EASY (16665 → 16663) ===\n";

$b = sadrzaj(16663);

// 1. Primena — dopuna liste namenama koje je nosila 16665 (sajmovi/koncerti/TV).
$b = zameni($b,
 '<li>Događaji na ledu</li></ul>',
 '<li>Događaji na ledu</li><li>Sajmovi i korporativne manifestacije</li><li>Trkački dani i motosport paddock</li><li>Koncerti i podloge za ples</li><li>Šatorski podovi i izlagački štandovi</li><li>TV i filmska studija</li></ul>',
 'lista primene dopunjena sa 5 event namena', $greske);

// 2. Reference galerija sa 16665 — ubacuje se U POSTOJEĆU „u praksi" mist sekciju,
//    ne kao nova sekcija (isti razlog kao kod 16589: ritam tonova).
$galerija = '<h3 style="margin-top:32px">Na manifestacijama, sajmovima i promocijama</h3>'
 . '<p>Podloga se postavlja i podiže bez alata i bez lepka, pa isti set ploča ide sa štanda na štand — od sajamskih i promotivnih prostora do bašta kafića i motosport paddock-a.</p>'
 . '<div class="al-grid al-grid--3" style="margin:24px 0">'
 . '<img src="' . $BASE . '/wp-content/uploads/2026/07/paddock-na-trkackoj-stazi.webp" alt="Trkački automobil i servisna oprema pod šatorom na crnoj modularnoj podlozi" loading="lazy" />'
 . '<img src="' . $BASE . '/wp-content/uploads/2026/07/izlozbeni-prostor-sa-trkackim-automobilom.webp" alt="Trkački automobil na narandžasto-crnoj podlozi u šahovskom rasporedu na izložbi" loading="lazy" />'
 . '<img src="' . $BASE . '/wp-content/uploads/2026/07/promotivni-stand-proizvodjaca-automobila.webp" alt="Automobil na promotivnom štandu sa modularnom podlogom i posetiocima" loading="lazy" />'
 . '<img src="' . $BASE . '/wp-content/uploads/2026/07/sajamski-stand-plavo-i-narandzasto.webp" alt="Sajamski štand sa barskim stolicama na plavo-narandžastoj modularnoj podlozi" loading="lazy" />'
 . '<img src="' . $BASE . '/wp-content/uploads/2026/07/stand-na-poslovnom-sajmu.webp" alt="Posetioci na štandu poslovnog sajma sa podlogom u bojama izlagača" loading="lazy" />'
 . '<img src="' . $BASE . '/wp-content/uploads/2026/07/otvoreni-izlozbeni-prostor.webp" alt="Automobil i sedeći deo na otvorenom izložbenom prostoru sa modularnom podlogom" loading="lazy" />'
 . '<img src="' . $BASE . '/wp-content/uploads/2026/07/basta-kafica.webp" alt="Bašta kafića sa stolovima i stolicama na svetlo-tamnoj šahovskoj podlozi" loading="lazy" />'
 . '<img src="' . $BASE . '/wp-content/uploads/2026/07/terasa-uz-plazu.webp" alt="Ležaljka sa suncobranom na beloj podlozi terase uz plažu" loading="lazy" />'
 . '</div>';

$b = zameni($b,
 '<span class="al-card__title">Podloga za događaje</span></div></div>',
 '<span class="al-card__title">Podloga za događaje</span></div></div>' . $galerija,
 'galerija sa 8 event fotografija preseljena u „Bergo Solid u praksi"', $greske);

upisi_sadrzaj(16663, $b, $WRITE);

// 3. Veza sa 17019 (Bergo brend stranica) → 16663.
$s = sadrzaj(17019);
$n = substr_count($s, '/spoljnje-podne-obloge/bergo-easy/');
$s = str_replace($BASE . '/spoljnje-podne-obloge/bergo-easy/', $BASE . '/iznajmljivanje-podova/', $s);
printf("  · 17019: %d veza preusmereno na 16663\n", $n);
upisi_sadrzaj(17019, $s, $WRITE);

// 4. Title/meta 16663 — preuzima ključne reči sa 16665 („manifestacije, sajmovi,
//    promocije"), jer te upite posle draftovanja niko drugi ne cilja.
$meta = [
 'rank_math_title'       => 'Podloge za manifestacije, sajmove i promocije — najam',
 'rank_math_description' => 'Iznajmljivanje modularnih podloga za sajmove, manifestacije, koncerte i promocije — montaža bez lepka i alata, preko trave, peska i zemlje. Zatražite ponudu.',
];
foreach ($meta as $k => $v) {
    if ($WRITE) { update_post_meta(16663, $k, $v); }
    printf("  %s %s = %s\n", $WRITE ? '✅' : '[probno]', $k, mb_substr($v, 0, 60) . '…');
}

echo "\n=== DRAFTOVANJE IZVORA + MENI ===\n";

$draft = [
 16876 => 'Podloge za parkiralište — cena (sadržaj u 16589; live 404, 301 ne treba)',
 16683 => 'Podovi za maloprodajne objekte (dupliranje sa 16142; live 200 → 301 potreban)',
 16665 => 'Bergo Easy (proizvod diskontinuiran; live 200 → 301 potreban)',
];
foreach ($draft as $id => $zasto) {
    if ($WRITE) { $wpdb->update($wpdb->posts, ['post_status' => 'draft'], ['ID' => $id]); clean_post_cache($id); }
    printf("  %s %d → draft — %s\n", $WRITE ? '✅' : '[probno]', $id, $zasto);
}

// Meni: stavke koje bi ostale da pokazuju na draft (mrtav link u navigaciji).
$meni = [
 17427 => ['draft', null, 'Podloge za parkiralište (Cene segment) — 16589 je već u meniju kao „Parkiralište i staze"'],
 17411 => ['repoint', 16142, 'Radnje – Ecotile ploče → 16142'],
 16724 => ['repoint', 16663, 'Sajamske podloge → 16663'],
 17415 => ['repoint', 16663, 'Sajmovi i manifestacije → 16663'],
];
foreach ($meni as $mid => [$akcija, $cilj, $opis]) {
    if ($WRITE) {
        if ($akcija === 'draft') { $wpdb->update($wpdb->posts, ['post_status' => 'draft'], ['ID' => $mid]); }
        else { update_post_meta($mid, '_menu_item_object_id', (string) $cilj); }
    }
    printf("  %s meni %d (%s) — %s\n", $WRITE ? '✅' : '[probno]', $mid, $akcija, $opis);
}

echo "\n=== 301 PRAVILA ZA DRAFT (ručno dodati u htaccess-301-DRAFT.txt) ===\n";
echo 'RedirectMatch 301 "^/spoljnje-podne-obloge/bergo-easy/?$" "https://www.antasline.com/iznajmljivanje-podova/"' . "\n";
echo 'RedirectMatch 301 "^/industrijski-podovi/podovi-za-maloprodajne-objekte/?$" "https://www.antasline.com/podovi-za-radnje-i-maloprodajne-objekte/"' . "\n";
echo "(16876 nema pravilo — /podloge-za-parkiraliste-cena/ vraća 404 na live-u, nikad nije objavljena.)\n";

printf("\n%s Promašenih obrazaca: %d\n", $greske ? '🔴' : '🟢', $greske);
if (!$WRITE) { echo "PROBNI PROLAZ — ništa nije upisano. Ponovi sa --write.\n"; }
if ($WRITE) { echo "Ne zaboraviti: wp rewrite flush ako se URL-ovi ponašaju čudno.\n"; }
