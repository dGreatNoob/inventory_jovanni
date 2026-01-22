CREATE DATABASE IF NOT EXISTS inventory_jovanni;
CREATE USER IF NOT EXISTS 'jovanni'@'localhost' IDENTIFIED BY 'secret';
GRANT ALL PRIVILEGES ON inventory_jovanni.* TO 'jovanni'@'localhost';
FLUSH PRIVILEGES;
SELECT 'Database and user created successfully' AS status;

