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
            __DIR__.'/resources/assets/js/components' => resource_path('assets/js/vendor/laravel-enso/components'),
        ], 'localisation-component');

        $this->publishes([
            __DIR__.'/resources/views' => resource_path('views/vendor/laravel-enso/localisation'),
        ], 'localisation-views');

        $this->publishes([
            __DIR__.'/resources/assets/js/components' => resource_path('assets/js/vendor/laravel-enso/components'),
        ], 'localisation-update');
    }

    private function loadDependencies()
    {
        $this->app['router']->aliasMiddleware('set-language', SetLanguage::class);
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'laravel-enso/localisation');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

    public function register()
    {
        //
    }
}
