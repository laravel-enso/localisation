<?php

namespace LaravelEnso\Localisation\App\Services;

use Illuminate\Support\Facades\File;
use LaravelEnso\Localisation\App\Models\Language;
use LaravelEnso\Localisation\App\Services\Traits\JsonFilePathResolver;
use LaravelEnso\Localisation\App\Services\Traits\LegacyFolderPathResolver;

class Destroyer
{
    use JsonFilePathResolver, LegacyFolderPathResolver;

    private $language;

    public function __construct(Language $language)
    {
        $this->language = $language;
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
