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

        <!-- Add Expense Slide-in Panel -->
        <div
            x-data="{ open: @entangle('showCreatePanel').live }"
            x-cloak
            x-on:keydown.escape.window="if (open) { open = false; $wire.closeCreatePanel(); }"
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
                                            Add New Expense
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            Record a new operational expense.
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
                                <form class="flex h-full flex-col">
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
                                                            <label for="expense_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                Expense Type
                                                            </label>
                                                            <select id="expense_type" class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm">
                                                                <option value="" disabled selected>Select Expense Type</option>
                                                                <option value="transport">Transport</option>
                                                                <option value="utilities">Utilities</option>
                                                                <option value="office_supplies">Office Supplies</option>
                                                                <option value="meals">Meals & Entertainment</option>
                                                                <option value="equipment">Equipment</option>
                                                                <option value="other">Other</option>
                                                            </select>
                                                        </div>

                                                        <div>
                                                            <label for="amount" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                Amount
                                                            </label>
                                                            <input type="number" id="amount" 
                                                                class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                                placeholder="0.00" step="0.01" />
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label for="expense_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                            Expense Date
                                                        </label>
                                                        <input type="date" id="expense_date"
                                                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm" />
                                                    </div>
                                                </div>
                                            </section>

                                            <!-- Payment Information -->
                                            <section class="space-y-4">
                                                <div>
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Payment Information</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Who was this expense paid to.</p>
                                                </div>

                                                <div>
                                                    <label for="paid_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                        Paid To
                                                    </label>
                                                    <input type="text" id="paid_to"
                                                        class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                        placeholder="Company or person name" />
                                                </div>
                                            </section>

                                            <!-- Description & Remarks -->
                                            <section class="space-y-4">
                                                <div>
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Description & Remarks</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Additional details about this expense.</p>
                                                </div>

                                                <div>
                                                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                        Description
                                                    </label>
                                                    <input type="text" id="description"
                                                        class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                        placeholder="Brief description of the expense" />
                                                </div>

                                                <div>
                                                    <label for="remarks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                        Remarks
                                                    </label>
                                                    <textarea id="remarks"
                                                        class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-green-500 focus:outline-none focus:ring-green-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm resize-y"
                                                        style="min-height: 80px;"
                                                        placeholder="Additional notes or details"></textarea>
                                                </div>
                                            </section>

                                            <!-- File Attachment (Optional) -->
                                            <section class="space-y-4">
                                                <div>
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Receipt Attachment</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Upload receipt or supporting document (optional).</p>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                        Upload File
                                                    </label>
                                                    <div class="flex items-center justify-center w-full">
                                                        <label for="file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:hover:bg-bray-800 dark:bg-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:hover:border-gray-500 dark:hover:bg-gray-600">
                                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                                <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                                                </svg>
                                                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                                                <p class="text-xs text-gray-500 dark:text-gray-400">PNG, JPG, PDF (MAX. 10MB)</p>
                                                            </div>
                                                            <input id="file" type="file" class="hidden" />
                                                        </label>
                                                    </div>
                                                </div>
                                            </section>
                                        </div>
                                    </div>

                                    <div class="border-t border-gray-200 bg-white px-6 py-4 dark:border-zinc-700 dark:bg-zinc-900">
                                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                Review details before recording the expense.
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <flux:button type="button" variant="ghost">
                                                    Reset
                                                </flux:button>

                                                <flux:button type="button" variant="primary">
                                                    Record Expense
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
                                    <dd class="text-lg font-medium text-gray-900 dark:text-white">₱45,230.00</dd>
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
                                    <dd class="text-lg font-medium text-gray-900 dark:text-white">₱12,450.00</dd>
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
                                    <dd class="text-lg font-medium text-gray-900 dark:text-white">₱3,200.00</dd>
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
                                    <dd class="text-lg font-medium text-gray-900 dark:text-white">8</dd>
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
                            <input type="text" class="block w-64 p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Search expenses...">
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="flex flex-wrap gap-3">
                        <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                            <option selected>All Categories</option>
                            <option>Transport</option>
                            <option>Utilities</option>
                            <option>Office Supplies</option>
                            <option>Meals</option>
                            <option>Equipment</option>
                        </select>

                        <input type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">

                        <input type="date" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">

                        <flux:button variant="outline" size="sm">
                            Apply Filters
                        </flux:button>
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
                                <th class="px-6 py-3">Expense Type</th>
                                <th class="px-6 py-3">Description</th>
                                <th class="px-6 py-3">Amount</th>
                                <th class="px-6 py-3">Paid To</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <!-- Sample Data -->
                            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col space-y-1 text-sm">
                                        <span class="font-medium text-gray-900 dark:text-white">Dec 15, 2023</span>
                                        <span class="text-gray-500 dark:text-gray-400">Friday</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                        Transport
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col space-y-1">
                                        <span class="font-medium text-gray-900 dark:text-white">Grab ride to client meeting</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Round trip to Makati</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-red-600 dark:text-red-400">₱450.00</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-gray-900 dark:text-white">Grab Philippines</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300">
                                        Paid
                                    </span>
                                </td>
                                <td class="px-6 py-4 space-x-2">
                                    <flux:button variant="outline" size="sm">View</flux:button>
                                    <flux:button variant="outline" size="sm">Edit</flux:button>
                                    <flux:button variant="outline" size="sm" class="text-red-600 hover:text-red-700">Delete</flux:button>
                                </td>
                            </tr>

                            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col space-y-1 text-sm">
                                        <span class="font-medium text-gray-900 dark:text-white">Dec 12, 2023</span>
                                        <span class="text-gray-500 dark:text-gray-400">Tuesday</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                        Utilities
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col space-y-1">
                                        <span class="font-medium text-gray-900 dark:text-white">Monthly internet bill</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">PLDT Fibr Plan</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-red-600 dark:text-red-400">₱2,899.00</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-gray-900 dark:text-white">PLDT Inc.</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300">
                                        Pending
                                    </span>
                                </td>
                                <td class="px-6 py-4 space-x-2">
                                    <flux:button variant="outline" size="sm">View</flux:button>
                                    <flux:button variant="outline" size="sm">Edit</flux:button>
                                    <flux:button variant="outline" size="sm" class="text-red-600 hover:text-red-700">Delete</flux:button>
                                </td>
                            </tr>

                            <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col space-y-1 text-sm">
                                        <span class="font-medium text-gray-900 dark:text-white">Dec 10, 2023</span>
                                        <span class="text-gray-500 dark:text-gray-400">Sunday</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300">
                                        Office Supplies
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col space-y-1">
                                        <span class="font-medium text-gray-900 dark:text-white">Printer ink cartridges</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">HP 63 Black & Color</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-red-600 dark:text-red-400">₱1,250.00</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-gray-900 dark:text-white">Office Warehouse</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300">
                                        Paid
                                    </span>
                                </td>
                                <td class="px-6 py-4 space-x-2">
                                    <flux:button variant="outline" size="sm">View</flux:button>
                                    <flux:button variant="outline" size="sm">Edit</flux:button>
                                    <flux:button variant="outline" size="sm" class="text-red-600 hover:text-red-700">Delete</flux:button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="py-4 px-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <label class="text-sm font-medium text-gray-900 dark:text-white">Per Page:</label>
                            <select class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-700 dark:text-gray-400">Showing 1 to 3 of 15 entries</span>
                            <flux:button variant="outline" size="sm">Previous</flux:button>
                            <flux:button variant="outline" size="sm">Next</flux:button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>