<?php

namespace App\Livewire\Pages\Reports;

use Livewire\Component;
use App\Models\Branch;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;

#[
    Layout('components.layouts.app'),
    Title('Branch Inventory Audit Report')
]
class BranchInventoryReport extends Component
{
    use WithPagination;

    public string $dateFrom = '';
    public string $dateTo = '';
    public ?int $selectedBranch = null;

    public bool $showAuditModal = false;
    public ?int $selectedAuditId = null;
    /** @var array<string,mixed> */
    public array $selectedAudit = [];

    public function mount()
    {
        $this->dateTo = now()->format('Y-m-d');
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
    }

    public function updating($name, $value)
    {
        // Reset pagination on filter changes
        if (in_array($name, ['dateFrom', 'dateTo', 'selectedBranch'], true)) {
            $this->resetPage();
        }
    }

    protected function baseQuery()
    {
        $query = Activity::query()
            ->with('subject')
            ->where('log_name', 'inventory_audit')
            ->where('subject_type', Branch::class)
            ->orderByDesc('created_at');

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }
        if ($this->selectedBranch) {
            $query->where('subject_id', $this->selectedBranch);
        }

        return $query;
    }

    public function openAudit(int $activityId): void
    {
        $activity = Activity::where('log_name', 'inventory_audit')
            ->where('subject_type', Branch::class)
            ->with('subject')
            ->findOrFail($activityId);

        $properties = $activity->properties;
        // Spatie casts properties to a Collection; normalize to array
        $propertiesArray = method_exists($properties, 'toArray') ? $properties->toArray() : (array) $properties;

        $this->selectedAuditId = $activity->id;
        $this->selectedAudit = [
            'id' => $activity->id,
            'created_at' => $activity->created_at,
            'branch_id' => $activity->subject_id,
            'branch_name' => $activity->subject?->name ?? 'Unknown Branch',
            'branch_batch' => $activity->subject?->batch ?? null,
            'description' => $activity->description,
            'properties' => $propertiesArray,
        ];

        $this->showAuditModal = true;
    }

    public function closeAuditModal(): void
    {
        $this->showAuditModal = false;
        $this->selectedAuditId = null;
        $this->selectedAudit = [];
    }

    public function render()
    {
        $base = $this->baseQuery();

        $totalAudits = (clone $base)->count();
        $branchesAudited = (clone $base)->distinct('subject_id')->count('subject_id');

        $passAudits = (clone $base)
            ->where('properties->missing_items_count', 0)
            ->where('properties->extra_items_count', 0)
            ->where('properties->quantity_variances_count', 0)
            ->count();

        $failAudits = max(0, $totalAudits - $passAudits);

        $audits = (clone $base)->paginate(15);

        $branches = Branch::orderBy('name')->get();

        return view('livewire.pages.reports.branch-inventory-report', compact(
            'audits',
            'branches',
            'totalAudits',
            'branchesAudited',
            'passAudits',
            'failAudits',
        ));
    }
}