<?php

namespace LaravelEnso\Localisation\app\Classes;

use LaravelEnso\Localisation\app\Models\Language;
use LaravelEnso\Localisation\app\Classes\Traits\JsonFilePathResolver;
use LaravelEnso\Localisation\app\Classes\Traits\LegacyFolderPathResolver;

class Updater
{
    use JsonFilePathResolver, LegacyFolderPathResolver;

    private $localisation;
    private $request;

    public function __construct(Language $localisation, array $request)
    {
        $this->localisation = $localisation;
        $this->request = $request;
    }

    public function run()
    {
        \DB::transaction(function () {
            $oldName = $this->localisation->name;
            $this->localisation->updateWithFlagSufix($this->request, $this->request['flag_sufix']);
            $this->updateLangFiles($oldName, $this->request['name']);
        });
    }

    private function updateLangFiles(string $oldName, string $newName)
    {
        if ($oldName === $newName) {
            return;
        }

        $this->updateJson($oldName, $newName);
        $this->updateAppJson($oldName, $newName);
        $this->updateEnsoJson($oldName, $newName);
        $this->updateLegacyFolder($oldName, $newName);
    }

    public function updateJson($oldName, $newName)
    {
        \File::move(
            $this->jsonFileName($oldName),
            $this->jsonFileName($newName)
        );
    }

    public function updateAppJson($oldName, $newName)
    {
        \File::move(
            $this->jsonFileName($oldName, 'app'),
            $this->jsonFileName($newName, 'app')
        );
    }

    public function updateEnsoJson($oldName, $newName)
    {
        \File::move(
            $this->jsonFileName($oldName, 'enso'),
            $this->jsonFileName($newName, 'enso')
        );
    }

    private function updateLegacyFolder($oldName, $newName)
    {
        \File::move(
            $this->legacyFolderName($oldName),
            $this->legacyFolderName($newName)
        );
    }
}
