<?php

namespace LaravelEnso\Localisation\Services\Legacy;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

class Store
{
    public function __construct(private string $locale)
    {
    }

    public function handle(): void
    {
        $folder = App::langPath($this->locale);

        if (File::exists($folder)) {
            return;
        }

        $source = $this->sourceFolder();

        File::makeDirectory($folder, recursive: true);

        if ($source) {
            File::copyDirectory($source, $folder);
        }
    }

    private function sourceFolder(): ?string
    {
        $packageFolder = base_path("vendor/laravel-enso/localisation/lang/{$this->locale}");

        if (File::exists($packageFolder)) {
            return $packageFolder;
        }

        $fallbackFolder = App::langPath(Config::get('app.fallback_locale'));

        return File::exists($fallbackFolder) ? $fallbackFolder : null;
    }
}
