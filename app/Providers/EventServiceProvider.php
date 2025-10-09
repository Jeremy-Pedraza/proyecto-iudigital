<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Regla;
use App\Observers\ReglaObserver;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The model observers for your application.
     *
     * @var array
     */
    protected $observers = [
        Regla::class => [ReglaObserver::class],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        // Los observers se registran automáticamente desde $observers
        // o puedes hacerlo manualmente aquí:
        Regla::observe(ReglaObserver::class);
    }
}
