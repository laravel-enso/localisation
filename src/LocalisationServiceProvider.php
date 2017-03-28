<?php

namespace LaravelEnso\Localisation;

use Illuminate\Support\ServiceProvider;
use LaravelEnso\Localisation\app\Commands\Generate;
use LaravelEnso\Localisation\app\Commands\Scan;
use LaravelEnso\Localisation\app\Commands\Sync;

class LocalisationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishesDepedencies();
        $this->registerCommands();
        $this->loadDependencies();
    }

    private function publishesDepedencies()
    {
        $this->publishes([
            __DIR__.'/database/migrations' => database_path('migrations'),
        ], 'localisation-migration');

        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views/vendor/laravel-enso/localisation'),
        ], 'localisation-views');

        $this->publishes([
            __DIR__.'/resources/assets/js' => resource_path('assets/js/vendor/laravel-enso/pages/localisation'),
        ], 'localisation-assets');
    }

    private function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Generate::class,
                Scan::class,
                Sync::class,
            ]);
        }
    }

    private function loadDependencies()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'laravel-enso/localisation');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
