#!/usr/bin/env bash
###############################################################################
# live-export.sh  —  POKRENUTI NA cPANEL (LIVE), u WP root folderu
#
# Izvlaci SAMO proizvode, varijacije i kategorije proizvoda iz live baze.
# NE dira stranice, blog postove, korisnike, narudzbine.
# Live prefiks: wp_   (import skripta ce ga prebaciti na wpgs_ — MALIM slovima!)
#
# Rezultat: woo-export.sql  -> prebaci na staging (scp/rsync)
###############################################################################
set -euo pipefail

# Prefiks se moze pregaziti radi testiranja na lokalnom buildu:
#   PFX=wpgs_ OUT=/tmp/test.sql bash live-export.sh
PFX="${PFX:-wp_}"
OUT="${OUT:-woo-export.sql}"

# Omotac oko `wp db query`. Tri stvari, sve tri izmerene 2026-08-12 pri testiranju
# skripte na lokalnom buildu — bez njih skripta tiho radi pogresnu stvar:
#  - `\r` — WP-CLI na Windows-u vraca CRLF, pa `grep '^[0-9]+$'` ne pogodi nista
#    i sve liste ID-eva ispadnu prazne, bez ijedne greske
#  - prazan zavrsni red — `paste -sd, -` ga pretvori u zavrsni zarez, pa
#    `IN (1,2,)` puca sa „syntax error near ')'"
#  - SQL MORA biti u jednoj liniji — visellinijski upit kroz `wp db query` vraca
#    prazan rezultat sa exit kodom 0 (tih promasaj, najgora vrsta)
q() { wp db query "$1" --skip-column-names | sed 's/\r$//; /^[[:space:]]*$/d'; }

echo "==> Sakupljam ID-eve proizvoda i varijacija..."
PRODUCT_IDS=$(q "SELECT ID FROM ${PFX}posts WHERE post_type IN ('product','product_variation');" | paste -sd, -)

if [ -z "$PRODUCT_IDS" ]; then
  echo "!! Nema proizvoda u live bazi. Prekidam."
  exit 1
fi

echo "==> Sakupljam ID-eve attachmenta (slike proizvoda)..."
# (1) attachmenti vezani preko post_parent
ATTACH_IDS=$(q "SELECT DISTINCT p.ID FROM ${PFX}posts p WHERE p.post_type='attachment' AND p.post_parent IN (${PRODUCT_IDS});" | paste -sd, -)

# (2) glavne slike (proizvodi cesto nemaju post_parent na slici)
THUMB_IDS=$(q "SELECT DISTINCT meta_value FROM ${PFX}postmeta WHERE meta_key='_thumbnail_id' AND post_id IN (${PRODUCT_IDS}) AND meta_value REGEXP '^[0-9]+$';" | paste -sd, -)

# (3) GALERIJE — _product_image_gallery je zarezom razdvojena lista ID-eva.
# 🔴 Ovo je do 2026-08-12 nedostajalo: izmereno na lokalu, 145 od 170 galerijskih
# slika bi tiho nestalo iz exporta (bez ijedne greske) jer nemaju post_parent vezu.
GAL_IDS=$(q "SELECT meta_value FROM ${PFX}postmeta WHERE meta_key='_product_image_gallery' AND meta_value <> '' AND post_id IN (${PRODUCT_IDS});" \
  | tr ',' '\n' | grep -E '^[0-9]+$' | sort -un | paste -sd, - || true)

# (4) slike kategorija proizvoda (termmeta thumbnail_id) — takodje bez post_parent veze
CAT_THUMB_IDS=$(q "SELECT DISTINCT tm.meta_value FROM ${PFX}termmeta tm JOIN ${PFX}term_taxonomy tt ON tt.term_id = tm.term_id WHERE tm.meta_key='thumbnail_id' AND tt.taxonomy='product_cat' AND tm.meta_value REGEXP '^[0-9]+$' AND tm.meta_value <> '0';" | paste -sd, - || true)

