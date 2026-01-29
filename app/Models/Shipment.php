<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Shipment extends Model
{
    use HasFactory, LogsActivity;
    
    protected $fillable = [
        'customer_id',
        'shipping_plan_num',
        'sales_order_id',
        'batch_allocation_id',
        'branch_allocation_id',
        'delivery_receipt_id',
        'delivery_method',
        'carrier_name',
        'vehicle_plate_number',
        'shipping_priority',
        'special_handling_notes',
        'shipping_status',
        'scheduled_ship_date'
    ];

    public static function deliveryMethodDropDown(){
        return [
            'courier' => 'courier' , 
            'pickup' => 'pickup', 
            'truck' => 'truck', 
            'motorbike' => 'motorbike', 
            'in-house' => 'in-house', 
            'cargo' =>'cargo'
        ]; 
    }
   
     // Scope for filtering by status
    public function scopeFilterStatus($query, $status = null) 
    {
        if (! $status) return $query;

        return $query->where('shipping_status', $status);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('shipping_plan_num', 'like', "%{$search}%")
            ->orWhere('delivery_method', 'like', "%{$search}%")
            ->orWhere('vehicle_plate_number', 'like', "%{$search}%")
            ->orWhere('shipping_priority', 'like', "%{$search}%")
            ->orWhere('carrier_name', 'like', "%{$search}%");
    }

    protected static function booted()
    {
        static::creating(function ($shipment) {
            $date = now()->format('Ymd');
            $latest = self::count() + 1;
            $shipment->shipping_plan_num = 'SHIP-' . $date . '-' . str_pad($latest, 3, '0', STR_PAD_LEFT);
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    public function salesOrder()
    {
        return $this->belongsTo(SalesOrder::class);
    }

    public function batchAllocation()
    {
        return $this->belongsTo(BatchAllocation::class);
    }

    public function branchAllocation()
    {
        return $this->belongsTo(BranchAllocation::class);
    }

    public function deliveryReceipt()
    {
        return $this->belongsTo(DeliveryReceipt::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(ShipmentStatusLog::class);
    }

     public function getActivitylogOptions(): LogOptions
    {       
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->useLogName('Shipment')
            ->logOnlyDirty()
            ->setDescriptionForEvent(function(string $eventName){
                   
                if ($eventName === 'updated') {
                    $changes = collect($this->getChanges())->only($this->fillable);
                    $original = collect($this->getOriginal())->only($changes->keys());

                    return $changes->map(function($new, $field) use ($original) {
                            $old = $original[$field] ?? 'N/A';                           
                            return ucfirst(str_replace('_', ' ', $field)) . ": {$old} → {$new}";
                        })->implode('<br>');
                }
                
                $getCustomer = \App\Models\Customer::find($this->customer_id);
                $customerName = '';
                
                if($getCustomer){
                    $customerName = $getCustomer->name;
                }     
                
                $approver_id = \App\Models\User::find($this->approver_id);
                $Username = '';
                
                if($approver_id){
                    $Username = $approver_id->name;
                }    
                
                $fields =  [
                    'shipping_status' => $this->shipping_status ?? 'Pending',
                    'scheduled_ship_date'=> $this->scheduled_ship_date ?? 'N/A',
                    'customer_id' => $customerName ?? 'N/A',
                    'shipping_plan_num' => $this->shipping_plan_num ?? 'N/A',
                    'delivery_method'=> $this->delivery_method ?? 'N/A',
                    'carrier_name'=> $this->carrier_name ?? 'N/A',
                    'vehicle_plate_number'=> $this->vehicle_plate_number ?? 'N/A',
                    'shipping_priority'=> $this->shipping_priority ?? 'N/A',
                    'special_handling_notes'=> $this->special_handling_notes ?? 'N/A',
                    'approver_id'=> $Username ?? 'N/A',
                    'updated_at' => $this->updated_at ?? 'N/A',
                ];
                       
                 return collect($fields)
                    ->map(fn($v, $k) => "<strong>$k</strong>: $v")
                    ->implode('<br>');
            
            });
    }

}
