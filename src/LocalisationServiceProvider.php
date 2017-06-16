<?php

namespace LaravelEnso\Localisation;

use Illuminate\Support\ServiceProvider;

class LocalisationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishesDepedencies();
        $this->loadDependencies();
    }

    private function publishesDepedencies()
    {
        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views/vendor/laravel-enso/localisation'),
        ], 'localisation-views');
    }

    private function loadDependencies()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'laravel-enso/localisation');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

    public function register()
    {
        //
    }
}
