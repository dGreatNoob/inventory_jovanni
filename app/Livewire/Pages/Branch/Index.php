<?php

namespace App\Livewire\Pages\Branch;

use Livewire\Component;
use App\Models\Branch;
use App\Models\Agent;
use App\Models\AgentBranchAssignment;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    public $name, $address, $contact_num, $manager_name;

    public $editData = [];
    public $perPage = 10;
    public $search = '';
    public $showDeleteModal = false;
    public $showEditModal = false;
    public $deleteId = null;
    public $selectedItemId;

    // Agent per branch dashboard properties
    public $dashboardSearch = '';
    public $statusFilter = 'all';
    public $sortBy = 'agent_count';
    public $sortDirection = 'desc';

    // Edit properties
    public $edit_name, $edit_address, $edit_contact_num, $edit_manager_name;

    public function sortByColumn($column)
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'desc';
        }
    }

    public function submit()
    {
        $this->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'contact_num' => 'nullable|string',
            'manager_name' => 'nullable|string',
        ]);

        Branch::create([
            'name' => $this->name,
            'address' => $this->address,
            'contact_num' => $this->contact_num,
            'manager_name' => $this->manager_name,
        ]);

        session()->flash('message', 'Branch Profile Added Successfully.');
        $this->reset([
            'name', 'address', 'contact_num', 'manager_name'
        ]);
    }

    public function edit($id)
    {
        $branch = Branch::findOrFail($id);

        $this->selectedItemId = $id;
        $this->edit_name = $branch->name;
        $this->edit_address = $branch->address;
        $this->edit_contact_num = $branch->contact_num;
        $this->edit_manager_name = $branch->manager_name;

        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate([
            'edit_name' => 'required|string',
            'edit_address' => 'required|string',
            'edit_contact_num' => 'nullable|string',
            'edit_manager_name' => 'nullable|string',
        ]);

        $branch = Branch::findOrFail($this->selectedItemId);
        $branch->update([
            'name' => $this->edit_name,
            'address' => $this->edit_address,
            'contact_num' => $this->edit_contact_num,
            'manager_name' => $this->edit_manager_name,
        ]);

        $this->showEditModal = false;
        session()->flash('message', 'Branch Profile Updated Successfully.');
    }

    public function confirmDelete($id)
    {
        $this->deleteId = $id;
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        Branch::findOrFail($this->deleteId)->delete();
        session()->flash('message', 'Branch Profile Deleted Successfully.');
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
            'editData'
        ]);
    }

    public function getDashboardStatsProperty()
    {
        $totalBranches = Branch::count();
        $newThisMonth = Branch::whereMonth('created_at', now()->month)
                               ->whereYear('created_at', now()->year)
                               ->count();
        $newLastMonth = Branch::whereMonth('created_at', now()->subMonth()->month)
                               ->whereYear('created_at', now()->subMonth()->year)
                               ->count();

        $changePercent = $newLastMonth > 0 ? 
            round((($newThisMonth - $newLastMonth) / $newLastMonth) * 100, 1) : 0;

        // Count active agents deployed per branch
        $branchesWithAgents = AgentBranchAssignment::whereNull('released_at')
            ->distinct('branch_id')
            ->count('branch_id');
        
        $coveragePercent = $totalBranches > 0 
            ? round(($branchesWithAgents / $totalBranches) * 100, 1) 
            : 0;

        return [
            [
                'label' => 'Total Branches',
                'value' => number_format($totalBranches),
                'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>',
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
                'label' => 'Active Branches',
                'value' => number_format($totalBranches),
                'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                'gradient' => 'from-purple-500 to-purple-600'
            ],
            [
                'label' => 'Branch Coverage',
                'value' => "{$branchesWithAgents}/{$totalBranches}",
                'subtext' => "{$coveragePercent}% covered",
                'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>',
                'gradient' => 'from-indigo-500 to-indigo-600'
            ]
        ];
    }

    public function getAgentPerBranchStatsProperty()
    {
        // Get branches with active agents eager loaded
        $branches = Branch::with(['activeAgents' => function($query) {
                $query->with('agent');
            }])
            ->when($this->dashboardSearch, function($query) {
                $query->where(function($q) {
                    $q->where('name', 'like', '%'.$this->dashboardSearch.'%')
                      ->orWhere('address', 'like', '%'.$this->dashboardSearch.'%')
                      ->orWhere('manager_name', 'like', '%'.$this->dashboardSearch.'%');
                });
            })
            ->get();

        // Calculate agent_count for each branch
        foreach ($branches as $branch) {
            $branch->agent_count = $branch->activeAgents->count();
        }

        // Filter by status
        if ($this->statusFilter === 'covered') {
            $branches = $branches->filter(function($branch) {
                return $branch->agent_count > 0;
            });
        } elseif ($this->statusFilter === 'no_agent') {
            $branches = $branches->filter(function($branch) {
                return $branch->agent_count == 0;
            });
        }

        // Sort
        if ($this->sortBy === 'agent_count') {
            $branches = $this->sortDirection === 'asc' 
                ? $branches->sortBy('agent_count')
                : $branches->sortByDesc('agent_count');
        } elseif ($this->sortBy === 'name') {
            $branches = $this->sortDirection === 'asc' 
                ? $branches->sortBy('name')
                : $branches->sortByDesc('name');
        } elseif ($this->sortBy === 'code') {
            $branches = $this->sortDirection === 'asc' 
                ? $branches->sortBy('code')
                : $branches->sortByDesc('code');
        }

        return $branches->values();
    }

    public function render()
    {
        $items = Branch::query()
            ->where(function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('address', 'like', '%'.$this->search.'%')
                    ->orWhere('contact_num', 'like', '%'.$this->search.'%')
                    ->orWhere('manager_name', 'like', '%'.$this->search.'%');
            })
            ->latest()
            ->paginate($this->perPage);

        // Total agents (all agents in the system)
        $totalAgents = Agent::count();

        // Total active agents (currently assigned to branches)
        $totalActiveAgents = AgentBranchAssignment::whereNull('released_at')
            ->distinct('agent_id')
            ->count('agent_id');

        return view('livewire.pages.branch.index', [
            'items' => $items,
            'totalAgents' => $totalAgents,
            'totalActiveAgents' => $totalActiveAgents,
        ]);
    }
}