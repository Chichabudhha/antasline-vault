#!/bin/bash
# Uploaduje delove (chunks) staging arhive preko FTP-a, sa retry po delu.
# Kontrolna FTP konekcija radi stabilno na trenutnoj mrezi, ali data-konekcija
# za velike/duge transfere pada posle ~15-20s (verovatno firewall/NAT na wifi-ju) -
# zato se salje po 50MB umesto jednog 3.18GB streama.

DIR="${2:-/c/xampp/htdocs/antasline-staging-upload/chunks}"
CREDS='staging@antasline.com:1~LI$$Ex&^gm~A2e'
PATTERN="${1:-antasline-wp-site-2026-08-06.tar.gz.part-*}"

cd "$DIR" || exit 1

for f in $PATTERN; do
  local_size=$(stat -c%s "$f")
  ok=0
  for attempt in 1 2 3 4 5 6 7 8 9 10; do
    remote_size=$(curl -s --connect-timeout 15 -u "$CREDS" -I "ftp://antasline.com/$f" 2>/dev/null | grep -i "Content-Length" | tr -dc '0-9')
    if [ -n "$remote_size" ] && [ "$remote_size" = "$local_size" ]; then
      echo "OK (vec gotov): $f"
      ok=1
      break
    fi
    echo "Upload $f (pokusaj $attempt, nastavlja od $remote_size/$local_size)..."
    curl -s --connect-timeout 20 --max-time 40 -u "$CREDS" -C - -T "$f" "ftp://antasline.com/$f" 2>&1 | tail -2
    remote_size=$(curl -s --connect-timeout 15 -u "$CREDS" -I "ftp://antasline.com/$f" 2>/dev/null | grep -i "Content-Length" | tr -dc '0-9')
    if [ -n "$remote_size" ] && [ "$remote_size" = "$local_size" ]; then
      echo "OK: $f ($remote_size bajtova)"
      ok=1
      break
    fi
    sleep 2
  done
  if [ "$ok" != "1" ]; then
    echo "GRESKA — $f nije uspeo posle 10 pokusaja, PREKIDAM."
    exit 1
  fi
done

echo "SVI DELOVI USPESNO PREBACENI."
