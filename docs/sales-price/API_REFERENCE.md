# Sales Price API Reference

## Overview

This document provides a comprehensive reference for the Sales Price module's API endpoints, Livewire component methods, and data structures.

## Livewire Component API

### Component: `App\Livewire\SalesPrice\Index`

#### Public Properties

| Property | Type | Default | Description |
|----------|------|---------|-------------|
| `$search` | string | `''` | Search query for filtering records |
| `$perPage` | int | `10` | Number of records per page |
| `$showCreateModal` | bool | `false` | Controls create modal visibility |
| `$showEditModal` | bool | `false` | Controls edit modal visibility |
| `$editingId` | int\|null | `null` | ID of record being edited |
| `$description` | string | `''` | Description input field |
| `$less_percentage` | float\|string | `''` | Percentage input field |
| `$pricing_note` | string | `''` | Pricing note input field |

#### Validation Rules

```php
protected $rules = [
    'description' => 'required|string|max:255',
    'less_percentage' => 'required|numeric|min:0|max:100',
    'pricing_note' => 'nullable|string|max:1000',
];
```

#### Public Methods

##### Modal Management

###### `openCreateModal()`
Opens the create modal and resets the form.

**Returns:** void

**Example:**
```php
// Called when "Add Sales Price" button is clicked
$this->openCreateModal();
```

###### `closeCreateModal()`
Closes the create modal and resets the form.

**Returns:** void

###### `openEditModal($id)`
Opens the edit modal and loads existing data for the specified record.

**Parameters:**
- `$id` (int): The ID of the sales price record to edit

**Returns:** void

**Example:**
```php
$this->openEditModal(5); // Edit record with ID 5
```

###### `closeEditModal()`
Closes the edit modal and resets the form.

**Returns:** void

##### Form Management

###### `resetForm()`
Resets all form fields to their default values.

**Returns:** void

**Resets:**
- `description` → `''`
- `less_percentage` → `''`
- `pricing_note` → `''`
- Clears validation errors

##### CRUD Operations

###### `create()`
Validates input data and creates a new sales price record.

**Returns:** void

**Throws:** ValidationException on validation failure

**Side Effects:**
- Creates new database record
- Shows success flash message
- Closes create modal
- Resets form

**Example:**
```php
// Triggered by form submission in create modal
$this->create();
```

###### `update()`
Validates input data and updates an existing sales price record.

**Returns:** void

**Throws:** ValidationException on validation failure

**Side Effects:**
- Updates database record
- Shows success flash message
- Closes edit modal
- Resets form

**Example:**
```php
// Triggered by form submission in edit modal
$this->update();
```

###### `delete($id)`
Deletes a sales price record after user confirmation.

**Parameters:**
- `$id` (int): The ID of the sales price record to delete

**Returns:** void

**Side Effects:**
- Deletes database record
- Shows success flash message

**Example:**
```php
// Called from delete button with confirmation
$this->delete(5);
```

##### Lifecycle Methods

###### `updatingSearch()`
Resets pagination to page 1 when search query changes.

**Returns:** void

**Side Effects:**
- Resets pagination to first page

###### `render()`
Renders the component view with paginated data.

**Returns:** `\Illuminate\Contracts\View\View`

**Data Passed to View:**
- `salesPrices`: Paginated collection of SalesPrice models

## HTTP Routes

### Route Definition

```php
// File: routes/web.php
Route::get('/sales-price', SalesPriceIndex::class)->name('sales-price.index');
```

### Route Details

| Method | URI | Name | Controller | Middleware |
|--------|-----|------|------------|------------|
| GET | `/sales-price` | `sales-price.index` | `App\Livewire\SalesPrice\Index` | `auth` |

## Data Models

### SalesPrice Model

#### Properties

| Property | Type | Description |
|----------|------|-------------|
| `$fillable` | array | Mass assignable attributes |
| `$casts` | array | Attribute casting definitions |

#### Fillable Attributes

```php
protected $fillable = [
    'description',
    'less_percentage',
    'pricing_note',
];
```

#### Attribute Casting

```php
protected $casts = [
    'less_percentage' => 'decimal:2',
];
```

#### Relationships

Currently, no relationships are defined.

## Request/Response Formats

### Create/Update Request Data

```javascript
{
    "description": "Bulk Discount",
    "less_percentage": "15.50",
    "pricing_note": "Applied to orders over 100 units"
}
```

### Validation Error Response

```javascript
{
    "description": ["The description field is required."],
    "less_percentage": ["The less percentage must be between 0 and 100."]
}
```

### Success Response (Flash Message)

```javascript
{
    "message": "Sales Price created successfully."
}
```

## Error Handling

### Validation Errors

