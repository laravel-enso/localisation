<?php

namespace LaravelEnso\Localisation\app\Classes\Json;

use Illuminate\Contracts\Support\Responsable;
use LaravelEnso\Localisation\app\Models\Language;
use LaravelEnso\Localisation\app\Classes\Traits\JsonFilePathResolver;

class Reader implements Responsable
{
    use JsonFilePathResolver;

    private $jsonFile;

    public function __construct(Language $language, string $subDir = null)
    {
        $this->jsonFile = $this->jsonFileName($language->name, $subDir);
    }

    public function toResponse($request)
    {
        return \File::get($this->jsonFile);
    }
}
