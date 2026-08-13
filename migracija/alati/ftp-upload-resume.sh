#!/bin/bash
# Robustan FTP upload sa auto-resume na svaki pad konekcije.
# Koristi curl -C - (REST) da nastavi tamo gde je stalo, umesto od nule.

# 🔴 Kredencijali su 2026-08-13 IZMESTENI VAN VAULT-A (do tada su stajali ovde
# u cistom tekstu i bili verzionisani u git-u od 06.08). Fajl van repozitorijuma:
#   ~/antasline-ftp-creds.txt   (pregazivo preko FTP_CREDS_FILE)
# Nikad ne vracati vrednost u ovaj fajl — vault se sinhronizuje na hosting.
CREDS_FILE="${FTP_CREDS_FILE:-$HOME/antasline-ftp-creds.txt}"
if [ ! -f "$CREDS_FILE" ]; then
  echo "GRESKA: nema kredencijala — ocekivan fajl: $CREDS_FILE" >&2
  echo "Sadrzaj treba da bude:  FTP_CREDS='korisnik:lozinka'" >&2
  exit 1
fi
. "$CREDS_FILE"
if [ -z "${FTP_CREDS:-}" ]; then
  echo "GRESKA: $CREDS_FILE ne definise FTP_CREDS." >&2
  exit 1
fi
CREDS="$FTP_CREDS"
HOST="${FTP_HOST:-antasline.com}"

LOCAL="${1:-/c/xampp/htdocs/antasline-staging-upload/antasline-wp-site-2026-08-06.tar.gz}"
REMOTE="ftp://$HOST/$(basename "$LOCAL")"
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
