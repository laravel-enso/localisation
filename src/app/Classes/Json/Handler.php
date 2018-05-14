<?php

namespace LaravelEnso\Localisation\app\Classes\Json;

use LaravelEnso\Localisation\app\Models\Language;
use LaravelEnso\Localisation\app\Classes\Traits\JsonFilePathResolver;

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

    protected function saveMerged(string $locale, array $langFile)
    {
        $this->saveToDisk($locale, $langFile);
    }

    protected function savePartial(string $locale, array $langFile, string $subDir)
    {
        $this->saveToDisk($locale, $langFile, $subDir);
    }

    protected function saveToDisk(string $locale, array $langFile, string $subDir = null)
    {
        \File::put(
            $this->jsonFileName($locale, $subDir),
            json_encode($langFile, JSON_FORCE_OBJECT | ($subDir ? JSON_PRETTY_PRINT : 0))
        );
    }

    protected function merge(string $locale = null)
    {
        $languages = Language::extra();

        if ($locale) {
            $languages->where('name', $locale);
        }

        $languages->pluck('name')
            ->each(function ($locale) {
                $this->mergeLocale($locale);
            });
    }

    private function mergeLocale(string $locale)
    {
        $core = (array) $this->jsonFileContent($this->coreJsonFileName($locale));
        $app = (array) $this->jsonFileContent($this->appJsonFileName($locale));

        $this->saveMerged($locale, array_merge($core, $app));
    }
}
