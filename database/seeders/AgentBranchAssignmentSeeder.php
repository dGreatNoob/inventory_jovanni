<?php

namespace Database\Seeders;

use App\Models\Agent;
use App\Models\Branch;
use App\Models\AgentBranchAssignment;
use Illuminate\Database\Seeder;

class AgentBranchAssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agents = Agent::all();
        $branches = Branch::all();

        if ($agents->isEmpty() || $branches->isEmpty()) {
            $this->command?->warn('Agents or Branches not found. Please seed Agents and Branches first.');
            return;
        }

        // Assign agents to branches
        $assignments = [
            // Agent 1 assigned to multiple branches
            ['agent_code' => 'AGT001', 'branch_codes' => ['BR001', 'BR002'], 'selling_area' => 'Premium'],
            ['agent_code' => 'AGT002', 'branch_codes' => ['BR003', 'BR004'], 'selling_area' => 'Standard'],
            ['agent_code' => 'AGT003', 'branch_codes' => ['BR005'], 'selling_area' => 'Premium'],
            ['agent_code' => 'AGT004', 'branch_codes' => ['BR006', 'BR007'], 'selling_area' => 'Standard'],
            ['agent_code' => 'AGT005', 'branch_codes' => ['BR001', 'BR003'], 'selling_area' => 'High Traffic'],
            ['agent_code' => 'AGT006', 'branch_codes' => ['BR002'], 'selling_area' => 'Standard'],
            ['agent_code' => 'AGT007', 'branch_codes' => ['BR004', 'BR005'], 'selling_area' => 'Premium'],
            ['agent_code' => 'AGT008', 'branch_codes' => ['BR006'], 'selling_area' => 'Standard'],
        ];

        foreach ($assignments as $assignment) {
            $agent = $agents->where('agent_code', $assignment['agent_code'])->first();
            if (!$agent) {
                continue;
            }

            foreach ($assignment['branch_codes'] as $branchCode) {
                $branch = $branches->where('code', $branchCode)->first();
                if (!$branch) {
                    continue;
                }

                // Check if assignment already exists
                $existing = AgentBranchAssignment::where('agent_id', $agent->id)
                    ->where('branch_id', $branch->id)
                    ->whereNull('released_at')
                    ->first();

                if (!$existing) {
                    AgentBranchAssignment::create([
                        'agent_id' => $agent->id,
                        'branch_id' => $branch->id,
                        'selling_area' => $assignment['selling_area'],
                        'assigned_at' => now()->subDays(rand(1, 90)),
                        'released_at' => null,
                    ]);
                }
            }
        }

        // Create some historical assignments (released)
        $historicalAssignments = [
            ['agent_code' => 'AGT001', 'branch_code' => 'BR003', 'assigned_days_ago' => 120, 'released_days_ago' => 60],
            ['agent_code' => 'AGT002', 'branch_code' => 'BR001', 'assigned_days_ago' => 100, 'released_days_ago' => 40],
        ];

        foreach ($historicalAssignments as $hist) {
            $agent = $agents->where('agent_code', $hist['agent_code'])->first();
            $branch = $branches->where('code', $hist['branch_code'])->first();

            if ($agent && $branch) {
                AgentBranchAssignment::create([
                    'agent_id' => $agent->id,
                    'branch_id' => $branch->id,
                    'selling_area' => 'Standard',
                    'assigned_at' => now()->subDays($hist['assigned_days_ago']),
                    'released_at' => now()->subDays($hist['released_days_ago']),
                ]);
            }
        }
    }
}

