<x-slot:header>Activity Logs</x-slot:header>
<x-slot:subheader>System-wide user and action history</x-slot:subheader>
<div class="pt-4">
    <!-- Card -->
    <div class="mb-5 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm p-6">
        <div class="mb-5">
            <button wire:click="toggleFilters" type="button" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707l-6.414 6.414A1 1 0 0013 13.414V19a1 1 0 01-1 1H9a1 1 0 01-1-1v-5.586a1 1 0 00-.293-.707L1.293 6.707A1 1 0 011 6V4z" /></svg>
                Filters
            </button>
        </div>
        @if($showFilters)
        <div class="mb-5 bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-xl shadow p-4">
            <div class="flex flex-col sm:flex-row sm:items-end gap-4 flex-wrap">
                <!-- Date Range Dropdown -->
                <div class="flex flex-col">
                    <label class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Date Range</label>
                    <div class="relative" x-data="{ open: false }">
                        <div @click="open = !open" tabindex="0"
                            class="flex items-center w-44 h-10 px-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-gray-100 text-sm cursor-pointer focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            <span class="flex-1 text-left">Select Range</span>
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                        </div>
                        <div x-show="open" @click.away="open = false" class="absolute left-0 mt-2 w-64 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg z-20 p-4">
                            <div class="mb-3">
                                <label for="start_date" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">From</label>
                                <input type="date" wire:model.live="start_date" id="start_date" class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-3 py-2 w-full focus:ring-blue-500 focus:border-blue-500" />
                            </div>
                            <div>
                                <label for="end_date" class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">To</label>
                                <input type="date" wire:model.live="end_date" id="end_date" class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-3 py-2 w-full focus:ring-blue-500 focus:border-blue-500" />
                            </div>
                        </div>
                    </div>
                </div>
                <!-- User Filter -->
                <div class="flex flex-col">
                    <label class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">User</label>
                    <select wire:model.live="user" id="user" class="w-44 h-10 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm px-3 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Users</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Role Filter -->
                <div class="flex flex-col">
                    <label class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
                    <select wire:model.live="role" id="role" class="w-44 h-10 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm px-3 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Roles</option>
                        @foreach ($roles as $r)
                            <option value="{{ $r }}">{{ $r }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Action Filter -->
                <div class="flex flex-col">
                    <label class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Action</label>
                    <select wire:model.live="action" id="action" class="w-44 h-10 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm px-3 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Actions</option>
                        <option value="Created">Created</option>
                        <option value="Deleted">Deleted</option>
                        <option value="Edited">Edited</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                    </select>
                </div>
                <!-- Module Filter -->
                <div class="flex flex-col">
                    <label class="text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Module</label>
                    <select wire:model.live="subjectType" id="subjectType" class="w-44 h-10 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 text-sm px-3 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Modules</option>
                        @foreach ($subjectTypes as $type)
                            <option value="{{ $type }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        @endif
        <div class="overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
            <table class="min-w-full text-sm text-gray-900 dark:text-gray-100">
                <thead class="text-sm text-gray-700 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-700 sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-3 font-semibold text-left w-24 text-gray-900 dark:text-gray-100">Action</th>
                        <th class="px-6 py-3 font-semibold text-left w-40 whitespace-nowrap text-gray-900 dark:text-gray-100">User</th>
                        <th class="px-6 py-3 font-semibold text-left w-32 whitespace-nowrap text-gray-900 dark:text-gray-100">Role</th>
                        <th class="px-6 py-3 font-semibold text-left w-32 text-gray-900 dark:text-gray-100">Module</th>
                        <th class="px-6 py-3 font-semibold text-left w-32 whitespace-nowrap text-gray-900 dark:text-gray-100">Ref No.</th>
                        <th class="px-6 py-3 font-semibold text-left min-w-[200px] max-w-[400px] text-gray-900 dark:text-gray-100">Description</th>
                        <th class="px-6 py-3 font-semibold text-left w-40 whitespace-nowrap text-gray-900 dark:text-gray-100">Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        @php
                            $isDeleted = strtolower($log->event ?? $log->description) === 'deleted';
                            $props = $isDeleted ? ($log->properties['old'] ?? []) : ($log->properties['attributes'] ?? []);
                            $departmentsMap = $departments->keyBy('id');
                            $user = $log->causer;
                            $role = $user && method_exists($user, 'getRoleNames') ? $user->getRoleNames()->first() : null;
                            $module = class_basename($log->subject_type);
                            if ($module === 'SupplyProfile') {
                                $module = 'ProductProfile';
                            }
                            $action = '';
                            $event = strtolower($log->event ?? $log->description);
                            $status = $props['status'] ?? null;
                            if ($event === 'created') {
                                $action = 'Created';
                            } elseif ($event === 'deleted') {
                                $action = 'Deleted';
                            } elseif ($module === 'RequestSlip' && $status === 'approved') {
                                $action = 'Approved';
                            } elseif ($module === 'RequestSlip' && $status === 'rejected') {
                                $action = 'Rejected';
                            } elseif ($module === 'PurchaseOrder' && str_contains(strtolower($log->description), 'created')) {
                                $action = 'Created';
                            } elseif ($module === 'PurchaseOrder' && str_contains(strtolower($log->description), 'approved')) {
                                $action = 'Approved';
                            } elseif ($module === 'PurchaseOrder' && str_contains(strtolower($log->description), 'rejected')) {
                                $action = 'Rejected';
                            } elseif ($module === 'PurchaseOrder' && str_contains(strtolower($log->description), 'deleted')) {
                                $action = 'Deleted';
                            } else {
                                $action = 'Edited';
                            }
                            $refNo = '';
                            if ($module === 'PurchaseOrder') {
                                $refNo = $log->properties['po_num'] ?? $props['po_num'] ?? $log->subject_id ?? '';
                            } else {
                                $refNo = $props['id'] ?? $log->subject_id ?? '';
                            }
                            $description = '';
                            if ($module === 'RequestSlip') {
                                $purpose = $props['purpose'] ?? '';
                                $sender = (isset($props['sent_from']) && $departmentsMap->has($props['sent_from'])) ? $departmentsMap[$props['sent_from']]->name : '';
                                $receiver = (isset($props['sent_to']) && $departmentsMap->has($props['sent_to'])) ? $departmentsMap[$props['sent_to']]->name : '';
                                $approver = (isset($props['approver']) && isset($users[$props['approver']])) ? $users[$props['approver']]->name : '';
                                if (($action === 'Approved' || $action === 'Rejected') && $approver) {
                                    $description = "Action by: $approver";
                                } else {
                                    $description = collect([
                                        $purpose ? "Purpose: $purpose" : null,
                                        $sender ? "Sender: $sender" : null,
                                        $receiver ? "Receiver: $receiver" : null,
                                    ])->filter()->implode('<br>');
                                }
                                if ($description === '') $description = '—';
                            } elseif ($module === 'Supplier') {
                                $description = $log->description;
                            } elseif ($module === 'SupplyProfile' || $module === 'ProductProfile') {
                                $description = $log->description;
                            } elseif ($module === 'PurchaseOrder') {
                                $description = $log->description;
                             } elseif ($module === 'Shipment') {
                                $description = $log->description;
                            } elseif ($module === 'SalesOrder') {
                                $description = $log->description;
                            } elseif ($module === 'SalesOrderItem') {
                                $description = $log->description;
                            } else {
                                $description = $props['description'] ?? null;
                                if (empty($description) || strtolower($description) === strtolower($action) || strtolower($description) === strtolower($event)) {
                                    $description = '—';
                                }
                            }
                        @endphp
                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors">
                            <td class="px-6 py-3 align-top w-24 text-gray-900 dark:text-gray-100">
                                <span class="inline-block w-24 text-center px-3 py-1 rounded-full text-xs font-semibold
                                    @if($action === 'Created') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                    @elseif($action === 'Approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($action === 'Deleted') bg-gray-200 text-gray-800 dark:bg-gray-600 dark:text-gray-100
                                    @elseif($action === 'Rejected') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                    @else bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200 @endif">
                                    {{ $action }}
                                </span>
                            </td>
                            <td class="px-6 py-3 align-top w-40 whitespace-nowrap truncate text-gray-900 dark:text-gray-100">{{ $user?->name ?? 'System' }}</td>
                            <td class="px-6 py-3 align-top w-32 whitespace-nowrap truncate text-gray-900 dark:text-gray-100">{{ $role ?? '-' }}</td>
                            <td class="px-6 py-3 align-top w-32 text-gray-900 dark:text-gray-100">{{ $module }}</td>
                            <td class="px-6 py-3 align-top w-32 whitespace-nowrap truncate text-gray-900 dark:text-gray-100">@if($refNo){{ $refNo }}@else—@endif</td>
                            <td class="px-6 py-3 align-top min-w-[200px] max-w-[400px] text-gray-900 dark:text-gray-100">
                                @php
                                    $plainDesc = strip_tags($description);
                                    $hasMore = (strlen($plainDesc) > 60) || (strpos($description, '<br>') !== false);
                                    $descPreview = Str::of($plainDesc)->before("\n")->before('<br>')->limit(60);
                                @endphp
                                <div x-data="{ expanded: false }">
                                    <div x-show="!expanded" class="flex items-center justify-between w-full">
                                        <span class="truncate">{{ $descPreview }}</span>
                                        @if($hasMore)
                                            <button @click="expanded = true" class="text-blue-600 dark:text-blue-400 hover:underline ml-2 whitespace-nowrap">... See more</button>
                                        @endif
                                    </div>
                                    <div x-show="expanded" class="flex items-start justify-between w-full">
                                        <span class="break-words">{!! $description !!}</span>
                                        <button @click="expanded = false" class="text-blue-600 dark:text-blue-400 hover:underline ml-2 whitespace-nowrap">Show less</button>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-3 align-top w-40 whitespace-nowrap truncate text-gray-900 dark:text-gray-100">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-12 text-center text-gray-400 dark:text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="w-12 h-12 mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 20h.01M12 4a8 8 0 100 16 8 8 0 000-16z" /></svg>
                                    <span class="text-lg mb-2">No activity logs yet</span>
                                    <span class="text-sm">This is a placeholder. Activity logs will appear here once data is available.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="py-4 px-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <label class="text-sm font-medium text-gray-900 dark:text-white">Per Page:</label>
                    <select wire:model.live="perPage"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                        <option value="5">5</option>
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div>
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
