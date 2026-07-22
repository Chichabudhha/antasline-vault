---
tip: log
naziv: Bot/crawler praćenje (access log analiza)
status: aktivan
azurirano: 2026-07-23
---

# Bot/crawler log — antasline.com

Živi, append-only log za praćenje koji botovi/crawleri pristupaju sajtu, koliko
često, i (od 2026-07-23) da li ima efekta uvođenja `llms.txt`/`llms-full.txt`
na AI-asistent crawlere. Svaki novi presek se dodaje kao nova sekcija na dnu
(ne prepisuje stare).

## Metodologija
- Izvor: `~/access-logs/antasline.com-ssl_log` (live cPanel, `wp1.oblak.host`) — Apache/LiteSpeed combined log format
- ⚠️ Log se rotira (stari fajlovi arhivirani kao `old.antasline.com*`) — svaki presek pokriva samo prozor od poslednje rotacije, NE kumulativno. Uvek navesti tačan vremenski raspon preseka.
- Kategorizacija: regex po User-Agent stringu, grupisano u (1) AI asistent/LLM crawleri, (2) tradicionalni search engine botovi, (3) SEO-alat scraperi (Ahrefs/Moz/Semrush — ne pomažu saobraćaj/AI vidljivost, čisto konkurentska analitika), (4) ostalo/sumnjivo
- Da se ponovi provera: pogledati dole "Kako ponoviti" sekciju

---

## Presek #1 — 2026-07-23 (baseline, dan posle llms.txt/llms-full.txt deploy-a)

**Prozor:** 22/Jul/2026 10:54 — 23/Jul/2026 00:35 (~14h, log rotiran pre toga)
**Ukupno zahteva u prozoru:** 9.457
**Kategorisani bot zahtevi:** 352 (3,7% od ukupnog saobraćaja) — ostatak je real browser traffic (mobilni/desktop Chrome/Safari/Firefox varijante)

### AI asistent / LLM crawleri (GEO-relevantni, cilj je da ih PUSTIMO)

| Bot | Hitova | Prvi | Poslednji | Statusi | Trenutno blokiran? |
|---|---|---|---|---|---|
| meta-externalagent / meta-webindexer (Meta/Llama + FB link-preview) | 73 | 10:54 | 00:22 | 200×49, 301×6, 404×6, **429×12** | Ne (ali server ga povremeno rate-limituje, 12× 429) |
| ClaudeBot (Anthropic) | 20 | 12:09 | 00:27 | 200×18, 301×2 | Ne |
| ChatGPT-User (OpenAI, real-time browsing) | 8 | 11:25 | 20:44 | 200×7, 301×1 | Ne |
| YouBot (You.com) | 8 | 13:40 | 00:26 | 200×8 | Ne |
| OAI-SearchBot (OpenAI, ChatGPT Search) | 6 | 14:26 | 21:26 | 200×4, 301×1, 404×1 | Ne |
| Bravebot (Brave Search AI) | 1 | 13:17 | — | 200×1 | Ne |
| Amazonbot | 1 | 13:22 | — | 403×1 | **DA — server-level (Imunify360/LiteSpeed), potvrđeno curl testom, ne naš .htaccess** |
| PerplexityBot / Perplexity-User | 0 | — | — | — | nije viđen u ovom prozoru |
| GPTBot (OpenAI) | 0 | — | — | — | nije viđen u ovom prozoru (samo ChatGPT-User/OAI-SearchBot varijante) |
| CCBot (Common Crawl) | 0 | — | — | — | nije viđen |

### Tradicionalni search engine botovi (bitni za SEO, PUSTITI)

| Bot | Hitova | Statusi |
|---|---|---|
| bingbot | 75 | 200×67, 301×7, 404×1 |
| AdsBot-Google | 17 | 200×17 |
| Googlebot (core) | 15 | 200×8, 301×3, 404×4 |
| Googlebot-Image | 8 | 200×3, 304×5 |
| Baiduspider | 4 | 200×4 |
| DuckDuckBot | 3 | 200×3 |
| YandexBot | 3 | 200×2, 301×1 |
| Sogou spider | 2 | 200×1, 404×1 |

### SEO-alat scraperi (ne pomažu saobraćaj/AI vidljivost — čista konkurentska/backlink analitika)

| Bot | Hitova | Statusi | Trenutno blokiran? |
|---|---|---|---|
| **AhrefsBot** | **79** (najveći pojedinačni bot!) | 200×79 | Ne, potvrđeno curl testom |
| DotBot (Moz) | 13 | 200×13 | Ne |
| SemrushBot | 2 | 200×2 | Ne |

