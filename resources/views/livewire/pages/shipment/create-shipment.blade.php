<div class="p-4 max-w-2xl mx-auto bg-white shadow rounded">
    <form wire:submit.prevent="createShipment" class="space-y-4">
        @csrf

        @error('shipment_error') <p class="text-red-500">{{ $message }}</p> @enderror
        @if (session()->has('success'))
            <p class="text-green-600">{{ session('success') }}</p>
        @endif

        {{-- Sales Order Dropdown --}}
        <div>
            <label for="sales_order_id" class="block font-semibold">Sales Order</label>
            <select wire:model="sales_order_id" id="sales_order_id" class="w-full border rounded p-2">
                <option value="">Select Sales Order</option>
                @foreach ($salesOrders as $order)
                    <option value="{{ $order->id }}">#{{ $order->id }} - {{ $order->customer->name }}</option>
                @endforeach
            </select>
            @error('sales_order_id') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>

        {{-- Customer Info (auto-filled if order is selected) --}}
        <div>
            <label class="block font-semibold">Customer Name</label>
            <input type="text" wire:model="customer_name" class="w-full border rounded p-2" readonly>
        </div>

        <div>
            <label class="block font-semibold">Customer Address</label>
            <textarea wire:model="customer_address" class="w-full border rounded p-2" rows="2" readonly></textarea>
        </div>

        {{-- Scheduled Ship Date --}}
        <div>
            <label class="block font-semibold">Scheduled Ship Date</label>
            <input type="date" wire:model="scheduled_ship_date" class="w-full border rounded p-2">
            @error('scheduled_ship_date') <span class="text-red-500">{{ $message }}</span> @enderror
        </div>

        {{-- Delivery Method --}}
        <div>
            <label class="block font-semibold">Delivery Method</label>
            <input type="text" wire:model="delivery_method" class="w-full border rounded p-2" placeholder="e.g., Courier, Pickup">
        </div>

        {{-- Carrier Name --}}
        <div>
            <label class="block font-semibold">Carrier or Driver Name</label>
            <input type="text" wire:model="carrier_name" class="w-full border rounded p-2">
        </div>

        {{-- Vehicle Plate Number --}}
        <div>
            <label class="block font-semibold">Vehicle Plate Number</label>
            <input type="text" wire:model="vehicle_plate_number" class="w-full border rounded p-2">
        </div>

        {{-- Shipping Priority --}}
        <div>
            <label class="block font-semibold">Shipping Priority</label>
            <select wire:model="shipping_priority" class="w-full border rounded p-2">
                <option value="normal">Normal</option>
                <option value="rush">Rush</option>
                <option value="express">Express</option>
            </select>
        </div>

        {{-- Special Handling Notes --}}
        <div>
            <label class="block font-semibold">Special Handling Notes</label>
            <textarea wire:model="special_handling_notes" class="w-full border rounded p-2" rows="3"></textarea>
        </div>

        {{-- Submit --}}
        <div>
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Create Shipment
            </button>
        </div>
    </form>
</div>

<div class="p-4 max-w-7xl mx-auto">
    <h2 class="text-xl font-semibold mb-4">Shipment List</h2>

    {{-- Filters --}}
    <div class="flex items-center space-x-4 mb-4">
        <div>
            <label class="font-medium">Filter by Status:</label>
            <select wire:model="filterStatus" class="border rounded p-2">
                <option value="">All</option>
                <option value="ready">Ready</option>
                <option value="shipped">Shipped</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        <div class="flex-1">
            <input
                wire:model.debounce.300ms="search"
                type="text"
                placeholder="Search by customer or shipment #"
                class="w-full border p-2 rounded"
            >
        </div>
    </div>

    {{-- Table --}}
    <table class="w-full border text-sm bg-white shadow-sm rounded">
        <thead>
            <tr class="bg-gray-100">
                <th class="p-2 border">Shipment #</th>
                <th class="p-2 border">Sales Order</th>
                <th class="p-2 border">Customer</th>
                <th class="p-2 border">Scheduled Date</th>
                <th class="p-2 border">Status</th>
                <th class="p-2 border">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($shipments as $shipment)
                <tr>
                    <td class="p-2 border">{{ $shipment->shipping_plan_num }}</td>
                    <td class="p-2 border">#{{ $shipment->sales_order_id }}</td>
                    <td class="p-2 border">{{ $shipment->customer_name }}</td>
                    <td class="p-2 border">{{ $shipment->scheduled_ship_date }}</td>
                    <td class="p-2 border">
                        <span class="px-2 py-1 text-white rounded
                            @if($shipment->shipping_status == 'ready') bg-blue-500
                            @elseif($shipment->shipping_status == 'shipped') bg-orange-500
                            @elseif($shipment->shipping_status == 'delivered') bg-green-600
                            @elseif($shipment->shipping_status == 'cancelled') bg-red-500
                            @endif">
                            {{ ucfirst($shipment->shipping_status) }}
                        </span>
                    </td>
                    <td class="p-2 border space-x-2">
                        @if ($shipment->shipping_status === 'ready')
                            <button wire:click="markAsShipped({{ $shipment->id }})" class="text-orange-600 hover:underline">Mark as Shipped</button>
                        @elseif ($shipment->shipping_status === 'shipped')
                            <button wire:click="markAsDelivered({{ $shipment->id }})" class="text-green-600 hover:underline">Mark as Delivered</button>
                        @else
                            <span class="text-gray-400">No Action</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center text-gray-500 py-4">No shipments found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    <div class="mt-4">
        {{ $shipments->links() }}
    </div>
</div>
