<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryMovement;
use App\Services\InventoryMovementService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class InventoryMovementController extends Controller
{
    protected InventoryMovementService $movementService;

    public function __construct(InventoryMovementService $movementService)
    {
        $this->movementService = $movementService;
    }

    /**
     * Display a listing of inventory movements
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['product_id', 'location_id', 'movement_type', 'reference_type', 'created_by']);
            $perPage = $request->get('per_page', 20);
            $days = $request->get('days', 30);

            $movements = $this->movementService->getMovements($filters, $perPage, $days);

            return response()->json([
                'success' => true,
                'data' => $movements,
                'message' => 'Inventory movements retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving inventory movements: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created inventory movement
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'location_id' => 'required|exists:inventory_locations,id',
                'movement_type' => 'required|string|in:purchase,sale,adjustment,transfer_in,transfer_out,return,damage,expired',
                'quantity' => 'required|numeric',
                'unit_cost' => 'nullable|numeric|min:0',
                'total_cost' => 'nullable|numeric|min:0',
                'reference_type' => 'nullable|string|max:100',
                'reference_id' => 'nullable|integer',
                'notes' => 'nullable|string',
                'metadata' => 'nullable|array',
            ]);

            $movement = $this->movementService->createMovement($validated);

            return response()->json([
                'success' => true,
                'data' => $movement,
                'message' => 'Inventory movement created successfully'
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating inventory movement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified inventory movement
     */
    public function show(int $id): JsonResponse
    {
        try {
            $movement = $this->movementService->getMovementDetails($id);

            if (!$movement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Inventory movement not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $movement,
                'message' => 'Inventory movement retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving inventory movement: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get movement statistics
     */
    public function stats(Request $request): JsonResponse
    {
        try {
            $days = $request->get('days', 30);
            $stats = $this->movementService->getMovementStats($days);

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Movement statistics retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving movement statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get movements by product
     */
    public function byProduct(int $productId, Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 20);
            $days = $request->get('days', 30);

            $movements = $this->movementService->getMovementsByProduct($productId, $perPage, $days);

            return response()->json([
                'success' => true,
                'data' => $movements,
                'message' => 'Product movements retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving product movements: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get movements by location
     */
    public function byLocation(int $locationId, Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 20);
            $days = $request->get('days', 30);

            $movements = $this->movementService->getMovementsByLocation($locationId, $perPage, $days);

            return response()->json([
                'success' => true,
                'data' => $movements,
                'message' => 'Location movements retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving location movements: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get movement types
     */
    public function types(): JsonResponse
    {
        try {
            $types = $this->movementService->getMovementTypes();

            return response()->json([
                'success' => true,
                'data' => $types,
                'message' => 'Movement types retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving movement types: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get monthly movement summary
     */
    public function monthlySummary(Request $request): JsonResponse
    {
        try {
            $year = $request->get('year', now()->year);
            $month = $request->get('month', now()->month);

            $summary = $this->movementService->getMonthlySummary($year, $month);

            return response()->json([
                'success' => true,
                'data' => $summary,
                'message' => 'Monthly summary retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving monthly summary: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get movement trends
     */
    public function trends(Request $request): JsonResponse
    {
        try {
            $days = $request->get('days', 30);
            $trends = $this->movementService->getMovementTrends($days);

            return response()->json([
                'success' => true,
                'data' => $trends,
                'message' => 'Movement trends retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving movement trends: ' . $e->getMessage()
            ], 500);
        }
    }
}
