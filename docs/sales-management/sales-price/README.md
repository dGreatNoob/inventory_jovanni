# Sales Price Module Documentation

## Overview

The Sales Price module is a comprehensive pricing configuration system that allows administrators to define and manage sales price setups with percentage-based discounts and detailed pricing notes. This module is part of the Sales Management section and provides a flexible way to handle various pricing strategies.

## Features

- **CRUD Operations**: Complete Create, Read, Update, and Delete functionality
- **Percentage-based Pricing**: Configure discount percentages (0-100%)
- **Pricing Notes**: Optional detailed notes for each price configuration
- **Search & Pagination**: Efficient data browsing with search functionality
- **Responsive UI**: Mobile-friendly interface with dark mode support
- **Validation**: Comprehensive input validation and error handling

## Database Schema

### Table: `sales_prices`

| Column | Type | Nullable | Description |
|--------|------|----------|-------------|
| `id` | BIGINT UNSIGNED | NO | Primary key, auto-increment |
| `description` | VARCHAR(255) | NO | Description of the sales price configuration |
| `less_percentage` | DECIMAL(5,2) | NO | Discount percentage (0.00 - 100.00) |
| `pricing_note` | TEXT | YES | Optional detailed notes about the pricing |
| `created_at` | TIMESTAMP | YES | Record creation timestamp |
| `updated_at` | TIMESTAMP | YES | Record update timestamp |

### Indexes

- Primary Key: `id`
- No additional indexes defined

### Relationships

Currently, the `sales_prices` table has no foreign key relationships with other tables. It is designed as a standalone configuration table.

## API Endpoints

### Routes

| Method | URI | Controller | Description |
|--------|-----|------------|-------------|
| GET | `/sales-price` | `App\Livewire\SalesPrice\Index` | Display sales price management interface |

### Livewire Component: `App\Livewire\SalesPrice\Index`

#### Properties

- `$search`: Search query string
- `$perPage`: Number of records per page (default: 10)
- `$showCreateModal`: Boolean to show/hide create modal
- `$showEditModal`: Boolean to show/hide edit modal
- `$editingId`: ID of the record being edited
- `$description`: Description input field
- `$less_percentage`: Percentage input field
- `$pricing_note`: Pricing note input field

#### Methods

- `openCreateModal()`: Opens the create modal and resets form
- `closeCreateModal()`: Closes create modal and resets form
- `openEditModal($id)`: Opens edit modal and loads existing data
- `closeEditModal()`: Closes edit modal and resets form
- `resetForm()`: Resets all form fields
- `create()`: Validates and creates new sales price record
- `update()`: Validates and updates existing sales price record
- `delete($id)`: Deletes a sales price record
- `updatingSearch()`: Resets pagination when search changes

#### Validation Rules

```php
[
    'description' => 'required|string|max:255',
    'less_percentage' => 'required|numeric|min:0|max:100',
    'pricing_note' => 'nullable|string|max:1000',
]
```

## User Interface

### Main Interface

- **Header**: Module title and description
- **Search Bar**: Real-time search functionality
- **Add Button**: Opens create modal
- **Data Table**: Displays sales price records with actions
- **Pagination**: Navigate through records

### Create/Edit Modal

- **Description**: Required text field for price configuration name
- **Less (%)**: Required number field (0-100) for discount percentage
- **Pricing Note**: Optional textarea for detailed notes

### Data Table Columns

1. **Description**: Sales price configuration name
2. **Less (%)**: Discount percentage with 2 decimal places
3. **Pricing Note**: Truncated text with tooltip for full content
4. **Created At**: Record creation date (M d, Y format)
5. **Actions**: Edit and Delete buttons

## Usage Examples

### Creating a Sales Price Configuration

1. Navigate to Sales Management → Sales Price
2. Click "Add Sales Price" button
3. Fill in the required fields:
   - Description: "Bulk Discount"
   - Less (%): 15.50
   - Pricing Note: "Applied to orders over 100 units"
4. Click "Create"

### Editing a Sales Price

1. Click the edit icon in the Actions column
2. Modify the fields as needed
3. Click "Update"

### Deleting a Sales Price

1. Click the delete icon in the Actions column
2. Confirm the deletion in the browser dialog

## Technical Implementation

### File Structure

```
app/
├── Livewire/SalesPrice/
│   └── Index.php
└── Models/
    └── SalesPrice.php

resources/views/livewire/sales-price/
└── index.blade.php

database/migrations/
├── 2025_10_21_105412_create_sales_prices_table.php
└── 2025_10_21_151030_add_pricing_note_to_sales_prices_table.php

routes/
└── web.php
```

### Dependencies

- **Laravel Livewire**: For reactive components
- **Laravel Blade**: For templating
- **Tailwind CSS**: For styling
- **Alpine.js**: For modal interactions (via Livewire)

### Security Considerations

- All operations require authentication
- Input validation on both client and server side
- CSRF protection via Laravel
- SQL injection prevention via Eloquent ORM

## Future Enhancements

Potential improvements for the Sales Price module:

1. **Bulk Operations**: Select multiple records for batch operations
2. **Export Functionality**: CSV/Excel export of sales price data
3. **Audit Trail**: Track changes to sales price configurations
4. **Categories**: Group sales prices by categories
5. **Effective Dates**: Start/end dates for price configurations
6. **Role-based Permissions**: Different access levels for users

## Troubleshooting

### Common Issues

1. **Migration Errors**: Ensure database connection is working
2. **Permission Errors**: Check file permissions for storage directories
3. **Validation Errors**: Ensure all required fields are filled correctly

### Debug Commands

```bash
# Check migration status
php artisan migrate:status

# Clear cache
php artisan cache:clear
php artisan view:clear

# Run specific migration
php artisan migrate --path=database/migrations/filename.php
```

## Support

For technical support or questions about the Sales Price module, please refer to the main project documentation or contact the development team.