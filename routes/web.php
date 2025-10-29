<?php

use App\Livewire\Purchase;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use Illuminate\Support\Facades\Route;
use App\Livewire\Pages\Requisition\RequestSlip\Index as RequestSlip;
use App\Livewire\Pages\Requisition\RequestSlip\View;
use App\Livewire\Pages\Bodegero\StockIn\Index as BodegeroStockIn;
use App\Livewire\Pages\Bodegero\StockIn\View as StockInView;
use App\Livewire\Pages\Bodegero\StockIn\Receive as StockInReceive;
use App\Livewire\Pages\Bodegero\StockOut\Index as StockOut;
use App\Livewire\Pages\Warehousestaff\StockIn\Index as StockIn;



use App\Livewire\Pages\PaperRollWarehouse\Inventory\Index as PRWInventory;
use App\Livewire\Pages\PaperRollWarehouse\Inventory\Create as PRWInventoryCreate;
use App\Livewire\Pages\PaperRollWarehouse\PurchaseOrder\Index as PRWPurchaseOrder;
use App\Livewire\Pages\PaperRollWarehouse\PurchaseOrder\Create as PRWPurchaseOrderCreate;
use App\Livewire\Pages\PaperRollWarehouse\PurchaseOrder\Edit as PRWPurchaseOrderEdit;
use App\Livewire\Pages\PaperRollWarehouse\PurchaseOrder\Show as PRWPurchaseOrderShow;
use App\Livewire\Pages\PaperRollWarehouse\Profile\Index as PRWProfile;
use App\Livewire\Pages\PaperRollWarehouse\PurchaseOrder\ViewItem as PRWPurchaseOrderViewItem;

use App\Livewire\Pages\SupplierManagement\Profile\Index as SupplierProfile;
use App\Livewire\Pages\Customer\Index as CustomerProfile;
use App\Livewire\Pages\Agent\Index as AgentProfile;
use App\Livewire\Pages\Branch\Index as BranchProfile;

use App\Livewire\Pages\Setup\Department\Index as DepartmentSetup;
use App\Livewire\Pages\Setup\ItemType\Index as ItemTypeSetup;
use App\Livewire\Pages\Setup\Allocation\Index as AllocationSetup;
use App\Livewire\Pages\Notifications\Index as Notifications;

// Product Management
use App\Livewire\Pages\ProductManagement\Index as ProductManagement;
use App\Livewire\Pages\ProductManagement\CategoryManagement;



use App\Livewire\Pages\User\Index as UserIndex;
use App\Livewire\Pages\RolePermission\Index as RolePermissionIndex;
//QR CODE PRINTING
use App\Livewire\ActivityLogs;
use App\Livewire\UserLogs;

use App\Livewire\Pages\SalesManagement\Index as SalesManagementIndex;
use App\Livewire\Pages\SalesManagement\SalesReturn as SalesManagementSalesReturn;
use App\Livewire\Pages\SalesManagement\View as Viewsalesorder;
use App\Livewire\Pages\SalesManagement\ViewSalesReturn;
use App\Livewire\Pages\Shipment\Index as createShipmentIndex;
use App\Livewire\Pages\Shipment\View as createShipmentView;
use App\Livewire\Pages\Shipment\QrScannder as ShipmentQrScannder;
use App\Models\Branch;




use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PurchaseOrderQRController;

// Add missing PO Management imports
use App\Livewire\Pages\POManagement\PurchaseOrder\Index as POManagementPurchaseOrder;
use App\Livewire\Pages\POManagement\PurchaseOrder\Create as POManagementPurchaseOrderCreate;
use App\Livewire\Pages\POManagement\PurchaseOrder\Edit as POManagementPurchaseOrderEdit;
use App\Livewire\Pages\POManagement\PurchaseOrder\Show as POManagementPurchaseOrderShow;
use App\Livewire\Pages\POManagement\PurchaseOrder\ViewItem as POManagementPurchaseOrderViewItem;


Route::redirect('', '/login')->name('home');

