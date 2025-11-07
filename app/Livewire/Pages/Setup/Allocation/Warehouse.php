<?php

namespace App\Livewire\Pages\Setup\Allocation;

use App\Models\Allocation;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Url;

class Warehouse extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $search = '';

    // Form properties
    #[Validate('required|string|max:100')]
    public $name = '';

    #[Validate('nullable|string')]
    public $description = '';

    public int $perPage = 10;

    // Edit property
    public $editingAllocationId = null;

    // Delete property
    public $deletingAllocationId = null;

    // Modal states
    public $showEditModal = false;
    public $showDeleteModal = false;

    protected $messages = [
        'name.required' => 'Warehouse allocation name is required.',
        'name.string' => 'Warehouse allocation name must be text.',
        'name.max' => 'Warehouse allocation name cannot exceed 100 characters.',
        'description.string' => 'Description must be text.',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->validate();

        Allocation::create([
            'name' => $this->name,
            'description' => $this->description,
            'type' => 'warehouse'
        ]);

        $this->resetForm();
        session()->flash('message', 'Warehouse allocation created successfully.');
    }

    public function edit($id)
    {
        $allocation = Allocation::where('type', 'warehouse')->findOrFail($id);
        $this->editingAllocationId = $id;
        $this->name = $allocation->name;
        $this->description = $allocation->description;
        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string'
        ]);

        $allocation = Allocation::where('type', 'warehouse')->findOrFail($this->editingAllocationId);
        $allocation->update([
            'name' => $this->name,
            'description' => $this->description
        ]);

        $this->resetForm();
        session()->flash('message', 'Warehouse allocation updated successfully.');
    }

    public function confirmDelete($id)
    {
        $this->deletingAllocationId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $allocation = Allocation::where('type', 'warehouse')->findOrFail($this->deletingAllocationId);
        $allocation->delete();

        $this->reset(['deletingAllocationId', 'showDeleteModal']);
        session()->flash('message', 'Warehouse allocation deleted successfully.');
    }

    public function cancel()
    {
        $this->resetForm();
    }

    protected function resetForm()
    {
        $this->reset([
            'editingAllocationId',
            'name',
            'description',
            'showEditModal',
            'showDeleteModal'
        ]);
        $this->resetValidation();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.pages.setup.allocation.warehouse', [
            'allocations' => Allocation::where('type', 'warehouse')
                ->where(function($query) {
                    $query->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('description', 'like', '%' . $this->search . '%');
                })
                ->orderBy('id', 'desc')
                ->paginate($this->perPage)
        ]);
    }
}