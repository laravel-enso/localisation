<?php

namespace LaravelEnso\Localisation\Services;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use LaravelEnso\Localisation\Models\Language;
use LaravelEnso\Localisation\Services\Traits\JsonFilePathResolver;

class Destroyer
{
    use JsonFilePathResolver;

    public function __construct(private Language $language)
    {
    }

    public function run(): void
    {
        $this->language->delete();
        File::deleteDirectory(App::langPath($this->language->name));
        File::delete($this->jsonFileName($this->language->name, 'enso'));
        File::delete($this->jsonFileName($this->language->name, 'app'));
        File::delete($this->jsonFileName($this->language->name));
    }
}
