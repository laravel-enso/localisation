<?php

namespace LaravelEnso\Localisation;

use Illuminate\Support\ServiceProvider;
use LaravelEnso\Localisation\app\Commands\MergeCommand;
use LaravelEnso\Localisation\app\Http\Middleware\SetLanguage;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->commands([
            MergeCommand::class,
        ]);

        $this->app['router']->middleware(
            'set-language', SetLanguage::class
        );

        $this->loadDependencies()
            ->publishDependencies();
    }

    private function loadDependencies()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');

        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        return $this;
    }

    public function publishDependencies()
    {
        $this->publishes([
            __DIR__.'/database/factories' => database_path('factories'),
        ], 'localisation-factory');

        $this->publishes([
            __DIR__.'/database/factories' => database_path('factories'),
        ], 'enso-factories');

        $this->publishes([
            __DIR__.'/database/seeds' => database_path('seeds'),
        ], 'localisation-seeder');

        $this->publishes([
            __DIR__.'/database/seeds' => database_path('seeds'),
        ], 'enso-seeders');

        $this->publishes([
            __DIR__.'/config' => config_path('enso'),
        ], 'localisation-config');

        $this->publishes([
            __DIR__.'/resources/js' => resource_path('js'),
        ], 'localisation-assets');

        $this->publishes([
            __DIR__.'/resources/js' => resource_path('js'),
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
