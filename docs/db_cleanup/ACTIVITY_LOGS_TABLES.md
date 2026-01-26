# Activity Logs Module - Database Tables Analysis

**Route:** `/activity-logs`  
**Component:** `App\Livewire\ActivityLogs`  
**View:** `resources/views/livewire/activity-logs.blade.php`

## Directly Used Tables

### 1. `activity_log` (Primary Table)
- **Model:** `Spatie\Activitylog\Models\Activity`
- **Usage:**
  - Main table for displaying activity logs
  - Comprehensive filtering and searching
  - Display system-wide user and action history
  - Track all changes across the system
- **Relationships:**
  - `morphTo('subject')` - Links activity to any model (polymorphic)
  - `morphTo('causer')` - Links activity to user who performed the action (polymorphic)
- **Fields Used:**
  - `id` - Activity log ID (primary key)
  - `log_name` - Log name (indexed, filtering)
  - `description` - Activity description (search, display)
  - `subject_type` - Subject model type (polymorphic, filtering, display)
  - `subject_id` - Subject model ID (polymorphic, search, display)
  - `causer_type` - Causer model type (polymorphic, relationship)
  - `causer_id` - Causer user ID (polymorphic, filtering, relationship)
  - `properties` - JSON properties (search, filtering, display)
  - `event` - Event type (created, updated, deleted, filtering)
  - `batch_uuid` - Batch UUID (grouping related activities)
  - `created_at` - Creation timestamp (filtering, sorting, display)
  - `updated_at` - Update timestamp
- **Methods:**
  - `Activity::with('causer')->orderByDesc('created_at')` - Load activities with causer relationship
  - `Activity::select('subject_type')->distinct()->pluck('subject_type')` - Get unique subject types
  - `Activity::whereDate('created_at', '>=', $start_date)` - Filter by start date
  - `Activity::whereDate('created_at', '<=', $end_date)` - Filter by end date
  - `Activity::where('causer_id', $user)` - Filter by user
  - `Activity::whereHas('causer.roles', function($q) { $q->where('name', $role); })` - Filter by role
  - `Activity::where('event', 'created')` - Filter by event type
  - `Activity::where('subject_type', $subjectType)` - Filter by subject type
  - `Activity::where('subject_id', 'like', "%{$ref_no}%")` - Search by reference number
  - `Activity::whereJsonContains('properties->attributes->sent_from', $department)` - Filter by department
  - `Activity::whereJsonContains('properties->attributes->sent_to', $department)` - Filter by department
  - `Activity::whereJsonContains('properties->attributes->id', $ref_no)` - Search in JSON properties
  - `Activity::whereJsonContains('properties->old->id', $ref_no)` - Search in JSON old properties
  - `Activity::where('description', 'like', "%{$description}%")` - Search by description
  - `Activity::whereJsonContains('properties->ip', $ip)` - Search by IP address
  - `Activity::paginate($perPage)` - Paginate results
- **Features:**
  - Comprehensive filtering (date range, user, role, action, module, reference number, description, IP)
  - JSON property searching (searches in attributes, old properties, and root properties)
  - Subject type filtering (filters by model type, e.g., PurchaseOrder, RequestSlip, SupplyProfile)
  - Event type filtering (created, deleted, edited)
  - Department filtering (searches in sent_from and sent_to JSON properties)
  - Reference number searching (searches in subject_id and JSON properties)
  - Description searching (searches in description and JSON properties)
  - IP address searching (searches in JSON properties)
  - Causer relationship eager loaded for performance
  - Subject type mapping (SupplyProfile → ProductProfile for display)

### 2. `departments` (Related Table)
- **Model:** `App\Models\Department`
- **Usage:**
  - Load departments for filtering dropdown
  - Display department names in activity logs
  - Filter activities by department (sent_from, sent_to)
- **Relationships:**
  - None directly used in this module
- **Fields Used:**
  - `id` - Department ID (filtering, mapping)
  - `name` - Department name (display, sorting)
- **Methods:**
  - `Department::orderBy('name')->get()` - Load all departments ordered by name
- **Features:**
  - Departments loaded for filtering dropdown
  - Department names displayed in activity logs
  - Department IDs used for JSON property filtering

### 3. `users` (Related Table)
- **Model:** `App\Models\User`
- **Usage:**
  - Load users for filtering dropdown
  - Display user information in activity logs
  - Filter activities by user (causer)
  - Get user roles for filtering
- **Relationships:**
  - `hasMany(Role::class)` - Links user to roles (via Spatie Permissions)
- **Fields Used:**
  - `id` - User ID (filtering, mapping, relationship)
  - `name` - User name (display)
  - User fields accessed via `causer` relationship
- **Methods:**
  - `User::all()->keyBy('id')` - Load all users keyed by ID
  - `User::with('roles')->get()` - Load users with roles
  - `$user->getRoleNames()` - Get user role names (Spatie Permissions)
  - `Activity::where('causer_id', $user)` - Filter by user
  - `Activity::whereHas('causer.roles', function($q) { $q->where('name', $role); })` - Filter by user role
- **Features:**
  - Users loaded for filtering dropdown
  - User information displayed in activity logs
  - User roles extracted for role filtering dropdown
  - Causer relationship eager loaded for performance

### 4. `roles` (Related Table - Spatie Permissions)
- **Model:** `Spatie\Permission\Models\Role`
- **Usage:**
  - Extract unique role names for filtering dropdown
  - Filter activities by user role
