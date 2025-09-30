<?php

namespace App\Livewire\Pages\Setup\ItemType;

use App\Models\ItemType;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Url;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $search = '';

    // Form properties
    #[Validate('required|string|max:100|unique:item_types,name')]
    public $name = '';

    #[Validate('nullable|string')]
    public $description = '';

    public int $perPage = 10;

    // Edit property
    public $editingItemTypeId = null;

    // Delete property
    public $deletingItemTypeId = null;

    // Modal states
    public $showEditModal = false;
    public $showDeleteModal = false;

    protected $messages = [
        'name.required' => 'Item type name is required.',
        'name.string' => 'Item type name must be text.',
        'name.max' => 'Item type name cannot exceed 100 characters.',
        'name.unique' => 'This item type name already exists.',
        'description.string' => 'Description must be text.',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->validate();

        ItemType::create([
            'name' => $this->name,
            'description' => $this->description
        ]);

        $this->resetForm();
        session()->flash('message', 'Item type created successfully.');
    }

    public function edit($id)
    {
        $itemType = ItemType::findOrFail($id);
        $this->editingItemTypeId = $id;
        $this->name = $itemType->name;
        $this->description = $itemType->description;
        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:100|unique:item_types,name,' . $this->editingItemTypeId,
            'description' => 'nullable|string'
        ]);

        $itemType = ItemType::findOrFail($this->editingItemTypeId);
        $itemType->update([
            'name' => $this->name,
            'description' => $this->description
        ]);

        $this->resetForm();
        session()->flash('message', 'Item type updated successfully.');
    }

    public function confirmDelete($id)
    {
        $this->deletingItemTypeId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $itemType = ItemType::findOrFail($this->deletingItemTypeId);
        $itemType->delete();
        
        $this->reset(['deletingItemTypeId', 'showDeleteModal']);
        session()->flash('message', 'Item type deleted successfully.');
    }

    public function cancel()
    {
        $this->resetForm();
    }

    protected function resetForm()
    {
        $this->reset([
            'editingItemTypeId',
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
        return view('livewire.pages.setup.item-type.index', [
            'itemTypes' => ItemType::where('name', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%')
                ->orderBy('id', 'desc')
                ->paginate($this->perPage)
        ]);
    }
}
