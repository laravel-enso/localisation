<?php

namespace LaravelEnso\Localisation\app\Classes\Json;

use LaravelEnso\Helpers\app\Classes\JsonParser;
use LaravelEnso\Localisation\app\Models\Language;
use LaravelEnso\Localisation\app\Classes\Traits\JsonFilePathResolver;

class Reader
{
    use JsonFilePathResolver;

    private $language;
    private $subDirectory;

    public function __construct(Language $language, string $subDirectory = null)
    {
        $this->language = $language;
        $this->subDirectory = $subDirectory;
    }

    public function get()
    {
        return (new JsonParser($this->filename()))
            ->json();
    }

    private function filename()
    {
        return $this->jsonFileName(
            $this->language->name,
            $this->subDirectory
        );
    }
}
