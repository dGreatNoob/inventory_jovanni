<x-slot:header>Purchase Order Analytics</x-slot:header>
<x-slot:subheader>Analytics and Reports</x-slot:subheader>

<div class="max-w-7xl mx-auto p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
    {{-- Filters Section --}}
    <div class="mb-6 p-4 bg-gray-50 rounded-lg dark:bg-gray-700">
        <div class="grid gap-4 md:grid-cols-4">
            <div>
                <label for="dateFrom" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">From Date</label>
                <input type="date" id="dateFrom" wire:model.live="dateFrom" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            </div>
            <div>
                <label for="dateTo" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">To Date</label>
                <input type="date" id="dateTo" wire:model.live="dateTo" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
            </div>
            <div>
                <label for="selectedSupplier" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Supplier</label>
                <select id="selectedSupplier" wire:model.live="selectedSupplier" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="reportType" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Report Type</label>
                <select id="reportType" wire:model.live="reportType" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                    <option value="summary">Summary</option>
                    <option value="outstanding">Outstanding POs</option>
                    <option value="supplier_history">Supplier History</option>
                    <option value="lead_time">Lead Time Analysis</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Export Button --}}
    <!-- <div class="mb-4">
        <button wire:click="exportReport" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
            Export Report
        </button>
    </div> -->

    {{-- Report Content --}}
    @if($reportType === 'summary')
        {{-- Summary Report --}}
        <div class="grid gap-4 md:grid-cols-3 mb-6">
            <div class="bg-blue-50 p-4 rounded-lg dark:bg-blue-900/20">
                <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100">Total POs</h3>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $data['summary']['total_pos'] ?? 0 }}</p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg dark:bg-green-900/20">
                <h3 class="text-lg font-semibold text-green-900 dark:text-green-100">Total Amount</h3>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">₱{{ number_format($data['summary']['total_amount'] ?? 0, 2) }}</p>
            </div>
            <div class="bg-purple-50 p-4 rounded-lg dark:bg-purple-900/20">
                <h3 class="text-lg font-semibold text-purple-900 dark:text-purple-100">Total Quantity</h3>
                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ number_format($data['summary']['total_qty'] ?? 0, 2) }}</p>
            </div>
        </div>

        {{-- Status Breakdown --}}
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Purchase Orders by Status</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Count</th>
                            <th scope="col" class="px-6 py-3">Total Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['summary']['by_status'] ?? [] as $status)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ ucfirst($status->status->value) }}</td>
                            <td class="px-6 py-4">{{ $status->count }}</td>
                            <td class="px-6 py-4">₱{{ number_format($status->total ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($reportType === 'outstanding')
        {{-- Outstanding POs --}}
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Outstanding Purchase Orders</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">PO Number</th>
                            <th scope="col" class="px-6 py-3">Supplier</th>
                            <th scope="col" class="px-6 py-3">Order Date</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Total Amount</th>
                            <th scope="col" class="px-6 py-3">Department</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['outstanding'] ?? [] as $po)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $po->po_num }}</td>
                            <td class="px-6 py-4">{{ $po->supplier->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $po->order_date ? \Carbon\Carbon::parse($po->order_date)->format('M d, Y') : 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    @if($po->status->value === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300
                                    @elseif($po->status->value === 'approved') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300
                                    @elseif($po->status->value === 'to_receive') bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300
                                    @elseif($po->status->value === 'received') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300
                                    @elseif($po->status->value === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300
                                    @endif">
                                    {{ ucfirst($po->status->value) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">₱{{ number_format($po->total_price ?? 0, 2) }}</td>
                            <td class="px-6 py-4">{{ $po->department->name ?? 'N/A' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No outstanding purchase orders found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($reportType === 'supplier_history')
        {{-- Supplier Purchase History --}}
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Supplier Purchase History</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Supplier</th>
                            <th scope="col" class="px-6 py-3">Total Orders</th>
                            <th scope="col" class="px-6 py-3">Total Spent</th>
                            <th scope="col" class="px-6 py-3">Total Items</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['supplier_history'] ?? [] as $history)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $history->supplier->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $history->total_orders }}</td>
                            <td class="px-6 py-4">₱{{ number_format($history->total_spent ?? 0, 2) }}</td>
                            <td class="px-6 py-4">{{ number_format($history->total_items ?? 0, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No supplier history found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($reportType === 'lead_time')
        {{-- Lead Time Analysis --}}
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-white">Lead Time Analysis</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">PO Number</th>
                            <th scope="col" class="px-6 py-3">Supplier</th>
                            <th scope="col" class="px-6 py-3">Order Date</th>
                            <th scope="col" class="px-6 py-3">Expected Delivery</th>
                            <th scope="col" class="px-6 py-3">Actual Delivery</th>
                            <th scope="col" class="px-6 py-3">Expected Lead Time (days)</th>
                            <th scope="col" class="px-6 py-3">Actual Lead Time (days)</th>
                            <th scope="col" class="px-6 py-3">Variance (days)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($data['lead_time'] ?? [] as $item)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $item['po_num'] }}</td>
                            <td class="px-6 py-4">{{ $item['supplier'] }}</td>
                            <td class="px-6 py-4">{{ $item['order_date'] ? \Carbon\Carbon::parse($item['order_date'])->format('M d, Y') : 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $item['expected_delivery'] ? \Carbon\Carbon::parse($item['expected_delivery'])->format('M d, Y') : 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $item['actual_delivery'] ? \Carbon\Carbon::parse($item['actual_delivery'])->format('M d, Y') : 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $item['expected_lead_time'] ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $item['actual_lead_time'] ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                @if($item['difference'] !== null)
                                    <span class="{{ $item['difference'] > 0 ? 'text-red-600' : ($item['difference'] < 0 ? 'text-green-600' : 'text-gray-600') }}">
                                        {{ $item['difference'] > 0 ? '+' : '' }}{{ $item['difference'] }}
                                    </span>
                                @else
                                    N/A
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No lead time data available</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>