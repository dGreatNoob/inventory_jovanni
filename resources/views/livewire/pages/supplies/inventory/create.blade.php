<x-slot:header>Create Product Profile</x-slot:header>
<x-slot:subheader>Add New Product to Inventory</x-slot:subheader>

<div class="pt-4">
    <!-- Back Button -->
    <div class="mb-6">
        <a href="{{ route('supplies.inventory') }}" 
           class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-blue-300 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Inventory
        </a>
    </div>

    <!-- Create Product Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700">

            <div class="p-6">
                <form wire:submit.prevent="create">
                    <!-- Basic Information Section -->
                    <div class="mb-8">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Basic Information</h4>
                        <div class="grid gap-6 md:grid-cols-3">
                            <div>
                                <label for="supply_sku" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">SKU</label>
                                <input type="text" id="supply_sku" wire:model="supply_sku"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Enter SKU" required />
                            </div>
                            <div>
                                <label for="supply_item_class" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Item Class</label>
                                <select id="supply_item_class" wire:model="supply_item_class"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="">Select Item Class</option>
                                    <option value="consumable">Consumable</option>
                                    <option value="accessories">Accessories</option>
                                </select>
                            </div>
                            <div>
                                <label for="item_type_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Item Type</label>
                                <select id="item_type_id" wire:model="item_type_id"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="">Select Item Type</option>
                                    @foreach($itemTypes as $itemType)
                                        <option value="{{ $itemType->id }}">{{ $itemType->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Product Details Section -->
                    <div class="mb-8">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Product Details</h4>
                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <label for="allocation_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Allocation</label>
                                <select id="allocation_id" wire:model="allocation_id"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="">Select Allocation</option>
                                    @foreach($allocations as $allocation)
                                        <option value="{{ $allocation->id }}">{{ $allocation->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="supply_uom" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Unit Of Measure</label>
                                <select id="supply_uom" wire:model="supply_uom"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="">Select unit of measure</option>
                                    @foreach($uomOptions as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-6">
                            <label for="supply_description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description</label>
                            <textarea id="supply_description" wire:model="supply_description" rows="3"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                placeholder="Enter product description"></textarea>
                        </div>
                    </div>

                    <!-- Inventory Management Section -->
                    <div class="mb-8">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Inventory Management</h4>
                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <label for="supply_qty" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Initial Quantity</label>
                                <input type="number" step="1" id="supply_qty" wire:model="supply_qty"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="0" />
                            </div>
                            <div>
                                <label for="low_stock_threshold_percentage" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Low Stock Threshold (%)</label>
                                <input type="number" step="1" id="low_stock_threshold_percentage" wire:model="low_stock_threshold_percentage"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Enter threshold (e.g., 20)" />
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Section -->
                    <div class="mb-8">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Pricing</h4>
                        <div class="grid gap-6 md:grid-cols-2">
                            <div>
                                <label for="unit_price" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Unit Price</label>
                                <input type="number" step="0.01" id="unit_price" wire:model="unit_price"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="0.00" />
                            </div>
                            <div>
                                <label for="unit_cost" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Unit Cost</label>
                                <input type="number" step="0.01" id="unit_cost" wire:model="unit_cost"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="0.00" />
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-600">
                        <a href="{{ route('supplies.inventory') }}" 
                           class="px-6 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-blue-300 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700">
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-6 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            Create Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
