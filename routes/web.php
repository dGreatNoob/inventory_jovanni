<?php

use App\Livewire\Purchase;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;
use App\Livewire\Pages\Warehousestaff\StockIn\Index as StockIn;
use App\Livewire\Pages\Warehousestaff\StockIn\ExpectedStockIn as ExpectedStockIn;




use App\Livewire\Pages\SupplierManagement\Profile\Index as SupplierProfile;
use App\Livewire\Pages\SupplierManagement\Profile\View as SupplierProfileView;
use App\Livewire\Pages\Customer\Index as CustomerProfile;
use App\Livewire\Pages\Agent\Index as AgentProfile;
use App\Livewire\Pages\Branch\Index as BranchProfile;

use App\Livewire\Pages\Notifications\Index as Notifications;

// Allocation Management
use App\Livewire\Pages\Allocation\Warehouse;
use App\Livewire\Pages\Allocation\Sales;
use App\Livewire\Pages\Allocation\Scanning;
use App\Livewire\Pages\Allocation\ForDispatchDrList;
use App\Livewire\Pages\Allocation\SummaryDrView;

// Product Management
use App\Livewire\Pages\ProductManagement\Index as ProductManagement;
use App\Livewire\Pages\ProductManagement\CategoryManagement;



use App\Livewire\Pages\User\Index as UserIndex;
use App\Livewire\Pages\RolePermission\Index as RolePermissionIndex;
//QR CODE PRINTING
use App\Livewire\ActivityLogs;
use App\Livewire\UserLogs;

use App\Livewire\Pages\SalesManagement\Index as SalesManagementIndex;
use App\Livewire\Pages\SalesManagement\View as Viewsalesorder;
use App\Livewire\Pages\SalesManagement\SalesPromo as SalesManagementPromo;
use App\Livewire\Pages\SalesManagement\PromoView;
use App\Livewire\SalesManagement\SalesReturn;



use App\Livewire\Pages\Shipment\Index as createShipmentIndex;
use App\Livewire\Pages\Shipment\View as createShipmentView;
use App\Livewire\Pages\Shipment\QrScannder as ShipmentQrScannder;
use App\Models\Branch;
use App\Livewire\Pages\Branch\BranchInventory;
use App\Livewire\Pages\Branch\SalesTracker;
use App\Livewire\Pages\Branch\BranchTransfer;





use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PurchaseOrderQRController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\DeliveryReceiptController;


// Add missing PO Management imports
use App\Livewire\Pages\POManagement\PurchaseOrder\Index as POManagementPurchaseOrder;
use App\Livewire\Pages\POManagement\PurchaseOrder\Create as POManagementPurchaseOrderCreate;
use App\Livewire\Pages\POManagement\PurchaseOrder\Edit as POManagementPurchaseOrderEdit;
use App\Livewire\Pages\POManagement\PurchaseOrder\Show as POManagementPurchaseOrderShow;
use App\Livewire\Pages\POManagement\PurchaseOrder\ViewItem as POManagementPurchaseOrderViewItem;
use App\Livewire\Pages\POManagement\PurchaseOrder\PODeliveries;



