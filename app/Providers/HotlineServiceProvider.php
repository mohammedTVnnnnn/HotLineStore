<?php

namespace App\Providers;

use App\Services\UserService;
use App\Services\ProductService;
use App\Services\CartService;
use App\Services\InvoiceService;
use App\Services\CategoryService;
use Illuminate\Support\ServiceProvider;

class HotlineServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register Services as singletons
        $this->app->singleton(UserService::class);
        $this->app->singleton(ProductService::class);
        $this->app->singleton(CartService::class);
        $this->app->singleton(InvoiceService::class);
        $this->app->singleton(CategoryService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
