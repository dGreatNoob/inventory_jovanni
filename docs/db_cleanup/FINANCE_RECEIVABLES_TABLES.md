# Finance Receivables Module - Database Tables Analysis

**Route:** `/finance/receivables`  
**Component:** `App\Livewire\Pages\Finance\Receivables`  
**View:** `resources/views/livewire/pages/finance/receivables.blade.php`

## Directly Used Tables

### 1. `finances` (Primary Table)
- **Model:** `App\Models\Finance`
- **Usage:**
  - Main CRUD operations (create, read, update, delete)
  - Store receivable records (type = 'receivable')
  - Search and filtering (by reference_id, remarks, branch name, agent name)
  - Status filtering (pending, paid, overdue, cancelled)
  - Date range filtering (due_date)
  - Branch and agent filtering
  - Auto-update overdue statuses
  - Inline status updates
- **Relationships:**
  - `belongsTo` → `branch` (Branch model)
  - `belongsTo` → `agent` (Agent model)
- **Fields Used:**
  - `id` - Finance ID (primary key, editing, deletion)
  - `type` - Finance type (filtered to 'receivable')
  - `reference_id` - Reference number (unique per day, search, display, auto-generation)
  - `branch_id` - Branch reference (foreign key, filtering, relationships)
  - `agent_id` - Agent reference (foreign key, filtering, relationships, auto-assigned)
  - `date` - Transaction date (default: current date)
  - `amount` - Amount due (required, validation, display, calculation)
  - `due_date` - Due date (nullable, filtering, display, overdue calculation)
  - `payment_method` - Payment method (default: 'N/A', not actively used)
  - `status` - Payment status (pending, paid, overdue, cancelled) (filtering, updates, validation)
  - `remarks` - Remarks (nullable, search, display)
  - `created_at` - Creation timestamp (sorting)
  - `updated_at` - Update timestamp
- **Methods:**
  - `Finance::where('type', 'receivable')` - Filter receivables only
  - `Finance::where('reference_id', 'like', $prefix . '%')` - Find latest reference ID
  - `Finance::where('due_date', '<', now())->whereNotIn('status', ['paid', 'cancelled', 'overdue'])->update(['status' => 'overdue'])` - Auto-update overdue
  - `Finance::with(['branch', 'agent'])` - Eager load relationships
  - `Finance::whereHas('branch', ...)` - Search by branch name
  - `Finance::whereHas('agent', ...)` - Search by agent name
  - `Finance::create([...])` - Create receivable
  - `Finance::findOrFail($id)->update([...])` - Update receivable
  - `Finance::findOrFail($id)->delete()` - Delete receivable
