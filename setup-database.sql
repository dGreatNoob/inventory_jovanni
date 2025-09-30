-- CliqueHA Database Setup SQL
-- Run this file as MySQL root user

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

-- Use the database
USE gentle;
