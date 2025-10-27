<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\SalesOrderItem;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Facades\Auth;
class SalesOrder extends Model
{
    use HasFactory, LogsActivity;
    protected $fillable = [
        'status',
        'customer_id',
        'product_id',
        'contact_person_name',
        'phone',
        'email',
        'billing_address',
        'shipping_address',
        'product',
        'product_code',
        'quantity',
        'unit_price',
        'discounts',
        'payment_method',
        'shipping_method',
        'payment_terms',
        'delivery_date',
    ];

    protected $casts = [
        'delivery_date' => 'date',
    ];

    // Static method to access dropdown options
    public static function shippingMethodDropDown()
    {
        return [
            'standard' => 'Standard Delivery',
            'express' => 'Express Delivery',
            'same_day' => 'Same-Day Delivery',
            'next_day' => 'Next-Day Delivery',
            'pick_up' => 'Pick Up',
            'lalamove' => 'Lalamove',
            'grab' => 'Grab Express',
            'jnt' => 'J&T Express',
            'jrs' => 'JRS Express',
            'lbc' => 'LBC',
            'ninjavan' => 'Ninja Van',
            'gogo' => 'GoGo Xpress',
            'air' => 'Air Freight',
            'sea' => 'Sea Freight',
            'courier' => 'Courier Service',
            '3pl' => 'Third-Party Logistics (3PL)',
        ];
    }

    public static function paymentMethodDropdown(){
        return [
            'cash' => 'Cash',
            'gcash' => 'GCash',
            'paymaya' => 'PayMaya',
            'bank_transfer' => 'Bank Transfer',
            'credit_card' => 'Credit Card',
            'debit_card' => 'Debit Card',
            'cheque' => 'Cheque',
            'cod' => 'Cash on Delivery',
            'online_banking' => 'Online Banking',
            'otc_payment' => 'Over-the-Counter Payment',
            'installment' => 'Installment',
            'qr_payment' => 'QR Payment',
            'remittance' => 'Remittance Center',
        ]; 
    }

    public static function  paymentTermsDropdown(){
        return [
            'cod' => 'Cash on Delivery (COD)',
            'cia' => 'Cash in Advance (CIA)',
            'net_15' => 'Net 15',
            'net_30' => 'Net 30',
            'net_60' => 'Net 60',
            'eom' => 'End of Month (EOM)',
            'two_ten_net_30' => '2/10 Net 30',
            'pia' => 'Payment in Advance (PIA)',
            'partial_payment' => 'Partial Payment',
        ];
    }

    protected static function boot()
    {
        parent::boot();       
        static::creating(function ($salesOrder) {            
            $salesOrder->sales_order_number = 'SO-' . strtoupper(uniqid());
        });   
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('sales_order_number', 'like', "%{$search}%")            
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhere('shipping_address', 'like', "%{$search}%")
            ->orWhere('shipping_method', 'like', "%{$search}%");
    }

    public function salesReturns()
    {        
        return $this->hasMany(SalesReturn::class, 'sales_order_id');
    }

    public function customer()
    {
        return $this->belongsTo(Branch::class, 'customer_id');
    }

    public function product()
    {
        return $this->belongsTo(SupplyProfile::class, 'product_id');
    }

    public function items()
    {
        return $this->hasMany(SalesOrderItem::class, 'sales_order_id');
    }

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function getActivitylogOptions(): LogOptions
    {       
        return LogOptions::defaults()
            ->logOnly($this->fillable)
            ->useLogName('SalesOrder')
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
                
                $getBranch = \App\Models\Branch::find($this->customer_id);
                $customerName = '';

                if($getBranch){
                    $customerName = $getBranch->name;
                }
                
                $fields =  [                 
                    'status' => $this->status ?? 'N/A',
                    'customer_id' => $customerName ?? 'N/A',                
                    'contact_person_name' => $this->contact_person_name ?? 'N/A',
                    'phone'=> $this->phone ?? 'N/A',
                    'email'=> $this->email ?? 'N/A',
                    'billing_address'=> $this->billing_address ?? 'N/A',
                    'shipping_address'=> $this->shipping_address ?? 'N/A',
                    'payment_method'=> $this->payment_method ?? 'N/A',
                    'shipping_method'=> $this->shipping_method ?? 'N/A',
                    'payment_terms'=> $this->payment_terms ?? 'N/A',
                    'delivery_date'=> $this->delivery_date ?? 'N/A',
                    //'items' => $summary ?? 'None',
                ];
                       
                 return collect($fields)
                    ->map(fn($v, $k) => "<strong>$k</strong>: $v")
                    ->implode('<br>');
            
            });
    }
}
