<?php

namespace LaravelEnso\Localisation\app\Handlers\Traits;

trait JsonFilePathResolver
{
	private $coreDir = 'enso';
    private $appDir = 'app';

    protected function jsonFileName($locale, $subDir = '')
    {
        return resource_path('lang/'.($subDir ? $subDir.'/' : '').$locale.'.json');
    }

    protected function jsonFileNameCore($locale)
    {
        return $this->jsonFileName($locale, $this->coreDir);
    }

    protected function jsonFileNameApp($locale)
    {
        return $this->jsonFileName($locale, $this->appDir);
    }

    protected function getUpdateDir()
    {
        return config('enso.localisation.core') ? $this->coreDir : $this->appDir;
    }
}
