Listing the target folder to get an exact file list (step 1). Then will extract all .php, .sh and .sql filenames and open each to search for any wrong-cased WP table prefixes. Running directory listing now.

Opening every .php and .sh file found in the directory and scanning for occurrences of variants of the WP table prefix and related patterns ($table_prefix, sed/search-replace, str_replace). Reading all files in parallel.

Searching the folder for any mention of "wpgs" (case-insensitive), occurrences of "$table_prefix", and uses of sed/str_replace/search-replace to locate hardcoded prefix changes. Running three grep searches in the target directory.

| # | NALAZ/SUMNJA | Fajl | Linija | Napisano | Trebalo bi |
|---:|---|---|---:|---|---|
| 1 | NALAZ | migracija\alati\job-plugin-cleanup-cron.php | 12 | $res = $mysqli->query("SELECT option_value FROM wpGs_options WHERE option_name='cron'"); | wpgs_options |
| 2 | NALAZ | migracija\alati\job-plugin-cleanup-cron.php | 33 | $stmt = $mysqli->prepare("UPDATE wpGs_options SET option_value=? WHERE option_name='cron'"); | wpgs_options |
| 3 | NALAZ | migracija\alati\job-5438-semantika-faq-schema-2026-08-13.php | 254 | // 🔴 CLAUDE.md §2: lokalni wp-config nosi `wpGs_`, a MySQL vraća `wpgs_` (Windows | wpgs_ |
| 4 | SUMNJA | migracija\alati\job-5438-semantika-faq-schema-2026-08-13.php | 257 | $tabela = strtolower( $wpdb->prefix ) . 'yoast_indexable'; | wpgs_yoast_indexable (iz $wpdb->prefix) |
| 5 | SUMNJA | migracija\alati\job-16613-parket-konsolidacija.php | 74 | $wpdb->delete( $wpdb->prefix . 'yoast_indexable', array( 'object_id' => $old_id, 'object_type' => 'post' ) ); | wpgs_yoast_indexable (iz $wpdb->prefix) |
| 6 | SUMNJA | migracija\alati\job-brand-seo.php | 42 | $wpdb->query("DELETE FROM {$wpdb->prefix}rank_math_sitemap_cache"); | wpgs_rank_math_sitemap_cache (iz $wpdb->prefix) |
| 7 | SUMNJA | migracija\alati\job-futsal-mali-fudbal-refresh.php | 68 | $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}yoast_indexable WHERE object_id = %d", $id ) ); | wpgs_yoast_indexable (iz $wpdb->prefix) |
| 8 | SUMNJA | migracija\alati\job-piklbol-title-refresh.php | 40 | $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}yoast_indexable WHERE object_id = %d", $id ) ); | wpgs_yoast_indexable (iz $wpdb->prefix) |
| 9 | SUMNJA | migracija\alati\job-w1-polish-faza3-batch2.php | 42 | $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}yoast_indexable WHERE object_id = %d", $id ) ); | wpgs_yoast_indexable (iz $wpdb->prefix) |
| 10 | SUMNJA | migracija\alati\job-w1-polish-faza3-batch3.php | 38 | $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}yoast_indexable WHERE object_id = %d", $id ) ); | wpgs_yoast_indexable (iz $wpdb->prefix) |
| 11 | SUMNJA | migracija\alati\job-w1-polish-faza3-batch4.php | 37 | $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}yoast_indexable WHERE object_id = %d", $id ) ); | wpgs_yoast_indexable (iz $wpdb->prefix) |
| 12 | SUMNJA | migracija\alati\job-w1-polish-faza3-batch5.php | 35 | $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}yoast_indexable WHERE object_id = %d", $id ) ); | wpgs_yoast_indexable (iz $wpdb->prefix) |
| 13 | SUMNJA | migracija\alati\job-w7f3-4-duplikata-konsolidacija.php | 66 | $wpdb->delete( $wpdb->prefix . 'yoast_indexable', array( 'object_id' => $old_id, 'object_type' => 'post' ) ); | wpgs_yoast_indexable (iz $wpdb->prefix) |
| 14 | SUMNJA | migracija\alati\job-w7f35-podovi-za-stale.php | 60 | $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}yoast_indexable WHERE object_id=%d AND object_type='post'", $ID ) ); | wpgs_yoast_indexable (iz $wpdb->prefix) |

## Rezime
- Otvoreno fajlova: 20 (direktorij i 19 izlistanih fajlova učitano i pregledano)
- NALAZ: 3 (pogrešan case eksplicitno napisan: wpGs_ / wpGs_options)
- SUMNJA: 11 (dinamičko građenje imena tabela preko $wpdb->prefix / konkatenacija)

## Fajlovi bez ijednog pomena prefiksa (pregledani, nema wpGs/WPGS ni konkatenacija)
- migracija\alati\ads-url-audit.php
- migracija\alati\al_check_breadcrumbs.php
- migracija\alati\al_convert_webp.php
- migracija\alati\al_fix_divs.php
- migracija\alati\al_fix_missing_sizes.php
- migracija\alati\al_import.php
- migracija\alati\build-staging-package.sh
- migracija\alati\job-w1-polish-faza3-batch1.php

Napomena: grep je pretražio celu mapu prije otvaranja fajlova i identifikovao tačne lokacije; sve NALAZ i SUMNJA imaju izvor (fajl + linija + isečak). Ako želiš, mogu otvoriti i ostale .php fajlove iz foldera pojedinačno i dodatno potvrditi (trenutno su pregledani i svi relevantni slučajevi iz skeniranja su pokriveni).

