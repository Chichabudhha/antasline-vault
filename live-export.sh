#!/usr/bin/env bash
###############################################################################
# live-export.sh  —  POKRENUTI NA cPANEL (LIVE), u WP root folderu
#
# Izvlaci SAMO proizvode, varijacije i kategorije proizvoda iz live baze.
# NE dira stranice, blog postove, korisnike, narudzbine.
# Live prefiks: wp_   (import skripta ce ga prebaciti na wpGs_)
#
# Rezultat: woo-export.sql  -> prebaci na staging (scp/rsync)
###############################################################################
set -euo pipefail

PFX="wp_"
OUT="woo-export.sql"

echo "==> Sakupljam ID-eve proizvoda i varijacija..."
PRODUCT_IDS=$(wp db query "SELECT ID FROM ${PFX}posts WHERE post_type IN ('product','product_variation');" --skip-column-names | paste -sd, -)

if [ -z "$PRODUCT_IDS" ]; then
  echo "!! Nema proizvoda u live bazi. Prekidam."
  exit 1
fi

echo "==> Sakupljam ID-eve attachmenta (slike proizvoda)..."
# thumbnail + galerija; uzimamo i parent-vezane attachmente
ATTACH_IDS=$(wp db query "
  SELECT DISTINCT p.ID FROM ${PFX}posts p
  WHERE p.post_type='attachment'
    AND p.post_parent IN (${PRODUCT_IDS});" --skip-column-names | paste -sd, -)

# meta thumbnaili (proizvodi cesto nemaju post_parent na slici)
THUMB_IDS=$(wp db query "
  SELECT DISTINCT meta_value FROM ${PFX}postmeta
  WHERE meta_key='_thumbnail_id'
    AND post_id IN (${PRODUCT_IDS})
    AND meta_value REGEXP '^[0-9]+$';" --skip-column-names | paste -sd, -)

# spoji sve attachment ID-eve (ukloni prazne)
ALL_ATTACH=$(echo "${ATTACH_IDS},${THUMB_IDS}" | tr ',' '\n' | grep -E '^[0-9]+$' | sort -un | paste -sd, -)

if [ -n "$ALL_ATTACH" ]; then
  ALL_POST_IDS="${PRODUCT_IDS},${ALL_ATTACH}"
else
  ALL_POST_IDS="${PRODUCT_IDS}"
fi

echo "==> Proizvodi+varijacije ID count: $(echo $PRODUCT_IDS | tr ',' '\n' | wc -l)"
echo "==> Attachment ID count: $(echo $ALL_ATTACH | tr ',' '\n' | grep -c . || true)"

# Konzistentan WHERE za posts/postmeta
POSTS_WHERE="ID IN (${ALL_POST_IDS})"
META_WHERE="post_id IN (${ALL_POST_IDS})"

# Taksonomije proizvoda
TAX_LIST="'product_cat','product_tag','product_shipping_class','product_type','product_visibility'"
# pa_* atributi su dinamicki - hvatamo ih posebno
PA_TAX=$(wp db query "SELECT DISTINCT taxonomy FROM ${PFX}term_taxonomy WHERE taxonomy LIKE 'pa\_%';" --skip-column-names | sed "s/.*/'&'/" | paste -sd, - || true)
if [ -n "$PA_TAX" ]; then
  TAX_LIST="${TAX_LIST},${PA_TAX}"
fi

echo "==> Taksonomije: ${TAX_LIST}"

# term_taxonomy ID-evi za te taksonomije
TT_IDS=$(wp db query "SELECT term_taxonomy_id FROM ${PFX}term_taxonomy WHERE taxonomy IN (${TAX_LIST});" --skip-column-names | paste -sd, -)
TERM_IDS=$(wp db query "SELECT term_id FROM ${PFX}term_taxonomy WHERE taxonomy IN (${TAX_LIST});" --skip-column-names | sort -un | paste -sd, -)

echo "==> Pisem dump: ${OUT}"
: > "$OUT"

DUMP="wp db export - --skip-add-drop-table --no-create-info"

# 1) posts (proizvodi, varijacije, attachmenti)
eval $DUMP --tables=${PFX}posts --where="\"${POSTS_WHERE}\"" >> "$OUT"
# 2) postmeta
eval $DUMP --tables=${PFX}postmeta --where="\"${META_WHERE}\"" >> "$OUT"
# 3) terms / term_taxonomy / term_relationships
if [ -n "$TERM_IDS" ]; then
  eval $DUMP --tables=${PFX}terms --where="\"term_id IN (${TERM_IDS})\"" >> "$OUT"
  eval $DUMP --tables=${PFX}term_taxonomy --where="\"taxonomy IN (${TAX_LIST})\"" >> "$OUT"
  eval $DUMP --tables=${PFX}term_relationships --where="\"object_id IN (${ALL_POST_IDS})\"" >> "$OUT"
  # termmeta (ako postoji - boje/slike kategorija)
  if wp db query "SHOW TABLES LIKE '${PFX}termmeta';" --skip-column-names | grep -q .; then
    eval $DUMP --tables=${PFX}termmeta --where="\"term_id IN (${TERM_IDS})\"" >> "$OUT"
  fi
fi

# 4) WooCommerce lookup tabele (ceo sadrzaj - regenerise se posle)
for T in wc_product_meta_lookup wc_category_lookup; do
  if wp db query "SHOW TABLES LIKE '${PFX}${T}';" --skip-column-names | grep -q .; then
    wp db export - --tables=${PFX}${T} --skip-add-drop-table --no-create-info >> "$OUT" 2>/dev/null || true
  fi
done

echo ""
echo "================================================"
echo " GOTOVO. Fajl: ${OUT} ($(du -h $OUT | cut -f1))"
echo " Prebaci na staging i pokreni staging-import.sh"
echo " Slike: zasebno rsync-uj wp-content/uploads/"
echo "================================================"
