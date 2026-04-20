<?php

namespace LaravelEnso\Localisation\Services\Json;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;

class SaveToDisk
{
    public static function handle(
        string $locale,
        ?array $langFile = [],
        bool $overwrite = false
    ): void {
        if ($locale === 'en') {
            return;
        }

        $path = App::langPath("{$locale}.json");

        if (! File::exists($path) || $overwrite) {
            $json = json_encode(
                $langFile,
                JSON_FORCE_OBJECT | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            );

            File::put($path, $json);
        }
    }
}
