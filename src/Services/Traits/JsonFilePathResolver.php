<?php

namespace LaravelEnso\Localisation\Services\Traits;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

trait JsonFilePathResolver
{
    protected function jsonFileName($locale, $subDir = null): string
    {
        $path = Collection::wrap([$subDir, "{$locale}.json"])
            ->filter()->implode('/');

        $basePath = base_path();

        return $subDir === 'enso'
            ? "{$basePath}/vendor/laravel-enso/localisation/lang/{$path}"
            : App::langPath($path);
    }

    protected function coreJsonFileName($locale): string
    {
        return $this->jsonFileName($locale, 'enso');
    }

    protected function appJsonFileName($locale): string
    {
        return $this->jsonFileName($locale, 'app');
    }

    protected function updateDir(): string
    {
        return config('enso.localisation.core') ? 'enso' : 'app';
    }
}