Route::middleware(['auth'])->group(function () {
    Route::view('dashboard', 'dashboard')
        ->name('dashboard');

    Route::get('/RequestSlip', RequestSlip::class)
        ->name('requisition.requestslip');
    
    Route::get('/Shipment', createShipmentIndex::class)
        ->name('shipment.index');   
        
    Route::get('/Shipment/View/{shipmentId}', createShipmentView::class)
        ->name('shipping.view'); 

    Route::get('/Shipment/scan', ShipmentQrScannder::class)
        ->name('shipment.qrscanner');

    Route::get('/reports/financial-summary', \App\Livewire\Pages\Reports\ProfitLoss::class)
        ->name('reports.financial-summary');

    Route::prefix('prw')->name('prw.')->group(function () {
        Route::get('/inventory', PRWInventory::class)->name('inventory');
        Route::get('/purchase-order', PRWPurchaseOrder::class)->name('purchaseorder');
        Route::get('/purchase-order/create', PRWPurchaseOrderCreate::class)->name('purchaseorder.create');
        Route::get('/purchase-order/edit/{Id}', PRWPurchaseOrderEdit::class)->name('purchaseorder.edit');
        Route::get('/purchase-order/show/{Id}', PRWPurchaseOrderShow::class)->name('purchaseorder.show');
        Route::get('/profile', PRWProfile::class)->name('profile');
        Route::get('/purchase-order/view-item/{poId?}', PRWPurchaseOrderViewItem::class)->name('purchaseorder.viewItem');
    });


    Route::prefix('po-management')->name('pomanagement.')->group(function () {
        Route::get('/purchase-order', POManagementPurchaseOrder::class)->name('purchaseorder');
        Route::get('/purchase-order/create', POManagementPurchaseOrderCreate::class)->name('purchaseorder.create');
        Route::get('/purchase-order/edit/{Id}', POManagementPurchaseOrderEdit::class)->name('purchaseorder.edit');
        Route::get('/purchase-order/show/{Id}', POManagementPurchaseOrderShow::class)->name('purchaseorder.show');
        Route::get('/purchase-order/view-item/{poId?}', POManagementPurchaseOrderViewItem::class)->name('purchaseorder.viewItem');
    });
    Route::get('/po-management/purchaseorder/{Id}/qr', [PurchaseOrderQRController::class, 'show'])
        ->name('pomanagement.purchaseorder.qr');
    Route::get('/po-management/purchaseorder/{Id}/qr', [PurchaseOrderQRController::class, 'show'])
        ->name('pomanagement.purchaseorder.qr');
    Route::get('/suppliermanagement/profile', SupplierProfile::class)
        ->name('supplier.profile');

    Route::get('/customermanagement/profile', CustomerProfile::class)
        ->name('customer.profile');

    Route::get('/agentmanagement/profile', AgentProfile::class)
        ->name('agent.profile');

    Route::get('/Branchmanagement/profile', BranchProfile::class)
        ->name('branch.profile');


    Route::get('/RequestSlip/{request_slip_id}', View::class)
        ->name('requisition.requestslip.view');

    //Route::get('/warehouseguy/stockin', BodegeroStockIn::class)
        //->name('bodegero.stockin');
    Route::get('/warehouseguy/stockout', StockOut::class)
        ->name('bodegero.stockout');
    // Route::get('/Bodegero/StockIn/View/{purchaseOrder}', StockInView::class)
    // ->name('bodegero.stockin.view');
    // Route::get('/Bodegero/StockIn/Receive/{purchaseOrder}', StockInReceive::class)
    // ->name('bodegero.stockin.receive');
    
    Route::get('/warehousestaff/stockin', StockIn::class)
        ->name('warehousestaff.stockin');


    Route::get('/Setup/Department', DepartmentSetup::class)
        ->name('setup.department');

    Route::get('/Setup/ItemType', ItemTypeSetup::class)
        ->name('setup.itemType');

    Route::get('/Setup/Allocation', AllocationSetup::class)
        ->name('setup.allocation');

    Route::get('/user-management', UserIndex::class)
        ->name('user.index');
    Route::get('/roles-permissions', RolePermissionIndex::class)->name('roles.index');

    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/receivables', \App\Livewire\Pages\Finance\Receivables::class)->name('receivables');
        Route::get('/payables', \App\Livewire\Pages\Finance\Payables::class)->name('payables');
        Route::get('/expenses', \App\Livewire\Pages\Finance\Expenses::class)->name('expenses');
        Route::get('/currency-conversion', \App\Livewire\Pages\Finance\CurrencyConversion::class)->name('currency-conversion');
    });

    //QR CODE PRINTING
    Route::get('/purchase-order/print/{po_num}', function ($po_num) {
        $purchaseOrder = \App\Models\PurchaseOrder::with(['supplier', 'department', 'supplyOrders.supplyProfile'])->where('po_num', $po_num)->firstOrFail();
        return view('livewire.pages.qrcode.purchaseorderprint', compact('purchaseOrder'));
    })->name('purchase-orders.print');

    Route::get('/sales-order', SalesManagementIndex::class)->name('salesorder.index');
    Route::get('/sales-order/{salesOrderId}', Viewsalesorder::class)->name('salesorder.view');
    Route::get('/sales-return', SalesManagementSalesReturn::class)->name('salesorder.return');
    Route::get('/sales-return/{salesreturnId}', ViewSalesReturn::class)->name('salesreturn.view');

    // Product Management
    Route::prefix('product-management')->name('product-management.')->group(function () {
        Route::get('/', ProductManagement::class)->name('index');
        Route::get('/categories', CategoryManagement::class)->name('categories');
        Route::get('/suppliers', \App\Livewire\Pages\SupplierManagement\Profile\Index::class)->name('suppliers');
        Route::get('/locations', \App\Livewire\Pages\ProductManagement\InventoryLocationManagement::class)->name('locations');
        Route::get('/images', \App\Livewire\Pages\ProductManagement\ProductImageGallery::class)->name('images');
        Route::get('/dashboard', \App\Livewire\Pages\ProductManagement\InventoryDashboard::class)->name('dashboard');
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
        
    Route::get('/reports/stock-available', \App\Livewire\Pages\Reports\StockAvailable::class)
        ->name('reports.stock-available');
        
    Route::get('/reports/purchase-orders', \App\Livewire\Pages\Reports\PurchaseOrders::class)
        ->name('reports.purchase-orders');
        
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
        
    Route::get('/reports/sales-returns', \App\Livewire\Pages\Reports\SalesReturns::class)
        ->name('reports.sales-returns');
        
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
