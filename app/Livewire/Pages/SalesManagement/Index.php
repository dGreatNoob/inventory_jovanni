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



    public $status = '';    
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
    public $customerSelected = null;
         
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
        
        $thisProducList = SupplyProfile::select('id','supply_description','supply_sku','supply_qty','supply_uom')
            ->where('supply_qty', '>', 0) // Only show products with stock
            ->orderBy('supply_description')
            ->get();
              
        if($thisProducList->isNotEmpty()){
            $this->product_list = $thisProducList->pluck('supply_description','id')->toArray();
        }
        
        $this->company_results = \App\Models\Branch::all()->pluck('name', 'id');
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
        $salesOrders = SalesOrder::with('items.product')->where('id',$id)->first();
        $this->items = []; // reset array

        if ($salesOrders->items->isNotEmpty()) {
            foreach ($salesOrders->items as $item) {
                $this->items[] = [
                    'id' => $item->id,
                    'product_id' =>  $item->product_id, 
                    'quantity' => $item->quantity,  
                    'price_option' => [ 
                        $item->product->supply_price1 => $item->product->supply_price1,
                        $item->product->supply_price2 => $item->product->supply_price2,
                        $item->product->supply_price3 => $item->product->supply_price3
                    ], 
                    'unit_price' => (string)$item->unit_price
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
       
      

        $this->status = $salesOrders->status;       
        $this->customerSelected = $salesOrders->customer_id; 
        $this->contactPersonName = $salesOrders->contact_person_name;
        $this->phone = $salesOrders->phone;  
        $this->email = $salesOrders->email;
        $this->billingAddress = $salesOrders->billing_address;
        $this->shippingAddress = $salesOrders->shipping_address;
        
        //$this->discounts = $salesOrders->discounts;
        $this->paymentMethod = $salesOrders->payment_method;
        $this->shippingMethod = $salesOrders->shipping_method;
        $this->paymentTerms = $salesOrders->payment_terms;
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
        if ($value) {
            $branch = \App\Models\Branch::find($value);
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
        } else {
            $this->customerData = [];
        }
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
                    $getProducTable = SupplyProfile::select('supply_price1','supply_price2','supply_price3')->where('id',$itemProductId)->first();
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
                'status' => ['required', 'string', Rule::in(['pending','approved','rejected','confirmed','processing','shipped','delivered','cancelled','returned','on hold'])],        
                'customerSelected' => 'required|exists:branches,id',
                'contactPersonName' => 'nullable|string|max:255',
                'phone' => 'required|digits_between:7,15',
                'email' => 'required|email|max:150',
                'billingAddress' => 'required|string',
                'shippingAddress' => 'required|string',
                'deliveryDate' => 'required|date|after_or_equal:today',
                'paymentMethod' => 'required|string|max:50',
                'shippingMethod' => 'required|string|max:50',
                'paymentTerms' => 'required|string|max:100',          
                'items.*.product_id' => ['required', 'exists:supply_profiles,id', 'distinct'],
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
                'status' => $this->status,
                'customer_id' => $this->customerSelected,               
                'contact_person_name' => $this->contactPersonName,
                'phone' => $this->phone,
                'email' => $this->email,
                'billing_address' => $this->billingAddress,
                'shipping_address' => $this->shippingAddress,
                //'discounts' => $this->discounts,
                'payment_method' => $this->paymentMethod,
                'shipping_method' => $this->shippingMethod,
                'payment_terms' => $this->paymentTerms,
                'delivery_date' => $formattedDeliveryDate                
            ];  

            if($this->editValue) {
                $SalesOrder = SalesOrder::find($this->editValue);
                $success = $SalesOrder->update($salesOrderData);                        
            }else{
                // Create new Sales Order
                $SalesOrder = SalesOrder::create($salesOrderData); 
            }

            // Sync items
            $existingItemIds = $SalesOrder->items()->pluck('id')->toArray();
            $incomingItemIds = [];

            foreach ($this->items as $itemData) {

                $getProduct = SupplyProfile::find( $itemData['product_id'] );

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
                        'unit_price' => $itemData['unit_price'],
                        'subtotal'   => $itemData['quantity'] * $itemData['unit_price'],
                    ]);

                    $incomingItemIds[] = $itemData['id'];

                } else {                  
                    $success = $SalesOrder->items()->create([
                        'product_id' => $itemData['product_id'],
                        'quantity'   => $itemData['quantity'],
                        'unit_price' => $itemData['unit_price'],
                        'subtotal'   => $itemData['quantity'] * $itemData['unit_price'],
                    ]);

                    // temporarily commented out
                    if($success ){                      
                        if($getProduct){
                            if( ( $getProduct->supply_qty -  $itemData['quantity'] ) >= 0 ){
                                $getProduct->supply_qty = $getProduct->supply_qty -  $itemData['quantity'];
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
            $getProduct = SupplyProfile::find($item['product_id']);
            if($getProduct){
                $qtyToDeduct = $item['quantity'];
                
                // For consumable items, use FIFO batch deduction
                if ($getProduct->isConsumable()) {
                    $batchDeduction = SupplyBatch::getBatchesForFifoDeduction($getProduct->id, $qtyToDeduct);
                    
                    if ($batchDeduction['shortage'] > 0) {
                        // Not enough stock in batches
                        session()->flash('error', "Insufficient stock for {$getProduct->supply_description}. Available: {$batchDeduction['total_available']}, Requested: {$qtyToDeduct}");
                        return;
                    }
                    
                    // Deduct from batches using FIFO
                    foreach ($batchDeduction['batches'] as $batchInfo) {
                        $batch = $batchInfo['batch'];
                        $qtyFromBatch = $batchInfo['quantity'];
                        
                        $batch->deductQuantity($qtyFromBatch);
                        
                        // Log batch deduction
                        activity('Stock-out')
                            ->causedBy(\Illuminate\Support\Facades\Auth::user())
                            ->performedOn($batch)
                            ->withProperties([
                                'product_sku' => $getProduct->supply_sku,
                                'batch_number' => $batch->batch_number,
                                'qty_deducted' => $qtyFromBatch,
                                'remaining_batch_qty' => $batch->current_qty,
                                'sales_order' => $this->salesOrderNumber ?? 'Direct Sale',
                            ])
                            ->log("FIFO deduction from batch {$batch->batch_number}: {$qtyFromBatch} units");
                    }
                } else {
                    // For non-consumable items, use the original logic
                    $getProduct->supply_qty = $getProduct->supply_qty - $qtyToDeduct;
                    $getProduct->save();
                }
                
                // Update the main supply profile quantity in both cases
                $getProduct->supply_qty = $getProduct->supply_qty - $qtyToDeduct;
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
            $getProducTable = SupplyProfile::select('supply_price1','supply_price2','supply_price3')
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
                $getTotalQty = SupplyProfile::select('supply_qty')->find($getkey['product_id']);
                if ($getTotalQty) {
                    if($getkey['quantity'] >  $getTotalQty->supply_qty ){
                        return $fail('The quantity in line'. ( $index + 1) .' exceeds the remaining quantity of ' . $getTotalQty->supply_qty .'.' );
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

    public function resetForms()
    {
        // Reset form fields to empty/null/default
        $this->reset([
            'status',            
            'contactPersonName',  
            'phone', 
            'email', 
            'billingAddress', 
            'shippingAddress', 
            'paymentMethod',
            'shippingMethod', 
            'paymentTerms', 
            'deliveryDate',
            'search', 
            'editValue',           
            'customerSelected',
           
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
            'data_results' => SalesOrder::with('customer')->search($this->search)
                ->latest()->paginate($this->perPage), 
            'months' =>  $this->months,
            'days'=> $this->days,
            'getSalesOrderDetails' => $this->getSalesOrderDetails

        ]); 
    }
}