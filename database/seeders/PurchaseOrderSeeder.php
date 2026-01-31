<?php

namespace Database\Seeders;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\Department;
use App\Models\User;
use App\Models\Product;
use App\Enums\PurchaseOrderStatus;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class PurchaseOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get required dependencies
        $suppliers = Supplier::take(5)->get();
        $departments = Department::take(3)->get();
        $users = User::take(5)->get();
        $products = Product::take(10)->get();

        if ($suppliers->isEmpty() || $departments->isEmpty() || $users->isEmpty()) {
            $this->command?->warn('Suppliers, Departments, or Users not found. Please seed them first.');
            return;
        }

        if ($products->isEmpty()) {
            $this->command?->warn('Products not found. Purchase orders will be created without items. Please seed Products first for complete data.');
        }

        // Helper function to safely get random item
        $getRandom = function($collection) {
            return $collection->isNotEmpty() ? $collection->random() : null;
        };

        $purchaseOrders = [
            [
                'po_num' => 'PO-' . now()->format('Ymd') . '-001',
                'status' => PurchaseOrderStatus::PENDING,
                'supplier_id' => $getRandom($suppliers)?->id ?? $suppliers->first()?->id,
                'order_date' => now()->subDays(5),
                'ordered_by' => $getRandom($users)?->id ?? $users->first()?->id,
                'del_to' => $getRandom($departments)?->id ?? $departments->first()?->id,
                'total_qty' => 150,
                'total_price' => 75000.00,
                'approver' => $users->where('id', '!=', $users->first()?->id)->first()?->id ?? $users->first()?->id,
                'items' => $products->isNotEmpty() ? [
                    ['product_id' => $products->first()->id, 'quantity' => 50, 'unit_price' => 250.00],
                    ['product_id' => $products->count() > 1 ? $products->skip(1)->first()->id : $products->first()->id, 'quantity' => 100, 'unit_price' => 500.00],
                ] : [],
            ],
            [
                'po_num' => 'PO-' . now()->format('Ymd') . '-002',
                'status' => PurchaseOrderStatus::APPROVED,
                'supplier_id' => $getRandom($suppliers)?->id ?? $suppliers->first()?->id,
                'order_date' => now()->subDays(3),
                'ordered_by' => $getRandom($users)?->id ?? $users->first()?->id,
                'del_to' => $getRandom($departments)?->id ?? $departments->first()?->id,
                'total_qty' => 200,
                'total_price' => 120000.00,
                'approver' => $users->where('id', '!=', $users->first()?->id)->first()?->id ?? $users->first()?->id,
                'items' => $products->isNotEmpty() ? [
                    ['product_id' => $products->first()->id, 'quantity' => 75, 'unit_price' => 600.00],
                    ['product_id' => $products->count() > 1 ? $products->skip(1)->first()->id : $products->first()->id, 'quantity' => 125, 'unit_price' => 480.00],
                ] : [],
            ],
            [
                'po_num' => 'PO-' . now()->format('Ymd') . '-003',
                'status' => PurchaseOrderStatus::TO_RECEIVE,
                'po_type' => 'supply',
                'supplier_id' => $getRandom($suppliers)?->id ?? $suppliers->first()?->id,
                'order_date' => now()->subDays(10),
                'expected_delivery_date' => now()->addDays(2),
                'ordered_by' => $getRandom($users)?->id ?? $users->first()?->id,
                'del_to' => $getRandom($departments)?->id ?? $departments->first()?->id,
                'quotation' => 'QUO-2025-003',
                'total_qty' => 100,
                'total_price' => 45000.00,
                'approver' => $users->where('id', '!=', $users->first()?->id)->first()?->id ?? $users->first()?->id,
                'items' => $products->isNotEmpty() ? [
                    ['product_id' => $products->first()->id, 'quantity' => 60, 'unit_price' => 300.00],
                    ['product_id' => $products->count() > 1 ? $products->skip(1)->first()->id : $products->first()->id, 'quantity' => 40, 'unit_price' => 375.00],
                ] : [],
            ],
            [
                'po_num' => 'PO-' . now()->format('Ymd') . '-004',
                'status' => PurchaseOrderStatus::RECEIVED,
                'po_type' => 'supply',
                'supplier_id' => $getRandom($suppliers)?->id ?? $suppliers->first()?->id,
                'order_date' => now()->subDays(20),
                'expected_delivery_date' => now()->subDays(5),
                'ordered_by' => $getRandom($users)?->id ?? $users->first()?->id,
                'del_to' => $getRandom($departments)?->id ?? $departments->first()?->id,
                'quotation' => 'QUO-2025-004',
                'total_qty' => 300,
                'total_price' => 180000.00,
                'approver' => $users->where('id', '!=', $users->first()?->id)->first()?->id ?? $users->first()?->id,
                'items' => $products->isNotEmpty() ? [
                    ['product_id' => $products->first()->id, 'quantity' => 150, 'unit_price' => 400.00],
                    ['product_id' => $products->count() > 1 ? $products->skip(1)->first()->id : $products->first()->id, 'quantity' => 150, 'unit_price' => 600.00],
                ] : [],
            ],
            [
                'po_num' => 'PO-' . now()->format('Ymd') . '-005',
                'status' => PurchaseOrderStatus::CANCELLED,
                'po_type' => 'supply',
                'supplier_id' => $getRandom($suppliers)?->id ?? $suppliers->first()?->id,
                'order_date' => now()->subDays(15),
                'expected_delivery_date' => now()->addDays(5),
                'ordered_by' => $getRandom($users)?->id ?? $users->first()?->id,
                'del_to' => $getRandom($departments)?->id ?? $departments->first()?->id,
                'quotation' => 'QUO-2025-005',
                'total_qty' => 80,
                'total_price' => 40000.00,
                'approver' => $users->where('id', '!=', $users->first()?->id)->first()?->id ?? $users->first()?->id,
                'items' => $products->isNotEmpty() ? [
                    ['product_id' => $products->first()->id, 'quantity' => 80, 'unit_price' => 500.00],
                ] : [],
            ],
        ];

        // Fields that exist in the purchase_orders table (from migration)
        // Only include fields that actually exist in the database
        $allowedFields = [
            'po_num', 'status', 'supplier_id', 'order_date', 'del_to',
            'quotation', 'po_type', 'expected_delivery_date', 'total_qty', 'total_price',
            'ordered_by', 'approver', 'del_on', 'dr_number', 'received_date', 'total_est_weight',
        ];

        foreach ($purchaseOrders as $poData) {
            $items = $poData['items'] ?? [];
            unset($poData['items']);

            // Convert enum to string for database
            $status = $poData['status']->value;
            unset($poData['status']);

            // Filter to only include allowed fields
            $filteredData = array_intersect_key($poData, array_flip($allowedFields));
            $filteredData['status'] = $status;

            $purchaseOrder = PurchaseOrder::updateOrCreate(
                ['po_num' => $poData['po_num']],
                $filteredData
            );

            // Create purchase order items only if products exist
            if (!$products->isEmpty()) {
                foreach ($items as $itemData) {
                    // Ensure product_id exists in the products collection
                    $product = $products->firstWhere('id', $itemData['product_id']);
                    if ($product) {
                        PurchaseOrderItem::updateOrCreate(
                            [
                                'purchase_order_id' => $purchaseOrder->id,
                                'product_id' => $itemData['product_id'],
                            ],
                            [
                                'quantity' => $itemData['quantity'],
                                'unit_price' => $itemData['unit_price'],
                            ]
                        );
                    }
                }
            }
        }
    }
}

