# Branch Management Profile Module - Database Tables Analysis

**Route:** `/Branchmanagement/profile`  
**Component:** `App\Livewire\Pages\Branch\Index`  
**View:** `resources/views/livewire/pages/branch/index.blade.php`

## Directly Used Tables

### 1. `branches` (Primary Table)
- **Model:** `App\Models\Branch`
- **Usage:**
  - Main CRUD operations (create, read, update, delete)
  - Search and filtering (by name, address, contact_num, manager_name)
  - Dashboard statistics
  - Agent per branch statistics
  - Subclass management (subclass1-4)
- **Relationships:**
  - `hasMany` → `agentAssignments` (AgentBranchAssignment model)
  - `hasMany` → `branchAssignments` (BranchAllocation model, indirect)
  - `belongsToMany` → `currentAgents` (Agent model, via agent_branch_assignments, where released_at is null)
  - `hasMany` → `activeAgents` (AgentBranchAssignment model, where released_at is null, with agent)
  - `belongsToMany` → `products` (Product model, via branch_product pivot, indirect)
- **Fields Used:**
  - `id` - Branch ID (primary key, relationships, editing)
  - `name` - Branch name (required, search, display, validation)
  - `code` - Branch code (required, search, display, validation)
  - `category` - Branch category (required, validation)
  - `address` - Branch address (required, search, display, validation)
  - `contact_num` - Contact number (nullable, search, display)
  - `manager_name` - Manager name (nullable, search, display)
  - `subclass1` - Subclass option 1 (nullable, agent assignment)
  - `subclass2` - Subclass option 2 (nullable, agent assignment)
  - `subclass3` - Subclass option 3 (nullable, agent assignment)
  - `subclass4` - Subclass option 4 (nullable, agent assignment)
  - `remarks` - Remarks (nullable, display)
  - `batch` - Batch number (nullable, filtering, allocation workflow)
  - `branch_code` - Branch code (nullable, alternative code)
  - `company_name` - Company name (nullable, display)
  - `company_tin` - Company TIN (nullable, display)
  - `dept_code` - Department code (nullable, display)
  - `pull_out_addresse` - Pull out address (nullable, display)
  - `vendor_code` - Vendor code (nullable, display)
  - `created_at` - Creation timestamp (sorting, statistics)
  - `updated_at` - Update timestamp
- **Methods:**
  - `Branch::create([...])` - Create new branch
  - `Branch::findOrFail($id)` - Find branch by ID
  - `Branch::with(['activeAgents' => function($q) { $q->with('agent'); }])` - Eager load active agents
  - `Branch::where('name', 'like', ...)` - Search by name
  - `Branch::whereMonth('created_at', ...)->whereYear('created_at', ...)` - Filter by date
  - `Branch::count()` - Count total branches
  - `Branch::latest()->paginate($perPage)` - Paginate results
  - `$branch->update([...])` - Update branch
  - `$branch->delete()` - Delete branch
  - `$branch->activeAgents->count()` - Count active agents
- **Features:**
  - Comprehensive branch information management
  - Subclass management (up to 4 subclasses per branch)
  - Search across multiple fields
  - Dashboard statistics (total, new this month, coverage)
  - Agent per branch statistics
  - Batch number support (for allocation workflow)

### 2. `agent_branch_assignments`
- **Model:** `App\Models\AgentBranchAssignment`
- **Usage:**
  - Count active agents per branch
  - Calculate branch coverage statistics
  - Filter branches by agent assignment status
  - Dashboard statistics
- **Relationships:**
  - `belongsTo` → `agent` (Agent model)
  - `belongsTo` → `branch` (Branch model)
- **Fields Used:**
  - `id` - Assignment ID (primary key)
  - `agent_id` - Agent reference (foreign key, counting)
  - `branch_id` - Branch reference (foreign key, filtering, counting)
  - `released_at` - Release timestamp (null = active, not null = released)
- **Methods:**
  - `AgentBranchAssignment::whereNull('released_at')->distinct('branch_id')->count('branch_id')` - Count branches with agents
  - `AgentBranchAssignment::whereNull('released_at')->distinct('agent_id')->count('agent_id')` - Count active agents
  - `$branch->activeAgents` - Get active agents for branch (via relationship)
- **Features:**
  - Used to calculate branch coverage (branches with active agents)
  - Used to count active agents per branch
  - Used in dashboard statistics
  - Used in agent per branch dashboard

### 3. `agents`
- **Model:** `App\Models\Agent`
- **Usage:**
  - Count total agents in system
  - Display agent information in branch dashboard
  - Eager load agent details for branch assignments
- **Relationships:**
  - `hasMany` → `branchAssignments` (AgentBranchAssignment model, indirect)
  - `belongsToMany` → `currentBranches` (Branch model, via agent_branch_assignments, indirect)
- **Fields Used:**
  - `id` - Agent ID (foreign key, counting)
  - `name` - Agent name (display, if shown)
- **Methods:**
  - `Agent::count()` - Count total agents
  - `$branch->activeAgents->with('agent')` - Eager load agent details
- **Features:**
  - Used in dashboard statistics (total agents)
  - Eager loaded with branch assignments for display

## Indirectly Used Tables (via Relationships)

### 4. `users`
- **Model:** `App\Models\User`
- **Usage:**
  - Permission checks (`branch view`, `branch create`, `branch edit`, `branch delete`)
- **Methods:**
  - `auth()->user()->hasAnyPermission(['branch view'])` - Permission check
- **Note:** Used for access control only

## Summary

**Total Tables Used: 4**

1. ✅ `branches` - Primary table for branch management
2. ✅ `agent_branch_assignments` - Agent assignment tracking and statistics
3. ✅ `agents` - Agent information for statistics
4. ✅ `users` - Permissions

## Notes

- **Comprehensive Fields:** Branches have many fields including subclass management, company info, vendor codes, etc.
- **Subclass Management:** Supports up to 4 subclasses per branch (subclass1-4)
- **Batch Number:** Used for allocation workflow (links to batch_allocations)
- **Dashboard Statistics:** Tracks total branches, new branches this month, branch coverage (branches with agents)
- **Agent Per Branch Dashboard:** Shows agent count per branch with filtering and sorting
- **Search Functionality:** Searches across name, address, contact_num, and manager_name
- **Status Filtering:** Can filter branches by agent coverage (all, covered, no_agent)
- **Sorting:** Can sort by agent_count, name, or code
- **Coverage Calculation:** Calculates percentage of branches with active agents
- **Monthly Statistics:** Tracks new branches created this month vs last month
- **Eager Loading:** Uses eager loading to prevent N+1 queries when loading active agents
- **Validation:** Name, code, category, and address are required fields
- **Historical Tracking:** Agent assignments are tracked via agent_branch_assignments table

