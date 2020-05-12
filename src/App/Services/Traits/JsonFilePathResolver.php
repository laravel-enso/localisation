<?php

namespace LaravelEnso\Localisation\App\Services\Traits;

use Illuminate\Support\Collection;

trait JsonFilePathResolver
{
    protected function jsonFileName($locale, $subDir = null)
    {
        $path = (new Collection(['lang', $subDir, "{$locale}.json"]))
            ->filter()->implode(DIRECTORY_SEPARATOR);

        return $subDir === 'enso'
            ? __DIR__.'/../../../resources/'.$path
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