- **Relationships:**
  - `belongsToMany(User::class)` - Links role to users (via model_has_roles)
- **Fields Used:**
  - `name` - Role name (filtering, display)
- **Methods:**
  - `User::with('roles')->get()->flatMap(function($user) { return $user->getRoleNames(); })->unique()->values()` - Extract unique role names
  - `Activity::whereHas('causer.roles', function($q) { $q->where('name', $role); })` - Filter by role
- **Features:**
  - Unique role names extracted from users for filtering dropdown
  - Activities filtered by user role
  - Role filtering uses Spatie Permissions relationship

### 5. `model_has_roles` (Related Table - Spatie Permissions)
- **Model:** Spatie Permissions pivot table
- **Usage:**
  - Links users to roles
  - Used for role filtering via `whereHas('causer.roles')`
- **Relationships:**
  - Pivot table for many-to-many relationship between users and roles
- **Fields Used:**
  - `role_id` - Foreign key to roles table
  - `model_type` - Model type (User)
  - `model_id` - Foreign key to users table
- **Methods:**
  - Accessed via `User::with('roles')` relationship
  - Used in `Activity::whereHas('causer.roles', function($q) { $q->where('name', $role); })`
- **Features:**
  - Enables role-based filtering of activities
  - Used indirectly through Spatie Permissions relationship

## Filtering Capabilities

### Date Range Filtering
- Filter activities by start date and end date
- Uses `whereDate('created_at', '>=', $start_date)` and `whereDate('created_at', '<=', $end_date)`

### User Filtering
- Filter activities by specific user (causer)
- Uses `where('causer_id', $user)`

### Role Filtering
- Filter activities by user role
- Uses `whereHas('causer.roles', function($q) { $q->where('name', $role); })`

### Action Filtering
- Filter activities by action type (Created, Deleted, Edited)
- Uses `where('event', 'created')`, `where('event', 'deleted')`, or `whereNotIn('event', ['created', 'deleted'])`

### Module Filtering
- Filter activities by subject type (model type)
- Maps display names (e.g., SupplyProfile → ProductProfile)
- Uses `where('subject_type', $subjectType)`

### Department Filtering
- Filter activities by department (sent_from, sent_to)
- Uses JSON property filtering: `whereJsonContains('properties->attributes->sent_from', $department)`

### Reference Number Filtering
- Search activities by reference number
- Searches in `subject_id`, `properties->attributes->id`, and `properties->old->id`
- Uses `where('subject_id', 'like', "%{$ref_no}%")` and JSON property searches

### Description Filtering
- Search activities by description
- Searches in `description`, `properties->attributes->description`, and `properties->old->description`
- Uses `where('description', 'like', "%{$description}%")` and JSON property searches

### IP Address Filtering
- Search activities by IP address
- Searches in `properties->ip`, `properties->attributes->ip`, and `properties->old->ip`
- Uses JSON property filtering

## Display Features

### Action Display
- Determines action type from event and description
- Maps events: created → Created, deleted → Deleted, others → Edited
- Special handling for RequestSlip (Approved/Rejected) and PurchaseOrder (Created/Approved/Rejected/Deleted)

### Module Display
- Displays subject type (model class name)
- Maps SupplyProfile → ProductProfile for display

### Reference Number Display
- Extracts reference number from properties or subject_id
- Special handling for PurchaseOrder (uses po_num from properties)

### Description Display
- Formats description based on module type
- Special formatting for RequestSlip (purpose, sender, receiver, approver)
- Displays formatted field changes for other modules

### User and Role Display
- Displays causer (user) name
- Displays user's first role name
- Shows "System" if no causer

## Summary

**Total Tables Used: 5**

1. ✅ `activity_log` - Primary table for activity logging (Spatie Activity Log)
2. ✅ `departments` - Related table for departments (filtering, display)
3. ✅ `users` - Related table for users (filtering, display, causer relationship)
4. ✅ `roles` - Related table for roles (Spatie Permissions, filtering)
5. ✅ `model_has_roles` - Pivot table for user-role relationships (Spatie Permissions, filtering)

## Notes

- **Activity Logging:** Uses Spatie Activity Log package for comprehensive activity tracking
- **Polymorphic Relationships:** Activity log uses polymorphic relationships for subject and causer
- **JSON Property Searching:** Extensive use of JSON property searching for flexible filtering
- **Subject Type Mapping:** Maps SupplyProfile to ProductProfile for display consistency
- **Role Extraction:** Extracts unique role names from users for filtering dropdown
- **Comprehensive Filtering:** Supports filtering by date range, user, role, action, module, department, reference number, description, and IP address
- **Action Detection:** Intelligently determines action type from event and description
- **Special Module Handling:** Special formatting for RequestSlip and PurchaseOrder modules
- **Eager Loading:** Causer relationship is eager loaded for performance
- **Pagination:** Results are paginated for performance
- **Filter Toggle:** Filters can be shown/hidden via toggle button
- **Department Filtering:** Filters by department using JSON properties (sent_from, sent_to)
- **Reference Number Search:** Searches in multiple locations (subject_id, JSON properties)
- **Description Search:** Searches in description field and JSON properties
- **IP Address Search:** Searches in JSON properties for IP addresses
- **Event Type Filtering:** Filters by event type (created, deleted, edited)
- **Subject Type Filtering:** Filters by model type (module filtering)
- **Unused Model:** Log model is imported but not used (legacy code)

