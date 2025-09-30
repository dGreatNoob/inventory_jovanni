<?php

namespace App\Livewire\Pages\Shipment;

use App\Models\SalesOrder;
use App\Models\Shipment;
use App\Models\StockMovement;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
class Index extends Component
{
    use WithPagination; 
    
    public $shippingPriorityDropdown = [
        'same-day'=> 'Same Day', // For deliveries within the same day
        'next-day'=> 'Next Day', // Promises delivery on the following day
        'normal'  => 'Normal',   // Default, regular delivery  
        'scheduled'=>'Scheduled' , //Customer chooses a specific delivery date/time
        'backorder' => 'Backorder', // Delivery delayed until stock is available
        'rush'    => 'Rush', // Prioritized processing and delivery
        'express' => 'Express', // Express – Fastest available delivery
    ];

    public $showQrModal = false;
    public $getShipmentDetails = null;
    public $perPage = 10;
    public $salesOrders;
    public $shipping_plan_num = '';
    public $sales_order_id;   
    public $customer_name;
    public $customer_address;
    public $scheduled_ship_date;
    public $delivery_method;
    public $carrier_name;
    public $vehicle_plate_number;
    public $shipping_priority = '';
    public $special_handling_notes;
    public $filterStatus = '';
    public $search = '';
    public $salesOrderResults = [];
    public $phone = '';
    public $email = '';
    public $editValue = null;
    public $statusFilter = '';
        
    public $deliveryMethods = [
        'courier', 
        'pickup', 
        'truck', 
        'motorbike', 
        'in-house', 
        'cargo'
    ];


    public function updatingSearch() { $this->resetPage(); }
    public function updatingFilterStatus() { $this->resetPage(); }

    public function mount()
    {
        $this->deliveryMethods = Shipment::deliveryMethodDropDown();

        // Get all sales orders marked as 'released' (after stock-out), including customer info
        $this->salesOrders = SalesOrder::where('status', 'released')->with('customer')->get();
    }
    
    public function edit($id){
        
        $results = Shipment::with('customer')->find($id);

        if($results){
            if($results->shipping_status == 'pending'){
                $this->sales_order_id = $results->sales_order_id;
                $this->scheduled_ship_date = $results->scheduled_ship_date;
                $this->carrier_name = $results->carrier_name;
                $this->vehicle_plate_number = $results->vehicle_plate_number;
                $this->special_handling_notes = $results->special_handling_notes;
                $this->customer_name = $results->customer_name;
                $this->customer_address = $results->customer_address;
                $this->phone = $results->customer_phone;
                $this->email = $results->customer_email;
                $this->delivery_method = $results->delivery_method;
                $this->shipping_priority = $results->shipping_priority;                
                $this->editValue = $id;   
            }else{
                session()->flash('error', 'You can only edit shipments that are in pending status.');
            }
        }        
    }

    public function showShipmentQrCode($shipping_plan_num)
    {
        $this->showQrModal = true;
        $this->getShipmentDetails = Shipment::where('shipping_plan_num',$shipping_plan_num)
            ->with(['salesOrder.items.product'])
            ->first();        
    }

    public function updatedStatusFilter($value){
        $this->statusFilter = $value;
    }
 
    public function updatedSalesOrderId($value)
    {
        $order = SalesOrder::with('customer')->find($value);

        if ($order) {
            $this->customer_name = $order->customer->name ?? '';
            $this->customer_address = $order->customer->address ?? '';
            $this->phone = $order->customer->contact_num ?? '';           
        } else {
            $this->customer_name = '';
            $this->customer_address = '';
            $this->phone = '';
        }
    }

    public function createShipment()
    {
        $this->validate([
            'sales_order_id' => 'required|exists:sales_orders,id',
            'scheduled_ship_date' => [
                'required',
                'date',
                function ($attribute, $value, $fail) {
                    if (strtotime($value) < strtotime('today')) {
                        $fail('The scheduled ship date cannot be in the past.');
                    }
                },
            ],
            'delivery_method' => 'required|string|max:255',
            'carrier_name' => 'required|nullable|string|max:255',
            'vehicle_plate_number' => 'nullable|string|max:255',
            'shipping_priority' => ['required', Rule::in(['normal', 'rush', 'express','scheduled','backorder','next-day','same-day'])],            
            'customer_name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'email' => 'string|max:255',
            'customer_address' => 'required|string|max:255',
        ]);

        DB::beginTransaction();

        try {
            if($this->editValue == null){
                Shipment::create([
                    'sales_order_id' => $this->sales_order_id,                   
                    'customer_id' => SalesOrder::find($this->sales_order_id)->customer->id,
                    'customer_name' => $this->customer_name,
                    'customer_address' => $this->customer_address,
                    'scheduled_ship_date' => $this->scheduled_ship_date,
                    'delivery_method' => $this->delivery_method,
                    'carrier_name' => $this->carrier_name,
                    'vehicle_plate_number' => $this->vehicle_plate_number,
                    'shipping_priority' => $this->shipping_priority,
                    'special_handling_notes' => $this->special_handling_notes,  
                    'customer_email' => $this->email,            
                    'customer_phone' => $this->phone,                                      
                ]);
            }else{

                $Shipment = Shipment::find($this->editValue);
                $ShipmentData = [
                    'sales_order_id' => $this->sales_order_id,                   
                    'customer_id' => SalesOrder::find($this->sales_order_id)->customer->id,
                    'customer_name' => $this->customer_name,
                    'customer_address' => $this->customer_address,
                    'scheduled_ship_date' => $this->scheduled_ship_date,
                    'delivery_method' => $this->delivery_method,
                    'carrier_name' => $this->carrier_name,
                    'vehicle_plate_number' => $this->vehicle_plate_number,
                    'shipping_priority' => $this->shipping_priority,
                    'special_handling_notes' => $this->special_handling_notes,
                    'customer_email' => $this->email,            
                    'customer_phone' => $this->phone                    
                ];
             
                $Shipment->update($ShipmentData);

            }

            DB::commit();

            session()->flash('success', 'Shipment created successfully.');
            $this->resetForm();

        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('shipment_error', $e->getMessage());
        }
    }

    public function closeQrModal()
    {
        $this->showQrModal = false;
        $this->getShipmentDetails = null;
    }

    protected function resetForm()
    {
        $this->reset([
            'sales_order_id',
            'customer_name',
            'customer_address',
            'scheduled_ship_date',
            'delivery_method',
            'carrier_name',
            'vehicle_plate_number',
            'shipping_priority',
            'special_handling_notes',
            'editValue'
        ]);
    }

    public function markAsShipped($id)
    {
        $shipment = Shipment::findOrFail($id);
        $shipment->update([
            'shipping_status' => 'shipped',
            'shipped_at' => now(),
        ]);
    }

    public function markAsDelivered($id)
    {
        $shipment = Shipment::findOrFail($id);
        $shipment->update([
            'shipping_status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }

    public function render()
    {
        $getShipmentDetails = SalesOrder::with('shipments')
            ->where('status', 'released')
            ->doesntHave('shipments') // exclude orders that already have shipments
            ->orderBy('created_at', 'desc')
            ->get()
            ->pluck('sales_order_number', 'id');

        $query = Shipment::with('customer');

        return view('livewire.pages.shipment.index', [
            'shipments' => $query->search( $this->search )->filterStatus($this->statusFilter)
                ->latest()
                ->paginate($this->perPage),
            'salesorder_results' => $getShipmentDetails
        ]);
    }
}