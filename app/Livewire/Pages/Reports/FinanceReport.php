<?php

namespace App\Livewire\Pages\Reports;

use Livewire\Component;
use App\Models\Finance;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class FinanceReport extends Component
{
    public $dateFrom = '';
    public $dateTo = '';

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function getExpenseStatsProperty()
    {
        $query = Finance::where('type', 'expense')
            ->when($this->dateFrom, fn($q) => $q->where('date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->where('date', '<=', $this->dateTo));

        return [
            'total' => $query->sum('amount'),
            'count' => $query->count(),
            'categories' => $query->select('category', DB::raw('SUM(amount) as total'))
                ->groupBy('category')
                ->orderByDesc('total')
                ->get(),
            'monthly' => $query->selectRaw('MONTH(date) as month, YEAR(date) as year, SUM(amount) as total')
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get(),
        ];
    }

    public function getReceivableStatsProperty()
    {
        $query = Finance::where('type', 'receivable')
            ->when($this->dateFrom, fn($q) => $q->where('date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->where('date', '<=', $this->dateTo));

        return [
            'total' => $query->sum('amount'),
            'balance' => $query->sum('balance'),
            'count' => $query->count(),
            'statuses' => $query->select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->get(),
            'monthly' => $query->selectRaw('MONTH(date) as month, YEAR(date) as year, SUM(amount) as total')
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get(),
        ];
    }

    public function getPayableStatsProperty()
    {
        $query = Finance::where('type', 'payable')
            ->when($this->dateFrom, fn($q) => $q->where('date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->where('date', '<=', $this->dateTo));

        return [
            'total' => $query->sum('amount'),
            'balance' => $query->sum('balance'),
            'count' => $query->count(),
            'statuses' => $query->select('status', DB::raw('COUNT(*) as count'))
                ->groupBy('status')
                ->get(),
            'monthly' => $query->selectRaw('MONTH(date) as month, YEAR(date) as year, SUM(amount) as total')
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get(),
        ];
    }

    public function getPaymentStatsProperty()
    {
        $query = Payment::query()
            ->when($this->dateFrom, fn($q) => $q->where('payment_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->where('payment_date', '<=', $this->dateTo));

        return [
            'total' => $query->sum('amount'),
            'count' => $query->count(),
            'methods' => $query->select('payment_method', DB::raw('SUM(amount) as total'))
                ->groupBy('payment_method')
                ->orderByDesc('total')
                ->get(),
            'monthly' => $query->selectRaw('MONTH(payment_date) as month, YEAR(payment_date) as year, SUM(amount) as total')
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get(),
        ];
    }

    public function getOverallStatsProperty()
    {
        $expenses = $this->expenseStats['total'];
        $receivables = $this->receivableStats['total'];
        $payables = $this->payableStats['total'];
        $payments = $this->paymentStats['total'];

        return [
            'net_position' => $receivables - $payables - $expenses + $payments,
            'total_transactions' => $this->expenseStats['count'] + $this->receivableStats['count'] + $this->payableStats['count'] + $this->paymentStats['count'],
        ];
    }

    public function render()
    {
        return view('livewire.pages.reports.finance', [
            'expenseStats' => $this->expenseStats,
            'receivableStats' => $this->receivableStats,
            'payableStats' => $this->payableStats,
            'paymentStats' => $this->paymentStats,
            'overallStats' => $this->overallStats,
        ]);
    }
}