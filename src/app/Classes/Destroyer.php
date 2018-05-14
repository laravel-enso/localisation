<?php

namespace LaravelEnso\Localisation\app\Classes;

use LaravelEnso\Localisation\app\Models\Language;
use LaravelEnso\Localisation\app\Classes\Traits\JsonFilePathResolver;
use LaravelEnso\Localisation\app\Classes\Traits\LegacyFolderPathResolver;

class Destroyer
{
    use JsonFilePathResolver, LegacyFolderPathResolver;

    private $localisation;

    public function __construct(Language $localisation)
    {
        $this->localisation = $localisation;
    }

    public function run()
    {
        \DB::transaction(function () {
            $this->localisation->delete();
            \File::deleteDirectory($this->legacyFolderName($this->localisation->name));
            \File::delete($this->jsonFileName($this->localisation->name, 'enso'));
            \File::delete($this->jsonFileName($this->localisation->name, 'app'));
            \File::delete($this->jsonFileName($this->localisation->name));
        });
    }
}
