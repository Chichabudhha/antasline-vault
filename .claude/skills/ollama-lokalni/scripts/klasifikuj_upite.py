#!/usr/bin/env python
"""Razvrstavanje GSC/Ads upita u AntasLine kategorije — hibridno: pravila + lokalni LLM.

Ulaz:  sirov JSON iz `antasline-konektor` (gsc_report.py / ads_report.py) ili
       bilo koji JSON sa listom objekata koji imaju polje sa tekstom upita.
Izlaz: (a) pun razvrstan JSON na disk — arhiva, niko ga ne cita ceo
       (b) kratak markdown sazetak na stdout — SAMO to ide u Claude kontekst

Zasto hibrid, a ne cist LLM: model od 4B na srpskom gresi na nijansama
(izmereno na ovom nalogu). Zato deterministicka pravila prvo pokupe ono sto
je nedvosmisleno (~85% prikaza), a lokalni model dobija samo ostatak — i to
kao predlog koji se u izlazu jasno obelezava kao "LLM", ne kao cinjenica.

Primer:
    python klasifikuj_upite.py --ulaz gsc_raw.json --izlaz razvrstano.json
    python klasifikuj_upite.py --ulaz gsc_raw.json --bez-llm   # samo pravila
"""

from __future__ import annotations

import argparse
import json
import sys
import unicodedata
from collections import defaultdict
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
from lokalni_llm import DEFAULT_MODEL, OllamaGreska, chat_json  # noqa: E402

# --- Kategorije -------------------------------------------------------------
# Redosled JE pravilo: prvi pogodak pobedjuje. Epoksid ide PRVI namerno —
# "epoksidni podovi za terase" je conquest upit, ne upit za terase
# (v. CLAUDE.md §1: epoksid = kvalifikovana traznja, nikad "smece").
KATEGORIJE: list[tuple[str, tuple[str, ...]]] = [
    # "epoks" pokriva epoksid/epoksidni/epoksi, "epox" pokriva epoxy/epoxi/
    # epoxidni/epox — pogresno kucanje je ovde pravilo, ne izuzetak.
    # Konkurentski brendovi idu PRE svega — "lidl podne obloge" nije upit za
    # podne obloge, nego za Lidl. Zasebna korpa jer je to trzisna informacija,
    # ne kategorija proizvoda.
    ("konkurencija-brend", (
        "dunk shop", "dunkshop", "lidl", "slajs", "amicus", "mosolut shop",
    )),
    # "tecni/polimerni/izliveni/3d/samorazlivajuci pod" su sve nazivi za liveni
    # pod — ista conquest traznja kao epoksid, samo drugim recima.
    ("epoksid-conquest", (
        "epoks", "epox", "tecni pod", "tecnih pod", "polimern", "izliven",
        "3d pod", "samorazliv",
    )),
    ("esd-antistatik", ("esd", "antistatik", "antistaticn", "elektrostat", "provodn")),
    ("sport", (
        "kosark", "basket", "3x3", "tenis", "padel", "piklbol", "pickleball",
        "odbojk", "sportsk", "teren", "sljak", "kos sa", "kos za", "za kos",
        "kosev", "kos konstrukcij", "montazni kos", "igralist", "rukomet",
        "fudbal", "gimnastick", "teretan", "tartan", "baletsk", "us open",
        "stadion",
    )),
    ("ecotile-industrijski", (
        "ecotile", "industrijsk", "industrisk", "radionic", "garaz", "magacin",
        "hala", "pvc ploc", "podne ploc", "pvc kock", "mosolut", "durastripe",
        "viljuskar", "skladist", "proizvodn",
        # "odbojnic", ne "odbojnik" — GSC vraca "odbojnici za zidove";
        # "odbojnik" ne hvata mnozinu. Ne sme biti "odbojn" — to bi pojelo
        # odbojku (sport), koja je gore u redosledu ali ne uvek.
        "odbojnic", "plasticn", "plastika za pod", "pvc trak",
        "traka za obele", "trake za obele",
        # modularne / puzzle / multifunkcionalne ploce = Ecotile jezgro ponude
        "modularn", "puzzle", "multifunkcionaln",
        # "kombi vozil", NE goli "kombi" — "kombinacija parketa i plocica"
        # sadrzi "kombi" i zavrsila bi ovde
        "kombi vozil", "za kombi", "zastita za kabl", "kabl na podu", "bumper",
        "kontejner",
    )),
    ("spolja-terasa", (
        "teras", "dvorist", "bergo", "parkiral", "parking", "bazen", "spoljn",
        "balkon", "staz", "trotoar", "vrt", "bast", "sljunak", "asfalt",
    )),
    ("lvt-vinil-trava", (
        "lvt", "expona", "vinil", "tepih", "linoleum", "laminat", "marmoleum",
        # "trav" pokriva travu/travnjak/travnatu — vestacka trava je zasebna
        # stavka u ponudi, a ovde nema sudara ni sa jednom drugom kategorijom
        "trav",
        # renoviranje preko postojeceg poda: ciljano fraze, NE goli "plocic" —
        # "gumene plocice za pod" bi tako zavrsile u vinilu
        "preko plocic", "preko ploc", "preko parket", "parketa i ploc",
        "plocica i parket", "imitacija parket",
    )),
    # Namerno POSLEDNJI i namerno odvojen: guma se kod nas prodaje i za sport
    # i za terase. Umesto da ih pravilo tiho gurne u pogresnu korpu, dobijaju
    # svoju — vidljivi su, pa se rucno presude ako ih bude mnogo.
    # "gumiran", ne "gumirani" — GSC vraca i "gumirana podloga" (zenski rod)
    ("gumene-podloge", ("gumen", "guma za pod", "gume za pod", "gumiran")),
]

