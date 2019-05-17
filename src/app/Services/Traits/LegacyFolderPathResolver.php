<?php

namespace LaravelEnso\Localisation\app\Services\Traits;

trait LegacyFolderPathResolver
{
    protected function legacyFolderName($locale)
    {
        return resource_path('lang/'.$locale);
    }
}
