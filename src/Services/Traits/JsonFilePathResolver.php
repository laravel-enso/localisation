<?php

namespace LaravelEnso\Localisation\Services\Traits;

use Illuminate\Support\Collection;

trait JsonFilePathResolver
{
    protected function jsonFileName($locale, $subDir = null)
    {
        $path = (new Collection(['lang', $subDir, "{$locale}.json"]))
            ->filter()->implode(DIRECTORY_SEPARATOR);
        $basePath = base_path();

        return $subDir === 'enso'
            ? "{$basePath}/vendor/laravel-enso/localisation/resources/{$path}"
            : resource_path($path);
    }

    protected function coreJsonFileName($locale)
    {
        return $this->jsonFileName($locale, 'enso');
    }

    protected function appJsonFileName($locale)
    {
        return $this->jsonFileName($locale, 'app');
    }

    protected function updateDir()
    {
        return config('enso.localisation.core') ? 'enso' : 'app';
    }
}
