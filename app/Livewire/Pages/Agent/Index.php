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

    public $agent_code, $name, $address, $contact_num, $tin_num;
    public $assignedBranches = [];

    public $edit_agent_code, $edit_name, $edit_address, $edit_contact_num, $edit_tin_num;

    public $perPage = 10;
    public $search = '';
    public $showDeleteModal = false;
    public $showEditModal = false;
    public $deleteId = null;
    public $selectedItemId;

    public $branches;

    // Deploy/Release workflow state
    public $showAssignModal = false;
    public $assign_agent_id = null;
    public $assign_branch_id = null;
    public $assign_subclass = null;     // string value from subclass1–4
    public $subclassOptions = [];       // array of strings

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
            'tin_num' => 'required|string',
        ]);

        $agent = Agent::create([
            'agent_code' => $this->agent_code,
            'name' => $this->name,
            'address' => $this->address,
            'contact_num' => $this->contact_num,
            'tin_num' => $this->tin_num,
        ]);

        foreach ($this->assignedBranches as $branchId) {
            $agent->branchAssignments()->create([
                'branch_id' => $branchId,
                'assigned_at' => now(),
            ]);
        }

        session()->flash('message', 'Agent Profile Added Successfully.');
        $this->reset(['agent_code', 'name', 'address', 'contact_num', 'tin_num', 'assignedBranches']);
    }

    public function edit($id)
    {
        $agent = Agent::findOrFail($id);
        $this->selectedItemId = $id;

        $this->edit_agent_code = $agent->agent_code;
        $this->edit_name = $agent->name;
        $this->edit_address = $agent->address;
        $this->edit_contact_num = $agent->contact_num;
        $this->edit_tin_num = $agent->tin_num;

        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate([
            'edit_agent_code' => 'required|string|unique:agents,agent_code,' . $this->selectedItemId,
            'edit_name' => 'required|string',
            'edit_address' => 'required|string',
            'edit_contact_num' => 'required|string',
            'edit_tin_num' => 'required|string',
        ]);

        $agent = Agent::findOrFail($this->selectedItemId);

        $agent->update([
            'agent_code' => $this->edit_agent_code,
            'name' => $this->edit_name,
            'address' => $this->edit_address,
            'contact_num' => $this->edit_contact_num,
            'tin_num' => $this->edit_tin_num,
        ]);

        // Branch assignment syncing removed from Edit modal.
        // Use Deploy/Assign modal to manage assignments.

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
            'assign_subclass',
            'subclassOptions',
        ]);
    }

    // ------------ Deploy / Release ---------------

    public function toggleDeployment(int $agentId): void
    {
        if ($this->isDeployed($agentId)) {
            $this->releaseAgent($agentId);
            session()->flash('message', 'Agent released from active branch assignments.');
            $this->dispatch('deploymentHistoryRefresh'); // <--- EMIT REFRESH
        } else {
            $this->openAssignModal($agentId);
        }
    }

    public function openAssignModal(int $agentId): void
    {
        $this->assign_agent_id = $agentId;
        $this->assign_branch_id = null;
        $this->assign_subclass = null;
        $this->subclassOptions = [];
        $this->showAssignModal = true;
    }

    // When Branch changes, build subclass options from subclass1..subclass4
    public function updatedAssignBranchId($branchId): void
    {
        $this->assign_subclass = null;
        $this->subclassOptions = [];

        if (!$branchId) return;

        $branch = Branch::find($branchId);
        if (!$branch) return;

        $this->subclassOptions = collect([
                $branch->subclass1,
                $branch->subclass2,
                $branch->subclass3,
                $branch->subclass4,
            ])
            ->filter(fn ($v) => filled($v))
            ->unique()
            ->values()
            ->toArray();
    }

    public function assignToBranch(): void
    {
        // Base rules
        $rules = [
            'assign_agent_id' => 'required|integer|exists:agents,id',
            'assign_branch_id' => 'required|integer|exists:branches,id',
        ];

        // Require subclass if there are options for the chosen branch
        if (count($this->subclassOptions) > 0) {
            $rules['assign_subclass'] = 'required|string|in:' . implode(',', array_map(fn($s) => str_replace(',', '\,', $s), $this->subclassOptions));
        } else {
            $rules['assign_subclass'] = 'nullable|string';
        }

        $this->validate($rules);

        $agent = Agent::findOrFail($this->assign_agent_id);

        // Release any current active assignments (or remove this block if multiple concurrent assignments are desired)
        AgentBranchAssignment::where('agent_id', $agent->id)
            ->whereNull('released_at')
            ->update(['released_at' => now()]);

        // Create new assignment including subclass if given
        $payload = [
            'branch_id' => $this->assign_branch_id,
            'subclass' => $this->assign_subclass, // nullable
            'assigned_at' => now(),
        ];

        $agent->branchAssignments()->create($payload);

        $this->showAssignModal = false;
        session()->flash('message', 'Agent assigned to branch successfully.');
        $this->reset(['assign_agent_id', 'assign_branch_id', 'assign_subclass', 'subclassOptions']);
        $this->resetPage(); // Ensures status updates after deploy
        $this->dispatch('deploymentHistoryRefresh'); // <--- EMIT REFRESH
    }

    protected function releaseAgent(int $agentId): void
    {
        AgentBranchAssignment::where('agent_id', $agentId)
            ->whereNull('released_at')
            ->update(['released_at' => now()]);
        $this->dispatch('deploymentHistoryRefresh'); // <--- EMIT REFRESH
    }

    protected function isDeployed(int $agentId): bool
    {
        return AgentBranchAssignment::where('agent_id', $agentId)
            ->whereNull('released_at')
            ->exists();
    }

    public function render()
    {
        // Eager load branchAssignments for up-to-date status logic
        $items = Agent::with('branchAssignments')->where(function ($q) {
                $q->where('name', 'like', '%'.$this->search.'%')
                  ->orWhere('agent_code', 'like', '%'.$this->search.'%')
                  ->orWhere('address', 'like', '%'.$this->search.'%')
                  ->orWhere('contact_num', 'like', '%'.$this->search.'%')
                  ->orWhere('tin_num', 'like', '%'.$this->search.'%');
            })
            ->latest()
            ->paginate($this->perPage);

        $deployedAgentIds = AgentBranchAssignment::select('agent_id')
            ->whereNull('released_at')
            ->distinct()
            ->pluck('agent_id')
            ->toArray();

        return view('livewire.pages.agent.index', [
            'items' => $items,
            'deployedAgentIds' => $deployedAgentIds,
        ]);
    }
}