VALIDNE = [k for k, _ in KATEGORIJE] + ["ostalo"]

# Namera — nezavisna od kategorije, cisto po pravilima.
NAMERA_KOMERCIJALNA = ("cena", "cijena", "kosta", "kupovina", "kupiti", "prodaja",
                       "po m2", "cenovnik", "ponuda", "akcij")
NAMERA_INFORMATIVNA = ("kako", "sta je", "koji", "koja", "zasto", "razlika",
                       "najbolj", "iskustv", "recenzij")


# GSC vraca i cirilicne upite ("кошаркашки терен") — bez ovoga ih nijedno
# pravilo ne vidi i svi tiho zavrse u "ostalo".
CIRILICA = {
    "а": "a", "б": "b", "в": "v", "г": "g", "д": "d", "ђ": "dj", "е": "e",
    "ж": "z", "з": "z", "и": "i", "ј": "j", "к": "k", "л": "l", "љ": "lj",
    "м": "m", "н": "n", "њ": "nj", "о": "o", "п": "p", "р": "r", "с": "s",
    "т": "t", "ћ": "c", "у": "u", "ф": "f", "х": "h", "ц": "c", "ч": "c",
    "џ": "dz", "ш": "s",
}
LATINICA = {"đ": "dj", "š": "s", "č": "c", "ć": "c", "ž": "z"}


def bez_dijakritike(s: str) -> str:
    s = s.lower()
    s = "".join(CIRILICA.get(z, LATINICA.get(z, z)) for z in s)
    return unicodedata.normalize("NFKD", s).encode("ascii", "ignore").decode()


def po_pravilima(upit: str) -> str | None:
    n = bez_dijakritike(upit)
    for naziv, kljucne in KATEGORIJE:
        if any(k in n for k in kljucne):
            return naziv
    return None


def namera(upit: str) -> str:
    n = bez_dijakritike(upit)
    if any(k in n for k in NAMERA_KOMERCIJALNA):
        return "komercijalna"
    if any(k in n for k in NAMERA_INFORMATIVNA):
        return "informativna"
    return "navigaciona/opsta"


# --- LLM sloj za ostatak ----------------------------------------------------

SISTEM = (
    "Ti si klasifikator upita sa pretrage za firmu AntasLine (Srbija). "
    "AntasLine prodaje: industrijske i antistatik (ESD) podove, Ecotile/PVC "
    "industrijske ploce, sportske terene i podloge, gumene i PVC podloge za "
    "terase i dvorista, Bergo modularne podloge, LVT/Expona, vestacku travu. "
    "AntasLine NE prodaje epoksid, ali epoksid upite namerno cilja kao "
    "conquest — zato epoksid NIJE 'ostalo'. "
    "Svaki upit svrstaj u tacno jednu kategoriju. Ako upit nema veze ni sa "
    "jednom, koristi 'ostalo'. Ne objasnjavaj, samo klasifikuj."
)

SCHEMA = {
    "type": "object",
    "properties": {
        "rezultati": {
            "type": "array",
            "items": {
                "type": "object",
                "properties": {
                    "upit": {"type": "string"},
                    "kategorija": {"type": "string", "enum": VALIDNE},
                },
                "required": ["upit", "kategorija"],
            },
        }
    },
    "required": ["rezultati"],
}


