<?php

namespace App\Services;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SupplierService
{
    /**
     * Search suppliers with filtering
     */
    public function searchSuppliers(string $query = '', array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        $suppliers = Supplier::with(['products' => function($query) {
            $query->with(['category', 'inventory.location'])->limit(5);
        }])
        ->when($query, function ($q) use ($query) {
            $q->where('name', 'like', "%{$query}%")
              ->orWhere('contact_person', 'like', "%{$query}%")
              ->orWhere('email', 'like', "%{$query}%")
              ->orWhere('phone', 'like', "%{$query}%")
              ->orWhere('city', 'like', "%{$query}%")
              ->orWhere('country', 'like', "%{$query}%");
        })
        ->when(isset($filters['is_active']), fn($q) => $q->where('is_active', $filters['is_active']))
        ->when($filters['city'] ?? null, fn($q) => $q->where('city', 'like', "%{$filters['city']}%"))
        ->when($filters['country'] ?? null, fn($q) => $q->where('country', 'like', "%{$filters['country']}%"))
        ->orderBy('name')
        ->paginate($perPage);

        return $suppliers;
    }

    /**
     * Create a new supplier
     */
    public function createSupplier(array $data): Supplier
    {
        return DB::transaction(function () use ($data) {
            // Set default values
            $data['entity_id'] = $data['entity_id'] ?? 1;
            $data['is_active'] = $data['is_active'] ?? true;
            $data['credit_limit'] = $data['credit_limit'] ?? 0;
            $data['payment_terms_days'] = $data['payment_terms_days'] ?? 30;

            $supplier = Supplier::create($data);

            return $supplier->load(['products']);
        });
    }

    /**
     * Update supplier information
     */
    public function updateSupplier(Supplier $supplier, array $data): Supplier
    {
        return DB::transaction(function () use ($supplier, $data) {
            $supplier->update($data);

            return $supplier->fresh(['products']);
        });
    }

    /**
     * Delete supplier (soft delete)
     */
    public function deleteSupplier(Supplier $supplier): bool
    {
        return DB::transaction(function () use ($supplier) {
            // Check if supplier has products
            if ($supplier->products()->count() > 0) {
                throw new \Exception('Cannot delete supplier with existing products');
            }

            // Soft delete the supplier
            $supplier->delete();

            return true;
        });
    }

    /**
     * Get supplier with full details
     */
    public function getSupplierDetails(int $supplierId): ?Supplier
    {
        return Supplier::with([
            'products' => function($query) {
                $query->with(['category', 'inventory.location', 'images']);
            }
        ])->find($supplierId);
    }

    /**
     * Get suppliers for dropdown/select
     */
    public function getSuppliersForSelect(): Collection
    {
        return Supplier::active()
            ->orderBy('name')
            ->get(['id', 'name', 'contact_person', 'phone', 'email']);
    }

    /**
     * Get supplier products
     */
    public function getSupplierProducts(Supplier $supplier, int $perPage = 20): LengthAwarePaginator
    {
        return $supplier->products()
            ->with(['category', 'inventory.location', 'images'])
            ->orderBy('name')
            ->paginate($perPage);
    }

    /**
     * Get supplier statistics
     */
    public function getSupplierStats(): array
    {
        $totalSuppliers = Supplier::count();
        $activeSuppliers = Supplier::active()->count();
        $inactiveSuppliers = $totalSuppliers - $activeSuppliers;

        // Get suppliers with most products
        $topSuppliers = Supplier::withCount('products')
            ->orderBy('products_count', 'desc')
            ->limit(5)
            ->get();

        // Get suppliers by country
        $suppliersByCountry = Supplier::selectRaw('country, COUNT(*) as count')
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderBy('count', 'desc')
            ->get();

        // Get suppliers by city
        $suppliersByCity = Supplier::selectRaw('city, COUNT(*) as count')
            ->whereNotNull('city')
            ->groupBy('city')
            ->orderBy('count', 'desc')
            ->limit(10)
            ->get();

        return [
            'total_suppliers' => $totalSuppliers,
            'active_suppliers' => $activeSuppliers,
            'inactive_suppliers' => $inactiveSuppliers,
            'top_suppliers' => $topSuppliers,
            'suppliers_by_country' => $suppliersByCountry,
            'suppliers_by_city' => $suppliersByCity,
        ];
    }

    /**
     * Get supplier performance metrics
     */
    public function getSupplierPerformance(int $supplierId, int $days = 30): array
    {
        $supplier = Supplier::findOrFail($supplierId);
        
        // Get products count
        $totalProducts = $supplier->products()->count();
        $activeProducts = $supplier->products()->active()->count();

        // Get recent activity (last 30 days)
        $recentProducts = $supplier->products()
            ->where('created_at', '>=', now()->subDays($days))
            ->count();

        // Get average product price
        $avgPrice = $supplier->products()->avg('price');
        $avgCost = $supplier->products()->avg('cost');

        return [
            'supplier' => $supplier,
            'period_days' => $days,
            'total_products' => $totalProducts,
            'active_products' => $activeProducts,
            'recent_products' => $recentProducts,
            'average_price' => round($avgPrice ?? 0, 2),
            'average_cost' => round($avgCost ?? 0, 2),
            'average_margin' => $avgPrice && $avgCost ? round((($avgPrice - $avgCost) / $avgPrice) * 100, 2) : 0,
        ];
    }

    /**
     * Search suppliers by location
     */
    public function searchByLocation(string $city = null, string $country = null): Collection
    {
        $query = Supplier::active();

        if ($city) {
            $query->where('city', 'like', "%{$city}%");
        }

        if ($country) {
            $query->where('country', 'like', "%{$country}%");
        }

        return $query->orderBy('name')->get();
    }

    /**
     * Get suppliers with low credit limit
     */
    public function getSuppliersWithLowCredit(): Collection
    {
        return Supplier::active()
            ->where('credit_limit', '>', 0)
            ->whereRaw('credit_limit < 1000') // Adjust threshold as needed
            ->orderBy('credit_limit')
            ->get();
    }

    /**
     * Update supplier credit limit
     */
    public function updateCreditLimit(int $supplierId, float $creditLimit): Supplier
    {
        $supplier = Supplier::findOrFail($supplierId);
        $supplier->update(['credit_limit' => $creditLimit]);

        return $supplier->fresh();
    }

    /**
     * Get supplier contact information
     */
    public function getSupplierContacts(): Collection
    {
        return Supplier::active()
            ->whereNotNull('contact_person')
            ->orWhereNotNull('email')
            ->orWhereNotNull('phone')
            ->orderBy('name')
            ->get(['id', 'name', 'contact_person', 'email', 'phone']);
    }
}
