<?php

namespace LaravelEnso\Localisation\app\Classes\Json;

use LaravelEnso\Helpers\app\Classes\JsonParser;
use LaravelEnso\Localisation\app\Models\Language;

class Storer extends Handler
{
    private $locale;
    private $language;

    public function __construct(string $locale)
    {
        $this->locale = $locale;
        $this->language = Language::extra()->first();
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
        return (new JsonParser($this->filename($subDirectory)))
            ->array();
    }

    private function filename($subDirectory)
    {
        return $this->jsonFileName(
            $this->language->name,
            $subDirectory
        );
    }
}
