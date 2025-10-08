<?php

namespace App\Livewire\Pages\ProductManagement;

use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\InventoryLocation;
use App\Models\InventoryMovement;
use App\Models\ProductInventory;
use App\Services\InventoryService;
use App\Services\ProductService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Inventory Dashboard')]
class InventoryDashboard extends Component
{
    // Time period filters
    public $timePeriod = '30'; // days
    public $dateFrom = '';
    public $dateTo = '';

    // Data refresh
    public $lastRefresh = null;
    public $autoRefresh = true;
    public $refreshInterval = 30; // seconds

    protected $inventoryService;
    protected $productService;

    public function boot(InventoryService $inventoryService, ProductService $productService)
    {
        $this->inventoryService = $inventoryService;
        $this->productService = $productService;
    }

    public function mount()
    {
        $this->initializeDateRange();
        $this->lastRefresh = now();
    }

    public function initializeDateRange()
    {
        $this->dateTo = now()->format('Y-m-d');
        $this->dateFrom = now()->subDays($this->timePeriod)->format('Y-m-d');
    }

    public function updatedTimePeriod()
    {
        $this->initializeDateRange();
    }

    public function updatedDateFrom()
    {
        // Ensure dateFrom is not after dateTo
        if ($this->dateFrom && $this->dateTo && $this->dateFrom > $this->dateTo) {
            $this->dateTo = $this->dateFrom;
        }
    }

    public function updatedDateTo()
    {
        // Ensure dateTo is not before dateFrom
        if ($this->dateFrom && $this->dateTo && $this->dateTo < $this->dateFrom) {
            $this->dateFrom = $this->dateTo;
        }
    }

    public function refreshData()
    {
        $this->lastRefresh = now();
        // The computed properties will automatically refresh due to Livewire's reactivity
    }

    public function getOverviewStatsProperty()
    {
        $totalProducts = Product::count();
        $activeProducts = Product::where('disabled', false)->count();
        $totalCategories = Category::where('is_active', true)->count();
        $totalSuppliers = Supplier::where('is_active', true)->count();
        $totalLocations = InventoryLocation::where('is_active', true)->count();

        // Inventory value calculation (guard against NULL cost)
        $inventoryValue = ProductInventory::join('products', 'product_inventory.product_id', '=', 'products.id')
            ->sum(DB::raw('product_inventory.quantity * COALESCE(products.cost, 0)'));

        // Low stock products (quantity < 10)
        $lowStockProducts = ProductInventory::where('quantity', '<', 10)->count();

        // Out of stock products
        $outOfStockProducts = ProductInventory::where('quantity', '<=', 0)->count();

        return [
            'total_products' => $totalProducts,
            'active_products' => $activeProducts,
            'total_categories' => $totalCategories,
            'total_suppliers' => $totalSuppliers,
            'total_locations' => $totalLocations,
            'inventory_value' => $inventoryValue,
            'low_stock_products' => $lowStockProducts,
            'out_of_stock_products' => $outOfStockProducts,
        ];
    }

    public function getInventoryMovementsProperty()
    {
        $query = InventoryMovement::with(['product', 'location'])
            ->whereBetween('created_at', [$this->dateFrom, $this->dateTo . ' 23:59:59']);

        return [
            'total_movements' => $query->count(),
            'inbound_movements' => $query->clone()->where('movement_type', 'in')->count(),
            'outbound_movements' => $query->clone()->where('movement_type', 'out')->count(),
            'adjustment_movements' => $query->clone()->where('movement_type', 'adjustment')->count(),
        ];
    }

    public function getTopProductsProperty()
    {
        return Product::with(['category', 'supplier'])
            ->withSum('inventoryMovements as total_movements', 'quantity')
            ->whereNotNull('name')
            ->orderBy('total_movements', 'desc')
            ->limit(10)
            ->get();
    }

    public function getLowStockProductsProperty()
    {
        return ProductInventory::with(['product.category', 'product.supplier', 'location'])
            ->whereHas('product', function($query) {
                $query->whereNotNull('id');
            })
            ->where('quantity', '<', 10)
            ->orderBy('quantity', 'asc')
            ->limit(10)
            ->get()
            ->filter(function($inventory) {
                return $inventory->product !== null;
            });
    }


