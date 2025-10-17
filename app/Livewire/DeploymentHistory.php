<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AgentBranchAssignment;
use Illuminate\Support\Facades\DB;

class DeploymentHistory extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;

    protected $listeners = ['deploymentHistoryRefresh' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = AgentBranchAssignment::with(['agent', 'branch'])
            ->latest('assigned_at');

        if (filled($this->search)) {
            $searchTerm = '%' . addcslashes($this->search, '%_') . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->whereHas('agent', function ($agentQuery) use ($searchTerm) {
                    $agentQuery->where('agent_code', 'like', $searchTerm)
                        ->orWhere('name', 'like', $searchTerm);
                })
                ->orWhereHas('branch', function ($branchQuery) use ($searchTerm) {
                    $branchQuery->where('name', 'like', $searchTerm);
                });
            });
        }

        $assignments = $query->paginate($this->perPage);

        return view('livewire.deployment-history', [
            'assignments' => $assignments
        ]);
    }
}