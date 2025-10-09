<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\InventoryLocation;
use App\Services\InventoryLocationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class InventoryLocationController extends Controller
{
    protected InventoryLocationService $locationService;

    public function __construct(InventoryLocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * Display a listing of inventory locations
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q', '');
            $filters = $request->only(['type', 'is_active']);
            $perPage = $request->get('per_page', 20);

            $locations = $this->locationService->searchLocations($query, $filters, $perPage);

            return response()->json([
                'success' => true,
                'data' => $locations,
                'message' => 'Inventory locations retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving inventory locations: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created inventory location
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'entity_id' => 'nullable|integer',
                'name' => 'required|string|max:255',
                'type' => 'required|string|max:100',
                'address' => 'nullable|string',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            $location = $this->locationService->createLocation($validated);

            return response()->json([
                'success' => true,
                'data' => $location,
                'message' => 'Inventory location created successfully'
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
                'message' => 'Error creating inventory location: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified inventory location
     */
    public function show(int $id): JsonResponse
    {
        try {
            $location = $this->locationService->getLocationDetails($id);

            if (!$location) {
                return response()->json([
                    'success' => false,
                    'message' => 'Inventory location not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $location,
                'message' => 'Inventory location retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving inventory location: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified inventory location
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $location = InventoryLocation::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'type' => 'sometimes|required|string|max:100',
                'address' => 'nullable|string',
                'description' => 'nullable|string',
                'is_active' => 'boolean',
            ]);

            $updatedLocation = $this->locationService->updateLocation($location, $validated);

            return response()->json([
                'success' => true,
                'data' => $updatedLocation,
                'message' => 'Inventory location updated successfully'
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
                'message' => 'Error updating inventory location: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified inventory location
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $location = InventoryLocation::findOrFail($id);
            $this->locationService->deleteLocation($location);

            return response()->json([
                'success' => true,
                'message' => 'Inventory location deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting inventory location: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get locations for dropdown/select
     */
    public function select(): JsonResponse
    {
        try {
            $locations = $this->locationService->getLocationsForSelect();

            return response()->json([
                'success' => true,
                'data' => $locations,
                'message' => 'Inventory locations retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving inventory locations: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get location statistics
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->locationService->getLocationStats();

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Location statistics retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving location statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get location inventory
     */
    public function inventory(int $id, Request $request): JsonResponse
    {
        try {
            $location = InventoryLocation::findOrFail($id);
            $perPage = $request->get('per_page', 20);

            $inventory = $this->locationService->getLocationInventory($location, $perPage);

            return response()->json([
                'success' => true,
                'data' => $inventory,
                'message' => 'Location inventory retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving location inventory: ' . $e->getMessage()
            ], 500);
        }
    }
}
