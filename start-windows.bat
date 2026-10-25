@echo off
cd /d "%~dp0"
echo.
echo Blue Form - http://localhost:8000
echo To stop the server press Ctrl+C.
echo.
php -S 127.0.0.1:8000 -t public public/router.php
pause
