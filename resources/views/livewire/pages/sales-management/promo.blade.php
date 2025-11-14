<x-slot:header>Promo Creation</x-slot:header>
<x-slot:subheader>Create new promos</x-slot:subheader>

<div class="pt-4">
    <div class="">
        <section class="mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-6">
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
            </div>
        </section>

        <!-- Create Promo Form -->
        <x-collapsible-card title="Create promo" open="false" size="full">
            <section class="mb-6">
                <div class="px-4 py-5 sm:p-6">
                    <form wire:submit.prevent="submit">
                        <div class="grid gap-6">

                            <!-- Row 1: Promo Name, Promo Code, Promo Type -->
                            <div class="grid md:grid-cols-3 gap-6">
                                <div>
                                    <label for="promo_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        Promo Name
                                    </label>
                                    <input type="text" id="promo_name" wire:model="promo_name"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 
                                            focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 
                                            dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="Autumn Sale" required />
                                    @error('promo_name') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="promo_code" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        Promo Code
                                    </label>
                                    <input type="text" id="promo_code" wire:model="promo_code"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 
                                            focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 
                                            dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        placeholder="PRM-001" required />
                                    @error('promo_code') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="promo_type" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        Promo Type
                                    </label>
                                    <select id="promo_type" wire:model="promo_type"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 block w-full"
                                        required>
                                        <option value="" disabled selected>Select Promo Type</option>
                                        @foreach($promo_type_options as $type)
                                            <option value="{{ $type }}">{{ $type }}</option>
                                        @endforeach
                                    </select>
                                    @error('promo_type')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Row 2: Start Date, End Date -->
                            <div class="grid md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        Start Date
                                    </label>
                                    <input type="date" wire:model="startDate"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 
                                            focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 
                                            dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                                    @error('startDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">End Date</label>
                                    <input type="date" wire:model="endDate"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 
                                            focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 
                                            dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" />
                                    @error('endDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                             <!-- Row 3: Branch, Product -->
                            <div class="grid md:grid-cols-2 gap-6">

                                {{-- Branch Selector --}}
                                <div class="relative" wire:click.outside="$set('branchDropdown', false)">
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                        Branch
                                    </label>
                                    <div wire:click="$toggle('branchDropdown')" 
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 cursor-pointer 
                                                dark:bg-gray-700 dark:border-gray-600 dark:text-white flex justify-between items-center">
                                        <span>
                                            @if(empty($selected_branches))
                                                Select Branch
                                            @else
                                                @foreach($branches as $branch)
                                                    @if(in_array($branch->id, $selected_branches))
                                                        <span class="inline-block mr-1 bg-blue-500 text-white px-2 py-0.5 rounded text-xs">
                                                            {{ $branch->name }}
                                                        </span>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </span>
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>

                                    @if($branchDropdown)
                                    <div class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-auto dark:bg-gray-700 dark:border-gray-600">
                                        @foreach($branches as $branch)
                                            <label class="flex items-center p-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                                                <input type="checkbox" value="{{ $branch->id }}" wire:model="selected_branches"
                                                    class="form-checkbox h-4 w-4 text-blue-600 dark:text-blue-400">
                                                <span class="ml-2 text-sm text-gray-900 dark:text-white">{{ $branch->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                    @endif
                                    @error('selected_branches')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Product Selector --}}
                                <div class="grid gap-6 @if($promo_type === 'Buy one Take one') md:grid-cols-2 @else md:grid-cols-1 @endif">

                                {{-- First Product Dropdown --}}
                                <div class="relative" wire:click.outside="$set('productDropdown', false)">
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Product</label>

                                    {{-- Dropdown Toggle --}}
                                    <div wire:click="$toggle('productDropdown')"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 cursor-pointer 
                                            dark:bg-gray-700 dark:border-gray-600 dark:text-white flex justify-between items-center flex-wrap gap-2 min-h-[42px]">
                                        
                                        <div class="flex flex-wrap gap-1 items-center">
                                            {{-- Show pills when dropdown is closed --}}
                                            @if(!$productDropdown)
                                                @if(empty($selected_products))
                                                    <span class="text-gray-400 dark:text-gray-300">Select Product</span>
                                                @else
                                                    @foreach($products as $product)
                                                        @if(in_array($product->id, $selected_products))
                                                            <span class="inline-flex items-center bg-gray-600 text-white text-xs font-medium px-2 py-0.5 rounded-full">
                                                                {{ $product->name }}
                                                                <span class="ml-1 text-gray-200 text-[11px]">₱{{ number_format($product->price, 0) }}</span>
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            @else
                                                <span class="text-gray-400 dark:text-gray-300">Select Product</span>
                                            @endif
                                        </div>

                                        <svg class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </div>

                                    {{-- Dropdown List --}}
                                    @if($productDropdown)
                                        <div class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-auto dark:bg-gray-700 dark:border-gray-600">
                                            @foreach($products as $product)
                                                <label class="flex items-center justify-between p-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 rounded-md">
                                                    <div class="flex items-center space-x-2">
                                                        <input type="checkbox"
                                                            value="{{ $product->id }}"
                                                            wire:model="selected_products"
                                                            @if($promo_type === 'Buy one Take one' && count($selected_products) >= 1 && !in_array($product->id, $selected_products)) disabled @endif
                                                            onclick="event.stopPropagation()"
                                                            class="form-checkbox h-4 w-4 text-green-600 dark:text-green-400">
                                                        <span class="text-sm text-gray-900 dark:text-white">
                                                            {{ $product->name }}
                                                        </span>
                                                    </div>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-500 text-white dark:bg-emerald-400 dark:text-gray-900 shadow-sm">
                                                        ₱ {{ number_format($product->price, 0) }}
                                                    </span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif

                                    @error('selected_products')
                                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Second Product Dropdown (only for Buy one Take one) --}}
                                @if($promo_type === 'Buy one Take one')
                                    <div class="relative" wire:click.outside="$set('secondProductDropdown', false)">
                                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Second Product</label>

                                        {{-- Dropdown Toggle --}}
                                        <div wire:click="$toggle('secondProductDropdown')"
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 cursor-pointer 
                                                dark:bg-gray-700 dark:border-gray-600 dark:text-white flex justify-between items-center flex-wrap gap-2 min-h-[42px]">
                                            
                                            <div class="flex flex-wrap gap-1 items-center">
                                                @if(!$secondProductDropdown)
                                                    @if(empty($selected_second_products))
                                                        <span class="text-gray-400 dark:text-gray-300">
                                                            @if(empty($selected_products))
                                                                Select first product first
                                                            @else
                                                                Select Second Product
                                                            @endif
                                                        </span>
                                                    @else
                                                        @foreach($products as $product)
                                                            @if(in_array($product->id, $selected_second_products))
                                                                <span class="inline-flex items-center bg-gray-600 text-white text-xs font-medium px-2 py-0.5 rounded-full">
                                                                    {{ $product->name }}
                                                                    <span class="ml-1 text-gray-200 text-[11px]">₱{{ number_format($product->price, 0) }}</span>
                                                                </span>
                                                            @endif
                                                        @endforeach
                                                    @endif
                                                @else
                                                    <span class="text-gray-400 dark:text-gray-300">
                                                        @if(empty($selected_products))
                                                            Select first product first
                                                        @else
                                                            Select Second Product
                                                        @endif
                                                    </span>
                                                @endif
                                            </div>

                                            <svg class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>

                                        {{-- Dropdown List --}}
                                        @if($secondProductDropdown && !empty($selected_products))
                                            <div class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-auto dark:bg-gray-700 dark:border-gray-600">
                                                @foreach($products as $product)
                                                    @if(!empty($selected_products) && $product->price <= $products->find($selected_products[0])->price && $product->id != $selected_products[0])
                                                        <label class="flex items-center justify-between p-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 rounded-md">
                                                            <div class="flex items-center space-x-2">
                                                                <input type="checkbox"
                                                                    value="{{ $product->id }}"
                                                                    wire:model="selected_second_products"
                                                                    onclick="event.stopPropagation()"
                                                                    class="form-checkbox h-4 w-4 text-green-600 dark:text-green-400">
                                                                <span class="text-sm text-gray-900 dark:text-white">{{ $product->name }}</span>
                                                            </div>
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-500 text-white dark:bg-emerald-400 dark:text-gray-900 shadow-sm">
                                                                ₱ {{ number_format($product->price, 0) }}
                                                            </span>
                                                        </label>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif

                                        @error('selected_second_products')
                                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                    </div>
                                @endif
                            </div>
                            </div>

                            <!-- Description -->
                            <div>
                                <label for="promo_description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                    Description
                                </label>
                                <textarea id="promo_description" wire:model="promo_description"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 
                                        focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 
                                        dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 
                                        resize-y"
                                    style="min-height: 50px; overflow: auto;"
                                    placeholder="Promo for Halloween"></textarea>
                                @error('promo_description')
                                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit button -->
                        <div class="flex justify-end mt-6">
                            <flux:button type="submit" wire:loading.attr="disabled" wire:target="submit">
                                <span wire:loading.remove wire:target="submit">Submit</span>
                                <span wire:loading wire:target="submit">Saving...</span>
                            </flux:button>
                        </div>

                    </form>
                </div>
            </section>
        </x-collapsible-card>

        <!-- Search -->
        <section class="mb-6">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                <!-- Search Bar -->
                <div class="flex items-center justify-between p-4 pr-10">
                    <div class="flex space-x-6">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg aria-hidden="true" class="w-5 h-5 text-gray-500 dark:text-gray-400"
                                    fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                    d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                                    clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" wire:model.live.debounce.500ms="search"
                            class="block w-64 p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Search Promo...">
                        </div>
                    </div>
                </div>
                <!-- Data Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th class="px-6 py-3">Promo</th>
                                <th class="px-6 py-3">Type</th>
                                <th class="px-6 py-3">Branch</th>
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

                                    <!-- Branch -->
                                    <td class="px-6 py-4">
                                        @if(!empty($item->branch_names))
                                            @foreach($item->branch_names as $branchName)
                                                <span class="inline-block mr-1 bg-blue-500 text-white px-2 py-0.5 rounded text-xs">
                                                    {{ $branchName }}
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
                                        <flux:button wire:click.prevent="view({{ $item->id }})" variant="outline" size="sm">View</flux:button>
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

        <section>
            <!-- Edit Promo Modal -->
            <div x-data="{ show: @entangle('showEditModal').live }" x-show="show" x-cloak
                class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center">
                <div class="relative w-full max-w-2xl max-h-full">
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">

                        {{-- Modal Header --}}
                        <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Edit Promo</h3>
                            <button type="button" wire:click="cancelEdit"
                                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 14 14">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                        stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                </svg>
                                <span class="sr-only">Close modal</span>
                            </button>
                        </div>

                        {{-- Modal Body --}}
                        <div class="p-6 space-y-6">

                            {{-- Promo Name & Code --}}
                            <div class="grid gap-6 md:grid-cols-2">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Promo Name</label>
                                    <input type="text" wire:model="edit_name"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 block w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @error('edit_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Promo Code</label>
                                    <input type="text" wire:model="edit_code"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 block w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @error('edit_code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Promo Type --}}
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Promo Type</label>
                                <select wire:model="edit_type"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 block w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">Select Promo Type</option>
                                    @foreach($promo_type_options as $type)
                                        <option value="{{ $type }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                                @error('edit_type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            {{-- Start & End Dates --}}
                            <div class="grid gap-6 md:grid-cols-2">
                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Start Date</label>
                                    <input type="date" wire:model="edit_startDate"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 block w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @error('edit_startDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">End Date</label>
                                    <input type="date" wire:model="edit_endDate"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 block w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    @error('edit_endDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Branch Dropdown --}}
                            <div class="relative" wire:click.outside="$set('editBranchDropdown', false)">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Branch</label>
                                <div wire:click="$toggle('editBranchDropdown')"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 cursor-pointer flex justify-between items-center flex-wrap gap-2 min-h-[42px]">
                                    <div class="flex flex-wrap gap-1 items-center">
                                        @if(empty($edit_selected_branches))
                                            <span class="text-gray-400 dark:text-gray-300">Select Branch</span>
                                        @else
                                            @foreach($branches as $branch)
                                                @if(in_array($branch->id, $edit_selected_branches))
                                                    <span class="inline-block bg-blue-500 text-white px-2 py-0.5 rounded text-xs">{{ $branch->name }}</span>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                    <svg class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>

                                @if($editBranchDropdown)
                                    <div class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-auto dark:bg-gray-700 dark:border-gray-600">
                                        @foreach($branches as $branch)
                                            <label class="flex items-center p-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600">
                                                <input type="checkbox" value="{{ $branch->id }}" wire:model="edit_selected_branches" class="form-checkbox h-4 w-4 text-blue-600 dark:text-blue-400">
                                                <span class="ml-2 text-sm text-gray-900 dark:text-white">{{ $branch->name }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                                @error('edit_selected_branches') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>

                            {{-- First Product Dropdown --}}
                            <div class="relative" wire:click.outside="$set('editProductDropdown', false)">
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Product <span class="text-red-500">*</span></label>
                                <div wire:click="$toggle('editProductDropdown')" 
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 cursor-pointer flex justify-between items-center flex-wrap gap-2 min-h-[42px]">
                                    <div class="flex flex-wrap gap-1 items-center">
                                        @if(!$editProductDropdown)
                                            @if(empty($edit_selected_products))
                                                <span class="text-gray-400 dark:text-gray-300">Select Product</span>
                                            @else
                                                @foreach($products as $product)
                                                    @if(in_array($product->id, $edit_selected_products))
                                                        <span class="inline-flex items-center bg-gray-600 text-white text-xs font-medium px-2 py-0.5 rounded-full">
                                                            {{ $product->name }}
                                                            <span class="ml-1 text-gray-200 text-[11px]">₱{{ number_format($product->price, 0) }}</span>
                                                        </span>
                                                    @endif
                                                @endforeach
                                            @endif
                                        @else
                                            <span class="text-gray-400 dark:text-gray-300">Select Product</span>
                                        @endif
                                    </div>
                                    <svg class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>

                                @if($editProductDropdown)
                                    <div class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-auto dark:bg-gray-700 dark:border-gray-600">
                                        @foreach($products as $product)
                                            <label class="flex items-center justify-between p-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 rounded-md">
                                                <div class="flex items-center space-x-2">
                                                    <input type="checkbox" value="{{ $product->id }}"
                                                        wire:model="edit_selected_products"
                                                        onclick="event.stopPropagation()"
                                                        @if($edit_type === 'Buy one Take one' && !in_array($product->id, $edit_selected_products) && count($edit_selected_products) >= 1) disabled @endif
                                                        class="form-checkbox h-4 w-4 text-green-600 dark:text-green-400">
                                                    <span class="text-sm text-gray-900 dark:text-white">{{ $product->name }}</span>
                                                </div>
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-500 text-white dark:bg-emerald-400 dark:text-gray-900 shadow-sm">
                                                    ₱ {{ number_format($product->price, 0) }}
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                                @error('edit_selected_products') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>

                            {{-- Second Product Dropdown (only for Buy-One-Take-One) --}}
@if($edit_type === 'Buy one Take one')
    <div class="relative" wire:click.outside="$set('editSecondProductDropdown', false)">
        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Second Product <span class="text-red-500">*</span></label>
        <div wire:click="$toggle('editSecondProductDropdown')" 
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 cursor-pointer flex justify-between items-center flex-wrap gap-2 min-h-[42px] 
                   @error('edit_selected_second_products') border-red-500 @enderror">
            <div class="flex flex-wrap gap-1 items-center">
                @if(!$editSecondProductDropdown)
                    @if(empty($edit_selected_second_products))
                        <span class="text-gray-400 dark:text-gray-300">Select Second Product</span>
                    @else
                        @foreach($this->editSecondProducts as $product)
                            @if(in_array($product->id, $edit_selected_second_products))
                                <span class="inline-flex items-center bg-gray-600 text-white text-xs font-medium px-2 py-0.5 rounded-full">
                                    {{ $product->name }}
                                    <span class="ml-1 text-gray-200 text-[11px]">₱{{ number_format($product->price, 0) }}</span>
                                </span>
                            @endif
                        @endforeach
                    @endif
                @else
                    <span class="text-gray-400 dark:text-gray-300">Select Second Product</span>
                @endif
            </div>
            <svg class="w-4 h-4 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </div>

        @if($editSecondProductDropdown)
            <div class="absolute z-10 mt-1 w-full bg-white border border-gray-300 rounded-lg shadow-lg max-h-60 overflow-auto dark:bg-gray-700 dark:border-gray-600">
                @if($this->editSecondProducts->count() > 0)
                    @foreach($this->editSecondProducts as $product)
                        <label class="flex items-center justify-between p-2 cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-600 rounded-md">
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" value="{{ $product->id }}" 
                                    wire:model="edit_selected_second_products" 
                                    onclick="event.stopPropagation()"
                                    @if(!in_array($product->id, $edit_selected_second_products) && count($edit_selected_second_products) >= 1) disabled @endif
                                    class="form-checkbox h-4 w-4 text-green-600 dark:text-green-400">
                                <span class="text-sm text-gray-900 dark:text-white">{{ $product->name }}</span>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-500 text-white dark:bg-emerald-400 dark:text-gray-900 shadow-sm">
                                ₱ {{ number_format($product->price, 0) }}
                            </span>
                        </label>
                    @endforeach
                @else
                    <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                        No products available for selection
                    </div>
                @endif
            </div>
        @endif
        @error('edit_selected_second_products') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
    </div>
@endif

                            {{-- Description --}}
                            <div>
                                <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Description</label>
                                <textarea wire:model="edit_description"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg p-2.5 block w-full dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                        rows="3"></textarea>
                                @error('edit_description') <p class="mt-1 text-sm text-red-500">{{ $message }}</p> @enderror
                            </div>

                        </div>

                        {{-- Modal Footer --}}
                        <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                            <flux:button wire:click="update">
                                Save changes
                            </flux:button>
                            <flux:button wire:click="cancelEdit" variant="outline">
                                Cancel
                            </flux:button>
                        </div>

                    </div>
                </div>
            </div>
        </section>

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

                            <!-- Branches & Products Section -->
                            <div class="space-y-4">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                                    <span class="w-1.5 h-5 bg-blue-500 rounded-full mr-2"></span>
                                    Branches & Products
                                </h4>
                                
                                <div class="grid md:grid-cols-3 gap-6 ml-2">
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-3">Branches</h4>
                                        <div class="flex flex-wrap gap-2">
                                            @forelse($branches as $branch)
                                                @if(in_array($branch->id, $view_selected_branches ?? []))
                                                    <span class="inline-flex items-center bg-blue-100 text-blue-800 px-3 py-1.5 rounded-full text-xs font-medium dark:bg-blue-900/30 dark:text-blue-300">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                                                        </svg>
                                                        {{ $branch->name }}
                                                    </span>
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

                                    @if($view_type === 'Buy one Take one')
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-300 mb-3">Second Product</h4>
                                            <div class="flex flex-wrap gap-2">
                                                @forelse($products as $product)
                                                    @if(in_array($product->id, $view_selected_second_products ?? []))
                                                        <span class="inline-flex items-center bg-emerald-100 text-emerald-800 text-xs font-medium px-3 py-1.5 rounded-full dark:bg-emerald-900/30 dark:text-emerald-300">
                                                            {{ $product->name }}
                                                            <span class="ml-1 text-emerald-600 dark:text-emerald-400 text-[11px] font-medium">₱{{ number_format($product->price, 0) }}</span>
                                                        </span>
                                                    @endif
                                                @empty
                                                    <span class="text-gray-400 text-sm">-</span>
                                                @endforelse
                                            </div>
                                        </div>
                                    @endif
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