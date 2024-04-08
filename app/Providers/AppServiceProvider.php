<?php

namespace App\Providers;

use App\Models\Obra;
use App\Observers\ObraObserver;

use Illuminate\Support\ServiceProvider;

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
    public function boot(): void
    {
        Obra::observe(ObraObserver::class);
    }
}
