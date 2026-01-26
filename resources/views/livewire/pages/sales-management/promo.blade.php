<div class="pt-4">
    <div class="">
        <!-- Header Section -->
        <div class="mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Promo Creation</h1>
                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Create and manage promotions</p>
                </div>
                <div class="flex flex-row items-center space-x-3">
                    <flux:button 
                        wire:click="$set('showCreatePanel', true)"
                        variant="primary" 
                        class="flex items-center gap-2 whitespace-nowrap min-w-fit"
                    >
                        <svg class="inline w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <span>Create Promo</span>
                    </flux:button>
                </div>
            </div>
        </div>

        <section class="mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Total Promo -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-indigo-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M12 6a3.5 3.5 0 1 0 0 7 3.5 3.5 0 0 0 0-7Zm-1.5 8a4 4 0 0 0-4 4 2 2 0 0 0 2 2h7a2 2 0 0 0 2-2 4 4 0 0 0-4-4h-3Zm6.82-3.096a5.51 5.51 0 0 0-2.797-6.293 3.5 3.5 0 1 1 2.796 6.292ZM19.5 18h.5a2 2 0 0 0 2-2 4 4 0 0 0-4-4h-1.1a5.503 5.503 0 0 1-.471.762A5.998 5.998 0 0 1 19.5 18ZM4 7.5a3.5 3.5 0 0 1 5.477-2.889 5.5 5.5 0 0 0-2.796 6.293A3.501 3.501 0 0 1 4 7.5ZM7.1 12H6a4 4 0 0 0-4 4 2 2 0 0 0 2 2h.5a5.998 5.998 0 0 1 3.071-5.238A5.505 5.505 0 0 1 7.1 12Z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Promos</dt>
                                    <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                        {{ $totalPromos }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Promo -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Active Promos</dt>
                                    <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                        {{ $activePromos }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Promo -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="h-6 w-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Upcoming Promos</dt>
                                    <dd class="text-lg font-medium text-gray-900 dark:text-white">
                                        {{ $upcomingPromos }}
                                    </dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Create Promo Slide-in Panel -->
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
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-500 dark:bg-indigo-400"></div>

                        <div class="ml-[0.25rem] flex h-full w-full flex-col bg-white shadow-xl dark:bg-zinc-900">
                            <header class="flex items-start justify-between border-b border-gray-200 px-6 py-5 dark:border-zinc-700">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-300">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                            Create New Promo
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            Fill in the promo information to create a new promotion.
                                        </p>
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    class="rounded-full p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:text-gray-500 dark:hover:bg-zinc-800 dark:hover:text-gray-200"
                                    @click="open = false; $wire.closeCreatePanel()"
                                    aria-label="Close promo panel"
                                >
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </header>

                            <div class="flex-1 overflow-hidden">
                                <form wire:submit.prevent="submit" class="flex h-full flex-col">
                                    <div class="flex-1 overflow-y-auto px-6 py-6">
                                        <div class="space-y-8">
                                            <!-- Promo Details -->
                                            <section class="space-y-4">
                                                <div>
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Promo Details</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Basic information about the promotion.</p>
                                                </div>

                                                <div class="space-y-4">
                                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                                        <div>
                                                            <label for="promo_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                Promo Name
                                                            </label>
                                                            <input type="text" id="promo_name" wire:model="promo_name"
                                                                class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                                placeholder="Autumn Sale" required />
                                                            @error('promo_name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                                        </div>

                                                        <div>
                                                            <label for="promo_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                Promo Code
                                                            </label>
                                                            <input type="text" id="promo_code" wire:model="promo_code"
                                                                class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                                placeholder="PRM-001" required />
                                                            @error('promo_code') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label for="promo_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                            Promo Type
                                                        </label>
                                                        <select id="promo_type" wire:model="promo_type"
                                                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                            required>
                                                            <option value="" disabled selected>Select Promo Type</option>
                                                            @foreach($promo_type_options as $type)
                                                                <option value="{{ $type }}">{{ $type }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('promo_type')
                                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </section>

                                            <!-- Date Range -->
                                            <section class="space-y-4">
                                                <div>
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Date Range</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Set the active period for the promotion.</p>
                                                </div>

                                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                            Start Date
                                                        </label>
                                                        <input type="date" wire:model="startDate"
                                                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm" />
                                                        @error('startDate') <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                                                        <input type="date" wire:model="endDate"
                                                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm" />
                                                        @error('endDate') <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                            </section>

                                            <!-- Branch & Product Selection -->
                                            <section class="space-y-4">
                                                <div>
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Batch & Product Selection</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Choose where and what products are included in the promo.</p>
                                                </div>

                                                <!-- Batch Allocation Selector -->
                                                <div>
    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
        Batch
    </label>
    <div class="relative" wire:click.outside="$set('batchDropdown', false)">
        <div wire:click="$toggle('batchDropdown')" 
            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm cursor-pointer flex justify-between items-center">
            <span class="flex flex-wrap gap-1">
                @if(empty($selected_batches))
                    <span class="text-gray-400">Select Batch</span>
                @else
                    @foreach($batchAllocations as $batchAllocation)
                        @if(in_array($batchAllocation->id, $selected_batches))
                            <span class="inline-flex items-center gap-1.5 bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-1 rounded-full dark:bg-indigo-900/30 dark:text-indigo-300">
                                <span class="font-semibold">{{ $batchAllocation->ref_no }}</span>
                                @if($batchAllocation->batch_number)
                                    <span class="text-gray-500 dark:text-gray-400">•</span>
                                    <span class="text-gray-700 dark:text-gray-300 font-normal">Batch: {{ $batchAllocation->batch_number }}</span>
                                @endif
                            </span>
                        @endif
                    @endforeach
                @endif
            </span>
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>

        @if($batchDropdown)
        <div class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-auto dark:bg-gray-700 dark:border-gray-600">
            @foreach($batchAllocations as $batchAllocation)
                <label class="flex items-center p-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 border-b border-gray-100 dark:border-gray-600 last:border-b-0">
                    <input type="checkbox" value="{{ $batchAllocation->id }}" wire:model="selected_batches"
                        class="form-checkbox h-4 w-4 accent-blue-600 text-blue-600 focus:ring-blue-500 border-gray-300 rounded dark:accent-blue-400 dark:text-blue-400">
                    <div class="ml-3 flex-1">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $batchAllocation->ref_no }}
                        </div>
                        @if($batchAllocation->batch_number)
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                Batch Number: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $batchAllocation->batch_number }}</span>
                            </div>
                        @endif
                    </div>
                </label>
            @endforeach
        </div>
        @endif
        @error('selected_batches')
            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
        @enderror
    </div>
