<?php

namespace App\Observers;

use App\Models\SalesOrderItem;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
class SalesOrderItemObserver
{ 
    // Temporarily commented out — not in use.
    // public function updating(SalesOrderItem $SalesOrderItemitem)
    // {
    //     if ($SalesOrderItemitem->isDirty('unit_price')) {
           
    //         $getSalesReturnItems = SalesReturn::with('items')->where('sales_order_id',$SalesOrderItemitem->sales_order_id)->get();

    //         if ($getSalesReturnItems->items->isNotEmpty()) {

    //             foreach ($getSalesReturnItems->items as $item) { 
                        
    //                 $updateSaleReturn = SalesReturn::with('items')->find($item->sales_return_id);

    //                 if ($updateSaleReturn) {
    //                     $updateSaleReturn->total_refund = ($item->unit_price * $item->quantity);
    //                     $updateSaleReturn->save();

    //                     foreach ($updateSaleReturn->items as $items) {   // sa salesreturniems  

    //                         // if the same product the update
    //                         if( $SalesOrderItemitem->product_id == $items->product_id ) {                    
    //                             $items->unit_price = $SalesOrderItemitem->unit_price;
    //                             $items->total_price= $SalesOrderItemitem->quantity * $SalesOrderItemitem->unit_price;                            
    //                             $items->save();
    //                         }
    //                     }
    //                 }
    //             } 
    //         }
    //     }
    // }
}   
