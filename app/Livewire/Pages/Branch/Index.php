<?php

namespace App\Livewire\Pages\Branch;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Branch;
use App\Models\Agent;
use App\Models\AgentBranchAssignment;
use App\Support\ProductSearchHelper;
use Illuminate\Support\Facades\DB;

class Index extends Component
{
    use WithPagination;

    public $name, $address, $contact_num, $manager_name, $email;
    public $selling_area1, $selling_area2, $selling_area3, $selling_area4;
    public $code, $category, $remarks, $batch, $batchSelect = '', $batchNew = '', $branch_code, $company_name, $company_tin, $dept_code, $pull_out_address, $vendor_code;

    public $editData = [];
    public $perPage = 10;
    public $search = '';
    public $batchFilter = '';
    public $showDeleteModal = false;
    public $showEditModal = false;
    public $deleteId = null;
    public $selectedItemId;

    // Bulk assign and Batch Management
    public $selectedBranchIds = [];
    public $bulkAssignBatch = '';
    public $bulkAssignBatchNew = '';
    public $showDeleteBatchModal = false;
    public $deleteBatchName = null;
    public $showAddBranchesModal = false;
    public $addBranchesTargetBatch = '';
    public $addBranchesSearch = '';
    public $addBranchesSelectedIds = [];

