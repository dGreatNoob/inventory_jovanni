# WSL2 Port Forwarding Script for Windows
# This script forwards ports from WSL2 to Windows localhost
# Run this script in PowerShell as Administrator

# Required ports to forward
$ports = @(8000, 8081)

# Get WSL2 distribution name (usually "Ubuntu" or your distro name)
$wslDistro = wsl -l -v | Select-String "Running" | ForEach-Object { ($_ -split '\s+')[0] } | Select-Object -First 1

if (-not $wslDistro) {
    Write-Host "❌ No running WSL distribution found!" -ForegroundColor Red
    exit 1
}

Write-Host "✅ Found WSL distribution: $wslDistro" -ForegroundColor Green

# Get WSL2 IP address
$wslIp = wsl -d $wslDistro hostname -I | ForEach-Object { ($_ -split '\s+')[0] }

if (-not $wslIp) {
    Write-Host "❌ Could not get WSL IP address!" -ForegroundColor Red
    exit 1
}

Write-Host "📍 WSL2 IP Address: $wslIp" -ForegroundColor Cyan
Write-Host ""

# Remove existing port forwards first (ignore errors if they don't exist)
Write-Host "🧹 Cleaning up existing port forwards..." -ForegroundColor Yellow
foreach ($port in $ports) {
    netsh interface portproxy delete v4tov4 listenport=$port listenaddress=0.0.0.0 2>$null
    netsh advfirewall firewall delete rule name="WSL2 Port Forward $port" 2>$null
}

Write-Host "✅ Cleanup complete" -ForegroundColor Green
Write-Host ""

# Add new port forwards
Write-Host "🔧 Setting up port forwarding..." -ForegroundColor Yellow
foreach ($port in $ports) {
    # Forward the port
    netsh interface portproxy add v4tov4 listenport=$port listenaddress=0.0.0.0 connectport=$port connectaddress=$wslIp
    
    # Add firewall rule to allow the connection
    netsh advfirewall firewall add rule name="WSL2 Port Forward $port" dir=in action=allow protocol=TCP localport=$port
    
    Write-Host "   ✅ Forwarded port $port -> $wslIp`:$port" -ForegroundColor Green
}

Write-Host ""
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Green
Write-Host "✅ Port forwarding configured successfully!" -ForegroundColor Green
Write-Host "═══════════════════════════════════════════════════════════" -ForegroundColor Green
Write-Host ""
Write-Host "🌐 You can now access from Windows browser:" -ForegroundColor Cyan
Write-Host "   • Laravel App:    http://localhost:8000" -ForegroundColor White
Write-Host "   • phpMyAdmin:     http://localhost:8081" -ForegroundColor White
Write-Host ""
Write-Host "💡 Note: This forwarding is active until WSL restarts." -ForegroundColor Yellow
Write-Host "   Run this script again if you restart WSL2." -ForegroundColor Yellow
Write-Host ""

