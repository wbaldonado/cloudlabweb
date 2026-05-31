# PowerShell script to create storage symbolic link on Windows
Write-Host "Creating storage symbolic link for Windows..." -ForegroundColor Cyan

$publicStorage = "public\storage"
$storageAppPublic = "storage\app\public"

# Check if link already exists
if (Test-Path $publicStorage) {
    Write-Host "Removing existing storage link..." -ForegroundColor Yellow
    Remove-Item $publicStorage -Force -ErrorAction SilentlyContinue
}

# Check if target directory exists
if (-not (Test-Path $storageAppPublic)) {
    Write-Host "Creating target directory: $storageAppPublic" -ForegroundColor Yellow
    New-Item -ItemType Directory -Path $storageAppPublic -Force | Out-Null
}

# Create the symbolic link
try {
    # Check if running as Administrator
    $isAdmin = ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
    
    if ($isAdmin) {
        New-Item -ItemType SymbolicLink -Path $publicStorage -Target (Resolve-Path $storageAppPublic) -Force | Out-Null
        Write-Host "SUCCESS: Storage link created successfully!" -ForegroundColor Green
    } else {
        Write-Host "ERROR: This script requires Administrator privileges." -ForegroundColor Red
        Write-Host "Please run PowerShell as Administrator and execute this script again." -ForegroundColor Yellow
        Write-Host ""
        Write-Host "Alternative: Run this command manually as Administrator:" -ForegroundColor Yellow
        Write-Host "New-Item -ItemType SymbolicLink -Path `"$publicStorage`" -Target `"$(Resolve-Path $storageAppPublic)`"" -ForegroundColor Cyan
        exit 1
    }
} catch {
    Write-Host "ERROR: Failed to create storage link." -ForegroundColor Red
    Write-Host $_.Exception.Message -ForegroundColor Red
    Write-Host ""
    Write-Host "Try running PowerShell as Administrator and execute:" -ForegroundColor Yellow
    Write-Host "New-Item -ItemType SymbolicLink -Path `"$publicStorage`" -Target `"$(Resolve-Path $storageAppPublic)`"" -ForegroundColor Cyan
    exit 1
}
