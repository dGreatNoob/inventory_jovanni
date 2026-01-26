<?php

namespace Database\Seeders;

use App\Models\Agent;
use Illuminate\Database\Seeder;

class AgentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agents = [
            [
                'agent_code' => 'AGT001',
                'name' => 'John Smith',
                'address' => '123 Main Street, Quezon City, Metro Manila',
                'contact_num' => '+63-912-345-6789',
                'tin_num' => '123-456-789-000',
                'status' => 'active',
            ],
            [
                'agent_code' => 'AGT002',
                'name' => 'Maria Garcia',
                'address' => '456 Rizal Avenue, Makati City, Metro Manila',
                'contact_num' => '+63-923-456-7890',
                'tin_num' => '234-567-890-000',
                'status' => 'active',
            ],
            [
                'agent_code' => 'AGT003',
                'name' => 'Robert Johnson',
                'address' => '789 EDSA, Mandaluyong City, Metro Manila',
                'contact_num' => '+63-934-567-8901',
                'tin_num' => '345-678-901-000',
                'status' => 'active',
            ],
            [
                'agent_code' => 'AGT004',
                'name' => 'Sarah Martinez',
                'address' => '321 Ortigas Avenue, Pasig City, Metro Manila',
                'contact_num' => '+63-945-678-9012',
                'tin_num' => '456-789-012-000',
                'status' => 'active',
            ],
            [
                'agent_code' => 'AGT005',
                'name' => 'Michael Brown',
                'address' => '654 Taft Avenue, Manila City',
                'contact_num' => '+63-956-789-0123',
                'tin_num' => '567-890-123-000',
                'status' => 'active',
            ],
            [
                'agent_code' => 'AGT006',
                'name' => 'Jennifer Wilson',
                'address' => '987 Ayala Avenue, Makati City, Metro Manila',
                'contact_num' => '+63-967-890-1234',
                'tin_num' => '678-901-234-000',
                'status' => 'on_leave',
            ],
            [
                'agent_code' => 'AGT007',
                'name' => 'David Lee',
                'address' => '147 BGC, Taguig City, Metro Manila',
                'contact_num' => '+63-978-901-2345',
                'tin_num' => '789-012-345-000',
                'status' => 'active',
            ],
            [
                'agent_code' => 'AGT008',
                'name' => 'Emily Davis',
                'address' => '258 Shaw Boulevard, Mandaluyong City',
                'contact_num' => '+63-989-012-3456',
                'tin_num' => '890-123-456-000',
                'status' => 'active',
            ],
        ];

        foreach ($agents as $agentData) {
            Agent::updateOrCreate(
                ['agent_code' => $agentData['agent_code']],
                $agentData
            );
        }
    }
}

