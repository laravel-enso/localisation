<?php

namespace LaravelEnso\Localisation\Services\Legacy;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class Storer
{
    private $newLocaleFolder;
    private $fallbackLocaleFolder;

    public function __construct(string $locale)
    {
        $fallback = Config::get('app.fallback_locale');

        $this->newLocaleFolder = App::langPath($locale);
        $this->fallbackLocaleFolder = App::langPath($fallback);
    }

    public function create(): void
    {
        File::copyDirectory(
            $this->fallbackLocaleFolder,
            $this->newLocaleFolder
        );
    }
}
