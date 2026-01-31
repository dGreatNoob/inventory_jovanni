<?php

namespace App\Livewire\Pages\Agent;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Agent;
use App\Models\Branch;
use App\Models\AgentBranchAssignment;

class Index extends Component
{
    use WithPagination;

    public $agent_code, $name, $address, $contact_num, $sss_num;
    public $assignedBranches = [];

    public $edit_agent_code, $edit_name, $edit_address, $edit_contact_num, $edit_sss_num;

    public $perPage = 10;
    public $search = '';
    public $statusFilter = 'all';
    public $showDeleteModal = false;
    public $showEditModal = false;
    public $deleteId = null;
    public $selectedItemId;

    public $branches;

    // Assign / Manage workflow state
    public $showAssignModal = false;
    public $assign_agent_id = null;
    public $assign_branch_id = null;
    public $assign_selling_area = null; // string value from selling_area1–4
    public $sellingAreaOptions = [];    // array of strings

    public $showManagePanel = false;
    public $managePanelAgentId = null;

    public function mount()
    {
        $this->branches = Branch::all();
    }

    public function submit()
    {
        $this->validate([
            'agent_code' => 'required|string|unique:agents,agent_code',
            'name' => 'required|string',
            'address' => 'required|string',
            'contact_num' => 'required|string',
            'sss_num' => 'required|string',
        ]);

        $agent = Agent::create([
            'agent_code' => $this->agent_code,
            'name' => $this->name,
            'address' => $this->address,
            'contact_num' => $this->contact_num,
            'sss_num' => $this->sss_num,
        ]);

        foreach ($this->assignedBranches as $branchId) {
            $agent->branchAssignments()->create([
                'branch_id' => $branchId,
                'assigned_at' => now(),
            ]);
        }

        session()->flash('message', 'Agent Profile Added Successfully.');
        $this->reset(['agent_code', 'name', 'address', 'contact_num', 'sss_num', 'assignedBranches']);
    }

