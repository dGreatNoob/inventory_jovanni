<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use Illuminate\Http\Request;

class PurchaseOrderQRController extends Controller
{
    public function show($Id)
    {
        // ✅ Use correct relationships based on your models
        $purchaseOrder = PurchaseOrder::with([
            'supplier',
            'department',
            'productOrders.product', // each ProductOrder belongsTo Product
        ])->findOrFail($Id);

        // ✅ Return correct Blade path
        return view('livewire.pages.POmanagement.purchaseorderQR', compact('purchaseOrder'));
    }
}
