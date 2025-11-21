<flux:modal name="product-price-history" class="max-w-3xl" aria-labelledby="price-history-title">
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <flux:heading id="price-history-title" size="lg" class="text-gray-900 dark:text-white">Price History</flux:heading>
            <flux:modal.close aria-label="Close" />
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
                                <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $h['changed_at'] ? \Carbon\Carbon::parse($h['changed_at'])->format('Y-m-d H:i') : '—' }}
                                </td>
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
