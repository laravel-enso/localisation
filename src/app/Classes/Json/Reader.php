<?php

namespace LaravelEnso\Localisation\app\Classes\Json;

use LaravelEnso\Localisation\app\Models\Language;
use LaravelEnso\Localisation\app\Classes\Traits\JsonFilePathResolver;

class Reader
{
    use JsonFilePathResolver;

    private $jsonFile;

    public function __construct(Language $language, string $subDir = null)
    {
        $this->jsonFile = $this->jsonFileName($language->name, $subDir);
    }

    public function get()
    {
        return \File::get($this->jsonFile);
    }
}
