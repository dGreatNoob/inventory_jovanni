<?php

namespace App\Livewire\Pages\Reports;

use Livewire\Component;
use App\Models\Finance;

class ProfitLoss extends Component
{
    public $dateFrom;
    public $dateTo;
    public $selectedPeriod = 'current_month';

    public function mount()
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->endOfMonth()->format('Y-m-d');
    }

    public function updatedSelectedPeriod()
    {
        switch ($this->selectedPeriod) {
            case 'current_month':
                $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
                $this->dateTo = now()->endOfMonth()->format('Y-m-d');
                break;
            case 'last_month':
                $this->dateFrom = now()->subMonth()->startOfMonth()->format('Y-m-d');
                $this->dateTo = now()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
            case 'current_quarter':
                $this->dateFrom = now()->startOfQuarter()->format('Y-m-d');
                $this->dateTo = now()->endOfQuarter()->format('Y-m-d');
                break;
            case 'current_year':
                $this->dateFrom = now()->startOfYear()->format('Y-m-d');
                $this->dateTo = now()->endOfYear()->format('Y-m-d');
                break;
        }
    }

    public function getFinancialDataProperty()
    {
        // Get receivables data
        $receivables = Finance::where('type', 'receivable')
            ->whereBetween('date', [$this->dateFrom, $this->dateTo])
            ->get();
        
        $totalReceivables = $receivables->sum('amount');
        $paidReceivables = $receivables->where('status', 'paid')->sum('amount');
        $pendingReceivables = $receivables->where('status', 'pending')->sum('amount');

        // Get payables data
        $payables = Finance::where('type', 'payable')
            ->whereBetween('date', [$this->dateFrom, $this->dateTo])
            ->get();
        
        $totalPayables = $payables->sum('amount');
        $paidPayables = $payables->where('status', 'paid')->sum('amount');
        $pendingPayables = $payables->where('status', 'pending')->sum('amount');

        // Get expenses data
        $expenses = Finance::where('type', 'expense')
            ->whereBetween('date', [$this->dateFrom, $this->dateTo])
            ->get();
        
        $totalExpenses = $expenses->sum('amount');
        $paidExpenses = $expenses->where('status', 'paid')->sum('amount');
        $pendingExpenses = $expenses->where('status', 'pending')->sum('amount');

        // Calculate profit (Receivables - Payables - Expenses)
        $totalProfit = $paidReceivables - $paidPayables - $paidExpenses;

        return [
            'receivables' => [
                'total' => $totalReceivables,
                'paid' => $paidReceivables,
                'pending' => $pendingReceivables,
                'count' => $receivables->count()
            ],
            'payables' => [
                'total' => $totalPayables,
                'paid' => $paidPayables,
                'pending' => $pendingPayables,
                'count' => $payables->count()
            ],
            'expenses' => [
                'total' => $totalExpenses,
                'paid' => $paidExpenses,
                'pending' => $pendingExpenses,
                'count' => $expenses->count()
            ],
            'profit' => $totalProfit
        ];
    }

    public function getMonthlyTrendProperty()
    {
        // Generate dummy monthly trend data for the last 6 months
        $months = [];
        $receivablesData = [];
        $payablesData = [];
        $expensesData = [];
        $profitData = [];

        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $months[] = $month->format('M Y');
            
            // Generate realistic dummy data
            $receivablesData[] = rand(50000, 150000);
            $payablesData[] = rand(30000, 100000);
            $expensesData[] = rand(20000, 80000);
            $profitData[] = $receivablesData[count($receivablesData) - 1] - $payablesData[count($payablesData) - 1] - $expensesData[count($expensesData) - 1];
        }

        return [
            'months' => $months,
            'receivables' => $receivablesData,
            'payables' => $payablesData,
            'expenses' => $expensesData,
            'profit' => $profitData
        ];
    }

    public function getTopExpenseCategoriesProperty()
    {
        // Generate dummy expense categories data
        return [
            ['category' => 'Office Supplies', 'amount' => 25000, 'percentage' => 25],
            ['category' => 'Utilities', 'amount' => 20000, 'percentage' => 20],
            ['category' => 'Travel', 'amount' => 18000, 'percentage' => 18],
            ['category' => 'Marketing', 'amount' => 15000, 'percentage' => 15],
            ['category' => 'Maintenance', 'amount' => 12000, 'percentage' => 12],
            ['category' => 'Professional Fees', 'amount' => 10000, 'percentage' => 10]
        ];
    }

    public function render()
    {
        return view('livewire.pages.reports.profit-loss', [
            'financialData' => $this->getFinancialDataProperty(),
            'monthlyTrend' => $this->getMonthlyTrendProperty(),
            'topExpenseCategories' => $this->getTopExpenseCategoriesProperty()
        ]);
    }
}
