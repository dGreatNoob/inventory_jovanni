#!/bin/bash
# WSL2 Port Forwarding Helper Script
# This script helps set up port forwarding from WSL to Windows
# It generates a PowerShell command that you can run on Windows

echo "🔧 WSL2 Port Forwarding Setup"
echo ""
echo "This script will help you forward ports from WSL2 to Windows."
echo ""

# Get WSL IP
WSL_IP=$(hostname -I | awk '{print $1}')

if [ -z "$WSL_IP" ]; then
    echo "❌ Could not determine WSL IP address"
    exit 1
fi

echo "📍 WSL2 IP Address: $WSL_IP"
echo ""
echo "═══════════════════════════════════════════════════════════"
echo "Option 1: Use the PowerShell script (Recommended)"
echo "═══════════════════════════════════════════════════════════"
echo ""
echo "1. Open PowerShell as Administrator on Windows"
echo "2. Navigate to this project directory"
echo "3. Run: .\wsl-port-forward.ps1"
echo ""
echo "═══════════════════════════════════════════════════════════"
echo "Option 2: Manual PowerShell commands"
echo "═══════════════════════════════════════════════════════════"
echo ""
echo "Run these commands in PowerShell (as Administrator):"
echo ""
echo "# Forward Laravel app port"
echo "netsh interface portproxy add v4tov4 listenport=8000 listenaddress=0.0.0.0 connectport=8000 connectaddress=$WSL_IP"
echo "netsh advfirewall firewall add rule name=\"WSL2 Laravel\" dir=in action=allow protocol=TCP localport=8000"
echo ""
echo "# Forward phpMyAdmin port"
echo "netsh interface portproxy add v4tov4 listenport=8081 listenaddress=0.0.0.0 connectport=8081 connectaddress=$WSL_IP"
echo "netsh advfirewall firewall add rule name=\"WSL2 phpMyAdmin\" dir=in action=allow protocol=TCP localport=8081"
echo ""
echo "═══════════════════════════════════════════════════════════"
echo "To remove port forwarding later:"
echo "═══════════════════════════════════════════════════════════"
echo ""
echo "netsh interface portproxy delete v4tov4 listenport=8000 listenaddress=0.0.0.0"
echo "netsh interface portproxy delete v4tov4 listenport=8081 listenaddress=0.0.0.0"
echo ""

