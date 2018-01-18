<?php

namespace LaravelEnso\Localisation\app\Handlers\Json;

use LaravelEnso\Localisation\app\Models\Language;

class Storer extends Handler
{
    private $locale;

    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }

    public function create()
    {
        $translations = $this->newTranslations(
            $this->existingTranslations()
        );

        $this->saveToDisk($this->locale, $translations->all());
    }

    private function existingTranslations()
    {
        $language = Language::extra()->first();

        return !is_null($language)
            ? (array) $this->jsonFileContent($this->jsonFileName($language->name))
            : [];
    }
}
