<?php

namespace LaravelEnso\Localisation\Services\Json;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use LaravelEnso\Helpers\Services\JsonReader;
use Symfony\Component\Finder\SplFileInfo;

class Template
{
    public static function handle(?string $exceptLocale = null): array
    {
        $template = Collection::wrap(File::files(App::langPath()))
            ->filter(fn (SplFileInfo $file) => $file->getExtension() === 'json')
            ->reject(fn (SplFileInfo $file) => $file->getFilenameWithoutExtension() === $exceptLocale)
            ->sortBy(fn (SplFileInfo $file) => $file->getFilename())
            ->map(fn (SplFileInfo $file) => (new JsonReader($file->getPathname()))->array())
            ->first(fn (array $langFile) => $langFile !== [], []);

        return array_fill_keys(array_keys($template), null);
    }
}
