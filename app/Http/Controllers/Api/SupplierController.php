<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class SupplierController extends Controller
{
    protected SupplierService $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
    }

    /**
     * Display a listing of suppliers
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = $request->get('q', '');
            $filters = $request->only(['is_active', 'city', 'country']);
            $perPage = $request->get('per_page', 20);

            $suppliers = $this->supplierService->searchSuppliers($query, $filters, $perPage);

            return response()->json([
                'success' => true,
                'data' => $suppliers,
                'message' => 'Suppliers retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving suppliers: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created supplier
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'entity_id' => 'nullable|integer',
                'name' => 'required|string|max:255',
                'contact_person' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:50',
                'address' => 'nullable|string',
                'city' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'terms' => 'nullable|string',
                'tax_id' => 'nullable|string|max:100',
                'credit_limit' => 'nullable|numeric|min:0',
                'payment_terms_days' => 'nullable|integer|min:0',
                'is_active' => 'boolean',
            ]);

            $supplier = $this->supplierService->createSupplier($validated);

            return response()->json([
                'success' => true,
                'data' => $supplier,
                'message' => 'Supplier created successfully'
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
                'message' => 'Error creating supplier: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified supplier
     */
    public function show(int $id): JsonResponse
    {
        try {
            $supplier = $this->supplierService->getSupplierDetails($id);

            if (!$supplier) {
                return response()->json([
                    'success' => false,
                    'message' => 'Supplier not found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $supplier,
                'message' => 'Supplier retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving supplier: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified supplier
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $supplier = Supplier::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'contact_person' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:50',
                'address' => 'nullable|string',
                'city' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'terms' => 'nullable|string',
                'tax_id' => 'nullable|string|max:100',
                'credit_limit' => 'nullable|numeric|min:0',
                'payment_terms_days' => 'nullable|integer|min:0',
                'is_active' => 'boolean',
            ]);

            $updatedSupplier = $this->supplierService->updateSupplier($supplier, $validated);

            return response()->json([
                'success' => true,
                'data' => $updatedSupplier,
                'message' => 'Supplier updated successfully'
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
                'message' => 'Error updating supplier: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified supplier
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $this->supplierService->deleteSupplier($supplier);

            return response()->json([
                'success' => true,
                'message' => 'Supplier deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting supplier: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get suppliers for dropdown/select
     */
    public function select(): JsonResponse
    {
        try {
            $suppliers = $this->supplierService->getSuppliersForSelect();

            return response()->json([
                'success' => true,
                'data' => $suppliers,
                'message' => 'Suppliers retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving suppliers: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get supplier statistics
     */
    public function stats(): JsonResponse
    {
        try {
            $stats = $this->supplierService->getSupplierStats();

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Supplier statistics retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving supplier statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get supplier products
     */
    public function products(int $id, Request $request): JsonResponse
    {
        try {
            $supplier = Supplier::findOrFail($id);
            $perPage = $request->get('per_page', 20);

            $products = $this->supplierService->getSupplierProducts($supplier, $perPage);

            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Supplier products retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving supplier products: ' . $e->getMessage()
            ], 500);
        }
    }
}
