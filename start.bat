@echo off
REM Script per avviare Laravel + Vite su Windows

echo ================================================
echo   Avvio Passepartout Utenti Carrello
echo ================================================
echo.

echo [Laravel] Avvio server su http://127.0.0.1:8000
start /B php artisan serve --host=127.0.0.1 --port=8000

timeout /t 3 /nobreak > NUL

echo [Vite] Avvio server su http://localhost:5173
echo.
echo ================================================
echo   Apri il browser su: http://127.0.0.1:8000
echo ================================================
echo.

npm run dev

echo.
echo Server fermati. Arrivederci!
