<?php

namespace App\Livewire\Pages\ProductManagement;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\InventoryLocation;
use App\Services\InventoryLocationService;
use Illuminate\Support\Str;

#[Layout('components.layouts.app')]
#[Title('Inventory Location Management')]
class InventoryLocationManagement extends Component
{
    use WithPagination;

    // Search and Filters
    public $search = '';
    public $typeFilter = '';
    public $statusFilter = '';
    public $sortBy = 'name';
    public $sortDirection = 'asc';
    public $perPage = 20;

    // Data
    public $locations = [];
    public $selectedLocations = [];
    public $showFilters = false;

    // Modals
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $showBulkActionModal = false;
    public $editingLocation = null;

    // Form Data
    public $form = [
        'name' => '',
        'type' => 'warehouse',
        'address' => '',
        'description' => '',
        'is_active' => true,
    ];

    // Bulk Actions
    public $bulkAction = '';
    public $bulkActionValue = '';

    protected $inventoryLocationService;

    public function boot(InventoryLocationService $inventoryLocationService)
    {
        $this->inventoryLocationService = $inventoryLocationService;
    }

    public function mount()
    {
        $this->loadFilters();
    }

    public function loadFilters()
    {
        // Load any filter data if needed
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedTypeFilter()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->typeFilter = '';
        $this->statusFilter = '';
        $this->resetPage();
    }

    public function getLocationsProperty()
    {
        $filters = [
            'type' => $this->typeFilter,
            'is_active' => $this->statusFilter === 'active' ? true : ($this->statusFilter === 'inactive' ? false : null),
        ];

        return $this->inventoryLocationService->searchLocations(
            $this->search,
            array_filter($filters),
            $this->perPage
        );
    }

    public function getStatsProperty()
    {
        return $this->inventoryLocationService->getLocationStats();
    }

    public function createLocation()
    {
        $this->resetForm();
        $this->editingLocation = null;
        $this->showCreateModal = true;
    }

    public function editLocation($locationId)
    {
        $this->editingLocation = InventoryLocation::findOrFail($locationId);
        $this->loadLocationData();
        $this->showEditModal = true;
    }

    public function deleteLocation($locationId)
    {
        $this->editingLocation = InventoryLocation::findOrFail($locationId);
        $this->showDeleteModal = true;
    }

    public function confirmDelete()
    {
        if ($this->editingLocation) {
            try {
                $this->inventoryLocationService->deleteLocation($this->editingLocation);
                $this->showDeleteModal = false;
                $this->editingLocation = null;
                session()->flash('message', 'Location deleted successfully.');
            } catch (\Exception $e) {
                session()->flash('error', 'Error deleting location: ' . $e->getMessage());
            }
        }
    }

    public function toggleLocationSelection($locationId)
    {
        if (in_array($locationId, $this->selectedLocations)) {
            $this->selectedLocations = array_diff($this->selectedLocations, [$locationId]);
        } else {
            $this->selectedLocations[] = $locationId;
        }
    }

    public function selectAllLocations()
    {
        $this->selectedLocations = collect($this->locations)->pluck('id')->toArray();
    }

    public function clearSelection()
    {
        $this->selectedLocations = [];
    }

    public function openBulkActionModal()
    {
        if (empty($this->selectedLocations)) {
            session()->flash('error', 'Please select locations first.');
            return;
        }
        $this->showBulkActionModal = true;
    }

    public function performBulkAction()
    {
        if (empty($this->selectedLocations) || empty($this->bulkAction)) {
            return;
        }

        try {
            switch ($this->bulkAction) {
                case 'delete':
                    foreach ($this->selectedLocations as $locationId) {
                        $location = InventoryLocation::findOrFail($locationId);
                        $this->inventoryLocationService->deleteLocation($location);
                    }
                    session()->flash('message', 'Selected locations deleted successfully.');
                    break;
                case 'activate':
                    InventoryLocation::whereIn('id', $this->selectedLocations)->update(['is_active' => true]);
                    session()->flash('message', 'Selected locations activated successfully.');
                    break;
                case 'deactivate':
                    InventoryLocation::whereIn('id', $this->selectedLocations)->update(['is_active' => false]);
                    session()->flash('message', 'Selected locations deactivated successfully.');
                    break;
            }

            $this->clearSelection();
            $this->showBulkActionModal = false;
            $this->bulkAction = '';
            $this->bulkActionValue = '';

        } catch (\Exception $e) {
            session()->flash('error', 'Error performing bulk action: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->form = [
            'name' => '',
            'type' => 'warehouse',
            'address' => '',
            'description' => '',
            'is_active' => true,
        ];
    }

    public function loadLocationData()
    {
        if ($this->editingLocation) {
            $this->form = [
                'name' => $this->editingLocation->name,
                'type' => $this->editingLocation->type,
                'address' => $this->editingLocation->address,
                'description' => $this->editingLocation->description,
                'is_active' => $this->editingLocation->is_active,
            ];
        }
    }

    public function saveLocation()
    {
        $this->validate([
            'form.name' => 'required|string|max:255',
            'form.type' => 'required|string|in:warehouse,store,office,other',
            'form.address' => 'nullable|string|max:500',
            'form.description' => 'nullable|string|max:1000',
            'form.is_active' => 'boolean',
        ]);

        try {
            if ($this->editingLocation) {
                // Update existing location
                $this->inventoryLocationService->updateLocation($this->editingLocation, $this->form);
                session()->flash('message', 'Location updated successfully.');
            } else {
                // Create new location
                $this->inventoryLocationService->createLocation($this->form);
                session()->flash('message', 'Location created successfully.');
            }

            $this->showCreateModal = false;
            $this->showEditModal = false;
            $this->resetForm();
            $this->editingLocation = null;

        } catch (\Exception $e) {
            session()->flash('error', 'Error saving location: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.pages.product-management.inventory-location-management', [
            'locations' => $this->locations,
            'stats' => $this->stats,
        ]);
    }
}
