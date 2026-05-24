<?php

namespace LaravelEnso\Localisation;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class TranslatableModelServiceProvider extends ServiceProvider
{
    protected array $models = [];

    public function boot(): void
    {
        if (! $this->runningLocalisationScan()) {
            return;
        }

        Config::set('enso.localisation.scan.models', array_values(array_unique([
            ...Config::get('enso.localisation.scan.models', []),
            ...$this->models,
        ])));
    }

    private function runningLocalisationScan(): bool
    {
        return $this->app->runningInConsole()
            && in_array('enso:localisation:scan', $_SERVER['argv'] ?? [], true);
    }
}
