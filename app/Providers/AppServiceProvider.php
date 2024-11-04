<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Tarea;
use App\Observers\TareaObserver;
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
    public function boot()
    {
        Tarea::observe(TareaObserver::class);
    }
}
