<?php

namespace App\Livewire\Pages\Agent;

use Livewire\Component;
use App\Models\Agent;

class Index extends Component
{
    public $agent_code, $name, $address, $contact_num, $tin_num, $branch_designation;
    public $edit_agent_code, $edit_name, $edit_address, $edit_contact_num, $edit_tin_num, $edit_branch_designation;
    public $perPage = 10;
    public $search = '';
    public $showDeleteModal = false;
    public $showEditModal = false;
    public $deleteId = null;
    public $selectedItemId;

    public function submit()
    {
        $this->validate([
            'agent_code' => 'required|string|unique:agents,agent_code',
            'name' => 'required|string',
            'address' => 'required|string',
            'contact_num' => 'required|string',
            'tin_num' => 'required|string',
            'branch_designation' => 'nullable|string'
        ]);

        Agent::create([
            'agent_code' => $this->agent_code,
            'name' => $this->name,
            'address' => $this->address,
            'contact_num' => $this->contact_num,
            'tin_num' => $this->tin_num,
            'branch_designation' => $this->branch_designation,
        ]);

        session()->flash('message', 'Agent Profile Added Successfully.');
        $this->reset(['agent_code', 'name', 'address', 'contact_num', 'tin_num', 'branch_designation']);
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
        $this->edit_branch_designation = $agent->branch_designation;

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
            'edit_branch_designation' => 'nullable|string',
        ]);

        $agent = Agent::findOrFail($this->selectedItemId);
        $agent->update([
            'agent_code' => $this->edit_agent_code,
            'name' => $this->edit_name,
            'address' => $this->edit_address,
            'contact_num' => $this->edit_contact_num,
            'tin_num' => $this->edit_tin_num,
            'branch_designation' => $this->edit_branch_designation,
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
            'edit_agent_code',
            'edit_name',
            'edit_address',
            'edit_contact_num',
            'edit_tin_num',
            'edit_branch_designation',
        ]);
    }

    public function getDashboardStatsProperty()
    {
        $totalAgents = Agent::count();
        $newThisMonth = Agent::whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year)
                            ->count();
        $newLastMonth = Agent::whereMonth('created_at', now()->subMonth()->month)
                            ->whereYear('created_at', now()->subMonth()->year)
                            ->count();
        
        $changePercent = $newLastMonth > 0 ? 
            round((($newThisMonth - $newLastMonth) / $newLastMonth) * 100, 1) : 0;

        return [
            [
                'label' => 'Total Agents',
                'value' => number_format($totalAgents),
                'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zM6 10a2 2 0 114 0 2 2 0 11-4 0z"></path></svg>',
                'gradient' => 'from-blue-500 to-blue-600'
            ],
            [
                'label' => 'New This Month',
                'value' => number_format($newThisMonth),
                'change' => $changePercent,
                'period' => 'last month',
                'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>',
                'gradient' => 'from-green-500 to-green-600'
            ],
            [
                'label' => 'Active Profiles',
                'value' => number_format($totalAgents),
                'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                'gradient' => 'from-purple-500 to-purple-600'
            ],
            [
                'label' => 'Growth Rate',
                'value' => $changePercent >= 0 ? "+{$changePercent}%" : "{$changePercent}%",
                'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>',
                'gradient' => 'from-indigo-500 to-indigo-600'
            ]
        ];
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
            'dashboardStats' => $this->getDashboardStatsProperty()
        ]);
    }
}
