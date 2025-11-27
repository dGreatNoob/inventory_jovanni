<div class="pt-4">
    <div class="">
        <!-- Header Section -->
        <div class="mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Expenses Management</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Record and track operational expenses</p>
                </div>
                <div class="flex flex-row items-center space-x-3">
                    <flux:button 
                        wire:click="$set('showCreatePanel', true)"
                        variant="primary" 
                        class="flex items-center gap-2 whitespace-nowrap min-w-fit"
                        type="button"
                    >
                        <svg class="inline w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Add Expense</span>
                    </flux:button>
                </div>
            </div>
        </div>

        <!-- Flash Message -->
        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded dark:bg-green-900 dark:border-green-600 dark:text-green-300">
                {{ session('success') }}
            </div>
        @endif

        <!-- Add/Edit Expense Slide-in Panel -->
        <div
            x-data="{ open: @entangle('showCreatePanel').live }"
            x-cloak
            x-on:keydown.escape.window="if (open) { open = false; $wire.closeCreatePanel(); }"
            x-on:file-reset.window="if (window.filePreviewComponent) { window.filePreviewComponent.preview = null; }"
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
                        @click="open = false; $wire.closeCreatePanel()"
                    ></div>

                    <section
                        x-show="open"
                        x-transition:enter="transform transition ease-in-out duration-300"
                        x-transition:enter-start="translate-x-full"
                        x-transition:enter-end="translate-x-0"
                        x-transition:leave="transform transition ease-in-out duration-300"
                        x-transition:leave-start="translate-x-0"
                        x-transition:leave-end="translate-x-full"
                        class="relative ml-auto flex h-full w-full max-w-4xl"
                    >
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-green-500 dark:bg-green-400"></div>

                        <div class="ml-[0.25rem] flex h-full w-full flex-col bg-white shadow-xl dark:bg-zinc-900">
                            <header class="flex items-start justify-between border-b border-gray-200 px-6 py-5 dark:border-zinc-700">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-green-100 text-green-600 dark:bg-green-900/40 dark:text-green-300">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                            {{ $editingExpenseId ? 'Edit Expense' : 'Add New Expense' }}
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            {{ $editingExpenseId ? 'Update expense details.' : 'Record a new operational expense.' }}
                                        </p>
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    class="rounded-full p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-green-500 dark:text-gray-500 dark:hover:bg-zinc-800 dark:hover:text-gray-200"
                                    @click="open = false; $wire.closeCreatePanel()"
                                    aria-label="Close expense panel"
                                >
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </header>

                            <div class="flex-1 overflow-hidden">
                                <form wire:submit.prevent="{{ $editingExpenseId ? 'update' : 'save' }}" class="flex h-full flex-col">
                                    <div class="flex-1 overflow-y-auto px-6 py-6">
                                        <div class="space-y-8">
                                            <!-- Expense Details -->
                                            <section class="space-y-4">
                                                <div>
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Expense Details</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Basic information about the expense.</p>
                                                </div>

                                                <div class="space-y-4">
                                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                                        <div>
                                                            <label for="category" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                Expense Category
                                                            </label>
                                                            <select 
                                                                id="category" 
                                                                wire:model="category"
                                                                class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                            >
                                                                @if(empty($category))
                                                                    <option value="" disabled>Select Expense Category</option>
                                                                @endif
                                                                @foreach($this->categories as $key => $label)
                                                                    <option value="{{ $key }}">{{ $label }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('category') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                        </div>

                                                        <div>
                                                            <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                Amount
                                                            </label>
                                                            <input 
                                                                type="number" 
                                                                id="amount" 
                                                                wire:model="amount"
                                                                class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                                placeholder="0.00" 
                                                                step="0.01" 
                                                            />
                                                            @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>

                                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                                        <div>
                                                            <label for="date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                Expense Date
                                                            </label>
                                                            <input 
                                                                type="date" 
                                                                id="date"
                                                                wire:model="date"
                                                                class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm" 
                                                            />
                                                            @error('date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                        </div>

                                                        <div>
                                                            <label for="payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                Payment Method
                                                            </label>
                                                            <select 
                                                                id="payment_method" 
                                                                wire:model="payment_method"
                                                                class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                            >
                                                                @if(empty($payment_method))
                                                                    <option value="" disabled>Select Payment Method</option>
                                                                @endif
                                                                @foreach($this->paymentMethods as $key => $label)
                                                                    <option value="{{ $key }}">{{ $label }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error('payment_method') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>

                                            <!-- Payment Information -->
                                            <section class="space-y-4">
                                                <div>
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payment Information</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Who was this expense paid to.</p>
                                                </div>

                                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                                    <div>
                                                        <label for="party" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                            Paid To
                                                        </label>
                                                        <input 
                                                            type="text" 
                                                            id="party"
                                                            wire:model="party"
                                                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                            placeholder="Company or person name" 
                                                        />
                                                        @error('party') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div>
                                                        <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                            Status
                                                        </label>
                                                        <select 
                                                            id="status" 
                                                            wire:model="status"
                                                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                        >
                                                            <option value="pending">Pending</option>
                                                            <option value="paid">Paid</option>
                                                            <option value="cancelled">Cancelled</option>
                                                        </select>
                                                        @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                            </section>

                                            <!-- Description & Remarks -->
                                            <section class="space-y-4">
                                                <div>
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Description & Remarks</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Additional details about this expense.</p>
                                                </div>

                                                <div>
                                                    <label for="reference_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                        Reference ID
                                                    </label>
                                                    <input 
                                                        type="text" 
                                                        id="reference_id"
                                                        wire:model="reference_id"
                                                        class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                        readonly
                                                    />
                                                </div>

                                                <div>
                                                    <label for="remarks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                        Remarks
                                                    </label>
                                                    <textarea 
                                                        id="remarks"
                                                        wire:model="remarks"
                                                        class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm resize-y"
                                                        style="min-height: 80px;"
                                                        placeholder="Additional notes or details"
                                                    ></textarea>
                                                    @error('remarks') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                </div>

                                                <div 
                                                    x-data="{ 
                                                        preview: null,
                                                        isImage(file) {
                                                            return file && file.type && file.type.startsWith('image/');
                                                        },
                                                        handleFileChange(event) {
                                                            const file = event.target.files[0];
                                                            if (file && this.isImage(file)) {
                                                                const reader = new FileReader();
                                                                reader.onload = (e) => {
                                                                    this.preview = e.target.result;
                                                                };
                                                                reader.readAsDataURL(file);
                                                            } else {
                                                                this.preview = null;
                                                            }
                                                        },
                                                        init() {
                                                            window.filePreviewComponent = this;
                                                            // Clear preview when Livewire resets the file
                                                            this.$watch('$wire.file', (value) => {
                                                                if (!value) {
                                                                    this.preview = null;
                                                                }
                                                            });
                                                        }
                                                    }"
                                                >
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Receipt Attachment</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Upload receipt or supporting document (optional).</p>
                                                    
                                                    <div class="flex items-center justify-center w-full">
                                                        <label for="file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 transition-colors">
                                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                                <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                                                </svg>
                                                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400">
                                                                    <span class="font-semibold">Click to upload</span> or drag and drop
                                                                </p>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                                    PNG, JPG, PDF (MAX. 10MB)
                                                                </p>
                                                            </div>
                                                            <input 
                                                                id="file" 
                                                                type="file" 
                                                                class="hidden" 
                                                                wire:model="file"
                                                                accept="image/*,.pdf"
                                                                x-on:change="handleFileChange($event)"
                                                            />
                                                        </label>
                                                    </div>
                                                    
                                                    <!-- Image Preview -->
                                                    <div x-show="preview" x-transition class="mt-4">
                                                        <div class="relative bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4 shadow-sm">
                                                            <div class="flex items-center justify-between mb-3">
                                                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Image Preview</h4>
                                                                <button 
                                                                    type="button" 
                                                                    x-on:click="preview = null; $wire.set('file', null)"
                                                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors"
                                                                >
                                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                            <div class="flex justify-center">
                                                                <img 
                                                                    :src="preview" 
                                                                    alt="Receipt preview" 
                                                                    class="max-w-full max-h-64 rounded-lg shadow-md object-contain"
                                                                />
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- File preview and remove button (for non-image files) -->
                                                    @if ($file)
                                                        @php
                                                            $isImageFile = $file && in_array(strtolower($file->getClientOriginalExtension()), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                                        @endphp
                                                        @if (!$isImageFile)
                                                            <div class="mt-3 flex items-center justify-between p-3 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                                                <div class="flex items-center space-x-3">
                                                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                                    </svg>
                                                                    <span class="text-sm text-green-800 dark:text-green-300">{{ $file->getClientOriginalName() }}</span>
                                                                </div>
                                                                <button 
                                                                    type="button" 
                                                                    wire:click="$set('file', null)"
                                                                    x-on:click="preview = null"
                                                                    class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300"
                                                                >
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        @endif
                                                    @endif
                                                    
                                                    @error('file') 
                                                        <span class="text-red-500 text-xs mt-1">{{ $message }}</span> 
                                                    @enderror
                                                </div>
                                                
                                            </section>
                                        </div>
                                    </div>

                                    <div class="border-t border-gray-200 bg-white px-6 py-4 dark:border-zinc-700 dark:bg-zinc-900">
                                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                Review details before {{ $editingExpenseId ? 'updating' : 'recording' }} the expense.
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <flux:button 
                                                    type="button" 
                                                    variant="ghost"
                                                    wire:click="resetForm"
                                                >
                                                    Reset
                                                </flux:button>

                                                <flux:button 
                                                    type="submit" 
                                                    variant="primary"
                                                >
                                                    {{ $editingExpenseId ? 'Update Expense' : 'Record Expense' }}
                                                </flux:button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>
                </div>
            </template>
        </div>

        <!-- Delete Confirmation Modal -->
        <div
            x-data="{ open: @entangle('showDeleteModal').live }"
            x-cloak
        >
            <template x-teleport="body">
                <div
                    x-show="open"
                    x-transition.opacity
                    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50"
                >
                    <div
                        x-show="open"
                        x-transition
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6"
                    >
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Confirm Deletion</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">Are you sure you want to delete this expense? This action cannot be undone.</p>
                        <div class="flex justify-end space-x-3">
                            <flux:button 
                                variant="ghost" 
                                wire:click="closeDeleteModal"
                            >
                                Cancel
                            </flux:button>
                            <flux:button 
                                variant="danger" 
                                wire:click="delete"
                            >
                                Delete Expense
                            </flux:button>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Stats Cards -->
        <section class="mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Expenses -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Expenses</dt>
                                    <dd class="text-lg font-medium text-gray-900 dark:text-white">₱{{ number_format($stats['total'], 2) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- This Month -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">This Month</dt>
                                    <dd class="text-lg font-medium text-gray-900 dark:text-white">₱{{ number_format($stats['this_month'], 2) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Pending</dt>
                                    <dd class="text-lg font-medium text-gray-900 dark:text-white">₱{{ number_format($stats['pending'], 2) }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Categories -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Categories</dt>
                                    <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $stats['categories'] }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Filters and Search -->
        <section class="mb-6">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between p-4 gap-4">
                    <!-- Search -->
                    <div class="flex space-x-6">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <input 
                                type="text" 
                                wire:model.live="search"
                                class="block w-64 p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                                placeholder="Search expenses..."
                            >
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="flex flex-wrap gap-3">
                        <select 
                            wire:model.live="categoryFilter"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        >
                            <option value="">All Categories</option>
                            @foreach($this->categories as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>

                        <input 
                            type="date" 
                            wire:model.live="startDate"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        >

                        <input 
                            type="date" 
                            wire:model.live="endDate"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        >

                        <button 
                            type="button"
                            wire:click="resetFilters" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 px-2.5 py-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors flex items-center gap-2"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Reset Filters
                        </button>
                    </div>
                </div>
            </div>
        </section>

        <!-- Data Table -->
        <section class="mb-6">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Date</th>
                                <th class="px-6 py-3">Reference ID</th>
                                <th class="px-6 py-3">Category</th>
                                <th class="px-6 py-3">Description</th>
                                <th class="px-6 py-3">Amount</th>
                                <th class="px-6 py-3">Paid To</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($expenses as $expense)
                                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col space-y-1 text-sm">
                                            <span class="font-medium text-gray-900 dark:text-white">
                                                {{ \Carbon\Carbon::parse($expense->date)->format('M j, Y') }}
                                            </span>
                                            <span class="text-gray-500 dark:text-gray-400">
                                                {{ \Carbon\Carbon::parse($expense->date)->format('l') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-mono text-xs text-gray-500 dark:text-gray-400">
                                            {{ $expense->reference_id }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $badgeColors = $this->categoryBadgeColors;
                                            $color = $badgeColors[$expense->category] ?? $badgeColors['other'];
                                            $categoryLabel = $this->categories[$expense->category] ?? ucfirst(str_replace('_', ' ', $expense->category));
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $color }}">
                                            {{ $categoryLabel }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col space-y-1">
                                            <span class="font-medium text-gray-900 dark:text-white">
                                                {{ ($this->paymentMethods[$expense->payment_method] ?? ucwords($expense->payment_method)) }} Payment
                                            </span>
                                            @if($expense->remarks)
                                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($expense->remarks, 50) }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-semibold text-red-600 dark:text-red-400">₱{{ number_format($expense->amount, 2) }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-gray-900 dark:text-white">{{ $expense->party }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusColors = [
                                                'paid' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300',
                                                'pending' => 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300',
                                                'cancelled' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
                                            ];
                                            $statusColor = $statusColors[$expense->status] ?? $statusColors['pending'];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColor }}">
                                            {{ ucfirst($expense->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center space-x-2">
                                            <!-- View Receipt Button (always show) -->
                                            <flux:button 
                                                type="button"
                                                wire:click="viewReceipt({{ $expense->id }})" 
                                                variant="outline" 
                                                size="sm"
                                                class="text-blue-600 hover:text-blue-700"
                                            >
                                                Receipt
                                            </flux:button>

                                            <!-- Edit button -->
                                            <flux:button 
                                                type="button"
                                                wire:click="edit({{ $expense->id }})" 
                                                variant="outline" 
                                                size="sm"
                                            >
                                                Edit
                                            </flux:button>

                                            <!-- Delete button -->
                                            <flux:button 
                                                type="button"
                                                wire:click="confirmDelete({{ $expense->id }})" 
                                                variant="outline" 
                                                size="sm" 
                                                class="text-red-600 hover:text-red-700"
                                            >
                                                Delete
                                            </flux:button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        No expenses found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="py-4 px-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <label class="text-sm font-medium text-gray-900 dark:text-white">Per Page:</label>
                            <select 
                                wire:model.live="perPage"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            >
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-700 dark:text-gray-400">
                                Showing {{ $expenses->firstItem() }} to {{ $expenses->lastItem() }} of {{ $expenses->total() }} entries
                            </span>
                            {{ $expenses->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Receipt View Modal -->
        <div 
            x-data="{ open: @entangle('showReceiptModal').live }" 
            x-cloak
            x-on:keydown.escape.window="if (open) { open = false; $wire.closeReceiptModal(); }"
        >
            <template x-teleport="body">
                <div 
                    x-show="open" 
                    x-transition.opacity 
                    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-neutral-900/30 dark:bg-neutral-900/50"
                    @click.self="open = false; $wire.closeReceiptModal()"
                >
                    <div 
                        x-show="open" 
                        x-transition 
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-4xl w-full max-h-[90vh] overflow-hidden mx-4 sm:mx-0"
                        @click.stop
                    >
                        <!-- Modal Header -->
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Receipt - {{ $currentReceipt['expense']['reference_id'] ?? '' }}
                                </h3>
                            </div>
                            <div class="flex items-center space-x-2 sm:space-x-3">
                                @if($currentReceipt)
                                    <button type="button" wire:click="downloadReceipt({{ $currentReceipt['expense']['id'] ?? '' }})"
                                            class="flex items-center space-x-1 sm:space-x-2 px-2 sm:px-3 py-2 text-xs sm:text-sm text-blue-600 bg-blue-50 hover:bg-blue-100 rounded-lg dark:bg-blue-900/20 dark:text-blue-400 dark:hover:bg-blue-900/30 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <span class="hidden sm:inline">Download</span>
                                    </button>
                                @endif
                                <button type="button" x-on:click="open = false; $wire.closeReceiptModal()"
                                        class="rounded-full p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700 dark:hover:text-gray-300 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Modal Content -->
                        <div class="p-4 sm:p-6 lg:p-8 max-h-[calc(90vh-120px)] overflow-y-auto">
                            @if($currentReceipt)
                                @php
                                    $hasFile = !empty($currentReceipt['file_extension']) && !empty($currentReceipt['file_url']);
                                    $extension = $hasFile ? strtolower($currentReceipt['file_extension']) : null;
                                    $isImage = $hasFile && in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                    $isPdf = $hasFile && $extension === 'pdf';
                                    
                                    // Improved Status colors
                                    $statusColors = [
                                        'paid' => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800',
                                        'pending' => 'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300 border-amber-200 dark:border-amber-800', 
                                        'cancelled' => 'bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300 border-rose-200 dark:border-rose-800',
                                        'default' => 'bg-slate-50 text-slate-700 dark:bg-slate-900/30 dark:text-slate-300 border-slate-200 dark:border-slate-800'
                                    ];
                                    $statusClass = $statusColors[$currentReceipt['expense']['status']] ?? $statusColors['default'];
                                @endphp

                                <!-- Header -->
                                <div class="bg-slate-700 dark:bg-slate-800 rounded-2xl p-4 sm:p-6 mb-6 sm:mb-8 text-white shadow-xl">
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                        <div class="flex items-center space-x-3 sm:space-x-4">
                                            <div class="bg-white/15 p-2 sm:p-3 rounded-xl">
                                                <svg class="w-5 h-5 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h2 class="text-xl sm:text-2xl font-bold">Receipt Preview</h2>
                                                <p class="text-slate-300 text-xs sm:text-sm mt-1 break-words">
                                                    @if($hasFile && $currentReceipt['file_name'])
                                                        {{ $currentReceipt['file_name'] }}
                                                    @else
                                                        No file attached
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap items-center gap-2 sm:gap-3 w-full sm:w-auto">
                                            @if($hasFile && $extension)
                                                <span class="bg-white/20 px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm font-medium">
                                                    {{ strtoupper($extension) }} File
                                                </span>
                                            @endif
                                            <span class="{{ $statusClass }} border px-3 sm:px-4 py-1.5 sm:py-2 rounded-lg text-xs sm:text-sm font-medium">
                                                {{ ucfirst($currentReceipt['expense']['status']) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 xl:grid-cols-3 gap-4 sm:gap-6 lg:gap-8">
                                    <!-- File Preview Section -->
                                    <div class="xl:col-span-2">
                                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                                            <div class="p-4 sm:p-6 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                                                <h3 class="text-lg font-semibold text-slate-800 dark:text-white flex items-center gap-2">
                                                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                    </svg>
                                                    Document Preview
                                                </h3>
                                            </div>
                                            
                                            <div class="p-4 sm:p-6">
                                                @if(!$hasFile)
                                                    <!-- No File Attached -->
                                                    <div class="text-center py-12">
                                                        <div class="bg-slate-100 dark:bg-slate-800/50 w-24 h-24 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                                                            <svg class="w-12 h-12 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                            </svg>
                                                        </div>
                                                        <h4 class="text-xl font-bold text-slate-800 dark:text-white mb-3">
                                                            No Receipt File Attached
                                                        </h4>
                                                        <p class="text-slate-600 dark:text-slate-400 mb-8 max-w-md mx-auto leading-relaxed">
                                                            This expense does not have a receipt file attached. You can edit the expense to upload a receipt.
                                                        </p>
                                                        <button type="button" wire:click="edit({{ $currentReceipt['expense']['id'] }})"
                                                                class="inline-flex items-center gap-3 px-8 py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                                                                @click="open = false; $wire.closeReceiptModal()">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                            </svg>
                                                            Edit Expense to Upload Receipt
                                                        </button>
                                                    </div>
                                                @elseif($isImage)
                                                    <!-- Enhanced Image Preview -->
                                                    <div class="flex justify-center items-center">
                                                        <div class="rounded-2xl p-2 sm:p-4 lg:p-6 w-full">
                                                            <div class="flex justify-center items-center min-h-[200px] sm:min-h-[300px] lg:min-h-[400px]">
                                                                <img src="{{ $currentReceipt['file_url'] }}" 
                                                                    alt="Receipt for {{ $currentReceipt['expense']['reference_id'] }}"
                                                                    class="max-w-full max-h-[50vh] sm:max-h-[60vh] lg:max-h-[70vh] rounded-xl shadow-2xl object-contain"
                                                                    onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'400\' height=\'300\'%3E%3Crect fill=\'%23f3f4f6\' width=\'400\' height=\'300\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%239ca3af\' font-family=\'sans-serif\' font-size=\'18\'%3EImage not found%3C/text%3E%3C/svg%3E';"
                                                                    loading="lazy">
                                                            </div>
                                                        </div>
                                                    </div>
                                                @elseif($isPdf)
                                                    <!-- PDF Preview -->
                                                    <div class="space-y-6">
                                                        <div class="bg-slate-800 rounded-xl p-8 text-center border-2 border-slate-700">
                                                            <div class="w-16 h-16 bg-red-500 rounded-xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                                                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                                </svg>
                                                            </div>
                                                            <h4 class="text-white font-semibold text-lg mb-2">PDF Document Ready</h4>
                                                            <p class="text-slate-300 text-sm">Click below to view the full document</p>
                                                        </div>
                                                        
                                                        <div class="flex gap-3 justify-center">
                                                            <a href="{{ $currentReceipt['file_url'] }}" 
                                                            target="_blank" 
                                                            class="flex items-center gap-3 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                                </svg>
                                                                Open in New Tab
                                                            </a>
                                                            <button type="button" wire:click="downloadReceipt({{ $currentReceipt['expense']['id'] }})" class="flex items-center gap-3 px-6 py-3 bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-800 dark:text-slate-200 rounded-xl font-medium transition-all duration-300">
                                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                                </svg>
                                                                Download
                                                            </button>
                                                        </div>
                                                    </div>
                                                @else
                                                    <!-- Unsupported File -->
                                                    <div class="text-center py-12">
                                                        <div class="bg-slate-100 dark:bg-slate-800/50 w-24 h-24 rounded-2xl flex items-center justify-center mx-auto mb-6 shadow-lg">
                                                            <svg class="w-12 h-12 text-slate-500 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                            </svg>
                                                        </div>
                                                        <h4 class="text-xl font-bold text-slate-800 dark:text-white mb-3">
                                                            {{ $currentReceipt['file_name'] }}
                                                        </h4>
                                                        <p class="text-slate-600 dark:text-slate-400 mb-8 max-w-md mx-auto leading-relaxed">
                                                            This file format cannot be previewed in the browser. Download the file to access its contents.
                                                        </p>
                                                        <button type="button" wire:click="downloadReceipt({{ $currentReceipt['expense']['id'] }})"
                                                                class="inline-flex items-center gap-3 px-8 py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                            </svg>
                                                            Download File
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Sidebar -->
                                    <div class="space-y-4 sm:space-y-6">
                                        <!-- Amount Card -->
                                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                                            <div class="p-4 sm:p-6 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                                                <h3 class="text-lg font-semibold text-slate-800 dark:text-white flex items-center gap-2">
                                                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    Amount
                                                </h3>
                                            </div>
                                            
                                            <div class="p-4 sm:p-6">
                                                <div class="space-y-1">
                                                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Total Amount</label>
                                                    <p class="text-2xl sm:text-3xl font-bold text-red-600 dark:text-red-400">₱{{ number_format($currentReceipt['expense']['amount'], 2) }}</p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Expense Details -->
                                        <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                                            <div class="p-4 sm:p-6 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                                                <h3 class="text-lg font-semibold text-slate-800 dark:text-white flex items-center gap-2">
                                                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                    </svg>
                                                    Expense Details
                                                </h3>
                                            </div>
                                            
                                            <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
                                                <div class="space-y-1">
                                                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Reference ID</label>
                                                    <p class="text-lg font-semibold text-slate-800 dark:text-white font-mono">{{ $currentReceipt['expense']['reference_id'] }}</p>
                                                </div>
                                                
                                                <div class="space-y-1">
                                                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Paid To</label>
                                                    <p class="text-lg font-semibold text-slate-800 dark:text-white">{{ $currentReceipt['expense']['party'] }}</p>
                                                </div>
                                                
                                                <div class="space-y-1">
                                                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Category</label>
                                                    <div class="flex items-center gap-2">
                                                        @php
                                                            $badgeColors = $this->categoryBadgeColors;
                                                            $categoryColor = $badgeColors[$currentReceipt['expense']['category']] ?? $badgeColors['other'];
                                                            $categoryLabel = $this->categories[$currentReceipt['expense']['category']] ?? ucfirst(str_replace('_', ' ', $currentReceipt['expense']['category']));
                                                        @endphp
                                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-medium {{ $categoryColor }}">
                                                            {{ $categoryLabel }}
                                                        </span>
                                                    </div>
                                                </div>
                                                
                                                <div class="space-y-1">
                                                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide">Transaction Date</label>
                                                    <div class="flex items-center gap-2 text-slate-800 dark:text-slate-200">
                                                        <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                        </svg>
                                                        <span class="font-medium">{{ \Carbon\Carbon::parse($currentReceipt['expense']['date'])->format('F j, Y') }}</span>
                                                    </div>
                                                </div>
                                                
                                                <div class="pt-4 border-t border-slate-200 dark:border-slate-700">
                                                    <label class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2 block">Status</label>
                                                    <div class="{{ $statusClass }} border px-4 py-3 rounded-xl font-semibold text-center">
                                                        {{ ucfirst($currentReceipt['expense']['status']) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <!-- Empty State -->
                                <div class="text-center py-20">
                                    <div class="bg-slate-100 dark:bg-slate-800/50 w-32 h-32 rounded-3xl flex items-center justify-center mx-auto mb-8 shadow-2xl">
                                        <svg class="w-16 h-16 text-slate-400 dark:text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                    <h3 class="text-2xl font-bold text-slate-800 dark:text-white mb-4">No Receipt Available</h3>
                                    <p class="text-slate-600 dark:text-slate-400 max-w-md mx-auto text-lg mb-8 leading-relaxed">
                                        There is no receipt information to display at this time.
                                    </p>
                                    <button class="inline-flex items-center gap-3 px-8 py-3.5 bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white rounded-xl font-semibold transition-all duration-300 shadow-lg hover:shadow-xl">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Upload New Receipt
                                    </button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>
</div>