<?php

namespace App\Livewire\Pages\Setup\Department;

use App\Models\Department;
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
    #[Validate('required|string|max:100|unique:departments,name')]
    public $name = '';

    #[Validate('nullable|string')]
    public $description = '';

    public int $perPage = 10;

    // Edit property
    public $editingDepartmentId = null;

    // Delete property
    public $deletingDepartmentId = null;

    // Modal states
    public $showEditModal = false;
    public $showDeleteModal = false;

    protected $messages = [
        'name.required' => 'Department name is required.',
        'name.string' => 'Department name must be text.',
        'name.max' => 'Department name cannot exceed 100 characters.',
        'name.unique' => 'This department name already exists.',
        'description.string' => 'Description must be text.',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->validate();

        Department::create([
            'name' => $this->name,
            'description' => $this->description
        ]);

        $this->resetForm();
        session()->flash('message', 'Department created successfully.');
    }

    public function edit($id)
    {
        $department = Department::findOrFail($id);
        $this->editingDepartmentId = $id;
        $this->name = $department->name;
        $this->description = $department->description;
        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:100|unique:departments,name,' . $this->editingDepartmentId,
            'description' => 'nullable|string'
        ]);

        $department = Department::findOrFail($this->editingDepartmentId);
        $department->update([
            'name' => $this->name,
            'description' => $this->description
        ]);

        $this->resetForm();
        session()->flash('message', 'Department updated successfully.');
    }

    public function confirmDelete($id)
    {
        $this->deletingDepartmentId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $department = Department::findOrFail($this->deletingDepartmentId);
        $department->delete();
        
        $this->reset(['deletingDepartmentId', 'showDeleteModal']);
        session()->flash('message', 'Department deleted successfully.');
    }

    public function cancel()
    {
        $this->resetForm();
    }

    protected function resetForm()
    {
        $this->reset([
            'editingDepartmentId',
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
        return view('livewire.pages.setup.department.index', [
            'departments' => Department::where('name', 'like', '%' . $this->search . '%')
                ->orWhere('description', 'like', '%' . $this->search . '%')
                ->orderBy('id', 'desc')
                ->paginate($this->perPage)
        ]);
    }
}
