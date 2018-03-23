<?php

namespace LaravelEnso\Localisation;

use Illuminate\Support\ServiceProvider;
use LaravelEnso\Localisation\app\Console\MergeCommand;
use LaravelEnso\Localisation\app\Http\Middleware\SetLanguage;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/api.php');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->app['router']->aliasMiddleware('set-language', SetLanguage::class);

        $this->publishes([
            __DIR__.'/config' => config_path('enso'),
        ], 'localisation-config');

        $this->publishes([
            __DIR__.'/resources/assets/js' => resource_path('assets/js'),
        ], 'localisation-assets');

        $this->publishes([
            __DIR__.'/resources/assets/js' => resource_path('assets/js'),
        ], 'enso-assets');

        $this->publishes([
            __DIR__.'/resources/lang/enso' => resource_path('lang/enso'),
        ], 'localisation-lang-files');

        $this->commands([
            MergeCommand::class,
        ]);
    }

    public function register()
    {
        //
    }
}
