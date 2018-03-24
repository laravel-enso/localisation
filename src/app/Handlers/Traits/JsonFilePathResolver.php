<?php

namespace LaravelEnso\Localisation\app\Handlers\Traits;

trait JsonFilePathResolver
{
    protected function jsonFileName($locale, $subDir = null)
    {
        return resource_path('lang/'.($subDir ? $subDir.'/' : '').$locale.'.json');
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
