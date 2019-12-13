<?php

namespace LaravelEnso\Localisation\app\Services\Legacy;

use Illuminate\Support\Facades\File;
use LaravelEnso\Localisation\app\Exceptions\Localisation as Exception;
use LaravelEnso\Localisation\app\Services\Traits\LegacyFolderPathResolver;

class Storer
{
    use LegacyFolderPathResolver;

    private $locale;
    private $newLocaleFolder;
    private $fallbackLocaleFolder;

    public function __construct(string $locale)
    {
        $this->locale = $locale;
        $this->newLocaleFolder = $this->legacyFolderName($locale);
        $this->fallbackLocaleFolder = $this->legacyFolderName(config('app.fallback_locale'));
    }

    public function create()
    {
        if (File::isDirectory($this->newLocaleFolder)) {
            throw Exception::folderExists($this->locale, $this->newLocaleFolder);
        }

        File::copyDirectory(
            $this->fallbackLocaleFolder,
            $this->newLocaleFolder
        );
    }
}
