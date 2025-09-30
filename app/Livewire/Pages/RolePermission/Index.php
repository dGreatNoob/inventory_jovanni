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
    public $deletingId = null;

    public function mount()
    {
        $this->permissions = Permission::orderBy('name')->get();
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
            'selectedPermissions' => 'array',
        ];

        $this->validate($rules);

        if ($this->editingId) {
            $role = Role::findOrFail($this->editingId);
            $role->update([
                'name' => $this->name,
            ]);
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
    }

    public function delete()
    {
        Role::findOrFail($this->deletingId)->delete();
        session()->flash('message', 'Role deleted successfully.');
    }

    public function render()
    {
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin'])) {
            return view('livewire.pages.errors.403');
        }
        return view('livewire.pages.role-permission.index', [
            'roles' => Role::with('permissions')->paginate(10),
            'permissions' => $this->permissions,
        ]);
    }
}
