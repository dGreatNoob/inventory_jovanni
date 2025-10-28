<?php

namespace App\Livewire\SalesProfile;

use App\Models\SalesProfile;
use App\Models\Branch;
use App\Models\Agent;
use App\Models\Product;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public $search = '';

    // Form properties
    #[Validate('required|date')]
    public $sales_date = '';

    #[Validate('required|array|min:1')]
    public $branch_ids = [];


    #[Validate('required|exists:agents,id')]
    public $agent_id = '';

    #[Validate('nullable|string|max:500')]
    public $remarks = '';

    // Items management
    public $items = [];
    public $selectedProduct = '';
    public $quantity = 1;
    public $unitPrice = 0;

    public int $perPage = 10;

    // Edit property
    public $editingSalesId = null;

    // Delete property
    public $deletingSalesId = null;

    // Modal states
    public $showCreateModal = false;
    public $showEditModal = false;
    public $showDeleteModal = false;
    public $showViewModal = false;

    // View property
    public $viewingSalesProfile = null;

    protected $messages = [
        'sales_date.required' => 'Sales date is required.',
        'sales_date.date' => 'Sales date must be a valid date.',
        'branch_ids.required' => 'At least one branch is required.',
        'branch_ids.array' => 'Branches must be an array.',
        'branch_ids.min' => 'At least one branch must be selected.',
        'branch_ids.*' => 'One or more selected branches do not exist.',
        'agent_id.required' => 'Agent is required.',
        'agent_id.exists' => 'Selected agent does not exist.',
        'remarks.string' => 'Remarks must be text.',
        'remarks.max' => 'Remarks cannot exceed 500 characters.',
    ];

    public function mount()
    {
        $this->sales_date = now()->format('Y-m-d');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function addItem()
    {
        $this->validateOnly('selectedProduct');
        $this->validateOnly('quantity');
        $this->validateOnly('unitPrice');

        $product = Product::find($this->selectedProduct);

        // Check if product already exists in items
        $existingIndex = collect($this->items)->search(function ($item) {
            return $item['product_id'] == $this->selectedProduct;
        });

        if ($existingIndex !== false) {
            $this->items[$existingIndex]['quantity'] += $this->quantity;
            $this->items[$existingIndex]['total_price'] = $this->items[$existingIndex]['quantity'] * $this->items[$existingIndex]['unit_price'];
        } else {
            $this->items[] = [
                'product_id' => $this->selectedProduct,
                'product_name' => $product->name,
                'quantity' => $this->quantity,
                'unit_price' => $this->unitPrice,
                'total_price' => $this->quantity * $this->unitPrice
            ];
        }

        $this->reset(['selectedProduct', 'quantity', 'unitPrice']);
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function create()
    {
        $this->validate();

        if (empty($this->items)) {
            session()->flash('error', 'At least one item is required.');
            return;
        }

        DB::transaction(function () {
            $totalAmount = collect($this->items)->sum('total_price');

            $salesProfile = SalesProfile::create([
                'sales_date' => $this->sales_date,
                'agent_id' => $this->agent_id,
                'total_amount' => $totalAmount,
                'remarks' => $this->remarks
            ]);

            // Attach branches
            $salesProfile->branches()->attach($this->branch_ids);

            foreach ($this->items as $item) {
                $salesProfile->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price']
                ]);
            }
        });

        $this->resetForm();
        session()->flash('message', 'Sales profile created successfully.');
    }

    public function edit($id)
    {
        $salesProfile = SalesProfile::with('items.product')->findOrFail($id);
        $this->editingSalesId = $id;
        $this->sales_date = $salesProfile->sales_date->format('Y-m-d');
        $this->branch_ids = $salesProfile->branches->pluck('id')->toArray();
        $this->agent_id = $salesProfile->agent_id;
        $this->remarks = $salesProfile->remarks;

        $this->items = $salesProfile->items->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'total_price' => $item->total_price
            ];
        })->toArray();

        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate();

        if (empty($this->items)) {
            session()->flash('error', 'At least one item is required.');
            return;
        }

        DB::transaction(function () {
            $salesProfile = SalesProfile::findOrFail($this->editingSalesId);
            $totalAmount = collect($this->items)->sum('total_price');

            $salesProfile->update([
                'sales_date' => $this->sales_date,
                'agent_id' => $this->agent_id,
                'total_amount' => $totalAmount,
                'remarks' => $this->remarks
            ]);

            // Sync branches
            $salesProfile->branches()->sync($this->branch_ids);

            // Delete existing items and create new ones
            $salesProfile->items()->delete();

            foreach ($this->items as $item) {
                $salesProfile->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['total_price']
                ]);
            }
        });

        $this->resetForm();
        session()->flash('message', 'Sales profile updated successfully.');
    }

    public function confirmDelete($id)
    {
        $this->deletingSalesId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        $salesProfile = SalesProfile::findOrFail($this->deletingSalesId);
        $salesProfile->delete();

        $this->reset(['deletingSalesId', 'showDeleteModal']);
        session()->flash('message', 'Sales profile deleted successfully.');
    }

    public function view($id)
    {
        $this->viewingSalesProfile = SalesProfile::with(['branches', 'agent', 'items.product'])->findOrFail($id);
        $this->showViewModal = true;
    }

    public function cancel()
    {
        $this->resetForm();
    }

    protected function resetForm()
    {
        $this->reset([
            'editingSalesId',
            'sales_date',
            'branch_ids',
            'agent_id',
            'remarks',
            'items',
            'selectedProduct',
            'quantity',
            'unitPrice',
            'showCreateModal',
            'showEditModal',
            'showDeleteModal',
            'showViewModal',
            'viewingSalesProfile'
        ]);
        $this->sales_date = now()->format('Y-m-d');
        $this->quantity = 1;
        $this->unitPrice = 0;
        $this->resetValidation();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.sales-profile.index', [
            'salesProfiles' => SalesProfile::with(['branches', 'agent', 'items'])
                ->where(function($query) {
                    $query->where('sales_number', 'like', '%' . $this->search . '%')
                           ->orWhereHas('branches', function($q) {
                               $q->where('name', 'like', '%' . $this->search . '%');
                           })
                           ->orWhereHas('agent', function($q) {
                               $q->where('name', 'like', '%' . $this->search . '%');
                           });
                })
                ->orderBy('created_at', 'desc')
                ->paginate($this->perPage),
            'branches' => Branch::orderBy('name')->get(),
            'agents' => Agent::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get()
        ]);
    }
}
