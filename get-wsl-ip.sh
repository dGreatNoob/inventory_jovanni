#!/bin/bash
# Helper script to get WSL2 IP address for accessing from Windows browser

WSL_IP=$(hostname -I | awk '{print $1}')

if [ -z "$WSL_IP" ]; then
    WSL_IP=$(ip -4 addr show scope global | grep inet | awk '{print $2}' | cut -d/ -f1 | head -1)
fi

if [ ! -z "$WSL_IP" ]; then
    echo ""
    echo "🌐 Access your Laravel app from Windows browser:"
    echo "   http://${WSL_IP}:8000"
    echo ""
    echo "📋 Or try (if port forwarding works):"
    echo "   http://localhost:8000"
    echo ""
else
    echo "❌ Could not determine WSL IP address"
    exit 1
fi

