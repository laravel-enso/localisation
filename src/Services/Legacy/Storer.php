<?php

namespace LaravelEnso\Localisation\Services\Legacy;

use Illuminate\Support\Facades\File;
use LaravelEnso\Localisation\Exceptions\Localisation;
use LaravelEnso\Localisation\Services\Traits\LegacyFolderPathResolver;

class Storer
{
    use LegacyFolderPathResolver;

    private $newLocaleFolder;
    private $fallbackLocaleFolder;

    public function __construct(string $locale)
    {
        $this->newLocaleFolder = $this->legacyFolderName($locale);
        $this->fallbackLocaleFolder = $this->legacyFolderName(config('app.fallback_locale'));
    }

    public function create()
    {
        if (File::isDirectory($this->newLocaleFolder)) {
            throw Localisation::legacyFolderExists($this->folder, $this->newLocaleFolder);
        }

        File::copyDirectory(
            $this->fallbackLocaleFolder,
            $this->newLocaleFolder
        );
    }
}
