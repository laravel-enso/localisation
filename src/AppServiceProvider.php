<?php

namespace LaravelEnso\Localisation;

use Illuminate\Support\ServiceProvider;
use LaravelEnso\Localisation\App\Commands\Merge;
use LaravelEnso\Localisation\App\Http\Middleware\SetLanguage;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app['router']->aliasMiddleware('set-language', SetLanguage::class);

        $this->load()
            ->publish()
            ->commands(Merge::class);
    }

    private function load()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        return $this;
    }

    private function publish()
    {
        $this->publishes([
            __DIR__.'/database/factories' => database_path('factories'),
        ], ['localisation-factory', 'enso-factories']);

        $this->publishes([
            __DIR__.'/database/seeds' => database_path('seeds'),
        ], ['localisation-seeder', 'enso-seeders']);

        $this->publishes([
            __DIR__.'/config' => config_path('enso'),
        ], ['localisation-config', 'enso-config']);

        $this->publishes([
            __DIR__.'/resources/lang/enso' => resource_path('lang/enso'),
        ], 'enso-assets');

        $this->publishes([
            __DIR__.'/resources/lang' => resource_path('lang'),
        ], 'localisation-lang-files');

        return $this;
    }
}
