<?php

namespace App\Livewire\Components;

use Livewire\Component;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\InventoryLocation;
use App\Models\Product;

class AdvancedSearch extends Component
{
    // Search query
    public $search = '';
    public $searchFields = ['name', 'sku', 'barcode', 'remarks'];
    
    // Filters
    public $categoryFilter = '';
    public $supplierFilter = '';
    public $locationFilter = '';
    public $priceMin = '';
    public $priceMax = '';
    public $costMin = '';
    public $costMax = '';
    public $stockLevelFilter = '';
    public $statusFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    
    // Advanced options
    public $exactMatch = false;
    public $includeInactive = false;
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 20;
    
    // UI state
    public $showAdvancedFilters = false;
    public $showSearchFields = false;
    
    // Data for dropdowns
    public $categories = [];
    public $suppliers = [];
    public $locations = [];
    
    // Search field options
    public $availableSearchFields = [
        'name' => 'Product Name',
        'sku' => 'SKU',
        'barcode' => 'Barcode',
        'remarks' => 'Remarks',
        'supplier_code' => 'Supplier SKU',
        'price_note' => 'Price Note',
    ];

    protected $listeners = ['resetSearch' => 'resetFilters'];

    public function mount()
    {
        $this->loadFilterData();
    }

    public function loadFilterData()
    {
        $this->categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'parent_id']);
            
        $this->suppliers = Supplier::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name']);
            
        $this->locations = InventoryLocation::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'type']);
    }

    public function updatedSearch()
    {
        $this->emit('searchUpdated', $this->getSearchParams());
    }

    public function updatedCategoryFilter()
    {
        $this->emit('filtersUpdated', $this->getSearchParams());
    }

    public function updatedSupplierFilter()
    {
        $this->emit('filtersUpdated', $this->getSearchParams());
    }

    public function updatedLocationFilter()
    {
        $this->emit('filtersUpdated', $this->getSearchParams());
    }

    public function updatedPriceMin()
    {
        $this->emit('filtersUpdated', $this->getSearchParams());
    }

    public function updatedPriceMax()
    {
        $this->emit('filtersUpdated', $this->getSearchParams());
    }

    public function updatedCostMin()
    {
        $this->emit('filtersUpdated', $this->getSearchParams());
    }

    public function updatedCostMax()
    {
        $this->emit('filtersUpdated', $this->getSearchParams());
    }

    public function updatedStockLevelFilter()
    {
        $this->emit('filtersUpdated', $this->getSearchParams());
    }

    public function updatedStatusFilter()
    {
        $this->emit('filtersUpdated', $this->getSearchParams());
    }

    public function updatedDateFrom()
    {
        $this->emit('filtersUpdated', $this->getSearchParams());
    }

    public function updatedDateTo()
    {
        $this->emit('filtersUpdated', $this->getSearchParams());
    }

    public function updatedExactMatch()
    {
        $this->emit('filtersUpdated', $this->getSearchParams());
    }

    public function updatedIncludeInactive()
    {
        $this->emit('filtersUpdated', $this->getSearchParams());
    }

    public function updatedSortBy()
    {
        $this->emit('filtersUpdated', $this->getSearchParams());
    }

    public function updatedSortDirection()
    {
        $this->emit('filtersUpdated', $this->getSearchParams());
    }

    public function updatedPerPage()
    {
        $this->emit('filtersUpdated', $this->getSearchParams());
    }

    public function toggleAdvancedFilters()
    {
        $this->showAdvancedFilters = !$this->showAdvancedFilters;
    }

    public function toggleSearchFields()
    {
        $this->showSearchFields = !$this->showSearchFields;
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->categoryFilter = '';
        $this->supplierFilter = '';
        $this->locationFilter = '';
        $this->priceMin = '';
        $this->priceMax = '';
        $this->costMin = '';
        $this->costMax = '';
        $this->stockLevelFilter = '';
        $this->statusFilter = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->exactMatch = false;
        $this->includeInactive = false;
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->perPage = 20;
        
        $this->emit('filtersCleared');
    }

    public function resetFilters()
    {
        $this->clearFilters();
    }

    public function getSearchParams()
    {
        return [
            'search' => $this->search,
            'search_fields' => $this->searchFields,
            'category_filter' => $this->categoryFilter,
            'supplier_filter' => $this->supplierFilter,
            'location_filter' => $this->locationFilter,
            'price_min' => $this->priceMin,
            'price_max' => $this->priceMax,
            'cost_min' => $this->costMin,
            'cost_max' => $this->costMax,
            'stock_level_filter' => $this->stockLevelFilter,
            'status_filter' => $this->statusFilter,
            'date_from' => $this->dateFrom,
            'date_to' => $this->dateTo,
            'exact_match' => $this->exactMatch,
            'include_inactive' => $this->includeInactive,
            'sort_by' => $this->sortBy,
            'sort_direction' => $this->sortDirection,
            'per_page' => $this->perPage,
        ];
    }

    public function toggleSearchField($field)
    {
        if (in_array($field, $this->searchFields)) {
            $this->searchFields = array_diff($this->searchFields, [$field]);
        } else {
            $this->searchFields[] = $field;
        }
        
        $this->emit('searchUpdated', $this->getSearchParams());
    }

    public function getActiveFiltersCount()
    {
        $count = 0;
        
        if ($this->categoryFilter) $count++;
        if ($this->supplierFilter) $count++;
        if ($this->locationFilter) $count++;
        if ($this->priceMin) $count++;
        if ($this->priceMax) $count++;
        if ($this->costMin) $count++;
        if ($this->costMax) $count++;
        if ($this->stockLevelFilter) $count++;
        if ($this->statusFilter) $count++;
        if ($this->dateFrom) $count++;
        if ($this->dateTo) $count++;
        if ($this->exactMatch) $count++;
        if ($this->includeInactive) $count++;
        
        return $count;
    }

    public function render()
    {
        return view('livewire.components.advanced-search');
    }
}
