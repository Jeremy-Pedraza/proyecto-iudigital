<?php

namespace App\Providers;

// Contracts
use App\Domain\Contracts\Users\UserRepositoryInterface;
use App\Domain\Contracts\Users\UserServiceInterface;

use App\Domain\Contracts\Roles\RoleRepositoryInterface;
use App\Domain\Contracts\Roles\RoleServiceInterface;

use App\Domain\Contracts\Products\ProductRepositoryInterface;
use App\Domain\Contracts\Products\ProductServiceInterface;

// Repositories
use App\Infrastructure\Users\UserRepository;
use App\Infrastructure\Roles\RoleRepository;
use App\Infrastructure\Products\ProductRepository;

// Services
use App\Domain\Services\UserService;
use App\Domain\Services\RoleService;
use App\Domain\Services\ProductService;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Vite;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Users
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);

        // Roles
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(RoleServiceInterface::class, RoleService::class);

        // Products
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(ProductServiceInterface::class, ProductService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
