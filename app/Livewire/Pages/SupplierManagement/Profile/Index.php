<?php

namespace App\Livewire\Pages\SupplierManagement\Profile;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Supplier;

class Index extends Component
{
    use WithPagination;

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
        return \App\Models\Supplier::count();
    }

    public function getActiveSuppliersProperty()
    {
        return \App\Models\Supplier::where('status', 'active')->count();
    }

    public function getPendingSuppliersProperty()
    {
        return \App\Models\Supplier::where('status', 'pending')->count();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function submit()
    {
        $this->validate([
            'supplier_name' => 'required|string',
            'supplier_code' => 'required|string',
            'supplier_address' => 'required|string',
            'contact_person' => 'required|string',
            'contact_num' => 'required|string',
            'email' => 'required|email',
            'categories' => 'required|array',
            'categories.*' => 'string',
        ]);

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
        $this->reset(['supplier_name', 'supplier_code', 'supplier_address', 'contact_person', 'contact_num', 'email', 'categories']);
    }

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

    public function update()
    {
        $this->validate([
            'edit_name' => 'required|string',
            'edit_code' => 'required|string',
            'edit_address' => 'required|string',
            'edit_contact_person' => 'required|string',
            'edit_contact_num' => 'required|string',
            'edit_email' => 'required|email',
            'edit_tin_num' => 'nullable|string',
            'edit_status' => 'required|string',
            'edit_categories' => 'required|array',
            'edit_categories.*' => 'string',
        ]);

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

    // 🔴 Delete Supplier
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

    // Cancel resets modal states
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

    // 🔍 Render and filter suppliers
    public function render()
    {
        $items = Supplier::when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('address', 'like', '%' . $this->search . '%')
                  ->orWhere('contact_person', 'like', '%' . $this->search . '%')
                  ->orWhere('contact_num', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        })
        ->latest()
        ->paginate($this->perPage);

        return view('livewire.pages.supplier-management.profile.index', compact('items'));
    }
}
