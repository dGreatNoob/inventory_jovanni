<x-slot:header>Paper Roll Warehouse</x-slot:header>
<x-slot:subheader>Profile</x-slot:subheader>
<div class="pt-4">
    <div class="">
        <section
            class="mb-5 max-w-xlg p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <form wire:submit.prevent="submit">
                <div class="grid gap-6 mb-6 md:grid-cols-2">
                    <div>
                        <label for="gsm"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">GSM</label>
                        <input type="text" id="gsm" wire:model="gsm"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="25" required />
                    </div>
                    <div>
                        <label for="width_size" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Width Size</label>
                        <input type="text" id="width_size" wire:model="width_size"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="24" required />
                    </div>
                    <div>
                        <label for="classification"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Classification</label>
                        <input type="text" id="classification" wire:model="classification"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Kraft Paper" required />
                    </div>
                    <div>
                        <label for="supplier" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Supplier</label>
                        <input type="text" id="supplier" wire:model="supplier"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                            placeholder="Tanduay"required />
                    </div>
                </div>
                <div class="mb-6">
                    <label for="country_origin" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Country Origin</label>
                    <input type="country_origin" id="country_origin" wire:model="country_origin"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Philippines" required />
                </div>
                <div class="flex justify-end">
                    <button type="submit"
                        class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Submit
                    </button>
                </div>

            </form>
        </section>
        @if (session()->has('message'))
            <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800">
                {{ session('message') }}
            </div>
        @endif
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
                                <input type="text" wire:model.debounce.500ms="search"
                                    class="block w-64 p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Search Raw Material..." required="">
                            </div>

                            <div class="flex items-center space-x-2">
                                <label class="text-sm font-medium text-gray-900 dark:text-white">Material Type:</label>
                                <select
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="">All Materials</option>
                                    <option value="paper">Paper</option>
                                    <option value="ink">Ink</option>
                                    <option value="adhesive">Adhesive</option>
                                    <option value="coating">Coating</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                       GSM
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Width Size
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Classification
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Supplier
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Country Origin
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($items as $item)
                                    <tr class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                            {{ $item['gsm'] }}
                                        </th>
                                        <td class="px-6 py-4">{{ $item['width_size'] }}</td>
                                        <td class="px-6 py-4">{{ $item['classification'] }}</td>
                                        <td class="px-6 py-4">{{ $item['supplier'] }}</td>
                                        <td class="px-6 py-4">{{ $item['country_origin'] }}</td>
                                        <td class="px-6 py-4">
                                            <a href="#" wire:click.prevent="edit({{ $item['id'] }})"
                                                class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                                            <a href="#" wire:click.prevent="confirmDelete({{ $item->id }})"
                                                class="font-medium text-red-600 dark:text-red-500 hover:underline">Delete</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="py-4 px-3">
                        <div class="flex ">
                            <div class="flex space-x-4 items-center mb-3">
                                <label class="w-32 text-sm font-medium text-gray-900 dark:text-white">Per Page</label>
                                <select wire:model="perPage"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500">
                                    <option value="5">5</option>
                                    <option value="10" selected>10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <section>
            <div x-data="{ show: @entangle('showEditModal').live }" x-show="show" x-cloak
                class="fixed top-0 left-0 right-0 z-50 w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full flex items-center justify-center">
                <div class="relative w-full max-w-2xl max-h-full">
                    <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                        <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Edit Raw Material</h3>
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
                                    <label for="edit_gsm"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">GSM</label>
                                    <input type="text" wire:model="edit_gsm" id="edit_gsm"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500
                                        block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white 
                                        dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Enter GSM" required />
                                    @error('edit_gsm') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="edit_width_size"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white"
                                        >Width Size</label>
                                    <input type="text" wire:model="edit_width_size" id="edit_width_size"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500
                                        block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white 
                                        dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter width size" />
                                    @error('edit_width_size') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="edit_classification"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Classification</label>
                                    <input type="text" wire:model="edit_classification" id="edit_classification"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500
                                        block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white 
                                        dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter classification" />
                                    @error('edit_classification') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="edit_supplier"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Supplier</label>
                                    <input type="text" wire:model="edit_supplier" id="edit_supplier"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500
                                        block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white 
                                        dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter supplier" />
                                    @error('edit_supplier') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label for="edit_country_origin"
                                        class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Country of Origin</label>
                                    <input type="text" wire:model="edit_country_origin" id="edit_country_origin"
                                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500
                                        block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white 
                                        dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Enter country of origin" />
                                    @error('edit_country_origin') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center p-6 space-x-2 border-t border-gray-200 rounded-b dark:border-gray-600">
                            <button type="button" wire:click="update"
                                    class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
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
                                <h3 class="mb-5 text-lg font-normal text-gray-500 dark:text-gray-400">Are you sure you want to delete this raw material profile?</h3>
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