# -*- coding: utf-8 -*-
"""
Rotacija append-only ledgera DNEVNIK-NAPRETKA.md.

Zašto postoji: ledger je 2026-08-18 narastao na 988 KB / 357 unosa. Read alat
čita prvih 2000 linija = ~160 KB = ~52k tokena po sesiji, a potrebno je ~10
poslednjih unosa. Rotacija drži glavni fajl na ~20 KB, sve starije ide u
mesečne arhive DOSLOVNO (ništa se ne skraćuje ni prepisuje).

Usput popravlja i hronologiju: unosi koji su završili na DNU fajla (append iz
cPanel workflow-a) sortiraju se natrag na svoje mesto. Na dan pisanja takvih
je bilo 4 (2026-06-23, 07-10, 07-30, 08-13) — unos od 13.08 je zbog toga bio
„praktično nevidljiv" i propušten iz PROGRESS tabele.

Upotreba:
    python skripte/rotiraj-dnevnik.py                 # zadrži poslednjih 5 dana
    python skripte/rotiraj-dnevnik.py 2026-08-14      # zadrži od datuma
    python skripte/rotiraj-dnevnik.py --dry-run       # samo izveštaj
"""
import re
import sys
import os
import datetime
import collections

LEDGER = 'DNEVNIK-NAPRETKA.md'
ARHIVA = 'dnevnik/arhiva-dnevnik-%s.md'   # %s = YYYY-MM
DANA_ZADRZI = 5
HEAD_RE = re.compile(r'^## (\d{4}-\d{2}-\d{2})')

NL = '\n'            # interno (parsiranje)
NL_DISK = '\r\n'     # na disk: ostatak vault-a je CRLF, ne pravi lazni diff


def ucitaj(path):
    """-> (preambula, [(datum, telo), ...]); telo uključuje '## ' naslov."""
    if not os.path.exists(path):
        return '', []
    lines = open(path, encoding='utf-8').read().split(NL)
    idx = [i for i, l in enumerate(lines) if HEAD_RE.match(l)]
    if not idx:
        return NL.join(lines).strip(), []
    pre = NL.join(lines[:idx[0]]).strip()
    out = []
    for n, i in enumerate(idx):
        end = idx[n + 1] if n + 1 < len(idx) else len(lines)
        out.append((HEAD_RE.match(lines[i]).group(1), NL.join(lines[i:end]).rstrip()))
    return pre, out


def sortiraj(unosi):
    """Newest-on-top, stabilno unutar istog datuma (čuva zatečen redosled)."""
    return [u for _, u in sorted(enumerate(unosi),
                                 key=lambda t: (t[1][0], -t[0]),
                                 reverse=True)]


def zaglavlje_ledgera(arhive):
    lst = ' · '.join('[[dnevnik/%s]]' % os.path.basename(p)[:-3]
                     for p in sorted(arhive, reverse=True))
    red = [
        '# Dnevnik napretka — tekući ledger',
        '',
        '> **Newest-on-top.** Nov unos ide **NA VRH**, nikad na dno — i iz Claude Code-a',
        '> i sa cPanel-a (`[cpanel-live]`). Unos na dnu je praktično nevidljiv: 13.08.2026',
        '> je tako jedan propušten iz PROGRESS tabele.',
        '>',
        '> **Ovde stoji samo poslednjih ~5 dana.** Starije je DOSLOVNO preneto u mesečne',
        '> arhive (ništa skraćeno ni prepisano): ' + lst,
        '>',
        '> Rotacija: `python skripte/rotiraj-dnevnik.py` (korak u `/zatvori-sesiju`).',
        '> Pretraga cele istorije: `grep -rn "pojam" --include="*.md" .` — u kontekst',
        '> ulaze samo pogođene linije, ne ceo fajl.',
        '',
    ]
    return NL.join(red)


def zaglavlje_arhive(mesec):
    red = [
        '# Dnevnik napretka — arhiva %s' % mesec,
        '',
        '> Izdvojeno iz [[DNEVNIK-NAPRETKA]] rotacijom (`skripte/rotiraj-dnevnik.py`).',
        '> **Ništa nije skraćeno ni prepisano** — unosi su preneti doslovno, sortirani',
        '> newest-on-top. Pun tekst svake sesije je i dalje u `dnevnik/YYYY-MM-DD-*.md`.',
        '',
    ]
    return NL.join(red)


def main():
    args = [a for a in sys.argv[1:] if not a.startswith('--')]
    dry = '--dry-run' in sys.argv
    if args:
        cutoff = args[0]
    else:
        cutoff = (datetime.date.today()
                  - datetime.timedelta(days=DANA_ZADRZI - 1)).isoformat()

    pre, unosi = ucitaj(LEDGER)
    if not unosi:
        print('Nema unosa u %s — prekid.' % LEDGER)
        return 1
    unosi = sortiraj(unosi)

    zadrzi = [u for u in unosi if u[0] >= cutoff]
    seli = [u for u in unosi if u[0] < cutoff]
    if not seli:
        print('Ništa starije od %s — ledger već čist (%d unosa).' % (cutoff, len(zadrzi)))
        return 0

    po_mesecu = collections.OrderedDict()
    for d, b in seli:
        po_mesecu.setdefault(d[:7], []).append((d, b))

    for mesec, novi in po_mesecu.items():
        put = ARHIVA % mesec
        _, stari = ucitaj(put)
        vidjeni = set()
        spoj = []
        for d, b in sortiraj(stari + novi):
            k = b.strip()
            if k not in vidjeni:
                vidjeni.add(k)
                spoj.append((d, b))
        sadrzaj = zaglavlje_arhive(mesec) + NL + (NL + NL).join(b for _, b in spoj) + NL
        dupl = len(stari) + len(novi) - len(spoj)
        print('  arhiva %-34s %3d unosa (+%d novih, %d duplikata preskočeno)  %.1f KB'
              % (put, len(spoj), len(novi), dupl, len(sadrzaj) / 1024.0))
        if not dry:
            open(put, 'w', encoding='utf-8', newline=NL_DISK).write(sadrzaj)

    svearhive = sorted(set(ARHIVA % m for m in po_mesecu)
                       | set('dnevnik/' + f for f in os.listdir('dnevnik')
                             if f.startswith('arhiva-dnevnik-')))

    # Zaglavlje koje je upisala prethodna rotacija se ne cuva kao "preambula"
    # (inace bi se dupliralo pri svakoj sledecoj rotaciji) — pise se iznova,
    # sa azuriranim spiskom arhiva.
    if pre.startswith('# Dnevnik napretka'):
        pre = ''

    delovi = [zaglavlje_ledgera(svearhive)]
    if pre:
        print('  ! preambula van unosa (%d B) sačuvana na vrhu' % len(pre))
        delovi.append(pre)
    delovi.append((NL + NL).join(b for _, b in zadrzi))
    novi_ledger = NL.join(delovi) + NL

    stara_kb = os.path.getsize(LEDGER) / 1024.0
    print('  ledger %-34s %3d unosa (cutoff %s)  %.1f KB  (bilo %.1f KB)'
          % (LEDGER, len(zadrzi), cutoff, len(novi_ledger) / 1024.0, stara_kb))
    if dry:
        print('  [dry-run] ništa nije upisano')
    else:
        open(LEDGER, 'w', encoding='utf-8', newline=NL_DISK).write(novi_ledger)
    return 0


if __name__ == '__main__':
    sys.exit(main())