    public function edit($id)
    {
        $agent = Agent::findOrFail($id);
        $this->selectedItemId = $id;

        $this->edit_agent_code = $agent->agent_code;
        $this->edit_name = $agent->name;
        $this->edit_address = $agent->address;
        $this->edit_contact_num = $agent->contact_num;
        $this->edit_sss_num = $agent->sss_num;

        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate([
            'edit_agent_code' => 'required|string|unique:agents,agent_code,' . $this->selectedItemId,
            'edit_name' => 'required|string',
            'edit_address' => 'required|string',
            'edit_contact_num' => 'required|string',
            'edit_sss_num' => 'required|string',
        ]);

        $agent = Agent::findOrFail($this->selectedItemId);

        $agent->update([
            'agent_code' => $this->edit_agent_code,
            'name' => $this->edit_name,
            'address' => $this->edit_address,
            'contact_num' => $this->edit_contact_num,
            'sss_num' => $this->edit_sss_num,
        ]);

        $this->showEditModal = false;
        session()->flash('message', 'Agent Profile Updated Successfully.');
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        Agent::findOrFail($this->deleteId)->delete();
        session()->flash('message', 'Agent Profile Deleted Successfully.');
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
            'assignedBranches',

            // assign modal
            'showAssignModal',
            'assign_agent_id',
            'assign_branch_id',
            'assign_selling_area',
            'sellingAreaOptions',
            // manage panel
            'showManagePanel',
            'managePanelAgentId',
        ]);
    }

    // ------------ Assign / Manage (multi-branch) ---------------

    public function openManagePanel(int $agentId): void
    {
        $this->managePanelAgentId = $agentId;
        $this->showManagePanel = true;
    }

    public function closeManagePanel(): void
    {
        $this->showManagePanel = false;
        $this->managePanelAgentId = null;
    }

    public function openAssignModal(int $agentId): void
    {
        $this->assign_agent_id = $agentId;
        $this->assign_branch_id = null;
        $this->assign_selling_area = null;
        $this->sellingAreaOptions = [];
        $this->showAssignModal = true;
    }

    public function updatedAssignBranchId($branchId): void
    {
        $this->assign_selling_area = null;
        $this->sellingAreaOptions = [];

        if (!$branchId) return;

        $branch = Branch::find($branchId);
        if (!$branch) return;

        $this->sellingAreaOptions = collect([
                $branch->selling_area1,
                $branch->selling_area2,
                $branch->selling_area3,
                $branch->selling_area4,
            ])
            ->filter(fn ($v) => filled($v))
            ->unique()
            ->values()
            ->toArray();
    }

    public function assignToBranch(): void
    {
        $rules = [
            'assign_agent_id' => 'required|integer|exists:agents,id',
            'assign_branch_id' => 'required|integer|exists:branches,id',
        ];

        if (count($this->sellingAreaOptions) > 0) {
            $rules['assign_selling_area'] = 'required|string|in:' . implode(',', array_map(fn($s) => str_replace(',', '\,', $s), $this->sellingAreaOptions));
        } else {
            $rules['assign_selling_area'] = 'nullable|string';
        }

        $this->validate($rules);

        $agent = Agent::findOrFail($this->assign_agent_id);

        $exists = AgentBranchAssignment::where('agent_id', $agent->id)
            ->where('branch_id', $this->assign_branch_id)
            ->whereNull('released_at')
            ->exists();
        if ($exists) {
            $this->addError('assign_branch_id', 'Agent is already assigned to this branch.');
            return;
        }

        $payload = [
            'branch_id' => $this->assign_branch_id,
            'selling_area' => $this->assign_selling_area,
            'assigned_at' => now(),
        ];

        $agent->branchAssignments()->create($payload);

        $this->showAssignModal = false;
        session()->flash('message', 'Agent assigned to branch successfully.');
        $this->reset(['assign_agent_id', 'assign_branch_id', 'assign_selling_area', 'sellingAreaOptions']);
        $this->resetPage();
        $this->dispatch('deploymentHistoryRefresh');
    }

    public function releaseAssignment(int $assignmentId): void
    {
        $assignment = AgentBranchAssignment::findOrFail($assignmentId);
        $assignment->update(['released_at' => now()]);
        session()->flash('message', 'Agent released from branch successfully.');
        $this->dispatch('deploymentHistoryRefresh');
    }

    protected function isDeployed(int $agentId): bool
    {
        return AgentBranchAssignment::where('agent_id', $agentId)
            ->whereNull('released_at')
            ->exists();
    }

    public function render()
    {
        if (!auth()->user()->hasAnyPermission(['agent view'])) {
            return view('livewire.pages.errors.403');
        }

        $query = Agent::with('branchAssignments')
            ->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('agent_code', 'like', '%'.$this->search.'%')
                  ->orWhere('address', 'like', '%'.$this->search.'%')
                  ->orWhere('contact_num', 'like', '%'.$this->search.'%')
                  ->orWhere('sss_num', 'like', '%'.$this->search.'%');
            });

        // Apply status filter
        if ($this->statusFilter === 'deployed') {
            $query->whereHas('branchAssignments', function($q) {
                $q->whereNull('released_at');
            });
        } elseif ($this->statusFilter === 'active') {
            $query->whereDoesntHave('branchAssignments', function($q) {
                $q->whereNull('released_at');
            });
        }
        // 'all' shows everything - no additional filter needed

        $items = $query->latest()->paginate($this->perPage);

        $deployedAgentIds = AgentBranchAssignment::select('agent_id')
            ->whereNull('released_at')
            ->distinct()
            ->pluck('agent_id')
            ->toArray();

        $managePanelAgent = $this->managePanelAgentId
            ? Agent::with(['branchAssignments' => fn ($q) => $q->whereNull('released_at')->with('branch')])->find($this->managePanelAgentId)
            : null;

        return view('livewire.pages.agent.index', [
            'items' => $items,
            'deployedAgentIds' => $deployedAgentIds,
            'managePanelAgent' => $managePanelAgent,
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }
}