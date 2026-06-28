#!/usr/bin/env bash
###############################################################################
# staging-import.sh  —  POKRENUTI NA LOKALU (STAGING), u WP root folderu
#                       cd C:\xampp\htdocs\antasline
#
# 1) Backup (ponovo, za svaki slucaj)
# 2) Brise zaostale staging proizvode/varijacije/attachmente
# 3) Prefiks rewrite wp_ -> wpGs_ u dumpu
# 4) Import
# 5) Flat /proizvod/ permalink + regen lookup + flush
#
# PREDUSLOV: woo-export.sql u istom folderu; uploads/ vec rsync-ovan.
###############################################################################
set -euo pipefail

SRC="woo-export.sql"
REWRITTEN="woo-export-wpGs.sql"
LIVE_PFX="wp_"
STG_PFX="wpGs_"

if [ ! -f "$SRC" ]; then
  echo "!! Nema $SRC u ovom folderu. Prekidam."
  exit 1
fi

echo "==> [1/6] Backup staging baze..."
wp db export "backup-pre-woo-import-$(date +%Y%m%d-%H%M).sql"

echo "==> [2/6] Brisem zaostale staging proizvode i njihove attachmente..."
# attachmenti vezani za proizvode
STALE_ATTACH=$(wp db query "
  SELECT DISTINCT p.ID FROM ${STG_PFX}posts p
  WHERE p.post_type='attachment'
    AND p.post_parent IN (
      SELECT ID FROM (SELECT ID FROM ${STG_PFX}posts WHERE post_type IN ('product','product_variation')) t
    );" --skip-column-names | paste -sd, - || true)

# obrisi proizvode/varijacije (postmeta i term_relationships ciscenje posle)
wp db query "DELETE FROM ${STG_PFX}posts WHERE post_type IN ('product','product_variation');"
if [ -n "$STALE_ATTACH" ]; then
  wp db query "DELETE FROM ${STG_PFX}posts WHERE ID IN (${STALE_ATTACH});"
fi
# orphan postmeta i term_relationships
wp db query "DELETE pm FROM ${STG_PFX}postmeta pm LEFT JOIN ${STG_PFX}posts p ON pm.post_id=p.ID WHERE p.ID IS NULL;"
wp db query "DELETE tr FROM ${STG_PFX}term_relationships tr LEFT JOIN ${STG_PFX}posts p ON tr.object_id=p.ID WHERE p.ID IS NULL;"

echo "==> [3/6] Prefiks rewrite ${LIVE_PFX} -> ${STG_PFX}..."
# Menja SAMO INSERT INTO `wp_tabela` i tabela imena na pocetku reda.
# Bezbedno jer dump sadrzi samo nase ciljane tabele.
sed -E "s/\`${LIVE_PFX}(posts|postmeta|terms|term_taxonomy|term_relationships|termmeta|wc_product_meta_lookup|wc_category_lookup)\`/\`${STG_PFX}\1\`/g" "$SRC" > "$REWRITTEN"

echo "==> [4/6] Import dumpa..."
wp db import "$REWRITTEN"

echo "==> [5/6] Flat permalink struktura /proizvod/ ..."
# WooCommerce permalink baze: product_base = proizvod, category_base = kategorija-proizvoda
wp option patch update woocommerce_permalinks product_base "/proizvod" 2>/dev/null || \
  wp option update woocommerce_permalinks '{"product_base":"/proizvod","category_base":"kategorija-proizvoda","tag_base":"","attribute_base":"","use_verbose_page_rules":false}' --format=json

echo "==> [6/6] Regen lookup tabela + flush rewrite + recount..."
wp wc tool run regenerate_product_lookup_tables --user=1 2>/dev/null || echo "   (preskoceno: wc tool nije dostupan, lookup je vec importovan)"
wp rewrite flush --hard
wp wc tool run recount_terms --user=1 2>/dev/null || true

echo ""
echo "================================================"
echo " IMPORT GOTOV."
echo "   Proizvoda na stagingu: $(wp post list --post_type=product --format=count)"
echo "   Kategorija: $(wp term list product_cat --format=count)"
echo ""
echo " PROVERI:"
echo "   1. http://localhost/antasline/proizvod/<slug>/ (200, ne 404)"
echo "   2. Slike se prikazuju (uploads/ rsync OK?)"
echo "   3. Cene i SKU vidljivi na proizvodu"
echo "================================================"