Route::redirect('', '/login')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')
        ->name('dashboard');

    Route::get('/Shipment', createShipmentIndex::class)
        ->name('shipment.index');   
        
    Route::get('/Shipment/View/{shipmentId}', createShipmentView::class)
        ->name('shipping.view'); 

    Route::get('/Shipment/scan', ShipmentQrScannder::class)
        ->name('shipment.qrscanner');

    Route::get('/reports/financial-summary', \App\Livewire\Pages\Reports\ProfitLoss::class)
        ->name('reports.financial-summary');



    Route::prefix('po-management')->name('pomanagement.')->group(function () {
        Route::get('/purchase-order', POManagementPurchaseOrder::class)->name('purchaseorder');
        Route::get('/purchase-order/create', POManagementPurchaseOrderCreate::class)->name('purchaseorder.create');
        Route::get('/purchase-order/edit/{Id}', POManagementPurchaseOrderEdit::class)->name('purchaseorder.edit');
        Route::get('/purchase-order/show/{Id}', POManagementPurchaseOrderShow::class)->name('purchaseorder.show');
        Route::get('/purchase-order/view-item/{poId?}', POManagementPurchaseOrderViewItem::class)->name('purchaseorder.viewItem');
        Route::get('/deliveries', PODeliveries::class)->name('deliveries');
    });
    Route::get('/po-management/purchaseorder/{Id}/qr', [PurchaseOrderQRController::class, 'show'])
        ->name('pomanagement.purchaseorder.qr');
    Route::get('/po-management/purchaseorder/{Id}/qr', [PurchaseOrderQRController::class, 'show'])
        ->name('pomanagement.purchaseorder.qr');
    Route::get('/suppliermanagement/profile', SupplierProfile::class)
        ->name('supplier.profile');
        
    Route::get('/suppliermanagement/profile/{id}', SupplierProfileView::class)
        ->name('supplier.view');

        
    Route::get('/customermanagement/profile', CustomerProfile::class)
        ->name('customer.profile');

    Route::get('/agentmanagement/profile', AgentProfile::class)
        ->name('agent.profile');

    Route::redirect('/Branchmanagement/profile', '/branch-management/profile', 301);
    Route::get('/branch-management/profile', BranchProfile::class)
        ->name('branch.profile');
    
    Route::get('/branch-inventory', BranchInventory::class)
        ->name('branch.inventory');

    Route::get('/branch-sales', \App\Livewire\Pages\Branch\BranchSales::class)
        ->name('branch.sales');

    Route::get('/branch-sales-tracker', SalesTracker::class)
        ->name('branch.salesTrack');

    Route::get('/branch-transfer', BranchTransfer::class)
        ->name('branch.stockTransfer');



    //Route::get('/warehouseguy/stockin', BodegeroStockIn::class)
        //->name('bodegero.stockin');
    // Route::get('/warehouseguy/stockout', StockOut::class)
        // ->name('bodegero.stockout');
    // Route::get('/Bodegero/StockIn/View/{purchaseOrder}', StockInView::class)
    // ->name('bodegero.stockin.view');
    // Route::get('/Bodegero/StockIn/Receive/{purchaseOrder}', StockInReceive::class)
    // ->name('bodegero.stockin.receive');
    
    Route::get('/warehousestaff/stockin', StockIn::class)
        ->name('warehousestaff.stockin');




    Route::get('/user-management', UserIndex::class)
        ->name('user.index');

    Route::get('/roles-permissions', RolePermissionIndex::class)->name('roles.index');

    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/receivables', \App\Livewire\Pages\Finance\Receivables::class)->name('receivables');
        Route::get('/payables', \App\Livewire\Pages\Finance\Payables::class)->name('payables');
        Route::get('/expenses', \App\Livewire\Pages\Finance\Expenses::class)->name('expenses');
        Route::get('/payments', \App\Livewire\Pages\Finance\Payments::class)->name('payments');
        Route::get('/currency-conversion', \App\Livewire\Pages\Finance\CurrencyConversion::class)->name('currency-conversion');
    });

    //QR CODE PRINTING
    Route::get('/purchase-order/print/{po_num}', function ($po_num) {
        $purchaseOrder = \App\Models\PurchaseOrder::with(['supplier', 'department', 'items.product'])->where('po_num', $po_num)->firstOrFail();
        return view('livewire.pages.qrcode.purchaseorderprint', compact('purchaseOrder'));
    })->name('purchase-orders.print');

    Route::get('/sales-order/print/{sales_order_number}', function ($sales_order_number) {
        $salesOrder = \App\Models\SalesOrder::with(['customers', 'agents', 'items.product'])->where('sales_order_number', $sales_order_number)->firstOrFail();
        return view('livewire.pages.qrcode.salesorderprint', compact('salesOrder'));
    })->name('sales-orders.print');

    Route::get('/shipment/print/{shipping_plan_num}', function ($shipping_plan_num) {
        $shipment = \App\Models\Shipment::with(['salesOrder.items.product'])->where('shipping_plan_num', $shipping_plan_num)->firstOrFail();
        return view('livewire.pages.qrcode.shipmentprint', compact('shipment'));
    })->name('shipments.print');

    // VDR Print Route
    Route::get('/allocation/vdr/print/{batchId}', [\App\Http\Controllers\VDRPrintController::class, 'printVDR'])->name('allocation.vdr.print');
    
    // VDR Excel Export Route
    Route::get('/allocation/vdr/excel/{batchId}', [\App\Http\Controllers\VDRExcelController::class, 'exportVDR'])->name('allocation.vdr.excel');

    // DR Excel Export Route
    Route::get('/allocation/dr/excel/{batchId}/{branchId?}', [\App\Http\Controllers\DRExcelController::class, 'exportDR'])->name('allocation.dr.excel');

    // Receipt PDF and Excel Export Routes
    Route::get('/allocation/receipt/pdf/{receiptId}', [\App\Http\Controllers\ReceiptController::class, 'exportPDF'])->name('allocation.receipt.pdf');
    Route::get('/allocation/receipt/excel/{receiptId}', [\App\Http\Controllers\ReceiptController::class, 'exportExcel'])->name('allocation.receipt.excel');

    // Delivery Receipt Routes
    Route::get('/allocation/delivery-receipt/generate/{branchAllocationId}', [\App\Http\Controllers\DeliveryReceiptController::class, 'generateDeliveryReceipt'])->name('allocation.delivery-receipt.generate');
    Route::get('/allocation/delivery-receipt/preview/{branchAllocationId}', [\App\Http\Controllers\DeliveryReceiptController::class, 'previewDeliveryReceipt'])->name('allocation.delivery-receipt.preview');

    // Allocation Matrix PDF Export Route
    Route::get('/allocation/matrix/pdf/{batchId}', [\App\Http\Controllers\AllocationMatrixController::class, 'exportPDF'])->name('allocation.matrix.pdf');

    Route::get('/sales-order', SalesManagementIndex::class)->name('salesorder.index');
    Route::get('/sales-order/{salesOrderId}', Viewsalesorder::class)->name('salesorder.view');
    Route::get('/sales-promo', SalesManagementPromo::class)->name('sales.promo');
    Route::get('/promo/view/{id}', \App\Livewire\Pages\SalesManagement\PromoView::class)->name('promo.view');
    Route::get('/promo/print/{id}', function($id) {
        $promo = \App\Models\Promo::findOrFail($id);
        $products = \App\Models\Product::whereIn('id', json_decode($promo->product, true) ?? [])->with(['images' => function($q){
            $q->orderByDesc('is_primary')->orderBy('sort_order')->orderBy('created_at', 'desc');
        }])->orderBy('name')->get();
        return view('livewire.pages.sales-management.print-promo', compact('promo', 'products'));
    })->name('promo.print');
    Route::get('/sales-return', SalesReturn::class)->name('sales-return.index');

    // Allocation Management
    Route::prefix('allocation')->name('allocation.')->group(function () {
        Route::get('/warehouse', Warehouse::class)->name('warehouse');
        Route::get('/for-dispatch', ForDispatchDrList::class)->name('for-dispatch');
        Route::get('/for-dispatch/{summaryDr}', SummaryDrView::class)->name('for-dispatch.view');
        Route::get('/manual-stockin', ExpectedStockIn::class)->name('manual-stockin');
        Route::get('/sales', Sales::class)->name('sales');
        Route::get('/scan', Scanning::class)->name('scan');
    });


    Route::get('/receipts/{receipt}/show', [ReceiptController::class, 'show'])->name('receipts.show');
    Route::get('/receipts/{receipt}/confirm', [ReceiptController::class, 'confirm'])->name('receipts.confirm');
    Route::get('/receipts/{receipt}/preview', [ReceiptController::class, 'preview'])->name('receipts.preview');
    Route::get('/receipts/{receipt}/items/{item}/edit', [ReceiptController::class, 'editItem'])->name('receipts.editItem');
    Route::get('/receipts/{receipt}/items/{item}/mark-sold', [ReceiptController::class, 'markSold'])->name('receipts.markSold');
    Route::get('/receipts/{receipt}/export-pdf', [ReceiptController::class, 'exportPDF'])->name('receipts.exportPDF');
    Route::get('/receipts/{receipt}/export-excel', [ReceiptController::class, 'exportExcel'])->name('receipts.exportExcel');


    // Product Management
    Route::prefix('product-management')->name('product-management.')->group(function () {
        Route::get('/', ProductManagement::class)->name('index');
        Route::get('/categories', CategoryManagement::class)->name('categories');
        Route::get('/suppliers', \App\Livewire\Pages\SupplierManagement\Profile\Index::class)->name('suppliers');
        Route::get('/locations', \App\Livewire\Pages\ProductManagement\InventoryLocationManagement::class)->name('locations');
        Route::get('/images', \App\Livewire\Pages\ProductManagement\ProductImageGallery::class)->name('images');
        // Analytics dashboard removed from Product Management
        Route::get('/print-catalog', function() {
            $products = \App\Models\Product::with(['images' => function($q){
                $q->orderByDesc('is_primary')->orderBy('sort_order')->orderBy('created_at', 'desc');
            }])->orderBy('name')->get();
            return view('livewire.pages.product-management.print-catalog', compact('products'));
        })->name('print');
    });
