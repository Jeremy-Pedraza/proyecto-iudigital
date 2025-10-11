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

use App\Domain\Contracts\Clientes\ClienteRepositoryInterface;
use App\Domain\Contracts\Clientes\ClienteServiceInterface;

use App\Domain\Contracts\Comerciales\ComercialesServiceInterface;
use App\Domain\Contracts\Comerciales\ComercialesRepositoryInterface;

use App\Domain\Contracts\Zonas\ZonaRepositoryInterface;
use App\Domain\Contracts\Zonas\ZonaServiceInterface;

use App\Domain\Contracts\Reglas\ReglaRepositoryInterface;
use App\Domain\Contracts\Reglas\ReglaServiceInterface;

use App\Domain\Contracts\Rutas\RutaRepositoryInterface;
use App\Domain\Contracts\Rutas\RutaServiceInterface;

// Repositories
use App\Infrastructure\Users\UserRepository;
use App\Infrastructure\Roles\RoleRepository;
use App\Infrastructure\Products\ProductRepository;
use App\Infrastructure\ListasRapidas\ListaRapidaRepository;
use App\Infrastructure\Auditoria\AuditoriaRepository;
use App\Infrastructure\Clientes\ClienteRepository;
use App\Infrastructure\Comerciales\ComercialesRepository;
use App\Infrastructure\Zonas\ZonaRepository;
use App\Infrastructure\Reglas\ReglaRepository;
use App\Infrastructure\Rutas\RutaRepository;


// Services
use App\Domain\Services\UserService;
use App\Domain\Services\RoleService;
use App\Domain\Services\ProductService;
use App\Domain\Services\ListaRapidaService;
use App\Domain\Services\AuditoriaService;
use App\Domain\Services\ClienteService;
use App\Domain\Services\ComercialesService;
use App\Domain\Services\ZonaService;
use App\Domain\Services\ReglaService;
use App\Domain\Services\RutaService;

use App\Application\Rutas\GetPlannerDataUseCase;

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

    // Clientes
    $this->app->bind(ClienteRepositoryInterface::class, ClienteRepository::class);
    $this->app->bind(ClienteServiceInterface::class, ClienteService::class);

    // Comerciales
    $this->app->bind(ComercialesRepositoryInterface::class, ComercialesRepository::class);
    $this->app->bind(ComercialesServiceInterface::class, ComercialesService::class);

    // Zonas
    $this->app->bind(ZonaRepositoryInterface::class, ZonaRepository::class);
    $this->app->bind(ZonaServiceInterface::class, ZonaService::class);

    $this->app->bind(ReglaRepositoryInterface::class, ReglaRepository::class);

    // Bind del servicio
    $this->app->bind(ReglaServiceInterface::class, ReglaService::class);

    // Registrar Repository
    $this->app->singleton(RutaRepositoryInterface::class, RutaRepository::class);

    // Registrar Service
    $this->app->singleton(RutaServiceInterface::class, RutaService::class);

    // Registrar Use Cases (se resuelven automáticamente por inyección de dependencias)
    $this->app->bind(GetPlannerDataUseCase::class, fn($app) => new GetPlannerDataUseCase($app->make(RutaServiceInterface::class)));

  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    Paginator::useBootstrapFive();
  }
}
