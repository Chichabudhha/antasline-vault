<?php
// Izvlaci citljiv tekst sa live stranica za llms-full.txt
// Usage: php extract-page-text.php <url>

$url = $argv[1];
$html = file_get_contents($url);
if ( ! $html ) {
    fwrite( STDERR, "GRESKA: ne mogu da povucem $url\n" );
    exit( 1 );
}

libxml_use_internal_errors( true );
$doc = new DOMDocument();
$doc->loadHTML( '<?xml encoding="utf-8" ?>' . $html );
libxml_clear_errors();

$xpath = new DOMXPath( $doc );

// Ukloni skriptove, stilove, header, footer, nav, forme (nebitno za sadrzaj)
$removeTags = [ 'script', 'style', 'nav', 'header', 'footer', 'noscript', 'form', 'iframe', 'svg' ];
foreach ( $removeTags as $tag ) {
    $nodes = $doc->getElementsByTagName( $tag );
    for ( $i = $nodes->length - 1; $i >= 0; $i-- ) {
        $node = $nodes->item( $i );
        $node->parentNode->removeChild( $node );
    }
}

$body = $doc->getElementsByTagName( 'body' )->item( 0 );
$text = $body ? $body->textContent : '';

// Ocisti visestruke praznine/nove redove
$text = preg_replace( '/[ \t]+/', ' ', $text );
$text = preg_replace( '/\n\s*\n+/', "\n\n", $text );
$lines = array_filter( array_map( 'trim', explode( "\n", $text ) ), function( $l ) {
    return strlen( $l ) > 0;
} );
$text = implode( "\n", $lines );

echo $text;
