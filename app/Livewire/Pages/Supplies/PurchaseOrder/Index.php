<?php

namespace App\Livewire\Pages\Supplies\PurchaseOrder;

use App\Models\PurchaseOrder;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $statusFilter = '';
    public $deletingPurchaseOrderId = null;
    public $showDeleteModal = false;

    // QR Code Modal properties
    public $showQrModal = false;
    public $selectedPurchaseOrder = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
    ];

    // Computed properties for QR modal totals
    public function getSelectedPurchaseOrderTotalQuantityProperty()
    {
        if (!$this->selectedPurchaseOrder) {
            return 0;
        }
        return $this->selectedPurchaseOrder->supplyOrders->sum('order_qty');
    }

    public function getSelectedPurchaseOrderTotalPriceProperty()
    {
        if (!$this->selectedPurchaseOrder) {
            return 0;
        }
        return $this->selectedPurchaseOrder->supplyOrders->sum('order_total_price');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function confirmDelete($id)
    {
        $this->deletingPurchaseOrderId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        try {
            DB::beginTransaction();

            $purchaseOrder = PurchaseOrder::with(['supplyOrders', 'supplier'])->findOrFail($this->deletingPurchaseOrderId);
            
            // Prepare item summary for activity log
            $itemSummary = $purchaseOrder->supplyOrders->map(function($order) {
                $supply = $order->supplyProfile;
                return 'SKU: ' . ($supply->supply_sku ?? '-') . ', Desc: ' . ($supply->supply_description ?? '-') . ', Qty: ' . $order->order_qty . ', Unit Price: ' . $order->unit_price;
            })->implode('<br>');

            // Log activity before deletion
            activity()
                ->causedBy(\Illuminate\Support\Facades\Auth::user())
                ->performedOn($purchaseOrder)
                ->withProperties([
                    'po_num' => $purchaseOrder->po_num,
                    'supplier' => $purchaseOrder->supplier ? $purchaseOrder->supplier->name : '-',
                    'total_price' => $purchaseOrder->total_price,
                    'items' => $itemSummary,
                ])
                ->log('Purchase order deleted');
            
            // Delete all associated supply orders first
            $purchaseOrder->supplyOrders()->delete();

            // Delete the purchase order
            $purchaseOrder->delete();

            DB::commit();

            $this->reset(['deletingPurchaseOrderId', 'showDeleteModal']);
            session()->flash('message', 'Purchase order and its associated items have been deleted successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Failed to delete purchase order: ' . $e->getMessage());
        }
    }

    public function showQrCode($id)
    {
        $this->selectedPurchaseOrder = PurchaseOrder::with([
            'supplier', 
            'department', 
            'orderedBy', 
            'approverInfo',
            'supplyOrders.supplyProfile'
        ])->findOrFail($id);
        $this->showQrModal = true;
    }

    public function closeQrModal()
    {
        $this->showQrModal = false;
        $this->selectedPurchaseOrder = null;
    }

    public function cancel()
    {
        $this->reset(['deletingPurchaseOrderId', 'showDeleteModal']);
    }

    public function getDashboardStatsProperty()
    {
        $baseQuery = PurchaseOrder::where('po_type', 'supply');
        
        $totalPurchaseOrders = $baseQuery->count();
        $pendingOrders = $baseQuery->where('status', 'for_approval')->count();
        $approvedOrders = $baseQuery->where('status', 'to_receive')->count();
        $receivedOrders = $baseQuery->where('status', 'received')->count();
        $totalValue = $baseQuery->sum('total_price');

        return [
            [
                'label' => 'Total Orders',
                'value' => number_format($totalPurchaseOrders),
                'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>',
                'gradient' => 'from-blue-500 to-blue-600'
            ],
            [
                'label' => 'Pending Approval',
                'value' => number_format($pendingOrders),
                'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                'gradient' => 'from-yellow-500 to-yellow-600'
            ],
            [
                'label' => 'To Receive',
                'value' => number_format($approvedOrders),
                'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>',
                'gradient' => 'from-indigo-500 to-indigo-600'
            ],
            [
                'label' => 'Total Value',
                'value' => '₱' . number_format($totalValue, 2),
                'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path></svg>',
                'gradient' => 'from-green-500 to-green-600'
            ]
        ];
    }

    public function render()
    {
        $purchaseOrders = PurchaseOrder::query()
            ->where('po_type', 'supply')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('del_to', 'like', '%' . $this->search . '%')
                        ->orWhere('payment_terms', 'like', '%' . $this->search . '%')
                        ->orWhere('quotation', 'like', '%' . $this->search . '%')
                        ->orWhereHas('supplier', function ($query) {
                            $query->where('name', 'like', '%' . $this->search . '%');
                        });
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->with(['supplier', 'department'])
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.pages.supplies.purchase-order.index', [
            'purchaseOrders' => $purchaseOrders,
            'dashboardStats' => $this->getDashboardStatsProperty()
        ]);
    }
}
