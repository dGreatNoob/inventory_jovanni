<?php

namespace App\Livewire\Pages\SupplierManagement\Profile;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Supplier;

class Index extends Component
{
    use WithPagination;

    public $name, $address, $contact_num, $tin_num;
    public $edit_name, $edit_address, $edit_contact_num, $edit_tin_num;
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

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function submit()
    {
        $this->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'contact_num' => 'required|string',
            'tin_num' => 'required|string'
        ]);

        Supplier::create([
            'name' => $this->name,
            'address' => $this->address,
            'contact_num' => $this->contact_num,
            'tin_num' => $this->tin_num
        ]);

        session()->flash('message', 'Supplier Profile Added Successfully.');
        $this->reset(['name', 'address', 'contact_num', 'tin_num']);
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);

        $this->selectedItemId = $id;
        $this->edit_name = $supplier->name;
        $this->edit_address = $supplier->address;
        $this->edit_contact_num = $supplier->contact_num;
        $this->edit_tin_num = $supplier->tin_num;

        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate([
            'edit_name' => 'required|string',
            'edit_address' => 'required|string',
            'edit_contact_num' => 'required|string',
            'edit_tin_num' => 'required|string'
        ]);

        $supplier = Supplier::findOrFail($this->selectedItemId);
        $supplier->update([
            'name' => $this->edit_name,
            'address' => $this->edit_address,
            'contact_num' => $this->edit_contact_num,
            'tin_num' => $this->edit_tin_num
        ]);

        $this->showEditModal = false;
        session()->flash('message', 'Supplier Profile Updated Successfully.');
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $supplier = Supplier::findOrFail($this->deleteId);
        $supplier->delete();

        session()->flash('message', 'Supplier Profile Deleted Successfully.');
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
            'edit_name',
            'edit_address',
            'edit_contact_num',
            'edit_tin_num',
        ]);
    }

    public function render()
    {
        $items = Supplier::when($this->search, function ($query) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('address', 'like', '%' . $this->search . '%')
                  ->orWhere('contact_num', 'like', '%' . $this->search . '%')
                  ->orWhere('tin_num', 'like', '%' . $this->search . '%');
            });
        })
        ->latest()
        ->paginate($this->perPage);

        return view('livewire.pages.supplier-management.profile.index', compact('items'));
    }
}
