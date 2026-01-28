# Test Database Setup Guide

## Local Development

The test database configuration is set up for local development using your existing MySQL credentials.

### Current Configuration (phpunit.xml)
- **Host:** 127.0.0.1
- **Port:** 3307
- **Database:** inventory_jovanni_test
- **Username:** jovanni
- **Password:** secret

### Creating the Test Database

Since the `jovanni` user may not have CREATE DATABASE privileges, you have two options:

#### Option 1: Create Database Manually (Recommended)
Connect to MySQL with a user that has CREATE DATABASE privileges (usually `root`):

```bash
# Connect to MySQL (adjust credentials as needed)
mysql -u root -p -h 127.0.0.1 -P 3307

# Create the test database
CREATE DATABASE inventory_jovanni_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Grant privileges to jovanni user
GRANT ALL PRIVILEGES ON inventory_jovanni_test.* TO 'jovanni'@'%';
FLUSH PRIVILEGES;

# Exit MySQL
EXIT;
```

#### Option 2: Use Existing Database (Not Recommended)
If you cannot create a separate test database, you can temporarily use the main database for testing. **Warning:** This will modify your production data!

Update `phpunit.xml` to use `inventory_jovanni` instead of `inventory_jovanni_test`.

### Running Tests

After the database is set up:

```bash
# Run migrations on test database
php artisan migrate --database=mysql --env=testing

# Run tests
./vendor/bin/pest
```

## CI/CD (GitHub Actions)

The GitHub Actions workflows automatically:
1. Create the test database using the MySQL service
2. Update `phpunit.xml` to use CI/CD credentials (`root`/`root` on port `3306`)
3. Run migrations and tests

No manual setup required for CI/CD.
