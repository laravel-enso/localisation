<?php

namespace LaravelEnso\Localisation\app\Handlers\Traits;

trait LegacyFolderPathResolver
{
    protected function legacyFolderName($locale)
    {
        return resource_path('lang/'.$locale);
    }
}
