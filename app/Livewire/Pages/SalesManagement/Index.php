<?php
namespace App\Livewire\Pages\SalesManagement;

use Livewire\Component;
use App\Models\SalesOrder;
use App\Models\SupplyProfile;
use App\Models\SupplyBatch;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\SalesReturn;
use App\Models\SalesOrderItem;
use Illuminate\Support\Str;

use Illuminate\Validation\Rule;
#[
    Layout('components.layouts.app'),
    Title('Sales Management')
]
class Index extends Component
{
    use WithPagination;
    public $salesOrders = []; 
    public $perPage = 10;
    public $total = 0;



    // Status is now always 'pending' for new orders
    public $contactPersonName = '';   
    public $phone = '';
    public $email = '';
    public $billingAddress = '';
    public $shippingAddress = '';

    public $paymentMethod = '';
    public $shippingMethod = '';
    public $paymentTerms = '';
    public $deliveryDate ='';
    public $search = '';
    public $editValue = null;
    public $customerSelected = [];
    public $subclassSelected = [];
    public $agentSelected = [];
         
    public $shippingMethodDropDown = [];
    public $paymentMethodDropdown = []; 
    public $paymentTermsDropdown = [];  
    public $selectedSalesOrder = [];

    public $items = [];

    public $day = '';
    public $month = '';
    public $year = '';

    public $days = [];
    public $months = [];
    public $years = [];  

    public $company_results = [];
    public $subclass_results = [];
    public $agent_results = [];
    public $product_list = [];
    public $getSalesOrderDetails = [];
    public $product_taken_ids = [];

    public $showQrModal = false;
    
    // Customer data for auto-population
    public $customerData = [];
    public function mount()
    {      
        $this->shippingMethodDropDown = SalesOrder::shippingMethodDropDown();   
        $this->paymentMethodDropdown  = SalesOrder::paymentMethodDropdown();   
        $this->paymentTermsDropdown   = SalesOrder::paymentTermsDropdown();  
        $this->items = [
            [   
                'sales_order_item_id' => 0,
                'product_id' => '', 
                'quantity' => 1, 
                'price_option' => [], 
                'unit_price' => 0
            ]
        ];  
        
        // Get products from Product model with inventory instead of SupplyProfile
        $thisProducList = \App\Models\Product::select('products.id','products.name','products.sku','product_inventory.available_quantity as stock_quantity','products.uom as unit')
            ->join('product_inventory', 'products.id', '=', 'product_inventory.product_id')
            ->where('product_inventory.available_quantity', '>', 0) // Only show products with stock
            ->orderBy('products.name')
            ->get();

        if($thisProducList->isNotEmpty()){
            $this->product_list = $thisProducList->pluck('name','id')->toArray();
        }
        
        $this->company_results = \App\Models\Branch::all()->pluck('name', 'id');
        $this->agent_results = \App\Models\Agent::all()->pluck('name', 'id');
    }            
       
    public function addItem()
    {
        $this->items[] = [
            'sales_order_item_id' => 0,
            'product_id' => '', 
            'quantity' => 1,  
            'price_option' => [], 
            'unit_price' => 0
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items); // Re-index
    }