def llm_ostatak(upiti: list[str], model: str, velicina_grupe: int = 15) -> dict[str, str]:
    """Vrati {upit: kategorija}. Greska u jednoj grupi ne rusi ceo posao."""
    mapa: dict[str, str] = {}
    grupe = [upiti[i:i + velicina_grupe] for i in range(0, len(upiti), velicina_grupe)]
    for i, grupa in enumerate(grupe, 1):
        spisak = "\n".join(f"- {u}" for u in grupa)
        print(f"  LLM grupa {i}/{len(grupe)} ({len(grupa)} upita)...", file=sys.stderr)
        try:
            odg = chat_json(
                SISTEM,
                f"Klasifikuj svaki od ovih {len(grupa)} upita:\n{spisak}",
                SCHEMA,
                model=model,
            )
        except OllamaGreska as e:
            print(f"  ! grupa {i} pala: {e}", file=sys.stderr)
            continue
        for r in odg.get("rezultati", []):
            if r.get("kategorija") in VALIDNE:
                mapa[r["upit"]] = r["kategorija"]
    return mapa


# --- Rudarenje pravila ------------------------------------------------------
# Ovo je STVARNA korist lokalnog modela na ovoj masini: ne da klasifikuje svaki
# izvestaj iznova (sporo i gresi), nego da JEDNOM prodje kroz ostatak i predlozi
# nove kljucne reci. Predlog cita covek, doda ga u KATEGORIJE gore, i od tada je
# to pravilo — trenutno, besplatno i ponovljivo zauvek.

SISTEM_PRAVILA = (
    SISTEM + " Tvoj zadatak sada NIJE da klasifikujes pojedinacne upite, nego "
    "da predlozis KRATKE korene reci (2-8 slova, bez dijakritike, mala slova) "
    "koji bi ubuduce automatski hvatali ove upite. Koren mora biti dovoljno "
    "specifican da ne hvata upite iz drugih kategorija."
)

SCHEMA_PRAVILA = {
    "type": "object",
    "properties": {
        "predlozi": {
            "type": "array",
            "items": {
                "type": "object",
                "properties": {
                    "koren": {"type": "string"},
                    "kategorija": {"type": "string", "enum": VALIDNE},
                    "primeri": {"type": "array", "items": {"type": "string"}},
                },
                "required": ["koren", "kategorija", "primeri"],
            },
        }
    },
    "required": ["predlozi"],
}


def rudari_pravila(redovi: list[dict], model: str) -> str:
    ostatak = sorted(
        (r for r in redovi if r["kategorija"] == "ostalo"),
        key=lambda r: -r["impressions"],
    )[:60]
    if not ostatak:
        return "\n> Nema nerazvrstanih upita — nema sta da se predlozi."

    spisak = "\n".join(f"- {r['query']} ({r['impressions']} prikaza)" for r in ostatak)
    print(f"  Rudarim pravila iz {len(ostatak)} nerazvrstanih upita ({model})...",
          file=sys.stderr)
    try:
        odg = chat_json(
            SISTEM_PRAVILA,
            "Ovi upiti nisu uhvaceni nijednim pravilom. Predlozi korene reci "
            f"koji bi ih pokrili. Ako upit stvarno nema veze sa ponudom, "
            f"preskoci ga.\n{spisak}",
            SCHEMA_PRAVILA,
            model=model,
        )
    except OllamaGreska as e:
        return f"\n> Rudarenje pravila nije uspelo: {e}"

    linije = ["", "## Predlozi novih pravila (lokalni LLM — PROVERI pre unosa)", ""]
    linije.append("| Koren | Kategorija | Primeri |")
    linije.append("|---|---|---|")
    for p in odg.get("predlozi", []):
        primeri = ", ".join(p.get("primeri", [])[:3])
        linije.append(f"| `{p['koren']}` | {p['kategorija']} | {primeri} |")
    linije.append("")
    linije.append(
        "> Predlog nije pravilo. Pre unosa u `KATEGORIJE` proveri da koren ne "
        "hvata tudje upite (klasican sudar: `odbojn` pojede i odbojku i odbojnike)."
    )
    return "\n".join(linije)


# --- Agregacija -------------------------------------------------------------

