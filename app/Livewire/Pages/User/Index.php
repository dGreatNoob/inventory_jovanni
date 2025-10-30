<?php

namespace App\Livewire\Pages\User;

use Livewire\Component;
use App\Models\User;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Hash;
use App\Models\Department;
use Spatie\Permission\Models\Role;

class Index extends Component
{



    use WithPagination;

    #[Url(as: 'q')]
    public $search = '';

    public int $perPage = 10;

    // Form properties
    #[Validate('required|string|max:100')]
    public $name = '';

    #[Validate('required|email|unique:users,email')]
    public $email = '';

    #[Validate('nullable|string|min:8|confirmed')]
    public $password = '';

    #[Validate('required|exists:departments,id')]
    public $department_id = null;

    public $password_confirmation = '';
    #[Validate('required|integer|exists:roles,id')]
    public $selectedRole = '';

    public $roles;

    // Modal states
    public $showEditModal = false;
    public $showDeleteModal = false;

    // Identifiers
    public $editingUserId = null;
    public $deletingUserId = null;

    protected $messages = [
        'name.required' => 'Name is required.',
        'email.required' => 'Email is required.',
        'email.email' => 'Email must be valid.',
        'email.unique' => 'Email already taken.',
        'password.min' => 'Password must be at least 8 characters.',
        'password.confirmed' => 'Password confirmation does not match.',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    #[Computed()]
    public function departments()
    {

        return Department::orderBy('name')->get();
    }

    public function mount()
    {
        $this->roles = Role::orderBy('name')->get();
    }

    public function create()
    {
        $this->validate();

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'department_id' => $this->department_id,
        ]);
        $role = Role::findOrFail($this->selectedRole);
        $user->assignRole($role->name);
        $this->resetForm();
        session()->flash('message', 'User created successfully.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        $this->editingUserId = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = '';
        $this->password_confirmation = '';
        $this->department_id = $user->department_id;

        $roleName = $user->getRoleNames()->first();
        $this->selectedRole = Role::where('name', $roleName)->value('id');

        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $this->editingUserId,
            'department_id' => 'nullable|exists:departments,id',
            'password' => 'nullable|string|min:8|confirmed',
            'selectedRole' => 'required|integer|exists:roles,id',
        ]);

        $user = User::findOrFail($this->editingUserId);

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'department_id' => $this->department_id,
        ];

        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        $user->update($data);

        // ✅ Properly sync role
        $role = Role::findOrFail($this->selectedRole);
        $user->syncRoles([$role->name]);

        $this->resetForm();
        session()->flash('message', 'User updated successfully.');
    }



    public function confirmDelete($id)
    {
        $this->deletingUserId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        User::findOrFail($this->deletingUserId)->delete();

        $this->reset(['deletingUserId', 'showDeleteModal']);
        session()->flash('message', 'User deleted successfully.');
    }

    public function cancel()
    {
        $this->resetForm();
    }

    protected function resetForm()
    {
        $this->reset([
            'editingUserId',
            'deletingUserId',
            'name',
            'email',
            'password',
            'password_confirmation',
            'showEditModal',
            'showDeleteModal',
        ]);
        $this->resetValidation();
    }

    public function render()
    {
        if (!auth()->user()->hasAnyPermission(['user view'])) {
            return view('livewire.pages.errors.403');
        }

        $search = trim($this->search);

        $users = User::with(['department', 'roles'])
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('department', function ($dept) use ($search) {
                            $dept->where('name', 'like', "%{$search}%");
                        })
                        ->orWhereHas('roles', function ($role) use ($search) {
                            $role->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.pages.user.index', [
            'users' => $users,
            'roles' => $this->roles = Role::orderBy('name')->get(),
        ]);
    }
}

