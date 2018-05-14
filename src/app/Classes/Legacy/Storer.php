<?php

namespace LaravelEnso\Localisation\app\Classes\Legacy;

use LaravelEnso\Localisation\app\Exceptions\LocalisationException;
use LaravelEnso\Localisation\app\Classes\Traits\LegacyFolderPathResolver;

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
        if (\File::isDirectory($this->newLocaleFolder)) {
            throw new LocalisationException(__(
                "Can't create the language for locale :locale files because the legacy folder :folder already exists",
                ['locale' => $this->locale, 'folder' => $this->newLocaleFolder]
            ));
        }

        \File::copyDirectory(
            $this->fallbackLocaleFolder,
            $this->newLocaleFolder
        );
    }
}
