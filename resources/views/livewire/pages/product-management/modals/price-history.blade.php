<!-- Price History Modal -->
<flux:modal name="product-price-history" class="max-w-xl">
    <div class="pr-2">
        <div class="mb-4">
            <flux:heading size="lg" class="text-gray-900 dark:text-white">Price History</flux:heading>
            <flux:subheading class="text-gray-600 dark:text-gray-400">Past price adjustments for this product</flux:subheading>
        </div>

        @php
            // Temporary dummy data until audit logging is wired
            $__dummyHistory = [
                [
                    'changed_at' => now()->subDays(7)->format('Y-m-d H:i'),
                    'user' => 'Admin User',
                    'old' => 89.00,
                    'new' => 95.50,
                    'reason' => 'Supplier increase'
                ],
                [
                    'changed_at' => now()->subDays(30)->format('Y-m-d H:i'),
                    'user' => 'Inventory Clerk',
                    'old' => 99.00,
                    'new' => 89.00,
                    'reason' => 'Promo pricing (REG2 → REG1)'
                ],
            ];
        @endphp

        <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-0 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm">
                <thead class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium">Changed At</th>
                        <th class="px-4 py-2 text-left font-medium">User</th>
                        <th class="px-4 py-2 text-right font-medium">Old</th>
                        <th class="px-4 py-2 text-right font-medium">New</th>
                        <th class="px-4 py-2 text-left font-medium">Reason</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-800 dark:text-gray-200">
                    @foreach($__dummyHistory as $row)
                        <tr>
                            <td class="px-4 py-2 whitespace-nowrap">{{ $row['changed_at'] }}</td>
                            <td class="px-4 py-2 whitespace-nowrap">{{ $row['user'] }}</td>
                            <td class="px-4 py-2 text-right whitespace-nowrap">₱{{ number_format($row['old'], 2) }}</td>
                            <td class="px-4 py-2 text-right whitespace-nowrap">₱{{ number_format($row['new'], 2) }}</td>
                            <td class="px-4 py-2">{{ $row['reason'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-end space-x-3 pt-6">
            <flux:modal.close>
                <flux:button variant="primary">Close</flux:button>
            </flux:modal.close>
        </div>
    </div>
</flux:modal>

<!-- Price History Modal -->
<flux:modal name="price-history" class="max-w-3xl" aria-labelledby="price-history-title">
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <flux:heading id="price-history-title" size="lg" class="text-gray-900 dark:text-white">Price History</flux:heading>
            <flux:button variant="ghost" wire:click="$dispatch('close-modal', { name: 'price-history' })" aria-label="Close">Close</flux:button>
        </div>

        @if(empty($priceHistories))
            <p class="text-sm text-gray-500 dark:text-gray-400">No price changes recorded.</p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Changed By</th>
                            <th scope="col" class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Old Price</th>
                            <th scope="col" class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">New Price</th>
                            <th scope="col" class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Note</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($priceHistories as $h)
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($h['changed_at'])->format('Y-m-d H:i') }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ data_get($h, 'changed_by.name', '—') }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-right text-gray-700 dark:text-gray-300">{{ is_null($h['old_price']) ? '—' : '₱' . number_format($h['old_price'], 2) }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white font-medium">₱{{ number_format($h['new_price'], 2) }}</td>
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">{{ $h['pricing_note'] ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</flux:modal>
