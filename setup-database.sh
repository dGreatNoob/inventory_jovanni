#!/bin/bash

# Setup script for CliqueHA Inventory Management System
# This script will create the database and user for the application

echo "=========================================="
echo "  CliqueHA Database Setup"
echo "=========================================="
echo ""
echo "This script will create the MySQL database and user."
echo "You will be prompted for your MySQL root password."
echo ""

# Create database and user
sudo mysql -u root << EOF
-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS gentle;

-- Create user if it doesn't exist
CREATE USER IF NOT EXISTS 'gentlewalker'@'localhost' IDENTIFIED BY 'secret';

-- Grant privileges
GRANT ALL PRIVILEGES ON gentle.* TO 'gentlewalker'@'localhost';

-- Flush privileges
FLUSH PRIVILEGES;

-- Show databases to confirm
SHOW DATABASES;
EOF

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Database setup completed successfully!"
    echo ""
    echo "Database: gentle"
    echo "User: gentlewalker"
    echo "Password: secret"
    echo ""
else
    echo ""
    echo "❌ Database setup failed. Please check your MySQL root credentials."
    echo ""
    exit 1
fi
