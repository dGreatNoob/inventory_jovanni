-- Grant privileges to jovanni user from any host
CREATE USER IF NOT EXISTS 'jovanni'@'%' IDENTIFIED WITH mysql_native_password BY 'secret';
GRANT ALL PRIVILEGES ON inventory_jovanni.* TO 'jovanni'@'%';
FLUSH PRIVILEGES;

-- Ensure root can connect from any host
CREATE USER IF NOT EXISTS 'root'@'%' IDENTIFIED WITH mysql_native_password BY 'rootsecret';
GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' WITH GRANT OPTION;
FLUSH PRIVILEGES;


