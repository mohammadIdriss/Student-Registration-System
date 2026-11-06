@echo off
setlocal EnableExtensions
title Campus Registry Launcher

rem ============================================================
rem Campus Registry - start MySQL, import the database, and run
rem the website. XAMPP is required. No Apache setup is needed.
rem ============================================================

set "PROJECT_DIR=%~dp0"
if defined XAMPP_HOME (
    set "XAMPP_DIR=%XAMPP_HOME%"
) else (
    set "XAMPP_DIR=C:\xampp"
)

rem If XAMPP is installed on D:, try that location too.
if not exist "%XAMPP_DIR%\php\php.exe" if exist "D:\xampp\php\php.exe" set "XAMPP_DIR=D:\xampp"

set "PHP_EXE=%XAMPP_DIR%\php\php.exe"
set "MYSQL_EXE=%XAMPP_DIR%\mysql\bin\mysql.exe"
set "MYSQLADMIN_EXE=%XAMPP_DIR%\mysql\bin\mysqladmin.exe"

if not exist "%PHP_EXE%" goto xampp_error
if not exist "%MYSQL_EXE%" goto xampp_error
if not exist "%PROJECT_DIR%database.sql" goto file_error

echo.
echo ============================================================
echo   Campus Registry - Student Registration System
echo ============================================================
echo.
echo [1/4] Checking MySQL...

"%MYSQLADMIN_EXE%" -u root ping >nul 2>&1
if errorlevel 1 (
    echo       Starting MySQL through XAMPP...
    start "XAMPP MySQL" /min "%XAMPP_DIR%\mysql_start.bat"
)

set /a RETRIES=0
:wait_for_mysql
"%MYSQLADMIN_EXE%" -u root ping >nul 2>&1
if not errorlevel 1 goto mysql_ready
set /a RETRIES+=1
if %RETRIES% GEQ 30 goto mysql_error
timeout /t 1 /nobreak >nul
goto wait_for_mysql

:mysql_ready
echo       MySQL is ready.
echo [2/4] Importing database.sql if needed...
"%MYSQL_EXE%" --default-character-set=utf8mb4 -u root < "%PROJECT_DIR%database.sql"
if errorlevel 1 goto import_error
echo       Database is ready.

echo [3/4] Starting the website on port 7100...
start "Campus Registry Server" /min "%PHP_EXE%" -S 127.0.0.1:7100 -t "%PROJECT_DIR%."
timeout /t 2 /nobreak >nul

echo [4/4] Opening Campus Registry in your browser...
start "" "http://127.0.0.1:7100/"

echo.
echo Website: http://127.0.0.1:7100/
echo.
echo The website runs in the minimized "Campus Registry Server"
echo window. Close that window to stop the website.
echo MySQL remains available in XAMPP for your team.
echo.
pause
exit /b 0

:xampp_error
echo.
echo ERROR: XAMPP was not found.
echo Install XAMPP in C:\xampp, or set XAMPP_HOME to its folder.
echo Example: set XAMPP_HOME=D:\xampp
echo.
pause
exit /b 1

:file_error
echo.
echo ERROR: database.sql is missing from the project folder.
echo Keep start.bat and database.sql together and try again.
echo.
pause
exit /b 1

:mysql_error
echo.
echo ERROR: MySQL did not start within 30 seconds.
echo Open the XAMPP Control Panel, check the MySQL error message,
echo and make sure another database is not using the same port.
echo.
pause
exit /b 1

:import_error
echo.
echo ERROR: The database could not be imported.
echo This launcher uses the default XAMPP login: root, no password.
echo If your MySQL login is different, update start.bat and
echo config\database.php, then run this file again.
echo.
pause
exit /b 1
