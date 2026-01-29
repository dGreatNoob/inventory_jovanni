<?php

namespace App\Livewire\Pages\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Models\Promo;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
#[Title('Promo Performance Dashboard')]
class PromoReport extends Component
{
    use WithPagination;

    public $dateFrom = '';
    public $dateTo = '';
    public $statusFilter = '';

    public function mount()
    {
        // Start with no date filters to show all promos initially
        $this->dateFrom = '';
        $this->dateTo = '';
    }

    public function getTotalPromosProperty()
    {
        return Promo::count();
    }

    public function getActivePromosProperty()
    {
        return Promo::where('startDate', '<=', now())
            ->where('endDate', '>=', now())
            ->count();
    }

    public function getTotalDiscountValueProperty()
    {
        // Calculate total potential discount value
        // This is a placeholder - adjust based on your promo discount logic
        return 0.00;
    }

    public function getAverageDiscountProperty()
    {
        // Calculate average discount percentage
        // This is a placeholder - adjust based on your promo discount logic
        return 0.0;
    }

    public function getPromosProperty()
    {
        $query = Promo::query();

        if ($this->dateFrom) {
            $query->where('startDate', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('endDate', '<=', $this->dateTo);
        }

        if ($this->statusFilter) {
            switch ($this->statusFilter) {
                case 'active':
                    $query->where('startDate', '<=', now())
                          ->where('endDate', '>=', now());
                    break;
                case 'upcoming':
                    $query->where('startDate', '>', now());
                    break;
                case 'expired':
                    $query->where('endDate', '<', now());
                    break;
            }
        }

        return $query->get()->map(function ($promo) {
            // Load products from JSON
            $productIds = json_decode($promo->product, true) ?? [];
            $products = Product::whereIn('id', $productIds)->get();

            $promo->products = $products->map(function ($product) {
                // Assuming product has price
                // For demo, use a placeholder discount
                $currentPrice = $product->price ?? 0;
                $discountedPrice = $currentPrice * 0.9; // Placeholder: 10% discount

                return (object) [
                    'id' => $product->id,
                    'name' => $product->name,
                    'current_price' => $currentPrice,
                    'discounted_price' => $discountedPrice,
                ];
            });

            return $promo;
        });
    }

    public function render()
    {
        return view('livewire.pages.reports.promo', [
            'totalPromos' => $this->totalPromos,
            'activePromos' => $this->activePromos,
            'totalDiscountValue' => $this->totalDiscountValue,
            'averageDiscount' => $this->averageDiscount,
            'promos' => $this->promos,
        ]);
    }
}