    // Agent per branch dashboard properties
    public $dashboardSearch = '';
    public $statusFilter = 'all';
    public $sortBy = 'agent_count';
    public $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'batchFilter' => ['except' => ''],
    ];

    // Edit properties
    public $edit_name, $edit_address, $edit_contact_num, $edit_manager_name, $edit_email;
    public $edit_selling_area1, $edit_selling_area2, $edit_selling_area3, $edit_selling_area4;
    public $edit_code, $edit_category, $edit_remarks, $edit_batch, $edit_batchSelect = '', $edit_batchNew = '', $edit_branch_code, $edit_company_name, $edit_company_tin, $edit_dept_code, $edit_pull_out_address, $edit_vendor_code;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedBatchFilter()
    {
        $this->resetPage();
    }

    public function getBatchOptionsProperty()
    {
        return Branch::whereNotNull('batch')
            ->where('batch', '!=', '')
            ->distinct()
            ->orderBy('batch')
            ->pluck('batch', 'batch')
            ->toArray();
    }

    public function getBatchSummaryProperty()
    {
        return Branch::whereNotNull('batch')
            ->where('batch', '!=', '')
            ->selectRaw('batch, count(*) as count')
            ->groupBy('batch')
            ->orderBy('batch')
            ->get()
            ->map(fn ($row) => ['batch' => $row->batch, 'count' => (int) $row->count])
            ->toArray();
    }

    public function bulkAssignToBatch()
    {
        if ($this->bulkAssignBatch === '' || empty($this->selectedBranchIds)) {
            return;
        }
        $batchToAssign = ($this->bulkAssignBatch === '__new__')
            ? trim($this->bulkAssignBatchNew ?? '')
            : (($this->bulkAssignBatch === '__none__') ? null : $this->bulkAssignBatch);

        Branch::whereIn('id', $this->selectedBranchIds)->update(['batch' => $batchToAssign]);

        $count = count($this->selectedBranchIds);
        $label = $batchToAssign ?? 'No batch';
        session()->flash('message', "{$count} branch(es) assigned to {$label}.");
        $this->clearBulkSelection();
    }

    public function selectAllOnPage(array $ids = [])
    {
        $this->selectedBranchIds = array_values(array_unique(array_merge($this->selectedBranchIds, $ids)));
    }

    public function clearBulkSelection()
    {
        $this->selectedBranchIds = [];
        $this->bulkAssignBatch = '';
        $this->bulkAssignBatchNew = '';
    }

    public function confirmDeleteBatch(string $batchName)
    {
        $this->deleteBatchName = $batchName;
        $this->showDeleteBatchModal = true;
    }

    public function deleteBatch()
    {
        $batchName = $this->deleteBatchName;
        $count = Branch::where('batch', $batchName)->count();
        Branch::where('batch', $batchName)->update(['batch' => null]);
        session()->flash('message', "Batch '{$batchName}' removed. {$count} branch(es) now have no batch.");
        $this->showDeleteBatchModal = false;
        $this->deleteBatchName = null;
    }

    public function removeBranchFromBatch(int $branchId)
    {
        Branch::where('id', $branchId)->update(['batch' => null]);
        session()->flash('message', 'Branch removed from batch.');
    }

    public function openAddBranchesModal(string $batchName)
    {
        $this->addBranchesTargetBatch = $batchName;
        $this->addBranchesSearch = '';
        $this->addBranchesSelectedIds = [];
        $this->showAddBranchesModal = true;
    }

    public function closeAddBranchesModal()
    {
        $this->showAddBranchesModal = false;
        $this->addBranchesTargetBatch = '';
        $this->addBranchesSearch = '';
        $this->addBranchesSelectedIds = [];
    }

    public function deselectAllBranches()
    {
        $this->addBranchesSelectedIds = [];
    }

    /**
     * Plain click: if item is selected, remove it; else deselect all.
     */
    public function handlePlainBranchClick(int $branchId)
    {
        $ids = $this->addBranchesSelectedIds ?? [];
        if (in_array($branchId, $ids)) {
            $this->addBranchesSelectedIds = array_values(array_diff($ids, [$branchId]));
        } else {
            $this->addBranchesSelectedIds = [];
        }
    }

    /**
     * Ctrl+Click: toggle single item (multi-select non-contiguous).
     */
    public function toggleBranchSelection(int $branchId)
    {
        $ids = $this->addBranchesSelectedIds ?? [];
        $key = array_search($branchId, $ids);
        if ($key !== false) {
            unset($ids[$key]);
            $this->addBranchesSelectedIds = array_values($ids);
        } else {
            $this->addBranchesSelectedIds = array_values(array_merge($ids, [$branchId]));
        }
    }

    /**
     * Shift+Click: select contiguous range. Extends selection if clicking outside current range.
     */
    public function selectBranchRange(int $fromIndex, int $toIndex)
    {
        $candidates = $this->addBranchesCandidates;
        $selectedIds = $this->addBranchesSelectedIds ?? [];

        $selectedIndices = $candidates->keys()->filter(fn ($idx) => in_array($candidates->get($idx)?->id, $selectedIds))->values();
        $allIndices = $selectedIndices->merge([$fromIndex, $toIndex])->unique()->values();
        $low = $allIndices->min();
        $high = $allIndices->max();

        $idsInRange = $candidates->slice($low, $high - $low + 1)->pluck('id')->toArray();
        $this->addBranchesSelectedIds = array_values(array_unique(array_merge($selectedIds, $idsInRange)));
    }

    public function addBranchesToBatch()
    {
        if (empty($this->addBranchesSelectedIds) || empty($this->addBranchesTargetBatch)) {
            return;
        }
        Branch::whereIn('id', $this->addBranchesSelectedIds)->update(['batch' => $this->addBranchesTargetBatch]);
        $count = count($this->addBranchesSelectedIds);
        session()->flash('message', "{$count} branch(es) added to batch '{$this->addBranchesTargetBatch}'.");
        $this->closeAddBranchesModal();
    }

    public function getAddBranchesCandidatesProperty()
    {
        // Only branches with no batch assigned (exclude branches already in any batch)
        $all = Branch::where(function ($q) {
            $q->whereNull('batch')->orWhere('batch', '');
        })->orderBy('name')->get();
        $search = trim($this->addBranchesSearch ?? '');
        if ($search !== '') {
            return $all->filter(function ($branch) use ($search) {
                return ProductSearchHelper::matchesAnyField($search, [
                    $branch->name ?? '',
                    $branch->code ?? '',
                    $branch->address ?? '',
                ]);
            })->values();
        }
        return $all;
    }

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
            'code' => 'required|string',
            'category' => 'required|string',
            'address' => 'required|string',
            'contact_num' => 'nullable|string',
            'manager_name' => 'nullable|string',
            'email' => 'nullable|email',
            'selling_area1' => 'nullable|string',
            'selling_area2' => 'nullable|string',
            'selling_area3' => 'nullable|string',
            'selling_area4' => 'nullable|string',
            'remarks' => 'nullable|string',
            'batch' => 'nullable|string',
            'branch_code' => 'nullable|string',
            'company_name' => 'nullable|string',
            'company_tin' => 'nullable|string',
            'dept_code' => 'nullable|string',
            'pull_out_address' => 'nullable|string',
            'vendor_code' => 'nullable|string',
        ]);

        $batchValue = ($this->batchSelect === '__new__') ? trim($this->batchNew ?? '') : ($this->batchSelect ?: null);

        Branch::create([
            'name' => $this->name,
            'code' => $this->code,
            'category' => $this->category,
            'address' => $this->address,
            'contact_num' => $this->contact_num,
            'manager_name' => $this->manager_name,
            'email' => $this->email,
            'selling_area1' => $this->selling_area1,
            'selling_area2' => $this->selling_area2,
            'selling_area3' => $this->selling_area3,
            'selling_area4' => $this->selling_area4,
            'remarks' => $this->remarks,
            'batch' => $batchValue ?: null,
            'branch_code' => $this->branch_code,
            'company_name' => $this->company_name,
            'company_tin' => $this->company_tin,
            'dept_code' => $this->dept_code,
            'pull_out_address' => $this->pull_out_address,
            'vendor_code' => $this->vendor_code,
        ]);

        session()->flash('message', 'Branch Profile Added Successfully.');
        $this->reset([
            'name', 'code', 'category', 'address', 'contact_num', 'manager_name', 'email',
            'selling_area1', 'selling_area2', 'selling_area3', 'selling_area4',
            'remarks', 'batch', 'batchSelect', 'batchNew', 'branch_code', 'company_name', 'company_tin',
            'dept_code', 'pull_out_address', 'vendor_code'
        ]);
    }

    public function edit($id)
    {
        $branch = Branch::findOrFail($id);

        $this->selectedItemId = $id;
        $this->edit_name = $branch->name;
        $this->edit_code = $branch->code;
        $this->edit_category = $branch->category;
        $this->edit_address = $branch->address;
        $this->edit_contact_num = $branch->contact_num;
        $this->edit_manager_name = $branch->manager_name;
        $this->edit_email = $branch->email;
        $this->edit_selling_area1 = $branch->selling_area1;
        $this->edit_selling_area2 = $branch->selling_area2;
        $this->edit_selling_area3 = $branch->selling_area3;
        $this->edit_selling_area4 = $branch->selling_area4;
        $this->edit_remarks = $branch->remarks;
        $this->edit_batch = $branch->batch;
        $batchOptions = $this->batchOptions;
        $this->edit_batchSelect = ($branch->batch && isset($batchOptions[$branch->batch])) ? $branch->batch : ($branch->batch ? '__new__' : '');
        $this->edit_batchNew = ($this->edit_batchSelect === '__new__' && $branch->batch) ? $branch->batch : '';
        $this->edit_branch_code = $branch->branch_code;
        $this->edit_company_name = $branch->company_name;
        $this->edit_company_tin = $branch->company_tin;
        $this->edit_dept_code = $branch->dept_code;
        $this->edit_pull_out_address = $branch->pull_out_address;
        $this->edit_vendor_code = $branch->vendor_code;

        $this->showEditModal = true;
    }

    public function update()
    {
        $this->validate([
            'edit_name' => 'required|string',
            'edit_code' => 'required|string',
            'edit_category' => 'required|string',
            'edit_address' => 'required|string',
            'edit_contact_num' => 'nullable|string',
            'edit_manager_name' => 'nullable|string',
            'edit_email' => 'nullable|email',
            'edit_selling_area1' => 'nullable|string',
            'edit_selling_area2' => 'nullable|string',
            'edit_selling_area3' => 'nullable|string',
            'edit_selling_area4' => 'nullable|string',
            'edit_remarks' => 'nullable|string',
            'edit_batch' => 'nullable|string',
            'edit_branch_code' => 'nullable|string',
            'edit_company_name' => 'nullable|string',
            'edit_company_tin' => 'nullable|string',
            'edit_dept_code' => 'nullable|string',
            'edit_pull_out_address' => 'nullable|string',
            'edit_vendor_code' => 'nullable|string',
        ]);

        $editBatchValue = ($this->edit_batchSelect === '__new__') ? trim($this->edit_batchNew ?? '') : ($this->edit_batchSelect ?: null);

        $branch = Branch::findOrFail($this->selectedItemId);
        $branch->update([
            'name' => $this->edit_name,
            'code' => $this->edit_code,
            'category' => $this->edit_category,
            'address' => $this->edit_address,
            'contact_num' => $this->edit_contact_num,
            'manager_name' => $this->edit_manager_name,
            'email' => $this->edit_email,
            'selling_area1' => $this->edit_selling_area1,
            'selling_area2' => $this->edit_selling_area2,
            'selling_area3' => $this->edit_selling_area3,
            'selling_area4' => $this->edit_selling_area4,
            'remarks' => $this->edit_remarks,
            'batch' => $editBatchValue ?: null,
            'branch_code' => $this->edit_branch_code,
            'company_name' => $this->edit_company_name,
            'company_tin' => $this->edit_company_tin,
            'dept_code' => $this->edit_dept_code,
            'pull_out_address' => $this->edit_pull_out_address,
            'vendor_code' => $this->edit_vendor_code,
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
            'showDeleteBatchModal',
            'showAddBranchesModal',
            'deleteId',
            'deleteBatchName',
            'selectedItemId',
            'editData',
            'addBranchesTargetBatch',
            'addBranchesSearch',
            'addBranchesSelectedIds',
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
            ->get();

        $dashboardSearch = trim($this->dashboardSearch ?? '');
        if ($dashboardSearch !== '') {
            $branches = $branches->filter(function ($branch) use ($dashboardSearch) {
                return ProductSearchHelper::matchesAnyField($dashboardSearch, [
                    $branch->name ?? '',
                    $branch->address ?? '',
                    $branch->manager_name ?? '',
                ]);
            })->values();
        }

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
        if (!auth()->user()->hasAnyPermission(['branch view'])) {
            return view('livewire.pages.errors.403');
        }

        $query = Branch::query()
            ->when($this->batchFilter, fn ($q) => $q->where('batch', $this->batchFilter))
            ->latest();

        $search = trim($this->search ?? '');
        if ($search !== '') {
            $all = $query->get();
            $filtered = $all->filter(function ($branch) use ($search) {
                return ProductSearchHelper::matchesAnyField($search, [
                    $branch->name ?? '',
                    $branch->address ?? '',
                    $branch->code ?? '',
                    $branch->contact_num ?? '',
                    $branch->email ?? '',
                    $branch->manager_name ?? '',
                ]);
            })->values();
            $items = new \Illuminate\Pagination\LengthAwarePaginator(
                $filtered->forPage($this->getPage(), $this->perPage)->values(),
                $filtered->count(),
                $this->perPage,
                $this->getPage(),
                ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'pageName' => 'page']
            );
        } else {
            $items = $query->paginate($this->perPage);
        }

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