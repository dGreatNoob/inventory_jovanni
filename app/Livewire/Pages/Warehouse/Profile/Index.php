<?php

namespace App\Livewire\Pages\PaperRollWarehouse\Profile;

use Livewire\Component;
use App\Models\RawMatProfile;

class Index extends Component
{
    public $gsm, 
           $width_size, 
           $classification, 
           $supplier, 
           $country_origin, 
           $edit_gsm, 
           $edit_width_size, 
           $edit_classification, 
           $edit_supplier, 
           $edit_country_origin;
    public $perPage = 10;
    public $search = '';
    public $showDeleteModal = false;
    public $showEditModal = false;
    public $deleteId = null;
    public $selectedItemId;
    
    public function submit()
    {
        $this->validate([
            'gsm' => 'required|string',
            'width_size' => 'required|integer',
            'classification' => 'required|string',
            'supplier' => 'required|string',
            'country_origin' => 'required|string',
        ]);

        RawMatProfile::create([
            'gsm' => $this->gsm,
            'width_size' => $this->width_size,
            'classification' => $this->classification,
            'supplier' => $this->supplier,
            'country_origin' => $this->country_origin,
        ]);
        session()->flash('message', 'Raw Material Profile Added Successfully.');
        $this->reset(['gsm', 'width_size', 'classification', 'supplier', 'country_origin']);
        
    }

    public function edit($id)
    {
        $item = RawMatProfile::findOrFail($id);
        
        $this->selectedItemId = $id;
        $this->edit_gsm = $item->gsm;
        $this->edit_width_size = $item->width_size;
        $this->edit_classification = $item->classification;
        $this->edit_supplier = $item->supplier;
        $this->edit_country_origin = $item->country_origin;

        $this->showEditModal = true;
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function cancel()
    {
        $this->resetValidation();
        $this->reset([
            'showDeleteModal',
            'showEditModal',
            'deleteId',
            'selectedItemId',
            'edit_gsm',
            'edit_width_size',
            'edit_classification',
            'edit_supplier',
            'edit_country_origin',
        ]);
    }

    public function delete()
    {
        RawMatProfile::findOrFail($this->deleteId)->delete();
        session()->flash('message', 'Raw Material Profile Deleted Successfully.');
        $this->cancel(); // resets modal
    }

    public function update()
    {
        $this->validate([
            'edit_gsm' => 'required',
            'edit_width_size' => 'required',
            'edit_classification' => 'required',
            'edit_supplier' => 'required',
            'edit_country_origin' => 'required',
        ]);

        $item = RawMatProfile::find($this->selectedItemId);
        $item->update([
            'gsm' => $this->edit_gsm,
            'width_size' => $this->edit_width_size,
            'classification' => $this->edit_classification,
            'supplier' => $this->edit_supplier,
            'country_origin' => $this->edit_country_origin,
        ]);

        $this->showEditModal = false;
        session()->flash('message', 'Item updated successfully.');
    }

    public function render()
    {
        $items = RawMatProfile::where('gsm', 'like', '%'.$this->search.'%')
            ->orWhere('classification', 'like', '%'.$this->search.'%')
            ->orWhere('supplier', 'like', '%'.$this->search.'%')
            ->orWhere('country_origin', 'like', '%'.$this->search.'%')
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.pages.paper-roll-warehouse.profile.index', compact('items'));
    }
}