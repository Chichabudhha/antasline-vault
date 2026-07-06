<?php
require_once '../../xampp/htdocs/antasline/wp-load.php';

global $wpdb;

$post_id = 16588;
$post = get_post( $post_id );

// Extract post_content, append JSON-LD before closing last row tag
$faq_schema = [
    "@context" => "https://schema.org",
    "@type" => "FAQPage",
    "mainEntity" => [
        [
            "@type" => "Question",
            "name" => "Koliko dugo traju Bergo podne obloge?",
            "acceptedAnswer" => [
                "@type" => "Answer",
                "text" => "Bergo podne obloge su dizajnirane da traju više od 15 godina čak i u teškim vremenskim uslovima. Proizvedene su prema ISO 9001 standardima i testirana na Univerzitetu u Štutgartu."
            ]
        ],
        [
            "@type" => "Question",
            "name" => "Mogu li se Bergo ploče demontirati?",
            "acceptedAnswer" => [
                "@type" => "Answer",
                "text" => "Da, Bergo ploče su montažno-demontažne. Mogu se lako postaviti i ukloniti, što ih čini idealno rešenje za privremene i sezonske prostore poput terasa i bašti."
            ]
        ]
    ]
];

$json_ld = '<script type="application/ld+json">' . json_encode($faq_schema, JSON_UNESCAPED_UNICODE) . '</script>';

// Append to post_content
$new_content = $post->post_content . "\n" . $json_ld;

$wpdb->update(
    $wpdb->posts,
    [ 'post_content' => $new_content ],
    [ 'ID' => $post_id ],
    [ '%s' ],
    [ '%d' ]
);

clean_post_cache( $post_id );

echo "✅ JSON-LD appended to post_content\n";
?>
