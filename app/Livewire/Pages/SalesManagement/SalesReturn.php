<?php
namespace App\Livewire\Pages\SalesManagement;

use Livewire\Component;
use App\Models\Customer;
use App\Models\SalesOrder;
use App\Models\SupplyProfile;
use App\Models\SalesReturn as SalesReturnModel;
use App\Models\SalesReturnItem;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class SalesReturn extends Component
{
    use WithPagination;
    public $customers;
    public $products;
    public $perPage = 10;
    public $search = "";

    // Sales return master fields
    public $sales_order_id;
    public $customer_id;
    public $return_date = null;
    public $return_reference = '';
    public $status = 'pending';
    public $reason;
    public $total_refund = 0;   
    public $showCreateModal = false;
    public $salesOrders;
    public $editValue = null; // To track if we are editing an existing return
    public $returnItems = [];

    public $hasPendingSalesReturn = false;

    public function mount()
    {
        $this->customers = Customer::all()->pluck('name', 'id');
        $this->salesOrders = SalesOrder::where('status','approved')           
            ->get()->pluck('sales_order_number', 'id');
              
        $this->products  = SupplyProfile::where('supply_qty', '>', 0)->get();
            
        // Initialize one empty item row
        $this->returnItems = [
            [
                'product_id' => '', 
                'supply_sku' => '',
                'quantity' => 1,
                'is_checked' => false,
                'old_quantity' => 0,
                'unit_price' => 0, 
                'total_price' => 0
            ]
        ];
    }

    public function getIsItemIsOneProperty()
    {
        // Example: return true if item quantity is 1
        return (collect($this->returnItems)->count() == 1);
    }

    public function addReturnItem()
    {
        $this->returnItems[] = [
            'product_id' => '',
            'supply_sku' => '',
            'quantity' => 1, 
            'is_checked' => false,
            'old_quantity' => 0,
            'unit_price' => 0, 
            'total_price' => 0
        ];
    }

    public function removeReturnItem($index)
    {
        unset($this->returnItems[$index]);
        $this->returnItems = array_values($this->returnItems);
    }

    public function close(){
        $this->showCreateModal = false;
        $this->resetForms();       
        $this->resetPage();
    }

    public function edit($id)
    {
      
        $salesReturn = SalesReturnModel::with(['items', 'salesOrder.items'])->find($id);       

        if($salesReturn){
            if($salesReturn->status !=='pending'){
                 session()->flash('error', 'Heads up! This sales return can no longer be edited because it\'s not pending.');
                 return;
            }
        }

        $this->sales_order_id = $salesReturn->sales_order_id;
        $this->customer_id = $salesReturn->customer_id;
        $this->return_date = $salesReturn->return_date;
        $this->return_reference = $salesReturn->return_reference;
        $this->status = $salesReturn->status;
        $this->reason = $salesReturn->reason;
        $this->total_refund = $salesReturn->total_refund;
        $this->editValue = $id;
        $this->showCreateModal = true;

        $collectSalesOrderitems = collect($salesReturn->salesOrder->items);         
       
        $this->returnItems = $salesReturn->items->map(function ($item) use ($collectSalesOrderitems) {                
            $resultProductQty = $collectSalesOrderitems->firstWhere('product_id', $item->product_id);          

            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'supply_sku' => $item->product->supply_sku ?? '',
                'quantity' => $item->quantity,
                'old_quantity' => $resultProductQty['quantity'],
                'is_checked' => true,
                'unit_price' => $item->unit_price,
                'total_price' => $item->total_price,
            ];
        })->toArray();

        $currentCollection = collect($this->returnItems);
        $currentProductIds = $currentCollection->pluck('product_id');

        $filteredProductNotInCurrentList = $collectSalesOrderitems->whereNotIn('product_id', $currentProductIds);

        $addUnSelectedItem = $filteredProductNotInCurrentList->map(function ($item) use ($collectSalesOrderitems) {                
            $resultProductQty = $collectSalesOrderitems->firstWhere('product_id', $item->product_id);  
            return [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'supply_sku' => $item->product->supply_sku ?? '',
                'quantity' => $item->quantity,
                'old_quantity' => $resultProductQty['quantity'],
                'is_checked' => false,
                'unit_price' => $item->unit_price,
                'total_price' => $item->total_price,
            ];
        });


        $this->returnItems = $currentCollection->merge($addUnSelectedItem)->toArray();
    }

    public function resetForms()
    {
        $this->reset('sales_order_id', 
                    'customer_id', 
                    'return_date',
                    'return_reference',
                    'status',
                    'editValue',
                    'reason',
                    'total_refund',
                    'returnItems'
                ); 

        $this->resetValidation(); // Reset validation errors as well      

    }

    public function updatingSearch() 
    {
        $this->resetPage(); 
    }

    public function updatedSalesOrderId($value)
    {
        $this->reset([
            'customer_id',          
            'return_reference', 
            'status',           
            'total_refund', 
            'returnItems'
        ]);
        
        if ($value) {
          
            $salesOrder = SalesOrder::with([
                            'items.product',
                            'salesReturns' => function ($q) {
                                $q->where('status', 'pending');
                            }
                        ])->where('id', $value)->first();         
            
           
            if ($salesOrder && $salesOrder->salesReturns->isNotEmpty()) {
                $this->hasPendingSalesReturn = true;
                session()->flash('errorCreate', 'There are pending sales returns for this sales order. Please select a different one.');
                return;
            }
            
            if($salesOrder){
                $this->customer_id = $salesOrder->customer_id;             
                $this->return_date = now()->toDateString();
                $this->return_reference = 'RR-' . strtoupper(uniqid());                
                
                if($salesOrder->items->isNotEmpty()){
                    $object = [];

                    foreach($salesOrder->items as $item){
                        $object[] = [
                                'product_id' => $item->product_id, 
                                'quantity'   => $item->quantity,
                                'old_quantity' => $item->quantity,
                                'is_checked' => true,
                                'unit_price' => $item->unit_price,
                                'supply_sku' => $item->product->supply_sku ?? '',
                                'total_price'=> $item->unit_price * $item->quantity                            
                        ];
                    }

                    $this->returnItems = $object;
                    $this->recalculateTotalRefund();
                }
            }           
        }
    }

    public function updatedReturnItems($value, $name)
    {
        $parts = explode('.', $name);        
       
        if (count($parts) >= 2) {
            $index = $parts[0];

            $productId = $this->returnItems[$index]['product_id'] ?? null;
            $quantity = $this->returnItems[$index]['quantity'] ?? 1;

            $product = $this->products->firstWhere('id', $productId);
                      
            if ($product) {
                $unitPrice = $product->supply_price1;
                $this->returnItems[$index]['unit_price'] = $unitPrice;
                $this->returnItems[$index]['total_price'] = $unitPrice * max(1, (int) $quantity);
            }
        }
        $this->recalculateTotalRefund();
    }

    public function recalculateTotalRefund()
    {        
        $selectedItems = collect($this->returnItems)->filter(fn($item) => $item['is_checked']);
        $selectedArray = $selectedItems->values()->all();        

        $this->total_refund = collect($selectedArray)->sum(fn($item) => $item['total_price'] ?? 0);
    }

    public function submit()
    {      
        try {
            $this->validate([
                'sales_order_id' => 'required|exists:sales_orders,id',               
                'return_date' => 'required|date',
                'return_reference' => 'required|string|max:50',
                'status' => ['required', Rule::in(['pending', 'approved', 'rejected'])],
                'reason' => 'required|string|max:500',
                'returnItems' => 'required|array|min:1',
                'returnItems.*.product_id' => 'required|exists:supply_profiles,id',              
                'returnItems.*.quantity' => [
                    'required',
                    'integer',
                            function ($attribute, $value, $fail) {
                                $this->callbackValidationcheck($attribute, $value, $fail);                       
                            }                            
                            ,
                ],
            ]);       
        } catch (\Illuminate\Validation\ValidationException $e) {
            session()->flash('error_update', $e->validator->errors());            
        }                       
        
        try {
             DB::transaction(function () {
                if($this->editValue){
                   $salesReturn = SalesReturnModel::where('id', $this->editValue)->first();
                   
                    if($salesReturn){                        
                        $salesReturn->customer_id = $this->customer_id;
                        $salesReturn->return_date = $this->return_date;
                        $salesReturn->return_reference = $this->return_reference;
                        $salesReturn->status = $this->status;
                        $salesReturn->reason = $this->reason;
                        $salesReturn->total_refund = $this->total_refund;
                        $salesReturn->processed_by = Auth::id();
                        $salesReturn->save();
                    }

                }else{
                    $salesReturn = SalesReturnModel::create([
                        'sales_order_id' => $this->sales_order_id,
                        'customer_id' => $this->customer_id,
                        'return_date' => $this->return_date,
                        'return_reference' => $this->return_reference,
                        'status' => $this->status,
                        'reason' => $this->reason,
                        'total_refund' => $this->total_refund,
                        'processed_by' => Auth::id(),
                    ]);   
                }             

                $existingItemIds = $salesReturn->items()->pluck('id')->toArray();
                $incomingItemIds = [];

                foreach ($this->returnItems as $item) {
                    //$product = SupplyProfile::find($item['product_id']);

                    if($item['is_checked']){
                        $dataItems = [
                            'sales_return_id' => $salesReturn->id,
                            'product_id' => $item['product_id'],
                            'quantity' => $item['quantity'],
                            'unit_price' => $item['unit_price'],
                            'total_price' => $item['total_price'],
                        ];                        

                        if (isset($item['id']) && in_array($item['id'], $existingItemIds)) {

                            SalesReturnItem::where('id', $item['id'])->update($dataItems);
                            $incomingItemIds[] = $item['id'];

                        }else{
                            SalesReturnItem::create($dataItems);      
                        }
                    }

                }

                $toDelete = array_diff($existingItemIds, $incomingItemIds);
                SalesReturnItem::destroy($toDelete);

                // foreach ($this->returnItems as $item) {  
                //     // restocking
                //     $product = SupplyProfile::find($item['product_id']);
                    
                //     if ($product) {
                //         $product->increment('supply_qty', $item['quantity']);
                //     }

                //     SalesReturnItem::create([
                //         'sales_return_id' => $salesReturn->id,
                //         'product_id' => $item['product_id'],
                //         'quantity' => $item['quantity'],
                //         'unit_price' => $item['unit_price'],
                //         'total_price' => $item['total_price'],
                //     ]);                
                // }

                $items = collect($this->returnItems);
                $checkedItems = $items->filter(function($item) {
                    return $item['is_checked'] === true;
                });                
                
                // add is full return 
                $totalOrderQty = collect($this->returnItems)->sum('old_quantity');
                $returnedQty = $checkedItems->sum('quantity');
                
                $isFullReturn = ($returnedQty == $totalOrderQty) ? 1 : 0;                           
                $salesReturn->is_full_return = $isFullReturn;
                $salesReturn->save();  
            });

        } catch (\Throwable $th) { 
           // $th;          
            session()->flash('error', 'Something went wrong.');
        }       

        session()->flash('message', 'Sales return saved successfully!');
        return redirect()->route('salesorder.return');
    }

    private function callbackValidationcheck($attribute, $value, $fail)
    {
        if (preg_match('/returnItems\.(\d+)\.quantity/', $attribute, $matches)) {
            $index = (int) $matches[1]; // $index = 0
            
            $collect = collect($this->returnItems);
            $getkey = $collect->get($index);      

            if( $getkey['quantity'] > $getkey['old_quantity'] ){
                //return $fail("The returned quantity exceeds the quantity in the sales order.");
                return $fail('The quantity in line '. ( $index + 1) .' exceeds the quantity in the sales order.');
            }
        } 
    }

    public function render()
    {
        $salesReturns = SalesReturnModel::with('salesOrder.customer')->search($this->search)
            ->paginate($this->perPage);
        
        return view('livewire.pages.sales-management.sales-return',[
            'salesReturns' => $salesReturns,
        ]);
    }
}