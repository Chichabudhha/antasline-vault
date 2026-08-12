#!/usr/bin/env python
"""Tanak klijent za lokalni Ollama server — deljeni sloj za sve `ollama-lokalni` skripte.

Zasto postoji: Ollama 0.18 sam bira `num_ctx` iz modelovog maksimuma (qwen3 =
262k), pa i model od 3B trazi ~15 GiB i odbije da se ucita na ovoj masini
("model requires more system memory"). Kontekst se OVDE fiksira, uvek.

Radi bez ijedne spoljne zavisnosti (samo stdlib) — ne treba `pip install`.
"""

from __future__ import annotations

import json
import re
import urllib.error
import urllib.request

OLLAMA_URL = "http://127.0.0.1:11434"

# 15,7 GB RAM, MX450 2 GB (prakticno bez offload-a) -> sve ide na CPU.
# 8192 je najveci kontekst koji je jos brz na i5-11320H; batch-evi su mali pa
# je ovo i vise nego dovoljno.
NUM_CTX = 8192

DEFAULT_MODEL = "qwen3:4b"

_THINK_RE = re.compile(r"<think>.*?</think>", re.DOTALL)


class OllamaGreska(RuntimeError):
    pass


def _post(putanja: str, telo: dict, timeout: int) -> dict:
    req = urllib.request.Request(
        f"{OLLAMA_URL}{putanja}",
        data=json.dumps(telo).encode("utf-8"),
        headers={"Content-Type": "application/json"},
    )
    try:
        with urllib.request.urlopen(req, timeout=timeout) as r:
            return json.loads(r.read().decode("utf-8"))
    except urllib.error.HTTPError as e:
        poruka = e.read().decode("utf-8", "replace")
        raise OllamaGreska(f"Ollama HTTP {e.code}: {poruka}") from e
    except urllib.error.URLError as e:
        raise OllamaGreska(
            "Ollama server ne odgovara na 127.0.0.1:11434. "
            "Pokreni ga sa `ollama serve` (ili startuj Ollama aplikaciju)."
        ) from e


def dostupni_modeli() -> list[str]:
    with urllib.request.urlopen(f"{OLLAMA_URL}/api/tags", timeout=10) as r:
        return [m["name"] for m in json.loads(r.read().decode("utf-8"))["models"]]


def chat_json(
    sistem: str,
    korisnik: str,
    schema: dict,
    model: str = DEFAULT_MODEL,
    timeout: int = 900,
    max_izlaz: int = 4096,
) -> dict:
    """Jedan poziv sa iznudjenim JSON izlazom (Ollama structured outputs).

    `schema` je obican JSON Schema dict — Ollama ogranicava dekodiranje na
    njega, pa nema "model je vratio prozu umesto JSON-a" klase gresaka.
    """
    odg = _post(
        "/api/chat",
        {
            "model": model,
            "messages": [
                {"role": "system", "content": sistem},
                {"role": "user", "content": korisnik},
            ],
            "stream": False,
            "think": False,  # qwen3 je hibridni reasoner; misljenje ovde samo kosta
            "format": schema,
            "options": {
                "num_ctx": NUM_CTX,
                "temperature": 0,  # klasifikacija mora biti ponovljiva
                # Odsecen izlaz = neispravan JSON = ceo poziv baceno vreme
                # (izmereno: qwen3:4b probije 2048 na 60 upita).
                "num_predict": max_izlaz,
            },
        },
        timeout,
    )
    sadrzaj = _THINK_RE.sub("", odg["message"]["content"]).strip()
    try:
        return json.loads(sadrzaj)
    except json.JSONDecodeError as e:
        raise OllamaGreska(f"Model nije vratio validan JSON: {sadrzaj[:300]}") from e


def statistika(odg: dict) -> str:
    if not odg.get("eval_duration"):
        return ""
    return f"{odg['eval_count'] / (odg['eval_duration'] / 1e9):.1f} tok/s"
