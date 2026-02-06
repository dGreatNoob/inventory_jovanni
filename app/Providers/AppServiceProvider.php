<?php

namespace App\Providers;

use App\Enums\RolesEnum;
use Illuminate\Support\ServiceProvider;
// use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Vite;
use App\Models\SalesOrderItem;
use App\Models\PurchaseOrder;
use App\Observers\SalesOrderItemObserver;
use App\Observers\PurchaseOrderObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Schema::defaultStringLength(191);
        SalesOrderItem::observe(SalesOrderItemObserver::class);
        PurchaseOrder::observe(PurchaseOrderObserver::class);
        Gate::before(function ($user, $ability) {
        return $user->hasRole(RolesEnum::SUPERADMIN->value) ? true : null;
    });

        // Handle missing Vite manifest in test environment
        // This is a fallback - assets should be built before tests run
        if (app()->environment('testing')) {
            $manifestPath = public_path('build/manifest.json');
            if (!file_exists($manifestPath)) {
                // Create build directory if it doesn't exist
                if (!is_dir(public_path('build'))) {
                    mkdir(public_path('build'), 0755, true);
                }
                // Create minimal valid manifest to prevent Vite errors
                file_put_contents($manifestPath, json_encode([
                    'resources/css/app.css' => ['file' => 'assets/app.css', 'src' => 'resources/css/app.css'],
                    'resources/js/app.js' => ['file' => 'assets/app.js', 'src' => 'resources/js/app.js'],
                ], JSON_PRETTY_PRINT));
            }
        }
    }
}
