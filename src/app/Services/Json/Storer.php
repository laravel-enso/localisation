<?php

namespace LaravelEnso\Localisation\App\Services\Json;

use LaravelEnso\Helpers\App\Classes\JsonReader;
use LaravelEnso\Localisation\App\Models\Language;

class Storer extends Handler
{
    private string $locale;

    public function __construct(string $locale)
    {
        $this->locale = $locale;
    }

    public function create()
    {
        $core = $this->newTranslations(
            $this->existingTranslations('enso')
        );

        $this->savePartial($this->locale, $core->all(), 'enso');

        $app = $this->newTranslations(
            $this->existingTranslations('app')
        );

        $this->savePartial($this->locale, $app->all(), 'app');

        $this->saveToDisk($this->locale, $core->merge($app)->all());
    }

    private function existingTranslations(string $subDirectory)
    {
        return (new JsonReader($this->filename($subDirectory)))->array();
    }

    private function filename($subDirectory)
    {
        return $this->jsonFileName(
            Language::extra()->first()->name,
            $subDirectory
        );
    }
}
