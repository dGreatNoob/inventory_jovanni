# WSL2 Port Forwarding to Windows

This guide explains how to forward ports from WSL2 to Windows so you can access your Laravel app at `localhost:8000` from your Windows browser.

## Quick Setup (Recommended)

### Method 1: PowerShell Script (Easiest)

1. **Open PowerShell as Administrator** on Windows
   - Right-click PowerShell → "Run as Administrator"

2. **Navigate to your project directory**
   ```powershell
   cd \\wsl$\Ubuntu\home\biiieem\repos\inventory_jovanni
   ```
   Or use the Windows path if you've set it up.

3. **Run the port forwarding script**
   ```powershell
   .\wsl-port-forward.ps1
   ```

4. **Access your app** in Windows browser:
   - Laravel App: `http://localhost:8000`
   - phpMyAdmin: `http://localhost:8081`

### Method 2: Manual Setup

If you prefer to set it up manually, run the helper script in WSL:

```bash
./wsl-port-forward.sh
```

This will show you the exact commands to run in PowerShell.

## How It Works

The script:
1. Detects your WSL2 IP address automatically
2. Forwards ports 8000 and 8081 from WSL2 to Windows localhost
3. Configures Windows Firewall to allow the connections

## Important Notes

- **Port forwarding is temporary**: If you restart WSL2, the IP address may change and you'll need to run the script again
- **Run as Administrator**: The PowerShell script must be run with administrator privileges
- **Firewall**: The script automatically adds firewall rules, but Windows may prompt you for permission

## Troubleshooting

### Port forwarding doesn't work

1. **Check if WSL2 is running**:
   ```powershell
   wsl -l -v
   ```

2. **Verify WSL IP address**:
   In WSL, run:
   ```bash
   hostname -I
   ```

3. **Check existing port forwards**:
   ```powershell
   netsh interface portproxy show all
   ```

4. **Remove and re-add port forwarding**:
   ```powershell
   netsh interface portproxy delete v4tov4 listenport=8000 listenaddress=0.0.0.0
   netsh interface portproxy delete v4tov4 listenport=8081 listenaddress=0.0.0.0
   ```
   Then run the script again.

### Firewall blocking connections

If Windows Firewall is blocking connections:
1. Open Windows Defender Firewall
2. Check if rules named "WSL2 Port Forward 8000" and "WSL2 Port Forward 8081" exist
3. The script should create these automatically, but you can manually allow them if needed

## Alternative: Use WSL IP directly

If port forwarding is too complicated, you can always access your app directly using the WSL IP address:

1. In WSL, run: `./get-wsl-ip.sh`
2. Use that IP in your Windows browser: `http://<WSL_IP>:8000`

This IP may change when WSL restarts, but it works immediately without any setup.

