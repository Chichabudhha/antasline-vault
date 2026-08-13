<#
.SYNOPSIS
    Pokreće Grok CLI kao read-only delegata nad antasline-vault-om.

.DESCRIPTION
    Sve zabrane su ovde, u skripti — ne moraš ih pamtiti niti kucati.
    Dva nezavisna sloja zaštite:

      Sloj 1  --tools "read_file,grep,list_dir"
              Alati za pisanje (search_replace) i shell (run_terminal_cmd) se
              modelu uopšte NE prikazuju. Ne može da traži ono što ne vidi.

      Sloj 2  .grok/config.toml → [permission] deny
              Edit/Write/Bash + kredencijali + kvota-bombe. `deny` pobeđuje sve,
              uključujući --yolo. Radi i ako neko ukloni Sloj 1.

    NAPOMENA: --sandbox se namerno NE koristi. Grok sandbox je Landlock/Seatbelt,
    dakle Linux/macOS. Na Windows-u samo upiše upozorenje u log i nastavi bez
    ikakve zaštite — lažan osećaj sigurnosti.

.PARAMETER Prompt
    Putanja do .txt fajla sa promptom, ili sam tekst prompta.
    Za ozbiljan posao koristi fajl — v. promptovi/_SABLON.txt (6 obaveznih delova).

.PARAMETER Out
    Gde se upisuje odgovor. Podrazumevano: scratchpad\grok-<timestamp>.md

.PARAMETER Model
    Opciono. Ako se izostavi, grok bira sam.

.PARAMETER Effort
    Opciono: none | minimal | low | medium | high | xhigh | max

.EXAMPLE
    .\grok-citaj.ps1 -Prompt .\promptovi\h1-audit.txt -Out ..\..\..\..\scratchpad\h1-audit.md

.EXAMPLE
    .\grok-citaj.ps1 -Prompt "Koliko .md fajlova ima u dnevnik\ i koji je najnoviji?"
#>

[CmdletBinding()]
param(
    [Parameter(Mandatory = $true)]
    [string] $Prompt,

    [string] $Out,

    [string] $Model,

    [ValidateSet('none','minimal','low','medium','high','xhigh','max')]
    [string] $Effort
)

$ErrorActionPreference = 'Stop'

$Vault    = 'C:\Projekti\antasline-vault'
$GrokExe  = 'C:\Users\Miroslav\.grok\bin\grok.exe'

# ── Preduslovi ────────────────────────────────────────────────────────────────
if (-not (Test-Path $GrokExe)) {
    throw "Grok CLI nije nađen na $GrokExe"
}
# Sloj 2 se učitava SAMO iz korisničkog config-a — projektni .grok\config.toml
# grok 1.0.3 nađe ali ne primeni (v. komentar u tom fajlu). Zato se proverava
# korisnički, i to sadržinski, ne samo postojanje fajla.
$UserCfg = Join-Path $env:USERPROFILE '.grok\config.toml'
if (-not (Test-Path $UserCfg) -or -not (Select-String -Path $UserCfg -Pattern '^\s*\[permission\]' -Quiet)) {
    throw "Nema [permission] bloka u $UserCfg — Sloj 2 zabrana nije na mestu. Prekidam."
}

# ── Prompt: fajl ili inline tekst ─────────────────────────────────────────────
if (Test-Path -LiteralPath $Prompt -PathType Leaf) {
    $PromptText = Get-Content -LiteralPath $Prompt -Raw
    Write-Host "Prompt iz fajla: $Prompt ($($PromptText.Length) znakova)" -ForegroundColor DarkGray
} else {
    $PromptText = $Prompt
}
if ([string]::IsNullOrWhiteSpace($PromptText)) { throw "Prompt je prazan." }

# 🔴 PowerShell 5.1 gotcha: navodnik u argumentu razbije prosleđivanje native
# exe-u. Neparan broj navodnika je dovoljan, a srpski tekst to lako napravi:
# „ovako" ima ASCII zatvarač uz tipografski otvarač. Escape-uje se kao \" .
$PromptText = $PromptText -replace '"', '\"'

# ── Izlaz ─────────────────────────────────────────────────────────────────────
if (-not $Out) {
    $stamp = Get-Date -Format 'yyyy-MM-dd-HHmmss'
    $Out   = Join-Path $Vault "scratchpad\grok-$stamp.md"
}
$OutDir = Split-Path -Parent $Out
if ($OutDir -and -not (Test-Path $OutDir)) {
    New-Item -ItemType Directory -Path $OutDir -Force | Out-Null
}

# ── Poziv ─────────────────────────────────────────────────────────────────────
$grokArgs = @(
    '-p', $PromptText
    '--cwd', $Vault
    '--tools', 'read_file,grep,list_dir'   # Sloj 1: write/shell se ne prikazuju
    '--no-memory'                          # ne pamti projektne činjenice van vault-a
    '--no-subagents'                       # bez granatanja — kvota i predvidivost
    '--disable-web-search'                 # posao je nad lokalnim fajlovima
    '--no-auto-update'
    '--output-format', 'json'              # nosi usage / total_cost_usd
)
if ($Model)  { $grokArgs += @('--model', $Model) }
if ($Effort) { $grokArgs += @('--effort', $Effort) }

Write-Host "Grok radi (read-only)..." -ForegroundColor Cyan
$raw = & $GrokExe @grokArgs
if ($LASTEXITCODE -ne 0) {
    Write-Warning "Grok je izašao sa kodom $LASTEXITCODE."
}

# ── Rezultat ──────────────────────────────────────────────────────────────────
# Grok ume da pomeša poruke o ažuriranju / upozorenja sa JSON-om na stdout-u,
# pa se ne parsira ceo izlaz nego se izdvoji JSON objekat. Prvo ceo izlaz,
# pa fallback na poslednji red koji počinje sa "{".
$json = $null
$rawText = $raw | Out-String
try {
    $json = $rawText | ConvertFrom-Json
} catch {
    $jsonLine = ($raw | Where-Object { $_ -is [string] -and $_.TrimStart().StartsWith('{') } | Select-Object -Last 1)
    if ($jsonLine) {
        try { $json = $jsonLine | ConvertFrom-Json } catch { }
    }
}

if ($null -ne $json -and $json.PSObject.Properties.Name -contains 'text') {
    $json.text | Out-File -LiteralPath $Out -Encoding utf8
    Write-Host "Odgovor upisan: $Out" -ForegroundColor Green

    if ($json.usage) {
        $u = $json.usage
        Write-Host ("Tokeni: ulaz {0} | keš {1} | izlaz {2} | ukupno {3}" -f `
            $u.input_tokens, $u.cache_read_input_tokens, $u.output_tokens, $u.total_tokens) -ForegroundColor DarkGray
    }
    if ($json.total_cost_usd) {
        Write-Host ("Trosak: `${0}" -f $json.total_cost_usd) -ForegroundColor DarkGray
    } else {
        Write-Host "Trosak: nije prijavljen (ne znaci besplatno)." -ForegroundColor DarkGray
    }
    if ($json.sessionId) {
        Write-Host "Sesija: $($json.sessionId)  (nastavak: grok -p '...' --resume $($json.sessionId))" -ForegroundColor DarkGray
    }
} else {
    # JSON se nije isparsirao — snimi sirov izlaz da se ne izgubi posao
    $raw | Out-File -LiteralPath $Out -Encoding utf8
    Write-Warning "Izlaz nije bio validan JSON. Sirov izlaz je u: $Out"
}
