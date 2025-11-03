# Sales Price Database Schema Documentation

## Table Overview

### `sales_prices`

The `sales_prices` table stores configuration data for sales price setups, including discount percentages and optional pricing notes.

## Schema Definition

### Migration: `2025_10_21_105412_create_sales_prices_table.php`

```php
Schema::create('sales_prices', function (Blueprint $table) {
    $table->id();
    $table->string('description');
    $table->decimal('less_percentage', 5, 2);
    $table->timestamps();
});
```

### Migration: `2025_10_21_151030_add_pricing_note_to_sales_prices_table.php`

```php
Schema::table('sales_prices', function (Blueprint $table) {
    $table->text('pricing_note')->nullable()->after('less_percentage');
});
```

## Column Specifications

| Column Name | Data Type | Length | Nullable | Default | Description |
|-------------|-----------|--------|----------|---------|-------------|
| `id` | BIGINT UNSIGNED | - | NO | AUTO_INCREMENT | Primary key identifier |
| `description` | VARCHAR | 255 | NO | NULL | Human-readable description of the sales price configuration |
| `less_percentage` | DECIMAL | 5,2 | NO | NULL | Discount percentage (0.00 to 100.00) |
| `pricing_note` | TEXT | - | YES | NULL | Optional detailed notes about the pricing configuration |
| `created_at` | TIMESTAMP | - | YES | NULL | Record creation timestamp |
| `updated_at` | TIMESTAMP | - | YES | NULL | Record last update timestamp |

## Indexes and Constraints

### Primary Key
- **Name**: `PRIMARY`
- **Type**: Primary Key
- **Columns**: `id`
- **Auto Increment**: Yes

### Unique Constraints
None defined.

### Foreign Key Constraints
None defined.

### Indexes
- **Primary Key Index**: Automatically created on `id` column

## Data Types Explanation

### DECIMAL(5,2)
- **Total Digits**: 5 (including decimal places)
- **Decimal Places**: 2
- **Range**: 0.00 to 999.99
- **Usage**: Stores percentage values with 2 decimal precision

### VARCHAR(255)
- **Maximum Length**: 255 characters
- **Usage**: Short descriptive text
- **Encoding**: UTF-8 (default Laravel charset)

### TEXT
- **Maximum Length**: 65,535 characters
- **Usage**: Longer descriptive content
- **Encoding**: UTF-8 (default Laravel charset)

## Sample Data

### Example Records

```sql
-- Bulk discount configuration
INSERT INTO sales_prices (description, less_percentage, pricing_note, created_at, updated_at)
VALUES ('Bulk Order Discount', 15.50, 'Applied to orders over 100 units for premium customers', NOW(), NOW());

-- Seasonal discount
INSERT INTO sales_prices (description, less_percentage, pricing_note, created_at, updated_at)
VALUES ('Holiday Sale', 25.00, 'December holiday promotion - ends December 31st', NOW(), NOW());

-- Standard discount
INSERT INTO sales_prices (description, less_percentage, pricing_note, created_at, updated_at)
VALUES ('Loyalty Discount', 10.00, NULL, NOW(), NOW());
```

## Relationships

### Current Relationships
The `sales_prices` table currently has no foreign key relationships. It is designed as a standalone configuration table.

### Potential Future Relationships
```sql
-- Example of potential relationships (not implemented)
ALTER TABLE sales_prices ADD CONSTRAINT fk_sales_prices_category
FOREIGN KEY (category_id) REFERENCES categories(id);

ALTER TABLE sales_prices ADD CONSTRAINT fk_sales_prices_created_by
FOREIGN KEY (created_by) REFERENCES users(id);
```

## Business Rules and Constraints

### Validation Rules (Application Level)

```php
[
    'description' => 'required|string|max:255',
    'less_percentage' => 'required|numeric|min:0|max:100',
    'pricing_note' => 'nullable|string|max:1000',
]
```

### Business Logic Constraints

1. **Percentage Range**: Must be between 0.00 and 100.00
2. **Description Required**: Cannot be null or empty
3. **Pricing Note Optional**: Can be null, but if provided, limited to 1000 characters
4. **Unique Descriptions**: While not enforced at database level, consider business rule for unique descriptions

## Performance Considerations

### Current Indexes
- Primary key index on `id` (automatic)

### Recommended Additional Indexes

