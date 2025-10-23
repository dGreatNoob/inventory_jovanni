<?php

namespace App\Livewire\Pages\PaperRollWarehouse\Inventory;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use Livewire\Component;
use App\Models\RawMatOrder;
use App\Models\RawMatInv;


class Index extends Component
{
    public $sp_num, $supplier_num, $age, $weight, $rem_weight, $raw_mat_order_id;
    public $first_remarks = 'pending';
    public $final_remarks = 'pending';
    public $edit_spc_number, $edit_supplier_number, $edit_age, $edit_weight, $edit_remaining_weight, $edit_first_remarks, $edit_final_remarks, $edit_raw_mat_order_id;
    public $perPage = 10;
    public $search = '';
    public $showDeleteModal = false;
    public $showEditModal = false;
    public $deleteId = null;
    public $selectedItemId;

    public function getRawMatOrderProperty()
    {
        return RawMatOrder::orderBy('id')->get();
    }

    public function submit()
    {
        $this->validate([
            'sp_num' => 'required|string',
            'supplier_num' => 'required|numeric',
            'age' => 'required|numeric',
            'weight' => 'required|numeric',
            'rem_weight' => 'required|numeric'
        ]);
        
        RawMatInv::create([
            'sp_num' => $this->sp_num,
            'supplier_num' => $this->supplier_num,
            'status' => 'pending',
            'age' => $this->age,
            'weight' => $this->weight,
            'rem_weight' => $this->rem_weight,
            'first_remarks' => $this->first_remarks,
            'final_remarks' => $this->final_remarks,
            'raw_mat_order_id' => $this->raw_mat_order_id
        ]);

        session()->flash('message', 'Raw Mats Added Successfully.');
        $this->reset(['sp_num', 'supplier_num', 'age', 'weight', 'rem_weight', 'first_remarks', 'final_remarks']);
    }

    public function edit($id)
    {
        $rawmatinv = RawMatInv::findOrFail($id);

        $this->selectedItemId = $id;
        $this->edit_spc_number = $rawmatinv->sp_num;
        $this->edit_supplier_number = $rawmatinv->supplier_num;
        $this->edit_age = $rawmatinv->age;
        $this->edit_weight = $rawmatinv->weight;
        $this->edit_remaining_weight = $rawmatinv->rem_weight;
        $this->edit_first_remarks = $rawmatinv->first_remarks;
        $this->edit_final_remarks = $rawmatinv->final_remarks;
        $this->edit_raw_mat_order_id = $rawmatinv->raw_mat_order_id;

        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate([
            'edit_spc_number' => 'required|numeric',
            'edit_supplier_number' => 'nullable',
            'edit_age' => 'required|numeric',
            'edit_weight' => 'nullable',
            'edit_remaining_weight' => 'nullable',
            'edit_first_remarks' => 'required|string',
            'edit_final_remarks' => 'required|string',
            'edit_raw_mat_order_id' => 'required',
        ]);

        $rawmatinv = RawMatInv::findOrFail($this->selectedItemId);
        $rawmatinv->update([
            'sp_num' => $this->edit_spc_number,
            'supplier_num' => $this->edit_supplier_number ?: null,
            'age' => $this->edit_age, 
            'weight' => $this->edit_weight ?: null,
            'rem_weight' => $this->edit_remaining_weight ?: null,
            'first_remarks' => $this->edit_first_remarks,
            'final_remarks' => $this->edit_final_remarks,
            'raw_mat_order_id' => $this->edit_raw_mat_order_id
        ]);

        $this->showEditModal = false;
        session()->flash('message', 'Inventory Updated Successfully.');
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        RawMatInv::findOrFail($this->deleteId)->delete();
        session()->flash('message', 'Inventory Deleted Successfully.');
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
            'edit_spc_number',
            'edit_supplier_number',
            'edit_age',
            'edit_weight',
            'edit_remaining_weight',
            'edit_first_remarks',
            'edit_final_remarks',
            'edit_raw_mat_order_id'
        ]);
    }

    public function render()
    {
        $items = RawMatInv::where('spc_num', 'like', '%' . $this->search . '%')
            ->orWhere('supplier_num', 'like', '%' . $this->search . '%')
            ->orWhere('remarks', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.pages.paper-roll-warehouse.inventory.index', [
            'rawmatorders' => $this->getRawMatOrderProperty(),
            'items' => $items
        ]);
    }

}
