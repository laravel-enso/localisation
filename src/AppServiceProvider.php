<?php

namespace LaravelEnso\Localisation;

use Illuminate\Support\ServiceProvider;
use LaravelEnso\Localisation\Commands\Publish;
use LaravelEnso\Localisation\Commands\Scan;
use LaravelEnso\Localisation\Http\Middleware\SetLanguage;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app['router']->aliasMiddleware('set-language', SetLanguage::class);

        $this->load()
            ->publish()
            ->commands([Publish::class, Scan::class]);
    }

    private function load()
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');

        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        return $this;
    }

    private function publish()
    {
        $this->publishes([
            __DIR__.'/../database/factories' => database_path('factories'),
        ], ['localisation-factory', 'enso-factories']);

        $this->publishes([
            __DIR__.'/../database/seeders' => database_path('seeders'),
        ], ['localisation-seeder', 'enso-seeders']);

        $this->publishes([
            __DIR__.'/../config' => config_path('enso'),
        ], ['localisation-config', 'enso-config']);

        return $this;
    }
}
