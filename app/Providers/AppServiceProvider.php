<?php

namespace App\Providers;

use App\Filament\Resources\StockInResource;
use App\Models\Warehouse;
use App\Policies\WarehousePolicy;
use App\Services\TestcosaHashing;
use Filament\Facades\Filament;
use Filament\Pages\Auth\Register;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {



        Gate::policy(Warehouse::class, WarehousePolicy::class);

        Schema::defaultStringLength(191);

        Hash::extend('custom', function ($app) {
            return new TestcosaHashing();
        });
    }

}
