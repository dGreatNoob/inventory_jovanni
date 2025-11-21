<?php

namespace App\Livewire\Pages\Branch;

use Livewire\Component;
use App\Models\Branch;
use App\Models\Product;

class BranchInventory extends Component
{
    public $branches;
    public $products;
    public $branchProductStocks;

    public function mount()
    {
        $this->branches = Branch::with('products')->get();
        $this->products = Product::all();

        // Build your branchProductStocks mapping
        $this->branchProductStocks = [];
        foreach ($this->branches as $branch) {
            foreach ($this->products as $product) {
                $this->branchProductStocks[$branch->id][$product->id] =
                    $branch->products->find($product->id)?->pivot->stock ?? 0;
            }
        }
    }

    public function render()
    {
        return view('livewire.pages.branch.branch-inventory');
    }
}