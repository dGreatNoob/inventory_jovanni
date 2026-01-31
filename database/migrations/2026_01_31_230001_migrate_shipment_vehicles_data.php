<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Backfill shipment_vehicles from existing shipments.
     */
    public function up(): void
    {
        $shipments = DB::table('shipments')
            ->where(function ($q) {
                $q->whereNotNull('vehicle_plate_number')
                    ->orWhereNotNull('delivery_receipt_id');
            })
            ->get();

        foreach ($shipments as $shipment) {
            DB::table('shipment_vehicles')->insert([
                'shipment_id' => $shipment->id,
                'plate_number' => $shipment->vehicle_plate_number,
                'delivery_receipt_id' => $shipment->delivery_receipt_id,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('shipment_vehicles')->truncate();
    }
};
