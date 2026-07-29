import re, zlib, sys

p = sys.argv[1]
d = open(p, 'rb').read()
out = []
for m in re.finditer(rb'stream\r?\n(.*?)endstream', d, re.S):
    try:
        s = zlib.decompress(m.group(1))
    except Exception:
        continue
    parts = re.findall(rb'\(((?:\\.|[^()\\])*)\)', s)
    t = b' '.join(parts).decode('latin-1', errors='replace')
    t = re.sub(r'\\[0-9]{3}', '', t)
    t = t.replace('\\', '')
    t = re.sub(r'\s+', ' ', t).strip()
    if len(t) > 30:
        out.append(t)
print('blokova sa tekstom:', len(out))
txt = ' || '.join(out)[:3000]
sys.stdout.buffer.write(txt.encode('utf-8', 'replace'))
