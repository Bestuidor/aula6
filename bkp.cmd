@echo off
cls
color 1e

echo Caminho completo: %cd%

:: Pega apenas o nome da pasta atual
for %%i in ("%cd%") do set "pasta=%%~nxi"
echo Última pasta: %pasta%

if not exist \temp mkdir \temp
start winrar a -afzip -r -ep1 \temp\%pasta%.zip *
start explorer c:\temp
