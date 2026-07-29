import re, sys, os

src = sys.argv[1]
outdir = sys.argv[2]
os.makedirs(outdir, exist_ok=True)
d = open(src, 'rb').read()

n = 0
# DCTDecode = ugrađen JPEG; vadi se doslovno, bez prekodiranja
for m in re.finditer(rb'/DCTDecode(.{0,600}?)stream\r?\n', d, re.S):
    start = m.end()
    end = d.find(b'endstream', start)
    if end < 0:
        continue
    blob = d[start:end].rstrip(b'\r\n')
    if len(blob) < 60000:      # sitne ikone/logotipi ne trebaju
        continue
    n += 1
    p = os.path.join(outdir, 'img%02d.jpg' % n)
    open(p, 'wb').write(blob)
    print(p, len(blob) // 1024, 'KB')
print('ukupno:', n)
