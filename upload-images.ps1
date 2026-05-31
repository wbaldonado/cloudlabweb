# Script para subir imágenes a producción vía FTP
# Uso: .\upload-images.ps1

param(
    [Parameter(Mandatory=$true)]
    [string]$FtpServer,
    
    [Parameter(Mandatory=$true)]
    [string]$FtpUsername,
    
    [Parameter(Mandatory=$true)]
    [string]$FtpPassword,
    
    [Parameter(Mandatory=$false)]
    [string]$RemotePath = "/public/images"
)

Write-Host "=== Subiendo imágenes a producción ===" -ForegroundColor Cyan

$images = @(
    "public\images\digital-transformation-es.png",
    "public\images\digital-transformation-en.png"
)

foreach ($image in $images) {
    if (Test-Path $image) {
        $fileName = Split-Path $image -Leaf
        $remoteFile = "$RemotePath/$fileName"
        
        Write-Host "Subiendo: $fileName..." -ForegroundColor Yellow
        
        try {
            $ftpUri = "ftp://$FtpServer$remoteFile"
            $ftpRequest = [System.Net.FtpWebRequest]::Create($ftpUri)
            $ftpRequest.Credentials = New-Object System.Net.NetworkCredential($FtpUsername, $FtpPassword)
            $ftpRequest.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
            $ftpRequest.UseBinary = $true
            $ftpRequest.UsePassive = $true
            
            $fileContent = [System.IO.File]::ReadAllBytes((Resolve-Path $image))
            $ftpRequest.ContentLength = $fileContent.Length
            
            $requestStream = $ftpRequest.GetRequestStream()
            $requestStream.Write($fileContent, 0, $fileContent.Length)
            $requestStream.Close()
            
            $response = $ftpRequest.GetResponse()
            Write-Host "✓ $fileName subido exitosamente" -ForegroundColor Green
            $response.Close()
        }
        catch {
            Write-Host "✗ Error al subir $fileName : $_" -ForegroundColor Red
        }
    }
    else {
        Write-Host "✗ Archivo no encontrado: $image" -ForegroundColor Red
    }
}

Write-Host "`n=== Proceso completado ===" -ForegroundColor Cyan
Write-Host "`nNota: Si usas otro método (SFTP, panel de hosting, etc.), sube manualmente:" -ForegroundColor Yellow
Write-Host "  - digital-transformation-es.png" -ForegroundColor White
Write-Host "  - digital-transformation-en.png" -ForegroundColor White
Write-Host "`nA la carpeta: public/images/ en tu servidor de producción" -ForegroundColor Yellow

