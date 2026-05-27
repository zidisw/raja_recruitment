@echo off
setlocal
cd /d "%~dp0"

title Raja Recruitment - Development Mode
echo ==========================================================
echo    Raja Recruitment - Vite Dev Server (Hot Reload/HMR)
echo ==========================================================
echo.

:: Suppress Node deprecation warnings (especially for DEP0205)
set NODE_NO_WARNINGS=1
set VITE_DEV_HOST=127.0.0.1
set VITE_DEV_PORT=5173

:: Keep Laravel bootstrap cache stable so VS Code Intellisense does not race
:: with Artisan while it regenerates bootstrap/cache/packages.php.
echo [*] Melewati optimize:clear otomatis agar Intellisense tidak berebut cache Laravel.
echo [*] Jika perlu bersihkan cache manual: php artisan optimize:clear
echo.

:: Remove stale Vite hot marker so Laravel does not point to an old dev server.
echo [*] Membersihkan marker HMR lama...
if exist public\hot (
    del /q public\hot
    echo [+] public/hot lama berhasil dihapus.
) else (
    echo [.] Tidak ada public/hot lama.
)
echo.

echo [*] Membuka server Vite untuk update realtime (HMR)...
echo [*] Vite URL : http://%VITE_DEV_HOST%:%VITE_DEV_PORT%
echo [*] App URL  : http://raja-recruitment.test
echo [*] Silakan tetap biarkan jendela CMD ini terbuka selama development.
echo.

:: Menjalankan Vite Development Server
npm run dev -- --host %VITE_DEV_HOST% --port %VITE_DEV_PORT% --strictPort

echo.
echo [*] Membersihkan marker HMR setelah Vite berhenti...
if exist public\hot del /q public\hot
echo.
echo ==========================================================
echo    Vite Dev Server dihentikan.
echo ==========================================================
pause
endlocal
