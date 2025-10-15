<?php

namespace App\Providers;

use App\Models\Product;
use App\Models\Cart;
use App\Policies\ProductPolicy;
use App\Policies\CartPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Product::class => ProductPolicy::class,
        Cart::class => CartPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Gate per operazioni admin
        Gate::define('admin-only', function ($user) {
            return $user->isAdmin();
        });

        // Gate per gestione profilo
        Gate::define('manage-profile', function ($user, $targetUser) {
            return $user->id === $targetUser->id || $user->isAdmin();
        });
    }
}
