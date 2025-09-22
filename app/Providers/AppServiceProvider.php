<?php

namespace App\Providers;

use App\Domain\Contracts\Users\UserRepositoryInterface;
use App\Domain\Contracts\Users\UserServiceInterface;
use App\Infrastructure\Users\UserRepository;
use Illuminate\Support\ServiceProvider;
use App\Domain\Services\UserService;
use Illuminate\Support\Facades\Vite;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
