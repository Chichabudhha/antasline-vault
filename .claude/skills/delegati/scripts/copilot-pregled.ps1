<#
.SYNOPSIS
    Pokreće GitHub Copilot CLI kao read-only delegata za pregled koda.

.DESCRIPTION
    Copilot je najjači na kodu — zato mu ide pregled child teme, mu-plugina,
    migracionih skripti i git istorije, a ne bulk čitanje markdown-a.

    Zaštita (Copilot NEMA zabranu čitanja po putanji, pa je drugačija od Grok-a):

      1. --deny-tool 'write' + --deny-tool 'shell'
         Deny pobeđuje --allow-all-tools. Agent ne može ni da piše ni da izvrši
         komandu, ali i dalje odgovara tekstom.

      2. Pokretanje isključivo iz vault-a
         Copilot ograničava pristup fajlovima na cwd + podfoldere + temp dir.
         Skripta odbija da radi ako cwd nije vault — pokretanje iz C:\ bi mu
         otvorilo ceo disk.

      3. --disallow-temp-dir
         Uklanja i temp dir iz dozvoljenih putanja.

      4. --no-remote --no-remote-export
         🔴 KLJUČNO: Copilot PODRAZUMEVANO izvozi sesiju na GitHub web/mobile.
         Bez ovih flag-ova bi sadržaj privatnog vault-a odlazio van mašine.

.PARAMETER Prompt
    Putanja do .txt fajla sa promptom, ili sam tekst prompta.

.PARAMETER Out
    Gde se upisuje odgovor. Podrazumevano: scratchpad\copilot-<timestamp>.md

.PARAMETER Model
    Opciono, npr. claude-sonnet-5, gpt-5.6-sol, gemini-3.6-flash.
    Za jeftin bulk pregled: gemini-3.6-flash ili gpt-5.4-mini.

.PARAMETER MaxCredits
    Gornja granica potrošnje za ovu sesiju (minimum 30). Preporučeno na Free planu.

.PARAMETER Build
    ⚠️ Dodaje pristup lokalnom WordPress build-u C:\xampp\htdocs\antasline.
    Copilot nema izuzimanje po putanji, pa ovo otvara i wp-config.php (DB
    kredencijali). Koristiti SAMO za konkretan pregled koda teme/plugina,
    nikad za bulk čitanje. Podrazumevano isključeno.

.EXAMPLE
    .\copilot-pregled.ps1 -Prompt .\promptovi\wpgs-prefiks.txt

.EXAMPLE
    .\copilot-pregled.ps1 -Prompt "Nadji hardkodovane localhost URL-ove" -Build -Model gemini-3.6-flash
#>

[CmdletBinding()]
param(
    [Parameter(Mandatory = $true)]
    [string] $Prompt,

    [string] $Out,

    [string] $Model,

    [int] $MaxCredits,

    [switch] $Build
)

$ErrorActionPreference = 'Stop'

$Vault = 'C:\Projekti\antasline-vault'
$BuildPath = 'C:\xampp\htdocs\antasline'

# ── Preduslovi ────────────────────────────────────────────────────────────────
if (-not (Get-Command copilot -ErrorAction SilentlyContinue)) {
    throw "Copilot CLI nije na PATH-u."
}
if (-not (Test-Path (Join-Path $Vault 'AGENTS.md'))) {
    throw "Nema AGENTS.md u vault-u — Copilot bi radio bez pravila. Prekidam."
}

# ── Prompt ────────────────────────────────────────────────────────────────────
if (Test-Path -LiteralPath $Prompt -PathType Leaf) {
    $PromptText = Get-Content -LiteralPath $Prompt -Raw
    Write-Host "Prompt iz fajla: $Prompt ($($PromptText.Length) znakova)" -ForegroundColor DarkGray
} else {
    $PromptText = $Prompt
}
if ([string]::IsNullOrWhiteSpace($PromptText)) { throw "Prompt je prazan." }

# 🔴 PowerShell 5.1 gotcha: navodnik u argumentu razbije prosleđivanje native
# exe-u — Copilot tada javi "Invalid command format ... prompt was not quoted".
# Neparan broj navodnika je dovoljan. Srpski tekst to lako napravi: „ovako"
# ima ASCII zatvarač uz tipografski otvarač. Escape-uje se kao \" .
$PromptText = $PromptText -replace '"', '\"'

# ── Izlaz ─────────────────────────────────────────────────────────────────────
if (-not $Out) {
    $stamp = Get-Date -Format 'yyyy-MM-dd-HHmmss'
    $Out   = Join-Path $Vault "scratchpad\copilot-$stamp.md"
}
$OutDir = Split-Path -Parent $Out
if ($OutDir -and -not (Test-Path $OutDir)) {
    New-Item -ItemType Directory -Path $OutDir -Force | Out-Null
}

# ── Argumenti ─────────────────────────────────────────────────────────────────
$cpArgs = @(
    '-C', $Vault                    # cwd = vault; ujedno i granica pristupa fajlovima
    '-p', $PromptText
    '--allow-all-tools'             # obavezno za neinteraktivni rezim...
    '--deny-tool', 'write'          # ...ali deny ima prednost nad njim
    '--deny-tool', 'shell'
    '--disallow-temp-dir'
    '--no-remote'                   # bez daljinskog upravljanja sesijom
    '--no-remote-export'            # 🔴 bez izvoza sadrzaja vault-a na GitHub
    '--silent'                      # samo odgovor, bez statistike u stdout
)
if ($Model)      { $cpArgs += @('--model', $Model) }
if ($MaxCredits) { $cpArgs += @('--max-ai-credits', $MaxCredits) }

if ($Build) {
    Write-Warning "Build rezim: Copilot dobija pristup $BuildPath (ukljucujuci wp-config.php)."
    if (-not (Test-Path $BuildPath)) { throw "Build nije nadjen na $BuildPath" }
    $cpArgs += @('--add-dir', $BuildPath)
}

# ── Poziv ─────────────────────────────────────────────────────────────────────
Write-Host "Copilot radi (read-only)..." -ForegroundColor Cyan
$raw = & copilot @cpArgs
$code = $LASTEXITCODE

$raw | Out-File -LiteralPath $Out -Encoding utf8

if ($code -ne 0) {
    Write-Warning "Copilot je izasao sa kodom $code. Izlaz je ipak snimljen."
}
Write-Host "Odgovor upisan: $Out" -ForegroundColor Green
Write-Host "Kvota se proverava u interaktivnoj sesiji: copilot -> /usage" -ForegroundColor DarkGray
