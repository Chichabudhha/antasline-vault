---
name: ollama-lokalni
description: Lokalna obrada velikih izlaza iz konektora (GSC upiti, Ads search terms, final URL-ovi) — razvrstavanje po AntasLine kategorijama i sazimanje u kratak izvestaj, da sirove liste od par stotina redova ne ulaze u Claude kontekst. Koristi kad Miroslav kaze "ollama", "lokalni model", "qwen", "razvrstaj upite", "stedi kvotu/tokene" ili kad izlaz konektora prelazi ~100 redova. NE koristi za odluke o budzetu, licitiranju, GTM-u ni bilo sta nepovratno.
---

# `ollama-lokalni` — obrada podataka pre nego sto udju u kontekst

Sestrinski skill uz `[[agy-delegat]]`. Razlika je bitna:

| | `agy-delegat` (Gemini) | `ollama-lokalni` | 
|---|---|---|
| Gde radi | Google cloud, mala besplatna kvota | ova masina, bez kvote |
| Sta mu ide dobro | citanje **mnogo fajlova**, trazenje protivrecnosti | **sazimanje brojki** iz jednog JSON-a |
| Ogranicenje | trosi Google kvotu | trosi ~8 min CPU vremena po pozivu |

---

## 1. 🔴 Glavni nalaz — usteda NE dolazi od modela

Izmereno 2026-08-12 na pravom GSC izvozu (400 upita, 31 dan, 37 KB):

| Pristup | Pokriveno (% prikaza) | Vreme | Pouzdanost |
|---|---:|---:|---|
| **Pravila (regex) — podrazumevano** | **93,6 %** | **0,2 s** | ponovljivo 1:1 |
| qwen3:4b na ostatku | +2 % | 475 s | ~50 % gresaka na spornim |
| qwen3:8b | — | >10 min / poziv | neupotrebljivo sporo |

**Usteda tokena dolazi od agregacije, ne od LLM-a.** Skripta pretvori 37 KB
sirovog JSON-a (~12k tokena) u sazetak od ~2 KB (~700 tokena) — to je ~94 %
manje, potpuno besplatno i bez ijednog poziva modela.

Zato: **podrazumevano se vrti BEZ modela.** Model je opcioni dodatak, ne motor.

## 2. Hardver — sta ova masina stvarno moze

i5-11320H · 15,7 GB RAM · MX450 sa 2 GB (prakticno bez offload-a → sve na CPU).

| Model | Stanje |
|---|---|
| `qwen3:4b` (2,5 GB) | ✅ radi, ~15 tok/s — jedini prakticno upotrebljiv |
| `qwen3:8b` (5,2 GB) | ⚠️ radi ali >10 min po pozivu — nema smisla |
| `qwen3:30b` (18 GB) | ❌ **ne staje u RAM**, Ollama odbija da ga ucita |
| `llama3.2:3b`, `gemma3:1b` | ✅ brzi, ali preslabi za srpski |

🔴 **Gotcha #1 — `num_ctx`.** Ollama 0.18 sam bira kontekst iz modelovog
maksimuma (qwen3 = 262k), pa i model od 3B trazi ~15 GiB i pukne sa
`model requires more system memory (15.3 GiB)`. Izgleda kao da model ne staje
— a staje. `lokalni_llm.py` fiksira `num_ctx=8192`; nikad ne zvati Ollama API
direktno bez toga.

🔴 **Gotcha #2 — `num_predict`.** Odsecen izlaz = neispravan JSON = ceo poziv
je baceno vreme. qwen3:4b probije 2048 tokena vec na 60 upita. Sada je 4096.

🔴 **Gotcha #3 — duplo enkodiranje.** `python skripta.py | Out-File -Encoding utf8`
u Windows PowerShell-u dekodira Python-ov UTF-8 kao cp1252 pa ga opet enkoduje —
cirilicni upiti postanu `ĐşĐľŃĐ°Ń€`. Uvek pre pokretanja:
```powershell
$env:PYTHONIOENCODING="utf-8"
[Console]::OutputEncoding=[System.Text.Encoding]::UTF8
```
i `Set-Content -Encoding utf8` umesto `Out-File`.

⚠️ Python koji pise na `stderr` PowerShell prikaze kao `NativeCommandError`
crvenim slovima iako je izlaz 0. To nije greska — dodati `2>$null` ako smeta.

## 3. Kako se pokrece

