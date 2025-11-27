# Agent Management Profile Module - Database Tables Analysis

**Route:** `/agentmanagement/profile`  
**Component:** `App\Livewire\Pages\Agent\Index`  
**View:** `resources/views/livewire/pages/agent/index.blade.php`

## Directly Used Tables

### 1. `agents` (Primary Table)
- **Model:** `App\Models\Agent`
- **Usage:**
  - Main CRUD operations (create, read, update, delete)
  - Search and filtering (by name, agent_code, address, contact_num, tin_num)
  - Status filtering (all, deployed, active)
  - Agent deployment management
- **Relationships:**
  - `hasMany` → `branchAssignments` (AgentBranchAssignment model)
  - `belongsToMany` → `currentBranches` (Branch model, via agent_branch_assignments, where released_at is null)
- **Fields Used:**
  - `id` - Agent ID (primary key, relationships, editing)
  - `agent_code` - Agent code (unique, search, display, validation)
  - `name` - Agent name (search, display, validation)
  - `address` - Agent address (search, display, validation)
  - `contact_num` - Contact number (search, display, validation)
  - `tin_num` - TIN number (search, display, validation)
  - `status` - Agent status (if used, filtering)
  - `created_at` - Creation timestamp (sorting)
  - `updated_at` - Update timestamp
- **Methods:**
  - `Agent::create([...])` - Create new agent
  - `Agent::findOrFail($id)` - Find agent by ID
  - `Agent::with('branchAssignments')` - Eager load branch assignments
  - `Agent::where('name', 'like', ...)` - Search by name
  - `Agent::whereHas('branchAssignments', ...)` - Filter deployed agents
  - `Agent::whereDoesntHave('branchAssignments', ...)` - Filter active (non-deployed) agents
  - `Agent::latest()->paginate($perPage)` - Paginate results
  - `$agent->branchAssignments()->create([...])` - Create branch assignment
  - `$agent->update([...])` - Update agent
  - `$agent->delete()` - Delete agent
- **Features:**
  - Agent code must be unique
  - Search across multiple fields (name, code, address, contact, TIN)
  - Status filtering (all, deployed, active)
  - Deployment workflow (deploy/release agents to branches)
  - Branch assignment on creation

### 2. `agent_branch_assignments`
- **Model:** `App\Models\AgentBranchAssignment`
- **Usage:**
  - Store agent-to-branch assignments
  - Track assignment history (assigned_at, released_at)
  - Deploy/release workflow
  - Subclass assignment tracking
  - Determine if agent is deployed
- **Relationships:**
  - `belongsTo` → `agent` (Agent model)
  - `belongsTo` → `branch` (Branch model)
- **Fields Used:**
  - `id` - Assignment ID (primary key)
  - `agent_id` - Agent reference (foreign key, filtering, relationships)
  - `branch_id` - Branch reference (foreign key, filtering, relationships)
  - `subclass` - Subclass assignment (string, from branch's subclass1-4)
  - `assigned_at` - Assignment timestamp (tracking, filtering)
  - `released_at` - Release timestamp (null = active, not null = released)
- **Methods:**
  - `AgentBranchAssignment::where('agent_id', $agentId)->whereNull('released_at')` - Find active assignments
  - `AgentBranchAssignment::where('agent_id', $agentId)->whereNull('released_at')->update(['released_at' => now()])` - Release agent
  - `AgentBranchAssignment::where('agent_id', $agentId)->whereNull('released_at')->exists()` - Check if deployed
  - `AgentBranchAssignment::select('agent_id')->whereNull('released_at')->distinct()->pluck('agent_id')` - Get deployed agent IDs
  - `$agent->branchAssignments()->create([...])` - Create assignment
- **Features:**
  - Historical tracking (keeps all assignments, marks released ones)
  - Only one active assignment per agent (released_at is null)
  - Subclass assignment support (from branch's subclass fields)
  - Automatic release of previous assignment when new one is created
  - Deployment status tracking

### 3. `branches`
- **Model:** `App\Models\Branch`
- **Usage:**
  - Branch selection for agent assignment
  - Display branch information
  - Subclass options (subclass1-4)
  - Filter branches for assignment
- **Relationships:**
  - `belongsToMany` → `agents` (via agent_branch_assignments, indirect)
  - `hasMany` → `agentBranchAssignments` (AgentBranchAssignment model, indirect)
- **Fields Used:**
  - `id` - Branch ID (foreign key, selection, validation)
  - `name` - Branch name (display)
  - `subclass1` - Subclass option 1 (assignment options)
  - `subclass2` - Subclass option 2 (assignment options)
  - `subclass3` - Subclass option 3 (assignment options)
  - `subclass4` - Subclass option 4 (assignment options)
  - `code` - Branch code (if displayed)
  - `address` - Branch address (if displayed)
- **Methods:**
  - `Branch::all()` - Get all branches
  - `Branch::find($branchId)` - Find branch by ID
  - `$branch->subclass1, subclass2, subclass3, subclass4` - Get subclass options
- **Features:**
  - Subclass options dynamically loaded from branch's subclass1-4 fields
  - Subclass options filtered to unique, non-empty values
  - Subclass validation (must be one of the branch's subclass options)
  - Branch selection for agent assignment

## Indirectly Used Tables (via Relationships)

### 4. `users`
- **Model:** `App\Models\User`
- **Usage:**
  - Permission checks (`agent view`, `agent create`, `agent edit`, `agent delete`)
- **Methods:**
  - `auth()->user()->hasAnyPermission(['agent view'])` - Permission check
- **Note:** Used for access control only

## Summary

**Total Tables Used: 4**

1. ✅ `agents` - Primary table for agent management
2. ✅ `agent_branch_assignments` - Agent-to-branch assignment tracking
3. ✅ `branches` - Branch information and subclass options
4. ✅ `users` - Permissions

## Notes

- **Agent Code Uniqueness:** Agent code must be unique across all agents
- **Deployment Workflow:** Agents can be deployed to branches with subclass assignments
- **Historical Tracking:** All assignments are kept in history (released_at marks when released)
- **Active Assignment:** Only one active assignment per agent (released_at is null)
- **Subclass Assignment:** Agents can be assigned to specific subclasses within a branch (from branch's subclass1-4 fields)
- **Automatic Release:** When assigning an agent to a new branch, previous active assignment is automatically released
- **Status Filtering:** Can filter agents by deployment status (all, deployed, active/non-deployed)
- **Search Functionality:** Searches across name, agent_code, address, contact_num, and tin_num
- **Branch Assignment on Creation:** Can assign branches when creating a new agent
- **Validation:** Subclass must be one of the branch's available subclass options (subclass1-4)
- **Eager Loading:** Uses eager loading to prevent N+1 queries when loading branch assignments

