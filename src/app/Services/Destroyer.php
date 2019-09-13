<?php

namespace LaravelEnso\Localisation\app\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use LaravelEnso\Localisation\app\Models\Language;
use LaravelEnso\Localisation\app\Services\Traits\JsonFilePathResolver;
use LaravelEnso\Localisation\app\Services\Traits\LegacyFolderPathResolver;

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
        DB::transaction(function () {
            $this->language->delete();
            File::deleteDirectory($this->legacyFolderName($this->language->name));
            File::delete($this->jsonFileName($this->language->name, 'enso'));
            File::delete($this->jsonFileName($this->language->name, 'app'));
            File::delete($this->jsonFileName($this->language->name));
        });
    }
}
