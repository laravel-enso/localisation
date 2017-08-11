<?php

namespace LaravelEnso\Localisation;

use Illuminate\Support\ServiceProvider;
use LaravelEnso\Localisation\app\Http\Middleware\SetLanguage;

class LocalisationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'laravel-enso/localisation');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');
        $this->app['router']->aliasMiddleware('set-language', SetLanguage::class);
    }

    public function register()
    {
        $this->app->register(AuthServiceProvider::class);
    }
}
