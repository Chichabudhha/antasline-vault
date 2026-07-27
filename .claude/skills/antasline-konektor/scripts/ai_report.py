#!/usr/bin/env python
"""GA4 — saobracaj sa AI asistenata (ChatGPT, Perplexity, Gemini, Copilot...).

Zasto postoji odvojeno od ga4_report.py: GA4-ov ugradjeni kanal "AI Assistant"
POTCENJUJE stvarni AI saobracaj ~3x. Klasifikacija (`medium=ai-assistant`) je
proradila tek u junu 2026 — sve pre toga je razbacano po referral/organic/(not set),
a deo dolazi i kroz utm-tagovane linkove (`medium=gmb`). Ova skripta agregira
po IZVORU (hostname), pa hvata sve varijante.

Primer:
    python ai_report.py --from 2026-01-01 --to 2026-07-26
"""

import argparse
import json
import sys
from collections import defaultdict
from pathlib import Path

sys.path.insert(0, str(Path(__file__).parent))
from auth import friendly_api_error, get_ga4_credentials  # noqa: E402

GA4_PROPERTY = "properties/292720335"

AI_HOSTS = (
    "chatgpt", "openai", "perplexity", "claude", "anthropic", "gemini", "bard",
    "copilot", "you.com", "poe.com", "deepseek", "grok", "mistral", "phind",
)


def main() -> None:
    p = argparse.ArgumentParser()
    p.add_argument("--from", dest="date_from", required=True, help="YYYY-MM-DD")
    p.add_argument("--to", dest="date_to", required=True, help="YYYY-MM-DD")
    a = p.parse_args()

    from google.analytics.data_v1beta import BetaAnalyticsDataClient
    from google.analytics.data_v1beta.types import (
        DateRange, Dimension, Metric, RunReportRequest,
    )

    client = BetaAnalyticsDataClient(credentials=get_ga4_credentials())
    rng = [DateRange(start_date=a.date_from, end_date=a.date_to)]

    def run(dims, mets):
        r = client.run_report(RunReportRequest(
            property=GA4_PROPERTY, date_ranges=rng,
            dimensions=[Dimension(name=d) for d in dims],
            metrics=[Metric(name=m) for m in mets], limit=100000))
        return [([v.value for v in x.dimension_values],
                 [v.value for v in x.metric_values]) for x in r.rows]

    def is_ai(src: str) -> bool:
        return any(h in src.lower() for h in AI_HOSTS)

    by_source = defaultdict(int)
    classified = 0
    total = 0
    for d, m in run(["sessionSource", "sessionMedium"], ["sessions"]):
        if is_ai(d[0]):
            n = int(m[0])
            by_source[d[0]] += n
            total += n
            if d[1] == "ai-assistant":
                classified += n

    channel = 0
    for d, m in run(["sessionDefaultChannelGroup"], ["sessions"]):
        if d[0] == "AI Assistant":
            channel = int(m[0])

    landing = defaultdict(int)
    for d, m in run(["sessionSource", "landingPage"], ["sessions"]):
        if is_ai(d[0]):
            landing[d[1]] += int(m[0])

    events = defaultdict(int)
    for d, m in run(["sessionSource", "eventName"], ["eventCount"]):
        if is_ai(d[0]) and d[1] in ("generate_lead", "tel", "mailto", "form_submit"):
            events[d[1]] += int(m[0])

    print(json.dumps({
        "period": {"from": a.date_from, "to": a.date_to},
        "ai_sessions_total": total,
        "ga4_channel_ai_assistant": channel,
        "podbacaj_kanala": total - channel,
        "po_izvoru": dict(sorted(by_source.items(), key=lambda kv: -kv[1])),
        "top_landing": dict(sorted(landing.items(), key=lambda kv: -kv[1])[:10]),
        "eventi": dict(events),
    }, ensure_ascii=False, indent=1))


if __name__ == "__main__":
    try:
        main()
    except SystemExit:
        raise
    except Exception as exc:  # noqa: BLE001
        friendly_api_error(exc)