def sazetak(redovi: list[dict]) -> str:
    grupe: dict[str, list[dict]] = defaultdict(list)
    for r in redovi:
        grupe[r["kategorija"]].append(r)

    ukupno_prikaza = sum(r["impressions"] for r in redovi) or 1
    linije = [
        f"# Razvrstani upiti — {len(redovi)} upita, {ukupno_prikaza} prikaza",
        "",
        "| Kategorija | Upita | Prikazi | % prikaza | Klikovi | CTR % | Pros. poz. | Po pravilima |",
        "|---|---:|---:|---:|---:|---:|---:|---:|",
    ]
    for kat in sorted(grupe, key=lambda k: -sum(r["impressions"] for r in grupe[k])):
        g = grupe[kat]
        pr = sum(r["impressions"] for r in g)
        kl = sum(r["clicks"] for r in g)
        poz = sum(r["position"] * r["impressions"] for r in g) / (pr or 1)
        pravila = sum(1 for r in g if r["izvor"] == "pravilo")
        linije.append(
            f"| {kat} | {len(g)} | {pr} | {100 * pr / ukupno_prikaza:.1f} | {kl} | "
            f"{100 * kl / (pr or 1):.2f} | {poz:.1f} | {pravila}/{len(g)} |"
        )

    linije += ["", "## Top 5 upita po kategoriji (prikazi / klikovi / pozicija)", ""]
    for kat in sorted(grupe, key=lambda k: -sum(r["impressions"] for r in grupe[k])):
        top = sorted(grupe[kat], key=lambda r: -r["impressions"])[:5]
        stavke = " · ".join(
            f"{r['query']} ({r['impressions']}/{r['clicks']}/{r['position']:.1f})"
            for r in top
        )
        linije.append(f"- **{kat}**: {stavke}")

    prilike = sorted(
        (r for r in redovi if r["impressions"] >= 25 and r["clicks"] == 0
         and 5 <= r["position"] <= 15),
        key=lambda r: -r["impressions"],
    )[:15]
    if prilike:
        linije += ["", "## Prilike: ≥25 prikaza, 0 klikova, pozicija 5–15", ""]
        linije += [
            f"- {r['query']} — {r['impressions']} prikaza, poz. {r['position']:.1f} "
            f"[{r['kategorija']}, {r['namera']}]"
            for r in prilike
        ]

    nesigurni = [r for r in redovi if r["izvor"] == "llm"]
    if nesigurni:
        linije += [
            "",
            f"> ⚠️ {len(nesigurni)} upita svrstao lokalni LLM (ne pravila) — "
            "predlog, ne cinjenica. Proveri pre nego sto na tome doneses odluku.",
        ]
    return "\n".join(linije)


def main() -> None:
    p = argparse.ArgumentParser()
    p.add_argument("--ulaz", required=True, help="JSON iz konektora")
    p.add_argument("--izlaz", help="gde upisati pun razvrstan JSON (arhiva)")
    p.add_argument("--polje", default="query", help="naziv polja sa tekstom upita")
    p.add_argument("--model", default=DEFAULT_MODEL)
    p.add_argument("--bez-llm", action="store_true",
                   help="samo pravila; ostatak ide u 'ostalo' (PODRAZUMEVANI nacin rada)")
    p.add_argument("--llm-klasifikuj", action="store_true",
                   help="pusti LLM da svrsta ostatak. SPORO (~8 min / 130 upita) "
                        "i gresi na srpskom — koristi samo kad ti treba gruba slika")
    p.add_argument("--predlozi-pravila", action="store_true",
                   help="LLM predlaze nove korene reci iz ostatka (preporuceno "
                        "koriscenje modela — jednom, pa se predlozi upisu u KATEGORIJE)")
    a = p.parse_args()

    sirovo = json.loads(Path(a.ulaz).read_text(encoding="utf-8-sig"))
    if isinstance(sirovo, dict):
        stavke = next((v for v in sirovo.values() if isinstance(v, list)), [])
    else:
        stavke = sirovo
    stavke = [s for s in stavke if isinstance(s, dict) and a.polje in s]
    if not stavke:
        sys.exit(f"Nema nijedne stavke sa poljem '{a.polje}' u {a.ulaz}")

    redovi = []
    ostatak = []
    for s in stavke:
        upit = s[a.polje]
        kat = po_pravilima(upit)
        red = {
            "query": upit,
            "impressions": int(s.get("impressions", 0)),
            "clicks": int(s.get("clicks", 0)),
            "position": float(s.get("position", 0)),
            "kategorija": kat or "ostalo",
            "namera": namera(upit),
            "izvor": "pravilo" if kat else "nerazvrstano",
        }
        redovi.append(red)
        if not kat:
            ostatak.append(red)

    print(f"Pravila su razvrstala {len(redovi) - len(ostatak)}/{len(redovi)} upita.",
          file=sys.stderr)

    if ostatak and a.llm_klasifikuj:
        print(f"Ostatak za lokalni LLM ({a.model}): {len(ostatak)} upita",
              file=sys.stderr)
        mapa = llm_ostatak([r["query"] for r in ostatak], a.model)
        for r in ostatak:
            if r["query"] in mapa:
                r["kategorija"] = mapa[r["query"]]
                r["izvor"] = "llm"
    else:
        for r in ostatak:
            r["izvor"] = "pravilo"  # svesno ostavljeno u 'ostalo'

    if a.izlaz:
        Path(a.izlaz).write_text(
            json.dumps(redovi, ensure_ascii=False, indent=1), encoding="utf-8")
        print(f"Pun JSON: {a.izlaz}", file=sys.stderr)

    izvestaj = sazetak(redovi)
    if a.predlozi_pravila:
        izvestaj += "\n" + rudari_pravila(redovi, a.model)
    print(izvestaj)


if __name__ == "__main__":
    main()
