<?php

namespace LaravelEnso\Localisation\Services\Json;

use LaravelEnso\Helpers\Services\JsonReader;
use LaravelEnso\Localisation\Models\Language;
use LaravelEnso\Localisation\Services\Traits\JsonFilePathResolver;

class Reader
{
    use JsonFilePathResolver;

    private Language $language;
    private string $subDirectory;

    public function __construct(Language $language, ?string $subDirectory = null)
    {
        $this->language = $language;
        $this->subDirectory = $subDirectory;
    }

    public function get(): string
    {
        return (new JsonReader($this->filename()))->json();
    }

    private function filename(): string
    {
        return $this->jsonFileName(
            $this->language->name,
            $this->subDirectory
        );
    }
}