    public function edit($id)
    {
        $salesOrders = SalesOrder::with(['items' => function($query) {
            $query->with('product');
        }, 'customers', 'agents'])->where('id',$id)->first();
        $this->items = []; // reset array

        if ($salesOrders->items->isNotEmpty()) {
            foreach ($salesOrders->items as $item) {
                $priceOptions = [];
                if ($item->product) {
                    $price = $item->product->price;
                    $priceOptions = [
                        $price => $price,
                        $price => $price,
                        $price => $price
                    ];
                }

                $this->items[] = [
                    'id' => $item->id,
                    'product_id' =>  $item->product_id,
                    'quantity' => $item->quantity,
                    'price_option' => $priceOptions,
                    'unit_price' => (float)$item->unit_price
                ];
            }
        }

        if (!$salesOrders) {
            session()->flash('error', 'Sales Order not found.');
            return;
        }

        // dont allow editing if not pending
        if ($salesOrders) {
            if($salesOrders->status !=='pending'){
                $this->resetForms();
                session()->flash('error', 'Editing is disabled — this sales order is no longer in a pending state.');
                return;
            }
        }
       
      

        // Status is now always 'pending' for new orders, so we don't set it from edit
        $this->customerSelected = $salesOrders->customers->pluck('id')->toArray();
        $this->subclassSelected = $salesOrders->customers->pluck('pivot.subclass')->filter()->toArray();
        $this->agentSelected = $salesOrders->agents->pluck('id')->toArray();

        // Update subclass and agent options based on loaded data
        if (!empty($this->customerSelected)) {
            $this->updateSubclassOptions($this->customerSelected);
        }
        if (!empty($this->subclassSelected)) {
            $this->updateAgentOptions($this->subclassSelected);
        }
        $this->shippingMethod = $salesOrders->shipping_method;
        $this->deliveryDate = $salesOrders->delivery_date ? $salesOrders->delivery_date->format('n-j-Y') : '';
           
        $this->editValue = $id;    
        
        //$this->selectedProductId = $salesOrders->product_id; 
        
        $date = explode('-',$this->deliveryDate);  

        $this->month = $date[0];
        $this->day = $date[1];
        $this->year = $date[2];
    }
         
    private function getmonthcalculate($value)
    {      
        if($value ==''){
            $value = date('m');
        }

        $numberOfDays = cal_days_in_month(CAL_GREGORIAN, $value, date('Y') );        
        $values = range(1, $numberOfDays);

        $this->days = array_combine($values, $values);

        $this->year = date('Y');
    }

    public function updatedMonth($value, $name){
        $this->getmonthcalculate($value);      
    }

    public function updatedCustomerSelected($value)
    {
        if (is_array($value) && !empty($value)) {
            // For multi-select, get the first selected branch for auto-population
            $branchId = $value[0];
            $branch = \App\Models\Branch::find($branchId);
            if ($branch) {
                $this->customerData = [
                    'name' => $branch->name,
                    'address' => $branch->address,
                    'contact_num' => $branch->contact_num,
                    'manager_name' => $branch->manager_name,
                ];

                // Auto-populate fields if they're empty
                if (empty($this->contactPersonName)) {
                    $this->contactPersonName = $branch->name;
                }
                if (empty($this->phone)) {
                    $this->phone = $branch->contact_num;
                }
                if (empty($this->billingAddress)) {
                    $this->billingAddress = $branch->address;
                }
                if (empty($this->shippingAddress)) {
                    $this->shippingAddress = $branch->address;
                }
            }

            // Update subclass options based on selected branches
            $this->updateSubclassOptions($value);
        } else {
            $this->customerData = [];
            $this->subclass_results = [];
        }

        // Reset subclass and agent selections when branches change
        $this->subclassSelected = [];
        $this->agentSelected = [];
    }

    public function updatedSubclassSelected($value)
    {
        if (is_array($value) && !empty($value)) {
            $this->updateAgentOptions($value);
            // Auto-select all agents for the selected subclasses
            $this->agentSelected = array_keys($this->agent_results);
        } else {
            $this->agent_results = \App\Models\Agent::all()->pluck('name', 'id')->toArray();
            $this->agentSelected = [];
        }
    }

    private function updateSubclassOptions($branchIds)
    {
        $subclasses = [];
        $branches = \App\Models\Branch::whereIn('id', $branchIds)->get();

        foreach ($branches as $branch) {
            $branchSubclasses = $branch->getSubclasses();
            foreach ($branchSubclasses as $subclass) {
                if (!in_array($subclass, $subclasses)) {
                    $subclasses[] = $subclass;
                }
            }
        }

        $this->subclass_results = array_combine($subclasses, $subclasses);
    }

    private function updateAgentOptions($subclasses)
    {
        $agents = \App\Models\Agent::whereHas('branchAssignments', function($query) use ($subclasses) {
            $query->whereIn('subclass', $subclasses)
                  ->whereNull('released_at');
        })->pluck('name', 'id');

        $this->agent_results = $agents->toArray();
    }

