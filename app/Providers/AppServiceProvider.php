<?php

namespace App\Providers;

use App\Models\Delivery;
use App\Observers\DeliveryObserver;
use Illuminate\Support\ServiceProvider;

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
        Delivery::observe(DeliveryObserver::class);
    }
}
