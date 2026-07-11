# QUIC.cloud support tiket — nacrt (kopiraj/nalepi u dashboard)

**Domain:** antasline.com
**Plugin:** LiteSpeed Cache 7.8.1 for WordPress
**Feature:** Image Optimization (queue stuck), dashboard shows: "QUIC.cloud
service nodes cannot reach your WP REST endpoints. Please check your
WordPress or firewall setup."

---

## Issue

Image Optimization queue has been stuck at exactly 200 images in
`REQUESTED` status for weeks, blocking all new optimization requests with
"Too many requested images". The dashboard attributes this to REST
endpoint unreachability, but our server-side evidence contradicts that
diagnosis:

**REST API IS reachable from QUIC.cloud** — access logs show successful
qcbot calls to other notify endpoints today:

```
95.216.116.209 - "POST /?rest_route=/litespeed/v1/notify_ccss" 200 (qcbot/1.0)
65.108.104.232 - "POST /?rest_route=/litespeed/v1/notify_ucss" 200 (qcbot/1.0)
```

Both returned HTTP 200. This proves your bots can reach and successfully
call our WP REST API right now.

**But `notify_img` has never been called** — we searched all available
access log history (back to ~2026-06-12) and found zero qcbot requests to
`notify_img` or `notify_vpi`, only our own manual test calls.

## Supporting data (from `wp_litespeed_img_optming` / plugin options)

- `need_pull` option stuck at `9` (STATUS_PULLED), never advances to `6`
  (STATUS_NOTIFIED)
- Queue: 200 images in status REQUESTED (optm_status=3), 1,157 waiting in
  RAW (optm_status=1) behind them
- `last_requested`: 2026-07-10 15:42 UTC (requests keep being sent)
- `last_pulled`: 2026-06-13 07:32 UTC (**no successful pull in ~4 weeks**
  despite continuous requests)
- Domain shows `qc_activated: "linked"` in plugin options — correctly
  linked to QUIC.cloud account
- We manually reset the stuck rows via `Img_Optm::reset_row()` on
  2026-07-05 and again 2026-07-10, and re-triggered `new_req()` — same 200
  cap re-fills and re-sticks every time

## Update — root cause identified, action in progress on our side

We found a specific error in the QUIC.cloud dashboard: "Unable to notify
WordPress to pick up images from node 54.36.103.97." We confirmed
54.36.103.97 IS on your official current IP list (`https://quic.cloud/ips`)
— but it has **never once hit our access logs**, while other nodes from
your list (e.g. 65.108.104.232, 95.216.116.209) succeed normally today.
This points to our hosting provider's server-level firewall (Imunify360)
blocking that specific IP/OVH range before it reaches our webserver — we
are opening a ticket with them to whitelist it.

## Ask

1. Could you confirm on your side whether node 54.36.103.97 is still the
   one assigned to our domain's image-optm queue, so we can verify the
   fix once our host whitelists it?
2. Separately: is the image optimization queue for this domain otherwise
   completing correctly on your end (aside from the notify-back issue)?
3. Why does `notify_img`/`notify_vpi` use a different node pool than
   `notify_ccss`/`notify_ucss` (which succeed for us) — is that expected?

Happy to provide additional logs/timestamps if useful.
