<?php

namespace App\Livewire\Pages\SalesManagement;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Promo;
use App\Models\Product;
use App\Models\BatchAllocation;
use Illuminate\Support\Facades\DB;

class PromoView extends Component
{
    use WithPagination;

    public $promoId;
    public $promo;
    public $products;

    // View data
    public $name;
    public $code;
    public $type;
    public $startDate;
    public $endDate;
    public $description;
    public $selected_branches = [];
    public $selected_products = [];
    public $selected_second_products = [];

    // Products table
    public $productSearch = '';
    public $productsPerPage = 10;

    public $viewingProduct = null;

    public function mount($id)
    {
        $this->promoId = $id;
        $this->loadPromoData();
    }

    public function loadPromoData()
    {
        $this->promo = Promo::findOrFail($this->promoId);
        $this->products = Product::all();

        // Set the view data
        $this->name = $this->promo->name;
        $this->code = $this->promo->code;
        $this->type = $this->promo->type;
        $this->startDate = $this->promo->startDate;
        $this->endDate = $this->promo->endDate;
        $this->description = $this->promo->description;
        $this->selected_branches = json_decode($this->promo->branch, true) ?? [];
        $this->selected_products = json_decode($this->promo->product, true) ?? [];
        $this->selected_second_products = json_decode($this->promo->second_product ?? '[]', true);
    }

    public function getConnectedProductsProperty()
    {
        $allProductIds = array_merge($this->selected_products, $this->selected_second_products);
        
        return Product::whereIn('id', $allProductIds)
            ->when($this->productSearch, function ($query) {
                $query->where('name', 'like', '%' . $this->productSearch . '%')
                      ->orWhere('sku', 'like', '%' . $this->productSearch . '%');
            })
            ->orderBy('name')
            ->paginate($this->productsPerPage);
    }

    public function viewProduct($productId)
    {
        $this->viewingProduct = Product::find($productId);
        $this->dispatch('open-modal', name: 'product-details');
    }

    /**
     * Get stock on hand for a product in the branches associated with this promo
     */
    public function getProductStockInBranches($productId)
    {
        // Get batch allocation IDs from promo
        $batchAllocationIds = json_decode($this->promo->branch, true) ?? [];
        if (empty($batchAllocationIds)) {
            return 0;
        }

        // Get all branch IDs from the batch allocations
        $branchIds = DB::table('branch_allocations')
            ->whereIn('batch_allocation_id', $batchAllocationIds)
            ->pluck('branch_id')
            ->unique()
            ->toArray();

        if (empty($branchIds)) {
            return 0;
        }

        // Get stock from branch_product pivot table for this product in those branches
        $totalStock = DB::table('branch_product')
            ->where('product_id', $productId)
            ->whereIn('branch_id', $branchIds)
            ->sum('stock');

        return (float) $totalStock;
    }

    /**
     * Get stock status for a product in the branches associated with this promo
     */
    public function getProductStockStatusInBranches($productId)
    {
        $stock = $this->getProductStockInBranches($productId);
        
        if ($stock <= 0) {
            return [
                'status' => 'out_of_stock',
                'label' => 'Out of Stock',
                'color' => 'red'
            ];
        } elseif ($stock <= 10) { // Low stock threshold
            return [
                'status' => 'low_stock',
                'label' => 'Low Stock',
                'color' => 'yellow'
            ];
        } else {
            return [
                'status' => 'in_stock',
                'label' => 'In Stock',
                'color' => 'green'
            ];
        }
    }

    public function exportPromo()
    {
        return redirect()->away(route('promo.print', $this->promoId));
    }

    public function goBack()
    {
        return redirect()->route('sales.promo'); // Adjust to your actual route
    }

    public function render()
    {
        return view('livewire.pages.sales-management.promo-view', [
            'branch_names' => $this->promo->branch_names ?? [],
            'connectedProducts' => $this->connectedProducts,
        ]);
    }
}