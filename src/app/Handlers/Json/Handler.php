<?php

namespace LaravelEnso\Localisation\app\Handlers\Json;

use Illuminate\Support\Facades\Artisan;
use LaravelEnso\Localisation\app\Handlers\Traits\JsonFilePathResolver;

abstract class Handler
{
    use JsonFilePathResolver;

    protected function newTranslations(array $array)
    {
        $keys = collect($array)->keys();
        $values = collect()->pad($keys->count(), null);

        return $keys->combine($values);
    }

    protected function jsonFileContent(string $jsonFile)
    {
        return json_decode(
            \File::get($jsonFile)
        );
    }

    protected function saveToDisk(string $locale, array $json, string $subDir = null)
    {
        if (is_null($subDir)) {
            $subDir = $this->getUpdateDir();
        }

        \File::put(
            $this->jsonFileName($locale, $subDir),
            json_encode($json, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT)
        );

        return $this;
    }

    protected function merge(string $locale)
    {
        Artisan::call('localisation:merge', [
            '--locale' => $locale,
        ]);
    }
}
