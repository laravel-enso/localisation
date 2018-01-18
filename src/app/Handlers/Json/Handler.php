<?php

namespace LaravelEnso\Localisation\app\Handlers\Json;

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

    protected function saveToDisk(string $locale, array $json)
    {
        \File::put(
            $this->jsonFileName($locale),
            json_encode($json, JSON_FORCE_OBJECT)
        );
    }
}
