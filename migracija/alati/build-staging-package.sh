#!/bin/bash
# build-staging-package.sh — pravi ciste pakete za staging.antasline.com refresh.
#
# Popravlja dva uzroka pada iz 2026-08-06 sesije (v. PROGRESS.md 2026-08-06
# "Staging refresh PONISTEN"):
#
#   1. "Kod" paket ranije bio tar CELE XAMPP fascikle (4.8GB smeca — stari
#      backup-*.sql, debug PHP skripte, .claude/, itd.), ne cist WP install.
#      Ovde se pakuje SAMO wp-admin/wp-includes/wp-content + root PHP/txt
#      fajlovi iz WP_ROOT, sa eksplicitnim exclude listom.
#
#   2. Uploads-diff je ranije filtriran PO IMENU FOLDERA (samo 2026/07 posle
#      21.07 + sav 2026/08) — WordPress cuva fajlove u folderu ORIGINALNOG
#      uploada, ne datuma izmene, pa je nova sadrzina u STARIM folderima
#      (webp regen, novi meni-ikonice poddirektorijum) bila promasena.
#      Ovde se koristi `find -newermt` preko CELOG uploads stabla, bez
#      filtriranja po imenu foldera.
#
# 🔴 VAZNA NAPOMENA (2026-08-06): staging docroot+baza su NAKON revert-a
# potpuno prazni (v. PROGRESS.md) — stari 07-21 sadrzaj vise NE POSTOJI na
# serveru. To znaci da je DIFF mod ("since") trenutno BESPREDMETAN — diff
# bi pretpostavio postojecu 07-21 osnovu na koju se lepi, a te osnove vise
# nema. Dok se staging ne vrati u neko poznato stanje, jedini ispravan mod
# za sledeci refresh je FULL. "since" mod je ovde ostavljen za BUDUCE
# inkrementalne refresh-eve (posle sledeceg punog postavljanja).
#
# Upotreba:
#   ./build-staging-package.sh full
#   ./build-staging-package.sh since "2026-07-21 00:00:00"

set -euo pipefail

# WP_ROOT/OUT_DIR su pregazivi preko okruzenja — isti obrazac kao PFX/OUT u
# live-export.sh (popravka 2026-08-12). Bez ovoga se dry-run ne moze pustiti
# van produkcione izlazne fascikle, pa se skripta u praksi nikad ne testira.
WP_ROOT="${WP_ROOT:-/c/xampp/htdocs/antasline}"
OUT_DIR="${OUT_DIR:-/c/xampp/htdocs/antasline-staging-upload}"
DATE_TAG=$(date +%Y-%m-%d)
CHUNK_SIZE=20m
MODE="${1:-}"
SINCE="${2:-}"

if [ "$MODE" != "full" ] && [ "$MODE" != "since" ]; then
  echo "Upotreba: $0 full           (pun paket)"
  echo "      ili: $0 since 'YYYY-MM-DD HH:MM:SS'   (SAMO za buduce inkrementalne refresh-eve)"
  exit 1
fi

mkdir -p "$OUT_DIR"
cd "$OUT_DIR"

echo "== 1. KOD paket (WP core+tema+plagini, BEZ uploads, BEZ smeca iz cele XAMPP fascikle) =="
CODE_TAR="antasline-wp-code-${DATE_TAG}.tar.gz"
# Root-level fajlovi: samo pravi WP core fajlovi + robots/llms/license — NE
# wp-config*.php (staging ima SVOJ, lokalni bi ga prepisao dev vrednostima -
# poznat gotcha iz 08-06 sesije) i NE lokalni debug/import/backup skripte
# (add-blocks-*.php, fix-*.php, import-*.php, restore-and-fix.php, "kopija"
# fajlovi itd. — ostaci lokalnog rada, nemaju posla na serveru, bezbednosni
# rizik ako ostanu javno dostupni).
#
# 🔴 W3 3.10 (2026-08-10) — dva exclude pravila dodata posle stvarnih nalaza:
#  1. `mu-plugins/al-local-mail-log.php` — lokalni mail logger PRESREĆE sve
#     mejlove. Otišao je na staging u V3 paketu 2026-08-07 i forme tamo nisu
#     stvarno slale ništa. Ostaje na lokalu (tamo je i dalje potreban), samo
#     više ne može da uđe u paket.
#  2. `*.bak-*` / `*.orig` / `*.old` / `*~` — izmereno 2026-08-10: 27 takvih
#     fajlova u `wp-content`, a Apache ih servira kao ČIST TEKST
#     (`functions.php.bak-…` → HTTP 200, 53KB PHP izvornog koda). Nema
#     kredencijala u njima, ali otkrivaju logiku court-builder tokena,
#     honeypota i rate-limita. Nemaju šta da traže na produkciji.
# 🔴 W3 3.10 dry-run (2026-08-13) — `.htaccess` IZBACEN iz whitelist-e.
# Lokalni `.htaccess` nosi `RewriteBase /antasline/` i `RewriteRule .
# /antasline/index.php` (build je u podfolderu) + lokalno-only RedirectMatch sa
# `/antasline/` prefiksom. Ako prepise `.htaccess` na serveru, SVAKI zahtev se
# prepisuje na nepostojeci `/antasline/index.php` — sajt pada u celosti, a uz
# to nestaje i produkcijski `# BEGIN LSCACHE` blok (LiteSpeed kesiranje).
# Checklist B3 ionako kaze da se 301 blok DODAJE u postojeci serverski
# `.htaccess` iznad `# BEGIN WordPress`, dakle fajl se na serveru edituje,
# nikad ne prenosi iz builda.
ROOT_WHITELIST=(index.php wp-activate.php wp-blog-header.php wp-comments-post.php
  wp-cron.php wp-links-opml.php wp-load.php wp-login.php wp-mail.php
  wp-settings.php wp-signup.php wp-trackback.php xmlrpc.php
  license.txt robots.txt llms.txt)