```sql
-- For search functionality
CREATE INDEX idx_sales_prices_description ON sales_prices(description);

-- For percentage-based queries
CREATE INDEX idx_sales_prices_percentage ON sales_prices(less_percentage);

-- For date-based queries
CREATE INDEX idx_sales_prices_created_at ON sales_prices(created_at);
```

### Query Optimization

```sql
-- Optimized search query
SELECT * FROM sales_prices
WHERE description LIKE '%search_term%'
ORDER BY created_at DESC
LIMIT 10 OFFSET 0;

-- Optimized percentage range query
SELECT * FROM sales_prices
WHERE less_percentage BETWEEN 10.00 AND 20.00
ORDER BY less_percentage ASC;
```

## Migration History

### Migration Timeline

1. **2025-10-21 10:54:12**: Initial table creation
   - Created `sales_prices` table with basic columns
   - Added primary key and timestamps

2. **2025-10-21 15:10:30**: Added pricing note column
   - Added `pricing_note` TEXT column
   - Positioned after `less_percentage` column

### Rollback Strategy

```php
-- Rollback second migration
Schema::table('sales_prices', function (Blueprint $table) {
    $table->dropColumn('pricing_note');
});

-- Rollback first migration
Schema::dropIfExists('sales_prices');
```

## Data Integrity

### Constraints Implementation

```sql
-- Check constraint for percentage range (if supported by database)
ALTER TABLE sales_prices
ADD CONSTRAINT chk_percentage_range
CHECK (less_percentage >= 0.00 AND less_percentage <= 100.00);

-- Not null constraint (already implemented via Laravel migration)
ALTER TABLE sales_prices MODIFY description VARCHAR(255) NOT NULL;
ALTER TABLE sales_prices MODIFY less_percentage DECIMAL(5,2) NOT NULL;
```

## Backup and Recovery

### Table Size Estimation
- **Row Size**: ~300-400 bytes per record (depending on content)
- **Index Size**: ~50-100 bytes per record
- **Total per Record**: ~350-500 bytes

### Backup Commands

```bash
# MySQL dump for sales_prices table
mysqldump -u username -p database_name sales_prices > sales_prices_backup.sql

# Laravel backup (if using spatie/laravel-backup)
php artisan backup:run --only-db
```

## Monitoring and Maintenance

### Health Check Queries

```sql
-- Check table structure
DESCRIBE sales_prices;

-- Check data integrity
SELECT
    COUNT(*) as total_records,
    MIN(less_percentage) as min_percentage,
    MAX(less_percentage) as max_percentage,
    AVG(less_percentage) as avg_percentage
FROM sales_prices;

-- Check for invalid data
SELECT * FROM sales_prices
WHERE less_percentage < 0 OR less_percentage > 100;

-- Check recent activity
SELECT * FROM sales_prices
ORDER BY updated_at DESC
LIMIT 10;
```

### Maintenance Tasks

```sql
-- Analyze table for optimization
ANALYZE TABLE sales_prices;

-- Optimize table
OPTIMIZE TABLE sales_prices;

-- Check for fragmentation
SHOW TABLE STATUS LIKE 'sales_prices';
```

## Security Considerations

### Access Control
- Table access should be restricted to authenticated users only
- Consider row-level security for multi-tenant applications
- Audit trail recommended for create/update/delete operations

### Data Validation
- Always validate input at application level
- Use prepared statements to prevent SQL injection
- Sanitize text inputs to prevent XSS attacks

## Future Schema Evolution

### Potential Enhancements

1. **Category Support**
   ```sql
   ALTER TABLE sales_prices ADD COLUMN category_id BIGINT UNSIGNED NULL;
   ALTER TABLE sales_prices ADD CONSTRAINT fk_sales_prices_category FOREIGN KEY (category_id) REFERENCES categories(id);
   ```

2. **Effective Date Range**
   ```sql
   ALTER TABLE sales_prices ADD COLUMN effective_from DATE NULL;
   ALTER TABLE sales_prices ADD COLUMN effective_to DATE NULL;
   ```

3. **Status Management**
   ```sql
   ALTER TABLE sales_prices ADD COLUMN is_active BOOLEAN DEFAULT TRUE;
   ```

4. **Audit Fields**
   ```sql
   ALTER TABLE sales_prices ADD COLUMN created_by BIGINT UNSIGNED NULL;
   ALTER TABLE sales_prices ADD COLUMN updated_by BIGINT UNSIGNED NULL;
   ```

This schema provides a solid foundation for sales price management while allowing for future enhancements and scalability.