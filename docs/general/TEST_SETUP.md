# Test Setup Guide

This document provides comprehensive instructions for setting up and running tests in the Inventory Jovanni application.

## Table of Contents

- [Prerequisites](#prerequisites)
- [Test Database Setup](#test-database-setup)
- [Configuration](#configuration)
- [Running Tests](#running-tests)
- [Test Structure](#test-structure)
- [CI/CD Integration](#cicd-integration)
- [Troubleshooting](#troubleshooting)
- [Best Practices](#best-practices)

## Prerequisites

Before setting up tests, ensure you have:

1. **PHP 8.3+** installed
2. **Composer** installed
3. **MySQL 8.0+** running (local or Docker)
4. **Node.js 22+** installed (for frontend asset building in CI/CD)
5. **Pest PHP** testing framework (installed via Composer)

### Verify Installation

```bash
php --version        # Should be 8.3 or higher
composer --version   # Should be 2.x
mysql --version      # Should be 8.0 or higher
node --version       # Should be 22.x or higher
```

## Test Database Setup

### Local Development

The test suite requires a separate database (`inventory_jovanni_test`) to avoid affecting your development data.

#### Step 1: Connect to MySQL

Connect to your MySQL instance using a privileged user (usually `root`):

```bash
# If using Docker MySQL
mysql -u root -p -h 127.0.0.1 -P 3307

# If using local MySQL
mysql -u root -p
```

#### Step 2: Create Test Database

```sql
CREATE DATABASE inventory_jovanni_test 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;
```

#### Step 3: Grant Privileges

Grant privileges to your application database user:

```sql
-- Replace 'jovanni' with your actual database username
GRANT ALL PRIVILEGES ON inventory_jovanni_test.* TO 'jovanni'@'%';
FLUSH PRIVILEGES;
```

#### Step 4: Verify Database Creation

```sql
SHOW DATABASES LIKE 'inventory_jovanni_test';
EXIT;
```

### Using Docker

If you're using Docker Compose, you can create the test database through the MySQL container:

```bash
# Access MySQL container
docker exec -it inventory-jovanni-db mysql -u root -p

# Then follow steps 2-4 above
```

## Configuration

### phpunit.xml

The test configuration is located in `phpunit.xml` at the project root. Key settings:

```xml
<env name="DB_CONNECTION" value="mysql"/>
<env name="DB_HOST" value="127.0.0.1"/>
<env name="DB_PORT" value="3307"/>
<env name="DB_DATABASE" value="inventory_jovanni_test"/>
<env name="DB_USERNAME" value="jovanni"/>
<env name="DB_PASSWORD" value="secret"/>
```

**Important:** These credentials are configured for **local development**. CI/CD workflows automatically override these values.

### .env.testing

An optional `.env.testing` file can be created for additional test environment variables:

```env
APP_ENV=testing
APP_DEBUG=true
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=inventory_jovanni_test
DB_USERNAME=jovanni
DB_PASSWORD=secret
```

**Note:** `phpunit.xml` environment variables take precedence over `.env.testing`.

### Updating Database Credentials

If your local MySQL uses different credentials:

1. **Update `phpunit.xml`:**
   ```xml
   <env name="DB_PORT" value="YOUR_PORT"/>
   <env name="DB_USERNAME" value="YOUR_USERNAME"/>
   <env name="DB_PASSWORD" value="YOUR_PASSWORD"/>
   ```

2. **Or create `.env.testing`:**
   ```env
   DB_PORT=YOUR_PORT
   DB_USERNAME=YOUR_USERNAME
   DB_PASSWORD=YOUR_PASSWORD
   ```

## Running Tests

### Initial Setup

Before running tests for the first time:

```bash
# 1. Install dependencies (if not already done)
composer install

# 2. Run database migrations on test database
php artisan migrate --database=mysql --env=testing

# 3. (Optional) Seed test data
php artisan db:seed --database=mysql --env=testing
```

### Running All Tests

```bash
# Using Pest (recommended)
./vendor/bin/pest

# Or using PHPUnit
./vendor/bin/phpunit
```

### Running Specific Test Suites

```bash
# Run only unit tests
./vendor/bin/pest --testsuite=Unit

# Run only feature tests
./vendor/bin/pest --testsuite=Feature
```

### Running Specific Tests

```bash
# Run a specific test file
./vendor/bin/pest tests/Feature/ProductManagementTest.php

# Run a specific test method
./vendor/bin/pest --filter "product can be created"

# Run tests matching a pattern
./vendor/bin/pest --filter "Product"
```

### Running Tests with Coverage

```bash
# Generate code coverage report
./vendor/bin/pest --coverage

# Generate HTML coverage report
./vendor/bin/pest --coverage --min=80
```

## Test Structure

### Directory Structure

```
tests/
├── Feature/              # Feature/integration tests
│   ├── Auth/            # Authentication tests
│   ├── Settings/        # Settings tests
│   ├── ProductManagementTest.php
│   ├── BranchInventoryAuditTest.php
│   ├── PurchaseOrderTest.php
│   └── SalesManagementTest.php
├── Unit/                # Unit tests
│   └── ExampleTest.php
├── Pest.php            # Pest configuration
└── TestCase.php        # Base test case
```

### Test Types

#### Feature Tests

Feature tests test complete workflows and interactions:

- **ProductManagementTest**: Product CRUD operations
- **BranchInventoryAuditTest**: Inventory audit variance detection
- **PurchaseOrderTest**: Purchase order creation and status updates
- **SalesManagementTest**: Sales recording and activity logging

#### Unit Tests

Unit tests test individual components in isolation:

- Currently minimal unit test coverage
- Focus on business logic and helper methods

### Writing Tests

#### Basic Test Structure

```php
<?php

use App\Models\Product;
use App\Models\Category;

test('product can be created', function () {
    $category = Category::factory()->create();
    
    $product = Product::factory()->create([
        'category_id' => $category->id,
        'name' => 'Test Product',
    ]);

    expect($product)->toBeInstanceOf(Product::class);
    expect($product->name)->toBe('Test Product');
});
```

#### Using Factories

```php
// Create a single model
$product = Product::factory()->create();

// Create with specific attributes
$product = Product::factory()->create([
    'name' => 'Custom Product',
    'price' => 100.00,
]);

// Create multiple
$products = Product::factory()->count(5)->create();
```

#### Available Factories

- `ProductFactory`
- `CategoryFactory`
- `SupplierFactory`
- `BranchFactory`
- `BranchAllocationFactory`
- `BranchAllocationItemFactory`
- `ShipmentFactory`
- `PurchaseOrderFactory`
- `PurchaseOrderItemFactory`
- `BatchAllocationFactory`
- `UserFactory`
- `DepartmentFactory`

## CI/CD Integration

### GitHub Actions

Tests run automatically on:

- **Push** to `dev` or `main` branches
- **Pull Requests** targeting `dev` or `main`
- **Before deployment** (in deploy workflow)

### Workflow Configuration

The CI/CD workflows (`.github/workflows/tests.yml` and `.github/workflows/deploy.yml`) automatically:

1. Set up MySQL service (port 3306, root/root)
2. Override `phpunit.xml` database credentials for CI/CD
3. Run migrations
4. Run tests
5. Report results

### Local vs CI/CD Differences

| Setting | Local Development | CI/CD |
|--------|------------------|-------|
| Database Port | 3307 | 3306 |
| Database User | jovanni | root |
| Database Password | secret | root |
| MySQL Source | Local/Docker | GitHub Actions Service |

**Note:** CI/CD workflows use `sed` commands to automatically update `phpunit.xml` before running tests.

## Troubleshooting

### Common Issues

#### 1. Database Connection Failed

**Error:**
```
SQLSTATE[HY000] [1045] Access denied for user 'jovanni'@'localhost'
```

**Solutions:**
- Verify MySQL is running: `mysql -u jovanni -psecret -h 127.0.0.1 -P 3307 -e "SELECT 1;"`
- Check database credentials in `phpunit.xml`
- Ensure test database exists: `SHOW DATABASES LIKE 'inventory_jovanni_test';`
- Verify user has privileges: `SHOW GRANTS FOR 'jovanni'@'%';`

#### 2. Test Database Doesn't Exist

**Error:**
```
SQLSTATE[HY000] [1049] Unknown database 'inventory_jovanni_test'
```

**Solution:**
Follow the [Test Database Setup](#test-database-setup) section to create the database.

#### 3. Migration Errors

**Error:**
```
SQLSTATE[42S01]: Base table or view already exists
```

**Solutions:**
```bash
# Drop and recreate test database
mysql -u root -p -e "DROP DATABASE inventory_jovanni_test;"
mysql -u root -p -e "CREATE DATABASE inventory_jovanni_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Re-run migrations
php artisan migrate --database=mysql --env=testing
```

#### 4. Factory Not Found

**Error:**
```
Call to undefined method App\Models\Product::factory()
```

**Solution:**
- Ensure model uses `HasFactory` trait:
  ```php
  use Illuminate\Database\Eloquent\Factories\HasFactory;
  
  class Product extends Model
  {
      use HasFactory;
  }
  ```
- Verify factory exists: `database/factories/ProductFactory.php`

#### 5. Permission Denied Errors

**Error:**
```
Permission denied in vendor/pestphp/pest-plugin-mutate/src/Cache/FileStore.php
```

**Solution:**
```bash
# Fix vendor directory permissions
chmod -R 755 vendor/
chown -R $(whoami):$(whoami) vendor/
```

### Debugging Tests

#### Enable Verbose Output

```bash
# Show detailed test output
./vendor/bin/pest --verbose

# Stop on first failure
./vendor/bin/pest --stop-on-failure
```

#### Debug Specific Test

```bash
# Run with debug output
./vendor/bin/pest --filter "test name" --verbose

# Use dd() or dump() in test code
test('debug test', function () {
    $product = Product::factory()->create();
    dump($product->toArray());
    dd('Stop here');
});
```

#### Check Database State

```bash
# Connect to test database
mysql -u jovanni -psecret -h 127.0.0.1 -P 3307 inventory_jovanni_test

# Check tables
SHOW TABLES;

# Check specific table
SELECT * FROM products LIMIT 5;
```

## Best Practices

### 1. Test Isolation

- Each test should be independent
- Use database transactions or refresh database between tests
- Don't rely on test execution order

### 2. Factory Usage

- Use factories instead of manual model creation
- Create related models through factory relationships
- Use factory states for variations

### 3. Test Naming

- Use descriptive test names: `test('product can be created')`
- Follow pattern: `[action] [subject] [expected outcome]`
- Group related tests in the same file

### 4. Assertions

- Use Pest's `expect()` syntax for readability
- Test both positive and negative cases
- Verify edge cases and boundary conditions

### 5. Performance

- Keep tests fast (avoid unnecessary database queries)
- Use `factory()->make()` instead of `factory()->create()` when possible
- Clean up test data after tests complete

### 6. Coverage

- Aim for meaningful coverage, not just high percentages
- Focus on critical business logic
- Test error handling and edge cases

## Additional Resources

- [Pest PHP Documentation](https://pestphp.com/docs)
- [Laravel Testing Documentation](https://laravel.com/docs/testing)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Factory Documentation](https://laravel.com/docs/eloquent-factories)

## Quick Reference

### Common Commands

```bash
# Setup
php artisan migrate --database=mysql --env=testing

# Run all tests
./vendor/bin/pest

# Run specific test
./vendor/bin/pest --filter "test name"

# Run with coverage
./vendor/bin/pest --coverage

# Run only failed tests
./vendor/bin/pest --only-failed
```

### Database Commands

```bash
# Create test database
mysql -u root -p -e "CREATE DATABASE inventory_jovanni_test CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Grant privileges
mysql -u root -p -e "GRANT ALL PRIVILEGES ON inventory_jovanni_test.* TO 'jovanni'@'%'; FLUSH PRIVILEGES;"

# Drop test database (careful!)
mysql -u root -p -e "DROP DATABASE inventory_jovanni_test;"
```

---

**Last Updated:** January 2026  
**Maintained By:** Development Team
