@echo off
REM Script para iniciar servidor PHP embutido

start c:\xampp\php\php -S 127.0.0.1:8000 -t public
timeout /t 2 >nul
start http://127.0.0.1:8000
