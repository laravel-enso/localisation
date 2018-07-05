<?php

namespace LaravelEnso\Localisation;

use Illuminate\Support\ServiceProvider;
use LaravelEnso\Localisation\app\Commands\MergeCommand;
use LaravelEnso\Localisation\app\Http\Middleware\SetLanguage;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadDependencies();
        $this->publishesAll();

        $this->app['router']->aliasMiddleware('set-language', SetLanguage::class);

        $this->commands([
            MergeCommand::class,
        ]);
    }

    private function loadDependencies()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
    }

    public function publishesAll()
    {
        $this->publishes([
            __DIR__.'/config' => config_path('enso'),
        ], 'localisation-config');

        $this->publishes([
            __DIR__.'/resources/assets/js' => resource_path('assets/js'),
        ], 'localisation-assets');

        $this->publishes([
            __DIR__.'/resources/assets/js' => resource_path('assets/js'),
            __DIR__.'/resources/lang/enso' => resource_path('lang/enso'),
        ], 'enso-assets');

        $this->publishes([
            __DIR__.'/resources/lang' => resource_path('lang'),
        ], 'localisation-lang-files');
    }

    public function register()
    {
        //
    }
}
