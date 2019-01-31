<?php

namespace LaravelEnso\Localisation\app\Classes\Traits;

trait LegacyFolderPathResolver
{
    protected function legacyFolderName($locale)
    {
        return resource_path('lang/'.$locale);
    }
}
