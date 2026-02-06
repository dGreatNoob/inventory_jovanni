<x-slot:header>Branch Management</x-slot:header>
<x-slot:subheader>Stcok Transfer</x-slot:subheader>
@script
    <script>
        // VDR CSV Download Handler
        window.addEventListener('download-vdr', (event) => {
            const {
                content,
                filename
            } = event.detail;

            // Create a blob with the CSV content
            const blob = new Blob([content], {
                type: 'text/csv;charset=utf-8;'
            });
            const url = window.URL.createObjectURL(blob);

            // Create a temporary link and trigger download
            const link = document.createElement('a');
            if (link.download !== undefined) {
                link.href = url;
                link.download = filename;
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }

            // Clean up
            window.URL.revokeObjectURL(url);
        });

        // VDR Print Handler
        window.addEventListener('open-vdr-print', (event) => {
            const {
                batchId
            } = event.detail;

            // Open print window with the VDR content
            const printUrl = `/allocation/vdr/print/${batchId}`;
            window.open(printUrl, '_blank', 'width=800,height=600');
        });

        // VDR Excel Download Handler
        window.addEventListener('open-excel-download', (event) => {
            const {
                url
            } = event.detail;

            // Open the Excel export URL which will trigger a download
            window.open(url, '_blank');
        });

        // PDF Download Handler
        window.addEventListener('open-pdf-download', (event) => {
            const {
                url
            } = event.detail;

            // Open the PDF export URL which will trigger a download
            window.open(url, '_blank');
        });

        // Delivery Receipt Download Handler
        window.addEventListener('download-delivery-receipt', (event) => {
            const {
                url,
                filename
            } = event.detail;

            // Open the delivery receipt URL which will trigger a download
            window.open(url, '_blank');
        });

        // Alternative: Listen for Livewire events
        document.addEventListener('DOMContentLoaded', function() {
            // VDR CSV Download
            window.Livewire.on('download-vdr', (data) => {
                const {
                    content,
                    filename
                } = data[0];

                const blob = new Blob([content], {
                    type: 'text/csv;charset=utf-8;'
                });
                const url = window.URL.createObjectURL(blob);

                const link = document.createElement('a');
                if (link.download !== undefined) {
                    link.href = url;
                    link.download = filename;
                    link.style.visibility = 'hidden';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }

                window.URL.revokeObjectURL(url);
            });

            // VDR Print
            window.Livewire.on('open-vdr-print', (data) => {
                const {
                    batchId
                } = data[0];

                const printUrl = `/allocation/vdr/print/${batchId}`;
                window.open(printUrl, '_blank', 'width=800,height=600');
            });

            // VDR Excel Download
            window.Livewire.on('open-excel-download', (data) => {
                const {
                    url
                } = data[0];

                // Open the Excel export URL which will trigger a download
                window.open(url, '_blank');
            });

            // Delivery Receipt Download
            window.Livewire.on('download-delivery-receipt', (data) => {
                const {
                    url,
                    filename
                } = data[0];

                // Open the delivery receipt URL which will trigger a download
                window.open(url, '_blank');
            });

            // PDF Download
            window.Livewire.on('open-pdf-download', (data) => {
                const {
                    url
                } = data[0];

                // Open the PDF export URL which will trigger a download
                window.open(url, '_blank');
            });

            // Success popup handler
            window.Livewire.on('show-success-popup', (data) => {
                const {
                    title,
                    message
                } = data[0];

                Swal.fire({
                    icon: 'success',
                    title: title,
                    text: message,
                    timer: 3000,
                    showConfirmButton: false
                });
            });

            // Info popup handler
            window.Livewire.on('show-info-popup', (data) => {
                const {
                    title,
                    message
                } = data[0];

                Swal.fire({
                    icon: 'info',
                    title: title,
                    text: message,
                    confirmButtonText: 'OK'
                });
            });
        });
    </script>
@endscript