</div>

                                                <!-- Product Selection -->
                                                <div class="grid gap-4">
                                                    <!-- First Product Dropdown -->
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Product</label>
                                                        <div class="relative" wire:click.outside="$set('productDropdown', false)">
                                                            <div wire:click="$toggle('productDropdown')"
                                                                class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm cursor-pointer flex justify-between items-center">
                                                                <div class="flex flex-wrap gap-1 items-center">
                                                                    @if(!$productDropdown)
                                                                        @if(empty($selected_products))
                                                                            <span class="text-gray-400">
                                                                                @if(empty($selected_batches))
                                                                                    Select batch allocation first
                                                                                @else
                                                                                    Select Product
                                                                                @endif
                                                                            </span>
                                                                        @else
                                                                            @foreach($this->availableProductsForBatches as $product)
                                                                                @if(in_array($product->id, $selected_products))
                                                                                    <span class="inline-flex items-center bg-gray-600 text-white text-xs font-medium px-2 py-0.5 rounded-full">
                                                                                        {{ $product->name }}
                                                                                        <span class="ml-1 text-gray-200 text-[11px]">₱{{ number_format($product->price, 0) }}</span>
                                                                                    </span>
                                                                                @endif
                                                                            @endforeach
                                                                        @endif
                                                                    @else
                                                                        <span class="text-gray-400">
                                                                            @if(empty($selected_batches))
                                                                                Select batch allocation first
                                                                            @else
                                                                                Select Product
                                                                            @endif
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                                </svg>
                                                            </div>

                                                            @if($productDropdown && !empty($selected_batches))
                                                                <div class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-auto dark:bg-gray-700 dark:border-gray-600">
                                                                    @if($this->availableProductsForBatches->count() > 0)
                                                                        @foreach($this->availableProductsForBatches as $product)
                                                                            <label class="flex items-center justify-between p-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 border-b border-gray-100 dark:border-gray-600 last:border-b-0 {{ $product->isDisabled ?? false ? 'opacity-50 cursor-not-allowed' : '' }}">
                                                                                <div class="flex items-center space-x-3">
                                                                                    <input type="checkbox"
                                                                                        value="{{ $product->id }}"
                                                                                        wire:model="selected_products"
                                                                                        @if($product->isDisabled ?? false) disabled @endif
                                                                                        onclick="event.stopPropagation()"
                                                                                        class="form-checkbox h-4 w-4 accent-green-600 text-green-600 focus:ring-green-500 border-gray-300 rounded dark:accent-green-400 dark:text-green-400 {{ $product->isDisabled ?? false ? 'cursor-not-allowed' : '' }}">
                                                                                    <span class="text-sm text-gray-900 dark:text-white {{ $product->isDisabled ?? false ? 'line-through' : '' }}">
                                                                                        {{ $product->name }}
                                                                                        @if($product->isDisabled ?? false)
                                                                                            <span class="ml-2 text-xs text-red-500">(Already in promo)</span>
                                                                                        @endif
                                                                                    </span>
                                                                                </div>
                                                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-500 text-white dark:bg-emerald-400 dark:text-gray-900 shadow-sm">
                                                                                    ₱ {{ number_format($product->price, 0) }}
                                                                                </span>
                                                                            </label>
                                                                        @endforeach
                                                                    @else
                                                                        <div class="p-4 text-center text-gray-500 dark:text-gray-400 text-sm">
                                                                            No products available in selected batch allocations
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endif

                                                            @error('selected_products')
                                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>

                                            <!-- Description -->
                                            <section class="space-y-4">
                                                <div>
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Description</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Additional details about the promotion.</p>
                                                </div>

                                                <div>
                                                    <label for="promo_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                        Description
                                                    </label>
                                                    <textarea id="promo_description" wire:model="promo_description"
                                                        class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm resize-y"
                                                        style="min-height: 80px;"
                                                        placeholder="Promo for Halloween"></textarea>
                                                    @error('promo_description')
                                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </section>
                                        </div>
                                    </div>

                                    <div class="border-t border-gray-200 bg-white px-6 py-4 dark:border-zinc-700 dark:bg-zinc-900">
                                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                Review details carefully before creating a new promo.
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <flux:button type="button" wire:click="resetForm" variant="ghost">
                                                    Reset
                                                </flux:button>

                                                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                                                    <span wire:loading.remove wire:target="submit">Create Promo</span>
                                                    <span wire:loading wire:target="submit">Creating...</span>
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
                                placeholder="Search promos..."
                            >
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="flex flex-wrap gap-3">
                        <select 
                            wire:model.live="typeFilter"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        >
                            <option value="">All Types</option>
                            @foreach($promo_type_options as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>

                        <select 
                            wire:model.live="statusFilter"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        >
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="upcoming">Upcoming</option>
                            <option value="expired">Expired</option>
                        </select>

                        <input 
                            type="date" 
                            wire:model.live="filterStartDate"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Start Date"
                        >

                        <input 
                            type="date" 
                            wire:model.live="filterEndDate"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="End Date"
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
                <!-- Data Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Promo</th>
                                <th class="px-6 py-3">Type</th>
                                <th class="px-6 py-3">Batch Allocation</th>
                                <th class="px-6 py-3">Date Range</th>
                                <th class="px-6 py-3">Status</th>
                                <th class="px-6 py-3">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($items as $item)
                                <tr wire:key="promo-{{ $item->id }}" 
                                    class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                    
                                    <!-- Promo -->
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col space-y-1">
                                            <div class="inline-flex items-center space-x-2 text-sm">
                                                <span class="font-medium text-gray-900 dark:text-white">{{ $item->name }}</span>
                                                <span class="text-gray-400 dark:text-gray-500">|</span>
                                                <span class="text-gray-500 dark:text-gray-300">{{ $item->code ?? '-' }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Type -->
                                    <td class="px-6 py-4">{{ $item->type ?? '-' }}</td>

                                    <!-- Batch Allocation -->
                                    <td class="px-6 py-4">
                                        @if(!empty($item->branch_names))
                                            @foreach($item->branch_names as $refNo)
                                                <span class="inline-block mr-1 bg-blue-500 text-white px-2 py-0.5 rounded text-xs">
                                                    {{ $refNo }}
                                                </span>
                                            @endforeach
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <!-- Date Range -->
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col space-y-1 text-sm text-gray-500 dark:text-gray-300">
                                            <span>Starts: {{ $item->startDate ? $item->startDate->format('M d, Y') : '-' }}</span>
                                            <span>Ends: {{ $item->endDate ? $item->endDate->format('M d, Y') : '-' }}</span>
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-4">
                                        @php
                                            $now = \Carbon\Carbon::now();
                                            $startDate = $item->startDate; // already cast to Carbon
                                            $endDate = $item->endDate;     // already cast to Carbon
                                        @endphp

                                        @if($now->between($startDate, $endDate))
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300">
                                                Active
                                            </span>
                                        @elseif($now->lt($startDate))
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-300">
                                                Upcoming
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                                Expired
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Action -->
                                    <td class="px-6 py-4 space-x-2">
                                        <flux:button 
                                        onclick="window.location='{{ route('promo.view', ['id' => $item->id]) }}'" 
                                        variant="outline" 
                                        size="sm"
                                        type="button">
                                        View
                                    </flux:button>
                                        <flux:button wire:click.prevent="edit({{ $item->id }})" variant="outline" size="sm">Edit</flux:button>
                                        <flux:button wire:click.prevent="confirmDelete({{ $item->id }})" variant="outline" size="sm" class="text-red-600 hover:text-red-700">Delete</flux:button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No promos found.</td>
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
                            <select wire:model.live="perPage"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>
                        <div>
                            {{ $items->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </section>

            @if($showDeleteModal)
                <div class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center">
                    <div class="relative w-full max-w-md max-h-full">
                        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                            <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" wire:click="cancelDelete">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                            <div class="p-6 text-center">
                                <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                </svg>
                                <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete this promo?</h3>
                                <flux:button wire:click="delete" class="mr-2 bg-red-600 hover:bg-red-700 text-white">
                                    Yes, I'm sure
                                </flux:button>
                                <flux:button wire:click="cancelDelete" variant="outline">
                                    No, cancel
                                </flux:button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        <!-- Edit Promo Slide-in Panel -->
        <div
            x-data="{ open: @entangle('showEditModal').live }"
            x-cloak
            x-on:keydown.escape.window="if (open) { open = false; $wire.cancelEdit(); }"
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
                        @click="open = false; $wire.cancelEdit()"
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
                        <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-500 dark:bg-indigo-400"></div>

                        <div class="ml-[0.25rem] flex h-full w-full flex-col bg-white shadow-xl dark:bg-zinc-900">
                            <header class="flex items-start justify-between border-b border-gray-200 px-6 py-5 dark:border-zinc-700">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-300">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                                            Edit Promo
                                        </h2>
                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                            Update the promotion information.
                                        </p>
                                    </div>
                                </div>

                                <button
                                    type="button"
                                    class="rounded-full p-2 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:text-gray-500 dark:hover:bg-zinc-800 dark:hover:text-gray-200"
                                    @click="open = false; $wire.cancelEdit()"
                                    aria-label="Close edit promo panel"
                                >
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </header>

                            <div class="flex-1 overflow-hidden">
                                <form wire:submit.prevent="update" class="flex h-full flex-col">
                                    <div class="flex-1 overflow-y-auto px-6 py-6">
                                        <div class="space-y-8">
                                            <!-- Promo Details -->
                                            <section class="space-y-4">
                                                <div>
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Promo Details</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Basic information about the promotion.</p>
                                                </div>

                                                <div class="space-y-4">
                                                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                                        <div>
                                                            <label for="edit_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                Promo Name
                                                            </label>
                                                            <input type="text" id="edit_name" wire:model="edit_name"
                                                                class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                                placeholder="Autumn Sale" required />
                                                            @error('edit_name') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                                        </div>

                                                        <div>
                                                            <label for="edit_code" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                                Promo Code
                                                            </label>
                                                            <input type="text" id="edit_code" wire:model="edit_code"
                                                                class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                                placeholder="PRM-001" />
                                                            @error('edit_code') <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p> @enderror
                                                        </div>
                                                    </div>

                                                    <div>
                                                        <label for="edit_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                            Promo Type
                                                        </label>
                                                        <select id="edit_type" wire:model="edit_type"
                                                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm"
                                                            required>
                                                            <option value="" disabled>Select Promo Type</option>
                                                            @foreach($promo_type_options as $type)
                                                                <option value="{{ $type }}">{{ $type }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('edit_type')
                                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </section>

                                            <!-- Date Range -->
                                            <section class="space-y-4">
                                                <div>
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Date Range</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Set the active period for the promotion.</p>
                                                </div>

                                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                            Start Date
                                                        </label>
                                                        <input type="date" wire:model="edit_startDate"
                                                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm" />
                                                        @error('edit_startDate') <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                                                    </div>

                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                                                        <input type="date" wire:model="edit_endDate"
                                                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm" />
                                                        @error('edit_endDate') <span class="text-red-600 dark:text-red-400 text-sm">{{ $message }}</span> @enderror
                                                    </div>
                                                </div>
                                            </section>

                                            <!-- Branch & Product Selection -->
                                            <section class="space-y-4">
                                                <div>
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Batch & Product Selection</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Choose where and what products are included in the promo.</p>
                                                </div>

                                                <!-- Batch Allocation Selector -->
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                        Batch
                                                    </label>
                                                    <div class="relative" wire:click.outside="$set('editBatchDropdown', false)">
                                                        <div wire:click="$toggle('editBatchDropdown')" 
                                                            class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm cursor-pointer flex justify-between items-center">
                                                            <span class="flex flex-wrap gap-1">
                                                                @if(empty($edit_selected_batches))
                                                                    <span class="text-gray-400">Select Batch</span>
                                                                @else
                                                                    @foreach($batchAllocations as $batchAllocation)
                                                                        @if(in_array($batchAllocation->id, $edit_selected_batches))
                                                                            <span class="inline-flex items-center gap-1.5 bg-indigo-100 text-indigo-800 text-xs font-medium px-2.5 py-1 rounded-full dark:bg-indigo-900/30 dark:text-indigo-300">
                                                                                <span class="font-semibold">{{ $batchAllocation->ref_no }}</span>
                                                                                @if($batchAllocation->batch_number)
                                                                                    <span class="text-gray-500 dark:text-gray-400">•</span>
                                                                                    <span class="text-gray-700 dark:text-gray-300 font-normal">Batch: {{ $batchAllocation->batch_number }}</span>
                                                                                @endif
                                                                            </span>
                                                                        @endif
                                                                    @endforeach
                                                                @endif
                                                            </span>
                                                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                            </svg>
                                                        </div>

                                                        @if($editBatchDropdown)
                                                        <div class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-auto dark:bg-gray-700 dark:border-gray-600">
                                                            @foreach($batchAllocations as $batchAllocation)
                                                                <label class="flex items-center p-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 border-b border-gray-100 dark:border-gray-600 last:border-b-0">
                                                                    <input type="checkbox" value="{{ $batchAllocation->id }}" wire:model="edit_selected_batches"
                                                                        class="form-checkbox h-4 w-4 accent-blue-600 text-blue-600 focus:ring-blue-500 border-gray-300 rounded dark:accent-blue-400 dark:text-blue-400">
                                                                    <div class="ml-3 flex-1">
                                                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                                            {{ $batchAllocation->ref_no }}
                                                                        </div>
                                                                        @if($batchAllocation->batch_number)
                                                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                                                                Batch Number: <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $batchAllocation->batch_number }}</span>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                        @endif
                                                        @error('edit_selected_batches')
                                                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                </div>

                                                <!-- Product Selection -->
                                                <div class="grid gap-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Product</label>
                                                        <div class="relative" wire:click.outside="$set('editProductDropdown', false)">
                                                            <div wire:click="$toggle('editProductDropdown')"
                                                                class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm cursor-pointer flex justify-between items-center">
                                                                <div class="flex flex-wrap gap-1 items-center">
                                                                    @if(!$editProductDropdown)
                                                                        @if(empty($edit_selected_products))
                                                                            <span class="text-gray-400">
                                                                                @if(empty($edit_selected_batches))
                                                                                    Select batch allocation first
                                                                                @else
                                                                                    Select Product
                                                                                @endif
                                                                            </span>
                                                                        @else
                                                                            @foreach($this->availableProductsForEditBatches as $product)
                                                                                @if(in_array($product->id, $edit_selected_products))
                                                                                    <span class="inline-flex items-center bg-gray-600 text-white text-xs font-medium px-2 py-0.5 rounded-full">
                                                                                        {{ $product->name }}
                                                                                        <span class="ml-1 text-gray-200 text-[11px]">₱{{ number_format($product->price, 0) }}</span>
                                                                                    </span>
                                                                                @endif
                                                                            @endforeach
                                                                        @endif
                                                                    @else
                                                                        <span class="text-gray-400">
                                                                            @if(empty($edit_selected_batches))
                                                                                Select batch allocation first
                                                                            @else
                                                                                Select Product
                                                                            @endif
                                                                        </span>
                                                                    @endif
                                                                </div>
                                                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                                                </svg>
                                                            </div>

                                                            @if($editProductDropdown && !empty($edit_selected_batches))
                                                                <div class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-auto dark:bg-gray-700 dark:border-gray-600">
                                                                    @if($this->availableProductsForEditBatches->count() > 0)
                                                                        @foreach($this->availableProductsForEditBatches as $product)
                                                                            <label class="flex items-center justify-between p-3 cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-600 border-b border-gray-100 dark:border-gray-600 last:border-b-0 {{ $product->isDisabled ?? false ? 'opacity-50 cursor-not-allowed' : '' }}">
                                                                                <div class="flex items-center space-x-3">
                                                                                    <input type="checkbox"
                                                                                        value="{{ $product->id }}"
                                                                                        wire:model="edit_selected_products"
                                                                                        @if($product->isDisabled ?? false) disabled @endif
                                                                                        onclick="event.stopPropagation()"
                                                                                        class="form-checkbox h-4 w-4 accent-green-600 text-green-600 focus:ring-green-500 border-gray-300 rounded dark:accent-green-400 dark:text-green-400 {{ $product->isDisabled ?? false ? 'cursor-not-allowed' : '' }}">
                                                                                    <span class="text-sm text-gray-900 dark:text-white {{ $product->isDisabled ?? false ? 'line-through' : '' }}">
                                                                                        {{ $product->name }}
                                                                                        @if($product->isDisabled ?? false)
                                                                                            <span class="ml-2 text-xs text-red-500">(Already in promo)</span>
                                                                                        @endif
                                                                                    </span>
                                                                                </div>
                                                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-500 text-white dark:bg-emerald-400 dark:text-gray-900 shadow-sm">
                                                                                    ₱ {{ number_format($product->price, 0) }}
                                                                                </span>
                                                                            </label>
                                                                        @endforeach
                                                                    @else
                                                                        <div class="p-4 text-center text-gray-500 dark:text-gray-400 text-sm">
                                                                            No products available in selected batch allocations
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            @endif

                                                            @error('edit_selected_products')
                                                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </section>

                                            <!-- Description -->
                                            <section class="space-y-4">
                                                <div>
                                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Description</h3>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400">Additional details about the promotion.</p>
                                                </div>

                                                <div>
                                                    <label for="edit_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                        Description
                                                    </label>
                                                    <textarea id="edit_description" wire:model="edit_description"
                                                        class="block w-full rounded-md border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white sm:text-sm resize-y"
                                                        style="min-height: 80px;"
                                                        placeholder="Promo for Halloween"></textarea>
                                                    @error('edit_description')
                                                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            </section>
                                        </div>
                                    </div>

                                    <div class="border-t border-gray-200 bg-white px-6 py-4 dark:border-zinc-700 dark:bg-zinc-900">
                                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                Review changes carefully before updating the promo.
                                            </div>
                                            <div class="flex items-center gap-3">
                                                <flux:button type="button" wire:click="cancelEdit" variant="ghost">
                                                    Cancel
                                                </flux:button>

                                                <flux:button type="submit" variant="primary" wire:loading.attr="disabled">
                                                    <span wire:loading.remove wire:target="update">Save Changes</span>
                                                    <span wire:loading wire:target="update">Saving...</span>
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

        <!-- View Modal -->
        <section>
            <div x-data="{ show: @entangle('showViewModal').live }" 
                x-show="show" 
                x-cloak
                class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto 
                        md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center bg-black/50">
                <div class="relative w-full max-w-3xl max-h-full">
                    <div class="relative bg-white rounded-lg shadow-xl dark:bg-gray-700">

                        <!-- Header -->
                        <div class="flex items-start justify-between p-5 border-b dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
                            <div class="flex items-center space-x-3">
                                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">View Promo</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Promotion details</p>
                                </div>
                            </div>
                            <button type="button" 
                                    wire:click="$set('showViewModal', false)"
                                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 
                                        rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center 
                                        dark:hover:bg-gray-600 dark:hover:text-white">
                                <svg class="w-3 h-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                            </button>
                        </div>

                        <!-- Body -->
                        <div class="p-6 space-y-6">

                            <!-- Basic Information Section -->
                            <div class="space-y-4">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                    <span class="w-1.5 h-5 bg-blue-500 rounded-full mr-2"></span>
                                    Basic Information
                                </h4>
                                
                                <div class="grid md:grid-cols-3 gap-6">
                                    <div class="bg-gray-50 dark:bg-gray-600/30 p-4 rounded-lg border border-gray-100 dark:border-gray-600">
                                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Promo Name</h4>
                                        <p class="text-gray-900 dark:text-white font-medium">{{ $view_name }}</p>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-600/30 p-4 rounded-lg border border-gray-100 dark:border-gray-600">
                                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Promo Code</h4>
                                        <p class="text-gray-900 dark:text-white font-medium">
                                            @if($view_code)
                                                <span class="inline-flex items-center bg-blue-100 text-blue-800 px-3 py-1.5 rounded-full text-xs font-medium dark:bg-blue-900/30 dark:text-blue-300">
                                                    {{ $view_code }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-600/30 p-4 rounded-lg border border-gray-100 dark:border-gray-600">
                                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Promo Type</h4>
                                        <p class="text-gray-900 dark:text-white font-medium">{{ $view_type }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Date Information Section -->
                            <div class="space-y-4">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                    <span class="w-1.5 h-5 bg-blue-500 rounded-full mr-2"></span>
                                    Date Information
                                </h4>
                                
                                <div class="grid md:grid-cols-2 gap-6">
                                    <div class="bg-gray-50 dark:bg-gray-600/30 p-4 rounded-lg border border-gray-100 dark:border-gray-600">
                                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">Start Date</h4>
                                        <p class="text-gray-900 dark:text-white font-medium">{{ $view_startDate ?? '-' }}</p>
                                    </div>
                                    <div class="bg-gray-50 dark:bg-gray-600/30 p-4 rounded-lg border border-gray-100 dark:border-gray-600">
                                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-2">End Date</h4>
                                        <p class="text-gray-900 dark:text-white font-medium">{{ $view_endDate ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Batch Allocations & Products Section -->
                            <div class="space-y-4">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                    <span class="w-1.5 h-5 bg-blue-500 rounded-full mr-2"></span>
                                    Batch Allocations & Products
                                </h4>
                                
                                <div class="grid md:grid-cols-3 gap-6 ml-2">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-3">Batch Allocations</h4>
                                        <div class="flex flex-wrap gap-2">
                                            @forelse($batchAllocations as $batchAllocation)
                                                @if(in_array($batchAllocation->id, $view_selected_batches ?? []))
                                                    <div class="inline-flex flex-col gap-1 bg-blue-100 text-blue-800 px-3 py-2 rounded-lg text-xs font-medium dark:bg-blue-900/30 dark:text-blue-300">
                                                        <div class="flex items-center gap-1.5">
                                                            <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                                            </svg>
                                                            <span class="font-semibold">{{ $batchAllocation->ref_no }}</span>
                                                        </div>
                                                        @if($batchAllocation->batch_number)
                                                            <div class="text-[10px] text-blue-600 dark:text-blue-400 font-normal pl-4.5">
                                                                Batch: <span class="font-semibold">{{ $batchAllocation->batch_number }}</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            @empty
                                                <span class="text-gray-400 text-sm">-</span>
                                            @endforelse
                                        </div>
                                    </div>

                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-3">Products</h4>
                                        <div class="flex flex-wrap gap-2">
                                            @forelse($products as $product)
                                                @if(in_array($product->id, $view_selected_products ?? []))
                                                    <span class="inline-flex items-center bg-gray-100 text-gray-800 text-xs font-medium px-3 py-1.5 rounded-full dark:bg-gray-600 dark:text-gray-300">
                                                        {{ $product->name }}
                                                        <span class="ml-1 text-gray-600 dark:text-gray-400 text-[11px] font-medium">₱{{ number_format($product->price, 0) }}</span>
                                                    </span>
                                                @endif
                                            @empty
                                                <span class="text-gray-400 text-sm">-</span>
                                            @endforelse
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <!-- Description Section -->
                            <div class="space-y-4">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                    <span class="w-1.5 h-5 bg-blue-500 rounded-full mr-2"></span>
                                    Description
                                </h4>
                                
                                <div class="bg-gray-50 dark:bg-gray-600/30 p-4 rounded-lg border border-gray-100 dark:border-gray-600">
                                    <p class="text-gray-900 dark:text-white">
                                        @if($view_description)
                                            {{ $view_description }}
                                        @else
                                            <span class="text-gray-400 italic">No description</span>
                                        @endif
                                    </p>
                                </div>
                            </div>

                        </div>

                        <!-- Footer -->
                        <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600 bg-gray-50 dark:bg-gray-700">
                            <flux:button wire:click="$set('showViewModal', false)" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-lg">
                                Close
                            </flux:button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- End -->
    </div>
</div>