    public function updatedItems($value, $name)
    { 
        $parts = explode('.', $name);
             
        if (count($parts) >= 2) {

            $index = $parts[0];
        
            if($parts[1] =='product_id'){
                $index = $parts[0];
                $items = collect($this->items);
                $item = $items->get($index); // $parts[0] is element key

                if(!empty($item)){
                    $itemProductId = $item['product_id'];
                    $getProducTable = \App\Models\Product::selectRaw('price as price_tier_1, price as price_tier_2, price as price_tier_3')->where('id',$itemProductId)->first();
                    if($getProducTable){
                        $this->items[$index]['unit_price'] = 0;
                        $this->items[$index]['price_option'] = $getProducTable->toArray();
                    }else{
                        $this->items[$index]['price_option'] = [];
                        $this->items[$index]['unit_price'] = 0;
                    }
                }
            }
        } 
    }

    public function validationAttributes()
    {  
        $attributes = [];

        foreach ($this->items as $index => $item) {
            $row = $index + 1;
            $attributes["items.$index.product_id"] = "Please select a product in line $row";
            $attributes["items.$index.quantity"] = "quantity in line $row";
            $attributes["items.$index.unit_price"] = "unit price in line $row";
        }

        return $attributes;
    }
    
    public function submitOrder()
    { 
        // catch error
        try {
            //code...
            $this->validate([
                'customerSelected' => 'required|array|min:1',
                'customerSelected.*' => 'exists:branches,id',
                'subclassSelected' => 'required|array|min:1',
                'agentSelected' => 'nullable|array',
                'agentSelected.*' => 'exists:agents,id',
                'deliveryDate' => 'required|date|after_or_equal:today',
                'shippingMethod' => 'required|string|max:50',
                'items.*.product_id' => ['required', 'exists:products,id', 'distinct'],
                'items.*.quantity' => [
                        'required',
                        'integer',
                        'min:1',
                        function ($attribute, $value, $fail) {
                            $this->callbackValidationcheck($attribute, $value, $fail);                       
                        },
                ],
                'items.*.unit_price' => [  
                        'required',                                                      
                        function ($attribute, $value, $fail) {
                            $this->callbackValidationcheckUnitPrice($attribute, $value, $fail);                       
                        },
                ]
            ]);      
        
            $formattedDeliveryDate = $this->deliveryDate
                ? \Carbon\Carbon::parse($this->deliveryDate)->format('Y-m-d')
                : null;
            

        
            $salesOrderData = [
                'status' => 'pending',
                'shipping_method' => $this->shippingMethod,
                'delivery_date' => $formattedDeliveryDate
            ];

            if($this->editValue) {
                $SalesOrder = SalesOrder::find($this->editValue);
                $success = $SalesOrder->update($salesOrderData);
                // Sync branches and agents for editing - sync handles detach/attach automatically
                $SalesOrder->customers()->sync($this->customerSelected);
                $SalesOrder->agents()->sync($this->agentSelected);

                // Update branch items for editing
                $this->updateBranchItems($SalesOrder);
            }else{
                // Create new Sales Order
                $SalesOrder = SalesOrder::create($salesOrderData);
                // Attach branches and agents for new order
                $SalesOrder->customers()->attach($this->customerSelected);
                $SalesOrder->agents()->attach($this->agentSelected);

                // Create branch items for new order
                $this->createBranchItems($SalesOrder);
            }

            // Sync items
            $existingItemIds = $SalesOrder->items()->pluck('id')->toArray();
            $incomingItemIds = [];

            foreach ($this->items as $itemData) {

                $getProduct = \App\Models\Product::find( $itemData['product_id'] );

                if (isset($itemData['id']) && in_array($itemData['id'], $existingItemIds)) {   

                    // 9 = in salesorderitem
                    // 5 = new incoming value
                    // 3 = database supply_profile quantity
                    // (3 + 9 ) - 5

                    $getOldValuesSalesOrderItem = SalesOrderItem::where('id', $itemData['id'])->first();
                    
                    $oldValueQty  = $getOldValuesSalesOrderItem ->quantity;
                    $newValueQty  = $itemData['quantity'];
                    
                    if($getProduct){
                        $newComputedValue = ( $getProduct->supply_qty +  $oldValueQty ) - $newValueQty;
                        if( $newComputedValue >= 0 ){
                            $getProduct->supply_qty = $newComputedValue;
                            $getProduct->save();
                        }
                    }

                    SalesOrderItem::where('id', $itemData['id'])->update([
                        'product_id' => $itemData['product_id'],
                        'quantity'   => $itemData['quantity'],
                        'unit_price' => (float)$itemData['unit_price'],
                        'subtotal'   => $itemData['quantity'] * $itemData['unit_price'],
                    ]);

                    $incomingItemIds[] = $itemData['id'];

                } else {                  
                    $success = $SalesOrder->items()->create([
                        'product_id' => $itemData['product_id'],
                        'quantity'   => $itemData['quantity'],
                        'unit_price' => (float)$itemData['unit_price'],
                        'subtotal'   => $itemData['quantity'] * $itemData['unit_price'],
                    ]);

                    // temporarily commented out
                    if($success ){                      
                        if($getProduct){
                            if( ( $getProduct->stock_quantity -  $itemData['quantity'] ) >= 0 ){
                                $getProduct->stock_quantity = $getProduct->stock_quantity -  $itemData['quantity'];
                                //$getProduct->save();
                            }
                        }
                    }
                }
            }

            // Delete removed items
            $toDelete = array_diff($existingItemIds, $incomingItemIds);
            SalesOrderItem::destroy($toDelete);

            if($this->editValue) {
                // Update existing Sales Order 
                session()->flash('message', 'Sales Order successfully updated!');
            } else {
                session()->flash('message', 'Sales Order successfully created!');                  
            }
        
            $this->resetForms();

        } catch (\Throwable $th) {
            throw $th;
            //session()->flash('error', 'Something went wrong while saving or updating the record.');
        }  
         
    }