Validation errors are automatically handled by Livewire and displayed in the UI. Common validation errors:

- **Description Required**: `"The description field is required."`
- **Description Too Long**: `"The description may not be greater than 255 characters."`
- **Invalid Percentage**: `"The less percentage must be between 0 and 100."`
- **Note Too Long**: `"The pricing note may not be greater than 1000 characters."`

### Database Errors

Database errors are caught by Laravel's exception handler and may result in:
- 500 Internal Server Error
- Rollback of database transactions
- Error logging

## Events and Listeners

### Livewire Events

The component listens for the following Livewire events:

- **Form Submission**: `wire:submit.prevent="create"`
- **Modal Actions**: `wire:click="openCreateModal"`
- **Search Input**: `wire:model.live.debounce.300ms="search"`

### Browser Events

- **Delete Confirmation**: Browser's native `confirm()` dialog
- **Flash Messages**: Automatic display via Livewire flash system

## Security

### Authentication
- Route protected by `auth` middleware
- Only authenticated users can access the sales price management interface

### Authorization
- No specific authorization policies implemented
- Consider implementing role-based access control for production use

### Input Validation
- Server-side validation using Laravel's validation rules
- Client-side validation via HTML5 attributes
- SQL injection prevention via Eloquent ORM

### CSRF Protection
- Automatic CSRF protection via Laravel
- All forms include CSRF tokens

## Performance Considerations

### Database Queries

#### Main Query (render method)
```sql
SELECT * FROM sales_prices
WHERE description LIKE ?
ORDER BY id DESC
LIMIT ? OFFSET ?
```

**Parameters:**
- Search term (with wildcards)
- Per page limit
- Offset for pagination

#### Edit Query (openEditModal method)
```sql
SELECT * FROM sales_prices WHERE id = ?
```

### Caching Strategy

Currently no caching is implemented. Consider adding:

```php
// Cache frequently accessed data
Cache::remember('sales_prices', 3600, function () {
    return SalesPrice::all();
});
```

### Pagination Optimization

- Uses Laravel's built-in pagination
- Configurable per-page limits: 5, 10, 20, 50, 100
- Search resets to page 1 automatically

## Testing

### Unit Tests

```php
// Example test for create method
public function test_can_create_sales_price()
{
    $data = [
        'description' => 'Test Discount',
        'less_percentage' => 10.00,
        'pricing_note' => 'Test note'
    ];

    Livewire::test(SalesPrice\Index::class)
        ->set($data)
        ->call('create')
        ->assertHasNoErrors()
        ->assertSet('showCreateModal', false);
}
```

### Feature Tests

```php
public function test_user_can_access_sales_price_page()
{
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('sales-price.index'))
        ->assertStatus(200);
}
```

## Monitoring and Logging

### Error Logging

All errors are automatically logged by Laravel's logging system:

```php
// Automatic error logging
Log::error('Sales Price creation failed', [
    'user_id' => auth()->id(),
    'data' => $validatedData,
    'error' => $exception->getMessage()
]);
```

### Activity Logging

Consider implementing activity logging for audit trails:

```php
// Log create operation
activity()
    ->performedOn($salesPrice)
    ->causedBy(auth()->user())
    ->log('Created sales price: ' . $salesPrice->description);
```

## Future API Extensions

### Potential New Endpoints

1. **Bulk Operations**
   ```php
   // Bulk delete
   public function bulkDelete(array $ids)
   ```

2. **Export Functionality**
   ```php
   // Export to CSV
   public function export()
   ```

3. **Import Functionality**
   ```php
   // Import from CSV
   public function import(Request $request)
   ```

### API Versioning

For future API versions, consider:

```php
// Versioned routes
Route::prefix('api/v1')->group(function () {
    Route::apiResource('sales-prices', SalesPriceController::class);
});
```

## Troubleshooting

### Common Issues

1. **Modal Not Opening**
   - Check Alpine.js is loaded
   - Verify Livewire scripts are included

2. **Validation Errors Not Showing**
   - Ensure form fields have `wire:model` directives
   - Check validation rules are properly defined

3. **Search Not Working**
   - Verify `wire:model.live.debounce.300ms` is applied
   - Check database query in `render()` method

4. **Pagination Not Working**
   - Ensure `updatingSearch()` resets page
   - Check `$perPage` property is reactive

### Debug Commands

```bash
# Clear all Laravel caches
php artisan cache:clear
php artisan view:clear
php artisan config:clear

# Check Livewire component status
php artisan livewire:discover

# Debug database queries
php artisan tinker
DB::enableQueryLog();
// Perform actions
dd(DB::getQueryLog());
```

This API reference provides comprehensive documentation for developers working with the Sales Price module, covering all aspects from basic usage to advanced customization and troubleshooting.