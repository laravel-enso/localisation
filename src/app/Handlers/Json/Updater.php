<?php

namespace LaravelEnso\Localisation\app\Handlers\Json;

use LaravelEnso\Localisation\app\Models\Language;

class Updater extends Handler
{
    private $locale;
    private $updatedLangFile;

    public function __construct(Language $language, array $updatedLangFile)
    {
        $this->updatedLangFile = $updatedLangFile;
        $this->locale = $language->name;
    }

    public function run()
    {
        $this->saveToDisk($this->locale, $this->updatedLangFile);
        $this->processDifferences();
    }

    private function processDifferences()
    {
        Language::extra()
            ->where('name', '<>', $this->locale)
            ->pluck('name')
            ->each(function ($locale) {
                $this->updateDifferences($locale);
            });
    }

    private function updateDifferences(string $locale)
    {
        $extraLangFile = (array) $this->jsonFileContent($this->jsonFileName($locale));
        [$removedCount, $extraLangFile] = $this->removeExtraKeys($extraLangFile);
        [$addedCount, $extraLangFile] = $this->addNewKeys($extraLangFile);

        if ($addedCount || $removedCount) {
            $this->saveToDisk($locale, $extraLangFile);
        }
    }

    private function removeExtraKeys(array $extraLangFile)
    {
        $keysToRemove = collect($extraLangFile)
            ->diffKeys($this->updatedLangFile);

        $keysToRemove->each(function ($keyToRemove) use ($extraLangFile) {
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