    public function updatingSearch() 
    {
        $this->resetPage(); 
    }
   
    // Auto-decrease stock quantity based on order
    // call this during the loop
    private function recordstockdeduction()
    {
      
        collect($this->items)->each(function ($item, $index) {          
            $getProduct = \App\Models\Product::find($item['product_id']);
            if($getProduct){
                $qtyToDeduct = $item['quantity'];
                
                // Deduct stock from Product model
                $getProduct->stock_quantity = $getProduct->stock_quantity - $qtyToDeduct;
                $getProduct->save();
            }
        });         
    }

    public function close()
    {
        //$this->showCreateModal = false;
        $this->resetForms();       
        $this->resetPage();
    }

    public function closeQrModal()
    {
        $this->showQrModal = false;
        $this->getSalesOrderDetails = null;
    }

    public function showSalesOrdeQrCode($salesOrderNumber)
    {
        $this->showQrModal = true;
        $this->getSalesOrderDetails = SalesOrder::where('sales_order_number',$salesOrderNumber)
            ->with(['items.product','salesReturns.items'])
            ->first();        
    }

    protected function messages()
    {
        return [
            'items.*.product_id.required' => 'Product is required in line :position.', 
            'items.*.product_id.distinct' => 'Product is dudplicate in line :position.',           
        ];
    }

    private function callbackValidationcheckUnitPrice($attribute, $value, $fail)
    {
        
        if (preg_match('/items\.(\d+)\.unit_price/', $attribute, $matches)) {
            $index = (int) $matches[1]; // $index = 0
        }

        if( $value  == 0){
            return $fail('Unit price is required in line '.( $index + 1 ) .'.');
        }else{
            $getkey = $this->collectItems($index);
            $getProducTable = \App\Models\Product::selectRaw('price as price_tier_1, price as price_tier_2, price as price_tier_3')
                ->find($getkey['product_id']);

            if($getProducTable){
                if(!in_array($value,$getProducTable->toArray())){
                    return $fail('Unit price is required in line '.( $index + 1 ) .'.');
                }
            }
        }
    }
    
    private function collectItems($index)
    {
        $collect = collect($this->items);
        return $collect->get($index);
    }

