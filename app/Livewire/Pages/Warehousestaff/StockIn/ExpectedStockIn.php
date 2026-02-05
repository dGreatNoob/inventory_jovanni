<?php

namespace App\Livewire\Pages\Warehousestaff\StockIn;

use App\Models\PurchaseOrder;
use App\Models\ProductOrder;
use App\Models\ProductInventoryExpected;
use App\Enums\PurchaseOrderStatus;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

/**
 * Manual Expected Stock-In: Record "we expect X from PO-YYY" for allocation.
 * PO must exist first. Creates/updates ProductInventoryExpected.
 */
class ExpectedStockIn extends Component
{
    public ?int $selectedPurchaseOrderId = null;

    /** @var array<int, float> product_id => expected_quantity */
    public array $expectedQuantities = [];

    public string $message = '';
    public string $messageType = '';

    protected function validPOStatuses(): array
    {
        return [
            PurchaseOrderStatus::APPROVED->value,
            PurchaseOrderStatus::TO_RECEIVE->value,
        ];
    }

    public function getAvailablePurchaseOrdersProperty()
    {
        return PurchaseOrder::with('supplier')
            ->whereIn('status', $this->validPOStatuses())
            ->orderByDesc('order_date')
            ->get();
    }

    public function getSelectedPurchaseOrderProperty()
    {
        if (!$this->selectedPurchaseOrderId) {
            return null;
        }
        return PurchaseOrder::with(['productOrders.product.color', 'supplier'])
            ->find($this->selectedPurchaseOrderId);
    }

    public function getProductsForSelectedPOProperty()
    {
        $po = $this->getSelectedPurchaseOrderProperty();
        if (!$po) {
            return collect();
        }
        return $po->productOrders->map(fn ($poLine) => $poLine->product)->filter()->unique('id')->values();
    }

    /** Existing ProductInventoryExpected for selected PO, keyed by product_id. */
    public function getExistingExpectedProperty()
    {
        if (!$this->selectedPurchaseOrderId) {
            return collect();
        }
        return ProductInventoryExpected::where('purchase_order_id', $this->selectedPurchaseOrderId)
            ->get()
            ->keyBy('product_id');
    }

    public function updatedSelectedPurchaseOrderId()
    {
        $this->loadExpectedQuantities();
        $this->message = '';
        $this->messageType = '';
    }

    /**
     * Load existing expected quantities from ProductInventoryExpected.
     */
    public function loadExpectedQuantities(): void
    {
        $this->expectedQuantities = [];

        if (!$this->selectedPurchaseOrderId) {
            return;
        }

        $existing = ProductInventoryExpected::where('purchase_order_id', $this->selectedPurchaseOrderId)
            ->get()
            ->keyBy('product_id');

        $products = $this->getProductsForSelectedPOProperty();
        foreach ($products as $product) {
            $record = $existing->get($product->id);
            $this->expectedQuantities[$product->id] = $record
                ? (float) $record->expected_quantity
                : 0;
        }
    }

    public function saveExpected(): void
    {
        $this->message = '';
        $this->messageType = '';

        if (!$this->selectedPurchaseOrderId) {
            $this->message = 'Please select a Purchase Order.';
            $this->messageType = 'error';
            return;
        }

        $po = PurchaseOrder::find($this->selectedPurchaseOrderId);
        if (!$po) {
            $this->message = 'Selected PO not found.';
            $this->messageType = 'error';
            return;
        }
        $status = $po->status instanceof PurchaseOrderStatus ? $po->status->value : (string) $po->status;
        if (!in_array($status, $this->validPOStatuses())) {
            $this->message = 'Selected PO is not in a valid status for expected stock-in.';
            $this->messageType = 'error';
            return;
        }

        $errors = [];
        foreach ($this->expectedQuantities as $productId => $qty) {
            $qty = (float) $qty;
            if ($qty < 0) {
                $errors[] = "Expected quantity cannot be negative.";
                break;
            }
            $existing = ProductInventoryExpected::where('product_id', $productId)
                ->where('purchase_order_id', $this->selectedPurchaseOrderId)
                ->first();
            if ($existing && $qty < (float) $existing->received_quantity) {
                $product = $existing->product;
                $name = $product->name ?? $product->product_number ?? "Product #{$productId}";
                $errors[] = "{$name}: Expected quantity ({$qty}) cannot be less than already received (" . (float) $existing->received_quantity . ").";
            }
        }

        if (!empty($errors)) {
            $this->message = implode(' ', $errors);
            $this->messageType = 'error';
            return;
        }

        try {
            DB::beginTransaction();

            $products = $this->getProductsForSelectedPOProperty();
            foreach ($products as $product) {
                $qty = (float) ($this->expectedQuantities[$product->id] ?? 0);

                if ($qty > 0) {
                    ProductInventoryExpected::updateOrCreate(
                        [
                            'product_id' => $product->id,
                            'purchase_order_id' => $this->selectedPurchaseOrderId,
                        ],
                        [
                            'expected_quantity' => $qty,
                        ]
                    );
                } else {
                    ProductInventoryExpected::where('product_id', $product->id)
                        ->where('purchase_order_id', $this->selectedPurchaseOrderId)
                        ->delete();
                }
            }

            DB::commit();
            $this->message = 'Expected quantities saved successfully.';
            $this->messageType = 'success';
            $this->loadExpectedQuantities();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->message = 'Failed to save: ' . $e->getMessage();
            $this->messageType = 'error';
        }
    }

    public function mount(): void
    {
        $poNum = request()->query('po');
        if ($poNum) {
            $po = PurchaseOrder::where('po_num', $poNum)->first();
            if ($po) {
                $status = $po->status instanceof PurchaseOrderStatus ? $po->status->value : (string) $po->status;
                if (in_array($status, $this->validPOStatuses())) {
                    $this->selectedPurchaseOrderId = $po->id;
                    $this->loadExpectedQuantities();
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.pages.warehousestaff.stock-in.expected-stockin')
            ->layout('components.layouts.app');
    }
}