<div class="pt-4">
    <!-- Success/Error Messages -->
    @if (session()->has('message'))
        <div
            class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                        clip-rule="evenodd"></path>
                </svg>
                <span class="ml-2 text-green-700 dark:text-green-300">{{ session('message') }}</span>
            </div>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd"></path>
                </svg>
                <span class="ml-2 text-red-700 dark:text-red-300">{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Branch Transfer</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Transfer products between branches</p>
            </div>

            <button wire:click="openStepper"
                class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Start Transfer
            </button>
        </div>

        <!-- Transfer Statistics -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold">{{ $transfers->count() }}</div>
                        <div class="text-blue-200 text-sm">Total Transfers</div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold">{{ $transfers->where('status', 'completed')->count() }}</div>
                        <div class="text-green-200 text-sm">Completed</div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-4 text-white">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-purple-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold">{{ $transfers->sum('total_quantity') }}</div>
                        <div class="text-purple-200 text-sm">Items Transferred</div>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg p-4 text-white">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-orange-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <div class="text-2xl font-bold">₱{{ number_format($transfers->sum('total_value'), 0) }}</div>
                        <div class="text-orange-200 text-sm">Total Value</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Area: Show either Transfer History or Stepper -->
    @if (!$showStepper)
        <!-- Transfer History Table -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Transfer History</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        View all branch transfer records
                    </p>
                </div>
            </div>

            <!-- Search and Filters -->
            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                            Search Transfers
                        </label>
                        <input type="text" id="search" wire:model.live.debounce.300ms="search"
                            placeholder="Transfer number..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                    </div>

                    <div>
                        <label for="dateFrom" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                            Date From
                        </label>
                        <input type="date" id="dateFrom" wire:model.live="dateFrom"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                    </div>

                    <div>
                        <label for="dateTo" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                            Date To
                        </label>
                        <input type="date" id="dateTo" wire:model.live="dateTo"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                    </div>

                    <div class="flex items-end">
                        <button wire:click="clearFilters"
                            class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                            Clear Filters
                        </button>
                    </div>
                </div>
            </div>

            <!-- Transfers Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Transfer Number
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                From → To
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Items
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Total Value
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Created By
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($transfers as $transfer)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $transfer->transfer_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $transfer->created_at->format('M d, Y') }}
                                    <div class="text-xs text-gray-400 dark:text-gray-500">
                                        {{ $transfer->created_at->format('h:i A') }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    <div class="flex items-center">
                                        <span class="font-medium">{{ $transfer->sourceBranch->name ?? 'N/A' }}</span>
                                        <svg class="w-4 h-4 mx-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                        </svg>
                                        <span class="font-medium">{{ $transfer->destinationBranch->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $transfer->total_quantity }} items
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    ₱{{ number_format($transfer->total_value, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($transfer->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300
                                        @elseif($transfer->status === 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300
                                        @else bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300 @endif">
                                        {{ ucfirst($transfer->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ $transfer->creator->name ?? 'System' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                    No transfer records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- Stepper Workflow (Inline) -->
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg mb-6">
            <!-- Stepper Header -->
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Create Branch Transfer</h3>
                    <button wire:click="closeStepper"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

                <!-- Stepper Navigation -->
                <div class="px-6 py-8 border-b border-gray-200 dark:border-gray-700">
                    <nav aria-label="Progress">
                        <ol class="flex items-center w-full space-x-4">
                            <li
                                class="flex w-full items-center after:content-[''] after:w-full after:h-1 {{ $currentStep > 1 ? 'after:border-gray-300' : 'after:border-gray-200 dark:after:border-gray-600' }} after:border-4 after:inline-block after:ms-4 after:rounded-full">
                                <span
                                    class="flex items-center justify-center w-10 h-10 {{ $currentStep > 1 ? 'bg-gray-100' : ($currentStep == 1 ? 'bg-gray-100' : 'bg-gray-100 dark:bg-gray-700') }} rounded-full lg:h-12 lg:w-12 shrink-0">
                                    @if ($currentStep > 1)
                                        <svg class="w-5 h-5 text-gray-600" aria-hidden="true"
                                            xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="M5 11.917 9.724 16.5 19 7.5" />
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 {{ $currentStep == 1 ? 'text-gray-600' : 'text-gray-400' }}"
                                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" fill="none" viewBox="0 0 24 24">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    @endif
                                </span>
                            </li>
                            <li
                                class="flex w-full items-center after:content-[''] after:w-full after:h-1 {{ $currentStep > 2 ? 'after:border-gray-300' : 'after:border-gray-200 dark:after:border-gray-600' }} after:border-4 after:inline-block after:ms-4 after:rounded-full">
                                <span
                                    class="flex items-center justify-center w-10 h-10 {{ $currentStep > 2 ? 'bg-gray-100' : ($currentStep == 2 ? 'bg-gray-100' : 'bg-gray-100 dark:bg-gray-700') }} rounded-full lg:h-12 lg:w-12 shrink-0">
                                    <svg class="w-5 h-5 {{ $currentStep > 2 ? 'text-gray-600' : ($currentStep == 2 ? 'text-gray-600' : 'text-gray-400') }}"
                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                        height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M15 9h3m-3 3h3m-3 3h3m-6 1c-.306-.613-.933-1-1.618-1H7.618c-.685 0-1.312.387-1.618 1M4 5h16a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1Zm7 5a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z" />
                                    </svg>
                                </span>
                            </li>
                            <li
                                class="flex w-full items-center after:content-[''] after:w-full after:h-1 {{ $currentStep > 3 ? 'after:border-gray-300' : 'after:border-gray-200 dark:after:border-gray-600' }} after:border-4 after:inline-block after:ms-4 after:rounded-full">
                                <span
                                    class="flex items-center justify-center w-10 h-10 {{ $currentStep > 3 ? 'bg-gray-100' : ($currentStep == 3 ? 'bg-gray-100' : 'bg-gray-100 dark:bg-gray-700') }} rounded-full lg:h-12 lg:w-12 shrink-0">
                                    <svg class="w-5 h-5 {{ $currentStep > 3 ? 'text-gray-600' : ($currentStep == 3 ? 'text-gray-600' : 'text-gray-400') }}"
                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                        height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M20 7h-3a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h3m-5 4H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2Z" />
                                    </svg>
                                </span>
                            </li>
                            <li class="flex items-center w-full">
                                <span
                                    class="flex items-center justify-center w-10 h-10 {{ $currentStep == 4 ? 'bg-gray-100' : 'bg-gray-100 dark:bg-gray-700' }} rounded-full lg:h-12 lg:w-12 shrink-0">
                                    <svg class="w-5 h-5 {{ $currentStep == 4 ? 'text-gray-600' : 'text-gray-400' }}"
                                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24"
                                        height="24" fill="none" viewBox="0 0 24 24">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M8.5 8.5v.01M8.5 12v.01M8.5 15.5v.01M12 8.5v.01M12 12v.01M12 15.5v.01M15.5 8.5v.01M15.5 12v.01M15.5 15.5v.01" />
                                    </svg>
                                </span>
                            </li>
                        </ol>
                    </nav>
                </div>

                <!-- Step Content -->
                <div class="p-6">
                    <!-- STEP 1: SELECT SOURCE BRANCH -->
                    @if ($currentStep === 1)
                        <div>
                            <h4 class="text-md font-medium mb-4">Step 1: Select Source Branch</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                Choose the branch from which you want to transfer products. Only branches with received products will be shown.
                            </p>
                            <form wire:submit="selectSourceBranch" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="source_branch"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                            Source Branch *
                                        </label>
                                        <select id="source_branch" wire:model="sourceBranchId"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                            <option value="">Select Source Branch</option>
                                            @foreach ($availableBranches as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('sourceBranchId')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div
                                    class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                                    <button type="button" wire:click="closeStepper"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500">
                                        Cancel
                                    </button>

                                    <button type="submit"
                                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Select & Continue
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    <!-- STEP 2: SELECT PRODUCTS TO TRANSFER -->
                    @if ($currentStep === 2)
                        <div>
                            <h4 class="text-md font-medium mb-4">Step 2: Select Products to Transfer from {{ $sourceBranch->name ?? 'Source Branch' }}</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                Select products with "Received" status to transfer to another branch.
                            </p>

                            <!-- Product Selection -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                                <h5 class="font-medium mb-3">Available Products for Transfer</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                    Choose which received products you want to transfer.
                                </p>

                                @if (!empty($availableProducts))
                                    <div class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden max-h-80 overflow-y-auto">
                                        <table class="min-w-full">
                                            <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                                                <tr>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Select</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Product Details</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                        Available Qty</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white dark:bg-gray-800">
                                                @foreach ($availableProducts as $product)
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 border-l-4 border-blue-200">
                                                        <td class="px-4 py-3 pl-8">
                                                            <input type="checkbox"
                                                                wire:model.live="selectedProductIds"
                                                                value="{{ $product['id'] }}"
                                                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                        </td>
                                                        <td class="px-4 py-3">
                                                            <div class="space-y-1">
                                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                                    {{ $product['name'] }}
                                                                    @if ($product['color'])
                                                                        <span class="text-gray-500 dark:text-gray-400"> · {{ $product['color']['name'] ?? $product['color']['code'] }}</span>
                                                                    @endif
                                                                </div>
                                                                <div class="text-xs font-mono text-gray-600 dark:text-gray-400">
                                                                    SKU: {{ $product['sku'] ?? '—' }}
                                                                </div>
                                                                <div class="text-xs text-gray-600 dark:text-gray-400">
                                                                    Supplier Code: {{ $product['supplier_code'] ?? '—' }}
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-3">
                                                            <div class="text-sm text-gray-900 dark:text-white">
                                                                {{ intval($product['available_quantity']) }}
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-8 border border-gray-200 dark:border-gray-600 rounded-lg">
                                        <p class="text-gray-500 dark:text-gray-400">No received products available for transfer from this branch.</p>
                                    </div>
                                @endif

                                @if (empty($selectedProductIds))
                                    <div class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                                        <p class="text-sm text-yellow-700 dark:text-yellow-300">Please select at least one product to proceed with transfer.</p>
                                    </div>
                                @endif
                            </div>

                            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                                <button type="button" wire:click="previousStep"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500">
                                    Back
                                </button>
                                <button type="button" wire:click="nextStep"
                                    @if (empty($selectedProductIds)) disabled @endif
                                    class="px-4 py-2 text-sm font-medium text-white bg-gray-600 border border-transparent rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 disabled:bg-gray-400">
                                    Continue to Destination
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- STEP 3: SELECT DESTINATION BRANCH -->
                    @if ($currentStep === 3)
                        <div>
                            <h4 class="text-md font-medium mb-4">Step 3: Select Destination Branch</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                Choose the branch where you want to transfer the selected products.
                            </p>
                            <form wire:submit="selectDestinationBranch" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="destination_branch"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                            Destination Branch *
                                        </label>
                                        <select id="destination_branch" wire:model="destinationBranchId"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                            <option value="">Select Destination Branch</option>
                                            @foreach ($availableDestinationBranches as $branch)
                                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('destinationBranchId')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                                    <button type="button" wire:click="previousStep"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500">
                                        Back
                                    </button>
                                    <button type="submit"
                                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        Select & Continue
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    <!-- STEP 4: CONFIRM TRANSFER -->
                    @if ($currentStep === 4)
                        <div>
                            <h4 class="text-md font-medium mb-4">Step 4: Confirm Transfer</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                                Review the transfer details and enter quantities for each product.
                            </p>

                            <!-- Transfer Summary -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                                <h5 class="font-medium mb-3">Transfer Summary</h5>
                                <div class="grid grid-cols-2 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-600 dark:text-gray-400">From:</span>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $sourceBranch->name ?? 'N/A' }}</div>
                                    </div>
                                    <div>
                                        <span class="text-gray-600 dark:text-gray-400">To:</span>
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $destinationBranch->name ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Product Transfer Matrix -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                                <h5 class="font-medium mb-3">Product Transfer Matrix</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                    Enter quantities to transfer for each product. Quantity cannot exceed available stock.
                                </p>

                                @if (!empty($selectedProducts))
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full bg-white dark:bg-gray-800 border-collapse border border-gray-300 dark:border-gray-600">
                                            <thead>
                                                <tr>
                                                    <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-700">
                                                        Product</th>
                                                    <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase min-w-[120px] bg-gray-50 dark:bg-gray-700">
                                                        Available Qty</th>
                                                    <th class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase min-w-[120px] bg-gray-50 dark:bg-gray-700">
                                                        Transfer Qty</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($selectedProducts as $product)
                                                    <tr>
                                                        <td class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm bg-gray-50 dark:bg-gray-700">
                                                            <div class="space-y-1">
                                                                <div class="font-medium text-gray-900 dark:text-white">
                                                                    {{ $product['name'] }}
                                                                    @if ($product['color'])
                                                                        <span class="text-gray-500 dark:text-gray-400"> · {{ $product['color']['name'] ?? $product['color']['code'] }}</span>
                                                                    @endif
                                                                </div>
                                                                <div class="text-xs font-mono text-gray-600 dark:text-gray-400">
                                                                    SKU: {{ $product['sku'] ?? '—' }}
                                                                </div>
                                                                <div class="text-xs text-gray-600 dark:text-gray-400">
                                                                    Supplier Code: {{ $product['supplier_code'] ?? '—' }}
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center bg-white dark:bg-gray-800">
                                                            <span class="text-sm font-semibold">{{ intval($product['available_quantity']) }}</span>
                                                        </td>
                                                        <td class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center bg-white dark:bg-gray-800">
                                                            <input type="number"
                                                                wire:model.blur="transferQuantities.{{ $product['product_id'] }}"
                                                                min="0" max="{{ intval($product['available_quantity']) }}"
                                                                placeholder="0"
                                                                class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-gray-500 focus:border-gray-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white text-center">
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mt-4 flex justify-end items-center space-x-3">
                                        <button wire:click="executeTransfer"
                                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            Execute Transfer
                                        </button>
                                    </div>
                                @else
                                    <p class="text-gray-500 dark:text-gray-400">No products selected for transfer.</p>
                                @endif
                            </div>

                            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                                <button type="button" wire:click="previousStep"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500">
                                    Back
                                </button>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
            </div>
        @endif

    </div>
</div>