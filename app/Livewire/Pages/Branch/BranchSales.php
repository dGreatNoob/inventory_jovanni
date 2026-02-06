<?php

namespace App\Livewire\Pages\Branch;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\BranchCustomerSale;
use App\Models\Branch;
use App\Models\Agent;

class BranchSales extends Component
{
    use WithPagination;

    public $search = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $selectedBranchId = '';
    public $selectedAgentId = '';
    public $showDetailsModal = false;
    public $selectedSale = null;

    public function mount()
    {
        // Set default date range to current month
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function updatingSelectedBranchId()
    {
        $this->resetPage();
    }

    public function updatingSelectedAgentId()
    {
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->selectedBranchId = '';
        $this->selectedAgentId = '';
        $this->resetPage();
    }

    public function viewSaleDetails($saleId)
    {
        $this->selectedSale = BranchCustomerSale::with(['branch', 'agent', 'items.product'])
            ->findOrFail($saleId);
        $this->showDetailsModal = true;
    }

    public function closeDetailsModal()
    {
        $this->showDetailsModal = false;
        $this->selectedSale = null;
    }

    public function render()
    {
        $query = BranchCustomerSale::with(['branch', 'agent', 'items'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($this->search) {
            $query->where(function($q) {
                $q->where('ref_no', 'like', '%' . $this->search . '%')
                  ->orWhereHas('branch', function($subq) {
                      $subq->where('name', 'like', '%' . $this->search . '%');
                  })
                  ->orWhereHas('agent', function($subq) {
                      $subq->where('name', 'like', '%' . $this->search . '%')
                           ->orWhere('agent_code', 'like', '%' . $this->search . '%');
                  });
            });
        }

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        if ($this->selectedBranchId) {
            $query->where('branch_id', $this->selectedBranchId);
        }

        if ($this->selectedAgentId) {
            $query->where('agent_id', $this->selectedAgentId);
        }

        $sales = $query->paginate(20);

        // Get branches and agents for filters
        $branches = Branch::orderBy('name')->get();
        $agents = Agent::orderBy('name')->get();

        return view('livewire.pages.branch.branch-sales', [
            'sales' => $sales,
            'branches' => $branches,
            'agents' => $agents,
        ]);
    }
}
