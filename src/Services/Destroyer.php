<?php

namespace LaravelEnso\Localisation\Services;

use Illuminate\Support\Facades\File;
use LaravelEnso\Localisation\Models\Language;
use LaravelEnso\Localisation\Services\Traits\JsonFilePathResolver;
use LaravelEnso\Localisation\Services\Traits\LegacyFolderPathResolver;

class Destroyer
{
    use JsonFilePathResolver;
    use LegacyFolderPathResolver;

    public function __construct(private Language $language)
    {
    }

    public function run()
    {
        $this->language->delete();
        File::deleteDirectory($this->legacyFolderName($this->language->name));
        File::delete($this->jsonFileName($this->language->name, 'enso'));
        File::delete($this->jsonFileName($this->language->name, 'app'));
        File::delete($this->jsonFileName($this->language->name));
    }
}
