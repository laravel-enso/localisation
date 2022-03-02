<?php

namespace LaravelEnso\Localisation;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\ServiceProvider;
use LaravelEnso\Localisation\Commands\Merge;
use LaravelEnso\Localisation\Http\Middleware\SetLanguage;

class AppServiceProvider extends ServiceProvider
{
    private array $langs = ['ar', 'br', 'de', 'es', 'fr', 'hu', 'mn', 'nl', 'ro', 'ru'];

    public function boot()
    {
        $this->app['router']->aliasMiddleware('set-language', SetLanguage::class);

        $this->load()
            ->publish()
            ->commands(Merge::class);
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

        $this->publishes(
            Collection::wrap($this->langs)->mapWithKeys(fn ($key) => [
                __DIR__.'/../lang/'.$key => App::langPath($key),
            ])->toArray(),
            'enso-localisation'
        );

        return $this;
    }
}
