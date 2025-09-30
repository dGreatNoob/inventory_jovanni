<form wire:submit.prevent="submit" class="space-y-6 max-w-4xl mx-auto">

    <div>
        <label>Sales Order</label>
        <input type="number" wire:model="sales_order_id" required />
        @error('sales_order_id') <span class="text-red-600">{{ $message }}</span> @enderror
    </div>

    <div>
        <label>Customer</label>
        <select wire:model="customer_id" required>
            <option value="">-- Select Customer --</option>
            @foreach($customers as $customer)
                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
            @endforeach
        </select>
        @error('customer_id') <span class="text-red-600">{{ $message }}</span> @enderror
    </div>

    <div>
        <label>Return Date</label>
        <input type="date" wire:model="return_date" required />
        @error('return_date') <span class="text-red-600">{{ $message }}</span> @enderror
    </div>

    <div>
        <label>Return Reference</label>
        <input type="text" wire:model="return_reference" />
        @error('return_reference') <span class="text-red-600">{{ $message }}</span> @enderror
    </div>

    <div>
        <label>Status</label>
        <select wire:model="status" required>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </select>
        @error('status') <span class="text-red-600">{{ $message }}</span> @enderror
    </div>

    <div>
        <label>Reason</label>
        <textarea wire:model="reason"></textarea>
        @error('reason') <span class="text-red-600">{{ $message }}</span> @enderror
    </div>

    <hr/>

    <h3>Return Items</h3>

    <table class="w-full border border-gray-200">
        <thead>
            <tr class="bg-gray-100">
                <th class="border px-2 py-1">Product</th>
                <th class="border px-2 py-1">Quantity</th>
                <th class="border px-2 py-1">Unit Price</th>
                <th class="border px-2 py-1">Total Price</th>
                <th class="border px-2 py-1">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($returnItems as $index => $item)
                <tr>
                    <td class="border p-1">
                        <select wire:model="returnItems.{{ $index }}.product_id" required>
                            <option value="">-- Select Product --</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        @error("returnItems.{$index}.product_id") <span class="text-red-600">{{ $message }}</span> @enderror
                    </td>
                    <td class="border p-1">
                        <input type="number" min="1" wire:model="returnItems.{{ $index }}.quantity" required />
                        @error("returnItems.{$index}.quantity") <span class="text-red-600">{{ $message }}</span> @enderror
                    </td>
                    <td class="border p-1 text-right">
                        {{ number_format($item['unit_price'] ?? 0, 2) }}
                    </td>
                    <td class="border p-1 text-right">
                        {{ number_format($item['total_price'] ?? 0, 2) }}
                    </td>
                    <td class="border p-1 text-center">
                        <button type="button" wire:click.prevent="removeReturnItem({{ $index }})" class="text-red-600">Remove</button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <button type="button" wire:click.prevent="addReturnItem" class="mt-3 px-3 py-1 bg-blue-600 text-white rounded">Add Item</button>

    <div class="mt-5 text-right font-semibold">
        Total Refund: ₱{{ number_format($total_refund, 2) }}
    </div>

    <button type="submit" class="mt-4 px-6 py-2 bg-green-600 text-white rounded">Save Return</button>

    @if (session()->has('message'))
        <div class="mt-4 p-2 text-green-800 bg-green-200 rounded">
            {{ session('message') }}
        </div>
    @endif
</form>
