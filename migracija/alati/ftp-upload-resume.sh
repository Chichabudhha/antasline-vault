#!/bin/bash
# Robustan FTP upload sa auto-resume na svaki pad konekcije.
# Koristi curl -C - (REST) da nastavi tamo gde je stalo, umesto od nule.

LOCAL="/c/xampp/htdocs/antasline-staging-upload/antasline-wp-site-2026-08-06.tar.gz"
REMOTE="ftp://antasline.com/antasline-wp-site-2026-08-06.tar.gz"
CREDS='staging@antasline.com:1~LI$$Ex&^gm~A2e'
LOCAL_SIZE=$(stat -c%s "$LOCAL")

attempt=0
while true; do
  attempt=$((attempt+1))
  REMOTE_SIZE=$(curl -s --connect-timeout 15 -u "$CREDS" -I "$REMOTE" 2>/dev/null | grep -i "Content-Length" | tr -dc '0-9')
  if [ -z "$REMOTE_SIZE" ]; then REMOTE_SIZE=0; fi
  echo "=== Pokusaj $attempt === Remote: $REMOTE_SIZE / Local: $LOCAL_SIZE bajtova"
  if [ "$REMOTE_SIZE" -ge "$LOCAL_SIZE" ]; then
    echo "ZAVRSENO: remote size >= local size"
    break
  fi
  curl --connect-timeout 20 --speed-limit 1 --speed-time 60 -u "$CREDS" -C - -T "$LOCAL" "$REMOTE" 2>&1 | tail -3
  echo "--- pokusaj $attempt zavrsen sa exit $?, proveravam da li treba jos ---"
  sleep 3
  if [ $attempt -gt 100 ]; then
    echo "PREVISE POKUSAJA (100), prekidam."
    break
  fi
done
