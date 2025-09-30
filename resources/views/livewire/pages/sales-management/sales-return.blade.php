<x-slot:header>Sales Return</x-slot:header>
<x-slot:subheader>Start a sales return by clicking the button. You'll be able to view and make changes to the return afterward.</x-slot:subheader>
<div>
    <div>
        <!-- New Request Button -->
        <div class="flex justify-end mb-4">
            <x-button 
                type="button" 
                variant="primary" 
                wire:click="$set('showCreateModal', true)">
                + New Sales Return
            </x-button>
        </div>      

        @if (session()->has('message'))
           <x-flash-message />
        @endif

        @if (session()->has('error'))          
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif 

        <!-- Search Bar -->                
        <div 
            x-data="{ isOpen: @entangle('showCreateModal') }" 
            x-init="$watch('isOpen', value => {
                    if (!value) {                       
                        $wire.close()                       
                    }
                })
            ">
            <x-modal wire:model="showCreateModal" class="max-h-[80vh] w-full max-w-2xl">
                <h2 class="text-xl font-bold mb-4">Sales Return</h2>
               
                <form wire:submit.prevent="submit" class="space-y-6">
                    @if (session()->has('errorCreate'))          
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
                            {{ session('errorCreate') }}
                        </div>
                    @endif 
                    
                    @if($editValue)
                        <i>The sales order dropdown is disabled for editing.</i> 
                    @endif

                    <div class="grid gap-6 mt-5 mb-2 md:grid-cols-2">                  
                        @if($editValue)
                            <?php 
                                $salesOrderNumber = $salesOrders->get($sales_order_id); 
                            ?>
                            <x-input type="text" class="bg-gray-200 cursor-not-allowed" disabled readonly rows="8" value="{{$salesOrderNumber}}" name="return_date" label="Sales Order # " />                           
                        @else
                            <x-dropdown
                                wire:model.live="sales_order_id" 
                                name="sales_order_id" 
                                label="Sales Order #" 
                                :options="$salesOrders ?? []"
                                placeholder="Pick sales order to fill details" 
                            />  
                        @endif 
                        <x-input type="text" wire:model.defer="return_date" rows="8" name="return_date" label="Return Date" />
                    </div>
                    
                    <div class="grid gap-6 mb-2 md:grid-cols-2">                     
                       <x-dropdown
                        wire:model.defer="status" 
                        name="status" 
                        label="Status" 
                        :options="[                    
                            'pending'   => 'Pending',  
                            'approved'  => 'Approved',                   
                            'rejected'  => 'Rejected',
                            'confirmed' => 'Confirmed',
                            'processing'=> 'Processing',
                        ]"
                        placeholder="--Select--" 
                    />
                        <x-input type="text" wire:model.defer="return_reference" rows="8" name="return_reference" label="Return Reference #" />                    
                    </div>
                   <div class="grid gap-6 mb-2 md:grid-cols-2">
                    
                    
                   </div>   
                    <x-textarea wire:model="reason"  label="What is the reason for returning this item?" placeholder="Write your reason..." />
                  
                    <h2 class="text-xl font-bold mb-4 mt-5">Items</h2>                     

                    {{-- @if ($errors->has('returnItems.0.quantity'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
                            {{ $errors->first('returnItems.0.quantity') }}
                        </div>
                    @endif --}}

                    @php
                        $itemErrors = collect($errors->getMessages())
                            ->filter(fn($_, $key) => Str::startsWith($key, 'returnItems.'));
                    @endphp

                    @if ($itemErrors->isNotEmpty())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
                            <ul class="list-disc list-inside text-sm">
                                @foreach ($itemErrors->flatten() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                <th scope="col" class="px-2 py-3">Product</th>
                                <th scope="col" class="px-2 py-3">Qty</th>
                                <th scope="col" class="px-2 py-3">Unit Price</th>
                                <th scope="col" class="px-2 py-3">Total Price</th>  
                                <th scope="col" class="px-2 py-3">Mark as returned</th>                            
                            </tr>
                        </thead>
                        <tbody>                         
                        
                            @foreach($returnItems as $index => $item)
                                 <tr wire:key
                                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                    <th scope="row"
                                            class="w-24 px-2 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                                                            
                                            @if( isset($item['supply_sku']))
                                                {{ $item['supply_sku'] ?? 'N/A' }} 
                                            @endif 
                                    </th> 
                                   <td class="px-2 py-4 w-24">    
                                    <?php                                   
                                        $maxQty = $returnItems[$index]['old_quantity']  ;   
                                    ?>                                   
                                       <input 
                                            type="number"                                           
                                            name="quantity" 
                                            wire:model.live="returnItems.{{ $index }}.quantity" 
                                            value="{{ $maxQty }}" 
                                            min="1" 
                                            max="{{ $maxQty }}" 
                                            step="1"
                                        >
                                    </td>
                                    <td class="px-2 py-4">                                       
                                       {{ number_format($returnItems[$index]['unit_price'] ?? 0, 2) }}
                                    </td>
                                    <td class="px-2 py-4">
                                        {{ number_format($returnItems[$index]['total_price'] ?? 0, 2) }}
                                    </td>
                                    
                                        <td class="border p-1 text-center">
                                            <div style="">
                                           
                                            <div class="flex items-center justify-center">
                                                <div class="bg-gray-200 p-4 rounded">
                                                    @if($this->isItemIsOne == 1)
                                                        <input type="checkbox"
                                                        {{ $returnItems[$index]['is_checked'] ? 'checked' : '' }}
                                                        class="text-red-600"
                                                        name="selected"
                                                        disabled
                                                        readonly>
                                                    @else 
                                                        <input type="checkbox" wire:model.live="returnItems.{{ $index }}.is_checked" name="selected" class="text-red-600">
                                                    @endif 

                                                   
                                                </div>
                                            </div>
                                            </div>
                                        </td> 
                                   
                                </tr>
                            @endforeach
                        </tbody>
                        </table>
                    </div>                

                    <div class="mt-5 text-right font-semibold">
                        Total Refund: ₱{{ number_format($total_refund, 2) }}
                    </div>

                    <x-button type="button" variant="secondary" wire:click="close">Cancel</x-button>

                    @if($hasPendingSalesReturn)
                        @if($total_refund > 0 )
                            <x-button type="submit" variant="primary">{{$editValue ? 'Update': 'Save'}}</x-button>
                        @endif
                    @else 
                        @if($total_refund > 0 )
                            <x-button type="submit" variant="primary">{{$editValue ? 'Update': 'Save'}}</x-button>
                        @endif 
                    @endif

                    @if (session()->has('message'))
                        <div class="mt-4 p-2 text-green-800 bg-green-200 rounded">
                            {{ session('message') }}
                        </div>
                    @endif 
                </form>
            </x-modal>
        </div>
        
              
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
                                <input type="text" wire:model.live.debounce.300ms="search"
                                    class="block w-64 p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                    placeholder="Search Sales Return..." required="">
                            </div>                           
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-sm text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-6 py-3">
                                        Sales Order #
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3">
                                        Customer Name
                                    </th>
                                     <th scope="col" class="px-6 py-3">
                                        Return Date
                                    </th>                                  
                                    <th scope="col" class="px-6 py-3">
                                        Return Ref#
                                    </th>                                    
                                    <th scope="col" class="px-6 py-3">
                                       Return Type
                                    </th>                                 
                                    <th scope="col" class="px-6 py-3">
                                        Action
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    use App\Enums\Enum\PermissionEnum;
                                @endphp
                                @forelse ($salesReturns as $data)
                                    <tr wire:key
                                        class="odd:bg-white odd:dark:bg-gray-900 even:bg-gray-50 even:dark:bg-gray-800 border-b dark:border-gray-700 border-gray-200">
                                         <th scope="row"
                                            class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                           {{$data->salesOrder->sales_order_number}}
                                        </th>                                      
                                        <td class="px-6 py-4">
                                            <span
                                                class="
                                                    px-2 py-1 rounded-full text-white text-xs font-semibold
                                                    @if ($data->status === 'pending') bg-yellow-500
                                                    @elseif ($data->status === 'approved') bg-green-600
                                                    @elseif ($data->status === 'rejected') bg-red-600
                                                    @else bg-gray-500 @endif">
                                                {{ ucfirst($data->status) }}
                                            </span>
                                        </td>                                       
                                        <td class="px-6 py-4">
                                            {{$data->salesOrder->customer->name}}
                                        </td> 
                                        <td class="px-6 py-4">
                                           {{ $data->return_date }}
                                        </td>   
                                        <td class="px-6 py-4">
                                           {{ $data->return_reference }}
                                        </td>    
                                        <td class="px-6 py-4">
                                           {{ $data->is_full_return ? 'Full': 'Partial' }}
                                        </td>                                 
                                        <td class="px-6 py-4">                                            
                                            <button wire:click="edit({{ $data->id }})">Edit</button>
                                            <a href="{{ route('salesreturn.view',$data->id) }}"
                                                class="font-medium px-1 text-grey-600 dark:text-blue-500 hover:underline"> 
                                                View
                                            </a>                                           
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            No request sales orders found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="py-4 px-3">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <!-- Per Page Selection -->
                            <div class="flex items-center space-x-4">
                                <label for="perPage" class="text-sm font-medium text-gray-900 dark:text-white">Per
                                    Page
                                </label>
                                <select 
                                    id="perPage" 
                                    wire:model.live="perPage"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="5">5</option>
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                            <!-- Pagination Links -->                          
                            <div>
                                {{$salesReturns->links()}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>