### Ostalo / poznato agresivno

| Bot | Hitova | Statusi | Trenutno blokiran? |
|---|---|---|---|
| facebookexternalhit / Twitterbot (social share preview, ne AI training) | 8 | 200×8 | Ne — treba ostati dozvoljeno, pravi link-preview kad neko deli link |
| Bytespider (ByteDance/TikTok) | 2 | **403×2** | **DA — server-level, potvrđeno curl testom** |
| TikTokSpider | 1 | 200×1 | Ne (ista firma kao Bytespider, ali ovaj prolazi) |
| qcbot (QUIC.cloud CDN, naša infrastruktura) | 1 | 200×1 | Ne — treba ostati dozvoljeno, ovo je naš CDN |

### llms.txt / llms-full.txt hitovi u ovom prozoru
**Nula organskih (trećestranih) hitova.** Svi zahtevi na `/llms.txt` i `/llms-full.txt` u ovom prozoru potiču od moje sopstvene curl verifikacije (IP servera) i verovatno Miroslavljevog browser-a (IP `150.228.61.138`, ponovljeni GET-ovi tačno prateći redosled mojih izmena tokom sesije — 861→1195→1873→2784 bajtova, poklapa se sa uzastopnim edit-ima). Nijedan poznat AI bot (ClaudeBot/ChatGPT-User/OAI-SearchBot/meta-externalagent/YouBot) još nije zatražio ova dva fajla, iako su SVI aktivno crawlovali sajt u istom prozoru — normalno, fajlovi su stari tek par sati, a `llms.txt` konvencija nije garantovano da je crawleri automatski traže bez linka/pomena u `robots.txt`. **Prati se u narednim presecima.**

### Nalazi i preporuka
1. **AhrefsBot je pojedinačno najveći bot na sajtu (79 hitova, više od bilo kog AI/search bota)** — ovo je komercijalni SEO alat (backlink/rank tracking), ne pomaže saobraćaj niti AI vidljivost. Ako AntasLine/agencija ne koristi Ahrefs pretplatu za sopstveni SEO rad, ima smisla blokirati preko `robots.txt` (`User-agent: AhrefsBot / Disallow: /`) — ovo je legitiman, poznat bot koji poštuje robots.txt, pa bi blok stvarno radio. **#ceka-miroslav: da li se koristi Ahrefs/Semrush/Moz za SEO rad?** Ako da, ne dirati (blokiranje bi onda oslepilo sopstveni alat).
2. **Bytespider (ByteDance) i Amazonbot su već blokirani na server nivou** (403, potvrđeno curl testom) — nije naš `.htaccess` (nema takvog pravila), verovatno Imunify360/hosting bad-bot lista. Nema akcije potrebne, već rešeno van naše kontrole.
3. **Meta crawler (73 hitova) je daleko najaktivniji AI-povezani bot** — dobar znak za Llama/Meta AI vidljivost, ali server ga 12× rate-limitovao (429) u ovih ~14h — ako se ovo pogorša (Meta poveća frekvenciju crawla), vredi proveriti Imunify360 rate-limit podešavanja da se ne izgubi legitiman crawl.
4. **GPTBot i PerplexityBot nisu viđeni u ovom prozoru** — ne znači da su blokirani (test pokazao 200 za ClaudeBot koji JESTE viđen), samo nisu prošli u ovih 14h. Pratiti u sledećem preseku.
5. **`llms.txt`/`llms-full.txt` efekat: još nema podataka** (0 organskih hitova) — prerano za zaključak, prvi fajl je star manje od 24h. Sledeći presek (preporuka: za ~1 nedelju) treba da pokaže da li se ijedan AI bot uopšte javio po ove fajlove.

## Kako ponoviti (sledeći presek)
```bash
cd ~/access-logs
grep -E "GET /llms(-full)?\.txt|HEAD /llms(-full)?\.txt" antasline.com-ssl_log   # da li je iko (osim nas) povukao fajlove
# Puna bot kategorizacija: python3 skripta iz DNEVNIK-NAPRETKA 2026-07-23 unosa (bot regex lista + line_re parser)
```
Dodati novi "Presek #N" na dno ovog fajla, ne prepisivati Presek #1.

## Veze
[[seo/geo-ai-plan]] · [[DNEVNIK-NAPRETKA]] · [[PROGRESS]]
