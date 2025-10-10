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
    public $edit_assignedBranches = [];

    public $perPage = 10;
    public $search = '';
    public $showDeleteModal = false;
    public $showEditModal = false;
    public $deleteId = null;
    public $selectedItemId;

    public $branches; // All branches

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
        $this->edit_assignedBranches = $agent->currentBranches->pluck('id')->toArray();

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

        // Sync branches (release old ones not selected, add new ones)
        $current = $agent->branchAssignments()->whereNull('released_at')->pluck('branch_id')->toArray();
        $toRelease = array_diff($current, $this->edit_assignedBranches);
        $toAssign = array_diff($this->edit_assignedBranches, $current);

        AgentBranchAssignment::where('agent_id', $agent->id)
            ->whereIn('branch_id', $toRelease)
            ->update(['released_at' => now()]);

        foreach ($toAssign as $branchId) {
            $agent->branchAssignments()->create([
                'branch_id' => $branchId,
                'assigned_at' => now(),
            ]);
        }

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
            'edit_assignedBranches',
        ]);
    }

    public function render()
    {
        $items = Agent::where('name', 'like', '%'.$this->search.'%')
            ->orWhere('agent_code', 'like', '%'.$this->search.'%')
            ->orWhere('address', 'like', '%'.$this->search.'%')
            ->orWhere('contact_num', 'like', '%'.$this->search.'%')
            ->orWhere('tin_num', 'like', '%'.$this->search.'%')
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.pages.agent.index', [
            'items' => $items,
        ]);
    }
}