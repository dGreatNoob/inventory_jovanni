# Finance Payments Module - Database Tables Analysis

**Route:** `/finance/payments`  
**Component:** `App\Livewire\Pages\Finance\Payments`  
**View:** `resources/views/livewire/pages/finance/payments.blade.php`

## Directly Used Tables

### 1. `payments` (Primary Table)
- **Model:** `App\Models\Payment`
- **Usage:**
  - Main CRUD operations (create, read, update, delete)
  - Store payment records linked to payables/receivables
  - Search and filtering (by payment_ref, payment_method, remarks, finance reference_id)
  - Payment method filtering
  - Status filtering
  - Date range filtering
  - Balance management (updates finance balance)
- **Relationships:**
  - `belongsTo(Finance::class)` - Links payment to payable/receivable record
- **Fields Used:**
  - `id` - Payment ID (primary key, editing, deletion)
  - `payment_ref` - Payment reference number (unique per day, search, display, auto-generation)
  - `amount` - Payment amount (required, validation, display, balance calculation)
  - `payment_date` - Payment date (required, filtering, display, validation)
  - `payment_method` - Payment method (required, search, filtering, display, validation)
  - `finance_id` - Foreign key to finances table (required, validation, relationship)
  - `status` - Payment status (required, display, validation, auto-calculated)
  - `remarks` - Remarks (nullable, search, display)
  - `created_at` - Creation timestamp
  - `updated_at` - Update timestamp
- **Methods:**
  - `Payment::where('payment_ref', 'like', $prefix . '%')` - Find latest payment reference ID
  - `Payment::with('finance')` - Eager load finance relationship
  - `Payment::where('payment_ref', 'like', "%{$search}%")` - Search by payment reference
  - `Payment::where('payment_method', 'like', "%{$search}%")` - Search by payment method
  - `Payment::where('remarks', 'like', "%{$search}%")` - Search by remarks
  - `Payment::whereHas('finance', function ($fq) { $fq->where('reference_id', 'like', "%{$search}%"); })` - Search by finance reference ID
  - `Payment::where('payment_method', $filterPaymentMethod)` - Filter by payment method
  - `Payment::where('status', $filterStatus)` - Filter by status
  - `Payment::whereBetween('payment_date', [$filterDateFrom, $filterDateTo])` - Filter by date range
  - `Payment::orderByDesc('payment_date')->paginate($perPage)` - Paginate results
  - `Payment::create([...])` - Create payment
  - `Payment::findOrFail($id)->update([...])` - Update payment
  - `Payment::findOrFail($id)->delete()` - Delete payment
- **Features:**
  - Payment reference ID auto-generation (format: PAYyymmdd###, e.g., PAY250721001)
  - Linked to payables/receivables (finances table)
  - Balance management (updates finance balance on create/update/delete)
  - Amount validation (cannot exceed finance balance)
  - Status auto-calculation (fully_paid, partially_paid, overdue, not_paid)
  - Comprehensive filtering (payment method, status, date range)
  - Search across multiple fields (payment_ref, payment_method, remarks, finance reference_id)
  - Balance restoration on payment update/delete
  - Available finances dropdown (only shows finances with balance > 0)
  - Default amount set to finance balance when finance is selected

### 2. `finances` (Related Table)
- **Model:** `App\Models\Finance`
- **Usage:**
  - Load available payables/receivables for payment linking
  - Update balance when payment is created/updated/deleted
  - Display finance information in payment records
  - Search payments by finance reference_id
- **Relationships:**
  - `hasMany(Payment::class)` - One finance record can have multiple payments
  - `belongsTo(Branch::class)` - Optional branch relationship (not directly used in Payments module)
  - `belongsTo(Agent::class)` - Optional agent relationship (not directly used in Payments module)
- **Fields Used:**
  - `id` - Finance ID (foreign key in payments table, selection, validation)
  - `reference_id` - Finance reference ID (display, search)
  - `type` - Finance type (payable/receivable, display)
  - `balance` - Finance balance (required for payment validation, updated on payment create/update/delete)
  - `amount` - Finance amount (used in status calculation)
  - `due_date` - Finance due date (used in status calculation for overdue check)
- **Methods:**
  - `Finance::where('balance', '>', 0)->orderByDesc('date')->get()` - Load available finances for dropdown
  - `Finance::find($id)` - Find finance record
  - `Finance::find($id)->balance` - Get finance balance
  - `Finance::find($id)->balance -= $amount; $finance->save()` - Update balance on payment create
  - `Finance::find($id)->balance += $amount; $finance->save()` - Restore balance on payment update/delete
  - `Finance::where('reference_id', 'like', "%{$search}%")` - Search by finance reference ID
- **Features:**
  - Only finances with balance > 0 are shown in dropdown
  - Balance is automatically updated when payment is created/updated/deleted
  - Balance restoration on payment update/delete (adds back original payment amount)
  - Finance information displayed in payment records
  - Finance reference ID searchable in payment search

## Payment Status Calculation

The payment status is automatically calculated based on:
- **FULLY_PAID:** Remaining balance after payment = 0
- **PARTIALLY_PAID:** Remaining balance > 0 and < original finance amount
- **OVERDUE:** Finance has due_date and current date is after due_date
- **NOT_PAID:** Default status for other cases

## Summary

**Total Tables Used: 2**

1. ✅ `payments` - Primary table for payment management
2. ✅ `finances` - Related table for payables/receivables (balance management)

## Notes

- **Payment Reference ID Generation:** Auto-generates payment reference ID in format PAYyymmdd### (e.g., PAY250721001)
- **Balance Management:** Automatically updates finance balance when payment is created/updated/deleted
- **Amount Validation:** Payment amount cannot exceed finance balance
- **Status Auto-Calculation:** Payment status is automatically calculated based on remaining balance and due date
- **Available Finances:** Only finances with balance > 0 are shown in dropdown
- **Default Amount:** When finance is selected, default amount is set to finance balance
- **Balance Restoration:** On payment update/delete, original payment amount is added back to finance balance
- **Comprehensive Filtering:** Filter by payment method, status, and date range
- **Search Functionality:** Searches across payment_ref, payment_method, remarks, and finance reference_id
- **Payment Methods:** Cash, Bank Transfer, Credit Card, Check, GCash, Maya, Others
- **Payment Statuses:** Fully Paid, Partially Paid, Overdue, Not Paid (from PaymentStatus enum)
- **Date Defaults:** Default payment date is current date
- **Finance Relationship:** Payments are linked to payables/receivables via finance_id
- **Balance Tracking:** Finance balance is decremented on payment create, restored on payment update/delete
- **Eager Loading:** Finance relationship is eager loaded when displaying payments
- **Module Status:** Module is under revision (as noted in the blade view)

