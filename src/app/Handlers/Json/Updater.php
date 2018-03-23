<?php

namespace LaravelEnso\Localisation\app\Handlers\Json;

use LaravelEnso\Localisation\app\Models\Language;

class Updater extends Handler
{
    private $locale;
    private $updatedLangFile;
    private $subDir;

    public function __construct(Language $language, array $updatedLangFile, string $subDir = null)
    {
        $this->updatedLangFile = $updatedLangFile;
        $this->locale = $language->name;
        $this->subDir = $subDir;
    }

    public function run()
    {
        $this->saveToDisk($this->locale, $this->updatedLangFile, $this->subDir)
            ->merge($this->locale);
        $this->processDifferences();
    }

    public function addKey()
    {
        $this->processDifferences(false, true);
    }

    private function processDifferences(bool $remove = true, bool $add = true)
    {
        Language::extra()
            ->where('name', '<>', $this->locale)
            ->pluck('name')
            ->each(function ($locale) use ($remove, $add) {
                $this->updateDifferences($locale, $remove, $add);
            });
    }

    private function updateDifferences(string $locale, bool $remove = true, bool $add = true)
    {
        $removedCount = $addedCount = 0;

        $extraLangFile = (array) $this->jsonFileContent($this->jsonFileName($locale, $this->getUpdateDir()));

        if ($remove) {
            [$removedCount, $extraLangFile] = $this->removeExtraKeys($extraLangFile);
        }

        if ($add) {
            [$addedCount, $extraLangFile] = $this->addNewKeys($extraLangFile);
        }

        if ($addedCount || $removedCount) {
            $this->saveToDisk($locale, $extraLangFile, $this->subDir)
                ->merge($locale);
        }
    }

    private function removeExtraKeys(array $extraLangFile)
    {
        $keysToRemove = collect($extraLangFile)
            ->diffKeys($this->updatedLangFile);

        $keysToRemove->each(function ($valueToRemove, $keyToRemove) use (&$extraLangFile) {
            unset($extraLangFile[$keyToRemove]);
        });

        return [$keysToRemove->count(), $extraLangFile];
    }

    private function addNewKeys(array $extraLangFile)
    {
        $keysToAdd = collect($this->updatedLangFile)
            ->diffKeys($extraLangFile);

        $arrayToAdd = $this->newTranslations($keysToAdd->all());
        $extraLangFile = collect($arrayToAdd)->merge($extraLangFile);

        return [count($keysToAdd), $extraLangFile->all()];
    }
}
