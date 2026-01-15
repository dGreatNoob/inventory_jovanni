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

<div>


    <!-- STEP 1: CREATE BATCH ALLOCATION -->
    <div class="mb-6">

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
        <!-- Header Section with Create Button -->
        <div class="mb-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Warehouse Batch Allocations</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage and dispatch batch allocations to
                        branches</p>
                </div>

                <!-- Only show Create button when stepper is NOT open -->
                @if (!$showStepper)
                    <button wire:click="openStepper"
                        class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create New Batch
                    </button>
                @endif
            </div>
        </div>


        <!-- StePPER WORKFLOW (Inline, not modal) -->
        @if ($showStepper)
            <div
                class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-lg mb-6">
                <!-- Stepper Header -->
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Create New Batch Allocation</h3>
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
                                                stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
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
                    <!-- STEP 1: CREATE BATCH -->
                    <!-- STEP 1: CREATE BATCH -->
                    @if ($currentStep === 1)
                        <div>
                            <h4 class="text-md font-medium mb-4">
                                @if ($isEditing)
                                    Step 1: Edit Batch Details
                                @else
                                    Step 1: Create New Batch
                                @endif
                            </h4>
                            <form wire:submit="createBatch" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="batch_number"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                            Batch Numbers *
                                        </label>
                                        <div
                                            class="border border-gray-300 rounded-md shadow-sm p-2 max-h-48 overflow-y-auto dark:bg-gray-600 dark:border-gray-500">
                                            @foreach ($availableBatchNumbers as $batchNum)
                                                <label
                                                    class="flex items-center p-2 hover:bg-gray-100 dark:hover:bg-gray-500 cursor-pointer">
                                                    <input type="checkbox" wire:model.live="selectedBatchNumbers"
                                                        value="{{ $batchNum }}"
                                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                    <span
                                                        class="ml-2 text-sm text-gray-900 dark:text-white">{{ $batchNum }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        @if ($isEditing)
                                            <p class="mt-1 text-xs text-blue-600 dark:text-blue-400">
                                                You can modify batch selection when editing
                                            </p>
                                        @endif
                                        @error('selectedBatchNumbers')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="ref_no"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                            Reference Number
                                        </label>
                                        <input type="text" id="ref_no" wire:model="ref_no" readonly
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm bg-gray-100 dark:bg-gray-500 dark:border-gray-500 dark:text-white">
                                    </div>

                                    <div>
                                        <label for="remarks"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                            Remarks (Optional)
                                        </label>
                                        <textarea id="remarks" wire:model="remarks" rows="2"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white"
                                            placeholder="e.g., 'Dispatched by Mark', 'For VisMin route'"></textarea>
                                        @error('remarks')
                                            <span class="text-red-500 text-sm">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="status"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                            Status
                                        </label>
                                        <select id="status" wire:model="status"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                            <option value="draft">Draft</option>
                                        </select>
                                    </div>
                                </div>

                                @if ($isEditing)
                                    <!-- Show current batch info when editing -->
                                    <div
                                        class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                        <h5 class="font-medium text-blue-900 dark:text-blue-100 mb-2">Editing Batch
                                            Information</h5>
                                        <div class="grid grid-cols-2 gap-4 text-sm">
                                            <div>
                                                <span class="text-blue-700 dark:text-blue-300">Batch Number:</span>
                                                <div class="font-medium text-blue-900 dark:text-blue-100">
                                                    {{ $batch_number }}</div>
                                            </div>
                                            <div>
                                                <span class="text-blue-700 dark:text-blue-300">Branches:</span>
                                                <div class="font-medium text-blue-900 dark:text-blue-100">
                                                    {{ $currentBatch->branchAllocations->count() }} branches</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div
                                    class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                                    <button type="button" wire:click="closeStepper"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500">
                                        Cancel
                                    </button>

                                    @if ($isEditing)
                                        <!-- When editing, show Update and Continue buttons separately -->
                                        <button type="submit"
                                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                            💾 Update Details
                                        </button>
                                        <button type="button" wire:click="nextStep"
                                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            Continue to Branches →
                                        </button>
                                    @else
                                        <!-- When creating, show single button -->
                                        <button type="submit"
                                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            Create & Continue
                                        </button>
                                    @endif
                                </div>
                            </form>
                        </div>
                    @endif

                    <!-- STEP 2: DISPLAY BRANCHES -->
                    @if ($currentStep === 2)
                        <div>
                            <h4 class="text-md font-medium mb-4">Step 2: Branches from Batches:
                                {{ implode(', ', $selectedBatchNumbers) }}</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                All branches from the selected batches have been automatically added to this allocation.
                            </p>

                            <div
                                class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-64 overflow-y-auto mb-6">
                                @foreach ($filteredBranchesByBatch as $branch)
                                    <div
                                        class="flex items-center p-3 border border-gray-200 dark:border-gray-600 rounded-lg">
                                        <div class="flex-1">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                {{ $branch['name'] }}</div>
                                            @if ($branch['address'])
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $branch['address'] }}</div>
                                            @endif
                                        </div>
                                        <span
                                            class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300 rounded-full">
                                            Auto-added
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                            @if (empty($filteredBranchesByBatch))
                                <div class="text-center py-8">
                                    <p class="text-gray-500 dark:text-gray-400">No branches found for batch:
                                        {{ $batch_number }}</p>
                                </div>
                            @endif

                            <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                                <button type="button" wire:click="previousStep"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500">
                                    Back
                                </button>
                                <button type="button" wire:click="nextStep"
                                    @if (empty($filteredBranchesByBatch)) disabled @endif
                                    class="px-4 py-2 text-sm font-medium text-white bg-gray-600 border border-transparent rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 disabled:bg-gray-400">
                                    Continue to Products
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- STEP 3: ADD PRODUCTS (Applied to all branches) -->
                    @if ($currentStep === 3)
                        <div>
                            <h4 class="text-md font-medium mb-4">Step 3: Add Products to All Branches</h4>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                                Select products to allocate and enter quantities for each product and branch
                                combination.
                            </p>

                            <!-- Product Filtering Controls -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                                <h5 class="font-medium mb-3">Filter Products</h5>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                    <!-- Category Filter -->
                                    <div>
                                        <label for="category-filter"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                            Category
                                        </label>
                                        <select id="category-filter" wire:model.live="selectedCategoryId"
                                            wire:change="filterProducts"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                            <option value="">All Categories</option>
                                            @foreach ($availableCategories as $category)
                                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Product Filter -->
                                    <div>
                                        <label for="product-filter"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                            Product Name
                                        </label>
                                        <select id="product-filter" wire:model.live="selectedProductFilterName"
                                            wire:change="filterProducts"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                                            <option value="">All Products</option>
                                            @foreach ($availableProducts->groupBy('name') as $productName => $variants)
                                                <option value="{{ $productName }}">{{ $productName }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Show All Button -->
                                    <!-- <div class="flex items-end">
                                        <button wire:click="showAllProducts"
                                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            Show All Products
                                        </button>
                                    </div> -->
                                </div>

                                <!-- Filtered Product Selection -->
                                <div class="flex items-center justify-between mb-3">
                                    <h5 class="font-medium">Select Products for Allocation</h5>
                                    <div class="flex items-center space-x-2">
                                        @if ($selectedCategoryId || $selectedProductFilterName || $showAllProducts)
                                            <button type="button" wire:click="selectAllVisible"
                                                class="px-3 py-1 text-sm font-medium text-gray-600 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                                Select All Visible
                                            </button>
                                        @endif
                                        @if (!empty($temporarySelectedProducts))
                                            <button type="button" wire:click="addSelectedProductsToAllocation"
                                                class="px-3 py-1 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                Add Selected ({{ count($temporarySelectedProducts) }})
                                            </button>
                                        @endif
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                    Choose which products you want to allocate to branches. Only selected products will
                                    appear in the allocation matrix below.
                                </p>

                                <!-- Show Already Selected Products -->
                                @if (!empty($selectedProductIdsForAllocation))
                                    <div
                                        class="mb-4 p-3 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700 rounded-lg">
                                        <h6 class="text-sm font-medium text-green-800 dark:text-green-200 mb-2">
                                            Products Added to Allocation
                                            ({{ count($selectedProductIdsForAllocation) }})
                                        </h6>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach ($availableProducts->whereIn('id', $selectedProductIdsForAllocation) as $product)
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">
                                                    {{ $product->sku }} - {{ $product->name }}
                                                    @if ($product->color)
                                                        ({{ $product->color->code }})
                                                    @endif
                                                </span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if ($selectedCategoryId || $selectedProductFilterName || $showAllProducts)
                                    @if (($showAllProducts ? $availableProducts : $filteredProducts)->count() > 0)
                                        <div
                                            class="border border-gray-200 dark:border-gray-600 rounded-lg overflow-hidden max-h-80 overflow-y-auto">
                                            <table class="min-w-full">
                                                <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                                                    <tr>
                                                        <th
                                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                            Select</th>
                                                        <th
                                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                            Product Details</th>
                                                        <th
                                                            class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                                            Price & Stock</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="bg-white dark:bg-gray-800">
                                                    @php
                                                        $products = $showAllProducts
                                                            ? $availableProducts
                                                            : $filteredProducts;
                                                        // Group products by product name
                                                        $groupedProducts = $products->groupBy('name');
                                                    @endphp
                                                    @foreach ($groupedProducts as $productName => $productVariants)
                                                        <!-- Product Group Header -->
                                                        <tr class="bg-gray-100 dark:bg-gray-700">
                                                            <td colspan="3"
                                                                class="px-4 py-2 border-t border-gray-300 dark:border-gray-600">
                                                                <div class="flex items-center justify-between">
                                                                    <span
                                                                        class="text-sm font-semibold text-gray-800 dark:text-white">
                                                                        {{ $productName }}
                                                                        <span
                                                                            class="text-xs text-gray-600 dark:text-gray-400">({{ $productVariants->count() }}
                                                                            colors)</span>
                                                                    </span>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <!-- Product Variants -->
                                                        @foreach ($productVariants as $product)
                                                            <tr
                                                                class="hover:bg-gray-50 dark:hover:bg-gray-700 border-l-4 border-blue-200">
                                                                <td class="px-4 py-3 pl-8">
                                                                    <input type="checkbox"
                                                                        wire:model.live="temporarySelectedProducts"
                                                                        value="{{ $product->id }}"
                                                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                                                </td>
                                                                <td class="px-4 py-3">
                                                                    @php
                                                                        $colorCode = $product->color->code ?? '';
                                                                    @endphp
                                                                    <div class="text-sm text-gray-900 dark:text-white">
                                                                        <span
                                                                            class="font-mono bg-blue-100 dark:bg-blue-900 px-2 py-1 rounded text-xs">{{ $product->sku }}</span>
                                                                        <span
                                                                            class="ml-2 font-medium">{{ $product->name }}</span>
                                                                    </div>
                                                                    <div
                                                                        class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                                        @if ($colorCode)
                                                                            <span class="font-medium">Color:
                                                                                {{ $colorCode }}</span>
                                                                        @endif
                                                                        @if ($product->color && $product->color->name)
                                                                            <span
                                                                                class="ml-2">({{ $product->color->name }})</span>
                                                                        @endif
                                                                        @if ($product->color && $product->color->code)
                                                                            <span
                                                                                class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                                                                style="background-color: {{ $product->color->code }}20; color: {{ $product->color->code }}">
                                                                                {{ $product->color->code }}
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                                <td class="px-4 py-3">
                                                                    <div class="text-sm text-gray-900 dark:text-white">
                                                                        ₱{{ number_format($product->price ?? ($product->selling_price ?? 0), 2) }}
                                                                    </div>
                                                                    <div
                                                                        class="text-xs text-gray-500 dark:text-gray-400">
                                                                        Stock:
                                                                        {{ intval($product->initial_quantity) ?? 0 }}
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div
                                            class="text-center py-8 border border-gray-200 dark:border-gray-600 rounded-lg">
                                            <p class="text-gray-500 dark:text-gray-400">No products match the selected
                                                filters.</p>
                                        </div>
                                    @endif
                                @else
                                    <div
                                        class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                        <div class="flex items-center justify-center">
                                            <svg class="h-5 w-5 text-yellow-600 mr-2" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="text-sm text-yellow-700 dark:text-yellow-300">
                                                Please use the filtering controls above to find and select products.
                                            </span>
                                        </div>
                                    </div>
                                @endif

                                @if (empty($selectedProductIdsForAllocation))
                                    <div
                                        class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                                        <p class="text-sm text-yellow-700 dark:text-yellow-300">Please select at least
                                            one product to proceed with allocation.</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Allocation Matrix -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                                <h5 class="font-medium mb-3">Product Allocation Matrix</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                    Enter quantities for each product and branch combination. Leave blank or 0 for no
                                    allocation.
                                </p>

                                @if ($currentBatch && !empty($selectedProductIdsForAllocation))
                                    @php
                                        // Show ALL products that have been added to allocation, regardless of current filters
                                        $filteredProductsForMatrix = $this->availableProductsForBatch->whereIn(
                                            'id',
                                            $selectedProductIdsForAllocation,
                                        );
                                    @endphp

                                    @if ($filteredProductsForMatrix->count() > 0)
                                        <div class="overflow-x-auto">
                                            <table
                                                class="min-w-full bg-white dark:bg-gray-800 border-collapse border border-gray-300 dark:border-gray-600">
                                                <thead>
                                                    <tr>
                                                        <th
                                                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase bg-gray-50 dark:bg-gray-700">
                                                            Branch</th>
                                                        @foreach ($filteredProductsForMatrix as $product)
                                                            <th
                                                                class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase min-w-[120px] bg-gray-50 dark:bg-gray-700">
                                                                <div class="flex flex-col items-center space-y-1">
                                                                    <button type="button"
                                                                        wire:click="removeProductFromAllocation({{ $product->id }})"
                                                                        class="text-red-500 hover:text-red-700 text-xs mb-1"
                                                                        title="Remove this product from allocation">
                                                                        <svg class="w-4 h-4" fill="none"
                                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round"
                                                                                stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M6 18L18 6M6 6l12 12"></path>
                                                                        </svg>
                                                                    </button>
                                                                    <div class="text-center">
                                                                        {{ $product->name }}
                                                                        {{ $product->color->name ?? '' }}
                                                                        ({{ $product->color->shortcut ?? '' }})
                                                                        <br>
                                                                        <span
                                                                            class="text-xs text-gray-400">₱{{ number_format($product->price ?? ($product->selling_price ?? 0), 2) }}</span><br>
                                                                        <span class="text-xs text-gray-400">Stock:
                                                                            {{ intval($product->initial_quantity) }}</span>
                                                                    </div>
                                                                </div>
                                                            </th>
                                                        @endforeach
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($currentBatch->branchAllocations as $branchAllocation)
                                                        <tr>
                                                            <td
                                                                class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700">
                                                                {{ $branchAllocation->branch->name }}
                                                            </td>
                                                            @foreach ($filteredProductsForMatrix as $product)
                                                                <td
                                                                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-center bg-white dark:bg-gray-800">
                                                                    <input type="number"
                                                                        wire:model.blur="matrixQuantities.{{ $branchAllocation->id }}.{{ $product->id }}"
                                                                        min="0" placeholder="0"
                                                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded focus:outline-none focus:ring-gray-500 focus:border-gray-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white text-center">
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>

                                        <div class="mt-4 flex justify-end items-center space-x-3">
                                            <!-- Flash Messages -->
                                            @if (session()->has('success'))
                                                <div
                                                    class="px-3 py-2 text-sm text-green-700 bg-green-100 border border-green-200 rounded-md">
                                                    {{ session('success') }}
                                                </div>
                                            @endif
                                            @if (session()->has('info'))
                                                <div
                                                    class="px-3 py-2 text-sm text-blue-700 bg-blue-100 border border-blue-200 rounded-md">
                                                    {{ session('info') }}
                                                </div>
                                            @endif

                                            <button wire:click="saveMatrixAllocations"
                                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                Save All Allocations
                                            </button>
                                        </div>
                                    @else
                                        <p class="text-gray-500 dark:text-gray-400">No products match the selected
                                            filters. Please adjust your filter criteria.</p>
                                    @endif
                                @else
                                    <p class="text-gray-500 dark:text-gray-400">Please select filters (Category or
                                        Product) to display the allocation matrix.</p>
                                @endif
                            </div>

                            <!-- Product Allocations Table -->
                            @if (!empty($productAllocations))
                                <div
                                    class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden">
                                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                                        <h5 class="font-medium text-gray-900 dark:text-white">Product Allocations</h5>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead class="bg-gray-50 dark:bg-gray-700">
                                                <tr>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                        Image</th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                        Product</th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                        Quantity</th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                        Unit Price</th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                        Total Value</th>
                                                    <th
                                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                        Applied to Branches</th>
                                                </tr>
                                            </thead>
                                            <tbody
                                                class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                @foreach ($productAllocations as $index => $allocation)
                                                    <tr>
                                                        <td class="px-6 py-4 whitespace-nowrap">
                                                            @if ($allocation['image'])
                                                                <img src="{{ asset('storage/' . $allocation['image']) }}"
                                                                    alt="{{ $allocation['product_name'] }}"
                                                                    class="w-12 h-12 rounded object-cover">
                                                            @else
                                                                <div
                                                                    class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center">
                                                                    <svg class="w-6 h-6 text-gray-400" fill="none"
                                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                            stroke-linejoin="round" stroke-width="2"
                                                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                                        </path>
                                                                    </svg>
                                                                </div>
                                                            @endif
                                                        </td>
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                            {{ $allocation['product_name'] }}</td>
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                            {{ $allocation['quantity'] }}</td>
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                            @if ($allocation['unit_price'])
                                                                ₱{{ number_format($allocation['unit_price'], 2) }}
                                                            @else
                                                                N/A
                                                            @endif
                                                        </td>
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                            @if ($allocation['total_value'] === 'Varies')
                                                                {{ $allocation['total_value'] }}
                                                            @else
                                                                ₱{{ number_format($allocation['total_value'], 2) }}
                                                            @endif
                                                        </td>
                                                        <td
                                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                                            {{ $allocation['applied_to_branches'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    </div>
                                    
                            @endif


                            <div
                                class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600 mt-6">
                                <button type="button" wire:click="previousStep"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500">
                                    Back
                                </button>
                                <button type="button" wire:click="nextStep"
                                    class="px-4 py-2 text-sm font-medium text-white bg-gray-600 border border-transparent rounded-md hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                                    Continue to Dispatch
                                </button>
                            </div>
                        </div>
                    @endif

                    <!-- STEP 4: DISPATCH -->
                    @if ($currentStep === 4)
                        <div>
                            <!-- Export Allocation Matrix Button -->
                            <div class="mb-6 flex justify-end space-x-3">
                                <button
                                    class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                                    Genereta DR
                                </button>
                                <button wire:click="exportAllocationToPDF"
                                    class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Export Allocation Matrix to PDF
                                </button>
                            </div>

                            <!-- Branch Selection for Scanning -->
                            <div
                                class="mb-6 p-4 bg-white dark:bg-gray-900 border-2 border-gray-200 dark:border-gray-700 rounded-lg">
                                <h4 class="font-bold text-lg text-gray-900 dark:text-gray-100 mb-3">
                                    Select Branch to Scan
                                </h4>
                                <p class="text-sm text-gray-700 dark:text-gray-300 mb-4">
                                    Choose which branch you are currently scanning products for. You must select a
                                    branch before scanning.
                                </p>
                                <div
                                    class="overflow-x-auto max-h-96 border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <table class="min-w-full bg-white dark:bg-gray-800">
                                        <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                                            <tr>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                    Status</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                    Branch Name</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                    Products</th>
                                                <th
                                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                                    Action</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach ($currentBatch->branchAllocations as $branchAllocation)
                                                @php
                                                    $isActive = $activeBranchId === $branchAllocation->id;
                                                    $isComplete = $this->isBranchComplete($branchAllocation->id);
                                                @endphp
                                                <tr
                                                    class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors
                                                        @if ($isActive) bg-blue-50 dark:bg-blue-900/20 @endif">
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                        @if ($isComplete)
                                                            <span
                                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300">
                                                                ✅ Complete
                                                            </span>
                                                        @elseif($isActive)
                                                            <span
                                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300">
                                                                🔄 Active
                                                            </span>
                                                        @else
                                                            <span
                                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200">
                                                                ⏳ Pending
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td
                                                        class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                        {{ $branchAllocation->branch->name }}
                                                    </td>
                                                    <td
                                                        class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $branchAllocation->items()->whereNull('box_id')->count() }} products
                                                    </td>
                                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                        @if (!$isActive)
                                                            <button type="button"
                                                                wire:click="setActiveBranch({{ $branchAllocation->id }})"
                                                                class="px-3 py-1 text-xs font-medium text-white bg-blue-600 border border-transparent rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                                Select
                                                            </button>
                                                        @else
                                                            <span
                                                                class="px-3 py-1 text-xs font-medium text-blue-600 bg-blue-100 rounded dark:bg-blue-900/20 dark:text-blue-300">
                                                                Current
                                                            </span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Box and DR Selection for Active Branch -->
                            @if ($activeBranchId)
                                <div class="mb-6 p-4 bg-white dark:bg-gray-900 border-2 border-green-500 dark:border-green-700 rounded-lg">
                                    <h4 class="font-bold text-lg text-gray-900 dark:text-gray-100 mb-3">
                                        Box & Delivery Receipt Management
                                    </h4>
                                    <p class="text-sm text-gray-700 dark:text-gray-300 mb-4">
                                        Create or select boxes and manage delivery receipts for scanning products.
                                    </p>

                                    <!-- Create New Box Button -->
                                    <div class="mb-4">
                                        <button wire:click="createNewBox"
                                            class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Create New Box
                                        </button>
                                    </div>

                                    <!-- Available Boxes Table -->
                                    @if (!empty($availableBoxes))
                                        <div class="overflow-x-auto max-h-64 border border-gray-200 dark:border-gray-700 rounded-lg">
                                            <table class="min-w-full bg-white dark:bg-gray-800">
                                                <thead class="bg-gray-50 dark:bg-gray-700 sticky top-0">
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Box Number</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Items</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Status</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">DR Status</th>
                                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">Action</th>
                                                </thead>
                                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                    @foreach ($availableBoxes as $box)
                                                        @php
                                                            $isSelected = $selectedBoxId === $box->id;
                                                            $dr = \App\Models\DeliveryReceipt::where('box_id', $box->id)->first();
                                                        @endphp
                                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ $isSelected ? 'bg-green-50 dark:bg-green-900/20' : '' }}">
                                                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                                                {{ $box->box_number }}
                                                            </td>
                                                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                                                {{ $box->current_count }}
                                                            </td>
                                                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                                @if ($box->status === 'full')
                                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300">Full</span>
                                                                @elseif ($box->status === 'open')
                                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300">Open</span>
                                                                @else
                                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200">Closed</span>
                                                                @endif
                                                            </td>
                                                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                                @if ($dr)
                                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300">
                                                                        {{ $dr->dr_number }} ({{ $dr->type }})
                                                                    </span>
                                                                @else
                                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200">No DR</span>
                                                                @endif
                                                            </td>
                                                            <td class="px-4 py-3 whitespace-nowrap text-sm">
                                                                <div class="flex items-center space-x-2">
                                                                    @if (!$isSelected && $box->status !== 'closed')
                                                                        <button type="button" wire:click="selectBox({{ $box->id }})"
                                                                            class="px-3 py-1 text-xs font-medium text-white bg-blue-600 border border-transparent rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                                            Select
                                                                        </button>
                                                                    @elseif ($isSelected)
                                                                        <span class="px-3 py-1 text-xs font-medium text-green-600 bg-green-100 rounded dark:bg-green-900/20 dark:text-green-300">
                                                                            Active
                                                                        </span>
                                                                    @else
                                                                        <span class="px-3 py-1 text-xs font-medium text-gray-400 bg-gray-100 rounded dark:bg-gray-600 dark:text-gray-200">
                                                                            Closed
                                                                        </span>
                                                                    @endif
                                                                    @if ($box->status !== 'closed')
                                                                        <button type="button" wire:click="deleteBox({{ $box->id }})"
                                                                            wire:confirm="Are you sure you want to delete this box? All scanned items will be reset."
                                                                            class="px-3 py-1 text-xs font-medium text-white bg-red-600 border border-transparent rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                                            Delete
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <div class="text-center py-8 border border-gray-200 dark:border-gray-600 rounded-lg">
                                            <p class="text-gray-500 dark:text-gray-400 mb-4">No boxes available for this branch.</p>
                                            <p class="text-sm text-gray-400 dark:text-gray-500">Create a new box to start scanning products.</p>
                                        </div>
                                    @endif

                                    <!-- Current DR Info -->
                                    {{-- @if ($currentDr)
                                        <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                                            <h5 class="font-medium text-blue-900 dark:text-blue-100 mb-2">Active Delivery Receipt</h5>
                                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                                <div>
                                                    <span class="text-blue-700 dark:text-blue-300">DR Number:</span>
                                                    <div class="font-medium text-blue-900 dark:text-blue-100">{{ $currentDr->dr_number }}</div>
                                                </div>
                                                <div>
                                                    <span class="text-blue-700 dark:text-blue-300">Type:</span>
                                                    <div class="font-medium text-blue-900 dark:text-blue-100">{{ ucfirst($currentDr->type) }}</div>
                                                </div>
                                                <div>
                                                    <span class="text-blue-700 dark:text-blue-300">Status:</span>
                                                    <div class="font-medium text-blue-900 dark:text-blue-100">{{ ucfirst($currentDr->status) }}</div>
                                                </div>
                                                <div>
                                                    <span class="text-blue-700 dark:text-blue-300">Items Scanned:</span>
                                                    <div class="font-medium text-blue-900 dark:text-blue-100">{{ $currentDr->scanned_items }} / {{ $currentDr->total_items }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif --}}
                                </div>
                            @endif

                            <!-- Barcode Scanner Modal will be shown when box is selected -->


                                <div class="space-y-4"> <!-- COLUMN 2 -->
                                    <h3 class="text-lg font-medium mb-4">Step 4: Dispatch Batch</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                                        Review and dispatch your batch allocation. Scan items based on container/truck capacity.
                                        Dispatch is allowed even if all items are not scanned.
                                    </p>

                                    @if ($currentBatch)
                                        <!-- Product Allocation by Branch -->
                                        @if ($activeBranchId)
                                            {{-- Show only the active branch table --}}
                                            @php
                                                $activeBranchAllocation = $currentBatch->branchAllocations->find(
                                                    $activeBranchId,
                                                );
                                            @endphp

                                            @if ($activeBranchAllocation)
                                                <div
                                                    class="bg-white dark:bg-gray-800 border-2 border-blue-500 shadow-lg rounded-lg overflow-hidden mb-6">
                                                    <div
                                                        class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-blue-500 to-blue-600">
                                                        <div class="flex items-center justify-between">
                                                            <div>
                                                                <h5 class="font-bold text-lg text-white">
                                                                    {{ $activeBranchAllocation->branch->name }}
                                                                    <span
                                                                        class="ml-2 px-2 py-1 bg-white text-blue-600 rounded text-xs">ACTIVE</span>
                                                                </h5>
                                                                <p class="text-sm text-blue-100 mt-1">
                                                                    {{ $activeBranchAllocation->items()->whereNull('box_id')->count() }}
                                                                    products
                                                                    allocated
                                                                </p>
                                                            </div>
                                                            <div class="text-right">
                                                                @php
                                                                    $branchScannedCount = 0;
                                                                    $branchTotalProducts = $activeBranchAllocation->items()->whereNull('box_id')->count();
                                                                    $originalItems = $activeBranchAllocation->items()->whereNull('box_id')->get();
                                                                    foreach ($originalItems as $item) {
                                                                        $scannedQty =
                                                                            $scannedQuantities[
                                                                                $activeBranchAllocation->id
                                                                            ][$item->product_id] ?? 0;
                                                                        if ($scannedQty >= $item->quantity) {
                                                                            $branchScannedCount++;
                                                                        }
                                                                    }
                                                                    $branchProgress =
                                                                        $branchTotalProducts > 0
                                                                            ? round(
                                                                                ($branchScannedCount /
                                                                                    $branchTotalProducts) *
                                                                                    100,
                                                                            )
                                                                            : 0;
                                                                @endphp
                                                                <div class="text-2xl font-bold text-white">
                                                                    {{ $branchProgress }}%
                                                                </div>
                                                                <div class="text-xs text-blue-100">
                                                                    {{ $branchScannedCount }} /
                                                                    {{ $branchTotalProducts }}
                                                                    complete
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Container -->
<div class="border border-gray-200 dark:border-gray-700 rounded-lg">

    <!-- Buttons above table -->
    {{-- <div class="px-4 py-2 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 flex justify-end space-x-2">
        <button type="button"
            wire:click="generateDeliveryReceipt({{ $activeBranchAllocation->id }})"
            class="inline-flex items-center px-3 py-1 text-xs font-medium text-white bg-green-600 rounded hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            Generate Delivery Receipt
        </button>

        <button type="button"
            wire:click="resetScannedQuantities({{ $activeBranchAllocation->branch->name }})"
            wire:confirm="Are you sure you want to reset all scanned quantities for {{ $activeBranchAllocation->branch->name }}? This cannot be undone."
            class="inline-flex items-center px-3 py-1 text-xs font-medium text-white bg-orange-600 rounded hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500">
            🔄 Reset Branch Scans
        </button>
    </div> --}}

    <!-- Table -->
    <table class="min-w-full table-fixed divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="w-16 px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Image</th>
                <th class="w-48 px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                <th class="w-36 px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Barcode</th>
                <th class="w-24 px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Allocated Qty</th>
                <th class="w-24 px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Scanned Qty</th>
                <th class="w-36 px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
            </tr>
        </thead>
    </table>

    <!-- Scrollable tbody -->
    <div class="max-h-64 overflow-y-auto">
        <table class="min-w-full table-fixed divide-y divide-gray-200 dark:divide-gray-700">
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @php
                    // Filter to show only original allocation items (not scanned tracking records)
                    $originalItems = $activeBranchAllocation->items->where('box_id', null);
                @endphp
                @if ($originalItems->count() > 0)
                    @foreach ($originalItems as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-4 py-2 w-16">
                                @if ($item->product->primary_image)
                                    <img src="{{ asset('storage/' . $item->product->primary_image) }}" alt="{{ $item->product->name }}" class="w-12 h-12 rounded object-cover">
                                @else
                                    <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 py-2 w-48 text-sm font-medium text-gray-900 dark:text-white truncate" title="{{ $item->display_name }}{{ $item->product && $item->product->color ? ' ' . $item->product->color->name : '' }}">
                                {{ $item->display_name }}{{ $item->product && $item->product->color ? ' ' . $item->product->color->name : '' }}
                            </td>
                            <td class="px-4 py-2 w-36 text-sm font-mono text-gray-500 dark:text-gray-400 truncate" title="{{ $item->display_barcode }}">
                                {{ $item->display_barcode }}
                            </td>
                            <td class="px-4 py-2 w-24 text-sm font-semibold text-lg text-gray-900 dark:text-white">
                                {{ $item->quantity }}
                            </td>
                            <td class="px-4 py-2 w-24 text-3xl font-bold text-blue-600 dark:text-blue-400">
                                @php
                                    // Calculate actual scanned quantity from box-specific records
                                    $actualScannedQty = \App\Models\BranchAllocationItem::where('branch_allocation_id', $activeBranchAllocation->id)
                                        ->where('product_id', $item->product_id)
                                        ->whereNotNull('box_id')
                                        ->sum('scanned_quantity');

                                    // Check if this product has been quantity edited AFTER the batch was created
                                    $hasBeenEdited = \Spatie\Activitylog\Models\Activity::where('log_name', 'branch_inventory')
                                        ->where('properties->barcode', $item->product->barcode ?? '')
                                        ->where('properties->branch_id', $activeBranchAllocation->branch_id)
                                        ->where('description', 'like', 'Updated allocated quantity%')
                                        ->where('created_at', '>', $currentBatch->created_at)
                                        ->exists();

                                    // If edited after batch creation, consider it fully scanned
                                    $totalScannedQty = $hasBeenEdited ? $item->quantity : $actualScannedQty;
                                @endphp
                                {{ $totalScannedQty }}
                            </td>
                            <td class="px-4 py-2 w-36 text-sm truncate">
                                @php
                                    $scannedQty = $totalScannedQty;
                                    $allocatedQty = $item->quantity;
                                @endphp
                                @if ($scannedQty == 0)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200">Not Scanned</span>
                                @elseif($scannedQty >= $allocatedQty)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100">✓ Complete</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100">{{ $allocatedQty - $scannedQty }} remaining</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="px-4 py-2 text-center text-sm text-gray-500 dark:text-gray-400">
                            No products allocated to this branch.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

                                                </div>
                                            @endif
                                        @else
                                            {{-- Show message when no branch is selected --}}
                                            <div
                                                class="bg-yellow-50 dark:bg-yellow-900/20 border-2 border-yellow-300 dark:border-yellow-700 rounded-lg p-8 mb-6 text-center">
                                                <div class="text-6xl mb-4">⚠️</div>
                                                <h4
                                                    class="text-xl font-bold text-yellow-800 dark:text-yellow-200 mb-2">
                                                    No Branch Selected
                                                </h4>
                                                <p class="text-yellow-700 dark:text-yellow-300">
                                                    Please select a branch above to view and scan products.
                                                </p>
                                            </div>
                                        @endif

                                </div> <!-- END COLUMN 2 -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <!-- Batch Summary -->
    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
        <h4 class="font-medium mb-3">Batch Summary</h4>
        <div class="flex flex-wrap gap-6 text-sm">
            <div>
                <span class="text-gray-600 dark:text-gray-400">Reference:</span>
                <div class="font-medium">{{ $currentBatch->ref_no }}</div>
            </div>
            <div>
                <span class="text-gray-600 dark:text-gray-400">Branches:</span>
                <div class="font-medium">{{ $currentBatch->branchAllocations->count() }}</div>
            </div>
            <div>
                <span class="text-gray-600 dark:text-gray-400">Boxes:</span>
                <div class="font-medium">{{ $this->getTotalBoxesCount() }}</div>
            </div>
            <div>
                <span class="text-gray-600 dark:text-gray-400">Total Quantities:</span>
                <div class="font-medium">{{ $this->getTotalQuantitiesCount() }}</div>
            </div>
        </div>
    </div>

    <!-- Overall Scan Summary -->
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800
                rounded-lg p-4 mb-6">
        <h5 class="font-medium text-blue-900 dark:text-blue-100 mb-2">Overall Scan Summary</h5>
        <div class="flex flex-wrap gap-6 text-sm">
            <div>
                <span class="text-blue-700 dark:text-blue-300">Total Quantities:</span>
                <div class="font-medium text-blue-900 dark:text-blue-100 text-xl">
                    {{ $this->getTotalQuantitiesCount() }}
                </div>
            </div>
            <div>
                <span class="text-blue-700 dark:text-blue-300">Scanned Quantities:</span>
                <div class="font-medium text-blue-900 dark:text-blue-100 text-xl">
                    {{ $this->getTotalScannedQuantitiesCount() }} /
                    {{ $this->getTotalQuantitiesCount() }}
                </div>
            </div>
            <div>
                <span class="text-blue-700 dark:text-blue-300">Pending:</span>
                <div class="font-medium text-blue-900 dark:text-blue-100 text-xl">
                    {{ $this->getPendingItemsCount() }}
                </div>
            </div>
        </div>
    </div>

</div>


                               
                </div> <!-- END OF 2 COLUMNS -->
                 <div
                                    class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4 mb-6">
                                    <div class="flex">
                                        <svg class="h-5 w-5 text-yellow-400 mt-0.5" fill="currentColor"
                                            viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                clip-rule="evenodd"></path>
                                        </svg>
                                        <div class="ml-3">
                                            <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                                Ready
                                                to Dispatch</h3>
                                            <p class="mt-1 text-sm text-yellow-700 dark:text-yellow-300">
                                                Once dispatched, delivery receipts will be generated for all
                                                branches
                                                and this batch cannot be modified.
                                                You can dispatch based on scanned items (partial dispatch allowed).
                                            </p>
                                        </div>
                                    </div>
                                </div>
                    @endif

                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-gray-600">
                    <button type="button" wire:click="previousStep"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:bg-gray-600 dark:text-gray-200 dark:border-gray-500 dark:hover:bg-gray-500">
                        Back
                    </button>
                    <button type="button" wire:click="dispatchBatchFromStepper"
                        wire:confirm="Are you sure you want to dispatch this batch? This action cannot be undone."
                        class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        ✓ Dispatch Batch
                    </button>
                </div>
            </div>
            @push('scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const barcodeInput = document.getElementById('barcode-scanner-input');
                        let lastScrollPosition = 0;

                        if (barcodeInput) {
                            // Auto-focus on load
                            barcodeInput.focus();

                            // Save scroll position before Livewire update
                            window.addEventListener('livewire:update', function() {
                                lastScrollPosition = window.scrollY || window.pageYOffset;
                            });

                            // Restore scroll position after Livewire update
                            window.addEventListener('livewire:updated', function() {
                                window.scrollTo(0, lastScrollPosition);
                                // Refocus the input after update
                                setTimeout(() => {
                                    if (barcodeInput) {
                                        barcodeInput.focus();
                                    }
                                }, 50);
                            });

                            // Prevent scroll on focus
                            barcodeInput.addEventListener('focus', function(e) {
                                e.preventDefault();
                            });

                            // Refocus when clicking anywhere on the page (except buttons)
                            document.addEventListener('click', function(e) {
                                if (e.target.tagName !== 'BUTTON' && !e.target.closest('button')) {
                                    setTimeout(() => barcodeInput.focus(), 50);
                                }
                            });
                        }
                    });

                    // Additional Livewire hook to prevent scroll
                    document.addEventListener('livewire:initialized', () => {
                        let scrollPosition = 0;

                        Livewire.hook('morph.updating', ({
                            component,
                            cleanup
                        }) => {
                            scrollPosition = window.scrollY || window.pageYOffset;
                        });

                        Livewire.hook('morph.updated', ({
                            component
                        }) => {
                            window.scrollTo(0, scrollPosition);

                            // Refocus barcode input
                            const barcodeInput = document.getElementById('barcode-scanner-input');
                            if (barcodeInput) {
                                setTimeout(() => barcodeInput.focus(), 100);
                            }
                        });
                    });
                </script>
            @endpush
        @endif

    <!-- Barcode Scanner Side Panel -->
    <div
        x-data="{ open: @entangle('showBarcodeScannerModal').live }"
        x-cloak
        x-on:keydown.escape.window="if (open) { open = false; $wire.closeBarcodeScannerModal(); }"
    >
        <template x-teleport="body">
            <div
                x-show="open"
                x-transition.opacity
                class="fixed inset-0 z-50 flex"
            >
                <div
                    x-show="open"
                    x-transition.opacity
                    class="fixed inset-0 bg-neutral-900/30 dark:bg-neutral-900/50"
                    @click="open = false; $wire.closeBarcodeScannerModal()"
                ></div>

                <section
                    x-show="open"
                    x-transition:enter="transform transition ease-in-out duration-300"
                    x-transition:enter-start="translate-x-full"
                    x-transition:enter-end="translate-x-0"
                    x-transition:leave="transform transition ease-in-out duration-300"
                    x-transition:leave-start="translate-x-0"
                    x-transition:leave-end="translate-x-full"
                    class="relative ml-auto flex h-full w-full max-w-2xl"
                >
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500 dark:bg-blue-400"></div>

                    <div class="ml-[0.25rem] flex h-full w-full flex-col bg-white shadow-xl dark:bg-zinc-900">
                        <header class="flex items-start justify-between border-b border-gray-200 px-6 py-5 dark:border-zinc-700">
                            <div class="flex items-start gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-blue-600 dark:bg-blue-900/40 dark:text-blue-300">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                        Barcode Scanner
                                    </h2>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        @if ($activeBranchId && $currentBox && $currentDr)
                                            {{ $currentBatch->branchAllocations->find($activeBranchId)->branch->name }}
                                            • Box: {{ $currentBox->box_number }} • DR: {{ $currentDr->dr_number }}
                                        @else
                                            Select a box to begin scanning
                                        @endif
                                    </p>
                                </div>
                            </div>

                            <button
                                type="button"
                                class="rounded-full p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-gray-500 dark:hover:bg-zinc-800 dark:hover:text-gray-200"
                                @click="open = false; $wire.closeBarcodeScannerModal()"
                                aria-label="Close scanner panel"
                            >
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </header>

                        <div class="flex-1 overflow-hidden">
                            <div class="flex h-full flex-col">
                                <div class="flex-1 overflow-y-auto px-6 py-6">
                                    <div class="space-y-6">
                                        <!-- Barcode Input -->
                                        <section class="space-y-4">
                                            <div>
                                                <flux:heading size="md" class="text-gray-900 dark:text-white">Scan Product</flux:heading>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Scan product barcodes to allocate items to the current box.</p>
                                            </div>

                                            <div class="space-y-4">
                                                <div>
                                                    <label for="barcode-input" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                                                        Barcode
                                                    </label>
                                                    <input
                                                        id="barcode-input"
                                                        type="text"
                                                        wire:model.live="barcodeInput"
                                                        wire:keydown.enter="processBarcodeScanner"
                                                        placeholder="Scan barcode or enter manually..."
                                                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                                        x-ref="barcodeInput"
                                                        x-init="$nextTick(() => { if ($el) $el.focus(); })"
                                                        autofocus
                                                    />
                                                </div>
                                            </div>
                                        </section>

                                        <!-- Scan Feedback -->
                                        <section class="space-y-4">
                                            <div>
                                                <flux:heading size="md" class="text-gray-900 dark:text-white">Scan Status</flux:heading>
                                            </div>

                                            <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-700">
                                                <div class="flex items-start">
                                                    <div class="flex-shrink-0">
                                                        @if(str_contains($scanFeedback, '✅'))
                                                            <svg class="h-5 w-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        @elseif(str_contains($scanFeedback, '❌'))
                                                            <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        @elseif(str_contains($scanFeedback, '⚠️'))
                                                            <svg class="h-5 w-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        @else
                                                            <svg class="h-5 w-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                                            </svg>
                                                        @endif
                                                    </div>
                                                    <div class="ml-3 flex-1">
                                                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                                                            {{ $scanFeedback ?: 'Ready to scan products...' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>

                                        <!-- Scanned Items Table -->
                                        @if ($currentBox && $currentDr)
                                        <section class="space-y-4">
                                            <div>
                                                <flux:heading size="md" class="text-gray-900 dark:text-white">Scanned Items</flux:heading>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Items scanned into this box.</p>
                                            </div>

                                            <div class="rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                                    <thead class="bg-gray-50 dark:bg-gray-700">
                                                        <tr>
                                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Image</th>
                                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Product</th>
                                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Barcode</th>
                                                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantity</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                                        @php
                                                            $scannedItems = \App\Models\BranchAllocationItem::where('box_id', $currentBox->id)
                                                                ->with('product')
                                                                ->where('scanned_quantity', '>', 0)
                                                                ->get();
                                                        @endphp
                                                        @if ($scannedItems->count() > 0)
                                                            @foreach ($scannedItems as $item)
                                                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                                    <td class="px-4 py-3">
                                                                        @if ($item->product && $item->product->primary_image)
                                                                            <img src="{{ asset('storage/' . $item->product->primary_image) }}" alt="{{ $item->display_name }}" class="w-12 h-12 rounded object-cover">
                                                                        @else
                                                                            <div class="w-12 h-12 bg-gray-200 dark:bg-gray-600 rounded flex items-center justify-center">
                                                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2v12a2 2 0 002 2z"></path>
                                                                                </svg>
                                                                            </div>
                                                                        @endif
                                                                    </td>
                                                                    <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                                                        {{ $item->display_name }}
                                                                        @if ($item->product && $item->product->color)
                                                                            <span class="text-xs text-gray-500 dark:text-gray-400">({{ $item->product->color->name }})</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="px-4 py-3 text-sm font-mono text-gray-500 dark:text-gray-400">
                                                                        {{ $item->display_barcode }}
                                                                    </td>
                                                                    <td class="px-4 py-3 text-sm font-semibold text-blue-600 dark:text-blue-400">
                                                                        {{ $item->scanned_quantity }}
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                        @else
                                                            <tr>
                                                                <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                                                    No items scanned yet
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                        </section>
                                        @endif

                                        <!-- Current Status -->
                                        <section class="space-y-4">
                                            <div>
                                                <flux:heading size="md" class="text-gray-900 dark:text-white">Current Status</flux:heading>
                                            </div>

                                            <div class="space-y-4">
                                                <!-- Box Status -->
                                                @if ($currentBox)
                                                    <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                                                        <h5 class="font-medium text-blue-900 dark:text-blue-100 mb-3 flex items-center">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                            </svg>
                                                            Box Status
                                                        </h5>
                                                        <div class="space-y-2 text-sm">
                                                            <div class="flex justify-between">
                                                                <span class="text-blue-700 dark:text-blue-300">Box Number:</span>
                                                                <span class="font-medium text-blue-900 dark:text-blue-100">{{ $currentBox->box_number }}</span>
                                                            </div>
                                                            <div class="flex justify-between">
                                                                <span class="text-blue-700 dark:text-blue-300">Items in box:</span>
                                                                <span class="font-medium text-blue-900 dark:text-blue-100">{{ $currentBox->current_count }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- DR Status -->
                                                @if ($currentDr)
                                                    <div class="rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-800 dark:bg-green-900/20">
                                                        <h5 class="font-medium text-green-900 dark:text-green-100 mb-3 flex items-center">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                            </svg>
                                                            Delivery Receipt
                                                        </h5>
                                                        <div class="space-y-2 text-sm">
                                                            <div class="flex justify-between">
                                                                <span class="text-green-700 dark:text-green-300">DR Number:</span>
                                                                <span class="font-medium text-green-900 dark:text-green-100">{{ $currentDr->dr_number }}</span>
                                                            </div>
                                                            <div class="flex justify-between">
                                                                <span class="text-green-700 dark:text-green-300">Type:</span>
                                                                <span class="font-medium text-green-900 dark:text-green-100">{{ ucfirst($currentDr->type) }}</span>
                                                            </div>
                                                            <div class="flex justify-between">
                                                                <span class="text-green-700 dark:text-green-300">Items Scanned:</span>
                                                                <span class="font-medium text-green-900 dark:text-green-100">{{ $currentDr->scanned_items }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Branch Progress -->
                                                @if ($activeBranchId && $currentBatch)
                                                    @php
                                                        $branchAllocation = $currentBatch->branchAllocations->find($activeBranchId);
                                                        $totalItems = $branchAllocation ? $branchAllocation->items()->whereNull('box_id')->count() : 0;
                                                        $scannedItems = 0;
                                                        if ($branchAllocation) {
                                                            $originalItems = $branchAllocation->items()->whereNull('box_id')->get();
                                                            foreach ($originalItems as $item) {
                                                                // Calculate actual scanned quantity for this product
                                                                $productScannedQty = \App\Models\BranchAllocationItem::where('branch_allocation_id', $branchAllocation->id)
                                                                    ->where('product_id', $item->product_id)
                                                                    ->whereNotNull('box_id')
                                                                    ->sum('scanned_quantity');
                                                                if ($productScannedQty >= $item->quantity) {
                                                                    $scannedItems++;
                                                                }
                                                            }
                                                        }
                                                    @endphp
                                                    <div class="rounded-lg border border-purple-200 bg-purple-50 p-4 dark:border-purple-800 dark:bg-purple-900/20">
                                                        <h5 class="font-medium text-purple-900 dark:text-purple-100 mb-3 flex items-center">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                            </svg>
                                                            Branch Progress
                                                        </h5>
                                                        <div class="space-y-3 text-sm">
                                                            <div class="flex justify-between">
                                                                <span class="text-purple-700 dark:text-purple-300">Items Scanned:</span>
                                                                <span class="font-medium text-purple-900 dark:text-purple-100">{{ $scannedItems }} / {{ $totalItems }}</span>
                                                            </div>
                                                            <div class="w-full bg-purple-200 rounded-full h-2 dark:bg-purple-700">
                                                                <div class="bg-purple-600 h-2 rounded-full transition-all duration-300" style="width: {{ $totalItems > 0 ? ($scannedItems / $totalItems) * 100 : 0 }}%"></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </section>
                                    </div>
                                </div>

                                <div class="border-t border-gray-200 bg-white px-6 py-4 dark:border-zinc-700 dark:bg-zinc-900">
                                    <div class="flex items-center justify-between">
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            @if($currentBox && $currentDr)
                                                Scanning active • Box: {{ $currentBox->box_number }} • DR: {{ $currentDr->dr_number }}
                                            @else
                                                Select a box to begin scanning
                                            @endif
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            @if($currentBox && $currentDr)
                                                <button type="button" wire:click="declareBoxFull"
                                                    class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                    Declare as Full
                                                </button>
                                            @endif
                                            <flux:button type="button" wire:click="closeBarcodeScannerModal" variant="ghost">
                                                Close Scanner
                                            </flux:button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </template>
    </div>

    </div>
</div>
@endif
</div>

<!-- Search and Date Filters -->
<div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-4">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Search Filter -->
        <div>
            <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                Search Batches
            </label>
            <div class="relative">
                <input type="text" id="search" wire:model.live="search"
                    placeholder="Search by reference no, remarks, or branch name..."
                    class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Date From Filter -->
        <div>
            <label for="dateFrom" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                Date From
            </label>
            <input type="date" id="dateFrom" wire:model.live="dateFrom"
                class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
        </div>

        <!-- Date To Filter -->
        <div>
            <label for="dateTo" class="block text-sm font-medium text-gray-700 dark:text-gray-200 mb-1">
                Date To
            </label>
            <div class="flex space-x-2">
                <input type="date" id="dateTo" wire:model.live="dateTo"
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-gray-500 focus:border-gray-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                <button wire:click="clearFilters"
                    class="px-3 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm rounded-md focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                    Clear
                </button>
            </div>
        </div>
    </div>

    <!-- Active Filters Display -->
    @if ($search || $dateFrom || $dateTo)
        <div class="mt-3 flex flex-wrap items-center gap-2">
            <span class="text-sm text-gray-600 dark:text-gray-400">Active filters:</span>
            @if (isset($search) && $search)
                <span
                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300">
                    Search: "{{ $search }}"
                    <button wire:click="$set('search', '')"
                        class="ml-1 text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-200">
                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </span>
            @endif
            @if (isset($dateFrom) && $dateFrom)
                <span
                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300">
                    From: {{ \Carbon\Carbon::parse($dateFrom)->format('M d, Y') }}
                    <button wire:click="$set('dateFrom', '')"
                        class="ml-1 text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200">
                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </span>
            @endif
            @if (isset($dateTo) && $dateTo)
                <span
                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300">
                    To: {{ \Carbon\Carbon::parse($dateTo)->format('M d, Y') }}
                    <button wire:click="$set('dateTo', '')"
                        class="ml-1 text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-200">
                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </span>
            @endif
        </div>
    @endif
</div>

<!-- Table All-->
<div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 mb-4">
    <!-- Current Steps Mapping Table -->
    @php
        $steps = [
            1 => 'Step 1: Creating New Branch',
            2 => 'Step 2: Branches Adding',
            3 => 'Step 3: Adding Products',
            4 => 'Step 4: Dispatch Scanning',
        ];
    @endphp

    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 mt-6">
        <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                    <button wire:click="sortBy('ref_no')" class="flex items-center space-x-1 hover:text-blue-600 dark:hover:text-blue-400">
                        <span>Reference Number</span>
                        <div class="flex flex-col">
                            <svg class="w-3 h-3 {{ $sortField === 'ref_no' && $sortDirection === 'asc' ? 'text-blue-600' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                            </svg>
                            <svg class="w-3 h-3 {{ $sortField === 'ref_no' && $sortDirection === 'desc' ? 'text-blue-600' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </button>
                </th>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                    <button wire:click="sortBy('created_at')" class="flex items-center space-x-1 hover:text-blue-600 dark:hover:text-blue-400">
                        <span>Date Created</span>
                        <div class="flex flex-col">
                            <svg class="w-3 h-3 {{ $sortField === 'created_at' && $sortDirection === 'asc' ? 'text-blue-600' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                            </svg>
                            <svg class="w-3 h-3 {{ $sortField === 'created_at' && $sortDirection === 'desc' ? 'text-blue-600' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </button>
                </th>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                    Current Step
                </th>
                <!-- New Scan Progress Column -->
                <th class="px-4 py-2 text-left text-sm font-medium text-blue-700 dark:text-blue-200">
                    Scan Progress
                </th>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                    <button wire:click="sortBy('status')" class="flex items-center space-x-1 hover:text-blue-600 dark:hover:text-blue-400">
                        <span>Status</span>
                        <div class="flex flex-col">
                            <svg class="w-3 h-3 {{ $sortField === 'status' && $sortDirection === 'asc' ? 'text-blue-600' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                            </svg>
                            <svg class="w-3 h-3 {{ $sortField === 'status' && $sortDirection === 'desc' ? 'text-blue-600' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </button>
                </th>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                    <button wire:click="sortBy('batch_number')" class="flex items-center space-x-1 hover:text-blue-600 dark:hover:text-blue-400">
                        <span>Batch</span>
                        <div class="flex flex-col">
                            <svg class="w-3 h-3 {{ $sortField === 'batch_number' && $sortDirection === 'asc' ? 'text-blue-600' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"></path>
                            </svg>
                            <svg class="w-3 h-3 {{ $sortField === 'batch_number' && $sortDirection === 'desc' ? 'text-blue-600' : 'text-gray-400' }}" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </button>
                </th>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-700 dark:text-gray-200">
                    Actions
                </th>
            </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @foreach ($batches as $record)
                <tr>
                    <!-- Reference Number -->
                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-200">
                        {{ $record->ref_no }}
                    </td>

                    <!-- Date Created -->
                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-200">
                        {{ $record->created_at->format('M d, Y') }}
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $record->created_at->format('h:i A') }}
                        </div>
                    </td>

                    <!-- Current Step -->
                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-200">
                        @php
                            $batchStep = $batchSteps[$record->id] ?? 1;
                            $stepLabels = [
                                1 => 'Step 1: Creating New Batch',
                                2 => 'Step 2: Branches Adding',
                                3 => 'Step 3: Adding Products',
                                4 => 'Step 4: Dispatch Scanning',
                            ];
                        @endphp
                        <div class="flex items-center">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    @if ($batchStep == 4 && $record->status === 'dispatched') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300
                                    @elseif($batchStep == 4) bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-300
                                    @elseif($batchStep == 3) bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300
                                    @else bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-300 @endif">
                                {{ $batchStep == 4 && $record->status === 'dispatched' ? 'Completed' : $stepLabels[$batchStep] ?? 'Not Started' }}
                            </span>
                        </div>
                    </td>

                    <!-- Scan Progress -->
                    <td class="px-4 py-2 text-sm text-blue-900 dark:text-blue-200">
                        @php
                            $scannedQty = 0;
                            $allocatedQty = 0;
                            $allScanned = true;
                            foreach ($record->branchAllocations as $branchAllocation) {
                                // Only count original allocation items (without box_id)
                                $originalItems = $branchAllocation->items()->whereNull('box_id')->get();
                                foreach ($originalItems as $item) {
                                    $allocatedQty += $item->quantity ?? 0;

                                    // Check if this product has been quantity edited AFTER the batch was created
                                    $hasBeenEdited = \Spatie\Activitylog\Models\Activity::where('log_name', 'branch_inventory')
                                        ->where('properties->barcode', $item->product->barcode ?? '')
                                        ->where('properties->branch_id', $branchAllocation->branch_id)
                                        ->where('description', 'like', 'Updated allocated quantity%')
                                        ->where('created_at', '>', $record->created_at)
                                        ->exists();

                                    if ($hasBeenEdited) {
                                        // If edited, consider it fully scanned
                                        $scannedQty += $item->quantity ?? 0;
                                    } else {
                                        // Calculate actual scanned quantity for this product across all boxes
                                        $productScannedQty = \App\Models\BranchAllocationItem::where('branch_allocation_id', $branchAllocation->id)
                                            ->where('product_id', $item->product_id)
                                            ->whereNotNull('box_id')
                                            ->sum('scanned_quantity');
                                        $scannedQty += $productScannedQty;

                                        // Check if this product is fully scanned
                                        if ($productScannedQty < ($item->quantity ?? 0)) {
                                            $allScanned = false;
                                        }
                                    }
                                }
                            }
                        @endphp
                        @if ($allocatedQty > 0)
                            <span class="font-semibold">{{ $scannedQty }}/{{ $allocatedQty }}</span>
                            <span
                                class="ml-2 px-2 py-0.5 rounded text-xs font-medium
                                    @if ($scannedQty >= $allocatedQty && $allocatedQty > 0) bg-green-100 text-green-800 dark:bg-green-900/10 dark:text-green-200
                                    @elseif($scannedQty == 0)
                                        bg-gray-100 text-gray-500 dark:bg-gray-900/10 dark:text-gray-400
                                    @else
                                        bg-yellow-100 text-yellow-800 dark:bg-yellow-900/10 dark:text-yellow-300 @endif">
                                @if ($scannedQty >= $allocatedQty && $allocatedQty > 0)
                                    All scanned
                                @elseif($scannedQty == 0)
                                    Not Started
                                @else
                                    {{ $allocatedQty - $scannedQty }} left
                                @endif
                            </span>
                        @else
                            <span class="text-gray-400 text-xs">No items</span>
                        @endif
                    </td>

                    <!-- Status -->
                    <td class="px-4 py-2 text-sm capitalize text-gray-800 dark:text-gray-200">
                        {{ $record->status }}
                    </td>

                    <!-- Batches -->
                    <td class="px-4 py-2 text-sm text-gray-800 dark:text-gray-200">
                        @if ($record->batch_number)
                            {{ $record->batch_number }}
                        @else
                            <span class="text-gray-400">No batches</span>
                        @endif
                    </td>

                    <!-- Actions -->
                    <td class="px-4 py-2 text-sm">
                        <div class="flex items-center justify-center space-x-2"
                            style="min-width: 150px; min-height:42px;">
                            <button wire:click="editRecord({{ $record->id }})"
                                class="px-3 py-1 text-xs font-medium text-white rounded bg-blue-600 hover:bg-blue-700">
                                View
                            </button>
                            <button wire:click="removeBatch({{ $record->id }})"
                                class="px-3 py-1 text-xs font-medium text-white bg-red-600 rounded hover:bg-red-700">
                                Delete
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
</div>
