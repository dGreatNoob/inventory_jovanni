# Finance Expenses Module - Database Tables Analysis

**Route:** `/finance/expenses`  
**Component:** `App\Livewire\Pages\Finance\Expenses`  
**View:** `resources/views/livewire/pages/finance/expenses.blade.php`

## Directly Used Tables

### 1. `finances` (Primary Table)
- **Model:** `App\Models\Finance`
- **Usage:**
  - Main CRUD operations (create, read, update, delete)
  - Store expense records (type = 'expense')
  - Search and filtering (by reference_id, party, category, payment_method, status, remarks)
  - Category filtering
  - Date range filtering
  - Statistics calculation
  - File attachment management
- **Relationships:**
  - None directly used in this module (no branch/agent relationships for expenses)
- **Fields Used:**
  - `id` - Finance ID (primary key, editing, deletion)
  - `type` - Finance type (filtered to 'expense')
  - `reference_id` - Reference number (unique per day, search, display, auto-generation)
  - `party` - Party/vendor name (required, search, display, validation)
  - `date` - Expense date (required, filtering, display, validation)
  - `category` - Expense category (required, search, filtering, display, validation)
  - `amount` - Expense amount (required, validation, display, statistics)
  - `payment_method` - Payment method (required, search, display, validation)
  - `status` - Expense status (required, search, display, validation, default: 'pending')
  - `remarks` - Remarks (nullable, search, display)
  - `file_path` - Receipt file path (nullable, file storage, display, download)
  - `created_at` - Creation timestamp
  - `updated_at` - Update timestamp
- **Methods:**
  - `Finance::where('type', 'expense')` - Filter expenses only
  - `Finance::where('reference_id', 'like', $prefix . '%')` - Find latest reference ID
  - `Finance::where('type', 'expense')->sum('amount')` - Calculate total expenses
  - `Finance::where('type', 'expense')->whereYear('date', ...)->whereMonth('date', ...)->sum('amount')` - Calculate monthly expenses
  - `Finance::where('type', 'expense')->where('status', 'pending')->sum('amount')` - Calculate pending expenses
  - `Finance::where('type', 'expense')->distinct('category')->count('category')` - Count categories
  - `Finance::where('type', 'expense')->where('date', '>=', $startDate)` - Filter by date range
  - `Finance::where('type', 'expense')->where('date', '<=', $endDate)` - Filter by date range
  - `Finance::where('type', 'expense')->where('category', $category)` - Filter by category
  - `Finance::where('type', 'expense')->orderByDesc('date')->paginate($perPage)` - Paginate results
  - `Finance::create([...])` - Create expense
  - `Finance::findOrFail($id)->update([...])` - Update expense
  - `Finance::findOrFail($id)->delete()` - Delete expense
- **Features:**
  - Reference ID auto-generation (format: EXPyymmdd###, e.g., EXP250721001)
  - File attachment support (receipt upload, view, download)
  - File storage in `storage/app/public/expense-receipts/`
  - File deletion on expense update/delete
  - Comprehensive filtering (category, date range)
  - Search across multiple fields
  - Statistics dashboard (total, this month, pending, categories)
  - Category management (transport, utilities, office supplies, meals, equipment, other)
  - Payment method management (cash, bank transfer, credit card, debit card, digital wallet, check)

## File Storage

### Receipt Files
- **Storage Location:** `storage/app/public/expense-receipts/`
- **Storage Disk:** `public`
- **File Size Limit:** 10MB max
- **File Management:**
  - Upload on create/update
  - Delete old file when updating with new file
  - Delete file when expense is deleted
  - View receipt in modal
  - Download receipt with custom filename (reference_id.extension)
- **File URL Generation:** Uses `asset('storage/' . $file_path)` for reliable public file URLs
- **File Validation:** Checks if file exists before viewing/downloading

## Summary

**Total Tables Used: 1**

1. ✅ `finances` - Primary table for expense management

## Notes

- **Reference ID Generation:** Auto-generates reference ID in format EXPyymmdd### (e.g., EXP250721001)
- **File Management:** Supports receipt file upload, view, and download
- **File Storage:** Files stored in `storage/app/public/expense-receipts/`
- **File Cleanup:** Automatically deletes old files when updating or deleting expenses
- **Category Management:** Predefined categories (transport, utilities, office supplies, meals, equipment, other)
- **Payment Methods:** Predefined payment methods (cash, bank transfer, credit card, debit card, digital wallet, check)
- **Status Management:** Default status is 'pending'
- **Comprehensive Filtering:** Filter by category and date range
- **Search Functionality:** Searches across reference_id, party, category, payment_method, status, and remarks
- **Statistics Dashboard:** Tracks total expenses, this month's expenses, pending expenses, and category count
- **Date Defaults:** Default date is current date
- **No Relationships:** Expenses are standalone records (no branch/agent relationships)
- **File Size Limit:** Maximum file size is 10MB
- **Receipt Viewing:** Can view receipts in modal with file preview
- **Receipt Download:** Can download receipts with custom filename based on reference_id

