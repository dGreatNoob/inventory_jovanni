<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\AgentBranchAssignment;

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

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = AgentBranchAssignment::with(['agent', 'branch'])->latest();

        if (filled($this->search)) {
            $searchTerm = $this->search;
            $query->whereHas('agent', function ($q) use ($searchTerm) {
                $q->where('agent_code', 'like', '%' . $searchTerm . '%')
                  ->orWhere('name', 'like', '%' . $searchTerm . '%');
            });
        }

        $assignments = $query->paginate($this->perPage);

        return view('livewire.deployment-history', [
            'assignments' => $assignments
        ]);
    }
}