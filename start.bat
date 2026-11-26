@echo off
cd /d "%~dp0"

echo Menjalankan Vite (npm run dev)...
start "Laravel Vite" cmd /k "npm run dev"

echo Menjalankan Laravel Server (php artisan serve)...
start "Laravel Server" cmd /k "php artisan serve"

echo Server berhasil dijalankan. 
timeout /t 3 /nobreak >nul

start http://localhost:8000/