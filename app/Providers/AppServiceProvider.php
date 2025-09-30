<?php

namespace App\Providers;

// Contracts
use App\Domain\Contracts\Users\UserRepositoryInterface;
use App\Domain\Contracts\Users\UserServiceInterface;

use App\Domain\Contracts\Roles\RoleRepositoryInterface;
use App\Domain\Contracts\Roles\RoleServiceInterface;

use App\Domain\Contracts\Products\ProductRepositoryInterface;
use App\Domain\Contracts\Products\ProductServiceInterface;

use App\Domain\Contracts\ListasRapidas\ListaRapidaServiceInterface;
use App\Domain\Contracts\ListasRapidas\ListaRapidaRepositoryInterface;

use App\Domain\Contracts\Auditoria\AuditoriaRepositoryInterface;
use App\Domain\Contracts\Auditoria\AuditoriaServiceInterface;

// Repositories
use App\Infrastructure\Users\UserRepository;
use App\Infrastructure\Roles\RoleRepository;
use App\Infrastructure\Products\ProductRepository;
use App\Infrastructure\ListasRapidas\ListaRapidaRepository;
use App\Infrastructure\Auditoria\AuditoriaRepository;

// Services
use App\Domain\Services\UserService;
use App\Domain\Services\RoleService;
use App\Domain\Services\ProductService;
use App\Domain\Services\ListaRapidaService;
use App\Domain\Services\AuditoriaService;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
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

        // ListasRapidas
        $this->app->bind(ListaRapidaRepositoryInterface::class, ListaRapidaRepository::class);
        $this->app->bind(ListaRapidaServiceInterface::class, ListaRapidaService::class);

        // Auditoria
        $this->app->bind(AuditoriaRepositoryInterface::class, AuditoriaRepository::class);
        $this->app->bind(AuditoriaServiceInterface::class, AuditoriaService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();
    }
}