    private function callbackValidationcheck($attribute, $value, $fail)
    {

        if (preg_match('/items\.(\d+)\.quantity/', $attribute, $matches)) {
            $index   = (int) $matches[1];
            $getkey  = $this->collectItems($index);
            $collectResult = collect($getkey);

            if ($collectResult->has('product_id')) {
                $getTotalQty = \App\Models\Product::select('product_inventory.available_quantity as stock_quantity')
                    ->join('product_inventory', 'products.id', '=', 'product_inventory.product_id')
                    ->find($getkey['product_id']);
                if ($getTotalQty) {
                    // Multiply by number of branches since each branch gets the same quantity
                    $totalRequiredQty = $value * count($this->customerSelected);
                    if($totalRequiredQty >  $getTotalQty->stock_quantity ){
                        return $fail('The total quantity required (' . $totalRequiredQty . ') exceeds the remaining stock of ' . $getTotalQty->stock_quantity .'.' );
                    }
                }
            }

            // when editing sales order quantity
            if($this->editValue){
                // compute all returned quantity in sales return items
                $getSalesReturns = SalesReturn::with('items')->where('sales_order_id', $this->editValue)->get();

                $sum = 0;
                if ($getSalesReturns->isNotEmpty()) {
                    foreach ($getSalesReturns as $salesReturn) {
                        $sum += $salesReturn->items->sum('quantity');
                    }
                }

                if($value < $sum ){
                    // dapat malaki pa ang value ng salesorder quantity compare sa sales return quantity
                    return $fail("The quantity cannot be less than the total returned quantity of {$sum}.");
                }
            }
        }

    }

    private function createBranchItems($salesOrder)
    {
        foreach ($this->customerSelected as $branchId) {
            foreach ($this->items as $item) {
                if (!empty($item['product_id'])) {
                    $product = \App\Models\Product::find($item['product_id']);
                    if ($product) {
                        \App\Models\SalesOrderBranchItem::create([
                            'sales_order_id' => $salesOrder->id,
                            'branch_id' => $branchId,
                            'product_id' => $item['product_id'],
                            'original_unit_price' => $item['unit_price'],
                            'unit_price' => $item['unit_price'],
                            'quantity' => $item['quantity'],
                            'subtotal' => $item['quantity'] * $item['unit_price'],
                        ]);
                    }
                }
            }
        }
    }

    private function updateBranchItems($salesOrder)
    {
        // Delete existing branch items
        $salesOrder->branchItems()->delete();

        // Create new branch items
        $this->createBranchItems($salesOrder);
    }

    public function resetForms()
    {
        // Reset form fields to empty/null/default
        $this->reset([
            // 'status', // Status is now always 'pending'
            'shippingMethod',
            'deliveryDate',
            'search', 
            'editValue',           
            'customerSelected',
            'subclassSelected',
            'agentSelected',
           
            'items',
            'days',
            'months',
            'years',
            'customerData'
        ]);
        
        $this->resetValidation(); // Reset validation errors as well
        
        // Reset items to initial state
        $this->items = [
            [   
                'sales_order_item_id' => 0,
                'product_id' => '', 
                'quantity' => 1, 
                'price_option' => [], 
                'unit_price' => 0
            ]
        ];
    }
    
    private function getdays()
    {
        if(empty($this->days)){           
            $values = range(1, 31);
           return array_combine($values, $values);
        }
        return $this->days;
    }

    public function render()
    {           
        $this->months = [
            1 => 'January',
            2 => 'February', 
            3 => 'March',
            4 => 'April', 
            5 => 'May', 
            6 => 'June',
            7 => 'July', 
            8 => 'August', 
            9 => 'September',
            10 => 'October', 
            11 => 'November', 
            12 => 'December',
        ];

        $this->years = [
            1 => '2025',            
        ];

        $this->days = $this->getdays();

        return view('livewire.pages.sales-management.index', [
            'data_results' => SalesOrder::with('customers', 'agents')->search($this->search)
                ->latest()->paginate($this->perPage),
            'months' =>  $this->months,
            'days'=> $this->days,
            'getSalesOrderDetails' => $this->getSalesOrderDetails

        ]);
    }
}