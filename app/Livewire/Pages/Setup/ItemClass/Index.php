<?php

namespace App\Livewire\Pages\Setup\ItemClass;

use App\Models\ItemClass;
use Livewire\WithPagination;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Url;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $search = '';

    // Form properties
    #[Validate('required|string|max:100|unique:item_classes,name')]
    public $name = '';

    #[Validate('nullable|string')]
    public $description = '';

    public int $perPage = 10;

    // Edit property
    public $editingItemClassId = null;

    // Delete property
    public $deletingItemClassId = null;

    // Modal states
    public $showEditModal = false;
    public $showDeleteModal = false;

    protected $messages = [
        'name.required' => 'Item class name is required.',
        'name.string' => 'Item class name must be text.',
        'name.max' => 'Item class name cannot exceed 100 characters.',
        'name.unique' => 'This item class name already exists.',
        'description.string' => 'Description must be text.',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->validate();

        ItemClass::create([
            'name' => $this->name,
            'description' => $this->description
        ]);

        $this->resetForm();
        session()->flash('message', 'Item class created successfully.');
    }

    public function edit($id)
    {
        $itemClass = ItemClass::findOrFail($id);
        $this->editingItemClassId = $id;
        $this->name = $itemClass->name;
        $this->description = $itemClass->description;
        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:100|unique:item_classes,name,' . $this->editingItemClassId,
            'description' => 'nullable|string',
        ]);

        $itemClass = ItemClass::findOrFail($this->editingItemClassId);
        $itemClass->update([
            'name' => $this->name,
            'description' => $this->description
        ]);

        $this->resetForm();
        session()->flash('message', 'Item class updated successfully.');
    }

    public function confirmDelete($id)
    {
        $this->deletingItemClassId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $itemClass = ItemClass::findOrFail($this->deletingItemClassId);
        $itemClass->delete();
        $this->resetForm();
        session()->flash('message', 'Item class deleted successfully.');
    }

    public function cancel()
    {
        $this->resetForm();
    }

    protected function resetForm()
    {
        $this->reset([
            'editingItemClassId',
            'deletingItemClassId',
            'name',
            'description',
            'showEditModal',
            'showDeleteModal',
        ]);
        $this->resetValidation();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = ItemClass::query();
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
        }
        return view('livewire.pages.setup.item-class.index', [
            'itemClasses' => $query->orderBy('id', 'desc')->paginate($this->perPage)
        ]);
    }
}