ROOT_FILES=()
for f in "${ROOT_WHITELIST[@]}"; do
  [ -f "$WP_ROOT/$f" ] && ROOT_FILES+=("$f")
done
tar -czf "$CODE_TAR" \
  -C "$WP_ROOT" \
  --exclude='wp-content/uploads' \
  --exclude='wp-content/cache' \
  --exclude='wp-content/mail-log.txt' \
  --exclude='wp-content/mu-plugins/al-local-mail-log.php' \
  --exclude='*.bak' \
  --exclude='*.bak-*' \
  --exclude='*.orig' \
  --exclude='*.old' \
  --exclude='*~' \
  --exclude='al-harness.html' \
  --exclude='.git' \
  --exclude='.claude' \
  --exclude='*.sql' \
  wp-admin wp-includes wp-content "${ROOT_FILES[@]}"
echo "Napravljeno: $CODE_TAR ($(du -h "$CODE_TAR" | cut -f1))"

echo "== 2. UPLOADS paket =="
if [ "$MODE" = "full" ]; then
  UP_TAR="antasline-uploads-${DATE_TAG}.tar.gz"
  echo "Pun uploads paket (staging je prazan posle revert-a, diff nema osnovu na koju bi se lepio)"
  tar -czf "$UP_TAR" -C "$WP_ROOT" wp-content/uploads
else
  UP_TAR="antasline-uploads-diff-${DATE_TAG}.tar.gz"
  echo "🟡 Diff mod pozvan eksplicitno — koristi se SAMO ako staging vec ima poznatu osnovu od $SINCE."
  FILELIST=$(mktemp)
  find "$WP_ROOT/wp-content/uploads" -type f -newermt "$SINCE" > "$FILELIST"
  COUNT=$(wc -l < "$FILELIST")
  echo "$COUNT fajlova izmenjeno/dodato posle $SINCE (pretraga CELOG uploads stabla, ne po imenu foldera)"
  if [ "$COUNT" -eq 0 ]; then
    echo "Nema fajlova za diff — proveri datum."
    rm -f "$FILELIST"
    exit 1
  fi
  tar -czf "$UP_TAR" -C "$WP_ROOT" -T <(sed "s|^$WP_ROOT/||" "$FILELIST")
  rm -f "$FILELIST"
fi
echo "Napravljeno: $UP_TAR ($(du -h "$UP_TAR" | cut -f1))"

echo "== 3. Chunk-uj oba paketa na ${CHUNK_SIZE} delove + md5 (isti obrazac kao ftp-upload-chunks.sh) =="
for f in "$CODE_TAR" "$UP_TAR"; do
  [ -f "$f" ] || continue
  BASE=$(basename "$f" .tar.gz)
  split -b "$CHUNK_SIZE" -d -a 3 "$f" "${f}.part-"
  md5sum "${f}.part-"* > "chunks-md5sums-${BASE}.txt"
  echo "$f -> $(ls "${f}.part-"* | wc -l) delova, md5 upisan u chunks-md5sums-${BASE}.txt"
done

echo ""
echo "GOTOVO. Paketi + delovi + md5 fajlovi su u $OUT_DIR."
echo "Sledece: proveri ukupnu velicinu ispod naspram trenutne FTP kvote pre uploada."
du -ch "$OUT_DIR"/antasline-*-"${DATE_TAG}".tar.gz | tail -1
