<?php

namespace LaravelEnso\Localisation\app\Classes\Json;

use LaravelEnso\Helpers\app\Classes\JsonParser;
use LaravelEnso\Localisation\app\Models\Language;

class Updater extends Handler
{
    private $locale;
    private $langArray;
    private $subDir;

    public function __construct(Language $language, array $langArray, string $subDir = null)
    {
        $this->langArray = $langArray;
        $this->locale = $language->name;
        $this->subDir = $subDir;
    }

    public function run()
    {
        $this->savePartial(
            $this->locale,
            $this->langArray,
            $this->subDir
        );

        $this->extraLangs()
            ->each(function ($locale) {
                $this->updateDifferences($locale);
            });
    }

    public function addKey()
    {
        $this->extraLangs()
            ->each(function ($locale) {
                $extraLangFile = $this->extraLangFile($locale, $this->updateDir());
                [$addedCount, $extraLangFile] = $this->addNewKeys($extraLangFile);
                $this->savePartial($locale, $extraLangFile, $this->updateDir());
            });
    }

    private function updateDifferences(string $locale)
    {
        $removedCount = $addedCount = 0;
        $extraLangFile = $this->extraLangFile($locale, $this->subDir);
        [$removedCount, $extraLangFile] = $this->removeExtraKeys($extraLangFile);
        [$addedCount, $extraLangFile] = $this->addNewKeys($extraLangFile);

        if ($addedCount || $removedCount) {
            $this->savePartial($locale, $extraLangFile, $this->subDir);
        }
    }

    private function removeExtraKeys(array $extraLangFile)
    {
        $keysToRemove = collect($extraLangFile)
            ->diffKeys($this->langArray)
            ->keys();

        $keysToRemove->each(function ($keyToRemove) use (&$extraLangFile) {
            unset($extraLangFile[$keyToRemove]);
        });

        return [$keysToRemove->count(), $extraLangFile];
    }

    private function addNewKeys(array $extraLangFile)
    {
        $keysToAdd = collect($this->langArray)
            ->diffKeys($extraLangFile);

        $arrayToAdd = $this->newTranslations($keysToAdd->all());
        $extraLangFile = collect($arrayToAdd)
            ->merge($extraLangFile);

        return [count($keysToAdd), $extraLangFile->all()];
    }

    private function extraLangFile(string $locale, string $subDir)
    {
        return (new JsonParser($this->jsonFileName($locale, $subDir)))
            ->array();
    }

    private function extraLangs()
    {
        return Language::extra()
            ->where('name', '<>', $this->locale)
            ->pluck('name');
    }
}
