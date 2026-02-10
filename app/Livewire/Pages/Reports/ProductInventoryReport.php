<?php

namespace App\Livewire\Pages\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\InventoryMovement;
use App\Models\ProductInventory;
use App\Services\InventoryService;
use App\Services\ProductService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

#[Layout('components.layouts.app')]
#[Title('Product Inventory')]
class ProductInventoryReport extends Component
{
    use WithPagination;
    public $timePeriod = '30';
    public $dateFrom = '';
    public $dateTo = '';

    public $lastRefresh = null;
    public $autoRefresh = true;
    public $refreshInterval = 30;

    protected $inventoryService;
    protected $productService;
    public $search = '';
    public $categoryId = null;
    public $supplierId = null;
    public $locationId = null;
    public $pageSize = 25;
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $visibleColumns = [
        'sku' => true,
        'name' => true,
        'category' => true,
        'supplier' => true,
        'on_hand' => true,
        'allocated' => true,
        'available' => true,
        'on_order' => true,
        'unit_cost' => true,
        'ext_value' => true,
    ];

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
        if ($this->dateFrom && $this->dateTo && $this->dateFrom > $this->dateTo) {
            $this->dateTo = $this->dateFrom;
        }
    }

    public function updatedDateTo()
    {
        if ($this->dateFrom && $this->dateTo && $this->dateTo < $this->dateFrom) {
            $this->dateFrom = $this->dateTo;
        }
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function refreshData()
    {
        $this->lastRefresh = now();
    }

    public function getOverviewStatsProperty()
    {
        $totalProducts = Product::count();
        $activeProducts = Product::where('disabled', false)->count();
        $totalCategories = Category::where('is_active', true)->count();
        $totalSuppliers = Supplier::where('is_active', true)->count();

        // Inventory valuation (current): quantity * product cost. In future, switch to average_cost.
        $inventoryValue = ProductInventory::join('products', 'product_inventory.product_id', '=', 'products.id')
            ->whereNull('products.deleted_at')
            ->sum(DB::raw('product_inventory.quantity * COALESCE(products.cost, 0)'));

        $lowStockProducts = ProductInventory::where('quantity', '<', 10)
            ->whereHas('product')
            ->count();

        $outOfStockProducts = ProductInventory::where('quantity', '<=', 0)
            ->whereHas('product')
            ->count();

        return [
            'total_products' => $totalProducts,
            'active_products' => $activeProducts,
            'total_categories' => $totalCategories,
            'total_suppliers' => $totalSuppliers,
            'inventory_value' => $inventoryValue,
            'low_stock_products' => $lowStockProducts,
            'out_of_stock_products' => $outOfStockProducts,
        ];
    }

    public function getCategoryOptionsProperty()
    {
        return Category::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function getSupplierOptionsProperty()
    {
        return Supplier::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function getLocationOptionsProperty()
    {
        return \App\Models\InventoryLocation::active()
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function getInventoryBalancesProperty()
    {
        // Stock balances per product with financials. Uses existing fields and provides placeholders
        // for allocated/on_order until underlying data is available.
        $query = ProductInventory::query()
            ->select([
                'product_inventory.product_id',
                DB::raw('SUM(product_inventory.quantity) as on_hand'),
            ])
            ->groupBy('product_inventory.product_id')
            ->with(['product' => function ($q) {
                $q->select('id', 'name', 'sku', 'category_id', 'supplier_id', 'cost');
            }, 'product.category:id,name', 'product.supplier:id,name']);

        if ($this->search) {
            $query->whereHas('product', function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('sku', 'like', "%{$this->search}%");
            });
        }
        if ($this->categoryId) {
            $query->whereHas('product', function ($q) {
                $q->where('category_id', $this->categoryId);
            });
        }
        if ($this->supplierId) {
            $query->whereHas('product', function ($q) {
                $q->where('supplier_id', $this->supplierId);
            });
        }
        if ($this->locationId) {
            $query->where('location_id', $this->locationId);
        }

        // Sorting
        if (in_array($this->sortField, ['name', 'sku'])) {
            $query->join('products', 'products.id', '=', 'product_inventory.product_id')
                  ->orderBy("products.{$this->sortField}", $this->sortDirection);
        } elseif ($this->sortField === 'on_hand') {
            $query->orderBy('on_hand', $this->sortDirection);
        }

        $paginator = $query->paginate($this->pageSize);

        $balances = $paginator->getCollection()->map(function ($row) {
            $allocated = 0; // Placeholder until reservations are tracked
            $onOrder = 0;   // Placeholder until PO open quantities are linked
            $available = max(0, ($row->on_hand ?? 0) - $allocated);
            // Use product cost; average_cost not present in schema yet
            $unitCost = $row->product->cost ?? 0;
            $extValue = $available * $unitCost;

            return [
                'product_id' => $row->product_id,
                'sku' => $row->product->sku ?? null,
                'name' => $row->product->name ?? null,
                'category' => $row->product->category->name ?? null,
                'supplier' => $row->product->supplier->name ?? null,
                'on_hand' => (int) ($row->on_hand ?? 0),
                'allocated' => (int) $allocated,
                'available' => (int) $available,
                'on_order' => (int) $onOrder,
                'unit_cost' => (float) $unitCost,
                'ext_value' => (float) $extValue,
            ];
        });

        // Replace the paginator's collection with the transformed data
        $paginator->setCollection(collect($balances));
        return $paginator;
    }

    public function getTotalsProperty()
    {
        // Grand totals for footer
        $all = ProductInventory::join('products', 'products.id', '=', 'product_inventory.product_id')
            ->when($this->categoryId, fn($q) => $q->where('products.category_id', $this->categoryId))
            ->when($this->supplierId, fn($q) => $q->where('products.supplier_id', $this->supplierId))
            ->when($this->locationId, fn($q) => $q->where('product_inventory.location_id', $this->locationId))
            ->selectRaw('SUM(product_inventory.quantity) as on_hand_sum, SUM(product_inventory.quantity * COALESCE(products.cost, 0)) as value_sum')
            ->first();

        return [
            'on_hand' => (int)($all->on_hand_sum ?? 0),
            'value' => (float)($all->value_sum ?? 0.0),
        ];
    }

    public function getCategorySubtotalsProperty()
    {
        return Category::select('categories.id', 'categories.name')
            ->join('products', 'products.category_id', '=', 'categories.id')
            ->join('product_inventory', 'product_inventory.product_id', '=', 'products.id')
            ->when($this->supplierId, fn($q) => $q->where('products.supplier_id', $this->supplierId))
            ->when($this->locationId, fn($q) => $q->where('product_inventory.location_id', $this->locationId))
            ->groupBy('categories.id', 'categories.name')
            ->selectRaw('SUM(product_inventory.quantity) as on_hand_sum, SUM(product_inventory.quantity * COALESCE(products.cost, 0)) as value_sum')
            ->orderBy('categories.name')
            ->get();
    }

    public function getSupplierSubtotalsProperty()
    {
        return Supplier::where('suppliers.is_active', true)
            ->select('suppliers.id', 'suppliers.name')
            ->join('products', 'products.supplier_id', '=', 'suppliers.id')
            ->join('product_inventory', 'product_inventory.product_id', '=', 'products.id')
            ->when($this->categoryId, fn($q) => $q->where('products.category_id', $this->categoryId))
            ->when($this->locationId, fn($q) => $q->where('product_inventory.location_id', $this->locationId))
            ->groupBy('suppliers.id', 'suppliers.name')
            ->selectRaw('SUM(product_inventory.quantity) as on_hand_sum, SUM(product_inventory.quantity * COALESCE(products.cost, 0)) as value_sum')
            ->orderBy('suppliers.name')
            ->get();
    }

    public function getLocationSubtotalsProperty()
    {
        // If schema lacks location_id, skip location subtotals gracefully
        if (!Schema::hasColumn('product_inventory', 'location_id')) {
            return collect();
        }

        return \App\Models\InventoryLocation::select('inventory_locations.id', 'inventory_locations.name')
            ->leftJoin('product_inventory', 'product_inventory.location_id', '=', 'inventory_locations.id')
            ->join('products', 'products.id', '=', 'product_inventory.product_id')
            ->when($this->categoryId, fn($q) => $q->where('products.category_id', $this->categoryId))
            ->when($this->supplierId, fn($q) => $q->where('products.supplier_id', $this->supplierId))
            ->groupBy('inventory_locations.id', 'inventory_locations.name')
            ->selectRaw('SUM(product_inventory.quantity) as on_hand_sum, SUM(product_inventory.quantity * COALESCE(products.cost, 0)) as value_sum')
            ->orderBy('inventory_locations.name')
            ->get();
    }

    public function exportCsv()
    {
        $rows = $this->inventoryBalances->getCollection();
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="inventory_balances.csv"',
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['SKU','Product','Category','Supplier','On-hand','Allocated','Available','On-order','Unit Cost','Ext. Value']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r['sku'], $r['name'], $r['category'], $r['supplier'],
                    $r['on_hand'], $r['allocated'], $r['available'], $r['on_order'],
                    number_format($r['unit_cost'], 2, '.', ''), number_format($r['ext_value'], 2, '.', ''),
                ]);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportXlsx()
    {
        // Stub: integrate Laravel Excel (maatwebsite/excel) for XLSX
        session()->flash('message', 'XLSX export is not yet configured.');
    }

    public function exportPdf()
    {
        // Stub: integrate DOMPDF or Snappy for PDF
        session()->flash('message', 'PDF export is not yet configured.');
    }

    public function getInventoryMovementsProperty()
    {
        $query = InventoryMovement::with(['product'])
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

    public function getAlertsProperty()
    {
        $alerts = [];

        $lowStockCount = ProductInventory::where('quantity', '<', 10)->count();
        if ($lowStockCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Low Stock Alert',
                'message' => "{$lowStockCount} products are running low on stock",
                'icon' => 'exclamation-triangle',
            ];
        }

        $outOfStockCount = ProductInventory::where('quantity', '<=', 0)->count();
        if ($outOfStockCount > 0) {
            $alerts[] = [
                'type' => 'error',
                'title' => 'Out of Stock Alert',
                'message' => "{$outOfStockCount} products are out of stock",
                'icon' => 'x-circle',
            ];
        }

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

    public function getOutOfStockProductsProperty()
    {
        return ProductInventory::with(['product.category'])
            ->where('quantity', '<=', 0)
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get()
            ->filter(function ($inventory) {
                return $inventory->product !== null;
            });
    }

    public function getCategoryValuationProperty()
    {
        return Category::select('categories.id', 'categories.name')
            ->join('products', 'products.category_id', '=', 'categories.id')
            ->join('product_inventory', 'product_inventory.product_id', '=', 'products.id')
            ->whereNull('products.deleted_at')
            ->groupBy('categories.id', 'categories.name')
            ->selectRaw('COALESCE(SUM(product_inventory.quantity * COALESCE(products.cost, 0)), 0) as total_value')
            ->orderByDesc('total_value')
            ->limit(8)
            ->get();
    }

    public function render()
    {
        return view('livewire.pages.reports.product-inventory', [
            'overviewStats' => $this->overviewStats,
            'inventoryMovements' => $this->inventoryMovements,
            'inventoryBalances' => $this->inventoryBalances,
            'totals' => $this->totals,
            'categorySubtotals' => $this->categorySubtotals,
            'supplierSubtotals' => $this->supplierSubtotals,
            'locationSubtotals' => $this->locationSubtotals,
            'topProducts' => $this->topProducts,
            'lowStockProducts' => $this->lowStockProducts,
            'recentMovements' => $this->recentMovements,
            'categoryDistribution' => $this->categoryDistribution,
            'inventoryTrends' => $this->inventoryTrends,
            'supplierPerformance' => $this->supplierPerformance,
            'alerts' => $this->alerts,
            'outOfStockProducts' => $this->outOfStockProducts,
            'categoryValuation' => $this->categoryValuation,
        ]);
    }
}
