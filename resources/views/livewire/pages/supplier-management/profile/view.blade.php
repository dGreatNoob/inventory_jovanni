<x-slot:header>Supplier Profile</x-slot:header>
<div class="pt-4">
    <div class="">
        <div class="mb-4">
            <a href="{{ route('supplier.profile') }}"
                class="inline-flex items-center gap-2 px-3 py-2 rounded-md 
                        text-base font-bold tracking-wide 
                        text-gray-700 dark:text-gray-300 
                        bg-gray-50 dark:bg-gray-700
                        hover:bg-gray-100 dark:hover:bg-gray-600 
                        transition-colors duration-200">
                <svg class="w-[28px] h-[28px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12l4-4m-4 4 4 4"/>
            </svg>
                Back
            </a>
    </div>

        <section class="mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- Total Orders -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-[28px] h-[28px] text-blue-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 3v4a1 1 0 0 1-1 1H5m4 8h6m-6-4h6m4-8v16a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V7.914a1 1 0 0 1 .293-.707l3.914-3.914A1 1 0 0 1 9.914 3H18a1 1 0 0 1 1 1Z"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Orders</dt>
                                    <dd class="text-lg font-medium text-gray-900 dark:text-white">25</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Total Spent -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-[28px] h-[28px] text-green-600 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M12 14a3 3 0 0 1 3-3h4a2 2 0 0 1 2 2v2a2 2 0 0 1-2 2h-4a3 3 0 0 1-3-3Zm3-1a1 1 0 1 0 0 2h4v-2h-4Z" clip-rule="evenodd"/>
                                    <path fill-rule="evenodd" d="M12.293 3.293a1 1 0 0 1 1.414 0L16.414 6h-2.828l-1.293-1.293a1 1 0 0 1 0-1.414ZM12.414 6 9.707 3.293a1 1 0 0 0-1.414 0L5.586 6h6.828ZM4.586 7l-.056.055A2 2 0 0 0 3 9v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2h-4a5 5 0 0 1 0-10h4a2 2 0 0 0-1.53-1.945L17.414 7H4.586Z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Spent</dt>
                                    <dd class="text-lg font-medium text-gray-900 dark:text-white">₱ 45,250.00</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Active Products -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-[30px] h-[30px] text-violet-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                    <path fill="currentColor" d="M9.98189 4.50602c1.24881-.67469 2.78741-.67469 4.03621 0l3.9638 2.14148c.3634.19632.6862.44109.9612.72273l-6.9288 3.60207L5.20654 7.225c.2403-.22108.51215-.41573.81157-.5775l3.96378-2.14148ZM4.16678 8.84364C4.05757 9.18783 4 9.5493 4 9.91844v4.28296c0 1.3494.7693 2.5963 2.01811 3.2709l3.96378 2.1415c.32051.1732.66011.3019 1.00901.3862v-7.4L4.16678 8.84364ZM13.009 20c.3489-.0843.6886-.213 1.0091-.3862l3.9638-2.1415C19.2307 16.7977 20 15.5508 20 14.2014V9.91844c0-.30001-.038-.59496-.1109-.87967L13.009 12.6155V20Z"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Active Products</dt>
                                    <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ $activeProductCount }}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Delivery Performance -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
                    <div class="p-5">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <svg class="w-[28px] h-[28px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" viewBox="0 0 24 24">
                                    <path fill-rule="evenodd" d="M2.586 4.586A2 2 0 0 1 4 4h8a2 2 0 0 1 2 2h5a1 1 0 0 1 .894.553l2 4c.07.139.106.292.106.447v4a1 1 0 0 1-1 1h-.535a3.5 3.5 0 1 1-6.93 0h-3.07a3.5 3.5 0 1 1-6.93 0H3a1 1 0 0 1-1-1V6a2 2 0 0 1 .586-1.414ZM18.208 15.61a1.497 1.497 0 0 0-2.416 0 1.5 1.5 0 1 0 2.416 0Zm-10 0a1.498 1.498 0 0 0-2.416 0 1.5 1.5 0 1 0 2.416 0Zm5.79-7.612v2.02h5.396l-1-2.02h-4.396ZM9 8.667a1 1 0 1 0-2 0V10a1 1 0 0 0 .293.707l1.5 1.5a1 1 0 0 0 1.414-1.414L9 9.586v-.92Z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-5 w-0 flex-1">
                                <dl>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Delivery Performance</dt>
                                    <dd class="text-lg font-medium text-gray-900 dark:text-white">95%</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Profiling -->
        <x-collapsible-card title="Supplier Information" open="false" size="full">
                <section class="mb-8">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-10 gap-y-10">

                        <!-- Company Name -->
                        <div class="pb-4">
                            <div class="flex items-center space-x-2 mb-2 text-gray-600 dark:text-gray-400">
                            <svg class="w-[28px] h-[28px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 4h12M6 4v16M6 4H5m13 0v16m0-16h1m-1 16H6m12 0h1M6 20H5M9 7h1v1H9V7Zm5 0h1v1h-1V7Zm-5 4h1v1H9v-1Zm5 0h1v1h-1v-1Zm-3 4h2a1 1 0 0 1 1 1v4h-4v-4a1 1 0 0 1 1-1Z"/>
                            </svg>

                            <p class="text-base font-bold tracking-wide">Company Name</p>
                            </div>
                            <p class="block mb-2 text-m font-medium text-gray-900 dark:text-white">{{ $supplier_name }}</p>
                        </div>

                        <!-- Supplier Code -->
                        <div class="pb-4">
                            <div class="flex items-center space-x-2 mb-2 text-gray-600 dark:text-gray-400">
                            <svg class="w-[28px] h-[28px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 4h12M6 4v16M6 4H5m13 0v16m0-16h1m-1 16H6m12 0h1M6 20H5M9 7h1v1H9V7Zm5 0h1v1h-1V7Zm-5 4h1v1H9v-1Zm5 0h1v1h-1v-1Zm-3 4h2a1 1 0 0 1 1 1v4h-4v-4a1 1 0 0 1 1-1Z"/>
                            </svg>
                            <p class="text-base font-bold tracking-wide">Supplier Code</p>
                            </div>
                            <p class="block mb-2 text-m font-medium text-gray-900 dark:text-white">{{ $supplier_code }}</p>
                        </div>

                        <!-- Address -->
                        <div class="pb-4">
                            <div class="flex items-center space-x-2 mb-2 text-gray-600 dark:text-gray-400">
                            <svg class="w-[28px] h-[28px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 13a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/>
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.8 13.938h-.011a7 7 0 1 0-11.464.144h-.016l.14.171c.1.127.2.251.3.371L12 21l5.13-6.248c.194-.209.374-.429.54-.659l.13-.155Z"/>
                            </svg>
                            <p class="text-base font-bold tracking-wide">Address</p>
                            </div>
                            <p class="block mb-2 text-m font-medium text-gray-900 dark:text-white">{{ $supplier_address }}</p>
                        </div>

                        <!-- Contact Person -->
                        <div class="pb-4">
                            <div class="flex items-center space-x-2 mb-2 text-gray-600 dark:text-gray-400">
                            <svg class="w-[28px] h-[28px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a9 9 0 1 0 0-18 9 9 0 0 0 0 18Zm0 0a8.949 8.949 0 0 0 4.951-1.488A3.987 3.987 0 0 0 13 16h-2a3.987 3.987 0 0 0-3.951 3.512A8.948 8.948 0 0 0 12 21Zm3-11a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                            </svg>
                            <p class="text-base font-bold tracking-wide">Contact Person</p>
                            </div>
                            <p class="block mb-2 text-m font-medium text-gray-900 dark:text-white">{{ $contact_person }}</p>
                        </div>

                        <!-- Contact Number -->
                        <div class="pb-4">
                            <div class="flex items-center space-x-2 mb-2 text-gray-600 dark:text-gray-400">
                            <svg class="w-[28px] h-[28px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.427 14.768 17.2 13.542a1.733 1.733 0 0 0-2.45 0l-.613.613a1.732 1.732 0 0 1-2.45 0l-1.838-1.84a1.735 1.735 0 0 1 0-2.452l.612-.613a1.735 1.735 0 0 0 0-2.452L9.237 5.572a1.6 1.6 0 0 0-2.45 0c-3.223 3.2-1.702 6.896 1.519 10.117 3.22 3.221 6.914 4.745 10.12 1.535a1.601 1.601 0 0 0 0-2.456Z"/>
                            </svg>
                            <p class="text-base font-bold tracking-wide">Contact Number</p>
                            </div>
                            <p class="block mb-2 text-m font-medium text-gray-900 dark:text-white">{{ $contact_num }}</p>
                        </div>

                        <!-- Email -->
                        <div class="pb-4">
                            <div class="flex items-center space-x-2 mb-2 text-gray-600 dark:text-gray-400">
                            <svg class="w-[28px] h-[28px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-width="2" d="m3.5 5.5 7.893 6.036a1 1 0 0 0 1.214 0L20.5 5.5M4 19h16a1 1 0 0 0 1-1V6a1 1 0 0 0-1-1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1Z"/>
                            </svg>
                            <p class="text-base font-bold tracking-wide">Email</p>
                            </div>
                            <p class="block mb-2 text-m font-medium text-gray-900 dark:text-white">{{ $email }}</p>
                        </div>

                        <!-- Status -->
                        <div class="pb-4">
                            <div class="flex items-center space-x-2 mb-2 text-gray-600 dark:text-gray-400">
                            <svg class="w-[28px] h-[28px] text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                            </svg>
                            <p class="text-base font-bold tracking-wide">Status</p>
                            </div>
                            @if($status === 'active')
                            <span class="ml-6 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[12px] font-semibold bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-300">
                                <span class="w-2 h-2 rounded-full bg-green-500 dark:bg-green-400"></span>
                                Active
                            </span>
                            @elseif($status === 'inactive')
                            <span class="ml-6 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[12px] font-semibold bg-red-100 text-red-800 dark:bg-red-800/30 dark:text-red-300">
                                <span class="w-2 h-2 rounded-full bg-red-500 dark:bg-red-400"></span>
                                Inactive
                            </span>
                            @else
                            <span class="ml-6 inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[12px] font-semibold bg-yellow-100 text-yellow-800 dark:bg-yellow-800/30 dark:text-yellow-300">
                                <span class="w-2 h-2 rounded-full bg-yellow-500 dark:bg-yellow-400"></span>
                                Pending
                            </span>
                            @endif
                        </div>
                        </div>
                </section>
        </x-collapsible-card>

        <x-collapsible-card title="Recent Purchase Order" open="false" size="full">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">PO Number</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Total Price</th>
                            <th scope="col" class="px-6 py-3">Order Date</th>
                            <th scope="col" class="px-6 py-3">Delivery Date</th>
                            <th scope="col" class="px-6 py-3">Variance</th>
                            <th scope="col" class="px-6 py-3">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </x-collapsible-card>

        @if (session()->has('message'))
            <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800">
                {{ session('message') }}
            </div>
        @endif

        <!-- DataTables Section -->
            <x-collapsible-card title="Supplier Products" open="false" size="full">
                <section class="mb-6">
                            <!-- Data Table -->
                            <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                                <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                    <tr>
                                        <th scope="col" class="px-6 py-3">Product</th>
                                        <th scope="col" class="px-6 py-3">SKU</th>
                                        <th scope="col" class="px-6 py-3">Price</th>
                                        <th scope="col" class="px-6 py-3">Min Order</th>
                                        <th scope="col" class="px-6 py-3">Lead time</th>
                                        <th scope="col" class="px-6 py-3">Status</th>
                                        <th scope="col" class="px-6 py-3">Action</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($items as $item)
                                        <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $item->name }}</td>
                                            <td class="px-6 py-4">{{ $item->sku }}</td>
                                            <td class="px-6 py-4">₱ {{ number_format($item->price, 2) }}</td>
                                            <td class="px-6 py-4">{{ $item->min_order_quantity }}</td>
                                            <td class="px-6 py-4">{{ $item->lead_time }} days</td>
                                            <td class="px-6 py-4">
                                                @if(!$item->disabled)
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[12px] font-semibold bg-green-100 text-green-800 dark:bg-green-800/30 dark:text-green-300">
                                                        <span class="w-2 h-2 rounded-full bg-green-500 dark:bg-green-400"></span>
                                                        Active
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[12px] font-semibold bg-red-100 text-red-800 dark:bg-red-800/30 dark:text-red-300">
                                                        <span class="w-2 h-2 rounded-full bg-red-500 dark:bg-red-400"></span>
                                                        Inactive
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                <flux:button wire:click.prevent="edit({{ $item->id }})" variant="outline" size="sm">Edit</flux:button>
                                                <flux:button wire:click.prevent="confirmDelete({{ $item->id }})" variant="outline" size="sm">Delete</flux:button>
                                            </td>
                                        </tr>
                                    @endforeach
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
                    </div>
                </section>

                <!-- Action Button -->
                <section>
                    <div x-data="{ show: @entangle('showEditModal').live }" x-show="show" x-cloak
                        class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center">
                        <div class="relative w-full max-w-2xl max-h-full">
                            <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Edit Product Status</h3>
                                    <button type="button" wire:click="cancel"
                                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                                            fill="none" viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                        </svg>
                                        <span class="sr-only">Close modal</span>
                                    </button>
                                </div>

                                <div class="p-6">
                                    <div>
                                        <label for="edit_status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Status</label>
                                        <select wire:model="edit_status" id="edit_status" 
                                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg 
                                                focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
                                                dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white 
                                                dark:focus:ring-blue-500 dark:focus:border-blue-500" required>
                                            <option value="">Select status</option>
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                        @error('edit_status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                                    <flux:button wire:click="update">
                                        Save changes
                                    </flux:button>
                                    <flux:button wire:click="cancel" variant="outline">
                                        Cancel
                                    </flux:button>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($showDeleteModal)
                        <div class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center">
                            <div class="relative w-full max-w-md max-h-full">
                                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                                    <button type="button" class="absolute top-3 right-2.5 text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ml-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" wire:click="cancel">
                                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                                        </svg>
                                        <span class="sr-only">Close modal</span>
                                    </button>
                                    <div class="p-6 text-center">
                                        <svg class="mx-auto mb-4 text-gray-400 w-12 h-12 dark:text-gray-200" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11V6m0 8h.01M19 10a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                                        </svg>
                                        <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete this product?</h3>
                                        <flux:button wire:click="delete" class="mr-2 bg-red-600 hover:bg-red-700 text-white">
                                            Yes, I'm sure
                                        </flux:button>
                                        <flux:button wire:click="cancel" variant="outline">
                                            No, cancel
                                        </flux:button>
                                    </div>
                                </div>
                            </div>
                    @endif
                </section>
        </x-collapsible-card>
    </div>
</div>
</div>