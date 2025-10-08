<?php

namespace App\Livewire\Pages\Branch;

use Livewire\Component;
use App\Models\Branch;

class Index extends Component
{

    public $name, $address, $contact_num, $manager_name;
    public $edit_name, $edit_address, $edit_contact_num, $edit_manager_name;
    public $perPage = 10;
    public $search = '';
    public $showDeleteModal = false;
    public $showEditModal = false;
    public $deleteId = null;
    public $selectedItemId;

    public function submit()
    {
        $this->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'contact_num' => 'required|string',
            'manager_name' => 'required|string'
        ]);

        Branch::create([
            'name' => $this->name,
            'address' => $this->address,
            'contact_num' => $this->contact_num,
            'manager_name' => $this->manager_name
        ]);

        session()->flash('message', 'Branch Profile Added Successfully.');
        $this->reset(['name', 'address', 'contact_num', 'manager_name']);
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
            'edit_contact_num' => 'required|string',
            'edit_manager_name' => 'required|string'
        ]);

        $branch = Branch::findOrFail($this->selectedItemId);
        $branch->update([
            'name' => $this->edit_name,
            'address' => $this->edit_address,
            'contact_num' => $this->edit_contact_num,
            'manager_name' => $this->edit_manager_name
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
            'edit_name',
            'edit_address',
            'edit_contact_num',
            'edit_manager_name',
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
                'label' => 'Growth Rate',
                'value' => $changePercent >= 0 ? "+{$changePercent}%" : "{$changePercent}%",
                'icon' => '<svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>',
                'gradient' => 'from-indigo-500 to-indigo-600'
            ]
        ];
    }

    public function render()
    {
        $items = Branch::where('name', 'like', '%'.$this->search.'%')
            ->orWhere('address', 'like', '%'.$this->search.'%')
            ->orWhere('contact_num', 'like', '%'.$this->search.'%')
            ->orWhere('manager_name', 'like', '%'.$this->search.'%')
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.pages.branch.index', [
            'items' => $items,
            'dashboardStats' => $this->getDashboardStatsProperty()
        ]);
    }
}

