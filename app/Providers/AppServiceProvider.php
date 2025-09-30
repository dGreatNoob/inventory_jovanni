<?php

namespace App\Providers;

use App\Enums\RolesEnum;
use Illuminate\Support\ServiceProvider;
// use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Gate;
use App\Models\SalesOrderItem;
use App\Observers\SalesOrderItemObserver;

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
        Gate::before(function ($user, $ability) {
        return $user->hasRole(RolesEnum::SUPERADMIN->value) ? true : null;
    });
    }
}
