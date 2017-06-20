<?php

namespace LaravelEnso\Localisation;

use Illuminate\Support\ServiceProvider;

class LocalisationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishesDepedencies();
        $this->loadDependencies();
        $this->registerMiddleware();
    }

    private function publishesDepedencies()
    {
        $this->publishes([
            __DIR__.'/resources/assets/js/components' => resource_path('assets/js/vendor/laravel-enso/components'),
        ], 'localisation-component');

        $this->publishes([
            __DIR__.'/resources/assets/js/components' => resource_path('assets/js/vendor/laravel-enso/components'),
        ], 'localisation-update');
    }

    private function loadDependencies()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'laravel-enso/localisation');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

    private function registerMiddleware()
    {
        $this->app['router']->aliasMiddleware('set-language', SetLanguage::class);
    }

    public function register()
    {
        //
    }
}
