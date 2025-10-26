<?php

namespace App\Livewire\Pages\RolePermission;

use Livewire\Component;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $name = '';
    public $selectedPermissions = [];
    public $permissions;
    public $showCreateModal = false;
    public $editingId = null;

    public $perPage = 10;
    public $search = '';

    public $showDeleteModal = false;
    public $deletingId = null;

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    
    public function selectAllPermissions()
    {
        $this->selectedPermissions = collect(\App\Enums\Enum\PermissionEnum::cases())
            ->pluck('value')
            ->toArray();
    }

    public function deselectAllPermissions()
    {
        $this->selectedPermissions = [];
    }
    
    public function mount()
    {
        $this->permissions = Permission::orderBy('name')->get();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function store()
    {
        $rules = [
            'name' => 'required|unique:roles,name,' . $this->editingId,
            'selectedPermissions' => 'required|array|min:1',
        ];

        $this->validate($rules);

        if ($this->editingId) {
            $role = Role::findOrFail($this->editingId);
            $role->update(['name' => $this->name]);
            $role->syncPermissions($this->selectedPermissions);
            session()->flash('message', 'Role updated successfully.');
        } else {
            $role = Role::create([
                'name' => $this->name,
                'guard_name' => 'web',
            ]);
            $role->syncPermissions($this->selectedPermissions);
            session()->flash('message', 'Role created successfully.');
        }

        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'name',
            'selectedPermissions',
            'showCreateModal',
            'editingId',
        ]);
    }

    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $this->editingId = $id;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions->pluck('name')->toArray();
        $this->showCreateModal = true;
    }

    public function confirmDelete($id)
    {
        $this->deletingId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        Role::findOrFail($this->deletingId)->delete();
        session()->flash('message', 'Role deleted successfully.');
        $this->cancelDelete();
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->deletingId = null;
    }

    public function render()
    {
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin'])) {
            return view('livewire.pages.errors.403');
        }

        $search = trim($this->search);

        $roles = Role::with('permissions')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhereHas('permissions', function ($q) use ($search) {
                          $q->where('name', 'like', "%{$search}%");
                      });
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.pages.role-permission.index', [
            'roles' => $roles,
            'permissions' => $this->permissions,
        ]);
    }
}


