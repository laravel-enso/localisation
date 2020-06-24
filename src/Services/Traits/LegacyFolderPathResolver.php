<?php

namespace LaravelEnso\Localisation\Services\Traits;

trait LegacyFolderPathResolver
{
    protected function legacyFolderName($locale)
    {
        return resource_path('lang'.DIRECTORY_SEPARATOR.$locale);
    }
}
