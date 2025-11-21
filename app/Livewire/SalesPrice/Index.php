<?php

namespace App\Livewire\SalesPrice;

use App\Models\SalesPrice;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $showCreateModal = false;
    public $showEditModal = false;
    public $editingId = null;

    public $description = '';
    public $less_percentage = '';
    public $pricing_note = '';

    protected $rules = [
        'description' => 'required|string|max:255',
        'less_percentage' => 'required|numeric|min:0|max:100',
        'pricing_note' => 'nullable|string|max:1000',
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal()
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function openEditModal($id)
    {
        $this->editingId = $id;
        $salesPrice = SalesPrice::find($id);
        $this->description = $salesPrice->description;
        $this->less_percentage = $salesPrice->less_percentage;
        $this->pricing_note = $salesPrice->pricing_note;
        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editingId = null;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->description = '';
        $this->less_percentage = '';
        $this->pricing_note = '';
        $this->resetValidation();
    }

    public function create()
    {
        $this->validate();

        SalesPrice::create([
            'description' => $this->description,
            'less_percentage' => $this->less_percentage,
            'pricing_note' => $this->pricing_note,
        ]);

        session()->flash('message', 'Sales Price created successfully.');
        $this->closeCreateModal();
    }

    public function update()
    {
        $this->validate();

        $salesPrice = SalesPrice::find($this->editingId);
        $salesPrice->update([
            'description' => $this->description,
            'less_percentage' => $this->less_percentage,
            'pricing_note' => $this->pricing_note,
        ]);

        session()->flash('message', 'Sales Price updated successfully.');
        $this->closeEditModal();
    }

    public function delete($id)
    {
        SalesPrice::find($id)->delete();
        session()->flash('message', 'Sales Price deleted successfully.');
    }

    public function render()
    {
        $salesPrices = SalesPrice::query()
            ->when($this->search, function ($query) {
                $query->where('description', 'like', '%' . $this->search . '%');
            })
            ->paginate($this->perPage);

        return view('livewire.sales-price.index', [
            'salesPrices' => $salesPrices,
        ]);
    }
}