# spoji sve attachment ID-eve (ukloni prazne)
ALL_ATTACH=$(echo "${ATTACH_IDS},${THUMB_IDS},${GAL_IDS},${CAT_THUMB_IDS}" \
  | tr ',' '\n' | grep -E '^[0-9]+$' | sort -un | paste -sd, - || true)

if [ -n "$ALL_ATTACH" ]; then
  ALL_POST_IDS="${PRODUCT_IDS},${ALL_ATTACH}"
else
  ALL_POST_IDS="${PRODUCT_IDS}"
fi

echo "==> Proizvodi+varijacije ID count: $(echo $PRODUCT_IDS | tr ',' '\n' | wc -l)"
echo "==> Attachment ID count: $(echo $ALL_ATTACH | tr ',' '\n' | grep -c . || true)"
echo "      od toga galerijskih: $(echo "$GAL_IDS" | tr ',' '\n' | grep -c . || true)"
echo "      od toga slika kategorija: $(echo "$CAT_THUMB_IDS" | tr ',' '\n' | grep -c . || true)"

# TVRDA PROVERA: svaki galerijski ID mora biti u ALL_ATTACH, inace prekid.
# Bolje da export pukne ovde nego da se otkrije posle migracije.
MISSING=$(comm -23 \
  <(echo "$GAL_IDS" | tr ',' '\n' | grep -E '^[0-9]+$' | sort -u) \
  <(echo "$ALL_ATTACH" | tr ',' '\n' | grep -E '^[0-9]+$' | sort -u) || true)
if [ -n "$MISSING" ]; then
  echo "!! GRESKA: galerijski attachmenti nisu usli u export:"; echo "$MISSING"
  exit 1
fi

# Konzistentan WHERE za posts/postmeta
POSTS_WHERE="ID IN (${ALL_POST_IDS})"
META_WHERE="post_id IN (${ALL_POST_IDS})"

# Taksonomije proizvoda
TAX_LIST="'product_cat','product_tag','product_shipping_class','product_type','product_visibility'"
# pa_* atributi su dinamicki - hvatamo ih posebno
PA_TAX=$(q "SELECT DISTINCT taxonomy FROM ${PFX}term_taxonomy WHERE taxonomy LIKE 'pa\_%';" | sed "s/.*/'&'/" | paste -sd, - || true)
if [ -n "$PA_TAX" ]; then
  TAX_LIST="${TAX_LIST},${PA_TAX}"
fi

echo "==> Taksonomije: ${TAX_LIST}"

# term_taxonomy ID-evi za te taksonomije
TT_IDS=$(q "SELECT term_taxonomy_id FROM ${PFX}term_taxonomy WHERE taxonomy IN (${TAX_LIST});" | paste -sd, -)
TERM_IDS=$(q "SELECT term_id FROM ${PFX}term_taxonomy WHERE taxonomy IN (${TAX_LIST});" | sort -un | paste -sd, -)

echo "==> Pisem dump: ${OUT}"
: > "$OUT"

DUMP="wp db export - --skip-add-drop-table --no-create-info=true"

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
  if q "SHOW TABLES LIKE '${PFX}termmeta';" | grep -q .; then
    eval $DUMP --tables=${PFX}termmeta --where="\"term_id IN (${TERM_IDS})\"" >> "$OUT"
  fi
fi

# 4) WooCommerce lookup tabele (ceo sadrzaj - regenerise se posle)
for T in wc_product_meta_lookup wc_category_lookup; do
  if q "SHOW TABLES LIKE '${PFX}${T}';" | grep -q .; then
    wp db export - --tables=${PFX}${T} --skip-add-drop-table --no-create-info=true >> "$OUT" 2>/dev/null || true
  fi
done

echo ""
echo "================================================"
echo " GOTOVO. Fajl: ${OUT} ($(du -h $OUT | cut -f1))"
echo " Prebaci na staging i pokreni staging-import.sh"
echo " Slike: zasebno rsync-uj wp-content/uploads/"
echo "================================================"