```powershell
$env:PYTHONIOENCODING="utf-8"; [Console]::OutputEncoding=[System.Text.Encoding]::UTF8
$S = "C:\Projekti\antasline-vault\.claude\skills"

# 1) povuci sirove podatke konektorom (v. [[antasline-konektor]])
cd C:\Users\Miroslav\antasline-connector
venv\Scripts\python.exe "$S\antasline-konektor\scripts\gsc_report.py" `
  --from 2026-07-10 --to 2026-08-09 --limit 400 | Set-Content sirovo.json -Encoding utf8

# 2) razvrstaj i sazmi — SAMO ovaj izlaz ide u Claude kontekst
python "$S\ollama-lokalni\scripts\klasifikuj_upite.py" `
  --ulaz sirovo.json --izlaz razvrstano.json
```

`--izlaz` je arhiva punog razvrstavanja na disku. **Ne cita se ceo** — postoji
da se pojedinacna tvrdnja moze proveriti kad zatreba.

### Zastave

| Zastava | Kada |
|---|---|
| *(bez zastave)* | podrazumevano — samo pravila, 0,2 s. **Ovo koristis u 95 % slucajeva** |
| `--predlozi-pravila` | jednom u par meseci: model procesljá ostatak i predlozi nove korene reci. **Preporuceno koriscenje modela** — predlog se upise u `KATEGORIJE` i od tada je besplatno pravilo zauvek |
| `--llm-klasifikuj` | gruba slika kad ostatak naraste a nemas vremena da pises pravila. Spor i gresi — svaki takav red je u izlazu obelezen kao LLM |
| `--polje NAZIV` | za Ads search terms / druge izvore gde se polje ne zove `query` |

## 4. Kategorije — zive u kodu, ne u promptu

`KATEGORIJE` u `klasifikuj_upite.py` su uredjena lista; **prvi pogodak
pobedjuje**, pa je redosled deo logike:

1. `epoksid-conquest` — ide **prvi** namerno. „epoksidni podovi za terase" je
   conquest upit, ne upit za terase (CLAUDE.md §1: epoksid je kvalifikovana
   traznja, **nikad „smece"**). Koreni `epoks` + `epox` pokrivaju i pogresno
   kucanje (`epoxi`, `epoxidni`, `epox pod`).
2. `esd-antistatik` · 3. `sport` · 4. `ecotile-industrijski` ·
   5. `spolja-terasa` · 6. `lvt-vinil-trava`
7. `gumene-podloge` — **poslednja i namerno odvojena**. Guma se prodaje i za
   sport i za terase; umesto da je pravilo tiho gurne u pogresnu korpu, dobija
   svoju pa je vidljiva.

**Sudari kojih se cuvati pri dodavanju korena:**
- `odbojn` bi pojeo i **odbojku** (sport) i **odbojnike za zid** (industrija) —
  zato je koren `odbojnic`, a `odbojk` ostaje u sportu.
- `kos` sam za sebe hvata „kosa", „kosara" — zato `kosark`, `kos sa`, `za kos`,
  `kos konstrukcij`, a ne goli `kos`.

**Cirilica se transliterise** pre poredjenja — GSC vraca i „кошаркашки терен".
Bez toga takvi upiti tiho zavrse u „ostalo" (bilo ih je 9+ u julskom izvozu).

## 5. Sta se NE delegira lokalnom modelu

Isto kao kod `agy`-ja, plus jedno vise:

- ❌ Odluke o budzetu, licitiranju, strategiji
- ❌ GTM tagovi/trigeri, GA4 key eventi, baza, `.htaccess`, dan migracije
- ❌ **Bilo koja tvrdnja koja ide u izvestaj Miroslavu bez provere.** Model od
  4B na srpskom je 2026-08-12 svrstao „table za kos" u vinil a „tartan kocke"
  u vestacku travu. Sve sto dolazi iz `--llm-klasifikuj` je **predlog**, i
  izlaz to eksplicitno obelezava.

## 6. Odrzavanje

Kad `ostalo` predje ~10 % prikaza — to je signal da fale pravila, ne da treba
jaci model. Pokrenuti `--predlozi-pravila`, procitati predloge, dopisati
korene u `KATEGORIJE`, pa proveriti da nista nije regresiralo:

```powershell
python "$S\ollama-lokalni\scripts\klasifikuj_upite.py" --ulaz sirovo.json --izlaz p.json
# pa pogledati da li je neka kategorija neocekivano skocila
```

## Veze
- `[[antasline-konektor]]` — odakle dolaze sirovi podaci
- `[[agy-delegat]]` — delegat za masovno citanje **fajlova** (Gemini)
- `[[nedeljni-izvestaj]]` — glavni potrosac ovog sazetka
- `reference/naucene-lekcije.md` — gotcha-i gore pripadaju i tamo
