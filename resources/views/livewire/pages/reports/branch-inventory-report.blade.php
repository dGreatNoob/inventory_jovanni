<x-slot:header>Branch Inventory Audit Report</x-slot:header>
<x-slot:subheader>Inventory audit reports per branch (missing, extra, and quantity variances)</x-slot:subheader>

<div class="space-y-6">
    <!-- Filters -->
    <div class="p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 grid gap-4 md:grid-cols-3">
        <div>
            <label for="dateFrom" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">From Date</label>
            <input type="date" id="dateFrom" wire:model.live="dateFrom" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-900 dark:border-gray-600 dark:text-white">
        </div>
        <div>
            <label for="dateTo" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">To Date</label>
            <input type="date" id="dateTo" wire:model.live="dateTo" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-900 dark:border-gray-600 dark:text-white">
        </div>
        <div>
            <label for="selectedBranch" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Branch</label>
            <select id="selectedBranch" wire:model.live="selectedBranch" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-900 dark:border-gray-600 dark:text-white">
                <option value="">All Branches</option>
                @foreach($branches as $branch)
                    <option value="{{ $branch->id }}">{{ $branch->name }} @if($branch->batch) (Batch: {{ $branch->batch }}) @endif</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="text-sm text-gray-500 dark:text-gray-400">Total Audits</div>
            <div class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">{{ number_format($totalAudits) }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="text-sm text-gray-500 dark:text-gray-400">Branches Audited</div>
            <div class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">{{ number_format($branchesAudited) }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="text-sm text-gray-500 dark:text-gray-400">Pass Audits</div>
            <div class="mt-1 text-3xl font-semibold text-emerald-600 dark:text-emerald-400">{{ number_format($passAudits) }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="text-sm text-gray-500 dark:text-gray-400">Fail Audits</div>
            <div class="mt-1 text-3xl font-semibold text-red-600 dark:text-red-400">{{ number_format($failAudits) }}</div>
        </div>
    </div>

    <!-- Audit Reports Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Audit Reports</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Click “View” to see variance details.</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-900">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Audit Date</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Branch</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Batch No.</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Allocated</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Scanned</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Missing</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Extra</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Mismatch</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($audits as $audit)
                        @php
                            $props = $audit->properties;
                            $auditDate = data_get($props, 'audit_date') ?: optional($audit->created_at)->toDateTimeString();
                            $allocated = (int) data_get($props, 'total_allocated', 0);
                            $scanned = (int) data_get($props, 'total_scanned', 0);
                            $missing = (int) data_get($props, 'missing_items_count', 0);
                            $extra = (int) data_get($props, 'extra_items_count', 0);
                            $mismatch = (int) data_get($props, 'quantity_variances_count', 0);
                            $isPass = ($missing + $extra + $mismatch) === 0;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white whitespace-nowrap">
                                {{ $auditDate }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">
                                {{ $audit->subject?->name ?? 'Unknown Branch' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-700 dark:text-gray-300">
                                {{ $audit->subject?->batch ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white text-right">{{ number_format($allocated) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-white text-right">{{ number_format($scanned) }}</td>
                            <td class="px-4 py-3 text-sm text-red-600 dark:text-red-400 text-right">{{ number_format($missing) }}</td>
                            <td class="px-4 py-3 text-sm text-orange-600 dark:text-orange-400 text-right">{{ number_format($extra) }}</td>
                            <td class="px-4 py-3 text-sm text-yellow-600 dark:text-yellow-400 text-right">{{ number_format($mismatch) }}</td>
                            <td class="px-4 py-3 text-sm">
                                @if($isPass)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300">
                                        PASS
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300">
                                        FAIL
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button wire:click="openAudit({{ $audit->id }})" class="px-3 py-1.5 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                                    View
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                No audit reports found for the selected filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            {{ $audits->links() }}
        </div>
    </div>

    <!-- Audit Details Modal -->
    @if($showAuditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-black/50" wire:click="closeAuditModal"></div>
            <div class="relative w-full max-w-4xl bg-white dark:bg-gray-800 rounded-lg shadow-xl overflow-hidden">
                <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
                    <div>
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Audit Details</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-300">
                            {{ $selectedAudit['branch_name'] ?? '' }}
                            @if(!empty($selectedAudit['branch_batch']))
                                <span class="text-gray-400 dark:text-gray-500">•</span>
                                <span class="font-medium">Batch:</span> {{ $selectedAudit['branch_batch'] }}
                            @endif
                        </p>
                    </div>
                    <button wire:click="closeAuditModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                @php
                    $p = $selectedAudit['properties'] ?? [];
                    $missingItems = data_get($p, 'missing_items', []);
                    $extraItems = data_get($p, 'extra_items', []);
                    $quantityVariances = data_get($p, 'quantity_variances', []);
                @endphp

                <div class="p-4 space-y-6 max-h-[75vh] overflow-y-auto">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-gray-50 dark:bg-gray-900/40 rounded-lg p-4">
                            <div class="text-xs text-gray-500 dark:text-gray-400">Allocated</div>
                            <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ number_format((int) data_get($p, 'total_allocated', 0)) }}</div>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-900/40 rounded-lg p-4">
                            <div class="text-xs text-gray-500 dark:text-gray-400">Scanned</div>
                            <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ number_format((int) data_get($p, 'total_scanned', 0)) }}</div>
                        </div>
                        <div class="bg-red-50 dark:bg-red-900/30 rounded-lg p-4 border border-red-200 dark:border-red-800">
                            <div class="text-xs font-medium text-red-800 dark:text-red-200">Missing</div>
                            <div class="text-lg font-semibold text-red-800 dark:text-red-200">{{ number_format((int) data_get($p, 'missing_items_count', 0)) }}</div>
                        </div>
                        <div class="bg-yellow-50 dark:bg-yellow-900/30 rounded-lg p-4 border border-yellow-200 dark:border-yellow-800">
                            <div class="text-xs font-medium text-yellow-800 dark:text-yellow-200">Extra + Mismatch</div>
                            <div class="text-lg font-semibold text-yellow-800 dark:text-yellow-200">{{ number_format(((int) data_get($p, 'extra_items_count', 0)) + ((int) data_get($p, 'quantity_variances_count', 0))) }}</div>
                        </div>
                    </div>

                    @if(!empty($missingItems))
                        <div>
                            <h5 class="text-sm font-semibold text-red-700 dark:text-red-300 mb-2">Missing Items</h5>
                            <div class="overflow-x-auto border border-red-200 dark:border-red-800 rounded-lg">
                                <table class="min-w-full divide-y divide-red-200 dark:divide-red-800">
                                    <thead class="bg-red-50 dark:bg-red-900/20">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-red-700 dark:text-red-300 uppercase">Barcode</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-red-700 dark:text-red-300 uppercase">Product</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-red-700 dark:text-red-300 uppercase">SKU</th>
                                            <th class="px-3 py-2 text-right text-xs font-medium text-red-700 dark:text-red-300 uppercase">Allocated</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-red-100 dark:divide-red-900/40">
                                        @foreach($missingItems as $item)
                                            <tr>
                                                <td class="px-3 py-2 text-sm font-mono text-gray-900 dark:text-white">{{ data_get($item, 'barcode') }}</td>
                                                <td class="px-3 py-2 text-sm text-gray-900 dark:text-white">{{ data_get($item, 'product_name') }}</td>
                                                <td class="px-3 py-2 text-sm font-mono text-gray-500 dark:text-gray-400">{{ data_get($item, 'sku') }}</td>
                                                <td class="px-3 py-2 text-sm text-gray-900 dark:text-white text-right">{{ number_format((int) data_get($item, 'allocated_quantity', 0)) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if(!empty($extraItems))
                        <div>
                            <h5 class="text-sm font-semibold text-orange-700 dark:text-orange-300 mb-2">Extra Items</h5>
                            <div class="overflow-x-auto border border-orange-200 dark:border-orange-800 rounded-lg">
                                <table class="min-w-full divide-y divide-orange-200 dark:divide-orange-800">
                                    <thead class="bg-orange-50 dark:bg-orange-900/20">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-orange-700 dark:text-orange-300 uppercase">Barcode</th>
                                            <th class="px-3 py-2 text-right text-xs font-medium text-orange-700 dark:text-orange-300 uppercase">Scanned</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-orange-100 dark:divide-orange-900/40">
                                        @foreach($extraItems as $item)
                                            <tr>
                                                <td class="px-3 py-2 text-sm font-mono text-gray-900 dark:text-white">{{ data_get($item, 'barcode') }}</td>
                                                <td class="px-3 py-2 text-sm text-gray-900 dark:text-white text-right">{{ number_format((int) data_get($item, 'scanned_quantity', 0)) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if(!empty($quantityVariances))
                        <div>
                            <h5 class="text-sm font-semibold text-yellow-700 dark:text-yellow-300 mb-2">Quantity Mismatches</h5>
                            <div class="overflow-x-auto border border-yellow-200 dark:border-yellow-800 rounded-lg">
                                <table class="min-w-full divide-y divide-yellow-200 dark:divide-yellow-800">
                                    <thead class="bg-yellow-50 dark:bg-yellow-900/20">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-yellow-700 dark:text-yellow-300 uppercase">Barcode</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-yellow-700 dark:text-yellow-300 uppercase">Product</th>
                                            <th class="px-3 py-2 text-left text-xs font-medium text-yellow-700 dark:text-yellow-300 uppercase">SKU</th>
                                            <th class="px-3 py-2 text-right text-xs font-medium text-yellow-700 dark:text-yellow-300 uppercase">Allocated</th>
                                            <th class="px-3 py-2 text-right text-xs font-medium text-yellow-700 dark:text-yellow-300 uppercase">Scanned</th>
                                            <th class="px-3 py-2 text-right text-xs font-medium text-yellow-700 dark:text-yellow-300 uppercase">Variance</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-yellow-100 dark:divide-yellow-900/40">
                                        @foreach($quantityVariances as $item)
                                            @php $variance = (int) data_get($item, 'variance', 0); @endphp
                                            <tr>
                                                <td class="px-3 py-2 text-sm font-mono text-gray-900 dark:text-white">{{ data_get($item, 'barcode') }}</td>
                                                <td class="px-3 py-2 text-sm text-gray-900 dark:text-white">{{ data_get($item, 'product_name') }}</td>
                                                <td class="px-3 py-2 text-sm font-mono text-gray-500 dark:text-gray-400">{{ data_get($item, 'sku') }}</td>
                                                <td class="px-3 py-2 text-sm text-gray-900 dark:text-white text-right">{{ number_format((int) data_get($item, 'allocated_quantity', 0)) }}</td>
                                                <td class="px-3 py-2 text-sm text-gray-900 dark:text-white text-right">{{ number_format((int) data_get($item, 'scanned_quantity', 0)) }}</td>
                                                <td class="px-3 py-2 text-sm font-semibold text-right {{ $variance > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                                    {{ $variance > 0 ? '+' : '' }}{{ $variance }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif

                    @if(empty($missingItems) && empty($extraItems) && empty($quantityVariances))
                        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg p-4">
                            <div class="text-sm text-emerald-800 dark:text-emerald-200 font-medium">No variances found.</div>
                            <div class="text-xs text-emerald-700 dark:text-emerald-300">Scanned inventory matches the allocated products for this branch.</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