- **Features:**
  - Reference ID auto-generation (format: RECyymmdd###, e.g., REC250721001)
  - Auto-update overdue statuses on render
  - Inline status updates
  - Comprehensive filtering (status, date range, branch, agent)
  - Search across multiple fields
  - Agent auto-assignment based on branch's current agents

### 2. `branches`
- **Model:** `App\Models\Branch`
- **Usage:**
  - Branch selection for receivables
  - Display branch information
  - Filter receivables by branch
  - Get current agents for branch (for auto-assignment)
  - Auto-populate amount due from sales receipts
- **Relationships:**
  - `hasMany` → `finances` (Finance model, indirect)
  - `belongsToMany` → `currentAgents` (Agent model, via agent_branch_assignments, where released_at is null)
  - `hasMany` → `salesReceipts` (SalesReceipt model, indirect)
- **Fields Used:**
  - `id` - Branch ID (foreign key, selection, filtering)
  - `name` - Branch name (display, search, dropdown)
- **Methods:**
  - `Branch::all()` - Get all branches for dropdown
  - `Branch::find($branchId)` - Find branch by ID
  - `$branch->currentAgents` - Get current agents for branch
  - `$branch->currentAgents->first()?->id` - Get first active agent ID
- **Features:**
  - Branch selection with agent display in dropdown
  - Auto-assigns first active agent when branch is selected
  - Used to auto-populate amount due from sales receipts

### 3. `agents`
- **Model:** `App\Models\Agent`
- **Usage:**
  - Display agent information in receivables
  - Filter receivables by agent
  - Auto-assignment based on branch's current agents
  - Agent dropdown for filtering
- **Relationships:**
  - `hasMany` → `finances` (Finance model, indirect)
  - `belongsToMany` → `currentBranches` (Branch model, via agent_branch_assignments, indirect)
- **Fields Used:**
  - `id` - Agent ID (foreign key, filtering, auto-assignment)
  - `name` - Agent name (display, search, dropdown)
- **Methods:**
  - `Agent::all()` - Get all agents for dropdown
  - `$branch->currentAgents->first()?->id` - Get first active agent for branch
- **Features:**
  - Auto-assigned from branch's current agents
  - Display in receivables table
  - Filter receivables by agent

### 4. `agent_branch_assignments`
- **Model:** `App\Models\AgentBranchAssignment`
- **Usage:**
  - Get current agents for branch (via Branch->currentAgents relationship)
  - Auto-assign agent to receivable based on branch
- **Relationships:**
  - `belongsTo` → `agent` (Agent model)
  - `belongsTo` → `branch` (Branch model)
- **Fields Used:**
  - `agent_id` - Agent reference (via relationship)
  - `branch_id` - Branch reference (via relationship)
  - `released_at` - Release timestamp (null = active)
- **Methods:**
  - `$branch->currentAgents` - Get active agents (where released_at is null)
- **Features:**
  - Used to determine which agent to auto-assign to receivable
  - Only active agents (released_at is null) are considered

### 5. `sales_receipts`
- **Model:** `App\Models\SalesReceipt`
- **Usage:**
  - Auto-populate amount due from latest sales receipt
  - Calculate total amount from sales receipt items
- **Relationships:**
  - `belongsTo` → `branch` (Branch model)
  - `belongsTo` → `batchAllocation` (BatchAllocation model)
  - `hasMany` → `items` (SalesReceiptItem model)
- **Fields Used:**
  - `id` - Sales receipt ID
  - `branch_id` - Branch reference (foreign key, filtering)
  - `status` - Receipt status (filtered to 'received')
  - `updated_at` - Update timestamp (sorting)
- **Methods:**
  - `SalesReceipt::where('branch_id', $branchId)->where('status', 'received')->with('items.product')->orderBy('updated_at', 'desc')->first()` - Get latest receipt
- **Features:**
  - Used to auto-populate amount due when branch is selected
  - Only considers receipts with 'received' status
  - Gets latest receipt by updated_at

### 6. `sales_receipt_items`
- **Model:** `App\Models\SalesReceiptItem`
- **Usage:**
  - Calculate total amount from sales receipt items
  - Get received quantities and product prices
- **Relationships:**
  - `belongsTo` → `salesReceipt` (SalesReceipt model)
  - `belongsTo` → `product` (Product model)
- **Fields Used:**
  - `id` - Item ID
  - `sales_receipt_id` - Sales receipt reference (foreign key)
  - `product_id` - Product reference (foreign key)
  - `received_qty` - Received quantity (calculation)
- **Methods:**
  - `$salesReceipt->items` - Get receipt items
  - `$item->received_qty ?? 0` - Get received quantity
  - `$item->product->price ?? 0` - Get product price
- **Features:**
  - Used to calculate total amount (quantity * price) for auto-population
  - Eager loaded with product relationship

### 7. `products`
- **Model:** `App\Models\Product`
- **Usage:**
  - Get product price for amount calculation
  - Calculate total amount from sales receipt items
- **Relationships:**
  - `belongsTo` → `salesReceiptItems` (SalesReceiptItem model, indirect)
- **Fields Used:**
  - `id` - Product ID (foreign key)
  - `price` / `selling_price` - Product price (calculation)
- **Methods:**
  - `$item->product->price ?? $item->product->selling_price ?? 0` - Get product price
- **Features:**
  - Used in amount calculation for auto-population
  - Eager loaded with sales receipt items

## Indirectly Used Tables (via Relationships)

### 8. `users`
- **Model:** `App\Models\User`
- **Usage:**
  - Permission checks (if implemented)
- **Note:** May be used for access control

## Summary

**Total Tables Used: 8**

1. ✅ `finances` - Primary table for receivable management
2. ✅ `branches` - Branch selection and filtering
3. ✅ `agents` - Agent assignment and filtering
4. ✅ `agent_branch_assignments` - Agent assignment tracking
5. ✅ `sales_receipts` - Auto-populate amount due
6. ✅ `sales_receipt_items` - Calculate amount from receipt items
7. ✅ `products` - Product price for amount calculation
8. ✅ `users` - Permissions (if implemented)

## Notes

- **Reference ID Generation:** Auto-generates reference ID in format RECyymmdd### (e.g., REC250721001)
- **Auto-Assignment:** Automatically assigns first active agent from branch's current agents
- **Auto-Population:** Can auto-populate amount due from latest sales receipt for selected branch
- **Overdue Management:** Automatically updates status to 'overdue' for receivables past due date
- **Status Management:** Supports pending, paid, overdue, and cancelled statuses
- **Comprehensive Filtering:** Filter by status, due date range, branch, and agent
- **Search Functionality:** Searches across reference_id, remarks, branch name, and agent name
- **Inline Updates:** Supports inline status updates
- **Eager Loading:** Uses eager loading to prevent N+1 queries
- **Agent Display:** Shows assigned agents in branch dropdown
- **Amount Calculation:** Calculates total from sales receipt items (quantity * price)
- **Module Status:** Module is under revision (as noted in the view)

