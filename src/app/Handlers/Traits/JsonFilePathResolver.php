<?php

namespace LaravelEnso\Localisation\app\Handlers\Traits;

trait JsonFilePathResolver
{
    protected function jsonFileName($locale)
    {
        return resource_path('lang/'.$locale.'.json');
    }
}
