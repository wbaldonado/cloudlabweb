# Fix Storage Link Error on Windows

## Problem
When running `php artisan storage:link` on Windows, you may get this error:
```
Call to undefined function Illuminate\Filesystem\symlink()
```

This happens because the PHP `symlink()` function requires Administrator privileges on Windows.

## Solution

### Option 1: Use the Batch File (Easiest)

1. **Right-click** on `create-storage-link.bat`
2. Select **"Run as administrator"**
3. The script will create the symbolic link automatically

### Option 2: Use PowerShell Script

1. **Right-click** on PowerShell
2. Select **"Run as administrator"**
3. Navigate to your project directory:
   ```powershell
   cd C:\xampp\htdocs\cloudlablatam1
   ```
4. Run the script:
   ```powershell
   powershell -ExecutionPolicy Bypass -File create-storage-link.ps1
   ```

### Option 3: Manual PowerShell Command

1. **Right-click** on PowerShell
2. Select **"Run as administrator"**
3. Navigate to your project directory:
   ```powershell
   cd C:\xampp\htdocs\cloudlablatam1
   ```
4. Remove existing link if it exists:
   ```powershell
   Remove-Item "public\storage" -Force -ErrorAction SilentlyContinue
   ```
5. Create the symbolic link:
   ```powershell
   New-Item -ItemType SymbolicLink -Path "public\storage" -Target "$(Resolve-Path storage\app\public)"
   ```

### Option 4: Use CMD (Command Prompt)

1. **Right-click** on Command Prompt
2. Select **"Run as administrator"**
3. Navigate to your project directory:
   ```cmd
   cd C:\xampp\htdocs\cloudlablatam1
   ```
4. Remove existing link if it exists:
   ```cmd
   rmdir /q "public\storage" 2>nul
   ```
5. Create the symbolic link:
   ```cmd
   mklink /D "public\storage" "storage\app\public"
   ```

## Verify the Link

After creating the link, verify it works:

1. Check if the link exists:
   ```powershell
   Test-Path "public\storage"
   ```

2. Check if you can access files:
   - Navigate to `public\storage` in Windows Explorer
   - You should see the contents of `storage\app\public`

## For Production (Hostinger/Linux)

On Linux servers (like Hostinger), you can use the standard Laravel command:

```bash
php artisan storage:link
```

This works because Linux doesn't require special privileges for symbolic links.

## Troubleshooting

### Error: "You do not have sufficient privileges"
- Make sure you're running PowerShell/CMD as Administrator
- Right-click the program and select "Run as administrator"

### Error: "Cannot create a file when that file already exists"
- Remove the existing link first:
  ```powershell
  Remove-Item "public\storage" -Force
  ```
- Then create the link again

### Link exists but images don't load
- Verify the link points to the correct location
- Check file permissions on `storage\app\public`
- Clear Laravel cache:
  ```bash
  php artisan cache:clear
  php artisan config:clear
  ```
