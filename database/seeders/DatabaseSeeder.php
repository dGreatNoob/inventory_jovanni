<?php

namespace Database\Seeders;

use App\Enums\Enum\PermissionEnum;
use App\Enums\RolesEnum;
use App\Models\PurchaseOrder;
use App\Models\RawMatInv;
use App\Models\RawMatProfile;
use App\Models\SupplyOrder;
use App\Models\SupplyProfile;
use App\Models\User;
use App\Models\RequestSlip;
use App\Models\ItemType;
use App\Models\Allocation;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Termwind\Components\Raw;
use App\Models\RawMatOrder;
use App\Models\Department;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {


        $this->call([
            DepartmentSeeder::class,
            RoleAndPermissionSeeder::class,
            PetGoodsSeeder::class,
            FinanceSeeder::class,
        ]);

        RequestSlip::factory(20)->create();

        // Create Item Types and Allocations first
        ItemType::factory(10)->create();
        Allocation::factory(10)->create();
        

        
        // Create Supply Profiles with relationships
        SupplyProfile::factory(10)
            ->state(function (array $attributes) {
                return [
                    'item_type_id' => ItemType::inRandomOrder()->first()->id,
                    'allocation_id' => Allocation::inRandomOrder()->first()->id,
                ];
            })
            ->create();
            
        RawMatProfile::factory(10)->create();

        PurchaseOrder::factory(2)
            ->supply()
            ->has(SupplyOrder::factory()->count(5))
            ->create();

        PurchaseOrder::factory(2)
            ->rawMat()
            ->has(
                RawMatOrder::factory()
                    ->count(5)
                    ->has(RawMatInv::factory()->count(5))
            )
            ->create();


    }
}
