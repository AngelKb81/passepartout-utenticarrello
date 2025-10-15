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
        // Registrazione Repository Pattern
        $this->app->bind(\App\Repositories\UserRepository::class, function ($app) {
            return new \App\Repositories\UserRepository($app->make(\App\Models\User::class));
        });

        $this->app->bind(\App\Repositories\ProductRepository::class, function ($app) {
            return new \App\Repositories\ProductRepository($app->make(\App\Models\Product::class));
        });

        $this->app->bind(\App\Repositories\CartRepository::class, function ($app) {
            return new \App\Repositories\CartRepository($app->make(\App\Models\Cart::class));
        });

        // Registrazione Services
        $this->app->bind(\App\Services\UserService::class, function ($app) {
            return new \App\Services\UserService($app->make(\App\Repositories\UserRepository::class));
        });

        $this->app->bind(\App\Services\ProductService::class, function ($app) {
            return new \App\Services\ProductService($app->make(\App\Repositories\ProductRepository::class));
        });

        $this->app->bind(\App\Services\CartService::class, function ($app) {
            return new \App\Services\CartService(
                $app->make(\App\Repositories\CartRepository::class),
                $app->make(\App\Repositories\ProductRepository::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
