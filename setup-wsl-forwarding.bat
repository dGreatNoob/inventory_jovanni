@echo off
REM WSL2 Port Forwarding Setup for Windows
REM Double-click this file to run (requires Administrator privileges)

echo.
echo ========================================
echo WSL2 Port Forwarding Setup
echo ========================================
echo.
echo This script will set up port forwarding from WSL2 to Windows.
echo.
echo NOTE: This requires Administrator privileges.
echo If prompted, click "Yes" to allow.
echo.
pause

REM Check for PowerShell
where powershell >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: PowerShell is not found!
    pause
    exit /b 1
)

REM Get the directory where this batch file is located
set SCRIPT_DIR=%~dp0

REM Run PowerShell script
powershell.exe -ExecutionPolicy Bypass -File "%SCRIPT_DIR%wsl-port-forward.ps1"

echo.
echo ========================================
echo Setup complete!
echo ========================================
echo.
echo You can now access:
echo   - Laravel App: http://localhost:8000
echo   - phpMyAdmin:  http://localhost:8081
echo.
pause

