<x-slot:header>Supplier Management</x-slot:header>
<x-slot:subheader>Profile</x-slot:subheader>
<div class="pt-4">
    <div class="">
        <section class="my-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <!-- Total Suppliers -->
                <div class="bg-white rounded-xl shadow-md p-4 flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Suppliers</p>
                        <p class="text-2xl font-semibold text-gray-800">{{ $this->totalSuppliers }}</p>
                    </div>
                    <div class="flex-shrink-0 w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M12 12a4 4 0 100-8 4 4 0 000 8z" />
                        </svg>
                    </div>
                </div>

                <!-- Active Contracts -->
                <div class="bg-white rounded-xl shadow-md p-4 flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Active Contracts</p>
                        <p class="text-2xl font-semibold text-gray-800">{{ $this->activeSuppliers }}</p>
                    </div>
                    <div class="flex-shrink-0 w-12 h-12 bg-green-100 text-green-600 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6M9 16h6M9 8h6M7 2h10a2 2 0 012 2v16a2 2 0 01-2 2H7a2 2 0 01-2-2V4a2 2 0 012-2z" />
                        </svg>
                    </div>
                </div>

                <!-- Pending Contracts -->
                <div class="bg-white rounded-xl shadow-md p-4 flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Pending Contracts</p>
                        <p class="text-2xl font-semibold text-gray-800">{{ $this->pendingSuppliers }}</p>
                    </div>
                    <div class="flex-shrink-0 w-12 h-12 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2 9h14l-2-9M9 21h6" />
                        </svg>
                    </div>
                </div>

                <!-- Total Spend -->
                <div class="bg-white rounded-xl shadow-md p-4 flex justify-between items-center">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Total Spend</p>
                        <p class="text-2xl font-semibold text-gray-800">PHP 2.4M</p>
                    </div>
                    <div class="flex-shrink-0 w-12 h-12 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 3v10h10M21 21H3v-4h18v4z" />
                        </svg>
                    </div>
                </div>
            </div>
        </section>

        <!-- Profiling -->
        <section
            class="mb-5 max-w-xlg p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <form wire:submit.prevent="submit">
                <div class="grid gap-6 mb-6 md:grid-cols-2">
                    <!-- Supplier Name -->
                    <div>
                        <label for="supplier_name"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Supplier Name</label>
                        <input type="text" id="supplier_name" wire:model="supplier_name"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 
                                focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 
                                dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Jovanni Bags Manufacturer Co." required />
                        @error('supplier_name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="supplier_code"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Supplier Code</label>
                        <input type="text" id="supplier_code" wire:model="supplier_code"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 
                                focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 
                                dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="P20-209" required />
                        @error('supplier_code')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="supplier_address"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Address</label>
                        <input type="text" id="supplier_address" wire:model="supplier_address"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 
                                focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 
                                dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="P24 lawaan st. bayugan city" required />
                        @error('supplier_address')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contact_person"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Contact Person</label>
                        <input type="text" id="contact_person" wire:model="contact_person"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 
                                focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 
                                dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Mark Tabugara" required />
                        @error('contact_person')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contact_num"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Contact Number</label>
                        <input type="tel" id="contact_num" wire:model="contact_num"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 
                                focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 
                                dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="012345678910" required />
                        @error('contact_num')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                        <input type="text" id="email" wire:model="email"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 
                                focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 
                                dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="JovanniBags@gmail.com" required />
                        @error('email')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Spacing -->
                    <div class="md:col-span-2 h-6"></div>

                    <div x-data="{ open: false }" class="relative">
                        <label for="categories" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Categories</label>

                        <!-- Button -->
                        <button type="button" @click="open = !open"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 
                                focus:border-blue-500 block w-full p-2.5 text-left flex items-center justify-between 
                                dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <span class="truncate">
                                <template x-if="!$wire.categories.length">Select categories</template>
                                <template x-for="cat in $wire.categories" :key="cat">
                                    <span class="inline-block px-2 py-1 mr-1 mb-1 text-xs font-semibold text-gray-800 bg-gray-200 rounded-full"
                                        x-text="cat"></span>
                                </template>
                            </span>
                            <svg class="w-5 h-5 text-gray-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Dropdown -->
                        <div x-show="open" @click.away="open = false"
                            class="absolute mt-1 w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 
                                rounded-lg shadow-lg z-50 max-h-72 overflow-auto">
                            <template x-for="category in @js($availableCategories)" :key="category">
                                <label class="flex items-center px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer">
                                    <input type="checkbox" :value="category" wire:model.defer="categories" class="mr-2 w-5 h-5">
                                    <span x-text="category" class="text-gray-900 dark:text-white text-sm"></span>
                                </label>
                            </template>
                        </div>
                        @error('categories')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>


                    <!-- For tags -->
                    
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            wire:loading.attr="disabled"
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none 
                                focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center 
                                dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">

                        <span wire:loading.remove>Submit</span>
                        <span wire:loading>Saving...</span>
                    </button>
                </div>

            </form>
        </section>

        @if (session()->has('message'))
            <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800">
                {{ session('message') }}
            </div>
        @endif

        <!-- DataTables Section -->
        <section>
            <div>
                <!-- Start coding here -->
                <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
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
                                    placeholder="Search Supplier...">
                            </div>

                            <!-- <div class="flex items-center space-x-2">
                                <label class="text-sm font-medium text-gray-900 dark:text-white">Material Type:</label>
                                <select
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-gray-500 focus:border-gray-500 dark:focus:ring-gray-400 dark:focus:border-gray-400 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="">All Materials</option>
                                    <option value="paper">Paper</option>
                                    <option value="ink">Ink</option>
                                    <option value="adhesive">Adhesive</option>
                                    <option value="coating">Coating</option>
                                </select>
                            </div> -->
                        </div>
                    </div>

                    <!-- Data Table -->
                    <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Supplier</th>
                                <th scope="col" class="px-6 py-3">Contact</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                                <th scope="col" class="px-6 py-3">Categories</th>
                                <th scope="col" class="px-6 py-3">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($items as $item)
                                <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                    <!-- Supplier -->
                    <td class="px-6 py-4">
                        <div class="flex flex-col space-y-1">
                            <div class="inline-flex items-center space-x-2 text-sm">
                                <span class="font-medium text-gray-900 dark:text-white">{{ $item->name }}</span>
                                <span class="text-gray-400 dark:text-gray-500">|</span>
                                <span class="text-gray-500 dark:text-gray-300">{{ $item->code ?? '-' }}</span>
                            </div>
                            <div class="flex items-center text-gray-500 dark:text-gray-300 text-sm space-x-1 mt-1">
                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2C6.686 2 4 4.686 4 8c0 4 6 10 6 10s6-6 6-10c0-3.314-2.686-6-6-6zM10 10a2 2 0 110-4 2 2 0 010 4z"/>
                                </svg>
                                <span>{{ $item->address ?? '-' }}</span>
                            </div>
                        </div>
                    </td>

                    <!-- Contact -->
                    <td class="px-6 py-4">
                        <div class="flex flex-col space-y-1 text-sm text-gray-500 dark:text-gray-300">
                            <!-- Contact Person -->
                            <div class="flex items-center space-x-1">
                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2a4 4 0 100 8 4 4 0 000-8zm0 10c-5.33 0-8 2.667-8 4v2h16v-2c0-1.333-2.667-4-8-4z"/>
                                </svg>
                                <span>{{ $item->contact_person ?? '-' }}</span>
                            </div>

                            <!-- Email -->
                            <div class="flex items-center space-x-1">
                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M2.01 6.995C2 6.443 2.442 6 2.994 6h18.012c.552 0 .994.443.994.995v10.01c0 .552-.442.995-.994.995H2.994A.996.996 0 012 17.005V6.995zm1.822-.077l8.184 5.093 8.184-5.093H3.832zm16.346 1.385l-7.46 4.641-7.46-4.641V16h14.92V8.303z"/>
                                </svg>
                                <span>{{ $item->email ?? '-' }}</span>
                            </div>

                            <!-- Contact Number -->
                            <div class="flex items-center space-x-1">
                                <svg class="w-4 h-4 text-gray-400 dark:text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M2.003 5.884l2-3A1 1 0 015 2h3a1 1 0 011 1v3a1 1 0 01-.293.707l-2 2a11.05 11.05 0 005.657 5.657l2-2A1 1 0 0114 11h3a1 1 0 011 1v3a1 1 0 01-.293.707l-2 2a2 2 0 01-2.414.243C7.716 16.912 3.088 12.284 2.003 5.884z"/>
                                </svg>
                                <span>{{ $item->contact_num ?? '-' }}</span>
                            </div>
                        </div>
                    </td>

                    <!-- Status -->
                    <td class="px-6 py-4">
                        @if($item->status === 'active')
                            <span class="inline-block px-2 py-1 text-xs font-semibold text-green-800 bg-green-100 rounded-full">
                                Active
                            </span>
                        @elseif($item->status === 'inactive')
                            <span class="inline-block px-2 py-1 text-xs font-semibold text-red-800 bg-red-100 rounded-full">
                                Inactive
                            </span>
                        @elseif($item->status === 'pending')
                            <span class="inline-block px-2 py-1 text-xs font-semibold text-orange-800 bg-orange-100 rounded-full">
                                Pending
                            </span>
                        @else
                            <span class="inline-block px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-200 rounded-full">
                                -
                            </span>
                        @endif
                    </td>

                    <!-- Categories -->
                    <td class="px-6 py-4">
                        @if(!empty($item->categories))
                            @foreach($item->categories as $category)
                                <span class="inline-block px-2 py-1 mr-1 mb-1 text-xs font-semibold text-gray-800 bg-gray-200 rounded-full">
                                    {{ $category }}
                                </span>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>

                    <!-- Action -->
                    <td class="px-6 py-4 space-x-2">
                        <x-button wire:click.prevent="edit({{ $item->id }})" variant="warning">Edit</x-button>
                        <x-button wire:click.prevent="confirmDelete({{ $item->id }})" variant="danger">Delete</x-button>
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
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Edit Supplier</h3>
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

                        <div class="p-6 space-y-6">
                            <div class="grid gap-6 mb-6 md:grid-cols-2">
                            <div>
                                <label for="edit_name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Name</label>
                                <input type="text" wire:model="edit_name" id="edit_name"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg 
                                            focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
                                            dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white 
                                            dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Enter supplier name" required />
                                @error('edit_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="edit_code" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Supplier Code</label>
                                <input type="text" wire:model="edit_code" id="edit_code"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg 
                                            focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
                                            dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white 
                                            dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Enter supplier code" required />
                                @error('edit_code') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="edit_address" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Address</label>
                                <input type="text" wire:model="edit_address" id="edit_address"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg 
                                            focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
                                            dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white 
                                            dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Enter address" required />
                                @error('edit_address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="edit_contact_person" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Contact Person</label>
                                <input type="text" wire:model="edit_contact_person" id="edit_contact_person"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg 
                                            focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
                                            dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white 
                                            dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Enter contact person" required />
                                @error('edit_contact_person') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="edit_contact_num" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Contact Number</label>
                                <input type="text" wire:model="edit_contact_num" id="edit_contact_num"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg 
                                            focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
                                            dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white 
                                            dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Enter contact number" required />
                                @error('edit_contact_num') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-4">
                                <label for="edit_email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email</label>
                                <input type="email" id="edit_email" wire:model.defer="edit_email"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg 
                                        focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5
                                        dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                    placeholder="Enter supplier email">
                                @error('edit_email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="md:col-span-2 h-6"></div>
                            
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
                                    <option value="pending">Pending</option>
                                </select>
                                @error('edit_status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div x-data="{ open: false }" class="relative md:col-span-2">
                            <label for="edit_categories" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Categories</label>

                            <!-- Trigger button -->
                            <button type="button" @click="open = !open"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg 
                                    focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 text-left
                                    flex items-center justify-between dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <span class="truncate">
                                    <template x-if="!$wire.edit_categories.length">Select categories</template>
                                    <template x-for="cat in $wire.edit_categories" :key="cat">
                                        <span class="inline-block px-2 py-1 mr-1 mb-1 text-xs font-semibold text-gray-800 bg-gray-200 rounded-full" x-text="cat"></span>
                                    </template>
                                </span>
                                <svg class="w-5 h-5 text-gray-400 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Dropdown -->
                            <div x-show="open" @click.away="open = false"
                                class="absolute mt-1 w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg z-50 max-h-72 overflow-auto">
                                @foreach ($availableCategories as $category)
                                    <label class="flex items-center px-3 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 cursor-pointer">
                                        <input type="checkbox" value="{{ $category }}" wire:model="edit_categories" class="mr-2 w-5 h-5">
                                        <span class="text-gray-900 dark:text-white text-sm">{{ $category }}</span>
                                    </label>
                                @endforeach
                            </div>

                            @error('edit_categories')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                            </div>

                        </div>
                        </div>

                        <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                            <button type="button" wire:click="update"
                                    class="text-white bg-gray-700 hover:bg-gray-800 focus:ring-4 focus:outline-none focus:ring-gray-300 dark:bg-gray-600 dark:hover:bg-gray-500 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-gray-600 dark:hover:bg-gray-700 dark:focus:ring-gray-800">
                                Save changes
                            </button>
                            <button type="button" wire:click="cancel"
                                    class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-blue-300 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">
                                Cancel
                            </button>
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
                                <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete this supplier profile?</h3>
                                <button type="button" wire:click="delete" class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 dark:focus:ring-red-800 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
                                    Yes, I'm sure
                                </button>
                                <button type="button" wire:click="cancel" class="text-gray-500 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-500 dark:hover:text-white dark:hover:bg-gray-600 dark:focus:ring-gray-600">No, cancel</button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </section>
        

    </div>
</div>

</div>