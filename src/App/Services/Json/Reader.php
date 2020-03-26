<?php

namespace LaravelEnso\Localisation\App\Services\Json;

use LaravelEnso\Helpers\App\Classes\JsonReader;
use LaravelEnso\Localisation\App\Models\Language;
use LaravelEnso\Localisation\App\Services\Traits\JsonFilePathResolver;

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
