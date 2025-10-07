<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class InventoryController extends Controller
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Update inventory quantity
     */
    public function updateInventory(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'location_id' => 'required|integer|exists:inventory_locations,id',
                'quantity' => 'required|numeric',
                'movement_type' => 'required|string|in:purchase,sale,return,transfer_in,transfer_out,adjustment,damage,theft,expired',
                'unit_cost' => 'nullable|numeric|min:0',
                'reference_type' => 'nullable|string',
                'reference_id' => 'nullable|integer',
                'notes' => 'nullable|string',
                'metadata' => 'nullable|array',
            ]);

            $movement = $this->inventoryService->updateInventory(
                $validated['product_id'],
                $validated['location_id'],
                $validated['quantity'],
                $validated['movement_type'],
                $validated
            );

            return response()->json([
                'success' => true,
                'data' => $movement,
                'message' => 'Inventory updated successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating inventory: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reserve inventory quantity
     */
    public function reserveInventory(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'location_id' => 'required|integer|exists:inventory_locations,id',
                'quantity' => 'required|numeric|min:0',
            ]);

            $success = $this->inventoryService->reserveInventory(
                $validated['product_id'],
                $validated['location_id'],
                $validated['quantity']
            );

            if (!$success) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient inventory available for reservation'
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Inventory reserved successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error reserving inventory: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Unreserve inventory quantity
     */
    public function unreserveInventory(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'location_id' => 'required|integer|exists:inventory_locations,id',
                'quantity' => 'required|numeric|min:0',
            ]);

            $this->inventoryService->unreserveInventory(
                $validated['product_id'],
                $validated['location_id'],
                $validated['quantity']
            );

            return response()->json([
                'success' => true,
                'message' => 'Inventory unreserved successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error unreserving inventory: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Transfer inventory between locations
     */
    public function transferInventory(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'from_location_id' => 'required|integer|exists:inventory_locations,id',
                'to_location_id' => 'required|integer|exists:inventory_locations,id',
                'quantity' => 'required|numeric|min:0',
                'transfer_id' => 'nullable|integer',
                'notes' => 'nullable|string',
                'metadata' => 'nullable|array',
            ]);

            if ($validated['from_location_id'] === $validated['to_location_id']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Source and destination locations cannot be the same'
                ], 400);
            }

            $transfers = $this->inventoryService->transferInventory(
                $validated['product_id'],
                $validated['from_location_id'],
                $validated['to_location_id'],
                $validated['quantity'],
                $validated
            );

            return response()->json([
                'success' => true,
                'data' => $transfers,
                'message' => 'Inventory transferred successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error transferring inventory: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get inventory by location
     */
    public function getByLocation(Request $request): JsonResponse
    {
        try {
            $locationId = $request->get('location_id');
            $inventory = $this->inventoryService->getInventoryByLocation($locationId);

            return response()->json([
                'success' => true,
                'data' => $inventory,
                'message' => 'Inventory retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving inventory: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get low stock alerts
     */
    public function lowStockAlerts(): JsonResponse
    {
        try {
            $alerts = $this->inventoryService->getLowStockAlerts();

            return response()->json([
                'success' => true,
                'data' => $alerts,
                'message' => 'Low stock alerts retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving low stock alerts: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get out of stock alerts
     */
    public function outOfStockAlerts(): JsonResponse
    {
        try {
            $alerts = $this->inventoryService->getOutOfStockAlerts();

            return response()->json([
                'success' => true,
                'data' => $alerts,
                'message' => 'Out of stock alerts retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving out of stock alerts: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get movement history
     */
    public function movementHistory(Request $request): JsonResponse
    {
        try {
            $productId = $request->get('product_id');
            $locationId = $request->get('location_id');
            $days = $request->get('days', 30);

            $history = $this->inventoryService->getMovementHistory($productId, $locationId, $days);

            return response()->json([
                'success' => true,
                'data' => $history,
                'message' => 'Movement history retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving movement history: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get inventory valuation
     */
    public function valuation(Request $request): JsonResponse
    {
        try {
            $locationId = $request->get('location_id');
            $valuation = $this->inventoryService->getInventoryValuation($locationId);

            return response()->json([
                'success' => true,
                'data' => $valuation,
                'message' => 'Inventory valuation retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving inventory valuation: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get monthly inventory summary
     */
    public function monthlySummary(Request $request): JsonResponse
    {
        try {
            $year = $request->get('year');
            $month = $request->get('month');
            
            $summary = $this->inventoryService->getMonthlyInventorySummary($year, $month);

            return response()->json([
                'success' => true,
                'data' => $summary,
                'message' => 'Monthly inventory summary retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving monthly summary: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set reorder point
     */
    public function setReorderPoint(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|integer|exists:products,id',
                'location_id' => 'required|integer|exists:inventory_locations,id',
                'reorder_point' => 'required|numeric|min:0',
            ]);

            $inventory = $this->inventoryService->setReorderPoint(
                $validated['product_id'],
                $validated['location_id'],
                $validated['reorder_point']
            );

            return response()->json([
                'success' => true,
                'data' => $inventory,
                'message' => 'Reorder point set successfully'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error setting reorder point: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get inventory statistics
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->inventoryService->getInventoryStats();

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Inventory statistics retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving inventory statistics: ' . $e->getMessage()
            ], 500);
        }
    }
}