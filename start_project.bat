@echo off
:: Open Windows Terminal with 3 tabs in the current project directory
:: @REM wt -d "%cd%" cmd /k "php artisan serve --port=8181" ; new-tab -d "%cd%" cmd /k "npm run build" ; new-tab -d "%cd%" cmd /k "php artisan queue:work"

:: Define the path to your WAMP manager executable (Update if your WAMP is in C:\wamp\)
set WAMP_PATH=D:\phpServer\wampmanager.exe

:: 1. Start WAMP Server first
echo Starting WAMP Server...
start "" "%WAMP_PATH%"

:: 2. Wait for 5 seconds to let WAMP services (MySQL/Apache) initialize properly
echo Waiting for WAMP services to spin up...
timeout /t 15 /nobreak > nul

:: Define the exact absolute path to your Laravel project folder here
set PROJECT_PATH=D:\phpServer\www\JodTod

:: Open Windows Terminal with 3 tabs pointing directly to the project path
wt -d "%PROJECT_PATH%" cmd /k "php artisan serve --port=8181" ; new-tab -d "%PROJECT_PATH%" cmd /k "npm run build" ; new-tab -d "%PROJECT_PATH%" cmd /k "php artisan queue:work"
