<?php

namespace Database\Seeders;

use App\Models\Finance;
use Illuminate\Database\Seeder;

class FinanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create receivables
        Finance::factory(15)->receivable()->create();
        
        // Create payables
        Finance::factory(12)->payable()->create();
        
        // Create expenses
        Finance::factory(20)->expense()->create();
    }
}

