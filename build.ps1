$ErrorActionPreference = "Stop"
Set-Location $PSScriptRoot
function Run-Step([string]$Program, [string[]]$Arguments) {
    & $Program @Arguments
    if ($LASTEXITCODE -ne 0) { throw "$Program failed. Stop here and fix the error shown above." }
}
foreach ($program in @('php', 'composer', 'node', 'npm')) {
    if (-not (Get-Command $program -ErrorAction SilentlyContinue)) { throw "Install $program and reopen this terminal before building." }
}
if (-not (Test-Path '.env')) { Copy-Item '.env.example' '.env' }
Run-Step 'composer' @('install','--prefer-dist','--no-interaction')
Run-Step 'php' @('artisan','key:generate','--force')
Run-Step 'php' @('tools/install-breeze.php')
Run-Step 'composer' @('install','--prefer-dist','--no-interaction')
Get-ChildItem app,bootstrap,config,database,routes,tools,tests -Recurse -Filter '*.php' | ForEach-Object { Run-Step 'php' @('-l', $_.FullName) }
Run-Step 'php' @('artisan','route:list','--except-vendor')
Run-Step 'php' @('artisan','view:cache')
Run-Step 'php' @('artisan','view:clear')
Run-Step 'composer' @('test')
Run-Step 'composer' @('install','--no-dev','--prefer-dist','--optimize-autoloader','--no-interaction')
Run-Step 'php' @('tools/package.php')
Write-Host 'Build passed. Upload package: dist/TFA8-InfinityFree-Upload.zip'
