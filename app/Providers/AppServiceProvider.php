<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(\App\Contracts\AuthenticationService::class, \App\Services\AuthenticationServiceImpl::class);
        $this->app->bind(\App\Contracts\OrderService::class, \App\Services\OrderServiceImpl::class);
        $this->app->bind(\App\Contracts\ProductService::class, \App\Services\ProductServiceImpl::class);
        $this->app->bind(\App\Contracts\PaymentGatewayService::class, \App\Services\PaymentGatewayServiceImpl::class);
        $this->app->bind(\App\Contracts\InventoryMovementService::class, \App\Services\InventoryMovementServiceImpl::class);
        $this->app->bind(\App\Contracts\ProductService::class, \App\Services\ProductServiceImpl::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
