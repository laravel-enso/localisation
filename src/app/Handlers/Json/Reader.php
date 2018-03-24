<?php

namespace LaravelEnso\Localisation\app\Handlers\Json;

use LaravelEnso\Localisation\app\Models\Language;
use LaravelEnso\Localisation\app\Handlers\Traits\JsonFilePathResolver;

class Reader
{
    use JsonFilePathResolver;

    private $jsonFile;

    public function __construct(Language $language, string $subDir = null)
    {
        $this->jsonFile = $this->jsonFileName($language->name, $subDir);
    }

    public function content()
    {
        return \File::get($this->jsonFile);
    }
}
