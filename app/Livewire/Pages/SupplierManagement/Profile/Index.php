<?php

namespace App\Livewire\Pages\SupplierManagement\Profile;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Supplier;

class Index extends Component
{
    use WithPagination;

    public $availableCategories = [];
    public $categories = [];
    public $edit_categories = [];
    public $supplier_name, $supplier_code, $supplier_address, $contact_person, $contact_num, $email, $status;
    public $edit_name, $edit_code, $edit_address, $edit_contact_person, $edit_contact_num, $edit_email, $edit_tin_num, $edit_status;
    public $perPage = 10;
    public $search = '';
    public $showDeleteModal = false;
    public $showEditModal = false;
    public $deleteId = null;
    public $selectedItemId;

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function getTotalSuppliersProperty()
    {
        return Supplier::count();
    }

    public function getActiveSuppliersProperty()
    {
        return Supplier::where('status', 'active')->count();
    }

    public function getPendingSuppliersProperty()
    {
        return Supplier::where('status', 'pending')->count();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function mount()
    {
        $this->availableCategories = Supplier::CATEGORIES;
    }

    /**
     * ✅ Dynamic validation rules for create/edit
     */
    public function rules()
    {
        if ($this->selectedItemId) {
            // 🔹 Edit mode
            return [
                'edit_name' => 'required|string|max:255',
                'edit_code' => 'required|string|max:50|unique:suppliers,code,' . $this->selectedItemId,
                'edit_address' => 'required|string|max:500',
                'edit_contact_person' => 'required|string|max:255',
                'edit_contact_num' => ['required', 'regex:/^[0-9+\-\(\)\s]+$/'],
                'edit_email' => 'required|email:rfc,dns|unique:suppliers,email,' . $this->selectedItemId,
                'edit_tin_num' => 'nullable|string|max:255',
                'edit_status' => 'required|string',
                'edit_categories' => 'required|array|min:1',
                'edit_categories.*' => 'string|in:' . implode(',', Supplier::CATEGORIES),
            ];
        }

        // 🔹 Create mode
        return [
            'supplier_name' => 'required|string|max:255',
            'supplier_code' => 'required|string|max:50|unique:suppliers,code',
            'supplier_address' => 'required|string|max:500',
            'contact_person' => 'required|string|max:255',
            'contact_num' => ['required', 'regex:/^[0-9+\-\(\)\s]+$/'],
            'email' => 'required|email:rfc,dns|unique:suppliers,email',
            'categories' => 'required|array|min:1',
            'categories.*' => 'string|in:' . implode(',', Supplier::CATEGORIES),
        ];
    }

    /**
     * ✅ Create new supplier
     */
    public function submit()
    {
        $this->validate(); // runs create-mode rules

        Supplier::create([
            'name' => $this->supplier_name,
            'code' => $this->supplier_code,
            'address' => $this->supplier_address,
            'contact_person' => $this->contact_person,
            'contact_num' => $this->contact_num,
            'email' => $this->email,
            'status' => 'pending',
            'categories' => $this->categories,
        ]);

        session()->flash('message', 'Supplier Profile Added Successfully.');
        $this->reset([
            'supplier_name',
            'supplier_code',
            'supplier_address',
            'contact_person',
            'contact_num',
            'email',
            'categories',
        ]);
    }

    /**
     * ✅ Load supplier into edit modal
     */
    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);

        $this->selectedItemId = $id;
        $this->edit_name = $supplier->name;
        $this->edit_code = $supplier->code;
        $this->edit_address = $supplier->address;
        $this->edit_contact_person = $supplier->contact_person;
        $this->edit_contact_num = $supplier->contact_num;
        $this->edit_email = $supplier->email;
        $this->edit_tin_num = $supplier->tin_num;
        $this->edit_status = $supplier->status;
        $this->edit_categories = $supplier->categories ?? [];

        $this->showEditModal = true;
    }

    /**
     * ✅ Update supplier record safely
     */
    public function update()
    {
        $this->validate(); // runs edit-mode rules

        $supplier = Supplier::findOrFail($this->selectedItemId);

        $supplier->update([
            'name' => $this->edit_name,
            'code' => $this->edit_code,
            'address' => $this->edit_address,
            'contact_person' => $this->edit_contact_person,
            'contact_num' => $this->edit_contact_num,
            'email' => $this->edit_email,
            'tin_num' => $this->edit_tin_num,
            'status' => $this->edit_status,
            'categories' => $this->edit_categories,
        ]);

        $this->showEditModal = false;
        session()->flash('message', 'Supplier profile updated successfully.');
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        Supplier::findOrFail($this->deleteId)->delete();

        session()->flash('message', 'Supplier profile deleted successfully.');
        $this->cancel();
    }

    public function cancel()
    {
        $this->resetValidation();
        $this->reset([
            'showDeleteModal',
            'showEditModal',
            'deleteId',
            'selectedItemId',
            'supplier_name',
            'supplier_code',
            'supplier_address',
            'contact_person',
            'contact_num',
            'email',
        ]);
    }

    public function render()
    {
        $search = trim($this->search);

        $items = Supplier::when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhere('contact_num', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereJsonContains('categories', $search)
                    ->orWhereRaw('LOWER(JSON_EXTRACT(categories, "$")) LIKE ?', ['%' . strtolower($search) . '%']);
            });
        })
        ->latest()
        ->paginate($this->perPage);

        return view('livewire.pages.supplier-management.profile.index', compact('items'));
    }
}