    public function getRecentMovementsProperty()
    {
        return InventoryMovement::with(['product', 'location', 'creator'])
            ->whereBetween('created_at', [$this->dateFrom, $this->dateTo . ' 23:59:59'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->filter(function($movement) {
                return $movement->product !== null;
            });
    }

    public function getCategoryDistributionProperty()
    {
        return Category::withCount(['products' => function ($query) {
            $query->where('disabled', false);
        }])
            ->where('is_active', true)
            ->orderBy('products_count', 'desc')
            ->limit(10)
            ->get();
    }

    public function getLocationDistributionProperty()
    {
        return InventoryLocation::withCount(['productInventory as total_products'])
            ->where('is_active', true)
            ->orderBy('total_products', 'desc')
            ->limit(10)
            ->get();
    }

    public function getInventoryTrendsProperty()
    {
        $trends = [];
        $days = $this->timePeriod;

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            
            $inbound = InventoryMovement::where('movement_type', 'in')
                ->whereDate('created_at', $date)
                ->sum('quantity');
                
            $outbound = InventoryMovement::where('movement_type', 'out')
                ->whereDate('created_at', $date)
                ->sum('quantity');

            $trends[] = [
                'date' => $date,
                'inbound' => $inbound,
                'outbound' => $outbound,
                'net' => $inbound - $outbound,
            ];
        }

        return $trends;
    }

    public function getSupplierPerformanceProperty()
    {
        return Supplier::withCount(['products' => function ($query) {
            $query->where('disabled', false);
        }])
            ->where('is_active', true)
            ->orderBy('products_count', 'desc')
            ->limit(10)
            ->get();
    }

    public function getInventoryValueByLocationProperty()
    {
        return InventoryLocation::with(['productInventory.product'])
            ->where('is_active', true)
            ->get()
            ->map(function ($location) {
                $totalValue = $location->productInventory->sum(function ($inventory) {
                    $cost = optional($inventory->product)->cost ?? 0;
                    return $inventory->quantity * $cost;
                });

                return [
                    'location' => $location->name,
                    'value' => $totalValue,
                    'product_count' => $location->productInventory->count(),
                ];
            })
            ->sortByDesc('value')
            ->values();
    }

    public function getAlertsProperty()
    {
        $alerts = [];

        // Low stock alerts
        $lowStockCount = ProductInventory::where('quantity', '<', 10)->count();
        if ($lowStockCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Low Stock Alert',
                'message' => "{$lowStockCount} products are running low on stock",
                'icon' => 'exclamation-triangle',
            ];
        }

        // Out of stock alerts
        $outOfStockCount = ProductInventory::where('quantity', '<=', 0)->count();
        if ($outOfStockCount > 0) {
            $alerts[] = [
                'type' => 'error',
                'title' => 'Out of Stock Alert',
                'message' => "{$outOfStockCount} products are out of stock",
                'icon' => 'x-circle',
            ];
        }

        // No recent movements
        $recentMovements = InventoryMovement::where('created_at', '>=', now()->subDays(7))->count();
        if ($recentMovements === 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'No Recent Activity',
                'message' => 'No inventory movements in the last 7 days',
                'icon' => 'information-circle',
            ];
        }

        return $alerts;
    }

    public function render()
    {
        return view('livewire.pages.product-management.inventory-dashboard', [
            'overviewStats' => $this->overviewStats,
            'inventoryMovements' => $this->inventoryMovements,
            'topProducts' => $this->topProducts,
            'lowStockProducts' => $this->lowStockProducts,
            'recentMovements' => $this->recentMovements,
            'categoryDistribution' => $this->categoryDistribution,
            'locationDistribution' => $this->locationDistribution,
            'inventoryTrends' => $this->inventoryTrends,
            'supplierPerformance' => $this->supplierPerformance,
            'inventoryValueByLocation' => $this->inventoryValueByLocation,
            'alerts' => $this->alerts,
        ]);
    }
}