//    Route::get('/notifications', Notifications::class)
//         ->name('notifications.index');


    // Route::get('/Roles', Roles::class)
    //     ->name('roles');

    // Route::get('/WarehouseInventory', WHInventory::class)
    //     ->name('wh.inventory');

    // Route::get('/DeliveryPlanning', DeliveryPlanning::class)
    //     ->name('delivery.planning');

    // Route::get('/StockReport', StockReport::class)
    //     ->name('stock.report');

    // Route::get('/FSInventory', FSInventory::class)
    //     ->name('fs.inventory');

    // Route::get('/FSPurchaseRequest', FSPurchaseRequest::class)
    //     ->name('fs.purchaserequest');

    // Route::get('/StockTracker', StockTracker::class)
    //     ->name('fs.stocktracker');

    // Route::get('/RmInventory', RmInventory::class)
    //     ->name('rm.inventory');

    // Route::get('/RmRequest', RmRequest::class)
    //     ->name('rm.request');

    // Route::get('/RmReport', RmReport::class)
    //     ->name('rm.report');

    Route::get('/activity-logs', ActivityLogs::class)
        ->name('activity.logs');

    // Route::get('/SmSalesList', SmSalesList::class)
    //     ->name('sm.saleslist');

    // Route::get('/SmCustomerManagement', SmCustomerManagement::class)
    //     ->name('sm.customermgmt');

    // Route::get('/hosted-dashboard', OverallDashboard::class)
    //     ->name('hosted.dashboard');

    // Route::get('/hosted-reports', Reports::class)
    //     ->name('hosted.reports');

    // Route::get('/hosted-approver', ApproverSection::class)
    //     ->name('hosted.approver');

    // Route::get('/waste-baling', WasteAndBaling::class)
    //     ->name('waste.baling');

    // Route::get('/purchase', Purchase::class)
    //     ->name('purchase');

    Route::get('/user-logs', UserLogs::class)
        ->name('user.logs');

    // Reports Routes
    Route::get('/reports/sales-orders', \App\Livewire\Pages\Reports\SalesOrders::class)
        ->name('reports.sales-orders');
        
    Route::get('/reports/stock-available', \App\Livewire\Pages\Reports\ProductInventoryReport::class)
        ->name('reports.stock-available');
        
    Route::get('/reports/purchase-orders', \App\Livewire\Pages\Reports\PurchaseOrders::class)
        ->name('reports.purchase-orders');

    Route::get('/reports/branch-inventory', \App\Livewire\Pages\Reports\BranchInventoryReport::class)
        ->name('reports.branch-inventory');

    Route::get('/reports/warehouse-allocation', \App\Livewire\Pages\Reports\WarehouseAllocationReport::class)
        ->name('reports.warehouse-allocation');

    Route::get('/reports/finance', \App\Livewire\Pages\Reports\FinanceReport::class)
        ->name('reports.finance');

    Route::get('/reports/promo', \App\Livewire\Pages\Reports\PromoReport::class)
        ->name('reports.promo');

    Route::get('/reports/shipment', \App\Livewire\Pages\Reports\Shipment::class)
        ->name('reports.shipment');

    Route::get('/reports/stock-movement', \App\Livewire\Pages\Reports\StockMovement::class)
        ->name('reports.stock-movement');
        
    Route::get('/reports/inventory-valuation', \App\Livewire\Pages\Reports\InventoryValuation::class)
        ->name('reports.inventory-valuation');
        
    Route::get('/reports/top-products', \App\Livewire\Pages\Reports\TopProducts::class)
        ->name('reports.top-products');
        
    Route::get('/reports/supplier-performance', \App\Livewire\Pages\Reports\SupplierPerformance::class)
        ->name('reports.supplier-performance');
        
    Route::get('/reports/customer-analysis', \App\Livewire\Pages\Reports\CustomerAnalysis::class)
        ->name('reports.customer-analysis');
        
        
    Route::get('/reports/purchase-returns', \App\Livewire\Pages\Reports\PurchaseReturns::class)
        ->name('reports.purchase-returns');

});

Route::get('/camera-test', function () {
    return view('camera-test');
})->name('camera.test');

Route::get('/qr-test', function () {
    return view('qr-test');
})->name('qr.test');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');
});


require __DIR__ . '/auth.php';
