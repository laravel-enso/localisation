<?php

namespace LaravelEnso\Localisation\Services\Json;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;

class SaveToDisk
{
    public static function handle(string $locale, ?array $langFile = []): void
    {
        $json = json_encode(
            $langFile,
            JSON_FORCE_OBJECT | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );

        File::put(App::langPath("{$locale}.json"), $json);
    }
}
