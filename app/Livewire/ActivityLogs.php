<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Log;
use Livewire\WithPagination;
use Spatie\Activitylog\Models\Activity;
use App\Models\Department;
use App\Models\User;

class ActivityLogs extends Component
{
    use WithPagination;

    public $department = '';
    public $subjectType = '';
    public $start_date = '';
    public $end_date = '';
    public $user = '';
    public $role = '';
    public $action = '';
    public $ref_no = '';
    public $description = '';
    public $ip = '';
    public $showFilters = false;
    public $perPage = 10;

    public function updating($name, $value)
    {
        if ($name === 'perPage') {
            $this->resetPage();
        }
        $this->resetPage();
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function render()
    {
        $departments = Department::orderBy('name')->get();
        $users = User::all()->keyBy('id');
        $roles = User::with('roles')->get()->flatMap(function($user) {
            return $user->getRoleNames();
        })->unique()->values();
        $logsQuery = Activity::with('causer')->orderByDesc('created_at');

        if ($this->department) {
            $logsQuery->where(function($query) {
                $query->whereJsonContains('properties->attributes->sent_from', (int)$this->department)
                    ->orWhereJsonContains('properties->attributes->sent_to', (int)$this->department);
            });
        }

        // Get all unique subject types (full class names)
        $allSubjectTypes = Activity::select('subject_type')->distinct()->pluck('subject_type');
        $subjectTypes = $allSubjectTypes->map(function($type) {
            $base = class_basename($type);
            return $base;
        })->unique()->values();

        // Map selected base name back to full class name for filtering
        $selectedFullSubjectType = $allSubjectTypes->first(function($type) {
            $base = class_basename($type);
            $selected = $this->subjectType;
            return $base === $selected;
        });
        if ($this->subjectType && $selectedFullSubjectType) {
            $logsQuery->where('subject_type', $selectedFullSubjectType);
        }

        if ($this->start_date) {
            $logsQuery->whereDate('created_at', '>=', $this->start_date);
        }
        if ($this->end_date) {
            $logsQuery->whereDate('created_at', '<=', $this->end_date);
        }
        if ($this->user) {
            $logsQuery->where('causer_id', $this->user);
        }
        if ($this->role) {
            $logsQuery->whereHas('causer.roles', function($q) { $q->where('name', $this->role); });
        }
        if ($this->action) {
            $logsQuery->where(function($q) {
                if ($this->action === 'Created') $q->where('event', 'created');
                elseif ($this->action === 'Deleted') $q->where('event', 'deleted');
                elseif ($this->action === 'Edited') $q->whereNotIn('event', ['created', 'deleted']);
            });
        }
        if ($this->ref_no) {
            $logsQuery->where(function($q) {
                $q->where('subject_id', 'like', "%{$this->ref_no}%")
                  ->orWhereJsonContains('properties->attributes->id', $this->ref_no)
                  ->orWhereJsonContains('properties->old->id', $this->ref_no);
            });
        }
        if ($this->description) {
            $logsQuery->where(function($q) {
                $q->where('description', 'like', "%{$this->description}%")
                  ->orWhereJsonContains('properties->attributes->description', $this->description)
                  ->orWhereJsonContains('properties->old->description', $this->description);
            });
        }
        if ($this->ip) {
            $logsQuery->where(function($q) {
                $q->orWhereJsonContains('properties->ip', $this->ip)
                  ->orWhereJsonContains('properties->attributes->ip', $this->ip)
                  ->orWhereJsonContains('properties->old->ip', $this->ip);
            });
        }

        $logs = $logsQuery->paginate($this->perPage);
        return view('livewire.activity-logs', [
            'logs' => $logs,
            'departments' => $departments,
            'users' => $users,
            'roles' => $roles,
            'subjectTypes' => $subjectTypes,
            'selectedDepartment' => $this->department,
            'selectedSubjectType' => $this->subjectType,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'selectedUser' => $this->user,
            'selectedRole' => $this->role,
            'selectedAction' => $this->action,
            'ref_no' => $this->ref_no,
            'descriptionFilter' => $this->description,
            'ipFilter' => $this->ip,
            'showFilters' => $this->showFilters,
        ]);
    }
}
